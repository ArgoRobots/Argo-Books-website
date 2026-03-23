<?php
session_start();
require_once '../../db_connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Set page variables for the header
$page_title = "Business Outreach";
$page_description = "Find local businesses, generate outreach emails, and track leads";

// Include the admin header
include '../admin_header.php';
?>

<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../../resources/styles/checkbox.css">

<!-- Dashboard Stats -->
<div class="stats-row" id="statsRow">
    <div class="stat-card">
        <div class="stat-label">Total Leads</div>
        <div class="stat-value" id="statTotal">0</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">New</div>
        <div class="stat-value stat-new" id="statNew">0</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Drafts Pending</div>
        <div class="stat-value stat-pending" id="statDraftsPending">0</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Contacted</div>
        <div class="stat-value stat-contacted" id="statContacted">0</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Replied</div>
        <div class="stat-value stat-replied" id="statReplied">0</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Interested</div>
        <div class="stat-value stat-interested" id="statInterested">0</div>
    </div>
</div>

<!-- Business Discovery Panel -->
<div class="panel discovery-panel">
    <div class="panel-header" onclick="togglePanel('discoveryContent')">
        <h2><?= svg_icon('search', 18) ?> Business Discovery</h2>
        <span class="panel-toggle" id="discoveryToggle">&#9660;</span>
    </div>
    <div class="panel-content" id="discoveryContent">
        <div class="discovery-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="discCity">City <span class="required">*</span></label>
                    <input type="text" id="discCity" placeholder="e.g. Saskatoon" required>
                </div>
                <div class="form-group">
                    <label for="discProvince">Province / State</label>
                    <input type="text" id="discProvince" placeholder="e.g. Saskatchewan">
                </div>
                <div class="form-group">
                    <label for="discCategory">Category / Industry</label>
                    <input type="text" id="discCategory" placeholder="e.g. landscaping, cleaners">
                </div>
                <div class="form-group">
                    <label for="discCompanySize">Company Size</label>
                    <select id="discCompanySize" onchange="renderDiscoveryResults()">
                        <option value="">All Sizes</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="discLimit">Limit</label>
                    <select id="discLimit">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="form-group form-group-btn">
                    <button class="btn btn-blue" onclick="searchBusinesses()" id="searchBtn">Search</button>
                </div>
            </div>
        </div>

        <div id="discoveryResults" style="display:none;">
            <div class="discovery-actions">
                <span id="discoveryCount">0 results</span>
                <div>
                    <button class="btn btn-small btn-blue" onclick="selectAllDiscovery()">Select All</button>
                    <button class="btn btn-small btn-blue" onclick="deselectAllDiscovery()">Deselect All</button>
                    <button class="btn btn-small btn-blue" onclick="importSelected()">Import Selected</button>
                    <button class="btn btn-small btn-blue" onclick="importAll()">Import All</button>
                </div>
            </div>
            <div class="discovery-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><div class="checkbox"><input type="checkbox" id="discSelectAll" onchange="toggleDiscoveryCheckboxes(this)"><label for="discSelectAll"></label></div></th>
                            <th>Business Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Website</th>
                            <th>Address</th>
                            <th>Category</th>
                            <th>Size</th>
                        </tr>
                    </thead>
                    <tbody id="discoveryTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Leads Management -->
