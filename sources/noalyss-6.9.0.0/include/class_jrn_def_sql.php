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
 * @brief Manage the table public.jrn_def
 *
 *
  Example
  @code

  @endcode
 */
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';

/**
 * @brief Manage the table public.jrn_def
 */
class Jrn_Def_sql
{
	/* example private $variable=array("easy_name"=>column_name,"email"=>"column_name_email","val3"=>0); */

	protected $variable = array(
		"jrn_def_id" => "jrn_def_id",
		"jrn_def_name" => "jrn_def_name"
		, "jrn_def_class_deb" => "jrn_def_class_deb"
		, "jrn_def_class_cred" => "jrn_def_class_cred"
		, "jrn_def_fiche_deb" => "jrn_def_fiche_deb"
		, "jrn_def_fiche_cred" => "jrn_def_fiche_cred"
		, "jrn_deb_max_line" => "jrn_deb_max_line"
		, "jrn_cred_max_line" => "jrn_cred_max_line"
		, "jrn_def_ech" => "jrn_def_ech"
		, "jrn_def_ech_lib" => "jrn_def_ech_lib"
		, "jrn_def_type" => "jrn_def_type"
		, "jrn_def_code" => "jrn_def_code"
		, "jrn_def_pj_pref" => "jrn_def_pj_pref"
		, "jrn_def_bank" => "jrn_def_bank"
		, "jrn_def_num_op" => "jrn_def_num_op"
		, "jrn_def_description" => "jrn_def_description"
	);

	function __construct(& $p_cn, $p_id=-1)
	{
		$this->db = $p_cn;
		$this->jrn_def_id = $p_id;

		if ($p_id == -1)
		{
			/* Initialize an empty object */
			foreach ($this->variable as $key => $value)
				$this->$value = null;
			$this->jrn_def_id = $p_id;
		}
		else
		{
			/* load it */

			$this->load();
		}
	}

	public function get_parameter($p_string)
	{
		if (array_key_exists($p_string, $this->variable))
		{
			$idx = $this->variable[$p_string];
			return $this->$idx;
		}
		else
			throw new Exception(__FILE__ . ":" . __LINE__ . $p_string . 'Erreur attribut inexistant');
	}

	public function set_parameter($p_string, $p_value)
	{
		if (array_key_exists($p_string, $this->variable))
		{
			$idx = $this->variable[$p_string];
			$this->$idx = $p_value;
		}
		else
			throw new Exception(__FILE__ . ":" . __LINE__ . $p_string . 'Erreur attribut inexistant');
	}

	public function get_info()
	{
		return var_export($this, true);
	}

	public function verify_sql()
	{
		// Verify that the elt we want to add is correct
		/* verify only the datatype */
		if (trim($this->jrn_def_name) == '')
			$this->jrn_def_name = null;
		if (trim($this->jrn_def_class_deb) == '')
			$this->jrn_def_class_deb = null;
		if (trim($this->jrn_def_class_cred) == '')
			$this->jrn_def_class_cred = null;
		if (trim($this->jrn_def_fiche_deb) == '')
			$this->jrn_def_fiche_deb = null;
		if (trim($this->jrn_def_fiche_cred) == '')
			$this->jrn_def_fiche_cred = null;
		if (trim($this->jrn_deb_max_line) == '')
			$this->jrn_deb_max_line = null;
		if ($this->jrn_deb_max_line !== null && settype($this->jrn_deb_max_line, 'float') == false)
			throw new Exception('DATATYPE jrn_deb_max_line $this->jrn_deb_max_line non numerique');
		if (trim($this->jrn_cred_max_line) == '')
			$this->jrn_cred_max_line = null;
		if ($this->jrn_cred_max_line !== null && settype($this->jrn_cred_max_line, 'float') == false)
			throw new Exception('DATATYPE jrn_cred_max_line $this->jrn_cred_max_line non numerique');
		if (trim($this->jrn_def_ech) == '')
			$this->jrn_def_ech = null;
		if (trim($this->jrn_def_ech_lib) == '')
			$this->jrn_def_ech_lib = null;
		if (trim($this->jrn_def_type) == '')
			$this->jrn_def_type = null;
		if (trim($this->jrn_def_code) == '')
			$this->jrn_def_code = null;
		if (trim($this->jrn_def_pj_pref) == '')
			$this->jrn_def_pj_pref = null;
		if (trim($this->jrn_def_bank) == '')
			$this->jrn_def_bank = null;
		if ($this->jrn_def_bank !== null && settype($this->jrn_def_bank, 'float') == false)
			throw new Exception('DATATYPE jrn_def_bank $this->jrn_def_bank non numerique');
		if (trim($this->jrn_def_num_op) == '')
			$this->jrn_def_num_op = null;
		if ($this->jrn_def_num_op !== null && settype($this->jrn_def_num_op, 'float') == false)
			throw new Exception('DATATYPE jrn_def_num_op $this->jrn_def_num_op non numerique');
	}

