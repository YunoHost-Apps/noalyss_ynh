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
 *
 */
require_once NOALYSS_INCLUDE.'/class_html_input.php';
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_ibutton.php';
require_once NOALYSS_INCLUDE.'/class_ipopup.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';
/**
 *@brief show a button, for selecting a account and a input text for manually inserting an account
 * the different value of table are
 * - 0 no table, it means no TD tags
 * - 1 the button and the text are separated by TD tags
 * - 2 the button and the text are in the same column (TD)
 * - 3 the button and the text are in the table (TD)
 *\note we use the set_attribute for giving parameter to search_account
 * attribute are
 *  - gDossier
 *  - jrn  if set there is a filter on a ledger, in  that case, contains the jrn_id (0 for no filter)
 *  - account field to update with the account_number,
 *  - label  field to update  control with account_label,
 *  - bracket if true return the account_number between bracket
 *  - noquery don't start a search with the content
 *  - no_overwrite do not overwrite the existant content
 *  - query value to seek
 *@note needed javascript are
 - echo js_include('prototype.js');
 - echo js_include('scriptaculous.js');
 - echo js_include('effects.js');
 - echo js_include('controls.js');
 - echo js_include('dragdrop.js');
 - echo js_include('accounting_item.js');
 *\see ajax_poste.php
 *\code
// must be done BEFORE any FORM
 echo js_include('prototype.js');
 echo js_include('scriptaculous.js');
 echo js_include('effects.js');
 echo js_include('controls.js');
 echo js_include('dragdrop.js');
 echo js_include('accounting_item.js');


require_once NOALYSS_INCLUDE.'/class_iposte.php';

// In the FORM
$text=new IPoste();
$text->name('field');
$text->value=$p_res[$i]['pvalue'];
$text->set_attribute('gDossier',Dossier::id());
$text->set_attribute('jrn',0);
$text->set_attribute('account','field');


\endcode
 */
class IPoste extends HtmlInput
{

    function __construct($p_name="",$p_value="",$p_id="")
    {
        $this->name=$p_name;
        $this->readOnly=false;
        $this->size=10;
        $this->value=$p_value;
        $this->selected="";
        $this->table=0;
        $this->disabled=false;
        $this->javascript="";
        $this->extra2="all";
        $this->attribute=array();
	$this->id=$p_id;
       

    }

    static function ipopup($p_name)
    {
        $ip=new IPopup($p_name);
        $ip->title='Plan comptable';
        $ip->value='';
        $ip->set_height('80%');
        $ip->set_zindex(20);
        return $ip->input();
    }
    /*!\brief create the javascript for adding the javascript properties
     * onto the *button*
     *\return a javascript surrounded by the tag <SCRIPT>
     */
    public function get_js_attr()
    {
        $attr="";
        /* Add properties at the widget */
        for ($i=0;$i< count($this->attribute);$i++)
        {
            list($name,$value)=$this->attribute[$i];
            $tmp1=sprintf("$('%s_bt').%s='%s';",
                          $this->id,
                          $name,
                          $value);
            $attr.=$tmp1;
        }
        $attr=create_script($attr);
        return $attr;
    }

    public function dsp_button()
    {
		$this->id=($this->id=="")?$this->name:$this->id;
        $javascript='search_poste(this)';
        $button=HtmlInput::button_image($javascript,$this->id."_bt", 'alt="'._('Recherche').'" class="image_search"',"image/magnifier13.png");
        /*  add the property */
        $sc=$this->get_js_attr();
        return $button.$sc;
    }
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null)
    {
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
        if ( $this->readOnly==true) return $this->display();
        //--
        if ( ! isset($this->ctrl) ) $this->ctrl='none';

        if ( ! isset($this->javascript)) $this->javascript="";
		$this->id=($this->id=="")?$this->name:$this->id;

        /* create the text  */
        $itext=new IText($this->name,$this->value,$this->id);

	if ( isset ($this->css_size))
	     $itext->css_size=$this->css_size;
	else
	     $itext->size=$this->size;

		 $itext->javascript=$this->javascript;
        /* create the button */
        $ibutton=$this->dsp_button();
        if ( $this->table==3)
        {
            $r='<table>'.tr(td($itext->input()).td($ibutton));
            $r.='</table>';
            return $r;
        }
        $r=$itext->input().$ibutton;
        if ( $this->table==1) $r=td($r);

        return $r;


        //--

    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        $r=sprintf('<TD><input type="hidden" name="%s" value="%s">
                   %s

                   </TD>',
                   $this->name,
                   $this->value ,
                   $this->value
                  );

        return $r;

    }
	/**
	 *add a double click to poste to see his history
	 *@note change $this->javascript
	 */
	public function dbl_click_history()
	{
		$r=' ondblclick="get_history_account(\''.$this->name.'\',\''.dossier::id().'\')"';
		$this->javascript=$r;
	}
    static public function test_me()
    {
    }
}
