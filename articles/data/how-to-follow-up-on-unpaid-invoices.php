<?php
// articles/data/how-to-follow-up-on-unpaid-invoices.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-follow-up-on-unpaid-invoices',

  'h1' => 'How to follow up on unpaid invoices',

  'meta_title' => 'How to Follow Up on Unpaid Invoices | Argo Books',

  'meta_description' => 'How to follow up on unpaid invoices without scaring off a good client: a six-step sequence with sample emails, phone scripts, and a final-notice template.',

  'schema_type' => 'HowTo',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'invoicing',
  'hub_weight' => 70,

  'published' => '2026-05-30',

  'updated' => '2026-06-26',

  'reading_time_min' => 8,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>An invoice goes out, the due date comes, and the money isn't in the account. It happens to almost every small business at some point, and it doesn't mean the client is a deadbeat. Most of the time it's a missed email, a misplaced PDF, or an accounts payable run that didn't get to the invoice this cycle. The way you follow up decides whether you get paid in the next week or you get paid in three months.</p>
<p>This guide walks through a six-step sequence that handles the common case and the awkward case. You start on the day after the due date with a polite reminder, you tighten up the wording at day five, you pick up the phone if email isn't landing, and you have a clean playbook for the rare client who goes quiet. There are full email templates you can copy and a one-line phone opener. No accounting background needed.</p>
HTML,

  'sections' => [

    // 0
    [
      'h2' => 'Step 1: Wait until the day after the due date',
      'anchor' => 'wait-day-after-due',
      'step_name' => 'Wait until the day after the due date',
      'step_text' => 'Don\'t chase on the day itself. If you wrote Net 15 on the invoice, the client has until the end of day 15 to pay. Reach out the morning after.',
      'html' => <<<'HTML'
<p>The first rule of following up is to respect the term you wrote on the invoice. If the invoice says <a href="/net-30-vs-due-on-receipt/">Net 15</a> and you sent it on June 1, the client has until the end of June 16 to pay. Sending a reminder on June 15 makes you look like you can't count, and it gives a slow payer a reason to push back rather than pay. Wait until the morning of June 17 and the conversation is simple: the invoice is overdue, and you're asking about it.</p>
<p>Before you send anything, check three places. Check the bank account for a payment that landed but hasn't been matched to the invoice yet. Check the email inbox in case the client replied with a question and the message was filtered. Check your sent folder to confirm the original invoice actually went out, with the PDF attached, to the right address. Roughly one in ten "unpaid" invoices turns out to be a payment already received or an email that never sent. You don't want to send a reminder for an invoice the client paid yesterday.</p>
<p>Also pull up your records on this client. How long do they usually take to pay? Some clients pay every invoice on day 14 of a Net 15 cycle and every invoice on day 29 of a Net 30 cycle. That's not late, that's their cadence, and a polite reminder one day late won't surprise them. Other clients pay within 48 hours of receiving the invoice, every time. If a normally fast payer is two days late, something specific has happened: a holiday, a sick week, an accounts payable handover. A short, low-pressure reminder is the right tool either way.</p>
<p>Pick a time to send the reminder. Tuesday and Wednesday mornings, between 9am and 11am in the client's time zone, get the best response rates. Avoid Mondays (overflowing inbox), Friday afternoons (already checked out), and anything that lands during a public holiday.</p>
HTML,
    ],

    // 1
    [
      'h2' => 'Step 2: Send the first reminder by email',
      'anchor' => 'first-reminder-email',
      'step_name' => 'Send the first reminder by email',
      'step_text' => 'Send a friendly email the day after the due date. Re-attach the invoice, restate the amount and due date, and keep the tone neutral.',
      'html' => <<<'HTML'
<p>The first reminder is short, friendly, and factual. Assume the missed payment is an oversight, because nine times out of ten it is. Re-attach the original PDF, because the easiest reason for a client to delay is that they can't find the file. Restate the invoice number, the amount, and the due date, so the email is self-contained and the client doesn't have to dig through their inbox to figure out what you're talking about.</p>
<p>Here's the full template. Copy it, change the bracketed parts, and send.</p>
<p><strong>Subject:</strong> Reminder: Invoice 1023 due May 22</p>
<pre>Hi [Client first name],

Just a quick reminder that invoice 1023 for $[amount] was due on May 22, and I haven't seen the payment land yet. I've re-attached the PDF in case it's easier than digging up the original email. Let me know if there's anything I can clear up from my end to get this processed.

Thanks,
[Your first name]</pre>
<p>A few things to notice about the template. The subject line uses the word "Reminder" and the actual invoice number and due date, so the email is searchable in the client's inbox. The body is four short sentences. There's no apology for "bothering" the client, because asking to be paid for work you've already done isn't a bother. There's no threat of late fees yet, because it's one day overdue and the client may have already paid this morning. The signoff uses your first name only, which keeps the tone informal and matches how you probably wrote to them while the job was running.</p>
<p>Send the email to the same person you sent the original invoice to. If you also have an accounts payable contact, CC them. Don't BCC anyone, and don't loop in the client's boss or partner on the first reminder. That's reserved for later in the sequence if things actually go sideways.</p>
HTML,
    ],

    // 2
    [
      'h2' => 'Step 3: If no reply in 5 days, send a second email',
      'anchor' => 'second-email',
      'step_name' => 'Send a second email at day 5',
      'step_text' => 'Five days after the first reminder, send a slightly firmer email. Reference the first message, restate the amount, and re-attach the invoice.',
      'html' => <<<'HTML'
<p>If five business days have passed since the first reminder and the inbox is still empty, send a second email. The tone shifts from "checking in" to "this is now overdue and I need a status". Still polite, still professional, but the friendliness gets dialed down a notch. The goal is to get a reply, even if that reply is "we're processing it this week".</p>
<p>Reference the original reminder by date, so it's clear this is the second touch and not the first. Restate the amount in numbers, because numbers focus attention. Re-attach the PDF for the third time in this conversation, because by now the client has had three chances to file it and the fact that the invoice is still unpaid suggests something is genuinely missing. Mention that the late fee on the agreed terms will start applying soon, so it isn't a surprise when it does.</p>
<p>Here's the template:</p>
<p><strong>Subject:</strong> Second reminder: Invoice 1023 now overdue</p>
<pre>Hi [Client first name],

I sent a reminder on May 23 about invoice 1023 for $[amount], and I haven't heard back yet. The invoice is now five days overdue.

Could you let me know where it sits on your end, and roughly when payment will be processed? If there's a hold-up I can help with, I'm happy to jump on a quick call.

As a reminder, the terms on the invoice include a 1.5% monthly late fee on overdue balances, which would start applying from today if the invoice isn't paid this week.

Thanks,
[Your first name]</pre>
<p>The phrase "where it sits on your end" is doing real work. It gives the client a chance to say "it's in accounts payable for processing this Friday", which is the answer you want. Most genuine delays come unstuck at this stage, because the second email is hard to ignore and it asks a direct question. If you get a reply with a specific date, write that date on your calendar and wait. If the date passes without payment, go straight to step 4.</p>
HTML,
    ],

    // 3
    [
      'h2' => 'Step 4: If still no reply, pick up the phone',
      'anchor' => 'pick-up-phone',
      'step_name' => 'Pick up the phone',
      'step_text' => 'If two emails get no reply, call the client. A two-minute phone call resolves more late invoices than another week of emails.',
      'html' => <<<'HTML'
<p>Two emails with no reply is a signal that email isn't the channel. Pick up the phone. A two-minute call gets a real answer about a late invoice more often than another week of writing.</p>
<p>The opener is one sentence. Don't make small talk, don't apologize for calling, and don't start with "I was just wondering". Try this:</p>
<p><strong>"Hi [name], it's [your first name] from [your business]. I'm calling to follow up on invoice 1023 for $[amount], which was due on May 22 and is now ten days overdue."</strong></p>
<p>Then stop talking and let the client respond. The silence is uncomfortable for them, not for you. Whatever they say next will fall into one of four buckets:</p>
<ul>
<li><strong>"Sorry, we missed it. We'll pay this week."</strong> Ask for a specific date. "Great, can I expect it by Friday?" Write the date down, send a short follow-up email after the call confirming what was agreed, and put a calendar reminder for the day after that date.</li>
<li><strong>"We didn't receive the invoice."</strong> Ask for the correct email address, resend it during the call if you can, and ask the client to confirm receipt by replying. A meaningful share of late invoices fall into this bucket and resolve themselves once the PDF actually reaches the right inbox.</li>
<li><strong>"There's a question about the invoice."</strong> Hear them out. Sometimes there's a genuine issue, like a line item that doesn't match the quote or a missing PO number. Agree what needs to change, send a corrected invoice with a new number, and reset the clock.</li>
<li><strong>"We're having cash flow problems."</strong> This is the honest answer that nobody likes to give. If the client says it, take it as a positive: you now know the situation. Ask for a partial payment now and a date for the balance, get the plan in writing by email after the call, and decide whether to keep working with them on the next job.</li>
</ul>
<p>Get a specific date before the call ends. "We'll pay soon" isn't a date. "By next Friday" is. Write down the name of the person you spoke to and the date they committed to, and send a one-line email after the call: "Thanks for the call. Confirming you'll process invoice 1023 by Friday June 6."</p>
HTML,
    ],

    // 4
    [
      'h2' => 'Step 5: Send a fresh invoice with the late fee added',
      'anchor' => 'fresh-invoice-late-fee',
      'step_name' => 'Send a fresh invoice with the late fee added',
      'step_text' => 'If a phone commitment is missed, issue a new invoice with a new number, the original work as one line, and the late fee as a separate line.',
      'html' => <<<'HTML'
<p>If the date the client gave you on the phone passes without payment, the conversation has shifted. The friendly reminders didn't work, the phone call didn't work, and a commitment was missed. The next move is a fresh invoice with the late fee applied, on the terms that were already agreed at the start.</p>
<p>Issue a new invoice with a new invoice number. Don't edit the original invoice. Keep it on file as the original record, and send a separate document for the updated balance, so both sides have a clean paper trail. The new invoice has two line items:</p>
<ul>
<li><strong>Line 1.</strong> The original work, as a single line, with the same description and amount as the original invoice. In the description, reference the original invoice number, for example "Re-issued from invoice 1023, dated May 8, for [project description]".</li>
<li><strong>Line 2.</strong> The late fee, as a separate line. Label it "Late fee per agreed terms" and enter the dollar amount. The standard is 1.5% per month on the overdue balance, so a $2,000 invoice that's one month overdue carries a $30 late fee. If the invoice is two months overdue, the late fee is $60. State the calculation in the description if you want to be explicit: "Late fee: 1.5% of $2,000 for one month overdue".</li>
</ul>
<p>Set the due date to seven days from the new invoice date, which is short on purpose. Send the new invoice by email, with a short body that names the missed commitment from the phone call. Something like: "Hi [name], following our call on May 30 and the commitment to pay by June 6, I'm attaching a fresh invoice 1024 covering the original amount plus the late fee per the terms on the original invoice. Payment is due by June 13."</p>
<p>The late fee isn't a punishment, it's a contract term. It was on the original invoice in the Terms section, and applying it now is consistent with what both sides agreed to at the start. For a full walkthrough of how to set the rate, what some states cap it at, and when to waive it for a good client, see <a href="/late-fees-when-and-how-to-charge/">late fees: when and how to charge</a>.</p>
HTML,
    ],

    // 5
    [
      'h2' => 'Step 6: Send a final notice',
      'anchor' => 'final-notice',
      'step_name' => 'Send a final notice',
      'step_text' => 'If the late-fee invoice is also ignored, send a written final notice by email and printed letter. State the next step, give a hard deadline, and stop.',
      'html' => <<<'HTML'
<p>If the new invoice with the late fee is also ignored, you're dealing with the small minority of invoices that need formal handling, since most paying clients sort themselves out long before this step. Most invoices never get here. The ones that do almost always have a story behind them, usually involving the client's business being in real trouble.</p>
<p>Send a final notice in writing, by email and, where possible, by printed letter to the client's business address. The printed copy matters. It shows you're treating the matter formally, and it puts the notice in front of someone other than the email inbox where the previous messages were ignored. Keep a copy of both for your records.</p>
<p>The final notice has four parts:</p>
<ul>
<li><strong>The facts.</strong> Invoice number, original due date, current balance including any late fees, the dates you sent reminders, and the date of the phone call if there was one. No emotion, just the timeline.</li>
<li><strong>The ask.</strong> Pay the full outstanding balance by a specific date. Give the client 7 to 10 days from the date of the notice. Be explicit: "Payment of $[amount] is required by [date]."</li>
<li><strong>The consequence.</strong> State what happens if the deadline is missed. The two normal options are sending the debt to a collections agency, or filing a claim in small claims court. Name the one you actually plan to do, and only the one you actually plan to do. Empty threats damage your credibility.</li>
<li><strong>Bank details.</strong> List your payment options clearly. If the client wants to pay by bank transfer to clear it quickly, don't make them email you for the account number.</li>
</ul>
<p>After the final notice goes out, stop sending reminders. You've stated the deadline and the consequence. Sending a fifth or sixth email at this point only weakens the position. If the deadline passes, follow through on whatever you said you would do, either collections or small claims. If the client pays before the deadline, mark the invoice paid, file the records, and move on. For the full set of options if you reach this point, including when small claims court actually makes sense and when writing it off is the cheaper answer, see <a href="/what-to-do-when-a-client-does-not-pay/">what to do when a client does not pay</a>.</p>
HTML,
    ],

    // 6
    [
      'h2' => 'Email scripts to copy and paste',
      'anchor' => 'email-scripts',
      'html' => <<<'HTML'
<p>The full set of templates from the steps above, in one place, so you can grab whichever one you need without scrolling. Change the bracketed parts and send.</p>
<p><strong>First reminder, day after the due date:</strong></p>
<p><strong>Subject:</strong> Reminder: Invoice 1023 due May 22</p>
<pre>Hi [Client first name],

Just a quick reminder that invoice 1023 for $[amount] was due on May 22, and I haven't seen the payment land yet. I've re-attached the PDF in case it's easier than digging up the original email. Let me know if there's anything I can clear up from my end to get this processed.

Thanks,
[Your first name]</pre>
<p><strong>Second reminder, five days later:</strong></p>
<p><strong>Subject:</strong> Second reminder: Invoice 1023 now overdue</p>
<pre>Hi [Client first name],

I sent a reminder on May 23 about invoice 1023 for $[amount], and I haven't heard back yet. The invoice is now five days overdue.

Could you let me know where it sits on your end, and roughly when payment will be processed? If there's a hold-up I can help with, I'm happy to jump on a quick call.

As a reminder, the terms on the invoice include a 1.5% monthly late fee on overdue balances, which would start applying from today if the invoice isn't paid this week.

Thanks,
[Your first name]</pre>
<p><strong>Final notice, after the late-fee invoice is also ignored:</strong></p>
<p><strong>Subject:</strong> Final notice: Invoice 1024 outstanding</p>
<pre>Hi [Client first name],

This is a formal final notice regarding invoice 1024, originally issued as invoice 1023 on May 8, for [project description].

The current outstanding balance is $[amount], which includes a late fee of $[late fee amount] applied per the terms on the original invoice. The invoice was due on May 22. Reminders were sent on May 23 and May 28. We spoke by phone on May 30, where payment was committed by June 6. Invoice 1024 was issued on June 7 with a due date of June 13.

Payment in full is required by [date, 7 to 10 days from the notice]. If payment is not received by that date, the account will be sent to [collections agency / small claims court] for recovery.

Bank transfer details:
Account name: [your business name]
Bank: [bank name]
Account number: [account number]
Sort code or routing number: [code]

Please contact me directly if you would like to arrange payment or discuss the account.

Regards,
[Your full name]
[Your business name]</pre>
<p>Two notes on using these. First, change the dates and invoice numbers to match your actual situation. Sending a final notice with placeholder text in it tells the client you copied it off the internet and didn't check. Second, send each one as plain text or as a clean signature-free draft. Heavy formatting, banner images, and disclaimers at the bottom of the email make a short, direct message read like a marketing blast, which is the opposite of what you want.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 4,

  'tool_callout_text' => 'You can build the reminder invoice with the late-fee line in the free generator in under a minute.',
  'tool_callout_cta' => 'Open the invoice generator',

  'faqs' => [
    [
      'q' => 'What if the client says payment was sent but I haven\'t received it?',
      'a' => 'Ask for the date the payment went out, the method (bank transfer, check, card), and a reference number or last four digits of the check. Then check your bank again, including the past three or four business days, since transfers between different banks can take 2 to 5 days to clear. If a check is in the mail, ask which address it was sent to, because mailings sometimes go to an old address from a previous invoice. If a week passes with no payment showing up after the client said it was sent, ask for the payment to be reissued and have them void the original. Keep the emails on file so the timeline is documented.',
    ],
    [
      'q' => 'How firm should I be in the second email?',
      'a' => 'Firm enough to signal that the invoice is overdue and you\'re paying attention, but not so firm that you damage the relationship over what\'s often a missed deadline. The right tone is matter-of-fact, not angry. Say the invoice is overdue, give the exact number of days, ask for a status, and mention that the agreed late fee will start applying. Don\'t use words like "urgent" or "demand" yet, those belong in the final notice if you ever get there. The second email is firmer than the first by about one notch, not three.',
    ],
    [
      'q' => 'Is it OK to text a client about an unpaid invoice?',
      'a' => 'Only if texting is already how you communicate with that client. If every other conversation you\'ve had with them was over text, then a short, polite text on the day after the due date works fine and often gets a faster reply than email. If you\'ve only ever emailed them, switching to text feels like an escalation and can come across as pushy. The same rule applies to messaging apps. Match the channel they already use, and keep the message short. If you do text, follow up with an email so there\'s a written record. Two cautions for US senders: under TCPA, commercial texts to a cell phone require the recipient\'s prior express consent (the client has historically texted you = consent in practice, but a marketing-style text to a number they never gave you is not). Second, don\'t automate it. Send the reminder yourself from your own phone or app, one at a time. Automated debt-collection texts have specific opt-out and content requirements under CFPB rules that are easy to miss as a small business.',
    ],
    [
      'q' => 'What if the client just goes quiet?',
      'a' => 'A silent client isn\'t a refusing client. Most quiet stretches are caused by something on their end: a sick week, a key person who left, an accounts payable change. Move through the sequence anyway, step by step, on the schedule above. Two emails, then a phone call, then the new invoice with the late fee, then the final notice. Silence isn\'t a reason to skip steps, but it\'s also not a reason to send seven emails in a week. The pace of the sequence is the point, and it gives the client every reasonable chance to respond before formal action.',
    ],
    [
      'q' => 'Should I CC anyone on the reminder emails?',
      'a' => 'CC the accounts payable contact if the client has given you one, starting with the first reminder. At larger companies the person who hired you is rarely the person who pays you, and CCing AP shortens the cycle. Don\'t CC the client\'s boss, partner, or anyone outside the AP function on the first or second reminder, that reads as going over their head and can damage the relationship. For the phone-call step and the final notice, you can widen the audience: include the AP team and, on the final notice, address the letter to the business itself rather than a single contact, so it can\'t get stuck on one person\'s desk.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'contractor',
  ],

  'related_article_slugs' => [
    'what-to-do-when-a-client-does-not-pay',
    'late-fees-when-and-how-to-charge',
    'net-30-vs-due-on-receipt',
  ],
];
