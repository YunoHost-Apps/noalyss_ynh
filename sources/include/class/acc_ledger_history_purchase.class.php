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
 * @brief class Acc_Ledger_History_Purchase , list of operations
 */
require_once NOALYSS_INCLUDE."/class/acc_ledger_history.class.php";

/**
 * @brief Display the operations for Purchase
 */
class Acc_Ledger_History_Purchase extends Acc_Ledger_History
{

    private $data; //!< Contains rows from SQL

    public function __construct(\Database $cn, $pa_ledger, $p_from, $p_to,
            $p_mode)
    {
        parent::__construct($cn, $pa_ledger, $p_from, $p_to, $p_mode);
    }

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
        $own=new Noalyss_Parameter_Folder($this->db);

        $this->get_row();
        $this->add_vat_info();
        $this->prepare_reconcile_date();
        include NOALYSS_TEMPLATE."/acc_ledger_history_purchase_detail.php";
    }

    public function export_extended_html()
    {
        $this->get_row();
        $this->add_vat_info();
        $this->prepare_detail();
        $this->prepare_reconcile_date();
        include NOALYSS_TEMPLATE."/acc_ledger_history_purchase_extended.php";
    }

    /**
     * @brief display in HTML following the mode 
     */
    function export_html()
    {
        switch ($this->m_mode)
        {
            case "E":
                $this->export_extended_html();
                break;
            case "D":
                $this->export_detail_html();
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
        require_once NOALYSS_TEMPLATE.'/acc_ledger_history_purchase_oneline.php';
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
            with row_purchase as 
                (select qp_internal,
                    qp_supplier,sum(qp_price) as novat,
                    sum(qp_vat) as vat ,
                    sum(qp_vat_sided) as tva_sided ,
                    sum(qp_nd_amount) as noded_amount, 
                    sum(qp_nd_tva) as noded_vat,
                    sum(qp_dep_priv) as private_amount
                 from 
                    quant_purchase group by qp_supplier,qp_internal),
              supplier_detail as (
              select x.f_id as f_id,
                (select ad_value from fiche_detail where ad_id=1 and f_id=x.f_id) as name,
                (select ad_value from fiche_detail where ad_id=32 and f_id=x.f_id) as first_name,
                (select ad_value from fiche_detail where ad_id=23 and f_id=x.f_id) as qcode
              from 
              fiche as x)
            select   
                    name,
                    first_name,
                    qcode,
                    jr_id,
                    jr_pj_number,
                    jr_date,
                    jr_date_paid,
                    jr_internal,
                    qp_supplier,
                    jrn.jr_comment,
                    jr_pj_name,
                    vat,
                    tva_sided,
                    novat,
                    noded_amount,
                    noded_vat,
                    private_amount,
                    novat+vat-tva_sided as tvac,
                    n_text
            from
                jrn
                join row_purchase on (qp_internal=jr_internal)
                join supplier_detail on (qp_supplier=f_id)
                left join jrn_note using (jr_id)
            where
                jr_def_id in ({$ledger_list})
                and {$periode}
                {$cond_limite}";
        $this->data=$this->db->get_array($sql);
    }

    /**
     * @brief preprare the query for fetching the detailed VAT of an operation
     */
    private function add_vat_info()
    {
        $prepare=$this->db->is_prepare("vat_infop");
        if ($prepare==FALSE)
        {
            $this->db->prepare("vat_infop",
                    "
                select 
                    sum(qp_vat) vat_amount , 
                    qp_vat_code 
                from 
                    quant_purchase
                where 
                    qp_internal = $1 
                group by qp_vat_code,qp_internal order by qp_vat_code");
        }

        $nb_row=count($this->data);
        for ($i=0; $i<$nb_row; $i++)
        {
            $ret=$this->db->execute("vat_infop",
                    array($this->data[$i]["jr_internal"]));
            $array=Database::fetch_all($ret);
            $this->data[$i]["detail_vat"]=$array;
        }
    }

    /**
     * Prepare the query for fetching detail of an operation
     */
    private function prepare_detail()
    {

        if ($this->db->is_prepare("detail_purchase")==FALSE)
        {
            $this->db->prepare("detail_purchase",
                    "
                with card_name as 
                (select f_id,ad_value as name 
                    from fiche_detail where ad_id=1),
                card_qcode as  
                (select f_id,ad_value as qcode 
                from fiche_detail where ad_id=23)
                select 	qp_price,qp_quantite,qp_vat,qp_vat_code,qp_unit,qp_vat_sided,name,qcode,tva_label,
                qp_price+qp_vat-qp_vat_sided as tvac
                from 
                    quant_purchase
                    join jrnx using (j_id)              
                    join card_name on (card_name.f_id=qp_fiche)
                    join card_qcode on (card_qcode.f_id=qp_fiche)
                    join tva_rate on ( qp_vat_code=tva_id)
                where
                    qp_internal=$1
                
            ");
        }
    }
    /**
     * To get data
     * @return array of rows
     */
    function get_data()
    {
        return $this->data;
    }
    function export_csv()
    {
        $export=new Noalyss_Csv(_('journal'));
        $export->send_header();
        
        $this->get_row();
        $this->prepare_reconcile_date();
        $this->add_vat_info();
                
        $own=new Noalyss_Parameter_Folder($this->db);
        $title=array();
        $title[]=_('Date');
        $title[]=_("Paiement");
        $title[]=_("operation");
        $title[]=_("Pièce");
        $title[]=_("Fournisseur");
        $title[]=_("Note");
        $title[]=_("interne");
        $title[]=_("HTVA");
        $title[]=_("privé");
        $title[]=_("DNA");
        $title[]=_("tva non ded.");
        $title[]=_("TVA NP");
       

        if ( $own->MY_TVA_USE=='Y')
        {
            $a_Tva=$this->db->get_array("select tva_id,tva_label from tva_rate order by tva_rate,tva_label,tva_id");
            foreach($a_Tva as $line_tva)
            {
                $title[]="Tva ".$line_tva['tva_label'];
            }
        }
        $title[]=_("TVAC/TTC");
        $title[]=_("opérations liées");
        $export->write_header($title);
        
        foreach ($this->data as $line)
        {
            $export->add($line['jr_date']);
            $export->add($line['jr_date_paid']);
            $export->add($line['jr_id']);
            $export->add($line['jr_pj_number']);
            $export->add($line['name']." ".
                         $line["first_name"]." ".
                         $line["qcode"]); // qp_supplier
            $export->add($line['jr_comment']);
            $export->add($line['jr_internal']);
            $export->add($line['novat'],"number");
            $export->add($line['private_amount'],"number");
            $export->add($line['noded_amount'],"number");
            $export->add($line['noded_vat'],"number");
            $export->add($line['tva_sided'],"number");
            
            $a_tva_amount=array();
            //- set all TVA to 0
            foreach ($a_Tva as $l) {
                $t_id=$l["tva_id"];
                $a_tva_amount[$t_id]=0;
            }
            foreach ($line['detail_vat'] as $lineTVA)
            {
                $idx_tva=$lineTVA['qp_vat_code'];
                $a_tva_amount[$idx_tva]=$lineTVA['vat_amount'];
             }
            if ($own->MY_TVA_USE == 'Y' )
            {
                foreach ($a_Tva as $line_tva)
                {
                    $a=$line_tva['tva_id'];
                    $export->add($a_tva_amount[$a],"number");
                }
            }
            $export->add($line['tvac'],"number");
            /**
             * Retrieve payment if any
             */
             $ret_reconcile=$this->db->execute('reconcile_date',array($line['jr_id']));
             $max=Database::num_row($ret_reconcile);
            if ($max > 0) {
                for ($e=0;$e<$max;$e++) {
                    $row=Database::fetch_array($ret_reconcile, $e);
                    $export->add($row['jr_date']);
                    $export->add($row['jr_internal']);
                }
            }
	    $export->write();

        }

    }
}
