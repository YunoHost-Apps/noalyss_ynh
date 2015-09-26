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
 * @brief show all the categories of card fiche_def
 *
 */
$max=Database::num_row($res);
?>
<div id="list_cat_div" class="content">
	<?php echo "Filtre"; echo HtmlInput::filter_table("fiche_def_tb", "0,1,2,3,4", "1"); ?>
<table id="fiche_def_tb" class="result">
	<tR>
		<th>
			<?php echo $tab->get_header(0)?>
		</th>
		<th>
			<?php echo $tab->get_header(1)?>
		</th>
		<th>
			<?php echo $tab->get_header(2)?>
		</th>
		<th>
			<?php echo $tab->get_header(3)?>
		</th>
		<th>
			Description
		</th>
	</tR>
<?php
$dossier=Dossier::id();
for ($i=0;$i<$max;$i++):
	$class=($i%2==0)?' class="even" ':' class="odd" ';
	$row=Database::fetch_array($res, $i);
?>
	<tr <?php echo $class?> >
		<td>
		<?php echo HtmlInput::anchor(h($row['fd_label']), "javascript:void(0)", "onclick=\"detail_category_show('detail_category_div','".$dossier."','".$row['fd_id']."')\"")?>
		</td>
		<td>
			<?php echo h($row['fd_class_base'])?>
		</td>
		<td>
			<?php
			 $v=($row['fd_create_account']=='t')?_("Automatique"):_("Manuel");
			 echo $v;
			?>
		</td>
		<td>
			<?php echo $row['frd_text']?>
		</td>
		<td>
			<?php echo h($row['fd_description']) ?>
		</td>
	</tr>


<?php
endfor;
?>
</table>
<?php
echo HtmlInput::button("cat_fiche_def_add",_("Ajout d'une nouvelle catégorie"), "onclick=\"detail_category_show('detail_category_div','".$dossier."','-1')\"");
?>
</div>
<div id="detail_category_div" style="display:none"">

</div>