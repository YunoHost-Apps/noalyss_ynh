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
 * \brief Create, view, modify and parse report
 */

require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_ibutton.php';
require_once NOALYSS_INCLUDE.'/class_acc_report_row.php';
require_once NOALYSS_INCLUDE.'/class_impress.php';

/*!
 * \brief Class rapport  Create, view, modify and parse report
 */

class Acc_Report
{

    var $db;    /*!< $db database connx */
    var $id;    /*!< $id formdef.fr_id */
    var $name;  /*!< $name report's name */
    var $aAcc_Report_row;		/*!< array of rapport_row */
    var $nb;
    /*!\brief  Constructor */
    function __construct($p_cn,$p_id=0)
    {
        $this->db=$p_cn;
        $this->id=$p_id;
        $this->name='Nouveau';
        $this->aAcc_Report_row=null;
    }
    /*!\brief Return the report's name
     */
    function get_name()
    {
        $ret=$this->db->exec_sql("select fr_label from formdef where fr_id=".$this->id);
        if (Database::num_row($ret) == 0) return $this->name;
        $a=Database::fetch_array($ret,0);
        $this->name=$a['fr_label'];
        return $this->name;
    }
    /*!\brief return all the row and parse formula
     *        from a report
     * \param $p_start start periode
     * \param $p_end end periode
     * \param $p_type_date type of the date : periode or calendar
     */
    function get_row($p_start,$p_end,$p_type_date)
    {

        $Res=$this->db->exec_sql("select fo_id ,
                                 fo_fr_id,
                                 fo_pos,
                                 fo_label,
                                 fo_formula,
                                 fr_label from form
                                 inner join formdef on fr_id=fo_fr_id
                                 where fr_id =".$this->id.
                                 "order by fo_pos");
        $Max=Database::num_row($Res);
        if ($Max==0)
        {
            $this->row=0;
            return null;
        }
        $col=array();
        for ($i=0;$i<$Max;$i++)
        {
            $l_line=Database::fetch_array($Res,$i);
            $col[]=Impress::parse_formula($this->db,
                                $l_line['fo_label'],
                                $l_line['fo_formula'],
                                $p_start,
                                $p_end,
                                true,
                                $p_type_date
                               );

        } //for ($i
        $this->row=$col;
        return $col;
    }
    /*!
     * \brief  Display a form for encoding a new report or update one
     *
     * \param $p_line number of line
     *
     */
    function form($p_line=0)
    {

        $r="";
        if ($p_line == 0 ) $p_line=count($this->aAcc_Report_row);
        $r.= dossier::hidden();
        $r.= HtmlInput::hidden('line',$p_line);
        $r.= HtmlInput::hidden('fr_id',$this->id);
        $wForm=new IText();
        $r.="Nom du rapport : ";
        $r.=$wForm->input('form_nom',$this->name);

        $r.= '<TABLE id="rap1" width="100%">';
        $r.= "<TR>";
        $r.= "<TH> Position </TH>";
        $r.= "<TH> Texte </TH>";
        $r.= "<TH> Formule</TH>";

        $r.= '</TR>';
        $wName=new IText();
        $wName->size=40;
        $wPos=new IText();
        $wPos->size=3;
        $wForm=new IText();
        $wForm->size=35;
        for ( $i =0 ; $i < $p_line;$i++)
        {

            $r.= "<TR>";

            $r.= "<TD>";
            $wPos->value=( isset($this->aAcc_Report_row[$i]->fo_pos))?$this->aAcc_Report_row[$i]->fo_pos:$i+1;
            $r.=$wPos->input("pos".$i);
            $r.= '</TD>';


            $r.= "<TD>";
            $wName->value=( isset($this->aAcc_Report_row[$i]->fo_label))?$this->aAcc_Report_row[$i]->fo_label:"";
            $r.=$wName->input("text".$i);
            $r.= '</TD>';

            $r.='<td>';
            $search=new IPoste("form".$i);
            $search->size=50;
            $search->value=( isset($this->aAcc_Report_row[$i]->fo_formula))?$this->aAcc_Report_row[$i]->fo_formula:"";
            $search->label=_("Recherche poste");
            $search->set_attribute('gDossier',dossier::id());
            $search->set_attribute('bracket',1);
            $search->set_attribute('no_overwrite',1);
            $search->set_attribute('noquery',1);
            $search->set_attribute('account',$search->name);
            $search->set_attribute('ipopup','ipop_card');

            $r.=$search->input();
            $r.='</td>';


            $r.= "</TR>";
        }

        $r.= "</TABLE>";
        $wButton=new IButton();
        $wButton->javascript=" rapport_add_row('".dossier::id()."')";
        $wButton->label="Ajout d'une ligne";
        $r.=$wButton->input();
        return $r;

    }
    /*!\brief save into form and form_def
     */
    function save()
    {

        if ( strlen(trim($this->name)) == 0 )
            return;
        if ( $this->id == 0 )
            $this->insert();
        else
            $this->update();

    }
    function insert()
    {
        try
        {
            $this->db->start();
            $ret_sql=$this->db->exec_sql(
                         "insert into formdef (fr_label) values($1) returning fr_id",
                         array($this->name)
                     );
            $this->id=Database::fetch_result($ret_sql,0,0);
            $ix=1;
            foreach ( $this->aAcc_Report_row as $row)
            {
                if ( strlen(trim($row->get_parameter("name"))) != 0 &&
                        strlen(trim($row->get_parameter("formula"))) != 0 )
                {
                    $ix=($row->get_parameter("position")!="")?$row->get_parameter("position"):$ix;
                    $row->set_parameter("position",$ix);
                    $ret_sql=$this->db->exec_sql(
                                 "insert into form (fo_fr_id,fo_pos,fo_label,fo_formula)".
                                 " values($1,$2,$3,$4)",
                                 array($this->id,
                                       $row->fo_pos,
                                       $row->fo_label,
                                       $row->fo_formula)
                             );
                }
            }

        }
        catch (Exception $e)
        {
            $this->db->rollback();
            echo $e->getMessage();
        }
        $this->db->commit();

    }
    function update()
    {
        try
        {
            $this->db->start();
            $ret_sql=$this->db->exec_sql(
                         "update formdef set fr_label=$1 where fr_id=$2",
                         array($this->name,$this->id));
            $ret_sql=$this->db->exec_sql(
                         "delete from form where fo_fr_id=$1",
                         array($this->id));
            $ix=0;

            foreach ( $this->aAcc_Report_row as $row)
            {
                if ( strlen(trim($row->get_parameter("name"))) != 0 &&
                        strlen(trim($row->get_parameter("formula"))) != 0 )
                {
                    $ix=($row->get_parameter("position")!="")?$row->get_parameter("position"):$ix;
                    $row->set_parameter("position",$ix);
                    $ret_sql=$this->db->exec_sql(
                                 "insert into form (fo_fr_id,fo_pos,fo_label,fo_formula)".
                                 " values($1,$2,$3,$4)",
                                 array($this->id,
                                       $row->fo_pos,
                                       $row->fo_label,
                                       $row->fo_formula)
                             );
                }
            }


        }
        catch (Exception $e)
        {
            $this->db->rollback();
            echo $e->getMessage();
        }
        $this->db->commit();
    }
    /*!\brief fill a form thanks an array, usually it is $_POST
     *\param $p_array keys = fr_id, form_nom,textXX, formXX, posXX where
        XX is an number
     */
    function from_array($p_array)
    {
        $this->id=(isset($p_array['fr_id']))?$p_array['fr_id']:0;
        $this->name=(isset($p_array['form_nom']))?$p_array['form_nom']:"";
        $ix=0;

        $rr=new Acc_Report_Row();
        $rr->set_parameter("form_id",$this->id);
        $rr->set_parameter('database',$this->db);

        $this->aAcc_Report_row=$rr->from_array($p_array);


    }
    /*!\brief the fr_id MUST be set before calling
     */


