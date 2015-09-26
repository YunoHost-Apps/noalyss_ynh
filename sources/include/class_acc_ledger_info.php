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
require_once  NOALYSS_INCLUDE.'/class_dossier.php';
require_once  NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';

/*!\file
 * \brief Manage additional info for Accountancy
 */

/*!
 * \brief Manage the additionnal info for operation (from jrn), when an invoice is generated, 
 * the order or other info are going to be stored and used in the detail.
 * this class maps the table jrn_info
 */
class Acc_Ledger_Info
{
    var $cn;    /*!< connection */
    var $ji_id;    /*!< primary key */
    var $id_type;    /*!< type id */
    var $jr_id;    /*!< primary key of the table jrn */
    var $ji_value;	/*!< value for this */
    function __construct($p_cn,$p_ji_id=0)
    {
        $this->cn=$p_cn;
        $this->ji_id=$p_ji_id;
    }
    function insert()
    {
        if ( ! isset ($this->jr_id) ||
                ! isset ($this->ji_value) ||
                ! isset ($this->id_type ) )
        {
            echo 'Appel incorrecte '.__FILE__.__LINE__;
            var_dump($this);
            throw new Exception(_('appel incorrect'));
        }
        try
        {
            $sql=$this->cn->exec_sql('insert into jrn_info(jr_id,id_type,ji_value) values ($1,$2,$3)'.
                                     ' returning ji_id ',
                                     array ($this->jr_id,$this->id_type,$this->ji_value)
                                    );
            $this->ji_id=Database::fetch_result($sql,0,0);
        }
        catch (Exception $e)
        {
            echo "Echec sauvegarde info additionnelles";
            throw $e;
        }
    }
    function update()
    {
        if ( ! isset ($this->jr_id) ||
                ! isset ($this->ji_value) ||
                ! isset ($this->jr_id ) )
        {
            echo 'Appel incorrecte '.__FILE__.__LINE__;
            var_dump($this);
            throw  new Exception('appel incorrect');
        }
        try
        {
            $sql=$this->exec_sql('update jrn_info set jr_id=$1 ,id_type=$2,ji_value=$3 where ji_id=$4)'.
                                 array ($this->jr_id,$this->id_type,$this->ji_value,$this->ji_id)
                                );
        }
        catch (Exception $e)
        {
            $this->cn->rollback();
            echo "Echec sauvegarde info additionnelles";
            throw $e;
        }
    }
    function load()
    {
        $sql="select jr_id,id_type,ji_value from jrn_info where ji_id=".$this->ji_id;
        $r=$this->cn->exec_sql($sql);
        if (Database::num_row ($r) > 0 )
        {
            $this->from_array(Database::fetch_array($r,0));
            return 0;
        }
        else
        {
            return 1;
        }

    }
    function from_array($p_array)
    {
        foreach ($p_array as $col=>$value)
        {
            $this->$col=$value;
        }
    }
    function set_id($p_ji_id)
    {
        $this->$ji_id=$p_ji_id;
    }
    function set_jrn_id($p_id)
    {
        $this->jr_id=$p_id;
    }
    function set_type($p_id)
    {
        $this->id_type=$p_id;
    }
    function set_value($p_id)
    {
        $this->ji_value=$p_id;
    }
    /*!\brief load all the jrn_info thanks the jr_id
     * \return an array of object
     */
    function load_all()
    {
        if ( ! isset ($this->jr_id) )
        {
            echo "jr_id is not set ".__FILE__.__LINE__;
            throw new Exception('Error : jr_id not set');
        }

        $sql="select ji_id from jrn_info where jr_id=".$this->jr_id;
        $r=$this->cn->exec_sql($sql);
        if (Database::num_row($r) == 0 )
            return array();
        $array=Database::fetch_all($r);
        $ret=array();
        foreach ($array as $row)
        {
            $o=new Acc_Ledger_Info($this->cn,$row['ji_id']);
            $o->load();
            $ret[]=clone $o;
        }
        return $ret;

    }
    function count()
    {
        $sql="select ji_id from jrn_info where jr_id=".$this->jr_id;
        return $this->cn->count_sql($sql);
    }
    function search_id_internal($p_internal)
    {
        $sql="select jr_id from jrn where jr_internal='$p_internal'";
        $r=$this->cn->exec_sql($sql);
        if (Database::num_row($r) > 0 )
        {
            $this->jr_id=Database::fetch_result($r,0,0);
            return $this->jr_id;
        }
        else
        {
            $this->jr_id=-1;
            return $this->jr_id;
        }
    }
    /**
     *@brief save all extra information in once, called by compta_ven and compta_ach
     *@param $p_jr_id is the jrn.jr_id concerned, 
     *@param $p_array is the array with the data usually it is $_POST
     *@note will change this->jr_id
     *@see compta_ven.inc.php compta_ach.inc.php
     */
    function save_extra($p_jr_id,$p_array)
    {
        $this->jr_id=$p_jr_id;
        if (strlen(trim($p_array['bon_comm'] )) != 0 )
        {
            $this->set_type('BON_COMMANDE');
            $this->set_value($p_array['bon_comm']);
            $this->insert();
        }
        if (strlen(trim($p_array['other_info'] )) != 0 )
        {
            $this->set_type('OTHER');
            $this->set_value($p_array['other_info']);
            $this->insert();
        }
    }
    static function test_me()
    {
        echo "Dossier = ".Dossier::id();
        $cn=new Database(Dossier::id());
        $a=new Acc_Ledger_Info($cn);
        $a->jr_id=3;
        $a->id_type='BON_COMMANDE';
        $a->ji_value='BON';
        var_dump($a);
        $a->insert();

        $a->set_jrn_id(7);
        $a->set_type('OTHER');
        $a->set_value('Autre test');
        $a->insert();
    }
}
