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

/**
 * @file
 * @brief export in CSV the summary of stock in list
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class/stock.class.php';
global $cn;
// var_dump($_GET);
$stock=new Stock($cn);
$tmp_id = $stock->build_tmp_table($_GET);

require_once NOALYSS_INCLUDE.'/lib/noalyss_csv.class.php';
$export=new Noalyss_Csv(_('résumé-stock'));
$export->send_header();
$export->write_header(array(_("Depot"),_("Adresse"),_("Ville"),_("Pays"),_("Code Stock"),_("Fiches"),_("IN"),_("OUT"),_("Delta")));

$a_repo=$cn->get_array("select distinct t.r_id,r_name,r_adress,r_city,r_country from stock_repository as s join tmp_stockgood_detail as t
	on (s.r_id=t.r_id)
	where
	s_id=$1
	order by 2
	",array($tmp_id));
for ($r=0;$r<count($a_repo);$r++) {

    $a_stock=$cn->get_array(
            "
            select coalesce(sum(s_qin),0) as qin,coalesce(sum(s_qout),0) as qout,sg_code
                    from tmp_stockgood_detail  where r_id=$1 and s_id=$2
                    group by sg_code
                    order by sg_code

            ",array($a_repo[$r]['r_id'],$tmp_id));
    for ($s=0;$s<count($a_stock);$s++){

            $a_card=$cn->get_array(
                            "
                                    select f_id,vw_name,quick_code
                                    from vw_fiche_attr
                                    where
                             f_id in (
                                    select distinct f_id from fiche_detail
                                            where
                                            ad_id=19 and
                                            ad_value=$1)
                                    order by vw_name,quick_code
                            ",array($a_stock[$s]['sg_code']));

        $export->add($a_repo[$r]['r_name']);
        $export->add($a_repo[$r]['r_adress']);
        $export->add($a_repo[$r]['r_city']);
        $export->add($a_repo[$r]['r_country']);
        $export->add($a_stock[$s]['sg_code']);
        for ( $c=0;$c<count($a_card);$c++) {
            $a=sprintf('[%s] %s',$a_card[$c]['quick_code'], $a_card[$c]['vw_name']);
        }
        if ( count($a_card)== 0 ) $a= ' Erreur Code non utilisé';
        $export->add($a);
        $export->add($a_stock[$s]['qin'],"number");
        $export->add($a_stock[$s]['qout'],"number");
        $export->add(bcsub($a_stock[$s]['qin'],$a_stock[$s]['qout']),"number");
        $export->write();
    }
 }

