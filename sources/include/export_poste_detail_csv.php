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
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_acc_account_ledger.php';
require_once  NOALYSS_INCLUDE.'/class_acc_operation.php';
$fDate=date('dmy-Hi');
header('Pragma: public');
header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="poste-'.$fDate.'-'.$_REQUEST['poste_id'].'.csv"',FALSE);
require_once NOALYSS_INCLUDE.'/class_dossier.php';
$gDossier=dossier::id();

/* Admin. Dossier */
$cn=new Database($gDossier);

if ( isset ( $_REQUEST['poste_fille']) )
{ //choisit de voir tous les postes
  $a_poste=$cn->get_array("select pcm_val from tmp_pcmn where pcm_val::text like $1||'%'",array($_REQUEST["poste_id"]));
}
else
{
  $a_poste=$cn->get_array("select pcm_val from tmp_pcmn where pcm_val = $1",array($_REQUEST['poste_id']));
}
bcscale(2);
if ( ! isset ($_REQUEST['oper_detail']))
{
    if ( count($a_poste) == 0 )
        exit;

    foreach ($a_poste as $pos)
    {
        $Poste=new Acc_Account_Ledger($cn,$pos['pcm_val']);
        $name=$Poste->get_name();
        list($array,$tot_deb,$tot_cred)=$Poste->get_row_date( $_REQUEST['from_periode'],
							      $_REQUEST['to_periode'],
							      $_GET['ople']
							      );
        if ( count($Poste->row ) == 0 )
            continue;

        echo '"Poste";'.
	  '"n° pièce";'.
	  '"Code journal";'.
	  '"Nom journal";'.
	  '"Lib.";'.
        "\"Code interne\";".
        "\"Date\";".
        "\"Description\";".
        "\"Débit\";".
        "\"Crédit\";".
        "\"Prog.\";".
		"\"Let.\"";
        printf("\n");

        $prog=0;
        $current_exercice="";
        $tot_cred=0;
        $tot_deb=0;
        $diff=0;
        foreach ( $Poste->row as $op )
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
                     '"'.$current_exercice.'";'.
                '"'."$solde_type".'"'.";".
                nb($tot_deb).";".
                nb($tot_cred).";".
                nb($diff).";"."\n");
                /*
                * reset total and current_exercice
                */
                $prog=0;
                $current_exercice=$op['p_exercice'];
                $tot_deb=0;$tot_cred=0;    
            }
          $tot_deb=bcadd($tot_deb,$op['deb_montant']);
          $tot_cred=bcadd($tot_cred,$op['cred_montant']);
	  $diff=bcsub($op['deb_montant'],$op['cred_montant']);
	  $prog=bcadd($prog,$diff);
	  echo '"'.$pos['pcm_val'].'";'.
	    '"'.$op['jr_pj_number'].'"'.";".
	    '"'.$op['jrn_def_code'].'"'.";".
	    '"'.$op['jrn_def_name'].'"'.";".
            '"'.$name.'";'.
            '"'.$op['jr_internal'].'"'.";".
            '"'.$op['j_date_fmt'].'"'.";".
            '"'.$op['description'].'";'.
            nb($op['deb_montant']).";".
            nb($op['cred_montant']).";".
            nb(abs($prog)).";".
			(($op['letter']!=-1)?strtoupper(base_convert($op['letter'],10,36)):"");
            printf("\n");


        }
        $solde_type=($tot_deb>$tot_cred)?"solde débiteur":"solde créditeur";
        $diff=abs($tot_deb-$tot_cred);
       printf(
                         ";;;".
                         '"'._('total').'";'.
                         '"'.$current_exercice.'";'.
            '"'."$solde_type".'"'.";".
            nb($tot_deb).";".
            nb($tot_cred).";".
            nb($diff).";"."\n");
    }
}
else
{
    /* detail of all operation */
    if ( count($a_poste) == 0 )
        exit;

    foreach ($a_poste as $pos)
    {
        $Poste=new Acc_Account_Ledger($cn,$pos['pcm_val']);
        $Poste->get_name();
        list($array,$tot_deb,$tot_cred)=$Poste->get_row_date( $_REQUEST['from_periode'],
                                        $_REQUEST['to_periode'],
									      $_GET['ople']
                                                            );
        if ( count($Poste->row ) == 0 )
            continue;

        echo '"Poste";'.
        '"Lib.";'.
        '"QuickCode";'.
        "\"Code interne\";".
        "\"Date\";".
        "\"Description\";".
        "\"Montant\";".
        "\"D/C\"";
        printf("\n");


        foreach ( $Poste->row as $a )
        {
            $op=new Acc_Operation($cn);
            $op->jr_id=$a['jr_id'];
            $result=$op->get_jrnx_detail();
            foreach ( $result as $r)
            {
                printf('"%s";"%s";"%s";"%s";"%s";"%s";"%s";%12.2f;"%s"',
                       $r['j_poste'],
                       $r['pcm_lib'],
                       $r['j_qcode'],
                       $r['jr_internal'],
                       $r['jr_date'],
                       $a['description'],
                       $a['jr_pj_number'],
                       nb($r['j_montant']),
                       $r['debit']);
                printf("\r\n");

            }



        }
    }
    exit;
}
?>
