<?php
// articles/data/bookkeeping-for-nonprofits.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-nonprofits',

  'h1' => 'Bookkeeping for nonprofits: a simple guide',

  'meta_title' => 'Bookkeeping for Nonprofits: a Simple Guide | Argo Books',

  'meta_description' => 'A plain guide to bookkeeping for small nonprofits, clubs, and churches: fund accounting basics, donations and grants, in-kind gifts, and clean board reports.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 55,

  'published' => '2026-06-15',

  'updated' => '2026-06-15',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Bookkeeping for a small nonprofit, a club, or a church is different from bookkeeping for a business in one big way: the goal isn't profit, it's trust. A business tracks money to see if it made any. A nonprofit tracks money to show that every dollar went where it was meant to go, to donors, to members, to a board, and sometimes to a regulator. The numbers are a promise you're keeping in public.</p>
<p>The good news is that the core habits are the same plain ones any small organization needs, plus one extra idea: tracking money by its purpose, not just by date. This guide explains that idea in everyday language, walks through donations, grants, and in-kind gifts, and shows what a clean board report looks like. It also says honestly where a general tool fits and where a bigger nonprofit really needs dedicated software.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Fund accounting, in plain language',
      'anchor' => 'fund-accounting',
      'html' => <<<'HTML'
<p>The one idea that makes nonprofit bookkeeping its own thing is called fund accounting. It sounds technical and it really isn't. All it means is that you track money by what it's for, not just whether you have it.</p>
<p>A business has one pot of money and asks, "how much do we have, and did we make a profit?" A nonprofit often has several pots that have to stay mentally separate, even when they sit in one bank account. The building-repair fund, the general running money, a specific program someone donated for: each is a pot with its own purpose, and you need to be able to say at any time how much is in each one.</p>
<p>Why it matters: if a donor gives money for the youth program and you spend it on the heating bill, you've broken a promise, even if it felt like all the same account at the time. Fund accounting is just the habit of tagging every dollar in and out with which purpose it belongs to, so you can always answer "how much do we have for X?" honestly. For a small organization, that can be as simple as a category on every transaction. The idea is the foundation everything else in this guide sits on.</p>
HTML,
    ],

    [
      'h2' => 'Restricted versus unrestricted funds',
      'anchor' => 'restricted-funds',
      'html' => <<<'HTML'
<p>Once you're tracking money by purpose, the most important split to understand is restricted versus unrestricted. It decides what you're actually allowed to spend on what.</p>
<ul>
<li><strong>Unrestricted funds</strong> are money the organization can use for any legitimate purpose. General donations with no strings, membership dues, and most fundraising income usually land here. This is the money that keeps the lights on and covers whatever needs covering.</li>
<li><strong>Restricted funds</strong> are money a donor or grant gave for a specific purpose, and you're obliged to use it only for that. A gift "for the new roof" or a grant "for the literacy program" is restricted. Spending it on anything else isn't allowed, and for grants it can mean having to pay the money back.</li>
</ul>
<p>The practical habit is to tag income as restricted or unrestricted the moment it comes in, and to record which fund each expense draws from. Then you can always show that restricted money was spent the way it was promised. This is exactly what donors, grant-makers, and boards want to see, and it's the difference between a clean year-end and an awkward conversation. You don't need fancy software to do it at a small scale, just the discipline to tag every transaction with its fund and its restriction.</p>
HTML,
    ],

    [
      'h2' => 'Recording donations and grants',
      'anchor' => 'donations-grants',
      'html' => <<<'HTML'
<p>Donations and grants are the lifeblood of most nonprofits and the part regulators and donors care most about, so the records need to be clean and complete.</p>
<ul>
<li><strong>Record every donation with who, how much, when, and any restriction.</strong> Even small cash gifts in a collection plate or a donation box should be counted and recorded as a total, because untracked income is exactly what undermines trust. For larger gifts, keep the donor's details so you can issue receipts where your country allows tax-deductible giving.</li>
<li><strong>Treat grants carefully, because they come with strings.</strong> A grant usually specifies what the money is for and often what reporting you owe in return. Record it as a restricted fund, track spending against it separately, and keep the paperwork. Many grants require you to show exactly how their money was spent, sometimes line by line, so a clean per-grant record isn't optional.</li>
<li><strong>Match the bank to your records regularly.</strong> Count the donations and other income against what actually landed in the account each month. For an organization that handles cash, this is how you show the money you collected is the money that got banked.</li>
</ul>
<p>The rules on donation receipts, tax-deductible giving, and grant reporting vary a great deal by country and by the type of organization, so check your local regulator's guidance or ask an accountant who works with nonprofits. The bookkeeping habit underneath is universal: record every gift and grant with its purpose, and keep the proof.</p>
HTML,
    ],

    [
      'h2' => 'In-kind gifts and donated time',
      'anchor' => 'in-kind',
      'html' => <<<'HTML'
<p>Nonprofits often receive things that aren't cash: a donated laptop, a printer, free use of a hall, professional services given for nothing. These are called in-kind gifts, and they're easy to leave out of the books entirely, which understates what your organization actually received and did.</p>
<p>The general principle is that donated goods and services with a clear value are recorded at a fair estimate of what they'd have cost, showing both the gift coming in and the equivalent expense or asset. A donated $500 printer is recorded as a $500 gift and a $500 piece of equipment, so the books reflect the real picture. This matters for grant reporting and for showing donors the full scale of support behind the organization.</p>
<p>Donated time is the tricky one. Volunteer hours are hugely valuable, but most accounting rules only let you record donated services in the books in narrow cases, often only specialized professional work that you'd otherwise have paid for. General volunteering usually isn't recorded as a financial figure, though it's well worth tracking separately for your own reports and grant applications. The exact rules differ by country and standard, so this is another good question for a nonprofit accountant. The habit: record in-kind goods and qualifying services at a fair value, and keep a note of how you valued them.</p>
HTML,
    ],

    [
      'h2' => 'Reports your board and donors can trust',
      'anchor' => 'reports',
      'html' => <<<'HTML'
<p>All the tracking exists so you can produce a few clear reports, usually for a treasurer to present to a board, and sometimes for donors or a regulator. If you've tagged income and expenses by fund all year, these reports almost write themselves.</p>
<ul>
<li><strong>A summary of money in and out, by fund.</strong> What came in, what went out, and the balance left in each fund. This is the heart of a treasurer's report: it shows the restricted pots are intact and the general money is being managed sensibly.</li>
<li><strong>A snapshot of what the organization holds.</strong> Cash in the bank, anything owed to or by the organization, and the equipment or assets it owns. A simple version of this is enough for most small nonprofits.</li>
<li><strong>Spending against budget.</strong> If the board set a budget, showing actual spending next to it is what lets them steer. It turns the books from a record into a tool the board can act on.</li>
</ul>
<p>The point of all three is the same: a board member or a donor should be able to read them and trust that the money did what it was meant to. Clean, fund-tagged records make that easy. Messy ones make every board meeting a round of questions nobody can answer, which is corrosive to a volunteer board's confidence. A treasurer who hands over clear reports each meeting is giving the organization something more valuable than the numbers themselves: the quiet sense that everything is in order.</p>
HTML,
    ],

    [
      'h2' => 'When a general tool fits, and when it doesn\'t',
      'anchor' => 'when-it-fits',
      'html' => <<<'HTML'
<p>Here's the honest part, and it matters more for nonprofits than for most. General small-business accounting tools, including Argo Books, are not purpose-built fund-accounting software. They're built around a business that tracks income, expenses, and profit. You can absolutely use one for a small nonprofit by treating each fund as a category or a tag, and for a small club, a church, or a community group, that's often perfectly enough.</p>
<p>Where a general tool fits well: a small organization with a handful of funds, donations and dues coming in, modest expenses going out, and a treasurer who needs clear reports for the board. Tag every transaction with its fund, keep the receipts, run a monthly check against the bank, and a general tool handles it comfortably. Argo Books, being a free-to-start desktop app that keeps your data on your own machine, can suit this kind of small, simple organization, and the free tier covers a lot of small-group bookkeeping without cost.</p>
<p>Where it doesn't fit: a larger nonprofit with many restricted funds, multiple complex grants each with their own reporting rules, or strict regulatory requirements around fund reporting. At that point, dedicated fund-accounting software exists for a reason. It builds restricted-fund tracking, grant reporting, and the right financial statements in as first-class features, instead of you bending a business tool to do them. If you're managing significant grant money or your regulator expects formal fund reports, that specialized software, and an accountant who knows nonprofits, is the right call. Don't force a general tool past the point where it serves you. The goal is trustworthy records, and sometimes the trustworthy choice is the specialized one.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'For a small nonprofit, club, or church, Argo Books is a free-to-start way to track funds, donations, and expenses, with your data on your own machine.',
  'tool_callout_cta' => 'Try Argo Books for free',
  'tool_callout_url' => '/downloads/',

  'faqs' => [
    [
      'q' => 'What is fund accounting, in simple terms?',
      'a' => 'Fund accounting just means tracking money by what it\'s for, not only by date. A business has one pot and asks whether it made a profit. A nonprofit usually has several pots that must stay separate even when they share one bank account: a building fund, general running money, a specific program a donor gave for. The habit is to tag every dollar in and out with which fund it belongs to, so you can always answer how much you have for each purpose. That is the whole idea. For a small organization it can be as simple as a category on every transaction, and it\'s the foundation that lets you prove restricted money was spent the way it was promised.',
    ],
    [
      'q' => 'What is the difference between restricted and unrestricted funds?',
      'a' => 'Unrestricted funds are money the organization can use for any legitimate purpose, like general donations, membership dues, and most fundraising income. Restricted funds are money a donor or grant gave for a specific purpose, and you are obliged to use it only for that. A gift for the new roof or a grant for the literacy program is restricted, and spending it on anything else is not allowed. For grants it can even mean paying the money back. The practical habit is to tag income as restricted or unrestricted the moment it arrives, and to record which fund each expense draws from, so you can always show restricted money went where it was promised.',
    ],
    [
      'q' => 'How do I record a donated item or donated services?',
      'a' => 'Record donated goods and qualifying services at a fair estimate of what they would have cost, showing both the gift coming in and the matching expense or asset. A donated 500-dollar printer is recorded as a 500-dollar gift and a 500-dollar piece of equipment, so the books reflect the real picture. Donated time is trickier: most accounting rules only let you record donated services in narrow cases, often only specialized professional work you would otherwise have paid for, while general volunteering usually is not recorded as a financial figure even though it\'s worth tracking for your own reports. The exact rules vary by country and standard, so it\'s worth asking an accountant who works with nonprofits.',
    ],
    [
      'q' => 'Does my small nonprofit need special fund-accounting software?',
      'a' => 'Often not. A small club, church, or community group with a handful of funds, donations and dues coming in, and modest expenses can run perfectly well on a general accounting tool by treating each fund as a category or tag, keeping receipts, and checking against the bank monthly. Special fund-accounting software earns its place when the organization gets larger and more complex: many restricted funds, multiple grants each with their own reporting rules, or strict regulatory fund-reporting requirements. At that point dedicated software builds those things in as core features instead of you bending a business tool to do them, and an accountant who knows nonprofits becomes worth the cost. Match the tool to the complexity, not the other way around.',
    ],
    [
      'q' => 'Is this article just trying to sell me Argo Books?',
      'a' => 'Argo Books is mentioned, and yes, this is the Argo Books site, so read it with that in mind. But we have been deliberately honest about the limits: Argo Books is general small-business accounting, not purpose-built fund-accounting software, and we say plainly that a larger or grant-heavy nonprofit should use dedicated fund-accounting software and a nonprofit accountant. Argo Books suits small, simple organizations, and the advice in this guide, tracking by fund, recording donations and in-kind gifts, and producing clean board reports, works with any tool or even a careful spreadsheet. If the right answer for your organization is specialized software, that is the answer, and we would rather tell you that than sell you a tool past the point where it serves you.',
    ],
  ],

  'related_niche_slugs' => [
    'generic',
    'consultant',
    'freelance',
  ],

  'related_article_slugs' => [
    'small-business-bookkeeping-basics',
    'how-to-do-bookkeeping-without-an-accountant',
    'small-business-tax-deductions',
  ],
];
