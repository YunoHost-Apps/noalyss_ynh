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
class IDate extends HtmlInput
{
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null)
    {
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
        if ( $this->readOnly==true) return $this->display();
	if( $this->id=="")
	  $this->id=self::generate_id($this->name);
        $r='<input type="text" name="'.$this->name.'" id="'.$this->id.'" '.
           ' class="input_text" '.
           'size="10" style="width:6em" '.
           ' value ="'.$this->value.'" '.
           '/>'.
           '<img src="image/x-office-calendar.png" id="'.$this->id.'_trigger"'.
           ' style="cursor: pointer" '.
           'onmouseover="this.style.background=\'red\';" onmouseout="this.style.background=\'\'" />';
        $r.='<script type="text/javascript">'.
            'Calendar.setup({'.
            //	'date : "'.$this->value.'",
            'inputField     :    "'.$this->id.'",     // id of the input field
            ifFormat       :    "%d.%m.%Y",      // format of the input field
            button         :    "'.$this->id.'_trigger",  // trigger for the calendar (button ID)
            align          :    "Bl",           // alignment (defaults to "Bl")
            singleClick    :    true
        });
            </script>
            ';
        return $r;

    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        $r="<span>  : ".$this->value;
        $r.='<input type="hidden" name="'.$this->name.'"'.
            'id="'.$this->name.'"'.
            ' value = "'.$this->value.'"></span>';
        return $r;

    }
    static public function test_me()
    {
    }
}
