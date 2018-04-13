
SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;






INSERT INTO ac_users (use_id, use_first_name, use_name, use_login, use_active, use_pass, use_admin, use_email) VALUES (4, 'demo', 'demo', 'demo', 0, 'fe01ce2a7fbac8fafaed7c982a04e229', 0, NULL);
INSERT INTO ac_users (use_id, use_first_name, use_name, use_login, use_active, use_pass, use_admin, use_email) VALUES (1, NULL, NULL, 'admin', 1, 'b1cc88e1907cde80cb2595fa793b3da9', 1, NULL);






SELECT pg_catalog.setval('audit_connect_ac_id_seq', 1, false);



SELECT pg_catalog.setval('dossier_id', 24, true);






SELECT pg_catalog.setval('dossier_sent_email_id_seq', 1, false);






INSERT INTO modeledef (mod_id, mod_name, mod_desc) VALUES (1, '(BE) Basique', 'Comptabilité Belge, à adapter');
INSERT INTO modeledef (mod_id, mod_name, mod_desc) VALUES (2, '(FR) Basique', 'Comptabilité Française, à adapter');









SELECT pg_catalog.setval('s_modid', 8, true);



SELECT pg_catalog.setval('seq_jnt_use_dos', 28, true);



SELECT pg_catalog.setval('seq_priv_user', 12, true);



INSERT INTO theme (the_name, the_filestyle, the_filebutton) VALUES ('Light', 'style-light.css', NULL);
INSERT INTO theme (the_name, the_filestyle, the_filebutton) VALUES ('Classique', 'style-classic.css', NULL);
INSERT INTO theme (the_name, the_filestyle, the_filebutton) VALUES ('Classic7', 'style-classic7.css', NULL);



INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('demo', 'PAGESIZE', '50');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('demo', 'LANG', 'fr_FR.utf8');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('admin', 'PAGESIZE', '50');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('admin', 'LANG', 'fr_FR.utf8');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('admin', 'TOPMENU', 'TEXT');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('demo', 'THEME', 'Classic7');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('admin', 'THEME', 'Classic7');



SELECT pg_catalog.setval('users_id', 5, true);



INSERT INTO version (val) VALUES (18);



