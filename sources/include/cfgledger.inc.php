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
 * @file
 *
 * @brief Create, update and delete ledgers
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/user_menu.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';

$gDossier=dossier::id();
global $cn;
$show_menu=1;
$ledger=new Acc_Ledger($cn,-1);
$sa=HtmlInput::default_value("sa","",$_REQUEST);
//////////////////////////////////////////////////////////////////////////
// Perform request action : update
//////////////////////////////////////////////////////////////////////////
$action_frm = HtmlInput::default_value_post('action_frm', '');
if (  $action_frm == 'update')
{
	try
	{
		$ledger->id=$_POST['p_jrn'];
		if ( $ledger->load() == -1) throw new Exception (_('Journal inexistant'));
		$ledger->verify_ledger($_POST);
		$ledger->update($_POST);
                $show_menu=1;
	} catch (Exception $e)
	{
		alert($e->getMessage());
	}
}

//////////////////////////////////////////////////////////////////////////
// Perform request action : delete
//////////////////////////////////////////////////////////////////////////
if ($action_frm == 'delete' )
{
	$ledger->jrn_def_id=$_POST['p_jrn'];
	$ledger->id=$_POST['p_jrn'];
	$ledger->load();
	$name=$ledger->get_name();
	try {
		$ledger->delete_ledger();
		$sa="";
		echo '<div id="jrn_name_div">';
		echo '<h2 id="jrn_name">'.h($name). "  est effacé"."</h2>";
		echo '</div>';
                $show_menu=1;
	}
	catch (Exception $e)
	{
		alert ($e->getMessage());
	}

}

//////////////////////////////////////////////////////////////////////////
// Perform request action : add
//////////////////////////////////////////////////////////////////////////
if (isset($_POST['add']))
{
	try
	{
		$ledger->verify_ledger($_POST);
		$ledger->save_new($_POST);
		$sa="detail";
		$_REQUEST['p_jrn']=$ledger->jrn_def_id;
                $show_menu=1;
	}
	catch (Exception $e)
	{
		alert($e->getMessage());
	}
}





//////////////////////////////////////////////////////////////////////////
//Display detail of ledger
//////////////////////////////////////////////////////////////////////////

switch ($sa)
{
	case 'detail': /* detail of a ledger */
		try
		{
			$ledger->id=$_REQUEST['p_jrn'];
			echo '<div class="content">';
			echo '<form id="cfg_ledger_frm"  method="POST">';
			echo $ledger->display_ledger();
                        echo HtmlInput::hidden('action_frm','');
			echo '<INPUT TYPE="SUBMIT" class="smallbutton" VALUE="'._("Sauve").'" name="update" onClick="$(\'action_frm\').value=\'update\';return confirm_box(\'cfg_ledger_frm\',\'Valider ?\')">
			<INPUT TYPE="RESET" class="smallbutton" VALUE="Reset">
			<INPUT TYPE="submit" class="smallbutton"  name="efface" value="'._("Efface").'" onClick="$(\'action_frm\').value=\'delete\';return confirm_box(\'cfg_ledger_frm\',\'Vous effacez ce journal ?\')">';
                        $href=http_build_query(array('ac'=>$_REQUEST['ac'],'gDossier'=>$_REQUEST['gDossier']));
                        echo '<a style="display:inline" class="smallbutton" href="do.php?'.$href.'">'._('Retour').'</a>';
			echo '</FORM>';
			echo "</div>";
                        $show_menu=0;
		}
		catch (Exception $e)
		{
			alert($e->getMessage());
		}
		break;
	case 'add': /* Add a new ledger */
		echo '<div class="content">';
		echo '<FORM METHOD="POST">';
		$ledger->input_new();
		echo HtmlInput::submit('add',_('Sauver'));
		echo '<INPUT TYPE="RESET" class="smallbutton" VALUE="Reset">';
		echo '</FORM>';
		echo "</DIV>";
                $show_menu=0;
}

//////////////////////////////////////////////////////////////////////////
// Display list of ledgers
//////////////////////////////////////////////////////////////////////////
if ( $show_menu == 1 ) {
    echo '<div class="content">';
    echo $ledger->listing();
    echo '</div>';
}


html_page_stop();



?>
