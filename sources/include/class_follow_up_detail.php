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
require_once  NOALYSS_INCLUDE.'/class_fiche.php';
require_once  NOALYSS_INCLUDE.'/class_database.php';
// Copyright Author Dany De Bontridder danydb@aevalys.eu

/*!\file
 * \brief Follow_Up details are the details for a actions
 */

/*!\brief Follow_Up Details are the details for an actions, it means
 * the details of an order, delivery order, submit a quote...
 * this class is linked to the table action_detail
 * - "id"=>"ad_id", primary key
 * - "qcode"=>"f_id", quick_code
 * - "text"=>"ad_text", description lines
 * - "price_unit"=>"ad_pu", price by unit
 * - "quantity"=>"ad_quant", quantity
 * - "tva_id"=>"ad_tva_id", tva_od
 * - "tva_amount"=>"ad_tva_amount", vat amount
 * - "total"=>"ad_total_amount", total amount including vat
 * - "ag_id"=>"ag_id" => foreign key to action_gestion
 * -  db is the database connection
 */
class Follow_Up_Detail
{
    private static $variable=array(
                                 "id"=>"ad_id",
                                 "qcode"=>"f_id",
                                 "text"=>"ad_text",
                                 "price_unit"=>"ad_pu",
                                 "quantity"=>"ad_quant",
                                 "tva_id"=>"ad_tva_id",
                                 "tva_amount"=>"ad_tva_amount",
                                 "total"=>"ad_total_amount",
                                 "ag_id"=>"ag_id"
                             );
    function __construct ($p_cn,$p_id=0)
    {
        $this->db=$p_cn;
        $this->ad_id=$p_id;
    }
    public function get_parameter($p_string)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            return $this->$idx;
        }
        else
           throw new Exception("Attribut inexistant $p_string");
    }
    public function set_parameter($p_string,$p_value)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            $this->$idx=$p_value;
        }
        else
            throw new Exception("Attribut inexistant $p_string");


    }
    public function get_info()
    {
        return var_export(self::$variable,true);
    }
    public function verify()
    {
        // Verify that the elt we want to add is correct
        return 0;
    }
    public function save()
    {
        if (  $this->ad_id == 0 )
            $this->insert();
        else
            $this->update();
    }

    public function insert()
    {
        if ( $this->verify() != 0 ) return;
        $sql='INSERT INTO action_detail('.
             ' f_id, ad_text, ad_pu, ad_quant, ad_tva_id, ad_tva_amount,'.
             '   ad_total_amount, ag_id)'.
             ' VALUES ($1, $2, $3, $4,$5,$6,$7,$8) returning ad_id';
        $this->ad_id=$this->db->get_value($sql,array(
                                              $this->f_id,
                                              $this->ad_text,
                                              $this->ad_pu,
                                              $this->ad_quant,
                                              $this->ad_tva_id,
                                              $this->ad_tva_amount,
                                              $this->ad_total_amount,
                                              $this->ag_id
                                          )
                                         );

    }

    public function update()
    {
        if ( $this->verify() != 0 ) return;

        $sql='UPDATE action_detail '.
             ' SET f_id=$1, ad_text=$2, ad_pu=$3, ad_quant=$4, ad_tva_id=$5,'.
             '     ad_tva_amount=$6, ad_total_amount=$7, ag_id=$8'.
             ' WHERE ad_id=$9';
        $this->id=$this->db->exec_sql($sql,array(
                                          $this->f_id,
                                          $this->ad_text,
                                          $this->ad_pu,
                                          $this->ad_quant,
                                          $this->ad_tva_id,
                                          $this->ad_tva_amount,
                                          $this->ad_total_amount,
                                          $this->ag_id,
                                          $this->ad_id
                                      )
                                     );


    }
    /*!\brief retrieve all the details of an Follow_Up
     *\return array of Action_Detail
     *\see Follow_Up::get
     */
    public function load_all()
    {
        $sql="SELECT ad_id, f_id, ad_text, ad_pu, ad_quant, ad_tva_id, ad_tva_amount,
             ad_total_amount, ag_id   FROM action_detail ".
             " where ag_id=$1 order by ad_id";
        $res=$this->db->get_array(
                 $sql,
                 array($this->ag_id)
             );
        if ( $this->db->count() == 0 ) return;
        $aRet=array();
        for($i=0;$i<count($res);$i++)
        {
            $a=new Follow_Up_Detail($this->db);
            $row=$res[$i];
            foreach ($row as $idx=>$value)
            {
                $a->$idx=$value;
            }
            $aRet[$i]=clone $a;
        }
        return $aRet;
    }

    public function load()
    {
        $sql="SELECT ad_id, f_id, ad_text, ad_pu, ad_quant, ad_tva_id, ad_tva_amount,
             ad_total_amount, ag_id   FROM action_detail".
             " where ad_id=$1";

        $res=$this->db->get_array($this->db,
                                  $sql,
                                  array($this->ad_id)
                                 );
        if ( $this->db->count() == 0 ) return;
        $row=$res[0];
        foreach ($row as $idx=>$value)
        {
            $this->$idx=$value;
        }

    }
    public function delete()
    {
		$sql="delete from action_detail where ad_id=$1";
		$this->db->exec_sql($sql,array($this->ad_id));
	}
    /*!\brief Fill an Action_Detail Object with the data contained in an array
    *\param $array
     - [ad_id7] => ad_id
     - [e_march7] =>  f_id
     - [e_march7_label] => ad_text
     - [e_march7_price] => ad_pu
     - [e_quant7] => ad_quant
     - [e_march7_tva_id] => ad_tva_id
     - [e_march7_tva_amount] => ad_tva_amount
     - [tvac_march7] => ad_total_amount
     - [ag_id] => ag_id
     *\param $idx is the idx (example 7)
     *\note    */
    public function from_array($array,$idx)
    {
        $row=$array;
        $this->ad_id=(isset($row['ad_id'.$idx]))?$row['ad_id'.$idx]:0;

        $qcode=(isset($row['e_march'.$idx]))?$row['e_march'.$idx]:"";
        if (trim($qcode)=='')
        {
            $this->f_id=0;
        }
        else
        {
            $tmp=new Fiche($this->db);
            $tmp->get_by_qcode($qcode,false);
            $this->f_id=$tmp->id;
        }
        $this->ad_text=(isset($row['e_march'.$idx.'_label']))?$row['e_march'.$idx.'_label']:"";
        $this->ad_pu=(isset($row['e_march'.$idx.'_price']))?$row['e_march'.$idx.'_price']:0;
        $this->ad_quant=(isset($row['e_quant'.$idx]))?$row['e_quant'.$idx]:0;
        $this->ad_tva_id=(isset($row['e_march'.$idx.'_tva_id']))?$row['e_march'.$idx.'_tva_id']:0;
        $this->ad_tva_amount=(isset($row['e_march'.$idx.'_tva_amount']))?$row['e_march'.$idx.'_tva_amount']:0;
        $this->ad_total_amount=(isset($row['tvac_march'.$idx]))?$row['tvac_march'.$idx]:0;
        $this->ag_id=(isset($array['ag_id']))?$array['ag_id']:0;
        /* protect numeric */
        if (trim($this->ad_pu)=="" || isNumber($this->ad_pu)==0) $this->ad_pu=0;
        if (trim($this->ad_quant)=="" || isNumber($this->ad_quant)==0) $this->ad_quant=0;
        if (trim($this->ad_tva_amount)==""||isNumber($this->ad_tva_amount)==0) $this->ad_tva_amount=0;
        if (trim($this->ad_total_amount)==""||isNumber($this->ad_total_amount)==0) $this->ad_total_amount=0;
        if (trim($this->ad_tva_id)=="" || isNumber($this->ad_tva_id)==0) $this->ad_tva_id=0;
    }
    /*!\brief
     *\param
     *\return
     *\note
     *\see
     */
    static function test_me()
{}

}

/* test::test_me(); */

