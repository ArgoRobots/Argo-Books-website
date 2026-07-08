#!/usr/bin/env node
/**
 * Human-assisted YouTube channel email harvester.
 *
 * YouTube deliberately hides a channel's business email behind a captcha, so it
 * cannot be scraped automatically. This tool does the next best thing: it opens
 * each channel's About page in a REAL (headed) Chromium window, you solve the
 * captcha and reveal the email yourself, then it scrapes the revealed address and
 * moves to the next channel. It is deliberately semi-manual, one captcha per
 * channel is unavoidable, so run it for your best matches, not thousands.
 *
 * Input:  a JSON file (default ./input.json) shaped like:
 *           [{ "lead_id": 12, "url": "https://youtube.com/@somechannel" }, ...]
 *         (creator_export_emailless in the admin API produces exactly this).
 * Output: ./output.json with { lead_id, url, email } for the ones you captured,
 *         which you can feed back via the admin creator_set_email action.
 *
 * Usage:
 *   npm install
 *   npx playwright install chromium
 *   node assist.mjs                 # reads ./input.json, writes ./output.json
 *   node assist.mjs leads.json out.json
 *   node assist.mjs --urls "https://youtube.com/@a,https://youtube.com/@b"
 *
 * This is a local, personal-use helper. It does not run on the web server.
 */

import { chromium } from 'playwright';
import { readFile, writeFile } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import readline from 'node:readline';

const EMAIL_RE = /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/gi;
// Addresses that appear on YouTube chrome/consent pages but are never the
// creator's contact email, filtered out of scrape results.
const EMAIL_BLOCKLIST = [
  'youtube.com', 'google.com', 'gmail-noreply', 'no-reply', 'noreply',
  'example.com', 'sentry', 'schema.org',
];

function parseArgs(argv) {
  const args = { input: 'input.json', output: 'output.json', urls: null };
  const rest = [];
  for (let i = 0; i < argv.length; i++) {
    if (argv[i] === '--urls') { args.urls = (argv[++i] || '').split(',').map(s => s.trim()).filter(Boolean); }
    else rest.push(argv[i]);
  }
  if (rest[0]) args.input = rest[0];
  if (rest[1]) args.output = rest[1];
  return args;
}

async function loadTargets(args) {
  if (args.urls && args.urls.length) {
    return args.urls.map(url => ({ lead_id: null, url }));
  }
  if (!existsSync(args.input)) {
    console.error(`Input file "${args.input}" not found. Create it as a JSON array of {lead_id, url}, or pass --urls "a,b".`);
    process.exit(1);
  }
  const raw = await readFile(args.input, 'utf8');
  let data;
  try { data = JSON.parse(raw); } catch { console.error(`"${args.input}" is not valid JSON.`); process.exit(1); }
  if (!Array.isArray(data)) { console.error('Input JSON must be an array.'); process.exit(1); }
  return data.filter(d => d && d.url).map(d => ({ lead_id: d.lead_id ?? null, url: String(d.url) }));
}

/** Normalize a channel URL to its About page, where the email lives. */
function toAboutUrl(url) {
  try {
    const u = new URL(url);
    // Strip trailing slash, then append /about if not already an about/video URL.
    let path = u.pathname.replace(/\/+$/, '');
    if (/\/(watch|shorts|about)$/.test(path) || u.searchParams.has('v')) {
      // A video URL: leave as-is; the user can navigate to the channel manually.
      if (!/\/about$/.test(path)) return u.origin + path + u.search;
    }
    if (!/\/about$/.test(path)) path += '/about';
    return u.origin + path;
  } catch {
    return url;
  }
}

function scrapeEmailsFromText(text) {
  const found = new Set();
  for (const m of text.matchAll(EMAIL_RE)) {
    const email = m[0].toLowerCase();
    if (EMAIL_BLOCKLIST.some(b => email.includes(b))) continue;
    found.add(email);
  }
  return [...found];
}

function ask(question) {
  const rl = readline.createInterface({ input: process.stdin, output: process.stdout });
  return new Promise(resolve => rl.question(question, ans => { rl.close(); resolve(ans.trim()); }));
}

async function main() {
  const args = parseArgs(process.argv.slice(2));
  const targets = await loadTargets(args);
  if (!targets.length) { console.error('No target URLs.'); process.exit(1); }

  console.log(`\nYouTube email assist: ${targets.length} channel(s).`);
  console.log('A browser window will open on each channel. Solve the captcha and reveal the email,');
  console.log('then come back here and press Enter. Type the email to override the scrape, or "s" to skip.\n');

  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();

  const results = [];
  for (let i = 0; i < targets.length; i++) {
    const t = targets[i];
    const aboutUrl = toAboutUrl(t.url);
    console.log(`\n[${i + 1}/${targets.length}] ${t.url}`);
    try {
      await page.goto(aboutUrl, { waitUntil: 'domcontentloaded', timeout: 45000 });
    } catch (e) {
      console.log(`  Could not load the page (${e.message}). You can still navigate manually in the window.`);
    }

    const answer = await ask('  Reveal the email in the window, then press Enter (or type the email / "s" to skip): ');
    if (answer.toLowerCase() === 's') { console.log('  Skipped.'); continue; }

    let email = '';
    if (EMAIL_RE.test(answer)) {
      EMAIL_RE.lastIndex = 0;
      email = answer.match(EMAIL_RE)[0].toLowerCase();
      console.log(`  Using typed email: ${email}`);
    } else {
      const body = await page.content();
      const emails = scrapeEmailsFromText(body);
      if (emails.length === 1) {
        email = emails[0];
        console.log(`  Scraped: ${email}`);
      } else if (emails.length > 1) {
        console.log(`  Found ${emails.length}: ${emails.join(', ')}`);
        const pick = await ask('  Type the correct one (or Enter to take the first): ');
        email = (pick && EMAIL_RE.test(pick)) ? pick.match(EMAIL_RE)[0].toLowerCase() : emails[0];
        EMAIL_RE.lastIndex = 0;
      } else {
        console.log('  No email found on the page. Skipped.');
        continue;
      }
    }
    results.push({ lead_id: t.lead_id, url: t.url, email });
    // Persist after each capture so a crash never loses progress.
    await writeFile(args.output, JSON.stringify(results, null, 2), 'utf8');
  }

  await browser.close();
  console.log(`\nDone. Captured ${results.length} email(s) -> ${args.output}`);
  console.log('Apply them by feeding this file to the admin creator_set_email action (see README).');
}

main().catch(err => { console.error(err); process.exit(1); });
