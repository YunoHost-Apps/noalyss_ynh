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

/*!\file
 * \brief this file is included to perform modification on category of document
 * table document_type
 */

// show list of document
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_document_type.php';

if ( isset($_POST['add']) )
{
    $catDoc=new Document_Type($cn);
    $catDoc->insert($_POST['cat'],$_POST['prefix']);
}
if ( isset($_POST['save'])) {
	$catDoc=new Document_Type($cn,$_POST['dt_id']);
	$catDoc->get();
	$catDoc->dt_value=trim($_POST['dt_name']);
	$catDoc->dt_prefix=trim($_POST['dt_prefix']);
	if ( $catDoc->dt_value=="") {
		alert(_("Le nom ne peut pas être vide"));
	} else {
		$catDoc->update();
	}
	if ($_POST['seq'] != 0 && isNumber($_POST['seq'])==1){
		$catDoc->set_number($_POST['seq']);
	}
}
$aList=Document_Type::get_list($cn);
$addCat=new IText('cat');
$addPrefix=new IText('prefix');
$str_addCat=$addCat->input();
$str_addPrefix=$addPrefix->input();
$str_submit=HtmlInput::submit('add',_('Ajout'));
echo '<div class="content">';
require_once NOALYSS_INCLUDE.'/template/list_category_document.php';

echo '</div>';
?>
