<?php
session_start();
require_once '../../db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Set page variables for header
$page_title = "Argo Books Statistics";
$page_description = "View and analyze anonymous user data with geo-location insights from the Argo Books application";

$dataDir = '../data-logs/';
$errorMessage = '';
$aggregatedData = [
    'dataPoints' => [
        'Export' => [],
        'OpenAI' => [],
        'OpenExchangeRates' => [],
        'GoogleSheets' => [],
        'ReceiptScanning' => [],
        'Session' => [],
        'Error' => [],
        'FeatureUsage' => []
    ],
    'geoLocationEnabled' => false,
    'privacySettings' => [
        'collectCityData' => true,
        'collectIPHashes' => false,
        'collectISPData' => true,
        'collectCoordinates' => false
    ]
];
$fileInfo = [];

// Helper function to normalize event data from the new format
// Handles both PascalCase and camelCase field names
function normalizeEvent($event) {
    $normalized = [
        'timestamp' => $event['timestamp'] ?? $event['Timestamp'] ?? date('Y-m-d H:i:s'),
        'appVersion' => $event['appVersion'] ?? $event['AppVersion'] ?? 'Unknown',
        'platform' => $event['platform'] ?? $event['Platform'] ?? 'Unknown',
        'userAgent' => $event['userAgent'] ?? $event['UserAgent'] ?? '',
        'dataType' => $event['dataType'] ?? $event['DataType'] ?? 'Unknown'
    ];

    // Extract geo-location data (handle both camelCase and PascalCase)
    $geo = $event['geoLocation'] ?? $event['GeoLocation'] ?? null;
    if (isset($geo) && is_array($geo)) {
        $normalized['country'] = $geo['country'] ?? $geo['Country'] ?? 'Unknown';
        $normalized['region'] = $geo['region'] ?? $geo['Region'] ?? '';
        $normalized['city'] = $geo['city'] ?? $geo['City'] ?? '';
        $normalized['timezone'] = $geo['timezone'] ?? $geo['Timezone'] ?? '';
        $normalized['hashedIP'] = $geo['hashedIp'] ?? $geo['IpHash'] ?? '';
    }

    return $normalized;
}

