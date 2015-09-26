
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



SELECT pg_catalog.setval('del_action_del_id_seq', 1, false);



SELECT pg_catalog.setval('document_d_id_seq', 1, false);



SELECT pg_catalog.setval('document_modele_md_id_seq', 1, false);



SELECT pg_catalog.setval('document_seq', 1, false);



SELECT pg_catalog.setval('document_state_s_id_seq', 3, true);



SELECT pg_catalog.setval('document_type_dt_id_seq', 25, false);



SELECT pg_catalog.setval('extension_ex_id_seq', 1, true);



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



SELECT pg_catalog.setval('s_oa_group', 1, true);



SELECT pg_catalog.setval('plan_analytique_pa_id_seq', 1, false);



SELECT pg_catalog.setval('poste_analytique_po_id_seq', 1, false);



SELECT pg_catalog.setval('s_attr_def', 27, true);



SELECT pg_catalog.setval('s_cbc', 1, false);



SELECT pg_catalog.setval('s_central', 1, false);



SELECT pg_catalog.setval('s_central_order', 1, false);



SELECT pg_catalog.setval('s_centralized', 1, false);



SELECT pg_catalog.setval('s_currency', 1, true);



SELECT pg_catalog.setval('s_fdef', 6, true);



SELECT pg_catalog.setval('s_fiche', 20, true);



SELECT pg_catalog.setval('s_fiche_def_ref', 16, true);



SELECT pg_catalog.setval('s_form', 1, false);



SELECT pg_catalog.setval('s_formdef', 1, false);



SELECT pg_catalog.setval('s_grpt', 2, true);



SELECT pg_catalog.setval('s_idef', 1, false);



SELECT pg_catalog.setval('s_internal', 1, false);



SELECT pg_catalog.setval('s_invoice', 1, false);



SELECT pg_catalog.setval('s_isup', 1, false);



SELECT pg_catalog.setval('s_jnt_fic_att_value', 371, true);



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



SELECT pg_catalog.setval('s_jrn_rapt', 1, false);



SELECT pg_catalog.setval('s_jrnaction', 5, true);



SELECT pg_catalog.setval('s_jrnx', 1, false);



SELECT pg_catalog.setval('s_periode', 91, true);



SELECT pg_catalog.setval('s_quantity', 7, true);



SELECT pg_catalog.setval('s_stock_goods', 1, false);



SELECT pg_catalog.setval('s_tva', 1001, true);



SELECT pg_catalog.setval('s_user_act', 1, false);



SELECT pg_catalog.setval('s_user_jrn', 1, false);



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



SELECT pg_catalog.setval('user_sec_extension_use_id_seq', 1, true);



INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (313, 'Administration', 'gestion', 'GEADM');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1600, 'Gestion des extensions', 'extension', 'EXTENSION');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1701, 'Consultation', 'prvision', 'PREVCON');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1702, 'Modification et cration', 'prvision', 'PREVMOD');
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
INSERT INTO attr_def (ad_id, ad_text) VALUES (31, 'Dpense  charge du grant (partie prive)');



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
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 4);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 3);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 5);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 15);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 16);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 24);
INSERT INTO attr_min (frd_id, ad_id) VALUES (25, 23);
INSERT INTO attr_min (frd_id, ad_id) VALUES (2, 30);






INSERT INTO bilan (b_id, b_name, b_file_template, b_file_form, b_type) VALUES (1, 'Bilan Belge complet', 'document/fr_be/bnb.rtf', 'document/fr_be/bnb.form', 'RTF');





















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



INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (2, '400', 'Client', true, 9);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (1, '604', 'Marchandises', true, 2);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (3, '5500', 'Banque', true, 4);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (4, '440', 'Fournisseur', true, 8);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (5, '61', 'S & B D', true, 3);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id) VALUES (6, '700', 'Vente', true, 1);



INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (1, 'Vente Service', 700);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (2, 'Achat Marchandises', 604);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (3, 'Achat Service et biens divers', 61);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (4, 'Banque', 5500);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (5, 'Prêt > a un an', 17);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (6, 'Prêt < a un an', 430);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (8, 'Fournisseurs', 440);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (9, 'Clients', 400);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (10, 'Salaire Administrateur', 6200);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (11, 'Salaire Ouvrier', 6203);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (12, 'Salaire Employé', 6202);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (13, 'Dépenses non admises', 674);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (14, 'Administration des Finances', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (15, 'Autres fiches', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (7, 'Matériel à amortir', 2400);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (16, 'Contact', NULL);
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









INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (2, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'VEN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (4, 'Voir Impayés', 'Voir toutes les factures non payées', 'user_jrn.php', 'action=voir_jrn_non_paye', 'FR', 'VEN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (1, 'Nouvelle', 'Création d''une facture', 'user_jrn.php', 'action=insert_vente&blank', 'FR', 'VEN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (10, 'Nouveau', 'Encode un nouvel achat (matériel, marchandises, services et biens divers)', 'user_jrn.php', 'action=new&blank', 'FR', 'ACH');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (12, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'ACH');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (14, 'Voir Impayés', 'Voir toutes les factures non payées', 'user_jrn.php', 'action=voir_jrn_non_paye', 'FR', 'ACH');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (20, 'Nouveau', 'Encode un nouvel achat (matériel, marchandises, services et biens divers)', 'user_jrn.php', 'action=new&blank', 'FR', 'FIN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (22, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'FIN');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (30, 'Nouveau', NULL, 'user_jrn.php', 'action=new&blank', 'FR', 'ODS');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (32, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'ODS');
INSERT INTO jrn_action (ja_id, ja_name, ja_desc, ja_url, ja_action, ja_lang, ja_jrn_type) VALUES (40, 'Soldes', 'Voir les soldes des comptes en banques', 'user_jrn.php', 'action=solde', 'FR', 'FIN');



INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref) VALUES (4, 'Opération Diverses', NULL, NULL, NULL, NULL, 5, 5, false, NULL, 'ODS', 'ODS-01', 'ODS');
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref) VALUES (1, 'Financier', '5* ', '5*', '3,2,4', '3,2,4', 5, 5, false, NULL, 'FIN', 'FIN-01', 'FIN');
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref) VALUES (3, 'Achat', '6*', '4*', '5', '4', 1, 3, true, 'échéance', 'ACH', 'ACH-01', 'ACH');
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref) VALUES (2, 'Vente', '4*', '7*', '2', '6', 2, 1, true, 'échéance', 'VEN', 'VEN-01', 'VEN');






INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 79, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 79, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 79, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 79, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 80, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 80, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 80, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 80, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 81, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 81, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 81, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 81, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 82, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 82, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 82, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 82, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 83, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 83, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 83, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 83, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 84, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 84, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 84, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 84, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 85, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 85, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 85, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 85, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 86, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 86, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 86, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 86, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 87, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 87, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 87, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 87, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 88, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 88, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 88, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 88, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 89, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 89, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 89, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 89, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 90, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 90, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 90, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 90, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (4, 91, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (1, 91, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (3, 91, 'OP');
INSERT INTO jrn_periode (jrn_def_id, p_id, status) VALUES (2, 91, 'OP');






INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('FIN', 'Financier');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('VEN', 'Vente');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('ACH', 'Achat');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('ODS', 'Opérations Diverses');












INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (2, 'Caisse', 1, 'VEN', NULL, NULL);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (4, 'Caisse', 1, 'ACH', NULL, NULL);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (1, 'Paiement électronique', 1, 'VEN', NULL, NULL);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_type, mp_fd_id, mp_qcode) VALUES (3, 'Par gérant ou administrateur', 2, 'ACH', NULL, NULL);












INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_NAME', NULL);
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_CP', NULL);
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_COMMUNE', NULL);
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_TVA', NULL);
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_STREET', NULL);
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_NUMBER', NULL);
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_TEL', NULL);
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_PAYS', NULL);
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_FAX', NULL);
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_ANALYTIC', 'nu');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_COUNTRY', 'BE');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_STRICT', 'Y');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_TVA_USE', 'Y');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_PJ_SUGGEST', 'Y');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_CHECK_PERIODE', 'N');



INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('DNA', '6740', 'Dépense non déductible');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('CUSTOMER', '400', 'Poste comptable de base pour les clients');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('COMPTE_TVA', '451', 'TVA à payer');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('BANQUE', '550', 'Poste comptable de base pour les banques');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('VIREMENT_INTERNE', '58', 'Poste Comptable pour les virements internes');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('COMPTE_COURANT', '56', 'Poste comptable pour le compte courant');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('CAISSE', '57', 'Poste comptable pour la caisse');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('TVA_DNA', '6740', 'Tva non déductible s');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('TVA_DED_IMPOT', '619000', 'Tva déductible par l''impôt');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('VENTE', '70', 'Poste comptable de base pour les ventes');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('DEP_PRIV', '4890', 'Depense a charge du gerant');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('SUPPLIER', '440', 'Poste par défaut pour les fournisseurs');



INSERT INTO parm_money (pm_id, pm_code, pm_rate) VALUES (1, 'EUR', 1.0000);



INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (79, '2010-01-01', '2010-01-31', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (80, '2010-02-01', '2010-02-28', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (81, '2010-03-01', '2010-03-31', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (82, '2010-04-01', '2010-04-30', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (83, '2010-05-01', '2010-05-31', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (84, '2010-06-01', '2010-06-30', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (85, '2010-07-01', '2010-07-31', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (86, '2010-08-01', '2010-08-31', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (87, '2010-09-01', '2010-09-30', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (88, '2010-10-01', '2010-10-31', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (89, '2010-11-01', '2010-11-30', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (90, '2010-12-01', '2010-12-30', '2010', false, false);
INSERT INTO parm_periode (p_id, p_start, p_end, p_exercice, p_closed, p_central) VALUES (91, '2010-12-31', '2010-12-31', '2010', false, false);



INSERT INTO parm_poste (p_value, p_type) VALUES ('1', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('101', 'PASINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('141', 'PASINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('42', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('43', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('44', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('45', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('46', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('47', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('48', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('492', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('493', 'PAS');
INSERT INTO parm_poste (p_value, p_type) VALUES ('2', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('2409', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('3', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('491', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('490', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6', 'CHA');
INSERT INTO parm_poste (p_value, p_type) VALUES ('7', 'PRO');
INSERT INTO parm_poste (p_value, p_type) VALUES ('4', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('40', 'ACT');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5501', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5511', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5521', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5531', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5541', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5551', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5561', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5571', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5581', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('5591', 'ACTINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6311', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6321', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6331', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6341', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6351', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6361', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6371', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('649', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6511', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('6701', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('608', 'CHAINV');
INSERT INTO parm_poste (p_value, p_type) VALUES ('709', 'PROINV');


















INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('794', 'Intervention d''associés (ou du propriétaire) dans la perte', '79', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1', 'Fonds propres, provisions pour risques et charges à plus d''un an', '0', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2', 'Frais d''établissement, actifs immobilisés et créances à plus d''un an', '0', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('3', 'Stocks et commandes en cours d''éxécution', '0', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4', 'Créances et dettes à un an au plus', '0', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5', 'Placements de trésorerie et valeurs disponibles', '0', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6', 'Charges', '0', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7', 'Produits', '0', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4000001', 'Client 1', '400', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4000002', 'Client 2', '400', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4000003', 'Client 3', '400', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6040001', 'Electricité', '604', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6040002', 'Loyer', '604', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('55000002', 'Banque 1', '5500', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('55000003', 'Banque 2', '5500', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4400001', 'Fournisseur 1', '440', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4400002', 'Fournisseur 2', '440', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4400003', 'Fournisseur 4', '440', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('610001', 'Electricité', '61', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('610002', 'Loyer', '61', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('610003', 'Assurance', '61', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('610004', 'Matériel bureau', '61', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7000002', 'Marchandise A', '700', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7000001', 'Prestation', '700', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7000003', 'Déplacement', '700', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('101', 'Capital non appelé', '10', 'PASINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6190', 'TVA récupérable par l''impôt', '61', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6740', 'Dépense non admise', '67', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('9', 'Comptes hors Compta', '0', 'CON');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('100', 'Capital souscrit', '10', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1311', 'Autres réserves indisponibles', '131', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('132', ' Réserves immunisées', '13', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6711', 'Suppléments d''impôts estimés', '671', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6712', 'Provisions fiscales constituées', '671', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('672', 'Impôts étrangers sur le résultat de l''exercice', '67', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('673', 'Impôts étrangers sur le résultat d''exercice antérieures', '67', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('68', 'Transferts aux réserves immunisées', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('69', 'Affectations et prélévements', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('690', 'Perte reportée de l''exercice précédent', '69', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('691', 'Dotation à la réserve légale', '69', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('692', 'Dotation aux autres réserves', '69', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('693', 'Bénéfice à reporter', '69', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('694', 'Rémunération du capital', '69', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('695', 'Administrateurs ou gérants', '69', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('696', 'Autres allocataires', '69', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('70', 'Chiffre d''affaire', '7', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('700', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('701', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('702', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('703', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('704', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('706', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('707', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('709', 'Remises, ristournes et rabais accordés(-)', '70', 'PROINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('71', 'Variations des stocks et commandes en cours d''éxécution', '7', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('712', 'des en-cours de fabrication', '71', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('713', 'des produits finis', '71', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('715', 'des immeubles construits destinés à la vente', '71', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('717', ' des commandes  en cours d''éxécution', '71', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7170', 'Valeur d''acquisition', '717', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7171', 'Bénéfice pris en compte', '717', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('72', 'Production immobilisée', '7', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('74', 'Autres produits d''exploitation', '7', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('740', 'Subsides d'' exploitation  et montants compensatoires', '74', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('741', 'Plus-values sur réalisation courantes d'' immobilisations corporelles', '74', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('742', 'Plus-values sur réalisations de créances commerciales', '74', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('743', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('744', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('745', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('746', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('747', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('748', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('75', 'Produits financiers', '7', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('750', 'Produits sur immobilisations financières', '75', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('751', 'Produits des actifs circulants', '75', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('752', 'Plus-value sur réalisations d''actis circulants', '75', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('753', 'Subsides en capital et intérêts', '75', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('754', 'Différences de change', '75', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('755', 'Ecarts de conversion des devises', '75', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('221', 'Construction', '22', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('756', 'Produits financiers divers', '75', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('757', 'Produits financiers divers', '75', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('758', 'Produits financiers divers', '75', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('759', 'Produits financiers divers', '75', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('76', 'Produits exceptionnels', '7', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('760', 'Reprise d''amortissements et de réductions de valeur', '76', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7601', 'sur immobilisations corporelles', '760', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7602', 'sur immobilisations incorporelles', '760', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('761', 'Reprises de réductions de valeur sur immobilisations financières', '76', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('762', 'Reprises de provisions pour risques et charges exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('763', 'Plus-value sur réalisation d''actifs immobilisé', '76', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('764', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('765', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('766', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('767', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('768', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('769', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('77', 'Régularisations d''impôts et reprises de provisions fiscales', '7', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('771', 'impôts belges sur le résultat', '77', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7710', 'Régularisations d''impôts dus ou versé', '771', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7711', 'Régularisations d''impôts estimés', '771', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('7712', 'Reprises de provisions fiscales', '771', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('773', 'Impôts étrangers sur le résultats', '77', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('79', 'Affectations et prélévements', '7', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('790', 'Bénéfice reporté de l''exercice précédent', '79', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('791', 'Prélévement sur le capital et les primes d''émission', '79', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('792', 'Prélévement sur les réserves', '79', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('793', 'Perte à reporter', '79', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6301', 'Dotations aux amortissements sur immobilisations incorporelles', '630', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6302', 'Dotations aux amortissements sur immobilisations corporelles', '630', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6308', 'Dotations aux réductions de valeur sur immobilisations incorporelles', '630', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6309', 'Dotations aux réductions de valeur sur immobilisations corporelles', '630', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('631', 'Réductions de valeur sur stocks', '63', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6310', 'Dotations', '631', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6311', 'Reprises(-)', '631', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('632', 'Réductions de valeur sur commande en cours d''éxécution', '63', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6320', 'Dotations', '632', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6321', 'Reprises(-)', '632', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('633', 'Réductions de valeurs sur créances commerciales à plus d''un an', '63', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6330', 'Dotations', '633', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6331', 'Reprises(-)', '633', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('634', 'Réductions de valeur sur créances commerciales à un an au plus', '63', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6340', 'Dotations', '634', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6341', 'Reprise', '634', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('635', 'Provisions pour pensions et obligations similaires', '63', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6350', 'Dotations', '635', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6351', 'Utilisation et reprises', '635', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('636', 'Provisions pour grosses réparations et gros entretien', '63', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6360', 'Dotations', '636', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6361', 'Reprises(-)', '636', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('637', 'Provisions pour autres risques et charges', '63', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6370', 'Dotations', '637', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6371', 'Reprises(-)', '637', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('64', 'Autres charges d''exploitation', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('640', 'Charges fiscales d''exploitation', '64', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('641', 'Moins-values sur réalisations courantes d''immobilisations corporelles', '64', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('642', 'Moins-value sur réalisation de créances commerciales', '64', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('643', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('644', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('645', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('646', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('647', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('648', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('649', 'Charges d''exploitation portées à l''actif au titre de frais de restructuration(-)', '64', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('65', 'Charges financières', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('650', 'Charges des dettes', '65', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6500', 'Intérêts, commmissions et frais afférents aux dettes', '650', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6501', 'Amortissements des frais d''émissions d''emrunts et des primes de remboursement', '650', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6502', 'Autres charges des dettes', '650', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6503', 'Intérêts intercalaires portés à l''actif(-)', '650', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('651', 'Réductions de valeur sur actifs circulants', '65', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6510', 'Dotations', '651', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6511', 'Reprises(-)', '651', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('652', 'Moins-value sur réalisation d''actifs circulants', '65', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('653', 'Charges d''escompte de créances', '65', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('654', 'Différences de changes', '65', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('655', 'Ecarts de conversion des devises', '65', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('656', 'Charges financières diverses', '65', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('657', 'Charges financières diverses', '65', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('658', 'Charges financières diverses', '65', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('659', 'Charges financières diverses', '65', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('66', 'Charges exceptionnelles', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('660', 'Amortissements et réductions de valeur exceptionnels (dotations)', '66', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6600', 'sur frais d''établissement', '660', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6601', 'sur immobilisations incorporelles', '660', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6602', 'sur immobilisations corporelles', '660', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('661', 'Réductions de valeur sur immobilisations financières (dotations)', '66', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('662', 'Provisions pour risques et charges exceptionnels', '66', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('663', 'Moins-values sur réalisations d''actifs immobilisés', '66', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('664', 'Autres charges exceptionnelles', '66', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('665', 'Autres charges exceptionnelles', '66', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('666', 'Autres charges exceptionnelles', '66', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('667', 'Autres charges exceptionnelles', '66', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('668', 'Autres charges exceptionnelles', '66', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('669', ' Charges exceptionnelles portées à l''actif au titre de frais de restructuration', '66', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('67', 'impôts sur le résultat', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('670', 'Impôts belge sur le résultat de l''exercice', '67', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6700', 'Impôts et précomptes dus ou versés', '670', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6701', 'Excédents de versement d''impôts et de précomptes portés à l''actifs (-)', '670', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6702', 'Charges fiscales estimées', '670', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('671', 'Impôts belges sur le résultats d''exercices antérieures', '67', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6710', 'Suppléments d''impôt dus ou versés', '671', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('50', 'Actions propres', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('51', 'Actions et parts', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('510', 'Valeur d''acquisition', '51', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('511', 'Montant non appelés', '51', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('519', 'Réductions de valeur actées', '51', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('52', 'Titres à revenu fixe', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('520', 'Valeur d''acquisition', '52', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('529', 'Réductions de valeur actées', '52', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('53', 'Dépôts à terme', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('530', 'de plus d''un an', '53', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('531', 'de plus d''un mois et d''un an au plus', '53', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('532', 'd''un mois au plus', '53', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('539', 'Réductions de valeur actées', '53', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('54', 'Valeurs échues à l''encaissement', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('55', 'Etablissement de crédit', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('550', 'Banque 1', '55', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5500', 'Comptes courants', '550', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5501', 'Chèques émis (-)', '550', 'ACTINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5509', 'Réduction de valeur actée', '550', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5510', 'Comptes courants', '551', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5511', 'Chèques émis (-)', '551', 'ACTINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5519', 'Réduction de valeur actée', '551', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5520', 'Comptes courants', '552', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5521', 'Chèques émis (-)', '552', 'ACTINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5529', 'Réduction de valeur actée', '552', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5530', 'Comptes courants', '553', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5531', 'Chèques émis (-)', '553', 'ACTINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5539', 'Réduction de valeur actée', '553', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5540', 'Comptes courants', '554', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5541', 'Chèques émis (-)', '554', 'ACTINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5549', 'Réduction de valeur actée', '554', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5550', 'Comptes courants', '555', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5551', 'Chèques émis (-)', '555', 'ACTINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5559', 'Réduction de valeur actée', '555', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5560', 'Comptes courants', '556', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5561', 'Chèques émis (-)', '556', 'ACTINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5569', 'Réduction de valeur actée', '556', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5570', 'Comptes courants', '557', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5571', 'Chèques émis (-)', '557', 'ACTINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5579', 'Réduction de valeur actée', '557', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5580', 'Comptes courants', '558', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5581', 'Chèques émis (-)', '558', 'ACTINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5589', 'Réduction de valeur actée', '558', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5590', 'Comptes courants', '559', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5591', 'Chèques émis (-)', '559', 'ACTINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('5599', 'Réduction de valeur actée', '559', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('56', 'Office des chèques postaux', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('560', 'Compte courant', '56', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('561', 'Chèques émis', '56', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('578', 'Caisse timbre', '57', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('58', 'Virement interne', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('60', 'Approvisionnement et marchandises', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('600', 'Achats de matières premières', '60', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('601', 'Achats de fournitures', '60', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('602', 'Achats de services, travaux et études', '60', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('603', 'Sous-traitances générales', '60', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('604', 'Achats de marchandises', '60', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('605', 'Achats d''immeubles destinés à la vente', '60', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('608', 'Remises, ristournes et rabais obtenus(-)', '60', 'CHAINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('609', 'Variation de stock', '60', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6090', 'de matières premières', '609', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6091', 'de fournitures', '609', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6094', 'de marchandises', '609', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6095', 'immeubles achetés destinés à la vente', '609', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('61', 'Services et biens divers', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('62', 'Rémunérations, charges sociales et pensions', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('620', 'Rémunérations et avantages sociaux directs', '62', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6200', 'Administrateurs ou gérants', '620', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6201', 'Personnel de directions', '620', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6202', 'Employés,620', '6202', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6203', 'Ouvriers', '620', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6204', 'Autres membres du personnel', '620', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('621', 'Cotisations patronales d''assurances sociales', '62', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('622', 'Primes partonales pour assurances extra-légales', '62', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('623', 'Autres frais de personnel', '62', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('624', 'Pensions de retraite et de survie', '62', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6240', 'Administrateurs ou gérants', '624', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6241', 'Personnel', '624', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('63', 'Amortissements, réductions de valeurs et provisions pour risques et charges', '6', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('630', 'Dotations aux amortissements et réduction de valeurs sur immobilisations', '63', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6300', ' Dotations aux amortissements sur frais d''établissement', '630', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('705', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('414', 'Produits à recevoir', '41', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('416', 'Créances diverses', '41', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4160', 'Comptes de l''exploitant', '416', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('417', 'Créances douteuses', '41', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('418', 'Cautionnements versés en numéraires', '41', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('419', 'Réductions de valeur actées', '41', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('42', 'Dettes à plus dun an échéant dans l''année', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('420', 'Emprunts subordonnés', '42', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4200', 'convertibles', '420', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4201', 'non convertibles', '420', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('421', 'Emprunts subordonnés', '42', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4210', 'convertibles', '420', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4211', 'non convertibles', '420', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('422', ' Dettes de locations financement', '42', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('423', ' Etablissement de crédit', '42', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4230', 'Dettes en comptes', '423', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4231', 'Promesses', '423', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4232', 'Crédits d''acceptation', '423', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('424', 'Autres emprunts', '42', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('425', 'Dettes commerciales', '42', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4250', 'Fournisseurs', '425', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4251', 'Effets à payer', '425', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('426', 'Acomptes reçus sur commandes', '42', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('428', 'Cautionnement reçus en numéraires', '42', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('429', 'Dettes diverses', '42', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('43', 'Dettes financières', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('430', 'Etablissements de crédit - Emprunts à compte à terme fixe', '43', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('431', 'Etablissements de crédit - Promesses', '43', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('432', ' Etablissements de crédit - Crédits d''acceptation', '43', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('433', 'Etablissements de crédit -Dettes en comptes courant', '43', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('439', 'Autres emprunts', '43', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('44', 'Dettes commerciales', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('440', 'Fournisseurs', '44', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('441', 'Effets à payer', '44', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('444', 'Factures à recevoir', '44', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('45', 'Dettes fiscales, salariales et sociales', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('450', 'Dettes fiscales estimées', '45', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4500', 'Impôts belges sur le résultat', '450', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4501', 'Impôts belges sur le résultat', '450', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4502', 'Impôts belges sur le résultat', '450', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4503', 'Impôts belges sur le résultat', '450', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4504', 'Impôts belges sur le résultat', '450', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4505', 'Autres impôts et taxes belges', '450', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4506', 'Autres impôts et taxes belges', '450', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4507', 'Autres impôts et taxes belges', '450', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4508', 'Impôts et taxes étrangers', '450', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('451', 'TVA à payer', '45', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4511', 'TVA à payer 21%', '451', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4512', 'TVA à payer 12%', '451', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4513', 'TVA à payer 6%', '451', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4514', 'TVA à payer 0%', '451', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('452', 'Impôts et taxes à payer', '45', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4520', 'Impôts belges sur le résultat', '452', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4521', 'Impôts belges sur le résultat', '452', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4522', 'Impôts belges sur le résultat', '452', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4523', 'Impôts belges sur le résultat', '452', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4524', 'Impôts belges sur le résultat', '452', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4525', 'Autres impôts et taxes belges', '452', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4526', 'Autres impôts et taxes belges', '452', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4527', 'Autres impôts et taxes belges', '452', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4528', 'Impôts et taxes étrangers', '452', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('453', 'Précomptes retenus', '45', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('454', 'Office National de la Sécurité Sociales', '45', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('455', 'Rémunérations', '45', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('456', 'Pécules de vacances', '45', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('459', 'Autres dettes sociales', '45', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('46', 'Acomptes reçus sur commandes', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('47', 'Dettes découlant de l''affectation du résultat', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('470', 'Dividendes et tantièmes d''exercices antérieurs', '47', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('471', 'Dividendes de l''exercice', '47', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('472', 'Tantièmes de l''exercice', '47', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('473', 'Autres allocataires', '47', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('48', 'Dettes diverses', '4', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('480', 'Obligations et coupons échus', '48', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('488', 'Cautionnements reçus en numéraires', '48', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('489', 'Autres dettes diverses', '48', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4890', 'Compte de l''exploitant', '489', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('49', 'Comptes de régularisation', '4', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('490', 'Charges à reporter', '49', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('491', 'Produits acquis', '49', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('492', 'Charges à imputer', '49', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('493', 'Produits à reporter', '49', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('499', 'Comptes d''attentes', '49', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2821', 'Montants non-appelés(-)', '282', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2828', 'Plus-values actées', '282', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2829', 'Réductions de valeurs actées', '282', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('283', 'Créances sur des entreprises avec lesquelles existe un lien de participation', '28', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2830', 'Créance en compte', '283', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2831', 'Effets à recevoir', '283', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('57', 'Caisse', '5', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2832', 'Titre à revenu fixe', '283', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2837', 'Créances douteuses', '283', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2839', 'Réduction de valeurs actées', '283', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('284', 'Autres actions et parts', '28', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2840', 'Valeur d''acquisition', '284', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2841', 'Montants non-appelés(-)', '284', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2848', 'Plus-values actées', '284', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2849', 'Réductions de valeurs actées', '284', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('285', 'Autres créances', '28', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2850', 'Créance en compte', '285', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2851', 'Effets à recevoir', '285', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2852', 'Titre à revenu fixe', '285', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2857', 'Créances douteuses', '285', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2859', 'Réductions de valeurs actées', '285', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('288', 'Cautionnements versés en numéraires', '28', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('29', 'Créances à plus d''un an', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('290', 'Créances commerciales', '29', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2900', 'Clients', '290', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2901', 'Effets à recevoir', '290', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2906', 'Acomptes versés', '290', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2907', 'Créances douteuses', '290', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2909', 'Réductions de valeurs actées', '290', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('291', 'Autres créances', '29', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2910', 'Créances en comptes', '291', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2911', 'Effets à recevoir', '291', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2917', 'Créances douteuses', '291', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2919', 'Réductions de valeurs actées(-)', '291', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('30', 'Approvisionements - Matières premières', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('300', 'Valeur d''acquisition', '30', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('309', 'Réductions de valeur actées', '30', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('31', 'Approvisionnements - fournitures', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('310', 'Valeur d''acquisition', '31', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('319', 'Réductions de valeurs actées(-)', '31', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('32', 'En-cours de fabrication', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('320', 'Valeurs d''acquisition', '32', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('329', 'Réductions de valeur actées', '32', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('33', 'Produits finis', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('330', 'Valeur d''acquisition', '33', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('339', 'Réductions de valeur actées', '33', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('34', 'Marchandises', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('340', 'Valeur d''acquisition', '34', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('349', 'Réductions de valeur actées', '34', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('35', 'Immeubles destinés à la vente', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('350', 'Valeur d''acquisition', '35', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('359', 'Réductions de valeur actées', '35', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('36', 'Acomptes versés sur achats pour stocks', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('360', 'Valeur d''acquisition', '36', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('369', 'Réductions de valeur actées', '36', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('37', 'Commandes en cours éxécution', '3', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('370', 'Valeur d''acquisition', '37', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('371', 'Bénéfice pris en compte ', '37', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('379', 'Réductions de valeur actées', '37', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('40', 'Créances commerciales', '4', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('400', 'Clients', '40', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('401', 'Effets à recevoir', '40', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('404', 'Produits à recevoir', '40', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('406', 'Acomptes versés', '40', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('407', 'Créances douteuses', '40', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('409', 'Réductions de valeur actées', '40', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('41', 'Autres créances', '4', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('410', 'Capital appelé non versé', '41', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('411', 'TVA à récupérer', '41', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4111', 'TVA à récupérer 21%', '411', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4112', 'TVA à récupérer 12%', '411', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4113', 'TVA à récupérer 6% ', '411', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4114', 'TVA à récupérer 0%', '411', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('412', 'Impôts et précomptes à récupérer', '41', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4120', 'Impôt belge sur le résultat', '412', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4121', 'Impôt belge sur le résultat', '412', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4122', 'Impôt belge sur le résultat', '412', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4123', 'Impôt belge sur le résultat', '412', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4124', 'Impôt belge sur le résultat', '412', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4125', 'Autres impôts et taxes belges', '412', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4126', 'Autres impôts et taxes belges', '412', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4127', 'Autres impôts et taxes belges', '412', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4128', 'Impôts et taxes étrangers', '412', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('10', 'Capital ', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6040003', 'Petit matériel', '604', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('11', 'Prime d''émission ', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('12', 'Plus Value de réévaluation ', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('13', 'Réserve ', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('130', 'Réserve légale', '13', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('131', 'Réserve indisponible', '13', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1310', 'Réserve pour actions propres', '131', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('6040004', 'Assurance', '604', 'CHA');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('55000001', 'Caisse', '5500', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('133', 'Réserves disponibles', '13', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('14', 'Bénéfice ou perte reportée', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('140', 'Bénéfice reporté', '14', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('141', 'Perte reportée', '14', 'PASINV');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('15', 'Subside en capital', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('16', 'Provisions pour risques et charges', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('160', 'Provisions pour pensions et obligations similaires', '16', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('161', 'Provisions pour charges fiscales', '16', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('749', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('162', 'Provisions pour grosses réparation et gros entretien', '16', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('17', ' Dettes à plus d''un an', '1', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('170', 'Emprunts subordonnés', '17', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1700', 'convertibles', '170', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1701', 'non convertibles', '170', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('171', 'Emprunts subordonnés', '17', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1710', 'convertibles', '170', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1711', 'non convertibles', '170', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('172', ' Dettes de locations financement', '17', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('173', ' Etablissement de crédit', '17', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1730', 'Dettes en comptes', '173', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1731', 'Promesses', '173', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1732', 'Crédits d''acceptation', '173', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('174', 'Autres emprunts', '17', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('175', 'Dettes commerciales', '17', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1750', 'Fournisseurs', '175', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('1751', 'Effets à payer', '175', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('176', 'Acomptes reçus sur commandes', '17', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('178', 'Cautionnement reçus en numéraires', '17', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('179', 'Dettes diverses', '17', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('20', 'Frais d''établissement', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('200', 'Frais de constitution et d''augmentation de capital', '20', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('201', ' Frais d''émission d''emprunts et primes de remboursement', '20', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('202', 'Autres frais d''établissement', '20', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('204', 'Frais de restructuration', '20', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('21', 'Immobilisations incorporelles', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('210', 'Frais de recherche et de développement', '21', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('211', 'Concessions, brevet, licence savoir faire, marque et droit similaires', '21', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('212', 'Goodwill', '21', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('213', 'Acomptes versés', '21', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('22', 'Terrains et construction', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('220', 'Terrains', '22', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('222', 'Terrains bâtis', '22', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('223', 'Autres droits réels sur des immeubles', '22', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('23', ' Installations, machines et outillages', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('24', 'Mobilier et Matériel roulant', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('25', 'Immobilisations détenus en location-financement et droits similaires', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('250', 'Terrains', '25', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('251', 'Construction', '25', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('252', 'Terrains bâtis', '25', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('253', 'Mobilier et matériels roulants', '25', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('26', 'Autres immobilisations corporelles', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('27', 'Immobilisations corporelles en cours et acomptes versés', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('28', 'Immobilisations financières', '2', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('280', 'Participation dans des entreprises liées', '28', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2800', 'Valeur d''acquisition', '280', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2801', 'Montants non-appelés(-)', '280', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2808', 'Plus-values actées', '280', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2809', 'Réductions de valeurs actées', '280', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('281', 'Créance sur  des entreprises liées', '28', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2810', 'Créance en compte', '281', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2811', 'Effets à recevoir', '281', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2812', 'Titre à reveny fixe', '281', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2817', 'Créances douteuses', '281', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2819', 'Réduction de valeurs actées', '281', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('282', 'Participations dans des entreprises avec lesquelles il existe un lien de participation', '28', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('2820', 'Valeur d''acquisition', '282', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4516', 'Tva Export 0%', '451', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4115', 'Tva Intracomm 0%', '411', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('4116', 'Tva Export 0%', '411', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('41141', 'TVA pour l\\''export', '4114', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('41142', 'TVA sur les opérations intracommunautaires', '4114', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('45141', 'TVA pour l\\''export', '451', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('45142', 'TVA sur les opérations intracommunautaires', '4514', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('41143', 'TVA sur les opérations avec des assujettis art 44 Code TVA', '4114', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('45143', 'TVA sur les opérations avec des assujettis art 44 Code TVA', '4514', 'PAS');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('41144', 'TVA sur les opérations avec des cocontractants', '4114', 'ACT');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type) VALUES ('45144', 'TVA sur les opérations avec des cocontractants', '4514', 'PAS');






INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (1, '21%', 0.2100, 'Tva applicable à tout ce qui bien et service divers', '4111,4511');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (2, '12%', 0.1200, 'Tva ', '4112,4512');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (3, '6%', 0.0600, 'Tva applicable aux journaux et livres', '4113,4513');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (4, '0%', 0.0000, 'Aucune tva n''est applicable', '4114,4514');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (6, 'EXPORT', 0.0000, 'Tva pour les exportations', '41141,45144');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (5, 'INTRA', 0.0000, 'Tva pour les livraisons / acquisition intra communautaires', '41142,45142');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (7, 'COC', 0.0000, 'Opérations avec des cocontractants', '41144,45144');
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste) VALUES (8, 'ART44', 0.0000, 'Opérations pour les opérations avec des assujettis à l\\''art 44 Code TVA', '41143,45143');



INSERT INTO user_local_pref (user_id, parameter_type, parameter_value) VALUES ('1', 'MINIREPORT', '0');
INSERT INTO user_local_pref (user_id, parameter_type, parameter_value) VALUES ('1', 'PERIODE', '79');






INSERT INTO user_sec_extension (use_id, ex_id, use_login, use_access) VALUES (1, 1, 'phpcompta', 'Y');






INSERT INTO version (val) VALUES (75);



