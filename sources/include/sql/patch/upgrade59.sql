begin;

insert into parameter(pr_id,pr_value) values ('MY_CHECK_PERIODE','Y');
alter table jrn add jr_mt text ;
update jrn set jr_mt=	extract (microseconds from jr_tech_date);
create	 index x_mt on jrn(jr_mt);
DROP FUNCTION insert_quant_purchase(text, numeric, character varying, numeric, numeric,numeric, integer, numeric, numeric, numeric, character varying);
DROP FUNCTION insert_quant_sold(text, character varying, numeric, numeric, numeric, integer, character varying);

alter table groupe_analytique add constraint fk_pa_id foreign key(pa_id)  references plan_analytique(pa_id) on delete cascade;
alter table stock_goods add constraint fk_stock_good_f_id foreign key(f_id)  references fiche(f_id) ;

drop table invoice;

DROP FUNCTION account_parent(poste_comptable);

CREATE FUNCTION account_parent(p_account poste_comptable)
  RETURNS poste_comptable AS
$BODY$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	sParent varchar;
	nCount integer;
begin
	sParent:=to_char(p_account,'9999999999999999');
	sParent:=trim(sParent::text);
	nParent:=0;
	while nParent = 0 loop
		select count(*) into nCount
		from tmp_pcmn
		where
		pcm_val = to_number(sParent,'9999999999999999');
		if nCount != 0 then
			nParent:=to_number(sParent,'9999999999999999');
			exit;
		end if;
		sParent:= substr(sParent,1,length(sParent)-1);
		if length(sParent) <= 0 then
			raise exception 'Impossible de trouver le compte parent pour %',p_account;
		end if;
	end loop;
	raise notice 'account_parent : Parent is %',nParent;
	return nParent;
end;
$BODY$
LANGUAGE 'plpgsql';

alter table document drop column d_state;

--alter table action_gestion set ag_title type text;
ALTER TABLE action_gestion ADD COLUMN ag_hour text default null;
ALTER TABLE action_gestion ADD COLUMN ag_priority integer;
ALTER TABLE action_gestion ALTER COLUMN ag_priority SET DEFAULT 2;
ALTER TABLE action_gestion ADD COLUMN ag_dest text;
ALTER TABLE action_gestion ADD COLUMN ag_owner text;
ALTER TABLE action_gestion ADD COLUMN ag_contact int8;

CREATE OR REPLACE FUNCTION action_gestion_ins_upd()
  RETURNS trigger AS
$BODY$
begin
NEW.ag_title := substr(trim(NEW.ag_title),1,70);
NEW.ag_hour := substr(trim(NEW.ag_hour),1,5);
return NEW;
end;
$BODY$
LANGUAGE 'plpgsql' VOLATILE;

CREATE TRIGGER action_gestion_t_insert_update
  BEFORE INSERT OR UPDATE
  ON action_gestion
  FOR EACH ROW
  EXECUTE PROCEDURE action_gestion_ins_upd();

COMMENT ON TRIGGER action_gestion_t_insert_update ON action_gestion IS 'Truncate the column ag_title to 70 char';

ALTER TABLE action_gestion   ADD COLUMN ag_state integer;
update action_gestion set f_id_dest=f_id_exp where f_id_exp != 0;
alter table action_gestion drop column f_id_exp;
UPDATE document_state	 SET s_value= 'Clôturé' WHERE s_id=1;
UPDATE document_state	 SET s_value= 'A suivre' WHERE s_id=2;
UPDATE document_state	 SET s_value= 'A faire' WHERE s_id=3;
UPDATE document_state	 SET s_value= 'Abandonné' WHERE s_id=4;


CREATE TABLE action_detail
(
  ad_id serial,
  f_id int8,
  ad_text text,
  ad_pu numeric(20,4) DEFAULT 0,
   ad_quant numeric(20,4) DEFAULT 0,
  ad_tva_id integer DEFAULT 0,
  ad_tva_amount numeric(20,4) DEFAULT 0,
  ad_total_amount numeric(20,4) DEFAULT 0,
  ag_id integer NOT NULL DEFAULT 0,
  CONSTRAINT action_detail_pkey PRIMARY KEY (ad_id),
  CONSTRAINT action_detail_ag_id_fkey FOREIGN KEY (ag_id)
      REFERENCES action_gestion (ag_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

COMMENT ON TABLE action_detail IS 'Detail of action_gestion, see class Action_Detail';
-- trim the qcode
CREATE OR REPLACE FUNCTION insert_quant_purchase(p_internal text, p_j_id numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_nd_amount numeric, p_nd_tva numeric, p_nd_tva_recup numeric, p_dep_priv numeric, p_client character varying)
  RETURNS void AS
$BODY$
declare
	fid_client integer;
	fid_good   integer;
begin
	select f_id into fid_client from
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(trim(p_client));
	select f_id into fid_good from
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(trim(p_fiche));
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
		qp_dep_priv)
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
		p_dep_priv);
	return;
