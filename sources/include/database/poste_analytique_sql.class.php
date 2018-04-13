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
 * class_poste_analytique_sql.php
 *
 * @file
 * @brief abstract of the table public.poste_analytique 
 * 
 * 
 * @class
 * @brief abstract of the table public.poste_analytique */
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/lib/noalyss_sql.class.php';

class Poste_analytique_SQL extends Noalyss_SQL
{

    function __construct(Database $p_cn, $p_id=-1)
    {
        $this->table="public.poste_analytique";
        $this->primary_key="po_id";
        /*
         * List of columns
         */
        $this->name=array(
            "po_id"=>"po_id"
            , "po_name"=>"po_name"
            , "pa_id"=>"pa_id"
            , "po_amount"=>"po_amount"
            , "po_description"=>"po_description"
            , "ga_id"=>"ga_id"
        );
        /*
         * Type of columns
         */
        $this->type=array(
            "po_id"=>"numeric"
            , "po_name"=>"text"
            , "pa_id"=>"numeric"
            , "po_amount"=>"numeric"
            , "po_description"=>"text"
            , "ga_id"=>"text"
        );


        $this->default=array(
            "po_id"=>"auto"
        );

        $this->date_format="DD.MM.YYYY";
        parent::__construct($p_cn, $p_id);
    }

}
