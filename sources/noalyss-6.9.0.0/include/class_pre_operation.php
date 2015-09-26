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
 * \brief definition of Pre_operation
 */

/*! \brief manage the predefined operation, link to the table op_def
 * and op_def_detail
 *
 */
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_pre_op_ach.php';
require_once NOALYSS_INCLUDE.'/class_pre_op_ven.php';
require_once NOALYSS_INCLUDE.'/class_pre_op_advanced.php';
class Pre_operation
{
    var $db;						/*!< $db database connection */
    var $nb_item;					/*!< $nb_item nb of item */
    var $p_jrn;					/*!< $p_jrn jrn_def_id */
    var $jrn_type;					/*!< $jrn_type */
    var $name;						/*!< $name name of the predef. operation */

    function Pre_operation($cn,$p_id=0)
    {
        $this->db=$cn;
        $this->od_direct='false';
        $this->od_id=$p_id;
    }
    /**
     * @brief Propose to save the operation into a predefined operation
     * @return HTML  string
     */
    static function save_propose() {
        $r="";
        $r.= '<p class="decale">';
        $r.= _("Donnez un nom pour sauver cette opération comme modèle")." <br>";
        $opd_name = new IText('opd_name');
        $r.= "Nom du modèle " . $opd_name->input();
        $opd_description=new ITextarea('od_description');
        $opd_description->style=' class="itextarea" style="width:30em;height:4em;vertical-align:top"';
        $r.='</p>';
        $r.= '<p class="decale">';
        $r.= _('Description (max 50 car.)');   
        $r.='<br>';
        $r.=$opd_description->input();
        $r.='</p>';
        return $r;
    }

    /*!\brief fill the object with the $_POST variable */
    function get_post()
    {
        $this->nb_item=$_POST['nb_item'];
        $this->p_jrn=$_REQUEST['p_jrn'];
        $this->jrn_type=$_POST['jrn_type'];
        
	$this->name=$_POST['opd_name'];

        $this->name=(trim($this->name)=='')?$_POST['e_comm']:$this->name;
        $this->description=  $_POST['od_description'];
        if ( $this->name=="")
        {
            $n=$this->db->get_next_seq('op_def_op_seq');
            $this->name=$this->jrn_type.$n;
            // common value
        }
    }
    function delete ()
    {
        $sql="delete from op_predef where od_id=".$this->od_id;
        $this->db->exec_sql($sql);
    }
    /*!\brief save the predef check first is the name is unique
     * \return true op.success otherwise false
     */
    function save()
    {

        if (	$this->db->count_sql("select * from op_predef ".
                                  "where upper(od_name)=upper('".Database::escape_string($this->name)."')".
                                  "and jrn_def_id=".$this->p_jrn)
                != 0 )
        {
            $this->name="copy_".$this->name."_".microtime(true);
        }
        if ( $this->count()  > MAX_PREDEFINED_OPERATION )
        {
            echo '<span class="notice">'.("Vous avez atteint le max. d'op&eacute;ration pr&eacute;d&eacute;finie, d&eacute;sol&eacute;").'</span>';
            return false;
        }
        $sql='insert into op_predef (jrn_def_id,od_name,od_item,od_jrn_type,od_direct,od_description)'.
                     'values'.
                     "($1,$2,$3,$4,$5  ,$6               )";
        $this->db->exec_sql($sql,array($this->p_jrn,
                     $this->name,
                     $this->nb_item,
                     $this->jrn_type,
                     $this->od_direct,
                     $this->description,
            ));
        $this->od_id=$this->db->get_current_seq('op_def_op_seq');
        return true;
    }
    /*!\brief load the data from the database and return an array
     * \return an array
     */
    function load()
    {
        $sql="select od_id,jrn_def_id,od_name,od_item,od_jrn_type,od_description".
             " from op_predef where od_id=".$this->od_id.
             " order by od_name";
        $res=$this->db->exec_sql($sql);
        $array=Database::fetch_all($res);
        foreach (array('jrn_def_id','od_name','od_item','od_jrn_type','od_description') as $field) {
            $this->$field=$array[0][$field];
        }
        switch ($this->od_jrn_type) {
            case 'ACH':
                $this->detail=new Pre_op_ach($this->db);
                break;
            case 'VEN':
                $this->detail=new Pre_Op_ven($this->db);
                break;
            case 'ODS':
                $this->detail=new Pre_op_advanced($this->db);
                break;
            default:
                throw new Exception('Load PreOperatoin failed'.$this->od_jrn_type);
          }
        $this->detail->set_od_id($this->od_id);
        $this->detail->jrn_def_id=$this->jrn_def_id;
        
        return $array;
    }
    function compute_array()
    {
        $p_array=$this->load();
        $array=array(
                   "e_comm"=>$p_array[0]["od_name"],
                   "nb_item"=>(($p_array[0]["od_item"]<10?10:$p_array[0]["od_item"]))   ,
                   "p_jrn"=>$p_array[0]["jrn_def_id"],
                   "jrn_type"=>$p_array[0]["od_jrn_type"],
                   "od_description"=>$p_array['0']['od_description']
               );
        return $array;

    }

