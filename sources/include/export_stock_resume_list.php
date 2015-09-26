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
 * @brief export in CSV the summary of stock in list
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_stock.php';
global $cn;
// var_dump($_GET);
$stock=new Stock($cn);
$tmp_id = $stock->build_tmp_table($_GET);

header('Pragma: public');
header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="stock-summary-list.csv"',FALSE);

?>
"Depot";"Adresse";"Ville";"Pays";"Code Stock";"Fiches";"IN";"OUT";"DIFF"
<?php 
$a_repo=$cn->get_array("select distinct t.r_id,r_name,r_adress,r_city,r_country from stock_repository as s join tmp_stockgood_detail as t
	on (s.r_id=t.r_id)
	where
	s_id=$1
	order by 2
	",array($tmp_id));
 for ($r=0;$r<count($a_repo);$r++):

		$a_stock=$cn->get_array(
				"
					select coalesce(sum(s_qin),0) as qin,coalesce(sum(s_qout),0) as qout,sg_code
						from tmp_stockgood_detail  where r_id=$1 and s_id=$2
						group by sg_code
						order by sg_code

					",array($a_repo[$r]['r_id'],$tmp_id));
		for ($s=0;$s<count($a_stock);$s++):

			$a_card=$cn->get_array(
					"
						select f_id,vw_name,quick_code
						from vw_fiche_attr
						where
					 f_id in (
						select distinct f_id from fiche_detail
							where
							ad_id=19 and
							ad_value=$1)
						order by vw_name,quick_code
					",array($a_stock[$s]['sg_code']));

printf ('"%s";',$a_repo[$r]['r_name']);
printf ('"%s";',$a_repo[$r]['r_adress']);
printf ('"%s";',$a_repo[$r]['r_city']);
printf ('"%s";',$a_repo[$r]['r_country']);
printf('"%s";',$a_stock[$s]['sg_code']);
	$sep="";
				for ( $c=0;$c<count($a_card);$c++):
					$a=sprintf('[%s] %s',$a_card[$c]['quick_code'], $a_card[$c]['vw_name']);
					$sep="  / ";
				endfor; // for C
				if ( count($a_card)== 0 ) $a= ' Erreur Code non utilisé';
 printf('"%s";',$a);
 printf('%s;',nbm($a_stock[$s]['qin']));
 printf('%s;',nbm($a_stock[$s]['qout']));
 printf ('%s',nbm(bcsub($a_stock[$s]['qin'],$a_stock[$s]['qout'])));
 printf("\r\n");
 endfor;
 endfor;

