begin;
CREATE or replace FUNCTION account_compute(p_f_id integer) RETURNS poste_comptable
    AS $$
declare
        class_base poste_comptable;
        maxcode poste_comptable;
begin
        select fd_class_base into class_base
        from
                fiche_def join fiche using (fd_id)
        where
                f_id=p_f_id;
        raise notice 'account_compute class base %',class_base;
        select count (pcm_val) into maxcode from tmp_pcmn where pcm_val_parent = class_base;
        if maxcode = 0  then
                maxcode:=class_base;
        else
                select max (pcm_val) into maxcode from tmp_pcmn where pcm_val_parent = class_base;
        end if;
        if maxcode = class_base then
                maxcode:=class_base*1000;
        end if;
        maxcode:=maxcode+1;
        raise notice 'account_compute Max code %',maxcode;
        return maxcode;
end;
$$
    LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION account_insert(p_f_id integer, p_account poste_comptable) RETURNS integer
    AS $$
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
			perform attribut_insert(p_f_id,5,to_char(p_account,'999999999999999999999999'));
                   else 
                       -- account doesn't exist, create it
			select av_text into sName from 
				attr_value join jnt_fic_att_value using (jft_id)
			where	
			ad_id=1 and f_id=p_f_id;

			nParent:=account_parent(p_account);
			insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (p_account,sName,nParent);
			perform attribut_insert(p_f_id,5,to_char(p_account,'999999999999999999999999'));
	
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
			perform attribut_insert(p_f_id,5,to_char(nNew,'999999999999999999999999'));
	
		else 
                -- if there is an account_base then it is the default
 	              select fd_class_base into nNew from fiche_def join fiche using (fd_id) where f_id=p_f_id;
			if nNew is null or length(trim(nNew)) = 0 then	 
				raise notice 'count is null';
				 perform attribut_insert(p_f_id,5,null);
			else
				 perform attribut_insert(p_f_id,5,to_char(nNew,'999999999999999999999999'));
			end if;
		end if;
	end if;
		
return 0;
end;
$$
    LANGUAGE plpgsql;


update version set val=27;
commit;
