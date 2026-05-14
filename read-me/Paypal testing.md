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

## Portal Connect (OAuth)

Testing the portal Connect flow requires a PayPal sandbox **Business** test account — distinct from the Personal (buyer) account used for SaaS testing, and distinct from your real PayPal account (real credentials don't work on sandbox). The Business account represents the Argo Books user who's authorizing the website to receive payments into their PayPal.

1. Go to [PayPal Developer Dashboard - Sandbox Accounts](https://developer.paypal.com/dashboard/accounts).
2. Use the auto-created `sb-...@business.example.com` account or click **Create account** -> **Business** to make a new one.
3. Get the password for that account: click the **...** menu next to the account -> **View/edit account** -> copy the **System Generated Password**. Keep this tab open — you'll paste the password in the next step.
4. In the Argo Books desktop app (dev mode), open **Settings -> Payment Portal** and click **Connect** on PayPal.
5. A new tab opens at `sandbox.paypal.com`. Sign in with the Business sandbox email (step 2) and password (step 3).
   - **If you see "Invalid email or password":** double-check you're using the sandbox account from step 2, not your live PayPal account. Sandbox passwords are NOT the same as your real account password.
   - **If it asks for a verification code:** Click "Use a different method" and choose "Password" instead.
6. Approve the access request. PayPal redirects back to the callback and PayPal will show as connected in the app.

## Switch to Production

When ready to go live:

1. Change `.env`: `APP_ENV=production`
2. Test with a real card (you'll be charged)
