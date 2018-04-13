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



INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (13, 'Dépenses non admises', '674');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (14, 'Administration des Finances', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (15, 'Autres fiches', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (5, 'Prêt > a un an', '27');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (8, 'Fournisseurs', '400');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (6, 'Prêt < a un an', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (16, 'Contact', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (1, 'Vente Service', '706');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (2, 'Achat Marchandises', '603');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (9, 'Clients', '410');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (10, 'Salaire Administrateur', '644');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (11, 'Salaire Ouvrier', '641');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (12, 'Salaire Employé', '641');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (7, 'Matériel à amortir, immobilisation corporelle', '21');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (3, 'Achat Service et biens divers', '61');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (17, 'Escomptes accordées', '66');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (18, 'Produits Financiers', '76');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (25, 'Compte Salarié / Administrateur', NULL);
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (4, 'Trésorerie', '51');
INSERT INTO fiche_def_ref (frd_id, frd_text, frd_class_base) VALUES (26, 'Projet', NULL);



INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (500000, NULL, 'Stock', false, 15, NULL);
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (1, '604', 'Marchandises', true, 2, 'Achats de marchandises');
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (2, '410', 'Client', true, 9, 'Catégorie qui contient la liste des clients');
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (3, '51', 'Banque', true, 4, 'Catégorie qui contient la liste des comptes financiers: banque, caisse,...');
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (4, '400', 'Fournisseur', true, 8, 'Catégorie qui contient la liste des fournisseurs');
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (5, '61', 'Services & Biens Divers', true, 3, 'Catégorie qui contient la liste des charges diverses');
INSERT INTO fiche_def (fd_id, fd_class_base, fd_label, fd_create_account, frd_id, fd_description) VALUES (6, '700', 'Vente', true, 1, 'Catégorie qui contient la liste des prestations, marchandises... que l''on vend ');






INSERT INTO profile (p_name, p_id, p_desc, with_calc, with_direct_form) VALUES ('Administrateur', 1, 'Profil par défaut pour les adminstrateurs', true, true);
INSERT INTO profile (p_name, p_id, p_desc, with_calc, with_direct_form) VALUES ('Utilisateur', 2, 'Profil par défaut pour les utilisateurs', true, true);
INSERT INTO profile (p_name, p_id, p_desc, with_calc, with_direct_form) VALUES ('Public', -1, 'faux groupe', NULL, NULL);









SELECT pg_catalog.setval('action_detail_ad_id_seq', 1, false);



SELECT pg_catalog.setval('action_gestion_ag_id_seq', 1, false);






SELECT pg_catalog.setval('action_gestion_comment_agc_id_seq', 1, false);



INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('VEN', 'Vente');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('ACH', 'Achat');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('ODS', 'Opérations Diverses');
INSERT INTO jrn_type (jrn_type_id, jrn_desc) VALUES ('FIN', 'Banque');



INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref, jrn_def_bank, jrn_def_num_op, jrn_def_description, jrn_enable) VALUES (3, 'Achat', '', '', '5', '4', 10, 10, true, '''echeance''', 'ACH', 'A01', 'ACH', NULL, NULL, 'Concerne tous les achats, factures reçues, notes de crédit reçues et notes de frais', 1);
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref, jrn_def_bank, jrn_def_num_op, jrn_def_description, jrn_enable) VALUES (1, 'Financier', '', '', '3,2,4,5', '3,2,4,5', 10, 10, true, '''echeance''', 'FIN', 'F01', 'FIN', NULL, NULL, 'Concerne tous les mouvements financiers (comptes en banque, caisses, visa...)', 1);
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref, jrn_def_bank, jrn_def_num_op, jrn_def_description, jrn_enable) VALUES (4, 'Opération Diverses', NULL, NULL, NULL, NULL, 5, 5, false, NULL, 'ODS', 'O01', 'ODS', NULL, NULL, 'Concerne toutes les opérations comme les amortissements, les comptes TVA, ...', 1);
INSERT INTO jrn_def (jrn_def_id, jrn_def_name, jrn_def_class_deb, jrn_def_class_cred, jrn_def_fiche_deb, jrn_def_fiche_cred, jrn_deb_max_line, jrn_cred_max_line, jrn_def_ech, jrn_def_ech_lib, jrn_def_type, jrn_def_code, jrn_def_pj_pref, jrn_def_bank, jrn_def_num_op, jrn_def_description, jrn_enable) VALUES (2, 'Vente', '', '', '2', '6', 10, 10, true, '''echeance''', 'VEN', 'V01', 'VEN', NULL, NULL, 'Concerne toutes les ventes, notes de crédit envoyées', 1);









SELECT pg_catalog.setval('action_gestion_operation_ago_id_seq', 1, false);









SELECT pg_catalog.setval('action_gestion_related_aga_id_seq', 1, false);






SELECT pg_catalog.setval('action_person_ap_id_seq', 1, false);









SELECT pg_catalog.setval('action_tags_at_id_seq', 1, false);



INSERT INTO attr_def (ad_id, ad_text, ad_type, ad_size, ad_extra) VALUES (25, 'Société', 'card', '22', '[sql] frd_id in (4,8,9,14)');
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
INSERT INTO attr_min (frd_id, ad_id) VALUES (26, 1);
INSERT INTO attr_min (frd_id, ad_id) VALUES (26, 9);



INSERT INTO bilan (b_id, b_name, b_file_template, b_file_form, b_type) VALUES (5, 'Comptes de résultat', 'document/fr_fr/fr_plan_abrege_perso_cr1000.rtf', 'document/fr_fr/fr_plan_abrege_perso_cr1000.form', 'rtf');
INSERT INTO bilan (b_id, b_name, b_file_template, b_file_form, b_type) VALUES (1, 'Bilan français', 'document/fr_fr/fr_plan_abrege_perso_bil10000.ods', 'document/fr_fr/fr_plan_abrege_perso_bil10000.form', 'ods');
INSERT INTO bilan (b_id, b_name, b_file_template, b_file_form, b_type) VALUES (9, 'ASBL', 'document/fr_be/bnb-asbl.rtf', 'document/fr_be/bnb-asbl.form', 'RTF');



SELECT pg_catalog.setval('bilan_b_id_seq', 9, true);






SELECT pg_catalog.setval('bookmark_b_id_seq', 1, false);



SELECT pg_catalog.setval('bud_card_bc_id_seq', 1, false);



SELECT pg_catalog.setval('bud_detail_bd_id_seq', 1, false);



SELECT pg_catalog.setval('bud_detail_periode_bdp_id_seq', 1, false);



INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('101', 'Capital', '1', 'PAS', 159, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('105', 'Ecarts de réévaluation', '1', 'PAS', 160, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1061', 'Réserve légale', '1', 'PAS', 161, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1063', 'Réserves statutaires ou contractuelles', '1', 'PAS', 162, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1064', 'Réserves réglementées', '1', 'PAS', 163, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1068', 'Autres réserves', '1', 'PAS', 164, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('108', 'Compte de l''exploitant', '1', 'PAS', 165, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('145', 'Amortissements dérogatoires', '1', 'PAS', 167, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('146', 'Provision spéciale de réévaluation', '1', 'PAS', 168, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('147', 'Plus-values réinvesties', '1', 'PAS', 169, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('148', 'Autres provisions réglementées', '1', 'PAS', 170, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('201', 'Frais d''établissement', '20', 'ACT', 175, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('206', 'Droit au bail', '20', 'ACT', 176, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('207', 'Fonds commercial', '20', 'ACT', 177, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('208', 'Autres immobilisations incorporelles', '20', 'ACT', 178, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('280', 'Amortissements des immobilisations incorporelles', '2', 'ACT', 182, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('281', 'Amortissements des immobilisations corporelles', '2', 'ACT', 183, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('290', 'Provisions pour dépréciation des immobilisations incorporelles', '2', 'ACT', 184, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('291', 'Provisions pour dépréciation des immobilisations corporelles (même ventilation que celle du compte 21)', '2', 'ACT', 185, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('297', 'Provisions pour dépréciation des autres immobilisations financières', '2', 'ACT', 186, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('391', 'Provisions pour dépréciation des matières premières (et fournitures)', '3', 'ACT', 194, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('392', 'Provisions pour dépréciation des autres approvisionnements', '3', 'ACT', 195, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('393', 'Provisions pour dépréciation des en-cours de production de biens', '3', 'ACT', 196, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('394', 'Provisions pour dépréciation des en-cours de production de services', '3', 'ACT', 197, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('395', 'Provisions pour dépréciation des stocks de produits', '3', 'ACT', 198, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('397', 'Provisions pour dépréciation des stocks de marchandises', '3', 'ACT', 199, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('400', 'Fournisseurs et Comptes rattachés', '4', 'ACT', 201, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('409', 'Fournisseurs débiteurs', '4', 'ACT', 202, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('410', 'Clients et Comptes rattachés', '4', 'ACT', 203, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('419', 'Clients créditeurs', '4', 'ACT', 204, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('421', 'Personnel - Rémunérations dues', '4', 'PAS', 205, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('428', 'Personnel - Charges à payer et produits à recevoir', '4', 'PAS', 206, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('444', 'Etat - Impôts sur les bénéfices', '4', 'PAS', 208, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445', 'Etat - Taxes sur le chiffre d''affaires', '4', 'PAS', 209, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('447', 'Autres impôts, taxes et versements assimilés', '4', 'PAS', 210, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('455', 'Associés - Comptes courants', '45', 'PAS', 212, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('481', 'Charges à répartir sur plusieurs exercices', '4', 'PAS', 215, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('486', 'Charges constatées d''avance', '4', 'PAS', 216, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('487', 'Produits constatés d''avance', '4', 'ACT', 217, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('491', 'Provisions pour dépréciation des comptes de clients', '4', 'PAS', 218, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('496', 'Provisions pour dépréciation des comptes de débiteurs divers', '4', 'PAS', 219, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('590', 'Provisions pour dépréciation des valeurs mobilières de placement', '5', 'ACT', 226, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('603', 'variations des stocks (approvisionnements et marchandises)', '6', 'CHA', 229, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('641', 'Rémunérations du personnel', '6', 'CHA', 233, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('644', 'Rémunération du travail de l''exploitant', '6', 'CHA', 234, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('645', 'Charges de sécurité sociale et de prévoyance', '6', 'CHA', 235, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('646', 'Cotisations sociales personnelles de l''exploitant', '6', 'CHA', 236, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('681', 'Dotations aux amortissements et aux provisions - Charges d''exploitation', '6', 'CHA', 240, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('686', 'Dotations aux amortissements et aux provisions - Charges financières', '6', 'CHA', 241, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('687', 'Dotations aux amortissements et aux provisions - Charges exceptionnelles', '6', 'CHA', 242, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('691', 'Participation des salariés aux résultats', '6', 'CHA', 243, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('695', 'Impôts sur les bénéfices', '6', 'CHA', 244, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('697', 'Imposition forfaitaire annuelle des sociétés', '6', 'CHA', 245, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('699', 'Produits - Reports en arrière des déficits', '6', 'CHA', 246, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('701', 'Ventes de produits finis', '7', 'PAS', 248, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('706', 'Prestations de services', '7', 'PAS', 249, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('707', 'Ventes de marchandises', '7', 'PAS', 250, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('708', 'Produits des activités annexes', '7', 'PAS', 251, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('709', 'Rabais, remises et ristournes accordés par l''entreprise', '7', 'PAS', 252, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('713', 'Variation des stocks (en-cours de production, produits)', '7', 'PAS', 253, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('753', 'Jetons de présence et rémunérations d''administrateurs, gérants,...', '75', 'PAS', 258, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('754', 'Ristournes perçues des coopératives (provenant des excédents)', '75', 'PAS', 259, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('755', 'Quotes-parts de résultat sur opérations faites en commun', '75', 'PAS', 260, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('781', 'Reprises sur amortissements et provisions (à inscrire dans les produits d''exploitation)', '7', 'PAS', 263, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('786', 'Reprises sur provisions pour risques (à inscrire dans les produits financiers)', '7', 'PAS', 264, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('787', 'Reprises sur provisions (à inscrire dans les produits exceptionnels)', '7', 'PAS', 265, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4456601', 'TVA 19,6% - France métropolitaine - Taux immobilisations Déductible', '4456', 'ACT', 269, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445701', 'TVA 19,6% - France métropolitaine - Taux immobilisations Collectée ', '4457', 'PAS', 270, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4456602', 'TVA x% - France métropolitaine - Taux anciens Déductible', '4456', 'ACT', 271, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445702', 'TVA x% - France métropolitaine - Taux anciens Collectée ', '4457', 'PAS', 272, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4456603', 'TVA 8,5%  - DOM - Taux normal Déductible', '4456', 'ACT', 273, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445703', 'TVA 8,5%  - DOM - Taux normal Collectée ', '4457', 'PAS', 274, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4456604', 'TVA 8,5% - DOM - Taux normal NPR Déductible', '4456', 'ACT', 275, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445704', 'TVA 8,5% - DOM - Taux normal NPR Collectée ', '4457', 'PAS', 276, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4456605', 'TVA 2,1% - DOM - Taux réduit Déductible', '4456', 'ACT', 277, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445705', 'TVA 2,1% - DOM - Taux réduit Collectée ', '4457', 'PAS', 278, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4456606', 'TVA 1,75% - DOM - Taux I Déductible', '4456', 'ACT', 279, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445706', 'TVA 1,75% - DOM - Taux I Collectée ', '4457', 'PAS', 280, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4456607', 'TVA 1,05% - DOM - Taux publications de presse Déductible', '4456', 'ACT', 281, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445707', 'TVA 1,05% - DOM - Taux publications de presse Collectée ', '4457', 'PAS', 282, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4456608', 'TVA x% - DOM - Taux octroi de mer Déductible', '4456', 'ACT', 283, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445708', 'TVA x% - DOM - Taux octroi de mer Collectée ', '4457', 'PAS', 284, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4456609', 'TVA x% - DOM - Taux immobilisations Déductible', '4456', 'ACT', 285, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445709', 'TVA x% - DOM - Taux immobilisations Collectée ', '4457', 'PAS', 286, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566010', 'TVA 13% - Corse - Taux I Déductible', '4456', 'ACT', 287, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457010', 'TVA 13% - Corse - Taux I Collectée ', '4457', 'PAS', 288, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566011', 'TVA 8% - Corse - Taux II Déductible', '4456', 'ACT', 289, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457011', 'TVA 8% - Corse - Taux II Collectée ', '4457', 'PAS', 290, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566012', 'TVA 2,1% - Corse - Taux III Déductible', '4456', 'ACT', 291, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457012', 'TVA 2,1% - Corse - Taux III Collectée ', '4457', 'PAS', 292, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566013', 'TVA 0,9% - Corse - Taux IV Déductible', '4456', 'ACT', 293, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457013', 'TVA 0,9% - Corse - Taux IV Collectée ', '4457', 'PAS', 294, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566014', 'TVA x% - Corse - Taux immobilisations Déductible', '4456', 'ACT', 295, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457014', 'TVA x% - Corse - Taux immobilisations Collectée ', '4457', 'PAS', 296, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566015', 'TVA x% - Acquisitions intracommunautaires/Pays Déductible', '4456', 'ACT', 297, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457015', 'TVA x% - Acquisitions intracommunautaires/Pays Collectée ', '4457', 'PAS', 298, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566016', 'TVA x% - Acquisitions intracommunautaires immobilisations/Pays Déductible', '4456', 'ACT', 299, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457016', 'TVA x% - Acquisitions intracommunautaires immobilisations/Pays Collectée ', '4457', 'PAS', 300, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566017', 'TVA x% - Non imposable : Achats en franchise Déductible', '4456', 'ACT', 301, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457017', 'TVA x% - Non imposable : Achats en franchise Collectée ', '4457', 'PAS', 302, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566018', 'TVA x% - Non imposable : Exports hors CE/Pays Déductible', '4456', 'ACT', 303, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457018', 'TVA x% - Non imposable : Exports hors CE/Pays Collectée ', '4457', 'PAS', 304, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566019', 'TVA x% - Non imposable : Autres opérations Déductible', '4456', 'ACT', 305, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457019', 'TVA x% - Non imposable : Autres opérations Collectée ', '4457', 'PAS', 306, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44566020', 'TVA x% - Non imposable : Livraisons intracommunautaires/Pays Déductible', '4456', 'ACT', 307, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4457020', 'TVA x% - Non imposable : Livraisons intracommunautaires/Pays Collectée ', '4457', 'PAS', 308, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445661', 'TVA 19,6% - France métropolitaine - Taux normal', '445', 'PAS', 309, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445662', 'TVA 5,5% - France métropolitaine - Taux réduit', '445', 'PAS', 310, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('445663', 'TVA 2,1% - France métropolitaine - Taux super réduit', '445', 'PAS', 311, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44571', 'TVA 19,6% - France métropolitaine - Taux normal', '445', 'ACT', 312, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44572', 'TVA 5,5% - France métropolitaine - Taux réduit', '445', 'ACT', 313, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('44573', 'TVA 2,1% - France métropolitaine - Taux super réduit', '445', 'ACT', 314, 'Y');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('53', 'Caisse', '5', 'ACT', 223, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('1', 'comptes de capitaux', '0', 'PAS', 158, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('12', 'résultat de l''exercice (bénéfice ou perte)', '1', 'CON', 166, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('15', 'Provisions pour risques et charges', '1', 'PAS', 171, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('16', 'emprunts et dettes assimilees', '1', 'PAS', 172, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('2', 'comptes d''immobilisations', '0', 'ACT', 173, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('20', 'immobilisations incorporelles', '2', 'ACT', 174, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('21', 'immobilisations corporelles', '2', 'ACT', 179, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('23', 'immobilisations en cours', '2', 'ACT', 180, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('27', 'autres immobilisations financieres', '2', 'ACT', 181, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('3', 'comptes de stocks et en cours', '0', 'ACT', 187, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('31', 'matieres premières (et fournitures)', '3', 'ACT', 188, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('32', 'autres approvisionnements', '3', 'ACT', 189, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('33', 'en-cours de production de biens', '3', 'ACT', 190, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('34', 'en-cours de production de services', '3', 'ACT', 191, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('35', 'stocks de produits', '3', 'ACT', 192, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('37', 'stocks de marchandises', '3', 'ACT', 193, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('4', 'comptes de tiers', '0', 'CON', 200, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('43', 'Sécurité sociale et autres organismes sociaux', '4', 'PAS', 207, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('45', 'Groupe et associes', '4', 'PAS', 211, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('46', 'Débiteurs divers et créditeurs divers', '4', 'CON', 213, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('47', 'comptes transitoires ou d''attente', '4', 'CON', 214, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('5', 'comptes financiers', '0', 'ACT', 220, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('50', 'valeurs mobilières de placement', '5', 'ACT', 221, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('51', 'banques, établissements financiers et assimilés', '5', 'ACT', 222, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('54', 'régies d''avance et accréditifs', '5', 'ACT', 224, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('58', 'virements internes', '5', 'ACT', 225, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('6', 'comptes de charges', '0', 'CHA', 227, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('60', 'Achats (sauf 603)', '6', 'CHA', 228, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('61', 'autres charges externes - Services extérieurs', '6', 'CHA', 230, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('62', 'autres charges externes - Autres services extérieurs', '6', 'CHA', 231, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('63', 'Impôts, taxes et versements assimiles', '6', 'CHA', 232, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('65', 'Autres charges de gestion courante', '6', 'CHA', 237, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('66', 'Charges financières', '6', 'CHA', 238, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('67', 'Charges exceptionnelles', '6', 'CHA', 239, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('7', 'comptes de produits', '0', 'PAS', 247, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('72', 'Production immobilisée', '7', 'PAS', 254, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('73', 'Produits nets partiels sur opérations à long terme', '7', 'PAS', 255, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('74', 'Subventions d''exploitation', '7', 'PAS', 256, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('75', 'Autres produits de gestion courante', '7', 'PAS', 257, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('76', 'Produits financiers', '7', 'PAS', 261, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('77', 'Produits exceptionnels', '7', 'PAS', 262, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('79', 'Transferts de charges', '7', 'PAS', 266, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('8', 'Comptes spéciaux', '0', 'CON', 267, 'N');
INSERT INTO tmp_pcmn (pcm_val, pcm_lib, pcm_val_parent, pcm_type, id, pcm_direct_use) VALUES ('9', 'Comptes analytiques', '0', 'CON', 268, 'N');









SELECT pg_catalog.setval('del_action_del_id_seq', 1, true);






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






SELECT pg_catalog.setval('extension_ex_id_seq', 1, false);






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
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (500000, 1, 54, 10);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (500000, 9, 55, 20);
INSERT INTO jnt_fic_attr (fd_id, ad_id, jnt_id, jnt_order) VALUES (500000, 23, 56, 30);






SELECT pg_catalog.setval('jnt_letter_jl_id_seq', 1, false);






SELECT pg_catalog.setval('jrn_info_ji_id_seq', 1, false);






SELECT pg_catalog.setval('jrn_note_n_id_seq', 1, false);



INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 105, 'OP', 1);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 105, 'OP', 2);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 105, 'OP', 3);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 105, 'OP', 4);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 106, 'OP', 5);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 106, 'OP', 6);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 106, 'OP', 7);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 106, 'OP', 8);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 107, 'OP', 9);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 107, 'OP', 10);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 107, 'OP', 11);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 107, 'OP', 12);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 108, 'OP', 13);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 108, 'OP', 14);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 108, 'OP', 15);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 108, 'OP', 16);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 109, 'OP', 17);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 109, 'OP', 18);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 109, 'OP', 19);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 109, 'OP', 20);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 110, 'OP', 21);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 110, 'OP', 22);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 110, 'OP', 23);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 110, 'OP', 24);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 111, 'OP', 25);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 111, 'OP', 26);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 111, 'OP', 27);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 111, 'OP', 28);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 112, 'OP', 29);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 112, 'OP', 30);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 112, 'OP', 31);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 112, 'OP', 32);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 113, 'OP', 33);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 113, 'OP', 34);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 113, 'OP', 35);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 113, 'OP', 36);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 114, 'OP', 37);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 114, 'OP', 38);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 114, 'OP', 39);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 114, 'OP', 40);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 115, 'OP', 41);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 115, 'OP', 42);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 115, 'OP', 43);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 115, 'OP', 44);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 116, 'OP', 45);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 116, 'OP', 46);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 116, 'OP', 47);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 116, 'OP', 48);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (4, 117, 'OP', 49);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (2, 117, 'OP', 50);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (3, 117, 'OP', 51);
INSERT INTO jrn_periode (jrn_def_id, p_id, status, id) VALUES (1, 117, 'OP', 52);



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
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_DATE_SUGGEST', 'Y');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_ALPHANUM', 'N');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_CHECK_PERIODE', 'N');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_UPDLAB', 'N');
INSERT INTO parameter (pr_id, pr_value) VALUES ('MY_STOCK', 'N');



INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('BANQUE', '51', 'Poste comptable par défaut pour les banques');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('CAISSE', '53', 'Poste comptable par défaut pour les caisses');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('CUSTOMER', '410', 'Poste comptable par défaut pour les clients');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('VENTE', '707', 'Poste comptable par défaut pour les ventes');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('VIREMENT_INTERNE', '58', 'Poste comptable par défaut pour les virements internes');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('DEP_PRIV', '4890', 'Depense a charge du gerant');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('SUPPLIER', '400', 'Poste par défaut pour les fournisseurs');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('DNA', '67', 'Dépense non déductible');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('TVA_DNA', '', 'TVA non déductible');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('TVA_DED_IMPOT', '', 'TVA déductible à l''impôt');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('COMPTE_COURANT', '', 'Poste comptable pour le compte courant');
INSERT INTO parm_code (p_code, p_value, p_comment) VALUES ('COMPTE_TVA', '', 'TVA à payer ou à recevoir');



INSERT INTO parm_money (pm_id, pm_code, pm_rate) VALUES (1, 'EUR', 1.0000);



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



INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (101, 'FR_NOR', 0.1960, 'TVA 19,6% - France métropolitaine - Taux normal', '445661,44571', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (102, 'FR_RED', 0.0550, 'TVA 5,5% - France métropolitaine - Taux réduit', '445662,44572', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (103, 'FR_SRED', 0.0210, 'TVA 2,1% - France métropolitaine - Taux super réduit', '445663,44573', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (104, 'FR_IMMO', 0.1960, 'TVA 19,6% - France métropolitaine - Taux immobilisations', '4456601,445701', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (105, 'FR_ANC', 0.0000, 'TVA x% - France métropolitaine - Taux anciens', '4456602,445702', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (201, 'DOM', 0.0850, 'TVA 8,5%  - DOM - Taux normal', '4456603,445703', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (202, 'DOM_NPR', 0.0850, 'TVA 8,5% - DOM - Taux normal NPR', '4456604,445704', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (203, 'DOM_REDUIT', 0.0210, 'TVA 2,1% - DOM - Taux réduit', '4456605,445705', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (204, 'DOM_I', 0.0175, 'TVA 1,75% - DOM - Taux I', '4456606,445706', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (205, 'DOM_PRESSE', 0.0105, 'TVA 1,05% - DOM - Taux publications de presse', '4456607,445707', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (206, 'DOM_OCTROI', 0.0000, 'TVA x% - DOM - Taux octroi de mer', '4456608,445708', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (207, 'DOM_IMMO', 0.0000, 'TVA x% - DOM - Taux immobilisations', '4456609,445709', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (301, 'COR_I', 0.1300, 'TVA 13% - Corse - Taux I', '44566010,4457010', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (302, 'COR_II', 0.0800, 'TVA 8% - Corse - Taux II', '44566011,4457011', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (303, 'COR_III', 0.0210, 'TVA 2,1% - Corse - Taux III', '44566012,4457012', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (304, 'COR_IV', 0.0090, 'TVA 0,9% - Corse - Taux IV', '44566013,4457013', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (305, 'COR_IMMO', 0.0000, 'TVA x% - Corse - Taux immobilisations', '44566014,4457014', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (401, 'INTRA', 0.0000, 'TVA x% - Acquisitions intracommunautaires/Pays', '44566015,4457015', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (402, 'INTRA_IMMMO', 0.0000, 'TVA x% - Acquisitions intracommunautaires immobilisations/Pays', '44566016,4457016', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (501, 'FRANCH', 0.0000, 'TVA x% - Non imposable : Achats en franchise', '44566017,4457017', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (502, 'EXPORT', 0.0000, 'TVA x% - Non imposable : Exports hors CE/Pays', '44566018,4457018', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (503, 'AUTRE', 0.0000, 'TVA x% - Non imposable : Autres opérations', '44566019,4457019', 0);
INSERT INTO tva_rate (tva_id, tva_label, tva_rate, tva_comment, tva_poste, tva_both_side) VALUES (504, 'INTRA_LIV', 0.0000, 'TVA x% - Non imposable : Livraisons intracommunautaires/Pays', '44566020,4457020', 0);









SELECT pg_catalog.setval('s_attr_def', 9001, false);



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



SELECT pg_catalog.setval('s_jrn_rapt', 20, true);



SELECT pg_catalog.setval('s_jrnaction', 5, true);



SELECT pg_catalog.setval('s_jrnx', 1, false);



SELECT pg_catalog.setval('s_oa_group', 7, true);



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






SELECT pg_catalog.setval('stock_change_c_id_seq', 1, false);






SELECT pg_catalog.setval('stock_repository_r_id_seq', 1, true);



SELECT pg_catalog.setval('tags_t_id_seq', 1, false);



SELECT pg_catalog.setval('tmp_pcmn_id_seq', 314, true);









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
INSERT INTO version (val, v_description, v_date) VALUES (127, 'Add filter for search, inactive tag or ledger, type of operation, security', '2018-02-10 22:46:49.270348');
INSERT INTO version (val, v_description, v_date) VALUES (128, 'Add a view to manage VAT', '2018-02-10 22:46:49.93138');



