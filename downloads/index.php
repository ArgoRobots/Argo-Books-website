<?php
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../track_referral.php';

track_referral_event('downloads_page');
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
    $basePath = '../resources/downloads/';

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

// Detect browser for SmartScreen guide (Windows downloads only)
function detectBrowserForGuide(): string
{
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $chUa = $_SERVER['HTTP_SEC_CH_UA'] ?? '';

    if (stripos($chUa, 'Brave') !== false) return 'brave';
    if (stripos($ua, 'Edg/') !== false) return 'edge';
    if (stripos($ua, 'OPR/') !== false || stripos($ua, 'Opera/') !== false) return 'opera';
    if (stripos($ua, 'Vivaldi/') !== false) return 'vivaldi';
    if (stripos($ua, 'Firefox/') !== false) return 'firefox';
    if (stripos($ua, 'Chrome/') !== false) return 'chrome';
    return 'unknown';
}

// Browser-specific walkthroughs for the SmartScreen / download warning.
// Add more browsers by dropping a key here + saving screenshots to
// resources/images/smartscreen-guide/<browser>-step-N.svg.
$smartScreenGuides = [
    'edge' => [
        'browser_name' => 'Microsoft Edge',
        'intro' => 'Edge sometimes flags new apps before they are widely downloaded. Here\'s how to keep the installer:',
        'steps' => [
            [
                'title' => 'Open the Downloads panel, hover the file, and click the ⋯ menu',
                'image' => '../resources/images/smartscreen-guide/edge-step-1.svg',
                'alt'   => 'Edge Downloads panel showing the Argo Books installer with the more-options menu',
            ],
            [
                'title' => 'Choose Keep from the menu',
                'image' => '../resources/images/smartscreen-guide/edge-step-2.svg',
                'alt'   => 'Edge download menu with the Keep option highlighted',
            ],
            [
                'title' => 'Click the arrow next to Delete, then choose Keep anyway',
                'image' => '../resources/images/smartscreen-guide/edge-step-3.svg',
                'alt'   => 'Edge confirmation dialog with the Keep anyway button',
            ],
        ],
    ],
];

// Only Edge gets an illustrated, browser-specific keep-guide: Edge reliably nags
// on downloads and we have accurate screenshots for it. Other browsers (Chrome,
// Firefox, etc.) rarely warn on a signed installer, and we can't verify their
// exact dialogs, so they fall through to the Windows launch step alone.

// Windows launch step. Appended as the final step of the walkthrough (after the
// browser's "keep" steps), because the "Windows protected your PC" prompt appears
// when the installer is opened. One combined step on purpose.
$windowsLaunchStep = [
    'title' => 'Open the installer. If Windows shows "Windows protected your PC", click More info, then Run anyway.',
    'image' => '../resources/images/smartscreen-guide/windows-step.svg',
    'alt'   => 'Windows protected your PC dialog showing Argo Books Installer, publisher Evan Di Placido, with the Run anyway button highlighted',
];

