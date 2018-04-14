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
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
require_once NOALYSS_INCLUDE.'/lib/icon_action.class.php';

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/* ! \file
 * \brief This class is used to create all the HTML INPUT TYPE
 */

/* !
 * \brief class widget This class is used to create all the HTML INPUT TYPE
 *        and some specials which works with javascript like
 *        js_search.
 *
 * special value
 *    js_search and js_search_only :you need to add a span widget the name
 *    of the js_* widget + '_label' , the member extra contains cred,deb to
 *    filter the search of cred of deb of a jrn or contains a string with
 *    a list of frd_id.
 *    Possible type
 *    $type
 *      - TEXT
 *      - HIDDEN
 *      - BUTTON in this->js you have the javascript code
 *      - SELECT the options are passed via this->value, this array is
 *        build thanks the make_array function, each array (of the
 *        array) aka row must contains a field value and a field label
 *      - PASSWORD
 *      - CHECKBOX
 *      - RADIO
 *      - TEXTAREA
 *      - RICHTEXT
 *      - FILE
 *      - SPAN
 */
class HtmlInput
{

    var $type;                      /* !<  $type type of the widget */
    var $name;                      /* !<  $name field NAME of the INPUT */
    var $value;                     /* !<  $value what the INPUT contains */
    var $readOnly;                  /* !<  $readonly true : we cannot change value */
    var $size;                      /* !<  $size size of the input */
    var $selected;                  /* !<  $selected for SELECT RADIO and CHECKBOX the selected value */
    var $table;                     /* !<  $table =1 add the table tag */
    var $label;                     /* !<  $label the question before the input */
    var $disabled;                  /* !<  $disabled poss. value == true or nothing, to disable INPUT */
    var $extra;                     /* !<  $extra different usage, it depends of the $type */
    var $extra2;                    /* !<  $extra2 different usage,
      it depends of the $type */
    var $javascript;                /* !< $javascript  is the javascript to add to the widget */
    var $ctrl;   /* !<$ctrl is the control to update (see js_search_card_control) */
    var $tabindex;

    function __construct($p_name="", $p_value="", $p_id="")
    {
        $this->name=$p_name;
        $this->readOnly=false;
        $this->size=20;
        $this->width=50;
        $this->heigh=20;
        $this->value=$p_value;
        $this->selected="";
        $this->table=0;
        $this->disabled=false;
        $this->javascript="";
        $this->extra="";
        $this->extra2="all";
        $this->attribute=array();
        $this->id=$p_id;
    }

    function setReadOnly($p_read)
    {
        $this->readOnly=$p_read;
    }

    /* !\brief set the extra javascript property for the INPUT field
     * \param $p_name name of the parameter
     * \param $p_value default value of this parameter
     */

    public function set_attribute($p_name, $p_value)
    {
        $this->attribute[]=array($p_name, $p_value);
        $this->$p_name=$p_value;
    }

    /**
     * Set the value of input (IText, INum,...)
     * @param type $p_string
     */
    function set_value($p_string)
    {
        $this->value=$p_string;
    }

    /**
     * Return the value of input (IText, INum,...)
     */
    function get_value()
    {
        return $this->value;
    }

    /**
     * @brief you can add attribute to this in javascript
     * this function is a wrapper and create a script (in js) to modify
     * "this" (in javascript) with the value of obj->attribute from PHP
     * @return return string with the javascript code
     */
    public function get_js_attr()
    {
        require_once NOALYSS_INCLUDE.'/lib/function_javascript.php';
        $attr="";
        if (count($this->attribute)==0)
            return "";

        /* Add properties at the widget */
        for ($i=0; $i<count($this->attribute); $i++)
        {
            list($name, $value)=$this->attribute[$i];
            $tmp1=sprintf("$('%s').%s='%s';", $this->name, $name, $value);
            $attr.=$tmp1;
        }
        $attr=create_script($attr);
        return $attr;
    }

