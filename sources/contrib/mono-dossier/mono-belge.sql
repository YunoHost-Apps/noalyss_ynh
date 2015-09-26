--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: comptaproc; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA comptaproc;


--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: -
--

CREATE PROCEDURAL LANGUAGE plpgsql;


SET search_path = public, pg_catalog;

--
-- Name: account_type; Type: DOMAIN; Schema: public; Owner: -
--

CREATE DOMAIN account_type AS character varying(40);


--
-- Name: anc_table_account_type; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE anc_table_account_type AS (
	po_id bigint,
	pa_id bigint,
	po_name text,
	po_description text,
	sum_amount numeric(25,4),
	card_account text,
	name text
);


--
-- Name: anc_table_card_type; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE anc_table_card_type AS (
	po_id bigint,
	pa_id bigint,
	po_name text,
	po_description text,
	sum_amount numeric(25,4),
	f_id bigint,
	card_account text,
	name text
);


--
-- Name: menu_tree; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE menu_tree AS (
	code text,
	description text
);


SET search_path = comptaproc, pg_catalog;

--
-- Name: account_add(public.account_type, character varying); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: account_alphanum(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: account_auto(integer); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: account_compute(integer); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION account_compute(p_f_id integer) RETURNS public.account_type
    LANGUAGE plpgsql
    AS $$
declare
	class_base fiche_def.fd_class_base%type;
	maxcode numeric;
	sResult account_type;
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
		-- if alphanum, use name
		select ad_value into sName from fiche_detail where f_id=p_f_id and ad_id=1;
		if sName is null then
			raise exception 'Cannot compute an accounting without the name of the card for %',p_f_id;
		end if;
		sResult := class_base||sName;
	end if;
	return sResult;
end;
$$;


--
-- Name: account_insert(integer, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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
begin

	if p_account is not null and length(trim(p_account)) != 0 then
	-- if there is coma in p_account, treat normally
		if position (',' in p_account) = 0 then
			raise info 'p_account is not empty';
				select count(*)  into nCount from tmp_pcmn where pcm_val=p_account::account_type;
				raise notice 'found in tmp_pcm %',nCount;
				if nCount !=0  then
					raise info 'this account exists in tmp_pcmn ';
					perform attribut_insert(p_f_id,5,p_account);
				   else
				       -- account doesn't exist, create it
					select ad_value into sName from
						fiche_detail
					where
					ad_id=1 and f_id=p_f_id;

					nParent:=account_parent(p_account::account_type);
					insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (p_account::account_type,sName,nParent);
					perform attribut_insert(p_f_id,5,p_account);

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
	raise info 'p_account is  empty';
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


--
-- Name: account_parent(public.account_type); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: account_update(integer, public.account_type); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: action_gestion_ins_upd(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION action_gestion_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
NEW.ag_title := substr(trim(NEW.ag_title),1,70);
NEW.ag_hour := substr(trim(NEW.ag_hour),1,5);
return NEW;
end;
$$;


--
-- Name: action_get_tree(bigint); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION action_get_tree(p_id bigint) RETURNS SETOF bigint
    LANGUAGE plpgsql
    AS $$

declare
   e bigint;
   i bigint;
begin
   for e in select ag_id from action_gestion where ag_ref_ag_id=p_id
   loop
        for i in select action_get_tree from  comptaproc.action_get_tree(e)
        loop
                raise notice ' == i %', i;
                return next i;
        end loop;
    raise notice ' = e %', e;
    return next e;
   end loop;
   return;

end;
$$;


--
-- Name: attribut_insert(integer, integer, character varying); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION attribut_insert(p_f_id integer, p_ad_id integer, p_value character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$
begin
	insert into fiche_detail (f_id,ad_id, ad_value) values (p_f_id,p_ad_id,p_value);
	
return;
end;
$$;


--
-- Name: attribute_correct_order(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: card_after_delete(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION card_after_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

begin

	delete from action_gestion where f_id_dest = OLD.f_id;
	return OLD;

end;
$$;


--
-- Name: card_class_base(integer); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: check_balance(integer); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION check_balance(p_grpt integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
declare 
	amount_jrnx_debit numeric;
	amount_jrnx_credit numeric;
	amount_jrn numeric;
begin
	select sum (j_montant) into amount_jrnx_credit 
	from jrnx 
		where 
	j_grpt=p_grpt
	and j_debit=false;

	select sum (j_montant) into amount_jrnx_debit 
	from jrnx 
		where 
	j_grpt=p_grpt
	and j_debit=true;

	select jr_montant into amount_jrn 
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


--
-- Name: correct_sequence(text, text, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: FUNCTION correct_sequence(p_sequence text, p_col text, p_table text); Type: COMMENT; Schema: comptaproc; Owner: -
--

COMMENT ON FUNCTION correct_sequence(p_sequence text, p_col text, p_table text) IS ' Often the primary key is a sequence number and sometimes the value of the sequence is not synchronized with the primary key ( p_sequence : sequence name, p_col : col of the pk,p_table : concerned table';


--
-- Name: create_missing_sequence(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: drop_index(character varying); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: drop_it(character varying); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: extension_ins_upd(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: fiche_account_parent(integer); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: fiche_attribut_synchro(integer); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: fiche_def_ins_upd(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION fiche_def_ins_upd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin

if position (',' in NEW.fd_class_base) != 0 then
   NEW.fd_create_account='f';

end if;
return NEW;
end;$$;


--
-- Name: fill_quant_fin(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: find_pcm_type(public.account_type); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: find_periode(text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: format_account(public.account_type); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION format_account(p_account public.account_type) RETURNS public.account_type
    LANGUAGE plpgsql
    AS $_$

declare

sResult account_type;

begin
sResult := lower(p_account);

sResult := translate(sResult,'éèêëàâäïîüûùöôç','eeeeaaaiiuuuooc');
sResult := translate(sResult,' $€µ£%.+-/\!(){}(),;_&|"#''^<>*','');

return upper(sResult);

end;
$_$;


--
-- Name: FUNCTION format_account(p_account public.account_type); Type: COMMENT; Schema: comptaproc; Owner: -
--

COMMENT ON FUNCTION format_account(p_account public.account_type) IS 'format the accounting :
- upper case
- remove space and special char.
';


--
-- Name: get_letter_jnt(bigint); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: get_menu_tree(text, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION get_menu_tree(p_code text, login text) RETURNS SETOF public.menu_tree
    LANGUAGE plpgsql
    AS $$
declare
	i menu_tree;
	e menu_tree;
	a text;
	x v_all_menu%ROWTYPE;
begin
	for x in select *  from v_all_menu where me_code_dep=p_code::text and user_name=login::text
	loop
		if x.me_code_dep is not null then
			i.code := x.me_code_dep||'/'||x.me_code;
		else
			i.code := x.me_code;
		end if;

		i.description := x.me_description;

		return next i;

	for e in select *  from get_menu_tree(x.me_code,login)
		loop
			e.code:=x.me_code_dep||'/'||e.code;
			return next e;
		end loop;

	end loop;
	return;
end;
$$;


--
-- Name: get_pcm_tree(public.account_type); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: get_profile_menu(text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION get_profile_menu(login text) RETURNS SETOF public.menu_tree
    LANGUAGE plpgsql
    AS $$
declare
	a menu_tree;
	e menu_tree;
begin
for a in select me_code,me_description from v_all_menu where user_name=login
	and me_code_dep is null and me_type <> 'PR' and me_type <>'SP'
loop
		return next a;

		for e in select * from get_menu_tree(a.code,login)
		loop
			return next e;
		end loop;

	end loop;
return;
end;
$$;


--
-- Name: group_analytic_ins_upd(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: group_analytique_del(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION group_analytique_del() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
update poste_analytique set ga_id=null
where ga_id=OLD.ga_id;
return OLD;
end;$$;


--
-- Name: html_quote(text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: FUNCTION html_quote(p_string text); Type: COMMENT; Schema: comptaproc; Owner: -
--

COMMENT ON FUNCTION html_quote(p_string text) IS 'remove harmfull HTML char';


--
-- Name: info_def_ins_upd(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: insert_jrnx(character varying, numeric, public.account_type, integer, integer, boolean, text, integer, text, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: insert_quant_purchase(text, numeric, character varying, numeric, numeric, numeric, integer, numeric, numeric, numeric, numeric, character varying, numeric); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION insert_quant_purchase(p_internal text, p_j_id numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_nd_amount numeric, p_nd_tva numeric, p_nd_tva_recup numeric, p_dep_priv numeric, p_client character varying, p_tva_sided numeric) RETURNS void
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
                qp_vat_sided)
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
                p_dep_priv,
                p_tva_sided);
        return;
end;
 $$;


--
-- Name: insert_quant_sold(text, numeric, character varying, numeric, numeric, numeric, integer, character varying, numeric); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION insert_quant_sold(p_internal text, p_jid numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_client character varying, p_tva_sided numeric) RETURNS void
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
                (qs_internal,j_id,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client,qs_valid,qs_vat_sided)
        values
                (p_internal,p_jid,fid_good,p_quant,p_price,p_vat,p_vat_code,fid_client,'Y',p_tva_sided);
        return;
end;
 $$;


--
-- Name: insert_quick_code(integer, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION insert_quick_code(nf_id integer, tav_text text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
	declare
	ns integer;
	nExist integer;
	tText text;
	begin
	tText := upper(trim(tav_text));
	tText := replace(tText,' ','');
	
	loop
		-- take the next sequence
		select nextval('s_jnt_fic_att_value') into ns;
		if length (tText) = 0 or tText is null then
			tText := 'FID'||ns;
		end if;
		-- av_text already used ?
		select count(*) into nExist 
			from fiche_detail
		where 
			ad_id=23 and  ad_value=upper(tText);

		if nExist = 0 then
			exit;
		end if;
		tText:='FID'||ns;
	end loop;


	insert into fiche_detail(jft_id,f_id,ad_id,ad_value) values (ns,nf_id,23,upper(tText));
	return ns;
	end;
$$;


--
-- Name: is_closed(integer, integer); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: jnt_fic_attr_ins(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: jrn_add_note(bigint, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: jrn_check_periode(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: jrn_def_add(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: jrn_def_delete(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: jrn_del(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: jrnx_del(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: jrnx_ins(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION jrnx_ins() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
n_fid bigint;
begin

NEW.j_tech_per := comptaproc.find_periode(to_char(NEW.j_date,'DD.MM.YYYY'));
if NEW.j_tech_per = -1 then
	raise exception 'Période invalide';
end if;

if NEW.j_qcode is NULL then
   return NEW;
end if;

NEW.j_qcode=trim(upper(NEW.j_qcode));

if length (NEW.j_qcode) = 0 then
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


--
-- Name: jrnx_letter_del(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: plan_analytic_ins_upd(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: poste_analytique_ins_upd(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: proc_check_balance(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: quant_purchase_ins_upd(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: quant_sold_ins_upd(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: t_document_modele_validate(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: t_document_type_insert(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: t_document_validate(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: t_jrn_def_sequence(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: table_analytic_account(text, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION table_analytic_account(p_from text, p_to text) RETURNS SETOF public.anc_table_account_type
    LANGUAGE plpgsql
    AS $$
declare
	ret ANC_table_account_type%ROWTYPE;
	sql_from text:='';
	sql_to text:='';
	sWhere text:='';
	sAnd text:='';
	sResult text:='';
begin
if p_from <> '' and p_from is not null then
	sql_from:='oa_date >= to_date('''||p_from::text||''',''DD.MM.YYYY'')';
	sWhere:=' where ';
end if;

if p_to <> '' and p_to is not null then
	sql_to=' oa_date <= to_date('''||p_to::text||''',''DD.MM.YYYY'')';
	sWhere := ' where ';
end if;

if sql_to <> '' and sql_from <> '' then
	sAnd:=' and ';
end if;

sResult := sWhere || sql_from || sAnd || sql_to;

for ret in EXECUTE 'SELECT po.po_id,
			    po.pa_id, po.po_name, 
			    po.po_description,sum(
        CASE
            WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
            ELSE operation_analytique.oa_amount
        END) AS sum_amount, jrnx.j_poste, tmp_pcmn.pcm_lib AS name
   FROM operation_analytique
   JOIN poste_analytique po USING (po_id)
   JOIN jrnx USING (j_id)
   JOIN tmp_pcmn ON jrnx.j_poste::text = tmp_pcmn.pcm_val::text
'|| sResult ||'
  GROUP BY po.po_id, po.po_name, po.pa_id, jrnx.j_poste, tmp_pcmn.pcm_lib, po.po_description
 HAVING sum(
CASE
    WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
    ELSE operation_analytique.oa_amount
END) <> 0::numeric '
	loop
	return next ret;
end loop;
end;
$$;


--
-- Name: table_analytic_card(text, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION table_analytic_card(p_from text, p_to text) RETURNS SETOF public.anc_table_card_type
    LANGUAGE plpgsql
    AS $$
declare
	ret ANC_table_card_type%ROWTYPE;
	sql_from text:='';
	sql_to text:='';
	sWhere text:='';
	sAnd text:='';
	sResult text:='';
begin
if p_from <> '' and p_from is not null then
	sql_from:='oa_date >= to_date('''||p_from::text||''',''DD.MM.YYYY'')';
	sWhere:=' where ';
end if;

if p_to <> '' and p_to is not null then
	sql_to=' oa_date <= to_date('''||p_to::text||''',''DD.MM.YYYY'')';
	sWhere := ' where ';
end if;

if sql_to <> '' and sql_from <> '' then
	sAnd :=' and ';
end if;

sResult := sWhere || sql_from || sAnd || sql_to;

for ret in EXECUTE ' SELECT po.po_id, po.pa_id, po.po_name, po.po_description,  sum(
        CASE
            WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
            ELSE operation_analytique.oa_amount
        END) AS sum_amount, jrnx.f_id, jrnx.j_qcode, ( SELECT fiche_detail.ad_value
           FROM fiche_detail
          WHERE fiche_detail.ad_id = 1 AND fiche_detail.f_id = jrnx.f_id) AS name
   FROM operation_analytique
   JOIN poste_analytique po USING (po_id)
   JOIN jrnx USING (j_id)'|| sResult ||'
  GROUP BY po.po_id, po.po_name, po.pa_id, jrnx.f_id, jrnx.j_qcode, ( SELECT fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 1 AND fiche_detail.f_id = jrnx.f_id), po.po_description
 HAVING sum(
CASE
    WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
    ELSE operation_analytique.oa_amount
END) <> 0::numeric;'


	loop
	return next ret;
end loop;
end;
$$;


--
-- Name: tmp_pcmn_alphanum_ins_upd(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: tmp_pcmn_ins(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: trim_cvs_quote(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: trim_space_format_csv_banque(); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: tva_delete(integer); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: tva_insert(text, numeric, text, text, integer); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: tva_modify(integer, text, numeric, text, text, integer); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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


--
-- Name: update_quick_code(integer, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE FUNCTION update_quick_code(njft_id integer, tav_text text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
	declare
	ns integer;
	nExist integer;
	tText text;
	old_qcode varchar;
	begin
	-- get current value
	select ad_value into old_qcode from fiche_detail where jft_id=njft_id;
	-- av_text didn't change so no update
	if tav_text = upper( trim(old_qcode)) then
		return 0;
	end if;
	
	tText := trim(upper(tav_text));
	tText := replace(tText,' ','');
	if length ( tText) = 0 or tText is null then
		return 0;
	end if;
		
	ns := njft_id;

	loop
		-- av_text already used ?
		select count(*) into nExist 
			from fiche_detail
		where 
			ad_id=23 and ad_value=tText;

		if nExist = 0 then
			exit;
		end if;	
		if tText = 'FID'||ns then
			-- take the next sequence
			select nextval('s_jnt_fic_att_value') into ns;
		end if;
		tText  :='FID'||ns;
		
	end loop;
	update fiche_detail set ad_value = tText where jft_id=njft_id;

	-- update also the contact
	update fiche_detail set ad_value = tText 
		where jft_id in 
			( select jft_id 
				from fiche_detail 
			where ad_id=25 and ad_value=old_qcode);


	update jrnx set j_qcode=tText where j_qcode = old_qcode;
	return ns;
	end;
$$;


SET search_path = public, pg_catalog;

--
-- Name: bud_card_ins_upd(); Type: FUNCTION; Schema: public; Owner: -
--

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


--
-- Name: bud_detail_ins_upd(); Type: FUNCTION; Schema: public; Owner: -
--

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


--
-- Name: correct_quant_purchase(); Type: FUNCTION; Schema: public; Owner: -
--

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


--
-- Name: correct_quant_sale(); Type: FUNCTION; Schema: public; Owner: -
--

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


SET default_tablespace = '';

SET default_with_oids = true;

--
-- Name: action; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE action (
    ac_id integer NOT NULL,
    ac_description text NOT NULL,
    ac_module text,
    ac_code character varying(9)
);


--
-- Name: TABLE action; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE action IS 'The different privileges';


--
-- Name: COLUMN action.ac_code; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action.ac_code IS 'this code will be used in the code with the function User::check_action ';


SET default_with_oids = false;

--
-- Name: action_detail; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE action_detail (
    ad_id integer NOT NULL,
    f_id bigint,
    ad_text text,
    ad_pu numeric(20,4) DEFAULT 0,
    ad_quant numeric(20,4) DEFAULT 0,
    ad_tva_id integer DEFAULT 0,
    ad_tva_amount numeric(20,4) DEFAULT 0,
    ad_total_amount numeric(20,4) DEFAULT 0,
    ag_id integer DEFAULT 0 NOT NULL
);


--
-- Name: TABLE action_detail; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE action_detail IS 'Detail of action_gestion, see class Action_Detail';


--
-- Name: COLUMN action_detail.f_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_detail.f_id IS 'the concerned	card';


--
-- Name: COLUMN action_detail.ad_text; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_detail.ad_text IS ' Description ';


--
-- Name: COLUMN action_detail.ad_pu; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_detail.ad_pu IS ' price per unit ';


--
-- Name: COLUMN action_detail.ad_quant; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_detail.ad_quant IS 'quantity ';


--
-- Name: COLUMN action_detail.ad_tva_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_detail.ad_tva_id IS ' tva_id ';


--
-- Name: COLUMN action_detail.ad_tva_amount; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_detail.ad_tva_amount IS ' tva_amount ';


--
-- Name: COLUMN action_detail.ad_total_amount; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_detail.ad_total_amount IS ' total amount';


--
-- Name: action_detail_ad_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE action_detail_ad_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: action_detail_ad_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE action_detail_ad_id_seq OWNED BY action_detail.ad_id;


--
-- Name: action_detail_ad_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('action_detail_ad_id_seq', 1, false);


--
-- Name: action_gestion_ag_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE action_gestion_ag_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: action_gestion_ag_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('action_gestion_ag_id_seq', 1, false);


SET default_with_oids = true;

--
-- Name: action_gestion; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE action_gestion (
    ag_id integer DEFAULT nextval('action_gestion_ag_id_seq'::regclass) NOT NULL,
    ag_type integer,
    f_id_dest integer NOT NULL,
    ag_title character varying(70),
    ag_timestamp timestamp without time zone DEFAULT now(),
    ag_cal character(1) DEFAULT 'C'::bpchar,
    ag_ref_ag_id integer,
    ag_comment text,
    ag_ref text,
    ag_hour text,
    ag_priority integer DEFAULT 2,
    ag_dest text,
    ag_owner text,
    ag_contact bigint,
    ag_state integer
);


--
-- Name: TABLE action_gestion; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE action_gestion IS 'Contains the details for the follow-up of customer, supplier, administration';


--
-- Name: COLUMN action_gestion.ag_type; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_type IS ' type of action: see document_type ';


--
-- Name: COLUMN action_gestion.f_id_dest; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.f_id_dest IS ' third party ';


--
-- Name: COLUMN action_gestion.ag_title; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_title IS ' title ';


--
-- Name: COLUMN action_gestion.ag_timestamp; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_timestamp IS ' ';


--
-- Name: COLUMN action_gestion.ag_cal; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_cal IS ' visible in the calendar if = C';


--
-- Name: COLUMN action_gestion.ag_ref_ag_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_ref_ag_id IS ' concerning the action ';


--
-- Name: COLUMN action_gestion.ag_comment; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_comment IS ' comment of the action';


--
-- Name: COLUMN action_gestion.ag_ref; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_ref IS 'its reference ';


--
-- Name: COLUMN action_gestion.ag_priority; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_priority IS 'Low, medium, important ';


--
-- Name: COLUMN action_gestion.ag_dest; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_dest IS ' is the person who has to take care of this action ';


--
-- Name: COLUMN action_gestion.ag_owner; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_owner IS ' is the owner of this action ';


--
-- Name: COLUMN action_gestion.ag_contact; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_contact IS ' contact of the third part ';


--
-- Name: COLUMN action_gestion.ag_state; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN action_gestion.ag_state IS 'state of the action same as document_state ';


--
-- Name: attr_def; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE attr_def (
    ad_id integer DEFAULT nextval(('s_attr_def'::text)::regclass) NOT NULL,
    ad_text text,
    ad_type text,
    ad_size text NOT NULL
);


--
-- Name: TABLE attr_def; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE attr_def IS 'The available attributs for the cards';


--
-- Name: attr_min; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE attr_min (
    frd_id integer NOT NULL,
    ad_id integer NOT NULL
);


--
-- Name: TABLE attr_min; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE attr_min IS 'The value of  attributs for the cards';


--
-- Name: bilan_b_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE bilan_b_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: bilan_b_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('bilan_b_id_seq', 4, true);


SET default_with_oids = false;

--
-- Name: bilan; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE bilan (
    b_id integer DEFAULT nextval('bilan_b_id_seq'::regclass) NOT NULL,
    b_name text NOT NULL,
    b_file_template text NOT NULL,
    b_file_form text,
    b_type text NOT NULL
);


--
-- Name: TABLE bilan; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE bilan IS 'contains the template and the data for generating different documents  ';


--
-- Name: COLUMN bilan.b_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bilan.b_id IS 'primary key';


--
-- Name: COLUMN bilan.b_name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bilan.b_name IS 'Name of the document';


--
-- Name: COLUMN bilan.b_file_template; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bilan.b_file_template IS 'path of the template (document/...)';


--
-- Name: COLUMN bilan.b_file_form; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bilan.b_file_form IS 'path of the file with forms';


--
-- Name: COLUMN bilan.b_type; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bilan.b_type IS 'type = ODS, RTF...';


--
-- Name: bud_card_bc_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE bud_card_bc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: bud_card_bc_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('bud_card_bc_id_seq', 1, false);


--
-- Name: bud_detail_bd_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE bud_detail_bd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: bud_detail_bd_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('bud_detail_bd_id_seq', 1, false);


--
-- Name: bud_detail_periode_bdp_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE bud_detail_periode_bdp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: bud_detail_periode_bdp_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('bud_detail_periode_bdp_id_seq', 1, false);


SET default_with_oids = true;

--
-- Name: centralized; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE centralized (
    c_id integer DEFAULT nextval(('s_centralized'::text)::regclass) NOT NULL,
    c_j_id integer,
    c_date date NOT NULL,
    c_internal text NOT NULL,
    c_montant numeric(20,4) NOT NULL,
    c_debit boolean DEFAULT true,
    c_jrn_def integer NOT NULL,
    c_poste account_type,
    c_description text,
    c_grp integer NOT NULL,
    c_comment text,
    c_rapt text,
    c_periode integer,
    c_order integer
);


--
-- Name: TABLE centralized; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE centralized IS 'The centralized journal';


SET default_with_oids = false;

--
-- Name: del_action; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE del_action (
    del_id integer NOT NULL,
    del_name text NOT NULL,
    del_time timestamp without time zone
);


--
-- Name: del_action_del_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE del_action_del_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: del_action_del_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE del_action_del_id_seq OWNED BY del_action.del_id;


--
-- Name: del_action_del_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('del_action_del_id_seq', 1, false);


--
-- Name: del_jrn; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE del_jrn (
    jr_id integer NOT NULL,
    jr_def_id integer,
    jr_montant numeric(20,4),
    jr_comment text,
    jr_date date,
    jr_grpt_id integer,
    jr_internal text,
    jr_tech_date timestamp without time zone,
    jr_tech_per integer,
    jrn_ech date,
    jr_ech date,
    jr_rapt text,
    jr_valid boolean,
    jr_opid integer,
    jr_c_opid integer,
    jr_pj oid,
    jr_pj_name text,
    jr_pj_type text,
    del_jrn_date timestamp without time zone,
    jr_pj_number text,
    dj_id integer NOT NULL
);


--
-- Name: del_jrn_dj_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE del_jrn_dj_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: del_jrn_dj_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE del_jrn_dj_id_seq OWNED BY del_jrn.dj_id;


--
-- Name: del_jrn_dj_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('del_jrn_dj_id_seq', 1, false);


--
-- Name: del_jrnx; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE del_jrnx (
    j_id integer NOT NULL,
    j_date date,
    j_montant numeric(20,4),
    j_poste account_type,
    j_grpt integer,
    j_rapt text,
    j_jrn_def integer,
    j_debit boolean,
    j_text text,
    j_centralized boolean,
    j_internal text,
    j_tech_user text,
    j_tech_date timestamp without time zone,
    j_tech_per integer,
    j_qcode text,
    djx_id integer NOT NULL,
    f_id bigint
);


--
-- Name: del_jrnx_djx_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE del_jrnx_djx_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: del_jrnx_djx_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE del_jrnx_djx_id_seq OWNED BY del_jrnx.djx_id;


--
-- Name: del_jrnx_djx_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('del_jrnx_djx_id_seq', 1, false);


--
-- Name: document_d_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE document_d_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: document_d_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('document_d_id_seq', 1, false);


SET default_with_oids = true;

--
-- Name: document; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE document (
    d_id integer DEFAULT nextval('document_d_id_seq'::regclass) NOT NULL,
    ag_id integer NOT NULL,
    d_lob oid,
    d_number bigint NOT NULL,
    d_filename text,
    d_mimetype text
);


--
-- Name: TABLE document; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE document IS 'This table contains all the documents : summary and lob files';


--
-- Name: document_modele_md_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE document_modele_md_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: document_modele_md_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('document_modele_md_id_seq', 1, false);


--
-- Name: document_modele; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE document_modele (
    md_id integer DEFAULT nextval('document_modele_md_id_seq'::regclass) NOT NULL,
    md_name text NOT NULL,
    md_lob oid,
    md_type integer NOT NULL,
    md_filename text,
    md_mimetype text,
    md_affect character varying(3) NOT NULL
);


--
-- Name: TABLE document_modele; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE document_modele IS ' contains all the template for the  documents';


--
-- Name: document_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE document_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: SEQUENCE document_seq; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON SEQUENCE document_seq IS 'Sequence for the sequence bound to the document modele';


--
-- Name: document_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('document_seq', 1, false);


--
-- Name: document_state_s_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE document_state_s_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: document_state_s_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('document_state_s_id_seq', 3, true);


--
-- Name: document_state; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE document_state (
    s_id integer DEFAULT nextval('document_state_s_id_seq'::regclass) NOT NULL,
    s_value character varying(50) NOT NULL
);


--
-- Name: TABLE document_state; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE document_state IS 'State of the document';


--
-- Name: document_type_dt_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE document_type_dt_id_seq
    START WITH 25
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: document_type_dt_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('document_type_dt_id_seq', 25, false);


--
-- Name: document_type; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE document_type (
    dt_id integer DEFAULT nextval('document_type_dt_id_seq'::regclass) NOT NULL,
    dt_value character varying(80)
);


--
-- Name: TABLE document_type; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE document_type IS 'Type of document : meeting, invoice,...';


SET default_with_oids = false;

--
-- Name: extension; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE extension (
    ex_id integer NOT NULL,
    ex_name character varying(30) NOT NULL,
    ex_code character varying(15) NOT NULL,
    ex_desc character varying(250),
    ex_file character varying NOT NULL,
    ex_enable "char" DEFAULT 'Y'::"char" NOT NULL
);


--
-- Name: TABLE extension; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE extension IS 'Content the needed information for the extension';


--
-- Name: COLUMN extension.ex_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN extension.ex_id IS 'Primary key';


--
-- Name: COLUMN extension.ex_name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN extension.ex_name IS 'code of the extension ';


--
-- Name: COLUMN extension.ex_code; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN extension.ex_code IS 'code of the extension ';


--
-- Name: COLUMN extension.ex_desc; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN extension.ex_desc IS 'Description of the extension ';


--
-- Name: COLUMN extension.ex_file; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN extension.ex_file IS 'path to the extension to include';


--
-- Name: COLUMN extension.ex_enable; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN extension.ex_enable IS 'Y : enabled N : disabled ';


--
-- Name: extension_ex_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE extension_ex_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: extension_ex_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE extension_ex_id_seq OWNED BY extension.ex_id;


--
-- Name: extension_ex_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('extension_ex_id_seq', 1, true);


SET default_with_oids = true;

--
-- Name: fiche; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE fiche (
    f_id integer DEFAULT nextval(('s_fiche'::text)::regclass) NOT NULL,
    fd_id integer
);


--
-- Name: TABLE fiche; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE fiche IS 'Cards';


--
-- Name: fiche_def; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE fiche_def (
    fd_id integer DEFAULT nextval(('s_fdef'::text)::regclass) NOT NULL,
    fd_class_base text,
    fd_label text NOT NULL,
    fd_create_account boolean DEFAULT false,
    frd_id integer NOT NULL
);


--
-- Name: TABLE fiche_def; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE fiche_def IS 'Cards definition';


--
-- Name: fiche_def_ref; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE fiche_def_ref (
    frd_id integer DEFAULT nextval(('s_fiche_def_ref'::text)::regclass) NOT NULL,
    frd_text text,
    frd_class_base account_type
);


--
-- Name: TABLE fiche_def_ref; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE fiche_def_ref IS 'Family Cards definition';


--
-- Name: fiche_detail; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE fiche_detail (
    jft_id integer DEFAULT nextval(('s_jnt_fic_att_value'::text)::regclass) NOT NULL,
    f_id integer,
    ad_id integer,
    ad_value text
);


--
-- Name: TABLE fiche_detail; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE fiche_detail IS 'join between the card and the attribut definition';


SET default_with_oids = false;

--
-- Name: forecast; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE forecast (
    f_id integer NOT NULL,
    f_name text NOT NULL,
    f_start_date bigint,
    f_end_date bigint
);


--
-- Name: TABLE forecast; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE forecast IS 'contains the name of the forecast';


--
-- Name: forecast_cat; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE forecast_cat (
    fc_id integer NOT NULL,
    fc_desc text NOT NULL,
    f_id bigint,
    fc_order integer DEFAULT 0 NOT NULL
);


--
-- Name: COLUMN forecast_cat.fc_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_cat.fc_id IS 'primary key';


--
-- Name: COLUMN forecast_cat.fc_desc; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_cat.fc_desc IS 'text of the category';


--
-- Name: COLUMN forecast_cat.f_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_cat.f_id IS 'Foreign key, it is the parent from the table forecast';


--
-- Name: COLUMN forecast_cat.fc_order; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_cat.fc_order IS 'Order of the category, used when displaid';


--
-- Name: forecast_cat_fc_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE forecast_cat_fc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: forecast_cat_fc_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE forecast_cat_fc_id_seq OWNED BY forecast_cat.fc_id;


--
-- Name: forecast_cat_fc_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('forecast_cat_fc_id_seq', 1, false);


--
-- Name: forecast_f_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE forecast_f_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: forecast_f_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE forecast_f_id_seq OWNED BY forecast.f_id;


--
-- Name: forecast_f_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('forecast_f_id_seq', 1, false);


--
-- Name: forecast_item; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE forecast_item (
    fi_id integer NOT NULL,
    fi_text text,
    fi_account text,
    fi_card integer,
    fi_order integer,
    fc_id integer,
    fi_amount numeric(20,4) DEFAULT 0,
    fi_debit "char" DEFAULT 'd'::"char" NOT NULL,
    fi_pid integer
);


--
-- Name: COLUMN forecast_item.fi_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_item.fi_id IS 'Primary key';


--
-- Name: COLUMN forecast_item.fi_text; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_item.fi_text IS 'Label of the i	tem';


--
-- Name: COLUMN forecast_item.fi_account; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_item.fi_account IS 'Accountancy entry';


--
-- Name: COLUMN forecast_item.fi_card; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_item.fi_card IS 'Card (fiche.f_id)';


--
-- Name: COLUMN forecast_item.fi_order; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_item.fi_order IS 'Order of showing (not used)';


--
-- Name: COLUMN forecast_item.fi_amount; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_item.fi_amount IS 'Amount';


--
-- Name: COLUMN forecast_item.fi_debit; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_item.fi_debit IS 'possible values are D or C';


--
-- Name: COLUMN forecast_item.fi_pid; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN forecast_item.fi_pid IS '0 for every month, or the value parm_periode.p_id ';


--
-- Name: forecast_item_fi_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE forecast_item_fi_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: forecast_item_fi_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE forecast_item_fi_id_seq OWNED BY forecast_item.fi_id;


--
-- Name: forecast_item_fi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('forecast_item_fi_id_seq', 1, false);


SET default_with_oids = true;

--
-- Name: form; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE form (
    fo_id integer DEFAULT nextval(('s_form'::text)::regclass) NOT NULL,
    fo_fr_id integer,
    fo_pos integer,
    fo_label text,
    fo_formula text
);


--
-- Name: TABLE form; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE form IS 'Forms content';


--
-- Name: formdef; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE formdef (
    fr_id integer DEFAULT nextval(('s_formdef'::text)::regclass) NOT NULL,
    fr_label text
);


SET default_with_oids = false;

--
-- Name: groupe_analytique; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE groupe_analytique (
    ga_id character varying(10) NOT NULL,
    pa_id integer,
    ga_description text
);


--
-- Name: historique_analytique_ha_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE historique_analytique_ha_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: historique_analytique_ha_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('historique_analytique_ha_id_seq', 1, false);


--
-- Name: info_def; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE info_def (
    id_type text NOT NULL,
    id_description text
);


--
-- Name: TABLE info_def; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE info_def IS 'Contains the types of additionnal info we can add to a operation';


--
-- Name: s_jnt_id; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jnt_id
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jnt_id; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jnt_id', 53, true);


SET default_with_oids = true;

--
-- Name: jnt_fic_attr; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jnt_fic_attr (
    fd_id integer,
    ad_id integer,
    jnt_id bigint DEFAULT nextval('s_jnt_id'::regclass) NOT NULL,
    jnt_order integer NOT NULL
);


--
-- Name: TABLE jnt_fic_attr; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE jnt_fic_attr IS 'join between the family card and the attribut definition';


SET default_with_oids = false;

--
-- Name: jnt_letter; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jnt_letter (
    jl_id integer NOT NULL,
    jl_amount_deb numeric(20,4)
);


--
-- Name: jnt_letter_jl_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE jnt_letter_jl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: jnt_letter_jl_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE jnt_letter_jl_id_seq OWNED BY jnt_letter.jl_id;


--
-- Name: jnt_letter_jl_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('jnt_letter_jl_id_seq', 1, false);


SET default_with_oids = true;

--
-- Name: jrn; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jrn (
    jr_id integer DEFAULT nextval(('s_jrn'::text)::regclass) NOT NULL,
    jr_def_id integer NOT NULL,
    jr_montant numeric(20,4) NOT NULL,
    jr_comment text,
    jr_date date,
    jr_grpt_id integer NOT NULL,
    jr_internal text,
    jr_tech_date timestamp without time zone DEFAULT now() NOT NULL,
    jr_tech_per integer NOT NULL,
    jrn_ech date,
    jr_ech date,
    jr_rapt text,
    jr_valid boolean DEFAULT true,
    jr_opid integer,
    jr_c_opid integer,
    jr_pj oid,
    jr_pj_name text,
    jr_pj_type text,
    jr_pj_number text,
    jr_mt text
);


--
-- Name: TABLE jrn; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE jrn IS 'Journal: content one line for a group of accountancy writing';


--
-- Name: jrn_action; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jrn_action (
    ja_id integer DEFAULT nextval(('s_jrnaction'::text)::regclass) NOT NULL,
    ja_name text NOT NULL,
    ja_desc text,
    ja_url text NOT NULL,
    ja_action text NOT NULL,
    ja_lang text DEFAULT 'FR'::text,
    ja_jrn_type character(3)
);


--
-- Name: TABLE jrn_action; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE jrn_action IS 'Possible action when we are in journal (menu)';


--
-- Name: jrn_def; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jrn_def (
    jrn_def_id integer DEFAULT nextval(('s_jrn_def'::text)::regclass) NOT NULL,
    jrn_def_name text NOT NULL,
    jrn_def_class_deb text,
    jrn_def_class_cred text,
    jrn_def_fiche_deb text,
    jrn_def_fiche_cred text,
    jrn_deb_max_line integer DEFAULT 1,
    jrn_cred_max_line integer DEFAULT 1,
    jrn_def_ech boolean DEFAULT false,
    jrn_def_ech_lib text,
    jrn_def_type character(3) NOT NULL,
    jrn_def_code text NOT NULL,
    jrn_def_pj_pref text,
    jrn_def_bank bigint,
    jrn_def_num_op integer
);


--
-- Name: TABLE jrn_def; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE jrn_def IS 'Definition of a journal, his properties';


SET default_with_oids = false;

--
-- Name: jrn_info; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jrn_info (
    ji_id integer NOT NULL,
    jr_id integer NOT NULL,
    id_type text NOT NULL,
    ji_value text
);


--
-- Name: jrn_info_ji_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE jrn_info_ji_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: jrn_info_ji_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE jrn_info_ji_id_seq OWNED BY jrn_info.ji_id;


--
-- Name: jrn_info_ji_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('jrn_info_ji_id_seq', 1, false);


--
-- Name: jrn_note; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jrn_note (
    n_id integer NOT NULL,
    n_text text,
    jr_id bigint NOT NULL
);


--
-- Name: TABLE jrn_note; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE jrn_note IS 'Note about operation';


--
-- Name: jrn_note_n_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE jrn_note_n_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: jrn_note_n_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE jrn_note_n_id_seq OWNED BY jrn_note.n_id;


--
-- Name: jrn_note_n_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('jrn_note_n_id_seq', 1, false);


--
-- Name: jrn_periode; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jrn_periode (
    jrn_def_id integer NOT NULL,
    p_id integer NOT NULL,
    status text
);


SET default_with_oids = true;

--
-- Name: jrn_rapt; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jrn_rapt (
    jra_id integer DEFAULT nextval(('s_jrn_rapt'::text)::regclass) NOT NULL,
    jr_id integer NOT NULL,
    jra_concerned integer NOT NULL
);


--
-- Name: TABLE jrn_rapt; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE jrn_rapt IS 'Rapprochement between operation';


--
-- Name: jrn_type; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jrn_type (
    jrn_type_id character(3) NOT NULL,
    jrn_desc text
);


--
-- Name: TABLE jrn_type; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE jrn_type IS 'Type of journal (Sell, Buy, Financial...)';


--
-- Name: jrnx; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE jrnx (
    j_id integer DEFAULT nextval(('s_jrn_op'::text)::regclass) NOT NULL,
    j_date date DEFAULT now(),
    j_montant numeric(20,4) DEFAULT 0,
    j_poste account_type NOT NULL,
    j_grpt integer NOT NULL,
    j_rapt text,
    j_jrn_def integer NOT NULL,
    j_debit boolean DEFAULT true,
    j_text text,
    j_centralized boolean DEFAULT false,
    j_internal text,
    j_tech_user text NOT NULL,
    j_tech_date timestamp without time zone DEFAULT now() NOT NULL,
    j_tech_per integer NOT NULL,
    j_qcode text,
    f_id bigint
);


--
-- Name: TABLE jrnx; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE jrnx IS 'Journal: content one line for each accountancy writing';


SET default_with_oids = false;

--
-- Name: letter_cred; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE letter_cred (
    lc_id integer NOT NULL,
    j_id bigint NOT NULL,
    jl_id bigint NOT NULL
);


--
-- Name: letter_cred_lc_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE letter_cred_lc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: letter_cred_lc_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE letter_cred_lc_id_seq OWNED BY letter_cred.lc_id;


--
-- Name: letter_cred_lc_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('letter_cred_lc_id_seq', 1, false);


--
-- Name: letter_deb; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE letter_deb (
    ld_id integer NOT NULL,
    j_id bigint NOT NULL,
    jl_id bigint NOT NULL
);


--
-- Name: letter_deb_ld_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE letter_deb_ld_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: letter_deb_ld_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE letter_deb_ld_id_seq OWNED BY letter_deb.ld_id;


--
-- Name: letter_deb_ld_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('letter_deb_ld_id_seq', 1, false);


--
-- Name: menu_ref; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE menu_ref (
    me_code text NOT NULL,
    me_menu text,
    me_file text,
    me_url text,
    me_description text,
    me_parameter text,
    me_javascript text,
    me_type character varying(2)
);


--
-- Name: COLUMN menu_ref.me_code; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN menu_ref.me_code IS 'Menu Code ';


--
-- Name: COLUMN menu_ref.me_menu; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN menu_ref.me_menu IS 'Label to display';


--
-- Name: COLUMN menu_ref.me_file; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN menu_ref.me_file IS 'if not empty file to include';


--
-- Name: COLUMN menu_ref.me_url; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN menu_ref.me_url IS 'url ';


--
-- Name: COLUMN menu_ref.me_type; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN menu_ref.me_type IS 'ME for menu
PR for Printing
SP for special meaning (ex: return to line)
PL for plugin';


--
-- Name: mod_payment; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE mod_payment (
    mp_id integer NOT NULL,
    mp_lib text NOT NULL,
    mp_jrn_def_id integer NOT NULL,
    mp_fd_id bigint,
    mp_qcode text,
    jrn_def_id bigint
);


--
-- Name: TABLE mod_payment; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE mod_payment IS 'Contains the different media of payment and the corresponding ledger';


--
-- Name: COLUMN mod_payment.jrn_def_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN mod_payment.jrn_def_id IS 'Ledger using this payment method';


--
-- Name: mod_payment_mp_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE mod_payment_mp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: mod_payment_mp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE mod_payment_mp_id_seq OWNED BY mod_payment.mp_id;


--
-- Name: mod_payment_mp_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('mod_payment_mp_id_seq', 10, true);


--
-- Name: op_def_op_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE op_def_op_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: op_def_op_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('op_def_op_seq', 1, false);


--
-- Name: op_predef; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE op_predef (
    od_id integer DEFAULT nextval('op_def_op_seq'::regclass) NOT NULL,
    jrn_def_id integer NOT NULL,
    od_name text NOT NULL,
    od_item integer NOT NULL,
    od_jrn_type text NOT NULL,
    od_direct boolean NOT NULL
);


--
-- Name: TABLE op_predef; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE op_predef IS 'predefined operation';


--
-- Name: COLUMN op_predef.jrn_def_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN op_predef.jrn_def_id IS 'jrn_id';


--
-- Name: COLUMN op_predef.od_name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN op_predef.od_name IS 'name of the operation';


--
-- Name: op_predef_detail_opd_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE op_predef_detail_opd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: op_predef_detail_opd_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('op_predef_detail_opd_id_seq', 1, false);


--
-- Name: op_predef_detail; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE op_predef_detail (
    opd_id integer DEFAULT nextval('op_predef_detail_opd_id_seq'::regclass) NOT NULL,
    od_id integer NOT NULL,
    opd_poste text NOT NULL,
    opd_amount numeric(20,4),
    opd_tva_id integer,
    opd_quantity numeric(20,4),
    opd_debit boolean NOT NULL,
    opd_tva_amount numeric(20,4),
    opd_comment text,
    opd_qc boolean
);


--
-- Name: TABLE op_predef_detail; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE op_predef_detail IS 'contains the detail of predefined operations';


--
-- Name: s_oa_group; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_oa_group
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_oa_group; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_oa_group', 1, true);


--
-- Name: operation_analytique; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE operation_analytique (
    oa_id integer DEFAULT nextval('historique_analytique_ha_id_seq'::regclass) NOT NULL,
    po_id integer NOT NULL,
    oa_amount numeric(20,4) NOT NULL,
    oa_description text,
    oa_debit boolean DEFAULT true NOT NULL,
    j_id integer,
    oa_group integer DEFAULT nextval('s_oa_group'::regclass) NOT NULL,
    oa_date date NOT NULL,
    oa_row integer,
    CONSTRAINT operation_analytique_oa_amount_check CHECK ((oa_amount >= (0)::numeric))
);


--
-- Name: TABLE operation_analytique; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE operation_analytique IS 'History of the analytic account';


SET default_with_oids = true;

--
-- Name: parameter; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE parameter (
    pr_id text NOT NULL,
    pr_value text
);


--
-- Name: TABLE parameter; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE parameter IS 'parameter of the company';


--
-- Name: parm_code; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE parm_code (
    p_code text NOT NULL,
    p_value text,
    p_comment text
);


--
-- Name: parm_money; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE parm_money (
    pm_id integer DEFAULT nextval(('s_currency'::text)::regclass),
    pm_code character(3) NOT NULL,
    pm_rate numeric(20,4)
);


--
-- Name: TABLE parm_money; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE parm_money IS 'Currency conversion';


--
-- Name: parm_periode; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE parm_periode (
    p_id integer DEFAULT nextval(('s_periode'::text)::regclass) NOT NULL,
    p_start date NOT NULL,
    p_end date NOT NULL,
    p_exercice text DEFAULT to_char(now(), 'YYYY'::text) NOT NULL,
    p_closed boolean DEFAULT false,
    p_central boolean DEFAULT false,
    CONSTRAINT parm_periode_check CHECK ((p_end >= p_start))
);


--
-- Name: TABLE parm_periode; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE parm_periode IS 'Periode definition';


SET default_with_oids = false;

--
-- Name: parm_poste; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE parm_poste (
    p_value account_type NOT NULL,
    p_type text NOT NULL
);


--
-- Name: TABLE parm_poste; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE parm_poste IS 'Contains data for finding is the type of the account (asset)';


--
-- Name: plan_analytique_pa_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE plan_analytique_pa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: plan_analytique_pa_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('plan_analytique_pa_id_seq', 1, false);


--
-- Name: plan_analytique; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE plan_analytique (
    pa_id integer DEFAULT nextval('plan_analytique_pa_id_seq'::regclass) NOT NULL,
    pa_name text DEFAULT 'Sans Nom'::text NOT NULL,
    pa_description text
);


--
-- Name: TABLE plan_analytique; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE plan_analytique IS 'Plan Analytique (max 5)';


--
-- Name: poste_analytique_po_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE poste_analytique_po_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: poste_analytique_po_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('poste_analytique_po_id_seq', 1, false);


--
-- Name: poste_analytique; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE poste_analytique (
    po_id integer DEFAULT nextval('poste_analytique_po_id_seq'::regclass) NOT NULL,
    po_name text NOT NULL,
    pa_id integer NOT NULL,
    po_amount numeric(20,4) DEFAULT 0.0 NOT NULL,
    po_description text,
    ga_id character varying(10)
);


--
-- Name: TABLE poste_analytique; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE poste_analytique IS 'Poste Analytique';


--
-- Name: profile; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE profile (
    p_name text NOT NULL,
    p_id integer NOT NULL,
    p_desc text,
    with_calc boolean DEFAULT true,
    with_direct_form boolean DEFAULT true
);


--
-- Name: TABLE profile; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE profile IS 'Available profile ';


--
-- Name: COLUMN profile.p_name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile.p_name IS 'Name of the profile';


--
-- Name: COLUMN profile.p_desc; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile.p_desc IS 'description of the profile';


--
-- Name: COLUMN profile.with_calc; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile.with_calc IS 'show the calculator';


--
-- Name: COLUMN profile.with_direct_form; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile.with_direct_form IS 'show the direct form';


--
-- Name: profile_menu; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE profile_menu (
    pm_id integer NOT NULL,
    me_code text,
    me_code_dep text,
    p_id integer,
    p_order integer,
    p_type_display text NOT NULL,
    pm_default integer
);


--
-- Name: TABLE profile_menu; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE profile_menu IS 'Join  between the profile and the menu ';


--
-- Name: COLUMN profile_menu.me_code_dep; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile_menu.me_code_dep IS 'menu code dependency';


--
-- Name: COLUMN profile_menu.p_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile_menu.p_id IS 'link to profile';


--
-- Name: COLUMN profile_menu.p_order; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile_menu.p_order IS 'order of displaying menu';


--
-- Name: COLUMN profile_menu.p_type_display; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile_menu.p_type_display IS 'M is a module
E is a menu
S is a select (for plugin)';


--
-- Name: COLUMN profile_menu.pm_default; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile_menu.pm_default IS 'default menu';


--
-- Name: profile_menu_pm_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE profile_menu_pm_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: profile_menu_pm_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE profile_menu_pm_id_seq OWNED BY profile_menu.pm_id;


--
-- Name: profile_menu_pm_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('profile_menu_pm_id_seq', 779, true);


--
-- Name: profile_menu_type; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE profile_menu_type (
    pm_type text NOT NULL,
    pm_desc text
);


--
-- Name: profile_p_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE profile_p_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: profile_p_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE profile_p_id_seq OWNED BY profile.p_id;


--
-- Name: profile_p_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('profile_p_id_seq', 11, true);


--
-- Name: profile_user; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE profile_user (
    user_name text NOT NULL,
    pu_id integer NOT NULL,
    p_id integer
);


--
-- Name: TABLE profile_user; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE profile_user IS 'Contains the available profile for users';


--
-- Name: COLUMN profile_user.user_name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile_user.user_name IS 'fk to available_user : login';


--
-- Name: COLUMN profile_user.p_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN profile_user.p_id IS 'fk to profile';


--
-- Name: profile_user_pu_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE profile_user_pu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: profile_user_pu_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE profile_user_pu_id_seq OWNED BY profile_user.pu_id;


--
-- Name: profile_user_pu_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('profile_user_pu_id_seq', 6, true);


--
-- Name: quant_fin; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE quant_fin (
    qf_id bigint NOT NULL,
    qf_bank bigint,
    jr_id bigint,
    qf_other bigint,
    qf_amount numeric(20,4) DEFAULT 0
);


--
-- Name: TABLE quant_fin; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE quant_fin IS 'Simple operation for financial';


--
-- Name: quant_fin_qf_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE quant_fin_qf_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: quant_fin_qf_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE quant_fin_qf_id_seq OWNED BY quant_fin.qf_id;


--
-- Name: quant_fin_qf_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('quant_fin_qf_id_seq', 1, false);


--
-- Name: quant_purchase; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE quant_purchase (
    qp_id integer DEFAULT nextval(('s_quantity'::text)::regclass) NOT NULL,
    qp_internal text,
    j_id integer NOT NULL,
    qp_fiche integer NOT NULL,
    qp_quantite numeric(20,4) NOT NULL,
    qp_price numeric(20,4),
    qp_vat numeric(20,4) DEFAULT 0.0,
    qp_vat_code integer,
    qp_nd_amount numeric(20,4) DEFAULT 0.0,
    qp_nd_tva numeric(20,4) DEFAULT 0.0,
    qp_nd_tva_recup numeric(20,4) DEFAULT 0.0,
    qp_supplier integer NOT NULL,
    qp_valid character(1) DEFAULT 'Y'::bpchar NOT NULL,
    qp_dep_priv numeric(20,4) DEFAULT 0.0,
    qp_vat_sided numeric(20,4) DEFAULT 0.0
);


--
-- Name: COLUMN quant_purchase.qp_vat_sided; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN quant_purchase.qp_vat_sided IS 'amount of the VAT which avoid VAT, case of the VAT which add the same amount at the deb and cred';


SET default_with_oids = true;

--
-- Name: quant_sold; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE quant_sold (
    qs_id integer DEFAULT nextval(('s_quantity'::text)::regclass) NOT NULL,
    qs_internal text,
    qs_fiche integer NOT NULL,
    qs_quantite numeric(20,4) NOT NULL,
    qs_price numeric(20,4),
    qs_vat numeric(20,4),
    qs_vat_code integer,
    qs_client integer NOT NULL,
    qs_valid character(1) DEFAULT 'Y'::bpchar NOT NULL,
    j_id integer NOT NULL,
    qs_vat_sided numeric(20,4) DEFAULT 0.0
);


--
-- Name: TABLE quant_sold; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE quant_sold IS 'Contains about invoice for customer';


--
-- Name: s_attr_def; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_attr_def
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_attr_def; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_attr_def', 9001, false);


--
-- Name: s_cbc; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_cbc
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_cbc; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_cbc', 1, false);


--
-- Name: s_central; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_central
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_central; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_central', 1, false);


--
-- Name: s_central_order; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_central_order
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_central_order; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_central_order', 1, false);


--
-- Name: s_centralized; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_centralized
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_centralized; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_centralized', 1, false);


--
-- Name: s_currency; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_currency
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_currency; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_currency', 1, true);


--
-- Name: s_fdef; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_fdef
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_fdef; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_fdef', 6, true);


--
-- Name: s_fiche; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_fiche
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_fiche; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_fiche', 20, true);


--
-- Name: s_fiche_def_ref; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_fiche_def_ref
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_fiche_def_ref; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_fiche_def_ref', 16, true);


--
-- Name: s_form; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_form
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_form; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_form', 1, false);


--
-- Name: s_formdef; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_formdef
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_formdef; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_formdef', 1, false);


--
-- Name: s_grpt; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_grpt
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_grpt; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_grpt', 2, true);


--
-- Name: s_idef; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_idef
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_idef; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_idef', 1, false);


--
-- Name: s_internal; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_internal
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_internal; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_internal', 1, false);


--
-- Name: s_invoice; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_invoice
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_invoice; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_invoice', 1, false);


--
-- Name: s_isup; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_isup
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_isup; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_isup', 1, false);


--
-- Name: s_jnt_fic_att_value; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jnt_fic_att_value
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jnt_fic_att_value; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jnt_fic_att_value', 371, true);


--
-- Name: s_jrn; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn', 1, false);


--
-- Name: s_jrn_1; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_1; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_1', 1, false);


--
-- Name: s_jrn_2; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_2
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_2; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_2', 1, false);


--
-- Name: s_jrn_3; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_3
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_3; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_3', 1, false);


--
-- Name: s_jrn_4; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_4
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_4; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_4', 1, false);


--
-- Name: s_jrn_def; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_def
    START WITH 5
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_def; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_def', 5, false);


--
-- Name: s_jrn_op; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_op
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_op; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_op', 1, false);


--
-- Name: s_jrn_pj1; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_pj1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_pj1; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_pj1', 1, false);


--
-- Name: s_jrn_pj2; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_pj2
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_pj2; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_pj2', 1, false);


--
-- Name: s_jrn_pj3; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_pj3
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_pj3; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_pj3', 1, false);


--
-- Name: s_jrn_pj4; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_pj4
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_pj4; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_pj4', 1, false);


--
-- Name: s_jrn_rapt; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrn_rapt
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrn_rapt; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrn_rapt', 1, false);


--
-- Name: s_jrnaction; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrnaction
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrnaction; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrnaction', 5, true);


--
-- Name: s_jrnx; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_jrnx
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_jrnx; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_jrnx', 1, false);


--
-- Name: s_periode; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_periode
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_periode; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_periode', 91, true);


--
-- Name: s_quantity; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_quantity
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_quantity; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_quantity', 7, true);


--
-- Name: s_stock_goods; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_stock_goods
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_stock_goods; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_stock_goods', 1, false);


--
-- Name: s_tva; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_tva
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_tva; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_tva', 1001, true);


--
-- Name: s_user_act; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_user_act
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_user_act; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_user_act', 1, false);


--
-- Name: s_user_jrn; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_user_jrn
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: s_user_jrn; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('s_user_jrn', 1, false);


--
-- Name: seq_bud_hypothese_bh_id; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_bud_hypothese_bh_id
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_bud_hypothese_bh_id; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_bud_hypothese_bh_id', 1, false);


--
-- Name: seq_doc_type_1; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_1; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_1', 1, false);


--
-- Name: seq_doc_type_10; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_10
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_10; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_10', 1, false);


--
-- Name: seq_doc_type_2; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_2
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_2; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_2', 1, false);


--
-- Name: seq_doc_type_20; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_20
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_20; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_20', 1, false);


--
-- Name: seq_doc_type_21; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_21
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_21; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_21', 1, false);


--
-- Name: seq_doc_type_22; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_22
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_22; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_22', 1, false);


--
-- Name: seq_doc_type_3; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_3
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_3; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_3', 1, false);


--
-- Name: seq_doc_type_4; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_4
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_4; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_4', 1, false);


--
-- Name: seq_doc_type_5; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_5
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_5; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_5', 1, false);


--
-- Name: seq_doc_type_6; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_6
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_6; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_6', 1, false);


--
-- Name: seq_doc_type_7; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_7
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_7; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_7', 1, false);


--
-- Name: seq_doc_type_8; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_8
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_8; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_8', 1, false);


--
-- Name: seq_doc_type_9; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seq_doc_type_9
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: seq_doc_type_9; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seq_doc_type_9', 1, false);


--
-- Name: stock_goods; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE stock_goods (
    sg_id integer DEFAULT nextval(('s_stock_goods'::text)::regclass) NOT NULL,
    j_id integer,
    f_id integer,
    sg_code text,
    sg_quantity numeric(8,4) DEFAULT 0,
    sg_type character(1) DEFAULT 'c'::bpchar NOT NULL,
    sg_date date,
    sg_tech_date date DEFAULT now(),
    sg_tech_user text,
    sg_comment character varying(80),
    sg_exercice character varying(4),
    CONSTRAINT stock_goods_sg_type CHECK (((sg_type = 'c'::bpchar) OR (sg_type = 'd'::bpchar)))
);


--
-- Name: TABLE stock_goods; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE stock_goods IS 'About the goods';


--
-- Name: tmp_pcmn; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE tmp_pcmn (
    pcm_val account_type NOT NULL,
    pcm_lib text,
    pcm_val_parent account_type DEFAULT 0,
    pcm_type text
);


--
-- Name: TABLE tmp_pcmn; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE tmp_pcmn IS 'Plan comptable minimum normalisé';


--
-- Name: todo_list_tl_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE todo_list_tl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: todo_list_tl_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('todo_list_tl_id_seq', 1, false);


SET default_with_oids = false;

--
-- Name: todo_list; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE todo_list (
    tl_id integer DEFAULT nextval('todo_list_tl_id_seq'::regclass) NOT NULL,
    tl_date date NOT NULL,
    tl_title text NOT NULL,
    tl_desc text,
    use_login text NOT NULL
);


--
-- Name: TABLE todo_list; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE todo_list IS 'Todo list';


SET default_with_oids = true;

--
-- Name: tva_rate; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE tva_rate (
    tva_id integer DEFAULT nextval('s_tva'::regclass) NOT NULL,
    tva_label text NOT NULL,
    tva_rate numeric(8,4) DEFAULT 0.0 NOT NULL,
    tva_comment text,
    tva_poste text,
    tva_both_side integer DEFAULT 0
);


--
-- Name: TABLE tva_rate; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE tva_rate IS 'Rate of vat';


--
-- Name: user_local_pref; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_local_pref (
    user_id text NOT NULL,
    parameter_type text NOT NULL,
    parameter_value text
);


--
-- Name: TABLE user_local_pref; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE user_local_pref IS 'The user''s local parameter ';


--
-- Name: COLUMN user_local_pref.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN user_local_pref.user_id IS 'user''s login ';


--
-- Name: COLUMN user_local_pref.parameter_type; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN user_local_pref.parameter_type IS 'the type of parameter ';


--
-- Name: COLUMN user_local_pref.parameter_value; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN user_local_pref.parameter_value IS 'the value of parameter ';


--
-- Name: user_sec_act; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_sec_act (
    ua_id integer DEFAULT nextval(('s_user_act'::text)::regclass) NOT NULL,
    ua_login text,
    ua_act_id integer
);


SET default_with_oids = false;

--
-- Name: user_sec_extension; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_sec_extension (
    use_id integer NOT NULL,
    ex_id integer NOT NULL,
    use_login text NOT NULL,
    use_access character(1) DEFAULT 0 NOT NULL
);


--
-- Name: TABLE user_sec_extension; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE user_sec_extension IS 'Security for extension';


--
-- Name: user_sec_extension_use_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE user_sec_extension_use_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: user_sec_extension_use_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE user_sec_extension_use_id_seq OWNED BY user_sec_extension.use_id;


--
-- Name: user_sec_extension_use_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('user_sec_extension_use_id_seq', 1, true);


SET default_with_oids = true;

--
-- Name: user_sec_jrn; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_sec_jrn (
    uj_id integer DEFAULT nextval(('s_user_jrn'::text)::regclass) NOT NULL,
    uj_login text,
    uj_jrn_id integer,
    uj_priv text
);


--
-- Name: v_all_menu; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW v_all_menu AS
    SELECT pm.me_code, pm.pm_id, pm.me_code_dep, pm.p_order, pm.p_type_display, pu.user_name, pu.pu_id, p.p_name, p.p_desc, mr.me_menu, mr.me_file, mr.me_url, mr.me_parameter, mr.me_javascript, mr.me_type, pm.p_id, mr.me_description FROM (((profile_menu pm JOIN profile_user pu ON ((pu.p_id = pm.p_id))) JOIN profile p ON ((p.p_id = pm.p_id))) JOIN menu_ref mr USING (me_code)) ORDER BY pm.p_order;


--
-- Name: version; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE version (
    val integer
);


--
-- Name: vw_client; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_client AS
    SELECT fiche.f_id, a1.ad_value AS name, a.ad_value AS quick_code, b.ad_value AS tva_num, c.ad_value AS poste_comptable, d.ad_value AS rue, e.ad_value AS code_postal, f.ad_value AS pays, g.ad_value AS telephone, h.ad_value AS email FROM (((((((((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 1)) a1 USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 13)) b USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 23)) a USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 5)) c USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 14)) d USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 15)) e USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 16)) f USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 17)) g USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 18)) h USING (f_id)) WHERE (fiche_def_ref.frd_id = 9);


--
-- Name: vw_fiche_attr; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_fiche_attr AS
    SELECT a.f_id, a.fd_id, a.ad_value AS vw_name, k.ad_value AS vw_first_name, b.ad_value AS vw_sell, c.ad_value AS vw_buy, d.ad_value AS tva_code, tva_rate.tva_id, tva_rate.tva_rate, tva_rate.tva_label, e.ad_value AS vw_addr, f.ad_value AS vw_cp, j.ad_value AS quick_code, h.ad_value AS vw_description, i.ad_value AS tva_num, fiche_def.frd_id FROM ((((((((((((SELECT fiche.f_id, fiche.fd_id, fiche_detail.ad_value FROM (fiche LEFT JOIN fiche_detail USING (f_id)) WHERE (fiche_detail.ad_id = 1)) a LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 6)) b ON ((a.f_id = b.f_id))) LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 7)) c ON ((a.f_id = c.f_id))) LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 2)) d ON ((a.f_id = d.f_id))) LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 14)) e ON ((a.f_id = e.f_id))) LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 15)) f ON ((a.f_id = f.f_id))) LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 23)) j ON ((a.f_id = j.f_id))) LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 9)) h ON ((a.f_id = h.f_id))) LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 13)) i ON ((a.f_id = i.f_id))) LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 32)) k ON ((a.f_id = k.f_id))) LEFT JOIN tva_rate ON ((d.ad_value = (tva_rate.tva_id)::text))) JOIN fiche_def USING (fd_id));


--
-- Name: vw_fiche_def; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_fiche_def AS
    SELECT jnt_fic_attr.fd_id, jnt_fic_attr.ad_id, attr_def.ad_text, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def.frd_id FROM ((fiche_def JOIN jnt_fic_attr USING (fd_id)) JOIN attr_def ON ((attr_def.ad_id = jnt_fic_attr.ad_id)));


--
-- Name: VIEW vw_fiche_def; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON VIEW vw_fiche_def IS 'all the attributs for	card family';


--
-- Name: vw_fiche_min; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_fiche_min AS
    SELECT attr_min.frd_id, attr_min.ad_id, attr_def.ad_text, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base FROM ((attr_min JOIN attr_def USING (ad_id)) JOIN fiche_def_ref USING (frd_id));


--
-- Name: vw_fiche_name; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_fiche_name AS
    SELECT fiche_detail.f_id, fiche_detail.ad_value AS name FROM fiche_detail WHERE (fiche_detail.ad_id = 1);


--
-- Name: vw_poste_qcode; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_poste_qcode AS
    SELECT c.f_id, a.ad_value AS j_poste, b.ad_value AS j_qcode FROM ((fiche c LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 5)) a USING (f_id)) LEFT JOIN (SELECT fiche_detail.f_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 23)) b USING (f_id));


--
-- Name: vw_supplier; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_supplier AS
    SELECT fiche.f_id, a1.ad_value AS name, a.ad_value AS quick_code, b.ad_value AS tva_num, c.ad_value AS poste_comptable, d.ad_value AS rue, e.ad_value AS code_postal, f.ad_value AS pays, g.ad_value AS telephone, h.ad_value AS email FROM (((((((((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 1)) a1 USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 13)) b USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 23)) a USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 5)) c USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 14)) d USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 15)) e USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 16)) f USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 17)) g USING (f_id)) LEFT JOIN (SELECT fiche_detail.jft_id, fiche_detail.f_id, fiche_detail.ad_id, fiche_detail.ad_value FROM fiche_detail WHERE (fiche_detail.ad_id = 18)) h USING (f_id)) WHERE (fiche_def_ref.frd_id = 8);


--
-- Name: ad_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY action_detail ALTER COLUMN ad_id SET DEFAULT nextval('action_detail_ad_id_seq'::regclass);


--
-- Name: del_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY del_action ALTER COLUMN del_id SET DEFAULT nextval('del_action_del_id_seq'::regclass);


--
-- Name: dj_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY del_jrn ALTER COLUMN dj_id SET DEFAULT nextval('del_jrn_dj_id_seq'::regclass);


--
-- Name: djx_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY del_jrnx ALTER COLUMN djx_id SET DEFAULT nextval('del_jrnx_djx_id_seq'::regclass);


--
-- Name: ex_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY extension ALTER COLUMN ex_id SET DEFAULT nextval('extension_ex_id_seq'::regclass);


--
-- Name: f_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY forecast ALTER COLUMN f_id SET DEFAULT nextval('forecast_f_id_seq'::regclass);


--
-- Name: fc_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY forecast_cat ALTER COLUMN fc_id SET DEFAULT nextval('forecast_cat_fc_id_seq'::regclass);


--
-- Name: fi_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY forecast_item ALTER COLUMN fi_id SET DEFAULT nextval('forecast_item_fi_id_seq'::regclass);


--
-- Name: jl_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY jnt_letter ALTER COLUMN jl_id SET DEFAULT nextval('jnt_letter_jl_id_seq'::regclass);


--
-- Name: ji_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_info ALTER COLUMN ji_id SET DEFAULT nextval('jrn_info_ji_id_seq'::regclass);


--
-- Name: n_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_note ALTER COLUMN n_id SET DEFAULT nextval('jrn_note_n_id_seq'::regclass);


--
-- Name: lc_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY letter_cred ALTER COLUMN lc_id SET DEFAULT nextval('letter_cred_lc_id_seq'::regclass);


--
-- Name: ld_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY letter_deb ALTER COLUMN ld_id SET DEFAULT nextval('letter_deb_ld_id_seq'::regclass);


--
-- Name: mp_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY mod_payment ALTER COLUMN mp_id SET DEFAULT nextval('mod_payment_mp_id_seq'::regclass);


--
-- Name: p_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY profile ALTER COLUMN p_id SET DEFAULT nextval('profile_p_id_seq'::regclass);


--
-- Name: pm_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY profile_menu ALTER COLUMN pm_id SET DEFAULT nextval('profile_menu_pm_id_seq'::regclass);


--
-- Name: pu_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY profile_user ALTER COLUMN pu_id SET DEFAULT nextval('profile_user_pu_id_seq'::regclass);


--
-- Name: qf_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY quant_fin ALTER COLUMN qf_id SET DEFAULT nextval('quant_fin_qf_id_seq'::regclass);


--
-- Name: use_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_sec_extension ALTER COLUMN use_id SET DEFAULT nextval('user_sec_extension_use_id_seq'::regclass);


--
-- Data for Name: action; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO action VALUES (800, 'Ajout de fiche', 'fiche', 'FICADD');
INSERT INTO action VALUES (805, 'Création, modification et effacement de fiche', 'fiche', 'FIC');
INSERT INTO action VALUES (910, 'création, modification et effacement de catégorie de fiche', 'fiche', 'FICCAT');
INSERT INTO action VALUES (1020, 'Effacer les documents du suivi', 'followup', 'RMDOC');
INSERT INTO action VALUES (1010, 'Voir les documents du suivi', 'followup', 'VIEWDOC');
INSERT INTO action VALUES (1050, 'Modifier le type de document', 'followup', 'PARCATDOC');


--
-- Data for Name: action_detail; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: action_gestion; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: attr_def; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO attr_def VALUES (20, 'Partie fiscalement non déductible', 'numeric', '22');
INSERT INTO attr_def VALUES (10, 'Date début', 'date', '8');
INSERT INTO attr_def VALUES (6, 'Prix vente', 'numeric', '6');
INSERT INTO attr_def VALUES (7, 'Prix achat', 'numeric', '6');
INSERT INTO attr_def VALUES (8, 'Durée Amortissement', 'numeric', '6');
INSERT INTO attr_def VALUES (11, 'Montant initial', 'numeric', '6');
INSERT INTO attr_def VALUES (21, 'TVA non déductible', 'numeric', '6');
INSERT INTO attr_def VALUES (22, 'TVA non déductible récupérable par l''impôt', 'numeric', '6');
INSERT INTO attr_def VALUES (1, 'Nom', 'text', '22');
INSERT INTO attr_def VALUES (2, 'Taux TVA', 'text', '22');
INSERT INTO attr_def VALUES (3, 'Numéro de compte', 'text', '22');
INSERT INTO attr_def VALUES (4, 'Nom de la banque', 'text', '22');
INSERT INTO attr_def VALUES (5, 'Poste Comptable', 'text', '22');
INSERT INTO attr_def VALUES (9, 'Description', 'text', '22');
INSERT INTO attr_def VALUES (12, 'Personne de contact ', 'text', '22');
INSERT INTO attr_def VALUES (13, 'numéro de tva ', 'text', '22');
INSERT INTO attr_def VALUES (14, 'Adresse ', 'text', '22');
INSERT INTO attr_def VALUES (16, 'pays ', 'text', '22');
INSERT INTO attr_def VALUES (17, 'téléphone ', 'text', '22');
INSERT INTO attr_def VALUES (18, 'email ', 'text', '22');
INSERT INTO attr_def VALUES (19, 'Gestion stock', 'text', '22');
INSERT INTO attr_def VALUES (23, 'Quick Code', 'text', '22');
INSERT INTO attr_def VALUES (24, 'Ville', 'text', '22');
INSERT INTO attr_def VALUES (25, 'Société', 'text', '22');
INSERT INTO attr_def VALUES (26, 'Fax', 'text', '22');
INSERT INTO attr_def VALUES (27, 'GSM', 'text', '22');
INSERT INTO attr_def VALUES (15, 'code postal', 'text', '22');
INSERT INTO attr_def VALUES (30, 'Numero de client', 'text', '22');
INSERT INTO attr_def VALUES (32, 'Prénom', 'text', '22');
INSERT INTO attr_def VALUES (31, 'Dépense  charge du grant (partie privé) ', 'text', '22');
INSERT INTO attr_def VALUES (50, 'Contrepartie pour TVA récup par impot', 'poste', '22');
INSERT INTO attr_def VALUES (51, 'Contrepartie pour TVA non Ded.', 'poste', '22');
INSERT INTO attr_def VALUES (52, 'Contrepartie pour dépense à charge du gérant', 'poste', '22');
INSERT INTO attr_def VALUES (53, 'Contrepartie pour dépense fiscal. non déd.', 'poste', '22');


--
-- Data for Name: attr_min; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO attr_min VALUES (1, 1);
INSERT INTO attr_min VALUES (1, 2);
INSERT INTO attr_min VALUES (2, 1);
INSERT INTO attr_min VALUES (2, 2);
INSERT INTO attr_min VALUES (3, 1);
INSERT INTO attr_min VALUES (3, 2);
INSERT INTO attr_min VALUES (4, 1);
INSERT INTO attr_min VALUES (4, 3);
INSERT INTO attr_min VALUES (4, 4);
INSERT INTO attr_min VALUES (4, 12);
INSERT INTO attr_min VALUES (4, 13);
INSERT INTO attr_min VALUES (4, 14);
INSERT INTO attr_min VALUES (4, 15);
INSERT INTO attr_min VALUES (4, 16);
INSERT INTO attr_min VALUES (4, 17);
INSERT INTO attr_min VALUES (4, 18);
INSERT INTO attr_min VALUES (8, 1);
INSERT INTO attr_min VALUES (8, 12);
INSERT INTO attr_min VALUES (8, 13);
INSERT INTO attr_min VALUES (8, 14);
INSERT INTO attr_min VALUES (8, 15);
INSERT INTO attr_min VALUES (8, 16);
INSERT INTO attr_min VALUES (8, 17);
INSERT INTO attr_min VALUES (8, 18);
INSERT INTO attr_min VALUES (9, 1);
INSERT INTO attr_min VALUES (9, 12);
INSERT INTO attr_min VALUES (9, 13);
INSERT INTO attr_min VALUES (9, 14);
INSERT INTO attr_min VALUES (9, 16);
INSERT INTO attr_min VALUES (9, 17);
INSERT INTO attr_min VALUES (9, 18);
INSERT INTO attr_min VALUES (1, 6);
INSERT INTO attr_min VALUES (1, 7);
INSERT INTO attr_min VALUES (2, 6);
INSERT INTO attr_min VALUES (2, 7);
INSERT INTO attr_min VALUES (3, 7);
INSERT INTO attr_min VALUES (1, 19);
INSERT INTO attr_min VALUES (2, 19);
INSERT INTO attr_min VALUES (14, 1);
INSERT INTO attr_min VALUES (5, 1);
INSERT INTO attr_min VALUES (5, 4);
INSERT INTO attr_min VALUES (5, 10);
INSERT INTO attr_min VALUES (5, 12);
INSERT INTO attr_min VALUES (6, 1);
INSERT INTO attr_min VALUES (6, 4);
INSERT INTO attr_min VALUES (6, 10);
INSERT INTO attr_min VALUES (6, 12);
INSERT INTO attr_min VALUES (10, 1);
INSERT INTO attr_min VALUES (10, 12);
INSERT INTO attr_min VALUES (11, 1);
INSERT INTO attr_min VALUES (11, 12);
INSERT INTO attr_min VALUES (12, 1);
INSERT INTO attr_min VALUES (12, 12);
INSERT INTO attr_min VALUES (13, 1);
INSERT INTO attr_min VALUES (13, 9);
INSERT INTO attr_min VALUES (7, 1);
INSERT INTO attr_min VALUES (7, 8);
INSERT INTO attr_min VALUES (7, 9);
INSERT INTO attr_min VALUES (7, 10);
INSERT INTO attr_min VALUES (5, 11);
INSERT INTO attr_min VALUES (6, 11);
INSERT INTO attr_min VALUES (1, 15);
INSERT INTO attr_min VALUES (9, 15);
INSERT INTO attr_min VALUES (15, 1);
INSERT INTO attr_min VALUES (15, 9);
INSERT INTO attr_min VALUES (1, 23);
INSERT INTO attr_min VALUES (2, 23);
INSERT INTO attr_min VALUES (3, 23);
INSERT INTO attr_min VALUES (4, 23);
INSERT INTO attr_min VALUES (5, 23);
INSERT INTO attr_min VALUES (6, 23);
INSERT INTO attr_min VALUES (8, 23);
INSERT INTO attr_min VALUES (9, 23);
INSERT INTO attr_min VALUES (10, 23);
INSERT INTO attr_min VALUES (11, 23);
INSERT INTO attr_min VALUES (12, 23);
INSERT INTO attr_min VALUES (13, 23);
INSERT INTO attr_min VALUES (14, 23);
INSERT INTO attr_min VALUES (15, 23);
INSERT INTO attr_min VALUES (7, 23);
INSERT INTO attr_min VALUES (9, 24);
INSERT INTO attr_min VALUES (8, 24);
INSERT INTO attr_min VALUES (14, 24);
INSERT INTO attr_min VALUES (16, 1);
INSERT INTO attr_min VALUES (16, 17);
INSERT INTO attr_min VALUES (16, 18);
INSERT INTO attr_min VALUES (16, 25);
INSERT INTO attr_min VALUES (16, 26);
INSERT INTO attr_min VALUES (16, 27);
INSERT INTO attr_min VALUES (16, 23);
INSERT INTO attr_min VALUES (25, 1);
INSERT INTO attr_min VALUES (25, 4);
INSERT INTO attr_min VALUES (25, 3);
INSERT INTO attr_min VALUES (25, 5);
INSERT INTO attr_min VALUES (25, 15);
INSERT INTO attr_min VALUES (25, 16);
INSERT INTO attr_min VALUES (25, 24);
INSERT INTO attr_min VALUES (25, 23);
INSERT INTO attr_min VALUES (2, 30);


--
-- Data for Name: bilan; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO bilan VALUES (1, 'Bilan Belge complet', 'document/fr_be/bnb.rtf', 'document/fr_be/bnb.form', 'RTF');


--
-- Data for Name: centralized; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: del_action; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: del_jrn; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: del_jrnx; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: document; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: document_modele; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: document_state; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO document_state VALUES (1, 'Clôturé');
INSERT INTO document_state VALUES (2, 'A suivre');
INSERT INTO document_state VALUES (3, 'A faire');
INSERT INTO document_state VALUES (4, 'Abandonné');


--
-- Data for Name: document_type; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO document_type VALUES (1, 'Document Interne');
INSERT INTO document_type VALUES (2, 'Bons de commande client');
INSERT INTO document_type VALUES (3, 'Bon de commande Fournisseur');
INSERT INTO document_type VALUES (4, 'Facture');
INSERT INTO document_type VALUES (5, 'Lettre de rappel');
INSERT INTO document_type VALUES (6, 'Courrier');
INSERT INTO document_type VALUES (7, 'Proposition');
INSERT INTO document_type VALUES (8, 'Email');
INSERT INTO document_type VALUES (9, 'Divers');
INSERT INTO document_type VALUES (10, 'Note de frais');
INSERT INTO document_type VALUES (20, 'Réception commande Fournisseur');
INSERT INTO document_type VALUES (21, 'Réception commande Client');
INSERT INTO document_type VALUES (22, 'Réception magazine');


--
-- Data for Name: extension; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO extension VALUES (1, 'Module de TVA', 'TVA', 'Cette extension permet de faire les listings et declarations TVA', 'tva/index.php', 'Y');


--
-- Data for Name: fiche; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: fiche_def; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO fiche_def VALUES (2, '400', 'Client', true, 9);
INSERT INTO fiche_def VALUES (1, '604', 'Marchandises', true, 2);
INSERT INTO fiche_def VALUES (3, '5500', 'Banque', true, 4);
INSERT INTO fiche_def VALUES (4, '440', 'Fournisseur', true, 8);
INSERT INTO fiche_def VALUES (5, '61', 'S & B D', true, 3);
INSERT INTO fiche_def VALUES (6, '700', 'Vente', true, 1);


--
-- Data for Name: fiche_def_ref; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO fiche_def_ref VALUES (1, 'Vente Service', '700');
INSERT INTO fiche_def_ref VALUES (2, 'Achat Marchandises', '604');
INSERT INTO fiche_def_ref VALUES (3, 'Achat Service et biens divers', '61');
INSERT INTO fiche_def_ref VALUES (4, 'Banque', '5500');
INSERT INTO fiche_def_ref VALUES (5, 'Prêt > a un an', '17');
INSERT INTO fiche_def_ref VALUES (6, 'Prêt < a un an', '430');
INSERT INTO fiche_def_ref VALUES (8, 'Fournisseurs', '440');
INSERT INTO fiche_def_ref VALUES (9, 'Clients', '400');
INSERT INTO fiche_def_ref VALUES (10, 'Salaire Administrateur', '6200');
INSERT INTO fiche_def_ref VALUES (11, 'Salaire Ouvrier', '6203');
INSERT INTO fiche_def_ref VALUES (12, 'Salaire Employé', '6202');
INSERT INTO fiche_def_ref VALUES (13, 'Dépenses non admises', '674');
INSERT INTO fiche_def_ref VALUES (14, 'Administration des Finances', NULL);
INSERT INTO fiche_def_ref VALUES (15, 'Autres fiches', NULL);
INSERT INTO fiche_def_ref VALUES (7, 'Matériel à amortir', '2400');
INSERT INTO fiche_def_ref VALUES (16, 'Contact', NULL);
INSERT INTO fiche_def_ref VALUES (25, 'Compte Salarié / Administrateur', NULL);


--
-- Data for Name: fiche_detail; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: forecast; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: forecast_cat; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: forecast_item; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: form; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO form VALUES (3000398, 3000000, 1, 'Prestation [ case 03 ]', '[700%]-[7000005]');
INSERT INTO form VALUES (3000399, 3000000, 2, 'Prestation intra [ case 47 ]', '[7000005]');
INSERT INTO form VALUES (3000400, 3000000, 3, 'Tva due   [case 54]', '[4513]+[4512]+[4511] FROM=01.2005');
INSERT INTO form VALUES (3000401, 3000000, 4, 'Marchandises, matière première et auxiliaire [case 81 ]', '[60%]');
INSERT INTO form VALUES (3000402, 3000000, 7, 'Service et bien divers [case 82]', '[61%]');
INSERT INTO form VALUES (3000403, 3000000, 8, 'bien d''invest [ case 83 ]', '[2400%]');
INSERT INTO form VALUES (3000404, 3000000, 9, 'TVA déductible [ case 59 ]', 'abs([4117]-[411%])');
INSERT INTO form VALUES (3000405, 3000000, 8, 'TVA non ded -> voiture', '[610022]*0.21/2');
INSERT INTO form VALUES (3000406, 3000000, 9, 'Acompte TVA', '[4117]');


--
-- Data for Name: formdef; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO formdef VALUES (3000000, 'TVA déclaration Belge');


--
-- Data for Name: groupe_analytique; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: info_def; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO info_def VALUES ('BON_COMMANDE', 'Numero de bon de commande');
INSERT INTO info_def VALUES ('OTHER', 'Info diverses');


--
-- Data for Name: jnt_fic_attr; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO jnt_fic_attr VALUES (3, 1, 16, 0);
INSERT INTO jnt_fic_attr VALUES (4, 1, 27, 0);
INSERT INTO jnt_fic_attr VALUES (5, 1, 36, 0);
INSERT INTO jnt_fic_attr VALUES (6, 1, 40, 0);
INSERT INTO jnt_fic_attr VALUES (3, 4, 18, 2);
INSERT INTO jnt_fic_attr VALUES (3, 12, 19, 3);
INSERT INTO jnt_fic_attr VALUES (6, 19, 44, 2);
INSERT INTO jnt_fic_attr VALUES (2, 13, 9, 31);
INSERT INTO jnt_fic_attr VALUES (3, 13, 20, 31);
INSERT INTO jnt_fic_attr VALUES (4, 13, 29, 31);
INSERT INTO jnt_fic_attr VALUES (1, 2, 3, 1);
INSERT INTO jnt_fic_attr VALUES (2, 12, 8, 1);
INSERT INTO jnt_fic_attr VALUES (3, 3, 17, 1);
INSERT INTO jnt_fic_attr VALUES (4, 12, 28, 1);
INSERT INTO jnt_fic_attr VALUES (5, 2, 37, 1);
INSERT INTO jnt_fic_attr VALUES (6, 2, 41, 1);
INSERT INTO jnt_fic_attr VALUES (1, 6, 4, 120);
INSERT INTO jnt_fic_attr VALUES (6, 6, 42, 120);
INSERT INTO jnt_fic_attr VALUES (1, 7, 5, 130);
INSERT INTO jnt_fic_attr VALUES (5, 7, 38, 130);
INSERT INTO jnt_fic_attr VALUES (6, 7, 43, 130);
INSERT INTO jnt_fic_attr VALUES (2, 14, 10, 40);
INSERT INTO jnt_fic_attr VALUES (3, 14, 21, 40);
INSERT INTO jnt_fic_attr VALUES (4, 14, 30, 40);
INSERT INTO jnt_fic_attr VALUES (2, 16, 12, 70);
INSERT INTO jnt_fic_attr VALUES (3, 16, 23, 70);
INSERT INTO jnt_fic_attr VALUES (4, 16, 32, 70);
INSERT INTO jnt_fic_attr VALUES (2, 17, 13, 80);
INSERT INTO jnt_fic_attr VALUES (3, 17, 24, 80);
INSERT INTO jnt_fic_attr VALUES (4, 17, 33, 80);
INSERT INTO jnt_fic_attr VALUES (2, 18, 14, 90);
INSERT INTO jnt_fic_attr VALUES (3, 18, 25, 90);
INSERT INTO jnt_fic_attr VALUES (4, 18, 34, 90);
INSERT INTO jnt_fic_attr VALUES (2, 23, 45, 400);
INSERT INTO jnt_fic_attr VALUES (1, 23, 46, 400);
INSERT INTO jnt_fic_attr VALUES (3, 23, 47, 400);
INSERT INTO jnt_fic_attr VALUES (4, 23, 48, 400);
INSERT INTO jnt_fic_attr VALUES (5, 23, 49, 400);
INSERT INTO jnt_fic_attr VALUES (6, 23, 50, 400);
INSERT INTO jnt_fic_attr VALUES (2, 24, 51, 60);
INSERT INTO jnt_fic_attr VALUES (4, 24, 52, 60);
INSERT INTO jnt_fic_attr VALUES (2, 15, 11, 50);
INSERT INTO jnt_fic_attr VALUES (3, 15, 22, 50);
INSERT INTO jnt_fic_attr VALUES (4, 15, 31, 50);
INSERT INTO jnt_fic_attr VALUES (1, 5, 1, 30);
INSERT INTO jnt_fic_attr VALUES (2, 5, 6, 30);
INSERT INTO jnt_fic_attr VALUES (3, 5, 15, 30);
INSERT INTO jnt_fic_attr VALUES (4, 5, 26, 30);
INSERT INTO jnt_fic_attr VALUES (5, 5, 35, 30);
INSERT INTO jnt_fic_attr VALUES (6, 5, 39, 30);
INSERT INTO jnt_fic_attr VALUES (1, 1, 2, 0);
INSERT INTO jnt_fic_attr VALUES (2, 1, 7, 0);


--
-- Data for Name: jnt_letter; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: jrn; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: jrn_action; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO jrn_action VALUES (2, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'VEN');
INSERT INTO jrn_action VALUES (4, 'Voir Impayés', 'Voir toutes les factures non payées', 'user_jrn.php', 'action=voir_jrn_non_paye', 'FR', 'VEN');
INSERT INTO jrn_action VALUES (1, 'Nouvelle', 'Création d''une facture', 'user_jrn.php', 'action=insert_vente&blank', 'FR', 'VEN');
INSERT INTO jrn_action VALUES (10, 'Nouveau', 'Encode un nouvel achat (matériel, marchandises, services et biens divers)', 'user_jrn.php', 'action=new&blank', 'FR', 'ACH');
INSERT INTO jrn_action VALUES (12, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'ACH');
INSERT INTO jrn_action VALUES (14, 'Voir Impayés', 'Voir toutes les factures non payées', 'user_jrn.php', 'action=voir_jrn_non_paye', 'FR', 'ACH');
INSERT INTO jrn_action VALUES (20, 'Nouveau', 'Encode un nouvel achat (matériel, marchandises, services et biens divers)', 'user_jrn.php', 'action=new&blank', 'FR', 'FIN');
INSERT INTO jrn_action VALUES (22, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'FIN');
INSERT INTO jrn_action VALUES (30, 'Nouveau', NULL, 'user_jrn.php', 'action=new&blank', 'FR', 'ODS');
INSERT INTO jrn_action VALUES (32, 'Voir', 'Voir toutes les factures', 'user_jrn.php', 'action=voir_jrn', 'FR', 'ODS');
INSERT INTO jrn_action VALUES (40, 'Soldes', 'Voir les soldes des comptes en banques', 'user_jrn.php', 'action=solde', 'FR', 'FIN');


--
-- Data for Name: jrn_def; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO jrn_def VALUES (4, 'Opération Diverses', NULL, NULL, NULL, NULL, 5, 5, false, NULL, 'ODS', 'O01', 'ODS', NULL, NULL);
INSERT INTO jrn_def VALUES (1, 'Financier', '5* ', '5*', '3,2,4', '3,2,4', 5, 5, false, NULL, 'FIN', 'F01', 'FIN', NULL, NULL);
INSERT INTO jrn_def VALUES (3, 'Achat', '6*', '4*', '5', '4', 1, 3, true, 'échéance', 'ACH', 'A01', 'ACH', NULL, NULL);
INSERT INTO jrn_def VALUES (2, 'Vente', '4*', '7*', '2', '6', 2, 1, true, 'échéance', 'VEN', 'V01', 'VEN', NULL, NULL);


--
-- Data for Name: jrn_info; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: jrn_note; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: jrn_periode; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO jrn_periode VALUES (4, 79, 'OP');
INSERT INTO jrn_periode VALUES (1, 79, 'OP');
INSERT INTO jrn_periode VALUES (3, 79, 'OP');
INSERT INTO jrn_periode VALUES (2, 79, 'OP');
INSERT INTO jrn_periode VALUES (4, 80, 'OP');
INSERT INTO jrn_periode VALUES (1, 80, 'OP');
INSERT INTO jrn_periode VALUES (3, 80, 'OP');
INSERT INTO jrn_periode VALUES (2, 80, 'OP');
INSERT INTO jrn_periode VALUES (4, 81, 'OP');
INSERT INTO jrn_periode VALUES (1, 81, 'OP');
INSERT INTO jrn_periode VALUES (3, 81, 'OP');
INSERT INTO jrn_periode VALUES (2, 81, 'OP');
INSERT INTO jrn_periode VALUES (4, 82, 'OP');
INSERT INTO jrn_periode VALUES (1, 82, 'OP');
INSERT INTO jrn_periode VALUES (3, 82, 'OP');
INSERT INTO jrn_periode VALUES (2, 82, 'OP');
INSERT INTO jrn_periode VALUES (4, 83, 'OP');
INSERT INTO jrn_periode VALUES (1, 83, 'OP');
INSERT INTO jrn_periode VALUES (3, 83, 'OP');
INSERT INTO jrn_periode VALUES (2, 83, 'OP');
INSERT INTO jrn_periode VALUES (4, 84, 'OP');
INSERT INTO jrn_periode VALUES (1, 84, 'OP');
INSERT INTO jrn_periode VALUES (3, 84, 'OP');
INSERT INTO jrn_periode VALUES (2, 84, 'OP');
INSERT INTO jrn_periode VALUES (4, 85, 'OP');
INSERT INTO jrn_periode VALUES (1, 85, 'OP');
INSERT INTO jrn_periode VALUES (3, 85, 'OP');
INSERT INTO jrn_periode VALUES (2, 85, 'OP');
INSERT INTO jrn_periode VALUES (4, 86, 'OP');
INSERT INTO jrn_periode VALUES (1, 86, 'OP');
INSERT INTO jrn_periode VALUES (3, 86, 'OP');
INSERT INTO jrn_periode VALUES (2, 86, 'OP');
INSERT INTO jrn_periode VALUES (4, 87, 'OP');
INSERT INTO jrn_periode VALUES (1, 87, 'OP');
INSERT INTO jrn_periode VALUES (3, 87, 'OP');
INSERT INTO jrn_periode VALUES (2, 87, 'OP');
INSERT INTO jrn_periode VALUES (4, 88, 'OP');
INSERT INTO jrn_periode VALUES (1, 88, 'OP');
INSERT INTO jrn_periode VALUES (3, 88, 'OP');
INSERT INTO jrn_periode VALUES (2, 88, 'OP');
INSERT INTO jrn_periode VALUES (4, 89, 'OP');
INSERT INTO jrn_periode VALUES (1, 89, 'OP');
INSERT INTO jrn_periode VALUES (3, 89, 'OP');
INSERT INTO jrn_periode VALUES (2, 89, 'OP');
INSERT INTO jrn_periode VALUES (4, 90, 'OP');
INSERT INTO jrn_periode VALUES (1, 90, 'OP');
INSERT INTO jrn_periode VALUES (3, 90, 'OP');
INSERT INTO jrn_periode VALUES (2, 90, 'OP');
INSERT INTO jrn_periode VALUES (4, 91, 'OP');
INSERT INTO jrn_periode VALUES (1, 91, 'OP');
INSERT INTO jrn_periode VALUES (3, 91, 'OP');
INSERT INTO jrn_periode VALUES (2, 91, 'OP');


--
-- Data for Name: jrn_rapt; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: jrn_type; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO jrn_type VALUES ('FIN', 'Financier');
INSERT INTO jrn_type VALUES ('VEN', 'Vente');
INSERT INTO jrn_type VALUES ('ACH', 'Achat');
INSERT INTO jrn_type VALUES ('ODS', 'Opérations Diverses');


--
-- Data for Name: jrnx; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: letter_cred; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: letter_deb; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: menu_ref; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO menu_ref VALUES ('ACH', 'Achat', 'compta_ach.inc.php', NULL, 'Nouvel achat ou dépense', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCHOP', 'Historique', 'anc_history.inc.php', NULL, 'Historique des imputations analytiques', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCGL', 'Grand''Livre', 'anc_great_ledger.inc.php', NULL, 'Grand livre d''plan analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCBS', 'Balance simple', 'anc_balance_simple.inc.php', NULL, 'Balance simple des imputations analytiques', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCBC2', 'Balance croisée double', 'anc_balance_double.inc.php', NULL, 'Balance double croisées des imputations analytiques', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCTAB', 'Tableau', 'anc_acc_table.inc.php', NULL, 'Tableau lié à la comptabilité', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCBCC', 'Balance Analytique/comptabilité', 'anc_acc_balance.inc.php', NULL, 'Lien entre comptabilité et Comptabilité analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCGR', 'Groupe', 'anc_group_balance.inc.php', NULL, 'Balance par groupe', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CSV:AncGrandLivre', 'Impression Grand-Livre', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncBalGroup', 'Export Balance groupe analytique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('OTH:Bilan', 'Export Bilan', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:ledger', 'Export Journaux', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:postedetail', 'Export Poste détail', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:postedetail', 'Export Poste détail', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:fichedetail', 'Export Fiche détail', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('SEARCH', 'Recherche', NULL, NULL, 'Recherche', NULL, 'popup_recherche()', 'ME');
INSERT INTO menu_ref VALUES ('DIVPARM', 'Divers', NULL, NULL, 'Paramètres divers', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGTVA', 'TVA', 'tva.inc.php', NULL, 'Config. de la tva', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CARD', 'Fiche', 'fiche.inc.php', NULL, 'Fiche', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('STOCK', 'Stock', 'stock.inc.php', NULL, 'Stock', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('MOD', 'Menu et profile', NULL, NULL, 'Menu ', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGPRO', 'Profile', 'profile.inc.php', NULL, 'Configuration profile', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGPAY', 'Moyen de paiement', 'payment_middle.inc.php', NULL, 'Config. des méthodes de paiement', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGACC', 'Poste', 'poste.inc.php', NULL, 'Config. poste comptable de base', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('VEN', 'Vente', 'compta_ven.inc.php', NULL, 'Nouvelle vente ou recette', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGMENU', 'Config. Menu', 'menu.inc.php', NULL, 'Configuration des menus et plugins', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('COMPANY', 'Sociétés', 'company.inc.php', NULL, 'Parametre societe', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PERIODE', 'Période', 'periode.inc.php', NULL, 'Gestion des périodes', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PDF:fichedetail', 'Export Fiche détail', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:fiche_balance', 'Export Fiche balance', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:fiche_balance', 'Export Fiche balance', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:report', 'Export report', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:report', 'Export report', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:fiche', 'Export Fiche', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:fiche', 'Export Fiche', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:glcompte', 'Export Grand Livre', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:glcompte', 'Export Grand Livre', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:sec', 'Export Sécurité', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncList', 'Export Comptabilité analytique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncBalSimple', 'Export Comptabilité analytique balance simple', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:AncBalSimple', 'Export Comptabilité analytique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncBalDouble', 'Export Comptabilité analytique balance double', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:AncBalDouble', 'Export Comptabilité analytique balance double', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:balance', 'Export Balance comptable', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:balance', 'Export Balance comptable', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:histo', 'Export Historique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:ledger', 'Export Journaux', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncTable', 'Export Tableau Analytique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncAccList', 'Export Historique Compt. Analytique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('SUPPL', 'Fournisseur', 'supplier.inc.php', NULL, 'Suivi fournisseur', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('LET', 'Lettrage', NULL, NULL, 'Lettrage', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCODS', 'Opérations diverses', 'anc_od.inc.php', NULL, 'OD analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('VERIFBIL', 'Vérification ', 'verif_bilan.inc.php', NULL, 'Vérification de la comptabilité', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('REPORT', 'Création de rapport', 'report.inc.php', NULL, 'Création de rapport', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('OPEN', 'Ecriture Ouverture', 'opening.inc.php', NULL, 'Ecriture d''ouverture', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ACHIMP', 'Historique achat', 'history_operation.inc.php', NULL, 'Historique achat', 'ledger_type=ACH', NULL, 'ME');
INSERT INTO menu_ref VALUES ('FOLLOW', 'Courrier', 'action.inc.php', NULL, 'Suivi, courrier, devis', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('FORECAST', 'Prévision', 'forecast.inc.php', NULL, 'Prévision', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('EXT', 'Extension', 'extension_choice.inc.php', NULL, 'Extensions (plugins)', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGDOC', 'Document', 'document_modele.inc.php', NULL, 'Config. modèle de document', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGLED', 'journaux', 'cfgledger.inc.php', NULL, 'Configuration des journaux', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PREDOP', 'Ecriture prédefinie', 'preod.inc.php', NULL, 'Gestion des opérations prédéfinifies', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ADV', 'Avancé', NULL, NULL, 'Menu avancé', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANC', 'Compta Analytique', NULL, NULL, 'Module comptabilité analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGSEC', 'Sécurité', 'param_sec.inc.php', NULL, 'configuration de la sécurité', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PLANANC', 'Plan Compt. analytique', 'anc_pa.inc.php', NULL, 'Plan analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCGROUP', 'Groupe', 'anc_group.inc.php', NULL, 'Groupe analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ODSIMP', 'Historique opérations diverses', 'history_operation.inc.php', NULL, 'Historique opérations diverses', 'ledger_type=ODS', NULL, 'ME');
INSERT INTO menu_ref VALUES ('VENMENU', 'Vente / Recette', NULL, NULL, 'Menu ventes et recettes', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PREFERENCE', 'Préférence', 'pref.inc.php', NULL, 'Préférence', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('HIST', 'Historique', 'history_operation.inc.php', NULL, 'Historique', 'ledger_type=ALL', NULL, 'ME');
INSERT INTO menu_ref VALUES ('MENUFIN', 'Financier', NULL, NULL, 'Menu Financier', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('FIMP', 'Historique financier', 'history_operation.inc.php', NULL, 'Historique financier', 'ledger_type=FIN', NULL, 'ME');
INSERT INTO menu_ref VALUES ('MENUACH', 'Achat', NULL, NULL, 'Menu achat', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('MENUODS', 'Opérations diverses', NULL, NULL, 'Menu opérations diverses', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ODS', 'Opérations Diverses', 'compta_ods.inc.php', NULL, 'Nouvelle opérations diverses', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('FREC', 'Rapprochement', 'compta_fin_rec.inc.php', NULL, 'Rapprochement bancaire', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ADM', 'Administration', 'adm.inc.php', NULL, 'Suivi administration, banque', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('FIN', 'Nouvel extrait', 'compta_fin.inc.php', NULL, 'Nouvel extrait bancaire', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGATCARD', 'Attribut de fiche', 'card_attr.inc.php', NULL, 'Gestion des modèles de fiches', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('FSALDO', 'Soldes', 'compta_fin_saldo.inc.php', NULL, 'Solde des comptes en banques, caisse...', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('JSSEARCH', 'Recherche', NULL, NULL, 'Recherche', NULL, 'search_reconcile()', 'ME');
INSERT INTO menu_ref VALUES ('LETACC', 'Lettrage par Poste', 'lettering.account.inc.php', NULL, 'lettrage par poste comptable', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CARDBAL', 'Balance', 'balance_card.inc.php', NULL, 'Balance par catégorie de fiche', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CUST', 'Client', 'client.inc.php', NULL, 'Suivi client', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGCARDCAT', 'Catégorie de fiche', 'fiche_def.inc.php', NULL, 'Gestion catégorie de fiche', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGCATDOC', 'Catégorie de documents', 'cat_document.inc.php', NULL, 'Config. catégorie de documents', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('VENIMP', 'Historique vente', 'history_operation.inc.php', NULL, 'Historique des ventes', 'ledger_type=VEN', NULL, 'ME');
INSERT INTO menu_ref VALUES ('LETCARD', 'Lettrage par Fiche', 'lettering.card.inc.php', NULL, 'Lettrage par fiche', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGPCMN', 'Plan Comptable', 'param_pcmn.inc.php', NULL, 'Config. du plan comptable', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('LOGOUT', 'Sortie', NULL, 'logout.php', 'Sortie', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('DASHBOARD', 'Tableau de bord', 'dashboard.inc.php', NULL, 'Tableau de bord', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('COMPTA', 'Comptabilité', NULL, NULL, 'Module comptabilité', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('GESTION', 'Gestion', NULL, NULL, 'Module gestion', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PARAM', 'Paramètre', NULL, NULL, 'Module paramètre', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTJRN', 'Historique', 'impress_jrn.inc.php', NULL, 'Impression historique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTREC', 'Rapprochement', 'impress_rec.inc.php', NULL, 'Impression des rapprochements', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTPOSTE', 'Poste', 'impress_poste.inc.php', NULL, 'Impression du détail d''un poste comptable', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTREPORT', 'Rapport', 'impress_rapport.inc.php', NULL, 'Impression de rapport', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTBILAN', 'Bilan', 'impress_bilan.inc.php', NULL, 'Impression de bilan', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTGL', 'Grand Livre', 'impress_gl_comptes.inc.php', NULL, 'Impression du grand livre', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTBAL', 'Balance', 'balance.inc.php', NULL, 'Impression des balances comptables', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTCARD', 'Catégorie de Fiches', 'impress_fiche.inc.php', NULL, 'Impression catégorie de fiches', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINT', 'Impression', NULL, NULL, 'Menu impression', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ACCESS', 'Accueil', NULL, 'user_login.php', 'Accueil', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCIMP', 'Impression', NULL, NULL, 'Impression compta. analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('new_line', 'saut de ligne', NULL, NULL, 'Saut de ligne', NULL, NULL, 'SP');
INSERT INTO menu_ref VALUES ('TVA', 'Module de TVA', 'tva/index.php', NULL, 'Cette extension permet de faire les listings et declarations TVA', 'plugin_code=TVA', NULL, 'PL');


--
-- Data for Name: mod_payment; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO mod_payment VALUES (2, 'Caisse', 1, NULL, NULL, 2);
INSERT INTO mod_payment VALUES (1, 'Paiement électronique', 1, NULL, NULL, 2);
INSERT INTO mod_payment VALUES (4, 'Caisse', 1, NULL, NULL, 3);
INSERT INTO mod_payment VALUES (3, 'Par gérant ou administrateur', 2, NULL, NULL, 3);


--
-- Data for Name: op_predef; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: op_predef_detail; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: operation_analytique; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: parameter; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO parameter VALUES ('MY_NAME', NULL);
INSERT INTO parameter VALUES ('MY_CP', NULL);
INSERT INTO parameter VALUES ('MY_COMMUNE', NULL);
INSERT INTO parameter VALUES ('MY_TVA', NULL);
INSERT INTO parameter VALUES ('MY_STREET', NULL);
INSERT INTO parameter VALUES ('MY_NUMBER', NULL);
INSERT INTO parameter VALUES ('MY_TEL', NULL);
INSERT INTO parameter VALUES ('MY_PAYS', NULL);
INSERT INTO parameter VALUES ('MY_FAX', NULL);
INSERT INTO parameter VALUES ('MY_ANALYTIC', 'nu');
INSERT INTO parameter VALUES ('MY_COUNTRY', 'BE');
INSERT INTO parameter VALUES ('MY_STRICT', 'Y');
INSERT INTO parameter VALUES ('MY_TVA_USE', 'Y');
INSERT INTO parameter VALUES ('MY_PJ_SUGGEST', 'Y');
INSERT INTO parameter VALUES ('MY_DATE_SUGGEST', 'Y');
INSERT INTO parameter VALUES ('MY_ALPHANUM', 'N');
INSERT INTO parameter VALUES ('MY_CHECK_PERIODE', 'N');


--
-- Data for Name: parm_code; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO parm_code VALUES ('DNA', '6740', 'Dépense non déductible');
INSERT INTO parm_code VALUES ('CUSTOMER', '400', 'Poste comptable de base pour les clients');
INSERT INTO parm_code VALUES ('COMPTE_TVA', '451', 'TVA à payer');
INSERT INTO parm_code VALUES ('BANQUE', '550', 'Poste comptable de base pour les banques');
INSERT INTO parm_code VALUES ('VIREMENT_INTERNE', '58', 'Poste Comptable pour les virements internes');
INSERT INTO parm_code VALUES ('COMPTE_COURANT', '56', 'Poste comptable pour le compte courant');
INSERT INTO parm_code VALUES ('CAISSE', '57', 'Poste comptable pour la caisse');
INSERT INTO parm_code VALUES ('TVA_DNA', '6740', 'Tva non déductible s');
INSERT INTO parm_code VALUES ('TVA_DED_IMPOT', '619000', 'Tva déductible par l''impôt');
INSERT INTO parm_code VALUES ('VENTE', '70', 'Poste comptable de base pour les ventes');
INSERT INTO parm_code VALUES ('DEP_PRIV', '4890', 'Depense a charge du gerant');
INSERT INTO parm_code VALUES ('SUPPLIER', '440', 'Poste par défaut pour les fournisseurs');


--
-- Data for Name: parm_money; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO parm_money VALUES (1, 'EUR', 1.0000);


--
-- Data for Name: parm_periode; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO parm_periode VALUES (79, '2010-01-01', '2010-01-31', '2010', false, false);
INSERT INTO parm_periode VALUES (80, '2010-02-01', '2010-02-28', '2010', false, false);
INSERT INTO parm_periode VALUES (81, '2010-03-01', '2010-03-31', '2010', false, false);
INSERT INTO parm_periode VALUES (82, '2010-04-01', '2010-04-30', '2010', false, false);
INSERT INTO parm_periode VALUES (83, '2010-05-01', '2010-05-31', '2010', false, false);
INSERT INTO parm_periode VALUES (84, '2010-06-01', '2010-06-30', '2010', false, false);
INSERT INTO parm_periode VALUES (85, '2010-07-01', '2010-07-31', '2010', false, false);
INSERT INTO parm_periode VALUES (86, '2010-08-01', '2010-08-31', '2010', false, false);
INSERT INTO parm_periode VALUES (87, '2010-09-01', '2010-09-30', '2010', false, false);
INSERT INTO parm_periode VALUES (88, '2010-10-01', '2010-10-31', '2010', false, false);
INSERT INTO parm_periode VALUES (89, '2010-11-01', '2010-11-30', '2010', false, false);
INSERT INTO parm_periode VALUES (90, '2010-12-01', '2010-12-30', '2010', false, false);
INSERT INTO parm_periode VALUES (91, '2010-12-31', '2010-12-31', '2010', false, false);


--
-- Data for Name: parm_poste; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO parm_poste VALUES ('1', 'PAS');
INSERT INTO parm_poste VALUES ('101', 'PASINV');
INSERT INTO parm_poste VALUES ('141', 'PASINV');
INSERT INTO parm_poste VALUES ('42', 'PAS');
INSERT INTO parm_poste VALUES ('43', 'PAS');
INSERT INTO parm_poste VALUES ('44', 'PAS');
INSERT INTO parm_poste VALUES ('45', 'PAS');
INSERT INTO parm_poste VALUES ('46', 'PAS');
INSERT INTO parm_poste VALUES ('47', 'PAS');
INSERT INTO parm_poste VALUES ('48', 'PAS');
INSERT INTO parm_poste VALUES ('492', 'PAS');
INSERT INTO parm_poste VALUES ('493', 'PAS');
INSERT INTO parm_poste VALUES ('2', 'ACT');
INSERT INTO parm_poste VALUES ('2409', 'ACTINV');
INSERT INTO parm_poste VALUES ('3', 'ACT');
INSERT INTO parm_poste VALUES ('5', 'ACT');
INSERT INTO parm_poste VALUES ('491', 'ACT');
INSERT INTO parm_poste VALUES ('490', 'ACT');
INSERT INTO parm_poste VALUES ('6', 'CHA');
INSERT INTO parm_poste VALUES ('7', 'PRO');
INSERT INTO parm_poste VALUES ('4', 'ACT');
INSERT INTO parm_poste VALUES ('40', 'ACT');
INSERT INTO parm_poste VALUES ('5501', 'ACTINV');
INSERT INTO parm_poste VALUES ('5511', 'ACTINV');
INSERT INTO parm_poste VALUES ('5521', 'ACTINV');
INSERT INTO parm_poste VALUES ('5531', 'ACTINV');
INSERT INTO parm_poste VALUES ('5541', 'ACTINV');
INSERT INTO parm_poste VALUES ('5551', 'ACTINV');
INSERT INTO parm_poste VALUES ('5561', 'ACTINV');
INSERT INTO parm_poste VALUES ('5571', 'ACTINV');
INSERT INTO parm_poste VALUES ('5581', 'ACTINV');
INSERT INTO parm_poste VALUES ('5591', 'ACTINV');
INSERT INTO parm_poste VALUES ('6311', 'CHAINV');
INSERT INTO parm_poste VALUES ('6321', 'CHAINV');
INSERT INTO parm_poste VALUES ('6331', 'CHAINV');
INSERT INTO parm_poste VALUES ('6341', 'CHAINV');
INSERT INTO parm_poste VALUES ('6351', 'CHAINV');
INSERT INTO parm_poste VALUES ('6361', 'CHAINV');
INSERT INTO parm_poste VALUES ('6371', 'CHAINV');
INSERT INTO parm_poste VALUES ('649', 'CHAINV');
INSERT INTO parm_poste VALUES ('6511', 'CHAINV');
INSERT INTO parm_poste VALUES ('6701', 'CHAINV');
INSERT INTO parm_poste VALUES ('608', 'CHAINV');
INSERT INTO parm_poste VALUES ('709', 'PROINV');


--
-- Data for Name: plan_analytique; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: poste_analytique; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: profile; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO profile VALUES ('Administrateur', 1, 'Profil par défaut pour les adminstrateurs', true, true);
INSERT INTO profile VALUES ('Utilisateur', 2, 'Profil par défaut pour les utilisateurs', true, true);


--
-- Data for Name: profile_menu; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO profile_menu VALUES (59, 'CFGPAY', 'DIVPARM', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (68, 'CFGATCARD', 'DIVPARM', 1, 9, 'E', 0);
INSERT INTO profile_menu VALUES (61, 'CFGACC', 'DIVPARM', 1, 6, 'E', 0);
INSERT INTO profile_menu VALUES (54, 'COMPANY', 'PARAM', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (651, 'ANCHOP', 'ANCIMP', 1, 10, 'E', 0);
INSERT INTO profile_menu VALUES (173, 'COMPTA', NULL, 1, 40, 'M', 0);
INSERT INTO profile_menu VALUES (55, 'PERIODE', 'PARAM', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (56, 'DIVPARM', 'PARAM', 1, 3, 'E', 0);
INSERT INTO profile_menu VALUES (652, 'ANCGL', 'ANCIMP', 1, 20, 'E', 0);
INSERT INTO profile_menu VALUES (60, 'CFGTVA', 'DIVPARM', 1, 5, 'E', 0);
INSERT INTO profile_menu VALUES (653, 'ANCBS', 'ANCIMP', 1, 30, 'E', 0);
INSERT INTO profile_menu VALUES (654, 'ANCBC2', 'ANCIMP', 1, 40, 'E', 0);
INSERT INTO profile_menu VALUES (655, 'ANCTAB', 'ANCIMP', 1, 50, 'E', 0);
INSERT INTO profile_menu VALUES (656, 'ANCBCC', 'ANCIMP', 1, 60, 'E', 0);
INSERT INTO profile_menu VALUES (657, 'ANCGR', 'ANCIMP', 1, 70, 'E', 0);
INSERT INTO profile_menu VALUES (658, 'CSV:AncGrandLivre', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (662, 'new_line', NULL, 1, 35, 'M', 0);
INSERT INTO profile_menu VALUES (67, 'CFGCATDOC', 'DIVPARM', 1, 8, 'E', 0);
INSERT INTO profile_menu VALUES (69, 'CFGPCMN', 'PARAM', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (526, 'PRINTGL', 'PRINT', 1, 20, 'E', 0);
INSERT INTO profile_menu VALUES (23, 'LET', 'COMPTA', 1, 8, 'E', 0);
INSERT INTO profile_menu VALUES (523, 'PRINTBAL', 'PRINT', 1, 50, 'E', 0);
INSERT INTO profile_menu VALUES (529, 'PRINTREPORT', 'PRINT', 1, 85, 'E', 0);
INSERT INTO profile_menu VALUES (72, 'PREDOP', 'PARAM', 1, 7, 'E', 0);
INSERT INTO profile_menu VALUES (75, 'PLANANC', 'ANC', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (65, 'CFGCARDCAT', 'DIVPARM', 1, 7, 'E', 0);
INSERT INTO profile_menu VALUES (76, 'ANCODS', 'ANC', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (77, 'ANCGROUP', 'ANC', 1, 3, 'E', 0);
INSERT INTO profile_menu VALUES (78, 'ANCIMP', 'ANC', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (45, 'PARAM', NULL, 1, 20, 'M', 0);
INSERT INTO profile_menu VALUES (527, 'PRINTJRN', 'PRINT', 1, 10, 'E', 0);
INSERT INTO profile_menu VALUES (530, 'PRINTREC', 'PRINT', 1, 100, 'E', 0);
INSERT INTO profile_menu VALUES (524, 'PRINTBILAN', 'PRINT', 1, 90, 'E', 0);
INSERT INTO profile_menu VALUES (79, 'PREFERENCE', NULL, 1, 15, 'M', 0);
INSERT INTO profile_menu VALUES (37, 'CUST', 'GESTION', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (38, 'SUPPL', 'GESTION', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (39, 'ADM', 'GESTION', 1, 3, 'E', 0);
INSERT INTO profile_menu VALUES (36, 'CARD', 'GESTION', 1, 6, 'E', 0);
INSERT INTO profile_menu VALUES (40, 'STOCK', 'GESTION', 1, 5, 'E', 0);
INSERT INTO profile_menu VALUES (41, 'FORECAST', 'GESTION', 1, 7, 'E', 0);
INSERT INTO profile_menu VALUES (42, 'FOLLOW', 'GESTION', 1, 8, 'E', 0);
INSERT INTO profile_menu VALUES (29, 'VERIFBIL', 'ADV', 1, 21, 'E', 0);
INSERT INTO profile_menu VALUES (30, 'STOCK', 'ADV', 1, 22, 'E', 0);
INSERT INTO profile_menu VALUES (31, 'PREDOP', 'ADV', 1, 23, 'E', 0);
INSERT INTO profile_menu VALUES (32, 'OPEN', 'ADV', 1, 24, 'E', 0);
INSERT INTO profile_menu VALUES (33, 'REPORT', 'ADV', 1, 25, 'E', 0);
INSERT INTO profile_menu VALUES (5, 'CARD', 'COMPTA', 1, 7, 'E', 0);
INSERT INTO profile_menu VALUES (43, 'HIST', 'COMPTA', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (28, 'ADV', 'COMPTA', 1, 20, 'E', 0);
INSERT INTO profile_menu VALUES (53, 'ACCESS', NULL, 1, 25, 'M', 0);
INSERT INTO profile_menu VALUES (123, 'CSV:histo', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (20, 'LOGOUT', NULL, 1, 30, 'M', 0);
INSERT INTO profile_menu VALUES (35, 'PRINT', 'GESTION', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (124, 'CSV:ledger', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (125, 'PDF:ledger', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (6, 'PRINT', 'COMPTA', 1, 6, 'E', 0);
INSERT INTO profile_menu VALUES (126, 'CSV:postedetail', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (3, 'MENUACH', 'COMPTA', 1, 3, 'E', 0);
INSERT INTO profile_menu VALUES (86, 'ACHIMP', 'MENUACH', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (34, 'GESTION', NULL, 1, 45, 'M', 0);
INSERT INTO profile_menu VALUES (18, 'MENUODS', 'COMPTA', 1, 5, 'E', 0);
INSERT INTO profile_menu VALUES (88, 'ODS', 'MENUODS', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (89, 'ODSIMP', 'MENUODS', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (2, 'ANC', NULL, 1, 50, 'M', 0);
INSERT INTO profile_menu VALUES (4, 'VENMENU', 'COMPTA', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (90, 'VEN', 'VENMENU', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (91, 'VENIMP', 'VENMENU', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (19, 'FIN', 'MENUFIN', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (73, 'CFGDOC', 'PARAM', 1, 8, 'E', 0);
INSERT INTO profile_menu VALUES (74, 'CFGLED', 'PARAM', 1, 9, 'E', 0);
INSERT INTO profile_menu VALUES (71, 'CFGSEC', 'PARAM', 1, 6, 'E', 0);
INSERT INTO profile_menu VALUES (82, 'EXT', NULL, 1, 55, 'M', 0);
INSERT INTO profile_menu VALUES (95, 'FREC', 'MENUFIN', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (94, 'FSALDO', 'MENUFIN', 1, 3, 'E', 0);
INSERT INTO profile_menu VALUES (27, 'LETACC', 'LET', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (24, 'LETCARD', 'LET', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (167, 'MOD', 'PARAM', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (92, 'MENUFIN', 'COMPTA', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (93, 'FIMP', 'MENUFIN', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (151, 'SEARCH', NULL, 1, 60, 'M', 0);
INSERT INTO profile_menu VALUES (85, 'ACH', 'MENUACH', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (127, 'PDF:postedetail', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (128, 'CSV:fichedetail', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (129, 'PDF:fichedetail', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (130, 'CSV:fiche_balance', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (131, 'PDF:fiche_balance', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (132, 'CSV:report', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (133, 'PDF:report', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (134, 'CSV:fiche', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (135, 'PDF:fiche', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (136, 'CSV:glcompte', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (137, 'PDF:glcompte', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (138, 'PDF:sec', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (139, 'CSV:AncList', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (140, 'CSV:AncBalSimple', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (141, 'PDF:AncBalSimple', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (142, 'CSV:AncBalDouble', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (143, 'PDF:AncBalDouble', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (144, 'CSV:balance', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (145, 'PDF:balance', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (146, 'CSV:AncTable', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (147, 'CSV:AncAccList', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (148, 'CSV:AncBalGroup', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (149, 'OTH:Bilan', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (528, 'PRINTPOSTE', 'PRINT', 1, 30, 'E', 0);
INSERT INTO profile_menu VALUES (525, 'PRINTCARD', 'PRINT', 1, 40, 'E', 0);
INSERT INTO profile_menu VALUES (1, 'DASHBOARD', NULL, 1, 10, 'M', 1);
INSERT INTO profile_menu VALUES (172, 'CFGPRO', 'MOD', 1, NULL, 'E', 0);
INSERT INTO profile_menu VALUES (171, 'CFGMENU', 'MOD', 1, NULL, 'E', 0);
INSERT INTO profile_menu VALUES (663, 'CFGPAY', 'DIVPARM', 2, 4, 'E', 0);
INSERT INTO profile_menu VALUES (664, 'CFGATCARD', 'DIVPARM', 2, 9, 'E', 0);
INSERT INTO profile_menu VALUES (665, 'CFGACC', 'DIVPARM', 2, 6, 'E', 0);
INSERT INTO profile_menu VALUES (668, 'ANCHOP', 'ANCIMP', 2, 10, 'E', 0);
INSERT INTO profile_menu VALUES (669, 'COMPTA', NULL, 2, 40, 'M', 0);
INSERT INTO profile_menu VALUES (672, 'ANCGL', 'ANCIMP', 2, 20, 'E', 0);
INSERT INTO profile_menu VALUES (673, 'CFGTVA', 'DIVPARM', 2, 5, 'E', 0);
INSERT INTO profile_menu VALUES (674, 'ANCBS', 'ANCIMP', 2, 30, 'E', 0);
INSERT INTO profile_menu VALUES (675, 'ANCBC2', 'ANCIMP', 2, 40, 'E', 0);
INSERT INTO profile_menu VALUES (676, 'ANCTAB', 'ANCIMP', 2, 50, 'E', 0);
INSERT INTO profile_menu VALUES (677, 'ANCBCC', 'ANCIMP', 2, 60, 'E', 0);
INSERT INTO profile_menu VALUES (678, 'ANCGR', 'ANCIMP', 2, 70, 'E', 0);
INSERT INTO profile_menu VALUES (679, 'CSV:AncGrandLivre', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (680, 'new_line', NULL, 2, 35, 'M', 0);
INSERT INTO profile_menu VALUES (681, 'CFGCATDOC', 'DIVPARM', 2, 8, 'E', 0);
INSERT INTO profile_menu VALUES (683, 'PRINTGL', 'PRINT', 2, 20, 'E', 0);
INSERT INTO profile_menu VALUES (684, 'LET', 'COMPTA', 2, 8, 'E', 0);
INSERT INTO profile_menu VALUES (685, 'PRINTBAL', 'PRINT', 2, 50, 'E', 0);
INSERT INTO profile_menu VALUES (686, 'PRINTREPORT', 'PRINT', 2, 85, 'E', 0);
INSERT INTO profile_menu VALUES (688, 'PLANANC', 'ANC', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (689, 'CFGCARDCAT', 'DIVPARM', 2, 7, 'E', 0);
INSERT INTO profile_menu VALUES (690, 'ANCODS', 'ANC', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (717, 'CSV:ledger', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (718, 'PDF:ledger', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (719, 'PRINT', 'COMPTA', 2, 6, 'E', 0);
INSERT INTO profile_menu VALUES (720, 'CSV:postedetail', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (721, 'MENUACH', 'COMPTA', 2, 3, 'E', 0);
INSERT INTO profile_menu VALUES (722, 'ACHIMP', 'MENUACH', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (723, 'GESTION', NULL, 2, 45, 'M', 0);
INSERT INTO profile_menu VALUES (724, 'MENUODS', 'COMPTA', 2, 5, 'E', 0);
INSERT INTO profile_menu VALUES (725, 'ODS', 'MENUODS', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (726, 'ODSIMP', 'MENUODS', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (727, 'ANC', NULL, 2, 50, 'M', 0);
INSERT INTO profile_menu VALUES (728, 'VENMENU', 'COMPTA', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (729, 'VEN', 'VENMENU', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (730, 'VENIMP', 'VENMENU', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (731, 'FIN', 'MENUFIN', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (735, 'EXT', NULL, 2, 55, 'M', 0);
INSERT INTO profile_menu VALUES (736, 'FREC', 'MENUFIN', 2, 4, 'E', 0);
INSERT INTO profile_menu VALUES (737, 'FSALDO', 'MENUFIN', 2, 3, 'E', 0);
INSERT INTO profile_menu VALUES (738, 'LETACC', 'LET', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (691, 'ANCGROUP', 'ANC', 2, 3, 'E', 0);
INSERT INTO profile_menu VALUES (692, 'ANCIMP', 'ANC', 2, 4, 'E', 0);
INSERT INTO profile_menu VALUES (694, 'PRINTJRN', 'PRINT', 2, 10, 'E', 0);
INSERT INTO profile_menu VALUES (695, 'PRINTREC', 'PRINT', 2, 100, 'E', 0);
INSERT INTO profile_menu VALUES (696, 'PRINTBILAN', 'PRINT', 2, 90, 'E', 0);
INSERT INTO profile_menu VALUES (697, 'PREFERENCE', NULL, 2, 15, 'M', 0);
INSERT INTO profile_menu VALUES (698, 'CUST', 'GESTION', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (699, 'SUPPL', 'GESTION', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (700, 'ADM', 'GESTION', 2, 3, 'E', 0);
INSERT INTO profile_menu VALUES (701, 'CARD', 'GESTION', 2, 6, 'E', 0);
INSERT INTO profile_menu VALUES (702, 'STOCK', 'GESTION', 2, 5, 'E', 0);
INSERT INTO profile_menu VALUES (703, 'FORECAST', 'GESTION', 2, 7, 'E', 0);
INSERT INTO profile_menu VALUES (704, 'FOLLOW', 'GESTION', 2, 8, 'E', 0);
INSERT INTO profile_menu VALUES (705, 'VERIFBIL', 'ADV', 2, 21, 'E', 0);
INSERT INTO profile_menu VALUES (706, 'STOCK', 'ADV', 2, 22, 'E', 0);
INSERT INTO profile_menu VALUES (707, 'PREDOP', 'ADV', 2, 23, 'E', 0);
INSERT INTO profile_menu VALUES (708, 'OPEN', 'ADV', 2, 24, 'E', 0);
INSERT INTO profile_menu VALUES (709, 'REPORT', 'ADV', 2, 25, 'E', 0);
INSERT INTO profile_menu VALUES (710, 'CARD', 'COMPTA', 2, 7, 'E', 0);
INSERT INTO profile_menu VALUES (711, 'HIST', 'COMPTA', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (712, 'ADV', 'COMPTA', 2, 20, 'E', 0);
INSERT INTO profile_menu VALUES (713, 'ACCESS', NULL, 2, 25, 'M', 0);
INSERT INTO profile_menu VALUES (714, 'CSV:histo', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (715, 'LOGOUT', NULL, 2, 30, 'M', 0);
INSERT INTO profile_menu VALUES (716, 'PRINT', 'GESTION', 2, 4, 'E', 0);
INSERT INTO profile_menu VALUES (739, 'LETCARD', 'LET', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (742, 'MENUFIN', 'COMPTA', 2, 4, 'E', 0);
INSERT INTO profile_menu VALUES (743, 'FIMP', 'MENUFIN', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (744, 'SEARCH', NULL, 2, 60, 'M', 0);
INSERT INTO profile_menu VALUES (745, 'ACH', 'MENUACH', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (746, 'PDF:postedetail', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (747, 'CSV:fichedetail', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (748, 'PDF:fichedetail', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (749, 'CSV:fiche_balance', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (750, 'PDF:fiche_balance', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (751, 'CSV:report', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (752, 'PDF:report', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (753, 'CSV:fiche', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (754, 'PDF:fiche', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (755, 'CSV:glcompte', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (756, 'PDF:glcompte', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (757, 'PDF:sec', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (758, 'CSV:AncList', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (759, 'CSV:AncBalSimple', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (760, 'PDF:AncBalSimple', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (761, 'CSV:AncBalDouble', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (762, 'PDF:AncBalDouble', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (763, 'CSV:balance', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (764, 'PDF:balance', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (765, 'CSV:AncTable', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (766, 'CSV:AncAccList', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (767, 'CSV:AncBalGroup', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (768, 'OTH:Bilan', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (769, 'PRINTPOSTE', 'PRINT', 2, 30, 'E', 0);
INSERT INTO profile_menu VALUES (770, 'PRINTCARD', 'PRINT', 2, 40, 'E', 0);
INSERT INTO profile_menu VALUES (777, 'CFGPRO', 'MOD', 2, NULL, 'E', 0);
INSERT INTO profile_menu VALUES (778, 'CFGMENU', 'MOD', 2, NULL, 'E', 0);
INSERT INTO profile_menu VALUES (772, 'DASHBOARD', NULL, 2, 10, 'M', 1);
INSERT INTO profile_menu VALUES (779, 'TVA', 'EXT', 1, NULL, 'S', NULL);


--
-- Data for Name: profile_menu_type; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO profile_menu_type VALUES ('P', 'Impression');
INSERT INTO profile_menu_type VALUES ('S', 'Extension');
INSERT INTO profile_menu_type VALUES ('E', 'Menu');
INSERT INTO profile_menu_type VALUES ('M', 'Module');


--
-- Data for Name: profile_user; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO profile_user VALUES ('phpcompta', 1, 1);


--
-- Data for Name: quant_fin; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: quant_purchase; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: quant_sold; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: stock_goods; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: tmp_pcmn; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO tmp_pcmn VALUES ('794', 'Intervention d''associés (ou du propriétaire) dans la perte', '79', 'PRO');
INSERT INTO tmp_pcmn VALUES ('1', 'Fonds propres, provisions pour risques et charges à plus d''un an', '0', 'PAS');
INSERT INTO tmp_pcmn VALUES ('2', 'Frais d''établissement, actifs immobilisés et créances à plus d''un an', '0', 'ACT');
INSERT INTO tmp_pcmn VALUES ('3', 'Stocks et commandes en cours d''éxécution', '0', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4', 'Créances et dettes à un an au plus', '0', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5', 'Placements de trésorerie et valeurs disponibles', '0', 'ACT');
INSERT INTO tmp_pcmn VALUES ('6', 'Charges', '0', 'CHA');
INSERT INTO tmp_pcmn VALUES ('7', 'Produits', '0', 'PRO');
INSERT INTO tmp_pcmn VALUES ('4000001', 'Client 1', '400', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4000002', 'Client 2', '400', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4000003', 'Client 3', '400', 'ACT');
INSERT INTO tmp_pcmn VALUES ('6040001', 'Electricité', '604', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6040002', 'Loyer', '604', 'CHA');
INSERT INTO tmp_pcmn VALUES ('55000002', 'Banque 1', '5500', 'ACT');
INSERT INTO tmp_pcmn VALUES ('55000003', 'Banque 2', '5500', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4400001', 'Fournisseur 1', '440', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4400002', 'Fournisseur 2', '440', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4400003', 'Fournisseur 4', '440', 'PAS');
INSERT INTO tmp_pcmn VALUES ('610001', 'Electricité', '61', 'CHA');
INSERT INTO tmp_pcmn VALUES ('610002', 'Loyer', '61', 'CHA');
INSERT INTO tmp_pcmn VALUES ('610003', 'Assurance', '61', 'CHA');
INSERT INTO tmp_pcmn VALUES ('610004', 'Matériel bureau', '61', 'CHA');
INSERT INTO tmp_pcmn VALUES ('7000002', 'Marchandise A', '700', 'PRO');
INSERT INTO tmp_pcmn VALUES ('7000001', 'Prestation', '700', 'PRO');
INSERT INTO tmp_pcmn VALUES ('7000003', 'Déplacement', '700', 'PRO');
INSERT INTO tmp_pcmn VALUES ('101', 'Capital non appelé', '10', 'PASINV');
INSERT INTO tmp_pcmn VALUES ('6190', 'TVA récupérable par l''impôt', '61', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6740', 'Dépense non admise', '67', 'CHA');
INSERT INTO tmp_pcmn VALUES ('9', 'Comptes hors Compta', '0', 'CON');
INSERT INTO tmp_pcmn VALUES ('100', 'Capital souscrit', '10', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1311', 'Autres réserves indisponibles', '131', 'PAS');
INSERT INTO tmp_pcmn VALUES ('132', ' Réserves immunisées', '13', 'PAS');
INSERT INTO tmp_pcmn VALUES ('6711', 'Suppléments d''impôts estimés', '671', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6712', 'Provisions fiscales constituées', '671', 'CHA');
INSERT INTO tmp_pcmn VALUES ('672', 'Impôts étrangers sur le résultat de l''exercice', '67', 'CHA');
INSERT INTO tmp_pcmn VALUES ('673', 'Impôts étrangers sur le résultat d''exercice antérieures', '67', 'CHA');
INSERT INTO tmp_pcmn VALUES ('68', 'Transferts aux réserves immunisées', '6', 'CHA');
INSERT INTO tmp_pcmn VALUES ('69', 'Affectations et prélévements', '6', 'CHA');
INSERT INTO tmp_pcmn VALUES ('690', 'Perte reportée de l''exercice précédent', '69', 'CHA');
INSERT INTO tmp_pcmn VALUES ('691', 'Dotation à la réserve légale', '69', 'CHA');
INSERT INTO tmp_pcmn VALUES ('692', 'Dotation aux autres réserves', '69', 'CHA');
INSERT INTO tmp_pcmn VALUES ('693', 'Bénéfice à reporter', '69', 'CHA');
INSERT INTO tmp_pcmn VALUES ('694', 'Rémunération du capital', '69', 'CHA');
INSERT INTO tmp_pcmn VALUES ('695', 'Administrateurs ou gérants', '69', 'CHA');
INSERT INTO tmp_pcmn VALUES ('696', 'Autres allocataires', '69', 'CHA');
INSERT INTO tmp_pcmn VALUES ('70', 'Chiffre d''affaire', '7', 'PRO');
INSERT INTO tmp_pcmn VALUES ('700', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn VALUES ('701', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn VALUES ('702', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn VALUES ('703', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn VALUES ('704', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn VALUES ('706', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn VALUES ('707', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn VALUES ('709', 'Remises, ristournes et rabais accordés(-)', '70', 'PROINV');
INSERT INTO tmp_pcmn VALUES ('71', 'Variations des stocks et commandes en cours d''éxécution', '7', 'PRO');
INSERT INTO tmp_pcmn VALUES ('712', 'des en-cours de fabrication', '71', 'PRO');
INSERT INTO tmp_pcmn VALUES ('713', 'des produits finis', '71', 'PRO');
INSERT INTO tmp_pcmn VALUES ('715', 'des immeubles construits destinés à la vente', '71', 'PRO');
INSERT INTO tmp_pcmn VALUES ('717', ' des commandes  en cours d''éxécution', '71', 'PRO');
INSERT INTO tmp_pcmn VALUES ('7170', 'Valeur d''acquisition', '717', 'PRO');
INSERT INTO tmp_pcmn VALUES ('7171', 'Bénéfice pris en compte', '717', 'PRO');
INSERT INTO tmp_pcmn VALUES ('72', 'Production immobilisée', '7', 'PRO');
INSERT INTO tmp_pcmn VALUES ('74', 'Autres produits d''exploitation', '7', 'PRO');
INSERT INTO tmp_pcmn VALUES ('740', 'Subsides d'' exploitation  et montants compensatoires', '74', 'PRO');
INSERT INTO tmp_pcmn VALUES ('741', 'Plus-values sur réalisation courantes d'' immobilisations corporelles', '74', 'PRO');
INSERT INTO tmp_pcmn VALUES ('742', 'Plus-values sur réalisations de créances commerciales', '74', 'PRO');
INSERT INTO tmp_pcmn VALUES ('743', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn VALUES ('744', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn VALUES ('745', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn VALUES ('746', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn VALUES ('747', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn VALUES ('748', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn VALUES ('75', 'Produits financiers', '7', 'PRO');
INSERT INTO tmp_pcmn VALUES ('750', 'Produits sur immobilisations financières', '75', 'PRO');
INSERT INTO tmp_pcmn VALUES ('751', 'Produits des actifs circulants', '75', 'PRO');
INSERT INTO tmp_pcmn VALUES ('752', 'Plus-value sur réalisations d''actis circulants', '75', 'PRO');
INSERT INTO tmp_pcmn VALUES ('753', 'Subsides en capital et intérêts', '75', 'PRO');
INSERT INTO tmp_pcmn VALUES ('754', 'Différences de change', '75', 'PRO');
INSERT INTO tmp_pcmn VALUES ('755', 'Ecarts de conversion des devises', '75', 'PRO');
INSERT INTO tmp_pcmn VALUES ('221', 'Construction', '22', 'ACT');
INSERT INTO tmp_pcmn VALUES ('756', 'Produits financiers divers', '75', 'PRO');
INSERT INTO tmp_pcmn VALUES ('757', 'Produits financiers divers', '75', 'PRO');
INSERT INTO tmp_pcmn VALUES ('758', 'Produits financiers divers', '75', 'PRO');
INSERT INTO tmp_pcmn VALUES ('759', 'Produits financiers divers', '75', 'PRO');
INSERT INTO tmp_pcmn VALUES ('76', 'Produits exceptionnels', '7', 'PRO');
INSERT INTO tmp_pcmn VALUES ('760', 'Reprise d''amortissements et de réductions de valeur', '76', 'PRO');
INSERT INTO tmp_pcmn VALUES ('7601', 'sur immobilisations corporelles', '760', 'PRO');
INSERT INTO tmp_pcmn VALUES ('7602', 'sur immobilisations incorporelles', '760', 'PRO');
INSERT INTO tmp_pcmn VALUES ('761', 'Reprises de réductions de valeur sur immobilisations financières', '76', 'PRO');
INSERT INTO tmp_pcmn VALUES ('762', 'Reprises de provisions pour risques et charges exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn VALUES ('763', 'Plus-value sur réalisation d''actifs immobilisé', '76', 'PRO');
INSERT INTO tmp_pcmn VALUES ('764', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn VALUES ('765', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn VALUES ('766', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn VALUES ('767', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn VALUES ('768', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn VALUES ('769', 'Autres produits exceptionnels', '76', 'PRO');
INSERT INTO tmp_pcmn VALUES ('77', 'Régularisations d''impôts et reprises de provisions fiscales', '7', 'PRO');
INSERT INTO tmp_pcmn VALUES ('771', 'impôts belges sur le résultat', '77', 'PRO');
INSERT INTO tmp_pcmn VALUES ('7710', 'Régularisations d''impôts dus ou versé', '771', 'PRO');
INSERT INTO tmp_pcmn VALUES ('7711', 'Régularisations d''impôts estimés', '771', 'PRO');
INSERT INTO tmp_pcmn VALUES ('7712', 'Reprises de provisions fiscales', '771', 'PRO');
INSERT INTO tmp_pcmn VALUES ('773', 'Impôts étrangers sur le résultats', '77', 'PRO');
INSERT INTO tmp_pcmn VALUES ('79', 'Affectations et prélévements', '7', 'PRO');
INSERT INTO tmp_pcmn VALUES ('790', 'Bénéfice reporté de l''exercice précédent', '79', 'PRO');
INSERT INTO tmp_pcmn VALUES ('791', 'Prélévement sur le capital et les primes d''émission', '79', 'PRO');
INSERT INTO tmp_pcmn VALUES ('792', 'Prélévement sur les réserves', '79', 'PRO');
INSERT INTO tmp_pcmn VALUES ('793', 'Perte à reporter', '79', 'PRO');
INSERT INTO tmp_pcmn VALUES ('6301', 'Dotations aux amortissements sur immobilisations incorporelles', '630', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6302', 'Dotations aux amortissements sur immobilisations corporelles', '630', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6308', 'Dotations aux réductions de valeur sur immobilisations incorporelles', '630', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6309', 'Dotations aux réductions de valeur sur immobilisations corporelles', '630', 'CHA');
INSERT INTO tmp_pcmn VALUES ('631', 'Réductions de valeur sur stocks', '63', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6310', 'Dotations', '631', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6311', 'Reprises(-)', '631', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('632', 'Réductions de valeur sur commande en cours d''éxécution', '63', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6320', 'Dotations', '632', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6321', 'Reprises(-)', '632', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('633', 'Réductions de valeurs sur créances commerciales à plus d''un an', '63', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6330', 'Dotations', '633', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6331', 'Reprises(-)', '633', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('634', 'Réductions de valeur sur créances commerciales à un an au plus', '63', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6340', 'Dotations', '634', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6341', 'Reprise', '634', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('635', 'Provisions pour pensions et obligations similaires', '63', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6350', 'Dotations', '635', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6351', 'Utilisation et reprises', '635', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('636', 'Provisions pour grosses réparations et gros entretien', '63', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6360', 'Dotations', '636', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6361', 'Reprises(-)', '636', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('637', 'Provisions pour autres risques et charges', '63', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6370', 'Dotations', '637', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6371', 'Reprises(-)', '637', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('64', 'Autres charges d''exploitation', '6', 'CHA');
INSERT INTO tmp_pcmn VALUES ('640', 'Charges fiscales d''exploitation', '64', 'CHA');
INSERT INTO tmp_pcmn VALUES ('641', 'Moins-values sur réalisations courantes d''immobilisations corporelles', '64', 'CHA');
INSERT INTO tmp_pcmn VALUES ('642', 'Moins-value sur réalisation de créances commerciales', '64', 'CHA');
INSERT INTO tmp_pcmn VALUES ('643', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn VALUES ('644', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn VALUES ('645', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn VALUES ('646', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn VALUES ('647', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn VALUES ('648', 'Charges d''exploitations', '64', 'CHA');
INSERT INTO tmp_pcmn VALUES ('649', 'Charges d''exploitation portées à l''actif au titre de frais de restructuration(-)', '64', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('65', 'Charges financières', '6', 'CHA');
INSERT INTO tmp_pcmn VALUES ('650', 'Charges des dettes', '65', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6500', 'Intérêts, commmissions et frais afférents aux dettes', '650', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6501', 'Amortissements des frais d''émissions d''emrunts et des primes de remboursement', '650', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6502', 'Autres charges des dettes', '650', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6503', 'Intérêts intercalaires portés à l''actif(-)', '650', 'CHA');
INSERT INTO tmp_pcmn VALUES ('651', 'Réductions de valeur sur actifs circulants', '65', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6510', 'Dotations', '651', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6511', 'Reprises(-)', '651', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('652', 'Moins-value sur réalisation d''actifs circulants', '65', 'CHA');
INSERT INTO tmp_pcmn VALUES ('653', 'Charges d''escompte de créances', '65', 'CHA');
INSERT INTO tmp_pcmn VALUES ('654', 'Différences de changes', '65', 'CHA');
INSERT INTO tmp_pcmn VALUES ('655', 'Ecarts de conversion des devises', '65', 'CHA');
INSERT INTO tmp_pcmn VALUES ('656', 'Charges financières diverses', '65', 'CHA');
INSERT INTO tmp_pcmn VALUES ('657', 'Charges financières diverses', '65', 'CHA');
INSERT INTO tmp_pcmn VALUES ('658', 'Charges financières diverses', '65', 'CHA');
INSERT INTO tmp_pcmn VALUES ('659', 'Charges financières diverses', '65', 'CHA');
INSERT INTO tmp_pcmn VALUES ('66', 'Charges exceptionnelles', '6', 'CHA');
INSERT INTO tmp_pcmn VALUES ('660', 'Amortissements et réductions de valeur exceptionnels (dotations)', '66', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6600', 'sur frais d''établissement', '660', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6601', 'sur immobilisations incorporelles', '660', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6602', 'sur immobilisations corporelles', '660', 'CHA');
INSERT INTO tmp_pcmn VALUES ('661', 'Réductions de valeur sur immobilisations financières (dotations)', '66', 'CHA');
INSERT INTO tmp_pcmn VALUES ('662', 'Provisions pour risques et charges exceptionnels', '66', 'CHA');
INSERT INTO tmp_pcmn VALUES ('663', 'Moins-values sur réalisations d''actifs immobilisés', '66', 'CHA');
INSERT INTO tmp_pcmn VALUES ('664', 'Autres charges exceptionnelles', '66', 'CHA');
INSERT INTO tmp_pcmn VALUES ('665', 'Autres charges exceptionnelles', '66', 'CHA');
INSERT INTO tmp_pcmn VALUES ('666', 'Autres charges exceptionnelles', '66', 'CHA');
INSERT INTO tmp_pcmn VALUES ('667', 'Autres charges exceptionnelles', '66', 'CHA');
INSERT INTO tmp_pcmn VALUES ('668', 'Autres charges exceptionnelles', '66', 'CHA');
INSERT INTO tmp_pcmn VALUES ('669', ' Charges exceptionnelles portées à l''actif au titre de frais de restructuration', '66', 'CHA');
INSERT INTO tmp_pcmn VALUES ('67', 'impôts sur le résultat', '6', 'CHA');
INSERT INTO tmp_pcmn VALUES ('670', 'Impôts belge sur le résultat de l''exercice', '67', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6700', 'Impôts et précomptes dus ou versés', '670', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6701', 'Excédents de versement d''impôts et de précomptes portés à l''actifs (-)', '670', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('6702', 'Charges fiscales estimées', '670', 'CHA');
INSERT INTO tmp_pcmn VALUES ('671', 'Impôts belges sur le résultats d''exercices antérieures', '67', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6710', 'Suppléments d''impôt dus ou versés', '671', 'CHA');
INSERT INTO tmp_pcmn VALUES ('50', 'Actions propres', '5', 'ACT');
INSERT INTO tmp_pcmn VALUES ('51', 'Actions et parts', '5', 'ACT');
INSERT INTO tmp_pcmn VALUES ('510', 'Valeur d''acquisition', '51', 'ACT');
INSERT INTO tmp_pcmn VALUES ('511', 'Montant non appelés', '51', 'ACT');
INSERT INTO tmp_pcmn VALUES ('519', 'Réductions de valeur actées', '51', 'ACT');
INSERT INTO tmp_pcmn VALUES ('52', 'Titres à revenu fixe', '5', 'ACT');
INSERT INTO tmp_pcmn VALUES ('520', 'Valeur d''acquisition', '52', 'ACT');
INSERT INTO tmp_pcmn VALUES ('529', 'Réductions de valeur actées', '52', 'ACT');
INSERT INTO tmp_pcmn VALUES ('53', 'Dépôts à terme', '5', 'ACT');
INSERT INTO tmp_pcmn VALUES ('530', 'de plus d''un an', '53', 'ACT');
INSERT INTO tmp_pcmn VALUES ('531', 'de plus d''un mois et d''un an au plus', '53', 'ACT');
INSERT INTO tmp_pcmn VALUES ('532', 'd''un mois au plus', '53', 'ACT');
INSERT INTO tmp_pcmn VALUES ('539', 'Réductions de valeur actées', '53', 'ACT');
INSERT INTO tmp_pcmn VALUES ('54', 'Valeurs échues à l''encaissement', '5', 'ACT');
INSERT INTO tmp_pcmn VALUES ('55', 'Etablissement de crédit', '5', 'ACT');
INSERT INTO tmp_pcmn VALUES ('550', 'Banque 1', '55', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5500', 'Comptes courants', '550', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5501', 'Chèques émis (-)', '550', 'ACTINV');
INSERT INTO tmp_pcmn VALUES ('5509', 'Réduction de valeur actée', '550', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5510', 'Comptes courants', '551', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5511', 'Chèques émis (-)', '551', 'ACTINV');
INSERT INTO tmp_pcmn VALUES ('5519', 'Réduction de valeur actée', '551', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5520', 'Comptes courants', '552', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5521', 'Chèques émis (-)', '552', 'ACTINV');
INSERT INTO tmp_pcmn VALUES ('5529', 'Réduction de valeur actée', '552', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5530', 'Comptes courants', '553', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5531', 'Chèques émis (-)', '553', 'ACTINV');
INSERT INTO tmp_pcmn VALUES ('5539', 'Réduction de valeur actée', '553', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5540', 'Comptes courants', '554', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5541', 'Chèques émis (-)', '554', 'ACTINV');
INSERT INTO tmp_pcmn VALUES ('5549', 'Réduction de valeur actée', '554', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5550', 'Comptes courants', '555', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5551', 'Chèques émis (-)', '555', 'ACTINV');
INSERT INTO tmp_pcmn VALUES ('5559', 'Réduction de valeur actée', '555', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5560', 'Comptes courants', '556', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5561', 'Chèques émis (-)', '556', 'ACTINV');
INSERT INTO tmp_pcmn VALUES ('5569', 'Réduction de valeur actée', '556', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5570', 'Comptes courants', '557', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5571', 'Chèques émis (-)', '557', 'ACTINV');
INSERT INTO tmp_pcmn VALUES ('5579', 'Réduction de valeur actée', '557', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5580', 'Comptes courants', '558', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5581', 'Chèques émis (-)', '558', 'ACTINV');
INSERT INTO tmp_pcmn VALUES ('5589', 'Réduction de valeur actée', '558', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5590', 'Comptes courants', '559', 'ACT');
INSERT INTO tmp_pcmn VALUES ('5591', 'Chèques émis (-)', '559', 'ACTINV');
INSERT INTO tmp_pcmn VALUES ('5599', 'Réduction de valeur actée', '559', 'ACT');
INSERT INTO tmp_pcmn VALUES ('56', 'Office des chèques postaux', '5', 'ACT');
INSERT INTO tmp_pcmn VALUES ('560', 'Compte courant', '56', 'ACT');
INSERT INTO tmp_pcmn VALUES ('561', 'Chèques émis', '56', 'ACT');
INSERT INTO tmp_pcmn VALUES ('578', 'Caisse timbre', '57', 'ACT');
INSERT INTO tmp_pcmn VALUES ('58', 'Virement interne', '5', 'ACT');
INSERT INTO tmp_pcmn VALUES ('60', 'Approvisionnement et marchandises', '6', 'CHA');
INSERT INTO tmp_pcmn VALUES ('600', 'Achats de matières premières', '60', 'CHA');
INSERT INTO tmp_pcmn VALUES ('601', 'Achats de fournitures', '60', 'CHA');
INSERT INTO tmp_pcmn VALUES ('602', 'Achats de services, travaux et études', '60', 'CHA');
INSERT INTO tmp_pcmn VALUES ('603', 'Sous-traitances générales', '60', 'CHA');
INSERT INTO tmp_pcmn VALUES ('604', 'Achats de marchandises', '60', 'CHA');
INSERT INTO tmp_pcmn VALUES ('605', 'Achats d''immeubles destinés à la vente', '60', 'CHA');
INSERT INTO tmp_pcmn VALUES ('608', 'Remises, ristournes et rabais obtenus(-)', '60', 'CHAINV');
INSERT INTO tmp_pcmn VALUES ('609', 'Variation de stock', '60', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6090', 'de matières premières', '609', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6091', 'de fournitures', '609', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6094', 'de marchandises', '609', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6095', 'immeubles achetés destinés à la vente', '609', 'CHA');
INSERT INTO tmp_pcmn VALUES ('61', 'Services et biens divers', '6', 'CHA');
INSERT INTO tmp_pcmn VALUES ('62', 'Rémunérations, charges sociales et pensions', '6', 'CHA');
INSERT INTO tmp_pcmn VALUES ('620', 'Rémunérations et avantages sociaux directs', '62', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6200', 'Administrateurs ou gérants', '620', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6201', 'Personnel de directions', '620', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6202', 'Employés,620', '6202', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6203', 'Ouvriers', '620', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6204', 'Autres membres du personnel', '620', 'CHA');
INSERT INTO tmp_pcmn VALUES ('621', 'Cotisations patronales d''assurances sociales', '62', 'CHA');
INSERT INTO tmp_pcmn VALUES ('622', 'Primes partonales pour assurances extra-légales', '62', 'CHA');
INSERT INTO tmp_pcmn VALUES ('623', 'Autres frais de personnel', '62', 'CHA');
INSERT INTO tmp_pcmn VALUES ('624', 'Pensions de retraite et de survie', '62', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6240', 'Administrateurs ou gérants', '624', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6241', 'Personnel', '624', 'CHA');
INSERT INTO tmp_pcmn VALUES ('63', 'Amortissements, réductions de valeurs et provisions pour risques et charges', '6', 'CHA');
INSERT INTO tmp_pcmn VALUES ('630', 'Dotations aux amortissements et réduction de valeurs sur immobilisations', '63', 'CHA');
INSERT INTO tmp_pcmn VALUES ('6300', ' Dotations aux amortissements sur frais d''établissement', '630', 'CHA');
INSERT INTO tmp_pcmn VALUES ('705', 'Ventes et prestations de services', '70', 'PRO');
INSERT INTO tmp_pcmn VALUES ('414', 'Produits à recevoir', '41', 'ACT');
INSERT INTO tmp_pcmn VALUES ('416', 'Créances diverses', '41', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4160', 'Comptes de l''exploitant', '416', 'ACT');
INSERT INTO tmp_pcmn VALUES ('417', 'Créances douteuses', '41', 'ACT');
INSERT INTO tmp_pcmn VALUES ('418', 'Cautionnements versés en numéraires', '41', 'ACT');
INSERT INTO tmp_pcmn VALUES ('419', 'Réductions de valeur actées', '41', 'ACT');
INSERT INTO tmp_pcmn VALUES ('42', 'Dettes à plus dun an échéant dans l''année', '4', 'PAS');
INSERT INTO tmp_pcmn VALUES ('420', 'Emprunts subordonnés', '42', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4200', 'convertibles', '420', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4201', 'non convertibles', '420', 'PAS');
INSERT INTO tmp_pcmn VALUES ('421', 'Emprunts subordonnés', '42', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4210', 'convertibles', '420', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4211', 'non convertibles', '420', 'PAS');
INSERT INTO tmp_pcmn VALUES ('422', ' Dettes de locations financement', '42', 'PAS');
INSERT INTO tmp_pcmn VALUES ('423', ' Etablissement de crédit', '42', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4230', 'Dettes en comptes', '423', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4231', 'Promesses', '423', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4232', 'Crédits d''acceptation', '423', 'PAS');
INSERT INTO tmp_pcmn VALUES ('424', 'Autres emprunts', '42', 'PAS');
INSERT INTO tmp_pcmn VALUES ('425', 'Dettes commerciales', '42', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4250', 'Fournisseurs', '425', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4251', 'Effets à payer', '425', 'PAS');
INSERT INTO tmp_pcmn VALUES ('426', 'Acomptes reçus sur commandes', '42', 'PAS');
INSERT INTO tmp_pcmn VALUES ('428', 'Cautionnement reçus en numéraires', '42', 'PAS');
INSERT INTO tmp_pcmn VALUES ('429', 'Dettes diverses', '42', 'PAS');
INSERT INTO tmp_pcmn VALUES ('43', 'Dettes financières', '4', 'PAS');
INSERT INTO tmp_pcmn VALUES ('430', 'Etablissements de crédit - Emprunts à compte à terme fixe', '43', 'PAS');
INSERT INTO tmp_pcmn VALUES ('431', 'Etablissements de crédit - Promesses', '43', 'PAS');
INSERT INTO tmp_pcmn VALUES ('432', ' Etablissements de crédit - Crédits d''acceptation', '43', 'PAS');
INSERT INTO tmp_pcmn VALUES ('433', 'Etablissements de crédit -Dettes en comptes courant', '43', 'PAS');
INSERT INTO tmp_pcmn VALUES ('439', 'Autres emprunts', '43', 'PAS');
INSERT INTO tmp_pcmn VALUES ('44', 'Dettes commerciales', '4', 'PAS');
INSERT INTO tmp_pcmn VALUES ('440', 'Fournisseurs', '44', 'PAS');
INSERT INTO tmp_pcmn VALUES ('441', 'Effets à payer', '44', 'PAS');
INSERT INTO tmp_pcmn VALUES ('444', 'Factures à recevoir', '44', 'PAS');
INSERT INTO tmp_pcmn VALUES ('45', 'Dettes fiscales, salariales et sociales', '4', 'PAS');
INSERT INTO tmp_pcmn VALUES ('450', 'Dettes fiscales estimées', '45', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4500', 'Impôts belges sur le résultat', '450', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4501', 'Impôts belges sur le résultat', '450', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4502', 'Impôts belges sur le résultat', '450', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4503', 'Impôts belges sur le résultat', '450', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4504', 'Impôts belges sur le résultat', '450', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4505', 'Autres impôts et taxes belges', '450', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4506', 'Autres impôts et taxes belges', '450', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4507', 'Autres impôts et taxes belges', '450', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4508', 'Impôts et taxes étrangers', '450', 'PAS');
INSERT INTO tmp_pcmn VALUES ('451', 'TVA à payer', '45', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4511', 'TVA à payer 21%', '451', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4512', 'TVA à payer 12%', '451', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4513', 'TVA à payer 6%', '451', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4514', 'TVA à payer 0%', '451', 'PAS');
INSERT INTO tmp_pcmn VALUES ('452', 'Impôts et taxes à payer', '45', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4520', 'Impôts belges sur le résultat', '452', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4521', 'Impôts belges sur le résultat', '452', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4522', 'Impôts belges sur le résultat', '452', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4523', 'Impôts belges sur le résultat', '452', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4524', 'Impôts belges sur le résultat', '452', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4525', 'Autres impôts et taxes belges', '452', 'PAS');
INSERT INTO tmp_pcmn VALUES ('55000001', 'Caisse', '5500', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4526', 'Autres impôts et taxes belges', '452', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4527', 'Autres impôts et taxes belges', '452', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4528', 'Impôts et taxes étrangers', '452', 'PAS');
INSERT INTO tmp_pcmn VALUES ('453', 'Précomptes retenus', '45', 'PAS');
INSERT INTO tmp_pcmn VALUES ('454', 'Office National de la Sécurité Sociales', '45', 'PAS');
INSERT INTO tmp_pcmn VALUES ('455', 'Rémunérations', '45', 'PAS');
INSERT INTO tmp_pcmn VALUES ('456', 'Pécules de vacances', '45', 'PAS');
INSERT INTO tmp_pcmn VALUES ('459', 'Autres dettes sociales', '45', 'PAS');
INSERT INTO tmp_pcmn VALUES ('46', 'Acomptes reçus sur commandes', '4', 'PAS');
INSERT INTO tmp_pcmn VALUES ('47', 'Dettes découlant de l''affectation du résultat', '4', 'PAS');
INSERT INTO tmp_pcmn VALUES ('470', 'Dividendes et tantièmes d''exercices antérieurs', '47', 'PAS');
INSERT INTO tmp_pcmn VALUES ('471', 'Dividendes de l''exercice', '47', 'PAS');
INSERT INTO tmp_pcmn VALUES ('472', 'Tantièmes de l''exercice', '47', 'PAS');
INSERT INTO tmp_pcmn VALUES ('473', 'Autres allocataires', '47', 'PAS');
INSERT INTO tmp_pcmn VALUES ('48', 'Dettes diverses', '4', 'PAS');
INSERT INTO tmp_pcmn VALUES ('480', 'Obligations et coupons échus', '48', 'PAS');
INSERT INTO tmp_pcmn VALUES ('488', 'Cautionnements reçus en numéraires', '48', 'PAS');
INSERT INTO tmp_pcmn VALUES ('489', 'Autres dettes diverses', '48', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4890', 'Compte de l''exploitant', '489', 'PAS');
INSERT INTO tmp_pcmn VALUES ('49', 'Comptes de régularisation', '4', 'ACT');
INSERT INTO tmp_pcmn VALUES ('490', 'Charges à reporter', '49', 'ACT');
INSERT INTO tmp_pcmn VALUES ('491', 'Produits acquis', '49', 'ACT');
INSERT INTO tmp_pcmn VALUES ('492', 'Charges à imputer', '49', 'PAS');
INSERT INTO tmp_pcmn VALUES ('493', 'Produits à reporter', '49', 'PAS');
INSERT INTO tmp_pcmn VALUES ('499', 'Comptes d''attentes', '49', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2821', 'Montants non-appelés(-)', '282', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2828', 'Plus-values actées', '282', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2829', 'Réductions de valeurs actées', '282', 'ACT');
INSERT INTO tmp_pcmn VALUES ('283', 'Créances sur des entreprises avec lesquelles existe un lien de participation', '28', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2830', 'Créance en compte', '283', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2831', 'Effets à recevoir', '283', 'ACT');
INSERT INTO tmp_pcmn VALUES ('57', 'Caisse', '5', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2832', 'Titre à revenu fixe', '283', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2837', 'Créances douteuses', '283', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2839', 'Réduction de valeurs actées', '283', 'ACT');
INSERT INTO tmp_pcmn VALUES ('284', 'Autres actions et parts', '28', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2840', 'Valeur d''acquisition', '284', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2841', 'Montants non-appelés(-)', '284', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2848', 'Plus-values actées', '284', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2849', 'Réductions de valeurs actées', '284', 'ACT');
INSERT INTO tmp_pcmn VALUES ('285', 'Autres créances', '28', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2850', 'Créance en compte', '285', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2851', 'Effets à recevoir', '285', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2852', 'Titre à revenu fixe', '285', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2857', 'Créances douteuses', '285', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2859', 'Réductions de valeurs actées', '285', 'ACT');
INSERT INTO tmp_pcmn VALUES ('288', 'Cautionnements versés en numéraires', '28', 'ACT');
INSERT INTO tmp_pcmn VALUES ('29', 'Créances à plus d''un an', '2', 'ACT');
INSERT INTO tmp_pcmn VALUES ('290', 'Créances commerciales', '29', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2900', 'Clients', '290', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2901', 'Effets à recevoir', '290', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2906', 'Acomptes versés', '290', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2907', 'Créances douteuses', '290', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2909', 'Réductions de valeurs actées', '290', 'ACT');
INSERT INTO tmp_pcmn VALUES ('291', 'Autres créances', '29', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2910', 'Créances en comptes', '291', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2911', 'Effets à recevoir', '291', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2917', 'Créances douteuses', '291', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2919', 'Réductions de valeurs actées(-)', '291', 'ACT');
INSERT INTO tmp_pcmn VALUES ('30', 'Approvisionements - Matières premières', '3', 'ACT');
INSERT INTO tmp_pcmn VALUES ('300', 'Valeur d''acquisition', '30', 'ACT');
INSERT INTO tmp_pcmn VALUES ('309', 'Réductions de valeur actées', '30', 'ACT');
INSERT INTO tmp_pcmn VALUES ('31', 'Approvisionnements - fournitures', '3', 'ACT');
INSERT INTO tmp_pcmn VALUES ('310', 'Valeur d''acquisition', '31', 'ACT');
INSERT INTO tmp_pcmn VALUES ('319', 'Réductions de valeurs actées(-)', '31', 'ACT');
INSERT INTO tmp_pcmn VALUES ('32', 'En-cours de fabrication', '3', 'ACT');
INSERT INTO tmp_pcmn VALUES ('320', 'Valeurs d''acquisition', '32', 'ACT');
INSERT INTO tmp_pcmn VALUES ('329', 'Réductions de valeur actées', '32', 'ACT');
INSERT INTO tmp_pcmn VALUES ('33', 'Produits finis', '3', 'ACT');
INSERT INTO tmp_pcmn VALUES ('330', 'Valeur d''acquisition', '33', 'ACT');
INSERT INTO tmp_pcmn VALUES ('339', 'Réductions de valeur actées', '33', 'ACT');
INSERT INTO tmp_pcmn VALUES ('34', 'Marchandises', '3', 'ACT');
INSERT INTO tmp_pcmn VALUES ('340', 'Valeur d''acquisition', '34', 'ACT');
INSERT INTO tmp_pcmn VALUES ('349', 'Réductions de valeur actées', '34', 'ACT');
INSERT INTO tmp_pcmn VALUES ('35', 'Immeubles destinés à la vente', '3', 'ACT');
INSERT INTO tmp_pcmn VALUES ('350', 'Valeur d''acquisition', '35', 'ACT');
INSERT INTO tmp_pcmn VALUES ('359', 'Réductions de valeur actées', '35', 'ACT');
INSERT INTO tmp_pcmn VALUES ('36', 'Acomptes versés sur achats pour stocks', '3', 'ACT');
INSERT INTO tmp_pcmn VALUES ('360', 'Valeur d''acquisition', '36', 'ACT');
INSERT INTO tmp_pcmn VALUES ('369', 'Réductions de valeur actées', '36', 'ACT');
INSERT INTO tmp_pcmn VALUES ('37', 'Commandes en cours éxécution', '3', 'ACT');
INSERT INTO tmp_pcmn VALUES ('370', 'Valeur d''acquisition', '37', 'ACT');
INSERT INTO tmp_pcmn VALUES ('371', 'Bénéfice pris en compte ', '37', 'ACT');
INSERT INTO tmp_pcmn VALUES ('379', 'Réductions de valeur actées', '37', 'ACT');
INSERT INTO tmp_pcmn VALUES ('40', 'Créances commerciales', '4', 'ACT');
INSERT INTO tmp_pcmn VALUES ('400', 'Clients', '40', 'ACT');
INSERT INTO tmp_pcmn VALUES ('401', 'Effets à recevoir', '40', 'ACT');
INSERT INTO tmp_pcmn VALUES ('404', 'Produits à recevoir', '40', 'ACT');
INSERT INTO tmp_pcmn VALUES ('406', 'Acomptes versés', '40', 'ACT');
INSERT INTO tmp_pcmn VALUES ('407', 'Créances douteuses', '40', 'ACT');
INSERT INTO tmp_pcmn VALUES ('409', 'Réductions de valeur actées', '40', 'ACT');
INSERT INTO tmp_pcmn VALUES ('41', 'Autres créances', '4', 'ACT');
INSERT INTO tmp_pcmn VALUES ('410', 'Capital appelé non versé', '41', 'ACT');
INSERT INTO tmp_pcmn VALUES ('411', 'TVA à récupérer', '41', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4111', 'TVA à récupérer 21%', '411', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4112', 'TVA à récupérer 12%', '411', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4113', 'TVA à récupérer 6% ', '411', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4114', 'TVA à récupérer 0%', '411', 'ACT');
INSERT INTO tmp_pcmn VALUES ('412', 'Impôts et précomptes à récupérer', '41', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4120', 'Impôt belge sur le résultat', '412', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4121', 'Impôt belge sur le résultat', '412', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4122', 'Impôt belge sur le résultat', '412', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4123', 'Impôt belge sur le résultat', '412', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4124', 'Impôt belge sur le résultat', '412', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4125', 'Autres impôts et taxes belges', '412', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4126', 'Autres impôts et taxes belges', '412', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4127', 'Autres impôts et taxes belges', '412', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4128', 'Impôts et taxes étrangers', '412', 'ACT');
INSERT INTO tmp_pcmn VALUES ('10', 'Capital ', '1', 'PAS');
INSERT INTO tmp_pcmn VALUES ('6040003', 'Petit matériel', '604', 'CHA');
INSERT INTO tmp_pcmn VALUES ('11', 'Prime d''émission ', '1', 'PAS');
INSERT INTO tmp_pcmn VALUES ('12', 'Plus Value de réévaluation ', '1', 'PAS');
INSERT INTO tmp_pcmn VALUES ('13', 'Réserve ', '1', 'PAS');
INSERT INTO tmp_pcmn VALUES ('130', 'Réserve légale', '13', 'PAS');
INSERT INTO tmp_pcmn VALUES ('131', 'Réserve indisponible', '13', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1310', 'Réserve pour actions propres', '131', 'PAS');
INSERT INTO tmp_pcmn VALUES ('6040004', 'Assurance', '604', 'CHA');
INSERT INTO tmp_pcmn VALUES ('133', 'Réserves disponibles', '13', 'PAS');
INSERT INTO tmp_pcmn VALUES ('14', 'Bénéfice ou perte reportée', '1', 'PAS');
INSERT INTO tmp_pcmn VALUES ('140', 'Bénéfice reporté', '14', 'PAS');
INSERT INTO tmp_pcmn VALUES ('141', 'Perte reportée', '14', 'PASINV');
INSERT INTO tmp_pcmn VALUES ('15', 'Subside en capital', '1', 'PAS');
INSERT INTO tmp_pcmn VALUES ('16', 'Provisions pour risques et charges', '1', 'PAS');
INSERT INTO tmp_pcmn VALUES ('160', 'Provisions pour pensions et obligations similaires', '16', 'PAS');
INSERT INTO tmp_pcmn VALUES ('161', 'Provisions pour charges fiscales', '16', 'PAS');
INSERT INTO tmp_pcmn VALUES ('749', 'Produits d''exploitations divers', '74', 'PRO');
INSERT INTO tmp_pcmn VALUES ('162', 'Provisions pour grosses réparation et gros entretien', '16', 'PAS');
INSERT INTO tmp_pcmn VALUES ('17', ' Dettes à plus d''un an', '1', 'PAS');
INSERT INTO tmp_pcmn VALUES ('170', 'Emprunts subordonnés', '17', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1700', 'convertibles', '170', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1701', 'non convertibles', '170', 'PAS');
INSERT INTO tmp_pcmn VALUES ('171', 'Emprunts subordonnés', '17', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1710', 'convertibles', '170', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1711', 'non convertibles', '170', 'PAS');
INSERT INTO tmp_pcmn VALUES ('172', ' Dettes de locations financement', '17', 'PAS');
INSERT INTO tmp_pcmn VALUES ('173', ' Etablissement de crédit', '17', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1730', 'Dettes en comptes', '173', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1731', 'Promesses', '173', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1732', 'Crédits d''acceptation', '173', 'PAS');
INSERT INTO tmp_pcmn VALUES ('174', 'Autres emprunts', '17', 'PAS');
INSERT INTO tmp_pcmn VALUES ('175', 'Dettes commerciales', '17', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1750', 'Fournisseurs', '175', 'PAS');
INSERT INTO tmp_pcmn VALUES ('1751', 'Effets à payer', '175', 'PAS');
INSERT INTO tmp_pcmn VALUES ('176', 'Acomptes reçus sur commandes', '17', 'PAS');
INSERT INTO tmp_pcmn VALUES ('178', 'Cautionnement reçus en numéraires', '17', 'PAS');
INSERT INTO tmp_pcmn VALUES ('179', 'Dettes diverses', '17', 'PAS');
INSERT INTO tmp_pcmn VALUES ('20', 'Frais d''établissement', '2', 'ACT');
INSERT INTO tmp_pcmn VALUES ('200', 'Frais de constitution et d''augmentation de capital', '20', 'ACT');
INSERT INTO tmp_pcmn VALUES ('201', ' Frais d''émission d''emprunts et primes de remboursement', '20', 'ACT');
INSERT INTO tmp_pcmn VALUES ('202', 'Autres frais d''établissement', '20', 'ACT');
INSERT INTO tmp_pcmn VALUES ('204', 'Frais de restructuration', '20', 'ACT');
INSERT INTO tmp_pcmn VALUES ('21', 'Immobilisations incorporelles', '2', 'ACT');
INSERT INTO tmp_pcmn VALUES ('210', 'Frais de recherche et de développement', '21', 'ACT');
INSERT INTO tmp_pcmn VALUES ('211', 'Concessions, brevet, licence savoir faire, marque et droit similaires', '21', 'ACT');
INSERT INTO tmp_pcmn VALUES ('212', 'Goodwill', '21', 'ACT');
INSERT INTO tmp_pcmn VALUES ('213', 'Acomptes versés', '21', 'ACT');
INSERT INTO tmp_pcmn VALUES ('22', 'Terrains et construction', '2', 'ACT');
INSERT INTO tmp_pcmn VALUES ('220', 'Terrains', '22', 'ACT');
INSERT INTO tmp_pcmn VALUES ('222', 'Terrains bâtis', '22', 'ACT');
INSERT INTO tmp_pcmn VALUES ('223', 'Autres droits réels sur des immeubles', '22', 'ACT');
INSERT INTO tmp_pcmn VALUES ('23', ' Installations, machines et outillages', '2', 'ACT');
INSERT INTO tmp_pcmn VALUES ('24', 'Mobilier et Matériel roulant', '2', 'ACT');
INSERT INTO tmp_pcmn VALUES ('25', 'Immobilisations détenus en location-financement et droits similaires', '2', 'ACT');
INSERT INTO tmp_pcmn VALUES ('250', 'Terrains', '25', 'ACT');
INSERT INTO tmp_pcmn VALUES ('251', 'Construction', '25', 'ACT');
INSERT INTO tmp_pcmn VALUES ('252', 'Terrains bâtis', '25', 'ACT');
INSERT INTO tmp_pcmn VALUES ('253', 'Mobilier et matériels roulants', '25', 'ACT');
INSERT INTO tmp_pcmn VALUES ('26', 'Autres immobilisations corporelles', '2', 'ACT');
INSERT INTO tmp_pcmn VALUES ('27', 'Immobilisations corporelles en cours et acomptes versés', '2', 'ACT');
INSERT INTO tmp_pcmn VALUES ('28', 'Immobilisations financières', '2', 'ACT');
INSERT INTO tmp_pcmn VALUES ('280', 'Participation dans des entreprises liées', '28', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2800', 'Valeur d''acquisition', '280', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2801', 'Montants non-appelés(-)', '280', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2808', 'Plus-values actées', '280', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2809', 'Réductions de valeurs actées', '280', 'ACT');
INSERT INTO tmp_pcmn VALUES ('281', 'Créance sur  des entreprises liées', '28', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2810', 'Créance en compte', '281', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2811', 'Effets à recevoir', '281', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2812', 'Titre à reveny fixe', '281', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2817', 'Créances douteuses', '281', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2819', 'Réduction de valeurs actées', '281', 'ACT');
INSERT INTO tmp_pcmn VALUES ('282', 'Participations dans des entreprises avec lesquelles il existe un lien de participation', '28', 'ACT');
INSERT INTO tmp_pcmn VALUES ('2820', 'Valeur d''acquisition', '282', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4516', 'Tva Export 0%', '451', 'PAS');
INSERT INTO tmp_pcmn VALUES ('4115', 'Tva Intracomm 0%', '411', 'ACT');
INSERT INTO tmp_pcmn VALUES ('4116', 'Tva Export 0%', '411', 'ACT');
INSERT INTO tmp_pcmn VALUES ('41141', 'TVA pour l\\''export', '4114', 'ACT');
INSERT INTO tmp_pcmn VALUES ('41142', 'TVA sur les opérations intracommunautaires', '4114', 'ACT');
INSERT INTO tmp_pcmn VALUES ('45141', 'TVA pour l\\''export', '451', 'PAS');
INSERT INTO tmp_pcmn VALUES ('45142', 'TVA sur les opérations intracommunautaires', '4514', 'PAS');
INSERT INTO tmp_pcmn VALUES ('41143', 'TVA sur les opérations avec des assujettis art 44 Code TVA', '4114', 'ACT');
INSERT INTO tmp_pcmn VALUES ('45143', 'TVA sur les opérations avec des assujettis art 44 Code TVA', '4514', 'PAS');
INSERT INTO tmp_pcmn VALUES ('41144', 'TVA sur les opérations avec des cocontractants', '4114', 'ACT');
INSERT INTO tmp_pcmn VALUES ('45144', 'TVA sur les opérations avec des cocontractants', '4514', 'PAS');


--
-- Data for Name: todo_list; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: tva_rate; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO tva_rate VALUES (1, '21%', 0.2100, 'Tva applicable à tout ce qui bien et service divers', '4111,4511', 0);
INSERT INTO tva_rate VALUES (2, '12%', 0.1200, 'Tva ', '4112,4512', 0);
INSERT INTO tva_rate VALUES (3, '6%', 0.0600, 'Tva applicable aux journaux et livres', '4113,4513', 0);
INSERT INTO tva_rate VALUES (4, '0%', 0.0000, 'Aucune tva n''est applicable', '4114,4514', 0);
INSERT INTO tva_rate VALUES (6, 'EXPORT', 0.0000, 'Tva pour les exportations', '41141,45144', 0);
INSERT INTO tva_rate VALUES (5, 'INTRA', 0.0000, 'Tva pour les livraisons / acquisition intra communautaires', '41142,45142', 0);
INSERT INTO tva_rate VALUES (7, 'COC', 0.0000, 'Opérations avec des cocontractants', '41144,45144', 0);
INSERT INTO tva_rate VALUES (8, 'ART44', 0.0000, 'Opérations pour les opérations avec des assujettis à l\\''art 44 Code TVA', '41143,45143', 0);


--
-- Data for Name: user_local_pref; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO user_local_pref VALUES ('1', 'MINIREPORT', '0');
INSERT INTO user_local_pref VALUES ('1', 'PERIODE', '79');


--
-- Data for Name: user_sec_act; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: user_sec_extension; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO user_sec_extension VALUES (1, 1, 'phpcompta', 'Y');


--
-- Data for Name: user_sec_jrn; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: version; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO version VALUES (99);


--
-- Name: action_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY action_detail
    ADD CONSTRAINT action_detail_pkey PRIMARY KEY (ad_id);


--
-- Name: action_gestion_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY action_gestion
    ADD CONSTRAINT action_gestion_pkey PRIMARY KEY (ag_id);


--
-- Name: action_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY action
    ADD CONSTRAINT action_pkey PRIMARY KEY (ac_id);


--
-- Name: attr_def_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY attr_def
    ADD CONSTRAINT attr_def_pkey PRIMARY KEY (ad_id);


--
-- Name: bilan_b_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY bilan
    ADD CONSTRAINT bilan_b_name_key UNIQUE (b_name);


--
-- Name: bilan_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY bilan
    ADD CONSTRAINT bilan_pkey PRIMARY KEY (b_id);


--
-- Name: centralized_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY centralized
    ADD CONSTRAINT centralized_pkey PRIMARY KEY (c_id);


--
-- Name: del_action_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY del_action
    ADD CONSTRAINT del_action_pkey PRIMARY KEY (del_id);


--
-- Name: dj_id; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY del_jrn
    ADD CONSTRAINT dj_id PRIMARY KEY (dj_id);


--
-- Name: djx_id; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY del_jrnx
    ADD CONSTRAINT djx_id PRIMARY KEY (djx_id);


--
-- Name: document_modele_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY document_modele
    ADD CONSTRAINT document_modele_pkey PRIMARY KEY (md_id);


--
-- Name: document_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY document
    ADD CONSTRAINT document_pkey PRIMARY KEY (d_id);


--
-- Name: document_state_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY document_state
    ADD CONSTRAINT document_state_pkey PRIMARY KEY (s_id);


--
-- Name: document_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY document_type
    ADD CONSTRAINT document_type_pkey PRIMARY KEY (dt_id);


--
-- Name: fiche_def_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fiche_def
    ADD CONSTRAINT fiche_def_pkey PRIMARY KEY (fd_id);


--
-- Name: fiche_def_ref_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fiche_def_ref
    ADD CONSTRAINT fiche_def_ref_pkey PRIMARY KEY (frd_id);


--
-- Name: fiche_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fiche
    ADD CONSTRAINT fiche_pkey PRIMARY KEY (f_id);


--
-- Name: forecast_cat_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY forecast_cat
    ADD CONSTRAINT forecast_cat_pk PRIMARY KEY (fc_id);


--
-- Name: forecast_item_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY forecast_item
    ADD CONSTRAINT forecast_item_pkey PRIMARY KEY (fi_id);


--
-- Name: forecast_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY forecast
    ADD CONSTRAINT forecast_pk PRIMARY KEY (f_id);


--
-- Name: form_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY form
    ADD CONSTRAINT form_pkey PRIMARY KEY (fo_id);


--
-- Name: formdef_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY formdef
    ADD CONSTRAINT formdef_pkey PRIMARY KEY (fr_id);


--
-- Name: frd_ad_attr_min_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY attr_min
    ADD CONSTRAINT frd_ad_attr_min_pk PRIMARY KEY (frd_id, ad_id);


--
-- Name: historique_analytique_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT historique_analytique_pkey PRIMARY KEY (oa_id);


--
-- Name: idx_ex_code; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY extension
    ADD CONSTRAINT idx_ex_code UNIQUE (ex_code);


--
-- Name: info_def_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY info_def
    ADD CONSTRAINT info_def_pkey PRIMARY KEY (id_type);


--
-- Name: jnt_fic_att_value_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY fiche_detail
    ADD CONSTRAINT jnt_fic_att_value_pkey PRIMARY KEY (jft_id);


--
-- Name: jnt_letter_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jnt_letter
    ADD CONSTRAINT jnt_letter_pk PRIMARY KEY (jl_id);


--
-- Name: jrn_action_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrn_action
    ADD CONSTRAINT jrn_action_pkey PRIMARY KEY (ja_id);


--
-- Name: jrn_def_jrn_def_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrn_def
    ADD CONSTRAINT jrn_def_jrn_def_name_key UNIQUE (jrn_def_name);


--
-- Name: jrn_def_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrn_def
    ADD CONSTRAINT jrn_def_pkey PRIMARY KEY (jrn_def_id);


--
-- Name: jrn_info_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrn_info
    ADD CONSTRAINT jrn_info_pkey PRIMARY KEY (ji_id);


--
-- Name: jrn_periode_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrn_periode
    ADD CONSTRAINT jrn_periode_pk PRIMARY KEY (jrn_def_id, p_id);


--
-- Name: jrn_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrn
    ADD CONSTRAINT jrn_pkey PRIMARY KEY (jr_id, jr_def_id);


--
-- Name: jrn_rapt_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrn_rapt
    ADD CONSTRAINT jrn_rapt_pkey PRIMARY KEY (jra_id);


--
-- Name: jrn_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrn_type
    ADD CONSTRAINT jrn_type_pkey PRIMARY KEY (jrn_type_id);


--
-- Name: jrnx_note_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrn_note
    ADD CONSTRAINT jrnx_note_pkey PRIMARY KEY (n_id);


--
-- Name: jrnx_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrnx
    ADD CONSTRAINT jrnx_pkey PRIMARY KEY (j_id);


--
-- Name: letter_cred_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY letter_cred
    ADD CONSTRAINT letter_cred_pk PRIMARY KEY (lc_id);


--
-- Name: letter_deb_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY letter_deb
    ADD CONSTRAINT letter_deb_pk PRIMARY KEY (ld_id);


--
-- Name: menu_ref_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY menu_ref
    ADD CONSTRAINT menu_ref_pkey PRIMARY KEY (me_code);


--
-- Name: mod_payment_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_pkey PRIMARY KEY (mp_id);


--
-- Name: op_def_op_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY op_predef
    ADD CONSTRAINT op_def_op_name_key UNIQUE (od_name, jrn_def_id);


--
-- Name: op_def_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY op_predef
    ADD CONSTRAINT op_def_pkey PRIMARY KEY (od_id);


--
-- Name: op_predef_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY op_predef_detail
    ADD CONSTRAINT op_predef_detail_pkey PRIMARY KEY (opd_id);


--
-- Name: parameter_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY parameter
    ADD CONSTRAINT parameter_pkey PRIMARY KEY (pr_id);


--
-- Name: parm_code_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY parm_code
    ADD CONSTRAINT parm_code_pkey PRIMARY KEY (p_code);


--
-- Name: parm_money_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY parm_money
    ADD CONSTRAINT parm_money_pkey PRIMARY KEY (pm_code);


--
-- Name: parm_periode_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY parm_periode
    ADD CONSTRAINT parm_periode_pkey PRIMARY KEY (p_id);


--
-- Name: parm_poste_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY parm_poste
    ADD CONSTRAINT parm_poste_pkey PRIMARY KEY (p_value);


--
-- Name: pk_extension; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY extension
    ADD CONSTRAINT pk_extension PRIMARY KEY (ex_id);


--
-- Name: pk_ga_id; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY groupe_analytique
    ADD CONSTRAINT pk_ga_id PRIMARY KEY (ga_id);


--
-- Name: pk_jnt_fic_attr; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jnt_fic_attr
    ADD CONSTRAINT pk_jnt_fic_attr PRIMARY KEY (jnt_id);


--
-- Name: pk_user_local_pref; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_local_pref
    ADD CONSTRAINT pk_user_local_pref PRIMARY KEY (user_id, parameter_type);


--
-- Name: plan_analytique_pa_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY plan_analytique
    ADD CONSTRAINT plan_analytique_pa_name_key UNIQUE (pa_name);


--
-- Name: plan_analytique_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY plan_analytique
    ADD CONSTRAINT plan_analytique_pkey PRIMARY KEY (pa_id);


--
-- Name: poste_analytique_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY poste_analytique
    ADD CONSTRAINT poste_analytique_pkey PRIMARY KEY (po_id);


--
-- Name: profile_menu_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY profile_menu
    ADD CONSTRAINT profile_menu_pkey PRIMARY KEY (pm_id);


--
-- Name: profile_menu_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY profile_menu_type
    ADD CONSTRAINT profile_menu_type_pkey PRIMARY KEY (pm_type);


--
-- Name: profile_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY profile
    ADD CONSTRAINT profile_pkey PRIMARY KEY (p_id);


--
-- Name: profile_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY profile_user
    ADD CONSTRAINT profile_user_pkey PRIMARY KEY (pu_id);


--
-- Name: profile_user_user_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY profile_user
    ADD CONSTRAINT profile_user_user_name_key UNIQUE (user_name, p_id);


--
-- Name: qp_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT qp_id_pk PRIMARY KEY (qp_id);


--
-- Name: qs_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT qs_id_pk PRIMARY KEY (qs_id);


--
-- Name: quant_fin_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY quant_fin
    ADD CONSTRAINT quant_fin_pk PRIMARY KEY (qf_id);


--
-- Name: stock_goods_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY stock_goods
    ADD CONSTRAINT stock_goods_pkey PRIMARY KEY (sg_id);


--
-- Name: tmp_pcmn_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tmp_pcmn
    ADD CONSTRAINT tmp_pcmn_pkey PRIMARY KEY (pcm_val);


--
-- Name: todo_list_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY todo_list
    ADD CONSTRAINT todo_list_pkey PRIMARY KEY (tl_id);


--
-- Name: tva_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tva_rate
    ADD CONSTRAINT tva_id_pk PRIMARY KEY (tva_id);


--
-- Name: user_sec_act_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_sec_act
    ADD CONSTRAINT user_sec_act_pkey PRIMARY KEY (ua_id);


--
-- Name: user_sec_extension_ex_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_sec_extension
    ADD CONSTRAINT user_sec_extension_ex_id_key UNIQUE (ex_id, use_login);


--
-- Name: user_sec_extension_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_sec_extension
    ADD CONSTRAINT user_sec_extension_pkey PRIMARY KEY (use_id);


--
-- Name: user_sec_jrn_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_sec_jrn
    ADD CONSTRAINT user_sec_jrn_pkey PRIMARY KEY (uj_id);


--
-- Name: ux_internal; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jrn
    ADD CONSTRAINT ux_internal UNIQUE (jr_internal);


--
-- Name: fd_id_ad_id_x; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX fd_id_ad_id_x ON jnt_fic_attr USING btree (fd_id, ad_id);


--
-- Name: fiche_detail_f_id_ad_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX fiche_detail_f_id_ad_id ON fiche_detail USING btree (f_id, ad_id);


--
-- Name: fk_stock_goods_f_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fk_stock_goods_f_id ON stock_goods USING btree (f_id);


--
-- Name: fk_stock_goods_j_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fk_stock_goods_j_id ON stock_goods USING btree (j_id);


--
-- Name: fki_f_end_date; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_f_end_date ON forecast USING btree (f_end_date);


--
-- Name: fki_f_start_date; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_f_start_date ON forecast USING btree (f_start_date);


--
-- Name: fki_jrnx_f_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_jrnx_f_id ON jrnx USING btree (f_id);


--
-- Name: fki_profile_menu_me_code; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_profile_menu_me_code ON profile_menu USING btree (me_code);


--
-- Name: fki_profile_menu_profile; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_profile_menu_profile ON profile_menu USING btree (p_id);


--
-- Name: fki_profile_menu_type_fkey; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_profile_menu_type_fkey ON profile_menu USING btree (p_type_display);


--
-- Name: idx_qs_internal; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX idx_qs_internal ON quant_sold USING btree (qs_internal);


--
-- Name: jnt_fic_att_value_fd_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX jnt_fic_att_value_fd_id_idx ON fiche_detail USING btree (f_id);


--
-- Name: jnt_fic_attr_fd_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX jnt_fic_attr_fd_id_idx ON jnt_fic_attr USING btree (fd_id);


--
-- Name: k_ag_ref; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX k_ag_ref ON action_gestion USING btree (ag_ref);


--
-- Name: qcode_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX qcode_idx ON fiche_detail USING btree (ad_value) WHERE (ad_id = 23);


--
-- Name: qf_jr_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX qf_jr_id ON quant_fin USING btree (jr_id);


--
-- Name: qp_j_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX qp_j_id ON quant_purchase USING btree (j_id);


--
-- Name: qs_j_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX qs_j_id ON quant_sold USING btree (j_id);


--
-- Name: uj_login_uj_jrn_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX uj_login_uj_jrn_id ON user_sec_jrn USING btree (uj_login, uj_jrn_id);


--
-- Name: ux_po_name; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX ux_po_name ON poste_analytique USING btree (po_name);


--
-- Name: x_jrn_jr_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX x_jrn_jr_id ON jrn USING btree (jr_id);


--
-- Name: x_mt; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX x_mt ON jrn USING btree (jr_mt);


--
-- Name: x_periode; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX x_periode ON parm_periode USING btree (p_start, p_end);


--
-- Name: x_poste; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX x_poste ON jrnx USING btree (j_poste);


--
-- Name: action_gestion_t_insert_update; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER action_gestion_t_insert_update
    BEFORE INSERT OR UPDATE ON action_gestion
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.action_gestion_ins_upd();


--
-- Name: TRIGGER action_gestion_t_insert_update ON action_gestion; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TRIGGER action_gestion_t_insert_update ON action_gestion IS 'Truncate the column ag_title to 70 char';


--
-- Name: document_modele_validate; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER document_modele_validate
    BEFORE INSERT OR UPDATE ON document_modele
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.t_document_modele_validate();


--
-- Name: document_validate; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER document_validate
    BEFORE INSERT OR UPDATE ON document
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.t_document_validate();


--
-- Name: fiche_def_ins_upd; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER fiche_def_ins_upd
    BEFORE INSERT OR UPDATE ON fiche_def
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.fiche_def_ins_upd();


--
-- Name: info_def_ins_upd_t; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER info_def_ins_upd_t
    BEFORE INSERT OR UPDATE ON info_def
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.info_def_ins_upd();


--
-- Name: quant_sold_ins_upd_tr; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER quant_sold_ins_upd_tr
    AFTER INSERT OR UPDATE ON quant_purchase
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.quant_purchase_ins_upd();


--
-- Name: quant_sold_ins_upd_tr; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER quant_sold_ins_upd_tr
    AFTER INSERT OR UPDATE ON quant_sold
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.quant_sold_ins_upd();


--
-- Name: remove_action_gestion; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER remove_action_gestion
    AFTER DELETE ON fiche
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.card_after_delete();


--
-- Name: t_check_balance; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_check_balance
    AFTER INSERT OR UPDATE ON jrn
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.proc_check_balance();


--
-- Name: t_check_jrn; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_check_jrn
    BEFORE INSERT OR DELETE OR UPDATE ON jrn
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrn_check_periode();


--
-- Name: t_group_analytic_del; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_group_analytic_del
    BEFORE DELETE ON groupe_analytique
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.group_analytique_del();


--
-- Name: t_group_analytic_ins_upd; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_group_analytic_ins_upd
    BEFORE INSERT OR UPDATE ON groupe_analytique
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.group_analytic_ins_upd();


--
-- Name: t_jnt_fic_attr_ins; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_jnt_fic_attr_ins
    AFTER INSERT ON jnt_fic_attr
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jnt_fic_attr_ins();


--
-- Name: t_jrn_def_add_periode; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_jrn_def_add_periode
    AFTER INSERT ON jrn_def
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrn_def_add();


--
-- Name: t_jrn_def_delete; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_jrn_def_delete
    BEFORE DELETE ON jrn_def
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrn_def_delete();


--
-- Name: t_jrn_del; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_jrn_del
    BEFORE DELETE ON jrn
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrn_del();


--
-- Name: t_jrnx_del; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_jrnx_del
    BEFORE DELETE ON jrnx
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrnx_del();


--
-- Name: t_jrnx_ins; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_jrnx_ins
    BEFORE INSERT ON jrnx
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrnx_ins();


--
-- Name: TRIGGER t_jrnx_ins ON jrnx; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TRIGGER t_jrnx_ins ON jrnx IS 'check that the qcode used by the card exists and format it : uppercase and trim the space';


--
-- Name: t_jrnx_upd; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_jrnx_upd
    BEFORE UPDATE ON jrnx
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrnx_ins();


--
-- Name: t_letter_del; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_letter_del
    AFTER DELETE ON jrnx
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrnx_letter_del();


--
-- Name: TRIGGER t_letter_del ON jrnx; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TRIGGER t_letter_del ON jrnx IS 'Delete the lettering for this row';


--
-- Name: t_plan_analytique_ins_upd; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_plan_analytique_ins_upd
    BEFORE INSERT OR UPDATE ON plan_analytique
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.plan_analytic_ins_upd();


--
-- Name: t_poste_analytique_ins_upd; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_poste_analytique_ins_upd
    BEFORE INSERT OR UPDATE ON poste_analytique
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.poste_analytique_ins_upd();


--
-- Name: t_tmp_pcm_alphanum_ins_upd; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_tmp_pcm_alphanum_ins_upd
    BEFORE INSERT OR UPDATE ON tmp_pcmn
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.tmp_pcmn_alphanum_ins_upd();


--
-- Name: t_tmp_pcmn_ins; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER t_tmp_pcmn_ins
    BEFORE INSERT ON tmp_pcmn
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.tmp_pcmn_ins();


--
-- Name: trg_extension_ins_upd; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trg_extension_ins_upd
    BEFORE INSERT OR UPDATE ON extension
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.extension_ins_upd();


--
-- Name: trigger_document_type_i; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trigger_document_type_i
    AFTER INSERT ON document_type
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.t_document_type_insert();


--
-- Name: trigger_jrn_def_sequence_i; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER trigger_jrn_def_sequence_i
    AFTER INSERT ON jrn_def
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.t_jrn_def_sequence();


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY centralized
    ADD CONSTRAINT "$1" FOREIGN KEY (c_jrn_def) REFERENCES jrn_def(jrn_def_id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_sec_act
    ADD CONSTRAINT "$1" FOREIGN KEY (ua_act_id) REFERENCES action(ac_id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY fiche_def
    ADD CONSTRAINT "$1" FOREIGN KEY (frd_id) REFERENCES fiche_def_ref(frd_id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY attr_min
    ADD CONSTRAINT "$1" FOREIGN KEY (frd_id) REFERENCES fiche_def_ref(frd_id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY fiche
    ADD CONSTRAINT "$1" FOREIGN KEY (fd_id) REFERENCES fiche_def(fd_id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY fiche_detail
    ADD CONSTRAINT "$1" FOREIGN KEY (f_id) REFERENCES fiche(f_id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jnt_fic_attr
    ADD CONSTRAINT "$1" FOREIGN KEY (fd_id) REFERENCES fiche_def(fd_id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn
    ADD CONSTRAINT "$1" FOREIGN KEY (jr_def_id) REFERENCES jrn_def(jrn_def_id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_action
    ADD CONSTRAINT "$1" FOREIGN KEY (ja_jrn_type) REFERENCES jrn_type(jrn_type_id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_def
    ADD CONSTRAINT "$1" FOREIGN KEY (jrn_def_type) REFERENCES jrn_type(jrn_type_id);


--
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrnx
    ADD CONSTRAINT "$2" FOREIGN KEY (j_jrn_def) REFERENCES jrn_def(jrn_def_id);


--
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY attr_min
    ADD CONSTRAINT "$2" FOREIGN KEY (ad_id) REFERENCES attr_def(ad_id);


--
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY fiche_detail
    ADD CONSTRAINT "$2" FOREIGN KEY (ad_id) REFERENCES attr_def(ad_id);


--
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jnt_fic_attr
    ADD CONSTRAINT "$2" FOREIGN KEY (ad_id) REFERENCES attr_def(ad_id);


--
-- Name: action_detail_ag_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY action_detail
    ADD CONSTRAINT action_detail_ag_id_fkey FOREIGN KEY (ag_id) REFERENCES action_gestion(ag_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: card; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY forecast_item
    ADD CONSTRAINT card FOREIGN KEY (fi_card) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_card; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY quant_fin
    ADD CONSTRAINT fk_card FOREIGN KEY (qf_bank) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_card_other; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY quant_fin
    ADD CONSTRAINT fk_card_other FOREIGN KEY (qf_other) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_forecast; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY forecast_item
    ADD CONSTRAINT fk_forecast FOREIGN KEY (fc_id) REFERENCES forecast_cat(fc_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_info_def; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_info
    ADD CONSTRAINT fk_info_def FOREIGN KEY (id_type) REFERENCES info_def(id_type) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_jrn; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_info
    ADD CONSTRAINT fk_jrn FOREIGN KEY (jr_id) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_jrn; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY quant_fin
    ADD CONSTRAINT fk_jrn FOREIGN KEY (jr_id) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_pa_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY groupe_analytique
    ADD CONSTRAINT fk_pa_id FOREIGN KEY (pa_id) REFERENCES plan_analytique(pa_id) ON DELETE CASCADE;


--
-- Name: fk_pcmn_val; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrnx
    ADD CONSTRAINT fk_pcmn_val FOREIGN KEY (j_poste) REFERENCES tmp_pcmn(pcm_val);


--
-- Name: fk_pcmn_val; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY centralized
    ADD CONSTRAINT fk_pcmn_val FOREIGN KEY (c_poste) REFERENCES tmp_pcmn(pcm_val);


--
-- Name: fk_stock_good_f_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY stock_goods
    ADD CONSTRAINT fk_stock_good_f_id FOREIGN KEY (f_id) REFERENCES fiche(f_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: forecast_child; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY forecast_cat
    ADD CONSTRAINT forecast_child FOREIGN KEY (f_id) REFERENCES forecast(f_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: forecast_f_end_date_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY forecast
    ADD CONSTRAINT forecast_f_end_date_fkey FOREIGN KEY (f_end_date) REFERENCES parm_periode(p_id) ON UPDATE SET NULL ON DELETE SET NULL;


--
-- Name: forecast_f_start_date_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY forecast
    ADD CONSTRAINT forecast_f_start_date_fkey FOREIGN KEY (f_start_date) REFERENCES parm_periode(p_id) ON UPDATE SET NULL ON DELETE SET NULL;


--
-- Name: formdef_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY form
    ADD CONSTRAINT formdef_fk FOREIGN KEY (fo_fr_id) REFERENCES formdef(fr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: jnt_cred_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY letter_cred
    ADD CONSTRAINT jnt_cred_fk FOREIGN KEY (jl_id) REFERENCES jnt_letter(jl_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: jnt_deb_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY letter_deb
    ADD CONSTRAINT jnt_deb_fk FOREIGN KEY (jl_id) REFERENCES jnt_letter(jl_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: jrn_def_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY op_predef
    ADD CONSTRAINT jrn_def_id_fk FOREIGN KEY (jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: jrn_per_jrn_def_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_periode
    ADD CONSTRAINT jrn_per_jrn_def_id FOREIGN KEY (jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: jrn_periode_p_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_periode
    ADD CONSTRAINT jrn_periode_p_id FOREIGN KEY (p_id) REFERENCES parm_periode(p_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: jrn_rapt_jr_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_rapt
    ADD CONSTRAINT jrn_rapt_jr_id_fkey FOREIGN KEY (jr_id) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: jrn_rapt_jra_concerned_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_rapt
    ADD CONSTRAINT jrn_rapt_jra_concerned_fkey FOREIGN KEY (jra_concerned) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: jrnx_f_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrnx
    ADD CONSTRAINT jrnx_f_id_fkey FOREIGN KEY (f_id) REFERENCES fiche(f_id) ON UPDATE CASCADE;


--
-- Name: jrnx_note_j_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY jrn_note
    ADD CONSTRAINT jrnx_note_j_id_fkey FOREIGN KEY (jr_id) REFERENCES jrn(jr_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: letter_cred_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY letter_cred
    ADD CONSTRAINT letter_cred_fk FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: letter_deb_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY letter_deb
    ADD CONSTRAINT letter_deb_fk FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: md_type; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY document_modele
    ADD CONSTRAINT md_type FOREIGN KEY (md_type) REFERENCES document_type(dt_id);


--
-- Name: mod_payment_jrn_def_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_jrn_def_id_fk FOREIGN KEY (jrn_def_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: mod_payment_mp_fd_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_mp_fd_id_fkey FOREIGN KEY (mp_fd_id) REFERENCES fiche_def(fd_id);


--
-- Name: mod_payment_mp_jrn_def_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY mod_payment
    ADD CONSTRAINT mod_payment_mp_jrn_def_id_fkey FOREIGN KEY (mp_jrn_def_id) REFERENCES jrn_def(jrn_def_id);


--
-- Name: operation_analytique_j_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT operation_analytique_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: operation_analytique_po_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY operation_analytique
    ADD CONSTRAINT operation_analytique_po_id_fkey FOREIGN KEY (po_id) REFERENCES poste_analytique(po_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: poste_analytique_pa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY poste_analytique
    ADD CONSTRAINT poste_analytique_pa_id_fkey FOREIGN KEY (pa_id) REFERENCES plan_analytique(pa_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: profile_menu_me_code_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY profile_menu
    ADD CONSTRAINT profile_menu_me_code_fkey FOREIGN KEY (me_code) REFERENCES menu_ref(me_code) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: profile_menu_p_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY profile_menu
    ADD CONSTRAINT profile_menu_p_id_fkey FOREIGN KEY (p_id) REFERENCES profile(p_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: profile_menu_type_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY profile_menu
    ADD CONSTRAINT profile_menu_type_fkey FOREIGN KEY (p_type_display) REFERENCES profile_menu_type(pm_type);


--
-- Name: profile_user_p_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY profile_user
    ADD CONSTRAINT profile_user_p_id_fkey FOREIGN KEY (p_id) REFERENCES profile(p_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: qp_vat_code_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT qp_vat_code_fk FOREIGN KEY (qp_vat_code) REFERENCES tva_rate(tva_id);


--
-- Name: qs_vat_code_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT qs_vat_code_fk FOREIGN KEY (qs_vat_code) REFERENCES tva_rate(tva_id);


--
-- Name: quant_purchase_j_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT quant_purchase_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: quant_purchase_qp_internal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY quant_purchase
    ADD CONSTRAINT quant_purchase_qp_internal_fkey FOREIGN KEY (qp_internal) REFERENCES jrn(jr_internal) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: quant_sold_j_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT quant_sold_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: quant_sold_qs_internal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY quant_sold
    ADD CONSTRAINT quant_sold_qs_internal_fkey FOREIGN KEY (qs_internal) REFERENCES jrn(jr_internal) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: stock_goods_j_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY stock_goods
    ADD CONSTRAINT stock_goods_j_id_fkey FOREIGN KEY (j_id) REFERENCES jrnx(j_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: uj_priv_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_sec_jrn
    ADD CONSTRAINT uj_priv_id_fkey FOREIGN KEY (uj_jrn_id) REFERENCES jrn_def(jrn_def_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

commit;
