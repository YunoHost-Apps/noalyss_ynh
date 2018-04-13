begin;

insert into fiche_def_ref(frd_id,frd_text) values (26,'Projet');
insert into attr_min (frd_id,ad_id) values (26,1),(26,9);
CREATE OR REPLACE FUNCTION public.upgrade_repo(p_version integer)
 RETURNS void
AS $function$
declare 
        is_mono integer;
begin
        select count (*) into is_mono from information_schema.tables where table_name='repo_version';
        if is_mono = 1 then
                update repo_version set val=p_version;
        else
                update version set val=p_version;
        end if;
end;
$function$
 language plpgsql;



INSERT INTO menu_ref(me_code, me_menu, me_file,   me_type,me_description_etendue)VALUES ('ANCKEY', 'Clef de répartition',  'anc_key.inc.php','ME','Permet de gèrer les clefs de répartition en comptabilité analytique');

insert into profile_menu(me_code,p_id,p_type_display,pm_default,me_code_dep,p_order) values ('ANCKEY',1,'E',0,'ANC',15);
insert into profile_menu(me_code,p_id,p_type_display,pm_default,me_code_dep,p_order) values ('ANCKEY',2,'E',0,'ANC',15);

INSERT INTO menu_ref(me_code, me_menu, me_file,   me_type,me_description_etendue)VALUES ('CFGPLUGIN', 'Configuration extension',  'cfgplugin.inc.php','ME','Permet d''installer et d''activer facilement des extensions');

insert into profile_menu(me_code,p_id,p_type_display,pm_default,me_code_dep,p_order) values ('CFGPLUGIN',1,'E',0,'PARAM',15);
insert into profile_menu(me_code,p_id,p_type_display,pm_default,me_code_dep,p_order) values ('CFGPLUGIN',2,'E',0,'PARAM',15);

create table key_distribution (
    kd_id serial primary key,
    kd_name text,
    kd_description text);

create table key_distribution_ledger (
    kl_id serial primary key,
    kd_id bigint not null references key_distribution(kd_id) on update cascade on delete cascade,
    jrn_def_id bigint not null references jrn_def(jrn_def_id) on update cascade on delete cascade
    );

create table key_distribution_detail(
    ke_id serial primary key,
    kd_id bigint not null references key_distribution(kd_id) on update cascade on delete cascade,
    ke_row  integer not null,
    ke_percent numeric(20,4) not null 

    );

create table key_distribution_activity
(
    ka_id serial primary key,
    ke_id  bigint not null  references key_distribution_detail(ke_id) on update cascade on delete cascade,
    po_id bigint  references poste_analytique(po_id) on update cascade on delete cascade,
    pa_id bigint not null references plan_analytique(pa_id) on update cascade on delete cascade
);

comment on table key_distribution is 'Distribution key for analytic';
comment on table key_distribution_activity is 'activity (account) linked to the row';
comment on column key_distribution.kd_id is 'PK';
comment on column key_distribution.kd_name is 'Name of the key';
comment on column key_distribution.kd_description is 'Description of the key';

comment on table key_distribution_ledger is 'Legder where the distribution key can be used' ;
comment on column key_distribution_ledger.kl_id is 'pk';
comment on column key_distribution_ledger.kd_id is 'fk to key_distribution';
comment on column key_distribution_ledger.jrn_def_id is 'fk to jrnd_def, ledger where this key is available';


comment on table key_distribution_detail is 'Row of activity and percent';
comment on column key_distribution_detail.ke_id is 'pk';
comment on column key_distribution_detail.kd_id is 'fk to key_distribution';
comment on column key_distribution_detail.ke_row is 'group order';

comment on table key_distribution_activity is 'Contains the analytic account';
comment on column key_distribution_activity.ka_id is 'pk';
comment on column key_distribution_activity.ke_id is 'fk to key_distribution_detail';
comment on column key_distribution_activity.po_id is 'fk to poste_analytique';
comment on column key_distribution_activity.pa_id is 'fk to plan_analytique';

drop view vw_fiche_attr cascade;

