begin;
-- bug 1753
create or replace function correct_sequence ( p_sequence text,p_col text, p_table text )
returns integer
as
$body$
declare
-- fonction description
-- Often the primary key is a sequence number and sometimes 
-- the value of the sequence is not synchronized with the 
-- primary key
-- parameter p_sequence : sequence name
-- parameter p_col : col of the pk
-- parameter p_table : concerned table
-- variable
-- last value of the sequence
last_sequence int8;
-- max value of the pk
max_sequence int8;
-- n integer
n integer;
begin
-- the sequence exist ?
	select count(*) into n from pg_class where relkind='S' and relname=lower(p_sequence);
	if n = 0 then
		raise exception  ' Unknow sequence  % ',p_sequence;
	end if;
	select count(*) into n from pg_class where relkind='r' and relname=lower(p_table);
	if n = 0 then
		raise exception ' Unknow table  % ',p_table;
	end if;

	execute 'select last_value   from '||p_sequence into last_sequence;
	raise notice 'Last value of the sequence is %', last_sequence;

	execute 'select max('||p_col||')  from '||p_table into max_sequence;
	if  max_sequence is null then
		max_sequence := 0;
	end if;
	raise notice 'Max value of the sequence is %', max_sequence;
	max_sequence:= max_sequence +1;	
	execute 'alter sequence '||p_sequence||' restart with '||max_sequence;
return 0;

end;
$body$ language plpgsql;

comment on function correct_sequence (text,text,text) is ' Often the primary key is a sequence number and sometimes the value of the sequence is not synchronized with the primary key ( p_sequence : sequence name, p_col : col of the pk,p_table : concerned table';
commit;
begin;
select correct_sequence('s_jnt_fic_att_value','jft_id','jnt_fic_att_value');

-- bug 17544
-- add a pk to the table jnt_fic_attr
alter table jnt_fic_attr add jnt_id int8;
create sequence s_jnt_id;
alter table jnt_fic_attr alter jnt_id set default nextval('s_jnt_id');
update jnt_fic_attr set jnt_id=nextval('s_jnt_id');
alter table jnt_fic_attr add constraint pk_jnt_fic_attr primary key (jnt_id);

-- remove duplicate attr
delete  from jnt_fic_attr where jnt_id in ( select a.jnt_id from jnt_fic_attr a join jnt_fic_attr b on (a.fd_id=b.fd_id and a.ad_id=b.ad_id) where a.jnt_id > b.jnt_id); 

-- bug 17543

--account_compute
CREATE or replace FUNCTION account_parent(p_account poste_comptable) RETURNS poste_comptable
    AS $$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	sParent varchar;
	nCount integer;
begin
	sParent:=to_char(p_account,'9999999999999999');
	sParent:=trim(sParent);
	nParent:=0;
	while nParent = 0 loop
		select count(*) into nCount
		from tmp_pcmn
		where
		pcm_val = to_number(sParent,'9999999999999999');
		if nCount != 0 then
			nParent:=to_number(sParent,'9999999999999999');
		end if;
		sParent:= substr(sParent,1,length(sParent)-1);
		if length(sParent) <= 0 then	
			raise exception 'Impossible de trouver le compte parent pour %',p_account;
		end if;
	end loop;
	raise notice 'account_parent : Parent is %',nParent;
	return nParent;
end;
$$
    LANGUAGE plpgsql;


commit;


CREATE or replace FUNCTION account_compute(p_f_id integer) RETURNS poste_comptable
    AS $$
declare
	class_base poste_comptable;
	maxcode int8;
begin
	select fd_class_base into class_base 
	from
		fiche_def join fiche using (fd_id)
	where 
		f_id=p_f_id;
	raise notice 'account_compute class base %',class_base;
	select max(pcm_val) into maxcode from tmp_pcmn where pcm_val_parent = class_base;
	if maxcode = class_base then
		maxcode:=class_base*1000;
	end if;
	maxcode:=maxcode+1;
	raise notice 'account_compute Max code %',maxcode;
	return maxcode;
end;
$$
    LANGUAGE plpgsql;



--
-- Name: account_insert(integer, poste_comptable); Type: FUNCTION; Schema: public; Owner: phpcompta
--

CREATE or replace FUNCTION account_insert(p_f_id integer, p_account poste_comptable) RETURNS integer
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
$$
    LANGUAGE plpgsql;
update version set val=20;
commit;
