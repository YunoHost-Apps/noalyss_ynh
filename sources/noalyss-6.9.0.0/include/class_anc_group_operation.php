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
 *  \brief group of object operations, used for misc operation
 */

/*! \brief group of object operations, used for misc operation
 *
 */
require_once NOALYSS_INCLUDE.'/class_idate.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_icheckbox.php';
require_once  NOALYSS_INCLUDE.'/class_anc_operation.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once  NOALYSS_INCLUDE.'/class_anc_plan.php';
require_once  NOALYSS_INCLUDE.'/class_dossier.php';

class Anc_Group_Operation
{
    var $db;	/*!< database connection */
    var  $id;	/*!< oa_group, a group contains
                          several rows of
                          operation_analytique linked by the
                          group id */

    var $a_operation;						/*!< array of operations */
    var $date;							/*!< date of the operations */
    var $pa_id;							/*!< the concerned pa_id */

    /*!\brief constructor */
    function  Anc_Group_Operation($p_cn,$p_id=0)
    {
        $this->db=$p_cn;
        $this->id=$p_id;
        $this->date=date("d.m.Y");
        $this->nMaxRow=10;
    }
    /*!\brief add several rows */
    function add()
    {

        $amount=0;
        try
        {
            $this->db->start();
            foreach ($this->a_operation as $row)
            {
                $add=round($row->oa_amount,2);
                $add=($row->oa_debit=='t')?$add:$add*(-1);
                $amount+=round($add,2);
                $row->add();
            }
            if ( $amount != 0 ) throw new Exception (_('Operation non equilibrée'));
        }
        catch (Exception $e)
        {
            echo $e->getTrace();
            $this->db->rollback();
            throw new Exception($e);
        }
        $this->db->commit();
    }
    /*!\brief show a form for the operation (several rows)
     * \return the string containing the form but without the form tag
     *
     */
    function form($p_readonly=0)
    {
        $wDate=new IDate("pdate",$this->date);
        $wDate->table=1;
        $wDate->size=10;
        $wDate->readonly=$p_readonly;

        $wDescription=new IText("pdesc");
        $wDescription->table=0;
        $wDescription->size=80;
        $wDescription->readonly=$p_readonly;
        // Show an existing operation
        //
        if ( isset ($this->a_operation[0]))
        {
            $wDate->value=$this->a_operation[0]->oa_date;
            $wDescription->value=$this->a_operation[0]->oa_description;
        }

        $ret="";

        $ret.='<table style="result"	>';

        $ret.="<TR>".$wDate->input()."</tr>";
        $ret.='<tr><td>Description</td>'.
              '<td colspan="3">'.
              $wDescription->input()."</td></tr>";
        $Plan=new Anc_Plan($this->db);
        $aPlan=$Plan->get_list();
        $max=(count($this->a_operation)<$this->nMaxRow)?$this->nMaxRow:count($this->a_operation);
        $ret.='</table><table  id="ago" style="width: 100%;">';
        /* show 10 rows */
        $ret.="<tr>";
        foreach ($aPlan as $d)
        {
            $idx=$d['id'];
            /* array of possible value for the select */
            $aPoste[$idx]=$this->db->make_array("select po_id as value,".
                                                " po_name||':'||coalesce(po_description,'-') as label ".
                                                " from poste_analytique ".
                                                " where pa_id = ".$idx.
                                                " order by po_name ");

            $ret.="<th> Poste </th>";
        }
        $ret.="<th></th>".
              "<th> Montant</th>".
              "<th>D&eacute;bit</th>".
              "</tr>";

        for ($i = 0;$i < $max;$i++)
        {
            $ret.="<tr>";

            foreach ($aPlan as $d)
            {
                $idx=$d['id'];
                // init variable
                $wSelect=new ISelect("pop".$i."plan".$idx);
                $wSelect->value=$aPoste[$idx];
                $wSelect->size=12;

                $wSelect->readOnly=$p_readonly;
                if ( isset($this->a_operation[$i]))
                {
                    $wSelect->selected=$this->a_operation[$i]->po_id;
                }
                $ret.=td($wSelect->input());
            }
            $wAmount=new INum("pamount$i",0.0);
            $wAmount->size=12;
            $wAmount->table=1;
            $wAmount->javascript=" onChange=format_number(this);caod_checkTotal()";
            $wAmount->readOnly=$p_readonly;

            $wDebit=new ICheckBox("pdeb$i");
            $wDebit->readOnly=$p_readonly;
            $wDebit->javascript=" onChange=caod_checkTotal()";
            if ( isset ($this->a_operation[$i]))
            {
                $wSelect->selected=$this->a_operation[$i]->po_id;
                $wAmount->value=$this->a_operation[$i]->oa_amount;
                $wDebit->value=$this->a_operation[$i]->oa_debit;
                if ( $wDebit->value=='t')
                {
                    $wDebit->selected=true;
                }

            }

            // build the table

            $ret.="<TD></TD>";
            $ret.=$wAmount->input();
            $ret.=td($wDebit->input());

            $ret.="</tr>";
        }
        $ret.="</table>";
        if ( $p_readonly==false)
        {
            $add_row=new IButton('Ajouter');
            $add_row->label=_('Ajouter une ligne');
            $add_row->javascript='anc_add_row(\'ago\');';
            $ret.=HtmlInput::hidden('nbrow',$max);

            $ret.=$add_row->input();
        }
        return $ret;
    }
    /*!\brief fill row from $_POST data
     *
     */
    function get_from_array($p_array)
    {
        $Plan=new Anc_Plan($this->db);
        $aPlan=$Plan->get_list();


        for ( $i = 0;$i <$p_array['nbrow'];$i++)
        {
            foreach ($aPlan as $d)
            {
                $idx=$d['id'];
                $p=new Anc_Operation($this->db);
                $p->oa_amount=$p_array["pamount$i"];

                $p->oa_description=$p_array["pdesc"];
                $p->oa_date=$p_array['pdate'];
                $p->j_id=0;
                $p->oa_debit=(isset ($p_array["pdeb$i"]))?'t':'f';
                $p->oa_group=0;

                $p->po_id=$p_array["pop$i"."plan".$idx];
                $p->pa_id=$idx;
                $this->a_operation[]=clone $p;
            }
        }
    }
    /*!\brief save the group of operation but only if the amount is
       balanced  */
    function save()
    {
        $this->db->start();
        try
        {
            $oa_group=$this->db->get_next_seq('s_oa_group');
            for ($i=0;$i<count($this->a_operation);$i++)
            {
                $this->a_operation[$i]->oa_group=$oa_group;
                $this->a_operation[$i]->add();
            }
        }
        catch (Exception $ex)
        {
            echo '<span class="error">'.
            'Erreur dans l\'enregistrement '.
            __FILE__.':'.__LINE__.' '.
            $ex->getMessage();
            $p_cn->rollback();
            throw new Exception("Erreur ".$ex->getMessage());

        }
        $this->db->commit();
    }
    /*!\brief show the form */
    function show()
    {
        return $this->form(1);
    }
    static function test_me()
    {
        $dossier=dossier::id();
        $cn=new Database($dossier);

        if ( isset($_POST['go']))
        {
            $b=new Anc_Group_Operation($cn);
            $b->get_from_array($_POST);
            return;
        }

        $a=new Anc_Group_Operation($cn);
        echo '<form method="post">';
        echo $a->form();
        echo dossier::hidden();
        echo '<input type="submit" name="go">';
        echo '</form>';

    }

}
