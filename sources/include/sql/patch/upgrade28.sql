begin;
delete from user_sec_act where ua_act_id in (2,9,19,18);
delete from action where ac_id=2;
delete from action where ac_id=9;
delete from action where ac_id=19;
delete from action where ac_id=18;
update action set ac_description='Lecture du Grand-Livre' where ac_id=1;
insert into action values (31,'Gestion des périodes comptables');
update action set ac_description = 'Lecture des fiches' where ac_id=3;
update action set ac_description = 'Ajout de fiche et modification' where ac_id=15;
update action set ac_description = 'Création et modifications des rapports' where ac_id=5;

update version set val=29;
commit;