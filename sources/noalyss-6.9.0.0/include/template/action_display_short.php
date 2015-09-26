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
 * @brief  call from follow_up::display_short display a small form to
 * enter a new event
 */
global $g_user;
// Date of the event
$date=new IDate("date_event");
$date->id="date_event_action_short";
$title=new IText('title_event');
$title->css_isze="60%";
// Description
$summary=new ITextarea('summary');
$summary->style.='class="itextarea" style="padding:0px;margin:0px"';

// Type of document / event
$type=new ISelect("type_event");
$type->name="type_event";
$type->value=$cn->make_array("select dt_id,dt_value from document_type order by dt_value", 1);
$type->selected=0;

// Available for the profile
$profile=new ISelect('event_group');
$profile->value=$cn->make_array("select  p_id as value, ".
                "p_name as label ".
                " from profile  "
        . "where "
        . "p_id in "
        . " (select p_granted "
        . "     from user_sec_action_profile "
        . "     where ua_right='W' and p_id=".$g_user->get_profile().") "
        . "order by 2");

// priority
$priority=new ISelect('event_priority');
$priority->value=array(
            array('value'=>1, 'label'=>_('Haute')),
            array('value'=>2, 'label'=>_('Moyenne')),
            array('value'=>3, 'label'=>_('Basse'))
        );
$priority->selected=2;

// Card 
$dest=new ICard('dest');
$dest->jrn=0;
$dest->name='dest';
$dest->value="";
$dest->label="";
$list_recipient=$cn->make_list('select fd_id from fiche_def where frd_id in (14,25,8,9,16)');
$dest->extra=$list_recipient;
$dest->set_attribute('typecard', $list_recipient);
$dest->set_dblclick("fill_ipopcard(this);");
$dest->set_attribute('ipopup', 'ipopcard');
$dest->style=' style="vertical-align:0%"';

echo HtmlInput::title_box(_('Nouvel événement'), 'action_add_div');
?>
<span class="notice" style="float:right" id="action_add_frm_info"></span>
<form method="get" id="action_add_frm" onsubmit="action_save_short(<?php echo Dossier::id()?>);return false">
<p>
    <span>
    Date<?php echo $date->input()?>
    </span>
    <span>
    Type évenement
<?php echo $type->input();?>
    </span>
    Priorité
<?php echo $priority->input()?>
    groupe
<?php echo $profile->input()?>
</p>
Destinataire    <?php echo $dest->input();?>
<p>
    <span>
        <?php echo _('Sujet')?>
        <?php echo $title->input()?>
    </span>
</p>
    <?php echo "Description"?>
<p>
    <?php echo $summary->input()?>
</p>
<?php
echo HtmlInput::hidden('gDossier',Dossier::id());
echo HtmlInput::hidden('op','action_save');
?>
<p style="text-align: center">
    <?php echo HtmlInput::submit("action_add_submit", _('Valider'));?>
</p>
</form>
