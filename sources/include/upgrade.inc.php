<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS isfree software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS isdistributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>
/**
 * @file
 * @brief Upgrade all the database : the central repository , the templates and
 * the folder
 * @param $rep db connection to central repository
 */
if (!defined('ALLOWED'))     die('Appel direct ne sont pas permis');
if ( ! defined ('ALLOWED_ADMIN')) { die (_('Non autorisé'));}

require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

$menu=array(
    ["?action=upgrade&sb=database", _("Base de données"), _("Met à jour toutes les dossiers et modèles"), 'database'],
    ["?action=upgrade&sb=application", _("Application"), _("Installe la dernière version de Noalyss"), 'application'],
    ["?action=upgrade&sb=plugin", _("Extension"), _("Installe ou met à jour les extensions"), "plugin"],
    ["?action=upgrade&sb=template", _("Modèle"), _("Installe des modèles"), "template"]
);
$sb=$http->request("sb", "string", "application");
echo '<div class="menu2">';
echo ShowItem($menu, "H", "mtitle", "mtitle", $sb);
echo '</div>';

$sc=$http->get("sc", "string", "none");

//-----------------------------------------------------------------------------
// Upgrade Databases (Folder, Template , Account )
//-----------------------------------------------------------------------------
if ($sb=="database")
{
    ?>
<p>
    
<?php
echo _("Mettez vos bases de données à jour pour qu'elles correspondent à cette version de Noalyss");
?>
</p>
    <form method="get" id="frm_upg_all" onsubmit="return confirm_box('frm_upg_all', '<?php echo _('Confirmez') ?>')">
        <input type="hidden" name="sb" value="database">
        <input type="hidden" name="sc" value="upg_all">
        <input type="hidden" name="action" value="upgrade">
        <input type="submit" class="button" name="submit_upg_all" id="submit_upg_all" value="<?php echo _('Tout mettre à jour') ?>">
    </form>

    <?php
    if ($sc==="upg_all"&&(!defined('MULTI')||(defined('MULTI')&&MULTI==1)))
    {
        echo '<div class="content">';

        Dossier::upgrade();

        echo '</div>';
        return;
    }
}
// Import the file with the package
//------------------------------------------------------------------------------
// Upgrade Main application, show all the info from the NOALYSS_PACKAGE site
//------------------------------------------------------------------------------
if ($sb=="application")
{
    require NOALYSS_INCLUDE."/upgrade-core.php";
}
//------------------------------------------------------------------------------
// Install or Upgrade Extension, show all the info from the NOALYSS_PACKAGE site
//------------------------------------------------------------------------------
if ($sb=="plugin")
{
    require NOALYSS_INCLUDE."/upgrade-plugin.php";
    
}
//-------------------------------------------------------------------------------------------------------------------------------
// Install template
//-------------------------------------------------------------------------------------------------------------------------------
if ( $sb == 'template')
{
    require NOALYSS_INCLUDE."/upgrade-template.php";
    
}

?>
