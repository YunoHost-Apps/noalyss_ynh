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
 * \brief p_action contains the main action (always poste here)
 *  action contains the sub action 
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_acc_parm_code.php';
echo '<div class="content">';

$gDossier=dossier::id();

//-----------------------------------------------------
// confirm mod
if ( isset( $_POST['confirm_mod'] ) )
{
    extract($_POST);
    $update=new Acc_Parm_Code($cn,$p_code);
    $update->p_comment=$p_comment;
    $update->p_value=$p_value;
    $update->save();
}
$object=new Acc_Parm_Code($cn);

$all=$object->load_all();
echo '<div style="float:left; ">';
echo '<table align="left">';
for ($i=0;$i<sizeof($all);$i++)
{
    echo '<TR>';
    echo $all[$i]->display();
    echo '<TD><FORM method="POST">';
    $w=new IHidden();
    $w->name='id';
    $w->value=$i;
    echo $w->input();
    echo HtmlInput::submit('mod','modifie');
    echo '</FORM>';
    echo '</TD>';
    echo "</TR>";
}
echo "</table>";
echo "</div>";
//-----------------------------------------------------
// modifie
if ( isset ($_POST['mod'] ))
{
    echo '<div style="float:left;">';
    echo IPoste::ipopup('ipop_account');
    echo '<fieldset>';
    echo "<legend>Voulez-vous vraiment modifier ?</legend>";
    echo '<FORM METHOD="POST">';

    echo "<TABLE>";
    $id=$_POST['id'];
    echo $all[$id]->form();
    echo "</TABLE>";
    $h=new IHidden();
    $h->name='p_action';
    $h->value='divers';
    ;
    echo $h->input();
    echo HtmlInput::hidden('sa','poste');
    echo HtmlInput::submit('confirm_mod','Confirme');
    echo HtmlInput::submit('no','Cancel');
    echo "</FORM>";
    echo '</fieldset>';
    echo "</div>";

}
echo '</div>';
