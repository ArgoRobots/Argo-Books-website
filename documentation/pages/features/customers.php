<?php
$pageTitle = 'Customer Management';
$pageDescription = 'Learn how to manage customer profiles, track expense history, and build lasting relationships with Argo Books customer management features.';
$currentPage = 'customers';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Build lasting relationships with your customers using Argo Books' customer management features.
            Keep track of customer information, expense history, and preferences to deliver personalized
            service that keeps them coming back.</p>

            <h2>Customer Profiles</h2>
            <p>Create detailed customer profiles that store all the information you need:</p>
            <ul>
                <li><strong>Contact Information:</strong> First name, last name, email, and phone number</li>
                <li><strong>Address:</strong> Street, city, state, ZIP code, and country</li>
                <li><strong>Status:</strong> Track whether customers are Active, Inactive, or Banned</li>
                <li><strong>Notes:</strong> Add personal notes about preferences or special requirements</li>
            </ul>

            <h2>Transaction History</h2>
            <p>View a complete history of all transactions with each customer:</p>
            <ul>
                <li>All past purchases and sales linked to the customer</li>
                <li>Last rental date for rental customers</li>
                <li>Complete transaction records with dates and amounts</li>
            </ul>

            <div class="info-box">
                <p><strong>Tip:</strong> Use transaction history to identify your most valuable customers and
                offer them special discounts or early access to new products.</p>
            </div>

            <h2>Quick Customer Lookup</h2>
            <p>Find customers instantly using the search feature. Search by:</p>
            <ul>
                <li>First or last name</li>
                <li>Email address</li>
                <li>Phone number</li>
                <li>Customer ID</li>
            </ul>

            <h2>Linking Transactions</h2>
            <p>When recording a revenue or expense entry, you can link it to a customer profile:</p>
            <ol class="steps-list">
                <li>Start creating a new transaction</li>
                <li>Click the "Customer" field</li>
                <li>Search for or select an existing customer</li>
                <li>The transaction will be automatically added to their transaction history</li>
            </ol>

            <h2>Customer Statistics</h2>
            <p>Get insights into your customer base with built-in statistics:</p>
            <ul>
                <li><strong>Total Customers:</strong> Track your growing customer base</li>
                <li><strong>Active Customers:</strong> See how many customers are currently active</li>
                <li><strong>Banned Customers:</strong> Track customers who have been banned</li>
            </ul>

            <h2>Exporting Customer Data</h2>
            <p>Export your customer list to Excel or CSV for backup or use in other applications.
            See the <a href="spreadsheet-export.php" class="link">Spreadsheet Export</a> guide for details.</p>

            <div class="page-navigation">
                <a href="advanced-search.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Quick Actions
                </a>
                <a href="invoicing.php" class="nav-button next">
                    Next: Invoicing & Payments
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
