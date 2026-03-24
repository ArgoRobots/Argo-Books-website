# Email Outreach System

Argo Books includes an automated email outreach system that discovers local businesses, generates personalized AI-drafted emails, and sends them on a daily schedule. The system can run fully hands-off via a cron pipeline or be operated manually through an admin dashboard.

## Overview

The outreach system has three main components:

| Component | Path | Purpose |
|-----------|------|---------|
| **Admin Dashboard** | `/admin/outreach/` | Web UI for manual lead management, discovery, and sending |
| **Cron Pipeline** | `/cron/outreach_pipeline.php` | Automated daily workflow |
| **Shared Helpers** | `/cron/lib/outreach_helpers.php` | Core business logic used by both |

## Pipeline Flow

The automated pipeline runs daily and executes five steps in sequence:

### Step 1: Business Discovery

The pipeline picks the next city from a rotating list of 45+ Canadian cities (Saskatchewan first, then expanding outward to Alberta, Manitoba, BC, and Ontario) and searches the Google Places API for businesses.

For each result the system:

1. Fetches place details (phone, website, address) from the Google Place Details API
2. Scrapes the business website for a contact email — checks `mailto:` links first, then falls back to regex patterns, and follows links to `/contact` or `/about` pages if needed
3. Filters out false-positive emails from CDNs and platforms (e.g. `wixpress.com`, `wordpress.org`)
4. Skips businesses that have no website or no discoverable email
5. Deduplicates against existing leads by Google Places ID and email address

After completing a city the pipeline advances the index so the next run searches a different city. When all cities have been visited it wraps around to the start.

### Step 2: Lead Import

Discovered businesses are inserted into the `outreach_leads` table with source `google_places_auto`. Each import is logged in the activity timeline.

### Step 3: AI Draft Generation

Leads that have an email but no draft are picked up in batches. For each lead the system:

1. Fetches the business website and extracts readable text (up to 3,000 characters)
2. Sends the text to OpenAI to generate a 3–5 sentence business summary covering services, customers, billing approach, and pain points
3. Sends the summary along with a detailed brand prompt to OpenAI to generate a personalized email subject and body

The brand prompt instructs the AI to:

- Position Argo Books as a simpler alternative to QuickBooks
- Reference specific details from the business summary
- Emphasize a local connection if the business is in Saskatchewan
- Keep the email under 100 words across 2–3 paragraphs
- Include the Argo Books URL (`https://argorobots.com/`)
- Sign off as "Evan" from Argo Books

If the AI returns invalid JSON the draft is saved with `approval_status = 'needs_review'` for manual editing.

### Step 4: Auto-Approve

When auto-approve is enabled (the default), all drafts that have a subject, body, and recipient email are approved automatically. This step can be disabled via the `OUTREACH_AUTO_APPROVE` environment variable to require manual review in the admin dashboard before any email is sent.

### Step 5: Send Emails

Approved leads are sent up to the daily limit. Each email is wrapped in the Argo Books branded HTML template and dispatched via SMTP (or PHP `mail()` as a fallback). After sending, the lead status is updated to `contacted` and timestamps are recorded.

A 2-second pause between sends avoids triggering rate limits.

## Admin Dashboard

The admin dashboard at `/admin/outreach/` provides manual control over every step of the pipeline.

### Dashboard Features

| Feature | Description |
|---------|-------------|
| **Stats Panel** | Real-time counts of leads by status (new, drafted, contacted, replied, interested, onboarded) |
| **Business Discovery** | Search Google Places by city and category, preview results, and import selected businesses |
| **Leads Table** | Filter, sort, search, and bulk-operate on leads |
| **Lead Detail Modal** | Edit lead info, review/edit AI drafts, view activity timeline |
| **Bulk Operations** | Generate drafts, send emails, or delete multiple leads at once |
| **CSV Import/Export** | Upload leads from a spreadsheet or download the current list |
| **Company Size Classification** | AI-powered bulk classification of businesses as small, medium, or large |

### API Endpoints

The dashboard frontend communicates with `/admin/outreach/api.php`, which exposes the following actions:

| Action | Description |
|--------|-------------|
| `get_leads` | Fetch leads with filters (status, response, company size, search text) |
| `get_lead` | Fetch a single lead by ID |
| `create_lead` | Create a lead manually |
| `update_lead` | Edit lead fields |
| `delete_lead` | Delete a lead and its activity log |
| `get_stats` | Dashboard statistics |
| `search_businesses` | Google Places business discovery |
| `import_leads` | Bulk import from discovery results |
| `generate_draft` | Generate an AI email draft for one lead |
| `send_email` | Send an approved email |
| `get_activity` | Activity timeline for a lead |
| `classify_company_sizes` | AI-classify selected leads by company size |
| `export_csv` / `import_csv` | CSV export and import |

All state-changing endpoints require admin session authentication and CSRF token validation.

## Lead Lifecycle

Each lead progresses through two parallel status tracks:

### Lead Status

Tracks the overall stage of engagement:

`new` → `draft_generated` → `awaiting_approval` → `approved` → `contacted` → `replied` → `interested` → `onboarded`

(A lead can also be marked `not_interested` at any point.)

