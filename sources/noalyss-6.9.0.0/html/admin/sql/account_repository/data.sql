--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- Name: dossier_id; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('dossier_id', 24, true);


--
-- Name: s_modid; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_modid', 8, true);


--
-- Name: seq_jnt_use_dos; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_jnt_use_dos', 28, true);


--
-- Name: seq_priv_user; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_priv_user', 12, true);


--
-- Name: users_id; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('users_id', 5, true);


--
-- Data for Name: ac_dossier; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: ac_users; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO ac_users (use_id, use_first_name, use_name, use_login, use_active, use_pass, use_admin) VALUES (4, 'demo', 'demo', 'demo', 1, 'fe01ce2a7fbac8fafaed7c982a04e229', 0);
INSERT INTO ac_users (use_id, use_first_name, use_name, use_login, use_active, use_pass, use_admin) VALUES (1, NULL, NULL, 'phpcompta', 1, 'b1cc88e1907cde80cb2595fa793b3da9', 1);


--
-- Data for Name: jnt_use_dos; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: modeledef; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO modeledef (mod_id, mod_name, mod_desc) VALUES (1, '(BE) Basique', 'Comptabilité Belge, à adapter');
INSERT INTO modeledef (mod_id, mod_name, mod_desc) VALUES (2, '(FR) Basique', 'Comptabilité Française, à adapter');


--
-- Data for Name: priv_user; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: theme; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO theme (the_name, the_filestyle, the_filebutton) VALUES ('classic', 'style.css', NULL);
INSERT INTO theme (the_name, the_filestyle, the_filebutton) VALUES ('Light', 'style-light.css', NULL);
INSERT INTO theme (the_name, the_filestyle, the_filebutton) VALUES ('Colored', 'style-color.css', NULL);


--
-- Data for Name: user_global_pref; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('demo', 'PAGESIZE', '50');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('phpcompta', 'PAGESIZE', '50');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('demo', 'THEME', 'classic');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('phpcompta', 'THEME', 'classic');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('demo', 'LANG', 'fr_FR.utf8');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('phpcompta', 'LANG', 'fr_FR.utf8');
INSERT INTO user_global_pref (user_id, parameter_type, parameter_value) VALUES ('phpcompta', 'TOPMENU', 'TEXT');


--
-- Data for Name: version; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO version (val) VALUES (11);


--
-- PostgreSQL database dump complete
--

