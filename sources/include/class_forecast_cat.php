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
 * @brief  manage the table forecast_cat, this table contains the categories of forecast
 *  as expense, asset, sales...
 */
/**
 *@brief this class is called normally from forecast, a forecast contains category
 * like sales, expenses, each category contains items
 *@see Forecast
 *
 */
class Forecast_Cat
{
    /* example private $variable=array("val1"=>1,"val2"=>"Seconde valeur","val3"=>0); */
    private static $variable=array ("id"=>"fc_id","order"=>"fc_order","desc"=>"fc_desc","forecast"=>"f_id");
    private $cn;
    /**
     * @brief constructor
     * @param $p_init Database object
     */
    function __construct ($p_init,$p_id=0)
    {
        $this->cn=$p_init;
        $this->fc_id=$p_id;
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
        if (strlen(trim($this->fc_order))==0)
        {
            $this->fc_order="1";
        }
        // Verify that the elt we want to add is correct
        // the f_name must be unique (case insensitive)
        return 0;
    }
    public function save()
    {
        if (  $this->get_parameter("id") == 0 )
            $this->insert();
        else
            $this->update();
    }

    public function insert()
    {
        if ( $this->verify() != 0 ) return;

        $sql="insert into forecast_cat (fc_desc,fc_order,f_id) ".
             " values ($1,$2,$3)  returning fc_id";
        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->fc_desc,$this->fc_order,$this->f_id)
             );
        $this->fc_id=Database::fetch_result($res,0,0);
    }

    public function update()
    {
        if ( $this->verify() != 0 ) return;

        $sql="update forecast_cat set fc_desc=$1,f_id=$2,fc_order=$3 ".
             " where fc_id = $4";
        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->fc_desc,$this->f_id, $this->fc_order,$this->fc_id)
             );
    }
    /**
     *@brief Load all the cat. for a given forecast and return them into a array
     *@param $p_cn database connx
     *@param $p_id is the forecast id (f_id)
     *@return an array with all the data
     */
    public static function load_all($p_cn,$p_id)
    {
        $sql="select fc_id,fc_desc,fc_order from forecast_cat where f_id=$1";

        $res=$p_cn->get_array($sql,array($p_id));

        return $res;
    }
    public function load()
    {

        $sql="select fc_desc, f_id,fc_order from forecast_cat where fc_id=$1";

        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->fc_id)
             );

        if ( Database::num_row($res) == 0 ) return;
        $row=Database::fetch_array($res,0);
        foreach ($row as $idx=>$value)
        {
            $this->$idx=$value;
        }

    }
    /**
     *@brief Make a array for a ISelect of the available cat
     *@param $id is forecast::f_id
     *@return array for ISelect
     *@see ISelect
     */
    public function make_array($id)
    {
        $sql="select fc_id,fc_desc from forecast_cat where f_id=$id";
        $ret=$this->cn->make_array($sql);
        return $ret;
    }
    public function delete()
    {
        $sql="delete from forecast_cat where fc_id=$1";
        $res=$this->cn->exec_sql($sql,array($this->fc_id));
    }
    /**
     * @brief unit test
     */
    static function test_me()
    {}

}

?>