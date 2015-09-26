begin; 
create schema comptaproc;

 alter function account_add (poste_comptable,character varying) set  schema comptaproc;
 alter function account_auto (integer) set  schema comptaproc;
 alter function account_compute (integer) set  schema comptaproc;
 alter function account_insert (integer,text) set  schema comptaproc;
 alter function account_parent (poste_comptable) set  schema comptaproc;
 alter function account_update (integer,text) set  schema comptaproc;
 alter function action_gestion_ins_upd () set  schema comptaproc;
 alter function attribut_insert (integer,integer,character varying) set  schema comptaproc;
 alter function attribute_correct_order () set	schema comptaproc;
 alter function card_class_base (integer) set  schema comptaproc;
 alter function check_balance (integer) set  schema comptaproc;
 alter function correct_sequence (text,text,text) set  schema comptaproc;
 alter function create_missing_sequence () set	schema comptaproc;
 alter function drop_index (character varying) set  schema comptaproc;
 alter function drop_it (character varying) set  schema comptaproc;
 alter function extension_ins_upd () set  schema comptaproc;
 alter function fiche_account_parent (integer) set  schema comptaproc;
 alter function fiche_attribut_synchro (integer) set  schema comptaproc;
 alter function fiche_def_ins_upd () set  schema comptaproc;
 alter function find_pcm_type (numeric) set  schema comptaproc;
 alter function group_analytic_ins_upd () set  schema comptaproc;
 alter function group_analytique_del () set  schema comptaproc;
 alter function html_quote (text) set  schema comptaproc;
 alter function info_def_ins_upd () set  schema comptaproc;
 alter function insert_jrnx (p_date character varying, p_montant numeric, p_poste poste_comptable, p_grpt integer, p_jrn_def integer, p_debit boolean, p_tech_user text, p_tech_per integer, p_qcode text, p_comment text) set	schema comptaproc;
 alter function insert_quant_purchase ( p_internal text, p_j_id numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_nd_amount numeric, p_nd_tva numeric, p_nd_tva_recup numeric, p_dep_priv numeric, p_client character varying) set  schema comptaproc;
 alter function insert_quant_sold (p_internal text, p_jid numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_client character varying) set  schema comptaproc;
 alter function insert_quick_code (integer,text) set  schema comptaproc;
 alter function jrn_check_periode () set  schema comptaproc;
 alter function jrn_def_add () set  schema comptaproc;
 alter function jrn_def_delete () set  schema comptaproc;
 alter function jrn_del () set	schema comptaproc;
 alter function jrnx_del () set  schema comptaproc;
 alter function plan_analytic_ins_upd () set  schema comptaproc;
 alter function poste_analytique_ins_upd () set  schema comptaproc;
 alter function proc_check_balance () set  schema comptaproc;
 alter function t_document_modele_validate () set  schema comptaproc;
 alter function t_document_type_insert () set  schema comptaproc;
 alter function t_document_validate () set  schema comptaproc;
 alter function t_jrn_def_sequence () set  schema comptaproc;
 alter function tmp_pcmn_ins () set  schema comptaproc;
 alter function trim_cvs_quote () set  schema comptaproc;
 alter function trim_space_format_csv_banque () set  schema comptaproc;
 alter function tva_delete (integer) set  schema comptaproc;
 alter function tva_insert (text,numeric,text,text) set  schema comptaproc;
 alter function tva_modify (integer,text,numeric,text,text) set  schema comptaproc;
 alter function update_quick_code (integer,text) set  schema comptaproc;

set search_path to public,comptaproc;
alter table centralized DROP CONSTRAINT "$2";
alter table jrnx DROP CONSTRAINT "$1";

DROP VIEW vw_client ;
DROP VIEW vw_fiche_attr ;
DROP VIEW vw_fiche_def ;
DROP VIEW vw_fiche_min ;
DROP VIEW vw_poste_qcode ;
DROP VIEW vw_supplier ;

