const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
const csrfToken = () => $('meta[name="csrf-token"]')?.content || '';

// Keep online presence fresh while an authenticated workspace tab is visible.
const presencePingUrl = $('meta[name="presence-ping-url"]')?.content;
if (presencePingUrl) {
    const pingPresence = () => {
        if (document.visibilityState === 'hidden') return;
        fetch(presencePingUrl, {
            method: 'POST',
            credentials: 'same-origin',
            keepalive: true,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
        }).catch(() => {});
    };

    window.setInterval(pingPresence, 60_000);
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') pingPresence();
    });
}

// Refresh staff presence on the team panel without reloading the page.
const teamPresencePanel = $('[data-team-presence-url]');
if (teamPresencePanel) {
    let presenceRefreshInProgress = false;
    const refreshTeamPresence = async () => {
        if (document.visibilityState === 'hidden' || presenceRefreshInProgress) return;
        presenceRefreshInProgress = true;
        try {
            const response = await fetch(teamPresencePanel.dataset.teamPresenceUrl, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) return;
            const data = await response.json();
            const onlineCount = $('[data-team-online-count]');
            if (onlineCount && Number.isFinite(Number(data.online_count))) onlineCount.textContent = String(data.online_count);

            $$('[data-team-presence-row]').forEach((row) => {
                const member = data.members?.[row.dataset.teamPresenceRow];
                const state = $('[data-team-presence-state]', row);
                const label = state ? $('[data-team-presence-label]', state) : null;
                const detail = $('[data-team-presence-detail]', row);
                const loginDate = $('[data-team-login-date]', row);
                const loginTime = $('[data-team-login-time]', row);
                if (!member || !state || !label || !detail || !loginDate || !loginTime) return;
                state.classList.remove('presence-online', 'presence-offline', 'presence-unknown');
                state.classList.add(member.class);
                label.textContent = member.label;
                detail.textContent = member.detail;
                loginDate.textContent = member.login_date;
                loginDate.classList.toggle('staff-login-never', !member.has_logged_in);
                loginTime.textContent = member.login_time;
            });
        } catch {
            // Keep the server-rendered status if a refresh cannot reach the endpoint.
        } finally {
            presenceRefreshInProgress = false;
        }
    };

    window.setInterval(refreshTeamPresence, 60_000);
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') refreshTeamPresence();
    });
}

// Responsive navigation drawer.
const sidebar = $('#app-sidebar');
const scrim = $('.sidebar-scrim');
const setSidebar = (open) => {
    sidebar?.classList.toggle('is-open', open);
    scrim?.classList.toggle('is-visible', open);
    document.body.classList.toggle('drawer-open', open);
};
$('[data-sidebar-toggle]')?.addEventListener('click', () => setSidebar(true));
$$('[data-sidebar-close]').forEach((element) => element.addEventListener('click', () => setSidebar(false)));

// Lightweight, accessible header menus.
$$('[data-dropdown-toggle]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.stopPropagation();
        const panel = document.getElementById(button.dataset.dropdownToggle);
        const willOpen = !panel?.classList.contains('is-open');
        $$('.dropdown-panel.is-open').forEach((menu) => menu.classList.remove('is-open'));
        if (willOpen) panel?.classList.add('is-open');
    });
});
document.addEventListener('click', (event) => {
    if (!event.target.closest('.dropdown-wrap')) {
        $$('.dropdown-panel.is-open').forEach((panel) => panel.classList.remove('is-open'));
    }
});

// Server-rendered forms get a restrained submit state to prevent accidental double taps.
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || event.defaultPrevented || form.method.toLowerCase() === 'get' || form.hasAttribute('data-no-loading')) return;
    if (form.matches('[data-ajax-lead-form], [data-ajax-lead-status], [data-ajax-lead-delete], [data-ajax-bulk-delete]')) return;
    if (form.matches('[data-confirm]') && form.dataset.confirmConfirmed !== 'true') return;
    const button = form.querySelector('button[type="submit"]:not([form])') || form.querySelector('button[type="submit"]');
    if (!button || button.disabled) return;
    button.classList.add('is-loading');
    button.setAttribute('aria-busy', 'true');
    button.disabled = true;
});

// Existing server-rendered toast messages.
$$('[data-toast]').forEach((toast) => {
    const dismiss = () => {
        toast.classList.add('toast-leaving');
        window.setTimeout(() => toast.remove(), 180);
    };
    $('[data-toast-close]', toast)?.addEventListener('click', dismiss);
    window.setTimeout(dismiss, 5200);
});

// Password visibility on the authentication screen.
$$('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.togglePassword);
        if (!input) return;
        const showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
});

// Shared confirmation dialog for destructive actions and sensitive form submissions.
const confirmBackdrop = $('[data-confirm-dialog]');
const confirmTitle = $('[data-confirm-title]');
const confirmMessage = $('[data-confirm-message]');
const confirmAccept = $('[data-confirm-accept]');
let confirmResolve = null;
let confirmReturnFocus = null;
const confirmationIsOpen = () => !!confirmBackdrop && !confirmBackdrop.hidden;
const closeConfirmation = (accepted = false) => {
    if (!confirmBackdrop || confirmBackdrop.hidden) return;
    confirmBackdrop.hidden = true;
    confirmBackdrop.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('confirmation-open');
    const resolve = confirmResolve;
    confirmResolve = null;
    if (resolve) resolve(accepted);
    if (confirmReturnFocus?.isConnected) confirmReturnFocus.focus();
    confirmReturnFocus = null;
};
const askForConfirmation = ({ title = 'Are you sure?', message = 'Do you want to continue?', acceptLabel = 'Continue' } = {}) => new Promise((resolve) => {
    if (!confirmBackdrop) { resolve(false); return; }
    if (confirmResolve) closeConfirmation(false);
    confirmResolve = resolve;
    confirmReturnFocus = document.activeElement instanceof HTMLElement ? document.activeElement : null;
    if (confirmTitle) confirmTitle.textContent = title;
    if (confirmMessage) confirmMessage.textContent = message;
    if (confirmAccept) confirmAccept.textContent = acceptLabel;
    confirmBackdrop.hidden = false;
    confirmBackdrop.setAttribute('aria-hidden', 'false');
    document.body.classList.add('confirmation-open');
    window.requestAnimationFrame(() => confirmAccept?.focus());
});
confirmBackdrop?.addEventListener('click', (event) => {
    if (event.target === confirmBackdrop || event.target.closest('[data-confirm-cancel]')) closeConfirmation(false);
    if (event.target.closest('[data-confirm-accept]')) closeConfirmation(true);
});
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-confirm]')) return;
    if (form.dataset.confirmConfirmed === 'true') { delete form.dataset.confirmConfirmed; return; }
    event.preventDefault();
    askForConfirmation({
        title: form.dataset.confirmTitle || 'Please confirm',
        message: form.dataset.confirm || 'Are you sure you want to continue?',
        acceptLabel: form.dataset.confirmAccept || 'Continue',
    }).then((accepted) => {
        if (!accepted || !form.isConnected) return;
        form.dataset.confirmConfirmed = 'true';
        if (typeof form.requestSubmit === 'function') form.requestSubmit();
        else form.submit();
    });
});

// Staff CRUD: modal create/edit, custom-confirmed delete and JSON-powered table refresh.
const teamPanel = $('[data-team-panel]');
const teamRows = $('[data-team-rows]');
const teamModal = $('[data-team-modal]');
const teamForm = $('[data-team-form]');
const teamModalTitle = $('[data-team-modal-title]');
const teamModalDescription = $('[data-team-modal-description]');
const teamMethodInput = $('[data-team-method]', teamForm || document);
const teamActiveField = $('[data-team-active-field]', teamForm || document);
const teamActiveSelect = $('[name="is_active"]', teamForm || document);
const teamPasswordFields = $('[data-team-password-fields]', teamForm || document);
let teamModalReturnFocus = null;

const renderTeamResponse = (data = {}) => {
    if (teamRows && typeof data.rows_html === 'string') teamRows.innerHTML = data.rows_html;
    const summary = data.summary || {};
    const setText = (selector, value) => {
        const element = $(selector);
        if (element && value !== undefined && value !== null) element.textContent = String(value);
    };
    setText('[data-team-stat="total"]', summary.teamCount);
    setText('[data-team-stat="active"]', summary.activeCount);
    setText('[data-team-online-count]', summary.onlineCount);
    setText('[data-team-active-label]', `${summary.activeCount ?? 0} active`);
    if (summary.salesCount !== undefined) {
        setText('[data-team-stat="sales-label"]', `${summary.salesCount} active sales ${Number(summary.salesCount) === 1 ? 'rep' : 'reps'}`);
    }
};

