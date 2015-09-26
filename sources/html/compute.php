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
 * \brief respond ajax request, the get contains
 *  the value :
 * - c for qcode
 * - t for tva_id -1 if there is no TVA to compute
 * - p for price
 * - q for quantity
 * - n for number of the ctrl
 * - gDossier
 * Must return at least tva, htva and tvac
 */
require_once '../include/constant.php';
require_once  NOALYSS_INCLUDE.'/class_database.php';
require_once  NOALYSS_INCLUDE.'/class_acc_compute.php';
require_once  NOALYSS_INCLUDE.'/class_dossier.php';
require_once  NOALYSS_INCLUDE.'/class_acc_tva.php';
require_once  NOALYSS_INCLUDE.'/class_user.php';

// Check if the needed field does exist
extract ($_GET);
foreach (array('t','c','p','q','n','gDossier') as $a)
{
    if ( ! isset (${$a}) )
    {
        echo "error $a is not set ";
        exit();
    }

}
$cn=new Database(dossier::id());
$User=new User($cn);
$User->Check();
// Retrieve the rate of vat, it $t == -1 it means no VAT
if ( $t != -1 && isNumber($t) == 1 )
{
    $tva_rate=new Acc_Tva($cn);
    $tva_rate->set_parameter('id',$t);
    /**
     *if the tva_rate->load failed we don't compute tva
     */
    if ( $tva_rate->load() != 0 )
    {
        $tva_rate->set_parameter('rate',0);
    }
}

$total=new Acc_Compute();
bcscale(4);
$amount=round(bcmul($p,$q),2);
$total->set_parameter('amount',$amount);
if ( $t != -1 && isNumber($t) == 1 )
{
    $total->set_parameter('amount_vat_rate',$tva_rate->get_parameter('rate'));
    $total->compute_vat();
    if ($tva_rate->get_parameter('both_side')== 1) $total->set_parameter('amount_vat', 0);
    $tvac=($tva_rate->get_parameter('rate') == 0 || $tva_rate->get_parameter('both_side')== 1) ? $amount : bcadd($total->get_parameter('amount_vat'),$amount);
    header("Content-type: text/html; charset: utf8",true);
    echo '{"ctl":"'.$n.'","htva":"'.$amount.'","tva":"'.$total->get_parameter('amount_vat').'","tvac":"'.$tvac.'"}';
}
else
{
    /* there is no vat to compute */
    header("Content-type: text/html; charset: utf8",true);
    echo '{"ctl":"'.$n.'","htva":"'.$amount.'","tva":"NA","tvac":"'.$amount.'"}';
}
?>

