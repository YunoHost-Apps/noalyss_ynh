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
class IText extends HtmlInput
{
    function __construct($name='',$value='',$p_id="")
    {
        parent::__construct($name,$value,$p_id);
        $this->style=' class="input_text" ';
    }
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null)
    {
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
        if ( $this->readOnly==true) return $this->display();
		$this->id=($this->id=="")?$this->name:$this->id;

        $t= ((isset($this->title)))?'title="'.$this->title.'"   ':' ';

        $extra=(isset($this->extra))?$this->extra:"";

        $this->value=str_replace('"','',$this->value);
        if ( ! isset ($this->css_size))
        {
        $r='<INPUT '.$this->style.' TYPE="TEXT" id="'.
           $this->id.'"'.$t.
           'NAME="'.$this->name.'" VALUE="'.$this->value.'"  '.
           'SIZE="'.$this->size.'" '.$this->javascript."  $this->extra >";
        /* add tag for column if inside a table */
        } else {
           $r='<INPUT '.$this->style.' TYPE="TEXT" id="'.
           $this->id.'"'.$t.
           'NAME="'.$this->name.'" VALUE="'.$this->value.'"  '.
           ' style="width:'.$this->css_size.';" '.$this->javascript."  $this->extra >";

        }

        if ( $this->table == 1 )		  $r='<td>'.$r.'</td>';

        return $r;

    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        $t= ((isset($this->title)))?'title="'.$this->title.'"   ':' ';

        $extra=(isset($this->extra))?$this->extra:"";

        $readonly=" readonly ";
        $this->value=str_replace('"','',$this->value);
		 $this->style=' class="input_text_ro" ';
         if ( ! isset ($this->css_size))
        {
        $r='<INPUT '.$this->style.' TYPE="TEXT" id="'.
           $this->id.'"'.$t.
           'NAME="'.$this->name.'" VALUE="'.$this->value.'"  '.
           'SIZE="'.$this->size.'" '.$this->javascript." $readonly $this->extra >";
        } else {
               $r='<INPUT '.$this->style.' TYPE="TEXT" id="'.
           $this->id.'"'.$t.
           'NAME="'.$this->name.'" VALUE="'.$this->value.'"  '.
           ' style="width:'.$this->css_size.'" '.$this->javascript." $readonly  $this->extra >";
        }

        /* add tag for column if inside a table */
        if ( $this->table == 1 )		  $r='<td>'.$r.'</td>';

        return $r;

    }
    static public function test_me()
    {
    }
}
