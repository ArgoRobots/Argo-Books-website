<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Installation Guide';
$pageDescription = 'Learn how to download and install Argo Books on your Windows, macOS, or Linux computer.';
$currentPage = 'installation';
$pageCategory = 'getting-started';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Follow these steps to install Argo Books on your computer.</p>

            <ol class="steps-list">
                <li>Download the installer <a class="link" href="../../../downloads/">here</a></li>
                <li>Run the installer file (Argo Books Installer.exe)</li>
                <li>Follow the installation wizard</li>
                <li>Launch Argo Books from your desktop or start menu</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> Your computer may display a security warning. This is normal for new applications.
                Click "More info" and then "Run anyway" to proceed.
            </div>

            <div class="page-navigation">
                <a href="system-requirements.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: System Requirements
                </a>
                <a href="quick-start.php" class="nav-button next">
                    Next: Quick Start Tutorial
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
