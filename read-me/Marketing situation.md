# Marketing situation

Context snapshot for anyone (including Claude) picking up marketing work. Numbers are as of **2026-08-03**. Update this file when the picture changes.

Related: [Google Ads economics.md](Google%20Ads%20economics.md), [Email outreach.md](Email%20outreach.md).

## Where things stand

- Solo founder, bootstrapping. Argo Books has been in development for about 2 years.
- Windows and Linux only. No macOS build yet.
- **2 paying customers.** Both signed up about a month ago, both auto-renewed once.
  - Customer 1 came from a YouTube video (the receipt scanning one).
  - Customer 2 came from Google search.
- Pricing: \$15/month or \$150/year. Customer 1 is grandfathered at \$10/month with no payment processing fee added, which is why all-time revenue is \$51.48 rather than \$60.
- Revenue: \$51.48 all time, \$25.74 in the last 30 days. 2 active licenses, 0% churn so far.
- App telemetry: 19 total unique users all time, 7 monthly active, 2 Premium.
- **Both paying customers have not opened the app in over a month**, despite still being billed. A feedback email went to both about 2 weeks ago and neither replied. 0% churn is not a retention signal yet, it just means nobody has reached a renewal decision while noticing they don't use it.

## Traffic numbers are not trustworthy yet

Read this before drawing any conclusion from the visitor counts below.

Internal analytics reports about 4.8k visitors all time, 84% of them **direct** (4,014), 12% referral (586), 4% organic search (172), 1% organic social (31). Google Search Console over roughly the same window reports **29 clicks** from 8.95K impressions, 0.3% CTR, average position 67.5.

Those two pictures do not reconcile, and the internal one is the suspect one:

- 4,014 direct visitors means "no referrer sent". For a site with essentially no brand awareness, almost nobody is typing the URL in. Bots, scrapers, uptime checks, and referrer-stripped traffic all land in this bucket. The site's bot filter (`is_likely_bot()` in `statistics.php`) is basic and clearly not catching everything.
- Average position 67.5 in Search Console is page 7 of results. Organic search is technically indexed but barely being served to anyone.

What **is** trustworthy is app telemetry, because it requires someone to actually install and run a desktop app: 19 unique users all time, 21 first runs. Work backwards from that number, not from 4.8k.

## Funnel (all traffic, all time)

| Step | Count |
|---|---|
| Landing | 4,600 |
| Downloads page | 354 |
| Download click | 31 |
| App first run | 21 |
| Premium signup | 2 |
| Premium paid | 2 |

The landing figure is inflated by bots (see above), so the apparent 92% drop from landing to downloads page is not a real user behavior problem. The steps that are real: 31 download clicks produced 21 first runs, and 2 of those 21 became paying customers. Conversion after install is fine. The problem is that only ~21 humans have ever installed it.

Top entry pages: `/` (2.3k), `/downloads/` (287), `/pricing/` (254), `/features/invoicing/` (218), `/compare/argo-books-vs-quickbooks/` (140).

## The reachability gap

Almost everyone who installs Argo Books is permanently anonymous. There is no account requirement at install (deliberately, it is part of the positioning), so:

- Only paying customers hand over an email address.
- 8 people signed up to the community section on the website, but those accounts are not linked to desktop telemetry, so there is no way to tell which of them ever ran the app.
- The remaining ~17 free users cannot be contacted, surveyed, or re-engaged. Every install is currently a one-shot.

This is worth fixing before pushing more traffic in, because it multiplies the value of every future install. The fix is **not** an email gate at first run: "no account, runs on your computer" is the actual differentiator against QuickBooks Online and it is on the YouTube thumbnails. Better options are an in-app one-question prompt after the user has gotten value (no email needed, submitted anonymously against the existing telemetry ID), or an optional skippable email field offered later for release notes.

Note that having an email is not the same as getting a reply. Both paying customers were emailed directly for feedback and neither responded, which is an argument for asking inside the app while people are actually using it.

## What has been tried

### Google Ads: stopped, not worth restarting yet
About CA\$300 spent, 0 attributable customers. Details in [Google Ads economics.md](Google%20Ads%20economics.md). Roughly two thirds of spend went to mobile and tablet clicks that cannot install a Windows app. Even with device exclusions, realistic cost per customer looks like \$300+ against a \$150/year price. Not viable at current conversion rates.

