<?php
/**
 * Centralized SVG icon library.
 *
 * Usage:
 *   <?= svg_icon('check', 20) ?>
 *   <?= svg_icon('chevron-down', 24, 'dropdown-arrow') ?>
 *   <?= svg_icon('analytics', null, '', 1.5) ?>
 */
function svg_icon($name, $size = null, $class = '', $stroke_width = null, $extra_attrs = '') {
    static $icons = null;
    if ($icons === null) {
        $icons = _svg_icon_definitions();
    }

    if (!isset($icons[$name])) {
        return '<!-- unknown icon: ' . htmlspecialchars($name) . ' -->';
    }

    $icon = $icons[$name];
    $body = $icon['body'];
    $type = $icon['type'] ?? 'stroked';
    $vb   = $icon['viewBox'] ?? '0 0 24 24';

    $parts = ['<svg'];

    if ($size) {
        $parts[] = 'width="' . $size . '" height="' . $size . '"';
    }

    $parts[] = 'viewBox="' . $vb . '"';

    if ($type === 'stroked') {
        $sw = $stroke_width ?? $icon['stroke_width'] ?? '2';
        $parts[] = 'fill="none" stroke="currentColor" stroke-width="' . $sw . '"';
    } elseif ($type === 'filled') {
        $parts[] = 'fill="currentColor"';
    }
    // type 'plain' adds no fill/stroke attrs

    if ($class !== '') {
        $parts[] = 'class="' . htmlspecialchars($class) . '"';
    }

    if ($extra_attrs !== '') {
        $parts[] = $extra_attrs;
    }

    return implode(' ', $parts) . '>' . $body . '</svg>';
}

