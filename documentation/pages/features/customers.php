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

            <h2>Linking Transactions</h2>
            <p>When recording a revenue or expense entry, you can link it to a customer profile:</p>
            <ol class="steps-list">
                <li>Start creating a new transaction</li>
                <li>Click the "Customer" field</li>
                <li>Select an existing customer</li>
                <li>The transaction will be automatically added to their transaction history</li>
            </ol>

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
