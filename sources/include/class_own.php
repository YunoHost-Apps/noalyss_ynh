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
/*! \file
 * \brief Class to manage the company parameter (address, name...)
 */
/*!
 * \brief Class to manage the company parameter (address, name...)
 */

class Own
{
    var $db;
    var $MY_NAME;
    var $MY_TVA;
    var $MY_STREET;
    var $MY_NUMBER;
    var $MY_CP;
    var $MY_TEL;
    var $MY_PAYS;
    var $MY_COMMUNE;
    var $MY_FAX;
    var $MY_ANALYTIC;
    var $MY_STRICT;
    var $MY_TVA_USE;
    var $MY_PJ_SUGGEST;
    var $MY_CHECK_PERIODE;
    var $MY_DATE_SUGGEST;
    var $MY_ALPHANUM;
    var $MY_UPDLAB;
    var $MY_STOCK;
    
    // constructor
    function Own($p_cn)
    {
        $this->db=$p_cn;
        $Res=$p_cn->exec_sql("select * from parameter where pr_id like 'MY_%'");
        for ($i = 0;$i < Database::num_row($Res);$i++)
        {
            $row=Database::fetch_array($Res,$i);
            $key=$row['pr_id'];
            $elt=$row['pr_value'];
            // store value here
            $this->{"$key"}=$elt;
        }

    }
    function check(&$p_value)
    {
        if ($p_value == 'MY_STRICT'
                && $this->MY_STRICT != 'Y'
                && $this->MY_STRICT != 'N')
            $p_value='N';
        $p_value=htmlspecialchars($p_value);
    }
    /*!
     **************************************************
     * \brief  save the parameter into the database by inserting or updating
     *
     *
     * \param $p_attr give the attribut name
     *
     */
    function save($p_attr)
    {
        $this->check($p_attr);
        $value=$this->$p_attr;
        // check if the parameter does exist
        if ( $this->db->get_value('select count(*) from parameter where pr_id=$1',array($p_attr)) != 0 )
        {
            $Res=$this->db->exec_sql("update parameter set pr_value=$1 where pr_id=$2",
                                     array($value,$p_attr));
        }
        else
        {

            $Res=$this->db->exec_sql("insert into parameter (pr_id,pr_value) values( $1,$2)",
                                     array($p_attr,$value));

        }

    }

    /*!
     **************************************************
     * \brief  save data
     *
     *
     */
    function update()
    {

        $this->save('MY_NAME');
        $this->save('MY_TVA');
        $this->save('MY_STREET');
        $this->save('MY_NUMBER');
        $this->save('MY_CP');
        $this->save('MY_TEL');
        $this->save('MY_PAYS');
        $this->save('MY_COMMUNE');
        $this->save('MY_FAX');
        $this->save('MY_ANALYTIC');
        $this->save('MY_STRICT');
        $this->save('MY_TVA_USE');
        $this->save('MY_PJ_SUGGEST');
        $this->save('MY_CHECK_PERIODE');
        $this->save('MY_DATE_SUGGEST');
        $this->save('MY_ALPHANUM');
        $this->save('MY_UPDLAB');
        $this->save('MY_STOCK');


    }

}
