<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Supported Languages';
$pageDescription = 'View the list of 54 supported languages in Argo Books and learn how to change your application language.';
$currentPage = 'supported-languages';
$pageCategory = 'reference';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Choose from 54 languages including English, Spanish, French, German, Chinese, Arabic, and many others. The installer is currently only available in English, but you can change the application language in "Settings > General" after installation.</p>

            <h2>Changing Your Language</h2>
            <ol class="steps-list">
                <li>Go to "Settings > General" in the application</li>
                <li>Find the "Language" dropdown menu</li>
                <li>Select your preferred language from the list</li>
            </ol>

            <p><a class="link" href="supported_languages.php">View complete list of all 54 supported languages</a></p>

            <div class="page-navigation">
                <a href="supported-currencies.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Supported Currencies
                </a>
                <a href="../security/encryption.php" class="nav-button next">
                    Next: Encryption
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
