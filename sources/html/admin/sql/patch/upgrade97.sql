begin;
drop table public.import_tmp;
drop table public.format_csv_banque;
insert into parameter values ('MY_ALPHANUM','N');
update PARAMETER set pr_value='N' where pr_id='MY_CHECK_PERIODE';
delete from user_sec_act where ua_act_id not in (800,805,910);
delete from action where ac_id not in (800,805,910);
insert into action (ac_id,ac_description, ac_module, ac_code) values(1020,'Effacer les documents du suivi','followup','RMDOC');
insert into action (ac_id,ac_description, ac_module, ac_code) values(1010,'Voir les documents du suivi','followup','VIEWDOC');
insert into action (ac_id,ac_description, ac_module, ac_code) values(1050,'Modifier le type de document','followup','PARCATDOC');
create unique index qcode_idx on fiche_detail (ad_value) where ad_id=23;

CREATE OR REPLACE FUNCTION comptaproc.account_alphanum()
  RETURNS boolean AS
$BODY$
declare
	l_auto bool;
begin
	l_auto := true;
	select pr_value into l_auto from parameter where pr_id='MY_ALPHANUM';
	if l_auto = 'N' or l_auto is null then
		l_auto:=false;
	end if;
	return l_auto;
end;
$BODY$
  LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION comptaproc.account_compute(p_f_id integer)
  RETURNS account_type AS
$BODY$
declare
	class_base fiche_def.fd_class_base%type;
	maxcode numeric;
	sResult account_type;
	bAlphanum bool;
	sName text;
begin
	select fd_class_base into class_base
	from
		fiche_def join fiche using (fd_id)
	where
		f_id=p_f_id;
	raise notice 'account_compute class base %',class_base;
	bAlphanum := account_alphanum();
	if bAlphanum = false  then
		select count (pcm_val) into maxcode from tmp_pcmn where pcm_val_parent = class_base;
		if maxcode = 0	then
			maxcode:=class_base::numeric;
		else
			select max (pcm_val) into maxcode from tmp_pcmn where pcm_val_parent = class_base;
			maxcode:=maxcode::numeric;
		end if;
		if maxcode::text = class_base then
			maxcode:=class_base::numeric*1000;
		end if;
		maxcode:=maxcode+1;
		raise notice 'account_compute Max code %',maxcode;
		sResult:=maxcode::account_type;
	else
		-- if alphanum, use name
		select ad_value into sName from fiche_detail where f_id=p_f_id and ad_id=1;
		if sName is null then
			raise exception 'Cannot compute an accounting without the name of the card for %',p_f_id;
		end if;
		sResult := class_base||sName;
	end if;
	return sResult;
end;
$BODY$
LANGUAGE plpgsql;

DROP FUNCTION comptaproc.account_insert(integer, text);

CREATE OR REPLACE FUNCTION comptaproc.account_insert(p_f_id integer, p_account text)
  RETURNS text  AS
$BODY$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	sName varchar;
	sNew tmp_pcmn.pcm_val%type;
	bAuto bool;
	nFd_id integer;
	sClass_Base fiche_def.fd_class_base%TYPE;
	nCount integer;
	first text;
	second text;
begin

	if p_account is not null and length(trim(p_account)) != 0 then
	-- if there is coma in p_account, treat normally
		if position (',' in p_account) = 0 then
			raise info 'p_account is not empty';
				select count(*)  into nCount from tmp_pcmn where pcm_val=p_account::account_type;
				raise notice 'found in tmp_pcm %',nCount;
				if nCount !=0  then
					raise info 'this account exists in tmp_pcmn ';
					perform attribut_insert(p_f_id,5,p_account);
				   else
				       -- account doesn't exist, create it
					select ad_value into sName from
						fiche_detail
					where
					ad_id=1 and f_id=p_f_id;

					nParent:=account_parent(p_account::account_type);
					insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (p_account::account_type,sName,nParent);
					perform attribut_insert(p_f_id,5,p_account);

				end if;
		else
		raise info 'presence of a comma';
		-- there is 2 accounts separated by a comma
		first := split_part(p_account,',',1);
		second := split_part(p_account,',',2);
		-- check there is no other coma
		raise info 'first value % second value %', first, second;

		if  position (',' in first) != 0 or position (',' in second) != 0 then
			raise exception 'Too many comas, invalid account';
		end if;
		perform attribut_insert(p_f_id,5,p_account);
		end if;
	else
	raise info 'p_account is  empty';
		select fd_id into nFd_id from fiche where f_id=p_f_id;
		bAuto:= account_auto(nFd_id);

		select fd_class_base into sClass_base from fiche_def where fd_id=nFd_id;
raise info 'sClass_Base : %',sClass_base;
		if bAuto = true and sClass_base similar to '[[:digit:]]*'  then
			raise info 'account generated automatically';
			sNew:=account_compute(p_f_id);
			raise info 'sNew %', sNew;
			select ad_value into sName from
				fiche_detail
			where
				ad_id=1 and f_id=p_f_id;
			nParent:=account_parent(sNew);
			sNew := account_add  (sNew,sName);
			perform attribut_insert(p_f_id,5,sNew);

		else
		-- if there is an account_base then it is the default
		      select fd_class_base::account_type into sNew from fiche_def join fiche using (fd_id) where f_id=p_f_id;
			if sNew is null or length(trim(sNew)) = 0 then
				raise notice 'count is null';
				 perform attribut_insert(p_f_id,5,null);
			else
				 perform attribut_insert(p_f_id,5,sNew);
			end if;
		end if;
	end if;

return 0;
end;
$BODY$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION comptaproc.account_update(p_f_id integer, p_account account_type)
  RETURNS integer AS
$BODY$
declare
	nMax fiche.f_id%type;
	nCount integer;
	nParent tmp_pcmn.pcm_val_parent%type;
	sName varchar;
	first text;
	second text;
begin

	if length(trim(p_account)) != 0 then
		-- 2 accounts in card separated by comma
		if position (',' in p_account) = 0 then
			select count(*) into nCount from tmp_pcmn where pcm_val=p_account;
			if nCount = 0 then
			select ad_value into sName from
				fiche_detail
				where
				ad_id=1 and f_id=p_f_id;
			nParent:=account_parent(p_account);
			insert into tmp_pcmn(pcm_val,pcm_lib,pcm_val_parent) values (p_account,sName,nParent);
		end if;
		else
		raise info 'presence of a comma';
		-- there is 2 accounts separated by a comma
		first := split_part(p_account,',',1);
		second := split_part(p_account,',',2);
		-- check there is no other coma
		raise info 'first value % second value %', first, second;

		if  position (',' in first) != 0 or position (',' in second) != 0 then
			raise exception 'Too many comas, invalid account';
		end if;
		-- check that both account are in PCMN

		end if;
	else
		-- account is null
		update fiche_detail set ad_value=null where f_id=p_f_id and ad_id=5 ;
	end if;

	update fiche_detail set ad_value=p_account where f_id=p_f_id and ad_id=5 ;

