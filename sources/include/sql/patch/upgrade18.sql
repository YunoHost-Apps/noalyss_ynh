begin;
alter table parm_periode drop constraint parm_periode_p_start_key;

update version set val=19;
commit;