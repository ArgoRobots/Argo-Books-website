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
                    <li>Albanian</li>
                    <li>Arabic</li>
                    <li>Basque</li>
                    <li>Belarusian</li>
                    <li>Bengali</li>
                    <li>Bosnian</li>
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
                    <li>Galician</li>
                    <li>German</li>
                    <li>Greek</li>
                    <li>Hebrew</li>
                    <li>Hindi</li>
                    <li>Hungarian</li>
                    <li>Icelandic</li>
                    <li>Indonesian</li>
                    <li>Irish</li>
                    <li>Italian</li>
                    <li>Japanese</li>
                    <li>Korean</li>
                    <li>Latvian</li>
                    <li>Lithuanian</li>
                    <li>Luxembourgish</li>
                    <li>Macedonian</li>
                    <li>Malay</li>
                    <li>Maltese</li>
                    <li>Norwegian</li>
                    <li>Persian</li>
                    <li>Polish</li>
                    <li>Portuguese</li>
                    <li>Romanian</li>
                    <li>Russian</li>
                    <li>Serbian</li>
                    <li>Slovak</li>
                    <li>Slovenian</li>
                    <li>Spanish</li>
                    <li>Swahili</li>
                    <li>Swedish</li>
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
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Supported Currencies</span>
                </a>
                <a href="keyboard_shortcuts.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Keyboard Shortcuts &rarr;</span>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
