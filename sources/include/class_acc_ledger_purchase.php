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
 * \brief class for the purchase, herits from acc_ledger
 */
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_icard.php';
require_once NOALYSS_INCLUDE.'/class_ispan.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_iperiod.php';
require_once NOALYSS_INCLUDE.'/class_idate.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_ifile.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';
require_once NOALYSS_INCLUDE.'/class_acc_compute.php';
require_once NOALYSS_INCLUDE.'/class_anc_operation.php';
require_once NOALYSS_INCLUDE.'/user_common.php';
require_once NOALYSS_INCLUDE.'/class_acc_parm_code.php';
require_once NOALYSS_INCLUDE.'/class_acc_payment.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_itva_popup.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger_info.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger_fin.php';
require_once NOALYSS_INCLUDE.'/class_stock_goods.php';

/*!\brief Handle the ledger of purchase,
 *
 *
 */
class  Acc_Ledger_Purchase extends Acc_Ledger
{
    function __construct ($p_cn,$p_init)
    {
        $this->type='ACH';
        parent::__construct($p_cn,$p_init);
    }
    /*!\brief verify that the data are correct before inserting or confirming
     *\param an array (usually $_POST)
     *\return String
     *\throw Exception if an error occurs
     */
    public function verify($p_array)
    {
        global $g_parameter,$g_user;
        
        if (is_array($p_array ) == false || empty($p_array))
                    throw new Exception ("Array empty");
        /*
         * Check needed value
         */
        check_parameter($p_array,'p_jrn,e_date,e_client');

        extract ($p_array);
        /* check if we can write into this ledger */
        if ( $g_user->check_jrn($p_jrn) != 'W' )
            throw new Exception (_('Accès interdit'),20);


        /* check for a double reload */
        if (  isset($mt) && $this->db->count_sql('select jr_mt from jrn where jr_mt=$1',array($mt)) != 0 )
            throw new Exception (_('Double Encodage'),5);

        /* check if there is a customer */
        if ( strlen(trim($e_client)) == 0 )
            throw new Exception(_('Vous n\'avez pas donné de fournisseur'),11);

        /*  check if the date is valid */
        if ( isDate($e_date) == null )
        {
            throw new Exception(_('Date invalide'), 2);
        }
        $oPeriode=new Periode($this->db);
        if ( $this->check_periode() == false)
        {
            $tperiode=$oPeriode->find_periode($e_date);
        }
        else
        {
            $tperiode=$period;
            $oPeriode->p_id=$tperiode;
            /* check that the datum is in the choosen periode */
            list ($min,$max)=$oPeriode->get_date_limit($tperiode);
            if ( cmpDate($e_date,$min) < 0 ||
                    cmpDate($e_date,$max) > 0)
                throw new Exception(_('Date et periode ne correspondent pas'),6);
        }
        /* check if the periode is closed */
        if ( $this->is_closed($tperiode)==1 )
        {
            throw new Exception(_('Periode fermee'),6);
        }

        /* check if we are using the strict mode */
        if( $this->check_strict() == true)
        {
            /* if we use the strict mode, we get the date of the last
            operation */
            $last_date=$this->get_last_date();
            if ( $last_date != null  && cmpDate($e_date,$last_date) < 0 )
                throw new Exception(_('Vous utilisez le mode strict la dernière operation est à la date du ')
                                    .$last_date._(' vous ne pouvez pas encoder à une '.
                                                  ' date antérieure dans ce journal'),13);

        }

        /* check the account */
        $fiche=new Fiche($this->db);
        $fiche->get_by_qcode($e_client);

        if ( $fiche->empty_attribute(ATTR_DEF_ACCOUNT) == true)
            throw new Exception(_('La fiche ').$e_client._('n\'a pas de poste comptable'),8);

        /* get the account and explode if necessary */
        $sposte=$fiche->strAttribut(ATTR_DEF_ACCOUNT);
        // if 2 accounts, take only the credit one for supplier
        if ( strpos($sposte,',') != 0 )
        {
            $array=explode(',',$sposte);
            $poste_val=$array[1];
        }
        else
        {
            $poste_val=$sposte;
        }

        /* The account exists */
        $poste=new Acc_Account_Ledger($this->db,$poste_val);
        if ( $poste->load() == false )
        {
            throw new Exception(_('Pour la fiche ').$e_client._(' le poste comptable [').$poste->id.'] '._('n\'existe pas'),9);
        }
        /* Check if the card belong to the ledger */
        $fiche=new Fiche ($this->db);
        $fiche->get_by_qcode($e_client,'cred');
        if ( $fiche->belong_ledger($p_jrn) !=1 )
            throw new Exception(_('La fiche ').$e_client._('n\'est pas accessible à ce journal'),10);

        $nb=0;
        //------------------------------------------------------
        // The "Paid By"  check
        //------------------------------------------------------
        if ($e_mp != 0 ) $this->check_payment($e_mp,${"e_mp_qcode_".$e_mp});


        //----------------------------------------
        // foreach item
        //----------------------------------------
        for ($i=0;$i< $nb_item;$i++)
        {
            if ( strlen(trim(${'e_march'.$i}))== 0) continue;
            /* check if amount are numeric and */
            if ( isNumber(${'e_march'.$i.'_price'}) == 0 )
                throw new Exception(_('La fiche ').${'e_march'.$i}._('a un montant invalide').' ['.${'e_march'.$i}.']',6);
            if ( isNumber(${'e_quant'.$i}) == 0 )
                throw new Exception(_('La fiche ').${'e_march'.$i}._('a une quantité invalide').' ['.${'e_quant'.$i}.']',7);

            // Check if the given tva id is valid
            if ( $g_parameter->MY_TVA_USE=='Y')
            {
                if (${'e_march'.$i.'_tva_id'} == 0 )
                    throw new Exception(_('La fiche ').${'e_march'.$i}._('a un code tva invalide').' ['.${'e_march'.$i.'_tva_id'}.']',13);
                $tva_rate=new Acc_Tva($this->db);
                $tva_rate->set_parameter('id',${'e_march'.$i.'_tva_id'});

                if ( $tva_rate->load() != 0 )
                    throw new Exception(_('La fiche ').${'e_march'.$i}._('a un code tva invalide').' ['.${'e_march'.$i.'_tva_id'}.']',13);
		/*
		 * check if the accounting for VAT are valid
		 */
		$a_poste=explode(',',$tva_rate->tva_poste);

		if (
		    $this->db->get_value('select count(*) from tmp_pcmn where pcm_val=$1',array($a_poste[0])) == 0 ||
		    $this->db->get_value('select count(*) from tmp_pcmn where pcm_val=$1',array($a_poste[1])) == 0 )
		  throw new Exception(_(" La TVA ".$tva_rate->tva_label." utilise des postes comptables inexistants"));

            }
            /* check if all card has a ATTR_DEF_ACCOUNT*/
            $fiche=new Fiche($this->db);
            $fiche->get_by_qcode(${'e_march'.$i});
            if ( $fiche->empty_attribute(ATTR_DEF_ACCOUNT) == true)
                throw new Exception(_('La fiche ').${'e_march'.$i}._('n\'a pas de poste comptable'),8);

            /* get the account and explode if necessary */
            $sposte=$fiche->strAttribut(ATTR_DEF_ACCOUNT);
            // if 2 accounts, take only the  debit
            if ( strpos($sposte,',') != 0 )
            {
                $array=explode(',',$sposte);
                $poste_val=$array[0];
            }
            else
            {
                $poste_val=$sposte;
            }

            /* The account exists */
            $poste=new Acc_Account_Ledger($this->db,$poste_val);
            if ( $poste->load() == false )
            {
                throw new Exception(_('Pour la fiche ').${'e_march'.$i}._(' le poste comptable').' ['.$poste->id._('n\'existe pas'),9);
            }
            /* Check if the card belong to the ledger */
            $fiche=new Fiche ($this->db);
            $fiche->get_by_qcode(${'e_march'.$i});
            if ( $fiche->belong_ledger($p_jrn,'deb') !=1 )
                throw new Exception(_('La fiche ').${'e_march'.$i}._('n\'est pas accessible à ce journal'),10);
            /**
             * we have to check also if the different accountings exist
             "ATTR_DEF_DEP_PRIV"
             "ATTR_DEF_DEPENSE_NON_DEDUCTIBLE"
             "ATTR_DEF_TVA_NON_DEDUCTIBLE"
             "ATTR_DEF_TVA_NON_DEDUCTIBLE_RECUP"
            */
            foreach (array(
			   array(ATTR_DEF_DEPENSE_NON_DEDUCTIBLE,'DNA',ATTR_DEF_ACCOUNT_ND),
			   array(ATTR_DEF_DEP_PRIV,'DEP_PRIV',ATTR_DEF_ACCOUNT_ND_PERSO),
			   array(ATTR_DEF_TVA_NON_DEDUCTIBLE_RECUP,'TVA_DED_IMPOT',ATTR_DEF_ACCOUNT_ND_TVA),
			   array(ATTR_DEF_TVA_NON_DEDUCTIBLE,'TVA_DNA',ATTR_DEF_ACCOUNT_ND_TVA_ND)) as $key)
	      {
                if ( ! $fiche->empty_attribute($key[0]) &&  $fiche->empty_attribute($key[2]))
		  {
                    $a=new Acc_Parm_Code($this->db,$key[1]);
                    if ( $this->db->count_sql('select pcm_val from tmp_pcmn where pcm_val=$1',array($a->p_value))==0)
		      throw new Exception ($key[1]._("ce code n'a pas de poste comptable, créez ce poste : [".$a->p_value."]"));
		  }
		if ( ! $fiche->empty_attribute($key[0]) &&  ! $fiche->empty_attribute($key[2]))
		  {
		    $nd_str=$fiche->strAttribut($key[2]);
		    if ( $nd_str != '')
		      {
			$poste_nd=new Acc_Account_Ledger($this->db,$nd_str);
			if ( $poste_nd->load() == false)
			  {
			    $nd_msg=sprintf(_("Pour la fiche %s, le compte contrepartie %s n'existe pas"),
					    $fiche->getName(),$poste_nd->id);
			    $nd_msg=h($nd_msg);
			    throw new Exception ($nd_msg);
			  }
		      }
		  }
	      }
	    $nb++;
        }

        if ( $nb == 0 )
            throw new Exception(_('Il n\'y a aucune marchandise'),12);

    }
    /**
     * Compute the ND amount thanks the attribute of the concerned card. The object 
     * $p_nd_amount will changed
     * 
     * @param Acc_Compute $p_nd_amount object with ND amount
     * @param Fiche $p_fiche Concerned Card (purchase items)
     * @param type $p_tva_bot 0 TVA on one side, 1 TVA on both side
     */
    private function compute_no_deductible(Acc_Compute $p_nd_amount, Fiche $p_fiche)
    {
        if (!$p_fiche->empty_attribute(ATTR_DEF_DEPENSE_NON_DEDUCTIBLE))
        {
            $p_nd_amount->amount_nd_rate = $p_fiche->strAttribut(ATTR_DEF_DEPENSE_NON_DEDUCTIBLE);
            $p_nd_amount->compute_nd();
        }
        if (!$p_fiche->empty_attribute(ATTR_DEF_TVA_NON_DEDUCTIBLE) )
        {
            $p_nd_amount->nd_vat_rate = $p_fiche->strAttribut(ATTR_DEF_TVA_NON_DEDUCTIBLE);
            $p_nd_amount->compute_nd_vat();
        }
        if (!$p_fiche->empty_attribute(ATTR_DEF_TVA_NON_DEDUCTIBLE_RECUP) )
        {
            $p_nd_amount->nd_ded_vat_rate = $p_fiche->strAttribut(ATTR_DEF_TVA_NON_DEDUCTIBLE_RECUP);
            $p_nd_amount->compute_ndded_vat();
        }

        if (!$p_fiche->empty_attribute(ATTR_DEF_DEP_PRIV))
        {
            $p_nd_amount->amount_perso_rate = $p_fiche->strAttribut(ATTR_DEF_DEP_PRIV);
            $p_nd_amount->compute_perso();
        }

    }

