<?php
// late-fee-calculator/index.php
// Interest and fees on an overdue invoice. Tier 1 (see read-me/Tool page
// standards.md). Math in shared/scripts/business-calcs.js.

require_once __DIR__ . '/../shared/_base.php';
require_once __DIR__ . '/../shared/currencies.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/schema.php';

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../statistics.php';
    track_page_view('latefeecalc_tool');
}

$page_title = 'Late Fee Calculator: Interest on an Overdue Invoice | Argo Books';
$page_description = 'Free late payment calculator. Work out the interest and fees owed on an overdue invoice, simple or compounding, and what the total now comes to.';
$canonical_url = 'https://argorobots.com/late-fee-calculator/';

$tools_back = ['href' => INVGEN_BASE . '/tools/', 'label' => 'All tools'];
$ref_qs = '?source=latefeecalc-tool&amp;utm_source=late-fee-calculator&amp;utm_medium=tool&amp;utm_campaign=phase1';

$faqs = [
    [
        'q' => 'How much can I charge for a late payment?',
        'a' => 'Whatever your contract or invoice terms say, within the limits your jurisdiction sets. A common commercial term is 1.5% a month, which is 18% a year, plus a fixed administrative fee. Some countries also give you a statutory right to interest and a fixed recovery amount even when your terms are silent, so check what applies where you trade.',
    ],
    [
        'q' => 'Do I have to have late fees in my contract to charge them?',
        'a' => 'To rely on your own rate, yes. The fee has to be stated somewhere the customer agreed to, which usually means your terms of business or the invoice itself, and it needs to have been visible before the work was done rather than added afterwards. Several jurisdictions provide a statutory fallback rate for late commercial payments regardless, but it is normally lower than what you would have set yourself.',
    ],
    [
        'q' => 'What is the difference between simple and compound interest here?',
        'a' => 'Simple interest charges the annual rate against the original invoice, prorated by days overdue. Compounding adds each month\'s interest to the balance so the next month is charged on a slightly larger figure. Over 90 days at 12% the difference is small, but on an invoice left unpaid for a year it becomes significant. Only compound if your terms actually say so.',
    ],
    [
        'q' => 'Should I actually charge a late fee?',
        'a' => 'The fee matters less than the fact that it exists. Most small businesses never invoice the interest they are owed, but stating it clearly on every invoice measurably changes when people pay, because it moves you up the queue. A reasonable approach is to state the fee, apply it consistently to persistent late payers, and waive it for a good customer who pays a week late once.',
    ],
    [
        'q' => 'When does an invoice actually become overdue?',
        'a' => 'The day after the due date on the invoice. If you have not stated a due date, most jurisdictions default to something like 30 days from the invoice or delivery date, which is rarely what you wanted. Always put an explicit due date on the invoice, because "payable on receipt" is ambiguous enough to be argued with.',
    ],
    [
        'q' => 'Can I charge a flat fee instead of interest?',
        'a' => 'Yes, and many businesses do because it is simpler to explain and to collect. A flat administrative charge per overdue invoice, or per reminder sent, is common. Some places allow a statutory fixed recovery sum on top of interest. Combining a modest flat fee with interest is usually more effective than either alone.',
    ],
    [
        'q' => 'What happens if the customer just refuses to pay the fee?',
        'a' => 'In practice you decide whether the amount is worth the relationship and the effort. Interest is usually a lever rather than a revenue line: it gives you something to waive as a gesture when agreeing a payment plan. If an invoice is genuinely being ignored, the fee matters far less than escalating promptly through reminders, a formal demand, and then a small claims process.',
    ],
    [
        'q' => 'How do I stop invoices going late in the first place?',
        'a' => 'Invoice the same day the work finishes, state a specific due date, offer a payment method that takes seconds, and send a reminder before the due date rather than after. Most late payment is not refusal, it is an invoice sitting in someone\'s inbox. The businesses that get paid on time are the ones that make paying easy and follow up early.',
    ],
];

$page_schema_json = json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'SoftwareApplication',
            'name' => 'Late Fee Calculator',
            'applicationCategory' => 'FinanceApplication',
            'operatingSystem' => 'Web',
            'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
            'creator' => ['@id' => 'https://argorobots.com/#organization'],
            'url' => $canonical_url,
        ],
        argo_faq_schema_node($faqs),
    ],
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

$breadcrumb_schema_json = argo_breadcrumb_schema([
    'Home' => '/',
    'Free Tools' => '/tools/',
    'Late Fee Calculator' => $canonical_url,
]);