function _svg_icon_definitions() {
    return [
        // ── Checkmarks ───────────────────────────────────────────────
        'check' => [
            'body' => '<polyline points="20,6 9,17 4,12"/>',
        ],
        'check-alt' => [
            'body' => '<path d="M20 6L9 17l-5-5"/>',
        ],
        'check-pricing' => [
            'body' => '<path d="M5 13l4 4L19 7"/>',
            'type' => 'plain',
        ],
        'check-rounded' => [
            'body' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>',
        ],
        'circle-check' => [
            'body' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/>',
        ],

        // ── Arrows & Chevrons ────────────────────────────────────────
        'chevron-right' => [
            'body' => '<path d="M9 18l6-6-6-6"/>',
        ],
        'chevron-left' => [
            'body' => '<path d="M15 18l-6-6 6-6"/>',
        ],
        'chevron-down' => [
            'body' => '<polyline points="6,9 12,15 18,9"/>',
        ],
        'arrow-right' => [
            'body' => '<path d="M5 12h14M12 5l7 7-7 7"/>',
        ],
        'arrow-back' => [
            'body' => '<path d="M19 12H5M12 19l-7-7 7-7"/>',
        ],
        'arrow-right-sm' => [
            'body' => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
        ],
        'chevron-left-sm' => [
            'body' => '<polyline points="15,18 9,12 15,6"/>',
        ],
        'chevron-right-sm' => [
            'body' => '<polyline points="9,18 15,12 9,6"/>',
        ],
        'vote-up' => [
            'body' => '<path d="M12 19V5M5 12l7-7 7 7"/>',
        ],
        'vote-down' => [
            'body' => '<path d="M12 5v14M5 12l7 7 7-7"/>',
        ],

        // ── Navigation & UI ──────────────────────────────────────────
        'search' => [
            'body' => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        ],
        'home' => [
            'body' => '<path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>',
            'type' => 'filled',
        ],
        'flag' => [
            'body' => '<path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>',
        ],
        'edit' => [
            'body' => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
        ],
        'eye' => [
            'body' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        ],
        'help-circle' => [
            'body' => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="17" r="1.25" fill="currentColor" stroke="none"/>',
        ],
        'alert-circle' => [
            'body' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
        ],
        'x-circle' => [
            'body' => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
        ],
        'map-pin' => [
            'body' => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>',
        ],
        'clock' => [
            'body' => '<circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>',
        ],
        'refresh' => [
            'body' => '<path d="M21 12a9 9 0 11-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/>',
        ],

        // ── Communication ────────────────────────────────────────────
        'mail' => [
            'body' => '<path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
        ],
        'chat' => [
            'body' => '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v10z"/>',
        ],
        'message-circle' => [
            'body' => '<path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>',
        ],
        'bell' => [
            'body' => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
        ],

        // ── Business & Finance ───────────────────────────────────────
        'document' => [
            'body' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        ],
        'document-lines' => [
            'body' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>',
        ],
        'receipt' => [
            'body' => '<path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1z"/><path d="M8 10h8M8 14h4"/>',
        ],
        'dollar' => [
            'body' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
        ],
        'analytics' => [
            'body' => '<path d="M3 3v18h18"/><path d="M18 9l-5 5-4-4-3 3"/>',
        ],
        'trending-up' => [
            'body' => '<polyline points="23,6 13.5,15.5 8.5,10.5 1,18"/><polyline points="17,6 23,6 23,12"/>',
        ],
        'receipt-scan' => [
            'body' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h6"/><circle cx="17" cy="14" r="3"/>',
        ],
        'receipt-scan-detail' => [
            'body' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h6"/><circle cx="17" cy="14" r="3"/><path d="M17 17v-1.5"/>',
        ],

        // ── Inventory & Products ─────────────────────────────────────
        'package' => [
            'body' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27,6.96 12,12.01 20.73,6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
        ],
        'calendar' => [
            'body' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        ],
        'users' => [
            'body' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        ],

        // ── Security ─────────────────────────────────────────────────
        'shield' => [
            'body' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        ],
        'lock' => [
            'body' => '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        ],

        // ── Actions ──────────────────────────────────────────────────
        'download' => [
            'body' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/>',
        ],
        'bolt' => [
            'body' => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
        ],
        'pencil' => [
            'body' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>',
        ],
        'book' => [
            'body' => '<path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/><path d="M8 7h8M8 11h8M8 15h5"/>',
        ],
        'biometric-clock' => [
            'body' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M12 6v6l4 2"/>',
        ],
        'subscription' => [
            'body' => '<path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
        ],
        'loading' => [
            'body' => '<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>',
        ],
        'document-upload' => [
            'body' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 12 15 15"/>',
        ],
        'document-download' => [
            'body' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><polyline points="9 15 12 18 15 15"/>',
        ],

        // ── About/Values (unique illustrations) ─────────────────────
        'innovation' => [
            'body' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        ],
        'user-focused' => [
            'body' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
        ],
        'reliability' => [
            'body' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
        ],
        'arrow-top-right' => [
            'body' => '<path d="M7 17l9.2-9.2M17 17V7H7"/>',
        ],

        // ── Sidebar / Documentation ──────────────────────────────────
        'monitor' => [
            'body' => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
        ],
        'play' => [
            'body' => '<polygon points="5 3 19 12 5 21 5 3"/>',
        ],
        'table' => [
            'body' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
        ],
        'globe' => [
            'body' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        ],
        'translate' => [
            'body' => '<path d="m5 8 6 6M4 14l6-6 2-3M2 5h12M7 2h1M22 22l-5-10-5 10M14 18h6"/>',
        ],
        'key' => [
            'body' => '<path d="m21 2-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/>',
        ],
        'database' => [
            'body' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/>',
        ],
        'eye-off' => [
            'body' => '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>',
        ],
        'house' => [
            'body' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        ],

        // ── Documents (additional) ─────────────────────────────────────
        'book-open' => [
            'body' => '<path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
        ],
        'clipboard-check' => [
            'body' => '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M9 14l2 2 4-4"/>',
        ],
        'document-plus' => [
            'body' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>',
        ],
        'pie-chart' => [
            'body' => '<path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/>',
        ],
        'credit-card' => [
            'body' => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        ],
        'book-question' => [
            'body' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="12" y1="6" x2="12" y2="10"/><line x1="12" y1="14" x2="12.01" y2="14"/>',
        ],
        'save' => [
            'body' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
        ],

        // ── Charts & Data ──────────────────────────────────────────────
        'bar-chart' => [
            'body' => '<line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/>',
        ],
        'shopping-bag' => [
            'body' => '<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>',
        ],

        // ── Navigation & UI (additional) ───────────────────────────────
        'play-circle' => [
            'body' => '<circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/>',
        ],
        'grid' => [
            'body' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
        ],
        'x' => [
            'body' => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
        ],
        'star' => [
            'body' => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>',
        ],

        // ── People & Communication (additional) ────────────────────────
        'user' => [
            'body' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>',
        ],
        'thumbs-up' => [
            'body' => '<path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>',
        ],
        'send' => [
            'body' => '<line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>',
        ],
        'mail-alt' => [
            'body' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
        ],

        // ── Alerts (additional) ────────────────────────────────────────
        'alert-triangle' => [
            'body' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        ],

        // ── Calendar (additional) ──────────────────────────────────────
        'calendar-dots' => [
            'body' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>',
        ],

        // ── Package (additional) ───────────────────────────────────────
        'package-detail' => [
            'body' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="7.5 4.21 12 6.81 16.5 4.21"/><polyline points="7.5 19.79 7.5 14.6 3 12"/><polyline points="21 12 16.5 14.6 16.5 19.79"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
        ],

        // ── Security (additional) ──────────────────────────────────────
        'shield-check' => [
            'body' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
        ],
        'camera' => [
            'body' => '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>',
        ],
        'trash' => [
            'body' => '<path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
        ],

        // ── Geometric shapes ───────────────────────────────────────────
        'shape-square' => [
            'body' => '<rect x="3" y="3" width="18" height="18" rx="2"/>',
        ],
        'shape-circle' => [
            'body' => '<circle cx="12" cy="12" r="10"/>',
        ],
        'shape-hexagon' => [
            'body' => '<polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"/>',
        ],

        // ── Platform logos ─────────────────────────────────────────────
        'windows' => [
            'body' => '<path d="M0 3.449L9.75 2.1v9.451H0m10.949-9.602L24 0v11.4H10.949M0 12.6h9.75v9.451L0 20.699M10.949 12.6H24V24l-12.9-1.801"/>',
            'type' => 'filled',
        ],
        'apple' => [
            'body' => '<path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>',
            'type' => 'filled',
        ],

        // ── Filled variants ───────────────────────────────
        'shield-filled' => [
            'body' => '<path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>',
            'type' => 'filled',
        ],
        'bar-chart-filled' => [
            'body' => '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>',
            'type' => 'filled',
        ],
        'globe-filled' => [
            'body' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>',
            'type' => 'filled',
        ],
        'users-filled' => [
            'body' => '<path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>',
            'type' => 'filled',
        ],
        'play-filled' => [
            'body' => '<path d="M8 5v14l11-7z"/>',
            'type' => 'filled',
        ],
        'check-filled' => [
            'body' => '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>',
            'type' => 'filled',
        ],
        'credit-card-filled' => [
            'body' => '<path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>',
            'type' => 'filled',
        ],
        'document-filled' => [
            'body' => '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>',
            'type' => 'filled',
        ],
        'circle-check-sm' => [
            'body' => '<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        'info' => [
            'body' => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4" stroke-linecap="round"/><circle cx="12" cy="8" r="1.25" fill="currentColor" stroke="none"/>',
        ],
    ];
}
