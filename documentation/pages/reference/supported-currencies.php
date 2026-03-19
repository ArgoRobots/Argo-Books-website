<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Supported Currencies';
$pageDescription = 'View the list of 28 supported currencies in Argo Books for import, export, and real-time conversion.';
$currentPage = 'supported-currencies';
$pageCategory = 'reference';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Argo Books supports 28 international currencies with real-time exchange rate conversion. You can set your company's default currency when creating a company, and the system will handle conversions automatically when importing, exporting, or displaying data in other currencies.</p>

            <h2>Supported Currencies</h2>
            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>USD</td><td>United States Dollar</td></tr>
                        <tr><td>EUR</td><td>Euro</td></tr>
                        <tr><td>GBP</td><td>British Pound Sterling</td></tr>
                        <tr><td>CAD</td><td>Canadian Dollar</td></tr>
                        <tr><td>AUD</td><td>Australian Dollar</td></tr>
                        <tr><td>JPY</td><td>Japanese Yen</td></tr>
                        <tr><td>CNY</td><td>Chinese Yuan</td></tr>
                        <tr><td>INR</td><td>Indian Rupee</td></tr>
                        <tr><td>BRL</td><td>Brazilian Real</td></tr>
                        <tr><td>MXN</td><td>Mexican Peso</td></tr>
                        <tr><td>CHF</td><td>Swiss Franc</td></tr>
                        <tr><td>SEK</td><td>Swedish Krona</td></tr>
                        <tr><td>NOK</td><td>Norwegian Krone</td></tr>
                        <tr><td>DKK</td><td>Danish Krone</td></tr>
                        <tr><td>NZD</td><td>New Zealand Dollar</td></tr>
                        <tr><td>SGD</td><td>Singapore Dollar</td></tr>
                        <tr><td>HKD</td><td>Hong Kong Dollar</td></tr>
                        <tr><td>KRW</td><td>South Korean Won</td></tr>
                        <tr><td>ZAR</td><td>South African Rand</td></tr>
                        <tr><td>TRY</td><td>Turkish Lira</td></tr>
                        <tr><td>RUB</td><td>Russian Ruble</td></tr>
                        <tr><td>PLN</td><td>Polish Zloty</td></tr>
                        <tr><td>THB</td><td>Thai Baht</td></tr>
                        <tr><td>IDR</td><td>Indonesian Rupiah</td></tr>
                        <tr><td>MYR</td><td>Malaysian Ringgit</td></tr>
                        <tr><td>PHP</td><td>Philippine Peso</td></tr>
                        <tr><td>CZK</td><td>Czech Koruna</td></tr>
                        <tr><td>ILS</td><td>Israeli New Shekel</td></tr>
                    </tbody>
                </table>
            </div>

            <h2>How Currency Conversion Works</h2>
            <ul>
                <li><strong>Real-time rates:</strong> Exchange rates are fetched automatically when needed</li>
                <li><strong>Historical rates:</strong> When importing or exporting data, the system uses the exchange rate from the transaction date for accurate conversion</li>
                <li><strong>Local caching:</strong> Rates are cached on your device to reduce internet requests and allow limited offline use</li>
            </ul>

            <div class="warning-box">
                <strong>Internet Connection Required:</strong> Currency conversion requires an internet connection to fetch current and historical exchange rates. Cached rates are used when offline, but may not reflect the latest values.
            </div>

            <div class="page-navigation">
                <a href="../features/spreadsheet-export.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Spreadsheet Export</span>
                </a>
                <a href="supported-languages.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Supported Languages &rarr;</span>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
