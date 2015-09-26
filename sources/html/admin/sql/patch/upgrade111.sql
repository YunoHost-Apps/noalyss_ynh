begin;

insert into menu_ref(me_code,me_menu,me_file, me_url,me_description,me_parameter,me_javascript,me_type,me_description_etendue)
values
('MANAGER','Administrateur','manager.inc.php',null,'Suivi des gérants, administrateurs et salariés',null,null,'ME','Suivi de vos salariés, managers ainsi que des administrateurs, pour les documents et les opérations comptables');

insert into profile_menu (me_code,me_code_dep,p_id,p_order, p_type_display,pm_default) 
values
('MANAGER','GESTION',1,25,'E',0), ('MANAGER','GESTION',2,25,'E',0);

insert into menu_ref(me_code,me_menu,me_file, me_url,me_description,me_parameter,me_javascript,me_type,me_description_etendue)
values
('CFGDEFMENU','Menu par défaut','default_menu.inc.php',null,'Configuration des menus par défaut',null,null,'ME','Configuration des menus par défaut, ces menus sont appelés par des actions dans d''autres menus');

insert into profile_menu (me_code,me_code_dep,p_id,p_order, p_type_display,pm_default) 
values
('CFGDEFMENU','MOD',1,30,'E',0);

insert into menu_ref(me_code,me_menu,me_file, me_url,me_description,me_parameter,me_javascript,me_type,me_description_etendue)
values
('AGENDA','Agenda','calendar.inc.php',null,'Agenda',null,null,'ME','Agenda, présentation du suivi sous forme d''agenda ');

insert into profile_menu (me_code,me_code_dep,p_id,p_order, p_type_display,pm_default) 
values
('AGENDA','NULL',1,410,'M',0),('AGENDA','NULL',2,410,'M',0);

create table menu_default
(
    md_id   serial primary key,
    md_code text not null unique ,
    me_code text not null
);
insert into menu_default (md_code,me_code) values ('code_invoice','COMPTA/VENMENU/VEN'),('code_follow','GESTION/FOLLOW');
update menu_ref set me_file='customer.inc.php' where me_code ='CUST';

update version set val=112;

commit;