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

/**
 * \file
 * \brief this file respond to an ajax request
 * The parameters are
 * - gDossier
 * - $op operation the file has to execute
 * Part 1
 * dsp_tva fill a ipopup with a choice of possible VAT
 *     - if code is set then fill the field code
 *     - if compute is set then add event to call clean_tva and compute_ledger
  @see Acc_Ledger_Sold::input
 * Part 2
 * dl : display form to modify, add and delete lettering for a given operation
 *
 */
if ( ! defined('ALLOWED')) define ('ALLOWED',1);

require_once '../include/constant.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once  NOALYSS_INCLUDE.'/class/fiche.class.php';
require_once NOALYSS_INCLUDE.'/lib/iradio.class.php';
require_once NOALYSS_INCLUDE.'/lib/function_javascript.php';
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once  NOALYSS_INCLUDE.'/class/user.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
require_once NOALYSS_INCLUDE.'/lib/icon_action.class.php';
require_once NOALYSS_INCLUDE.'/lib/progress_bar.class.php';
$http=new HttpInput();

mb_internal_encoding("UTF-8");

$var = array('gDossier', 'op');
$cont = 0;
/*  check if mandatory parameters are given */
foreach ($var as $v)
{
	if (!isset($_REQUEST [$v]))
	{
		echo "$v is not set ";
		$cont = 1;
	}
}
if ($cont != 0)
	exit();
extract($_REQUEST, EXTR_SKIP );
if ( isset($div)) ajax_disconnected($div);
global $g_user, $cn, $g_parameter;
//
// If database id == 0 then we are not connected to a folder 
// but to the administration
// 
if ($gDossier<>0) {
    $cn =Dossier::connect();
    $g_parameter=new Noalyss_Parameter_Folder($cn);
    $g_user = new User($cn);
    $g_user->check(true);
    if ( $g_user->check_dossier($gDossier, true) == 'X' ) {
        die(_('Non autorisé'));
    }
}
else
{
    // connect to repository
    $cn=new Database(); 
    $g_user = new User($cn);
    $g_user->check(true);
}

// For progress bar, for saving time , we check and answer directly
if ($op == "progressBar") {
    $task_id=$http->request("task_id");
    $task=new Progress_Bar($task_id);
    $task->answer();
    return;
}


