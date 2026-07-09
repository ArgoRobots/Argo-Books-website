// ─── State ───
let currentLeadId = null;
let discoveryResults = [];
let debounceTimer = null;
let leadsPaginator = null;
let discoveryPaginator = null;

// ─── URL Param Helpers ───
function getFilterParams() {
    return new URLSearchParams(window.location.search);
}

function updateUrlParams() {
    // Start from the current URL so non-filter params (tab, test_id, etc.) survive.
    const params = new URLSearchParams(window.location.search);
    const search = document.getElementById('filterSearch').value.trim();
    const status = document.getElementById('filterStatus').value;
    const response = document.getElementById('filterResponse').value;
    const companySize = document.getElementById('filterCompanySize').value;
    const source = document.getElementById('filterSource').value;
    const sort = document.getElementById('filterSort').value;

    const setOrDelete = (key, value) => value ? params.set(key, value) : params.delete(key);
    setOrDelete('search', search);
    setOrDelete('status', status);
    setOrDelete('response', response);
    setOrDelete('company_size', companySize);
    setOrDelete('source', source);
    setOrDelete('sort', sort && sort !== 'date_added_desc' ? sort : '');

    const qs = params.toString();
    const newUrl = qs ? window.location.pathname + '?' + qs : window.location.pathname;
    window.history.replaceState({}, '', newUrl);
}

function restoreFiltersFromUrl() {
    const params = getFilterParams();
    if (params.has('search')) document.getElementById('filterSearch').value = params.get('search');
    if (params.has('status')) document.getElementById('filterStatus').value = params.get('status');
    if (params.has('response')) document.getElementById('filterResponse').value = params.get('response');
    if (params.has('company_size')) document.getElementById('filterCompanySize').value = params.get('company_size');
    if (params.has('source')) document.getElementById('filterSource').value = params.get('source');
    if (params.has('sort')) document.getElementById('filterSort').value = params.get('sort');
}

// ─── Discovery SessionStorage ───
function saveDiscoveryToSession() {
    try {
        sessionStorage.setItem('outreach_discovery', JSON.stringify(discoveryResults));
        // Save discovery form values
        sessionStorage.setItem('outreach_disc_city', document.getElementById('discCity').value);
        sessionStorage.setItem('outreach_disc_province', document.getElementById('discProvince').value);
        sessionStorage.setItem('outreach_disc_category', document.getElementById('discCategory').value);
        sessionStorage.setItem('outreach_disc_limit', document.getElementById('discLimit').value);
        sessionStorage.setItem('outreach_disc_company_size', document.getElementById('discCompanySize').value);
    } catch (e) { /* storage full or unavailable */ }
}

function restoreDiscoveryFromSession() {
    try {
        const saved = sessionStorage.getItem('outreach_discovery');
        if (saved) {
            discoveryResults = JSON.parse(saved);
            if (discoveryResults.length) {
                document.getElementById('discoveryResults').style.display = 'block';
                renderDiscoveryResults();
            }
        }
        // Restore form values
        const city = sessionStorage.getItem('outreach_disc_city');
        const province = sessionStorage.getItem('outreach_disc_province');
        const category = sessionStorage.getItem('outreach_disc_category');
        const limit = sessionStorage.getItem('outreach_disc_limit');
        const companySize = sessionStorage.getItem('outreach_disc_company_size');
        if (city) document.getElementById('discCity').value = city;
        if (province) document.getElementById('discProvince').value = province;
        if (category) document.getElementById('discCategory').value = category;
        if (limit) document.getElementById('discLimit').value = limit;
        if (companySize) document.getElementById('discCompanySize').value = companySize;
    } catch (e) { /* storage unavailable */ }
}

// ─── Init ───
document.addEventListener('DOMContentLoaded', function () {
    restoreFiltersFromUrl();
    restoreDiscoveryFromSession();
    loadStats();
    loadLeads();
});

// ─── API Helper ───
async function api(action, options = {}) {
    const { method = 'GET', body = null, params = {} } = options;
    let url = `api.php?action=${action}`;

    if (method === 'GET' && Object.keys(params).length) {
        const qs = new URLSearchParams(params).toString();
        url += '&' + qs;
    }

    const fetchOptions = { method };
    if (method !== 'GET') {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        if (body instanceof FormData) {
            body.append('csrf_token', csrfToken);
            fetchOptions.body = body;
        } else {
            const payload = Object.assign({}, body || {}, { csrf_token: csrfToken });
            fetchOptions.headers = { 'Content-Type': 'application/json' };
            fetchOptions.body = JSON.stringify(payload);
        }
    }

    const res = await fetch(url, fetchOptions);
    if (action === 'export_csv') return res;

    if (res.status === 401) {
        // Session expired, redirect to login
        window.location.href = '../login.php';
        throw new Error('Session expired. Redirecting to login...');
    }

    if (!res.ok) {
        let msg = `Server error (${res.status})`;
        try { const err = await res.json(); msg = err.message || msg; } catch(e) {}
        throw new Error(msg);
    }

    return await res.json();
}

function notify(message, type) {
    // Non-blocking toast instead of a native alert box. notifications.js (loaded
    // by admin_header.php) watches for .success-message/.error-message nodes and
    // removes them when the fadeInOut animation ends.
    const el = document.createElement('div');
    el.className = type === 'error' ? 'error-message' : 'success-message';
    el.textContent = message;
    document.body.appendChild(el);
}