    /**
     * Make a JSON object, this method create a javascript object
     * with the attribute set, it returns a javascript string with the object
     * @param $p_name : name of the object, can be null. If the name is not null, return
     * $p_name={} otherwise only the object {}
     * @return javascript string with the object
     * @note: there is not check on the key->value, so you could need to escape
     * special char as quote, single-quote...
     * @deprecated 
     * @code
      $a=new IButton()
      $a->set_attribute('prop','1');
      $a->set_attribute('prop','2');
      $a->set_attribute('prop','3');
      $string = $a->make_object('property');
      echo $string => property={'prop':'1','prop2':'2','prop3':'3'};
      $string = $a->make_object(null);
      echo $string => {'prop':'1','prop2':'2','prop3':'3'};
      @endcode
     */
    public function make_object_deprecated($p_name=null)
    {
        $name=($p_name!=null)?$p_name.'=':'';
        if (count($this->attribute)==0)
            return $name."{}";
        $ret=$name."{";
        $and='';

        for ($i=0; $i<count($this->attribute); $i++)
        {
            list($name, $value)=$this->attribute[$i];
            $tmp1=sprintf($and."'%s':'%s'", $name, $value);
            $ret.=$tmp1;
            $and=',';
        }

        $ret.='}';
        return $ret;
    }

    //#####################################################################
    /* Debug
     */
    function debug()
    {
        echo "Type ".$this->type."<br>";
        echo "name ".$this->name."<br>";
        echo "value".$this->value."<br>";
        $readonly=($this->readonly==false)?"false":"true";
        echo "read only".$readonly."<br>";
    }

    static function submit($p_name, $p_value, $p_javascript="",
            $p_class="smallbutton")
    {

        return '<INPUT TYPE="SUBMIT" class="'.$p_class.'" NAME="'.$p_name.'" ID="'.$p_name.'_submit_id"  VALUE="'.$p_value.'" '.$p_javascript.'>';
    }

    static function button($p_name, $p_value, $p_javascript="",
            $p_class="smallbutton")
    {

        return '<INPUT TYPE="button" class="'.$p_class.'" NAME="'.$p_name.'" ID="'.$p_name.'" VALUE="'.$p_value.'" '.$p_javascript.'>';
    }

    static function reset($p_value)
    {
        return '<INPUT TYPE="RESET" class="smallbutton" VALUE="'.$p_value.'">';
    }

    static function hidden($p_name, $p_value, $p_id="")
    {
        if ($p_id=="")
            $p_id=$p_name;
        return '<INPUT TYPE="hidden" id="'.$p_id.'" NAME="'.$p_name.'" VALUE="'.$p_value.'">';
    }

    static function extension()
    {
        return self::hidden('plugin_code', $_REQUEST['plugin_code']);
    }

    /* !\brief create a button with a ref
     * \param $p_label the text
     * \param $p_value the location of the window,
     * \param $p_name the id of the span
     * \param $p_javascript javascript for this button
     * \return string with htmlcode
     */

    static function button_anchor($p_label, $p_value, $p_name="",
            $p_javascript="", $p_class="button")
    {
        $href="";
        if ($p_value!="")
            $href=sprintf('  href ="%s"  ', $p_value);
        $r=sprintf('<span id="%s" > <A class="'.$p_class.'" style="display:inline;"  %s %s >%s</A></span>',
                $p_name, $href, $p_javascript, $p_label);
        return $r;
    }
    /**
     * @deprecated since version 7.0
     * @see Icon_Action::warnbulle
     * @param type $p_comment
     * @return string
     */
    static function warnbulle($p_comment)
    {
        throw new Exception("DEPRECATED");
    }

    static function errorbulle($p_comment)
    {
        global $g_failed;
        $r=sprintf('<A HREF="#" tabindex="-1" style="display:inline;color:red;background-color:transparent;padding-left:4px;padding-right:4px;text-decoration:none;" onmouseover="displayBulle(\'%s\')"  onclick="displayBulle(\'%s\')" onmouseout="hideBulle(0)"> %s </A>',
                $p_comment, $p_comment, $g_failed);
        return $r;
    }

    /**
     * return a string containing the html code for calling the modifyOperation
     */
    static function detail_op($p_jr_id, $p_mesg)
    {
        return sprintf('<A class="detail" style="text-decoration:underline;display:inline" HREF="javascript:modifyOperation(%d,%d)">%s</A>',
                $p_jr_id, dossier::id(), $p_mesg);
    }

