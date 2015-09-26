
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;


CREATE SCHEMA comptaproc;





SET search_path = public, pg_catalog;


CREATE DOMAIN account_type AS character varying(40);


SET search_path = comptaproc, pg_catalog;


CREATE FUNCTION account_add(p_id public.account_type, p_name character varying) RETURNS void
    AS $$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	nCount integer;
begin
	select count(*) into nCount from tmp_pcmn where pcm_val=p_id;
	if nCount = 0 then
		nParent=account_parent(p_id);
		insert into tmp_pcmn (pcm_val,pcm_lib,pcm_val_parent)
			values (p_id, p_name,nParent);
	end if;
return;
end ;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION account_auto(p_fd_id integer) RETURNS boolean
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION account_compute(p_f_id integer) RETURNS public.account_type
    AS $$
declare
	class_base fiche_def.fd_class_base%type;
	maxcode numeric;
	sResult account_type;
begin
	select fd_class_base into class_base
	from
		fiche_def join fiche using (fd_id)
	where
		f_id=p_f_id;
	raise notice 'account_compute class base %',class_base;
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
	return sResult;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION account_insert(p_f_id integer, p_account text) RETURNS integer
    AS $_$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	sName varchar;
	nNew tmp_pcmn.pcm_val%type;
	bAuto bool;
	nFd_id integer;
	sClass_Base fiche_def.fd_class_base%TYPE;
	nCount integer;
	first text;
	second text;
begin

	if length(trim(p_account)) != 0 then
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
					select av_text into sName from
						attr_value join jnt_fic_att_value using (jft_id)
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
		if bAuto = true and sClass_base similar to '^[[:digit:]]*$'  then
			raise info 'account generated automatically';
			nNew:=account_compute(p_f_id);
			raise info 'nNew %', nNew;
			select av_text into sName from
				attr_value join jnt_fic_att_value using (jft_id)
			where
				ad_id=1 and f_id=p_f_id;
				nParent:=account_parent(nNew);
			perform account_add  (nNew,sName);
			perform attribut_insert(p_f_id,5,nNew);

		else
		-- if there is an account_base then it is the default
		      select fd_class_base::account_type into nNew from fiche_def join fiche using (fd_id) where f_id=p_f_id;
			if nNew is null or length(trim(nNew)) = 0 then
				raise notice 'count is null';
				 perform attribut_insert(p_f_id,5,null);
			else
				 perform attribut_insert(p_f_id,5,nNew);
			end if;
		end if;
	end if;

return 0;
end;
$_$
    LANGUAGE plpgsql;



CREATE FUNCTION account_parent(p_account public.account_type) RETURNS public.account_type
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION account_update(p_f_id integer, p_account public.account_type) RETURNS integer
    AS $$
declare
	nMax fiche.f_id%type;
	nCount integer;
	nParent tmp_pcmn.pcm_val_parent%type;
	sName varchar;
	nJft_id attr_value.jft_id%type;
	first text;
	second text;
begin

	if length(trim(p_account)) != 0 then
		if position (',' in p_account) = 0 then
			select count(*) into nCount from tmp_pcmn where pcm_val=p_account;
			if nCount = 0 then
			select av_text into sName from
				attr_value join jnt_fic_att_value using (jft_id)
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
		end if;
	end if;
	select jft_id into njft_id from jnt_fic_att_value where f_id=p_f_id and ad_id=5;
	update attr_value set av_text=p_account where jft_id=njft_id;

return njft_id;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION action_gestion_ins_upd() RETURNS trigger
    AS $$
begin
NEW.ag_title := substr(trim(NEW.ag_title),1,70);
NEW.ag_hour := substr(trim(NEW.ag_hour),1,5);
return NEW;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION action_get_tree(p_id bigint) RETURNS SETOF bigint
    AS $$

declare
   e bigint;
   i bigint;
begin
   for e in select ag_id from action_gestion where ag_ref_ag_id=p_id
   loop
	if e = 0 then 
		return;
	end if;
	return next e;
	for i in select ag_id from action_gestion where ag_ref_ag_id=e
	loop
	if i = 0 then 
		return;
	end if;
		return next i;
	end loop;
   end loop;
   return;

end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION attribut_insert(p_f_id integer, p_ad_id integer, p_value character varying) RETURNS void
    AS $$
declare 
	n_jft_id integer;
begin
	select nextval('s_jnt_fic_att_value') into n_jft_id;
	 insert into jnt_fic_att_value (jft_id,f_id,ad_id) values (n_jft_id,p_f_id,p_ad_id);
	 insert into attr_value (jft_id,av_text) values (n_jft_id,trim(p_value));
return;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION attribute_correct_order() RETURNS void
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION card_class_base(p_f_id integer) RETURNS text
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION check_balance(p_grpt integer) RETURNS numeric
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION correct_sequence(p_sequence text, p_col text, p_table text) RETURNS integer
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
$$
    LANGUAGE plpgsql;



COMMENT ON FUNCTION correct_sequence(p_sequence text, p_col text, p_table text) IS ' Often the primary key is a sequence number and sometimes the value of the sequence is not synchronized with the primary key ( p_sequence : sequence name, p_col : col of the pk,p_table : concerned table';



CREATE FUNCTION create_missing_sequence() RETURNS integer
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION drop_index(p_constraint character varying) RETURNS void
    AS $$
declare 
	nCount integer;
begin
	select count(*) into nCount from pg_indexes where indexname=p_constraint;
	if nCount = 1 then
	execute 'drop index '||p_constraint ;
	end if;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION drop_it(p_constraint character varying) RETURNS void
    AS $$
declare 
	nCount integer;
begin
	select count(*) into nCount from pg_constraint where conname=p_constraint;
	if nCount = 1 then
	execute 'alter table parm_periode drop constraint '||p_constraint ;
	end if;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION extension_ins_upd() RETURNS trigger
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

$$
    LANGUAGE plpgsql;



CREATE FUNCTION fiche_account_parent(p_f_id integer) RETURNS public.account_type
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION fiche_attribut_synchro(p_fd_id integer) RETURNS void
    AS $$
