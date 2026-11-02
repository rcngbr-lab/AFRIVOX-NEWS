-- Apply once to an existing v1 MySQL installation. Take and verify a backup first.
-- This migration is additive except for widening role/type/status ENUMs to VARCHAR/expanded ENUMs.
ALTER TABLE users MODIFY role VARCHAR(60) NOT NULL DEFAULT 'reporter',
  ADD COLUMN IF NOT EXISTS department VARCHAR(100) DEFAULT NULL AFTER role,
  ADD COLUMN IF NOT EXISTS locale ENUM('rw','en','fr') NOT NULL DEFAULT 'rw' AFTER department,
  ADD COLUMN IF NOT EXISTS mfa_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER locale,
  ADD COLUMN IF NOT EXISTS last_login_at DATETIME DEFAULT NULL AFTER active;

ALTER TABLE articles MODIFY status VARCHAR(40) NOT NULL DEFAULT 'draft';
UPDATE articles SET status='submitted' WHERE status='review';
ALTER TABLE articles
  MODIFY type ENUM('news','breaking','live','investigation','feature','analysis','editorial','opinion','interview','explainer','factcheck','photo','video','podcast','data','sponsored','press_release','correction') NOT NULL DEFAULT 'news',
  MODIFY status ENUM('draft','submitted','editorial_review','fact_check','legal_review','approved','scheduled','published','archived','retracted') NOT NULL DEFAULT 'draft',
  ADD COLUMN IF NOT EXISTS subheadline VARCHAR(500) DEFAULT NULL AFTER slug,
  ADD COLUMN IF NOT EXISTS key_points TEXT DEFAULT NULL AFTER body,
  ADD COLUMN IF NOT EXISTS language ENUM('rw','en','fr') NOT NULL DEFAULT 'rw' AFTER key_points,
  ADD COLUMN IF NOT EXISTS original_article_id BIGINT UNSIGNED DEFAULT NULL AFTER language,
  ADD COLUMN IF NOT EXISTS translation_status ENUM('not_required','assigned','draft','review','approved') NOT NULL DEFAULT 'not_required' AFTER original_article_id,
  ADD COLUMN IF NOT EXISTS image_alt VARCHAR(400) DEFAULT NULL AFTER image_caption,
  ADD COLUMN IF NOT EXISTS seo_title VARCHAR(240) DEFAULT NULL AFTER video_url,
  ADD COLUMN IF NOT EXISTS meta_description VARCHAR(320) DEFAULT NULL AFTER seo_title,
  ADD COLUMN IF NOT EXISTS is_premium TINYINT(1) NOT NULL DEFAULT 0 AFTER is_breaking,
  ADD COLUMN IF NOT EXISTS risk_level ENUM('low','standard','high','critical') NOT NULL DEFAULT 'standard' AFTER correction_note,
  ADD COLUMN IF NOT EXISTS fact_check_required TINYINT(1) NOT NULL DEFAULT 0 AFTER risk_level,
  ADD COLUMN IF NOT EXISTS legal_review_required TINYINT(1) NOT NULL DEFAULT 0 AFTER fact_check_required,
  ADD COLUMN IF NOT EXISTS right_of_reply_status ENUM('not_applicable','not_started','requested','received','declined','published_response') NOT NULL DEFAULT 'not_applicable' AFTER legal_review_required,
  ADD COLUMN IF NOT EXISTS scheduled_at DATETIME DEFAULT NULL AFTER editor_id,
  ADD COLUMN IF NOT EXISTS retracted_at DATETIME DEFAULT NULL AFTER published_at;

ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS request_id CHAR(32) DEFAULT NULL AFTER detail;

-- Remaining tables are identical to their definitions in schema.sql. The migration runner
-- executes schema.sql after this ALTER block; CREATE TABLE IF NOT EXISTS makes that safe.
