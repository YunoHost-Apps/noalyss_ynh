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
 * Description of class_default_menu_sql
 *
 * @author dany
 */
require_once NOALYSS_INCLUDE.'/class_noalyss_sql.php';

class Default_Menu_SQL extends Noalyss_SQL
{
    var $md_id;
    var $md_code;
    var $me_code;

    function __construct(&$p_cn, $p_id = -1)
    {
        $this->table = "public.menu_default";
        $this->primary_key = "md_id";

        $this->name = array(
            "md_id"=>"md_id",
            "md_code" => "md_code",
            "me_code" => "me_code"
        );
        $this->type = array(
            "md_id"=>"md_id"
            ,"md_code" => "text"
            , "me_code" => "text"
        );
        $this->default = array(
            "md_id"
        );
        global $cn;

        parent::__construct($cn, $p_id);
    }

}
