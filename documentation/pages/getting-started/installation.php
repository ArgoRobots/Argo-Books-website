<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Installation Guide';
$pageDescription = 'Learn how to download and install Argo Books on Windows, macOS, or Linux.';
$currentPage = 'installation';
$pageCategory = 'getting-started';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>Follow these steps to download and install Argo Books on your computer.</p>

            <h2>Windows</h2>
            <ol class="steps-list">
                <li>Download the installer from the <a class="link" href="../../../downloads/">downloads page</a></li>
                <li>Run the installer file (<strong>Argo Books Installer.exe</strong>)</li>
                <li>Follow the installation wizard to complete the setup</li>
                <li>Launch Argo Books from your desktop shortcut or start menu</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> Windows may display a SmartScreen warning because the application is from a newer publisher. Click "More info" and then "Run anyway" to proceed with the installation.
            </div>

            <h2>macOS</h2>
            <ol class="steps-list">
                <li>Download the <strong>.zip</strong> for your Mac from the <a class="link" href="../../../downloads/">downloads page</a>. Choose <strong>Apple Silicon</strong> or <strong>Intel</strong> to match your machine; if you are not sure, open the Apple menu and choose <strong>About This Mac</strong>, then read the Chip or Processor line</li>
                <li>Double-click the downloaded .zip to unpack it, which produces <strong>Argo Books.app</strong></li>
                <li>Drag <strong>Argo Books.app</strong> into your <strong>Applications</strong> folder</li>
                <li>Double-click it to launch Argo Books</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> macOS requires 14 Sonoma or later. Downloading the build for the wrong processor is harmless: it simply will not open, and you can take the other one instead.
            </div>

            <h2>Linux</h2>
            <ol class="steps-list">
                <li>Download the <strong>AppImage</strong> file from the <a class="link" href="../../../downloads/">downloads page</a></li>
                <li>Make the file executable: right-click the file, go to Properties > Permissions, and check "Allow executing file as program". The exact wording varies between distributions. From a terminal, <code>chmod +x ArgoBooks-*-linux-x64.AppImage</code> does the same thing.</li>
                <li>Double-click the AppImage to launch Argo Books</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> AppImage files are self-contained and don't require installation. You can move the file to any location on your system and run it from there.
            </div>

            <h2>After Installation</h2>
            <p>Once installed, Argo Books will prompt you to create your first company or open the sample company to explore the features. See the <a class="link" href="quick-start.php">Quick Start Tutorial</a> for next steps.</p>

            <div class="page-navigation">
                <a href="system-requirements.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; System Requirements</span>
                </a>
                <a href="quick-start.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Quick Start Tutorial &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
