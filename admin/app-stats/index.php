<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Set page variables for header
$page_title = "Argo Books Statistics";
$page_description = "View and analyze anonymous user data with geo-location insights from the Argo Books application";

// Telemetry files now live in data-logs/telemetry/. The legacy data-logs/ root
// is still read during the transition so files not yet moved keep showing.
$dataDir = '../data-logs/telemetry/';
$legacyDataDir = '../data-logs/';
$errorMessage = '';

// Tier filter: 'all' (default), 'free', 'premium'
$tierFilter = $_GET['tier'] ?? 'all';
if (!in_array($tierFilter, ['all', 'free', 'premium'], true)) {
    $tierFilter = 'all';
}

$aggregatedData = [
    'dataPoints' => [
        'Export' => [],
        'Gemini' => [],
        'OpenExchangeRates' => [],
        'GoogleSheets' => [],
        'ReceiptScanning' => [],
        'Session' => [],
        'Error' => [],
        'FeatureUsage' => []
    ],
    'geoLocationEnabled' => false,
    'tierFilter' => $tierFilter,
    // Per-tier user counts computed from ALL files (independent of $tierFilter)
    'tierStats' => [
        'free' => ['files' => 0, 'mauUsers' => 0, 'totalUsers' => 0],
        'premium' => ['files' => 0, 'mauUsers' => 0, 'totalUsers' => 0],
    ],
];
$fileInfo = [];

// Helper function to normalize event data
// $sessionMeta contains top-level fields from the compact format (including tier + authId)
function normalizeEvent($event, $sessionMeta = []) {
    $normalized = [
        'timestamp' => $event['timestamp'] ?? date('Y-m-d H:i:s'),
        'appVersion' => $event['appVersion'] ?? $sessionMeta['appVersion'] ?? 'Unknown',
        'platform' => $event['platform'] ?? $sessionMeta['platform'] ?? 'Unknown',
        'userAgent' => $event['userAgent'] ?? $sessionMeta['userAgent'] ?? '',
        'dataType' => $event['dataType'] ?? 'Unknown',
        'tier' => $sessionMeta['tier'] ?? 'premium',
        'authId' => $sessionMeta['authId'] ?? '',
    ];

    // Geo-location: prefer event-level, fall back to session-level
    $geo = $event['geoLocation'] ?? $sessionMeta['geoLocation'] ?? null;
    if (isset($geo) && is_array($geo)) {
        $normalized['country'] = $geo['country'] ?? 'Unknown';
        $normalized['region'] = $geo['region'] ?? '';
        $normalized['city'] = $geo['city'] ?? '';
        $normalized['timezone'] = $geo['timezone'] ?? '';
        $normalized['hashedIP'] = $geo['hashedIp'] ?? '';
    }

    // Fallback user identifier for free-tier events (no hashedIp); use authId as a stable per-device key.
    if (empty($normalized['hashedIP']) && !empty($normalized['authId'])) {
        $normalized['hashedIP'] = $normalized['authId'];
    }

    return $normalized;
}

