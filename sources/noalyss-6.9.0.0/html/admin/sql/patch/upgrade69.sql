begin;

CREATE OR REPLACE FUNCTION account_update(p_f_id integer, p_account text)
  RETURNS integer AS
$BODY$
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
			select count(*) into nCount from tmp_pcmn where pcm_val=p_account::poste_comptable;
			if nCount = 0 then
			select av_text into sName from 
				attr_value join jnt_fic_att_value using (jft_id)
				where
				ad_id=1 and f_id=p_f_id;
			nParent:=account_parent(p_account::poste_comptable);
			insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (p_account::poste_comptable,sName,nParent);
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
$BODY$
LANGUAGE 'plpgsql' ;

update version set val=70;
commit;
