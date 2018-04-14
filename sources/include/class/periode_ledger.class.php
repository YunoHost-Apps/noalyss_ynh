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
 * @brief Manage the periode of a specific ledger 
 */
require_once NOALYSS_INCLUDE."/class/periode_ledger_table.class.php";
require_once NOALYSS_INCLUDE."/class/periode.class.php";
/**
 * @brief Manage the periode of a specif ledger, wrap the SQL Class Jrn_Periode_SQL
 * @see Periode
 * @see Periode_Ledger_Table
 * 
 */
class Periode_Ledger
{
    private $m_jrn_periode_sql;
    function __construct(Jrn_periode_SQL $p_jrn_periode_sql)
    {
        $this->m_jrn_periode_sql=$p_jrn_periode_sql;
    }
    public function get_jrn_periode_sql()
    {
        return $this->m_jrn_periode_sql;
    }

    public function set_jrn_periode_sql($m_jrn_periode_sql)
    {
        $this->m_jrn_periode_sql=$m_jrn_periode_sql;
    }
    /**
     * Close the month / periode for the ledger , 
     * Call Periode->close()
     * @see Periode::close
     */
    function close() 
    {
        $cn=Dossier::connect();
        $jrn_periode_sql=$this->m_jrn_periode_sql;
        $periode=new Periode($cn,$jrn_periode_sql->getp("p_id"));
        $periode->set_ledger($this->m_jrn_periode_sql->getp("jrn_def_id"));
        $periode->close();
    }
     /**
     * Reopen the month / periode for the ledger , 
     * Call Periode->reopen()
     * @see Periode::reopen
     */
    function reopen() 
    {
        $cn=Dossier::connect();
        $periode=new Periode($cn,$this->m_jrn_periode_sql->getp("p_id"));
        $periode->jrn_def_id=$this->m_jrn_periode_sql->getp("jrn_def_id");
        $periode->reopen();
    }
}

