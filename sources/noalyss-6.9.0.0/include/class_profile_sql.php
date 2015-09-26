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
 * @brief Manage the table public.profile
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
 * @brief Manage the table public.profile
 */
class Profile_sql extends Noalyss_SQL
{
	/* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

	function __construct(& $p_cn, $p_id = -1)
	{
		$this->table = "public.profile";
		$this->primary_key = "p_id";

		$this->name = array(
			"p_id" => "p_id"
			, "p_name" => "p_name"
			, "p_desc" => "p_desc"
			, "with_calc" => "with_calc"
			, "with_direct_form" => "with_direct_form"
		);
		$this->type = array(
			"p_id" => "numeric"
			, "p_name" => "text"
			, "p_desc" => "text"
			, "with_calc" => "text"
			, "with_direct_form" => "text"
		);
		$this->default = array(
			"p_id" => "auto",
		);

		parent::__construct($p_cn,$p_id);

	}

}

// Profile_sql::test_me();
?>
