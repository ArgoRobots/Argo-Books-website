# Marketing situation

Context snapshot for anyone (including Claude) picking up marketing work. Numbers are as of **2026-09-03**. Update this file when the picture changes.

Related: [Google Ads economics.md](Google%20Ads%20economics.md), [Email outreach.md](Email%20outreach.md).

## Where things stand

- Solo founder, bootstrapping. Argo Books has been in development for about 2 years.
- Windows and Linux only. No macOS build yet.
- **2 paying customers.** Both signed up around May, both auto-renewed and the subscriptions are still active, although they don't use the app.
  - Customer 1 came from a YouTube video (the receipt scanning one).
  - Customer 2 came from Google search.
- Pricing: \$15/month or \$150/year. Customer 1 is paying \$10/month with no payment processing fee added, because I increased the price after he signed up.
- Revenue: **\$77.22 CAD all time**, \$25.74 in the last 30 days. 2 active licenses, 0% churn so far.
- **The "LTV" tile on `/admin/marketing-funnel/` is not lifetime value.** It computes `total_revenue / total_paying` (see `admin/marketing-funnel/index.php`), which is revenue booked to date per customer. It reads low early and drifts up as customers renew. Do not quote it as LTV.
- **LTV is not known.** Both customers are only a few months in and neither has churned, so there is no retention data to calculate it from.
- App telemetry (as of 28 Aug 2026, excluding my own device): **35 unique users**, 21 active in the last 30 days, 8 on a Premium identity. Only 9 of the 35 ever came back on a second day.
- **Both paying customers have not opened the app in over a month**, despite still being billed. A feedback email went to both and neither replied. 0% churn is not a retention signal yet, because the numbers are too small and not enough time has passed.

## Traffic numbers are not trustworthy yet

Read this before drawing any conclusion from the visitor counts below.

Internal analytics reports about 4.9k visitors all time, 82% of them **direct** (4,206), 14% referral (698), 4% organic search (194), 1% organic social (47). Google Search Console over roughly the same window reports **46 clicks** from 12.8K impressions, 0.4% CTR, average position 63.7.

Those two pictures do not reconcile, and the internal one is the suspect one:

- 4,206 direct visitors means "no referrer sent". This is a suspicion, not a measurement. Nobody has verified how much of it is bots, so do not state it as settled. For a site with essentially no brand awareness, almost nobody is typing the URL in. Bots, scrapers, uptime checks, and referrer-stripped traffic all land in this bucket. The site's bot filter (`is_likely_bot()` in `statistics.php`) is basic and probably not catching everything.
- Average position in Search Console is very low. Organic search is indexed but barely being served to anyone.

What **is** trustworthy is app telemetry, because it requires someone to actually install and run a desktop app: 35 unique users. Work backwards from that number, not from 4.8k.

## Funnel (all traffic, all time)

| Step | Count |
|---|---|
| Landing | 4,900 |
| Downloads page | 411 |
| Download click | 60 |
| App first run | 34 |
| Premium signup | 2 |
| Premium paid | 2 |

The landing figure may be inflated by bots, so the 92% drop from landing to downloads page is not useful. Treat it as unresolved rather than explained. The steps that are real: 60 download clicks produced 34 first runs, and 2 of those 34 became paying customers. Conversion after install is fine. The problem is that only ~34 humans have ever installed it.

Top entry pages: `/` (2.34), `/downloads/` (325), `/pricing/` (259), `/features/invoicing/` (224), `/compare/argo-books-vs-quickbooks/` (145).

### Per-source funnel, and why YouTube traffic is different

From the users-by-source export, 3 Sept 2026. Summing the nine `youtube-*` video CTA sources (excluding the channel bio link):

| Step | YouTube video links | All traffic |
|---|---|---|
| Landings | 49 | 4,900 |
| Download clicks | 16 (**32.7%**) | 408 to 59 (14.5%) |
| Installs | 9 (56.3%) | 34 (57.6%) |
| Paying | 1 (11.1% of installs) | 2 (5.9% of installs) |

Video CTA links point at `/downloads/?source=...`, so a YouTube visitor's first page is the downloads page. That means the landing-to-downloads-page step does not exist for this traffic, and the bot question above does not apply to it.

The number that matters: **YouTube visitors click download at 32.7%, more than double the 14.5% of everyone else.** This is the most reliable conversion figure. Once someone has clicked download, source stops mattering (56.3% vs 57.6% install rate).

Link CTR for reference: the receipt scanner video has 223 views and produced 26 landings, an **11.7%** click-through on a description link. Useful as a benchmark when a sponsored placement quotes its own link CTR, because a sponsor slot interrupts a browsing viewer or has different intent, while these 26 came from people who searched for the topic.

