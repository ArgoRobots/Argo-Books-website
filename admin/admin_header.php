<?php
require_once __DIR__ . '/../resources/icons.php';
// Get current page and directory for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Determine base path for assets (different for subdirectories vs root)
$in_subdir = ($current_dir !== 'admin');
$base_path = $in_subdir ? '../' : '';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script>
        // Apply saved theme immediately to prevent flash
        (function() {
            var theme = localStorage.getItem('admin-theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $base_path; ?>../resources/images/argo-logo/argo-icon.ico">
    <title>Admin - Argo Books</title>

    <!-- Preconnect hints -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        // Set Chart.js default text color based on current theme
        (function() {
            var theme = document.documentElement.getAttribute('data-theme') || 'light';
            Chart.defaults.color = theme === 'dark' ? '#ffffff' : '#666666';
        })();
    </script>
    <script src="<?php echo $base_path; ?>../resources/notifications/notifications.js" defer></script>
    <script src="<?php echo $base_path; ?>pagination.js" defer></script>
    <script src="<?php echo $base_path; ?>section-tabs.js" defer></script>

    <link rel="stylesheet" href="<?php echo $base_path; ?>common-style.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>../resources/styles/link.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>../resources/styles/button.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>../resources/notifications/notifications.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>../resources/styles/table-auto-size.css">
</head>

<body>
    <div class="admin-wrapper">
        <!-- Admin Header -->
        <header class="admin-header">
            <!-- BURGER MENU -->
            <input class="menu-btn" type="checkbox" id="menu-btn" onclick="headerToggleMenu()">
            <label class="menu-icon" id="menu-icon" for="menu-btn"><span class="nav-icon"></span></label>

            <div class="header-container">
                <!-- Left: Home button and Logo -->
                <div class="header-left">
                    <a href="<?php echo $base_path; ?>../" class="btn-small btn-home" title="Go to Main Site">
                        <?= svg_icon('home', 16) ?>
                        <span class="home-text">Home</span>
                    </a>

                    <a href="<?php echo $base_path; ?>index.php" class="logo-section">
                        <img src="<?php echo $base_path; ?>../resources/images/argo-logo/argo-icon.ico" alt="Argo Logo" class="header-logo">
                        <span class="header-title">Admin Dashboard</span>
                    </a>
                </div>

                <!-- Center: Desktop Navigation -->
                <nav class="header-nav desktop-nav">
                    <a href="<?php echo $base_path; ?>license/" class="header-link <?php echo $current_dir === 'license' ? 'active' : ''; ?>">
                        Licenses
                    </a>
                    <a href="<?php echo $base_path; ?>payments/" class="header-link <?php echo $current_dir === 'payments' ? 'active' : ''; ?>">
                        Payment Portal
                    </a>
                    <a href="<?php echo $base_path; ?>app-stats/" class="header-link <?php echo $current_dir === 'app-stats' ? 'active' : ''; ?>">
                        App Stats
                    </a>
                    <a href="<?php echo $base_path; ?>website-stats/" class="header-link <?php echo $current_dir === 'website-stats' ? 'active' : ''; ?>">
                        Website Stats
                    </a>
                    <a href="<?php echo $base_path; ?>referral-links/" class="header-link <?php echo $current_dir === 'referral-links' ? 'active' : ''; ?>">
                        Referrals
                    </a>
                    <a href="<?php echo $base_path; ?>users/" class="header-link <?php echo $current_dir === 'users' ? 'active' : ''; ?>">
                        Users
                    </a>
                    <a href="<?php echo $base_path; ?>reports/" class="header-link <?php echo $current_dir === 'reports' ? 'active' : ''; ?>">
                        Reports
                    </a>
                    <a href="<?php echo $base_path; ?>outreach/" class="header-link <?php echo $current_dir === 'outreach' ? 'active' : ''; ?>">
                        Outreach
                    </a>
                    <a href="<?php echo $base_path; ?>settings/" class="header-link <?php echo $current_dir === 'settings' ? 'active' : ''; ?>">
                        2FA
                    </a>
                </nav>

                <!-- Right: Desktop Actions (Logout) -->
                <div class="header-right desktop-actions">
                    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark/light theme">
                        <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                        <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                    </button>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                    <a href="<?php echo $base_path; ?>logout.php" class="btn btn-small btn-red">Logout</a>
                </div>

                <!-- Mobile User Name (right side) -->
                <div class="mobile-user">
                    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark/light theme">
                        <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                        <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                    </button>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                </div>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div id="menu" class="hamburger-nav-menu">
                <ul>
                    <li>
                        <a href="<?php echo $base_path; ?>../" class="home-link">
                            <?= svg_icon('home', 16) ?>
                            Home
                        </a>
                    </li>
                    <li><a href="<?php echo $base_path; ?>index.php" class="<?php echo $current_dir === 'admin' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="<?php echo $base_path; ?>license/" class="<?php echo $current_dir === 'license' ? 'active' : ''; ?>">Licenses</a></li>
                    <li><a href="<?php echo $base_path; ?>payments/" class="<?php echo $current_dir === 'payments' ? 'active' : ''; ?>">Payment Portal</a></li>
                    <li><a href="<?php echo $base_path; ?>app-stats/" class="<?php echo $current_dir === 'app-stats' ? 'active' : ''; ?>">App Stats</a></li>
                    <li><a href="<?php echo $base_path; ?>website-stats/" class="<?php echo $current_dir === 'website-stats' ? 'active' : ''; ?>">Website Stats</a></li>
                    <li><a href="<?php echo $base_path; ?>referral-links/" class="<?php echo $current_dir === 'referral-links' ? 'active' : ''; ?>">Referrals</a></li>
                    <li><a href="<?php echo $base_path; ?>users/" class="<?php echo $current_dir === 'users' ? 'active' : ''; ?>">Users</a></li>
                    <li><a href="<?php echo $base_path; ?>reports/" class="<?php echo $current_dir === 'reports' ? 'active' : ''; ?>">Reports</a></li>
                    <li><a href="<?php echo $base_path; ?>outreach/" class="<?php echo $current_dir === 'outreach' ? 'active' : ''; ?>">Outreach</a></li>
                    <li><a href="<?php echo $base_path; ?>settings/" class="<?php echo $current_dir === 'settings' ? 'active' : ''; ?>">2FA</a></li>
                    <li><a href="<?php echo $base_path; ?>logout.php" class="logout-link">Logout</a></li>
                </ul>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="admin-content">
            <?php if (isset($page_title) || isset($page_description)): ?>
                <div class="page-header">
                    <?php if (isset($page_title)): ?>
                        <h1 class="page-title"><?php echo htmlspecialchars($page_title); ?></h1>
                    <?php endif; ?>
                    <?php if (isset($page_description)): ?>
                        <p class="page-description"><?php echo htmlspecialchars($page_description); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Alert Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info'; ?>">
                    <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
                    <?php
                    echo htmlspecialchars($_SESSION['message']);
                    unset($_SESSION['message']);
                    if (isset($_SESSION['message_type'])) {
                        unset($_SESSION['message_type']);
                    }
                    ?>
                </div>
            <?php endif; ?>

            <script>
                function toggleTheme() {
                    var current = document.documentElement.getAttribute('data-theme') || 'light';
                    var next = current === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', next);
                    localStorage.setItem('admin-theme', next);

                    // Update Chart.js default text color and re-render all charts
                    if (typeof Chart !== 'undefined') {
                        Chart.defaults.color = next === 'dark' ? '#ffffff' : '#666666';
                        Object.values(Chart.instances).forEach(function(chart) {
                            chart.options.scales && Object.values(chart.options.scales).forEach(function(scale) {
                                if (scale.ticks) scale.ticks.color = Chart.defaults.color;
                                if (scale.title) scale.title.color = Chart.defaults.color;
                            });
                            if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                                chart.options.plugins.legend.labels.color = Chart.defaults.color;
                            }
                            chart.update();
                        });
                    }
                }

                const menu = document.getElementById('menu');
                const header = document.querySelector('.admin-header');
                const menuBtn = document.getElementById('menu-btn');
                const menuIcon = document.getElementById('menu-icon');

                function headerToggleMenu() {
                    if (!menu.classList.contains('active')) {
                        // Opening the menu
                        menu.classList.add('active');

                        // Get the current scroll height
                        const currentMenuHeight = menu.scrollHeight;
                        menu.style.height = currentMenuHeight + 'px';

                        document.body.classList.add('menu-open');
                    } else {
                        // Closing the menu
                        menu.classList.remove('active');
                        menu.style.height = '0';
                        document.body.classList.remove('menu-open');
                    }
                }

                // Close menu when clicking outside
                document.addEventListener('click', (e) => {
                    // Don't close if clicking menu icon
                    if (menuIcon.contains(e.target)) {
                        return;
                    }

                    // Close only if menu is active and click is outside header
                    if (menu.classList.contains('active') && !header.contains(e.target)) {
                        menuBtn.checked = false;
                        headerToggleMenu();
                    }
                });

                // Close menu when clicking on a link
                const mobileNavLinks = menu.querySelectorAll('a');
                mobileNavLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        menuBtn.checked = false;
                        headerToggleMenu();
                    });
                });

                // Close menu on escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape' && menu.classList.contains('active')) {
                        menuBtn.checked = false;
                        headerToggleMenu();
                    }
                });
            </script>