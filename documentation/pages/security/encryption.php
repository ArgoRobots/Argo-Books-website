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

            <h2>How It Works</h2>
            <p>All sensitive data stored in your company file is encrypted automatically. When you save your data, Argo Books encrypts it before writing to disk. When you open a company file, the data is decrypted in memory so you can work with it normally.</p>
            <ul>
                <li><strong>AES-256-GCM:</strong> Advanced Encryption Standard with 256-bit keys and Galois/Counter Mode, providing both confidentiality and data integrity</li>
                <li><strong>PBKDF2 Key Derivation:</strong> Your encryption key is derived using PBKDF2 with a unique salt, making it resistant to brute-force attacks</li>
                <li><strong>Local Only:</strong> Encryption and decryption happen entirely on your device. Your encryption keys never leave your computer</li>
            </ul>

            <h2>What Gets Encrypted</h2>
            <p>Encryption protects all the sensitive business data in your company file, including:</p>
            <ul>
                <li>Financial transactions (expenses, revenue, payments)</li>
                <li>Customer and supplier information</li>
                <li>Product and inventory data</li>
                <li>Invoices and purchase orders</li>
                <li>Attached receipts and documents</li>
            </ul>

            <h2>Always-On Protection</h2>
            <p>Encryption is always enabled and requires no setup or configuration. All company files are automatically encrypted using AES-256-GCM, ensuring your business data is always protected at rest.</p>

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
