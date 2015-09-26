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
include_once("class_fiche_attr.php");
require_once NOALYSS_INCLUDE.'/class_ispan.php';
require_once NOALYSS_INCLUDE.'/class_itva_popup.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_fiche_def.php';
require_once NOALYSS_INCLUDE.'/class_iposte.php';

/*! \file
 * \brief define Class fiche, this class are using
 *        class attribut
 */
/*!
 * \brief define Class fiche and fiche def, those class are using
 *        class attribut. When adding or modifing new card in a IPOPUP
 *        the ipopup for the accounting item is ipop_account
 */

//-----------------------------------------------------
// class fiche
//-----------------------------------------------------
class Fiche
{
    var $cn;           /*! < $cn database connection */
    var $id;           /*! < $id fiche.f_id */
    var $fiche_def;    /*! < $fiche_def fd_id */
    var $attribut;     /*! < $attribut array of attribut object */
    var $fiche_def_ref; /*!< $fiche_def_ref Type */
    var $row;           /*! < All the row from the ledgers */
    var $quick_code;		/*!< quick_code of the card */
    function __construct($p_cn,$p_id=0)
    {
        $this->cn=$p_cn;
        $this->id=$p_id;
        $this->quick_code='';
    }
    /**
     *@brief used with a usort function, to sort an array of Fiche on the name
     */
    static function cmp_name(Fiche $o1,Fiche $o2)
    {
        return strcmp($o1->strAttribut(ATTR_DEF_NAME),$o2->strAttribut(ATTR_DEF_NAME));
    }