    function load()
    {
        $sql=$this->db->exec_sql(
                 "select fr_label from formdef where fr_id=$1",
                 array($this->id));
        if ( Database::num_row($sql) == 0 ) return;
        $this->name=Database::fetch_result($sql,0,0);
        $sql=$this->db->exec_sql(
                 "select fo_id,fo_pos,fo_label,fo_formula ".
                 " from form ".
                 " where fo_fr_id=$1 order by fo_pos",
                 array($this->id));
        $f=Database::fetch_all($sql);
        $array=array();
        if ( ! empty($f) )
        {
            foreach ($f as $r)
            {
                $obj=new Acc_Report_Row();
                $obj->set_parameter("name",$r['fo_label']);
                $obj->set_parameter("id",$r['fo_id']);
                $obj->set_parameter("position",$r['fo_pos']);
                $obj->set_parameter("formula",$r['fo_formula']);
                $obj->set_parameter('database',$this->db);
                $obj->set_parameter('form_id',$this->id);
                $array[]=clone $obj;
            }
        }
        $this->aAcc_Report_row=$array;

    }
    function delete()
    {
        $ret=$this->db->exec_sql(
                 "delete from formdef where fr_id=$1",
                 array($this->id)
             );
    }
    /*!\brief get a list from formdef of all defined form
     *
     *\return array of object rapport
     *
     */
    function get_list()
    {
        $sql="select fr_id,fr_label from formdef order by fr_label";
        $ret=$this->db->exec_sql($sql);
        if ( Database::num_row($ret) == 0 ) return array();
        $array=Database::fetch_all($ret);
        $obj=array();
        foreach ($array as $row)
        {
            $tmp=new Acc_Report($this->db);
            $tmp->id=$row['fr_id'];
            $tmp->name=$row['fr_label'];
            $obj[]=clone $tmp;
        }
        return $obj;
    }
    /*!\brief To make a SELECT button with the needed value, it is used
     *by the SELECT widget
     *\return string with html code
     */
    function make_array()
    {
        $sql=$this->db->make_array("select fr_id,fr_label from formdef order by fr_label");
        return $sql;
    }


