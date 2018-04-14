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
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
class Acc_Ledger_Search
{

    protected $cn; //!< Database Connection
    private $type; //!< type of ledger : FIN,ODS,VEN,ACH
    private $all; //!< Flag to indicate if all ledgers must searched (1 for yes)
    private $div; //! prefix for id of DOM id

    /**
     * @brief return a HTML string with the form for the search
     * @param  $p_type if the type of ledger possible values=ALL,VEN,ACH,ODS,FIN
     * @param  $all_type_ledger
     *       values :
     *         - 1 means all the ledger of this type
     *         - 0 No have the "Tous les journaux" availables
     * @param  $div is the div (for reconciliation)
     * @param type $p_type
     * @param type $p_all
     * @param type $p_div
     */

    function __construct($p_type, $p_all=1, $p_div="")
    {
        $this->cn=Dossier::connect();
        $this->type=$p_type;
        $this->all=$p_all;
        $this->div=$p_div;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function get_all()
    {
        return $this->all;
    }

    public function get_div()
    {
        return $this->div;
    }

    public function set_type($type)
    {
        $this->type=$type;
    }

    public function set_all($all)
    {
        $this->all=$all;
    }

    public function set_div($div)
    {
        $this->div=$div;
    }

    /**
     * @brief return a HTML string with the form for the search
     * @return a HTML String without the tag FORM or DIV
     *
     * @see build_search_sql
     * @see display_search_form
     * @see list_operation
     * @example search_acc_operation.php
     */
    function search_form()
    {
        global $g_user;
        $r="";
        $bledger_param=json_encode(array(
            'dossier'=>Dossier::id(),
            'type'=>$this->type,
            'all_type'=>$this->all,
            'div'=>$this->div
        ));

        $bledger_param=str_replace('"', "'", $bledger_param);
        $bledger=new ISmallButton('l');
        $bledger->label=_("choix des journaux");
        $bledger->javascript=" show_ledger_choice($bledger_param)";
        $f_ledger=$bledger->input();
        $hid_jrn="";
        if (isset($_REQUEST[$this->div.'nb_jrn']))
        {
            for ($i=0; $i<$_REQUEST[$this->div.'nb_jrn']; $i++)
            {
                if (isset($_REQUEST[$this->div."r_jrn"][$i]))
                    $hid_jrn.=HtmlInput::hidden($this->div.'r_jrn['.$i.']',
                                    $_REQUEST[$this->div."r_jrn"][$i]);
            }
            $hid_jrn.=HtmlInput::hidden($this->div.'nb_jrn',
                            $_REQUEST[$this->div.'nb_jrn']);
        } else
        {
            $hid_jrn=HtmlInput::hidden($this->div.'nb_jrn', 0);
        }
        /* Compute default date for exercice */
        $period=$g_user->get_periode();
        $per=new Periode($this->cn, $period);
        $exercice=$per->get_exercice();
        list($per_start, $per_end)=$per->get_limit($exercice);
        $date_end=$per_end->last_day();
        $date_start=$per_start->first_day();
        
        $date_start_hidden=HtmlInput::hidden("{$this->div}date_start_hidden", $date_start);
        $date_end_hidden=HtmlInput::hidden("{$this->div}date_end_hidden", $date_end);
        /* widget for date_start */
        $f_date_start=new IDate('date_start', '', $this->div."date_start");
        /* all periode or only the selected one */
        if (isset($_REQUEST['date_start']))
        {
            $f_date_start->value=$_REQUEST['date_start'];
        }
        else
        {
            $f_date_start->value=$date_start;
        }

        /* widget for date_end */
        $f_date_end=new IDate('date_end', '', $this->div."date_end");
        /* all date or only the selected one */
        if (isset($_REQUEST['date_end']))
        {
            $f_date_end->value=$_REQUEST['date_end'];
        }
        else
        {
            $f_date_end->value=$date_end;
        }
        /* widget for date term */
        $f_date_paid_start=new IDate('date_paid_start', '',
                $this->div."date_paid_start");
        $f_date_paid_end=new IDate('date_paid_end', '',
                $this->div."date_paid_end");

        $f_date_paid_start->value=(isset($_REQUEST['date_paid_start']))?$_REQUEST['date_paid_start']:'';
        $f_date_paid_end->value=(isset($_REQUEST['date_paid_end']))?$_REQUEST['date_paid_end']:'';

        /* widget for desc */
        $f_descript=new IText('desc', "", $this->div."desc");
        $f_descript->size=40;
        if (isset($_REQUEST['desc']))
        {
            $f_descript->value=$_REQUEST['desc'];
        }

        /* widget for amount */
        $f_amount_min=new INum('amount_min', '0', $this->div."amount_min");
        $f_amount_min->value=(isset($_REQUEST['amount_min']))?abs($_REQUEST['amount_min']):0;
        $f_amount_max=new INum('amount_max', '0', $this->div."amount_max");
        $f_amount_max->value=(isset($_REQUEST['amount_max']))?abs($_REQUEST['amount_max']):0;

        /* input quick code */
        $f_qcode=new ICard($this->div.'qcode');

        $f_qcode->set_attribute('typecard', 'all');
        /*        $f_qcode->set_attribute('p_jrn','0');

          $f_qcode->set_callback('filter_card');
         */
        $f_qcode->set_dblclick("fill_ipopcard(this);");
        // Add the callback function to filter the card on the jrn
        //$f_qcode->set_callback('filter_card');
        $f_qcode->set_function('fill_data');
        $f_qcode->javascript=sprintf(' onchange="fill_data_onchange(%s);" ',
                $f_qcode->name);
        $f_qcode->value=(isset($_REQUEST[$this->div.'qcode']))?$_REQUEST[$this->div.'qcode']:'';

        /*        $f_txt_qcode=new IText('qcode');
          $f_txt_qcode->value=(isset($_REQUEST['qcode']))?$_REQUEST['qcode']:'';
         */

        /* input poste comptable */
        $f_accounting=new IPoste('accounting', "", $this->div."accounting");
        $f_accounting->value=(isset($_REQUEST['accounting']))?$_REQUEST['accounting']:'';
        /*
         * utile ??? Filtre les postes comptables en fonction du journal 
         * if ($this->id==-1)
          $jrn=0;
          else
          $jrn=$this->id; */
        $f_accounting->set_attribute('jrn', 0);
        $f_accounting->set_attribute('ipopup', 'ipop_account');
        $f_accounting->set_attribute('label', 'ld');
        $f_accounting->set_attribute('account', $this->div.'accounting');
        $info=Icon_Action::infobulle(13);

        $f_paid=new ICheckbox('unpaid', null, $this->div.'unpaid');
        $f_paid->selected=(isset($_REQUEST['unpaid']))?true:false;

        $r.=dossier::hidden();
        $r.=HtmlInput::hidden('ledger_type', $this->type,
                        $this->div."ledger_type");
        $r.=HtmlInput::hidden('ac', $_REQUEST['ac']);
        ob_start();
        $search_filter=$this->build_search_filter();
        require_once NOALYSS_TEMPLATE.'/ledger_search.php';
        $r.=ob_get_contents();
        ob_end_clean();
        return $r;
    }

    /**
     * Build the button for managing the filter for search
     * @param type $p_div id prefix of the div, button, table ..
     * @param  $this->type if the type of ledger possible values=ALL,VEN,ACH,ODS,FIN
     * @param  $all_type_ledger
     *       values :
     *         - 1 means all the ledger of this type
     *         - 0 No have the "Tous les journaux" availables
     */
    function build_search_filter()
    {
        $json=json_encode(["div"=>$this->div, "ledger_type"=>$this->type, "all_type"=>$this->all,
            "dossier"=>Dossier::id()]);
        $json=str_replace('"', "'", $json);
        $r=sprintf('manage_search_filter(%s)', $json);
        return $r;
    }

    /**
     * Build the button for saving the filter for search
     */
    function build_name_filter()
    {
        $name=new IText($this->div."filter_new");
        $name->placeholder=_("Nom du filtre");
        $r=$name->input();
        $bt=new IButton($this->div."save_ok",_("Ajout"));
        $bt->javascript=sprintf("save_filter('%s','%s')",$this->div,Dossier::id());
        $r.=$bt->input();
        return $r;
    }

    /**
     * @brief this function will create a sql stmt to use to create the list for
     * the ledger,
     * @param  $p_array is usually the $_GET,
     * @param  $p_order the order of the row
     * @param  $p_where is the sql condition if not null then the $p_array will not be used
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
    public function build_search_sql($p_array, $p_order="", $p_where="")
    {
        $sql="select jr_id	,
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
            extract($p_array, EXTR_SKIP);

        $op=$this->div;
        if (isset($p_array[$op."r_jrn"]))
        {
            $r_jrn=$p_array[$op."r_jrn"];
        }
        else
        {
            $r_jrn=(isset($r_jrn))?$r_jrn:-1;
        }

        /* if no variable are set then give them a default
         * value */
        if ($p_array==null||empty($p_array)||!isset($amount_min))
        {
            $amount_min=0;
            $amount_max=0;

            $desc='';
            $qcode=(isset($p_array[$this->div."qcode"]))?$p_array[$this->div."qcode"]:"";
            $accounting=(isset($accounting))?$accounting:"";
            $periode=new Periode($this->cn);
            $g_user=new User($this->cn);
            $p_id=$g_user->get_periode();
            if ($p_id!=null)
            {
                list($date_start, $date_end)=$periode->get_date_limit($p_id);
            }
        }

        /* if p_jrn : 0 if means all ledgers, if -1 means all ledger of this
         *  type otherwise only one ledger */
        $fil_ledger='';
        $fil_amount='';
        $fil_date='';
        $fil_desc='';
        $fil_sec='';
        $fil_qcode='';
        $fil_account='';
        $fil_paid='';
        $fil_date_paid='';

        $and='';
        $g_user=new User($this->cn);
        $p_action=$ledger_type;
        if ($p_action=='')
            $p_action='ALL';
        if ($r_jrn==-1)
        {

            /* from compta.php the p_action is quick_writing instead of ODS  */
            if ($p_action=='quick_writing')
                $p_action='ODS';


            $fil_ledger=$g_user->get_ledger_sql($p_action, 3);
            $and=' and ';
        }
        else
        {

            if ($p_action=='quick_writing')
                $p_action='ODS';

            $aLedger=$g_user->get_ledger($p_action, 3);
            $fil_ledger='';
            $sp='';
            for ($i=0; $i<count($r_jrn); $i++)
            {
                if (isset($r_jrn[$i]))
                {
                    $a=$r_jrn[$i];
                    $fil_ledger.=$sp.$a;
                    $sp=',';
                }
            }
            $fil_ledger=' jrn_def_id in ('.$fil_ledger.')';
            $and=' and ';

            /* no ledger selected */
            if ($sp=='')
            {
                $fil_ledger='';
                $and='';
            }
        }

        /* format the number */
        $amount_min=abs(toNumber($amount_min));
        $amount_max=abs(toNumber($amount_max));
        if ($amount_min>0&&isNumber($amount_min))
        {
            $fil_amount=$and.' jr_montant >='.$amount_min;
            $and=' and ';
        }
        if ($amount_max>0&&isNumber($amount_max))
        {
            $fil_amount.=$and.' jr_montant <='.$amount_max;
            $and=' and ';
        }
        /* -------------------------------------------------------------------------- *
         * if both amount are the same then we need to search into the detail
         * and we reset the fil_amount
         * -------------------------------------------------------------------------- */
        if (isNumber($amount_min)&&
                isNumber($amount_max)&&
                $amount_min>0&&
                bccomp($amount_min, $amount_max, 2)==0)
        {
            $fil_amount=$and.' ( ';

            // Look in detail
            $fil_amount.='jr_grpt_id in ( select distinct j_grpt from jrnx where j_montant = '.$amount_min.') ';

            //and the total operation
            $fil_amount.=' or ';
            $fil_amount.=' jr_montant = '.$amount_min;

            $fil_amount.=')';
            $and=" and ";
        }
        // date
        if (isset($date_start)&&isDate($date_start)!=null)
        {
            $fil_date=$and." jr_date >= to_date('".$date_start."','DD.MM.YYYY')";
            $and=" and ";
        }
        if (isset($date_end)&&isDate($date_end)!=null)
        {
            $fil_date.=$and." jr_date <= to_date('".$date_end."','DD.MM.YYYY')";
            $and=" and ";
        }
        // date paiement
        if (isset($date_paid_start)&&isDate($date_paid_start)!=null)
        {
            $fil_date_paid=$and." jr_date_paid >= to_date('".$date_paid_start."','DD.MM.YYYY')";
            $and=" and ";
        }
        if (isset($date_paid_end)&&isDate($date_paid_end)!=null)
        {
            $fil_date_paid.=$and." jr_date_paid <= to_date('".$date_paid_end."','DD.MM.YYYY')";
            $and=" and ";
        }
        // comment
        if (isset($desc)&&$desc!=null)
        {
            $desc=sql_string($desc);
            $fil_desc=$and." ( upper(jr_comment) like upper('%".$desc."%') or upper(jr_pj_number) like upper('%".$desc."%') ".
                    " or upper(jr_internal)  like upper('%".$desc."%')
                          or jr_grpt_id in (select j_grpt from jrnx where j_text ~* '".$desc."')
                          or jr_id in (select jr_id from jrn_info where ji_value is not null and ji_value ~* '$desc')
                          )";
            $and=" and ";
        }
        //    Poste
        if (isset($accounting)&&$accounting!=null)
        {
            $fil_account=$and."  jr_grpt_id in (select j_grpt
                         from jrnx where j_poste::text like '".sql_string($accounting)."%' )  ";
            $and=" and ";
        }
        // Quick Code
         $qcode=(isset($p_array[$this->div."qcode"]))?$p_array[$this->div."qcode"]:"";
        if ($qcode!="")
        {
            $fil_qcode=$and."  jr_grpt_id in ( select j_grpt from
                       jrnx where trim(j_qcode) = upper(trim('".sql_string($qcode)."')))";
            $and=" and ";
        }

        // Only the unpaid
        if (isset($unpaid))
        {
            $fil_paid=$and.SQL_LIST_UNPAID_INVOICE;
            $and=" and ";
        }
        global $g_user;
        if ($g_user->admin==0&&$g_user->is_local_admin()==0 && $g_user->get_status_security_ledger()==1 )
        {
            
            $fil_sec=$and." jr_def_id in ( select uj_jrn_id ".
                    " from user_sec_jrn where ".
                    " uj_login='".sql_string($_SESSION['g_user'])."'".
                    " and uj_priv in ('R','W'))";
        }
        $where=$fil_ledger.$fil_amount.$fil_date.$fil_desc.$fil_sec.$fil_amount.$fil_qcode.$fil_paid.$fil_account.$fil_date_paid;
        $sql.=" where ".$where;
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
        $r='';
        $r.='<div id="search_form" style="display:none">';
        $r.=HtmlInput::title_box(_('Recherche'), "search_form", "hide", "", "n");
        $r.='<FORM METHOD="GET">';
        $r.=$this->search_form();
        
        $r.=HtmlInput::submit('search', _('Rechercher'));
        
        $button_search=new IButton("{$this->div}button", _('Filtre'));
        $button_search->javascript=$this->build_search_filter();
        $r.=$button_search->input();
        
        

        $r.=HtmlInput::hidden('ac', $_REQUEST['ac']);

        /*  when called from commercial.php some hidden values are needed */
        if (isset($_REQUEST['sa']))
            $r.=HtmlInput::hidden("sa", $_REQUEST['sa']);
        if (isset($_REQUEST['sb']))
            $r.=HtmlInput::hidden("sb", $_REQUEST['sb']);
        if (isset($_REQUEST['sc']))
            $r.=HtmlInput::hidden("sc", $_REQUEST['sc']);
        if (isset($_REQUEST['f_id']))
            $r.=HtmlInput::hidden("f_id", $_REQUEST['f_id']);

        $r.='</FORM>';

        $r.='</div>';
        $button=new IButton('tfs');
        $button->label=_("Chercher");
        $button->javascript="toggleHideShow('search_form','tfs');";
        $r.=$button->input();
        return $r;
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
    public function list_operation($sql, $offset, $p_paid=0)
    {
        global $g_parameter, $g_user;
        bcscale(2);
        $table=new Sort_Table();
        $gDossier=dossier::id();
        $amount_paid=0.0;
        $amount_unpaid=0.0;
        $limit=($_SESSION['g_pagesize']!=-1)?" LIMIT ".$_SESSION['g_pagesize']:"";
        $offset=($_SESSION['g_pagesize']!=-1)?" OFFSET ".Database::escape_string($offset):"";
        $order="  order by jr_date_order asc,jr_internal asc";
        // Sort
        $url="?".CleanUrl();
        $str_dossier=dossier::get();
        $table->add(_("Date"), $url,
                'order by jr_date asc,substring(jr_pj_number,\'[0-9]+$\')::numeric asc',
                'order by  jr_date desc,substring(jr_pj_number,\'[0-9]+$\')::numeric desc',
                "da", "dd");
        $table->add(_('Echeance'), $url, " order by  jr_ech asc",
                " order by  jr_ech desc", 'ea', 'ed');
        $table->add(_('Paiement'), $url, " order by  jr_date_paid asc",
                " order by  jr_date_paid desc", 'eap', 'edp');
        $table->add(_('Pièce'), $url,
                ' order by  substring(jr_pj_number,\'[0-9]+$\')::numeric asc ',
                ' order by  substring(jr_pj_number,\'[0-9]+$\')::numeric desc ',
                "pja", "pjd");
        $table->add(_('Tiers'), $url, " order by  name asc",
                " order by  name desc", 'na', 'nd');
        $table->add(_('Montant'), $url, " order by jr_montant asc",
                " order by jr_montant desc", "ma", "md");
        $table->add(_("Description"), $url, "order by jr_comment asc",
                "order by jr_comment desc", "ca", "cd");

        $ord=(!isset($_GET['ord']))?'da':$_GET['ord'];
        $order=$table->get_sql_order($ord);

        // Count
        $count=$this->cn->count_sql($sql);
        // Add the limit
        $sql.=$order.$limit.$offset;
        // Execute SQL stmt
        $Res=$this->cn->exec_sql($sql);

        //starting from here we can refactor, so that instead of returning the generated HTML,
        //this function returns a tree structure.

        $r="";


        $Max=Database::num_row($Res);

        if ($Max==0)
            return array(0, _("Aucun enregistrement trouvé"));

        $r.='<table class="result">';


        $r.="<tr >";
        $r.="<th>"._("n° interne")."</th>";
        if ($this->type=='ALL')
        {
            $r.=th('Journal');
        }
        $r.='<th>'.$table->get_header(0).'</th>';
        if ($p_paid!=0)
            $r.='<th>'.$table->get_header(1).'</td>';
        if ($p_paid!=0)
            $r.='<th>'.$table->get_header(2).'</th>';
        $r.='<th>'.$table->get_header(3).'</th>';
        $r.='<th>'.$table->get_header(4).'</th>';
        $r.='<th>'.$table->get_header(6).'</th>';
        $r.=th('Notes', ' style="width:15%"');
        $r.='<th>'.$table->get_header(5).'</th>';
        // if $p_paid is not equal to 0 then we have a paid column
        if ($p_paid!=0)
        {
            $r.="<th> "._('Payé')."</th>";
        }
        $r.="<th>"._('Concerne')."</th>";
        $r.="<th>"._('Document')."</th>";
        $r.="</tr>";
        // Total Amount
        $tot=0.0;
        $gDossier=dossier::id();
        for ($i=0; $i<$Max; $i++)
        {


            $row=Database::fetch_array($Res, $i);

            if ($i%2==0)
                $tr='<TR class="odd">';
            else
                $tr='<TR class="even">';
            $r.=$tr;
            //internal code
            // button  modify
            $r.="<TD>";
            // If url contains
            //

            $href=basename($_SERVER['PHP_SELF']);


            $r.=sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:modifyOperation(\'%s\',\'%s\')" >%s </A>',
                    $row['jr_id'], $gDossier, $row['jr_internal']);
            $r.="</TD>";
            if ($this->type=='ALL')
                $r.=td($row['jrn_def_name']);
            // date
            $r.="<TD>";
            $r.=$row['str_jr_date'];
            $r.="</TD>";
            // echeance
            if ($p_paid!=0)
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
            $other=($row['quick_code']!='')?HtmlInput::card_detail($row['quick_code'],h($row['name'].' '.$row['first_name'])):'';
            $r.=td($other);
            // comment
            $r.="<TD>";
            $tmp_jr_comment=h($row['jr_comment']);
            $r.=$tmp_jr_comment;
            $r.="</TD>";
            $r.=td(h($row['n_text']), ' style="font-size:0.87em%"');
            // Amount
            // If the ledger is financial :
            // the credit must be negative and written in red
            $positive=0;

            // Check ledger type :
            if ($row['jrn_def_type']=='FIN')
            {
                $positive=$this->cn->get_value("select qf_amount from quant_fin where jr_id=$1",
                        array($row['jr_id']));
                if ($this->cn->count()!=0)
                    $positive=($positive<0)?1:0;
            }
            $r.="<TD align=\"right\">";
            $t_amount=$row['jr_montant'];
            if ($row['total_invoice']!=null&&$row['total_invoice']!=$row['jr_montant'])
                $t_amount=$row['total_invoice'];
            $tot=($positive!=0)?bcsub($tot, $t_amount):bcadd($tot, $t_amount);
            //STAN $positive always == 0
            if ($row ['jrn_def_type']=='FIN')
            {
                $r.=( $positive!=0 )?"<font color=\"red\">  - ".nbm($t_amount)."</font>":nbm($t_amount);
            }
            else
            {
                $r.=( $t_amount<0 )?"<font color=\"red\">  ".nbm($t_amount)."</font>":nbm($t_amount);
            }
            $r.="</TD>";


            // Show the paid column if p_paid is not null
            if ($p_paid!=0)
            {
                $w=new ICheckBox();
                $w->name="rd_paid".$row['jr_id'];
                $w->selected=($row['jr_rapt']=='paid')?true:false;
                // if p_paid == 2 then readonly
                $w->readonly=( $p_paid==2)?true:false;
                $h=new IHidden();
                $h->name="set_jr_id".$row['jr_id'];
                $r.='<TD>'.$w->input().$h->input().'</TD>';
                if ($row['jr_rapt']=='paid')
                    $amount_paid=bcadd($amount_paid, $t_amount);
                else
                    $amount_unpaid=bcadd($amount_unpaid, $t_amount);
            }

            // Rapprochement
            $rec=new Acc_Reconciliation($this->cn);
            $rec->set_jr_id($row['jr_id']);
            $a=$rec->get();
            $r.="<TD>";
            if ($a!=null)
            {

                foreach ($a as $key=> $element)
                {
                    $operation=new Acc_Operation($this->cn);
                    $operation->jr_id=$element;
                    $l_amount=$this->cn->get_value("select jr_montant from jrn ".
                            " where jr_id=$1", array($element));
                    $r.="<A class=\"detail\" HREF=\"javascript:modifyOperation('".$element."',".$gDossier.")\" > ".$operation->get_internal()."[".nbm($l_amount)."]</A>";
                }//for
            }// if ( $a != null ) {
            $r.="</TD>";

            if ($row['jr_valid']=='f')
            {
                $r.="<TD>"._("Opération annulée")."</TD>";
            }
            else
            {
                
            } // else
            //document
            if ($row['jr_pj_name']!="")
            {
                $r.='<td>'.HtmlInput::show_receipt_document($row['jr_id']).'</td>';
            }
            else
                $r.="<TD></TD>";

            // end row
            $r.="</tr>";
        }
        $amount_paid=round($amount_paid, 4);
        $amount_unpaid=round($amount_unpaid, 4);
        $tot=round($tot, 4);
        $r.="<TR>";
        $r.='<TD COLSPAN="5">Total</TD>';
        $r.='<TD ALIGN="RIGHT">'.nbm($tot)."</TD>";
        $r.="</tr>";
        if ($p_paid!=0)
        {
            $r.="<TR>";
            $r.='<TD COLSPAN="5">'._("Payé").'</TD>';
            $r.='<TD ALIGN="RIGHT">'.nbm($amount_paid)."</TD>";
            $r.="</tr>";
            $r.="<TR>";
            $r.='<TD COLSPAN="5">'._("Non payé").'</TD>';
            $r.='<TD ALIGN="RIGHT">'.nbm($amount_unpaid)."</TD>";
            $r.="</tr>";
        }
        $r.="</table>";

        return array($count, $r);
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
      $max_line=$this->cn->count_sql($sql);

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
    public function list_operation_to_reconcile($sql, $p_target)
    {
        global $g_parameter, $g_user;
        $gDossier=dossier::id();
        $limit=" LIMIT ".MAX_RECONCILE;
        // Sort
        // Count
        $count=$this->cn->count_sql($sql);
        // Add the limit
        $sql.=" order by jr_date asc ".$limit;

        // Execute SQL stmt
        $Res=$this->cn->exec_sql($sql);

        //starting from here we can refactor, so that instead of returning the generated HTML,
        //this function returns a tree structure.

        $r="";


        $Max=Database::num_row($Res);

        if ($Max==0)
            return array(0, _("Aucun enregistrement trouvé"));
        $r.=HtmlInput::hidden("target", $p_target);
        $r.='<table class="result">';


        $r.="<tr >";
        $r.="<th>"._("Selection")."</th>";
        $r.="<th>"._("Internal")."</th>";

        if ($this->type=='ALL')
        {
            $r.=th(_('Journal'));
        }

        $r.='<th>'._("Date").'</th>';
        $r.='<th>'._("Pièce").'</td>';
        $r.=th(_('tiers'));
        $r.='<th>'._("Description").'</th>';
        $r.=th(_('Notes'), ' ');
        $r.='<th>'._("Montant").'</th>';
        $r.="<th>"._('Concerne')."</th>";
        $r.="</tr>";
        // Total Amount
        $tot=0.0;
        $gDossier=dossier::id();
        $str_dossier=Dossier::id();
        for ($i=0; $i<$Max; $i++)
        {


            $row=Database::fetch_array($Res, $i);

            if ($i%2==0)
                $tr='<TR class="odd">';
            else
                $tr='<TR class="even">';
            $r.=$tr;
            // Radiobox
            //

			$r.='<td><INPUT TYPE="CHECKBOX" name="jr_concerned'.$row['jr_id'].'" ID="jr_concerned'.$row['jr_id'].'" value="'.$row['quick_code'].'"> </td>';
            //internal code
            // button  modify
            $r.="<TD>";
            // If url contains
            //

            $href=basename($_SERVER['PHP_SELF']);


            $r.=sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:modifyOperation(\'%s\',\'%s\')" >%s </A>',
                    $row['jr_id'], $gDossier, $row['jr_internal']);
            $r.="</TD>";
            if ($this->type=='ALL')
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
            $other=($row['quick_code']!='')?'['.$row['quick_code'].'] '.$row['name'].' '.$row['first_name']:'';
            $r.=td($other);
            // comment
            $r.="<TD>";
            $tmp_jr_comment=h($row['jr_comment']);
            $r.=$tmp_jr_comment;
            $r.="</TD>";
            $r.=td(h($row['n_text']), ' style="font-size:0.87em"');
            // Amount
            // If the ledger is financial :
            // the credit must be negative and written in red
            $positive=0;

            // Check ledger type :
            if ($row['jrn_def_type']=='FIN')
            {
                $positive=$this->cn->get_value("select qf_amount from quant_fin where jr_id=$1",
                        array($row['jr_id']));
                if ($this->cn->count()!=0)
                    $positive=($positive<0)?1:0;
            }
            $r.="<TD align=\"right\">";

            $r.=( $positive!=0 )?"<font color=\"red\">  - ".nbm($row['jr_montant'])."</font>":nbm($row['jr_montant']);
            $r.="</TD>";



            // Rapprochement
            $rec=new Acc_Reconciliation($this->cn);
            $rec->set_jr_id($row['jr_id']);
            $a=$rec->get();
            $r.="<TD>";
            if ($a!=null)
            {

                foreach ($a as $key=> $element)
                {
                    $operation=new Acc_Operation($this->cn);
                    $operation->jr_id=$element;
                    $l_amount=$this->cn->get_value("select jr_montant from jrn ".
                            " where jr_id=$1", array($element));
                    $r.="<A class=\"detail\" HREF=\"javascript:modifyOperation('".$element."',".$gDossier.")\" > ".$operation->get_internal()."[".nbm($l_amount)."]</A>";
                }//for
            }// if ( $a != null ) {
            $r.="</TD>";

            if ($row['jr_valid']=='f')
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
     * return the html code to create an hidden div and a button
     * to show this DIV. This contains all the available ledgers
     * for the user in READ or RW
     *@param $p_selected is an array of checkbox
     *@param $p_div div suffix for the list of ledgers
     *@note the choosen ledger are stored in the array r_jrn (_GET)
     */
    function select_ledger($p_selected,$p_div)
    {
        global $g_user;
	$r = '';
	/* security : filter ledger on user */
	$p_array = $g_user->get_ledger($this->type, 3,FALSE);
        
        ob_start();
        

        /* create a hidden div for the ledger */
        echo '<div id="div_jrn'.$p_div.'" >';
        echo HtmlInput::title_box(_("Journaux"), $p_div."jrn_search");
        echo '<div style="padding:5px">';
        echo '<form method="GET" id="'.$p_div.'search_frm" onsubmit="return hide_ledger_choice(\''.$p_div.'search_frm\')">';
        echo HtmlInput::hidden('nb_jrn', count($p_array));
        echo _('Filtre ').HtmlInput::filter_table($p_div.'tb_jrn', '0,1,2', 2);
        echo HtmlInput::anchor_action(_('Inverser sel'),' toggle_checkbox(\''."{$p_div}search_frm".'\')','sel_'.$p_div,"nav");
        echo "-";
        echo HtmlInput::anchor_action(_('Effacer sel'),' unselect_checkbox(\''."{$p_div}search_frm".'\')','unsel_'.$p_div,"nav");
        echo "-";
        echo HtmlInput::anchor_action(_('Financier'),'  select_checkbox_attribute(\''."{$p_div}search_frm".'\',\'ledger_type\',\'FIN\') ','selfin_'.$p_div,"nav");
        echo "-";
        echo HtmlInput::anchor_action(_('Vente'),'  select_checkbox_attribute(\''."{$p_div}search_frm".'\',\'ledger_type\',\'VEN\') ','selfven_'.$p_div,"nav");
        echo "-";
        echo HtmlInput::anchor_action(_('Achat'),'  select_checkbox_attribute(\''."{$p_div}search_frm".'\',\'ledger_type\',\'ACH\') ','selfach_'.$p_div,"nav");
        echo "-";
        echo HtmlInput::anchor_action(_('Op.Diverses'),'  select_checkbox_attribute(\''."{$p_div}search_frm".'\',\'ledger_type\',\'ODS\') ','selfods_'.$p_div,"nav");
        echo '<table class="result" id="'.$p_div.'tb_jrn">';
        echo '<tr>';
        echo th(_('Nom'));
        echo th(_('Description'));
        echo th(_('Type'));
        echo '</tr>';
        echo '<tr>';
        echo '<td>';
        
        echo '</td>';
        echo '</tr>';
        for ($e=0;$e<count($p_array);$e++)
        {
            $row=$p_array[$e];
//            if ( $row['jrn_enable']==0) continue;
            $r=new ICheckBox($p_div.'r_jrn'.$e,$row['jrn_def_id']);
            $r->set_attribute("ledger_type", $row['jrn_def_type']);
            $idx=$row['jrn_def_id'];
            if ( $p_selected != null &&  in_array($row['jrn_def_id'],$p_selected))
            {
                $r->selected=true;
            }
            $class=($e%2==0)?' class="even" ':' class="odd" ';
            echo '<tr '.$class.'>';
            echo '<td style="white-space: nowrap">'.$r->input().$row['jrn_def_name'].'</td>';
            echo '<td >'.$row['jrn_def_description'].'</td>';
            echo '<td >'.$row['jrn_def_type'].'</td>';
            echo '</tr>';

        }
        echo '</table>';
        echo HtmlInput::hidden('div',$p_div);
        echo HtmlInput::submit('save',_('Valider'));
        echo HtmlInput::button_close($p_div."jrn_search");
        echo '</form>';
        echo '</div>';
        echo '</div>';
  
        $ret=ob_get_contents();
        ob_end_clean();
        return $ret;
    }

}
