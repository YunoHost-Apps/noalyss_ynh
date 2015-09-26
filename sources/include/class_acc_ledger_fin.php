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

/**\file
 * \brief the class Acc_Ledger_Fin inherits from Acc_Ledger, this
 * object permit to manage the financial ledger
 */
require_once NOALYSS_INCLUDE.'/class_idate.php';
require_once NOALYSS_INCLUDE.'/class_icard.php';
require_once NOALYSS_INCLUDE.'/class_ispan.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_iconcerned.php';
require_once NOALYSS_INCLUDE.'/class_ifile.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_acc_reconciliation.php';

class Acc_Ledger_Fin extends Acc_Ledger
{

	function __construct($p_cn, $p_init)
	{
		parent::__construct($p_cn, $p_init);
		$this->type = 'FIN';
	}

	/**
         * Verify that the data are correct before inserting or confirming
         * @brief verify the data 
	 * @param an array (usually $_POST)
	 * @return String
	 * @throw Exception on error occurs
	 */

	public function verify($p_array)
	{
		global $g_user;
                if (is_array($p_array ) == false || empty($p_array))
                    throw new Exception ("Array empty");
               /*
                * Check needed value
                */
                check_parameter($p_array,'p_jrn');

                
		extract($p_array);
		/* check for a double reload */
		if (isset($mt) && $this->db->count_sql('select jr_mt from jrn where jr_mt=$1', array($mt)) != 0)
			throw new Exception(_('Double Encodage'), 5);

		/* check if we can write into this ledger */
		if ($g_user->check_jrn($p_jrn) != 'W')
			throw new Exception(_('Accès interdit'), 20);

		/* check if there is a bank account linked to the ledger */
		$bank_id = $this->get_bank();

		if ($this->db->count() == 0)
			throw new Exception("Ce journal n'a pas de compte en banque, allez dans paramètre->journal pour régler cela");
		/* check if the accounting of the bank is correct */
		$fBank = new Fiche($this->db, $bank_id);
		$bank_accounting = $fBank->strAttribut(ATTR_DEF_ACCOUNT);
		if (trim($bank_accounting) == '')
			throw new Exception('Le poste comptable du compte en banque de ce journal est invalide');

		/* check if the account exists */
		$poste = new Acc_Account_Ledger($this->db, $bank_accounting);
		if ($poste->load() == false)
			throw new Exception('Le poste comptable du compte en banque de ce journal est invalide');
		if ($chdate != 1 && $chdate != 2) throw new Exception ('Le choix de date est invalide');
		if ( $chdate == 1 )
		{
			/*  check if the date is valid */
			if (isDate($e_date) == null)
			{
				throw new Exception('Date invalide', 2);
			}
			$oPeriode = new Periode($this->db);
			if ($this->check_periode() == false)
			{
				$periode = $oPeriode->find_periode($e_date);
			}
			else
			{
				$oPeriode->p_id = $periode;
				list ($min, $max) = $oPeriode->get_date_limit();
				if (cmpDate($e_date, $min) < 0 ||
						cmpDate($e_date, $max) > 0)
					throw new Exception(_('Date et periode ne correspondent pas'), 6);
			}

			/* check if the periode is closed */
			if ($this->is_closed($periode) == 1)
			{
				throw new Exception(_('Periode fermee'), 6);
			}

			/* check if we are using the strict mode */
			if ($this->check_strict() == true)
			{
				/* if we use the strict mode, we get the date of the last
				operation */
				$last_date = $this->get_last_date();
				if ($last_date != null && cmpDate($e_date, $last_date) < 0)
					throw new Exception(_('Vous utilisez le mode strict la dernière operation est à la date du ')
							. $last_date . _(' vous ne pouvez pas encoder à une date antérieure'), 15);
			}
		}

		$acc_pay = new Acc_Operation($this->db);

		$nb = 0;
		$tot_amount = 0;
		//----------------------------------------
		// foreach item
		//----------------------------------------
		for ($i = 0; $i < $nb_item; $i++)
		{
			if (strlen(trim(${'e_other' . $i})) == 0)
				continue;
			/* check if amount are numeric and */
			if (isNumber(${'e_other' . $i . '_amount'}) == 0)
				throw new Exception('La fiche ' . ${'e_other' . $i} . 'a un montant invalide [' . ${'e_other' . $i . '_amount'} . ']', 6);

			/* compute the total */
			$tot_amount+=round(${'e_other' . $i . '_amount'}, 2);
			/* check if all card has a ATTR_DEF_ACCOUNT */
			$fiche = new Fiche($this->db);
			$fiche->get_by_qcode(${'e_other' . $i});
			if ($fiche->empty_attribute(ATTR_DEF_ACCOUNT) == true)
				throw new Exception('La fiche ' . ${'e_other' . $i} . 'n\'a pas de poste comptable', 8);

			$sposte = $fiche->strAttribut(ATTR_DEF_ACCOUNT);
			// if 2 accounts, take only the debit one for customer
			if (strpos($sposte, ',') != 0)
			{
				$array = explode(',', $sposte);
				$poste_val = $array[1];
			}
			else
			{
				$poste_val = $sposte;
			}
			/* The account exists */
			$poste = new Acc_Account_Ledger($this->db, $poste_val);
			if ($poste->load() == false)
			{
				throw new Exception('Pour la fiche ' . ${'e_other' . $i} . ' le poste comptable [' . $poste->id . 'n\'existe pas', 9);
			}
			/* Check if the card belong to the ledger */
			$fiche = new Fiche($this->db);
			$fiche->get_by_qcode(${'e_other' . $i});
			if ($fiche->belong_ledger($p_jrn, 'deb') != 1)
				throw new Exception('La fiche ' . ${'e_other' . $i} . 'n\'est pas accessible à ce journal', 10);
			if ($chdate == 2)
			{
				{/*  check if the date is valid */
					if (isDate(${'dateop' . $i}) == null)
					{
						throw new Exception('Date invalide', 2);
					}
					$oPeriode = new Periode($this->db);
					if ($this->check_periode() == false)
					{
						$periode = $oPeriode->find_periode(${'dateop' . $i});
					}
					else
					{
						$oPeriode->p_id = $periode;
						list ($min, $max) = $oPeriode->get_date_limit();
						if (cmpDate(${'dateop' . $i}, $min) < 0 ||
								cmpDate(${'dateop' . $i}, $max) > 0)
							throw new Exception(_('Date et periode ne correspondent pas'), 6);
					}

					/* check if the periode is closed */
					if ($this->is_closed($periode) == 1)
					{
						throw new Exception(_('Periode fermee'), 6);
					}

					/* check if we are using the strict mode */
					if ($this->check_strict() == true)
					{
						/* if we use the strict mode, we get the date of the last
						  operation */
						$last_date = $this->get_last_date();
						if ($last_date != null && cmpDate(${'dateop' . $i}, $last_date) < 0)
							throw new Exception(_('Vous utilisez le mode strict la dernière operation est à la date du ')
									. $last_date . _(' vous ne pouvez pas encoder à une date antérieure'), 15);
					}
				}
			}
			$nb++;
		}
		if ($nb == 0)
			throw new Exception('Il n\'y a aucune opération', 12);

		/* Check if the last_saldo and first_saldo are correct */
		if (strlen(trim($last_sold)) != 0 && isNumber($last_sold) &&
				strlen(trim($first_sold)) != 0 && isNumber($first_sold))
		{
			$diff = $last_sold - $first_sold;
			$diff = round($diff, 2) - round($tot_amount, 2);
			if ($first_sold != 0 && $last_sold != 0)
			{
				if ($diff != 0)
					throw new Exception('Le montant de l\'extrait est incorrect' .
							$tot_amount . ' extrait ' . $diff, 13);
			}
		}
	}