// Helper function to categorize and transform events
function processEvent($event, $sourceFile) {
    // Handle nested event structure (wrapper has dataType, actual data is in event property)
    if (isset($event['event']) && is_array($event['event'])) {
        $event = $event['event'];
    }

    $dataType = $event['dataType'] ?? $event['DataType'] ?? null;
    if (!$dataType) {
        return null;
    }

    $normalized = normalizeEvent($event);
    $normalized['source_file'] = $sourceFile;

    switch ($dataType) {
        case 'Session':
            $normalized['sessionId'] = $event['sessionId'] ?? $event['SessionId'] ?? '';
            // Handle both 'action' field (SessionStart/SessionEnd) and 'EventType' field (Start/End)
            $action = $event['action'] ?? null;
            $eventType = $event['eventType'] ?? $event['EventType'] ?? null;
            if ($action) {
                $normalized['action'] = $action;
            } elseif ($eventType) {
                $normalized['action'] = $eventType === 'Start' ? 'SessionStart' : 'SessionEnd';
            } else {
                $normalized['action'] = 'Unknown';
            }
            $normalized['duration'] = $event['durationSeconds'] ?? $event['DurationSeconds'] ?? 0;
            $normalized['companyCount'] = $event['companyCount'] ?? $event['CompanyCount'] ?? 0;
            return ['category' => 'Session', 'data' => $normalized];

        case 'Export':
            $normalized['ExportType'] = $event['exportType'] ?? $event['ExportType'] ?? 'Unknown';
            $normalized['DurationMS'] = $event['durationMs'] ?? $event['DurationMs'] ?? 0;
            $normalized['FileSize'] = $event['fileSizeBytes'] ?? $event['FileSizeBytes'] ?? null;
            $normalized['RecordCount'] = $event['recordCount'] ?? $event['RecordCount'] ?? 0;
            return ['category' => 'Export', 'data' => $normalized];

        case 'ApiUsage':
            $serviceName = $event['serviceName'] ?? $event['ServiceName'] ?? 'Unknown';
            $normalized['DurationMS'] = $event['durationMs'] ?? $event['DurationMs'] ?? 0;
            $normalized['Success'] = $event['success'] ?? $event['Success'] ?? true;
            $normalized['Endpoint'] = $event['endpoint'] ?? $event['Endpoint'] ?? '';
            $normalized['ErrorMessage'] = $event['errorMessage'] ?? $event['ErrorMessage'] ?? null;

            switch ($serviceName) {
                case 'OpenAI':
                    $normalized['TokensUsed'] = $event['tokensUsed'] ?? $event['TokensUsed'] ?? 0;
                    $normalized['Model'] = $event['model'] ?? $event['Model'] ?? 'Unknown';
                    return ['category' => 'OpenAI', 'data' => $normalized];

                case 'ExchangeRate':
                    return ['category' => 'OpenExchangeRates', 'data' => $normalized];

                case 'GoogleSheets':
                    return ['category' => 'GoogleSheets', 'data' => $normalized];

                case 'AzureReceipt':
                    $normalized['ServiceName'] = 'AzureReceipt';
                    return ['category' => 'ReceiptScanning', 'data' => $normalized];

                default:
                    return null;
            }

        case 'Error':
            $normalized['Category'] = $event['category'] ?? $event['Category'] ?? 'Unknown';
            $normalized['Severity'] = $event['severity'] ?? $event['Severity'] ?? 'Error';
            $normalized['Message'] = $event['message'] ?? $event['Message'] ?? '';
            $normalized['StackTrace'] = $event['stackTrace'] ?? $event['StackTrace'] ?? '';
            $normalized['Context'] = $event['context'] ?? $event['Context'] ?? '';
            // Legacy field mapping for existing charts
            $normalized['ErrorCategory'] = $normalized['Category'];
            $normalized['ErrorCode'] = $normalized['Category'];
            return ['category' => 'Error', 'data' => $normalized];

        case 'FeatureUsage':
            $normalized['FeatureName'] = $event['featureName'] ?? $event['FeatureName'] ?? 'Unknown';
            $normalized['Context'] = $event['context'] ?? $event['Context'] ?? '';
            $normalized['DurationMs'] = $event['durationMs'] ?? $event['DurationMs'] ?? 0;
            $normalized['Metadata'] = $event['metadata'] ?? $event['Metadata'] ?? [];
            return ['category' => 'FeatureUsage', 'data' => $normalized];

        default:
            return null;
    }
}

