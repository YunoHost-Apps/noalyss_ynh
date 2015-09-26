begin;

  update menu_ref set me_description_etendue='Liste de tous vos contacts' where me_code='CONTACT' ;
 update menu_ref set me_description_etendue='Vous permet d''encoder des achats, dépenses, des notes de frais ou des notes de crédits, vous pouvez spécifier un bénéficiaire ou un autre moyen de paiement' where me_code='ACH' ;
 update menu_ref set me_description_etendue='Historique des imputations analytiques' where me_code='ANCHOP' ;
 update menu_ref set me_description_etendue='Balance simple des imputations analytiques' where me_code='ANCBS' ;
 update menu_ref set me_description_etendue='Tableau lié à la comptabilité' where me_code='ANCTAB' ;
 update menu_ref set me_description_etendue='Lien entre comptabilité et Comptabilité analytique' where me_code='ANCBCC' ;
 update menu_ref set me_description_etendue='Balance par groupe' where me_code='ANCGR' ;
 update menu_ref set me_description_etendue='Permet d''ajouter des taux de TVA ou de les modifier ainsi que les postes comptables de ces TVA, ces TVA sont utilisables dans les menus de vente et d''achat' where me_code='CFGTVA' ;
 update menu_ref set me_description_etendue='Encodage de tous vos revenus ou vente' where me_code='VEN' ;
 update menu_ref set me_description_etendue='Ajout de menu ou de plugins' where me_code='CFGMENU' ;
 update menu_ref set me_description_etendue='Suivi des fournisseurs : devis, lettres, email....' where me_code='SUPPL' ;
 update menu_ref set me_description_etendue='Opérations diverses en Analytique' where me_code='ANCODS' ;
 update menu_ref set me_description_etendue='Création de rapport sur mesure, comme les ratios, vous permet de créer des graphiques de vos données (vente, achat...)' where me_code='REPORT' ;
 update menu_ref set me_description_etendue='Menu regroupant les plugins' where me_code='EXT' ;
 update menu_ref set me_description_etendue='Les opérations prédéfinies sont des opérations que vous faites régulièrement (loyer, abonnement,...) ' where me_code='PREDOP' ;
 update menu_ref set me_description_etendue='Axe analytique' where me_code='PLANANC' ;
 update menu_ref set me_description_etendue='Regroupement de compte analytique' where me_code='ANCGROUP' ;
 update menu_ref set me_description_etendue='Opération diverses tels que les amortissements, les augmentations de capital, les salaires, ...' where me_code='ODS' ;
 update menu_ref set me_description_etendue='Encodage d''un extrait bancaire (=relevé bancaire)' where me_code='FIN' ;
 update menu_ref set me_description_etendue='Historique de toutes vos opérations un menu de recherche dans une nouvelle fenêtre, vous permettra de retrouver rapidement l''opération qui vous intéresse' where me_code='JSSEARCH' ;
 update menu_ref set me_description_etendue='Suivi client : devis, réunion, courrier, commande...' where me_code='CUST' ;
 update menu_ref set me_description_etendue='Vous permet d''ajouter de nouveaux type de documents (bordereau de livraison, devis..)' where me_code='CFGCATDOC' ;
 update menu_ref set me_description_etendue='Lettrage par fiche' where me_code='LETCARD' ;
 update menu_ref set me_description_etendue='Historique de toutes vos opérations dans les journaux d''achats un menu de recherche, vous permettra de retrouver rapidement l''opération qui vous intéresse' where me_code='ACHISTO' ;
 update menu_ref set me_description_etendue='Historique de toutes vos opérations dans les journaux d''opérations diverses un menu de recherche, vous permettra de retrouver rapidement l''opération qui vous intéresse' where me_code='ODHISTO' ;
 update menu_ref set me_description_etendue='Impression du détail d''un poste comptable' where me_code='PRINTPOSTE' ;
 update menu_ref set me_description_etendue='Impression de rapport personnalisé, il est aussi possible d''exporter en CSV afin de faire des graphiques' where me_code='PRINTREPORT' ;
 update menu_ref set me_description_etendue='Impression du grand livre' where me_code='PRINTGL' ;
 update menu_ref set me_description_etendue='Impression des balances comptables' where me_code='PRINTBAL' ;
 update menu_ref set me_description_etendue='Regroupement pour les menus d''achats(nouvelle opération, historique...)' where me_code='MENUACH' ;
 update menu_ref set me_description_etendue='Regroupement pour les menus et les profils' where me_code='MOD' ;
 update menu_ref set me_description_etendue='Module paramètres' where me_code='PARAM' ;
 update menu_ref set me_description_etendue='Menu impression' where me_code='PRINT' ;
 update menu_ref set me_description_etendue='Regroupement des menus des journaux de trésorerie' where me_code='BK' ;
 update menu_ref set me_description_etendue='Grand livre pour la comptabilité analytique' where me_code='ANCGL' ;
 update menu_ref set me_description_etendue='Module gestion' where me_code='GESTION' ;
 update menu_ref set me_description_etendue='Menu Lettrage' where me_code='LET' ;
 update menu_ref set me_description_etendue='Choix de votre dossier' where me_code='ACCESS' ;
 update menu_ref set me_description_etendue='Module comptabilité' where me_code='COMPTA' ;
 update menu_ref set me_description_etendue='Menu de différents paramètres' where me_code='DIVPARM' ;
 update menu_ref set me_description_etendue='Déconnexion ' where me_code='LOGOUT' ;
 update menu_ref set me_description_etendue='Configuration des profils des utilisateurs, permet de fixer les journaux, profils dans les documents et stock que  ce profil peut utiliser. Cela limite les utilisateurs puisque ceux-ci ont un profil' where me_code='CFGPRO' ;
 update menu_ref set me_description_etendue='Config. poste comptable de base' where me_code='CFGACC' ;
 update menu_ref set me_description_etendue='Permet d''avoir la balance de toutes vos fiches, les résumés exportables en CSV, les historiques avec ou sans lettrages' where me_code='CARD' ;
 update menu_ref set me_description_etendue='Préférence de l''utilisateur, apparence de l''application pour l''utilisateur, période par défaut et mot de passe' where me_code='PREFERENCE' ;
 update menu_ref set me_description_etendue='Configuration des tags ou dossiers, on l''appele tag ou dossier suivant la façon dont vous utilisez 
