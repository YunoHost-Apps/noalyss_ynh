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
 * \brief Manage the table parm_code which contains the custom parameter
 * for the module accountancy
 */
/*!
 * \brief Manage the table parm_code which contains the custom parameter
 * for the module accountancy
 */
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_acc_account_ledger.php';

class Acc_Parm_Code
{
    var $db;        /*!< $db  database connection */
    var $p_code;    /*!< $p_code  parm_code.p_code primary key */
    var $p_value;   /*!< $p_value  parm_code.p_value  */
    var $p_comment; /*!< $p_comment parm_code.p_comment */
// constructor
    function Acc_Parm_Code($p_cn,$p_id=-1)
    {
        $this->db=$p_cn;
        $this->p_code=$p_id;
        if ( $p_id != -1 )
            $this->load();
    }
    /*!
     **************************************************
     * \brief  
     *  Load all parmCode
     *  return an array of Acc_Parm_Code object
     *
     * \return array
     */

    function load_all()
    {
        $sql="select * from parm_code order by p_code";
        $Res=$this->db->exec_sql($sql);
        $r= Database::fetch_all($Res);
        $idx=0;
        $array=array();

        if ( $r === false ) return null;
        foreach ($r as $row )
        {
            $o=new Acc_Parm_Code($this->db,$row['p_code']);
            $array[$idx]=$o;
            $idx++;
        }

        return $array;
    }
    /*!
    **************************************************
    * \brief  update a parm_object into the database
    *        p_code is _not_ updatable
    * \return
    *     nothing
    */
    function save()
    {
        // if p_code=="" nothing to save
        if ( $this->p_code== -1) return;
        // check if the account exists
        $acc=new Acc_Account_Ledger($this->db,$this->p_value);
        if ( $acc->load() == false )
        {
            alert(_("Ce compte n'existe pas"));
        }
        else
        {
            $this->p_comment=sql_string($this->p_comment);
            $this->p_value=sql_string($this->p_value);
            $this->p_code=sql_string($this->p_code);
            $sql="update parm_code set ".
                 "p_comment='".$this->p_comment."'  ".
                 ",p_value='".$this->p_value."'  ".
                 "where p_code='".$this->p_code."'";
            $Res=$this->db->exec_sql($sql);
        }
    }
    /*!
     **************************************************
     * \brief  Display an object, with the <TD> tag
     *        
     * \return
     *     string
     */
    function display()
    {
        $r="";
        $r.= '<TD>'.$this->p_code.'</TD>';
        $r.= '<TD>'.h($this->p_comment).'</TD>';
        $r.= '<TD>'.$this->p_value.'</TD>';

        return $r;
    }
    /*!
     **************************************************
     * \brief  Display a form to enter info about
     *        a parm_code object with the <TD> tag
     *    
     * \return string
     */
    function form()
    {
        $comment=new IText();
        $comment->name='p_comment';
        $comment->value=$this->p_comment;
        $comment->size=45;
        $value=new IPoste();
        $value->name='p_value';
        $value->value=$this->p_value;
        $value->size=7;
        $value->set_attribute('ipopup','ipop_account');
        $value->set_attribute('account','p_value');
        $poste=new IText();
        $poste->setReadOnly(true);
        $poste->size=strlen($this->p_code)+1;
        $poste->name='p_code';
        $poste->value=$this->p_code;
        $r="";
        $r.='<tr>';
        $r.='<td align="right"> Code </td>';
        $r.= '<TD>'.$poste->input().'</TD>';
        $r.='</tr>';
        $r.='<tr>';
        $r.='<td align="right"> Commentaire </td>';
        $r.= '<TD>'.$comment->input().'</TD>';
        $r.='</tr>';
        $r.='<tr>';
        $r.='<td align="right"> Poste comptable </td>';
        $r.= '<TD>'.$value->input();
        $r.='<span id="p_value_label"></span></td>';
        $r.='</tr>';
        $r.=Dossier::hidden();
        return $r;

    }

    /*!
     **************************************************
     * \brief  
     * Complete a parm_code object thanks the p_code 
     *        
     * \return array
     */

    function load()
    {
        if ( $this->p_code == -1 ) return "p_code non initialisé";
        $sql='select * from parm_code where p_code=$1 ';

        $Res=$this->db->exec_sql($sql,array($this->p_code));

        if ( Database::num_row($Res) == 0 ) return 'INCONNU';
        $row= Database::fetch_array($Res,0);
        $this->p_value=$row['p_value'];
        $this->p_comment=$row['p_comment'];

    }

}
