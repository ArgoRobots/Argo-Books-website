# YouTube email assist

A small local helper for the Creators (affiliate partners) outreach channel.

YouTube hides a channel's business email behind a captcha, so it cannot be scraped
automatically. This tool opens each channel's About page in a real browser window,
you solve the captcha and reveal the email yourself, and it scrapes the revealed
address and moves on. It is deliberately semi-manual: one captcha per channel is
unavoidable, so run it for your best matches, not thousands.

This runs on your machine, not the web server.

## Install

You need Node.js 18+.

```
cd tools/youtube-email-assist
npm install
npx playwright install chromium
```

## Get the list of channels to work

In the admin Partners channel, the leads that came in without an email (mostly
YouTubers) are the ones to run through this. Export them while logged in to admin
by opening this URL in your browser and saving the response as `input.json`:

```
https://argorobots.com/admin/outreach/api.php?action=creator_export_emailless
```

The relevant part is the `leads` array. Save it as a JSON array shaped like:

```json
[
  { "lead_id": 12, "url": "https://youtube.com/@somechannel" },
  { "lead_id": 15, "url": "https://youtube.com/@another" }
]
```

(Or skip the file entirely and pass URLs directly: `node assist.mjs --urls "https://youtube.com/@a,https://youtube.com/@b"`.)

## Run it

```
node assist.mjs               # reads ./input.json, writes ./output.json
node assist.mjs leads.json out.json
```

For each channel a browser window opens on the About page. Reveal the email
(solve the captcha), then return to the terminal and:

- press **Enter** to scrape the revealed email off the page, or
- type the **email** to record it directly, or
- type **s** to skip this channel.

Progress is saved to `output.json` after every capture, so a crash never loses
what you already did.

## Apply the captured emails back to your leads

`output.json` looks like `[{ "lead_id": 12, "url": "...", "email": "hi@channel.com" }]`.
Send it to the admin `creator_set_email` action (while logged in to admin). For
example with curl and your admin session cookie:

```
curl -X POST "https://argorobots.com/admin/outreach/api.php?action=creator_set_email" \
  -H "Content-Type: application/json" \
  -b "PHPSESSID=YOUR_ADMIN_SESSION" \
  --data @output.json
```

Note: `creator_set_email` accepts either the raw array as `results`, or a single
`{ "lead_id": 12, "email": "hi@channel.com" }`. It only fills leads that are still
missing an email, so it is safe to re-run.

Once a lead has an email, generate and send its affiliate pitch from the Partners
→ Leads tab like any other lead.
