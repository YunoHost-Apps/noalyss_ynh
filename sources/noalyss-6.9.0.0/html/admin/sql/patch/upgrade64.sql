begin;

DROP VIEW vw_fiche_attr;

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

update version set val=65;

commit;