$html = var_export($_REQUEST, true);
set_language();
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
$path = array(
    // search accounting , detail ...
    "account"=>"ajax_poste",
    // display card detail :possible to update or add
    "card"=>"ajax_card",
    "ledger"=>"ajax_ledger",
    // Manage ledger access
    "ledger_access"=>"ajax_user_security",
    // Manage user profile
    "profile"=>"ajax_user_security",
    // enable or not the security on ledger
    "user_sec_ledger"=>"ajax_user_security",
    // enable or not the security on action
    "user_sec_action"=>"ajax_user_security",
    // Update in once all the ledgers
    "ledger_access_all"=>"ajax_user_security",
    // From the page CFGSEC,set the actions
    "action_access"=>"ajax_user_security",
    // From the page CFGSEC,set all the actions
    "action_access_all"=>"ajax_user_security",
    "todo_list"=>"ajax_todo_list",
    // Writing operation History for a card or an accounting
    "history"=>"ajax_history",
    "mod_doc"=>"ajax_mod_document",
    // Periode menu: PERIODE
    'periode'=>"ajax_periode",
    "mod_predf"=>"ajax_mod_predf_op",
    "save_predf"=>"ajax_save_predf_op",
    "search_action"=>"ajax_search_action",
    "display_profile"=>"ajax_get_profile",
    "det_menu"=>"ajax_get_menu_detail",
    "add_menu"=>"ajax_add_menu",
    "display_submenu"=>"ajax_display_submenu",
    "remove_submenu"=>"ajax_remove_submenu",
    "cardsearch"=>"ajax_boxcard_search",
    "saldo"=>"ajax_bank_saldo",
    "up_predef"=>"ajax_update_predef",
    "upd_receipt"=>"ajax_get_receipt",
    "up_pay_method"=>"ajax_update_payment",
    "openancsearch"=>"ajax_anc_search",
    "resultancsearch"=>"ajax_anc_search",
    "autoanc"=>"ajax_auto_anc_card",
    "create_menu"=>"ajax_create_menu",
    "modify_menu"=>"ajax_mod_menu",
    "mod_stock_repo"=>"ajax_mod_stock_repo",
    "view_mod_stock"=>"ajax_view_mod_stock",
    "fddetail"=>"ajax_fiche_def_detail",
    "vw_action"=>"ajax_view_action",
    "minrow"=>"ajax_min_row",
    "navigator"=>"ajax_navigator",
    "preference"=>"ajax_preference",
    "bookmark"=>"ajax_bookmark",
    // Tag 
    "tag_detail"=>"ajax_tag_detail",
    "tag_save"=>"ajax_tag_save",
    "tag_list"=>"ajax_tag_list",
    "tag_add"=>"ajax_tag_add_action",
    "tag_remove"=>"ajax_tag_remove_action",
    "tag_choose"=>"ajax_tag_choose",
    "tag_activate"=>"ajax_tag_save",
    // search
    "search_display_tag"=>"ajax_search_display_tag",
    "search_add_tag"=>"ajax_search_add_tag",
    "search_clear_tag"=>"ajax_search_clear_tag",
    "calendar_zoom"=>"ajax_calendar_zoom",
    "ledger_show"=>"ajax_ledger_show",
  //Show the available distribution keys for analytic 
    "anc_key_choice"=>"ajax_anc_key_choice" ,
  // Show the activities computed with the selected distribution key 
    "anc_key_compute"=>"ajax_anc_key_compute" ,
  //From admin, revoke the access to a folder from an user
    "folder_remove"=>"ajax_admin",
  //From admin, display a list of folder to which the user has no access
    "folder_display"=>"ajax_admin",
  // From admin, grant the access to a folder to an
  // user
    "folder_add"=>"ajax_admin",
  // From admin, display info and propose to drop the folder
    "folder_drop"=>"ajax_admin",
  // From admin, display the information of a folder you can 
  // modify
    "folder_modify"=>"ajax_admin",
  // From admin, display info and propose to drop the template
    "modele_drop"=>"ajax_admin",
  // From admin, display the information of a template you can modify
    "modele_modify"=>"ajax_admin",
    // From admin , upgrade Noalyss
    "upgradeCore"=>"ajax_admin",
    // From admin , upgrade or install plugin
    "upgradePlugin"=>"ajax_admin",
    // From admin , install a template
    "installTemplate"=>"ajax_admin",
  // From dashboard, display detail about last operation     
    "action_show"=>"ajax_gestion",
  // From dashboard, display form for a new event    
    "action_add"=>"ajax_gestion",
  // Save a event given in the short form
    "action_save"=>"ajax_gestion",
    /* display the lettering , callebd from acc_ledger : dsp_letter*/
    "dl"=>"ajax_display_letter",
    // Add , delete update anc accounting
    "anc_accounting"=>"ajax_anc_accounting",
    // Update name and description
    "anc_updatedescription"=>"ajax_anc_plan",
    // Update, insert or delete accounting frmo CFGPCMN
    "accounting"=>"ajax_accounting",
    // Show detail of an ANC operation
    "anc_detail_op"=>"ajax_anc_detail_operation",
    // show history of an analytic account
    "history_anc_account"=>"ajax_history_anc_account",
    // Display the list of filter saved
    "display_search_filter"=>"ajax_search_filter",
    // Save search filter 
    "save_filter"=>"ajax_search_filter",
    // Load a search filter
    "load_filter"=>"ajax_search_filter",
    // search operation to reconcile
    	'search_op'=>'ajax_search_operation',
    // delete operation
    	'delete_search_operation'=>'ajax_search_filter',
    // template category of card
    'template_cat_card'=>'ajax_template_cat_card',
    // Attribute for category of card
    'template_cat_category'=>'ajax_template_cat_category',
    // From FollowUp , update a comment on a file
    'update_comment_followUp'=>'ajax_follow_up',
    // TVA param
    "tva_parameter"=>"ajax_tva_parameter"
)    ;

