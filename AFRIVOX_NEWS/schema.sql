-- AFRIVOX NEWS production schema (MySQL 8 / MariaDB compatible subset)
-- Existing installations must run migrations/20261028_phase2.sql.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(160) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(60) NOT NULL DEFAULT 'reporter',
  department VARCHAR(100) DEFAULT NULL,
  locale ENUM('rw','en','fr') NOT NULL DEFAULT 'rw',
  mfa_enabled TINYINT(1) NOT NULL DEFAULT 0,
  mfa_required TINYINT(1) NOT NULL DEFAULT 0,
  mfa_secret_encrypted TEXT DEFAULT NULL,
  session_version INT UNSIGNED NOT NULL DEFAULT 1,
  force_password_change TINYINT(1) NOT NULL DEFAULT 1,
  active TINYINT(1) NOT NULL DEFAULT 1,
  suspended_at DATETIME DEFAULT NULL,
  password_changed_at DATETIME DEFAULT NULL,
  last_login_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_users_role_active (role,active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS roles (
  role_key VARCHAR(60) PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  description VARCHAR(500) DEFAULT NULL,
  system_role TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permission definitions are intentionally version-controlled in app/EditorialWorkflow.php.
-- Database role_permissions are not created: this prevents a second contradictory policy source.
CREATE TABLE IF NOT EXISTS user_roles (
  user_id BIGINT UNSIGNED NOT NULL,
  role_key VARCHAR(60) NOT NULL,
  scope_type VARCHAR(40) NOT NULL DEFAULT 'global',
  scope_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
  assigned_by BIGINT UNSIGNED DEFAULT NULL,
  expires_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(user_id,role_key,scope_type,scope_id),
  CONSTRAINT fk_user_role_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_role_role FOREIGN KEY(role_key) REFERENCES roles(role_key),
  CONSTRAINT fk_user_role_assigner FOREIGN KEY(assigned_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  description VARCHAR(500) DEFAULT NULL,
  parent_id INT UNSIGNED DEFAULT NULL,
  language ENUM('rw','en','fr') NOT NULL DEFAULT 'rw',
  sort_order INT NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  INDEX idx_category_navigation (language,active,parent_id,sort_order),
  CONSTRAINT fk_category_parent FOREIGN KEY(parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS authors (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED DEFAULT NULL UNIQUE,
  display_name VARCHAR(160) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  bio TEXT DEFAULT NULL,
  avatar_path VARCHAR(400) DEFAULT NULL,
  public_email VARCHAR(190) DEFAULT NULL,
  verified TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_author_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS story_pitches (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(240) NOT NULL,
  description TEXT NOT NULL,
  proposed_angle TEXT DEFAULT NULL,
  source_plan TEXT DEFAULT NULL,
  urgency ENUM('low','normal','high','breaking') NOT NULL DEFAULT 'normal',
  expected_cost_minor BIGINT UNSIGNED NOT NULL DEFAULT 0,
  currency CHAR(3) NOT NULL DEFAULT 'RWF',
  risk_level ENUM('low','standard','high','critical') NOT NULL DEFAULT 'standard',
  reporting_location VARCHAR(190) DEFAULT NULL,
  status ENUM('submitted','clarification','accepted','rejected','hold','merged','assigned','closed') NOT NULL DEFAULT 'submitted',
  proposed_by BIGINT UNSIGNED NOT NULL,
  assigned_to BIGINT UNSIGNED DEFAULT NULL,
  decision_reason VARCHAR(1000) DEFAULT NULL,
  decided_by BIGINT UNSIGNED DEFAULT NULL,
  merged_into_id BIGINT UNSIGNED DEFAULT NULL,
  deadline DATETIME DEFAULT NULL,
  lock_version INT UNSIGNED NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_pitch_status_deadline(status,deadline),
  INDEX idx_pitch_proposer(proposed_by,status),
  INDEX idx_pitch_assignee(assigned_to,status),
  CONSTRAINT fk_pitch_proposer FOREIGN KEY(proposed_by) REFERENCES users(id),
  CONSTRAINT fk_pitch_assignee FOREIGN KEY(assigned_to) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_pitch_decider FOREIGN KEY(decided_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_pitch_merge FOREIGN KEY(merged_into_id) REFERENCES story_pitches(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS articles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(240) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  subheadline VARCHAR(500) DEFAULT NULL,
  summary TEXT NOT NULL,
  body MEDIUMTEXT NOT NULL,
  key_points TEXT DEFAULT NULL,
  language ENUM('rw','en','fr') NOT NULL DEFAULT 'rw',
  original_article_id BIGINT UNSIGNED DEFAULT NULL,
  pitch_id BIGINT UNSIGNED DEFAULT NULL,
  translation_status ENUM('not_required','assigned','draft','review','approved') NOT NULL DEFAULT 'not_required',
  category_id INT UNSIGNED NOT NULL,
  author_id BIGINT UNSIGNED NOT NULL,
  type ENUM('news','breaking','live','investigation','feature','analysis','editorial','opinion','interview','explainer','factcheck','photo','video','podcast','data','sponsored','press_release','correction') NOT NULL DEFAULT 'news',
  status ENUM('draft','submitted','under_review','revision_requested','fact_check_pending','legal_review_pending','approved','scheduled','published','unpublished','retracted','archived') NOT NULL DEFAULT 'draft',
  cover_image VARCHAR(400) DEFAULT NULL,
  image_caption VARCHAR(400) DEFAULT NULL,
  image_alt VARCHAR(400) DEFAULT NULL,
  image_credit VARCHAR(200) DEFAULT NULL,
  video_url VARCHAR(500) DEFAULT NULL,
  seo_title VARCHAR(240) DEFAULT NULL,
  meta_description VARCHAR(320) DEFAULT NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_breaking TINYINT(1) NOT NULL DEFAULT 0,
  is_premium TINYINT(1) NOT NULL DEFAULT 0,
  is_demo TINYINT(1) NOT NULL DEFAULT 0,
  public_sources TEXT DEFAULT NULL,
  internal_source_notes MEDIUMTEXT DEFAULT NULL,
  verification_evidence MEDIUMTEXT DEFAULT NULL,
  editorial_review_notes TEXT DEFAULT NULL,
  legal_review_notes TEXT DEFAULT NULL,
  source_confidentiality_level ENUM('public','internal','confidential','restricted') NOT NULL DEFAULT 'internal',
  correction_note TEXT DEFAULT NULL,
  risk_level ENUM('low','standard','high','critical') NOT NULL DEFAULT 'standard',
  fact_check_required TINYINT(1) NOT NULL DEFAULT 0,
  legal_review_required TINYINT(1) NOT NULL DEFAULT 0,
  right_of_reply_status ENUM('not_applicable','not_started','requested','received','declined','published_response') NOT NULL DEFAULT 'not_applicable',
  editor_id BIGINT UNSIGNED DEFAULT NULL,
  approved_by BIGINT UNSIGNED DEFAULT NULL,
  published_by BIGINT UNSIGNED DEFAULT NULL,
  lock_version INT UNSIGNED NOT NULL DEFAULT 1,
  scheduled_at DATETIME DEFAULT NULL,
  submitted_at DATETIME DEFAULT NULL,
  approved_at DATETIME DEFAULT NULL,
  published_at DATETIME DEFAULT NULL,
  retracted_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_article_status_published (status,published_at),
  INDEX idx_article_schedule (status,scheduled_at),
  INDEX idx_article_category (category_id,status,published_at),
  INDEX idx_article_author (author_id,status,published_at),
  INDEX idx_article_language (language,status,published_at),
  INDEX idx_article_pitch (pitch_id),
  CONSTRAINT fk_article_category FOREIGN KEY (category_id) REFERENCES categories(id),
  CONSTRAINT fk_article_author FOREIGN KEY (author_id) REFERENCES users(id),
  CONSTRAINT fk_article_pitch FOREIGN KEY (pitch_id) REFERENCES story_pitches(id) ON DELETE SET NULL,
  CONSTRAINT fk_article_editor FOREIGN KEY (editor_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_article_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_article_publisher FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_article_original FOREIGN KEY (original_article_id) REFERENCES articles(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_revisions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  article_id BIGINT UNSIGNED NOT NULL,
  revision_no INT UNSIGNED NOT NULL,
  title VARCHAR(240) NOT NULL,
  summary TEXT NOT NULL,
  body MEDIUMTEXT NOT NULL,
  snapshot_json JSON NOT NULL,
  change_note VARCHAR(600) DEFAULT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_article_revision (article_id,revision_no),
  CONSTRAINT fk_revision_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
  CONSTRAINT fk_revision_user FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS workflow_transitions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  article_id BIGINT UNSIGNED NOT NULL,
  from_status VARCHAR(40) NOT NULL,
  to_status VARCHAR(40) NOT NULL,
  explanation VARCHAR(1000) DEFAULT NULL,
  performed_by BIGINT UNSIGNED NOT NULL,
  occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_workflow_article (article_id,occurred_at),
  CONSTRAINT fk_workflow_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
  CONSTRAINT fk_workflow_user FOREIGN KEY (performed_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS editorial_checks (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  article_id BIGINT UNSIGNED NOT NULL,
  check_type ENUM('sources','fairness','right_of_reply','fact_check','legal','copy','media_rights','seo','accessibility') NOT NULL,
  outcome ENUM('pending','passed','failed','not_applicable') NOT NULL DEFAULT 'pending',
  notes TEXT DEFAULT NULL,
  checked_by BIGINT UNSIGNED DEFAULT NULL,
  checked_at DATETIME DEFAULT NULL,
  UNIQUE KEY uq_article_check (article_id,check_type),
  CONSTRAINT fk_check_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
  CONSTRAINT fk_check_user FOREIGN KEY (checked_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS media_assets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(240) NOT NULL,
  description TEXT DEFAULT NULL,
  asset_type ENUM('image','video','audio','document','graphic','map') NOT NULL,
  storage_path VARCHAR(500) NOT NULL,
  original_path VARCHAR(500) DEFAULT NULL,
  public_path VARCHAR(500) DEFAULT NULL,
  sha256 CHAR(64) DEFAULT NULL,
  mime_type VARCHAR(120) NOT NULL,
  file_size BIGINT UNSIGNED NOT NULL,
  width INT UNSIGNED DEFAULT NULL,
  height INT UNSIGNED DEFAULT NULL,
  creator VARCHAR(200) DEFAULT NULL,
  source_organization VARCHAR(200) DEFAULT NULL,
  license_type VARCHAR(120) DEFAULT NULL,
  copyright_holder VARCHAR(240) DEFAULT NULL,
  usage_restrictions TEXT DEFAULT NULL,
  rights_expires_at DATETIME DEFAULT NULL,
  caption TEXT DEFAULT NULL,
  alt_text VARCHAR(500) DEFAULT NULL,
  approval_status ENUM('pending','approved','restricted','rejected') NOT NULL DEFAULT 'pending',
  malware_scan_status ENUM('unavailable','pending','clean','infected','error') NOT NULL DEFAULT 'unavailable',
  review_note VARCHAR(1000) DEFAULT NULL,
  uploaded_by BIGINT UNSIGNED NOT NULL,
  approved_by BIGINT UNSIGNED DEFAULT NULL,
  approved_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_media_rights (approval_status,rights_expires_at),
  CONSTRAINT fk_media_uploader FOREIGN KEY (uploaded_by) REFERENCES users(id),
  CONSTRAINT fk_media_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_media (
  article_id BIGINT UNSIGNED NOT NULL,
  media_id BIGINT UNSIGNED NOT NULL,
  usage_role ENUM('cover','inline','gallery','attachment','social') NOT NULL DEFAULT 'inline',
  sort_order INT NOT NULL DEFAULT 0,
  PRIMARY KEY(article_id,media_id,usage_role),
  CONSTRAINT fk_article_media_article FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE CASCADE,
  CONSTRAINT fk_article_media_asset FOREIGN KEY(media_id) REFERENCES media_assets(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS live_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(240) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  summary TEXT NOT NULL,
  status ENUM('draft','live','closed','archived') NOT NULL DEFAULT 'draft',
  created_by BIGINT UNSIGNED NOT NULL,
  editor_id BIGINT UNSIGNED DEFAULT NULL,
  started_at DATETIME DEFAULT NULL,
  closed_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_live_creator FOREIGN KEY(created_by) REFERENCES users(id),
  CONSTRAINT fk_live_editor FOREIGN KEY(editor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS live_updates (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  live_event_id BIGINT UNSIGNED NOT NULL,
  body TEXT NOT NULL,
  is_pinned TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('draft','approved','corrected','withdrawn') NOT NULL DEFAULT 'draft',
  correction_note VARCHAR(1000) DEFAULT NULL,
  author_id BIGINT UNSIGNED NOT NULL,
  approved_by BIGINT UNSIGNED DEFAULT NULL,
  published_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_live_updates (live_event_id,status,published_at),
  CONSTRAINT fk_live_update_event FOREIGN KEY(live_event_id) REFERENCES live_events(id) ON DELETE CASCADE,
  CONSTRAINT fk_live_update_author FOREIGN KEY(author_id) REFERENCES users(id),
  CONSTRAINT fk_live_update_approver FOREIGN KEY(approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS subscribers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  verification_token_hash CHAR(64) DEFAULT NULL,
  verification_expires_at DATETIME DEFAULT NULL,
  unsubscribe_token_hash CHAR(64) DEFAULT NULL,
  consent_at DATETIME NOT NULL,
  verified_at DATETIME DEFAULT NULL,
  unsubscribed_at DATETIME DEFAULT NULL,
  status ENUM('pending','confirmed','unsubscribed','suppressed') NOT NULL DEFAULT 'pending',
  locale ENUM('rw','en','fr') NOT NULL DEFAULT 'rw',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_subscriber_confirmation(verification_token_hash,verification_expires_at,status),
  INDEX idx_subscriber_unsubscribe(unsubscribe_token_hash,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(160) NOT NULL,
  message TEXT NOT NULL,
  message_type ENUM('general','correction','complaint','news_tip','rights','privacy') NOT NULL DEFAULT 'general',
  status ENUM('new','in_progress','resolved','archived') NOT NULL DEFAULT 'new',
  assigned_to BIGINT UNSIGNED DEFAULT NULL,
  archived_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS public_form_attempts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  form_key VARCHAR(40) NOT NULL,
  identity_hash CHAR(64) NOT NULL,
  attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_public_form_window(form_key,identity_hash,attempted_at),
  INDEX idx_public_form_cleanup(attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS editorial_assignments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  article_id BIGINT UNSIGNED DEFAULT NULL,
  pitch_id BIGINT UNSIGNED DEFAULT NULL,
  assigned_to BIGINT UNSIGNED NOT NULL,
  assignment_type ENUM('reporting','editing','fact_check','legal','copy','translation','media') NOT NULL,
  priority ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  briefing TEXT DEFAULT NULL,
  status ENUM('assigned','in_progress','completed','cancelled') NOT NULL DEFAULT 'assigned',
  due_at DATETIME DEFAULT NULL,
  started_at DATETIME DEFAULT NULL,
  completed_at DATETIME DEFAULT NULL,
  assigned_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_assignment_assignee_status_due(assigned_to,status,due_at),
  INDEX idx_assignment_pitch(pitch_id),
  CONSTRAINT fk_assignment_article FOREIGN KEY(article_id) REFERENCES articles(id),
  CONSTRAINT fk_assignment_pitch FOREIGN KEY(pitch_id) REFERENCES story_pitches(id),
  CONSTRAINT fk_assignment_user FOREIGN KEY(assigned_to) REFERENCES users(id),
  CONSTRAINT fk_assignment_assigner FOREIGN KEY(assigned_by) REFERENCES users(id),
  CONSTRAINT chk_assignment_target CHECK (article_id IS NOT NULL OR pitch_id IS NOT NULL)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS homepage_slots (
  slot_key VARCHAR(60) PRIMARY KEY,
  article_id BIGINT UNSIGNED DEFAULT NULL,
  starts_at DATETIME DEFAULT NULL,
  ends_at DATETIME DEFAULT NULL,
  updated_by BIGINT UNSIGNED NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_slot_article FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE SET NULL,
  CONSTRAINT fk_slot_user FOREIGN KEY(updated_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS advertisers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  contact_email VARCHAR(190) NOT NULL,
  billing_currency CHAR(3) NOT NULL DEFAULT 'RWF',
  status ENUM('prospect','active','suspended','closed') NOT NULL DEFAULT 'prospect',
  created_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_advertiser_creator FOREIGN KEY(created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ad_campaigns (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  advertiser_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(200) NOT NULL,
  pricing_model ENUM('CPM','CPC','FIXED') NOT NULL,
  budget_minor BIGINT UNSIGNED NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'RWF',
  starts_at DATETIME NOT NULL,
  ends_at DATETIME NOT NULL,
  status ENUM('draft','pending_approval','approved','active','paused','completed','cancelled') NOT NULL DEFAULT 'draft',
  approved_by BIGINT UNSIGNED DEFAULT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_campaign_advertiser FOREIGN KEY(advertiser_id) REFERENCES advertisers(id),
  CONSTRAINT fk_campaign_approver FOREIGN KEY(approved_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_campaign_creator FOREIGN KEY(created_by) REFERENCES users(id),
  CONSTRAINT chk_campaign_dates CHECK (ends_at > starts_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  advertiser_id BIGINT UNSIGNED NOT NULL,
  campaign_id BIGINT UNSIGNED DEFAULT NULL,
  invoice_number VARCHAR(80) NOT NULL UNIQUE,
  idempotency_key VARCHAR(120) NOT NULL UNIQUE,
  amount_minor BIGINT UNSIGNED NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'RWF',
  status ENUM('draft','issued','partially_paid','paid','void','overdue') NOT NULL DEFAULT 'draft',
  due_at DATETIME DEFAULT NULL,
  issued_at DATETIME DEFAULT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_invoice_advertiser FOREIGN KEY(advertiser_id) REFERENCES advertisers(id),
  CONSTRAINT fk_invoice_campaign FOREIGN KEY(campaign_id) REFERENCES ad_campaigns(id) ON DELETE SET NULL,
  CONSTRAINT fk_invoice_creator FOREIGN KEY(created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_id BIGINT UNSIGNED NOT NULL,
  idempotency_key VARCHAR(120) NOT NULL UNIQUE,
  amount_minor BIGINT UNSIGNED NOT NULL,
  currency CHAR(3) NOT NULL,
  method VARCHAR(60) NOT NULL,
  reference VARCHAR(160) DEFAULT NULL,
  received_at DATETIME NOT NULL,
  recorded_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_payment_invoice FOREIGN KEY(invoice_id) REFERENCES invoices(id),
  CONSTRAINT fk_payment_recorder FOREIGN KEY(recorded_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS analytics_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_type VARCHAR(60) NOT NULL,
  article_id BIGINT UNSIGNED DEFAULT NULL,
  referrer_host VARCHAR(190) DEFAULT NULL,
  locale VARCHAR(10) DEFAULT NULL,
  occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_analytics_event_time(event_type,occurred_at),
  CONSTRAINT fk_analytics_article FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  notification_type VARCHAR(80) NOT NULL,
  title VARCHAR(240) NOT NULL,
  body VARCHAR(1000) DEFAULT NULL,
  entity_type VARCHAR(60) DEFAULT NULL,
  entity_id BIGINT UNSIGNED DEFAULT NULL,
  read_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notification_user(user_id,read_at,created_at),
  CONSTRAINT fk_notification_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED DEFAULT NULL,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(60) NOT NULL,
  entity_id BIGINT UNSIGNED DEFAULT NULL,
  detail VARCHAR(600) DEFAULT NULL,
  request_id CHAR(32) DEFAULT NULL,
  prev_hash CHAR(64) DEFAULT NULL,
  event_hash CHAR(64) DEFAULT NULL,
  occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_when (occurred_at),
  INDEX idx_audit_entity (entity_type,entity_id,occurred_at),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  identifier_hash CHAR(64) NOT NULL,
  attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_login_attempt (identifier_hash,attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mfa_attempts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  identifier_hash CHAR(64) NOT NULL,
  attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_mfa_attempt (user_id,identifier_hash,attempted_at),
  CONSTRAINT fk_mfa_attempt_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mfa_recovery_codes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  code_hash VARCHAR(255) NOT NULL,
  used_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_mfa_recovery_user (user_id,used_at),
  CONSTRAINT fk_mfa_recovery_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  used_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_password_reset_user (user_id,expires_at),
  CONSTRAINT fk_password_reset_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS source_access_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  article_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED DEFAULT NULL,
  action VARCHAR(40) NOT NULL,
  reason VARCHAR(240) NOT NULL,
  request_id CHAR(32) NOT NULL,
  occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_source_access (article_id,occurred_at),
  CONSTRAINT fk_source_access_article FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE CASCADE,
  CONSTRAINT fk_source_access_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS schema_migrations (
  migration_key VARCHAR(120) PRIMARY KEY,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS system_settings (
  setting_key VARCHAR(120) PRIMARY KEY,
  setting_value TEXT DEFAULT NULL,
  is_secret TINYINT(1) NOT NULL DEFAULT 0,
  updated_by BIGINT UNSIGNED DEFAULT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_setting_user FOREIGN KEY(updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
