CREATE INDEX audit_connect_ac_user ON audit_connect USING btree (ac_user);
CREATE INDEX fk_jnt_dos_id ON jnt_use_dos USING btree (dos_id);
CREATE INDEX fk_jnt_use_dos ON jnt_use_dos USING btree (use_id);
CREATE INDEX fki_ac_users_recover_pass_fk ON recover_pass USING btree (use_id);
