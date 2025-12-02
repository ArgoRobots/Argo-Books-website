<?php
$pageTitle = 'Supported Languages';
$pageDescription = 'View the list of 54 supported languages in Argo Books and learn how to change your application language.';
$currentPage = 'supported-languages';

include '../../docs-header.php';
$pageCategory = 'reference';
include '../../sidebar.php';
?>

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <h1>Supported Languages</h1>
                <p>Choose from 54 languages including English, Spanish, French, German, Chinese, Arabic, and many
                    others. The installer is currently only available in English, but you can change the application
                    language in "Settings > General" after installation.</p>

                <h2>Changing Your Language</h2>
                <ol class="steps-list">
                    <li>Go to "Settings > General" in the application</li>
                    <li>Find the "Language" dropdown menu</li>
                    <li>Select your preferred language from the list</li>
                </ol>

                <p><a class="link" href="references/supported_languages.php">View complete list of all 54 supported
                        languages</a></p>

                <div class="page-navigation">
                    <a href="supported-currencies.php" class="nav-button prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                        Previous: Supported Currencies
                    </a>
                    <a href="../security/encryption.php" class="nav-button next">
                        Next: Encryption
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
            </section>
        </main>

<?php include '../../docs-footer.php'; ?>
