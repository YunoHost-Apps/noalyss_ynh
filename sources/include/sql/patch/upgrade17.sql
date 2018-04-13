begin;
-- add quick code for contact
insert into attr_min (frd_id,ad_id) values (16,23);



insert into jnt_fic_attr select fd_id,23 from fiche_Def where frd_id=16;
insert into jnt_fic_att_value(jft_id,f_id,ad_id) select nextval('s_jnt_fic_att_value')+200,f_id,23 from fiche 
	where fd_id in (select fd_id from fiche_Def where frd_id=16);
insert into attr_value select jft_id,'FID'||f_id from jnt_fic_att_value join fiche using(f_id) where ad_id=23 and
 fd_id in (select fd_id from fiche_Def where frd_id=16);

update version set val=18;
commit;