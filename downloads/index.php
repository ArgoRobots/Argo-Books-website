<?php
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../resources/format.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../partials/fonts.php';

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


$latestVersion = getLatestVersion();
$systemRequirements = getSystemRequirements();

// The SmartScreen guide config, detection, and markup live in the shared
// partial resources/smartscreen-guide/guide.php (also used by the paid
// landing pages); it's included below where the block renders.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <!-- Platform wording here has to match the page: there is no macOS build yet,
         only a waitlist. The title and description are what Google prints in the
         SERP and what link previews and crawlers read, so promising a Mac download
         here sends Mac users to a signup form they didn't ask for. -->
    <meta name="description"
        content="Download Argo Books free for Windows and Linux. Simple bookkeeping software for small businesses, with easy invoicing, expense tracking, and financial reports. Mac users can join the waitlist.">
    <meta name="keywords"
        content="argo books download, bookkeeping software, Windows, Linux, free accounting software, small business software, invoice software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Download Argo Books | Windows & Linux">
    <meta property="og:description"
        content="Download Argo Books for your platform. Free bookkeeping software with invoicing, expense tracking, and financial reports.">
    <meta property="og:url" content="https://argorobots.com/downloads/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Download Argo Books | Windows & Linux">
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
    <title>Download Argo Books | Windows & Linux</title>

    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/smartscreen-guide.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/styles/link.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
    <!-- Brand typefaces (Fraunces display + IBM Plex Sans body), matched to the rest of the site -->
    <?= argo_font_links('default', '    ') ?>
    <link rel="stylesheet" href="../resources/styles/typography.css">
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
        <?php include __DIR__ . '/../resources/smartscreen-guide/guide.php'; ?>

        <!-- What you get after installing. Sits between the download buttons and the
             requirements because this is where the "should I run an unknown installer"
             hesitation lands, and the page had nothing but text to answer it. -->
        <div class="preview-section">
            <img class="preview-image"
                 src="../resources/images/laptop-coffee-800.webp"
                 srcset="../resources/images/laptop-coffee-800.webp 800w, ../resources/images/laptop-coffee-1200.webp 1200w, ../resources/images/laptop-coffee-1600.webp 1600w"
                 sizes="(max-width: 900px) 100vw, 560px"
                 width="1200" height="900"
                 alt="Argo Books running on a laptop, showing the dashboard with total revenue, expenses, outstanding invoices and recent transactions"
                 loading="lazy" decoding="async">
            <div class="preview-copy">
                <h2>What you get</h2>
                <p>Argo Books opens straight onto your dashboard: revenue, expenses, profit and everything still outstanding, in one place. There is no account to create and no trial clock. Install it, open it, and your books stay on your computer.</p>
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
