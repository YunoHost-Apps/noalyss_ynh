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

/**
 *  Parent class for the print_ledger class
 *
 * @author danydb
 */
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/class/print_ledger_detail.class.php';
require_once NOALYSS_INCLUDE.'/class/print_ledger_simple.class.php';
require_once NOALYSS_INCLUDE.'/class/print_ledger_simple_without_vat.class.php';
require_once NOALYSS_INCLUDE.'/class/print_ledger_fin.class.php';
require_once NOALYSS_INCLUDE.'/class/print_ledger_misc.class.php';
require_once NOALYSS_INCLUDE.'/class/print_ledger_detail_item.class.php';

/**
 * @brief Strategie class for the print_ledger class
 * 
 */
class Print_Ledger {

    /**
     * Create an object Print_Ledger* depending on $p_type_export ( 0 => accounting
     * 1-> one row per operation 2-> detail of item)
     * @param type $cn
     * @param type $p_type_export
     * @param type $p_format_output CSV or PDF
     * @param Acc_Ledger $ledger
     */
    static function factory(Database $cn, $p_type_export, $p_format_output, Acc_Ledger $p_ledger) {
        /**
         * For PDF output
         */
        if ($p_format_output == 'PDF') {
            switch ($p_type_export) {
                case 'D':
                    $own=new Noalyss_Parameter_Folder($cn);
                    $jrn_type=$p_ledger->get_type();
                    //---------------------------------------------
                    // Detailled Printing (accounting )
                    //---------------------------------------------
                    if ($jrn_type=='ACH'||$jrn_type=='VEN')
                    {
                        if (
                                ($jrn_type=='ACH'&&$cn->get_value('select count(qp_id) from quant_purchase')
                                ==0)||
                                ($jrn_type=='VEN'&&$cn->get_value('select count(qs_id) from quant_sold')
                                ==0)
                        )
                        {
                            $pdf=new Print_Ledger_Simple_without_vat($cn,
                                    $p_ledger);
                            $pdf->set_error(_('Ce journal ne peut être imprimé en mode simple'));
                            return $pdf;
                        }
                        if ($own->MY_TVA_USE=='Y')
                        {
                            $pdf=new Print_Ledger_Simple($cn, $p_ledger);
                            return $pdf;
                        }
                        if ($own->MY_TVA_USE=='N')
                        {
                            $pdf=new Print_Ledger_Simple_without_vat($cn,
                                    $p_ledger);
                            return $pdf;
                        }
                    }
                    else
                        return new Print_Ledger_Detail($cn, $p_ledger);
                    break;

                case 'L':
                    //----------------------------------------------------------------------
                    // Simple Printing Purchase Ledger
                    //---------------------------------------------------------------------
                    $own=new Noalyss_Parameter_Folder($cn);
                    $jrn_type=$p_ledger->get_type();


                    if ($jrn_type=='ACH'||$jrn_type=='VEN')
                    {
                        if (
                                ($jrn_type=='ACH'&&$cn->get_value('select count(qp_id) from quant_purchase')
                                ==0)||
                                ($jrn_type=='VEN'&&$cn->get_value('select count(qs_id) from quant_sold')
                                ==0)
                        )
                        {
                            $pdf=new Print_Ledger_Simple_without_vat($cn,
                                    $p_ledger);
                            $pdf->set_error(_('Ce journal ne peut être imprimé en mode simple'));
                            return $pdf;
                        }
                        if ($own->MY_TVA_USE=='Y')
                        {
                            $pdf=new Print_Ledger_Simple($cn, $p_ledger);
                            return $pdf;
                        }
                        if ($own->MY_TVA_USE=='N')
                        {
                            $pdf=new Print_Ledger_Simple_without_vat($cn,
                                    $p_ledger);
                            return $pdf;
                        }
                    }

                    if ($jrn_type=='FIN')
                    {
                        $pdf=new Print_Ledger_Financial($cn, $p_ledger);
                        return $pdf;
                    }
                    if ($jrn_type=='ODS'||$p_ledger->id==0)
                    {
                        $pdf=new Print_Ledger_Misc($cn, $p_ledger);
                        return $pdf;
                    }
                    break;
                case 'E':
                    /**********************************************************
                     * Print Detail Operation + Item
                     * ********************************************************* */
                    $own=new Noalyss_Parameter_Folder($cn);
                    $jrn_type=$p_ledger->get_type();
                    if ($jrn_type=='FIN')
                    {
                        $pdf=new Print_Ledger_Financial($cn, $p_ledger);
                        return $pdf;
                        ;
                    }
                    if ($jrn_type=='ODS'||$p_ledger->id==0)
                    {
                        $pdf=new Print_Ledger_Detail($cn, $p_ledger);
                        return $pdf;
                    }
                    if (
                            ($jrn_type=='ACH'&&$cn->get_value('select count(qp_id) from quant_purchase')
                            ==0)||
                            ($jrn_type=='VEN'&&$cn->get_value('select count(qs_id) from quant_sold')
                            ==0)
                    )
                    {
                        $pdf=new Print_Ledger_Simple_without_vat($cn, $p_ledger);
                        $pdf->set_error('Ce journal ne peut être imprimé en mode simple');
                        return $pdf;
                    }
                    $pdf=new Print_Ledger_Detail_Item($cn, $p_ledger);
                    return $pdf;
                case 'A':
                    /***********************************************************
                     * Accounting
                     */
                    $pdf=new Print_Ledger_Detail($cn, $p_ledger);
                    return $pdf;
                    break;
            } // end switch
        } // end $p_format == PDF
    }

// end function
    /**
     * @brief find all the active ledger  for the exerice of the periode
     * and readable by the current user
     * @global type $g_user
     * @param int  $get_from_periode
     * @return array of ledger id
     */
    static function available_ledger($get_from_periode)
    {
        global $g_user;
        $cn=Dossier::connect();
        // Find periode 
        $periode=new Periode($cn, $get_from_periode);
        $exercice=$periode->get_exercice($get_from_periode);

        if ($g_user->Admin()==0&&$g_user->is_local_admin()==0&&$g_user->get_status_security_ledger()
                ==1)
        {
            $sql="select jrn_def_id 
                 from jrn_def join jrn_type on jrn_def_type=jrn_type_id
                 join user_sec_jrn on uj_jrn_id=jrn_def_id
                 where
                 uj_login=$1
                 and uj_priv in ('R','W')
                 and ( jrn_enable=1 
                        or 
                        exists (select 1 from jrn where  jr_def_id=jrn_def_id and jr_tech_per in (select p_id from parm_periode where p_exercice=$2)))
                         order by jrn_def_name
                 ";
            $a_jrn=$cn->get_array($sql, array($g_user->login, $exercice));
        }
        else
        {
            $a_jrn=$cn->get_array("select jrn_def_id
                                 from jrn_def join jrn_type on jrn_def_type=jrn_type_id
                                 where
                                 jrn_enable=1 or exists(select 1 from jrn where  jr_def_id=jrn_def_id and  jr_tech_per in (select p_id from parm_periode where p_exercice=$1))
                                                         order by jrn_def_name
                                                         ", [$exercice]);
        }
        $a=[];
        $nb_jrn=count($a_jrn);
        for ($i=0; $i<$nb_jrn; $i++)
        {
            $a[]=$a_jrn[$i]['jrn_def_id'];
        }
        return $a;
    }

}

?>
