-- AFRIVOX NEWS V4 newsroom operations: pitches, assignments and calendar.
-- Additive/idempotent migration. Run after 20261029_v3_security_gates.sql.
SET NAMES utf8mb4;

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

-- Every legacy article foreign key must be removed before nullability changes.
-- Earlier releases left this constraint unnamed, so MySQL may have assigned a name
-- such as editorial_assignments_ibfk_1. Drop by referenced column, not assumed name,
-- then recreate exactly one RESTRICT constraint below to preserve assignment history.
SET @v4_sql = (SELECT IF(COUNT(*)>0,
  CONCAT(
    'ALTER TABLE editorial_assignments ',
    GROUP_CONCAT(DISTINCT CONCAT('DROP FOREIGN KEY `', REPLACE(constraint_name,'`','``'), '`') SEPARATOR ', ')
  ),
  'SELECT 1')
  FROM information_schema.key_column_usage
  WHERE constraint_schema=DATABASE()
    AND table_name='editorial_assignments'
    AND column_name='article_id'
    AND referenced_table_name IS NOT NULL);
PREPARE v4_stmt FROM @v4_sql; EXECUTE v4_stmt; DEALLOCATE PREPARE v4_stmt;

ALTER TABLE editorial_assignments
  MODIFY article_id BIGINT UNSIGNED NULL,
  ADD COLUMN IF NOT EXISTS pitch_id BIGINT UNSIGNED DEFAULT NULL AFTER article_id,
  ADD COLUMN IF NOT EXISTS priority ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal' AFTER assignment_type,
  ADD COLUMN IF NOT EXISTS briefing TEXT DEFAULT NULL AFTER priority,
  ADD COLUMN IF NOT EXISTS started_at DATETIME DEFAULT NULL AFTER due_at,
  ADD COLUMN IF NOT EXISTS completed_at DATETIME DEFAULT NULL AFTER started_at,
  ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE articles
  ADD COLUMN IF NOT EXISTS pitch_id BIGINT UNSIGNED DEFAULT NULL AFTER original_article_id;

-- MySQL and MariaDB do not share a portable CREATE INDEX IF NOT EXISTS form.
-- Guard each index/constraint through INFORMATION_SCHEMA so this file can be rerun safely.
SET @v4_sql = (SELECT IF(COUNT(*)=0,
  'ALTER TABLE editorial_assignments ADD INDEX idx_assignment_assignee_status_due(assigned_to,status,due_at)',
  'SELECT 1') FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='editorial_assignments' AND index_name='idx_assignment_assignee_status_due');
PREPARE v4_stmt FROM @v4_sql; EXECUTE v4_stmt; DEALLOCATE PREPARE v4_stmt;

SET @v4_sql = (SELECT IF(COUNT(*)=0,
  'ALTER TABLE editorial_assignments ADD INDEX idx_assignment_pitch(pitch_id)',
  'SELECT 1') FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='editorial_assignments' AND index_name='idx_assignment_pitch');
PREPARE v4_stmt FROM @v4_sql; EXECUTE v4_stmt; DEALLOCATE PREPARE v4_stmt;

SET @v4_sql = (SELECT IF(COUNT(*)=0,
  'ALTER TABLE articles ADD INDEX idx_article_pitch(pitch_id)',
  'SELECT 1') FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='articles' AND index_name='idx_article_pitch');
PREPARE v4_stmt FROM @v4_sql; EXECUTE v4_stmt; DEALLOCATE PREPARE v4_stmt;

SET @v4_sql = (SELECT IF(COUNT(*)=0,
  'ALTER TABLE editorial_assignments ADD CONSTRAINT fk_assignment_pitch FOREIGN KEY(pitch_id) REFERENCES story_pitches(id)',
  'SELECT 1') FROM information_schema.table_constraints
  WHERE constraint_schema=DATABASE() AND table_name='editorial_assignments' AND constraint_name='fk_assignment_pitch');
PREPARE v4_stmt FROM @v4_sql; EXECUTE v4_stmt; DEALLOCATE PREPARE v4_stmt;

SET @v4_sql = (SELECT IF(COUNT(*)=0,
  'ALTER TABLE editorial_assignments ADD CONSTRAINT fk_assignment_article FOREIGN KEY(article_id) REFERENCES articles(id)',
  'SELECT 1') FROM information_schema.table_constraints
  WHERE constraint_schema=DATABASE() AND table_name='editorial_assignments' AND constraint_name='fk_assignment_article');
PREPARE v4_stmt FROM @v4_sql; EXECUTE v4_stmt; DEALLOCATE PREPARE v4_stmt;

SET @v4_sql = (SELECT IF(COUNT(*)=0,
  'ALTER TABLE articles ADD CONSTRAINT fk_article_pitch FOREIGN KEY(pitch_id) REFERENCES story_pitches(id) ON DELETE SET NULL',
  'SELECT 1') FROM information_schema.table_constraints
  WHERE constraint_schema=DATABASE() AND table_name='articles' AND constraint_name='fk_article_pitch');
PREPARE v4_stmt FROM @v4_sql; EXECUTE v4_stmt; DEALLOCATE PREPARE v4_stmt;

SET @v4_sql = (SELECT IF(COUNT(*)=0,
  'ALTER TABLE editorial_assignments ADD CONSTRAINT chk_assignment_target CHECK (article_id IS NOT NULL OR pitch_id IS NOT NULL)',
  'SELECT 1') FROM information_schema.table_constraints
  WHERE constraint_schema=DATABASE() AND table_name='editorial_assignments' AND constraint_name='chk_assignment_target');
PREPARE v4_stmt FROM @v4_sql; EXECUTE v4_stmt; DEALLOCATE PREPARE v4_stmt;

INSERT IGNORE INTO schema_migrations(migration_key) VALUES ('20261029_v4_newsroom_operations');

