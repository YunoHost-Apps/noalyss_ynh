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

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
echo HtmlInput::title_box(_("Poste comptable"), "acc_update", "hide");
?>
<span id="acc_update_info" class="notice"></span>
<div class="content" style="margin:5px;padding: 5px" >
<form method="post" id="acc_update_frm_id" onsubmit="pcmn_save();return false;">
    <p style="text-align: left">
<table >
<?php
$r= td(_('Poste comptable'),'style="width:auto;width:9rem;text-align:right"').td($val->input());
echo tr($r);
$r= td(_('Description'),'style="width:auto;text-align:right"').td($lib->input());
echo tr($r);
$r= td(_('Parent'),'style="width:auto;text-align:right"').td($parent->input());
echo tr($r);
$r= td(_('Type'),'style="width:auto;text-align:right"').td($type->input());
echo tr($r);
?>
</table>
<?php
echo HtmlInput::hidden('p_oldu',$pcmn_val);
echo HtmlInput::hidden('p_action',$action);
echo dossier::hidden();
$checkbox=new ICheckBox("delete_acc");
echo _('Cocher pour effacer')." ".$checkbox->input();
echo '<hr>';
echo HtmlInput::submit('update',_('Sauve'));
echo HtmlInput::button('hide',_('Annuler'),'onClick="$(\'acc_update\').hide();return true;"');
?>
</p>
</form>
</div>