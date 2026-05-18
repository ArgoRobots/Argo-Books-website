<?php
/**
 * Follow-ups tab partial.
 *
 * Renders the static structure of the Follow-ups tab — sub-view pills,
 * filters, empty bulk actions bar, and the table tbody that outreach.js
 * populates via `get_followups` API calls.
 */

function followups_tab_render($pdo)
{
    ?>
    <div class="panel">
        <div class="panel-header">
            <h2>Follow-ups</h2>
            <div class="panel-actions">
                <div class="section-tabs" style="display:inline-flex; gap:0; border-radius:6px; overflow:hidden;">
                    <button type="button" class="section-tab active" data-fu-view="pending_review" onclick="switchFollowupsView(this, 'pending_review')">Pending review <span class="fu-count" id="fuCountPending">0</span></button>
                    <button type="button" class="section-tab" data-fu-view="approved" onclick="switchFollowupsView(this, 'approved')">Approved & queued</button>
                    <button type="button" class="section-tab" data-fu-view="upcoming" onclick="switchFollowupsView(this, 'upcoming')">Upcoming</button>
                    <button type="button" class="section-tab" data-fu-view="sent" onclick="switchFollowupsView(this, 'sent')">Sent</button>
                    <button type="button" class="section-tab" data-fu-view="halted" onclick="switchFollowupsView(this, 'halted')">Halted / failed</button>
                </div>
            </div>
        </div>

        <div class="bulk-actions-bar" id="fuBulkActionsBar" style="display:none;">
            <span><strong id="fuSelectedCount">0</strong> selected</span>
            <button class="btn btn-small btn-blue" onclick="bulkApproveFollowups()">Approve selected</button>
            <button class="btn btn-small btn-blue" onclick="bulkSkipFollowups()">Skip selected</button>
            <button class="btn btn-small btn-red" onclick="bulkHaltFollowupSequences()">Halt sequences for selected leads</button>
        </div>

        <div id="followupsContainer">
            <p class="empty-state">Loading follow-ups...</p>
        </div>
    </div>
    <?php
}
