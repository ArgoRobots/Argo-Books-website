<?php
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../config/pricing.php';

// Real numbers drive both the copy and the calculator, so the page can never
// drift from actual pricing. Commission is 50% for the first 12 months.
$pricing         = get_pricing_config();
$premium_monthly = (float) $pricing['premium_monthly_price'];   // e.g. 10.00
$premium_yearly  = (float) $pricing['premium_yearly_price'];    // e.g. 100.00
$rate            = 0.50;
$commission_rate_pct = (int) round($rate * 100);
$window_months   = 12;
$c_month         = $premium_monthly * $rate;                    // per-month commission, monthly plan
$c_year          = $premium_yearly * $rate;                     // per-customer commission, yearly plan

$fmt = function (float $n): string {
    // Whole dollars for these round figures; drop trailing .00.
    return '$' . number_format($n, ($n == floor($n)) ? 0 : 2);
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <meta name="description" content="Join the Argo Books affiliate program and earn <?php echo $commission_rate_pct; ?>% recurring commission for every customer you refer, every month for their first year. Free to join, real-time dashboard, PayPal payouts.">
    <meta name="keywords" content="Argo Books affiliate program, accounting software affiliate, recurring commission, refer and earn">

    <meta property="og:title" content="Affiliate Program: Earn <?php echo $commission_rate_pct; ?>% Recurring | Argo Books">
    <meta property="og:description" content="Earn <?php echo $commission_rate_pct; ?>% commission for every customer you refer to Argo Books, every month for their first year.">
    <meta property="og:url" content="https://argorobots.com/affiliates/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Affiliate Program: Earn <?php echo $commission_rate_pct; ?>% Recurring | Argo Books">
    <meta name="twitter:description" content="Earn <?php echo $commission_rate_pct; ?>% commission for every customer you refer to Argo Books, every month for their first year.">

    <link rel="canonical" href="https://argorobots.com/affiliates/">

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Affiliate Program: Earn <?php echo $commission_rate_pct; ?>% Recurring | Argo Books</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600;700&display=swap">

    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
</head>

<body class="aff-page">
    <header>
        <?php include __DIR__ . '/../resources/header/header.php'; ?>
    </header>

    <main>
        <!-- ============ HERO: thesis + live earnings calculator ============ -->
        <section class="aff-hero">
            <div class="aff-hero-grid" aria-hidden="true"></div>
            <div class="aff-hero-inner">
                <div class="aff-hero-copy">
                    <h1 class="aff-headline">One referral.<br><span class="aff-headline-accent">Twelve paydays.</span></h1>
                    <p class="aff-lede">
                        Earn <strong><?php echo $commission_rate_pct; ?>% of every payment</strong> from customers you send to
                        Argo Books, every month for their first year. The more you share, the more your
                        commission stacks up.
                    </p>
                    <div class="aff-hero-actions">
                        <a href="../community/affiliate/" class="aff-btn aff-btn-primary">
                            <span>Become an affiliate</span>
                            <?php echo svg_icon('arrow-right', 18); ?>
                        </a>
                        <a href="#how" class="aff-btn aff-btn-ghost">See how it works</a>
                    </div>
                    <ul class="aff-trust">
                        <li><?php echo svg_icon('circle-check', 16); ?> Free to join</li>
                        <li><?php echo svg_icon('circle-check', 16); ?> Paid via PayPal</li>
                        <li><?php echo svg_icon('circle-check', 16); ?> Cancel anytime</li>
                    </ul>
                </div>

                <!-- Signature: the page does the accounting for you. -->
                <div class="aff-calc" id="affCalc"
                     data-cmonth="<?php echo $c_month; ?>"
                     data-cyear="<?php echo $c_year; ?>"
                     data-monthly="<?php echo $premium_monthly; ?>"
                     data-yearly="<?php echo $premium_yearly; ?>">
                    <div class="aff-calc-head">
                        <span class="aff-calc-title">Your earnings</span>
                        <div class="aff-calc-toggle" role="tablist" aria-label="Subscription plan">
                            <button type="button" class="aff-calc-tab is-active" role="tab" aria-selected="true" data-plan="monthly">Monthly plan</button>
                            <button type="button" class="aff-calc-tab" role="tab" aria-selected="false" data-plan="yearly">Yearly plan</button>
                        </div>
                    </div>

                    <div class="aff-calc-readout">
                        <span class="aff-calc-big" id="affBig" aria-live="polite"><?php echo $fmt($c_month * 10); ?></span>
                        <span class="aff-calc-cur">CAD</span>
                        <span class="aff-calc-unit" id="affUnit">/ month</span>
                    </div>
                    <p class="aff-calc-sub" id="affSub">
                        <?php echo $fmt($c_month * 12 * 10); ?> over their first year
                    </p>

                    <div class="aff-calc-control">
                        <div class="aff-calc-control-label">
                            <label for="affRefs">Customers you refer</label>
                            <output id="affRefsOut" for="affRefs">10</output>
                        </div>
                        <input type="range" id="affRefs" min="1" max="50" value="10" step="1"
                               aria-label="Number of customers you refer">
                        <div class="aff-calc-scale"><span>1</span><span>50+</span></div>
                    </div>

                    <p class="aff-calc-fine" id="affFine">
                        Based on Argo Premium at <?php echo $fmt($premium_monthly); ?>/mo. You keep
                        <?php echo $commission_rate_pct; ?>% (<?php echo $fmt($c_month); ?>) of every monthly payment,
                        for 12 months. Figures in CAD.
                    </p>
                </div>
            </div>
        </section>

        <!-- ============ HOW IT WORKS: a real three-step sequence ============ -->
        <section class="aff-steps" id="how">
            <div class="aff-container">
                <div class="aff-section-head aff-reveal">
                    <span class="aff-kicker">How it works</span>
                    <h2>From link to payout in three steps</h2>
                </div>
                <ol class="aff-step-list">
                    <li class="aff-step aff-reveal">
                        <span class="aff-step-num">01</span>
                        <h3>Apply in minutes</h3>
                        <p>Create a free Argo account and tell us how you plan to promote. Most applications are reviewed within a day or two.</p>
                    </li>
                    <li class="aff-step aff-reveal">
                        <span class="aff-step-num">02</span>
                        <h3>Share your link</h3>
                        <p>Get a unique referral link and drop it in your videos, posts, newsletter, or client emails. Every click is tracked back to you.</p>
                    </li>
                    <li class="aff-step aff-reveal">
                        <span class="aff-step-num">03</span>
                        <h3>Get paid every month</h3>
                        <p>Earn <?php echo $commission_rate_pct; ?>% of every payment your referrals make for their first 12 months, sent straight to your PayPal.</p>
                    </li>
                </ol>
            </div>
        </section>

        <!-- ============ WHY JOIN: the terms, stated plainly ============ -->
        <section class="aff-why">
            <div class="aff-container">
                <div class="aff-section-head aff-reveal">
                    <span class="aff-kicker">Why join</span>
                    <h2>Built to actually be worth your while</h2>
                </div>
                <div class="aff-why-grid">
                    <div class="aff-card aff-reveal">
                        <span class="aff-card-figure"><?php echo $commission_rate_pct; ?>%</span>
                        <h3>Generous commission</h3>
                        <p>Half of every payment is yours, one of the most generous rates in accounting software.</p>
                    </div>
                    <div class="aff-card aff-reveal">
                        <span class="aff-card-figure">12<span class="aff-card-figure-unit">mo</span></span>
                        <h3>A full year per customer</h3>
                        <p>You earn on every payment for twelve months, renewals included, not just the first sale.</p>
                    </div>
                    <div class="aff-card aff-reveal">
                        <span class="aff-card-icon"><?php echo svg_icon('refresh', 26); ?></span>
                        <h3>Recurring, not one-time</h3>
                        <p>Monthly subscribers pay you every single month. Referrals compound as they add up.</p>
                    </div>
                    <div class="aff-card aff-reveal">
                        <span class="aff-card-icon"><?php echo svg_icon('analytics', 26); ?></span>
                        <h3>Real-time dashboard</h3>
                        <p>Watch clicks, signups, and commission update live, so you always know what's working.</p>
                    </div>
                    <div class="aff-card aff-reveal">
                        <span class="aff-card-icon"><?php echo svg_icon('dollar', 26); ?></span>
                        <h3>Fast PayPal payouts</h3>
                        <p>Commission is paid to your PayPal on a monthly cadence, in the currency you expect.</p>
                    </div>
                    <div class="aff-card aff-reveal">
                        <span class="aff-card-icon"><?php echo svg_icon('circle-check', 26); ?></span>
                        <h3>Free to join</h3>
                        <p>No cost, no quotas, no catch. Apply, get approved, and start sharing your link.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ WHO IT'S FOR ============ -->
        <section class="aff-audience">
            <div class="aff-container">
                <div class="aff-section-head aff-reveal">
                    <span class="aff-kicker">Who it's for</span>
                    <h2>If your audience runs a small business, this is for you</h2>
                </div>
                <div class="aff-audience-grid">
                    <div class="aff-audience-item aff-reveal"><?php echo svg_icon('bank', 22); ?><span>Bookkeepers &amp; accountants setting up client books</span></div>
                    <div class="aff-audience-item aff-reveal"><?php echo svg_icon('play', 22); ?><span>Creators &amp; YouTubers in finance and small business</span></div>
                    <div class="aff-audience-item aff-reveal"><?php echo svg_icon('pencil', 22); ?><span>Bloggers &amp; reviewers writing software roundups</span></div>
                    <div class="aff-audience-item aff-reveal"><?php echo svg_icon('users', 22); ?><span>Consultants &amp; agencies onboarding new clients</span></div>
                </div>
            </div>
        </section>

        <!-- ============ FAQ ============ -->
        <section class="aff-faq">
            <div class="aff-container aff-faq-inner">
                <div class="aff-section-head aff-reveal">
                    <span class="aff-kicker">Questions</span>
                    <h2>The details, up front</h2>
                </div>
                <div class="aff-faq-list">
                    <details class="aff-faq-item aff-reveal">
                        <summary>How much do I actually earn?<?php echo svg_icon('chevron-down', 20); ?></summary>
                        <p><?php echo $commission_rate_pct; ?>% of every payment for the first 12 months of each subscription. On monthly Premium that's <?php echo $fmt($c_month); ?> per customer, every month. On yearly Premium it's <?php echo $fmt($c_year); ?> per customer.</p>
                    </details>
                    <details class="aff-faq-item aff-reveal">
                        <summary>Who can join?<?php echo svg_icon('chevron-down', 20); ?></summary>
                        <p>Anyone with an audience of small-business owners or self-employed people. You don't need a huge following, just a genuine way to reach people who'd benefit from Argo Books.</p>
                    </details>
                    <details class="aff-faq-item aff-reveal">
                        <summary>When and how do I get paid?<?php echo svg_icon('chevron-down', 20); ?></summary>
                        <p>Commission is paid to your PayPal on a monthly cadence once you've earned a payout. Your dashboard shows exactly what you've earned, what's been paid, and what's still owed.</p>
                    </details>
                    <details class="aff-faq-item aff-reveal">
                        <summary>How are referrals tracked?<?php echo svg_icon('chevron-down', 20); ?></summary>
                        <p>Your unique link tags every visitor you send. When they sign up and subscribe, the sale is credited to you automatically, and it keeps earning on their renewals for a full year.</p>
                    </details>
                    <details class="aff-faq-item aff-reveal">
                        <summary>Does it cost anything to join?<?php echo svg_icon('chevron-down', 20); ?></summary>
                        <p>No. Joining is free, there are no quotas, and there's nothing to lose. Create a free Argo account and apply.</p>
                    </details>
                </div>
            </div>
        </section>

        <!-- ============ FINAL CTA: flows into the dark footer ============ -->
        <section class="aff-final">
            <div class="aff-container aff-final-inner aff-reveal">
                <h2>Start earning from every referral</h2>
                <p>Join the Argo Books affiliate program and turn your audience into recurring income.</p>
                <a href="../community/affiliate/" class="aff-btn aff-btn-primary aff-btn-lg">
                    <span>Become an affiliate</span>
                    <?php echo svg_icon('arrow-right', 18); ?>
                </a>
                <p class="aff-final-note">Free to join. Sign in or create a free Argo account to apply.</p>
            </div>
        </section>
    </main>

    <footer class="footer">
        <?php include __DIR__ . '/../resources/footer/footer.php'; ?>
    </footer>

    <script>
        (function () {
            var calc = document.getElementById('affCalc');
            if (!calc) return;

            var cMonth  = parseFloat(calc.dataset.cmonth);
            var cYear   = parseFloat(calc.dataset.cyear);
            var monthly = parseFloat(calc.dataset.monthly);
            var yearly  = parseFloat(calc.dataset.yearly);

            var refs   = document.getElementById('affRefs');
            var refsOut = document.getElementById('affRefsOut');
            var bigEl  = document.getElementById('affBig');
            var unitEl = document.getElementById('affUnit');
            var subEl  = document.getElementById('affSub');
            var fineEl = document.getElementById('affFine');
            var tabs   = calc.querySelectorAll('.aff-calc-tab');

            var plan = 'monthly';
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            // Exact money: whole dollars stay clean, half-dollars show cents,
            // so the per-payment figure ($7.50) can never contradict the total.
            function money(n) {
                var r = Math.round(n * 100) / 100;
                var opts = Number.isInteger(r) ? {} : { minimumFractionDigits: 2, maximumFractionDigits: 2 };
                return '$' + r.toLocaleString('en-CA', opts);
            }

            // Count-up so the ledger figure animates as inputs change. Frames
            // round to whole dollars to avoid jittery decimals; the final value
            // snaps to the exact amount.
            var rafId = null;
            function setBig(target) {
                if (reduce) { bigEl.textContent = money(target); return; }
                var start = parseFloat((bigEl.textContent || '0').replace(/[^0-9.]/g, '')) || 0;
                var t0 = null, dur = 450;
                if (rafId) cancelAnimationFrame(rafId);
                function tick(ts) {
                    if (t0 === null) t0 = ts;
                    var p = Math.min((ts - t0) / dur, 1);
                    var eased = 1 - Math.pow(1 - p, 3);
                    var val = start + (target - start) * eased;
                    bigEl.textContent = (p < 1) ? ('$' + Math.round(val).toLocaleString('en-CA')) : money(target);
                    if (p < 1) rafId = requestAnimationFrame(tick);
                }
                rafId = requestAnimationFrame(tick);
            }

            function render() {
                var n = parseInt(refs.value, 10);
                refsOut.textContent = n;
                if (plan === 'monthly') {
                    setBig(cMonth * n);
                    unitEl.textContent = '/ month';
                    subEl.textContent = money(cMonth * 12 * n) + ' over their first year';
                    fineEl.innerHTML = 'Based on Argo Premium at ' + money(monthly) + '/mo. You keep 50% ('
                        + money(cMonth) + ') of every monthly payment, for 12 months. Figures in CAD.';
                } else {
                    setBig(cYear * n);
                    unitEl.textContent = '/ year';
                    subEl.textContent = money(cYear) + ' per yearly subscriber, paid upfront';
                    fineEl.innerHTML = 'Based on Argo Premium at ' + money(yearly) + '/yr. You keep 50% ('
                        + money(cYear) + ') of each yearly subscription. Figures in CAD.';
                }
            }

            refs.addEventListener('input', render);
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    if (tab.classList.contains('is-active')) return;
                    tabs.forEach(function (t) {
                        t.classList.remove('is-active');
                        t.setAttribute('aria-selected', 'false');
                    });
                    tab.classList.add('is-active');
                    tab.setAttribute('aria-selected', 'true');
                    plan = tab.dataset.plan;
                    render();
                });
            });

            render();

            // Scroll reveal, reduced-motion safe.
            var reveals = document.querySelectorAll('.aff-reveal');
            if (reduce || !('IntersectionObserver' in window)) {
                reveals.forEach(function (el) { el.classList.add('is-in'); });
            } else {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
                reveals.forEach(function (el) { io.observe(el); });
            }
        })();
    </script>
</body>

</html>
