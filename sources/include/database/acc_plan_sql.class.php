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
 * @file
 * @brief  Layer above Tmp_Pcmn_Sql
 */
require_once NOALYSS_INCLUDE."/lib/data_sql.class.php";
require_once NOALYSS_INCLUDE."/database/tmp_pcmn_sql.class.php";
/**
 * @brief this class is above tmp_pcmn_sql and is a view of tmp_pcmn
 * @see Tmp_Pcmn_SQL
 * @see Acc_Plan_MTable
 */
class Acc_Plan_SQL extends Data_SQL
{   

        private $limit_fiche_qcode;
    function __construct($p_cn, $p_id=-1)
    {
        $this->table = "accounting_card";
        $this->primary_key = "id";
        $this->limit_fiche_qcode=0;
        $this->name = array(
            "id" => "id",
            "pcm_val"=>"pcm_val",
            "parent_accounting"=>"parent_accounting",
            "pcm_lib"=>"pcm_lib",
            "pcm_type"=>"pcm_type",
            "fiche_qcode"=>"fiche_qcode",
            "pcm_direct_use"=>"pcm_direct_use"
        );

        $this->type = array(
            "id" => "numeric",
            "pcm_val" => "text",
            "parent_accounting" => "text",
            "pcm_lib" => "text",
            "pcm_type" => "text",
            "fiche_qcode"=>"string",
            "pcm_direct_use"=>"text"
        );

        $this->default = array(
            "id" => "auto",
            "fiche_qcode"=>"auto"
        );
        $this->sql="
      SELECT pcm_val,
      pcm_lib, 
      pcm_val_parent as parent_accounting, 
      pcm_type, 
      id,
      pcm_direct_use,
        (select string_agg(m.fiche_qcode,' , ') 
        from (select a.ad_value as fiche_qcode 
            from fiche_detail as a 
            join fiche_detail as b on (a.ad_id=23 and a.f_id=b.f_id and b.ad_id=5) 
            where b.ad_value=pcm_val::text order by a.ad_value %s)as m) as fiche_qcode
      FROM public.tmp_pcmn
            
";
        parent::__construct($p_cn,$p_id);
     }

    public function count($p_where="", $p_array=null)
    {
        throw new Exception("not implemented");
    }

    public function delete()
    {
        $obj=new Tmp_Pcmn_SQL($this->cn,$this->id);
        if ( $this->cn->get_value("select count(*) from jrnx where j_poste=$1",[$obj->pcm_val]) > 0)
        {
            throw new Exception(_("Impossible d'effacer : ce poste est utilisé"));
        }
        if ( $this->cn->get_value("select count(*) from tmp_pcmn where pcm_val_parent=$1",[$obj->pcm_val]) > 0)
        {
            throw new Exception(_("Impossible d'effacer : ce poste est utilisé"));
        }
        return $obj->delete();
        
    }

    public function exist()
    {
        $obj=new Tmp_Pcmn_SQL($this->cn,$this->id);
        return $obj->exist();
    }

    public function insert()
    {
        $obj=new Tmp_Pcmn_SQL($this->cn);
        $obj->set("pcm_val",$this->pcm_val);
        $obj->set("pcm_lib",$this->pcm_lib);
        $obj->set("pcm_type",$this->pcm_type);
        $obj->set("pcm_val_parent",$this->parent_accounting);
        $obj->set("pcm_direct_use",$this->pcm_direct_use);
        $obj->insert();
        $this->id=$obj->id;
    }
    public function get_pk_value()
    {
        return $this->id;
    }
    public function load()
    {
        $pk=$this->primary_key;
        if ( $this->get_limit_fiche_qcode() != 0 ) 
        {
            $sql=sprintf($this->sql," limit ".$this->get_limit_fiche_qcode());
        } else
        {
            $sql=sprintf($this->sql,"  ");
        }
        $result=$this->cn->get_array($sql. " where id=$1",array ($this->$pk));
        if ($this->cn->count()==0)
        {
            $this->$pk=-1;
            return;
        }

        foreach ($result[0] as $key=> $value)
        {
            $this->$key=$value;
        }
    }

    public function seek($cond='', $p_array=null)
    {
        if ( $this->get_limit_fiche_qcode() != 0 ) 
        {
            $sql=sprintf($this->sql," limit ".$this->get_limit_fiche_qcode());
        } else
        {
            $sql=sprintf($this->sql,"  ");
        }
        $ret=$this->cn->exec_sql($sql." ".$cond,$p_array);
        return $ret;
    }

    public function update()
    {
       $obj=new Tmp_Pcmn_SQL($this->cn,$this->id);
       $obj->set("pcm_val",$this->pcm_val);
       $obj->set("pcm_lib",$this->pcm_lib);
       $obj->set("pcm_type",$this->pcm_type);
       $obj->set("pcm_val_parent",$this->parent_accounting);
       $obj->set("pcm_direct_use",$this->pcm_direct_use);

       $obj->update(); 
    }
     public function get_limit_fiche_qcode()
    {
        return $this->limit_fiche_qcode;
    }

    public function set_limit_fiche_qcode($limit_fiche_qcode)
    {
        $this->limit_fiche_qcode=$limit_fiche_qcode;
    }
    

}