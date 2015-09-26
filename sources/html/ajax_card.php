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
 * \brief this file respond to an ajax request and return an object with the ctl and the html string
 * at minimum
 \verbatim
 {'ctl':'','html':''}
\endverbatim
 * The parameters are
 * - gDossier
 * - op
      - dc Detail of a card
      parameter : $qcode , optional ro for readonly and nohistory without the history button
      - bc Blank Card : display form for adding a card
      parameter fd_id (fiche_def:fd_id)
      - st Show Type : select type of card
      parameter fil : possible values of fd_id if empty it means all the fiche cat.
      - sc Save Card : insert a new card (call first bc)
      - upc  update a card
      specific parameter qcode
      - fs  Form to search card
          parameter like
	  - inp : the input text field to update
	  - str : current content of the input text field (inp)
	  - label : the element to put the name of the card
	  - price : the element to put the price of the card (if exists)
	  - tvaid : the element to put the tvaid of the card (if exists)
	  - jrn : the concerned ledger (or all)
	  - typecard : possible values are cred, deb, filter (list of fd_id)
      - ac Add Category
          - cat type of cat to add (FICHE_TYPE_CLIENT,...)
 * - ctl (to return)
 * - popup
 * - ref if we want to refresh the window
 *\see fiche fiche::Save constant.php
 */
if ( ! defined('ALLOWED')) define ('ALLOWED',1);

require_once '../include/constant.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_fiche.php';
require_once NOALYSS_INCLUDE.'/class_iradio.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_fiche_attr.php';

mb_internal_encoding("UTF-8");

$var=array('gDossier','op','ctl');
$cont=0;
/*  check if mandatory parameters are given */
foreach ($var as $v)
{
    if ( ! isset ($_REQUEST [$v] ) )
    {
        echo "$v._(' is not set ')";
        $cont=1;
    }
}
extract($_REQUEST );

ajax_disconnected($ctl);

if ( $cont != 0 ) exit();

set_language();
/*
 *echo a warning if disconnected
 */
ajax_disconnected($_REQUEST['ctl']);

