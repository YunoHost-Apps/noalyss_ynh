
begin;
ALTER TABLE ac_dossier drop COLUMN dos_jnt_user ;
delete from jnt_use_dos where jnt_id in (select priv_jnt from priv_user where priv_priv='X');
delete from jnt_use_dos where use_id in (select use_id from ac_users where use_admin=1 or use_active=0);              
ALTER TABLE ac_users ADD COLUMN use_email text;
COMMENT ON COLUMN ac_users.use_email IS 'Email of the user';

drop table priv_user;
select upgrade_repo(16);
alter table 
rollback;
