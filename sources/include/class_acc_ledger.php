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
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_icard.php';
require_once NOALYSS_INCLUDE.'/class_ispan.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_idate.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_icheckbox.php';
require_once NOALYSS_INCLUDE.'/class_iperiod.php';
require_once NOALYSS_INCLUDE.'/class_fiche.php';
require_once NOALYSS_INCLUDE.'/class_user.php';
require_once  NOALYSS_INCLUDE.'/class_dossier.php';
require_once  NOALYSS_INCLUDE.'/class_anc_operation.php';
require_once  NOALYSS_INCLUDE.'/class_acc_operation.php';
require_once  NOALYSS_INCLUDE.'/class_acc_account_ledger.php';
require_once  NOALYSS_INCLUDE.'/class_pre_op_advanced.php';
require_once  NOALYSS_INCLUDE.'/class_acc_reconciliation.php';
require_once  NOALYSS_INCLUDE.'/class_periode.php';
require_once  NOALYSS_INCLUDE.'/class_gestion_purchase.php';
require_once  NOALYSS_INCLUDE.'/class_gestion_sold.php';
require_once  NOALYSS_INCLUDE.'/class_acc_account.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_inum.php';
require_once NOALYSS_INCLUDE.'/class_lettering.php';
require_once NOALYSS_INCLUDE.'/class_sort_table.php';
require_once NOALYSS_INCLUDE.'/class_jrn_def_sql.php';
require_once NOALYSS_INCLUDE.'/class_acc_payment.php';
/** \file
 * @brief Class for jrn,  class acc_ledger for manipulating the ledger
 */

/** @brief Class for jrn,  class acc_ledger for manipulating the ledger
 *
 */

class Acc_Ledger extends jrn_def_sql
{

	var $id;   /**< jrn_def.jrn_def_id */
	var $name;   /**< jrn_def.jrn_def_name */
	var $db;   /**< database connextion */
	var $row;   /**< row of the ledger */
	var $type;   /**< type of the ledger ACH ODS FIN
	  VEN or GL */
	var $nb;   /**< default number of rows by
	  default 10 */

	/**
	 * @param $p_cn database connexion
	 * @param $p_id jrn.jrn_def_id
	 */
	function __construct($p_cn, $p_id)
	{
		$this->id = $p_id;
		$this->name = &$this->jrn_def_name;
		$this->jrn_def_id = &$this->id;
		$this->db = $p_cn;
		$this->row = null;
		$this->nb = MAX_ARTICLE;
	}

	function get_last_pj()
	{
		if ($this->db->exist_sequence("s_jrn_pj" . $this->id))
		{
			$ret = $this->db->get_array("select last_value,is_called from s_jrn_pj" . $this->id);
			$last = $ret[0]['last_value'];
			/**
			 * \note  With PSQL sequence , the last_value column is 1 when before   AND after the first call, to make the difference between them
			 * I have to check whether the sequence has been already called or not */
			if ($ret[0]['is_called'] == 'f')
				$last--;
			return $last;
		}
		else
			$this->db->create_sequence("s_jrn_pj" . $this->id);
		return 0;
	}

	/**
	 * @brief Return the type of a ledger (ACH,VEN,ODS or FIN) or GL
	 *
	 */

	function get_type()
	{
		if ($this->id == 0)
		{
			$this->name = _(" Tous les journaux");
			$this->type = "GL";
			return "GL";
		}

		$Res = $this->db->exec_sql("select jrn_def_type from " .
				" jrn_def where jrn_def_id=" .
				$this->id);
		$Max = Database::num_row($Res);
		if ($Max == 0)
			return null;
		$ret = Database::fetch_array($Res, 0);
		$this->type = $ret['jrn_def_type'];
		return $ret['jrn_def_type'];
	}

	/**
	 * let you delete a operation
	 * @note by cascade it will delete also in
	 * - jrnx
	 * - stock
	 * - quant_purchase
	 * - quant_fin
	 * - quant_sold
	 * - operation_analytique
	 * - letter
	 * - reconciliation
	 * @bug the attached document is not deleted
         * @bug Normally it should be named delete_operation, cause the id is the ledger_id
         * (jrn_def_id) and not the operation id
	 */
	function delete()
	{
		if ($this->id == 0)
			return;
		$grpt_id = $this->db->get_value('select jr_grpt_id from jrn where jr_id=$1', array($this->jr_id));
		if ($this->db->count() == 0)
			return;
		$this->db->exec_sql('delete from jrnx where j_grpt=$1', array($grpt_id));
		$this->db->exec_sql('delete from jrn where jr_id=$1', array($this->jr_id));
	}

	/**
	 * Display warning contained in an array
	 * @return string with error message
	 */
	function display_warning($pa_msg, $p_warning)
	{
		$str = '<p class="notice"> ' . $p_warning;
		$str.="<ol class=\"notice\">";
		for ($i = 0; $i < count($pa_msg); $i++)
		{
			$str.="<li>" . $pa_msg[$i] . "</li>";
		}
		$str.='</ol>';
		$str.='</p>';
		return $str;
	}

	/**
	 * reverse the operation by creating the opposite one,
	 * the result is to avoid it
	 * it must be done in
	 * - jrn
	 * - jrnx
	 * - quant_fin
	 * - quant_sold
	 * - quant_purchase
	 * - stock
	 * - ANC
	 * @param $p_date is the date of the reversed op
	 * @exception if date is invalid or other prob
	 * @note automatically create a reconciliation between operation
	 * You must set the ledger_id $this->jrn_def_id
         * This function should be in operation or call an acc_operation object
         * 
	 */
	function reverse($p_date)
	{
		global $g_user;
		try
		{
			$this->db->start();
			if (!isset($this->jr_id) || $this->jr_id == '')
				throw new Exception(_("this->jr_id is not set ou opération inconnue"));

			/* check if the date is valid */
			if (isDate($p_date) == null)
				throw new Exception(_('Date invalide') . $p_date);

			// if the operation is in a closed or centralized period
			// the operation is voided thanks the opposite operation
			$grp_new = $this->db->get_next_seq('s_grpt');
			$seq = $this->db->get_next_seq("s_jrn");
			$p_internal = $this->compute_internal_code($seq);
			$this->jr_grpt_id = $this->db->get_value('select jr_grpt_id from jrn where jr_id=$1', array($this->jr_id));
			if ($this->db->count() == 0)
				throw new Exception(_("Cette opération n'existe pas"));
			$this->jr_internal = $this->db->get_value('select jr_internal from jrn where jr_id=$1', array($this->jr_id));
			if ($this->db->count() == 0 || trim($this->jr_internal) == '')
				throw new Exception(_("Cette opération n'existe pas"));

			/* find the periode thanks the date */
			$per = new Periode($this->db);
			$per->jrn_def_id = $this->id;
			$per->find_periode($p_date);

			if ($per->is_open() == 0)
				throw new Exception(_('PERIODE FERMEE'));





			// Mark the operation invalid into the ledger
			// to avoid to nullify twice the same op.
			$sql = "update jrn set jr_comment='extourne : '||jr_comment where jr_id=$1";
			$Res = $this->db->exec_sql($sql, array($this->jr_id));

			// Check return code
			if ($Res == false)
				throw new Exception(__FILE__ . __LINE__ . "sql a echoue [ $sql ]");

			//////////////////////////////////////////////////
			// Reverse in jrnx* tables
			//////////////////////////////////////////////////
			$a_jid = $this->db->get_array("select j_id,j_debit from jrnx where j_grpt=$1", array($this->jr_grpt_id));
			for ($l = 0; $l < count($a_jid); $l++)
			{
				$row = $a_jid[$l]['j_id'];
				// Make also the change into jrnx
				$sql = "insert into jrnx (
                  j_date,j_montant,j_poste,j_grpt,
                  j_jrn_def,j_debit,j_text,j_internal,j_tech_user,j_tech_per,j_qcode
                  ) select to_date($1,'DD.MM.YYYY'),j_montant,j_poste,$2,
                  j_jrn_def,not (j_debit),j_text,$3,$4,$5,
                  j_qcode
                  from
                  jrnx
                  where   j_id=$6 returning j_id";
				$Res = $this->db->exec_sql($sql, array($p_date, $grp_new, $p_internal, $g_user->id, $per->p_id, $row));
				// Check return code
				if ($Res == false)
					throw (new Exception(__FILE__ . __LINE__ . "SQL ERROR [ $sql ]"));
				$aj_id = $this->db->fetch(0);
				$j_id = $aj_id['j_id'];

				/* automatic lettering */
				$let = new Lettering($this->db);
				$let->insert_couple($j_id, $row);

				// reverse in QUANT_SOLD
				$Res = $this->db->exec_sql("INSERT INTO quant_sold(
                                     qs_internal, qs_fiche, qs_quantite, qs_price, qs_vat,
                                     qs_vat_code, qs_client, qs_valid, j_id)
                                     SELECT $1, qs_fiche, qs_quantite*(-1), qs_price*(-1), qs_vat*(-1),
                                     qs_vat_code, qs_client, qs_valid, $2
                                     FROM quant_sold where j_id=$3", array($p_internal, $j_id, $row));

				if ($Res == false)
					throw new Exception(__FILE__ . __LINE__ . "sql a echoue [ $sql ]");
				$Res = $this->db->exec_sql("INSERT INTO quant_purchase(
                                     qp_internal, j_id, qp_fiche, qp_quantite, qp_price, qp_vat,
                                     qp_vat_code, qp_nd_amount, qp_nd_tva, qp_nd_tva_recup, qp_supplier,
                                     qp_valid, qp_dep_priv)
                                     SELECT  $1, $2, qp_fiche, qp_quantite*(-1), qp_price*(-1), qp_vat*(-1),
                                     qp_vat_code, qp_nd_amount*(-1), qp_nd_tva*(-1), qp_nd_tva_recup*(-1), qp_supplier,
                                     qp_valid, qp_dep_priv*(-1)
                                     FROM quant_purchase where j_id=$3", array($p_internal, $j_id, $row));

				if ($Res == false)
					throw new Exception(__FILE__ . __LINE__ . "SQL ERROR [ $sql ]");
			}
			$sql = "insert into jrn (
              jr_id,
              jr_def_id,
              jr_montant,
              jr_comment,
              jr_date,
              jr_grpt_id,
              jr_internal
              ,jr_tech_per, jr_valid
              )
              select $1,jr_def_id,jr_montant,jr_comment,
              to_date($2,'DD.MM.YYYY'),$3,$4,
              $5, true
              from
              jrn
              where   jr_id=$6";
				$Res = $this->db->exec_sql($sql, array($seq, $p_date, $grp_new, $p_internal, $per->p_id, $this->jr_id));
				// Check return code
				if ($Res == false)
					throw (new Exception(__FILE__ . __LINE__ . "SQL ERROR [ $sql ]"));
			// reverse in QUANT_FIN table
			$Res = $this->db->exec_sql("  INSERT INTO quant_fin(
                                 qf_bank,  qf_other, qf_amount,jr_id)
                                 SELECT  qf_bank,  qf_other, qf_amount*(-1),$1
                                 FROM quant_fin where jr_id=$2", array($seq, $this->jr_id));
			if ($Res == false)
				throw (new Exception(__FILE__ . __LINE__ . "SQL ERROR[ $sql ]"));

			// Add a "concerned operation to bound these op.together
			//
        $rec = new Acc_Reconciliation($this->db);
			$rec->set_jr_id($seq);
			$rec->insert($this->jr_id);

			// Check return code
			if ($Res == false)
			{
				throw (new Exception(__FILE__ . __LINE__ . "SQL ERROR [ $sql ]"));
			}