$browserKey = detectBrowserForGuide();
$smartScreenGuide = $smartScreenGuides[$browserKey] ?? null;
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
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">
    <meta name="geo.position" content="52.1579;-106.6702">
    <meta name="ICBM" content="52.1579, -106.6702">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/downloads/">

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Download Argo Books | Windows, macOS & Linux</title>

    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/styles/link.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../resources/header/header.php'; ?>
    </header>
    <main>

    <section class="hero">
        <div class="hero-bg">
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
        </div>
        <div class="hero-content">
            <h1>Download Argo Books</h1>
            <p>Get started for free. No account required.</p>
        </div>
    </section>

    <div class="container">
        <div class="platform-grid">
            <!-- Windows -->
            <div class="platform-card platform-windows">
                <div class="platform-icon">
                    <?= svg_icon('windows') ?>
                </div>
                <div class="platform-info">
                    <h2>Windows</h2>
                    <p class="platform-desc">For Windows 10 and later</p>
                    <?php if ($latestVersion && isset($latestVersion['platforms']['windows'])): ?>
                        <div class="version-details">
                            <span class="version-tag">V.<?php echo htmlspecialchars($latestVersion['version']); ?></span>
                            <span class="file-size"><?php echo formatFileSize($latestVersion['platforms']['windows']['filesize']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="platform-actions">
                    <a href="../download/avalonia/win" class="btn btn-blue download-btn" data-platform="windows">
                        <?= svg_icon('download', null, 'btn-icon') ?>
                        Download for Windows
                    </a>
                </div>
            </div>

            <!-- macOS: no build yet, so the action is a launch-notification
                 waitlist signup (api/waitlist/subscribe.php). -->
            <div class="platform-card platform-macos">
                <div class="platform-icon">
                    <?= svg_icon('apple') ?>
                </div>
                <div class="platform-info">
                    <h2>macOS</h2>
                    <p class="platform-desc">For macOS 14 Sonoma and later</p>
                    <div class="version-details">
                        <span class="version-tag">Coming soon</span>
                    </div>
                </div>
                <div class="platform-actions">
                    <form class="waitlist-form" id="macWaitlistForm" autocomplete="off" novalidate>
                        <div class="waitlist-fields">
                            <input type="email" name="email" class="waitlist-email" placeholder="you@example.com"
                                   required aria-label="Email address for the Mac release notification">
                            <button type="submit" class="btn btn-blue waitlist-submit">Notify me</button>
                        </div>
                        <!-- Honeypot: hidden from real users, bots autofill it -->
                        <input type="text" name="website" class="waitlist-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <p class="waitlist-note" id="macWaitlistNote">One email when the Mac version ships. Nothing else.</p>
                    </form>
                    <div class="waitlist-success" id="macWaitlistSuccess" hidden>
                        <?= svg_icon('check', 16) ?>
                        <span>You're on the list</span>
                    </div>
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
                    <p class="platform-desc">Ubuntu, Debian, Fedora & more (AppImage)</p>
                    <?php if ($latestVersion && isset($latestVersion['platforms']['linux'])): ?>
                        <div class="version-details">
                            <span class="version-tag">V.<?php echo htmlspecialchars($latestVersion['version']); ?></span>
                            <span class="file-size"><?php echo formatFileSize($latestVersion['platforms']['linux']['filesize']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="platform-actions">
                    <a href="../download/avalonia/linux" class="btn btn-blue download-btn" data-platform="linux">
                        <?= svg_icon('download', null, 'btn-icon') ?>
                        Download for Linux
                    </a>
                    <button type="button" class="install-help-link" id="linuxInstallHelp">Installation instructions</button>
                </div>
            </div>
        </div>

        <!-- Post-download walkthrough: the browser's "keep" steps (when the browser
             warns) followed by a final Windows launch step, as one continuous
             numbered list. Revealed after a Windows download click. -->
        <?php
        if ($smartScreenGuide) {
            $guideHeading = $smartScreenGuide['browser_name'] . ' has an extra confirmation step';
            $guideIntro   = $smartScreenGuide['intro'];
            $guideSteps   = $smartScreenGuide['steps'];
        } else {
            // Browser without a keep-guide: still show the Windows launch step alone.
            $guideHeading = 'Opening Argo Books on Windows';
            $guideIntro   = 'Windows may warn you because Argo Books is a newer app, not because it is unsafe. After the download finishes, here is the last step to open it:';
            $guideSteps   = [];
        }
        // Append the Windows launch step as the final numbered step.
        if (!empty($windowsLaunchStep)) {
            $guideSteps[] = $windowsLaunchStep;
        }
        ?>
        <?php if ($guideSteps): ?>
        <div class="download-guides" id="downloadGuides" data-browser="<?php echo htmlspecialchars($browserKey); ?>" style="--step-count: <?php echo count($guideSteps); ?>;" hidden>
            <div class="smartscreen-guide">
                <div class="smartscreen-guide-header">
                    <div class="smartscreen-status">
                        <?= svg_icon('check', 16) ?>
                        <span>Your download is starting</span>
                    </div>
                    <h2><?php echo htmlspecialchars($guideHeading); ?></h2>
                    <p><?php echo htmlspecialchars($guideIntro); ?></p>
                </div>
                <ol class="smartscreen-steps" style="--step-count: <?php echo count($guideSteps); ?>;">
                    <?php foreach ($guideSteps as $i => $step): ?>
                    <li class="smartscreen-step">
                        <?php if (count($guideSteps) > 1): ?>
                        <div class="smartscreen-step-number"><?php echo $i + 1; ?></div>
                        <?php endif; ?>
                        <p class="smartscreen-step-title"><?php echo htmlspecialchars($step['title']); ?></p>
                        <?php if (!empty($step['image'])): ?>
                        <div class="smartscreen-step-image">
                            <img src="<?php echo htmlspecialchars($step['image']); ?>" alt="<?php echo htmlspecialchars($step['alt'] ?? ''); ?>" loading="lazy">
                        </div>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
        <?php endif; ?>

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
                    <?= svg_icon('clock') ?>
                </div>
                <div class="additional-content">
                    <h3>Looking for older versions?</h3>
                    <p>Access previous releases of Argo Books for compatibility or testing purposes.</p>
                    <a href="../older-versions/" class="link-arrow">
                        View older versions
                        <?= svg_icon('arrow-right-sm') ?>
                    </a>
                </div>
            </div>
            <div class="additional-card">
                <div class="additional-icon">
                    <?= svg_icon('document-lines') ?>
                </div>
                <div class="additional-content">
                    <h3>Need help getting started?</h3>
                    <p>Check out our documentation for installation guides and tutorials.</p>
                    <a href="../documentation/" class="link-arrow">
                        View documentation
                        <?= svg_icon('arrow-right-sm') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    </main>

    <!-- Linux installation instructions modal -->
    <div class="install-modal" id="linuxInstallModal">
        <div class="install-modal-backdrop"></div>
        <div class="install-modal-content" role="dialog" aria-modal="true" aria-labelledby="linuxInstallModalTitle">
            <button class="install-modal-close" aria-label="Close">&times;</button>
            <h2 id="linuxInstallModalTitle">Installing on Linux</h2>
            <ol class="install-modal-steps">
                <li>Download the AppImage file.</li>
                <li>Right-click the downloaded file and choose <strong>Properties</strong>.</li>
                <li>In the <strong>Permissions</strong> tab, check <strong>"Allow executing file as program"</strong> (the wording varies slightly between distros).</li>
                <li>Double-click the file to launch Argo Books.</li>
            </ol>
            <p class="install-modal-alt">Prefer the terminal? Run <code>chmod +x ArgoBooks-<?php echo $latestVersion ? htmlspecialchars($latestVersion['version']) : 'X.X.X'; ?>-linux-x64.AppImage</code> instead.</p>
            <p class="install-modal-note">AppImages are self-contained: there's nothing else to install, and you can keep the file anywhere you like. See the <a href="../documentation/pages/getting-started/installation.php">full installation guide</a> for more.</p>
        </div>
    </div>

    <footer class="footer">
        <?php include __DIR__ . '/../resources/footer/footer.php'; ?>
    </footer>

    <script>
        const downloadGuides = document.getElementById('downloadGuides');

        // Add download tracking + reveal SmartScreen guide for Windows downloads
        document.querySelectorAll('.download-btn:not(.disabled)').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                const platform = this.getAttribute('data-platform');
                if (platform && typeof gtag !== 'undefined') {
                    gtag('event', 'download_click', {
                        'event_category': 'software',
                        'event_label': 'argo_books_' + platform,
                        'platform': platform
                    });

                    // Google Ads conversion: fire on the download click
                    gtag('event', 'conversion', {'send_to': 'AW-17210317271/niGZCJv2vbkbENezwo5A'});
                }

                if (platform === 'windows' && downloadGuides) {
                    downloadGuides.hidden = false;
                    requestAnimationFrame(function() {
                        downloadGuides.querySelectorAll('.smartscreen-guide')
                            .forEach(function(g) { g.classList.add('is-visible'); });
                        setTimeout(function() {
                            const targetY = downloadGuides.getBoundingClientRect().top
                                + window.pageYOffset - 130;
                            window.scrollTo({ top: targetY, behavior: 'smooth' });
                        }, 120);
                    });
                }
            });
        });

        // macOS waitlist signup ("notify me when the Mac version ships")
        (function () {
            const form = document.getElementById('macWaitlistForm');
            const success = document.getElementById('macWaitlistSuccess');
            const note = document.getElementById('macWaitlistNote');
            if (!form || !success || !note) return;
            const defaultNote = note.textContent;

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const emailInput = form.querySelector('.waitlist-email');
                const submitBtn = form.querySelector('.waitlist-submit');
                const email = (emailInput.value || '').trim();
                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    note.textContent = 'Please enter a valid email address.';
                    note.classList.add('waitlist-note-error');
                    emailInput.focus();
                    return;
                }
                note.textContent = defaultNote;
                note.classList.remove('waitlist-note-error');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Adding…';

                fetch('../api/waitlist/subscribe.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        email: email,
                        platform: 'macos',
                        website: form.querySelector('.waitlist-hp').value || ''
                    })
                }).then(function (res) {
                    return res.json().catch(function () { return {}; }).then(function (data) {
                        return { ok: res.ok, data: data };
                    });
                }).then(function (r) {
                    if (r.ok && r.data.success) {
                        form.hidden = true;
                        success.hidden = false;
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'mac_waitlist_signup', { 'event_category': 'software' });
                        }
                    } else {
                        note.textContent = (r.data && r.data.error) || 'Something went wrong. Please try again.';
                        note.classList.add('waitlist-note-error');
                    }
                }).catch(function () {
                    note.textContent = 'Something went wrong. Please try again.';
                    note.classList.add('waitlist-note-error');
                }).finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Notify me';
                });
            });
        })();

        // Linux installation instructions modal
        const installModal = document.getElementById('linuxInstallModal');
        const installHelpLink = document.getElementById('linuxInstallHelp');

        function closeInstallModal() {
            installModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (installModal && installHelpLink) {
            installHelpLink.addEventListener('click', function() {
                installModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
            installModal.querySelector('.install-modal-close').addEventListener('click', closeInstallModal);
            installModal.querySelector('.install-modal-backdrop').addEventListener('click', closeInstallModal);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && installModal.classList.contains('active')) {
                    closeInstallModal();
                }
            });
        }
    </script>
</body>

</html>
