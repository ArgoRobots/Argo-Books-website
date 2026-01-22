<?php
$pageTitle = 'Quick Actions';
$pageDescription = 'Learn how to use Quick Actions in Argo Books to quickly navigate, create records, and access features with keyboard shortcuts.';
$currentPage = 'advanced-search';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Quick Actions is a command palette that lets you quickly navigate the app, create new records, and access features without using menus. Press <strong>Ctrl+K</strong> to open it anytime.</p>

            <h2>Opening Quick Actions</h2>
            <ul>
                <li>Press <strong>Ctrl+K</strong> on your keyboard</li>
                <li>Or click the search icon in the header</li>
            </ul>

            <h2>How It Works</h2>
            <p>Start typing to search through available actions. The search uses fuzzy matching, so you don't need to type exact names:</p>
            <ul>
                <li>Type <code>inv</code> to find "Create Invoice" or "View Invoices"</li>
                <li>Type <code>cust</code> to find customer-related actions</li>
                <li>Type <code>settings</code> to access application settings</li>
            </ul>

            <p>Results are organized into categories:</p>
            <ul>
                <li><strong>Top Results:</strong> Best matches for your search</li>
                <li><strong>Quick Actions:</strong> Create new records (invoices, expenses, customers, etc.)</li>
                <li><strong>Navigation:</strong> Go to different pages in the app</li>
                <li><strong>Tools & Settings:</strong> Access settings and configuration options</li>
            </ul>

            <h2>Common Actions</h2>
            <div class="info-box">
                <h3>Creating Records</h3>
                <ul>
                    <li>Create new expense</li>
                    <li>Create new invoice</li>
                    <li>Add customer</li>
                    <li>Add product</li>
                </ul>

                <h3>Navigation</h3>
                <ul>
                    <li>Go to Dashboard</li>
                    <li>Go to Analytics</li>
                    <li>View Inventory</li>
                    <li>Open Settings</li>
                </ul>
            </div>

            <h2>Page Search</h2>
            <p>Each page in the app has its own search box for filtering records on that specific page. Use the search box at the top of any list view to filter by:</p>
            <ul>
                <li>Names and descriptions</li>
                <li>IDs and reference numbers</li>
                <li>Other relevant fields for that data type</li>
            </ul>

            <div class="page-navigation">
                <a href="report-generator.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Report Generator
                </a>
                <a href="customers.php" class="nav-button next">
                    Next: Customer Management
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