create domain account_type varchar(40);
alter table tmp_pcmn alter pcm_val_parent  type account_type;
alter table tmp_pcmn alter pcm_val  type account_type;
alter table centralized alter c_poste  type account_type;
alter table del_jrnx alter j_poste  type account_type;
alter table fiche_def alter fd_class_base  type text;
alter table jrnx alter j_poste	type account_type;
alter table parm_poste alter p_value  type account_type;

CREATE OR REPLACE VIEW vw_client AS
 SELECT a.f_id, a.av_text AS name, a1.av_text AS quick_code, b.av_text AS tva_num, c.av_text AS poste_comptable, d.av_text AS rue, e.av_text AS code_postal, f.av_text AS pays, g.av_text AS telephone, h.av_text AS email
   FROM ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
	   FROM fiche
      JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 1) a
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
	   FROM fiche
      JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 13) b USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
      FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 23) a1 USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 5) c USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 14) d USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 15) e USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 16) f USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 17) g USING (f_id)
   LEFT JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 18) h USING (f_id)
  WHERE a.frd_id = 9;


CREATE OR REPLACE VIEW vw_fiche_attr AS
 SELECT a.f_id, a.fd_id, a.av_text AS vw_name, b.av_text AS vw_sell, c.av_text AS vw_buy, d.av_text AS tva_code, tva_rate.tva_id, tva_rate.tva_rate, tva_rate.tva_label, e.av_text AS vw_addr, f.av_text AS vw_cp, j.av_text AS quick_code, h.av_text AS vw_description, i.av_text AS tva_num, fiche_def.frd_id
   FROM ( SELECT fiche.f_id, fiche.fd_id, attr_value.av_text
	   FROM fiche
      JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 1) a
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
	   FROM fiche
      JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 6) b ON a.f_id = b.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
      FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 7) c ON a.f_id = c.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
   FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 2) d ON a.f_id = d.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
   FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 14) e ON a.f_id = e.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
   FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 15) f ON a.f_id = f.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
   FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 23) j ON a.f_id = j.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
   FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 9) h ON a.f_id = h.f_id
   LEFT JOIN ( SELECT fiche.f_id, attr_value.av_text
   FROM fiche
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
   JOIN attr_def USING (ad_id)
  WHERE jnt_fic_att_value.ad_id = 13) i ON a.f_id = i.f_id
   LEFT JOIN tva_rate ON d.av_text = tva_rate.tva_id::text
   JOIN fiche_def USING (fd_id);

CREATE OR REPLACE VIEW vw_fiche_def AS
 SELECT jnt_fic_attr.fd_id, jnt_fic_attr.ad_id, attr_def.ad_text, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def.frd_id
   FROM fiche_def
   JOIN jnt_fic_attr USING (fd_id)
   JOIN attr_def ON attr_def.ad_id = jnt_fic_attr.ad_id;

COMMENT ON VIEW vw_fiche_def IS 'all the attributs for	card family';


CREATE OR REPLACE VIEW vw_fiche_min AS
 SELECT attr_min.frd_id, attr_min.ad_id, attr_def.ad_text, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base
   FROM attr_min
   JOIN attr_def USING (ad_id)
   JOIN fiche_def_ref USING (frd_id);

CREATE OR REPLACE VIEW vw_poste_qcode AS
 SELECT a.f_id, a.av_text AS j_poste, b.av_text AS j_qcode
   FROM ( SELECT jnt_fic_att_value.f_id, attr_value.av_text
	   FROM attr_value
      JOIN jnt_fic_att_value USING (jft_id)
     WHERE jnt_fic_att_value.ad_id = 5) a
   JOIN ( SELECT jnt_fic_att_value.f_id, attr_value.av_text
	   FROM attr_value
      JOIN jnt_fic_att_value USING (jft_id)
     WHERE jnt_fic_att_value.ad_id = 23) b USING (f_id);

