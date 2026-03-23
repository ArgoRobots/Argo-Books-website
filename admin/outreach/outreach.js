// ─── State ───
let currentLeadId = null;
let discoveryResults = [];
let debounceTimer = null;

// ─── URL Param Helpers ───
function getFilterParams() {
    return new URLSearchParams(window.location.search);
}

function updateUrlParams() {
    const params = new URLSearchParams();
    const search = document.getElementById('filterSearch').value.trim();
    const status = document.getElementById('filterStatus').value;
    const response = document.getElementById('filterResponse').value;
    const approval = document.getElementById('filterApproval').value;
    const sort = document.getElementById('filterSort').value;

    if (search) params.set('search', search);
    if (status) params.set('status', status);
    if (response) params.set('response', response);
    if (approval) params.set('approval', approval);
    if (sort && sort !== 'date_added_desc') params.set('sort', sort);

    const newUrl = params.toString()
        ? window.location.pathname + '?' + params.toString()
        : window.location.pathname;
    window.history.replaceState({}, '', newUrl);
}

function restoreFiltersFromUrl() {
    const params = getFilterParams();
    if (params.has('search')) document.getElementById('filterSearch').value = params.get('search');
    if (params.has('status')) document.getElementById('filterStatus').value = params.get('status');
    if (params.has('response')) document.getElementById('filterResponse').value = params.get('response');
    if (params.has('approval')) document.getElementById('filterApproval').value = params.get('approval');
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
        if (city) document.getElementById('discCity').value = city;
        if (province) document.getElementById('discProvince').value = province;
        if (category) document.getElementById('discCategory').value = category;
        if (limit) document.getElementById('discLimit').value = limit;
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
        // Session expired — redirect to login
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

function notify(message, type = 'success') {
    if (typeof showNotification === 'function') {
        showNotification(message, type);
    } else {
        alert(message);
    }
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
            document.getElementById('statApproved').textContent = s.approved || 0;
            document.getElementById('statContacted').textContent = s.contacted || 0;
            document.getElementById('statReplied').textContent = s.replied || 0;
            document.getElementById('statInterested').textContent = s.interested || 0;
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
    const approval = document.getElementById('filterApproval').value;
    const sort = document.getElementById('filterSort').value;

    if (search) params.search = search;
    if (status) params.status = status;
    if (response) params.response_status = response;
    if (approval) params.approval_status = approval;
    if (sort) params.sort = sort;

    // Persist filters to URL
    updateUrlParams();

    try {
        const data = await api('get_leads', { params });
        const tbody = document.getElementById('leadsTableBody');

        if (!data.success || !data.leads.length) {
            tbody.innerHTML = '<tr><td colspan="10" class="empty-state">No leads found</td></tr>';
            updateBulkBar();
            return;
        }

        tbody.innerHTML = data.leads.map(lead => `
            <tr onclick="openLeadDetail(${lead.id})" class="clickable-row">
                <td class="checkbox-column" onclick="event.stopPropagation()">
                    <div class="checkbox"><input type="checkbox" class="lead-check" value="${lead.id}" id="lead-check-${lead.id}" onchange="updateBulkBar()"><label for="lead-check-${lead.id}"></label></div>
                </td>
                <td>
                    <strong>${esc(lead.business_name)}</strong>
                    ${lead.contact_name ? '<br><small>' + esc(lead.contact_name) + '</small>' : ''}
                </td>
                <td>${lead.email ? esc(lead.email) : '<span class="text-muted">—</span>'}</td>
                <td>${lead.phone ? esc(lead.phone) : '<span class="text-muted">—</span>'}</td>
                <td>${esc(lead.city || '')}</td>
                <td>${esc(lead.category || '')}</td>
                <td><span class="badge badge-status-${lead.status || 'new'}">${formatStatus(lead.status || 'new')}</span></td>
                <td><span class="badge badge-approval-${lead.approval_status && lead.approval_status !== 'none' ? lead.approval_status : 'not_drafted'}">${formatApproval(lead.approval_status || 'not_drafted')}</span></td>

                <td onclick="event.stopPropagation()">
                    <div class="actions-cell">
                        <button class="btn btn-small btn-blue" onclick="openLeadDetail(${lead.id})" title="View">View</button>
                        ${lead.approval_status !== 'sent' ? `<button class="btn btn-small btn-blue" onclick="quickGenerateDraft(${lead.id}, this)" title="${lead.draft_subject ? 'Regenerate Draft' : 'Generate Draft'}">${lead.draft_subject ? 'Redraft' : 'Draft'}</button>` : ''}
                    </div>
                </td>
            </tr>
        `).join('');

        // Reset select-all checkbox
        const selectAll = document.getElementById('leadsSelectAll');
        if (selectAll) selectAll.checked = false;
        updateBulkBar();
    } catch (e) {
        notify(e.message, 'error');
    }
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
}

function getSelectedLeadIds() {
    return Array.from(document.querySelectorAll('.lead-check:checked')).map(cb => parseInt(cb.value));
}

async function bulkGenerateDrafts() {
    const ids = getSelectedLeadIds();
    if (!ids.length) return;
    if (!confirm(`Generate drafts for ${ids.length} lead(s)?`)) return;

    notify(`Generating ${ids.length} draft(s)...`, 'info');
    let success = 0, fail = 0;
    for (const id of ids) {
        try {
            const result = await api('generate_draft', { method: 'POST', body: { id } });
            if (result.success) success++; else fail++;
        } catch { fail++; }
    }
    notify(`Drafted: ${success}, Failed: ${fail}`, success ? 'success' : 'error');
    loadLeads();
    loadStats();
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

async function openBulkSendModal() {
    const ids = getSelectedLeadIds();
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

        // Auto-generate drafts for leads that don't have one
        const needsDraft = withEmail.filter(l => !l.draft_subject || !l.draft_body);
        if (needsDraft.length) {
            statusEl.textContent = `Generating drafts for ${needsDraft.length} lead(s)...`;
            for (const lead of needsDraft) {
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
                ${!hasEmail ? '<span class="bulk-send-item-no-email">Will be skipped — no email address</span>' :
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

        document.getElementById('detailOfferSent').value = lead.offer_sent ? '1' : '0';
        document.getElementById('detailContactPageUrl').value = lead.contact_page_url || '';
        document.getElementById('detailNotes').value = lead.notes || '';
        document.getElementById('detailFeedback').value = lead.feedback_summary || '';

        // Meta info
        let meta = `Added: ${formatDateTime(lead.date_added)}`;
        if (lead.first_contact_date) meta += ` | First contact: ${formatDateTime(lead.first_contact_date)}`;
        if (lead.last_contact_date) meta += ` | Last contact: ${formatDateTime(lead.last_contact_date)}`;
        if (lead.sent_at) meta += ` | Last sent: ${formatDateTime(lead.sent_at)}`;
        document.getElementById('detailMeta').textContent = meta;

        // Draft tab
        document.getElementById('draftSubject').value = lead.draft_subject || '';
        document.getElementById('draftBody').value = lead.draft_body || '';
        updateDraftStatus(lead);

        // Reset to info tab
        switchTab('tabInfo', document.querySelector('.tab'));

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
    const approveBtn = document.getElementById('btnApprove');

    let statusHtml = '';
    if (lead.approval_status === 'sent') {
        statusHtml = `<span class="badge badge-approval-sent">Sent</span> on ${formatDateTime(lead.sent_at)}`;
        sendBtn.disabled = true;
        approveBtn.disabled = true;
        approveBtn.style.display = 'none';
    } else if (lead.approval_status === 'approved') {
        statusHtml = '<span class="badge badge-approval-approved">Approved</span> — Ready to send';
        sendBtn.disabled = !lead.email;
        approveBtn.disabled = true;
        approveBtn.style.display = '';
        if (!lead.email) statusHtml += ' <span class="text-muted">(no email address)</span>';
    } else if (lead.draft_subject || lead.draft_body) {
        statusHtml = '<span class="badge badge-approval-draft_ready">Draft Ready</span>';
        sendBtn.disabled = !lead.email;
        approveBtn.disabled = false;
        approveBtn.style.display = '';
        if (!lead.email) statusHtml += ' <span class="text-muted">(no email address)</span>';
    } else {
        statusHtml = '<span class="badge badge-approval-not_drafted">None</span>';
        sendBtn.disabled = true;
        approveBtn.disabled = true;
        approveBtn.style.display = '';
    }

    if (lead.drafted_at) {
        statusHtml += ` | Drafted: ${formatDateTime(lead.drafted_at)}`;
    }
    if (lead.approved_at) {
        statusHtml += ` | Approved: ${formatDateTime(lead.approved_at)}`;
    }

    bar.innerHTML = statusHtml;

    // Update Generate Draft button text and visibility based on status
    const genBtn = document.getElementById('btnGenerate');
    if (lead.approval_status === 'sent') {
        genBtn.style.display = 'none';
        sendBtn.style.display = 'none';
    } else {
        genBtn.style.display = '';
        sendBtn.style.display = '';
        if (lead.draft_subject || lead.draft_body) {
            genBtn.textContent = 'Regenerate Draft';
        } else {
            genBtn.textContent = 'Generate Draft';
        }
    }

    // Info section
    let info = '';
    if (!lead.email) info = 'No email address — email sending is disabled for this lead.';
    document.getElementById('draftInfo').textContent = info;
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
        notify(result.message, result.success ? 'success' : 'error');
        if (result.success) {
            loadLeads();
            loadStats();
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
            loadLeads();
            loadStats();
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

// ─── Add Lead ───
function showAddLeadModal() {
    document.getElementById('addBusinessName').value = '';
    document.getElementById('addContactName').value = '';
    document.getElementById('addEmail').value = '';
    document.getElementById('addPhone').value = '';
    document.getElementById('addWebsite').value = '';
    document.getElementById('addAddress').value = '';
    document.getElementById('addCategory').value = '';
    document.getElementById('addCity').value = '';
    document.getElementById('addNotes').value = '';
    showModal('addLeadModal');
}

async function createLead() {
    const name = document.getElementById('addBusinessName').value.trim();
    if (!name) { notify('Business name is required', 'error'); return; }

    const data = {
        business_name: name,
        contact_name: document.getElementById('addContactName').value.trim(),
        email: document.getElementById('addEmail').value.trim(),
        phone: document.getElementById('addPhone').value.trim(),
        website: document.getElementById('addWebsite').value.trim(),
        address: document.getElementById('addAddress').value.trim(),
        category: document.getElementById('addCategory').value.trim(),
        city: document.getElementById('addCity').value.trim(),
        notes: document.getElementById('addNotes').value.trim(),
    };

    try {
        const result = await api('create_lead', { method: 'POST', body: data });
        notify(result.message, result.success ? 'success' : 'error');
        if (result.success) {
            closeModal('addLeadModal');
            loadLeads();
            loadStats();
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

// ─── Business Discovery ───
async function searchBusinesses() {
    const city = document.getElementById('discCity').value.trim();
    if (!city) { notify('City is required', 'error'); return; }

    const btn = document.getElementById('searchBtn');
    btn.disabled = true;
    btn.textContent = 'Searching...';

    const params = {
        city: city,
        province: document.getElementById('discProvince').value.trim(),
        category: document.getElementById('discCategory').value.trim(),
        limit: document.getElementById('discLimit').value,
    };

    const data = await api('search_businesses', { params });
    btn.disabled = false;
    btn.textContent = 'Search';

    if (!data.success) {
        notify(data.message, 'error');
        return;
    }

    discoveryResults = data.businesses;
    document.getElementById('discoveryResults').style.display = 'block';
    renderDiscoveryResults();
    saveDiscoveryToSession();

    if (data.note) {
        notify(data.note, 'info');
    }
}

function renderDiscoveryResults() {
    document.getElementById('discoveryCount').textContent = `${discoveryResults.length} results`;
    const tbody = document.getElementById('discoveryTableBody');

    if (!discoveryResults.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="empty-state">No businesses found</td></tr>';
        return;
    }

    tbody.innerHTML = discoveryResults.map((biz, i) => `
        <tr>
            <td><div class="checkbox"><input type="checkbox" class="disc-check" data-index="${i}" id="disc-check-${i}" checked><label for="disc-check-${i}"></label></div></td>
            <td>${esc(biz.business_name)}</td>
            <td>${biz.email ? esc(biz.email) : '<span class="text-muted">—</span>'}</td>
            <td>${biz.phone ? esc(biz.phone) : '<span class="text-muted">—</span>'}</td>
            <td>${biz.website ? '<a href="' + esc(biz.website) + '" target="_blank" rel="noopener noreferrer" class="link">Link</a>' : '<span class="text-muted">—</span>'}</td>
            <td>${esc(biz.address || '')}</td>
            <td>${esc(biz.category || '')}</td>
        </tr>
    `).join('');

    const selectAll = document.getElementById('discSelectAll');
    if (selectAll) selectAll.checked = true;
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
            // Refresh lead to update status
            openLeadDetail(currentLeadId);
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
            loadLeads();
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
async function approveDraft() {
    if (!currentLeadId) return;

    // Save any edits first
    const subject = document.getElementById('draftSubject').value.trim();
    const body = document.getElementById('draftBody').value.trim();
    if (!subject || !body) { notify('Subject and body are required', 'error'); return; }

    try {
        // Save draft edits
        await api('update_lead', {
            method: 'POST',
            body: { id: currentLeadId, draft_subject: subject, draft_body: body }
        });

        const result = await api('approve_draft', { method: 'POST', body: { id: currentLeadId } });
        notify(result.message, result.success ? 'success' : 'error');
        if (result.success) {
            openLeadDetail(currentLeadId);
            loadLeads();
            loadStats();
        }
    } catch (e) {
        notify(e.message, 'error');
    }
}

async function sendEmail() {
    if (!currentLeadId) return;
    if (!confirm('Send this approved email now?')) return;

    const btn = document.getElementById('btnSend');
    btn.disabled = true;
    btn.textContent = 'Sending...';

    try {
        const result = await api('send_email', { method: 'POST', body: { id: currentLeadId } });
        btn.textContent = 'Send Email';

        notify(result.message, result.success ? 'success' : 'error');
        if (result.success) {
            openLeadDetail(currentLeadId);
            loadLeads();
            loadStats();
        } else {
            btn.disabled = false;
        }
    } catch (e) {
        btn.textContent = 'Send Email';
        btn.disabled = false;
        notify(e.message, 'error');
    }
}

function togglePreview() {
    const preview = document.getElementById('draftPreview');
    if (preview.style.display === 'none') {
        const subject = document.getElementById('draftSubject').value;
        const body = document.getElementById('draftBody').value;
        document.getElementById('draftPreviewContent').innerHTML =
            '<p><strong>Subject:</strong> ' + esc(subject) + '</p><hr>' +
            '<p>' + esc(body).replace(/\n/g, '<br>') + '</p>';
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function copyDraft() {
    const subject = document.getElementById('draftSubject').value;
    const body = document.getElementById('draftBody').value;
    const text = `Subject: ${subject}\n\n${body}`;
    navigator.clipboard.writeText(text).then(() => {
        notify('Draft copied to clipboard', 'success');
    }).catch(() => {
        // Fallback
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        notify('Draft copied to clipboard', 'success');
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
    formData.append('action', 'import_csv');

    try {
        const res = await fetch('api.php?action=import_csv', { method: 'POST', body: formData });
        const result = await res.json();

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

// ─── Tab Switching ───
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
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

function formatApproval(status) {
    if (status === 'not_drafted' || status === 'none') return 'None';
    return status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function formatActionType(type) {
    return type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('en-CA', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatDateTime(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-CA', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' });
}
