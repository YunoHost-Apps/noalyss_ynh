begin;
delete from jnt_use_dos where use_id not in (select use_id from ac_users);

delete from jnt_use_dos where dos_id not in (select dos_id from ac_dossier);

alter table jnt_use_dos add CONSTRAINT jnt_use_dos_dos_id_fkey FOREIGN KEY (dos_id)
      REFERENCES ac_dossier (dos_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;
alter table jnt_use_dos add   CONSTRAINT jnt_use_dos_use_id_fkey FOREIGN KEY (use_id)
      REFERENCES ac_users (use_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;


delete from priv_user where priv_jnt not in (select jnt_id from jnt_use_dos);

alter table jnt_use_dos drop constraint jnt_use_dos_pkey;

alter table jnt_use_dos add constraint jnt_use_dos_pkey PRIMARY KEY (jnt_id);

alter table priv_user add CONSTRAINT priv_user_priv_jnt_fkey FOREIGN KEY (priv_jnt)
      REFERENCES jnt_use_dos (jnt_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE cascade;
alter table version add  primary key (val);
update version set val=12;
commit;
