# Email Outreach

Argo Books has a built-in outreach system that finds local small businesses, writes them a personal-sounding email about trying Argo Books, and sends it. You can run it fully hands-off — the cron finds new leads, writes emails, runs A/B tests to learn what works, and promotes the winners — or flip it to 'Review before send' mode, where it still generates everything but waits for you to review (or edit) each draft in the Leads tab and click **Send Email** before anything goes out.

Everything lives in the admin dashboard under **Outreach**, which has three tabs: **Leads**, **A/B Tests**, and **Settings**.

## How it works

Once a day the outreach cron does a routine:

1. **Picks a city**, rotating through Saskatchewan first and then expanding outward into the rest of Canada.
2. **Finds small businesses** there by category (plumbers, cafes, salons, and 90-odd other small-business types) and grabs their public contact email from their website.
3. **Writes each one a short email** with Gemini (currently `gemini-2.5-flash`). The email references the kind of business they run and the everyday headaches that category tends to have.
4. **Runs a quiet A/B test** alongside the send. It splits traffic across 2–4 variants and keeps the one that gets clicks. By default it tests subject lines; you can extend it to other things (body, CTA, sender name, preheader, HTML-vs-plain, with-vs-without personalization).
5. **Sends the emails**, up to the daily cap (`OUTREACH_DAILY_SEND_LIMIT`), with a tracked link so clicks can be attributed back to the lead and variant.

You decide whether to let all of that run on its own or pause at each step for you to review. Both modes are just a toggle.

## The Leads tab

This is the list of businesses the system has found or that you've added manually. Each row shows the business name, category, city, status (new / draft ready / contacted / replied / etc.), whether they've clicked, and whether they've replied.

From here you can:

- **Search the web for new businesses** in a specific city and category, preview the results, and pick which ones to import.
- **Add a lead by hand** or import a spreadsheet.
- **Open a lead** to see the AI-generated email, edit the draft before it's sent, or mark the conversation status (interested / not interested / onboarded / replied).
- **Bulk-generate drafts** or **bulk-send emails** for a group of leads you've selected.
- **See the full activity history** for any lead — every draft, every send, every click.

Every outreach email's `argorobots.com` link is rewritten to include a `?source=outreach-{leadId}` parameter (plus `-v{variantId}` when the lead was assigned to an A/B variant). Hits land in `referral_visits` and show up on the Leads table as "Clicked" automatically.

## The A/B Tests tab

This is where the system runs experiments on the things that affect open and click rate. You don't have to create tests yourself — when automation is on, it does it for you. But you can also run your own if you want to try a specific idea.

### What it can test

Each test targets one **variant type**:

- **Subject line** — the biggest single factor in whether a cold email gets opened.
- **Email body** — the AI's writing style and structure for the message itself.
- **CTA / offer** — the framing of what the recipient gets ("free 1-year license for feedback" vs other offers).
- **Sender from-name** — the name the email appears to come from (e.g. `Evan` vs `Evan from Argo Books` vs `Argo Books`).
- **Preheader** — the snippet most inboxes show next to the subject as a preview.
- **Format** — full HTML email with logo and styling vs plain text. Plain text often outperforms styled HTML in cold outreach because it looks like a human's email rather than marketing.
- **Personalization depth** — with vs without the AI-generated business summary (which costs a Gemini call per lead). Use this to find out whether that extra call is worth keeping.

Only one test can be active at a time, regardless of type — that keeps the math clean. The auto-loop creates the next cycle as soon as the current one promotes.

### How variants work

Each test has 2–4 variants. The way variant content is interpreted depends on the type:

- **Subject / body / CTA** can be either:
  - **A literal value** — used exactly as written for every email in that variant. Good for "I have a specific wording I want to try."
  - **A style directive** prefixed with `directive:` (e.g. `directive: ask a curiosity question referencing their city`). The AI generates fresh content in that style for each lead. Good for testing *kinds* of writing, not specific wordings. Anything without the prefix is treated as a literal.
- **Sender / preheader** are always literal strings.
- **Format / personalization** use a fixed two-variant template (`html` vs `plain`, `on` vs `off`). The form fills these automatically when you pick the type — you don't author them.

When you create a test by hand, the form adapts to the type you pick. When the system creates a test automatically, it uses directives for subject (so they generalise across different businesses) or the fixed pool for sender / format / personalization.

### How the experiment runs

While a test is active, every new email gets assigned to a variant by deterministic round-robin (A, B, C, A, B, C…), so the split stays exactly even regardless of cron run boundaries. The `-v{id}` suffix on the `?source=` URL is how clicks get credited to the right variant.

