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
*/


/**
 * @brief included from Todo_List::display
 * create a html with content
 * @see Todo_List
 */
global $g_user;
$wDate=new IDate('p_date_todo',$this->tl_date);
       
$wTitle=new IText('p_title',$this->tl_title);
$wDesc=new ITextArea('p_desc',  strip_tags($this->tl_desc));
$wDesc->heigh=5;
$wDesc->width=40;
$is_public=new ICheckBox('p_public');
$is_public->value='Y';
$is_public->set_check($this->is_public);
$dossier=Dossier::id();
$close_share=" if ( \$('shared_{$this->tl_id}') ){ \$('shared_{$this->tl_id}').remove();}";
echo HtmlInput::title_box("Note","todo_list_div".$this->tl_id,'close',$close_share);
?>
<form id="todo_form_<?php echo $this->tl_id?>" onsubmit="todo_list_save(<?php echo $this->tl_id?>);return false">
    <table>
        <tr>
            <td>
                <?php echo _("Date") ?>
            </td>
           
            <td>
                <?php echo $wDate->input() ?>
            </td>
           
        </tr>
        <tr>
            <td>
                <?php echo _("Titre") ?>
            </td>
           
            <td>
                <?php echo h($wTitle->input());?>
            </td>
           
        </tr>
        
        <?php
        // Section about Public note
        // display only if priv granted
        if ($g_user->check_action(SHARENOTEPUBLIC)):
        ?>
        <tr>
            <td>
                <?php echo _('Public')?>
            </td>
            <td>
                <?php echo $is_public->input()?>
            </td>
        </tr>
        <?php
        endif;
        ?>
        <?php 
        // section if the user can share note with other
        //users
        if ($g_user->check_action(SHARENOTE)) : 
        ?>
        
        <tr>
            <td>
                <?php echo _('Partage')?>
            </td>
            <td>
               <?php echo HtmlInput::anchor(_('Partage'), "", " onclick=\"todo_list_share({$this->tl_id},{$dossier}) \";")?>
            </td>
        </tr>
        <?php
        endif;
        ?>
    </table>
    <?php echo h($wDesc->input()); ?>
    <?php echo dossier::hidden(); ?>
    <?php echo HtmlInput::hidden('act','save') ?>
    <?php echo HtmlInput::hidden('id',$this->tl_id) ?>
    <?php if ($this->use_login == $_SESSION['g_user']) : ?>
    <p style='text-align: center'>
        <input type="submit" class="smallbutton" value="<?php echo _('Sauve');?>" onclick="todo_list_save(<?php echo $this->tl_id?>);return false">
    </p>
     <?php endif; ?>   
</form>