cette fonctionnalité. Vous pouvez en ajouter, en supprimer ou les modifier' where me_code='CFGTAG' ;
 update menu_ref set me_description_etendue='Balance double croisées des imputations analytiques' where me_code='ANCBC2' ;
 update menu_ref set me_description_etendue='Information sur votre société : nom, adresse... utilisé lors de la génération de documents' where me_code='COMPANY' ;
 update menu_ref set me_description_etendue='Gestion des périodes : clôture, ajout de période, afin de créer des périodes vous pouvez aussi utiliser le plugin outil comptable' where me_code='PERIODE' ;
 update menu_ref set me_description_etendue='Vérifie que votre comptabilité ne contient pas d''erreur de base, tels que l''équilibre entre le passif et l''actif, l''utilisation des postes comptables...' where me_code='VERIFBIL' ;
 update menu_ref set me_description_etendue='Ecriture d''ouverture ou écriture à nouveau, reporte les soldes des comptes de l''année passé du poste comptable 0xxx à 5xxxx sur l''année courante' where me_code='OPEN' ;
 update menu_ref set me_description_etendue='Chargement de modèles de documents qui seront générés par NOALYSS, les formats utilisables sont libreoffice, html, text et rtf' where me_code='CFGDOC' ;
 update menu_ref set me_description_etendue='Création et modification des journaux, préfixe des pièces justificatives, numérotation, catégories de fiches accessibles à ce journal' where me_code='CFGLED' ;
 update menu_ref set me_description_etendue='Configuration de la sécurité, vous permet de donner un profil à vos utilisateurs, cela leur permettra d''utiliser ce que vous souhaitez qu''ils puissent utiliser' where me_code='CFGSEC' ;
 update menu_ref set me_description_etendue='Permet d''ajouter de nouveaux attributs que vous pourrez par la suite ajouter à des catégories de fiches' where me_code='CFGATCARD' ;
 update menu_ref set me_description_etendue='Etat des stock de l''exercice indiqué' where me_code='STOCK_STATE' ;
 update menu_ref set me_description_etendue='Modification des stocks, menu utilisé pour l''inventaire' where me_code='STOCK_INV' ;
 update menu_ref set me_description_etendue='Liste des changements manuels des stocks, inventaire, transfert de marchandises entre dépôts...' where me_code='STOCK_INVHISTO' ;
 update menu_ref set me_description_etendue='Le navigateur vous présente une liste de menu auquel vous avez accès et vous permet d''accèder plus rapidement au menu que vous souhaitez' where me_code='NAVI' ;
 update menu_ref set me_description_etendue='Historique de toutes vos opérations dans tous  les journaux auquels vous avez accès, vous permettra de retrouver rapidement l''opération qui vous intéresse sur base de la date, du poste comptable, des montants...' where me_code='SEARCH' ;
 update menu_ref set me_description_etendue='Historique de toutes vos opérations un menu de recherche, vous permettra de retrouver rapidement l''opération qui vous intéresse' where me_code='HIST' ;
 update menu_ref set me_description_etendue='Permet de faire correspondre vos extraits bancaires avec les opérations de vente ou d''achat, le lettrage se fait automatiquement' where me_code='FREC' ;
 update menu_ref set me_description_etendue='Solde des journaux de trésorerie cela concerne les comptes en banques, caisse , les chèques... ' where me_code='FSALDO' ;
 update menu_ref set me_description_etendue='lettrage par poste comptable' where me_code='LETACC' ;
 update menu_ref set me_description_etendue='Balance par catégorie de fiche ou pour toutes les fiches ayant un poste comptable' where me_code='CARDBAL' ;
 update menu_ref set me_description_etendue='Modification de votre plan comptable, parfois il est plus rapide d''utiliser le plugin "Poste Comptable"' where me_code='CFGPCMN' ;
 update menu_ref set me_description_etendue='Historique de toutes vos opérations dans les journaux de vente un menu de recherche, vous permettra de retrouver rapidement l''opération qui vous intéresse' where me_code='VEHISTO' ;
 update menu_ref set me_description_etendue='Historique de toutes vos opérations dans les journaux de trésorerie un menu de recherche, vous permettra de retrouver rapidement l''opération qui vous intéresse' where me_code='FIHISTO' ;
 update menu_ref set me_description_etendue='Impression des rapprochements : opérations non rapprochées ou avec des montants différents' where me_code='PRINTREC' ;
 update menu_ref set me_description_etendue='Impression de bilan, ce module est basique, il est plus intéressant d''utiliser le plugin "rapport avancés"' where me_code='PRINTBILAN' ;
 update menu_ref set me_description_etendue='Configuration des entrepots de dépôts' where me_code='CFGSTOCK' ;
 update menu_ref set me_description_etendue='Permet d''ajouter de nouvelles catégorie de fiche, d''ajouter des attributs à ces catégories (numéro de téléphone, gsm, email...)' where me_code='STOCK' ;
 update menu_ref set me_description_etendue='Permet de changer le poste comptable de base des catégories de fiches' where me_code='CFGCARDCAT' ;
 update menu_ref set me_description_etendue='Permet d''ajouter de nouvelles catégorie de fiche, d''ajouter des attributs à ces catégories (numéro de téléphone, gsm, email...)' where me_code='CFGCARD' ;
 update menu_ref set me_description_etendue='Permet d''ajouter des état pour les documents utilisés dans le suivi (à faire, à suivre...)' where me_code='CFGDOCST' ;
 update menu_ref set me_description_etendue='Regroupement pour les menus d''opérations diverses (nouvelle opération, historique...)' where me_code='MENUODS' ;
 update menu_ref set me_description_etendue='Impression des journaux avec les détails pour les parties privés, la TVA et ce qui est non déductibles en ce qui concerne les journaux de vente et d''achat' where me_code='PRINTJRN' ;
 update menu_ref set me_description_etendue='Regroupement des menus ventes et recettes' where me_code='VENMENU' ;
 update menu_ref set me_description_etendue='Impression compta. analytique' where me_code='ANCIMP' ;
 update menu_ref set me_description_etendue='Module comptabilité analytique' where me_code='ANC' ;
 update menu_ref set me_description_etendue='Tableau de suivi, vous permet de voir en un coup d''oeil vos dernières opérations, un petit calendrier, une liste de chose à faire...' where me_code='DASHBOARD' ;
 update menu_ref set me_description_etendue='Menu regroupant la création de rapport, la vérification de la comptabilité...' where me_code='ADV' ;
 update menu_ref set me_description_etendue='Ce menu vous présente  un menu rapide de vos menus préférés' where me_code='BOOKMARK' ;
 update menu_ref set me_description_etendue='Regroupement pour les menus de trésorerie (nouvelle opération, historique...)' where me_code='MENUFIN' ;
 update menu_ref set me_description_etendue='Liste de vos suivis, en fait de tous les documents, réunions ... dont vous avez besoin afin de suivre vos clients, fournisseurs ou administrations. Il permet la génération de documents comme les devis, les bordereau de livraison...' where me_code='FOLLOW' ;
 update menu_ref set me_description_etendue='Configuration des moyens de paiements que vous voulez utiliser dans les journaux de type VEN ou ACH, les moyens de paiement permettent de générer l''opération de trésorerie en même temps que l''achat, la note de frais ou la vente' where me_code='CFGPAY' ;
 update menu_ref set me_description_etendue='Suivi des administrations : courrrier, déclarations.' where me_code='ADM' ;
 update menu_ref set me_description_etendue='Prévision de vos achats, revenus, permet de suivre l''évolution de votre société. Vos prévisions sont des formules sur les postes comptables et vous permettent aussi vos marges brutes.' where me_code='FORECAST' ;

