<?php

/*
 *   This file is part of NOALYSS.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief Add , delete and update Poste_Analytic
 * 
 */
/**
 * @class
 * @brief derived from Manage_Table_SQL , 
 */
require_once NOALYSS_INCLUDE."/lib/manage_table_sql.class.php";
require_once NOALYSS_INCLUDE.'/database/poste_analytique_sql.class.php';

class Anc_Account_Table extends Manage_Table_SQL
{
    function __construct(Data_SQL $p_table)
    {
        parent::__construct($p_table);
        $cn=Dossier::connect();
        $this->set_property_updatable("po_id", FALSE);
        $this->set_property_updatable("pa_id", FALSE);
        $this->set_property_visible("pa_id", FALSE);
        $this->set_property_visible("po_id", FALSE);
        $this->set_property_visible("po_amount", FALSE);
        $this->set_col_label("po_name", _("Label"));
        $this->set_col_label("po_description", _("Description"));
        $this->set_col_label("ga_id", _("Groupe"));
        $this->set_col_type("ga_id", "select");
        $this->set_object_name("anc_accounting");
        $this->set_col_sort(1);
        $this->a_select["ga_id"]=$cn->make_array("select '-','-' union all select ga_id,ga_id||' '||ga_description 
            from groupe_analytique
            where
            pa_id=$1
            order by 2",0,array($p_table->pa_id));
        
    }
    /**
     * Check and change po_name values
     * @return boolean
     */
    function check()
    {
        $cn=Dossier::connect();
        $table=$this->get_table();
        $is_error=0;
        $table->po_amount=0;
        // po_name must contains only valid letter (remove < > and ')
        $table->po_name=str_replace("'", '', $table->po_name);
        $table->po_name=str_replace("<", '', $table->po_name);
        $table->po_name=str_replace(">", '', $table->po_name);
        
        // po_name must be uniq in the Analytic Plan
        if ( $cn->get_value("select count(*) from poste_analytique where pa_id=$1 and po_name=$2 and po_id != $3",
                array($table->pa_id,$table->po_name,$table->po_id)) > 0)
        {
            $is_error++;
            $this->set_error("po_name", _("Le nom doit être unique dans un plan analytique"));
            
        }
        // po_name cannot be empty
        if (trim($table->po_name)=="") {
            $is_error++;
            $this->set_error("po_name", _("Le nom ne peut être vide"));
        }
        $table->ga_id=($table->ga_id=="-")?null:$table->ga_id;
        if ($is_error==0)return TRUE;
        return FALSE;
    }
}