// ─── Stats ───
async function loadStats() {
    try {
        const data = await api('get_stats');
        if (data.success) {
            const s = data.stats;
            document.getElementById('statTotal').textContent = s.total || 0;
            document.getElementById('statNew').textContent = s.new_leads || 0;
            document.getElementById('statDraftsPending').textContent = s.drafts_pending || 0;
            document.getElementById('statContacted').textContent = s.contacted || 0;
            document.getElementById('statReplied').textContent = s.replied || 0;
            document.getElementById('statInterested').textContent = s.interested || 0;
            document.getElementById('statClicked').textContent = s.clicked || 0;

            // Show/hide pipeline running banner
            const banner = document.getElementById('pipelineBanner');
            if (banner) {
                banner.style.display = data.pipeline_running ? 'block' : 'none';
            }
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

// ─── Leads Table ───
function debounceLoadLeads() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(loadLeads, 300);
}

async function loadLeads() {
    const params = {};
    const search = document.getElementById('filterSearch').value.trim();
    const status = document.getElementById('filterStatus').value;
    const response = document.getElementById('filterResponse').value;
    const companySize = document.getElementById('filterCompanySize').value;
    const source = document.getElementById('filterSource').value;
    const sort = document.getElementById('filterSort').value;

    if (search) params.search = search;
    if (status) params.status = status;
    if (response) params.response_status = response;
    if (companySize) params.company_size = companySize;
    if (source) params.source = source;
    if (sort) params.sort = sort;

    // Persist filters to URL
    updateUrlParams();

    try {
        const data = await api('get_leads', { params });
        const tbody = document.getElementById('leadsTableBody');

        if (!data.success || !data.leads.length) {
            tbody.innerHTML = '<tr><td colspan="12" class="empty-state">No leads found</td></tr>';
            updateBulkBar();
            return;
        }

        tbody.innerHTML = data.leads.map(lead => `
            <tr class="lead-row">
                <td class="checkbox-column" onclick="event.stopPropagation()">
                    <div class="checkbox"><input type="checkbox" class="lead-check" value="${lead.id}" id="lead-check-${lead.id}" data-has-draft="${lead.draft_subject ? '1' : ''}" onchange="updateBulkBar()"><label for="lead-check-${lead.id}"></label></div>
                </td>
                <td>
                    <strong>${esc(lead.business_name)}</strong>
                    ${lead.contact_name ? '<br><small>' + esc(lead.contact_name) + '</small>' : ''}
                </td>
                <td>${lead.website ? '<a class="website-link" href="' + esc(lead.website) + '" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()">' + esc(lead.website.replace(/^https?:\/\//, '').replace(/\?.*$/, '')) + '</a>' : '<span class="text-muted">—</span>'}</td>
                <td>${lead.email ? esc(lead.email) : '<span class="text-muted">—</span>'}</td>
                <td>${lead.phone ? esc(lead.phone) : '<span class="text-muted">—</span>'}</td>
                <td>${esc(lead.city || '')}</td>
                <td>${esc(lead.category || '')}</td>
                <td>${formatSource(lead.source)}</td>
                <td><span class="badge badge-status-${lead.status || 'new'}">${formatStatus(lead.status || 'new')}</span></td>
                <td>${lead.sent_at ? formatDateTime(lead.sent_at) : '<span class="text-muted">—</span>'}</td>
                <td>${lead.clicked_at ? formatDateTime(lead.clicked_at) : '<span class="text-muted">—</span>'}</td>
                <td onclick="event.stopPropagation()">
                    <div class="actions-cell">
                        <button class="btn btn-small btn-blue" onclick="openLeadDetail(${lead.id})" title="View">View</button>
                        ${!lead.draft_subject && !['contacted','replied','interested','not_interested','onboarded'].includes(lead.status) ? `<button class="btn btn-small btn-blue" onclick="quickGenerateDraft(${lead.id}, this)" title="Generate Draft">Draft</button>` : ''}
                    </div>
                </td>
            </tr>
        `).join('');

        // Reset select-all checkbox
        const selectAll = document.getElementById('leadsSelectAll');
        if (selectAll) selectAll.checked = false;
        updateBulkBar();

        // Initialize or reset pagination
        const leadsTable = document.querySelector('.leads-table');
        if (leadsTable) {
            if (!leadsPaginator) {
                leadsPaginator = new TablePaginator(leadsTable, { perPage: 25 });
            } else {
                leadsPaginator.reset();
            }
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

// Refresh whichever leads tables are currently populated. The lead detail modal
// (and quick-draft) can be opened from the Email, Editorial, or Creator tab, so
// after a status change we refresh all of them; the editorial/creator tables only
// reload if they've been opened (their paginator exists), keeping this cheap.
function refreshLeadViews() {
    loadLeads();
    if (typeof editorialLeadsPaginator !== 'undefined' && editorialLeadsPaginator) loadEditorialLeads();
    if (typeof creatorLeadsPaginator !== 'undefined' && creatorLeadsPaginator) loadCreatorLeads();
}

// ─── Bulk Select ───
function toggleLeadCheckboxes(master) {
    document.querySelectorAll('.lead-check').forEach(cb => cb.checked = master.checked);
    updateBulkBar();
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.lead-check:checked');
    const bar = document.getElementById('bulkActionsBar');
    const count = document.getElementById('selectedCount');
    const selectAll = document.getElementById('leadsSelectAll');
    const allBoxes = document.querySelectorAll('.lead-check');

    if (bar) bar.style.display = checked.length ? 'flex' : 'none';
    if (count) count.textContent = checked.length;

    // Update select-all indeterminate state
    if (selectAll && allBoxes.length) {
        if (checked.length === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        } else if (checked.length === allBoxes.length) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        }
    }

    // Switch to "Redraft Selected" if all selected leads already have drafts
    const draftBtn = document.getElementById('btnDraftSelected');
    if (draftBtn) {
        const allDrafted = checked.length > 0 && Array.from(checked).every(cb => cb.dataset.hasDraft === '1');
        draftBtn.textContent = allDrafted ? 'Redraft Selected' : 'Draft Selected';
    }
}

function getSelectedLeadIds() {
    return Array.from(document.querySelectorAll('.lead-check:checked')).map(cb => parseInt(cb.value));
}

let bulkDraftCancelled = false;

function cancelBulkDrafts() {
    bulkDraftCancelled = true;
    // Disable whichever cancel button is currently visible (email or editorial).
    ['btnCancelDraft', 'edBtnCancelDraft'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn && btn.offsetParent !== null) { btn.disabled = true; btn.textContent = 'Cancelling...'; }
    });
}

/**
 * Shared bulk-draft engine used by both the Email and Editorial leads tables.
 * The two tables use distinct checkbox classes/IDs and distinct progress/button
 * element IDs, so the caller passes those in; everything else is id-based and
 * identical. Concurrency of 3, per-row live updates, cancellable.
 */
async function runBulkDrafts(opts) {
    const {
        ids, checkClass, checkIdPrefix,
        progressId, progressTextId, cancelBtnId, draftBtnId, onDone,
    } = opts;
    if (!ids.length) return;
    if (!confirm(`Generate drafts for ${ids.length} lead(s)?`)) return;

    bulkDraftCancelled = false;
    const total = ids.length;
    let success = 0, fail = 0;
    const progressEl = document.getElementById(progressId);
    const progressText = document.getElementById(progressTextId);
    const cancelBtn = document.getElementById(cancelBtnId);
    if (progressEl) progressEl.style.display = 'flex';
    if (cancelBtn) { cancelBtn.disabled = false; cancelBtn.textContent = 'Cancel'; }

    // Disable the draft button during processing
    const draftBtn = document.getElementById(draftBtnId);
    if (draftBtn) draftBtn.disabled = true;

    function updateProgress() {
        const done = success + fail;
        if (progressText) progressText.textContent = `Drafting: ${done} of ${total} complete` + (fail ? ` (${fail} failed)` : '');
    }
    updateProgress();

    // Process drafts concurrently (3 at a time)
    const CONCURRENCY = 3;
    async function processDraft(id) {
        if (bulkDraftCancelled) return;

        const row = document.querySelector(`#${checkIdPrefix}${id}`)?.closest('tr');
        const draftCellBtn = row?.querySelector('.actions-cell .btn-blue[title="Generate Draft"]');
        if (draftCellBtn) {
            draftCellBtn.disabled = true;
            draftCellBtn.textContent = 'Drafting...';
        }

        try {
            const result = await api('generate_draft', { method: 'POST', body: { id } });
            if (bulkDraftCancelled) {
                if (draftCellBtn) { draftCellBtn.disabled = false; draftCellBtn.textContent = 'Draft'; }
                return;
            }
            if (result.success) {
                success++;
                if (draftCellBtn) draftCellBtn.remove();
                if (row) {
                    const badge = row.querySelector('.badge');
                    if (badge && !['contacted','replied','interested','not_interested','onboarded'].includes(badge.textContent.trim().toLowerCase().replace(/\s+/g, '_'))) {
                        badge.className = 'badge badge-status-draft_generated';
                        badge.textContent = 'Draft Generated';
                    }
                    const cb = row.querySelector(checkClass);
                    if (cb) cb.dataset.hasDraft = '1';
                }
            } else {
                fail++;
                if (draftCellBtn) { draftCellBtn.disabled = false; draftCellBtn.textContent = 'Draft'; }
            }
        } catch {
            fail++;
            if (draftCellBtn) { draftCellBtn.disabled = false; draftCellBtn.textContent = 'Draft'; }
        }
        updateProgress();
    }

    // Run in batches of CONCURRENCY
    for (let i = 0; i < ids.length; i += CONCURRENCY) {
        if (bulkDraftCancelled) break;
        const batch = ids.slice(i, i + CONCURRENCY);
        await Promise.all(batch.map(id => processDraft(id)));
    }

    // Done
    const cancelled = bulkDraftCancelled;
    const doneMsg = cancelled
        ? `Cancelled: ${success} drafted` + (fail ? `, ${fail} failed` : '') + `, ${total - success - fail} skipped`
        : `Done: ${success} drafted` + (fail ? `, ${fail} failed` : '');
    if (progressText) progressText.textContent = doneMsg;
    if (progressEl) setTimeout(() => { progressEl.style.display = 'none'; }, 3000);

    if (draftBtn) draftBtn.disabled = false;
    notify(doneMsg, success ? 'success' : 'error');
    loadStats();
    if (onDone) onDone();
}

function bulkGenerateDrafts() {
    return runBulkDrafts({
        ids: getSelectedLeadIds(),
        checkClass: '.lead-check', checkIdPrefix: 'lead-check-',
        progressId: 'bulkDraftProgress', progressTextId: 'bulkDraftProgressText',
        cancelBtnId: 'btnCancelDraft', draftBtnId: 'btnDraftSelected',
        onDone: updateBulkBar,
    });
}

async function bulkDeleteLeads() {
    const ids = getSelectedLeadIds();
    if (!ids.length) return;
    if (!confirm(`Delete ${ids.length} lead(s)? This cannot be undone.`)) return;

    let success = 0, fail = 0;
    let lastError = '';
    for (const id of ids) {
        try {
            const result = await api('delete_lead', { method: 'POST', body: { id } });
            if (result.success) success++; else { fail++; lastError = result.message || 'Unknown error'; }
        } catch (err) { fail++; lastError = err.message; }
    }
    if (fail > 0) {
        notify(`Deleted: ${success}, Failed: ${fail}. Last error: ${lastError}`, 'error');
    } else {
        notify(`Successfully deleted ${success} lead(s)`, 'success');
    }
    loadLeads();
    loadStats();
}

// ─── Bulk Send Email ───
let bulkSendLeads = [];

async function openBulkSendModal(idsArg) {
    const ids = (Array.isArray(idsArg) && idsArg.length) ? idsArg : getSelectedLeadIds();
    if (!ids.length) { notify('No leads selected', 'error'); return; }

    const statusEl = document.getElementById('bulkSendStatus');
    const listEl = document.getElementById('bulkSendList');
    const sendBtn = document.getElementById('btnBulkSend');

    statusEl.textContent = 'Loading leads...';
    listEl.innerHTML = '';
    sendBtn.disabled = true;
    sendBtn.textContent = 'Send All';
    sendBtn.onclick = executeBulkSend;
    showModal('bulkSendModal');

    try {
        const data = await api('bulk_get_leads', { params: { ids: ids.join(',') } });
        if (!data.success) { statusEl.textContent = data.message; return; }

        bulkSendLeads = data.leads;

        // Filter out leads without email
        const withEmail = bulkSendLeads.filter(l => l.email);
        const withoutEmail = bulkSendLeads.filter(l => !l.email);

        // Render all leads
        renderBulkSendList();

        // Auto-generate drafts for leads that don't have one (3 at a time)
        const needsDraft = withEmail.filter(l => !l.draft_subject || !l.draft_body);
        if (needsDraft.length) {
            statusEl.textContent = `Generating drafts for ${needsDraft.length} lead(s)...`;
            const CONCURRENCY = 3;
            async function processSendDraft(lead) {
                const itemEl = document.getElementById('bulk-send-item-' + lead.id);
                if (itemEl) {
                    itemEl.querySelector('.bulk-send-item-draft').innerHTML = '<span class="bulk-send-item-generating">Generating draft...</span>';
                }
                try {
                    const result = await api('generate_draft', { method: 'POST', body: { id: lead.id } });
                    if (result.success) {
                        lead.draft_subject = result.subject;
                        lead.draft_body = result.body;
                        if (itemEl) renderBulkSendItemDraft(itemEl, lead);
                    } else {
                        if (itemEl) {
                            itemEl.querySelector('.bulk-send-item-draft').innerHTML = '<span class="bulk-send-item-error">Failed to generate draft</span>';
                        }
                    }
                } catch (e) {
                    if (itemEl) {
                        itemEl.querySelector('.bulk-send-item-draft').innerHTML = '<span class="bulk-send-item-error">Error: ' + esc(e.message) + '</span>';
                    }
                }
            }
            for (let i = 0; i < needsDraft.length; i += CONCURRENCY) {
                const batch = needsDraft.slice(i, i + CONCURRENCY);
                await Promise.all(batch.map(lead => processSendDraft(lead)));
            }
        }

        const sendable = withEmail.filter(l => l.draft_subject && l.draft_body);
        statusEl.textContent = `${sendable.length} email(s) ready to send` +
            (withoutEmail.length ? `, ${withoutEmail.length} skipped (no email)` : '');
        sendBtn.disabled = sendable.length === 0;

    } catch (e) {
        statusEl.textContent = 'Error: ' + e.message;
    }
}

function renderBulkSendList() {
    const listEl = document.getElementById('bulkSendList');
    listEl.innerHTML = bulkSendLeads.map(lead => {
        const hasEmail = !!lead.email;
        const hasDraft = lead.draft_subject && lead.draft_body;
        return `<div class="bulk-send-item" id="bulk-send-item-${lead.id}">
            <div class="bulk-send-item-header">
                <strong>${esc(lead.business_name)}</strong>
                <span class="bulk-send-item-email">${hasEmail ? esc(lead.email) : '<span class="bulk-send-item-no-email">No email address</span>'}</span>
            </div>
            <div class="bulk-send-item-draft">
                ${!hasEmail ? '<span class="bulk-send-item-no-email">Will be skipped (no email address)</span>' :
                  hasDraft ? `<div class="bulk-send-item-subject">Subject: ${esc(lead.draft_subject)}</div><div class="bulk-send-item-body">${esc(lead.draft_body)}</div>` :
                  '<span class="bulk-send-item-generating">Draft will be generated...</span>'}
            </div>
        </div>`;
    }).join('');
}

function renderBulkSendItemDraft(itemEl, lead) {
    itemEl.querySelector('.bulk-send-item-draft').innerHTML =
        `<div class="bulk-send-item-subject">Subject: ${esc(lead.draft_subject)}</div><div class="bulk-send-item-body">${esc(lead.draft_body)}</div>`;
}

async function executeBulkSend() {
    const sendBtn = document.getElementById('btnBulkSend');
    const statusEl = document.getElementById('bulkSendStatus');
    const sendable = bulkSendLeads.filter(l => l.email && l.draft_subject && l.draft_body);

    if (!sendable.length) return;

    sendBtn.disabled = true;
    sendBtn.textContent = 'Sending...';

    let success = 0, fail = 0;
    for (const lead of sendable) {
        statusEl.textContent = `Sending ${success + fail + 1} of ${sendable.length}...`;
        try {
            const result = await api('send_email', { method: 'POST', body: { id: lead.id } });
            if (result.success) success++; else fail++;
        } catch { fail++; }
    }

    notify(`Sent: ${success}` + (fail ? `, Failed: ${fail}` : ''), success ? 'success' : 'error');
    closeBulkSendModal();

    if (success > 0) {
        loadLeads();
        loadStats();
        // Refresh the editorial/creator leads tables too if they were ever
        // populated, so a bulk send launched from those tabs reflects the new status.
        if (editorialLeadsPaginator) loadEditorialLeads();
        if (creatorLeadsPaginator) loadCreatorLeads();
    }
}

function closeBulkSendModal() {
    closeModal('bulkSendModal');
    bulkSendLeads = [];
}

// ─── Lead Detail Modal ───
async function openLeadDetail(id) {
    currentLeadId = id;
    try {
        const data = await api('get_lead', { params: { id } });
        if (!data.success) { notify(data.message, 'error'); return; }

        const lead = data.lead;
        document.getElementById('detailModalTitle').textContent = lead.business_name;

        // Info tab
        document.getElementById('detailBusinessName').value = lead.business_name || '';
        document.getElementById('detailContactName').value = lead.contact_name || '';
        document.getElementById('detailEmail').value = lead.email || '';
        document.getElementById('detailPhone').value = lead.phone || '';
        document.getElementById('detailWebsite').value = lead.website || '';
        document.getElementById('detailAddress').value = lead.address || '';
        document.getElementById('detailCategory').value = lead.category || '';
        document.getElementById('detailCity').value = lead.city || '';
        document.getElementById('detailSource').value = lead.source || 'manual';
        document.getElementById('detailStatus').value = lead.status || 'new';
        document.getElementById('detailResponseStatus').value = lead.response_status || 'no_response';

        document.getElementById('detailCompanySize').value = lead.company_size || '';
        document.getElementById('detailOfferSent').value = lead.offer_sent ? '1' : '0';
        document.getElementById('detailContactPageUrl').value = lead.contact_page_url || '';
        document.getElementById('detailNotes').value = lead.notes || '';
        document.getElementById('detailFeedback').value = lead.feedback_summary || '';

        // Meta info
        // Meta info removed from UI

        // Draft tab
        document.getElementById('draftSubject').value = lead.draft_subject || '';
        document.getElementById('draftBody').value = lead.draft_body || '';
        updateDraftStatus(lead);
        updateFollowupStatus(lead);

        // Reset to info tab (scope to the modal, since the page now has its own .tab bar)
        switchTab('tabInfo', document.querySelector('#leadDetailModal .tab'));

        // Load activity
        loadActivity(id);

        showModal('leadDetailModal');
    } catch (e) {
        notify(e.message, 'error');
    }
}

function updateDraftStatus(lead) {
    const bar = document.getElementById('draftStatusBar');
    const sendBtn = document.getElementById('btnSend');
    const genBtn = document.getElementById('btnGenerate');
    const saveBtn = document.getElementById('btnSaveDraft');

    let statusHtml = '';
    const isSent = ['contacted','replied','interested','not_interested','onboarded'].includes(lead.status);
    const isDisqualified = lead.status === 'disqualified';

    // Editing the draft only makes sense before it's sent or disqualified.
    if (saveBtn) saveBtn.style.display = (isDisqualified || (isSent && lead.sent_at)) ? 'none' : '';

    if (isDisqualified) {
        const reasonTag = lead.disqualified_reason ? ` (${escapeHtml(lead.disqualified_reason)})` : '';
        statusHtml = `<span class="badge badge-status-disqualified">Disqualified</span>${reasonTag}`;
        sendBtn.disabled = true;
        genBtn.style.display = 'none';
        sendBtn.style.display = 'none';
    } else if (isSent && lead.sent_at) {
        statusHtml = `<span class="badge badge-status-contacted">Sent</span> on ${formatDateTime(lead.sent_at)}`;
        sendBtn.disabled = true;
        genBtn.style.display = 'none';
        sendBtn.style.display = 'none';
    } else if (lead.draft_subject || lead.draft_body) {
        statusHtml = '<span class="badge badge-status-draft_generated">Draft Ready</span>';
        sendBtn.disabled = !lead.email;
        genBtn.style.display = '';
        sendBtn.style.display = '';
        genBtn.textContent = 'Regenerate Draft';
        if (!lead.email) statusHtml += ' <span class="text-muted">(no email address)</span>';
    } else {
        statusHtml = '<span class="badge badge-status-new">No Draft</span>';
        sendBtn.disabled = true;
        genBtn.style.display = '';
        sendBtn.style.display = '';
        genBtn.textContent = 'Generate Draft';
    }

    if (lead.drafted_at) {
        statusHtml += ` Drafted: ${formatDateTime(lead.drafted_at)}`;
    }

    bar.innerHTML = statusHtml;

    // Info section
    let info = '';
    if (!lead.email) info = 'No email address. Email sending is disabled for this lead.';
    document.getElementById('draftInfo').textContent = info;
}

function updateFollowupStatus(lead) {
    const el = document.getElementById('followupStatus');
    if (!el) return;

    const replied = ['replied', 'interested', 'not_interested', 'onboarded'].includes(lead.status);

    if ((lead.followup_count | 0) > 0 && lead.last_followup_at) {
        el.textContent = 'Follow-up #' + lead.followup_count + ' sent ' + formatDateTime(lead.last_followup_at);
    } else if (replied) {
        el.textContent = 'Lead responded, no follow-up needed.';
    } else if (lead.next_followup_due_at) {
        const due = new Date(lead.next_followup_due_at);
        if (due <= new Date()) {
            el.textContent = 'Follow-up due now (will send on next pipeline run).';
        } else {
            el.textContent = 'Follow-up scheduled for ' + formatDateTime(lead.next_followup_due_at);
        }
    } else if (lead.sent_at) {
        el.textContent = 'No follow-up scheduled (legacy send; backfill migration may not have run).';
    } else {
        el.textContent = 'Not sent yet. Follow-up will be scheduled at send time.';
    }
}

function openWebsite() {
    let url = document.getElementById('detailWebsite').value.trim();
    if (!url) return;
    try {
        if (!/^[a-zA-Z][a-zA-Z0-9+.-]*:/.test(url)) url = 'https://' + url;
        const parsed = new URL(url);
        if (parsed.protocol === 'http:' || parsed.protocol === 'https:') {
            window.open(parsed.href, '_blank', 'noopener,noreferrer');
        }
    } catch (e) { /* invalid URL */ }
}

async function saveLeadDetails() {
    const data = {
        id: currentLeadId,
        business_name: document.getElementById('detailBusinessName').value,
        contact_name: document.getElementById('detailContactName').value,
        email: document.getElementById('detailEmail').value,
        phone: document.getElementById('detailPhone').value,
        website: document.getElementById('detailWebsite').value,
        address: document.getElementById('detailAddress').value,
        category: document.getElementById('detailCategory').value,
        city: document.getElementById('detailCity').value,
        status: document.getElementById('detailStatus').value,
        response_status: document.getElementById('detailResponseStatus').value,
        company_size: document.getElementById('detailCompanySize').value,
        offer_sent: document.getElementById('detailOfferSent').value,
        contact_page_url: document.getElementById('detailContactPageUrl').value,
        notes: document.getElementById('detailNotes').value,
        feedback_summary: document.getElementById('detailFeedback').value,
    };

    // Also save draft fields if modified
    data.draft_subject = document.getElementById('draftSubject').value;
    data.draft_body = document.getElementById('draftBody').value;

    try {
        const result = await api('update_lead', { method: 'POST', body: data });
        if (result.success) {
            closeModal('leadDetailModal');
            refreshLeadViews();
            loadStats();
        } else {
            notify(result.message, 'error');
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

async function deleteCurrentLead() {
    if (!confirm('Are you sure you want to delete this lead?')) return;
    try {
        const result = await api('delete_lead', { method: 'POST', body: { id: currentLeadId } });
        notify(result.message, result.success ? 'success' : 'error');
        if (result.success) {
            closeModal('leadDetailModal');
            refreshLeadViews();
            loadStats();
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

// ─── Add Lead ───
function showAddLeadModal() {
    document.getElementById('addWebsite').value = '';
    showModal('addLeadModal');
}

async function createLead() {
    const website = document.getElementById('addWebsite').value.trim();
    if (!website) { notify('Website is required', 'error'); return; }

    // Enrichment fetches the site + makes an AI call, so it takes a few seconds.
    const btn = document.getElementById('btnAddLead');
    const originalText = btn ? btn.textContent : '';
    if (btn) { btn.disabled = true; btn.textContent = 'Fetching…'; }

    try {
        const result = await api('create_lead_from_website', { method: 'POST', body: { website } });
        if (result.success) {
            closeModal('addLeadModal');
            notify(result.message || 'Lead added', 'success');
            loadLeads();
            loadStats();
        } else {
            notify(result.message, 'error');
        }
    } catch (e) {
        notify(e.message, 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.textContent = originalText; }
    }
}

// ─── Business Discovery ───
async function searchBusinesses() {
    const city = document.getElementById('discCity').value.trim();
    if (!city) { notify('City is required', 'error'); return; }

    const btn = document.getElementById('searchBtn');
    btn.disabled = true;
    btn.textContent = 'Searching...';

    const sizeFilter = document.getElementById('discCompanySize').value;
    const limit = parseInt(document.getElementById('discLimit').value);
    const baseParams = {
        city: city,
        province: document.getElementById('discProvince').value.trim(),
        category: document.getElementById('discCategory').value.trim(),
        limit: limit,
    };

    discoveryResults = [];
    document.getElementById('discoveryResults').style.display = 'block';

    // Keep paging Google Places until we have `limit` results matching the
    // filter, or the API stops returning new businesses (exhausted area).
    // 10 is a safety cap so a city + category with no matches doesn't loop
    // forever; each round already excludes places_ids already collected.
    const maxAttempts = 10;
    let lastNote = null;

    for (let attempt = 0; attempt < maxAttempts; attempt++) {
        // Exclude businesses we already have so the API finds new ones
        const excludeIds = discoveryResults.map(b => b.places_id).filter(Boolean).join(',');
        const params = { ...baseParams };
        if (excludeIds) params.exclude_place_ids = excludeIds;

        btn.textContent = attempt === 0 ? 'Searching...' : `Searching (round ${attempt + 1})...`;
        const data = await api('search_businesses', { params });

        if (!data.success) {
            if (attempt === 0) {
                btn.disabled = false;
                btn.textContent = 'Search';
                notify(data.message, 'error');
                return;
            }
            break;
        }

        if (!data.businesses.length) break;
        lastNote = data.note || null;

        // Classify sizes for the new batch
        const newBatch = data.businesses;
        try {
            const classifyData = await api('classify_company_sizes', {
                method: 'POST',
                body: { businesses: newBatch }
            });
            if (classifyData.success && classifyData.sizes) {
                classifyData.sizes.forEach((size, i) => {
                    if (i < newBatch.length) newBatch[i].company_size = size;
                });
            }
        } catch (e) {
            console.warn('Company size classification failed:', e.message);
        }

        // Drop items that don't match the size filter so they never land in
        // discoveryResults, since they shouldn't be displayed OR imported.
        const matchingBatch = sizeFilter
            ? newBatch.filter(b => b.company_size === sizeFilter)
            : newBatch;

        discoveryResults = discoveryResults.concat(matchingBatch);
        renderDiscoveryResults();
        saveDiscoveryToSession();

        if (discoveryResults.length >= limit) break;
    }

    btn.disabled = false;
    btn.textContent = 'Search';

    if (lastNote && sizeFilter && discoveryResults.length < limit) {
        notify(`Found ${discoveryResults.length} ${sizeFilter} businesses (asked for ${limit}). Not enough in this area.`, 'info');
    }
}

function renderDiscoveryResults() {
    document.getElementById('discoveryCount').textContent = `${discoveryResults.length} results`;

    const tbody = document.getElementById('discoveryTableBody');

    if (!discoveryResults.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="empty-state">No businesses found</td></tr>';
        return;
    }

    tbody.innerHTML = discoveryResults.map((biz, i) => {
        const origIndex = i;
        return `
        <tr>
            <td><div class="checkbox"><input type="checkbox" class="disc-check" data-index="${origIndex}" id="disc-check-${origIndex}" checked><label for="disc-check-${origIndex}"></label></div></td>
            <td>${esc(biz.business_name)}</td>
            <td>${biz.email ? esc(biz.email) : '<span class="text-muted">—</span>'}</td>
            <td>${biz.phone ? esc(biz.phone) : '<span class="text-muted">—</span>'}</td>
            <td>${biz.website ? '<a href="' + esc(biz.website) + '" target="_blank" rel="noopener noreferrer" class="link">Link</a>' : '<span class="text-muted">—</span>'}</td>
            <td>${esc(biz.address || '')}</td>
            <td>${esc(biz.category || '')}</td>
            <td>${biz.company_size ? '<span class="badge badge-size-' + biz.company_size + '">' + biz.company_size.charAt(0).toUpperCase() + biz.company_size.slice(1) + '</span>' : '<span class="text-muted">—</span>'}</td>
        </tr>`;
    }).join('');

    const selectAll = document.getElementById('discSelectAll');
    if (selectAll) selectAll.checked = true;

    // Initialize or reset discovery pagination
    const discTable = document.querySelector('.discovery-table');
    if (discTable) {
        if (!discoveryPaginator) {
            discoveryPaginator = new TablePaginator(discTable, { perPage: 25 });
        } else {
            discoveryPaginator.reset();
        }
    }
}

function toggleDiscoveryCheckboxes(master) {
    document.querySelectorAll('.disc-check').forEach(cb => cb.checked = master.checked);
}

function selectAllDiscovery() {
    document.querySelectorAll('.disc-check').forEach(cb => cb.checked = true);
    document.getElementById('discSelectAll').checked = true;
}

function deselectAllDiscovery() {
    document.querySelectorAll('.disc-check').forEach(cb => cb.checked = false);
    document.getElementById('discSelectAll').checked = false;
}

async function importSelected() {
    const selectedIndexes = [];
    document.querySelectorAll('.disc-check:checked').forEach(cb => {
        selectedIndexes.push(parseInt(cb.dataset.index));
    });
    if (!selectedIndexes.length) { notify('No businesses selected', 'error'); return; }
    const selected = selectedIndexes.map(i => discoveryResults[i]);
    const success = await doImport(selected);
    if (success) {
        // Remove imported businesses from discovery results
        discoveryResults = discoveryResults.filter((_, i) => !selectedIndexes.includes(i));
        renderDiscoveryResults();
        saveDiscoveryToSession();
    }
}

async function importAll() {
    if (!discoveryResults.length) return;
    const success = await doImport(discoveryResults);
    if (success) {
        discoveryResults = [];
        renderDiscoveryResults();
        saveDiscoveryToSession();
    }
}

async function doImport(businesses) {
    try {
        const result = await api('import_leads', { method: 'POST', body: { businesses } });
        notify(result.message, result.success ? 'success' : 'error');
        if (result.success) {
            loadLeads();
            loadStats();
            return true;
        }
    } catch (err) {
        notify('Import failed: ' + err.message, 'error');
    }
    return false;
}

// ─── Draft Generation ───
async function generateDraft() {
    if (!currentLeadId) return;
    const btn = document.getElementById('btnGenerate');
    btn.disabled = true;
    btn.textContent = 'Generating...';

    try {
        const data = await api('generate_draft', { method: 'POST', body: { id: currentLeadId } });
        btn.disabled = false;
        btn.textContent = 'Regenerate Draft';

        if (data.success) {
            document.getElementById('draftSubject').value = data.subject;
            document.getElementById('draftBody').value = data.body;
            // Refresh draft status without switching tabs
            const leadData = await api('get_lead', { params: { id: currentLeadId } });
            if (leadData.success) updateDraftStatus(leadData.lead);
            loadActivity(currentLeadId);
        } else {
            notify(data.message, 'error');
        }
    } catch (e) {
        btn.disabled = false;
        btn.textContent = 'Regenerate Draft';
        notify(e.message, 'error');
    }
}

async function quickGenerateDraft(id, btn) {
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Drafting...';
    }
    try {
        const result = await api('generate_draft', { method: 'POST', body: { id } });
        if (result.success) {
            refreshLeadViews();
            loadStats();
        } else {
            notify(result.message, 'error');
            if (btn) { btn.disabled = false; btn.textContent = 'Draft'; }
        }
    } catch (err) {
        notify('Draft failed: ' + err.message, 'error');
        if (btn) { btn.disabled = false; btn.textContent = 'Draft'; }
    }
}

// ─── Email Workflow ───
// Persist the current subject/body from the Draft tab. Used on its own via the
// Save Draft button, and by sendEmail() so a send always uses exactly what's
// shown (the send endpoint reads the draft straight from the DB).
async function saveDraft() {
    if (!currentLeadId) return;
    const subject = document.getElementById('draftSubject').value;
    const body = document.getElementById('draftBody').value;
    const btn = document.getElementById('btnSaveDraft');
    const original = btn ? btn.textContent : '';
    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
    try {
        const result = await api('update_lead', {
            method: 'POST',
            body: { id: currentLeadId, draft_subject: subject, draft_body: body }
        });
        if (result.success) {
            notify('Draft saved', 'success');
            // A draft typed from scratch (Send was disabled) should now be sendable.
            const sendBtn = document.getElementById('btnSend');
            const hasEmail = (document.getElementById('detailEmail').value || '').trim() !== '';
            if (sendBtn && (subject.trim() || body.trim()) && hasEmail) sendBtn.disabled = false;
            refreshLeadViews();
        } else {
            notify(result.message, 'error');
        }
    } catch (e) {
        notify(e.message, 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.textContent = original; }
    }
}

async function sendEmail() {
    if (!currentLeadId) return;
    if (!confirm('Send this email now?')) return;

    const btn = document.getElementById('btnSend');
    btn.disabled = true;
    btn.textContent = 'Sending...';

    try {
        // Persist any edits first so we send exactly what's shown in the textareas.
        await api('update_lead', {
            method: 'POST',
            body: {
                id: currentLeadId,
                draft_subject: document.getElementById('draftSubject').value,
                draft_body: document.getElementById('draftBody').value
            }
        });

        const result = await api('send_email', { method: 'POST', body: { id: currentLeadId } });
        btn.textContent = 'Send Email';

        if (result.success) {
            openLeadDetail(currentLeadId);
            refreshLeadViews();
            loadStats();
        } else {
            notify(result.message, 'error');
            btn.disabled = false;
        }
    } catch (e) {
        btn.textContent = 'Send Email';
        btn.disabled = false;
        notify(e.message, 'error');
    }
}


function copyDraft(btn) {
    const subject = document.getElementById('draftSubject').value;
    const body = document.getElementById('draftBody').value;
    const text = `Subject: ${subject}\n\n${body}`;

    const copied = () => {
        const original = btn.textContent;
        btn.textContent = 'Copied';
        setTimeout(() => btn.textContent = original, 1000);
    };

    navigator.clipboard.writeText(text).then(copied).catch(() => {
        // Fallback
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        copied();
    });
}

// ─── AI Enrichment ───
// ─── Activity ───
async function loadActivity(id) {
    try {
        const data = await api('get_activity', { params: { id } });
        const container = document.getElementById('activityTimeline');

        if (!data.success || !data.activity.length) {
            container.innerHTML = '<p class="empty-state-text">No activity yet</p>';
            return;
        }

        container.innerHTML = data.activity.map(a => `
            <div class="activity-item">
                <div class="activity-dot"></div>
                <div class="activity-content">
                    <strong>${formatActionType(a.action_type)}</strong>
                    ${a.details ? '<span class="activity-details">' + esc(a.details) + '</span>' : ''}
                    <span class="activity-time">${formatDateTime(a.created_at)}</span>
                </div>
            </div>
        `).join('');
    } catch (e) {
        notify(e.message, 'error');
    }
}

// ─── CSV ───
function showImportCSVModal() {
    document.getElementById('csvFile').value = '';
    showModal('csvImportModal');
}

async function importCSV() {
    const fileInput = document.getElementById('csvFile');
    if (!fileInput.files.length) { notify('Please select a CSV file', 'error'); return; }

    const formData = new FormData();
    formData.append('csv_file', fileInput.files[0]);

    try {
        const result = await api('import_csv', { method: 'POST', body: formData });

        notify(result.message, result.success ? 'success' : 'error');
        if (result.success) {
            closeModal('csvImportModal');
            loadLeads();
            loadStats();
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

// ─── Panel Toggle ───
function togglePanel(contentId) {
    const content = document.getElementById(contentId);
    const toggle = document.getElementById(contentId.replace('Content', 'Toggle'));
    if (content.style.display === 'none') {
        content.style.display = 'block';
        toggle.innerHTML = '&#9660;';
    } else {
        content.style.display = 'none';
        toggle.innerHTML = '&#9654;';
    }
}

// ─── Modal Helpers ───
function showModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modals on backdrop click (only if mousedown also started on the backdrop)
let modalMouseDownTarget = null;
document.addEventListener('mousedown', function (e) {
    modalMouseDownTarget = e.target;
});
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal') && modalMouseDownTarget === e.target) {
        e.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});

// Close modals on Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(m => {
            if (m.style.display === 'flex') {
                m.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
});

// ─── Modal tab switching (Info / Email Draft / Activity inside the lead-detail modal) ───
// Page-level tabs are handled by admin/section-tabs.js. This function is only
// for the modal's inner tabs (.tabs inside .modal-body) and is scoped to its
// own container so it doesn't clobber the page-level section-tabs.
function switchTab(tabId, btn) {
    const tabsContainer = btn.closest('.tabs');
    if (!tabsContainer) return;
    const scope = tabsContainer.parentElement;
    scope.querySelectorAll(':scope > .tab-content').forEach(t => t.classList.remove('active'));
    tabsContainer.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    const target = document.getElementById(tabId);
    if (target) target.classList.add('active');
    btn.classList.add('active');
}

// ─── Formatting Helpers ───
function esc(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function formatStatus(status) {
    return status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function formatSource(source) {
    switch (source) {
        case 'google_places':      return 'Google Places';
        case 'google_places_auto': return 'Google Places <small style="color:#888">· auto</small>';
        case 'shopify_auto':       return 'Shopify';
        case 'manual':             return 'Manual';
        case 'csv_import':         return 'CSV';
        default:                   return source ? esc(source) : '<span class="text-muted">—</span>';
    }
}


function formatActionType(type) {
    return type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function formatDateTime(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-CA', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' });
}

// ─── Follow-ups tab ───

let currentFollowupView = 'pending_review';

window.switchFollowupsView = function(button, view) {
    document.querySelectorAll('[data-fu-view]').forEach(b => b.classList.remove('active'));
    button.classList.add('active');
    currentFollowupView = view;
    loadFollowups();
};

async function loadFollowups() {
    const container = document.getElementById('followupsContainer');
    container.innerHTML = '<p class="empty-state">Loading...</p>';
    try {
        const data = await api('get_followups&view=' + encodeURIComponent(currentFollowupView));
        if (!data.success) {
            container.innerHTML = '<p class="empty-state">Error loading: ' + (data.message || 'unknown') + '</p>';
            return;
        }
        if (!data.rows.length) {
            const emptyMessages = {
                'pending_review': 'No follow-ups awaiting review. Drafts appear here ~1 day before each scheduled send.',
                'approved': 'No approved follow-ups waiting in the send queue.',
                'upcoming': 'No follow-ups scheduled. New ones are created as leads receive their first email.',
                'sent': 'No follow-ups sent in the last 30 days.',
                'halted': 'No halted or failed follow-ups in the last 30 days.',
            };
            container.innerHTML = '<p class="empty-state">' + (emptyMessages[currentFollowupView] || 'No rows.') + '</p>';
            updateFollowupsBulkBar();
            return;
        }
        container.innerHTML = data.rows.map(r => renderFollowupRow(r, currentFollowupView)).join('');
        if (currentFollowupView === 'pending_review') {
            var badge = document.getElementById('fuCountPending');
            if (badge) badge.textContent = data.rows.length;
        }
        updateFollowupsBulkBar();
    } catch (e) {
        container.innerHTML = '<p class="empty-state">Error loading follow-ups: ' + e.message + '</p>';
    }
}

function renderFollowupRow(r, view) {
    const cityLabel = r.city ? ' (' + escapeHtml(r.city) + ')' : '';
    const scheduledStr = r.scheduled_for ? formatScheduled(r.scheduled_for) : '';
    const sentStr = r.sent_at ? formatScheduled(r.sent_at) : '';
    const haltReason = r.halt_reason ? ' · Reason: ' + escapeHtml(r.halt_reason) : '';
    const abLabel = r.ab_variant_label ? ' · A/B: ' + escapeHtml(r.ab_variant_label) : '';

    let actions = '';
    let bodyEditor = '';
    let checkboxCell = '';

    if (view === 'pending_review') {
        checkboxCell = '<input type="checkbox" class="fu-row-check" data-fu-id="' + r.id + '" data-lead-id="' + r.lead_id + '" onchange="updateFollowupsBulkBar()">';
        bodyEditor = '<input type="text" class="fu-subject" data-id="' + r.id + '" value="' + escapeHtml(r.draft_subject || '') + '" style="width:100%; margin-bottom:6px;">' +
            '<textarea class="fu-body" data-id="' + r.id + '" rows="6" style="width:100%; font-family:inherit;">' + escapeHtml(r.draft_body || '') + '</textarea>';
        actions = '<button class="btn btn-small btn-blue" onclick="approveFollowup(' + r.id + ')">Approve & queue</button>' +
            ' <button class="btn btn-small btn-blue" onclick="regenerateFollowup(' + r.id + ')">Regenerate draft</button>' +
            ' <button class="btn btn-small btn-neutral" onclick="skipFollowup(' + r.id + ')">Skip this touch</button>' +
            ' <button class="btn btn-small btn-red" onclick="haltFollowupSequence(' + r.lead_id + ')">Halt sequence</button>';
    } else if (view === 'approved') {
        actions = '<button class="btn btn-small btn-neutral" onclick="skipFollowup(' + r.id + ')">Skip</button>' +
            ' <button class="btn btn-small btn-red" onclick="haltFollowupSequence(' + r.lead_id + ')">Halt sequence</button>';
    } else if (view === 'upcoming') {
        actions = '<button class="btn btn-small btn-red" onclick="haltFollowupSequence(' + r.lead_id + ')">Halt sequence</button>';
    }

    return '<div class="panel" style="margin-bottom:12px;">' +
        '<div class="panel-content">' +
            '<div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">' +
                (checkboxCell ? '<div>' + checkboxCell + '</div>' : '') +
                '<div style="flex:1;">' +
                    '<strong>' + escapeHtml(r.business_name || 'Unknown') + '</strong>' + cityLabel +
                    ' &nbsp;·&nbsp; Touch ' + r.touch_number +
                    (scheduledStr ? ' &nbsp;·&nbsp; Scheduled ' + scheduledStr : '') +
                    (sentStr ? ' &nbsp;·&nbsp; Sent ' + sentStr : '') +
                    haltReason +
                '</div>' +
            '</div>' +
            (bodyEditor ? '<div style="margin-bottom:8px;">' + bodyEditor + '</div>' :
                (r.draft_subject ? '<div style="font-size:13px; color:#666;">Subject: ' + escapeHtml(r.draft_subject) + '</div>' : '')) +
            '<div style="font-size:12px; color:#999; margin-top:6px;">' + abLabel + '</div>' +
            (actions ? '<div style="margin-top:10px;">' + actions + '</div>' : '') +
        '</div>' +
    '</div>';
}

function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[c]);
}

function formatScheduled(dt) {
    try {
        const d = new Date(dt.replace(' ', 'T'));
        const now = new Date();
        const diffMs = d.getTime() - now.getTime();
        const diffHours = diffMs / 3600000;
        if (Math.abs(diffHours) < 48) {
            const h = Math.round(diffHours);
            if (h === 0) return 'now';
            if (h > 0) return 'in ' + h + 'h';
            return Math.abs(h) + 'h ago';
        }
        return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        return dt;
    }
}

window.approveFollowup = async function(id) {
    // Save any inline subject/body edits first
    const subjectInput = document.querySelector('.fu-subject[data-id="' + id + '"]');
    const bodyInput = document.querySelector('.fu-body[data-id="' + id + '"]');
    if (subjectInput && bodyInput) {
        const saveResult = await api('save_followup_draft', { method: 'POST', body: { id: id, subject: subjectInput.value, body: bodyInput.value } });
        if (!saveResult.success) {
            alert('Save failed: ' + (saveResult.message || 'unknown') + '. Not approving.');
            return;
        }
    }
    const data = await api('approve_followup', { method: 'POST', body: { id: id } });
    if (data.success) loadFollowups();
    else alert('Approve failed: ' + (data.message || 'unknown'));
};

window.regenerateFollowup = async function(id) {
    if (!confirm('Regenerate this follow-up via Gemini? Current draft will be replaced.')) return;
    const data = await api('regenerate_followup', { method: 'POST', body: { id: id } });
    if (data.success) loadFollowups();
    else alert('Regenerate failed: ' + (data.message || 'unknown'));
};

window.skipFollowup = async function(id) {
    if (!confirm('Skip this follow-up touch? The next touch in the sequence will still be sent on schedule.')) return;
    const data = await api('skip_followup', { method: 'POST', body: { id: id } });
    if (data.success) loadFollowups();
    else alert('Skip failed: ' + (data.message || 'unknown'));
};

window.haltFollowupSequence = async function(leadId) {
    if (!confirm('Halt the entire follow-up sequence for this lead? No more follow-ups will be sent.')) return;
    const data = await api('halt_followup_sequence', { method: 'POST', body: { lead_id: leadId } });
    if (data.success) loadFollowups();
    else alert('Halt failed: ' + (data.message || 'unknown'));
};

window.updateFollowupsBulkBar = function() {
    const checks = document.querySelectorAll('.fu-row-check:checked');
    const bar = document.getElementById('fuBulkActionsBar');
    document.getElementById('fuSelectedCount').textContent = checks.length;
    bar.style.display = checks.length > 0 ? 'flex' : 'none';
};

window.bulkApproveFollowups = async function() {
    const ids = Array.from(document.querySelectorAll('.fu-row-check:checked')).map(c => parseInt(c.dataset.fuId));
    if (!ids.length) return;
    const data = await api('bulk_approve_followups', { method: 'POST', body: { ids: ids } });
    if (data.success) { alert('Approved ' + data.approved_count + ' follow-up(s).'); loadFollowups(); }
    else alert('Bulk approve failed: ' + (data.message || 'unknown'));
};

window.bulkSkipFollowups = async function() {
    const ids = Array.from(document.querySelectorAll('.fu-row-check:checked')).map(c => parseInt(c.dataset.fuId));
    if (!ids.length) return;
    if (!confirm('Skip ' + ids.length + ' follow-up(s)?')) return;
    const data = await api('bulk_skip_followups', { method: 'POST', body: { ids: ids } });
    if (data.success) { alert('Skipped ' + data.skipped_count + ' follow-up(s).'); loadFollowups(); }
    else alert('Bulk skip failed: ' + (data.message || 'unknown'));
};

window.bulkHaltFollowupSequences = async function() {
    const leadIds = Array.from(new Set(Array.from(document.querySelectorAll('.fu-row-check:checked')).map(c => parseInt(c.dataset.leadId))));
    if (!leadIds.length) return;
    if (!confirm('Halt the follow-up sequence for ' + leadIds.length + ' lead(s)? This stops ALL remaining follow-ups for these leads.')) return;
    const data = await api('bulk_halt_followups', { method: 'POST', body: { lead_ids: leadIds } });
    if (data.success) { alert('Halted ' + data.halted_count + ' follow-up row(s).'); loadFollowups(); }
    else alert('Bulk halt failed: ' + (data.message || 'unknown'));
};

// Auto-load when the followups tab activates
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.section-tab[data-tab="followups"]').forEach(btn => {
        btn.addEventListener('click', () => setTimeout(loadFollowups, 0));
    });
    // Also auto-load on page load if we're already on the tab (e.g. ?tab=followups)
    if (window.location.search.indexOf('tab=followups') !== -1) {
        loadFollowups();
    }
});

window.loadLeadFollowups = async function() {
    const list = document.getElementById('leadFollowupsList');
    list.innerHTML = '<p class="empty-state-text">Loading...</p>';
    // currentLeadId is the global declared at outreach.js:2 and set by
    // openLeadDetail() at outreach.js:527 whenever a lead modal opens.
    if (!currentLeadId) {
        list.innerHTML = '<p class="empty-state-text">No lead selected.</p>';
        return;
    }
    const data = await api('get_followups_for_lead&lead_id=' + currentLeadId);
    if (!data.success) {
        list.innerHTML = '<p class="empty-state-text">Error: ' + (data.message || 'unknown') + '</p>';
        return;
    }
    if (!data.rows.length) {
        list.innerHTML = '<p class="empty-state-text">No follow-ups scheduled for this lead. Sequence is configured in Settings; rows are created when the first-touch email sends.</p>';
        return;
    }
    list.innerHTML = '<table class="data-table"><thead><tr><th>Touch</th><th>Status</th><th>Scheduled</th><th>Sent</th><th>Halt reason</th><th>A/B variant</th></tr></thead><tbody>' +
        data.rows.map(r =>
            '<tr>' +
                '<td>' + r.touch_number + '</td>' +
                '<td>' + escapeHtml(r.status) + '</td>' +
                '<td>' + (r.scheduled_for || '—') + '</td>' +
                '<td>' + (r.sent_at || '—') + '</td>' +
                '<td>' + (r.halt_reason ? escapeHtml(r.halt_reason) : '—') + '</td>' +
                '<td>' + (r.ab_variant_label ? escapeHtml(r.ab_variant_label) : '—') + '</td>' +
            '</tr>'
        ).join('') +
    '</tbody></table>';
};

// ─── Shopify Discovery ───

let shopifyResults = [];
let shopifyLastSummary = null; // { rejected_count, reject_reasons, total_evaluated }

function _shopifyUpdateUsageDisplay(callsToday, limit) {
    const span = document.getElementById('serpapiUsage');
    if (!span) return;
    span.textContent = `${callsToday}/${limit} SerpAPI queries`;
}

async function loadShopifyStatus() {
    try {
        const data = await api('shopify_get_status');
        if (data.success) {
            _shopifyUpdateUsageDisplay(data.serpapi_calls_today, data.serpapi_limit);
        }
    } catch (e) { /* non-fatal */ }
}

async function runShopifyDiscovery() {
    const limit = Math.min(50, Math.max(1, parseInt(document.getElementById('shopifyLimit').value, 10) || 10));
    const btn = document.getElementById('shopifyRunBtn');

    btn.disabled = true;
    btn.textContent = 'Searching…';

    try {
        const data = await api('shopify_run_dork', {
            method: 'POST',
            body: { limit }
        });

        if (!data.success) {
            notify(data.message || 'Shopify discovery failed');
            return;
        }

        shopifyResults = data.results || [];
        shopifyLastSummary = {
            rejected_count: data.rejected_count || 0,
            reject_reasons: data.reject_reasons || {},
            already_imported_count: data.already_imported_count || 0,
            total_evaluated: data.total_evaluated || 0,
            requested_limit: data.requested_limit || limit,
            quota_exhausted: !!data.quota_exhausted,
            queries_run: (data.queries_run || []).length,
        };
        _shopifyUpdateUsageDisplay(data.serpapi_calls_today, data.serpapi_limit);
        document.getElementById('shopifyResults').style.display = 'block';
        renderShopifyResults();

        if (shopifyLastSummary.quota_exhausted) {
            notify(`SerpAPI daily quota hit before finding ${shopifyLastSummary.requested_limit}. Got ${shopifyResults.length} fit${shopifyResults.length === 1 ? '' : 's'} after ${shopifyLastSummary.queries_run} ${shopifyLastSummary.queries_run === 1 ? 'query' : 'queries'}. Resets at midnight or raise SERPAPI_DAILY_QUERY_LIMIT in .env.`);
        }
    } catch (e) {
        notify(e.message);
    } finally {
        btn.disabled = false;
        btn.textContent = 'Run';
    }
}

function renderShopifyResults() {
    const body = document.getElementById('shopifyResultsBody');
    const countEl = document.getElementById('shopifyResultsCount');
    const importAllBtn = document.getElementById('shopifyImportAllBtn');
    const safe = (s) => escapeHtml(String(s ?? ''));

    // Table only ever holds importable fits. Already-imported rows are
    // filtered server-side at search time and removed locally after each
    // successful import.
    let summary = `${shopifyResults.length} fit`;
    if (shopifyLastSummary) {
        if (shopifyLastSummary.already_imported_count > 0) {
            summary += ` · ${shopifyLastSummary.already_imported_count} already imported (hidden)`;
        }
        if (shopifyLastSummary.rejected_count > 0) {
            const reasons = shopifyLastSummary.reject_reasons || {};
            const reasonStr = Object.entries(reasons)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 3)
                .map(([k, n]) => `${n}× ${k}`)
                .join(', ');
            summary += ` · ${shopifyLastSummary.rejected_count} rejected`;
            if (reasonStr) summary += ` (${reasonStr})`;
        }
    }
    countEl.textContent = summary;

    if (importAllBtn) {
        importAllBtn.disabled = shopifyResults.length === 0;
        importAllBtn.textContent = shopifyResults.length > 0 ? `Import ${shopifyResults.length} Fit${shopifyResults.length !== 1 ? 's' : ''}` : 'Import All Fits';
    }

    if (shopifyResults.length === 0) {
        const queriesRunN = (shopifyLastSummary && shopifyLastSummary.queries_run) || 0;
        const queryWord = queriesRunN === 1 ? 'query' : 'queries';
        let emptyMsg = `SerpAPI returned no results across ${queriesRunN} ${queryWord}.`;
        if (shopifyLastSummary && shopifyLastSummary.total_evaluated > 0) {
            const reasons = [];
            if (shopifyLastSummary.rejected_count > 0) reasons.push(`${shopifyLastSummary.rejected_count} rejected`);
            if (shopifyLastSummary.already_imported_count > 0) reasons.push(`${shopifyLastSummary.already_imported_count} already imported`);
            emptyMsg = reasons.length
                ? `No new fits. All ${shopifyLastSummary.total_evaluated} results across ${queriesRunN} ${queryWord} were filtered (${reasons.join(', ')}).`
                : `SerpAPI returned results across ${queriesRunN} ${queryWord} but none passed the filter.`;
        }
        body.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:24px; color:#888;">${emptyMsg}</td></tr>`;
        return;
    }

    body.innerHTML = shopifyResults.map((r, idx) => {
        const displayUrl = r.final_url || r.canonical_url;
        const store = r.business_name
            ? `<div><strong>${safe(r.business_name)}</strong></div><div style="font-size:12px;"><a href="${safe(displayUrl)}" target="_blank" rel="noopener" class="link">${safe(displayUrl)}</a></div>`
            : `<a href="${safe(displayUrl)}" target="_blank" rel="noopener" class="link">${safe(displayUrl)}</a>`;

        return '<tr>' +
            `<td>${store}</td>` +
            `<td>${safe(r.email) || '—'}</td>` +
            `<td>${r.products_count != null ? safe(r.products_count) : '—'}</td>` +
            `<td>${safe(r.first_product_at) || '—'}</td>` +
            `<td>${safe(r.country) || '—'}</td>` +
            `<td><span class="status-badge status-replied">fit</span></td>` +
            `<td><button class="btn btn-small btn-blue" onclick="importShopifyRow(${idx}, this)">Import</button></td>` +
        '</tr>';
    }).join('');
}

async function importShopifyRow(idx, btn) {
    const row = shopifyResults[idx];
    if (!row) return;
    btn.disabled = true;
    btn.textContent = 'Importing…';
    try {
        const data = await api('shopify_import', {
            method: 'POST',
            body: { canonical_url: row.canonical_url }
        });
        if (data.success) {
            shopifyResults = shopifyResults.filter(r => r.canonical_url !== row.canonical_url);
            if (shopifyLastSummary) {
                shopifyLastSummary.already_imported_count = (shopifyLastSummary.already_imported_count || 0) + 1;
            }
            renderShopifyResults();
            loadStats();
            loadLeads();
        } else {
            notify(data.message || 'Import failed');
            btn.disabled = false;
            btn.textContent = 'Import';
        }
    } catch (e) {
        notify(e.message);
        btn.disabled = false;
        btn.textContent = 'Import';
    }
}

async function importAllShopifyFits() {
    if (shopifyResults.length === 0) return;
    if (!confirm(`Import ${shopifyResults.length} Shopify store${shopifyResults.length === 1 ? '' : 's'} as new leads?`)) return;

    // Copy by canonical_url so index shifts during the loop don't matter
    const toImport = shopifyResults.map(r => r.canonical_url);
    const btn = document.getElementById('shopifyImportAllBtn');
    btn.disabled = true;
    btn.textContent = 'Importing…';

    const succeeded = new Set();
    let failed = 0;
    for (const canonical of toImport) {
        try {
            const data = await api('shopify_import', {
                method: 'POST',
                body: { canonical_url: canonical }
            });
            if (data.success) succeeded.add(canonical);
            else failed++;
        } catch (e) {
            failed++;
        }
    }

    shopifyResults = shopifyResults.filter(r => !succeeded.has(r.canonical_url));
    if (shopifyLastSummary) {
        shopifyLastSummary.already_imported_count = (shopifyLastSummary.already_imported_count || 0) + succeeded.size;
    }
    renderShopifyResults();
    loadStats();
    loadLeads();
    notify(`Imported ${succeeded.size} lead${succeeded.size === 1 ? '' : 's'}` + (failed > 0 ? `, ${failed} failed` : ''));
}

// Load Shopify status on page load so the usage display is populated
document.addEventListener('DOMContentLoaded', loadShopifyStatus);

// ─── Editorial / Roundup Discovery ───

let editorialResults = [];
let editorialLastSummary = null;

async function loadEditorialStatus() {
    try {
        const data = await api('editorial_get_status');
        if (data.success) {
            const usage = document.getElementById('editorialSerpUsage');
            if (usage) usage.textContent = `${data.serpapi_calls_today}/${data.serpapi_limit} queries`;
            const hunter = document.getElementById('editorialHunterState');
            if (hunter) hunter.textContent = data.has_hunter ? 'connected' : 'not set (scrapes contact pages instead)';
        }
    } catch (e) { /* non-fatal */ }
}

async function runEditorialDiscovery() {
    const limit = Math.min(30, Math.max(1, parseInt(document.getElementById('editorialLimit').value, 10) || 8));
    const btn = document.getElementById('editorialRunBtn');
    btn.disabled = true;
    btn.textContent = 'Searching…';
    try {
        const data = await api('editorial_run_discovery', { method: 'POST', body: { limit } });
        if (!data.success) {
            notify(data.message || 'Editorial discovery failed');
            return;
        }
        editorialResults = data.results || [];
        editorialLastSummary = {
            rejected_count: data.rejected_count || 0,
            reject_reasons: data.reject_reasons || {},
            already_imported_count: data.already_imported_count || 0,
            already_rejected_count: data.already_rejected_count || 0,
            total_evaluated: data.total_evaluated || 0,
            requested_limit: data.requested_limit || limit,
            quota_exhausted: !!data.quota_exhausted,
            queries_run: (data.queries_run || []).length,
        };
        const usage = document.getElementById('editorialSerpUsage');
        if (usage) usage.textContent = `${data.serpapi_calls_today}/${data.serpapi_limit} queries`;
        document.getElementById('editorialResults').style.display = 'block';
        renderEditorialResults();
        if (editorialLastSummary.quota_exhausted) {
            notify(`SerpAPI daily quota hit before finding ${editorialLastSummary.requested_limit}. Got ${editorialResults.length} fit${editorialResults.length === 1 ? '' : 's'}. Resets at midnight or raise SERPAPI_DAILY_QUERY_LIMIT in .env.`);
        }
    } catch (e) {
        notify(e.message);
    } finally {
        btn.disabled = false;
        btn.textContent = 'Run';
    }
}

function renderEditorialResults() {
    const body = document.getElementById('editorialResultsBody');
    const countEl = document.getElementById('editorialResultsCount');
    const importAllBtn = document.getElementById('editorialImportAllBtn');
    const safe = (s) => escapeHtml(String(s ?? ''));

    let summary = `${editorialResults.length} fit`;
    if (editorialLastSummary) {
        if (editorialLastSummary.already_imported_count > 0) {
            summary += ` · ${editorialLastSummary.already_imported_count} already imported (hidden)`;
        }
        if (editorialLastSummary.already_rejected_count > 0) {
            summary += ` · ${editorialLastSummary.already_rejected_count} previously skipped (hidden)`;
        }
        if (editorialLastSummary.rejected_count > 0) {
            const reasons = editorialLastSummary.reject_reasons || {};
            const reasonStr = Object.entries(reasons)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 3)
                .map(([k, n]) => `${n}× ${k}`)
                .join(', ');
            summary += ` · ${editorialLastSummary.rejected_count} skipped`;
            if (reasonStr) summary += ` (${reasonStr})`;
        }
    }
    countEl.textContent = summary;

    if (importAllBtn) {
        importAllBtn.disabled = editorialResults.length === 0;
        importAllBtn.textContent = editorialResults.length > 0 ? `Import ${editorialResults.length} Fit${editorialResults.length !== 1 ? 's' : ''}` : 'Import All Fits';
    }

    if (editorialResults.length === 0) {
        const n = (editorialLastSummary && editorialLastSummary.total_evaluated) || 0;
        body.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:24px; color:#888;">No new roundups to pitch (evaluated ${n}). They were not roundups, already list Argo, or had no findable contact.</td></tr>`;
        return;
    }

    body.innerHTML = editorialResults.map((r, idx) => {
        const outlet = `<div><strong>${safe(r.outlet_name) || safe(r.canonical_url)}</strong></div>`;
        const emailSrc = r.email_source ? ` <span style="color:#888; font-size:11px;">(${safe(r.email_source)})</span>` : '';
        return '<tr>' +
            `<td>${outlet}</td>` +
            `<td>${safe(r.author_name) || '—'}</td>` +
            `<td>${safe(r.email) || '—'}${emailSrc}</td>` +
            `<td style="max-width:220px; font-size:12px;">${safe(r.listed_tools) || '—'}</td>` +
            `<td><a href="${safe(r.canonical_url)}" target="_blank" rel="noopener" class="link">open</a></td>` +
            `<td><button class="btn btn-small btn-blue" onclick="importEditorialRow(${idx}, this)">Import</button></td>` +
        '</tr>';
    }).join('');
}

