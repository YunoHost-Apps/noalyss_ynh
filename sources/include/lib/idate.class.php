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

  /**
   *\file
   \brief Html Input
  */


/// Html Input : Input a date format dd.mm.yyyy
/// The property title should be set to indicate what it is expected
require_once NOALYSS_INCLUDE.'/lib/html_input.class.php';

class IDate extends HtmlInput
{

    var $placeholder;
    var $title;
    var $autofocus;

    function __construct($name='', $value='', $p_id="")
    {
        parent::__construct($name, $value, $p_id);
        $this->title="";
        $this->placeholder="dd.mm.yyyy";
        $this->extra="";
        $this->style=' class="input_text" ';
        $this->autofocus=false;
    }

    /* !\brief show the html  input of the widget */

    public function input($p_name=null, $p_value=null)
    {
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
        if ($this->readOnly==true)
            return $this->display();
        if ($this->id=="")             $this->id=self::generate_id($this->name);
        $t= 'title="'.$this->title.'" ';
        $autofocus=($this->autofocus)?" autofocus ":"";
        
        $r=sprintf('
            <input type="text" name="%s" id="%s" 
                 class="input_text" 
                size="10" style="width:6em"
                 value ="%s" 
                 placeholder="%s"
                 title="%s"
                 %s
                 pattern="[0-9]{1,2}.[0-9]{1,2}.[0-9]{4}"
                />
                <span  class="smallbutton icon"
                id="%s_trigger"
                />
                &#xe811;
                </span>
                ',$this->name,$this->id,$this->value,$this->placeholder,$this->title,$t,$this->id
                );
        
        $r.=sprintf('<script type="text/javascript">
                Calendar.setup({'.
                'inputField     :    "%s",     // id of the input field
            ifFormat       :    "%%d.%%m.%%Y",      // format of the input field
            button         :    "%s_trigger",  // trigger for the calendar (button ID)
            align          :    "Bl",           // alignment (defaults to "Bl")
            singleClick    :    true
        });
            </script>'
                ,$this->id,$this->id);
        return $r;
    }

    /* !\brief print in html the readonly value of the widget */

    public function display()
    {
        $r="<span>  ".$this->value;
        $r.='<input type="hidden" name="'.$this->name.'"'.
                'id="'.$this->id.'"'.
                ' value = "'.$this->value.'"></span>';
        return $r;
    }

    static public function test_me()
    {
        
    }

}