    /**
     * @brief return an anchor to view the detail of an action
     * @param $ag_id
     * @param $p_mesg
     * @param $p_modify let you modify an operation
     *
     */
    static function detail_action($ag_id, $p_mesg, $p_modify=1)
    {
        return sprintf('<A class="detail" style="text-decoration:underline;display:inline" HREF="javascript:view_action(%d,%d,%d)">%s</A>',
                $ag_id, dossier::id(), $p_modify, $p_mesg);
    }

    /**
     * return a string containing the html code for calling the modifyModeleDocument
     */
    static function detail_modele_document($p_id, $p_mesg)
    {
        return sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:modifyModeleDocument(%d,%d)">%s</A>',
                $p_id, dossier::id(), $p_mesg);
    }

    /**
     * return a string containing the html code for calling the removeStock
     */
    static function remove_stock($p_id, $p_mesg)
    {
        return sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:removeStock(%d,%d)">%s</A>',
                $p_id, dossier::id(), $p_mesg);
    }

    /**
     * display a div with the history of the card
     */
    static function history_card($f_id, $p_mesg, $p_style="",$p_exercice="")
    {
        global $g_user;
        if ( $p_exercice=="") {
            $p_exercice=$g_user->get_exercice();
        }
        $view_history=sprintf('<A class="detail"  style="text-decoration:underline;%s" HREF="javascript:view_history_card(\'%s\',\'%s\',\'%s\')" >%s</A>',
                $p_style, $f_id, dossier::id(),$p_exercice, $p_mesg);
        return $view_history;
    }

    /**
     * display a div with the history of the card
     * @param int $f_id fiche.f_id
     * @param string $p_mesg string to display
     * @param int $p_exercice exercice of the history
     */
    static function history_card_button($f_id, $p_mesg,$p_exercice="")
    {
        static $e=0;
        $e++;
         global $g_user;
        if ( $p_exercice=="") {
            $p_exercice=$g_user->get_exercice();
        }
        $js=sprintf('onclick="view_history_card(\'%s\',\'%s\',\'%s\')"', $f_id,
                dossier::id(),$p_exercice);
        $view_history=HtmlInput::button("hcb".$e, $p_mesg, $js);
        return $view_history;
    }

    /**
     * display a div with the history of the account
     * @param string $p_account accounting
     * @param string $p_mesg string to display
     * @param string $p_style extra code for HTML
     * @param int $p_exercice exercice of the history
     */
    static function history_account($p_account, $p_mesg, $p_style="",$p_exercice="")
    {
        global $g_user;
        if ( $p_exercice=="") {
            $p_exercice=$g_user->get_exercice();
        }
        $view_history=sprintf('<A class="detail" style="text-decoration:underline;%s" HREF="javascript:view_history_account(\'%s\',\'%s\',\'%s\')" >%s</A>',
                $p_style, $p_account, dossier::id(),$p_exercice, $p_mesg);
        return $view_history;
    }
    /**
     * display a div with the history of the analytic account
     * @param int $p_account  po_id 
     * @param string $p_mesg string to display
     * @param string $p_style extra code for HTML
     * @param int $p_exercice exercice of the history
     */
    static function history_anc_account($p_account, $p_mesg, $p_style="",$p_exercice="")
    {
        global $g_user;
        if ( $p_exercice=="") {
            $p_exercice=$g_user->get_exercice();
        }
        $view_history=sprintf('<A class="detail" style="text-decoration:underline;%s" HREF="javascript:view_history_anc_account(\'%s\',\'%s\',\'%s\')" >%s</A>',
                $p_style, $p_account, dossier::id(),$p_exercice, $p_mesg);
        return $view_history;
    }

    /**
     * create a hidden plus button to select the cat of ledger
     * @note the selected value is stored in the array p_cat
     */
    static function select_cat($array_cat)
    {
        ob_start();
        $ledger=new ISmallButton('l');
        $ledger->label=_("Catégorie");
        $ledger->javascript=" show_cat_choice()";
        echo $ledger->input();

        /* create a hidden div for the ledger */
        echo '<div id="div_cat">';
        echo '<h2 class="info">'._('Choix des categories').'</h2>';
        $selected=(isset($_GET['r_cat']))?$_GET['r_cat']:null;

        echo '<ul>';
        for ($e=0; $e<count($array_cat); $e++)
        {
            $row=$array_cat[$e];
            $re=new ICheckBox('r_cat['.$e.']', $row['cat']);

            if ($selected!=null&&isset($selected[$e]))
            {
                $re->selected=true;
            }
            echo '<li style="list-style-type: none;">'.$re->input().$row['name'].'('.$row['cat'].')</li>';
        }
        echo '</ul>';
        $hide=new IButton('l2');
        $hide->label=_("Valider");
        $hide->javascript=" hide_cat_choice() ";
        echo $hide->input();

        echo '</div>';
        $r=ob_get_contents();
        ob_end_clean();
        return $r;
    }

    static function display_periode($p_id)
    {
        $r=sprintf('<a href="javascript:void(0)" onclick="display_periode(%d,%d)">Modifier</a>',
                dossier::id(), $p_id);
        return $r;
    }

    /**
     * close button for the HTML popup
     * @see add_div modify_operation
     * @param $div_name is the name of the div to remove
     */
    static function button_close($div_name)
    {
        $a=new IButton('Fermer');
        $a->label=_("Fermer");
        $a->javascript="removeDiv('".$div_name."')";
        $html=$a->input();

        return $html;
    }

    /**
     * @brief Hide the HTML popup
     * @param type $div_name
     * @return type
     */
    static function button_hide($div_name)
    {
        $a=new IButton('Fermer');
        $a->label=_("Fermer");
        $a->javascript="$('".$div_name."').hide()";
        $html=$a->input();

        return $html;
    }

    /**
     * Return a html string with an anchor which close the inside popup. (top-right corner)
     * @param name of the DIV to close
     * @deprecated 
     * @see Icon_Action::close
     */
    static function anchor_close($div, $p_js="")
    {
        throw new Exception("DEPRECATED");
    }

    /**
     * Anchor Html with javascript
     * @param $action action action to perform (message) without onclick
     * @param $javascript javascript to execute
     * @param $id is the DOM element id 
     * @param $p_class CSS class of the button
     * @param $p_symbole raw symbole to add to the action message
     */
    static function anchor_action($action, $javascript, $id=NULL,
            $p_class="button", $p_symbole="")
    {
        if ($id==NULL)
        {
            $id=uniqid("xx");
        }
        $r="";
        $r.='<a  id="'.$id.'" class="'.$p_class.'" onclick="'.$javascript.'">'.$p_symbole.h($action).'</a>';
        return $r;
    }

    /**
     * button Html with javascript
     * @param $action action action to perform (message) without onclick
     * @param $javascript javascript to execute
     * @param $id is the DOM element id 
     * @param $p_class CSS class of the button
     * @param $p_symbole raw symbole to add to the action message
     */
    static function button_action($action, $javascript, $id=NULL,
            $p_class="button", $p_symbole="")
    {
        if ($id==NULL)
        {
            $id=uniqid("xx");
        }
        $r="";
        $r.='<input type="button" id="'.$id.'" class="'.$p_class.'" onclick="'.$javascript.'" value="'.$p_symbole.h($action).'">';
        return $r;
    }

    /**
     * Image to click , 
     * @param string $p_image filename of the image under image/
     * @param string $p_js javascript when the image is clicked
     * @param string $p_message Message 
     */
    static function image_click($p_image, $p_js, $p_message, $p_class="")
    {
        $ret=sprintf('<a class="nav %s" style="display:inline" title="%s"><img src="image/%s" onclick="%s"></a>',
                $p_class, $p_message, $p_image, $p_js);
        return $ret;
    }

    /**
     * button Html image
     * @param $javascript javascript to execute
     * @param $id id of the button
     * @param  $class class of the button
     * @param $p_image image
     */
    static function button_image($javascript, $id="xx",
            $p_class='class="button"', $p_image="")
    {
        if ($id=="xx")
        {
            $id=uniqid("xx");
        }
        $r="";
        $r.='<image id="'.$id.'" '.$p_class.' onclick="'.$javascript.'"  src="'.$p_image.'" />';
        return $r;
    }

    /**
     * Return a html string with an anchor to hide a div, put it in the right corner
     * @param $action action action to perform (message)
     * @param $javascript javascript
     * @note not protected against html
     * @see Icon_Action::hide
     * @deprecated
     * 
     */
    static function anchor_hide($action, $javascript)
    {
        throw new Exception("DEPRECATED");
    }

    /**
     * Javascript to print the current window
     */
    static function print_window()
    {
        $r='';
        $r.=HtmlInput::button('print', 'Imprimer', 'onclick="window.print();"');
        return $r;
    }

    /**
     * show the detail of a card
     */
    static function card_detail($p_qcode, $pname='', $p_style="",
            $p_nohistory=false)
    {
        //if ($pname=='')$pname=$p_qcode;
        $r="";
        $histo=($p_nohistory==true)?' ,nohistory:1':"";
        $r.=sprintf('<a href="javascript:void(0)" %s onclick="fill_ipopcard({qcode:\'%s\' %s})">%s [%s]</a>',
                $p_style, $p_qcode, $histo, $pname, $p_qcode);
        return $r;
    }

    /**
     * transform request data  to hidden
     * @param $array is an of indices
     * @param $request name of the superglobal $_POST $_GET $_REQUEST(default)
     * @return html string with the hidden data
     */
    static function array_to_hidden($array, $global_array)
    {

        $r="";

        if (count($global_array)==0)
            return '';
        foreach ($array as $a)
        {
            if (isset($global_array [$a]))
                if (is_array($global_array[$a])==false)
                {
                    $r.=HtmlInput::hidden($a, $global_array [$a]);
                }
                else
                {
                    if (count($global_array[$a])>0)
                    {
                        foreach ($global_array[$a] as $value)
                        {
                            $r.=HtmlInput::hidden($a."[]", $value);
                        }
                    }
                }
        }

        return $r;
    }

    /**
     * @brief transform a json to hidden 
     * @param json $p_json
     */
    static function json_to_hidden($p_json)
    {
        $aJson=json_decode($p_json);
        foreach ($aJson as $key=> $value)
        {
            echo HtmlInput::hidden($key, $value);
        }
    }

    /**
     * transform $_GET   data  to hidden
     * @param $array is an of indices
     * @see HtmlInput::request_to_hidden
     * @return html string with the hidden data
     */
    static function get_to_hidden($array)
    {
        $r=self::array_to_hidden($array, $_GET);
        return $r;
    }

    /**
     * transform $_POST  data  to hidden
     * @param $array is an of indices
     * @see HtmlInput::request_to_hidden
     * @return html string with the hidden data
     */
    static function post_to_hidden($array)
    {
        $r=self::array_to_hidden($array, $_POST);
        return $r;
    }

    /**
     * transform $_REQUEST   data  to hidden
     * @param $array is an of indices
     * @see HtmlInput::request_to_hidden
     * @return html string with the hidden data
     */
    static function request_to_hidden($array)
    {
        $r=self::array_to_hidden($array, $_REQUEST);
        return $r;
    }

    /**
     * transform request data  to string
     * @param $array is an of indices
     * @param $request name of the superglobal $_POST $_GET $_REQUEST(default)
     * @return html string with the string data
     */
    static function array_to_string($array, $global_array, $start="?")
    {

        $r=$start;

        if (count($global_array)==0)
            return '';
        $and="";
        foreach ($array as $a)
        {
            if (isset($global_array [$a]))
            {
                if (is_array($global_array[$a])==false)
                {
                    $r.=$and."$a=".$global_array [$a];
                }
                else
                {
                    for ($i=0; $i<count($global_array[$a]); $i++)
                    {
                        $r.=$and."$a"."[]=".$global_array[$a][$i];
                        $and="&amp;";
                    }
                }
            }
            $and="&amp;";
        }

        return $r;
    }

    /**
     * transform $_GET   data  to string
     * @param $array is an of indices
     * @see HtmlInput::request_to_string
     * @return html string with the string data
     */
    static function get_to_string($array, $start="?")
    {
        $r=self::array_to_string($array, $_GET, $start);
        return $r;
    }

    /**
     * transform $_POST  data  to string
     * @param $array is an of indices
     * @see HtmlInput::request_to_string
     * @return html string with the string data
     */
    static function post_to_string($array)
    {
        $r=self::array_to_string($array, $_POST);
        return $r;
    }

    /**
     * transform $_REQUEST   data  to string
     * @param $array is an of indices
     * @see HtmlInput::request_to_string
     * @return html string with the string data
     */
    static function request_to_string($array, $start="?")
    {
        $r=self::array_to_string($array, $_REQUEST, $start);
        return $r;
    }

    /**
     * generate an unique id for a widget,
     * @param $p_prefix prefix
     * @see HtmlInput::IDate
     * @return string with a unique id
     */
    static function generate_id($p_prefix)
    {
        $r=sprintf('%s_%d', $p_prefix, mt_rand(0, 999999));
        return $r;
    }

    /**
     * return default if the value if the value doesn't exist in the array
     * @param $ind the index to check
     * @param $default the value to return
     * @param $array the array
     */
    static function default_value($ind, $default, $array)
    {
        if (!isset($array[$ind]))
        {
            return $default;
        }
        return $array[$ind];
    }

    /**
     *  return default if the value if the value doesn't exist in $_GET
     * use HttpInput instead
     * @see HttpInput
     * @deprecated 
     * @param  $ind name of the variable
     * @param type $default
     * @return type
     */
    static function default_value_get($ind, $default)
    {
        if (!isset($_GET[$ind]))
        {
            return $default;
        }
        return $_GET[$ind];
    }

    /**
     *  return default if the value if the value doesn't exist in $_POST
     * use HttpInput instead
     * @see HttpInput
     * @deprecated 
     * @param  $ind name of the variable
     * @param type $default
     * @return type
     */
    static function default_value_post($ind, $default)
    {
        if (!isset($_POST[$ind]))
        {
            return $default;
        }
        return $_POST[$ind];
    }

    /**
     *  return default if the value if the value doesn't exist in $_REQUEST
     * use HttpInput instead
     * @see HttpInput
     * @deprecated 
     * @param  $ind name of the variable
     * @param type $default
     * @return type
     */
    static function default_value_request($ind, $default)
    {
        if (!isset($_REQUEST[$ind]))
        {
            return $default;
        }
        return $_REQUEST[$ind];
    }

    /**
     * Title for boxes, you can customize the symbol thanks symbol with
     * the mode "custom"
     * @param type $name Title
     * @param type $div element id, except for mode none or custom
     * @param type $mod hide , close , zoom , custom or none, with
     * custom , the $name contains all the code
     * @param type $p_js contains the javascript if mod = "custom" or "zoom" contains button + code 
     * @param type $p_draggable , if set to yes the box will be draggable
     * @return type
     */
    static function title_box($p_name, $p_div, $p_mod="close", $p_js="",
            $p_draggable="n")
    {
        $r='<div class="bxbutton">';

        // If draggable : display a icon to unpin and move the dialog box
        if ($p_draggable=="y")
        {
            $r.=Icon_Action::draggable($p_div);
        }
        if ($p_mod=='close')
        {
            $r.=Icon_Action::close($p_div, $p_js);
        }
        elseif ($p_mod=='zoom')
        {
            $r.=Icon_Action::zoom($p_div,$p_js);
        }
        elseif ($p_mod=='hide')
        {
            $r.=Icon_Action::hide("&#10761;", "$('$p_div').hide();$p_js");
        }
        elseif ($p_mod=='custom')
        {
            $r.='<span  id="span_'.$p_div.'" style="float:right;margin-right:5px">'.$p_js."</span>";
        }
        elseif ($p_mod=='none')
        {
            $r.="";
        }
        else
            die(__FILE__.":".__LINE__._('Paramètre invaide'));


        $r.='</div>';
        $r.=h2($p_name, ' class="title" ');
   
        return $r;
    }

    /**
     * @brief let you create only a link and set an id on it.
     * After create a javascript for getting the event 
     * onclick = function() {...}
     * @param type $p_text Text to display
     * @param type $p_id id of the link
     * @param type $type title of the link
     * @code
     * echo HtmlInput::anchor_empty('go','go_id');
     * <script>$("go_id").onclick=function (e) { ...}</script>
     * @endcode
     */
    static function anchor_empty($p_text, $p_id, $p_title="")
    {
        $p_url="javascript:void(0)";
        $str=sprintf('<a id="%s" href="javascript:void(0)" class="line" title="%s">%s</a>',
                $p_id, $p_title, $p_text);
        return $str;
    }

    /**
     * Return a simple anchor with a url or a javascript
     * if $p_js is not null then p_url will be javascript:void(0)
     * we don't add the event onclick. You must give p_url OR p_js
     * default CSS class=line
     * @param string $p_text text of the anchor
     * @param string $p_url  url
     * @param string $p_js javascript
     * @param string $p_style is the visuable effect (class, style...)
     */
    static function anchor($p_text, $p_url="", $p_js="",
            $p_style=' class="line" ')
    {
        if ($p_js!="")
        {
            $p_url="javascript:void(0)";
          } else {
              $p_url=sprintf('href="%s"',$p_url);
        }

        $str=sprintf('<a %s %s %s>%s</a>', $p_style, $p_url, $p_js, $p_text);
        return $str;
    }

    /**
     * Create an ISelect object containing the available repository for reading
     * or writing
     * @remark $g_user global.
     *
     * @param $p_cn db object
     * @param $p_name name of the select
     * @param $p_mode is 'R' for reading, 'W' for writinh
     * @return ISelect
     * @throws Exception if p_mode is wrong
     */
    static function select_stock($p_cn, $p_name, $p_mode)
    {
        global $g_user;
        if (!in_array($p_mode, array('R', 'W')))
        {
            throw new Exception(__FILE__.":".__LINE__." $p_mode invalide");
        }
        $profile=$g_user->get_profile();
        $sel=new ISelect($p_name);

        if ($p_mode=='W')
        {
            $sel->value=$p_cn->make_array("
                select r_id,r_name
				  from stock_repository join profile_sec_repository using (r_id)
                where
                 ur_right='W' and  p_id=".sql_string($profile).
                    " order by 2");
            return $sel;
        }
        if ($p_mode=='R')
        {
            $sel->value=$p_cn->make_array("
                select r_id,r_name
				  from stock_repository join profile_sec_repository using (r_id)
                where
                  p_id=".sql_string($profile).
                    " order by 2");
            return $sel;
        }
    }

    static function filter_table_form($p_table_id, $p_col, $start_row, $p_name,
            $p_old_value)
    {
        $r="
			<span>
			<input id=\"lk_".$p_table_id."\" name=\"$p_name\" value=\"$p_old_value\"autocomplete=\"off\" class=\"input_text\" name=\"filter\" onkeyup=\"filter_table(this, '$p_table_id','$p_col',$start_row )\" type=\"text\">
			<input type=\"button\" class=\"smallbutton\" onclick=\"$('lk_".$p_table_id."').value='';filter_table($('lk_".$p_table_id."'), '$p_table_id','$p_col',$start_row );\" value=\"X\">
			</span>
			";
        $r.=' <span class="notice" id="info_'.$p_table_id.'"></span>';
        return $r;
    }

    static function filter_table($p_table_id, $p_col, $start_row)
    {
        $r="
			<span>
			<input id=\"lk_".$p_table_id."\" autocomplete=\"off\" class=\"input_text\" name=\"filter\" onkeyup=\"filter_table(this, '$p_table_id','$p_col',$start_row )\" type=\"text\">
			<input type=\"button\" class=\"smallbutton\" onclick=\"$('lk_".$p_table_id."').value='';filter_table($('lk_".$p_table_id."'), '$p_table_id','$p_col',$start_row );\" value=\"X\">
			</span>
			";
        $r.=' <span class="notice" id="info_'.$p_table_id.'"></span>';
        return $r;
    }

    static function show_reconcile($p_div, $let, $span="")
    {
        $r='<A  style="color:red;text-decoration:underline" href="javascript:void(0)" onclick="show_reconcile(\''.$p_div.'\',\''.$let.'\')">'.$let.$span.'</A>';
        return $r;
    }

    /**
     * Zoom the calendar
     * @param type $obj objet json for the javascript
     * @see calendar_zoom in scripts.js 
     */
    static function calendar_zoom($obj)
    {
        $button=new ISmallButton("calendar", _("Calendrier"));
        $button->javascript="calendar_zoom($obj)";
        return $button->input();
    }

    /**
     * 
     * @param type $p_array indice
     *   - div div name
     *   - type ALL, VEN, ACH or ODS
     *   - all_type 1 yes 0 no
     * 
     */
    static function button_choice_ledger($p_array)
    {
        extract($p_array, EXTR_SKIP);
        $bledger_param=json_encode(array(
            'dossier'=>$_REQUEST['gDossier'],
            'type'=>$type,
            'all_type'=>$all_type,
            'div'=>$div,
            'class'=>'inner_box'
        ));

        $bledger_param=str_replace('"', "'", $bledger_param);
        $bledger=new ISmallButton('l');
        $bledger->label=_("choix des journaux");
        $bledger->javascript=" show_ledger_choice($bledger_param)";
        $f_ledger=$bledger->input();
        $hid_jrn="";
        if (isset($_REQUEST[$div.'nb_jrn']))
        {
            for ($i=0; $i<$_REQUEST[$div.'nb_jrn']; $i++)
            {
                if (isset($_REQUEST[$div."r_jrn"][$i]))
                    $hid_jrn.=HtmlInput::hidden($div.'r_jrn['.$i.']',
                                    $_REQUEST[$div."r_jrn"][$i]);
            }
            $hid_jrn.=HtmlInput::hidden($div.'nb_jrn',
                            $_REQUEST[$div.'nb_jrn']);
        } else
        {
            $hid_jrn=HtmlInput::hidden($div.'nb_jrn', 0);
        }
        echo $f_ledger;
        echo '<span id="ledger_id'.$div.'">';
        echo $hid_jrn;
        echo '</span>';
    }

    /**
     * @brief Returns HTML code for displaying a icon with a link to a receipt document from
     * the ledger 
     * @param $p_jr_id jrn.jr_id
     * @param $p_name name in the link , if the name is empty then we show the icon
     * @return nothing or HTML Code for a link to the document
     */
    static function show_receipt_document($p_jr_id, $p_name="")
    {
        global $cn;
        $image=$p_name;

        // Check the jr_id has a receipt document
        $array=$cn->get_array('select jr_def_id,jr_pj_name,jr_grpt_id from jrn where jr_id=$1',
                array($p_jr_id));
        if (count($array)==0)
            return "";
        if ($array[0]['jr_pj_name']=="")
            return "";
        $str_dossier=Dossier::get();

        // Name is empty then use an image
        if ($p_name=="")
        {
            $image='<IMG style="width:24px;height:24px;border:0px" SRC="image/documents.png" title="'.h($array[0]['jr_pj_name']).'" >';
        }

        // Build the url
        $href=http_build_query(array('gDossier'=>Dossier::id(), 'jr_id'=>$p_jr_id,
            'act'=>'RAW:receipt'));

        $r=sprintf('<A class="mtitle line" HREF="export.php?%s">%s</A>', $href,
                $image);
        return $r;
    }

    /**
     * 
     * @param type $p_operation_jr_id action_gestion_operation.ago_id
     */
    static function button_action_remove_operation($p_operation)
    {
        $rmOperation=sprintf("javascript:confirm_box(null,'"._('Voulez-vous effacer cette relation ')."',function ()  {remove_operation('%s','%s');});",
                dossier::id(), $p_operation);
        $js='<a class="tinybutton" id="acop'.$p_operation.'" href="javascript:void(0)" onclick="'.$rmOperation.'">'.SMALLX.'</a>';
        return $js;
    }

    static function button_action_add_concerned_card($p_agid)
    {
        $dossier=Dossier::id();
        $javascript=<<<EOF
                    obj={dossier:$dossier,ag_id:$p_agid};action_add_concerned_card(obj);
EOF;
        $js=HtmlInput::button_action(_('Ajout autres'), $javascript, 'xx',
                        'smallbutton');
        return $js;
    }

    static function button_action_add()
    {
        $dossier=Dossier::id();
        $js=HtmlInput::button_action(_('Nouvel événement'),
                        'action_add('.$dossier.')', 'xx', 'smallbutton');
        return $js;
    }

    /**
     * Insert attribute inside a INPUT TYPE, these attribute can be retrieved
     * in javascript with element.getAttribute or changed with element.setAttribute
     * example insert my_attribute into a checkbox <input type="checkbox" "my_attribute"="XX"> 
     * @return string to insert into the HTML node
     * 
     */
    function get_node_attribute()
    {
        $r="";
        $nb_attribute=count($this->attribute);
        for ($i=0; $i<$nb_attribute; $i++)
        {
            $r.=sprintf(' %s="%s" ', $this->attribute[$i][0],
                    $this->attribute[$i][1]);
        }
        return $r;
    }

}
