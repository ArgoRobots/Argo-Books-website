<?php
require_once __DIR__ . '/../admin_session.php';
require_once __DIR__ . '/../../db_connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Tab partials (load render + POST handlers)
require_once __DIR__ . '/tabs/ab-tests.php';
require_once __DIR__ . '/tabs/settings.php';
require_once __DIR__ . '/tabs/followups.php';
require_once __DIR__ . '/tabs/reddit-threads.php';
require_once __DIR__ . '/tabs/reddit-settings.php';

// Dispatch POST submissions from tab-specific forms BEFORE any output so
// redirects via header() still work. CSRF: every state-changing tab form
// must include the session csrf_token; reject anything that doesn't match.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tab'])) {
    $postedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (!$sessionToken || !$postedToken || !hash_equals($sessionToken, $postedToken)) {
        $_SESSION['message'] = 'Session expired or invalid request token. Please try again.';
        $_SESSION['message_type'] = 'error';
        header('Location: index.php?tab=' . urlencode($_POST['tab'])); exit;
    }
    $postTab = $_POST['tab'];
    if ($postTab === 'ab-tests') {
        ab_tests_tab_handle_post($pdo);
    } elseif ($postTab === 'settings') {
        settings_tab_handle_post($pdo);
    } elseif ($postTab === 'reddit-settings') {
        reddit_settings_tab_handle_post($pdo);
    }
}

// Determine active tab from ?tab=
$activeTab = $_GET['tab'] ?? 'leads';
$allowedTabs = ['discovery', 'leads', 'ab-tests', 'followups', 'settings', 'reddit-threads', 'reddit-settings'];
if (!in_array($activeTab, $allowedTabs, true)) {
    $activeTab = 'leads';
}

// Set page variables for the header
$page_title = "Business Outreach";
$page_description = "Find local businesses, generate outreach emails, and track leads";

// Include the admin header
include __DIR__ . '/../admin_header.php';
?>

<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../../resources/styles/checkbox.css">

<?php
// Determine which channel is active. Reddit sub-tabs map to the reddit pane;
// everything else (including legacy URLs without a tab param) maps to email.
$redditTabs = ['reddit-threads', 'reddit-settings'];
$activeChannel = $_GET['channel'] ?? (in_array($activeTab, $redditTabs, true) ? 'reddit' : 'email');
if (!in_array($activeChannel, ['email', 'reddit', 'editorial', 'creator'], true)) {
    $activeChannel = 'email';
}
?>

<!-- Channel-level tabs (Email | Reddit) -->
<div class="channel-tabs">
    <button class="channel-tab <?php echo $activeChannel === 'email' ? 'active' : ''; ?>" data-channel="email">Email</button>
    <button class="channel-tab <?php echo $activeChannel === 'reddit' ? 'active' : ''; ?>" data-channel="reddit">Reddit</button>
    <button class="channel-tab <?php echo $activeChannel === 'editorial' ? 'active' : ''; ?>" data-channel="editorial">Editorial Partners</button>
    <button class="channel-tab <?php echo $activeChannel === 'creator' ? 'active' : ''; ?>" data-channel="creator">Creator Partners</button>
</div>

<!-- Email channel -->
<div class="channel-pane <?php echo $activeChannel === 'email' ? 'active' : ''; ?>" data-channel-pane="email">

<!-- Page-level tabs -->
<div class="section-tabs">
    <button class="section-tab <?php echo $activeTab === 'discovery' ? 'active' : ''; ?>" data-tab="discovery">Discovery</button>
    <button class="section-tab <?php echo $activeTab === 'leads' ? 'active' : ''; ?>" data-tab="leads">Leads</button>
    <button class="section-tab <?php echo $activeTab === 'followups' ? 'active' : ''; ?>" data-tab="followups">Follow-ups</button>
    <button class="section-tab <?php echo $activeTab === 'ab-tests' ? 'active' : ''; ?>" data-tab="ab-tests">A/B Tests</button>
    <button class="section-tab <?php echo $activeTab === 'settings' ? 'active' : ''; ?>" data-tab="settings">Settings</button>
</div>

<div id="discovery" class="tab-content <?php echo $activeTab === 'discovery' ? 'active' : ''; ?>">

<!-- Google Places Discovery Panel -->
<div class="panel discovery-panel">
    <div class="panel-header" onclick="togglePanel('discoveryContent')">
        <h2><?= svg_icon('search', 18) ?> Google Places</h2>
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
                <table class="data-table discovery-table">
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

