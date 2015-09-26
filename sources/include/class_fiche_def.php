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
require_once NOALYSS_INCLUDE.'/class_fiche_attr.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_fiche_def_ref.php';
require_once NOALYSS_INCLUDE.'/class_fiche.php';
require_once NOALYSS_INCLUDE.'/user_common.php';
require_once NOALYSS_INCLUDE.'/class_iradio.php';

/*! \file
 * \brief define Class fiche and fiche def, those class are using
 *        class attribut
 */
/*!
 * \brief define Class fiche and fiche def, those class are using
 *        class attribut
 */
class Fiche_Def
{
    var $cn;           // database connection
    var $id;			// id (fiche_def.fd_id
    var $label;			// fiche_def.fd_label
    var $class_base;		// fiche_def.fd_class_base
    var $fiche_def;		// fiche_def.frd_id = fiche_def_ref.frd_id
    var $create_account;		// fd_create_account: flag
    var $all;
    var $attribut;		// get from attr_xxx tables
    function __construct($p_cn,$p_id = 0)
    {
        $this->cn=$p_cn;
        $this->id=$p_id;
    }
    /*!\brief show the content of the form to create  a new Fiche_Def_Ref
    */
    function input ()
    {
        $ref=$this->cn->get_array("select * from fiche_def_ref order by frd_text");
        $iradio=new IRadio();
        /* the accounting item */
        $class_base=new IPoste('class_base');
        $class_base->set_attribute('ipopup','ipop_account');
        $class_base->set_attribute('account','class_base');
        $class_base->set_attribute('label','acc_label');
        $f_class_base=$class_base->input();
		$fd_description=new ITextarea('fd_description');
		$fd_description->width=80;
		$fd_description->heigh=4;
		$fd_description->style='class="itextarea" style="margin-left:0px;vertical-align:text-top"';
        require_once  NOALYSS_INCLUDE.'/template/fiche_def_input.php';
        return;
    }

    /*!
     *  \brief  Get attribut of a fiche_def
     *
     * \return string value of the attribute
     */
    function getAttribut()
    {
        $sql="select * from jnt_fic_attr ".
             " natural join attr_def where fd_id=".$this->id.
             " order by jnt_order";

        $Ret=$this->cn->exec_sql($sql);

        if ( ($Max=Database::num_row($Ret)) == 0 )
            return ;
        for ($i=0;$i < $Max;$i++)
        {
            $row=Database::fetch_array($Ret,$i);
            $t = new Fiche_Attr($this->cn);
            $t->ad_id=$row['ad_id'];
            $t->ad_text=$row['ad_text'];
            $t->jnt_order=$row['jnt_order'];
            $t->ad_size=$row['ad_size'];
            $t->ad_type=$row['ad_type'];
            $t->ad_extra=$row['ad_extra'];
            $this->attribut[$i]=clone $t;
        }
        return $this->attribut;
    }

