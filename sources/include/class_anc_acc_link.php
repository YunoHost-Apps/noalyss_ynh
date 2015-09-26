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

/*!\file
 * \brief link between accountancy and analytic, like table but as a listing
 */
require_once NOALYSS_INCLUDE.'/class_anc_print.php';

class Anc_Acc_Link extends Anc_Print
{
  function __contruct($p_cn)
  {
    $this->cn=$p_cn;
  }

  /**
   *@brief get the parameters
   */
  function get_request()
  {
    parent::get_request();
    $this->card_poste=HtmlInput::default_value('card_poste',1,$_GET);
  }
    function set_sql_filter()
    {
        $sql="";
        $and=" and ";
        if ( $this->from != "" )
        {
            $sql.="$and oa_date >= to_date('".$this->from."','DD.MM.YYYY')";
        }
        if ( $this->to != "" )
        {
            $sql.=" $and oa_date <= to_date('".$this->to."','DD.MM.YYYY')";
        }

        return $sql;

    }


}

