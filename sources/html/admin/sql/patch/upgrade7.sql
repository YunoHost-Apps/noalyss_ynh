begin;

insert into tva_rate values (5,'0%',0, 'Pas soumis à la TVA',null);

update fiche_def_ref set frd_class_base=2400 where frd_id=7;

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
alter table jrn  alter jr_montant type numeric(20,4);
alter table jrnx  alter j_montant type numeric(20,4);
alter table centralized  alter c_montant type numeric(20,4);
alter table parm_money  alter pm_rate type numeric(20,4);
drop view vw_fiche_attr;
alter table tva_rate  alter tva_rate type numeric(8,4);
-- version 8
create view vw_fiche_attr as  SELECT a.f_id, a.fd_id, a.av_text AS vw_name, b.av_text AS vw_sell, c.av_text AS vw_buy, d.av_text AS tva_code, tva_rate.tva_id, tva_rate.tva_rate, tva_rate.tva_label, e.av_text AS vw_addr, f.av_text AS vw_cp, fiche_def.frd_id
   FROM ( SELECT fiche.f_id, fiche.fd_id, attr_value.av_text
           FROM fiche
      JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 1) a
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
           FROM fiche
      JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 6) b ON a.f_id = b.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
      FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 7) c ON a.f_id = c.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
   FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 2) d ON a.f_id = d.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
   FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 14) e ON a.f_id = e.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
   FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 15) f ON a.f_id = f.f_id
   LEFT JOIN tva_rate ON d.av_text = tva_rate.tva_id::text
   JOIN fiche_def USING (fd_id);


create function check_balance (p_internal text)  returns numeric as $$
declare 
	amount_jrnx_debit numeric;
	amount_jrnx_credit numeric;
	amount_jrn numeric;
begin
	select sum (j_montant) into amount_jrnx_credit 
	from jrnx join jrn on (j_grpt=jr_grpt_id)
		where 
	jr_internal=p_internal
	and j_debit=false;

	select sum (j_montant) into amount_jrnx_debit 
	from jrnx join jrn on (j_grpt=jr_grpt_id)
		where 
	jr_internal=p_internal
	and j_debit=true;

	select jr_montant into amount_jrn 
	from jrn
	where
	jr_internal=p_internal;

	if ( amount_jrnx_debit != amount_jrnx_credit ) 
		then
		return abs(amount_jrnx_debit-amount_jrnx_credit);
		end if;
	if ( amount_jrn != amount_jrnx_credit)
		then
		return -1*abs(amount_jrn - amount_jrnx_credit);
		end if;
	return 0;
end;
$$ language plpgsql;

create function proc_check_balance () returns TRIGGER as $jrn$
declare 
	diff numeric;
	tt text;
begin
	if TG_OP = 'INSERT' then
	tt=NEW.jr_internal;
	diff:=check_balance(tt);
	if diff != 0 then
		raise exception 'Rounded error %',diff ;
	end if;
	return NEW;
	end if;
end;
$jrn$ language plpgsql;
create trigger tr_jrn_check_balance after insert  on jrn for each row execute procedure proc_check_balance();
create table user_local_pref (
	user_id text,
	parameter_type text,
	parameter_value text
);
comment on table user_local_pref is 'The user''s local parameter ';
comment on column user_local_pref.user_id is 'user''s login ';
comment on column user_local_pref.parameter_type is 'the type of parameter ';
comment on column user_local_pref.parameter_value is 'the value of parameter ';

alter table user_local_pref add constraint pk_user_local_pref primary key (user_id,parameter_type);

insert into user_local_pref  (user_id,parameter_type,parameter_value)
select pref_user,'PERIODE',pref_periode from user_pref ;

update version set val=8;


commit;