    /*!\brief show the button for selecting a predefined operation */
    function show_button()
    {

        $select=new ISelect();
        $value=$this->db->make_array("select od_id,od_name from op_predef ".
                                     " where jrn_def_id=".$this->p_jrn.
                                     " and od_direct ='".$this->od_direct."'".
                                     " order by od_name");

        if ( empty($value)==true) return "";
        $select->value=$value;
        $r=$select->input("pre_def");

        return $r;
    }
    /*!\brief count the number of pred operation for a ledger */
    function count()
    {
        $a=$this->db->count_sql("select od_id,od_name from op_predef ".
                                " where jrn_def_id=".$this->p_jrn.
                                " and od_direct ='".$this->od_direct."'".
                                " order by od_name");
        return $a;
    }
    /*!\brief get the list of the predef. operation of a ledger
     * \return string
     */
    function get_list_ledger()
    {
        $sql="select od_id,od_name,od_description from op_predef ".
             " where jrn_def_id=".$this->p_jrn.
             " and od_direct ='".$this->od_direct."'".
             " order by od_name";
        $res=$this->db->exec_sql($sql);
        $all=Database::fetch_all($res);
        return $all;
    }
    /*!\brief set the ledger
     * \param $p_jrn is the ledger (jrn_id)
     */
    function set_jrn($p_jrn)
    {
        $this->p_jrn=$p_jrn;
    }
   
    /**
     * 
     * @brief display the detail of predefined operation, normally everything 
     * is loaded
     */
    function display() 
    {
        $array=$this->detail->compute_array();
        echo $this->detail->display($array);
    }
}

/*!\brief mother of the pre_op_XXX, it contains only one data : an
 * object Pre_Operation. The child class contains an array of
 * Pre_Operation object
 */
class Pre_operation_detail
{
    var $operation;
    function __construct($p_cn,$p_id=0)
    {
        $this->db=$p_cn;
        $this->operation=new Pre_operation($this->db);
        $this->valid=array('ledger'=>'jrn_def_id','ledger_type'=>'jrn_type','direct'=>'od_direct');
		$this->jrn_def_id=-1;
    }


    /*!\brief show a form to use pre_op
     */
    function form_get ($p_url)
    {
        $r=HtmlInput::button_action(_("Modèle d'opérations"), ' $(\'modele_op_div\').style.display=\'block\';$(\'lk_modele_op_tab\').focus();');
        $r.='<div id="modele_op_div" class="noprint">';
        $r.=HtmlInput::title_box(_("Modèle d'opérations"), 'modele_op_div', 'hide');
        $hid=new IHidden();
        $r.=$hid->input("action","use_opd");
        $r.=$hid->input("jrn_type",$this->get("ledger_type"));
        $r.= $this->show_button($p_url);
        $r.='</div>';
        return $r;

    }
    /*!\brief count the number of pred operation for a ledger */
    function count()
    {
        $a=$this->db->count_sql("select od_id,od_name from op_predef ".
                                " where jrn_def_id=".$this->jrn_def_id.
                                " and od_direct ='".$this->od_direct."'".
                                " order by od_name");
        return $a;
    }
    /*!\brief show the button for selecting a predefined operation */
    function show_button($p_url)
    {
        
        
        $value=$this->db->get_array("select od_id,od_name,od_description from op_predef ".
                                     " where jrn_def_id=$1".
                                     " and od_direct =$2".
                                     " order by od_name",
                            array($this->jrn_def_id,$this->od_direct ));
        
        if ( $this->jrn_def_id=='') $value=array();
        
        $r="";
        $r.='<h2>'._("Choisissez un modèle").'</h2>';
        $r.=_('Filtrer').' '.HtmlInput::filter_table('modele_op_tab', '0', '0');
        $r.='<table style="width:100%" id="modele_op_tab">';
        for ($i=0;$i<count($value);$i++) {
            $r.='<tr class="'.(($i%2==0)?"even":"odd").'">';
            $r.='<td style="font-weight:bold;vertical-align:top;text-decoration:underline">';
            $r.=sprintf('<a href="%s&pre_def=%s" onclick="waiting_box()">%s</a> ',
                    $p_url,$value[$i]['od_id'],$value[$i]['od_name']);
            $r.='</td>';
            $r.='<td>'.h($value[$i]['od_description']).'</td>';
            $r.='</tr>';
        }
        $r.='</table>';
        return $r;
    }
    public function   get_operation()
    {
		if ( $this->jrn_def_id=='') return array();
        $value=$this->db->make_array("select od_id,od_name from op_predef ".
                                     " where jrn_def_id=".sql_string($this->jrn_def_id).
                                     " and od_direct ='".sql_string($this->od_direct)."'".
                                     " order by od_name",1);
        return $value;
    }
    function set($p_param,$value)
    {
        if ( ! isset ($this->valid[$p_param] ) )
        {
            $msg=_(" le parametre $p_param n'existe pas ".__FILE__.':'.__LINE__);
            throw new Exception($msg);
        }
        $attr=$this->valid[$p_param];
        $this->$attr=$value;
    }
    function get($p_param)
    {

        if ( ! isset ($this->valid[$p_param] ) )
        {
            $msg=_(" le parametre $p_param n'existe pas ".__FILE__.':'.__LINE__);
            throw new Exception($msg);
        }
        $attr=$this->valid[$p_param];
        return $this->$attr;
    }

    function get_post()
    {
        $this->operation->get_post();
    }

}
