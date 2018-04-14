CREATE FUNCTION account_add(p_id public.account_type, p_name character varying) RETURNS text
    LANGUAGE plpgsql
    AS $$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	nCount integer;
	sReturn text;
begin
	sReturn:= format_account(p_id);
	select count(*) into nCount from tmp_pcmn where pcm_val=sReturn;
	if nCount = 0 then
		nParent=account_parent(p_id);
		insert into tmp_pcmn (pcm_val,pcm_lib,pcm_val_parent)
			values (p_id, p_name,nParent) returning pcm_val into sReturn;
	end if;
return sReturn;
end ;
$$;
CREATE FUNCTION account_alphanum() RETURNS boolean
    LANGUAGE plpgsql
    AS $$
declare
	l_auto bool;
begin
	l_auto := true;
	select pr_value into l_auto from parameter where pr_id='MY_ALPHANUM';
	if l_auto = 'N' or l_auto is null then
		l_auto:=false;
	end if;
	return l_auto;
end;
$$;
CREATE FUNCTION account_auto(p_fd_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
declare
	l_auto bool;
begin

	select fd_create_account into l_auto from fiche_def where fd_id=p_fd_id;
	if l_auto is null then
		l_auto:=false;
	end if;
	return l_auto;
end;
$$;
CREATE FUNCTION account_compute(p_f_id integer) RETURNS public.account_type
    LANGUAGE plpgsql
    AS $$
declare
	class_base fiche_def.fd_class_base%type;
	maxcode numeric;
	sResult text;
	bAlphanum bool;
	sName text;
begin
	select fd_class_base into class_base
	from
		fiche_def join fiche using (fd_id)
	where
		f_id=p_f_id;
	raise notice 'account_compute class base %',class_base;
	bAlphanum := account_alphanum();
	if bAlphanum = false  then
	raise info 'account_compute : Alphanum is false';
		select count (pcm_val) into maxcode from tmp_pcmn where pcm_val_parent = class_base;
		if maxcode = 0	then
			maxcode:=class_base::numeric;
		else
			select max (pcm_val) into maxcode from tmp_pcmn where pcm_val_parent = class_base;
			maxcode:=maxcode::numeric;
		end if;
		if maxcode::text = class_base then
			maxcode:=class_base::numeric*1000;
		end if;
		maxcode:=maxcode+1;
		raise notice 'account_compute Max code %',maxcode;
		sResult:=maxcode::account_type;
	else
	raise info 'account_compute : Alphanum is true';
		-- if alphanum, use name
		select ad_value into sName from fiche_detail where f_id=p_f_id and ad_id=1;
		raise info 'name is %',sName;
		if sName is null then
			raise exception 'Cannot compute an accounting without the name of the card for %',p_f_id;
		end if;
		sResult := class_base||sName;
		sResult := substr(sResult,1,40);
		raise info 'Result is %',sResult;
	end if;
	return sResult::account_type;
end;
$$;
CREATE FUNCTION account_insert(p_f_id integer, p_account text) RETURNS text
    LANGUAGE plpgsql
    AS $$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	sName varchar;
	sNew tmp_pcmn.pcm_val%type;
	bAuto bool;
	nFd_id integer;
	sClass_Base fiche_def.fd_class_base%TYPE;
	nCount integer;
	first text;
	second text;
	s_account text;
begin

	if p_account is not null and length(trim(p_account)) != 0 then
	-- if there is coma in p_account, treat normally
		if position (',' in p_account) = 0 then
			raise info 'p_account is not empty';
				s_account := substr( p_account,1 , 40);
				select count(*)  into nCount from tmp_pcmn where pcm_val=s_account::account_type;
				raise notice 'found in tmp_pcm %',nCount;
				if nCount !=0  then
					raise info 'this account exists in tmp_pcmn ';
					perform attribut_insert(p_f_id,5,s_account);
				   else
				       -- account doesn't exist, create it
					select ad_value into sName from
						fiche_detail
					where
					ad_id=1 and f_id=p_f_id;

					nParent:=account_parent(s_account::account_type);
					insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (s_account::account_type,sName,nParent);
					perform attribut_insert(p_f_id,5,s_account);

				end if;
		else
		raise info 'presence of a comma';
		-- there is 2 accounts separated by a comma
		first := split_part(p_account,',',1);
		second := split_part(p_account,',',2);
		-- check there is no other coma
		raise info 'first value % second value %', first, second;

		if  position (',' in first) != 0 or position (',' in second) != 0 then
			raise exception 'Too many comas, invalid account';
		end if;
		perform attribut_insert(p_f_id,5,p_account);
		end if;
	else
	raise info 'A000 : p_account is  empty';
		select fd_id into nFd_id from fiche where f_id=p_f_id;
		bAuto:= account_auto(nFd_id);

		select fd_class_base into sClass_base from fiche_def where fd_id=nFd_id;
raise info 'sClass_Base : %',sClass_base;
		if bAuto = true and sClass_base similar to '[[:digit:]]*'  then
			raise info 'account generated automatically';
			sNew:=account_compute(p_f_id);
			raise info 'sNew %', sNew;
			select ad_value into sName from
				fiche_detail
			where
				ad_id=1 and f_id=p_f_id;
			nParent:=account_parent(sNew);
			sNew := account_add  (sNew,sName);
			perform attribut_insert(p_f_id,5,sNew);

		else
		-- if there is an account_base then it is the default
		      select fd_class_base::account_type into sNew from fiche_def join fiche using (fd_id) where f_id=p_f_id;
			if sNew is null or length(trim(sNew)) = 0 then
				raise notice 'count is null';
				 perform attribut_insert(p_f_id,5,null);
			else
				 perform attribut_insert(p_f_id,5,sNew);
			end if;
		end if;
	end if;

return 0;
end;
$$;
CREATE FUNCTION account_parent(p_account public.account_type) RETURNS public.account_type
    LANGUAGE plpgsql
    AS $$
declare
	sSubParent tmp_pcmn.pcm_val_parent%type;
	sResult tmp_pcmn.pcm_val_parent%type;
	nCount integer;
begin
	if p_account is NULL then
		return NULL;
	end if;
	sSubParent:=p_account;
	while true loop
		select count(*) into nCount
		from tmp_pcmn
		where
		pcm_val = sSubParent;
		if nCount != 0 then
			sResult:= sSubParent;
			exit;
		end if;
		sSubParent:= substr(sSubParent,1,length(sSubParent)-1);
		if length(sSubParent) <= 0 then
			raise exception 'Impossible de trouver le compte parent pour %',p_account;
		end if;
		raise notice 'sSubParent % % ',sSubParent,length(sSubParent);
	end loop;
	raise notice 'account_parent : Parent is %',sSubParent;
	return sSubParent;
end;
$$;
CREATE FUNCTION account_update(p_f_id integer, p_account public.account_type) RETURNS integer
    LANGUAGE plpgsql
    AS $$
declare
	nMax fiche.f_id%type;
	nCount integer;
	nParent tmp_pcmn.pcm_val_parent%type;
	sName varchar;
	first text;
	second text;
begin

	if length(trim(p_account)) != 0 then
		-- 2 accounts in card separated by comma
		if position (',' in p_account) = 0 then
			select count(*) into nCount from tmp_pcmn where pcm_val=p_account;
			if nCount = 0 then
			select ad_value into sName from
				fiche_detail
				where
				ad_id=1 and f_id=p_f_id;
			nParent:=account_parent(p_account);
			insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (p_account,sName,nParent);
		end if;
		else
		raise info 'presence of a comma';
		-- there is 2 accounts separated by a comma
		first := split_part(p_account,',',1);
		second := split_part(p_account,',',2);
		-- check there is no other coma
		raise info 'first value % second value %', first, second;

		if  position (',' in first) != 0 or position (',' in second) != 0 then
			raise exception 'Too many comas, invalid account';
		end if;
		-- check that both account are in PCMN

		end if;
	else
		-- account is null
		update fiche_detail set ad_value=null where f_id=p_f_id and ad_id=5 ;
	end if;

	update fiche_detail set ad_value=p_account where f_id=p_f_id and ad_id=5 ;

return 0;
end;
$$;
CREATE FUNCTION action_gestion_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
NEW.ag_title := substr(trim(NEW.ag_title),1,70);
NEW.ag_hour := substr(trim(NEW.ag_hour),1,5);
NEW.ag_owner := lower(NEW.ag_owner);
return NEW;
end;
$$;
CREATE FUNCTION action_gestion_related_ins_up() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
	nTmp bigint;
begin

if NEW.aga_least > NEW.aga_greatest then
	nTmp := NEW.aga_least;
	NEW.aga_least := NEW.aga_greatest;
	NEW.aga_greatest := nTmp;
end if;

if NEW.aga_least = NEW.aga_greatest then
	return NULL;
end if;

return NEW;

end;
$$;
CREATE FUNCTION anc_correct_tvand() RETURNS void
    LANGUAGE plpgsql
    AS $$ 
declare
        n_count numeric;
        i record;
        newrow_tva record;
begin
         for i in select * from operation_analytique where oa_jrnx_id_source is not null loop
         -- Get all the anc accounting from the base operation and insert the missing record for VAT 
                for newrow_tva in select *  from operation_analytique where j_id=i.oa_jrnx_id_source and po_id <> i.po_id loop
                    
                        -- check if the record is yet present
                        select count(*) into n_count from operation_analytique where  po_id=newrow_tva.po_id and oa_jrnx_id_source=i.oa_jrnx_id_source;

                        if n_count = 0 then
                          raise info 'insert operation analytique po_id = % oa_group = % ',i.po_id, i.oa_group;
                          insert into operation_analytique 
                          (po_id,oa_amount,oa_description,oa_debit,j_id,oa_group,oa_date,oa_jrnx_id_source,oa_positive)
                          values (newrow_tva.po_id,i.oa_amount,i.oa_description,i.oa_debit,i.j_id,i.oa_group,i.oa_date,i.oa_jrnx_id_source,i.oa_positive);
                        end if;
         
                end loop;

         
         end loop;
end;
 $$;
CREATE FUNCTION attribut_insert(p_f_id integer, p_ad_id integer, p_value character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$
begin
	insert into fiche_detail (f_id,ad_id, ad_value) values (p_f_id,p_ad_id,p_value);
	
return;
end;
$$;
CREATE FUNCTION attribute_correct_order() RETURNS void
    LANGUAGE plpgsql
    AS $$
declare
    crs_correct cursor for select A.jnt_id,A.jnt_order from jnt_fic_attr as A join jnt_fic_attr as B using (fd_id) where A.jnt_order=B.jnt_order and A.jnt_id > B.jnt_id;
    rec record;
begin
	open crs_correct;
	loop
	fetch crs_correct into rec;
	if NOT FOUND then
		close crs_correct;
		return;
	end if;
	update jnt_fic_attr set jnt_order=jnt_order + 1 where jnt_id = rec.jnt_id;
	end loop;
	close crs_correct;
	perform attribute_correct_order ();
end;
$$;
CREATE FUNCTION card_after_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

begin

	delete from action_gestion where f_id_dest = OLD.f_id;
	return OLD;

end;
$$;
CREATE FUNCTION card_class_base(p_f_id integer) RETURNS text
    LANGUAGE plpgsql
    AS $$
declare
	n_poste fiche_def.fd_class_base%type;
begin

	select fd_class_base into n_poste from fiche_def join fiche using
(fd_id)
	where f_id=p_f_id;
	if not FOUND then
		raise exception 'Invalid fiche card_class_base(%)',p_f_id;
	end if;
return n_poste;
end;
$$;
CREATE FUNCTION category_card_before_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

begin
    if OLD.fd_id > 499000 then
        return null;
    end if;
    return OLD;

end;
$$;
CREATE FUNCTION check_balance(p_grpt integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
declare
	amount_jrnx_debit numeric;
	amount_jrnx_credit numeric;
	amount_jrn numeric;
begin
	select coalesce(sum (j_montant),0) into amount_jrnx_credit
	from jrnx
		where
	j_grpt=p_grpt
	and j_debit=false;

	select coalesce(sum (j_montant),0) into amount_jrnx_debit
	from jrnx
		where
	j_grpt=p_grpt
	and j_debit=true;

	select coalesce(jr_montant,0) into amount_jrn
	from jrn
	where
	jr_grpt_id=p_grpt;

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
$$;
CREATE FUNCTION check_periode() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare 
  nPeriode int;
begin
if periode_exist(to_char(NEW.p_start,'DD.MM.YYYY'),NEW.p_id) <> -1 then
       nPeriode:=periode_exist(to_char(NEW.p_start,'DD.MM.YYYY'),NEW.p_id) ;
        raise info 'Overlap periode start % periode %',NEW.p_start,nPeriode;
	return null;
end if;

if periode_exist(to_char(NEW.p_end,'DD.MM.YYYY'),NEW.p_id) <> -1 then
	nPeriode:=periode_exist(to_char(NEW.p_start,'DD.MM.YYYY'),NEW.p_id) ;
        raise info 'Overlap periode end % periode %',NEW.p_end,nPeriode;
	return null;
end if;
return NEW;
end;
$$;
CREATE FUNCTION correct_sequence(p_sequence text, p_col text, p_table text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
declare
last_sequence int8;
max_sequence int8;
n integer;
begin
	select count(*) into n from pg_class where relkind='S' and relname=lower(p_sequence);
	if n = 0 then
		raise exception  ' Unknow sequence  % ',p_sequence;
	end if;
	select count(*) into n from pg_class where relkind='r' and relname=lower(p_table);
	if n = 0 then
		raise exception ' Unknow table  % ',p_table;
	end if;

	execute 'select last_value   from '||p_sequence into last_sequence;
	raise notice 'Last value of the sequence is %', last_sequence;

	execute 'select max('||p_col||')  from '||p_table into max_sequence;
	if  max_sequence is null then
		max_sequence := 0;
	end if;
	raise notice 'Max value of the sequence is %', max_sequence;
	max_sequence:= max_sequence +1;	
	execute 'alter sequence '||p_sequence||' restart with '||max_sequence;
return 0;

end;
$$;
CREATE FUNCTION create_missing_sequence() RETURNS integer
    LANGUAGE plpgsql
    AS $$
declare
p_sequence text;
nSeq integer;
c1 cursor for select jrn_def_id from jrn_def;
begin
	open c1;
	loop
	   fetch c1 into nSeq;
	   if not FOUND THEN
	   	close c1;
	   	return 0;
	   end if;
	   p_sequence:='s_jrn_pj'||nSeq::text;
	execute 'create sequence '||p_sequence;
	end loop;
close c1;
return 0;

end;
$$;
CREATE FUNCTION drop_index(p_constraint character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$
declare 
	nCount integer;
begin
	select count(*) into nCount from pg_indexes where indexname=p_constraint;
	if nCount = 1 then
	execute 'drop index '||p_constraint ;
	end if;
end;
$$;
CREATE FUNCTION drop_it(p_constraint character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$
declare 
	nCount integer;
begin
	select count(*) into nCount from pg_constraint where conname=p_constraint;
	if nCount = 1 then
	execute 'alter table parm_periode drop constraint '||p_constraint ;
	end if;
end;
$$;
CREATE FUNCTION extension_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
 sCode text;
 sFile text;
begin
sCode:=trim(upper(NEW.ex_code));
sCode:=replace(sCode,' ','_');
sCode:=substr(sCode,1,15);
sCode=upper(sCode);
NEW.ex_code:=sCode;
sFile:=NEW.ex_file;
sFile:=replace(sFile,';','_');
sFile:=replace(sFile,'<','_');
sFile:=replace(sFile,'>','_');
sFile:=replace(sFile,'..','');
sFile:=replace(sFile,'&','');
sFile:=replace(sFile,'|','');



return NEW;

end;

$$;
CREATE FUNCTION fiche_account_parent(p_f_id integer) RETURNS public.account_type
    LANGUAGE plpgsql
    AS $$
declare
ret tmp_pcmn.pcm_val%TYPE;
begin
	select fd_class_base into ret from fiche_def join fiche using (fd_id) where f_id=p_f_id;
	if not FOUND then
		raise exception '% N''existe pas',p_f_id;
	end if;
	return ret;
end;
$$;
CREATE FUNCTION fiche_attribut_synchro(p_fd_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
declare
	-- this sql gives the f_id and the missing attribute (ad_id)
	list_missing cursor for select f_id,fd_id,ad_id,jnt_order from jnt_fic_attr join fiche as A using (fd_id) where fd_id=p_fd_id and ad_id not in (select ad_id from fiche join fiche_detail using (f_id) where fd_id=jnt_fic_attr.fd_id and A.f_id=f_id);
	rec record;
begin
	open list_missing;
	loop
	
	fetch list_missing into rec;
	IF NOT FOUND then
		exit;
	end if;
	
	-- now we insert into attr_value
	insert into fiche_detail (f_id,ad_id,ad_value) values (rec.f_id,rec.ad_id,null);
	end loop;
	close list_missing;
end; 
$$;
CREATE FUNCTION fiche_def_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin

if position (',' in NEW.fd_class_base) != 0 then
   NEW.fd_create_account='f';

end if;
return NEW;
end;$$;
CREATE FUNCTION fiche_detail_qcode_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
	i record;
begin
	if NEW.ad_id=23 and NEW.ad_value != OLD.ad_value then
		RAISE NOTICE 'new qcode [%] old qcode [%]',NEW.ad_value,OLD.ad_value;
		update jrnx set j_qcode=NEW.ad_value where j_qcode = OLD.ad_value;    
	        update op_predef_detail set opd_poste=NEW.ad_value where opd_poste=OLD.ad_value;
	        raise notice 'TRG fiche_detail update op_predef_detail set opd_poste=% where opd_poste=%;',NEW.ad_value,OLD.ad_value;
		for i in select ad_id from attr_def where ad_type = 'card' or ad_id=25 loop
			update fiche_detail set ad_value=NEW.ad_value where ad_value=OLD.ad_value and ad_id=i.ad_id;
			RAISE NOTICE 'change for ad_id [%] ',i.ad_id;
			if i.ad_id=19 then
				RAISE NOTICE 'Change in stock_goods OLD[%] by NEW[%]',OLD.ad_value,NEW.ad_value;
				update stock_goods set sg_code=NEW.ad_value where sg_code=OLD.ad_value;
			end if;

		end loop;
	end if;
return NEW;
end;
$$;
CREATE FUNCTION fill_quant_fin() RETURNS void
    LANGUAGE plpgsql
    AS $$
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
$$;
CREATE FUNCTION find_pcm_type(pp_value public.account_type) RETURNS text
    LANGUAGE plpgsql
    AS $$
declare
	str_type parm_poste.p_type%TYPE;
	str_value parm_poste.p_type%TYPE;
	nLength integer;
begin
	str_value:=pp_value;
	nLength:=length(str_value::text);
	while nLength > 0 loop
		select p_type into str_type from parm_poste where p_value=str_value;
		if FOUND then
			return str_type;
		end if;
		nLength:=nLength-1;
		str_value:=substring(str_value::text from 1 for nLength)::account_type;
	end loop;
return 'CON';
end;
$$;
CREATE FUNCTION find_periode(p_date text) RETURNS integer
    LANGUAGE plpgsql
    AS $$

declare n_p_id int4;
begin

select p_id into n_p_id
	from parm_periode
	where
		p_start <= to_date(p_date,'DD.MM.YYYY')
		and
		p_end >= to_date(p_date,'DD.MM.YYYY');

if NOT FOUND then
	return -1;
end if;

return n_p_id;

end;$$;
CREATE FUNCTION format_account(p_account public.account_type) RETURNS public.account_type
    LANGUAGE plpgsql
    AS $_$

declare

sResult account_type;

begin
sResult := lower(p_account);

sResult := translate(sResult,E'éèêëàâäïîüûùöôç','eeeeaaaiiuuuooc');
sResult := translate(sResult,E' $€µ£%.+-/\\!(){}(),;_&|"#''^<>*','');

return upper(sResult);

end;
$_$;



COMMENT ON FUNCTION format_account(p_account public.account_type) IS 'format the accounting :
- upper case
- remove space and special char.
';



CREATE FUNCTION get_letter_jnt(a bigint) RETURNS bigint
    LANGUAGE plpgsql
    AS $$
declare
 nResult bigint;
begin
   select jl_id into nResult from jnt_letter join letter_deb using (jl_id) where j_id = a;
   if NOT FOUND then
	select jl_id into nResult from jnt_letter join letter_cred using (jl_id) where j_id = a;
	if NOT found then
		return null;
	end if;
    end if;
return nResult;
end;
$$;
CREATE FUNCTION get_menu_dependency(profile_menu_id integer) RETURNS SETOF integer
    LANGUAGE plpgsql
    AS $$
declare
	i int;
	x int;
	e int;
begin
	for x in select pm_id,me_code
			from profile_menu
			where me_code_dep in (select me_code from profile_menu where pm_id=profile_menu_id)
			and p_id = (select p_id from profile_menu where pm_id=profile_menu_id)
	loop
		return next x;

	for e in select *  from comptaproc.get_menu_dependency(x)
		loop
			return next e;
		end loop;

	end loop;
	return;
end;
$$;
CREATE FUNCTION get_menu_tree(p_code text, p_profile integer) RETURNS SETOF public.menu_tree
    LANGUAGE plpgsql
    AS $$
declare
	i menu_tree;
	e menu_tree;
	a text;
	x v_all_menu%ROWTYPE;
begin
	for x in select *  from v_all_menu where me_code_dep=p_code::text and p_id=p_profile
	loop
		if x.me_code_dep is not null then
			i.code := x.me_code_dep||'/'||x.me_code;
		else
			i.code := x.me_code;
		end if;

		i.description := x.me_description;

		return next i;

	for e in select *  from get_menu_tree(x.me_code,p_profile)
		loop
			e.code:=x.me_code_dep||'/'||e.code;
			return next e;
		end loop;

	end loop;
	return;
end;
$$;
CREATE FUNCTION get_pcm_tree(source public.account_type) RETURNS SETOF public.account_type
    LANGUAGE plpgsql
    AS $$
declare
	i account_type;
	e account_type;
begin
	for i in select pcm_val from tmp_pcmn where pcm_val_parent=source
	loop
		return next i;
		for e in select get_pcm_tree from get_pcm_tree(i)
		loop
			return next e;
		end loop;

	end loop;
	return;
end;
$$;
CREATE FUNCTION get_profile_menu(p_profile integer) RETURNS SETOF public.menu_tree
    LANGUAGE plpgsql
    AS $$
declare
	a menu_tree;
	e menu_tree;
begin
for a in select me_code,me_description from v_all_menu where p_id=p_profile
	and me_code_dep is null and me_type <> 'PR' and me_type <>'SP'
loop
		return next a;

		for e in select * from get_menu_tree(a.code,p_profile)
		loop
			return next e;
		end loop;

	end loop;
return;
end;
$$;
CREATE FUNCTION group_analytic_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare 
name text;
begin
name:=upper(NEW.ga_id);
name:=trim(name);
name:=replace(name,' ','');
NEW.ga_id:=name;
return NEW;
end;$$;
CREATE FUNCTION group_analytique_del() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
update poste_analytique set ga_id=null
where ga_id=OLD.ga_id;
return OLD;
end;$$;
CREATE FUNCTION html_quote(p_string text) RETURNS text
    LANGUAGE plpgsql
    AS $$
declare
	r text;
begin
	r:=p_string;
	r:=replace(r,'<','&lt;');
	r:=replace(r,'>','&gt;');
	r:=replace(r,'''','&quot;');
	return r;
end;$$;
CREATE FUNCTION info_def_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare 
	row_info_def info_def%ROWTYPE;
	str_type text;
begin
row_info_def:=NEW;
str_type:=upper(trim(NEW.id_type));
str_type:=replace(str_type,' ','');
str_type:=replace(str_type,',','');
str_type:=replace(str_type,';','');
if  length(str_type) =0 then
	raise exception 'id_type cannot be null';
end if;
row_info_def.id_type:=str_type;
return row_info_def;
end;
$$;
CREATE FUNCTION insert_jrnx(p_date character varying, p_montant numeric, p_poste public.account_type, p_grpt integer, p_jrn_def integer, p_debit boolean, p_tech_user text, p_tech_per integer, p_qcode text, p_comment text) RETURNS void
    LANGUAGE plpgsql
    AS $$
begin
	insert into jrnx
	(
		j_date,
		j_montant,
		j_poste,
		j_grpt,
		j_jrn_def,
		j_debit,
		j_text,
		j_tech_user,
		j_tech_per,
		j_qcode
	) values
	(
		to_date(p_date,'DD.MM.YYYY'),
		p_montant,
		p_poste,
		p_grpt,
		p_jrn_def,
		p_debit,
		p_comment,
		p_tech_user,
		p_tech_per,
		p_qcode
	);

return;
end;
$$;
CREATE FUNCTION insert_quant_purchase(p_internal text, p_j_id numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_nd_amount numeric, p_nd_tva numeric, p_nd_tva_recup numeric, p_dep_priv numeric, p_client character varying, p_tva_sided numeric, p_price_unit numeric) RETURNS void
    LANGUAGE plpgsql
    AS $$
declare
        fid_client integer;
        fid_good   integer;
        account_priv    account_type;
        fid_good_account account_type;
        n_dep_priv numeric;
begin
        n_dep_priv := 0;
        select p_value into account_priv from parm_code where p_code='DEP_PRIV';
        select f_id into fid_client from
                fiche_detail where ad_id=23 and ad_value=upper(trim(p_client));
        select f_id into fid_good from
                 fiche_detail where ad_id=23 and ad_value=upper(trim(p_fiche));
        select ad_value into fid_good_account from fiche_detail where ad_id=5 and f_id=fid_good;
        if strpos( fid_good_account , account_priv ) = 1 then
                n_dep_priv=p_price;
        end if; 
            
        insert into quant_purchase
                (qp_internal,
                j_id,
                qp_fiche,
                qp_quantite,
                qp_price,
                qp_vat,
                qp_vat_code,
                qp_nd_amount,
                qp_nd_tva,
                qp_nd_tva_recup,
                qp_supplier,
                qp_dep_priv,
                qp_vat_sided,
                qp_unit)
        values
                (p_internal,
                p_j_id,
                fid_good,
                p_quant,
                p_price,
                p_vat,
                p_vat_code,
                p_nd_amount,
                p_nd_tva,
                p_nd_tva_recup,
                fid_client,
                n_dep_priv,
                p_tva_sided,
                p_price_unit);
        return;
end;
 $$;
CREATE FUNCTION insert_quant_sold(p_internal text, p_jid numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_client character varying, p_tva_sided numeric, p_price_unit numeric) RETURNS void
    LANGUAGE plpgsql
    AS $$
declare
        fid_client integer;
        fid_good   integer;
begin

        select f_id into fid_client from
                fiche_detail where ad_id=23 and ad_value=upper(trim(p_client));
        select f_id into fid_good from
                fiche_detail where ad_id=23 and ad_value=upper(trim(p_fiche));
        insert into quant_sold
                (qs_internal,j_id,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client,qs_valid,qs_vat_sided,qs_unit)
        values
                (p_internal,p_jid,fid_good,p_quant,p_price,p_vat,p_vat_code,fid_client,'Y',p_tva_sided,p_price_unit);
        return;
end;
 $$;
CREATE FUNCTION insert_quick_code(nf_id integer, tav_text text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
	declare
	ns integer;
	nExist integer;
	tText text;
	tBase text;
	tName text;
	nCount Integer;
	nDuplicate Integer;
	begin
	tText := lower(trim(tav_text));
	tText := replace(tText,' ','');
        tText:= translate(tText,E' $€µ£%+/\\!(){}(),;&|"#''^<>*','');
	tText := translate(tText,E'éèêëàâäïîüûùöôç','eeeeaaaiiuuuooc');
	nDuplicate := 0;
	tBase := tText;
	loop
		-- take the next sequence
		select nextval('s_jnt_fic_att_value') into ns;
		if length (tText) = 0 or tText is null then
			select count(*) into nCount from fiche_detail where f_id=nf_id and ad_id=1;
			if nCount = 0 then
				tText := 'FICHE'||ns::text;
			else
				select ad_value into tName from fiche_detail where f_id=nf_id and ad_id=1;
				
				tName := lower(trim(tName));
				tName := substr(tName,1,6);
				tName := replace(tName,' ','');
				tName:= translate(tName,E' $€µ£%+/\\!(){}(),;&|"#''^<>*','');
				tName := translate(tName,E'éèêëàâäïîüûùöôç','eeeeaaaiiuuuooc');
				tBase := tName;
				if nDuplicate = 0 then
					tText := tName;
				else
					tText := tName||nDuplicate::text;
				end if;
			end if;
		end if;
		-- av_text already used ?
		select count(*) into nExist
			from fiche_detail
		where
			ad_id=23 and  ad_value=upper(tText);

		if nExist = 0 then
			exit;
		end if;
		nDuplicate := nDuplicate + 1 ;
		tText := tBase || nDuplicate::text;
		
		if nDuplicate > 9999 then
			raise Exception 'too many duplicate % duplicate# %',tText,nDuplicate;
		end if;
	end loop;


	insert into fiche_detail(jft_id,f_id,ad_id,ad_value) values (ns,nf_id,23,upper(tText));
	return ns;
	end;
$_$;



CREATE FUNCTION is_closed(p_periode integer, p_jrn_def_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
declare
bClosed bool;
str_status text;
begin
select p_closed into bClosed from parm_periode
	where p_id=p_periode;

if bClosed = true then
	return bClosed;
end if;

select status into str_status from jrn_periode
       where p_id =p_periode and jrn_def_id=p_jrn_def_id;

if str_status <> 'OP' then
   return bClosed;
end if;
return false;
end;
$$;
CREATE FUNCTION jnt_fic_attr_ins() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
   r_record jnt_fic_attr%ROWTYPE;
   i_max integer;
begin
r_record=NEW;
perform comptaproc.fiche_attribut_synchro(r_record.fd_id);
select coalesce(max(jnt_order),0) into i_max from jnt_fic_attr where fd_id=r_record.fd_id;
i_max := i_max + 10;
NEW.jnt_order=i_max;
return NEW;
end;
$$;
CREATE FUNCTION jrn_add_note(p_jrid bigint, p_note text) RETURNS void
    LANGUAGE plpgsql
    AS $$
declare
	tmp bigint;
begin
	if length(trim(p_note)) = 0 then
	   delete from jrn_note where jr_id= p_jrid;
	   return;
	end if;
	
	select n_id into tmp from jrn_note where jr_id = p_jrid;
	
	if FOUND then
	   update jrn_note set n_text=trim(p_note) where jr_id = p_jrid;
	else 
	   insert into jrn_note (jr_id,n_text) values ( p_jrid, p_note);

	end if;
	
	return;
end;
$$;
CREATE FUNCTION jrn_check_periode() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
bClosed bool;
str_status text;
ljr_tech_per jrn.jr_tech_per%TYPE;
ljr_def_id jrn.jr_def_id%TYPE;
lreturn jrn%ROWTYPE;
begin
if TG_OP='UPDATE' then
	ljr_tech_per :=OLD.jr_tech_per ;
	NEW.jr_tech_per := comptaproc.find_periode(to_char(NEW.jr_date,'DD.MM.YYYY'));
	ljr_def_id   :=OLD.jr_def_id;
	lreturn      :=NEW;
	if NEW.jr_date = OLD.jr_date then
		return NEW;
	end if;
	if comptaproc.is_closed(NEW.jr_tech_per,NEW.jr_def_id) = true then
	      	raise exception 'Periode fermee';
	end if;
end if;

if TG_OP='INSERT' then
	NEW.jr_tech_per := comptaproc.find_periode(to_char(NEW.jr_date,'DD.MM.YYYY'));
	ljr_tech_per :=NEW.jr_tech_per ;
	ljr_def_id   :=NEW.jr_def_id;
	lreturn      :=NEW;
end if;

if TG_OP='DELETE' then
	ljr_tech_per :=OLD.jr_tech_per;
	ljr_def_id   :=OLD.jr_def_id;
	lreturn      :=OLD;
end if;

if comptaproc.is_closed (ljr_def_id,ljr_def_id) = true then
   	raise exception 'Periode fermee';
end if;

return lreturn;
end;$$;
CREATE FUNCTION jrn_def_add() RETURNS trigger
    LANGUAGE plpgsql
    AS $$begin
execute 'insert into jrn_periode(p_id,jrn_def_id,status) select p_id,'||NEW.jrn_def_id||',
	case when p_central=true then ''CE''
	      when p_closed=true then ''CL''
	else ''OP''
	end
from
parm_periode ';
return NEW;
end;$$;
CREATE FUNCTION jrn_def_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare 
nb numeric;
begin
select count(*) into nb from jrn where jr_def_id=OLD.jrn_def_id;

if nb <> 0 then
	raise exception 'EFFACEMENT INTERDIT: JOURNAL UTILISE';
end if;
return OLD;
end;$$;
CREATE FUNCTION jrn_del() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
row jrn%ROWTYPE;
begin
row:=OLD;
insert into del_jrn ( jr_id,
       jr_def_id,
       jr_montant,
       jr_comment,
       jr_date,
       jr_grpt_id,
       jr_internal,
       jr_tech_date,
       jr_tech_per,
       jrn_ech,
       jr_ech,
       jr_rapt,
       jr_valid,
       jr_opid,
       jr_c_opid,
       jr_pj,
       jr_pj_name,
       jr_pj_type,
       jr_pj_number,
       del_jrn_date) 
       select  jr_id,
	      jr_def_id,
	      jr_montant,
	      jr_comment,
	      jr_date,
	      jr_grpt_id,
	      jr_internal,
	      jr_tech_date,
	      jr_tech_per,
	      jrn_ech,
	      jr_ech,
	      jr_rapt,
	      jr_valid,
	      jr_opid,
	      jr_c_opid,
	      jr_pj,
	      jr_pj_name,
	      jr_pj_type,
	      jr_pj_number
	      ,now() from jrn where jr_id=row.jr_id;
return row;
end;
$$;
CREATE FUNCTION jrnx_del() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
row jrnx%ROWTYPE;
begin
row:=OLD;


insert into del_jrnx(
            j_id, j_date, j_montant, j_poste, j_grpt, j_rapt, j_jrn_def, 
            j_debit, j_text, j_centralized, j_internal, j_tech_user, j_tech_date, 
            j_tech_per, j_qcode, f_id)  SELECT j_id, j_date, j_montant, j_poste, j_grpt, j_rapt, j_jrn_def, 
       j_debit, j_text, j_centralized, j_internal, j_tech_user, j_tech_date, 
       j_tech_per, j_qcode, f_id from jrnx where j_id=row.j_id;
return row;
end;
$$;
CREATE FUNCTION jrnx_ins() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
n_fid bigint;
nCount integer;
sQcode text;

begin
n_fid := NULL;
sQcode := NULL;

NEW.j_tech_per := comptaproc.find_periode(to_char(NEW.j_date,'DD.MM.YYYY'));
if NEW.j_tech_per = -1 then
        raise exception 'Période invalide';
end if;

if trim(coalesce(NEW.j_qcode,'')) = '' then
        -- how many card has this accounting
        select count(*) into nCount from fiche_detail where ad_id=5 and ad_value=NEW.j_poste;
        -- only one card is found , then we change the j_qcode by the card
        if nCount = 1 then
                select f_id into n_fid from fiche_detail where ad_id = 5 and ad_value=NEW.j_poste;
            if FOUND then
                select ad_value into sQcode  from fiche_detail where f_id=n_fid and ad_id = 23;
                NEW.f_id := n_fid;
                NEW.j_qcode = sQcode;
                raise info 'comptaproc.jrnx_ins : found card % qcode %',n_fid,sQcode;
            end if;
        end if;

end if;

NEW.j_qcode=trim(upper(coalesce(NEW.j_qcode,'')));

if length (coalesce(NEW.j_qcode,'')) = 0 then
    NEW.j_qcode=NULL;
    else
      select f_id into n_fid from fiche_detail  where ad_id=23 and ad_value=NEW.j_qcode;
      if NOT FOUND then
        raise exception 'La fiche dont le quick code est % n''existe pas',NEW.j_qcode;
    end if;
end if;

NEW.f_id:=n_fid;
return NEW;
end;
$$;
CREATE FUNCTION jrnx_letter_del() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
row jrnx%ROWTYPE;
begin
row:=OLD;
delete from jnt_letter 
	where (jl_id in (select jl_id from letter_deb) and jl_id not in(select jl_id from letter_cred )) 
		or (jl_id not in (select jl_id from letter_deb  ) and jl_id  in(select jl_id from letter_cred ));
return row;
end;
$$;
CREATE FUNCTION menu_complete_dependency(n_profile numeric) RETURNS void
    LANGUAGE plpgsql
    AS $$
declare 
 n_count integer;
 csr_root_menu cursor (p_profile numeric) is select pm_id,
	me_code,
	me_code_dep 
	
	from profile_menu 
	where 
	me_code in 
		(select a.me_code_dep 
			from profile_menu as a 
			join profile_menu as b on (a.me_code=b.me_code and a.me_code_dep=b.me_code_dep and a.pm_id <> b.pm_id and a.p_id=b.p_id) 
			where a.p_id=n_profile) 
		and p_id=p_profile;

begin
	for duplicate in csr_root_menu(n_profile)
	loop
		raise notice 'found %',duplicate;
		update profile_menu set pm_id_dep  = duplicate.pm_id 
			where pm_id in (select a.pm_id
				from profile_menu as a 
				left join profile_menu as b on (a.me_code=b.me_code and a.me_code_dep=b.me_code_dep)
				where 
				a.p_id=n_profile
				and b.p_id=n_profile
				and a.pm_id_dep is null 
				and a.me_code_dep = duplicate.me_code
				and a.pm_id < b.pm_id);
	end loop;
	
	for duplicate in csr_root_menu(n_profile) 
	loop
		select count(*) into n_count from profile_menu where p_id=n_profile and pm_id_dep = duplicate.pm_id;
		raise notice '% use % times',duplicate,n_count;
		if n_count = 0 then
			raise notice ' Update with %',duplicate;
			update profile_menu set pm_id_dep = duplicate.pm_id where p_id = n_profile and me_code_dep = duplicate.me_code and pm_id_dep is null;
		end if;

	end loop;
	
end;
$$;
CREATE FUNCTION opd_limit_description() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	declare
		sDescription text;
	begin
	sDescription := NEW.od_description;
	NEW.od_description := substr(sDescription,1,80);
	return NEW;
	end;
$$;
CREATE FUNCTION periode_exist(p_date text, p_periode_id bigint) RETURNS integer
    LANGUAGE plpgsql
    AS $$

declare n_p_id int4;
begin

select p_id into n_p_id
        from parm_periode
        where
                p_start <= to_date(p_date,'DD.MM.YYYY')
                and
                p_end >= to_date(p_date,'DD.MM.YYYY')
                and
                p_id <> p_periode_id;

if NOT FOUND then
        return -1;
end if;

return n_p_id;

end;$$;
CREATE FUNCTION plan_analytic_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
   name text;
begin
   name:=upper(NEW.pa_name);
   name:=trim(name);
   name:=replace(name,' ','');
   NEW.pa_name:=name;
return NEW;
end;
$$;
CREATE FUNCTION poste_analytique_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$declare
name text;
rCount record;

begin
name:=upper(NEW.po_name);
name:=trim(name);
name:=replace(name,' ','');		
NEW.po_name:=name;

if NEW.ga_id is NULL then
return NEW;
end if;

if length(trim(NEW.ga_id)) = 0 then
  NEW.ga_id:=NULL;
  return NEW;
end if;
perform 'select ga_id from groupe_analytique where ga_id='||NEW.ga_id;
if NOT FOUND then
   raise exception' Inexistent Group Analytic %',NEW.ga_id;
end if;
return NEW;
end;$$;
CREATE FUNCTION proc_check_balance() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare 
	diff numeric;
	tt integer;
begin
	if TG_OP = 'INSERT' or TG_OP='UPDATE' then
	tt=NEW.jr_grpt_id;
	diff:=check_balance(tt);
	if diff != 0 then
		raise exception 'balance error %',diff ;
	end if;
	return NEW;
	end if;
end;
$$;
CREATE FUNCTION quant_purchase_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	begin
		if NEW.qp_price < 0 OR NEW.qp_quantite <0 THEN
			NEW.qp_price := abs (NEW.qp_price)*(-1);
			NEW.qp_quantite := abs (NEW.qp_quantite)*(-1);
		end if;
return NEW;
end;
$$;
CREATE FUNCTION quant_sold_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	begin
		if NEW.qs_price < 0 OR NEW.qs_quantite <0 THEN
			NEW.qs_price := abs (NEW.qs_price)*(-1);
			NEW.qs_quantite := abs (NEW.qs_quantite)*(-1);
		end if;
return NEW;
end;
$$;
CREATE FUNCTION t_document_modele_validate() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare 
    lText text;
    modified document_modele%ROWTYPE;
begin
    modified:=NEW;

	modified.md_filename:=replace(NEW.md_filename,' ','_');
	return modified;
end;
$$;
CREATE FUNCTION t_document_type_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
nCounter integer;
    BEGIN
select count(*) into nCounter from pg_class where relname='seq_doc_type_'||NEW.dt_id;
if nCounter = 0 then
        execute  'create sequence seq_doc_type_'||NEW.dt_id;
end if;
        RETURN NEW;
    END;
$$;
CREATE FUNCTION t_document_validate() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
  lText text;
  modified document%ROWTYPE;
begin
    	modified:=NEW;
	modified.d_filename:=replace(NEW.d_filename,' ','_');
	return modified;
end;
$$;
CREATE FUNCTION t_jrn_def_description() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    declare
        str varchar(200);
    BEGIN
        str := substr(NEW.jrn_def_description,1,200);
        NEW.jrn_def_description := str;

        RETURN NEW;
    END;
$$;
CREATE FUNCTION t_jrn_def_sequence() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
nCounter integer;

    BEGIN
    select count(*) into nCounter 
       from pg_class where relname='s_jrn_'||NEW.jrn_def_id;
       if nCounter = 0 then
       	   execute  'create sequence s_jrn_'||NEW.jrn_def_id;
	   raise notice 'Creating sequence s_jrn_%',NEW.jrn_def_id;
	 end if;

        RETURN NEW;
    END;
$$;
CREATE FUNCTION tmp_pcmn_alphanum_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
   r_record tmp_pcmn%ROWTYPE;
begin
r_record := NEW;
r_record.pcm_val:=format_account(NEW.pcm_val);

return r_record;
end;
$$;
CREATE FUNCTION tmp_pcmn_ins() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
   r_record tmp_pcmn%ROWTYPE;
begin
r_record := NEW;
if  length(trim(r_record.pcm_type))=0 or r_record.pcm_type is NULL then
   r_record.pcm_type:=find_pcm_type(NEW.pcm_val);
   return r_record;
end if;
return NEW;
end;
$$;
CREATE FUNCTION trg_profile_user_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

begin

NEW.user_name := lower(NEW.user_name);
return NEW;

end;
$$;
CREATE FUNCTION trg_todo_list_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

begin

NEW.use_login:= lower(NEW.use_login);
return NEW;

end;
$$;
CREATE FUNCTION trg_todo_list_shared_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

begin

NEW.use_login:= lower(NEW.use_login);
return NEW;

end;
$$;
CREATE FUNCTION trg_user_sec_act_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

begin

NEW.ua_login:= lower(NEW.ua_login);
return NEW;

end;
$$;
CREATE FUNCTION trg_user_sec_jrn_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

begin

NEW.uj_login:= lower(NEW.uj_login);
return NEW;

end;
$$;
CREATE FUNCTION trim_cvs_quote() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
        modified import_tmp%ROWTYPE;
begin
	modified:=NEW;
	modified.devise=replace(new.devise,'"','');
	modified.poste_comptable=replace(new.poste_comptable,'"','');
        modified.compte_ordre=replace(NEW.COMPTE_ORDRE,'"','');
        modified.detail=replace(NEW.DETAIL,'"','');
        modified.num_compte=replace(NEW.NUM_COMPTE,'"','');
        return modified;
end;
$$;
CREATE FUNCTION trim_space_format_csv_banque() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
        modified format_csv_banque%ROWTYPE;
begin
        modified.name=trim(NEW.NAME);
        modified.include_file=trim(new.include_file);
		if ( length(modified.name) = 0 ) then
			modified.name=null;
		end if;
		if ( length(modified.include_file) = 0 ) then
			modified.include_file=null;
		end if;

        return modified;
end;
$$;
CREATE FUNCTION tva_delete(integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$ 
declare
	p_tva_id alias for $1;
	nCount integer;
begin
	nCount=0;
	select count(*) into nCount from quant_sold where qs_vat_code=p_tva_id;
	if nCount != 0 then
                 return;
		
	end if;
	select count(*) into nCount from quant_purchase where qp_vat_code=p_tva_id;
	if nCount != 0 then
                 return;
		
	end if;

delete from tva_rate where tva_id=p_tva_id;
	return;
end;
$_$;



CREATE FUNCTION tva_insert(text, numeric, text, text, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
declare
	l_tva_id integer;
	p_tva_label alias for $1;
	p_tva_rate alias for $2;
	p_tva_comment alias for $3;
	p_tva_poste alias for $4;
	p_tva_both_side alias for $5;
	debit text;
	credit text;
	nCount integer;
begin
if length(trim(p_tva_label)) = 0 then
	return 3;
end if;

if length(trim(p_tva_poste)) != 0 then
	if position (',' in p_tva_poste) = 0 then return 4; end if;
	debit  = split_part(p_tva_poste,',',1);
	credit	= split_part(p_tva_poste,',',2);
	select count(*) into nCount from tmp_pcmn where pcm_val=debit::account_type;
	if nCount = 0 then return 4; end if;
	select count(*) into nCount from tmp_pcmn where pcm_val=credit::account_type;
	if nCount = 0 then return 4; end if;

end if;
select into l_tva_id nextval('s_tva') ;
insert into tva_rate(tva_id,tva_label,tva_rate,tva_comment,tva_poste,tva_both_side)
	values (l_tva_id,p_tva_label,p_tva_rate,p_tva_comment,p_tva_poste,p_tva_both_side);
return 0;
end;
$_$;



CREATE FUNCTION tva_modify(integer, text, numeric, text, text, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
declare
	p_tva_id alias for $1;
	p_tva_label alias for $2;
	p_tva_rate alias for $3;
	p_tva_comment alias for $4;
	p_tva_poste alias for $5;
	p_tva_both_side alias for $6;
	debit text;
	credit text;
	nCount integer;
begin
if length(trim(p_tva_label)) = 0 then
	return 3;
end if;

if length(trim(p_tva_poste)) != 0 then
	if position (',' in p_tva_poste) = 0 then return 4; end if;
	debit  = split_part(p_tva_poste,',',1);
	credit	= split_part(p_tva_poste,',',2);
	select count(*) into nCount from tmp_pcmn where pcm_val=debit::account_type;
	if nCount = 0 then return 4; end if;
	select count(*) into nCount from tmp_pcmn where pcm_val=credit::account_type;
	if nCount = 0 then return 4; end if;

end if;
update tva_rate set tva_label=p_tva_label,tva_rate=p_tva_rate,tva_comment=p_tva_comment,tva_poste=p_tva_poste,tva_both_side=p_tva_both_side
	where tva_id=p_tva_id;
return 0;
end;
$_$;



CREATE FUNCTION update_quick_code(njft_id integer, tav_text text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
	declare
	ns integer;
	nExist integer;
	tText text;
	tBase text;
	old_qcode varchar;
	num_rows_jrnx integer;
	num_rows_predef integer;
	begin
	-- get current value
	select ad_value into old_qcode from fiche_detail where jft_id=njft_id;
	-- av_text didn't change so no update
	if tav_text = upper( trim(old_qcode)) then
		raise notice 'nothing to change % %' , tav_text,old_qcode;
		return 0;
	end if;

	tText := trim(lower(tav_text));
	tText := replace(tText,' ','');
        -- valid alpha is [ . : - _ ]
	tText := translate(tText,E' $€µ£%+/\\!(){}(),;&|"#''^<>*','');
	tText := translate(tText,E'éèêëàâäïîüûùöôç','eeeeaaaiiuuuooc');
	tText := upper(tText);
	if length ( tText) = 0 or tText is null then
		return 0;
	end if;

	ns := njft_id;
	tBase := tText;
	loop
		-- av_text already used ?
		select count(*) into nExist
			from fiche_detail
		where
			ad_id=23 and ad_value=tText
			and jft_id <> njft_id;

		if nExist = 0 then
			exit;
		end if;
		if tText = tBase||ns then
			-- take the next sequence
			select nextval('s_jnt_fic_att_value') into ns;
		end if;
		tText  :=tBase||ns;

	end loop;
	update fiche_detail set ad_value = tText where jft_id=njft_id;

	-- update also the contact
	update fiche_detail set ad_value = tText
		where jft_id in
			( select jft_id
				from fiche_detail
			where ad_id=25 and ad_value=old_qcode);


	return ns;
	end;
$_$;


SET search_path = public, pg_catalog;


CREATE FUNCTION bud_card_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$declare
 sCode text;
begin

sCode:=trim(upper(NEW.bc_code));
sCode:=replace(sCode,' ','_');
sCode:=substr(sCode,1,10);
NEW.bc_code:=sCode;
return NEW;
end;$$;
CREATE FUNCTION bud_detail_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$declare
mline bud_detail%ROWTYPE;
begin
mline:=NEW;
if mline.po_id = -1 then
   mline.po_id:=NULL;
end if;
return mline;
end;$$;
CREATE FUNCTION correct_quant_purchase() RETURNS void
    LANGUAGE plpgsql
    AS $$
declare
	r_invalid quant_purchase;
	s_QuickCode text;
	b_j_debit bool;
	r_new record;
	r_jrnx record;
begin

for r_invalid in select * from quant_purchase where qp_valid='A'
loop

select j_qcode into s_QuickCode from vw_poste_qcode where f_id=r_invalid.qp_fiche;
raise notice 'qp_id % Quick code is %',r_invalid.qp_id,s_QuickCode;

select j_debit,j_grpt,j_jrn_def,j_montant into r_jrnx from jrnx where j_id=r_invalid.j_id;
if NOT FOUND then
	raise notice 'error not found jrnx %',r_invalid.j_id;
	update quant_purchase set qp_valid='Y' where qp_id=r_invalid.qp_id;
	continue;
end if;
raise notice 'j_debit % , j_grpt % ,j_jrn_def  % qp_price %',r_jrnx.j_debit,r_jrnx.j_grpt,r_jrnx.j_jrn_def ,r_invalid.qp_price;

select jr_internal,j_id,j_montant into r_new
	from jrnx join jrn on (j_grpt=jr_grpt_id)
	where 
	j_jrn_def=r_jrnx.j_jrn_def
	and j_id not in (select j_id from  quant_purchase)
	and j_qcode=s_QuickCode
	and j_montant=r_jrnx.j_montant
	and j_debit != r_jrnx.j_debit;

if NOT FOUND then
	raise notice 'error not found %', r_invalid.j_id;
	update quant_purchase set qp_valid='Y' where qp_id=r_invalid.qp_id;
	continue;     
end if;
raise notice 'j_id % found amount %',r_new.j_id,r_new.j_montant;

insert into quant_purchase (qp_internal,j_id,qp_fiche,qp_quantite,qp_price,qp_vat,qp_nd_amount,qp_nd_tva_recup,qp_valid,qp_dep_priv,qp_supplier,qp_vat_code)
values (r_new.jr_internal,r_invalid.j_id,r_invalid.qp_fiche,(r_invalid.qp_quantite * (-1)),r_invalid.qp_price * (-1),r_invalid.qp_vat*(-1),r_invalid.qp_nd_amount*(-1),r_invalid.qp_nd_tva_recup*(-1) ,'Y',r_invalid.qp_dep_priv*(-1),r_invalid.qp_supplier,r_invalid.qp_vat_code);

update quant_purchase set qp_valid='Y' where qp_id=r_invalid.qp_id;
end loop;
return;
end;
$$;
CREATE FUNCTION correct_quant_sale() RETURNS void
    LANGUAGE plpgsql
    AS $$
declare
	r_invalid quant_sold;
	s_QuickCode text;
	b_j_debit bool;
	r_new record;
	r_jrnx record;
begin

for r_invalid in select * from quant_sold where qs_valid='A'
loop

select j_qcode into s_QuickCode from vw_poste_qcode where f_id=r_invalid.qs_fiche;
raise notice 'qp_id % Quick code is %',r_invalid.qs_id,s_QuickCode;

select j_debit,j_grpt,j_jrn_def,j_montant into r_jrnx from jrnx where j_id=r_invalid.j_id;
if NOT FOUND then
	update quant_sold set qs_valid='Y' where qs_id=r_invalid.qs_id;
	raise notice 'error not found jrnx %',r_invalid.j_id;
	continue;
end if;
raise notice 'j_debit % , j_grpt % ,j_jrn_def  % qs_price %',r_jrnx.j_debit,r_jrnx.j_grpt,r_jrnx.j_jrn_def ,r_invalid.qs_price;

select jr_internal,j_id,j_montant into r_new
	from jrnx join jrn on (j_grpt=jr_grpt_id)
	where 
	j_jrn_def=r_jrnx.j_jrn_def
	and j_id not in (select j_id from  quant_sold)
	and j_qcode=s_QuickCode
	and j_montant=r_jrnx.j_montant
	and j_debit != r_jrnx.j_debit;

if NOT FOUND then
   update quant_sold set qs_valid='Y' where qs_id=r_invalid.qs_id;
	raise notice 'error not found %', r_invalid.j_id;
	continue;
end if;
raise notice 'j_id % found amount %',r_new.j_id,r_new.j_montant;


 insert into quant_sold (qs_internal,j_id,qs_fiche,qs_quantite,qs_price,qs_vat,qs_valid,qs_client,qs_vat_code)
 values (r_new.jr_internal,r_invalid.j_id,r_invalid.qs_fiche,(r_invalid.qs_quantite * (-1)),r_invalid.qs_price * (-1),r_invalid.qs_vat*(-1),'Y',r_invalid.qs_client,r_invalid.qs_vat_code);
 update quant_sold set qs_valid='Y' where qs_id=r_invalid.qs_id;
end loop;
return;
end;
$$;
CREATE FUNCTION modify_menu_system(n_profile numeric) RETURNS void
    LANGUAGE plpgsql
    AS $$
declare 
r_duplicate profile_menu%ROWTYPE;
str_duplicate text;
n_lowest_id numeric; -- lowest pm_id : update the dependency in profile_menu
n_highest_id numeric; -- highest pm_id insert into profile_menu

begin

for str_duplicate in   
	select me_code 
	from profile_menu 
	where 
	p_id=n_profile and 
	p_type_display <> 'P' and
	pm_id_dep is null
	group by me_code 
	having count(*) > 1 
loop
	raise info 'str_duplicate %',str_duplicate;
	for r_duplicate in select * 
		from profile_menu 
		where 
		p_id=n_profile and
		me_code_dep=str_duplicate
	loop
		raise info 'r_duplicate %',r_duplicate;
		-- get the lowest 
		select a.pm_id into n_lowest_id from profile_menu a join profile_menu b on (a.me_code=b.me_code and a.p_id = b.p_id)
		where
		a.me_code=str_duplicate
		and a.p_id=n_profile
		and a.pm_id < b.pm_id;
		raise info 'lowest is %',n_lowest_id;
		-- get the highest
		select a.pm_id into n_highest_id from profile_menu a join profile_menu b on (a.me_code=b.me_code and a.p_id = b.p_id)
		where
		a.me_code=str_duplicate
		and a.p_id=n_profile
		and a.pm_id > b.pm_id;
		raise info 'highest is %',n_highest_id;

		-- update the first one
		update profile_menu set pm_id_dep = n_lowest_id where pm_id=r_duplicate.pm_id;
		-- insert a new one
		insert into profile_menu (me_code,
			me_code_dep,
			p_id,
			p_order,
			p_type_display,
			pm_default,
			pm_id_dep)
		values (r_duplicate.me_code,
			r_duplicate.me_code_dep,
			r_duplicate.p_id,
			r_duplicate.p_order,
			r_duplicate.p_type_display,
			r_duplicate.pm_default,
			n_highest_id);
		
	end loop;	

end loop;	
end;
$$;
CREATE FUNCTION upgrade_repo(p_version integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
declare 
        is_mono integer;
begin
        select count (*) into is_mono from information_schema.tables where table_name='repo_version';
        if is_mono = 1 then
                update repo_version set val=p_version;
        else
                update version set val=p_version;
        end if;
end;
$$;
