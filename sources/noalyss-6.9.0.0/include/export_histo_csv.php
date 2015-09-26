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
 * \brief  history of the accountancy exported in CSV
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
header('Pragma: public');
header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="histo-export.csv"',FALSE);

$ledger=new Acc_Ledger($cn,0);
list($sql,$where)=$ledger->build_search_sql($_GET);

$order=" order by jr_date_order asc,substring(jr_pj_number,'[0-9]+$')::numeric asc ";

$res=$cn->get_array($sql.$order);

printf('"%s";',"Internal");
printf('"%s";',"Journal");
printf('"%s";',"Date");
printf('"%s";',"Echeance");
printf('"%s";',"Paiement");
printf('"%s";',"Piece");
printf('"%s";"";',"Tiers");
printf('"%s";',"Description");
printf('"%s";',"Note");
printf('"%s"',"Montant opération");
printf("\r\n");

for ($i=0;$i<count($res);$i++)
  {
    printf('"%s";',$res[$i]['jr_internal']);
    printf('"%s";',$res[$i]['jrn_def_name']);
    printf('"%s";',$res[$i]['str_jr_date']);
    printf('"%s";',$res[$i]['str_jr_ech']);
    printf('"%s";',$res[$i]['str_jr_date_paid']);
    printf('"%s";',$res[$i]['jr_pj_number']);
    printf('"%s";',$res[$i]['quick_code']);
    printf('"%s %s";',$res[$i]['name'],$res[$i]['first_name']);
    printf('"%s";',$res[$i]['jr_comment']);
    printf('"%s";',$res[$i]['n_text']);

    $amount=$res[$i]['jr_montant'];

	if ( $res[$i]['total_invoice']!=null && $res[$i]['jr_montant']!=$res[$i]['total_invoice'])
		$amount=$res[$i]['total_invoice'];
    if ( $res[$i]['jrn_def_type'] == 'FIN')
      {
		$positive = $cn->get_value("select qf_amount from quant_fin where jr_id=$1",array($res[$i]['jr_id']));
		if ( $positive !='' ) $amount=$positive;
      }
    printf('%s',nb($amount));

    printf("\r\n");

  }