On the test's detail page you'll see, for each variant: assigned count, sends, clicks, CTR, and — once there's enough data — a confidence tag vs the current leader (two-proportion z-test, with 80% and 95% thresholds).

### How a winner gets picked

With automation on, the cron ends a test and promotes the leader when **any** of these is true:

- **Significant** — the leader's z-test vs every other variant is significant at p<0.05, and every variant has ≥30 sends.
- **Time-boxed** — the test is ≥14 days old and every variant has ≥20 sends.
- **Hard timeout** — the test is ≥28 days old; leader picked by CTR so the loop doesn't stall on low volume.

Once a winner is promoted, the cron immediately starts the next cycle.

For directive-style types (subject / body / CTA), it carries the winning variant forward as variant A so the current champion keeps being measured, and asks Gemini for three new directive styles to test against it. If Gemini errors, it falls back to a curated set of seed directives so the loop never stalls. The first-ever cycle has no prior winner, so it runs with three fresh directives only.

For fixed-pool types (sender / format / personalization), the next cycle just re-runs the fixed variants — there's no carry-forward, since the pool itself is the test. Each new cycle is a fresh measurement against current conditions.

You can also stop a test early, pause it, or promote a winner manually from the detail page.

### Safety pause

If the cron's own pick turns out badly — winner CTR under the configured floor (default 1%, stored in `outreach_pipeline_state.ab_ctr_floor`) — automation sets `ab_auto_enabled = '0'` and shows an "A/B automation is off" banner at the top of the Settings tab with the pause reason. Flip the toggle below it to resume. This is so a run of bad cycles can't quietly drag CTR downward.

## The Settings tab



### Send mode

Two options, shown as big side-by-side buttons:

- **Auto-send.** The system generates drafts, approves them, and sends them up to the daily limit. No touch from you.
- **Review before send.** The system still generates drafts, but stops there. You open each lead in the Leads tab, read the email, then send it (or tweak it and then send it). Nothing goes out until you click.

You can flip between the two at any time. The next scheduled run honours whichever one is set.

### A/B automation

Two options: **On** or **Off**.

- **On** — the system manages tests for you: it creates new cycles, picks winners, and starts the next cycle as soon as one promotes. Successive cycles rotate across types in this order: **subject line → sender from-name → format (HTML vs plain) → personalization (with vs without business summary) → (loop back to subject)**. The Settings panel shows which type is queued next. Body, CTA, and preheader tests aren't in the rotation — those need wording you write yourself, so they're always started manually from the A/B Tests tab. Manual tests of any type still work — they just delay the rotation while they run, since only one test can be active at a time.
- **Off** — any test that's currently running keeps running, but no new ones get started and no winners get promoted automatically. You can still run tests by hand from the A/B Tests tab.

### Current status

Underneath the toggles you'll see a live read-out: active test, days running, variants, sends/clicks, and current leader. Plus the daily send limit and a tail of today's `cron/logs/outreach_pipeline_<date>.log` filtered to A/B automation events — so you can see things like "Promoted variant B after 15 days via timebox" without tailing the log file on the server.

## What you should do, practically

**The no-touch setup:**

1. Go to **Outreach → Settings**, turn both **Auto-send** and **A/B automation** on.
2. That's it. Leave it alone. Check back once a week or so on the A/B Tests tab to see which styles are winning.

**The cautious setup:**

1. Settings → **Review before send**, A/B automation **on**.
2. You open each day's drafts in the Leads tab, review or edit them, then click Send Email (or use bulk send) to put them in the queue. The A/B experiments still run, measured against whatever you actually send.

**The hands-on setup:**

1. Settings → A/B automation **off**.
2. Run your own tests by hand in the A/B Tests tab when you have specific ideas to try.

You can switch between these whenever — nothing about the data changes; only future behaviour does.

## What actually gets sent

Emails are short (2–3 short paragraphs, under 100 words), mention that Evan is a local Saskatchewan developer (or a Canadian developer if the business isn't in Saskatchewan), and reference the kind of business they run — in general industry terms, never with made-up specifics about the recipient. Every email includes a link to argorobots.com and a soft one-line unsubscribe. Sign-off is always Evan / Argo Books.

If Gemini returns invalid JSON (or leaves out the required fields), the draft is saved with `approval_status = 'needs_review'` and held back from auto-approve until an admin edits it.

## Pausing everything quickly

If you ever want to stop cold: flip **Send mode** to *Review before send*. Nothing new goes out. Your existing leads are untouched and nothing is lost. Flip it back to *Auto-send* whenever you're ready to resume.