$extra_head = '<link rel="stylesheet" href="' . INVGEN_BASE . '/shared/styles/calculator.css">'
    . '<script>window.ARGO_CURRENCY_LOCALES = ' . json_encode(argo_currency_locales(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';</script>';
$extra_scripts = '<script type="module" src="' . INVGEN_BASE . '/late-fee-calculator/scripts/main.js"></script>';

ob_start();
?>
<div class="calc-app">

  <section class="site-hero">
    <h1 class="site-hero-title">Late Fee Calculator</h1>
    <p class="site-hero-tagline">Work out the interest and fees owed on an overdue invoice, and what the total comes to today.</p>
  </section>

  <aside class="page-banner" role="complementary">
    <span class="page-banner-text">Argo Books tracks which invoices are overdue and how long by, so you chase before it gets to this.</span>
    <a class="page-banner-link" data-pitch-placement="banner" href="<?= INVGEN_BASE ?>/features/invoicing/<?= $ref_qs ?>&amp;placement=banner">See how <span aria-hidden="true">&rarr;</span></a>
  </aside>

  <div class="calc-grid">
    <form class="calc-form" autocomplete="off" aria-label="Late fee inputs">

      <div class="calc-field">
        <label for="lf-amount">Unpaid invoice amount</label>
        <div class="calc-money">
          <span class="calc-money-affix">$</span>
          <input id="lf-amount" data-lf="amount" type="number" inputmode="decimal" min="0" step="0.01" placeholder="0.00">
        </div>
      </div>

      <div class="calc-field">
        <label for="lf-days">Days overdue</label>
        <input id="lf-days" data-lf="daysOverdue" type="number" inputmode="numeric" min="0" step="1" placeholder="0">
        <p class="calc-hint">Counted from the day after the due date, not the invoice date.</p>
      </div>

      <div class="calc-field">
        <label for="lf-rate">Annual interest rate</label>
        <div class="calc-money calc-money-suffix">
          <input id="lf-rate" data-lf="annualRate" type="number" inputmode="decimal" min="0" step="0.5" value="18">
          <span class="calc-money-affix calc-money-affix-right">%</span>
        </div>
        <div class="calc-presets" role="group" aria-label="Common rates">
          <button type="button" class="calc-preset-btn" data-lf-preset="8">Statutory-ish <span class="calc-preset-pct">8%</span></button>
          <button type="button" class="calc-preset-btn" data-lf-preset="12">1% a month <span class="calc-preset-pct">12%</span></button>
          <button type="button" class="calc-preset-btn" data-lf-preset="18">1.5% a month <span class="calc-preset-pct">18%</span></button>
          <button type="button" class="calc-preset-btn" data-lf-preset="24">2% a month <span class="calc-preset-pct">24%</span></button>
        </div>
        <p class="calc-hint">Use the rate your terms actually state. Invoice terms are usually quoted per month; the annual figure is twelve times it.</p>
      </div>

      <div class="calc-field">
        <label for="lf-flat">Fixed administrative fee</label>
        <div class="calc-money">
          <span class="calc-money-affix">$</span>
          <input id="lf-flat" data-lf="flatFee" type="number" inputmode="decimal" min="0" step="1" placeholder="0.00">
        </div>
        <p class="calc-hint">A one-off charge per overdue invoice, if your terms allow one.</p>
      </div>

      <div class="calc-field">
        <label for="lf-compound">How interest accrues</label>
        <select id="lf-compound" data-lf="compound">
          <option value="simple">Simple, on the original amount</option>
          <option value="monthly">Compounding monthly</option>
        </select>
        <p class="calc-hint">Only choose compounding if your terms say so.</p>
      </div>

      <div class="calc-field">
        <label for="lf-currency">Currency</label>
        <select id="lf-currency" data-lf="currency"><?= argo_currency_options() ?></select>
      </div>
    </form>

    <div class="calc-results" data-lf-results aria-live="polite">
      <div class="calc-headline">
        <span class="calc-headline-label">Total now due</span>
        <span class="calc-headline-amount" data-lf="totalDue">$0.00</span>
        <span class="calc-headline-sub" data-lf="totalSub">Enter the invoice amount and how late it is</span>
      </div>

      <dl class="calc-breakdown">
        <div class="calc-breakdown-row"><dt>Original invoice</dt><dd data-lf="amountOut">$0.00</dd></div>
        <div class="calc-group">Charges</div>
        <div class="calc-breakdown-row calc-row-cost"><dt>Interest <span class="calc-band-rate" data-lf="rateNote"></span></dt><dd data-lf="interest">$0.00</dd></div>
        <div class="calc-breakdown-row calc-row-cost" data-lf-row="flat" hidden><dt>Administrative fee</dt><dd data-lf="flatFeeOut">$0.00</dd></div>
        <div class="calc-breakdown-row calc-row-subtotal"><dt>Total charges</dt><dd data-lf="totalFees">$0.00</dd></div>
        <div class="calc-breakdown-row calc-row-rate"><dt>Growing by</dt><dd data-lf="daily">$0.00 a day</dd></div>
      </dl>

      <div class="calc-callout" data-lf-context hidden>
        <span class="calc-headline-label">Put in context</span>
        <p data-lf="contextText"></p>
      </div>
    </div>
  </div>

  <article class="calc-content">

    <section>
      <h2>How late payment interest is worked out</h2>
      <p>Almost every set of invoice terms states an annual percentage, then charges it for the portion of the year the invoice has been late.</p>
      <p class="calc-formula">Interest = amount &times; annual rate &times; (days overdue &divide; 365)</p>
      <p>So a $5,000 invoice at 8%, sixty days late, has accrued <strong>$65.75</strong>. Add a $40 administrative fee and the customer owes <strong>$5,105.75</strong>.</p>
      <p>Terms are usually quoted monthly, because "1.5% a month" sounds smaller than "18% a year". They are the same thing, and the annual figure is what goes in the box above.</p>
    </section>

    <section>
      <h2>Simple or compounding?</h2>
      <p>Simple interest charges the rate against the original invoice for as long as it is late. Compounding rolls each month's interest into the balance, so the following month is charged on a slightly bigger number.</p>
      <p>Over 90 days at 12% the difference is a few dollars. Over two years on a large invoice it stops being trivial. Use compounding only if your terms explicitly say interest compounds, because charging it when your terms are silent is the kind of detail that undermines an otherwise straightforward demand.</p>
    </section>

    <section>
      <h2>What you are allowed to charge</h2>
      <p>Two separate things determine this, and it is worth knowing which you are relying on.</p>
      <ul>
        <li><strong>Your own terms.</strong> A rate you set, which has to have been agreed before the work, normally through terms of business or a clearly stated line on the invoice. Adding a fee after the fact is not enforceable.</li>
        <li><strong>Statutory rights.</strong> Many jurisdictions give businesses a right to interest on late commercial payments even when the contract is silent, sometimes with a fixed recovery sum on top. The statutory rate is usually lower than a rate you would have chosen, so having your own terms is worth the ten minutes.</li>
      </ul>
      <p>Consumer sales are often treated differently from business-to-business ones, and some places cap what can be charged. Check what applies where you trade before relying on a high rate.</p>
    </section>

    <section>
      <h2>The fee is a lever, not a revenue line</h2>
      <p>Most small businesses never actually invoice the interest they are owed, and that is a reasonable choice. What matters is that the term exists and is visible, because it moves you up the queue when someone is deciding which of a dozen invoices to pay this week.</p>
      <p>A practical policy that keeps both the money and the relationship:</p>
      <ol class="calc-list">
        <li><strong>State the fee on every invoice</strong>, whether or not you intend to charge it.</li>
        <li><strong>Send a reminder before the due date.</strong> Most lateness is an invoice lost in an inbox, not a refusal.</li>
        <li><strong>Apply the fee to repeat offenders</strong>, consistently, so it means something.</li>
        <li><strong>Waive it as a gesture</strong> when agreeing a payment plan. Something you can give away is worth having.</li>
      </ol>
    </section>

    <section>
      <h2>What late payment really costs you</h2>
      <p>The interest is the visible part. The rest is the hours spent chasing, the cash you could not spend on stock, and the overdraft you used instead. On thin margins, one large invoice sixty days late can be the difference between a comfortable quarter and a stressful one.</p>
      <p>That is why the fix is nearly always earlier in the process: invoice immediately, state a real due date, make paying take seconds, and follow up before it is late rather than after.</p>
    </section>

    <section>
      <h2>Where a calculator stops helping</h2>
      <p>This works out one invoice. It cannot tell you which customers are habitually late, how much you are owed right now, or which invoices need chasing today.</p>
      <p><a class="calc-link" href="<?= INVGEN_BASE ?>/features/invoicing/<?= $ref_qs ?>&amp;placement=content">Argo Books</a> tracks what has been invoiced, what has been paid, and what is overdue and by how long, so chasing is a two-minute job rather than an afternoon with a spreadsheet. It runs on your own computer and it is free to start.</p>
    </section>

  </article>

  <section class="calc-faqs">
    <h2>Frequently asked questions</h2>
    <?= argo_faq_grid($faqs) ?>
  </section>

  <p class="calc-disclaimer">General information, not legal advice. What you may charge depends on your contract and your jurisdiction. Check local rules or take advice before relying on a rate.</p>

</div>
<?php
$body_content = ob_get_clean();

include __DIR__ . '/../shared/layout.php';