const clearTeamFormErrors = () => {
    if (!teamForm) return;
    $$('.field-error', teamForm).forEach((item) => item.remove());
    $$('[aria-invalid="true"]', teamForm).forEach((field) => field.removeAttribute('aria-invalid'));
    const summary = $('[data-form-errors]', teamForm);
    if (summary) { summary.textContent = ''; summary.hidden = true; }
};

const closeTeamModal = () => {
    if (!teamModal || teamModal.hidden) return;
    teamModal.hidden = true;
    teamModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('team-modal-open');
    clearTeamFormErrors();
    teamForm?.reset();
    if (teamModalReturnFocus?.isConnected) teamModalReturnFocus.focus();
    else $('[data-team-create]')?.focus();
    teamModalReturnFocus = null;
};

const openTeamModal = (mode = 'create', opener = document.activeElement) => {
    if (!teamModal || !teamForm) return;
    clearTeamFormErrors();
    teamForm.reset();
    teamModalReturnFocus = opener instanceof HTMLElement ? opener : null;
    const editing = mode === 'edit';
    teamForm.dataset.teamMode = editing ? 'edit' : 'create';
    teamForm.action = editing ? opener.dataset.updateUrl : teamForm.dataset.createUrl;
    if (teamMethodInput) teamMethodInput.disabled = !editing;
    if (teamActiveField) teamActiveField.hidden = !editing;
    if (teamActiveSelect) teamActiveSelect.disabled = !editing;
    if (teamPasswordFields) teamPasswordFields.hidden = editing;
    $$('input[type="password"]', teamForm).forEach((field) => {
        field.disabled = editing;
        field.required = !editing;
    });
    if (teamModalTitle) teamModalTitle.textContent = editing ? 'Edit teammate' : 'Add a teammate';
    if (teamModalDescription) teamModalDescription.textContent = editing
        ? 'Update this member’s profile and workspace access.'
        : 'Create a secure account for your workspace.';
    const submitLabel = $('[data-team-submit-label]', teamForm);
    if (submitLabel) submitLabel.textContent = editing ? 'Save changes' : 'Add teammate';

    if (editing) {
        const fields = {
            name: $('[name="name"]', teamForm),
            email: $('[name="email"]', teamForm),
            phone: $('[name="phone"]', teamForm),
            role: $('[name="role"]', teamForm),
            is_active: teamActiveSelect,
        };
        if (fields.name) fields.name.value = opener.dataset.memberName || '';
        if (fields.email) fields.email.value = opener.dataset.memberEmail || '';
        if (fields.phone) fields.phone.value = opener.dataset.memberPhone || '';
        if (fields.role) fields.role.value = opener.dataset.memberRole || 'sales';
        if (fields.is_active) fields.is_active.value = opener.dataset.memberActive || '1';
        const inactiveOption = fields.is_active?.querySelector('option[value="0"]');
        if (inactiveOption) inactiveOption.disabled = opener.dataset.memberSelf === 'true';
    } else {
        const inactiveOption = teamActiveSelect?.querySelector('option[value="0"]');
        if (inactiveOption) inactiveOption.disabled = false;
    }

    teamModal.hidden = false;
    teamModal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('team-modal-open');
    window.requestAnimationFrame(() => $('[name="name"]', teamForm)?.focus({ preventScroll: true }));
};

if (teamForm) teamForm.dataset.createUrl = teamForm.action;
teamModal?.addEventListener('click', (event) => {
    if (event.target === teamModal || event.target.closest('[data-team-modal-close]')) closeTeamModal();
});

document.addEventListener('click', async (event) => {
    const createButton = event.target.closest('[data-team-create]');
    if (createButton) {
        event.preventDefault();
        openTeamModal('create', createButton);
        return;
    }
    const editButton = event.target.closest('[data-team-edit]');
    if (editButton) {
        event.preventDefault();
        openTeamModal('edit', editButton);
        return;
    }
    if (event.target.closest('[data-team-modal-close]')) {
        event.preventDefault();
        closeTeamModal();
        return;
    }
    const deleteButton = event.target.closest('[data-team-delete]');
    if (!deleteButton) return;
    event.preventDefault();
    const name = deleteButton.dataset.memberName || 'this teammate';
    const accepted = await askForConfirmation({
        title: `Remove “${name}” from the workspace?`,
        message: 'Their assigned leads and follow-ups will be transferred to you. Import records and lead activity are retained.',
        acceptLabel: 'Remove teammate',
    });
    if (!accepted) return;
    deleteButton.disabled = true;
    deleteButton.classList.add('is-loading');
    deleteButton.setAttribute('aria-busy', 'true');
    try {
        const data = await jsonRequest(deleteButton.dataset.deleteUrl, { method: 'DELETE' });
        renderTeamResponse(data);
        showAjaxToast(data.message || `${name} was removed from the workspace.`);
        $('[data-team-create]')?.focus({ preventScroll: true });
    } catch (error) {
        showAjaxToast(errorMessage(error, 'Could not remove this teammate.'), 'error');
    } finally {
        if (deleteButton.isConnected) {
            deleteButton.disabled = false;
            deleteButton.classList.remove('is-loading');
            deleteButton.removeAttribute('aria-busy');
        }
    }
});

teamForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearTeamFormErrors();
    const button = $('[data-team-submit]', teamForm);
    const editing = teamForm.dataset.teamMode === 'edit';
    if (button) { button.disabled = true; button.classList.add('is-loading'); button.setAttribute('aria-busy', 'true'); }
    try {
        const data = await jsonRequest(teamForm.action, { method: 'POST', body: new FormData(teamForm) });
        renderTeamResponse(data);
        closeTeamModal();
        showAjaxToast(data.message || (editing ? 'Teammate details updated.' : 'Teammate added.'));
    } catch (error) {
        if (error.status === 422 && error.payload?.errors) showFormErrors(teamForm, error.payload.errors);
        showAjaxToast(errorMessage(error, editing ? 'Could not update this teammate.' : 'Could not add this teammate.'), 'error');
    } finally {
        if (button?.isConnected) { button.disabled = false; button.classList.remove('is-loading'); button.removeAttribute('aria-busy'); }
    }
});

// Leads index: server-rendered rows with JSON-powered search, filtering and pagination.
const leadIndex = $('[data-lead-index]');
const leadFilterForm = $('[data-lead-filter-form]');
const leadResults = $('[data-lead-results]');
const leadFooter = $('[data-lead-footer]');
const leadSpinner = $('[data-lead-spinner]');
const leadResultLabel = $('[data-lead-result-label]');
const leadTotalLabel = $('[data-total-leads]');
const leadExportLink = $('[data-lead-export]');
const filterPanel = $('[data-filter-panel]');
const filterPanelToggle = $('[data-filter-panel-toggle]');
const filterPanelClose = $('[data-filter-panel-close]');
const activeFilterCount = $('[data-active-filter-count]');
const bulkToolbar = $('[data-bulk-toolbar]');
const selectedCountLabel = $('[data-selected-count]');
const selectedHidden = $('[data-selected-hidden]');
const selectAll = $('[data-select-all]');
let leadSearchTimer = 0;
let leadListController = null;
let leadListRequestId = 0;
let leadModalController = null;
let leadModalReturnFocus = null;
let ajaxToastTimer = 0;
let ajaxToastHideTimer = 0;

const ajaxToast = $('[data-ajax-toast]');
const showAjaxToast = (message, kind = 'success') => {
    if (!ajaxToast) return;
    window.clearTimeout(ajaxToastTimer);
    window.clearTimeout(ajaxToastHideTimer);
    ajaxToast.className = `ajax-toast ajax-toast-${kind}`;
    ajaxToast.textContent = message;
    ajaxToast.hidden = false;
    ajaxToast.setAttribute('data-kind', kind);
    ajaxToastTimer = window.setTimeout(() => {
        ajaxToast.classList.add('is-leaving');
        ajaxToastHideTimer = window.setTimeout(() => {
            if (ajaxToast) { ajaxToast.hidden = true; ajaxToast.classList.remove('is-leaving'); }
        }, 180);
    }, 4200);
};

const jsonRequest = async (url, options = {}) => {
    const headers = new Headers(options.headers || {});
    headers.set('Accept', 'application/json');
    headers.set('X-Requested-With', 'XMLHttpRequest');
    if (options.method && !['GET', 'HEAD'].includes(options.method.toUpperCase())) headers.set('X-CSRF-TOKEN', csrfToken());
    const response = await fetch(url, { credentials: 'same-origin', ...options, headers });
    const contentType = response.headers.get('content-type') || '';
    let data = null;
    if (contentType.includes('application/json')) {
        try { data = await response.json(); } catch (_) { data = {}; }
    }
    if (!response.ok || !contentType.includes('application/json')) {
        const error = new Error(data?.message || (response.redirected ? 'Your session may have expired. Sign in again and retry.' : `Unexpected server response (${response.status}).`));
        error.status = response.status || 500;
        error.payload = data;
        throw error;
    }
    return data || {};
};

