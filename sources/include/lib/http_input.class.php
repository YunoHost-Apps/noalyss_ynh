<?php

/*
 *   This file is part of PhpCompta.
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
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

/**
 * @file
 * @brief manage the http input (get , post, request)
 */


/**
 * @file
 * @brief manage the http input (get , post, request)
 */

class HttpInput
{

    private $array;

    function _construct()
    {
        $this->array=null;
    }

    /**
     * Check the type of the value
     * @param $p_name name of the variable
     * @param $p_type type of the variable (number,string,date,array)
     * @throws Exception if the variable doesn't exist or type incorrect
     * @todo Add regex:pattern
     */
    function check_type($p_name, $p_type)
    {
        try
        {
            // no check on string
            if ($p_type=="string")
                return;
            // Check if number
            else if ($p_type=="number")
            {
                if ( isNumber($this->array[$p_name])==0 )
                {
                    throw new Exception(_("Type invalide")."[ $p_name ] = {$this->array[$p_name]}"
                    , EXC_PARAM_TYPE);
                }
                $this->array[$p_name]=h($this->array[$p_name]);
            }
            // Check if date dd.mm.yyyy
            else if ($p_type=="date")
            {
                if (isDate($this->array[$p_name]) <> $this->array[$p_name])
                {
                    throw new Exception(_("Type invalide")."[ $p_name ] = {$this->array[$p_name]}"
                    , EXC_PARAM_TYPE);
                }
                $this->array[$p_name]=h($this->array[$p_name]);
            }
            else if ($p_type=="array")
            {
                if (!is_array($this->array[$p_name]) ) {
                    throw new Exception(_("Type invalide")."[ $p_name ] = {$this->array[$p_name]}"
                , EXC_PARAM_TYPE);
                }
                $this->array[$p_name]=h($this->array[$p_name]);
            }else {
                throw new Exception(_("Unknown type"));
            }
        }
        catch (Exception $ex)
        {
            throw $ex;
        }
    }

    /**
     * Retrieve from $this->array the variable
     * @param $p_name name of the variable
     * @param $p_type type of the variable (number,string,date('dd.mm.yyyy'),array)
     * @param $p_default default value is variable
     * @throws Exception if invalid
     * @see check_type
     */
    function get_value($p_name, $p_type="string", $p_default="")
    {
        try
        {
            if (func_num_args()==3)
            {
                if (array_key_exists($p_name,$this->array) )
                {
                    $this->check_type($p_name, $p_type);
                    return $this->array[$p_name];
                }
                else
                {
                    return $p_default;
                }
            }
            if (!array_key_exists($p_name,$this->array))
            {
                throw new Exception(_('Paramètre invalide')."[$p_name]",
                EXC_PARAM_VALUE);
            }
            $this->check_type($p_name, $p_type);
            return $this->array[$p_name];
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Retrieve from $_GET
     * @param $p_name name of the variable
     * @param $p_type type of the variable , opt. default string
     * @param $p_default default value is variable is not set
     * @throws Exception if invalid
     */
    function get($p_name, $p_type="string", $p_default="")
    {
        try
        {
            $this->array=$_GET;
            if (func_num_args()==1)
                return $this->get_value($p_name);
            if (func_num_args()==2)
                return $this->get_value($p_name, $p_type);
            if (func_num_args()==3)
                return $this->get_value($p_name, $p_type, $p_default);
        }
        catch (Exception $exc)
        {
            throw $exc;
        }
    }

    /**
     * Retrieve from $_POST
     * @param $p_name name of the variable
     * @param $p_type type of the variable , opt. default string
     * @param $p_default default value is variable is not set
     * @throws Exception if invalid
     */
    function post($p_name, $p_type="string", $p_default="")
    {
        try
        {
            $this->array=$_POST;
            if (func_num_args()==1)
                return $this->get_value($p_name);
            if (func_num_args()==2)
                return $this->get_value($p_name, $p_type);
            if (func_num_args()==3)
                return $this->get_value($p_name, $p_type, $p_default);
        }
        catch (Exception $exc)
        {
            throw $exc;
        }
    }
    /**
     * Retrieve from $_REQUEST
     * @param $p_name name of the variable
     * @param $p_type type of the variable , opt. default string
     * @param $p_default default value is variable is not set
     * @throws Exception if invalid
     */
    function request($p_name, $p_type="string", $p_default="")
    {
        try
        {
            $this->array=$_REQUEST;
            if (func_num_args()==1)
                return $this->get_value($p_name);
            if (func_num_args()==2)
                return $this->get_value($p_name, $p_type);
            if (func_num_args()==3)
                return $this->get_value($p_name, $p_type, $p_default);
        }
        catch (Exception $exc)
        {
            throw $exc;
        }
    }
    /**
     * Retrieve from $p_array
     * @param $p_array source 
     * @param $p_name name of the variable
     * @param $p_type type of the variable , opt. default string
     * @param $p_default default value is variable is not set
     * @throws Exception if invalid
     */
    function extract($p_array, $p_name, $p_type="string", $p_default="")
    {
        try
        {
            $this->array=$p_array;
            if (func_num_args()==2)
                return $this->get_value($p_name);
            if (func_num_args()==3)
                return $this->get_value($p_name, $p_type);
            if (func_num_args()==4)
                return $this->get_value($p_name, $p_type, $p_default);
        }
        catch (Exception $exc)
        {
            throw $exc;
        }
    }

    /**
     * Extract variable name from an exception message. If an exception is thrown
     * then thanks this function it is possible to know what variable triggers
     * the exception
     * @param type $p_string
     * @return string like "[variable]"
     */
    function extract_variable($p_string)
    {
        if (preg_match("/\[.*\]/", $p_string, $found)==1)
        {
            return $found[0];
        }
    }

}

?>
