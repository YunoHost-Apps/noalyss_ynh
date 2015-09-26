begin;
-- index needed
create unique index attr_value_jft_id on attr_value (jft_id);
-- add quick code
insert into attr_def (ad_id,ad_text) values(23,'Quick Code');
-- update existing card & template
insert into attr_min select frd_id,23 from fiche_def_ref;
insert into jnt_fic_attr select fd_id,23 from fiche_Def;
insert into jnt_fic_att_value(jft_id,f_id,ad_id) select nextval('s_jnt_fic_att_value')+200,f_id,23 from fiche;
-- generate a quick code
insert into attr_value select jft_id,'FID'||f_id from jnt_fic_att_value join fiche using(f_id) where ad_id=23;
-- add quick code to jrnx
alter table jrnx add j_qcode text;

update jrnx set j_qcode = B.av_text from
	 (select f_id,av_text from attr_value join jnt_fic_att_value using (jft_id)
                where ad_id=5) as A
        join ( select f_id,av_text from attr_value join jnt_fic_att_value using (jft_id)
                where ad_id=23) as B  using(f_id) where j_poste=a.av_text;

create or replace function insert_jrnx (
	p_date	      	varchar,
	p_montant 	jrnx.j_montant%TYPE,	
	p_poste  	jrnx.j_poste%TYPE,
	p_grpt		jrnx.j_grpt%type,
	p_jrn_def	jrnx.j_jrn_def%type,
	p_debit		jrnx.j_debit%type,
	p_tech_user	jrnx.j_tech_user%type,
	p_tech_per	jrnx.j_tech_per%type,
	p_qcode		jrnx.j_qcode%type 
) returns void as $body$
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
			select j_qcode into sCode 
			from vw_poste_qcode where j_poste=p_poste;
		else 
		 sCode=NULL;
		end if;
		
	end if;
	if p_montant = 0.0 then 
		return;	
	end if;
	insert into jrnx 
	(
		j_date,
		j_montant, 	
		j_poste,
		j_grpt, 
		j_jrn_def,
		j_debit,
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
		p_tech_user,
		p_tech_per,
		sCode
	);

return;
end;
$body$
  language plpgsql;



-- Function: update_quick_code(njft_id int4, tav_text text)

-- DROP FUNCTION update_quick_code(int4, text);

CREATE OR REPLACE FUNCTION update_quick_code(njft_id int4,tav_text text)
  RETURNS int4 AS $BODY$
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
			ad_id=23 and av_text=upper(tText);

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
	update jrnx set j_qcode=tText where j_qcode = old_qcode;
	return ns;
	end;
$BODY$
  LANGUAGE plpgsql VOLATILE;





-- View: "vw_fiche_def"

DROP VIEW vw_fiche_def;

CREATE OR REPLACE VIEW vw_fiche_def AS 
 SELECT jnt_fic_attr.fd_id, jnt_fic_attr.ad_id, 
		attr_def.ad_text, 
	        attr_value.av_text,
		fiche_def.fd_class_base, 
		fiche_def.fd_label, fiche_def.fd_create_account, fiche_def.frd_id
   FROM jnt_fic_att_value
   JOIN attr_value using (jft_id)
   join fiche using (f_id)
   join jnt_fic_attr using (fd_id)
   JOIN attr_def on  (attr_def.ad_id=jnt_fic_attr.ad_id)
   JOIN fiche_def USING (fd_id);

COMMENT ON VIEW vw_fiche_def IS 'all the attributs for  card family';



create or replace function insert_quant_sold (
	p_internal quant_sold.qs_internal%type,
	p_fiche varchar,
	p_quant 	quant_sold.qs_quantite%type,
	p_price quant_sold.qs_price%type,
	p_vat 	quant_sold.qs_vat%type,
	p_vat_code quant_sold.qs_vat_code%type,
	p_client varchar) returns void as $body$
declare 
	fid_client integer;
	fid_good   integer;
begin
	select f_id into fid_client from 
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=p_client;

	select f_id into fid_good from 
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=p_fiche;


	insert into quant_sold
		(qs_internal,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client) 
	values
		(p_internal,fid_good,p_quant,p_price,p_vat,p_vat_code,fid_client);
	return;
end;	
$body$
  LANGUAGE plpgsql VOLATILE;



-- Function: insert_quick_code(nf_id int4, tav_text text)

-- DROP FUNCTION insert_quick_code(int4, text);

CREATE OR REPLACE FUNCTION insert_quick_code(nf_id int4, tav_text text)
  RETURNS int4 AS $BODY$
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
$BODY$
  LANGUAGE plpgsql VOLATILE;




DROP VIEW vw_fiche_attr;
CREATE view vw_fiche_attr as 
SELECT a.f_id, a.fd_id, a.av_text AS vw_name, b.av_text AS vw_sell, c.av_text AS vw_buy, d.av_text AS tva_code, tva_rate.tva_id, tva_rate.tva_rate, tva_rate.tva_label, e.av_text AS vw_addr, f.av_text AS vw_cp, j.av_text as quick_code,fiche_def.frd_id
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
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
   FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 23) j ON a.f_id = j.f_id
   LEFT JOIN tva_rate ON d.av_text = tva_rate.tva_id::text
   JOIN fiche_def USING (fd_id);



create view vw_poste_qcode 
	as  
	select A.f_id,a.av_text as j_poste,b.av_text as j_qcode
        from (select f_id,av_text from attr_value join jnt_fic_att_value using (jft_id)
                where ad_id=5) as A
        join ( select f_id,av_text from attr_value join jnt_fic_att_value using (jft_id)
                where ad_id=23) as B  using(f_id)
;








update version set val=10;
commit;
