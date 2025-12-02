<?php
$pageTitle = 'Accepted Countries';
$pageDescription = 'View the list of accepted country names and variants for importing data into Argo Books.';
$currentPage = 'accepted-countries';

include '../../docs-header.php';
$pageCategory = 'reference';
include '../../sidebar.php';
?>

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <h1>Accepted Countries</h1>
                <p>When importing spreadsheet data, country names must match the system's accepted country list or use
                    recognized variants. The system accepts standard country names, ISO codes, and common alternative
                    names.</p>

                <h2>Common Examples</h2>
                <p>Popular countries with their accepted variants:</p>
                <ul>
                    <li><strong>United States:</strong> US, USA, U.S., America</li>
                    <li><strong>United Kingdom:</strong> UK, U.K., Great Britain, Britain, England</li>
                    <li><strong>Germany:</strong> DE, Deutschland</li>
                </ul>
                <br>
                <p><a class="link" href="references/accepted_countries.php">View complete list of all accepted country
                        names and variants</a></p>

                <div class="page-navigation">
                    <a href="../features/advanced-search.php" class="nav-button prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                        Previous: Advanced Search
                    </a>
                    <a href="supported-currencies.php" class="nav-button next">
                        Next: Supported Currencies
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
            </section>
        </main>

<?php include '../../docs-footer.php'; ?>
