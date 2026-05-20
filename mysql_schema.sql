-- License keys table
CREATE TABLE IF NOT EXISTS license_keys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    license_key VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    user_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    activated TINYINT(1) DEFAULT 0,
    activation_date DATETIME DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    transaction_id VARCHAR(100),
    order_id VARCHAR(100),
    payment_method VARCHAR(50),
    payment_intent VARCHAR(100),
    review_email_sent_at DATETIME DEFAULT NULL,
    review_email_variant VARCHAR(20) DEFAULT NULL COMMENT 'active, inactive, or manually_skipped',
    review_email_token CHAR(48) DEFAULT NULL,
    INDEX idx_license_keys_user_id (user_id),
    UNIQUE KEY uk_review_email_token (review_email_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    two_factor_secret VARCHAR(100),
    two_factor_enabled TINYINT(1) DEFAULT 0,
    last_2fa_counter BIGINT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- For existing installs, add the TOTP replay-prevention column:
--   ALTER TABLE admin_users
--     ADD COLUMN last_2fa_counter BIGINT NOT NULL DEFAULT 0 AFTER two_factor_enabled;

-- Trusted admin devices (skip TOTP step on opted-in devices for 30 days).
-- Split-token pattern: cookie holds "selector.validator"; DB stores selector
-- in plaintext (for O(1) lookup) and a SHA-256 hash of the validator so a DB
-- read cannot impersonate the user. The trust cookie ONLY bypasses TOTP --
-- the password step is still required on every login.
CREATE TABLE IF NOT EXISTS admin_trusted_devices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    selector CHAR(16) NOT NULL UNIQUE,
    validator_hash CHAR(64) NOT NULL,
    label VARCHAR(120),
    user_agent VARCHAR(255),
    ip_address VARCHAR(45),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_used_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    CONSTRAINT fk_atd_user FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    INDEX idx_atd_user_id (user_id),
    INDEX idx_atd_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create users table
CREATE TABLE IF NOT EXISTS community_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    bio TEXT,
    avatar VARCHAR(255),
    role VARCHAR(20) DEFAULT 'user',
    email_verified BOOLEAN DEFAULT 0,
    verification_code VARCHAR(10),
    reset_token VARCHAR(100),
    reset_token_expiry DATETIME,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deletion_scheduled_at DATETIME DEFAULT NULL,
    email_pref_product_updates BOOLEAN NOT NULL DEFAULT 0,
    email_pref_tips_onboarding BOOLEAN NOT NULL DEFAULT 0,
    email_pref_reviews BOOLEAN NOT NULL DEFAULT 0,
    email_pref_promotions BOOLEAN NOT NULL DEFAULT 0,
    email_pref_community_digest BOOLEAN NOT NULL DEFAULT 0,
    email_pref_unsubscribe_token CHAR(48) DEFAULT NULL,
    UNIQUE KEY uk_email_pref_unsubscribe_token (email_pref_unsubscribe_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create posts table
CREATE TABLE IF NOT EXISTS community_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    user_name VARCHAR(50) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    post_type VARCHAR(10) NOT NULL,
    status VARCHAR(20) DEFAULT 'open',
    votes INT DEFAULT 0,
    views INT DEFAULT 0,
    metadata TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES community_users(id) ON DELETE SET NULL,
    CHECK (post_type IN ('bug', 'feature')),
    CHECK (status IN ('open', 'in_progress', 'completed', 'declined'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create comments table
CREATE TABLE IF NOT EXISTS community_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT,
    user_name VARCHAR(50) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    votes INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES community_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create votes table
CREATE TABLE IF NOT EXISTS community_votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT,
    user_email VARCHAR(100) NOT NULL,
    vote_type TINYINT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES community_users(id) ON DELETE SET NULL,
    UNIQUE KEY (post_id, user_email),
    CHECK (vote_type IN (-1, 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create comment votes table
CREATE TABLE IF NOT EXISTS comment_votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    comment_id INT NOT NULL,
    user_id INT,
    user_email VARCHAR(100) NOT NULL,
    vote_type TINYINT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id) REFERENCES community_comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES community_users(id) ON DELETE SET NULL,
    UNIQUE KEY (comment_id, user_email),
    CHECK (vote_type IN (-1, 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create post edit history
CREATE TABLE IF NOT EXISTS post_edit_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT,
    title VARCHAR(255),
    content TEXT,
    metadata TEXT,
    edited_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES community_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create rate limits table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action_type VARCHAR(20) NOT NULL,
    count INT DEFAULT 1,
    period_start DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_action_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES community_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create remember tokens table
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES community_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create admin notification settings
CREATE TABLE IF NOT EXISTS admin_notification_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    notify_new_posts BOOLEAN DEFAULT 1,
    notify_new_comments BOOLEAN DEFAULT 1,
    notify_new_reports BOOLEAN DEFAULT 1,
    notification_email VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES community_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create a view for user profiles
CREATE OR REPLACE VIEW community_user_profiles AS
SELECT 
    u.id,
    u.username,
    u.email,
    u.bio,
    u.avatar,
    u.role,
    u.created_at,
    COUNT(DISTINCT p.id) AS post_count,
    COUNT(DISTINCT c.id) AS comment_count
FROM
    community_users u
LEFT JOIN
    community_posts p ON u.id = p.user_id
LEFT JOIN
    community_comments c ON u.id = c.user_id
GROUP BY
    u.id, u.username, u.email, u.bio, u.avatar, u.role, u.created_at;

-- Create statistics table for more detailed tracking
CREATE TABLE IF NOT EXISTS statistics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_type VARCHAR(50) NOT NULL, -- 'download_avalonia', 'page_view', etc.
    event_data VARCHAR(255), -- Additional data like version, page, etc.
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    country_code VARCHAR(2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at),
    INDEX idx_country_code (country_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create referral_links table for tracking ad/sponsor sources
CREATE TABLE IF NOT EXISTS referral_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    source_code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    target_url VARCHAR(500) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_source_code (source_code),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create referral_visits table to track visits from referral sources
CREATE TABLE IF NOT EXISTS referral_visits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    source_code VARCHAR(50) NOT NULL,
    page_url VARCHAR(500),
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    country_code VARCHAR(2),
    visited_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    converted TINYINT(1) DEFAULT 0,
    license_key VARCHAR(255),
    INDEX idx_source_code (source_code),
    INDEX idx_visited_at (visited_at),
    INDEX idx_converted (converted),
    INDEX idx_country_code (country_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Content reports table
CREATE TABLE IF NOT EXISTS content_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reporter_user_id INT,
    reporter_email VARCHAR(100) NOT NULL,
    content_type ENUM('post', 'comment', 'user') NOT NULL,
    content_id INT NOT NULL,
    violation_type VARCHAR(50) NOT NULL,
    additional_info TEXT,
    status ENUM('pending', 'resolved', 'dismissed') DEFAULT 'pending',
    resolved_by INT,
    resolved_at DATETIME,
    resolution_action VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_user_id) REFERENCES community_users(id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES community_users(id) ON DELETE SET NULL,
    INDEX idx_content_type_id (content_type, content_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User bans table
CREATE TABLE IF NOT EXISTS user_bans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    banned_by INT,
    ban_reason TEXT NOT NULL,
    ban_duration VARCHAR(20) NOT NULL,
    banned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME,
    is_active BOOLEAN DEFAULT 1,
    unbanned_at DATETIME,
    unbanned_by INT,
    FOREIGN KEY (user_id) REFERENCES community_users(id) ON DELETE CASCADE,
    FOREIGN KEY (banned_by) REFERENCES community_users(id) ON DELETE SET NULL,
    FOREIGN KEY (unbanned_by) REFERENCES community_users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_is_active (is_active),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Premium Subscriptions table
CREATE TABLE IF NOT EXISTS premium_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subscription_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    billing_cycle ENUM('monthly', 'yearly') NOT NULL DEFAULT 'monthly',
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'CAD',
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('active', 'cancelled', 'expired', 'past_due', 'payment_failed') NOT NULL DEFAULT 'active',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_token VARCHAR(255) COMMENT 'Stored payment method token for recurring billing',
    stripe_customer_id VARCHAR(255) COMMENT 'Stripe customer ID for recurring billing',
    auto_renew TINYINT(1) DEFAULT 1 COMMENT 'Whether to auto-renew the subscription',
    paypal_subscription_id VARCHAR(100) COMMENT 'PayPal subscription ID for recurring billing',
    previous_paypal_subscription_id VARCHAR(100) DEFAULT NULL COMMENT 'Old PayPal sub-id during in-flight cycle switch; cancel webhook for this ID is ignored',
    discount_applied TINYINT(1) DEFAULT 0,
    credit_balance DECIMAL(10,2) DEFAULT 0 COMMENT 'Remaining credit balance from premium discount',
    original_credit DECIMAL(10,2) DEFAULT 0 COMMENT 'Original credit amount (to track if credit was used)',
    cancelled_at DATETIME DEFAULT NULL,
    last_cycle_change_at DATETIME DEFAULT NULL COMMENT 'Timestamp of most recent billing-cycle switch (used for cooldown + audit)',
    environment ENUM('production', 'sandbox') NOT NULL DEFAULT 'production' COMMENT 'Which environment created this row (APP_ENV at insert time). Admin dashboards filter by current env so dev test data does not pollute prod stats.',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_subscription_id (subscription_id),
    INDEX idx_user_id (user_id),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_end_date (end_date),
    INDEX idx_renewal (status, end_date, auto_renew)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Premium Subscription Payments table
CREATE TABLE IF NOT EXISTS premium_subscription_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subscription_id VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'CAD',
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    payment_type ENUM('initial', 'renewal', 'manual', 'credit', 'retry', 'cycle_change') DEFAULT 'initial' COMMENT 'Type of payment (credit = covered by credit balance, cycle_change = mid-cycle billing-cycle switch)',
    error_message TEXT NULL COMMENT 'Error message if payment failed',
    environment ENUM('production', 'sandbox') NOT NULL DEFAULT 'production' COMMENT 'Which environment created this row (APP_ENV at insert time). Admin dashboards filter by current env so dev test data does not pollute prod stats.',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_subscription_id (subscription_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_payment_type (payment_type),
    INDEX idx_env_status_created (environment, status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Premium Subscription Keys table (free/promo keys)
CREATE TABLE IF NOT EXISTS premium_subscription_keys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subscription_key VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) DEFAULT NULL COMMENT 'Optional: restrict to specific email',
    duration_months INT NOT NULL DEFAULT 1 COMMENT 'Duration in months (0 = permanent)',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    redeemed_at DATETIME DEFAULT NULL,
    device_id VARCHAR(255) DEFAULT NULL COMMENT 'Hashed machine identifier of redeeming device',
    subscription_id VARCHAR(50) DEFAULT NULL COMMENT 'Link to created subscription',
    notes TEXT DEFAULT NULL COMMENT 'Admin notes about this key',
    INDEX idx_subscription_key (subscription_key),
    INDEX idx_email (email),
    INDEX idx_redeemed (redeemed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for license_keys table
CREATE INDEX idx_license_keys_transaction_id ON license_keys(transaction_id);
CREATE INDEX idx_license_keys_email ON license_keys(email);
CREATE INDEX idx_license_keys_payment_intent ON license_keys(payment_intent);

-- Add indexes for community tables
CREATE INDEX idx_users_username ON community_users(username);
CREATE INDEX idx_users_email ON community_users(email);
CREATE INDEX idx_posts_user_id ON community_posts(user_id);
CREATE INDEX idx_posts_user_email ON community_posts(user_email);
CREATE INDEX idx_posts_post_type ON community_posts(post_type);
CREATE INDEX idx_posts_status ON community_posts(status);
CREATE INDEX idx_posts_created_at ON community_posts(created_at);
CREATE INDEX idx_comments_post_id ON community_comments(post_id);
CREATE INDEX idx_comments_user_id ON community_comments(user_id);
CREATE INDEX idx_comments_user_email ON community_comments(user_email);
CREATE INDEX idx_comments_created_at ON community_comments(created_at);
CREATE INDEX idx_votes_post_id ON community_votes(post_id);
CREATE INDEX idx_votes_user_id ON community_votes(user_id);
CREATE INDEX idx_votes_user_email ON community_votes(user_email);
CREATE INDEX idx_comment_votes_comment_id ON comment_votes(comment_id);
CREATE INDEX idx_comment_votes_user_id ON comment_votes(user_id);
CREATE INDEX idx_comment_votes_user_email ON comment_votes(user_email);
CREATE INDEX idx_post_edit_history_post_id ON post_edit_history(post_id);
CREATE INDEX idx_rate_limits_user_action ON rate_limits(user_id, action_type);
CREATE INDEX idx_remember_tokens_token ON remember_tokens(token);
CREATE INDEX idx_remember_tokens_user_id ON remember_tokens(user_id);
CREATE INDEX idx_notification_settings_user_id ON admin_notification_settings(user_id);

-- Receipt scan usage tracking table (for rate limiting Premium tier: 500 scans/month)
CREATE TABLE IF NOT EXISTS receipt_scan_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    license_key VARCHAR(255) NOT NULL,
    usage_month DATE NOT NULL COMMENT 'First day of the month (e.g., 2025-01-01)',
    scan_count INT NOT NULL DEFAULT 0,
    monthly_limit INT NOT NULL DEFAULT 500,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_license_month (license_key, usage_month),
    INDEX idx_license_key (license_key),
    INDEX idx_usage_month (usage_month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Customer Payment Portal Tables
-- ============================================

-- Companies (Argo Books businesses) registered for the payment portal
CREATE TABLE IF NOT EXISTS portal_companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    api_key_hash VARCHAR(64) DEFAULT NULL COMMENT 'SHA-256 hash of the API key for secure lookup',
    company_name VARCHAR(255) NOT NULL,
    company_logo_url VARCHAR(500) DEFAULT NULL,
    -- Connected payment provider accounts (money goes to these, not to ArgoRobots)
    stripe_account_id VARCHAR(255) DEFAULT NULL COMMENT 'Stripe Connect account ID',
    stripe_email VARCHAR(255) DEFAULT NULL COMMENT 'Email on the connected Stripe account',
    paypal_merchant_id VARCHAR(255) DEFAULT NULL COMMENT 'PayPal merchant ID for marketplace',
    paypal_email VARCHAR(255) DEFAULT NULL COMMENT 'Email on the connected PayPal account',
    square_merchant_id VARCHAR(255) DEFAULT NULL COMMENT 'Square merchant ID',
    square_access_token VARCHAR(500) DEFAULT NULL COMMENT 'Square OAuth access token (encrypted at rest)',
    square_location_id VARCHAR(255) DEFAULT NULL COMMENT 'Square location ID',
    square_email VARCHAR(255) DEFAULT NULL COMMENT 'Email on the connected Square account',
    -- Metadata
    owner_email VARCHAR(100) DEFAULT NULL,
    email_verified_at DATETIME DEFAULT NULL COMMENT 'Set when the owner verifies the registration code; required for refunds/email-change',
    environment VARCHAR(10) DEFAULT 'sandbox' COMMENT 'sandbox or production',
    is_active TINYINT(1) DEFAULT 1,
    locked TINYINT(1) DEFAULT 0 COMMENT 'Auto-locked by the refund velocity engine on hard-block; manual review required to unlock',
    lock_reason VARCHAR(255) DEFAULT NULL,
    locked_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_api_key_hash (api_key_hash),
    INDEX idx_is_active (is_active),
    INDEX idx_environment (environment),
    INDEX idx_locked (locked)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Google OAuth tokens (free feature, keyed by device ID)
CREATE TABLE IF NOT EXISTS google_oauth_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    device_id_hash VARCHAR(64) NOT NULL UNIQUE COMMENT 'SHA-256 hash of the device ID',
    google_refresh_token TEXT DEFAULT NULL,
    google_access_token TEXT DEFAULT NULL,
    google_token_expires DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Google OAuth state tokens for CSRF protection during Google auth flow
CREATE TABLE IF NOT EXISTS google_oauth_states (
    id INT PRIMARY KEY AUTO_INCREMENT,
    state_token VARCHAR(255) NOT NULL UNIQUE,
    device_id_hash VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_device_id_hash (device_id_hash),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- OAuth state tokens for CSRF protection during provider connect flows
CREATE TABLE IF NOT EXISTS portal_oauth_states (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    provider VARCHAR(20) NOT NULL COMMENT 'stripe, paypal, or square',
    state_token VARCHAR(64) NOT NULL UNIQUE COMMENT 'CSRF state parameter',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL COMMENT 'State tokens expire after 10 minutes',
    INDEX idx_state_token (state_token),
    INDEX idx_expires_at (expires_at),
    FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Invoices published to the portal by Argo Books businesses
CREATE TABLE IF NOT EXISTS portal_invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    invoice_id VARCHAR(100) NOT NULL COMMENT 'Invoice number/ID from Argo Books (e.g., INV-0001)',
    invoice_token VARCHAR(48) NOT NULL UNIQUE COMMENT '48-char hex token for direct invoice access',
    customer_token VARCHAR(48) NOT NULL COMMENT '48-char hex token for customer portal access',
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) DEFAULT NULL,
    invoice_data JSON COMMENT 'Full invoice data (line items, addresses, tax, notes)',
    status ENUM('draft', 'sent', 'viewed', 'pending', 'partial', 'paid', 'overdue', 'cancelled') NOT NULL DEFAULT 'sent',
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    balance_due DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(3) NOT NULL DEFAULT 'USD',
    due_date DATE DEFAULT NULL,
    pass_processing_fee TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Whether to add processing fee to online payments',
    environment VARCHAR(10) DEFAULT 'sandbox' COMMENT 'sandbox or production',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_company_invoice (company_id, invoice_id),
    INDEX idx_invoice_token (invoice_token),
    INDEX idx_customer_token (customer_token),
    INDEX idx_company_id (company_id),
    INDEX idx_status (status),
    INDEX idx_customer_email (customer_email),
    INDEX idx_due_date (due_date),
    INDEX idx_environment (environment),
    FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments received through the portal
CREATE TABLE IF NOT EXISTS portal_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    invoice_id VARCHAR(100) NOT NULL COMMENT 'Invoice number/ID that was paid',
    customer_name VARCHAR(255) DEFAULT NULL,
    amount DECIMAL(12,2) NOT NULL,
    processing_fee DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(3) NOT NULL DEFAULT 'USD',
    payment_method VARCHAR(50) NOT NULL COMMENT 'stripe, paypal, or square',
    provider_payment_id VARCHAR(255) DEFAULT NULL COMMENT 'Payment ID from the payment provider',
    provider_transaction_id VARCHAR(255) DEFAULT NULL COMMENT 'Transaction/charge ID from the provider',
    reference_number VARCHAR(50) NOT NULL COMMENT 'Human-readable reference (PAY-YYYYMMDD-XXXXXX)',
    status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    synced_to_argo TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Whether Argo Books has pulled this payment',
    payment_environment VARCHAR(10) DEFAULT NULL COMMENT 'sandbox or production',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_company_id (company_id),
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_reference_number (reference_number),
    UNIQUE INDEX idx_provider_payment_id (provider_payment_id),
    INDEX idx_status (status),
    INDEX idx_synced (synced_to_argo),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Refund Flow Tables
-- ============================================

-- A refund a merchant has initiated through Argo Books. State machine:
--   pending_code → code_verified → (cooling_off →)? processing → completed | failed
--   any state → cancelled (by owner or admin)
-- Webhooks finalize completed/failed transitions when the provider call is
-- async; the synchronous path can also finalize completed/failed for
-- providers that return a terminal status inline.
CREATE TABLE IF NOT EXISTS refund_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    invoice_id VARCHAR(100) NOT NULL,
    invoice_number VARCHAR(100) NOT NULL,
    customer_name VARCHAR(255) DEFAULT NULL,
    provider VARCHAR(20) NOT NULL COMMENT 'stripe, paypal, or square',
    provider_payment_id VARCHAR(255) NOT NULL COMMENT 'Original payment ID being refunded',
    provider_refund_id VARCHAR(255) DEFAULT NULL COMMENT 'Provider-returned refund ID; set on processing/completed',
    amount_cents INT NOT NULL,
    currency VARCHAR(3) NOT NULL,
    line_items_json JSON DEFAULT NULL COMMENT 'Snapshot of which invoice items the merchant chose to refund',
    reason VARCHAR(500) DEFAULT NULL,
    state ENUM('pending_code','code_verified','cooling_off','processing','completed','failed','cancelled') NOT NULL DEFAULT 'pending_code',
    state_reason VARCHAR(100) DEFAULT NULL COMMENT 'Free-text reason for the current state (e.g. hard_block, too_many_code_attempts)',
    velocity_tier ENUM('normal','soft_warn','delayed','hard_block') DEFAULT NULL,
    cooling_off_until DATETIME DEFAULT NULL,
    cancel_token CHAR(64) DEFAULT NULL COMMENT 'Token for the public cancel link in the cooling-off email',
    completed_at DATETIME DEFAULT NULL,
    requested_ip VARCHAR(45) DEFAULT NULL,
    requested_user_agent VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE,
    INDEX idx_refund_company (company_id),
    INDEX idx_refund_state (state),
    INDEX idx_refund_company_state (company_id, state),
    INDEX idx_refund_created (created_at),
    INDEX idx_refund_cooling_off (state, cooling_off_until),
    INDEX idx_refund_cancel_token (cancel_token),
    INDEX idx_refund_provider_refund_id (provider_refund_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6-digit verification codes emailed to the company owner before a refund
-- can advance past pending_code. Codes are hashed (HMAC keyed by request id)
-- and capped at 5 attempts + 10-minute expiry.
CREATE TABLE IF NOT EXISTS refund_email_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    refund_request_id INT NOT NULL,
    code_hash CHAR(64) NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    expires_at DATETIME NOT NULL,
    consumed_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (refund_request_id) REFERENCES refund_requests(id) ON DELETE CASCADE,
    INDEX idx_refund_code_request (refund_request_id),
    INDEX idx_refund_code_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cached response bodies for Idempotency-Key replay on refund mutation
-- endpoints. Lookups are scoped per (company_id, idempotency_key); a match
-- with the same body_hash returns the cached response, a match with a
-- different body_hash returns 409. Rows expire 24h after creation
-- (enforced in the query, swept by the daily cron).
CREATE TABLE IF NOT EXISTS refund_idempotency_cache (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    idempotency_key VARCHAR(128) NOT NULL,
    body_hash CHAR(64) NOT NULL,
    response_status SMALLINT UNSIGNED NOT NULL,
    response_body LONGTEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE,
    UNIQUE KEY uk_idempotency_company_key (company_id, idempotency_key),
    INDEX idx_idempotency_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-company (or global default with company_id IS NULL) thresholds for
-- the refund velocity engine. One row with company_id IS NULL provides
-- the global default; per-company rows override.
CREATE TABLE IF NOT EXISTS refund_velocity_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT DEFAULT NULL COMMENT 'NULL = global default',
    soft_warn_multiplier DECIMAL(6,2) NOT NULL DEFAULT 3.00 COMMENT 'Daily total >= multiplier * baseline daily avg → soft_warn',
    cooling_multiplier DECIMAL(6,2) NOT NULL DEFAULT 10.00 COMMENT 'Daily total >= multiplier * baseline daily avg → delayed',
    cooling_revenue_pct DECIMAL(6,4) NOT NULL DEFAULT 0.2500 COMMENT 'Daily total >= pct * 30d revenue → delayed',
    hard_revenue_pct DECIMAL(6,4) NOT NULL DEFAULT 0.5000 COMMENT 'Daily total >= pct * 30d revenue → hard_block',
    cooling_off_minutes INT NOT NULL DEFAULT 15 COMMENT 'Minutes the refund waits in cooling_off before promotion',
    new_account_floor_cents INT NOT NULL DEFAULT 500000 COMMENT 'First-week hard-block floor ($5000 default; must be > cooling)',
    new_account_soft_cents INT NOT NULL DEFAULT 50000 COMMENT 'First-week soft-warn floor ($500 default)',
    new_account_cooling_cents INT NOT NULL DEFAULT 100000 COMMENT 'First-week delayed floor ($1000 default; must be < hard)',
    young_account_floor_cents INT NOT NULL DEFAULT 1000000 COMMENT 'Days 7-30 hard-block floor ($10000 default; must be > cooling)',
    young_account_soft_cents INT NOT NULL DEFAULT 100000 COMMENT 'Days 7-30 soft-warn floor ($1000 default)',
    young_account_cooling_cents INT NOT NULL DEFAULT 300000 COMMENT 'Days 7-30 delayed floor ($3000 default; must be < hard)',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE,
    UNIQUE KEY uk_velocity_company (company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Materialized baseline stats per company that the velocity engine compares
-- against on established accounts (>30 days old). Refreshed nightly by cron.
CREATE TABLE IF NOT EXISTS refund_velocity_baselines (
    company_id INT PRIMARY KEY,
    daily_avg_refund_cents BIGINT NOT NULL DEFAULT 0 COMMENT 'Trailing-90d daily average refund amount in cents',
    revenue_30d_cents BIGINT NOT NULL DEFAULT 0 COMMENT 'Trailing-30d gross payment revenue in cents',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Append-only audit log for every state-mutating refund + email-change event.
-- Used by the admin payments page (recent activity), the refund cron
-- (stale-processing detection), and the customer-portal owner page (history).
CREATE TABLE IF NOT EXISTS refund_audit_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    refund_request_id INT DEFAULT NULL,
    email_change_request_id INT DEFAULT NULL,
    event_type VARCHAR(60) NOT NULL,
    payload_json JSON DEFAULT NULL,
    actor_type VARCHAR(20) NOT NULL COMMENT 'owner | admin | system | webhook',
    actor_id VARCHAR(64) DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE,
    INDEX idx_audit_company (company_id),
    INDEX idx_audit_refund_request (refund_request_id),
    INDEX idx_audit_email_change (email_change_request_id),
    INDEX idx_audit_event_type (event_type),
    INDEX idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email-change requests for the portal owner. 4-step flow:
--   pending → (verify old email code) → old_verified
--          → (verify new email code) → completed (+ 30-day revert window)
--          → reverted (anytime within revert_until window)
-- Verification codes are stored on this row directly (one per leg) with
-- per-leg expiry + attempt counters that mirror the refund code flow.
CREATE TABLE IF NOT EXISTS email_change_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    old_email VARCHAR(255) NOT NULL,
    new_email VARCHAR(255) NOT NULL,
    password_verified TINYINT(1) NOT NULL DEFAULT 0,
    state ENUM('pending','old_verified','completed','cancelled','reverted') NOT NULL DEFAULT 'pending',
    old_email_code_hash CHAR(64) DEFAULT NULL,
    old_email_code_expires_at DATETIME DEFAULT NULL,
    old_email_code_attempts INT NOT NULL DEFAULT 0,
    old_email_verified_at DATETIME DEFAULT NULL,
    new_email_code_hash CHAR(64) DEFAULT NULL,
    new_email_code_expires_at DATETIME DEFAULT NULL,
    new_email_code_attempts INT NOT NULL DEFAULT 0,
    new_email_verified_at DATETIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    cancel_token CHAR(64) DEFAULT NULL COMMENT 'Revert token for the 30-day window after completion',
    revert_until DATETIME DEFAULT NULL,
    reverted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE,
    INDEX idx_echange_company (company_id),
    INDEX idx_echange_state (state),
    INDEX idx_echange_cancel_token (cancel_token),
    INDEX idx_echange_completed (completed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed the global default for the velocity engine on a fresh install.
INSERT IGNORE INTO refund_velocity_config
    (company_id, soft_warn_multiplier, cooling_multiplier, cooling_revenue_pct, hard_revenue_pct,
     cooling_off_minutes, new_account_floor_cents, new_account_soft_cents, new_account_cooling_cents,
     young_account_floor_cents, young_account_soft_cents, young_account_cooling_cents)
VALUES
    (NULL, 3.00, 10.00, 0.2500, 0.5000, 15, 500000, 50000, 100000, 1000000, 100000, 300000);

-- Verification codes used during portal registration and "set initial email" flows.
-- One-row-per-issued-code; the most-recent unconsumed row for a given
-- (company_id, purpose) is the active code. Capped at 5 attempts + 10-minute
-- expiry, with a rolling rate-limit enforced in PHP.
CREATE TABLE IF NOT EXISTS email_verifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    purpose VARCHAR(40) NOT NULL DEFAULT 'registration',
    code_hash CHAR(64) NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    expires_at DATETIME NOT NULL,
    consumed_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE,
    INDEX idx_emailver_company_purpose (company_id, purpose),
    INDEX idx_emailver_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Invoice send usage tracking for free-tier limits
CREATE TABLE IF NOT EXISTS invoice_send_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_key VARCHAR(255) NOT NULL COMMENT 'License key or device_<hash> for free users',
    usage_month DATE NOT NULL,
    send_count INT NOT NULL DEFAULT 0,
    monthly_limit INT NOT NULL DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_license_month (license_key, usage_month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exchange rates cache (persistent — historical rates never change)
CREATE TABLE IF NOT EXISTS exchange_rates (
    rate_date DATE NOT NULL,
    rates JSON NOT NULL COMMENT 'All currency rates relative to USD for this date',
    fetched_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (rate_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Outreach CRM Tables
-- ============================================

-- Outreach leads for business discovery and outreach tracking
CREATE TABLE IF NOT EXISTS outreach_leads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    business_name VARCHAR(255) NOT NULL,
    contact_name VARCHAR(255) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    website VARCHAR(500) DEFAULT NULL,
    address VARCHAR(500) DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    source VARCHAR(100) DEFAULT 'manual',
    status ENUM('new','draft_generated','approved','contacted','replied','interested','not_interested','onboarded','email_bounced') DEFAULT 'new',
    response_status ENUM('no_response','positive','neutral','negative') DEFAULT 'no_response',
    approval_status ENUM('not_drafted','draft_ready','needs_review','approved','sent') DEFAULT 'not_drafted',
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
    first_contact_date DATETIME DEFAULT NULL,
    last_contact_date DATETIME DEFAULT NULL,
    followup_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
    last_followup_at DATETIME DEFAULT NULL,
    next_followup_due_at DATETIME DEFAULT NULL,
    original_message_id VARCHAR(255) DEFAULT NULL,
    offer_sent TINYINT(1) DEFAULT 0,
    notes TEXT DEFAULT NULL,
    feedback_summary TEXT DEFAULT NULL,
    draft_subject VARCHAR(500) DEFAULT NULL,
    ab_test_id INT DEFAULT NULL,
    ab_variant_id INT DEFAULT NULL,
    draft_body TEXT DEFAULT NULL,
    drafted_at DATETIME DEFAULT NULL,
    approved_at DATETIME DEFAULT NULL,
    sent_at DATETIME DEFAULT NULL,
    unsubscribe_token VARCHAR(64) UNIQUE DEFAULT NULL,
    contact_page_url VARCHAR(500) DEFAULT NULL,
    places_id VARCHAR(255) DEFAULT NULL,
    company_size ENUM('small','medium','large') DEFAULT NULL,
    country CHAR(2) DEFAULT 'CA',
    business_summary TEXT DEFAULT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_outreach_status (status),
    INDEX idx_outreach_city (city),
    INDEX idx_outreach_approval (approval_status),
    INDEX idx_outreach_company_size (company_size),
    INDEX idx_unsubscribe_token (unsubscribe_token),
    INDEX idx_outreach_ab (ab_test_id, ab_variant_id),
    INDEX idx_outreach_ab_variant (ab_variant_id),
    INDEX idx_outreach_followup_due (next_followup_due_at, status, followup_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- For existing installs, add the A/B columns:
--   ALTER TABLE outreach_leads
--     ADD COLUMN ab_test_id INT NULL AFTER draft_subject,
--     ADD COLUMN ab_variant_id INT NULL AFTER ab_test_id,
--     ADD INDEX idx_outreach_ab (ab_test_id, ab_variant_id),
--     ADD INDEX idx_outreach_ab_variant (ab_variant_id);
-- (Existing installs that already added idx_outreach_ab need only:
--   ALTER TABLE outreach_leads ADD INDEX idx_outreach_ab_variant (ab_variant_id);)
--
-- Existing installs also need 'email_bounced' added to the status ENUM
-- so the Resend webhook can flag bounced/complained recipients:
--   ALTER TABLE outreach_leads
--     MODIFY COLUMN status ENUM('new','draft_generated','approved','contacted','replied','interested','not_interested','onboarded','email_bounced') DEFAULT 'new';

-- Email suppression list (unsubscribes, opt-outs across all email contexts)
-- Known context values:
--   'outreach'           - cold outreach campaign
--   'reviews'            - review-request emails to license-key holders
--   'product_updates'    - release notes / feature announcements
--   'tips_onboarding'    - how-to / getting-started nudges
--   'promotions'         - discount codes / upsells
--   'community_digest'   - replies / activity digest
--   'all_marketing'      - blanket suppression of all marketing contexts
CREATE TABLE IF NOT EXISTS email_suppressions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    context VARCHAR(50) NOT NULL DEFAULT 'outreach',
    reason VARCHAR(255) DEFAULT NULL,
    source_id INT DEFAULT NULL,
    suppressed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email_context (email, context),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log of marketing emails actually sent (for debugging and de-duplication)
CREATE TABLE IF NOT EXISTS email_marketing_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    context VARCHAR(50) NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    related_id INT DEFAULT NULL COMMENT 'license_keys.id for review emails, etc.',
    INDEX idx_email_context (email, context),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity log for outreach leads
CREATE TABLE IF NOT EXISTS outreach_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    details TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES outreach_leads(id) ON DELETE CASCADE,
    INDEX idx_outreach_activity_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-event delivery / engagement records from the Resend webhook
-- (webhooks/resend.php). One row per (message_id, event_type); the unique key
-- gives idempotency on Svix retries.
CREATE TABLE IF NOT EXISTS outreach_email_events (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    event_type VARCHAR(40) NOT NULL,
    message_id VARCHAR(255) DEFAULT NULL,
    occurred_at DATETIME NOT NULL,
    raw_payload TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES outreach_leads(id) ON DELETE CASCADE,
    UNIQUE KEY unique_message_event (message_id, event_type),
    INDEX idx_email_events_lead_type (lead_id, event_type),
    INDEX idx_email_events_occurred (occurred_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A/B tests for outreach email variants. variant_type covers every test type
-- the framework supports: subject, body, sender, cta, preheader, format,
-- personalization, followup_sequence. One first-touch test (everything except followup_sequence)
-- and one follow-up test (followup_sequence) can be active concurrently; activating a test
-- pauses any other active test in the same phase only.
CREATE TABLE IF NOT EXISTS outreach_ab_tests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    variant_type ENUM('subject','body','sender','cta','preheader','format','personalization','followup_sequence') NOT NULL DEFAULT 'subject',
    status ENUM('draft','active','paused','completed') NOT NULL DEFAULT 'draft',
    notes TEXT DEFAULT NULL,
    started_at DATETIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    winner_variant_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ab_test_status (status),
    INDEX idx_ab_test_type_status (variant_type, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Variants that belong to an A/B test
CREATE TABLE IF NOT EXISTS outreach_ab_variants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    test_id INT NOT NULL,
    label VARCHAR(60) NOT NULL,
    content TEXT NOT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (test_id) REFERENCES outreach_ab_tests(id) ON DELETE CASCADE,
    INDEX idx_ab_variant_test (test_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- For existing installs, expand the variant_type ENUM:
--   ALTER TABLE outreach_ab_tests
--     MODIFY COLUMN variant_type ENUM('subject','body','sender','cta','preheader','format','personalization','followup_sequence') NOT NULL DEFAULT 'subject';

-- ─────────────────────────────────────────────────────────────────────
-- outreach_followups
-- One row per scheduled follow-up touch (touch 1 = original first-touch
-- email, lives in outreach_leads). Created in bulk when first-touch send
-- succeeds (one row per configured touch in followup_sequence_config).
--
-- State machine:
--   scheduled  →  drafted  →  approved  →  sent
--      └─→ halted (replied/unsubscribed/bounced/manual/max_reached)
--      └─→ skipped (admin clicked skip on the drafted row)
--      └─→ failed  (Gemini call failed 3 times)
--
-- ab_test_id / ab_variant_id are copied from the lead's assignment at
-- creation time so the whole sequence shares one variant (we test
-- strategies, not arbitrary mixes).
-- ─────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS outreach_followups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    touch_number TINYINT UNSIGNED NOT NULL,
    scheduled_for DATETIME NOT NULL,
    draft_subject VARCHAR(500) DEFAULT NULL,
    draft_body TEXT DEFAULT NULL,
    drafted_at DATETIME DEFAULT NULL,
    draft_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('scheduled','drafted','approved','sent','halted','skipped','failed') NOT NULL DEFAULT 'scheduled',
    halt_reason VARCHAR(100) DEFAULT NULL,
    ab_test_id INT DEFAULT NULL,
    ab_variant_id INT DEFAULT NULL,
    sent_at DATETIME DEFAULT NULL,
    message_id VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES outreach_leads(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_lead_touch (lead_id, touch_number),
    INDEX idx_status_scheduled (status, scheduled_for)
    -- (no separate idx_lead — uniq_lead_touch already covers lookups by lead_id
    -- via the leftmost-prefix rule, and InnoDB implements UNIQUE as a B-tree)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- For existing installs (run alongside the ALTER above):
--   (the CREATE TABLE IF NOT EXISTS above is safe to re-run.)

-- Cache of email-scrape results keyed by website URL. The same business often
-- shows up under multiple search categories (e.g. "spa" and "massage
-- therapists"), and many sites have no scrape-able email at all — without this
-- cache the cron re-downloads the same dead-ends every run. A NULL email is a
-- valid cached result meaning "we tried, found nothing"; entries refresh after
-- 30 days so sites that later add an email get re-checked.
CREATE TABLE IF NOT EXISTS outreach_scrape_cache (
    id INT PRIMARY KEY AUTO_INCREMENT,
    url VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) DEFAULT NULL,
    last_attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_scrape_attempted (last_attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tracks Shopify storefronts discovered via SerpAPI dorking.
-- Acts as a dedup cache + reject-reason audit log so SerpAPI quota
-- isn't wasted re-evaluating known stores. Fit candidates are imported
-- into outreach_leads (source='shopify_auto') and linked via lead_id.
CREATE TABLE IF NOT EXISTS outreach_shopify_candidates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    canonical_url VARCHAR(500) NOT NULL,
    myshopify_url VARCHAR(500) DEFAULT NULL,
    status ENUM('imported','rejected','error','pending') NOT NULL DEFAULT 'pending',
    reject_reason VARCHAR(100) DEFAULT NULL,
    reject_detail VARCHAR(500) DEFAULT NULL,
    products_count INT DEFAULT NULL,
    first_product_created_at DATETIME DEFAULT NULL,
    detected_country VARCHAR(8) DEFAULT NULL,
    harvested_email VARCHAR(255) DEFAULT NULL,
    lead_id INT DEFAULT NULL,
    last_query VARCHAR(255) DEFAULT NULL,
    checked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_canonical_url (canonical_url),
    INDEX idx_status_checked (status, checked_at),
    INDEX idx_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14-day cache of SerpAPI organic-results responses. The Shopify discovery
-- cron rotates through ~12 dork queries on a daily schedule, and Google's
-- site:myshopify.com results for these queries change slowly. Caching cuts
-- SerpAPI credit burn substantially with no measurable impact on lead
-- discovery. Cache hits do NOT increment serpapi_calls_today.
CREATE TABLE IF NOT EXISTS serpapi_response_cache (
    id INT PRIMARY KEY AUTO_INCREMENT,
    query_hash CHAR(64) NOT NULL,
    query_text VARCHAR(500) NOT NULL,
    response_json MEDIUMTEXT NOT NULL,
    fetched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_query_hash (query_hash),
    INDEX idx_fetched_at (fetched_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-run audit trail for every cron job. Each cron calls cron_run_start
-- at the top, increments named metrics throughout its work
-- (cron_metric_incr / cron_metric_set in cron/lib/run_tracker.php), then
-- calls cron_run_finish on exit. The /admin/crons page reads this table
-- to render a dashboard of cron activity over a chosen time range.
CREATE TABLE IF NOT EXISTS cron_runs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cron_name VARCHAR(100) NOT NULL,
    started_at DATETIME NOT NULL,
    completed_at DATETIME DEFAULT NULL,
    status ENUM('running','ok','error') NOT NULL DEFAULT 'running',
    error_message TEXT DEFAULT NULL,
    metrics JSON DEFAULT NULL,
    INDEX idx_cron_started (cron_name, started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-visitor funnel event log. Captures every step of the referral funnel
-- (landing -> downloads_page -> download_click -> app_first_run ->
-- premium_signup -> premium_paid -> premium_churned). The argo_visitor_id
-- cookie ties events together across sessions; on Premium signup all prior
-- events for that visitor get backfilled with subscription_id + user_id so
-- attribution survives a logged-out browse-to-buy flow.
CREATE TABLE IF NOT EXISTS referral_events (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    visitor_id CHAR(36) DEFAULT NULL COMMENT 'UUID from argo_visitor_id cookie; NULL for unattributed app_first_run events',
    source_code VARCHAR(50) DEFAULT NULL,
    event_type ENUM(
        'landing','downloads_page','download_click','app_first_run',
        'premium_signup','premium_paid','premium_churned'
    ) NOT NULL,
    event_data JSON DEFAULT NULL,
    subscription_id VARCHAR(50) DEFAULT NULL,
    user_id INT DEFAULT NULL,
    page_url VARCHAR(500) DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    country_code VARCHAR(2) DEFAULT NULL,
    environment ENUM('production','sandbox') NOT NULL DEFAULT 'production',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_visitor (visitor_id),
    INDEX idx_source_event_created (source_code, event_type, created_at),
    INDEX idx_event_created (event_type, created_at),
    INDEX idx_subscription (subscription_id),
    INDEX idx_env_created (environment, created_at),
    INDEX idx_ip (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Manual monthly ad-spend entry per source. Used to compute Customer
-- Acquisition Cost (CAC) and LTV:CAC ratio in the admin funnel UI. Period
-- granularity is one calendar month; period_start is always YYYY-MM-01.
CREATE TABLE IF NOT EXISTS campaign_spend (
    id INT PRIMARY KEY AUTO_INCREMENT,
    source_code VARCHAR(50) NOT NULL,
    period_start DATE NOT NULL COMMENT 'First day of month (YYYY-MM-01)',
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'CAD',
    notes TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_source_period (source_code, period_start),
    INDEX idx_period (period_start),
    FOREIGN KEY (source_code) REFERENCES referral_links(source_code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
