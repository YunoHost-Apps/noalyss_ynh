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

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/**
 * @file
 * @brief show a depot
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_stock_sql.php';
$st=new Stock_Sql($_GET['r_id']);

?>
<?php echo HtmlInput::title_box("Ajouter un dépôt","change_stock_repo_div","close")?>
	<form method="post">
		<?php echo HtmlInput::hidden("r_id",$_GET['r_id']);?>
		<table>
			<tr>
				<td>
					<?php echo _("Nom");?>
				</td>
				<td>
					<?php $name=new IText("r_name",$st->r_name); echo $name->input();?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _("Adresse");?>
				</td>
				<td>
					<?php $name=new IText("r_adress",$st->r_adress); echo $name->input();?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _("Ville");?>
				</td>
				<td>
					<?php $name=new IText("r_city",$st->r_city); echo $name->input();?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _("Pays");?>
				</td>
				<td>
					<?php $name=new IText("r_country",$st->r_country); echo $name->input();?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _("Téléphone");?>
				</td>
				<td>
					<?php $name=new IText("r_phone",$st->r_phone); echo $name->input();?>
				</td>
			</tr>

		</table>
		<?php echo HtmlInput::submit("mod_stock",_("Sauver"))?>
	</form>