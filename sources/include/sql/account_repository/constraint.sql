 SET client_encoding = 'utf8';
 SET check_function_bodies = false;
 SET client_min_messages = warning;
SET search_path = public, pg_catalog;
ALTER TABLE ONLY audit_connect ALTER COLUMN ac_id SET DEFAULT nextval('audit_connect_ac_id_seq'::regclass);
ALTER TABLE ONLY dossier_sent_email ALTER COLUMN id SET DEFAULT nextval('dossier_sent_email_id_seq'::regclass);
ALTER TABLE ONLY ac_dossier
    ADD CONSTRAINT ac_dossier_dos_name_key UNIQUE (dos_name);
ALTER TABLE ONLY ac_dossier
    ADD CONSTRAINT ac_dossier_pkey PRIMARY KEY (dos_id);
ALTER TABLE ONLY ac_users
    ADD CONSTRAINT ac_users_pkey PRIMARY KEY (use_id);
ALTER TABLE ONLY ac_users
    ADD CONSTRAINT ac_users_use_login_key UNIQUE (use_login);
ALTER TABLE ONLY audit_connect
    ADD CONSTRAINT audit_connect_pkey PRIMARY KEY (ac_id);
ALTER TABLE ONLY dossier_sent_email
    ADD CONSTRAINT de_date_dos_id_ux UNIQUE (de_date, dos_id);
ALTER TABLE ONLY dossier_sent_email
    ADD CONSTRAINT dossier_sent_email_pkey PRIMARY KEY (id);
ALTER TABLE ONLY jnt_use_dos
    ADD CONSTRAINT jnt_use_dos_pkey PRIMARY KEY (jnt_id);
ALTER TABLE ONLY modeledef
    ADD CONSTRAINT modeledef_pkey PRIMARY KEY (mod_id);
ALTER TABLE ONLY user_global_pref
    ADD CONSTRAINT pk_user_global_pref PRIMARY KEY (user_id, parameter_type);
ALTER TABLE ONLY progress
    ADD CONSTRAINT progress_pkey PRIMARY KEY (p_id);
ALTER TABLE ONLY recover_pass
    ADD CONSTRAINT recover_pass_pkey PRIMARY KEY (request);
ALTER TABLE ONLY jnt_use_dos
    ADD CONSTRAINT use_id_dos_id_uniq UNIQUE (use_id, dos_id);
ALTER TABLE ONLY version
    ADD CONSTRAINT version_pkey PRIMARY KEY (val);
ALTER TABLE ONLY recover_pass
    ADD CONSTRAINT ac_users_recover_pass_fk FOREIGN KEY (use_id) REFERENCES ac_users(use_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY dossier_sent_email
    ADD CONSTRAINT de_ac_dossier_fk FOREIGN KEY (dos_id) REFERENCES ac_dossier(dos_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY user_global_pref
    ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES ac_users(use_login) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jnt_use_dos
    ADD CONSTRAINT jnt_use_dos_dos_id_fkey FOREIGN KEY (dos_id) REFERENCES ac_dossier(dos_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY jnt_use_dos
    ADD CONSTRAINT jnt_use_dos_use_id_fkey FOREIGN KEY (use_id) REFERENCES ac_users(use_id);
CREATE TRIGGER limit_user_trg BEFORE INSERT OR UPDATE ON audit_connect FOR EACH ROW EXECUTE PROCEDURE limit_user();
