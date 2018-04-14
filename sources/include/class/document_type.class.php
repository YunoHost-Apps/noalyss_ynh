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
/** \file
 * \brief  class for the table document_type
 */

/** \brief class for the table document_type
 * < dt_id pk document_type
 * < dt_value value
 */
class Document_type
{
	/** document_type
	 * \brief constructor
	 * \param $p_cn database connx
	 */

	function __construct($p_cn, $p_id = -1)
	{
		$this->db = $p_cn;
		$this->dt_id = $p_id;
	}

	/**
	 * \brief Get all the data for this dt_id
	 */

	function get()
	{
		$sql = "select * from document_type where dt_id=$1";
		$R = $this->db->exec_sql($sql, array($this->dt_id));
		if (count($R) == 0) return 1;
		$r = Database::fetch_array($R, 0);
		$this->dt_id = $r['dt_id'];
		$this->dt_value = $r['dt_value'];
		$this->dt_prefix = $r['dt_prefix'];
		return 0;
	}

	/**
	 * @brief get a list
	 * @param $p_cn database connection
	 * @return array of data from document_type
	 */
	static function get_list($p_cn)
	{
		$sql = "select * from document_type order by dt_value";
		$r = $p_cn->get_array($sql);
		$array = array();
		for ($i = 0; $i < count($r); $i++)
		{
			$tmp['dt_value'] = $r[$i]['dt_value'];
			$tmp['dt_prefix'] = $r[$i]['dt_prefix'];

			$bt = new IButton('M' . $r[$i]['dt_id']);
			$bt->label = _('Modifier');
			$bt->javascript = "cat_doc_change('" . $r[$i]['dt_id'] . "','" . Dossier::id() . "');";

			$tmp['js_mod'] = $bt->input();
			$tmp['dt_id'] = $r[$i]['dt_id'];

			$bt = new IButton('X' . $r[$i]['dt_id']);
			$bt->label = _('Effacer');
			$bt->javascript = "confirm_box('X{$r[$i]['dt_id']}','" . _('Vous confirmez') . "',";
                        $bt->javascript.="function () { cat_doc_remove('{$r[$i]['dt_id']}','" . Dossier::id() . "');})";

			$tmp['js_remove'] = $bt->input();


			$array[$i] = $tmp;
		}
		return $array;
	}

	function insert($p_value, $p_prefix)
	{
		$sql = "insert into document_type(dt_value,dt_prefix) values ($1,$2)";
		try
		{
			if ($this->db->count_sql('select * from document_type where upper(dt_value)=upper(trim($1))', array($p_value)) > 0)
				throw new Exception('Nom en double');
			if (strlen(trim($p_value)) > 0)
				$this->db->exec_sql($sql, array($p_value, $p_prefix));
		}
		catch (Exception $e)
		{
                    record_log($e->getTraceAsString());
			alert(j(_("Impossible d'ajouter [$p_value] ") . $e->getMessage()));
		}
	}

	/**
	 * Update
	 */
	function update()
	{
		try
		{
			$this->db->exec_sql("update document_type set dt_value=$1,dt_prefix=$2 where dt_id=$3", array($this->dt_value,
				$this->dt_prefix, $this->dt_id));
		}
		catch (Exception $e)
		{
                    record_log($e->getTraceAsString());
			alert(" Erreur " . $e->getMessage());
		}
	}

	function set_number($p_int)
	{
		try
		{
			$this->db->exec_sql("alter sequence seq_doc_type_" . $this->dt_id . " restart " . $p_int);
		}
		catch (Exception $e)
		{
                    record_log($e->getTraceAsString());
			alert("Erreur " . $e->getMessage());
		}
	}
}
