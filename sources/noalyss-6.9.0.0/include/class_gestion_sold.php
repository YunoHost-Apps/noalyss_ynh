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
 * \brief definition of the class gestion_sold
 */

/*! \brief this object handles the table quant_sold
 *
 */

require_once  NOALYSS_INCLUDE.'/class_gestion_table.php';


class gestion_sold extends gestion_table
{
    var $qs_id; 					/*!< $qs_id primary key */
    var $qs_internal;				/*!< qs_internal */
    var $qs_fiche;				/*!< f_id code  */
    var $qs_quantite;				/*!< quantity of the card */
    var $qs_price;				/*!< price */
    var $qs_vat;					/*!< vat_amount */
    var $qs_vat_code;				/*!< vat_code */
    var $qs_client;				/*!< f_id of the customer */
    var $qs_valid;				/*!< will not be used */
    var $j_id;					/*!< jrnx.j_id */
    var $qs_vat_sided;
    /*!\brief return an array of gestion_table, the object are
     * retrieved thanks the qs_internal
     */
    function get_list()
    {
        if ($this->qs_internal=="")
            throw  new Exception(__FILE__.__LINE__." qs_internal est vide");
        $sql="select  qs_id,
             qs_internal,
             qs_fiche,
             qs_quantite,
             qs_price,
             qs_vat,
             tva_label,
             tva_rate,
             qs_vat_code,
             qs_client,
             j_id,
             qs_vat_sided
             from quant_sold left join tva_rate on (qs_vat_code=tva_id)
             where qs_internal='".$this->qs_internal."'";
        $ret=$this->db->exec_sql($sql);
        // $res contains all the line
        $res=Database::fetch_all($ret);

        if ( sizeof($res)==0) return null;
        $count=0;

        foreach ($res as $row)
        {
            $t_gestion_sold=new gestion_sold($this->db);
            foreach ($row as $idx=>$value)
            $t_gestion_sold->$idx=$value;
            $array[$count]=clone $t_gestion_sold;
            $count++;
        }
        return $array;
    }
     function search_by_jid($p_jid)
    {
        $res=$this->db->exec_sql("select qs_id from quant_sold where j_id=".$p_jid);

        if ( Database::num_row($res) == 1)
            $this->qs_id=Database::fetch_result($res,0,0);
        else
            $this->qs_id=0;
    }
    function load()
    {
       $sql=" select  qs_id,
             qs_internal,
             qs_fiche,
             qs_quantite,
             qs_price,
             qs_vat,
             qs_vat_code,
             qs_client,
             j_id,
             qs_vat_sided
             from quant_sold 
             where qs_id=$1";
       $ret=$this->db->exec_sql($sql,array($this->qs_id));
        // $res contains all the line
        $res=Database::fetch_all($ret);

        if ( empty($res) ) return null;
        foreach ($res[0] as $idx=>$value)
        $this->$idx=$value;
    }
}
