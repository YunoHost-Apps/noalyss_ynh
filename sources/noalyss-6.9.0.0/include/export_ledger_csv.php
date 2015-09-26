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
 * \brief Send a ledger in CSV format
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
$fDate = date('dmy-Hi');
header('Pragma: public');
header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="jrn-'.$fDate.'.csv"',FALSE);
include_once ("ac_common.php");
require_once NOALYSS_INCLUDE.'/class_own.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger_sold.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger_purchase.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
$gDossier=dossier::id();

require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';

/*
 * Variable from $_GET
 */
$get_jrn=HtmlInput::default_value_get('jrn_id', -1);
$get_option=HtmlInput::default_value_get('p_simple', -1);
$get_from_periode=  HtmlInput::default_value_get('from_periode', null);
$get_to_periode=HtmlInput::default_value_get('to_periode', NULL);

//--- Check validity
if ( $get_jrn ==-1  || $get_option == -1 || $get_from_periode == null || $get_to_periode == null)
{
    die (_('Options invalides'));
}


require_once  NOALYSS_INCLUDE.'/class_user.php';
$g_user->Check();
$g_user->check_dossier($gDossier);

//----------------------------------------------------------------------------
// $get_jrn == 0 when request for all ledger, in that case, we must filter
// the legder with the security in Acc_Ledger::get_row
//----------------------------------------------------------------------------
if ($get_jrn!=0 &&  $g_user->check_jrn($get_jrn) =='X')
{
    NoAccess();
    exit();
}

$Jrn=new Acc_Ledger($cn,$get_jrn);

$Jrn->get_name();
$jrn_type=$Jrn->get_type();