// Helper function to categorize and transform events
// $sessionMeta: top-level fields from compact format (appVersion, platform, userAgent, geoLocation)
function processEvent($event, $sourceFile, $sessionMeta = []) {
    // Handle nested event structure (wrapper has dataType, actual data is in event property)
    if (isset($event['event']) && is_array($event['event'])) {
        $event = $event['event'];
    }

    $dataType = $event['dataType'] ?? null;
    if (!$dataType) {
        return null;
    }

    $normalized = normalizeEvent($event, $sessionMeta);
    $normalized['source_file'] = $sourceFile;

    switch ($dataType) {
        case 'Session':
            $normalized['action'] = $event['action'] ?? 'Unknown';
            $normalized['duration'] = $event['durationSeconds'] ?? 0;
            return ['category' => 'Session', 'data' => $normalized];

        case 'Export':
            $normalized['ExportType'] = $event['exportType'] ?? 'Unknown';
            $normalized['DurationMS'] = $event['durationMs'] ?? 0;
            $normalized['FileSize'] = $event['fileSize'] ?? null;
            return ['category' => 'Export', 'data' => $normalized];

        case 'ApiUsage':
            $apiName = $event['apiName'] ?? 'Unknown';
            $normalized['DurationMS'] = $event['durationMs'] ?? 0;
            $normalized['Success'] = $event['success'] ?? true;

            switch ($apiName) {
                case 'Gemini':
                    return ['category' => 'Gemini', 'data' => $normalized];

                case 'OpenExchangeRates':
                    return ['category' => 'OpenExchangeRates', 'data' => $normalized];

                case 'GoogleSheets':
                    return ['category' => 'GoogleSheets', 'data' => $normalized];

                case 'ReceiptScanProxy':
                    return ['category' => 'ReceiptScanning', 'data' => $normalized];

                default:
                    return null;
            }

        case 'Error':
            $normalized['ErrorCategory'] = $event['errorCategory'] ?? 'Unknown';
            $normalized['ErrorCode'] = $event['errorCode'] ?? '';
            $normalized['Message'] = $event['message'] ?? '';
            $normalized['SourceFile'] = $event['sourceFile'] ?? '';
            $normalized['LineNumber'] = $event['lineNumber'] ?? null;
            $normalized['MethodName'] = $event['methodName'] ?? '';
            return ['category' => 'Error', 'data' => $normalized];

        case 'FeatureUsage':
            $normalized['FeatureName'] = $event['featureName'] ?? 'Unknown';
            $normalized['Context'] = $event['context'] ?? '';
            if (isset($event['durationMs'])) {
                $normalized['DurationMs'] = $event['durationMs'];
            }
            return ['category' => 'FeatureUsage', 'data' => $normalized];

        default:
            return null;
    }
}