CREATE OR REPLACE VIEW v_detail_sale AS 
with m as 
	(select sum(qs_price) as htva, sum(qs_vat) as tot_vat,jr_id from quant_sold join jrnx using (j_id) join jrn on (j_grpt=jr_grpt_id) group by jr_id)
 SELECT jrn.jr_id, 
	jrn.jr_date, 
	jrn.jr_tech_per, 
	jrn.jr_comment, 
	jrn.jr_pj_number, 
	jrn.jr_internal, 
	jrn.jr_def_id, 
	jrnx.j_poste, 
	jrnx.j_text, 
	jrnx.j_qcode, 
	quant_sold.qs_fiche as item_card, 
	a.name AS item_name, 
	quant_sold.qs_client, 
	b.vw_name AS tiers_name, 
	b.quick_code, 
	tva_rate.tva_label, 
	tva_rate.tva_comment, 
	tva_rate.tva_both_side, 
	quant_sold.qs_vat_sided as vat_sided, 
	quant_sold.qs_vat_code as vat_code, 
	quant_sold.qs_vat as vat, 
	quant_sold.qs_price as price, 
	quant_sold.qs_quantite as quantity, 
	quant_sold.qs_price / quant_sold.qs_quantite AS price_per_unit
	, htva
	,tot_vat
   FROM jrn
   JOIN jrnx ON jrn.jr_grpt_id = jrnx.j_grpt
   JOIN quant_sold USING (j_id)
   JOIN vw_fiche_name a ON quant_sold.qs_fiche = a.f_id
   JOIN vw_fiche_attr b ON quant_sold.qs_client = b.f_id
   JOIN tva_rate ON quant_sold.qs_vat_code = tva_rate.tva_id
   join m on (m.jr_id=jrn.jr_id)
   ;

