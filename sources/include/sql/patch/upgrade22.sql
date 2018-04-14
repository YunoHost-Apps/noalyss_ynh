begin;
create or replace function account_insert (p_f_id integer, p_account poste_comptable)
RETURNS int4 AS
$body$
declare
nParent tmp_pcmn.pcm_val_parent%type;
sName varchar;
nNew tmp_pcmn.pcm_val%type;
bAuto bool;
nFd_id integer;
nCount integer;
begin
	
	if length(trim(p_account)) != 0 then
	raise notice 'p_account is not empty';
		select *  into nCount from tmp_pcmn where pcm_val=p_account;
		if nCount !=0  then
			raise notice 'this account exists in tmp_pcmn ';
			perform attribut_insert(p_f_id,5,to_char(p_account,'999999999999'));
                   else 
                       -- account doesn't exist, create it
			select av_text into sName from 
				attr_value join jnt_fic_att_value using (jft_id)
			where	
			ad_id=1 and f_id=p_f_id;

			nParent:=account_parent(p_account);
			insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) 
				values (p_account,sName,nParent);
			perform attribut_insert(p_f_id,5,to_char(nNew,'999999999999'));
	
		end if;		
	else 
	raise notice 'p_account is  empty';
		select fd_id into nFd_id from fiche where f_id=p_f_id;
		bAuto:= account_auto(nFd_id);
		if bAuto = true then
			raise notice 'account generated automatically';
			nNew:=account_compute(p_f_id);
			raise notice 'nNew %', nNew;
			select av_text into sName from 
			attr_value join jnt_fic_att_value using (jft_id)
			where
			ad_id=1 and f_id=p_f_id;
			nParent:=account_parent(nNew);
			perform account_add  (nNew,sName);
			perform attribut_insert(p_f_id,5,to_char(nNew,'999999999999'));
	
		else 
			 perform attribut_insert(p_f_id,5,null);
		end if;
	end if;
		
return 0;
end;
$body$   
LANGUAGE 'plpgsql' VOLATILE;

-- Function: attribut_insert(p_f_id int4, p_ad_id int4, p_value "varchar")

-- DROP FUNCTION attribut_insert(p_f_id int4, p_ad_id int4, p_value "varchar");

CREATE OR REPLACE FUNCTION attribut_insert(p_f_id int4, p_ad_id int4, p_value "varchar")
  RETURNS void AS
$BODY$
declare 
	n_jft_id integer;
begin
	select nextval('s_jnt_fic_att_value') into n_jft_id;
	 insert into jnt_fic_att_value (jft_id,f_id,ad_id) values (n_jft_id,p_f_id,p_ad_id);
	 insert into attr_value (jft_id,av_text) values (n_jft_id,trim(p_value));
return;
end;
$BODY$ LANGUAGE 'plpgsql' VOLATILE;

update attr_value set av_text=trim(av_text);

update jrnx set j_qcode = B.av_text from
         (select f_id,av_text from attr_value join jnt_fic_att_value using (jft_id)
                where ad_id=5) as A
        join ( select f_id,av_text from attr_value join jnt_fic_att_value using (jft_id)
                where ad_id=23) as B  using(f_id) where j_poste=a.av_text;


update jrnx set j_qcode = upper(j_qcode);

update version set val=23;
commit;