declare
	-- this sql gives the f_id and the missing attribute (ad_id)
	list_missing cursor for select f_id,fd_id,ad_id,jnt_order from jnt_fic_attr join fiche as A using (fd_id) where fd_id=p_fd_id and ad_id not in (select ad_id from fiche join jnt_fic_att_value using (f_id) where fd_id=jnt_fic_attr.fd_id and A.f_id=f_id);
	rec record;
	-- value of the last insert
	jnt jnt_fic_att_value%ROWTYPE;
begin
	open list_missing;
	loop
	
	fetch list_missing into rec;
	IF NOT FOUND then
		exit;
	end if;
	-- insert a value into jnt_fic_att_value
	insert 	into jnt_fic_att_value (f_id,ad_id) values (rec.f_id,rec.ad_id) returning * into jnt;

	-- now we insert into attr_value
	insert into attr_value values (jnt.jft_id,'');
	end loop;
	close list_missing;
end; 
$$
    LANGUAGE plpgsql;



CREATE FUNCTION fiche_def_ins_upd() RETURNS trigger
    AS $$
begin

if position (',' in NEW.fd_class_base) != 0 then
   NEW.fd_create_account='f';

end if;
return NEW;
end;$$
    LANGUAGE plpgsql;



CREATE FUNCTION find_pcm_type(pp_value public.account_type) RETURNS text
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION get_letter_jnt(a bigint) RETURNS bigint
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION get_pcm_tree(source public.account_type) RETURNS SETOF public.account_type
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION group_analytic_ins_upd() RETURNS trigger
    AS $$
declare 
name text;
begin
name:=upper(NEW.ga_id);
name:=trim(name);
name:=replace(name,' ','');
NEW.ga_id:=name;
return NEW;
end;$$
    LANGUAGE plpgsql;



CREATE FUNCTION group_analytique_del() RETURNS trigger
    AS $$
begin
update poste_analytique set ga_id=null
where ga_id=OLD.ga_id;
return OLD;
end;$$
    LANGUAGE plpgsql;



CREATE FUNCTION html_quote(p_string text) RETURNS text
    AS $$
declare
	r text;
