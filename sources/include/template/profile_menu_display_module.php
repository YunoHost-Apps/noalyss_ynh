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
 * @brief display the module, used to setup the module and menu, included from
 * Profile_Menu
 */
?>
 <div id="module_setting">
	<table class="result">
	    <tr>
		<?php
		foreach ($ap_module as $row):
			$js="";
		    $style="";
		    if ( $row['me_code']=='new_line')
		    {
			echo "</tr><tr>";
			continue;
		    }
                    $style=" tool ";
                    $url="XX";
		    if ( $row['me_url']!='')
		    {
			$url=$row['me_url'];
		    }
		    elseif ($row['me_javascript'] != '')
                    {
                        $url=$row['me_javascript'];
                    }
                    elseif ( $row['me_file'] != "")
                    {
                        $url=$row['me_file'];
                    }
                    else
		    {
			$url=HtmlInput::anchor(_('Menu'),'',
                                sprintf(" onclick = \" \$('menu_table').innerHTML='';display_sub_menu(%d,%d,%d,%d)\" ",
                                        Dossier::id(),
                                        $this->p_id,
                                        $row['pm_id'],0));
		    }
		    ?>
		<td class="<?php echo $style?>" id="sub<?php echo $row['pm_id']?>">
                        <?php echo HtmlInput::anchor(gettext($row['me_menu']),'',sprintf(" onclick =\"mod_menu (%d,%d) \" ",Dossier::id(),$row['pm_id']),' class="line" ')?>
                        <span>
                            <?php echo HtmlInput::anchor(SMALLX, "", 
                                    sprintf (" onclick = \"remove_sub_menu(%d,%d)\"", Dossier::id(),$row['pm_id']),
                                    'class="tinybutton"' ) ?>
                        </span>
                        <p>
                        <?php echo _($row['me_description'])?>
                        </p>
                        <p>
                        <?php echo $url?>
                        </p>
                        <p>
                        <?php echo _('ordre apparition') , " ",$row['p_order'];?>
                        <p>
                        <?php echo _('Default')," : ",
                                ($row['pm_default']==1)?_('Oui'):_('Non')
                            ?>
                        </p>
                        
                 </td>
		<?php 
		    endforeach;
		?>
                 <td>
                     <?php
                     echo HtmlInput::button_action("+", 
                             sprintf("add_menu({dossier:%d,p_id:%d,type:'%s',p_level:%d,dep:0})",
                                     Dossier::id(),$this->p_id,'me',0)
                             ,"xx",'smallbutton')
                     ?>
                 </td>
	    </tr>
	</table>
    </div>

<div id='sub_menu_div'>
    <table id="menu_table" class="result">

    </table>
</div>