CREATE view vw_fiche_attr as 
SELECT a.f_id, a.fd_id, a.ad_value AS vw_name, k.ad_value AS vw_first_name, b.ad_value AS vw_sell, c.ad_value AS vw_buy, d.ad_value AS tva_code, tva_rate.tva_id, tva_rate.tva_rate, tva_rate.tva_label, e.ad_value AS vw_addr, f.ad_value AS vw_cp, j.ad_value AS quick_code, h.ad_value AS vw_description, i.ad_value AS tva_num, fiche_def.frd_id,l.ad_value as accounting
   FROM ( SELECT fiche.f_id, fiche.fd_id, fiche_detail.ad_value
           FROM fiche
      LEFT JOIN fiche_detail USING (f_id)
     WHERE fiche_detail.ad_id = 1) a
   LEFT JOIN ( SELECT fiche_detail.f_id, fiche_detail.ad_value
           FROM fiche_detail
          WHERE fiche_detail.ad_id = 6) b ON a.f_id = b.f_id
   LEFT JOIN ( SELECT fiche_detail.f_id, fiche_detail.ad_value
      FROM fiche_detail
     WHERE fiche_detail.ad_id = 7) c ON a.f_id = c.f_id
   LEFT JOIN ( SELECT fiche_detail.f_id, fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 2) d ON a.f_id = d.f_id
   LEFT JOIN ( SELECT fiche_detail.f_id, fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 14) e ON a.f_id = e.f_id
   LEFT JOIN ( SELECT fiche_detail.f_id, fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 15) f ON a.f_id = f.f_id
   LEFT JOIN ( SELECT fiche_detail.f_id, fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 23) j ON a.f_id = j.f_id
   LEFT JOIN ( SELECT fiche_detail.f_id, fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 9) h ON a.f_id = h.f_id
   LEFT JOIN ( SELECT fiche_detail.f_id, fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 13) i ON a.f_id = i.f_id
   LEFT JOIN ( SELECT fiche_detail.f_id, fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 32) k ON a.f_id = k.f_id
   LEFT JOIN tva_rate ON d.ad_value = tva_rate.tva_id::text
   JOIN fiche_def USING (fd_id)
LEFT JOIN ( SELECT fiche_detail.f_id, fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 5) l ON a.f_id = l.f_id;


create view v_detail_sale  as 
WITH m AS (
         SELECT sum(quant_sold.qs_price) AS htva, sum(quant_sold.qs_vat) AS tot_vat,sum(quant_sold.qs_vat_sided) as tot_tva_np, jrn.jr_id
           FROM quant_sold
      JOIN jrnx USING (j_id)
   JOIN jrn ON jrnx.j_grpt = jrn.jr_grpt_id
  GROUP BY jrn.jr_id
        )
SELECT jrn.jr_id, jrn.jr_date, jrn.jr_date_paid, jrn.jr_ech, jrn.jr_tech_per, jrn.jr_comment, jrn.jr_pj_number, jrn.jr_internal, jrn.jr_def_id, jrnx.j_poste, jrnx.j_text, jrnx.j_qcode, quant_sold.qs_fiche AS item_card, a.name AS item_name, quant_sold.qs_client, b.vw_name AS tiers_name, b.quick_code, tva_rate.tva_label, tva_rate.tva_comment, tva_rate.tva_both_side, quant_sold.qs_vat_sided AS vat_sided, quant_sold.qs_vat_code AS vat_code, quant_sold.qs_vat AS vat, quant_sold.qs_price AS price, quant_sold.qs_quantite AS quantity, quant_sold.qs_price / quant_sold.qs_quantite AS price_per_unit, m.htva, m.tot_vat,m.tot_tva_np
   FROM jrn
   JOIN jrnx ON jrn.jr_grpt_id = jrnx.j_grpt
   JOIN quant_sold USING (j_id)
   JOIN vw_fiche_name a ON quant_sold.qs_fiche = a.f_id
   JOIN vw_fiche_attr b ON quant_sold.qs_client = b.f_id
   JOIN tva_rate ON quant_sold.qs_vat_code = tva_rate.tva_id
   JOIN m ON m.jr_id = jrn.jr_id;