CREATE OR REPLACE VIEW vw_poste_qcode AS
 SELECT a.f_id, a.av_text AS j_poste, b.av_text AS j_qcode
   FROM ( SELECT jnt_fic_att_value.f_id, attr_value.av_text
	   FROM attr_value
      JOIN jnt_fic_att_value USING (jft_id)
     WHERE jnt_fic_att_value.ad_id = 5) a
   JOIN ( SELECT jnt_fic_att_value.f_id, attr_value.av_text
	   FROM attr_value
      JOIN jnt_fic_att_value USING (jft_id)
     WHERE jnt_fic_att_value.ad_id = 23) b USING (f_id);

CREATE OR REPLACE VIEW vw_supplier AS
 SELECT a.f_id, a.av_text AS name, a1.av_text AS quick_code, b.av_text AS tva_num, c.av_text AS poste_comptable, d.av_text AS rue, e.av_text AS code_postal, f.av_text AS pays, g.av_text AS telephone, h.av_text AS email
   FROM ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
	   FROM fiche
      JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 1) a
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
	   FROM fiche
      JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 13) b USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
      FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 23) a1 USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 5) c USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 14) d USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 15) e USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 16) f USING (f_id)
   JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 17) g USING (f_id)
   LEFT JOIN ( SELECT jnt_fic_att_value.jft_id, fiche.f_id, fiche_def.frd_id, fiche.fd_id, fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base, jnt_fic_att_value.ad_id, attr_value.av_text
   FROM fiche
   JOIN fiche_def USING (fd_id)
   JOIN fiche_def_ref USING (frd_id)
   JOIN jnt_fic_att_value USING (f_id)
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 18) h USING (f_id)
  WHERE a.frd_id = 8;

alter table jrnx add CONSTRAINT fk_pcmn_val foreign key (j_poste) references tmp_pcmn (pcm_val);
alter table centralized add CONSTRAINT fk_pcmn_val foreign key (c_poste) references tmp_pcmn (pcm_val);

drop function comptaproc.account_add (p_id poste_comptable,t character varying) ;
drop function comptaproc.account_parent (a poste_comptable) ;
drop function comptaproc.insert_jrnx (p_date character varying, p_montant numeric, p_poste poste_comptable, p_grpt integer, p_jrn_def integer, p_debit boolean, p_tech_user text, p_tech_per integer, p_qcode text, p_comment text);
drop function comptaproc.account_update(integer,text);
drop function comptaproc.find_pcm_type(numeric);
drop function comptaproc.account_compute(integer);

