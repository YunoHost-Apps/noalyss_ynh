begin;
drop function insert_jrnx(varchar,numeric,integer,integer,integer,boolean,text,integer,text);

CREATE or replace FUNCTION insert_jrnx(p_date character varying, p_montant numeric, p_poste poste_comptable, p_grpt integer, p_jrn_def integer, p_debit boolean, p_tech_user text, p_tech_per integer, p_qcode text) RETURNS void
    AS $$
declare
	sCode varchar;
	nCount_qcode integer;
begin
	sCode=trim(p_qcode);

	-- if p_qcode is empty try to find one	
	if length(sCode) = 0 or p_qcode is null then

		select count(*) into nCount_qcode 	
			from vw_poste_qcode where j_poste=p_poste::text;
	-- if we find only one q_code for a accountancy account
	-- then retrieve it
		if nCount_qcode = 1 then
			select j_qcode into sCode 
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
$$ 
LANGUAGE plpgsql;

update version set val=48;
commit;
