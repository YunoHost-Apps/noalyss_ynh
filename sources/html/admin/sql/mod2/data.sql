
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;


SELECT pg_catalog.setval('action_detail_ad_id_seq', 1, false);



SELECT pg_catalog.setval('action_gestion_ag_id_seq', 1, false);



SELECT pg_catalog.setval('bilan_b_id_seq', 4, true);



SELECT pg_catalog.setval('bud_card_bc_id_seq', 1, false);



SELECT pg_catalog.setval('bud_detail_bd_id_seq', 1, false);



SELECT pg_catalog.setval('bud_detail_periode_bdp_id_seq', 1, false);



SELECT pg_catalog.setval('del_action_del_id_seq', 1, true);



SELECT pg_catalog.setval('document_d_id_seq', 1, false);



SELECT pg_catalog.setval('document_modele_md_id_seq', 1, false);



SELECT pg_catalog.setval('document_seq', 1, false);



SELECT pg_catalog.setval('document_state_s_id_seq', 3, true);



SELECT pg_catalog.setval('document_type_dt_id_seq', 25, false);



SELECT pg_catalog.setval('extension_ex_id_seq', 1, false);



SELECT pg_catalog.setval('forecast_cat_fc_id_seq', 1, false);



SELECT pg_catalog.setval('forecast_f_id_seq', 1, false);



SELECT pg_catalog.setval('forecast_item_fi_id_seq', 1, false);



SELECT pg_catalog.setval('historique_analytique_ha_id_seq', 1, false);



SELECT pg_catalog.setval('s_jnt_id', 53, true);



SELECT pg_catalog.setval('jnt_letter_jl_id_seq', 1, false);



SELECT pg_catalog.setval('jrn_info_ji_id_seq', 1, false);



SELECT pg_catalog.setval('letter_cred_lc_id_seq', 1, false);



SELECT pg_catalog.setval('letter_deb_ld_id_seq', 1, false);



SELECT pg_catalog.setval('mod_payment_mp_id_seq', 10, true);



SELECT pg_catalog.setval('op_def_op_seq', 1, false);



SELECT pg_catalog.setval('op_predef_detail_opd_id_seq', 1, false);



SELECT pg_catalog.setval('s_oa_group', 7, true);



SELECT pg_catalog.setval('plan_analytique_pa_id_seq', 1, false);



SELECT pg_catalog.setval('poste_analytique_po_id_seq', 1, false);



SELECT pg_catalog.setval('s_attr_def', 27, true);



SELECT pg_catalog.setval('s_cbc', 1, false);



SELECT pg_catalog.setval('s_central', 1, false);



SELECT pg_catalog.setval('s_central_order', 1, false);



SELECT pg_catalog.setval('s_centralized', 1, false);



SELECT pg_catalog.setval('s_currency', 1, true);



SELECT pg_catalog.setval('s_fdef', 6, true);



SELECT pg_catalog.setval('s_fiche', 79, true);



SELECT pg_catalog.setval('s_fiche_def_ref', 18, true);



SELECT pg_catalog.setval('s_form', 1, false);



SELECT pg_catalog.setval('s_formdef', 1, false);



SELECT pg_catalog.setval('s_grpt', 102, true);



SELECT pg_catalog.setval('s_idef', 1, false);



SELECT pg_catalog.setval('s_internal', 1, false);



SELECT pg_catalog.setval('s_invoice', 1, false);



SELECT pg_catalog.setval('s_isup', 1, false);



SELECT pg_catalog.setval('s_jnt_fic_att_value', 875, true);



SELECT pg_catalog.setval('s_jrn', 1, false);



SELECT pg_catalog.setval('s_jrn_1', 1, false);



SELECT pg_catalog.setval('s_jrn_2', 1, false);



SELECT pg_catalog.setval('s_jrn_3', 1, false);



SELECT pg_catalog.setval('s_jrn_4', 1, false);



SELECT pg_catalog.setval('s_jrn_def', 5, false);



SELECT pg_catalog.setval('s_jrn_op', 1, false);



SELECT pg_catalog.setval('s_jrn_pj1', 1, false);



SELECT pg_catalog.setval('s_jrn_pj2', 1, false);



SELECT pg_catalog.setval('s_jrn_pj3', 1, false);



SELECT pg_catalog.setval('s_jrn_pj4', 1, false);



SELECT pg_catalog.setval('s_jrn_rapt', 20, true);



SELECT pg_catalog.setval('s_jrnaction', 5, true);



SELECT pg_catalog.setval('s_jrnx', 1, false);



SELECT pg_catalog.setval('s_periode', 117, true);



SELECT pg_catalog.setval('s_quantity', 13, true);



SELECT pg_catalog.setval('s_stock_goods', 1, false);



SELECT pg_catalog.setval('s_tva', 1000, false);



SELECT pg_catalog.setval('s_user_act', 1, false);



SELECT pg_catalog.setval('s_user_jrn', 8, true);



SELECT pg_catalog.setval('seq_bud_hypothese_bh_id', 1, false);



SELECT pg_catalog.setval('seq_doc_type_1', 1, false);



SELECT pg_catalog.setval('seq_doc_type_10', 1, false);