--
-- Name: account_update(integer, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE OR REPLACE FUNCTION comptaproc.account_update(p_f_id integer, p_account tmp_pcmn.pcm_val_parent%type ) RETURNS integer
    AS $$
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
			select count(*) into nCount from tmp_pcmn where pcm_val=p_account;
			if nCount = 0 then
			select av_text into sName from
				attr_value join jnt_fic_att_value using (jft_id)
				where
				ad_id=1 and f_id=p_f_id;
			nParent:=account_parent(p_account);
			insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (p_account,sName,nParent);
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
$$
    LANGUAGE plpgsql;


--
-- Name: insert_jrnx(character varying, numeric, public.account_type, integer, integer, boolean, text, integer, text, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE OR REPLACE FUNCTION comptaproc.insert_jrnx(p_date character varying, p_montant numeric, p_poste tmp_pcmn.pcm_val_parent%type, p_grpt integer, p_jrn_def integer, p_debit boolean, p_tech_user text, p_tech_per integer, p_qcode text, p_comment text) RETURNS void
    AS $$
declare
	sCode varchar;
	nCount_qcode integer;
begin
	sCode=trim(p_qcode);

	-- if p_qcode is empty try to find one
	if length(sCode) = 0 or p_qcode is null then
		select count(*) into nCount_qcode
			from vw_poste_qcode where j_poste=p_poste;
	-- if we find only one q_code for a accountancy account
	-- then retrieve it
		if nCount_qcode = 1 then
			select j_qcode::text into sCode
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
		j_text,
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
		p_comment,
		p_tech_user,
		p_tech_per,
		sCode
	);

return;
end;
$$
    LANGUAGE plpgsql;


--
-- Name: tva_insert(text, numeric, text, text); Type: FUNCTION; Schema: comptaproc; Owner: -
--

CREATE OR REPLACE FUNCTION comptaproc.tva_insert(text, numeric, text, text) RETURNS integer
    AS $_$
declare
	l_tva_id integer;
	p_tva_label alias for $1;
	p_tva_rate alias for $2;
	p_tva_comment alias for $3;
	p_tva_poste alias for $4;
	debit text;
	credit text;
	nCount integer;
begin
if length(trim(p_tva_label)) = 0 then
	return 3;
end if;

if length(trim(p_tva_poste)) != 0 then
	if position (',' in p_tva_poste) = 0 then return 4; end if;
	debit  = split_part(p_tva_poste,',',1);
	credit	= split_part(p_tva_poste,',',2);
	select count(*) into nCount from tmp_pcmn where pcm_val=debit::account_type;
	if nCount = 0 then return 4; end if;
	select count(*) into nCount from tmp_pcmn where pcm_val=credit::account_type;
	if nCount = 0 then return 4; end if;

end if;
select into l_tva_id nextval('s_tva') ;
insert into tva_rate(tva_id,tva_label,tva_rate,tva_comment,tva_poste)
	values (l_tva_id,p_tva_label,p_tva_rate,p_tva_comment,p_tva_poste);
return 0;
end;
$_$
    LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION comptaproc.tva_modify(integer, text, numeric, text, text) RETURNS integer
    AS $_$
declare
	p_tva_id alias for $1;
	p_tva_label alias for $2;
	p_tva_rate alias for $3;
	p_tva_comment alias for $4;
	p_tva_poste alias for $5;
	debit text;
	credit text;
	nCount integer;
begin
if length(trim(p_tva_label)) = 0 then
	return 3;
end if;

if length(trim(p_tva_poste)) != 0 then
	if position (',' in p_tva_poste) = 0 then return 4; end if;
	debit  = split_part(p_tva_poste,',',1);
	credit	= split_part(p_tva_poste,',',2);
	select count(*) into nCount from tmp_pcmn where pcm_val=debit::account_type;
	if nCount = 0 then return 4; end if;
	select count(*) into nCount from tmp_pcmn where pcm_val=credit::account_type;
	if nCount = 0 then return 4; end if;

end if;
update tva_rate set tva_label=p_tva_label,tva_rate=p_tva_rate,tva_comment=p_tva_comment,tva_poste=p_tva_poste
	where tva_id=p_tva_id;
return 0;
end;
$_$
    LANGUAGE plpgsql;

-- Function: comptaproc.account_add(account_type, character varying)

-- DROP FUNCTION comptaproc.account_add(account_type, character varying);

CREATE OR REPLACE FUNCTION comptaproc.account_add(p_id account_type, p_name character varying)
  RETURNS void AS
$BODY$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	nCount integer;
begin
	select count(*) into nCount from tmp_pcmn where pcm_val=p_id;
	if nCount = 0 then
		nParent=account_parent(p_id);
		insert into tmp_pcmn (pcm_val,pcm_lib,pcm_val_parent)
			values (p_id, p_name,nParent);
	end if;
return;
end ;
$BODY$
LANGUAGE 'plpgsql';
-- Function: comptaproc.account_compute(integer)

-- DROP FUNCTION comptaproc.account_compute(integer);

CREATE OR REPLACE FUNCTION comptaproc.account_compute(p_f_id integer)
  RETURNS account_type AS
$BODY$
declare
	class_base fiche_def.fd_class_base%type;
	maxcode numeric;
	sResult account_type;
begin
	select fd_class_base into class_base
	from
		fiche_def join fiche using (fd_id)
	where
		f_id=p_f_id;
	raise notice 'account_compute class base %',class_base;
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
	return sResult;
end;
$BODY$
  LANGUAGE 'plpgsql' ;
-- Function: comptaproc.account_insert(integer, text)

-- DROP FUNCTION comptaproc.account_insert(integer, text);

CREATE OR REPLACE FUNCTION comptaproc.account_insert(p_f_id integer, p_account text)
  RETURNS integer AS
$BODY$
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

	if length(trim(p_account)) != 0 then
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
					select av_text into sName from
						attr_value join jnt_fic_att_value using (jft_id)
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
			select av_text into sName from
				attr_value join jnt_fic_att_value using (jft_id)
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
$BODY$
  LANGUAGE 'plpgsql' ;
-- Function: comptaproc.account_parent(account_type)

-- DROP FUNCTION comptaproc.account_parent(account_type);

CREATE OR REPLACE FUNCTION comptaproc.account_parent(p_account account_type)
  RETURNS account_type AS
$BODY$
declare
	sSubParent tmp_pcmn.pcm_val_parent%type;
	sResult tmp_pcmn.pcm_val_parent%type;
	nCount integer;
begin
	if p_account is NULL then
		return NULL;
	end if;
	sSubParent:=p_account;
	while true loop
		select count(*) into nCount
		from tmp_pcmn
		where
		pcm_val = sSubParent;
		if nCount != 0 then
			sResult:= sSubParent;
			exit;
		end if;
		sSubParent:= substr(sSubParent,1,length(sSubParent)-1);
		if length(sSubParent) <= 0 then
			raise exception 'Impossible de trouver le compte parent pour %',p_account;
		end if;
		raise notice 'sSubParent % % ',sSubParent,length(sSubParent);
	end loop;
	raise notice 'account_parent : Parent is %',sSubParent;
	return sSubParent;
end;
$BODY$
  LANGUAGE 'plpgsql';
-- Function: comptaproc.card_class_base(integer)

-- DROP FUNCTION comptaproc.card_class_base(integer);
DROP FUNCTION comptaproc.card_class_base(p_f_id integer);

CREATE OR REPLACE FUNCTION comptaproc.card_class_base(p_f_id integer)
  RETURNS fiche_def.fd_class_base%TYPE AS
$BODY$
declare
	n_poste fiche_def.fd_class_base%type;
begin

	select fd_class_base into n_poste from fiche_def join fiche using
(fd_id)
	where f_id=p_f_id;
	if not FOUND then
		raise exception 'Invalid fiche card_class_base(%)',p_f_id;
	end if;
return n_poste;
end;
$BODY$
  LANGUAGE 'plpgsql' ;
-- Function: comptaproc.fiche_account_parent(integer)

-- DROP FUNCTION comptaproc.fiche_account_parent(integer);

CREATE OR REPLACE FUNCTION comptaproc.fiche_account_parent(p_f_id integer)
  RETURNS poste_comptable AS
$BODY$
declare
ret tmp_pcmn.pcm_val%TYPE;
begin
	select fd_class_base into ret from fiche_def join fiche using (fd_id) where f_id=p_f_id;
	if not FOUND then
		raise exception '% N''existe pas',p_f_id;
	end if;
	return ret;
end;
$BODY$
  LANGUAGE 'plpgsql' ;
CREATE OR REPLACE FUNCTION comptaproc.find_pcm_type(pp_value account_type)
  RETURNS text AS
$BODY$
declare
	str_type parm_poste.p_type%TYPE;
	str_value parm_poste.p_type%TYPE;
	nLength integer;
begin
	str_value:=pp_value;
	nLength:=length(str_value::text);
	while nLength > 0 loop
		select p_type into str_type from parm_poste where p_value=str_value;
		if FOUND then
			return str_type;
		end if;
		nLength:=nLength-1;
		str_value:=substring(str_value::text from 1 for nLength)::account_type;
	end loop;
return 'CON';
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
DROP FUNCTION comptaproc.fiche_account_parent(integer);

CREATE OR REPLACE FUNCTION comptaproc.fiche_account_parent(p_f_id integer)
  RETURNS account_type AS
$BODY$
declare
ret tmp_pcmn.pcm_val%TYPE;
begin
	select fd_class_base into ret from fiche_def join fiche using (fd_id) where f_id=p_f_id;
	if not FOUND then
		raise exception '% N''existe pas',p_f_id;
	end if;
	return ret;
end;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

drop domain poste_comptable;

update version set val=73;
commit;
