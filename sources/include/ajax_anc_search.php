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
 * \brief show a screen to search a ca account
 *
 */

// parameter are gDossier , c1 : the control id to update,
// c2 the control id which contains the pa_id
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_ibutton.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_anc_account.php';
require_once NOALYSS_INCLUDE.'/class_anc_plan.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';

echo HtmlInput::title_box(_("Recherche activité"), $ctl);

//------------- FORM ----------------------------------
echo '<FORM id="anc_search_form" METHOD="GET" onsubmit="search_anc_form(this);return false">';
echo '<span>'._('Recherche').':';

$texte=new IText('plabel');
$texte->value=HtmlInput::default_value('plabel',"", $_GET);
echo $texte->input();
echo '</span>';
echo dossier::hidden();
$hid=new IHidden();
echo $hid->input("c1",$_REQUEST['c1']);
echo $hid->input("c2",$_REQUEST['c2']);
echo $hid->input("go");
echo HtmlInput::submit("go",_("Recherche"));
echo '</form>';
//------------- FORM ----------------------------------
if ( isset($_REQUEST['go']))
{
    $cn=Dossier::connect();
    $plan=new Anc_Plan($cn,$_REQUEST['c2']);
    $plan->pa_id=$_REQUEST['c2'];
    if ( $plan->exist()==false)
        exit(_("Ce plan n'existe pas"));

    $sql="select po_name , po_description from poste_analytique ".
         "where pa_id=$1 and ".
         " (po_name ~* $2 or po_description ~* $3) order by po_name";
    $array=$cn->get_array($sql,array($_REQUEST['c2'],$_REQUEST['plabel'],$_REQUEST['plabel']));

    if (empty($array) == true)
    {
        echo _("Aucun poste trouvé");
        return;
    }
    $button=new IButton();
    $button->name=_("Choix");
    $button->label=_("Choix");

    echo '<table>';
    foreach ($array as $line)
    {
        $button->javascript=sprintf("$('%s').value='%s';removeDiv('%s')",
                                    $_REQUEST['c1'],
                                    $line['po_name'],$ctl);
        echo '<tr>'.
        '<td>'.
        $button->input().
        '</td>'.
        '<td>'.
        h($line['po_name']).
        '</td><td>'.
        h($line['po_description']).
        '</tr>';
    }
    echo '</table>';
}
