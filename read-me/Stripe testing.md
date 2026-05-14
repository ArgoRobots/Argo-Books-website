# Testing Stripe Payments

Two separate flows go through Stripe:

- **SaaS subscription payments** — argorobots.com customers paying for Argo Premium.
- **Portal Connect (Express onboarding)** — Argo Books users authorizing the website to accept invoice payments through their own Stripe account.

## Setup

Set environment to sandbox in `.env`:

```
APP_ENV=sandbox
```

## SaaS subscription payment

1. Go to checkout page with Stripe selected:

```
https://dev.argorobots.com/pricing/checkout/index.php?method=stripe
```

2. Fill in the form:

   - **Cardholder Name**: Any name
   - **Card Number**: `4242 4242 4242 4242`
   - **Expiry**: Any future date (e.g., 12/28)
   - **CVC**: Any 3 digits (e.g., 123)
   - **Email**: Your email address

3. Click "Pay"

4. Check results:
   - Should redirect to thank you page with license key
   - Check email for license key

### Other Test Cards

- **Success**: `4242 4242 4242 4242`
- **Requires 3D Secure**: `4000 0025 0000 3155`
- **Declined**: `4000 0000 0000 9995`

### Verify in Stripe Dashboard

1. Go to [Stripe Dashboard](https://dashboard.stripe.com/test/payments)
2. Make sure you're in **Test Mode** (toggle in top right)
3. Check the Payments section for your test payment

## Portal Connect (Express onboarding)

Unlike Square and PayPal (OAuth flows), Stripe portal Connect uses **Stripe Connect Express** — Stripe creates a fresh connected account for the merchant and they fill in identity / business / bank details on Stripe's hosted onboarding pages. In test mode, Stripe ships **magic-success values** that pass verification without making real bank calls.

1. In the Argo Books desktop app (dev mode), open **Settings -> Payment Portal** and click **Connect** on Stripe.
2. A new tab opens at `connect.stripe.com/express/onboarding/...`. Use these magic test values (full reference: [Stripe Connect testing docs](https://docs.stripe.com/connect/testing)):

   | Field | Test value |
   |---|---|
   | Phone | Any valid format, e.g. `000 000 0000` |
   | Email | Any address (will receive Express dashboard emails) |
   | Date of birth | `01 / 01 / 1901` — magic DOB that auto-passes ID verification |
   | Address | Any valid address in the country you chose |
   | Tax ID / SSN (US) | `000000000` |
   | SIN (Canada) | `000 000 000` |
   | Bank account (US) | Routing `110000000`, account `000123456789` |
   | Bank account (Canada) | Transit `11000`, institution `000`, account `000123456789` |

3. Submit the form. Stripe redirects back to the portal callback and Stripe will show as connected in the app.

### Verify in Stripe Dashboard

1. Go to [Stripe Dashboard - Connected Accounts](https://dashboard.stripe.com/test/connect/accounts/overview)
2. Make sure you're in **Test Mode**
3. The newly-onboarded Express account appears in the list (its account ID, starting with `acct_`, is also stored in `portal_companies.stripe_account_id`).

## Switch to Production

When ready to go live:

1. Change `.env`: `APP_ENV=production`
2. Test with a real card (you'll be charged)
