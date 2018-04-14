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
if ( !defined ('ALLOWED') )  die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief Manage the tags
 *
 */
require_once NOALYSS_INCLUDE.'/class_tag.php';
require_once NOALYSS_INCLUDE.'/class_tool_uos.php';

$tag=new Tag($cn);
$uos=new Tool_Uos('tag');
if ( isset ($_POST['save_tag_sb']))
{
    if ( ! isset ($_POST['remove']))
    {
        try {
            $uos->check();
            $tag->save($_POST);
            $uos->save();
        } catch (Exception $e)
        {
            alert("déjà sauvé");
        }
    } else {
        $tag->remove($_POST);
    }
}
?>
<div style="margin-left:10%;width:80%">
     <p class="notice">
        <?php echo _("Vous pouvez utiliser ceci comme des étiquettes pour marquer des documents ou 
         comme des dossiers pour rassembler des documents. Un document peut appartenir
         à plusieurs dossiers ou avoir plusieurs étiquettes.");?>
     </p>
    <?php
        $tag->show_list();
         $js=sprintf("onclick=\"show_tag('%s','%s','%s','p')\"",Dossier::id(),$_REQUEST['ac'],'-1');
        echo HtmlInput::button("tag_add", "Ajout d'un tag", $js);
    ?>