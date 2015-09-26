<!doctype html>
<HTML><HEAD>
    <TITLE>Noalyss - Mise à jour</TITLE>
    <META http-equiv="Content-Type" content="text/html; charset=UTF8">
    </title>
<head>
<link rel="icon" type="image/ico" href="../favicon.ico" />
 <META http-equiv="Content-Type" content="text/html; charset=UTF8">
 <script type="text/javascript" charset="<div>utf-8</div>" language="javascript" src="../js/prototype.js"></script>
 <script type="text/javascript" charset="utf-8" language="javascript" src="../js/infobulle.js"></script>
 <style>
     body {
         font : 100%;
         color:darkblue;
         margin-left : 50px;
         margin-right: 50px;
         background-color: #F8F8FF;
     }
     h1 {
         font-size: 120%;
         text-align: center;
         background-color: darkblue;
         color:white;
         text-transform: uppercase;
     }
     h2 {
         font-size: 105%;
         text-align: left;
         text-decoration: underline;
     }
     h3 {
         font-size : 102%;
         font-style: italic;
         margin-left: 3px;
     }
 
    .button {
        font-size:110%;
        color:white;
        font-weight: bold;
        border:0px;
        text-decoration:none;
        font-family: helvetica,arial,sans-serif;
        background-image: url("../image/bg-submit2.gif");
        background-repeat: repeat-x;
        background-position: left;
        text-decoration:none;
        font-family: helvetica,arial,sans-serif;
        border-width:0px;
        padding:2px 4px 2px 4px;
        cursor:pointer;
        margin:1px 2px 1px 2px;
        -moz-border-radius:2px 2px;
        border-radius:2px 2px;
     }
    .button:hover {
    cursor:pointer;
    background-color:white;
    border-style:  solid;
    border-width:  0px;
    font-color:blue;
    margin:2px 2px 1px 2px;
    }
    .warning,.error {
        color:red;
    }
 </style>
</head>
<body>
<p align="center">
  <IMG SRC="../image/logo6820.png" style="width: 415px;height: 200px" alt="NOALYSS">
</p>
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
/* $Revision*/
// Copyright Author Dany De Bontridder danydb@aevalys.eu
/*!\file
 * \brief This file permit to upgrade a version of NOALYSS , it should be
 *        used and immediately delete after an upgrade.
 *        This file is included in each release  for a new upgrade
 *
 */
?>
<DIV id="bulle" class="infobulle"></DIV>
        <script type="text/javascript" language="javascript"  src="../js/infobulle.js"> </script>
		 <script type="text/javascript" charset="utf-8" language="javascript" src="setup.js"></script>

<?php
require_once '../../include/constant.php';

$failed="<span style=\"font-size:18px;color:red\">&#x2716;</span>";
$succeed="<span style=\"font-size:18px;color:green\">&#x2713;</span>";
$inc_path=get_include_path();
global $os;
$inc_path=get_include_path();
global $os;
if ( strpos($inc_path,";") != 0 ) {
  $new_path=$inc_path.';../../include;addon';
  $os=0;			/* $os is 0 for windoz */
} else {
  $new_path=$inc_path.':../../include:addon';
  $os=1;			/* $os is 1 for unix */
}

/**
 *@brief create correctly the htaccess file
 */
function create_htaccess()
{
	global $os;


	/* If htaccess file doesn't exists we create them here
	 * if os == 1 then windows, 0 means Unix
	 */
	$file='..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'.htaccess';
	if (! file_exists($file))
	{
		$hFile=@fopen($file,'w+');
		if ( ! $hFile )     exit('Impossible d\'&eacute;crire dans le r&eacute;pertoire include');
		fwrite($hFile,'order deny,allow'."\n");
		fwrite($hFile,'deny from all'."\n");
		fclose($hFile);
	}
	$file='..'.DIRECTORY_SEPARATOR.'.htaccess';
	if (! file_exists($file))
	{

		$hFile=@fopen($file,'w+');
		if ( ! $hFile )     exit('Impossible d\'&eacute;crire dans le r&eacute;pertoire html');
		$array=array("php_flag  magic_quotes_gpc off",
				 "php_value max_execution_time 240",
				 "php_value memory_limit 20M",
				 "AddDefaultCharset utf-8",
				 "php_flag  register_globals off",
				 "php_value error_reporting 10239",
				 "php_value post_max_size 20M",
				 "php_flag short_open_tag on",
				 "php_value upload_max_filesize 20M",
				 "php_value session.use_trans_sid 1",
				 "php_value session.use_cookies 1",
				 "php_flag session.use_only_cookies on");

		if ( $os == 0 )
		  fwrite($hFile,'php_value include_path .;../../include;../include;addon'."\n");
		else
		  fwrite($hFile,'php_value include_path .:../../include:../include:addon'."\n");
		foreach ($array as $value ) fwrite($hFile,$value."\n");
		fclose($hFile);
	}

}

