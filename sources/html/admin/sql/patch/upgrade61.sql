begin;
-- View: vw_fiche_def

DROP VIEW vw_fiche_def;

CREATE  VIEW vw_fiche_def AS 
 SELECT jnt_fic_attr.fd_id, jnt_fic_attr.ad_id, attr_def.ad_text,fiche_def.fd_class_base, fiche_def.fd_label, fiche_def.fd_create_account, fiche_def.frd_id
   from  fiche_def 
   join jnt_fic_attr USING (fd_id)
   JOIN attr_def ON attr_def.ad_id = jnt_fic_attr.ad_id
;
COMMENT ON VIEW vw_fiche_def IS 'all the attributs for  card family';


commit;