## The reachability gap

Almost everyone who installs Argo Books is anonymous. There is no account requirement at install (deliberately, it is part of the positioning), so:

- Only paying customers hand over an email address.
- 8 people signed up to the community section on the website, but those accounts are not linked to desktop telemetry, so there is no way to tell which of them ever ran the app.
- The remaining free users cannot be contacted, surveyed, or re-engaged. Every install is currently a one-shot.

This is worth fixing before pushing more traffic in, because it multiplies the value of every future install. The fix is **not** an email gate at first run: "no account, runs on your computer" is a differentiator against the competitors. Better options are an in-app surveys, or an optional email field.

## What has been tried

### YouTube channel
9 videos, **496 total views** (channel checked 3 Sept 2026). Best performer is "Best Free AI Receipt Scanner" (223 views, 3 months old), which produced one of the two paying customers. Also posting comments on other accounting software videos mentioning Argo Books.

Current view counts, newest first:

| Video | Views | Age |
|---|---|---|
| Best Free Invoicing Software for Small Business (2026) | 5 | 15 hours |
| QuickBooks Receipt Scanner vs a Free Alternative (2026) | 8 | 2 days |
| How To Redeem Your Argo Books License Key | 7 | 12 days |
| Scan Receipts Into Excel Automatically (Free) | 40 | 3 weeks |
| QuickBooks Desktop Is Discontinued: What To Do Now | 51 | 1 month |
| Best Free QuickBooks Alternative in 2026 | 63 | 1 month |
| Free invoicing with online payments | 28 | 2 months |
| Best Free AI Receipt Scanner | 223 | 3 months |
| Argo Books Demo | 73 | 4 months |

Subscriber count: 3.

### Stack Social
A Stack Social went live late August, and has resulted in one paying customer so far. Argo Books Premium is being sold as a lifetime deal of $83.99 CAD. My share of the revenue is 45-50%, depending on how they acquired each customer. Stack Social is generally either a hit or a miss, with most companies making almost no sales, while some do very well, with hundreds, or thousands of sales. While ~$40 revenue on each sale is very little considering I also have business expenses, based on my research:
- Around 80% of people who buy lifetime software deals never use the software, or use it very little. 
- Lifetime users tend to churn at similar rates as subscription users.
Plus, this is a great opportunity to get customer reviews, which would be extremely valuable because I currently have no social proof. I could add this social proof to my website's landing page and include it in my outreach emails.

### Google Ads
About CA\$300 spent, 0 attributable customers. Details in [Google Ads economics.md](Google%20Ads%20economics.md). Roughly two thirds of spend went to mobile and tablet clicks that cannot install a Windows app, which was a mistake. Even with device exclusions, realistic cost per customer looks like \$300+ against an unproven LTV. Not viable.

### Cold email outreach
~1,100 emails sent starting around January 2026. A couple of replies, zero customers. Stopped.

### Editorial outreach
22 emails to blog and article writers who cover accounting/bookkeeping software, started July 20226. Zero responses. Main friction is finding unique targets, and the auto-discovery feature in the admin outreach page does not work well.

### YouTuber outreach
82 emails sent starting about a month ago. 2 responses, both rejections (one "schedule is full", one asked which regions Argo Books supports then went quiet). A third response showed interest then said that the email had been forwarded to someone else for consideration. A fourth response gave a quote of $1000 USD as a flat-fee instead of an affiliate, which is currently being negotiated.

### Reddit
0-5 comments a day, roughly 100 total. Posts get auto removed immediately even when they follow the rules and do not mention Argo Books, with no explanation given. Account is 2 months old with 21 karma, which is the likely cause. About 90% of comments get 1 view, and some get 10-100.

### LinkedIn
16 connections, 53 profile views, 943 post impressions (879 of those from the latest post), 4 posts. Messaged startup/business/accounting influencers about the affiliate program. Nothing has come of it, at least that we can measure.

### Directory listings: done, no measurable traffic
Already listed on G2, Capterra, Product Hunt, and roughly 30 cheap Product Hunt copycats. None of it brought traffic.

That result is expected, and it clarifies what a listing is actually for. The value was never the directory's own visitors, it's that the directory's pages rank in Google.

- G2 and Capterra do rank, but they rank on review volume, so they stay dead until there are reviews. Hard with 2 customers.
- The ~30 copycats fail the test entirely. No further effort there.

### SEO: indexed, but not ranking yet
Programmatic SEO pages, clean site structure, all pages indexed in Google Search Console, auto-submission to Bing and others. It did produce one of the two paying customers (I assume, given it was unattributed). But 36 clicks in 3 months at average position 64.5 means the pages exist and are indexed without ranking anywhere useful.

