-- =============================================================
-- Invoice Refunds — schema migration
-- Spec: Argo-Books-Avalonia/docs/superpowers/specs/2026-05-09-invoice-refunds-design.md
-- Plan: Argo-Books-Avalonia/docs/superpowers/plans/2026-05-09-invoice-refunds.md
--
-- Adds:
--   - refund_requests           (state machine for in-flight refunds)
--   - refund_email_codes        (hashed 6-digit codes for refund verification)
--   - refund_audit_log          (append-only audit trail for refund + email-change events)
--   - email_change_requests     (4-step locked-email change flow)
--   - email_verifications       (initial-registration email verification)
--   - refund_velocity_baselines (cron-recomputed company refund baselines)
--   - refund_velocity_config    (admin-tunable thresholds; null company = global default)
--   - refund_idempotency_cache  (Idempotency-Key support for POST endpoints)
--   - portal_companies.locked, lock_reason, locked_at, email_verified_at
--
-- Safe to re-run: all tables use IF NOT EXISTS, ALTER uses guarded checks.
-- Wrap in a transaction so a partial failure rolls back. Note: ALTER TABLE
-- in MySQL is implicitly committing; we still wrap CREATEs.
-- =============================================================

START TRANSACTION;

-- -------------------------------------------------------------
-- refund_requests — primary state machine row
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS refund_requests (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    invoice_id VARCHAR(100) NOT NULL,
    invoice_number VARCHAR(64) NOT NULL,
    customer_name VARCHAR(255) NULL,
    provider ENUM('stripe','paypal','square') NOT NULL,
    provider_payment_id VARCHAR(255) NOT NULL,
    provider_refund_id VARCHAR(255) NULL,
    amount_cents BIGINT NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'USD',
    line_items_json TEXT NULL,
    reason TEXT NULL,
    state ENUM(
        'pending_code','code_verified','cooling_off','processing',
        'completed','cancelled','failed'
    ) NOT NULL DEFAULT 'pending_code',
    state_reason TEXT NULL,
    cooling_off_until DATETIME NULL,
    velocity_tier ENUM('normal','soft_warn','delayed','hard_block') NULL,
    cancel_token VARCHAR(64) NULL,
    requested_ip VARCHAR(45) NULL,
    requested_user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    INDEX idx_refund_requests_company_state (company_id, state, created_at),
    INDEX idx_refund_requests_provider_refund (provider_refund_id),
    INDEX idx_refund_requests_cooling (cooling_off_until),
    INDEX idx_refund_requests_cancel_token (cancel_token),
    CONSTRAINT fk_refund_requests_company FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- refund_email_codes — hashed 6-digit codes for refund verification
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS refund_email_codes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    refund_request_id BIGINT NOT NULL,
    code_hash VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    consumed_at DATETIME NULL,
    attempts INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_refund_email_codes_request (refund_request_id, consumed_at),
    CONSTRAINT fk_refund_email_codes_request FOREIGN KEY (refund_request_id) REFERENCES refund_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- refund_audit_log — append-only event log
-- Used by refund flow AND email-change flow AND admin actions.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS refund_audit_log (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    refund_request_id BIGINT NULL,
    email_change_request_id BIGINT NULL,
    event_type VARCHAR(64) NOT NULL,
    payload_json TEXT NULL,
    actor_type ENUM('owner','admin','system','webhook') NOT NULL,
    actor_id VARCHAR(64) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_company_time (company_id, created_at),
    INDEX idx_audit_request (refund_request_id, created_at),
    INDEX idx_audit_email_change (email_change_request_id, created_at),
    INDEX idx_audit_event (event_type, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- email_change_requests — 4-step locked-email change
-- States: pending → old_verified → new_verified (=> completed) → reverted/cancelled
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS email_change_requests (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    old_email VARCHAR(100) NOT NULL,
    new_email VARCHAR(100) NOT NULL,
    old_email_code_hash VARCHAR(64) NULL,
    new_email_code_hash VARCHAR(64) NULL,
    old_email_verified_at DATETIME NULL,
    new_email_verified_at DATETIME NULL,
    password_verified TINYINT(1) NOT NULL DEFAULT 0,
    state ENUM('pending','old_verified','new_verified','completed','cancelled','reverted') NOT NULL DEFAULT 'pending',
    cancel_token VARCHAR(64) NULL,
    revert_until DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    reverted_at DATETIME NULL,
    INDEX idx_email_change_company (company_id, created_at),
    INDEX idx_email_change_cancel_token (cancel_token),
    CONSTRAINT fk_email_change_company FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- email_verifications — registration email verification (and future purposes)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS email_verifications (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    purpose ENUM('registration') NOT NULL DEFAULT 'registration',
    code_hash VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    consumed_at DATETIME NULL,
    attempts INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email_verifications_company (company_id, purpose, consumed_at),
    CONSTRAINT fk_email_verifications_company FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- refund_velocity_baselines — recomputed nightly by cron
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS refund_velocity_baselines (
    company_id INT PRIMARY KEY,
    daily_avg_refund_cents BIGINT NOT NULL DEFAULT 0,
    daily_avg_refund_count INT NOT NULL DEFAULT 0,
    revenue_30d_cents BIGINT NOT NULL DEFAULT 0,
    last_recomputed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_velocity_baselines_company FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- refund_velocity_config — admin-tunable thresholds
-- company_id = NULL means global default; company-specific row overrides.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS refund_velocity_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NULL,
    soft_warn_multiplier DECIMAL(5,2) NOT NULL DEFAULT 3.00,
    cooling_multiplier DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    cooling_revenue_pct DECIMAL(5,2) NOT NULL DEFAULT 0.25,
    hard_revenue_pct DECIMAL(5,2) NOT NULL DEFAULT 0.50,
    cooling_off_minutes INT NOT NULL DEFAULT 15,
    new_account_floor_cents BIGINT NOT NULL DEFAULT 100000,
    new_account_soft_cents BIGINT NOT NULL DEFAULT 50000,
    new_account_cooling_cents BIGINT NOT NULL DEFAULT 100000,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_velocity_config_company (company_id),
    CONSTRAINT fk_velocity_config_company FOREIGN KEY (company_id) REFERENCES portal_companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed the global default row (company_id IS NULL).
-- Use a separate INSERT IGNORE because UNIQUE on a nullable column doesn't
-- treat NULLs as equal in MySQL; check explicitly.
INSERT INTO refund_velocity_config (company_id)
SELECT NULL FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM refund_velocity_config WHERE company_id IS NULL);

-- -------------------------------------------------------------
-- refund_idempotency_cache — Idempotency-Key support for POST endpoints
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS refund_idempotency_cache (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    idempotency_key VARCHAR(128) NOT NULL,
    body_hash VARCHAR(64) NOT NULL,
    response_status INT NOT NULL,
    response_body MEDIUMTEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_company_key (company_id, idempotency_key),
    INDEX idx_idem_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

-- -------------------------------------------------------------
-- portal_companies extension: locked + email_verified_at columns
-- ALTER TABLE auto-commits in MySQL, so this is outside the transaction.
-- Use information_schema to skip if already added (idempotent re-run).
-- -------------------------------------------------------------

-- locked
SET @col_exists := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'portal_companies' AND COLUMN_NAME = 'locked'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE portal_companies ADD COLUMN locked TINYINT(1) NOT NULL DEFAULT 0',
    'SELECT "portal_companies.locked already exists" AS msg');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- lock_reason
SET @col_exists := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'portal_companies' AND COLUMN_NAME = 'lock_reason'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE portal_companies ADD COLUMN lock_reason TEXT NULL',
    'SELECT "portal_companies.lock_reason already exists" AS msg');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- locked_at
SET @col_exists := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'portal_companies' AND COLUMN_NAME = 'locked_at'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE portal_companies ADD COLUMN locked_at DATETIME NULL',
    'SELECT "portal_companies.locked_at already exists" AS msg');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- email_verified_at
SET @col_exists := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'portal_companies' AND COLUMN_NAME = 'email_verified_at'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE portal_companies ADD COLUMN email_verified_at DATETIME NULL',
    'SELECT "portal_companies.email_verified_at already exists" AS msg');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Backfill: existing companies are grandfathered as already-verified so
-- production users aren't suddenly gated. New registrations after this
-- migration will have email_verified_at = NULL until they verify.
UPDATE portal_companies SET email_verified_at = COALESCE(email_verified_at, created_at, NOW())
WHERE email_verified_at IS NULL;

SELECT 'Migration complete' AS status;
