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
 * \brief Manage the account
 */
/*!
 * \brief Manage the account from the table tmp_pcmn
 */
require_once NOALYSS_INCLUDE.'/lib/iselect.class.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/database/tmp_pcmn_sql.class.php';

class Acc_Account
{
    var $db;          /*!< $db database connection */
    private $data_sql ;//< Tmp_Pcmn_SQL
    static public $type=array(
                            array('label'=>'Actif','value'=>'ACT'),
                            array('label'=>'Passif','value'=>'PAS'),
                            array('label'=>'Actif c. inverse','value'=>'ACTINV'),
                            array('label'=>'Passif c.inverse','value'=>'PASINV'),
                            array('label'=>'Produit','value'=>'PRO'),
                            array('label'=>'Produit Inverse','value'=>'PROINV'),
                            array('label'=>'Charge','value'=>'CHA'),
                            array('label'=>'Charge Inverse','value'=>'CHAINV'),
                            array('label'=>'Contexte','value'=>'CON')
                        );
    /**
     * 
     * @param type $p_cn Database connection
     * @param type $pcm_val Accounting tmp_pcmn.pcm_val
     */
    function __construct (Database $p_cn,$pcm_val="")
    {
        $this->db=$p_cn;
        $id=-1;
        if ( trim($pcm_val)  != "" ) {
            $pcm_val=$this->db->get_value("select format_account($1)",
                                        array($pcm_val));
            $id=$p_cn->get_value("select id from tmp_pcmn where pcm_val=$1",[$pcm_val]);
        }
        if ( $id == "") { $id=-1;}
        $this->data_sql=new Tmp_Pcmn_SQL($p_cn, $id);
        $this->data_sql->pcm_val=$pcm_val;

    }
    public function get_parameter($p_string)
    {
       return $this->data_sql->getp($p_string);
    }

    function set_parameter($p_string,$p_value)
    {
       return $this->data_sql->setp($p_string,$p_value);

    }
    /*!\brief Return the name of a account
     *        it doesn't change any data member
     * \return string with the pcm_lib
     */
    function get_lib()
    {
        $ret=$this->data_sql->getp('pcm_lib');
        if ( $ret !="")
        {
            return $ret;
        }
        else
        {
            return _("Poste inconnu");
        }
    }
    function searchValue($p_value) {
        
        
    }
    /*!\brief Get all the value for this object from the database
     *        the data member are set
     * \return false if this account doesn't exist otherwise true
     */
    function load()
    {
      $this->data_sql->load();
    }
    
    function count($p_value)
    {
        $sql="select count(*) from tmp_pcmn where pcm_val=$1";
        return $this->db->get_value($sql,array($p_value));
    }
    /**
     * Find the parent of an account
     * @return string (pcm_val)
     */
    function find_parent() {
        $name=$this->data_sql->pcm_val;
        $length_name=strlen($name);
        $parent="";
        for ($i = 1;$i <$length_name;$i++) {
            $parent=mb_substr($name, 0, $length_name-$i);
            $exist=$this->db->get_value("select count(*) from tmp_pcmn where pcm_val=$1",[$parent]);
            if ( $exist == 1) return $parent;
        }
        
        return $parent;
    }
    /**
     * Check before inserting or updating
     */
    function verify() {
        // check for Duplicate key, parent ... see Acc_Plan_MTable
        $count=$this->data_sql->count(" where pcm_val =$1 and id <> $2",
                           [$this->data_sql->pcm_val,$this->data_sql->id]);
        if ( $count > 0)
            throw new Exception (_("Poste en double"),EXC_DUPLICATE);
        
        if (trim($this->data_sql->pcm_lib)=="")
            throw new Exception (_("Libellé vide"),EXC_PARAM_VALUE);
        
        // can not depend of itself
        if ( $this->data_sql->pcm_val_parent == $this->data_sql->pcm_val)
            throw new Exception (_("Poste parent incorrect"),EXC_PARAM_VALUE);
        
        if ( $this->data_sql->pcm_val_parent == "") {
            $account=$this->find_parent();
            $this->data_sql->pcm_val_parent=$account;
            if ($account == "") 
                throw new Exception (_("Poste Parent n'existe pas"),EXC_PARAM_VALUE);
        }
        
        // purpose not clear
        if ( $this->data_sql->count(" where pcm_val = $1 and pcm_val <> $2",
                [$this->data_sql->pcm_val_parent,$this->data_sql->pcm_val])  == 0)
            throw new Exception (_("Poste Parent n'existe pas"),EXC_PARAM_VALUE);
        
     
        if ( $this->data_sql->pcm_direct_use != 'N' && $this->data_sql->pcm_direct_use != 'Y') 
            throw new Exception (_("Paramètre incorrect"),EXC_PARAM_VALUE);
        
        if ( trim($this->data_sql->pcm_val)==""||trim($this->data_sql->pcm_val_parent)=="")
            throw new Exception (_("Paramètre incorrect"),EXC_PARAM_VALUE);
        
        if ( strlen($this->data_sql->pcm_val)>40) {
            throw new Exception (_("Poste comptable doit être de 40 caractères maximum"),EXC_PARAM_VALUE);
        }
                
    }
    function update() {
        // check for Duplicate key, parent ... see Acc_Plan_MTable
        $this->verify();
        $this->data_sql->update();
    }
    function insert() {
        // check for Duplicate key, parent ... see Acc_Plan_MTable
        $this->verify();
        $this->data_sql->insert();
    }
    function delete() {
        // if already use cannot be deleted
        if ( $this->data_sql->count("where pcm_val in (select j_poste from jrnx where j_poste=$1) or pcm_val_parent=$1", 
                [$this->data_sql->pcm_val]) > 0)
        {
            throw new Exception(_("Poste utilisé : effacement interdit"),EXC_PARAM_VALUE);
        }
        $this->data_sql->delete();

    }
    function find_by_value($p_pcm_val)
    {
        $id=$this->db->get_value("select id from tmp_pcmn where pcm_val=$1",[$p_pcm_val]);
        $this->data_sql->setp("id",$id);
        $this->data_sql->load();
    }
    function save() {
        try {
            $this->verify();
            $this->data_sql->save();
        } catch (Exception $e) {
            throw $e;
        }
    }
 }
