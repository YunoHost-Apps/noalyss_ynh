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

  /*!\file
   * \brief show the history of a card of an accounting
   * for the card f_id is set and for an accounting : pcm_val
   */
if ( ! defined('ALLOWED')) define ('ALLOWED',1);

require_once '../include/constant.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_periode.php';
require_once NOALYSS_INCLUDE.'/class_html_input.php';
require_once NOALYSS_INCLUDE.'/class_acc_account.php';
require_once NOALYSS_INCLUDE.'/class_exercice.php';
$div=$_REQUEST['div'];
mb_internal_encoding("UTF-8");

/**
 *if $_SESSION['g_user'] is not set : echo a warning
 */
ajax_disconnected($div);
global $g_user,$cn;
$cn=new Database(dossier::id());
$g_user=new User($cn);
set_language();
/* security */
if ( $g_user->check_dossier(dossier::id(),true) == 'X' ) exit();

$from_div = (isset($_REQUEST['ajax'])) ? 1 : $_GET['l'];
if ( LOGINPUT)
    {
        $file_loginput=fopen($_ENV['TMP'].'/scenario-'.$_SERVER['REQUEST_TIME'].'.php','a+');
        fwrite ($file_loginput,"<?php \n");
        fwrite ($file_loginput,"//@description:\n");
        fwrite($file_loginput, '$_GET='.var_export($_GET,true));
        fwrite($file_loginput,";\n");
        fwrite($file_loginput, '$_POST='.var_export($_POST,true));
        fwrite($file_loginput,";\n");
        fwrite($file_loginput, '$_POST[\'gDossier\']=$gDossierLogInput;');
        fwrite($file_loginput,"\n");
        fwrite($file_loginput, '$_GET[\'gDossier\']=$gDossierLogInput;');
        fwrite($file_loginput,"\n");
        fwrite($file_loginput,' $_REQUEST=array_merge($_GET,$_POST);');
        fwrite($file_loginput,"\n");
        fwrite($file_loginput,"include '".basename(__FILE__)."';\n");
        fclose($file_loginput);
    }
///////////////////////////////////////////////////////////////////////////
/* first detail for a card */
///////////////////////////////////////////////////////////////////////////
if ( isset($_GET['f_id']))
  {
    $exercice=new Exercice($cn);
    $old='';
    $fiche=new Fiche($cn,$_GET['f_id']);
    $year=$g_user->get_exercice();
    if ( $year == 0 )
      {
        $html=_("erreur aucune période par défaut, allez dans préférence pour en choisir une");
      }
    else
      {
        $per=new Periode($cn);
        $limit_periode=$per->get_limit($year);
        $array['from_periode']=$limit_periode[0]->first_day();
        $array['to_periode']=$limit_periode[1]->last_day();
	if (isset($_GET['ex']))
	  {
	    $limit_periode=$per->get_limit($_GET['ex']);
	    if ( $_GET['ex'] < $year)
	      $array['from_periode']=$limit_periode[0]->first_day();
	    else
	      $array['to_periode']=$limit_periode[1]->last_day();

	  }

	/*
	 * Add button to select another year
	 */
	if ($exercice->count() > 1 )
	  {
	    $default=(isset($_GET['ex']))?$_GET['ex']:$year;
	    $dossier=dossier::id();
	    if ( $div != 'popup')
	      {
		$obj="{div:'$div',f_id:'".$_GET['f_id']."',gDossier:'$dossier',select:this}";
		$is=$exercice->select('p_exercice',$default,' onchange="update_history_card('.$obj.');"');
		$old=_("Autre exercice")." ".$is->input();
	      }
	    else
	      {
		$old='<form method="get" action="popup.php">';
		$is=$exercice->select('ex',$default,'onchange = "submit(this)"');
		$old.=_("Autre exercice")." ".$is->input();
		$old.=HtmlInput::hidden('div','popup');
		$old.=HtmlInput::hidden('act',$_GET['act']);
		$old.=HtmlInput::hidden('f_id',$_GET['f_id']);
		$old.=HtmlInput::hidden('ajax',$_GET['ajax']);
		$old.=dossier::hidden();
		$old.='</form>';
	      }
	  }

        ob_start();
        require_once NOALYSS_INCLUDE.'/template/history_top.php';
	$detail_card=HtmlInput::card_detail($fiche->strAttribut(ATTR_DEF_QUICKCODE),$fiche->getName());
	echo h2(  $fiche->getName().'['.$fiche->strAttribut(ATTR_DEF_QUICKCODE).']',' class="title" ');
	echo '<p style="text-align:center;">'.$detail_card.'</p>';

	if (   $fiche->HtmlTable($array,0,$from_div)==-1){
	  echo h2(_("Aucune opération pour l'exercice courant"),'class="error"');
	}

	echo $old;

        $html=ob_get_contents();
        ob_end_clean();
      }
  }
///////////////////////////////////////////////////////////////////////////
// for an account
///////////////////////////////////////////////////////////////////////////
if ( isset($_REQUEST['pcm_val']))
  {
    $poste=new Acc_Account_Ledger($cn,$_REQUEST['pcm_val']);
    $year=$g_user->get_exercice();
    if ( $year == 0 )
      {
        $html=_("erreur aucune période par défaut, allez dans préférence pour en choisir une");
      }
    else
      {
	$exercice=new Exercice($cn);
	$old='';
        $per=new Periode($cn);
        $limit_periode=$per->get_limit($year);
        $array['from_periode']=$limit_periode[0]->first_day();
        $array['to_periode']=$limit_periode[1]->last_day();
	if (isset($_GET['ex']))
	  {
	    $limit_periode=$per->get_limit($_GET['ex']);
	    if ( $_GET['ex'] < $year)
	      $array['from_periode']=$limit_periode[0]->first_day();
	    else
	      $array['to_periode']=$limit_periode[1]->last_day();

	  }
	/*
	 * Add button to select another year
	 */
	if ($exercice->count() > 1 )
	  {
	    $default=(isset($_GET['ex']))?$_GET['ex']:$year;
	    $dossier=dossier::id();
	    if ( $div != 'popup')
	      {
		$obj="{div:'$div',pcm_val:'".$_GET['pcm_val']."',gDossier:'$dossier',select:this}";
		$is=$exercice->select('p_exercice',$default,' onchange="update_history_account('.$obj.');"');
		$old=_("Autre exercice")." ".$is->input();
	      }
	    else
	      {
		$old='<form method="get" action="popup.php">';
		$is=$exercice->select('ex',$default,'onchange = "submit(this)"');
		$old.=_("Autre exercice")." ".$is->input();
		$old.=HtmlInput::hidden('div','popup');
		$old.=HtmlInput::hidden('act',$_GET['act']);
		$old.=HtmlInput::hidden('pcm_val',$_GET['pcm_val']);
		$old.=HtmlInput::hidden('ajax',$_GET['ajax']);
		$old.=dossier::hidden();
		$old.='</form>';
	      }

	  }

        ob_start();
        require_once NOALYSS_INCLUDE.'/template/history_top.php';

        if ( $poste->HtmlTable($array) == -1)
	  {
	    echo h2($poste->id." ".$poste->name,' class="title"');
	    echo h2(_("Aucune opération pour l'exercice courant"),'class="error"');
	  }
	echo $old;

        $html=ob_get_contents();
        ob_end_clean();
      }
  }
$xml=escape_xml($html);
if (DEBUG && headers_sent()) {
    echo $html;return;
}
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$div</ctl>
<code>$xml</code>
</data>
EOF;
