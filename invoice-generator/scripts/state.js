// invoice-generator/scripts/state.js
// Invoice state model + localStorage persistence.

const STORAGE_KEY = 'argobooks.invoiceGenerator.draft';

export function emptyState() {
  return {
    template: 'classic',
    country: 'US',
    currency: 'USD',
    locale: 'en-US',
    logoDataUrl: null,
    logoWidth: null,
    logoHeight: null,
    from: '',
    billTo: '',
    shipTo: null, // null means "Ship To" not shown; '' or content means shown
    invoiceNumber: '1',
    date: todayISO(),
    paymentTerms: 'Net 30',
    dueDate: addDaysISO(todayISO(), 30),
    poNumber: '',
    lineItems: [{ description: '', quantity: 1, rate: 0 }],
    discount: null, // null means hidden; { mode: 'percent'|'fixed', value: number } when shown
    shipping: null, // same pattern: null or { value: number }
    taxRatePercent: 0,
    taxMode: 'exclusive',   // 'exclusive' | 'inclusive' (legacy, kept for math)
    taxRateMode: 'percent', // 'percent' (rate * subtotal) | 'fixed' (flat dollar amount)
    notes: '',
    terms: '',
    amountPaid: 0,
    // Every visible piece of text on the invoice surface is user-editable.
    // The default values are the standard invoice labels; the user can
    // rename any of them (e.g. "Bill To" -> "Client", "Tax" -> "GST").
    labels: {
      businessTitle: '',
      documentTitle: 'INVOICE',
      from: 'From',
      billTo: 'Bill To',
      shipTo: 'Ship To',
      date: 'Date',
      paymentTerms: 'Payment Terms',
      dueDate: 'Due Date',
      poNumber: 'PO Number',
      description: 'Description',
      quantity: 'Quantity',
      rate: 'Rate',
      amount: 'Amount',
      notes: 'Notes',
      terms: 'Terms',
      subtotal: 'Subtotal',
      tax: 'Tax',
      shipping: 'Shipping',
      discount: 'Discount',
      total: 'Total',
      amountPaid: 'Amount Paid',
      balanceDue: 'Balance Due',
    },
  };
}

function todayISO() {
  return new Date().toISOString().slice(0, 10);
}

function addDaysISO(iso, days) {
  const d = new Date(iso);
  d.setDate(d.getDate() + days);
  return d.toISOString().slice(0, 10);
}

// Priority: localStorage draft > nicheDefaults > empty state.
// (A.18 introduces nicheDefaults; A.7 ships the parameter as a no-op-friendly default.)
export function loadDraft(nicheDefaults) {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (raw) return { ...emptyState(), ...JSON.parse(raw) };
  } catch (_e) {
    // fall through to defaults
  }
  if (nicheDefaults && typeof nicheDefaults === 'object') {
    return { ...emptyState(), ...nicheDefaults };
  }
  return emptyState();
}

export function saveDraft(state) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    return { ok: true };
  } catch (e) {
    // Likely QuotaExceededError (a huge logo + many line items can push past ~5MB).
    // Fallback: drop the logo (the heaviest field) and try again.
    if (e && e.name === 'QuotaExceededError' && state.logoDataUrl) {
      try {
        const trimmed = { ...state, logoDataUrl: null, logoWidth: null, logoHeight: null };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(trimmed));
        return { ok: true, droppedLogo: true };
      } catch (_e2) {
        // fall through
      }
    }
    return { ok: false, error: e?.name || 'unknown' };
  }
}

export function clearDraft() {
  try {
    localStorage.removeItem(STORAGE_KEY);
  } catch (_e) {
    // ignore
  }
}
