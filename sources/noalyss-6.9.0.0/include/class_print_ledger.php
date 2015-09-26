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
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_print_ledger_detail.php';
require_once NOALYSS_INCLUDE.'/class_print_ledger_simple.php';
require_once NOALYSS_INCLUDE.'/class_print_ledger_simple_without_vat.php';
require_once NOALYSS_INCLUDE.'/class_print_ledger_fin.php';
require_once NOALYSS_INCLUDE.'/class_print_ledger_misc.php';
require_once NOALYSS_INCLUDE.'/class_print_ledger_detail_item.php';

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
                case 0:
                    //---------------------------------------------
                    // Detailled Printing (accounting )
                    //---------------------------------------------
                    return new Print_Ledger_Detail($cn, $p_ledger);
                    break;

                case 1:
                    //----------------------------------------------------------------------
                    // Simple Printing Purchase Ledger
                    //---------------------------------------------------------------------
                    $own = new Own($cn);
                    $jrn_type = $p_ledger->get_type();


                    if ($jrn_type == 'ACH' || $jrn_type == 'VEN') {
                        if (
                                ($jrn_type == 'ACH' && $cn->get_value('select count(qp_id) from quant_purchase') == 0) ||
                                ($jrn_type == 'VEN' && $cn->get_value('select count(qs_id) from quant_sold') == 0)
                        ) {
                            $pdf = new Print_Ledger_Simple_without_vat($cn, $p_ledger);
                            $pdf->set_error(_('Ce journal ne peut être imprimé en mode simple'));
                            return $pdf;
                        }
                        if ($own->MY_TVA_USE == 'Y') {
                            $pdf = new Print_Ledger_Simple($cn, $p_ledger);
                            return $pdf;
                        }
                        if ($own->MY_TVA_USE == 'N') {
                            $pdf = new Print_Ledger_Simple_without_vat($cn, $p_ledger);
                            return $pdf;
                        }
                    }

                    if ($jrn_type == 'FIN') {
                        $pdf = new Print_Ledger_Financial($cn, $p_ledger);
                        return $pdf;
                    }
                    if ($jrn_type == 'ODS' || $p_ledger->id == 0) {
                        $pdf = new Print_Ledger_Misc($cn, $p_ledger);
                        return $pdf;
                    }
                    break;
                case 2:
                    /**********************************************************
                     * Print Detail Operation + Item
                     ********************************************************** */
                    $own = new Own($cn);
                    $jrn_type = $p_ledger->get_type();
                    if ($jrn_type == 'FIN') {
                        $pdf = new Print_Ledger_Financial($cn, $p_ledger);
                        return $pdf;
                        ;
                    }
                    if ($jrn_type == 'ODS' || $p_ledger->id == 0) {
                        $pdf = new Print_Ledger_Misc($cn, $p_ledger);
                        return $pdf;
                    }
                    if (
                            ($jrn_type == 'ACH' && $cn->get_value('select count(qp_id) from quant_purchase') == 0) ||
                            ($jrn_type == 'VEN' && $cn->get_value('select count(qs_id) from quant_sold') == 0)
                    ) {
                        $pdf = new Print_Ledger_Simple_without_vat($cn, $p_ledger);
                        $pdf->set_error('Ce journal ne peut être imprimé en mode simple');
                        return $pdf;
                    }
                    $pdf = new Print_Ledger_Detail_Item($cn,$p_ledger);
                    return $pdf;
                    
            } // end switch
        } // end $p_format == PDF
    }

// end function
}

?>
