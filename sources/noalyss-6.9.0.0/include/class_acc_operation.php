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
 * \brief  this file match the tables jrn & jrnx the purpose is to
 *   remove or save accountant writing to these table.
 */
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';

/*! \brief  this file match the tables jrn & jrnx the purpose is to
 *   remove or save accountant writing to these table.
 *
 */
class Acc_Operation
{
    var $db; 				/*!< database connx */
    var $jr_id;       	/*!< pk of jrn */
    var $jrn_id;			/*!< jrn_def_id */
    var $debit;			/*!< debit or credit */
    var $user;			/*!< current user */
    var $jrn;			/*!< the ledger to use */
    var $poste;			/*!< account  */
    var $date;			/*!< the date */
    var $periode;			/*!< periode to use */
    var $amount;			/*!< amount of the operatoin */
    var $grpt;			/*!< the group id */
    var $date_paid;
    /*!
     * \brief constructor set automatically the attributes user and periode
     * \param $p_cn the databse connection
     */
    function __construct($p_cn)
    {
        global $g_user;
        $this->db=$p_cn;
        $this->qcode="";
        $this->user=$_SESSION['g_user'];
        $this->periode=$g_user->get_periode();
        $this->jr_id=0;
    }
    /**
     *@brief retrieve the grpt_id from jrn for a jr_id
     *@return jrn.jr_grpt_id or an empty string if not found
     */
    function seek_group()
    {
        $ret=$this->db->get_value('select jr_grpt_id from jrn where jr_id=$1',
                                  array($this->jr_id));
        return $ret;
    }
    /**
     *@brief  Insert into the table Jrn
     *The needed data are :
     * - this->date
     * - this->amount
     * - this->poste
     * - this->grpt
     * - this->jrn
     * - this->type ( debit or credit)
     * - this->user
     * - this->periode
     * - this->qcode
     * - this->desc optional
     *@note if the amount is less than 0 then side changes, for example debit becomes
     *a credit and vice versa
     *@return jrnx.j_id
     */

    function insert_jrnx()
    {
      if ( $this->poste == "") { return false; throw new  Exception (__FILE__.':'.__LINE__.' Poste comptable vide');}
        /* for negative amount the operation is reversed */
        if ( $this->amount < 0 )
        {
            $this->type=($this->type=='d')?'c':'d';
        }
        $this->amount=abs($this->amount);
        $debit=($this->type=='c')?'false':'true';
        $this->desc=(isset($this->desc))?$this->desc:'';
        $Res=$this->db->exec_sql("select insert_jrnx
                                 ($1::text,abs($2)::numeric,$3::account_type,$4::integer,$5::integer,$6::bool,$7::text,$8::integer,upper($9),$10::text)",
                                 array(
                                     $this->date, //$1
                                     round($this->amount,2), //$2
                                     $this->poste, //$3
                                     $this->grpt, //$4
                                     $this->jrn, //$5
                                     $debit, //$6
                                     $this->user, //$7
                                     $this->periode, //$8
                                     $this->qcode, // $9
                                     $this->desc)); //$10
        if ( $Res===false) return $Res;
        $this->jrnx_id=$this->db->get_current_seq('s_jrn_op');
        return $this->jrnx_id;

    }
    /*!\brief set the pj of a operation in jrn. the jr_id must be set
     *\note if the jr_id it fails
     */
    function set_pj()
    {
        if ( strlen(trim($this->pj)) == 0 )
        {
            $sql="update jrn set jr_pj_number=$1 where jr_id=$2";
            $this->db->exec_sql($sql,array(null,$this->jr_id));
            return '';
        }
        /* is pj uniq ? */
        if ( $this->db->count_sql("select jr_id from jrn where jr_pj_number=$1 and jr_def_id=$2",
                                  array($this->pj,$this->jrn)
                                 ) == 0 )
        {
            $sql="update jrn set jr_pj_number=$1 where jr_id=$2";
            $this->db->exec_sql($sql,array($this->pj,$this->jr_id));
        }
        else
        {
            /* get pref */
            $pref=$this->db->get_value("select jrn_def_pj_pref from jrn_def where jrn_def_id=$1",
                                       array($this->jrn));
            /*  try another seq */
            $flag=0;
            $limit=100;
            while ( $flag == 0 )
            {
                /*  limit the search to $limit */
                if ( $limit < 1 )
                {
                    $this->pj='';
                    $flag=2;
                    break;
                }

                $seq=$this->db->get_next_seq('s_jrn_pj'.$this->jrn);
                $this->pj=$pref.$seq;

                /* check if the new pj numb exist */
                $c=$this->db->count_sql("select jr_id from jrn where jr_pj_number=$1 and jr_def_id=$2",
                                        array($this->pj,$this->jrn)
                                       );
                if ( $c == 0 )
                {
                    $flag=1;
                    break;
                }
                $limit--;
            }
            /* a pj numb is found */
            if ( $flag == 1 )
            {
                $sql="update jrn set jr_pj_number=$1 where jr_id=$2";
                $this->db->exec_sql($sql,array($this->pj,$this->jr_id));
            }
        }
        return $this->pj;
    }