<!-- Shopify Discovery Panel -->
<div class="panel discovery-panel">
    <div class="panel-header" onclick="togglePanel('shopifyContent')">
        <h2><?= svg_icon('search', 18) ?> Shopify</h2>
        <span class="panel-toggle" id="shopifyToggle">&#9660;</span>
    </div>
    <div class="panel-content" id="shopifyContent">
        <div class="discovery-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="shopifyLimit">How many startups to find</label>
                    <input type="number" id="shopifyLimit" value="10" min="1" max="50">
                </div>
                <div class="form-group form-group-btn">
                    <button class="btn btn-blue" onclick="runShopifyDiscovery()" id="shopifyRunBtn">Run</button>
                </div>
            </div>
            <p class="text-muted" style="margin:8px 0 0; font-size:13px; text-align:center;">
                The system picks Canadian-startup queries automatically and keeps searching until it finds enough or your daily SerpAPI quota runs out. Usage today: <span id="serpapiUsage">…</span>.
            </p>
            <div style="text-align:center; margin-top:10px;">
                <button class="btn btn-small btn-blue" onclick="showModal('shopifyRejectStatsModal')">
                    Why candidates get rejected (last 30 days)
                </button>
            </div>
        </div>

        <div id="shopifyResults" style="display:none; margin-top:16px;">
            <div class="discovery-actions">
                <span id="shopifyResultsCount">0 results</span>
                <div>
                    <button class="btn btn-small btn-blue" onclick="importAllShopifyFits()" id="shopifyImportAllBtn">Import All Fits</button>
                </div>
            </div>
            <div class="discovery-table-wrapper">
                <table class="data-table discovery-table" data-paginate="25">
                    <thead>
                        <tr>
                            <th>Store</th>
                            <th>Email</th>
                            <th>Products</th>
                            <th>Oldest Product</th>
                            <th>Country</th>
                            <th>Result</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="shopifyResultsBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Shopify reject-reason distribution (last 30 days). Helps tune which
// filters are killing leads so the discovery cron can be adjusted.
$shopifyRejectStats = [];
$shopifyImported30d = 0;
$shopifyTotal30d = 0;
try {
    $stmt = $pdo->prepare(
        "SELECT reject_reason, COUNT(*) AS cnt, MAX(reject_detail) AS sample
         FROM outreach_shopify_candidates
         WHERE status = 'rejected'
           AND checked_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
           AND reject_reason IS NOT NULL
         GROUP BY reject_reason
         ORDER BY cnt DESC"
    );
    $stmt->execute();
    $shopifyRejectStats = $stmt->fetchAll();

    $stmt2 = $pdo->prepare(
        "SELECT
             SUM(status='imported') AS imported,
             COUNT(*) AS total
         FROM outreach_shopify_candidates
         WHERE checked_at > DATE_SUB(NOW(), INTERVAL 30 DAY)"
    );
    $stmt2->execute();
    $totals = $stmt2->fetch();
    $shopifyImported30d = (int) ($totals['imported'] ?? 0);
    $shopifyTotal30d    = (int) ($totals['total'] ?? 0);
} catch (Throwable $e) {
    // Show empty state on query failure
}
$shopifyRejectedTotal = max(0, $shopifyTotal30d - $shopifyImported30d);
?>

</div> <!-- /#discovery -->

<div id="leads" class="tab-content <?php echo $activeTab === 'leads' ? 'active' : ''; ?>">

<!-- Pipeline Running Banner -->
<div id="pipelineBanner" style="display:none; background:#fff3cd; color:#856404; border:1px solid #ffc107; border-radius:6px; padding:12px 16px; margin-bottom:16px; font-weight:500;">
    The outreach cron pipeline is currently running. Sending, drafting, and discovery are temporarily disabled to prevent conflicts.
</div>

