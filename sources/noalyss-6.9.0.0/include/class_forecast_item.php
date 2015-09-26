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
 * @brief manage the table forecast_item  contains the items, the item are part of category of forecast_cat, which are
 *  part of Forecast
 */
/**
 *@brief manage the table forecast_item  contains the items, the item are part of category of forecast_cat, which are
 *  part of Forecast
 * @see Forecast Forecast_Cat
 *
 *
 */
class Forecast_Item
{
    /* example private $variable=array("val1"=>1,"val2"=>"Seconde valeur","val3"=>0); */
    private static $variable=array ("id"=>"fi_id","text"=>"fi_text","account"=>"fi_account",
                                    "card"=>"fi_card","order"=>"fi_order","cat_id"=>"fc_id","amount"=>"fi_amount","debit"=>"fi_debit","periode"=>"fi_pid");
    private $cn;
    /**
     * @brief constructor
     * @param $p_init Database object
     */
    function __construct ($p_init,$p_id=0)
    {
        $this->cn=$p_init;
        $this->fi_id=$p_id;
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
		$this->fi_account=  str_replace(" ", "", $this->fi_account);
        // Verify that the elt we want to add is correct
        // the f_name must be unique (case insensitive)
        return 0;
    }
    public function save()
    {
        /* please adapt */
        if (  $this->get_parameter("id") == 0 )
            $this->insert();
        else
            $this->update();
    }

    public function insert()
    {
        if ( $this->verify() != 0 ) return;

        $sql="INSERT INTO forecast_item(
             fi_text, fi_account, fi_card, fi_order, fc_id, fi_amount,
             fi_debit,fi_pid)
             VALUES ($1, $2, $3, $4, $5, $6, $7,$8) returning fi_id;";
        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->fi_text,$this->fi_account,$this->fi_card,$this->fi_order,$this->fc_id,$this->fi_amount,$this->fi_debit,$this->fi_pid)
             );
        $this->fi_id=Database::fetch_result($res,0,0);
    }

    public function update()
    {
        if ( $this->verify() != 0 ) return;

        $sql="UPDATE forecast_item
             SET  fi_text=$1, fi_account=$2, fi_card=$3, fi_order=$4, fc_id=$5,
             fi_amount=$6, fi_debit=$7,fi_pid=$8
             WHERE fi_id=$9;";
        $res=$this->cn->exec_sql($sql,
                                 array($this->fi_text,
                                       $this->fi_account,
                                       $this->fi_card,
                                       $this->fi_order,
                                       $this->fc_id,
                                       $this->fi_amount,
                                       $this->fi_debit,
                                       $this->fi_pid,
                                       $this->fi_id)
                                );

    }

    public function load()
    {

        $sql="SELECT fi_id, fi_text, fi_account, fi_card, fi_order, fc_id, fi_amount,
             fi_debit,fi_pid
             FROM forecast_item where fi_id=$1";

        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->fi_id)
             );

        if ( Database::num_row($res) == 0 ) return;
        $row=Database::fetch_array($res,0);
        foreach ($row as $idx=>$value)
        {
            $this->$idx=$value;
        }

    }




    public function delete()
    {
        $sql="delete from forecast_item where fi_id=$1";
        $res=$this->cn->exec_sql($sql,array($this->fi_id));
    }
    /**
     * @brief unit test
     */
    static function test_me()
    {}

}

?>