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
 * @brief  display the folders the user has no access and permit to add them 
 * thanks ajax call. 
 * 
 * The received parameter  are 
 *  - $a_dossier, the result of   Dossier::show_dossier
 *  - $user_id id of the user
 * 
 */
echo js_include('admin.js');
if ( count($a_dossier) == 0 ) 
{
    echo '<h1 class="notice">'._('Aucun dossier à afficher').'</h1>';
    return;
}
?>
<table class="result">
<?php
$nb_dossier=count($a_dossier);
for ($i=0;$i<$nb_dossier;$i++):
    $class=($i%2==0)?"even":"odd";
?>
    <tr id="row_db_<?php echo $a_dossier[$i]['dos_id'];?>" class="<?php echo $class?>">
        <td>
            <?php
                echo HtmlInput::button('add_folder',BUTTONADD,  " onclick=\"folder_add({$user_id},{$a_dossier[$i]['dos_id']});\"", ' smallbutton');
            ?>
         
        </td>
        <td>
            <?php
                echo h($a_dossier[$i]['dos_name']);
            ?>
        </td>
        <td>
            <?php
                echo h($a_dossier[$i]['dos_description']);
            ?>
        </td>
    </tr>
<?php
endfor;
?>
</table>