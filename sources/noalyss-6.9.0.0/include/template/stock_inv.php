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
/* $Revision$ */

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/**
 * @file
 * @brief show the input
 *
 */
?>
<div style="<?php if ( ! $p_readonly) echo "position:absolute";?>" class="content">
	<form method="POST" id="stock_reprise" class="print" onsubmit="return confirm_box(this,'<?php echo _("Vous confirmez ?")?>')">
<table>
	<tr><td>
			<?php echo _("Date")?>
		</td>
		<td>
			<?php echo $date->input()?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo _("Dépot")?>
		</td>
		<td>
			<?php echo $idepo->input()?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo _("Motif de changement")?>
		</td>
		<td>
			<?php echo $motif->input()?>
		</td>
	</tr>
</table>
<table id="stock_tb" style="width: 80%">
	<tr>
		<th style="text-align: left">
			<?php echo _("Code Stock")?>
		</th>
<?php if ( $p_readonly == true ) :?>
		<th style="text-align: left">
			<?php echo _("Fiche")?>
		</th>
<?php endif;?>
		<th style="text-align:right">
			<?php echo _("Quantité")?>
		</th>
	</tr>
<?php for($i=0;$i<$nb;$i++): ?>
	<tr>
		<td>
<?php if ( $p_readonly == false) : ?>
			<?php echo $sg_code[$i]->input()?>
			<?php echo $sg_code[$i]->search()?>
			<?php echo $label[$i]->input()?>
<?php else: ?>
			<?php if ( trim($sg_code[$i]->value) != "")  echo HtmlInput::card_detail($sg_code[$i]->value,h($sg_code[$i]->value),' class="line"',true)?>
<?php endif ?>

		</td>
<?php if ( $p_readonly == true && isset ($fiche[$i])) :?>
		<td>
			<?php echo HtmlInput::card_detail($fiche[$i]->get_quick_code(),h($fiche[$i]->getName()),' class="line"');?>
		</td>
<?php endif;?>
		<TD class="num"">
		<?php if ($sg_quantity[$i]->value==0 && $p_readonly==true):?>

		<?php else : ?>
			<?php echo $sg_quantity[$i]->input()?>
			<?php endif;?>
		</td>
		<TD class="num"">
			<?php if (isset ($sg_type[$i])):?>
			<?php echo $sg_type[$i]?>
			<?php endif;?>
		</td>
	</tr>
<?php endfor; ?>
</table>
<?php if ($p_readonly == false) echo HtmlInput::button_action(_('Ajouter une ligne'),'stock_add_row();',"xx",'smallbutton')?>
<?php if ($p_readonly == false) echo HtmlInput::submit('save',_('Sauver'))?>
<?php if ($p_readonly == false) echo HtmlInput::hidden('row',$nb)?>
<?php if ($p_readonly == false) echo HtmlInput::button("reprise_show",_('Reprise inventaire'),  " onclick=\"$('reprise_inventaire_div').show();\"")?>
	</form>
</div>
<div class="inner_box" id="reprise_inventaire_div" style="display:none">
    <form method="get">
    <?php echo HtmlInput::title_box(_('Reprise inventaire'), 'reprise_inventaire_div', 'hide');?>
    <?php echo HtmlInput::request_to_hidden(array('gDossier','ac'))?>
    <table>
        <tr>
            <td>
                <?php echo _('Dépot'); ?>
            </td>
            <td>
                <?php echo $idepo->input()?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo _('Exercice')?>
            </td>
            <td>
                <?php echo $select_exercice->input();?>
            </td>
        </tr>
    </table>
        <?php echo HtmlInput::hidden('reprise_frm',1);?>
        <?php echo HtmlInput::submit("reprise_frm_bt", _('Reprise inventaire'));?>
     </form>
</div>