    /*!
    * \brief  Get attribut of the fiche_def
    *
    */
    function get()
    {
        if ( $this->id == 0 )
            return 0;
        /*    $this->cn->exec_sql('select fiche_attribut_synchro($1)',
        array($this->id));
        */
        $sql="select * from fiche_def ".
             " where fd_id=".$this->id;
        $Ret=$this->cn->exec_sql($sql);
        if ( ($Max=Database::num_row($Ret)) == 0 )
            return ;
        $row=Database::fetch_array($Ret,0);
        $this->label=$row['fd_label'];
        $this->class_base=$row['fd_class_base'];
        $this->fiche_def=$row['frd_id'];
        $this->create_account=$row['fd_create_account'];
        $this->fd_description=$row['fd_description'];
    }
    /*!
     **************************************************
     * \brief  Get all the fiche_def
     *
     * \return an array of fiche_def object
     */
    function get_all()
    {
        $sql="select * from fiche_def ";

        $Ret=$this->cn->exec_sql($sql);
        if ( ($Max=Database::num_row($Ret)) == 0 )
            return ;

        for ( $i = 0; $i < $Max;$i++)
        {
            $row=Database::fetch_array($Ret,$i);
            $this->all[$i]=new Fiche_Def($this->cn,$row['fd_id']);
            $this->all[$i]->label=$row['fd_label'];
            $this->all[$i]->class_base=$row['fd_class_base'];
            $this->all[$i]->fiche_def=$row['frd_id'];
            $this->all[$i]->create_account=$row['fd_create_account'];
        }
    }
    /*!
     **************************************************
     * \brief  Check in vw_fiche_def if a fiche has
     *           a attribut X
     *
     *
     * \param  $p_attr attribut to check
     * \return  true or false
     */
    function HasAttribute($p_attr)
    {
        return ($this->cn->count_sql("select * from vw_fiche_def where ad_id=$p_attr and fd_id=".$this->id)>0)?true:false;

    }
    /*!
     **************************************************
     * \brief  Display category into a table
     *
     * \return HTML row
     */
    function Display()
    {
		$tab = new Sort_Table();

		$url = HtmlInput::get_to_string(array('ac', 'gDossier'));
		$tab->add(_("Nom de fiche"), $url, "order by fd_label asc", "order by fd_label desc", "na", "nd");
		$tab->add(_("Basé sur le poste comptable"), $url, "order by fd_class_base asc", "order by fd_class_base desc", "pa", "pd");
		$tab->add(_("Calcul automatique du poste comptable"), $url, "order by fd_create_account asc", "order by fd_create_account desc", "ca", "cd");
		$tab->add(_("Basé sur le modèle"), $url, "order by frd_text asc", "order by frd_text  desc", "ma", "md");

		$order = (isset($_GET['ord'])) ? $tab->get_sql_order($_GET["ord"]) : $tab->get_sql_order("na");


		$res = $this->cn->exec_sql("SELECT fd_id, fd_class_base, fd_label, fd_create_account, fiche_def_ref.frd_id,
frd_text , fd_description FROM fiche_def join fiche_def_ref on (fiche_def.frd_id=fiche_def_ref.frd_id)
$order
");

		require_once NOALYSS_INCLUDE.'/template/fiche_def_list.php';
	}
    /*!\brief Add a fiche category thanks the element from the array
     * you cannot add twice the same cat. name
     * table : insert into fiche_def
     *         insert into attr_def
     *
     * \param $array array
     *        index FICHE_REF
     *              nom_mod
     *              class_base
     *              fd_description
     */
    function Add($array)
    {
        /** 
         * Check needed info
         */
        $p_nom_mod = HtmlInput::default_value('nom_mod', "", $array);
        $p_fd_description = HtmlInput::default_value('fd_description', "", $array);
        $p_class_base= HtmlInput::default_value('class_base', "", $array);
        $p_fiche_def= HtmlInput::default_value('FICHE_REF', "", $array);
        $p_create= HtmlInput::default_value('create', "off", $array);
        
        // If there is no description then add a empty one
        if ( ! isset ($p_fd_description)) {
            $p_fd_description="";
        }
        // Format correctly the name of the cat. of card
        $p_nom_mod=sql_string($p_nom_mod);


        // Name can't be empty
        if ( strlen(trim($p_nom_mod)) == 0 )
	{
            alert (_('Le nom de la catégorie ne peut pas être vide'));
            return 1;
	}
        // $p_fiche_def can't be empty
        if ( strlen(trim($p_fiche_def)) == 0 )
	{
            alert (_('Un modéle de catégorie est obligatoire'));
            return 1;
	}
        
        /* check if the cat. name already exists */
        $sql="select count(*) from fiche_Def where upper(fd_label)=upper($1)";
        $count=$this->cn->get_value($sql,array(trim($p_nom_mod)));

        if ($count != 0 ) {
			 echo alert (_('Catégorie existante'));
			return 1;
		}
        // Set the value of fiche_def.fd_create_account
        // automatic creation for 'poste comptable'
        if ( $p_create == "on" && strlen(trim($p_class_base)) != 0)
            $p_create='true';
        else
            $p_create='false';

        // Class is valid ?
        if ( sql_string($p_class_base) != null || ( $p_class_base !='' && strpos(',',$p_class_base) != 0 ))
        {
            // p_class is a valid number
            $sql="insert into fiche_def(fd_label,fd_class_base,frd_id,fd_create_account,fd_description)
                 values ($1,$2,$3,$4,$5) returning fd_id";

            $fd_id=$this->cn->get_value($sql,array($p_nom_mod,$p_class_base,$p_fiche_def,$p_create,$p_fd_description));

            // p_class must be added to tmp_pcmn if it is a single accounting
            if ( strpos(',',$p_class_base) ==0)
            {
                $sql="select account_add($1,$2)";
                $Res=$this->cn->exec_sql($sql,array($p_class_base,$p_nom_mod));
            }
			// Get the fd_id
			$fd_id=$this->cn->get_current_seq('s_fdef');

			// update jnt_fic_attr
			$sql=sprintf("insert into jnt_fic_attr(fd_id,ad_id,jnt_order)
					 values (%d,%d,10)",$fd_id,ATTR_DEF_ACCOUNT);
			$Res=$this->cn->exec_sql($sql);
        }
        else
        {
            //There is no class base not even as default
            $sql="insert into fiche_def(fd_label,frd_id,fd_create_account,fd_description) values ($1,$2,$3,$4) returning fd_id";


            $this->id=$this->cn->get_value($sql,array($p_nom_mod,$p_fiche_def,$p_create,$p_fd_description));

            // Get the fd_id
            $fd_id=$this->cn->get_current_seq('s_fdef');

        }

        // Get the default attr_def from attr_min
        $def_attr=$this->get_attr_min($p_fiche_def);

        //if defaut attr not null
        // build the sql insert for the table attr_def
        if (sizeof($def_attr) != 0 )
        {
            // insert all the mandatory fields into jnt_fiche_attr
            $jnt_order=10;
            foreach ( $def_attr as $i=>$v)
            {
				$order=$jnt_order;
                if ( $v['ad_id'] == ATTR_DEF_NAME )
                    $order=0;
				$count=$this->cn->get_value("select count(*) from jnt_fic_attr where fd_id=$1 and ad_id=$2",array($fd_id,$v['ad_id']));
				if ($count == 0)
				{
					$sql=sprintf("insert into jnt_fic_Attr(fd_id,ad_id,jnt_order)
                             values (%d,%s,%d)",
                             $fd_id,$v['ad_id'],$order);
					$this->cn->exec_sql($sql);
					$jnt_order+=10;
				}
            }
        }
        $this->id=$fd_id;
        return 0;

    }//--------------end function Add ----------------------------
    /*!
     * \brief Get all the card where the fiche_def.fd_id is given in parameter
     * \param $step = 0 we don't use the offset, page_size,...
     *        $step = 1 we use the jnr_bar_nav
     *
     * \return array ('f_id'=>..,'ad_value'=>..)
     *\see fiche
     */
    function get_by_type($step=0)
    {
        $sql="select f_id,ad_value
             from
             fiche join fiche_detail using(f_id)
             where ad_id=1 and fd_id=$1 order by 2";

        // we use navigation_bar
        if ($step == 1  && $_SESSION['g_pagesize'] != -1   )
        {
            $offset=(isset($_GET['offset']))?$_GET['offset']:0;
            $step=$_SESSION['g_pagesize'];
            $sql.=" offset $offset limit $step";
        }

        $Ret=$this->cn->get_array($sql,array($this->id));

        return $Ret;
    }
    /*!
     * \brief Get all the card where the fiche_def.frd_id is given in parameter
     * \return array of fiche or null is nothing is found
     */
    function get_by_category($p_cat)
    {
        $sql="select f_id,ad_value
             from
             fiche join fiche_def  using(fd_id)
	     join fiche_detail using(f_id)
             where ad_id=1 and frd_id=$1 order by 2 ";

        $Ret=$this->cn->exec_sql($sql,array($p_cat));
        if ( ($Max=Database::num_row($Ret)) == 0 )
            return null;
        $all[0]=new Fiche($this->cn);

        for ($i=0;$i<$Max;$i++)
        {
            $row=Database::fetch_array($Ret,$i);
            $t=new Fiche($this->cn,$row['f_id']);
            $t->getAttribut();
            $all[$i]=$t;

        }
        return $all;
    }

    /*!\brief list the card of a fd_id
     */
    function myList()
    {
        $this->get();
        echo '<H2 class="info">'.$this->id." ".$this->label.'</H2>';

        $step=$_SESSION['g_pagesize'];
        $sql_limit="";
        $sql_offset="";
        $bar="";
        if ( $step != -1 )
        {

            $page=(isset($_GET['page']))?$_GET['page']:1;
            $offset=(isset($_GET['offset']))?$_GET['offset']:0;
            $max_line=$this->cn->count_sql("select f_id,ad_value  from
                                           fiche join fiche_detail using (f_id)
                                           where fd_id='".$this->id."' and ad_id=".ATTR_DEF_NAME." order by f_id");
            $sql_limit=" limit ".$step;
            $sql_offset=" offset ".$offset;
            $bar=navigation_bar($offset,$max_line,$step,$page);
        }

        // Get all name the cards of the select category
        // 1 for attr_def.ad_id is always the name
        $Res=$this->cn->exec_sql("select f_id,vw_name,quick_code  from ".
                                 " vw_fiche_attr ".
                                 " where fd_id='".$this->id.
                                 "' order by f_id $sql_offset $sql_limit ");
        $Max=Database::num_row($Res);
        echo $bar;
        $str="";
        // save the url
        // with offet &offset=15&step=15&page=2&size=15
        if ( $_SESSION['g_pagesize'] != -1)
        {
            $str=sprintf("&offset=%s&step=%s&page=%s&size=%s",
                         $offset,
                         $step,
                         $page,
                         $max_line);
        }


        echo '<FORM METHOD="POST" action="?p_action=fiche&action=vue'.$str.'">';
	echo HtmlInput::hidden('ac',$_REQUEST['ac']);
        echo dossier::hidden();
        echo HtmlInput::hidden("fiche",$this->id);
        echo HtmlInput::submit('add','Ajout fiche');
        echo '</FORM>';
        $str_dossier=dossier::get();
        echo '<table>';
        for ( $i = 0; $i < $Max; $i++)
        {
            $l_line=Database::fetch_array($Res,$i);
            if ( $i%2 == 0)
                echo '<TR class="odd">';
            else
                echo '<TR class="even">';

            $span_mod='<TD><A href="?p_action=fiche&'.$str_dossier.
		    '&action=detail&fiche_id='.$l_line['f_id'].$str.'&fiche='.
		    $_REQUEST['fiche'].'&ac='.$_REQUEST['ac'].'">'.$l_line['quick_code']
		    .'</A></TD>';

            echo $span_mod.'<TD>'.h($l_line['vw_name'])."</TD>";
            echo '</tr>';
        }
        echo '</table>';
        echo '<FORM METHOD="POST" action="?p_action=fiche&action=vue'.$str.'">';
	echo HtmlInput::hidden('ac',$_REQUEST['ac']);
        echo dossier::hidden();
        echo HtmlInput::hidden("fiche",$this->id);
        echo HtmlInput::submit('add','Ajout fiche');
        echo '</FORM>';
        echo $bar;

    }
    /*!\brief show input for the basic attribute : label, class_base, create_account
     * use only when we want to update
     *
     *\return HTML string with the form
     */
    function input_base()
    {
        $r="";
        $r.=_('Label');
        $label=new IText('label',$this->label);
        $r.=$label->input();
        $r.='<br>';
        /* the accounting item */
        $class_base=new IPoste('class_base',$this->class_base);
        $class_base->set_attribute('ipopup','ipop_account');
        $class_base->set_attribute('account','class_base');
        $class_base->set_attribute('label','acc_label');
        $fd_description=new ITextarea('fd_description',$this->fd_description);
        $fd_description->width=80;
        $fd_description->heigh=4;
        $fd_description->style='class="itextarea" style="margin-left:0px;vertical-align:text-top"';

        $r.=_('Poste Comptable de base').' : ';
        $r.=$class_base->input();
        $r.='<span id="acc_label"></span><br>';
		$r.='<br/>';
		$r.=" Description ".$fd_description->input();
        /* auto Create */
		$r.='<br/>';
        $ck=new ICheckBox('create');
        $ck->selected=($this->create_account=='f')?false:true;
        $r.=_('Chaque fiche aura automatiquement son propre poste comptable : ');
        $r.=$ck->input();
        return $r;
    }
    /*!\brief Display all the attribut of the fiche_def
     *\param $str give the action possible values are remove, empty
     */
    function DisplayAttribut($str="")
    {
        if ( $this->id == 0 )
            return ;
           $this->cn->exec_sql('select fiche_attribut_synchro($1)',array($this->id));

		   $MaxLine=sizeof($this->attribut);
        $r="<TABLE>";
	$r.="<tr>".th('Nom attribut').th('').th('Ordre','style="text-align:right"').'</tr>';
        // Display each attribute
        $add_action="";
        for ($i=0;$i<$MaxLine;$i++)
        {
            $class="even";
            if ( $i % 2 == 0 )
                $class="odd";

            $r.='<TR class="'.$class.'"><td>';
            // Can change the name
            if ( $this->attribut[$i]->ad_id == ATTR_DEF_NAME )
            {
                continue;
            }
            else
            {
                if ( $str == "remove" )
                {
                    //Only for the not mandatory attribute (not defined in attr_min)
                    if ( $this->cn->count_sql("select * from attr_min where frd_id=".
                                              $this->fiche_def." and ad_id = ".$this->attribut[$i]->ad_id) == 0
                            && $this->attribut[$i]->ad_id != ATTR_DEF_QUICKCODE
                            && $this->attribut[$i]->ad_id != ATTR_DEF_ACCOUNT
                       )
                    {
                        $add_action=sprintf( '</TD><TD> Supprimer <input type="checkbox" name="chk_remove[]" value="%d">',
                                             $this->attribut[$i]->ad_id);
                    }
                    else
                        $add_action="</td><td>";
                }
                // The attribut.
                $a=sprintf('%s ',  $this->attribut[$i]->ad_text);
                $r.=$a.$add_action;
                /*----------------------------------------  */
                /*  ORDER OF THE CARD */
                /*----------------------------------------  */
                $order=new IText();
                $order->name='jnt_order'.$this->attribut[$i]->ad_id;
                $order->size=3;
                $order->value=$this->attribut[$i]->jnt_order;
                $r.='</td><td> '.$order->input();
            }
            $r.= '</td></tr>';
        }

        // Show the possible attribute which are not already attribute of the model
        // of card
        $Res=$this->cn->exec_sql("select ad_id,ad_text from attr_def
                                 where
                                 ad_id not in (select ad_id from fiche_def natural join jnt_fic_attr
                                 where fd_id=$1) order by ad_text",array($this->id) );
        $M=Database::num_row($Res);

        // Show the unused attribute
        $r.='<TR> <TD>';
        $r.= '<SELECT NAME="ad_id">';
        for ($i=0;$i<$M;$i++)
        {
            $l=Database::fetch_array($Res,$i);
            $a=sprintf('<OPTION VALUE="%s"> %s',
                       $l['ad_id'],$l['ad_text']);
            $r.=$a;
        }
        $r.='</SELECT>';

        $r.="</TABLE>";
        return $r;
    }
    /*!\brief Save the label of the fiche_def
     * \param $p_label label
     */
    function SaveLabel($p_label)
    {
        if ( $this->id == 0 ) return;
        $p_label=sql_string($p_label);
        if (strlen(trim ($p_label)) == 0 )
        {
            return;
        }
        $sql=sprintf("update   fiche_def set fd_label='%s' ".
                     "where                    fd_id=%d",
                     $p_label,$this->id);
        $Res=$this->cn->exec_sql($sql);

    }
    /*!\brief set the auto create accounting item for each card and
     * save it into the database
     * \param $p_label true or false
     */
    function set_autocreate($p_label)
    {
        if ( $this->id == 0 ) return;
        if ($p_label==true)
            $t='t';
        if ($p_label==false)
            $t='f';

        $sql="update   fiche_def set fd_create_account=$1 ".
             "where                    fd_id=$2";

        $Res=$this->cn->exec_sql($sql,array($t,$this->id));

    }
    /*!\brief Save the class base
     * \param $p_label label
     */
    function save_class_base($p_label)
    {
        if ( $this->id == 0 ) return;
        $p_label=sql_string($p_label);

        $sql="update   fiche_def set fd_class_base=$1 ".
             "where                    fd_id=$2";

        $Res=$this->cn->exec_sql($sql,array($p_label,$this->id));
    }
	function save_description($p_description)
	{
		if ( $this->id == 0)			return;
		$this->cn->exec_sql("update fiche_def set fd_description=$1 where fd_id=$2",array($p_description,$this->id));
	}


    /*!\brief insert a new attribut for this fiche_def
     * \param $p_ad_id id of the attribut
     */
    function InsertAttribut($p_ad_id)
    {
        if ( $this->id == 0 ) return;
        /* ORDER */
        $this->GetAttribut();
        $max=sizeof($this->attribut)*15;
        // Insert a new attribute for the model
        // it means insert a row in jnt_fic_attr
        $sql=sprintf("insert into jnt_fic_attr (fd_id,ad_id,jnt_order) values (%d,%d,%d)",
                     $this->id,$p_ad_id,$max);
        $Res=$this->cn->exec_sql($sql);
    }
    /*!\brief remove an attribut for this fiche_def
     * \param array of ad_id to remove
     * \remark you can't remove the attribut defined in attr_min
     */
    function RemoveAttribut($array)
    {
        foreach ($array as $ch)
        {
            $this->cn->start();
            $sql="delete from jnt_fic_attr where fd_id=$1 ".
                 "   and ad_id=$2";
            $this->cn->exec_sql($sql,array($this->id,$ch));

            $sql="delete from fiche_detail  where jft_id in ( select ".
                 " jft_id from fiche_Detail ".
                 " join fiche using(f_id) ".
                 " where ".
                 "fd_id = $1 and ".
                 "ad_id=$2)";
            $this->cn->exec_sql($sql,array($this->id,$ch));

            $this->cn->commit();
        }
    }

    /*!\brief save the order of a card, update the column jnt_fic_attr.jnt_order
     *\param $p_array containing the order
     */
    function save_order($p_array)
    {
        extract($p_array);
        $this->GetAttribut();
        foreach ($this->attribut as $row)
        {
            if ( $row->ad_id == 1 ) continue;
            if ( ${'jnt_order'.$row->ad_id} <= 0 ) continue;
            $sql='update jnt_fic_attr set jnt_order=$1 where fd_id=$2 and ad_id=$3';
            $this->cn->exec_sql($sql,array(${'jnt_order'.$row->ad_id},
                                           $this->id,
                                           $row->ad_id));

        }
        /* correct the order */
        $this->cn->exec_sql('select attribute_correct_order()');
    }


    /*!\brief remove all the card from a categorie after having verify
     *that the card is not used and then remove also the category
     *\return the remains items, not equal to 0 if a card remains and
     *then the category is not removed
     */
    function remove()
    {
        if ( $this->id >= 500000 ) {
            throw new Exception(_('Catégorie verrouillée '));
        }
        $remain=0;
        /* get all the card */
        $aFiche=fiche::get_fiche_def($this->cn,$this->id);
        if ( $aFiche != null )
        {
            /* check if the card is used */
            foreach ($aFiche as $dfiche)
            {
	      $fiche=new Fiche($this->cn,$dfiche['f_id']);

                /* if the card is not used then remove it otherwise increment remains */
                if ( $fiche->is_used() == false )
                {
                    $fiche->delete();
                }
                else
                    $remain++;
            }
        }
        /* if remains == 0 then remove cat */
        if ( $remain == 0 )
        {
            $sql='delete from jnt_fic_attr where fd_id=$1';
            $this->cn->exec_sql($sql,array($this->id));
            $sql='delete from fiche_def where fd_id=$1';
            $this->cn->exec_sql($sql,array($this->id));
        }

        return $remain;

    }
    /*!
     * \brief  retrieve the mandatory field of the card model
     *
     * \param $p_fiche_def_ref
     * \return array of ad_id  (attr_min.ad_id) and  labels (attr_def.ad_text)
     */
    function get_attr_min($p_fiche_def_ref)
    {

        // find the min attr for the fiche_def_ref
        $Sql="select ad_id,ad_text from attr_min natural join attr_def
             natural join fiche_def_ref
             where
             frd_id= $1";
        $Res=$this->cn->exec_sql($Sql,array($p_fiche_def_ref));
        $Num=Database::num_row($Res);

        // test the number of returned rows
        if ($Num == 0 ) return null;

        // Get Results & Store them in a array
        for ($i=0;$i<$Num;$i++)
        {
            $f=Database::fetch_array($Res,$i);
            $array[$i]['ad_id']=$f['ad_id'];
            $array[$i]['ad_text']=$f['ad_text'];
        }
        return $array;
    }
    /*!\brief count the number of fiche_def (category) which has the frd_id (type of category)
     *\param $p_frd_id is the frd_id in constant.php the FICHE_TYPE_
     *\return the number of cat. of card of the given type
     *\see constant.php
     */
    function count_category($p_frd_id)
    {
        $ret=$this->cn->count_sql("select fd_id from fiche_def where frd_id=$1",array($p_frd_id));
        return $ret;
    }
	function input_detail()
	{
		$r = "";
		// Save the label

		$this->get();
		$this->GetAttribut();
		$r.= '<H2 class="info">' . $this->id . " " . h($this->label) . '</H2>';
		$r.='<fieldset><legend>'._('Données générales').'</legend>';

		/* show the values label class_base and create account */
		$r.='<form method="post">';
		$r.=dossier::hidden();
		$r.=HtmlInput::hidden("fd_id", $this->id);
		$r.=HtmlInput::hidden("p_action", "fiche");
		$r.= $this->input_base();
		$r.='<hr>';
		$r.=HtmlInput::submit('change_name', _('Sauver'));
		$r.='</form>';
		$r.='</fieldset>';
		/* attributes */
		$r.='<fieldset><legend>'._('Détails').'</legend>';

		$r.= '<FORM  id="input_detail_frm" method="POST">';
		$r.=dossier::hidden();
		$r.=HtmlInput::hidden("fd_id", $this->id);
		$r.=HtmlInput::hidden("action", "");
		$r.= $this->DisplayAttribut("remove");
		$r.= HtmlInput::submit('add_line_bt', _('Ajoutez cet élément'),
                        'onclick="$(\'action\').value=\'add_line\'"');
		$r.= HtmlInput::submit("save_line_bt", _("Sauvez"),
                        'onclick="$(\'action\').value=\'save_line\'"');
                        
		$r.=HtmlInput::submit('remove_cat_bt', _('Effacer cette catégorie'), 'onclick="$(\'action\').value=\'remove_cat\';return confirm_box(\'input_detail_frm\',\'' . _('Vous confirmez ?') . '\')"');
		// if there is nothing to remove then hide the button
		if (strpos($r, "chk_remove") != 0)
		{
                    $r.=HtmlInput::submit('remove_line_bt', _("Enleve les éléments cochés"), 
                            'onclick="$(\'action\').value=\'remove_line\';return confirm_box(\'input_detail_frm\',\'' . _('Vous confirmez ?') . '\')"');
		}
		$r.= "</form>";
		$r.=" <p class=\"notice\"> " . _("Attention : il n'y aura pas de demande de confirmation pour enlever les
                                   attributs sélectionnés. Il ne sera pas possible de revenir en arrière") . "</p>";
		$r.='</fieldset>';
		return $r;
	}
	function input_new()
	{
		$single=new Tool_Uos("dup");
		echo '<form method="post" style="display:inline">';
		echo $single->hidden();
		echo HtmlInput::hidden("p_action","fiche");
		echo dossier::hidden();
		echo $this->input(); //    CreateCategory($cn,$search);
		echo HtmlInput::submit("add_modele" ,_("Sauve"));
		echo '</FORM>';
	}

}
?>
