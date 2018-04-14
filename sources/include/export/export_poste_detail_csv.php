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
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_account_ledger.class.php';
require_once  NOALYSS_INCLUDE.'/class/acc_operation.class.php';
require_once NOALYSS_INCLUDE.'/lib/noalyss_csv.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_operation.class.php';

$http=new HttpInput();

$r_poste=$http->request("poste_id");
$from_periode=$http->request("from_periode");
$to_periode=$http->request("to_periode");
$ople=$http->request("ople");

$export=new Noalyss_Csv(_('poste').'_'.$r_poste);

require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
$gDossier=dossier::id();

/* Admin. Dossier */
$cn=Dossier::connect();

if ( isset ( $_REQUEST['poste_fille']) )
{ //choisit de voir tous les postes
  $a_poste=$cn->get_array("select pcm_val from tmp_pcmn where pcm_val::text like $1||'%'",array($r_poste));
}
else
{
  $a_poste=$cn->get_array("select pcm_val from tmp_pcmn where pcm_val = $1",array($r_poste));
}
bcscale(2);
$export->send_header();
if ( ! isset ($_REQUEST['oper_detail']))
{
    /*
     * Without detail for accounting
     */
    if ( count($a_poste) == 0 )
        exit;

    foreach ($a_poste as $pos)
    {
        $Poste=new Acc_Account_Ledger($cn,$pos['pcm_val']);
        $operation=new Acc_Operation($cn);
        $name=$Poste->get_name();
        list($array,$tot_deb,$tot_cred)=$Poste->get_row_date( $from_periode,
							      $to_periode,
							      $ople
							      );
        if ( count($Poste->row ) == 0 )
            continue;
        $title=array();
        
        $title[]=_("Date");
        $title[]=_("Poste");
        $title[]=_("n° pièce");
        $title[]=_("Code journal");
        $title[]=_("Nom journal");
        $title[]=_("QuickCode");
        $title[]=_("Lib.");
        $title[]=_("Interne");
        $title[]=_("Tiers");
        $title[]=_("Description");
        $title[]=_("Débit");
        $title[]=_("Crédit");
        $title[]=_("Prog.");
	$title[]=_("Let.");
        $export->write_header($title);

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
                $export->add("");
                $export->add("");
                $export->add("");
                $export->add(_("total"));
                $export->add($current_exercice);
                $export->add($solde_type);
                $export->add("");
                $export->add("");
                $export->add("");
                $export->add("");
                
                $export->add($tot_deb,"number");
                $export->add($tot_cred,"number");
                $export->add($diff,"number");
                $export->write();
                /*
                * reset total and current_exercice
                */
                $prog=0;
                $current_exercice=$op['p_exercice'];
                $tot_deb=0;$tot_cred=0;    
            }
            $tiers=$operation->find_tiers($op['jr_id'],$op['j_id'],$op['j_qcode']);
           
            $tot_deb=bcadd($tot_deb,$op['deb_montant']);
            $tot_cred=bcadd($tot_cred,$op['cred_montant']);
            $diff=bcsub($op['deb_montant'],$op['cred_montant']);
            $prog=bcadd($prog,$diff);
            $export->add($op['j_date_fmt']);
            $export->add($pos['pcm_val']);
	    $export->add($op['jr_pj_number']);
	    $export->add($op['jrn_def_code']);
	    $export->add($op['jrn_def_name']);
	    $export->add($op['j_qcode']);
            $export->add($name);
            $export->add($op['jr_internal']);
            $export->add($tiers);
            $export->add($op['description']);
            $export->add($op['deb_montant'],"number");
            $export->add($op['cred_montant'],"number");
            $export->add(abs($prog),"number");
            $export->add((($op['letter']!=-1)?strtoupper(base_convert($op['letter'],10,36)):""));
            
            $export->write();


        }
        $solde_type=($tot_deb>$tot_cred)?"solde débiteur":"solde créditeur";
        $diff=abs($tot_deb-$tot_cred);
        $export->add("");
        $export->add("");
        $export->add("");
        $export->add(_("total"));
        $export->add($current_exercice);
        $export->add($solde_type);
        $export->add("");
        $export->add("");
        $export->add("");
        $export->add("");

        $export->add($tot_deb,"number");
        $export->add($tot_cred,"number");
        $export->add($diff,"number");
        $export->write();
    }
}
else
{
    /* 
     * detail of all operation 
     */
    if ( count($a_poste) == 0 )
        exit;

    foreach ($a_poste as $pos)
    {
        $Poste=new Acc_Account_Ledger($cn,$pos['pcm_val']);
        $Poste->get_name();
        list($array,$tot_deb,$tot_cred)=$Poste->get_row_date($from_periode,
							      $to_periode,
							      $ople
                                                            );
        if ( count($Poste->row ) == 0 )
            continue;
        $title=array();
        $title[]=_("Poste");
        $title[]=_("Lib.");
        $title[]=_("QuickCode");
        $title[]=_("Interne");
        $title[]=_("Date");
        $title[]=_("Tiers");
        $title[]=_("Description");
        $title[]=_("Montant");
        $title[]=_("D/C");
        $export->write_header($title);
        $operation=new Acc_Operation($cn);


        foreach ( $Poste->row as $a )
        {
            $op=new Acc_Operation($cn);
            $op->jr_id=$a['jr_id'];
            $result=$op->get_jrnx_detail();
            foreach ( $result as $r)
            {
                $tiers=$operation->find_tiers($r['jr_id'], $r['j_id'], $r['j_qcode']);
                $export->add($r['j_poste']);
                $export->add($r['pcm_lib']);
                $export->add($r['j_qcode']);
                $export->add($r['jr_internal']);
                $export->add($r['jr_date']);
                $export->add($tiers);
                $export->add($a['description']);
                $export->add($a['jr_pj_number']);
                $export->add($r['j_montant'],"number");
                $export->add($r['debit']);
                $export->write();

            }



        }
    }
    exit;
}
?>
