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
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_fiche_def.php';

/*! \file
 * \brief Let customise the fiche_def_ref for the user
 */
echo '<div class="content">';
// record change
if ( isset ($_POST['confirm_mod']))
{
    extract ($_POST);
    $update=new Fiche_Def_Ref($cn);
    $update->frd_id=sql_string($frd_id);
    $update->frd_text=sql_string($frd_text);
    $update->frd_class_base=sql_string($frd_class_base);
    $update->Save();
}
// Load All Fiche_def
$fiche_def=new Fiche_Def_Ref($cn);
$all=$fiche_def->LoadAll();

// Display Them
echo '<table align="left">';
for ($i=0;$i<sizeof($all);$i++)
{
    echo '<TR>';
    echo $all[$i]->Display();
    echo "<TD>";
    echo '<form method="post">';
    $w=new IHidden();
    echo $w->input('idx',$all[$i]->frd_id);
    echo HtmlInput::submit('mod','modifie');
    echo $w->input($_REQUEST['ac'],'ac');
    //echo $w->input($sa,'sa');
    echo "</form>";
    echo "</TD>";
    echo '</TR>';
}
echo "</table>";
// modify input
if ( isset ($_POST['mod']) )
{
    extract ($_POST);
    echo '<div style="float:left;padding:2%">';
    echo _("Voulez-vous modifier ?");
    echo "<br><font color=\"red\"> ";
    echo _("Attention, ne changer pas la signification de ce poste.");
    echo hi(_("par exemple ne pas changer Client par fournisseur"))."<br>";
    echo _("sinon le programme fonctionnera mal, ".
           "utiliser uniquement des chiffres pour la classe de base ou rien")."</font>";

    $mod=new Fiche_Def_Ref($cn);
    $mod->frd_id=$idx;
    $mod->Get();
    echo '<form method="post">';
    echo '<ul style="list-style-type:none"';
    echo $mod->Input();
    echo "</ul>";
    $w=new IHidden();
    echo $w->input('ac',$_REQUEST['ac']);
//    echo $w->input('sa',$sa);
    echo HtmlInput::submit('confirm_mod' ,'Confirme');
    echo HtmlInput::submit('no','Cancel');
    echo '</form>';
    echo '</div>';
}
echo '</div>';
