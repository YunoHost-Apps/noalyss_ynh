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
 * \brief definition of the class Pre_op_fin
 */
require_once  NOALYSS_INCLUDE.'/class_pre_operation.php';

/*---------------------------------------------------------------------- */
/*!\brief concerns the predefined operation for FIN ledger
 */
class Pre_op_fin extends Pre_operation_detail
{
    var $op;
    function __construct($cn)
    {
        parent::__construct($cn);
        $this->operation->od_direct='f';
    }

    function get_post()
    {
        parent::get_post();
        $this->operation->od_direct='f';
        $this->e_bank_account=$_POST['e_bank_account'];
        for ($i=0;$i<$this->operation->nb_item;$i++)
        {
            $this->{"e_other".$i}=$_POST['e_other'.$i];
            $this->{"e_other".$i."_comment"}=$_POST['e_other'.$i.'_comment'];
            $this->{"e_other".$i."_amount"}=$_POST['e_other'.$i."_amount"];
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
            // save the client
            $sql=sprintf('insert into op_predef_detail (od_id,opd_poste,opd_debit)'.
                         ' values '.
                         "(%d,'%s','%s')",
                         $this->operation->od_id,
                         $this->e_bank_account,
                         "t");
            $this->db->exec_sql($sql);
            // save the selling
            for ($i=0;$i<$this->operation->nb_item;$i++)
            {
                $sql=sprintf('insert into op_predef_detail (opd_poste,'.
                             'opd_amount,opd_comment,'.
                             'opd_debit,od_id)'.
                             ' values '.
                             "('%s',%.2f,'%s','%s',%d)",
                             $this->{"e_other".$i},
                             $this->{"e_other".$i."_amount"},
                             $this->{"e_other".$i."_comment"},
                             'f',
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
            if ( $row['opd_debit']=='t')
            {
                $array+=array('e_bank_account'=>$row['opd_poste']);
            }
            else
            {
                $array+=array("e_other".$count=>$row['opd_poste'],
                              "e_other".$count."_amount"=>$row['opd_amount'],
                              "e_other".$count."_comment"=>$row['opd_comment']
                             );
                $count++;
            }
        }
        return $array;
    }
    /*!\brief load the data from the database and return an array
     * \return an array 
     */
    function load()
    {
        $sql="select opd_id,opd_poste,opd_amount,opd_comment,opd_debit".
             " from op_predef_detail where od_id=".$this->operation->od_id.
             " order by opd_id";
        $res=$this->db->exec_sql($sql);
        $array=Database::fetch_all($res);
        return $array;
    }
    function set_od_id($p_id)
    {
        $this->operation->od_id=$p_id;
    }
}
