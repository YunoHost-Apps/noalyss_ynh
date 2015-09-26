begin;
create or replace function comptaproc.fill_quant_fin() returns void as
$_$
declare
   sBank text;
   sCassa text;
   sCustomer text;
   sSupplier text;
   rec record;
   recBank record;
   recSupp_Cust record;
   nCount integer;
   nAmount numeric;
   nBank integer;
   nOther integer;
   nSupp_Cust integer;
begin
	select p_value into sBank from parm_code where p_code='BANQUE';
	select p_value into sCassa from parm_code where p_code='CAISSE';
	select p_value into sSupplier from parm_code where p_code='SUPPLIER';
	select p_value into sCustomer from parm_code where p_code='CUSTOMER';
	
	for rec in select jr_id,jr_grpt_id from jrn 
	    where jr_def_id in (select jrn_def_id from jrn_def where jrn_def_type='FIN')
		and jr_id not in (select jr_id from quant_fin)
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

-- if row remains
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
		nSupp_Cust := 0; nOther:=0;
		for recSupp_Cust in select  j_id, j_montant,j_debit,j_qcode,j_poste from jrnx where j_grpt=rec.jr_grpt_id
		loop
		if recSupp_Cust.j_poste like sSupplier||'%'  then
			-- retrieve f_id for bank
			select f_id into nSupp_Cust from vw_poste_qcode where j_qcode=recSupp_Cust.j_qcode;
			if recSupp_Cust.j_debit = true then
				nAmount=recSupp_Cust.j_montant*(-1);
			else 
				nAmount=recSupp_Cust.j_montant;
			end if;
		else if  recSupp_Cust.j_poste like sCustomer||'%' then
			select f_id into nSupp_Cust from vw_poste_qcode where j_qcode=recSupp_Cust.j_qcode;
			if recSupp_Cust.j_debit = false then
				nAmount=recSupp_Cust.j_montant*(-1);
			else 
				nAmount=recSupp_Cust.j_montant;
			end if;
			else
			select f_id into nOther from vw_poste_qcode where j_qcode=recSupp_Cust.j_qcode;
			
			end if;
		end if;
		end loop;
		if nSupp_Cust != 0 and nOther != 0 then
			insert into quant_fin (jr_id,qf_bank,qf_other,qf_amount) values (rec.jr_id,nOther,nSupp_Cust,nAmount);
		end if;
	end loop;
-- if row remains --> VISA (441*)
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
		nSupp_Cust := 0; nOther:=0;
		for recSupp_Cust in select  j_id, j_montant,j_debit,j_qcode,j_poste from jrnx where j_grpt=rec.jr_grpt_id
		loop
		if recSupp_Cust.j_poste like '441%'  then
			-- retrieve f_id for bank
			select f_id into nSupp_Cust from vw_poste_qcode where j_qcode=recSupp_Cust.j_qcode;
			if recSupp_Cust.j_debit = false then
				nAmount=recSupp_Cust.j_montant*(-1);
			else 
				nAmount=recSupp_Cust.j_montant;
			end if;
			else
			select f_id into nOther from vw_poste_qcode where j_qcode=recSupp_Cust.j_qcode;
			
			
		end if;
		end loop;
		if nSupp_Cust != 0 and nOther != 0 then
			insert into quant_fin (jr_id,qf_bank,qf_other,qf_amount) values (rec.jr_id,nOther,nSupp_Cust,nAmount);
		end if;
	end loop;
	return;
end;
$_$
language plpgsql;
select  comptaproc.fill_quant_fin();

update jrnx set j_date=jr_date from jrn where j_grpt=jr_grpt_id;
update jrnx set j_jrn_def=jr_def_id from jrn where j_grpt=jr_grpt_id;

update version set val=84;
commit;