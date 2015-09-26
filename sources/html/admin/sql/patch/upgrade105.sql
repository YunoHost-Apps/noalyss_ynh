begin;

CREATE OR REPLACE FUNCTION comptaproc.insert_quick_code(nf_id integer, tav_text text)
  RETURNS integer AS
$BODY$
	declare
	ns integer;
	nExist integer;
	tText text;
	begin
	tText := lower(trim(tav_text));
	tText := replace(tText,' ','');
	tText := translate(tText,E' $€µ£%.+-/\\!(){}(),;_&|"#''^<>*','');
	tText := translate(tText,E'éèêëàâäïîüûùöôç','eeeeaaaiiuuuooc');


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
$BODY$
  LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION comptaproc.update_quick_code(njft_id integer, tav_text text)
  RETURNS integer AS
$BODY$
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

	tText := trim(lower(tav_text));
	tText := replace(tText,' ','');
	tText := translate(tText,E' $€µ£%.+-/\\!(){}(),;_&|"#''^<>*','');
	tText := translate(tText,E'éèêëàâäïîüûùöôç','eeeeaaaiiuuuooc');
	tText := upper(tText);
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
$BODY$
  LANGUAGE plpgsql;


alter table document_state add s_status char(1);

update document_state set s_status='C' where s_id in (1,4);

update version set val=106;

commit;
