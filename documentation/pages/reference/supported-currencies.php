<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Supported Currencies';
$pageDescription = 'View the list of 28 supported currencies in Argo Books for import, export, and real-time conversion.';
$currentPage = 'supported-currencies';
$pageCategory = 'reference';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Argo Books supports 28 international currencies including USD, EUR, GBP, CAD, JPY, CNY, and others. The system uses real-time exchange rates to convert between currencies accurately for import, export, and display.</p>

            <p><a class="link" href="/supported_currencies.php">View complete list of all 28 supported currencies</a></p>

            <div class="warning-box">
                <strong>Internet Connection Required:</strong> Currency conversion requires an internet connection to fetch current and historical exchange rates. The rates are cached locally to minimize future requests.
            </div>

            <div class="page-navigation">
                <a href="accepted-countries.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Accepted Countries
                </a>
                <a href="supported-languages.php" class="nav-button next">
                    Next: Supported Languages
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