async function importEditorialRow(idx, btn) {
    const row = editorialResults[idx];
    if (!row) return;
    btn.disabled = true;
    btn.textContent = 'Importing…';
    try {
        const data = await api('editorial_import', { method: 'POST', body: {
            canonical_url: row.canonical_url,
            outlet_name: row.outlet_name,
            author_name: row.author_name,
            email: row.email,
            business_summary: row.business_summary
        } });
        if (data.success) {
            editorialResults = editorialResults.filter(r => r.canonical_url !== row.canonical_url);
            if (editorialLastSummary) editorialLastSummary.already_imported_count = (editorialLastSummary.already_imported_count || 0) + 1;
            renderEditorialResults();
            loadStats();
            loadEditorialLeads();
        } else {
            notify(data.message || 'Import failed');
            btn.disabled = false;
            btn.textContent = 'Import';
        }
    } catch (e) {
        notify(e.message);
        btn.disabled = false;
        btn.textContent = 'Import';
    }
}

async function importAllEditorialFits() {
    if (editorialResults.length === 0) return;
    if (!confirm(`Import ${editorialResults.length} article${editorialResults.length === 1 ? '' : 's'} as new editorial leads?`)) return;
    const toImport = editorialResults.map(r => ({
        canonical_url: r.canonical_url,
        outlet_name: r.outlet_name,
        author_name: r.author_name,
        email: r.email,
        business_summary: r.business_summary
    }));
    const btn = document.getElementById('editorialImportAllBtn');
    btn.disabled = true;
    btn.textContent = 'Importing…';
    const succeeded = new Set();
    let failed = 0;
    for (const item of toImport) {
        try {
            const data = await api('editorial_import', { method: 'POST', body: item });
            if (data.success) succeeded.add(item.canonical_url);
            else failed++;
        } catch (e) {
            failed++;
        }
    }
    editorialResults = editorialResults.filter(r => !succeeded.has(r.canonical_url));
    if (editorialLastSummary) editorialLastSummary.already_imported_count = (editorialLastSummary.already_imported_count || 0) + succeeded.size;
    renderEditorialResults();
    loadStats();
    loadEditorialLeads();
    notify(`Imported ${succeeded.size} lead${succeeded.size === 1 ? '' : 's'}` + (failed > 0 ? `, ${failed} failed` : ''));
}

