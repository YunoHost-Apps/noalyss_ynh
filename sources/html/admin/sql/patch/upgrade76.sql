begin;
drop table if exists quant_fin;

CREATE TABLE quant_fin
(
  qf_id bigserial NOT NULL,
  qf_bank bigint,
  jr_id bigint,
  qf_other bigint,
  qf_amount numeric(20,4) DEFAULT 0,
  CONSTRAINT quant_fin_pk PRIMARY KEY (qf_id),
  CONSTRAINT fk_card FOREIGN KEY (qf_bank)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_card_other FOREIGN KEY (qf_other)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_jrn FOREIGN KEY (jr_id)
      REFERENCES jrn (jr_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
COMMENT ON TABLE quant_fin IS 'Simple operation for financial';

create or replace function comptaproc.fill_quant_fin() returns void as
$_$
declare
   sBank text;
   sCassa text;
   rec record;
   recBank record;
   nCount integer;
   nAmount numeric;
   nBank integer;
   nOther integer;
begin
	select p_value into sBank from parm_code where p_code='BANQUE';
	select p_value into sCassa from parm_code where p_code='CAISSE';
	
	for rec in select jr_id,jr_grpt_id from jrn 
	    where jr_def_id in (select jrn_def_id from jrn_def where jrn_def_type='FIN')
	loop
		-- there are only 2 lines for bank operations
		-- first debit
		select count(j_id) into nCount from jrnx where j_grpt=rec.jr_grpt_id;
		if nCount > 2 then 
			raise notice 'Trop de valeur pour jr_grpt_id % count %',rec.jr_grpt_id,nCount;
			return;
		end if;
		nBank := 0; nOther:=0;
		for recBank in select  j_id, j_montant,j_debit,j_qcode,j_poste from jrnx where j_grpt=rec.jr_grpt_id
		loop
		if recBank.j_poste like sBank||'%' then
			-- retrieve f_id for bank
			select f_id into nBank from vw_poste_qcode where j_qcode=recBank.j_qcode;
			if recBank.j_debit = false then
				nAmount=recBank.j_montant*(-1);
			else 
				nAmount=recBank.j_montant;
			end if;
		else
			select f_id into nOther from vw_poste_qcode where j_qcode=recBank.j_qcode;
		end if;
		end loop;
		if nBank != 0 and nOther != 0 then
			insert into quant_fin (jr_id,qf_bank,qf_other,qf_amount) values (rec.jr_id,nBank,nOther,nAmount);
		end if;
	end loop;
-- only cash
	for rec in select jr_id,jr_grpt_id from jrn 
	    where jr_def_id in (select jrn_def_id from jrn_def where jrn_def_type='FIN') and jr_id not in (select jr_id from quant_fin)
	loop
		-- there are only 2 lines for bank operations
		-- first debit
		select count(j_id) into nCount from jrnx where j_grpt=rec.jr_grpt_id;
		if nCount > 2 then 
			raise notice 'Trop de valeur pour jr_grpt_id % count %',rec.jr_grpt_id,nCount;
			return;
		end if;
		nBank := 0; nOther:=0;
		for recBank in select  j_id, j_montant,j_debit,j_qcode,j_poste from jrnx where j_grpt=rec.jr_grpt_id
		loop
		if recBank.j_poste like sCassa||'%' then
			-- retrieve f_id for bank
			select f_id into nBank from vw_poste_qcode where j_qcode=recBank.j_qcode;
			if recBank.j_debit = false then
				nAmount=recBank.j_montant*(-1);
			else 
				nAmount=recBank.j_montant;
			end if;
		else
			select f_id into nOther from vw_poste_qcode where j_qcode=recBank.j_qcode;
		end if;
		end loop;
		if nBank != 0 and nOther != 0 then
			insert into quant_fin (jr_id,qf_bank,qf_other,qf_amount) values (rec.jr_id,nBank,nOther,nAmount);
		end if;
	end loop;

	return;
end;
$_$
language plpgsql;
select  comptaproc.fill_quant_fin();

alter table del_jrn drop constraint jr_id;
alter table del_jrn add dj_id serial;
alter table del_jrn add constraint dj_id primary key(dj_id);

alter table del_jrnx drop constraint j_id;
alter table del_jrnx add djx_id serial;
alter table del_jrnx add constraint djx_id primary key(djx_id);

update version set val=77;
commit;