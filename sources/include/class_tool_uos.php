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
/**
 * @file
 * @brief Objec to check a double insert into the database, this duplicate occurs after
 * a refresh of the web page
 */
// Copyright Author Dany De Bontridder danydb@aevalys.eu

require_once NOALYSS_INCLUDE.'/class_database.php';
define ('CODE_EXCP_DUPLICATE',901);
/**
 * @brief Objec to check a double insert into the database, this duplicate occurs after
 * a refresh of the web page
 * in
 */

class Tool_Uos
{
    /**
     * Constructor $p_name will be set to $this->name, it is also the name
     * of the tag hidden in a form
     * @global $cn Db connxion
     * @param $p_name
     */
    function __construct($p_name)
    {
        $this->name=$p_name;
    }
    /**
     * @brief return a string with a tag hidden and a uniq value
     * @param $hHidden is the name of the tag hidden
     * @return string : tag hidden
     */
    function hidden()
    {
		global $cn;
        $this->id=$cn->get_next_seq('uos_pk_seq');
        return HtmlInput::hidden($this->name,$this->id);
    }
    /**
     * @brief Try to insert into the table tool_uos
     * @global $cn Database connx
     * @throws Exception if the value $p_id is not unique
     */
    function save($p_array=null)
    {
        global $cn;
		if ( $p_array == null ) $p_array=$_POST;
		$this->id=$p_array[$this->name];
        $sql="insert into tool_uos(uos_value) values ($1)";
        try {
            $cn->exec_sql($sql,array($this->id));
        } catch (Exception $e)
        {
            throw new Exception('Duplicate value');
        }
    }
    /**
     * Count how many time we have this->id into the table tool_uos
     * @global $cn Database connx
     * @param $p_array is the array where to find the key name, usually it is
     * $_POST. The default value is $_POST
     * @return integer : 0 or 1
     */
    function get_count($p_array=null)
    {
        global $cn;
        if ( $p_array == null ) $p_array=$_POST;
        $this->id=$p_array[$this->name];
        $count=$cn->get_value('select count(*) from tool_uos where uos_value=$1',
                array($this->id));
        return $count;
    }
    function check ($p_array=null)
    {
        global $cn;
        if ( $p_array == null ) $p_array=$_POST;
        $this->id=$p_array[$this->name];
        try
        {
            $count=$cn->get_value('select count(*) from tool_uos where uos_value=$1',
                    array($this->id));
            if ($count != 0 ) throw new Exception ('DUPLICATE',CODE_EXCP_DUPLICATE);
        }catch (Exception $e)
        {
            throw $e;
        }
    }
}
?>
