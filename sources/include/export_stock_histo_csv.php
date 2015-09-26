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
 * @brief export in CSV the export of history
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_stock.php';

$stock=new Stock($cn);
$sql = $stock->create_query_histo($_GET);
$sql .= " order by  real_date asc";

$res=$cn->exec_sql($sql);
$max_row=Database::num_row($res);
header('Pragma: public');
header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="histo-stock.csv"',FALSE);
printf('"Date";');
	printf('"%s";','Code Stock');
	printf('"%s";','Depot');
	printf('"%s";','Fiche');
	printf('"%s";','Commentaire');
	printf('%s;','Quantité');
	printf('"%s";','IN/OUT');
		printf("\n\r");
for ($i=0;$i<$max_row;$i++)
{
	$row=Database::fetch_array($res,$i);
	printf('"%s";',$row['cdate']);
	printf('"%s";',$row['sg_code']);
	printf('"%s";',$row['r_name']);
	printf('"%s";',$row['qcode']);
	$row['ccomment']=str_replace('"','',$row['ccomment']);
	printf('"%s";',$row['ccomment']);
	printf('%s;',nbm($row['sg_quantity']));
	printf('"%s";',$row['direction']);
	printf("\n\r");

}

?>