begin
	r:=p_string;
	r:=replace(r,'<','&lt;');
	r:=replace(r,'>','&gt;');
	r:=replace(r,'''','&quot;');
	return r;
end;$$
    LANGUAGE plpgsql;



COMMENT ON FUNCTION html_quote(p_string text) IS 'remove harmfull HTML char';



CREATE FUNCTION info_def_ins_upd() RETURNS trigger
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION insert_jrnx(p_date character varying, p_montant numeric, p_poste public.account_type, p_grpt integer, p_jrn_def integer, p_debit boolean, p_tech_user text, p_tech_per integer, p_qcode text, p_comment text) RETURNS void
    AS $$
declare
	sCode varchar;
	nCount_qcode integer;
begin
	sCode=trim(p_qcode);

	-- if p_qcode is empty try to find one
	if length(sCode) = 0 or p_qcode is null then
		select count(*) into nCount_qcode
			from vw_poste_qcode where j_poste=p_poste;
	-- if we find only one q_code for a accountancy account
	-- then retrieve it
		if nCount_qcode = 1 then
			select j_qcode::text into sCode
			from vw_poste_qcode where j_poste=p_poste;
		else
		 sCode=NULL;
		end if;

	end if;

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
		sCode
	);

return;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION insert_quant_purchase(p_internal text, p_j_id numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_nd_amount numeric, p_nd_tva numeric, p_nd_tva_recup numeric, p_dep_priv numeric, p_client character varying) RETURNS void
    AS $$
declare
	fid_client integer;
	fid_good   integer;
begin
	select f_id into fid_client from
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(trim(p_client));
	select f_id into fid_good from
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(trim(p_fiche));
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
		qp_dep_priv)
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
		p_dep_priv);
	return;
end;
 $$
    LANGUAGE plpgsql;



CREATE FUNCTION insert_quant_sold(p_internal text, p_jid numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_client character varying) RETURNS void
    AS $$
declare
	fid_client integer;
	fid_good   integer;
begin

	select f_id into fid_client from
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(trim(p_client));
	select f_id into fid_good from
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(trim(p_fiche));
	insert into quant_sold
		(qs_internal,j_id,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client,qs_valid)
	values
		(p_internal,p_jid,fid_good,p_quant,p_price,p_vat,p_vat_code,fid_client,'Y');
	return;
end;
 $$
    LANGUAGE plpgsql;



CREATE FUNCTION insert_quick_code(nf_id integer, tav_text text) RETURNS integer
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
			from jnt_fic_att_value join attr_value using (jft_id) 
		where 
			ad_id=23 and  av_text=upper(tText);

		if nExist = 0 then
			exit;
		end if;
		tText:='FID'||ns;
	end loop;
	-- insert into table jnt_fic_att_value
	insert into jnt_fic_att_value values (ns,nf_id,23);
	-- insert value into attr_value
	insert into attr_value values (ns,upper(tText));
	return ns;
	end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION jrn_check_periode() RETURNS trigger
    AS $$
declare 
bClosed bool;
str_status text;
ljr_tech_per jrn.jr_tech_per%TYPE;
ljr_def_id jrn.jr_def_id%TYPE;
lreturn jrn%ROWTYPE;
begin
if TG_OP='INSERT' then
	ljr_tech_per :=NEW.jr_tech_per;
	ljr_def_id   :=NEW.jr_def_id;
        lreturn      :=NEW;
end if;

if TG_OP='DELETE' then
	ljr_tech_per :=OLD.jr_tech_per;
	ljr_def_id   :=OLD.jr_def_id;
        lreturn      :=OLD;
end if;

select p_closed into bClosed from parm_periode 
	where p_id=ljr_tech_per;

if bClosed = true then
	raise exception 'Periode fermee';
end if;

select status into str_status from jrn_periode 
       where p_id =ljr_tech_per and jrn_def_id=ljr_def_id;

if str_status <> 'OP' then
	raise exception 'Periode fermee';
end if;

return lreturn;
end;$$
    LANGUAGE plpgsql;



CREATE FUNCTION jrn_def_add() RETURNS trigger
    AS $$begin
execute 'insert into jrn_periode(p_id,jrn_def_id,status) select p_id,'||NEW.jrn_def_id||',
	case when p_central=true then ''CE''
	      when p_closed=true then ''CL''
	else ''OP''
	end
from
parm_periode ';
return NEW;
end;$$
    LANGUAGE plpgsql;



CREATE FUNCTION jrn_def_delete() RETURNS trigger
    AS $$
declare 
nb numeric;
begin
select count(*) into nb from jrn where jr_def_id=OLD.jrn_def_id;

if nb <> 0 then
	raise exception 'EFFACEMENT INTERDIT: JOURNAL UTILISE';
end if;
return OLD;
end;$$
    LANGUAGE plpgsql;



CREATE FUNCTION jrn_del() RETURNS trigger
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION jrnx_del() RETURNS trigger
    AS $$
declare
row jrnx%ROWTYPE;
begin
row:=OLD;
insert into del_jrnx select * from jrnx where j_id=row.j_id;
return row;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION plan_analytic_ins_upd() RETURNS trigger
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION poste_analytique_ins_upd() RETURNS trigger
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
end;$$
    LANGUAGE plpgsql;



CREATE FUNCTION proc_check_balance() RETURNS trigger
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION t_document_modele_validate() RETURNS trigger
    AS $$
declare 
    lText text;
    modified document_modele%ROWTYPE;
begin
    modified:=NEW;

	modified.md_filename:=replace(NEW.md_filename,' ','_');
	return modified;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION t_document_type_insert() RETURNS trigger
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION t_document_validate() RETURNS trigger
    AS $$
declare
  lText text;
  modified document%ROWTYPE;
begin
    	modified:=NEW;
	modified.d_filename:=replace(NEW.d_filename,' ','_');
	return modified;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION t_jrn_def_sequence() RETURNS trigger
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION tmp_pcmn_ins() RETURNS trigger
    AS $$
declare
   r_record tmp_pcmn%ROWTYPE;
begin
r_record=NEW;
if  length(trim(r_record.pcm_type))=0 or r_record.pcm_type is NULL then 
   r_record.pcm_type:=find_pcm_type(NEW.pcm_val);
   return r_record;
end if;
return NEW;
end;
$$
    LANGUAGE plpgsql;



CREATE FUNCTION trim_cvs_quote() RETURNS trigger
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION trim_space_format_csv_banque() RETURNS trigger
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
$$
    LANGUAGE plpgsql;



CREATE FUNCTION tva_delete(integer) RETURNS void
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
$_$
    LANGUAGE plpgsql;



CREATE FUNCTION tva_insert(text, numeric, text, text) RETURNS integer
    AS $_$
declare
	l_tva_id integer;
	p_tva_label alias for $1;
	p_tva_rate alias for $2;
	p_tva_comment alias for $3;
	p_tva_poste alias for $4;
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
insert into tva_rate(tva_id,tva_label,tva_rate,tva_comment,tva_poste)
	values (l_tva_id,p_tva_label,p_tva_rate,p_tva_comment,p_tva_poste);
return 0;
end;
$_$
    LANGUAGE plpgsql;



CREATE FUNCTION tva_modify(integer, text, numeric, text, text) RETURNS integer
    AS $_$
declare
	p_tva_id alias for $1;
	p_tva_label alias for $2;
	p_tva_rate alias for $3;
	p_tva_comment alias for $4;
	p_tva_poste alias for $5;
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
update tva_rate set tva_label=p_tva_label,tva_rate=p_tva_rate,tva_comment=p_tva_comment,tva_poste=p_tva_poste
	where tva_id=p_tva_id;
return 0;
end;
$_$
    LANGUAGE plpgsql;



CREATE FUNCTION update_quick_code(njft_id integer, tav_text text) RETURNS integer
    AS $$
	declare
	ns integer;
	nExist integer;
	tText text;
	old_qcode varchar;
	begin
	-- get current value
	select av_text into old_qcode from attr_value where jft_id=njft_id;
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
			from jnt_fic_att_value join attr_value using (jft_id) 
		where 
			ad_id=23 and av_text=tText;

		if nExist = 0 then
			exit;
		end if;	
		if tText = 'FID'||ns then
			-- take the next sequence
			select nextval('s_jnt_fic_att_value') into ns;
		end if;
		tText  :='FID'||ns;
		
	end loop;
	update attr_value set av_text = tText where jft_id=njft_id;

	-- update also the contact
	update attr_value set av_text = tText 
		where jft_id in 
			( select jft_id 
				from jnt_fic_att_value join attr_value using (jft_id) 
			where ad_id=25 and av_text=old_qcode);


	update jrnx set j_qcode=tText where j_qcode = old_qcode;
	return ns;
	end;
$$
    LANGUAGE plpgsql;


SET search_path = public, pg_catalog;


CREATE FUNCTION bud_card_ins_upd() RETURNS trigger
    AS $$declare
 sCode text;
begin

sCode:=trim(upper(NEW.bc_code));
sCode:=replace(sCode,' ','_');
sCode:=substr(sCode,1,10);
NEW.bc_code:=sCode;
return NEW;
end;$$
    LANGUAGE plpgsql;



CREATE FUNCTION bud_detail_ins_upd() RETURNS trigger
    AS $$declare
mline bud_detail%ROWTYPE;
begin
mline:=NEW;
if mline.po_id = -1 then
   mline.po_id:=NULL;
end if;
return mline;
end;$$
    LANGUAGE plpgsql;


SET default_tablespace = '';

SET default_with_oids = true;


CREATE TABLE action (
    ac_id integer NOT NULL,
    ac_description text NOT NULL,
    ac_module text,
    ac_code character varying(9)
);



COMMENT ON TABLE action IS 'The different privileges';



COMMENT ON COLUMN action.ac_code IS 'this code will be used in the code with the function User::check_action ';


SET default_with_oids = false;


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



COMMENT ON TABLE action_detail IS 'Detail of action_gestion, see class Action_Detail';



COMMENT ON COLUMN action_detail.f_id IS 'the concerned	card';



COMMENT ON COLUMN action_detail.ad_text IS ' Description ';



COMMENT ON COLUMN action_detail.ad_pu IS ' price per unit ';



COMMENT ON COLUMN action_detail.ad_quant IS 'quantity ';



COMMENT ON COLUMN action_detail.ad_tva_id IS ' tva_id ';



COMMENT ON COLUMN action_detail.ad_tva_amount IS ' tva_amount ';



COMMENT ON COLUMN action_detail.ad_total_amount IS ' total amount';



CREATE SEQUENCE action_detail_ad_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE action_detail_ad_id_seq OWNED BY action_detail.ad_id;



CREATE SEQUENCE action_gestion_ag_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


SET default_with_oids = true;


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



COMMENT ON TABLE action_gestion IS 'Contains the details for the follow-up of customer, supplier, administration';



COMMENT ON COLUMN action_gestion.ag_type IS ' type of action: see document_type ';



COMMENT ON COLUMN action_gestion.f_id_dest IS ' third party ';



COMMENT ON COLUMN action_gestion.ag_title IS ' title ';



COMMENT ON COLUMN action_gestion.ag_timestamp IS ' ';



COMMENT ON COLUMN action_gestion.ag_cal IS ' visible in the calendar if = C';



COMMENT ON COLUMN action_gestion.ag_ref_ag_id IS ' concerning the action ';



COMMENT ON COLUMN action_gestion.ag_comment IS ' comment of the action';



COMMENT ON COLUMN action_gestion.ag_ref IS 'its reference ';



COMMENT ON COLUMN action_gestion.ag_priority IS 'Low, medium, important ';



COMMENT ON COLUMN action_gestion.ag_dest IS ' is the person who has to take care of this action ';



COMMENT ON COLUMN action_gestion.ag_owner IS ' is the owner of this action ';



COMMENT ON COLUMN action_gestion.ag_contact IS ' contact of the third part ';



COMMENT ON COLUMN action_gestion.ag_state IS 'state of the action same as document_state ';



CREATE TABLE attr_def (
    ad_id integer DEFAULT nextval(('s_attr_def'::text)::regclass) NOT NULL,
    ad_text text
);



COMMENT ON TABLE attr_def IS 'The available attributs for the cards';



CREATE TABLE attr_min (
    frd_id integer,
    ad_id integer
);



COMMENT ON TABLE attr_min IS 'The value of  attributs for the cards';



CREATE TABLE attr_value (
    jft_id integer,
    av_text text
);



CREATE SEQUENCE bilan_b_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


SET default_with_oids = false;


CREATE TABLE bilan (
    b_id integer DEFAULT nextval('bilan_b_id_seq'::regclass) NOT NULL,
    b_name text NOT NULL,
    b_file_template text NOT NULL,
    b_file_form text,
    b_type text NOT NULL
);



COMMENT ON TABLE bilan IS 'contains the template and the data for generating different documents  ';



COMMENT ON COLUMN bilan.b_id IS 'primary key';



COMMENT ON COLUMN bilan.b_name IS 'Name of the document';



COMMENT ON COLUMN bilan.b_file_template IS 'path of the template (document/...)';



COMMENT ON COLUMN bilan.b_file_form IS 'path of the file with forms';



COMMENT ON COLUMN bilan.b_type IS 'type = ODS, RTF...';



CREATE SEQUENCE bud_card_bc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE bud_detail_bd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE bud_detail_periode_bdp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


SET default_with_oids = true;


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



COMMENT ON TABLE centralized IS 'The centralized journal';


SET default_with_oids = false;


CREATE TABLE del_action (
    del_id integer NOT NULL,
    del_name text NOT NULL,
    del_time timestamp without time zone
);



CREATE SEQUENCE del_action_del_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE del_action_del_id_seq OWNED BY del_action.del_id;



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
    jr_pj_number text
);



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
    j_qcode text
);



CREATE SEQUENCE document_d_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


SET default_with_oids = true;


CREATE TABLE document (
    d_id integer DEFAULT nextval('document_d_id_seq'::regclass) NOT NULL,
    ag_id integer NOT NULL,
    d_lob oid,
    d_number bigint NOT NULL,
    d_filename text,
    d_mimetype text
);



COMMENT ON TABLE document IS 'This table contains all the documents : summary and lob files';



CREATE SEQUENCE document_modele_md_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE TABLE document_modele (
    md_id integer DEFAULT nextval('document_modele_md_id_seq'::regclass) NOT NULL,
    md_name text NOT NULL,
    md_lob oid,
    md_type integer NOT NULL,
    md_filename text,
    md_mimetype text,
    md_affect character varying(3) NOT NULL
);



COMMENT ON TABLE document_modele IS ' contains all the template for the  documents';



CREATE SEQUENCE document_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



COMMENT ON SEQUENCE document_seq IS 'Sequence for the sequence bound to the document modele';



CREATE SEQUENCE document_state_s_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE TABLE document_state (
    s_id integer DEFAULT nextval('document_state_s_id_seq'::regclass) NOT NULL,
    s_value character varying(50) NOT NULL
);



COMMENT ON TABLE document_state IS 'State of the document';



CREATE SEQUENCE document_type_dt_id_seq
    START WITH 25
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE TABLE document_type (
    dt_id integer DEFAULT nextval('document_type_dt_id_seq'::regclass) NOT NULL,
    dt_value character varying(80)
);



COMMENT ON TABLE document_type IS 'Type of document : meeting, invoice,...';


SET default_with_oids = false;


CREATE TABLE extension (
    ex_id integer NOT NULL,
    ex_name character varying(30) NOT NULL,
    ex_code character varying(15) NOT NULL,
    ex_desc character varying(250),
    ex_file character varying NOT NULL,
    ex_enable "char" DEFAULT 'Y'::"char" NOT NULL
);



COMMENT ON TABLE extension IS 'Content the needed information for the extension';



COMMENT ON COLUMN extension.ex_id IS 'Primary key';



COMMENT ON COLUMN extension.ex_name IS 'code of the extension ';



COMMENT ON COLUMN extension.ex_code IS 'code of the extension ';



COMMENT ON COLUMN extension.ex_desc IS 'Description of the extension ';



COMMENT ON COLUMN extension.ex_file IS 'path to the extension to include';



COMMENT ON COLUMN extension.ex_enable IS 'Y : enabled N : disabled ';



CREATE SEQUENCE extension_ex_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE extension_ex_id_seq OWNED BY extension.ex_id;


SET default_with_oids = true;


CREATE TABLE fiche (
    f_id integer DEFAULT nextval(('s_fiche'::text)::regclass) NOT NULL,
    fd_id integer
);



COMMENT ON TABLE fiche IS 'Cards';



CREATE TABLE fiche_def (
    fd_id integer DEFAULT nextval(('s_fdef'::text)::regclass) NOT NULL,
    fd_class_base text,
    fd_label text NOT NULL,
    fd_create_account boolean DEFAULT false,
    frd_id integer NOT NULL
);



COMMENT ON TABLE fiche_def IS 'Cards definition';



CREATE TABLE fiche_def_ref (
    frd_id integer DEFAULT nextval(('s_fiche_def_ref'::text)::regclass) NOT NULL,
    frd_text text,
    frd_class_base integer
);



COMMENT ON TABLE fiche_def_ref IS 'Family Cards definition';


SET default_with_oids = false;


CREATE TABLE forecast (
    f_id integer NOT NULL,
    f_name text NOT NULL
);



COMMENT ON TABLE forecast IS 'contains the name of the forecast';



CREATE TABLE forecast_cat (
    fc_id integer NOT NULL,
    fc_desc text NOT NULL,
    f_id bigint,
    fc_order integer DEFAULT 0 NOT NULL
);



COMMENT ON COLUMN forecast_cat.fc_id IS 'primary key';



COMMENT ON COLUMN forecast_cat.fc_desc IS 'text of the category';



COMMENT ON COLUMN forecast_cat.f_id IS 'Foreign key, it is the parent from the table forecast';



COMMENT ON COLUMN forecast_cat.fc_order IS 'Order of the category, used when displaid';



CREATE SEQUENCE forecast_cat_fc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE forecast_cat_fc_id_seq OWNED BY forecast_cat.fc_id;



CREATE SEQUENCE forecast_f_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE forecast_f_id_seq OWNED BY forecast.f_id;



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



COMMENT ON COLUMN forecast_item.fi_id IS 'Primary key';



COMMENT ON COLUMN forecast_item.fi_text IS 'Label of the i	tem';



COMMENT ON COLUMN forecast_item.fi_account IS 'Accountancy entry';



COMMENT ON COLUMN forecast_item.fi_card IS 'Card (fiche.f_id)';



COMMENT ON COLUMN forecast_item.fi_order IS 'Order of showing (not used)';



COMMENT ON COLUMN forecast_item.fi_amount IS 'Amount';



COMMENT ON COLUMN forecast_item.fi_debit IS 'possible values are D or C';



COMMENT ON COLUMN forecast_item.fi_pid IS '0 for every month, or the value parm_periode.p_id ';



CREATE SEQUENCE forecast_item_fi_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE forecast_item_fi_id_seq OWNED BY forecast_item.fi_id;


SET default_with_oids = true;


CREATE TABLE form (
    fo_id integer DEFAULT nextval(('s_form'::text)::regclass) NOT NULL,
    fo_fr_id integer,
    fo_pos integer,
    fo_label text,
    fo_formula text
);



COMMENT ON TABLE form IS 'Forms content';



CREATE TABLE format_csv_banque (
    name text NOT NULL,
    include_file text NOT NULL
);



CREATE TABLE formdef (
    fr_id integer DEFAULT nextval(('s_formdef'::text)::regclass) NOT NULL,
    fr_label text
);


SET default_with_oids = false;


CREATE TABLE groupe_analytique (
    ga_id character varying(10) NOT NULL,
    pa_id integer,
    ga_description text
);



CREATE SEQUENCE historique_analytique_ha_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


SET default_with_oids = true;


CREATE TABLE import_tmp (
    code text NOT NULL,
    date_exec date NOT NULL,
    date_valeur date NOT NULL,
    devise text,
    compte_ordre text,
    detail text,
    num_compte text,
    poste_comptable text,
    status character varying(1) DEFAULT 'n'::character varying NOT NULL,
    bq_account text NOT NULL,
    jrn integer NOT NULL,
    jr_rapt text,
    montant numeric(20,4) DEFAULT 0 NOT NULL,
    CONSTRAINT import_tmp_status_check CHECK ((((((status)::text = 'n'::text) OR ((status)::text = 't'::text)) OR ((status)::text = 'd'::text)) OR ((status)::text = 'w'::text)))
);



COMMENT ON TABLE import_tmp IS 'Table temporaire pour l''importation des banques en format CSV';



COMMENT ON COLUMN import_tmp.status IS 'Status w waiting, d delete t transfert';


SET default_with_oids = false;


CREATE TABLE info_def (
    id_type text NOT NULL,
    id_description text
);



COMMENT ON TABLE info_def IS 'Contains the types of additionnal info we can add to a operation';


SET default_with_oids = true;


CREATE TABLE jnt_fic_att_value (
    jft_id integer DEFAULT nextval(('s_jnt_fic_att_value'::text)::regclass) NOT NULL,
    f_id integer,
    ad_id integer
);



COMMENT ON TABLE jnt_fic_att_value IS 'join between the card and the attribut definition';



CREATE SEQUENCE s_jnt_id
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE TABLE jnt_fic_attr (
    fd_id integer,
    ad_id integer,
    jnt_id bigint DEFAULT nextval('s_jnt_id'::regclass) NOT NULL,
    jnt_order integer NOT NULL
);



COMMENT ON TABLE jnt_fic_attr IS 'join between the family card and the attribut definition';


SET default_with_oids = false;


CREATE TABLE jnt_letter (
    jl_id integer NOT NULL,
    jl_amount_deb numeric(20,4)
);



CREATE SEQUENCE jnt_letter_jl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE jnt_letter_jl_id_seq OWNED BY jnt_letter.jl_id;


SET default_with_oids = true;


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



COMMENT ON TABLE jrn IS 'Journal: content one line for a group of accountancy writing';



CREATE TABLE jrn_action (
    ja_id integer DEFAULT nextval(('s_jrnaction'::text)::regclass) NOT NULL,
    ja_name text NOT NULL,
    ja_desc text,
    ja_url text NOT NULL,
    ja_action text NOT NULL,
    ja_lang text DEFAULT 'FR'::text,
    ja_jrn_type character(3)
);



COMMENT ON TABLE jrn_action IS 'Possible action when we are in journal (menu)';



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
    jrn_def_pj_pref text
);



COMMENT ON TABLE jrn_def IS 'Definition of a journal, his properties';


SET default_with_oids = false;


CREATE TABLE jrn_info (
    ji_id integer NOT NULL,
    jr_id integer NOT NULL,
    id_type text NOT NULL,
    ji_value text
);



CREATE SEQUENCE jrn_info_ji_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE jrn_info_ji_id_seq OWNED BY jrn_info.ji_id;



CREATE TABLE jrn_periode (
    jrn_def_id integer NOT NULL,
    p_id integer NOT NULL,
    status text
);


SET default_with_oids = true;


CREATE TABLE jrn_rapt (
    jra_id integer DEFAULT nextval(('s_jrn_rapt'::text)::regclass) NOT NULL,
    jr_id integer NOT NULL,
    jra_concerned integer NOT NULL
);



COMMENT ON TABLE jrn_rapt IS 'Rapprochement between operation';



CREATE TABLE jrn_type (
    jrn_type_id character(3) NOT NULL,
    jrn_desc text
);



COMMENT ON TABLE jrn_type IS 'Type of journal (Sell, Buy, Financial...)';



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
    j_qcode text
);



COMMENT ON TABLE jrnx IS 'Journal: content one line for each accountancy writing';


SET default_with_oids = false;


CREATE TABLE letter_cred (
    lc_id integer NOT NULL,
    j_id bigint NOT NULL,
    jl_id bigint NOT NULL
);



CREATE SEQUENCE letter_cred_lc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE letter_cred_lc_id_seq OWNED BY letter_cred.lc_id;



CREATE TABLE letter_deb (
    ld_id integer NOT NULL,
    j_id bigint NOT NULL,
    jl_id bigint NOT NULL
);



CREATE SEQUENCE letter_deb_ld_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE letter_deb_ld_id_seq OWNED BY letter_deb.ld_id;



CREATE TABLE mod_payment (
    mp_id integer NOT NULL,
    mp_lib text NOT NULL,
    mp_jrn_def_id integer NOT NULL,
    mp_type character varying(3) NOT NULL,
    mp_fd_id bigint,
    mp_qcode text
);



COMMENT ON TABLE mod_payment IS 'Contains the different media of payment and the corresponding ledger';



CREATE SEQUENCE mod_payment_mp_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE mod_payment_mp_id_seq OWNED BY mod_payment.mp_id;



CREATE SEQUENCE op_def_op_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE TABLE op_predef (
    od_id integer DEFAULT nextval('op_def_op_seq'::regclass) NOT NULL,
    jrn_def_id integer NOT NULL,
    od_name text NOT NULL,
    od_item integer NOT NULL,
    od_jrn_type text NOT NULL,
    od_direct boolean NOT NULL
);



COMMENT ON TABLE op_predef IS 'predefined operation';



COMMENT ON COLUMN op_predef.jrn_def_id IS 'jrn_id';



COMMENT ON COLUMN op_predef.od_name IS 'name of the operation';



CREATE SEQUENCE op_predef_detail_opd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



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



COMMENT ON TABLE op_predef_detail IS 'contains the detail of predefined operations';



CREATE SEQUENCE s_oa_group
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE TABLE operation_analytique (
    oa_id integer DEFAULT nextval('historique_analytique_ha_id_seq'::regclass) NOT NULL,
    po_id integer NOT NULL,
    pa_id integer NOT NULL,
    oa_amount numeric(20,4) NOT NULL,
    oa_description text,
    oa_debit boolean DEFAULT true NOT NULL,
    j_id integer,
    oa_group integer DEFAULT nextval('s_oa_group'::regclass) NOT NULL,
    oa_date date NOT NULL,
    oa_row integer
);



COMMENT ON TABLE operation_analytique IS 'History of the analytic account';


SET default_with_oids = true;


CREATE TABLE parameter (
    pr_id text NOT NULL,
    pr_value text
);



COMMENT ON TABLE parameter IS 'parameter of the company';



CREATE TABLE parm_code (
    p_code text NOT NULL,
    p_value text,
    p_comment text
);



CREATE TABLE parm_money (
    pm_id integer DEFAULT nextval(('s_currency'::text)::regclass),
    pm_code character(3) NOT NULL,
    pm_rate numeric(20,4)
);



COMMENT ON TABLE parm_money IS 'Currency conversion';



CREATE TABLE parm_periode (
    p_id integer DEFAULT nextval(('s_periode'::text)::regclass) NOT NULL,
    p_start date NOT NULL,
    p_end date NOT NULL,
    p_exercice text DEFAULT to_char(now(), 'YYYY'::text) NOT NULL,
    p_closed boolean DEFAULT false,
    p_central boolean DEFAULT false,
    CONSTRAINT parm_periode_check CHECK ((p_end >= p_start))
);



COMMENT ON TABLE parm_periode IS 'Periode definition';


SET default_with_oids = false;


CREATE TABLE parm_poste (
    p_value account_type NOT NULL,
    p_type text NOT NULL
);



COMMENT ON TABLE parm_poste IS 'Contains data for finding is the type of the account (asset)';



CREATE SEQUENCE plan_analytique_pa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE TABLE plan_analytique (
    pa_id integer DEFAULT nextval('plan_analytique_pa_id_seq'::regclass) NOT NULL,
    pa_name text DEFAULT 'Sans Nom'::text NOT NULL,
    pa_description text
);



COMMENT ON TABLE plan_analytique IS 'Plan Analytique (max 5)';



CREATE SEQUENCE poste_analytique_po_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE TABLE poste_analytique (
    po_id integer DEFAULT nextval('poste_analytique_po_id_seq'::regclass) NOT NULL,
    po_name text NOT NULL,
    pa_id integer NOT NULL,
    po_amount numeric(20,4) DEFAULT 0.0 NOT NULL,
    po_description text,
    ga_id character varying(10)
);



COMMENT ON TABLE poste_analytique IS 'Poste Analytique';



CREATE TABLE quant_purchase (
    qp_id integer DEFAULT nextval(('s_quantity'::text)::regclass) NOT NULL,
    qp_internal text NOT NULL,
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
    qp_dep_priv numeric(20,4) DEFAULT 0.0
);


SET default_with_oids = true;


CREATE TABLE quant_sold (
    qs_id integer DEFAULT nextval(('s_quantity'::text)::regclass) NOT NULL,
    qs_internal text NOT NULL,
    qs_fiche integer NOT NULL,
    qs_quantite numeric(20,4) NOT NULL,
    qs_price numeric(20,4),
    qs_vat numeric(20,4),
    qs_vat_code integer,
    qs_client integer NOT NULL,
    qs_valid character(1) DEFAULT 'Y'::bpchar NOT NULL,
    j_id integer NOT NULL
);



COMMENT ON TABLE quant_sold IS 'Contains about invoice for customer';



CREATE SEQUENCE s_attr_def
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_cbc
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_central
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_central_order
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_centralized
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_currency
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_fdef
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_fiche
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_fiche_def_ref
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_form
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_formdef
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_grpt
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_idef
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_internal
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_invoice
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_isup
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jnt_fic_att_value
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_2
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_3
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_4
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_def
    START WITH 5
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_op
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_pj1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_pj2
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_pj3
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_pj4
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrn_rapt
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrnaction
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_jrnx
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_periode
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_quantity
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_stock_goods
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_tva
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_user_act
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE s_user_jrn
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_bud_hypothese_bh_id
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_10
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_2
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_20
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_21
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_22
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_3
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_4
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_5
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_6
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_7
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_8
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE SEQUENCE seq_doc_type_9
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



CREATE TABLE stock_goods (
    sg_id integer DEFAULT nextval(('s_stock_goods'::text)::regclass) NOT NULL,
    j_id integer,
    f_id integer NOT NULL,
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



COMMENT ON TABLE stock_goods IS 'About the goods';



CREATE TABLE tmp_pcmn (
    pcm_val account_type NOT NULL,
    pcm_lib text,
    pcm_val_parent account_type DEFAULT 0,
    pcm_type text
);



COMMENT ON TABLE tmp_pcmn IS 'Plan comptable minimum normalisé';



CREATE SEQUENCE todo_list_tl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


SET default_with_oids = false;


CREATE TABLE todo_list (
    tl_id integer DEFAULT nextval('todo_list_tl_id_seq'::regclass) NOT NULL,
    tl_date date NOT NULL,
    tl_title text NOT NULL,
    tl_desc text,
    use_login text NOT NULL
);



COMMENT ON TABLE todo_list IS 'Todo list';


SET default_with_oids = true;


CREATE TABLE tva_rate (
    tva_id integer DEFAULT nextval('s_tva'::regclass) NOT NULL,
    tva_label text NOT NULL,
    tva_rate numeric(8,4) DEFAULT 0.0 NOT NULL,
    tva_comment text,
    tva_poste text
);



COMMENT ON TABLE tva_rate IS 'Rate of vat';



CREATE TABLE user_local_pref (
    user_id text NOT NULL,
    parameter_type text NOT NULL,
    parameter_value text
);



COMMENT ON TABLE user_local_pref IS 'The user''s local parameter ';



COMMENT ON COLUMN user_local_pref.user_id IS 'user''s login ';



COMMENT ON COLUMN user_local_pref.parameter_type IS 'the type of parameter ';



COMMENT ON COLUMN user_local_pref.parameter_value IS 'the value of parameter ';



CREATE TABLE user_sec_act (
    ua_id integer DEFAULT nextval(('s_user_act'::text)::regclass) NOT NULL,
    ua_login text,
    ua_act_id integer
);


SET default_with_oids = false;


CREATE TABLE user_sec_extension (
    use_id integer NOT NULL,
    ex_id integer NOT NULL,
    use_login text NOT NULL,
    use_access character(1) DEFAULT 0 NOT NULL
);



COMMENT ON TABLE user_sec_extension IS 'Security for extension';



CREATE SEQUENCE user_sec_extension_use_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



ALTER SEQUENCE user_sec_extension_use_id_seq OWNED BY user_sec_extension.use_id;


SET default_with_oids = true;


CREATE TABLE user_sec_jrn (
    uj_id integer DEFAULT nextval(('s_user_jrn'::text)::regclass) NOT NULL,
    uj_login text,
    uj_jrn_id integer,
    uj_priv text
);



CREATE TABLE version (
    val integer
);



CREATE VIEW vw_client AS
    SELECT a.f_id, a.av_text AS name, a1.av_text AS quick_code, b.av_text AS tva_num, c.av_text AS poste_comptable, d.av_text AS rue, e.av_text AS code_postal, f.av_text AS pays, g.av_text AS telephone, h.av_text AS email FROM (((((((((SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 1)) a JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 13)) b USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 23)) a1 USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 5)) c USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 14)) d USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 15)) e USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 16)) f USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 17)) g USING (f_id)) LEFT JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 18)) h USING (f_id)) WHERE (a.frd_id = 9);



CREATE VIEW vw_fiche_attr AS
    SELECT a.f_id, a.fd_id, a.av_text AS vw_name, b.av_text AS vw_sell, c.av_text AS vw_buy, d.av_text AS tva_code, tva_rate.tva_id, tva_rate.tva_rate, tva_rate.tva_label, e.av_text AS vw_addr, f.av_text AS vw_cp, j.av_text AS quick_code, h.av_text AS vw_description, i.av_text AS tva_num, fiche_def.frd_id FROM (((((((((((SELECT fiche.f_id, fiche.fd_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 1)) a LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 6)) b ON ((a.f_id = b.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 7)) c ON ((a.f_id = c.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 2)) d ON ((a.f_id = d.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 14)) e ON ((a.f_id = e.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 15)) f ON ((a.f_id = f.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 23)) j ON ((a.f_id = j.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 9)) h ON ((a.f_id = h.f_id))) LEFT JOIN (SELECT fiche.f_id, attr_value.av_text FROM (((fiche JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) JOIN attr_def USING (ad_id)) WHERE (jnt_fic_att_value.ad_id = 13)) i ON ((a.f_id = i.f_id))) LEFT JOIN tva_rate ON ((d.av_text = (tva_rate.tva_id)::text))) JOIN fiche_def USING (fd_id));



CREATE VIEW vw_fiche_def AS
    SELECT jnt_fic_attr.fd_id, jnt_fic_attr.ad_id, attr_def.ad_text, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def.frd_id FROM ((fiche_def JOIN jnt_fic_attr USING (fd_id)) JOIN attr_def ON ((attr_def.ad_id = jnt_fic_attr.ad_id)));



COMMENT ON VIEW vw_fiche_def IS 'all the attributs for	card family';



CREATE VIEW vw_fiche_min AS
    SELECT attr_min.frd_id, attr_min.ad_id, attr_def.ad_text, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base FROM ((attr_min JOIN attr_def USING (ad_id)) JOIN fiche_def_ref USING (frd_id));



CREATE VIEW vw_poste_qcode AS
    SELECT a.f_id, a.av_text AS j_poste, b.av_text AS j_qcode FROM ((SELECT jnt_fic_att_value.f_id, attr_value.av_text FROM (attr_value JOIN jnt_fic_att_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 5)) a JOIN (SELECT jnt_fic_att_value.f_id, attr_value.av_text FROM (attr_value JOIN jnt_fic_att_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 23)) b USING (f_id));



CREATE VIEW vw_supplier AS
    SELECT a.f_id, a.av_text AS name, a1.av_text AS quick_code, b.av_text AS tva_num, c.av_text AS poste_comptable, d.av_text AS rue, e.av_text AS code_postal, f.av_text AS pays, g.av_text AS telephone, h.av_text AS email FROM (((((((((SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 1)) a JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 13)) b USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 23)) a1 USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 5)) c USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 14)) d USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 15)) e USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 16)) f USING (f_id)) JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 17)) g USING (f_id)) LEFT JOIN (SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text FROM ((((fiche JOIN fiche_def USING (fd_id)) JOIN fiche_def_ref USING (frd_id)) JOIN jnt_fic_att_value USING (f_id)) JOIN attr_value USING (jft_id)) WHERE (jnt_fic_att_value.ad_id = 18)) h USING (f_id)) WHERE (a.frd_id = 8);













































































































































































































































CREATE UNIQUE INDEX attr_value_jft_id ON attr_value USING btree (jft_id);



CREATE UNIQUE INDEX fd_id_ad_id_x ON jnt_fic_attr USING btree (fd_id, ad_id);



CREATE INDEX fk_stock_goods_f_id ON stock_goods USING btree (f_id);



CREATE INDEX fk_stock_goods_j_id ON stock_goods USING btree (j_id);



CREATE UNIQUE INDEX idx_case ON format_csv_banque USING btree (upper(name));



CREATE INDEX idx_qs_internal ON quant_sold USING btree (qs_internal);



CREATE INDEX jnt_fic_att_value_fd_id_idx ON jnt_fic_att_value USING btree (f_id);



CREATE INDEX jnt_fic_attr_fd_id_idx ON jnt_fic_attr USING btree (fd_id);



CREATE UNIQUE INDEX k_ag_ref ON action_gestion USING btree (ag_ref);



CREATE UNIQUE INDEX uj_login_uj_jrn_id ON user_sec_jrn USING btree (uj_login, uj_jrn_id);



CREATE UNIQUE INDEX ux_po_name ON poste_analytique USING btree (po_name);



CREATE UNIQUE INDEX x_jrn_jr_id ON jrn USING btree (jr_id);



CREATE INDEX x_mt ON jrn USING btree (jr_mt);



CREATE UNIQUE INDEX x_periode ON parm_periode USING btree (p_start, p_end);



CREATE INDEX x_poste ON jrnx USING btree (j_poste);



CREATE TRIGGER action_gestion_t_insert_update
    BEFORE INSERT OR UPDATE ON action_gestion
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.action_gestion_ins_upd();



COMMENT ON TRIGGER action_gestion_t_insert_update ON action_gestion IS 'Truncate the column ag_title to 70 char';



CREATE TRIGGER document_modele_validate
    BEFORE INSERT OR UPDATE ON document_modele
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.t_document_modele_validate();



CREATE TRIGGER document_validate
    BEFORE INSERT OR UPDATE ON document
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.t_document_validate();



CREATE TRIGGER fiche_def_ins_upd
    BEFORE INSERT OR UPDATE ON fiche_def
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.fiche_def_ins_upd();



CREATE TRIGGER info_def_ins_upd_t
    BEFORE INSERT OR UPDATE ON info_def
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.info_def_ins_upd();



CREATE TRIGGER t_check_balance
    AFTER INSERT OR UPDATE ON jrn
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.proc_check_balance();



CREATE TRIGGER t_check_jrn
    BEFORE INSERT OR DELETE ON jrn
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrn_check_periode();



CREATE TRIGGER t_group_analytic_del
    BEFORE DELETE ON groupe_analytique
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.group_analytique_del();



CREATE TRIGGER t_group_analytic_ins_upd
    BEFORE INSERT OR UPDATE ON groupe_analytique
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.group_analytic_ins_upd();



CREATE TRIGGER t_jrn_def_add_periode
    AFTER INSERT ON jrn_def
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrn_def_add();



CREATE TRIGGER t_jrn_def_delete
    BEFORE DELETE ON jrn_def
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrn_def_delete();



CREATE TRIGGER t_jrn_del
    BEFORE DELETE ON jrn
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrn_del();



CREATE TRIGGER t_jrnx_del
    BEFORE DELETE ON jrnx
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.jrnx_del();



CREATE TRIGGER t_plan_analytique_ins_upd
    BEFORE INSERT OR UPDATE ON plan_analytique
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.plan_analytic_ins_upd();



CREATE TRIGGER t_poste_analytique_ins_upd
    BEFORE INSERT OR UPDATE ON poste_analytique
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.poste_analytique_ins_upd();



CREATE TRIGGER t_tmp_pcmn_ins
    BEFORE INSERT ON tmp_pcmn
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.tmp_pcmn_ins();



CREATE TRIGGER trg_extension_ins_upd
    BEFORE INSERT OR UPDATE ON extension
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.extension_ins_upd();



CREATE TRIGGER trigger_document_type_i
    AFTER INSERT ON document_type
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.t_document_type_insert();



CREATE TRIGGER trigger_jrn_def_sequence_i
    AFTER INSERT ON jrn_def
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.t_jrn_def_sequence();



CREATE TRIGGER trim_quote
    BEFORE INSERT OR UPDATE ON import_tmp
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.trim_cvs_quote();



CREATE TRIGGER trim_space
    BEFORE INSERT OR UPDATE ON format_csv_banque
    FOR EACH ROW
    EXECUTE PROCEDURE comptaproc.trim_space_format_csv_banque();
