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

/**\file
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
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once  NOALYSS_INCLUDE.'/class_fiche.php';
require_once NOALYSS_INCLUDE.'/class_iradio.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once  NOALYSS_INCLUDE.'/class_user.php';
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
extract($_REQUEST);
set_language();
global $g_user, $cn, $g_parameter;
//
// If database id == 0 then we are not connected to a folder 
// but to the administration
// 
if ($gDossier<>0) {
    $cn = new Database($gDossier);
    $g_parameter=new Own($cn);
    $g_user = new User($cn);
    $g_user->check(true);
    $g_user->check_dossier($gDossier, true);
}
else
{
    // connect to repository
    $cn=new Database(); 
    $g_user = new User($cn);
    $g_user->check(true);
}
$html = var_export($_REQUEST, true);

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
switch ($op)
{
        case 'pcmn_update':
            require 'ajax_pcmn_update.php';
            return;
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
		require_once NOALYSS_INCLUDE.'/class_acc_ledger_fin.php';
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
		require_once NOALYSS_INCLUDE.'/class_acc_ledger_fin.php';
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
		require_once NOALYSS_INCLUDE.'/class_calendar.php';
		/* others report */
		$cal = new Calendar();
		$cal->set_periode($per);
                $notitle=HtmlInput::default_value_get("notitle", 0);
		$html = "";
		$html = $cal->display($_GET['t'],$notitle);
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
		require_once NOALYSS_INCLUDE.'/class_document_type.php';
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
		require_once NOALYSS_INCLUDE.'/template/document_mod_change.php';
		break;
	case 'dsp_tva':
		$cn = new Database($gDossier);
		$Res = $cn->exec_sql("select * from tva_rate order by tva_rate desc");
		$Max = Database::num_row($Res);
		$r = "";
		$r = HtmlInput::anchor_close('tva_select');
		$r.=h2(_('Choisissez la TVA '),'class="title"');
		$r.='<div >';
                $r.=_('Filter')." ".HtmlInput::filter_table("tva_select_table",'0,1,2,3' , 1);
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
		$cn = new Database($gDossier);
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
	/**
	 * display the lettering
	 */
	case 'dl':
		require_once NOALYSS_INCLUDE.'/class_lettering.php';
		$exercice = $g_user->get_exercice();
		if ($g_user->check_module("LETCARD") == 0 && $g_user->check_module("LETACC") == 0)
			exit();
		$periode = new Periode($cn);
		list($first_per, $last_per) = $periode->get_limit($exercice);

		$ret = new IButton('return');
		$ret->label = _('Retour');
		$ret->javascript = "$('detail').hide();$('list').show();$('search').show();";

		// retrieve info for the given j_id (date, amount,side and comment)
		$sql = "select j_date,to_char(j_date,'DD.MM.YYYY') as j_date_fmt,J_POSTE,j_qcode,jr_id,
         jr_comment,j_montant, j_debit,jr_internal from jrnx join jrn on (j_grpt=jr_grpt_id)
         where j_id=$1";
		$arow = $cn->get_array($sql, array($j_id));
		$row = $arow[0];
		$r = '';
		$r.='<fieldset><legend>' . _('Lettrage') . '</legend>';
		$r.=_('Poste')." " . $row['j_poste'] . '  ' . $row['j_qcode'] . '<br>';

		$detail = "<A class=\"detail\" style=\"display:inline\" HREF=\"javascript:modifyOperation('" . $row['jr_id'] . "'," . $gDossier . ")\" > " . $row['jr_internal'] . "</A>";

		$r.=_('Date').' : ' . $row['j_date_fmt'] . ' ref :' . $detail . ' <br>  ';
		$r.=h($row['jr_comment']) ." ". _("montant")." : " . ($row['j_montant']) . " " . (($row['j_debit'] == 't') ? 'D' : 'C');
		$r.='</fieldset>';
		$r.='<div id="filtre" style="float:left;display:block">';
		$r.='<form method="get" id="search_form" onsubmit="search_letter(this);return false">';
		$r.='<div style="float:left;">';
		// needed hidden var
		$r.=dossier::hidden();
		if (isset($_REQUEST['ac']))
			$r.=HtmlInput::hidden('ac', $_REQUEST['ac']);
		if (isset($_REQUEST['sa']))
			$r.=HtmlInput::hidden('sa', $_REQUEST['sa']);
		if (isset($_REQUEST['acc']))
			$r.=HtmlInput::hidden('acc', $_REQUEST['acc']);
		$r.=HtmlInput::hidden('j_id', $j_id);
		$r.=HtmlInput::hidden('op', $op);
		$r.=HtmlInput::hidden('ot', $ot);

		$r.='<table>';
		//min amount
		$line = td(_('Montant min. '));
		$min = new INum('min_amount');
		$min->value = (isset($min_amount)) ? $min_amount : $row['j_montant'];
		$min_amount = (isset($min_amount)) ? $min_amount : $row['j_montant'];

		$line.=td($min->input());
		// max amount
		$line.=td(_('Montant max. '));
		$max = new INum('max_amount');
		$max->value = (isset($max_amount)) ? $max_amount : $row['j_montant'];
		$max_amount = (isset($max_amount)) ? $max_amount : $row['j_montant'];
		$line.=td($max->input());
		$r.=tr($line);

		$date_error="";
		// start date
		$start = new IDate('search_start');

		/*  check if date are valid */
		if (isset($search_start) && isDate($search_start) == null)
		{
			ob_start();
			alert(_('Date malformée'));
			$date_error = ob_get_contents();
			ob_end_clean();
			$search_start=$first_per->first_day();
		}
		$start->value = (isset($search_start)) ? $search_start : $first_per->first_day();

		$line = td('Date Debut') . td($start->input());
		// end date
		$end = new IDate('search_end');
						/*  check if date are valid */
		if (isset($search_end) && isDate($search_end) == null)
		{
			ob_start();
			alert(_('Date malformée'));
			$date_error = ob_get_contents();
			ob_end_clean();
			$search_end=$last_per->last_day();
		}
		$end->value = (isset($search_end)) ? $search_end : $last_per->last_day();
		$line.=td(_('Date Fin')) . td($end->input());
		$r.=tr($line);
		// Side
		$line = td(_('Debit / Credit'));
		$iside = new ISelect('side');
		$iside->value = array(
			array('label' => _('Debit'), 'value' => 0),
			array('label' => _('Credit'), 'value' => 1),
			array('label' => _('Les 2'), 'value' => 3)
		);
		/**
		 *
		 * if $side is not then
		 * - if jl_id exist and is > 0 show by default all the operation (=3)
		 * - if jl_id does not exist or is < 0 then show by default the opposite
		 *  side
		 */
		if (!isset($side))
		{
			// find the jl_id of the j_id
			$jl_id = $cn->get_value('select comptaproc.get_letter_jnt($1)', array($j_id));
			if ($jl_id == null)
			{
				// get the other side
				$iside->selected = (isset($side)) ? $side : (($row['j_debit'] == 't') ? 1 : 0);
				$side = (isset($side)) ? $side : (($row['j_debit'] == 't') ? 1 : 0);
			}
			else
			{
				// show everything
				$iside->selected = 3;
				$side = 3;
			}
		}
		else
		{
			$iside->selected = $side;
		}

		$r.=tr($line . td($iside->input()));
		$r.='</table>';
		$r.='</div>';
		$r.='<div style="float:left;padding-left:100">';
		$r.=HtmlInput::submit('search', 'Rechercher');
		$r.='</div>';
		$r.='</form>';
		$r.='</div>';

		$form = '<div id="result" style="float:top;clear:both">';

		$form.='<FORM id="letter_form" METHOD="post">';
		$form.=dossier::hidden();
		if (isset($_REQUEST['p_action']))
			$form.=HtmlInput::hidden('p_action', $_REQUEST['p_action']);
		if (isset($_REQUEST['sa']))
			$form.=HtmlInput::hidden('sa', $_REQUEST['sa']);
		if (isset($_REQUEST['acc']))
			$form.=HtmlInput::hidden('acc', $_REQUEST['acc']);
		if (isset($_REQUEST['sc']))
			$form.=HtmlInput::hidden('sc', $_REQUEST['sc']);
		if (isset($_REQUEST['sb']))
			$form.=HtmlInput::hidden('sb', $_REQUEST['sb']);
		if (isset($_REQUEST['f_id']))
			$form.=HtmlInput::hidden('f_id', $_REQUEST['f_id']);


		// display a list of operation from the other side + box button
		if ($ot == 'account')
		{
			$obj = new Lettering_Account($cn, $row['j_poste']);
			if (isset($search_start))
				$obj->start = $search_start;
			if (isset($search_end))
				$obj->end = $search_end;
			if (isset($max_amount))
				$obj->fil_amount_max = $max_amount;
			if (isset($min_amount))
				$obj->fil_amount_min = $min_amount;
			if (isset($side))
				$obj->fil_deb = $side;

			$form.=$obj->show_letter($j_id);
		}
		else if ($ot == 'card')
		{
			$obj = new Lettering_Card($cn, $row['j_qcode']);
			if (isset($search_start))
				$obj->start = $search_start;
			if (isset($search_end))
				$obj->end = $search_end;
			if (isset($max_amount))
				$obj->fil_amount_max = $max_amount;
			if (isset($min_amount))
				$obj->fil_amount_min = $min_amount;
			if (isset($side))
				$obj->fil_deb = $side;
			$form.=$obj->show_letter($j_id);
		}
		else
		{
			$form.=_('Mauvais type objet');
		}

		$form.=HtmlInput::submit('record', _('Sauver')) . $ret->input();
		$form.='</FORM>';
		$form.='</div>';

		$html = $r . $form;
		$html.=$date_error;
		//       echo $html;exit;
		$html = escape_xml($html);

		header('Content-type: text/xml; charset=UTF-8');
		echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<code>detail</code>
