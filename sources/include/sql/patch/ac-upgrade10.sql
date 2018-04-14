begin;
--- on account_repository
delete from user_global_pref where parameter_type='fr_FR.utf8';

insert into user_global_pref(user_id,parameter_type,parameter_value ) select use_login,'LANG','fr_FR.utf8' from ac_users ;

update version set val=11;
commit;