	/**\brief
	 * \param $p_array contains the value usually it is $_POST
	 * \return string with html code
	 * \note the form tag are not  set here
	 */

	function input($p_array = null, $notused = 0)
	{
		global $g_parameter, $g_user;
		if ($p_array != null)
			extract($p_array);

		$pview_only = false;

		$min_article=$this->get_min_row();

		$f_add_button = new IButton('add_card');
		$f_add_button->label = _('Créer une nouvelle fiche');
		$f_add_button->set_attribute('ipopup', 'ipop_newcard');
		$f_add_button->set_attribute('jrn', $this->id);
		$f_add_button->javascript = " this.jrn=\$('p_jrn').value;select_card_type(this);";
		$str_add_button = ($g_user->check_action(FICADD) == 1) ? $f_add_button->input() : "";

		// The first day of the periode
		$pPeriode = new Periode($this->db);
		list ($l_date_start, $l_date_end) = $pPeriode->get_date_limit($g_user->get_periode());
		if ($g_parameter->MY_DATE_SUGGEST == 'Y')
			$op_date = (!isset($e_date) ) ? $l_date_start : $e_date;
		else
			$op_date = (!isset($e_date) ) ? '' : $e_date;

		$r = "";

		$r.=dossier::hidden();
		$f_legend = 'Banque, caisse';
		//  Date
		//--
		$Date = new IDate("e_date", $op_date);
		$Date->setReadOnly($pview_only);
		$f_date = $Date->input();
		$f_period = '';
		if ($this->check_periode() == true)
		{
			// Periode
			//--
			$l_user_per = (isset($periode)) ? $periode : $g_user->get_periode();
			$period = new IPeriod();
			$period->cn = $this->db;
			$period->type = OPEN;
			$period->value = $l_user_per;
			$period->user = $g_user;
			$period->name = 'periode';
			try
			{
				$l_form_per = $period->input();
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 1)
				{
					throw  new Exception(_("Aucune période ouverte"));
					
				}
			}
			$label = HtmlInput::infobulle(3);
			$f_period = "Période comptable $label" . $l_form_per;
		}

		// Ledger (p_jrn)
		//--
		$onchange="update_bank();ajax_saldo('first_sold');update_name();update_row('fin_item');";

