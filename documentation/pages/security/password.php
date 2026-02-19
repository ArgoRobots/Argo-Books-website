<?php
$pageTitle = 'Password Protection';
$pageDescription = 'Learn how to set up password protection and biometric login in Argo Books to secure your business data.';
$currentPage = 'password';
$pageCategory = 'security';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Secure access to your business data with password protection and biometric login integration.</p>

            <h2>Setting Up Password Protection</h2>
            <p>Adding a password encrypts your company file so that only you can access it.</p>
            <ol class="steps-list">
                <li>Go to "Settings"</li>
                <li>Select the "Security" tab</li>
                <li>Click "Add Password"</li>
                <li>Enter and confirm your password</li>
            </ol>

            <p>Once a password is set, you'll be prompted to enter it each time you open the company file.</p>

            <div class="warning-box">
                <strong>Important:</strong> There is no password recovery option. If you forget your password, you will not be able to access your company file. Consider storing your password in a secure password manager.
            </div>

            <h2>Changing Your Password</h2>
            <p>To change an existing password:</p>
            <ol class="steps-list">
                <li>Go to "Settings > Security"</li>
                <li>Click "Change Password"</li>
                <li>Enter your current password, then set a new one</li>
            </ol>

            <h2>Biometric Login (Premium)</h2>
            <div class="info-box">
                <p><strong>Premium Feature:</strong> Biometric login is available with the Premium plan.
                <a href="../getting-started/version-comparison.php" class="link">Compare versions</a></p>
            </div>

            <p>Argo Books supports biometric authentication on Windows and macOS, allowing you to use fingerprint or facial recognition for quick, secure access instead of typing your password each time.</p>

            <h3>Enabling Biometric Login</h3>
            <ol class="steps-list">
                <li>First, set up a password (see above)</li>
                <li>In Settings > Security, a biometric toggle will appear below the password section</li>
                <li>Enable the toggle and verify your identity when prompted by your operating system</li>
                <li>Next time you open your company file, a biometric login button will appear alongside the password field</li>
            </ol>

            <h3>Platform Support</h3>
            <ul>
                <li><strong>Windows:</strong> Uses Windows Hello (fingerprint reader, facial recognition camera, or PIN)</li>
                <li><strong>macOS:</strong> Uses Touch ID</li>
                <li><strong>Linux:</strong> Biometric login is not currently supported</li>
            </ul>

            <div class="info-box">
                <strong>Note:</strong> Biometric login requires compatible hardware and proper configuration in your operating system settings. If your device does not have biometric hardware, the biometric button will not appear on the password prompt when opening a file.
            </div>

            <h2>Auto-Lock</h2>
            <p>When a password is set, Argo Books can automatically lock your company file after a period of inactivity. This protects your data if you step away from your computer. You can configure the auto-lock timeout in Settings > Security.</p>

            <div class="page-navigation">
                <a href="encryption.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Encryption
                </a>
                <a href="backups.php" class="nav-button next">
                    Next: Regular Backups
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
