begin;
DROP TRIGGER fiche_detail_upd_trg ON fiche_detail;

CREATE TRIGGER fiche_detail_upd_trg
  after UPDATE
  ON fiche_detail
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.fiche_detail_qcode_upd();

insert into menu_ref(me_code,me_file,me_menu,me_description,me_type) 
values ('RAW:receipt','export_receipt.php','Exporte la pièce','export la pièce justificative d''une opération','PR');

insert into profile_menu (me_code,p_id,p_type_display) select 'RAW:receipt',p_id,'P' from profile where p_id > 0;


insert into menu_ref(me_code,me_file,me_menu,me_description,me_type) 
values ('RAW:document','export_document.php','Export le document','exporte le document d''un événement','PR');

insert into profile_menu (me_code,p_id,p_type_display) select 'RAW:document',p_id,'P' from profile where p_id > 0;

insert into menu_ref(me_code,me_file,me_menu,me_description,me_type) 
values ('RAW:document_template','export_document_template.php','Exporte le modèle de document','export le modèle de document utilisé dans le suivi','PR');

insert into profile_menu (me_code,p_id,p_type_display) select 'RAW:document_template',p_id,'P' from profile where p_id > 0;


delete from PROFILE_USER where pu_id in (select b.pu_id 
	from profile_user as a , profile_user as b 
	where 
	upper(a.user_name) = b.user_name and b.user_name = upper(b.user_name) and a.pu_id <> b.pu_id );



CREATE OR REPLACE FUNCTION comptaproc.trg_profile_user_ins_upd()
  RETURNS trigger AS
$BODY$

begin

NEW.user_name := lower(NEW.user_name);
return NEW;

end;
$BODY$
language plpgsql;

CREATE TRIGGER profile_user_ins_upd
  BEFORE INSERT OR UPDATE
  ON profile_user
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.trg_profile_user_ins_upd();
COMMENT ON TRIGGER profile_user_ins_upd ON profile_user IS 'Force the column user_name to lowercase';



delete from user_sec_jrn where uj_id in (select b.uj_id
	from user_sec_jrn  as a , user_sec_jrn  as b 
	where 
	upper(a.uj_login) = b.uj_login and a.uj_id<> b.uj_id and a.uj_jrn_id=b.uj_jrn_id and b.uj_login=upper(b.uj_login));


update user_sec_jrn set uj_login = lower(uj_login);

ALTER TABLE user_sec_jrn
  ADD CONSTRAINT uniq_user_ledger UNIQUE(uj_login , uj_jrn_id );
COMMENT ON CONSTRAINT uniq_user_ledger ON user_sec_jrn IS 'Create an unique combination user / ledger';

CREATE OR REPLACE FUNCTION comptaproc.trg_user_sec_jrn_ins_upd()
  RETURNS trigger AS
$BODY$

begin

NEW.uj_login:= lower(NEW.uj_login);
return NEW;

end;
$BODY$
language plpgsql;


CREATE TRIGGER user_sec_jrn_after_ins_upd
  BEFORE INSERT OR UPDATE
  ON user_sec_jrn
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.trg_user_sec_jrn_ins_upd();
COMMENT ON TRIGGER user_sec_jrn_after_ins_upd  ON user_sec_jrn IS 'Force the column uj_login to lowercase';


delete from user_sec_act where ua_id in (select b.ua_id
	from user_sec_act as a , user_sec_act  as b 
	where 
	upper (b.ua_login) = b.ua_login and
	upper(a.ua_login) = b.ua_login and a.ua_id<> b.ua_id and a.ua_act_id=b.ua_act_id) ;

update user_sec_act set ua_login = lower(ua_login);

CREATE OR REPLACE FUNCTION comptaproc.trg_user_sec_act_ins_upd()
  RETURNS trigger AS
$BODY$

begin

NEW.ua_login:= lower(NEW.ua_login);
return NEW;

end;
$BODY$
language plpgsql;


CREATE TRIGGER user_sec_act_ins_upd
  BEFORE INSERT OR UPDATE
  ON user_sec_act
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.trg_user_sec_act_ins_upd();
COMMENT ON TRIGGER user_sec_act_ins_upd ON user_sec_act IS 'Force the column ua_login to lowercase';

update todo_list set use_login = lower(use_login);

CREATE OR REPLACE FUNCTION comptaproc.trg_todo_list_ins_upd()
  RETURNS trigger AS
$BODY$

begin

NEW.use_login:= lower(NEW.use_login);
return NEW;

end;
$BODY$
language plpgsql;


CREATE TRIGGER todo_list_ins_upd
  BEFORE INSERT OR UPDATE
  ON todo_list
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.trg_todo_list_ins_upd();
COMMENT ON TRIGGER todo_list_ins_upd ON todo_list IS 'Force the column use_login to lowercase';



delete from todo_list_shared where id in (select b.id
	from todo_list_shared as a , todo_list_shared as b 
	where 
	upper(a.use_login) = b.use_login and upper (b.use_login ) = b.use_login and a.id<> b.id);

update todo_list_shared set use_login = lower(use_login);

CREATE OR REPLACE FUNCTION comptaproc.trg_todo_list_shared_ins_upd()
  RETURNS trigger AS
$BODY$

begin

NEW.use_login:= lower(NEW.use_login);
return NEW;