    /**
     * Insert into JRNX the No Deductible amount and into Analytic Accountancy for the ND VAT
     * @param Acc_Compute $p_nd_amount content ND amount
     * @param Fiche $p_fiche Card of the Service
     * @param type $p_tva_both  0 if TVA is normal or 1 if on both side
     * @param type $p_tot_debit total debit
     * @param $p_acc_operation Acc_Operation for inserting into jrnx
     * @param $p_group group for AC
     * @param $idx row number
     * 
     * @see Acc_Ledger_Purchase::insert
     */
    private function insert_no_deductible(Acc_Compute $p_nd_amount, Fiche $p_fiche, $p_tva_both,&$p_tot_debit,Acc_Operation $p_acc_operation,$p_group,$idx)
    {
        global $g_parameter;
        if ($p_acc_operation->jrnx_id == 0) {
            throw new Exception(__FILE__.__LINE__.'invalid acc_operation.j_id');
        }
        $source_j_id=$p_acc_operation->jrnx_id ;
        /*
         * Save all the no deductible
         *     ATTR_DEF_ACCOUNT_ND_TVA,ATTR_DEF_ACCOUNT_ND_TVA_ND,ATTR_DEF_ACCOUNT_ND_PERSO,ATTR_DEF_ACCOUNT_ND
         */
        if ($p_nd_amount->amount_nd_rate != 0)
        {
            $dna_default = new Acc_Parm_Code($this->db, 'DNA');

            /* save op. */
            if (!$p_fiche->empty_attribute(ATTR_DEF_ACCOUNT_ND))
            {
                $dna = $p_fiche->strAttribut(ATTR_DEF_ACCOUNT_ND);
            } else
            {
                $dna = $dna_default->p_value;
            }
            $dna = ($dna == '') ? $dna_default->p_value : $dna;

            $p_acc_operation->type = 'd';
            $p_acc_operation->amount = $p_nd_amount->amount_nd;
            $p_acc_operation->poste = $dna;
            $p_acc_operation->qcode = '';
            $p_acc_operation->desc=$this->find_label($dna)." ND ".$p_fiche->strAttribut(ATTR_DEF_QUICKCODE);
            if ($p_nd_amount->amount_nd > 0)
                $p_tot_debit = bcadd($p_tot_debit, $p_nd_amount->amount_nd );
            $j_id = $p_acc_operation->insert_jrnx();
        }
        /*
         * ATTR_DEF_ACCOUNT_ND_PERSO
         */
        if ($p_nd_amount->amount_perso != 0)
        {
            $dna_default = new Acc_Parm_Code($this->db, 'DEP_PRIV');

            /* save op. */
            $p_acc_operation->type = 'd';
            if (!$p_fiche->empty_attribute(ATTR_DEF_ACCOUNT_ND_PERSO))
            {
                $dna = $p_fiche->strAttribut(ATTR_DEF_ACCOUNT_ND_PERSO);
            } else
            {
                $dna = $dna_default->p_value;
            }
            $dna = ($dna == '') ? $dna_default->p_value : $dna;

            $p_acc_operation->amount = $p_nd_amount->amount_perso ;
            $p_acc_operation->poste = $dna;
            $p_acc_operation->qcode = '';
            $p_acc_operation->desc=$this->find_label($dna)." ND_PRIV ".$p_fiche->strAttribut(ATTR_DEF_QUICKCODE);
            if ($p_nd_amount->amount_perso> 0)
                $p_tot_debit = bcadd($p_tot_debit, $p_nd_amount->amount_perso);
            $j_id = $p_acc_operation->insert_jrnx();
        }
        if ($p_nd_amount->nd_vat != 0)
        {
            $dna_default = new Acc_Parm_Code($this->db, 'TVA_DNA');

            /* save op. */
            $p_acc_operation->type = 'd';
            $p_acc_operation->qcode = '';
            if (!$p_fiche->empty_attribute(ATTR_DEF_ACCOUNT_ND_TVA_ND) )
            {
                $dna = $p_fiche->strAttribut(ATTR_DEF_ACCOUNT_ND_TVA_ND);
            } else
            {
                $dna = $dna_default->p_value;
            }
            $dna = ($dna == '') ? $dna_default->p_value : $dna;

            $p_acc_operation->amount = $p_nd_amount->nd_vat;
            $p_acc_operation->poste = $dna;
            $p_acc_operation->desc=$this->find_label($dna)." ND_TVA ".$p_fiche->strAttribut(ATTR_DEF_QUICKCODE);
            $j_id = $p_acc_operation->insert_jrnx();
            if ( $g_parameter->MY_ANALYTIC != "nu" )
            {
                $op=new Anc_Operation($this->db);
                $op->oa_group=$p_group;
                $op->j_id=$j_id;
                $op->oa_date=$p_acc_operation->date;

                $op->oa_debit='t';
                $op->oa_description=sql_string('ND_TVA');
                $op->oa_jrnx_id_source=$source_j_id;
                $op->save_form_plan_vat_nd($_POST,$idx,$j_id,$p_nd_amount->nd_vat,$p_acc_operation->jrnx_id);
            }
            if ($p_nd_amount->nd_vat> 0)
                $p_tot_debit = bcadd($p_tot_debit, $p_nd_amount->nd_vat);
            
        }
        if ($p_nd_amount->nd_ded_vat != 0)
        {
            $dna_default = new Acc_Parm_Code($this->db, 'TVA_DED_IMPOT');
            /* save op. */
            if (!$p_fiche->empty_attribute(ATTR_DEF_ACCOUNT_ND_TVA) )
            {
                $dna = $p_fiche->strAttribut(ATTR_DEF_ACCOUNT_ND_TVA);
            } else
            {
                $dna = $dna_default->p_value;
            }
            $dna = ($dna == '') ? $dna_default->value : $dna;



            $p_acc_operation->type = 'd';
            $p_acc_operation->qcode = '';
            $p_acc_operation->amount = $p_nd_amount->nd_ded_vat;
            $p_acc_operation->poste = $dna;
            $p_acc_operation->desc=$this->find_label($dna)." DED_TVA ".$p_fiche->strAttribut(ATTR_DEF_QUICKCODE);
            if ($p_nd_amount->nd_ded_vat > 0)
                $p_tot_debit = bcadd($p_tot_debit, $p_nd_amount->nd_ded_vat);
            $j_id = $p_acc_operation->insert_jrnx();
           if ( $g_parameter->MY_ANALYTIC != "nu" )
            {
                $op=new Anc_Operation($this->db);
                $op->oa_group=$p_group;
                $op->j_id=$j_id;
                $op->oa_date=$p_acc_operation->date;

                $op->oa_debit='t';
                $op->oa_description=sql_string('DED_TVA ');
                $op->oa_jrnx_id_source=$source_j_id;
                $op->save_form_plan_vat_nd($_POST,$idx,$j_id,$p_nd_amount->nd_ded_vat);
            }
        }
    }

