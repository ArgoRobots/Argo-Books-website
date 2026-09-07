<?php
// compare/mockups/argo-books-vs-wagepoint.php
//
// Decorative cost chart for the Wagepoint comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// $wp_* variables declared in compare/data/argo-books-vs-wagepoint.php; both
// files are included at global scope by the template.
//
// Grouped by headcount because the argument is "flat against sloped", which
// only shows across several headcounts.

$groups = [2, 5, 10, 20];

$costs = [];
foreach ($groups as $n) {
    $costs[$n] = payroll_monthly_cost('wagepoint', 'unlimited', $n);
}

$max   = max(max($costs), $argo_monthly);
$baseY = 350;                 // Bar baseline.
$scale = 205 / max($max, 1);  // Tallest bar lands ~205px above the baseline.
?>
<!-- Decorative cost comparison. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClipWp"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClipWp)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">Payroll cost per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">One price stays flat. The other is charged per person.</text>

                                <!-- Legend -->
                                <rect x="40" y="98" width="11" height="11" rx="3" fill="#3f63e8"/>
                                <text x="58" y="108" font-size="12" fill="#334155">Argo Books Premium</text>
                                <rect x="196" y="98" width="11" height="11" rx="3" fill="#ef4444"/>
                                <text x="214" y="108" font-size="12" fill="#334155">Wagepoint Unlimited</text>

                                <!-- Baseline -->
                                <line x1="40" y1="<?= $baseY ?>" x2="600" y2="<?= $baseY ?>" stroke="#e2e8f0" stroke-width="1"/>

<?php
$x = 70;
foreach ($groups as $n):
    $argoH = max(2, $argo_monthly * $scale);
    $compH = max(2, $costs[$n] * $scale);
?>
                                <!-- <?= $n ?> employees -->
                                <rect x="<?= $x ?>" y="<?= $baseY - $argoH ?>" width="42" height="<?= $argoH ?>" rx="4" fill="#3f63e8"/>
                                <text x="<?= $x + 21 ?>" y="<?= $baseY - $argoH - 9 ?>" text-anchor="middle" font-size="12" font-weight="700" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <rect x="<?= $x + 50 ?>" y="<?= $baseY - $compH ?>" width="42" height="<?= $compH ?>" rx="4" fill="#ef4444"/>
                                <text x="<?= $x + 71 ?>" y="<?= $baseY - $compH - 9 ?>" text-anchor="middle" font-size="12" font-weight="700" fill="#0f172a">$<?= number_format($costs[$n]) ?></text>

                                <text x="<?= $x + 46 ?>" y="<?= $baseY + 22 ?>" text-anchor="middle" font-size="12" font-weight="600" fill="#475569"><?= $n ?> staff</text>
<?php
    $x += 135;
endforeach;
?>

                                <!-- Footnote -->
                                <text x="40" y="416" font-size="12" fill="#64748b">Wagepoint Unlimited: $<?= $wp_unl_base ?> base + $<?= $wp_unl_per ?> per employee. Argo Books: flat.</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
