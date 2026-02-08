<?php
// Load system requirements from JSON
function getSystemRequirements()
{
    $jsonPath = '../resources/data/system-requirements.json';
    if (file_exists($jsonPath)) {
        $json = file_get_contents($jsonPath);
        return json_decode($json, true);
    }
    return [];
}

// Get platform icon SVG path
function getPlatformIconPath($platform)
{
    $icons = [
        'windows' => 'M0 3.449L9.75 2.1v9.451H0m10.949-9.602L24 0v11.4H10.949M0 12.6h9.75v9.451L0 20.699M10.949 12.6H24V24l-12.9-1.801',
        'macos' => 'M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z',
        'linux' => 'M12.504 0c-.155 0-.315.008-.48.021-4.226.333-3.105 4.807-3.17 6.298-.076 1.092-.3 1.953-1.05 3.02-.885 1.051-2.127 2.75-2.716 4.521-.278.832-.41 1.684-.287 2.489a.424.424 0 00-.11.135c-.26.268-.45.6-.663.839-.199.199-.485.267-.797.4-.313.136-.658.269-.864.68-.09.189-.136.394-.132.602 0 .199.027.4.055.536.058.399.116.728.04.97-.249.68-.28 1.145-.106 1.484.174.334.535.47.94.601.81.2 1.91.135 2.774.6.926.466 1.866.67 2.616.47.526-.116.97-.464 1.208-.946.587-.003 1.23-.269 2.26-.334.699-.058 1.574.267 2.577.2.025.134.063.198.114.333l.003.003c.391.778 1.113 1.132 1.884 1.071.771-.06 1.592-.536 2.257-1.306.631-.765 1.683-1.084 2.378-1.503.348-.199.629-.469.649-.853.023-.4-.2-.811-.714-1.376v-.097l-.003-.003c-.17-.2-.25-.535-.338-.926-.085-.401-.182-.786-.492-1.046h-.003c-.059-.054-.123-.067-.188-.135a.357.357 0 00-.19-.064c.431-1.278.264-2.55-.173-3.694-.533-1.41-1.465-2.638-2.175-3.483-.796-1.005-1.576-1.957-1.56-3.368.026-2.152.236-6.133-3.544-6.139z'
    ];
    return $icons[$platform] ?? '';
}

// Platform file patterns for Avalonia builds
$avaloniaPatterns = [
    'windows' => 'Argo Books Installer V.{version}.exe',
    'macos'   => 'ArgoBooks-{version}-osx-arm64.zip',
    'linux'   => 'ArgoBooks-{version}-linux-x64.AppImage',
];

// Get latest version information from filesystem
function getLatestVersion()
{
    $basePath = '../resources/downloads/versions/';

    if (!is_dir($basePath)) {
        return null;
    }

    $versions = [];
    foreach (scandir($basePath) as $folder) {
        if ($folder === '.' || $folder === '..') continue;
        if (!is_dir($basePath . $folder)) continue;
        if (!preg_match('/^\d+\.\d+\.\d+/', $folder)) continue;
        $versions[] = $folder;
    }

    if (empty($versions)) {
        return null;
    }

    usort($versions, function ($a, $b) {
        return version_compare($b, $a);
    });

    $latest = $versions[0];

    // Gather per-platform file sizes
    global $avaloniaPatterns;
    $platforms = [];
    foreach ($avaloniaPatterns as $platform => $pattern) {
        $filename = str_replace('{version}', $latest, $pattern);
        $filepath = $basePath . $latest . '/' . $filename;
        if (file_exists($filepath)) {
            $platforms[$platform] = [
                'filename' => $filename,
                'filesize' => filesize($filepath),
            ];
        }
    }

    return [
        'version'   => $latest,
        'platforms' => $platforms,
    ];
}

function formatFileSize($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, 1) . ' ' . $units[$pow];
}