const errorMessage = (error, fallback = 'Something went wrong. Please try again.') => {
    if (error?.status === 422) return error.payload?.message || 'Please review the entered information and try again.';
    if (error?.status === 403) return error.payload?.message || 'You do not have permission to perform this action.';
    if (error?.status === 419) return 'Your session expired. Refresh the page and try again.';
    return error?.message || fallback;
};

const leadFormUrl = () => {
    if (!leadFilterForm) return null;
    const url = new URL(leadFilterForm.action, window.location.href);
    for (const [name, rawValue] of new FormData(leadFilterForm).entries()) {
        const value = String(rawValue).trim();
        if (value !== '') url.searchParams.set(name, value);
        else url.searchParams.delete(name);
    }
    url.searchParams.delete('page');
    return url;
};

const updateLeadFormFromUrl = (url) => {
    if (!leadFilterForm) return;
    const params = url.searchParams;
    [...leadFilterForm.elements].forEach((field) => {
        if (!field.name || field.type === 'submit' || field.type === 'button') return;
        if (field.type === 'checkbox' || field.type === 'radio') {
            field.checked = params.getAll(field.name).includes(field.value);
            return;
        }
        const value = params.get(field.name);
        if (value !== null) field.value = value;
        else if (field.name === 'per_page') field.value = '15';
        else field.value = '';
    });
    refreshActiveFilterCount();
};

const updateLeadExportLink = (sourceUrl) => {
    if (!leadExportLink) return;
    const exportUrl = new URL(leadExportLink.dataset.leadExportUrl || leadExportLink.href, window.location.href);
    exportUrl.search = '';
    ['q', 'source', 'location', 'priority', 'status', 'from', 'to', 'assigned_to'].forEach((name) => {
        const value = sourceUrl.searchParams.get(name);
        if (value !== null && value !== '') exportUrl.searchParams.set(name, value);
    });
    leadExportLink.href = exportUrl.href;
};

const refreshActiveFilterCount = () => {
    if (!leadFilterForm || !activeFilterCount) return;
    const tracked = ['source', 'location', 'from', 'to', 'assigned_to', 'status', 'priority'];
    const count = tracked.reduce((sum, name) => {
        const field = [...leadFilterForm.elements].find((element) => element.name === name);
        return sum + (field && String(field.value).trim() !== '' ? 1 : 0);
    }, 0);
    activeFilterCount.textContent = String(count);
    activeFilterCount.hidden = count === 0;
};

const setLeadListLoading = (loading) => {
    if (leadSpinner) leadSpinner.hidden = !loading;
    leadFilterForm?.classList.toggle('is-searching', loading);
    leadResults?.setAttribute('aria-busy', loading ? 'true' : 'false');
    if (leadFilterForm) $('[name="q"]', leadFilterForm)?.setAttribute('aria-busy', loading ? 'true' : 'false');
};

const syncLeadSelection = () => {
    if (!leadIndex) return;
    const rowChecks = $$('[data-row-select]', leadIndex);
    const selected = rowChecks.filter((checkbox) => checkbox.checked);
    const count = selected.length;
    if (selectedCountLabel) selectedCountLabel.textContent = String(count);
    if (bulkToolbar) bulkToolbar.hidden = count === 0;
    if (selectAll) {
        selectAll.checked = rowChecks.length > 0 && count === rowChecks.length;
        selectAll.indeterminate = count > 0 && count < rowChecks.length;
        selectAll.disabled = rowChecks.length === 0;
    }
    if (selectedHidden) {
        selectedHidden.replaceChildren();
        selected.forEach((checkbox) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = checkbox.value;
            selectedHidden.append(input);
        });
    }
};

const fetchLeadResults = async (url, historyMode = 'none') => {
    if (!leadIndex || !leadResults || !leadFooter) return;
    if (leadListController) leadListController.abort();
    const controller = new AbortController();
    leadListController = controller;
    const requestId = ++leadListRequestId;
    setLeadListLoading(true);
    try {
        const data = await jsonRequest(url.href, { method: 'GET', signal: controller.signal });
        if (requestId !== leadListRequestId) return;
        leadResults.innerHTML = data.rows || '';
        leadFooter.innerHTML = data.footer || '';
        const total = Number(data.total || 0);
        if (leadTotalLabel) leadTotalLabel.textContent = new Intl.NumberFormat().format(total);
        if (leadResultLabel) leadResultLabel.textContent = `${new Intl.NumberFormat().format(total)} ${total === 1 ? 'lead' : 'leads'}`;
        updateLeadFormFromUrl(url);
        updateLeadExportLink(url);
        if (historyMode === 'push') window.history.pushState({ leads: true }, '', url.href);
        if (historyMode === 'replace') window.history.replaceState({ leads: true }, '', url.href);
        syncLeadSelection();
    } catch (error) {
        if (error.name !== 'AbortError' && requestId === leadListRequestId) showAjaxToast(errorMessage(error, 'Could not refresh your leads.'), 'error');
    } finally {
        if (requestId === leadListRequestId) {
            setLeadListLoading(false);
            leadListController = null;
        }
    }
};

const scheduleLeadSearch = (historyMode = 'replace', delay = 260) => {
    if (!leadIndex || !leadFilterForm) return;
    window.clearTimeout(leadSearchTimer);
    refreshActiveFilterCount();
    leadSearchTimer = window.setTimeout(() => {
        const url = leadFormUrl();
        if (url) fetchLeadResults(url, historyMode);
    }, delay);
};

const resetLeadFilters = () => {
    if (!leadFilterForm) return;
    window.clearTimeout(leadSearchTimer);
    [...leadFilterForm.elements].forEach((field) => {
        if (!field.name || field.type === 'submit' || field.type === 'button') return;
        if (field.name === 'per_page') field.value = '15';
        else if (field.type === 'checkbox' || field.type === 'radio') field.checked = false;
        else field.value = '';
    });
    if (filterPanel) filterPanel.hidden = true;
    filterPanelToggle?.setAttribute('aria-expanded', 'false');
    refreshActiveFilterCount();
    const url = leadFormUrl();
    if (url) fetchLeadResults(url, 'push');
};

if (leadFilterForm) {
    leadFilterForm.addEventListener('submit', (event) => {
        event.preventDefault();
        window.clearTimeout(leadSearchTimer);
        const url = leadFormUrl();
        if (url) fetchLeadResults(url, 'push');
    });
    leadFilterForm.addEventListener('input', (event) => {
        if (event.target.matches('[name="q"]')) scheduleLeadSearch('replace', 280);
    });
    leadFilterForm.addEventListener('change', (event) => {
        if (event.target.matches('select, input[type="date"]')) scheduleLeadSearch('push', 90);
    });
    refreshActiveFilterCount();
}
document.addEventListener('click', (event) => {
    const filterToggle = event.target.closest('[data-filter-panel-toggle]');
    if (filterToggle && filterPanel) {
        event.preventDefault();
        filterPanel.hidden = !filterPanel.hidden;
        filterToggle.setAttribute('aria-expanded', String(!filterPanel.hidden));
        return;
    }
    const filterClose = event.target.closest('[data-filter-panel-close]');
    if (filterClose && filterPanel) {
        event.preventDefault();
        filterPanel.hidden = true;
        filterPanelToggle?.setAttribute('aria-expanded', 'false');
        return;
    }
    if (filterPanel && !filterPanel.hidden && !event.target.closest('[data-filter-panel], [data-filter-panel-toggle]')) {
        filterPanel.hidden = true;
        filterPanelToggle?.setAttribute('aria-expanded', 'false');
    }
    if (event.target.closest('[data-reset-lead-filters], [data-clear-lead-filters]')) {
        event.preventDefault();
        resetLeadFilters();
    }
    const pageLink = event.target.closest('[data-lead-index] .pagination-links a');
    if (pageLink) {
        event.preventDefault();
        window.clearTimeout(leadSearchTimer);
        const url = new URL(pageLink.href, window.location.href);
        fetchLeadResults(url, 'push');
    }
});
window.addEventListener('popstate', () => {
    if (!leadIndex || !leadFilterForm) return;
    window.clearTimeout(leadSearchTimer);
    const url = new URL(window.location.href);
    updateLeadFormFromUrl(url);
    fetchLeadResults(url, 'none');
});

