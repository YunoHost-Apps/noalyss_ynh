<?php
/*
 * * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

 * 
 */


/**
 * @file
 * @brief  display a submenu contained in a array
 * @param $a_module contains rows from profile_menu
 * @param $p_module_id is the module / menu id main menu
 * @see Profile_Menu::display_module_menu
 */
?>
    <?php
    $nb_module=count($a_module);
    for ($i=0;$i < $nb_module ; $i ++ ):
    ?>
    <td id="sub<?php echo $a_module[$i]['pm_id']?>" class="tool">
        <?php
            if ( $a_module[$i]['me_file'] != "") {
                $url=$a_module[$i]['me_file'];
            } else if ( $a_module[$i]['me_url'] != "") {
                $url=h($a_module[$i]['me_url'] );
            } else if ($a_module[$i]['me_javascript'] != "") {
                $url="-> javascript";
            } else {
                $url = HtmlInput::anchor("sous-menu", "x", 
                        sprintf(" onclick = \" display_sub_menu(%d,%d,%d,%d)\"",Dossier::id(),$this->p_id,$a_module[$i]['pm_id'],$p_level), 
                        ' class="line" ');
            }
        ?>
        <?php 
            echo HtmlInput::anchor($a_module[$i]['me_code']. " ".
                    gettext($a_module[$i]['me_menu']),'',
                   sprintf(" onclick =\"mod_menu (%d,%d) \" ",Dossier::id(),$a_module[$i]['pm_id']),
                   ' class="line" ')?>
        <span>
                            <?php echo HtmlInput::anchor(SMALLX, "", 
                                    sprintf (" onclick = \"remove_sub_menu(%d,%d)\"", Dossier::id(),$a_module[$i]['pm_id']),
                                    'class="tinybutton"' ) ?>
        </span>
        <br/>
        <?php echo $url;?>
        <p>
            <?php echo _('ordre apparition') , " ",$a_module[$i]['p_order'];?>
        <p>
            <?php echo _('Default')," : ",
                    ($a_module[$i]['pm_default']==1)?_('Oui'):_('Non')
                ?>
        </p>
    </td>
    <?php
    endfor;
    ?>
<td>
                     <?php
                     echo HtmlInput::button_action("+", 
                             sprintf("add_menu({dossier:%d,p_id:%d,type:'%s',p_level:%d,dep:'%s'})",
                                     Dossier::id(),$this->p_id,'me',$p_level,$p_module_id)
                             ,"xx",'smallbutton')
                     ?>
                 
</td>
