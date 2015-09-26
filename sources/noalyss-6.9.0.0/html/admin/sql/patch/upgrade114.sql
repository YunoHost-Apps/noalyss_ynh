begin;

DROP VIEW v_detail_purchase;

CREATE OR REPLACE VIEW v_detail_purchase AS 
 WITH m AS (
         SELECT sum(quant_purchase.qp_price) AS htva, sum(quant_purchase.qp_vat) AS tot_vat, jrn.jr_id
           FROM quant_purchase
      JOIN jrnx USING (j_id)
   JOIN jrn ON jrnx.j_grpt = jrn.jr_grpt_id
  GROUP BY jrn.jr_id
        )
 SELECT jrn.jr_id, jrn.jr_date, jrn.jr_date_paid,jr_ech,
 jrn.jr_tech_per, jrn.jr_comment, 
 jrn.jr_pj_number, jrn.jr_internal, 
 jrn.jr_def_id, jrnx.j_poste, 
 jrnx.j_text, jrnx.j_qcode, 
 quant_purchase.qp_fiche AS item_card, a.name AS item_name, 
 quant_purchase.qp_supplier, b.vw_name AS tiers_name, 
 b.quick_code, tva_rate.tva_label, 
 tva_rate.tva_comment, tva_rate.tva_both_side, quant_purchase.qp_vat_sided AS vat_sided, quant_purchase.qp_vat_code AS vat_code, quant_purchase.qp_vat AS vat, quant_purchase.qp_price AS price, quant_purchase.qp_quantite AS quantity, quant_purchase.qp_price / quant_purchase.qp_quantite AS price_per_unit, quant_purchase.qp_nd_amount AS non_ded_amount, quant_purchase.qp_nd_tva AS non_ded_tva, quant_purchase.qp_nd_tva_recup AS non_ded_tva_recup, m.htva, m.tot_vat
   FROM jrn
   JOIN jrnx ON jrn.jr_grpt_id = jrnx.j_grpt
   JOIN quant_purchase USING (j_id)
   JOIN vw_fiche_name a ON quant_purchase.qp_fiche = a.f_id
   JOIN vw_fiche_attr b ON quant_purchase.qp_supplier = b.f_id
   JOIN tva_rate ON quant_purchase.qp_vat_code = tva_rate.tva_id
   JOIN m ON m.jr_id = jrn.jr_id;

DROP VIEW v_detail_sale;

CREATE OR REPLACE VIEW v_detail_sale AS 
 WITH m AS (
         SELECT sum(quant_sold.qs_price) AS htva, sum(quant_sold.qs_vat) AS tot_vat, jrn.jr_id
           FROM quant_sold
      JOIN jrnx USING (j_id)
   JOIN jrn ON jrnx.j_grpt = jrn.jr_grpt_id
  GROUP BY jrn.jr_id
        )
 SELECT jrn.jr_id, jrn.jr_date, jrn.jr_date_paid,jr_ech,jrn.jr_tech_per, jrn.jr_comment, jrn.jr_pj_number, jrn.jr_internal, jrn.jr_def_id, jrnx.j_poste, jrnx.j_text, jrnx.j_qcode, quant_sold.qs_fiche AS item_card, a.name AS item_name, quant_sold.qs_client, b.vw_name AS tiers_name, b.quick_code, tva_rate.tva_label, tva_rate.tva_comment, tva_rate.tva_both_side, quant_sold.qs_vat_sided AS vat_sided, quant_sold.qs_vat_code AS vat_code, quant_sold.qs_vat AS vat, quant_sold.qs_price AS price, quant_sold.qs_quantite AS quantity, quant_sold.qs_price / quant_sold.qs_quantite AS price_per_unit, m.htva, m.tot_vat
    FROM jrn
   JOIN jrnx ON jrn.jr_grpt_id = jrnx.j_grpt
   JOIN quant_sold USING (j_id)
   JOIN vw_fiche_name a ON quant_sold.qs_fiche = a.f_id
   JOIN vw_fiche_attr b ON quant_sold.qs_client = b.f_id
   JOIN tva_rate ON quant_sold.qs_vat_code = tva_rate.tva_id
   join  m on m.jr_id=jrn.jr_id;

update version set val=115;

commit;
