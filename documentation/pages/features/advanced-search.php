<?php
$pageTitle = 'Advanced Search';
$pageDescription = 'Learn how to use the advanced search features in Argo Books, including search operators and AI-powered search.';
$currentPage = 'advanced-search';

include '../../docs-header.php';
$pageCategory = 'features';
include '../../sidebar.php';
?>

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <h1>Advanced Search Features</h1>
                <p>Argo Books includes a powerful search system with advanced operators to help you find exactly
                    what you need. The search bar works across all your transactions, making it easy to filter and
                    locate specific data.</p>

                <h2>Basic Search</h2>
                <p>Simply type a word or phrase to search across all fields in your transactions:</p>
                <ul class="examples-list">
                    <li><code>shirt</code> - Finds all transactions containing "shirt" in any field</li>
                    <li><code>cotton mills</code> - Finds transactions containing both "cotton" and "mills"</li>
                </ul>

                <p>Basic search tolerates small spelling errors and variations. This helps you find records even if
                    there are minor typos in your data.</p>

                <h2>Search Operators</h2>
                <div class="info-box">
                    <h4>Exact Phrase Matching with Double Quotes (" ")</h4>
                    <p>Double quotes search for an <strong>exact sequence of words in that precise order</strong>:</p>
                    <ul class="examples-list">
                        <li><code>"black t-shirt"</code> - Finds only transactions containing these exact words together
                            in this exact order
                        </li>
                        <li>Will NOT match "t-shirt black" or "black cotton t-shirt"</li>
                    </ul>
                    <br>

                    <h4>Required Terms with Plus Sign (+)</h4>
                    <p>The plus sign marks words that <strong>must be present somewhere</strong> in the transaction, but
                        not necessarily together or in any specific order:</p>
                    <ul class="examples-list">
                        <li><code>+shirt +cotton</code> - Finds transactions that contain both "shirt" AND "cotton"
                            anywhere in the record
                        </li>
                        <li>Would match "cotton shirt," "shirt made of cotton," or even records where "shirt" appears in
                            one field and "cotton" in another</li>
                    </ul>
                    <br>

                    <h4>Exclusion Terms with Minus Sign (-)</h4>
                    <p>Use the minus sign to exclude words from your search:</p>
                    <ul class="examples-list">
                        <li><code>shirt -white</code> - Finds transactions containing "shirt" but NOT "white"</li>
                        <li><code>"t-shirt" -black -white</code> - Finds t-shirts that are neither black nor white</li>
                    </ul>
                </div>

                <h2>AI-Powered Search (Paid Version)</h2>
                <p>The paid version includes AI-powered search capabilities that understand natural language queries.
                </p>
                <ol class="steps-list">
                    <li>Start your search with an exclamation mark (!)</li>
                    <li>Type your query in natural language</li>
                    <li>Press Enter to execute the search</li>
                </ol>

                <div class="info-box">
                    <h4>AI Search Examples</h4>
                    <ul class="examples-list">
                        <li><code>!show me expensive items purchased last month</code>
                        <li><code>!orders from germany for ball bearings over $25</code>
                        <li><code>!sales with shipping costs over $10 in april 2025</code>
                        </li>
                    </ul>
                </div>

                <div class="warning-box">
                    <strong>Internet Connection Required:</strong> AI search requires an internet connection and is only
                    available in the paid version. <a class="link" href="../upgrade/">Upgrade here</a> to
                    access this feature.
                </div>

                <div class="page-navigation">
                    <a href="report-generator.php" class="nav-button prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                        Previous: Report Generator
                    </a>
                    <a href="../reference/accepted-countries.php" class="nav-button next">
                        Next: Accepted Countries
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
            </section>
        </main>

<?php include '../../docs-footer.php'; ?>
