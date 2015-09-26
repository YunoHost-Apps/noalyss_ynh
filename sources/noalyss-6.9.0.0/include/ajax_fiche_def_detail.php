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
 * @brief show detail of a fiche_def (category of card) + Attribut
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_fiche_def.php';
require_once NOALYSS_INCLUDE.'/class_tool_uos.php';
global $g_user;

$g_user->can_request(FICCAT,0);

$fd=new Fiche_Def($cn,$_GET['id']);
if ( $_GET['id'] > 0 )
{

	echo $fd->input_detail();
	echo HtmlInput::button("retour_b", _("Retour à la liste"), "onclick=\"$('detail_category_div').hide();$('list_cat_div').show()\"");
}
else
{
	$fd->input_new();

}
?>
