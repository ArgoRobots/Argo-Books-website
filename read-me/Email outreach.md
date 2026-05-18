# Email Outreach

Argo Books has a built-in outreach system that finds small businesses and writes them a personal email about trying Argo Books. You can run it in two modes:
- **Auto-send:** The system generates drafts and sends them automatically.
- **Review before send:** The system generates drafts, but stops there. You open each lead in the Leads tab, read the email, then send it (or tweak it and then send it). Nothing goes out until you click.

It also has a A/B test system to learn what works, so it constantly improves itself.

Everything lives in the admin dashboard under **Outreach**, which has five tabs: **Discovery**, **Leads**, **Follow-ups**, **A/B Tests**, and **Settings**.

## Google Places channel

Google Places finds small brick-and-mortar businesses.

### How it works

1. **Picks a city**, rotating through Saskatchewan first and then expanding outward into the rest of Canada.
2. **Finds small businesses** there by category (plumbers, cafes, salons, and 90-odd other small-business types) and grabs their public contact email from their website.
3. **Writes each one a short email** with Gemini (currently `gemini-2.5-flash`). The email references the kind of business they run and the everyday headaches that category tends to have.
4. **Runs a A/B test** alongside the send. It splits traffic across 2–4 variants and keeps the one that gets the most clicks or replies.
5. **Sends the first emails**, up to the daily cap (`OUTREACH_DAILY_SEND_LIMIT`), with a tracked link so clicks can be attributed back to the lead and variant.
6. **Schedules follow-ups** — when a first email goes out, the system queues a follow-up sequence (default: 3 more emails at +3, +7, and +14 days). The schedule is configurable in Settings.
7. **Halts follow-ups** for any lead who replied, unsubscribed, or hard-bounced since the last run.
8. **Drafts each follow-up** with Gemini about a day before it's due to send. Each one personalizes against the lead's business, the original first email, and a per-touch "intent" (e.g. "gentle bump", "different angle", "final note before closing"). The intent comes from the active follow-up A/B test or the default in Settings.
9. **Sends follow-ups** that are approved, up to the daily follow-up cap (`OUTREACH_DAILY_FOLLOWUP_LIMIT`, default 75 across all touch positions). In Auto-send mode, drafts auto-approve and go straight out. In Review-before-send mode, they queue in the Follow-ups tab for you to approve.

## Shopify channel

Finds small Canadian Shopify sellers in their first 3–24 months — stores that are past the "just launched" stage but likely haven't yet found solid accounting software

### How it works

1. SerpAPI runs a `site:myshopify.com` dork query, returning `.myshopify.com` storefronts.
2. The evaluator fetches each storefront's `/products.json`, checks product count (5–∞) and age of the oldest product (3–24 months), and looks for a Canadian address signal (postal code or province) on the storefront.
3. It then scrapes the store's contact page for a direct email address. Role-mailbox addresses (`support@`, `partnerships@`, etc.) are rejected; only personal or general-contact addresses are accepted.
4. Stores that pass all checks are imported as leads (`status='imported'`). Stores that fail are recorded with a `reject_reason` so they aren't re-evaluated on future runs.

## The Discovery tab

Manual discovery for both channels — useful for spot-checking what the cron would find before letting it run on its own, or for sourcing leads outside the cron schedule.

- **Google Places** — the city / province / category / size / limit form. Searches Google Places live and previews matching businesses with their scraped contact emails. Pick which ones to import.
- **Shopify** — pick a SerpAPI dork from the rotation (or write a custom one) and run it. Each result gets evaluated by the same filter as the cron (Canadian signal, 3–24 month age, 5+ products, non-gatekept email, not agency-operated) and the table shows fit / rejected / already-imported with the reject reason. Import any fit row, or "Import All Fits" in bulk.

Both panels show the daily quota state so you don't blow through SerpAPI's free tier mid-day. The Shopify panel respects the same `SERPAPI_DAILY_QUERY_LIMIT` and `OUTREACH_DAILY_SHOPIFY_DISCOVERY_LIMIT` env vars the cron uses.

## The Leads tab

This is the list of businesses the system has found or that you've added manually.

From here you can:

- **Add a lead manually** or import a CSV spreadsheet.
- **Open a lead** to see the AI-generated email, edit the draft before it's sent, or mark the conversation status (interested / not interested / onboarded / replied).
- **Bulk-generate drafts** or **bulk-send emails** for a group of leads you've selected.
- **See the full activity history** for any lead — every draft, every send, every click.

Every outreach email's `argorobots.com` link is rewritten to include a `?source=outreach-{leadId}` parameter (plus `-v{variantId}` when the lead was assigned to an A/B variant). Hits land in `referral_visits` and show up on the A/B table as "Clicked" automatically.

## The Follow-ups tab

This is the review queue for follow-up emails. It only matters in Review-before-send mode. In Auto-send mode, follow-ups are sent right away.

The tab has five sub-views:

