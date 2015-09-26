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
 * @brief show the result of stock history
 *
 */

?>
<div class="content">
	<?php echo $nav_bar?>
<table class="result">
	<tr>
		<th><?php echo $tb->get_header(0);?></th>
		<th><?php echo $tb->get_header(1);?></th>
		<th><?php echo $tb->get_header(2);?></th>
		<th><?php echo $tb->get_header(3);?></th>
		<th>Opération</th>
		<th><?php echo $tb->get_header(4);?></th>
		<th><?php echo $tb->get_header(5);?></th>
		<th><?php echo $tb->get_header(6);?></th>
		<th><?php echo $tb->get_header(7);?></th>
	</tr>
	<?php 

	for ($i=0;$i<$max_row;$i++):
		$row=Database::fetch_array($res, $i);
		$class=($i%2==0)?' class="even" ':' class="odd" ';
	?>
	<tr <?php echo $class?>>
		<td>
			<?php echo $row['cdate']?>
		</td>
		<td>
			<?php echo HtmlInput::card_detail($row['sg_code'],"",' class="line" ',true)?>
		</td>
		<td>
			<?php echo $row['r_name']?>
		</td>
		<td>
			<?php if (trim($row['qcode'])!='') : ?>
			<?php echo HtmlInput::card_detail($row['qcode'],$row['fname'],' class="line" ')?>
			<?php endif; ?>
		</td>
		<td>
			<?php if (trim($row['jr_internal'])!='') : ?>
			<?php echo HtmlInput::detail_op($row['jr_id'],$row['jr_internal'])?>
			<?php endif; ?>
		</td>
		<td>
			<?php echo $row['ccomment']?>
		</td>
		<td class="num">
			<?php echo nbm($row['j_montant'])?>
		</td>
		<td class="num">
			<?php echo nbm($row['sg_quantity'])?>
		</td>
		<td>
			<?php echo $row['direction']?>
		</td>
	</tr>
	<?php endfor;?>
</table>
	<?php echo $nav_bar?>
</div>