    /*!\brief insert into the database, it calls first the verify function
     * change the value of this->jr_id and this->jr_internal.
     * It generates the document and save the middle of payment, if 'gen_invoice is set
     * and e_mp
     *\param $p_array is usually $_POST or a predefined operation
    \code
     Array
    (

      [e_client] =>BELGACOM
      [nb_item] =>9
      [p_jrn] =>3
      [period] =>117
      [e_comm] =>Frais de téléphone
      [e_date] =>01.09.2009
      [e_ech] =>
      [jrn_type] =>ACH
      [e_pj] =>ACH53
      [e_pj_suggest] =>ACH53
      [mt] =>1265318941.39
      [e_mp] =>0
      [e_march0] =>TEL
      [e_march0_price] =>63.6700
      [e_march0_tva_id] =>1
      [e_march0_tva_amount] =>13.3700
      [e_quant0] =>1.000
      ...
      [bon_comm] =>
      [other_info] =>
      [record] =>Enregistrement
    )
    \endcode
     *\return string
     *\note throw an Exception
     */
    public function insert($p_array=null)
    {
        global $g_parameter;
        extract ($p_array);
        $this->verify($p_array) ;

        $group=$this->db->get_next_seq("s_oa_group"); /* for analytic */
        $seq=$this->db->get_next_seq('s_grpt');
        $this->id=$p_jrn;

        $internal=$this->compute_internal_code($seq);
        $this->internal=$internal;

        $cust=new Fiche($this->db);
        $cust->get_by_qcode($e_client);
        $sposte=$cust->strAttribut(ATTR_DEF_ACCOUNT);
        // if 2 accounts, take only the credit Supplier
        if ( strpos($sposte,',') != 0 )
        {
            $array=explode(',',$sposte);
            $poste=$array[1];
        }
        else
        {
            $poste=$sposte;
        }

        $oPeriode=new Periode($this->db);
        $check_periode=$this->check_periode();

        if ( $check_periode == true )
            $tperiode=$period;
        else
            $tperiode=$oPeriode->find_periode($e_date);

        bcscale(4);
        try
        {
            $tot_amount=0;
            $tot_tva=0;
            $tot_debit=0;
            $this->db->start();
            $tot_nd=0;
            $tot_perso=0;
            $tot_tva_nd=0;
            $tot_tva_ndded=0;
            $tot_tva_reversed=0;
			$tva=array();
            /* Save all the items without vat and no deductible vat and expense*/
            for ($i=0;$i< $nb_item;$i++)
            {
		$n_both=0;
                if ( strlen(trim(${'e_march'.$i})) == 0 ) continue;

                /* First we save all the items without vat */
                $fiche=new Fiche($this->db);
                $fiche->get_by_qcode(${"e_march".$i});
		$tva_both=0;
                /* tva */
                if ($g_parameter->MY_TVA_USE=='Y')
                {
                    $idx_tva=${'e_march'.$i.'_tva_id'};
                    $oTva=new Acc_Tva($this->db);
                    $oTva->set_parameter('id',$idx_tva);
                    $oTva->load();
                    $tva_both=$oTva->get_parameter("both_side");
                }
                /* -- Create acc_operation -- */
                $acc_operation=new Acc_Operation($this->db);
                $acc_operation->date=$e_date;
                $acc_operation->grpt=$seq;
                $acc_operation->jrn=$p_jrn;
                $acc_operation->type='d';
                $acc_operation->periode=$tperiode;
                $acc_operation->qcode="";
                
                
                /* We have to compute all the amount thanks Acc_Compute */
                $amount=round(bcmul(${'e_march'.$i.'_price'},${'e_quant'.$i}),2);
                
                $acc_amount=new Acc_Compute();
                $acc_amount->check=false;
                $acc_amount->set_parameter('amount',$amount);
                
                // Compute VAT or take the given one
                if ( $g_parameter->MY_TVA_USE=='Y')
                {
                    $acc_amount->set_parameter('amount_vat_rate',$oTva->get_parameter('rate'));
                    if ( strlen(trim(${'e_march'.$i.'_tva_amount'})) ==0 || ${'e_march'.$i.'_tva_amount'} == 0)
                    {
                        $acc_amount->compute_vat();

                    }
                    else
                    {
                        $acc_amount->amount_vat= ${'e_march'.$i.'_tva_amount'};

                    }
                   $tot_tva=bcadd($tot_tva,$acc_amount->amount_vat);
                }

               
                /* compute ND */
                $save_amount_vat=$acc_amount->amount_vat;
                $this->compute_no_deductible($acc_amount, $fiche);
                $acc_amount->correct();
                // TVA which avoid 
                $acc_amount->amount_unpaid=($tva_both == 1 ) ? $save_amount_vat :0 ;
                $tot_tva_reversed=bcadd($tot_tva_reversed,$acc_amount->amount_unpaid);
                

              
                $tot_amount=round(bcadd($tot_amount,$amount),2);

                /* get the account and explode if necessary */
                $sposte=$fiche->strAttribut(ATTR_DEF_ACCOUNT);
                // if 2 accounts, take only the debit one for customer
                if ( strpos($sposte,',') != 0 )
                {
                    $array=explode(',',$sposte);
                    $poste_val=$array[0];
                }
                else
                {
                    $poste_val=$sposte;
                }
                if ( $g_parameter->MY_UPDLAB=='Y')
                    $acc_operation->desc=strip_tags(${"e_march".$i."_label"});
                else
                    $acc_operation->desc=null;
                $acc_operation->poste=$poste_val;
                $acc_operation->amount=$acc_amount->amount;
                $acc_operation->qcode=${"e_march".$i};
                if( $acc_amount->amount > 0 ) $tot_debit=bcadd($tot_debit,$acc_amount->amount);
                $j_id=$acc_operation->insert_jrnx();
                
                /* insert ND */
                $this->insert_no_deductible($acc_amount, $fiche, $tva_both, $tot_debit,$acc_operation,$group,$i);

                
                /* Compute sum vat */
                if ( $g_parameter->MY_TVA_USE=='Y')
                {
                    $tva_item=$acc_amount->amount_vat;

                    if (isset($tva[$idx_tva] ) )
                        $tva[$idx_tva]=bcadd($tva[$idx_tva],$tva_item);
                    else
                        $tva[$idx_tva]=$tva_item;

                }
                /* Save the stock */
                /* if the quantity is < 0 then the stock increase (return of
                 *  material)
                 */
                $nNeg=(${"e_quant" . $i}< 0) ? -1 : 1;

                // always save quantity but in withStock we can find
                // what card need a stock management
                if ( $g_parameter->MY_STOCK='Y'&& isset ($repo))
                {
                    $dir=(${'e_quant'.$i} < 0 ) ? 'c':'d';
                    Stock_Goods::insert_goods($this->db,array('j_id'=>$j_id,'goods'=>${'e_march'.$i},'quant'=>$nNeg*${'e_quant'.$i},'dir'=>$dir,'repo'=>$repo)) ;
                }

                if ( $g_parameter->MY_ANALYTIC != "nu" )
                {
                    // for each item, insert into operation_analytique */
                    $op=new Anc_Operation($this->db);
                    $op->oa_group=$group;
                    $op->j_id=$j_id;
                    $op->oa_date=$e_date;
                    $op->oa_debit='t';
                    $op->oa_description=sql_string($e_comm);
                    $op->save_form_plan($_POST,$i,$j_id);
                }
                // insert into quant_purchase
                //-----
                if ( $g_parameter->MY_TVA_USE=='Y')
                {

                    $r=$this->db->exec_sql("select insert_quant_purchase ".
                                           "(null".
                                           ",".$j_id.		 /* 2 */
                                           ",'".${"e_march".$i}."'". /* 3 */
                                           ",".${"e_quant".$i}.",".  /* 4 */
                                           round($amount,2).	       /* 5 */
                                           ",".$acc_amount->amount_vat. /* 6 */
                                           ",".$oTva->get_parameter('id'). /* 7 */
                                           ",".$acc_amount->amount_nd.     /* 8 */
                                           ",".$acc_amount->nd_vat.	     /* 9 */
                                           ",".$acc_amount->nd_ded_vat.    /* 10 */
                                           ",".$acc_amount->amount_perso.  /* 11 */ 
                                           ",'".$e_client."',". $acc_amount->amount_unpaid.")");	     /* 12 */

                }
                else
                {
                    $r=$this->db->exec_sql("select insert_quant_purchase ".
                                           "(null".
                                           ",".$j_id.
                                           ",'".${"e_march".$i}."'".
                                           ",".${"e_quant".$i}.",".
                                           round($amount,2).
                                           ",0".
                                           ",null".
                                           ",".$acc_amount->amount_nd.
                                           ",0".
                                           ",".$acc_amount->nd_ded_vat.
                                           ",".$acc_amount->amount_perso.
                                           ",'".$e_client."',".$acc_amount->amount_unpaid.")");


                }

            }       // end loop : save all items
            /*  save total customer */
            $cust_amount=round(bcadd($tot_amount,$tot_tva),2);
            $acc_operation=new Acc_Operation($this->db);
            $acc_operation->date=$e_date;
            $acc_operation->poste=$poste;
            $acc_operation->amount=$cust_amount-$tot_tva_reversed;
            $acc_operation->grpt=$seq;
            $acc_operation->jrn=$p_jrn;
            $acc_operation->type='c';
            $acc_operation->periode=$tperiode;
            $acc_operation->qcode=${"e_client"};
            if ( $cust_amount < 0 )
				$tot_debit=bcadd($tot_debit,abs($cust_amount));
            $let_client=$acc_operation->insert_jrnx();
            

            if ( $g_parameter->MY_TVA_USE=='Y')
            {
                /* save all vat
                 * $i contains the tva_id and value contains the vat amount
                 */
                foreach ($tva as $i => $value)
                {
                    $oTva=new Acc_Tva($this->db);
                    $oTva->set_parameter('id',$i);
                    $oTva->load();

                    $poste_vat=$oTva->get_side('d');

                    $cust_amount=bcadd($tot_amount,$tot_tva);
                    $acc_operation=new Acc_Operation($this->db);
                    $acc_operation->date=$e_date;
                    $acc_operation->poste=$poste_vat;
                    $acc_operation->amount=$value;
                    $acc_operation->grpt=$seq;
                    $acc_operation->jrn=$p_jrn;
                    $acc_operation->type='d';
                    $acc_operation->periode=$tperiode;
                    if ( $value > 0 ) $tot_debit=bcadd($tot_debit,abs($value));
                    $acc_operation->insert_jrnx();
                    // if TVA is on both side, we deduce it immediately
                    if ( $oTva->get_parameter("both_side")==1)
                    {
                        $poste_vat=$oTva->get_side('c');
                        $cust_amount=bcadd($tot_amount,$tot_tva);
                        $cust_amount=bcsub($tot_amount,$tot_tva_reversed);
                        $acc_operation=new Acc_Operation($this->db);
                        $acc_operation->date=$e_date;
                        $acc_operation->poste=$poste_vat;
                        $acc_operation->amount=$tot_tva_reversed;
                        $acc_operation->grpt=$seq;
                        $acc_operation->jrn=$p_jrn;
                        $acc_operation->type='c';
                        $acc_operation->periode=$tperiode;
                        $acc_operation->insert_jrnx();
			if ( $value < 0 ) $tot_debit=bcadd($tot_debit,abs($value));
                    }

                }
            }
            /* insert into jrn */
            $acc_operation=new Acc_Operation($this->db);
            $acc_operation->date=$e_date;
            $acc_operation->echeance=$e_ech;
            $acc_operation->amount=abs(round($tot_debit,2));
            $acc_operation->desc=$e_comm;
            $acc_operation->grpt=$seq;
            $acc_operation->jrn=$p_jrn;
            $acc_operation->periode=$tperiode;
            $acc_operation->pj=$e_pj;
            $acc_operation->mt=$mt;
            $this->jr_id=$acc_operation->insert_jrn();
            $this->pj=$acc_operation->set_pj();

            // Set Internal code
            $this->grpt_id=$seq;
            $this->update_internal_code($internal);
            /* update quant_purchase */
            $this->db->exec_sql('update quant_purchase set qp_internal = $1 where j_id in (select j_id from jrnx where j_grpt=$2)',
                                array($internal,$seq));

            /**= e_pj then do not increment sequence */
            if ( strcmp($e_pj,$e_pj_suggest) == 0 && strlen(trim($e_pj)) != 0 )
            {
                $this->inc_seq_pj();
            }

            /* Save the attachment */
            if ( isset ($_FILES))
            {
                if ( sizeof($_FILES) != 0 )
                    $this->db->save_upload_document($seq);
            }
            $str_file="";
            /* Generate an document  and save it into the database (Note de frais only)
             */
            if ( isset($_POST['gen_invoice']) )
            {
                $ref_doc= $this->create_document($internal,$p_array);
                $this->doc='<A class="line" HREF="show_pj.php?'.dossier::get().'&jr_grpt_id='.$seq.'&jrn='.$this->id.'">'.$ref_doc.'</A>';
            }

            //----------------------------------------
            // Save the payer
            //----------------------------------------
            if ( $e_mp != 0 )
            {
                /* mp */
                $mp=new Acc_Payment($this->db,$e_mp);
                $mp->load();

                /* fiche */
                if ($mp->get_parameter('qcode') == '')
                    $fqcode=${'e_mp_qcode_'.$e_mp};
                else
                    $fqcode=$mp->get_parameter('qcode');

                $acfiche = new Fiche($this->db);
                $acfiche->get_by_qcode($fqcode);

                /* jrnx */
                $acseq=$this->db->get_next_seq('s_grpt');
                $acjrn=new Acc_Ledger($this->db,$mp->get_parameter('ledger_target'));
                $acinternal=$acjrn->compute_internal_code($acseq);

                /* Insert paid by  */
                $acc_pay=new Acc_Operation($this->db);
                $acc_pay->date=$e_date;

                /* get the account and explode if necessary */
                $sposte=$acfiche->strAttribut(ATTR_DEF_ACCOUNT);
                // if 2 accounts, take only the debit one for customer
                if ( strpos($sposte,',') != 0 )
                {
                    $array=explode(',',$sposte);
                    $poste_val=$array[1];
                }
                else
                {
                    $poste_val=$sposte;
                }

                $famount=bcsub($cust_amount,$acompte);
                $acc_pay->poste=$poste_val;
                $acc_pay->qcode=$fqcode;
                $acc_pay->amount=abs(round($famount,2));
                $acc_pay->desc='';
                $acc_pay->grpt=$acseq;
                $acc_pay->jrn=$mp->get_parameter('ledger_target');
                $acc_pay->periode=$tperiode;
		$acc_pay->type=($famount>=0)?'c':'d';
                $acc_pay->insert_jrnx();

                /* Insert supplier  */
                $acc_pay=new Acc_Operation($this->db);
                $acc_pay->date=$e_date;
                $acc_pay->poste=$poste;
                $acc_pay->qcode=$e_client;
                $acc_pay->amount=abs(round($famount,2));
                $acc_pay->desc='';
                $acc_pay->grpt=$acseq;
                $acc_pay->jrn=$mp->get_parameter('ledger_target');
                $acc_pay->periode=$tperiode;
				$acc_pay->type=($famount>=0)?'d':'c';
                $let_other=$acc_pay->insert_jrnx();

                /* insert into jrn */
                $acc_pay->mt=$mt;
				$acc_pay->desc=(!isset($e_comm_paiement) || strlen(trim($e_comm_paiement)) == 0) ?$e_comm:$e_comm_paiement;
                $mp_jr_id=$acc_pay->insert_jrn();
                $acjrn->grpt_id=$acseq;
                $acjrn->update_internal_code($acinternal);

                $r1=$this->get_id($internal);
                $r2=$this->get_id($acinternal);

		/*
		 * add lettering
		 */
		$oletter=new Lettering($this->db);
		$oletter->insert_couple($let_client,$let_other);

                /* set the flag paid */
                $Res=$this->db->exec_sql("update jrn set jr_rapt='paid' where jr_id=$1",array($r1));

                /* Reconcialiation */
                $rec=new Acc_Reconciliation($this->db);
                $rec->set_jr_id($r1);
                $rec->insert($r2);
		/*
		 * save also into quant_fin
		 */

		/* get ledger property */
		$ledger=new Acc_Ledger_Fin($this->db,$acc_pay->jrn);
		$prop=$ledger->get_propertie();

		/* if ledger is FIN then insert into quant_fin */
		if ( $prop['jrn_def_type'] == 'FIN' )
		  {
		    $ledger->insert_quant_fin($acfiche->id,$mp_jr_id,$cust->id,bcmul($famount,-1));
		  }


            }
        }//end try
        catch (Exception $e)
        {
            echo '<span class="error">'.
            'Erreur dans l\'enregistrement '.
            __FILE__.':'.__LINE__.' '.
            $e->getMessage().$e->getTraceAsString();

            $this->db->rollback();
            throw  new Exception($e);
        }
        $this->db->commit();
        return $internal;
    }