// Add one specific article URL directly (bypasses SerpAPI discovery). Reads the
// page, scrapes an email, researches the listed tools, and imports it as a lead.
async function addEditorialUrl() {
    const input = document.getElementById('editorialUrl');
    const url = (input.value || '').trim();
    if (!url) { notify('Enter an article URL'); return; }
    const btn = document.getElementById('editorialAddBtn');
    btn.disabled = true;
    btn.textContent = 'Adding…';
    try {
        const data = await api('editorial_add_url', { method: 'POST', body: { url } });
        if (data.success) {
            input.value = '';
            const emailMsg = data.has_email ? `email ${data.email}` : 'no email found — add it on the lead';
            notify(`Added ${data.outlet || 'article'} (${emailMsg})`, 'success');
            loadEditorialLeads();
            loadStats();
        } else {
            notify(data.message || 'Could not add that URL');
        }
    } catch (e) {
        notify(e.message);
    } finally {
        btn.disabled = false;
        btn.textContent = 'Add';
    }
}

document.addEventListener('DOMContentLoaded', loadEditorialStatus);

// Editorial leads list (own tab, scoped to source=editorial_auto). Reuses the
// shared esc/formatStatus/formatDateTime helpers and the id-driven lead modal
// (openLeadDetail) and draft flow (quickGenerateDraft), so nothing else needs
// duplicating. Uses its own paginator + table class to avoid colliding with the
// Email leads table.
let editorialLeadsPaginator = null;

