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


/**
 * @file
 * @brief Manage the periode per ledger, used the table jrn_periode
 * @example periode-ledger-test.php
 */
require_once NOALYSS_INCLUDE.'/database/jrn_periode_sql.class.php';

/**
 * @brief Display the periode per ledger: close : reopen ...
 * the close , open must be done thanks Periode
 * @see Periode
 */
class Periode_Ledger_Table
{

    private $sql;
    private $a_member;
    private $cn;
    
    function __construct($p_id)
    {
        $this->a_member["id"]=$p_id;
        $this->sql="select id,p_id,status,p_id,p_start,p_end,p_closed,p_exercice,jrn_def_id
                from 
                 jrn_periode 
                 join parm_periode using (p_id)";
        $this->cn=Dossier::connect();
        $this->a_member=$this->cn->get_row($this->sql." where id = $1",
                [$this->a_member["id"]]);
        
    }
    public function get_a_member()
    {
        return $this->a_member;
    }


    function set_id($p_id)
    {
        $a_member['id']=$p_id;
    }
    /**
     * Load a row and return it as an array
     * @return type
     */
    function load()
    {
        $this->a_member=$this->cn->get_row($this->sql." where id = $1",
                [$this->a_member["id"]]);
        return $this->a_member;
    }
    function get_resource_periode_ledger($p_ledger_id) 
    {
        $ret=$this->cn->exec_sql($this->sql." where jrn_def_id = $1 order by p_start desc",[$p_ledger_id]);
        return  $ret;
    }
    /**
     * Display all the periode for a specific ledger 
     * the ledger is in m_jrn_periode_sql
     * 
     * @param type $p_resource_psql resource SQL
     * @param type $p_js
     * @return type
     * @see Periode_Ledger_Table::get_resource_periode_ledger
     */
    function display_table($p_resource_psql, $p_js)
    {
        $nb_periode=Database::num_row($p_resource_psql);

        if ($nb_periode==0)
            return;
        echo '<table class="result" id="periode_tbl">';
        echo "<thead>";
        echo "<tr>";
        echo th("");
        echo th(_("Date Début"));
        echo th(_("Date Fin"));
        echo th(_("Exercice"));
        echo th("");
        echo "</tr>";
        echo "</thead>";
        echo '<tbody>';

        for ($i=0; $i<$nb_periode; $i++)
        {
            $obj=Database::fetch_array($p_resource_psql, $i);
            $this->display_row($obj, $i, $p_js);
        }
        echo '</tbody>';
        echo '</table>';
    }
    /**
     * Display one row from jrn_periode with supplemental info
     * 
     * @param type $pa_row is an array corresponding to a_member
     * @param type $p_num number of rows , for color
     * @param type $p_js name of the js variable passed to ajax
     */
    function display_row($pa_row, $p_num, $p_js)
    {
        $class=($p_num%2==0)?"even":"odd";
        printf('<tr id="row_per_%d" per_ledger="%s" per_exercice="%s" jrn_ledger_id="%s" class="%s"> ',
                $pa_row["id"], $pa_row["jrn_def_id"], $pa_row["p_exercice"],
                $pa_row["id"], $class
        );
        /**
         * Display a checkbox to select several month to close
         */
        if ($pa_row["status"] == "OP") {
            $checkbox=new ICheckBox("sel_per_close[]");
            $checkbox->set_attribute("per_id", $pa_row['id']);
            $checkbox->value=$pa_row['id'];
            echo "<td>".$checkbox->input()."</td>";
        }else {
            echo td("");
        }
        echo td(format_date($pa_row["p_start"], "YYYY-MM-DD", "DD.MM.YYYY"));
        echo td(format_date($pa_row["p_end"], "YYYY-MM-DD", "DD.MM.YYYY"));
        echo td($pa_row["p_exercice"]);
        $status=($pa_row['p_closed']=='t')?_("Fermée"):_("Ouvert");
        echo "<td>";
        if ($pa_row["status"] == "OP") { echo _("Ouvert"); }
        if ($pa_row["status"] == "CL") { echo _("Fermé"); }
        echo "</td>";
        /// Can close if open
        echo "<td>";
        if ($pa_row['status']=='OP')
        {
            $javascript=sprintf("%s.close_periode('%d')", $p_js, $pa_row['id']);
            echo Icon_Action::iconon(uniqid(), $javascript);
        }
        elseif ($pa_row['status']=='CL')
        {
            $javascript=sprintf("%s.open_periode('%d')", $p_js, $pa_row['id']);
            echo Icon_Action::iconoff(uniqid(), $javascript);
        }
        echo "</td>";
        echo "</tr>";
    }

}
