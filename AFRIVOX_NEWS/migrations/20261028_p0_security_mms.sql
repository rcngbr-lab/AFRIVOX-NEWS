-- AFRIVOX NEWS V2 P0 security, editorial workflow and management migration.
-- Back up the database first. Run after 20261028_phase2.sql.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS roles (
  role_key VARCHAR(60) PRIMARY KEY, name VARCHAR(120) NOT NULL,
  description VARCHAR(500) DEFAULT NULL, system_role TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS mfa_required TINYINT(1) NOT NULL DEFAULT 0 AFTER mfa_enabled,
  ADD COLUMN IF NOT EXISTS mfa_secret_encrypted TEXT DEFAULT NULL AFTER mfa_required,
  ADD COLUMN IF NOT EXISTS session_version INT UNSIGNED NOT NULL DEFAULT 1 AFTER mfa_secret_encrypted,
  ADD COLUMN IF NOT EXISTS force_password_change TINYINT(1) NOT NULL DEFAULT 1 AFTER session_version,
  ADD COLUMN IF NOT EXISTS suspended_at DATETIME DEFAULT NULL AFTER active,
  ADD COLUMN IF NOT EXISTS password_changed_at DATETIME DEFAULT NULL AFTER suspended_at;

UPDATE users SET mfa_required=1
 WHERE role IN ('system_administrator','publisher','editor_in_chief','managing_editor','section_editor','legal_reviewer','advertising_manager','finance_officer');

INSERT IGNORE INTO roles(role_key,name,system_role) VALUES
 ('system_administrator','System Administrator',1),('publisher','Publisher / Executive Management',1),
 ('editor_in_chief','Editor-in-Chief',1),('managing_editor','Managing Editor',1),('section_editor','Section Editor',1),
 ('editor','Editor',1),('reporter','Reporter / Journalist',1),('investigative_reporter','Investigative Reporter',1),
 ('fact_checker','Fact-Checker',1),('copy_editor','Copy Editor',1),('photographer','Photographer',1),
 ('video_producer','Video Producer',1),('multimedia_editor','Multimedia Editor',1),('legal_reviewer','Legal Reviewer',1),
 ('seo_audience_editor','SEO and Audience Editor',1),('social_media_manager','Social Media Manager',1),
 ('advertising_manager','Advertising Manager',1),('finance_officer','Finance Officer',1),
 ('subscriber_support','Subscriber Support Officer',1),('external_contributor','External Contributor',1);

CREATE TABLE IF NOT EXISTS user_roles (
  user_id BIGINT UNSIGNED NOT NULL, role_key VARCHAR(60) NOT NULL,
  scope_type VARCHAR(40) NOT NULL DEFAULT 'global', scope_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
  assigned_by BIGINT UNSIGNED DEFAULT NULL, expires_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(user_id,role_key,scope_type,scope_id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(role_key) REFERENCES roles(role_key),
  FOREIGN KEY(assigned_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO user_roles(user_id,role_key) SELECT id,role FROM users;

ALTER TABLE articles
  ADD COLUMN IF NOT EXISTS public_sources TEXT DEFAULT NULL AFTER is_demo,
  ADD COLUMN IF NOT EXISTS internal_source_notes MEDIUMTEXT DEFAULT NULL AFTER public_sources,
  ADD COLUMN IF NOT EXISTS verification_evidence MEDIUMTEXT DEFAULT NULL AFTER internal_source_notes,
  ADD COLUMN IF NOT EXISTS editorial_review_notes TEXT DEFAULT NULL AFTER verification_evidence,
  ADD COLUMN IF NOT EXISTS legal_review_notes TEXT DEFAULT NULL AFTER editorial_review_notes,
  ADD COLUMN IF NOT EXISTS source_confidentiality_level VARCHAR(20) NOT NULL DEFAULT 'internal' AFTER legal_review_notes,
  ADD COLUMN IF NOT EXISTS submitted_at DATETIME DEFAULT NULL AFTER scheduled_at,
  ADD COLUMN IF NOT EXISTS approved_at DATETIME DEFAULT NULL AFTER submitted_at;

-- Do not copy legacy plaintext into a second column. Run the encryption CLI after
-- AFRIVOX_APP_KEY is set; it reads source_note and writes ciphertext directly.
ALTER TABLE articles MODIFY status VARCHAR(40) NOT NULL DEFAULT 'draft';
UPDATE articles SET status='under_review' WHERE status='editorial_review';
UPDATE articles SET status='fact_check_pending' WHERE status='fact_check';
UPDATE articles SET status='legal_review_pending' WHERE status='legal_review';
ALTER TABLE articles MODIFY status ENUM('draft','submitted','under_review','revision_requested','fact_check_pending','legal_review_pending','approved','scheduled','published','unpublished','retracted','archived') NOT NULL DEFAULT 'draft';

ALTER TABLE media_assets
  ADD COLUMN IF NOT EXISTS original_path VARCHAR(500) DEFAULT NULL AFTER storage_path,
  ADD COLUMN IF NOT EXISTS public_path VARCHAR(500) DEFAULT NULL AFTER original_path,
  ADD COLUMN IF NOT EXISTS sha256 CHAR(64) DEFAULT NULL AFTER public_path,
  ADD COLUMN IF NOT EXISTS copyright_holder VARCHAR(240) DEFAULT NULL AFTER license_type,
  ADD COLUMN IF NOT EXISTS usage_restrictions TEXT DEFAULT NULL AFTER copyright_holder,
  ADD COLUMN IF NOT EXISTS approved_by BIGINT UNSIGNED DEFAULT NULL AFTER uploaded_by,
  ADD COLUMN IF NOT EXISTS approved_at DATETIME DEFAULT NULL AFTER approved_by;

ALTER TABLE contact_messages MODIFY status VARCHAR(40) NOT NULL DEFAULT 'new';
UPDATE contact_messages SET status='in_progress' WHERE status='in_review';
UPDATE contact_messages SET status='archived' WHERE status='deleted';
ALTER TABLE contact_messages
  MODIFY status ENUM('new','in_progress','resolved','archived') NOT NULL DEFAULT 'new',
  ADD COLUMN IF NOT EXISTS assigned_to BIGINT UNSIGNED DEFAULT NULL AFTER status,
  ADD COLUMN IF NOT EXISTS archived_at DATETIME DEFAULT NULL AFTER assigned_to;

CREATE TABLE IF NOT EXISTS editorial_assignments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, article_id BIGINT UNSIGNED NOT NULL,
  assigned_to BIGINT UNSIGNED NOT NULL,
  assignment_type ENUM('reporting','editing','fact_check','legal','copy','translation','media') NOT NULL,
  status ENUM('assigned','in_progress','completed','cancelled') NOT NULL DEFAULT 'assigned',
  due_at DATETIME DEFAULT NULL, assigned_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE CASCADE,
  FOREIGN KEY(assigned_to) REFERENCES users(id), FOREIGN KEY(assigned_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS homepage_slots (
  slot_key VARCHAR(60) PRIMARY KEY, article_id BIGINT UNSIGNED DEFAULT NULL,
  starts_at DATETIME DEFAULT NULL, ends_at DATETIME DEFAULT NULL,
  updated_by BIGINT UNSIGNED NOT NULL, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE SET NULL, FOREIGN KEY(updated_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, advertiser_id BIGINT UNSIGNED NOT NULL,
  campaign_id BIGINT UNSIGNED DEFAULT NULL, invoice_number VARCHAR(80) NOT NULL UNIQUE,
  idempotency_key VARCHAR(120) NOT NULL UNIQUE, amount_minor BIGINT UNSIGNED NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'RWF',
  status ENUM('draft','issued','partially_paid','paid','void','overdue') NOT NULL DEFAULT 'draft',
  due_at DATETIME DEFAULT NULL, issued_at DATETIME DEFAULT NULL,
  created_by BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(advertiser_id) REFERENCES advertisers(id), FOREIGN KEY(campaign_id) REFERENCES ad_campaigns(id) ON DELETE SET NULL,
  FOREIGN KEY(created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, invoice_id BIGINT UNSIGNED NOT NULL,
  idempotency_key VARCHAR(120) NOT NULL UNIQUE, amount_minor BIGINT UNSIGNED NOT NULL,
  currency CHAR(3) NOT NULL, method VARCHAR(60) NOT NULL, reference VARCHAR(160) DEFAULT NULL,
  received_at DATETIME NOT NULL, recorded_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(invoice_id) REFERENCES invoices(id), FOREIGN KEY(recorded_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS analytics_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, event_type VARCHAR(60) NOT NULL,
  article_id BIGINT UNSIGNED DEFAULT NULL, referrer_host VARCHAR(190) DEFAULT NULL,
  locale VARCHAR(10) DEFAULT NULL, occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_analytics_event_time(event_type,occurred_at),
  FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL,
  notification_type VARCHAR(80) NOT NULL, title VARCHAR(240) NOT NULL, body VARCHAR(1000) DEFAULT NULL,
  entity_type VARCHAR(60) DEFAULT NULL, entity_id BIGINT UNSIGNED DEFAULT NULL,
  read_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notification_user(user_id,read_at,created_at), FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
