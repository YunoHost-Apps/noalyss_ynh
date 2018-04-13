<?php

/*
 * Copyright (C) 2017 Dany De Bontridder <dany@alchimerys.be>
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


/* * *
 * @file 
 * @brief
 *
 */
require_once NOALYSS_INCLUDE."/lib/noalyss_sql.class.php";
class Tmp_Pcmn_SQL extends Noalyss_SQL
{

    /**
     * @brief manage table key_distribution_detail
     */
    function __construct($p_cn, $p_id=-1)
    {

        $this->table="public.tmp_pcmn";
        $this->primary_key="id";

        $this->name=array(
            "id"=>"id",
            "pcm_val"=>"pcm_val",
            "pcm_type"=>"pcm_type",
            "pcm_val_parent"=>"pcm_val_parent",
            "pcm_lib"=>"pcm_lib",
            "pcm_direct_use"=>"pcm_direct_use"
        );

        $this->type=array(
            "id"=>"numeric",
            "pcm_val"=>"text",
            "pcm_type"=>"text",
            "pcm_val_parent"=>"text",
            "pcm_lib"=>"text",
            "pcm_direct_use"=>"text"
        );

        $this->default=array(
            "id"=>"auto"
        );

        parent::__construct($p_cn, $p_id);
    }

}
