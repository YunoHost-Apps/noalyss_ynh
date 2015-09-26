begin;
CREATE or replace FUNCTION insert_quant_sold
	(p_internal text, 
	p_fiche character varying, 
	p_quant integer, 
	p_price numeric, 
	p_vat numeric, 
	p_vat_code integer, 
	p_client character varying) 
RETURNS void
AS
  $body$
declare 
	fid_client integer;
	fid_good   integer;
begin
	select f_id into fid_client from 
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(p_client);

	select f_id into fid_good from 
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(p_fiche);


	insert into quant_sold
		(qs_internal,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client) 
	values
		(p_internal,fid_good,p_quant,p_price,p_vat,p_vat_code,fid_client);
	return;
end;	
 $body$  LANGUAGE plpgsql;

-- add quick code for contact

insert into attr_min (frd_id,ad_id) 
	select distinct 16,23 
	from attr_min 
	where not exists (select * from attr_min where ad_id=23 and frd_id=16);


insert into jnt_fic_attr (fd_id,ad_id) 
       select fd_id,23 from fiche_def where frd_id=16 
       and not exists (select *
       from jnt_fic_attr join fiche_def using (fd_id) 
       where frd_id=16 and ad_id=23);


CREATE or replace FUNCTION update_quick_code(njft_id integer, tav_text text) RETURNS integer
    AS $body$
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
$body$
    LANGUAGE plpgsql;

    
update parm_periode set p_end = p_start where p_end is null;     
alter table parm_periode alter p_end set not null;

create or replace function drop_it (p_constraint varchar) 
returns void as 
$body$
declare 
-- drop a constraint if it exists
	nCount integer;
begin
	select count(*) into nCount from pg_constraint where conname=p_constraint;
	if nCount = 1 then
	execute 'alter table parm_periode drop constraint '||p_constraint ;
	end if;
end;
$body$ language plpgsql;

select drop_it('parm_periode_p_start_key');
create unique index x_periode on parm_periode (p_start,p_end);

update version set val=21;
commit;