create view v_detail_purchase as
  WITH m AS (
         SELECT sum(quant_purchase.qp_price) AS htva, sum(quant_purchase.qp_vat) AS tot_vat, sum(quant_purchase.qp_vat_sided) as tot_tva_np, jrn.jr_id
           FROM quant_purchase
      JOIN jrnx USING (j_id)
   JOIN jrn ON jrnx.j_grpt = jrn.jr_grpt_id
  GROUP BY jrn.jr_id
        )
 SELECT jrn.jr_id, jrn.jr_date, jrn.jr_date_paid, jrn.jr_ech, jrn.jr_tech_per, jrn.jr_comment, jrn.jr_pj_number, jrn.jr_internal, jrn.jr_def_id, jrnx.j_poste, jrnx.j_text, jrnx.j_qcode, quant_purchase.qp_fiche AS item_card, a.name AS item_name, 
quant_purchase.qp_supplier, b.vw_name AS tiers_name, b.quick_code, tva_rate.tva_label, 
tva_rate.tva_comment, tva_rate.tva_both_side, 
quant_purchase.qp_vat_sided AS vat_sided, 
quant_purchase.qp_vat_code AS vat_code, 
quant_purchase.qp_vat AS vat, 
quant_purchase.qp_price AS price, 
quant_purchase.qp_quantite AS quantity,
quant_purchase.qp_price / quant_purchase.qp_quantite AS price_per_unit, 
quant_purchase.qp_nd_amount AS non_ded_amount,
 quant_purchase.qp_nd_tva AS non_ded_tva, 
quant_purchase.qp_nd_tva_recup AS non_ded_tva_recup,
 m.htva, m.tot_vat
,m.tot_tva_np
   FROM jrn
   JOIN jrnx ON jrn.jr_grpt_id = jrnx.j_grpt
   JOIN quant_purchase USING (j_id)
   JOIN vw_fiche_name a ON quant_purchase.qp_fiche = a.f_id
   JOIN vw_fiche_attr b ON quant_purchase.qp_supplier = b.f_id
   JOIN tva_rate ON quant_purchase.qp_vat_code = tva_rate.tva_id
   JOIN m ON m.jr_id = jrn.jr_id;

create index jrnx_j_qcode_ix on jrnx (j_qcode);

CREATE TABLE action_person 
(
    ap_id  SERIAL NOT NULL, 
    ag_id int4 NOT NULL references action_gestion(ag_id) on update cascade on delete cascade,
    f_id int4 not null references fiche(f_id) on update cascade on delete cascade, 
    PRIMARY KEY (ap_id));

COMMENT ON TABLE action_person IS 'Person involved in the action';
comment on column action_person.ap_id is 'pk';
comment on column action_person.ag_id is 'fk to action_action';
comment on column action_person.ag_id is 'fk to fiche';

ALTER TABLE action_person ADD CONSTRAINT action_gestion_ag_id_fk2 FOREIGN KEY (ag_id) REFERENCES  action_gestion (ag_id);
ALTER TABLE action_person ADD CONSTRAINT fiche_f_id_fk2  FOREIGN KEY (f_id) REFERENCES fiche(f_id);

alter table action_gestion alter f_id_dest drop not null;
update action_gestion set f_id_dest = null where f_id_dest = 0;
update action_gestion set f_id_dest =null where f_id_dest not in (select f_id from fiche);

ALTER TABLE action_gestion ADD CONSTRAINT fiche_f_id_fk3  FOREIGN KEY (f_id_dest) REFERENCES fiche(f_id);
create index fk_action_person_action_gestion on action_person (ag_id);
create index fk_action_person_fiche on action_person (f_id);


CREATE OR REPLACE FUNCTION comptaproc.category_card_before_delete()
  RETURNS trigger AS
$BODY$

begin
    if OLD.fd_id > 499000 then
        return null;
    end if;
    return OLD;

end;
$BODY$
language plpgsql;

CREATE TRIGGER trg_category_card_before_delete
  BEFORE delete
  ON fiche_def
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.category_card_before_delete();
-- bug 
alter table action_gestion alter ag_title type text;
alter table action_gestion add constraint fk_action_gestion_document_type foreign key (ag_type) references document_type(dt_id);


update version set val=117;

commit;