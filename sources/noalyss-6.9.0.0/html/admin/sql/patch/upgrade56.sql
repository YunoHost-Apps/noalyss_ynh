begin;
CREATE OR REPLACE FUNCTION create_missing_sequence()
  RETURNS integer AS
$BODY$
declare
p_sequence text;
nSeq integer;
c1 cursor for select jrn_def_id from jrn_def;
begin
	open c1;
	loop
	   fetch c1 into nSeq;
	   if not FOUND THEN
	   	close c1;
	   	return 0;
	   end if;
	   p_sequence:='s_jrn_pj'||nSeq::text;
	execute 'create sequence '||p_sequence;
	end loop;
close c1;
return 0;

end;
$BODY$
LANGUAGE 'plpgsql';
  
select create_missing_sequence();  
  
  
CREATE OR REPLACE FUNCTION drop_index(p_constraint character varying)
  RETURNS void AS
$BODY$
declare 
	nCount integer;
begin
	select count(*) into nCount from pg_indexes where indexname=p_constraint;
	if nCount = 1 then
	execute 'drop index '||p_constraint ;
	end if;
end;
$BODY$
LANGUAGE 'plpgsql';
-- on dossier 

insert into parameter (pr_id,pr_value) values ('MY_TVA_USE','Y');
insert into parameter (pr_id,pr_value) values ('MY_PJ_SUGGEST','Y');

-- new security
alter table action add ac_module text;
alter table action add ac_code varchar(9);
create unique index uj_login_uj_jrn_id on user_sec_jrn(uj_login,uj_jrn_id);	

-- PostgreSQL database dump
--
delete from user_Sec_act;
delete from action;

COMMENT ON TABLE action IS 'The different privileges';
select drop_index('x_act');

INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (100, 'Accès en lecture', 'budget', 'BUDLEC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (110, 'Création hypothèse', 'budget', 'BUDHYP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (120, 'Création de fiche', 'budget', 'BUDFIC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (130, 'Impression', 'budget', 'BUDIMP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (210, 'Ajout de plan analytique', 'compta_anal', 'CAPA');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (220, 'Ajout de poste analytique', 'compta_anal', 'CAPO');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (230, 'Ajout de groupe analytique', 'compta_anal', 'CAGA');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (235, 'Ajout d''operation diverses', 'compta_anal', 'CAOD');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (240, 'Impression', 'compta_anal', 'CAIMP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (300, 'Gestion', 'gestion', 'GESTION');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (305, 'Import en Banque', 'gestion', 'GEBQ');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (307, 'Effacement d''opération', 'gestion', 'GEOP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (310, 'Courrier (lecture & écriture)', 'gestion', 'GECOUR');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (311, 'Fournisseur', 'gestion', 'GESUPPL');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (312, 'Client', 'gestion', 'GECUST');
-- INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (320, 'gestion de stock', 'gestion', 'GESTOCK');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (700, 'Rapport', 'impression', 'IMPRAP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (710, 'Journaux', 'impression', 'IMPJRN');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (720, 'Fiche', 'impression', 'IMPFIC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (730, 'Poste', 'impression', 'IMPPOSTE');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (740, 'Bilan', 'impression', 'IMPBIL');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (750, 'Balance', 'impression', 'IMPBAL');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (800, 'Ajout de fiche', 'fiche', 'FICADD');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (805, 'Création, modification et effacement de fiche', 'fiche', 'FIC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (910, 'création, modification et effacement de catégorie de fiche', 'fiche', 'FICCAT');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1100, 'Mode comptabilité analytique', 'parametre', 'PARCA');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1110, 'Ajout de période', 'parametre', 'PARPER');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1120, 'Catégorie des fiches', 'parametre', 'PARFIC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1130, 'Document', 'parametre', 'PARDOC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1140, 'Modification journaux', 'parametre', 'PARJRN');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1150, 'TVA', 'parametre', 'PARTVA');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1160, 'Moyen de paiement', 'parametre', 'PARMP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1180, 'Clôture ', 'parametre', 'PARCLO');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1185, 'Changement du plan comptable ', 'parametre', 'PARPCMN');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1170, 'Poste Comptable de base', 'parametre', 'PARPOS');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1190, 'Centralisation', 'parametre', 'PARCENT');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1200, 'Écriture d''ouverture', 'parametre', 'PAREO');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1210, 'Mode strict', 'parametre', 'PARSTR');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1220, 'Coordonnées société', 'parametre', 'PARCOORD');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1230, 'Création de rapport', 'parametre', 'PARRAP');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1240, 'Effacement et création d''opération prédéfinie', 'parametre', 'PARPREDE');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1245, 'Sécurité du dossier', 'parametre', 'PARSEC');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1500, 'Stock (lecture)', 'stock', 'STOLE');
INSERT INTO action (ac_id, ac_description, ac_module, ac_code) VALUES (1510, 'Stock (changement)', 'stock', 'STOWRITE');

ALTER TABLE jrn ADD COLUMN jr_pj_number text;
ALTER TABLE del_jrn ADD COLUMN jr_pj_number text;
ALTER TABLE jrn_def ADD COLUMN jrn_def_pj_pref text;

update jrn_def set jrn_def_pj_pref=jrn_def_type ;

CREATE OR REPLACE FUNCTION jrn_del()
  RETURNS trigger AS
$BODY$
declare
row jrn%ROWTYPE;
begin
row:=OLD;
insert into del_jrn ( jr_id,
       jr_def_id,
       jr_montant,
       jr_comment,
       jr_date,
       jr_grpt_id,
       jr_internal,
       jr_tech_date,
       jr_tech_per,
       jrn_ech,
       jr_ech,
       jr_rapt,
       jr_valid,
       jr_opid,
       jr_c_opid,
       jr_pj,
       jr_pj_name,
       jr_pj_type,
       jr_pj_number,
       del_jrn_date) 
       select  jr_id,
	      jr_def_id,
	      jr_montant,
	      jr_comment,
	      jr_date,
	      jr_grpt_id,
	      jr_internal,
	      jr_tech_date,
	      jr_tech_per,
	      jrn_ech,
	      jr_ech,
	      jr_rapt,
	      jr_valid,
	      jr_opid,
	      jr_c_opid,
	      jr_pj,
	      jr_pj_name,
	      jr_pj_type,
	      jr_pj_number
	      ,now() from jrn where jr_id=row.jr_id;
return row;
end;
$BODY$
LANGUAGE 'plpgsql' ;



update version set val=57;

commit;
