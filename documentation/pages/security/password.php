<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Password Protection';
$pageDescription = 'Learn how to set up password protection and biometric login in Argo Books to secure your business data.';
$currentPage = 'password';
$pageCategory = 'security';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>Secure access to your business data with password protection and biometric login integration.</p>

            <h2>Setting Up Password Protection</h2>
            <p>Adding a password encrypts your company file so that only you can access it. This is the step that turns encryption on: until a company file has a password, it is stored unencrypted and anyone who obtains the file can read it. See <a href="encryption.php" class="link">Encryption</a> for what that protects and how.</p>
            <ol class="steps-list">
                <li>Go to "Settings"</li>
                <li>Select the "Security" tab</li>
                <li>Click "Add Password"</li>
                <li>Enter and confirm your password</li>
            </ol>

            <p>Once a password is set, you'll be prompted to enter it each time you open the company file.</p>

            <div class="warning-box">
                <strong>Important: there is no password recovery, and no way for us to help.</strong> Your password is not checked against something stored in the file. It is what the encryption key is built from, so nothing in the file, on your computer, or on our servers can reproduce it. If you forget it, the data cannot be recovered by you, by us, or by anyone else. Store your password in a password manager before you rely on it.
            </div>

            <p>This is a deliberate design choice. A recovery option would mean a way into your books that doesn't need your password, and that would be available to anyone who took your file, not just to you.</p>

            <h3>If you're locked out</h3>
            <p>Support cannot unlock a company file, so please don't send us your file. There are only two things that genuinely work, and both depend on something you set up earlier:</p>
            <ul>
                <li><strong>Biometric login, if you enabled it.</strong> If Windows Hello or Touch ID is still set up for this file on this computer and under the same user account, you can open the file with it. Do that, then go straight to Settings &gt; Security and change the password to something you'll keep.</li>
                <li><strong>A backup made before you set the password.</strong> A backup from before the password existed is unencrypted and will open. Backups made afterwards use the same password as the file itself, so those won't help.</li>
            </ul>

            <p>If neither applies, the data is not recoverable. We'd rather be straight with you about that than have you wait on a support ticket that can't succeed.</p>

            <h2>Changing Your Password</h2>
            <p>To change an existing password:</p>
            <ol class="steps-list">
                <li>Go to "Settings > Security"</li>
                <li>Click the "Change" button next to the Password section</li>
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
                <li>In Settings > Security, the "Windows Hello" toggle (on Windows) or "Touch ID" toggle (on macOS) will appear below the password section</li>
                <li>Enable the toggle and verify your identity when prompted by your operating system</li>
                <li>Next time you open your company file, a biometric login button will appear alongside the password field</li>
            </ol>

            <h3>Platform Support</h3>
            <ul>
                <li><strong>Windows:</strong> Uses Windows Hello (fingerprint reader, facial recognition camera, or PIN)</li>
                <li><strong>macOS:</strong> Uses Touch ID</li>
                <li><strong>Linux:</strong> Biometric login is not currently supported</li>
            </ul>

            <h3>How Biometric Login Stores Your Password</h3>
            <p>Biometrics don't replace your password, they unlock it. When you enable the toggle, Argo Books hands your password to your operating system's protected storage, which ties it to your computer and to your signed-in user account. Your fingerprint or face is never seen by Argo Books; the operating system simply confirms it's you and releases the password back.</p>

            <p>Two things follow from that:</p>
            <ul>
                <li>Biometric login only works on that computer, under that user account. Copy the file to another machine and you'll need the password.</li>
                <li>It is as strong as your computer login. Anyone who can sign in as you could open the file, so keep a strong Windows or macOS account password.</li>
            </ul>

            <div class="info-box">
                <strong>Note:</strong> Biometric login requires a password to be set first, and compatible hardware configured in your operating system settings. If your device does not have biometric hardware, the biometric option will not appear in Settings.</div>

            <h2>Auto-Lock</h2>
            <p>When a password is set, Argo Books can automatically lock your company file after a period of inactivity. This protects your data if you step away from your computer. You can configure the auto-lock timeout in Settings > Security.</p>

            <div class="page-navigation">
                <a href="encryption.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Encryption</span>
                </a>
                <a href="backups.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Regular Backups &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