		if ($g_parameter->MY_DATE_SUGGEST == 'Y')
			$onchange .= 'get_last_date();';
		if ($g_parameter->MY_PJ_SUGGEST=='Y')
			$onchange .= 'update_pj();';

		$add_js = 'onchange="'.$onchange.'"';
		$wLedger = $this->select_ledger('FIN', 2);
		if ($wLedger == null)
			throw  new Exception(_('Pas de journal disponible'));

		$wLedger->javascript = $add_js;

		$label = " Journal " . HtmlInput::infobulle(2);
		$f_jrn = $label . $wLedger->input();


		// retrieve bank name, code and account from the jrn_def.jrn_def_bank

		$f_bank = '<span id="bkname">' . $this->get_bank_name() . '</span>';
		if ($this->bank_id == "")
		{
			echo h2("Journal de banque non configuré " . $this->get_name(), ' class="error"');
			echo '<span class="error"> vous devez donner à ce journal un compte en banque (fiche), modifiez dans CFGLED</span>';
			alert("Journal de banque non configuré " . $this->get_name());
		}

		$f_legend_detail = 'Opérations financières';
		//--------------------------------------------------
		// Saldo begin end
		//-------------------------------------------------
		// Extrait
		$default_pj = '';
		if ($g_parameter->MY_PJ_SUGGEST == 'Y')
		{
			$default_pj = $this->guess_pj();
		}
		$wPJ = new IText('e_pj');
		$wPJ->readonly = false;
		$wPJ->size = 10;
		$wPJ->value = (isset($e_pj)) ? $e_pj : $default_pj;

		$f_extrait = $wPJ->input() . HtmlInput::hidden('e_pj_suggest', $default_pj);
		$label = HtmlInput::infobulle(7);

		$first_sold = (isset($first_sold)) ? $first_sold : "";
		$wFirst = new INum('first_sold', $first_sold);

		$last_sold = isset($last_sold) ? $last_sold : "";
		$wLast = new INum('last_sold', $last_sold);


		$max = (isset($nb_item)) ? $nb_item : $min_article;

		$r.= HtmlInput::hidden('nb_item', $max);
		//--------------------------------------------------
		// financial operation
		//-------------------------------------------------

		$array = array();
		// Parse each " tiers"
		for ($i = 0; $i < $max; $i++)
		{
			$tiers = (isset(${"e_other" . $i})) ? ${"e_other" . $i} : "";

			$tiers_amount = (isset(${"e_other$i" . "_amount"})) ? round(${"e_other$i" . "_amount"}, 2) : 0;

			$tiers_comment = (isset(${"e_other$i" . "_comment"})) ? ${"e_other$i" . "_comment"} : "";

			$operation_date=new IDate("dateop".$i);
			$operation_date->value=(isset(${'dateop'.$i}))?${'dateop'.$i}:"";
			$array[$i]['dateop']=$operation_date->input();
			${"e_other$i" . "_amount"} = (isset(${"e_other$i" . "_amount"})) ? ${"e_other$i" . "_amount"} : 0;

			$W1 = new ICard();
			$W1->label = "";
			$W1->name = "e_other" . $i;
			$W1->id = "e_other" . $i;
			$W1->value = $tiers;
			$W1->extra = 'deb';  // credits
			$W1->typecard = 'deb';
                        $W1->style=' style = "vertical-align:65%"';
			$W1->set_dblclick("fill_ipopcard(this);");
			$W1->set_attribute('ipopup', 'ipopcard');

			// name of the field to update with the name of the card
			$W1->set_attribute('label', 'e_other_name' . $i);
			// name of the field to update with the name of the card
			$W1->set_attribute('typecard', 'filter');
			// Add the callback function to filter the card on the jrn
			$W1->set_callback('filter_card');
			$W1->set_function('fill_data');
			$W1->javascript = sprintf(' onchange="fill_data_onchange(\'%s\');" ', $W1->name);
			$W1->readonly = $pview_only;
			$array[$i]['qcode'] = $W1->input();
			$array[$i]['search'] = $W1->search();

			// Card name
			//
			 $card_name = "";
			if ($tiers != "")
			{
				$fiche = new Fiche($this->db);
				$fiche->get_by_qcode($tiers);
				$card_name = $this->db->get_value("Select ad_value from fiche_detail where ad_id=$1 and f_id=$2", array(ATTR_DEF_NAME, $fiche->id));
			}

			$wcard_name = new IText("e_other_name" . $i, $card_name);
			$wcard_name->id=$wcard_name->name;
			$wcard_name->readOnly = true;
			$array[$i]['cname'] = $wcard_name->input();

			// Comment
			$wComment = new IText("e_other$i" . "_comment", $tiers_comment);

			$wComment->size = 35;
			$wComment->setReadOnly($pview_only);
			$array[$i]['comment'] = $wComment->input();
			// amount
			$wAmount = new INum("e_other$i" . "_amount", $tiers_amount);

			$wAmount->size = 7;
			$wAmount->setReadOnly($pview_only);
			$array[$i]['amount'] = $wAmount->input();
			// concerned
			${"e_concerned" . $i} = (isset(${"e_concerned" . $i})) ? ${"e_concerned" . $i} : ""
			;
			$wConcerned = new IConcerned("e_concerned" . $i, ${"e_concerned" . $i});
                        $wConcerned->tiers="e_other" . $i;
			$wConcerned->setReadOnly($pview_only);
			$wConcerned->amount_id = "e_other" . $i . "_amount";

			$wConcerned->paid = 'paid';
			$array[$i]['concerned'] = $wConcerned->input();
		}