    /*!
     *\brief  Insert into the table Jrn, the amount is computed from jrnx thanks the
     *        group id ($p_grpt)
     *
     * \return  sequence of jr_id
     *
     */

    function insert_jrn()
    {
        $p_comment=$this->desc;

        $diff=$this->db->get_value("select check_balance ($1)",array($this->grpt));
        if ( $diff != 0 )
        {

            echo "Erreur : balance incorrecte :diff = $diff";
            return false;
        }

        $echeance=( isset( $this->echeance) && strlen(trim($this->echeance)) != 0)?$this->echeance:null;
        if ( ! isset($this->mt) )
        {
            $this->mt=microtime(true);
        }
        // if amount == -1then the triggers will throw an error
        //
        $Res=$this->db->exec_sql("insert into jrn (jr_def_id,jr_montant,jr_comment,".
                                 "jr_date,jr_ech,jr_grpt_id,jr_tech_per,jr_mt)   values (".
                                 "$1,$2,$3,".
                                 "to_date($4,'DD.MM.YYYY'),to_date($5,'DD.MM.YYYY'),$6,$7,$8)",
                                 array ($this->jrn, $this->amount,$p_comment,
                                        $this->date,$echeance,$this->grpt,$this->periode,$this->mt)
                                );
        if ( $Res == false)  return false;
        $this->jr_id=$this->db->get_current_seq('s_jrn');
        return $this->jr_id;
    }
    /*!
     * \brief  Return the internal value, the property jr_id must be set before
     *
     * \return  null si aucune valeur de trouv
     *
     */
    function get_internal()
    {
        if ( ! isset($this->jr_id) )
            throw new Exception('jr_id is not set',1);
        $Res=$this->db->exec_sql("select jr_internal from jrn where jr_id=".$this->jr_id);
        if ( Database::num_row($Res) == 0 ) return null;
        $l_line=Database::fetch_array($Res);
        $this->jr_internal= $l_line['jr_internal'];
        return $this->jr_internal;
    }
    /*!\brief search an operation thankx it internal code
     * \param internal code
     * \return 0 ok -1 nok
     */
    function seek_internal($p_internal)
    {
        $res=$this->db->exec_sql('select jr_id from jrn where jr_internal=$1',
                                 array($p_internal));
        if ( Database::num_row($Res) == 0 ) return -1;
        $this->jr_id=Database::fetch_result($Res,0,0);
        return 0;
    }
    /*!\brief retrieve data from jrnx
      *\note the data are filtered by the access of the current user
     * \return an array
     */
    function get_jrnx_detail()
    {
        global $g_user;
        $filter_sql=$g_user->get_ledger_sql('ALL',3);
        $filter_sql=str_replace('jrn_def_id','jr_def_id',$filter_sql);
        if ( $this->jr_id==0 ) return;
        $sql=" select  jr_date,j_qcode,j_poste,j_montant,jr_internal,case when j_debit = 'f' then 'C' else 'D' end as debit,jr_comment as description,
             vw_name,pcm_lib,j_debit,coalesce(comptaproc.get_letter_jnt(j_id),-1) as letter,jr_def_id ".
             " from jrnx join jrn on (jr_grpt_id=j_grpt)
             join tmp_pcmn on (j_poste=pcm_val)
             left join vw_fiche_attr on (j_qcode=quick_code)
             where
             jr_id=$1 and $filter_sql order by j_debit desc";
        $res=$this->db->exec_sql($sql,array($this->jr_id));
        if ( Database::num_row ($res) == 0 ) return array();
        $all=Database::fetch_all($res);
        return $all;
    }
    /*!\brief add a comment to the line (jrnx.j_text) */
    function update_comment($p_text)
    {
        $sql="update jrnx set j_text=$1 where j_id=$2";
        $this->db->exec_sql($sql,array($p_text,$this->jrnx_id));
    }
    /*!\brief add a comment to the operation (jrn.jr_text) */
    function operation_update_comment($p_text)
    {
        $sql="update jrn set jr_comment=$1 where jr_id=$2";
        $this->db->exec_sql($sql,array($p_text,$this->jr_id));
    }
    /*!\brief add a limit of payment to the operation (jrn.jr_ech) */
    function operation_update_date_limit($p_text)
    {
        if ( isDate($p_text) == null )
        {
            $p_text=null;
        }
        $sql="update jrn set jr_ech=to_date($1,'DD.MM.YYYY') where jr_id=$2";
        $this->db->exec_sql($sql,array($p_text,$this->jr_id));
    }
    /*!\brief return the jrn_def_id from jrn */
    function get_ledger()
    {
        $sql="select jr_def_id from jrn where jr_id=$1";
        $row=$this->db->get_value($sql,array($this->jr_id));
        return $row;
    }
    /*!\brief display_jrnx_detail : get the data from get_jrnx_data and
       return a string with HTML code
     * \param table(=0 no code for table,1 code for table,2 code for CSV)

    */
    function display_jrnx_detail($p_table)
    {
        $show=$this->get_jrnx_detail();

        $r='';
        $r_notable='';
        $csv="";
        foreach ($show as $l)
        {
            $border="";
            if ( $l['j_poste'] == $this->poste || ($l['j_qcode']==$this->qcode && trim($this->qcode) != ''))
                $border=' class="highlight"';
            $r.='<tr '.$border.'>';
            $r.='<td>';
            $a=$l['j_qcode'];
            
            $r_notable.=$a;
            $r.=$a;
            $csv.='"'.$a.'";';
            $r.='</td>';

            $r.='<td  '.$border.'>';
            $a=$l['j_poste'];
            $r_notable.=$a;
            $r.=$a;
            $csv.='"'.$a.'";';
            $r.='</td>';

            $r.='<td  '.$border.'>';
            //       $a=($l['vw_name']=="")?$l['j_qcode']:$l['pcm_lib'];
            $a=(strlen(trim($l['j_qcode']))==0)?$l['pcm_lib']:$l['vw_name'];
            $r_notable.=$a;
            $r.=h($a);
            $csv.='"'.$a.'";';
            $r.='</td>';

            $r.='<td  '.$border.'>';
            $a=$l['j_montant'];
            $r_notable.=$a;
            $r.=$a;
            $csv.=$a.';';
            $r.='</td>';

            $r.='<td  '.$border.'>';
            $a=$l['debit'];
            $r_notable.=$a;
            $r.=$a;
            $csv.='"'.$a.'"';

            $csv.="\r\n";
            $r.='</td>';
            $r.='<td  '.$border.'>';
            $a=($l['letter']!=-1)?$l['letter']:'';
            $r_notable.=$a;
            $r.=$a;
            $csv.='"'.$a.'"';

            $csv.="\r\n";
            $r.='</td>';


            $r.='</tr>';
        }
        switch ($p_table)
        {
        case 1:
            return $r;
            break;
        case 0:
            return $r_notable;
            break;
        case 2:
            return $csv;
        }
        return "ERROR PARAMETRE";
    }
    /*!
     * @brief  Get data from jrnx where p_grpt=jrnx(j_grpt)
     *
     * @param connection
     * @return array of 3 elements
     *  - First Element is an array
    @verbatim
    Array
    (
        [op_date] => 01.12.2009
        [class_cred0] => 7000008
        [mont_cred0] => 8880.0000
        [op_cred0] => 754
        [text_cred0] =>
        [jr_internal] => 23VEN-01-302
        [comment] =>
        [ech] =>
        [jr_id] => 302
        [jr_def_id] => 2
        [class_deb0] => 4000005
        [mont_deb0] => 10744.8000
        [text_deb0] =>
        [op_deb0] => 755
        [class_cred1] => 4511
        [mont_cred1] => 1864.8000
        [op_cred1] => 756
        [text_cred1] =>
    )
    @endverbatim
     *  - Second  : number of line with debit
     *  - Third  : number of line with credit
     */
    function get_data ($p_grpt)
    {
        $Res=$this->db->exec_sql("select
                                 to_char(j_date,'DD.MM.YYYY') as j_date,
                                 j_text,
                                 j_debit,
                                 j_poste,
                                 coalesce(j_qcode,'-') as qcode,
                                 j_montant,
                                 j_id,
                                 jr_comment,
                                 to_char(jr_ech,'DD.MM.YYYY') as jr_ech,
                                 to_char(jr_date,'DD.MM.YYYY') as jr_date,
                                 jr_id,jr_internal,jr_def_id,jr_pj
                                 from jrnx inner join jrn on j_grpt=jr_grpt_id where j_grpt=$1",array($p_grpt));
        $MaxLine=Database::num_row($Res);
        if ( $MaxLine == 0 ) return null;
        $deb=0;
        $cred=0;
        for ( $i=0; $i < $MaxLine; $i++)
        {

            $l_line=Database::fetch_array($Res,$i);
            $l_array['op_date']=$l_line['j_date'];
            if ( $l_line['j_debit'] == 't' )
            {
                $l_class=sprintf("class_deb%d",$deb);
                $l_montant=sprintf("mont_deb%d",$deb);
                $l_text=sprintf("text_deb%d",$deb);
                $l_qcode=sprintf("qcode_deb%d",$deb);
                $l_array[$l_class]=$l_line['j_poste'];
                $l_array[$l_montant]=$l_line['j_montant'];
                $l_array[$l_text]=$l_line['j_text'];
                $l_array[$l_qcode]=$l_line['qcode'];
                $l_id=sprintf("op_deb%d",$deb);
                $l_array[$l_id]=$l_line['j_id'];
                $deb++;
            }
            if ( $l_line['j_debit'] == 'f' )
            {
                $l_class=sprintf("class_cred%d",$cred);
                $l_montant=sprintf("mont_cred%d",$cred);
                $l_array[$l_class]=$l_line['j_poste'];
                $l_array[$l_montant]=$l_line['j_montant'];
                $l_id=sprintf("op_cred%d",$cred);
                $l_array[$l_id]=$l_line['j_id'];
                $l_text=sprintf("text_cred%d",$cred);
                $l_array[$l_text]=$l_line['j_text'];
                $l_qcode=sprintf("qcode_cred%d",$cred);
                $l_array[$l_qcode]=$l_line['qcode'];
                $cred++;
            }
            $l_array['jr_internal']=$l_line['jr_internal'];
            $l_array['comment']=$l_line['jr_comment'];
            $l_array['ech']=$l_line['jr_ech'];
            $l_array['jr_id']=$l_line['jr_id'];
            $l_array['jr_def_id']=$l_line['jr_def_id'];
        }
        return array($l_array,$deb,$cred);
    }
    /**
    *@brief retrieve data from jrnx and jrn
    *@return return an object
    *@note
    *@see
    @code

    @endcode
    */
    function get()
    {
        $ret=new Acc_Misc($this->db,$this->jr_id);
        $ret->get();
        return $ret;
    }
    /**
     *@brief retrieve data from the table QUANT_*
     *@return return an object or null if there is no
     * data from the QUANT table
     *@see Acc_Sold Acc_Purchase Acc_Fin Acc_Detail Acc_Misc
     */
    function get_quant()
    {
        $ledger_id=$this->get_ledger();
        if ( $ledger_id=='') throw new Exception(_('Journal non trouvé'));
        $oledger=new Acc_Ledger($this->db,$ledger_id);

        // retrieve info from jrn_info


        switch($oledger->get_type())
        {
        case 'VEN':
            $ret=new Acc_Sold($this->db,$this->jr_id);
            break;
        case 'ACH':
            $ret=new Acc_Purchase($this->db,$this->jr_id);
            break;
        case 'FIN':
            $ret=new Acc_Fin($this->db,$this->jr_id);
            break;
        default:
			$ret=new Acc_Misc($this->db,$this->jr_id);
			break;
        }
        $ret->get();
        if ( empty($ret->det->array))
        {
            $ret=new Acc_Misc($this->db,$this->jr_id);
            $ret->get();
        }
        $ret->get_info();
        return $ret;
    }
    /**
     * @brief retrieve info from the jrn_info, create 2 new arrays
     * obj->info->command and obj->info->other
     * the columns are the idx
     */
    function get_info()
    {
        $this->info=new stdClass();
        // other info
        $array=$this->db->get_value("select ji_value from jrn_info where
            jr_id=$1 and id_type=$2",array($this->jr_id,'OTHER'));
        $this->info->other= $array;

        // Bon de commande
        $array=$this->db->get_value("select ji_value from jrn_info where
            jr_id=$1 and id_type=$2",array($this->jr_id,'BON_COMMANDE'));
        $this->info->command=  $array;

    }
    /**
     * Save into jrn_info
     * @param $p_info msg to save
     * @param $p_type is OTHER or BON_COMMAND
     */
    function save_info($p_info,$p_type)
    {
        if ( ! in_array($p_type,array('OTHER','BON_COMMANDE'))) return;
        if (trim($p_info)=="") {
            $this->db->exec_sql('delete from jrn_info where jr_id=$1 and id_type=$2',array($this->jr_id,$p_type));
            return;
        }
        $exist=$this->db->get_value('select count(ji_id) from jrn_info where jr_id=$1 and id_type=$2',array($this->jr_id,$p_type));
        if ( $exist == "0" ) {
            //insert into jrn_info
            $this->db->exec_sql('insert into jrn_info(jr_id,id_type,ji_value) values ($1,$2,$3)',
                    array($this->jr_id,$p_type,$p_info));
        } elseif ( $exist == 1) {
            //update
            $this->db->exec_sql('update jrn_info set ji_value=$3 where jr_id=$1 and id_type=$2',
                    array($this->jr_id,$p_type,$p_info));
        }
    }
    
    function insert_related_action($p_string)
    {
        if ($p_string == "") return;
        $a_action=explode(',',$p_string);
        for ($i=0;$i<count($a_action);$i++)
        {
            $action = new Follow_Up($this->db,$a_action[$i]);
            $action->operation=$this->jr_id;
            $action->insert_operation();
        }
    }
    static function test_me()
    {
        $_SESSION['g_user']='phpcompta';
        $_SESSION['g_pass']='dany';
        global $g_user;
        $cn=new Database(dossier::id());
        $g_user=new User($cn);
        $a=new Acc_Operation($cn);
        $a->jr_id=1444;
        $b=$a->get_quant();
        var_dump($b);
    }
}
/////////////////////////////////////////////////////////////////////////////
class Acc_Detail extends Acc_Operation
{
    function __construct($p_cn,$p_jrid=0)
    {
        parent::__construct($p_cn);
        $this->jr_id=$p_jrid;
        $this->det=new stdClass();
    }
    /**
     *@brief retrieve some common data from jrn as
     * the datum, the comment,payment limit...
     */
    function get()
    {
        $sql="SELECT jr_id, jr_def_id, jr_montant, jr_comment, jr_date, jr_grpt_id,
             jr_internal, jr_tech_date, jr_tech_per, jrn_ech, jr_ech, jr_rapt,jr_ech,
             jr_valid, jr_opid, jr_c_opid, jr_pj, jr_pj_name, jr_pj_type,
             jr_pj_number, jr_mt,jr_rapt,jr_date_paid
             FROM jrn where jr_id=$1";
        $array=$this->db->get_array($sql,array($this->jr_id));
        if ( count($array) == 0 ) throw new Exception('Aucune ligne trouvée');
        foreach ($array[0] as $key=>$val)
        {
            $this->det->$key=$val;
        }
	$sql="select n_text from jrn_note where jr_id=$1";
	$this->det->note=$this->db->get_value($sql,array($this->jr_id));
	$this->det->note=strip_tags($this->det->note);
    }
}
/////////////////////////////////////////////////////////////////////////////
/**
 *@brief this class manage data from the JRNX and JRN
 * table
 *@note Data member are the column of the table
 */
class Acc_Misc extends Acc_Detail
{
    var $signature;		/*!< signature of the obj ODS */
    var $array;		/*!< an array containing the data from JRNX */
    function __construct($p_cn,$p_jrid=0)
    {
        parent::__construct($p_cn,$p_jrid);
        $this->signature='ODS';
        $this->det=new stdClass();
    }
    function get()
    {
        parent::get();
        $sql="SELECT j_id, j_date, j_montant, j_poste, j_grpt, j_rapt, j_jrn_def,
             j_debit, j_text, j_centralized, j_internal, j_tech_user, j_tech_date,
             j_tech_per, j_qcode
             FROM jrnx where j_grpt = $1 order by j_debit desc,j_poste";
        $this->det->array=$this->db->get_array($sql,array($this->det->jr_grpt_id));
    }
}
/////////////////////////////////////////////////////////////////////////////
/**
 *@brief this class manage data from the QUANT_SOLD
 * table
 *@note Data member are the column of the table
 */
class Acc_Sold extends Acc_Detail
{
    function __construct($p_cn,$p_jrid=0)
    {
        parent::__construct($p_cn,$p_jrid);
        $this->signature='VEN';
        $this->det=new stdClass();
    }
    function get()
    {
        parent::get();
        $sql="SELECT qs_id, qs_internal, qs_fiche, qs_quantite, qs_price, qs_vat,
             qs_vat_code, qs_client, qs_valid, j_id,j_text,qs_vat_sided
             FROM quant_sold  join jrnx using(j_id) where j_grpt=$1";
        $this->det->array=$this->db->get_array($sql,array($this->det->jr_grpt_id));
    }
    
}
/////////////////////////////////////////////////////////////////////////////
/**
 *@brief this class manage data from the QUANT_PURCHASE
 * table
 *@note Data member are the column of the table

 */
class Acc_Purchase extends Acc_Detail
{
    function __construct($p_cn,$p_jrid=0)
    {
        parent::__construct($p_cn,$p_jrid);
        $this->signature='ACH';
    }

    function get()
    {
        parent::get();
        $sql="SELECT qp_id, qp_internal, j_id, qp_fiche, qp_quantite, qp_price, qp_vat,
             qp_vat_code, qp_nd_amount, qp_nd_tva, qp_nd_tva_recup, qp_supplier,
             qp_valid, qp_dep_priv,j_text,qp_vat_sided
             FROM quant_purchase  join jrnx using(j_id) where j_grpt=$1";
        $this->det->array=$this->db->get_array($sql,array($this->det->jr_grpt_id));
    }
}
/////////////////////////////////////////////////////////////////////////////
/**
 *@brief this class manage data from the QUANT_FIN
 * table
 *@note Data member are the column of the table
 */
class Acc_Fin extends Acc_Detail
{
    function __construct($p_cn,$p_jrid=0)
    {
        parent::__construct($p_cn,$p_jrid);
        $this->signature='FIN';
    }

    function get()
    {
        parent::get();
        $sql="SELECT qf_id, qf_bank, jr_id, qf_other, qf_amount
             FROM quant_fin where jr_id = $1";
        $this->det->array=$this->db->get_array($sql,array($this->jr_id));
    }
}
