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
require_once NOALYSS_INCLUDE.'/lib/itext.class.php';
require_once NOALYSS_INCLUDE.'/lib/ihidden.class.php';
require_once NOALYSS_INCLUDE.'/lib/ibutton.class.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/class/anc_plan.class.php';
require_once NOALYSS_INCLUDE.'/lib/function_javascript.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

$texte=new IText('plabel');
$texte->value=$http->get('plabel',"string","");

echo HtmlInput::title_box(_("Recherche activité"), $ctl);

//------------- FORM ----------------------------------
echo '<FORM id="anc_search_form" METHOD="GET" onsubmit="search_anc_form(this);return false">';
echo '<span>'._('Recherche').':';

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

    echo '<table>';
    foreach ($array as $line)
    {
        $js=sprintf("onclick=\"$('%s').value='%s';removeDiv('%s')\"",
                                    $_REQUEST['c1'],
                                    $line['po_name'],$ctl);
        
        echo '<tr>'.
        '<td>'.
        HtmlInput::anchor(h($line['po_name']), "", $js).
        '</td>'.
        '<td>'.
        h($line['po_description']).
        '</tr>';
    }
    echo '</table>';
}