## macOS

Not shipped. A Mac signup list went live on the downloads page about a week ago with 3 signups, one of which is a family friend, which may not have real intent.

Build, signing, and release do not need owned hardware. GitHub Actions provides hosted macOS runners (Apple Silicon on current images), so `codesign`, `notarytool`, and DMG packaging can all run in CI with the Developer ID certificate stored as a repo secret. Still needs an Apple Developer Program membership (\$99/year USD) for the certificate and notarization.

Manual testing does need a Mac, and specifically Apple Silicon (M1 or newer):

- **Test on what customers actually run.** Nearly all Macs sold in the last several years are Apple Silicon, so the shipped build should be `osx-arm64` and tested natively on arm64. An Intel Mac can only really test the x64 build, which runs on Apple Silicon through Rosetta 2 translation rather than natively. Apple has also stopped adding Intel support in new macOS releases, so an Intel machine goes stale fast.
- **The fingerprint login feature needs real Touch ID hardware.** CI runners and cloud Mac services have no biometric sensor, so this path cannot be automated or rented. It also needs a separate macOS implementation: the Windows Hello APIs have no macOS equivalent, so the Mac side goes through Apple's LocalAuthentication framework.

**The build itself is not the hard part.** Argo Books is Avalonia on .NET 10, so producing a macOS binary is a publish target (`osx-arm64`), which takes a few minutes. The actual costs are a Mac to test on and the Apple Developer membership.

Given 2 paying customers and little macOS demand, the hardware purchase is a real cost. The signup list is the right way to gather that evidence before spending. Although, it's known that macOS is popular among small business owners in general. I plan on buying a Mac in the coming months.

## Honest read

The two things that produced customers are YouTube and organic search. Both are slow, compounding, and free. Everything push-based (cold email, editorial outreach, YouTuber outreach, paid ads) has produced zero customers.

The real constraint is not conversion, it's reach. 34 people have installed and run the app, and 2 of them have paid.

The second problem is retention. Both paying customers have gone a month without opening the app. Most of the free users use the app for a few minutes, do almost nothing, sometimes come back a few days later, do nothing, then leave. Not sure why. This could be a problem with the telemetry (unlikely, but possible), normal user behavior, or a real problem.

## Next steps

Ordered. Everything else is parked (see below).

### 1. YouTube

**Ride the QuickBooks Desktop discontinuation while it lasts.** There is a population being forced off desktop software onto a \$360+/year subscription, searching right now for what to do. Argo Books is a free desktop alternative, which is close to a perfect fit, and this window closes.

**Title every video as the search query, never as the product.** The channel's own numbers already prove this: "Best Free AI Receipt Scanner" got 221 views while "Argo Books Demo" got 73 despite being a month older. The QuickBooks-titled videos are the fastest starters. Topic selection is doing nearly all the work at this size.

**How to pick topics** (in order of usefulness):

1. **Google Search Console, Performance → Queries.** 8.95K impressions means Google is already showing the site for real searches. Sort by impressions and look specifically at high-impression, zero-click queries. That is validated demand that is currently being lost, and it comes from the actual audience rather than a guess. Best source available, and it is free and already owned.
2. **YouTube search autocomplete.** Type "quickbooks", "accounting software", "bookkeeping", "invoice", "receipt" into YouTube search and read the suggestions. Those are literal queries ordered roughly by volume. Twenty minutes produces a long title list.
3. **Check what already ranks for each candidate query.** If old videos with high view counts hold the top spots, demand is sustained. If the top results are thin or outdated, that is an opening.
4. **Complaint mining.** Comments under QuickBooks videos, and threads in r/smallbusiness and r/bookkeeping, are full of repeated grievances (price increases, data export, forced migration). Each recurring complaint is a video title.

Validate every title against YouTube autocomplete before committing. Do not invent queries.

### 2. Microsoft Store listing

About a day of work. The Store accepts unpackaged Win32 apps, so the existing installer can be listed without repackaging as MSIX. Mostly forms: description, screenshots, age rating, privacy policy, then certification review. Individual developer account is a one-time fee, around \$19 USD (confirm current pricing).

Expect very little traffic. Store search volume for accounting software is thin and the Store skews toward games and big-name apps. The reasons to do it anyway are that it is permanent for one day of work, and that a Store listing is a trust signal for a small-business owner deciding whether to run an unknown `.exe` on the machine holding their financial records.

### 3. Close the reachability gap in the app

See [The reachability gap](#the-reachability-gap). An in-app prompt, not an email gate. Worth doing before the YouTube push lands rather than after.
