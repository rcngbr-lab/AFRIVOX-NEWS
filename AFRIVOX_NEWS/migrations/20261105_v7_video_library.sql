-- AFRIVOX NEWS V7: rights-reviewed, externally hosted video library.
-- Run on existing MySQL/MariaDB installations after the previous migrations.
-- For new installations schema.sql already includes this table.
CREATE TABLE IF NOT EXISTS video_clips (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(240) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  description TEXT NOT NULL,
  provider ENUM('youtube') NOT NULL DEFAULT 'youtube',
  provider_video_id CHAR(11) NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  article_id BIGINT UNSIGNED DEFAULT NULL,
  language ENUM('rw','en') NOT NULL DEFAULT 'rw',
  creator VARCHAR(200) NOT NULL,
  source_organization VARCHAR(200) NOT NULL,
  license_type VARCHAR(120) NOT NULL,
  copyright_holder VARCHAR(240) NOT NULL,
  usage_restrictions TEXT DEFAULT NULL,
  rights_expires_at DATETIME DEFAULT NULL,
  status ENUM('draft','pending','approved','published','rejected') NOT NULL DEFAULT 'draft',
  review_note VARCHAR(1000) DEFAULT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  reviewed_by BIGINT UNSIGNED DEFAULT NULL,
  published_by BIGINT UNSIGNED DEFAULT NULL,
  reviewed_at DATETIME DEFAULT NULL,
  published_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_video_public (status,published_at),
  INDEX idx_video_category (category_id,status),
  INDEX idx_video_rights (status,rights_expires_at),
  CONSTRAINT fk_video_category FOREIGN KEY (category_id) REFERENCES categories(id),
  CONSTRAINT fk_video_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE SET NULL,
  CONSTRAINT fk_video_creator FOREIGN KEY (created_by) REFERENCES users(id),
  CONSTRAINT fk_video_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_video_publisher FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO schema_migrations(migration_key) VALUES ('20261105_v7_video_library');
