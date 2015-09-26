CREATE INDEX fk_jnt_dos_id ON jnt_use_dos USING btree (dos_id);
CREATE INDEX fk_jnt_use_dos ON jnt_use_dos USING btree (use_id);