$cn=new Database($gDossier);
global $g_user;
$g_user=new User($cn);
$g_user->check(true);
$g_user->check_dossier($gDossier,true);
$html=var_export($_REQUEST,true);
if ( LOGINPUT)
    {
        $file_loginput=fopen($_ENV['TMP'].'/scenario-'.$_SERVER['REQUEST_TIME'].'.php','a+');
        fwrite ($file_loginput,"<?php \n");
        fwrite ($file_loginput,'//@description:'.$op."\n");
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
switch($op)
{
    /* ------------------------------------------------------------ */
    /* Remove a attribut */
    /* ------------------------------------------------------------ */
case 'rmfa':
    if ($g_user->check_action(FICCAT)==0)exit();
        ob_start();
    if( ! isset($_GET['ad_id']) || isNumber($_GET['ad_id']) ==0)
        throw new Exception ( _("Parametre ad_id est invalide"),11);
    $ad_id=  $_GET['ad_id'];
    try
    {
        $cn->start();
        $fa=new Fiche_Attr($cn,$ad_id);
        $fa->delete();
        $cn->commit();
    }
    catch (Exception $e)
    {
        $cn->rollback();
        echo $e->getMessage();
    }
    $html=ob_get_contents();
    ob_end_clean();
    break;
    /* ------------------------------------------------------------ */
    /* Display card detail */
    /* ------------------------------------------------------------ */

case 'dc':
    $f=new Fiche($cn);
    /* add title + close */
    $html=HtmlInput::title_box(_("Détail fiche"), $ctl);
    if ( $qcode != '')
    {
        $f->get_by_qcode($qcode);
	$can_modify=$g_user->check_action(FIC);
	if ( isset($ro) )
	  {
	    $can_modify=0;
	  }
	if ( $can_modify==1)
	  $card=$f->Display(false);
	else
	  $card=$f->Display(true);
	if ( $card == 'FNT' )
	  {
	    $html.='<h2 class="error">'._('Fiche non trouvée').'</h2>';
	  }
	else
	  {

	    if ($can_modify==1)
	      {
		$html.='<form id="form_'.$ctl.'"method="get" onsubmit="update_card(this);return false;">';
		$html.=dossier::hidden();
		$html.=HtmlInput::hidden('f_id',$f->id);
		$html.=HtmlInput::hidden('ctl',$ctl);
	      }
	    $html.=$card;
            $html.='<p style="text-align:center">';
	    if ( $can_modify==1)
	      {
		$html.=HtmlInput::submit('save',_('Sauver'));
	      }
	    if ( ! isset ($nohistory))$html.=HtmlInput::history_card_button($f->id,_('Historique'));
            $html.='</p>';
	    if ($can_modify==1)
	      {
		$html.='</form>';
	      }
	  }
    }
    else
      {
      $html.='<h2 class="error">'._('Aucune fiche demandée').'</h2>';
      }
    break;
    /* ------------------------------------------------------------ */
    /* Blank card */
    /* ------------------------------------------------------------ */
case 'bc':
    if ( $g_user->check_action(FICADD)==1 )
    {
        $r=HtmlInput::title_box(_("Nouvelle fiche"), $ctl);
	/* get cat. name */
	$cat_name=$cn->get_value('select fd_label from fiche_def where fd_id=$1',
				 array($fd_id));
        $f=new Fiche($cn);
        $r.='<form id="save_card" method="POST" onsubmit="this.ipopup=\''.$ctl.'\';save_card(this);return false;" >';
        $r.=dossier::hidden();
        $r.=(isset($ref))?HtmlInput::hidden('ref',1):'';
        $r.=HtmlInput::hidden('fd_id',$fd_id);
        $r.=HtmlInput::hidden('ctl',$ctl);
        $r.=$f->blank($fd_id);
        $r.='<p style="text-align:center">';
        $r.=HtmlInput::submit('sc',_('Sauve'));
        $r.='</p>';
        $r.='</form>';
        $html=$r;
    }
    else
    {
        $html=alert(_('Action interdite'),true);
    }
    break;
    /* ------------------------------------------------------------ */
    /* Show Type */
    /* Before inserting a new card, the type must be selected */
    /* ------------------------------------------------------------ */
case 'st':
    $sql="select fd_id,fd_label,fd_description from fiche_def";
    /*  if we filter  thanks the ledger*/
    if ( $ledger != -1 )
    {
        /* we want the card for deb or cred or both of this ledger */
        switch( $fil  )
        {
        case -1:
            $l=new Acc_Ledger($cn,$ledger);
            $where='  where fd_id in ('.$l->get_all_fiche_def().')';
            break;
        case 'cred':
            $l=new Acc_Ledger($cn,$ledger);
            $prop=$l->get_propertie();
            if ( $prop['jrn_def_fiche_cred']=='')$prop=-1;
            $where='  where fd_id in ('.$prop['jrn_def_fiche_cred'].')';
            break;
        case 'deb':
            $l=new Acc_Ledger($cn,$ledger);
            $prop=$l->get_propertie();
            if ( $prop=='')$prop=-1;
            $where='  where fd_id in ('.$prop['jrn_def_fiche_deb'].')';
            break;
        }
    }
    else
    {
        /* we filter thanks a given model of card */
        if ( isset($cat))
        {
            $where=sprintf(' where frd_id in ('.sql_string ($cat).')');
        }
        else
            /* we filter thanks a given list of category of card
             */
            if ( isset($fil) && strlen(trim($fil)) > 0 )
            {
                $where=sprintf(" where fd_id in (%s)",
                                  sql_string($fil));
            }
    }
	if ( strpos($where," in ()") != 0)
	{
		 $html=HtmlInput::anchor_close('select_card_div');
		 $html.=h2info(_('Choix de la catégorie'));
		 $html.='<h3 class="notice">';
		 $html.=_("Aucune catégorie de fiche ne correspond à".
                " votre demande, le journal pourrait n'avoir accès à aucune fiche");
		 $html.='</h3>';
		 break;
	}
    $sql.=" ".$where." order by fd_label";

    $array=$cn->get_array($sql);
    $html=HtmlInput::title_box(_("Choix de la catégorie"), $ctl);

    if ( empty($array))
    {
        $html.=_("Aucune catégorie de fiche ne correspond  à".
                " votre demande");
		if ( DEBUG )        $html.=$sql;
    }
    else
    {
        $r='';
	$r.='<p  style="padding-left:2em">';
        $r.=_("Choisissez la catégorie de fiche à laquelle vous aimeriez ajouter une fiche").'</p>';
        
	$r.='<div style="text-align:center">';
        
        $msg=_('Choisissez une catégorie svp');
        $r.='<form id="sel_type" method="GET" onsubmit="this.ipopup='.$ctl.";if ($('fd_id').value != 0 ) {dis_blank_card(this);return false;} else "
                . "{ $('error_cat').innerHTML='".$msg."'; return false;}\">" ;
        $r.='<span id="error_cat" class="notice"></span>';
        $r.=dossier::hidden();
        $r.=(isset($ref))?HtmlInput::hidden('ref',1):'';
        $r.=_('Filtrer').' '.HtmlInput::filter_table("cat_card_table", '0,1', 0);
        $r.='<table id="cat_card_table" class="result">';
        for ($i=0;$i<count($array);$i++)
        {
            $class=($i%2==0)?' class="even" ':' class="odd" ';
            $r.='<tr '.$class.' id="select_cat_row_'.$array[$i]['fd_id'].'">';
            $r.='<td >';
            $r.='<a href="javascript:void(0)" onclick="select_cat(\''.$array[$i]['fd_id'].'\')">'.h($array[$i]['fd_label']).'</a>';
            $r.='</td>';
            $r.='<td>';
            $r.='<a href="javascript:void(0)" onclick="select_cat(\''.$array[$i]['fd_id'].'\')">'.h($array[$i]['fd_description']).'</a>';
            $r.='</td>';
           
             $r.="</tr>";
        }
        $r.='</table>';
        $r.=HtmlInput::hidden('fd_id',0);
        $r.='<p style="text-align:center">';
        $r.=HtmlInput::submit('st','choix');
	$r.=HtmlInput::button('Annuler',_('Annuler')," onclick=\"removeDiv('$ctl')\" ");
	$r.='</p>';
        $r.='</form>';
        $r.='</div>';
        $html.=$r;

    }
    break;
    /*----------------------------------------------------------------------
     * SC save card
     * save the new card (insert)
     *
     ----------------------------------------------------------------------*/
case 'sc':
    $html=HtmlInput::title_box(_("Choix de la catégorie"), $ctl);
    if ( $g_user->check_action(FICADD)==1 )
    {
        $f=new Fiche($cn);
        $f->insert($fd_id,$_POST);
		$f->Get();
        $html.='<h2 class="notice">'._('Fiche sauvée').'</h2>';
        $html.=$f->Display(true);
        $js="";
        if ( isset( $_POST['ref'])) $js=create_script(' window.location.reload()');
        $html.=$js;
    }
    else
    {
        $html.=alert(_('Action interdite'),true);
    }
    break;
    /*----------------------------------------------------------------------
     * Search a card
     *
     *----------------------------------------------------------------------*/
case 'fs':
    require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';
    $r=HtmlInput::title_box(_("Détail fiche"), 'search_card');
    $r.='<form method="GET" onsubmit="this.ctl=\'ipop_card\';search_get_card(this);return false;">';
    $q=new IText('query');
    $q->value=(isset($query))?$query:'';
	$r.='<span style="margin-left:50px">';
    $r.=_('Fiche contenant').HtmlInput::infobulle(19);
    $r.=$q->input();
    $r.=HtmlInput::submit('fs',_('Recherche'),"","smallbutton");
	$r.='</span>';
    $r.=dossier::hidden().HtmlInput::hidden('op','fs');
    $array=array();
    foreach (array('query','inp','jrn','label','typecard','price','tvaid') as $i)
    {
        if  (isset(${$i}) )
        {
            $r.=HtmlInput::hidden($i,${$i});
            $sql_array[$i]=${$i};
        }
    }
    /* what is the type of the ledger */
    $type="GL";
    if (isset($jrn) && $jrn > 1)
    {
        $ledger=new Acc_Ledger($cn,$jrn);
        $type=$ledger->get_type();
    }
    $fiche=new Fiche($cn);
    /* Build the SQL and show result */
    $sql=$fiche->build_sql($sql_array);

	if ( strpos($sql," in ()") != 0)
	{
		$html=HtmlInput::anchor_close('search_card');
		 $html.='<div> '.h2info(_('Recherche de fiche')).'</div>';
		 $html.='<h3 class="notice">';
		 $html.=_("Aucune catégorie de fiche ne correspond à".
                " votre demande, le journal pourrait n'avoir accès à aucune fiche");
		 $html.='</h3>';
		 break;
	}
     /* We limit the search to MAX_SEARCH_CARD records */
    $sql=$sql.' order by vw_name limit '.MAX_SEARCH_CARD;
    $a=$cn->get_array($sql);
    for($i=0;$i<count($a);$i++)
    {
        $array[$i]['quick_code']=$a[$i]['quick_code'];
        $array[$i]['name']=h($a[$i]['vw_name']);
        $array[$i]['accounting']=$a[$i]['accounting'];
        $array[$i]['first_name']=h($a[$i]['vw_first_name']);
        $array[$i]['description']=h($a[$i]['vw_description']);
        $array[$i]['javascript']=sprintf("set_value('%s','%s');",
                                         $inp,$array[$i]['quick_code']);
        $array[$i]['javascript'].=sprintf("set_value('%s','%s');",
                                          $label,j(h(strip_tags($a[$i]['vw_name']))));

        /* if it is a ledger of sales we use vw_buy
           if it is a ledger of purchase we use vw_sell*/
        if ( $type=="ACH" )
            $array[$i]['javascript'].=sprintf("set_value('%s','%s');",
                                              $price,$a[$i]['vw_buy']);
        if ( $type=="VEN" )
            $array[$i]['javascript'].=sprintf("set_value('%s','%s');",
                                              $price,$a[$i]['vw_sell']);
        $array[$i]['javascript'].=sprintf("set_value('%s','%s');",
                                          $tvaid,$a[$i]['tva_id']);
        $array[$i]['javascript'].="removeDiv('search_card');";

    }//foreach

    ob_start();
    require_once NOALYSS_INCLUDE.'/template/card_result.php';
    $r.=ob_get_contents();
    ob_end_clean();
    $ctl=$ctl.'_content';
    $html=$r;
    break;
    case 'action_add_concerned_card':
        require_once NOALYSS_INCLUDE.'/ajax_add_concerned_card.php';
        return;
    break;
    case 'action_save_concerned':
        require 'ajax_action_save_concerned.php';
        return;
    case 'action_remove_concerned':
        require 'ajax_action_remove_concerned.php';
        return;
case 'ac':
    if ( $g_user->check_action(FICCAT)==1 )
    {

        /*----------------------------------------------------------------------
         * Add a category, display first the form
         *
         *----------------------------------------------------------------------*/
        $ipopup=str_replace('_content','',$ctl);
        $msg="";$base="";
        switch($cat)
        {
        case FICHE_TYPE_CLIENT:
            $msg=_(' de clients');
            $base=$cn->get_value("select p_value from parm_code where p_code='CUSTOMER'");
            break;
        case FICHE_TYPE_FOURNISSEUR:
            $msg=_(' de fournisseurs');
            $base=$cn->get_value("select p_value from parm_code where p_code='SUPPLIER'");
            break;
        case FICHE_TYPE_ADM_TAX:
            $msg=_(' d\'administration');
            $base='';
            break;
	case FICHE_TYPE_CONTACT:
            $msg=_(' de contacts');
            $base='';
            break;
        case FICHE_TYPE_FIN:
            $msg=_(' Banque');
            $base=$cn->get_value("select p_value from parm_code where p_code='BANQUE'");
            break;
        case FICHE_TYPE_EMPL:
            $msg=_(' Employé ou administrateur');
            $base='';
            break;
         
        }

        $html='';
        /*  show the form */

        $search=new IPoste("class_base");
        $search->size=40;
        $search->value=$base;
        $search->label=_("Recherche poste");
        $search->set_attribute('gDossier',dossier::id());
        $search->set_attribute('account',$search->name);
        $search->set_attribute('ipopup','ipop_account');

	$nom_mod=new IText("nom_mod");
        $str_poste=$search->input();
        $submit=HtmlInput::submit('save',_('Sauve'));
        ob_start();
        require('template/category_of_card.php');
        $html.=ob_get_contents();
        ob_end_clean();

    }
    else
    {
        $html=alert(_('Action interdite'),true);
    }
    break;
case 'scc':
    /*----------------------------------------------------------------------
     * Save card Category into the database and return a ok message
     *
     *----------------------------------------------------------------------*/
    $html='';
    if ( $g_user->check_action(FICCAT) == 1 )
    {
		$script=create_script("removeDiv('$ctl')");
		$html.=$script;
        if ( strlen(trim($_GET['nom_mod'])) != 0 )
        {
            $array=array("FICHE_REF"=>$cat,
                         "nom_mod"=>$_GET['nom_mod'],
                         "class_base"=>$_GET['class_base'],
                          "fd_description"=>$_GET['fd_description']);
            if ( isset ($_POST['create'])) $array['create']=1;
            $catcard=new Fiche_Def($cn);
            if ( $catcard->Add($array) == -1)
                $script="alert_box('"._('Catégorie existe déjà')."')";
            else
                $script="alert_box('"._('Catégorie sauvée')."')";
            $html.=create_script($script);
        }
        else
        {
            $script="alert_box('"._("Le nom ne peut pas être vide")."')";
            $html.=create_script($script);

            $invalid=1;
        }
    }
    else
    {
        $html=alert(_('Action interdite'),true);
    }
    break;
case 'upc':
    $html=HtmlInput::title_box("Détail fiche", $ctl);

  if ( $g_user->check_action(FICADD)==0 )
    {
      $html.=alert(_('Action interdite'),true);
    }
  else
    {
      if ($cn->get_value('select count(*) from fiche where f_id=$1',array($_GET['f_id'])) == '0' )
	{
	  $html.=alert(_('Fiche non valide'),true);
	  }

      else
	{
	  $html=HtmlInput::title_box(_('Détail fiche (sauvée)'),$ctl);

	  $f=new Fiche($cn,$_GET['f_id']);
	  ob_start();
	  $f->update($_GET);
	  $html.=ob_get_contents();
	  ob_end_clean();
	  $html.=$f->Display(true);
	}
      }
} // switch
$xml=escape_xml($html);
if (DEBUG && headers_sent()) {
    echo $html;return;
}
header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$ctl</ctl>
<code>$xml</code>
</data>
EOF;
