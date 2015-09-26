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
 * \brief definition of the class Pre_op_ods
 */
require_once  NOALYSS_INCLUDE.'/class_pre_operation.php';

/*---------------------------------------------------------------------- */
/*!\brief concerns the predefined operation for ODS ledger
*/
class Pre_op_ods extends Pre_operation_detail
{
    var $op;
    function __construct($cn,$p_id=0)
    {
        parent::__construct($cn,$p_id);
        $this->operation->od_direct='f';
    }

    function get_post()
    {
        parent::get_post();
        $this->operation->od_direct='f';
        for ($i=0;$i<$this->operation->nb_item;$i++)
        {

            $this->{"e_account".$i}=$_POST['e_account'.$i];
            $this->{"e_account".$i."_amount"}=$_POST['e_account'.$i."_amount"];
            $this->{"e_account".$i."_type"}=$_POST['e_account'.$i."_type"];

        }
    }
    /*!
     * \brief save the detail and op in the database
     *
     */
    function save()
    {
        try
        {
            $this->db->start();
            if ($this->operation->save() == false )
                return;

            // save the selling
            for ($i=0;$i<$this->operation->nb_item;$i++)
            {
                $sql=sprintf('insert into op_predef_detail (opd_poste,opd_amount,'.
                             'opd_debit,od_id)'.
                             ' values '.
                             "('%s',%.2f,'%s',%d)",
                             $this->{"e_account".$i},
                             $this->{"e_account".$i."_amount"},
                             ($this->{"e_account".$i."_type"}=='d')?'t':'f',
                             $this->operation->od_id
                            );
                $this->db->exec_sql($sql);
            }
        }
        catch (Exception $e)
        {
            echo ($e->getMessage());
            $this->db->rollback();
        }

    }
    /*!\brief compute an array accordingly with the FormVenView function
     */
    function compute_array()
    {
        $count=0;
        $a_op=$this->operation->load();
        $array=$this->operation->compute_array($a_op);
        $p_array=$this->load();
        foreach ($p_array as $row)
        {
            $c=($row['opd_debit']=='t')?'d':'c';
            $array+=array("e_account".$count=>$row['opd_poste'],
                          "e_account".$count."_amount"=>$row['opd_amount'],
                          "e_account".$count."_type"=>$c
                         );
            $count++;

        }
        return $array;
    }
    /*!\brief load the data from the database and return an array
     * \return an array 
     */
    function load()
    {
        $sql="select opd_id,opd_poste,opd_amount,opd_debit".
             "  from op_predef_detail where od_id=".$this->operation->od_id.
             " order by opd_debit, opd_id,opd_amount";
        $res=$this->db->exec_sql($sql);
        $array=Database::fetch_all($res);
        return $array;
    }
    function set_od_id($p_id)
    {
        $this->operation->od_id=$p_id;
    }
}