// Accessible lead detail/form modal. Links keep their regular URLs as a no-JS fallback.
const leadDialog = $('[data-lead-dialog]');
const leadDialogContent = $('[data-lead-dialog-content]');
const leadDialogIsOpen = () => !!leadDialog && !leadDialog.hidden;
const showLeadDialog = () => {
    if (!leadDialog) return;
    leadDialog.hidden = false;
    leadDialog.setAttribute('aria-hidden', 'false');
    document.body.classList.add('lead-dialog-open');
};
const closeLeadDialog = () => {
    if (!leadDialog || leadDialog.hidden) return;
    leadModalController?.abort();
    leadModalController = null;
    leadDialog.hidden = true;
    leadDialog.classList.remove('lead-dialog-form-mode');
    leadDialog.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('lead-dialog-open');
    if (leadDialogContent) leadDialogContent.replaceChildren();
    if (leadModalReturnFocus?.isConnected) leadModalReturnFocus.focus();
    else $('[data-lead-filter-form] [name="q"]')?.focus({ preventScroll: true });
    leadModalReturnFocus = null;
};
const openLeadDialog = async (url, opener = document.activeElement) => {
    if (!leadDialog || !leadDialogContent) return;
    if (!leadDialogIsOpen()) leadModalReturnFocus = opener instanceof HTMLElement ? opener : null;
    leadModalController?.abort();
    const controller = new AbortController();
    leadModalController = controller;
    showLeadDialog();
    leadDialog.classList.remove('lead-dialog-form-mode');
    leadDialogContent.innerHTML = '<div class="lead-modal-loading"><span class="lead-spinner-large"></span><span>Loading lead details…</span></div>';
    try {
        const data = await jsonRequest(url, { method: 'GET', signal: controller.signal });
        if (controller.signal.aborted) return;
        leadDialogContent.innerHTML = data.html || '<div class="lead-modal-error">No lead details were returned.</div>';
        leadDialog.classList.toggle('lead-dialog-form-mode', Boolean($('[data-ajax-lead-form]', leadDialogContent)));
        const focusTarget = $('input:not([type="hidden"]), select, textarea, [data-lead-dialog-close], .lead-dialog-actions a', leadDialogContent);
        window.requestAnimationFrame(() => focusTarget?.focus({ preventScroll: true }));
    } catch (error) {
        if (error.name === 'AbortError') return;
        leadDialogContent.innerHTML = `<div class="lead-modal-error"><span class="confirm-dialog-icon"><svg class="icon" width="20" height="20" aria-hidden="true"><use href="#i-alert"></use></svg></span><strong>Couldn’t load this lead</strong><p></p><button class="btn btn-secondary" type="button" data-lead-dialog-close>Close</button></div>`;
        const message = $('.lead-modal-error p', leadDialogContent);
        if (message) message.textContent = errorMessage(error, 'Please try again.');
        $('[data-lead-dialog-close]', leadDialogContent)?.focus();
    }
};
leadDialog?.addEventListener('click', (event) => {
    if (event.target === leadDialog || event.target.closest('[data-lead-dialog-close]')) closeLeadDialog();
});
document.addEventListener('click', (event) => {
    const formLink = event.target.closest('a[data-lead-form-url]');
    const previewLink = event.target.closest('a[data-lead-preview-url]');
    const target = formLink || previewLink;
    if (!target || !leadDialog) return;
    event.preventDefault();
    openLeadDialog(target.dataset.leadFormUrl || target.dataset.leadPreviewUrl || target.href, target);
});

const showFormErrors = (form, errors = {}) => {
    const summary = $('[data-form-errors]', form);
    $$('.field-error', form).forEach((item) => item.remove());
    $$('[aria-invalid="true"]', form).forEach((field) => field.removeAttribute('aria-invalid'));
    const firstMessages = [];
    Object.entries(errors).forEach(([name, messages]) => {
        const message = Array.isArray(messages) ? messages[0] : String(messages);
        firstMessages.push(message);
        const field = [...form.elements].find((element) => element.name === name || element.name === `${name}[]`);
        if (!field) return;
        field.setAttribute('aria-invalid', 'true');
        const wrapper = field.closest('.form-field') || field.parentElement;
        if (!wrapper) return;
        const error = document.createElement('span');
        error.className = 'field-error';
        error.textContent = message;
        wrapper.append(error);
    });
    if (summary) {
        summary.textContent = firstMessages.length ? `Please review the highlighted fields. ${firstMessages[0]}` : 'Please review the entered information.';
        summary.hidden = false;
    }
    $('[aria-invalid="true"]', form)?.focus({ preventScroll: false });
};

const submitLeadForm = async (form) => {
    const button = $('button[type="submit"]', form);
    const originalLabel = button?.innerHTML;
    if (button) { button.disabled = true; button.classList.add('is-loading'); button.setAttribute('aria-busy', 'true'); }
    $$('.field-error', form).forEach((item) => item.remove());
    const summary = $('[data-form-errors]', form);
    if (summary) summary.hidden = true;
    try {
        const data = await jsonRequest(form.action, { method: 'POST', body: new FormData(form) });
        showAjaxToast(data.message || 'Lead saved successfully.');
        if (data.place_prospect) markPlaceProspectConverted(data.place_prospect);
        if (leadIndex) {
            closeLeadDialog();
            const url = leadFormUrl();
            if (url) fetchLeadResults(url, 'replace');
        } else if (data.url || form.dataset.leadProfileUrl) {
            openLeadDialog(data.url || form.dataset.leadProfileUrl);
        } else closeLeadDialog();
    } catch (error) {
        if (error.status === 422 && error.payload?.errors) showFormErrors(form, error.payload.errors);
        else showAjaxToast(errorMessage(error, 'Could not save this lead.'), 'error');
    } finally {
        if (button?.isConnected) {
            button.disabled = false;
            button.classList.remove('is-loading');
            button.removeAttribute('aria-busy');
            if (originalLabel) button.innerHTML = originalLabel;
        }
    }
};
const submitLeadStatus = async (form) => {
    const button = $('button[type="submit"]', form);
    if (button) { button.disabled = true; button.classList.add('is-loading'); button.setAttribute('aria-busy', 'true'); }
    try {
        const data = await jsonRequest(form.action, { method: 'POST', body: new FormData(form) });
        const select = $('select[name="status"]', form);
        const pill = $('[data-lead-status-pill]');
        if (pill && select) {
            [...pill.classList].forEach((className) => {
                if (className.startsWith('status-') && className !== 'status-pill') pill.classList.remove(className);
            });
            pill.classList.add(`status-${String(data.status || select.value).replaceAll('_', '-')}`);
            pill.textContent = select.selectedOptions[0]?.textContent.trim() || 'Updated';
        }
        showAjaxToast(data.message || 'Lead status updated.');
    } catch (error) {
        showAjaxToast(errorMessage(error, 'Could not update this lead status.'), 'error');
    } finally {
        if (button?.isConnected) { button.disabled = false; button.classList.remove('is-loading'); button.removeAttribute('aria-busy'); }
    }
};

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (form.matches('[data-ajax-lead-form]')) {
        event.preventDefault();
        submitLeadForm(form);
    } else if (form.matches('[data-ajax-lead-status]')) {
        event.preventDefault();
        submitLeadStatus(form);
    } else if (form.matches('[data-ajax-lead-delete]')) {
        event.preventDefault();
        deleteLeadForm(form);
    }
});

// Delete forms are always intercepted, confirmed, then sent through JSON/AJAX.
const deleteLeadForm = async (form) => {
    const button = $('button[type="submit"]', form);
    const name = form.dataset.leadDeleteName || 'this lead';
    const accepted = await askForConfirmation({
        title: `Delete “${name}”?`,
        message: 'This lead will be moved to deleted records. You can’t undo this action.',
        acceptLabel: 'Delete lead',
    });
    if (!accepted) return;
    if (button) { button.disabled = true; button.classList.add('is-loading'); button.setAttribute('aria-busy', 'true'); }
    try {
        const data = await jsonRequest(form.action, { method: 'POST', body: new FormData(form) });
        showAjaxToast(data.message || 'Lead moved to deleted records.');
        if (leadIndex) {
            const url = leadFormUrl();
            if (url) fetchLeadResults(url, 'replace');
        } else {
            const profile = $('[data-lead-profile]');
            if (profile) {
                const backUrl = profile.dataset.leadIndexUrl || '/leads';
                profile.innerHTML = `<section class="surface-card delete-success-state"><span class="confirm-dialog-icon"><svg class="icon" width="20" height="20" aria-hidden="true"><use href="#i-check-circle"></use></svg></span><h1>Lead moved to deleted records</h1><p>The record has been removed from your active pipeline.</p><a class="btn btn-primary" href="${backUrl}">Back to leads</a></section>`;
            }
        }
    } catch (error) {
        showAjaxToast(errorMessage(error, 'Could not delete this lead.'), 'error');
    } finally {
        if (button?.isConnected) { button.disabled = false; button.classList.remove('is-loading'); button.removeAttribute('aria-busy'); }
    }
};