// ─── Editorial Leads: bulk select (distinct class/IDs from the Email tab so the
// two tables' selections never collide, since both live in the DOM at once) ───
function toggleEditorialLeadCheckboxes(master) {
    document.querySelectorAll('.ed-lead-check').forEach(cb => cb.checked = master.checked);
    updateEditorialBulkBar();
}

function updateEditorialBulkBar() {
    const checked = document.querySelectorAll('.ed-lead-check:checked');
    const bar = document.getElementById('edBulkActionsBar');
    const count = document.getElementById('edSelectedCount');
    const selectAll = document.getElementById('edLeadsSelectAll');
    const allBoxes = document.querySelectorAll('.ed-lead-check');

    if (bar) bar.style.display = checked.length ? 'flex' : 'none';
    if (count) count.textContent = checked.length;

    if (selectAll && allBoxes.length) {
        if (checked.length === 0) { selectAll.checked = false; selectAll.indeterminate = false; }
        else if (checked.length === allBoxes.length) { selectAll.checked = true; selectAll.indeterminate = false; }
        else { selectAll.checked = false; selectAll.indeterminate = true; }
    }

    const draftBtn = document.getElementById('edBtnDraftSelected');
    if (draftBtn) {
        const allDrafted = checked.length > 0 && Array.from(checked).every(cb => cb.dataset.hasDraft === '1');
        draftBtn.textContent = allDrafted ? 'Redraft Selected' : 'Draft Selected';
    }
}