end;
 $BODY$
LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION insert_quant_sold(p_internal text, p_jid numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_client character varying)
  RETURNS void AS
$BODY$
declare
	fid_client integer;
	fid_good   integer;
begin

	select f_id into fid_client from
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(trim(p_client));
	select f_id into fid_good from
		attr_value join jnt_fic_att_value using (jft_id) where ad_id=23 and av_text=upper(trim(p_fiche));
	insert into quant_sold
		(qs_internal,j_id,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client,qs_valid)
	values
		(p_internal,p_jid,fid_good,p_quant,p_price,p_vat,p_vat_code,fid_client,'Y');
	return;
end;
 $BODY$
LANGUAGE 'plpgsql';
drop view vw_fiche_attr;

create view vw_fiche_attr
as  SELECT a.f_id, a.fd_id, a.av_text AS vw_name, b.av_text AS vw_sell, c.av_text AS vw_buy, d.av_text AS tva_code, tva_rate.tva_id, tva_rate.tva_rate, tva_rate.tva_label, e.av_text AS vw_addr, f.av_text AS vw_cp, j.av_text AS quick_code, h.av_text as vw_description,fiche_def.frd_id
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
   LEFT JOIN tva_rate ON d.av_text = tva_rate.tva_id::text
   JOIN fiche_def USING (fd_id);

-- ajout n client dans attr_min
insert into attr_min values (2,30);
update attr_def set ad_text='Dpense  charge du grant (partie prive)' where ad_id=31;

CREATE OR REPLACE FUNCTION update_account_item_card()
  RETURNS void AS
$BODY$
declare
cCard cursor  for select jft_id,fd_class_base from fiche join fiche_def using (fd_id)
join jnt_fic_att_value using (f_id)
join attr_value using (jft_id)
where
ad_id=5 and
fd_create_account=false
and av_text = '';
njft_id integer;
sClass_base text;
begin
open cCard;
loop
	fetch cCard into njft_id,sClass_base;
	if NOT FOUND then
	exit;
	end if;
	update attr_value set av_text=sClass_base where jft_id=njft_id;
end loop;

end;
$BODY$
LANGUAGE 'plpgsql';

select update_account_item_card();

drop function update_account_item_card();

delete from action where ac_module='budget';

drop table bud_hypothese cascade;
drop table bud_detail_periode cascade;
drop table bud_detail cascade;
drop table bud_card cascade;
-- drop sequence bud_card_bc_id_seq;
-- drop sequence bud_detail_bd_id_seq
-- drop sequence bud_detail_bdp_id_seq;
-- drop sequence bud_detail_periode_bdp_id_seq;
comment on column action.ac_code is 'this code will be used in the code with the function User::check_action ';
comment on column action_detail.f_id is 'the concerned	card';
comment on column action_detail.ad_text is ' Description ';
comment on column action_detail.ad_pu is ' price per unit ';
comment on column action_detail.ad_quant is 'quantity ';
comment on column action_detail.ad_tva_id is ' tva_id ';
comment on column action_detail.ad_tva_amount is ' tva_amount ';
comment on column action_detail.ad_total_amount is ' total amount';
comment on column action_gestion.ag_type is ' type of action: see document_type ';
comment on column action_gestion.f_id_dest is ' third party ';
comment on column action_gestion.ag_title is ' title ';
comment on column action_gestion.ag_timestamp is ' ';
comment on column action_gestion.ag_cal is ' visible in the calendar if = C';
comment on column action_gestion.ag_ref_ag_id is ' concerning the action ';
comment on column action_gestion.ag_comment is ' comment of the action';
comment on column action_gestion.ag_ref is 'its reference ';
comment on column action_gestion.ag_priority is 'Low, medium, important ';
comment on column action_gestion.ag_dest is ' is the person who has to take care of this action ';
comment on column action_gestion.ag_owner is ' is the owner of this action ';
comment on column action_gestion.ag_contact is ' contact of the third part ';
comment on column action_gestion.ag_state is 'state of the action same as document_state ';
comment on table action_gestion  is 'Contains the details for the follow-up of customer, supplier, administration';
-- clean the bud part
delete from document where ag_id=0;

INSERT INTO action(ac_id, ac_description, ac_module, ac_code) VALUES (313, 'Administration', 'gestion', 'GEADM');
INSERT INTO action(ac_id, ac_description, ac_module, ac_code) VALUES (1600, 'Gestion des extensions', 'extension', 'EXTENSION');
INSERT INTO action(ac_id, ac_description, ac_module, ac_code) VALUES (1701, 'Consultation', 'prvision', 'PREVCON');
INSERT INTO action(ac_id, ac_description, ac_module, ac_code) VALUES (1702, 'Modification et cration', 'prvision', 'PREVMOD');
update action_gestion set ag_state=2,ag_priority=2,ag_owner='phpcompta';
-- Function: extension_ins_upd()

