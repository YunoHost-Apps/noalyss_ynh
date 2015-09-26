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
 * @brief display the calendar as a list. Included from the Calendar::zoom_list()
 */
?>
<div class="content" id="user_cal" style="width:100%">
<?php 
    $short=HtmlInput::default_value_get('from', 0);
    $js=sprintf("calendar_zoom({gDossier:%d,invalue:'%s',outvalue:'%s',distype:'%s','notitle':%d})",
            dossier::id(),'per_div','calendar_zoom_div','cal',$notitle);
    echo HtmlInput::anchor(_('Calendrier'),''," onclick=\"{$js}\"")   ;
    echo HtmlInput::button_action_add();

?>
    <table class="result">
<?php
    $nb_event=count($a_event);
    $a_status=array('R'=>_('Retard'),'N'=>'Auj.','F'=>'');
    for ($i=0;$i<$nb_event;$i++):
        $class=($i % 2 == 0 )? 'even':'odd';
        $idx=$a_event[$i]['status'];
        $class=($idx=='R')?'notice':$class;
        
?>
        <tr class="<?php echo $class?>">
            <td>
                <?php
                echo $a_status[$idx];
                if ($a_event[$i]['delta_days'] != 0 ) echo $a_event[$i]['delta_days']," "._('jours');
                ?>
            </td>
            <td>
                <?php echo $a_event[$i]['str_date']; ?>
                &nbsp; 
                <?php echo $a_event[$i]['ag_hour']; ?>
            </td>
            <td>
                <?php echo HtmlInput::detail_action($a_event[$i]["ag_id"],$a_event[$i]["str_name"]);?>
            </td>
            <td>
                <?php echo h($a_event[$i]['ag_title']); ?>
            </td>
        </tr>
        
<?php        
    endfor;
?>
    </table>
    
    
</div>