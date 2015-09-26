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
 * \brief Concerns the Analytic plan (table plan_analytique)
 */

/*! \brief
 *  Concerns the Analytic plan (table plan_analytique)
 */
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/constant.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_anc_account.php';
require_once  NOALYSS_INCLUDE.'/class_dossier.php';

class Anc_Plan
{
    var $db; /*!<database connection */
    var $name; 					/*!< name plan_analytique.pa_name */
    var $description;				/*!< description of the PA plan_analytique.pa_description*/
    var $id;						/*!< id = plan_analytique.pa_id */

    function Anc_Plan($p_cn,$p_id=0)
    {
        $this->db=$p_cn;
        $this->id=$p_id;
        $this->name="";
        $this->description="";
        $this->get();
    }
    /*!\brief get the list of all existing PA
     * \return an array of PA (not object)
     *
     */
    function get_list($p_order=" order by pa_name")
    {
        $array=array();
        $sql="select pa_id as id,pa_name as name,".
             "pa_description as description from plan_analytique $p_order";
        $ret=$this->db->exec_sql($sql);
        $array=Database::fetch_all($ret);
        return $array;
    }

    function get()
    {
        if ( $this->id==0) return;

        $sql="select pa_name,pa_description from plan_analytique where pa_id=".$this->id;
        $ret= $this->db->exec_sql($sql);
        if ( Database::num_row($ret) == 0)
        {
            return;
        }
        $a=  Database::fetch_array($ret,0);
        $this->name=$a['pa_name'];
        $this->description=$a['pa_description'];

    }

    function delete()
    {
        if ( $this->id == 0 ) return;
        $this->db->exec_sql("delete from plan_analytique where pa_id=".$this->id);
    }

    function update()
    {
        if ( $this->id==0) return;
        $name=sql_string($this->name);
        if ( strlen($name) == 0)
            return;

        $description=sql_string($this->description);
        $this->db->exec_sql("update plan_analytique set pa_name=$1,
                            pa_description=$2 where pa_id=$3",array($name,$description,$this->id));
    }

    function add()
    {
        $name=sql_string($this->name);
        if ( strlen($name) == 0)
            return;
        if ( $this->isAppend() == false) return;
        $description=sql_string($this->description);
        $this->db->exec_sql("insert into plan_analytique(pa_name,pa_description)".
                            " values (".
                            "'".$name."',".
                            "'".$description."')");
        $this->id=$this->db->get_current_seq('plan_analytique_pa_id_seq');

    }
    function form()
    {

        $wName=new IText('pa_name',$this->name);

        $wName->table=1;
        $wDescription=new IText('pa_description',$this->description);
        $wDescription->table=1;
        $wId=new IHidden("pa_id",$this->id);
        $ret="<TABLE>";
        $ret.='<tr>'.td(_('Nom')).$wName->input().'</tr>';
        $ret.="<tr>".td(_('Description')).$wDescription->input()."</tr>";
        $ret.="</table>";
        $ret.=$wId->input();
        return $ret;
    }
    function isAppend()
    {
        $count=$this->db->get_value("select count(pa_id) from plan_analytique");

        if ( $count > 10 )
            return false;
        else
            return true;
    }
    /*!\brief get all the poste related to the current
     *        Analytic plan
     * \return an array of Poste_analytic object
     */
    function get_poste_analytique($p_order="")
    {
        $sql="select po_id,po_name from poste_analytique where pa_id=".$this->id." $p_order";
        $r=$this->db->exec_sql($sql);
        $ret=array();
        if ( Database::num_row($r) == 0 )
            return $ret;

        $all=Database::fetch_all($r);
        foreach ($all as $line)
        {
            $obj=new Anc_Account($this->db,$line['po_id']);
            $obj->get_by_id();
            $ret[]=clone $obj;
        }
        return $ret;
    }
    /*!\brief show the header for a table for PA
     * \return string like <th>name</th>...
     */
    function header()
    {
        $res="";
        $a_plan=$this->get_list(" order by pa_id");
        if ( empty($a_plan)) return "";
        foreach ($a_plan as $r_plan)
        {
            $res.="<th>".h($r_plan['name'])."</th>";
        }
        return $res;
    }
    function count()
    {
        $a=$this->db->count_sql("select pa_id from plan_analytique");
        return $a;
    }
    function exist()
    {
        $a=$this->db->count_sql("select pa_id from plan_analytique where pa_id=".
                                Database::escape_string($this->pa_id));

        return ($a==0)?false:true;

    }
    /**
    *@brief return an HTML string containing hidden input type to
    * hold the differant PA_ID
    *@param $p_array contains a array, it is the result of the fct
    * Anc_Plan::get_list
    *@return html string
    *@see Anc_Plan::get_list
    */
    static function hidden($p_array)
    {
        $r='';
        for ($i_anc=0;$i_anc <count($p_array);$i_anc++)
        {
            $r.=HtmlInput::hidden('pa_id[]',$p_array[$i_anc]['id']);
        }
        return $r;
    }
    static function test_me()
    {
        $cn=new Database(dossier::id());
        echo "<h1>Plan analytique : test</h1>";
        echo "clean";
        $cn->exec_sql("delete from plan_analytique");

        $p=new Anc_Plan($cn);
        echo "<h2>Add</h2>";
        $p->name="Nouveau 1";
        $p->description="C'est un test";
        echo "Add<hr>";
        $p->add();
        $p->name="Nouveau 2";
        $p->add();
        $pa_id=$p->id;
        echo $p->id."/";
        $p->name="Nouveau 3";
        $p->add();
        echo $p->id."/";


        $p->name="Nouveau 4";
        $p->add();
        echo $p->id;

        echo "<h2>get</h2>";
        $p->get();
        var_dump($p);
        echo "<h2>Update</h2> ";
        $p->name="Update ";
        $p->description="c'est change";
        $p->update();
        $p->get();
        var_dump($p);
        echo "<h2>get_list</h2>";
        $a=$p->get_list();
        var_dump($a);
        echo "<h2>delete </h2>";
        $p->delete();


    }
}

?>