SELECT pg_catalog.setval('seq_doc_type_2', 1, false);



SELECT pg_catalog.setval('seq_doc_type_20', 1, false);



SELECT pg_catalog.setval('seq_doc_type_21', 1, false);



SELECT pg_catalog.setval('seq_doc_type_22', 1, false);



SELECT pg_catalog.setval('seq_doc_type_3', 1, false);



SELECT pg_catalog.setval('seq_doc_type_4', 1, false);



SELECT pg_catalog.setval('seq_doc_type_5', 1, false);



SELECT pg_catalog.setval('seq_doc_type_6', 1, false);



SELECT pg_catalog.setval('seq_doc_type_7', 1, false);



SELECT pg_catalog.setval('seq_doc_type_8', 1, false);



SELECT pg_catalog.setval('seq_doc_type_9', 1, false);



SELECT pg_catalog.setval('todo_list_tl_id_seq', 1, false);



SELECT pg_catalog.setval('user_sec_extension_use_id_seq', 1, false);



INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1135, 'Ajoute ou modifie des catégories de documents', 'parametre', 'PARCATDOC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (210, 'Ajout de plan analytique', 'compta_anal', 'CAPA');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (220, 'Ajout de poste analytique', 'compta_anal', 'CAPO');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (230, 'Ajout de groupe analytique', 'compta_anal', 'CAGA');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (235, 'Ajout d''operation diverses', 'compta_anal', 'CAOD');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (240, 'Impression', 'compta_anal', 'CAIMP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (300, 'Gestion', 'gestion', 'GESTION');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (305, 'Import en Banque', 'gestion', 'GEBQ');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (307, 'Effacement d''opération', 'gestion', 'GEOP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (310, 'Courrier (lecture & écriture)', 'gestion', 'GECOUR');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (311, 'Fournisseur', 'gestion', 'GESUPPL');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (312, 'Client', 'gestion', 'GECUST');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (700, 'Rapport', 'impression', 'IMPRAP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (710, 'Journaux', 'impression', 'IMPJRN');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (720, 'Fiche', 'impression', 'IMPFIC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (730, 'Poste', 'impression', 'IMPPOSTE');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (740, 'Bilan', 'impression', 'IMPBIL');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (750, 'Balance', 'impression', 'IMPBAL');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (800, 'Ajout de fiche', 'fiche', 'FICADD');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (805, 'Création, modification et effacement de fiche', 'fiche', 'FIC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (910, 'création, modification et effacement de catégorie de fiche', 'fiche', 'FICCAT');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1100, 'Mode comptabilité analytique', 'parametre', 'PARCA');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1110, 'Ajout de période', 'parametre', 'PARPER');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1120, 'Catégorie des fiches', 'parametre', 'PARFIC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1130, 'Document', 'parametre', 'PARDOC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1140, 'Modification journaux', 'parametre', 'PARJRN');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1150, 'TVA', 'parametre', 'PARTVA');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1160, 'Moyen de paiement', 'parametre', 'PARMP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1180, 'Clôture ', 'parametre', 'PARCLO');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1185, 'Changement du plan comptable ', 'parametre', 'PARPCMN');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1170, 'Poste Comptable de base', 'parametre', 'PARPOS');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1190, 'Centralisation', 'parametre', 'PARCENT');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1200, 'Écriture d''ouverture', 'parametre', 'PAREO');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1210, 'Mode strict', 'parametre', 'PARSTR');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1220, 'Coordonnées société', 'parametre', 'PARCOORD');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1230, 'Création de rapport', 'parametre', 'PARRAP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1240, 'Effacement et création d''opération prédéfinie', 'parametre', 'PARPREDE');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1245, 'Sécurité du dossier', 'parametre', 'PARSEC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1500, 'Stock (lecture)', 'stock', 'STOLE');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1510, 'Stock (changement)', 'stock', 'STOWRITE');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (313, 'Administration', 'gestion', 'GEADM');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1600, 'Gestion des extensions', 'extension', 'EXTENSION');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1701, 'Consultation', 'prvision', 'PREVCON');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1702, 'Modification et cration', 'prvision', 'PREVMOD');









