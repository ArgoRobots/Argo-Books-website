<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Encryption';
$pageDescription = 'Learn about the AES-256-GCM encryption used in Argo Books to protect your business data.';
$currentPage = 'encryption';
$pageCategory = 'security';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>Argo Books uses AES-256-GCM encryption to protect your business data. This is the same encryption standard used by banks and military organizations worldwide, and is considered one of the strongest available.</p>

            <h2>When Encryption Is Active</h2>
            <p><strong>Encryption is switched on by setting a password on your company file.</strong> Your password is what the encryption key is built from, so until you set one there is no key and the file is not encrypted.</p>

            <p>A company file with no password is <strong>not encrypted</strong>. It's compressed, which is not the same thing: anyone who gets hold of the file can read everything in it without needing a password or any special tools. If your company file contains real business data, <a href="password.php" class="link">set a password</a>.</p>

            <p>There is nothing else to configure. Once a password is set, every save from that point on is encrypted automatically, and the file is decrypted only in memory while you have it open.</p>

            <h2>How It Works</h2>
            <p>When you save, Argo Books encrypts your data before writing it to disk. When you open a password-protected company file, the data is decrypted in memory so you can work with it normally. The copy on disk stays encrypted the whole time.</p>
            <ul>
                <li><strong>AES-256-GCM:</strong> Advanced Encryption Standard with 256-bit keys and Galois/Counter Mode, providing both confidentiality and data integrity. If a file has been tampered with, decryption fails rather than handing you altered data.</li>
                <li><strong>PBKDF2 key derivation:</strong> your encryption key is rebuilt from your password each time using PBKDF2-SHA256 with 600,000 iterations and a unique random salt.</li>
                <li><strong>The key is never stored:</strong> Argo Books doesn't keep your encryption key anywhere, not in the file and not on your computer. It's derived from your password when needed and wiped from memory immediately after.</li>
                <li><strong>Local only:</strong> encryption and decryption happen entirely on your device. Your company file, password, and key never leave your computer and are never sent to us.</li>
            </ul>

            <h2>What Gets Encrypted</h2>
            <p>Argo Books doesn't pick and choose which fields to protect. Your whole company file is packed into a single archive and that entire archive is encrypted in one piece, so everything inside it is covered:</p>
            <ul>
                <li>Financial transactions (expenses, revenue, payments)</li>
                <li>Customer and supplier information</li>
                <li>Product and inventory data</li>
                <li>Invoices and purchase orders</li>
                <li>Attached receipts and documents</li>
                <li>Your settings, reports, and everything else stored in the file</li>
            </ul>

            <h2>Who can decrypt your file</h2>
            <p>Your password is the everyday way in, and it's the only one that works on your own computer. Nobody can work it out from the file: it isn't stored, it's used to build the encryption key and then discarded.</p>

            <p>There is one other way in. From version 2.0.11, each encrypted file also stores its key wrapped under an Argo Books recovery key, so that forgetting a password doesn't have to mean losing your books. That key is held offline by us and is useless without your file, so it only ever comes into play if you send us your file and ask us to unlock it. Full details, including what we check before unlocking, are on the <a class="link" href="password.php">Password Protection</a> page.</p>

            <div class="page-navigation">
                <a href="../reference/keyboard_shortcuts.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Keyboard Shortcuts</span>
                </a>
                <a href="password.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Password Protection &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
