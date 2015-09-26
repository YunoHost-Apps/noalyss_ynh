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
 * \brief create GL comptes as PDF
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
include_once('class_acc_account_ledger.php');
include_once('ac_common.php');
require_once NOALYSS_INCLUDE.'/class_database.php';
include_once('class_impress.php');
require_once NOALYSS_INCLUDE.'/class_own.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_user.php';

header('Content-type: application/csv');
header('Content-Disposition: attachment;filename="gl_comptes.csv"',FALSE);
header('Pragma: public');


$gDossier=dossier::id();

/* Security */
$cn=new Database($gDossier);


extract($_GET);

if ( isset($poste_id) && strlen(trim($poste_id)) != 0 && isNumber($poste_id) )
{
    if ( isset ($poste_fille) )
    {
        $parent=$poste_id;
        $a_poste=$cn->get_array("select pcm_val from tmp_pcmn where pcm_val::text like '$parent%' order by pcm_val::text");
    }
    elseif ( $cn->count_sql('select * from tmp_pcmn where pcm_val='.sql_string($poste_id)) != 0 )
    {
        $a_poste=array('pcm_val' => $poste_id);
    }
}
else
{
  $cond_poste='';
  $sql="select pcm_val from tmp_pcmn ";
    if ($from_poste != '')
      {
	$cond_poste = '  where ';
	$cond_poste .=" pcm_val >= upper ('".Database::escape_string($from_poste)."')";
      }

    if ( $to_poste != '')
      {
	if  ( $cond_poste == '')
	  {
	    $cond_poste =  " where pcm_val <= upper ('".Database::escape_string($to_poste)."')";
	  }
	else
	  {
	    $cond_poste.=" and pcm_val <= upper ('".Database::escape_string($to_poste)."')";
	  }
      }

    $sql=$sql.$cond_poste.'  order by pcm_val::text';

    $a_poste=$cn->get_array($sql);

}

if ( count($a_poste) == 0 )
{
    echo 'Rien à rapporter.';
    printf("\n");
    exit;
}

// Header
$header = array( "Date", "Référence", "Libellé", "Pièce","Lettrage", "Débit", "Crédit", "Solde" );

$l=(isset($_GET['letter']))?2:0;
$s=(isset($_REQUEST['solded']))?1:0;

foreach ($a_poste as $poste)
{


  $Poste=new Acc_Account_Ledger($cn,$poste['pcm_val']);

  $array1=$Poste->get_row_date($from_periode,$to_periode,$l,$s);
  // don't print empty account
  if ( count($array1) == 0 )
    {
      continue;
    }
  $array=$array1[0];
  $tot_deb=$array1[1];
  $tot_cred=$array1[2];

    // don't print empty account
    if ( count($array) == 0 )
    {
        continue;
    }

    echo sprintf("%s - %s ",$Poste->id,$Poste->get_name());
    printf("\n");

    for($i=0;$i<count($header);$i++)
        echo $header[$i].";";
    printf("\n");

    $solde = 0.0;
    $solde_d = 0.0;
    $solde_c = 0.0;
    $current_exercice="";
    foreach ($Poste->row as $detail)
    {

        /*
               [0] => 1 [jr_id] => 1
               [1] => 01.02.2009 [j_date_fmt] => 01.02.2009
               [2] => 2009-02-01 [j_date] => 2009-02-01
               [3] => 0 [deb_montant] => 0
               [4] => 12211.9100 [cred_montant] => 12211.9100
               [5] => Ecriture douverture [description] => Ecriture douverture
               [6] => Opération Diverses [jrn_name] => Opération Diverses
               [7] => f [j_debit] => f
               [8] => 17OD-01-1 [jr_internal] => 17OD-01-1
               [9] => ODS1 [jr_pj_number] => ODS1 ) 1
         */
/*
             * separation per exercice
             */
        if ( $current_exercice == "") $current_exercice=$detail['p_exercice'];

        if ( $current_exercice != $detail['p_exercice']) {
            echo ";";
            echo '"'.$current_exercice.'";';
            echo ";";
            echo ";";
            echo 'Total du compte '.$Poste->id.";";
            echo ($solde_d  > 0 ? nb($solde_d)  : '').";";
            echo ($solde_c  > 0 ? nb( $solde_c)  : '').";";
            echo nb(abs($solde_c-$solde_d)).";";
            echo ($solde_c > $solde_d ? 'C' : 'D').";";
            printf("\n");
            printf("\n");
            /*
            * reset total and current_exercice
            */
            $current_exercice=$detail['p_exercice'];
            $solde = 0.0;
            $solde_d = 0.0;
            $solde_c = 0.0;

        }
        if ($detail['cred_montant'] > 0)
        {
            $solde   -= $detail['cred_montant'];
            $solde_c += $detail['cred_montant'];
        }
        if ($detail['deb_montant'] > 0)
        {
            $solde   += $detail['deb_montant'];
            $solde_d += $detail['deb_montant'];
        }

        echo $detail['j_date_fmt'].";";
        echo $detail['jr_internal'].";";
        echo $detail['description'].";";
        echo $detail['jr_pj_number'].";";
        if ($detail['letter'] == -1) { echo ';'; } else { echo $detail['letter'].";";}
        echo ($detail['deb_montant']  > 0 ? nb($detail['deb_montant'])  : '').";";
        echo ($detail['cred_montant'] > 0 ? nb($detail['cred_montant']) : '').";";
        echo nb(abs($solde)).";";
		echo $Poste->get_amount_side($solde);
        printf("\n");

    }


    echo ";";
    echo '"'.$current_exercice.'";';
    echo ";";
    echo ";";
    echo 'Total du compte '.$Poste->id.";";
    echo ($solde_d  > 0 ? nb($solde_d)  : '').";";
    echo ($solde_c  > 0 ? nb( $solde_c)  : '').";";
    echo nb(abs($solde_c-$solde_d)).";";
    echo ($solde_c > $solde_d ? 'C' : 'D').";";
    printf("\n");
    printf("\n");
}

exit;

?>