$dataDirs = array_values(array_filter([$dataDir, $legacyDataDir], 'is_dir'));
if (empty($dataDirs)) {
    $errorMessage = "Directory 'data-logs/' does not exist.";
} else {
    // Read from both the new telemetry/ folder and the legacy root, de-duping by
    // filename so a file present in both places is only counted once.
    $dataFiles = [];
    $seenNames = [];
    foreach ($dataDirs as $dir) {
        foreach (glob($dir . '*.json') ?: [] as $f) {
            $name = basename($f);
            if (!isset($seenNames[$name])) {
                $seenNames[$name] = true;
                $dataFiles[] = $f;
            }
        }
    }
    if (count($dataFiles) === 0) {
        $errorMessage = "No anonymous data files found.";
    } else {
        // Sort files by modification time (newest first) for file info display
        usort($dataFiles, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $totalFiles = count($dataFiles);
        $processedFiles = 0;
        $failedFiles = 0;

        // Track per-tier unique users (from ALL files, regardless of $tierFilter)
        $tierUsers = [
            'free' => ['all' => [], 'mau' => []],
            'premium' => ['all' => [], 'mau' => []],
        ];
        $mauThreshold = time() - 30 * 86400;

        // Process all JSON files and aggregate the data
        foreach ($dataFiles as $file) {
            $jsonDataRaw = file_get_contents($file);
            if ($jsonDataRaw === false || trim($jsonDataRaw) === '') {
                $failedFiles++;
                continue;
            }

            $fileData = json_decode($jsonDataRaw, true);
            if ($fileData === null) {
                $failedFiles++;
                continue;
            }

            $sourceFile = basename($file);

            // Upload payload with events array
            // Compact format: session metadata at top level, events only have unique fields
            if (isset($fileData['events']) && is_array($fileData['events'])) {
                // Files without an explicit tier (legacy uploads) are treated as premium
                $fileTier = $fileData['tier'] ?? 'premium';
                if (!in_array($fileTier, ['free', 'premium'], true)) {
                    $fileTier = 'premium';
                }
                $fileAuthId = $fileData['authId'] ?? '';

                // Always count toward per-tier stats, even if the file is filtered out below
                $aggregatedData['tierStats'][$fileTier]['files']++;

                // Extract session-level metadata from the top level
                $sessionMeta = [
                    'appVersion' => $fileData['appVersion'] ?? null,
                    'platform' => $fileData['platform'] ?? null,
                    'userAgent' => $fileData['userAgent'] ?? null,
                    'geoLocation' => $fileData['geoLocation'] ?? null,
                    'tier' => $fileTier,
                    'authId' => $fileAuthId,
                ];

                // Skip files that don't match the active tier filter (UI-only filter; tier stats above are unaffected)
                $includeFile = $tierFilter === 'all' || $tierFilter === $fileTier;

                foreach ($fileData['events'] as $event) {
                    $result = processEvent($event, $sourceFile, $sessionMeta);
                    if ($result === null) {
                        continue;
                    }

                    $data = $result['data'];

                    // Tier-stats accounting uses ALL events (not filter-gated)
                    $userKey = !empty($data['hashedIP']) ? $data['hashedIP'] : ($fileAuthId ?: null);
                    if ($userKey !== null) {
                        $tierUsers[$fileTier]['all'][$userKey] = true;
                        $eventTime = strtotime($data['timestamp'] ?? '1970-01-01');
                        if ($eventTime !== false && $eventTime >= $mauThreshold) {
                            $tierUsers[$fileTier]['mau'][$userKey] = true;
                        }
                    }

                    // Per-category dataPoints respect the tier filter
                    if (!$includeFile) {
                        continue;
                    }

                    $category = $result['category'];
                    if (!isset($aggregatedData['dataPoints'][$category])) {
                        $aggregatedData['dataPoints'][$category] = [];
                    }
                    $aggregatedData['dataPoints'][$category][] = $data;

                    if (!empty($data['country']) && $data['country'] !== 'Unknown') {
                        $aggregatedData['geoLocationEnabled'] = true;
                    }
                }
                $processedFiles++;
            }
            // Unknown format
            else {
                $failedFiles++;
            }
        }

        // Finalize per-tier user counts
        foreach (['free', 'premium'] as $t) {
            $aggregatedData['tierStats'][$t]['totalUsers'] = count($tierUsers[$t]['all']);
            $aggregatedData['tierStats'][$t]['mauUsers'] = count($tierUsers[$t]['mau']);
        }

        // Store file processing information
        $fileInfo = [
            'total_files' => $totalFiles,
            'processed_files' => $processedFiles,
            'failed_files' => $failedFiles,
            'latest_file' => $totalFiles > 0 ? basename($dataFiles[0]) : '',
            'latest_modified' => $totalFiles > 0 ? filemtime($dataFiles[0]) : 0,
            'oldest_file' => $totalFiles > 0 ? basename(end($dataFiles)) : '',
            'oldest_modified' => $totalFiles > 0 ? filemtime(end($dataFiles)) : 0
        ];

        // Sort all aggregated data by timestamp for chronological analysis
        foreach ($aggregatedData['dataPoints'] as $category => &$dataPoints) {
            usort($dataPoints, function ($a, $b) {
                $timeA = strtotime($a['timestamp'] ?? '1970-01-01');
                $timeB = strtotime($b['timestamp'] ?? '1970-01-01');
                return $timeA - $timeB;
            });
        }
        unset($dataPoints); // break reference

        if ($processedFiles === 0) {
            $errorMessage = "No valid JSON data found in any files.";
        }
    }
}

// Convert aggregated data to JSON for JavaScript. Escape HTML-meaningful characters
// (<, >, &, ', ") as \u00xx so a telemetry string containing "</script>" cannot break
// out of the inline <script> context where this is emitted.
$jsonData = json_encode(
    $aggregatedData,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES
);

// Include the shared header
include __DIR__ . '/../admin_header.php';
?>

<style>
.tabs-container {
    margin-bottom: 2rem;
}

/* Tab styles are shared across admin pages: see admin/common-style.css */

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    text-align: center;
}

.stat-card h3 {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--black);
    margin: 0 0 0.5rem 0;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-card .value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--black);
    margin: 0.5rem 0;
}

.stat-card .subtext {
    font-size: 0.75rem;
    color: var(--black);
    margin: 0;
}

.chart-section {
    margin-bottom: 3rem;
}

.section-title {
    text-align: center;
    margin-bottom: 1.5rem;
    color: var(--black);
    font-size: 1.5rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .section-tabs {
        gap: 2px;
    }

    .section-tab {
        padding: 10px 14px;
        font-size: 13px;
    }

    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}

