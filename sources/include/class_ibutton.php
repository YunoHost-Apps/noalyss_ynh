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
require_once NOALYSS_INCLUDE.'/class_html_input.php';
class IButton extends HtmlInput
{
    var $label;
    var $class;
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null,$p_class="")
    {
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
	$this->label=(trim($this->label) != '')?$this->label:$this->value;
        $this->class=($p_class != "")?$p_class:$this->class;
        $this->class=($this->class=="")?"smallbutton ":$this->class;
        if ( $this->readOnly==true) return $this->display();
        $extra= ( isset($this->extra))?$this->extra:"";
        $this->id=($this->id=="")?$this->name:$this->id;
		$tab=(isset($this->tabindex))?' tabindex="'.$this->tabindex.'"':"";
        $r='<input type="BUTTON" name="'.$this->name.'"'.
           ' class="'.$this->class.'" '.
                $this->extra.
				$tab.
           ' id="'.$this->id.'"'.
           ' value="'.$this->label.'"'.
           ' onClick="'.$this->javascript.'"'.$extra.'>';
        $attr=$this->get_js_attr();
        $r.=$attr;
        return $r;

    }

    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        return "";
    }
    static function tooggle_checkbox($p_form)
    {
        $select_all=new IButton('select_all');
        $select_all->label=_('Inverser la sélection');
        $select_all->javascript="toggle_checkbox('$p_form')";
        return $select_all->input();
    }
    static function select_checkbox($p_form)
    {
        $select_all=new IButton('select_all');
        $select_all->label=_('Cocher tous');
        $select_all->javascript="select_checkbox('$p_form')";
        return $select_all->input();
    }
    static function unselect_checkbox($p_form)
    {
        $select_all=new IButton('select_all');
        $select_all->label=_('Décocher tous');
        $select_all->javascript="unselect_checkbox('$p_form')";
        return $select_all->input();
    }
    static function show_calc()
    {
        $calc=new IButton('shcalc');
        $calc->label=_('Calculatrice');
        $calc->javascript="show_calc()";
        return $calc->input();

    }
    static public function test_me()
    {
    }
}
class ISmallButton extends IButton
{
    var $label;
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null,$p_style=null)
    {
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
		$this->label=(trim($this->label) != '')?$this->label:$this->value;
        if ( $this->readOnly==true) return $this->display();
        $extra= ( isset($this->extra))?$this->extra:"";
        $this->id=($this->id=="")?$this->name:$this->id;
		$tab=(isset($this->tabindex))?' tabindex="'.$this->tabindex.'"':"";
        $r='<input type="BUTTON" name="'.$this->name.'"'.
           ' class="smallbutton" '.
                $this->extra.
				$tab.
           ' id="'.$this->id.'"'.
           ' value="'.$this->label.'"'.
           ' onClick="'.$this->javascript.'"'.$extra.'>';
        $attr=$this->get_js_attr();
        $r.=$attr;
        return $r;

    }
}