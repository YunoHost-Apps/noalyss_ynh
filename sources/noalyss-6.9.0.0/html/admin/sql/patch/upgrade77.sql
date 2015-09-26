begin;
insert into parameter(pr_id,pr_value) values ('MY_DATE_SUGGEST','Y');
update version set val=78;
commit;