<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// Copyright Author Dany De Bontridder danydb@aevalys.eu

/*! \file
 * \brief Contains all the variable + the javascript
 * and some parameter
 */

// SVNVERSION
global $version_noalyss;
/*
 * Include path
 */
$inc_path=get_include_path();
$dirname=dirname(__FILE__);

/* Global variable of the include dir */
global $g_include_dir,$g_ext_dir,$g_template_dir;
$g_include_dir=$dirname;
$g_ext_dir = $dirname."/ext";
$g_template_dir = $dirname."/template";

if (file_exists($dirname.'/config.inc.php')) require_once $dirname.'/config.inc.php';

if ( !defined("NOALYSS_HOME")) define ("NOALYSS_HOME",dirname($dirname)."/html");
if ( !defined("NOALYSS_PLUGIN")) define ("NOALYSS_PLUGIN",$g_ext_dir);
if ( !defined("NOALYSS_INCLUDE")) define ("NOALYSS_INCLUDE",$g_include_dir);
if ( !defined("NOALYSS_TEMPLATE")) define ("NOALYSS_TEMPLATE",$g_template_dir);

require_once NOALYSS_INCLUDE.'/constant.security.php';

if ( strpos($inc_path,";") != 0 ) {
  $new_path=$inc_path.';'.$dirname;
  $os=0;			/* $os is 0 for windoz */
} else {
  $new_path=$inc_path.':'.$dirname;
  $os=1;			/* $os is 1 for unix */
}
set_include_path($new_path);
ini_set ('session.use_cookies',1);
ini_set ('session.use_only_cookies','on');
ini_set ('magic_quotes_gpc','off');
ini_set ('max_execution_time',240);
ini_set ('memory_limit','256M');
ini_set ('default_charset',"UTF-8");
@ini_set ('session.use_trans_sid','on');
@session_start();

/*
 * Ini session
 */


global $g_captcha,$g_failed,$g_succeed;
$g_captcha=false;
$g_failed="<span style=\"font-size:18px;color:red\">&#x2716;</span>";
$g_succeed="<span style=\"font-size:18px;color:green\">&#x2713;</span>";
define ('SMALLX','&#x2D5D;');
define ('BUTTONADD',"&#10010;");

/* uncomment for development */

// define ('SVNINFO',6800);
//define ("DEBUG",true);
//define ("LOGINPUT",true);



define ('SVNINFO',6900);
if ( ! defined  ('DEBUG')) {
    define ("DEBUG",false);
}
if ( ! defined ('LOGINPUT')) {
    define ("LOGINPUT",false);
}

$version_noalyss=SVNINFO;

// If you don't want to be notified of the update
if ( !defined("SITE_UPDATE"))
    define ("SITE_UPDATE",'http://www.noalyss.eu/last_version.txt');
if ( !defined("SITE_UPDATE_PLUGIN"))
    define ("SITE_UPDATE_PLUGIN",'http://www.noalyss.eu/plugin_last_version.txt');


define ("DBVERSION",121);
define ("MONO_DATABASE",25);
define ("DBVERSIONREPO",16);
define ('NOTFOUND','--not found--');
define ("MAX_COMPTE",4);
define ('MAX_ARTICLE',5);
define ('MAX_ARTICLE_STOCK',20);
define ('MAX_CAT',15);
define ('MAX_CARD_SEARCH',550);
define ('MAX_FORECAST_ITEM',10);
define ('MAX_PREDEFINED_OPERATION',50);
define ('MAX_COMPTE_CARD',4);
define ('COMPTA_MAX_YEAR',2100);
define ('COMPTA_MIN_YEAR',1900);
define ('MAX_RECONCILE',25);
define ('MAX_QCODE',4);
define ('MAX_SEARCH_CARD',20);
define ('MAX_FOLDER_TO_SHOW',20);
define ('MAX_ACTION_SHOW',20);

if ( DEBUG ) {
	error_reporting(2147483647);
	ini_set("display_errors",1);
	ini_set("display_startup_errors",1);
	ini_set("html_errors",1);
        ini_set('log_errors',1);
        ini_set('log_errors_max_len',0);
}
else {
        // Rapporte les erreurs d'exécution de script
        error_reporting(E_ERROR | E_WARNING | E_PARSE|E_NOTICE);
        ini_set("display_errors",0);
	ini_set("html_errors",0);
        ini_set('log_errors',1);
        ini_set('log_errors_max_len',0);
}
// Erreur
define ("NOERROR",0);
define ("BADPARM",1);
define ("BADDATE",2);
define ("NOTPERIODE",3);
define ("PERIODCLOSED",4);
define ("INVALID_ECH",5);
define ("RAPPT_ALREADY_USED",6);
define ("RAPPT_NOT_EXIST",7);
define ("DIFF_AMOUNT",8);
define ("RAPPT_NOMATCH_AMOUNT",9);
define ("NO_PERIOD_SELECTED",10);
define ("NO_POST_SELECTED",11);
define ("LAST",1);
define ("FIRST",0);
define ("ERROR",12);

