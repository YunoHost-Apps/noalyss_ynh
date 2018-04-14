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


/**
 * Description of Table_SQL
 *
 * @author dany
 */
class Table_SQL {
    private $table_name;
    private $schema_name;
    private $script;
    private $db;
    function __construct(Database $cn,$p_schematable) {
        list($this->schema_name,$this->table_name)=  explode(",",$p_schematable);
        $this->db=$cn;
    }	
    /**
     * Must create a Object to handle the SQL object
     */
    function create_class()
    {
        $this->script="";
        $columns=$this->db->get_array("
            select column_name,data_type,is_nullable from information_schema.columns  
                where
                table_name=$1
                and
                table_schema=$2 order by ordinal_position",
                array($this->table_name,$this->schema_name)
                );
        $apk=$this->db->get_array("
            select column_name from information_schema.constraint_column_usage where constraint_name = (select constraint_name from 
information_schema.table_constraints           
 where
            table_name = $1
            and table_schema=$2
            and constraint_type='PRIMARY KEY')
;
"               
                ,array($this->table_name,$this->schema_name));
        if ( count($apk) > 1 ) {
            $this->pk="pk composé";
        } else {
            $this->pk=$apk[0]['column_name'];
        }
        ob_start();
        include 'template/script_sql.php';
        $this->script= ob_get_contents();
        ob_clean();
    }
    function send()
    {
        echo $this->script;
    }
}
