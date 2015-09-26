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
 * \brief Html Input
 */
require_once  NOALYSS_INCLUDE.'/class_ipopup.php';
require_once NOALYSS_INCLUDE.'/class_ibutton.php';
require_once NOALYSS_INCLUDE.'/class_ispan.php';
/**
 *@brief let you choose a TVA in a popup
 *@code
    $a=new IPopup('popup_tva');
    $a->set_title('Choix de la tva');
    echo $a->input();
    $tva=new ITva_Popup("tva1");
    $tva->with_button(true);
    // You must add the attributes gDossier, popup
    $tva->set_attribute('popup','popup_tva');
    $tva->set_attribute('gDossier',dossier::id());

    // We can add a label for the code
    $tva->add_label('code');
    $tva->js='onchange="set_tva_label(this);"';
    echo $tva->input();
@endcode
*/
class ITva_Popup extends HtmlInput
{
    /**
     *@brief by default, the p_name is the name/id of the input type
     * the this->button is false (control if a button is visible) and
     * this->in_table=false (return the widget inside a table)
     * this->code is a span widget to display the code (in this case, you will
     * to set this->cn as database connexion)
     * to have its own javascript for the button you can use this->but_javascript)
     * by default it is 'popup_select_tva(this)';
     */
    public function __construct($p_name=null,$p_value="",$p_id="")
    {
        $this->name=$p_name;
        $this->button=true;
        $this->in_table=false;
		$this->value=$p_value;
		$this->id=$p_id;
    }
    function with_button($p)
    {
        if ($p == true )
            $this->button=true;
        else
            $this->button=false;
    }
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null)
    {
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
        $this->js=(isset($this->js))?$this->js:'onchange="format_number(this);"';
		$this->id=($this->id=="")?$this->name:$this->id;

        if ( $this->readOnly==true) return $this->display();

        $str='<input type="TEXT"  class="input_text" name="%s" value="%s" id="%s" size="3" %s>';
        $r=sprintf($str,$this->name,$this->value,$this->id,$this->js);

        if ($this->in_table)
            $table='<table>'.'<tr>'.td($r);

        if ( $this->button==true && ! $this->in_table)
            $r.=$this->dbutton();

        if ( $this->button==true &&  $this->in_table)
            $r=$table.td($this->dbutton()).'</tr></table>';

        if ( isset($this->code))
        {
            if ( $this->cn != NULL)
            {
                /* check if tva_id == integer */
                if (trim($this->value)!='' &&  isNumber($this->value)==1 && strpos($this->value,',') === false)
                    $this->code->value=$this->cn->get_value('select tva_label from tva_rate where tva_id=$1',
                                                            array($this->value));
                ;
            }
            $r.=$this->code->input();
            if ($this->table==1) $r=td($r);
            $this->set_attribute('jcode',$this->code->name);
            $this->set_attribute('gDossier',dossier::id());
            $this->set_attribute('ctl',$this->name);
            $r.=$this->get_js_attr();

        }

        return $r;

    }
    /**
     *@brief show a button, if it is pushed show a popup to select the need vat
     *@note
     * - a ipopup must be created before with the name popup_tva
     * - the javascript scripts.js must be loaded
     *@return string with html code
     */
    function dbutton()
    {
        if( trim($this->name)=='') throw new Exception (_('Le nom ne peut être vide'));
		$this->id=($this->id=="")?$this->name:$this->id;

        // button
        $bt=new ISmallButton('bt_'.$this->id);
		$bt->tabindex="-1";
        $bt->label=_(' TVA ');
        $bt->set_attribute('gDossier',dossier::id());
        $bt->set_attribute('ctl',$this->id);
        $bt->set_attribute('popup','popup_tva');
        if ( isset($this->code))
            $bt->set_attribute('jcode',$this->code->name);
        if ( isset($this->compute))
            $bt->set_attribute('compute',$this->compute);
        $bt->javascript=(isset($this->but_javascript))?$this->but_javascript:'popup_select_tva(this)';
        $r=$bt->input();
        return $r;
    }

    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        $cn=  new Database(Dossier::id());
        $tva=new Acc_Tva($cn, $this->value);
        
        $comment=($tva->load()  != "-1")? $tva->tva_label:"";
	$res=sprintf('<input type="text" name="%s" size="6" class="input_text_ro" value="%s" id="%s" readonly="">%s',$this->name,$this->value,$this->name,$comment);
        return $res;
    }
    /**
     *@brief add a field to show the selected tva's label
     *@param $p_code is the name of the label where you can see the label of VAT
     *@param $p_cn is a database connection if NULL it doesn't seek in the database
     */
    public function add_label($p_code,$p_cn=null)
    {
        $this->cn=$p_cn;
        $this->code=new ISpan($p_code);
    }
    static public function test_me()
    {
        $a=new IPopup('popup_tva');
        $a->set_title('Choix de la tva');
        echo $a->input();
        $tva=new ITva_Popup("tva1");
        $tva->with_button(true);
        // We can add a label for the code
        $tva->add_label('code');
        $tva->js='onchange="set_tva_label(this);"';
        echo $tva->input();
        echo $tva->dbutton();
    }
}
