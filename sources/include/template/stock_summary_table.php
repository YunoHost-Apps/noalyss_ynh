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
 * @brief show State of the stock
 *
 */
?>
<div class="content">
<table class="result">
	<tr>
		<th>
			<?php echo _("Code Stock")?>
		</th>
		<?php for ($i = 0; $i < count($a_repository); $i++):?>
			<th>
				<?php echo h( $a_repository[$i]['r_name'])?>
			</th>
		<?php endfor;?>
			<th>
				<?php echo _("Total")?>
			</th>
	</tr>
	<?php 
	for ($x = 0; $x < count($a_code); $x++):
		$class=($x%2==0)?' class="odd" ':' class="even" ';
		?>

		<tr <?php echo $class?> >
			<td>
				<?php echo HtmlInput::card_detail($a_code[$x]['sg_code'],"","",true)?>
			</td>
			<?php 
			$n_in=0;$n_out=0;
			for ($e = 0; $e < count($a_repository); $e++):

				$array = $cn->get_array("select coalesce(sum(s_qin)) as s_qin,coalesce(sum(s_qout)) as s_qout
											from tmp_stockgood_detail
										 where r_id=$1 and sg_code=$2 and s_id=$3"
						, array($a_repository[$e]['r_id'], $a_code[$x]['sg_code'],$tmp_id));
				?>
			<td>
				<?php 
					if (count($array)==0):
						echo 0;
					else:
						$n_in+=$array[0]['s_qin'];
						$n_out+=$array[0]['s_qout'];
						?>
						<table>
							<tr>
								<td>
									<?php echo _("IN")?>  :
								</td>
								<td class="num">
									<?php echo nbm($array[0]['s_qin'])?>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo _("OUT")?>  :
								</td>
								<td class="num">
									<?php echo nbm($array[0]['s_qout'])?>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo _("DIFF")?>  :
								</td>
								<td class="num">
									<?php echo nbm((bcsub($array[0]['s_qin'],$array[0]['s_qout'])))?>
								</td>
							</tr>
						</table>
						<?php 
					endif;
				?>
 			</td>
				<?php 
			endfor;  // loop e
			?>
			<td>
<table>
							<tr>
								<td>
									<?php echo _("IN")?>  :
								</td>
								<td class="num">
									<?php echo nbm($n_in)?>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo _("OUT")?>  :
								</td>
								<td class="num">
									<?php echo nbm($n_out)?>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo _("DIFF")?>  :
								</td>
								<td class="num">
									<?php echo nbm((bcsub($n_in,$n_out)))?>
								</td>
							</tr>
						</table>
			</td>
		</tr>
		<?php 
	endfor; // loop x
	?>
</table>
</div>