//!\enum ACTION  defines document_type for action
define('ACTION','1,5,6,7,8');

//valeurs standardd
define ("YES",1);
define ("NO",0);
define ("OPEN",1);
define ("CLOSED",0);
define ("NOTCENTRALIZED",3);
define ("ALL",4);

// Pour les ShowMenuComptaLeft
define ("MENU_FACT",1);
define ("MENU_FICHE",2);
define ("MENU_PARAM",3);

// for the fiche_inc.GetSqlFiche function
define ("ALL_FICHE_DEF_REF", 1000);

// fixed value for attr_def data
define ("ATTR_DEF_ACCOUNT",5);
define ("ATTR_DEF_NAME",1);
define ("ATTR_DEF_BQ_NO",3);
define ("ATTR_DEF_BQ_NAME",4);
define ("ATTR_DEF_PRIX_ACHAT",7);
define ("ATTR_DEF_PRIX_VENTE",6);
define ("ATTR_DEF_TVA",2);
define ("ATTR_DEF_NUMTVA",13);
define ("ATTR_DEF_ADRESS",14);
define ("ATTR_DEF_CP",15);
define ("ATTR_DEF_PAYS",16);
define ("ATTR_DEF_STOCK",19);
define ("ATTR_DEF_TEL",17);
define ("ATTR_DEF_EMAIL",18);
define ("ATTR_DEF_CITY",24);
define ("ATTR_DEF_COMPANY",25);
define ("ATTR_DEF_FAX",26);
define ("ATTR_DEF_NUMBER_CUSTOMER",30);
define ("ATTR_DEF_DEP_PRIV",31);
define ("ATTR_DEF_DEPENSE_NON_DEDUCTIBLE",20);
define ("ATTR_DEF_TVA_NON_DEDUCTIBLE",21);
define ("ATTR_DEF_TVA_NON_DEDUCTIBLE_RECUP",22);
define ("ATTR_DEF_QUICKCODE",23);
define ("ATTR_DEF_FIRST_NAME",32);

define( 'ATTR_DEF_ACCOUNT_ND_TVA',50);
define('ATTR_DEF_ACCOUNT_ND_TVA_ND',51);
define ('ATTR_DEF_ACCOUNT_ND_PERSO',52);
define ('ATTR_DEF_ACCOUNT_ND',53);

define ("FICHE_TYPE_CLIENT",9);
define ("FICHE_TYPE_VENTE",1);
define ("FICHE_TYPE_FOURNISSEUR",8);
define ("FICHE_TYPE_FIN",4);
define ("FICHE_TYPE_CONTACT",16);
define ("FICHE_TYPE_EMPL",25);
define ("FICHE_TYPE_ADM_TAX",14);
define ("FICHE_TYPE_ACH_MAR",2);
define ("FICHE_TYPE_ACH_SER",3);
define ("FICHE_TYPE_ACH_MAT",7);
define ("FICHE_TYPE_PROJET",26);
define ("FICHE_TYPE_MATERIAL",7);

/** 
 * -- pour utiliser unoconv démarrer un server libreoffice 
 * commande
 * libreoffice --headless --accept="socket,host=127.0.0.1,port=2002;urp;" --nofirststartwizard 
 * ou
 *  unoconv -l -v -s localhost
 */
define ('OFFICE','unoconv ');
define ('GENERATE_PDF','YES');

/**
 * Pour conversion GIF en PDF
 */
$convert_gif_pdf='/usr/bin/convert';
if (file_exists($convert_gif_pdf))
{
    define ('CONVERT_GIF_PDF',$convert_gif_pdf);
} else {
    define ('CONVERT_GIF_PDF','NOT');
    
}

/**
 * Outil pour manipuler les PDF 
 */
$pdftk='/usr/bin/pdftk';
if (file_exists($pdftk))
{
    define ('PDFTK',$pdftk);  
} 
else
{
    define ('PDFTK','NOT');  
}


define ('JS_INFOBULLE','
        <DIV id="bulle" class="infobulle"></DIV>
        <script type="text/javascript" language="javascript"  src="js/infobulle.js">
        </script>');


// Sql string
define ("SQL_LIST_ALL_INVOICE","");

define ("SQL_LIST_UNPAID_INVOICE","  (jr_rapt is null or jr_rapt = '') and jr_valid = true  "
       );


define ("SQL_LIST_UNPAID_INVOICE_DATE_LIMIT" ,"
        where (jr_rapt is null or jr_rapt = '')
        and to_date(to_char(jr_ech,'DD.MM.YYYY'),'DD.MM.YYYY') < to_date(to_char(now(),'DD.MM.YYYY'),'DD.MM.YYYY')
        and jr_valid = true" );
?>