    /*!\brief display the form for entering data for invoice
     *\param $p_array is null or you can put the predef operation or the $_POST
    \code
    array
    'sa' => string 'n' (length=1)
    'p_action' => string 'ach' (length=3)
    'gDossier' => string '28' (length=2)
    'e_client' => string 'ASEKURA' (length=7)
    'nb_item' => string '9' (length=1)
    'p_jrn' => string '3' (length=1)
    'period' => string '126' (length=3)
    'e_comm' => string 'descriptio' (length=10)
    'e_date' => string '01.05.2010' (length=10)
    'e_ech' => string '' (length=0)
    'jrn_type' => string 'ACH' (length=3)
    'e_pj' => string 'ACH37' (length=5)
    'e_pj_suggest' => string 'ACH37' (length=5)
    'mt' => string '1273759434.5701' (length=15)
    'e_mp' => string '0' (length=1)
    'e_march0' => string 'DOC' (length=3)
    'e_march0_price' => string '2000' (length=4)
    'e_march0_tva_id' => string '3' (length=1)
    'e_march0_tva_amount' => string '120' (length=3)
    'e_quant0' => string '1' (length=1)
    'gen_invoice' => string 'on' (length=2)
    'gen_doc' => string '7' (length=1)
    'bon_comm' => string '' (length=0)
    'other_info' => string '' (length=0)
    'correct' => string 'Corriger' (length=8)
    \endcode
     *\return HTML string
     */
    public function input($p_array=null,$p_readonly=0)
    {
        global $g_parameter,$g_user;
        if ( $p_array != null ) extract($p_array);

        $flag_tva=$g_parameter->MY_TVA_USE;
        /* Add button */
        $f_add_button=new IButton('add_card');
        $f_add_button->label=_('Créer une nouvelle fiche');
		$f_add_button->tabindex=-1;
        $f_add_button->set_attribute('jrn',$this->id);
        $f_add_button->javascript=" this.jrn=\$('p_jrn').value;select_card_type(this);";

        $str_add_button="";
        if ($g_user->check_action(FICADD)==1)
        {
                $str_add_button=$f_add_button->input();
        }
        // The first day of the periode
        $oPeriode=new Periode($this->db);
        list ($l_date_start,$l_date_end)=$oPeriode->get_date_limit($g_user->get_periode());
        if (  $g_parameter->MY_DATE_SUGGEST=='Y' )
            $op_date=( ! isset($e_date) ) ?$l_date_start:$e_date;
        else
            $op_date=( ! isset($e_date) ) ?'':$e_date;

        $e_ech=(isset($e_ech))?$e_ech:"";
        $e_comm=(isset($e_comm))?$e_comm:"";

        $r="";
        $r.=dossier::hidden();
        $f_legend_detail=_("Détail articles achetés");

        //  Date
        //--
        $Date=new IDate();
        $Date->setReadOnly(false);
        $Date->table=1;
        $Date->tabindex=1;
        $f_date=$Date->input("e_date",$op_date);
        // Payment limit
        //--
        $Echeance=new IDate();
        $Echeance->setReadOnly(false);
        $Echeance->tabindex=2;
        $label=HtmlInput::infobulle(4);
        $f_echeance=$Echeance->input('e_ech',$e_ech,'Echéance'.$label);
        $f_periode="";
        if ($this->check_periode() == true)
        {
            // Periode
            //--
            $l_user_per=$g_user->get_periode();
            $def=(isset($periode))?$periode:$l_user_per;

            $period=new IPeriod("period");
            $period->user=$g_user;
            $period->cn=$this->db;
            $period->value=$def;
            $period->type=OPEN;
            try
            {
                $l_form_per=$period->input();
            }
            catch (Exception $e)
            {
                if ($e->getCode() == 1 )
                {
                    throw new Exception( _("Aucune période ouverte"));
                }
            }

            $r.="<td>";
            $label=HtmlInput::infobulle(3);
            $f_periode=_("Période comptable")." $label ".$l_form_per;
        }
        // Ledger (p_jrn)
        //--
        /* if we suggest the next pj, then we need a javascript */
        $add_js="";
        if ( $g_parameter->MY_PJ_SUGGEST=='Y')
        {
            $add_js="update_pj();";
        }
		if ($g_parameter->MY_DATE_SUGGEST == 'Y')
		{
			$add_js.='get_last_date();';
		}
		$add_js.='update_name();';
		$add_js.='update_pay_method();';
		$add_js.='update_row("sold_item");';

		$wLedger=$this->select_ledger('ACH',2);
        if ($wLedger == null) throw  new Exception(_('Pas de journal disponible'));
        $wLedger->javascript="onChange='update_predef(\"ach\",\"f\",\"".$_REQUEST['ac']."\");$add_js'";
        $wLedger->table=1;
        $f_jrn=$wLedger->input();

        // Comment
        //--
        $Commentaire=new IText();
        $Commentaire->table=0;
        $Commentaire->setReadOnly(false);
        $Commentaire->size=60;
        $Commentaire->tabindex=3;
        $label=HtmlInput::infobulle(1) ;
        $f_desc=$Commentaire->input("e_comm",h($e_comm));

        // PJ
        //--
        /* suggest PJ ? */
        $default_pj='';
        if ( $g_parameter->MY_PJ_SUGGEST=='Y')
        {
            $default_pj=$this->guess_pj();
        }

        $pj=new IText();
        $pj->value=(isset($e_pj))?$e_pj:$default_pj;


        $pj->table=0;
        $pj->name="e_pj";
        $pj->size=10;
        $pj->readonly=false;

        $f_pj=$pj->input().HtmlInput::hidden('e_pj_suggest',$default_pj);

        // Display the customer
        //--
        $fiche='cred';

        // Save old value and set a new one
        //--
        $e_client=( isset ($e_client) )?$e_client:"";
        $e_client_label="&nbsp;";//str_pad("",100,".");


        // retrieve e_client_label
        //--

        if ( strlen(trim($e_client)) !=  0)
        {
            $fClient=new Fiche($this->db);
            $fClient->get_by_qcode($e_client);
            $e_client_label=$fClient->strAttribut(ATTR_DEF_NAME).' '.
                            ' Adresse : '.$fClient->strAttribut(ATTR_DEF_ADRESS).' '.
                            $fClient->strAttribut(ATTR_DEF_CP).' '.
                            $fClient->strAttribut(ATTR_DEF_CITY).' ';


        }

        $W1=new ICard();
        $W1->label=_("Fournisseur ").HtmlInput::infobulle(0) ;
        $W1->name="e_client";
        $W1->tabindex=3;
        $W1->value=$e_client;
        $W1->table=0;
        $W1->set_dblclick("fill_ipopcard(this);");
        $W1->set_attribute('ipopup','ipopcard');

        // name of the field to update with the name of the card
        $W1->set_attribute('label','e_client_label');
        // name of the field to update with the name of the card
        $W1->set_attribute('typecard','cred');

        // Add the callback function to filter the card on the jrn
        $W1->set_callback('filter_card');
        $W1->set_function('fill_data');
        $W1->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
                                $W1->name);
        $f_client_qcode=$W1->input();
        $client_label=new ISpan();
        $client_label->table=0;
        $f_client=$client_label->input("e_client_label",$e_client_label);
        $f_client_bt=$W1->search();


