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
 * @brief show the available profiles for action-management
 *
 */
?>
<form method="POST" class="print">
    <?php echo HtmlInput::hidden('tab','profile_gestion_div')?>
	<?php echo HtmlInput::hidden("p_id", $this->p_id);?>
	<table>
		<tr>
			<th><?php echo _("Profil")?></th>
			<th><?php echo _("Accès")?></th>
		</tr>
		<?php for ($i=0;$i<count($array);$i++): ?>
		<tr>
			<td>
				<?php echo $array[$i]['p_name']?>
				<?php echo HtmlInput::hidden('ua_id[]',$array[$i]['ua_id'])?>
				<?php echo HtmlInput::hidden('ap_id[]',$array[$i]['p_id'])?>
			</td>
                        <?php
                            $color=($array[$i]['ua_right']!='X')?"border:lightgreen 2px solid; ":"border:red 2px solid; ";
                        ?>
			<td style="<?php echo $color?>">
				<?php
				$isel=new ISelect("right[]");
				$isel->value=$aright_value;
				$isel->selected=$array[$i]['ua_right'];
				echo $isel->input();?>
			</td>
		</tr>
		<?php endfor;?>
	</table>
<?php echo HtmlInput::submit("change_profile", _("Sauver"))?>
</form>