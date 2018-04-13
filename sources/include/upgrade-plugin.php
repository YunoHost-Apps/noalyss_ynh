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

/**
 * @file
 * @brief Install new plugin
 */
$package_repository=new Package_Repository();
$xml=$package_repository->getContent();

$a_plugin=$xml->xpath('//plugins/plugin');
$nb_plugin=count($a_plugin);
echo _("Les extensions doivent être activées dans le dossier après installation");
?>

<table>
    <tr>
        <th>
            <?= _("Nom") ?>
        </th>
        <th>
            <?= _("Description") ?>
        </th>
        <th>
            <?= _("Auteur") ?>
        </th>
        <th>
            <?= _("Code") ?>
        </th>
        <th>
            Installé ou mettre à jour ???
        </th>

    </tr>
    <?php
    for ($i=0; $i<$nb_plugin; $i++)
    {
        ?>
        <tr>
            <td>
                <?= $a_plugin[$i]->name; ?>
            </td>
            <td>
                <?= $a_plugin[$i]->description; ?>
            </td>
            <td>
                <?= $a_plugin[$i]->author; ?>
            </td>
            <td>
                <?= $a_plugin[$i]->code; ?>
                version [<?= $a_plugin[$i]->version; ?>]
            </td>
            <td id="result<?=trim($a_plugin[$i]->code)?>">

                <?php
                //is installed
                if (is_file(NOALYSS_PLUGIN."/".trim($a_plugin[$i]->root)."/plugin.xml"))
                {
                    // plugin is installed take the version and compare with remote one
                    $xml_plugin=$package_repository->read_package_xml(NOALYSS_PLUGIN."/".trim($a_plugin[$i]->root)."/plugin.xml");
                    if (count($xml_plugin->plugin)>1)
                    {
                        echo _("MultiModule");
                    }
                    // Compute js to install or upgrade
                    $js=sprintf("onclick=\"upgradePlugin('%s')\"", trim($a_plugin[$i]->code));
                    // Check if new version is available
                    if (floatval(trim($xml_plugin->plugin->version))<floatval(trim($a_plugin[$i]->version)))
                    {
                        printf (_("Nouvelle version disponible %s , votre version %s"),
                                floatval(trim($a_plugin[$i]->version)),
                                trim($xml_plugin->plugin->version));
                        echo HtmlInput::button("upgrade", _("Mise à jour"), $js);
                    }
                    else
                    {
                        echo _("Dernière version installée");
                    }
                }
                else
                {
                    // It is not installed , propose to install it
                     $js=sprintf("onclick=\"upgradePlugin('%s')\"", trim($a_plugin[$i]->code));
                    echo _("Non installée");
                    echo HtmlInput::button("upgrade", _("Installation"), $js);
                }
                ?>
            </td>
        </tr>
        <?php
    }
    ?>
</table>

<script>
    function upgradePlugin(p_code) {
        var task_id="<?=uniqid()?>";
        progress_bar_start(task_id);
        new Ajax.Updater(
            "result"+p_code,
            "ajax_misc.php" ,
        {
            method:'POST',
            parameters:{gDossier:0,op:'upgradePlugin',code_plugin:p_code,"task_id":task_id}
        });
    }
</script>