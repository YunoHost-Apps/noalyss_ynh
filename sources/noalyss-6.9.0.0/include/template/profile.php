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
 * @brief show a profile
 *
 */

?>
<table>
<tr>
	<td><?php echo _("Nom")?></td>
	<td><?php echo $name->input();?></td>
</tr>
<tr>
	<td><?php echo _("Description")?></td>
	<td><?php echo $desc->input()?></td>
</tr>
<tr>
	<td><?php echo _("Avec Calculatrice")?></td>
	<td><?php echo $with_calc->input()?></td>
</tr>
<tr>
	<td><?php echo _("Avec  Accès Direct ")?></td>
	<td><?php echo $with_direct_form->input()?></td>
</tr>
</table>