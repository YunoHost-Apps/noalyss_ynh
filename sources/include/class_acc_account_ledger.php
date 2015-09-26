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
 * \brief Manage the account from the table jrn, jrnx or tmp_pcmn
 */
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_dossier.php';

class Acc_Account_Ledger
{
    var $db;          /*!< $db database connection */
    var $id;          /*!< $id poste_id (pcm_val)*/
    var $label;       /*!< $label label of the poste */
    var $parent;      /*!< $parent parent account */
    var $row;         /*!< $row double array see get_row */
    var $tot_deb;    /*!< value set by  get_row */
    var $tot_cred;    /*!< value by  get_row */
    function __construct ($p_cn,$p_id)
    {
        $this->db=$p_cn;
        $this->id=$p_id;
    }
    /**
     *@brief get the row thanks the resource
     *@return double array (j_date,deb_montant,cred_montant,description,jrn_name,j_debit,jr_internal)
     *         (tot_deb,tot_credit)
     */
    private function get_row_sql($Res)
    {
        $array=array();
        $tot_cred=0.0;
        $tot_deb=0.0;
        $Max=Database::num_row($Res);
        if ( $Max == 0 ) return null;
        for ($i=0;$i<$Max;$i++)
        {
            $array[]=Database::fetch_array($Res,$i);
            if ($array[$i]['j_debit']=='t')
            {
                $tot_deb+=$array[$i]['deb_montant'] ;
            }
            else
            {
                $tot_cred+=$array[$i]['cred_montant'] ;
            }
        }
        $this->row=$array;
        $this->tot_deb=$tot_deb;
        $this->tot_cred=$tot_cred;
        return array($array,$tot_deb,$tot_cred);

    }
    /*!
     * \brief  Get data for accounting entry between 2 periode
     *
     * \param  $p_from periode from
     * \param  $p_to   end periode
     * \return double array (j_date,deb_montant,cred_montant,description,jrn_name,j_debit,jr_internal)
     *         (tot_deb,tot_credit
     *
     */
    function get_row($p_from,$p_to)
    {
        $periode=sql_filter_per($this->db,$p_from,$p_to,'p_id','jr_tech_per');

        $Res=$this->db->exec_sql("select distinct j_id,jr_id,to_char(j_date,'DD.MM.YYYY') as j_date_fmt,j_date,".
                                 "case when j_debit='t' then j_montant else 0 end as deb_montant,".
                                 "case when j_debit='f' then j_montant else 0 end as cred_montant,".
                                 " jr_comment as description,jrn_def_name as jrn_name,".
                                 "j_debit, jr_internal,jr_pj_number ".
                                 " from jrnx left join jrn_def on jrn_def_id=j_jrn_def ".
                                 " left join jrn on jr_grpt_id=j_grpt".
                                 " where j_poste=".$this->id." and ".$periode.
                                 " order by j_date");
        return $this->get_row_sql($Res);
    }
    /*!
     * \brief  Get data for accounting entry between 2 date
     *
     * \param  $p_from date from
     * \param  $p_to   end date
     *\param $let 0 means all rows, 1 only lettered, 2 only unlettered
	 * \param $solded 0 means all account, 1 means only accounts with a saldo <> 0
     *\note the data are filtered by the access of the current user
     * \return double array (j_date,deb_montant,cred_montant,description,jrn_name,j_debit,jr_internal)
     *         (tot_deb,tot_credit
     *
     */
    function get_row_date($p_from,$p_to,$let=0,$solded=0)
    {
        global $g_user;
        $filter_sql=$g_user->get_ledger_sql('ALL',3);
        $sql_let='';
        switch ($let)
        {
        case 0:
                break;
        case 1:
            $sql_let=' and j_id in (select j_id from letter_cred union select j_id from letter_deb)';
            break;
        case '2':
            $sql_let=' and j_id not in (select j_id from letter_cred union select j_id from letter_deb) ';
            break;
        }
	if ( $solded == 1)
	  {
	    $filter=str_replace('jrn_def_id','jr_def_id',$filter_sql);
	    $bal_sql="select sum(amount_deb) as s_deb,sum(amount_cred) as s_cred, j_poste
				from 						(select case when j_debit='t' then j_montant else 0 end as amount_deb,
								case when j_debit='f' then j_montant else 0 end as amount_cred,
								j_poste
								from jrnx join jrn on (j_grpt = jr_grpt_id)
								where
								j_poste=$1 and
								$filter and
								( to_date($2,'DD.MM.YYYY') <= j_date and
                                  to_date($3,'DD.MM.YYYY') >= j_date  )) as signed_amount
						group by j_poste
						";
	    $r=$this->db->get_array($bal_sql,array($this->id,$p_from,$p_to));
	    if ( $this->db->count() == 0 ) return array();
	    if ($r[0]['s_deb']==$r[0]['s_cred']) return array();
	  }
        $Res=$this->db->exec_sql("select  jr_id,to_char(j_date,'DD.MM.YYYY') as j_date_fmt,j_date,".
                                 "case when j_debit='t' then j_montant else 0 end as deb_montant,".
                                 "case when j_debit='f' then j_montant else 0 end as cred_montant,".
                                 " case when j_text is null or j_text = '' then jr_comment 
                                   else jr_comment||' '||j_text  end
                as description,jrn_def_name as jrn_name,".
                                 "j_debit, jr_internal,jr_pj_number,
								 coalesce(comptaproc.get_letter_jnt(j_id),-1) as letter ".
                                 ",pcm_lib ".
				 ",jr_tech_per,p_exercice,jrn_def_name,jrn_def_code".
                                 " from jrnx left join jrn_def on (jrn_def_id=j_jrn_def )".
                                 " left join jrn on (jr_grpt_id=j_grpt)".
                                 " left join tmp_pcmn on (j_poste=pcm_val)".
				 " left join parm_periode on (p_id=jr_tech_per) ".
                                 " where j_poste=$1 and ".
                                 " ( to_date($2,'DD.MM.YYYY') <= j_date and ".
                                 "   to_date($3,'DD.MM.YYYY') >= j_date )".
                                 " and $filter_sql  $sql_let ".
                                 " order by j_date,substring(jr_pj_number,'[0-9]+$') asc",array($this->id,$p_from,$p_to));
        return $this->get_row_sql($Res);
    }


