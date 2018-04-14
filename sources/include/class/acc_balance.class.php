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
 * \brief Class for manipulating data to print the balance of account
 */
/*!
 * \brief Class for manipulating data to print the balance of account
 */
require_once NOALYSS_INCLUDE.'/class/acc_account.class.php';

class Acc_Balance
{
    var $db;       /*! < database connection */
    var $row;     /*! < row for ledger*/
    var $jrn;						/*!< idx of a table of ledger create by user->get_ledger */
    var $from_poste;				/*!< from_poste  filter on the post */
    var $to_poste;				/*!< to_poste filter on the post*/
    var $unsold;				/**= 0) */
    function __construct($p_cn)
    {
        $this->db=$p_cn;
        $this->jrn=null;
        $from_poste="";
        $to_poste="";
	$unsold=false;
    }


    /*!
     * \brief retrieve all the row from the ledger in the range of a periode
     * \param $p_from_periode start periode (p_id)
     * \param $p_to_periode end periode (p_id)
     * \param $p_previous_exc previous exercice 1= yes default =0
     *
     * \return a double array
     *     array of
     *         - $a['poste']
     *         - $a['label']
     *         - $a['sum_deb']
     *         - $a['sum_cred']
     *         - $a['solde_deb']
     *         - $a['solde_cred']
     */
    function get_row($p_from_periode,$p_to_periode,$p_previous_exc=0)
    {
        global $g_user;
        // filter on requested periode
        $per_sql=sql_filter_per($this->db,$p_from_periode,$p_to_periode,'p_id','j_tech_per');
        bcscale(2);

        $and="";
        $jrn="";
        $from_poste="";
        $to_poste="";
        /* if several ledgers are asked then we filter here  */
        if ($this->jrn!== null)
        {
            /**
             *@file
             *@bug the get_ledger here is not valid and useless we just need a list of the 
             * asked ledgers
             */

            $jrn="  j_jrn_def in (";
            $comma='';
            for ($e=0;$e<count($this->jrn);$e++)
            {
                $jrn.=$comma.$this->jrn[$e];
                $comma=',';
            }
            $jrn.=')';
            $and=" and ";
        }

        if ( strlen(trim($this->from_poste)) != 0 && $this->from_poste!=-1  )
        {
            $from_poste=" $and j_poste::text >= '".$this->from_poste."'";
            $and=" and ";
        }
        if ( strlen(trim($this->to_poste)) != 0   && $this->to_poste!=-1 )
        {
            $to_poste=" $and j_poste::text <= '".$this->to_poste."'";
            $and=" and ";
        }
        $filter_sql=$g_user->get_ledger_sql('ALL',3);
        
        switch ($p_previous_exc)
        {
            case 0:
                // build query
                $sql="select j_poste as poste,
                        sum(deb) as sum_deb, 
                        sum(cred) as sum_cred,
                        sum(deb_op)  as sum_deb_ope , 
                        sum(cred_op) as sum_cred_ope
                    from               
                     ( select j_poste,
                     case when j_debit='t' then j_montant else 0 end as deb,
                     case when j_debit='f' then j_montant else 0 end as cred,
                     case when j_debit='t' and jr_optype='OPE'  then j_montant else 0 end as deb_op,
                     case when j_debit='f' and jr_optype='OPE' then j_montant else 0 end as cred_op
                     from jrnx join tmp_pcmn on (j_poste=pcm_val)
                     left join parm_periode on (j_tech_per = p_id)
                     join jrn_def on (j_jrn_def=jrn_def_id)
                     join jrn on (j_grpt=jr_grpt_id)
                     where
                     $jrn $from_poste $to_poste
                     $and $filter_sql
                     and
                     $per_sql ) as m group by 1 order by 1";
                break;
            case 1:
                /*
                 * retrieve balance previous exercice
                 */
                $periode=new Periode($this->db);
                $previous_exc=$periode->get_exercice($p_from_periode)-1;
                try {
                    list($previous_start,$previous_end)=$periode->get_limit($previous_exc);
               
                        $per_sql_previous=sql_filter_per($this->db,$previous_start->p_id,$previous_end->p_id,'p_id','j_tech_per');
                        $sql="
                            with m as 
                                ( select j_poste,sum(deb) as sdeb,sum(cred) as scred,
                                            sum(deb_op)  as sum_deb_ope , 
                                            sum(cred_op) as sum_cred_ope
                                from 
                                (select j_poste, 
                                    case when j_debit='t' then j_montant else 0 end as deb, 
                                    case when j_debit='f' then j_montant else 0 end as cred ,
                                    case when j_debit='t' and jr_optype='OPE'  then j_montant else 0 end as deb_op,
                                    case when j_debit='f' and jr_optype='OPE' then j_montant else 0 end as cred_op
                                    from jrnx 
                                    join jrn on (j_grpt=jr_grpt_id)
                                    join tmp_pcmn on (j_poste=pcm_val) 
                                    left join parm_periode on (j_tech_per = p_id) 
                                    join jrn_def on (j_jrn_def=jrn_def_id) 
                                    where
                                                             $jrn $from_poste $to_poste
                                    $and $filter_sql and $per_sql
                                    ) as sub_m group by j_poste order by j_poste ) , 
                            p as ( select j_poste,sum(deb) as sdeb,
                                            sum(cred) as scred 
                                from 
                                    (select j_poste, 
                                        case when j_debit='t' then j_montant else 0 end as deb, 
                                        case when j_debit='f' then j_montant else 0 end as cred 
                                        from jrnx join tmp_pcmn on (j_poste=pcm_val) 
                                        left join parm_periode on (j_tech_per = p_id) 
                                        join jrn_def on (j_jrn_def=jrn_def_id) 
                                        join jrn on (j_grpt=jr_grpt_id)
                                        where 
                                       $jrn $from_poste $to_poste
                                    $and $filter_sql and $per_sql_previous)  as sub_p group by j_poste order by j_poste)
                            select coalesce(m.j_poste,p.j_poste) as poste
                                                                ,coalesce(m.sdeb,0) as sum_deb
                                                                , coalesce(m.scred,0) as sum_cred 
                                                                ,coalesce(p.sdeb,0) as sum_deb_previous
                                                                , coalesce(p.scred,0) as sum_cred_previous 
                                                                ,coalesce(sum_deb_ope,0) as sum_deb_ope
                                                                ,coalesce(sum_cred_ope,0) as sum_cred_ope
                                                        from m full join p on (p.j_poste=m.j_poste)
                                             order by poste";
                       
                 } catch (Exception $exc) {
                    $p_previous_exc=0;
                    /*
                     * no previous exercice
                     */
                     $sql="select j_poste as poste,
                        sum(deb) as sum_deb, 
                        sum(cred) as sum_cred,
                        sum(deb_op)  as sum_deb_ope , 
                        sum(cred_op) as sum_cred_ope
                        from               
                         ( select j_poste,
                         case when j_debit='t' then j_montant else 0 end as deb,
                         case when j_debit='f' then j_montant else 0 end as cred,
                         case when j_debit='t' and jr_optype='OPE'  then j_montant else 0 end as deb_op,
                         case when j_debit='f' and jr_optype='OPE' then j_montant else 0 end as cred_op
                         from jrnx join tmp_pcmn on (j_poste=pcm_val)
                         left join parm_periode on (j_tech_per = p_id)
                         join jrn_def on (j_jrn_def=jrn_def_id)
                         join jrn on (j_grpt=jr_grpt_id)
                     where
                     $jrn $from_poste $to_poste
                     $and $filter_sql
                     and
                     $per_sql ) as m group by poste order by poste";
                }
                break;
                           
        }
        $cn=clone $this->db;
        $Res=$this->db->exec_sql($sql);
        $tot_cred=  0.0;
        $tot_deb=  0.0;
        $tot_deb_saldo=0.0;
        $tot_cred_saldo=0.0;
        $tot_cred_ope=  0.0;
        $tot_deb_ope=  0.0;
        $tot_deb_saldo_ope=0.0;
        $tot_cred_saldo_ope=0.0;
        $tot_cred_previous=  0.0;
        $tot_deb_previous=  0.0;
        $tot_deb_saldo_previous=0.0;
        $tot_cred_saldo_previous=0.0;
        $M=$this->db->size();

        // Load the array
        for ($i=0; $i <$M;$i++)
        {
            $r=$this->db->fetch($i);
            $poste=new Acc_Account($cn,$r['poste']);

            $a['poste']=$r['poste'];
            $a['label']=mb_substr($poste->get_lib(),0,40);
            $a['sum_deb']=round($r['sum_deb'],2);
            $a['sum_cred']=round($r['sum_cred'],2);
            $a['solde_deb']=round(( $a['sum_deb']  >=  $a['sum_cred'] )? $a['sum_deb']- $a['sum_cred']:0,2);
            $a['solde_cred']=round(( $a['sum_deb'] <=  $a['sum_cred'] )?$a['sum_cred']-$a['sum_deb']:0,2);
            // opening
            $a['sum_deb_ope']=round($r['sum_deb_ope'],2);
            $a['sum_cred_ope']=round($r['sum_cred_ope'],2);
            $a['solde_deb_ope']=round(( $a['sum_deb_ope']  >=  $a['sum_cred_ope'] )? $a['sum_deb_ope']- $a['sum_cred_ope']:0,2);
            $a['solde_cred_ope']=round(( $a['sum_deb_ope'] <=  $a['sum_cred_ope'] )?$a['sum_cred_ope']-$a['sum_deb_ope']:0,2);
            
            
            if ($p_previous_exc==1)
            {
                $a['sum_deb_previous']=round($r['sum_deb_previous'],2);
                $a['sum_cred_previous']=round($r['sum_cred_previous'],2);
                $a['solde_deb_previous']=round(( $a['sum_deb_previous']  >=  $a['sum_cred_previous'] )? $a['sum_deb_previous']- $a['sum_cred_previous']:0,2);
                $a['solde_cred_previous']=round(( $a['sum_deb_previous'] <=  $a['sum_cred_previous'] )?$a['sum_cred_previous']-$a['sum_deb_previous']:0,2);
                $tot_cred_previous = bcadd ($tot_cred_previous, $a['sum_cred_previous']);
                $tot_deb_previous  = bcadd( $tot_deb_previous,$a['sum_deb_previous']);
                $tot_deb_saldo_previous  = bcadd ($tot_deb_saldo_previous ,$a['solde_deb_previous']);
                $tot_cred_saldo_previous = bcadd ($tot_cred_saldo_previous,$a['solde_cred_previous']);
            }
	    if ($p_previous_exc==0 && $this->unsold==true && $a['solde_cred']==0 && $a['solde_deb']==0) continue;
	    if ($p_previous_exc==1 && $this->unsold==true && $a['solde_cred']==0 && $a['solde_deb']==0 && $a['solde_cred_previous']==0 && $a['solde_deb_previous']==0) continue;
            $array[$i]=$a;
            // Normal op
            $tot_cred=  bcadd ($tot_cred,$a['sum_cred']);
            $tot_deb= bcadd($tot_deb, $a['sum_deb']);
            $tot_deb_saldo= bcadd($tot_deb_saldo, $a['solde_deb']);
            $tot_cred_saldo= bcadd($tot_cred_saldo,$a['solde_cred']);
            // Opening op.
            $tot_cred_ope=  bcadd ($tot_cred_ope,$a['sum_cred_ope']);
            $tot_deb_ope= bcadd($tot_deb_ope, $a['sum_deb_ope']);
            $tot_deb_saldo_ope= bcadd($tot_deb_saldo_ope, $a['solde_deb_ope']);
            $tot_cred_saldo_ope= bcadd($tot_cred_saldo_ope,$a['solde_cred_ope']);
            
            
        }//for i
        // Add the saldo
        $i+=1;
        $delta=bcsub($tot_deb,$tot_cred);
        $side_delta=($delta<0)?"C":"D";
        $side_delta=($delta==0)?"=":$side_delta;
        
        $a['poste']="";
        $a['label']="Totaux ".nbm(abs($delta)).$side_delta;
        $a['sum_deb']=$tot_deb;
        $a['sum_cred']=$tot_cred;
        $a['solde_deb']=$tot_deb_saldo;
        $a['solde_cred']=$tot_cred_saldo;
        $a['sum_deb_ope']=$tot_deb_ope;
        $a['sum_cred_ope']=$tot_cred_ope;
        $a['solde_deb_ope']=$tot_deb_saldo_ope;
        $a['solde_cred_ope']=$tot_cred_saldo_ope;
        if ($p_previous_exc==1) {
            $a['sum_deb_previous']=$tot_deb_previous;
            $a['sum_cred_previous']=$tot_cred_previous;
            $a['solde_deb_previous']=$tot_deb_saldo_previous;
            $a['solde_cred_previous']=$tot_cred_saldo_previous;
        }
        $array[$i]=$a;
        $this->row=$array;
        return $array;

    }
    /**
     * set the $this->jrn to the cat
     * @todo Cette function semble ne pas fonctionner correctement
     */
    function filter_cat($p_array)
    {
        if ( empty($p_array) )
        {
            $this->jrn=null;
            return;
        }
        /* get the list of jrn of the cat. */

        $array=Acc_Ledger::array_cat();
        $jrn=array();
        for ($e=0;$e<count($array);$e++)
        {
            if ( isset($p_array[$e]))
            {
                $t_a=$this->db->get_array('select jrn_def_id from jrn_def where jrn_def_type=$1',array($array[$e]['cat']));
                for ( $f=0;$f < count($t_a);$f++) $this->jrn[]=$t_a[$f]['jrn_def_id'];
            }
        }

    }
    /**
     * @brief create an empty array for computing the summary
     */
    function summary_init()
    {
        $array=[];
        $array["1_5"]=["deb"=>0,"cred"=>0];
        $array["6"]=["deb"=>0,"cred"=>0];
        $array["7"]=["deb"=>0,"cred"=>0];
        return $array;
    }
    /**
     * Add the current amount (d /c) to the right item in the array,  in order
     * to compute a summary (1 to 5 , 6 charge and 7 profit ), 
     * the return value is an array
     * @see Acc_Balance::summary_init()
     * @param array $p_array array with the result
     * @param string $p_accounting accounting
     * @param numeric $p_deb
     * @param numeric  $p_cred
     * @return array 
     */
    function summary_add($p_array,$p_accounting,$p_deb,$p_cred)
    {
        if (trim($p_accounting)=="")            return $p_array;
        // Summary
        $first_digit=trim($p_accounting);
        $first_digit_trim=$first_digit[0];
        if ( $first_digit_trim >0 && $first_digit_trim < 6) {
            $p_array["1_5"]["deb"]=bcadd( $p_array["1_5"]["deb"],$p_deb);
            $p_array["1_5"]["cred"]=bcadd( $p_array["1_5"]["cred"],$p_cred);
        } 
        elseif ($first_digit_trim == "6") {
            $p_array["6"]["deb"]=bcadd( $p_array["6"]["deb"],$p_deb);
            $p_array["6"]["cred"]=bcadd( $p_array["6"]["cred"],$p_cred);
        }
        elseif ($first_digit_trim=="7") {
            $p_array["7"]["deb"]=bcadd( $p_array["7"]["deb"],$p_deb);
            $p_array["7"]["cred"]=bcadd( $p_array["7"]["cred"],$p_cred);
                
        }
        return $p_array;
    }
    /**
     *  Display the summary of result in HTML
     * @see Acc_Balance::summary_init()
     * @param array $p_array
     */
    function summary_display($p_array)
    {
        echo "<table>";
        echo "<tr>";
        echo td(_("Class 1-5"));
        $diff=bcsub($p_array["1_5"]["deb"],$p_array["1_5"]["cred"]);
        echo td(nbm(abs($diff),2),' class="num"');
        $side=($diff < 0)?"C":"D";
        $side=($diff == 0)?"=":$side;
        echo td($side);
        echo "</tr>";
        echo "<tr>";
        echo td(_("Class 6"));
        $diff6=bcsub($p_array["6"]["deb"],$p_array["6"]["cred"]);
        echo td(nbm(abs($diff6),2),' class="num"');
        $side=($diff6 < 0)?"C":"D";
        $side=($diff6 == 0)?"=":$side;
        echo td($side);
        echo "</tr>";
        echo "<tr>";
        echo td(_("Class 7"));
        $diff7=bcsub($p_array["7"]["deb"],$p_array["7"]["cred"]);
        echo td(nbm(abs($diff7),2),' class="num"');
        $side=($diff7 < 0)?"C":"D";
        $side=($diff7 == 0)?"=":$side;
        echo td($side);
        echo "</tr>";
        echo "<tr>";
        echo td(_("Solde 6/7"));
        $diff=bcadd($diff6,$diff7);
        echo td(nbm(abs($diff),2),' class="num"');
        $side=($diff < 0)?"C":"D";
        $side=($diff == 0)?"=":$side;
        echo td($side);
        echo "</tr>";
        echo "</table>";
    }
    /**
     * Display the summary of result in PDF
     * @param array $p_array
     * @param PDF $p_pdf
     * @see Acc_Balance::summary_init()
     */
    function summary_display_pdf($p_array,$p_pdf)
    {
        $p_pdf->write_cell(30,6,_("Class 1-5"));
        $diff=bcsub($p_array["1_5"]["deb"],$p_array["1_5"]["cred"]);
        $p_pdf->write_cell(50,6,
                            nbm(abs($diff),2),0,0,'R');
        $side=($diff < 0)?"C":"D";
        $side=($diff == 0)?"=":$side;
        $p_pdf->write_cell(10,6,$side);
        $p_pdf->line_new();

        $p_pdf->write_cell(30,6,_("Class 6"));
        $diff6=bcsub($p_array["6"]["deb"],$p_array["6"]["cred"]);
        $p_pdf->write_cell(50,6,nbm(abs($diff6),2),0,0,'R');
        $side=($diff6 < 0)?"C":"D";
        $side=($diff6 == 0)?"=":$side;
        $p_pdf->write_cell(10,6,$side);
        $p_pdf->line_new();
        
        $p_pdf->write_cell(30,6,_("Class 7"));
        $diff7=bcsub($p_array["7"]["deb"],$p_array["7"]["cred"]);
        $p_pdf->write_cell(50,6,nbm(abs($diff7),2),0,0,'R');
        $side=($diff7 < 0)?"C":"D";
        $side=($diff7 == 0)?"=":$side;
        $p_pdf->write_cell(10,6,$side);
        $p_pdf->line_new();
       
        $p_pdf->write_cell(30,6,_("Solde 6/7"));
        $diff=bcadd($diff6,$diff7);
        $p_pdf->write_cell(50,6,nbm(abs($diff),2),0,0,'R');
        $side=($diff < 0)?"C":"D";
        $side=($diff == 0)?"=":$side;
        $p_pdf->write_cell(10,6,$side);
        $p_pdf->line_new();
    }
    static function test_me ()
    {
        require 'class/user.class.php';
        global $g_user;
        $cn=Dossier::connect();
        $g_user=new User($cn);
        $a=new Acc_Balance($cn);
        $a->get_row(163, 175, 1);
        var_dump($a);
    }
}