return 0;
end;
$BODY$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION comptaproc.format_account(p_account account_type)
  RETURNS account_type AS
$BODY$

declare

sResult account_type;

begin
sResult := lower(p_account);

sResult := translate(sResult,'éèêëàâäïîüûùöôç','eeeeaaaiiuuuooc');
sResult := translate(sResult,' $€µ£%.+-/\!(){}(),;_&|"#''^<>*','');

return upper(sResult);

end;
$BODY$
LANGUAGE plpgsql;

COMMENT ON FUNCTION comptaproc.format_account(account_type) IS 'format the accounting :
- upper case
- remove space and special char.
';

CREATE OR REPLACE FUNCTION comptaproc.tmp_pcmn_alphanum_ins_upd()
  RETURNS trigger AS
$BODY$
declare
   r_record tmp_pcmn%ROWTYPE;
begin
r_record := NEW;
r_record.pcm_val:=format_account(NEW.pcm_val);

return r_record;
end;
$BODY$
LANGUAGE plpgsql;
CREATE OR REPLACE FUNCTION comptaproc.tmp_pcmn_ins()
  RETURNS trigger AS
$BODY$
declare
   r_record tmp_pcmn%ROWTYPE;
begin
r_record := NEW;
if  length(trim(r_record.pcm_type))=0 or r_record.pcm_type is NULL then
   r_record.pcm_type:=find_pcm_type(NEW.pcm_val);
   return r_record;
end if;
return NEW;
end;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER t_tmp_pcm_alphanum_ins_upd
  BEFORE INSERT OR UPDATE
  ON tmp_pcmn
  FOR EACH ROW
  EXECUTE PROCEDURE comptaproc.tmp_pcmn_alphanum_ins_upd();

DROP FUNCTION comptaproc.account_add(account_type, character varying);

CREATE OR REPLACE FUNCTION comptaproc.account_add(p_id account_type, p_name character varying)
  RETURNS text AS
$BODY$
declare
	nParent tmp_pcmn.pcm_val_parent%type;
	nCount integer;
	sReturn text;
begin
	sReturn:= format_account(p_id);
	select count(*) into nCount from tmp_pcmn where pcm_val=sReturn;
	if nCount = 0 then
		nParent=account_parent(p_id);
		insert into tmp_pcmn (pcm_val,pcm_lib,pcm_val_parent)
			values (p_id, p_name,nParent) returning pcm_val into sReturn;
	end if;
return sReturn;
end ;
$BODY$
  LANGUAGE plpgsql;

CREATE TABLE menu_ref (
    me_code text NOT NULL,
    me_menu text,
    me_file text,
    me_url text,
    me_description text,
    me_parameter text,
    me_javascript text,
    me_type character varying(2)
);
COMMENT ON COLUMN menu_ref.me_code IS 'Menu Code ';
COMMENT ON COLUMN menu_ref.me_menu IS 'Label to display';
COMMENT ON COLUMN menu_ref.me_file IS 'if not empty file to include';
COMMENT ON COLUMN menu_ref.me_url IS 'url ';
COMMENT ON COLUMN menu_ref.me_type IS 'ME for menu
PR for Printing
SP for special meaning (ex: return to line)
PL for plugin';

CREATE TABLE profile (
    p_name text NOT NULL,
    p_id integer NOT NULL,
    p_desc text,
    with_calc boolean DEFAULT true,
    with_direct_form boolean DEFAULT true
);

COMMENT ON TABLE profile IS 'Available profile ';
COMMENT ON COLUMN profile.p_name IS 'Name of the profile';
COMMENT ON COLUMN profile.p_desc IS 'description of the profile';
COMMENT ON COLUMN profile.with_calc IS 'show the calculator';
COMMENT ON COLUMN profile.with_direct_form IS 'show the direct form';

CREATE TABLE profile_menu (
    pm_id integer NOT NULL,
    me_code text,
    me_code_dep text,
    p_id integer,
    p_order integer,
    p_type_display text NOT NULL,
    pm_default integer
);
COMMENT ON TABLE profile_menu IS 'Join  between the profile and the menu ';
COMMENT ON COLUMN profile_menu.me_code_dep IS 'menu code dependency';
COMMENT ON COLUMN profile_menu.p_id IS 'link to profile';
COMMENT ON COLUMN profile_menu.p_order IS 'order of displaying menu';
COMMENT ON COLUMN profile_menu.pm_default IS 'default menu';
COMMENT ON COLUMN profile_menu.p_type_display IS 'M is a module
E is a menu
S is a select (for plugin)';


CREATE SEQUENCE profile_menu_pm_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
ALTER SEQUENCE profile_menu_pm_id_seq OWNED BY profile_menu.pm_id;
SELECT pg_catalog.setval('profile_menu_pm_id_seq', 778, true);

CREATE TABLE profile_menu_type (
    pm_type text NOT NULL,
    pm_desc text
);

CREATE SEQUENCE profile_p_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE profile_p_id_seq OWNED BY profile.p_id;

SELECT pg_catalog.setval('profile_p_id_seq', 11, true);

CREATE TABLE profile_user (
    user_name text NOT NULL,
    pu_id integer NOT NULL,
    p_id integer
);

COMMENT ON TABLE profile_user IS 'Contains the available profile for users';
COMMENT ON COLUMN profile_user.user_name IS 'fk to available_user : login';
COMMENT ON COLUMN profile_user.p_id IS 'fk to profile';

CREATE SEQUENCE profile_user_pu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
ALTER SEQUENCE profile_user_pu_id_seq OWNED BY profile_user.pu_id;
SELECT pg_catalog.setval('profile_user_pu_id_seq', 6, true);
CREATE VIEW v_all_menu AS
    SELECT pm.me_code, pm.pm_id, pm.me_code_dep, pm.p_order, pm.p_type_display, pu.user_name, pu.pu_id, p.p_name, p.p_desc, mr.me_menu, mr.me_file, mr.me_url, mr.me_parameter, mr.me_javascript, mr.me_type, pm.p_id, mr.me_description FROM (((profile_menu pm JOIN profile_user pu ON ((pu.p_id = pm.p_id))) JOIN profile p ON ((p.p_id = pm.p_id))) JOIN menu_ref mr USING (me_code)) ORDER BY pm.p_order;
