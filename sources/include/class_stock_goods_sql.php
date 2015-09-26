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
 * @brief
 *
 */
require_once NOALYSS_INCLUDE.'/class_noalyss_sql.php';

class Stock_Goods_Sql extends Noalyss_SQL
{

	function __construct($cn,$p_id = -1)
	{
		$this->table = "public.stock_goods";
		$this->primary_key = "sg_id";
		$this->date_format="DD.MM.YYYY";

		$this->name = array(
			"sg_id" => "sg_id",
			"j_id" => "j_id",
			"f_id" => "f_id",
			"sg_code" => "sg_code",
			"sg_quantity" => "sg_quantity",
			"sg_type" => "sg_type",
			"sg_date" => "sg_date",
			"sg_tech_date" => "sg_tech_date",
			"sg_tech_user" => "sg_tech_user",
			"sg_comment" => "sg_comment",
			"sg_exercice" => "sg_exercice",
			"r_id" => "r_id",
			"c_id"=>"c_id"
		);

		$this->type = array(
			"sg_id" => "numeric",
			"j_id" => "numeric",
			"f_id" => "numeric",
			"sg_code" => "text",
			"sg_quantity" => "text",
			"sg_type" => "text",
			"sg_date" => "date",
			"sg_tech_date" => "date",
			"sg_tech_user" => "text",
			"sg_comment" => "text",
			"sg_exercice" => "sg_exercice",
			"r_id" => "numeric",
			"c_id" => "numeric"

		);

		$this->default = array(
			"sg_id" => "auto",
			"sg_tech_date" => "auto",
			"sg_user" => "auto"
		);
		global $cn;

		parent::__construct($cn, $p_id);
	}

}

class Stock_Change_Sql extends Noalyss_SQL
{

	function __construct($cn,$p_id = -1)
	{
		$this->date_format="DD.MM.YYYY";
		$this->table = "public.stock_change";
		$this->primary_key = "c_id";

		$this->name = array(
			"id" => "c_id",
			"c_comment" => "c_comment",
			"c_date" => "c_date",
			"tech_date"=>"tech_date",
			"tech_user"=>"tech_user",
			"r_id"=>"r_id"
		);

		$this->type = array(
			"c_id" => "numeric",
			"c_comment" => "text",
			"c_date" => "date",
			"tech_date"=>"date",
			"tech_user"=>"text",
			"r_id"=>"numeric"
		);

		$this->default = array(
			"c_id" => "auto",
			"tech_date" => "auto"
		);
		global $cn;

		parent::__construct($cn, $p_id);
	}
}
?>