function getSelectedEditorialLeadIds() {
    return Array.from(document.querySelectorAll('.ed-lead-check:checked')).map(cb => parseInt(cb.value));
}

function bulkGenerateEditorialDrafts() {
    return runBulkDrafts({
        ids: getSelectedEditorialLeadIds(),
        checkClass: '.ed-lead-check', checkIdPrefix: 'ed-lead-check-',
        progressId: 'edBulkDraftProgress', progressTextId: 'edBulkDraftProgressText',
        cancelBtnId: 'edBtnCancelDraft', draftBtnId: 'edBtnDraftSelected',
        onDone: updateEditorialBulkBar,
    });
}

function openEditorialBulkSend() {
    openBulkSendModal(getSelectedEditorialLeadIds());
}

async function bulkDeleteEditorialLeads() {
    const ids = getSelectedEditorialLeadIds();
    if (!ids.length) return;
    if (!confirm(`Delete ${ids.length} lead(s)? This cannot be undone.`)) return;

    let success = 0, fail = 0, lastError = '';
    for (const id of ids) {
        try {
            const result = await api('delete_lead', { method: 'POST', body: { id } });
            if (result.success) success++; else { fail++; lastError = result.message || 'Unknown error'; }
        } catch (err) { fail++; lastError = err.message; }
    }
    if (fail > 0) notify(`Deleted: ${success}, Failed: ${fail}. Last error: ${lastError}`, 'error');
    else notify(`Successfully deleted ${success} lead(s)`, 'success');
    loadEditorialLeads();
    loadStats();
}

function debounceLoadEditorialLeads() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(loadEditorialLeads, 300);
}

async function loadEditorialLeads() {
    const tbody = document.getElementById('editorialLeadsTableBody');
    if (!tbody) return;

    const params = { source: 'editorial_auto' };
    const searchEl = document.getElementById('edFilterSearch');
    const statusEl = document.getElementById('edFilterStatus');
    const responseEl = document.getElementById('edFilterResponse');
    const sortEl = document.getElementById('edFilterSort');
    if (searchEl && searchEl.value.trim()) params.search = searchEl.value.trim();
    if (statusEl && statusEl.value) params.status = statusEl.value;
    if (responseEl && responseEl.value) params.response_status = responseEl.value;
    params.sort = (sortEl && sortEl.value) ? sortEl.value : 'date_added_desc';

    try {
        const data = await api('get_leads', { params });
        if (!data.success || !data.leads.length) {
            tbody.innerHTML = '<tr><td colspan="8" class="empty-state">No editorial leads found.</td></tr>';
            if (editorialLeadsPaginator) editorialLeadsPaginator.reset();
            const selectAll = document.getElementById('edLeadsSelectAll');
            if (selectAll) selectAll.checked = false;
            updateEditorialBulkBar();
            return;
        }
        tbody.innerHTML = data.leads.map(lead => `
            <tr class="lead-row">
                <td class="checkbox-column" onclick="event.stopPropagation()">
                    <div class="checkbox"><input type="checkbox" class="ed-lead-check" value="${lead.id}" id="ed-lead-check-${lead.id}" data-has-draft="${lead.draft_subject ? '1' : ''}" onchange="updateEditorialBulkBar()"><label for="ed-lead-check-${lead.id}"></label></div>
                </td>
                <td>
                    <strong>${esc(lead.business_name)}</strong>
                    ${lead.contact_name ? '<br><small>' + esc(lead.contact_name) + '</small>' : ''}
                </td>
                <td>${lead.website ? '<a class="website-link" href="' + esc(lead.website) + '" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()">' + esc(lead.website.replace(/^https?:\/\//, '').replace(/\?.*$/, '')) + '</a>' : '<span class="text-muted">—</span>'}</td>
                <td>${lead.email ? esc(lead.email) : '<span class="text-muted">—</span>'}</td>
                <td><span class="badge badge-status-${lead.status || 'new'}">${formatStatus(lead.status || 'new')}</span></td>
                <td>${lead.sent_at ? formatDateTime(lead.sent_at) : '<span class="text-muted">—</span>'}</td>
                <td>${lead.clicked_at ? formatDateTime(lead.clicked_at) : '<span class="text-muted">—</span>'}</td>
                <td onclick="event.stopPropagation()">
                    <div class="actions-cell">
                        <button class="btn btn-small btn-blue" onclick="openLeadDetail(${lead.id})" title="View">View</button>
                        ${!lead.draft_subject && !['contacted','replied','interested','not_interested','onboarded'].includes(lead.status) ? `<button class="btn btn-small btn-blue" onclick="quickGenerateDraft(${lead.id}, this)" title="Generate Draft">Draft</button>` : ''}
                    </div>
                </td>
            </tr>
        `).join('');

        const selectAll = document.getElementById('edLeadsSelectAll');
        if (selectAll) selectAll.checked = false;
        updateEditorialBulkBar();

        const table = document.querySelector('.editorial-leads-table');
        if (table) {
            if (!editorialLeadsPaginator) editorialLeadsPaginator = new TablePaginator(table, { perPage: 25 });
            else editorialLeadsPaginator.reset();
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

// Load the editorial leads list when its section-tab is opened, and on initial
// load if that tab is already active.
document.querySelectorAll('.section-tab[data-tab="editorial-leads"]').forEach(btn =>
    btn.addEventListener('click', () => setTimeout(loadEditorialLeads, 0)));
document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('#editorial-leads.tab-content.active')) loadEditorialLeads();
});

// ═══════════════════════════════════════════════════════════════════════════
// Creators / affiliate-partner outreach channel
// Mirrors the editorial channel (discovery + leads with filters/bulk), scoped to
// source=creator_auto, reusing the shared runBulkDrafts()/openBulkSendModal() and
// the id-driven lead modal. Distinct classes/IDs (.cr-lead-check, cr*) so its
// selection never collides with the other leads tables in the DOM.
// ═══════════════════════════════════════════════════════════════════════════

let creatorResults = [];
let creatorLastSummary = null;
let creatorLeadsPaginator = null;

async function loadCreatorStatus() {
    try {
        const data = await api('creator_get_status');
        if (data.success) {
            const usage = document.getElementById('creatorSerpUsage');
            if (usage) usage.textContent = `${data.serpapi_calls_today}/${data.serpapi_limit} queries`;
            const hunter = document.getElementById('creatorHunterState');
            if (hunter) hunter.textContent = data.has_hunter ? 'connected' : 'not set (scrapes linked sites instead)';
        }
    } catch (e) { /* non-fatal */ }
}

async function runCreatorDiscovery() {
    const limit = Math.min(30, Math.max(1, parseInt(document.getElementById('creatorLimit').value, 10) || 8));
    const btn = document.getElementById('creatorRunBtn');
    btn.disabled = true;
    btn.textContent = 'Searching…';
    try {
        const data = await api('creator_run_discovery', { method: 'POST', body: { limit } });
        if (!data.success) {
            notify(data.message || 'Creator discovery failed');
            return;
        }
        creatorResults = data.results || [];
        creatorLastSummary = {
            rejected_count: data.rejected_count || 0,
            reject_reasons: data.reject_reasons || {},
            already_imported_count: data.already_imported_count || 0,
            already_rejected_count: data.already_rejected_count || 0,
            total_evaluated: data.total_evaluated || 0,
            requested_limit: data.requested_limit || limit,
            quota_exhausted: !!data.quota_exhausted,
            stop_reason: data.stop_reason || '',
        };
        const usage = document.getElementById('creatorSerpUsage');
        if (usage) usage.textContent = `${data.serpapi_calls_today}/${data.serpapi_limit} queries`;
        document.getElementById('creatorResults').style.display = 'block';
        renderCreatorResults();
        if (creatorLastSummary.quota_exhausted) {
            notify(`SerpAPI daily quota hit before finding ${creatorLastSummary.requested_limit}. Got ${creatorResults.length} fit${creatorResults.length === 1 ? '' : 's'}. Resets at midnight or raise SERPAPI_DAILY_QUERY_LIMIT in .env.`);
        }
    } catch (e) {
        notify(e.message);
    } finally {
        btn.disabled = false;
        btn.textContent = 'Run';
    }
}

function renderCreatorResults() {
    const body = document.getElementById('creatorResultsBody');
    const countEl = document.getElementById('creatorResultsCount');
    const importAllBtn = document.getElementById('creatorImportAllBtn');
    const safe = (s) => escapeHtml(String(s ?? ''));

    let summary = `${creatorResults.length} fit`;
    if (creatorLastSummary) {
        if (creatorLastSummary.already_imported_count > 0) summary += ` · ${creatorLastSummary.already_imported_count} already imported (hidden)`;
        if (creatorLastSummary.already_rejected_count > 0) summary += ` · ${creatorLastSummary.already_rejected_count} previously skipped (hidden)`;
        if (creatorLastSummary.rejected_count > 0) {
            const reasons = creatorLastSummary.reject_reasons || {};
            const reasonStr = Object.entries(reasons).sort((a, b) => b[1] - a[1]).slice(0, 3).map(([k, n]) => `${n}× ${k}`).join(', ');
            summary += ` · ${creatorLastSummary.rejected_count} skipped`;
            if (reasonStr) summary += ` (${reasonStr})`;
        }
    }
    countEl.textContent = summary;

    if (importAllBtn) {
        importAllBtn.disabled = creatorResults.length === 0;
        importAllBtn.textContent = creatorResults.length > 0 ? `Import ${creatorResults.length} Fit${creatorResults.length !== 1 ? 's' : ''}` : 'Import All Fits';
    }

    if (creatorResults.length === 0) {
        const n = (creatorLastSummary && creatorLastSummary.total_evaluated) || 0;
        body.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:24px; color:#888;">No new creators to recruit (evaluated ${n}). They were not a fit for the audience, or already imported.</td></tr>`;
        return;
    }

    body.innerHTML = creatorResults.map((r, idx) => {
        const name = `<div><strong>${safe(r.creator_name) || safe(r.canonical_url)}</strong></div>`;
        const emailSrc = r.email_source ? ` <span style="color:#888; font-size:11px;">(${safe(r.email_source)})</span>` : '';
        const emailCell = r.email ? (safe(r.email) + emailSrc) : '<span style="color:#c00; font-size:12px;">manual</span>';
        return '<tr>' +
            `<td>${name}</td>` +
            `<td><span class="badge">${safe(r.platform) || '—'}</span></td>` +
            `<td style="max-width:200px; font-size:12px;">${safe(r.audience) || '—'}</td>` +
            `<td>${emailCell}</td>` +
            `<td><a href="${safe(r.profile_url || r.canonical_url)}" target="_blank" rel="noopener" class="link">open</a></td>` +
            `<td><button class="btn btn-small btn-blue" onclick="importCreatorRow(${idx}, this)">Import</button></td>` +
        '</tr>';
    }).join('');
}

async function importCreatorRow(idx, btn) {
    const row = creatorResults[idx];
    if (!row) return;
    btn.disabled = true;
    btn.textContent = 'Importing…';
    try {
        const data = await api('creator_import', { method: 'POST', body: {
            canonical_url: row.canonical_url,
            profile_url: row.profile_url,
            platform: row.platform,
            creator_name: row.creator_name,
            email: row.email,
            business_summary: row.business_summary
        } });
        if (data.success) {
            creatorResults = creatorResults.filter(r => r.canonical_url !== row.canonical_url);
            if (creatorLastSummary) creatorLastSummary.already_imported_count = (creatorLastSummary.already_imported_count || 0) + 1;
            renderCreatorResults();
            loadStats();
            loadCreatorLeads();
        } else {
            notify(data.message || 'Import failed');
            btn.disabled = false;
            btn.textContent = 'Import';
        }
    } catch (e) {
        notify(e.message);
        btn.disabled = false;
        btn.textContent = 'Import';
    }
}

async function importAllCreatorFits() {
    if (creatorResults.length === 0) return;
    if (!confirm(`Import ${creatorResults.length} creator${creatorResults.length === 1 ? '' : 's'} as new partner leads?`)) return;
    const toImport = creatorResults.map(r => ({
        canonical_url: r.canonical_url,
        profile_url: r.profile_url,
        platform: r.platform,
        creator_name: r.creator_name,
        email: r.email,
        business_summary: r.business_summary
    }));
    const btn = document.getElementById('creatorImportAllBtn');
    btn.disabled = true;
    btn.textContent = 'Importing…';
    const succeeded = new Set();
    let failed = 0;
    for (const item of toImport) {
        try {
            const data = await api('creator_import', { method: 'POST', body: item });
            if (data.success) succeeded.add(item.canonical_url);
            else failed++;
        } catch (e) {
            failed++;
        }
    }
    creatorResults = creatorResults.filter(r => !succeeded.has(r.canonical_url));
    if (creatorLastSummary) creatorLastSummary.already_imported_count = (creatorLastSummary.already_imported_count || 0) + succeeded.size;
    renderCreatorResults();
    loadStats();
    loadCreatorLeads();
    notify(`Imported ${succeeded.size} lead${succeeded.size === 1 ? '' : 's'}` + (failed > 0 ? `, ${failed} failed` : ''));
}

// Add one specific creator URL directly (YouTube channel, newsletter, blog, or a
// LinkedIn profile which imports as a manual, email-less lead).
async function addCreatorUrl() {
    const input = document.getElementById('creatorUrl');
    const url = (input.value || '').trim();
    if (!url) { notify('Enter a creator URL'); return; }
    const btn = document.getElementById('creatorAddBtn');
    btn.disabled = true;
    btn.textContent = 'Adding…';
    try {
        const data = await api('creator_add_url', { method: 'POST', body: { url } });
        if (data.success) {
            input.value = '';
            const emailMsg = data.has_email ? `email ${data.email}` : 'no email found — add it on the lead or use assisted email';
            notify(`Added ${data.name || 'creator'} (${data.platform || ''}, ${emailMsg})`, 'success');
            loadCreatorLeads();
            loadStats();
        } else {
            notify(data.message || 'Could not add that URL');
        }
    } catch (e) {
        notify(e.message);
    } finally {
        btn.disabled = false;
        btn.textContent = 'Add';
    }
}

// ─── Creator Leads: bulk select (distinct .cr-lead-check class / cr* IDs) ───
function toggleCreatorLeadCheckboxes(master) {
    document.querySelectorAll('.cr-lead-check').forEach(cb => cb.checked = master.checked);
    updateCreatorBulkBar();
}

