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
 * @brief show box for search
 *
 */
?>
<div id="histo_search_d" class="inner_box" style="width:60%;height:380px;display:none">
	<?php echo  HtmlInput::title_box(_("Recherche"), "histo_search_d", "hide")?>
	<form method="GET">
		<?php echo  HtmlInput::get_to_hidden(array("gDossier", "ac"))?>
		<table>
			<tr>
				<td> <?php echo _("Code Stock")?> </td>
				<td> <?php echo  $wcode_stock->input()?><?php echo  $wcode_stock->search()?> </td>
			</tr>
			<tr>
				<td> <?php echo _("Fiche")?> </td>
				<td> <?php echo  $wcard->input()?><?php echo  $wcard->search()?> </td>
			</tr>
			<tr>
				<td> <?php echo _("A partir de")?> </td>
				<td> <?php echo  $wdate_start->input()?> </td>
			</tr>
			<tr>
				<td> <?php echo _("Jusque")?> </td>
				<td> <?php echo  $wdate_end->input()?> </td>
			</tr>
			<tr>
				<td> <?php echo _("Dépôt")?> </td>
				<td> <?php echo  $wrepo->input()?> </td>
			</tr>
			<tr>
				<td> <?php echo _("Montant supérieur ou égal à")?>  </td>
				<td> <?php echo  $wamount_start->input()?> </td>
			</tr>
			<tr>
				<td> <?php echo _("Montant inférieur ou égal à")?>  </td>
				<td> <?php echo  $wamount_end->input()?> </td>
			</tr>

			<tr>
				<td> <?php echo _("Direction")?> </td>
				<td> <?php echo  $wdirection->input()?> </td>
			</tr>
		</table>
		<?php echo  HtmlInput::submit("search_histo_b",_("Recherche"))?>
	</form>
</div>

