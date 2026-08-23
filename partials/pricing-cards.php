<?php
/**
 * Shared pricing cards partial.
 *
 * Renders the billing-cycle toggle plus the Free and Premium pricing cards.
 * Included from both the landing page and the pricing page so the two stay
 * in sync.
 *
 * Monthly is the default cycle. The Premium CTA carries the selected cycle
 * through as ?billing=monthly|yearly so /pricing/premium/ can hand it straight
 * to checkout instead of asking again. The page-level toggle script rewrites
 * the href on [data-premium-cta] when the cycle changes.
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
$pcYearlyPerMonth = $pcMonthly > 0 ? $pcYearly / 12 : 0;

$pcOpts        = $pricingCardsOptions ?? [];
$pcFreeUrl     = $pcOpts['free_cta_url']     ?? '/downloads/';
$pcFreeText    = $pcOpts['free_cta_text']    ?? 'Get Started';
$pcPremiumUrl  = $pcOpts['premium_cta_url']  ?? '/pricing/premium/';
$pcPremiumText = $pcOpts['premium_cta_text'] ?? 'Buy now';

// Base URL without a billing param; the toggle script appends the live cycle.
$pcPremiumBase = $pcPremiumUrl;
?>
<div class="pcards-toggle" role="tablist" aria-label="Billing cycle">
    <button type="button" class="pcards-cycle-btn active" data-cycle="monthly" role="tab" aria-selected="true">Monthly</button>
    <button type="button" class="pcards-cycle-btn" data-cycle="yearly" role="tab" aria-selected="false">
        Annual
        <span class="pcards-save">Save $<?= number_format($pcSavings, 0) ?></span>
    </button>
</div>

<!-- Each card's direct children are its six aligned rows: name, pitch, price,
     CTA, list lead-in, features. The grid lines them up across both cards via
     subgrid (see pricing-cards.css), so the CTAs stay level no matter how many
     lines the pitch wraps to. Keep the two cards' child counts equal, hence the
     hidden lead-in on the Free card. -->
<div class="pcards-grid" data-active-cycle="monthly">
    <!-- Free -->
    <div class="pcard pcard-free">
        <p class="pcard-name"><span class="pcard-chip">FREE</span> Plan</p>
        <p class="pcard-pitch">Just starting out, or you only need the basics? This is the place.</p>
        <div class="pcard-price-block">
            <div class="pcard-price">
                <div class="pcard-amount-row">
                    <span class="pcard-currency">$</span>
                    <span class="pcard-number">0</span>
                </div>
                <!-- Invisible copy of the Premium card's struck-out price line,
                     so the two price blocks are the same height. -->
                <div class="pcard-strike pcard-strike-spacer" aria-hidden="true">&nbsp;</div>
            </div>
            <p class="pcard-alt">Free forever</p>
        </div>
        <a href="<?= htmlspecialchars($pcFreeUrl) ?>" class="pcard-cta pcard-cta-free"><?= htmlspecialchars($pcFreeText) ?></a>
        <p class="pcard-plus pcard-plus-spacer" aria-hidden="true">&nbsp;</p>
        <ul class="pcard-features">
            <?php foreach ($pcPlans['free']['features'] as $feature): ?>
            <li>
                <?= svg_icon('check', 20) ?>
                <span><?= render_feature_label($feature) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Premium -->
    <div class="pcard pcard-premium">
        <span class="pcard-badge">Recommended</span>
        <p class="pcard-name"><span class="pcard-chip pcard-chip-premium">PREMIUM</span> Plan</p>
        <p class="pcard-pitch">Want unlimited invoicing, bigger monthly limits, and forecasts you can act on? Go Premium.</p>
        <div class="pcard-price-block">
            <!-- One price block for both cycles rather than two swapped blocks:
                 only the number changes, and the struck-out line below always
                 occupies its row (hidden on monthly), so switching cycle never
                 shifts the CTA underneath. -->
            <div class="pcard-price">
                <div class="pcard-amount-row">
                    <span class="pcard-currency">$</span>
                    <span class="pcard-number" data-cycle="monthly"><?= number_format($pcMonthly, 0) ?></span>
                    <span class="pcard-number" data-cycle="yearly"><?= number_format($pcYearlyPerMonth, 2) ?></span>
                    <span class="pcard-period">CAD/month</span>
                </div>
                <div class="pcard-strike" data-cycle="yearly">$<?= number_format($pcMonthly, 0) ?>/month</div>
                <div class="pcard-strike pcard-strike-spacer" data-cycle="monthly" aria-hidden="true">$<?= number_format($pcMonthly, 0) ?>/month</div>
            </div>
            <p class="pcard-alt" data-cycle="monthly">Billed monthly</p>
            <!-- The headline figure is the per-month equivalent so it compares
                 like-for-like against the monthly plan, but the amount actually
                 charged today has to be stated or checkout comes as a surprise. -->
            <p class="pcard-alt" data-cycle="yearly">Billed annually at $<?= number_format($pcYearly, 0) ?> <?= htmlspecialchars($pcPricing['currency']) ?></p>
        </div>
        <a href="<?= htmlspecialchars($pcPremiumBase) ?>?billing=monthly"
           class="pcard-cta pcard-cta-premium"
           data-premium-cta
           data-base-url="<?= htmlspecialchars($pcPremiumBase) ?>"><?= htmlspecialchars($pcPremiumText) ?></a>
        <p class="pcard-plus">Everything in Free, plus&hellip;</p>
        <ul class="pcard-features">
            <?php foreach ($pcPlans['premium']['features'] as $feature): ?>
            <li>
                <?= svg_icon('check', 20) ?>
                <span><?= render_feature_label($feature) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
