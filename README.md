# Argo Books Website

## Introduction

This is the website for [Argo Books](https://github.com/ArgoRobots/Argo-Books-Avalonia), accounting software with receipt scanning, predictive analytics, inventory management, and more. This website serves as a platform for users to download the software, purchase license keys, access documentation, and has an administrative system for managing licenses, user accounts, and viewing analytics.

You can view the live website here: www.argorobots.com.

## Technologies Used

### Frontend:

- **HTML5 and CSS3**: Structure and styling
- **JavaScript and jQuery**: Interactive elements and dynamic content loading
- **Chart.js**: Data visualization for analytics dashboard

### Backend:

- **PHP**: Server-side processing
- **MySQL**: Database for storing licenses, user accounts, and analytics data
- **Two-factor authentication (TOTP)**: Enhanced security for admin access

## Core Features

### Public Website

- Product information and marketing pages
- Free version download
- License key purchase system
- Comprehensive documentation
- Community page for feature requests and bug reports
- Support/contact system
- About us and legal information

### Admin System

- Secure admin dashboard with two-factor authentication
- License key generation and management
- User account administration
- Statistics tracking and analytics dashboard

## Installation Instructions

### Step 1: Install Laragon

1. Download Laragon from [https://laragon.org/download/](https://laragon.org/download/)
2. Install Laragon (default location: `C:\laragon`)
3. Open Laragon and click **Start All** to start Apache and MySQL

### Step 2: Install Composer

1. Download and install Composer from [https://getcomposer.org/](https://getcomposer.org/)
2. During installation, make sure it detects your `php.exe` from `C:\laragon\bin\php\php-8.3.26-Win32-vs16-x64`
3. Restart your computer to finish installing Composer
4. Open Command Prompt and run `composer -V` to verify Composer is installed

### Step 3: Set Up the Project

1. Place the project files directly in Laragon's `www` directory: `C:\laragon\www\argo-books-website`
   - The folder name will become part of your URL (e.g., folder `argo-books-website` → URL `localhost/argo-books-website`)
   - Avoid spaces in the folder name
2. Open Command Prompt and navigate to that directory:

```bash
cd C:\laragon\www\argo-books-website
```

3. Run the following command to install PHP dependencies:

```bash
composer install
```

This will download all required dependencies into the `vendor/` folder.

### Step 4: Set Up the Database

You need to create a MySQL database and import the schema.

**What is HeidiSQL?** HeidiSQL is a database management tool that comes with Laragon. It lets you manage MySQL databases through a visual interface (similar to phpMyAdmin).

1. **Open HeidiSQL**:
   - In Laragon, click the **Database** button
   - HeidiSQL will open and connect automatically

2. **Create the Database**:
   - Right-click in the left sidebar
   - Select **Create new → Database**
   - Name it: `argo_books`
   - Click **OK**

3. **Import the Schema**:
   - Click on the **argo_books** database in the left sidebar
   - Go to **File → Run SQL file...**
   - Navigate to your project folder and select: `mysql_schema.sql`
   - The tables will be created automatically

4. **Verify the Import**:
   - Expand the **argo_books** database in the left sidebar
   - You should see all the tables listed

## Running Locally

1. Open Laragon and click **Start All**
2. Navigate to http://localhost/argo-books-website in your browser (adjust the folder name if different)
3. The website should now be running locally
4. To view emails sent by the application, open http://localhost:8025 (requires MailHog setup, see [Local email setup](read-me/setup/Local%20email%20setup.md))

## Publishing a new version of Argo Books
1. Create a new folder in `resources/downloads` named whatever the version number is
1. Upload the new .exe and the language folder to this new directory
2. Update the version number in `avalonia-update.xml`
3. Add the new version to whats-new/index.php

## Documentation

Reference docs live in [read-me/](read-me/).

### Operations

| Document | Read it when |
|---|---|
| [Deployment](read-me/Deployment.md) | Shipping to production, or a push did not land on the server |
| [Cron jobs](read-me/Cron%20jobs.md) | Adding a scheduled task, or one has stopped running |
| [Admin guide](read-me/Admin%20guide.md) | Checking payment processor fees, switching between sandbox and production, rotating payment keys, or creating an admin account |
| [Refund block response procedure](read-me/procedures/Refund%20block%20response%20procedure.md) | The refund system has hard-blocked someone and emailed you |

### Setup

| Document | Read it when |
|---|---|
| [Local email setup](read-me/setup/Local%20email%20setup.md) | Setting up MailHog so local mail does not try to reach a real server |
| [Payment provider setup](read-me/setup/Payment%20provider%20setup.md) | Configuring Stripe, PayPal or Square, for the portal or subscriptions |
| [Cloudflare Turnstile setup](read-me/setup/Cloudflare%20Turnstile%20setup.md) | Working on the free receipt scanner's bot protection |
| [Google Ads campaign setup](read-me/setup/Google%20Ads%20campaign%20setup.md) | Building a new Google Ads campaign, step by step |

### Testing

| Document | Read it when |
|---|---|
| [Payment provider testing](read-me/testing/Payment%20provider%20testing.md) | Running sandbox payments through any provider |
| [First-run install tracking](read-me/testing/First-run%20install%20tracking.md) | Checking that installs attribute back to the originating ad click |
| [PHPUnit suite](tests/README.md) | Running or adding backend tests. Covers the financial and licensing flows |

### Marketing

| Document | Read it when |
|---|---|
| [Email outreach](read-me/Email%20outreach.md) | Running or changing the outreach pipeline |
| [Google Ads economics](read-me/Google%20Ads%20economics.md) | Deciding whether to spend, and what a click is worth. Keep the figures current |
