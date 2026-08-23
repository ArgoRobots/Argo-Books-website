<?php
// articles/data/bookkeeping-for-electricians.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-electricians',

  'h1' => 'Bookkeeping for electricians',

  'meta_title' => 'Bookkeeping for Electricians: A Practical Guide | Argo Books',

  'meta_description' => 'Bookkeeping for electricians: quoting vs invoicing, deposits and progress billing, van stock and materials, job costing, deductions, and a monthly routine.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 56,

  'published' => '2026-07-22',

  'updated' => '2026-07-22',

  'reading_time_min' => 10,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Electrical work has a bookkeeping problem that office businesses never face: your money is scattered across a van full of parts, a stack of supply-house receipts, quotes that may or may not turn into jobs, and invoices where the materials and the labor are tangled together. A sparky can be booked solid for months and still not know which jobs actually made money, because the panel upgrade that felt profitable quietly ate three extra trips to the supplier and a breaker that never got billed.</p>
<p>This guide covers the bookkeeping that actually matters for electricians, whether you're a one-person operation or running a small crew: how quoting and invoicing should connect, how to bill bigger jobs in stages with a deposit up front, how to track the wire and breakers you buy per job versus the stock riding around in your van, how to see which kinds of work pay best, which trade costs are deductible, and a short monthly routine that keeps it all straight without eating your evenings.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Quotes, invoices, deposits, and progress billing',
      'anchor' => 'quotes-invoices-deposits',
      'html' => <<<'HTML'
