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
 *
 *
 * \brief to write into the ledgers ODS a new operation
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_pre_op_ods.php';
require_once NOALYSS_INCLUDE.'/class_iconcerned.php';

global $g_user,$g_parameter;
$cn=new Database(dossier::id());

$id_predef = (isset($_REQUEST['p_jrn_predef'])) ? $_REQUEST['p_jrn_predef'] : -1;
$id_ledger = (isset($_REQUEST['p_jrn'])) ? $_REQUEST['p_jrn'] : $id_predef;
$ledger = new Acc_Ledger($cn, $id_ledger);
$first_ledger=$ledger->get_first('ODS');
$ledger->id = ($ledger->id == -1) ? $first_ledger['jrn_def_id'] : $id_ledger;

// check if we can write in the ledger
if ( $g_user->check_jrn($ledger->id)=='X')
{
	alert(_("Vous ne pouvez pas écrire dans ce journal, contacter votre administrateur"));
	return;
}
echo '<div style="position:absolute" class="content">';
echo '<div id="predef_form">';
echo HtmlInput::hidden('p_jrn_predef', $ledger->id);
$op = new Pre_op_ods($cn);
$op->set('ledger', $ledger->id);
$op->set('ledger_type', "ODS");
$op->set('direct', 't');
$url=http_build_query(array('action'=>'use_opd','p_jrn_predef'=>$ledger->id,'ac'=>$_REQUEST['ac'],'gDossier'=>dossier::id()));
echo $op->form_get('do.php?'.$url);

echo '</div>';
echo '<div id="jrn_name_div">';
echo '<h2 id="jrn_name" style="display:inline">' . $ledger->get_name() . '</h2>';
echo '</div>';

// Show the predef operation
// Don't forget the p_jrn
$p_post=$_POST;
if ( isset ($_GET['action']) && ! isset($_POST['correct']))
{
	if ( $_GET['action']=='use_opd')
	{
            // get data from predef. operation
            $op=new Pre_op_advanced($cn);
            $p_post=null;
            if ( isset($_REQUEST['pre_def']) && $_REQUEST['pre_def'] != '')
            {
                $op->set_od_id($_REQUEST['pre_def']);
                $p_post=$op->compute_array();
            }
	}
}
$p_msg=(isset($p_msg))?$p_msg:"";
print '<p class="notice">'.$p_msg.'</p>';
echo '<form method="post"  class="print">';
echo dossier::hidden();
echo HtmlInput::request_to_hidden(array('ac'));

echo $ledger->input($p_post);




echo '<div style="position:absolute;width:40%;right:20px">';
echo '<table class="info_op">'.
 '<tr>'.td(_('Débit')) . '<td id="totalDeb"></td>' .
 td(_('Crédit')) . ' <td id="totalCred"></td>' .
 td(_('Difference')) . ' <td id="totalDiff"></td>';
echo '</table>';
echo '</div>';

$iconcerned=new IConcerned('jrn_concerned');
$iconcerned->amount_id="totalDeb";
echo "Opération rapprochée : ".$iconcerned->input();

echo '<p>';
echo HtmlInput::button('add', _('Ajout d\'une ligne'), 'onClick="quick_writing_add_row()"');
echo HtmlInput::submit('summary', _('Sauvez'));
echo '</p>';

echo '</form>';

echo "<script>checkTotalDirect();</script>";
echo create_script(" update_name()");

if ($g_parameter->MY_DATE_SUGGEST=='Y')
{
	echo create_script(" get_last_date()");
}
echo '</div>';

?>
