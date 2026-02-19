<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Accepted Countries';
$pageDescription = 'View the list of accepted country names and variants for importing data into Argo Books.';
$currentPage = 'accepted-countries';
$pageCategory = 'reference';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>When importing spreadsheet data, country names must match the system's accepted country list or use recognized variants. The system accepts standard country names, ISO codes, and common alternative names, so you don't have to worry about exact formatting.</p>

            <h2>How Country Matching Works</h2>
            <p>Argo Books recognizes multiple formats for each country. You can use any of the following:</p>
            <ul>
                <li><strong>Full name:</strong> "United States", "United Kingdom", "Germany"</li>
                <li><strong>ISO 2-letter code:</strong> "US", "GB", "DE"</li>
                <li><strong>ISO 3-letter code:</strong> "USA", "GBR", "DEU"</li>
                <li><strong>Common alternatives:</strong> "America", "England", "Deutschland"</li>
            </ul>

            <h2>Common Examples</h2>
            <p>Here are popular countries with their accepted variants:</p>
            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>Accepted Variants</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>United States</td><td>US, USA, U.S., U.S.A., America, United States of America</td></tr>
                        <tr><td>United Kingdom</td><td>UK, U.K., GB, GBR, Great Britain, Britain, England</td></tr>
                        <tr><td>Canada</td><td>CA, CAN</td></tr>
                        <tr><td>Australia</td><td>AU, AUS</td></tr>
                        <tr><td>Germany</td><td>DE, DEU, Deutschland</td></tr>
                        <tr><td>France</td><td>FR, FRA</td></tr>
                        <tr><td>Japan</td><td>JP, JPN</td></tr>
                        <tr><td>China</td><td>CN, CHN</td></tr>
                        <tr><td>India</td><td>IN, IND</td></tr>
                        <tr><td>Brazil</td><td>BR, BRA</td></tr>
                        <tr><td>Mexico</td><td>MX, MEX</td></tr>
                        <tr><td>South Korea</td><td>KR, KOR, Korea</td></tr>
                    </tbody>
                </table>
            </div>

            <p><a class="link" href="accepted_countries.php">View the complete list of all accepted country names and variants</a></p>

            <div class="info-box">
                <strong>Tip:</strong> Country matching is case-insensitive. "united states", "UNITED STATES", and "United States" are all recognized.
            </div>

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
