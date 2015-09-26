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
 * @brief Manage the table public.profile_menu
 *
 *
  Example
  @code

  @endcode
 */
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_noalyss_sql.php';

/**
 * @brief Manage the table public.profile_menu
 */
class Profile_Menu_sql extends Noalyss_SQL
{

    function __construct(&$p_cn,$p_id=-1)
    {
        $this->table="public.profile_menu";
        $this->primary_key="pm_id";

        $this->name=array(
            "pm_id"=>"pm_id", "me_code"=>"me_code"
            , "me_code_dep"=>"me_code_dep"
            , "p_id"=>"p_id"
            , "p_order"=>"p_order"
            , "p_type_display"=>"p_type_display"
            , "pm_default"=>"pm_default"
            ,"pm_id_dep"=>"pm_id_dep"
        );

        $this->type=array(
            "pm_id"=>"number",
            "me_code"=>"text"
            , "me_code_dep"=>"text"
            , "p_id"=>"number"
            , "p_order"=>"number"
            , "p_type_display"=>"text"
            , "pm_default"=>"text"
            , "pm_id_dep"=>"number"
        );

        $this->default=array(
            "pm_id"=>"auto"
        );

        parent::__construct($p_cn, $p_id);
    }

}

?>
