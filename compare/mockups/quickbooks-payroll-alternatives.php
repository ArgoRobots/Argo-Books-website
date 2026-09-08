<?php
// compare/mockups/quickbooks-payroll-alternatives.php
//
// Decorative cost chart for the QuickBooks Payroll comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// $qbo_/$qbp_ variables declared in the matching data file; both are included
// at global scope by the template.
//
// Stacked rather than grouped: the QuickBooks number is a bundle plus a fee per head.

$employees = 5;
$per_total = $qbp_per * $employees;
$stack     = $qbp_base + $per_total;

$scale = 380 / max($stack, 1); // Widest bar fills the usable width.

$w_payroll = $qbp_base * $scale;
$w_heads   = $per_total * $scale;
$w_argo    = max(4, $argo_monthly * $scale);
?>
<!-- Decorative cost comparison. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClipQbp"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                                <!-- One rounded shape for the whole stack; a per-segment rx
                                     rounds the internal joins and notches the bar. -->
                                <clipPath id="dmClipQbpStack"><rect x="40" y="290" width="<?= $w_payroll + $w_heads ?>" height="34" rx="5"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClipQbp)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">Per month, with 5 employees</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">Cheapest bundle with payroll, then a fee for every person.</text>

                                <!-- Argo Books -->
                                <text x="40" y="150" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="40" y="162" width="<?= $w_argo ?>" height="34" rx="5" fill="#3f63e8"/>
                                <text x="<?= 40 + $w_argo + 12 ?>" y="184" font-size="14" font-weight="700" fill="#0f172a">$<?= $argo_monthly ?></text>
                                <text x="40" y="218" font-size="12" fill="#64748b">Books and Canadian payroll, one charge</text>

                                <!-- QuickBooks stack -->
                                <text x="40" y="278" font-size="13" font-weight="600" fill="#0f172a">QuickBooks Online + Payroll</text>
                                <g clip-path="url(#dmClipQbpStack)">
                                    <rect x="40" y="290" width="<?= $w_payroll ?>" height="34" fill="#f87171"/>
                                    <rect x="<?= 40 + $w_payroll ?>" y="290" width="<?= $w_heads ?>" height="34" fill="#ef4444"/>
                                    <!-- Hairline join, so the bundle and the head fee stay legible. -->
                                    <rect x="<?= 40 + $w_payroll - 1 ?>" y="290" width="2" height="34" fill="#ffffff"/>
                                </g>
                                <text x="<?= 40 + ($w_payroll + $w_heads) + 12 ?>" y="312" font-size="14" font-weight="700" fill="#0f172a">$<?= number_format($stack) ?></text>

                                <!-- Stack legend -->
                                <rect x="40" y="346" width="11" height="11" rx="3" fill="#f87171"/>
                                <text x="58" y="356" font-size="12" fill="#334155">Books and payroll $<?= $qbp_base ?></text>
                                <rect x="240" y="346" width="11" height="11" rx="3" fill="#ef4444"/>
                                <text x="258" y="356" font-size="12" fill="#334155">5 &times; $<?= $qbp_per ?> per employee</text>

                                <!-- Divider + footnote -->
                                <line x1="40" y1="386" x2="600" y2="386" stroke="#f1f5f9" stroke-width="1"/>
                                <text x="40" y="416" font-size="12" fill="#64748b">Typical published rates on the cheapest plans that allow unlimited pay runs.</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
