-- AFRIVOX NEWS V7: reviewed video registry and article attachment.
-- Additive only. Back up and rehearse on staging before production.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS video_assets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  provider ENUM('youtube') NOT NULL DEFAULT 'youtube',
  provider_video_id VARCHAR(64) NOT NULL,
  source_url VARCHAR(500) NOT NULL,
  title VARCHAR(240) NOT NULL,
  description TEXT DEFAULT NULL,
  language ENUM('rw','en','fr','multi') NOT NULL DEFAULT 'rw',
  duration_seconds INT UNSIGNED DEFAULT NULL,
  transcript_text MEDIUMTEXT DEFAULT NULL,
  captions_available TINYINT(1) NOT NULL DEFAULT 0,
  alt_text VARCHAR(500) NOT NULL,
  rights_holder VARCHAR(240) NOT NULL,
  license_reference VARCHAR(500) NOT NULL,
  rights_expires_at DATETIME DEFAULT NULL,
  usage_restrictions TEXT DEFAULT NULL,
  status ENUM('draft','submitted','approved','rejected','archived') NOT NULL DEFAULT 'draft',
  review_note VARCHAR(1000) DEFAULT NULL,
  approved_by BIGINT UNSIGNED DEFAULT NULL,
  approved_at DATETIME DEFAULT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  lock_version INT UNSIGNED NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_video_provider_asset(provider,provider_video_id),
  INDEX idx_video_status_language(status,language,created_at),
  CONSTRAINT fk_video_creator FOREIGN KEY(created_by) REFERENCES users(id),
  CONSTRAINT fk_video_approver FOREIGN KEY(approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_videos (
  article_id BIGINT UNSIGNED PRIMARY KEY,
  video_asset_id BIGINT UNSIGNED NOT NULL,
  attached_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_article_video_article FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE CASCADE,
  CONSTRAINT fk_article_video_asset FOREIGN KEY(video_asset_id) REFERENCES video_assets(id),
  CONSTRAINT fk_article_video_attacher FOREIGN KEY(attached_by) REFERENCES users(id),
  INDEX idx_article_video_asset(video_asset_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO schema_migrations(migration_key) VALUES ('20261106_v7_video_library');
