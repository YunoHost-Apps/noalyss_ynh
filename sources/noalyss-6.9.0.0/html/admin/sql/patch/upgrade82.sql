begin;

CREATE OR REPLACE VIEW vw_fiche_name AS 
 SELECT jnt_fic_att_value.f_id, attr_value.av_text AS name
   FROM jnt_fic_att_value
   JOIN attr_value USING (jft_id)
  WHERE jnt_fic_att_value.ad_id = 1;


update version set val=83;
commit;