-- AFRIVOX NEWS V5 audience safety and consent lifecycle.
-- MySQL 8.0.29+ / current MariaDB: back up and test on staging first.
SET NAMES utf8mb4;

ALTER TABLE subscribers
  ADD COLUMN IF NOT EXISTS verification_token_hash CHAR(64) DEFAULT NULL AFTER email,
  ADD COLUMN IF NOT EXISTS verification_expires_at DATETIME DEFAULT NULL AFTER verification_token_hash,
  ADD COLUMN IF NOT EXISTS unsubscribe_token_hash CHAR(64) DEFAULT NULL AFTER verification_expires_at,
  ADD COLUMN IF NOT EXISTS verified_at DATETIME DEFAULT NULL AFTER consent_at,
  ADD COLUMN IF NOT EXISTS unsubscribed_at DATETIME DEFAULT NULL AFTER verified_at,
  ADD COLUMN IF NOT EXISTS locale ENUM('rw','en','fr') NOT NULL DEFAULT 'rw' AFTER status,
  ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER created_at;

-- MySQL lacks CREATE INDEX IF NOT EXISTS, so use metadata-guarded DDL. The same
-- statements work on MariaDB and make a tracked rerun safe.
SET @afrivox_sql := IF(
  (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema=DATABASE() AND table_name='subscribers' AND index_name='idx_subscriber_confirmation')=0,
  'CREATE INDEX idx_subscriber_confirmation ON subscribers(verification_token_hash,verification_expires_at,status)',
  'SELECT 1'
);
PREPARE afrivox_stmt FROM @afrivox_sql; EXECUTE afrivox_stmt; DEALLOCATE PREPARE afrivox_stmt;
SET @afrivox_sql := IF(
  (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema=DATABASE() AND table_name='subscribers' AND index_name='idx_subscriber_unsubscribe')=0,
  'CREATE INDEX idx_subscriber_unsubscribe ON subscribers(unsubscribe_token_hash,status)',
  'SELECT 1'
);
PREPARE afrivox_stmt FROM @afrivox_sql; EXECUTE afrivox_stmt; DEALLOCATE PREPARE afrivox_stmt;

ALTER TABLE contact_messages
  ADD COLUMN IF NOT EXISTS message_type ENUM('general','correction','complaint','news_tip','rights','privacy') NOT NULL DEFAULT 'general' AFTER message;

CREATE TABLE IF NOT EXISTS public_form_attempts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  form_key VARCHAR(40) NOT NULL,
  identity_hash CHAR(64) NOT NULL,
  attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_public_form_window(form_key,identity_hash,attempted_at),
  INDEX idx_public_form_cleanup(attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO schema_migrations(migration_key) VALUES ('20261029_v5_audience');
