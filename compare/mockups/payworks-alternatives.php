<?php
// compare/mockups/payworks-alternatives.php
//
// Decorative cost chart for the Payworks comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// $pw_* variables declared in the matching data file; both are included at
// global scope by the template.
//
// Payworks publishes no rate card, so the chart labels its own figures
// indicative rather than looking more certain than the data behind it.

$groups = [2, 5, 10];

$costs = [];
foreach ($groups as $n) {
    $costs[$n] = payroll_monthly_cost('payworks', 'payroll', $n);
}

$max   = max(max($costs), $argo_monthly);
$baseY = 340;
$scale = 190 / max($max, 1);
?>
<!-- Decorative cost comparison. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClipPw"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClipPw)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">Payroll cost per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">A published flat price, against a quoted per-head rate.</text>

                                <!-- Legend -->
                                <rect x="40" y="98" width="11" height="11" rx="3" fill="#3f63e8"/>
                                <text x="58" y="108" font-size="12" fill="#334155">Argo Books Premium</text>
                                <rect x="196" y="98" width="11" height="11" rx="3" fill="#ef4444"/>
                                <text x="214" y="108" font-size="12" fill="#334155">Payworks (indicative)</text>

                                <!-- Baseline -->
                                <line x1="40" y1="<?= $baseY ?>" x2="600" y2="<?= $baseY ?>" stroke="#e2e8f0" stroke-width="1"/>

<?php
$x = 100;
foreach ($groups as $n):
    $argoH = max(2, $argo_monthly * $scale);
    $compH = max(2, $costs[$n] * $scale);
?>
                                <!-- <?= $n ?> employees -->
                                <rect x="<?= $x ?>" y="<?= $baseY - $argoH ?>" width="46" height="<?= $argoH ?>" rx="4" fill="#3f63e8"/>
                                <text x="<?= $x + 23 ?>" y="<?= $baseY - $argoH - 9 ?>" text-anchor="middle" font-size="12" font-weight="700" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <rect x="<?= $x + 54 ?>" y="<?= $baseY - $compH ?>" width="46" height="<?= $compH ?>" rx="4" fill="#ef4444"/>
                                <text x="<?= $x + 77 ?>" y="<?= $baseY - $compH - 9 ?>" text-anchor="middle" font-size="12" font-weight="700" fill="#0f172a">$<?= number_format($costs[$n]) ?></text>

                                <text x="<?= $x + 50 ?>" y="<?= $baseY + 22 ?>" text-anchor="middle" font-size="12" font-weight="600" fill="#475569"><?= $n ?> staff</text>
<?php
    $x += 160;
endforeach;
?>

                                <!-- Footnote -->
                                <text x="40" y="400" font-size="12" fill="#64748b">Payworks quotes per client and does not publish a rate card. Figures shown are</text>
                                <text x="40" y="418" font-size="12" fill="#64748b">typical small-business rates (about $<?= $pw_base ?> + $<?= $pw_per ?> per employee). Get your own quote.</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
