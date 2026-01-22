<?php
$pageTitle = 'Inventory Management';
$pageDescription = 'Learn how to track stock levels, set reorder points, and manage inventory with Argo Books inventory management features.';
$currentPage = 'inventory';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Keep complete control over your inventory with real-time stock tracking, automatic alerts, and intelligent reorder recommendations.</p>

            <h2>Stock Tracking</h2>
            <p>Monitor your inventory levels in real-time:</p>
            <ul>
                <li><strong>Current Stock:</strong> See how many units of each product you have on hand</li>
                <li><strong>Stock History:</strong> Track inventory changes over time</li>
            </ul>

            <h2>Setting Up Inventory</h2>
            <ol class="steps-list">
                <li>Navigate to "Products" in the main menu</li>
                <li>Select a product or create a new one</li>
                <li>Enable inventory tracking in the product settings</li>
                <li>Enter your current stock quantity</li>
                <li>Set your minimum stock level (reorder point)</li>
            </ol>

            <h2>Reorder Points</h2>
            <p>Set stock thresholds to stay on top of your inventory:</p>
            <ul>
                <li><strong>Reorder Point:</strong> Get alerted when stock falls below this quantity</li>
                <li><strong>Overstock Threshold:</strong> Get notified when stock exceeds this level</li>
            </ul>

            <h2>Low Stock Alerts</h2>
            <p>Argo Books automatically monitors your inventory and notifies you when action is needed:</p>
            <ul>
                <li><strong>Dashboard Alerts:</strong> Low stock items appear on your main dashboard</li>
                <li><strong>Notification Center:</strong> View all inventory alerts in one place</li>
                <!-- <li><strong>Mobile Notifications:</strong> Get push notifications on your phone (with mobile app)</li> -->
            </ul>

            <h2>Inventory Adjustments</h2>
            <p>Make manual adjustments when needed:</p>
            <ul>
                <li><strong>Stock Count:</strong> Update quantities after physical inventory counts</li>
                <li><strong>Damage/Loss:</strong> Record items lost to damage, theft, or expiration</li>
                <li><strong>Adjustments:</strong> Correct discrepancies with notes for audit trail</li>
            </ul>

            <h2>Automatic Stock Updates</h2>
            <p>Inventory is automatically adjusted when you:</p>
            <ul>
                <li>Record a revenue transaction (stock decreases)</li>
                <li>Record an expense/purchase transaction (stock increases)</li>
                <li>Process a return (stock adjusts accordingly)</li>
            </ul>

            <h2>Inventory Dashboard</h2>
            <p>Monitor your inventory at a glance with key metrics:</p>
            <ul>
                <li><strong>Total Units:</strong> Total inventory across all products</li>
                <li><strong>In Stock:</strong> Products with healthy stock levels</li>
                <li><strong>Low Stock:</strong> Products below their reorder point</li>
                <li><strong>Out of Stock:</strong> Products with zero inventory</li>
                <li><strong>Overstock:</strong> Products above their overstock threshold</li>
            </ul>

            <div class="page-navigation">
                <a href="predictive-analytics.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Predictive Analytics
                </a>
                <a href="rental.php" class="nav-button next">
                    Next: Rental Management
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
