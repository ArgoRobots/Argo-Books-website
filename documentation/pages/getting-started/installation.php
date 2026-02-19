<?php
$pageTitle = 'Installation Guide';
$pageDescription = 'Learn how to download and install Argo Books on your Windows, macOS, or Linux computer.';
$currentPage = 'installation';
$pageCategory = 'getting-started';

include '../../docs-header.php';
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
                <li>Download the <strong>.dmg</strong> file from the <a class="link" href="../../../downloads/">downloads page</a></li>
                <li>Open the downloaded .dmg file</li>
                <li>Drag Argo Books to your Applications folder</li>
                <li>Launch Argo Books from your Applications folder or Launchpad</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> If macOS blocks the application, go to System Settings > Privacy & Security and click "Open Anyway" next to the Argo Books message.
            </div>

            <h2>Linux</h2>
            <ol class="steps-list">
                <li>Download the <strong>AppImage</strong> file from the <a class="link" href="../../../downloads/">downloads page</a></li>
                <li>Make the file executable: right-click the file, go to Properties > Permissions, and check "Allow executing file as program" (or run <code>chmod +x</code> on the file)</li>
                <li>Double-click the AppImage to launch Argo Books</li>
            </ol>

            <div class="info-box">
                <strong>Note:</strong> AppImage files are self-contained and don't require installation. You can move the file to any location on your system and run it from there.
            </div>

            <h2>After Installation</h2>
            <p>Once installed, Argo Books will prompt you to create your first company or open the sample company to explore the features. See the <a class="link" href="quick-start.php">Quick Start Tutorial</a> for next steps.</p>

            <div class="page-navigation">
                <a href="system-requirements.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: System Requirements
                </a>
                <a href="quick-start.php" class="nav-button next">
                    Next: Quick Start Tutorial
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