/* The config file is created here */
if (isset($_POST['save_config'])) {
  require_once '../../include/config_file.php';
  $url=config_file_create($_POST,1,$os);
echo '
<form method="post" >
    Les informations sont sauv&eacute;es vous pouvez continuer
<input type="submit" class="button" value="Continuer">
</form>';
 exit();
 }
if ( is_writable ('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'constant.php') == false ) {
    echo '<h2 class="notice"> '._("Ecriture non possible").' </h2>'.
            '<p class="warning"> '.
            _("On ne peut pas écrire dans le répertoire de NOALYSS, changez-en les droits ")
            .'</p>';
    exit();
  }


if ( ! file_exists('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'config.inc.php')) {
  echo '<h1 class="info">'._('Entrez les informations nécessaires à noalyss').'</h1>';
  echo '<form method="post">';
  require_once('../../include/config_file.php');
  echo config_file_form();
  echo '<div style="position:float;float:left;"></div>';
  echo '<p style="text-align:center">',
        HtmlInput::submit('save_config',_('Sauver la configuration'),"","button"),
          '</p>';
  echo "</div>";
  echo '</form>';
  exit();
}

//----------------------------------------------------------------------
// End functions
//
//----------------------------------------------------------------------

// Verify some PHP parameters
// magic_quotes_gpc = Off
// magic_quotes_runtime = Off
// magic_quotes_sybase = Off
// include_path
require_once NOALYSS_INCLUDE.'/config_file.php';
require_once NOALYSS_INCLUDE.'/class_database.php';

if ( defined ("MULTI") && MULTI==1) { create_htaccess();}

echo '<h1 class="title">'._('Configuration').'</h1>';
?>
<h2>Info</h2>
Vous utilisez le domaine <?php echo domaine; ?>
<h2>PHP</h2>
<?php

$flag_php=0;

//ini_set("memory_limit","200M");
echo "<ul style=\"list-style-type: square;\">";
foreach (array('magic_quotes_gpc','magic_quotes_runtime') as $a) {
echo "<li>";
  if ( ini_get($a) == false ) print $a.': '.$succeed;
  else {
        print $a.': '.$failed;
	print ("<h2 class=\"error\">$a a une mauvaise valeur !</h2>");
	$flag_php++;
  }

echo "</li>";
}
$module=get_loaded_extensions();

echo "<li>";
$str_error_message=_('Vous devez installer ou activer l\'extension<span style="font-weight:bold"> %s </span>');
if (  in_array('mbstring',$module) == false ){
  echo 'module mbstring '.$failed;
  echo '<span class="warning">',
        sprintf($str_error_message, "mbstring"),
        ' </span>';
  $flag_php++;
} else echo 'module mbstring '.$succeed;
echo "</li>";

echo "<li>";
if (  in_array('pgsql',$module) == false )
{
  echo 'module PGSQL '.$failed;
   echo '<span class="warning">',
        sprintf($str_error_message, "psql"),
        ' </span>';
  $flag_php++;
} else echo 'module PGSQL '.$succeed;
echo "</li>";

echo "<li>";
if ( in_array('bcmath',$module) == false )
{
  echo 'module BCMATH ok '.$failed;
  echo '<span class="warning">',
        sprintf($str_error_message, "bcmath"),
        ' </span>';
  $flag_php++;
} else echo 'module BCMATH '.$succeed;
echo "</li>";

echo "<li>";
if (in_array('gettext',$module) == false )
{
  echo 'module GETTEXT '.$failed;
   echo '<span class="warning">',
        sprintf($str_error_message, "gettext"),
        ' </span>';
  $flag_php++;
} else echo 'module GETTEXT '.$succeed;
echo "</li>";

echo "<li>";
if ( in_array('zip',$module) == false )
{
  echo 'module ZIP '.$failed;
   echo '<span class="warning">',
        sprintf($str_error_message, "zip"),
        ' </span>';
  $flag_php++;
} else echo 'module ZIP '.$succeed;
echo "</li>";
echo "<li>";
if ( in_array('gd',$module) == false )
{
  echo 'module GD '.$failed;
   echo '<span class="warning">',
        sprintf($str_error_message, "gd"),
        ' </span>';
  $flag_php++;
} else echo 'module GD '.$succeed;
echo "</li>";

if ( ini_get("max_execution_time") < 60 )  {
        echo "<li>";
        echo _('Avertissement').' : '.$failed;
	echo '<span class="info"> ',
                _("max_execution_time devrait être de 60 minimum"),
                '</span>';
        echo "</li>";
}

if ( ini_get("register_globals") == true)  {
        echo "<li>";
        echo _('Avertissement').' : '.$failed;
	print '<span class="warning"> register_globals doit être à off</span>';
        echo "</li>";
	$flag_php++;
}

if ( ini_get("session.use_trans_sid") == false )  {
        echo "<li>";
        echo _('Avertissement').' : '.$failed;
	print '<span class="warning"> avertissement session.use_trans_sid should be set to true </span>';
        echo "</li>";
}

echo "</li>";

 echo "</ul>";
if ( $flag_php==0 ) {
	echo '<p class="info"> php.ini est bien configur&eacute; '.$succeed.'</p>';
} else {
	echo '<p class="warning"> php mal configur&eacute; '.$failed.' </p>';
}
/* check user */
if ( (defined("MULTI") && MULTI==1)|| !defined("MULTI"))
{

	$cn=new Database(-1,'template');
} else
{
	$cn=new Database();
}

?>
<h2>Base de données</h2>
<?php
 // Verify Psql version
 //--
$sql="select setting from pg_settings where name='server_version'";
$version=$cn->get_value($sql);

echo "Version base de données :",$version;

if ( $version[0] < 8 ||
     ($version[0]=='8' && $version[2]<4)
     )
  {
?>
  <p><?php echo $failed?> Vous devez absolument utiliser au minimum une version 8.4 de PostGresql, si votre distribution n'en
offre pas, installez-en une en la compilant. </p><p>Lisez attentivement la notice sur postgresql.org pour migrer
vos bases de donn&eacute;es
</p>
<?php exit(); //'
} else {
    echo " ",$g_succeed;
}

?>
<h3>Paramètre base de données</h3>
<?php
// Language plsql is installed
//--
$sql="select lanname from pg_language where lanname='plpgsql'";
$Res=$cn->count_sql($sql);
if ( $Res==0) { ?>
<p><?php echo $failed?> Vous devez installer le langage plpgsql pour permettre aux fonctions SQL de fonctionner.</p>
<p>Pour cela, sur la ligne de commande en tant qu\'utilisateur postgres, faites createlang plpgsql template1
</p>

<?php exit(); }

include_once('ac_common.php');
require_once('class_dossier.php');

// Memory setting
//--
$sql="select name,setting
      from pg_settings
      where
      name in ('effective_cache_size','shared_buffers')";
$cn->exec_sql($sql);
$flag=0;
for ($e=0;$e<$cn->size();$e++) {
  $a=$cn->fetch($e);
  switch ($a['name']){
  case 'effective_cache_size':
    if ( $a['setting'] < 1000 ){

      print '<p class="warning">'.$failed.'Attention le param&egrave;tre effective_cache_size est de '.
	$a['setting']." au lieu de 1000 </p>";
      $flag++;
    }
    break;
  case 'shared_buffers':
    if ( $a['setting'] < 640 ){
      print '<p class="warning">'.$failed.'Attention le param&egrave;tre shared_buffer est de '.
	$a['setting']."au lieu de 640</p>";
      $flag++;
    }
    break;
  }
 }
if ( $flag == 0 ) {
  echo '<p class="info"> La base de donn&eacute;es est bien configur&eacute;e '.$succeed.'</p>';
 } else {
  echo '<p class="warning">'.$failed.'Il y a '.$flag.' param&egrave;tre qui sont trop bas</p>';
 }
if ( ! isset($_POST['go']) ) {
?>
<span style="text-align: center">
<FORM action="setup.php" METHOD="post">
<input type="submit" class="button" name="go" value="<?php echo _("Commencer la mise à jour ou l'installation");?>">
</form>
</span>
<?php
}
if ( ! isset($_POST['go']) )
	exit();
// Check if account_repository exists
	if (!defined("MULTI") || (defined("MULTI") && MULTI == 1))
		$account = $cn->count_sql("select * from pg_database where datname=lower('" . domaine . "account_repository')");
	else
		$account=1;

// Create the account_repository
if ($account == 0 ) {

  echo "Creation of ".domaine."account_repository";
  if ( ! DEBUG) ob_start();
  $cn->exec_sql("create database ".domaine."account_repository encoding='utf8'");
  $cn=new Database();
  $cn->start();
  $cn->execute_script("sql/account_repository/schema.sql");
  $cn->execute_script("sql/account_repository/data.sql");
  $cn->execute_script("sql/account_repository/constraint.sql");
  $cn->commit($cn);

 if ( ! DEBUG) ob_end_clean();

  echo "Creation of Modele1";
  if ( ! DEBUG) ob_start();
  $cn->exec_sql("create database ".domaine."mod1 encoding='utf8'");

  $cn=new Database(1,'mod');
  $cn->start();
  $cn->execute_script('sql/mod1/schema.sql');
  $cn->execute_script('sql/mod1/data.sql');
  $cn->execute_script('sql/mod1/constraint.sql');
  $cn->commit();

  if ( ! DEBUG) ob_end_clean();

  echo "Creation of Modele2";
  $cn->exec_sql("create database ".domaine."mod2 encoding='utf8'");
  $cn=new Database(2,'mod');
  $cn->start();
  if ( ! DEBUG) { ob_start();  }
  $cn->execute_script('sql/mod1/schema.sql');
  $cn->execute_script('sql/mod2/data.sql');
  $cn->execute_script('sql/mod1/constraint.sql');
  $cn->commit();

 if ( ! DEBUG) ob_end_clean();

 }// end if
// Add a french accountancy model
//--
$cn=new Database();

echo "<h1>Mise a jour du systeme</h1>";
echo "<h2 > Mise &agrave; jour dossier</h2>";
if  (defined("MULTI") && MULTI == 0)
{
	$db = new Database();
	if ($db->exist_table("version") == false)
	{
		echo '<p class="warning">' . $failed . 'La base de donnée ' . dbname . ' est vide, veuillez executer noalyss/contrib/mono-dossier/mono.sql
                    puis faites un seul de ces choix : 
                    <ul>
                    <li>soit noalyss/contrib/mono-dossier/mono-france.sql pour la comptabilité française</li>
                    <li>soit noalyss/contrib/mono-dossier/mono-belge.sql pour la comptabilité belge</li>
                    <li>soit y restaurer un backup ou un modèle</li>
                    </ul>
				</p>';
		exit();
	}
	echo "<h3>Patching " . dbname . '</h3>';
	$db->apply_patch(dbname);
	echo "<p class=\"info\">Tout est install&eacute; $succeed";
        
         echo "<h2>Mise &agrave; jour Repository</h2>";
         if ( DEBUG == false ) ob_start();
        $MaxVersion=DBVERSIONREPO-1;
        for ($i=4;$i<= $MaxVersion;$i++)
        {
            if ( $db->get_value (' select val from repo_version') <= $i ) {
                $db->execute_script('sql/patch/ac-upgrade'.$i.'.sql');
            }
        }

	?>
<p style="text-align: center">
		<A style="" class="button" HREF="../index.php">Connectez-vous à NOALYSS</A>
                </p>
	<?php
	exit();
}

/*
 * If multi folders
 */
$Resdossier=$cn->exec_sql("select dos_id, dos_name from ac_dossier");
$MaxDossier=$cn->size($Resdossier);

//----------------------------------------------------------------------
// Upgrade the folders
//----------------------------------------------------------------------

for ($e=0;$e < $MaxDossier;$e++) {
   $db_row=Database::fetch_array($Resdossier,$e);
  echo "<h3>Patching ".$db_row['dos_name'].'</h3>';

  $name=$cn->format_name($db_row['dos_id'],'dos');

  if ( $cn->exist_database($name)> 0 )
  {
    $db=new Database($db_row['dos_id'],'dos');
    $db->apply_patch($db_row['dos_name']);
    Dossier::synchro_admin($db_row['dos_id']);

  } else
  {
      echo_warning(_("Dossier inexistant")." $name");
  }
 }

//----------------------------------------------------------------------
// Upgrade the template
//----------------------------------------------------------------------
$Resdossier=$cn->exec_sql("select mod_id, mod_name from modeledef");
$MaxDossier=$cn->size();
echo "<h2>Mise &agrave; jour mod&egrave;le</h2>";

for ($e=0;$e < $MaxDossier;$e++) {
  $db_row=Database::fetch_array($Resdossier,$e);
  echo "<h3>Patching ".$db_row['mod_name']."</h3>";
  $name=$cn->format_name($db_row['mod_id'],'mod');

  if ( $cn->exist_database($name)> 0 )
  {
    $db=new Database($db_row['mod_id'],'mod');
    $db->apply_patch($db_row['mod_name']);
   } else
  {
      echo_warning(_("Modèle inexistant")." $name");
  }
 }

//----------------------------------------------------------------------
// Upgrade the account_repository
//----------------------------------------------------------------------
 echo "<h2>Mise &agrave; jour Repository</h2>";
 $cn=new Database();
 if ( DEBUG == false ) ob_start();
 $MaxVersion=DBVERSIONREPO-1;
 for ($i=4;$i<= $MaxVersion;$i++)
   {
 	if ( $cn->get_version() <= $i ) {
 	  $cn->execute_script('sql/patch/ac-upgrade'.$i.'.sql');
 	}
   }

 if (! DEBUG) ob_end_clean();
 echo "<p class=\"info\">Tout est install&eacute; $succeed";
?>
</p>
<p style="text-align: center">
<A style="" class="button" HREF="../index.php">Connectez-vous à NOALYSS</A>
</p>