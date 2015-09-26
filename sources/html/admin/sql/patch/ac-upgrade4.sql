delete from jnt_use_dos where dos_id not in (select dos_id from ac_dossier );

delete from jnt_use_dos where dos_id not in  (select dos_id from ac_dossier);
update version set val=5;
