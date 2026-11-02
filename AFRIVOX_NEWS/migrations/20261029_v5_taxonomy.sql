-- AFRIVOX NEWS V5 taxonomy: hierarchical, language-aware, publishable categories.
-- Additive and rerunnable for MySQL 8 / MariaDB. Run after V4 newsroom operations.
SET NAMES utf8mb4;

ALTER TABLE categories
  ADD COLUMN IF NOT EXISTS parent_id INT UNSIGNED DEFAULT NULL AFTER description,
  ADD COLUMN IF NOT EXISTS language ENUM('rw','en','fr') NOT NULL DEFAULT 'rw' AFTER parent_id,
  ADD COLUMN IF NOT EXISTS active TINYINT(1) NOT NULL DEFAULT 1 AFTER sort_order;

SET @v5_sql = (SELECT IF(COUNT(*)=0,
  'ALTER TABLE categories ADD INDEX idx_category_navigation(language,active,parent_id,sort_order)',
  'SELECT 1') FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='categories' AND index_name='idx_category_navigation');
PREPARE v5_stmt FROM @v5_sql; EXECUTE v5_stmt; DEALLOCATE PREPARE v5_stmt;

SET @v5_sql = (SELECT IF(COUNT(*)=0,
  'ALTER TABLE categories ADD CONSTRAINT fk_category_parent FOREIGN KEY(parent_id) REFERENCES categories(id) ON DELETE SET NULL',
  'SELECT 1') FROM information_schema.table_constraints
  WHERE constraint_schema=DATABASE() AND table_name='categories' AND constraint_name='fk_category_parent');
PREPARE v5_stmt FROM @v5_sql; EXECUTE v5_stmt; DEALLOCATE PREPARE v5_stmt;

INSERT IGNORE INTO schema_migrations(migration_key) VALUES ('20261029_v5_taxonomy');