//
// With Detail per item which is possible only for VEN or ACH
// 
if ($get_option == 2)
{
    if ($jrn_type != 'ACH' && $jrn_type != 'VEN' || $Jrn->id == 0)
    {
        $get_option = 0;
    }
    else
    {
        switch ($jrn_type)
        {
            case 'VEN':
                $ledger = new Acc_Ledger_Sold($cn, $get_jrn);
                $ret_detail = $ledger->get_detail_sale($get_from_periode, $get_to_periode);
                $a_heading= Acc_Ledger_Sold::heading_detail_sale();
                
                break;
            case 'ACH':
                $ledger = new Acc_Ledger_Purchase($cn, $get_jrn);
                $ret_detail = $ledger->get_detail_purchase($get_from_periode, $get_to_periode);
                $a_heading=  Acc_Ledger_Purchase::heading_detail_purchase();
                break;
            default:
                die(__FILE__ . ":" . __LINE__ . 'Journal invalide');
                break;
        }
        if ($ret_detail == null)
            return;
        $nb = Database::num_row($ret_detail);
        $output=fopen("php://output","w");
        
        for ($i = 0;$i < $nb ; $i++) {
            $row=Database::fetch_array($ret_detail, $i);
            if ( $i == 0 ) {
              fputcsv($output,$a_heading,';');
            }
            $a_row=array();
            for ($j=0;$j < count($row) / 2;$j++) {
                $a_row[]=$row[$j];
            }
            fputcsv($output,$a_row,';');
            unset($a_row);
        }
    }
}
//-----------------------------------------------------------------------------
// Detailled printing
// For miscellaneous legder or all ledgers
//-----------------------------------------------------------------------------
if  ( $get_option == 0 )
{
    $Jrn->get_row( $get_from_periode, $get_to_periode );

    if ( count($Jrn->row) == 0)
        exit;
    foreach ( $Jrn->row as $op )
    {
        // should clean description : remove <b><i> tag and '; char
        $desc=$op['description'];
        $desc=str_replace("<b>","",$desc);
        $desc=str_replace("</b>","",$desc);
        $desc=str_replace("<i>","",$desc);
        $desc=str_replace("</i>","",$desc);
        $desc=str_replace('"',"'",$desc);
        $desc=str_replace(";",",",$desc);

        printf("\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";%s;%s\n",
               $op['j_id'],
               $op['jr_pj_number'],
               $op['internal'],
               $op['j_date'],
               $op['poste'],
               $desc,
               nb($op['deb_montant']),
               nb($op['cred_montant'])
              );

    }
    exit;
}
//-----------------------------------------------------------------------------
// Detail printing for ACH or VEN : 1 row resume the situation with VAT, DNA
// for Misc the amount 
// For Financial only the tiers and the sign of the amount
//-----------------------------------------------------------------------------
if  ($get_option == 1)
{
   
//-----------------------------------------------------
     if ( $jrn_type == 'ODS' || $jrn_type == 'FIN' || $jrn_type=='GL')
       {
          $Row=$Jrn->get_rowSimple($get_from_periode,
                             $get_to_periode,
                             0);
	 printf ('" operation";'.
		 '"Date";'.
		 '"N° Pièce";'.
		 '"Tiers";'.
		 '"commentaire";'.
		 '"internal";'.
		 '"montant";'.
		 "\r\n");
	 foreach ($Row as $line)
	   {

	     echo $line['num'].";";
	     echo $line['date'].";";
	     echo $line['jr_pj_number'].";";
	     echo $Jrn->get_tiers($line['jrn_def_type'],$line['jr_id']).";";
	     echo $line['comment'].";";
	     echo $line['jr_internal'].";";
	     //	  echo "<TD>".$line['pj'].";";
	     // If the ledger is financial :
	     // the credit must be negative and written in red
	     // Get the jrn type
	     if ( $line['jrn_def_type'] == 'FIN' ) {
	       $positive = $cn->get_value("select qf_amount from quant_fin  ".
					  " where jr_id=".$line['jr_id']);

	       echo nb($positive);
	       echo ";";
	     }
	     else
	       {
		 echo nb($line['montant']).";";
	       }

	     printf("\r\n");
	   }
       }

//------------------------------------------------------------------------------
// One line summary with tiers, amount VAT, DNA, tva code ....
// 
//------------------------------------------------------------------------------
    if ( $jrn_type=='ACH' || $jrn_type=='VEN')
    {
        $Row=$Jrn->get_rowSimple($get_from_periode,
                             $get_to_periode,
                             0);
        $cn->prepare('reconcile_date',"select to_char(jr_date,'DD.MM.YY') as str_date,* "
                . "from jrn "
                . "where "
                . "jr_id in (select jra_concerned from jrn_rapt where jr_id = $1 union all select jr_id from jrn_rapt where jra_concerned=$1)");

        $own=new Own($cn);
        $col_tva="";

        if ( $own->MY_TVA_USE=='Y')
        {
            $a_Tva=$cn->get_array("select tva_id,tva_label from tva_rate order by tva_rate,tva_label,tva_id");
            foreach($a_Tva as $line_tva)
            {
                $col_tva.='"Tva '.$line_tva['tva_label'].'";';
            }
        }
        echo '"Date";"Paiement";"operation";"Pièce";"Client/Fourn.";"Commentaire";"inter.";"HTVA";"privé";"DNA";"tva non ded.";"TVA NP";'.$col_tva.'"TVAC";"opérations liées"'."\n\r";
        foreach ($Row as $line)
        {
            printf('"%s";"%s";"%s";"%s";"%s";%s;%s;%s;%s;%s;%s;%s;',
                   $line['date'],
                   $line['date_paid'],
                   $line['num'],
                   $line['jr_pj_number'],
                   $Jrn->get_tiers($line['jrn_def_type'],$line['jr_id']),
                   $line['comment'],
                   $line['jr_internal'],
                   nb($line['HTVA']),
                   nb($line['dep_priv']),
                   nb($line['dna']),
                   nb($line['tva_dna']),
                    nb($line['tva_np'])
                   );
            $a_tva_amount=array();
            //- set all TVA to 0
            foreach ($a_Tva as $l) {
                $t_id=$l["tva_id"];
                $a_tva_amount[$t_id]=0;
            }
            foreach ($line['TVA'] as $lineTVA)
            {
                $idx_tva=$lineTVA[1][0];
                $a_tva_amount[$idx_tva]=$lineTVA[1][2];
             }
            if ($own->MY_TVA_USE == 'Y' )
            {
                foreach ($a_Tva as $line_tva)
                {
                    $a=$line_tva['tva_id'];
                    echo nb($a_tva_amount[$a]).';';
                }
            }
            echo nb ($line['TVAC']);
            /**
             * Retrieve payment if any
             */
             $ret_reconcile=$cn->execute('reconcile_date',array($line['jr_id']));
             $max=Database::num_row($ret_reconcile);
            if ($max > 0) {
                $sep=";";
                for ($e=0;$e<$max;$e++) {
                    $row=Database::fetch_array($ret_reconcile, $e);
                    echo $sep.$row['str_date'].'; '. $row['jr_internal'];
                }
            }
	    printf("\r\n");

        }
    }
}
?>
