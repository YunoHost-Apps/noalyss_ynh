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
 * \brief included file for managing the predefined operation
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/lib/iselect.class.php';
require_once NOALYSS_INCLUDE.'/lib/icheckbox.class.php';
require_once NOALYSS_INCLUDE.'/lib/ihidden.class.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/class/pre_operation.class.php';
global $http;
/*
 * Value from $_GET or $_REQUEST
 */
$request_jrn=$http->request("jrn","string", -1);
$request_ac=$http->request("ac","string", "");
$request_sa=$http->request("sa","string", "");
$get_jrn=$http->get('jrn',"string",-1);

echo '<div class="content">';
echo '<form method="GET">';
$sel=new ISelect();
$sel->name="jrn";
$sel->value=$cn->make_array("select jrn_def_id,jrn_def_name from ".
                            " jrn_def where jrn_def_type in ('VEN','ACH','ODS') order by jrn_def_name");
// Show a list of ledger
$sel->selected=$request_jrn;
echo 'Choisissez un journal '.$sel->input();

echo dossier::hidden();
$hid=new IHidden();
echo $hid->input("sa","jrn");
echo $hid->input("ac",$request_ac);
echo '<hr>';
echo HtmlInput::submit('Accepter','Accepter');
echo '</form>';

// if $_REQUEST[sa] == del delete the predefined operation
if ( $request_sa == 'del')
{
    $op=new Pre_operation($cn);
    $http=new HttpInput();
    $op->od_id=$http->request('od_id',"string",-1);
    if (isNumber($op->od_id)==1 && $op->od_id != -1 )
    {
        $op->delete();
    }
    $request_sa='jrn';
}

// if $_REQUEST[sa] == jrn show the  predefined operation for this
// ledger
if ( $request_sa== 'jrn' )
{
    $op=new Pre_operation($cn);
    $op->set_jrn($get_jrn);
   $is_ods = $cn->get_value("select count(*)
		from jrn_def where
			jrn_def_id=$1
			and jrn_def_type='ODS'", array($get_jrn));
	$op->od_direct = ($is_ods > 0) ? 't' : 'f';
	$array = $op->get_list_ledger();
	if (empty($array) == true)
    {
        echo _("Aucun enregistrement");
        return;
    }
    echo HtmlInput::filter_table('preod_table', '0', 0);
    echo '<table id="preod_table">';
    $count=0;
    foreach ($array as $row )
    {

      if ( $count %2 == 0 )
            echo '<tr class="odd">';
        else
            echo '<tr class="even">';
      $count++;

        echo '<td>'.h($row['od_name']).'</td>';
        echo '<td>'.h($row['od_description']).'</td>';
        echo '<td>';
	echo '<form method="POST" id="preod_frm'.$row['od_id'].'" class="print" style="margin:0px;padding:0px;">';
        echo dossier::hidden();
        echo HtmlInput::hidden("sa","del");
        echo HtmlInput::hidden("ac",$request_ac);
        echo HtmlInput::hidden("del","");
        echo HtmlInput::hidden("od_id",$row['od_id']);
        echo HtmlInput::hidden("jrn",$get_jrn);

	$b='<input type="submit" class="smallbutton" value="'._("Effacer").'"'.
	  ' onClick="return confirm_box(\'preod_frm'.$row['od_id'].'\',\''._("Voulez-vous vraiment effacer cette operation ?").'\');" >';
	   echo $b;
	   echo '</form>';

        echo '</td>';
	$b=HtmlInput::button('mod'.$row['od_id'],"Modifier","onclick=\"mod_predf_op('".dossier::id()."','".$row['od_id']."');\"");
	echo td($b);
        echo '</tr>';

    }
    echo '</table>';
}
echo '</div>';
?>