if (!is_dir($dataDir)) {
    $errorMessage = "Directory 'data-logs/' does not exist.";
} else {
    $dataFiles = glob($dataDir . '*.json');
    if (!$dataFiles || count($dataFiles) === 0) {
        $errorMessage = "No anonymous data files found.";
    } else {
        // Sort files by modification time (newest first) for file info display
        usort($dataFiles, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $totalFiles = count($dataFiles);
        $processedFiles = 0;
        $failedFiles = 0;

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

            // New Avalonia format: array of events with dataType/DataType field
            // Check for both camelCase (dataType) and PascalCase (DataType)
            $isEventArray = is_array($fileData) && isset($fileData[0]) &&
                (isset($fileData[0]['dataType']) || isset($fileData[0]['DataType']));

            if ($isEventArray) {
                foreach ($fileData as $event) {
                    $result = processEvent($event, $sourceFile);
                    if ($result !== null) {
                        $category = $result['category'];
                        $data = $result['data'];

                        if (!isset($aggregatedData['dataPoints'][$category])) {
                            $aggregatedData['dataPoints'][$category] = [];
                        }
                        $aggregatedData['dataPoints'][$category][] = $data;

                        // Enable geo-location if any event has it
                        if (!empty($data['country']) && $data['country'] !== 'Unknown') {
                            $aggregatedData['geoLocationEnabled'] = true;
                        }
                    }
                }
                $processedFiles++;
            }
            // Single event object with dataType/DataType
            elseif (isset($fileData['dataType']) || isset($fileData['DataType'])) {
                $result = processEvent($fileData, $sourceFile);
                if ($result !== null) {
                    $category = $result['category'];
                    $data = $result['data'];

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

// Convert aggregated data to JSON for JavaScript
$jsonData = json_encode($aggregatedData);

// Include the shared header
include '../admin_header.php';
?>

<style>
.tabs-container {
    margin-bottom: 2rem;
}

.tab-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0;
}

.tab-button {
    padding: 12px 20px;
    background: #f9fafb;
    border: 1px solid #d1d5db;
    border-bottom: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    border-radius: 8px 8px 0 0;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.tab-button:hover {
    background: #f3f4f6;
    color: #374151;
}

.tab-button.active {
    background: white;
    color: #1f2937;
    border-color: #9ca3af;
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: -2px;
    border-bottom: 2px solid white;
}

.tab-content {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

.tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

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
    color: #6b7280;
    margin: 0 0 0.5rem 0;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-card .value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0.5rem 0;
}

.stat-card .subtext {
    font-size: 0.75rem;
    color: #9ca3af;
    margin: 0;
}

.chart-section {
    margin-bottom: 3rem;
}

.section-title {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #374151;
    font-size: 1.5rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .tab-buttons {
        gap: 2px;
    }
    
    .tab-button {
        padding: 10px 14px;
        font-size: 13px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}
</style>

<div class="container">
    <?php if ($errorMessage): ?>
        <div class="error-message">
            <strong>Error:</strong> <?= htmlspecialchars($errorMessage) ?>
            <br><small>Make sure the data directory exists and contains valid JSON files.</small>
        </div>
    <?php elseif (
        empty($aggregatedData['dataPoints']) ||
        (count($aggregatedData['dataPoints']['Export']) == 0 &&
            count($aggregatedData['dataPoints']['OpenAI']) == 0 &&
            count($aggregatedData['dataPoints']['OpenExchangeRates']) == 0 &&
            count($aggregatedData['dataPoints']['GoogleSheets']) == 0 &&
            count($aggregatedData['dataPoints']['ReceiptScanning']) == 0 &&
            count($aggregatedData['dataPoints']['Session']) == 0 &&
            count($aggregatedData['dataPoints']['Error']) == 0 &&
            count($aggregatedData['dataPoints']['FeatureUsage']) == 0)
    ): ?>
        <div class="no-data">
            <h3>No Data Available</h3>
            <p>No anonymous data has been collected yet. Data will appear here once users start using the application and uploading their analytics.</p>
            <p><small>Data files are automatically uploaded to: <code>admin/data-logs/</code></small></p>
        </div>
    <?php else: ?>
        <div class="data-info">
            <strong>Data Summary:</strong>
            <?= $fileInfo['processed_files'] ?> files processed
            (<?= $fileInfo['total_files'] ?> total files)
            <?php if ($fileInfo['failed_files'] > 0): ?>
                | <?= $fileInfo['failed_files'] ?> files failed to process
            <?php endif; ?>
            <br>
            <strong>Latest Data:</strong> <?= date('M j, Y g:i A', $fileInfo['latest_modified']) ?>
            <?php if ($fileInfo['oldest_file'] !== $fileInfo['latest_file']): ?>
                | <strong>Oldest Data:</strong> <?= date('M j, Y g:i A', $fileInfo['oldest_modified']) ?>
            <?php endif; ?>
        </div>

        <div class="tabs-container">
            <div class="tab-buttons">
                <div class="tab-button active" onclick="switchTab('overview')">Overview</div>
                <?php if ($aggregatedData['geoLocationEnabled']): ?>
                <div class="tab-button" onclick="switchTab('geographic')">Geographic</div>
                <?php endif; ?>
                <div class="tab-button" onclick="switchTab('versions')">Versions</div>
                <div class="tab-button" onclick="switchTab('features')">Features</div>
                <div class="tab-button" onclick="switchTab('errors')">Errors</div>
                <div class="tab-button" onclick="switchTab('usage')">Usage</div>
                <div class="tab-button" onclick="switchTab('api')">API Usage</div>
                <div class="tab-button" onclick="switchTab('activity')">Activity</div>
            </div>

            <!-- Overview Tab -->
            <div id="overview-tab" class="tab-content active">
                <div class="stats-grid" id="statsGrid">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>

            <!-- Geographic Tab -->
            <?php if ($aggregatedData['geoLocationEnabled']): ?>
            <div id="geographic-tab" class="tab-content">
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
                        <h2>Feature Usage by Region</h2>
                        <canvas id="featureUsageByRegionChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Performance by Country</h2>
                        <canvas id="performanceByCountryChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Error Rates by Country</h2>
                        <canvas id="errorRatesByCountryChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Session Duration by Region</h2>
                        <canvas id="sessionDurationByRegionChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Timezone Distribution</h2>
                        <canvas id="timezoneChart"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Versions Tab -->
            <div id="versions-tab" class="tab-content">
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
                        <h2>Most Active Versions</h2>
                        <canvas id="topVersionsChart"></canvas>
                    </div>
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
            <div id="features-tab" class="tab-content">
                <h2 class="section-title">Feature Usage Analytics</h2>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Most Used Features</h2>
                        <canvas id="featureUsageChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Page Views Distribution</h2>
                        <canvas id="pageViewsChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Feature Usage Over Time</h2>
                        <canvas id="featureTimelineChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Average Feature Duration</h2>
                        <canvas id="featureDurationChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Import Operations</h2>
                        <canvas id="importStatsChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Context Usage</h2>
                        <canvas id="contextUsageChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Errors Tab -->
            <div id="errors-tab" class="tab-content">
                <h2 class="section-title">Error Analysis</h2>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Errors by Category</h2>
                        <canvas id="errorCategoryChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Error Severity Distribution</h2>
                        <canvas id="errorSeverityChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Error Frequency Over Time</h2>
                        <canvas id="errorTimeChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Errors by Category Over Time</h2>
                        <canvas id="errorCategoryTimelineChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Application Stability Overview</h2>
                        <canvas id="stabilityChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Error Context Analysis</h2>
                        <canvas id="errorContextChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Usage Tab -->
            <div id="usage-tab" class="tab-content">
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
            <div id="api-tab" class="tab-content">
                <h2 class="section-title">API Usage</h2>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>OpenAI API Usage</h2>
                        <canvas id="openaiChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>OpenAI Token Usage</h2>
                        <canvas id="openaiTokenChart"></canvas>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Export Durations Over Time</h2>
                        <canvas id="exportDurationChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Exchange Rates API Usage</h2>
                        <canvas id="exchangeRatesChart"></canvas>
                    </div>
                </div>

                <h2 class="section-title" style="margin-top: 2rem;">Receipt Scanning (Azure AI)</h2>

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
            </div>

            <!-- Activity Tab -->
            <div id="activity-tab" class="tab-content">
                <h2 class="section-title">Overall Activity</h2>

                <div class="chart-row">
                    <div class="chart-container">
                        <h2>Export File Sizes</h2>
                        <canvas id="exportFileSizeChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Data Points Over Time</h2>
                        <canvas id="overallActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Tab switching functionality
function switchTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab content
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
    
    // Add active class to clicked button
    event.target.classList.add('active');
}

// Pass PHP data to JavaScript
window.dashboardData = <?= $jsonData ?>;
</script>
<script src="main.js"></script>