<div class="panel">
    <div class="panel-header">
        <h2>Leads</h2>
        <div class="panel-actions">
            <button class="btn btn-small btn-blue" onclick="showAddLeadModal()">+ Add Lead</button>
            <button class="btn btn-small btn-blue" onclick="showImportCSVModal()">Import CSV</button>
            <button class="btn btn-small btn-blue" onclick="window.open('api.php?action=export_csv', '_blank')">Export CSV</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-container">
        <div class="filters-row">
            <div class="filter-group">
                <label for="filterSearch">Search</label>
                <input type="text" id="filterSearch" placeholder="Name, email, city..." oninput="debounceLoadLeads()">
            </div>
            <div class="filter-group">
                <label for="filterStatus">Status</label>
                <select id="filterStatus" onchange="loadLeads()">
                    <option value="">All</option>
                    <option value="new">New</option>
                    <option value="ready_to_contact">Ready to Contact</option>
                    <option value="draft_generated">Draft Generated</option>
                    <option value="contacted">Contacted</option>
                    <option value="replied">Replied</option>
                    <option value="interested">Interested</option>
                    <option value="not_interested">Not Interested</option>
                    <option value="onboarded">Onboarded</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterResponse">Response</label>
                <select id="filterResponse" onchange="loadLeads()">
                    <option value="">All</option>
                    <option value="no_response">No Response</option>
                    <option value="positive">Positive</option>
                    <option value="neutral">Neutral</option>
                    <option value="negative">Negative</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterCompanySize">Company Size</label>
                <select id="filterCompanySize" onchange="loadLeads()">
                    <option value="">All Sizes</option>
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterSort">Sort</label>
                <select id="filterSort" onchange="loadLeads()">
                    <option value="date_added_desc">Newest First</option>
                    <option value="date_added_asc">Oldest First</option>

                    <option value="last_contact_desc">Last Contacted</option>
                    <option value="business_name_asc">Name A-Z</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions-bar" id="bulkActionsBar" style="display:none;">
        <span><strong id="selectedCount">0</strong> selected</span>
        <button class="btn btn-small btn-blue" id="btnDraftSelected" onclick="bulkGenerateDrafts()">Draft Selected</button>
        <button class="btn btn-small btn-blue" onclick="openBulkSendModal()">Send Email</button>
        <button class="btn btn-small btn-blue" onclick="bulkDeleteLeads()">Delete Selected</button>
    </div>

    <!-- Bulk Draft Progress -->
    <div class="bulk-draft-progress" id="bulkDraftProgress" style="display:none;">
        <span class="bulk-draft-spinner"></span>
        <span id="bulkDraftProgressText"></span>
        <button class="btn btn-small" id="btnCancelDraft" onclick="cancelBulkDrafts()" style="margin-left:8px;">Cancel</button>
    </div>

    <!-- Leads Table -->
    <div class="leads-table-wrapper">
        <table class="data-table leads-table">
            <thead>
                <tr>
                    <th class="checkbox-column"><div class="checkbox"><input type="checkbox" id="leadsSelectAll" onchange="toggleLeadCheckboxes(this)"><label for="leadsSelectAll"></label></div></th>
                    <th>Business</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="leadsTableBody">
                <tr><td colspan="9" class="empty-state">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Lead Detail Modal -->
