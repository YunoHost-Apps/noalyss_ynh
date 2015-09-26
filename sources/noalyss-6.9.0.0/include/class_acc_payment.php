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
 * \brief Handle the table mod_payment
 */
require_once NOALYSS_INCLUDE.'/class_iselect.php';
require_once NOALYSS_INCLUDE.'/class_icard.php';
require_once NOALYSS_INCLUDE.'/class_ispan.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';
require_once NOALYSS_INCLUDE.'/class_fiche.php';
require_once NOALYSS_INCLUDE.'/class_fiche_def.php';
require_once NOALYSS_INCLUDE.'/constant.php';
/*!\brief Handle the table mod_payment
 *\note the private data member are accessed via
  - mp_id  ==> id ( Primary key )
  - mp_lib ==> lib (label)
  - mp_jrn_def_id ==> ledger (Number of the ledger where to save)
  - mp_fd_id ==> fiche_def (fiche class to use)
  - mp_qcode ==> qcode (quick_code of the card)
 *
 */
class Acc_Payment
{

    private static $variable=array("id"=>"mp_id",
                                   "lib"=>"mp_lib",
                                   "qcode"=>"mp_qcode",
                                   "ledger_target"=>"mp_jrn_def_id",
                                   "ledger_source"=>"jrn_def_id",
                                   "fiche_def"=>"mp_fd_id");


    private  $mp_lib;
    private  $mp_qcode;
    private  $mp_jrn_def_if;
    private  $jrn_def_id;
    private  $mp_fd_id;

