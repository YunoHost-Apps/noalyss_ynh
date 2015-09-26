begin;

ALTER TABLE action    ALTER COLUMN ac_code TYPE character varying(30);

INSERT INTO action(ac_id, ac_description, ac_module, ac_code)
    VALUES (1110, 'Enlever une pièce justificative', 'compta', 'RMRECEIPT');
INSERT INTO action(ac_id, ac_description, ac_module, ac_code)
    VALUES (1120, 'Effacer une opération ', 'compta', 'RMOPER');
INSERT INTO action(ac_id, ac_description, ac_module, ac_code)
    VALUES (1210, 'Partager une note', 'note', 'SHARENOTE');
INSERT INTO action(ac_id, ac_description, ac_module, ac_code)
    VALUES (1220, 'Créer une note publique', 'note', 'SHARENOTEPUBLIC');
INSERT INTO action(ac_id, ac_description, ac_module, ac_code)
    VALUES (1230, 'Effacer une note publique', 'note', 'SHARENOTEREMOVE');


CREATE TABLE todo_list_shared (id  serial primary key, todo_list_id int4 NOT NULL, use_login text NOT NULL, CONSTRAINT unique_todo_list_id_login 
    UNIQUE (todo_list_id, use_login));

ALTER TABLE todo_list_shared ADD CONSTRAINT fk_todo_list_shared_todo_list FOREIGN KEY (todo_list_id) REFERENCES todo_list (tl_id);

comment on table todo_list_shared is 'Note of todo list shared with other users';
comment on column todo_list_shared.todo_list_id is 'fk to todo_list';
comment on column todo_list_shared.use_login is 'user login';

alter table todo_list add is_public char(1) default 'N';
comment on column todo_list.is_public is 'Flag for the public parameter';
ALTER TABLE todo_list    ALTER COLUMN is_public SET NOT NULL;

ALTER TABLE todo_list ADD CONSTRAINT ck_is_public CHECK (is_public in ('Y','N'));

update menu_ref set me_menu = 'Favori &#9733; ' where me_code='BOOKMARK';
update menu_ref set me_menu = 'Sortie &#9094;' where me_code='LOGOUT'; 

insert into menu_ref(me_code,me_menu,me_file, me_url,me_description,me_parameter,me_javascript,me_type,me_description_etendue)
values
('BALAGE','Balance agée','balance_age.inc.php',null,'Balance agée',null,null,'ME','Balance agée pour les clients et fournisseurs') ,
('CSV:balance_age','Export Balance agée','export_balance_age_csv.php',null,'Balance agée',null,null,'PR','Balance agée pour les clients et fournisseurs') ;


insert into profile_menu (me_code,me_code_dep,p_id,p_order, p_type_display,pm_default) 
values
('BALAGE','PRINT',1,550,'E',0),('BALAGE','PRINT',2,550,'E',0),
('CSV:balance_age',null,1,null,'P',0),('CSV:balance_age',null,2,null,'P',0);

CREATE OR REPLACE FUNCTION comptaproc.account_compute(p_f_id integer)
  RETURNS account_type AS
$BODY$
declare
	class_base fiche_def.fd_class_base%type;
	maxcode numeric;
	sResult text;
	bAlphanum bool;
	sName text;
begin
	select fd_class_base into class_base
	from
		fiche_def join fiche using (fd_id)
	where
		f_id=p_f_id;
	raise notice 'account_compute class base %',class_base;
	bAlphanum := account_alphanum();
	if bAlphanum = false  then
	raise info 'account_compute : Alphanum is false';
		select count (pcm_val) into maxcode from tmp_pcmn where pcm_val_parent = class_base;
		if maxcode = 0	then
			maxcode:=class_base::numeric;
		else
			select max (pcm_val) into maxcode from tmp_pcmn where pcm_val_parent = class_base;
			maxcode:=maxcode::numeric;
		end if;
		if maxcode::text = class_base then
			maxcode:=class_base::numeric*1000;
		end if;
		maxcode:=maxcode+1;
		raise notice 'account_compute Max code %',maxcode;
		sResult:=maxcode::account_type;
	else
	raise info 'account_compute : Alphanum is true';
		-- if alphanum, use name
		select ad_value into sName from fiche_detail where f_id=p_f_id and ad_id=1;
		raise info 'name is %',sName;
		if sName is null then
			raise exception 'Cannot compute an accounting without the name of the card for %',p_f_id;
		end if;
		sResult := class_base||sName;
		sResult := substr(sResult,1,40);
		raise info 'Result is %',sResult;
	end if;
	return sResult::account_type;
end;
$BODY$ 
LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION comptaproc.account_insert(p_f_id integer, p_account text)
  RETURNS text AS
$BODY$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	sName varchar;
	sNew tmp_pcmn.pcm_val%type;
	bAuto bool;
	nFd_id integer;
	sClass_Base fiche_def.fd_class_base%TYPE;
	nCount integer;
	first text;
	second text;
	s_account text;
begin

	if p_account is not null and length(trim(p_account)) != 0 then
	-- if there is coma in p_account, treat normally
		if position (',' in p_account) = 0 then
			raise info 'p_account is not empty';
				s_account := substr( p_account,1 , 40);
				select count(*)  into nCount from tmp_pcmn where pcm_val=s_account::account_type;
				raise notice 'found in tmp_pcm %',nCount;
				if nCount !=0  then
					raise info 'this account exists in tmp_pcmn ';
					perform attribut_insert(p_f_id,5,s_account);
				   else
				       -- account doesn't exist, create it
					select ad_value into sName from
						fiche_detail
					where
					ad_id=1 and f_id=p_f_id;

					nParent:=account_parent(s_account::account_type);
					insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (s_account::account_type,sName,nParent);
					perform attribut_insert(p_f_id,5,s_account);

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
	raise info 'A000 : p_account is  empty';
		select fd_id into nFd_id from fiche where f_id=p_f_id;
		bAuto:= account_auto(nFd_id);

		select fd_class_base into sClass_base from fiche_def where fd_id=nFd_id;
raise info 'sClass_Base : %',sClass_base;
		if bAuto = true and sClass_base similar to '[[:digit:]]*'  then
			raise info 'account generated automatically';
			sNew:=account_compute(p_f_id);
			raise info 'sNew %', sNew;
			select ad_value into sName from
				fiche_detail
			where
				ad_id=1 and f_id=p_f_id;
			nParent:=account_parent(sNew);
			sNew := account_add  (sNew,sName);
			perform attribut_insert(p_f_id,5,sNew);

		else
		-- if there is an account_base then it is the default
		      select fd_class_base::account_type into sNew from fiche_def join fiche using (fd_id) where f_id=p_f_id;
			if sNew is null or length(trim(sNew)) = 0 then
				raise notice 'count is null';
				 perform attribut_insert(p_f_id,5,null);
			else
				 perform attribut_insert(p_f_id,5,sNew);
			end if;
		end if;
	end if;

return 0;
end;
$BODY$  LANGUAGE plpgsql ;


update version set val=118;

commit;