### Cold email outreach: stopped
~1,100 emails sent starting about 5 months ago. A couple of replies, zero customers. Stopped about 2 months ago.

### Editorial outreach: active, no results yet
22 emails to blog and article writers who cover accounting/bookkeeping software, started about a month ago. Zero responses. Main friction is finding targets: most articles are duplicates of each other, and the auto-discovery feature in the admin outreach page does not work well.

### YouTuber outreach: active, no results yet
74 emails sent starting about a month ago. 2 responses, both rejections (one "schedule is full", one asked which regions Argo Books supports then went quiet).

Outreach for both of the above is sent mostly automatically from an admin page.

### YouTube channel: the only channel that has produced a customer
5 videos, 2 subscribers, ~250 total views. Best performer is "Best Free AI Receipt Scanner" (111 views, 2 months old), which produced one of the two paying customers. Also posting comments on other accounting software videos mentioning Argo Books.

### Reddit: mostly blocked
0-5 comments a day, roughly 100 total. Posts get auto removed almost immediately even when they follow the rules and do not mention Argo Books, with no explanation given. Account is 2 months old with 21 karma, which is the likely cause. About 90% of comments get 1 view, a few get 10-100.

### LinkedIn: too early to tell
16 connections, 53 profile views, 943 post impressions (879 of those from the latest post), 4 posts. Started DMing startup/business/accounting influencers about the affiliate program a week ago. People accept the connection but nobody has replied yet.

### Directory listings: done, no measurable traffic
Already listed on G2, Capterra, Product Hunt, and roughly 30 cheap Product Hunt copycats. None of it brought traffic.

That result is expected, and it clarifies what a listing is actually for. The value was never the directory's own visitors, it is that the directory's pages rank in Google for the queries Argo Books cannot rank for at position 67. "QuickBooks alternatives" surfaces AlternativeTo, not Product Hunt clones. So the only test worth applying to a new listing is: **does this site rank for my buyer's search?**

- G2 and Capterra do rank, but they rank on review volume, so they stay dead until there are reviews. Hard with 2 customers.
- AlternativeTo and Slant rank and are open to anyone. Status unknown, worth checking.
- The ~30 copycats fail the test entirely. No further effort there.

### SEO: indexed, but not ranking yet
Programmatic SEO with guides and feature/compare pages, clean site structure, all pages indexed in Google Search Console, auto-submission to Bing and others. It did produce one of the two paying customers. But 29 clicks in 3 months at average position 67.5 means the pages exist and are indexed without ranking anywhere useful. Impressions are trending up over the last few weeks, which is the early sign to watch.

## macOS

Not shipped. A Mac signup list went live on the downloads page about a week ago with 2 signups, at least one of which is a family friend, so treat that as ~0 real demand signal so far.

**Build, signing, and release do not need owned hardware.** GitHub Actions provides hosted macOS runners (Apple Silicon on current images), so `codesign`, `notarytool`, and DMG packaging can all run in CI with the Developer ID certificate stored as a repo secret. Still needs an Apple Developer Program membership (\$99/year USD) for the certificate and notarization.

**Manual testing does need a Mac**, and specifically Apple Silicon (M1 or newer):

- **Test on what customers actually run.** Nearly all Macs sold in the last several years are Apple Silicon, so the shipped build should be `osx-arm64` and tested natively on arm64. An Intel Mac can only really test the x64 build, which runs on Apple Silicon through Rosetta 2 translation rather than natively. Apple has also stopped adding Intel support in new macOS releases, so an Intel machine goes stale fast.
- **The fingerprint login feature needs real Touch ID hardware.** CI runners and cloud Mac services have no biometric sensor, so this path cannot be automated or rented. It also needs a separate macOS implementation: the Windows Hello APIs have no macOS equivalent, so the Mac side goes through Apple's LocalAuthentication framework.

Touch ID narrows the hardware options, because **Mac mini and Mac Studio have no built-in fingerprint reader**:

- MacBook Air or MacBook Pro (M-series), Touch ID built into the keyboard.
- Mac mini (M-series) plus a Magic Keyboard with Touch ID. That keyboard only does Touch ID on Apple Silicon Macs, which is another reason M1+ rather than Intel.

