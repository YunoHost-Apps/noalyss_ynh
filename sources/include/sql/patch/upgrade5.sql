-- create vw_client view
create view vw_client as
select a.f_id,
a.av_text as name,
b.av_text as tva_num,c.av_text as poste_comptable,
d.av_text as rue,
e.av_text as code_postal,
f.av_text as pays,
g.av_text as telephone,
h.av_text as email
	from ( 
	select * from fiche join fiche_def using (fd_id) 
	join fiche_def_ref using (frd_id) 
	join jnt_fic_att_value using (f_id) join attr_value using (jft_id) where ad_id=1 ) a
	left join  ( 
	select * from fiche join fiche_def using (fd_id) 
	join fiche_def_ref using (frd_id) 
	join jnt_fic_att_value using (f_id) join attr_value using (jft_id) where ad_id=13 ) b using (f_id)
	left join  ( 
	select * from fiche join fiche_def using (fd_id) 
	join fiche_def_ref using (frd_id) 
	join jnt_fic_att_value using (f_id) 
	join attr_value using (jft_id) 
	where ad_id=5  ) c using (f_id)
	left join  ( 
	select * from fiche join fiche_def using (fd_id) 
	join fiche_def_ref using (frd_id) 
	join jnt_fic_att_value using (f_id) 
	join attr_value using (jft_id) 
	where ad_id=14  ) d using (f_id)
	left join  ( 
	select * from fiche join fiche_def using (fd_id) 
	join fiche_def_ref using (frd_id) 
	join jnt_fic_att_value using (f_id) 
	join attr_value using (jft_id) 
	where ad_id=15  ) e using (f_id)
	left join  ( 
	select * from fiche join fiche_def using (fd_id) 
	join fiche_def_ref using (frd_id) 
	join jnt_fic_att_value using (f_id) 
	join attr_value using (jft_id) 
	where ad_id=16  ) f using (f_id)
	left join  ( 
	select * from fiche join fiche_def using (fd_id) 
	join fiche_def_ref using (frd_id) 
	join jnt_fic_att_value using (f_id) 
	join attr_value using (jft_id) 
	where ad_id=17  ) g using (f_id)
	left join  ( 
	select * from fiche join fiche_def using (fd_id) 
	join fiche_def_ref using (frd_id) 
	join jnt_fic_att_value using (f_id) 
	join attr_value using (jft_id) 
	where ad_id=18  ) h using (f_id)
where a.frd_id=9;

-- all the min attribut for card reference

create view vw_fiche_min 
	as select frd_id, ad_id, ad_text, frd_text, frd_class_base
	 from  
	attr_min join attr_Def using (ad_id) 
	join fiche_Def_ref using (frd_id);
-- definition for card
create view vw_fiche_Def as
	 SELECT fd_id, 
	ad_id, 
	ad_text, 
	fd_class_base, 
	fd_label, 
	fd_create_account, 
	frd_id
   FROM jnt_fic_attr
   JOIN attr_def  USING (ad_id)
   JOIN fiche_def USING (fd_id);

-- comments
comment on view vw_fiche_min is 'minimum attribut for reference card';
comment on view vw_fiche_def is 'all the attributs for  card family';
comment on view vw_client is 'minimum attribut for the customer (frd_id=9)';

-- new table : parameter
create table parameter (
	pr_id text primary key,
	pr_value text
);

insert into parameter (pr_id) values ('MY_NAME');
insert into parameter (pr_id) values ('MY_CP');
insert into parameter (pr_id) values ('MY_COMMUNE');
insert into parameter (pr_id) values ('MY_TVA');
insert into parameter (pr_id) values ('MY_STREET');
insert into parameter (pr_id) values ('MY_NUMBER');


update  version set val=6;