        // Record the current number of article

        $e_comment=(isset($e_comment))?$e_comment:"";
		$p_article= ( isset ($nb_item))?$nb_item:$this->get_min_row();
        $max=($p_article < $this->get_min_row())?$this->get_min_row():$p_article;

        $Hid=new IHidden();
		$r.=$Hid->input("nb_item",$p_article);

        // For each article
        //--
        for ($i=0;$i< $max ;$i++)
        {
            // Code id, price & vat code
            //--
            $march=(isset(${"e_march$i"}))?${"e_march$i"}:""                   ;
            $march_price=(isset(${"e_march".$i."_price"}))?${"e_march".$i."_price"}:""
                         ;
            /* use vat */
            if ( $g_parameter->MY_TVA_USE=='Y')
            {
                $march_tva_id=(isset(${"e_march$i"."_tva_id"}))?${"e_march$i"."_tva_id"}:"";
                $march_tva_amount=(isset(${"e_march$i"."_tva_amount"}))?${"e_march$i"."_tva_amount"}:"";
            }



            $march_label=(isset(${"e_march".$i."_label"}))?${"e_march".$i."_label"}:"";
            // retrieve the tva label and name
            //--
            if ( strlen(trim($march))!=0  && strlen(trim($march_label))==0 )
            {
                $fMarch=new Fiche($this->db);
                $fMarch->get_by_qcode($march);
                $march_label=$fMarch->strAttribut(ATTR_DEF_NAME);
                /* vat use */
                if ( ! isset($march_tva_id) && $g_parameter->MY_TVA_USE=='Y' )
                    $march_tva_id=$fMarch->strAttribut(ATTR_DEF_TVA);
            }
            // Show input
            //--
            $W1=new ICard();
            $W1->label="";
            $W1->name="e_march".$i;
            $W1->value=$march;
            $W1->table=1;
            $W1->set_dblclick("fill_ipopcard(this);");
            $W1->set_attribute('ipopup','ipopcard');

            $W1->set_attribute('typecard','deb');

            // name of the field to update with the name of the card
            $W1->set_attribute('label','e_march'.$i.'_label');
            // name of the field with the price
            $W1->set_attribute('purchase','e_march'.$i.'_price'); /* autocomplete */
            $W1->set_attribute('price','e_march'.$i.'_price');    /* via search */

            // name of the field with the TVA_ID
            $W1->set_attribute('tvaid','e_march'.$i.'_tva_id');
            // Add the callback function to filter the card on the jrn
            $W1->set_callback('filter_card');
            $W1->set_function('fill_data');
            $W1->javascript=sprintf(' onchange="fill_data_onchange(\'%s\');" ',
                                    $W1->name);
            $W1->readonly=false;
            $array[$i]['quick_code']=$W1->input();
            $array[$i]['bt']=$W1->search();

            $array[$i]['hidden']='';
            // For computing we need some hidden field for holding the value
            if ( $g_parameter->MY_TVA_USE=='Y')
            {
                $array[$i]['hidden'].=HtmlInput::hidden('tva_march'.$i,0);
            }

            if ( $g_parameter->MY_TVA_USE=='Y')
                $tvac=new INum('tvac_march'.$i);
            else
                $tvac=new IHidden('tvac_march'.$i);

            $tvac->readOnly=1;
            $tvac->value=0;
            $array[$i]['tvac']=$tvac->input();

            $htva=new INum('htva_march'.$i);
            $htva->readOnly=1;

            $htva->value=0;
            $array[$i]['htva']=$htva->input();

            if ( $g_parameter->MY_UPDLAB == 'Y')
            {
                $Span=new IText("e_march".$i."_label");
                $Span->css_size="100%";
            } else
            {
                $Span=new ISpan("e_march".$i."_label");
            }
            $Span->value=$march_label;
            $Span->setReadOnly(false);
            // card's name, price
            //--
            $array[$i]['denom']=$Span->input("e_march".$i."_label",$march_label);
            // price
            $Price=new INum();
            $Price->setReadOnly(false);
            $Price->size=9;
            $Price->javascript="onBlur='format_number(this);clean_tva($i);compute_ledger($i)'";
            $array[$i]['pu']=$Price->input("e_march".$i."_price",$march_price);
            if ( $g_parameter->MY_TVA_USE=='Y')
            {

                // vat label
                //--
                $Tva=new ITva_Popup($this->db);
                $Tva->js="onblur=\"format_number(this);onChange=clean_tva($i);compute_ledger($i)\"";
                $Tva->in_table=true;
                $Tva->set_attribute('compute',$i);
                $Tva->value=$march_tva_id;
                $array[$i]['tva']=$Tva->input("e_march$i"."_tva_id");

                // Tva_amount

                // price
                $Tva_amount=new INum();
                $Tva_amount->setReadOnly(false);
                $Tva_amount->size=9;
                $Tva_amount->javascript="onBlur='format_number(this);compute_ledger($i)'";
                $array[$i]['amount_tva']=$Tva_amount->input("e_march".$i."_tva_amount",$march_tva_amount);
            }
            // quantity
            //--
            $quant=(isset(${"e_quant$i"}))?${"e_quant$i"}:"1"
                   ;
            $Quantity=new INum();
            $Quantity->setReadOnly(false);
            $Quantity->size=9;
            $Quantity->javascript="onChange=format_number(this);clean_tva($i);compute_ledger($i)";
            $array[$i]['quantity']=$Quantity->input("e_quant".$i,$quant);

        }
        $f_type=_('Fournisseur');