if (array_key_exists($op, $path)) {
    require NOALYSS_INCLUDE.'/ajax/'.$path[$op].".php";
    return;
}
switch ($op)
{
    case "periode_change":
        $field=$http->get("field");
        $type=$http->get("type");
        $exercice=$http->get("exercice","number");
        $last=$http->get("last","number");
        
        // if last == 1 then show first and last periode of the 
        // exercice
        $periode_start=0;
        $periode_end=0;
        if ( $last==1) {
            $t_periode=new Periode($cn);
            list($per_max,$per_min)=$t_periode->get_limit($exercice);
            $periode_start=$per_max->p_id;
            $periode_end=$per_min->p_id;
        }
        
        $iperiod = new IPeriod($field);
        $iperiod->id=$field;
        $iperiod->user = $g_user;
        $iperiod->cn = $cn;
        $iperiod->filter_year = true;
        $iperiod->exercice=$exercice;
        if ( $type=="from")
        {
            $iperiod->show_end_date=FALSE;
            $iperiod->value=$periode_start;
        } elseif ($type=="to"){
            $iperiod->show_start_date=FALSE;
            $iperiod->value=$periode_end;
            
        } else {
            throw new Exception(_("Invalide type"));
        }
        
        $iperiod->type = ALL;
        echo $iperiod->input();
        
        return;
        
        break;
    case "pref_exercice":
        $iperiod = new IPeriod("period");
        $iperiod->id="setting_period";
        $iperiod->user = $g_user;
        $iperiod->cn = $cn;
        $iperiod->filter_year = true;
        $iperiod->exercice=$http->get("exercice");
        
        $iperiod->type = ALL;
        echo $iperiod->input();
        
        return;
    break;
	case "remove_anc":
		if ($g_user->check_module('ANCODS') == 0)
			exit();
		$cn->exec_sql("delete from operation_analytique where oa_group=$1", array($_GET['oa']));
		break;
	case "rm_stock":
		if ($g_user->check_module('STOCK') == 0)
			exit();
		require_once NOALYSS_INCLUDE.'/constant.security.php';
		$cn->exec_sql('delete from stock_goods where sg_id=$1', array($s_id));
		$html = escape_xml($s_id);
		header('Content-type: text/xml; charset=UTF-8');
		printf('{"d_id":"%s"}', $s_id);
		exit();
		break;
	//--------------------------------------------------
	// get the last date of a ledger
	case 'lastdate':
		require_once NOALYSS_INCLUDE.'/class/acc_ledger_fin.class.php';
		$ledger = new Acc_Ledger_Fin($cn, $_GET['p_jrn']);
		$html = $ledger->get_last_date();
		$html = escape_xml($html);
		header('Content-type: text/xml; charset=UTF-8');
		echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>e_date</code>
<value>$html</value>
</data>
EOF;

		break;
	case 'bkname':
		require_once NOALYSS_INCLUDE.'/class/acc_ledger_fin.class.php';
		$ledger = new Acc_Ledger_Fin($cn, $_GET['p_jrn']);
		$html = $ledger->get_bank_name();
		$html = escape_xml($html);
		header('Content-type: text/xml; charset=UTF-8');
		echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>bkname</code>
<value>$html</value>
</data>
EOF;
		break;
	// display new calendar
	case 'cal':
		require_once NOALYSS_INCLUDE.'/class/calendar.class.php';
		/* others report */
		$cal = new Calendar();
		$cal->set_periode($per);
                $notitle=$http->get("notitle", "string",0);
		$html = "";
		$html = $cal->display($http->get('t'),$notitle);
		$html = escape_xml($html);
		header('Content-type: text/xml; charset=UTF-8');
		echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>$html</code>
</data>
EOF;
		break;
	/* rem a cat of document */
	case 'rem_cat_doc':
		require_once NOALYSS_INCLUDE.'/class/document_type.class.php';
		// if user can not return error message
                $message="";
		if ($g_user->check_action(PARCATDOC) == 0)
		{
			$html = "nok";
                        $message=_('Action non autorisée');
			header('Content-type: text/xml; charset=UTF-8');
			echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<dtid>$html</dtid>
<message>$message</message>                                
</data>
EOF;
			return;
		}
		// remove the cat if no action
		$count_md = $cn->get_value('select count(*) from document_modele where md_type=$1', array($dt_id));
		$count_a = $cn->get_value('select count(*) from action_gestion where ag_type=$1', array($dt_id));

		if ($count_md != 0 || $count_a != 0)
		{
                    $message=_('Des actions dépendent de cette catégorie');
			$html = "nok";
			header('Content-type: text/xml; charset=UTF-8');
			echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<dtid>$html</dtid>
<message>$message</message>                                
</data>
EOF;
			exit;
		}
		$cn->exec_sql('delete from document_type where dt_id=$1', array($dt_id));
		$html = $dt_id;
		header('Content-type: text/xml; charset=UTF-8');
		echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<dtid>$html</dtid>
<message>$message</message>                                
</data>
EOF;
		return;
		break;
	case 'mod_cat_doc':
		require_once NOALYSS_TEMPLATE.'/document_mod_change.php';
		break;
	case 'dsp_tva':
		$cn = Dossier::connect();
            // Filter the VAT 
                $filter=$http->get("filter","string","none");
                if ( $filter == 'sale')  {
                    $Res = $cn->exec_sql("select * 
                        from v_tva_rate 
                        where 
                        tva_sale <> '#'
                            order by tva_rate desc");
                    
                } elseif ($filter == "purchase") {
                    
                    $Res = $cn->exec_sql("select * 
                        from 
                        v_tva_rate 
                        where 
                        tva_purchase <> '#'
                            order by tva_rate desc");
                }else {
                    
                    $Res = $cn->exec_sql("select * from v_tva_rate 
                            order by tva_rate desc");
                }
		$Max = Database::num_row($Res);
		$r = "";
		$r.=HtmlInput::title_box(_('Choisissez la TVA'),'tva_select',"close","","y");
		$r.='<div >';
                $r.=_('Cherche')." ".HtmlInput::filter_table("tva_select_table",'0,1,2,3' , 1);
		$r.= '<TABLE style="width:100%" id="tva_select_table">';
		$r.=th(_('code'));
		$r.=th(_('Taux'));
		$r.=th(_('Symbole'));
		$r.=th(_('Explication'));

		for ($i = 0; $i < $Max; $i++)
		{
			$row = Database::fetch_array($Res, $i);
			if (!isset($compute))
			{
				if (!isset($code))
				{
					$script = "onclick=\"$('$ctl').value='" . $row['tva_id'] . "';removeDiv('tva_select');\"";
				}
				else
				{
					$script = "onclick=\"$('$ctl').value='" . $row['tva_id'] . "';set_value('$code','" . $row['tva_label'] . "');removeDiv('tva_select');\"";
				}
			}
			else
			{
				if (!isset($code))
				{
					$script = "onclick=\"$('$ctl').value='" . $row['tva_id'] . "';removeDiv('tva_select');clean_tva('$compute');compute_ledger('$compute');\"";
				}
				else
				{
					$script = "onclick=\"$('$ctl').value='" . $row['tva_id'] . "';set_value('$code','" . $row['tva_label'] . "');removeDiv('tva_select');clean_tva('$compute');compute_ledger('$compute');\"";
				}
			}
			$set = '<INPUT TYPE="BUTTON" class="button" Value="select" ' . $script . '>';
			$class=($i%2 == 0)?' class="odd" ':' class="even" ';
			$r.='<tr'.$class. $script.' style="cursor : pointer">';
			$r.=td($row['tva_id']);
			$r.=td($row['tva_rate']);
			$r.=td($row['tva_label']);
			$r.=td($row['tva_comment']);
			$r.='</tr>';
		}
		$r.='</TABLE>';
		$r.='</div>';
                
		$html = escape_xml($r);

		header('Content-type: text/xml; charset=UTF-8');
		echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>$html</code>
<popup>$popup</popup>
</data>
EOF;
		break;
	case 'label_tva':
		$cn =Dossier::connect();
		if (isNumber($id) == 0)
			$value = _('tva inconnue');
		else
		{
			$Res = $cn->get_array("select * from tva_rate where tva_id = $1", array($id));
			if (count($Res) == 0)
				$value = _('tva inconnue');
			else
				$value = $Res[0]['tva_label'];
		}
		header('Content-type: text/xml; charset=UTF-8');
		echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>$code</code>
<value>$value</value>
</data>
EOF;

		break;

	case 'add_plugin':
		$me_code = new IText('me_code');
		$me_file = new IText('me_file');
		$me_menu = new IText('me_menu');
		$me_description = new IText("me_description");
		$me_parameter = new IText("me_parameter");
		$new = true;
		require_once NOALYSS_INCLUDE.'/ajax/ajax_plugin_detail.php';
		break;
	case 'mod_plugin':
		$m = $cn->get_array("select me_code,me_file,me_menu,me_description,me_parameter
			from menu_ref where me_code=$1", array($me_code));
		if (empty($m))
		{
			echo HtmlInput::title_box(_("Ce plugin n'existe pas "), $ctl,"close","","y");
			echo "<p>"._("Il y a une erreur, ce plugin n'existe pas").
                                "</p>";
			exit;
		}
		$me_code = new IText('me_code', $m[0] ['me_code']);
		$me_file = new IText('me_file', $m[0] ['me_file']);
		$me_menu = new IText('me_menu', $m[0] ['me_menu']);
		$me_description = new IText("me_description", $m[0] ['me_description']);
		$me_parameter = new IText("me_parameter", $m[0] ['me_parameter']);
		$new = false;
		require_once NOALYSS_INCLUDE.'/ajax/ajax_plugin_detail.php';
		break;
        case 'ledger_description':
            $ajrn=$cn->get_array('select jrn_def_name,jrn_def_description from jrn_def where jrn_def_id=$1',array($l));
            if ( count($ajrn)==1)
            {
                echo '<div>';
                echo '<h2 id="info">'.$ajrn[0]['jrn_def_name'].'</h2>';
                if ( trim($ajrn[0]['jrn_def_description']) != "") {
                    echo '<p style="border:1px solid;margin-top:0px">'.$ajrn[0]['jrn_def_description'].'</p>';
                }
                echo '</div>';
            }
            exit();
            break;
        
	default:
		var_dump($_REQUEST);
}