const deleteSelectedLeads = async () => {
    const form = $('[data-ajax-bulk-delete]');
    const ids = $$('[data-row-select]:checked', leadIndex || document).map((checkbox) => checkbox.value);
    if (!form || ids.length === 0) return;
    const accepted = await askForConfirmation({
        title: `Delete ${ids.length} selected ${ids.length === 1 ? 'lead' : 'leads'}?`,
        message: 'The selected leads will be moved to deleted records. You can’t undo this action.',
        acceptLabel: `Delete ${ids.length} ${ids.length === 1 ? 'lead' : 'leads'}`,
    });
    if (!accepted) return;
    const trigger = $('[data-bulk-delete-trigger]');
    if (trigger) { trigger.disabled = true; trigger.classList.add('is-loading'); }
    try {
        const data = await jsonRequest(form.action, { method: 'POST', body: new FormData(form) });
        showAjaxToast(data.message || `${ids.length} leads deleted.`);
        syncLeadSelection();
        const url = leadFormUrl();
        if (url) fetchLeadResults(url, 'replace');
    } catch (error) {
        showAjaxToast(errorMessage(error, 'Could not delete the selected leads.'), 'error');
    } finally {
        if (trigger?.isConnected) { trigger.disabled = false; trigger.classList.remove('is-loading'); }
    }
};

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-bulk-delete-trigger]')) {
        event.preventDefault();
        deleteSelectedLeads();
        return;
    }
    if (event.target.closest('[data-clear-selection]')) {
        $$('[data-row-select]', leadIndex || document).forEach((checkbox) => { checkbox.checked = false; });
        if (selectAll) selectAll.checked = false;
        syncLeadSelection();
    }
});
document.addEventListener('change', (event) => {
    if (event.target.matches('[data-select-all]')) {
        $$('[data-row-select]', leadIndex || document).forEach((checkbox) => { checkbox.checked = event.target.checked; });
        syncLeadSelection();
    } else if (event.target.matches('[data-row-select]')) syncLeadSelection();
});
syncLeadSelection();

// Import upload field supports click, keyboard, and drag/drop.
const uploadZone = $('[data-upload-zone]');
const uploadInput = $('[data-upload-input]');
const uploadName = $('[data-upload-name]');
const uploadForm = $('[data-upload-form]');
const showFile = (file) => {
    if (!file || !uploadName) return;
    uploadName.textContent = `${file.name} · ${(file.size / 1024).toFixed(0)} KB`;
    uploadZone?.classList.add('has-file');
    const submit = $('[data-upload-submit]', uploadForm || document);
    if (submit) submit.disabled = false;
};
uploadInput?.addEventListener('change', () => showFile(uploadInput.files?.[0]));
if (uploadZone) {
    uploadZone.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); uploadInput?.click(); }
    });
    ['dragenter', 'dragover'].forEach((name) => uploadZone.addEventListener(name, (event) => {
        event.preventDefault(); uploadZone.classList.add('is-dragging');
    }));
    ['dragleave', 'drop'].forEach((name) => uploadZone.addEventListener(name, (event) => {
        event.preventDefault(); uploadZone.classList.remove('is-dragging');
    }));
    uploadZone.addEventListener('drop', (event) => {
        const file = event.dataTransfer?.files?.[0];
        if (!file || !uploadInput) return;
        const transfer = new DataTransfer();
        transfer.items.add(file);
        uploadInput.files = transfer.files;
        showFile(file);
    });
}

// Record a call or WhatsApp hand-off without interrupting the native phone/chat action.
$$('[data-contact-event][data-activity-url]').forEach((link) => {
    link.addEventListener('click', () => {
        fetch(link.dataset.activityUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ event: link.dataset.contactEvent }),
            keepalive: true,
        }).catch(() => {});
    });
});

// On narrow screens the search becomes a compact, tap-to-expand field.
const globalSearch = $('.global-search');
const globalSearchInput = $('.global-search input');
if (globalSearch && globalSearchInput && window.matchMedia('(max-width: 620px)').matches) {
    globalSearch.addEventListener('click', (event) => {
        if (!globalSearch.classList.contains('is-search-open')) {
            event.preventDefault();
            globalSearch.classList.add('is-search-open');
            globalSearchInput.focus();
        }
    });
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.global-search') && document.activeElement !== globalSearchInput) globalSearch.classList.remove('is-search-open');
    });
}

// Shortcut focuses the global lead search.
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        if (confirmationIsOpen()) closeConfirmation(false);
        else if (teamModal && !teamModal.hidden) closeTeamModal();
        else if (leadDialogIsOpen()) closeLeadDialog();
        else setSidebar(false);
        if (filterPanel && !filterPanel.hidden) {
            filterPanel.hidden = true;
            filterPanelToggle?.setAttribute('aria-expanded', 'false');
        }
        $$('.dropdown-panel.is-open').forEach((panel) => panel.classList.remove('is-open'));
    }
    if (event.key === 'Tab') {
        const activeDialog = confirmationIsOpen()
            ? $('[role="alertdialog"]', confirmBackdrop)
            : teamModal && !teamModal.hidden
                ? $('[role="dialog"]', teamModal)
                : leadDialogIsOpen() ? $('[role="dialog"]', leadDialog) : null;
        if (!activeDialog) return;
        const focusable = $$('button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])', activeDialog).filter((item) => item.offsetParent !== null);
        if (focusable.length === 0) { event.preventDefault(); activeDialog.focus(); return; }
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    }
    const isMac = navigator.platform.toUpperCase().includes('MAC');
    if ((isMac ? event.metaKey : event.ctrlKey) && event.key.toLowerCase() === 'k') {
        const search = $('.global-search input');
        if (search) { event.preventDefault(); search.focus(); }
    }
});

// Restore focus and history when returning from an AJAX pagination/filter change.
if (leadIndex && leadFilterForm) updateLeadFormFromUrl(new URL(window.location.href));

// Format local datetime controls with a usable initial value when provided.
$$('[data-auto-focus]').forEach((element) => element.focus());

// Google Places prospecting: live result fields stay in this page only; assignments store Place IDs.
const placeSearchForm = $('[data-place-search-form]');
const placeSearchResults = $('[data-place-search-results]');
const placeSearchRows = $('[data-place-search-rows]');
const placeSearchFeedback = $('[data-place-search-feedback]');
const placeSearchMessage = $('[data-place-search-message]');
const placeSearchSpinner = $('[data-place-search-spinner]');
const placeSearchProgress = $('[data-place-search-progress]');
const placeSearchSubmit = $('[data-place-search-submit]');
const placeMoreButton = $('[data-place-more]');
const placeAssignForm = $('[data-place-assign-form]');
const placeQueue = $('[data-place-queue]');
const placeQueueRows = $('[data-place-prospect-rows]');
const placeQueuePagination = $('[data-place-pagination-wrap]');
const placeAssignmentNotice = $('[data-place-assignment-notice]');
if (placeAssignmentNotice) {
    const currentUrl = new URL(window.location.href);
    if (currentUrl.searchParams.has('places_assigned')) {
        currentUrl.searchParams.delete('places_assigned');
        window.history.replaceState(window.history.state, '', `${currentUrl.pathname}${currentUrl.search}${currentUrl.hash}`);
    }
}
let placeSearchNextState = null;
let placeSearchSeenIds = new Set();
let placeSearchActive = false;

const placeQueueSummary = (summary = {}, openOnlyResponse = false) => {
    const total = $('[data-place-stat="total"]');
    const active = $('[data-place-stat="active"]');
    const converted = $('[data-place-stat="converted"]');
    if (total && summary.total !== undefined) total.textContent = Number(summary.total).toLocaleString();
    if (active && summary.active !== undefined) active.textContent = Number(summary.active).toLocaleString();
    if (converted && summary.converted !== undefined) converted.textContent = Number(summary.converted).toLocaleString();
    const queueBadge = $('.place-queue-heading .status-pill', placeQueue || document);
    if (queueBadge && summary.total !== undefined) {
        const isOpenOnly = placeQueue?.dataset.openOnly === 'true';
        if (!isOpenOnly || openOnlyResponse) {
            const noun = isOpenOnly ? 'open' : 'assigned';
            queueBadge.textContent = `${Number(summary.total).toLocaleString()} ${noun}`;
        }
    }
};

