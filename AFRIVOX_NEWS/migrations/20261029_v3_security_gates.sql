-- AFRIVOX NEWS V3 P0: publication gates, media quarantine, MFA recovery and auditable source access.
-- Run cli/migration_preflight.php and take a verified backup before applying.
SET NAMES utf8mb4;

ALTER TABLE articles
  ADD COLUMN IF NOT EXISTS approved_by BIGINT UNSIGNED DEFAULT NULL AFTER editor_id,
  ADD COLUMN IF NOT EXISTS published_by BIGINT UNSIGNED DEFAULT NULL AFTER approved_by,
  ADD COLUMN IF NOT EXISTS lock_version INT UNSIGNED NOT NULL DEFAULT 1 AFTER published_by;

ALTER TABLE media_assets
  ADD COLUMN IF NOT EXISTS malware_scan_status ENUM('unavailable','pending','clean','infected','error') NOT NULL DEFAULT 'unavailable' AFTER approval_status,
  ADD COLUMN IF NOT EXISTS review_note VARCHAR(1000) DEFAULT NULL AFTER malware_scan_status;

ALTER TABLE audit_logs
  ADD COLUMN IF NOT EXISTS prev_hash CHAR(64) DEFAULT NULL AFTER request_id,
  ADD COLUMN IF NOT EXISTS event_hash CHAR(64) DEFAULT NULL AFTER prev_hash;

CREATE TABLE IF NOT EXISTS mfa_attempts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,
 identifier_hash CHAR(64) NOT NULL,attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_mfa_attempt(user_id,identifier_hash,attempted_at),
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mfa_recovery_codes (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,
 code_hash VARCHAR(255) NOT NULL,used_at DATETIME DEFAULT NULL,created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_mfa_recovery_user(user_id,used_at),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,
 token_hash CHAR(64) NOT NULL UNIQUE,expires_at DATETIME NOT NULL,used_at DATETIME DEFAULT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,INDEX idx_password_reset_user(user_id,expires_at),
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS source_access_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,article_id BIGINT UNSIGNED NOT NULL,user_id BIGINT UNSIGNED DEFAULT NULL,
 action VARCHAR(40) NOT NULL,reason VARCHAR(240) NOT NULL,request_id CHAR(32) NOT NULL,
 occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,INDEX idx_source_access(article_id,occurred_at),
 FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE CASCADE,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS schema_migrations (
 migration_key VARCHAR(120) PRIMARY KEY,applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO schema_migrations(migration_key) VALUES ('20261029_v3_security_gates');