end;
$BODY$
language plpgsql;


CREATE TRIGGER todo_list_shared_ins_upd
  BEFORE INSERT OR UPDATE
  ON todo_list_shared
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.trg_todo_list_shared_ins_upd();
COMMENT ON TRIGGER todo_list_shared_ins_upd ON todo_list_shared IS 'Force the column ua_login to lowercase';

CREATE OR REPLACE FUNCTION comptaproc.action_gestion_ins_upd()
  RETURNS trigger AS
$BODY$
begin
NEW.ag_title := substr(trim(NEW.ag_title),1,70);
NEW.ag_hour := substr(trim(NEW.ag_hour),1,5);
NEW.ag_owner := lower(NEW.ag_owner);
return NEW;
end;
$BODY$
LANGUAGE plpgsql;

alter table quant_sold add column qs_unit numeric(20,4) default 0;
update quant_sold set qs_unit = qs_price / qs_quantite where qs_quantite <> 0 ;

alter table quant_purchase add column qp_unit numeric(20,4) default 0;
update quant_purchase set qp_unit = qp_price / qp_quantite where qp_quantite <> 0;

CREATE OR REPLACE FUNCTION comptaproc.insert_quant_sold(p_internal text, p_jid numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_client character varying, p_tva_sided numeric, p_price_unit numeric)
  RETURNS void AS
$BODY$
declare
        fid_client integer;
        fid_good   integer;
begin

        select f_id into fid_client from
                fiche_detail where ad_id=23 and ad_value=upper(trim(p_client));
        select f_id into fid_good from
                fiche_detail where ad_id=23 and ad_value=upper(trim(p_fiche));
        insert into quant_sold
                (qs_internal,j_id,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client,qs_valid,qs_vat_sided,qs_unit)
        values
                (p_internal,p_jid,fid_good,p_quant,p_price,p_vat,p_vat_code,fid_client,'Y',p_tva_sided,p_price_unit);
        return;
end;
 $BODY$
  LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION comptaproc.insert_quant_purchase(p_internal text, p_j_id numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_nd_amount numeric, p_nd_tva numeric, p_nd_tva_recup numeric, p_dep_priv numeric, p_client character varying, p_tva_sided numeric,p_price_unit numeric)
  RETURNS void AS
$BODY$
declare
        fid_client integer;
        fid_good   integer;
begin
        select f_id into fid_client from
                fiche_detail where ad_id=23 and ad_value=upper(trim(p_client));
        select f_id into fid_good from
                 fiche_detail where ad_id=23 and ad_value=upper(trim(p_fiche));
        insert into quant_purchase
                (qp_internal,
                j_id,
                qp_fiche,
                qp_quantite,
                qp_price,
                qp_vat,
                qp_vat_code,
                qp_nd_amount,
                qp_nd_tva,
                qp_nd_tva_recup,
                qp_supplier,
                qp_dep_priv,
                qp_vat_sided,
                qp_unit)
        values
                (p_internal,
                p_j_id,
                fid_good,
                p_quant,
                p_price,
                p_vat,
                p_vat_code,
                p_nd_amount,
                p_nd_tva,
                p_nd_tva_recup,
                fid_client,
                p_dep_priv,
                p_tva_sided,
                p_price_unit);
        return;
end;
 $BODY$
  LANGUAGE plpgsql ;

update attr_def set ad_extra=4 where ad_id in (6,7);

CREATE OR REPLACE FUNCTION comptaproc.menu_complete_dependency(n_profile numeric)
  RETURNS void AS
$BODY$
declare 
 n_count integer;
 csr_root_menu cursor (p_profile numeric) is select pm_id,
	me_code,
	me_code_dep 
	
	from profile_menu 
	where 
	me_code in 
		(select a.me_code_dep 
			from profile_menu as a 
			join profile_menu as b on (a.me_code=b.me_code and a.me_code_dep=b.me_code_dep and a.pm_id <> b.pm_id and a.p_id=b.p_id) 
			where a.p_id=n_profile) 
		and p_id=p_profile;

begin
	for duplicate in csr_root_menu(n_profile)
	loop
		raise notice 'found %',duplicate;
		update profile_menu set pm_id_dep  = duplicate.pm_id 
			where pm_id in (select a.pm_id
				from profile_menu as a 
				left join profile_menu as b on (a.me_code=b.me_code and a.me_code_dep=b.me_code_dep)
				where 
				a.p_id=n_profile
				and b.p_id=n_profile
				and a.pm_id_dep is null 
				and a.me_code_dep = duplicate.me_code
				and a.pm_id < b.pm_id);
	end loop;
	
	for duplicate in csr_root_menu(n_profile) 
	loop
		select count(*) into n_count from profile_menu where p_id=n_profile and pm_id_dep = duplicate.pm_id;
		raise notice '% use % times',duplicate,n_count;
		if n_count = 0 then
			raise notice ' Update with %',duplicate;
			update profile_menu set pm_id_dep = duplicate.pm_id where p_id = n_profile and me_code_dep = duplicate.me_code and pm_id_dep is null;
		end if;

	end loop;
	
end;
$BODY$
LANGUAGE plpgsql;


delete from profile_menu where pm_id_dep is not null and pm_id_dep not  in (select pm_id from profile_menu);

update version set val=122;

commit;
