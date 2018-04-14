<?php

/*
 *   This file is part of NOALYSS.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief display the detail of an anc operation
 * parameters : oa_group
 */
$http=new HttpInput();
$oa_group=$http->request("oa_group","number");
bcscale(4);
$row=$cn->get_row("select distinct oa_group,
                to_char(oa_date,'DD.MM.YYYY') as str_date ,
                oa_date,
                oa_description,
                jr_pj_number,
                jr_id
            from 
                operation_analytique as oa 
                join poste_analytique using (po_id)
                left join (select jr_id,jr_pj_number,j_id from jrn join jrnx on (jr_grpt_id=j_grpt) ) as m on (m.j_id=oa.j_id)
                where oa_group=$1",array($oa_group));
echo HtmlInput::title_box(_('Détail'), "anc_detail_op_div");
echo $row['str_date'],' ',
        h($row['oa_description']),' ',
        h($row['jr_pj_number']);
$a_row=$cn->get_array("select distinct oa_row from operation_analytique where oa_group=$1",array($oa_group));
$a_plan=$cn->get_array("select distinct pa_id,pa_name from operation_analytique join poste_analytique using (po_id) join plan_analytique using (pa_id) where oa_group=$1 order by pa_name",array($oa_group));
$nb_row=count($a_row);
$nb_plan=count($a_plan);
echo '<table class="result">';
echo '<tr>';
echo th(_('Fiche'));
for ( $e=0;$e<$nb_plan;$e++) echo th($a_plan[$e]['pa_name']);
echo th(_('Montant'),'style="text-align:right"');
echo th(_('D/C'));
echo '<tr>';

echo '</tr>';
for ($i=0;$i< $nb_row;$i++) {
    $class=($i%2==0)?"even":"odd";
    echo '<tr class="'.$class.'">';
    // retrieve card
    echo '<td>';
        $f_id=$cn->get_value("select distinct f_id from operation_analytique where oa_group = $1 and oa_row=$2",[$oa_group,$a_row[$i]['oa_row']]);
        $qcode=$cn->get_value("select ad_value from fiche_detail where f_id=$1 and ad_id=$2",[$f_id,ATTR_DEF_QUICKCODE] );
        echo $qcode;
    echo '</td>';
    for ( $e = 0;$e<$nb_plan;$e++) {
        $detail_row=$cn->get_row("select po_name , oa_amount,oa_positive ,oa_debit
                from operation_analytique 
                join poste_analytique using (po_id) 
                join plan_analytique using (pa_id) 
                where 
                    oa_group=$1 
                    and oa_row=$2 
                    and pa_id=$3",array($oa_group,$a_row[$i]['oa_row'],$a_plan[$e]['pa_id']));
        echo td($detail_row['po_name']);
    }
    $amount=$detail_row['oa_amount'];
    if ( $detail_row['oa_positive']=="N") {$amount=bcmult($amount,-1);}
    echo td($amount,'style="text-align:right"');
    $debit=($detail_row['oa_debit'] == 'f')?"C":"D";
    echo td($debit);
    echo '</tr>';
    
}
echo '</table>';
echo '<ul class="aligned-block">';
echo '<li>',HtmlInput::button_close("anc_detail_op_div");
echo '</ul >';