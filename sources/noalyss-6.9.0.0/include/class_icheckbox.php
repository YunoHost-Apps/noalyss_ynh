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
class ICheckBox extends HtmlInput
{
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null)
    {
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
        if ( $this->readOnly==true) return $this->display();
		 $this->id=($this->id=="")?$this->name:$this->id;

        $check=( $this->selected==true )?"checked":"unchecked";
        $r='<input type="CHECKBOX" id="'.$this->id.'" name="'.$this->name.'"'.' value="'.$this->value.'"';
        $r.="  $check";
        $r.=' '.$this->disabled."  ".$this->javascript.'>';

        $r=$r." $this->label";

        return $r;


    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        $check=( $this->selected==true )?"checked":"unchecked";
        $r='<input type="CHECKBOX" id="'.$this->name.'" name="'.$this->name.'"';
        $r.="  $check";
        $r.=' disabled>';

        return $r;

    }
    /**
     *set selected to true (checked) if the value equal the parameter
     * @param $p_value value to compare
     */
    public function set_check($p_value)
        {
        if ($this->value==$p_value)$this->selected=true;
        }
    static function toggle_checkbox($p_name,$p_form) {
            $a=new ICheckBox($p_name);
            $a->javascript='onclick="toggle_checkbox(\''.$p_form.'\')"';
            return $a->input();
        }
    static public function test_me()
    {
    }
}