function updateCreatorBulkBar() {
    const checked = document.querySelectorAll('.cr-lead-check:checked');
    const bar = document.getElementById('crBulkActionsBar');
    const count = document.getElementById('crSelectedCount');
    const selectAll = document.getElementById('crLeadsSelectAll');
    const allBoxes = document.querySelectorAll('.cr-lead-check');

    if (bar) bar.style.display = checked.length ? 'flex' : 'none';
    if (count) count.textContent = checked.length;

    if (selectAll && allBoxes.length) {
        if (checked.length === 0) { selectAll.checked = false; selectAll.indeterminate = false; }
        else if (checked.length === allBoxes.length) { selectAll.checked = true; selectAll.indeterminate = false; }
        else { selectAll.checked = false; selectAll.indeterminate = true; }
    }

    const draftBtn = document.getElementById('crBtnDraftSelected');
    if (draftBtn) {
        const allDrafted = checked.length > 0 && Array.from(checked).every(cb => cb.dataset.hasDraft === '1');
        draftBtn.textContent = allDrafted ? 'Redraft Selected' : 'Draft Selected';
    }
}

function getSelectedCreatorLeadIds() {
    return Array.from(document.querySelectorAll('.cr-lead-check:checked')).map(cb => parseInt(cb.value));
}

function bulkGenerateCreatorDrafts() {
    return runBulkDrafts({
        ids: getSelectedCreatorLeadIds(),
        checkClass: '.cr-lead-check', checkIdPrefix: 'cr-lead-check-',
        progressId: 'crBulkDraftProgress', progressTextId: 'crBulkDraftProgressText',
        cancelBtnId: 'crBtnCancelDraft', draftBtnId: 'crBtnDraftSelected',
        onDone: updateCreatorBulkBar,
    });
}

function openCreatorBulkSend() {
    openBulkSendModal(getSelectedCreatorLeadIds());
}

async function bulkDeleteCreatorLeads() {
    const ids = getSelectedCreatorLeadIds();
    if (!ids.length) return;
    if (!confirm(`Delete ${ids.length} lead(s)? This cannot be undone.`)) return;

    let success = 0, fail = 0, lastError = '';
    for (const id of ids) {
        try {
            const result = await api('delete_lead', { method: 'POST', body: { id } });
            if (result.success) success++; else { fail++; lastError = result.message || 'Unknown error'; }
        } catch (err) { fail++; lastError = err.message; }
    }
    if (fail > 0) notify(`Deleted: ${success}, Failed: ${fail}. Last error: ${lastError}`, 'error');
    else notify(`Successfully deleted ${success} lead(s)`, 'success');
    loadCreatorLeads();
    loadStats();
}

// Grab a creator's email when none was auto-found: open the page where the email
// lives (the channel About page for YouTube) in a new tab, then paste it back
// onto the lead. Beats a command-line tool for a handful of leads.
// Holds the button + lead being resolved while the paste-email modal is open.
// We use an in-page modal rather than a native prompt() because getCreatorEmail
// opens the channel in a foreground tab, and browsers suppress prompt()/alert()
// dialogs fired from the now-backgrounded admin tab (the box never appeared).
let creatorEmailBtn = null;
let creatorEmailLeadId = null;

function getCreatorEmail(btn) {
    const leadId = parseInt(btn.dataset.leadId, 10);
    let target = btn.dataset.website || '';
    if (/youtube\.com/i.test(target)) {
        target = target.replace(/\/+$/, '');
        if (!/\/about$/i.test(target)) target += '/about';
    }
    if (target) window.open(target, '_blank', 'noopener');

    creatorEmailBtn = btn;
    creatorEmailLeadId = leadId;
    const input = document.getElementById('creatorEmailInput');
    if (input) input.value = '';
    showModal('creatorEmailModal');
    if (input) setTimeout(() => input.focus(), 50);
}

async function saveCreatorEmail() {
    const input = document.getElementById('creatorEmailInput');
    const email = (input ? input.value : '').trim();
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
        notify('That does not look like an email address', 'error');
        return;
    }
    const btn = creatorEmailBtn;
    const leadId = creatorEmailLeadId;
    closeModal('creatorEmailModal');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Saving…';
    }
    try {
        const res = await api('creator_set_email', { method: 'POST', body: { lead_id: leadId, email } });
        if (res.success && res.updated > 0) {
            notify('Email saved', 'success');
            loadCreatorLeads();
        } else {
            notify(res.message || 'Could not save the email', 'error');
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Get email';
            }
        }
    } catch (e) {
        notify(e.message, 'error');
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Get email';
        }
    }
}

function debounceLoadCreatorLeads() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(loadCreatorLeads, 300);
}