const placeQueueHtml = (data = {}) => {
    if (placeQueueRows && typeof data.rows_html === 'string') placeQueueRows.innerHTML = data.rows_html;
    if (placeQueuePagination && typeof data.pagination_html === 'string') placeQueuePagination.innerHTML = data.pagination_html;
    placeQueueSummary(data.summary || {}, data.open_only === true);
};

const refreshPlaceSelectionControls = () => {
    if (!placeSearchResults || !placeSearchRows) return;
    const selectable = $$('[data-place-select]:not(:disabled)', placeSearchRows);
    const selected = selectable.filter((checkbox) => checkbox.checked).length;
    const selectedLabel = $('[data-place-selection-count]', placeSearchResults);
    const selectAll = $('[data-place-select-all]', placeSearchResults);
    const assignButton = $('[data-place-assign-submit]', placeSearchResults);
    const assignedTo = $('[name="assigned_to"]', placeAssignForm || document);
    const totalLabel = $('[data-place-result-count]', placeSearchResults);
    if (selectedLabel) selectedLabel.textContent = `${selected} selected`;
    if (assignButton) assignButton.disabled = selected === 0 || !assignedTo?.value;
    if (selectAll) {
        selectAll.disabled = selectable.length === 0;
        selectAll.checked = selectable.length > 0 && selectable.every((checkbox) => checkbox.checked);
        selectAll.indeterminate = selected > 0 && selected < selectable.length;
    }
    if (totalLabel) {
        const count = $$('[data-place-result-row]', placeSearchRows).length;
        totalLabel.textContent = `${count} ${count === 1 ? 'gym' : 'gyms'}`;
    }
};

const setPlaceButtonBusy = (button, busy, labelSelector = null, busyLabel = null, idleLabel = null) => {
    if (!button) return;
    button.disabled = busy;
    button.classList.toggle('is-place-loading', busy);
    button.setAttribute('aria-busy', busy ? 'true' : 'false');
    if (labelSelector) {
        const label = $(labelSelector, button);
        if (label && busyLabel && idleLabel) label.textContent = busy ? busyLabel : idleLabel;
    }
};

const renderPlaceSearchSkeleton = () => {
    if (!placeSearchRows) return;
    const fragment = document.createDocumentFragment();
    for (let index = 0; index < 4; index++) {
        const row = document.createElement('tr');
        row.className = 'place-search-skeleton-row';
        row.setAttribute('aria-hidden', 'true');
        for (let column = 0; column < 5; column++) {
            const cell = document.createElement('td');
            const block = document.createElement('span');
            block.className = `place-skeleton-block place-skeleton-column-${column + 1}`;
            cell.append(block);
            row.append(cell);
        }
        fragment.append(row);
    }
    placeSearchRows.replaceChildren(fragment);
};

const renderPlaceSearchPage = (results = [], append = false) => {
    if (!placeSearchRows) return;
    if (!append) placeSearchRows.replaceChildren();
    else $$('[data-place-prospect-empty]', placeSearchRows).forEach((row) => row.remove());
    const template = $('[data-place-result-template]');
    if (!template) return;
    results.forEach((place) => {
        if (!place?.place_id || placeSearchSeenIds.has(place.place_id)) return;
        placeSearchSeenIds.add(place.place_id);
        const fragment = document.importNode(template.content, true);
        const row = $('[data-place-result-row]', fragment);
        const checkbox = $('[data-place-select]', fragment);
        const name = $('[data-place-name]', fragment);
        const address = $('[data-place-address]', fragment);
        const mapLink = $('[data-place-map-link]', fragment);
        const status = $('[data-place-result-status]', fragment);
        if (!row || !checkbox) return;
        row.dataset.placeId = place.place_id;
        checkbox.value = place.selection_token || '';
        checkbox.dataset.placeId = place.place_id;
        checkbox.setAttribute('aria-label', `Select ${place.name || 'unnamed gym'}`);
        checkbox.disabled = Boolean(place.already_added || !place.selection_token);
        if (name) name.textContent = place.name || 'Unnamed gym';
        if (address) address.textContent = place.address || 'Address not listed';
        if (mapLink) {
            const mapUrl = safeGoogleMapUrl(place.maps_url);
            if (mapUrl) mapLink.href = mapUrl;
            else mapLink.hidden = true;
        }
        if (place.already_added) {
            row.classList.add('is-place-duplicate');
            if (status) { status.textContent = 'Already assigned'; status.classList.add('is-duplicate'); }
        } else if (status) status.textContent = 'New';
        placeSearchRows.append(fragment);
    });
    if (placeSearchSeenIds.size === 0) {
        const empty = document.createElement('tr');
        empty.className = 'place-prospect-empty';
        empty.dataset.placeProspectEmpty = '';
        const cell = document.createElement('td');
        cell.colSpan = 5;
        cell.textContent = 'No gyms matched this search page inside the selected radius.';
        empty.append(cell);
        placeSearchRows.append(empty);
    }
    refreshPlaceSelectionControls();
};

const runPlaceSearch = async (loadMore = false) => {
    if (!placeSearchForm || placeSearchActive) return;
    if (loadMore && !placeSearchNextState) return;
    placeSearchActive = true;
    if (!loadMore) {
        placeSearchSeenIds = new Set();
        placeSearchNextState = null;
        placeSearchResults.hidden = false;
        renderPlaceSearchSkeleton();
        const summary = $('[data-place-search-summary]', placeSearchResults);
        if (summary) summary.textContent = 'Searching for gyms within the selected radius…';
        placeMoreButton.disabled = true;
    }
    if (placeSearchFeedback) placeSearchFeedback.hidden = false;
    if (placeSearchSpinner) placeSearchSpinner.hidden = false;
    if (placeSearchProgress) placeSearchProgress.hidden = false;
    if (placeSearchMessage) placeSearchMessage.textContent = loadMore ? 'Loading the next Google results page…' : 'Resolving the location and searching Google Places…';
    const busyButton = loadMore ? placeMoreButton : placeSearchSubmit;
    const busyLabelSelector = loadMore ? '[data-place-more-label]' : '[data-place-search-submit-label]';
    const idleLabel = loadMore ? 'Search more results' : 'Search gyms';
    setPlaceButtonBusy(busyButton, true, busyLabelSelector, loadMore ? 'Loading next page…' : 'Searching…', idleLabel);

    try {
        const formData = new FormData(placeSearchForm);
        if (loadMore) formData.set('page_state', placeSearchNextState);
        else formData.delete('page_state');
        const data = await jsonRequest(placeSearchForm.action, { method: 'POST', body: formData, cache: 'no-store' });
        placeSearchNextState = data.next_page_state || null;
        renderPlaceSearchPage(data.results || [], loadMore);
        const summary = $('[data-place-search-summary]', placeSearchResults);
        if (summary) summary.textContent = `${data.location_label || 'Selected location'} · ${Number(data.radius_km || 0)} km radius · page ${data.page || 1} of ${data.max_pages || 2}. Select gyms to assign; Google details are not saved.`;
        if (placeSearchMessage) placeSearchMessage.textContent = data.message || 'Search complete.';
        if (placeMoreButton) placeMoreButton.disabled = !placeSearchNextState;
        if (placeSearchNextState) showAjaxToast('More Google results are available. Each additional page uses another guarded API request.');
    } catch (error) {
        if (placeSearchMessage) placeSearchMessage.textContent = errorMessage(error, 'Could not search Google Places.');
        if (!loadMore && placeSearchRows) placeSearchRows.replaceChildren();
        showAjaxToast(errorMessage(error, 'Could not search Google Places.'), 'error');
    } finally {
        placeSearchActive = false;
        if (placeSearchSpinner) placeSearchSpinner.hidden = true;
        if (placeSearchProgress) placeSearchProgress.hidden = true;
        setPlaceButtonBusy(busyButton, false, busyLabelSelector, loadMore ? 'Loading next page…' : 'Searching…', idleLabel);
        if (placeMoreButton && !placeSearchNextState) placeMoreButton.disabled = true;
        if (placeSearchFeedback) placeSearchFeedback.hidden = false;
        refreshPlaceSelectionControls();
    }
};

placeSearchForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    runPlaceSearch(false);
});
placeMoreButton?.addEventListener('click', () => runPlaceSearch(true));
placeAssignForm?.addEventListener('change', (event) => {
    if (event.target.matches('[data-place-select], [name="assigned_to"]')) refreshPlaceSelectionControls();
});

placeAssignForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const button = $('[data-place-assign-submit]', placeAssignForm);
    const selected = $$('[data-place-select]:checked', placeSearchRows || document);
    if (selected.length === 0) { showAjaxToast('Select at least one new gym before assigning.', 'error'); return; }
    setPlaceButtonBusy(button, true);
    try {
        const data = await jsonRequest(placeAssignForm.action, { method: 'POST', body: new FormData(placeAssignForm), cache: 'no-store' });
        if (data.leads_url) {
            window.location.assign(data.leads_url);
            return;
        }
        placeQueueHtml(data);
        const assignedIds = new Set(data.assigned_place_ids || []);
        $$('[data-place-result-row]', placeSearchRows || document).forEach((row) => {
            if (!assignedIds.has(row.dataset.placeId)) return;
            row.classList.add('is-place-duplicate');
            const checkbox = $('[data-place-select]', row);
            if (checkbox) { checkbox.checked = false; checkbox.disabled = true; }
            const status = $('[data-place-result-status]', row);
            if (status) { status.textContent = 'Already assigned'; status.classList.add('is-duplicate'); }
        });
        if (placeAssignForm) {
            const selectAll = $('[data-place-select-all]', placeAssignForm);
            if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
        }
        showAjaxToast(data.message || 'Prospect assignment saved.');
        refreshPlaceSelectionControls();
    } catch (error) {
        showAjaxToast(errorMessage(error, 'Could not assign those prospects.'), 'error');
    } finally {
        if (button?.isConnected) setPlaceButtonBusy(button, false);
        refreshPlaceSelectionControls();
    }
});

document.addEventListener('change', (event) => {
    if (!event.target.matches('[data-place-select-all]')) return;
    $$('[data-place-select]:not(:disabled)', placeSearchRows || document).forEach((checkbox) => { checkbox.checked = event.target.checked; });
    refreshPlaceSelectionControls();
});

const replacePlaceProspectRow = (rowHtml, oldRow) => {
    if (typeof rowHtml !== 'string' || !oldRow?.isConnected) return;
    const detailsRow = oldRow.nextElementSibling;
    if (detailsRow?.matches('[data-place-details-row]')) {
        detailsRow.hidden = true;
        $$('[data-place-details-content]', detailsRow).forEach((content) => {
            content.replaceChildren();
            content.setAttribute('aria-busy', 'false');
        });
    }
    const template = document.createElement('template');
    template.innerHTML = rowHtml.trim();
    const newRow = template.content.querySelector('[data-place-prospect-row]');
    if (newRow) oldRow.replaceWith(newRow);
};

const submitPlaceProspectForm = async (form, kind) => {
    const button = $('button[type="submit"]', form);
    setPlaceButtonBusy(button, true);
    try {
        const data = await jsonRequest(form.action, { method: 'POST', body: new FormData(form), cache: 'no-store' });
        const oldRow = form.closest('[data-place-prospect-row]');
        replacePlaceProspectRow(data.row_html, oldRow);
        if (data.summary) placeQueueSummary(data.summary);
        showAjaxToast(data.message || (kind === 'status' ? 'Prospect status updated.' : 'Prospect reassigned.'));
    } catch (error) {
        showAjaxToast(errorMessage(error, kind === 'status' ? 'Could not update this prospect.' : 'Could not reassign this prospect.'), 'error');
    } finally {
        if (button?.isConnected) setPlaceButtonBusy(button, false);
    }
};

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (form.matches('[data-place-reassign-form]')) {
        event.preventDefault();
        submitPlaceProspectForm(form, 'reassign');
    } else if (form.matches('[data-place-status-form]')) {
        event.preventDefault();
        submitPlaceProspectForm(form, 'status');
    }
});

const safeGoogleMapUrl = (value) => {
    if (typeof value !== 'string' || value === '') return null;
    try {
        const url = new URL(value, window.location.href);
        return url.protocol === 'https:' && ['google.com', 'www.google.com', 'maps.google.com'].includes(url.hostname.toLowerCase()) ? url.href : null;
    } catch (_) { return null; }
};

const renderLivePlaceDetails = (content, data = {}) => {
    const place = data.place || {};
    const grid = document.createElement('div');
    grid.className = 'place-live-details-grid';
    const addItem = (label, value, href = null) => {
        const item = document.createElement('div');
        item.className = 'place-live-details-item';
        const title = document.createElement('small');
        title.textContent = label;
        const valueElement = href ? document.createElement('a') : document.createElement('strong');
        valueElement.textContent = value || 'Not available from Google';
        if (href) { valueElement.href = href; valueElement.target = '_blank'; valueElement.rel = 'noopener noreferrer'; }
        item.append(title, valueElement);
        grid.append(item);
    };
    const phone = typeof place.phone === 'string' ? place.phone : '';
    const tel = phone.replace(/[^0-9+]/g, '');
    const telHref = /^\+?\d{7,18}$/.test(tel) ? `tel:${tel}` : null;
    const reviewCount = place.review_count === null || place.review_count === undefined ? null : Number(place.review_count).toLocaleString();
    addItem('Gym name', place.name);
    addItem('Phone', phone, telHref);
    addItem('Address', place.address);
    addItem('Google review count', reviewCount === null ? null : String(reviewCount));
    const attribution = document.createElement('div');
    attribution.className = 'place-details-attribution';
    const googleLabel = document.createElement('span');
    googleLabel.className = 'google-maps-attribution';
    googleLabel.setAttribute('translate', 'no');
    googleLabel.textContent = 'Google Maps';
    attribution.append(googleLabel);
    if (data.notice) {
        const notice = document.createElement('span');
        notice.className = 'place-details-notice';
        notice.textContent = data.notice;
        attribution.append(notice);
    }
    const mapUrl = safeGoogleMapUrl(place.maps_url);
    if (mapUrl) {
        const mapLink = document.createElement('a');
        mapLink.href = mapUrl;
        mapLink.target = '_blank';
        mapLink.rel = 'noopener noreferrer';
        mapLink.textContent = 'Open in Google Maps';
        attribution.append(mapLink);
    }
    content.replaceChildren(grid, attribution);
};

document.addEventListener('click', async (event) => {
    const detailsButton = event.target.closest('[data-place-details-button]');
    if (detailsButton) {
        event.preventDefault();
        const prospectRow = detailsButton.closest('[data-place-prospect-row]');
        const prospectId = prospectRow?.dataset.placeProspectId;
        const detailsRow = $$('[data-place-details-row]').find((row) => row.dataset.placeDetailsRow === prospectId);
        const content = $$('[data-place-details-content]').find((item) => item.dataset.placeDetailsContent === prospectId);
        if (!detailsRow || !content) return;
        if (!detailsRow.hidden) {
            detailsRow.hidden = true;
            content.replaceChildren();
            content.setAttribute('aria-busy', 'false');
            detailsButton.setAttribute('aria-expanded', 'false');
            return;
        }
        detailsRow.hidden = false;
        content.setAttribute('aria-busy', 'true');
        detailsButton.setAttribute('aria-expanded', 'true');
        detailsButton.disabled = true;
        detailsButton.classList.add('is-place-loading');
        content.innerHTML = '<div class="place-details-loading"><span class="place-progress-spinner" aria-hidden="true"><span></span></span><span>Fetching current Google details…</span></div><div class="place-details-skeleton" aria-hidden="true"><span></span><span></span><span></span><span></span></div>';
        try {
            const data = await jsonRequest(detailsButton.dataset.detailsUrl, { method: 'GET', cache: 'no-store' });
            renderLivePlaceDetails(content, data);
        } catch (error) {
            const message = document.createElement('p');
            message.className = 'place-details-error';
            message.textContent = errorMessage(error, 'Could not fetch live details. Try again later.');
            content.replaceChildren(message);
            showAjaxToast(message.textContent, 'error');
        } finally {
            content.setAttribute('aria-busy', 'false');
            if (detailsButton.isConnected) { detailsButton.disabled = false; detailsButton.classList.remove('is-place-loading'); }
        }
        return;
    }

    const pageButton = event.target.closest('[data-place-queue-page]');
    if (!pageButton || pageButton.disabled || !placeQueue) return;
    event.preventDefault();
    const page = Math.max(1, Number(pageButton.dataset.placeQueuePage || 1));
    const pageParam = placeQueue.dataset.queuePageParam || 'page';
    const queueEndpoint = new URL(placeQueue.dataset.queueUrl, window.location.href);
    const url = new URL(queueEndpoint.href);
    url.searchParams.set(pageParam, String(page));
    pageButton.disabled = true;
    pageButton.classList.add('is-place-loading');
    try {
        const data = await jsonRequest(url.href, { method: 'GET', cache: 'no-store' });
        placeQueueHtml(data);
        if (queueEndpoint.pathname === window.location.pathname) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set(pageParam, String(page));
            window.history.replaceState({ placeQueuePage: page }, '', `${currentUrl.pathname}${currentUrl.search}${currentUrl.hash}`);
        }
    } catch (error) {
        showAjaxToast(errorMessage(error, 'Could not load that prospect page.'), 'error');
    } finally {
        if (pageButton.isConnected) { pageButton.disabled = false; pageButton.classList.remove('is-place-loading'); }
    }
});

