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
 * \brief this file will handle all the actions for a specific customer (
 * contact,operation,invoice and financial)
 * include from supplier.inc.php and concerned only the customer card and
 * the customer category
 * parameter
 *  - p_action = supplier
 *  - sb = detail
 *  - sc = dc
 */
//----------------------------------------------------------------------------
// Save modification
//---------------------------------------------------------------------------
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_customer.php';
if ( isset ($_POST['mod']))
{

    // modification is asked
    $f_id=$_REQUEST['f_id'];

    $supplier=new Customer($cn,$f_id);
    $supplier->Save();

}

echo '<div class="u_content">';
$f_id=$_REQUEST['f_id'];
echo '<div class="content" style="width:50%">';
if ( isset($_POST['mod'])) echo hb(_('Information sauvée'));

$supplier=new Fiche($cn,$f_id);
$p_readonly=($g_user->check_action(FICADD)==0)?true:false;
if ( ! $p_readonly) echo '<form id="catergory_detail_frm" method="post">';
echo dossier::hidden();
echo HtmlInput::hidden('sb','detail');
echo HtmlInput::hidden('dc','cc');
echo $supplier->Display($p_readonly);
$w=new IHidden();
$w->name="p_action";
$w->value="supplier";
echo $w->input();
$w->name="f_id";
$w->value=$f_id;
echo $w->input();
echo HtmlInput::hidden('action_fiche','');
if ( ! $p_readonly)
{
	echo HtmlInput::submit('mod',_('Sauver les modifications'),' onclick="$(\'action_fiche\').value=\'mod\';"');
	echo HtmlInput::reset(_("Annuler"));
	echo HtmlInput::submit('delete_card',_('Effacer cette fiche'),'onclick="$(\'action_fiche\').value=\'delete_card\';return confirm_box(\'catergory_detail_frm\',\''.('Confirmer effacement ?').'\');"');
	echo '</form>';
}
echo '</div>';