- **Pending review** — drafts waiting for you to approve. The pill carries a count badge so you can tell at a glance whether there's work to do.
- **Approved & queued** — drafts you've approved that are waiting for their scheduled send time.
- **Upcoming** — touches that are scheduled but haven't been drafted yet (drafting happens about a day before each send).
- **Sent** — what's gone out in the last 30 days.
- **Halted / failed** — sequences that stopped (lead replied, unsubscribed, bounced, you manually halted, or the AI couldn't produce a draft).

For each pending row you can:

- **Approve & queue** — sends after the scheduled time.
- **Regenerate draft** — re-draft if the wording doesn't feel right.
- **Skip this touch** — drop just this one touch; the next touch in the sequence still goes out on its original schedule.
- **Halt sequence** — stop ALL remaining follow-ups for this lead.

Bulk-select via checkboxes to approve, skip, or halt sequences for multiple rows at once.

You can also see the per-lead sequence (every touch + status + scheduled date) by opening any lead in the Leads tab.

## The A/B Tests tab

This is where the system runs experiments on the things that affect open and click rate. You don't have to create tests manually because it's automated. But you can also create your own if you want to try a specific idea.

### What it can test

Each test targets one **variant type**:

- **Subject line** — the biggest single factor in whether a cold email gets opened.
- **Email body** — the AI's writing style and structure for the message itself.
- **CTA / offer** — the framing of what the recipient gets ("free 1-year license for feedback" vs other offers).
- **Sender from-name** — the name the email appears to come from (e.g. `Evan` vs `Evan from Argo Books` vs `Argo Books`).
- **Preheader** — the snippet most inboxes show next to the subject as a preview.
- **Format** — full HTML email with logo and styling vs plain text. Plain text often outperforms styled HTML in cold outreach because it looks like a human's email rather than marketing.
- **Personalization depth** — with vs without the AI-generated business summary (which costs a Gemini call per lead). Use this to find out whether that extra call is worth keeping.
- **Follow-up sequence** — tests the whole follow-up strategy as one unit. Each variant defines an intent per touch (e.g. variant A: bump → reframe → close; variant B: value tip → question → close; variant C: persistent bump). A lead gets assigned a variant when their first email goes out and stays on it through the whole sequence so attribution is clean. The system ships with 3 starter variants out of the box in `draft` status — activate from this tab when ready.

Only one test can be active at a time, regardless of type — that keeps the math clean. The auto-loop creates the next cycle as soon as the current one promotes.

### How variants work

Each test has 2–4 variants. The way variant content is interpreted depends on the type:

- **Subject / body / CTA** can be either:
  - **A literal value** — used exactly as written for every email in that variant. Good for "I have specific wording I want to try."
  - **A style directive** prefixed with `directive:` (e.g. `directive: ask a curiosity question referencing their city`). The AI generates fresh content in that style for each lead. Good for testing *kinds* of writing, not specific wordings. Anything without the prefix is treated as a literal.
- **Sender / preheader** are always literal strings.
- **Format / personalization** use a fixed two-variant template (`html` vs `plain`, `on` vs `off`). The form fills these automatically when you pick the type — you don't author them.

When you create a test by hand, the form adapts to the type you pick. When the system creates a test automatically, it uses directives for subject (so they generalise across different businesses) or the fixed pool for sender / format / personalization.

### How the experiment runs

While a test is active, every new email gets assigned to a variant by deterministic round-robin (A, B, C, A, B, C…), so the split stays exactly even regardless of cron run boundaries. The `-v{id}` suffix on the `?source=` URL is how clicks get credited to the right variant.

On the test's detail page you'll see, for each variant: assigned count, sends, clicks, CTR, and once there's enough data — a confidence tag vs the current leader.

### How a winner gets picked

With automation on, the cron ends a test and promotes the leader when **any** of these is true:

- **Significant** — the leader's z-test vs every other variant is significant at p<0.05, and every variant has ≥30 sends.
- **Time-boxed** — the test is ≥14 days old and every variant has ≥20 sends.
- **Hard timeout** — the test is ≥28 days old; leader picked by CTR so the loop doesn't stall on low volume.

Once a winner is promoted, the cron immediately starts the next cycle.

### Safety pause

The follow-up sequence A/B type auto-pauses if the configured touch count changes while a test is active (e.g. you add a 4th touch in Settings but the active test only has intents for 3 touches). The mismatch shows up in the A/B Tests tab so you can either match the test to the new shape or revert the Settings change.

## The Settings tab

The Settings tab has two runtime controls plus the sequence configuration:

- **Outreach system** — master enable/disable for the whole pipeline.
- **Send mode** — Auto-send vs Review-before-send (affects both first emails AND follow-ups).
- **Follow-up sequence** — an editable table of touches. Each row is one touch: how many days after the previous touch it sends (1-90), and a default "intent" string that drives Gemini's wording (used when no follow-up A/B test is active). Add/remove rows for between 0 and 6 follow-up touches. Setting 0 touches disables follow-ups entirely.

A/B automation runs unconditionally whenever the outreach system is enabled — there's no separate on/off toggle. The Settings tab also shows the active A/B test snapshot and a tail of the day's pipeline log for quick health checks.

## What you should do

**The no-touch setup:**

1. Go to **Outreach → Settings**, turn **Auto-send** on.
2. That's it. Leave it alone. Check back once a week or so to check on the emails to ensure they still look right. First emails and follow-ups both auto-approve and go straight out.

**The cautious setup:**

1. Go to **Outreach → Settings**, turn **Auto-send** off.
2. Open the drafts in the **Leads** tab every day, review or edit them, then send the email (or use bulk send). New first emails will queue here.
3. Open the **Follow-ups** tab to review drafted follow-ups for leads who already received their first email. Approve / regenerate / skip / halt per row, or use bulk actions.

## What actually gets sent

Each lead receives a sequence of emails — by default the first email plus 3 follow-ups (4 total), spaced +3, +7, +14 days after the first email. The count and gaps are configurable in Settings.

- **First email** — short (2–3 short paragraphs, under 100 words), AI-personalized to the lead's category and city. Includes a tracked argorobots.com link and a soft one-line unsubscribe.
- **Follow-ups** — also AI-personalized, threaded as `Re:` replies to the original so they land in the recipient's existing inbox conversation rather than as fresh emails. Each touch has its own intent (gentle bump / different angle / final note before closing), so the sequence doesn't read as the same email three times.

The sequence automatically halts when the lead replies, unsubscribes, hard-bounces, or you manually halt it. Halted sequences sit in the Follow-ups tab's Halted/failed sub-view for the record.