<!-- Dashboard Stats -->
<div class="stats-grid" id="statsRow">
    <div class="stat-card">
        <h3>Total Leads</h3>
        <div class="stat-value" id="statTotal">0</div>
    </div>
    <div class="stat-card">
        <h3>New</h3>
        <div class="stat-value stat-new" id="statNew">0</div>
    </div>
    <div class="stat-card">
        <h3>Drafts Pending</h3>
        <div class="stat-value stat-pending" id="statDraftsPending">0</div>
    </div>
    <div class="stat-card">
        <h3>Contacted</h3>
        <div class="stat-value stat-contacted" id="statContacted">0</div>
    </div>
    <div class="stat-card">
        <h3>Replied</h3>
        <div class="stat-value stat-replied" id="statReplied">0</div>
    </div>
    <div class="stat-card">
        <h3>Interested</h3>
        <div class="stat-value stat-interested" id="statInterested">0</div>
    </div>
    <div class="stat-card">
        <h3>Clicked</h3>
        <div class="stat-value stat-clicked" id="statClicked">0</div>
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
    <div class="control-bar">
            <div class="control-group">
                <span class="control-label">Search</span>
                <input type="text" class="control-input" id="filterSearch" placeholder="Name, email, city..." oninput="debounceLoadLeads()">
            </div>
            <div class="control-group">
                <span class="control-label">Status</span>
                <select class="control-select" id="filterStatus" onchange="loadLeads()">
                    <option value="">All</option>
                    <option value="new">New</option>
                    <option value="draft_generated">Draft Generated</option>
                    <option value="contacted">Contacted</option>
                    <option value="replied">Replied</option>
                    <option value="interested">Interested</option>
                    <option value="not_interested">Not Interested</option>
                    <option value="onboarded">Onboarded</option>
                    <option value="disqualified">Disqualified</option>
                </select>
            </div>
            <div class="control-group">
                <span class="control-label">Response</span>
                <select class="control-select" id="filterResponse" onchange="loadLeads()">
                    <option value="">All</option>
                    <option value="no_response">No Response</option>
                    <option value="positive">Positive</option>
                    <option value="neutral">Neutral</option>
                    <option value="negative">Negative</option>
                </select>
            </div>
            <div class="control-group">
                <span class="control-label">Company Size</span>
                <select class="control-select" id="filterCompanySize" onchange="loadLeads()">
                    <option value="">All Sizes</option>
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                </select>
            </div>
            <div class="control-group">
                <span class="control-label">Source</span>
                <select class="control-select" id="filterSource" onchange="loadLeads()">
                    <option value="">All</option>
                    <option value="google_places">Google Places</option>
                    <option value="shopify">Shopify</option>
                    <option value="manual">Manual</option>
                    <option value="csv_import">CSV</option>
                </select>
            </div>
            <div class="control-group">
                <span class="control-label">Sort</span>
                <select class="control-select" id="filterSort" onchange="loadLeads()">
                    <option value="date_added_desc">Newest First</option>
                    <option value="date_added_asc">Oldest First</option>

                    <option value="last_contact_desc">Last Contacted</option>
                    <option value="business_name_asc">Name A-Z</option>
                </select>
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
        <button class="btn btn-small btn-neutral" id="btnCancelDraft" onclick="cancelBulkDrafts()" style="margin-left:8px;">Cancel</button>
    </div>

    <!-- Leads Table -->
    <div class="leads-table-wrapper">
        <table class="data-table leads-table">
            <thead>
                <tr>
                    <th class="checkbox-column"><div class="checkbox"><input type="checkbox" id="leadsSelectAll" onchange="toggleLeadCheckboxes(this)"><label for="leadsSelectAll"></label></div></th>
                    <th>Business</th>
                    <th>Website</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Category</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Sent</th>
                    <th>Clicked</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="leadsTableBody">
                <tr><td colspan="12" class="empty-state">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

</div> <!-- /#leads -->

<div id="followups" class="tab-content <?php echo $activeTab === 'followups' ? 'active' : ''; ?>">
    <?php followups_tab_render($pdo); ?>
</div>

<div id="ab-tests" class="tab-content <?php echo $activeTab === 'ab-tests' ? 'active' : ''; ?>">
    <?php ab_tests_tab_render($pdo, (int) ($_GET['test_id'] ?? 0)); ?>
</div>

<div id="settings" class="tab-content <?php echo $activeTab === 'settings' ? 'active' : ''; ?>">
    <?php settings_tab_render($pdo); ?>
</div>

</div> <!-- /.channel-pane[data-channel-pane="email"] -->

<!-- Reddit channel -->
<?php
// If the URL points to the Reddit channel without a Reddit sub-tab, default to threads.
if ($activeChannel === 'reddit' && !in_array($activeTab, ['reddit-threads', 'reddit-settings'], true)) {
    $activeTab = 'reddit-threads';
}
?>
<div class="channel-pane <?php echo $activeChannel === 'reddit' ? 'active' : ''; ?>" data-channel-pane="reddit">

    <!-- Reddit sub-tabs -->
    <div class="section-tabs">
        <button class="section-tab <?php echo $activeTab === 'reddit-threads' ? 'active' : ''; ?>" data-tab="reddit-threads">Threads</button>
        <button class="section-tab <?php echo $activeTab === 'reddit-settings' ? 'active' : ''; ?>" data-tab="reddit-settings">Settings</button>
    </div>

    <div id="reddit-threads" class="tab-content <?php echo $activeTab === 'reddit-threads' ? 'active' : ''; ?>">
        <?php reddit_threads_tab_render($pdo); ?>
    </div>

    <div id="reddit-settings" class="tab-content <?php echo $activeTab === 'reddit-settings' ? 'active' : ''; ?>">
        <?php reddit_settings_tab_render($pdo); ?>
    </div>
</div> <!-- /.channel-pane[data-channel-pane="reddit"] -->

