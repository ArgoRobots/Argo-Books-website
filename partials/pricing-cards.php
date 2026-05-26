<?php
/**
 * Shared pricing cards partial.
 *
 * Renders the billing-cycle toggle plus the Free and Premium pricing cards.
 * Included from both the landing page and the pricing page so the two stay
 * in sync.
 *
 * Optional caller-provided overrides (set $pricingCardsOptions before include):
 *   - free_cta_url, free_cta_text
 *   - premium_cta_url, premium_cta_text
 */

require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../resources/icons.php';

$pcPricing        = get_pricing_config();
$pcPlans          = get_plan_features();
$pcMonthly        = $pcPricing['premium_monthly_price'];
$pcYearly         = $pcPricing['premium_yearly_price'];
$pcMonthlyTotal   = $pcMonthly * 12;
$pcSavings        = $pcMonthlyTotal - $pcYearly;
$pcSavingsPct     = $pcMonthlyTotal > 0 ? (int) round(($pcSavings / $pcMonthlyTotal) * 100) : 0;
$pcYearlyPerMonth = $pcMonthly > 0 ? $pcYearly / 12 : 0;

$pcOpts        = $pricingCardsOptions ?? [];
$pcFreeUrl     = $pcOpts['free_cta_url']     ?? '/downloads/';
$pcFreeText    = $pcOpts['free_cta_text']    ?? 'Get Started';
$pcPremiumUrl  = $pcOpts['premium_cta_url']  ?? '/pricing/premium/';
$pcPremiumText = $pcOpts['premium_cta_text'] ?? 'Buy now';
?>
<div class="pcards-toggle" role="tablist" aria-label="Billing cycle">
    <button type="button" class="pcards-cycle-btn active" data-cycle="yearly" role="tab" aria-selected="true">
        Yearly
        <span class="pcards-save">Save <?= $pcSavingsPct ?>%</span>
    </button>
    <button type="button" class="pcards-cycle-btn" data-cycle="monthly" role="tab" aria-selected="false">Monthly</button>
</div>

<div class="pcards-grid" data-active-cycle="yearly">
    <!-- Free -->
    <div class="pcard pcard-free">
        <div class="pcard-head">
            <span class="pcard-tag">Free Forever</span>
            <div class="pcard-amount">
                <span class="pcard-currency">$</span>
                <span class="pcard-number">0</span>
            </div>
        </div>
        <ul class="pcard-features">
            <?php foreach ($pcPlans['free']['features'] as $feature): ?>
            <li>
                <?= svg_icon('check', 20) ?>
                <span><?= render_feature_label($feature) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <a href="<?= htmlspecialchars($pcFreeUrl) ?>" class="pcard-cta pcard-cta-free"><?= htmlspecialchars($pcFreeText) ?></a>
    </div>

    <!-- Premium -->
    <div class="pcard pcard-premium">
        <div class="pcard-head">
            <span class="pcard-tag pcard-tag-premium">Premium</span>
            <div class="pcard-amount" data-cycle="monthly">
                <span class="pcard-currency">$</span>
                <span class="pcard-number"><?= number_format($pcMonthly, 0) ?></span>
                <span class="pcard-period">CAD/month</span>
            </div>
            <div class="pcard-amount pcard-amount-yearly" data-cycle="yearly">
                <div class="pcard-strike">$<?= number_format($pcMonthly, 0) ?>/month</div>
                <div class="pcard-amount-row">
                    <span class="pcard-currency">$</span>
                    <span class="pcard-number"><?= number_format($pcYearlyPerMonth, 2) ?></span>
                    <span class="pcard-period">CAD/month</span>
                </div>
            </div>
            <p class="pcard-alt" data-cycle="monthly">Billed monthly</p>
            <p class="pcard-alt" data-cycle="yearly">Billed annually</p>
        </div>
        <ul class="pcard-features">
            <?php foreach ($pcPlans['premium']['features'] as $feature): ?>
            <li>
                <?= svg_icon('check', 20) ?>
                <span><?= render_feature_label($feature) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <a href="<?= htmlspecialchars($pcPremiumUrl) ?>" class="pcard-cta pcard-cta-premium"><?= htmlspecialchars($pcPremiumText) ?></a>
    </div>
</div>
