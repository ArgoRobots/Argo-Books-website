<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Supported Currencies';
$pageDescription = 'View the list of 29 supported currencies in Argo Books for import, export, and real-time conversion.';
$currentPage = 'supported-currencies';
$pageCategory = 'reference';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>Argo Books supports 29 currencies with exchange rate conversion. You can set your company's default currency when creating a company, and the system will handle conversions automatically when importing, exporting, or displaying data in other currencies.</p>

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
                        <tr><td>ALL</td><td>Albanian Lek</td></tr>
                        <tr><td>AUD</td><td>Australian Dollar</td></tr>
                        <tr><td>BAM</td><td>Bosnia-Herzegovina Mark</td></tr>
                        <tr><td>BGN</td><td>Bulgarian Lev</td></tr>
                        <tr><td>BRL</td><td>Brazilian Real</td></tr>
                        <tr><td>BYN</td><td>Belarusian Ruble</td></tr>
                        <tr><td>CAD</td><td>Canadian Dollar</td></tr>
                        <tr><td>CHF</td><td>Swiss Franc</td></tr>
                        <tr><td>CNY</td><td>Chinese Yuan</td></tr>
                        <tr><td>CZK</td><td>Czech Koruna</td></tr>
                        <tr><td>DKK</td><td>Danish Krone</td></tr>
                        <tr><td>EUR</td><td>Euro</td></tr>
                        <tr><td>GBP</td><td>British Pound</td></tr>
                        <tr><td>HUF</td><td>Hungarian Forint</td></tr>
                        <tr><td>INR</td><td>Indian Rupee</td></tr>
                        <tr><td>ISK</td><td>Icelandic Kr&oacute;na</td></tr>
                        <tr><td>JPY</td><td>Japanese Yen</td></tr>
                        <tr><td>KRW</td><td>South Korean Won</td></tr>
                        <tr><td>MKD</td><td>Macedonian Denar</td></tr>
                        <tr><td>NOK</td><td>Norwegian Krone</td></tr>
                        <tr><td>PLN</td><td>Polish Z&#x142;oty</td></tr>
                        <tr><td>RON</td><td>Romanian Leu</td></tr>
                        <tr><td>RSD</td><td>Serbian Dinar</td></tr>
                        <tr><td>RUB</td><td>Russian Ruble</td></tr>
                        <tr><td>SEK</td><td>Swedish Krona</td></tr>
                        <tr><td>TRY</td><td>Turkish Lira</td></tr>
                        <tr><td>TWD</td><td>Taiwan Dollar</td></tr>
                        <tr><td>UAH</td><td>Ukrainian Hryvnia</td></tr>
                        <tr><td>USD</td><td>US Dollar</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="info-box">
                <strong>Tip:</strong> USD, EUR, CAD and AUD are pinned to the top of every currency dropdown in the app, so the ones most people need are always the first four in the list.
            </div>

            <h2>How Currency Conversion Works</h2>
            <ul>
                <li><strong>Historical rates:</strong> every amount is converted using the exchange rate for that transaction's own date, so a conversion never silently changes because rates moved</li>
                <li><strong>Fetched when needed:</strong> rates are retrieved automatically as transactions are entered, imported, or exported</li>
                <li><strong>Local caching:</strong> rates are cached on your device to reduce internet requests and allow limited offline use</li>
            </ul>

            <p>If the rate for a transaction's date isn't available yet, the transaction is saved and marked <strong>Pending</strong> rather than converted at the wrong rate. See <a class="link" href="how-numbers-are-calculated.php#pending-conversion">How Numbers Are Calculated</a> for what that means for your totals.</p>

            <div class="warning-box">
                <strong>Internet Connection Required:</strong> Currency conversion requires an internet connection to fetch current and historical exchange rates. Cached rates are used when offline, but a date that has never been fetched cannot be converted until you reconnect.
            </div>

            <div class="page-navigation">
                <a href="how-numbers-are-calculated.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; How Numbers Are Calculated</span>
                </a>
                <a href="supported-languages.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Supported Languages &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
