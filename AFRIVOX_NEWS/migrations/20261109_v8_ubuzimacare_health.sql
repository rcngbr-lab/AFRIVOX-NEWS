-- UBUZIMACARE health taxonomy. Additive and backward-compatible.
INSERT INTO categories(name,slug,description,parent_id,language,sort_order,active)
VALUES
('Ubuzima Rusange','general-health','General health education',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',101,1),
('VIH/SIDA','hiv-aids','HIV prevention, testing and care education',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',102,1),
('Malariya','malaria','Malaria prevention and care education',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',103,1),
('Kanseri','cancer','Cancer screening and care education',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',104,1),
('Diyabete','diabetes','Diabetes risk, screening and care education',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',105,1),
('Indwara z’Umutima','heart-diseases','Cardiovascular health education',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',106,1),
('Ubuzima bw’Ababyeyi n’Abana','maternal-child-health','Maternal and child health education',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',107,1),
('Ubuzima bwo mu Mutwe','mental-health','Mental health education and support routes',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',108,1),
('Imirire','nutrition','Nutrition and food safety education',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',109,1),
('Ubuzima bw’Imyororokere','sexual-reproductive-health','Sexual and reproductive health education',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',110,1),
('Indwara Zandura','infectious-diseases','Infectious disease prevention education',(SELECT id FROM (SELECT id FROM categories WHERE slug='ubuzima' LIMIT 1) p), 'rw',111,1)
ON DUPLICATE KEY UPDATE name=VALUES(name),description=VALUES(description),active=1;

INSERT IGNORE INTO schema_migrations(migration_key) VALUES ('20261109_v8_ubuzimacare_health');
