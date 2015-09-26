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
 * @brief display the form for searching action
 *
 */
?>
<?php if (! $inner ) : ?>
<div id="search_action" class="op_detail_frame" style="position:absolute;display:none;margin-left:120px;width:70%;clear:both;z-index:2;height:auto;border:1px #000080 solid">
	<?php echo HtmlInput::anchor_hide('&#10761;', "$('search_action').style.display='none';");?>
<?php endif; ?>
<?php if ( $inner ) : ?>
<div id="search_action" class="">
	<?php echo HtmlInput::anchor_hide('&#10761;', "removeDiv('search_action_div');");?>

	<?php endif; ?>
	<h2 class="title">
		<?php echo  _('Recherche avancée')?>
	</h2>
<?php if (! $inner ) : ?>
	<form method="get" action="do.php" style="padding:10px">
<?php endif; ?>
<?php if ( $inner ) : ?>
	<form method="get" id="fsearchaction" style="padding:10px" onsubmit="result_search_action('fsearchaction');return false;">
		<?php echo HtmlInput::hidden('ctlc',$_GET['ctlc'])?>
<?php endif; ?>
		<?php echo  dossier::hidden()?>
		<table style="width:100%">
                    <tr>
				<td style="text-align:right">
					<?php printf(_("Après le "))?>
				</td>
				<td >
					<?php echo  $start->input()?>
				</td>
			</tr>
			<tr>
				<td style="text-align:right"><?php echo  _('Avant le')?></td>
				<td>
					<?php echo  $end->input()?>
				</td>
			</tr>
			<tr>
				<td style="width:180px;text-align:right"> <?php echo _("Date de rappel après");?></td>
				<td>
					<?php echo $remind_date->input();?>
				</td>
			</tr>
			<tr>
				<td style="width:180px;text-align:right"> <?php echo _("Date de rappel avant");?></td>
				<td>
					<?php echo $remind_date_end->input();?>
				</td>
			</tr>
			<tr>
				<td style="width:180px;text-align:right"> <?php echo _("Affiche aussi les actions fermées");?></td>
				<td><?php echo $closed_action->input();?></td>
			</tr>
                        <tr>
			<td style="width:180px;text-align:right"><?php echo _('Etiquette'); ?></td>
				<td id="searchtag_choose_td">
                                    <?php echo Tag::button_search('search'); ?>
                                    <?php
                                        if ( isset($_GET['searchtag'])) {
                                            echo Tag::add_clear_button('search');
                                            for ($i=0;$i<count($_GET['searchtag']);$i++) {
                                                $t=new Tag($cn, $_GET['searchtag'][$i]);
                                                echo $t->update_search_cell('search');
                                            }
                                        }
                                    ?>
				</td>
			</tr>
                        <tr>
			<td style="width:180px;text-align:right"> <?php echo _("Référence");?></td>
				<td>
					<?php echo $osag_ref->input();?>
				</td>
			</tr>
			<tr>
				<td style="width:180px;text-align:right"> <?php echo _("Numéro document");?></td>
				<td>
					<?php $num=new INum('ag_id');echo $num->input();?>
				</td>
			</tr>
			<tr>
				<td style="width:180px;text-align:right"><?php echo _('Destinataire')?></td>
				<?php $label=$w->id."_label";?>
				<td ><?php echo  $w->input() . $w->search()?><span id="<?php echo $label?>"></span></td>
			</tr>
			<tr>
				<td style="text-align:right" ><?php echo  _("Profil")?></td>
				<td><?php echo  $str_ag_dest?></td>
			</tr>
			<tr>
				<td style="text-align:right" ><?php echo  _("Etat")?></td>
				<td><?php echo  $type_state->input()?></td>
			</tr>
			<tr>
				<td style="text-align:right" ><?php echo  _("Exclure Etat")?></td>
				<td><?php echo  $hsExcptype_state->input()?></td>
			</tr>
			<td style="text-align:right"><?php printf(_('contenant le mot'))?></td>
			<td ><input class="input_text" style="width:100%" type="text" name="action_query" value="<?php echo  $a?>"></td>
			</tr>
			<tr>
				<td style="text-align:right"><?php echo  _('Type de document')?></td>
				<td><?php echo $type_doc->input();?></td>
			</tr>
			
			</tr>
			<tr>
				<td style="text-align:right"><?php echo  _('Uniquement actions internes')?></td>
				<td><?php echo  $only_internal->input()?>
				</td>
			</tr>
		</table>
		<input type="submit" class="smallbutton" name="submit_query" value="<?php echo  _('recherche')?>">
		<input type="hidden" name="sa" value="list">

		<?php echo  $supl_hidden?>
		<?php echo HtmlInput::button_anchor(_('Fermer'), 'javascript:void(0)', 'fsearch_form', 'onclick="$(\'search_action\').style.display=\'none\';"','smallbutton');?>
	</form>
</div>

