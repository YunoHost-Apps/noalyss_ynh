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
 * @brief show the result of a search into a inner windows
 *
 */
?>
<form onsubmit="set_action_related('fresultaction');return false;" id="fresultaction">
	<?php echo HtmlInput::hidden('ctlc',$_GET['ctlc'])?>
	<?php echo HtmlInput::submit("save_action", _("Mettre à jour"))?>
<?php if (isset($limit)) : ?>
	<h2 class="notice">Recherche limitée à <?php echo $limit?> résultats</h2>
<?php endif;?>

<table class="result">

	<tr>
		<th>

		</th>
		<th>
			<?php echo _("Date");?>
		</th>
		<th>
			<?php echo _("Ref");?>
		</th>
		<th>
			<?php echo _("Titre");?>
		</th>
		<th>
			<?php echo _("Destinataire");?>
		</th>
		<th>
			<?php echo _("Type");?>
		</th>
	</tr>
<?php for ($i=0;$i<$limit;$i++):?>
	<?php $class=($i%2==0)?' class="odd" ':' class="even"'; ?>
	<tr  <?php echo $class?>>
		<td>
			<?php
			$ck=new ICheckBox('ag_id[]');
			 $ck->value=$a_row[$i]['ag_id'];
			 echo $ck->input();
			?>
		</td>
		<td >
			<?php echo h($a_row[$i]['my_date'])?>
		</td>
		<td>
			<?php echo h($a_row[$i]['ag_ref'])?>
		</td>
		<td>
			<?php echo h($a_row[$i]['sub_ag_title'])?>
		</td>
		<td>
			<?php echo h($a_row[$i]['name'])?>
		</td>
		<td>
			<?php echo h($a_row[$i]['dt_value'])?>
		</td>
	</tr>

<?php endfor;?>
</table>
	<?php echo HtmlInput::submit("save_action", _("Mettre à jour"))?>
</form>