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
 * \brief Input HTML for the card show buttons
 *
 */

/*!
 * \brief
*/
require_once NOALYSS_INCLUDE.'/class_html_input.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';

class IAncCard extends HtmlInput
{
    function __construct($name="",$value="",$p_id="")
    {
        parent::__construct($name,$value,$p_id);
        $this->fct='update_value';
        $this->dblclick='';
        $this->callback='null';
        $this->javascript='';
        // the pa_id to filter
        $this->plan=0;
        // or the container of the Plan Analytic which contains the pa_id
        $this->plan_ctl="";
    }
    /*!\brief set the javascript callback function
     * by default it is update_value called BEFORE the querystring is send
     * If you use the plan ctl must be set to filter_anc
     *\param $p_name callback function name
     */
    function set_callback($p_name)
    {
        $this->callback=$p_name;
    }

    /*!\brief set the javascript callback function
     * by default it is update_value called AFTER an item has been selected
     *\param $p_name callback function name
     */
    function set_function($p_name)
    {
        $this->fct=$p_name;
    }

    /*!\brief set the extra javascript property for a double click on
     *  INPUT field
     *\param $p_action action when a double click happens
     *\note the $p_action cannot contain a double quote
     */
    function set_dblclick($p_action)
    {
        $this->dblclick=$p_action;
    }
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null)
    {
        if ( $p_name == null && $this->name == "")
            throw (new Exception('Le nom d une icard doit être donne'));

        $this->value=($p_value==null)?$this->value:$p_value;
        if ( $this->readOnly==true) return $this->display();

        $this->id=($this->id=="")?$this->name:$this->id;



        $label='';
        if ( $this->dblclick != '')
        {
            $e=sprintf(' ondblclick="%s" ',
                       $this->dblclick);
            $this->dblclick=$e;
        }
        $input=sprintf('<INPUT TYPE="Text"  class="input_text"  '.
                       ' NAME="%s" ID="%s" VALUE="%s" SIZE="%d" %s %s>',
                       $this->name,
                       $this->name,
                       $this->value,
                       $this->size,
                       $this->dblclick,
                       $this->javascript
                      );


        $div=sprintf('<div id="%s_choices"  class="autocomplete"></div>',
                     $this->name);
        $query="op=autoanc&".dossier::get();

        // add parameter to search into a plan (pa_id) or get the value from
        // a HtmlObject
        if ($this->plan <> 0)
        {
            $query.="&pa_id=".$this->plan;
        } elseif ( $this->plan_ctl <> '')
        {
               $this->set_attribute("plan_ctl", $this->plan_ctl);
        }
        $attr=$this->get_js_attr();
        $javascript=sprintf('try { new Ajax.Autocompleter("%s","%s_choices","ajax_misc.php?%s",'.
                            '{paramName:"anccard",minChars:1,indicator:null, '.
                            'callback:%s, '.
                            ' afterUpdateElement:%s});} catch (e){alert(e.message);};',
                            $this->name,
                            $this->name,
                            $query,
                            $this->callback,
                            $this->fct);

        $javascript=create_script($javascript.$this->dblclick);

        $r=$label.$input.$attr.$div.$javascript;
        if ( $this->table == 1 )
            $r=td($r);
        return $r;

    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        $r=sprintf('
                    <INPUT TYPE="hidden" NAME="%s" VALUE="%s" SIZE="8">',
                   $this->name,
                   $this->value
                  );
        $r.='<span>'.$this->value.'</span>';
        return $r;

    }
    /**
     *@brief return a string containing the button for displaying
     * a search form. When clicking on the result, update the input text file
     * the common used attribute as
     *   - jrn   the ledger
     *   - label the field to update
     *   - name name of the input text
     *   - price amount
     *   - tvaid
     *   - typecard (deb, cred, filter or list of value)
     * will be set
     * if ICard is in readOnly, the button disappears, so the return string is empty
    \code
      // search ipopup
    $search_card=new IPopup('ipop_card');
    $search_card->title=_('Recherche de fiche');
    $search_card->value='';
    echo $search_card->input();

    $a=new ICard('test');
    $a->search();

    \endcode
     *\see ajax_card.php
     *\note the ipopup id is hard coded : ipop_card
     *@return HTML string with the button
     */
    function search()
    {
        if ( $this->readOnly==true) return '';

        $button=new IButton($this->name.'_bt');
        $a="";
        foreach (array('typecard','jrn','label','price','tvaid') as $att)
        {
            if (isset($this->$att) )
                $a.="this.".$att."='".$this->$att."';";
        }
        if (isset($this->name))
            $a.="this.inp='".$this->name."';";
        $a.="this.popup='ipop_card';";

        $button->javascript=$a.' search_card(this)';
        return $button->input();
    }

    static public function test_me()
    {

    }
}