        ob_start();
        require_once NOALYSS_INCLUDE.'/template/form_ledger_detail.php';
        $r.=ob_get_contents();
        ob_end_clean();

        // Set correctly the REQUEST param for jrn_type
        $r.= HtmlInput::hidden('jrn_type','ACH');
        $r.= HtmlInput::button('add_item',_('Ajout article'),      ' onClick="ledger_add_row()"');



        /* if we suggest the pj n# the run the script */
        if ( $g_parameter->MY_PJ_SUGGEST=='Y')
        {
            $r.='<script> update_pj();</script>';
        }
		// set focus on date
		$r.= create_script("$('".$Date->id."').focus()");
        return $r;
    }

    /*!@brief show the summary of the operation and propose to save it
     *@param array contains normally $_POST. It proposes also to save
     * the Analytic accountancy
	 * @param $p_summary true to confirm false, show only the result in RO
     *@return string
     */
    function confirm($p_array,$p_summary=false)
    {
        global $g_parameter;
        extract ($p_array);

		// we don't need to verify if we need only a feedback
        if ( ! $p_summary )
			$this->verify($p_array) ;

		$anc=null;
        // to show a select list for the analytic
        // if analytic is op (optionnel) there is a blank line

        bcscale(4);
        $client=new Fiche($this->db);
        $client->get_by_qcode($e_client,true);

        $client_name=h($client->getName().
                       ' '.$client->strAttribut(ATTR_DEF_ADRESS).' '.
                       $client->strAttribut(ATTR_DEF_CP).' '.
                       $client->strAttribut(ATTR_DEF_CITY));
        $lPeriode=new Periode($this->db);
        if ($this->check_periode() == true)
        {
            $lPeriode->p_id=$period;
        }
        else
        {
            $lPeriode->find_periode($e_date);
        }
        $date_limit=$lPeriode->get_date_limit();
        $r="";
        $r.='<TABLE>';
        if ( $p_summary ) {
            $jr_id=$this->db->get_value('select jr_id from jrn where jr_internal=$1',array($this->internal));
            $r.="<tr>";
            $r.='<td>';
            $r.=_('Détail opération ');
            $r.='</td>';
            $r.='<td>';
            $r.=sprintf ('<a class="line" style="display:inline" href="javascript:modifyOperation(%d,%d)">%s</a>',
                    $jr_id,dossier::id(),$this->internal);
            $r.='</td>';
            $r.="</tr>";
        }
        $r.='<tr>';
         if ( ! $p_summary) {
            $r.='<td>' . _('Numéro Pièce') .'</td><td>'. hb($e_pj) . '</td>';
        } else {
            
             if ( strcmp($this->pj,$e_pj) != 0 )
            {
                $r.='<td>' . _('Numéro Pièce') .'</td><td>'. hb($this->pj) . 
                        '<span class="notice"> '._('Attention numéro pièce existante, elle a du être adaptée').'</span></td>';
            } else {
                $r.='<td>' . _('Numéro Pièce') .'</td><td>'. hb($this->pj) . '</td>';
            }
        }
        $r.='</tr>';
        $r.='<td> ' . _('Date') . '</td><td> ' . hb($e_date) . '</td>';
        $r.='</tr>';
        $r.='<tr>';
        $r.='<td>' . _('Echeance') . '</td><td> ' . hb($e_ech) . '</td>';
        $r.='</tr>';
       
     
        $r.='<tr>';
        $r.='<td> ' . _('Période Comptable') . '</td><td> ' .hb( $date_limit['p_start'] . '-' . $date_limit['p_end']) . '</td>';
        $r.='</tr>';
        $r.='<tr>';
        $r.='<td> ' . _('Journal') . '</td><td> ' . hb($this->get_name()) . '</td>';
        $r.='</tr>';
        $r.='<tr>';
        $r.='<td> ' . _('Libellé') . '</td><td> ' . hb($e_comm) . '</td>';
        $r.='</tr>';
        $r.='<tr>';
        
        $r.='<tr>';
        $r.='<td> ' . _('Fournisseur') . '</td><td> ' . hb($e_client . ':' . $client_name) . '</td>';
        $r.='</tr>';
        $r.='</table>';
        $r.='<h2>' . _('Détail articles achetés') . '</h2>';
        $r.='<p class="decale">';
        $r.='<table class="result" >';
        $r.='<TR>';
        $r.="<th>" . _('Code') . "</th>";
        $r.="<th>" . _('Dénomination') . "</th>";
        $r.="<th style=\"text-align:right\">" . _('prix') . "</th>";
        $r.="<th style=\"text-align:right\">" . _('quantité') . "</th>";


        if ($g_parameter->MY_TVA_USE == 'Y') {
            $r.="<th style=\"text-align:right\">" . _('tva') . "</th>";
            $r.='<th style="text-align:right"> ' . _('Montant TVA') . '</th>';
            $r.='<th style="text-align:right">' . _('Montant HTVA') . '</th>';
            $r.='<th style="text-align:right">' . _('Montant TVAC') . '</th>';
        } else {
            $r.='<th style="text-align:right">' . _('Montant') . '</th>';
        }

        /* if we use the AC */
        if ($g_parameter->MY_ANALYTIC!='nu')
        {
            $anc=new Anc_Plan($this->db);
            $a_anc=$anc->get_list();
            $x=count($a_anc);
            /* set the width of the col */
            $r.='<th colspan="'.$x.'">'._('Compt. Analytique').'</th>';

            /* add hidden variables pa[] to hold the value of pa_id */
            $r.=Anc_Plan::hidden($a_anc);
        }

        $r.='</tr>';
        $tot_amount=0.0;
        $tot_tva=0.0;
        //--
        // For each item
        //--
        for ($i = 0; $i < $nb_item;$i++)
        {
			$tot_row=0;
            if ( strlen(trim(${"e_march".$i})) == 0 ) continue;

            /* retrieve information for card */
            $fiche=new Fiche($this->db);
            $fiche->get_by_qcode(${"e_march".$i});
            if ( $g_parameter->MY_UPDLAB=='Y')
                $fiche_name=h(${"e_march".$i."_label"});
            else
                $fiche_name=$fiche->strAttribut (ATTR_DEF_NAME);
            $amount=bcmul(${"e_march".$i."_price"},${'e_quant'.$i});
            if ( $g_parameter->MY_TVA_USE=='Y')
            {
                $idx_tva=${"e_march".$i."_tva_id"};
                $oTva=new Acc_Tva($this->db);
                $oTva->set_parameter('id',$idx_tva);
                $oTva->load();
                $op=new Acc_Compute();

                $op->set_parameter("amount",$amount);
                $op->set_parameter('amount_vat_rate',$oTva->get_parameter('rate'));
                $op->compute_vat();
                $tva_computed=$op->get_parameter('amount_vat');
                //----- if tva_amount is not given we compute the vat ----
                if ( strlen (trim (${'e_march'.$i.'_tva_amount'})) == 0)
                {
                    $tva_item=$op->get_parameter('amount_vat');
                }
                else
                    $tva_item=round(${'e_march'.$i.'_tva_amount'},2);

                if (isset($tva[$idx_tva] ) )
                    $tva[$idx_tva]=bcadd($tva_item,$tva[$idx_tva]);
                else
                    $tva[$idx_tva]=$tva_item;
               
		
                
            }
            $tot_amount=round(bcadd($tot_amount,$amount),2);
            $tot_row=round(bcadd($tot_row,$amount),2);
            $r.='<tr>';
            $r.='<td>';
            $r.=${"e_march".$i};
            $r.='</td>';
            $r.='<TD style="border-bottom:1px dotted grey;">';
            $r.=$fiche_name;
            $r.='</td>';
            $r.='<td class="num">';
            $r.=nbm(${"e_march".$i."_price"});
            $r.='</td>';
            $r.='<td class="num">';
            $r.=nbm(${"e_quant".$i});
            $r.='</td>';
            $both_side=0;
            if ($g_parameter->MY_TVA_USE == 'Y')
            {
                $r.='<td class="num">';
                $r.=$oTva->get_parameter('label');
                $both_side=$oTva->get_parameter("both_side");
                if ( $both_side == 0) {
                    $tot_row=bcadd($tot_row,$tva_item);
                    $tot_tva=round(bcadd($tva_item,$tot_tva),2);
                }
                $r.='</td>';
                /* warning if tva_computed and given are not the
                   same */
                $css_void_tva=($both_side == 1)?'style="text-decoration:line-through"':'';
                if ( bcsub($tva_item,$tva_computed) != 0 && ! ($tva_item == 0 && $both_side == 1))
                {

					 $r.='<td style="background-color:red" class="num" '.$css_void_tva.'>';
					 $r.=HtmlInput::infobulle(28);
                                         $r.='<a href="#" class="error" style="display:inline" title="'. _("Attention Différence entre TVA calculée et donnée").'">'
							.nbm($tva_item).'<a>';
                }
                else{
                        $r.='<td  class="num" '.$css_void_tva.'>';
                        $r.=nbm($tva_item);
                }
                $r.='</td>';
                $r.='<td class="num"> ';
                $r.=nbm(round($amount,2));
                $r.='</td>';
            }
            $r.='<td class="num">';
            $r.=nbm(round($tot_row,2));
            $r.='</td>';
            // encode the pa
            if ( $g_parameter->MY_ANALYTIC!='nu') // use of AA
            {
                // show form
                $anc_op=new Anc_Operation($this->db);
                $null=($g_parameter->MY_ANALYTIC=='op')?1:0;
                $r.='<td>';
                $p_mode=($p_summary==false)?1:0;
                $p_array['pa_id']=$a_anc;
                /* op is the operation it contains either a sequence or a jrnx.j_id */
                $r.=HtmlInput::hidden('op[]=',$i);
                $r.=$anc_op->display_form_plan($p_array,$null,$p_mode,$i,$amount);
                $r.='</td>';
            }


            $r.='</tr>';

        }
        // Add the sum
        $decalage=($g_parameter->MY_TVA_USE == 'Y')?'<td></td><td></td><td></td><td></td>':'<td></td>';
         $tot = round(bcadd($tot_amount, $tot_tva), 2);
        $tot_tva=nbm($tot_tva);
        $tot=nbm($tot);
        $str_tot=_('Totaux');
        $tot_amount=nbm($tot_amount);
        $r.=<<<EOF
<tr class="highlight">
    {$decalage}            
     <td>
                {$str_tot}
     </td>
    <td class="num">
        {$tot_tva}
    </td>
    <td class="num">
        {$tot_amount}
    </td>
    <td class="num">
        {$tot}
    </td>
EOF;

        $r.='</table>';
        $r.='</p>';
        if ( $g_parameter->MY_ANALYTIC!='nu' && !$p_summary) // use of AA
            $r.='<input type="button" class="button" value="'._('Vérifiez imputation analytique').'" onClick="verify_ca(\'\');">';
        
        $r.=(! $p_summary )?'<div id="total_div_id" >':'<div>';
        $r.='<h2>Totaux</h2>';
        /* use VAT */
        if ($g_parameter->MY_TVA_USE == 'Y') {
            $r.='<table>';
            $r.='<tr><td>Total HTVA</td>';
            $r.=td(hb($tot_amount ),'class="num"');
            foreach ($tva as $i => $value) {
                $oTva->set_parameter('id', $i);
                $oTva->load();

                $r.='<tr><td>  TVA ' . $oTva->get_parameter('label').'</td>';
                $r.=td(hb(nbm($tva[$i])),'class="num"');
            }
            $r.='<tr>'.td(_('Total TVA')).td(hb($tot_tva),'class="num"');
            $r.='<tr>'.td(_('Total TVAC')).td(hb($tot),'class="num"');
            $r.='</table>';
        } else {
            $r.='<br>Total '.hb($tot);
        }
        $r.='</div>';
        /*  Add hidden */
        $r.=HtmlInput::hidden('e_client',$e_client);
        $r.=HtmlInput::hidden('nb_item',$nb_item);
        $r.=HtmlInput::hidden('p_jrn',$p_jrn);
        if ( isset($period))
            $r.=HtmlInput::hidden('period',$period);
        $r.=HtmlInput::hidden('e_comm',$e_comm);
        $r.=HtmlInput::hidden('e_date',$e_date);
        $r.=HtmlInput::hidden('e_ech',$e_ech);
        $r.=HtmlInput::hidden('jrn_type',$jrn_type);
        $r.=HtmlInput::hidden('e_pj',$e_pj);
        $r.=HtmlInput::hidden('e_pj_suggest',$e_pj_suggest);
        $mt=microtime(true);
        $r.=HtmlInput::hidden('mt',$mt);

        $e_mp=(isset($e_mp))?$e_mp:0;
        $r.=HtmlInput::hidden('e_mp',$e_mp);
        /* Paid by */
        /* if the paymethod is not 0 and if a quick code is given */


        for ($i=0;$i < $nb_item;$i++)
        {
            $r.=HtmlInput::hidden("e_march".$i,${"e_march".$i});
            if (isset (${"e_march".$i."_label"})) $r.=HtmlInput::hidden("e_march".$i."_label",${"e_march".$i."_label"});
            $r.=HtmlInput::hidden("e_march".$i."_price",${"e_march".$i."_price"});
            if ( $g_parameter->MY_TVA_USE=='Y' )
            {
                $r.=HtmlInput::hidden("e_march".$i."_tva_id",${"e_march".$i."_tva_id"});
                $r.=HtmlInput::hidden('e_march'.$i.'_tva_amount', ${'e_march'.$i.'_tva_amount'});
            }
            $r.=HtmlInput::hidden("e_quant".$i,${"e_quant".$i});

        }

        /**
         * 
         */
        if ( $e_mp!=0 && strlen (trim (${'e_mp_qcode_'.$e_mp})) != 0 )
        {
            $r.=HtmlInput::hidden('e_mp_qcode_'.$e_mp,${'e_mp_qcode_'.$e_mp});
            $r.=HtmlInput::hidden('acompte',$acompte);
			$r.=HtmlInput::hidden('e_comm_paiement',$e_comm_paiement);
            /* needed for generating a invoice */
           $r.=HtmlInput::hidden('qcode_benef', ${'e_mp_qcode_' . $e_mp});
			$fname = new Fiche($this->db);
			$fname->get_by_qcode(${'e_mp_qcode_' . $e_mp});
            $r.='<h2>' . _("Payé par")." " . ${'e_mp_qcode_' . $e_mp} .
                    " " . $fname->getName() . '</h2> ' . '<p class="decale">' . _('Déduction acompte ') . h($acompte) . '</p>' .
                    _('Libellé :') . h($e_comm_paiement) ;
            $r.='<br>';
            $r.='<br>';
        }
        // check for upload piece

        return $r;
    }

    /*!\brief the function extra info allows to
     * - add a attachment
     * - generate an invoice
     * - insert extra info
     *\return html string
     */
    public function extra_info()
    {
        $r="";
        $r = '<div id="facturation_div_id" style="height:185px;height:10rem">';
        $r.='<p class="decale">';
        // check for upload piece
        $file=new IFile();
        $file->table=0;
        $r.=_("Ajoutez une pièce justificative ");
        $r.=$file->input("pj","");

        if ( $this->db->count_sql("select md_id,md_name from document_modele where md_affect='ACH'") > 0 )
        {

            $r.=_('ou générer un document').' <input type="checkbox" name="gen_invoice" >';
            // We propose to generate  the fee note
            $doc_gen=new ISelect();
            $doc_gen->name="gen_doc";
            $doc_gen->value=$this->db->make_array(
                                "select md_id,md_name ".
                                " from document_modele where md_affect='ACH'");
            $r.=$doc_gen->input().'<br>';
        }
        $r.='<br>';
        $obj=new IText();
        $r.=_('Numero de bon de commande : ').$obj->input('bon_comm').'<br>';
        $r.=_('Autre information : ').$obj->input('other_info').'<br>';
        $r.='</p>';
        $r.='</div>';
        return $r;
    }


    /**
     * @brief update the payment
     * @todo to remove, obsolete
     * @deprecated
     */
    function show_unpaid_deprecated()
    {
        // Show list of unpaid sell
        // Date - date of payment - Customer - amount
        // Nav. bar
        $step=$_SESSION['g_pagesize'];
        $page=(isset($_GET['offset']))?$_GET['page']:1;
        $offset=(isset($_GET['offset']))?$_GET['offset']:0;


        $sql=SQL_LIST_UNPAID_INVOICE_DATE_LIMIT." and jr_def_id=".$this->id ;
        list($max_line,$list)=$this->list_operation($sql,null,$offset,1);
        $sql=SQL_LIST_UNPAID_INVOICE." and jr_def_id=".$this->id ;
        list($max_line2,$list2)=$this->list_operation($sql,null,$offset,1);

        // Get the max line
        $m=($max_line2>$max_line)?$max_line2:$max_line;
        $bar2=navigation_bar($offset,$m,$step,$page);

        echo $bar2;
        echo '<h2 class="info"> '._('Echeance dépassée').' </h2>';
        echo $list;
        echo  '<h2 class="info"> '._('Non Payée').' </h2>';
        echo $list2;
        echo $bar2;
        // Add hidden parameter
        $hid=new IHidden();

        echo '<hr>';

        if ( $m != 0 )
            echo HtmlInput::submit('paid',_('Mise à jour paiement'));


    }
    /**
     * Retrieve data from the view v_detail_purchase
     * @global  $g_user connected user
     * @param $p_from jrn.jr_tech_per from 
     * @param type $p_end jrn.jr_tech_per to
     * @return type
     */
    function get_detail_purchase($p_from,$p_end)
    {
        global $g_user;
        // Journal valide
        if ( $this->id == 0 ) die (__FILE__.":".__LINE__." Journal invalide");
        
        // Securite
        if ( $g_user->get_ledger_access($this->id) == 'X' ) return null;
        
        // get the data from the view
        $sql = "select * 
                from v_detail_purchase
                 where 
                jr_def_id = $1 
                and  jr_date >= (select p_start from parm_periode where p_id = $2) 
		and  jr_date <= (select p_end from parm_periode where p_id  = $3) "
                .' order by jr_date,substring(jr_pj_number,\'[0-9]+$\')::numeric asc ';
        $ret = $this->db->exec_sql($sql, array($this->id,$p_from, $p_end));
        return $ret;
    }
    /**
     * @brief compute an array with the heading cells for the
     * details, used for the export in CSV
     * @return array
     */
    static function heading_detail_purchase()
    {
        $array['jr_id'] = _('Numéro opération');
        $array['jr_date'] = _('Date');
        $array['jr_date_paid'] = _('Date paiement');
        $array['jr_ech'] = _('Date échéance');
        $array['jr_tech_per'] = _('Période');
        $array['jr_comment'] = _('Libellé');
        $array['jr_pj_number'] = _('Pièce');
        $array['jr_internal'] = _('Interne');
        $array['jr_def_id'] = _('Code journal');
        $array['j_poste'] = _('Poste');
        $array['j_text'] = _('Commentaire');
        $array['j_qcode'] = _('Code Item');
        $array['item_card'] = _('N° fiche');
        $array['item_name'] = _('Nom fiche');
        $array['qp_supplier'] = _('N° fiche fournisseur');
        $array['tiers_name'] = _('Nom fournisseur');
        $array['quick_code'] = _('Code fournisseur');
        $array['tva_label'] = _('Nom TVA');
        $array['tva_comment'] = _('Commentaire TVA');
        $array['tva_both_side'] = _('TVA annulée');
        $array['vat_sided'] = _('TVA Non Payé');
        $array['vat_code'] = _('Code TVA');
        $array['vat'] = _('Montant TVA');
        $array['price'] = _('Total HTVA');
        $array['quantity'] = _('quantité');
        $array['price_per_unit'] = _('PU');
        $array['non_ded_amount'] = _('Montant ND');
        $array['non_ded_tva'] = _('Montant TVA ND');
        $array['non_ded_tva_recup'] = _('TVA récup.');
        $array['htva'] = _('HTVA Opération');
        $array['tot_vat'] = _('TVA Opération');
        $array['tot_tva_np'] = _('TVA NP opération');
        return $array;
    }

}