<div id="leadDetailModal" class="modal" style="display:none;">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3 id="detailModalTitle">Lead Details</h3>
            <button class="modal-close" onclick="closeModal('leadDetailModal')">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" onclick="switchTab('tabInfo', this)">Info</button>
                <button class="tab" onclick="switchTab('tabDraft', this)">Email Draft</button>
                <button class="tab" onclick="switchTab('tabActivity', this)">Activity</button>
            </div>

            <!-- Info Tab -->
            <div id="tabInfo" class="tab-content active">
                <div class="detail-grid">
                    <div class="form-group">
                        <label>Business Name</label>
                        <input type="text" id="detailBusinessName">
                    </div>
                    <div class="form-group">
                        <label>Contact Name</label>
                        <input type="text" id="detailContactName">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="detailEmail">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" id="detailPhone">
                    </div>
                    <div class="form-group">
                        <label>Website</label>
                        <div class="input-with-btn">
                            <input type="url" id="detailWebsite">
                            <button class="btn btn-small btn-blue" onclick="openWebsite()" title="Open in new tab">Open</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" id="detailAddress">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" id="detailCategory">
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" id="detailCity">
                    </div>
                    <div class="form-group">
                        <label>Source</label>
                        <input type="text" id="detailSource" readonly>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="detailStatus">
                            <option value="new">New</option>
                            <option value="ready_to_contact">Ready to Contact</option>
                            <option value="draft_generated">Draft Generated</option>
                            <option value="contacted">Contacted</option>
                            <option value="replied">Replied</option>
                            <option value="interested">Interested</option>
                            <option value="not_interested">Not Interested</option>
                            <option value="onboarded">Onboarded</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Response Status</label>
                        <select id="detailResponseStatus">
                            <option value="no_response">No Response</option>
                            <option value="positive">Positive</option>
                            <option value="neutral">Neutral</option>
                            <option value="negative">Negative</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Offer Sent</label>
                        <select id="detailOfferSent">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Company Size</label>
                        <select id="detailCompanySize">
                            <option value="">Unknown</option>
                            <option value="small">Small</option>
                            <option value="medium">Medium</option>
                            <option value="large">Large</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Contact Page URL</label>
                        <input type="url" id="detailContactPageUrl">
                    </div>
                </div>
                <div class="form-group full-width">
                    <label>Notes</label>
                    <textarea id="detailNotes" rows="4" placeholder="Add notes about this lead..."></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Feedback Summary</label>
                    <textarea id="detailFeedback" rows="3" placeholder="Summarize feedback received..."></textarea>
                </div>
                <div class="detail-actions">
                    <button class="btn btn-red" onclick="deleteCurrentLead()">Delete Lead</button>
                    <button class="btn btn-blue" onclick="saveLeadDetails()">Save Changes</button>
                </div>
            </div>

            <!-- Draft Tab -->
            <div id="tabDraft" class="tab-content">
                <div class="draft-section">
                    <div class="draft-status-bar" id="draftStatusBar"></div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" id="draftSubject" placeholder="Email subject...">
                    </div>
                    <div class="form-group">
                        <label>Message Body</label>
                        <textarea id="draftBody" rows="12" placeholder="Email body..."></textarea>
                    </div>
                    <div class="draft-actions">
                        <button class="btn btn-blue" onclick="generateDraft()" id="btnGenerate">Generate Draft</button>
                        <button class="btn btn-blue" onclick="sendEmail()" id="btnSend" disabled>Send Email</button>
                        <button class="btn btn-blue btn-small draft-copy-btn" onclick="copyDraft(this)">Copy</button>
                    </div>
                    <div class="draft-info" id="draftInfo"></div>
                </div>
            </div>

            <!-- Activity Tab -->
            <div id="tabActivity" class="tab-content">
                <div id="activityTimeline" class="activity-timeline">
                    <p class="empty-state-text">Loading activity...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Lead Modal -->
<div id="addLeadModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3>Add New Lead</h3>
            <button class="modal-close" onclick="closeModal('addLeadModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-grid">
                <div class="form-group">
                    <label>Business Name <span class="required">*</span></label>
                    <input type="text" id="addBusinessName" required>
                </div>
                <div class="form-group">
                    <label>Contact Name</label>
                    <input type="text" id="addContactName">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="addEmail">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" id="addPhone">
                </div>
                <div class="form-group">
                    <label>Website</label>
                    <input type="url" id="addWebsite">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" id="addAddress">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" id="addCategory">
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" id="addCity">
                </div>
            </div>
            <div class="form-group full-width">
                <label>Notes</label>
                <textarea id="addNotes" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-blue" onclick="closeModal('addLeadModal')">Cancel</button>
            <button class="btn btn-blue" onclick="createLead()">Add Lead</button>
        </div>
    </div>
</div>

<!-- CSV Import Modal -->
<div id="csvImportModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Import Leads from CSV</h3>
            <button class="modal-close" onclick="closeModal('csvImportModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Upload a CSV file with lead data. The file should have headers matching: Business Name, Contact Name, Email, Phone, Website, Address, Category, City, Notes.</p>
            <div class="form-group">
                <label for="csvFile">CSV File</label>
                <input type="file" id="csvFile" accept=".csv">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-blue" onclick="closeModal('csvImportModal')">Cancel</button>
            <button class="btn btn-blue" onclick="importCSV()">Import</button>
        </div>
    </div>
</div>

<!-- Bulk Send Email Modal -->
<div id="bulkSendModal" class="modal" style="display:none;">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>Send Emails</h3>
            <button class="modal-close" onclick="closeBulkSendModal()">&times;</button>
        </div>
        <div class="modal-body" style="padding:0;">
            <div id="bulkSendStatus" class="bulk-send-status"></div>
            <div id="bulkSendList" class="bulk-send-list"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-blue" onclick="closeBulkSendModal()">Cancel</button>
            <button class="btn btn-blue" id="btnBulkSend" disabled>Send All</button>
        </div>
    </div>
</div>

<script src="outreach.js"></script>

        </main>
    </div>
</body>

</html>
