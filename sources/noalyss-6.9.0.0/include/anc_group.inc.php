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
 *\brief manage the group
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_anc_group.php';
$r=new Anc_Group($cn);


//----------------------------------------------------------------------
// REMOVE
if ( isset ($_POST['remove']))
{
    if (isset($_POST['ck'] ))
    {
        foreach ($_POST['ck'] as $m )
        {
            $obj=new Anc_Group($cn);
            $obj->ga_id=$m;
            $obj->remove();
        }
    }
}

//----------------------------------------------------------------------
// INSERT
if ( isset($_POST['add']))
{
    $obj=new Anc_Group($cn);
    $obj->get_from_array($_POST);
    echo $obj->insert();
}
$array=$r->myList();

echo '<div class="content" >';
echo '<form method="post">';
echo dossier::hidden();
echo '<table class="result"  >';
echo '<tr> <th>'._("Code")." </th><th>"._("Plan")." </td><th>"._("Description").'</th></tr>';
foreach ($array as $idx=>$m)
{
    echo '<tr>';
    echo '<td>'.h($m->ga_id).'</td>';
    echo '<td>'.h($m->pa_name).'</td>';
    echo '<td>'.h($m->ga_description).'</td>';
    echo '<td> Effacer <input type="Checkbox" name="ck[]" value="'.$m->ga_id.'">'.'</td>';
    echo '</tr>';
}
$w=new IText("ga_id");
$wDesc=new IText("ga_description");
$val_pa_id=$cn->make_array("select pa_id,pa_name from plan_analytique");
$wPa_id=new ISelect("pa_id");
$wPa_id->value=$val_pa_id;

echo "<td>".$w->input()."</td>";
echo "<td>".$wPa_id->input("pa_id")."</td>";
echo "<td>".$wDesc->input("ga_description").
HtmlInput::submit('add',_('Ajouter')).
"</td>";
;

echo '</table>';

echo "<hr>";
echo HtmlInput::submit('remove',_('Effacer'));

echo '</div>';
