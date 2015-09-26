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

// Copyright Author Dany De Bontridder dany@alchimerys.be
if ( ! defined ('ALLOWED')) die('Appel direct ne sont pas permis');
echo HtmlInput::title_box(_("Navigateur"), "navi_div");
$sql="
    select distinct code, me_code,me_description,coalesce(me_description_etendue,me_description) as me_description_etendue,v1menu,v2menu,v3menu,p_type_display,
    javascript
    from 
    v_menu_description
    where user_name=$1
    order by 2 ";

$a_menu=$cn->get_array($sql,array($_SESSION['g_user']));

?>
<div class="content">
    <p>
        <?php echo _("Vous permet d'accèder rapidement au menu qui vous intéresse, utiliser le filtre pour trouver plus rapidement")?>
    </p>
    <p style='margin-left: 5%'>
    Filtre : <?php
    echo HtmlInput::filter_table("navi_tb", "0,1,2", '1');
    ?>
    </p>
    <table id="navi_tb" class="sortable" >
        <tr>
            <th class='sorttable_sorted'>
                Code AD 
                <?php echo HtmlInput::infobulle(34); ?>
                <span id="sorttable_sortfwdind">&nbsp;&blacktriangledown;</span>
            </th>
            <th>
                <?php echo _("Chemin");?>
                <?php echo HtmlInput::infobulle(35); ?>
            </th>
            <th>
                <?php echo _("Description complète");?>
            </th>
            <th>
                <?php echo _("Aide sur le wiki");?>
            </th>
        </tr>
<?php 
$nMax=count($a_menu);
$url="do.php?gDossier=".dossier::id();
for ($i=0;$i<$nMax;$i++):
?>
        <tr>
            <td>
                <?php
                if ( $a_menu[$i]['javascript'] == "" ) :
                ?>
                    <a class='mtitle' style='text-decoration: underline' href="<?php echo $url."&ac=".$a_menu[$i]['code']; ?>" target='_blank'>
                <?php else : 
                    $js=  str_replace('<DOSSIER>', Dossier::id(), $a_menu[$i]['javascript']);
                 ?>
                    <a class='mtitle' style='text-decoration: underline' href="javascript:void(0)"
                        onclick="<?php echo $js ?>">
                <?php endif; ?>
                <?php echo $a_menu[$i]['me_code'];               ?>
                </a>
            </td>
            <td>
                <?php
                if ( $a_menu[$i]['javascript'] == "" ) :
                ?>
                    <a class='mtitle' style='text-decoration: underline' href="<?php echo $url."&ac=".$a_menu[$i]['code']; ?>" target='_blank'>
                <?php else : 
                    $js=  str_replace('<DOSSIER>', Dossier::id(), $a_menu[$i]['javascript']);
                 ?>
                    <a class='mtitle' style='text-decoration: underline' href="javascript:void(0)"
                        onclick="<?php echo $js ?>">
                <?php endif; ?>
                <?php
                $path=$a_menu[$i]['v3menu'];
                $path.=$a_menu[$i]['v2menu'];
                $path.=$a_menu[$i]['v1menu'];
                echo $path;
                ?>
                </a>
            </td>
            <td>
                <?php if ($a_menu[$i]['p_type_display'] == 'PL') echo "("._('Extension').")";
                echo $a_menu[$i]['me_description_etendue']; ?>
            </td>
            <td>
                <a class="mtitle" style='text-decoration: underline' href="http://wiki.noalyss.eu/doku.php?id=MENU:<?php echo $a_menu[$i]['me_code']; ?>" target='_blank'><?php echo _("sur le wiki")?> : <?php echo $a_menu[$i]['me_code']; ?></a>
            </td>
        </tr>
<?php endfor; ?>        
    </table>

    
</div>
<script charset="UTF8" lang="javascript">
    $('lk_navi_tb').focus();
</script>