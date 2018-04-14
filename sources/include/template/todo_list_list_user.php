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
 * @brief display all the user for the todo_list.
 * @see Todo_List::display_user
 * @param $p_array array of user who can access this folder
 * @param Object Todo_List
 * @param $dossier = Dossier::id()
 * 
 */
echo _('Cherche')." ".HtmlInput::filter_table("todo_user_table", "0,1,2", 1);
?>

<table id="todo_user_table<?php echo $this->tl_id?>" class="result">
    <tr>
        <th>
            <?php echo _('Login')?>
        </th>
        <th>
            <?php echo _('Nom ')?>
        </th>
        <th>
            <?php echo _('Prénom')?>
        </th>
        <th>
        </th>
        <?php
            $max=count($p_array);
            for ($i=0;$i<$max;$i++):
                if ($p_array[$i]["use_login"]==$g_user->login) :
                    continue;
                endif;
        ?>
        <tr>
            <td>
                <?php echo $p_array[$i]['use_login'];?>
            </td>
            <td>
                <?php echo $p_array[$i]['use_name'];?>
            </td>
            <td>
                <?php echo $p_array[$i]['use_first_name'];?>
            </td>
            <td>
            <?php
            $check=new ICheckBox('use_login'.$p_array[$i]['use_login']."_".$this->tl_id);
            if ($this->is_shared_with($p_array[$i]['use_login']) != 0) {
                $check->selected=true;
            }
            $check->javascript=" onclick=\"todo_list_set_share({$this->tl_id},'{$p_array[$i]['use_login']}','{$dossier}')\"";
            echo $check->input();
            ?>
            </td>
        </tr>
        <?php    endfor; ?>
        
    </tr>
</table>   