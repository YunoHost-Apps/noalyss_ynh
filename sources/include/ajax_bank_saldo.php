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
 * - l for ledger
 * - gDossier
 * Must return at least tva, htva and tvac

 */

/*!\file
 * \brief get the saldo of a account
 * the get variable are :
 *  - l the jrn id
 *  - ctl the ctl where to get the quick_code
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';
require_once NOALYSS_INCLUDE.'/class_fiche.php';
extract($_GET);
/* check the parameters */
foreach ( array('j','ctl') as $a )
{
    if ( ! isset(${$a}) )
    {
        echo "missing $a";
        return;
    }
}

if ( $g_user->check_jrn($_GET['j'])=='X' ) return '{"saldo":"0"}';
/*  make a filter on the exercice */

$filter_year="  j_tech_per in (select p_id from parm_periode ".
             "where p_exercice='".$g_user->get_exercice()."')";


$id=$cn->get_value('select jrn_def_bank from jrn_def where jrn_def_id=$1',array($_GET['j']));
$acc=new Fiche($cn,$id);

$res=$acc->get_bk_balance($filter_year." and ( trim(jr_pj_number) != '' and jr_pj_number is not null)" );


if ( empty($res) ) return '{"saldo":"0"}';
$solde=$res['solde'];
if ( $res['debit'] < $res['credit'] ) $solde=$solde*(-1);

//header("Content-type: text/html; charset: utf8",true);
echo '{"saldo":"'.$solde.'"}';



?>