CREATE OR REPLACE VIEW v_detail_purchase AS 
 WITH m AS (
         SELECT sum(quant_purchase.qp_price) AS htva, sum(quant_purchase.qp_vat) AS tot_vat, jrn.jr_id
           FROM quant_purchase
      JOIN jrnx USING (j_id)
   JOIN jrn ON jrnx.j_grpt = jrn.jr_grpt_id
  GROUP BY jrn.jr_id
        )
 SELECT jrn.jr_id, 
            jrn.jr_date
        , jrn.jr_tech_per
        , jrn.jr_comment
        , jrn.jr_pj_number
        , jrn.jr_internal
        , jrn.jr_def_id
        , jrnx.j_poste
        , jrnx.j_text
        , jrnx.j_qcode
        , quant_purchase.qp_fiche AS item_card
        , a.name AS item_name
        , quant_purchase.qp_supplier
        , b.vw_name AS tiers_name
        , b.quick_code
        , tva_rate.tva_label
        , tva_rate.tva_comment
        , tva_rate.tva_both_side
        , quant_purchase.qp_vat_sided AS vat_sided
        , quant_purchase.qp_vat_code AS vat_code
        , quant_purchase.qp_vat AS vat
        , quant_purchase.qp_price AS price
        , quant_purchase.qp_quantite AS quantity
        , quant_purchase.qp_price / quant_purchase.qp_quantite AS price_per_unit
        , quant_purchase.qp_nd_amount AS non_ded_amount
        , quant_purchase.qp_nd_tva AS non_ded_tva
        , quant_purchase.qp_nd_tva_recup AS non_ded_tva_recup
        , m.htva, m.tot_vat
   FROM jrn
   JOIN jrnx ON jrn.jr_grpt_id = jrnx.j_grpt
   JOIN quant_purchase USING (j_id)
   JOIN vw_fiche_name a ON quant_purchase.qp_fiche = a.f_id
   JOIN vw_fiche_attr b ON quant_purchase.qp_supplier = b.f_id
   JOIN tva_rate ON quant_purchase.qp_vat_code = tva_rate.tva_id
   JOIN m ON m.jr_id = jrn.jr_id;

   create or replace view v_quant_detail as
