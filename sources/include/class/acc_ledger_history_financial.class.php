<?php

/*
 *   This file is part of NOALYSS.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2018) Author Dany De Bontridder <dany@alchimerys.be>


/**
 * @file
 * @brief displya financial operations
 * 
 */
require_once NOALYSS_INCLUDE."/class/acc_ledger_history.class.php";

class Acc_Ledger_History_Financial extends Acc_Ledger_History
{

    /**
     * @brief display the accounting 
     */
    public function export_accounting_html()
    {
        $ledger_history=new Acc_Ledger_History_Generic($this->db,
                $this->ma_ledger, $this->m_from, $this->m_to, $this->m_mode);
        $ledger_history->export_accounting_html();
    }

    public function export_detail_html()
    {
        $this->export_oneline_html();
    }

    public function export_extended_html()
    {
        $ledger_history=new Acc_Ledger_History_Generic($this->db,
                $this->ma_ledger, $this->m_from, $this->m_to, $this->m_mode);
        $ledger_history->export_accounting_html();
    }

    /**
     * @brief display in HTML following the mode 
     */
    function export_html()
    {
        switch ($this->m_mode)
        {
            case "E":
                $this->export_accounting_html();
                break;
            case "D":
                $this->export_oneline_html();
                break;
            case "L":
                $this->export_oneline_html();
                break;
            case "A":
                $this->export_accounting_html();
                break;
            default:
                break;
        }
    }

    /**
     * display in HTML one operation by line
     */
    public function export_oneline_html()
    {
        $this->get_row();
        $this->prepare_reconcile_date();
        require_once NOALYSS_TEMPLATE.'/acc_ledger_history_financial_oneline.php';
        
    }

    /**
     * Get the rows from jrnx and quant* tables
     * @param int $p_limit max of rows to returns
     * @param int $p_offset the number of rows to skip
     */
    public function get_row($p_limit=-1, $p_offset="")
    {
        $periode=sql_filter_per($this->db, $this->m_from, $this->m_to, 'p_id',
                'jr_tech_per');

        $cond_limite=($p_limit!=-1)?" limit ".$p_limit." offset ".$p_offset:"";

        $ledger_list=join(",", $this->ma_ledger);
        $sql="
            with detail as (
              select x.f_id as f_id,
                (select ad_value from fiche_detail where ad_id=1 and f_id=x.f_id) as name,
                (select ad_value from fiche_detail where ad_id=32 and f_id=x.f_id) as first_name,
                (select ad_value from fiche_detail where ad_id=23 and f_id=x.f_id) as qcode
              from 
              fiche as x)
            select   
                    bk.f_id as bk_f_id,
                    bk.name as bk_name,
                    bk.first_name as bk_first_name,
                    bk.qcode as bk_qcode,
                    tiers.f_id as tiers_f_id,
                    tiers.name as tiers_name,
                    tiers.first_name as tiers_first_name,
                    tiers.qcode as tiers_qcode,
                    jr_id,
                    jr_pj_number,
                    jr_date,
                    jr_date_paid,
                    jr_internal,
                    jrn.jr_comment,
                    jr_pj_name,
                    qf_amount
            from
                jrn
                join quant_fin using (jr_id)
                join detail as tiers on (tiers.f_id=qf_other) 
                join detail as bk on (bk.f_id=qf_bank) 
            where
                jr_def_id in ({$ledger_list})
                and {$periode}
                {$cond_limite}";
        $this->data=$this->db->get_array($sql);
    }
    /**
     * To get data
     * @return array of rows
     */
    function get_data()
    {
        return $this->data;
    }

}