ALTER TABLE profile ALTER COLUMN p_id SET DEFAULT nextval('profile_p_id_seq'::regclass);
ALTER TABLE profile_menu ALTER COLUMN pm_id SET DEFAULT nextval('profile_menu_pm_id_seq'::regclass);
ALTER TABLE profile_user ALTER COLUMN pu_id SET DEFAULT nextval('profile_user_pu_id_seq'::regclass);
INSERT INTO menu_ref VALUES ('ACH', 'Achat', 'compta_ach.inc.php', NULL, 'Nouvel achat ou dépense', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCHOP', 'Historique', 'anc_history.inc.php', NULL, 'Historique des imputations analytiques', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCGL', 'Grand''Livre', 'anc_great_ledger.inc.php', NULL, 'Grand livre d''plan analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCBS', 'Balance simple', 'anc_balance_simple.inc.php', NULL, 'Balance simple des imputations analytiques', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCBC2', 'Balance croisée double', 'anc_balance_double.inc.php', NULL, 'Balance double croisées des imputations analytiques', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCTAB', 'Tableau', 'anc_acc_table.inc.php', NULL, 'Tableau lié à la comptabilité', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCBCC', 'Balance Analytique/comptabilité', 'anc_acc_balance.inc.php', NULL, 'Lien entre comptabilité et Comptabilité analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCGR', 'Groupe', 'anc_group_balance.inc.php', NULL, 'Balance par groupe', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CSV:AncGrandLivre', 'Impression Grand-Livre', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncBalGroup', 'Export Balance groupe analytique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('OTH:Bilan', 'Export Bilan', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:ledger', 'Export Journaux', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:postedetail', 'Export Poste détail', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:postedetail', 'Export Poste détail', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:fichedetail', 'Export Fiche détail', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('SEARCH', 'Recherche', NULL, NULL, 'Recherche', NULL, 'popup_recherche()', 'ME');
INSERT INTO menu_ref VALUES ('DIVPARM', 'Divers', NULL, NULL, 'Paramètres divers', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGTVA', 'TVA', 'tva.inc.php', NULL, 'Config. de la tva', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CARD', 'Fiche', 'fiche.inc.php', NULL, 'Fiche', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('STOCK', 'Stock', 'stock.inc.php', NULL, 'Stock', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('MOD', 'Menu et profil', NULL, NULL, 'Menu ', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGPRO', 'Profil', 'profile.inc.php', NULL, 'Configuration profil', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGPAY', 'Moyen de paiement', 'payment_middle.inc.php', NULL, 'Config. des méthodes de paiement', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGACC', 'Poste', 'poste.inc.php', NULL, 'Config. poste comptable de base', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('VEN', 'Vente', 'compta_ven.inc.php', NULL, 'Nouvelle vente ou recette', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGMENU', 'Config. Menu', 'menu.inc.php', NULL, 'Configuration des menus et plugins', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('COMPANY', 'Sociétés', 'company.inc.php', NULL, 'Parametre societe', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PERIODE', 'Période', 'periode.inc.php', NULL, 'Gestion des périodes', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PDF:fichedetail', 'Export Fiche détail', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:fiche_balance', 'Export Fiche balance', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:fiche_balance', 'Export Fiche balance', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:report', 'Export report', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:report', 'Export report', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:fiche', 'Export Fiche', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:fiche', 'Export Fiche', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:glcompte', 'Export Grand Livre', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:glcompte', 'Export Grand Livre', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:sec', 'Export Sécurité', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncList', 'Export Comptabilité analytique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncBalSimple', 'Export Comptabilité analytique balance simple', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:AncBalSimple', 'Export Comptabilité analytique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncBalDouble', 'Export Comptabilité analytique balance double', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:AncBalDouble', 'Export Comptabilité analytique balance double', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:balance', 'Export Balance comptable', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('PDF:balance', 'Export Balance comptable', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:histo', 'Export Historique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:ledger', 'Export Journaux', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncTable', 'Export Tableau Analytique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('CSV:AncAccList', 'Export Historique Compt. Analytique', NULL, NULL, NULL, NULL, NULL, 'PR');
INSERT INTO menu_ref VALUES ('SUPPL', 'Fournisseur', 'supplier.inc.php', NULL, 'Suivi fournisseur', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('LET', 'Lettrage', NULL, NULL, 'Lettrage', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCODS', 'Opérations diverses', 'anc_od.inc.php', NULL, 'OD analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('VERIFBIL', 'Vérification ', 'verif_bilan.inc.php', NULL, 'Vérification de la comptabilité', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('REPORT', 'Création de rapport', 'report.inc.php', NULL, 'Création de rapport', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('OPEN', 'Ecriture Ouverture', 'opening.inc.php', NULL, 'Ecriture d''ouverture', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ACHIMP', 'Historique achat', 'history_operation.inc.php', NULL, 'Historique achat', 'ledger_type=ACH', NULL, 'ME');
INSERT INTO menu_ref VALUES ('FOLLOW', 'Courrier', 'action.inc.php', NULL, 'Suivi, courrier, devis', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('FORECAST', 'Prévision', 'forecast.inc.php', NULL, 'Prévision', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('EXT', 'Extension', 'extension_choice.inc.php', NULL, 'Extensions (plugins)', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGDOC', 'Document', 'document_modele.inc.php', NULL, 'Config. modèle de document', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGLED', 'journaux', 'cfgledger.inc.php', NULL, 'Configuration des journaux', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PREDOP', 'Ecriture prédefinie', 'preod.inc.php', NULL, 'Gestion des opérations prédéfinifies', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ADV', 'Avancé', NULL, NULL, 'Menu avancé', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANC', 'Compta Analytique', NULL, NULL, 'Module comptabilité analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGSEC', 'Sécurité', 'param_sec.inc.php', NULL, 'configuration de la sécurité', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PLANANC', 'Plan Compt. analytique', 'anc_pa.inc.php', NULL, 'Plan analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCGROUP', 'Groupe', 'anc_group.inc.php', NULL, 'Groupe analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ODSIMP', 'Historique opérations diverses', 'history_operation.inc.php', NULL, 'Historique opérations diverses', 'ledger_type=ODS', NULL, 'ME');
INSERT INTO menu_ref VALUES ('VENMENU', 'Vente / Recette', NULL, NULL, 'Menu ventes et recettes', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PREFERENCE', 'Préférence', 'pref.inc.php', NULL, 'Préférence', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('HIST', 'Historique', 'history_operation.inc.php', NULL, 'Historique', 'ledger_type=ALL', NULL, 'ME');
INSERT INTO menu_ref VALUES ('MENUFIN', 'Financier', NULL, NULL, 'Menu Financier', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('FIMP', 'Historique financier', 'history_operation.inc.php', NULL, 'Historique financier', 'ledger_type=FIN', NULL, 'ME');
INSERT INTO menu_ref VALUES ('MENUACH', 'Achat', NULL, NULL, 'Menu achat', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('MENUODS', 'Opérations diverses', NULL, NULL, 'Menu opérations diverses', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ODS', 'Opérations Diverses', 'compta_ods.inc.php', NULL, 'Nouvelle opérations diverses', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('FREC', 'Rapprochement', 'compta_fin_rec.inc.php', NULL, 'Rapprochement bancaire', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ADM', 'Administration', 'adm.inc.php', NULL, 'Suivi administration, banque', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('FIN', 'Nouvel extrait', 'compta_fin.inc.php', NULL, 'Nouvel extrait bancaire', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGATCARD', 'Attribut de fiche', 'card_attr.inc.php', NULL, 'Gestion des modèles de fiches', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('FSALDO', 'Soldes', 'compta_fin_saldo.inc.php', NULL, 'Solde des comptes en banques, caisse...', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('JSSEARCH', 'Recherche', NULL, NULL, 'Recherche', NULL, 'search_reconcile()', 'ME');
INSERT INTO menu_ref VALUES ('LETACC', 'Lettrage par Poste', 'lettering.account.inc.php', NULL, 'lettrage par poste comptable', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CARDBAL', 'Balance', 'balance_card.inc.php', NULL, 'Balance par catégorie de fiche', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CUST', 'Client', 'client.inc.php', NULL, 'Suivi client', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGCARDCAT', 'Catégorie de fiche', 'fiche_def.inc.php', NULL, 'Gestion catégorie de fiche', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGCATDOC', 'Catégorie de documents', 'cat_document.inc.php', NULL, 'Config. catégorie de documents', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('VENIMP', 'Historique vente', 'history_operation.inc.php', NULL, 'Historique des ventes', 'ledger_type=VEN', NULL, 'ME');
INSERT INTO menu_ref VALUES ('LETCARD', 'Lettrage par Fiche', 'lettering.card.inc.php', NULL, 'Lettrage par fiche', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('CFGPCMN', 'Plan Comptable', 'param_pcmn.inc.php', NULL, 'Config. du plan comptable', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('LOGOUT', 'Sortie', NULL, 'logout.php', 'Sortie', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('DASHBOARD', 'Tableau de bord', 'dashboard.inc.php', NULL, 'Tableau de bord', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('COMPTA', 'Comptabilité', NULL, NULL, 'Module comptabilité', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('GESTION', 'Gestion', NULL, NULL, 'Module gestion', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PARAM', 'Paramètre', NULL, NULL, 'Module paramètre', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTJRN', 'Historique', 'impress_jrn.inc.php', NULL, 'Impression historique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTREC', 'Rapprochement', 'impress_rec.inc.php', NULL, 'Impression des rapprochements', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTPOSTE', 'Poste', 'impress_poste.inc.php', NULL, 'Impression du détail d''un poste comptable', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTREPORT', 'Rapport', 'impress_rapport.inc.php', NULL, 'Impression de rapport', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTBILAN', 'Bilan', 'impress_bilan.inc.php', NULL, 'Impression de bilan', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTGL', 'Grand Livre', 'impress_gl_comptes.inc.php', NULL, 'Impression du grand livre', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTBAL', 'Balance', 'balance.inc.php', NULL, 'Impression des balances comptables', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINTCARD', 'Catégorie de Fiches', 'impress_fiche.inc.php', NULL, 'Impression catégorie de fiches', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('PRINT', 'Impression', NULL, NULL, 'Menu impression', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ACCESS', 'Accueil', NULL, 'user_login.php', 'Accueil', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('ANCIMP', 'Impression', NULL, NULL, 'Impression compta. analytique', NULL, NULL, 'ME');
INSERT INTO menu_ref VALUES ('new_line', 'saut de ligne', NULL, NULL, 'Saut de ligne', NULL, NULL, 'SP');

INSERT INTO profile VALUES ('Administrateur', 1, 'Profil par défaut pour les adminstrateurs', true, true);
INSERT INTO profile VALUES ('Utilisateur', 2, 'Profil par défaut pour les utilisateurs', true, true);
INSERT INTO profile_menu VALUES (59, 'CFGPAY', 'DIVPARM', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (68, 'CFGATCARD', 'DIVPARM', 1, 9, 'E', 0);
INSERT INTO profile_menu VALUES (61, 'CFGACC', 'DIVPARM', 1, 6, 'E', 0);
INSERT INTO profile_menu VALUES (54, 'COMPANY', 'PARAM', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (651, 'ANCHOP', 'ANCIMP', 1, 10, 'E', 0);
INSERT INTO profile_menu VALUES (173, 'COMPTA', NULL, 1, 40, 'M', 0);
INSERT INTO profile_menu VALUES (55, 'PERIODE', 'PARAM', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (56, 'DIVPARM', 'PARAM', 1, 3, 'E', 0);
INSERT INTO profile_menu VALUES (652, 'ANCGL', 'ANCIMP', 1, 20, 'E', 0);
INSERT INTO profile_menu VALUES (60, 'CFGTVA', 'DIVPARM', 1, 5, 'E', 0);
INSERT INTO profile_menu VALUES (653, 'ANCBS', 'ANCIMP', 1, 30, 'E', 0);
INSERT INTO profile_menu VALUES (654, 'ANCBC2', 'ANCIMP', 1, 40, 'E', 0);
INSERT INTO profile_menu VALUES (655, 'ANCTAB', 'ANCIMP', 1, 50, 'E', 0);
INSERT INTO profile_menu VALUES (656, 'ANCBCC', 'ANCIMP', 1, 60, 'E', 0);
INSERT INTO profile_menu VALUES (657, 'ANCGR', 'ANCIMP', 1, 70, 'E', 0);
INSERT INTO profile_menu VALUES (658, 'CSV:AncGrandLivre', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (662, 'new_line', NULL, 1, 35, 'M', 0);
INSERT INTO profile_menu VALUES (67, 'CFGCATDOC', 'DIVPARM', 1, 8, 'E', 0);
INSERT INTO profile_menu VALUES (69, 'CFGPCMN', 'PARAM', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (526, 'PRINTGL', 'PRINT', 1, 20, 'E', 0);
INSERT INTO profile_menu VALUES (23, 'LET', 'COMPTA', 1, 8, 'E', 0);
INSERT INTO profile_menu VALUES (523, 'PRINTBAL', 'PRINT', 1, 50, 'E', 0);
INSERT INTO profile_menu VALUES (529, 'PRINTREPORT', 'PRINT', 1, 85, 'E', 0);
INSERT INTO profile_menu VALUES (72, 'PREDOP', 'PARAM', 1, 7, 'E', 0);
INSERT INTO profile_menu VALUES (75, 'PLANANC', 'ANC', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (65, 'CFGCARDCAT', 'DIVPARM', 1, 7, 'E', 0);
INSERT INTO profile_menu VALUES (76, 'ANCODS', 'ANC', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (77, 'ANCGROUP', 'ANC', 1, 3, 'E', 0);
INSERT INTO profile_menu VALUES (78, 'ANCIMP', 'ANC', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (45, 'PARAM', NULL, 1, 20, 'M', 0);
INSERT INTO profile_menu VALUES (527, 'PRINTJRN', 'PRINT', 1, 10, 'E', 0);
INSERT INTO profile_menu VALUES (530, 'PRINTREC', 'PRINT', 1, 100, 'E', 0);
INSERT INTO profile_menu VALUES (524, 'PRINTBILAN', 'PRINT', 1, 90, 'E', 0);
INSERT INTO profile_menu VALUES (79, 'PREFERENCE', NULL, 1, 15, 'M', 0);
INSERT INTO profile_menu VALUES (37, 'CUST', 'GESTION', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (38, 'SUPPL', 'GESTION', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (39, 'ADM', 'GESTION', 1, 3, 'E', 0);
INSERT INTO profile_menu VALUES (36, 'CARD', 'GESTION', 1, 6, 'E', 0);
INSERT INTO profile_menu VALUES (40, 'STOCK', 'GESTION', 1, 5, 'E', 0);
INSERT INTO profile_menu VALUES (41, 'FORECAST', 'GESTION', 1, 7, 'E', 0);
INSERT INTO profile_menu VALUES (42, 'FOLLOW', 'GESTION', 1, 8, 'E', 0);
INSERT INTO profile_menu VALUES (29, 'VERIFBIL', 'ADV', 1, 21, 'E', 0);
INSERT INTO profile_menu VALUES (30, 'STOCK', 'ADV', 1, 22, 'E', 0);
INSERT INTO profile_menu VALUES (31, 'PREDOP', 'ADV', 1, 23, 'E', 0);
INSERT INTO profile_menu VALUES (32, 'OPEN', 'ADV', 1, 24, 'E', 0);
INSERT INTO profile_menu VALUES (33, 'REPORT', 'ADV', 1, 25, 'E', 0);
INSERT INTO profile_menu VALUES (5, 'CARD', 'COMPTA', 1, 7, 'E', 0);
INSERT INTO profile_menu VALUES (43, 'HIST', 'COMPTA', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (28, 'ADV', 'COMPTA', 1, 20, 'E', 0);
INSERT INTO profile_menu VALUES (53, 'ACCESS', NULL, 1, 25, 'M', 0);
INSERT INTO profile_menu VALUES (123, 'CSV:histo', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (20, 'LOGOUT', NULL, 1, 30, 'M', 0);
INSERT INTO profile_menu VALUES (35, 'PRINT', 'GESTION', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (124, 'CSV:ledger', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (125, 'PDF:ledger', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (6, 'PRINT', 'COMPTA', 1, 6, 'E', 0);
INSERT INTO profile_menu VALUES (126, 'CSV:postedetail', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (3, 'MENUACH', 'COMPTA', 1, 3, 'E', 0);
INSERT INTO profile_menu VALUES (86, 'ACHIMP', 'MENUACH', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (34, 'GESTION', NULL, 1, 45, 'M', 0);
INSERT INTO profile_menu VALUES (18, 'MENUODS', 'COMPTA', 1, 5, 'E', 0);
INSERT INTO profile_menu VALUES (88, 'ODS', 'MENUODS', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (89, 'ODSIMP', 'MENUODS', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (2, 'ANC', NULL, 1, 50, 'M', 0);
INSERT INTO profile_menu VALUES (4, 'VENMENU', 'COMPTA', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (90, 'VEN', 'VENMENU', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (91, 'VENIMP', 'VENMENU', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (19, 'FIN', 'MENUFIN', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (73, 'CFGDOC', 'PARAM', 1, 8, 'E', 0);
INSERT INTO profile_menu VALUES (74, 'CFGLED', 'PARAM', 1, 9, 'E', 0);
INSERT INTO profile_menu VALUES (71, 'CFGSEC', 'PARAM', 1, 6, 'E', 0);
INSERT INTO profile_menu VALUES (82, 'EXT', NULL, 1, 55, 'M', 0);
INSERT INTO profile_menu VALUES (95, 'FREC', 'MENUFIN', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (94, 'FSALDO', 'MENUFIN', 1, 3, 'E', 0);
INSERT INTO profile_menu VALUES (27, 'LETACC', 'LET', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (24, 'LETCARD', 'LET', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (167, 'MOD', 'PARAM', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (92, 'MENUFIN', 'COMPTA', 1, 4, 'E', 0);
INSERT INTO profile_menu VALUES (93, 'FIMP', 'MENUFIN', 1, 2, 'E', 0);
INSERT INTO profile_menu VALUES (151, 'SEARCH', NULL, 1, 60, 'M', 0);
INSERT INTO profile_menu VALUES (85, 'ACH', 'MENUACH', 1, 1, 'E', 0);
INSERT INTO profile_menu VALUES (127, 'PDF:postedetail', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (128, 'CSV:fichedetail', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (129, 'PDF:fichedetail', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (130, 'CSV:fiche_balance', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (131, 'PDF:fiche_balance', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (132, 'CSV:report', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (133, 'PDF:report', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (134, 'CSV:fiche', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (135, 'PDF:fiche', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (136, 'CSV:glcompte', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (137, 'PDF:glcompte', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (138, 'PDF:sec', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (139, 'CSV:AncList', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (140, 'CSV:AncBalSimple', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (141, 'PDF:AncBalSimple', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (142, 'CSV:AncBalDouble', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (143, 'PDF:AncBalDouble', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (144, 'CSV:balance', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (145, 'PDF:balance', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (146, 'CSV:AncTable', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (147, 'CSV:AncAccList', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (148, 'CSV:AncBalGroup', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (149, 'OTH:Bilan', NULL, 1, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (528, 'PRINTPOSTE', 'PRINT', 1, 30, 'E', 0);
INSERT INTO profile_menu VALUES (525, 'PRINTCARD', 'PRINT', 1, 40, 'E', 0);
INSERT INTO profile_menu VALUES (1, 'DASHBOARD', NULL, 1, 10, 'M', 1);
INSERT INTO profile_menu VALUES (172, 'CFGPRO', 'MOD', 1, NULL, 'E', 0);
INSERT INTO profile_menu VALUES (171, 'CFGMENU', 'MOD', 1, NULL, 'E', 0);
INSERT INTO profile_menu VALUES (663, 'CFGPAY', 'DIVPARM', 2, 4, 'E', 0);
INSERT INTO profile_menu VALUES (664, 'CFGATCARD', 'DIVPARM', 2, 9, 'E', 0);
INSERT INTO profile_menu VALUES (665, 'CFGACC', 'DIVPARM', 2, 6, 'E', 0);
INSERT INTO profile_menu VALUES (668, 'ANCHOP', 'ANCIMP', 2, 10, 'E', 0);
INSERT INTO profile_menu VALUES (669, 'COMPTA', NULL, 2, 40, 'M', 0);
INSERT INTO profile_menu VALUES (672, 'ANCGL', 'ANCIMP', 2, 20, 'E', 0);
INSERT INTO profile_menu VALUES (673, 'CFGTVA', 'DIVPARM', 2, 5, 'E', 0);
INSERT INTO profile_menu VALUES (674, 'ANCBS', 'ANCIMP', 2, 30, 'E', 0);
INSERT INTO profile_menu VALUES (675, 'ANCBC2', 'ANCIMP', 2, 40, 'E', 0);
INSERT INTO profile_menu VALUES (676, 'ANCTAB', 'ANCIMP', 2, 50, 'E', 0);
INSERT INTO profile_menu VALUES (677, 'ANCBCC', 'ANCIMP', 2, 60, 'E', 0);
INSERT INTO profile_menu VALUES (678, 'ANCGR', 'ANCIMP', 2, 70, 'E', 0);
INSERT INTO profile_menu VALUES (679, 'CSV:AncGrandLivre', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (680, 'new_line', NULL, 2, 35, 'M', 0);
INSERT INTO profile_menu VALUES (681, 'CFGCATDOC', 'DIVPARM', 2, 8, 'E', 0);
INSERT INTO profile_menu VALUES (683, 'PRINTGL', 'PRINT', 2, 20, 'E', 0);
INSERT INTO profile_menu VALUES (684, 'LET', 'COMPTA', 2, 8, 'E', 0);
INSERT INTO profile_menu VALUES (685, 'PRINTBAL', 'PRINT', 2, 50, 'E', 0);
INSERT INTO profile_menu VALUES (686, 'PRINTREPORT', 'PRINT', 2, 85, 'E', 0);
INSERT INTO profile_menu VALUES (688, 'PLANANC', 'ANC', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (689, 'CFGCARDCAT', 'DIVPARM', 2, 7, 'E', 0);
INSERT INTO profile_menu VALUES (690, 'ANCODS', 'ANC', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (717, 'CSV:ledger', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (718, 'PDF:ledger', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (719, 'PRINT', 'COMPTA', 2, 6, 'E', 0);
INSERT INTO profile_menu VALUES (720, 'CSV:postedetail', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (721, 'MENUACH', 'COMPTA', 2, 3, 'E', 0);
INSERT INTO profile_menu VALUES (722, 'ACHIMP', 'MENUACH', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (723, 'GESTION', NULL, 2, 45, 'M', 0);
INSERT INTO profile_menu VALUES (724, 'MENUODS', 'COMPTA', 2, 5, 'E', 0);
INSERT INTO profile_menu VALUES (725, 'ODS', 'MENUODS', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (726, 'ODSIMP', 'MENUODS', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (727, 'ANC', NULL, 2, 50, 'M', 0);
INSERT INTO profile_menu VALUES (728, 'VENMENU', 'COMPTA', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (729, 'VEN', 'VENMENU', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (730, 'VENIMP', 'VENMENU', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (731, 'FIN', 'MENUFIN', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (735, 'EXT', NULL, 2, 55, 'M', 0);
INSERT INTO profile_menu VALUES (736, 'FREC', 'MENUFIN', 2, 4, 'E', 0);
INSERT INTO profile_menu VALUES (737, 'FSALDO', 'MENUFIN', 2, 3, 'E', 0);
INSERT INTO profile_menu VALUES (738, 'LETACC', 'LET', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (691, 'ANCGROUP', 'ANC', 2, 3, 'E', 0);
INSERT INTO profile_menu VALUES (692, 'ANCIMP', 'ANC', 2, 4, 'E', 0);
INSERT INTO profile_menu VALUES (694, 'PRINTJRN', 'PRINT', 2, 10, 'E', 0);
INSERT INTO profile_menu VALUES (695, 'PRINTREC', 'PRINT', 2, 100, 'E', 0);
INSERT INTO profile_menu VALUES (696, 'PRINTBILAN', 'PRINT', 2, 90, 'E', 0);
INSERT INTO profile_menu VALUES (697, 'PREFERENCE', NULL, 2, 15, 'M', 0);
INSERT INTO profile_menu VALUES (698, 'CUST', 'GESTION', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (699, 'SUPPL', 'GESTION', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (700, 'ADM', 'GESTION', 2, 3, 'E', 0);
INSERT INTO profile_menu VALUES (701, 'CARD', 'GESTION', 2, 6, 'E', 0);
INSERT INTO profile_menu VALUES (702, 'STOCK', 'GESTION', 2, 5, 'E', 0);
INSERT INTO profile_menu VALUES (703, 'FORECAST', 'GESTION', 2, 7, 'E', 0);
INSERT INTO profile_menu VALUES (704, 'FOLLOW', 'GESTION', 2, 8, 'E', 0);
INSERT INTO profile_menu VALUES (705, 'VERIFBIL', 'ADV', 2, 21, 'E', 0);
INSERT INTO profile_menu VALUES (706, 'STOCK', 'ADV', 2, 22, 'E', 0);
INSERT INTO profile_menu VALUES (707, 'PREDOP', 'ADV', 2, 23, 'E', 0);
INSERT INTO profile_menu VALUES (708, 'OPEN', 'ADV', 2, 24, 'E', 0);
INSERT INTO profile_menu VALUES (709, 'REPORT', 'ADV', 2, 25, 'E', 0);
INSERT INTO profile_menu VALUES (710, 'CARD', 'COMPTA', 2, 7, 'E', 0);
INSERT INTO profile_menu VALUES (711, 'HIST', 'COMPTA', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (712, 'ADV', 'COMPTA', 2, 20, 'E', 0);
INSERT INTO profile_menu VALUES (713, 'ACCESS', NULL, 2, 25, 'M', 0);
INSERT INTO profile_menu VALUES (714, 'CSV:histo', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (715, 'LOGOUT', NULL, 2, 30, 'M', 0);
INSERT INTO profile_menu VALUES (716, 'PRINT', 'GESTION', 2, 4, 'E', 0);
INSERT INTO profile_menu VALUES (739, 'LETCARD', 'LET', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (742, 'MENUFIN', 'COMPTA', 2, 4, 'E', 0);
INSERT INTO profile_menu VALUES (743, 'FIMP', 'MENUFIN', 2, 2, 'E', 0);
INSERT INTO profile_menu VALUES (744, 'SEARCH', NULL, 2, 60, 'M', 0);
INSERT INTO profile_menu VALUES (745, 'ACH', 'MENUACH', 2, 1, 'E', 0);
INSERT INTO profile_menu VALUES (746, 'PDF:postedetail', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (747, 'CSV:fichedetail', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (748, 'PDF:fichedetail', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (749, 'CSV:fiche_balance', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (750, 'PDF:fiche_balance', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (751, 'CSV:report', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (752, 'PDF:report', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (753, 'CSV:fiche', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (754, 'PDF:fiche', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (755, 'CSV:glcompte', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (756, 'PDF:glcompte', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (757, 'PDF:sec', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (758, 'CSV:AncList', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (759, 'CSV:AncBalSimple', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (760, 'PDF:AncBalSimple', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (761, 'CSV:AncBalDouble', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (762, 'PDF:AncBalDouble', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (763, 'CSV:balance', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (764, 'PDF:balance', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (765, 'CSV:AncTable', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (766, 'CSV:AncAccList', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (767, 'CSV:AncBalGroup', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (768, 'OTH:Bilan', NULL, 2, NULL, 'P', 0);
INSERT INTO profile_menu VALUES (769, 'PRINTPOSTE', 'PRINT', 2, 30, 'E', 0);
INSERT INTO profile_menu VALUES (770, 'PRINTCARD', 'PRINT', 2, 40, 'E', 0);
INSERT INTO profile_menu VALUES (777, 'CFGPRO', 'MOD', 2, NULL, 'E', 0);
INSERT INTO profile_menu VALUES (778, 'CFGMENU', 'MOD', 2, NULL, 'E', 0);
INSERT INTO profile_menu VALUES (772, 'DASHBOARD', NULL, 2, 10, 'M', 1);
INSERT INTO profile_menu_type VALUES ('P', 'Impression');
INSERT INTO profile_menu_type VALUES ('S', 'Extension');
INSERT INTO profile_menu_type VALUES ('E', 'Menu');
INSERT INTO profile_menu_type VALUES ('M', 'Module');
INSERT INTO profile_user VALUES ('phpcompta', 1, 1);
ALTER TABLE ONLY menu_ref    ADD CONSTRAINT menu_ref_pkey PRIMARY KEY (me_code);
ALTER TABLE ONLY profile_menu    ADD CONSTRAINT profile_menu_pkey PRIMARY KEY (pm_id);
ALTER TABLE ONLY profile_menu_type    ADD CONSTRAINT profile_menu_type_pkey PRIMARY KEY (pm_type);
ALTER TABLE ONLY profile    ADD CONSTRAINT profile_pkey PRIMARY KEY (p_id);
ALTER TABLE ONLY profile_user    ADD CONSTRAINT profile_user_pkey PRIMARY KEY (pu_id);
ALTER TABLE ONLY profile_user    ADD CONSTRAINT profile_user_user_name_key UNIQUE (user_name, p_id);
CREATE INDEX fki_profile_menu_me_code ON profile_menu USING btree (me_code);
CREATE INDEX fki_profile_menu_profile ON profile_menu USING btree (p_id);
CREATE INDEX fki_profile_menu_type_fkey ON profile_menu USING btree (p_type_display);
ALTER TABLE ONLY profile_menu    ADD CONSTRAINT profile_menu_me_code_fkey FOREIGN KEY (me_code) REFERENCES menu_ref(me_code) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY profile_menu    ADD CONSTRAINT profile_menu_p_id_fkey FOREIGN KEY (p_id) REFERENCES profile(p_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY profile_menu    ADD CONSTRAINT profile_menu_type_fkey FOREIGN KEY (p_type_display) REFERENCES profile_menu_type(pm_type);
ALTER TABLE ONLY profile_user    ADD CONSTRAINT profile_user_p_id_fkey FOREIGN KEY (p_id) REFERENCES profile(p_id) ON UPDATE CASCADE ON DELETE CASCADE;
create type menu_tree as (code text,description text);

create or replace function comptaproc.get_profile_menu(login text)
returns setof  menu_tree
as
$BODY$
declare
	a menu_tree;
	e menu_tree;
begin
for a in select me_code,me_description from v_all_menu where user_name=login
	and me_code_dep is null and me_type <> 'PR' and me_type <>'SP'
loop
		return next a;

		for e in select * from get_menu_tree(a.code,login)
		loop
			return next e;
		end loop;

	end loop;
return;
end;
$BODY$ language plpgsql;




CREATE OR REPLACE FUNCTION comptaproc.get_menu_tree(p_code text,login text)
  RETURNS SETOF menu_tree AS
$BODY$
declare
	i menu_tree;
	e menu_tree;
	a text;
	x v_all_menu%ROWTYPE;
begin
	for x in select *  from v_all_menu where me_code_dep=p_code::text and user_name=login::text
	loop
		if x.me_code_dep is not null then
			i.code := x.me_code_dep||'/'||x.me_code;
		else
			i.code := x.me_code;
		end if;

		i.description := x.me_description;

		return next i;

	for e in select *  from get_menu_tree(x.me_code,login)
		loop
			e.code:=x.me_code_dep||'/'||e.code;
			return next e;
		end loop;

	end loop;
	return;
end;
$BODY$
LANGUAGE plpgsql;

alter table mod_payment add jrn_def_id bigint;
update mod_payment set jrn_def_id=2 where mp_type='VEN';
update mod_payment set jrn_def_id=3 where mp_type='ACH';

alter table mod_payment drop mp_type;

delete from mod_payment where jrn_def_id not in (select jrn_def_id from jrn_def);

alter table mod_payment add constraint mod_payment_jrn_def_id_fk foreign key (jrn_def_id) references jrn_def(jrn_def_id) on delete cascade on update cascade;

comment on column mod_payment.jrn_def_id is 'Ledger using this payment method';
alter table tva_rate add tva_both_side integer ;
alter table tva_rate alter tva_both_side set default 0;
update tva_rate set tva_both_side=0;

drop FUNCTION comptaproc.tva_modify(integer, text, numeric, text, text);
alter table quant_purchase add qp_vat_sided numeric (20,4);
alter table quant_sold add qs_vat_sided numeric (20,4);

alter table quant_purchase alter qp_vat_sided set default 0.0;
alter table quant_sold alter qs_vat_sided set default 0.0;

update quant_purchase set qp_vat_sided=0.0;
update quant_sold set qs_vat_sided=0.0;

comment on column quant_purchase.qp_vat_sided is 'amount of the VAT which avoid VAT, case of the VAT which add the same amount at the deb and cred';
comment on column quant_purchase.qp_vat_sided is 'amount of the VAT which avoid VAT, case of the VAT which add the same amount at the deb and cred';

CREATE OR REPLACE FUNCTION comptaproc.tva_modify(integer, text, numeric, text, text,integer)
 RETURNS integer
AS $function$
declare
	p_tva_id alias for $1;
	p_tva_label alias for $2;
	p_tva_rate alias for $3;
	p_tva_comment alias for $4;
	p_tva_poste alias for $5;
	p_tva_both_side alias for $6;
	debit text;
	credit text;
	nCount integer;
begin
if length(trim(p_tva_label)) = 0 then
	return 3;
end if;

if length(trim(p_tva_poste)) != 0 then
	if position (',' in p_tva_poste) = 0 then return 4; end if;
	debit  = split_part(p_tva_poste,',',1);
	credit	= split_part(p_tva_poste,',',2);
	select count(*) into nCount from tmp_pcmn where pcm_val=debit::account_type;
	if nCount = 0 then return 4; end if;
	select count(*) into nCount from tmp_pcmn where pcm_val=credit::account_type;
	if nCount = 0 then return 4; end if;

end if;
update tva_rate set tva_label=p_tva_label,tva_rate=p_tva_rate,tva_comment=p_tva_comment,tva_poste=p_tva_poste,tva_both_side=p_tva_both_side
	where tva_id=p_tva_id;
return 0;
end;
$function$
LANGUAGE plpgsql;

drop FUNCTION comptaproc.tva_insert(text, numeric, text, text);

CREATE OR REPLACE FUNCTION comptaproc.tva_insert(text, numeric, text, text,integer)
 RETURNS integer
AS $function$
declare
	l_tva_id integer;
	p_tva_label alias for $1;
	p_tva_rate alias for $2;
	p_tva_comment alias for $3;
	p_tva_poste alias for $4;
	p_tva_both_side alias for $5;
	debit text;
	credit text;
	nCount integer;
begin
if length(trim(p_tva_label)) = 0 then
	return 3;
end if;

if length(trim(p_tva_poste)) != 0 then
	if position (',' in p_tva_poste) = 0 then return 4; end if;
	debit  = split_part(p_tva_poste,',',1);
	credit	= split_part(p_tva_poste,',',2);
	select count(*) into nCount from tmp_pcmn where pcm_val=debit::account_type;
	if nCount = 0 then return 4; end if;
	select count(*) into nCount from tmp_pcmn where pcm_val=credit::account_type;
	if nCount = 0 then return 4; end if;

end if;
select into l_tva_id nextval('s_tva') ;
insert into tva_rate(tva_id,tva_label,tva_rate,tva_comment,tva_poste,tva_both_side)
	values (l_tva_id,p_tva_label,p_tva_rate,p_tva_comment,p_tva_poste,p_tva_both_side);
return 0;
end;
$function$
LANGUAGE plpgsql;

DROP FUNCTION comptaproc.insert_quant_purchase(text,numeric, character varying,numeric,numeric,numeric,integer,numeric,numeric,numeric,numeric,character varying);

CREATE OR REPLACE FUNCTION comptaproc.insert_quant_purchase(p_internal text, p_j_id numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_nd_amount numeric, p_nd_tva numeric, p_nd_tva_recup numeric, p_dep_priv numeric, p_client character varying,p_tva_sided numeric)
 RETURNS void
AS $function$
declare
        fid_client integer;
        fid_good   integer;
begin
        select f_id into fid_client from
                fiche_detail where ad_id=23 and ad_value=upper(trim(p_client));
        select f_id into fid_good from
                 fiche_detail where ad_id=23 and ad_value=upper(trim(p_fiche));
        insert into quant_purchase
                (qp_internal,
                j_id,
                qp_fiche,
                qp_quantite,
                qp_price,
                qp_vat,
                qp_vat_code,
                qp_nd_amount,
                qp_nd_tva,
                qp_nd_tva_recup,
                qp_supplier,
                qp_dep_priv,
                qp_vat_sided)
        values
                (p_internal,
                p_j_id,
                fid_good,
                p_quant,
                p_price,
                p_vat,
                p_vat_code,
                p_nd_amount,
                p_nd_tva,
                p_nd_tva_recup,
                fid_client,
                p_dep_priv,
                p_tva_sided);
        return;
end;
 $function$
 LANGUAGE plpgsql;

DROP FUNCTION comptaproc.insert_quant_sold(text, numeric, character varying, numeric, numeric, numeric, integer, character varying);
CREATE OR REPLACE FUNCTION comptaproc.insert_quant_sold(p_internal text, p_jid numeric, p_fiche character varying, p_quant numeric, p_price numeric, p_vat numeric, p_vat_code integer, p_client character varying,p_tva_sided numeric)
 RETURNS void
AS $function$
declare
        fid_client integer;
        fid_good   integer;
begin

        select f_id into fid_client from
                fiche_detail where ad_id=23 and ad_value=upper(trim(p_client));
        select f_id into fid_good from
                fiche_detail where ad_id=23 and ad_value=upper(trim(p_fiche));
        insert into quant_sold
                (qs_internal,j_id,qs_fiche,qs_quantite,qs_price,qs_vat,qs_vat_code,qs_client,qs_valid,qs_vat_sided)
        values
                (p_internal,p_jid,fid_good,p_quant,p_price,p_vat,p_vat_code,fid_client,'Y',p_tva_sided);
        return;
end;
 $function$
 LANGUAGE plpgsql;

insert into menu_ref(me_code,me_menu,me_file,me_description,me_type,me_parameter) select ex_code,ex_name,ex_file,ex_desC,'PL','plugin_code='||ex_code from extension;

insert into profile_menu (me_code,me_code_dep,p_id,p_type_display) select me_code,'EXT',1,'S' from menu_ref where me_type='PL';
update jrn set jr_internal=substr(jrn_def_type,1,1)||lpad(upper(to_hex(jr_id+1)),6,'0') from jrn_def where jrn_def_id=jr_def_id;

update version set val=98;

commit;