begin;
CREATE OR REPLACE FUNCTION comptaproc.insert_quick_code(nf_id integer, tav_text text)
  RETURNS integer AS
$BODY$
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
$BODY$
LANGUAGE plpgsql;

update fiche_detail set ad_value=replace(ad_value,'''','-') where ad_id=23;

CREATE OR REPLACE FUNCTION comptaproc.update_quick_code(njft_id integer, tav_text text)
  RETURNS integer AS
$BODY$
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
$BODY$
  LANGUAGE plpgsql;

DROP FUNCTION IF EXISTS comptaproc.insert_quant_purchase(text, numeric, character varying, numeric, numeric, numeric, integer, numeric, numeric, numeric, numeric, character varying, numeric);
DROP FUNCTION IF EXISTS comptaproc.insert_quant_purchase(text, numeric, text, numeric, numeric, numeric, integer, numeric, numeric, numeric, numeric, text, numeric);
DROP FUNCTION IF EXISTS comptaproc.insert_quant_sold(text, numeric, character varying, numeric, numeric, numeric, integer, character varying, numeric);

CREATE OR REPLACE FUNCTION comptaproc.insert_quant_purchase(p_internal text, 
    p_j_id numeric, 
    p_fiche character varying, 
    p_quant numeric, p_price numeric, 
    p_vat numeric, p_vat_code integer, 
    p_nd_amount numeric, p_nd_tva numeric, 
    p_nd_tva_recup numeric, 
    p_dep_priv numeric, 
    p_client character varying, 
    p_tva_sided numeric,
    p_price_unit numeric)
  RETURNS void AS
$BODY$
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
 $BODY$
  LANGUAGE plpgsql;

update version set val=123;

commit;