    function __construct ($p_cn,$p_init=0)
    {
        $this->cn=$p_cn;
        $this->mp_id=$p_init;
    }
    public function get_parameter($p_string)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            return $this->$idx;
        }
        else
		{
			throw new Exception("Attribut inexistant $p_string");
		}
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
        $sql='INSERT INTO mod_payment(
             mp_lib, mp_jrn_def_id, mp_fd_id, mp_qcode,jrn_def_id)
             VALUES ($1, $2, $3, upper($4),$5) returning mp_id';
        $this->mp_id=$this->cn->exec_sql($sql,array(
                                             $this->mp_lib,
                                             $this->mp_jrn_def_id,
                                             $this->mp_fd_id,
                                             $this->mp_qcode,
                                             $this->jrn_def_id));
    }

    public function update()
    {
        if ( $this->verify() != 0 ) return;

        $sql="update mod_payment set mp_lib=$1,mp_qcode=$2,mp_jrn_def_id=$3,mp_fd_id=$4,jrn_def_id=$5 ".
             " where mp_id = $6";
        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->mp_lib,
                       $this->mp_qcode,
                       $this->mp_jrn_def_id,
                       $this->mp_fd_id,
                       $this->jrn_def_id,
                       $this->mp_id)
             );
        if ( strlen (trim($this->mp_jrn_def_id))==0)
            $this->cn->exec_sql(
                'update mod_payment '.
                'set mp_jrn_def_id = null where mp_id=$1',
                array($this->mp_id));
        if ( strlen (trim($this->jrn_def_id))==0)
            $this->cn->exec_sql(
                'update mod_payment '.
                'set mp_jrn_def_id = null where mp_id=$1',
                array($this->mp_id));
        if ( strlen (trim($this->mp_qcode))==0)
            $this->cn->exec_sql(
                'update mod_payment '.
                'set mp_qcode = null where mp_id=$1',
                array($this->mp_id));
        if ( strlen (trim($this->mp_fd_id))==0)
            $this->cn->exec_sql(
                'update mod_payment '.
                'set mp_fd_id = null where mp_id=$1',
                array($this->mp_id));

    }

    public function load()
    {
        $sql='select mp_id,mp_lib,mp_fd_id,mp_jrn_def_id,mp_qcode,jrn_def_id from mod_payment '.
             ' where mp_id = $1';
        $res=$this->cn->exec_sql(
                 $sql,
                 array($this->mp_id)
             );

        if ( Database::num_row($res) == 0 ) return;
        $row=Database::fetch_array($res,0);
        foreach ($row as $idx=>$value)
        {
            $this->$idx=$value;
        }

    }
    /**
     *@brief remove a middle of payment
     */
    public function delete()
    {
        $sql="delete from mod_payment where mp_id=$1";
        $this->cn->exec_sql($sql,array($this->mp_id));
    }
    /*!\brief retrieve all the data for all ledgers
     *\param non
     *\return an array of row
     */
    public function get_all()
    {
        $sql='select mp_id,mp_lib '.
             ' from mod_payment order by mp_lib';
        $array=$this->cn->get_array($sql);
        $ret=array();
        if ( !empty($array) )
        {
            foreach ($array as $row)
            {
                $t=new Acc_Payment($this->cn,$row['mp_id']);
                $t->load();
                $ret[]=$t;
            }
        }
        return $ret;
    }
    /*!\brief retrieve all the data for a ledger but filter on the
     *valid record (jrn and fd not null
     *\param non
     *\return an array of row
     */
    public function get_valide()
    {
        $sql='select mp_id '.
             ' from mod_payment '.
             ' where jrn_def_id=$1 and mp_jrn_def_id is not null and '.
             ' (mp_fd_id is not null or mp_qcode is not null)';
        $array=$this->cn->get_array($sql,array($this->jrn_def_id));
        $ret=array();
        if ( !empty($array) )
        {
            foreach ($array as $row)
            {
                $t=new Acc_Payment($this->cn,$row['mp_id']);
                $t->load();
                $ret[]=$t;
            }
        }
        return $ret;
    }
    /*!\brief return a string with a form (into a table)
     *\param none
     *\return a html string
     */
    public function form()
    {
	//label
        $lib=new IText('mp_lib');
        $lib->value=$this->mp_lib;
		$f_lib=$lib->input();


        $ledger_source=new ISelect('jrn_def_id');
        $ledger_source->value=$this->cn->make_array("select jrn_def_id,jrn_Def_name from
                              jrn_def where jrn_def_type  in ('ACH','VEN') order by jrn_def_name");
		$ledger_source->selected=$this->jrn_def_id;
        $f_source=$ledger_source->input();

        // type of card
        $tcard=new ISelect('mp_fd_id');
        $tcard->value=$this->cn->make_array('select fd_id,fd_label from fiche_def join fiche_def_ref '.
                                            ' using (frd_id) where frd_id in (25,4) order by fd_label');
		$tcard->selected=$this->mp_fd_id;

        $f_type_fiche=$tcard->input();
        $ledger_record=new ISelect('mp_jrn_def_id');
        $ledger_record->value=$this->cn->make_array("select jrn_def_id,jrn_Def_name from
                              jrn_def where jrn_def_type  in ('ODS','FIN')");
		$ledger_record->selected=$this->mp_jrn_def_id;
        $f_ledger_record=$ledger_record->input();

        // the card
        $qcode=new ICard();
        $qcode->noadd=true;
        $qcode->name='mp_qcode';
        $list=$this->cn->make_list('select fd_id from fiche_def where frd_id in (25,4)');
        $qcode->typecard=$list;
		$qcode->dblclick='fill_ipopcard(this);';
		$qcode->value=$this->mp_qcode;

        $f_qcode=$qcode->input();

		$msg="Modification de ".$this->mp_lib;
        ob_start();
        require_once NOALYSS_INCLUDE.'/template/new_mod_payment.php';
        $r=ob_get_contents();
        ob_end_clean();
        return $r;

    }
    /*!\brief show several lines with radio button to select the payment
     *method we want to use, the $_POST['e_mp'] will be set
     *\param none
     *\return html string
     */
    public function select()
    {
        $r='';
        $array=$this->get_valide();
        $r.=HtmlInput::hidden('gDossier',dossier::id());

        if ( empty($array)==false ) {
            $acompte=new INum('acompte');
            $acompte->value=0;
            $r.=_(" Acompte à déduire");
            $r.=$acompte->input();
			$r.='<p>';
			$e_comm_paiement=new IText('e_comm_paiement');
			$e_comm_paiement->table = 0;
			$e_comm_paiement->setReadOnly(false);
			$e_comm_paiement->size = 60;
			$e_comm_paiement->tabindex = 3;
			$r.=_(" Libellé du paiement");
			$r.=$e_comm_paiement->input();
			$r.='</p>';
		}

        $r.='<ol>';
        $r.='<li ><input type="radio" name="e_mp" value="0" checked>'._('Paiement encodé plus tard');
        if ( empty($array ) == false )
        {
            foreach ($array as $row)
            {
                $f='';
                /* if the qcode is  null the propose a search button to select
                   the card */
                if ( $row->mp_qcode==NULL)
                {
                    $a=new ICard();
                    $a->jrn=$row->mp_jrn_def_id;
					$a->set_attribute('typecard',$row->mp_fd_id);
                    $a->name='e_mp_qcode_'.$row->mp_id;
                    $a->set_dblclick("fill_ipopcard(this);");
                    $a->set_callback('filter_card');
                    $a->set_function('fill_data');
                    $a->set_attribute('ipopup','ipopcard');
                    $a->set_attribute('label',$a->name.'_label');

                    $s=new ISpan();
                    $s->name=$a->name.'_label';
                    $f=_(" paiement par ").$a->input().$s->input();
                }
                else
                {
                    /* if the qcode is not null then add a hidden variable with
                       the qcode */

                    $fiche=new Fiche($this->cn);
                    $fiche->get_by_qcode($row->mp_qcode);
                    $f=HtmlInput::hidden('e_mp_qcode_'.$row->mp_id,$row->mp_qcode);

                    //	  $f.=$fiche->strAttribut(ATTR_DEF_NAME);
                }
                $r.='<li><input type="radio" name="e_mp" value="'.$row->mp_id.'">';
                $r.=$row->mp_lib.'  '.$f;

            }
        }
        $r.='</ol>';
        return $r;
    }

    /*!\brief convert an array into an Acc_Payment object
     *\param array to convert
     */
    public function from_array($p_array)
    {
        $idx=array('mp_id','mp_lib','mp_fd_id','mp_jrn_def_id','mp_qcode','jrn_def_id');
        foreach ($idx as $l)
        if (isset($p_array[$l])) $this->$l=$p_array[$l];
    }
    /**
     *@brief return an html with a form to add a new middle of payment
     */
    public function blank()
    {
        //label
        $lib=new IText('mp_lib');
        $f_lib=$lib->input();

        $ledger_source=new ISelect('jrn_def_id');
        $ledger_source->value=$this->cn->make_array("select jrn_def_id,jrn_Def_name from
                              jrn_def where jrn_def_type  in ('ACH','VEN') order by jrn_def_name");
        $f_source=$ledger_source->input();

        // type of card
        $tcard=new ISelect('mp_fd_id');
        $tcard->value=$this->cn->make_array('select fd_id,fd_label from fiche_def join fiche_def_ref '.
                                            ' using (frd_id) where frd_id in (25,4) order by fd_label');
        $f_type_fiche=$tcard->input();
        $ledger_record=new ISelect('mp_jrn_def_id');
        $ledger_record->value=$this->cn->make_array("select jrn_def_id,jrn_Def_name from
                              jrn_def where jrn_def_type  in ('ODS','FIN')");
        $f_ledger_record=$ledger_record->input();

        // the card
        $qcode=new ICard();
        $qcode->noadd=true;
        $qcode->name='mp_qcode';
        $list=$this->cn->make_list('select fd_id from fiche_def where frd_id in (25,4)');
        $qcode->typecard=$list;
		$qcode->dblclick='fill_ipopcard(this);';

        $f_qcode=$qcode->input();
		$msg="Ajout d'un nouveau moyen de paiement";
        ob_start();
        require_once NOALYSS_INCLUDE.'/template/new_mod_payment.php';
        $r=ob_get_contents();
        ob_end_clean();
        return $r;
    }
    /*!\brief test function
     */
    static function test_me()
    {

    }

}


