<?php
/*
 *   This file is part of NOALYSS.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2018) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');
if (!defined('ALLOWED_ADMIN'))
{
    die(_('Non autorisé'));
}
require_once NOALYSS_INCLUDE.'/class/package_repository.class.php';
require_once NOALYSS_INCLUDE.'/class/extension.class.php';


$package_repository=new Package_Repository();
$xml=$package_repository->getContent();


$a_template=$xml->xpath('//database_template/dbtemplate');
$nb_template=count($a_template);
echo "<table>";
echo tr(
        th(_("Nom")).th(_("Description")).th(_("Mise à jour"))
);
for ($i=0; $i<$nb_template; $i++)
{
    echo '<tr>';
    echo td($a_template[$i]->name);
    echo td($a_template[$i]->description);
    echo td($a_template[$i]->date_update);
    echo '<td id="template'.trim($a_template[$i]->code).'" >';
    $js=sprintf("onclick=\"install_template('%s')\"", trim($a_template[$i]->code));
    echo HtmlInput::button("installTemplate", "Installation modèle", $js);
    echo '</td>';
    echo '</tr>';
}
echo "</table>";
?>
<script>
    function install_template(p_code)
    {
        var task_id = "<?= uniqid() ?>";
        progress_bar_start(task_id);
        new Ajax.Updater("installTemplate" + p_code, "ajax_misc.php", {
                method:"POST",
                parameters:{op:"installTemplate", gDossier:0, code:p_code,task_id:task_id}
        }
        );
    }
</script>