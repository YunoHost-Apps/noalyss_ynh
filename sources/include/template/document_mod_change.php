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

/**\file
 * \brief this file respond to an ajax request to modify a type of document
 */
require_once NOALYSS_INCLUDE.'/class_document_type.php';
echo HtmlInput::title_box(_('Type de document'),'change_doc_div');

$doc_type=new Document_type($cn,$dt_id);
$doc_type->get();
?>
<form method="POST" id="cat_doc_f" onsubmit="cat_doc_change_record('cat_doc_f');">
	<?php echo HtmlInput::request_to_hidden(array("ac","gDossier","dt_id"))?>
<table>
<tr>
  <td> <?php echo _('Nom')?>
  </td>
  <td>
	  <?php 
	  $name=new IText('dt_name',$doc_type->dt_value);
	  echo $name->input();
	  ?>
  </td>
</tr>

<tr>
  <td><?php echo _('Préfixe')?>
  </td>
  <td>
	  <?php 
	  $prefix=new IText('dt_prefix',$doc_type->dt_prefix);
	  echo $prefix->input();
	  ?>
  </td>
</tr>

<tr>
  <td><?php echo _('numéro actuel')?>
  </td>
  <td>
	<?php 
	$ret= $cn->get_array("select last_value,is_called from seq_doc_type_".$doc_type->dt_id) ;

    $last=$ret[0]['last_value'];
             /*!
                  *\note  With PSQL sequence , the last_value column is 1 when before   AND after the first call, to make the difference between them
                  * I have to check whether the sequence has been already called or not */
    if ($ret[0]['is_called']=='f' ) $last--;
	echo $last;
	?>
  </td>
  <tr>
  <td><?php echo _('Prochain numéro')?>
	  <?php echo 
		HtmlInput::infobulle(15);
	?>
  </td>
   <td>
	  <?php 
	  $seq=new INum('seq',0);
	  echo $seq->input();
	  ?>
  </td>
</tr>

</table>

  <?php echo HtmlInput::submit("save",_('Sauver'));?>
</form>