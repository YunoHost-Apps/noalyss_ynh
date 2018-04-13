<?php

/*
 * Copyright (C) 2018 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * @file
 * @brief Manage the table public.fiche_def_ref , which concerns the template of
 * category of card
 */
require_once NOALYSS_INCLUDE.'/lib/noalyss_sql.class.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';

/**
 * @class
 * @file Manage the table public.fiche_def_ref , which concerns the template of
 * category of card
 */
class Fiche_def_ref_SQL extends Noalyss_SQL
{

    function __construct(Database $p_cn, $p_id=-1)
    {
        $this->table="public.fiche_def_ref";
        $this->primary_key="frd_id";
        /*
         * List of columns
         */
        $this->name=array(
            "frd_id"=>"frd_id"
            , "frd_text"=>"frd_text"
            , "frd_class_base"=>"frd_class_base"
        );
        /*
         * Type of columns
         */
        $this->type=array(
            "frd_id"=>"numeric"
            , "frd_text"=>"text"
            , "frd_class_base"=>"text"
        );


        $this->default=array(
            "frd_id"=>"auto"
        );

        $this->date_format="DD.MM.YYYY";
        parent::__construct($p_cn, $p_id);
    }

}
