<?php

/*
 * Copyright (C) 2014 Dany De Bontridder <danydb@aevalys.eu>
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

/**
 * @file
 * @brief Class to manage distribution keys for SQL.  
 *
 */
require_once NOALYSS_INCLUDE.'/class_noalyss_sql.php';


/**
 * @brief Manage the table key_distribution.
 */
class Anc_Key_SQL extends Noalyss_SQL
{

    function __construct($p_cn, $p_id = -1)
    {
        $this->table = "public.key_distribution";
        $this->primary_key = "kd_id";

        $this->name = array(
            "id" => "kd_id",
            "name"=>"kd_name",
            "description"=>"kd_description"
        );

        $this->type = array(
            "kd_id" => "numeric",
            "kd_name" => "text",
            "kd_description" => "text"
        );

        $this->default = array(
            "kd_id" => "auto"
        );
       // PHPUNIT seems to have a problem with this line
       //global $cn;

        parent::__construct($p_cn, $p_id);
    }

}
/**
 * @brief manage table key_distribution_ledger
 */
class Anc_Key_Ledger_SQL extends Noalyss_SQL
{
       function __construct(&$p_cn, $p_id = -1)
    {
        $this->table = "public.key_distribution_ledger";
        $this->primary_key = "kl_id";

        $this->name = array(
            "id" => "kl_id",
            "key"=>"kd_id",
            "ledger"=>"jrn_def_id"
        );

        $this->type = array(
            "kl_id" => "numeric",
            "kd_id" => "numeric",
            "jrn_def_id" => "numeric"
        );

        $this->default = array(
            "kl_id" => "auto"
        );
        // PHPUNIT seems to have a problem with this line
       //global $cn;

        parent::__construct($p_cn, $p_id);
    } 
}
/**
 * @brief manage table key_distribution_detail
 */
class Anc_Key_Detail_SQL extends Noalyss_SQL
{
       function __construct(&$p_cn, $p_id = -1)
    {
          
        $this->table = "public.key_distribution_detail";
        $this->primary_key = "ke_id";

        $this->name = array(
            "id" => "ke_id",
            "key"=>"kd_id",
            "row"=>"ke_row",
            "percent"=>"ke_percent"
        );

        $this->type = array(
            "ke_id" => "numeric",
            "kd_id" => "numeric",
            "ke_row" => "numeric",
            "ke_percent" => "numeric"
        );

        $this->default = array(
            "ke_id" => "auto"
        );
       // PHPUNIT seems to have a problem with this line
       //global $cn;
       

        parent::__construct($p_cn, $p_id);
    } 
}
/**
 * @brief manage table key_distribution_activity
 */
class Anc_Key_Activity_SQL extends Noalyss_SQL
{
       function __construct($p_cn, $p_id = -1)
    {
        $this->table = "public.key_distribution_activity";
        $this->primary_key = "ka_id";

        $this->name = array(
            "id" => "ka_id",
            "detail"=>"ke_id",
            "activity"=>"po_id",
            "plan"=>"pa_id"
        );

        $this->type = array(
            "ka_id" => "numeric",
            "ke_id" => "numeric",
            "po_id" => "numeric",
            "pa_id" => "numeric"
           
        );

        $this->default = array(
            "ka_id" => "auto"
        );

        parent::__construct($p_cn, $p_id);
    } 
}