# Phase C niche-page hand-edit checklist

Apply this checklist line-by-line to every drafted niche file BEFORE committing it.

## Required checks per page

- [ ] **Word count.** `intro_html` + `typical_payment_terms_html` + `tax_notes_html` + sum of FAQ answers >= 300 words, hand-counted. Sample-line-item descriptions do NOT count toward the 300.
- [ ] **No em dashes anywhere.** Search the file for `—` and `&mdash;`. Replace with a comma, colon, or period. A regular hyphen `-` is NOT an acceptable substitute in prose (it reads as a compound-word marker).
- [ ] **No banned words.** "Reconciliation" / "reconcile" never appears in user-facing copy. Use plainer alternatives: "matching", "checking", "balancing", "comparing", "sorting out".
- [ ] **No scary bug language.** This is not a What's New page so it should not arise, but if the LLM drafted anything about "errors" or "crashes" in the generator or in Argo Books, rephrase or remove. Accounting software has to feel rock-solid.
- [ ] **No emojis.**
- [ ] **No AI tells.** Common LLM ticks to strip: "In today's fast-paced world", "When it comes to", "It's important to note that", "Whether you are a... or a... or a...", "Look no further", "robust solutions", "streamline", "leverage", "delve into", "tapestry", "navigate the complexities of", "in the realm of". Also strip exhaustive parenthetical disclaimers and tri-colons.
- [ ] **Niche-specific facts.** Read the page as someone in that niche. The sample line items, typical terms, and tax notes must reflect real industry practice. If you are not sure (e.g. plumber call-out fees vs hourly + parts), do a 30-second sanity check before shipping.
- [ ] **Tax notes accurate for the locale.** Country pages must use the right tax label (Canada: GST/HST/PST nuance, UK: VAT 20% standard / 5% reduced / 0% zero-rated, US: state sales tax + nexus, Australia: GST 10%, India: GST with HSN/SAC codes). Do not give legal advice.
- [ ] **FAQs read like real searches.** Use natural query phrasing: "How do I", "When should I", "What is", "Do I need". Avoid "Why choose us?"-style sales questions.
- [ ] **One internal mention of "Argo Books" only.** The `cta_text` already mentions it. Do not name-drop the brand inside `intro_html` or FAQ answers; keep that copy niche-focused.
- [ ] **`sample_line_items` quantities and rates make arithmetic sense.** Realistic rate ranges, realistic quantities, no obviously round-numbered "1 x $1000".
- [ ] **`related_slugs` matches the cross-link graph in the plan exactly.**
- [ ] **No HTML tags in `faqs[*].q` or `faqs[*].a` (they are plain text rendered into `<h3>`/`<p>`).**
- [ ] **`generator_defaults` (if set) uses real state-shape keys.** See `_template.php` line 112. Common keys: `country`, `paymentTerms`, `lineItems[*].description/quantity/rate`. Country pages should set `country` to the right ISO code so the generator's tax label switches automatically.
