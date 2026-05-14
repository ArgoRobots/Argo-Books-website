# Testing PayPal Payments

Two separate flows go through PayPal:

- **SaaS subscription payments** — argorobots.com customers paying for Argo Premium.
- **Portal Connect (OAuth)** — Argo Books users authorizing the website to accept invoice payments through their own PayPal Business account.

## Setup

Set environment to sandbox in `.env`:

```
APP_ENV=sandbox
```

## SaaS subscription payment

1. Go to checkout page with PayPal selected:

```
https://dev.argorobots.com/pricing/checkout/index.php?method=paypal
```

2. Click the PayPal button

3. Log in with a PayPal sandbox **Personal (buyer)** test account:

   - Go to [PayPal Developer Dashboard - Sandbox Accounts](https://developer.paypal.com/dashboard/accounts)
   - Use the auto-created `sb-...@personal.example.com` account, or click **Create account** -> **Personal** to make a new one
   - Get the password: click the **...** menu next to the account -> **View/edit account** -> copy the system-generated password
   - **If it asks for a verification code:** Click "Use a different method" and choose "Password" instead

4. Complete the payment in the PayPal popup

5. Check results:
   - Should redirect to thank you page with license key
   - Check email for license key

### Verify in PayPal Dashboard

1. Go to [PayPal Sandbox](https://www.sandbox.paypal.com/)
2. Make sure you're in **Sandbox Mode**
3. Check the Payments section for your test payment

## Portal Connect — NOT CURRENTLY SUPPORTED

The PayPal portal Connect flow is **disabled**. PayPal's "Log in with PayPal" OAuth endpoint refuses to return identity for Business-account tokens, so the flow can't onboard real merchants. The desktop app hides the PayPal Connect button under Settings -> Payment Portal, and the server-side `api/portal/connect/paypal` endpoint returns 503 PROVIDER_UNSUPPORTED.

Re-enabling requires migrating to **PayPal Partner Referrals API** after approval into PayPal Platforms & Marketplaces — see `read-me/Admin guide.md` for the program details. There is no portal Connect test to run for PayPal today; test Stripe and Square portal Connect instead.

Only the SaaS subscription PayPal flow above is testable.

## Switch to Production

When ready to go live:

1. Change `.env`: `APP_ENV=production`
2. Test with a real card (you'll be charged)