    /*!\brief Return the name of a account
     *        it doesn't change any data member
     * \return string with the pcm_lib
     */
    function get_name()
    {
        $ret=$this->db->exec_sql(
                 "select pcm_lib from tmp_pcmn where
                 pcm_val=$1",array($this->id));
        if ( Database::num_row($ret) != 0)
        {
            $r=Database::fetch_array($ret);
            $this->name=$r['pcm_lib'];
        }
        else
        {
            $this->name="Poste inconnu";
        }
        return $this->name;
    }
    /*!\brief check if the poste exist in the tmp_pcmn
     *\return the number of line (normally 1 or 0)
     */
    function do_exist()
    {
        $sql="select pcm_val from tmp_pcmn where pcm_val= $1";
        $ret=$this->db->exec_sql($sql,array($this->id));
        return Database::num_row($ret) ;
    }
    /*!\brief Get all the value for this object from the database
     *        the data member are set
     * \return false if this account doesn't exist otherwise true
     */
    function load()
    {
        $ret=$this->db->exec_sql("select pcm_lib,pcm_val_parent from
                                 tmp_pcmn where pcm_val=$1",array($this->id));
        $r=Database::fetch_all($ret);

        if ( ! $r ) return false;
        $this->label=$r[0]['pcm_lib'];
        $this->parent=$r[0]['pcm_val_parent'];
        return true;

    }
    /*!\brief Get all the value for this object from the database
     *        the data member are set
     * \return false if this account doesn't exist otherwise true
     */
    function get()
    {
        echo "OBSOLETE Acc_Account_Ledger->get(), a remplacer par Acc_Account_Ledger->load()";
        return $this->load();
    }

    /*!
     * \brief  give the balance of an account
     *
     * \return
     *      balance of the account
     *
     */
    function get_solde($p_cond=" true ")
    {
        $Res=$this->db->exec_sql("select sum(deb) as sum_deb, sum(cred) as sum_cred from
                                 ( select j_poste,
                                 case when j_debit='t' then j_montant else 0 end as deb,
                                 case when j_debit='f' then j_montant else 0 end as cred
                                 from jrnx join tmp_pcmn on j_poste=pcm_val
                                 where
                                 j_poste::text like ('$this->id'::text) and
                                 $p_cond
                                 ) as m  ");
        $Max=Database::num_row($Res);
        if ($Max==0) return 0;
        $r=Database::fetch_array($Res,0);

        return abs($r['sum_deb']-$r['sum_cred']);
    }
    /*!
     * \brief   give the balance of an account
     * \return
     *      balance of the account
     *
     */
    function get_solde_detail($p_cond="")
    {

        if ( $p_cond != "") $p_cond=" and ".$p_cond;
        $sql="select sum(deb) as sum_deb, sum(cred) as sum_cred from
             ( select j_poste,
             case when j_debit='t' then j_montant else 0 end as deb,
             case when j_debit='f' then j_montant else 0 end as cred
             from jrnx
             where
             j_poste::text like ('$this->id'::text)
             $p_cond
             ) as m  ";

        $Res=$this->db->exec_sql($sql);
        $Max=Database::num_row($Res);

        if ($Max==0)
        {
            return array('debit'=>0,
                         'credit'=>0,
                         'solde'=>0)     ;
        }
        $r=Database::fetch_array($Res,0);
// if p_start is < p_end the query returns null to avoid any problem
// we set it to 0
        if ($r['sum_deb']=='')
            $r['sum_deb']=0.0;
        if ($r['sum_cred']=='')
            $r['sum_cred']=0.0;

        return array('debit'=>$r['sum_deb'],
                     'credit'=>$r['sum_cred'],
                     'solde'=>abs(bcsub($r['sum_deb'],$r['sum_cred'])));
    }
    /*!
     * \brief isTva tell is a poste is used for VAT
     * \param none
     *
     *
     * \return 1 is Yes otherwise 0
     */
    function isTVA()
    {
        // Load TVA array
        $a_TVA=$this->db->get_array('select tva_poste
                                    from tva_rate');
        foreach ( $a_TVA as $line_tva)
        {
            if ( $line_tva['tva_poste']  == '' )
                continue;
            list($tva_deb,$tva_cred)=explode(',',$line_tva['tva_poste']);
            if ( $this->id == $tva_deb ||
                    $this->id == $tva_cred )
            {
                return 1;
            }
        }
        return 0;

    }
    /*!
     * \brief HtmlTable, display a HTML of a poste for the asked period
     * \param $p_array array for filter
     * \param $let lettering of operation 0
     * \return -1 if nothing is found otherwise 0
     */
    function HtmlTable($p_array=null,$let=0 , $from_div=0)
    {
        if ( $p_array==null)$p_array=$_REQUEST;
        $this->get_name();
        list($array,$tot_deb,$tot_cred)=$this->get_row_date( $p_array['from_periode'],
							     $p_array['to_periode'],$let
                                                           );

        if ( count($this->row ) == 0 )
            return -1;

        $rep="";

        echo '<h2 class="title">'.$this->id." ".$this->name.'</h2>';
        if ( $from_div == 0)
			echo "<TABLE class=\"resultfooter\" style=\"border-collapse:separate;margin:1%;width:98%;;border-spacing:0px 5px\">";
		else
			echo "<TABLE class=\"resultfooter\" style=\"border-collapse:separate;margin:1%;width:98%;;border-spacing:0px 2px\">";
        echo '<tbody>';
        echo "<TR>".
        "<TH style=\"text-align:left\"> Date</TH>".
        "<TH style=\"text-align:left\"> n° de pièce </TH>".
        "<TH style=\"text-align:left\"> Code interne </TH>".
        "<TH style=\"text-align:left\"> Description </TH>".
        "<TH style=\"text-align:right\"> D&eacute;bit  </TH>".
        "<TH style=\"text-align:right\"> Cr&eacute;dit </TH>".
        th('Prog.','style="text-align:right"').
        th('Let.','style="text-align:right"');
        "</TR>"
        ;
        $progress=0;$sum_deb=0;$sum_cred=0;
	bcscale(2);
	$old_exercice="";
	$idx=0;
        foreach ( $this->row as $op )
        {
            $vw_operation = sprintf('<A class="detail" style="text-decoration:underline;color:red" HREF="javascript:modifyOperation(\'%s\',\'%s\')" >%s</A>', $op['jr_id'], dossier::id(), $op['jr_internal']);
            $let = '';
			$html_let = "";
			if ($op['letter'] != -1)
			{
				$let = strtoupper(base_convert($op['letter'], 10, 36));
				$html_let = HtmlInput::show_reconcile($from_div, $let);
			}
			$tmp_diff=bcsub($op['deb_montant'],$op['cred_montant']);

	    /*
	     * reset prog. balance to zero if we change of exercice
	     */
	    if ( $old_exercice != $op['p_exercice'])
	      {
		if ( $old_exercice != '')
		  {
		    $progress=bcsub($sum_deb,$sum_cred);
			$side="&nbsp;".$this->get_amount_side($progress);
		    echo "<TR class=\"highlight\">".
		      "<TD>$old_exercice</TD>".
		      td('').
		      "<TD></TD>".
		      "<TD>Totaux</TD>".
		      "<TD style=\"text-align:right\">".nbm($sum_deb)."</TD>".
		      "<TD style=\"text-align:right\">".nbm($sum_cred)."</TD>".
		      td(nbm(abs($progress)).$side,'style="text-align:right"').
		      td('').
		      "</TR>";
		    $sum_cred=0;
		    $sum_deb=0;
		    $progress=0;

		  }
	      }
	    $progress=bcadd($progress,$tmp_diff);
		$side="&nbsp;".$this->get_amount_side($progress);
	    $sum_cred=bcadd($sum_cred,$op['cred_montant']);
	    $sum_deb=bcadd($sum_deb,$op['deb_montant']);
		if ($idx%2 == 0) $class='class="odd"'; else $class=' class="even"';
		$idx++;

	    echo "<TR $class name=\"tr_" . $let . "_" . $from_div . "\">" .
			"<TD>".smaller_date(format_date($op['j_date']))."</TD>".
	      td(h($op['jr_pj_number'])).
	      "<TD>".$vw_operation."</TD>".
	      "<TD>".h($op['description'])."</TD>".
	      "<TD style=\"text-align:right\">".nbm($op['deb_montant'])."</TD>".
	      "<TD style=\"text-align:right\">".nbm($op['cred_montant'])."</TD>".
	      td(nbm(abs($progress)).$side,'style="text-align:right"').

	      td($html_let, ' style="color:red;text-align:right"') .
			"</TR>";
	    $old_exercice=$op['p_exercice'];
        }
        echo '<tfoot>';
        $solde_type=($sum_deb>$sum_cred)?"solde débiteur":"solde créditeur";
        $diff=bcsub($sum_deb,$sum_cred);
		$side="&nbsp;".$this->get_amount_side($diff);
        echo "<TR class=\"highlight\">".
        "<TD >Totaux</TD><td></td>".
        "<TD ></TD>".
        "<TD></TD>".
	  "<TD  style=\"text-align:right\">".nbm($sum_deb)."</TD>".
	  "<TD  style=\"text-align:right\">".nbm($sum_cred)."</TD>".
	  "<TD style=\"text-align:right\">".nbm(abs($diff)).$side."</TD>".

        "</TR>";
	echo   "<tr><TD>$solde_type</TD><td></td>".
	  "<TD style=\"text-align:right\">".nbm(abs($diff))."</TD>".
        "</TR>";
        echo '</tfoot>';
        echo '</tbody>';

        echo "</table>";

        return;
    }
	/**
	 * return the letter C if amount is > 0, D if < 0 or =
	 * @param type $p_amount
	 * @return string
	 */
	function get_amount_side($p_amount)
		{
			if ($p_amount == 0)
				return "=";
			if ($p_amount < 0)
				return "C";
			if ($p_amount > 0)
				return "D";
		}
    /*!
     * \brief Display HTML Table Header (button)
     *
     * \return none
     */
    static function HtmlTableHeader($actiontarget="poste")
    {
      switch($actiontarget)
	{
	case 'poste':
	  $action_csv='CSV:postedetail';
	  $action_pdf='PDF:postedetail';
	  break;
	case 'gl_comptes':
	  $action_csv='CSV:glcompte';
	  $action_pdf='PDF:glcompte';
	  break;
	default:
	  throw new Exception(" Fonction HtmlTableHeader argument actiontarget invalid");
	}
        $hid=new IHidden();

        echo "<table  >";
        echo '<TR>';
        $str_ople=(isset($_REQUEST['ople']))?HtmlInput::hidden('ople',$_REQUEST['ople']):'';
	if ($actiontarget=='poste')
	  {
	    echo '<TD><form method="GET" ACTION="">'.
	      dossier::hidden().
	      HtmlInput::submit('bt_other',"Autre poste").
	      $hid->input("type","poste").$hid->input('ac',$_REQUEST['ac'])."</form></TD>";
	  }


        echo '<TD><form method="GET" ACTION="export.php">'.
        dossier::hidden().
        HtmlInput::submit('bt_pdf',"Export PDF").
        HtmlInput::hidden('act',$action_pdf).
        $hid->input("type","poste").$str_ople.
        $hid->input('p_action','impress').
        $hid->input("from_periode",$_REQUEST['from_periode']).
        $hid->input("to_periode",$_REQUEST['to_periode'])
	  ;

	if ( isset($_REQUEST['letter'] )) echo HtmlInput::hidden('letter','2');
	if ( isset($_REQUEST['solded'] )) echo HtmlInput::hidden('solded','1');

	if (isset($_REQUEST['from_poste']))
	  echo HtmlInput::hidden('from_poste',$_REQUEST['from_poste']);

	if (isset($_REQUEST['to_poste']))
	  echo HtmlInput::hidden('to_poste',$_REQUEST['to_poste']);

        if (isset($_REQUEST['poste_id']))
	  echo HtmlInput::hidden("poste_id",$_REQUEST['poste_id']);

        if (isset($_REQUEST['poste_fille']))
            echo $hid->input('poste_fille','on');
        if (isset($_REQUEST['oper_detail']))
            echo $hid->input('oper_detail','on');

        echo "</form></TD>";

        echo '<TD><form method="GET" ACTION="export.php">'.
        dossier::hidden().
        HtmlInput::submit('bt_csv',"Export CSV").
	HtmlInput::hidden('act',$action_csv).
        $hid->input("type","poste").$str_ople.
        $hid->input('p_action','impress').
        $hid->input("from_periode",$_REQUEST['from_periode']).
	  $hid->input("to_periode",$_REQUEST['to_periode']);

	if (isset($_REQUEST['from_poste']))
	  echo HtmlInput::hidden('from_poste',$_REQUEST['from_poste']);

	if (isset($_REQUEST['to_poste']))
	  echo HtmlInput::hidden('to_poste',$_REQUEST['to_poste']);

        if (isset($_REQUEST['poste_id']))
	  echo HtmlInput::hidden("poste_id",$_REQUEST['poste_id']);

	if ( isset($_REQUEST['letter'] )) echo HtmlInput::hidden('letter','2');
	if ( isset($_REQUEST['solded'] )) echo HtmlInput::hidden('solded','1');

        if (isset($_REQUEST['poste_fille']))
            echo $hid->input('poste_fille','on');
        if (isset($_REQUEST['oper_detail']))
            echo $hid->input('oper_detail','on');
        if (isset($_REQUEST['poste_id'])) echo $hid->input("poste_id",$_REQUEST['poste_id']);

        echo "</form></TD>";
	echo "</form></TD>";
	echo '<td style="vertical-align:top">';
	echo HtmlInput::print_window();
	echo '</td>';
	echo '</tr>';
        echo "</table>";


    }
    /*!
     * \brief verify that the poste belong to a ledger
     *
     * \return 0 ok,  -1 no
     */
    function belong_ledger($p_jrn)
    {
        $filter=$this->db->get_value("select jrn_def_class_cred from jrn_def where jrn_def_id=$p_jrn");
        if ( trim ($filter) == '')
            return 0;

        $valid_cred=explode(" ",$filter);
        $sql="select count(*) as poste from tmp_pcmn where ";
        // Creation query
        $or="";
        $SqlFilter="";
        foreach ( $valid_cred as $item_cred)
        {
            if ( strlen (trim($item_cred)))
            {
                if ( strstr($item_cred,"*") == true )
                {
                    $item_cred=strtr($item_cred,"*","%");
                    $SqlItem="$or pcm_val::text like '".sql_string($item_cred)."'";
                    $or="  or ";
                }
                else
                {
                    $SqlItem="$or pcm_val::text = '".sql_string($item_cred)."' ";
                    $or="  or ";
                }
                $SqlFilter=$SqlFilter.$SqlItem;
            }
        }//foreach
        $sql.=$SqlFilter." and pcm_val::text='".sql_string($this->id)."'";
        $max=$this->db->get_value($sql);
        if ($max > 0 )
            return 0;
        else
            return -1;
    }
    /*!\brief With the id of the ledger, get the col jrn_def_class_deb
     *\param $p_jrn jrn_id
     *\return array of value, or an empty array if nothing is found
     *\note
     *\see
     */
    function get_account_ledger($p_jrn)
    {
        $l=new Acc_Ledger($this->db,$p_jrn);
        $row=$l->get_propertie();
        if ( strlen(trim($row['jrn_def_class_deb'])) == 0 ) return array();
        $valid_account=explode(" ",$row['jrn_def_class_deb']);
        return $valid_account;
    }
    /*!\brief build a sql statement thanks a array found with get_account_ledger
     *
     *\param $p_jrn jrn_id
     *\return an emty string if nothing is found or a valid SQL statement like
    \code
    pcm_val like ... or pcm_val like ...
    \endcode
     *\note
     *\see get_account_ledger
     */
    function build_sql_account($p_jrn)
    {
        $array=$this->get_account_ledger($p_jrn);
        if ( empty($array) ) return "";
        $sql="";
        foreach ( $array as $item_cred)
        {
            if ( strlen (trim($item_cred))>0 )
            {
                if ( strstr($item_cred,"*") == true )
                {
                    $item_cred=strtr($item_cred,"*","%");
                    $sql_tmp=" pcm_val::text like '$item_cred' or";
                }
                else
                {
                    $sql_tmp=" pcm_val::text = '$item_cred' or";
                }
                $sql.=$sql_tmp;
            }
        }//foreach
        /* remove the last or */
        $sql=substr($sql,0,strlen($sql)-2);
        return $sql;
    }
	/**
	 * Find the id of the cards which are using the current account
         * 
	 * @return an array of f_id
	 */
	function find_card()
	{
		$sql="select f_id from fiche_detail where ad_id=$1 and ad_value=$2";
		$account=$this->db->get_array($sql,array(ATTR_DEF_ACCOUNT,$this->id));
		return $account;
	}
    static function test_me()
    {
        $cn=new Database(dossier::id());
        $a=new Acc_Account_Ledger($cn,550);
        echo ' Journal 4 '.$a->belong_ledger(4);
        return $a->belong_ledger(4);;

    }
}