			// the table stock must updated
			// also in the stock table
			$sql = "delete from stock_goods where sg_id = any ( select sg_id
             from stock_goods natural join jrnx  where j_grpt=" . $this->jr_grpt_id . ")";
			$Res = $this->db->exec_sql($sql);
			if ($Res == false)
				throw (new Exception(__FILE__ . __LINE__ . "SQL ERROR [ $sql ]"));
                        $this->db->commit();
		}
		catch (Exception $e)
		{
			$this->db->rollback();
			throw $e;
		}
	}

	/**
	 * @brief Return the name of a ledger
	 *
	 */

	function get_name()
	{
		if ($this->id == 0)
		{
			$this->name = _("Grand Livre");
			return $this->name;
		}

		$Res = $this->db->exec_sql("select jrn_def_name from " .
				" jrn_def where jrn_def_id=$1", array($this->id));
		$Max = Database::num_row($Res);
		if ($Max == 0)
			return null;
		$ret = Database::fetch_array($Res, 0);
		$this->name = $ret['jrn_def_name'];
		return $ret['jrn_def_name'];
	}

	/** \function  get_row
	 * @brief  Get The data
	 *
	 *
	 * @paramp_from from periode
	 * @paramp_to to periode
	 * @paramp_limit starting line
	 * @paramp_offset number of lines
	 * \return Array with the asked data
	 *
	 */

	function get_row($p_from, $p_to, $p_limit = -1, $p_offset = -1)
	{
		global $g_user;
		$periode = sql_filter_per($this->db, $p_from, $p_to, 'p_id', 'jr_tech_per');

		$cond_limite = ($p_limit != -1) ? " limit " . $p_limit . " offset " . $p_offset : "";
		// retrieve the type
		$this->get_type();
		// Grand livre == 0
		if ($this->id != 0)
		{
			$Res = $this->db->exec_sql("select jr_id,j_id,j_id as int_j_id,to_char(j_date,'DD.MM.YYYY') as j_date,
                                     jr_internal,
                                     case j_debit when 't' then j_montant::text else '   ' end as deb_montant,
                                     case j_debit when 'f' then j_montant::text else '   ' end as cred_montant,
                                     j_debit as debit,j_poste as poste,jr_montant , " .
					"case when j_text='' or j_text is null then pcm_lib else j_text end as description,j_grpt as grp,
                                     jr_comment||' ('||jr_internal||')'  as jr_comment,
				     jr_pj_number,
                                     j_qcode,
                                     jr_rapt as oc, j_tech_per as periode
                                     from jrnx left join jrn on " .
					"jr_grpt_id=j_grpt " .
					" left join tmp_pcmn on pcm_val=j_poste " .
					" where j_jrn_def=" . $this->id .
					" and " . $periode . " order by j_date::date asc,substring(jr_pj_number,'[0-9]+$')::numeric asc,j_grpt,j_debit desc " .
					$cond_limite);
		}
		else
		{
			$Res = $this->db->exec_sql("select jr_id,j_id,j_id as int_j_id,to_char(j_date,'DD.MM.YYYY') as j_date,
                                     jr_internal,
                                     case j_debit when 't' then j_montant::text else '   ' end as deb_montant,
                                     case j_debit when 'f' then j_montant::text else '   ' end as cred_montant,
                                     j_debit as debit,j_poste as poste," .
					"case when j_text='' or j_text is null then pcm_lib else j_text end as description,j_grpt as grp,
                                     jr_comment||' ('||jr_internal||')' as jr_comment,
				     jr_pj_number,
                                     jr_montant,
                                     j_qcode,
                                     jr_rapt as oc, j_tech_per as periode from jrnx left join jrn on " .
					"jr_grpt_id=j_grpt left join tmp_pcmn on pcm_val=j_poste
										 join jrn_def on (jr_def_id=jrn_def_id)
										 where " .
					$g_user->get_ledger_sql() . " and " .
					"  " . $periode . " order by j_date::date,substring(jr_pj_number,'[0-9]+$') asc,j_grpt,j_debit desc   " .
					$cond_limite);
		}


		$array = array();
		$Max = Database::num_row($Res);
		if ($Max == 0)
			return null;
		$case = "";
		$tot_deb = 0;
		$tot_cred = 0;
		$row = Database::fetch_all($Res);
		for ($i = 0; $i < $Max; $i++)
		{
			$fiche = new Fiche($this->db);
			$line = $row[$i];
			$mont_deb = ($line['deb_montant'] != 0) ? sprintf("% 8.2f", $line['deb_montant']) : "";
			$mont_cred = ($line['cred_montant'] != 0) ? sprintf("% 8.2f", $line['cred_montant']) : "";
			$jr_montant = ($line['jr_montant'] != 0) ? sprintf("% 8.2f", $line['jr_montant']) : "";
			$tot_deb+=$line['deb_montant'];
			$tot_cred+=$line['cred_montant'];
			$tot_op = $line['jr_montant'];

			/* Check first if there is a quickcode */
			if (strlen(trim($line['description'])) == 0 && strlen(trim($line['j_qcode'])) != 0)
			{
				if ($fiche->get_by_qcode($line['j_qcode'], false) == 0)
				{
					$line['description'] = $fiche->strAttribut(ATTR_DEF_NAME);
				}
			}
			if ($case != $line['grp'])
			{
				$case = $line['grp'];
				// for financial, we show if the amount is or not in negative
				if ($this->type == 'FIN')
				{
					$amount = $this->db->get_value('select qf_amount from quant_fin where jr_id=$1', array($line['jr_id']));
					/*  if nothing is found */
					if ($this->db->count() == 0)
						$tot_op = $jr_montant;
					else if ($amount < 0)
					{
						$tot_op = $amount;
					}
				}
				$array[] = array(
					'jr_id' => $line['jr_id'],
					'int_j_id' => $line['int_j_id'],
					'j_id' => $line['j_id'],
					'j_date' => $line['j_date'],
					'internal' => $line['jr_internal'],
					'deb_montant' => '',
					'cred_montant' => ' ',
					'description' => '<b><i>' . h($line['jr_comment']) . ' [' . $tot_op . '] </i></b>',
					'poste' => $line['oc'],
					'qcode' => $line['j_qcode'],
					'periode' => $line['periode'],
					'jr_pj_number' => $line ['jr_pj_number']);

				$array[] = array(
					'jr_id' => '',
					'int_j_id' => $line['int_j_id'],
					'j_id' => '',
					'j_date' => '',
					'internal' => '',
					'deb_montant' => $mont_deb,
					'cred_montant' => $mont_cred,
					'description' => $line['description'],
					'poste' => $line['poste'],
					'qcode' => $line['j_qcode'],
					'periode' => $line['periode'],
					'jr_pj_number' => ''
				);
			}
			else
			{
				$array[] = array(
					'jr_id' => $line['jr_id'],
					'int_j_id' => $line['int_j_id'],
					'j_id' => '',
					'j_date' => '',
					'internal' => '',
					'deb_montant' => $mont_deb,
					'cred_montant' => $mont_cred,
					'description' => $line['description'],
					'poste' => $line['poste'],
					'qcode' => $line['j_qcode'],
					'periode' => $line['periode'],
					'jr_pj_number' => '');
			}
		}
		$this->row = $array;
		$a = array($array, $tot_deb, $tot_cred);
		return $a;
	}

	/** @brief  Get simplified row from ledger
	 *
	 * @param p_from periode
	 * @param p_to periode
	 * @param p_limit starting line
	 * @param p_offset number of lines
	 * @param trunc if data must be truncated (pdf export)
	 *
	 * \return an Array with the asked data
	 */

	function get_rowSimple($p_from, $p_to, $trunc = 0, $p_limit = -1, $p_offset = -1)
	{
		global $g_user;
		// Grand-livre : id= 0
		//---
		$jrn = ($this->id == 0 ) ? "and " . $g_user->get_ledger_sql() : "and jrn_def_id = " . $this->id;

		$periode = sql_filter_per($this->db, $p_from, $p_to, 'p_id', 'jr_tech_per');

		$cond_limite = ($p_limit != -1) ? " limit " . $p_limit . " offset " . $p_offset : "";
		//---
		$sql = "
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
             WHERE $periode $jrn order by jr_date,substring(jrn.jr_pj_number,'[0-9]+$')::numeric asc  $cond_limite";

		$Res = $this->db->exec_sql($sql);
		$Max = Database::num_row($Res);
		if ($Max == 0)
		{
			return null;
		}
		$type = $this->get_type();
		// for type ACH and Ven we take more info
		if ($type == 'ACH' || $type == 'VEN')
		{
			$a_ParmCode = $this->db->get_array('select p_code,p_value from parm_code');
			$a_TVA = $this->db->get_array('select tva_id,tva_label,tva_poste
                                        from tva_rate where tva_rate != 0 order by tva_rate,tva_label,tva_id ');
			for ($i = 0; $i < $Max; $i++)
			{
				$array[$i] = Database::fetch_array($Res, $i);
				$p = $this->get_detail($array[$i], $type, $trunc, $a_TVA, $a_ParmCode);
				if ($array[$i]['dep_priv'] != 0.0)
				{
					$array[$i]['comment'].="(priv. " . $array[$i]['dep_priv'] . ")";
				}
			}
		}
		else
		{
			$array = Database::fetch_all($Res);
		}

		return $array;
	}

// end function get_rowSimple

	/**
	 * @brief guess what  the next pj should be
	 */

	function guess_pj()
	{
		$prop = $this->get_propertie();
		$pj_pref = $prop["jrn_def_pj_pref"];
		$pj_seq = $this->get_last_pj() + 1;
		return $pj_pref . $pj_seq;
	}

	/**
	 * @brief Show all the operation
	 * @param$sql is the sql stmt, normally created by build_search_sql
	 * @param$offset the offset
	 * @param$p_paid if we want to see info about payment
	  @code
	  // Example
	  // Build the sql
	  list($sql,$where)=$Ledger->build_search_sql($_GET);
	  // Count nb of line
	  $max_line=$this->db->count_sql($sql);

	  $step=$_SESSION['g_pagesize'];
	  $page=(isset($_GET['offset']))?$_GET['page']:1;
	  $offset=(isset($_GET['offset']))?$_GET['offset']:0;
	  // create the nav. bar
	  $bar=navigation_bar($offset,$max_line,$step,$page);
	  // show a part
	  list($count,$html)= $Ledger->list_operation($sql,$offset,0);
	  echo $html;
	  // show nav bar
	  echo $bar;

	  @endcode
	 * @see build_search_sql
	 * @see display_search_form
	 * @see search_form

	 * @return HTML string
	 */

	public function list_operation_to_reconcile($sql,$p_target)
	{
		global $g_parameter, $g_user;
		$gDossier = dossier::id();
		$limit = " LIMIT ".MAX_RECONCILE;
		// Sort
		// Count
		$count = $this->db->count_sql($sql);
		// Add the limit
		$sql.=" order by jr_date asc " . $limit;

		// Execute SQL stmt
		$Res = $this->db->exec_sql($sql);

		//starting from here we can refactor, so that instead of returning the generated HTML,
		//this function returns a tree structure.

		$r = "";


		$Max = Database::num_row($Res);

		if ($Max == 0)
			return array(0, _("Aucun enregistrement trouvé"));
                $r.=HtmlInput::hidden("target", $p_target);
		$r.='<table class="result">';


		$r.="<tr >";
		$r.="<th>"._("Selection")."</th>";
		$r.="<th>"._("Internal")."</th>";

		if ($this->type == 'ALL')
		{
			$r.=th(_('Journal'));
		}

		$r.='<th>'._("Date").'</th>';
		$r.='<th>'._("Pièce").'</td>';
		$r.=th(_('tiers'));
		$r.='<th>'._("Description").'</th>';
		$r.=th(_('Notes'), ' ');
		$r.='<th>'._("Montant").'</th>';
		$r.="<th>" . _('Concerne') . "</th>";
		$r.="</tr>";
		// Total Amount
		$tot = 0.0;
		$gDossier = dossier::id();
		$str_dossier = Dossier::id();
		for ($i = 0; $i < $Max; $i++)
		{


			$row = Database::fetch_array($Res, $i);

			if ($i % 2 == 0)
				$tr = '<TR class="odd">';
			else
				$tr = '<TR class="even">';
			$r.=$tr;
			// Radiobox
			//

			$r.='<td><INPUT TYPE="CHECKBOX" name="jr_concerned' . $row['jr_id'] . '" ID="jr_concerned' . $row['jr_id'] . '" value="'.$row['quick_code'].'"> </td>';
			//internal code
			// button  modify
			$r.="<TD>";
			// If url contains
			//

            $href = basename($_SERVER['PHP_SELF']);


			$r.=sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:modifyOperation(\'%s\',\'%s\')" >%s </A>', $row['jr_id'], $gDossier, $row['jr_internal']);
			$r.="</TD>";
			if ($this->type == 'ALL')
				$r.=td($row['jrn_def_name']);
			// date
			$r.="<TD>";
			$r.=$row['str_jr_date'];
			$r.="</TD>";

			// pj
			$r.="<TD>";
			$r.=$row['jr_pj_number'];
			$r.="</TD>";

			// Tiers
			$other = ($row['quick_code'] != '') ? '[' . $row['quick_code'] . '] ' . $row['name'] . ' ' . $row['first_name'] : '';
			$r.=td($other);
			// comment
			$r.="<TD>";
			$tmp_jr_comment = h($row['jr_comment']);
			$r.=$tmp_jr_comment;
			$r.="</TD>";
			$r.=td(h($row['n_text']), ' style="font-size:0.87em"');
			// Amount
			// If the ledger is financial :
			// the credit must be negative and written in red
			$positive = 0;

			// Check ledger type :
			if ($row['jrn_def_type'] == 'FIN')
			{
				$positive = $this->db->get_value("select qf_amount from quant_fin where jr_id=$1", array($row['jr_id']));
				if ($this->db->count() != 0)
					$positive = ($positive < 0) ? 1 : 0;
			}
			$r.="<TD align=\"right\">";

			$r.=( $positive != 0 ) ? "<font color=\"red\">  - " . nbm($row['jr_montant']) . "</font>" : nbm($row['jr_montant']);
			$r.="</TD>";



			// Rapprochement
			$rec = new Acc_Reconciliation($this->db);
			$rec->set_jr_id($row['jr_id']);
			$a = $rec->get();
			$r.="<TD>";
			if ($a != null)
			{

				foreach ($a as $key => $element)
				{
					$operation = new Acc_Operation($this->db);
					$operation->jr_id = $element;
					$l_amount = $this->db->get_value("select jr_montant from jrn " .
							" where jr_id=$element");
					$r.= "<A class=\"detail\" HREF=\"javascript:modifyOperation('" . $element . "'," . $gDossier . ")\" > " . $operation->get_internal() . "[" . nbm($l_amount) . "]</A>";
				}//for
			}// if ( $a != null ) {
			$r.="</TD>";

			if ($row['jr_valid'] == 'f')
			{
				$r.="<TD>"._("Opération annulée")."</TD>";
			}
			// end row
			$r.="</tr>";
		}
		$r.='</table>';
		return array($count, $r);
	}

	/**
	 * @brief Show all the operation
	 * @param$sql is the sql stmt, normally created by build_search_sql
	 * @param$offset the offset
	 * @param$p_paid if we want to see info about payment
	  \code
	  // Example
	  // Build the sql
	  list($sql,$where)=$Ledger->build_search_sql($_GET);
	  // Count nb of line
	  $max_line=$cn->count_sql($sql);

	  $step=$_SESSION['g_pagesize'];
	  $page=(isset($_GET['offset']))?$_GET['page']:1;
	  $offset=(isset($_GET['offset']))?$_GET['offset']:0;
	  // create the nav. bar
	  $bar=navigation_bar($offset,$max_line,$step,$page);
	  // show a part
	  list($count,$html)= $Ledger->list_operation($sql,$offset,0);
	  echo $html;
	  // show nav bar
	  echo $bar;

	  \endcode
	 * \see build_search_sql
	 * \see display_search_form
	 * \see search_form

	 * \return HTML string
	 */

	public function list_operation($sql, $offset, $p_paid = 0)
	{
		global $g_parameter, $g_user;
		bcscale(2);
		$table = new Sort_Table();
		$gDossier = dossier::id();
		$amount_paid = 0.0;
		$amount_unpaid = 0.0;
		$limit = ($_SESSION['g_pagesize'] != -1) ? " LIMIT " . $_SESSION['g_pagesize'] : "";
		$offset = ($_SESSION['g_pagesize'] != -1) ? " OFFSET " . Database::escape_string($offset) : "";
		$order = "  order by jr_date_order asc,jr_internal asc";
		// Sort
		$url = "?" . CleanUrl();
		$str_dossier = dossier::get();
		$table->add(_("Date"), $url, 'order by jr_date asc,substring(jr_pj_number,\'[0-9]+$\')::numeric asc', 'order by  jr_date desc,substring(jr_pj_number,\'[0-9]+$\')::numeric desc', "da", "dd");
		$table->add(_('Echeance'), $url, " order by  jr_ech asc", " order by  jr_ech desc", 'ea', 'ed');
		$table->add(_('Paiement'), $url, " order by  jr_date_paid asc", " order by  jr_date_paid desc", 'eap', 'edp');
		$table->add(_('Pièce'), $url, ' order by  substring(jr_pj_number,\'[0-9]+$\')::numeric asc ', ' order by  substring(jr_pj_number,\'[0-9]+$\')::numeric desc ', "pja", "pjd");
		$table->add(_('Tiers'), $url, " order by  name asc", " order by  name desc", 'na', 'nd');
		$table->add(_('Montant'), $url, " order by jr_montant asc", " order by jr_montant desc", "ma", "md");
		$table->add(_("Description"), $url, "order by jr_comment asc", "order by jr_comment desc", "ca", "cd");

		$ord = (!isset($_GET['ord'])) ? 'da' : $_GET['ord'];
		$order = $table->get_sql_order($ord);

		// Count
		$count = $this->db->count_sql($sql);
		// Add the limit
		$sql.=$order . $limit . $offset;
		// Execute SQL stmt
		$Res = $this->db->exec_sql($sql);

		//starting from here we can refactor, so that instead of returning the generated HTML,
		//this function returns a tree structure.

		$r = "";


		$Max = Database::num_row($Res);

		if ($Max == 0)
			return array(0, _("Aucun enregistrement trouvé"));

		$r.='<table class="result">';


		$r.="<tr >";
		$r.="<th>"._("n° interne")."</th>";
		if ($this->type == 'ALL')
		{
			$r.=th('Journal');
		}
		$r.='<th>' . $table->get_header(0) . '</th>';
		if ($p_paid != 0 ) $r.='<th>' . $table->get_header(1) . '</td>';
		if ($p_paid != 0 ) $r.='<th>' . $table->get_header(2) . '</th>';
		$r.='<th>' . $table->get_header(3) . '</th>';
		$r.='<th>' . $table->get_header(4) . '</th>';
		$r.='<th>' . $table->get_header(6) . '</th>';
		$r.=th('Notes', ' style="width:15%"');
		$r.='<th>' . $table->get_header(5) . '</th>';
		// if $p_paid is not equal to 0 then we have a paid column
		if ($p_paid != 0)
		{
			$r.="<th> " . _('Payé') . "</th>";
		}
		$r.="<th>" . _('Concerne') . "</th>";
		$r.="<th>" . _('Document') . "</th>";
		$r.="</tr>";
		// Total Amount
		$tot = 0.0;
		$gDossier = dossier::id();
		for ($i = 0; $i < $Max; $i++)
		{


			$row = Database::fetch_array($Res, $i);

			if ($i % 2 == 0)
				$tr = '<TR class="odd">';
			else
				$tr = '<TR class="even">';
			$r.=$tr;
			//internal code
			// button  modify
			$r.="<TD>";
			// If url contains
			//

            $href = basename($_SERVER['PHP_SELF']);


			$r.=sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:modifyOperation(\'%s\',\'%s\')" >%s </A>', $row['jr_id'], $gDossier, $row['jr_internal']);
			$r.="</TD>";
			if ($this->type == 'ALL')
				$r.=td($row['jrn_def_name']);
			// date
			$r.="<TD>";
			$r.=$row['str_jr_date'];
			$r.="</TD>";
			// echeance
			if ($p_paid != 0 )
                        {
                            $r.="<TD>";
                            $r.=$row['str_jr_ech'];
                            $r.="</TD>";
                            $r.="<TD>";
                            $r.=$row['str_jr_date_paid'];
                            $r.="</TD>";
                        }

			// pj
			$r.="<TD>";
			$r.=$row['jr_pj_number'];
			$r.="</TD>";

			// Tiers
			$other = ($row['quick_code'] != '') ? '[' . $row['quick_code'] . '] ' . $row['name'] . ' ' . $row['first_name'] : '';
			$r.=td($other);
			// comment
			$r.="<TD>";
			$tmp_jr_comment = h($row['jr_comment']);
			$r.=$tmp_jr_comment;
			$r.="</TD>";
			$r.=td(h($row['n_text']), ' style="font-size:0.87em%"');
			// Amount
			// If the ledger is financial :
			// the credit must be negative and written in red
			$positive = 0;

			// Check ledger type :
			if ($row['jrn_def_type'] == 'FIN')
			{
				$positive = $this->db->get_value("select qf_amount from quant_fin where jr_id=$1", array($row['jr_id']));
				if ($this->db->count() != 0)
					$positive = ($positive < 0) ? 1 : 0;
			}
			$r.="<TD align=\"right\">";
			$t_amount=$row['jr_montant'];
			if ($row['total_invoice'] != null && $row['total_invoice'] != $row['jr_montant'])
				$t_amount=$row['total_invoice'];
			$tot = ($positive != 0) ? bcsub($tot , $t_amount ): bcadd($tot , $t_amount);
			//STAN $positive always == 0
			if ($row [ 'jrn_def_type']=='FIN')
			{
				$r.=( $positive != 0 ) ? "<font color=\"red\">  - " . nbm($t_amount) . "</font>" : nbm($t_amount);
			}
			else
			{
				$r.=( $t_amount <  0 ) ? "<font color=\"red\">  " . nbm($t_amount) . "</font>" : nbm($t_amount);
			}
			$r.="</TD>";


			// Show the paid column if p_paid is not null
			if ($p_paid != 0)
			{
				$w = new ICheckBox();
				$w->name = "rd_paid" . $row['jr_id'];
				$w->selected = ($row['jr_rapt'] == 'paid') ? true : false;
				// if p_paid == 2 then readonly
				$w->readonly = ( $p_paid == 2) ? true : false;
				$h = new IHidden();
				$h->name = "set_jr_id" . $row['jr_id'];
				$r.='<TD>' . $w->input() . $h->input() . '</TD>';
				if ($row['jr_rapt'] == 'paid')
					$amount_paid=bcadd($amount_paid,$t_amount);
				else
					$amount_unpaid=bcadd($amount_unpaid,$t_amount);
			}

			// Rapprochement
			$rec = new Acc_Reconciliation($this->db);
			$rec->set_jr_id($row['jr_id']);
			$a = $rec->get();
			$r.="<TD>";
			if ($a != null)
			{

				foreach ($a as $key => $element)
				{
					$operation = new Acc_Operation($this->db);
					$operation->jr_id = $element;
					$l_amount = $this->db->get_value("select jr_montant from jrn " .
							" where jr_id=$element");
					$r.= "<A class=\"detail\" HREF=\"javascript:modifyOperation('" . $element . "'," . $gDossier . ")\" > " . $operation->get_internal() . "[" . nbm($l_amount) . "]</A>";
				}//for
			}// if ( $a != null ) {
			$r.="</TD>";

			if ($row['jr_valid'] == 'f')
			{
				$r.="<TD>"._("Opération annulée")."</TD>";
			}
			else
			{

			} // else
			//document
			if ($row['jr_pj_name'] != "")
			{
                            $r.='<td>'.HtmlInput::show_receipt_document($row['jr_id']).'</td>';
			}
			else
				$r.="<TD></TD>";

			// end row
			$r.="</tr>";
		}
		$amount_paid = round($amount_paid, 4);
		$amount_unpaid = round($amount_unpaid, 4);
		$tot = round($tot, 4);
		$r.="<TR>";
		$r.='<TD COLSPAN="5">Total</TD>';
		$r.='<TD ALIGN="RIGHT">' . nbm($tot) . "</TD>";
		$r.="</tr>";
		if ($p_paid != 0)
		{
			$r.="<TR>";
			$r.='<TD COLSPAN="5">'._("Payé").'</TD>';
			$r.='<TD ALIGN="RIGHT">' . nbm($amount_paid) . "</TD>";
			$r.="</tr>";
			$r.="<TR>";
			$r.='<TD COLSPAN="5">'._("Non payé").'</TD>';
			$r.='<TD ALIGN="RIGHT">' . nbm($amount_unpaid) . "</TD>";
			$r.="</tr>";
		}
		$r.="</table>";

		return array($count, $r);
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
	 * @param $trunc if the data must be truncated, usefull for pdf export
	 * @paramp_jrn_type is the type of the ledger (ACH or VEN)
	 * @param $a_TVA TVA Array (default null)
	 * @param $a_ParmCode Array (default null)
	 * \return p_array
	 */

	function get_detail(&$p_array, $p_jrn_type, $trunc = 0, $a_TVA = null, $a_ParmCode = null)
	{
            bcscale(2);
            
		if ($a_TVA == null)
		{
			//Load TVA array
			$a_TVA = $this->db->get_array('select tva_id,tva_label,tva_poste
                                        from tva_rate where tva_rate != 0 order by tva_rate,tva_label,tva_id');
		}
		if ($a_ParmCode == null)
		{
			//Load Parm_code
			$a_ParmCode = $this->db->get_array('select p_code,p_value from parm_code');
		}
		// init
		$p_array['client'] = "";
		$p_array['TVAC'] = 0;
		$p_array['TVA'] = array();
		$p_array['AMOUNT_TVA'] = 0.0;
		$p_array['dep_priv'] = 0;
		$p_array['dna'] = 0;
		$p_array['tva_dna'] = 0;
		$p_array['tva_np'] = 0;
		$dep_priv = 0.0;
                
		//
		// Retrieve data from jrnx
		$sql = "select j_id,j_poste,j_montant, j_debit,j_qcode from jrnx where " .
				" j_grpt=" . $p_array['grpt_id'];
		$Res2 = $this->db->exec_sql($sql);
		$data_jrnx = Database::fetch_all($Res2);
		$c = 0;

		// Parse data from jrnx and fill diff. field
		foreach ($data_jrnx as $code)
		{
			$idx_tva = 0;
			$poste = new Acc_Account_Ledger($this->db, $code['j_poste']);

			// if card retrieve name if the account is not a VAT account
			if (strlen(trim($code['j_qcode'])) != 0 && $poste->isTva() == 0)
			{
				$fiche = new Fiche($this->db);
				$fiche->get_by_qcode(trim($code['j_qcode']), false);
				$fiche_def_id = $fiche->get_fiche_def_ref_id();
				// Customer or supplier
				if ($fiche_def_id == FICHE_TYPE_CLIENT ||
						$fiche_def_id == FICHE_TYPE_FOURNISSEUR
                                        ||$fiche_def_id == FICHE_TYPE_ADM_TAX)
				{
					$p_array['TVAC'] = $code['j_montant'];

					$p_array['client'] = ($trunc == 0) ? $fiche->getName() : mb_substr($fiche->getName(), 0, 20);
					$p_array['reversed'] = false;
					if ($fiche_def_id == FICHE_TYPE_CLIENT && $code['j_debit'] == 'f')
					{
						$p_array['reversed'] = true;
						$p_array['TVAC']*=-1;
					}
					if ($fiche_def_id == FICHE_TYPE_ADM_TAX && $code['j_debit'] == 'f')
					{
						$p_array['reversed'] = true;
						$p_array['TVAC']*=-1;
					}
					if ($fiche_def_id == FICHE_TYPE_FOURNISSEUR && $code['j_debit'] == 't')
					{
						$p_array['reversed'] = true;
						$p_array['TVAC']*=-1;
					}
				}
				else
				{
					// if we use the ledger ven / ach for others card than supplier and customer
					if ($fiche_def_id != FICHE_TYPE_VENTE &&
							$fiche_def_id != FICHE_TYPE_ACH_MAR &&
							$fiche_def_id != FICHE_TYPE_ACH_SER  &&
							$fiche_def_id != FICHE_TYPE_ACH_MAT
                                           )                                                
					{
						$p_array['TVAC'] = $code['j_montant'];

						$p_array['client'] = ($trunc == 0) ? $fiche->getName() : mb_substr($fiche->getName(), 0, 20);
						$p_array['reversed'] = false;
						if ($p_jrn_type == 'ACH' && $code['j_debit'] == 't')
						{
							$p_array['reversed'] = true;
							$p_array['TVAC']*=-1;
						}
						if ($p_jrn_type == 'VEN' && $code['j_debit'] == 'f')
						{
							$p_array['reversed'] = true;
							$p_array['TVAC']*=-1;
						}
					}
				}
			}
			// if TVA, load amount, tva id and rate in array
			foreach ($a_TVA as $line_tva)
			{
				list($tva_deb, $tva_cred) = explode(',', $line_tva['tva_poste']);
				if ($code['j_poste'] == $tva_deb ||
						$code['j_poste'] == $tva_cred)
				{

					// For the reversed operation
					if ($p_jrn_type == 'ACH' && $code['j_debit'] == 'f')
					{
						$code['j_montant'] = -1 * $code['j_montant'];
					}
					if ($p_jrn_type == 'VEN' && $code['j_debit'] == 't')
					{
						$code['j_montant'] = -1 * $code['j_montant'];
					}

					$p_array['AMOUNT_TVA']+=$code['j_montant'];

					$p_array['TVA'][$c] = array($idx_tva, array($line_tva['tva_id'], $line_tva['tva_label'], $code['j_montant']));
					$c++;

					$idx_tva++;
				}
			}

			// isDNA
			// If operation is reversed then  amount are negatif
			/* if ND */
			if ($p_array['jrn_def_type'] == 'ACH')
			{
				$purchase = new Gestion_Purchase($this->db);
				$purchase->search_by_jid($code['j_id']);
				$purchase->load();
				$dep_priv+=$purchase->qp_dep_priv;
				$p_array['dep_priv'] = $dep_priv;
                                $p_array['dna']=bcadd($p_array['dna'],$purchase->qp_nd_amount);
                                $p_array['tva_dna']=bcadd($p_array['tva_dna'],bcadd($purchase->qp_nd_tva,$purchase->qp_nd_tva_recup));
                                $p_array['tva_np']=bcadd($purchase->qp_vat_sided,$p_array['tva_np']);
			}
			if ($p_array['jrn_def_type'] == 'VEN') {
                            $sold=new gestion_sold($this->db);
                            $sold->search_by_jid($code['j_id']);
                            $sold->load();
                            $p_array['tva_np']=bcadd($sold->qs_vat_sided,$p_array['tva_np']);
                        }
                        
                        
		}
		$p_array['TVAC'] = sprintf('% 10.2f', $p_array['TVAC'] );
		$p_array['HTVA'] = sprintf('% 10.2f', $p_array['TVAC'] - $p_array['AMOUNT_TVA']-$p_array['tva_dna']);
		$r = "";
		$a_tva_amount = array();
		// inline TVA (used for the PDF)
		foreach ($p_array['TVA'] as $linetva)
		{
			foreach ($a_TVA as $tva)
			{
				if ($tva['tva_id'] == $linetva[1][0])
				{
					$a = $tva['tva_id'];
					$a_tva_amount[$a] = $linetva[1][2];
				}
			}
		}
		foreach ($a_TVA as $line_tva)
		{
			$a = $line_tva['tva_id'];
			if (isset($a_tva_amount[$a]))
			{
				$tmp = sprintf("% 10.2f", $a_tva_amount[$a]);
				$r.="$tmp";
			}
			else
				$r.=sprintf("% 10.2f", 0);
		}
		$p_array['TVA_INLINE'] = $r;

		return $p_array;
	}

// retrieve data from jrnx
	/**
	 * @brief  Get the properties of a journal
	 *
	 * \return an array containing properties
	 *
	 */

	function get_propertie()
	{
		if ($this->id == 0)
			return;

		$Res = $this->db->exec_sql("select jrn_Def_id,jrn_def_name,jrn_def_class_deb,jrn_def_class_cred,jrn_def_type,
                                 jrn_deb_max_line,jrn_cred_max_line,jrn_def_ech,jrn_def_ech_lib,jrn_def_code,
                                 jrn_def_fiche_deb,jrn_def_fiche_cred,jrn_def_pj_pref
                                 from jrn_Def
                                 where jrn_def_id=$1", array($this->id));
		$Count = Database::num_row($Res);
		if ($Count == 0)
		{
			echo '<DIV="redcontent"><H2 class="error">' . _('Parametres journaux non trouves') . '</H2> </DIV>';
			return null;
		}
		return Database::fetch_array($Res, 0);
	}

	/** \function GetDefLine
	 * @brief Get the number of lines of a journal
	 * @param$p_cred deb or cred
	 *
	 * \return an integer
	 */

	function GetDefLine()
	{
		$sql_cred = 'jrn_deb_max_line';
		$sql = "select jrn_deb_max_line as value from jrn_def where jrn_def_id=$1";
		$r = $this->db->exec_sql($sql, array($this->id));
		$Res = Database::fetch_all($r);
		if (sizeof($Res) == 0)
			return 1;
		return $Res[0]['value'];
	}

	/**
	 * @brief get the saldo of a ledger for a specific period
	 * @param$p_from start period
	 * @param$p_to end period
	 */

	function get_solde($p_from, $p_to)
	{
		$ledger = "";
		if ($this->id != 0)
		{
			$ledger = " and j_jrn_def = " . $this->id;
		}

		$periode = sql_filter_per($this->db, $p_from, $p_to, 'p_id', 'j_tech_per');
		$sql = 'select j_montant as montant,j_debit as deb from jrnx where '
				. $periode . $ledger;

		$ret = $this->db->exec_sql($sql);
		$array = Database::fetch_all($ret);
		$deb = 0.0;
		$cred = 0.0;
		foreach ($array as $line)
		{

			if ($line['deb'] == 't')
				$deb+=$line['montant'];
			else
				$cred+=$line['montant'];
		}
		$response = array($deb, $cred);
		return $response;
	}

	/**
	 * @brief Show a select list   of the ledgers you can access in
	 * writing, reading or simply accessing.
	 * @param$p_type = ALL or the type of the ledger (ACH,VEN,FIN,ODS)
	 * @param$p_access =3 for READ and WRITE, 2 for write and 1 for readonly
	 * \return     object HtmlInput select
	 */

	function select_ledger($p_type = "ALL", $p_access = 3)
	{
		global $g_user;
		$array = $g_user->get_ledger($p_type, $p_access);

		if ($array == null)
			return null;
		$idx = 0;
		$ret = array();

		foreach ($array as $value)
		{
			$ret[$idx]['value'] = $value['jrn_def_id'];
			$ret[$idx]['label'] = h($value['jrn_def_name']);
			$idx++;
		}

		$select = new ISelect();
		$select->name = 'p_jrn';
		$select->value = $ret;
		$select->selected = $this->id;
		return $select;
	}

	/**
	 * @brief retrieve the jrn_def_fiche and return them into a array
	 *        index deb, cred
	 * \param
	 * \param
	 * \param
	 *
	 *
	 * \return return an array ('deb'=> ,'cred'=>)
	 */

	function get_fiche_def()
	{
		$sql = "select jrn_def_fiche_deb as deb,jrn_def_fiche_cred as cred " .
				" from jrn_def where " .
				" jrn_def_id = $1 ";

		$r = $this->db->exec_sql($sql, array($this->id));

		$res = Database::fetch_all($r);
		if (empty($res))
			return null;

		return $res[0];
	}

	/**
	 * @brief retrieve the jrn_def_class_deb and return it
	 *
	 *
	 * \return return an string
	 */

	function get_class_def()
	{
		$sql = "select jrn_def_class_deb  " .
				" from jrn_def where " .
				" jrn_def_id = $1";

		$r = $this->db->exec_sql($sql, array($this->id));

		$res = Database::fetch_all($r);

		if (empty($res))
			return null;

		return $res[0];
	}

	/**
	 * @brief show the result of the array to confirm
	 * before inserting
	 * @param$p_array array from the form
	 * \return string
	 */

	function confirm($p_array, $p_readonly = false)
	{
		global $g_parameter;
		$msg = array();
		if (!$p_readonly)
			$msg = $this->verify($p_array);
		$this->id = $p_array['p_jrn'];
		if (empty($p_array))
			return 'Aucun r&eacute;sultat';
		$anc = null;
		extract($p_array);
		$lPeriode = new Periode($this->db);
		if ($this->check_periode() == true)
		{
			$lPeriode->p_id = $period;
		}
		else
		{
			$lPeriode->find_periode($e_date);
		}
		$total_deb = 0;
		$total_cred = 0;
		bcscale(2);

		$ret = "";
		if (!empty($msg))
		{
			$ret.=$this->display_warning($msg, _("Attention : il vaut mieux utiliser les fiches que les postes comptables"));
		}
		$ret.="<table >";
		$ret.="<tr><td>" . _('Date') . " : </td><td>$e_date</td></tr>";
		/* display periode */
		$date_limit = $lPeriode->get_date_limit();
		$ret.='<tr> ' . td(_('Période Comptable')) . td($date_limit['p_start'] . '-' . $date_limit['p_end']) . '</tr>';
		$ret.="<tr><td>" . _('Libellé') . " </td><td>" . h($desc) . "</td></tr>";
		$ret.="<tr><td>" . _('PJ Num') . " </td><td>" . h($e_pj) . "</td></tr>";
		$ret.='</table>';
		$ret.="<table class=\"result\">";
		$ret.="<tr>";
		$ret.="<th>" . _('Quick Code ou ');
		$ret.=_("Poste") . " </th>";
		$ret.="<th style=\"text-align:left\"> " . _("Libellé") . " </th>";
		$ret.="<th style=\"text-align:right\">" . _("Débit") . "</th>";
		$ret.="<th style=\"text-align:right\">" . _("Crédit") . "</th>";
		/* if we use the AC */
		if ($g_parameter->MY_ANALYTIC != 'nu')
		{
			$anc = new Anc_Plan($this->db);
			$a_anc = $anc->get_list();
			$x = count($a_anc);
			/* set the width of the col */
			$ret.='<th colspan="' . $x . '" style="width:auto;text-align:center" >' . _('Compt. Analytique') . '</th>';

			/* add hidden variables pa[] to hold the value of pa_id */
			$ret.=Anc_Plan::hidden($a_anc);
		}
		$ret.="</tr>";

		$ret.=HtmlInput::hidden('e_date', $e_date);
		$ret.=HtmlInput::hidden('desc', $desc);
		$ret.=HtmlInput::hidden('period', $lPeriode->p_id);
		$ret.=HtmlInput::hidden('e_pj', $e_pj);
		$ret.=HtmlInput::hidden('e_pj_suggest', $e_pj_suggest);
		$mt = microtime(true);
		$ret.=HtmlInput::hidden('mt', $mt);
		// For predefined operation
		$ret.=HtmlInput::hidden('e_comm', $desc);
		$ret.=HtmlInput::hidden('jrn_type', $this->get_type());
		$ret.=HtmlInput::hidden('p_jrn', $this->id);
		$ret.=HtmlInput::hidden('nb_item', $nb_item);
		if ($this->with_concerned == true)
		{
			$ret.=HtmlInput::hidden('jrn_concerned', $jrn_concerned);
		}
		$ret.=dossier::hidden();
		$count = 0;
		for ($i = 0; $i < $nb_item; $i++)
		{
			if ($p_readonly == true)
			{
				if (!isset(${'qc_' . $i}))
					${'qc_' . $i} = '';
				if (!isset(${'poste' . $i}))
					${'poste' . $i} = '';
				if (!isset(${'amount' . $i}))
					${'amount' . $i} = '';
			}
                        $class=($i%2==0)?' class="even" ':' class="odd" ';
			$ret.="<tr $class> ";
			if (trim(${'qc_' . $i}) != "")
			{
				$oqc = new Fiche($this->db);
				$oqc->get_by_qcode(${'qc_' . $i}, false);
				$strPoste = $oqc->strAttribut(ATTR_DEF_ACCOUNT);
				$ret.="<td>" .
						${'qc_' . $i} . ' - ' .
						$oqc->strAttribut(ATTR_DEF_NAME) . HtmlInput::hidden('qc_' . $i, ${'qc_' . $i}) .
						'</td>';
			}

			if (trim(${'qc_' . $i}) == "" && trim(${'poste' . $i}) != "")
			{
				$oposte = new Acc_Account_Ledger($this->db, ${'poste' . $i});
				$strPoste = $oposte->id;
				$ret.="<td>" . h(${"poste" . $i} . " - " .
								$oposte->get_name()) . HtmlInput::hidden('poste' . $i, ${'poste' . $i}) .
						'</td>';
			}

			if (trim(${'qc_' . $i}) == "" && trim(${'poste' . $i}) == "")
				continue;
			$ret.="<td>" . h(${"ld" . $i}) . HtmlInput::hidden('ld' . $i, ${'ld' . $i}) ; 
                        $ret .=(isset(${"ck$i"})) ? HtmlInput::hidden('ck' . $i, ${'ck' . $i}) : "";
                        $ret .=        "</td>";
			if (isset(${"ck$i"}))
			{
				$ret.="<td class=\"num\">" . nbm(${"amount" . $i}) . HtmlInput::hidden('amount' . $i, ${'amount' . $i}) . "</td>" . td("");
				$total_deb = bcadd($total_deb, ${'amount' . $i});
			}
			else
			{
				$ret.=td("") . "<td class=\"num\">" . nbm(${"amount" . $i}) . HtmlInput::hidden('amount' . $i, ${'amount' . $i}) . "</td>";
				$total_cred = bcadd($total_cred, ${"amount" . $i});
			}
			/*$ret.="<td>";
			$ret.=(isset(${"ck$i"})) ? HtmlInput::hidden('ck' . $i, ${'ck' . $i}) : "";
			$ret.="</td>";*/
			// CA

			if ($g_parameter->MY_ANALYTIC != 'nu') // use of AA
			{
				if (preg_match("/^[6,7]+/", $strPoste) == 1)
				{
					// show form
					$op = new Anc_Operation($this->db);
					$null = ($g_parameter->MY_ANALYTIC == 'op') ? 1 : 0;
					$p_array['pa_id'] = $a_anc;
					/* op is the operation it contains either a sequence or a jrnx.j_id */
					$ret.=HtmlInput::hidden('op[]=', $i);

					$ret.='<td style="text-align:center">';
					$read = ($p_readonly == true) ? 0 : 1;
					$ret.=$op->display_form_plan($p_array, $null, $read, $count, round(${'amount' . $i}, 2));
					$ret.='</td>';
					$count++;
				}
			}



			$ret.="</tr>";
		}
		$ret.=tr(td('') . td(_('Totaux')) . td($total_deb, 'class="num"') . td($total_cred, 'class="num"'), 'class="highlight"');
		$ret.="</table>";
		if ($g_parameter->MY_ANALYTIC != 'nu' && $p_readonly == false)
			$ret.='<input type="button" class="button" value="' . _('verifie Imputation Analytique') . '" onClick="verify_ca(\'\');">';
		return $ret;
	}
	function get_min_row()
	{
		$row=$this->db->get_value("select jrn_deb_max_line from jrn_def where jrn_def_id=$1",array($this->id));
		return $row;
	}
	/**
	 * @brief Show the form to encode your operation
	 * @param$p_array if you correct or use a predef operation (default = null)
	 * @param$p_readonly 1 for readonly 0 for writable (default 0)
	 *@exception if ledger not found
	 * \return a string containing the form
	 */

	function input($p_array = null, $p_readonly = 0)
	{
		global $g_parameter, $g_user;
		$this->nb=$this->get_min_row();
		if ($p_readonly == 1)
			return $this->confirm($p_array);

		if ($p_array != null)
			extract($p_array);
		$add_js = "";
		if ($g_parameter->MY_PJ_SUGGEST == 'Y')
		{
			$add_js = "update_pj();";
		}
		if ($g_parameter->MY_DATE_SUGGEST=='Y')
		{
			$add_js.='get_last_date();';
		}
		$add_js.='update_row("quick_item");';
		$ret = "";
		if ($g_user->check_action(FICADD) == 1)
		{
			/* Add button */
			$f_add_button = new IButton('add_card');
			$f_add_button->label = _('Créer une nouvelle fiche');
			$f_add_button->set_attribute('ipopup', 'ipop_newcard');
			$f_add_button->set_attribute('jrn', $this->id);
			$f_add_button->javascript = " this.jrn=\$('p_jrn').value;select_card_type(this);";
			$f_add_button->input();
		}
		$wLedger = $this->select_ledger('ODS', 2);
		if ($wLedger == null)
			throw new Exception(_('Pas de journal disponible'));
		$wLedger->javascript = "onChange='update_name();update_predef(\"ods\",\"t\",\"".$_REQUEST['ac']."\");$add_js'";
		$label = " Journal " . HtmlInput::infobulle(2);

		$ret.=$label . $wLedger->input();


		// Load the javascript
		//
        $ret.="<table>";
		$ret.= '<tr ><td colspan="2" style="width:auto">';
		$wDate = new IDate('e_date');
		$wDate->readonly = $p_readonly;
		$e_date = (isset($e_date) && trim($e_date) != '') ? $e_date : '';
		$wDate->value = $e_date;

		$ret.=_("Date") . ' : ' . $wDate->input();
		$ret.= '</td>';
		/* insert periode if needed */
		// Periode
		//--
		if ($this->check_periode() == true)
		{
			$l_user_per = $g_user->get_periode();
			$def = (isset($periode)) ? $periode : $l_user_per;

			$period = new IPeriod("period");
			$period->user = $g_user;
			$period->cn = $this->db;
			$period->value = $def;
			$period->type = OPEN;
			try
			{
				$l_form_per = $period->input();
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 1)
				{
					echo _("Aucune période ouverte");
					exit();
				}
			}
			$label = HtmlInput::infobulle(3);
			$f_periode = _("Période comptable") . " $label " . $l_form_per;
			$ret.=td($f_periode);
		}
		$wPJ = new IText('e_pj');
		$wPJ->readonly = false;
		$wPJ->size = 10;

		/* suggest PJ ? */
		$default_pj = '';
		if ($g_parameter->MY_PJ_SUGGEST == 'Y')
		{
			$default_pj = $this->guess_pj();
		}
		$wPJ->value = (isset($e_pj)) ? $e_pj : $default_pj;
		$ret.= '</tr>';
		$ret.='<tr >';
		$ret.='<td colspan="2" style="width:auto"> ' . _('Pièce') . ' : ' . $wPJ->input();
		$ret.=HtmlInput::hidden('e_pj_suggest', $default_pj);
		$ret.= '</tr>';
		$ret.= '</td>';

		$ret.= '<tr>';
		$ret.='<td colspan="2" style="width:auto">';
		$ret.=_('Libellé');
		$wDescription = new IText('desc');
		$wDescription->readonly = $p_readonly;
		$wDescription->size = "50";
		$wDescription->value = (isset($desc)) ? $desc : '';

		$ret.=$wDescription->input();
		$ret.= '</td>';
		$ret.='</tr>';

		$ret.= '</table>';
		$nb_row = (isset($nb_item) ) ? $nb_item : $this->nb;

		$ret.=HtmlInput::hidden('nb_item', $nb_row);
		$ret.=dossier::hidden();

		$ret.=dossier::hidden();

		$ret.=HtmlInput::hidden('jrn_type', $this->get_type());
		$info = HtmlInput::infobulle(0);
		$info_poste = HtmlInput::infobulle(9);
		if ($g_user->check_action(FICADD) == 1)
			$ret.=$f_add_button->input();
		$ret.='<table id="quick_item" style="position:float;width:100%">';
		$ret.='<tr>' .
				'<th style="text-align:left">Quickcode' . $info . '</th>' .
				'<th style="text-align:left">' . _('Poste') . $info_poste . '</th>' .
				'<th style="text-align:left">' . _('Libellé') . '</th>' .
				'<th style="text-align:left">' . _('Montant') . '</th>' .
				'<th style="text-align:left">' . _('Débit') . '</th>' .
				'</tr>';


		for ($i = 0; $i < $nb_row; $i++)
		{
			// Quick Code
			$quick_code = new ICard('qc_' . $i);
			$quick_code->set_dblclick("fill_ipopcard(this);");
			$quick_code->set_attribute('ipopup', 'ipopcard');

			// name of the field to update with the name of the card
			$quick_code->set_attribute('label', "ld" . $i);

			// name of the field to update with the name of the card
			$quick_code->set_attribute('typecard', 'filter');

			// Add the callback function to filter the card on the jrn
			$quick_code->set_callback('filter_card');
			$quick_code->set_function('fill_data');
			$quick_code->javascript = sprintf(' onchange="fill_data_onchange(\'%s\');" ', $quick_code->name);

			$quick_code->value = (isset(${'qc_' . $i})) ? ${'qc_' . $i} : "";
			$quick_code->readonly = $p_readonly;
                        
			$label = '';
			if ($quick_code->value != '')
			{
				$Fiche = new Fiche($this->db);
				$Fiche->get_by_qcode($quick_code->value);
				$label = $Fiche->strAttribut(ATTR_DEF_NAME);
			}


			// Account
			$poste = new IPoste();
			$poste->name = 'poste' . $i;
			$poste->set_attribute('jrn', $this->id);
			$poste->set_attribute('ipopup', 'ipop_account');
			$poste->set_attribute('label', 'ld' . $i);
			$poste->set_attribute('account', 'poste' . $i);
			$poste->set_attribute('dossier', Dossier::id());

			$poste->value = (isset(${'poste' . $i})) ? ${"poste" . $i} : ''
			;
			$poste->dbl_click_history();

			$poste->readonly = $p_readonly;

			if ($poste->value != '')
			{
				$Poste = new Acc_Account($this->db);
				$Poste->set_parameter('value', $poste->value);
				$label = $Poste->get_lib();
			}

			// Description of the line
			$line_desc = new IText();
			$line_desc->name = 'ld' . $i;
			$line_desc->size = 30;
			$line_desc->value = (isset(${"ld" . $i})) ? ${"ld" . $i} :
					$label;

			// Amount
			$amount = new INum();
			$amount->size = 10;
			$amount->name = 'amount' . $i;
			$amount->value = (isset(${'amount' . $i})) ? ${"amount" . $i} : ''
			;
			$amount->readonly = $p_readonly;
			$amount->javascript = ' onChange="format_number(this);checkTotalDirect()"';
			// D/C
			$deb = new ICheckBox();
			$deb->name = 'ck' . $i;
			$deb->selected = (isset(${'ck' . $i})) ? true : false;
			$deb->readonly = $p_readonly;
			$deb->javascript = ' onChange="checkTotalDirect()"';

			$ret.='<tr>';
			$ret.='<td>' . $quick_code->input() . $quick_code->search() . '</td>';
			$ret.='<td>' . $poste->input() .
					'<script> document.getElementById(\'poste' . $i . '\').onblur=function(){ if (trim(this.value) !=\'\') {document.getElementById(\'qc_' . $i . '\').value="";}}</script>' .
					'</td>';
			$ret.='<td>' . $line_desc->input() . '</td>';
			$ret.='<td>' . $amount->input() . '</td>';
			$ret.='<td>' . $deb->input() . '</td>';
			$ret.='</tr>';
			// If readonly == 1 then show CA
		}
		$ret.='</table>';
		if (isset($this->with_concerned) && $this->with_concerned == true)
		{
			$oRapt = new Acc_Reconciliation($this->db);
			$w = $oRapt->widget();
			$w->name = 'jrn_concerned';
			$w->value = (isset($jrn_concerned)) ? $jrn_concerned : "";
			$ret.="R&eacute;conciliation/rapprochements : " . $w->input();
		}
		$ret.= create_script("$('".$wDate->id."').focus()");
		return $ret;
	}

	/**
	 * @brief
	 * check if the current ledger is closed
	 * \return 1 for yes, otherwise 0
	 * \see Periode::is_closed
	 */

	function is_closed($p_periode)
	{
		$per = new Periode($this->db);
		$per->set_jrn($this->id);
		$per->set_periode($p_periode);
		$ret = $per->is_closed();
		return $ret;
	}

	/**
	 * @brief verify that the operation can be saved
	 * @param$p_array array of data same layout that the $_POST from show_form
	 *
	 *
	 * \throw  the getcode  value is 1 incorrect balance,  2 date
	 * invalid, 3 invalid amount,  4 the card is not in the range of
	 * permitted card, 5 not in the user's period, 6 closed period
	 *
	 */

	function verify($p_array)
	{
            if (is_array($p_array ) == false || empty($p_array))
                    throw new Exception ("Array empty");
        /*
         * Check needed value
         */
        check_parameter($p_array,'p_jrn,e_date');
        
		extract($p_array);
		global $g_user;
		$tot_cred = 0;
		$tot_deb = 0;
		$msg = array();

		/* check if we can write into this ledger */
		if ($g_user->check_jrn($p_jrn) != 'W')
			throw new Exception(_('Accès interdit'), 20);

		/* check for a double reload */
		if (isset($mt) && $this->db->count_sql('select jr_mt from jrn where jr_mt=$1', array($mt)) != 0)
			throw new Exception('Double Encodage', 5);

		// Check the periode and the date
		if (isDate($e_date) == null)
		{
			throw new Exception('Date invalide', 2);
		}
		$periode = new Periode($this->db);
		/* find the periode  if we have enabled the check_periode */
		if ($this->check_periode() == false)
		{
			$periode->find_periode($e_date);
		}
		else
		{
			$periode->p_id = $period;
			list ($min, $max) = $periode->get_date_limit();
			if (cmpDate($e_date, $min) < 0 ||
					cmpDate($e_date, $max) > 0)
				throw new Exception(_('Date et periode ne correspondent pas'), 6);
		}



		// Periode ferme
		if ($this->is_closed($periode->p_id) == 1)
		{
			throw new Exception('Periode fermee', 6);
		}
		/* check if we are using the strict mode */
		if ($this->check_strict() == true)
		{
			/* if we use the strict mode, we get the date of the last
			  operation */
			$last_date = $this->get_last_date();
			if ($last_date != null && cmpDate($e_date, $last_date) < 0)
				throw new Exception(_('Vous utilisez le mode strict la dernière operation est la date du ')
						. $last_date . ' ' . _('vous ne pouvez pas encoder à une date antérieure'), 15);
		}

		for ($i = 0; $i < $nb_item; $i++)
		{
			$err = 0;

			// Check the balance
			if (!isset(${'amount' . $i}))
				continue;

			$amount = round(${'amount' . $i}, 2);
			$tot_deb+=(isset(${'ck' . $i})) ? $amount : 0;
			$tot_cred+=(!isset(${'ck' . $i})) ? $amount : 0;

			// Check if the card is permitted
			if (isset(${'qc_' . $i}) && trim(${'qc_' . $i}) != "")
			{
				$f = new Fiche($this->db);
				$f->quick_code = ${'qc_' . $i};
				if ($f->belong_ledger($p_jrn) < 0)
					throw new Exception("La fiche quick_code = " .
							$f->quick_code . " n'est pas dans ce journal", 4);
				if (strlen(trim(${'qc_' . $i})) != 0 && isNumber(${'amount' . $i}) == 0)
					throw new Exception('Montant invalide', 3);

				$strPoste = $f->strAttribut(ATTR_DEF_ACCOUNT);
				if ($strPoste == '')
					throw new Exception(sprintf(_("La fiche %s n'a pas de poste comptable"), ${"qc_" . $i}));

				$p = new Acc_Account_Ledger($this->db, $strPoste);
				if ($p->do_exist() == 0)
					throw new Exception(_('Poste Inexistant pour la fiche [' . ${'qc_' . $i} . ']'), 4);
			}

			// Check if the account is permitted
			if (isset(${'poste' . $i}) && strlen(trim(${'poste' . $i})) != 0)
			{
				$p = new Acc_Account_Ledger($this->db, ${'poste' . $i});
				if ($p->belong_ledger($p_jrn) < 0)
					throw new Exception(_("Le poste") . " " . $p->id . " " . _("n'est pas dans ce journal"), 5);
				if (strlen(trim(${'poste' . $i})) != 0 && isNumber(${'amount' . $i}) == 0)
					throw new Exception(_('Poste invalide [' . ${'poste' . $i} . ']'), 3);
				if ($p->do_exist() == 0)
					throw new Exception(_('Poste Inexistant [' . ${'poste' . $i} . ']'), 4);
				$card_id = $p->find_card();
				if (!empty($card_id))
				{
					$str_msg = " Le poste " . $p->id . " appartient à " . count($card_id) . " fiche(s) dont :";
					$max = (count($card_id) > MAX_COMPTE_CARD) ? MAX_COMPTE_CARD : count($card_id);
					for ($x = 0; $x < $max; $x++)
					{
						$card = new Fiche($this->db, $card_id[$x]['f_id']);
						$str_msg.=HtmlInput::card_detail($card->strAttribut(ATTR_DEF_QUICKCODE), $card->strAttribut(ATTR_DEF_NAME), 'style="color:red;display:inline;text-decoration:underline"');
						$str_msg.=" ";
					}
					$msg[] = $str_msg;
				}
			}
		}
		$tot_deb = round($tot_deb, 4);
		$tot_cred = round($tot_cred, 4);
		if ($tot_deb != $tot_cred)
		{
			throw new Exception(_("Balance incorrecte ") . " debit = $tot_deb credit=$tot_cred ", 1);
		}

		return $msg;
	}

	/**
	 * @brief compute the internal code of the saved operation and set the $this->jr_internal to
	 *  the computed value
	 *
	 * @param$p_grpt id in jr_grpt_
	 *
	 * \return string internal_code
	 *      -
	 *
	 */

	function compute_internal_code($p_grpt)
	{
		if ($this->id == 0)
			return;
		$num = $this->db->get_next_seq('s_internal');
		$atype = $this->get_propertie();
		$type = substr($atype['jrn_def_code'], 0, 1);
		$internal_code = sprintf("%s%06X", $type, $num);
		$this->jr_internal = $internal_code;
		return $internal_code;
	}

	/**
	 * @brief save the operation into the jrnx,jrn, ,
	 *  CA and pre_def
	 * @param$p_array
	 *
	 * \return array with [0] = false if failed otherwise true, [1] error
	 * code
	 */

	function save($p_array = null)
	{
		if ($p_array == null)
			throw new Exception('save cannot use a empty array');
		global $g_parameter;
		extract($p_array);
		try
		{
			$msg = $this->verify($p_array);
			if (!empty($msg))
			{
				echo $this->display_warning($msg, _("Attention : il vaut mieux utiliser les fiches que les postes comptables "));
			}
			$this->db->start();

			$seq = $this->db->get_next_seq('s_grpt');
			$internal = $this->compute_internal_code($seq);

			$group = $this->db->get_next_seq("s_oa_group");
			$tot_amount = 0;
			$tot_deb = 0;
			$tot_cred = 0;
			$oPeriode = new Periode($this->db);
			$check_periode = $this->check_periode();
			if ($check_periode == false)
			{
				$oPeriode->find_periode($e_date);
			}
			else
			{
				$oPeriode->id = $period;
			}

			$count = 0;
			for ($i = 0; $i < $nb_item; $i++)
			{
				if (!isset(${'qc_' . $i}) && !isset(${'poste' . $i}))
					continue;
				$acc_op = new Acc_Operation($this->db);
				$quick_code = "";
				// First we save the jrnx
				if (isset(${'qc_' . $i}))
				{
					$qc = new Fiche($this->db);
					$qc->get_by_qcode(${'qc_' . $i}, false);
					$sposte = $qc->strAttribut(ATTR_DEF_ACCOUNT);
					/*  if there are 2 accounts take following the deb or cred */
					if (strpos($sposte, ',') != 0)
					{
						$array = explode(",", $sposte);
						$poste = (isset(${'ck' . $i})) ? $array[0] : $array[1];
					}
					else
					{
						$poste = $sposte;
						if ($poste == '')
							throw new Exception(sprintf(_("La fiche %s n'a pas de poste comptable"), ${"qc_" . $i}));
					}
					$quick_code = ${'qc_' . $i};
				}
				else
				{
					$poste = ${'poste' . $i};
				}

				$acc_op->date = $e_date;
				// compute the periode is do not check it
				if ($check_periode == false)
					$acc_op->periode = $oPeriode->p_id;
				$acc_op->desc = null;
				if (strlen(trim(${'ld' . $i})) != 0)
					$acc_op->desc = ${'ld' . $i};
				$acc_op->amount = round(${'amount' . $i}, 2);
				$acc_op->grpt = $seq;
				$acc_op->poste = $poste;
				$acc_op->jrn = $this->id;
				$acc_op->type = (isset(${'ck' . $i})) ? 'd' : 'c';
				$acc_op->qcode = $quick_code;
				$j_id = $acc_op->insert_jrnx();
				$tot_amount+=round($acc_op->amount, 2);
				$tot_deb+=($acc_op->type == 'd') ? $acc_op->amount : 0;
				$tot_cred+=($acc_op->type == 'c') ? $acc_op->amount : 0;
				if ($g_parameter->MY_ANALYTIC != "nu")
				{
					if (preg_match("/^[6,7]+/", $poste) == 1)
					{

						// for each item, insert into operation_analytique */
						$op = new Anc_Operation($this->db);
						$op->oa_group = $group;
						$op->j_id = $j_id;
						$op->oa_date = $e_date;
						$op->oa_debit = ($acc_op->type == 'd' ) ? 't' : 'f';
						$op->oa_description = $desc;
						$op->save_form_plan($p_array, $count, $j_id);
						$count++;
					}
				}
			}// loop for each item
			$acc_end = new Acc_Operation($this->db);
			$acc_end->amount = $tot_deb;
			if ($check_periode == false)
				$acc_end->periode = $oPeriode->p_id;
			$acc_end->date = $e_date;
			$acc_end->desc = $desc;
			$acc_end->grpt = $seq;
			$acc_end->jrn = $this->id;
			$acc_end->mt = $mt;
			$jr_id = $acc_end->insert_jrn();
			$this->jr_id = $jr_id;
			if ($jr_id == false)
				throw new Exception(_('Balance incorrecte'));
			$acc_end->pj = $e_pj;

			/* if e_suggest != e_pj then do not increment sequence */
			if (strcmp($e_pj, $e_pj_suggest) == 0 && strlen(trim($e_pj)) != 0)
			{
				$this->inc_seq_pj();
			}

			$this->pj = $acc_end->set_pj();

			$this->db->exec_sql("update jrn set jr_internal='" . $internal . "' where " .
					" jr_grpt_id = " . $seq);
			$this->internal = $internal;
			// Save now the predef op
			//------------------------
                        if (isset ($opd_name) && trim($opd_name) != "" ){
				$opd = new Pre_Op_Advanced($this->db);
				$opd->get_post();
				$opd->save();
			}

			if (isset($this->with_concerned) && $this->with_concerned == true)
			{
				$orap = new acc_reconciliation($this->db);
				$orap->jr_id = $jr_id;

				$orap->insert($jrn_concerned);
			}
                        /**
                         * Save the file is any
                         */
                        if (isset($_FILES["pj"]))
                        {
                            $this->db->save_upload_document($seq);
                        }
			
		}
		catch (Exception $a)
		{
			throw $a;
		}
		catch (Exception $e)
		{
			$this->db->rollback();
			echo _('OPERATION ANNULEE ');
			echo '<hr>';
			echo __FILE__ . __LINE__ . $e->getMessage();
			exit();
		}
		$this->db->commit();
		return true;
	}

	/**
	 * @brief get all the data from request and build the object
	 */

	function get_request()
	{
		$this->id = $_REQUEST['p_jrn'];
	}

	/**
	 * @brief retrieve the next number for this type of ledger
	 * @paramp_cn connx
	 * @paramp_type ledger type
	 *
	 * \return the number
	 *
	 *
	 */

	static function next_number($p_cn, $p_type)
	{

		$Ret = $p_cn->count_sql("select * from jrn_def where jrn_def_type='" . $p_type . "'");
		return $Ret + 1;
	}

	/**
	 * @brief get the first ledger
	 * @paramthe type
	 * \return the j_id
	 */

	public function get_first($p_type, $p_access = 3)
	{
		global $g_user;
		$all = $g_user->get_ledger($p_type, $p_access);
		return $all[0];
	}

	/**
	 * @brief Update the paiment  in the list of operation
	 * @param$p_array is normally $_GET
	 */

	function update_paid($p_array)
	{
		// reset all the paid flag because the checkbox is post only
		// when checked
		foreach ($p_array as $name => $paid)
		{
			list($ad) = sscanf($name, "set_jr_id%d");
			if ($ad == null)
				continue;
			$sql = "update jrn set jr_rapt='' where jr_id=$ad";
			$Res = $this->db->exec_sql($sql);
		}
		// set a paid flag for the checked box
		foreach ($p_array as $name => $paid)
		{
			list ($id) = sscanf($name, "rd_paid%d");
			if ($id == null)
				continue;

			$sql = "update jrn set jr_rapt='paid' where jr_id=$id";
			$Res = $this->db->exec_sql($sql);
		}
	}

	function update_internal_code($p_internal)
	{
		if (!isset($this->grpt_id))
			throw new Exception(('ERREUR ' . __FILE__ . ":" . __LINE__));
		$Res = $this->db->exec_sql("update jrn set jr_internal='" . $p_internal . "' where " .
				" jr_grpt_id = " . $this->grpt_id);
	}
        /**
         * Return an array of default card for the ledger type given 
         * 
         * @param $p_ledger_type VEN ACH ODS or FIN
         * @param $p_side   D for Debit or C for credit or NA No Applicable
         */
        function get_default_card($p_ledger_type,$p_side)
        {
            $array=array();
            $fiche_def_ref=new Fiche_Def_Ref($this->db);
            // ----- for FINANCIAL  ----
            if ($p_ledger_type =='FIN')
            {
                $array=$fiche_def_ref->get_by_modele(FICHE_TYPE_CLIENT);
                $array=array_merge ( $array , $fiche_def_ref->get_by_modele(FICHE_TYPE_FOURNISSEUR));
                $array=array_merge ( $array , $fiche_def_ref->get_by_modele(FICHE_TYPE_FIN));
                $array=array_merge ( $array , $fiche_def_ref->get_by_modele(FICHE_TYPE_ADM_TAX));
                $array=array_merge ( $array , $fiche_def_ref->get_by_modele(FICHE_TYPE_EMPL));
                
            }
            // --- for miscellaneous ----
            if ($p_ledger_type == 'ODS')
            {
                $result=$this->db->get_array('select fd_id from fiche_def');
                for ($i = 0;$i<count($result);$i++ )
                {
                    $array[$i]=$result[$i]['fd_id'];
                }
            }
            if ($p_side == 'D')
            {
                switch($p_ledger_type) 
                {
                    case 'VEN':
                        $array=$fiche_def_ref->get_by_modele(FICHE_TYPE_CLIENT);
                        break;
                    case 'ACH':
                        $array=$fiche_def_ref->get_by_modele(FICHE_TYPE_ACH_SER);
                        $array=array_merge ($array, $fiche_def_ref->get_by_modele(FICHE_TYPE_ACH_MAR));
                        $array=array_merge ($array,$fiche_def_ref->get_by_modele(FICHE_TYPE_ACH_MAT));
                        break;
                    default :
                        throw new Exception(_('get_default_card p_ledger_side is invalide ['.$p_ledger_type.']'));
                    
                }
            } elseif ($p_side == 'C')
            {
                 switch($p_ledger_type) 
                {
                    case 'VEN':
                        $array=$fiche_def_ref->get_by_modele(FICHE_TYPE_VENTE);
                        break;
                    case 'ACH':
                        $array=  array_merge($array, $fiche_def_ref->get_by_modele(FICHE_TYPE_ADM_TAX));
                        $array=  array_merge($array, $fiche_def_ref->get_by_modele(FICHE_TYPE_FOURNISSEUR));
                        break;
                    default :
                        throw new Exception(_('get_default_card p_ledger_side is invalide ['.$p_ledger_type.']'));
                    
                }
            }
            return $array;
            /*
            $return=array();
            $return = array_values($array);
            for ($i = 0;$i<count($array);$i++ )
            {
                $return[$i]=$array[$i]['fd_id'];
            }
            return $return;
             * 
             */
        }
	/**
	 * @brief retrieve all the card for this type of ledger, make them
	 * into a string separated by comma
	 * @paramnone
	 * \return all the card or null is nothing is found
	 */

	function get_all_fiche_def()
	{
		$sql = "select jrn_def_fiche_deb as deb,jrn_def_fiche_cred as cred " .
				" from jrn_def where " .
				" jrn_def_id = $1 ";

		$r = $this->db->exec_sql($sql, array($this->id));

		$res = Database::fetch_all($r);
		if (empty($res))
			return null;
		$card = "";
		$comma = '';
		foreach ($res as $item)
		{
			if (strlen(trim($item['deb'])) != 0)
			{
				$card.=$comma . $item['deb'];
				$comma = ',';
			}
			if (strlen(trim($item['cred'])) != '')
			{
				$card.=$comma . $item['cred'];
				$comma = ',';
			}
		}

		return $card;
	}

	/**
	 * @brief get the saldo of an exercice, used for the opening of a folder
	 * @param$p_exercice is the exercice we want
	 * \return an array
	 * index =
	 * - solde (debit > 0 ; credit < 0)
	 * - j_poste
	 * - j_qcode
	 */

	function get_saldo_exercice($p_exercice)
	{
		$sql = "select sum(a.montant) as solde, j_poste, j_qcode
             from
             (select j_id, case when j_debit='t' then j_montant
             else j_montant * (-1) end  as montant
             from jrnx) as a
             join jrnx using (j_id)
             join parm_periode on (j_tech_per = p_id )
             where
             p_exercice=$1
             and j_poste::text not like '7%'
             and j_poste::text not like '6%'
             group by j_poste,j_qcode
             having (sum(a.montant) != 0 )";
		$res = $this->db->get_array($sql, array($p_exercice));
		return $res;
	}

	/**
	 * @brief Check if a Dossier is using the strict mode or not
	 * \return true if we are using the strict_mode
	 */

	function check_strict()
	{
		global $g_parameter;
		if ($g_parameter->MY_STRICT == 'Y')
			return true;
		if ($g_parameter->MY_STRICT == 'N')
			return false;
		throw  new Exception("Valeur invalid " . __FILE__ . ':' . __LINE__);
	}

	/**
	 * @brief Check if a Dossier is using the check on the periode, if true than the user has to enter the date
	 * and the periode, it is a security check
	 * \return true if we are using the double encoding (date+periode)
	 */

	function check_periode()
	{
		global $g_parameter;
		if ($g_parameter->MY_CHECK_PERIODE == 'Y')
			return true;
		if ($g_parameter->MY_CHECK_PERIODE == 'N')
			return false;
		throw  new Exception("Valeur invalid " . __FILE__ . ':' . __LINE__);
	}

	/**
	 * @brief get the date of the last operation
	 */

	function get_last_date()
	{
		if ($this->id == 0)
			throw new Exception(__FILE__ . ":" . __LINE__ . "Journal incorrect ");
		$sql = "select to_char(max(jr_date),'DD.MM.YYYY') from jrn where jr_def_id=$1";
		$date = $this->db->get_value($sql, array($this->id));
		return $date;
	}

	/**
	 * @brief retrieve the jr_id thanks the internal code, do not change
	 * anything to the current object
	 * @paramthe internal code
	 * \return the jr_id or 0 if not found
	 */

	function get_id($p_internal)
	{
		$sql = 'select jr_id from jrn where jr_internal=$1';
		$value = $this->db->get_value($sql, array($p_internal));
		if ($value == '')
			$value = 0;
		return $value;
	}

	/**
	 * @brief create the invoice and saved it as attachment to the
	 * operation,
	 * @param$internal is the internal code
	 * @param$p_array is normally the $_POST
	 * \return a string
	 */

	function create_document($internal, $p_array)
	{
		extract($p_array);
		$doc = new Document($this->db);
		$doc->f_id = $e_client;
		$doc->md_id = $gen_doc;
		$doc->ag_id = 0;
                $p_array['e_pj']=$this->pj;
		$filename="";
		$doc->Generate($p_array,$p_array['e_pj']);
		// Move the document to the jrn
		$doc->MoveDocumentPj($internal);
		// Update the comment with invoice number, if the comment is empty
		if (!isset($e_comm) || strlen(trim($e_comm)) == 0)
		{
			$sql = "update jrn set jr_comment=' document " . $doc->d_number . "' where jr_internal='$internal'";
			$this->db->exec_sql($sql);
		}
		return h($doc->d_name . ' (' . $doc->d_filename . ')');
	}

	/**
	 * @brief check if the payment method is valid
	 * @param$e_mp is the value and $e_mp_qcode is the quickcode
	 * \return nothing throw an Exception
	 */

	public function check_payment($e_mp, $e_mp_qcode)
	{
		/*   Check if the "paid by" is empty, */
		if ($e_mp != 0)
		{
			/* the paid by is not empty then check if valid */
			$empl = new Fiche($this->db);
			$empl->get_by_qcode($e_mp_qcode);
			if ($empl->empty_attribute(ATTR_DEF_ACCOUNT) == true)
			{
				throw new Exception(_("Celui qui paie n' a pas de poste comptable"), 20);
			}
			/* get the account and explode if necessary */
			$sposte = $empl->strAttribut(ATTR_DEF_ACCOUNT);
			// if 2 accounts, take only the debit one for customer
			if (strpos($sposte, ',') != 0)
			{
				$array = explode(',', $sposte);
				$poste_val = $array[0];
			}
			else
			{
				$poste_val = $sposte;
			}
			$poste = new Acc_Account_Ledger($this->db, $poste_val);
			if ($poste->load() == false)
			{
				throw new Exception(sprintf(_("Pour la fiche %s le poste comptable [%s] n'existe pas"),$empl->quick_code,$poste->id  ), 9);
			}
		}
	}

	/**
	 * @brief increment the sequence for the pj */

	function inc_seq_pj()
	{
		$sql = "select nextval('s_jrn_pj" . $this->id . "')";
		$this->db->exec_sql($sql);
	}

	/**
	 * @brief return a HTML string with the form for the search
	 * @param $p_type if the type of ledger possible values=ALL,VEN,ACH,ODS,FIN
	 * @param $all_type_ledger
	 *       values :
	 *         - 1 means all the ledger of this type
	 *         - 0 No have the "Tous les journaux" availables
	 * @param $div is the div (for reconciliation)
	 * @return a HTML String without the tag FORM or DIV
	 *
	 * @see build_search_sql
	 * @see display_search_form
	 * @see list_operation
	 */

	function search_form($p_type, $all_type_ledger = 1, $div = "")
	{
            global $g_user;
            $r="";
                $bledger_param=  json_encode(array(
                    'dossier'=>$_REQUEST['gDossier'],
                    'type'=>$p_type,
                    'all_type'=>$all_type_ledger,
                    'div'=>$div
                    ));
                
                $bledger_param=  str_replace('"', "'", $bledger_param);
                $bledger=new ISmallButton('l');
                $bledger->label=_("choix des journaux");
                $bledger->javascript=" show_ledger_choice($bledger_param)";
                $f_ledger=$bledger->input();
                $hid_jrn="";
                if ( isset ($_REQUEST[$div.'nb_jrn']) ){
                    for ($i=0;$i < $_REQUEST[$div.'nb_jrn'];$i++) {
                        if ( isset ($_REQUEST[$div."r_jrn"][$i]))
                            $hid_jrn.=HtmlInput::hidden($div.'r_jrn['.$i.']',$_REQUEST[$div."r_jrn"][$i]);
                    }
                    $hid_jrn.=HtmlInput::hidden($div.'nb_jrn',$_REQUEST[$div.'nb_jrn']);
                } else {
                    $hid_jrn=HtmlInput::hidden($div.'nb_jrn',0);
                }
                /* Compute date for exercice */
                $period = $g_user->get_periode();
                $per = new Periode($this->db, $period);
		$exercice = $per->get_exercice();
		list($per_start, $per_end) = $per->get_limit($exercice);
		$date_end = $per_end->last_day();
                $date_start=$per_start->first_day();
                        
		/* widget for date_start */
		$f_date_start = new IDate('date_start');
		/* all periode or only the selected one */
		if (isset($_REQUEST['date_start']))
		{
			$f_date_start->value = $_REQUEST['date_start'];
		}
		else
		{
                        $f_date_start->value=$date_start;
		}

		/* widget for date_end */
		$f_date_end = new IDate('date_end');
		/* all date or only the selected one */
		if (isset($_REQUEST['date_end']))
		{
			$f_date_end->value = $_REQUEST['date_end'];
		}
		else
		{
			$f_date_end->value = $date_end;
		}
                /* widget for date term */
                $f_date_paid_start=new IDate('date_paid_start');
                $f_date_paid_end=new IDate('date_paid_end');
                
                $f_date_paid_start->value=(isset($_REQUEST['date_paid_start']))?$_REQUEST['date_paid_start']:'';
                $f_date_paid_end->value=(isset($_REQUEST['date_paid_end']))?$_REQUEST['date_paid_end']:'';
                
		/* widget for desc */
		$f_descript = new IText('desc');
		$f_descript->size = 40;
		if (isset($_REQUEST['desc']))
		{
			$f_descript->value = $_REQUEST['desc'];
		}

		/* widget for amount */
		$f_amount_min = new INum('amount_min');
		$f_amount_min->value = (isset($_REQUEST['amount_min'])) ? abs($_REQUEST['amount_min']) : 0;
		$f_amount_max = new INum('amount_max');
		$f_amount_max->value = (isset($_REQUEST['amount_max'])) ? abs($_REQUEST['amount_max']) : 0;

		/* input quick code */
		$f_qcode = new ICard('qcode' . $div);

		$f_qcode->set_attribute('typecard', 'all');
		/*        $f_qcode->set_attribute('p_jrn','0');

		  $f_qcode->set_callback('filter_card');
		 */
		$f_qcode->set_dblclick("fill_ipopcard(this);");
		// Add the callback function to filter the card on the jrn
		//$f_qcode->set_callback('filter_card');
		$f_qcode->set_function('fill_data');
		$f_qcode->javascript = sprintf(' onchange="fill_data_onchange(%s);" ', $f_qcode->name);
		$f_qcode->value = (isset($_REQUEST['qcode' . $div])) ? $_REQUEST['qcode' . $div] : '';

		/*        $f_txt_qcode=new IText('qcode');
		  $f_txt_qcode->value=(isset($_REQUEST['qcode']))?$_REQUEST['qcode']:'';
		 */

		/* input poste comptable */
		$f_accounting = new IPoste('accounting');
		$f_accounting->value = (isset($_REQUEST['accounting'])) ? $_REQUEST['accounting'] : '';
		if ($this->id == -1)
			$jrn = 0;
		else
			$jrn = $this->id;
		$f_accounting->set_attribute('jrn', $jrn);
		$f_accounting->set_attribute('ipopup', 'ipop_account');
		$f_accounting->set_attribute('label', 'ld');
		$f_accounting->set_attribute('account', 'accounting');
		$info = HtmlInput::infobulle(13);

		$f_paid = new ICheckbox('unpaid');
		$f_paid->selected = (isset($_REQUEST['unpaid'])) ? true : false;

		$r.=dossier::hidden();
		$r.=HtmlInput::hidden('ledger_type', $this->type);
		$r.=HtmlInput::hidden('ac', $_REQUEST['ac']);
		ob_start();
		require_once NOALYSS_INCLUDE.'/template/ledger_search.php';
		$r.=ob_get_contents();
		ob_end_clean();
		return $r;
	}

	/**
	 * @brief this function will create a sql stmt to use to create the list for
	 * the ledger,
	 * @param$p_array is usually the $_GET,
	 * @param$p_order the order of the row
	 * @param$p_where is the sql condition if not null then the $p_array will not be used
	 * \note the p_action will be used to filter the ledger but gl means ALL
	 * struct array $p_array
	  \verbatim
	  (
	  [gDossier] => 13
	  [p_jrn] => -1
	  [date_start] =>
	  [date_end] =>
	  [amount_min] => 0
	  [amount_max] => 0
	  [desc] =>
	  [search] => Rechercher
	  [p_action] => ven
	  [sa] => l
	  )
	  \endverbatim
	 * \return an array with a valid sql statement, an the where clause => array[sql] array[where]
	 * \see list_operation
	 * \see display_search_form
	 * \see search_form
	 */

	public function build_search_sql($p_array, $p_order = "", $p_where = "")
	{
		$sql = "select jr_id	,
             jr_montant,
             substr(jr_comment,1,60) as jr_comment,
             to_char(jr_ech,'DD.MM.YY') as str_jr_ech,
             to_char(jr_date,'DD.MM.YY') as str_jr_date,
             jr_date as jr_date_order,
             jr_grpt_id,
             jr_rapt,
             jr_internal,
             jrn_def_id,
             jrn_def_name,
             jrn_def_ech,
             jrn_def_type,
             jr_valid,
             jr_tech_per,
             jr_pj_name,
             p_closed,
             jr_pj_number,
             n_text,
	     case
	     when jrn_def_type='VEN' then
		 (select ad_value from fiche_detail where ad_id=1
		 and f_id=(select max(qs_client) from quant_sold join jrnx using (j_id) join jrn as e on (e.jr_grpt_id=j_grpt) where e.jr_id=x.jr_id))
	    when jrn_def_type = 'ACH' then
		(select ad_value from fiche_detail where ad_id=1
		and f_id=(select max(qp_supplier) from quant_purchase join jrnx using (j_id) join jrn as e on (e.jr_grpt_id=j_grpt) where e.jr_id=x.jr_id))
	    when jrn_def_type = 'FIN' then
		(select ad_value from fiche_detail where ad_id=1
		and f_id=(select qf_other from quant_fin where quant_fin.jr_id=x.jr_id))
	    end as name,
	   case
	     when jrn_def_type='VEN' then (select ad_value from fiche_detail where ad_id=32 and f_id=(select max(qs_client) from quant_sold join jrnx using (j_id) join jrn as e on (e.jr_grpt_id=j_grpt) where e.jr_id=x.jr_id))
	    when jrn_def_type = 'ACH' then (select ad_value from fiche_detail where ad_id=32 and f_id=(select max(qp_supplier) from quant_purchase join jrnx using (j_id) join jrn as e on (e.jr_grpt_id=j_grpt) where e.jr_id=x.jr_id))
	    when jrn_def_type = 'FIN' then (select ad_value from fiche_detail where ad_id=32 and f_id=(select qf_other from quant_fin where quant_fin.jr_id=x.jr_id))
	    end as first_name,
	    case
	     when jrn_def_type='VEN' then (select ad_value from fiche_detail where ad_id=23 and f_id=(select max(qs_client) from quant_sold join jrnx using (j_id) join jrn as e on (e.jr_grpt_id=j_grpt) where e.jr_id=x.jr_id))
	    when jrn_def_type = 'ACH' then (select ad_value from fiche_detail where ad_id=23 and f_id=(select max(qp_supplier) from quant_purchase join jrnx using (j_id) join jrn as e on (e.jr_grpt_id=j_grpt) where e.jr_id=x.jr_id))
	    when jrn_def_type = 'FIN' then (select ad_value from fiche_detail where ad_id=23 and f_id=(select qf_other from quant_fin where quant_fin.jr_id=x.jr_id))
	    end as quick_code,
	    case
	     when jrn_def_type='VEN' then
		     (select sum(qs_price)+sum(vat) from
				(select qs_internal,qs_price,case when qs_vat_sided<>0 then 0 else qs_vat end as vat from quant_sold where qs_internal=X.jr_internal) as ven_invoice
			  )
	    when jrn_def_type = 'ACH' then
			(
				select sum(qp_price)+sum(vat)+sum(qp_nd_tva)+sum(qp_nd_tva_recup)
				from
				 (select qp_internal,qp_price,qp_nd_tva,qp_nd_tva_recup,qp_vat-qp_vat_sided as vat from quant_purchase where qp_internal=X.jr_internal) as invoice_purchase
			)
		else null
		end as total_invoice,
            jr_date_paid,
            to_char(jr_date_paid,'DD.MM.YY') as str_jr_date_paid
             from
             jrn as X left join jrn_note using(jr_id)
             join jrn_def on jrn_def_id=jr_def_id
             join parm_periode on p_id=jr_tech_per";

		if (!empty($p_array))
			extract($p_array);

                if (isset($op) ) 
                    $r_jrn = (isset(${$op."r_jrn"})) ? ${$op."r_jrn"} : -1;
                else
                {
                    $r_jrn = (isset($r_jrn)) ? $r_jrn : -1;
                    
                }

		/* if no variable are set then give them a default
		 * value */
		if ($p_array == null || empty($p_array) || !isset($amount_min))
		{
			$amount_min = 0;
			$amount_max = 0;

			$desc = '';
			$qcode = (isset($qcode)) ? $qcode : "";
			if (isset($qcodesearch_op))
				$qcode = $qcodesearch_op;
			$accounting = (isset($accounting)) ? $accounting : "";
			$periode = new Periode($this->db);
			$g_user = new User($this->db);
			$p_id = $g_user->get_periode();
			if ($p_id != null)
			{
				list($date_start, $date_end) = $periode->get_date_limit($p_id);
			}
		}

		/* if p_jrn : 0 if means all ledgers, if -1 means all ledger of this
		 *  type otherwise only one ledger */
		$fil_ledger = '';
		$fil_amount = '';
		$fil_date = '';
		$fil_desc = '';
		$fil_sec = '';
		$fil_qcode = '';
		$fil_account = '';
		$fil_paid = '';
                $fil_date_paid='';

		$and = '';
		$g_user = new User($this->db);
		$p_action = $ledger_type;
		if ($p_action == '')
			$p_action = 'ALL';
		if ($r_jrn == -1)
		{

			/* from compta.php the p_action is quick_writing instead of ODS  */
			if ($p_action == 'quick_writing')
				$p_action = 'ODS';


			$fil_ledger = $g_user->get_ledger_sql($p_action, 3);
			$and = ' and ';
		}
		else
		{

			if ($p_action == 'quick_writing')
				$p_action = 'ODS';

			$aLedger = $g_user->get_ledger($p_action, 3);
			$fil_ledger = '';
			$sp = '';
			for ($i = 0; $i < count($r_jrn); $i++)
			{
                                if (isset($r_jrn[$i]) )
				{
                                    $a=$r_jrn[$i];
  				    $fil_ledger.=$sp . $a;
                                    $sp = ',';
				}
			}
			$fil_ledger = ' jrn_def_id in (' . $fil_ledger . ')';
			$and = ' and ';

			/* no ledger selected */
			if ($sp == '')
			{
				$fil_ledger = '';
				$and = '';
			}
		}

		/* format the number */
		$amount_min = abs(toNumber($amount_min));
		$amount_max = abs(toNumber($amount_max));
		if ($amount_min > 0 && isNumber($amount_min))
		{
			$fil_amount = $and . ' jr_montant >=' . $amount_min;
			$and = ' and ';
		}
		if ($amount_max > 0 && isNumber($amount_max))
		{
			$fil_amount.=$and . ' jr_montant <=' . $amount_max;
			$and = ' and ';
		}
		/* -------------------------------------------------------------------------- *
		 * if both amount are the same then we need to search into the detail
		 * and we reset the fil_amount
		 * -------------------------------------------------------------------------- */
		if (isNumber($amount_min) &&
				isNumber($amount_max) &&
				$amount_min > 0 &&
				bccomp($amount_min, $amount_max, 2) == 0)
		{
			 $fil_amount = $and . ' ( ';
 
                        // Look in detail
                       $fil_amount .= 'jr_grpt_id in ( select distinct j_grpt from jrnx where j_montant = ' . $amount_min . ') ';

                       //and the total operation
                       $fil_amount .= ' or ';
                       $fil_amount .= ' jr_montant = '.$amount_min;

                      $fil_amount .= ')';
                      $and = " and ";
		}
		// date
		if (isset($date_start) && isDate($date_start) != null)
		{
			$fil_date = $and . " jr_date >= to_date('" . $date_start . "','DD.MM.YYYY')";
			$and = " and ";
		}
		if (isset($date_end) && isDate($date_end) != null)
		{
			$fil_date.=$and . " jr_date <= to_date('" . $date_end . "','DD.MM.YYYY')";
			$and = " and ";
		}
		// date paiement
		if (isset($date_paid_start) && isDate($date_paid_start) != null)
		{
			$fil_date_paid = $and . " jr_date_paid >= to_date('" . $date_paid_start . "','DD.MM.YYYY')";
			$and = " and ";
		}
		if (isset($date_paid_end) && isDate($date_paid_end) != null)
		{
			$fil_date_paid.=$and . " jr_date_paid <= to_date('" . $date_paid_end . "','DD.MM.YYYY')";
			$and = " and ";
		}
		// comment
		if (isset($desc) && $desc != null)
		{
			$desc = sql_string($desc);
			$fil_desc = $and . " ( upper(jr_comment) like upper('%" . $desc . "%') or upper(jr_pj_number) like upper('%" . $desc . "%') " .
					" or upper(jr_internal)  like upper('%" . $desc . "%')
                          or jr_grpt_id in (select j_grpt from jrnx where j_text ~* '" . $desc . "')
                          or jr_id in (select jr_id from jrn_info where ji_value is not null and ji_value ~* '$desc')
                          )";
			$and = " and ";
		}
		//    Poste
		if (isset($accounting) && $accounting != null)
		{
			$fil_account = $and . "  jr_grpt_id in (select j_grpt
                         from jrnx where j_poste::text like '" . sql_string($accounting) . "%' )  ";
			$and = " and ";
		}
		// Quick Code
		if (isset($qcodesearch_op))
			$qcode = $qcodesearch_op;
		if (isset($qcode) && $qcode != null)
		{
			$fil_qcode = $and . "  jr_grpt_id in ( select j_grpt from
                       jrnx where trim(j_qcode) = upper(trim('" . sql_string($qcode) . "')))";
			$and = " and ";
		}

		// Only the unpaid
		if (isset($unpaid))
		{
			$fil_paid = $and . SQL_LIST_UNPAID_INVOICE;
			$and = " and ";
		}

		$g_user = new User(new Database());
		$g_user->Check();
		$g_user->check_dossier(dossier::id());

		if ($g_user->admin == 0 && $g_user->is_local_admin() == 0)
		{
			$fil_sec = $and . " jr_def_id in ( select uj_jrn_id " .
					" from user_sec_jrn where " .
					" uj_login='" . $_SESSION['g_user'] . "'" .
					" and uj_priv in ('R','W'))";
		}
		$where = $fil_ledger . $fil_amount . $fil_date . $fil_desc . $fil_sec . $fil_amount . $fil_qcode . $fil_paid . $fil_account.$fil_date_paid;
		$sql.=" where " . $where;
		return array($sql, $where);
	}

	/**
	 * @brief return a html string with the search_form
	 * \return a HTML string with the FORM
	 * \see build_search_sql
	 * \see search_form
	 * \see list_operation
	 */

	function display_search_form()
	{
		$r = '';
		$type = $this->type;

		if ($type == "")
			$type = 'ALL';
		$r.='<div id="search_form" style="display:none">';
		$r.=HtmlInput::anchor_hide('&#10761;', '$(\'search_form\').style.display=\'none\';');
		$r.=h2('Recherche','class="title"');
		$r.='<FORM METHOD="GET">';
		$r.=$this->search_form($type);
		$r.=HtmlInput::submit('search', _('Rechercher'));
		$r.=HtmlInput::hidden('ac', $_REQUEST['ac']);

		/*  when called from commercial.php some hidden values are needed */
		if (isset($_REQUEST['sa']))
			$r.= HtmlInput::hidden("sa", $_REQUEST['sa']);
		if (isset($_REQUEST['sb']))
			$r.= HtmlInput::hidden("sb", $_REQUEST['sb']);
		if (isset($_REQUEST['sc']))
			$r.= HtmlInput::hidden("sc", $_REQUEST['sc']);
		if (isset($_REQUEST['f_id']))
			$r.=HtmlInput::hidden("f_id", $_REQUEST['f_id']);

		$r.='</FORM>';

		$r.='</div>';
		$button = new IButton('tfs');
		$button->label = _("Filtrer");
		$button->javascript = "toggleHideShow('search_form','tfs');";

		$r.=$button->input();
		return $r;
	}

	/**
	 * @brief return the last p_limit operation into an array
	 * @param$p_limit is the max of operation to return
	 * \return $p_array of Follow_Up object
	 */

	function get_last($p_limit)
	{
		global $g_user;
		$filter_ledger = $g_user->get_ledger_sql('ALL', 3);
		$filter_ledger = str_replace('jrn_def_id', 'jr_def_id', $filter_ledger);
		$sql = "
			select jr_id,jr_pj_number,jr_date,to_char(jr_date,'DD.MM.YYYY') as jr_date_fmt,jr_montant, jr_comment,jr_internal,jrn_def_code
			from jrn
			join jrn_def on (jrn_def_id=jr_def_id)
			 where $filter_ledger
			order by jr_date desc , substring(jr_pj_number,'[0-9]+$')::numeric desc limit $p_limit";
		$array = $this->db->get_array($sql);
		return $array;
	}

	/**
	 * @brief retreive the jr_grpt_id from a ledger
	 * @param $p_what the column to seek
	 *    possible values are
	 *   - internal
	 * @param $p_value the value of the col.
	 */
	function search_group($p_what, $p_value)
	{
		switch ($p_what)
		{
			case 'internal':
				return $this->db->get_value('select jr_grpt_id from jrn where jr_internal=$1', array($p_value));
		}
	}

	/**
	 * @brief retrieve operation from  jrn
	 * @param $p_from periode (id)
	 * @param $p_to periode (id)
	 * @return an array
	 */
	function get_operation($p_from, $p_to)
	{
		global $g_user;
		$jrn = ($this->id == 0) ? 'and ' . $g_user->get_ledger_sql() : ' and jr_def_id = ' . $this->id;
		$sql = "select jr_id as id ,jr_internal as internal, " .
				"jr_pj_number as pj,jr_grpt_id," .
				" to_char(jr_date,'DDMMYY') as date_fmt, " .
				" jr_comment as comment, jr_montant as montant ," .
				" jr_grpt_id,jr_def_id" .
				" from jrn join jrn_def on (jr_def_id=jrn_def_id) where  " .
				" jr_date >= (select p_start from parm_periode where p_id = $1)
				 and  jr_date <= (select p_end from parm_periode where p_id  = $2)" .
				'  ' . $jrn . ' order by jr_date,substring(jr_pj_number,\'[0-9]+$\')::numeric asc';
		$ret = $this->db->get_array($sql, array($p_from, $p_to));
		return $ret;
	}

	/**
	 * @brief return the used VAT code with a rate > 0
	 * @return an array of tva_id,tva_label,tva_poste
	 */
	public function existing_vat()
	{
		if ($this->type == 'ACH')
		{
			$array = $this->db->get_array("select tva_id,tva_label,tva_poste from tva_rate where tva_rate != 0.0000 " .
					" and  exists (select qp_vat_code from quant_purchase
                                        where  qp_vat_code=tva_id and  exists (select j_id from jrnx where j_jrn_def = $1)) order by tva_id", array($this->id));
		}
		if ($this->type == 'VEN')
		{
			$array = $this->db->get_array("select tva_id,tva_label,tva_poste from tva_rate where tva_rate != 0.0000 " .
					" and  exists (select qs_vat_code from quant_sold
                                        where  qs_vat_code=tva_id and  exists (select j_id from jrnx where j_jrn_def = $1)) order by tva_id", array($this->id));
		}
		return $array;
	}

	/**
	 * @brief get the amount of vat for a given jr_grpt_id from the table
	 * quant_purchase
	 * @param the jr_grpt_id
	 * @return array price=htva, [1] =  vat,
	 * @note
	 * @see
	  @code
	  array
	  'price' => string '91.3500' (length=7)
	  'vat' => string '0.0000' (length=6)
	  'priv' => string '0.0000' (length=6)
	  'tva_nd_recup' => string '0.0000' (length=6)

	  @endcode
	 */
	function get_other_amount($p_jr_id)
	{
		if ($this->type == 'ACH')
		{
			$array = $this->db->get_array('select sum(qp_price) as price,sum(qp_vat) as vat ' .
					',sum(coalesce(qp_nd_amount,0)+coalesce(qp_dep_priv,0)) as priv' .
					',sum(coalesce(qp_nd_tva_recup,0)+coalesce(qp_nd_tva,0)) as tva_nd' .
					',sum(qp_vat_sided)  as tva_np' .
					'  from quant_purchase join jrnx using(j_id)
                                        where  j_grpt=$1 ', array($p_jr_id));
			$ret = $array[0];
		}
		if ($this->type == 'VEN')
		{
			$array = $this->db->get_array('select sum(qs_price) as price,sum(qs_vat) as vat ' .
					',0 as priv' .
					',0 as tva_nd' .
                                        ',sum(qs_vat_sided)  as tva_np' .
					'  from quant_sold join jrnx using(j_id)
                                        where  j_grpt=$1 ', array($p_jr_id));
			$ret = $array[0];
		}
		return $ret;
	}

	/**
	 * @brief get the amount of vat for a given jr_grpt_id from the table
	 * quant_purchase
	 * @param the jr_grpt_id
	 * @return array of sum_vat, tva_label
	 * @note
	 * @see
	  @code

	  @endcode
	 */
	function vat_operation($p_jr_id)
	{
		if ($this->type == 'ACH')
		{
			$array = $this->db->get_array('select coalesce(sum(qp_vat),0) as sum_vat,tva_id
                                        from quant_purchase as p right join tva_rate on (qp_vat_code=tva_id)  join jrnx using(j_id)
                                        where tva_rate !=0.0 and j_grpt=$1 group by tva_id', array($p_jr_id));
		}
		if ($this->type == 'VEN')
		{
			$array = $this->db->get_array('select coalesce(sum(qs_vat),0) as sum_vat,tva_id
                                        from quant_sold as p right join tva_rate on (qs_vat_code=tva_id)  join jrnx using(j_id)
                                        where tva_rate !=0.0 and j_grpt=$1 group by tva_id', array($p_jr_id));
		}
		return $array;
	}

	/**
	 * @brief retrieve amount of previous periode
	 * @param $p_to frmo the start of the exercise until $p_to
	 * @return $array with vat, price,other_amount
	 * @note
	 * @see
	  @code
	  array
	  'price' => string '446.1900' (length=8)
	  'vat' => string '21.7600' (length=7)
	  'priv' => string '0.0000' (length=6)
	  'tva_nd_recup' => string '0.0000' (length=6)
	  'tva' =>
	  array
	  0 =>
	  array
	  'sum_vat' => string '13.7200' (length=7)
	  'tva_id' => string '1' (length=1)
	  1 =>
	  array
	  'sum_vat' => string '8.0400' (length=6)
	  'tva_id' => string '3' (length=1)
	  2 =>
	  array
	  'sum_vat' => string '0.0000' (length=6)
	  'tva_id' => string '4' (length=1)

	  @endcode
	 */
	function previous_amount($p_to)
	{
		/* get the first periode of exercise */
		$periode = new Periode($this->db, $p_to);
		$exercise = $periode->get_exercice();
		list ($min, $max) = $periode->get_limit($exercise);
                // transform min into date
                $min_date=$min->first_day();
                // transform $p_to  into date
                $periode_max=new Periode($this->db,$p_to);
                $max_date=$periode_max->first_day();
                bcscale(2);
		// min periode
		if ($this->type == 'ACH')
		{
			/*  get all amount exclude vat */
			$sql = "select coalesce(sum(qp_price),0) as price" .
					" ,coalesce(sum(qp_vat),0) as vat " .
					',coalesce(sum(qp_dep_priv),0) as priv' .
					',coalesce(sum(qp_vat_sided),0) as reversed' .
					',coalesce(sum(qp_nd_tva_recup),0)+coalesce(sum(qp_nd_tva),0) as tva_nd' .
					',coalesce(sum(qp_vat_sided),0) as tva_np' .
					'  from quant_purchase join jrnx using(j_id) ' .
					" where j_date >= to_date($1,'DD.MM.YYYY') and j_date < to_date($2,'DD.MM.YYYY') ".
                                ' and j_jrn_def = $3';
			$array = $this->db->get_array($sql, array($min_date, $max_date,$this->id));

			$ret = $array[0];
			/* retrieve all vat code */
			$array = $this->db->get_array("select coalesce(sum(qp_vat),0) as sum_vat,tva_id
                                        from quant_purchase as p right join tva_rate on (qp_vat_code=tva_id)  join jrnx using(j_id)
                                        where tva_rate !=0 and  j_date >= to_date($1,'DD.MM.YYYY') and j_date < to_date($2,'DD.MM.YYYY') 
                                        and j_jrn_def = $3
                                        group by tva_id", 
                                array($min_date, $max_date,$this->id));
			$ret['tva'] = $array;
		}
		if ($this->type == 'VEN')
		{
			/*  get all amount exclude vat */
			$sql = "select coalesce(sum(qs_price),0) as price" .
					" ,coalesce(sum(qs_vat),0) as vat " .
					',0 as priv' .
					',0 as tva_nd' .
                                        ',coalesce(sum(qs_vat_sided),0) as tva_np' .
					'  from quant_sold join jrnx using(j_id) ' .
					" where j_date >= to_date($1,'DD.MM.YYYY') and j_date < to_date($2,'DD.MM.YYYY') ".
                                ' and j_jrn_def = $3';
			$array = $this->db->get_array($sql, array($min_date, $max_date,$this->id));
			$ret = $array[0];
			/* retrieve all vat code */
			$array = $this->db->get_array("select coalesce(sum(qs_vat),0) as sum_vat,tva_id
                                        from quant_sold as p right join tva_rate on (qs_vat_code=tva_id)  join jrnx using(j_id)
                                        where tva_rate !=0 and
                                        j_date >= to_date($1,'DD.MM.YYYY') and j_date < to_date($2,'DD.MM.YYYY') 
                                        and j_jrn_def = $3
                                        group by tva_id", array($min_date, $max_date,$this->id));
			$ret['tva'] = $array;
		}
                if ($this->type=="FIN") 
                {
                        
                    /* find the quick code of this ledger */
                    $ledger=new Acc_Ledger_Fin($this->db,$this->id);
                    $qcode=$ledger->get_bank();
                    $bank_card=new Fiche($this->db,$qcode);
                    
                    /*add the amount from Opening Writing                  */
                    $cond=sprintf(" j_jrn_def <> %d  and j_date >= to_date('%s','DD.MM.YYYY') and j_date < to_date('%s','DD.MM.YYYY') ",$this->id,$min_date,$max_date);
                    $saldo = $bank_card->get_bk_balance ($cond);
                    $ret['amount']=bcsub($saldo['debit'],$saldo['credit']);
                }
		return $ret;
	}

	////////////////////////////////////////////////////////////////////////////////
	// TEST MODULE
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * @brief this function is intended to test this class
	 */
	static function test_me($pCase = '')
	{
		if ($pCase == '')
		{
			echo Acc_Reconciliation::$javascript;
			html_page_start();
			$cn = new Database(dossier::id());
			$_SESSION['g_user'] = 'phpcompta';
			$_SESSION['g_pass'] = 'phpcompta';

			$id = (isset($_REQUEST['p_jrn'])) ? $_REQUEST['p_jrn'] : -1;
			$a = new Acc_Ledger($cn, $id);
			$a->with_concerned = true;
			// Vide
			echo '<FORM method="post">';
			echo $a->select_ledger()->input();
			echo HtmlInput::submit('go', 'Test it');
			echo '</form>';
			if (isset($_POST['go']))
			{
				echo "Ok ";
				echo '<form method="post">';
				echo $a->show_form();
				echo HtmlInput::submit('post_id', 'Try me');
				echo '</form>';
				// Show the predef operation
				// Don't forget the p_jrn
				echo '<form>';
				echo dossier::hidden();
				echo '<input type="hidden" value="' . $id . '" name="p_jrn">';
				$op = new Pre_operation($cn);
				$op->p_jrn = $id;
				$op->od_direct = 't';
				if ($op->count() != 0)
				{
					echo HtmlInput::submit('use_opd', 'Utilisez une opération pr&eacute;d&eacute;finie',"","smallbutton");
					echo $op->show_button();
				}
				echo '</form>';
				exit('test_me');
			}

			if (isset($_POST['post_id']))
			{

				echo '<form method="post">';
				echo $a->show_form($_POST, 1);
				echo HtmlInput::button('add', 'Ajout d\'une ligne', 'onClick="quick_writing_add_row()"');
				echo HtmlInput::submit('save_it', _("Sauver"));
				echo '</form>';
				exit('test_me');
			}
			if (isset($_POST['save_it']))
			{
				print 'saving';
				$array = $_POST;
				$array['save_opd'] = 1;
				try
				{
					$a->save($array);
				}
				catch (Exception $e)
				{
					alert($e->getMessage());
					echo '<form method="post">';

					echo $a->show_form($_POST);
					echo HtmlInput::submit('post_id', 'Try me');
					echo '</form>';
				}
				return;
			}
			// The GET at the end because automatically repost when you don't
			// specify the url in the METHOD field
			if (isset($_GET['use_opd']))
			{
				$op = new Pre_op_advanced($cn);
				$op->set_od_id($_REQUEST['pre_def']);
				//$op->p_jrn=$id;

				$p_post = $op->compute_array();

				echo '<FORM method="post">';

				echo $a->show_form($p_post);
				echo HtmlInput::submit('post_id', 'Use predefined operation');
				echo '</form>';
				return;
			}
		}// if case = ''
		///////////////////////////////////////////////////////////////////////////
		// search
		if ($pCase == 'search')
		{
			html_page_start();
			$cn = new Database(dossier::id());
			$ledger = new Acc_Ledger($cn, 0);
			$_SESSION['g_user'] = 'phpcompta';
			$_SESSION['g_pass'] = 'phpcompta';
			echo $ledger->search_form('ALL');
		}
		///////////////////////////////////////////////////////////////////////////
		// reverse
		// Give yourself the var and check in your tables
		///////////////////////////////////////////////////////////////////////////
		if ($pCase == 'reverse')
		{
			$cn = new Database(dossier::id());
			$jr_internal = 'OD-01-272';
			try
			{
				$cn->start();
				$jrn_def_id = $cn->get_value('select jr_def_id from jrn where jr_internal=$1', array($jr_internal));
				$ledger = new Acc_Ledger($cn, $jrn_def_id);
				$ledger->jr_id = $cn->get_value('select jr_id from jrn where jr_internal=$1', array($jr_internal));

				echo "Ouvrez le fichier " . __FILE__ . " à la ligne " . __LINE__ . " pour changer jr_internal et vérifier le résultat de l'extourne";

				$ledger->reverse('01.07.2010');
			}
			catch (Exception $e)
			{
				$cn->rollback();
				var_dump($e);
			}
			$cn->commit();
		}
	}

	/**
	 * create an array of the existing cat, to be used in a checkbox form
	 *
	 */
	static function array_cat()
	{
		$r = array(
			array('cat' => 'VEN', 'name' => _("Journaux de vente")),
			array('cat' => 'ACH', 'name' => _("Journaux d'achat")),
			array('cat' => 'FIN', 'name' => _("Journaux Financier")),
			array('cat' => 'ODS', 'name' => _("Journaux d'Opérations diverses"))
		);
		return $r;
	}

	/**
	 * Retrieve the third : supplier for purchase, customer for sale, bank for fin,
	 * @param $p_jrn_type type of the ledger FIN, VEN ACH or ODS
	 */
	function get_tiers($p_jrn_type, $jr_id)
	{
		if ($p_jrn_type == 'ODS')
			return ' ';
		$tiers = '';
		switch ($p_jrn_type)
		{
			case 'VEN':
				$tiers = $this->db->get_value('select max(qs_client) from quant_sold join jrnx using (j_id) join jrn on (jr_grpt_id=j_grpt) where jrn.jr_id=$1', array($jr_id));
				break;
			case 'ACH':
				$tiers = $this->db->get_value('select max(qp_supplier) from quant_purchase join jrnx using (j_id) join jrn on (jr_grpt_id=j_grpt) where jrn.jr_id=$1', array($jr_id));

				break;
			case 'FIN':
				$tiers = $this->db->get_value('select qf_other from quant_fin where jr_id=$1', array($jr_id));
				break;
		}
		if ($this->db->count() == 0)
			return '';
		$name = $this->db->get_value('select ad_value from fiche_detail where ad_id=1 and f_id=$1', array($tiers));
		$first_name = $this->db->get_value('select ad_value from fiche_detail where ad_id=32 and f_id=$1', array($tiers));
		return $name . ' ' . $first_name;
	}

	/**
	 * @brief listing of all ledgers
	 * @return HTML string
	 */
	function listing()
	{
		$str_dossier = dossier::get();
		$base_url = "?" . dossier::get() . "&ac=" . $_REQUEST['ac'];

		$r = "";
                $r.=_('Filtre')." ".HtmlInput::filter_table("cfgledger_table_id", "0", "1");
		$r.='<TABLE id="cfgledger_table_id" class="vert_mtitle">';
		$r.='<TR><TD class="first"><A HREF="' . $base_url . '&sa=add">' . _('Ajout journal') . ' </A></TD></TR>';
		$ret = $this->db->exec_sql("select distinct jrn_def_id,jrn_def_name,
                       jrn_def_class_deb,jrn_def_class_cred,jrn_def_type
                       from jrn_def order by jrn_def_name");
		$Max = Database::num_row($ret);


		for ($i = 0; $i < $Max; $i++)
		{
			$l_line = Database::fetch_array($ret, $i);
			$url = $base_url . "&sa=detail&p_jrn=" . $l_line['jrn_def_id'];
			$r.=sprintf('<TR><TD><A HREF="%s">%s</A></TD></TR>', $url, h($l_line['jrn_def_name']).' ('.$l_line['jrn_def_type'].')');
		}
		$r.= "</TABLE>";
		return $r;
	}

	/**
	 * display detail of a ledger
	 *
	 */
	function display_ledger()
	{
		if ($this->load() == -1)
		{
			throw new Exception(_("Journal n'existe pas"), -1);
		}
		$type = $this->jrn_def_type;
		$name = $this->jrn_def_name;
		$code = $this->jrn_def_code;
                $str_add_button="";
		/* widget for searching an account */
		$wSearch = new IPoste();
		$wSearch->set_attribute('ipopup', 'ipop_account');
		$wSearch->set_attribute('account', 'p_jrn_class_deb');
		$wSearch->set_attribute('no_overwrite', '1');
		$wSearch->set_attribute('noquery', '1');
		$wSearch->table = 3;
		$wSearch->name = "p_jrn_class_deb";
		$wSearch->size = 20;
		$wSearch->value = $this->jrn_def_class_deb;
		$search = $wSearch->input();

		$wPjPref = new IText();
		$wPjPref->name = 'jrn_def_pj_pref';
		$wPjPref->value = $this->jrn_def_pj_pref;
		$pj_pref = $wPjPref->input();

		$wPjSeq = new INum();
		$wPjSeq->value = 0;
		$wPjSeq->name = 'jrn_def_pj_seq';
		$pj_seq = $wPjSeq->input();
		$last_seq = $this->get_last_pj();
		$name = $this->jrn_def_name;

		$hidden = HtmlInput::hidden('p_jrn', $this->id);
		$hidden.= HtmlInput::hidden('sa', 'detail');
		$hidden.= dossier::hidden();
		$hidden.=HtmlInput::hidden('p_jrn_deb_max_line', 10);
		$hidden.=HtmlInput::hidden('p_ech_lib', 'echeance');
		$hidden.=HtmlInput::hidden('p_jrn_type', $type);

		$min_row = new INum("min_row",$this->jrn_deb_max_line);
		$min_row->prec=0;
                
                $description=new ITextarea('p_description');
                $description->style='class="itextarea" style="margin:0px;"';
                $description->value=$this->jrn_def_description;
                $str_description=$description->input();

		/* Load the card */
		$card = $this->get_fiche_def();
		$rdeb = explode(',', $card['deb']);
		$rcred = explode(',', $card['cred']);
		/* Numbering (only FIN) */
		$num_op = new ICheckBox('numb_operation');
		if ($this->jrn_def_num_op == 1)
			$num_op->selected = true;
		/* bank card */
		$qcode_bank = '';
		if ($type == 'FIN')
		{
			$f_id = $this->jrn_def_bank;
			if (isNumber($f_id) == 1)
			{
				$fBank = new Fiche($this->db, $f_id);
				$qcode_bank = $fBank->get_quick_code();
			}
		}
		$new = 0;
		$cn = $this->db;
		echo $hidden;
		require_once NOALYSS_INCLUDE.'/template/param_jrn.php';
	}

	/**
	 * Verify before update
	 *
	 * @param type $array
	 *   'p_jrn' => string '3' (length=1)
	  'sa' => string 'detail' (length=6)
	  'gDossier' => string '82' (length=2)
	  'p_jrn_deb_max_line' => string '10' (length=2)
	  'p_ech_lib' => string 'echeance' (length=8)
	  'p_jrn_type' => string 'ACH' (length=3)
	  'p_jrn_name' => string 'Achat' (length=5)
	  'jrn_def_pj_pref' => string 'ACH' (length=3)
	  'jrn_def_pj_seq' => string '0' (length=1)
	  'FICHECRED' =>
	  array
	  0 => string '4' (length=1)
	  'FICHEDEB' =>
	  array
	  0 => string '7' (length=1)
	  1 => string '5' (length=1)
	  2 => string '13' (length=2)
	  'update' => string 'Sauve' (length=5
	 * @exception is throw is test are not valid
	 */
	function verify_ledger($array)
	{
		extract($array);
		try
		{
			if (isNumber($p_jrn) == 0)
				throw new Exception("Id invalide");
			if (isNumber($p_jrn_deb_max_line) == 0)
				throw new Exception(_("Nombre de ligne incorrect"));
			if (trim($p_jrn_name) == "")
				throw new Exception("Nom de journal invalide");
			if ($this->db->get_value("select count(*) from jrn_def where jrn_def_name=$1 and jrn_Def_id<>$2", array($p_jrn_name, $p_jrn)) > 0)
				throw new Exception(_("Un journal avec ce nom existe déjà"));
			if ($p_jrn_type == 'FIN')
			{
				$a = new Fiche($this->db);
				$result = $a->get_by_qcode(trim(strtoupper($_POST['bank'])), false);
				if ($result == 1)
					throw new Exception(_("Aucun compte en banque n'est donné"));
			}
                        if ($p_jrn_type == "-1") {
                            throw new Exception(_('Choix du type de journal est obligatoire'));
                        }
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * update a ledger
	 * @param type $array  normally post
	 * @see verify_ledger
	 */
	function update($array = '')
	{
		if ($array == null)
			throw new Exception('save cannot use a empty array');

		extract($array);
		$this->jrn_def_id = $p_jrn;
		$this->jrn_def_name = $p_jrn_name;
		$this->jrn_def_ech_lib = $p_ech_lib;
		$this->jrn_def_max_line_deb = ($p_jrn_deb_max_line<1)?1:$p_jrn_deb_max_line;
		$this->jrn_def_type = $p_jrn_type;
		$this->jrn_def_pj_pref = $jrn_def_pj_pref;
		$this->jrn_def_fiche_deb = (isset($FICHEDEB)) ? join($FICHEDEB, ',') : "";
		$this->jrn_deb_max_line=($min_row<1)?1:$min_row;
		$this->jrn_def_description=$p_description;
		switch ($this->jrn_def_type)
		{
			case 'ACH':
			case 'VEN':
				$this->jrn_def_fiche_cred = (isset($FICHECRED)) ? join($FICHECRED, ',') : '';
				break;
			case 'ODS':
				$this->jrn_def_class_deb = $p_jrn_class_deb;
				$this->jrn_def_fiche_cred=null;
				break;
			case 'FIN':
				$a = new Fiche($this->db);
				$result = $a->get_by_qcode(trim(strtoupper($_POST['bank'])), false);
				$bank = $a->id;
				$this->jrn_def_bank = $bank;
				if ($result == -1)
					throw new Exception(_("Aucun compte en banque n'est donné"));
				$this->jrn_def_num_op = (isset($numb_operation)) ? 1 : 0;
				break;
		}

		parent::update();
		//Reset sequence if needed
		if ($jrn_def_pj_seq != 0)
		{
			$Res = $this->db->alter_seq("s_jrn_pj" . $p_jrn, $jrn_def_pj_seq);
		}
	}

	function input_paid()
	{
		$r = '';
		$r.='<div id="payment"> ';
		$r.='<h2> ' . _('Payé par') . ' </h2>';
		$mp = new Acc_Payment($this->db);
		$mp->set_parameter('ledger_source', $this->id);
		$r.=$mp->select();
		$r.='</div>';
		return $r;
	}

	/**
	 * display screen to enter a new ledger
	 */
	function input_new()
	{
            $retry=HtmlInput::default_value_post("sa", "");
//            if ( $retry == "add") {
                $default_type=HtmlInput::default_value_post("p_jrn_type", -1);
                $previous_jrn_def_pj_pref=HtmlInput::default_value_post("jrn_def_pj_pref","");
                $previous_p_description=HtmlInput::default_value_post("p_description","");
                $previous_p_jrn_name=HtmlInput::default_value_post('p_jrn_name','');
                $previous_p_jrn_type = HtmlInput::default_value_post("p_jrn_type","");
//            }
                global $g_user;
                $f_add_button=new ISmallButton('add_card');
                $f_add_button->label=_('Créer une nouvelle fiche');
                $f_add_button->tabindex=-1;
                $f_add_button->set_attribute('jrn',-1);
                $f_add_button->javascript=" this.jrn=-1;select_card_type({type_cat:4});";

                $str_add_button="";
                if ($g_user->check_action(FICADD)==1)
                {
                        $str_add_button=$f_add_button->input();
                }
		$wSearch = new IPoste();
		$wSearch->table = 3;
		$wSearch->set_attribute('ipopup', 'ipop_account');
		$wSearch->set_attribute('account', 'p_jrn_class_deb');
		$wSearch->set_attribute('no_overwrite', '1');
		$wSearch->set_attribute('noquery', '1');

		$wSearch->name = "p_jrn_class_deb";
		$wSearch->size = 20;

		$search = $wSearch->input();
                // default for ACH
                $default_deb_purchase = $this->get_default_card('ACH', 'D');
                $default_cred_purchase = $this->get_default_card('ACH', 'C');
                
                // default for VEN
                $default_deb_sale      = $this->get_default_card('VEN', 'D');
                $default_cred_sale     = $this->get_default_card('VEN', 'C');
                
                // default for FIN
                $default_fin           = $this->get_default_card("FIN", "");
                
                //default ods
                $default_ods          = $this->get_default_card("ODS", "");
                
		/* construct all the hidden */
		$hidden = HtmlInput::hidden('p_jrn', -1);
		$hidden.= HtmlInput::hidden('p_action', 'jrn');
		$hidden.= HtmlInput::hidden('sa', 'add');
		$hidden.= dossier::hidden();
		$hidden.=HtmlInput::hidden('p_jrn_deb_max_line', 10);
		$hidden.=HtmlInput::hidden('p_ech_lib', 'echeance');

		/* properties of the ledger */
		$name = $previous_p_jrn_name;
		$code = "";
		$wType = new ISelect();
                $a_jrn= $this->db->make_array("select '-1',' -- "._("choix du type de journal")." -- ' union select jrn_type_id,jrn_desc from jrn_type");
                $wType->selected='-1';
		$wType->value =$a_jrn;
		$wType->name = "p_jrn_type";
		$wType->id= "p_jrn_type_select_id";
                $wType->javascript=' onchange="show_ledger_div()"';
                $wType->selected=$default_type;
		$type = $wType->input();
		$rcred = $rdeb = array();
                $wPjPref = new IText();
		$wPjPref->name = 'jrn_def_pj_pref';
                $wPjPref->value=$previous_jrn_def_pj_pref;
		$pj_pref = $wPjPref->input();
		$pj_seq = '';
		$last_seq = 0;
		$new = 1;
                $description=new ITextarea('p_description');
                $description->style='class="itextarea" style="margin:0px;"';
                $description->value=$previous_p_description;
                $str_description=$description->input();
		/* bank card */
		$qcode_bank = '';
		/* Numbering (only FIN) */
		$num_op = new ICheckBox('numb_operation');
		echo dossier::hidden();
		echo HtmlInput::hidden('ac', $_REQUEST['ac']);
                echo $hidden;
                
		$cn = $this->db;
		$min_row = new INum("min_row",MAX_ARTICLE);
		$min_row->prec=0;
		require_once NOALYSS_INCLUDE.'/template/param_jrn.php';
	}

	/**
	 * Insert a new ledger
	 * @param type $array normally $_POST
	 * @see verify_ledger
	 */
	function save_new($array)
	{
		$this->load();
		extract($array);
		$this->jrn_def_id = -1;
		$this->jrn_def_name = $p_jrn_name;
		$this->jrn_def_ech_lib = $p_ech_lib;
		$this->jrn_def_max_line_deb = $p_jrn_deb_max_line;
		$this->jrn_def_type = $p_jrn_type;
		$this->jrn_def_pj_pref = $jrn_def_pj_pref;
		$this->jrn_def_fiche_deb = (isset($FICHEDEB)) ? join($FICHEDEB, ',') : "";
		$this->jrn_deb_max_line=$min_row;
		$this->jrn_def_code = sprintf("%s%02d", trim(substr($this->jrn_def_type, 0, 1)), Acc_Ledger::next_number($this->db, $this->jrn_def_type));
                $this->jrn_def_description=$p_description;
		switch ($this->jrn_def_type)
		{
			case 'ACH':
			case 'VEN':
				$this->jrn_def_fiche_cred = (isset($FICHECRED)) ? join($FICHECRED, ',') : '';

				break;
			case 'ODS':
				$this->jrn_def_class_deb = $p_jrn_class_deb;
				$this->jrn_def_fiche_cred = null;
				break;
			case 'FIN':
				$a = new Fiche($this->db);
				$result = $a->get_by_qcode(trim(strtoupper($_POST['bank'])), false);
				$bank = $a->id;
				$this->jrn_def_bank = $bank;
				if ($result == -1)
					throw new Exception(_("Aucun compte en banque n'est donné"));
				$this->jrn_def_num_op = (isset($numb_operation)) ? 1 : 0;
				break;
		}

		parent::insert();
	}

	/**
	 * delete a ledger IF is not already used
	 * @exeption : cannot delete
	 */
	function delete_ledger()
	{
		try
		{
			if ($this->db->get_value("select count(jr_id) from jrn where jr_def_id=$1", array($this->jrn_def_id)) > 0)
				throw new Exception(_("Impossible d'effacer un journal qui contient des opérations"));
			parent::delete();
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
        /**
         * Get operation from the ledger type before, after or with the 
         * given date . The array is filtered by the ledgers granted to the 
         * user
         * @global type $g_user
         * @param $p_date Date (d.m.Y)
         * @param $p_ledger_type VEN ACH 
         * @param type $sql_op < > or =
         * @return array from jrn (jr_id, jr_internal, jr_date, jr_comment,jr_pj_number,jr_montant)
         * @throws Exception
         */
	function get_operation_date($p_date,$p_ledger_type,$sql_op)
	{
		global $g_user;
		switch ($p_ledger_type)
		{
			case 'ACH':
				$filter=$g_user->get_ledger_sql('ACH',3);
				break;
			case 'VEN':
				$filter=$g_user->get_ledger_sql('VEN',3);
				break;
			default:
				throw new Exception ('Ledger_type invalid : '.$p_ledger_type);
		}


		$sql = "select jr_id, jr_internal, jr_date, jr_comment,jr_pj_number,jr_montant
				from jrn
				join jrn_def on (jrn_def_id=jr_def_id)
				where
				jr_ech is not null
				and jr_ech $sql_op to_date($1,'DD.MM.YYYY')
				and coalesce (jr_rapt,'xx') <> 'paid'
				and $filter
				";
		$array=$this->db->get_array($sql,array($p_date));
		return $array;
	}
	/**
	 * @brief get info from supplier to pay today
	 */
	function get_supplier_now()
	{
		$array=$this->get_operation_date(Date('d.m.Y'), 'ACH', '=');
		return $array;
	}
	/**
	 * @brief get info from supplier not yet paid
	 */
	function get_supplier_late()
	{
		$array=$this->get_operation_date(Date('d.m.Y'), 'ACH', '<');
		return $array;
	}
	/**
	 * @brief get info from customer to pay today
	 */
	function get_customer_now()
	{
		$array=$this->get_operation_date(Date('d.m.Y'), 'VEN', '=');
		return $array;
	}
	/**
	 * @brief get info from customer not yet paid
	 */
	function get_customer_late()
	{
		$array=$this->get_operation_date(Date('d.m.Y'), 'VEN', '<');
		return $array;
	}
        function convert_from_follow($p_ag_id)
        {
            global $g_user;
            if (isNumber($p_ag_id)==0) return null;
            if (! $g_user->can_read_action($p_ag_id)) die (_('Action non accessible'));
            $array=array();
            
            // retrieve info from action_gestion
            $tiers_id=$this->db->get_value('select f_id_dest from action_gestion where ag_id=$1',array($p_ag_id));
            if ( $this->db->size() !=0 )
                $qcode=$this->db->get_value('select j_qcode from vw_poste_qcode where f_id=$1',array($tiers_id));
            else
                $qcode="";
            
            $comment=$this->db->get_value('select ag_title from action_gestion where ag_id=$1',array($p_ag_id));
            $array['e_client']=$qcode;
            $array['e_comm']=$comment;
            
            // retrieve info from action_detail
            $a_item=$this->db->get_array('select f_id,ad_text,ad_pu,ad_quant,ad_tva_id,ad_tva_amount,j_qcode 
                    from 
                  action_detail 
                  left join vw_poste_qcode using(f_id)
                  where
                    ag_id=$1',array($p_ag_id));
            
            $array['nb_item']=($this->nb > count($a_item))?$this->nb:count($a_item);
            for ($i=0;$i<count($a_item);$i++)
            {
                $array['e_march'.$i]=$a_item[$i]['j_qcode'];
                $array['e_march'.$i.'_label']=$a_item[$i]['ad_text'];
                $array['e_march'.$i.'_price']=$a_item[$i]['ad_pu'];
                $array['e_march'.$i.'_tva_id']=$a_item[$i]['ad_tva_id'];
                $array['e_march'.$i.'_tva_amount']=$a_item[$i]['ad_tva_amount'];
                $array['e_quant'.$i]=$a_item[$i]['ad_quant'];
                
            }
            return $array;
                       
        }
        /**
         * Retrieve the label of an accounting
         * @param $p_value tmp_pcmn.pcm_val
         * @return string
         */
        protected function find_label($p_value)
        {
            $lib=$this->db->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',array($p_value));
            return $lib;
        }
        /**
         * Let you select the repository before confirming a sale or a purchase.
         * Returns an empty string if the company doesn't use stock
         * @brief Let you select the repository before confirming a sale or a purchase.
         * @global type $g_parameter check if company is using stock
         * @param type $p_readonly 
         * @param type $p_repo
         * @return string
         */
        public function select_depot($p_readonly, $p_repo)
        {
            global $g_parameter;
            $r=($p_readonly==false)?'<div id="repo_div_id" style="height:185px;height:10rem;">':'<div id="repo_div_id" >';
            // Show the available repository
            if ($g_parameter->MY_STOCK=='Y')
            {
                $sel=HtmlInput::select_stock($this->db, 'repo', 'W');
                $sel->readOnly=$p_readonly;
                if ($p_readonly==true)
                    $sel->selected=$p_repo;
                $r.="<p class=\"decale\">"._('Dans le dépôt')." : ";
                $r.=$sel->input();
                $r.='</p>';
            } else
            {
                $r.='<span class="notice">'.'Stock non utilisé'.'</span>';
            }
            $r.='</div>';
            return $r;
            
        }
        /**
         * Create a button to encode a new operation into the same ledger
         * @return string
         */
        function button_new_operation()
        {
            $url=http_build_query(array('ac'=>$_REQUEST['ac'],'gDossier'=>$_REQUEST['gDossier'],'p_jrn'=>$_REQUEST['p_jrn']));
            $button = HtmlInput::button_anchor(_("Nouvelle opération"), 'do.php?'.$url);
            return '<p>'.$button.'</p>';
        }

}
?>