with quant as 
	(select 
		j_id,
		qp_fiche as fiche_id,
		qp_supplier as tiers, 
		qp_vat as vat_amount,
		qp_price as price,
		qp_vat_code as vat_code,
		qp_dep_priv as dep_priv,
		qp_nd_tva as nd_tva,
		qp_nd_tva_recup as nd_tva_recup,
		qp_nd_amount	as nd_amount,
		qp_vat_sided as vat_sided
		from quant_purchase
	union all	
	select 
		j_id,
		qs_fiche,
		qs_client,
		qs_vat,
		qs_price,
		qs_vat_code,
		0,
		0,
		0,
		0,
		qs_vat_sided
	from 
		quant_sold
)	
select 
	jr_id,quant.tiers,jrn_def_name,jrn_def_type,name,jr_comment,jr_montant,sum(price) as price,vat_code,sum(vat_amount) as vat_amount,sum(dep_priv) as dep_priv,sum(nd_tva) as nd_tva,sum(nd_tva_recup) as nd_tva_recup,sum(nd_amount) as nd_amount,vat_sided,tva_label
from 
jrn
join jrnx on (jrnx.j_grpt=jrn.jr_grpt_id)
join quant using (j_id)
left join vw_fiche_name on (tiers=vw_fiche_name.f_id)
join jrn_def on (jrn_def_id=jr_def_id)
join tva_rate on (tva_id=vat_code)
group by
jr_id,quant.tiers,jr_comment,jr_montant,vat_code,vat_sided,name,jrn_def_name,jrn_def_type,tva_label;


insert into menu_ref(me_code,me_menu,me_file, me_url,me_description,me_parameter,me_javascript,me_type,me_description_etendue)
values
('CSV:Reconciliation','Export opérations rapprochées','export_rec_csv.php',null,'Export opérations rapprochées en CSV',null,null,'PR','');

insert into profile_menu (me_code,me_code_dep,p_id,p_order, p_type_display,pm_default) 
values
('CSV:Reconciliation',null,1,0,'P',0), ('CSV:Reconciliation',null,2,null,'P',0);

alter table stock_change alter tech_date type time;

alter table jrn add column jr_date_paid date;

create or replace function set_paiement_date() returns void
  as
  $body$
  declare 
	  row_jrn jrn%ROWTYPE;
	  cursor_jrn cursor for select * from jrn where substr(jr_internal,1,1) in ('A','V') and jr_date_paid is null;
	  n_rec jrn_rapt.jr_id%TYPE;
	  nCount integer;
	  jrn_date jrn.jr_date%TYPE;
  begin
	  for row_jrn in cursor_jrn
	  loop
		  select count(*) into nCount from jrn_rapt where jr_id=row_jrn.jr_id or jra_concerned=row_jrn.jr_id;
		  if nCount = 1 then
			  select jr_id into n_rec from jrn_rapt where  jra_concerned=row_jrn.jr_id;
			  if NOT FOUND then
				  select jra_concerned into n_rec from jrn_rapt where  jr_id=row_jrn.jr_id;
			  end if;
			  select jr_date into jrn_date from jrn where jr_id=n_rec;
			  update jrn set jr_date_paid = jrn_date where current of cursor_jrn;
		  end if;

	  end loop;

end;
$body$
language plpgsql;

select set_paiement_date();

drop function set_paiement_date();

update version set val=109;

commit;