INSERT INTO attr_def (ad_id, ad_text) VALUES (1, 'Nom');
INSERT INTO attr_def (ad_id, ad_text) VALUES (2, 'Taux TVA');
INSERT INTO attr_def (ad_id, ad_text) VALUES (3, 'Numéro de compte');
INSERT INTO attr_def (ad_id, ad_text) VALUES (4, 'Nom de la banque');
INSERT INTO attr_def (ad_id, ad_text) VALUES (5, 'Poste Comptable');
INSERT INTO attr_def (ad_id, ad_text) VALUES (6, 'Prix vente');
INSERT INTO attr_def (ad_id, ad_text) VALUES (7, 'Prix achat');
INSERT INTO attr_def (ad_id, ad_text) VALUES (8, 'Durée Amortissement');
INSERT INTO attr_def (ad_id, ad_text) VALUES (9, 'Description');
INSERT INTO attr_def (ad_id, ad_text) VALUES (10, 'Date début');
INSERT INTO attr_def (ad_id, ad_text) VALUES (11, 'Montant initial');
INSERT INTO attr_def (ad_id, ad_text) VALUES (12, 'Personne de contact ');
INSERT INTO attr_def (ad_id, ad_text) VALUES (13, 'numéro de tva ');
INSERT INTO attr_def (ad_id, ad_text) VALUES (14, 'Adresse ');
INSERT INTO attr_def (ad_id, ad_text) VALUES (16, 'pays ');
INSERT INTO attr_def (ad_id, ad_text) VALUES (17, 'téléphone ');
INSERT INTO attr_def (ad_id, ad_text) VALUES (18, 'email ');
INSERT INTO attr_def (ad_id, ad_text) VALUES (19, 'Gestion stock');
INSERT INTO attr_def (ad_id, ad_text) VALUES (20, 'Partie fiscalement non déductible');
INSERT INTO attr_def (ad_id, ad_text) VALUES (21, 'TVA non déductible');
INSERT INTO attr_def (ad_id, ad_text) VALUES (22, 'TVA non déductible récupérable par l''impôt');
INSERT INTO attr_def (ad_id, ad_text) VALUES (23, 'Quick Code');
INSERT INTO attr_def (ad_id, ad_text) VALUES (24, 'Ville');
INSERT INTO attr_def (ad_id, ad_text) VALUES (25, 'Société');
INSERT INTO attr_def (ad_id, ad_text) VALUES (26, 'Fax');
INSERT INTO attr_def (ad_id, ad_text) VALUES (27, 'GSM');
INSERT INTO attr_def (ad_id, ad_text) VALUES (15, 'code postal');
INSERT INTO attr_def (ad_id, ad_text) VALUES (30, 'Numero de client');
INSERT INTO attr_def (ad_id, ad_text) VALUES (31, 'Dépense  charge du gérant (partie privée)');



