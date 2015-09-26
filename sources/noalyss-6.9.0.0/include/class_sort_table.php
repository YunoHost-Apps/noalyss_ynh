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
 * Description of class_syn_sort_table
 *
 * @author dany
 */
class Sort_Table
{

    function __construct()
    {
	$this->nb = 0;
	$this->array = array();
    }

    /**
     *@brief add row of a header in the internal array ($this->array)
     * , it uses the $_GET['ord'] parameter,
     * @param type $p_header label of the header
     * @param type $p_url base url
     * @param type $p_sql_asc sql if ascending
     * @param type $p_sql_desc sql if descending
     * @param type $p_get_asc the value in $_GET if ascending is choosen
     * @param type $p_get_desc the value in $_GET if descending is choosen
     */
    function add($p_header, $p_url, $p_sql_asc, $p_sql_desc, $p_get_asc, $p_get_desc)
    {
	$array = array(
	    'head' => $p_header,
	    'url' => $p_url,
	    'sql_asc' => $p_sql_asc,
	    'sql_desc' => $p_sql_desc,
	    'parm_asc' => $p_get_asc,
	    'parm_desc' => $p_get_desc,
	    'car_asc' => '<span>&#9650</span>',
	    'car_desc' => '<span>&#9660</span>'
	);
	$ind = $this->nb;
	$this->array[$ind] = $array;
	$this->nb++;
    }
/**
 * Returns the header (the value into th tags) with the symbol ascending and
 * descending
 * @param  $p_ind the element (from 0 to nb)
 * @return string
 */
    function get_header($p_ind)
    {
	if ($p_ind < 0 || $p_ind > $this->nb)
	    return 'ERREUR TRI';
	$file = str_replace('extension.php', '', $_SERVER['SCRIPT_FILENAME']);

	$base = $this->array[$p_ind]['url'];
	$str = '';
	$str .= '<A style="display:inline" HREF="' . $base . '&ord=' . $this->array[$p_ind]['parm_asc'] . '">' .
		$this->array[$p_ind]['car_asc'] .
		'</A>' .
		$this->array[$p_ind]['head'] .
		'<A style="display:inline" HREF="' . $base . '&ord=' . $this->array[$p_ind]['parm_desc'] . '">' .
		 $this->array[$p_ind]['car_desc'] .
		'</A>';
	return $str;
    }

    function get_sql_order($p_get)
    {
	for ($i = 0; $i < $this->nb; $i++)
	{
	    if ($p_get == $this->array[$i]['parm_asc'])
	    {
		$this->array[$i]['car_asc'] = '<span style="color:red">&#9650</span>';
		return $this->array[$i]['sql_asc'];
	    }
	    if ($p_get == $this->array[$i]['parm_desc'])
	    {
		$this->array[$i]['car_desc'] = '<span style="color:red">&#9660</span>';
		return $this->array[$i]['sql_desc'];
	    }
	}
    }

}

?>
