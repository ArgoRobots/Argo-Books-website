# Phase E SEO Audit Report

Run: 2026-05-28T21:23:19+00:00
Base: http://argo-books-website.test (local Laragon, branch `feature/invoice-generator-phase-a`)

## Context

This audit was run against the local branch, not production, because the branch had not yet deployed at the time of audit. A separate run against `https://argorobots.com` would currently surface 2 deploy-state findings (`sitemap-tools.xml` is 404 on production; `robots.txt` on production references only `sitemap.xml`). Both clear automatically when this branch deploys, because the local repo already has both the `sitemap-tools.xml.php` file and the updated `robots.txt` with the second Sitemap line. Re-run the audit against `https://argorobots.com` after deploy to confirm.

## Summary

All checks passed.

## URL audit

| URL | Status | Canonical | Robots | JSON-LD types |
|---|---|---|---|---|
| `https://argorobots.com/invoice-generator/` | 200 | `https://argorobots.com/invoice-generator/` |  | Organization, WebSite, SoftwareApplication, Offer |
| `https://argorobots.com/free-invoice-generator/australia/` | 200 | `https://argorobots.com/free-invoice-generator/australia/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/canada/` | 200 | `https://argorobots.com/free-invoice-generator/canada/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/cleaning/` | 200 | `https://argorobots.com/free-invoice-generator/cleaning/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/consultant/` | 200 | `https://argorobots.com/free-invoice-generator/consultant/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/contractor/` | 200 | `https://argorobots.com/free-invoice-generator/contractor/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/designer/` | 200 | `https://argorobots.com/free-invoice-generator/designer/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/developer/` | 200 | `https://argorobots.com/free-invoice-generator/developer/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/electrician/` | 200 | `https://argorobots.com/free-invoice-generator/electrician/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/freelance/` | 200 | `https://argorobots.com/free-invoice-generator/freelance/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/` | 200 | `https://argorobots.com/free-invoice-generator/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/india/` | 200 | `https://argorobots.com/free-invoice-generator/india/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/photographer/` | 200 | `https://argorobots.com/free-invoice-generator/photographer/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/plumber/` | 200 | `https://argorobots.com/free-invoice-generator/plumber/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/tutor/` | 200 | `https://argorobots.com/free-invoice-generator/tutor/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/uk/` | 200 | `https://argorobots.com/free-invoice-generator/uk/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/free-invoice-generator/usa/` | 200 | `https://argorobots.com/free-invoice-generator/usa/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/` | 200 | `https://argorobots.com/invoice-template/` |  | Organization, WebSite, CollectionPage, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/classic-excel/` | 200 | `https://argorobots.com/invoice-template/classic-excel/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/classic-google-docs/` | 200 | `https://argorobots.com/invoice-template/classic-google-docs/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/classic-google-sheets/` | 200 | `https://argorobots.com/invoice-template/classic-google-sheets/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/classic-pdf/` | 200 | `https://argorobots.com/invoice-template/classic-pdf/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/classic-word/` | 200 | `https://argorobots.com/invoice-template/classic-word/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/elegant-excel/` | 200 | `https://argorobots.com/invoice-template/elegant-excel/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/elegant-google-docs/` | 200 | `https://argorobots.com/invoice-template/elegant-google-docs/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/elegant-google-sheets/` | 200 | `https://argorobots.com/invoice-template/elegant-google-sheets/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/elegant-pdf/` | 200 | `https://argorobots.com/invoice-template/elegant-pdf/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/elegant-word/` | 200 | `https://argorobots.com/invoice-template/elegant-word/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/excel/` | 200 | `https://argorobots.com/invoice-template/excel/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/formal-excel/` | 200 | `https://argorobots.com/invoice-template/formal-excel/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/formal-google-docs/` | 200 | `https://argorobots.com/invoice-template/formal-google-docs/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/formal-google-sheets/` | 200 | `https://argorobots.com/invoice-template/formal-google-sheets/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/formal-pdf/` | 200 | `https://argorobots.com/invoice-template/formal-pdf/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/formal-word/` | 200 | `https://argorobots.com/invoice-template/formal-word/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/google-docs/` | 200 | `https://argorobots.com/invoice-template/google-docs/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/google-sheets/` | 200 | `https://argorobots.com/invoice-template/google-sheets/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/modern-excel/` | 200 | `https://argorobots.com/invoice-template/modern-excel/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/modern-google-docs/` | 200 | `https://argorobots.com/invoice-template/modern-google-docs/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/modern-google-sheets/` | 200 | `https://argorobots.com/invoice-template/modern-google-sheets/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/modern-pdf/` | 200 | `https://argorobots.com/invoice-template/modern-pdf/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/modern-word/` | 200 | `https://argorobots.com/invoice-template/modern-word/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/pdf/` | 200 | `https://argorobots.com/invoice-template/pdf/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/ribbon-excel/` | 200 | `https://argorobots.com/invoice-template/ribbon-excel/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/ribbon-google-docs/` | 200 | `https://argorobots.com/invoice-template/ribbon-google-docs/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/ribbon-google-sheets/` | 200 | `https://argorobots.com/invoice-template/ribbon-google-sheets/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/ribbon-pdf/` | 200 | `https://argorobots.com/invoice-template/ribbon-pdf/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/ribbon-word/` | 200 | `https://argorobots.com/invoice-template/ribbon-word/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-template/word/` | 200 | `https://argorobots.com/invoice-template/word/` |  | Organization, WebSite, FAQPage, Question, Answer, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-guides/` | 200 | `https://argorobots.com/invoice-guides/` |  | Organization, WebSite, CollectionPage, ItemList, ListItem, BreadcrumbList |
| `https://argorobots.com/free-vs-paid-invoicing-tools/` | 200 | `https://argorobots.com/free-vs-paid-invoicing-tools/` |  | Organization, WebSite, Article, ImageObject, WebPage, BreadcrumbList, ListItem |
| `https://argorobots.com/how-to-follow-up-on-unpaid-invoices/` | 200 | `https://argorobots.com/how-to-follow-up-on-unpaid-invoices/` |  | Organization, WebSite, HowTo, ImageObject, WebPage, HowToStep, BreadcrumbList, ListItem |
| `https://argorobots.com/how-to-invoice-clients/` | 200 | `https://argorobots.com/how-to-invoice-clients/` |  | Organization, WebSite, HowTo, ImageObject, WebPage, HowToStep, BreadcrumbList, ListItem |
| `https://argorobots.com/invoice-numbering-best-practices/` | 200 | `https://argorobots.com/invoice-numbering-best-practices/` |  | Organization, WebSite, HowTo, ImageObject, WebPage, HowToStep, BreadcrumbList, ListItem |
| `https://argorobots.com/late-fees-when-and-how-to-charge/` | 200 | `https://argorobots.com/late-fees-when-and-how-to-charge/` |  | Organization, WebSite, Article, ImageObject, WebPage, BreadcrumbList, ListItem |
| `https://argorobots.com/net-30-vs-due-on-receipt/` | 200 | `https://argorobots.com/net-30-vs-due-on-receipt/` |  | Organization, WebSite, Article, ImageObject, WebPage, BreadcrumbList, ListItem |
| `https://argorobots.com/recurring-invoices-when-to-use-them/` | 200 | `https://argorobots.com/recurring-invoices-when-to-use-them/` |  | Organization, WebSite, Article, ImageObject, WebPage, BreadcrumbList, ListItem |
| `https://argorobots.com/tax-on-invoices-country-guide/` | 200 | `https://argorobots.com/tax-on-invoices-country-guide/` |  | Organization, WebSite, Article, ImageObject, WebPage, BreadcrumbList, ListItem |
| `https://argorobots.com/what-to-do-when-a-client-does-not-pay/` | 200 | `https://argorobots.com/what-to-do-when-a-client-does-not-pay/` |  | Organization, WebSite, Article, ImageObject, WebPage, BreadcrumbList, ListItem |
| `https://argorobots.com/what-to-include-on-an-invoice/` | 200 | `https://argorobots.com/what-to-include-on-an-invoice/` |  | Organization, WebSite, Article, ImageObject, WebPage, BreadcrumbList, ListItem |
| `https://argorobots.com/` | 200 | `https://argorobots.com/` |  | SoftwareApplication, Offer, Organization, PostalAddress |
| `https://argorobots.com/about-us/` | 200 | `https://argorobots.com/about-us/` |  | Organization, PostalAddress, Place |
| `https://argorobots.com/documentation/` | 200 | `https://argorobots.com/documentation/` |  |  |
| `https://argorobots.com/pricing/` | 200 | `https://argorobots.com/pricing/` |  | FAQPage, Question, Answer |
| `https://argorobots.com/contact-us/` | 200 | `https://argorobots.com/contact-us/` |  |  |
| `https://argorobots.com/whats-new/` | 200 | `https://argorobots.com/whats-new/` |  |  |
| `https://argorobots.com/community/` | 200 | `https://argorobots.com/community/` |  |  |
| `https://argorobots.com/older-versions/` | 200 | `https://argorobots.com/older-versions/` |  |  |
| `https://argorobots.com/features/` | 200 | `https://argorobots.com/features/` |  | BreadcrumbList, ListItem |
| `https://argorobots.com/features/receipt-scanning/` | 200 | `https://argorobots.com/features/receipt-scanning/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer, SoftwareApplication, Offer |
| `https://argorobots.com/features/expense-revenue-tracking/` | 200 | `https://argorobots.com/features/expense-revenue-tracking/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer, SoftwareApplication, Offer |
| `https://argorobots.com/features/predictive-analytics/` | 200 | `https://argorobots.com/features/predictive-analytics/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer, SoftwareApplication, Offer |
| `https://argorobots.com/features/inventory-management/` | 200 | `https://argorobots.com/features/inventory-management/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer, SoftwareApplication, Offer |
| `https://argorobots.com/features/rental-management/` | 200 | `https://argorobots.com/features/rental-management/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer, SoftwareApplication, Offer |
| `https://argorobots.com/features/customer-management/` | 200 | `https://argorobots.com/features/customer-management/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer, SoftwareApplication, Offer |
| `https://argorobots.com/features/invoicing/` | 200 | `https://argorobots.com/features/invoicing/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer, SoftwareApplication, Offer |
| `https://argorobots.com/features/spreadsheet-import/` | 200 | `https://argorobots.com/features/spreadsheet-import/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer, SoftwareApplication, Offer |
| `https://argorobots.com/compare/argo-books-vs-quickbooks/` | 200 | `https://argorobots.com/compare/argo-books-vs-quickbooks/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer |
| `https://argorobots.com/compare/argo-books-vs-freshbooks/` | 200 | `https://argorobots.com/compare/argo-books-vs-freshbooks/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer |
| `https://argorobots.com/compare/argo-books-vs-wave/` | 200 | `https://argorobots.com/compare/argo-books-vs-wave/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer |
| `https://argorobots.com/compare/argo-books-vs-odoo/` | 200 | `https://argorobots.com/compare/argo-books-vs-odoo/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer |
| `https://argorobots.com/compare/argo-books-vs-xero/` | 200 | `https://argorobots.com/compare/argo-books-vs-xero/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer |
| `https://argorobots.com/compare/argo-books-vs-zipbooks/` | 200 | `https://argorobots.com/compare/argo-books-vs-zipbooks/` |  | BreadcrumbList, ListItem, FAQPage, Question, Answer |
| `https://argorobots.com/legal/privacy.php` | 200 | `https://argorobots.com/legal/privacy.php` |  | BreadcrumbList, ListItem |
| `https://argorobots.com/legal/terms.php` | 200 | `https://argorobots.com/legal/terms.php` |  | BreadcrumbList, ListItem |
| `https://argorobots.com/legal/eula.php` | 200 | `https://argorobots.com/legal/eula.php` |  | BreadcrumbList, ListItem |
| `https://argorobots.com/legal/refund.php` | 200 | `https://argorobots.com/legal/refund.php` |  | BreadcrumbList, ListItem |

## Bad-slug 404 probes

- `/free-invoice-generator/this-slug-does-not-exist/`: HTTP 404 (OK)
- `/invoice-template/this-slug-does-not-exist/`: HTTP 404 (OK)