<!-- Editorial channel -->
<div class="channel-pane <?php echo $activeChannel === 'editorial' ? 'active' : ''; ?>" data-channel-pane="editorial">

    <div class="section-tabs">
        <button class="section-tab active" data-tab="editorial-discovery">Discovery</button>
        <button class="section-tab" data-tab="editorial-leads">Leads</button>
    </div>

    <div id="editorial-discovery" class="tab-content active">
        <div class="panel discovery-panel">
            <div class="panel-header" onclick="togglePanel('editorialContent')">
                <h2><?= svg_icon('search', 18) ?> Editorial Partners (roundups)</h2>
                <span class="panel-toggle" id="editorialToggle">&#9660;</span>
            </div>
            <div class="panel-content" id="editorialContent">
                <div class="discovery-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editorialLimit">How many articles to find</label>
                            <input type="number" id="editorialLimit" value="8" min="1" max="30">
                        </div>
                        <div class="form-group form-group-btn">
                            <button class="btn btn-blue" onclick="runEditorialDiscovery()" id="editorialRunBtn">Run</button>
                        </div>
                    </div>
                    <p class="text-muted" style="margin:8px 0 0; font-size:13px; text-align:center;">
                        Searches "best free accounting software" and "QuickBooks alternatives" listicles, finds the author (Hunter.io, else the outlet's contact page), and surfaces the ones that don't already list Argo. SerpAPI usage today: <span id="editorialSerpUsage">&hellip;</span> &middot; Hunter.io: <span id="editorialHunterState">&hellip;</span>.
                    </p>

                    <div class="form-row" style="margin-top:16px; padding-top:16px; border-top:1px solid var(--border-color, #e2e8f0);">
                        <div class="form-group" style="flex:1;">
                            <label for="editorialUrl">Or add a specific article URL you already found</label>
                            <input type="text" id="editorialUrl" placeholder="https://example.com/best-quickbooks-alternatives" style="width:100%;">
                        </div>
                        <div class="form-group form-group-btn">
                            <button class="btn btn-blue" onclick="addEditorialUrl()" id="editorialAddBtn">Add</button>
                        </div>
                    </div>
                    <p class="text-muted" style="margin:8px 0 0; font-size:12px; text-align:center;">
                        Reads the page, scrapes a contact email, and researches which tools it lists, then adds it to your Leads. If no email is found, add it on the lead and generate the pitch.
                    </p>
                </div>

                <div id="editorialResults" style="display:none; margin-top:16px;">
                    <div class="discovery-actions">
                        <span id="editorialResultsCount">0 results</span>
                        <div>
                            <button class="btn btn-small btn-blue" onclick="importAllEditorialFits()" id="editorialImportAllBtn">Import All Fits</button>
                        </div>
                    </div>
                    <div class="discovery-table-wrapper">
                        <table class="data-table discovery-table" data-paginate="25">
                            <thead>
                                <tr>
                                    <th>Outlet</th>
                                    <th>Author</th>
                                    <th>Email</th>
                                    <th>Already lists</th>
                                    <th>Article</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="editorialResultsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-muted" style="margin-top:12px; font-size:13px;">
            Imported articles become leads in the <strong>Leads</strong> tab above, where you review the AI-drafted pitch and send it. Keep Send mode on Review-before-send.
        </p>
    </div>

    <div id="editorial-leads" class="tab-content">
        <!-- Filters -->
        <div class="control-bar">
            <div class="control-group">
                <span class="control-label">Search</span>
                <input type="text" class="control-input" id="edFilterSearch" placeholder="Outlet, author, email..." oninput="debounceLoadEditorialLeads()">
            </div>
            <div class="control-group">
                <span class="control-label">Status</span>
                <select class="control-select" id="edFilterStatus" onchange="loadEditorialLeads()">
                    <option value="">All</option>
                    <option value="new">New</option>
                    <option value="draft_generated">Draft Generated</option>
                    <option value="contacted">Contacted</option>
                    <option value="replied">Replied</option>
                    <option value="interested">Interested</option>
                    <option value="not_interested">Not Interested</option>
                    <option value="onboarded">Onboarded</option>
                    <option value="disqualified">Disqualified</option>
                </select>
            </div>
            <div class="control-group">
                <span class="control-label">Response</span>
                <select class="control-select" id="edFilterResponse" onchange="loadEditorialLeads()">
                    <option value="">All</option>
                    <option value="no_response">No Response</option>
                    <option value="positive">Positive</option>
                    <option value="neutral">Neutral</option>
                    <option value="negative">Negative</option>
                </select>
            </div>
            <div class="control-group">
                <span class="control-label">Sort</span>
                <select class="control-select" id="edFilterSort" onchange="loadEditorialLeads()">
                    <option value="date_added_desc">Newest First</option>
                    <option value="date_added_asc">Oldest First</option>
                    <option value="last_contact_desc">Last Contacted</option>
                    <option value="business_name_asc">Name A-Z</option>
                </select>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions-bar" id="edBulkActionsBar" style="display:none;">
            <span><strong id="edSelectedCount">0</strong> selected</span>
            <button class="btn btn-small btn-blue" id="edBtnDraftSelected" onclick="bulkGenerateEditorialDrafts()">Draft Selected</button>
            <button class="btn btn-small btn-blue" onclick="openEditorialBulkSend()">Send Email</button>
            <button class="btn btn-small btn-blue" onclick="bulkDeleteEditorialLeads()">Delete Selected</button>
        </div>

        <!-- Bulk Draft Progress -->
        <div class="bulk-draft-progress" id="edBulkDraftProgress" style="display:none;">
            <span class="bulk-draft-spinner"></span>
            <span id="edBulkDraftProgressText"></span>
            <button class="btn btn-small btn-neutral" id="edBtnCancelDraft" onclick="cancelBulkDrafts()" style="margin-left:8px;">Cancel</button>
        </div>

        <div class="leads-table-wrapper">
            <table class="data-table editorial-leads-table">
                <thead>
                    <tr>
                        <th class="checkbox-column"><div class="checkbox"><input type="checkbox" id="edLeadsSelectAll" onchange="toggleEditorialLeadCheckboxes(this)"><label for="edLeadsSelectAll"></label></div></th>
                        <th>Outlet</th>
                        <th>Article</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Sent</th>
                        <th>Clicked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="editorialLeadsTableBody"></tbody>
            </table>
        </div>
    </div>
</div> <!-- /.channel-pane[data-channel-pane="editorial"] -->

<div class="channel-pane <?php echo $activeChannel === 'creator' ? 'active' : ''; ?>" data-channel-pane="creator">

    <div class="section-tabs">
        <button class="section-tab active" data-tab="creator-discovery">Discovery</button>
        <button class="section-tab" data-tab="creator-leads">Leads</button>
    </div>

    <div id="creator-discovery" class="tab-content active">
        <div class="panel discovery-panel">
            <div class="panel-header" onclick="togglePanel('creatorContent')">
                <h2><?= svg_icon('search', 18) ?> Creator Partners (YouTube &amp; newsletters)</h2>
                <span class="panel-toggle" id="creatorToggle">&#9660;</span>
            </div>
            <div class="panel-content" id="creatorContent">
                <div class="discovery-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="creatorLimit">How many creators to find</label>
                            <input type="number" id="creatorLimit" value="8" min="1" max="30">
                        </div>
                        <div class="form-group form-group-btn">
                            <button class="btn btn-blue" onclick="runCreatorDiscovery()" id="creatorRunBtn">Run</button>
                        </div>
                    </div>
                    <p class="text-muted" style="margin:8px 0 0; font-size:13px; text-align:center;">
                        Finds YouTubers and newsletter writers whose audience is small businesses and freelancers, then drafts an affiliate-partner pitch (50% recurring). Blogs and roundup articles live in the Editorial channel, not here. Emails are harvested from linked sites where possible; YouTube emails are captcha-gated, so those come in blank, use Get email on the lead to grab them. SerpAPI usage today: <span id="creatorSerpUsage">&hellip;</span> &middot; Hunter.io: <span id="creatorHunterState">&hellip;</span>.
                    </p>

                    <div class="form-row" style="margin-top:16px; padding-top:16px; border-top:1px solid var(--border-color, #e2e8f0);">
                        <div class="form-group" style="flex:1;">
                            <label for="creatorUrl">Or add a specific creator URL (YouTube channel, newsletter, or LinkedIn profile)</label>
                            <input type="text" id="creatorUrl" placeholder="https://youtube.com/@somechannel" style="width:100%;">
                        </div>
                        <div class="form-group form-group-btn">
                            <button class="btn btn-blue" onclick="addCreatorUrl()" id="creatorAddBtn">Add</button>
                        </div>
                    </div>
                    <p class="text-muted" style="margin:8px 0 0; font-size:12px; text-align:center;">
                        Researches the creator and scrapes a contact email if one is public, then adds them to your Leads. LinkedIn profiles are added as a manual list (no email, no auto-draft).
                    </p>
                </div>

                <div id="creatorResults" style="display:none; margin-top:16px;">
                    <div class="discovery-actions">
                        <span id="creatorResultsCount">0 results</span>
                        <div>
                            <button class="btn btn-small btn-blue" onclick="importAllCreatorFits()" id="creatorImportAllBtn">Import All Fits</button>
                        </div>
                    </div>
                    <div class="discovery-table-wrapper">
                        <table class="data-table discovery-table" data-paginate="25">
                            <thead>
                                <tr>
                                    <th>Creator</th>
                                    <th>Platform</th>
                                    <th>Audience</th>
                                    <th>Email</th>
                                    <th>Profile</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="creatorResultsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-muted" style="margin-top:12px; font-size:13px;">
            Imported creators become leads in the <strong>Leads</strong> tab above, where you review the AI-drafted affiliate pitch and send it. Keep Send mode on Review-before-send.
        </p>
    </div>

    <div id="creator-leads" class="tab-content">
        <!-- Filters -->
        <div class="control-bar">
            <div class="control-group">
                <span class="control-label">Search</span>
                <input type="text" class="control-input" id="crFilterSearch" placeholder="Creator, email..." oninput="debounceLoadCreatorLeads()">
            </div>
            <div class="control-group">
                <span class="control-label">Status</span>
                <select class="control-select" id="crFilterStatus" onchange="loadCreatorLeads()">
                    <option value="">All</option>
                    <option value="new">New</option>
                    <option value="draft_generated">Draft Generated</option>
                    <option value="contacted">Contacted</option>
                    <option value="replied">Replied</option>
                    <option value="interested">Interested</option>
                    <option value="not_interested">Not Interested</option>
                    <option value="onboarded">Onboarded</option>
                    <option value="disqualified">Disqualified</option>
                </select>
            </div>
            <div class="control-group">
                <span class="control-label">Response</span>
                <select class="control-select" id="crFilterResponse" onchange="loadCreatorLeads()">
                    <option value="">All</option>
                    <option value="no_response">No Response</option>
                    <option value="positive">Positive</option>
                    <option value="neutral">Neutral</option>
                    <option value="negative">Negative</option>
                </select>
            </div>
            <div class="control-group">
                <span class="control-label">Sort</span>
                <select class="control-select" id="crFilterSort" onchange="loadCreatorLeads()">
                    <option value="date_added_desc">Newest First</option>
                    <option value="date_added_asc">Oldest First</option>
                    <option value="last_contact_desc">Last Contacted</option>
                    <option value="business_name_asc">Name A-Z</option>
                </select>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions-bar" id="crBulkActionsBar" style="display:none;">
            <span><strong id="crSelectedCount">0</strong> selected</span>
            <button class="btn btn-small btn-blue" id="crBtnDraftSelected" onclick="bulkGenerateCreatorDrafts()">Draft Selected</button>
            <button class="btn btn-small btn-blue" onclick="openCreatorBulkSend()">Send Email</button>
            <button class="btn btn-small btn-blue" onclick="bulkDeleteCreatorLeads()">Delete Selected</button>
        </div>

        <!-- Bulk Draft Progress -->
        <div class="bulk-draft-progress" id="crBulkDraftProgress" style="display:none;">
            <span class="bulk-draft-spinner"></span>
            <span id="crBulkDraftProgressText"></span>
            <button class="btn btn-small btn-neutral" id="crBtnCancelDraft" onclick="cancelBulkDrafts()" style="margin-left:8px;">Cancel</button>
        </div>

        <div class="leads-table-wrapper">
            <table class="data-table creator-leads-table">
                <thead>
                    <tr>
                        <th class="checkbox-column"><div class="checkbox"><input type="checkbox" id="crLeadsSelectAll" onchange="toggleCreatorLeadCheckboxes(this)"><label for="crLeadsSelectAll"></label></div></th>
                        <th>Creator</th>
                        <th>Platform</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Sent</th>
                        <th>Clicked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="creatorLeadsTableBody"></tbody>
            </table>
        </div>
    </div>
</div> <!-- /.channel-pane[data-channel-pane="creator"] -->

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
                <button class="tab" onclick="switchTab('tabFollowups', this); loadLeadFollowups();">Follow-ups</button>
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
                            <option value="draft_generated">Draft Generated</option>
                            <option value="contacted">Contacted</option>
                            <option value="replied">Replied</option>
                            <option value="interested">Interested</option>
                            <option value="not_interested">Not Interested</option>
                            <option value="onboarded">Onboarded</option>
                            <option value="disqualified">Disqualified</option>
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
                        <button class="btn btn-blue" onclick="saveDraft()" id="btnSaveDraft">Save Draft</button>
                        <button class="btn btn-blue" onclick="sendEmail()" id="btnSend" disabled>Send Email</button>
                        <button class="btn btn-blue btn-small draft-copy-btn" onclick="copyDraft(this)">Copy</button>
                    </div>
                    <div class="draft-info" id="draftInfo"></div>
                    <div class="form-group" style="margin-top:16px;">
                        <label>Follow-up</label>
                        <div id="followupStatus" class="text-muted" style="margin-top:4px; font-size:13px;">—</div>
                    </div>
                </div>
            </div>

            <!-- Activity Tab -->
            <div id="tabActivity" class="tab-content">
                <div id="activityTimeline" class="activity-timeline">
                    <p class="empty-state-text">Loading activity...</p>
                </div>
            </div>

            <!-- Follow-ups Tab -->
            <div id="tabFollowups" class="tab-content">
                <div id="leadFollowupsList">
                    <p class="empty-state-text">Loading follow-ups...</p>
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
            <p class="text-muted" style="margin-top:0; font-size:13px;">
                Just paste the business's website. We'll fetch the page and auto-fill the name, email, phone, category, city, and a short summary. You can edit anything afterward by opening the lead.
            </p>
            <div class="form-group full-width">
                <label>Website <span class="required">*</span></label>
                <input type="url" id="addWebsite" placeholder="example.com">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-blue" onclick="closeModal('addLeadModal')">Cancel</button>
            <button class="btn btn-blue" id="btnAddLead" onclick="createLead()">Add Lead</button>
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

<!-- Reddit Thread Detail Modal -->
<div id="redditThreadModal" class="modal" style="display:none;">
    <div class="modal-content modal-large" style="height:auto; max-height:85vh;">
        <div class="modal-header">
            <h3 id="redditThreadTitle">Thread</h3>
            <button class="modal-close" onclick="closeModal('redditThreadModal')">&times;</button>
        </div>
        <div class="modal-body reddit-thread-modal-body">
            <div class="reddit-thread-meta">
                <span><strong>Subreddit:</strong> <span id="redditThreadSubreddit"></span></span>
                <span><strong>AI relevance:</strong> <span id="redditThreadAi"></span></span>
                <span><strong>Status:</strong> <span id="redditThreadStatus"></span></span>
                <span><strong>Posted:</strong> <span id="redditThreadPosted"></span></span>
                <a id="redditThreadUrl" target="_blank" rel="noopener noreferrer">Open on Reddit ↗</a>
            </div>
            <div class="reddit-thread-section">
                <h4>OP body</h4>
                <pre id="redditThreadBody" class="reddit-thread-body"></pre>
            </div>
            <div class="reddit-thread-section">
                <h4>Why this thread scored that way</h4>
                <p id="redditThreadReason" class="text-muted"></p>
            </div>
            <div class="reddit-thread-section">
                <h4>Draft</h4>
                <textarea id="redditDraftBody" rows="10" placeholder="Draft will appear here..."></textarea>
                <div class="reddit-draft-actions">
                    <button class="btn btn-small btn-blue" onclick="generatePendingRedditDraft()" id="redditDraftGenerateBtn" style="display:none;">Generate draft</button>
                    <button class="btn btn-small btn-blue" onclick="saveRedditDraft()">Save draft</button>
                    <button class="btn btn-small btn-neutral" onclick="openRedditRegenerateFeedback()">Regenerate</button>
                    <button class="btn btn-small btn-blue" onclick="copyRedditDraft(this)">Copy</button>
                </div>
                <div id="redditRegenerateFeedback" class="reddit-regenerate-feedback" style="display:none;">
                    <label for="redditRegenerateFeedbackText">What should be different about the next draft? (optional)</label>
                    <textarea id="redditRegenerateFeedbackText" rows="3" placeholder="e.g. too formal, don't mention QuickBooks, lead with the side-hustle angle, drop the disclosure phrasing..."></textarea>
                    <div class="reddit-regenerate-feedback-actions">
                        <button class="btn btn-small btn-neutral" onclick="cancelRedditRegenerate()">Cancel</button>
                        <button class="btn btn-small btn-blue" onclick="submitRedditRegenerate(this)" id="redditRegenerateSubmitBtn">Regenerate</button>
                    </div>
                </div>
            </div>
            <div class="reddit-thread-section" id="redditReplyStatusSection" style="display:none;">
                <h4>Posted reply status</h4>
                <div id="redditReplyStatusBody" class="text-muted"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-neutral" onclick="markRedditSkipped()">Skip</button>
            <button class="btn btn-blue" onclick="openMarkRedditRepliedModal()">Mark replied…</button>
        </div>
    </div>
</div>

<!-- Mark Replied Modal -->
<div id="redditMarkRepliedModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width:560px; height:auto; max-height:80vh;">
        <div class="modal-header">
            <h3>Mark replied</h3>
            <button class="modal-close" onclick="closeModal('redditMarkRepliedModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p class="text-muted" style="font-size:13px;">After posting your reply on Reddit, paste the comment permalink here so we can track whether it survives auto-removal.</p>
            <div class="form-group">
                <label for="redditReplyPermalink">Reddit comment permalink</label>
                <input type="url" id="redditReplyPermalink" placeholder="https://www.reddit.com/r/.../comments/.../slug/abc123/" required>
                <p class="form-help">Right-click the timestamp on your comment in Reddit and copy the link.</p>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="redditMentionedProduct" checked>
                    Mentioned Argo Books in this reply (counts toward post limit)
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-neutral" onclick="closeModal('redditMarkRepliedModal')">Cancel</button>
            <button class="btn btn-blue" onclick="confirmMarkRedditReplied()" id="redditConfirmMarkRepliedBtn">Confirm</button>
        </div>
    </div>
</div>

<!-- Add Reddit Thread Modal -->
<div id="addRedditThreadModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3>Add Reddit Thread</h3>
            <button class="modal-close" onclick="closeModal('addRedditThreadModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p class="text-muted" style="margin-top:0; font-size:13px;">
                Paste a Reddit post URL. We'll try to fetch the title, subreddit, and body automatically; fill the fields in below if it can't reach Reddit. The thread lands in the queue as drafted-pending so you can generate a reply for it.
            </p>
            <div class="form-group">
                <label>Reddit post URL <span class="required">*</span></label>
                <input type="url" id="addRedditUrl" placeholder="https://www.reddit.com/r/smallbusiness/comments/abc123/...">
            </div>
            <div class="detail-grid">
                <div class="form-group">
                    <label>Subreddit</label>
                    <input type="text" id="addRedditSubreddit" placeholder="e.g. smallbusiness">
                </div>
            </div>
            <div class="form-group full-width">
                <label>Title</label>
                <input type="text" id="addRedditTitle" placeholder="Auto-filled from the URL when possible">
            </div>
            <div class="form-group full-width">
                <label>OP body (optional)</label>
                <textarea id="addRedditBody" rows="5" placeholder="Paste the post body to improve the generated draft"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-blue" onclick="closeModal('addRedditThreadModal')">Cancel</button>
            <button class="btn btn-blue" onclick="addRedditThread()">Add Thread</button>
        </div>
    </div>
</div>

<!-- Creator: paste email modal -->
<div id="creatorEmailModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 480px;">
        <div class="modal-header">
            <h3>Paste the creator's email</h3>
            <button class="modal-close" onclick="closeModal('creatorEmailModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p class="text-muted" style="margin-top:0; font-size:13px;">
                On the channel page that just opened, reveal the email (solve the captcha if asked), then paste it here.
            </p>
            <div class="form-group full-width">
                <label>Email <span class="required">*</span></label>
                <input type="email" id="creatorEmailInput" placeholder="creator@example.com" autocomplete="off"
                       onkeydown="if(event.key==='Enter'){event.preventDefault();saveCreatorEmail();}">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-blue" onclick="closeModal('creatorEmailModal')">Cancel</button>
            <button class="btn btn-blue" onclick="saveCreatorEmail()">Save email</button>
        </div>
    </div>
</div>

<!-- Shopify Rejection Reasons Modal -->
<div id="shopifyRejectStatsModal" class="modal" style="display:none;">
    <div class="modal-content modal-large" style="height:auto; max-height:80vh;">
        <div class="modal-header">
            <h3>Why Shopify candidates get rejected (last 30 days)</h3>
            <button class="modal-close" onclick="closeModal('shopifyRejectStatsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <?php if ($shopifyTotal30d === 0): ?>
                <p class="text-muted" style="margin:8px 0; font-size:13px;">No Shopify candidates evaluated in the last 30 days.</p>
            <?php else: ?>
                <p class="text-muted" style="margin:0 0 12px; font-size:13px;">
                    Of <?= (int) $shopifyTotal30d ?> candidates evaluated, <?= (int) $shopifyImported30d ?> were imported as leads and <?= (int) $shopifyRejectedTotal ?> were rejected. Use this breakdown to tune the dork pool or evaluator thresholds.
                </p>
                <div class="discovery-table-wrapper">
                    <table class="data-table discovery-table">
                        <thead>
                            <tr>
                                <th>Reject reason</th>
                                <th style="width:90px; text-align:right;">Count</th>
                                <th style="width:90px; text-align:right;">% of rejects</th>
                                <th>Sample detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shopifyRejectStats as $row): ?>
                                <?php
                                    $cnt = (int) $row['cnt'];
                                    $pct = $shopifyRejectedTotal > 0 ? round($cnt / $shopifyRejectedTotal * 100) : 0;
                                ?>
                                <tr>
                                    <td><code><?= htmlspecialchars((string) $row['reject_reason']) ?></code></td>
                                    <td style="text-align:right;"><?= $cnt ?></td>
                                    <td style="text-align:right;"><?= $pct ?>%</td>
                                    <td class="text-muted" style="font-size:12px;"><?= htmlspecialchars((string) ($row['sample'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <div class="modal-footer">
            <button class="btn btn-blue" onclick="closeModal('shopifyRejectStatsModal')">Close</button>
        </div>
    </div>
</div>

<script src="outreach.js"></script>

        </main>
    </div>
</body>

</html>
