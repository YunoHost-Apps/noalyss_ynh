begin;

--
-- Name: account_insert(integer, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

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

	if p_account is not null and length(trim(p_account)) != 0 then
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

update version set val=95;

commit;