<?php
$pageTitle = 'Rental Management';
$pageDescription = 'Learn how to manage equipment rentals, track availability, handle bookings, and process returns with Argo Books rental features.';
$currentPage = 'rental';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Manage equipment rentals, track availability, and handle bookings with ease. Perfect for rental businesses of any size.</p>

            <h2>Setting Up Rental Items</h2>
            <ol class="steps-list">
                <li>Create a product and mark it as a rental item</li>
                <li>Set the rental price (daily, weekly, or custom period)</li>
                <li>Define the rental deposit amount (optional)</li>
                <li>Add any rental terms or conditions</li>
            </ol>

            <h2>Availability Calendar</h2>
            <p>View and manage item availability at a glance:</p>
            <ul>
                <li><strong>Visual Calendar:</strong> See which items are booked and when</li>
                <li><strong>Multiple Views:</strong> Switch between day, week, and month views</li>
                <li><strong>Color Coding:</strong> Quickly identify booked, available, and maintenance periods</li>
                <li><strong>Conflict Prevention:</strong> System prevents double-booking automatically</li>
            </ul>

            <h2>Creating a Booking</h2>
            <ol class="steps-list">
                <li>Select the item to rent from your product list</li>
                <li>Choose the start and end dates on the calendar</li>
                <li>Select or create a customer profile</li>
                <li>Review pricing (automatically calculated based on duration)</li>
                <li>Collect deposit if required</li>
                <li>Confirm the booking</li>
            </ol>

            <div class="info-box">
                <p><strong>Tip:</strong> Link bookings to customer profiles to track rental history and preferences for each customer.</p>
            </div>

            <h2>Booking Status</h2>
            <p>Track each booking through its lifecycle:</p>
            <ul>
                <li><strong>Reserved:</strong> Booking confirmed, awaiting pickup</li>
                <li><strong>Active:</strong> Item has been picked up and is currently rented</li>
                <li><strong>Returned:</strong> Item returned and booking completed</li>
                <li><strong>Overdue:</strong> Return date has passed</li>
                <li><strong>Cancelled:</strong> Booking was cancelled</li>
            </ul>

            <h2>Processing Returns</h2>
            <ol class="steps-list">
                <li>Find the booking in your active rentals list</li>
                <li>Inspect the item for any damage</li>
                <li>Record the return condition</li>
                <li>Calculate any additional charges (late fees, damage)</li>
                <li>Process the deposit refund or apply to charges</li>
                <li>Mark as returned</li>
            </ol>

            <h2>Late Fees and Damage Charges</h2>
            <p>Handle additional charges when items are returned late or damaged:</p>
            <ul>
                <li><strong>Late Fee Calculation:</strong> Automatic calculation based on days overdue</li>
                <li><strong>Damage Assessment:</strong> Document damage with notes and photos</li>
                <li><strong>Deposit Application:</strong> Apply deposit to cover charges</li>
                <li><strong>Additional Billing:</strong> Generate invoice for amounts exceeding deposit</li>
            </ul>

            <h2>Maintenance Periods</h2>
            <p>Block out time for equipment maintenance:</p>
            <ul>
                <li>Schedule regular maintenance windows</li>
                <li>Mark items as unavailable during service</li>
                <li>Track maintenance history for each item</li>
            </ul>

            <h2>Rental Reports</h2>
            <p>Analyze your rental business performance:</p>
            <ul>
                <li><strong>Utilization Rate:</strong> How often each item is rented</li>
                <li><strong>Revenue by Item:</strong> Which items generate the most income</li>
                <li><strong>Customer Analysis:</strong> Frequent renters and rental patterns</li>
                <li><strong>Upcoming Returns:</strong> Items due back soon</li>
            </ul>

            <div class="page-navigation">
                <a href="inventory.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Inventory Management
                </a>
                <a href="payments.php" class="nav-button next">
                    Next: Payment System
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