    /*!\brief write to a file the definition of a report
     * \param p_file is the file name (default php://output)
     */
    function export_csv($p_file)
    {
        $this->load();

        fputcsv($p_file,array($this->name));

        foreach ($this->aAcc_Report_row as $row)
        {
            fputcsv($p_file,array($row->get_parameter("name"),
                                  $row->get_parameter('position'),
                                  $row->get_parameter('formula'))
                   );
        }

    }
    /*!\brief upload a definition of a report and insert it into the
     * database
     */
    function upload()
    {
        if ( empty ($_FILES) ) return;
        if ( strlen(trim($_FILES['report']['tmp_name'])) == 0 )
        {
            alert("Nom de fichier est vide");
            return;
        }
        $file_report=tempnam('tmp','file_report');
        if (  move_uploaded_file($_FILES['report']['tmp_name'],$file_report))
        {
            // File is uploaded now we can try to parse it
            $file=fopen($file_report,'r');
            $data=fgetcsv($file);
            if ( empty($data) ) return;
            $this->name=$data[0];
            $array=array();
            while($data=fgetcsv($file))
            {
                $obj=new Acc_Report_Row();
                $obj->set_parameter("name",$data[0]);
                $obj->set_parameter("id",0);
                $obj->set_parameter("position",$data[1]);
                $obj->set_parameter("formula",$data[2]);
                $obj->set_parameter('database',$this->db);
                $obj->set_parameter('form_id',0);
                $array[]=clone $obj;
            }
            $this->aAcc_Report_row=$array;
            $this->insert();
        }
    }
    /**
     *@brief check if a report exist
     *@param $p_id, optional, if given check the report with this fr_id
     *@return return true if the report exist otherwise false
     */
    function exist($p_id=0)
    {
        $c=$this->id;
        if ( $p_id != 0 ) $c=$p_id;
        $ret=$this->db->exec_sql("select fr_label from formdef where fr_id=$1",array($c));
        if (Database::num_row($ret) == 0) return false;
        return true;
    }
    static function test_me()
    {
        $cn=new Database(dossier::id());
        $a=new Acc_Report($cn);
        print_r($a->get_list());
        $array=array("text0"=>"test1",
                     "form0"=>"7%",
                     "text1"=>"test2",
                     "form1"=>"6%",
                     "fr_id"=>110,
                     "form_nom"=>"Tableau"
                    );
        $a->from_array($array);
        print_r($a);
        echo '<form method="post">';
        echo $a->form(10);

        echo HtmlInput::submit('update','Enregistre');
        /* Add a line should be a javascript see comptanalytic */
        //  $r.= '<INPUT TYPE="submit" value="Ajoute une ligne" name="add_line">';
        echo HtmlInput::submit('del_form','Efface ce rapport');
        echo HtmlInput::hidden('test_select',$_REQUEST['test_select']);
        echo "</FORM>";
        if ( isset ($_POST['update']))
        {
            $b=new Acc_Report($cn);
            $b->from_array($_POST);
            echo '<hr>';
            print_r($b);
        }
    }
}

?>
