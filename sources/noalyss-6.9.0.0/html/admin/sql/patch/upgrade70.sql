begin;
insert into action (ac_id,ac_description,ac_module,ac_code) values (1135,'Ajoute ou modifie des catégories de documents','parametre','PARCATDOC');
update version set val=71;
commit;
