begin;

drop view vw_supplier;
drop view vw_fiche_min;
drop view vw_client;

alter table fiche_def_ref alter frd_class_base type account_type;

create view vw_client as
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





create view vw_fiche_min as 
SELECT attr_min.frd_id, attr_min.ad_id, attr_def.ad_text, fiche_def_ref.frd_text, fiche_def_ref.frd_class_base
   FROM attr_min
   JOIN attr_def USING (ad_id)
   JOIN fiche_def_ref USING (frd_id);

create view vw_supplier as
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
  alter table attr_min add constraint frd_ad_attr_min_pk primary key (frd_id,ad_id);

update version set val=76;

commit;