Given 2 paying customers and no proven macOS demand, this is a real cost with no evidence behind it yet. The signup list is the right way to gather that evidence before spending.

## Honest read

The two things that produced customers are YouTube and organic search. Both are slow, compounding, and free. Everything push-based (cold email, editorial outreach, YouTuber outreach, paid ads) has produced zero customers across roughly 1,200 emails and CA\$300 of spend.

The real constraint is not conversion, it is reach. Roughly 21 people have ever installed the app, and 2 of them paid. Nothing in the funnel needs fixing at that sample size. What is missing is qualified humans arriving at all, and the only two channels that have ever delivered one are the two that take months to compound.

The quiet second problem is retention. Both paying customers have gone a month without opening the app. Filling the top of the funnel does not help much if usage decays to zero within a month of install.

## Next steps

Ordered. Everything else is parked (see below).

### 1. YouTube, at 8+ videos per month

The only proven customer source with headroom, and it costs time rather than money. Current cadence is about 2 per month.

**Ride the QuickBooks Desktop discontinuation while it lasts.** There is a population being forced off desktop software onto a \$360+/year subscription, searching right now for what to do. Argo Books is a free desktop alternative, which is close to a perfect fit, and this window closes.

**Title every video as the search query, never as the product.** The channel's own numbers already prove this: "Best Free AI Receipt Scanner" got 111 views while "Argo Books Demo" got 59 despite being a month older. The two QuickBooks-titled videos are the fastest starters. Topic selection is doing nearly all the work at this size.

**How to pick topics** (in order of usefulness):

1. **Google Search Console, Performance → Queries.** 8.95K impressions means Google is already showing the site for real searches. Sort by impressions and look specifically at high-impression, zero-click queries. That is validated demand that is currently being lost, and it comes from the actual audience rather than a guess. Best source available, and it is free and already owned.
2. **YouTube search autocomplete.** Type "quickbooks", "accounting software", "bookkeeping", "invoice", "receipt" into YouTube search and read the suggestions. Those are literal queries ordered roughly by volume. Twenty minutes produces a long title list.
3. **Check what already ranks for each candidate query.** If old videos with high view counts hold the top spots, demand is sustained. If the top results are thin or outdated, that is an opening.
4. **Complaint mining.** Comments under QuickBooks videos, and threads in r/smallbusiness and r/bookkeeping, are full of repeated grievances (price increases, data export, forced migration). Each recurring complaint is a video title.

Validate every title against YouTube autocomplete before committing. Do not invent queries.

### 2. Microsoft Store listing

About a day of work. The Store accepts unpackaged Win32 apps, so the existing installer can be listed without repackaging as MSIX. Mostly forms: description, screenshots, age rating, privacy policy, then certification review. Individual developer account is a one-time fee, around \$19 USD (confirm current pricing).

**Expect very little traffic.** Store search volume for accounting software is thin and the Store skews toward games and big-name apps. The reasons to do it anyway are that it is permanent for one day of work, and that a Store listing is a trust signal for a small-business owner deciding whether to run an unknown `.exe` on the machine holding their financial records. There is no brand to overcome that objection with yet.

Skip winget and Chocolatey, which reach developers rather than bookkeepers. Flathub is a maybe for the Linux build, same reasoning as the Store.

### 3. Close the reachability gap in the app

See [The reachability gap](#the-reachability-gap). An in-app prompt, not an email gate. Worth doing before the YouTube push lands rather than after.

### 4. Check AlternativeTo and Slant listings

A couple of hours. They rank for "QuickBooks alternative" style searches and are open to anyone.

### Parked

Cold email, editorial outreach, YouTuber outreach, LinkedIn influencer DMs, Google Ads, the affiliate program, macOS, and further programmatic SEO page generation.

The first four are the same push motion that has now failed across roughly 1,200 emails, and the LinkedIn effort is one week into repeating it. The affiliate program has a chicken-and-egg problem: affiliates promote what already sells, and there is no proof of that yet. macOS is a hardware purchase plus a Touch ID port against 2 signups, one of which is a family friend. On SEO, adding more pages while existing ones sit at average position 67.5 is more of something that is not working. Effort belongs on links and depth for the ten pages with real buying intent.
