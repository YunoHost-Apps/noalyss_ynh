 SET client_encoding = 'utf8';
 SET check_function_bodies = false;
 SET client_min_messages = warning;
SET search_path = public, pg_catalog;
ALTER TABLE ONLY ac_dossier
    ADD CONSTRAINT ac_dossier_dos_name_key UNIQUE (dos_name);
ALTER TABLE ONLY ac_dossier
    ADD CONSTRAINT ac_dossier_pkey PRIMARY KEY (dos_id);
ALTER TABLE ONLY ac_users
    ADD CONSTRAINT ac_users_pkey PRIMARY KEY (use_id);
ALTER TABLE ONLY ac_users
    ADD CONSTRAINT ac_users_use_login_key UNIQUE (use_login);
ALTER TABLE ONLY jnt_use_dos
    ADD CONSTRAINT jnt_use_dos_pkey PRIMARY KEY (use_id, dos_id);
ALTER TABLE ONLY modeledef
    ADD CONSTRAINT modeledef_pkey PRIMARY KEY (mod_id);
ALTER TABLE ONLY user_global_pref
    ADD CONSTRAINT pk_user_global_pref PRIMARY KEY (user_id, parameter_type);
ALTER TABLE ONLY priv_user
    ADD CONSTRAINT priv_user_pkey PRIMARY KEY (priv_id);
ALTER TABLE ONLY user_global_pref
    ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES ac_users(use_login) ON UPDATE CASCADE ON DELETE CASCADE;
