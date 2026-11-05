-- AFRIVOX NEWS V6: independently approved advertising creatives and homepage delivery.
-- MySQL 8 / MariaDB: back up and rehearse on staging before production.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS ad_creatives (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  campaign_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  alt_text VARCHAR(500) NOT NULL,
  destination_url VARCHAR(2048) NOT NULL,
  button_text VARCHAR(80) DEFAULT NULL,
  locale ENUM('all','en','rw') NOT NULL DEFAULT 'all',
  disclosure ENUM('ADVERTISEMENT','SPONSORED') NOT NULL DEFAULT 'ADVERTISEMENT',
  private_original_path VARCHAR(700) NOT NULL,
  private_derivative_path VARCHAR(700) NOT NULL,
  public_path VARCHAR(500) DEFAULT NULL,
  mime_type VARCHAR(80) NOT NULL,
  file_size BIGINT UNSIGNED NOT NULL,
  width INT UNSIGNED NOT NULL,
  height INT UNSIGNED NOT NULL,
  sha256 CHAR(64) NOT NULL,
  rights_holder VARCHAR(240) NOT NULL,
  license_reference VARCHAR(500) NOT NULL,
  status ENUM('draft','pending_review','approved','rejected') NOT NULL DEFAULT 'draft',
  review_note TEXT,
  approved_by BIGINT UNSIGNED DEFAULT NULL,
  approved_at DATETIME DEFAULT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_ad_creative_campaign FOREIGN KEY(campaign_id) REFERENCES ad_campaigns(id),
  CONSTRAINT fk_ad_creative_approver FOREIGN KEY(approved_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_ad_creative_creator FOREIGN KEY(created_by) REFERENCES users(id),
  INDEX idx_ad_creative_campaign_status(campaign_id,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ad_placements (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  creative_id BIGINT UNSIGNED NOT NULL,
  slot_key VARCHAR(60) NOT NULL,
  starts_at DATETIME NOT NULL,
  ends_at DATETIME NOT NULL,
  status ENUM('draft','active','paused','completed','cancelled') NOT NULL DEFAULT 'draft',
  created_by BIGINT UNSIGNED NOT NULL,
  updated_by BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_ad_placement_creative FOREIGN KEY(creative_id) REFERENCES ad_creatives(id),
  CONSTRAINT fk_ad_placement_creator FOREIGN KEY(created_by) REFERENCES users(id),
  CONSTRAINT fk_ad_placement_updater FOREIGN KEY(updated_by) REFERENCES users(id),
  CONSTRAINT chk_ad_placement_dates CHECK (ends_at > starts_at),
  INDEX idx_ad_placement_delivery(slot_key,status,starts_at,ends_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ad_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  placement_id BIGINT UNSIGNED NOT NULL,
  event_type ENUM('served','viewable','click') NOT NULL,
  event_token CHAR(64) NOT NULL,
  occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ad_event_placement FOREIGN KEY(placement_id) REFERENCES ad_placements(id),
  UNIQUE KEY uq_ad_event_token(event_type,event_token),
  INDEX idx_ad_event_reporting(placement_id,event_type,occurred_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO schema_migrations(migration_key) VALUES ('20261105_v6_ad_delivery');
