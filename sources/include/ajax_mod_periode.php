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
require_once NOALYSS_INCLUDE.'/class_periode.php';

/**\file
 * \brief display or save a periode
 * variable received $op, $cn $g_user
 */
$err = 0;
$html = '';
/* we check the security */
switch ($op)
{
    case 'input_per':
        $per = new Periode($cn, $_GET['p_id']);
        $per->load();
        $limit = $per->get_date_limit($_GET['p_id']);

        $p_start = new IDate('p_start');
        $p_start->value = $limit['p_start'];
        $p_end = new IDate('p_end');
        $p_end->value = $limit['p_end'];
        $p_exercice = new INum('p_exercice');
        $p_exercice->value = $per->p_exercice;

        $html = '';
        $html.=HtmlInput::anchor_close('mod_periode');
        $html.=h2info(_('Modification période'));
        $html.='<p> '._('Modifier les dates de début et fin de période').'</p>';
        $html.='<p class="notice">'._('Cela pourrait avoir un impact sur les opérations déjà existantes').'</p>';
        $html.='<form method="post" onsubmit="return save_periode(this)">';
        $html.=dossier::hidden();
        $html.='<table>';

        $html.=tr(td(_(' Début période : ')) . td($p_start->input()));
        $html.=tr(td(_(' Fin période : ')) . td($p_end->input()));
        $html.=tr(td(_(' Exercice : ')) . td($p_exercice->input()));
        $html.='</table>';
        $html.=HtmlInput::submit('sauver', _('sauver'));
        $html.=HtmlInput::button('close', _('fermer'), 'onclick="removeDiv(\'mod_periode\')"');
        $html.=HtmlInput::hidden('p_id', $_GET['p_id']);
        $html.='</form>';
        break;
    case 'save_per':
        $per = new Periode($cn, $_POST['p_id']);
        $per->load();
        if (isDate($_POST['p_start']) == null ||
                isDate($_POST['p_end'] == null) ||
                isNumber($_POST['p_exercice']) == 0 ||
                $_POST['p_exercice'] > 2099 ||
                $_POST['p_exercice'] < 2000)
        {
            $html = '';
            $html.=HtmlInput::anchor_close('mod_periode');
            $html.='<h2 class="info">'._('Modifier les dates de début et fin de période').'</h2>';
            $html.="<div class=\"error\">"._('Erreur date invalide')."</div>";

            $html.=HtmlInput::button('close', _('fermer'), 'onclick="removeDiv(\'mod_periode\')"');
        }
        else
        {
            $sql = "update parm_periode set p_start=to_date($1,'DD.MM.YYYY'),p_end=to_date($2,'DD.MM.YYYY'),p_exercice=$3 where p_id=$4";
            try
            {
                $cn->exec_sql($sql, array($_POST['p_start'], $_POST['p_end'], $_POST['p_exercice'], $_POST['p_id']));
                $html = '<h2 class="info"> Modifier les dates de début et fin de période</h2>';
                $html.='<h2 class="notice"> Sauvé </h2>';

                $html.=HtmlInput::button('close', _('Fermer'), 'onclick="	refresh_window();"');
            }
            catch (Exception $e)
            {
                $html = alert($e->getTrace(), true);
            }
        }
        break;
}

$html = escape_xml($html);
header('Content-type: text/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<xml>';
echo '<data>' . $html . '</data>';
echo '</xml>';