async function loadCreatorLeads() {
    const tbody = document.getElementById('creatorLeadsTableBody');
    if (!tbody) return;

    const params = { source: 'creator_auto' };
    const searchEl = document.getElementById('crFilterSearch');
    const statusEl = document.getElementById('crFilterStatus');
    const responseEl = document.getElementById('crFilterResponse');
    const sortEl = document.getElementById('crFilterSort');
    if (searchEl && searchEl.value.trim()) params.search = searchEl.value.trim();
    if (statusEl && statusEl.value) params.status = statusEl.value;
    if (responseEl && responseEl.value) params.response_status = responseEl.value;
    params.sort = (sortEl && sortEl.value) ? sortEl.value : 'date_added_desc';

    try {
        const data = await api('get_leads', { params });
        if (!data.success || !data.leads.length) {
            tbody.innerHTML = '<tr><td colspan="8" class="empty-state">No partner leads found.</td></tr>';
            if (creatorLeadsPaginator) creatorLeadsPaginator.reset();
            const selectAll = document.getElementById('crLeadsSelectAll');
            if (selectAll) selectAll.checked = false;
            updateCreatorBulkBar();
            return;
        }
        tbody.innerHTML = data.leads.map(lead => {
            const platform = (lead.category || '').replace(/^creator:/, '') || '—';
            return `
            <tr class="lead-row">
                <td class="checkbox-column" onclick="event.stopPropagation()">
                    <div class="checkbox"><input type="checkbox" class="cr-lead-check" value="${lead.id}" id="cr-lead-check-${lead.id}" data-has-draft="${lead.draft_subject ? '1' : ''}" onchange="updateCreatorBulkBar()"><label for="cr-lead-check-${lead.id}"></label></div>
                </td>
                <td>
                    <strong>${esc(lead.business_name)}</strong>
                    ${lead.website ? '<br><a class="website-link" href="' + esc(lead.website) + '" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()">' + esc(lead.website.replace(/^https?:\/\//, '').replace(/\?.*$/, '')) + '</a>' : ''}
                </td>
                <td><span class="badge">${esc(platform)}</span></td>
                <td>${lead.email ? esc(lead.email) : '<span class="text-muted">—</span>'}</td>
                <td><span class="badge badge-status-${lead.status || 'new'}">${formatStatus(lead.status || 'new')}</span></td>
                <td>${lead.sent_at ? formatDateTime(lead.sent_at) : '<span class="text-muted">—</span>'}</td>
                <td>${lead.clicked_at ? formatDateTime(lead.clicked_at) : '<span class="text-muted">—</span>'}</td>
                <td onclick="event.stopPropagation()">
                    <div class="actions-cell">
                        <button class="btn btn-small btn-blue" onclick="openLeadDetail(${lead.id})" title="View">View</button>
                        ${!lead.email
                            ? `<button class="btn btn-small btn-blue" data-lead-id="${lead.id}" data-website="${escapeHtml(lead.website || '')}" onclick="getCreatorEmail(this)" title="Open the channel and paste the email">Get email</button>`
                            : (!lead.draft_subject && !['contacted','replied','interested','not_interested','onboarded'].includes(lead.status)
                                ? `<button class="btn btn-small btn-blue" onclick="quickGenerateDraft(${lead.id}, this)" title="Generate Draft">Draft</button>`
                                : '')}
                    </div>
                </td>
            </tr>`;
        }).join('');

        const selectAll = document.getElementById('crLeadsSelectAll');
        if (selectAll) selectAll.checked = false;
        updateCreatorBulkBar();

        const table = document.querySelector('.creator-leads-table');
        if (table) {
            if (!creatorLeadsPaginator) creatorLeadsPaginator = new TablePaginator(table, { perPage: 25 });
            else creatorLeadsPaginator.reset();
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

document.querySelectorAll('.section-tab[data-tab="creator-leads"]').forEach(btn =>
    btn.addEventListener('click', () => setTimeout(loadCreatorLeads, 0)));
document.addEventListener('DOMContentLoaded', function () {
    loadCreatorStatus();
    if (document.querySelector('#creator-leads.tab-content.active')) loadCreatorLeads();
});

// ═══════════════════════════════════════════════════════════════════════════
// Reddit outreach
// ═══════════════════════════════════════════════════════════════════════════

let redditCurrentThread = null; // {id, ...} when the detail modal is open

// ─── Init ───
document.addEventListener('DOMContentLoaded', function () {
    // Channel-tab switching (Email | Reddit)
    document.querySelectorAll('.channel-tab[data-channel]').forEach(btn => {
        btn.addEventListener('click', function () {
            const channel = btn.dataset.channel;
            switchChannel(channel);
        });
    });

    // Update safety meter bar colors from data-attributes set server-side
    updateRedditSafetyMeterColors();

    // If Reddit channel is active on initial load, kick off thread + stats loads
    if (document.querySelector('.channel-pane[data-channel-pane="reddit"].active')) {
        loadRedditThreads();
        loadRedditStats();
        startRedditProgressPolling();
    }
});

function switchChannel(channel) {
    document.querySelectorAll('.channel-tab').forEach(b => {
        b.classList.toggle('active', b.dataset.channel === channel);
    });
    document.querySelectorAll('.channel-pane').forEach(p => {
        p.classList.toggle('active', p.dataset.channelPane === channel);
    });

    // Sync URL
    try {
        const url = new URL(window.location.href);
        url.searchParams.set('channel', channel);
        if (channel === 'email' && /^(reddit|editorial|creator)-/.test(url.searchParams.get('tab') || '')) {
            url.searchParams.set('tab', 'leads');
        }
        if (channel === 'reddit' && !['reddit-threads', 'reddit-settings'].includes(url.searchParams.get('tab') || '')) {
            url.searchParams.set('tab', 'reddit-threads');
        }
        if (channel === 'editorial') {
            url.searchParams.set('tab', 'editorial-discovery');
        }
        if (channel === 'creator') {
            url.searchParams.set('tab', 'creator-discovery');
        }
        history.replaceState({}, '', url.toString());
    } catch (e) { /* ignore */ }

    // Re-activate the right sub-tab inside the newly-shown channel pane
    const targetPane = document.querySelector(`.channel-pane[data-channel-pane="${channel}"]`);
    if (targetPane) {
        const urlTab = new URLSearchParams(window.location.search).get('tab') || '';
        const explicit = urlTab ? targetPane.querySelector(`.section-tab[data-tab="${urlTab}"]`) : null;
        const fallback = targetPane.querySelector('.section-tab.active') || targetPane.querySelector('.section-tab');
        const sectionTab = explicit || fallback;
        if (sectionTab && !sectionTab.classList.contains('active')) sectionTab.click();
    }

    if (channel === 'reddit') {
        loadRedditThreads();
        loadRedditStats();
        startRedditProgressPolling();
    }
    if (channel === 'editorial') {
        loadEditorialStatus();
    }
    if (channel === 'creator') {
        loadCreatorStatus();
    }
}

// ─── Safety meter color ───

function updateRedditSafetyMeterColors() {
    ['redditDailyFill', 'redditWeeklyFill'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const used = parseInt(el.dataset.used || '0', 10);
        const limit = parseInt(el.dataset.limit || '0', 10);
        const pct = limit > 0 ? (used / limit) * 100 : 0;
        el.style.width = Math.min(100, pct) + '%';
        el.classList.remove('safety-green', 'safety-yellow', 'safety-red');
        if (pct >= 100) el.classList.add('safety-red');
        else if (pct >= 75) el.classList.add('safety-yellow');
        else el.classList.add('safety-green');
    });
}

// ─── Threads list ───

async function loadRedditThreads() {
    const tbody = document.getElementById('redditThreadsTableBody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="8" class="empty-state">Loading…</td></tr>';

    const status = document.getElementById('redditFilterStatus')?.value || 'actionable';
    const subreddit = document.getElementById('redditFilterSubreddit')?.value || '';
    const source = document.getElementById('redditFilterSource')?.value || '';
    const days = document.getElementById('redditFilterDays')?.value || '30';

    const data = await api('reddit_get_threads', { params: { status, subreddit, source, days } });
    if (!data || !data.success) {
        tbody.innerHTML = '<tr><td colspan="8" class="empty-state">Failed to load threads.</td></tr>';
        return;
    }

    if (!data.threads || data.threads.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="empty-state">No threads match this filter.</td></tr>';
        return;
    }

    tbody.innerHTML = data.threads.map(t => renderRedditThreadRow(t)).join('');
}

function renderRedditThreadRow(t) {
    const ai = t.ai_relevance === null ? '<span class="text-muted">—</span>' : `<span class="reddit-ai-score reddit-ai-${aiBucket(t.ai_relevance)}">${t.ai_relevance}/10</span>`;
    const statusBadge = redditStatusBadge(t.status);
    const replyBadge = t.reply_status ? redditReplyStatusBadge(t.reply_status) : '<span class="text-muted">—</span>';
    const upvotes = t.reply_upvotes !== null && t.reply_upvotes !== undefined ? t.reply_upvotes : '—';
    const discovered = t.discovered_at ? new Date(t.discovered_at.replace(' ', 'T')).toLocaleString() : '';
    const title = (t.title || '').length > 100 ? t.title.slice(0, 100) + '…' : (t.title || '');

    return `<tr onclick="openRedditThreadDetail(${t.id})">
        <td>r/${escapeHtml(t.subreddit)}</td>
        <td class="reddit-title-cell">${escapeHtml(title)}</td>
        <td>${ai}</td>
        <td>${statusBadge}</td>
        <td>${replyBadge}</td>
        <td>${upvotes}</td>
        <td class="text-muted" style="font-size:12px;">${escapeHtml(discovered)}</td>
        <td class="row-actions" onclick="event.stopPropagation();">
            <a href="${escapeAttr(t.url)}" target="_blank" rel="noopener noreferrer" class="btn btn-small btn-neutral">View</a>
        </td>
    </tr>`;
}

function aiBucket(n) {
    if (n >= 8) return 'high';
    if (n >= 6) return 'mid';
    return 'low';
}

function redditStatusBadge(s) {
    const map = {
        drafted: ['badge-green', 'Drafted'],
        drafted_pending: ['badge-yellow', 'Drafted-pending'],
        replied: ['badge-blue', 'Replied'],
        skipped: ['badge-gray', 'Skipped'],
        expired: ['badge-gray', 'Expired'],
        new: ['badge-yellow', 'New'],
    };
    const [cls, label] = map[s] || ['badge-gray', s];
    return `<span class="badge ${cls}">${escapeHtml(label)}</span>`;
}

function redditReplyStatusBadge(s) {
    const map = {
        pending: ['badge-yellow', 'Checking…'],
        live: ['badge-green', 'Live'],
        removed: ['badge-red', 'Removed'],
        removed_or_shadowbanned: ['badge-red', 'Removed/Shadow'],
        deleted_by_user: ['badge-gray', 'You deleted'],
    };
    const [cls, label] = map[s] || ['badge-gray', s];
    return `<span class="badge ${cls}">${escapeHtml(label)}</span>`;
}

// ─── Stats ───

async function loadRedditStats() {
    const data = await api('reddit_get_stats');
    if (!data || !data.success) return;
    const s = data.stats;

    setText('redditStatTotal', s.total);
    setText('redditStatDrafted', s.drafted);
    setText('redditStatPending', s.drafted_pending);
    setText('redditStatReplied7d', s.replied_7d);
    setText('redditStatSurvival', s.survival_pct === null ? '—' : `${s.survival_pct}% (n=${s.survival_n})`);
    setText('redditStatAvgUpvotes', s.avg_upvotes === null ? '—' : s.avg_upvotes);
    setText('redditStatProfileClicks', s.profile_clicks_30d);

    // Safety meter
    const dailyFill = document.getElementById('redditDailyFill');
    const weeklyFill = document.getElementById('redditWeeklyFill');
    if (dailyFill) {
        dailyFill.dataset.used = s.daily_used;
        dailyFill.dataset.limit = s.daily_limit;
    }
    if (weeklyFill) {
        weeklyFill.dataset.used = s.weekly_used;
        weeklyFill.dataset.limit = s.weekly_limit;
    }
    setText('redditDailyUsed', s.daily_used);
    setText('redditDailyLimit', s.daily_limit);
    setText('redditWeeklyUsed', s.weekly_used);
    setText('redditWeeklyLimit', s.weekly_limit);
    updateRedditSafetyMeterColors();
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el && value !== undefined && value !== null) el.textContent = String(value);
}

// ─── Pipeline status ───

let redditProgressPollTimer = null;
let redditDiscoveryWasRunning = false;

async function loadRedditPipelineProgress() {
    const data = await api('reddit_pipeline_progress');
    if (!data || !data.success) return;

    const banner = document.getElementById('redditPipelineBanner');
    const btn = document.getElementById('redditRunNowBtn');
    const progress = data.progress || {};
    const running = !!data.running;

    if (banner) {
        if (running) {
            const msg = progress.message || 'Reddit discovery running…';
            const found = progress.found ?? 0;
            const drafted = progress.drafted ?? 0;
            const startedAt = progress.started_at ? new Date(progress.started_at.replace(' ', 'T')) : null;
            const elapsed = startedAt ? Math.max(0, Math.floor((Date.now() - startedAt.getTime()) / 1000)) : null;
            const elapsedStr = elapsed === null ? '' : ` · ${elapsed}s elapsed`;
            const counts = (found || drafted)
                ? `<span class="reddit-progress-counts">${found} found · ${drafted} drafted${elapsedStr}</span>`
                : `<span class="reddit-progress-counts">${elapsedStr.replace(/^ · /, '')}</span>`;
            banner.innerHTML = `<span class="bulk-draft-spinner"></span> <span class="reddit-progress-msg">${escapeHtml(msg)}</span> ${counts}`;
            banner.style.display = 'flex';
        } else if (progress.completed && redditDiscoveryWasRunning) {
            // Just finished. Show the summary briefly, then refresh and hide.
            const msg = progress.error
                ? `Discovery failed: ${progress.error}`
                : (progress.message || `Discovery complete. Found ${progress.found ?? 0}, drafted ${progress.drafted ?? 0}.`);
            banner.innerHTML = `<span class="reddit-progress-msg">${escapeHtml(msg)}</span>`;
            banner.style.display = 'flex';
            banner.classList.add(progress.error ? 'banner-error' : 'banner-success');
            setTimeout(() => {
                banner.style.display = 'none';
                banner.classList.remove('banner-success', 'banner-error');
            }, 6000);
        } else {
            banner.style.display = 'none';
        }
    }

    if (btn) btn.disabled = running;

    // Stop polling and refresh data when the run ends
    if (!running && redditProgressPollTimer) {
        clearInterval(redditProgressPollTimer);
        redditProgressPollTimer = null;
        if (redditDiscoveryWasRunning) {
            loadRedditThreads();
            loadRedditStats();
        }
    }
    redditDiscoveryWasRunning = running;
}

function startRedditProgressPolling() {
    if (redditProgressPollTimer) return;
    loadRedditPipelineProgress(); // immediate first poll
    redditProgressPollTimer = setInterval(loadRedditPipelineProgress, 2000);
}

async function runRedditDiscoveryNow() {
    if (!confirm('Trigger the Reddit discovery cron now? It will run in the background and pull fresh threads.')) return;
    const data = await api('reddit_run_now', { method: 'POST' });
    if (!data || !data.success) {
        notify(data?.message || 'Failed to start cron');
        return;
    }
    startRedditProgressPolling();
}

// ─── Manually add a thread ───

function showAddRedditThreadModal() {
    document.getElementById('addRedditUrl').value = '';
    document.getElementById('addRedditSubreddit').value = '';
    document.getElementById('addRedditTitle').value = '';
    document.getElementById('addRedditBody').value = '';
    showModal('addRedditThreadModal');
}

async function addRedditThread() {
    const url = document.getElementById('addRedditUrl').value.trim();
    if (!url) { notify('Reddit post URL is required', 'error'); return; }

    const data = {
        url,
        subreddit: document.getElementById('addRedditSubreddit').value.trim(),
        title: document.getElementById('addRedditTitle').value.trim(),
        body: document.getElementById('addRedditBody').value.trim(),
    };

    try {
        const result = await api('reddit_add_thread', { method: 'POST', body: data });
        if (result.success) {
            closeModal('addRedditThreadModal');
            notify('Thread added to the queue', 'success');
            loadRedditThreads();
            loadRedditStats();
        } else {
            notify(result.message, 'error');
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

// ─── Thread detail modal ───

async function openRedditThreadDetail(id) {
    const data = await api('reddit_get_thread', { params: { id } });
    if (!data || !data.success) {
        notify(data?.message || 'Failed to load thread');
        return;
    }
    redditCurrentThread = data.thread;
    fillRedditThreadModal(data.thread);
    showModal('redditThreadModal');
}

function fillRedditThreadModal(t) {
    document.getElementById('redditThreadTitle').textContent = t.title || '(no title)';
    document.getElementById('redditThreadSubreddit').textContent = 'r/' + (t.subreddit || '');
    document.getElementById('redditThreadAi').textContent = t.ai_relevance === null ? '—' : `${t.ai_relevance}/10`;
    document.getElementById('redditThreadStatus').textContent = t.status || '';
    document.getElementById('redditThreadPosted').textContent = t.posted_at || '';
    document.getElementById('redditThreadUrl').href = t.url || '#';
    document.getElementById('redditThreadBody').textContent = t.body || '(link post, no self-text)';
    document.getElementById('redditThreadReason').textContent = t.ai_relevance_reason || '(no AI reasoning recorded)';
    document.getElementById('redditDraftBody').value = t.draft_body || '';

    // Show "Generate draft" only for drafted_pending
    document.getElementById('redditDraftGenerateBtn').style.display = t.status === 'drafted_pending' ? '' : 'none';

    // Reply status section
    const replySection = document.getElementById('redditReplyStatusSection');
    const replyBody = document.getElementById('redditReplyStatusBody');
    if (t.status === 'replied') {
        replySection.style.display = '';
        const upvotes = (t.reply_upvotes !== null && t.reply_upvotes !== undefined) ? t.reply_upvotes : '—';
        const replies = (t.reply_replies_count !== null && t.reply_replies_count !== undefined) ? t.reply_replies_count : '—';
        replyBody.innerHTML =
            `Reply status: ${redditReplyStatusBadge(t.reply_status || 'pending')} · ` +
            `Upvotes: ${upvotes} · Replies: ${replies} · ` +
            `<a href="${escapeAttr(t.reply_permalink || '#')}" target="_blank" rel="noopener noreferrer">Open your reply ↗</a>`;
    } else {
        replySection.style.display = 'none';
    }
}

async function saveRedditDraft() {
    if (!redditCurrentThread) return;
    const body = document.getElementById('redditDraftBody').value.trim();
    if (!body) { notify('Draft cannot be empty'); return; }
    const data = await api('reddit_save_draft', { method: 'POST', body: { id: redditCurrentThread.id, draft_body: body } });
    if (!data || !data.success) { notify(data?.message || 'Save failed'); return; }
}

function openRedditRegenerateFeedback() {
    if (!redditCurrentThread) return;
    const panel = document.getElementById('redditRegenerateFeedback');
    const ta = document.getElementById('redditRegenerateFeedbackText');
    if (!panel || !ta) return;
    ta.value = '';
    panel.style.display = 'block';
    ta.focus();
}

function cancelRedditRegenerate() {
    const panel = document.getElementById('redditRegenerateFeedback');
    if (panel) panel.style.display = 'none';
}

async function submitRedditRegenerate(btn) {
    if (!redditCurrentThread) return;
    const feedback = (document.getElementById('redditRegenerateFeedbackText')?.value || '').trim();
    if (btn) { btn.disabled = true; btn.textContent = 'Regenerating…'; }
    const data = await api('reddit_regenerate_draft', {
        method: 'POST',
        body: { id: redditCurrentThread.id, feedback },
    });
    if (btn) { btn.disabled = false; btn.textContent = 'Regenerate'; }
    if (!data || !data.success) { notify(data?.message || 'Regeneration failed'); return; }
    document.getElementById('redditDraftBody').value = data.draft_body;
    cancelRedditRegenerate();
}

async function generatePendingRedditDraft() {
    if (!redditCurrentThread) return;
    const btn = document.getElementById('redditDraftGenerateBtn');
    if (btn) { btn.disabled = true; btn.textContent = 'Generating…'; }
    const data = await api('reddit_generate_pending_draft', { method: 'POST', body: { id: redditCurrentThread.id } });
    if (btn) { btn.disabled = false; btn.textContent = 'Generate draft'; }
    if (!data || !data.success) { notify(data?.message || 'Generation failed'); return; }
    document.getElementById('redditDraftBody').value = data.draft_body;
    btn.style.display = 'none';
    loadRedditThreads();
}

function copyRedditDraft(btn) {
    const body = document.getElementById('redditDraftBody').value;
    if (!body) return;

    const copied = () => {
        const original = btn.textContent;
        btn.textContent = 'Copied';
        setTimeout(() => btn.textContent = original, 1000);
    };

    navigator.clipboard.writeText(body).then(copied).catch(() => {
        // Fallback for browsers without clipboard API (or insecure contexts)
        const ta = document.createElement('textarea');
        ta.value = body;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        copied();
    });
}

async function markRedditSkipped() {
    if (!redditCurrentThread) return;
    const data = await api('reddit_mark_skipped', { method: 'POST', body: { id: redditCurrentThread.id } });
    if (!data || !data.success) { notify('Failed'); return; }
    closeModal('redditThreadModal');
    loadRedditThreads();
    loadRedditStats();
}

// ─── Mark Replied flow ───

function openMarkRedditRepliedModal() {
    if (!redditCurrentThread) return;
    document.getElementById('redditReplyPermalink').value = '';
    document.getElementById('redditMentionedProduct').checked = true;
    showModal('redditMarkRepliedModal');
}

async function confirmMarkRedditReplied() {
    if (!redditCurrentThread) return;
    const permalink = document.getElementById('redditReplyPermalink').value.trim();
    const mentioned = document.getElementById('redditMentionedProduct').checked ? 1 : 0;

    if (!permalink) { notify('Permalink is required'); return; }
    if (!/^https?:\/\/(www\.|old\.|new\.)?reddit\.com\/r\/[^\/]+\/comments\/[a-z0-9]+\/[^\/]*\/[a-z0-9]+\/?/i.test(permalink)) {
        notify('That doesn\'t look like a Reddit comment permalink');
        return;
    }

    const btn = document.getElementById('redditConfirmMarkRepliedBtn');

    const submit = async (override) => {
        btn.disabled = true;
        try {
            return await api('reddit_mark_replied', {
                method: 'POST',
                body: { id: redditCurrentThread.id, permalink, mentioned_product: mentioned, override_limit: override }
            });
        } finally {
            btn.disabled = false;
        }
    };

    let data;
    try {
        data = await submit(0);

        if (data && !data.success && data.requires_override) {
            const ok = confirm(
                `You're at the post limit (daily ${data.daily_used}/${data.daily_limit}, weekly ${data.weekly_used}/${data.weekly_limit}). ` +
                `Post anyway? This increases shadowban risk.`
            );
            if (!ok) return;
            data = await submit(1);
        }
    } catch (e) {
        notify(e?.message || 'Failed to mark replied');
        return;
    }

    if (!data || !data.success) {
        notify(data?.message || 'Failed to mark replied');
        return;
    }

    closeModal('redditMarkRepliedModal');
    closeModal('redditThreadModal');
    loadRedditThreads();
    loadRedditStats();
}

// ─── Settings tab: account info ───

async function loadRedditAccountInfo() {
    const container = document.getElementById('redditAccountInfo');
    if (!container) return;
    container.innerHTML = '<span class="text-muted">Loading…</span>';

    let data;
    try {
        data = await api('reddit_get_account_info');
    } catch (e) {
        // api() throws on non-200 (e.g. 500 when REDDIT_USERNAME isn't configured).
        // Show the error inline instead of leaving the spinner stuck.
        container.innerHTML = `<span class="text-muted" style="color:#c00;">${escapeHtml(e.message || 'Failed to load account info')}</span>`;
        return;
    }
    if (!data || !data.success) {
        container.innerHTML = `<span class="text-muted" style="color:#c00;">${escapeHtml(data?.message || 'Failed to load account info')}</span>`;
        return;
    }
    const a = data.account;
    const ageDays = a.account_age_days;
    const ageStr = ageDays !== null && ageDays !== undefined ? `${ageDays} days old` : 'age unknown';
    let suggestion = '';
    if (ageDays !== null && ageDays !== undefined && a.total_karma !== null) {
        if (ageDays < 30 || a.total_karma < 100) {
            suggestion = 'Suggested limits at your account maturity: 2/day, 8/week (new/low-karma).';
        } else {
            suggestion = 'Suggested limits at your account maturity: 5/day, 15/week (established).';
        }
    }
    container.innerHTML = `
        <div><strong>u/${escapeHtml(a.username)}</strong> · ${escapeHtml(ageStr)} · karma: ${a.total_karma} (link ${a.link_karma}, comment ${a.comment_karma})</div>
        ${suggestion ? `<div class="text-muted" style="margin-top:6px;">${escapeHtml(suggestion)}</div>` : ''}
    `;
}

// ─── HTML escape helpers (used by Reddit row rendering) ───

function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
function escapeAttr(s) { return escapeHtml(s); }