  /**
   *@brief get the available bank_account filtered by the security
   *@return array of card
   */
    function get_bk_account()
    {
        global $g_user;
      $sql_ledger=$g_user->get_ledger_sql('FIN',3);
      $avail=$this->cn->get_array("select jrn_def_id,jrn_def_name,"
              . "jrn_def_bank,jrn_def_description from jrn_def where jrn_def_type='FIN' and $sql_ledger
                            order by jrn_def_name");

      if ( count($avail) == 0 )
            return null;

      for ($i=0;$i<count($avail);$i++)
        {
            $t=new Fiche($this->cn,$avail[$i]['jrn_def_bank']);
            $t->ledger_name=$avail[$i]['jrn_def_name'];
            $t->ledger_description=$avail[$i]['jrn_def_description'];
            $t->getAttribut();
            $all[$i]=$t;

        }
        return $all;
    }


    /*!   get_by_qcode($p_qcode)
     * \brief Retrieve a card thx his quick_code
     *        complete the object,, set the id member of the object or set it
     *        to 0 if no card is found
     * \param $p_qcode quick_code (ad_id=23)
     * \param $p_all retrieve all the attribut of the card, possible value
     * are true or false. false retrieves only the f_id. By default true
     * \return 0 success 1 error not found
     */
    function get_by_qcode($p_qcode=null,$p_all=true)
    {
        if ( $p_qcode == null )
            $p_qcode=$this->quick_code;
        $p_qcode=trim($p_qcode);
        $sql="select f_id from fiche_detail
             where ad_id=23 and ad_value=upper($1)";
        $this->id=$this->cn->get_value($sql,array($p_qcode));
        if ( $this->cn->count()==0)
        {
            $this->id=0;
            return 1;
        }


        if ( $p_all )
            $this->getAttribut();
        return 0;
    }
    /**
     *@brief set an attribute by a value, if the attribut array is empty
     * a call to getAttribut is performed
     *@param the AD_ID
     *@param the value
     *@see constant.php table: attr_def
     */
    function setAttribut($p_ad_id,$p_value)
    {
        if ( sizeof($this->attribut)==0 ) $this->getAttribut();
        for ($e=0;$e <sizeof($this->attribut);$e++)
        {
            if ( $this->attribut[$e]->ad_id == $p_ad_id )
            {
                $this->attribut[$e]->av_text=$p_value;
                break;
            }
        }
    }
    /**
     *\brief  get all the attribute of a card, add missing ones
     *         and sort the array ($this-\>attribut) by ad_id
     */
    function getAttribut()
    {
        if ( $this->id == 0)
        {
            return;
        }
        $sql="select *
             from
                   fiche
             natural join fiche_detail
	     join jnt_fic_attr on (jnt_fic_attr.fd_id=fiche.fd_id and fiche_detail.ad_id=jnt_fic_attr.ad_id)
             join attr_def on (attr_def.ad_id=fiche_detail.ad_id) where f_id=".$this->id.
             " order by jnt_order";

        $Ret=$this->cn->exec_sql($sql);
        if ( ($Max=Database::num_row($Ret)) == 0 )
            return ;
        for ($i=0;$i<$Max;$i++)
        {
            $row=Database::fetch_array($Ret,$i);
            $this->fiche_def=$row['fd_id'];
            $t=new Fiche_Attr ($this->cn);
            $t->ad_id=$row['ad_id'];
            $t->ad_text=$row['ad_text'];
            $t->av_text=$row['ad_value'];
            $t->ad_type=$row['ad_type'];
            $t->ad_size=$row['ad_size'];
            $t->ad_extra=$row['ad_extra'];
            $t->jnt_order=$row['jnt_order'];
            $this->attribut[$i]=$t;
        }
        $e=new Fiche_Def($this->cn,$this->fiche_def);
        $e->GetAttribut();

        if ( sizeof($this->attribut) != sizeof($e->attribut ) )
        {

            /*
			 * !! Missing attribute
			 */
            foreach ($e->attribut as $f )
            {
                $flag=0;
                foreach ($this->attribut as $g )
                {
                    if ( $g->ad_id == $f->ad_id )
                        $flag=1;
                }
                if ( $flag == 0 )
                {
                    // there's a missing one, we insert it
                    $t=new Fiche_Attr ($f->ad_id);
                    $t->av_text="";
                    $t->ad_text=$f->ad_text;
                    $t->jnt_order=$f->jnt_order;
                    $t->ad_type=$f->ad_type;
                    $t->ad_size=$f->ad_size;
                    $t->ad_id=$f->ad_id;
                    $t->ad_extra=$f->ad_extra;
                    $this->attribut[$Max]=$t;
                    $Max++;
                } // if flag == 0

            }// foreach


        }//missing attribut
    }
    /**
     * @brief find the card with the p_attribut equal to p_value, it is not case sensitive
     * @param $p_attribut attribute to find see table attr_def
     * @param $p_value value in attr_value.av_text
     * @return return ARRAY OF jft_id,f_id,fd_id,ad_id,av_text
     */
    function seek($p_attribut,$p_value)
    {
        $sql="select jft_id,f_id,fd_id,ad_id,ad_value from fiche join fiche_detail using (f_id)
             where ad_id=$1 and upper(ad_value)=upper($2)";
        $res=$this->cn->get_array($sql,array($p_attribut,$p_value));
        return $res;
    }

    /*!
     * \brief give the size of a card object
     *
     * \return size
     */
    function size()
    {
        if ( isset ($this->ad_id))
            return sizeof($this->ad_id);
        else
            return 0;
    }


    /*!
     **************************************************
     * \brief  Return array of card from the frd family
     *
     * \param $p_frd_id the fiche_def_ref.frd_id
     * \param $p_search p_search is a filter on the name
     * \param $p_sql extra sql condition
     *
     * \return array of fiche object
     */
    function count_by_modele($p_frd_id,$p_search="",$p_sql="")
    {
        $sql="select *
             from
             fiche join fiche_Def using (fd_id)
             where frd_id=".$p_frd_id;
        if ( $p_search != "" )
        {
            $a=sql_string($p_search);
            $sql="select * from vw_fiche_attr where frd_id=".$p_frd_id.
                 " and vw_name ~* '$p_search'";
        }

        $Ret=$this->cn->exec_sql($sql.$p_sql);

        return Database::num_row($Ret) ;
    }
    /*!
     **************************************************
     * \brief  Return array of card from the frd family
     *
     *
     * \param  $p_frd_id the fiche_def_ref.frd_id
     * \param  $p_offset
     * \param  $p_search is an optional filter
     *\param $p_order : possible values are name, f_id
     * \return array of fiche object
     */
    function GetByDef($p_frd_id,$p_offset=-1,$p_search="",$p_order='')
    {
        switch($p_order)
        {
        case 'name' :
                $order=' order by name';
            break;
        case 'f_id':
            $order='order by f_id';
            break;
        default:
            $order='';
        }
        if ( $p_offset == -1 )
        {
            $sql="select *
                 from
                 fiche join fiche_Def using (fd_id) join vw_fiche_name using(f_id)
                 where frd_id=".$p_frd_id." $p_search ".$order;
        }
        else
        {
            $limit=($_SESSION['g_pagesize']!=-1)?"limit ".$_SESSION['g_pagesize']:"";
            $sql="select *
                 from
                 fiche join fiche_Def using (fd_id) join vw_fiche_name using(f_id)
                 where frd_id=".$p_frd_id." $p_search $order  "
                 .$limit." offset ".$p_offset;

        }

        $Ret=$this->cn->exec_sql($sql);
        if ( ($Max=Database::num_row($Ret)) == 0 )
            return ;
        $all[0]=new Fiche($this->cn);

        for ($i=0;$i<$Max;$i++)
        {
            $row=Database::fetch_array($Ret,$i);
            $t=new Fiche($this->cn,$row['f_id']);
            $t->getAttribut();
            $all[$i]=clone $t;

        }
        return $all;
    }
    function ShowTable()
    {
        echo "<TR><TD> ".
        $this->id."</TD>".
        "<TR> <TD>".
        $this->attribut_value."</TD>".
        "<TR> <TD>".
        $this->attribut_def."</TD></TR>";
    }
    /***
     * @brief  return the string of the given attribute
     *        (attr_def.ad_id)
     * @param $p_ad_id the AD_ID from attr_def.ad_id
     * @param $p_return 1 return NOTFOUND otherwise an empty string
     * @see constant.php
     * @return string
     */
    function strAttribut($p_ad_id,$p_return=1)
    {
		$return=($p_return==1)?NOTFOUND:"";
        if ( sizeof ($this->attribut) == 0 )
        {

            if ($this->id==0) {
					return $return;
			}
            // object is not in memory we need to look into the database
            $sql="select ad_value from fiche_detail
                 where f_id= $1  and ad_id= $2 ";
            $Res=$this->cn->exec_sql($sql,array($this->id,$p_ad_id));
            $row=Database::fetch_all($Res);
            // if not found return error
            if ( $row == false )
                return $return;

            return $row[0]['ad_value'];
        }

        foreach ($this->attribut as $e)
        {
            if ( $e->ad_id == $p_ad_id )
                return $e->av_text;
        }
        return $return;
    }
    /**
     * @brief make an array of attributes of the category of card (FICHE_DEF.FD_ID)
     *The array can be used with the function insert, it will return a struct like this :
     * in the first key (av_textX),  X is the ATTR_DEF::AD_ID
    \verbatim
    Example
    Array
    (
      [av_text1] => Nom
      [av_text12] => Personne de contact
      [av_text5] => Poste Comptable
      [av_text13] => numéro de tva
      [av_text14] => Adresse
      [av_text15] => code postal
      [av_text24] => Ville
      [av_text16] => pays
      [av_text17] => téléphone
      [av_text18] => email
      [av_text23] => Quick Code
    )

    \endverbatim
     *\param $pfd_id FICHE_DEF::FD_ID
     *\return an array of attribute
     *\exception Exception if the cat of card doesn't exist, Exception.getCode()=1
     *\see fiche::insert()
     */
    function to_array($pfd_id)
    {
        $sql="select 'av_text'||to_char(ad_id,'9999') as key,".
             " ad_text ".
             " from fiche_def join jnt_fic_attr using (fd_id)".
             " join attr_def using (ad_id) ".
             " where fd_id=$1 order by jnt_order";
        $ret=$this->cn->get_array($sql,array($pfd_id));
        if ( empty($ret)) throw new Exception(_('Cette categorie de card n\'existe pas').' '.$pfd_id,1);
        $array=array();
        foreach($ret as $idx=>$val)
        {
            $a=str_replace(' ','',$val['key']);
            $array[$a]=$val['ad_text'];
        }
        return $array;

    }
    /*!
     * \brief  insert a new record
     *         show a blank card to be filled
     *
     * \param  $p_fiche_def is the fiche_def.fd_id
     *
     * \return HTML Code
     */
    function blank($p_fiche_def)
    {
        // array = array of attribute object sorted on ad_id
        $f=new Fiche_Def($this->cn,$p_fiche_def);
        $f->get();
        $array=$f->getAttribut();
        $r=h2(_('Catégorie').' '.$f->label,"");
        $r.='<table style="width:98%;margin:1%">';
        foreach ($array as $attr)
        {
            $table=0;
            $msg="";$bulle='';
            if ( $attr->ad_id == ATTR_DEF_ACCOUNT)
            {
                $w=new IPoste("av_text".$attr->ad_id);
                $w->set_attribute('ipopup','ipop_account');
                $w->set_attribute('account',"av_text".$attr->ad_id);
				$w->dbl_click_history();
                //  account created automatically
                $sql="select account_auto($p_fiche_def)";
                $ret_sql=$this->cn->exec_sql($sql);
                $a=Database::fetch_array($ret_sql,0);
                $label=new ISpan();
                $label->name="av_text".$attr->ad_id."_label";

                if ( $a['account_auto'] == 't' )
                    $msg.=$label->input()." <span style=\"color:red\">".
						_("Rappel: Poste créé automatiquement à partir de ")
						.$f->class_base." </span> ";
                else
                {
                    // if there is a class base in fiche_def_ref, this account will be the
                    // the default one
                    if ( strlen(trim($f->class_base)) != 0 )
                    {
                        $msg.="<TD>".$label->input()." <span style=\"color:red\">"._("Rappel: Poste par défaut sera ").
                              $f->class_base.
                              " !</span> ";
                        $w->value=$f->class_base;
                    }

                }
                $r.="<TR>".td(_("Poste Comptable"),' class="input_text" ' ).td($w->input().$msg)."</TR>";
                continue;
            }
            elseif ( $attr->ad_id == ATTR_DEF_TVA)
            {
                $w=new ITva_Popup('popup_tva');
                $w->table=1;
            }

            else
            {
	      switch ($attr->ad_type)
                {
                    case 'text':
                            $w = new IText();
                            $w->css_size = "100%";
                            break;
                    case 'numeric':
                            $w = new INum();
                            $w->prec=($attr->ad_extra=="")?2:$attr->ad_extra;
                            $w->size = $attr->ad_size;
                            break;
                    case 'date':
                            $w = new IDate();
                            break;
                    case 'zone':
                            $w = new ITextArea();
                            $w->style=' class="itextarea" style="margin:0px;width:100%"';
                            break;
                    case 'poste':
                            $w = new IPoste("av_text" . $attr->ad_id);
                            $w->set_attribute('ipopup', 'ipop_account');
                            $w->set_attribute('account', "av_text" . $attr->ad_id);
                            $w->table = 1;
                            $bulle = HtmlInput::infobulle(14);
                            break;
                    case 'select':
                            $w = new ISelect("av_text" . $attr->ad_id);
                            $w->value = $this->cn->make_array($attr->ad_extra);
                            $w->style= 'style="width:100%"';
                            break;
                    case 'card':
                            $w = new ICard("av_text" . $attr->ad_id);
                            // filter on frd_id
                            $w->extra = $attr->ad_extra;
                            $w->extra2 = 0;
                            $label = new ISpan();
                            $label->name = "av_text" . $attr->ad_id . "_label";
                            $w->set_attribute('ipopup', 'ipopcard');
                            $w->set_attribute('typecard', $attr->ad_extra);
                            $w->set_attribute('inp', "av_text" . $attr->ad_id);
                            $w->set_attribute('label', "av_text" . $attr->ad_id . "_label");
                            $msg = $w->search();
                            $msg.=$label->input();
                            break;
                }
                $w->table = 0;
            }
            $w->table = $table;
            $w->label = $attr->ad_text;
            $w->name = "av_text" . $attr->ad_id;
            if ($attr->ad_id == 21 || $attr->ad_id==22||$attr->ad_id==20||$attr->ad_id==31)
            {
                    $bulle=HtmlInput::infobulle(21);
            }
            $r.="<TR>" . td(_($w->label)." $bulle", ' class="input_text" ') . td($w->input()." $msg")." </TR>";
        }
        $r.= '</table>';
        return $r;
    }


    /*!
     * \brief  Display object instance, getAttribute
     *        sort the attribute and add missing ones
     * \param $p_readonly true= if can not modify, otherwise false
     *
     *
     * \return string to display or FNT string for fiche non trouvé
     */
    function Display($p_readonly)
    {
        $this->GetAttribut();
        $attr=$this->attribut;
        /* show card type here */
        $type_card=$this->cn->get_value('select fd_label '
                . ' from fiche_def join fiche using (fd_id) where f_id=$1',
                array($this->id));
        $ret="";
        $ret.=h2(_("Catégorie")." ".$type_card, 'style="display:inline"');
        $ret.='<span style="font-weight:bolder;margin-right:5px;float:right">'.
                _('id fiche').':'.$this->id."</span>";
        $ret.="<table style=\"width:98%;margin:1%\">";
        if (empty($attr))
        {
            return 'FNT';
        }

        /* for each attribute */
        foreach ($attr as $r)
        {
            $msg="";
            $bulle="";
            if ($p_readonly)
            {
                $w=new IText();
                $w->table=1;
                $w->readOnly=true;
                $w->css_size="100%";
            }
            if ($p_readonly==false)
            {

                if ($r->ad_id==ATTR_DEF_ACCOUNT)
                {
                    $w=new IPoste("av_text".$r->ad_id);
                    $w->set_attribute('ipopup', 'ipop_account');
                    $w->set_attribute('account', "av_text".$r->ad_id);
                    $w->dbl_click_history();
                    //  account created automatically
                    $w->table=0;
                    $w->value=$r->av_text;
                    //  account created automatically
                    $sql="select account_auto($this->fiche_def)";
                    $ret_sql=$this->cn->exec_sql($sql);
                    $a=Database::fetch_array($ret_sql, 0);
                    $bulle=HtmlInput::infobulle(10);

                    if ($a['account_auto']=='t')
                        $bulle.=HtmlInput::warnbulle(11);
                }
                elseif ($r->ad_id==ATTR_DEF_TVA)
                {
                    $w=new ITva_Popup('popup_tva');
                    $w->table=1;
                    $w->value=$r->av_text;
                }
                else
                {
                    switch ($r->ad_type)
                    {
                        case 'text':
                            $w=new IText('av_text'.$r->ad_id);
                            $w->css_size="100%";
                            $w->value=$r->av_text;
                            break;
                        case 'numeric':
                            $w=new INum('av_text'.$r->ad_id);
                            $w->size=$r->ad_size;
                            $w->prec=($r->ad_extra=="")?2:$r->ad_extra;
                            $w->value=$r->av_text;
                            break;
                        case 'date':
                            $w=new IDate('av_text'.$r->ad_id);
                            $w->value=$r->av_text;
                            break;
                        case 'zone':
                            $w=new ITextArea('av_text'.$r->ad_id);
                            $w->style=' class="itextarea" style="margin:0px;width:100%"';
                            $w->value=$r->av_text;
                            break;
                        case 'poste':
                            $w=new IPoste("av_text".$r->ad_id);
                            $w->set_attribute('ipopup', 'ipop_account');
                            $w->set_attribute('account', "av_text".$r->ad_id);
                            $w->dbl_click_history();
                            $w->width=$r->ad_size;
                            $w->table=0;
                            $bulle=HtmlInput::infobulle(14);
                            $w->value=$r->av_text;
                            break;
                        case 'card':
                            $uniq=rand(0, 1000);
                            $w=new ICard("av_text".$r->ad_id);
                            $w->id="card_".$this->id.$uniq;
                            // filter on ad_extra

                            $filter=$r->ad_extra;
                            $w->width=$r->ad_size;
                            $w->extra=$filter;
                            $w->extra2=0;
                            $label=new ISpan();
                            $label->name="av_text".$uniq.$r->ad_id."_label";
                            $fiche=new Fiche($this->cn);
                            $fiche->get_by_qcode($r->av_text);
                            if ($fiche->id==0)
                            {
                                $label->value=(trim($r->av_text)=='')?"":" "._("Fiche non trouvée")." ";
                                $r->av_text="";
                            }
                            else
                            {
                                $label->value=$fiche->strAttribut(ATTR_DEF_NAME).
                                        " ".
                                        $fiche->strAttribut(ATTR_DEF_FIRST_NAME,0);
                            }
                            $w->set_attribute('ipopup', 'ipopcard');
                            $w->set_attribute('typecard', $filter);
                            $w->set_attribute('inp', "av_text".$r->ad_id);
                            $w->set_attribute('label', $label->name);
                            $w->autocomplete=0;
                            $w->dblclick="fill_ipopcard(this);";
                            $msg=$w->search();
                            $msg.=$label->input();
                            $w->value=$r->av_text;
                            break;
                        case 'select':
                            $w=new ISelect();
                            $w->value=$this->cn->make_array($r->ad_extra);
                            $w->selected=$r->av_text;
                            $w->style=' style="width:100%" ';
                            break;
                        default:
                            var_dump($r);
                            throw new Exception("Type invalide");
                    }
                    $w->table=0;
                }
            }
            else
            {
                switch ($r->ad_type)
                {
                    case 'select':
                        $x=new ISelect();
                        $x->value=$this->cn->make_array($r->ad_extra);
                        $x->selected=$r->av_text;
                        $value=$x->display();
                        $w->value=$value;
                        break;
                    default:
                        $w->value=$r->av_text;
                }
            }

            $w->name="av_text".$r->ad_id;
            $w->readOnly=$p_readonly;

            if ($r->ad_id==21||$r->ad_id==22||$r->ad_id==20||$r->ad_id==31)
            {
                $bulle=HtmlInput::infobulle(21);
            }
            $ret.="<TR>".td(_($r->ad_text).$bulle).td($w->input()." ".$msg)." </TR>";
        }

        $ret.="</table>";

        return $ret;
    }

    /*!
     * \brief  Save a card, call insert or update
     *
     * \param p_fiche_def (default 0)
     */
    function Save($p_fiche_def=0)
    {
        // new card or only a update ?
        if ( $this->id == 0 )
            $this->insert($p_fiche_def);
        else
            $this->update();
    }
    /*!
     * \brief  insert a new record
     *
     * \param $p_fiche_def fiche_def.fd_id
     * \param $p_array is the array containing the data
     *\param $transation if we want to manage the transaction in this function
     * true for small insert and false for a larger loading, the BEGIN / COMMIT sql
     * must be done into the caller
     av_textX where X is the ad_id
     *\verb
    example
    av_text1=>'name'
    \endverb
     */
    function insert($p_fiche_def,$p_array=null,$transaction=true)
    {
        if ($p_array==null)
            $p_array=$_POST;

        $fiche_id=$this->cn->get_next_seq('s_fiche');
        $this->id=$fiche_id;
        // first we create the card
        if ($transaction)
            $this->cn->start();
        /*
         * Sort the array for having the name AFTER the quickcode and the 
         * Accounting
         */
        ksort($p_array);

        try
        {
            $sql=sprintf("insert into fiche(f_id,fd_id)".
                    " values (%d,%d)", $fiche_id, $p_fiche_def);
            $Ret=$this->cn->exec_sql($sql);
            // parse the $p_array array
            foreach ($p_array as $name=> $value)
            {
                /* avoid the button for searching an accounting item */
                if (preg_match('/^av_text[0-9]+$/', $name)==0)
                    continue;

                list ($id)=sscanf($name, "av_text%d");
                if ($id==null)
                    continue;

                // Special traitement
                // quickcode
                if ($id==ATTR_DEF_QUICKCODE)
                {
                    $sql=sprintf("select insert_quick_code(%d,'%s')", $fiche_id,
                            sql_string($value));
                    $this->cn->exec_sql($sql);
                    continue;
                }
                // name
                if ($id==ATTR_DEF_NAME)
                {
                    if (strlen(trim($value))==0)
                        $value="pas de nom";
                }
                // account
                if ($id==ATTR_DEF_ACCOUNT)
                {
                    $v=mb_substr(sql_string($value), 0, 40);
                    try
                    {

                        if (strlen(trim($v))!=0)
                        {
                            if (strpos($value, ',')==0)
                            {
                                $v=$this->cn->get_value("select format_account($1)",
                                        array($value));
                            }
                            else
                            {
                                $ac_array=explode(",", $value);
                                if (count($ac_array)<>2)
                                    throw new Exception('Désolé, il y a trop de virgule dans le poste comptable '.h($value));
                                $part1=$ac_array[0];
                                $part2=$ac_array[1];
                                $part1=$this->cn->get_value('select format_account($1)',
                                        array($part1));
                                $part2=$this->cn->get_value('select format_account($1)',
                                        array($part2));
                                $v=$part1.','.$part2;
                            }
                            $parameter=array($this->id, $v);
                        }
                        else
                        {
                            $parameter=array($this->id, null);
                        }
                        $v=$this->cn->get_value("select account_insert($1,$2)",
                                $parameter);
                    }
                    catch (Exception $e)
                    {
                        throw new Exception("Erreur : ce compte [$v] n'a pas de compte parent.".
                        "L'opération est annulée", 1);
                    }
                    continue;
                }
                // TVA
                if ($id==ATTR_DEF_TVA)
                {
                    // Verify if the rate exists, if not then do not update
                    if (strlen(trim($value))!=0)
                    {
                        if (isNumber($value)==0)
                            continue;
                        if ($this->cn->count_sql("select * from tva_rate where tva_id=".$value)==0)
                        {
                            continue;
                        }
                    }
                }
                // Normal traitement
                $value2=sql_string($value);

                $sql=sprintf("select attribut_insert(%d,%d,'%s')", $fiche_id,
                        $id, strip_tags(trim($value2)));
                $this->cn->exec_sql($sql);
            }
        }
        catch (Exception $e)
        {
            $this->cn->rollback();
            throw ($e);
            return;
        }
        if ($transaction)
            $this->cn->commit();
        return;
    }

    /*!\brief update a card
     */
    function update($p_array=null)
    {
        global $g_user;
        if ($p_array==null)
            $p_array=$_POST;

        try
        {
            $this->cn->start();
            // parse the $p_array array
            foreach ($p_array as $name=> $value)
            {
                if (preg_match('/^av_text[0-9]+$/', $name)==0)
                    continue;

                list ($id)=sscanf($name, "av_text%d");

                if ($id==null)
                    continue;

                // retrieve jft_id to update table attr_value
                $sql=" select jft_id from fiche_detail where ad_id=$id and f_id=$this->id";
                $Ret=$this->cn->exec_sql($sql);
                if (Database::num_row($Ret)==0)
                {
                    // we need to insert this new attribut
                    $jft_id=$this->cn->get_next_seq('s_jnt_fic_att_value');

                    $sql2="insert into fiche_detail(jft_id,ad_id,f_id,ad_value) values ($1,$2,$3,NULL)";

                    $ret2=$this->cn->exec_sql($sql2,
                            array($jft_id, $id, $this->id));
                }
                else
                {
                    $tmp=Database::fetch_array($Ret, 0);
                    $jft_id=$tmp['jft_id'];
                }
                // Special traitement
                // quickcode
                if ($id==ATTR_DEF_QUICKCODE)
                {
                    $sql=sprintf("select update_quick_code(%d,'%s')", $jft_id,
                            sql_string($value));
                    $this->cn->exec_sql($sql);
                    continue;
                }
                // name
                if ($id==ATTR_DEF_NAME)
                {
                    if (strlen(trim($value))==0)
                        continue;
                }
                // account
                if ($id==ATTR_DEF_ACCOUNT)
                {
                    $v=sql_string($value);
                    if (trim($v)!='')
                    {
                        if (strpos($v, ',')!=0)
                        {
                            $ac_array=explode(",", $v);
                            if (count($ac_array)<>2)
                                throw new Exception('Désolé, il y a trop de virgule dans le poste comptable '.h($v));
                            $part1=$ac_array[0];
                            $part2=$ac_array[1];
                            $part1=$this->cn->get_value('select format_account($1)',
                                    array($part1));
                            $part2=$this->cn->get_value('select format_account($1)',
                                    array($part2));
                            $v=$part1.','.$part2;
                        }
                        else
                        {
                            $v=$this->cn->get_value('select format_account($1)',
                                    array($value));
                        }
                        $sql=sprintf("select account_update(%d,'%s')",
                                $this->id, $v);
                        try
                        {
                            $this->cn->exec_sql($sql);
                        }
                        catch (Exception $e)
                        {
                            throw new Exception(__LINE__."Erreur : ce compte [$v] n'a pas de compte parent.".
                            "L'op&eacute;ration est annul&eacute;e");
                        }
                        continue;
                    }
                    if (strlen(trim($v))==0)
                    {

                        $sql=sprintf("select account_update(%d,null)", $this->id);
                        try
                        {
                            $Ret=$this->cn->exec_sql($sql);
                        }
                        catch (Exception $e)
                        {
                            throw new Exception(__LINE__."Erreur : ce compte [$v] n'a pas de compte parent.".
                            "L'opération est annulée");
                        }

                        continue;
                    }
                }
                // TVA
                if ($id==ATTR_DEF_TVA)
                {
                    // Verify if the rate exists, if not then do not update
                    if (strlen(trim($value))!=0)
                    {
                        if ($this->cn->count_sql("select * from tva_rate where tva_id=".$value)==0)
                        {
                            continue;
                        }
                    }
                }
                // Normal traitement
                $sql="update fiche_detail set ad_value=$1 where jft_id=$2";
                $this->cn->exec_sql($sql, array(strip_tags($value), $jft_id));
            }
        }
        catch (Exception $e)
        {
            echo '<span class="error">'.
            $e->getMessage().
            '</span>';
            $this->cn->rollback();
            return;
        }
        $this->cn->commit();
        return;
    }

    /*!\brief  remove a card
    */
    function remove($silent=false)
    {
        if ( $this->id==0 ) return;
        // verify if that card has not been used is a ledger
        // if the card has its own account in PCMN
        // Get the fiche_def.fd_id from fiche.f_id
        $this->Get();
        $fiche_def=new Fiche_Def($this->cn,$this->fiche_def);
        $fiche_def->get();

        // if the card is used do not removed it
        $qcode=$this->strAttribut(ATTR_DEF_QUICKCODE);

        if ( $this->cn->count_sql("select * from jrnx where j_qcode='".Database::escape_string($qcode)."'") != 0)
        {
            if ( ! $silent ) {
		alert(_('Impossible cette fiche est utilisée dans un journal'));
            }
            return 1;
        }

        $this->delete();
		return 0;
    }


    /*!\brief return the name of a card
     *
     */
    function getName()
    {
        $sql="select ad_value from fiche_detail
             where ad_id=1 and f_id=$1";
        $Res=$this->cn->exec_sql($sql,array($this->id));
        $r=Database::fetch_all($Res);
        if ( sizeof($r) == 0 )
            return 1;
        return $r[0]['ad_value'];
    }

    /*!\brief return the quick_code of a card
     * \return null if not quick_code is found
     */
    function get_quick_code()
    {
        $sql="select ad_value from fiche_detail where ad_id=23 and f_id=$1";
        $Res=$this->cn->exec_sql($sql,array($this->id));
        $r=Database::fetch_all($Res);
        if ( sizeof($r) == 0 )
            return null;
        return $r[0]['ad_value'];
    }

    /*!\brief Synonum of fiche::getAttribut
     */
    function Get()
    {
        $this->getAttribut();
    }
    /*!\brief Synonum of fiche::getAttribut
     */
    function load() 
    {
        $this->getAttribut();
    }
    /*!\brief get all the card thanks the fiche_def_ref
     * \param $p_offset (default =-1)
     * \param $p_search sql condition
     * \return array of fiche object
     */
    function get_by_category($p_offset=-1,$p_search="",$p_order='')
    {
        return fiche::GetByDef($this->fiche_def_ref,$p_offset,$p_search,$p_order);
    }
    /*!\brief retrieve the frd_id of the fiche it is the type of the
     *        card (bank, purchase...)
     *        (fiche_def_ref primary key)
     */
    function get_fiche_def_ref_id()
    {
        $result=$this->cn->get_array("select frd_id from fiche join fiche_Def using (fd_id) where f_id=".$this->id);
        if ( $result == null )
            return null;

        return $result[0]['frd_id'];
    }
    /**
     *@brief fetch and return and array
     *@see get_row get_row_date
     */
    private function get_row_result($res)
    {
        $array=array();
        $tot_cred=0.0;
        $tot_deb=0.0;
        $Max=Database::num_row($res);
        if ( $Max == 0 ) return null;
        for ($i=0;$i<$Max;$i++)
        {
            $array[]=Database::fetch_array($res,$i);
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
        return array($array,$tot_deb,$tot_cred);
    }
    /*!
     * \brief  Get data for poste
     *
     * \param  $p_from periode from
     * \param  $p_to   end periode
     *\param $op_let 0 all operation, 1 only lettered one, 2 only unlettered one
     * \return double array (j_date,deb_montant,cred_montant,description,jrn_name,j_debit,jr_internal)
     *         (tot_deb,tot_credit
     *
     */
    function get_row_date($p_from,$p_to,$op_let=0)
    {
        global $g_user;
        if ( $this->id == 0 )
        {
            echo_error("class_fiche",__LINE__,"id is 0");
            return;
        }
        $filter_sql=$g_user->get_ledger_sql('ALL',3);
        $sql_let='';
        switch ($op_let)
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

        $qcode=$this->strAttribut(ATTR_DEF_QUICKCODE);
        $Res=$this->cn->exec_sql("select distinct substring(jr_pj_number,'[0-9]+$'),j_id,j_date,to_char(j_date,'DD.MM.YYYY') as j_date_fmt,j_qcode,".
                                 "case when j_debit='t' then j_montant else 0 end as deb_montant,".
                                 "case when j_debit='f' then j_montant else 0 end as cred_montant,".
                                 " jr_comment as description,jrn_def_name as jrn_name,".
				 " jr_pj_number,".
                                 "j_debit, jr_internal,jr_id,coalesce(comptaproc.get_letter_jnt(j_id),-1) as letter, ".
				 " jr_tech_per,p_exercice,jrn_def_name,
								  jrn_def_code".
                                 " from jrnx left join jrn_def on jrn_def_id=j_jrn_def ".
                                 " left join jrn on jr_grpt_id=j_grpt".
				 " left join parm_periode on (p_id=jr_tech_per) ".
                                 " where j_qcode=$1 and ".
                                 " ( to_date($2,'DD.MM.YYYY') <= j_date and ".
                                 "   to_date($3,'DD.MM.YYYY') >= j_date )".
                                 " and $filter_sql $sql_let ".
                                 " order by j_date,substring(jr_pj_number,'[0-9]+$')",array($qcode,$p_from,$p_to));

        return $this->get_row_result($Res);
    }

    /*!
     * \brief  Get data for poste
     *
     * \param  $p_from periode from
     * \param  $p_to   end periode
     * \return double array (j_date,deb_montant,cred_montant,description,jrn_name,j_debit,jr_internal)
     *         (tot_deb,tot_credit
     *
     */
    function get_row($p_from,$p_to)
    {
        if ( $this->id == 0 )
        {
            echo_error("class_fiche",__LINE__,"id is 0");
            return;
        }
        $qcode=$this->strAttribut(ATTR_DEF_QUICKCODE);
        $periode=sql_filter_per($this->cn,$p_from,$p_to,'p_id','jr_tech_per');

        $Res=$this->cn->exec_sql("select j_date,to_char(j_date,'DD.MM.YYYY') as j_date_fmt,j_qcode,".
                                 "case when j_debit='t' then j_montant else 0 end as deb_montant,".
                                 "case when j_debit='f' then j_montant else 0 end as cred_montant,".
                                 " jr_comment as description,jrn_def_name as jrn_name,".
                                 "j_debit, jr_internal,jr_id ".
                                 " from jrnx left join jrn_def on jrn_def_id=j_jrn_def ".
                                 " left join jrn on jr_grpt_id=j_grpt".
                                 " where j_qcode='".$qcode."' and ".$periode.
                                 " order by j_date::date");
        return $this->get_row_result($Res);

    }
    /*!
     * \brief HtmlTable, display a HTML of a card for the asked period
     *\param $op_let 0 all operation, 1 only lettered one, 2 only unlettered one
     * \return none
     */
    function HtmlTableDetail($p_array=null,$op_let=0)
    {
        if ( $p_array == null)
            $p_array=$_REQUEST;

        $name=$this->getName();

        list($array,$tot_deb,$tot_cred)=$this->get_row_date( $p_array['from_periode'],
                                        $p_array['to_periode'],
                                        $op_let
                                                           );

        if ( count($this->row ) == 0 )
            return;
        $qcode=$this->strAttribut(ATTR_DEF_QUICKCODE);

        $rep="";
        $already_seen=array();
        echo '<h2 class="info">'.$this->id." ".$name.'</h2>';
        echo "<TABLE class=\"result\" style=\"width:100%;border-collapse:separate;border-spacing:5px\">";
        echo "<TR>".
        "<TH>"._("n° de pièce / Code interne")." </TH>".
        "<TH>"._("Date")."</TH>".
        "<TH>"._("Description")." </TH>".
        "<TH>"._('Montant')."  </TH>".
        "<TH> "._('Débit/Crédit')." </TH>".
        "</TR>";

        foreach ( $this->row as $op )
        {
            if ( in_array($op['jr_internal'],$already_seen) )
                continue;
            else
                $already_seen[]=$op['jr_internal'];
            echo "<TR  style=\"text-align:center;background-color:lightgrey\">".
            "<td>".$op['jr_pj_number']." / ".$op['jr_internal']."</td>".
            "<td>".$op['j_date']."</td>".
            "<td>".h($op['description'])."</td>".
            "<td>"."</td>".
            "<td>"."</td>".
            "</TR>";
            $ac=new Acc_Operation($this->cn);
            $ac->jr_id=$op['jr_id'];
            $ac->qcode=$qcode;
            echo $ac->display_jrnx_detail(1);

        }
        $solde_type=($tot_deb>$tot_cred)?_("solde débiteur"):_("solde créditeur");
        $diff=round(abs($tot_deb-$tot_cred),2);
        echo "<TR>".
        "<TD>$solde_type".
        "<TD>$diff</TD>".
        "<TD></TD>".
        "<TD>$tot_deb</TD>".
        "<TD>$tot_cred</TD>".
        "</TR>";

        echo "</table>";

        return;
    }
    /*!
     * \brief HtmlTable, display a HTML of a card for the asked period
     * \param $p_array default = null keys = from_periode, to_periode
     *\param $op_let 0 all operation, 1 only lettered one, 2 only unlettered one
     *\return -1 if nothing is found otherwise 0
     *\see get_row_date
     */
    function HtmlTable($p_array=null,$op_let=0,$from_div=1)
    {
        if ( $p_array == null)
            $p_array=$_REQUEST;
        $progress=0;
		// if from_periode is greater than to periode then swap the values
		if (cmpDate($p_array['from_periode'],$p_array['to_periode']) > 0)
		{
			$tmp=$p_array['from_periode'];
			$p_array['from_periode']=$p_array['to_periode'];
			$p_array['to_periode']=$tmp;

		}
        list($array, $tot_deb, $tot_cred) = $this->get_row_date($p_array['from_periode'], $p_array['to_periode'], $op_let);

        if ( count($this->row ) == 0 )
            return -1;

        $rep="";
	if ( $from_div==1)
	  {
	    echo "<TABLE class=\"resultfooter\" style=\"margin:1%;width:98%;;border-collapse:separate;border-spacing:0px 5px\">";
	  }
	else
	  {
	    echo "<TABLE id=\"tb" . $from_div . "\"class=\"result\" style=\"margin:1%;width:98%;border-collapse:separate;border-spacing:0px 2px\">";
		}
        echo '<tbody>';
        echo "<TR>".
        "<TH style=\"text-align:left\">"._('Date')."</TH>".
        "<TH style=\"text-align:left\">"._('n° pièce')." </TH>".
        "<TH style=\"text-align:left\">"._('Code interne')." </TH>".
        "<TH style=\"text-align:left\">"._('Description')." </TH>".
        "<TH style=\"text-align:right\">"._('Débit')."  </TH>".
        "<TH style=\"text-align:right\">"._('Crédit')." </TH>".
        th('Prog.','style="text-align:right"').
        th('Let.','style="text-align:right"');
        "</TR>"
        ;
	$old_exercice="";$sum_deb=0;$sum_cred=0;
	bcscale(2);
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
		if ($old_exercice != '' )
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
			"<TD>".smaller_date(format_date($op['j_date_fmt']))."</TD>".
	      td(h($op['jr_pj_number'])).
            "<TD>".$vw_operation."</TD>".
            "<TD>".h($op['description'])."</TD>".
            "<TD style=\"text-align:right\">".nbm($op['deb_montant'])."</TD>".
	      "<TD style=\"text-align:right\">".nbm($op['cred_montant'])."</TD>".
	      td(nbm(abs($progress)).$side,'style="text-align:right"').
            td($html_let, ' style="text-align:right"') .
			"</TR>";
	    $old_exercice=$op['p_exercice'];

        }
        $solde_type=($sum_deb>$sum_cred)?"solde débiteur":"solde créditeur";
        $diff=abs(bcsub($sum_deb,$sum_cred));
        echo '<tfoot>';
       echo "<TR class=\"highlight\">".
        "<TD>Totaux</TD>".
        "<TD ></TD>".
        "<TD ></TD>".
        "<TD></TD>".
	 "<TD  style=\"text-align:right\">".nbm($sum_deb)."</TD>".
	 "<TD  style=\"text-align:right\">".nbm($sum_cred)."</TD>".
	  "<TD style=\"text-align:right\">".nbm($diff)."</TD>".

        "</TR>";
        echo "<TR style=\"font-weight:bold\">".
        "<TD>$solde_type</TD>".
	  "<TD style=\"text-align:right\">".nbm($diff)."</TD>".
        "<TD></TD>".
        "</TR>";
        echo '</tfoot>';
        echo '</tbody>';

        echo "</table>";

        return 0;
    }
    /*!
     * \brief Display HTML Table Header (button)
     *
     * \return none
     */
    function HtmlTableHeader($p_array=null)
    {
        if ( $p_array == null)
            $p_array=$_REQUEST;

        $hid=new IHidden();
        echo '<div class="noprint">';
        echo "<table >";
        echo '<TR>';

        echo '<TD><form method="GET" ACTION="">'.
            HtmlInput::submit('bt_other',"Autre poste").
            HtmlInput::array_to_hidden(array('gDossier','ac'), $_REQUEST).
            dossier::hidden().
            $hid->input("type","poste").$hid->input('p_action','impress')."</form></TD>";
        $str_ople=(isset($_REQUEST['ople']))?HtmlInput::hidden('ople',$_REQUEST['ople']):'';

        echo '<TD><form method="GET" ACTION="export.php">'.
            HtmlInput::submit('bt_pdf',_("Export PDF")).
            dossier::hidden().$str_ople.
              HtmlInput::hidden('act','PDF:fichedetail').
            $hid->input("type","poste").
            $hid->input('p_action','impress').
            $hid->input("f_id",$this->id).
            dossier::hidden().
            $hid->input("from_periode",$p_array['from_periode']).
            $hid->input("to_periode",$p_array['to_periode']);
        if (isset($p_array['oper_detail']))
            echo $hid->input('oper_detail','on');

        echo "</form></TD>";

        echo '<TD><form method="GET" ACTION="export.php">'.
        HtmlInput::submit('bt_csv',_("Export CSV")).
	  HtmlInput::hidden('act','CSV:fichedetail').
        dossier::hidden().$str_ople.
        $hid->input("type","poste").
        $hid->input('p_action','impress').
        $hid->input("f_id",$this->id).
        $hid->input("from_periode",$p_array['from_periode']).
        $hid->input("to_periode",$p_array['to_periode']);
        if (isset($p_array['oper_detail']))
            echo $hid->input('oper_detail','on');

        echo "</form></TD>";
		echo "</form></TD>";
		echo '<td style="vertical-align:top">';
		echo HtmlInput::print_window();
		echo '</td>';
        echo "</table>";
        echo '</div>';

    }
    /*!
     * \brief   give the balance of an card
     * \return
     *      balance of the card
     *
     */
    function get_solde_detail($p_cond="")
    {
        if ( $this->id == 0 ) return array('credit'=>0,'debit'=>0,'solde'=>0);
        $qcode=$this->strAttribut(ATTR_DEF_QUICKCODE);

        if ( $p_cond != "") $p_cond=" and ".$p_cond;
        $Res=$this->cn->exec_sql("select sum(deb) as sum_deb, sum(cred) as sum_cred from
                                 ( select j_poste,
                                 case when j_debit='t' then j_montant else 0 end as deb,
                                 case when j_debit='f' then j_montant else 0 end as cred
                                 from jrnx
                                 where
                                 j_qcode = ('$qcode'::text)
                                 $p_cond
                                 ) as m  ");
        $Max=Database::num_row($Res);
        if ($Max==0) return 0;
        $r=Database::fetch_array($Res,0);

        return array('debit'=>$r['sum_deb'],
                     'credit'=>$r['sum_cred'],
                     'solde'=>abs($r['sum_deb']-$r['sum_cred']));
    }
    /**
     *get the bank balance with receipt or not
     *
     */
    function get_bk_balance($p_cond="")
    {
        if ( $this->id == 0 ) throw  new Exception('fiche->id est nul');
        $qcode=$this->strAttribut(ATTR_DEF_QUICKCODE);

        if ( $p_cond != "") $p_cond=" and ".$p_cond;
	$sql="select sum(deb) as sum_deb, sum(cred) as sum_cred from
                                 ( select j_poste,
                                 case when j_debit='t' then j_montant else 0 end as deb,
                                 case when j_debit='f' then j_montant else 0 end as cred
                                 from jrnx
                                 join jrn on (jr_grpt_id=j_grpt)
                                 where
                                 j_qcode = ('$qcode'::text)
                                 $p_cond
                                 ) as m  ";

        $Res=$this->cn->exec_sql($sql);
        $Max=Database::num_row($Res);
        if ($Max==0) return 0;
        $r=Database::fetch_array($Res,0);

        return array('debit'=>$r['sum_deb'],
                     'credit'=>$r['sum_cred'],
                     'solde'=>abs($r['sum_deb']-$r['sum_cred']));

    }
    /*!\brief check if an attribute is empty
     *\param $p_attr the id of the attribut to check (ad_id)
     *\return return true is the attribute is empty or missing
     */
    function empty_attribute($p_attr)
    {
        $sql="select ad_value
             from fiche_detail
             natural join fiche
             left join attr_def using (ad_id) where f_id=".$this->id.
             " and ad_id = ".$p_attr.
             " order by ad_id";
        $res=$this->cn->exec_sql($sql);
        if ( Database::num_row($res) == 0 ) return true;
        $text=Database::fetch_result($res,0,0);
        return (strlen(trim($text)) > 0)?false:true;


    }
    /*! Summary
     * \brief  show the default screen
     *
     * \param $p_search (filter)
     * \param $p_action used for specific action bank, red if credit < debit
     * \param $p_sql SQL to filter the number of card must start with AND
     * \param $p_amount true : only cards with at least one operation default : false
     * \return: string to display
     */
    function Summary($p_search="",$p_action="",$p_sql="",$p_amount=false)
    {
        global $g_user;
        $bank=new Acc_Parm_Code($this->cn,'BANQUE');
        $cash=new Acc_Parm_Code($this->cn,'CAISSE');
        $cc=new Acc_Parm_Code($this->cn,'COMPTE_COURANT');
        
        bcscale(4);
        $gDossier=dossier::id();
        $p_search=sql_string($p_search);
        $script=$_SERVER['PHP_SELF'];
        // Creation of the nav bar
        // Get the max numberRow
        $filter_amount='';
        global $g_user;

        $filter_year="  j_tech_per in (select p_id from parm_periode ".
                     "where p_exercice='".$g_user->get_exercice()."')";

        if ( $p_amount) $filter_amount=' and f_id in (select f_id from jrnx where  '.$filter_year.')';

        $all_tiers=$this->count_by_modele($this->fiche_def_ref,"",$p_sql.$filter_amount);
        // Get offset and page variable
        $offset=( isset ($_REQUEST['offset'] )) ?$_REQUEST['offset']:0;
        $page=(isset($_REQUEST['page']))?$_REQUEST['page']:1;
        $bar=navigation_bar($offset,$all_tiers,$_SESSION['g_pagesize'],$page);

        // set a filter ?
        $search=$p_sql;

        $exercice=$g_user->get_exercice();
        $tPeriode=new Periode($this->cn);
        list($max,$min)=$tPeriode->get_limit($exercice);


        if ( trim($p_search) != "" )
        {
            $search.=" and f_id in
                     (select distinct f_id from fiche_detail
                     where
                     ad_id in (1,32,30,23,18,13) and ad_value ~* '$p_search')";
        }
        // Get The result Array
        $step_tiers=$this->get_by_category($offset,$search.$filter_amount,'name');

        if ( $all_tiers == 0 || count($step_tiers)==0 ) return "";
        $r="";
        $r.=_("Filtre rapide ").HtmlInput::filter_table("tiers_tb", '0,1,2', 1);
        $r.=$bar;
        
        $r.='<table  id="tiers_tb" class="sortable"  style="width:90%;margin-left:5%">
            <TR >
            <TH>'._('Quick Code').HtmlInput::infobulle(17).'</TH>'.
            '<th>'._('Poste comptable').'</th>'.
            '<th  class="sorttable_sorted_reverse">'._('Nom').'<span id="sorttable_sortrevind">&nbsp;&blacktriangle;</span>'.'</th>
            <th>'._('Adresse').'</th>
            <th style="text-align:right">'._('Total débit').'</th>
            <th style="text-align:right">'._('Total crédit').'</th>
            <th style="text-align:right">'._('Solde').'</th>';
        $r.='</TR>';
        if ( sizeof ($step_tiers ) == 0 )
            return $r;

        $i=0;
		$deb=0;$cred=0;
        foreach ($step_tiers as $tiers )
        {
            $i++;
            
             /* Filter on the default year */
             $amount=$tiers->get_solde_detail($filter_year);

            /* skip the tiers without operation */
            if ( $p_amount && $amount['debit']==0 && $amount['credit'] == 0 && $amount['solde'] == 0 ) continue;

            $odd="";
             $odd  = ($i % 2 == 0 ) ? ' odd ': ' even ';
             $accounting=$tiers->strAttribut(ATTR_DEF_ACCOUNT);
             if ( $p_action == 'bank' && $amount['debit'] <  $amount['credit']  ){
                 if ( strpos($accounting,$bank->p_value)===0 || strpos($accounting,$cash->p_value)===0 || strpos($accounting,$cc->p_value)===0){
                 //put in red if c>d
                 $odd.=" notice ";
                 }
             }
        
             $odd=' class="'.$odd.'"';
             
            $r.="<TR $odd>";
            $url_detail=$script.'?'.http_build_query(array('sb'=>'detail','sc'=>'sv','ac'=>$_REQUEST['ac'],'f_id'=>$tiers->id,'gDossier'=>$gDossier));
            $e=sprintf('<A HREF="%s" title="Détail" class="line"> ',
                       $url_detail);

            $r.="<TD> $e".$tiers->strAttribut(ATTR_DEF_QUICKCODE)."</A></TD>";
            $r.="<TD> $e".$accounting."</TD>";
            $r.="<TD>".h($tiers->strAttribut(ATTR_DEF_NAME))."</TD>";
            $r.="<TD>".h($tiers->strAttribut(ATTR_DEF_ADRESS).
                         " ".$tiers->strAttribut(ATTR_DEF_CP).
                         " ".$tiers->strAttribut(ATTR_DEF_PAYS)).
                "</TD>";
            $str_deb=(($amount['debit']==0)?0:nbm($amount['debit']));
            $str_cred=(($amount['credit']==0)?0:nbm($amount['credit']));
            $str_solde=nbm($amount['solde']);
            $r.='<TD sorttable_customkey="'.$amount['debit'].'" align="right"> '.$str_deb.'</TD>';
            $r.='<TD sorttable_customkey="'.$amount['credit'].'" align="right"> '.$str_cred.'</TD>';
            $side=($amount['debit'] >  $amount['credit'])?'D':'C';
            $side=($amount['debit'] ==  $amount['credit'])?'=':$side;
            $red="";
            if ( $p_action == 'customer' && $amount['debit'] <  $amount['credit']  ){
                 //put in red if d>c
                 $red=" notice ";
             }
             if ( $p_action == 'supplier' && $amount['debit'] >  $amount['credit']  ){
                 //put in red if c>d
                 $red=" notice ";
             }
            $r.='<TD class="'.$red.'" sorttable_customkey="'.$amount['solde'].'" align="right"> '.$str_solde."$side </TD>";
            $deb=bcadd($deb,$amount['debit']);
            $cred=bcadd($cred,$amount['credit']);

            $r.="</TR>";

        }
		$r.="<tfoot >";
		$solde=abs(bcsub($deb,$cred));
                $side=($deb > $cred)?'Débit':'Crédit';
                $r.='<tr class="highlight">';
		$r.=td("").td("").td("").td("Totaux").td(nbm($deb),'class="num"').td(nbm($cred),'class="num"').td(" $side ".nbm($solde),'class="num"');
                $r.='</tr>';
		$r.="</tfoot>";
        $r.="</TABLE>";
        $r.=$bar;
        return $r;
    }
    /*!
     * \brief get the fd_id of the card : fd_id, it set the attribute fd_id
     */
    function get_categorie()
    {
        if ( $this->id == 0 ) throw  new Exception('class_fiche : f_id = 0 ');
        $sql='select fd_id from fiche where f_id='.$this->id;
        $R=$this->cn->get_value($sql);
        if ( $R == "" )
            $this->fd_id=0;
        else
            $this->fd_id=$R;
    }
    /*!
     ***************************************************
     * \brief   Check if a fiche is used by a jrn
     *  return 1 if the  fiche is in the range otherwise 0, the quick_code
     *  or the id  must be set
     *
     *
     * \param   $p_jrn journal_id
     * \param   $p_type : deb or cred default empty
     *
     * \return 1 if the fiche is in the range otherwise < 1
     *        -1 the card doesn't exist
     *        -2 the ledger has no card to check
     *
     */
    function belong_ledger($p_jrn,$p_type="")
    {
        // check if we have a quick_code or a f_id
        if (($this->quick_code==null || $this->quick_code == "" )
                && $this->id == 0 )
        {
            throw  new Exception( 'erreur ni quick_code ni f_id ne sont donnes');
        }

        //retrieve the quick_code
        if ( $this->quick_code=="")
            $this->quick_code=$this->get_quick_code();


        if ( $this->quick_code==null)
            return -1;

        if ( $this->id == 0 )
            if ( $this->get_by_qcode(null,false) == 1)
                return -1;

        $get="";
        if ( $p_type == 'deb' )
        {
            $get='jrn_def_fiche_deb';
        }
        if ( $p_type == 'cred' )
        {
            $get='jrn_def_fiche_cred';
        }
        if ( $get != "" )
        {
            $Res=$this->cn->exec_sql("select $get as fiche from jrn_def where jrn_def_id=$p_jrn");
        }
        else
        {
            // Get all the fiche type (deb and cred)
            $Res=$this->cn->exec_sql(" select jrn_def_fiche_cred as fiche
                                     from jrn_def where jrn_def_id=$p_jrn
                                     union
                                     select jrn_def_fiche_deb
                                     from jrn_def where jrn_def_id=$p_jrn"
                                    );
        }
        $Max=Database::num_row($Res);
        if ( $Max==0)
        {
            return -2;
        }
        /* convert the array to a string */
        $list=Database::fetch_all($Res);
        $str_list="";
        $comma='';
        foreach ($list as $row)
        {
            if ( $row['fiche'] != '' )
            {
                $str_list.=$comma.$row['fiche'];
                $comma=',';
            }
        }
        // Normally Max must be == 1

        if ( $str_list=="")
        {
            return -3;
        }

        $sql="select *
             from fiche
             where
             fd_id in (".$str_list.") and f_id= ".$this->id;

        $Res=$this->cn->exec_sql($sql);
        $Max=Database::num_row($Res);
        if ($Max==0 )
            return 0;
        else
            return 1;
    }
    /*!\brief  get all the card from a categorie
     *\param $p_cn database connx
     *\param $pFd_id is the category id
     *\param $p_order for the sort, possible values is name_asc,name_desc or nothing
     *\return an array of card, but only the fiche->id is set
     */
    static function get_fiche_def($p_cn,$pFd_id,$p_order='')
    {
        switch ($p_order)
        {
        case 'name_asc':
            $sql='select f_id,ad_value from fiche join fiche_detail using (f_id) where ad_id=1 and fd_id=$1 order by 2 asc';
            break;
        case 'name_desc':
            $sql='select f_id,ad_value from fiche join fiche_detail using (f_id) where ad_id=1 and fd_id=$1 order by 2 desc';
            break;
        default:
            $sql='select f_id from fiche  where fd_id=$1 ';
        }
        $array=$p_cn->get_array($sql,array($pFd_id));

	return $array;
    }
    /*!\brief check if a card is used
     *\return return true is a card is used otherwise false
     */
    function is_used()
    {
        /* retrieve first the quickcode */
        $qcode=$this->strAttribut(ATTR_DEF_QUICKCODE);
        $sql='select count(*) as c from jrnx where j_qcode=$1';
        $count=$this->cn->get_value($sql,array($qcode));
        if ( $count == 0 ) return false;
        return true;
    }
    /*\brief remove a card without verification */
    function delete()
    {
        // Remove from attr_value
        $Res=$this->cn->exec_sql("delete from fiche_detail
                                 where
                                   f_id=".$this->id);

        // Remove from fiche
        $Res=$this->cn->exec_sql("delete from fiche where f_id=".$this->id);

    }
    /*!\brief create the sql statement for retrieving all
     * the card
     *\return string with sql statement
     *\param $array contains the condition
    \verbatim
       [jrn] => 2
       [typecard] => cred / deb / filter or list
       [query] => string
    \endverbatim
     *\note the typecard cred, deb or filter must be used with jrn, the value of list means a list of fd_id
     *\see ajax_card.php cards.js
     */
    function build_sql($array)
    {
        if (!empty($array))
            extract($array);
        $and='';
        $filter_fd_id='true';
        $filter_query='';
        if (isset($typecard))
        {
            if (strpos($typecard, "sql")==false)
            {
                switch ($typecard)
                {
                    case 'cred':
                        if (!isset($jrn))
                            throw ('Erreur pas de valeur pour jrn');
                        $filter_jrn=$this->cn->make_list("select jrn_def_fiche_cred from jrn_Def where jrn_def_id=$1",
                                array($jrn));
                        $filter_fd_id=" fd_id in (".$filter_jrn.")";
                        $and=" and ";
                        break;
                    case 'deb':
                        if (!isset($jrn))
                            throw ('Erreur pas de valeur pour jrn');
                        $filter_jrn=$this->cn->make_list("select jrn_def_fiche_deb from jrn_Def where jrn_def_id=$1",
                                array($jrn));
                        $filter_fd_id=" fd_id in (".$filter_jrn.")";
                        $and=" and ";
                        break;
                    case 'filter':
                        if (!isset($jrn))
                            throw ('Erreur pas de valeur pour jrn');
                        $filter_jrn=$this->cn->make_list("select jrn_def_fiche_deb from jrn_Def where jrn_def_id=$1",
                                array($jrn));

                        if (trim($filter_jrn)!='')
                            $fp1=" fd_id in (".$filter_jrn.")";
                        else
                            $fp1="fd_id < 0";

                        $filter_jrn=$this->cn->make_list("select jrn_def_fiche_cred from jrn_Def where jrn_def_id=$1",
                                array($jrn));

                        if (trim($filter_jrn)!='')
                            $fp2=" fd_id in (".$filter_jrn.")";
                        else
                            $fp2="fd_id < 0";

                        $filter_fd_id='('.$fp1.' or '.$fp2.')';

                        $and=" and ";
                        break;
                    case 'all':
                        $filter_fd_id=' true';
                        break;
                    default:
                        if (trim($typecard)!='')
                            $filter_fd_id=' fd_id in ('.$typecard.')';
                        else
                            $filter_fd_id=' fd_id < 0';
                }
            }
            else
            {
                $filter_fd_id=str_replace('[sql]', '', $typecard);
            }
        }

        $and=" and ";
        if (isset($query))
        {
            $query=sql_string($query);

            if (strlen(trim($query))>1)
            {
                $filter_query=$and."(vw_name ilike '%$query%' or quick_code ilike ('%$query%') "
                        ." or vw_description ilike '%$query%' or tva_num ilike '%$query%' or accounting like upper('$query%'))";
            }
            else
            {
                $filter_query='';
            }
        }
        $sql="select * from vw_fiche_attr where ".$filter_fd_id.$filter_query;
        return $sql;
    }

    /**
     *@brief move a card to another cat. The properties will changed
     * and be removed
     *@param $p_fdid the fd_id of destination
     */
    function move_to($p_fdid)
    {
        $this->cn->start();
        $this->cn->exec_sql('update fiche set fd_id=$1 where f_id=$2',array($p_fdid,$this->id));
        // add missing
        $this->cn->exec_sql('select fiche_attribut_synchro($1)',array($p_fdid));
        // add to the destination missing fields
        $this->cn->exec_sql("insert into jnt_fic_attr (fd_id,ad_id,jnt_order) select $1,ad_id,100 from fiche_detail where f_id=$2 and ad_id not in (select ad_id from jnt_fic_attr where fd_id=$3)",array($p_fdid,$this->id,$p_fdid));
        $this->cn->commit();
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
    static function test_me()
    {
        $cn=new Database(dossier::id());
        $a=new Fiche($cn);
        $select_cat=new ISelect('fd_id');
        $select_cat->value=$cn->make_array('select fd_id,fd_label from fiche_def where frd_id='.
                                           FICHE_TYPE_CLIENT);
        echo '<FORM METHOD="GET"> ';
        echo dossier::hidden();
        echo HtmlInput::hidden('test_select',$_GET['test_select']);
        echo 'Choix de la catégorie';
        echo $select_cat->input();
        echo HtmlInput::submit('go_card','Afficher');
        echo '</form>';
        if ( isset ($_GET['go_card']))
        {
            $empty=$a->to_array($_GET['fd_id']);
            print_r($empty);
        }
    }

	function get_gestion_title()
	{
		$r = "<h2>" . h($this->getName()) . " " . h($this->getAttribut(ATTR_DEF_FIRST_NAME)) . '[' . $this->get_quick_code() . ']</h2>';
		return $r;
	}
	function get_all_account()
	{

	}
}

?>
