begin;

alter table jnt_fic_att_value add ad_value text;
update jnt_fic_att_value set ad_value=av_text from attr_value where jnt_fic_att_value.jft_id=attr_value.jft_id;
DROP VIEW vw_supplier;
DROP VIEW vw_poste_qcode;
DROP VIEW vw_fiche_attr;
DROP VIEW vw_fiche_name;
DROP VIEW vw_client;
alter table jnt_fic_att_value rename to fiche_detail;

drop table attr_value;

CREATE OR REPLACE VIEW vw_poste_qcode AS 
 SELECT c.f_id, a.ad_value AS j_poste, b.ad_value AS j_qcode
   FROM 
	fiche c
	left join 
   ( SELECT f_id,ad_value from fiche_Detail
     WHERE ad_id = 5) a using(f_id)
   left JOIN ( select f_id,ad_value from fiche_detail
     WHERE ad_id = 23) b USING (f_id);
     
     
CREATE OR REPLACE VIEW vw_client AS 
 SELECT fiche.f_id, a1.ad_value AS name, a.ad_value AS quick_code, b.ad_value AS tva_num, c.ad_value AS poste_comptable, d.ad_value AS rue, e.ad_value AS code_postal, f.ad_value AS pays, g.ad_value AS telephone, h.ad_value AS email
   FROM fiche
   join fiche_def using (fd_id)
   join fiche_def_ref using(frd_id)
  left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 1) a1 USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 13) b USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 23) a USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 5) c USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 14) d USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 15) e USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 16) f USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 17) g USING (f_id)
   LEFT JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from  fiche_detail 
  WHERE ad_id = 18) h USING (f_id)
  WHERE fiche_def_ref.frd_id = 9;

CREATE OR REPLACE VIEW vw_fiche_name AS 
 SELECT f_id, ad_value  AS name
   FROM fiche_detail
  WHERE ad_id = 1;
  
  CREATE OR REPLACE VIEW vw_supplier AS 
 SELECT fiche.f_id, a1.ad_value AS name, a.ad_value AS quick_code, b.ad_value AS tva_num, c.ad_value AS poste_comptable, d.ad_value AS rue, e.ad_value AS code_postal, f.ad_value AS pays, g.ad_value AS telephone, h.ad_value AS email
   FROM fiche
   join fiche_def using (fd_id)
   join fiche_def_ref using(frd_id)
  left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 1) a1 USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 13) b USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 23) a USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 5) c USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 14) d USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 15) e USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 16) f USING (f_id)
   left JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from fiche_detail 
  WHERE ad_id = 17) g USING (f_id)
   LEFT JOIN ( SELECT jft_id, F_ID, ad_id, ad_value
   from  fiche_detail 
  WHERE ad_id = 18) h USING (f_id)
  WHERE fiche_def_ref.frd_id = 8;

  
CREATE OR REPLACE VIEW vw_fiche_attr AS 
 SELECT a.f_id, a.fd_id, a.ad_value AS vw_name, k.ad_value as vw_first_name, b.ad_value AS vw_sell, c.ad_value AS vw_buy, d.ad_value AS tva_code, tva_rate.tva_id, tva_rate.tva_rate, tva_rate.tva_label, e.ad_value AS vw_addr, f.ad_value AS vw_cp, j.ad_value AS quick_code, h.ad_value AS vw_description, i.ad_value AS tva_num, fiche_def.frd_id
   FROM ( SELECT fiche.f_id, fiche.fd_id, ad_value
           FROM fiche
    left join fiche_detail using (f_id)
  WHERE ad_id = 1) a
   LEFT JOIN ( 
   select f_id ,ad_value from fiche_detail
  WHERE ad_id = 6) b ON a.f_id = b.f_id
   LEFT JOIN ( select f_id,ad_value  from fiche_detail
  WHERE ad_id = 7) c ON a.f_id = c.f_id
   LEFT JOIN ( select f_id,ad_value  from fiche_detail
  WHERE ad_id = 2) d ON a.f_id = d.f_id
   LEFT JOIN ( select f_id,ad_value  from fiche_detail
  WHERE ad_id = 14) e ON a.f_id = e.f_id
   LEFT JOIN ( select f_id,ad_value  from fiche_detail
  WHERE ad_id = 15) f ON a.f_id = f.f_id
   LEFT JOIN ( select f_id,ad_value  from fiche_detail
  WHERE ad_id = 23) j ON a.f_id = j.f_id
   LEFT JOIN ( select f_id,ad_value  from fiche_detail
  WHERE ad_id = 9) h ON a.f_id = h.f_id
   LEFT JOIN ( select f_id,ad_value  from fiche_detail
  WHERE ad_id = 13) i ON a.f_id = i.f_id
   LEFT JOIN ( select f_id,ad_value  from fiche_detail
  WHERE ad_id = 32) k ON a.f_id = k.f_id
   LEFT JOIN tva_rate ON d.ad_value = tva_rate.tva_id::text
   JOIN fiche_def USING (fd_id);
     
     
