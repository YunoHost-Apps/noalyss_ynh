<?php

/*
 * Copyright (C) 2018 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


/***
 * @file 
 * @brief Display history of operation
 *
 */
require_once NOALYSS_INCLUDE."/class/acc_ledger_history_generic.class.php";
require_once NOALYSS_INCLUDE."/class/acc_ledger_history_sale.class.php";
require_once NOALYSS_INCLUDE."/class/acc_ledger_history_purchase.class.php";
require_once NOALYSS_INCLUDE."/class/acc_ledger_history_financial.class.php";
/**
 * @brief Display history of operation
 */
abstract class Acc_Ledger_History
{

    protected $m_from; //!< Starting Periode : periode.p_id
    protected $m_to;   //!< Ending Periode : periode.p_id
    protected $ma_ledger; //!< Array of ledger id : jrn_def.jrn_def_id
    protected $m_mode; //!< mode of export L : one line, E accounting writing , D : Detail
    public $db; //!< database connx

    function __construct(Database $cn, $pa_ledger, $p_from, $p_to, $p_mode)
    {
        if (is_array($pa_ledger) == FALSE) {
            throw new Exception (_('pa_ledger doit être un tableau'),EXC_PARAM_VALUE);
        }
        $this->db=$cn;
        $this->ma_ledger=$pa_ledger;
        $this->m_from=$p_from;
        $this->m_to=$p_to;
        $this->m_mode=$p_mode;
    }
    /**
     * setter / getter
     * @returns m_from (periode id)
     */
    public function get_from()
    {
        return $this->m_from;
    }
    /**
     * setter / getter
     * @returns m_to (periode id)
     */
    public function get_to()
    {
        return $this->m_to;
    }
    /**
     * setter / getter
     * @returns ma_ledger (array)
     */
    public function get_ledger()
    {
        return $this->ma_ledger;
    }
    /**
     * setter / getter
     * @returns m_mode (A,L,E,D)
     */
    public function get_mode()
    {
        return $this->m_mode;
    }
    /**
     * setter m_from (periode id)
     */
    public function set_from($m_from)
    {
        $this->m_from=$m_from;
        return $this;
    }
    /**
     * setter m_to (periode id)
     */
    public function set_to($m_to)
    {
        $this->m_to=$m_to;
        return $this;
    }
    /**
     * setter ma_ledger (array of jrn_def_id)
     */
    public function set_a_ledger($ma_ledger)
    {
        if (is_array($ma_ledger)==FALSE)
            throw new Exception(_("invalid parameter"), EXC_PARAM_VALUE);
        $this->ma_ledger=$ma_ledger;
        return $this;
    }
    /**
     * Setter
     * @param  $m_mode D,A,L,E
     * @return $this
     * @throws Exception
     */
    public function set_m_mode($m_mode)
    {
        if ($m_mode!='E'&&$m_mode!='D'&&$m_mode!='L'&&$m_mode!='A')
            throw new Exception(_("invalid parameter"), EXC_PARAM_VALUE);
        $this->m_mode=$m_mode;
        return $this;
    }

    /**
     * Build the right object 
     * @return \Acc_Ledger_History_Generic|\Acc_Ledger_History_Sale|\Acc_Ledger_History_Financial|\Acc_Ledger_History_Purchase
     */
    static function factory(Database $cn, $pa_ledger, $p_from, $p_to, $p_mode)
    {
        // For Accounting writing , we use Acc_Ledger_History
        if ($p_mode=="A")
        {
            $ret=new Acc_Ledger_History_Generic($cn, $pa_ledger, $p_from, $p_to,
                    $p_mode);
            return $ret;
        }
        $nb_ledger=count($pa_ledger);
        $ledger=new Acc_Ledger($cn, $pa_ledger[0]);
        $type=$ledger->get_type();

        // If first one is ODS so Acc_Ledger_History
        if ($type=="ODS")
        {
            $ret=new Acc_Ledger_History_Generic($cn, $pa_ledger, $p_from, $p_to,
                    $p_mode);
            return $ret;
        }
        // If all of the same type then use the corresponding class

        for ($i=0; $i<$nb_ledger; $i++)
        {
            $ledger=new Acc_Ledger($cn, $pa_ledger[$i]);
            $type_next=$ledger->get_type();

            // If type different then we go back to the generic
            if ($type_next!=$type)
            {
                $ret=new Acc_Ledger_History_Generic($cn, $pa_ledger, $p_from,
                        $p_to, $p_mode);
                return $ret;
            }
        }
        switch ($type)
        {
            case "ACH":
                $ret=new Acc_Ledger_History_Purchase($cn, $pa_ledger, $p_from,
                        $p_to, $p_mode);
                return $ret;
                break;
            case "FIN":
                $ret=new Acc_Ledger_History_Financial($cn, $pa_ledger, $p_from,
                        $p_to, $p_mode);
                return $ret;
                break;
            case "VEN":
                $ret=new Acc_Ledger_History_Sale($cn, $pa_ledger, $p_from,
                        $p_to, $p_mode);
                return $ret;
                break;

            default:
                break;
        }
    }