INSERT INTO attr_min (frd_id, ad_id) VALUES (1, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (1, 2);
INSERT INTO attr_min (frd_id, ad_id) VALUES (2, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (2, 2);
INSERT INTO attr_min (frd_id, ad_id) VALUES (3, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (3, 2);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 3);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 4);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 12);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 13);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 14);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 15);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 16);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 17);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 18);
INSERT INTO attr_min (frd_id, ad_id) VALUES (8, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (8, 12);
INSERT INTO attr_min (frd_id, ad_id) VALUES (8, 13);
INSERT INTO attr_min (frd_id, ad_id) VALUES (8, 14);
INSERT INTO attr_min (frd_id, ad_id) VALUES (8, 15);
INSERT INTO attr_min (frd_id, ad_id) VALUES (8, 16);
INSERT INTO attr_min (frd_id, ad_id) VALUES (8, 17);
INSERT INTO attr_min (frd_id, ad_id) VALUES (8, 18);
INSERT INTO attr_min (frd_id, ad_id) VALUES (9, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (9, 12);
INSERT INTO attr_min (frd_id, ad_id) VALUES (9, 13);
INSERT INTO attr_min (frd_id, ad_id) VALUES (9, 14);
INSERT INTO attr_min (frd_id, ad_id) VALUES (9, 16);
INSERT INTO attr_min (frd_id, ad_id) VALUES (9, 17);
INSERT INTO attr_min (frd_id, ad_id) VALUES (9, 18);
INSERT INTO attr_min (frd_id, ad_id) VALUES (1, 6);
INSERT INTO attr_min (frd_id, ad_id) VALUES (1, 7);
INSERT INTO attr_min (frd_id, ad_id) VALUES (2, 6);
INSERT INTO attr_min (frd_id, ad_id) VALUES (2, 7);
INSERT INTO attr_min (frd_id, ad_id) VALUES (3, 7);
INSERT INTO attr_min (frd_id, ad_id) VALUES (1, 19);
INSERT INTO attr_min (frd_id, ad_id) VALUES (2, 19);
INSERT INTO attr_min (frd_id, ad_id) VALUES (14, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (5, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (5, 4);
INSERT INTO attr_min (frd_id, ad_id) VALUES (5, 10);
INSERT INTO attr_min (frd_id, ad_id) VALUES (5, 12);
INSERT INTO attr_min (frd_id, ad_id) VALUES (6, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (6, 4);
INSERT INTO attr_min (frd_id, ad_id) VALUES (6, 10);
INSERT INTO attr_min (frd_id, ad_id) VALUES (6, 12);
INSERT INTO attr_min (frd_id, ad_id) VALUES (10, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (10, 12);
INSERT INTO attr_min (frd_id, ad_id) VALUES (11, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (11, 12);
INSERT INTO attr_min (frd_id, ad_id) VALUES (12, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (12, 12);
INSERT INTO attr_min (frd_id, ad_id) VALUES (13, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (13, 9);
INSERT INTO attr_min (frd_id, ad_id) VALUES (7, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (7, 8);
INSERT INTO attr_min (frd_id, ad_id) VALUES (7, 9);
INSERT INTO attr_min (frd_id, ad_id) VALUES (7, 10);
INSERT INTO attr_min (frd_id, ad_id) VALUES (5, 11);
INSERT INTO attr_min (frd_id, ad_id) VALUES (6, 11);
INSERT INTO attr_min (frd_id, ad_id) VALUES (1, 15);
INSERT INTO attr_min (frd_id, ad_id) VALUES (9, 15);
INSERT INTO attr_min (frd_id, ad_id) VALUES (15, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (15, 9);
INSERT INTO attr_min (frd_id, ad_id) VALUES (1, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (2, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (3, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (4, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (5, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (6, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (8, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (9, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (10, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (11, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (12, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (13, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (14, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (15, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (7, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (9, 24);
INSERT INTO attr_min (frd_id, ad_id) VALUES (8, 24);
INSERT INTO attr_min (frd_id, ad_id) VALUES (14, 24);
INSERT INTO attr_min (frd_id, ad_id) VALUES (16, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (16, 17);
INSERT INTO attr_min (frd_id, ad_id) VALUES (16, 18);
INSERT INTO attr_min (frd_id, ad_id) VALUES (16, 25);
INSERT INTO attr_min (frd_id, ad_id) VALUES (16, 26);
INSERT INTO attr_min (frd_id, ad_id) VALUES (16, 27);
INSERT INTO attr_min (frd_id, ad_id) VALUES (16, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (17, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (17, 9);
INSERT INTO attr_min (frd_id, ad_id) VALUES (18, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (18, 9);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 4);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 3);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 5);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 15);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 16);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 24);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (2, 30);






INSERT INTO bilan (b_id, b_name, b_file_template, b_file_form, b_type) VALUES (5, 'Comptes de résultat', 'document/fr_fr/fr_plan_abrege_perso_cr1000.rtf', 'document/fr_fr/fr_plan_abrege_perso_cr1000.form', 'rtf');
INSERT INTO bilan (b_id, b_name, b_file_template, b_file_form, b_type) VALUES (1, 'Bilan français', 'document/fr_fr/fr_plan_abrege_perso_bil10000.ods', 'document/fr_fr/fr_plan_abrege_perso_bil10000.form', 'ods');





















INSERT INTO document_state (s_id, s_value) VALUES (1, 'Clôturé');
INSERT INTO document_state (s_id, s_value) VALUES (2, 'A suivre');
INSERT INTO document_state (s_id, s_value) VALUES (3, 'A faire');
INSERT INTO document_state (s_id, s_value) VALUES (4, 'Abandonné');



INSERT INTO document_type (dt_id, dt_value) VALUES (1, 'Document Interne');
INSERT INTO document_type (dt_id, dt_value) VALUES (2, 'Bons de commande client');
INSERT INTO document_type (dt_id, dt_value) VALUES (3, 'Bon de commande Fournisseur');
INSERT INTO document_type (dt_id, dt_value) VALUES (4, 'Facture');
INSERT INTO document_type (dt_id, dt_value) VALUES (5, 'Lettre de rappel');
INSERT INTO document_type (dt_id, dt_value) VALUES (6, 'Courrier');
INSERT INTO document_type (dt_id, dt_value) VALUES (7, 'Proposition');
INSERT INTO document_type (dt_id, dt_value) VALUES (8, 'Email');
INSERT INTO document_type (dt_id, dt_value) VALUES (9, 'Divers');
INSERT INTO document_type (dt_id, dt_value) VALUES (10, 'Note de frais');
INSERT INTO document_type (dt_id, dt_value) VALUES (20, 'Réception commande Fournisseur');
INSERT INTO document_type (dt_id, dt_value) VALUES (21, 'Réception commande Client');
INSERT INTO document_type (dt_id, dt_value) VALUES (22, 'Réception magazine');









INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (2, '410', 'Client', true, 9);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (1, '604', 'Marchandises', true, 2);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (3, '51', 'Banque', true, 4);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (4, '400', 'Fournisseur', true, 8);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (5, '61', 'S & B D', true, 3);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (6, '700', 'Vente', true, 1);



INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (13, 'Dépenses non admises', 674);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (14, 'Administration des Finances', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (15, 'Autres fiches', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (4, 'Banque', 51);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (5, 'Prêt > a un an', 27);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (8, 'Fournisseurs', 400);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (6, 'Prêt < a un an', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (16, 'Contact', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (1, 'Vente Service', 706);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (2, 'Achat Marchandises', 603);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (9, 'Clients', 410);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (10, 'Salaire Administrateur', 644);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (11, 'Salaire Ouvrier', 641);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (12, 'Salaire Employé', 641);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (7, 'Matériel à amortir, immobilisation corporelle', 21);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (3, 'Achat Service et biens divers', 61);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (17, 'Escomptes accordées', 66);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (18, 'Produits Financiers', 76);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (25, 'Compte Salarié / Administrateur', NULL);












INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000398, 3000000, 1, 'Prestation [ case 03 ]', '[700%]-[7000005]');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000399, 3000000, 2, 'Prestation intra [ case 47 ]', '[7000005]');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000400, 3000000, 3, 'Tva due   [case 54]', '[4513]+[4512]+[4511] FROM=01.2005');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000401, 3000000, 4, 'Marchandises, matière première et auxiliaire [case 81 ]', '[60%]');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000402, 3000000, 7, 'Service et bien divers [case 82]', '[61%]');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000403, 3000000, 8, 'bien d''invest [ case 83 ]', '[2400%]');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000404, 3000000, 9, 'TVA déductible [ case 59 ]', 'abs([4117]-[411%])');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000405, 3000000, 8, 'TVA non ded -> voiture', '[610022]*0.21/2');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000406, 3000000, 9, 'Acompte TVA', '[4117]');



INSERT INTO format_csv_banque (name, include_file) VALUES ('Fortis', 'fortis_be.inc.php');
INSERT INTO format_csv_banque (name, include_file) VALUES ('EUB', 'eub_be.inc.php');
INSERT INTO format_csv_banque (name, include_file) VALUES ('ING', 'ing_be.inc.php');
INSERT INTO format_csv_banque (name, include_file) VALUES ('CBC', 'cbc_be.inc.php');
INSERT INTO format_csv_banque (name, include_file) VALUES ('Argenta Belgique', 'argenta_be.inc.php');
INSERT INTO format_csv_banque (name, include_file) VALUES ('CBC Belgique', 'cbc_be.inc.php');
INSERT INTO format_csv_banque (name, include_file) VALUES ('Dexia', 'dexia_be.inc.php');
INSERT INTO format_csv_banque (name, include_file) VALUES ('VMS Keytrade', 'keytrade_be.inc.php');



INSERT INTO formdef (fr_id, fr_label) VALUES (3000000, 'TVA déclaration Belge');









INSERT INTO info_def (id_type, id_description) VALUES ('BON_COMMANDE', 'Numero de bon de commande');
INSERT INTO info_def (id_type, id_description) VALUES ('OTHER', 'Info diverses');






INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (1, 2, 3, 1);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 12, 8, 1);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 3, 17, 1);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 12, 28, 1);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (5, 2, 37, 1);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (6, 2, 41, 1);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (1, 6, 4, 120);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (6, 6, 42, 120);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (1, 7, 5, 130);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (5, 7, 38, 130);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (6, 7, 43, 130);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 14, 10, 40);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 14, 21, 40);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 14, 30, 40);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 16, 12, 70);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 16, 23, 70);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 16, 32, 70);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 17, 13, 80);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 17, 24, 80);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 17, 33, 80);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 18, 14, 90);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 18, 25, 90);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 18, 34, 90);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 23, 45, 400);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (1, 23, 46, 400);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 23, 47, 400);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 23, 48, 400);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (5, 23, 49, 400);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (6, 23, 50, 400);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 24, 51, 60);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 24, 52, 60);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 15, 11, 50);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 15, 22, 50);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 15, 31, 50);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (1, 5, 1, 30);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 5, 6, 30);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 5, 15, 30);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 5, 26, 30);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (5, 5, 35, 30);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (6, 5, 39, 30);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (1, 1, 2, 0);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 1, 7, 0);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 1, 16, 0);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 1, 27, 0);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (5, 1, 36, 0);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (6, 1, 40, 0);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 4, 18, 2);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 12, 19, 3);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (6, 19, 44, 2);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (2, 13, 9, 31);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (3, 13, 20, 31);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (4, 13, 29, 31);









INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (2, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'VEN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (4, 'Voir Impayés', 'Voir toutes les factures non payées', 'user_jrn.php', 'action=voir_jrn_non_paye', 'FR', 'VEN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (1, 'Nouvelle', 'Création d''une facture', 'user_jrn.php', 'action=insert_vente&blank', 'FR', 'VEN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (10, 'Nouveau', 'Encode un nouvel achat (matériel, marchandises, services et biens divers)', 'user_jrn.php', 'action=new&blank', 'FR', 'ACH');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (12, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'ACH');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (14, 'Voir Impayés', 'Voir toutes les factures non payées', 'user_jrn.php', 'action=voir_jrn_non_paye', 'FR', 'ACH');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (20, 'Nouveau', 'Encode un nouvel achat (matériel, marchandises, services et biens divers)', 'user_jrn.php', 'action=new&blank', 'FR', 'FIN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (22, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'FIN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (40, 'Soldes', 'Voir les soldes des comptes en banques', 'user_jrn.php', 'action=solde', 'FR', 'FIN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (30, 'Nouveau', NULL, 'user_jrn.php', 'action=new&blank', 'FR', 'ODS');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (32, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'ODS');



INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref) VALUES (4, 'Opération Diverses', NULL, NULL, NULL, NULL, 5, 5, false, NULL, 'ODS', 'OD-01', 'ODS');
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref) VALUES (2, 'Vente', '', '', '2', '6', 10, 10, true, '''echeance''', 'VEN', 'VEN-01', 'VEN');
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref) VALUES (3, 'Achat', '', '', '5', '4', 10, 10, true, '''echeance''', 'ACH', 'ACH-01', 'ACH');
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref) VALUES (1, 'Financier', '', '', '3,2,4,5', '3,2,4,5', 10, 10, true, '''echeance''', 'FIN', 'FIN-01', 'FIN');






INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 105, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 105, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 105, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 105, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 106, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 106, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 106, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 106, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 107, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 107, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 107, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 107, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 108, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 108, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 108, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 108, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 109, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 109, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 109, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 109, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 110, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 110, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 110, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 110, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 111, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 111, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 111, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 111, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 112, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 112, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 112, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 112, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 113, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 113, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 113, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 113, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 114, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 114, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 114, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 114, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 115, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 115, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 115, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 115, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 116, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 116, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 116, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 116, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 117, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 117, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 117, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 117, 'OP');






INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('VEN', 'Vente');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('ACH', 'Achat');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('ODS', 'Opérations Diverses');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('FIN', 'Banque');












INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (2, 'Caisse', 1, 'VEN', NULL, NULL);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (4, 'Caisse', 1, 'ACH', NULL, NULL);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (1, 'Paiement électronique', 1, 'VEN', NULL, NULL);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (3, 'Par gérant ou administrateur', 2, 'ACH', NULL, NULL);












INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_NAME', 'LaMule');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_TVA', 'FR33 123 456 789');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_STREET', '');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_NUMBER', '');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_CP', '');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_TEL', '');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_PAYS', '');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_COMMUNE', '');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_FAX', '');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_ANALYTIC', 'nu');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_COUNTRY', 'FR');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_STRICT', 'Y');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_TVA_USE', 'Y');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_PJ_SUGGEST', 'Y');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_CHECK_PERIODE', 'N');



INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('BANQUE', '51', 'Poste comptable par défaut pour les banques');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('CAISSE', '53', 'Poste comptable par défaut pour les caisses');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('CUSTOMER', '410', 'Poste comptable par défaut pour les clients');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('VENTE', '707', 'Poste comptable par défaut pour les ventes');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('VIREMENT_INTERNE', '58', 'Poste comptable par défaut pour les virements internes');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('DEP_PRIV', '4890', 'Depense a charge du gerant');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('SUPPLIER', '400', 'Poste par défaut pour les fournisseurs');



INSERT INTO parm_money (pm_id, pm_code, pm_rate) VALUES (1, 'EUR', 1.0000);



INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (105, '2009-01-01', '2009-01-31', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (106, '2009-02-01', '2009-02-28', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (107, '2009-03-01', '2009-03-31', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (108, '2009-04-01', '2009-04-30', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (109, '2009-05-01', '2009-05-31', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (110, '2009-06-01', '2009-06-30', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (111, '2009-07-01', '2009-07-31', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (112, '2009-08-01', '2009-08-31', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (113, '2009-09-01', '2009-09-30', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (114, '2009-10-01', '2009-10-31', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (115, '2009-11-01', '2009-11-30', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (116, '2009-12-01', '2009-12-30', '2009', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (117, '2009-12-31', '2009-12-31', '2009', false, false);



INSERT INTO parm_poste (p_value, p_type) VALUES ('1', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('12', 'CON');
INSERT INTO parm_poste (p_value, p_type) VALUES ('2', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('3', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('41', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('42', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('43', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('44', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('45', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('46', 'CON');
INSERT INTO parm_poste (p_value, p_type) VALUES ('47', 'CON');
INSERT INTO parm_poste (p_value, p_type) VALUES ('481', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('482', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('483', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('484', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('485', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('486', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('487', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('49', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6', 'CHA');
INSERT INTO parm_poste (p_value, p_type) VALUES ('7', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('40', 'ACT');


















INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1', 'comptes de capitaux', '0', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('101', 'Capital', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('105', 'Ecarts de réévaluation', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1061', 'Réserve légale', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1063', 'Réserves statutaires ou contractuelles', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1064', 'Réserves réglementées', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1068', 'Autres réserves', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('108', 'Compte de l''exploitant', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('12', 'résultat de l''exercice (bénéfice ou perte)', '1', 'CON');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('145', 'Amortissements dérogatoires', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('146', 'Provision spéciale de réévaluation', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('147', 'Plus-values réinvesties', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('148', 'Autres provisions réglementées', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('15', 'Provisions pour risques et charges', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('16', 'emprunts et dettes assimilees', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2', 'comptes d''immobilisations', '0', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('20', 'immobilisations incorporelles', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('201', 'Frais d''établissement', '20', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('206', 'Droit au bail', '20', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('207', 'Fonds commercial', '20', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('208', 'Autres immobilisations incorporelles', '20', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('21', 'immobilisations corporelles', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('23', 'immobilisations en cours', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('27', 'autres immobilisations financieres', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('280', 'Amortissements des immobilisations incorporelles', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('281', 'Amortissements des immobilisations corporelles', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('290', 'Provisions pour dépréciation des immobilisations incorporelles', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('291', 'Provisions pour dépréciation des immobilisations corporelles (même ventilation que celle du compte 21)', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('297', 'Provisions pour dépréciation des autres immobilisations financières', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('3', 'comptes de stocks et en cours', '0', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('31', 'matieres premières (et fournitures)', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('32', 'autres approvisionnements', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('33', 'en-cours de production de biens', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('34', 'en-cours de production de services', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('35', 'stocks de produits', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('37', 'stocks de marchandises', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('391', 'Provisions pour dépréciation des matières premières (et fournitures)', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('392', 'Provisions pour dépréciation des autres approvisionnements', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('393', 'Provisions pour dépréciation des en-cours de production de biens', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('394', 'Provisions pour dépréciation des en-cours de production de services', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('395', 'Provisions pour dépréciation des stocks de produits', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('397', 'Provisions pour dépréciation des stocks de marchandises', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4', 'comptes de tiers', '0', 'CON');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('400', 'Fournisseurs et Comptes rattachés', '4', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('409', 'Fournisseurs débiteurs', '4', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('410', 'Clients et Comptes rattachés', '4', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('419', 'Clients créditeurs', '4', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('421', 'Personnel - Rémunérations dues', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('428', 'Personnel - Charges à payer et produits à recevoir', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('43', 'Sécurité sociale et autres organismes sociaux', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('444', 'Etat - Impôts sur les bénéfices', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('445', 'Etat - Taxes sur le chiffre d''affaires', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('447', 'Autres impôts, taxes et versements assimilés', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('45', 'Groupe et associes', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('455', 'Associés - Comptes courants', '45', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('46', 'Débiteurs divers et créditeurs divers', '4', 'CON');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('47', 'comptes transitoires ou d''attente', '4', 'CON');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('481', 'Charges à répartir sur plusieurs exercices', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('486', 'Charges constatées d''avance', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('487', 'Produits constatés d''avance', '4', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('491', 'Provisions pour dépréciation des comptes de clients', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('496', 'Provisions pour dépréciation des comptes de débiteurs divers', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5', 'comptes financiers', '0', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('50', 'valeurs mobilières de placement', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('51', 'banques, établissements financiers et assimilés', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('53', 'Caisse', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('54', 'régies d''avance et accréditifs', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('58', 'virements internes', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('590', 'Provisions pour dépréciation des valeurs mobilières de placement', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6', 'comptes de charges', '0', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('60', 'Achats (sauf 603)', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('603', 'variations des stocks (approvisionnements et marchandises)', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('61', 'autres charges externes - Services extérieurs', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('62', 'autres charges externes - Autres services extérieurs', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('63', 'Impôts, taxes et versements assimiles', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('641', 'Rémunérations du personnel', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('644', 'Rémunération du travail de l''exploitant', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('645', 'Charges de sécurité sociale et de prévoyance', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('646', 'Cotisations sociales personnelles de l''exploitant', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('65', 'Autres charges de gestion courante', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('66', 'Charges financières', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('67', 'Charges exceptionnelles', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('681', 'Dotations aux amortissements et aux provisions - Charges d''exploitation', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('686', 'Dotations aux amortissements et aux provisions - Charges financières', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('687', 'Dotations aux amortissements et aux provisions - Charges exceptionnelles', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('691', 'Participation des salariés aux résultats', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('695', 'Impôts sur les bénéfices', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('697', 'Imposition forfaitaire annuelle des sociétés', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('699', 'Produits - Reports en arrière des déficits', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7', 'comptes de produits', '0', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('701', 'Ventes de produits finis', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('706', 'Prestations de services', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('707', 'Ventes de marchandises', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('708', 'Produits des activités annexes', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('709', 'Rabais, remises et ristournes accordés par l''entreprise', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('713', 'Variation des stocks (en-cours de production, produits)', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('72', 'Production immobilisée', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('73', 'Produits nets partiels sur opérations à long terme', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('74', 'Subventions d''exploitation', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('75', 'Autres produits de gestion courante', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('753', 'Jetons de présence et rémunérations d''administrateurs, gérants,...', '75', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('754', 'Ristournes perçues des coopératives (provenant des excédents)', '75', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('755', 'Quotes-parts de résultat sur opérations faites en commun', '75', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('76', 'Produits financiers', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('77', 'Produits exceptionnels', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('781', 'Reprises sur amortissements et provisions (à inscrire dans les produits d''exploitation)', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('786', 'Reprises sur provisions pour risques (à inscrire dans les produits financiers)', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('787', 'Reprises sur provisions (à inscrire dans les produits exceptionnels)', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('79', 'Transferts de charges', '7', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('8', 'Comptes spéciaux', '0', 'CON');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('9', 'Comptes analytiques', '0', 'CON');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4456601', 'TVA 19,6% - France métropolitaine - Taux immobilisations Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('445701', 'TVA 19,6% - France métropolitaine - Taux immobilisations Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4456602', 'TVA x% - France métropolitaine - Taux anciens Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('445702', 'TVA x% - France métropolitaine - Taux anciens Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4456603', 'TVA 8,5%  - DOM - Taux normal Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('445703', 'TVA 8,5%  - DOM - Taux normal Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4456604', 'TVA 8,5% - DOM - Taux normal NPR Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('445704', 'TVA 8,5% - DOM - Taux normal NPR Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4456605', 'TVA 2,1% - DOM - Taux réduit Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('445705', 'TVA 2,1% - DOM - Taux réduit Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4456606', 'TVA 1,75% - DOM - Taux I Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('445706', 'TVA 1,75% - DOM - Taux I Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4456607', 'TVA 1,05% - DOM - Taux publications de presse Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('445707', 'TVA 1,05% - DOM - Taux publications de presse Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4456608', 'TVA x% - DOM - Taux octroi de mer Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('445708', 'TVA x% - DOM - Taux octroi de mer Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4456609', 'TVA x% - DOM - Taux immobilisations Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('445709', 'TVA x% - DOM - Taux immobilisations Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566010', 'TVA 13% - Corse - Taux I Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457010', 'TVA 13% - Corse - Taux I Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566011', 'TVA 8% - Corse - Taux II Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457011', 'TVA 8% - Corse - Taux II Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566012', 'TVA 2,1% - Corse - Taux III Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457012', 'TVA 2,1% - Corse - Taux III Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566013', 'TVA 0,9% - Corse - Taux IV Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457013', 'TVA 0,9% - Corse - Taux IV Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566014', 'TVA x% - Corse - Taux immobilisations Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457014', 'TVA x% - Corse - Taux immobilisations Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566015', 'TVA x% - Acquisitions intracommunautaires/Pays Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457015', 'TVA x% - Acquisitions intracommunautaires/Pays Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566016', 'TVA x% - Acquisitions intracommunautaires immobilisations/Pays Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457016', 'TVA x% - Acquisitions intracommunautaires immobilisations/Pays Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566017', 'TVA x% - Non imposable : Achats en franchise Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457017', 'TVA x% - Non imposable : Achats en franchise Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566018', 'TVA x% - Non imposable : Exports hors CE/Pays Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457018', 'TVA x% - Non imposable : Exports hors CE/Pays Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566019', 'TVA x% - Non imposable : Autres opérations Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457019', 'TVA x% - Non imposable : Autres opérations Collectée ', '4457', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44566020', 'TVA x% - Non imposable : Livraisons intracommunautaires/Pays Déductible', '4456', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4457020', 'TVA x% - Non imposable : Livraisons intracommunautaires/Pays Collectée ', '4457', 'PAS');






INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (101, 'FR_NOR', 0.1960, 'TVA 19,6% - France métropolitaine - Taux normal', '445661,44571');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (102, 'FR_RED', 0.0550, 'TVA 5,5% - France métropolitaine - Taux réduit', '445662,44572');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (103, 'FR_SRED', 0.0210, 'TVA 2,1% - France métropolitaine - Taux super réduit', '445663,44573');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (104, 'FR_IMMO', 0.1960, 'TVA 19,6% - France métropolitaine - Taux immobilisations', '4456601,445701');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (105, 'FR_ANC', 0.0000, 'TVA x% - France métropolitaine - Taux anciens', '4456602,445702');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (201, 'DOM', 0.0850, 'TVA 8,5%  - DOM - Taux normal', '4456603,445703');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (202, 'DOM_NPR', 0.0850, 'TVA 8,5% - DOM - Taux normal NPR', '4456604,445704');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (203, 'DOM_REDUIT', 0.0210, 'TVA 2,1% - DOM - Taux réduit', '4456605,445705');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (204, 'DOM_I', 0.0175, 'TVA 1,75% - DOM - Taux I', '4456606,445706');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (205, 'DOM_PRESSE', 0.0105, 'TVA 1,05% - DOM - Taux publications de presse', '4456607,445707');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (206, 'DOM_OCTROI', 0.0000, 'TVA x% - DOM - Taux octroi de mer', '4456608,445708');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (207, 'DOM_IMMO', 0.0000, 'TVA x% - DOM - Taux immobilisations', '4456609,445709');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (301, 'COR_I', 0.1300, 'TVA 13% - Corse - Taux I', '44566010,4457010');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (302, 'COR_II', 0.0800, 'TVA 8% - Corse - Taux II', '44566011,4457011');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (303, 'COR_III', 0.0210, 'TVA 2,1% - Corse - Taux III', '44566012,4457012');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (304, 'COR_IV', 0.0090, 'TVA 0,9% - Corse - Taux IV', '44566013,4457013');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (305, 'COR_IMMO', 0.0000, 'TVA x% - Corse - Taux immobilisations', '44566014,4457014');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (401, 'INTRA', 0.0000, 'TVA x% - Acquisitions intracommunautaires/Pays', '44566015,4457015');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (402, 'INTRA_IMMMO', 0.0000, 'TVA x% - Acquisitions intracommunautaires immobilisations/Pays', '44566016,4457016');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (501, 'FRANCH', 0.0000, 'TVA x% - Non imposable : Achats en franchise', '44566017,4457017');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (502, 'EXPORT', 0.0000, 'TVA x% - Non imposable : Exports hors CE/Pays', '44566018,4457018');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (503, 'AUTRE', 0.0000, 'TVA x% - Non imposable : Autres opérations', '44566019,4457019');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (504, 'INTRA_LIV', 0.0000, 'TVA x% - Non imposable : Livraisons intracommunautaires/Pays', '44566020,4457020');



INSERT INTO user_local_pref (user_id, parameter_type, parameter_value) VALUES ('1', 'MINIREPORT', '0');
INSERT INTO user_local_pref (user_id, parameter_type, parameter_value) VALUES ('1', 'PERIODE', '105');












INSERT INTO version (val) VALUES (75);