### Approval Status

Tracks the email draft workflow:

`not_drafted` → `draft_ready` → `needs_review` → `approved` → `sent`

### Response Status

Tracks how the business responded after contact: `no_response`, `positive`, `neutral`, or `negative`.

## Database Schema

### outreach_leads

Stores all lead data including contact info, AI-generated drafts, and status tracking.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `business_name` | VARCHAR(255) | Business name from Google Places or manual entry |
| `contact_name` | VARCHAR(255) | Contact person (if known) |
| `email` | VARCHAR(255) | Scraped or manually entered email |
| `phone` | VARCHAR(50) | Phone number |
| `website` | VARCHAR(500) | Business website URL |
| `address` | VARCHAR(500) | Physical address |
| `category` | VARCHAR(100) | Business category (e.g. "plumber", "restaurant") |
| `city` | VARCHAR(100) | City name |
| `source` | VARCHAR(100) | How the lead was added (`manual`, `google_places_auto`, `csv_import`) |
| `status` | ENUM | Lead lifecycle status |
| `response_status` | ENUM | How the business responded |
| `approval_status` | ENUM | Draft approval workflow status |
| `company_size` | ENUM | AI or manual classification (`small`, `medium`, `large`) |
| `draft_subject` | VARCHAR(500) | AI-generated email subject |
| `draft_body` | TEXT | AI-generated email body |
| `business_summary` | TEXT | Cached AI summary of the business website |
| `places_id` | VARCHAR(255) | Google Places ID for deduplication |
| `drafted_at` | DATETIME | When the draft was generated |
| `sent_at` | DATETIME | When the email was sent |
| `first_contact_date` | DATETIME | Date of first email |
| `last_contact_date` | DATETIME | Date of most recent email |

### outreach_activity_log

Audit trail of all actions performed on each lead.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `lead_id` | INT | Foreign key to `outreach_leads` (cascading delete) |
| `action_type` | VARCHAR(50) | Action identifier (e.g. `lead_created`, `draft_generated`, `email_sent`) |
| `details` | TEXT | Human-readable description |
| `created_at` | DATETIME | Timestamp |

### outreach_pipeline_state

Key-value store used by the cron pipeline to track its position across runs.

| Key | Description |
|-----|-------------|
| `current_city_index` | Index into the target cities list |
| `last_discovery_date` | Date of the last discovery run |
| `last_discovery_city` | Name of the last city searched |

## Configuration

### Pipeline Settings

| Environment Variable | Default | Description |
|---------------------|---------|-------------|
| `OUTREACH_DAILY_SEND_LIMIT` | `10` | Maximum emails sent per day (also controls discovery and draft batch sizes) |
| `OUTREACH_AUTO_APPROVE` | `true` | Automatically approve generated drafts |

### Required API Keys

| Environment Variable | Service | Used For |
|---------------------|---------|----------|
| `GOOGLE_PLACES_API_KEY` | Google Places | Business discovery and details |
| `OPENAI_API_KEY` | OpenAI | Draft generation and business summarization |
| `OPENAI_MODEL` | OpenAI | Model selection (default: `gpt-4o-mini`) |

### SMTP

Email sending uses the shared SMTP configuration defined in `/smtp_mailer.php`:

| Environment Variable | Description |
|---------------------|-------------|
| `SMTP_HOST` | SMTP server (e.g. `smtp-relay.brevo.com`) |
| `SMTP_PORT` | Port (default: `587`) |
| `SMTP_USERNAME` | Login |
| `SMTP_PASSWORD` | Password |
| `SMTP_FROM_EMAIL` | Sender address (default: `noreply@argorobots.com`) |
| `SMTP_FROM_NAME` | Sender name (default: `Argo Books`) |

Falls back to PHP `mail()` if SMTP is not configured.

## Target Cities

The pipeline rotates through cities in this order, starting with Saskatchewan and expanding outward:

| Province | Cities |
|----------|--------|
| **Saskatchewan** | Saskatoon, Regina, Prince Albert, Moose Jaw, Swift Current, Yorkton, North Battleford, Estevan, Weyburn, Martensville, Warman, Humboldt, Melfort, Meadow Lake, Lloydminster |
| **Alberta** | Edmonton, Calgary, Red Deer, Lethbridge, Medicine Hat, Grande Prairie, Airdrie, Spruce Grove, St. Albert |
| **Manitoba** | Winnipeg, Brandon, Steinbach, Thompson, Portage la Prairie |
| **British Columbia** | Vancouver, Victoria, Kelowna, Kamloops, Nanaimo |
| **Ontario** | Toronto, Ottawa, Hamilton, London, Kitchener, Windsor, Barrie, Sudbury, Thunder Bay |

## Key Files

| File | Responsibility |
|------|----------------|
| `admin/outreach/index.php` | Admin dashboard UI |
| `admin/outreach/api.php` | Admin API endpoints |
| `cron/outreach_pipeline.php` | Automated daily pipeline |
| `cron/lib/outreach_helpers.php` | Shared logic: discovery, scraping, draft generation, sending, activity logging |
| `email_sender.php` | Branded HTML email rendering |
| `smtp_mailer.php` | SMTP transport |
