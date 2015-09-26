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
* \brief  export analytic list in csv
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
header('Pragma: public');
header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="export-anc-list.csv"',FALSE);

require_once  NOALYSS_INCLUDE.'/class_anc_acc_list.php';


$bal=new Anc_Acc_List($cn);
$bal->get_request();
echo $bal->export_csv();
