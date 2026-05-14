# Testing Square Payments

Two separate flows go through Square:

- **SaaS subscription payments** — argorobots.com customers paying for Argo Premium.
- **Portal Connect (OAuth)** — Argo Books users authorizing the website to accept invoice payments through their own Square account.

## Setup

Set environment to sandbox in `.env`:

```
APP_ENV=sandbox
```

## SaaS subscription payment

1. Go to checkout page with Square selected:

```
https://dev.argorobots.com/pricing/checkout/index.php?method=square
```

2. Fill in the form:

   - **Cardholder Name**: Any name
   - **Card Number**: `4111 1111 1111 1111` (Visa)
   - **Expiry**: Any future date (e.g., 12/28)
   - **CVV**: Any 3 digits (e.g., 111)
   - **Postal Code**: Any valid postal code
   - **Email**: Your email address

3. Click "Pay"

4. Check results:
   - Should redirect to thank you page with license key
   - Check email for license key

### Other Test Cards

**Success:**

- Visa: `4111 1111 1111 1111`
- Mastercard: `5105 1051 0510 5100`
- Amex: `3782 822463 10005`

**Declined:**

- `4000 0000 0000 0002` (generic decline)

**More test cards:** [Square Testing Guide](https://developer.squareup.com/docs/devtools/sandbox/payments)

### Verify in Square Dashboard

1. Go to [Square Developer Dashboard](https://developer.squareup.com/apps)
2. Make sure you're in **Sandbox** mode
3. Click on your application
4. Check the test payments

## Portal Connect (OAuth)

Testing the portal Connect flow requires a Square **sandbox seller test account** — distinct from your Square developer account. Without one, the authorize page is blank.

1. Open the [Square Developer Console](https://developer.squareup.com/console/en/apps) and go to **Sandbox test accounts** in the left sidebar.
2. Open the **Default Test Account** or create a new one (set Country to Canada to match the app's CAD currency).
3. A new tab opens at `app.squareupsandbox.com/dashboard` signed in as that test seller. Keep this tab open in the same browser.
4. In the Argo Books desktop app (dev mode), open **Settings -> Payment Portal** and click **Connect** on Square.
5. Click Allow, and Square will show as connected in the app.

## Switch to Production

When ready to go live:

1. Change `.env`: `APP_ENV=production`
2. Test with a real card (you'll be charged)
