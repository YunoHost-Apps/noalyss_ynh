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

if (!defined('ALLOWED'))     die('Appel direct ne sont pas permis');
if ( ! defined ('ALLOWED_ADMIN')) { die (_('Non autorisé'));}

global $version_noalyss;
require_once NOALYSS_INCLUDE.'/class/package_repository.class.php';
/**
 * @file
 * @brief 
 */
printf (_(" La version de votre installation est %s "),$version_noalyss);

$core=new Package_Repository();
$xml=$core->getContent();
printf(h1(_("Version %s du %s")),$xml->core->version,$xml->core->date);
echo '<p>';
echo $xml->core->description;
echo '</p>';

if ( $xml->core->version <= $version_noalyss) {
    echo '<p>';
    echo _("Votre version est à jour");
    echo '</p>';
    return;
}

$js="onclick='UpgradeCore()'";

echo HtmlInput::button("upgrade",_("Mise à jour de votre système"),$js);
?>
<div id="info_admin">
    
</div>
<script>
    function UpgradeCore()
    {
        progress_bar_start('upgradeCore');
        new Ajax.Updater("info_admin","ajax_misc.php",{
                method:'POST',
                parameters:{op:"upgradeCore",gDossier:0,task_id:'upgradeCore'
                }
            }
            );
    }
 </script>