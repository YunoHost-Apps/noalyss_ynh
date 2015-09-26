<?php
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_noalyss_sql.php';
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
 * @brief Manage the table public.tag
 */
class Tag_SQL extends Noalyss_SQL
{
	/* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

	function __construct(& $p_cn, $p_id = -1)
	{
		$this->table = "public.tags";
		$this->primary_key = "t_id";

		$this->name = array(
			"t_id" => "t_id"
			, "t_tag" => "t_tag"
			, "t_description" => "t_description"
                    );
		$this->type = array(
			"t_id" => "numeric"
			, "t_tag" => "text"
			, "t_description" => "text"
		);
		$this->default = array(
			"t_id" => "auto",
		);

		parent::__construct($p_cn,$p_id);

	}

}
?>
