begin;
insert into parameter values ('MY_STRICT','N');
update version set val=52;
commit;
