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
/*! \file
 * \brief Send the poste list in csv
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
include_once("ac_common.php");
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_fiche.php';
$f_id=HtmlInput::default_value_request("f_id", "-");
if ( $f_id == "-") {
     throw new Exception ('Invalid parameter');
}
require_once NOALYSS_INCLUDE.'/class_dossier.php';
$gDossier=dossier::id();

/* Admin. Dossier */
$cn=new Database($gDossier);


$Fiche=new Fiche($cn,$f_id);
$qcode=$Fiche->get_quick_code();

header('Content-type: application/csv');

header('Pragma: public');
header('Content-Disposition: attachment;filename="fiche-'.$qcode.'.csv"',FALSE);
$Fiche->getName();
list($array,$tot_deb,$tot_cred)=$Fiche->get_row_date(
                                    $_GET['from_periode'],
                                    $_GET['to_periode'],
                                    $_GET['ople']
                                );
if ( count($Fiche->row ) == 0 )
{
    echo "Aucune donnée";
    return;
}


if ( ! isset ($_REQUEST['oper_detail']))
{
    echo '"Qcode";'.
    "\"Date\";".
      "\"n° pièce\";".
    "\"Code interne\";".
    '"Code journal";'.
    '"Nom journal";'.
    "\"Description\";".
    "\"Débit\";".
    "\"Crédit\";".
    "\"Prog.\";".
    "\"Let.\""     ;
    printf("\n");
    $progress=0;
    $current_exercice="";
    $tot_deb=0;$tot_cred=0; 
    bcscale(2);
    foreach ( $Fiche->row as $op )
    {
        /*
             * separation per exercice
             */
            if ( $current_exercice == "") $current_exercice=$op['p_exercice'];
            
            if ( $current_exercice != $op['p_exercice']) {
                $solde_type=($tot_deb>$tot_cred)?"solde débiteur":"solde créditeur";
                $diff=abs($tot_deb-$tot_cred);
                printf(
                     ";;;".
                     '"'._('total').'";'.
                     '"'.$current_exercice.'";;'.
                '"'."$solde_type".'"'.";".
                nb($tot_deb).";".
                nb($tot_cred).";".
                nb($diff).";"."\n");
                /*
                * reset total and current_exercice
                */
                $progress=0;
                $current_exercice=$op['p_exercice'];
                $tot_deb=0;$tot_cred=0;    
            }
        $diff=bcsub($op['deb_montant'],$op['cred_montant']);
        $progress=bcadd($progress,$diff);
        $tot_deb=bcadd($tot_deb,$op['deb_montant']);
        $tot_cred=bcadd($tot_cred,$op['cred_montant']);
        echo '"'.$op['j_qcode'].'";'.
	  '"'.$op['j_date_fmt'].'"'.";".
	  '"'.$op['jr_pj_number'].'"'.";".
	  '"'.$op['jr_internal'].'"'.";".
	  '"'.$op['jrn_def_code'].'"'.";".
	  '"'.$op['jrn_def_name'].'"'.";".
	  '"'.$op['description'].'"'.";".
	  nb($op['deb_montant']).";".
	  nb($op['cred_montant']).";".
	  nb(abs($progress)).';'.
	  '"'.(($op['letter']==-1)?'':strtoupper(base_convert($op['letter'],10,36))).'"';
        printf("\n");

    }
}
else
{
    echo '"Poste";"Qcode";"date";"ref";"internal";';
    echo    "\"Description\";".
    "\"Montant\";".
    "\"D/C\"";

    printf("\r\n");

    foreach ( $Fiche->row as $op )
    {
        $acc=new Acc_Operation($cn);
        $acc->jr_id=$op['jr_id'];
        $result= $acc->get_jrnx_detail();

        foreach ( $result as $r)
        {
            printf('"%s";"%s";"%s";"%s";"%s";%s;%s;"%s"',
                   $r['j_poste'],
                   $r['j_qcode'],
                   $r['jr_date'],
		   $op['jr_pj_number'],
                   $r['jr_internal'],
                   $r['description'],
                   nb($r['j_montant']),
                   $r['debit']);
            printf("\r\n");

        }



    }
}
$solde_type=($tot_deb>$tot_cred)?"solde débiteur":"solde créditeur";
$diff=abs($tot_deb-$tot_cred);
printf(
    '"'."$solde_type".'"'.";".
    nb($diff).";".
    nb($tot_deb).";".
    nb($tot_cred)."\n");

exit;
?>
