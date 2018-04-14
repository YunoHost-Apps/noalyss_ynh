<?php

/*
 * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
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
require_once NOALYSS_INCLUDE.'/class/lettering.class.php';

/* * *
 * @file 
 * @brief compute the ageing balance, currently this code is not used
 *
 */

class Balance_Age
{

    private $cn;

    function __construct($p_cn)
    {
        $this->cn=$p_cn;
        $this->afiche=null;
    }

    function get_array_card($p_type, $p_extra="")
    {
        switch ($p_type)
        {
            case 'X':
                $this->afiche=$this->cn->get_array("
            with m as (select distinct qp_supplier as f_id from quant_purchase union select qs_client from quant_sold)
            select distinct fiche.f_id as f_id ,f1.ad_value as name, f3.ad_value as first_name,f2.ad_value  as quick_code  
                from fiche 
                    join m on (fiche.f_id=m.f_id)
                    join fiche_detail as f1 on (fiche.f_id=f1.f_id and f1.ad_id=1) 
                    join fiche_detail as f2 on (fiche.f_id=f2.f_id and f2.ad_id=23) 
                    left join fiche_detail as f3 on (fiche.f_id=f3.f_id and f3.ad_id=32)
                 where 
                 fiche.fd_id=$1
                 order by f1.ad_value
                 ", array($p_extra));
                break;
            case 'U':
                $fiche=new Fiche($this->cn, $p_extra);
                $this->afiche[0]['f_id']=$fiche->id;
                $this->afiche[0]['quick_code']=$fiche->get_quick_code();
                $this->afiche[0]['name']=$fiche->strAttribut(ATTR_DEF_NAME, 0);
                $this->afiche[0]['first_name']=$fiche->strAttribut(ATTR_DEF_FIRST_NAME, 0);
                break;
            case 'F':
                $this->afiche=$this->cn->get_array("
            select distinct qp_supplier as f_id ,f1.ad_value as name, f3.ad_value as first_name,f2.ad_value  as quick_code  
                from quant_purchase join 
                    fiche_detail as f1 on (qp_supplier=f1.f_id and f1.ad_id=1) 
                    join fiche_detail as f2 on (qp_supplier=f2.f_id and f2.ad_id=23) 
                    left join fiche_detail as f3 on (qp_supplier=f3.f_id and f3.ad_id=32)
                 order by f1.ad_value
                 ");
                break;
            case 'C':
                $this->afiche=$this->cn->get_array("
             select distinct qs_client as f_id ,f1.ad_value as name, f3.ad_value as first_name,f2.ad_value  as quick_code  
                from quant_sold join 
                    fiche_detail as f1 on (qs_client=f1.f_id and f1.ad_id=1) 
                    join fiche_detail as f2 on (qs_client=f2.f_id and f2.ad_id=23) 
                    left join fiche_detail as f3 on (qs_client=f3.f_id and f3.ad_id=32)
                 order by f1.ad_value
                 ");
                break;
            default:
                throw new Exception('Type invalide');
        }
    }

    function display_card($p_date_start, $p_fiche, $p_let)
    {
        $this->get_array_card('U', $p_fiche);
        $a_fiche=$this->afiche;
        $nb_fiche=count($a_fiche);
        require NOALYSS_TEMPLATE.'/balance_aged_result.php';
    }

    function display_category($p_date_start, $p_cat, $p_let)
    {
        // Get all fiche from Purchase

        $this->get_array_card('X', $p_cat);
        $a_fiche=$this->afiche;
        $nb_fiche=count($a_fiche);
        require NOALYSS_TEMPLATE.'/balance_aged_result.php';
    }

    /**
     * Display all the operation for the customer
     * @param type $p_date_start min date  of the operatin
     * @param type $p_let 'unlet' only unlettered or 'let' for all
     */
    function display_purchase($p_date_start, $p_let)
    {
        // Get all fiche from Purchase
        $this->get_array_card('F');
        $a_fiche=$this->afiche;
        $nb_fiche=count($a_fiche);
        require NOALYSS_TEMPLATE.'/balance_aged_result.php';
    }

    /**
     * Display all the operation for the supplier
     * @param type $p_date_start min date  of the operatin
     * @param type $p_let 'unlet' only unlettered or 'let' for all
     */
    function display_sale($p_date_start, $p_let)
    {
        // Get all fiche from Purchase
        $this->get_array_card('C');
        $a_fiche=$this->afiche;
        $nb_fiche=count($a_fiche);
        require NOALYSS_TEMPLATE.'/balance_aged_result.php';
    }
    /**
     * Export Aged Balance to CSV
     * @param $p_date_start date DD.MM.YYYY 
     * @param type $p_let unlet for unlettered operations
     */
    function export_csv($p_date_start, $p_let)
    {
        require_once NOALYSS_INCLUDE.'/lib/noalyss_csv.class.php';
        bcscale(2);
        $export=new Noalyss_Csv('aged_balance');
        $header = array(_('QuickCode') ,
                        _('Nom'),
                        _('Prénom'),
                        _('Date'),
                        _('N° pièce'),
                        _('Interne'),
                        _('Fin'),
                        _('<30 jours'),
                        _('entre 30 et 60 jours'),
                        _('entre 60 et 90 jours'),
                        _('> 90 jours') );
        $export->send_header();
        $export->write_header($header);
        
        
        $nb_fiche=count($this->afiche);
       
        for ($i=0; $i<$nb_fiche; $i++)
        {
            $card=new Lettering_Card($this->cn, $this->afiche[$i]['quick_code']);
            $card->set_parameter('start', $p_date_start);
            $card->get_balance_ageing($p_let);
            if (empty($card->content))
                continue;
            $nb_row=count($card->content);
            $sum_lt_30=0;
            $sum_gt_30_lt_60=0;
            $sum_gt_60_lt_90=0;
            $sum_gt_90=0;
            $sum_fin=0;
            for ($j=0; $j<$nb_row; $j++)
            {
                $show=true;
                $export->add($this->afiche[$i]['quick_code']);
                $export->add($this->afiche[$i]['name']);
                $export->add($this->afiche[$i]['first_name']);
                $export->add($card->content[$j]['j_date_fmt']);
                $export->add($card->content[$j]['jr_pj_number']);
                $export->add($card->content[$j]['jr_internal']);
                if ($card->content[$j]['jrn_def_type']=='FIN'||$card->content[$j]['jrn_def_type']=='ODS')
                {
                    $export->add($card->content[$j]['j_montant'],"number");
                    $sum_fin=bcadd($sum_fin, $card->content[$j]['j_montant']);
                    $show=false;
                }
                else
                {
                    $export->add(0,'number');
                }
                if ($show&&$card->content[$j]['day_paid']<=30)
                {
                    $export->add($card->content[$j]['j_montant'],"number");
                    $sum_lt_30=bcadd($sum_lt_30, $card->content[$j]['j_montant']);
                    $show=false;
                }
                else
                {
                    $export->add(0,'number');
                }

                if ($show&&$card->content[$j]['day_paid']>30&&$card->content[$j]['day_paid']<=60)
                {
                    $export->add($card->content[$j]['j_montant'],"number");
                    $sum_gt_30_lt_60=bcadd($sum_gt_30_lt_60, $card->content[$j]['j_montant']);
                }
                else
                {
                    $export->add(0,'number');
                }

                if ($show&&$card->content[$j]['day_paid']>60&&$card->content[$j]['day_paid']<=90)
                {
                    $export->add($card->content[$j]['j_montant'],"number");
                    $sum_gt_60_lt_90=bcadd($sum_gt_60_lt_90, $card->content[$j]['j_montant']);
                }
                else
                {
                    $export->add(0,'number');
                }
                if ($show&&$card->content[$j]['day_paid']>90)
                {
                   $export->add($card->content[$j]['j_montant'],"number");
                    $sum_gt_90=bcadd($sum_gt_90, $card->content[$j]['j_montant']);
                }
                else
                {
                    $export->add(0,'number');
                }
                $export->write();
            }
            $export->add(_('Totaux'));
            $export->add("");
            $export->add("");
            $export->add("");
            $export->add("");
            $export->add("");
            $export->add($sum_fin,"number");
            $export->add($sum_lt_30,"number");
            $export->add($sum_gt_30_lt_60,"number");
            $export->add($sum_gt_60_lt_90,"number");
            $export->add($sum_gt_90,"number");
            $export->write();
        
        
        }
    }

}
