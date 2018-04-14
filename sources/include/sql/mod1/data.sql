set search_path = public,comptaproc,pg_catalog ;

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;



INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (800, 'Ajout de fiche', 'fiche', 'FICADD');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (805, 'Création, modification et effacement de fiche', 'fiche', 'FIC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (910, 'création, modification et effacement de catégorie de fiche', 'fiche', 'FICCAT');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1020, 'Effacer les documents du suivi', 'followup', 'RMDOC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1010, 'Voir les documents du suivi', 'followup', 'VIEWDOC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1050, 'Modifier le type de document', 'followup', 'PARCATDOC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1110, 'Enlever une pièce justificative', 'compta', 'RMRECEIPT');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1120, 'Effacer une opération ', 'compta', 'RMOPER');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1210, 'Partager une note', 'note', 'SHARENOTE');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1220, 'Créer une note publique', 'note', 'SHARENOTEPUBLIC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1230, 'Effacer une note publique', 'note', 'SHARENOTEREMOVE');



INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (1, 'Document Interne', 'DOCUME1');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (2, 'Bons de commande client', 'BONSDE2');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (3, 'Bon de commande Fournisseur', 'BONDEC3');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (4, 'Facture', 'FACTUR4');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (5, 'Lettre de rappel', 'LETTRE5');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (6, 'Courrier', 'COURRI6');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (7, 'Proposition', 'PROPOS7');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (8, 'Email', 'EMAIL8');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (9, 'Divers', 'DIVERS9');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (10, 'Note de frais', 'NOTEDE10');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (20, 'Réception commande Fournisseur', 'RÉCEPT20');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (21, 'Réception commande Client', 'RÉCEPT21');
INSERT INTO document_type (dt_id, dt_value, dt_prefix) VALUES (22, 'Réception magazine', 'RÉCEPT22');



INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (1, 'Vente Service', '700');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (2, 'Achat Marchandises', '604');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (3, 'Achat Service et biens divers', '61');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (5, 'Prêt > a un an', '17');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (6, 'Prêt < a un an', '430');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (8, 'Fournisseurs', '440');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (9, 'Clients', '400');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (10, 'Salaire Administrateur', '6200');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (11, 'Salaire Ouvrier', '6203');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (12, 'Salaire Employé', '6202');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (13, 'Dépenses non admises', '674');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (14, 'Administration des Finances', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (15, 'Autres fiches', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (7, 'Matériel à amortir', '2400');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (16, 'Contact', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (25, 'Compte Salarié / Administrateur', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (4, 'Trésorerie', '5500');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (26, 'Projet', NULL);



INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (500000, NULL, 'Stock', false, 15, NULL);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (1, '604', 'Marchandises', true, 2, 'Achats de marchandises');
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (2, '400', 'Client', true, 9, 'Catégorie qui contient la liste des clients');
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (3, '5500', 'Banque', true, 4, 'Catégorie qui contient la liste des comptes financiers: banque, caisse,...');
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (4, '440', 'Fournisseur', true, 8, 'Catégorie qui contient la liste des fournisseurs');
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (5, '61', 'Services & Biens Divers', true, 3, 'Catégorie qui contient la liste des charges diverses');
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (6, '700', 'Vente', true, 1, 'Catégorie qui contient la liste des prestations, marchandises... que l''on vend ');






INSERT INTO profile (p_name, p_id, p_desc, with_calc, with_direct_form) VALUES ('Administrateur', 1, 'Profil par défaut pour les adminstrateurs', true, true);
INSERT INTO profile (p_name, p_id, p_desc, with_calc, with_direct_form) VALUES ('Utilisateur', 2, 'Profil par défaut pour les utilisateurs', true, true);
INSERT INTO profile (p_name, p_id, p_desc, with_calc, with_direct_form) VALUES ('Public', -1, 'faux groupe', NULL, NULL);









SELECT pg_catalog.setval('action_detail_ad_id_seq', 1, false);



SELECT pg_catalog.setval('action_gestion_ag_id_seq', 1, false);






SELECT pg_catalog.setval('action_gestion_comment_agc_id_seq', 1, false);



INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('FIN', 'Financier');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('VEN', 'Vente');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('ACH', 'Achat');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('ODS', 'Opérations Diverses');



INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref, jrn_def_bank, jrn_def_num_op, jrn_def_description, jrn_enable) VALUES (3, 'Achat', '6*', '4*', '5', '4', 1, 3, true, 'échéance', 'ACH', 'A01', 'ACH', NULL, NULL, 'Concerne tous les achats, factures reçues, notes de crédit reçues et notes de frais', 1);
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref, jrn_def_bank, jrn_def_num_op, jrn_def_description, jrn_enable) VALUES (1, 'Financier', '5* ', '5*', '3,2,4', '3,2,4', 5, 5, false, NULL, 'FIN', 'F01', 'FIN', NULL, NULL, 'Concerne tous les mouvements financiers (comptes en banque, caisses, visa...)', 1);
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref, jrn_def_bank, jrn_def_num_op, jrn_def_description, jrn_enable) VALUES (4, 'Opération Diverses', NULL, NULL, NULL, NULL, 5, 5, false, NULL, 'ODS', 'O01', 'ODS', NULL, NULL, 'Concerne toutes les opérations comme les amortissements, les comptes TVA, ...', 1);
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref, jrn_def_bank, jrn_def_num_op, jrn_def_description, jrn_enable) VALUES (2, 'Vente', '4*', '7*', '2', '6', 2, 1, true, 'échéance', 'VEN', 'V01', 'VEN', NULL, NULL, 'Concerne toutes les ventes, notes de crédit envoyées', 1);









SELECT pg_catalog.setval('action_gestion_operation_ago_id_seq', 1, false);









SELECT pg_catalog.setval('action_gestion_related_aga_id_seq', 1, false);






SELECT pg_catalog.setval('action_person_ap_id_seq', 1, false);









SELECT pg_catalog.setval('action_tags_at_id_seq', 1, false);



INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (8, 'Durée Amortissement', 'numeric', '6', '2');
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (11, 'Montant initial', 'numeric', '6', '2');
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (21, 'TVA non déductible', 'numeric', '6', '2');
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (22, 'TVA non déductible récupérable par l''impôt', 'numeric', '6', '2');
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (20, 'Partie fiscalement non déductible', 'numeric', '6', '2');
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (31, 'Dépense  charge du grant (partie privé) ', 'numeric', '6', '4');
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (3, 'Compte bancaire', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (6, 'Prix vente', 'numeric', '6', '4');
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (7, 'Prix achat', 'numeric', '6', '4');
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (10, 'Date début', 'date', '8', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (1, 'Nom', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (2, 'Taux TVA', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (4, 'Nom de la banque', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (9, 'Description', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (12, 'Personne de contact ', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (13, 'numéro de tva ', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (14, 'Adresse ', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (16, 'pays ', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (17, 'téléphone ', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (18, 'email ', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (23, 'Quick Code', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (24, 'Ville', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (26, 'Fax', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (27, 'GSM', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (15, 'code postal', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (30, 'Numero de client', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (32, 'Prénom', 'text', '22', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (50, 'Contrepartie pour TVA récup par impot', 'poste', '17', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (51, 'Contrepartie pour TVA non Ded.', 'poste', '17', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (52, 'Contrepartie pour dépense à charge du gérant', 'poste', '17', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (53, 'Contrepartie pour dépense fiscal. non déd.', 'poste', '17', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (5, 'Poste Comptable', 'poste', '17', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (33, 'Date Fin', 'date', '8', NULL);
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (19, 'Gestion stock', 'card', '22', '[sql] fd_id = 500000 ');
INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (25, 'Société', 'card', '22', '[sql] frd_id in (4,8,9,14)');



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
INSERT INTO attr_min (frd_id, ad_id) VALUES (26, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (26, 9);



INSERT INTO bilan (b_id, b_name, b_file_template, b_file_form, b_type) VALUES (1, 'Bilan Belge complet', 'document/fr_be/bnb.rtf', 'document/fr_be/bnb.form', 'RTF');
INSERT INTO bilan (b_id, b_name, b_file_template, b_file_form, b_type) VALUES (9, 'ASBL', 'document/fr_be/bnb-asbl.rtf', 'document/fr_be/bnb-asbl.form', 'RTF');



SELECT pg_catalog.setval('bilan_b_id_seq', 9, true);






SELECT pg_catalog.setval('bookmark_b_id_seq', 1, false);



SELECT pg_catalog.setval('bud_card_bc_id_seq', 1, false);



SELECT pg_catalog.setval('bud_detail_bd_id_seq', 1, false);



SELECT pg_catalog.setval('bud_detail_periode_bdp_id_seq', 1, false);



INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('550', 'Banque 1', '55', 'ACT', 715, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('440', 'Fournisseurs', '44', 'PAS', 813, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('539', 'Réductions de valeur actées', '53', 'ACT', 518, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('794', 'Intervention d''associés (ou du propriétaire) dans la perte', '79', 'PRO', 519, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4000001', 'Client 1', '400', 'ACT', 527, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4000002', 'Client 2', '400', 'ACT', 528, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4000003', 'Client 3', '400', 'ACT', 529, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6040001', 'Electricité', '604', 'CHA', 530, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6040002', 'Loyer', '604', 'CHA', 531, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('55000002', 'Banque 1', '5500', 'ACT', 532, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('55000003', 'Banque 2', '5500', 'ACT', 533, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4400001', 'Fournisseur 1', '440', 'PAS', 534, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4400002', 'Fournisseur 2', '440', 'PAS', 535, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4400003', 'Fournisseur 4', '440', 'PAS', 536, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('610001', 'Electricité', '61', 'CHA', 537, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('610002', 'Loyer', '61', 'CHA', 538, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('610003', 'Assurance', '61', 'CHA', 539, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('610004', 'Matériel bureau', '61', 'CHA', 540, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7000002', 'Marchandise A', '700', 'PRO', 541, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7000001', 'Prestation', '700', 'PRO', 542, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7000003', 'Déplacement', '700', 'PRO', 543, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('101', 'Capital non appelé', '10', 'PASINV', 544, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6190', 'TVA récupérable par l''impôt', '61', 'CHA', 545, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6740', 'Dépense non admise', '67', 'CHA', 546, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('100', 'Capital souscrit', '10', 'PAS', 548, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1311', 'Autres réserves indisponibles', '131', 'PAS', 549, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('132', ' Réserves immunisées', '13', 'PAS', 550, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6711', 'Suppléments d''impôts estimés', '671', 'CHA', 551, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6712', 'Provisions fiscales constituées', '671', 'CHA', 552, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('672', 'Impôts étrangers sur le résultat de l''exercice', '67', 'CHA', 553, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('673', 'Impôts étrangers sur le résultat d''exercice antérieures', '67', 'CHA', 554, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('690', 'Perte reportée de l''exercice précédent', '69', 'CHA', 557, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('691', 'Dotation à la réserve légale', '69', 'CHA', 558, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('692', 'Dotation aux autres réserves', '69', 'CHA', 559, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('693', 'Bénéfice à reporter', '69', 'CHA', 560, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('694', 'Rémunération du capital', '69', 'CHA', 561, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('695', 'Administrateurs ou gérants', '69', 'CHA', 562, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('696', 'Autres allocataires', '69', 'CHA', 563, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('700', 'Ventes et prestations de services', '70', 'PRO', 565, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('701', 'Ventes et prestations de services', '70', 'PRO', 566, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('702', 'Ventes et prestations de services', '70', 'PRO', 567, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('703', 'Ventes et prestations de services', '70', 'PRO', 568, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('704', 'Ventes et prestations de services', '70', 'PRO', 569, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('706', 'Ventes et prestations de services', '70', 'PRO', 570, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('707', 'Ventes et prestations de services', '70', 'PRO', 571, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('709', 'Remises, ristournes et rabais accordés(-)', '70', 'PROINV', 572, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('712', 'des en-cours de fabrication', '71', 'PRO', 574, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('713', 'des produits finis', '71', 'PRO', 575, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('715', 'des immeubles construits destinés à la vente', '71', 'PRO', 576, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('717', ' des commandes  en cours d''éxécution', '71', 'PRO', 577, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7170', 'Valeur d''acquisition', '717', 'PRO', 578, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7171', 'Bénéfice pris en compte', '717', 'PRO', 579, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('740', 'Subsides d'' exploitation  et montants compensatoires', '74', 'PRO', 582, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('741', 'Plus-values sur réalisation courantes d'' immobilisations corporelles', '74', 'PRO', 583, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('742', 'Plus-values sur réalisations de créances commerciales', '74', 'PRO', 584, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('743', 'Produits d''exploitations divers', '74', 'PRO', 585, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('744', 'Produits d''exploitations divers', '74', 'PRO', 586, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('745', 'Produits d''exploitations divers', '74', 'PRO', 587, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('746', 'Produits d''exploitations divers', '74', 'PRO', 588, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('747', 'Produits d''exploitations divers', '74', 'PRO', 589, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('748', 'Produits d''exploitations divers', '74', 'PRO', 590, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('750', 'Produits sur immobilisations financières', '75', 'PRO', 592, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('751', 'Produits des actifs circulants', '75', 'PRO', 593, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('752', 'Plus-value sur réalisations d''actis circulants', '75', 'PRO', 594, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('753', 'Subsides en capital et intérêts', '75', 'PRO', 595, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('754', 'Différences de change', '75', 'PRO', 596, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('755', 'Ecarts de conversion des devises', '75', 'PRO', 597, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('221', 'Construction', '22', 'ACT', 598, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('756', 'Produits financiers divers', '75', 'PRO', 599, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('757', 'Produits financiers divers', '75', 'PRO', 600, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('758', 'Produits financiers divers', '75', 'PRO', 601, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('759', 'Produits financiers divers', '75', 'PRO', 602, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('760', 'Reprise d''amortissements et de réductions de valeur', '76', 'PRO', 604, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7601', 'sur immobilisations corporelles', '760', 'PRO', 605, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7602', 'sur immobilisations incorporelles', '760', 'PRO', 606, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('761', 'Reprises de réductions de valeur sur immobilisations financières', '76', 'PRO', 607, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('762', 'Reprises de provisions pour risques et charges exceptionnels', '76', 'PRO', 608, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('763', 'Plus-value sur réalisation d''actifs immobilisé', '76', 'PRO', 609, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('764', 'Autres produits exceptionnels', '76', 'PRO', 610, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('765', 'Autres produits exceptionnels', '76', 'PRO', 611, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('766', 'Autres produits exceptionnels', '76', 'PRO', 612, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('767', 'Autres produits exceptionnels', '76', 'PRO', 613, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('768', 'Autres produits exceptionnels', '76', 'PRO', 614, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('769', 'Autres produits exceptionnels', '76', 'PRO', 615, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('771', 'impôts belges sur le résultat', '77', 'PRO', 617, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7710', 'Régularisations d''impôts dus ou versé', '771', 'PRO', 618, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7711', 'Régularisations d''impôts estimés', '771', 'PRO', 619, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7712', 'Reprises de provisions fiscales', '771', 'PRO', 620, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('773', 'Impôts étrangers sur le résultats', '77', 'PRO', 621, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('790', 'Bénéfice reporté de l''exercice précédent', '79', 'PRO', 623, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('791', 'Prélévement sur le capital et les primes d''émission', '79', 'PRO', 624, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('792', 'Prélévement sur les réserves', '79', 'PRO', 625, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('793', 'Perte à reporter', '79', 'PRO', 626, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6301', 'Dotations aux amortissements sur immobilisations incorporelles', '630', 'CHA', 627, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6302', 'Dotations aux amortissements sur immobilisations corporelles', '630', 'CHA', 628, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6308', 'Dotations aux réductions de valeur sur immobilisations incorporelles', '630', 'CHA', 629, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6309', 'Dotations aux réductions de valeur sur immobilisations corporelles', '630', 'CHA', 630, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('631', 'Réductions de valeur sur stocks', '63', 'CHA', 631, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6310', 'Dotations', '631', 'CHA', 632, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6311', 'Reprises(-)', '631', 'CHAINV', 633, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('632', 'Réductions de valeur sur commande en cours d''éxécution', '63', 'CHA', 634, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6320', 'Dotations', '632', 'CHA', 635, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6321', 'Reprises(-)', '632', 'CHAINV', 636, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('633', 'Réductions de valeurs sur créances commerciales à plus d''un an', '63', 'CHA', 637, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6330', 'Dotations', '633', 'CHA', 638, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6331', 'Reprises(-)', '633', 'CHAINV', 639, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('634', 'Réductions de valeur sur créances commerciales à un an au plus', '63', 'CHA', 640, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6340', 'Dotations', '634', 'CHA', 641, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6341', 'Reprise', '634', 'CHAINV', 642, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('635', 'Provisions pour pensions et obligations similaires', '63', 'CHA', 643, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6350', 'Dotations', '635', 'CHA', 644, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6351', 'Utilisation et reprises', '635', 'CHAINV', 645, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('636', 'Provisions pour grosses réparations et gros entretien', '63', 'CHA', 646, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6360', 'Dotations', '636', 'CHA', 647, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6361', 'Reprises(-)', '636', 'CHAINV', 648, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('637', 'Provisions pour autres risques et charges', '63', 'CHA', 649, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6370', 'Dotations', '637', 'CHA', 650, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6371', 'Reprises(-)', '637', 'CHAINV', 651, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('640', 'Charges fiscales d''exploitation', '64', 'CHA', 653, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('641', 'Moins-values sur réalisations courantes d''immobilisations corporelles', '64', 'CHA', 654, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('642', 'Moins-value sur réalisation de créances commerciales', '64', 'CHA', 655, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('643', 'Charges d''exploitations', '64', 'CHA', 656, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('644', 'Charges d''exploitations', '64', 'CHA', 657, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('645', 'Charges d''exploitations', '64', 'CHA', 658, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('646', 'Charges d''exploitations', '64', 'CHA', 659, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('647', 'Charges d''exploitations', '64', 'CHA', 660, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('648', 'Charges d''exploitations', '64', 'CHA', 661, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('649', 'Charges d''exploitation portées à l''actif au titre de frais de restructuration(-)', '64', 'CHAINV', 662, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('650', 'Charges des dettes', '65', 'CHA', 664, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6500', 'Intérêts, commmissions et frais afférents aux dettes', '650', 'CHA', 665, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6501', 'Amortissements des frais d''émissions d''emrunts et des primes de remboursement', '650', 'CHA', 666, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6502', 'Autres charges des dettes', '650', 'CHA', 667, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6503', 'Intérêts intercalaires portés à l''actif(-)', '650', 'CHA', 668, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('651', 'Réductions de valeur sur actifs circulants', '65', 'CHA', 669, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6510', 'Dotations', '651', 'CHA', 670, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6511', 'Reprises(-)', '651', 'CHAINV', 671, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('652', 'Moins-value sur réalisation d''actifs circulants', '65', 'CHA', 672, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('653', 'Charges d''escompte de créances', '65', 'CHA', 673, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('654', 'Différences de changes', '65', 'CHA', 674, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('655', 'Ecarts de conversion des devises', '65', 'CHA', 675, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('656', 'Charges financières diverses', '65', 'CHA', 676, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('657', 'Charges financières diverses', '65', 'CHA', 677, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('658', 'Charges financières diverses', '65', 'CHA', 678, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('659', 'Charges financières diverses', '65', 'CHA', 679, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('660', 'Amortissements et réductions de valeur exceptionnels (dotations)', '66', 'CHA', 681, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6600', 'sur frais d''établissement', '660', 'CHA', 682, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6601', 'sur immobilisations incorporelles', '660', 'CHA', 683, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6602', 'sur immobilisations corporelles', '660', 'CHA', 684, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('661', 'Réductions de valeur sur immobilisations financières (dotations)', '66', 'CHA', 685, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('662', 'Provisions pour risques et charges exceptionnels', '66', 'CHA', 686, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('663', 'Moins-values sur réalisations d''actifs immobilisés', '66', 'CHA', 687, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('664', 'Autres charges exceptionnelles', '66', 'CHA', 688, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('665', 'Autres charges exceptionnelles', '66', 'CHA', 689, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('666', 'Autres charges exceptionnelles', '66', 'CHA', 690, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('667', 'Autres charges exceptionnelles', '66', 'CHA', 691, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('668', 'Autres charges exceptionnelles', '66', 'CHA', 692, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('669', ' Charges exceptionnelles portées à l''actif au titre de frais de restructuration', '66', 'CHA', 693, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('670', 'Impôts belge sur le résultat de l''exercice', '67', 'CHA', 695, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6700', 'Impôts et précomptes dus ou versés', '670', 'CHA', 696, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6701', 'Excédents de versement d''impôts et de précomptes portés à l''actifs (-)', '670', 'CHAINV', 697, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6702', 'Charges fiscales estimées', '670', 'CHA', 698, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('671', 'Impôts belges sur le résultats d''exercices antérieures', '67', 'CHA', 699, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6710', 'Suppléments d''impôt dus ou versés', '671', 'CHA', 700, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('510', 'Valeur d''acquisition', '51', 'ACT', 703, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('511', 'Montant non appelés', '51', 'ACT', 704, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('519', 'Réductions de valeur actées', '51', 'ACT', 705, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('520', 'Valeur d''acquisition', '52', 'ACT', 707, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('529', 'Réductions de valeur actées', '52', 'ACT', 708, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('530', 'de plus d''un an', '53', 'ACT', 710, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('531', 'de plus d''un mois et d''un an au plus', '53', 'ACT', 711, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('532', 'd''un mois au plus', '53', 'ACT', 712, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5500', 'Comptes courants', '550', 'ACT', 716, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5501', 'Chèques émis (-)', '550', 'ACTINV', 717, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5509', 'Réduction de valeur actée', '550', 'ACT', 718, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5510', 'Comptes courants', '551', 'ACT', 719, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5511', 'Chèques émis (-)', '551', 'ACTINV', 720, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5519', 'Réduction de valeur actée', '551', 'ACT', 721, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5520', 'Comptes courants', '552', 'ACT', 722, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5521', 'Chèques émis (-)', '552', 'ACTINV', 723, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5529', 'Réduction de valeur actée', '552', 'ACT', 724, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5530', 'Comptes courants', '553', 'ACT', 725, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5531', 'Chèques émis (-)', '553', 'ACTINV', 726, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5539', 'Réduction de valeur actée', '553', 'ACT', 727, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5540', 'Comptes courants', '554', 'ACT', 728, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5541', 'Chèques émis (-)', '554', 'ACTINV', 729, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5549', 'Réduction de valeur actée', '554', 'ACT', 730, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5550', 'Comptes courants', '555', 'ACT', 731, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5551', 'Chèques émis (-)', '555', 'ACTINV', 732, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5559', 'Réduction de valeur actée', '555', 'ACT', 733, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5560', 'Comptes courants', '556', 'ACT', 734, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5561', 'Chèques émis (-)', '556', 'ACTINV', 735, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5569', 'Réduction de valeur actée', '556', 'ACT', 736, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5570', 'Comptes courants', '557', 'ACT', 737, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5571', 'Chèques émis (-)', '557', 'ACTINV', 738, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5579', 'Réduction de valeur actée', '557', 'ACT', 739, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5580', 'Comptes courants', '558', 'ACT', 740, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5581', 'Chèques émis (-)', '558', 'ACTINV', 741, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5589', 'Réduction de valeur actée', '558', 'ACT', 742, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5590', 'Comptes courants', '559', 'ACT', 743, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5591', 'Chèques émis (-)', '559', 'ACTINV', 744, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5599', 'Réduction de valeur actée', '559', 'ACT', 745, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('560', 'Compte courant', '56', 'ACT', 747, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('561', 'Chèques émis', '56', 'ACT', 748, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('578', 'Caisse timbre', '57', 'ACT', 749, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('600', 'Achats de matières premières', '60', 'CHA', 752, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('601', 'Achats de fournitures', '60', 'CHA', 753, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('602', 'Achats de services, travaux et études', '60', 'CHA', 754, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('603', 'Sous-traitances générales', '60', 'CHA', 755, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('604', 'Achats de marchandises', '60', 'CHA', 756, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('605', 'Achats d''immeubles destinés à la vente', '60', 'CHA', 757, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('608', 'Remises, ristournes et rabais obtenus(-)', '60', 'CHAINV', 758, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('609', 'Variation de stock', '60', 'CHA', 759, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6090', 'de matières premières', '609', 'CHA', 760, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6091', 'de fournitures', '609', 'CHA', 761, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6094', 'de marchandises', '609', 'CHA', 762, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6095', 'immeubles achetés destinés à la vente', '609', 'CHA', 763, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('620', 'Rémunérations et avantages sociaux directs', '62', 'CHA', 766, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6200', 'Administrateurs ou gérants', '620', 'CHA', 767, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6201', 'Personnel de directions', '620', 'CHA', 768, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6203', 'Ouvriers', '620', 'CHA', 769, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6204', 'Autres membres du personnel', '620', 'CHA', 770, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('621', 'Cotisations patronales d''assurances sociales', '62', 'CHA', 771, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('622', 'Primes partonales pour assurances extra-légales', '62', 'CHA', 772, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('623', 'Autres frais de personnel', '62', 'CHA', 773, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('624', 'Pensions de retraite et de survie', '62', 'CHA', 774, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6240', 'Administrateurs ou gérants', '624', 'CHA', 775, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6241', 'Personnel', '624', 'CHA', 776, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('630', 'Dotations aux amortissements et réduction de valeurs sur immobilisations', '63', 'CHA', 778, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6300', ' Dotations aux amortissements sur frais d''établissement', '630', 'CHA', 779, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('705', 'Ventes et prestations de services', '70', 'PRO', 780, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('414', 'Produits à recevoir', '41', 'ACT', 781, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('416', 'Créances diverses', '41', 'ACT', 782, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4160', 'Comptes de l''exploitant', '416', 'ACT', 783, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('417', 'Créances douteuses', '41', 'ACT', 784, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('418', 'Cautionnements versés en numéraires', '41', 'ACT', 785, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('419', 'Réductions de valeur actées', '41', 'ACT', 786, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('420', 'Emprunts subordonnés', '42', 'PAS', 788, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4200', 'convertibles', '420', 'PAS', 789, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4201', 'non convertibles', '420', 'PAS', 790, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('421', 'Emprunts subordonnés', '42', 'PAS', 791, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4210', 'convertibles', '420', 'PAS', 792, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4211', 'non convertibles', '420', 'PAS', 793, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('422', ' Dettes de locations financement', '42', 'PAS', 794, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('423', ' Etablissement de crédit', '42', 'PAS', 795, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4230', 'Dettes en comptes', '423', 'PAS', 796, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4231', 'Promesses', '423', 'PAS', 797, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4232', 'Crédits d''acceptation', '423', 'PAS', 798, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('424', 'Autres emprunts', '42', 'PAS', 799, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('425', 'Dettes commerciales', '42', 'PAS', 800, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4250', 'Fournisseurs', '425', 'PAS', 801, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4251', 'Effets à payer', '425', 'PAS', 802, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('426', 'Acomptes reçus sur commandes', '42', 'PAS', 803, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('428', 'Cautionnement reçus en numéraires', '42', 'PAS', 804, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('429', 'Dettes diverses', '42', 'PAS', 805, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('430', 'Etablissements de crédit - Emprunts à compte à terme fixe', '43', 'PAS', 807, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('431', 'Etablissements de crédit - Promesses', '43', 'PAS', 808, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('432', ' Etablissements de crédit - Crédits d''acceptation', '43', 'PAS', 809, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('433', 'Etablissements de crédit -Dettes en comptes courant', '43', 'PAS', 810, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('439', 'Autres emprunts', '43', 'PAS', 811, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('441', 'Effets à payer', '44', 'PAS', 814, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('444', 'Factures à recevoir', '44', 'PAS', 815, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('450', 'Dettes fiscales estimées', '45', 'PAS', 817, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4500', 'Impôts belges sur le résultat', '450', 'PAS', 818, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4501', 'Impôts belges sur le résultat', '450', 'PAS', 819, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4502', 'Impôts belges sur le résultat', '450', 'PAS', 820, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4503', 'Impôts belges sur le résultat', '450', 'PAS', 821, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4504', 'Impôts belges sur le résultat', '450', 'PAS', 822, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4505', 'Autres impôts et taxes belges', '450', 'PAS', 823, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4506', 'Autres impôts et taxes belges', '450', 'PAS', 824, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4507', 'Autres impôts et taxes belges', '450', 'PAS', 825, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4508', 'Impôts et taxes étrangers', '450', 'PAS', 826, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('451', 'TVA à payer', '45', 'PAS', 827, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4511', 'TVA à payer 21%', '451', 'PAS', 828, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4512', 'TVA à payer 12%', '451', 'PAS', 829, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4513', 'TVA à payer 6%', '451', 'PAS', 830, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4514', 'TVA à payer 0%', '451', 'PAS', 831, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('452', 'Impôts et taxes à payer', '45', 'PAS', 832, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4520', 'Impôts belges sur le résultat', '452', 'PAS', 833, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4521', 'Impôts belges sur le résultat', '452', 'PAS', 834, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4522', 'Impôts belges sur le résultat', '452', 'PAS', 835, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4523', 'Impôts belges sur le résultat', '452', 'PAS', 836, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4524', 'Impôts belges sur le résultat', '452', 'PAS', 837, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4525', 'Autres impôts et taxes belges', '452', 'PAS', 838, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4526', 'Autres impôts et taxes belges', '452', 'PAS', 839, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4527', 'Autres impôts et taxes belges', '452', 'PAS', 840, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4528', 'Impôts et taxes étrangers', '452', 'PAS', 841, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('453', 'Précomptes retenus', '45', 'PAS', 842, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('454', 'Office National de la Sécurité Sociales', '45', 'PAS', 843, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('455', 'Rémunérations', '45', 'PAS', 844, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('456', 'Pécules de vacances', '45', 'PAS', 845, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('459', 'Autres dettes sociales', '45', 'PAS', 846, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('470', 'Dividendes et tantièmes d''exercices antérieurs', '47', 'PAS', 849, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('471', 'Dividendes de l''exercice', '47', 'PAS', 850, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('472', 'Tantièmes de l''exercice', '47', 'PAS', 851, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('473', 'Autres allocataires', '47', 'PAS', 852, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('480', 'Obligations et coupons échus', '48', 'PAS', 854, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('488', 'Cautionnements reçus en numéraires', '48', 'PAS', 855, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('489', 'Autres dettes diverses', '48', 'PAS', 856, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4890', 'Compte de l''exploitant', '489', 'PAS', 857, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('490', 'Charges à reporter', '49', 'ACT', 859, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('491', 'Produits acquis', '49', 'ACT', 860, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('492', 'Charges à imputer', '49', 'PAS', 861, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('493', 'Produits à reporter', '49', 'PAS', 862, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('499', 'Comptes d''attentes', '49', 'ACT', 863, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2821', 'Montants non-appelés(-)', '282', 'ACT', 864, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2828', 'Plus-values actées', '282', 'ACT', 865, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2829', 'Réductions de valeurs actées', '282', 'ACT', 866, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('283', 'Créances sur des entreprises avec lesquelles existe un lien de participation', '28', 'ACT', 867, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2830', 'Créance en compte', '283', 'ACT', 868, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2831', 'Effets à recevoir', '283', 'ACT', 869, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2832', 'Titre à revenu fixe', '283', 'ACT', 871, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2837', 'Créances douteuses', '283', 'ACT', 872, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2839', 'Réduction de valeurs actées', '283', 'ACT', 873, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('284', 'Autres actions et parts', '28', 'ACT', 874, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2840', 'Valeur d''acquisition', '284', 'ACT', 875, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2841', 'Montants non-appelés(-)', '284', 'ACT', 876, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2848', 'Plus-values actées', '284', 'ACT', 877, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2849', 'Réductions de valeurs actées', '284', 'ACT', 878, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('285', 'Autres créances', '28', 'ACT', 879, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2850', 'Créance en compte', '285', 'ACT', 880, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2851', 'Effets à recevoir', '285', 'ACT', 881, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2852', 'Titre à revenu fixe', '285', 'ACT', 882, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2857', 'Créances douteuses', '285', 'ACT', 883, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2859', 'Réductions de valeurs actées', '285', 'ACT', 884, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('288', 'Cautionnements versés en numéraires', '28', 'ACT', 885, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('290', 'Créances commerciales', '29', 'ACT', 887, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2900', 'Clients', '290', 'ACT', 888, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2901', 'Effets à recevoir', '290', 'ACT', 889, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2906', 'Acomptes versés', '290', 'ACT', 890, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2907', 'Créances douteuses', '290', 'ACT', 891, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2909', 'Réductions de valeurs actées', '290', 'ACT', 892, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('291', 'Autres créances', '29', 'ACT', 893, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2910', 'Créances en comptes', '291', 'ACT', 894, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2911', 'Effets à recevoir', '291', 'ACT', 895, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2917', 'Créances douteuses', '291', 'ACT', 896, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2919', 'Réductions de valeurs actées(-)', '291', 'ACT', 897, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('300', 'Valeur d''acquisition', '30', 'ACT', 899, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('309', 'Réductions de valeur actées', '30', 'ACT', 900, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('310', 'Valeur d''acquisition', '31', 'ACT', 902, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('319', 'Réductions de valeurs actées(-)', '31', 'ACT', 903, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('320', 'Valeurs d''acquisition', '32', 'ACT', 905, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('329', 'Réductions de valeur actées', '32', 'ACT', 906, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('330', 'Valeur d''acquisition', '33', 'ACT', 908, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('339', 'Réductions de valeur actées', '33', 'ACT', 909, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('340', 'Valeur d''acquisition', '34', 'ACT', 911, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('349', 'Réductions de valeur actées', '34', 'ACT', 912, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('350', 'Valeur d''acquisition', '35', 'ACT', 914, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('359', 'Réductions de valeur actées', '35', 'ACT', 915, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('360', 'Valeur d''acquisition', '36', 'ACT', 917, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('369', 'Réductions de valeur actées', '36', 'ACT', 918, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('370', 'Valeur d''acquisition', '37', 'ACT', 920, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('371', 'Bénéfice pris en compte ', '37', 'ACT', 921, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('379', 'Réductions de valeur actées', '37', 'ACT', 922, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('400', 'Clients', '40', 'ACT', 924, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('401', 'Effets à recevoir', '40', 'ACT', 925, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('404', 'Produits à recevoir', '40', 'ACT', 926, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('406', 'Acomptes versés', '40', 'ACT', 927, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('407', 'Créances douteuses', '40', 'ACT', 928, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('409', 'Réductions de valeur actées', '40', 'ACT', 929, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('410', 'Capital appelé non versé', '41', 'ACT', 931, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('411', 'TVA à récupérer', '41', 'ACT', 932, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4111', 'TVA à récupérer 21%', '411', 'ACT', 933, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4112', 'TVA à récupérer 12%', '411', 'ACT', 934, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4113', 'TVA à récupérer 6% ', '411', 'ACT', 935, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4114', 'TVA à récupérer 0%', '411', 'ACT', 936, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('412', 'Impôts et précomptes à récupérer', '41', 'ACT', 937, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4120', 'Impôt belge sur le résultat', '412', 'ACT', 938, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4121', 'Impôt belge sur le résultat', '412', 'ACT', 939, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4122', 'Impôt belge sur le résultat', '412', 'ACT', 940, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4123', 'Impôt belge sur le résultat', '412', 'ACT', 941, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4124', 'Impôt belge sur le résultat', '412', 'ACT', 942, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4125', 'Autres impôts et taxes belges', '412', 'ACT', 943, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4126', 'Autres impôts et taxes belges', '412', 'ACT', 944, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4127', 'Autres impôts et taxes belges', '412', 'ACT', 945, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4128', 'Impôts et taxes étrangers', '412', 'ACT', 946, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6040003', 'Petit matériel', '604', 'CHA', 948, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('130', 'Réserve légale', '13', 'PAS', 952, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('131', 'Réserve indisponible', '13', 'PAS', 953, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1310', 'Réserve pour actions propres', '131', 'PAS', 954, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6040004', 'Assurance', '604', 'CHA', 955, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('55000001', 'Caisse', '5500', 'ACT', 956, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('133', 'Réserves disponibles', '13', 'PAS', 957, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('140', 'Bénéfice reporté', '14', 'PAS', 959, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('141', 'Perte reportée', '14', 'PASINV', 960, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('160', 'Provisions pour pensions et obligations similaires', '16', 'PAS', 963, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('161', 'Provisions pour charges fiscales', '16', 'PAS', 964, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('749', 'Produits d''exploitations divers', '74', 'PRO', 965, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('162', 'Provisions pour grosses réparation et gros entretien', '16', 'PAS', 966, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('170', 'Emprunts subordonnés', '17', 'PAS', 968, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1700', 'convertibles', '170', 'PAS', 969, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1701', 'non convertibles', '170', 'PAS', 970, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('171', 'Emprunts subordonnés', '17', 'PAS', 971, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1710', 'convertibles', '170', 'PAS', 972, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1711', 'non convertibles', '170', 'PAS', 973, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('172', ' Dettes de locations financement', '17', 'PAS', 974, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('173', ' Etablissement de crédit', '17', 'PAS', 975, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1730', 'Dettes en comptes', '173', 'PAS', 976, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1731', 'Promesses', '173', 'PAS', 977, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1732', 'Crédits d''acceptation', '173', 'PAS', 978, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('174', 'Autres emprunts', '17', 'PAS', 979, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('175', 'Dettes commerciales', '17', 'PAS', 980, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1750', 'Fournisseurs', '175', 'PAS', 981, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1751', 'Effets à payer', '175', 'PAS', 982, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('176', 'Acomptes reçus sur commandes', '17', 'PAS', 983, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('178', 'Cautionnement reçus en numéraires', '17', 'PAS', 984, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('179', 'Dettes diverses', '17', 'PAS', 985, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('200', 'Frais de constitution et d''augmentation de capital', '20', 'ACT', 987, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('201', ' Frais d''émission d''emprunts et primes de remboursement', '20', 'ACT', 988, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('202', 'Autres frais d''établissement', '20', 'ACT', 989, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('204', 'Frais de restructuration', '20', 'ACT', 990, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('210', 'Frais de recherche et de développement', '21', 'ACT', 992, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('211', 'Concessions, brevet, licence savoir faire, marque et droit similaires', '21', 'ACT', 993, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('212', 'Goodwill', '21', 'ACT', 994, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('213', 'Acomptes versés', '21', 'ACT', 995, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('220', 'Terrains', '22', 'ACT', 997, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('222', 'Terrains bâtis', '22', 'ACT', 998, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('223', 'Autres droits réels sur des immeubles', '22', 'ACT', 999, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('250', 'Terrains', '25', 'ACT', 1003, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('251', 'Construction', '25', 'ACT', 1004, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('252', 'Terrains bâtis', '25', 'ACT', 1005, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('253', 'Mobilier et matériels roulants', '25', 'ACT', 1006, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('280', 'Participation dans des entreprises liées', '28', 'ACT', 1010, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2800', 'Valeur d''acquisition', '280', 'ACT', 1011, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2801', 'Montants non-appelés(-)', '280', 'ACT', 1012, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2808', 'Plus-values actées', '280', 'ACT', 1013, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2809', 'Réductions de valeurs actées', '280', 'ACT', 1014, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('281', 'Créance sur  des entreprises liées', '28', 'ACT', 1015, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2810', 'Créance en compte', '281', 'ACT', 1016, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2811', 'Effets à recevoir', '281', 'ACT', 1017, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2812', 'Titre à reveny fixe', '281', 'ACT', 1018, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2817', 'Créances douteuses', '281', 'ACT', 1019, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2819', 'Réduction de valeurs actées', '281', 'ACT', 1020, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('282', 'Participations dans des entreprises avec lesquelles il existe un lien de participation', '28', 'ACT', 1021, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2820', 'Valeur d''acquisition', '282', 'ACT', 1022, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4516', 'Tva Export 0%', '451', 'PAS', 1023, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4115', 'Tva Intracomm 0%', '411', 'ACT', 1024, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4116', 'Tva Export 0%', '411', 'ACT', 1025, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('41141', 'TVA pour l\''export', '4114', 'ACT', 1026, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('41142', 'TVA sur les opérations intracommunautaires', '4114', 'ACT', 1027, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('45141', 'TVA pour l\''export', '451', 'PAS', 1028, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('45142', 'TVA sur les opérations intracommunautaires', '4514', 'PAS', 1029, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('41143', 'TVA sur les opérations avec des assujettis art 44 Code TVA', '4114', 'ACT', 1030, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('45143', 'TVA sur les opérations avec des assujettis art 44 Code TVA', '4514', 'PAS', 1031, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('41144', 'TVA sur les opérations avec des cocontractants', '4114', 'ACT', 1032, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('45144', 'TVA sur les opérations avec des cocontractants', '4514', 'PAS', 1033, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6202', 'Employés,620', '62', 'CHA', 1034, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('57', 'Caisse', '5', 'ACT', 870, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1', 'Fonds propres, provisions pour risques et charges à plus d''un an', '0', 'PAS', 520, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2', 'Frais d''établissement, actifs immobilisés et créances à plus d''un an', '0', 'ACT', 521, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('3', 'Stocks et commandes en cours d''éxécution', '0', 'ACT', 522, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4', 'Créances et dettes à un an au plus', '0', 'ACT', 523, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5', 'Placements de trésorerie et valeurs disponibles', '0', 'ACT', 524, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6', 'Charges', '0', 'CHA', 525, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7', 'Produits', '0', 'PRO', 526, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('34', 'Marchandises', '3', 'ACT', 910, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('9', 'Comptes hors Compta', '0', 'CON', 547, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('68', 'Transferts aux réserves immunisées', '6', 'CHA', 555, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('69', 'Affectations et prélévements', '6', 'CHA', 556, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('70', 'Chiffre d''affaire', '7', 'PRO', 564, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('71', 'Variations des stocks et commandes en cours d''éxécution', '7', 'PRO', 573, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('72', 'Production immobilisée', '7', 'PRO', 580, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('74', 'Autres produits d''exploitation', '7', 'PRO', 581, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('75', 'Produits financiers', '7', 'PRO', 591, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('76', 'Produits exceptionnels', '7', 'PRO', 603, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('77', 'Régularisations d''impôts et reprises de provisions fiscales', '7', 'PRO', 616, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('79', 'Affectations et prélévements', '7', 'PRO', 622, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('64', 'Autres charges d''exploitation', '6', 'CHA', 652, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('65', 'Charges financières', '6', 'CHA', 663, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('66', 'Charges exceptionnelles', '6', 'CHA', 680, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('67', 'impôts sur le résultat', '6', 'CHA', 694, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('50', 'Actions propres', '5', 'ACT', 701, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('51', 'Actions et parts', '5', 'ACT', 702, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('52', 'Titres à revenu fixe', '5', 'ACT', 706, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('53', 'Dépôts à terme', '5', 'ACT', 709, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('54', 'Valeurs échues à l''encaissement', '5', 'ACT', 713, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('55', 'Etablissement de crédit', '5', 'ACT', 714, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('56', 'Office des chèques postaux', '5', 'ACT', 746, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('58', 'Virement interne', '5', 'ACT', 750, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('60', 'Approvisionnement et marchandises', '6', 'CHA', 751, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('61', 'Services et biens divers', '6', 'CHA', 764, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('62', 'Rémunérations, charges sociales et pensions', '6', 'CHA', 765, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('63', 'Amortissements, réductions de valeurs et provisions pour risques et charges', '6', 'CHA', 777, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('42', 'Dettes à plus dun an échéant dans l''année', '4', 'PAS', 787, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('43', 'Dettes financières', '4', 'PAS', 806, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44', 'Dettes commerciales', '4', 'PAS', 812, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('45', 'Dettes fiscales, salariales et sociales', '4', 'PAS', 816, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('46', 'Acomptes reçus sur commandes', '4', 'PAS', 847, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('47', 'Dettes découlant de l''affectation du résultat', '4', 'PAS', 848, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('48', 'Dettes diverses', '4', 'PAS', 853, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('49', 'Comptes de régularisation', '4', 'ACT', 858, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('29', 'Créances à plus d''un an', '2', 'ACT', 886, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('30', 'Approvisionements - Matières premières', '3', 'ACT', 898, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('31', 'Approvisionnements - fournitures', '3', 'ACT', 901, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('32', 'En-cours de fabrication', '3', 'ACT', 904, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('33', 'Produits finis', '3', 'ACT', 907, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('35', 'Immeubles destinés à la vente', '3', 'ACT', 913, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('36', 'Acomptes versés sur achats pour stocks', '3', 'ACT', 916, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('37', 'Commandes en cours éxécution', '3', 'ACT', 919, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('40', 'Créances commerciales', '4', 'ACT', 923, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('41', 'Autres créances', '4', 'ACT', 930, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('10', 'Capital ', '1', 'PAS', 947, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('11', 'Prime d''émission ', '1', 'PAS', 949, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('12', 'Plus Value de réévaluation ', '1', 'PAS', 950, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('13', 'Réserve ', '1', 'PAS', 951, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('14', 'Bénéfice ou perte reportée', '1', 'PAS', 958, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('15', 'Subside en capital', '1', 'PAS', 961, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('16', 'Provisions pour risques et charges', '1', 'PAS', 962, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('17', ' Dettes à plus d''un an', '1', 'PAS', 967, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('20', 'Frais d''établissement', '2', 'ACT', 986, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('21', 'Immobilisations incorporelles', '2', 'ACT', 991, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('22', 'Terrains et construction', '2', 'ACT', 996, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('23', ' Installations, machines et outillages', '2', 'ACT', 1000, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('24', 'Mobilier et Matériel roulant', '2', 'ACT', 1001, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('25', 'Immobilisations détenus en location-financement et droits similaires', '2', 'ACT', 1002, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('26', 'Autres immobilisations corporelles', '2', 'ACT', 1007, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('27', 'Immobilisations corporelles en cours et acomptes versés', '2', 'ACT', 1008, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('28', 'Immobilisations financières', '2', 'ACT', 1009, 'N');









SELECT pg_catalog.setval('del_action_del_id_seq', 1, false);






SELECT pg_catalog.setval('del_jrn_dj_id_seq', 1, false);






SELECT pg_catalog.setval('del_jrnx_djx_id_seq', 1, false);






SELECT pg_catalog.setval('document_d_id_seq', 1, false);






SELECT pg_catalog.setval('document_modele_md_id_seq', 1, false);



SELECT pg_catalog.setval('document_seq', 1, false);



INSERT INTO document_state (s_id, s_value, s_status) VALUES (2, 'A suivre', NULL);
INSERT INTO document_state (s_id, s_value, s_status) VALUES (3, 'A faire', NULL);
INSERT INTO document_state (s_id, s_value, s_status) VALUES (1, 'Clôturé', 'C');
INSERT INTO document_state (s_id, s_value, s_status) VALUES (4, 'Abandonné', 'C');



SELECT pg_catalog.setval('document_state_s_id_seq', 100, false);



SELECT pg_catalog.setval('document_type_dt_id_seq', 25, false);






SELECT pg_catalog.setval('extension_ex_id_seq', 1, true);






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









SELECT pg_catalog.setval('forecast_cat_fc_id_seq', 1, false);



SELECT pg_catalog.setval('forecast_f_id_seq', 1, false);






SELECT pg_catalog.setval('forecast_item_fi_id_seq', 1, false);



INSERT INTO formdef (fr_id, fr_label) VALUES (3000000, 'TVA déclaration Belge');



INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000398, 3000000, 1, 'Prestation [ case 03 ]', '[700%]-[7000005]');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000399, 3000000, 2, 'Prestation intra [ case 47 ]', '[7000005]');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000400, 3000000, 3, 'Tva due   [case 54]', '[4513]+[4512]+[4511] FROM=01.2005');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000401, 3000000, 4, 'Marchandises, matière première et auxiliaire [case 81 ]', '[60%]');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000402, 3000000, 7, 'Service et bien divers [case 82]', '[61%]');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000403, 3000000, 8, 'bien d''invest [ case 83 ]', '[2400%]');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000404, 3000000, 9, 'TVA déductible [ case 59 ]', 'abs([4117]-[411%])');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000405, 3000000, 8, 'TVA non ded -> voiture', '[610022]*0.21/2');
INSERT INTO form (fo_id, fo_fr_id, fo_pos, fo_label, fo_formula) VALUES (3000406, 3000000, 9, 'Acompte TVA', '[4117]');









SELECT pg_catalog.setval('historique_analytique_ha_id_seq', 1, false);



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
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (500000, 1, 54, 10);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (500000, 9, 55, 20);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (500000, 23, 56, 30);






SELECT pg_catalog.setval('jnt_letter_jl_id_seq', 1, false);






SELECT pg_catalog.setval('jrn_info_ji_id_seq', 1, false);






SELECT pg_catalog.setval('jrn_note_n_id_seq', 1, false);



INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 79, 'OP', 1);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 79, 'OP', 2);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 79, 'OP', 3);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 79, 'OP', 4);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 80, 'OP', 5);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 80, 'OP', 6);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 80, 'OP', 7);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 80, 'OP', 8);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 81, 'OP', 9);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 81, 'OP', 10);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 81, 'OP', 11);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 81, 'OP', 12);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 82, 'OP', 13);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 82, 'OP', 14);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 82, 'OP', 15);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 82, 'OP', 16);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 83, 'OP', 17);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 83, 'OP', 18);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 83, 'OP', 19);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 83, 'OP', 20);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 84, 'OP', 21);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 84, 'OP', 22);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 84, 'OP', 23);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 84, 'OP', 24);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 85, 'OP', 25);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 85, 'OP', 26);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 85, 'OP', 27);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 85, 'OP', 28);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 86, 'OP', 29);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 86, 'OP', 30);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 86, 'OP', 31);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 86, 'OP', 32);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 87, 'OP', 33);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 87, 'OP', 34);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 87, 'OP', 35);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 87, 'OP', 36);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 88, 'OP', 37);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 88, 'OP', 38);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 88, 'OP', 39);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 88, 'OP', 40);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 89, 'OP', 41);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 89, 'OP', 42);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 89, 'OP', 43);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 89, 'OP', 44);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 90, 'OP', 45);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 90, 'OP', 46);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 90, 'OP', 47);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 90, 'OP', 48);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 91, 'OP', 49);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 91, 'OP', 50);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 91, 'OP', 51);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 91, 'OP', 52);



SELECT pg_catalog.setval('jrn_periode_id_seq', 52, true);





















SELECT pg_catalog.setval('key_distribution_activity_ka_id_seq', 1, false);



SELECT pg_catalog.setval('key_distribution_detail_ke_id_seq', 1, false);



SELECT pg_catalog.setval('key_distribution_kd_id_seq', 1, false);






SELECT pg_catalog.setval('key_distribution_ledger_kl_id_seq', 1, false);






SELECT pg_catalog.setval('letter_cred_lc_id_seq', 1, false);






SELECT pg_catalog.setval('letter_deb_ld_id_seq', 1, false);



SELECT pg_catalog.setval('link_action_type_l_id_seq', 1, false);



INSERT INTO menu_default (md_id, md_code, me_code) VALUES (1, 'code_invoice', 'COMPTA/VENMENU/VEN');
INSERT INTO menu_default (md_id, md_code, me_code) VALUES (2, 'code_follow', 'GESTION/FOLLOW');



SELECT pg_catalog.setval('menu_default_md_id_seq', 2, true);



INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ACH', 'Achat', 'compta_ach.inc.php', NULL, 'Nouvel achat ou dépense', NULL, NULL, 'ME', 'Vous permet d''encoder des achats, dépenses, des notes de frais ou des notes de crédits, vous pouvez spécifier un bénéficiaire ou un autre moyen de paiement');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCHOP', 'Historique', 'anc_history.inc.php', NULL, 'Historique des imputations analytiques', NULL, NULL, 'ME', 'Historique des imputations analytiques');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCBS', 'Balance simple', 'anc_balance_simple.inc.php', NULL, 'Balance simple des imputations analytiques', NULL, NULL, 'ME', 'Balance simple des imputations analytiques');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCTAB', 'Tableau', 'anc_acc_table.inc.php', NULL, 'Tableau lié à la comptabilité', NULL, NULL, 'ME', 'Tableau lié à la comptabilité');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCBCC', 'Balance Analytique/comptabilité', 'anc_acc_balance.inc.php', NULL, 'Lien entre comptabilité et Comptabilité analytique', NULL, NULL, 'ME', 'Lien entre comptabilité et Comptabilité analytique');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCGR', 'Groupe', 'anc_group_balance.inc.php', NULL, 'Balance par groupe', NULL, NULL, 'ME', 'Balance par groupe');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGTVA', 'TVA', 'tva.inc.php', NULL, 'Config. de la tva', NULL, NULL, 'ME', 'Permet d''ajouter des taux de TVA ou de les modifier ainsi que les postes comptables de ces TVA, ces TVA sont utilisables dans les menus de vente et d''achat');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('VEN', 'Vente', 'compta_ven.inc.php', NULL, 'Nouvelle vente ou recette', NULL, NULL, 'ME', 'Encodage de tous vos revenus ou vente');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGMENU', 'Config. Menu', 'menu.inc.php', NULL, 'Configuration des menus et plugins', NULL, NULL, 'ME', 'Ajout de menu ou de plugins');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('SUPPL', 'Fournisseur', 'supplier.inc.php', NULL, 'Suivi fournisseur', NULL, NULL, 'ME', 'Suivi des fournisseurs : devis, lettres, email....');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCODS', 'Opérations diverses', 'anc_od.inc.php', NULL, 'OD analytique', NULL, NULL, 'ME', 'Opérations diverses en Analytique');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('REPORT', 'Création de rapport', 'report.inc.php', NULL, 'Création de rapport', NULL, NULL, 'ME', 'Création de rapport sur mesure, comme les ratios, vous permet de créer des graphiques de vos données (vente, achat...)');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('new_line', 'saut de ligne', NULL, NULL, 'Saut de ligne', NULL, NULL, 'SP', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CUST', 'Client', 'customer.inc.php', NULL, 'Suivi client', NULL, NULL, 'ME', 'Suivi client : devis, réunion, courrier, commande...');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('STOCK_HISTO', 'Historique stock', 'stock_histo.inc.php', NULL, 'Historique des mouvement de stock', NULL, NULL, 'ME', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:histo', 'Export Historique', 'export_histo_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:ledger', 'Export Journaux', 'export_ledger_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:ledger', 'Export Journaux', 'export_ledger_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:postedetail', 'Export Poste détail', 'export_poste_detail_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:postedetail', 'Export Poste détail', 'export_poste_detail_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:fichedetail', 'Export Fiche détail', 'export_fiche_detail_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:fichedetail', 'Export Fiche détail', 'export_fiche_detail_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:fiche_balance', 'Export Fiche balance', 'export_fiche_balance_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:fiche_balance', 'Export Fiche balance', 'export_fiche_balance_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:report', 'Export report', 'export_form_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:report', 'Export report', 'export_form_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:fiche', 'Export Fiche', 'export_fiche_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:fiche', 'Export Fiche', 'export_fiche_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:glcompte', 'Export Grand Livre', 'export_gl_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:glcompte', 'Export Grand Livre', 'export_gl_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:sec', 'Export Sécurité', 'export_security_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:AncList', 'Export Comptabilité analytique', 'export_anc_list_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:AncBalSimple', 'Export Comptabilité analytique balance simple', 'export_anc_balance_simple_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:AncBalSimple', 'Export Comptabilité analytique', 'export_anc_balance_simple_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:AncBalDouble', 'Export Comptabilité analytique balance double', 'export_anc_balance_double_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:AncBalDouble', 'Export Comptabilité analytique balance double', 'export_anc_balance_double_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:balance', 'Export Balance comptable', 'export_balance_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:balance', 'Export Balance comptable', 'export_balance_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:AncTable', 'Export Tableau Analytique', 'export_anc_table_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:AncAccList', 'Export Historique Compt. Analytique', 'export_anc_acc_list_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:AncBalGroup', 'Export Balance groupe analytique', 'export_anc_balance_group_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('OTH:Bilan', 'Export Bilan', 'export_bilan_oth.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:AncGrandLivre', 'Impression Grand-Livre', 'export_anc_grandlivre_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:reportinit', 'Export définition d''un raport', 'export_reportinit_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:ActionGestion', 'Export Action Gestion', 'export_follow_up_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:StockHisto', 'Export Historique mouvement stock', 'export_stock_histo_csv.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:StockResmList', 'Export Résumé list stock', 'export_stock_resume_list.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('EXT', 'Extension', NULL, NULL, 'Extensions (plugins)', NULL, NULL, 'ME', 'Menu regroupant les plugins');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PLANANC', 'Plan Compt. analytique', 'anc_pa.inc.php', NULL, 'Plan analytique', NULL, NULL, 'ME', 'Axe analytique');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCGROUP', 'Groupe', 'anc_group.inc.php', NULL, 'Groupe analytique', NULL, NULL, 'ME', 'Regroupement de compte analytique');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CONTACT', 'Contact', 'contact.inc.php', NULL, 'Liste des contacts', NULL, NULL, 'ME', 'Liste de tous vos contacts');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PREDOP', 'Ecriture prédefinie', 'preod.inc.php', NULL, 'Gestion des opérations prédéfinifies', NULL, NULL, 'ME', 'Les opérations prédéfinies sont des opérations que vous faites régulièrement (loyer, abonnement,...) ');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ODS', 'Opérations Diverses', 'compta_ods.inc.php', NULL, 'Nouvelle opérations diverses', NULL, NULL, 'ME', 'Opération diverses tels que les amortissements, les augmentations de capital, les salaires, ...');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('FIN', 'Nouvel extrait', 'compta_fin.inc.php', NULL, 'Nouvel extrait bancaire', NULL, NULL, 'ME', 'Encodage d''un extrait bancaire (=relevé bancaire)');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('JSSEARCH', 'Recherche', NULL, NULL, 'Recherche', NULL, 'search_reconcile()', 'ME', 'Historique de toutes vos opérations un menu de recherche dans une nouvelle fenêtre, vous permettra de retrouver rapidement l''opération qui vous intéresse');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PARAM', 'Paramètre', NULL, NULL, 'Module paramètre', NULL, NULL, 'ME', 'Module paramètres');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGCATDOC', 'Catégorie de documents', 'cat_document.inc.php', NULL, 'Config. catégorie de documents', NULL, NULL, 'ME', 'Vous permet d''ajouter de nouveaux type de documents (bordereau de livraison, devis..)');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('LETCARD', 'Lettrage par Fiche', 'lettering.card.inc.php', NULL, 'Lettrage par fiche', NULL, NULL, 'ME', 'Lettrage par fiche');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ACHISTO', 'Historique achat', 'history_operation.inc.php', NULL, 'Historique achat', 'ledger_type=ACH', NULL, 'ME', 'Historique de toutes vos opérations dans les journaux d''achats un menu de recherche, vous permettra de retrouver rapidement l''opération qui vous intéresse');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ODHISTO', 'Historique opérations diverses', 'history_operation.inc.php', NULL, 'Historique opérations diverses', 'ledger_type=ODS', NULL, 'ME', 'Historique de toutes vos opérations dans les journaux d''opérations diverses un menu de recherche, vous permettra de retrouver rapidement l''opération qui vous intéresse');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PRINTPOSTE', 'Poste', 'impress_poste.inc.php', NULL, 'Impression du détail d''un poste comptable', NULL, NULL, 'ME', 'Impression du détail d''un poste comptable');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PRINTREPORT', 'Rapport', 'impress_rapport.inc.php', NULL, 'Impression de rapport', NULL, NULL, 'ME', 'Impression de rapport personnalisé, il est aussi possible d''exporter en CSV afin de faire des graphiques');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PRINTGL', 'Grand Livre', 'impress_gl_comptes.inc.php', NULL, 'Impression du grand livre', NULL, NULL, 'ME', 'Impression du grand livre');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PRINTBAL', 'Balance', 'balance.inc.php', NULL, 'Impression des balances comptables', NULL, NULL, 'ME', 'Impression des balances comptables');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('MENUACH', 'Achat', NULL, NULL, 'Menu achat', NULL, NULL, 'ME', 'Regroupement pour les menus d''achats(nouvelle opération, historique...)');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('MOD', 'Menu et profil', NULL, NULL, 'Menu ', NULL, NULL, 'ME', 'Regroupement pour les menus et les profils');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PRINT', 'Impression', NULL, NULL, 'Menu impression', NULL, NULL, 'ME', 'Menu impression');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('BK', 'Banque', 'bank.inc.php', NULL, 'Information Banque', NULL, NULL, 'ME', 'Regroupement des menus des journaux de trésorerie');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCGL', 'Grand''Livre', 'anc_great_ledger.inc.php', NULL, 'Grand livre analytique', NULL, NULL, 'ME', 'Grand livre pour la comptabilité analytique');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('GESTION', 'Gestion', NULL, NULL, 'Module gestion', NULL, NULL, 'ME', 'Module gestion');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('LET', 'Lettrage', NULL, NULL, 'Lettrage', NULL, NULL, 'ME', 'Menu Lettrage');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ACCESS', 'Accueil', NULL, 'user_login.php', 'Accueil', NULL, NULL, 'ME', 'Choix de votre dossier');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('COMPTA', 'Comptabilité', NULL, NULL, 'Module comptabilité', NULL, NULL, 'ME', 'Module comptabilité');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('DIVPARM', 'Divers', NULL, NULL, 'Paramètres divers', NULL, NULL, 'ME', 'Menu de différents paramètres');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGPRO', 'Profil', 'profile.inc.php', NULL, 'Configuration profil', NULL, NULL, 'ME', 'Configuration des profils des utilisateurs, permet de fixer les journaux, profils dans les documents et stock que  ce profil peut utiliser. Cela limite les utilisateurs puisque ceux-ci ont un profil');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGACC', 'Poste', 'poste.inc.php', NULL, 'Config. poste comptable de base', NULL, NULL, 'ME', 'Config. poste comptable de base');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CARD', 'Fiche', 'fiche.inc.php', NULL, 'Liste,Balance,Historique par fiche', NULL, NULL, 'ME', 'Permet d''avoir la balance de toutes vos fiches, les résumés exportables en CSV, les historiques avec ou sans lettrages');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PREFERENCE', 'Préférence', NULL, NULL, 'Préférence', NULL, 'set_preference(<DOSSIER>)', 'ME', 'Préférence de l''utilisateur, apparence de l''application pour l''utilisateur, période par défaut et mot de passe');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGTAG', 'Configuration étiquette', 'cfgtags.inc.php', NULL, 'Configuration des tags', NULL, NULL, 'ME', 'Configuration des tags ou dossiers, on l''appele tag ou dossier suivant la façon dont vous utilisez 
cette fonctionnalité. Vous pouvez en ajouter, en supprimer ou les modifier');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCBC2', 'Balance croisée double', 'anc_balance_double.inc.php', NULL, 'Balance double croisées des imputations analytiques', NULL, NULL, 'ME', 'Balance double croisées des imputations analytiques');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('COMPANY', 'Sociétés', 'company.inc.php', NULL, 'Parametre societe', NULL, NULL, 'ME', 'Information sur votre société : nom, adresse... utilisé lors de la génération de documents');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PERIODE', 'Période', 'periode.inc.php', NULL, 'Gestion des périodes', NULL, NULL, 'ME', 'Gestion des périodes : clôture, ajout de période, afin de créer des périodes vous pouvez aussi utiliser le plugin outil comptable');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('VERIFBIL', 'Vérification ', 'verif_bilan.inc.php', NULL, 'Vérification de la comptabilité', NULL, NULL, 'ME', 'Vérifie que votre comptabilité ne contient pas d''erreur de base, tels que l''équilibre entre le passif et l''actif, l''utilisation des postes comptables...');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('OPEN', 'Ecriture Ouverture', 'opening.inc.php', NULL, 'Ecriture d''ouverture', NULL, NULL, 'ME', 'Ecriture d''ouverture ou écriture à nouveau, reporte les soldes des comptes de l''année passé du poste comptable 0xxx à 5xxxx sur l''année courante');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGDOC', 'Document', 'document_modele.inc.php', NULL, 'Config. modèle de document', NULL, NULL, 'ME', 'Chargement de modèles de documents qui seront générés par NOALYSS, les formats utilisables sont libreoffice, html, text et rtf');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGLED', 'journaux', 'cfgledger.inc.php', NULL, 'Configuration des journaux', NULL, NULL, 'ME', 'Création et modification des journaux, préfixe des pièces justificatives, numérotation, catégories de fiches accessibles à ce journal');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGSEC', 'Sécurité', 'param_sec.inc.php', NULL, 'configuration de la sécurité', NULL, NULL, 'ME', 'Configuration de la sécurité, vous permet de donner un profil à vos utilisateurs, cela leur permettra d''utiliser ce que vous souhaitez qu''ils puissent utiliser');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGATCARD', 'Attribut de fiche', 'card_attr.inc.php', NULL, 'Gestion des attributs de fiches ', NULL, NULL, 'ME', 'Permet d''ajouter de nouveaux attributs que vous pourrez par la suite ajouter à des catégories de fiches');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('STOCK_STATE', 'Etat des stock', 'stock_state.inc.php', NULL, 'Etat des stock', NULL, NULL, 'ME', 'Etat des stock de l''exercice indiqué');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('STOCK_INV', 'Modification Stocks', 'stock_inv.inc.php', NULL, 'Modification des stocks (inventaire)', NULL, NULL, 'ME', 'Modification des stocks, menu utilisé pour l''inventaire');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('STOCK_INVHISTO', 'Histo. Changement', 'stock_inv_histo.inc.php', NULL, 'Liste des changements manuels des stocks', NULL, NULL, 'ME', 'Liste des changements manuels des stocks, inventaire, transfert de marchandises entre dépôts...');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('NAVI', 'Navigateur', NULL, NULL, 'Menu simplifié pour retrouver rapidement un menu', NULL, 'ask_navigator(<DOSSIER>)', 'ME', 'Le navigateur vous présente une liste de menu auquel vous avez accès et vous permet d''accèder plus rapidement au menu que vous souhaitez');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('SEARCH', 'Recherche', NULL, NULL, 'Recherche', NULL, 'popup_recherche(<DOSSIER>)', 'ME', 'Historique de toutes vos opérations dans tous  les journaux auquels vous avez accès, vous permettra de retrouver rapidement l''opération qui vous intéresse sur base de la date, du poste comptable, des montants...');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('HIST', 'Historique', 'history_operation.inc.php', NULL, 'Historique', 'ledger_type=ALL', NULL, 'ME', 'Historique de toutes vos opérations un menu de recherche, vous permettra de retrouver rapidement l''opération qui vous intéresse');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('FREC', 'Rapprochement', 'compta_fin_rec.inc.php', NULL, 'Rapprochement bancaire', NULL, NULL, 'ME', 'Permet de faire correspondre vos extraits bancaires avec les opérations de vente ou d''achat, le lettrage se fait automatiquement');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('FSALDO', 'Soldes', 'compta_fin_saldo.inc.php', NULL, 'Solde des comptes en banques, caisse...', NULL, NULL, 'ME', 'Solde des journaux de trésorerie cela concerne les comptes en banques, caisse , les chèques... ');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('LOGOUT', 'Sortie &#9094', NULL, 'logout.php', 'Sortie', NULL, NULL, 'ME', 'Déconnexion ');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('LETACC', 'Lettrage par Poste', 'lettering.account.inc.php', NULL, 'lettrage par poste comptable', NULL, NULL, 'ME', 'lettrage par poste comptable');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CARDBAL', 'Balance', 'balance_card.inc.php', NULL, 'Balance par catégorie de fiche', NULL, NULL, 'ME', 'Balance par catégorie de fiche ou pour toutes les fiches ayant un poste comptable');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGPCMN', 'Plan Comptable', 'param_pcmn.inc.php', NULL, 'Config. du plan comptable', NULL, NULL, 'ME', 'Modification de votre plan comptable, parfois il est plus rapide d''utiliser le plugin "Poste Comptable"');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('VEHISTO', 'Historique vente', 'history_operation.inc.php', NULL, 'Historique des ventes', 'ledger_type=VEN', NULL, 'ME', 'Historique de toutes vos opérations dans les journaux de vente un menu de recherche, vous permettra de retrouver rapidement l''opération qui vous intéresse');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('FIHISTO', 'Historique financier', 'history_operation.inc.php', NULL, 'Historique financier', 'ledger_type=FIN', NULL, 'ME', 'Historique de toutes vos opérations dans les journaux de trésorerie un menu de recherche, vous permettra de retrouver rapidement l''opération qui vous intéresse');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PRINTREC', 'Rapprochement', 'impress_rec.inc.php', NULL, 'Impression des rapprochements', NULL, NULL, 'ME', 'Impression des rapprochements : opérations non rapprochées ou avec des montants différents');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PRINTBILAN', 'Bilan', 'impress_bilan.inc.php', NULL, 'Impression de bilan', NULL, NULL, 'ME', 'Impression de bilan, ce module est basique, il est plus intéressant d''utiliser le plugin "rapport avancés"');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGSTOCK', 'Configuration des dépôts', 'stock_cfg.inc.php', NULL, 'Configuration dépôts', NULL, NULL, 'ME', 'Configuration des entrepots de dépôts');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('STOCK', 'Stock', NULL, NULL, 'Stock', NULL, NULL, 'ME', 'Permet d''ajouter de nouvelles catégorie de fiche, d''ajouter des attributs à ces catégories (numéro de téléphone, gsm, email...)');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGCARDCAT', 'Catégorie de fiche', 'fiche_def.inc.php', NULL, 'Gestion catégorie de fiche', NULL, NULL, 'ME', 'Permet de changer le poste comptable de base des catégories de fiches');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGCARD', 'Fiche', 'cfgfiche.inc.php', NULL, 'Configuration de catégorie de fiches', NULL, NULL, 'ME', 'Permet d''ajouter de nouvelles catégorie de fiche, d''ajouter des attributs à ces catégories (numéro de téléphone, gsm, email...)');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGDOCST', 'Etat des documents', 'doc_state.inc.php', NULL, 'Etat des documents', NULL, NULL, 'ME', 'Permet d''ajouter des état pour les documents utilisés dans le suivi (à faire, à suivre...)');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('MENUODS', 'Opérations diverses', NULL, NULL, 'Menu opérations diverses', NULL, NULL, 'ME', 'Regroupement pour les menus d''opérations diverses (nouvelle opération, historique...)');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PRINTJRN', 'Impression Journaux', 'impress_jrn.inc.php', NULL, 'Impression des journaux', NULL, NULL, 'ME', 'Impression des journaux avec les détails pour les parties privés, la TVA et ce qui est non déductibles en ce qui concerne les journaux de vente et d''achat');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('VENMENU', 'Vente / Recette', NULL, NULL, 'Menu ventes et recettes', NULL, NULL, 'ME', 'Regroupement des menus ventes et recettes');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCIMP', 'Impression', NULL, NULL, 'Impression compta. analytique', NULL, NULL, 'ME', 'Impression compta. analytique');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANC', 'Compta Analytique', NULL, NULL, 'Module comptabilité analytique', NULL, NULL, 'ME', 'Module comptabilité analytique');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('DASHBOARD', 'Tableau de bord', 'dashboard.inc.php', NULL, 'Tableau de bord', NULL, NULL, 'ME', 'Tableau de suivi, vous permet de voir en un coup d''oeil vos dernières opérations, un petit calendrier, une liste de chose à faire...');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ADV', 'Avancé', NULL, NULL, 'Menu avancé', NULL, NULL, 'ME', 'Menu regroupant la création de rapport, la vérification de la comptabilité...');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('MENUFIN', 'Trésorerie', NULL, NULL, 'Menu Financier', NULL, NULL, 'ME', 'Regroupement pour les menus de trésorerie (nouvelle opération, historique...)');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('FOLLOW', 'Liste Suivi', 'action.inc.php', NULL, 'Document de suivi sous forme de liste', NULL, NULL, 'ME', 'Liste de vos suivis, en fait de tous les documents, réunions ... dont vous avez besoin afin de suivre vos clients, fournisseurs ou administrations. Il permet la génération de documents comme les devis, les bordereau de livraison...');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGPAY', 'Moyen de paiement', 'payment_middle.inc.php', NULL, 'Config. des méthodes de paiement', NULL, NULL, 'ME', 'Configuration des moyens de paiements que vous voulez utiliser dans les journaux de type VEN ou ACH, les moyens de paiement permettent de générer l''opération de trésorerie en même temps que l''achat, la note de frais ou la vente');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ADM', 'Administration', 'adm.inc.php', NULL, 'Suivi administration, banque', NULL, NULL, 'ME', 'Suivi des administrations : courrrier, déclarations.');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('FORECAST', 'Prévision', 'forecast.inc.php', NULL, 'Prévision', NULL, NULL, 'ME', 'Prévision de vos achats, revenus, permet de suivre l''évolution de votre société. Vos prévisions sont des formules sur les postes comptables et vous permettent aussi vos marges brutes.');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:Reconciliation', 'Export opérations rapprochées', 'export_rec_csv.php', NULL, 'Export opérations rapprochées en CSV', NULL, NULL, 'PR', '');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('MANAGER', 'Administrateur', 'manager.inc.php', NULL, 'Suivi des gérants, administrateurs et salariés', NULL, NULL, 'ME', 'Suivi de vos salariés, managers ainsi que des administrateurs, pour les documents et les opérations comptables');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGDEFMENU', 'Menu par défaut', 'default_menu.inc.php', NULL, 'Configuration des menus par défaut', NULL, NULL, 'ME', 'Configuration des menus par défaut, ces menus sont appelés par des actions dans d''autres menus');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('AGENDA', 'Agenda', 'calendar.inc.php', NULL, 'Agenda', NULL, NULL, 'ME', 'Agenda, présentation du suivi sous forme d''agenda ');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('PDF:AncReceipt', 'Export pièce PDF', 'export_anc_receipt_pdf.php', NULL, NULL, NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('ANCKEY', 'Clef de répartition', 'anc_key.inc.php', NULL, NULL, NULL, NULL, 'ME', 'Permet de gèrer les clefs de répartition en comptabilité analytique');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CFGPLUGIN', 'Configuration extension', 'cfgplugin.inc.php', NULL, NULL, NULL, NULL, 'ME', 'Permet d''installer et d''activer facilement des extensions');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('BOOKMARK', 'Favori &#9733 ', NULL, NULL, 'Raccourci vers vos menus préférés', NULL, 'show_bookmark(<DOSSIER>)', 'ME', 'Ce menu vous présente  un menu rapide de vos menus préférés');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('BALAGE', 'Balance agée', 'balance_age.inc.php', NULL, 'Balance agée', NULL, NULL, 'ME', 'Balance agée pour les clients et fournisseurs');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('CSV:balance_age', 'Export Balance agée', 'export_balance_age_csv.php', NULL, 'Balance agée', NULL, NULL, 'PR', 'Balance agée pour les clients et fournisseurs');
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('RAW:receipt', 'Exporte la pièce', 'export_receipt.php', NULL, 'export la pièce justificative d''une opération', NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('RAW:document', 'Export le document', 'export_document.php', NULL, 'exporte le document d''un événement', NULL, NULL, 'PR', NULL);
INSERT INTO menu_ref (me_code, me_menu, me_file, me_url, me_description, me_parameter, me_javascript, me_type, me_description_etendue) VALUES ('RAW:document_template', 'Exporte le modèle de document', 'export_document_template.php', NULL, 'export le modèle de document utilisé dans le suivi', NULL, NULL, 'PR', NULL);



INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_fd_id, mp_qcode, jrn_def_id) VALUES (2, 'Caisse', 1, NULL, NULL, 2);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_fd_id, mp_qcode, jrn_def_id) VALUES (1, 'Paiement électronique', 1, NULL, NULL, 2);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_fd_id, mp_qcode, jrn_def_id) VALUES (4, 'Caisse', 1, NULL, NULL, 3);
INSERT INTO mod_payment (mp_id, mp_lib, mp_jrn_def_id, mp_fd_id, mp_qcode, jrn_def_id) VALUES (3, 'Par gérant ou administrateur', 2, NULL, NULL, 3);



SELECT pg_catalog.setval('mod_payment_mp_id_seq', 10, true);



SELECT pg_catalog.setval('op_def_op_seq', 1, false);









SELECT pg_catalog.setval('op_predef_detail_opd_id_seq', 1, false);






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
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_DATE_SUGGEST', 'Y');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_ALPHANUM', 'N');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_CHECK_PERIODE', 'N');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_UPDLAB', 'N');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_STOCK', 'N');



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
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('DNA', '67', 'Dépense non déductible');



INSERT INTO parm_money (pm_id, pm_code, pm_rate) VALUES (1, 'EUR', 1.0000);



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



SELECT pg_catalog.setval('plan_analytique_pa_id_seq', 1, false);



SELECT pg_catalog.setval('poste_analytique_po_id_seq', 1, false);



INSERT INTO profile_menu_type (pm_type, pm_desc) VALUES ('P', 'Impression');
INSERT INTO profile_menu_type (pm_type, pm_desc) VALUES ('S', 'Extension');
INSERT INTO profile_menu_type (pm_type, pm_desc) VALUES ('E', 'Menu');
INSERT INTO profile_menu_type (pm_type, pm_desc) VALUES ('M', 'Module');



INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (59, 'CFGPAY', 'DIVPARM', 1, 40, 'E', 0, 56);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (68, 'CFGATCARD', 'DIVPARM', 1, 90, 'E', 0, 56);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (61, 'CFGACC', 'DIVPARM', 1, 60, 'E', 0, 56);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (54, 'COMPANY', 'PARAM', 1, 10, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (2, 'ANC', NULL, 1, 500, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (526, 'PRINTGL', 'PRINT', 1, 200, 'E', 0, 6);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (523, 'PRINTBAL', 'PRINT', 1, 500, 'E', 0, 6);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (529, 'PRINTREPORT', 'PRINT', 1, 850, 'E', 0, 6);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (527, 'PRINTJRN', 'PRINT', 1, 100, 'E', 0, 6);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (530, 'PRINTREC', 'PRINT', 1, 1000, 'E', 0, 6);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (524, 'PRINTBILAN', 'PRINT', 1, 900, 'E', 0, 6);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (686, 'PRINTREPORT', 'PRINT', 2, 850, 'E', 0, 716);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (651, 'ANCHOP', 'ANCIMP', 1, 100, 'E', 0, 78);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (173, 'COMPTA', NULL, 1, 400, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (55, 'PERIODE', 'PARAM', 1, 20, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (56, 'DIVPARM', 'PARAM', 1, 30, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (652, 'ANCGL', 'ANCIMP', 1, 200, 'E', 0, 78);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (60, 'CFGTVA', 'DIVPARM', 1, 50, 'E', 0, 56);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (653, 'ANCBS', 'ANCIMP', 1, 300, 'E', 0, 78);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (654, 'ANCBC2', 'ANCIMP', 1, 400, 'E', 0, 78);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (655, 'ANCTAB', 'ANCIMP', 1, 500, 'E', 0, 78);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (656, 'ANCBCC', 'ANCIMP', 1, 600, 'E', 0, 78);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (657, 'ANCGR', 'ANCIMP', 1, 700, 'E', 0, 78);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (658, 'CSV:AncGrandLivre', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (662, 'new_line', NULL, 1, 350, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (67, 'CFGCATDOC', 'DIVPARM', 1, 80, 'E', 0, 56);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (69, 'CFGPCMN', 'PARAM', 1, 40, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (23, 'LET', 'COMPTA', 1, 80, 'E', 0, 173);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (72, 'PREDOP', 'PARAM', 1, 70, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (75, 'PLANANC', 'ANC', 1, 10, 'E', 0, 2);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (65, 'CFGCARDCAT', 'DIVPARM', 1, 70, 'E', 0, 56);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (76, 'ANCODS', 'ANC', 1, 20, 'E', 0, 2);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (77, 'ANCGROUP', 'ANC', 1, 30, 'E', 0, 2);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (78, 'ANCIMP', 'ANC', 1, 40, 'E', 0, 2);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (45, 'PARAM', NULL, 1, 200, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (79, 'PREFERENCE', NULL, 1, 150, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (37, 'CUST', 'GESTION', 1, 10, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (38, 'SUPPL', 'GESTION', 1, 20, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (39, 'ADM', 'GESTION', 1, 30, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (40, 'STOCK', 'GESTION', 1, 50, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (41, 'FORECAST', 'GESTION', 1, 70, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (42, 'FOLLOW', 'GESTION', 1, 80, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (29, 'VERIFBIL', 'ADV', 1, 210, 'E', 0, 28);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (31, 'PREDOP', 'ADV', 1, 230, 'E', 0, 28);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (32, 'OPEN', 'ADV', 1, 240, 'E', 0, 28);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (33, 'REPORT', 'ADV', 1, 250, 'E', 0, 28);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (43, 'HIST', 'COMPTA', 1, 10, 'E', 0, 173);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (28, 'ADV', 'COMPTA', 1, 200, 'E', 0, 173);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (688, 'PLANANC', 'ANC', 2, 10, 'E', 0, 727);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (690, 'ANCODS', 'ANC', 2, 20, 'E', 0, 727);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (528, 'PRINTPOSTE', 'PRINT', 1, 300, 'E', 0, 6);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (683, 'PRINTGL', 'PRINT', 2, 200, 'E', 0, 716);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (685, 'PRINTBAL', 'PRINT', 2, 500, 'E', 0, 716);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (694, 'PRINTJRN', 'PRINT', 2, 100, 'E', 0, 716);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (695, 'PRINTREC', 'PRINT', 2, 1000, 'E', 0, 716);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (696, 'PRINTBILAN', 'PRINT', 2, 900, 'E', 0, 716);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (53, 'ACCESS', NULL, 1, 250, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (785, 'STOCK_HISTO', 'STOCK', 1, 10, 'E', NULL, 30);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (820, 'STOCK_HISTO', 'STOCK', 1, 10, 'E', NULL, 40);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (786, 'STOCK_STATE', 'STOCK', 1, 20, 'E', NULL, 30);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (821, 'STOCK_STATE', 'STOCK', 1, 20, 'E', NULL, 40);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (789, 'STOCK_INVHISTO', 'STOCK', 1, 30, 'E', NULL, 30);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (822, 'STOCK_INVHISTO', 'STOCK', 1, 30, 'E', NULL, 40);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (795, 'STOCK_INV', 'STOCK', 1, 30, 'E', NULL, 30);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (823, 'STOCK_INV', 'STOCK', 1, 30, 'E', NULL, 40);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (824, 'PRINTGL', 'PRINT', 1, 200, 'E', 0, 35);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (825, 'PRINTBAL', 'PRINT', 1, 500, 'E', 0, 35);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (826, 'PRINTREPORT', 'PRINT', 1, 850, 'E', 0, 35);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (827, 'PRINTJRN', 'PRINT', 1, 100, 'E', 0, 35);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (828, 'PRINTREC', 'PRINT', 1, 1000, 'E', 0, 35);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (829, 'PRINTBILAN', 'PRINT', 1, 900, 'E', 0, 35);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (830, 'PRINTPOSTE', 'PRINT', 1, 300, 'E', 0, 35);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (816, 'BALAGE', 'PRINT', 1, 550, 'E', 0, 6);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (831, 'BALAGE', 'PRINT', 1, 550, 'E', 0, 35);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (787, 'STOCK_HISTO', 'STOCK', 2, 10, 'E', NULL, 702);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (832, 'STOCK_HISTO', 'STOCK', 2, 10, 'E', NULL, 706);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (788, 'STOCK_STATE', 'STOCK', 2, 20, 'E', NULL, 702);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (833, 'STOCK_STATE', 'STOCK', 2, 20, 'E', NULL, 706);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (790, 'STOCK_INVHISTO', 'STOCK', 2, 30, 'E', NULL, 702);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (834, 'STOCK_INVHISTO', 'STOCK', 2, 30, 'E', NULL, 706);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (796, 'STOCK_INV', 'STOCK', 2, 30, 'E', NULL, 702);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (835, 'STOCK_INV', 'STOCK', 2, 30, 'E', NULL, 706);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (836, 'PRINTREPORT', 'PRINT', 2, 850, 'E', 0, 719);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (837, 'PRINTGL', 'PRINT', 2, 200, 'E', 0, 719);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (838, 'PRINTBAL', 'PRINT', 2, 500, 'E', 0, 719);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (839, 'PRINTJRN', 'PRINT', 2, 100, 'E', 0, 719);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (840, 'PRINTREC', 'PRINT', 2, 1000, 'E', 0, 719);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (841, 'PRINTBILAN', 'PRINT', 2, 900, 'E', 0, 719);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (769, 'PRINTPOSTE', 'PRINT', 2, 300, 'E', 0, 716);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (842, 'PRINTPOSTE', 'PRINT', 2, 300, 'E', 0, 719);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (817, 'BALAGE', 'PRINT', 2, 550, 'E', 0, 716);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (843, 'BALAGE', 'PRINT', 2, 550, 'E', 0, 719);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (123, 'CSV:histo', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (20, 'LOGOUT', NULL, 1, 300, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (35, 'PRINT', 'GESTION', 1, 40, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (124, 'CSV:ledger', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (125, 'PDF:ledger', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (6, 'PRINT', 'COMPTA', 1, 60, 'E', 0, 173);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (126, 'CSV:postedetail', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (3, 'MENUACH', 'COMPTA', 1, 30, 'E', 0, 173);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (34, 'GESTION', NULL, 1, 450, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (18, 'MENUODS', 'COMPTA', 1, 50, 'E', 0, 173);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (88, 'ODS', 'MENUODS', 1, 10, 'E', 0, 18);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (4, 'VENMENU', 'COMPTA', 1, 20, 'E', 0, 173);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (90, 'VEN', 'VENMENU', 1, 10, 'E', 0, 4);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (19, 'FIN', 'MENUFIN', 1, 10, 'E', 0, 92);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (73, 'CFGDOC', 'PARAM', 1, 80, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (74, 'CFGLED', 'PARAM', 1, 90, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (71, 'CFGSEC', 'PARAM', 1, 60, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (82, 'EXT', NULL, 1, 550, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (95, 'FREC', 'MENUFIN', 1, 40, 'E', 0, 92);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (94, 'FSALDO', 'MENUFIN', 1, 30, 'E', 0, 92);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (27, 'LETACC', 'LET', 1, 20, 'E', 0, 23);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (24, 'LETCARD', 'LET', 1, 10, 'E', 0, 23);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (167, 'MOD', 'PARAM', 1, 10, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (92, 'MENUFIN', 'COMPTA', 1, 40, 'E', 0, 173);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (151, 'SEARCH', NULL, 1, 600, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (85, 'ACH', 'MENUACH', 1, 10, 'E', 0, 3);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (127, 'PDF:postedetail', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (128, 'CSV:fichedetail', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (129, 'PDF:fichedetail', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (130, 'CSV:fiche_balance', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (131, 'PDF:fiche_balance', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (132, 'CSV:report', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (133, 'PDF:report', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (134, 'CSV:fiche', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (135, 'PDF:fiche', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (136, 'CSV:glcompte', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (137, 'PDF:glcompte', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (138, 'PDF:sec', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (139, 'CSV:AncList', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (140, 'CSV:AncBalSimple', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (141, 'PDF:AncBalSimple', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (142, 'CSV:AncBalDouble', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (143, 'PDF:AncBalDouble', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (144, 'CSV:balance', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (145, 'PDF:balance', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (146, 'CSV:AncTable', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (147, 'CSV:AncAccList', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (148, 'CSV:AncBalGroup', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (149, 'OTH:Bilan', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (1, 'DASHBOARD', NULL, 1, 100, 'M', 1, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (172, 'CFGPRO', 'MOD', 1, NULL, 'E', 0, 167);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (171, 'CFGMENU', 'MOD', 1, NULL, 'E', 0, 167);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (86, 'ACHISTO', 'MENUACH', 1, 20, 'E', 0, 3);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (91, 'VEHISTO', 'VENMENU', 1, 20, 'E', 0, 4);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (781, 'BK', 'GESTION', 1, 35, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (783, 'CSV:ActionGestion', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (784, 'CFGSTOCK', 'PARAM', 1, 40, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (30, 'STOCK', 'COMPTA', 1, 90, 'E', 0, 173);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (791, 'CSV:StockHisto', NULL, 1, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (792, 'CSV:StockResmList', NULL, 1, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (779, 'CSV:reportinit', NULL, 1, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (797, 'CFGDOCST', 'DIVPARM', 1, 9, 'E', 0, 56);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (36, 'CARD', 'GESTION', 1, 60, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (5, 'CARD', 'COMPTA', 1, 70, 'E', 0, 173);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (525, 'CFGCARD', 'PARAM', 1, 400, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (93, 'FIHISTO', 'MENUFIN', 1, 20, 'E', 0, 92);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (89, 'ODHISTO', 'MENUODS', 1, 20, 'E', 0, 18);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (799, 'NAVI', NULL, 1, 90, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (801, 'BOOKMARK', NULL, 1, 85, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (803, 'CFGTAG', 'PARAM', 1, 390, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (804, 'CSV:Reconciliation', NULL, 1, 0, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (806, 'MANAGER', 'GESTION', 1, 25, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (808, 'CFGDEFMENU', 'MOD', 1, 30, 'E', 0, 167);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (809, 'AGENDA', 'NULL', 1, 410, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (811, 'PDF:AncReceipt', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (812, 'ANCKEY', 'ANC', 1, 15, 'E', 0, 2);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (814, 'CFGPLUGIN', 'PARAM', 1, 15, 'E', 0, 45);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (818, 'CSV:balance_age', NULL, 1, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (668, 'ANCHOP', 'ANCIMP', 2, 100, 'E', 0, 692);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (669, 'COMPTA', NULL, 2, 400, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (672, 'ANCGL', 'ANCIMP', 2, 200, 'E', 0, 692);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (674, 'ANCBS', 'ANCIMP', 2, 300, 'E', 0, 692);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (675, 'ANCBC2', 'ANCIMP', 2, 400, 'E', 0, 692);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (676, 'ANCTAB', 'ANCIMP', 2, 500, 'E', 0, 692);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (677, 'ANCBCC', 'ANCIMP', 2, 600, 'E', 0, 692);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (678, 'ANCGR', 'ANCIMP', 2, 700, 'E', 0, 692);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (679, 'CSV:AncGrandLivre', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (680, 'new_line', NULL, 2, 350, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (684, 'LET', 'COMPTA', 2, 80, 'E', 0, 669);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (717, 'CSV:ledger', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (718, 'PDF:ledger', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (719, 'PRINT', 'COMPTA', 2, 60, 'E', 0, 669);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (720, 'CSV:postedetail', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (721, 'MENUACH', 'COMPTA', 2, 30, 'E', 0, 669);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (723, 'GESTION', NULL, 2, 450, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (724, 'MENUODS', 'COMPTA', 2, 50, 'E', 0, 669);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (725, 'ODS', 'MENUODS', 2, 10, 'E', 0, 724);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (727, 'ANC', NULL, 2, 500, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (728, 'VENMENU', 'COMPTA', 2, 20, 'E', 0, 669);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (729, 'VEN', 'VENMENU', 2, 10, 'E', 0, 728);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (731, 'FIN', 'MENUFIN', 2, 10, 'E', 0, 742);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (735, 'EXT', NULL, 2, 550, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (736, 'FREC', 'MENUFIN', 2, 40, 'E', 0, 742);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (737, 'FSALDO', 'MENUFIN', 2, 30, 'E', 0, 742);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (738, 'LETACC', 'LET', 2, 20, 'E', 0, 684);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (691, 'ANCGROUP', 'ANC', 2, 30, 'E', 0, 727);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (692, 'ANCIMP', 'ANC', 2, 40, 'E', 0, 727);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (697, 'PREFERENCE', NULL, 2, 150, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (698, 'CUST', 'GESTION', 2, 10, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (699, 'SUPPL', 'GESTION', 2, 20, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (700, 'ADM', 'GESTION', 2, 30, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (702, 'STOCK', 'GESTION', 2, 50, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (703, 'FORECAST', 'GESTION', 2, 70, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (704, 'FOLLOW', 'GESTION', 2, 80, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (705, 'VERIFBIL', 'ADV', 2, 210, 'E', 0, 712);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (707, 'PREDOP', 'ADV', 2, 230, 'E', 0, 712);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (708, 'OPEN', 'ADV', 2, 240, 'E', 0, 712);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (709, 'REPORT', 'ADV', 2, 250, 'E', 0, 712);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (711, 'HIST', 'COMPTA', 2, 10, 'E', 0, 669);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (712, 'ADV', 'COMPTA', 2, 200, 'E', 0, 669);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (713, 'ACCESS', NULL, 2, 250, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (714, 'CSV:histo', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (715, 'LOGOUT', NULL, 2, 300, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (716, 'PRINT', 'GESTION', 2, 40, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (739, 'LETCARD', 'LET', 2, 10, 'E', 0, 684);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (742, 'MENUFIN', 'COMPTA', 2, 40, 'E', 0, 669);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (744, 'SEARCH', NULL, 2, 600, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (745, 'ACH', 'MENUACH', 2, 10, 'E', 0, 721);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (746, 'PDF:postedetail', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (747, 'CSV:fichedetail', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (748, 'PDF:fichedetail', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (749, 'CSV:fiche_balance', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (750, 'PDF:fiche_balance', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (751, 'CSV:report', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (752, 'PDF:report', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (722, 'ACHISTO', 'MENUACH', 2, 20, 'E', 0, 721);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (730, 'VEHISTO', 'VENMENU', 2, 20, 'E', 0, 728);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (753, 'CSV:fiche', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (754, 'PDF:fiche', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (755, 'CSV:glcompte', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (756, 'PDF:glcompte', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (757, 'PDF:sec', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (758, 'CSV:AncList', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (759, 'CSV:AncBalSimple', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (760, 'PDF:AncBalSimple', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (761, 'CSV:AncBalDouble', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (762, 'PDF:AncBalDouble', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (763, 'CSV:balance', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (764, 'PDF:balance', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (765, 'CSV:AncTable', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (766, 'CSV:AncAccList', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (767, 'CSV:AncBalGroup', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (768, 'OTH:Bilan', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (772, 'DASHBOARD', NULL, 2, 100, 'M', 1, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (782, 'BK', 'GESTION', 2, 35, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (706, 'STOCK', 'COMPTA', 2, 90, 'E', 0, 669);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (793, 'CSV:StockHisto', NULL, 2, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (794, 'CSV:StockResmList', NULL, 2, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (780, 'CSV:reportinit', NULL, 2, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (798, 'CFGDOCST', 'DIVPARM', 2, 9, 'E', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (701, 'CARD', 'GESTION', 2, 60, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (710, 'CARD', 'COMPTA', 2, 70, 'E', 0, 669);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (770, 'CFGCARD', 'PARAM', 2, 400, 'E', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (743, 'FIHISTO', 'MENUFIN', 2, 20, 'E', 0, 742);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (726, 'ODHISTO', 'MENUODS', 2, 20, 'E', 0, 724);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (800, 'NAVI', NULL, 2, 90, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (802, 'BOOKMARK', NULL, 2, 85, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (805, 'CSV:Reconciliation', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (807, 'MANAGER', 'GESTION', 2, 25, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (810, 'AGENDA', 'NULL', 2, 410, 'M', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (813, 'ANCKEY', 'ANC', 2, 15, 'E', 0, 727);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (815, 'CFGPLUGIN', 'PARAM', 2, 15, 'E', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (819, 'CSV:balance_age', NULL, 2, NULL, 'P', 0, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (844, 'CONTACT', 'GESTION', 1, 22, 'E', 0, 34);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (845, 'CONTACT', 'GESTION', 2, 22, 'E', 0, 723);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (846, 'RAW:receipt', NULL, 1, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (847, 'RAW:receipt', NULL, 2, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (848, 'RAW:document', NULL, 1, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (849, 'RAW:document', NULL, 2, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (850, 'RAW:document_template', NULL, 1, NULL, 'P', NULL, NULL);
INSERT INTO profile_menu (pm_id, me_code, me_code_dep, p_id, p_order, p_type_display, pm_default, pm_id_dep) VALUES (851, 'RAW:document_template', NULL, 2, NULL, 'P', NULL, NULL);



SELECT pg_catalog.setval('profile_menu_pm_id_seq', 851, true);



SELECT pg_catalog.setval('profile_p_id_seq', 11, true);



INSERT INTO stock_repository (r_id, r_name, r_adress, r_country, r_city, r_phone) VALUES (1, 'Dépôt par défaut', NULL, NULL, NULL, NULL);



INSERT INTO profile_sec_repository (ur_id, p_id, r_id, ur_right) VALUES (1, 1, 1, 'W');
INSERT INTO profile_sec_repository (ur_id, p_id, r_id, ur_right) VALUES (2, 2, 1, 'W');



SELECT pg_catalog.setval('profile_sec_repository_ur_id_seq', 2, true);



INSERT INTO profile_user (user_name, pu_id, p_id) VALUES ('phpcompta', 1, 1);



SELECT pg_catalog.setval('profile_user_pu_id_seq', 6, true);






SELECT pg_catalog.setval('quant_fin_qf_id_seq', 1, false);



INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (1, '21%', 0.2100, 'Tva applicable à tout ce qui bien et service divers', '4111,4511', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (2, '12%', 0.1200, 'Tva ', '4112,4512', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (3, '6%', 0.0600, 'Tva applicable aux journaux et livres', '4113,4513', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (4, '0%', 0.0000, 'Aucune tva n''est applicable', '4114,4514', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (6, 'EXPORT', 0.0000, 'Tva pour les exportations', '41141,45144', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (5, 'INTRA', 0.0000, 'Tva pour les livraisons / acquisition intra communautaires', '41142,45142', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (7, 'COC', 0.0000, 'Opérations avec des cocontractants', '41144,45144', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (8, 'ART44', 0.0000, 'Opérations pour les opérations avec des assujettis à l\''art 44 Code TVA', '41143,45143', 0);









SELECT pg_catalog.setval('s_attr_def', 9001, false);



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



SELECT pg_catalog.setval('s_jnt_id', 56, true);



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



SELECT pg_catalog.setval('s_oa_group', 1, true);



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






SELECT pg_catalog.setval('stock_change_c_id_seq', 1, false);






SELECT pg_catalog.setval('stock_repository_r_id_seq', 1, true);



SELECT pg_catalog.setval('tags_t_id_seq', 1, false);



SELECT pg_catalog.setval('tmp_pcmn_id_seq', 1034, true);









SELECT pg_catalog.setval('tmp_stockgood_detail_d_id_seq', 1, false);



SELECT pg_catalog.setval('tmp_stockgood_s_id_seq', 1, false);









SELECT pg_catalog.setval('todo_list_shared_id_seq', 1, false);



SELECT pg_catalog.setval('todo_list_tl_id_seq', 1, false);






SELECT pg_catalog.setval('uos_pk_seq', 1, false);



INSERT INTO user_active_security (id, us_login, us_ledger, us_action) VALUES (1, 'phpcompta', 'Y', 'Y');



SELECT pg_catalog.setval('user_active_security_id_seq', 1, true);






SELECT pg_catalog.setval('user_filter_id_seq', 1, false);









INSERT INTO user_sec_action_profile (ua_id, p_id, p_granted, ua_right) VALUES (1, 1, 1, 'W');
INSERT INTO user_sec_action_profile (ua_id, p_id, p_granted, ua_right) VALUES (2, 1, 2, 'W');
INSERT INTO user_sec_action_profile (ua_id, p_id, p_granted, ua_right) VALUES (3, 1, -1, 'W');
INSERT INTO user_sec_action_profile (ua_id, p_id, p_granted, ua_right) VALUES (4, 2, 1, 'W');
INSERT INTO user_sec_action_profile (ua_id, p_id, p_granted, ua_right) VALUES (5, 2, 2, 'W');
INSERT INTO user_sec_action_profile (ua_id, p_id, p_granted, ua_right) VALUES (6, 2, -1, 'W');



SELECT pg_catalog.setval('user_sec_action_profile_ua_id_seq', 6, true);






INSERT INTO version (val, v_description, v_date) VALUES (126, NULL, NULL);
INSERT INTO version (val, v_description, v_date) VALUES (127, 'Add filter for search, inactive tag or ledger, type of operation, security', '2018-02-10 22:46:38.653432');
INSERT INTO version (val, v_description, v_date) VALUES (128, 'Add a view to manage VAT', '2018-02-10 22:46:39.22354');