--
-- Name: account_insert(integer, text); Type: FUNCTION; Schema: comptaproc; Owner: dany
--

CREATE OR REPLACE FUNCTION COMPTAPROC.account_insert(p_f_id integer, p_account text) RETURNS integer
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
		if bAuto = true and sClass_base similar to '^[[:digit:]]*$'  then
			raise info 'account generated automatically';
			nNew:=account_compute(p_f_id);
			raise info 'nNew %', nNew;
			select ad_value into sName from
				fiche_detail
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
--
-- Name: account_update(integer, public.account_type); Type: FUNCTION; Schema: comptaproc; Owner: dany
--

CREATE OR REPLACE FUNCTION COMPTAPROC.account_update(p_f_id integer, p_account public.account_type) RETURNS integer
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
		end if;
	end if;
	
	update fiche_detail set ad_value=p_account where f_id=p_f_id and ad_id=5 ;

return 0;
end;
$$
LANGUAGE plpgsql;

--
-- Name: attribut_insert(integer, integer, character varying); Type: FUNCTION; Schema: comptaproc; Owner: dany
--

CREATE OR REPLACE FUNCTION COMPTAPROC.attribut_insert(p_f_id integer, p_ad_id integer, p_value character varying) RETURNS void
    AS $$
begin
	insert into fiche_detail (f_id,ad_id, ad_value) values (p_f_id,p_ad_id,p_value);
	
return;
end;
$$
LANGUAGE plpgsql;

--
-- Name: fiche_attribut_synchro(integer); Type: FUNCTION; Schema: comptaproc; Owner: dany
--

CREATE OR REPLACE FUNCTION COMPTAPROC.fiche_attribut_synchro(p_fd_id integer) RETURNS void
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
$$
LANGUAGE plpgsql;

--
-- Name: insert_quant_sold(text, numeric, character varying, numeric, numeric, numeric, integer, character varying); Type: FUNCTION; Schema: comptaproc; Owner: dany
--

CREATE OR REPLACE FUNCTION COMPTAPROC.insert_quant_sold(p_internal text, p_jid numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_client character varying) RETURNS void
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
		(qs_internal,j_id,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client,qs_valid)
	values
		(p_internal,p_jid,fid_good,p_quant,p_price,p_vat,p_vat_code,fid_client,'Y');
	return;
end;
 $$
LANGUAGE plpgsql;

--
-- Name: insert_quant_purchase(text, numeric, character varying, numeric, numeric, numeric, integer, numeric, numeric, numeric, numeric, character varying); Type: FUNCTION; Schema: comptaproc; Owner: dany
--

CREATE OR REPLACE FUNCTION COMPTAPROC.insert_quant_purchase(p_internal text, p_j_id numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_nd_amount numeric, p_nd_tva numeric, p_nd_tva_recup numeric, p_dep_priv numeric, p_client character varying) RETURNS void
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

--
-- Name: insert_quick_code(integer, text); Type: FUNCTION; Schema: comptaproc; Owner: dany
--

CREATE OR REPLACE FUNCTION COMPTAPROC.insert_quick_code(nf_id integer, tav_text text) RETURNS integer
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
$$
LANGUAGE plpgsql;


--
-- Name: update_quick_code(integer, text); Type: FUNCTION; Schema: comptaproc; Owner: dany
--

CREATE OR REPLACE FUNCTION COMPTAPROC.update_quick_code(njft_id integer, tav_text text) RETURNS integer
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
$$
LANGUAGE plpgsql;


--
-- Name: jrnx_ins(); Type: FUNCTION; Schema: comptaproc; Owner: dany
--

CREATE OR REPLACE FUNCTION COMPTAPROC.jrnx_ins() RETURNS trigger
    AS $$
declare
n_fid bigint;
begin

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
$$
LANGUAGE plpgsql;

update version set val=91;
commit;