    /**
     * Retrieve the third : supplier for purchase, customer for sale, bank for fin,
     * @param $p_jrn_type type of the ledger FIN, VEN ACH or ODS
     * @param $jr_id jrn.jr_id
     * @todo duplicate function , also in Acc_Ledger::get_tiers, remove one
     */
    function get_tiers($p_jrn_type, $jr_id)
    {
        if ($p_jrn_type=='ODS')
            return ' ';
        $tiers=$this->get_tiers_id($p_jrn_type, $jr_id);
        if ($tiers==0)
            return "";

        $name=$this->db->get_value('select ad_value from fiche_detail where ad_id=1 and f_id=$1',
                array($tiers));
        $first_name=$this->db->get_value('select ad_value from fiche_detail where ad_id=32 and f_id=$1',
                array($tiers));
        return $name.' '.$first_name;
    }

    /**
     * @brief Return the f_id of the tiers , called by get_tiers
     * @param $p_jrn_type type of the ledger FIN, VEN ACH or ODS
     * @param $jr_id jrn.jr_id
    */
    function get_tiers_id($p_jrn_type, $jr_id)
    {
        $tiers=0;
        switch ($p_jrn_type)
        {
            case 'VEN':
                $tiers=$this->db->get_value('select max(qs_client) from quant_sold join jrnx using (j_id) join jrn on (jr_grpt_id=j_grpt) where jrn.jr_id=$1',
                        array($jr_id));
                break;
            case 'ACH':
                $tiers=$this->db->get_value('select max(qp_supplier) from quant_purchase join jrnx using (j_id) join jrn on (jr_grpt_id=j_grpt) where jrn.jr_id=$1',
                        array($jr_id));

                break;
            case 'FIN':
                $tiers=$this->db->get_value('select qf_other from quant_fin where jr_id=$1',
                        array($jr_id));
                break;
        }
        if ($this->db->count()==0)
            return 0;
        return $tiers;
    }

    /**
     * Prepare the query for fetching the linked operation
     * @staticvar int $prepare
     */
    protected function prepare_reconcile_date()
    {
        $prepare=$this->db->is_prepare("reconcile_date");
        if ($prepare==FALSE)
        {
            $this->db->prepare('reconcile_date',
                    'select  * 
                         from 
                           jrn 
                         where 
                           jr_id in 
                               (select 
                                   jra_concerned 
                                   from 
                                   jrn_rapt 
                                   where jr_id = $1 
                                union all 
                                select 
                                jr_id 
                                from jrn_rapt 
                                where jra_concerned=$1)');
        }
    }
    /**
     * display accounting of operations m_mode=A
     */
    abstract function export_accounting_html();
    /**
     * display detail of operations m_mode=D
     */
    abstract function export_detail_html();
    /**
     * display extended details of operation m_mode=E
     */
    abstract function export_extended_html();
    /**
     * display operation on one line m_mode=L
     */
    abstract function export_oneline_html();
    /**
     * call the right function , depending of m_mode
     */
    abstract function export_html();

    abstract function get_row($p_limit, $p_offset);
}
