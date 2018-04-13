begin;
alter table attr_def add ad_extra text ;

insert into attr_def (ad_id,ad_text,ad_type,ad_size) values (33,'Date Fin','date',8);

ALTER TABLE fiche_detail DROP CONSTRAINT "$2";

ALTER TABLE fiche_detail
  ADD CONSTRAINT fiche_detail_attr_def_fk FOREIGN KEY (ad_id)
      REFERENCES attr_def (ad_id) MATCH SIMPLE
      ON UPDATE cascade ON DELETE cascade;

ALTER TABLE jnt_fic_attr DROP CONSTRAINT "$2";

ALTER TABLE jnt_fic_attr
  ADD CONSTRAINT jnt_fic_attr_attr_def_fk FOREIGN KEY (ad_id)
      REFERENCES attr_def (ad_id) MATCH SIMPLE
      ON UPDATE cascade ON DELETE cascade;

insert into menu_ref(me_code,me_menu,me_type) values ('CVS:reportinit','Export définition d''un raport','PR');

insert  into profile_menu (me_code,p_id,p_type_display) values ('CVS:reportinit',1,'P');
insert  into profile_menu (me_code,p_id,p_type_display) values ('CVS:reportinit',2,'P');

update version set val=101;

commit;