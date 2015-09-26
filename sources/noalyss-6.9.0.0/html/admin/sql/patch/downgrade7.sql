begin;

insert into tva_rate values (5,'0%',0, 'Pas soumis à la TVA',null);

update fiche_def_ref set frd_class_base=2400 where frd_id=7;

update  version set val=8;
-- banque n'a pas de gestion stock
delete from jnt_fic_attr where fd_id=1 and ad_id=19;
-- client n'a pas de gestion stock
delete from jnt_fic_attr where fd_id=2 and ad_id=19;
-- default periode for phpcompta
 update user_pref set pref_periode=40 where pref_user='phpcompta';
-- create index ix_j_grp on jrnx(j_grpt);
-- create index ix_jr_grp on jrn(jr_grpt_id);
update jrnx set j_tech_per = jr_tech_per  from jrn  where j_grpt=jr_grpt_id and j_tech_per is null;
alter table jrnx alter j_tech_per set not null;
alter table jrn alter jr_tech_per set not null;
alter table jrn  alter jr_montant type numeric(8,4);
alter table jrnx  alter j_montant type numeric(8,4);

update version set val=7;
commit;
drop trigger tr_jrn_check_balance on jrn ;
drop function proc_check_balance();
drop function check_balance(text);
drop table user_local_pref;
commit;

