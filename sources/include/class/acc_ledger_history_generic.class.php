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
// Copyright (2018) Author Dany De Bontridder <dany@alchimerys.be>

/**
 * @file
 * @brief class for Ledger's history, list of operation for MISC, FIN, ACH and VEN
 * 
 */

/**
 * @brief manage the list of operation when we need several ledger with a different
 * type or from Misceleaneous ledger 
 * @include acc_ledger_historyTest.php
 */
class Acc_Ledger_History_Generic extends Acc_Ledger_History
{

    private $data; //!< array of rows

    /**
     * Constructor
     * @param Database $cn
     * @param type $pa_ledger
     * @param type $p_from
     * @param type $p_to
     * @param type $p_mode
     * @example acc_ledger_historyTest.php
     */

    function __construct(Database $cn, $pa_ledger, $p_from, $p_to, $p_mode)
    {
        parent::__construct($cn, $pa_ledger, $p_from, $p_to, $p_mode);
        $this->data=[];
    }

    /**
     * @brief get_detail gives the detail of row
     * this array must contains at least the field
     *       <ul>
     *       <li> montant</li>
     *       <li> grpt_id
     *       </ul>
     * the following field will be added
     *       <ul>
     *       <li> HTVA
     *       <li> TVAC
     *       <li> TVA array with
     *          <ul>
     *          <li> field 0 idx
     *          <li> array containing tva_id,tva_label and tva_amount
     *          </ul>
     *       </ul>
     *
     * @paramp_array the structure is set in get_rowSimple, this array is
     *        modified,
      @verbatim
      jrn.jr_id as jr_id ,
      jrn.jr_id as num ,
      jrn.jr_def_id as jr_def_id,
      jrn.jr_montant as montant,
      substr(jrn.jr_comment,1,35) as comment,
      to_char(jrn.jr_date,'DD-MM-YYYY') as date,
      to_char(jrn.jr_date_paid,'DD-MM-YYYY') as date_paid,
      jr_pj_number,
      jr_internal,
      jrn.jr_grpt_id as grpt_id,
      jrn.jr_pj_name as pj,
      jrn_def_type,
      jrn.jr_tech_per
      @endverbatim
     * 
     * @param $trunc if the data must be truncated, usefull for pdf export
     * @paramp_jrn_type is the type of the ledger (ACH or VEN)
     * @param $a_TVA TVA Array (default null)
     * @param $a_ParmCode Array (default null)
     * 
     * @todo useless since only 2 modes are supported : oneline and extended (accounting writing) but
     * can be used for ledger before 2007
     * \return p_array
     */
    function get_detail(&$p_array, $p_jrn_type, $trunc=0, $a_TVA=null,
            $a_ParmCode=null)
    {
        bcscale(2);

        if ($a_TVA==null)
        {
            //Load TVA array
            $a_TVA=$this->db->get_array('select tva_id,tva_label,tva_poste
                                        from tva_rate where tva_rate != 0 order by tva_rate,tva_label,tva_id');
        }
        if ($a_ParmCode==null)
        {
            //Load Parm_code
            $a_ParmCode=$this->db->get_array('select p_code,p_value from parm_code');
        }
        // init
        $p_array['client']="";
        $p_array['TVAC']=0;
        $p_array['HTVA']=0;
        $p_array['TVA']=array();
        $p_array['AMOUNT_TVA']=0.0;
        $p_array['dep_priv']=0;
        $p_array['dna']=0;
        $p_array['tva_dna']=0;
        $p_array['tva_np']=0;
        $dep_priv=0.0;


        // if using the QUANT_* tables then get there the needed info
        //
        if ($this->use_quant_table($p_array['grpt_id'], $p_array['jrn_def_type'])
                ==TRUE)
        {
            // Initialize amount for VAT
            $nb_tva=count($a_TVA);
            for ($i=0; $i<$nb_tva; $i++)
            {
                $p_array['TVA'][$i]=array($i,
                    array(
                        $a_TVA[$i]['tva_id'],
                        $a_TVA[$i]['tva_label'],
                        0)
                );
            }
            switch ($p_array['jrn_def_type'])
            {
                case "ACH":
                    $sql="select 
                            sum(coalesce(qp_price,0)) as htva,
                            sum(coalesce(qp_vat)) as  vat,
                            sum(coalesce(qp_nd_tva)) as  nd_tva,
                            sum(coalesce(qp_nd_tva_recup)) as  nd_tva_recup,
                            sum(coalesce(qp_dep_priv)) as dep_priv,
                            qp_vat_code as tva_code,
                            qp_supplier as fiche_id,
                            qp_vat_sided as tva_sided
                        from 
                            quant_purchase 
                        where 
                            qp_internal=$1 
                            group by qp_supplier,qp_vat_code,qp_vat_sided ";
                    break;
                case "VEN":
                    $sql="select 
                            sum(coalesce(qs_price,0)) as htva,
                            sum(coalesce(qs_vat)) as  vat,
                            sum(0) as  nd_tva,
                            sum(0) as  nd_tva_recup,
                            sum(0) as dep_priv,
                            qs_vat_code as tva_code,
                            qs_client as fiche_id,
                            qs_vat_sided as tva_sided
                        from 
                            quant_sold
                        where 
                            qs_internal=$1 
                            group by qs_client,qs_vat_code,qs_vat_sided ";
                    break;

                default:
                    break;
            }
            $a_detail=$this->db->get_array($sql, array($p_array['jr_internal']));
            $nb_detail=count($a_detail);
            for ($x=0; $x<$nb_detail; $x++)
            {
                $p_array['HTVA']=bcadd($p_array['HTVA'], $a_detail[$x]['htva']);
                $p_array['tva_dna']=bcadd($p_array['tva_dna'],
                        $a_detail[$x]['nd_tva_recup']);
                $p_array['tva_dna']=bcadd($p_array['tva_dna'],
                        $a_detail[$x]['nd_tva']);
                $p_array['TVAC']=bcadd($p_array['TVAC'], $a_detail[$x]['htva']);
                if ($a_detail[$x]['tva_sided']==0)
                    $p_array['TVAC']=bcadd($p_array['TVAC'],
                            $a_detail[$x]['vat']);
                $p_array['TVAC']=bcadd($p_array['TVAC'], $a_detail[$x]['nd_tva']);
                $p_array['TVAC']=bcadd($p_array['TVAC'],
                        $a_detail[$x]['nd_tva_recup']);
                $p_array['dep_priv']=bcadd($p_array['dep_priv'],
                        $a_detail[$x]['dep_priv']);
                $xdx=$a_detail[$x]['tva_code'];
                // $p_array['TVA'][$xdx]=bcadd($p_array['TVA'][$xdx],$a_detail[$x]['vat']);
                //--- Put VAT in the right place in the array $a_TVA
                $nb_tva=count($a_TVA);
                for ($j=0; $j<$nb_tva; $j++)
                {
                    if ($xdx==$p_array['TVA'][$j][1][0])
                    {
                        $p_array['TVA'][$j][1][2]=bcadd($p_array['TVA'][$j][1][2],
                                $a_detail[$x]['vat']);
                    }
                }
            }
            $fiche=new Fiche($this->db, $a_detail[0]['fiche_id']);
            $p_array['client']=($trunc==0)?$fiche->getName():mb_substr($fiche->getName(),
                            0, 20);
            return $p_array;
        }
        //
        // Retrieve data from jrnx
        // Order is important for TVA autoreversed
        $sql="select j_id,j_poste,j_montant, j_debit,j_qcode from jrnx where ".
                " j_grpt=$1 order by 1 desc";
        $Res2=$this->db->exec_sql($sql, array($p_array['grpt_id']));
        $data_jrnx=Database::fetch_all($Res2);
        $c=0;

        // Parse data from jrnx and fill diff. field
        foreach ($data_jrnx as $code)
        {
            $idx_tva=0;
            $poste=new Acc_Account_Ledger($this->db, $code['j_poste']);

            // if card retrieve name if the account is not a VAT account
            if (strlen(trim($code['j_qcode']))!=0&&$poste->isTva()==0)
            {
                $fiche=new Fiche($this->db);
                $fiche->get_by_qcode(trim($code['j_qcode']), false);
                $fiche_def_id=$fiche->get_fiche_def_ref_id();
                // Customer or supplier
                if ($fiche_def_id==FICHE_TYPE_CLIENT||
                        $fiche_def_id==FICHE_TYPE_FOURNISSEUR||$fiche_def_id==FICHE_TYPE_ADM_TAX)
                {
                    $p_array['TVAC']=$code['j_montant'];

                    $p_array['client']=($trunc==0)?$fiche->getName():mb_substr($fiche->getName(),
                                    0, 20);
                    $p_array['reversed']=false;
                    if ($fiche_def_id==FICHE_TYPE_CLIENT&&$code['j_debit']=='f')
                    {
                        $p_array['reversed']=true;
                        $p_array['TVAC']*=-1;
                    }
                    if ($fiche_def_id==FICHE_TYPE_ADM_TAX&&$code['j_debit']=='f')
                    {
                        $p_array['reversed']=true;
                        $p_array['TVAC']*=-1;
                    }
                    if ($fiche_def_id==FICHE_TYPE_FOURNISSEUR&&$code['j_debit']=='t')
                    {
                        $p_array['reversed']=true;
                        $p_array['TVAC']*=-1;
                    }
                }
                else
                {
                    // if we use the ledger ven / ach for others card than supplier and customer
                    if ($fiche_def_id!=FICHE_TYPE_VENTE&&
                            $fiche_def_id!=FICHE_TYPE_ACH_MAR&&
                            $fiche_def_id!=FICHE_TYPE_ACH_SER&&
                            $fiche_def_id!=FICHE_TYPE_ACH_MAT
                    )
                    {
                        $p_array['TVAC']=$code['j_montant'];

                        $p_array['client']=($trunc==0)?$fiche->getName():mb_substr($fiche->getName(),
                                        0, 20);
                        $p_array['reversed']=false;
                        if ($p_jrn_type=='ACH'&&$code['j_debit']=='t')
                        {
                            $p_array['reversed']=true;
                            $p_array['TVAC']*=-1;
                        }
                        if ($p_jrn_type=='VEN'&&$code['j_debit']=='f')
                        {
                            $p_array['reversed']=true;
                            $p_array['TVAC']*=-1;
                        }
                    }
                }
            }
            // if TVA, load amount, tva id and rate in array
            foreach ($a_TVA as $line_tva)
            {
                list($tva_deb, $tva_cred)=explode(',', $line_tva['tva_poste']);
                if ($code['j_poste']==$tva_deb||
                        $code['j_poste']==$tva_cred)
                {

                    // For the reversed operation
                    if ($p_jrn_type=='ACH'&&$code['j_debit']=='f')
                    {
                        $code['j_montant']=-1*$code['j_montant'];
                    }
                    if ($p_jrn_type=='VEN'&&$code['j_debit']=='t')
                    {
                        $code['j_montant']=-1*$code['j_montant'];
                    }

                    $p_array['AMOUNT_TVA']+=$code['j_montant'];

                    $p_array['TVA'][$c]=array($idx_tva, array($line_tva['tva_id'],
                            $line_tva['tva_label'], $code['j_montant']));
                    $c++;

                    $idx_tva++;
                }
            }

            // isDNA
            // If operation is reversed then  amount are negatif
            /* if ND */
            if ($p_array['jrn_def_type']=='ACH')
            {
                $purchase=new Gestion_Purchase($this->db);
                $purchase->search_by_jid($code['j_id']);
                $purchase->load();
                $dep_priv+=$purchase->qp_dep_priv;
                $p_array['dep_priv']=$dep_priv;
                $p_array['dna']=bcadd($p_array['dna'], $purchase->qp_nd_amount);
                $p_array['tva_dna']=bcadd($p_array['tva_dna'],
                        bcadd($purchase->qp_nd_tva, $purchase->qp_nd_tva_recup));
                $p_array['tva_np']=bcadd($purchase->qp_vat_sided,
                        $p_array['tva_np']);
            }
            if ($p_array['jrn_def_type']=='VEN')
            {
                $sold=new gestion_sold($this->db);
                $sold->search_by_jid($code['j_id']);
                $sold->load();
                $p_array['tva_np']=bcadd($sold->qs_vat_sided, $p_array['tva_np']);
            }
        }
        $p_array['TVAC']=sprintf('% 10.2f', $p_array['TVAC']);
        $p_array['HTVA']=sprintf('% 10.2f',
                $p_array['TVAC']-$p_array['AMOUNT_TVA']-$p_array['tva_dna']);
        $r="";
        $a_tva_amount=array();
        // inline TVA (used for the PDF)
        foreach ($p_array['TVA'] as $linetva)
        {
            foreach ($a_TVA as $tva)
            {
                if ($tva['tva_id']==$linetva[1][0])
                {
                    $a=$tva['tva_id'];
                    $a_tva_amount[$a]=$linetva[1][2];
                }
            }
        }
        foreach ($a_TVA as $line_tva)
        {
            $a=$line_tva['tva_id'];
            if (isset($a_tva_amount[$a]))
            {
                $tmp=sprintf("% 10.2f", $a_tva_amount[$a]);
                $r.="$tmp";
            }
            else
                $r.=sprintf("% 10.2f", 0);
        }
        $p_array['TVA_INLINE']=$r;

        return $p_array;
    }

    /**
     * @brief depending on the mode will call the right function
     *  - export_oneline_html for one line (mode=L)
     *  - export_accounting_html for accounting (mode=A,D,E)
     */
    function export_html()
    {
        switch ($this->m_mode)
        {
            case "E":
                $this->export_accounting_html();
                break;
            case "D":
                $this->export_accounting_html();
                break;
            case "L":
                $this->export_oneline_html();
                break;
            case "A":
                $this->export_accounting_html();
                break;
            default:
                break;
        }
    }

    /**
     * @brief  Get simplified row from ledger
     *
     * @param p_from periode
     * @param p_to periode
     * @param p_limit starting line
     * @param p_offset number of lines
     * @param trunc if data must be truncated (pdf export)
     * @todo  Prévoir aussi les journaux sans tables quant < 2007
     * @return numbe of rows found
     */
    function get_rowSimple($trunc=0, $p_limit=-1, $p_offset=-1)
    {
        global $g_user;
        $jrn=" jrn_def_id in (".join($this->ma_ledger, ",").")";

        $periode=sql_filter_per($this->db, $this->m_from, $this->m_to, 'p_id',
                'jr_tech_per');

        $cond_limite=($p_limit!=-1)?" limit ".$p_limit." offset ".$p_offset:"";
        //---
        $sql="
             SELECT jrn.jr_id as jr_id ,
             jrn.jr_id as num ,
             jrn.jr_def_id as jr_def_id,
             jrn.jr_montant as montant,
             substr(jrn.jr_comment,1,35) as comment,
             to_char(jrn.jr_date,'DD-MM-YYYY') as date,
             to_char(jrn.jr_date_paid,'DD-MM-YYYY') as date_paid,
             jr_pj_number,
             jr_internal,
             jrn.jr_grpt_id as grpt_id,
             jrn.jr_pj_name as pj,
             jrn_def_type,
             jrn.jr_tech_per
             FROM jrn join jrn_def on (jrn_def_id=jr_def_id)
             WHERE $periode and $jrn order by jr_date,substring(jrn.jr_pj_number,'[0-9]+$')::numeric asc  $cond_limite";
        $Res=$this->db->exec_sql($sql);
        $Max=Database::num_row($Res);
        if ($Max==0)
        {
            return 0;
        }
//        @todo Pas nécessaire puisqu'on ne traite que les journaux d'opération diverses, 
//        Il faudrait p-e prévoir un système pour les journaux avant 2007 qui n'utilisaient
//        pas les tables quant
//        
//        $type=$this->get_type();
//        // for type ACH and Ven we take more info
//        if ($type=='ACH'||$type=='VEN')
//        {
//            $a_ParmCode=$this->db->get_array('select p_code,p_value from parm_code');
//            $a_TVA=$this->db->get_array('select tva_id,tva_label,tva_poste
//                                        from tva_rate where tva_rate != 0 order by tva_rate,tva_label,tva_id ');
//            for ($i=0; $i<$Max; $i++)
//            {
//                $array[$i]=Database::fetch_array($Res, $i);
//                $p=$this->get_detail($array[$i], $type, $trunc, $a_TVA,
//                        $a_ParmCode);
//                if ($array[$i]['dep_priv']!=0.0)
//                {
//                    $array[$i]['comment'].="(priv. ".$array[$i]['dep_priv'].")";
//                }
//            }
//        }
//        else
//        {
        $array=Database::fetch_all($Res);
//        }

        $this->data=$array;
        return $Max;
    }

    /**
     * @brief  set $this->data with the array of rows
     *
     *
     * @param p_limit starting line
     * @param p_offset number of lines
     * @returns nb of rows found
     *
     */
    function get_row($p_limit=-1, $p_offset=-1)
    {
        global $g_user;
        $periode=sql_filter_per($this->db, $this->m_from, $this->m_to, 'p_id',
                'jr_tech_per');

        $cond_limite=($p_limit!=-1)?" limit ".$p_limit." offset ".$p_offset:"";

        $ledger_list=join($this->ma_ledger, ",");
// Grand livre == 0
        $Res=$this->db->exec_sql("select jr_id,j_id,j_id as int_j_id,to_char(j_date,'DD.MM.YYYY') as j_date,
                                     jr_internal,
                                     case j_debit when 't' then j_montant::text else '   ' end as deb_montant,
                                     case j_debit when 'f' then j_montant::text else '   ' end as cred_montant,
                                     j_debit as debit,j_poste as poste,j_qcode,jr_montant , ".
                "case when j_text='' or j_text is null then pcm_lib else j_text end as description,j_grpt as grp,
                                     jr_comment||' ('||jr_internal||')'  as jr_comment,
				     jr_pj_number,
                                     j_qcode,
                                     jrn_def_type,
                                     jr_rapt as oc, j_tech_per as periode,
                                     j_id
                                     from jrnx left join jrn on 
                   jr_grpt_id=j_grpt 
                   left join tmp_pcmn on pcm_val=j_poste 
                   join jrn_def on (jrn_def_id=jr_def_id)
                    where j_jrn_def in (".$ledger_list.") 
                    and ".$periode."
                    order by j_date::date asc,substring(jr_pj_number,'[0-9]+$')::numeric asc,j_grpt,j_debit desc ".
                $cond_limite);

        $array=array();
        $Max=Database::num_row($Res);
        if ($Max==0)
            return 0;
        $case="";
        $tot_deb=0;
        $tot_cred=0;
        $row=Database::fetch_all($Res);
        bcscale(2);
        for ($i=0; $i<$Max; $i++)
        {

            $line=$row[$i];
            $tot_deb=bcadd($tot_deb, $line['deb_montant']);
            $tot_cred=bcadd($tot_cred, $line['cred_montant']);
            $tot_op=$line['jr_montant'];

            /* Check first if there is a quickcode */
            if (strlen(trim($line['description']))==0&&strlen(trim($line['j_qcode']))
                    !=0)
            {
                $fiche=new Fiche($this->db);
                if ($fiche->get_by_qcode($line['j_qcode'], false)==0)
                {
                    $line['description']=$fiche->strAttribut(ATTR_DEF_NAME);
                }
            }
            if ($case!=$line['grp'])
            {
                $case=$line['grp'];
                // for financial, we show if the amount is or not in negative
                if ($line['jrn_def_type']=='FIN')
                {
                    $amount=$this->db->get_value('select qf_amount from quant_fin where jr_id=$1',
                            array($line['jr_id']));
                    /*  if nothing is found */
                    if ($this->db->count()==0)
                        $tot_op=$line['jr_montant'];
                    else if ($amount<0)
                    {
                        $tot_op=$amount;
                    }
                }
                $array[]=array(
                    'jr_id'=>$line['jr_id'],
                    'int_j_id'=>$line['int_j_id'],
                    'j_id'=>$line['j_id'],
                    'j_date'=>$line['j_date'],
                    'internal'=>$line['jr_internal'],
                    'deb_montant'=>'',
                    'cred_montant'=>' ',
                    'description'=>'<b><i>'.h($line['jr_comment']).' ['.$tot_op.'] </i></b>',
                    'poste'=>$line['oc'],
                    'j_qcode'=>$line['j_qcode'],
                    'periode'=>$line['periode'],
                    'jr_pj_number'=>$line ['jr_pj_number'],
                    "ledger_type"=>$line['jrn_def_type']);

                $array[]=array(
                    'jr_id'=>'',
                    'int_j_id'=>$line['int_j_id'],
                    'j_id'=>'',
                    'j_date'=>'',
                    'internal'=>'',
                    'deb_montant'=>$line['deb_montant'],
                    'cred_montant'=>$line['cred_montant'],
                    'description'=>$line['description'],
                    'poste'=>$line['poste'],
                    'j_qcode'=>$line['j_qcode'],
                    'periode'=>$line['periode'],
                    'jr_pj_number'=>'',
                    "ledger_type"=>$line['jrn_def_type']
                );
            }
            else
            {
                $array[]=array(
                    'jr_id'=>$line['jr_id'],
                    'int_j_id'=>$line['int_j_id'],
                    'j_id'=>'',
                    'j_date'=>'',
                    'internal'=>'',
                    'deb_montant'=>$line['deb_montant'],
                    'cred_montant'=>$line['cred_montant'],
                    'description'=>$line['description'],
                    'poste'=>$line['poste'],
                    'j_qcode'=>$line['j_qcode'],
                    'periode'=>$line['periode'],
                    'jr_pj_number'=>'',
                    "ledger_type"=>$line['jrn_def_type']);
            }
        }
        $this->data=array($array, $tot_deb, $tot_cred);
        return $Max;
    }

    /**
     * display in  html the detail the list of operation
     */
    public function export_detail_html()
    {
        $this->export_accounting_html();
    }

    /**
     * display in  html with extended detail the list of operation
     */
    public function export_extended_html()
    {
        $this->export_accounting_html();
    }

    /**
     * display in  html the accounting of the list of operations
     */
    public function export_accounting_html()
    {

        $this->get_row();
        echo '<TABLE class="result">';
// detailled printing
//---
        foreach ($this->data[0] as $op)
        {
            $class="";
            if ($op['j_date']!='')
            {
                $class="odd";
            }

            echo "<TR  class=\"$class\">";

            echo "<TD>".$op['j_date']."</TD>";
            echo "<TD >".$op['jr_pj_number']."</TD>";


            if ($op['internal']!='')
                echo "<TD>".HtmlInput::detail_op($op['jr_id'], $op['internal'])."</TD>";
            else
                echo td();

            echo "<TD >".$op['poste']."</TD>".
            "<TD  >".$op['description']."</TD>".
            "<TD   style=\"text-align:right\">".nbm($op['deb_montant'])."</TD>".
            "<TD style=\"text-align:right\">".nbm($op['cred_montant'])."</TD>".
            "</TR>";
        }// end loop
        echo "</table>";

// show the saldo
//@todo use <li> instead of <br>
        echo _("solde débiteur:").$this->data[1]."<br>";
        echo _("solde créditeur:").$this->data[2];
    }

    /**
     * @brief list operation on one line per operation
     */
    public function export_oneline_html()
    {
        $this->get_rowSimple();
        echo '<TABLE class="result">';
        echo "<TR>".
        th(_("Date")).
        th(_("n° pièce")).
        th(_("internal")).
        th(_("Tiers")).
        th(_("Commentaire")).
        th(_("Total opération")).
        "</TR>";
        // set a filter for the FIN
        $i=0; $tot_amount=0;
        bcscale(2);
        foreach ($this->data as $line)
        {
            $i++;
            $class=($i%2==0)?' class="even" ':' class="odd" ';
            echo "<tr $class>";
            echo "<TD>".$line['date']."</TD>";
            echo "<TD>".h($line['jr_pj_number'])."</TD>";
            echo "<TD>".HtmlInput::detail_op($line['jr_id'],
                    $line['jr_internal'])."</TD>";
            $tiers=$this->get_tiers($line['jrn_def_type'], $line['jr_id']);
            echo td($tiers);
            echo "<TD>".h($line['comment'])."</TD>";


            //	  echo "<TD>".$line['pj']."</TD>";
            // If the ledger is financial :
            // the credit must be negative and written in red
            // Get the jrn type
            if ($line['jrn_def_type']=='FIN')
            {
                $positive=$this->db->get_value("select qf_amount from quant_fin where jr_id=$1",
                        array($line['jr_id']));
                if ($this->db->count()==0)
                    $positive=1;
                else
                    $positive=($positive>0)?1:0;

                echo "<TD align=\"right\">";
                echo ( $positive==0 )?"<font color=\"red\">  - ".nbm($line['montant'])."</font>":nbm($line['montant']);
                echo "</TD>";
                if ($positive==1)
                {
                    $tot_amount=bcadd($tot_amount, $line['montant']);
                }
                else
                {
                    $tot_amount=bcsub($tot_amount, $line['montant']);
                }
            }
            else
            {
                echo "<TD align=\"right\">".nbm($line['montant'])."</TD>";
                $tot_amount=bcadd($tot_amount, $line['montant']);
            }

            echo "</tr>";
        }
        echo '<tr class="highlight">';
        echo '<td>'._('Totaux').'</td>';
        echo td().td().td().td();
        echo '<td class="num">'.nbm($tot_amount).'</td>';
        echo '</tr>';
        echo "</table>";
    }

    /**
     * To get data
     * @return array of rows
     */
    function get_data()
    {
        return $this->data;
    }
    
    /**
     * export CSV
     */
    function export_csv()
    {
        $export=new Noalyss_Csv(_('journal'));
        $export->send_header();
        
        $this->get_row();
        $title=array();
        $title[]=_("operation");
        $title[]=_("N° Pièce");
        $title[]=_("Interne");
        $title[]=_("Date");
        $title[]=_("Poste");
        $title[]=_("QuickCode");
        $title[]=_("Libellé");
        $title[]=_("Débit");
        $title[]=_("Crédit");
        $export->write_header($title);
        if (count($this->data)==0)
            exit;
        $old_id="";
        /**
         * @todo add table headers
         */
        foreach ($this->data[0] as $idx=>$op)
        {
            // should clean description : remove <b><i> tag and '; char
            $desc=$op['description'];
            $desc=str_replace("<b>", "", $desc);
            $desc=str_replace("</b>", "", $desc);
            $desc=str_replace("<i>", "", $desc);
            $desc=str_replace("</i>", "", $desc);
            if ($op['j_id']!="")
                $old_id=$op['j_id'];

            $export->add($old_id, "text");
            $export->add($op['jr_pj_number']);
            $export->add($op['internal']);
            $export->add($op['j_date']);
            $export->add($op['poste']);
            $export->add($op['j_qcode']);
            $export->add($desc);
            $export->add($op['deb_montant'], "number");
            $export->add($op['cred_montant'], "number");
            $export->write();
        }
    }
}
    