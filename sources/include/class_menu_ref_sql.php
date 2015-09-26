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
 * @brief Manage the table public.menu_ref
 *
 *
 */
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_noalyss_sql.php';

/**
 * @brief Manage the table public.menu_ref
 */
class Menu_Ref_SQL extends Noalyss_SQL
{
    protected  $table="public.menu_ref";
    protected  $primary_key="me_code";
    protected $name = array(
                    "me_code" => "me_code"
                    , "me_menu" => "me_menu"
                    , "me_file" => "me_file"
                    , "me_url" => "me_url"
                    , "me_description" => "me_description"
                    , "me_parameter" => "me_parameter"
                    , "me_javascript" => "me_javascript"
                    , "me_type" => "me_type"
                    , 'me_description_etendue'=>'me_description_etendue'
            );
    protected $type=array(
                    "me_code" => "text"
                    , "me_menu" => "text"
                    , "me_file" => "text"
                    , "me_url" => "text"
                    , "me_description" => "text"
                    , "me_parameter" => "text"
                    , "me_javascript" => "text"
                    , "me_type" => "text"
                    ,"me_description_etendue"=>"text"
            );
    function __construct(Database &$p_cn,$p_id=-1)
    {
        parent::__construct($p_cn,$p_id);
    }

}
?>