<p>The first thing to get straight is that a quote and an invoice are different documents doing different jobs, and your books only care about one of them. A quote is a promise of a price; nothing has been earned yet, so it doesn't touch your income. The invoice is where money becomes real. Keep the two connected, though: when a quote is accepted, the invoice should be built from it, so the price you promised is the price you bill and nothing gets forgotten between the estimate and the final bill. The extra outlet the homeowner added mid-job, the upgraded panel they chose, the second trip because the inspector wanted a change: each of those should land on the invoice, and the quote is your baseline for spotting them.</p>
<p>On small service calls, one invoice at the end is fine. On bigger jobs, a rewire, a panel change, a fit-out that runs for weeks, billing everything at the end is how electricians end up floating thousands of dollars of materials on their own credit. Two habits fix that:</p>
<ul>
<li><strong>Take a deposit.</strong> On any job with serious material costs, ask for a percentage up front before you buy the gear. A deposit isn't rude, it's standard, and it means the supplier bill isn't coming out of your pocket while you wait to finish. In your books, a deposit is money received against work not yet done, so record it against the job rather than treating it as pure profit the day it lands.</li>
<li><strong>Bill in stages.</strong> For multi-week jobs, agree on progress payments tied to milestones: rough-in done, inspection passed, final fixtures in. Invoice each stage as it's reached. Your cash comes in as the work goes out, and a dispute at the end puts only the last payment at risk instead of the whole job.</li>
</ul>
<p>If you're still writing invoices by hand or in a document template, a proper invoicing tool takes minutes to learn and keeps the numbering, taxes, and totals consistent. Argo Books handles deposits and partial payments on an invoice, so a staged job doesn't need side math to track what's been paid and what's still owed.</p>
HTML,
    ],

    [
      'h2' => 'Separate materials from labor, and know your margin on each',
      'anchor' => 'materials-vs-labor',
      'html' => <<<'HTML'
<p>Plenty of electricians invoice a single line: "Panel upgrade, $2,400." It looks tidy, but it hides the two very different businesses you're actually running. One sells your time, and the margin on that is whatever your rate leaves after overhead. The other resells materials, and the margin there is the markup between what the supply house charged you and what you billed the customer. When both are mashed into one number, you can't see either.</p>
<p>Split them on the invoice: a materials section and a labor section. Customers generally accept it well, because they can see what the parts cost versus what the work costs, and it makes your books far more useful. Now you can answer questions that decide whether you're actually profitable:</p>
<ul>
<li><strong>Are you marking up materials at all?</strong> Many electricians bill materials at cost without noticing, which means every trip to the supplier is unpaid shopping. A standard markup on parts is normal in the trade; your books should show whether you're applying it consistently.</li>
<li><strong>Is your labor rate covering your real costs?</strong> Your hourly or day rate has to carry the van, insurance, licensing, tools, and the hours you spend quoting and driving that no one pays for directly. If materials markup is quietly subsidizing thin labor pricing, you want to know.</li>
<li><strong>Which side of a bad job went wrong?</strong> When a job's profit disappoints, split billing tells you whether you underestimated the hours or ate a materials overrun. Those have different fixes.</li>
</ul>
<p>The same split matters on the cost side: record what you paid for the materials on each job, not just what you billed. The gap between the two is your materials margin, and watching it per job is where the next section comes in.</p>
HTML,
    ],

    [
      'h2' => 'Parts and materials: per-job purchases, van stock, and COGS',
      'anchor' => 'parts-van-stock-cogs',
      'html' => <<<'HTML'
<p>Materials flow into an electrical business two ways, and they need slightly different handling.</p>
<p><strong>Per-job purchases</strong> are the easy case: the panel, the specific fixtures, the 200 feet of cable you bought for one particular job. Record the receipt, tag it to the job, bill it (with markup) on that job's invoice. The cost belongs to that job and nowhere else. The discipline is simply not losing the receipt between the supply-house counter and your desk. Snapping a photo of it before you leave the parking lot beats a dashboard full of faded paper; Argo Books can scan a receipt with your phone camera and turn it into a recorded expense, which is exactly the moment to do it.</p>
{{illustration:receipt-scan}}
<p><strong>Van stock</strong> is the harder case: the wire, breakers, boxes, connectors, and wire nuts you buy in bulk and carry around. When you spend $600 restocking the van, that money hasn't been used up yet. It becomes a cost, in accounting terms cost of goods sold (COGS), only as the parts go into jobs. If you expense the whole restock the day you buy it, your books show a bad month that wasn't bad and good months that quietly ran down your stock. You don't need to log every wire nut. A workable middle ground:</p>
<ul>
<li>Track van-stock purchases as inventory rather than an instant expense.</li>
<li>Bill the meaningful parts (breakers, devices, fixtures, cable by length) to each job as you use them, which both charges the customer and moves the cost out of stock.</li>
<li>Every few months, do a rough count of what's in the van and adjust. Shrinkage here is real: parts get used and never billed, and that leak only shows up if you look.</li>
</ul>
<p>Inventory software makes this far less tedious than a spreadsheet. Argo Books tracks inventory and cost of goods sold, so your van stock can live as a parts list that gets drawn down as jobs consume it, and your profit numbers reflect what jobs actually cost.</p>
HTML,
    ],

    [
      'h2' => 'Job costing: find out which work actually pays',
      'anchor' => 'job-costing',
      'html' => <<<'HTML'
<p>Once your invoices split materials from labor and your parts costs are tagged to jobs, you can do the single most valuable thing in trade bookkeeping: job costing. For each job, line up what you billed against what it cost you, materials at your buy price plus the hours you and any crew actually spent, including drive time and the return visit for the inspection. What's left is that job's real profit.</p>
<p>Do this for a few months and patterns show up that gut feel misses. Service calls with a call-out fee might turn out to be your best money per hour, while the big renovation jobs that feel impressive barely clear their costs once the extra trips and unbilled extras are counted. New construction might pay reliably but slowly; insurance work might pay well but chew up hours in paperwork. Maybe one builder always drags you into scope creep and another hands you clean, profitable jobs. None of that is visible in a bank balance. All of it is visible in job-level numbers.</p>
<p>You don't need anything fancy to start. A simple record per job with four numbers, billed labor, billed materials, actual materials cost, actual hours, is enough to rank your work by profit per hour. That ranking should drive real decisions: which work to chase, which to price higher, and which customers to politely let a competitor have. Our guide on <a href="/bookkeeping-for-contractors/">bookkeeping for contractors</a> digs further into job costing across bigger projects and crews.</p>
HTML,
    ],

    [
      'h2' => 'The van, the tools, and the deductions electricians miss',
      'anchor' => 'van-tools-deductions',
      'html' => <<<'HTML'
<p>An electrical business carries a load of costs that never appear on any customer invoice, and every one of them belongs in your books, both because they're the overhead your rates have to cover and because most are tax-deductible. The common ones:</p>
<ul>
<li><strong>The van.</strong> Fuel, insurance, repairs, tires, registration, lease or loan interest, and depreciation if you own it. How much you can claim depends on your country's rules and on business versus personal use, so keep the records and let your accountant apply the formula. One honest note on tooling: Argo Books records vehicle costs as expenses you enter, it doesn't automatically track your mileage, so if your tax authority wants a mileage log, keep one separately (a small notebook in the door pocket or a mileage app works).</li>
<li><strong>Tools and equipment.</strong> Hand tools, testers, the ladder, cordless kits, and their batteries. Small tools are usually deductible right away; expensive equipment may need to be claimed over several years. Either way, keep the receipts.</li>
<li><strong>Licensing and certification.</strong> Your license fees, renewals, and any bonding costs are business expenses.</li>
<li><strong>Insurance.</strong> Liability insurance and any trade-specific coverage. These are significant and fully legitimate deductions that solo tradespeople sometimes forget to record because they're paid annually.</li>
<li><strong>Continuing education.</strong> Code-update courses, safety tickets, and training required to keep your license current generally count as business costs.</li>
<li><strong>Phone, workwear, and safety gear.</strong> The business share of your phone plan, plus boots, gloves, and PPE.</li>
</ul>
<p>The rule of thumb: if you spent it to be able to do electrical work for money, record it. Whether and how it's deductible varies by country, so confirm the specifics with a local accountant, but you can't claim what you never wrote down. Our <a href="/small-business-bookkeeping-basics/">small business bookkeeping basics</a> guide covers the general habit of capturing expenses as they happen.</p>
HTML,
    ],

    [
      'h2' => 'Cash jobs, invoiced work, and getting paid faster',
      'anchor' => 'cash-and-getting-paid',
      'html' => <<<'HTML'
<p>Trade work still involves cash, and cash needs saying plainly: it's income like any other, it's taxable, and it belongs in your books the day you receive it. Beyond staying on the right side of the tax office, there's a practical reason to record it. Unrecorded cash makes your business look smaller than it is, which hurts you when you want a vehicle loan, a mortgage, or eventually to sell the business. A customer paying cash is fine; cash that never reaches your books is a problem you're creating for yourself.</p>
<p>Invoiced work has the opposite problem: the money is recorded but not yet in your hand, and slow payers are one of the biggest cash-flow drains in the trades. A few habits shorten the gap:</p>
<ul>
<li><strong>Invoice immediately.</strong> Send the invoice the day the job finishes, from the van if you can. Every day between finishing and invoicing is a free loan to the customer, and customers pay fastest while the work is fresh.</li>
<li><strong>Keep terms short.</strong> For residential service work, due on receipt or net 7 is reasonable. Net 30 is a convention from big-company billing; you don't owe it to a homeowner.</li>
<li><strong>Make paying easy.</strong> The more ways they can pay, the fewer excuses they have.</li>
<li><strong>Chase politely and on schedule.</strong> A reminder the day after the due date, then a firmer follow-up. Our guide on <a href="/how-to-follow-up-on-unpaid-invoices/">how to follow up on unpaid invoices</a> has templates and a timeline you can copy.</li>
</ul>
<p>And for the builders and property managers who pay in 45 days no matter what your invoice says: job costing from earlier tells you whether their volume is worth the wait. Sometimes it is. It should be a decision, not a default.</p>
HTML,
    ],

    [
      'h2' => 'Sales tax and a simple monthly routine',
      'anchor' => 'sales-tax-monthly-routine',
      'html' => <<<'HTML'
<p><strong>Sales tax first, with a caveat:</strong> the rules depend entirely on where you work. Depending on your country and region you may need to charge sales tax, GST, HST, or VAT on labor, on materials, or both, often only once your revenue passes a registration threshold, and contractor-specific rules about tax on materials vary a lot. Don't guess; confirm your situation with your local tax authority or an accountant. The bookkeeping habit is the same everywhere, though: track the tax you charge separately from your actual income. That money was never yours, you're holding it to pass on, and spending it is one of the classic ways small trade businesses get into trouble. It also helps to track the tax you paid on your own purchases, since in many systems you can offset one against the other. Argo Books tracks tax collected and tax paid and gives you a summary for filing time; it doesn't file or send the tax in for you, but it puts the numbers in one place.</p>
<p><strong>Then the routine.</strong> Once a month, ideally the same evening each month:</p>
<ol>
<li><strong>Clear the receipt backlog.</strong> Get every supply-house, fuel, and tool receipt recorded. If you've been scanning them as you go, this step is nearly done already.</li>
<li><strong>Invoice anything unbilled.</strong> Finished jobs, reached milestones, extras that got done but never billed. Unbilled work is the most expensive kind.</li>
<li><strong>Chase overdue invoices.</strong> Send reminders on everything past due.</li>
<li><strong>Check your books against your bank.</strong> Go down the bank statement and make sure every deposit and payment is in your books. Argo Books can import a bank statement to speed this up.</li>
<li><strong>Close out finished jobs.</strong> Fill in the final costs and hours so your job costing stays current.</li>
<li><strong>Set aside tax.</strong> Move sales tax collected and a slice for income tax into a separate account where you won't touch it.</li>
</ol>
{{illustration:checklist}}
<p>For most solo electricians this is an hour or two a month once the habit sticks. The payoff is a business you can actually steer: you know which work pays, your quotes are grounded in real costs, tax time is a report instead of a shoebox, and nobody is riding around your van as an unbilled expense.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books lets you invoice with deposits and partial payments, split materials from labor, and scan supply-house receipts on the spot, so every job\'s numbers stay straight.',
  'tool_callout_cta' => 'See invoicing in Argo Books',
  'tool_callout_url' => '/features/invoicing/',

  'faqs' => [
    [
      'q' => 'Should electricians charge a deposit before starting a job?',
      'a' => 'On any job with real material costs, yes. A deposit of somewhere around a quarter to a half of the job, taken before you order the gear, means the panel and fixtures aren\'t bought on your own credit while you wait to finish and bill. It also filters out customers who were never going to pay. Deposits are standard practice in the trades, and serious customers don\'t blink at them. In your books, record the deposit against the job rather than as free-and-clear income, because you still owe the work. Small service calls are the exception: for a one-hour call-out it\'s usually more friction than it\'s worth, and a single invoice on completion is fine.',
    ],
    [
      'q' => 'Should I put materials and labor as separate lines on my invoices?',
      'a' => 'Yes, and it helps you more than the customer. Separate materials and labor sections show you two different margins: the markup you make reselling parts and the profit left in your labor rate after overhead. Lumped into one number, you can\'t tell whether a disappointing job came from underestimating hours or from eating a materials overrun, and those problems have different fixes. Customers also tend to trust an itemized invoice more, because they can see what the parts cost versus what the work cost. The one habit to pair with it: record what the materials cost you, not just what you billed, so the gap between the two is visible per job.',
    ],
    [
      'q' => 'How do I handle the parts stock in my van for bookkeeping?',
      'a' => 'Treat bulk stock as inventory rather than an instant expense. When you spend a few hundred dollars restocking wire, breakers, and boxes, that money isn\'t used up yet; it becomes a cost only as parts go into jobs. Bill the meaningful parts to each job as you use them, which charges the customer and draws down your stock at the same time, and do a rough count of the van every few months to catch the drift. The drift is the point: parts that get installed but never billed are a slow leak that only shows up when you compare what you bought against what you invoiced. You don\'t need to count every wire nut, just the parts that cost real money.',
    ],
    [
      'q' => 'Can I deduct my van, tools, and license fees as an electrician?',
      'a' => 'Generally yes, though the details depend on your country\'s tax rules. Vehicle running costs, tools and test equipment, license and certification fees, liability insurance, code-update courses, safety gear, and the business share of your phone are all normal business expenses for an electrician. Expensive equipment may need to be claimed over several years instead of all at once, and vehicle claims usually require records of business versus personal use, which in many places means keeping a mileage log. The universal rule is that you can\'t claim what you didn\'t record, so capture every receipt when it happens and let a local accountant tell you exactly how each category applies where you live.',
    ],
    [
      'q' => 'Do I need to report cash jobs?',
      'a' => 'Yes. Cash income is taxable income in essentially every tax system, and the fact that it arrived as banknotes instead of a bank transfer changes nothing. Beyond the legal side, there\'s a self-interested reason to record it: unrecorded cash makes your business look smaller than it really is, which works against you when you apply for a vehicle loan or a mortgage, and it shrinks the value of the business if you ever sell it. Record cash jobs the day you\'re paid, the same as any invoice. If a customer wants to pay cash, that\'s completely fine; what matters is that the sale lands in your books like every other one.',
    ],
  ],

  'related_niche_slugs' => [
    'electrician',
    'contractor',
    'plumber',
  ],

  'related_article_slugs' => [
    'bookkeeping-for-contractors',
    'bookkeeping-for-repair-shops',
    'small-business-bookkeeping-basics',
  ],
];
