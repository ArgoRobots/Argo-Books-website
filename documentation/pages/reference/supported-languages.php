<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Supported Languages';
$pageDescription = 'View the list of 54 supported languages in Argo Books and learn how to change your application language.';
$currentPage = 'supported-languages';
$pageCategory = 'reference';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Argo Books supports 54 languages, allowing you to use the application in your preferred language. The installer is currently only available in English, but you can change the application language after installation.</p>

            <h2>Changing Your Language</h2>
            <ol class="steps-list">
                <li>Open Argo Books and go to "Settings"</li>
                <li>Select the "General" tab</li>
                <li>Find the "Language" dropdown menu</li>
                <li>Select your preferred language from the list</li>
            </ol>
            <p>The interface will update immediately after selecting a new language.</p>

            <h2>Available Languages</h2>
            <p>The following languages are supported:</p>
            <div style="column-count: 3; column-gap: 2rem; margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.2rem;">
                    <li>Arabic</li>
                    <li>Bengali</li>
                    <li>Bulgarian</li>
                    <li>Catalan</li>
                    <li>Chinese (Simplified)</li>
                    <li>Chinese (Traditional)</li>
                    <li>Croatian</li>
                    <li>Czech</li>
                    <li>Danish</li>
                    <li>Dutch</li>
                    <li>English</li>
                    <li>Estonian</li>
                    <li>Filipino</li>
                    <li>Finnish</li>
                    <li>French</li>
                    <li>German</li>
                    <li>Greek</li>
                    <li>Gujarati</li>
                    <li>Hebrew</li>
                    <li>Hindi</li>
                    <li>Hungarian</li>
                    <li>Indonesian</li>
                    <li>Italian</li>
                    <li>Japanese</li>
                    <li>Kannada</li>
                    <li>Korean</li>
                    <li>Latvian</li>
                    <li>Lithuanian</li>
                    <li>Malay</li>
                    <li>Malayalam</li>
                    <li>Marathi</li>
                    <li>Norwegian</li>
                    <li>Persian</li>
                    <li>Polish</li>
                    <li>Portuguese</li>
                    <li>Punjabi</li>
                    <li>Romanian</li>
                    <li>Russian</li>
                    <li>Serbian</li>
                    <li>Slovak</li>
                    <li>Slovenian</li>
                    <li>Spanish</li>
                    <li>Swahili</li>
                    <li>Swedish</li>
                    <li>Tamil</li>
                    <li>Telugu</li>
                    <li>Thai</li>
                    <li>Turkish</li>
                    <li>Ukrainian</li>
                    <li>Urdu</li>
                    <li>Vietnamese</li>
                </ul>
            </div>

            <p><a class="link" href="supported_languages.php">View complete list with native language names</a></p>

            <div class="info-box">
                <strong>Note:</strong> Translations are downloaded and cached automatically when you select a language. An internet connection is required the first time you switch to a new language.
            </div>

            <div class="page-navigation">
                <a href="supported-currencies.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Supported Currencies
                </a>
                <a href="keyboard_shortcuts.php" class="nav-button next">
                    Next: Keyboard Shortcuts
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
