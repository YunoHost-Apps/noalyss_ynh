begin;
--- on account_repository
update priv_user set priv_priv='X' where priv_priv='NO';
update priv_user set priv_priv='R' where priv_priv='W';
update user_global_pref set parameter_value='TEXT' where parameter_type='TOPMENU';


update version set val=10;
commit;

