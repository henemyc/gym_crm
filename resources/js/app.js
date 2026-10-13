const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];

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
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setSidebar(false);
        $$('.dropdown-panel.is-open').forEach((panel) => panel.classList.remove('is-open'));
    }
});

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
    const button = form.querySelector('button[type="submit"]:not([form])') || form.querySelector('button[type="submit"]');
    if (!button || button.disabled) return;
    button.classList.add('is-loading');
    button.setAttribute('aria-busy', 'true');
    button.disabled = true;
});

// Toasts dismiss themselves and can be closed manually.
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

// Lead list bulk selection.
const selectAll = $('[data-select-all]');
const rowChecks = $$('[data-row-select]');
const bulkToolbar = $('[data-bulk-toolbar]');
const selectedCount = $('[data-selected-count]');
const syncSelection = () => {
    const selected = rowChecks.filter((checkbox) => checkbox.checked).length;
    if (selectedCount) selectedCount.textContent = String(selected);
    bulkToolbar?.classList.toggle('is-visible', selected > 0);
    if (selectAll) {
        selectAll.checked = rowChecks.length > 0 && selected === rowChecks.length;
        selectAll.indeterminate = selected > 0 && selected < rowChecks.length;
    }
};
selectAll?.addEventListener('change', () => {
    rowChecks.forEach((checkbox) => {
        checkbox.checked = selectAll.checked;
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    });
    syncSelection();
});
rowChecks.forEach((checkbox) => checkbox.addEventListener('change', syncSelection));
$('[data-clear-selection]')?.addEventListener('click', () => {
    rowChecks.forEach((checkbox) => {
        checkbox.checked = false;
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    });
    if (selectAll) selectAll.checked = false;
    syncSelection();
});

// Destructive actions use the app's accessible confirmation dialog instead of native browser prompts.
const confirmBackdrop = $('[data-confirm-dialog]');
$$('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (form.dataset.confirmed === 'true') { delete form.dataset.confirmed; return; }
        event.preventDefault();
        if (!confirmBackdrop) return;
        const message = $('[data-confirm-message]', confirmBackdrop);
        const accept = $('[data-confirm-accept]', confirmBackdrop);
        if (message) message.textContent = form.dataset.confirm || 'Are you sure you want to continue?';
        if (accept) accept.textContent = form.dataset.confirmAccept || 'Continue';
        confirmBackdrop.hidden = false;
        confirmBackdrop.setAttribute('aria-hidden', 'false');
        const acceptOnce = () => {
            accept?.removeEventListener('click', acceptOnce);
            form.dataset.confirmed = 'true';
            form.requestSubmit?.();
            confirmBackdrop.hidden = true;
            confirmBackdrop.setAttribute('aria-hidden', 'true');
        };
        accept?.addEventListener('click', acceptOnce, { once: true });
    });
});

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
        const token = $('meta[name="csrf-token"]')?.content;
        fetch(link.dataset.activityUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '' },
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
    const isMac = navigator.platform.toUpperCase().includes('MAC');
    if ((isMac ? event.metaKey : event.ctrlKey) && event.key.toLowerCase() === 'k') {
        const search = $('.global-search input');
        if (search) { event.preventDefault(); search.focus(); }
    }
});

// Format local datetime controls with a usable initial value when provided.
$$('[data-auto-focus]').forEach((element) => element.focus());
