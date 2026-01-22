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
                <li>Go to Rental Inventory in the sidebar</li>
                <li>Click "Add Item" to create a new rental item</li>
                <li>Set pricing: daily rate, weekly rate, and monthly rate</li>
                <li>Define the security deposit amount (optional)</li>
                <li>Set the total quantity available</li>
                <li>Link to a supplier if applicable</li>
            </ol>

            <h2>Tracking Availability</h2>
            <p>Monitor your rental inventory status:</p>
            <ul>
                <li><strong>Total Quantity:</strong> How many units you own</li>
                <li><strong>Available:</strong> Units ready to rent</li>
                <li><strong>Rented:</strong> Units currently out with customers</li>
                <li><strong>In Maintenance:</strong> Units temporarily unavailable for service</li>
            </ul>

            <h2>Creating a Rental Record</h2>
            <ol class="steps-list">
                <li>Go to Rental Records in the sidebar</li>
                <li>Click "Add Rental" to create a new record</li>
                <li>Select the customer and rental item</li>
                <li>Enter the quantity and rental dates</li>
                <li>Review the pricing and total</li>
                <li>Save the rental record</li>
            </ol>

            <div class="info-box">
                <p><strong>Tip:</strong> Link rentals to customer profiles to track rental history for each customer.</p>
            </div>

            <h2>Rental Status</h2>
            <p>Track each rental through its lifecycle:</p>
            <ul>
                <li><strong>Active:</strong> Item is currently rented out</li>
                <li><strong>Returned:</strong> Item has been returned and rental is complete</li>
                <li><strong>Overdue:</strong> Return date has passed</li>
                <li><strong>Cancelled:</strong> Rental was cancelled</li>
            </ul>

            <h2>Processing Returns</h2>
            <ol class="steps-list">
                <li>Find the rental in your active rentals list</li>
                <li>Click "Return" to mark the item as returned</li>
                <li>The rental status will update to "Returned"</li>
                <li>Available quantity for the item will increase automatically</li>
            </ol>

            <h2>Maintenance Mode</h2>
            <p>Mark items as unavailable when they need service:</p>
            <ul>
                <li>Set an item's status to "In Maintenance" to remove it from available inventory</li>
                <li>Return it to "Active" status when service is complete</li>
            </ul>

            <h2>Rental Dashboard</h2>
            <p>Monitor your rental operations with key metrics:</p>
            <ul>
                <li><strong>Total Rentals:</strong> All rental records</li>
                <li><strong>Active Rentals:</strong> Currently rented out</li>
                <li><strong>Overdue:</strong> Rentals past their return date</li>
                <li><strong>Revenue:</strong> Total revenue from returned rentals</li>
            </ul>

            <div class="page-navigation">
                <a href="inventory.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Inventory Management
                </a>
                <!-- Payment System nav - TEMPORARILY DISABLED
                <a href="payments.php" class="nav-button next">
                    Next: Payment System
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
                -->
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