.error-details-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.error-details-table th,
.error-details-table td {
    padding: 10px 14px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.error-details-table th {
    background: #f9fafb;
    font-weight: 600;
    color: var(--black);
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    position: sticky;
    top: 0;
}

.error-details-table tr:hover {
    background: #f9fafb;
}

.error-details-table td.error-code {
    font-family: monospace;
    font-weight: 600;
    color: #ef4444;
}

.error-details-table td.error-source {
    font-family: monospace;
    font-size: 0.8rem;
    color: var(--black);
}

.error-details-wrapper {
    max-height: 500px;
    overflow-y: auto;
    border-radius: 8px;
    margin-top: 1rem;
}

/* Tier filter pills use the shared .control-bar / .control-pill component
   (admin/common-style.css). No page-specific styles needed. */
</style>

<div class="container">
    <?php
    $hasAnyData = !$errorMessage && ($fileInfo['total_files'] ?? 0) > 0;
    $currentViewHasData = !empty($aggregatedData['dataPoints']) && (
        count($aggregatedData['dataPoints']['Export']) > 0 ||
        count($aggregatedData['dataPoints']['Gemini']) > 0 ||
        count($aggregatedData['dataPoints']['OpenExchangeRates']) > 0 ||
        count($aggregatedData['dataPoints']['GoogleSheets']) > 0 ||
        count($aggregatedData['dataPoints']['ReceiptScanning']) > 0 ||
        count($aggregatedData['dataPoints']['Session']) > 0 ||
        count($aggregatedData['dataPoints']['Error']) > 0 ||
        count($aggregatedData['dataPoints']['FeatureUsage']) > 0
    );
    ?>

    <?php if ($hasAnyData): ?>
        <div class="control-bar">
            <div class="control-group">
                <span class="control-label">Tier:</span>
                <div class="control-pills">
                    <?php
                        $tierLabels = [
                            'all' => 'All',
                            'free' => 'Free',
                            'premium' => 'Premium',
                        ];
                        // Preserve the current tab across a tier switch (section-tabs.js keeps
                        // it in ?tab=). Whitelist against the real tabs to avoid reflecting
                        // arbitrary input into the href.
                        $validTabs = ['active-users', 'user-activity', 'geographic', 'versions', 'features', 'usage', 'api', 'errors', 'crashes'];
                        $currentTab = in_array($_GET['tab'] ?? '', $validTabs, true) ? $_GET['tab'] : '';
                        foreach ($tierLabels as $tierKey => $label):
                            $isActive = $tierFilter === $tierKey;
                            $pillHref = '?tier=' . urlencode($tierKey);
                            if ($currentTab !== '') $pillHref .= '&tab=' . urlencode($currentTab);
                    ?>
                        <a href="<?= htmlspecialchars($pillHref) ?>"
                           class="control-pill <?= $isActive ? 'active' : '' ?>">
                            <?= htmlspecialchars($label) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="no-data">
            <h3>No Data Available</h3>
            <p><?= htmlspecialchars($errorMessage) ?></p>
            <p><small>Make sure the data directory exists and contains valid JSON files at: <code>admin/data-logs/telemetry/</code></small></p>
        </div>
    <?php elseif (!$currentViewHasData): ?>
        <div class="no-data">
            <h3>No Data Available</h3>
            <?php if ($tierFilter !== 'all'): ?>
                <p>No <?= htmlspecialchars($tierFilter) ?>-tier data has been collected yet. Switch to a different tier above.</p>
            <?php else: ?>
                <p>No anonymous data has been collected yet. Data will appear here once users start using the application and uploading their analytics.</p>
                <p><small>Data files are automatically uploaded to: <code>admin/data-logs/telemetry/</code></small></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="tabs-container">
            <div class="section-tabs">
                <button class="section-tab active" data-tab="active-users">Active Users</button>
                <button class="section-tab" data-tab="user-activity">User Activity</button>
                <?php if ($aggregatedData['geoLocationEnabled']): ?>
                <button class="section-tab" data-tab="geographic">Geographic</button>
                <?php endif; ?>
                <button class="section-tab" data-tab="versions">Versions</button>
                <button class="section-tab" data-tab="features">Features</button>
                <button class="section-tab" data-tab="usage">Usage</button>
                <button class="section-tab" data-tab="api">API Usage</button>
                <button class="section-tab" data-tab="errors">Errors</button>
                <button class="section-tab" data-tab="crashes">Crashes</button>
            </div>

            <!-- Active Users Tab -->
            <div id="active-users" class="tab-content active">
                <h2 class="section-title">Active Users</h2>

                <div class="stats-grid" id="activeUsersKpiGrid">
                    <div class="stat-card">
                        <h3>Total Unique Users</h3>
                        <div class="value" id="kpiTotalUsers">—</div>
                        <p class="subtext">All time</p>
                    </div>
                    <div class="stat-card">
                        <h3>Active Today</h3>
                        <div class="value" id="kpiDAU">—</div>
                        <p class="subtext">Daily Active Users</p>
                    </div>
                    <div class="stat-card">
                        <h3>Active This Week</h3>
                        <div class="value" id="kpiWAU">—</div>
                        <p class="subtext">Last 7 days</p>
                    </div>
                    <div class="stat-card">
                        <h3>Active This Month</h3>
                        <div class="value" id="kpiMAU">—</div>
                        <p class="subtext">Last 30 days</p>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Free Users (Monthly Active Users)</h3>
                        <div class="value"><?= number_format($aggregatedData['tierStats']['free']['mauUsers']) ?></div>
                        <p class="subtext"><?= number_format($aggregatedData['tierStats']['free']['totalUsers']) ?> total · last 30 days</p>
                    </div>
                    <div class="stat-card">
                        <h3>Premium Users (Monthly Active Users)</h3>
                        <div class="value"><?= number_format($aggregatedData['tierStats']['premium']['mauUsers']) ?></div>
                        <p class="subtext"><?= number_format($aggregatedData['tierStats']['premium']['totalUsers']) ?> total · last 30 days</p>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container" style="flex: 1;">
                        <h2>Daily Active Users (Last 30 Days)</h2>
                        <canvas id="dauChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Active Users by Platform</h2>
                        <canvas id="platformBreakdownChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>New vs Returning Users</h2>
                        <canvas id="newVsReturningChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Peak Usage Hours</h2>
                        <canvas id="peakHoursChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Avg Session Duration (minutes)</h2>
                        <canvas id="avgSessionDurationChart"></canvas>
                    </div>
                </div>

                <h2 class="section-title" style="margin-top: 2rem;">User Details</h2>
                <div class="error-details-wrapper">
                    <table class="error-details-table" id="activeUsersTable">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>First Seen</th>
                                <th>Last Seen</th>
                                <th>Sessions</th>
                                <th>Platform</th>
                                <th>Country</th>
                                <th>App Version</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- User Activity Tab -->
            <div id="user-activity" class="tab-content">
                <?php include __DIR__ . '/user-activity-tab.php'; ?>
            </div>

            <!-- Geographic Tab -->
            <?php if ($aggregatedData['geoLocationEnabled']): ?>
            <div id="geographic" class="tab-content">
                <h2 class="section-title">Geographic Analytics</h2>
                
                <div class="chart-row">
                    <div class="chart-container">
                        <h2>User Distribution by Country</h2>
                        <canvas id="countryDistributionChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Top Cities</h2>
                        <canvas id="cityDistributionChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Performance by Country</h2>
                        <canvas id="performanceByCountryChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Error Rates by Country</h2>
                        <canvas id="errorRatesByCountryChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Session Duration by Region</h2>
                        <canvas id="sessionDurationByRegionChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Timezone Distribution</h2>
                        <canvas id="timezoneChart"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Versions Tab -->
            <div id="versions" class="tab-content">
                <h2 class="section-title">Version Analytics</h2>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Version Distribution</h2>
                        <canvas id="versionDistributionChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Version Usage Over Time</h2>
                        <canvas id="versionTimeChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Version Performance Comparison</h2>
                        <canvas id="versionPerformanceChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Session Duration by Version</h2>
                        <canvas id="versionSessionChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Error Rate by Version</h2>
                        <canvas id="versionErrorChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Features Tab -->
            <div id="features" class="tab-content">
                <h2 class="section-title">Feature Usage Analytics</h2>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Most Used Features</h2>
                        <canvas id="featureUsageChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Feature Usage Over Time</h2>
                        <canvas id="featureTimelineChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Usage Tab -->
            <div id="usage" class="tab-content">
                <h2 class="section-title">Usage Analytics</h2>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Average Session Duration</h2>
                        <canvas id="sessionDurationChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Export Types Distribution</h2>
                        <canvas id="exportTypesGrid"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Average Duration by Export Type</h2>
                        <canvas id="exportDurationByTypeChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Average File Size by Export Type</h2>
                        <canvas id="exportFileSizeByTypeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- API Usage Tab -->
            <div id="api" class="tab-content">
                <h2 class="section-title">API Usage</h2>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Gemini Success Rate</h2>
                        <canvas id="geminiChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Gemini Response Time</h2>
                        <canvas id="geminiResponseTimeChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Currency Conversion Response Time</h2>
                        <canvas id="exchangeRatesChart"></canvas>
                    </div>
                </div>

                <h2 class="section-title" style="margin-top: 2rem;">Receipt Scanning</h2>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Receipt Scans Overview</h2>
                        <canvas id="receiptScanOverviewChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Receipt Scan Success Rate</h2>
                        <canvas id="receiptScanSuccessChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Receipt Scan Duration</h2>
                        <canvas id="receiptScanDurationChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Scans Per Day Trend</h2>
                        <canvas id="receiptScanTrendChart"></canvas>
                    </div>
                </div>

                <h2 class="section-title" style="margin-top: 2rem;">AI Spreadsheet Importer</h2>

                <div class="stats-grid" id="aiImportStatsGrid">
                    <!-- Will be populated by JavaScript -->
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>AI Import Type Breakdown</h2>
                        <canvas id="aiImportOverviewChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>AI Imports Per Day Trend</h2>
                        <canvas id="aiImportTrendChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Import Type Over Time</h2>
                        <canvas id="aiImportTypeTimeChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>AI Import Duration Over Time</h2>
                        <canvas id="aiImportDurationChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>AI Import Duration by Type</h2>
                        <canvas id="aiImportDurationByTypeChart"></canvas>
                    </div>
                </div>
            </div>

              <!-- Errors Tab -->
            <div id="errors" class="tab-content">
                <h2 class="section-title">Error Analysis</h2>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Errors by Category</h2>
                        <canvas id="errorCategoryChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Errors by Code</h2>
                        <canvas id="errorCodeChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Errors by Category Over Time</h2>
                        <canvas id="errorCategoryTimelineChart"></canvas>
                    </div>
                </div>

                <h2 class="section-title" style="margin-top: 2rem;">Error Details</h2>
                <div class="error-details-wrapper" id="errorDetailsTableWrapper">
                    <p style="text-align: center; color: var(--black);">No error data available</p>
                </div>
            </div>

            <!-- Crashes Tab -->
            <div id="crashes" class="tab-content">
                <?php include __DIR__ . '/crashes-tab.php'; ?>
            </div>

        </div>
    <?php endif; ?>
</div>

<script>
// Tab switching is handled centrally by admin/section-tabs.js
// Pass PHP data to JavaScript
window.dashboardData = <?= $jsonData ?>;
</script>
<script src="main.js"></script>

<script>
// Preserve scroll position when switching tier filter (shared admin pattern; see CLAUDE.md)
document.addEventListener('DOMContentLoaded', function () {
    if (sessionStorage.getItem('scrollPosition')) {
        window.scrollTo(0, sessionStorage.getItem('scrollPosition'));
        sessionStorage.removeItem('scrollPosition');
    }
    document.querySelectorAll('.control-bar a.control-pill').forEach(function (link) {
        link.addEventListener('click', function () {
            sessionStorage.setItem('scrollPosition', window.scrollY);
            // The href is rendered server-side at load, before the user clicks a
            // tab (section-tabs.js only updates the URL, not these hrefs). Rewrite
            // it here so the tier switch keeps the currently-active tab.
            try {
                var dest = new URL(link.href, window.location.origin);
                var activeBtn = document.querySelector('.section-tab.active[data-tab]');
                var currentTab = activeBtn
                    ? activeBtn.dataset.tab
                    : new URLSearchParams(window.location.search).get('tab');
                if (currentTab) {
                    dest.searchParams.set('tab', currentTab);
                    link.href = dest.toString();
                }
            } catch (err) { /* URL API missing; fall back to server-rendered href */ }
        });
    });
});
</script>