<value>$html</value>
</data>
EOF;
		break;
	case 'mod_doc':
		require_once NOALYSS_INCLUDE.'/ajax_mod_document.php';
		break;
	case 'input_per':
		require_once NOALYSS_INCLUDE.'/ajax_mod_periode.php';
		break;
	case 'save_per':
		require_once NOALYSS_INCLUDE.'/ajax_mod_periode.php';
		break;
	case 'mod_predf':
		require_once NOALYSS_INCLUDE.'/ajax_mod_predf_op.php';
		break;
	case 'save_predf':
		require_once NOALYSS_INCLUDE.'/ajax_save_predf_op.php';
		break;
	case 'search_op':
		require_once NOALYSS_INCLUDE.'/search.inc.php';
		break;
	case 'search_action':
		require_once NOALYSS_INCLUDE.'/ajax_search_action.php';
		break;
	case 'display_profile':
		require_once NOALYSS_INCLUDE.'/ajax_get_profile.php';
		break;
	case 'det_menu':
		require_once NOALYSS_INCLUDE.'/ajax_get_menu_detail.php';
		break;
	case 'add_menu':
		require_once NOALYSS_INCLUDE.'/ajax_add_menu.php';
		break;
        case 'display_submenu':
                require_once NOALYSS_INCLUDE.'/ajax_display_submenu.php';
                break;
        case 'remove_submenu':
                require_once NOALYSS_INCLUDE.'/ajax_remove_submenu.php';
                break;
	case 'cardsearch':
		require_once NOALYSS_INCLUDE.'/ajax_boxcard_search.php';
		break;
	case 'add_plugin':
		$me_code = new IText('me_code');
		$me_file = new IText('me_file');
		$me_menu = new IText('me_menu');
		$me_description = new IText("me_description");
		$me_parameter = new IText("me_parameter");
		$new = true;
		require_once NOALYSS_INCLUDE.'/ajax_plugin_detail.php';
		break;
	case 'mod_plugin':
		$m = $cn->get_array("select me_code,me_file,me_menu,me_description,me_parameter
			from menu_ref where me_code=$1", array($me_code));
		if (empty($m))
		{
			echo HtmlInput::title_box("Ce plugin n'existe pas ", $ctl);
			echo "<p>Il y a une erreur, ce plugin n'existe pas";
			exit;
		}
		$me_code = new IText('me_code', $m[0] ['me_code']);
		$me_file = new IText('me_file', $m[0] ['me_file']);
		$me_menu = new IText('me_menu', $m[0] ['me_menu']);
		$me_description = new IText("me_description", $m[0] ['me_description']);
		$me_parameter = new IText("me_parameter", $m[0] ['me_parameter']);
		$new = false;
		require_once NOALYSS_INCLUDE.'/ajax_plugin_detail.php';
		break;
	case 'saldo':
		require_once NOALYSS_INCLUDE.'/ajax_bank_saldo.php';
		break;
	case 'up_predef':
		require_once NOALYSS_INCLUDE.'/ajax_update_predef.php';
		break;
	case 'upd_receipt':
		require_once NOALYSS_INCLUDE.'/ajax_get_receipt.php';
		break;
	case 'up_pay_method':
		require_once NOALYSS_INCLUDE.'/ajax_update_payment.php';
		break;
	case 'openancsearch':
	case 'resultancsearch':
		require_once NOALYSS_INCLUDE.'/ajax_anc_search.php';
		break;
	case 'autoanc':
		require_once NOALYSS_INCLUDE.'/ajax_auto_anc_card.php';
		break;
	case 'create_menu';
		require_once NOALYSS_INCLUDE.'/ajax_create_menu.php';
		break;
	case 'modify_menu';
		require_once NOALYSS_INCLUDE.'/ajax_mod_menu.php';
		break;
	case 'mod_stock_repo':
		require_once NOALYSS_INCLUDE.'/ajax_mod_stock_repo.php';
		break;
	case 'view_mod_stock':
		require_once NOALYSS_INCLUDE.'/ajax_view_mod_stock.php';
		break;
	case 'fddetail':
		require_once NOALYSS_INCLUDE.'/ajax_fiche_def_detail.php';
		break;
	case 'vw_action':
		require_once NOALYSS_INCLUDE.'/ajax_view_action.php';
		break;
	case 'minrow':
		require_once NOALYSS_INCLUDE.'/ajax_min_row.php';
		break;
        case 'navigator':
                require_once NOALYSS_INCLUDE.'/ajax_navigator.php';
                break;
        case 'preference':
                require_once NOALYSS_INCLUDE.'/ajax_preference.php';
                break;
        case 'bookmark':
            require_once NOALYSS_INCLUDE.'/ajax_bookmark.php';
            break;
        case 'tag_detail':
            require_once NOALYSS_INCLUDE.'/ajax_tag_detail.php';
            break;
        case 'tag_save':
            require_once NOALYSS_INCLUDE.'/ajax_tag_save.php';
            break;
        case 'tag_list':
            require_once NOALYSS_INCLUDE.'/ajax_tag_list.php';
            break;
        case 'tag_add':
            require_once NOALYSS_INCLUDE.'/ajax_tag_add_action.php';
            break;
        case 'tag_remove':
            require_once NOALYSS_INCLUDE.'/ajax_tag_remove_action.php';
            break;
        case 'tag_choose':
            require_once NOALYSS_INCLUDE.'/ajax_tag_choose.php';
            break;
        case 'tag_choose':
            require_once NOALYSS_INCLUDE.'/ajax_tag_choose.php';
            break;
       case 'search_display_tag':
            require_once NOALYSS_INCLUDE.'/ajax_search_display_tag.php';
            break;
        case 'search_add_tag':
            require_once NOALYSS_INCLUDE.'/ajax_search_add_tag.php';
            break;
        case 'search_clear_tag':
            require_once NOALYSS_INCLUDE.'/ajax_search_clear_tag.php';
            break;
        case 'calendar_zoom':
            require_once NOALYSS_INCLUDE.'/ajax_calendar_zoom.php';
            break;
        case 'ledger_show':
            require_once NOALYSS_INCLUDE.'/ajax_ledger_show.php';
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
        
        case 'anc_key_choice': 
            /*
             *  Show the available distribution keys for analytic 
             */
            require_once NOALYSS_INCLUDE.'/ajax_anc_key_choice.php';
            break;
        case 'anc_key_compute':
            /* 
             * Show the activities computed with the selected distribution key 
             */
            require_once NOALYSS_INCLUDE.'/ajax_anc_key_compute.php';
            break;
        case 'account_update':
            /**
             * update an accounting (from CFGPCMN)
             */
            require_once NOALYSS_INCLUDE.'/ajax_account_update.php';
            break;
        // From admin, revoke the access to a folder from an
        // user
        case 'folder_remove':
            require_once NOALYSS_INCLUDE.'/ajax_admin.php';
            break;
        // From admin, display a list of folder to which the user has 
        // no access
        case 'folder_display':
            require_once NOALYSS_INCLUDE.'/ajax_admin.php';
            break;
        // From admin, grant the access to a folder to an
        // user
        case 'folder_add':
            require_once NOALYSS_INCLUDE.'/ajax_admin.php';
            break;

        // From admin, display info and propose to drop the folder
        case 'folder_drop':
            require_once NOALYSS_INCLUDE.'/ajax_admin.php';
            break;

        // From admin, display the information of a folder you can 
        // modify
        case 'folder_modify':
            require_once NOALYSS_INCLUDE.'/ajax_admin.php';
            break;
        // From admin, display info and propose to drop the template
        case 'modele_drop':
            require_once NOALYSS_INCLUDE.'/ajax_admin.php';
            break;

        // From admin, display the information of a template you can 
        // modify
        case 'modele_modify':
            require_once NOALYSS_INCLUDE.'/ajax_admin.php';
            break;
        // From dashboard, display detail about last operation 
        case 'action_show':
            require_once NOALYSS_INCLUDE.'/ajax_gestion.php';
            break;
        // From dashboard, display form for a new event
        case 'action_add':
            require_once NOALYSS_INCLUDE.'/ajax_gestion.php';
            break;
        // Save a event given in the short form
        case 'action_save':
            require_once NOALYSS_INCLUDE.'/ajax_gestion.php';
            break;
	default:
		var_dump($_GET);
}
