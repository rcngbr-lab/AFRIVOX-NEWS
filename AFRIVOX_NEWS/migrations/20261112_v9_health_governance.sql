-- UBUZIMACARE medical-review publication gate.
-- Backward-compatible: one article flag, one editorial-check enum value and one role.
SET NAMES utf8mb4;

ALTER TABLE articles
  ADD COLUMN IF NOT EXISTS medical_review_required TINYINT(1) NOT NULL DEFAULT 0 AFTER legal_review_required;

ALTER TABLE editorial_checks
  MODIFY check_type ENUM('sources','fairness','right_of_reply','fact_check','legal','medical','copy','media_rights','seo','accessibility') NOT NULL;

INSERT IGNORE INTO roles(role_key,name,description,system_role)
VALUES ('medical_reviewer','Medical Reviewer','Independently reviews clinical accuracy, evidence and potential patient harm.',1);

-- Health-category articles are fail-closed. Existing demo records remain excluded from public health output.
UPDATE articles a
JOIN categories c ON c.id=a.category_id
LEFT JOIN categories p ON p.id=c.parent_id
SET a.medical_review_required=1
WHERE c.slug='ubuzima' OR p.slug='ubuzima' OR c.slug IN
('general-health','hiv-aids','malaria','cancer','diabetes','heart-diseases','maternal-child-health','mental-health','nutrition','sexual-reproductive-health','infectious-diseases');

INSERT IGNORE INTO schema_migrations(migration_key) VALUES ('20261112_v9_health_governance');