	public function save($p_string='')
	{
		/* please adapt */
		if ($this->jrn_def_id == -1)
			$this->insert();
		else
			$this->update();
	}

	/**
	 * @brief retrieve array of object thanks a condition
	 * @param $cond condition (where clause) (optional by default all the rows are fetched)
	 * you can use this parameter for the order or subselect
	 * @param $p_array array for the SQL stmt
	 * @see Database::exec_sql get_object  Database::num_row
	 * @return the return value of exec_sql
	 */
	public function seek($cond='', $p_array=null)
	{
		$sql = "select * from public.jrn_def  $cond";
		$aobj = array();
		$ret = $this->db->exec_sql($sql, $p_array);
		return $ret;
	}

	/**
	 * get_seek return the next object, the return of the query must have all the column
	 * of the object
	 * @param $p_ret is the return value of an exec_sql
	 * @param $idx is the index
	 * @see seek
	 * @return object
	 */
	public function get_object($p_ret, $idx)
	{
		// map each row in a object
		$oobj = new Jrn_Def_sql($this->db);
		$array = Database::fetch_array($p_ret, $idx);
		foreach ($array as $idx => $value)
		{
			$oobj->$idx = $value;
		}
		return $oobj;
	}

	public function insert($p_array=null)
	{
		if ($this->verify_sql() != 0)
			return;
		if ($this->jrn_def_id == -1)
		{
			/*  please adapt */
			$sql = "insert into public.jrn_def(jrn_def_name
,jrn_def_class_deb
,jrn_def_class_cred
,jrn_def_fiche_deb
,jrn_def_fiche_cred
,jrn_deb_max_line
,jrn_cred_max_line
,jrn_def_ech
,jrn_def_ech_lib
,jrn_def_type
,jrn_def_code
,jrn_def_pj_pref
,jrn_def_bank
,jrn_def_num_op
,jrn_def_description
) values ($1
,$2
,$3
,$4
,$5
,$6
,$7
,$8
,$9
,$10
,$11
,$12
,$13
,$14
,$15
) returning jrn_def_id";

			$this->jrn_def_id = $this->db->get_value(
					$sql, array($this->jrn_def_name
				, $this->jrn_def_class_deb
				, $this->jrn_def_class_cred
				, $this->jrn_def_fiche_deb
				, $this->jrn_def_fiche_cred
				, $this->jrn_deb_max_line
				, $this->jrn_cred_max_line
				, $this->jrn_def_ech
				, $this->jrn_def_ech_lib
				, $this->jrn_def_type
				, $this->jrn_def_code
				, $this->jrn_def_pj_pref
				, $this->jrn_def_bank
				, $this->jrn_def_num_op
				, strip_tags($this->jrn_def_description)
					)
			);
		}
		else
		{
			$sql = "insert into public.jrn_def(jrn_def_name
,jrn_def_class_deb
,jrn_def_class_cred
,jrn_def_fiche_deb
,jrn_def_fiche_cred
,jrn_deb_max_line
,jrn_cred_max_line
,jrn_def_ech
,jrn_def_ech_lib
,jrn_def_type
,jrn_def_code
,jrn_def_pj_pref
,jrn_def_bank
,jrn_def_num_op
,jrn_def_id
,jrn_def_description) values ($1
,$2
,$3
,$4
,$5
,$6
,$7
,$8
,$9
,$10
,$11
,$12
,$13
,$14
,$15
,$16
) returning jrn_def_id";

