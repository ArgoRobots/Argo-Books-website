<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Encryption';
$pageDescription = 'Learn about the AES-256 encryption used in Argo Books to protect your business data.';
$currentPage = 'encryption';
$pageCategory = 'security';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Argo Books uses AES-256 encryption to protect your business data, the same standard used by banks and military organizations.</p>

            <p>Encryption is automatic and requires no additional setup from users. It's enabled by default, but can be disabled in the settings under the "Security" tab.</p>

            <div class="page-navigation">
                <a href="../reference/supported-languages.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Supported Languages
                </a>
                <a href="password.php" class="nav-button next">
                    Next: Password Protection
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
