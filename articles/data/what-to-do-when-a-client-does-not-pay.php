<?php
// articles/data/what-to-do-when-a-client-does-not-pay.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'what-to-do-when-a-client-does-not-pay',

  'h1' => 'What to do when a client does not pay',

  'meta_title' => 'What to Do When a Client Does Not Pay | Argo Books',

  'meta_description' => 'A calm, step-by-step plan for when a client hasn\'t paid the invoice. What to send on day 1, day 8, day 22, day 45, and what to do at day 60 and beyond.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'invoicing',
  'hub_weight' => 80,

  'published' => '2026-05-30',
  'updated'   => '2026-05-30',

  'reading_time_min' => 9,

  'intro_html' => <<<'HTML'
<p>An invoice is overdue and the client has gone quiet. Most late invoices come from honest mistakes: a spam filter caught the email, an approval is sitting in someone's inbox, the accounts payable cycle runs every other Friday. The clients who are actually trying to skip out on the bill are a small minority, and you can tell them apart from the honest ones by the second or third contact, not the first. The right move on day one is a polite nudge, not an escalation.</p>
<p>This guide walks the timeline day by day, from the morning the invoice goes past due to the point where you decide between small claims, collections, or writing it off. You'll see sample wording for reminders, a phone script, a final notice, and a clear set of things not to do. The goal is to get paid without burning the relationship, and to stay professional even when the client hasn't been.</p>
HTML,

  'sections' => [

    // 0
    [
      'h2' => 'Day 1 past due: assume nothing',
      'anchor' => 'day-1-past-due',
      'html' => <<<'HTML'
<p>The invoice was due yesterday and the payment hasn't arrived. Your first move is to do nothing for a few hours. Most overdue invoices aren't a client problem, they're a process problem. The email may have gone to a junk folder. The approver may have been on vacation. The accounts payable run may happen on Fridays only, and yesterday was a Wednesday. None of those reasons reflect badly on you or the client, and none of them are worth a sharp message.</p>
<p>Open your records and check the basics first. Did the invoice actually send? Did the email bounce? Did you send it to the right address? On larger clients with a finance team, did you copy the AP inbox as well as your point of contact? A meaningful share of "missing payments" turn out to be missing invoices that never reached the right inbox. Fixing that is a different conversation from chasing a late payer, and it deserves a friendly tone rather than a reminder.</p>
<p>Tone matters from the very first contact. If the client is normally good and the invoice is one day late, you don't need to chase yet. Wait the rest of the week. If you have a payment portal, a small business owner, or a client who pays from their phone, they often pay inside 48 hours of the due date and a reminder on day 1 reads as anxious. Hold off, let the normal payment cycle run, and pick this back up later in the week if nothing arrives.</p>
<p>The mental shift here is the most important one in the whole guide: late doesn't mean unwilling. Treat the first few days as a small bookkeeping question, not a confrontation. The clients who appreciate the calm tone are the ones who keep hiring you. The ones who don't pay no matter what tone you use will reveal themselves later, and the calm record helps you when they do.</p>
HTML,
    ],

    // 1
    [
      'h2' => 'Day 1 to 7: send a polite reminder',
      'anchor' => 'day-1-to-7-polite-reminder',
      'html' => <<<'HTML'
<p>Somewhere between day 3 and day 7 past due, send a short reminder. Keep it to one short paragraph, attach the original invoice again as a PDF, and use a subject line that includes the invoice number. Most late payments at this stage are forgotten emails. The fix is to put the invoice back at the top of the client's inbox without making the message feel pointed.</p>
<p>Here's a sample you can paste and edit. Two sentences, friendly, no pressure:</p>
<p><strong>Sample reminder:</strong> "Hi [name], just a quick note that invoice #1042 for $1,850 was due on [date] and I haven't seen the payment land yet. I've attached a fresh copy in case the first one got buried, and please let me know if you need anything else from my side."</p>
<p>That's the entire message. No legal language, no late-fee threat, no exclamation points. You're telling them the invoice exists, that you noticed, and that you're happy to help if anything is blocking them. Plenty of late payments resolve right here, often within 24 hours, because the client opens the attachment, forwards it to their bookkeeper, and the bill gets paid on the next run.</p>
<p>A few small things make the reminder land better. Send it from the same email address that sent the original invoice, so the thread holds together. Include the invoice number in the subject line, because the AP team searches by number. Mention the exact amount, so the client doesn't have to open the file to confirm. And resist the urge to add a paragraph about how busy you are or how much you depend on the payment. The shorter the email, the faster it gets actioned.</p>
<p>If you want a deeper template for the wording and a longer escalation script, the full reminder playbook lives at <a href="/how-to-follow-up-on-unpaid-invoices/">How to follow up on unpaid invoices</a>. For most invoices, the two-sentence version above is enough on its own.</p>
HTML,
    ],

    // 2
    [
      'h2' => 'Day 8 to 21: pick up the phone',
      'anchor' => 'day-8-to-21-phone',
      'html' => <<<'HTML'
<p>If a week has passed since the polite reminder and the invoice is still unpaid, stop sending emails. Pick up the phone. A 60-second call resolves more late invoices than a month of follow-up emails, because email is easy to ignore and a ringing phone isn't. You don't need to be aggressive on the call. You just need to be heard.</p>
<p>Call the main number, ask for the person who handles accounts payable or your point of contact, and have the invoice number, the amount, and the original due date ready. The whole call should take less than two minutes if they answer.</p>
<p><strong>Sample opener when they answer:</strong> "Hi [name], this is [your name] from [your business]. I'm following up on invoice #1042 for $1,850. It was due on [date] and I just wanted to check whether you had a chance to process it. Is there anything you need from me to move it forward?"</p>
<p>That last sentence is the one that does the work. You're not accusing anyone of anything. You're offering to help unblock whatever is in the way. Common answers you'll get are: "We never received it" (resend it), "It's waiting on approval" (ask when the approval cycle runs), "We're short this month" (now you have real information), or "Let me check and get back to you" (set a specific callback date before you hang up).</p>
<p>If you reach voicemail, leave a brief message. Keep it under 20 seconds, repeat your number twice, and follow up with a short email that summarises the voicemail.</p>
<p><strong>Sample voicemail:</strong> "Hi [name], this is [your name] from [your business] calling about invoice #1042 for $1,850 from [date]. Could you give me a call back at [your number] when you get a chance, that's [your number] again. Thanks."</p>
<p>After the call, write a one-line note in your records: what date you called, who you spoke to, and what they promised. If they said "we'll pay it Friday", note it. If Friday comes and goes, you have a clean record of what was agreed, which makes the next conversation much shorter. Documentation at this stage is what separates a clean recovery later from a he-said-she-said argument.</p>
HTML,
    ],

    // 3
    [
      'h2' => 'Day 22 to 45: send a fresh invoice with the balance',
      'anchor' => 'day-22-to-45-fresh-invoice',
      'html' => <<<'HTML'
<p>Three weeks past due and the situation has shifted. The reminders didn't work, the phone calls didn't work, and the relationship is now in awkward territory. This is the right moment to issue a fresh invoice rather than chasing the original one again. A new invoice with a new number gets logged as a new item in the client's accounts payable system, and that often moves things along when an older invoice has gone stale in the queue.</p>
<p>Leave the original invoice exactly as it was. Don't edit it, don't change the date, don't delete it. The original is part of the paper trail and you may need it later for small claims or a collections handover. Create a new invoice with a brand new invoice number. In the description, reference the original: "Re-billing of invoice #1042 dated [date], plus late fee per agreed terms". That keeps both records consistent. For more on numbering, see <a href="/invoice-numbering-best-practices/">Invoice numbering best practices</a>.</p>
<p>Add a late fee as a separate line item. The standard rate in most jurisdictions is 1.5% per month on the overdue balance, which works out to $27.75 on a $1,850 invoice for one month overdue. The fee should already be written into your standard terms, otherwise it's hard to apply now. For the math, the legal limits, and the wording to put on every invoice, see <a href="/late-fees-when-and-how-to-charge/">Late fees: when and how to charge</a>.</p>
<p>Send the new invoice with a short note. Something like: "Hi [name], please find attached invoice #1058. This re-bills the balance on invoice #1042 from [date], which is now 30 days past due, plus the late fee per our agreed terms. Please let me know if there's anything blocking payment so we can sort it out." That message is firm without being hostile. You're telling the client that the clock is still running and that you're still willing to talk if there's a real problem on their side.</p>
<p>If the client does pay at this stage, great. Mark both the original invoice and the new one as resolved in your records, and decide whether you want to keep working with them. A client who paid 35 days late once is fine. A client who needs a re-bill every time is telling you they aren't a good fit for your cash flow.</p>
HTML,
    ],

    // 4
    [
      'h2' => 'When the client can\'t pay, not won\'t pay',
      'anchor' => 'cant-pay-vs-wont-pay',
      'html' => <<<'HTML'
<p>Somewhere between Day 22 and Day 60, you might realise this isn't a slow-pay problem. The client genuinely can't pay. Maybe their cash flow has collapsed, maybe they've lost a big customer of their own, maybe they're heading for bankruptcy, administration, or receivership. The escalation steps in the rest of this guide assume the client has the money and just isn't paying. If they don't have the money, the rules change.</p>
<p>Warning signs that this is "can't pay" rather than "won't pay":</p>
<ul>
  <li>A previously prompt client suddenly goes silent across email, phone, and text.</li>
  <li>The person who used to approve your invoices has left the company.</li>
  <li>You hear from other vendors that they haven't been paid either.</li>
  <li>You find public filings: bankruptcy notices, court actions, dissolution paperwork.</li>
  <li>The client asks you to take on more work on credit before they catch up on the original invoice.</li>
  <li>They start using phrases like "stretching payments until our next round closes" or "we just need to get to the end of the quarter".</li>
</ul>
<p>Once you suspect this is the situation, do four things:</p>
<ol>
  <li><strong>Try to talk to them.</strong> A direct phone call or a face-to-face meeting, not just email. A client who genuinely can't pay often appreciates honesty more than escalation. You may be able to negotiate a payment plan, a partial settlement, or a structured work-for-credit arrangement that recovers more than the legal route would. Get whatever you agree in writing, with their signed acknowledgement of the debt.</li>
  <li><strong>Stop the work.</strong> Don't deliver any more product, service, or hours until the existing balance is cleared. Continuing to work creates more bad debt and weakens your position if things go formal. Pause politely but firmly in writing, so there's a record.</li>
  <li><strong>Check the public record.</strong> Search for bankruptcy or insolvency filings, court cases, and dissolution notices in their jurisdiction. If they're already in a formal insolvency process, your normal escalation path won't work and you need to switch tracks.</li>
  <li><strong>If they've filed for bankruptcy or receivership:</strong> you become an unsecured creditor. File a proof of claim with the trustee before the deadline (varies by jurisdiction, often a few months from the filing date). Don't expect to recover the full balance. Unsecured creditors usually get cents on the dollar after secured creditors, employees, and tax authorities have been paid.</li>
</ol>
<p>Small claims court won't help once a company is in formal insolvency. Most jurisdictions impose an automatic stay that pauses all collection actions, including any small-claims judgment, the moment a bankruptcy petition is filed. Collections agencies generally won't take the case either, because there's nothing realistic to collect against.</p>
<p>Recognising "can't pay" early matters. Every extra week you keep working on credit, every reminder you send to a client already in liquidation, is wasted time and added loss. The realistic move is to write the balance off, document the bad debt for tax purposes within the rules for your accounting method, and move on. Tighten the deposit terms on your next client and don't repeat the lesson.</p>
HTML,
    ],

    // 5
    [
      'h2' => 'Day 45 to 60: send a final notice in writing',
      'anchor' => 'day-45-to-60-final-notice',
      'html' => <<<'HTML'
<p>Six weeks past due and the polite tone has run its course. The next step is a formal final notice, in writing, sent in a way that you can prove was received. Email is fine if you keep a copy, but a signed-for letter or a tracked courier delivery makes the paper trail stronger if you end up in small claims. Some businesses send both: the email for speed, the letter for proof.</p>
<p>The final notice is different from the earlier reminders in three ways. It states the total amount due including any late fees. It references the original contract or written agreement, so there's no ambiguity about what was owed for what work. And it explicitly names the next step: small claims, a collections handover, or both. This is the last chance for the client to settle without external help, and the letter should read that way.</p>
<p>Keep the wording professional, not emotional. A sample structure:</p>
<ul>
  <li>Open with the facts: invoice number, date, original amount, current balance with late fees, days past due.</li>
  <li>Reference the contract: "Per our agreement dated [date], payment was due within 30 days of invoice."</li>
  <li>List what has already happened: reminder on [date], phone call on [date], re-bill on [date].</li>
  <li>State the deadline: "Please remit the full balance of $1,933.50 within 10 business days of this notice."</li>
  <li>State the consequence: "If payment is not received by [date], the account will be referred to a collections agency or filed in small claims court without further notice."</li>
</ul>
<p>Don't bluff. If you write that you'll take the next step, be ready to actually take it. A final notice that gets ignored, followed by another final notice two weeks later, teaches the client that your deadlines don't mean anything. The whole point of this letter is that it's the last one. After this, you act.</p>
<p>One useful add-on: offer a settlement option in the same letter. Something like "If full payment is a hardship, I am open to a payment plan of $500 per month over four months. Please reply within 5 business days to set this up." A real client in real trouble will take that offer. A client who was never going to pay will ignore both options, which tells you everything you need to know about the next step.</p>
HTML,
    ],

    // 6
    [
      'h2' => 'Day 60+: small claims, collections, or write it off',
      'anchor' => 'day-60-small-claims-collections-write-off',
      'html' => <<<'HTML'
<p>Two months past due with no payment and no response. The polite phase is over. You now have three real options: file in small claims court, hand the debt to a collections agency, or write it off and move on. Each has trade-offs, and the right answer depends on how much is owed, how much time you have, and how strong your paper trail is.</p>
<p><strong>Small claims court.</strong> Designed for exactly this kind of dispute. Filing fees are usually under $100, you represent yourself, and judgments come fast. The dollar limits are different in every jurisdiction. In the US, the cap varies by state, roughly $2,500 to $25,000. In the UK, the small claims track covers up to £10,000. In Australia, state tribunals handle minor civil disputes up to between $10,000 and $100,000 AUD depending on the state. NCAT in New South Wales handles consumer claims up to $100,000 since 2022, and VCAT in Victoria has a civil claims jurisdiction up to $100,000 with a smaller $15,000 small-claims sub-tier. QCAT and ACAT sit at $25,000, and WA's Minor Cases division is at the floor at $10,000. In Canada, provincial limits range widely, from $15,000 in Quebec up to $100,000 in Alberta. Ontario raised its limit to $50,000 in October 2025. BC sits at $35,000. Check your provincial court website for the current figure. Check your local rules before you file, because the limit, the forms, and the filing fee are all set at the state, province, or country level. Small claims works best when you have a clean paper trail: a signed contract or written agreement, the original invoice, your reminders, the phone log, the re-bill, the final notice. Bring all of it.</p>
<p><strong>Collections agency.</strong> The agency takes over the chase in exchange for a cut of whatever they recover. Typical fees run 15 to 50% of the recovered amount, with smaller and older debts at the higher end and larger commercial debts often around 15 to 25%. The advantage is that you stop spending your own time on it. The disadvantages are the cut they take and the small chance that an aggressive agency damages your reputation with the client. Pick an agency that handles small business debts, ask up front about their fee structure, and read their methods before signing anything.</p>
<p><strong>Write it off.</strong> Sometimes the right call. If the unpaid balance is small relative to your monthly revenue, if the client has clearly gone dark, or if pursuing it would cost more time than the money is worth, writing it off and moving on is a legitimate business decision. In the United States, an unpaid invoice is generally NOT deductible if you're on cash-basis accounting (most freelancers and sole proprietors are), because the income was never reported in the first place. Accrual-basis businesses can usually claim a bad-debt deduction in the year the write-off happens. Rules differ in Canada, the UK, and Australia, so ask an accountant before claiming anything. The point of the write-off in bookkeeping is to keep your accounts realistic: you stop showing money as owed to you that you don't expect to see. Writing it off also lets you stop spending mental energy on the situation, which is worth real money on its own.</p>
<p>A short rule of thumb: if the debt is over $1,000 and you have a clean paper trail, small claims is usually the best option. If the debt is over $5,000 and you don't have time to file yourself, collections may be worth the cut. If the debt is under a few hundred dollars or your paper trail is thin, write it off and tighten your deposit policy for the next client.</p>
HTML,
    ],

    // 7
    [
      'h2' => 'Things NOT to do',
      'anchor' => 'things-not-to-do',
      'html' => <<<'HTML'
<p>The pressure to act when a client refuses to pay is real, and bad ideas feel reasonable at the time. None of the moves below help you collect, and most of them turn a recoverable invoice into a permanent loss, a reputation hit, or both. Keep this list in mind during the angrier hours.</p>
<p><strong>Public shaming on social media.</strong> Tempting and almost always a mistake. Naming a client on Twitter, LinkedIn, or a review site to pressure them into paying creates a permanent record that future clients will read. The client may file a defamation complaint if anything you wrote is even slightly off. And in most jurisdictions, the courts treat public shaming as evidence that you were unwilling to work the problem in good faith, which can hurt you in small claims. Whatever satisfaction the post gave you isn't worth the price.</p>
<p><strong>Threats of any kind.</strong> Threatening physical harm is obviously off limits, but the softer ones matter too. Don't threaten to call the client's other customers, their employer, their bank, their landlord, or their family. Don't threaten to "ruin" their reputation in their industry. None of those are legal collection methods in most jurisdictions, and a recorded threat can flip a clear-cut unpaid-invoice case into a harassment claim against you.</p>
<p><strong>Contacting their other clients.</strong> Reaching out to the people who pay your non-paying client to warn them off is illegal in many places under unfair business practice laws, and it almost always backfires socially. Word travels in small industries. The version of the story that spreads isn't the one where you were owed money, it's the one where you contacted strangers about a private business dispute.</p>
<p><strong>Withholding deliverables that are already paid for.</strong> If the client paid you a deposit and you delivered partial work against it, that work belongs to them. Holding it hostage to force payment of a later balance is breach of contract in most jurisdictions, and it gives the client a real legal claim against you that can wipe out whatever they owed plus more. Future work, where you haven't been paid yet, is a different story. Stopping that is reasonable. Clawing back what was already paid for isn't.</p>
<p><strong>Endless polite reminders.</strong> Past day 60, sending another friendly email every two weeks isn't a strategy. It's avoidance. If the standard timeline has run its course and the client hasn't paid, you owe yourself a decision: escalate or write it off. Drifting in between is the worst outcome of all, because it wastes your time, gives the client more chances to ignore you, and weakens any future legal case by making it look like you weren't serious.</p>
<p>The pattern across all of these is the same: actions that feel satisfying in the moment make recovery harder, not easier. The calm, documented path through small claims or a collections agency works because it's boring and predictable. The flashy options work in movies, not in real billing disputes.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'You can build the fresh invoice with the late fee line in the free generator.',
  'tool_callout_cta'  => 'Open the invoice generator',

  'faqs' => [
    [
      'q' => 'How long should I wait before assuming I won\'t get paid?',
      'a' => 'Around 60 days past the due date is the usual cut-off for most small businesses. By then you\'ve sent at least one reminder, made at least one phone call, and issued a re-bill with the late fee. If you\'ve heard nothing back, or only vague promises that never turn into payment, the client has moved from late to unwilling. That doesn\'t always mean a write-off. It just means the next step is small claims, a collections handover, or a settlement offer in writing. Some clients pay the moment they see a final notice, so escalation isn\'t the end of the road, just the next stop.',
    ],
    [
      'q' => 'Can I take a client to small claims if we didn\'t have a written contract?',
      'a' => 'Yes, in most jurisdictions, though a written contract makes the case much stronger. An invoice that was accepted and not disputed at the time, combined with emails confirming the work, screenshots of agreed scope, and proof that the client received the bill, can carry a small claims case on its own. Small claims courts are designed for ordinary people without lawyers, so they\'re forgiving on paperwork compared to higher courts. Rules vary by location, so check your local small claims guide before you file. The bigger the missing paperwork, the more the case rests on whatever written record you do have, which is one reason to keep every email and message tied to the job.',
    ],
    [
      'q' => 'How can I tell if a client can\'t pay rather than just won\'t pay?',
      'a' => 'Look for a sudden silence after months of prompt communication, the departure of the person who used to approve your invoices, other vendors complaining about non-payment, public filings against the company, or requests for more work on credit before the old balance clears. None of those on their own proves insolvency, but two or three together usually means the client is in real trouble. The response is different from the slow-pay timeline: stop delivering any more work, try to reach them directly to understand the situation, check the public record for bankruptcy or insolvency filings, and prepare to file a proof of claim if they\'ve formally collapsed. Small claims and collections agencies don\'t help once a company is in formal insolvency.',
    ],
    [
      'q' => 'Should I keep working for a client who hasn\'t paid?',
      'a' => 'No, not until the outstanding balance is cleared or a payment plan is signed. Continuing to deliver work when an invoice is past due trains the client to ignore your terms and adds more unpaid work to the problem you\'re trying to solve. Pause new work in writing, with a short, neutral message: "I\'ve paused work on the current project until invoice #1042 is settled. Once that lands, I\'m ready to pick up where we left off." That keeps the door open if they pay, and protects your time if they don\'t. Use a deposit on the next client to make this conversation easier the second time around.',
    ],
    [
      'q' => 'Can I report a non-paying client to a credit bureau?',
      'a' => 'Direct reporting is usually not available to small businesses. Credit bureaus generally only accept data from registered subscribers, which means banks, large utilities, and licensed collections agencies. The practical route is to hand the debt to a collections agency, who can then report it on your behalf as part of their process. A reported business debt can sit on the company credit file for several years and affect their ability to borrow. Reporting is a real consequence for the client, which is one reason the threat of collections often shakes payments loose at the final-notice stage. Check the rules in your country, because the reporting framework is different in each one.',
    ],
    [
      'q' => 'When should I write off an unpaid invoice for tax purposes?',
      'a' => 'When you\'ve reasonably concluded the debt isn\'t collectable, which usually means time has passed, contact attempts have ended, and you\'re not actively pursuing it through small claims or collections. Most accrual-method businesses can claim the loss against taxable income in the year the write-off happens, while cash-method businesses don\'t get a separate deduction because the income was never recorded in the first place. The exact treatment depends on your accounting method and your local tax rules, so check with an accountant before you write off anything bigger than a small amount. Keep the original invoice, the reminders, the phone log, and the final notice in your records as evidence that the write-off was reasonable.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'contractor',
    'designer',
  ],

  'related_article_slugs' => [
    'how-to-follow-up-on-unpaid-invoices',
    'late-fees-when-and-how-to-charge',
    'net-30-vs-due-on-receipt',
  ],
];