$latestVersion = getLatestVersion();
$systemRequirements = getSystemRequirements();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Download Argo Books for Windows, macOS, and Linux. Free bookkeeping software for small businesses. Get started with easy invoicing, expense tracking, and financial reports.">
    <meta name="keywords"
        content="argo books download, bookkeeping software, Windows, macOS, Linux, free accounting software, small business software, invoice software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Download Argo Books | Windows, macOS & Linux">
    <meta property="og:description"
        content="Download Argo Books for your platform. Free bookkeeping software with invoicing, expense tracking, and financial reports.">
    <meta property="og:url" content="https://argorobots.com/downloads/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Download Argo Books | Windows, macOS & Linux">
    <meta name="twitter:description"
        content="Download Argo Books for your platform. Free bookkeeping software with invoicing, expense tracking, and financial reports.">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Saskatoon">
    <meta name="geo.position" content="52.1579;-106.6702">
    <meta name="ICBM" content="52.1579, -106.6702">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/downloads/">

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/A-logo.ico">
    <title>Download Argo Books | Windows, macOS & Linux</title>

    <script src="../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/styles/icons.css">
    <link rel="stylesheet" href="../resources/styles/link.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>

    <section class="hero">
        <div class="hero-bg">
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
        </div>
        <div class="hero-content">
            <h1>Download Argo Books</h1>
            <p>Choose your platform and get started for free</p>
        </div>
    </section>

    <div class="container">
        <div class="platform-grid">
            <!-- Windows -->
            <div class="platform-card platform-windows">
                <div class="platform-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M0 3.449L9.75 2.1v9.451H0m10.949-9.602L24 0v11.4H10.949M0 12.6h9.75v9.451L0 20.699M10.949 12.6H24V24l-12.9-1.801"/>
                    </svg>
                </div>
                <div class="platform-info">
                    <h2>Windows</h2>
                    <p class="platform-desc">For Windows 10 and later</p>
                    <?php if ($latestVersion && isset($latestVersion['platforms']['windows'])): ?>
                        <div class="version-details">
                            <span class="version-tag">v<?php echo htmlspecialchars($latestVersion['version']); ?></span>
                            <span class="file-size"><?php echo formatFileSize($latestVersion['platforms']['windows']['filesize']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="platform-actions">
                    <a href="../download/avalonia/win" class="btn btn-blue download-btn" data-platform="windows">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Download for Windows
                    </a>
                    <?php if ($latestVersion): ?>
                        <span class="platform-badge available">V.<?php echo htmlspecialchars($latestVersion['version']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- macOS -->
            <div class="platform-card platform-macos">
                <div class="platform-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                </div>
                <div class="platform-info">
                    <h2>macOS</h2>
                    <p class="platform-desc">For macOS 11 Big Sur and later</p>
                </div>
                <div class="platform-actions">
                    <button class="btn btn-gray download-btn disabled" disabled>
                        Coming Soon
                    </button>
                </div>
            </div>

            <!-- Linux -->
            <div class="platform-card platform-linux">
                <div class="platform-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="<?php echo getPlatformIconPath('linux'); ?>"/>
                    </svg>
                </div>
                <div class="platform-info">
                    <h2>Linux</h2>
                    <p class="platform-desc">Ubuntu, Debian, Fedora & more</p>
                </div>
                <div class="platform-actions">
                    <button class="btn btn-gray download-btn disabled" disabled>
                        Coming Soon
                    </button>
                </div>
            </div>
        </div>

        <!-- System Requirements -->
        <div class="requirements-section">
            <h2>System Requirements</h2>
            <div class="requirements-grid">
                <?php foreach ($systemRequirements as $platform => $data): ?>
                <div class="requirement-card">
                    <h3>
                        <svg viewBox="0 0 24 24" fill="currentColor" class="req-icon">
                            <path d="<?php echo getPlatformIconPath($platform); ?>"/>
                        </svg>
                        <?php echo htmlspecialchars($data['name']); ?>
                    </h3>
                    <ul>
                        <?php foreach ($data['requirements'] as $req): ?>
                        <li><?php echo htmlspecialchars($req); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Additional Links -->
        <div class="additional-section">
            <div class="additional-card">
                <div class="additional-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="additional-content">
                    <h3>Looking for older versions?</h3>
                    <p>Access previous releases of Argo Books for compatibility or testing purposes.</p>
                    <a href="../older-versions/" class="link-arrow">
                        View older versions
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="additional-card">
                <div class="additional-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <div class="additional-content">
                    <h3>Need help getting started?</h3>
                    <p>Check out our documentation for installation guides and tutorials.</p>
                    <a href="../documentation/" class="link-arrow">
                        View documentation
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>

    <script>
        // Add download tracking
        document.querySelectorAll('.download-btn:not(.disabled)').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                const platform = this.getAttribute('data-platform');
                if (platform && typeof gtag !== 'undefined') {
                    gtag('event', 'download_click', {
                        'event_category': 'software',
                        'event_label': 'argo_books_' + platform,
                        'platform': platform
                    });
                }
            });
        });
    </script>
</body>

</html>
