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
 *
 *
 * \brief show bank saldo
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once  NOALYSS_INCLUDE.'/class_acc_parm_code.php';
    echo '<div class="content">';
    $fiche=new Fiche($cn);

    $array=$fiche->get_bk_account();

    echo '<div class="content">';
    echo dossier::hidden();
    echo _('Filtre :').HtmlInput::filter_table("fin_saldo_tb", '0,1,2,3', '1');
    echo '<table class="sortable"  style="margin-left:10%;width:80%" class="result" id="fin_saldo_tb">';
    echo tr(th('Quick Code',' class=" sorttable_sorted_reverse"',HtmlInput::infobulle(17).'<span id="sorttable_sortrevind">&nbsp;&blacktriangle;</span>')
            .th('Compte en banque',' style="text-align:left"')
            .th('Journal',' style="text-align:center"')
            .th('Description',' style="text-align:center"')
            .th('solde opération',' style="text-align:right" class="sorttable_numeric"')
	    .th('solde extrait/relevé',' style="text-align:right" class="sorttable_numeric"')
	    .th('différence',' style="text-align:right" class="sorttable_numeric"')
            );
    // Filter the saldo
    //  on the current year
    $filter_year="  j_tech_per in (select p_id from parm_periode where  p_exercice='".$g_user->get_exercice()."')";
    // for highligting tje line
    $idx=0;
    bcscale(2);
    $tot_extrait=0;$tot_diff=0;$tot_operation=0;
    // for each account
    for ( $i = 0; $i < count($array);$i++)
    {
		if ( $array[$i]->id==0) {
			echo '<tr >';
			echo td(h2("Journal mal configuré",' class="error" '),' colspan="5" style="width:auto" ');
			echo '</tr>';
			continue;
		}
        // get the saldo
        $m=$array[$i]->get_solde_detail($filter_year);

        $solde=$m['debit']-$m['credit'];

        // print the result if the saldo is not equal to 0
        if ( $m['debit'] != 0.0 || $m['credit'] != 0.0)
        {
            /*  get saldo for not reconcilied operations  */
            $saldo_not_reconcilied=$array[$i]->get_bk_balance($filter_year." and (trim(jr_pj_number) ='' or jr_pj_number is null)" );

            /*  get saldo for reconcilied operation  */

	    $saldo_reconcilied=$array[$i]->get_bk_balance($filter_year." and ( trim(jr_pj_number) != '' and jr_pj_number is not null)" );

            if ( $idx%2 != 0 )
                $odd="odd";
            else
                $odd="even";

            $idx++;
            echo "<tr class=\"$odd\">";
            echo "<TD >".
            IButton::history_card($array[$i]->id,$array[$i]->strAttribut(ATTR_DEF_QUICKCODE)).
            "</TD>";

	    $saldo_rec=bcsub($saldo_reconcilied['debit'],$saldo_reconcilied['credit']);
	    $diff=bcsub($saldo_not_reconcilied['debit'],$saldo_not_reconcilied['credit']);
            echo "<TD >".
            $array[$i]->strAttribut(ATTR_DEF_NAME).
            "</TD>".
             td(h($array[$i]->ledger_name)).
             td(h($array[$i]->ledger_description)).
            '<TD class="sorttable_numeric" sorttable_customkey="'.$solde.'"  style="text-align:right">'.
	      nbm($solde).
            "</TD>".
            '<TD class="sorttable_numeric" sorttable_customkey="'.$saldo_rec.'"  style="text-align:right">'.
	      nbm($saldo_rec).
            "</TD>".
            '<TD class="sorttable_numeric" sorttable_customkey="'.$diff.'"  style="text-align:right">'.
	      nbm($diff).
            "</TD>".
            "</TR>";
            $tot_extrait=bcadd($tot_extrait,$solde);
            $tot_operation=bcadd($tot_operation,$saldo_rec);
            $tot_diff=bcadd($tot_diff,$diff);
        }
    }// for
    echo '<tfoot>';
    echo '<tr class="highlight">';
    echo td('');
    echo td('');
    echo td('');
    echo td(' TOTAUX ','style="font-weight:bold"');
    echo td(nbm($tot_extrait),'style="font-weight:bold" class="num"');
    echo td(nbm($tot_operation),' style="font-weight:bold" class="num"');
    echo td(nbm($tot_diff),' style="font-weight:bold" class="num"');
    echo '</tr>';
    
    echo '</tfoot>';
    echo "</table>";
    echo "</div>";
    return;
?>