const markPlaceProspectConverted = (prospect = {}) => {
    const row = $$('[data-place-prospect-row]').find((item) => item.dataset.placeProspectId === String(prospect.id));
    if (!row) return;
    const previousStatus = $('[name="status"]', row)?.value;
    const wasActive = ['new', 'contacted', 'qualified'].includes(previousStatus);
    const detailsRow = row.nextElementSibling;
    if (detailsRow?.matches('[data-place-details-row]')) {
        detailsRow.hidden = true;
        $$('[data-place-details-content]', detailsRow).forEach((content) => {
            content.replaceChildren();
            content.setAttribute('aria-busy', 'false');
        });
    }
    if (placeQueue?.dataset.openOnly === 'true') {
        const openCount = $('[data-place-open-count]', placeQueue);
        let count = null;
        if (openCount) {
            count = Math.max(0, Number(openCount.textContent.replace(/[^0-9]/g, '')) - 1);
            openCount.textContent = `${count.toLocaleString()} open`;
        }
        detailsRow?.remove();
        row.remove();
        if (count === 0 && placeQueueRows && !$$('[data-place-prospect-row]', placeQueueRows).length) {
            const emptyRow = document.createElement('tr');
            emptyRow.className = 'place-prospect-empty';
            const cell = document.createElement('td');
            cell.colSpan = 5;
            const emptyState = document.createElement('div');
            emptyState.className = 'empty-state empty-state-compact';
            const title = document.createElement('strong');
            title.textContent = 'No open Google Maps prospects';
            const description = document.createElement('p');
            description.textContent = 'New assignments will appear here for follow-up.';
            emptyState.append(title, description);
            cell.append(emptyState);
            emptyRow.append(cell);
            placeQueueRows.replaceChildren(emptyRow);
        }
        return;
    }
    $('[data-place-details-button]', row)?.setAttribute('aria-expanded', 'false');
    const statusCell = row.children[2];
    if (statusCell) {
        const badge = document.createElement('span');
        badge.className = 'status-pill status-converted';
        badge.setAttribute('data-place-prospect-status', '');
        badge.textContent = 'Converted to lead';
        statusCell.replaceChildren(badge);
    }
    const actions = $('[data-place-details-button]', row)?.parentElement;
    if (actions && prospect.lead_url) {
        const link = document.createElement('a');
        link.className = 'btn btn-secondary btn-sm';
        link.href = prospect.lead_url;
        link.dataset.leadPreviewUrl = prospect.lead_url;
        link.innerHTML = '<svg class="icon" width="14" height="14" aria-hidden="true"><use href="#i-briefcase"></use></svg> Open lead';
        const oldConvert = $$('[data-lead-form-url]', actions)[0];
        if (oldConvert) oldConvert.replaceWith(link);
        else actions.append(link);
    }
    const active = $('[data-place-stat="active"]');
    const converted = $('[data-place-stat="converted"]');
    if (active && wasActive) active.textContent = String(Math.max(0, Number(active.textContent.replace(/,/g, '')) - 1));
    if (converted) converted.textContent = String(Number(converted.textContent.replace(/,/g, '')) + 1);
};

if (placeSearchRows) refreshPlaceSelectionControls();

// Admin-only Google key management; secrets are never rendered back into the page.
const googleSettingsForm = $('[data-google-settings-form]');
const googleKeyTestButton = $('[data-google-key-test]');
const googleKeyClearButton = $('[data-google-key-clear]');
const googleKeyState = $('[data-google-key-state]');
const googleKeyStatus = $('[data-google-key-status]');

const updateGoogleUsage = (usage = {}) => {
    Object.entries(usage.categories || {}).forEach(([sku, item]) => {
        const card = $$('[data-google-usage-sku]').find((element) => element.dataset.googleUsageSku === sku);
        if (!card) return;
        const value = $('[data-google-usage-value]', card);
        const remaining = $('[data-google-usage-remaining]', card);
        const progress = $('[data-google-usage-progress]', card);
        if (value) value.textContent = Number(item.used || 0).toLocaleString();
        if (remaining) remaining.textContent = Number(item.remaining || 0).toLocaleString();
        if (progress) {
            progress.setAttribute('aria-valuenow', String(item.used || 0));
            const bar = $('span', progress);
            if (bar) bar.style.width = `${Math.min(100, Number(item.percent || 0))}%`;
        }
    });
};

googleSettingsForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const saveButton = $('[data-google-key-save]', googleSettingsForm);
    setPlaceButtonBusy(saveButton, true);
    try {
        const data = await jsonRequest(googleSettingsForm.action, { method: 'POST', body: new FormData(googleSettingsForm), cache: 'no-store' });
        const keyField = $('[name="api_key"]', googleSettingsForm);
        if (keyField) keyField.value = '';
        if (googleKeyState) { googleKeyState.className = 'status-pill status-converted'; googleKeyState.textContent = 'Key saved'; }
        if (googleKeyStatus) googleKeyStatus.textContent = `Configured key: ${data.key_hint || '••••'}. Connection has not been tested after this key change.`;
        if (googleKeyTestButton) googleKeyTestButton.disabled = false;
        if (googleKeyClearButton) googleKeyClearButton.hidden = false;
        showAjaxToast(data.message || 'Google Places key saved securely.');
    } catch (error) {
        const firstValidationMessage = Object.values(error.payload?.errors || {}).flat()[0];
        showAjaxToast(firstValidationMessage || errorMessage(error, 'Could not save the Google Places key.'), 'error');
    } finally {
        if (saveButton?.isConnected) setPlaceButtonBusy(saveButton, false);
    }
});

googleKeyTestButton?.addEventListener('click', async () => {
    setPlaceButtonBusy(googleKeyTestButton, true);
    if (googleKeyStatus) googleKeyStatus.textContent = 'Testing the server-side key… this makes one Geocoding request and one Places Text Search Pro request.';
    try {
        const data = await jsonRequest(googleKeyTestButton.dataset.testUrl, { method: 'POST', cache: 'no-store' });
        if (googleKeyState) { googleKeyState.className = 'status-pill status-converted'; googleKeyState.textContent = 'Connection verified'; }
        if (googleKeyStatus) googleKeyStatus.textContent = `${data.message} Last verified ${data.verified_at}.`;
        updateGoogleUsage(data.usage || {});
        showAjaxToast(data.message || 'Google Places connection verified.');
    } catch (error) {
        if (googleKeyState) { googleKeyState.className = 'status-pill status-orange'; googleKeyState.textContent = 'Needs attention'; }
        if (googleKeyStatus) googleKeyStatus.textContent = errorMessage(error, 'Google Places connection could not be verified.');
        showAjaxToast(errorMessage(error, 'Google Places connection could not be verified.'), 'error');
    } finally {
        if (googleKeyTestButton?.isConnected) setPlaceButtonBusy(googleKeyTestButton, false);
    }
});

googleKeyClearButton?.addEventListener('click', async () => {
    const accepted = await askForConfirmation({
        title: 'Disconnect Google Places?',
        message: 'This removes the encrypted key from GymXBook. Saved Place IDs and assignments remain; live Google details will stop working until a key is added again.',
        acceptLabel: 'Disconnect key',
    });
    if (!accepted) return;
    setPlaceButtonBusy(googleKeyClearButton, true);
    try {
        const data = await jsonRequest(googleKeyClearButton.dataset.deleteUrl, { method: 'DELETE', cache: 'no-store' });
        if (googleKeyState) { googleKeyState.className = 'status-pill status-orange'; googleKeyState.textContent = 'Not connected'; }
        if (googleKeyStatus) googleKeyStatus.textContent = 'Google Places is disconnected. Add an Admin key to restore live search and details.';
        if (googleKeyTestButton) googleKeyTestButton.disabled = true;
        googleKeyClearButton.hidden = true;
        const keyField = $('[name="api_key"]', googleSettingsForm || document);
        if (keyField) keyField.value = '';
        showAjaxToast(data.message || 'Google Places disconnected.');
    } catch (error) {
        showAjaxToast(errorMessage(error, 'Could not disconnect Google Places.'), 'error');
    } finally {
        if (googleKeyClearButton?.isConnected) setPlaceButtonBusy(googleKeyClearButton, false);
    }
});