			$this->jrn_def_id = $this->db->get_value(
					$sql, array($this->jrn_def_name
				, $this->jrn_def_class_deb
				, $this->jrn_def_class_cred
				, $this->jrn_def_fiche_deb
				, $this->jrn_def_fiche_cred
				, $this->jrn_deb_max_line
				, $this->jrn_cred_max_line
				, $this->jrn_def_ech
				, $this->jrn_def_ech_lib
				, $this->jrn_def_type
				, $this->jrn_def_code
				, $this->jrn_def_pj_pref
				, $this->jrn_def_bank
				, $this->jrn_def_num_op
				, $this->jrn_def_id
                                , strip_tags($this->jrn_def_description))
			);
		}
	}

	public function update($p_string='')
	{
		if ($this->verify_sql() != 0)
			return;
		/*   please adapt */
		$sql = " update public.jrn_def set jrn_def_name = $1
,jrn_def_class_deb = $2
,jrn_def_class_cred = $3
,jrn_def_fiche_deb = $4
,jrn_def_fiche_cred = $5
,jrn_deb_max_line = $6
,jrn_cred_max_line = $7
,jrn_def_ech = $8
,jrn_def_ech_lib = $9
,jrn_def_type = $10
,jrn_def_code = $11
,jrn_def_pj_pref = $12
,jrn_def_bank = $13
,jrn_def_num_op = $14
,jrn_def_description = $15
 where jrn_def_id= $16";
		$res = $this->db->exec_sql(
				$sql, array($this->jrn_def_name
			, $this->jrn_def_class_deb
			, $this->jrn_def_class_cred
			, $this->jrn_def_fiche_deb
			, $this->jrn_def_fiche_cred
			, $this->jrn_deb_max_line
			, $this->jrn_cred_max_line
			, $this->jrn_def_ech
			, $this->jrn_def_ech_lib
			, $this->jrn_def_type
			, $this->jrn_def_code
			, $this->jrn_def_pj_pref
			, $this->jrn_def_bank
			, $this->jrn_def_num_op
			, strip_tags($this->jrn_def_description)
			, $this->jrn_def_id)
		);
	}

	/**
	 * @brief load a object
	 * @return 0 on success -1 the object is not found
	 */
	public function load()
	{

		$sql = "select jrn_def_name
,jrn_def_class_deb
,jrn_def_class_cred
,jrn_def_fiche_deb
,jrn_def_fiche_cred
,jrn_deb_max_line
,jrn_cred_max_line
,jrn_def_ech
,jrn_def_ech_lib
,jrn_def_type
,jrn_def_code
,jrn_def_pj_pref
,jrn_def_bank
,jrn_def_num_op
,jrn_def_description
 from public.jrn_def where jrn_def_id=$1";
		/* please adapt */
		$res = $this->db->get_array(
				$sql, array($this->jrn_def_id)
		);

		if (count($res) == 0)
		{
			/* Initialize an empty object */
			foreach ($this->variable as $key => $value)
				$this->$key = '';

			return -1;
		}
		foreach ($res[0] as $idx => $value)
		{
			$this->$idx = $value;
		}
		return 0;
	}

	public function delete()
	{
		$sql = "delete from public.jrn_def where jrn_def_id=$1";
		$res = $this->db->exec_sql($sql, array($this->jrn_def_id));
	}

	/**
	 * Unit test for the class
	 */
	static function test_me()
	{
	}

}

// Jrn_Def_sql::test_me();
?>