-- DROP FUNCTION extension_ins_upd();

CREATE OR REPLACE FUNCTION extension_ins_upd()
  RETURNS trigger AS
$BODY$
declare
 sCode text;
begin
sCode:=trim(upper(NEW.ex_code));
sCode:=replace(sCode,' ','_');
sCode:=substr(sCode,1,15);
sCode=upper(sCode);
NEW.ex_code:=sCode;
return NEW;

end;

$BODY$
LANGUAGE 'plpgsql';
-- Table: extension

-- DROP TABLE extension;

CREATE TABLE extension
(
  ex_id serial NOT NULL,
    ex_name character varying(30) NOT NULL,
  ex_code character varying(15) NOT NULL,
  ex_desc character varying(250),
  ex_file character varying NOT NULL,
  ex_enable "char" NOT NULL DEFAULT 'Y'::"char",
  CONSTRAINT pk_extension PRIMARY KEY (ex_id),
  CONSTRAINT idx_ex_code UNIQUE (ex_code)
);
COMMENT ON TABLE extension IS 'Content the needed information for the extension';
COMMENT ON COLUMN extension.ex_id IS 'Primary key';
COMMENT ON COLUMN extension.ex_code IS 'code of the extension ';
COMMENT ON COLUMN extension.ex_name IS 'code of the extension ';
COMMENT ON COLUMN extension.ex_desc IS 'Description of the extension ';
COMMENT ON COLUMN extension.ex_file IS 'path to the extension to include';
COMMENT ON COLUMN extension.ex_enable IS 'Y : enabled; N : disabled ';

CREATE TRIGGER trg_extension_ins_upd
  BEFORE INSERT OR UPDATE
  ON extension
  FOR EACH ROW
  EXECUTE PROCEDURE extension_ins_upd();

CREATE TABLE user_sec_extension
(
  use_id serial NOT NULL,
  ex_id integer NOT NULL,
  use_login text NOT NULL,
  use_access character(1) NOT NULL DEFAULT 0,
  CONSTRAINT user_sec_extension_pkey PRIMARY KEY (use_id),
  CONSTRAINT user_sec_extension_ex_id_key UNIQUE (ex_id, use_login)
);
COMMENT ON TABLE user_sec_extension IS 'Security for extension';

CREATE TABLE forecast
(
  f_id serial NOT NULL,
  f_name text NOT NULL,
  CONSTRAINT forecast_pk PRIMARY KEY (f_id)
);

COMMENT ON TABLE forecast IS 'contains the name of the forecast';


CREATE TABLE forecast_cat
(
  fc_id serial NOT NULL, -- primary key
  fc_desc text NOT NULL, -- text of the category
  f_id bigint, -- Foreign key, it is the parent from the table forecast
  fc_order integer NOT NULL DEFAULT 0, -- Order of the category, used when displaid
  CONSTRAINT forecast_cat_pk PRIMARY KEY (fc_id),
  CONSTRAINT forecast_child FOREIGN KEY (f_id)
      REFERENCES forecast (f_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
COMMENT ON COLUMN forecast_cat.fc_id IS 'primary key';
COMMENT ON COLUMN forecast_cat.fc_desc IS 'text of the category';
COMMENT ON COLUMN forecast_cat.f_id IS 'Foreign key, it is the parent from the table forecast';
COMMENT ON COLUMN forecast_cat.fc_order IS 'Order of the category, used when displaid';

CREATE TABLE forecast_item
(
  fi_id serial NOT NULL,
  fi_text text,
  fi_account text,
  fi_card integer,
  fi_order integer,
  fc_id integer,
  fi_amount numeric(20,4) DEFAULT 0,
  fi_debit "char" NOT NULL DEFAULT 'd'::"char",
  fi_pid integer,
  CONSTRAINT forecast_item_pkey PRIMARY KEY (fi_id),
  CONSTRAINT card FOREIGN KEY (fi_card)
      REFERENCES fiche (f_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_forecast FOREIGN KEY (fc_id)
      REFERENCES forecast_cat (fc_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
COMMENT ON COLUMN forecast_item.fi_id IS 'Primary key';
COMMENT ON COLUMN forecast_item.fi_text IS 'Label of the i	tem';
COMMENT ON COLUMN forecast_item.fi_account IS 'Accountancy entry';
COMMENT ON COLUMN forecast_item.fi_card IS 'Card (fiche.f_id)';
COMMENT ON COLUMN forecast_item.fi_amount IS 'Amount';
COMMENT ON COLUMN forecast_item.fi_debit IS 'possible values are D or C';
COMMENT ON COLUMN forecast_item.fi_order IS 'Order of showing (not used)';
COMMENT ON COLUMN forecast_item.fi_pid IS '0 for every month, or the value parm_periode.p_id ';

update version set val=60;

commit;
