begin;
alter table import_tmp alter bq_account type text;

update version set val=56;

commit;