		ob_start();
		require_once NOALYSS_INCLUDE.'/template/form_ledger_fin.php';
		$r.=ob_get_contents();
		ob_end_clean();
		$r.= create_script("$('".$Date->id."').focus()");

		return $r;
	}

	/**\brief show the summary before inserting into the database, it
	 * calls the function for adding a attachment. The function verify
	 * should be called before
	 * \param $p_array an array usually is $_POST
	 * \return string with code html
	 */

	public function confirm($p_array, $p_nothing = 0)
	{
		global $g_parameter,$g_user;
		$r = "";
		bcscale(2);
		extract($p_array);
		$pPeriode = new Periode($this->db);
		if ($this->check_periode() == true)
		{
			$pPeriode->p_id = $periode;
		}
		else
		{
			if (isDate($e_date) != null) {
				$pPeriode->find_periode($e_date);
			} else {
				$pPeriode->p_id=$g_user->get_periode();
			}
		}

		list ($l_date_start, $l_date_end) = $pPeriode->get_date_limit();
		$exercice = $pPeriode->get_exercice();
		$r.='';
		$r.='<fieldset><legend>Banque, caisse </legend>';
		$r.= '<div id="jrn_name_div">';
		$r.='<h2 id="jrn_name" style="display:inline">' . $this->get_name() . '</h2>';
		$r.= '</div>';
		$r.='<TABLE  width="100%">';
		//  Date
		//--
		$r.="<tr>";
		if ( $chdate == 1 ) $r.='<td> Date : </td><td>' . $e_date;
		// Periode
		//--
		$r.="<td>";
		$r.="Période comptable </td><td>";
		$r.=$l_date_start . ' - ' . $l_date_end;
		$r.="</td>";
		$r.="</tr>";
		// Ledger (p_jrn)
		//--
		$r.='<tr>';
		$r.='<td> Journal </td>';
		$this->id = $p_jrn;
		$r.='<td>';
		$r.=h($this->get_name());
		$r.='</td>';
		$r.='</tr>';

		//retrieve bank name
		$bk_id = $this->get_bank();

		$fBank = new Fiche($this->db, $bk_id);
		$e_bank_account_label = $this->get_bank_name();

		$filter_year = "  j_tech_per in (select p_id from parm_periode where  p_exercice='" . $exercice . "')";

		$acc_account = new Acc_Account_Ledger($this->db, $fBank->strAttribut(ATTR_DEF_ACCOUNT));
		$asolde= $acc_account->get_solde_detail($filter_year);
		$deb=$asolde['debit'];
		$cred=$asolde['credit'];
		$solde=  bcsub($deb, $cred);
		$new_solde=$solde;

		$r.="<TR><td colspan=\"4\"> Banque ";
		$r.=$e_bank_account_label;

		$r.="</TABLE>";

		$r.='</fieldset>';

		$r.='<div class="myfieldset"><h1 class="legend">Extrait de compte</h1>';
		//--------------------------------------------------
		// Saldo begin end
		//-------------------------------------------------
		$r.='<table>';
		$r.='<tr>';
		// Extrait
		//--
		$r.=tr('<td> Numéro d\'extrait</td>' . td(h($e_pj)));
		$r.='<tr><td >Solde début extrait </td>';
		$r.='<td style="num">' . nbm($first_sold) . '</td></tr>';
		$r.='<tr><td>Solde fin extrait </td>';
		$r.='<td style="num">' . nbm($last_sold) . '</td></tr>';
		$r.='</table>';

		$r.='<h1 class="legend">Opérations financières</h1>';
		//--------------------------------------------------
		// financial operation
		//-------------------------------------------------
		$r.='<TABLE style="width:100%" id="fin_item">';
		$r.="<TR>";
		if ($chdate==2) $r.='<th>Date</th>';
		$r.="<th style=\"width:auto;text-align:left\" colspan=\"2\">Nom</TH>";
		$r.="<th style=\"text-align:left\" >Commentaire</TH>";
		$r.="<th style=\"text-align:right\">Montant</TH>";
		$r.='<th colspan="2"> Op. Concern&eacute;e(s)</th>';

		/* if we use the AC */
		if ($g_parameter->MY_ANALYTIC != 'nu')
		{
			$anc = new Anc_Plan($this->db);
			$a_anc = $anc->get_list();
			$x = count($a_anc);
			/* set the width of the col */
			$r.='<th colspan="' . $x . '">' . _('Compt. Analytique') . '</th>';

			/* add hidden variables pa[] to hold the value of pa_id */
			$r.=Anc_Plan::hidden($a_anc);
		}
		$r.="</TR>";
		// Parse each " tiers"
		$tot_amount = 0;
		//--------------------------------------------------
		// For each items
		//--------------------------------------------------
		for ($i = 0; $i < $nb_item; $i++)
		{

			$tiers = (isset(${"e_other" . $i})) ? ${"e_other" . $i} : ""
			;

			if (strlen(trim($tiers)) == 0)
				continue;
			$tiers_label = "";
			$tiers_amount = round(${"e_other$i" . "_amount"}, 2);
			$tot_amount = bcadd($tot_amount, $tiers_amount);
			$tiers_comment = h(${"e_other$i" . "_comment"});
			// If $tiers has a value
			$fTiers = new Fiche($this->db);
			$fTiers->get_by_qcode($tiers);

			$tiers_label = $fTiers->strAttribut(ATTR_DEF_NAME);

			$r.="<TR>";
			if ($chdate==2) $r.=td(${"dateop".$i});
			$r.="<td>" . ${'e_other' . $i} . "</TD>";
			// label
			$r.='<TD style="width:25%;border-bottom:1px dotted grey;">';
			$r.=$fTiers->strAttribut(ATTR_DEF_NAME);
			$r.='</td>';
			// Comment
			$r.='<td style="width:40%">' . $tiers_comment . '</td>';
			// amount
			$r.='<td class="num">' . nbm($tiers_amount) . '</td>';
			// concerned
			$r.='<td style="text-align:center">';
			if (${"e_concerned" . $i} != '')
			{
				$jr_internal = $this->db->get_array("select jr_internal from jrn where jr_id in (" . ${"e_concerned" . $i} . ")");
				$comma="";
				for ($x = 0; $x < count($jr_internal); $x++)
				{
					$r.=$comma.HtmlInput::detail_op(${"e_concerned" . $i}, $jr_internal[$x]['jr_internal']);
					$comma=" , ";
				}
			}
			$r.='</td>';
			// encode the pa
			if ($g_parameter->MY_ANALYTIC != 'nu' && preg_match("/^[6,7]/", $fTiers->strAttribut(ATTR_DEF_ACCOUNT)) == 1) // use of AA
			{
				// show form
				$anc_op = new Anc_Operation($this->db);
				$null = ($g_parameter->MY_ANALYTIC == 'op') ? 1 : 0;
				$r.='<td>';
				$p_mode = 1;
				$p_array['pa_id'] = $a_anc;
				/* op is the operation it contains either a sequence or a jrnx.j_id */
				$r.=HtmlInput::hidden('op[]=', $i);
				$r.=$anc_op->display_form_plan($p_array, $null, $p_mode, $i, $tiers_amount);
				$r.='</td>';
			}

			$r.='</TR>';
		}
		$r.="</TABLE>";

		// saldo
		$r.='<br>Ancien solde = ' . $solde;
		$new_solde+=$tot_amount;
		$r.='<br>Nouveau solde = ' . $new_solde;
		$r.='<br>Difference =' . $tot_amount;
		// check for upload piece
		$file = new IFile();

		$r.="<br>Ajoutez une pi&egrave;ce justificative ";
		$r.=$file->input("pj", "");

		$r.='</div>';
		//--------------------------------------------------
		// Hidden variables
		//--------------------------------------------------
		$r.=dossier::hidden();
		$r.=HtmlInput::hidden('p_jrn', $this->id);
		$r.=HtmlInput::hidden('nb_item', $nb_item);
		$r.=HtmlInput::hidden('last_sold', $last_sold);
		$r.=HtmlInput::hidden('first_sold', $first_sold);
		$r.=HtmlInput::hidden('e_pj', $e_pj);
		$r.=HtmlInput::hidden('e_pj_suggest', $e_pj_suggest);
		$r.=HtmlInput::hidden('e_date', $e_date);
		$mt = microtime(true);
		$r.=HtmlInput::hidden('mt', $mt);

		if (isset($periode))
			$r.=HtmlInput::hidden('periode', $periode);
		$r.=dossier::hidden();
		$r.=HtmlInput::hidden('sa', 'n','chdate');
		for ($i = 0; $i < $nb_item; $i++)
		{
			$tiers = (isset(${"e_other" . $i})) ? ${"e_other" . $i} : ""			;
			$r.=HtmlInput::hidden('e_other' . $i, $tiers);
			$r.=HtmlInput::hidden('e_other' . $i, $tiers);
			$r.=HtmlInput::hidden('e_other' . $i . '_comment', ${'e_other' . $i . '_comment'});
			$r.=HtmlInput::hidden('e_other' . $i . '_amount', ${'e_other' . $i . '_amount'});
			$r.=HtmlInput::hidden('e_concerned' . $i, ${'e_concerned' . $i});
			$r.=HtmlInput::hidden('dateop' . $i, ${'dateop' . $i});
			$r.=HtmlInput::hidden('chdate' , $chdate);
		}

		return $r;
	}

	/**\brief save the data into the database, included the attachment,
	 * and the reconciliations
	 * \param $p_array usually $_POST
	 * \return string with HTML code
	 */

	public function insert($p_array = null)
	{
		global $g_parameter;
		bcscale(2);
		$internal_code = "";
		$oid = 0;
		extract($p_array);
		$ret = '';
		// Debit = banque
		$bank_id = $this->get_bank();
		$fBank = new Fiche($this->db, $bank_id);
		$e_bank_account = $fBank->strAttribut(ATTR_DEF_QUICKCODE);
		// Get the saldo
		$pPeriode = new Periode($this->db);
		$sposte = $fBank->strAttribut(ATTR_DEF_ACCOUNT);
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

		$acc_account = new Acc_Account_Ledger($this->db, $poste_val);

		// If date = deposit date
		if ($chdate == 1 )
		{
			if ($this->check_periode() == true)
			{
				$pPeriode->p_id = $periode;
			}
			else
			{
				$pPeriode->find_periode($e_date);
			}
			$exercice = $pPeriode->get_exercice();
			$filter_year = "  j_tech_per in (select p_id from parm_periode where  p_exercice='" . $exercice . "')";
			$asolde= $acc_account->get_solde_detail($filter_year);
			$deb=$asolde['debit'];
			$cred=$asolde['credit'];
			$solde=  bcsub($deb, $cred);
			$new_solde=$solde;
		}





		try
		{
			$this->db->start();
			$amount = 0.0;
			$idx_operation = 0;
			$ret = '<table class="result" >';
			$ret.=tr(th('Date').th('n° interne') . th('Quick Code') . th('Nom') . th('Libellé') . th('Montant', ' style="text-align:right"'));
			// Credit = goods
			$get_solde=true;
			for ($i = 0; $i < $nb_item; $i++)
			{
				// insert it into the database
				// and quit the loop ?
				if (strlen(trim(${"e_other$i"})) == 0)
					continue;

				if ( $chdate == 2 ) $e_date=${'dateop'.$i};
				// if date is date of operation
				if ($chdate == 2 && $get_solde )
				{
					$get_solde=false;
					if ($this->check_periode() == true)
					{
						$pPeriode->p_id = $periode;
					}
					else
					{
						$pPeriode->find_periode($e_date);
					}
					$exercice = $pPeriode->get_exercice();
					$filter_year = "  j_tech_per in (select p_id from parm_periode where  p_exercice='" . $exercice . "')";
					$solde = $acc_account->get_solde($filter_year);
					$new_solde = $solde;
				}
				$fPoste = new Fiche($this->db);
				$fPoste->get_by_qcode(${"e_other$i"});

				// round it
				${"e_other$i" . "_amount"} = round(${"e_other$i" . "_amount"}, 2);



				$amount+=${"e_other$i" . "_amount"};
				// Record a line for the bank
				// Compute the j_grpt
				$seq = $this->db->get_next_seq('s_grpt');

				$acc_operation = new Acc_Operation($this->db);
				$acc_operation->date = $e_date;
				$sposte = $fPoste->strAttribut(ATTR_DEF_ACCOUNT);
				// if 2 accounts
				if (strpos($sposte, ',') != 0)
				{
					$array = explode(',', $sposte);
					if (${"e_other$i" . "_amount"} < 0)
						$poste_val = $array[1];
					else
						$poste_val = $array[0];
				}
				else
				{
					$poste_val = $sposte;
				}


				$acc_operation->poste = $poste_val;
				$acc_operation->amount = ${"e_other$i" . "_amount"} * (-1);
				$acc_operation->grpt = $seq;
				$acc_operation->jrn = $p_jrn;
				$acc_operation->type = 'd';

				if (isset($periode))
					$tperiode = $periode;
				else
				{
					$per = new Periode($this->db);
					$tperiode = $per->find_periode($e_date);
				}
				$acc_operation->periode = $tperiode;
				$acc_operation->qcode = ${"e_other" . $i};
				$j_id = $acc_operation->insert_jrnx();

				$acc_operation = new Acc_Operation($this->db);
				$acc_operation->date = $e_date;
				$sposte = $fBank->strAttribut(ATTR_DEF_ACCOUNT);

				// if 2 accounts
				if (strpos($sposte, ',') != 0)
				{
					$array = explode(',', $sposte);
					if (${"e_other$i" . "_amount"} < 0)
						$poste_val = $array[1];
					else
						$poste_val = $array[0];
				}
				else
				{
					$poste_val = $sposte;
				}

				$acc_operation->poste = $poste_val;
				$acc_operation->amount = ${"e_other$i" . "_amount"};
				$acc_operation->grpt = $seq;
				$acc_operation->jrn = $p_jrn;
				$acc_operation->type = 'd';
				$acc_operation->periode = $tperiode;
				$acc_operation->qcode = $e_bank_account;
				$acc_operation->insert_jrnx();


				if (sql_string(${"e_other$i" . "_comment"}) == null)
				{
					// if comment is blank set a default one
					$comment = "  compte : " . $fBank->strAttribut(ATTR_DEF_NAME) . ' a ' .
							$fPoste->strAttribut(ATTR_DEF_NAME);
				}
				else
				{
					$comment = ${'e_other' . $i . '_comment'};
				}


				$acc_operation = new Acc_Operation($this->db);
				$acc_operation->jrn = $p_jrn;
				$acc_operation->amount = abs(${"e_other$i" . "_amount"});
				$acc_operation->date = $e_date;
				$acc_operation->desc = $comment;
				$acc_operation->grpt = $seq;
				$acc_operation->periode = $tperiode;
				$acc_operation->mt = $mt;
				$idx_operation++;
				$acc_operation->pj = '';

				if (trim($e_pj) != '' && $this->numb_operation() == true)
					$acc_operation->pj = $e_pj . str_pad($idx_operation, 3, 0, STR_PAD_LEFT);

				if (trim($e_pj) != '' && $this->numb_operation() == false)
					$acc_operation->pj = $e_pj;

				$jr_id = $acc_operation->insert_jrn();
				// 	  $acc_operation->set_pj();
				$this->db->exec_sql('update jrn set jr_pj_number=$1 where jr_id=$2', array($acc_operation->pj, $jr_id));
				$internal = $this->compute_internal_code($seq);


				if (trim(${"e_concerned" . $i}) != "")
				{
					if (strpos(${"e_concerned" . $i}, ',') != 0)
					{
						$aRapt = explode(',', ${"e_concerned" . $i});
						foreach ($aRapt as $rRapt)
						{
							// Add a "concerned operation to bound these op.together
							//
                                                        $rec = new Acc_Reconciliation($this->db);
							$rec->set_jr_id($jr_id);

							if (isNumber($rRapt) == 1)
							{
								$rec->insert($rRapt);
							}
						}
					}
					else
					if (isNumber(${"e_concerned" . $i}) == 1)
					{
						$rec = new Acc_Reconciliation($this->db);
						$rec->set_jr_id($jr_id);
						$rec->insert(${"e_concerned$i"});
					}
				}

				// Set Internal code
				$this->grpt_id = $seq;
				/**
				 * save also into quant_fin
				 */
				$this->insert_quant_fin($fBank->id, $jr_id, $fPoste->id, ${"e_other$i" . "_amount"});

				if ($g_parameter->MY_ANALYTIC != "nu")
				{
					// for each item, insert into operation_analytique */
					$op = new Anc_Operation($this->db);
					$op->oa_group = $this->db->get_next_seq("s_oa_group"); /* for analytic */
					$op->j_id = $j_id;
					$op->oa_date = $e_date;
					$op->oa_debit = 'f';
					$op->oa_description = sql_string($comment);
					$op->save_form_plan($_POST, $i, $j_id);
				}


				$this->update_internal_code($internal);

				$js_detail = HtmlInput::detail_op($jr_id, $internal);
				// Compute display
				$row = td($e_date).td($js_detail) . td(${"e_other$i"}) . td($fPoste->strAttribut(ATTR_DEF_NAME)) . td(${"e_other" . $i . "_comment"}) . td(nbm(${"e_other$i" . "_amount"}), 'class="num"');
                                $class=($i%2==0)?' class="even" ':' class="odd" ';
				$ret.=tr($row,$class);


				if ($i == 0)
				{
					// first record we upload the files and
					// keep variable to update other row of jrn
					if (isset($_FILES))
						$oid = $this->db->save_upload_document($seq);
				}
				else
				{
					if ($oid != 0)
					{
                                            $this->db->exec_sql("update jrn set jr_pj=$1 , jr_pj_name=$2,
                                            jr_pj_type=$3  where jr_grpt_id=$4",
                                                array($oid,$_FILES['pj']['name'] ,$_FILES['pj']['type'],$seq));
					}
				}
			} // for nbitem
			// increment pj
			if (strlen(trim($e_pj)) != 0)
			{
				$this->inc_seq_pj();
			}
			$ret.='</table>';
		}
		catch (Exception $e)
		{
			$r = '<span class="error">' .
			'Erreur dans l\'enregistrement ' .
			__FILE__ . ':' . __LINE__ . ' ' .
			$e->getMessage();
			$this->db->rollback();
			throw new Exception($r);
		}
		$this->db->commit();
		$r = "";
		$r.="<br>Ancien solde " . nbm($solde);
		$new_solde = bcadd($new_solde, $amount);
		$r.="<br>Nouveau solde " . nbm($new_solde);
		$ret.=$r;
		return $ret;
	}

	/**\brief display operation of a FIN ledger
	 * \return html code into a string
	 */

	function show_ledger()
	{
		global $g_user;
		echo dossier::hidden();
		$hid = new IHidden();

		$hid->name = "p_action";
		$hid->value = "bank";
		echo $hid->input();


		$hid->name = "sa";
		$hid->value = "l";
		echo $hid->input();


		$w = new ISelect();
		// filter on the current year
		$filter_year = " where p_exercice='" . $g_user->get_exercice() . "'";

		$periode_start = $this->db->make_array("select p_id,to_char(p_start,'DD-MM-YYYY') from parm_periode $filter_year order by p_start,p_end", 1);
		// User is already set User=new User($this->db);
		$current = (isset($_GET['p_periode'])) ? $_GET['p_periode'] : -1;
		$w->selected = $current;

		echo '<form>';
		echo 'Période  ' . $w->input("p_periode", $periode_start);
		$wLedger = $this->select_ledger('fin', 3);

		if ($wLedger == null)
			throw  new Exception(_('Pas de journal disponible'));

		if (count($wLedger->value) > 1)
		{
			$aValue = $wLedger->value;
			$wLedger->value[0] = array('value' => -1, 'label' => _('Tous les journaux financiers'));
			$idx = 1;
			foreach ($aValue as $a)
			{
				$wLedger->value[$idx] = $a;
				$idx++;
			}
		}



		echo 'Journal ' . $wLedger->input();
		$w = new ICard();
		$w->noadd = 'no';
		$w->jrn = $this->id;
		$qcode = (isset($_GET['qcode'])) ? $_GET['qcode'] : "";
		echo dossier::hidden();
		echo HtmlInput::hidden('p_action', 'bank');
		echo HtmlInput::hidden('sa', 'l');
		$w->name = 'qcode';
		$w->value = $qcode;
		$w->label = '';
		$this->type = 'FIN';
		$all = $this->get_all_fiche_def();
		$w->extra = $all;
		$w->extra2 = 'QuickCode';
		$sp = new ISpan();
		echo $sp->input("qcode_label", "", $qcode);
		echo $w->input();

		echo HtmlInput::submit('gl_submit', _('Rechercher'));
		echo '</form>';

		// Show list of sell
		// Date - date of payment - Customer - amount
		if ($current != -1)
		{
			$filter_per = " and jr_tech_per=" . $current;
		}
		else
		{
			$filter_per = " and jr_tech_per in (select p_id from parm_periode where p_exercice::integer=" .
					$g_user->get_exercice() . ")";
		}
		/* security  */
		if ($this->id != -1)
			$available_ledger = " and jr_def_id= " . $this->id . " and " . $g_user->get_ledger_sql();
		else
			$available_ledger = " and " . $g_user->get_ledger_sql();
		// Show list of sell
		// Date - date of payment - Customer - amount
		$sql = SQL_LIST_ALL_INVOICE . $filter_per . " and jr_def_type='FIN'" .
				" $available_ledger";
		$step = $_SESSION['g_pagesize'];
		$page = (isset($_GET['offset'])) ? $_GET['page'] : 1;
		$offset = (isset($_GET['offset'])) ? $_GET['offset'] : 0;

		$l = "";

		// check if qcode contains something
		if ($qcode != "")
		{
			// add a condition to filter on the quick code
			$l = " and jr_grpt_id in (select j_grpt from jrnx where j_qcode=upper('$qcode')) ";
		}

		list($max_line, $list) = ListJrn($this->db, "where jrn_def_type='FIN' $filter_per $l $available_ledger "
				, null, $offset, 0);
		$bar = navigation_bar($offset, $max_line, $step, $page);

		echo "<hr> $bar";
		echo $list;
		echo "$bar <hr>";
	}

	/**
	 * return a string with the bank account, name and quick_code
	 */
	function get_bank_name()
	{
		$this->bank_id = $this->db->get_value('select jrn_def_bank from jrn_def where jrn_def_id=$1', array($this->id));
		$fBank = new Fiche($this->db, $this->bank_id);
		$e_bank_account = " : " . $fBank->strAttribut(ATTR_DEF_BQ_NO);
		$e_bank_name = " : " . $fBank->strAttribut(ATTR_DEF_NAME);
		$e_bank_qcode = ": " . $fBank->strAttribut(ATTR_DEF_QUICKCODE);
		return $e_bank_qcode . $e_bank_name . $e_bank_account;
	}

	/**
	 * return the fiche_id of the bank
	 */
	function get_bank()
	{
		$bank_id = $this->db->get_value('select jrn_def_bank from jrn_def where jrn_def_id=$1', array($this->id));
		return $bank_id;
	}

	/**
	 * return true is we numbere each operation
	 */
	function numb_operation()
	{
		$a = $this->db->get_value('select jrn_def_num_op from jrn_def where jrn_def_id=$1', array($this->id));
		if ($a == 1)
			return true;
		return false;
	}

	/**
	 * insert into the quant_fin table
	 * @param $bank_id is the f_id of the bank
	 * @param $jr_id is the jrn.jr_id of the operation
	 * @param $other is the f_id of the benefit
	 * @param $amount is the amount
	 */
	function insert_quant_fin($p_bankid, $p_jrid, $p_otherid, $p_amount)
	{
		$sql = "INSERT INTO quant_fin(qf_bank, jr_id, qf_other, qf_amount)
                   VALUES ($1, $2, $3, $4);";

		$this->db->exec_sql($sql, array($p_bankid, $p_jrid, $p_otherid, round($p_amount, 2)));
	}

}
