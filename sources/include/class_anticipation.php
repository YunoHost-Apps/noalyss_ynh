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

/**
 * @file
 * @brief  Manage the anticipation, prediction of sales, expense, bank...
 *
 */
/**
 *@brief Manage the anticipation of expense, sales,...
 *@see Forecast Forecast_Cat Forecast_Item
 *
 */
require_once NOALYSS_INCLUDE.'/class_forecast.php';
require_once NOALYSS_INCLUDE.'/class_forecast_cat.php';
require_once NOALYSS_INCLUDE.'/class_forecast_item.php';
require_once NOALYSS_INCLUDE.'/class_fiche.php';
require_once NOALYSS_INCLUDE.'/class_acc_account_ledger.php';
require_once NOALYSS_INCLUDE.'/class_periode.php';
require_once NOALYSS_INCLUDE.'/class_impress.php';

class Anticipation
{
    /* example private $variable=array("val1"=>1,"val2"=>"Seconde valeur","val3"=>0); */
    private static $variable=array ("id"=>"f_id","name"=>"f_name");
    private $cn;
    var $cat; /*!< array of object categorie (forecast_cat)*/
    var $item; /*< array of object item (forecast_item) */
    /**
     * @brief constructor
     * @param $p_init Database object
     */
    function __construct ($p_init,$p_id=0)
    {
        $this->cn=$p_init;
        $this->f_id=$p_id;
    }
    public function get_parameter($p_string)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            return $this->$idx;
        }
        else
            throw new Exception("Attribut inexistant $p_string");
    }
    public function set_parameter($p_string,$p_value)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            $this->$idx=$p_value;
        }
        else
            throw new Exception("Attribut inexistant $p_string");


    }
    public function get_info()
    {
        return var_export(self::$variable,true);
    }
    public function verify()
    {
        // Verify that the elt we want to add is correct
        // the f_name must be unique (case insensitive)
        return 0;
    }
    public function save()
    {
        /* please adapt */
        if (  $this->get_parameter("id") == 0 )
            $this->insert();
        else
            $this->update();
    }

    public function insert()
    {
        if ( $this->verify() != 0 ) return;
    }

    public function update()
{}

    public function load()
    {}
    /**
     *@brief Display the result of the forecast
     *@param $p_periode
     *@return HTML String with the code
     */
    public function display()
    {
		bcscale(4);
        $forecast=new Forecast($this->cn,$this->f_id);
        $forecast->load();
        $str_name=h($forecast->get_parameter('name'));

	$start=$forecast->get_parameter('start_date');
	$end=$forecast->get_parameter('end_date');

	if ( $start=='') throw new Exception (_('Période de début non valable'));
	if ( $end=='') throw new Exception (_('Période de fin non valable'));

	$per=new Periode($this->cn,$start);
	$str_start=format_date($per->first_day());

	$per=new Periode($this->cn,$end);
	$str_end=format_date($per->last_day());


        $r="";
        $aCat=$this->cn->get_array('select fc_id,fc_desc from forecast_cat where f_id=$1 order by fc_order',array($this->f_id));
        $aItem=array();
        $aReal=array();
        $poste=new Acc_Account_Ledger($this->cn,0);
        $fiche=new Fiche($this->cn);
        $aPeriode=$this->cn->get_array("select p_id,to_char(p_start,'MM.YYYY') as myear from parm_periode
	                                 where p_start >= (select p_start from parm_periode where p_id=$start)
                                         and p_end <= (select p_end from parm_periode where p_id=$end)
					 order by p_start;");
	$error=array();
        for($j=0;$j<count($aCat);$j++)
        {
            $aItem[$j]=$this->cn->get_array('select fi_card,fi_account,fi_text,fi_amount,fi_debit from forecast_item where fc_id=$1  and fi_pid=0 order by fi_order ',array($aCat[$j]['fc_id']));
            $aPerMonth[$j]=$this->cn->get_array('select fi_pid,fi_card,fi_account,fi_text,fi_amount,fi_debit from forecast_item where fc_id=$1 and fi_pid !=0 order by fi_order ',array($aCat[$j]['fc_id']));

            /* compute the real amount for periode */
            for($k=0;$k<count($aItem[$j]);$k++)
            {
                /* for each periode */
                for ($l=0;$l<count($aPeriode);$l++)
                {
                    if ($aItem[$j][$k]['fi_account']=='')
                    {
                        $fiche->id=$aItem[$j][$k]['fi_card'];
                        $amount=$fiche->get_solde_detail("j_tech_per = ".$aPeriode[$l]['p_id']);
			if ($aItem[$j][$k]['fi_debit']=='C' && $amount['debit']>$amount['credit'])  $amount['solde']=$amount["solde"]*(-1);
			if ($aItem[$j][$k]['fi_debit']=='D' && $amount['debit']<$amount['credit'])  $amount['solde']=$amount["solde"]*(-1);

                    }
                    else
                    {
                        $poste->id=$aItem[$j][$k]['fi_account'];
			$aresult=Impress::parse_formula($this->cn,"OK",$poste->id,$aPeriode[$l]['p_id'],$aPeriode[$l]['p_id']);
                        $tmp_label=$aresult['desc'];
			$amount['solde']=$aresult['montant'];

			if ( $tmp_label != 'OK') $error[]="<li> ".$aItem[$j][$k]['fi_text'].$poste->id.'</li>';
                    }
                    $aReal[$j][$k][$l]=$amount['solde'];
                }
            }

        }
        ob_start();
        require_once NOALYSS_INCLUDE.'/template/forecast_result.php';
        $r.=ob_get_contents();
        ob_end_clean();
        return $r;
    }
    public static function div()
    {
        $r='<div id="div_anti" style="display:none">';
        $r.= '</div>';
        return $r;
    }
    public function delete()
    {}
    /**
     *@brief Display a form for modifying the name or/and the category of an existing
     * anticipation
     *@return html string with the form
     */
    private function form_cat_mod()
    {
        global $g_user;
        $a=new Forecast($this->cn,$this->f_id);
        $a->load();
        $name=new IText('an_name');
        $name->value=$a->get_parameter("name");
        $str_name=$name->input();
        $str_action=_('Modification');

	$start_date=new IPeriod('start_date');
	$start_date->type=ALL;
	$start_date->cn=$this->cn;
	$start_date->show_end_date=false;
	$start_date->show_start_date=true;
	$start_date->user=$g_user;
	$start_date->filter_year=false;

	$end_date=new IPeriod('end_date');
	$end_date->type=ALL;
	$end_date->cn=$this->cn;
	$end_date->show_end_date=true;
	$end_date->show_start_date=false;
	$end_date->user=$g_user;
	$end_date->filter_year=false;

	$start_date->value=$a->f_start_date;
	$end_date->value=$a->f_end_date;

	$str_start_date=$start_date->input();
	$str_end_date=$end_date->input();


        $r=HtmlInput::hidden('f_id',$this->f_id);
        $array=Forecast_Cat::load_all($this->cn,$this->f_id);

        for ($i=0;$i<MAX_CAT;$i++)
        {
            /* category name */
            $name_name=(isset($array[$i]['fc_id']))?'fr_cat'.$array[$i]['fc_id']:'fr_cat_new'.$i;
            $name=new IText($name_name);
            $name->value=(isset ($array[$i]['fc_desc']))?$array[$i]['fc_desc']:'';
            $aCat[$i]['name']=$name->input();


            /* category order */
            $order_name=(isset($array[$i]['fc_id']))?'fc_order'.$array[$i]['fc_id']:'fc_order_new'.$i;
            $order=new IText($order_name);
            $order->value=(isset($array[$i]['fc_order']))?$array[$i]['fc_order']:$i+1;
            $aCat[$i]['order']=$order->input();
        }

        ob_start();
        require_once NOALYSS_INCLUDE.'/template/forecast_cat.php';
        $r.=ob_get_contents();
        ob_end_clean();
        return $r;
    }
    /**
     *@brief Display a form for adding an new anticipation
     *@return html string with the form
     */
    private function form_cat_new()
    {
     global $g_user;
      $r="";
        $str_action=_('Nouveau');

        $name=new IText('an_name');
        $str_name=$name->input();

	$start_date=new IPeriod('start_date');
	$start_date->type=ALL;
	$start_date->cn=$this->cn;
	$start_date->show_end_date=false;
	$start_date->show_start_date=true;
	$start_date->user=$g_user;
	$start_date->filter_year=false;

	$end_date=new IPeriod('end_date');
	$end_date->type=ALL;
	$end_date->cn=$this->cn;
	$end_date->show_end_date=true;
	$end_date->show_start_date=false;
	$end_date->user=$g_user;
	$end_date->filter_year=false;

	$period=$g_user->get_periode();
	$per=new Periode($this->cn,$period);
	$year=$per->get_exercice();

	list($per_start,$per_end)=$per->get_limit($year);
	$start_date->value=$per_start->p_id;
	$end_date->value=$per_end->p_id;

	$str_start_date=$start_date->input();
	$str_end_date=$end_date->input();

        $aLabel=array(_('Ventes'),_('Dépense'),_('Banque'));
        $aCat=array();

        for ($i=0;$i<MAX_CAT;$i++)
        {
            /* category name */
            $name=new IText('fr_cat'.$i);
            $name->value=(isset($aLabel[$i]))?$aLabel[$i]:'';
            $aCat[$i]['name']=$name->input();


            /* category order */
            $order=new IText('fr_order'.$i);
            $order->value=$i+1;
            $aCat[$i]['order']=$order->input();
        }

        ob_start();
        require_once NOALYSS_INCLUDE.'/template/forecast_cat.php';
        $r.=ob_get_contents();
        ob_end_clean();
        return $r;

    }
    /**
     * @brief create an empty object anticipation
     * @return html string with the form
     */
    public  function form_cat()
    {
        if ($this->f_id != 0)
            return $this->form_cat_mod();
        else
            return $this->form_cat_new();
    }
    /**
     *@brief display a form for modifying or add a forecast
     *@return HTML code
     */
    public function form_item()
    {
        $forecast=new Forecast($this->cn,$this->f_id);
        $forecast->load();
        $str_name=$forecast->get_parameter('name');
        $str_start=$forecast->get_parameter('start_date');
        $str_end=$forecast->get_parameter('end_date');


        $r="";
        $str_action=_("Elements");
        $cat=new Forecast_Cat($this->cn);
        $array=$cat->make_array($this->f_id);
        $periode=new Periode($this->cn);
        $aPeriode=$this->cn->make_array("select p_id,to_char(p_start,'MM.YYYY') as label from parm_periode
                                  where p_start >= (select p_start from parm_periode where p_id=$str_start)
                                   and p_end <= (select p_end from parm_periode where p_id=$str_end)
				   order by p_start");
        $aPeriode[]=array('value'=>0,'label'=>'Mensuel');
        $value=$this->cn->get_array("select fi_id,fi_text,fi_account,fi_card,fc_id,fi_amount,fi_debit,fi_pid ".
                                    " from forecast_item ".
                                    " 	where fc_id in (select fc_id from forecast_cat where f_id = $1)",array($this->f_id));
        $max=(count($value) < MAX_FORECAST_ITEM)?MAX_FORECAST_ITEM:count($value);
        $r.=HtmlInput::hidden('nbrow',$max);

        for ($i=0;$i<$max;$i++)
        {
            if (isset($value[$i]['fi_id']))
            {
                $r.=HtmlInput::hidden('fi_id'.$i,$value[$i]['fi_id']);
            }
            /* category*/
            $category=new ISelect();
            $category->name='an_cat'.$i;
            $category->value=$array;
            $category->selected=(isset($value[$i]["fc_id"]))?$value[$i]["fc_id"]:-1;
            $aCat[$i]['cat']=$category->input();

            /* amount 	 */
            $amount=new INum('an_cat_amount'.$i);
            $amount->value=(isset($value[$i]["fi_amount"]))?$value[$i]["fi_amount"]:0;
            $aCat[$i]['amount']=$amount->input();

            /* Accounting*/
            $account=new IPoste('an_cat_acc'.$i);
            $account->set_attribute('ipopup','ipop_account');
	    //            $account->set_attribute('label','an_label'.$i);
            $account->set_attribute('account','an_cat_acc'.$i);
	    $account->set_attribute('bracket',1);
	    $account->set_attribute('no_overwrite',1);
	    $account->set_attribute('noquery',1);
	    $account->css_size="85%";
            $account->value=(isset($value[$i]["fi_account"]))?$value[$i]["fi_account"]:"";
            $aCat[$i]['account']=$account->input();
            /*Quick Code */
            $qc=new ICard('an_qc'.$i);
            // If double click call the javascript fill_ipopcard
            $qc->set_dblclick("fill_ipopcard(this);");

            // This attribute is mandatory, it is the name of the IPopup
            $qc->set_attribute('ipopup','ipopcard');

            // name of the field to update with the name of the card
            $qc->set_attribute('label','an_label'.$i);

            // Type of card : all
            $qc->set_attribute('typecard','all');
            $qc->set_attribute('jrn',0);
            $qc->extra='all';

            // when value selected in the autcomplete
            $qc->set_function('fill_data');
            if (isset($value[$i]["fi_card"]))
            {
                $f=new Fiche($this->cn,$value[$i]["fi_card"]);
                $qc->value=$f->strAttribut(ATTR_DEF_QUICKCODE);
                ;
            }

            $aCat[$i]['qc']=$qc->search().$qc->input();
            /* Label */
            $label=new IText('an_label'.$i);
            $label->value=(isset($value[$i]["fi_text"]))?$value[$i]["fi_text"]:"";
            $aCat[$i]['name']=$label->input();

            //Deb or Cred
            $deb=new ISelect('an_deb'.$i);
            $deb->selected=(isset($value[$i]["fi_debit"]))?$value[$i]["fi_debit"]:-1;
            $deb->value=array(array('value'=>'D','label'=>_('Débit')),
                              array('value'=>'C','label'=>_('Crédit'))
                             );
            $aCat[$i]['deb']=$deb->input();
            //Periode
            $isPeriode=new ISelect('month'.$i);
            $isPeriode->value=$aPeriode;
            $isPeriode->selected=(isset($value[$i]["fi_pid"]))?$value[$i]["fi_pid"]:0;
            $aCat[$i]['per']=$isPeriode->input();
        }
        $add_row=new IButton('add_row');
        $add_row->label=_('Ajouter une ligne');
        $add_row->javascript='for_add_row(\'fortable\')';
        $f_add_row=$add_row->input();
        ob_start();
        require_once NOALYSS_INCLUDE.'/template/forecast-detail.php';
        $r.=ob_get_contents();
        ob_end_clean();
        return $r;
    }
    /**
     * @brief unit test
     */
    static function test_me()
    {
        $cn=new Database(dossier::id());
        $test=new Anticipation($cn);

    }

}

?>
