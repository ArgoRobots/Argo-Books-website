# Search Console and Bing Webmaster submission checklist

Run this once at launch, then monitor weekly for the first month.

## Pre-flight (one-time)

- [ ] Confirm `https://argorobots.com/sitemap.xml` returns HTTP 200.
- [ ] Confirm `https://argorobots.com/sitemap-tools.xml` returns HTTP 200 and lists every Phase A-D URL.
- [ ] Confirm `https://argorobots.com/robots.txt` references both sitemaps and does not disallow any tool path.
- [ ] Run `php scripts/seo-audit.php --base=https://argorobots.com` one more time. Confirm zero findings.

## Google Search Console

1. [ ] Sign in at https://search.google.com/search-console with the Google account that owns argorobots.com.
2. [ ] If the property does not exist, add `https://argorobots.com` as a Domain property (DNS verification). Domain properties cover www and non-www, http and https. Add the TXT record at the registrar, click Verify.
3. [ ] In the property, go to Sitemaps. Submit both:
   - `sitemap.xml`
   - `sitemap-tools.xml`
4. [ ] Wait 24 hours. Both sitemaps should show "Success" with a discovered URL count matching the audit script's URL list.
5. [ ] In Coverage / Pages, watch for indexation. Expected timeline: first 5-10 URLs indexed within a week, the rest within a month.
6. [ ] In URL Inspection, check `https://argorobots.com/invoice-generator/`. Confirm: "URL is on Google" (or "URL is not on Google" with a clear reason like "Discovered but not yet crawled"). If "Excluded: noindex tag", something regressed; re-run the audit.

## Bing Webmaster Tools

1. [ ] Sign in at https://www.bing.com/webmasters with a Microsoft account (the outlook.com account is fine).
2. [ ] If the property does not exist, click "Import from Google Search Console". Bing pre-fills everything and skips re-verification.
3. [ ] If the GSC import fails, add the site manually and verify via the meta tag method (paste the tag into `invoice-generator/layout.php`'s `<?= $extra_head ?>` slot temporarily, then delete it after verification clears).
4. [ ] In Sitemaps, submit both `sitemap.xml` and `sitemap-tools.xml`.
5. [ ] In URL Inspection, spot-check 3 URLs (one tool page, one niche page, one article).

## Weekly monitoring (first 30 days)

Each Monday morning:

- [ ] GSC -> Coverage: how many tool / niche / article URLs are indexed? Sketch the count in a tracking sheet so you can see the trend.
- [ ] GSC -> Performance: which queries are starting to appear? Note any surprises (queries you did not expect to rank for, or absent ones you did expect).
- [ ] GSC -> Manual Actions: should be empty. If anything appears, stop and read it carefully.
- [ ] Bing -> Search Performance: same checks as GSC.
- [ ] Re-run `php scripts/seo-audit.php --base=https://argorobots.com`. Confirm zero new findings.

## When to escalate

- A sitemap URL count drops by more than 10% between weeks: something in `sitemap-tools.xml.php` or one of the data files regressed. Investigate.
- A "Crawled, not indexed" status persists past 30 days for a high-priority URL: review the page's content quality, internal linking, and canonical setup.
- Manual Action appears: read it verbatim, fix the underlying cause, request review.
