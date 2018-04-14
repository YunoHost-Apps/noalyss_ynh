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
 * \brief this class  maps the table form, which is a child table for formdef
 */

/*!\brief  manipulate the form_def's child table (form) */
class  Acc_Report_Row
{
    private static $variable=array(
                                 "name"=>"fo_label",
                                 "formula"=>"fo_formula",
                                 "id"=>"fo_id",
                                 "position"=>"fo_pos",
                                 "form_id"=>"fo_fr_id",
                                 "database"=>"db"
                             );
    function __construct ($p_name=null,$p_formula=null)
    {
        $this->set_parameter("id",0);

        $this->set_parameter("name",$p_name);
        $this->set_parameter("formula",$p_formula);
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
    /*!\brief Convert an array into an array of row_rapport object
     *        the fo_id is 0, position = 0, the fo_frd_id (form_id) is
     *  the one of the current object, the db is also the current one
     *\param $p_array contains the value
     *\return an array of Acc_Report_Row object
     */
    public function from_array($p_array)
    {
        extract ($p_array, EXTR_SKIP);
        $ret=array();
        $ix=0;
        $found=0;
        foreach ( $p_array as $r)
        {

            if ( isset(${'form'.$ix}) && isset ( ${'text'.$ix} ))
            {
                $obj=new Acc_Report_Row( ${'text'.$ix},${'form'.$ix});

                if ( isset(${'pos'.$ix}) &&  isNumber(${'pos'.$ix})==1 )
                    $obj->set_parameter("position",${'pos'.$ix});
                else
                {
                    $obj->set_parameter("position",$found);
                    $found++;
                }

                $obj->fo_id=0;
                $obj->fo_fr_id=$this->fo_fr_id;
                $obj->db=$this->db;

                $ret[]=clone $obj;
            }
            $ix++;

        }
        return $ret;
    }
    static function test_me()
    {
        $cn=Dossier::connect();
        $a=new Acc_Report_Row();
        $array=array("text0"=>"test1",
                     "form0"=>"7%",
                     "text1"=>"test2",
                     "form1"=>"6%"
                    );
        $a->db=$cn;
        $b=$a->from_array($array);
        print_r($b);
        echo $a->get_info();
    }
}
