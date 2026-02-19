<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Accepted Countries';
$pageDescription = 'View the list of accepted country names and variants for importing data into Argo Books.';
$currentPage = 'accepted-countries';
$pageCategory = 'reference';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>When importing spreadsheet data, country names must match the system's accepted country list or use recognized variants. The system accepts standard country names, ISO codes, and common alternative names.</p>

            <h2>Common Examples</h2>
            <p>Popular countries with their accepted variants:</p>
            <ul>
                <li><strong>United States:</strong> US, USA, U.S., America</li>
                <li><strong>United Kingdom:</strong> UK, U.K., Great Britain, Britain, England</li>
                <li><strong>Germany:</strong> DE, Deutschland</li>
            </ul>

            <p><a class="link" href="accepted_countries.php">View complete list of all accepted country names and variants</a></p>

            <div class="page-navigation">
                <a href="../features/rental.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Rental Management
                </a>
                <a href="supported-currencies.php" class="nav-button next">
                    Next: Supported Currencies
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
