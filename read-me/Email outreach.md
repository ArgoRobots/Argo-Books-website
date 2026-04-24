# Email Outreach

Argo Books has a built-in outreach system that finds local small businesses, writes them a personal-sounding email about trying Argo Books, and sends it. You can run it fully hands-off — the cron finds new leads, writes emails, tests subject lines, and learns which ones work — or flip it to 'Review before send' mode, where it still generates everything but waits for you to approve each draft in the Leads tab before anything goes out.

Everything lives in the admin dashboard under **Outreach**, which has three tabs: **Leads**, **A/B Tests**, and **Settings**.

## How it works

Once a day the outreach cron does a routine:

1. **Picks a city**, rotating through Saskatchewan first and then expanding outward into the rest of Canada.
2. **Finds small businesses** there by category (plumbers, cafes, salons, and 90-odd other small-business types) and grabs their public contact email from their website.
3. **Writes each one a short email** with OpenAI (currently `gpt-4o-mini`). The email references the kind of business they run and the everyday headaches that category tends to have.
4. **Runs a quiet subject-line A/B test** alongside the send. It splits traffic across 2–4 variant styles and keeps the one that gets clicks.
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

This is where the system runs experiments on your subject lines. You don't have to create tests yourself — when automation is on, it does it for you. But you can also run your own if you want to try a specific idea.

### What it tests

Right now the system tests **subject lines only**. That's the biggest single factor in whether someone opens a cold email, so it's the most useful thing to optimise. The schema is ready for body / sender / CTA tests later.

Each test has 2–4 variants. A variant can be either:

- **A literal subject line** — used exactly as written for every email in that variant. Good for "I have a specific subject I want to try."
- **A style directive** — a short instruction prefixed with `directive:` (e.g. `directive: ask a curiosity question referencing their city`). The AI writes a different subject for each lead but in that style. Good for testing *kinds* of subject lines, not specific wordings. Anything not prefixed with `directive:` is treated as a literal.

When you create a test by hand, you can mix both. When the system creates a test automatically, it always uses directives (because they generalise across different businesses).

### How the experiment runs

While a test is active, every new email gets assigned to a variant by deterministic round-robin (A, B, C, A, B, C…), so the split stays exactly even regardless of cron run boundaries. The `-v{id}` suffix on the `?source=` URL is how clicks get credited to the right variant.

On the test's detail page you'll see, for each variant: assigned count, sends, clicks, CTR, and — once there's enough data — a confidence tag vs the current leader (two-proportion z-test, with 80% and 95% thresholds).

### How a winner gets picked

With automation on, the cron ends a test and promotes the leader when **any** of these is true:

- **Significant** — the leader's z-test vs every other variant is significant at p<0.05, and every variant has ≥30 sends.
- **Time-boxed** — the test is ≥14 days old and every variant has ≥20 sends.
- **Hard timeout** — the test is ≥28 days old; leader picked by CTR so the loop doesn't stall on low volume.

Once a winner is promoted, the cron immediately starts the next cycle. It carries the winning variant forward as variant A (so the current champion keeps being measured) and asks OpenAI for three new directive styles to test against it. If OpenAI errors, it falls back to a curated set of seed directives so the loop never stalls. The first-ever cycle has no prior winner, so it runs with three fresh directives only.

You can also stop a test early, pause it, or promote a winner manually from the detail page.

### Safety pause

If the cron's own pick turns out badly — winner CTR under the configured floor (default 1%, stored in `outreach_pipeline_state.ab_ctr_floor`) — automation sets `ab_auto_enabled = '0'` and surfaces a Resume banner in the Settings tab. This is so a run of bad cycles can't quietly drag CTR downward.

## The Settings tab



### Send mode

Two options, shown as big side-by-side buttons:

- **Auto-send.** The system generates drafts, approves them, and sends them up to the daily limit. No touch from you.
- **Review before send.** The system still generates drafts, but stops there. You open each lead in the Leads tab, read the email, then send it (or tweak it and then send it). Nothing goes out until you click.

You can flip between the two at any time. The next scheduled run honours whichever one is set.

### A/B automation

Two options: **On** or **Off**.

- **On** — the system manages subject-line tests for you, as described above.
- **Off** — any test that's currently running keeps running, but no new ones get started and no winners get promoted automatically. You can still run tests by hand from the A/B Tests tab.

### Current status

Underneath the toggles you'll see a live read-out: active test, days running, variants, sends/clicks, and current leader. Plus the daily send limit and a tail of today's `cron/logs/outreach_pipeline_<date>.log` filtered to A/B automation events — so you can see things like "Promoted variant B after 15 days via timebox" without tailing the log file on the server.

## What you should do, practically

**The no-touch setup:**

1. Go to **Outreach → Settings**, turn both **Auto-send** and **A/B automation** on.
2. That's it. Leave it alone. Check back once a week or so on the A/B Tests tab to see which styles are winning.

**The cautious setup:**

1. Settings → **Review before send**, A/B automation **on**.
2. You open each day's drafts in the Leads tab and approve/edit/send manually. The subject-line experiments still run, measured against whatever you actually send.

**The hands-on setup:**

1. Settings → A/B automation **off**.
2. Run your own tests by hand in the A/B Tests tab when you have specific ideas to try.

You can switch between these whenever — nothing about the data changes; only future behaviour does.

## What actually gets sent

Emails are short (2–3 short paragraphs, under 100 words), mention that Evan is a local Saskatchewan developer (or a Canadian developer if the business isn't in Saskatchewan), and reference the kind of business they run — in general industry terms, never with made-up specifics about the recipient. Every email includes a link to argorobots.com and a soft one-line unsubscribe. Sign-off is always Evan / Argo Books.

If OpenAI returns invalid JSON (or leaves out the required fields), the draft is saved with `approval_status = 'needs_review'` and held back from auto-approve until an admin edits it.

## Pausing everything quickly

If you ever want to stop cold: flip **Send mode** to *Review before send*. Nothing new goes out. Your existing leads are untouched and nothing is lost. Flip it back to *Auto-send* whenever you're ready to resume.
