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
 * @brief list payment method
 *
 */

?>
 <table class="table_large" >
	 <tr >
		 <th>
			 <?php echo $header->get_header(0)?>
		 </th>
		 <th style="text-align:center">
			 <?php echo $header->get_header(1)?>
		 </th>
		 <th style="text-align:center">
			 <?php echo $header->get_header(2)?>
		 </th>
		 <th style="text-align:center">
			 <?php echo $header->get_header(3)?>
		 </th>
		 <th style="text-align:center">
			 <?php echo $header->get_header(4)?>
		 </th>
		 <th>

		 </th>
	 </tr>
<?php for ($i=0;$i<count($array);$i++):
	if ($i%2==0):
		$class='class="odd" ';
	else:
		$class='class="even"';
	endif;
?>
	 <tr <?php echo $class?>>
		 <td>
			 <?php echo h($array[$i]['mp_lib'])?>
		 </td>
		 <td style="text-align:center">
			 <?php echo h($array[$i]['jrn_def_name'])?>

		 </td>
		 <td style="text-align:center">
			 <?php echo h($array[$i]['fd_label'])?>

		 </td>
		 <td style="text-align:center">
			 <?php echo h($array[$i]['jrn_target'])?>
		 </td>
		 <td style="text-align:center">
			 <?php echo h($array[$i]['vw_name'])?>
		 </td>

<?php 
echo $td.HtmlInput::button_anchor(_('Modifie'),'?ac='.$_REQUEST['ac'].'&sa=mp&sb=change&'.dossier::get().
                                              '&id='.$array[$i]['mp_id'],"","","smallbutton");
?>

	 </tr>
<?php endfor;?>

</table>