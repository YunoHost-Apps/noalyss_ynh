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
 * \brief for the numeric input text field
 */
require_once NOALYSS_INCLUDE.'/class_itext.php';
/*!\brief
 * This class handles only the numeric input, the input will
 * call a javascript
 * to change comma to period  and will round it (2 decimal), the precision is given by
 * the attribute prec
 * attribute
 *  extra = extra code (free)
 *  size = size of the field
 *  prec = precision default = 2
 *  name = name of the html object
 *  javascript = javascript to execute (default = onchange="format_number(this,2);)
 *  value = value of the widget
 *
 */
class INum extends IText
{
    function __construct($name='',$value='')
    {
        parent::__construct($name,$value);

        $this->size=9;
        $this->style='class="inum"';
	$this->javascript= 'onchange="format_number(this,2);"';
    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {

        $readonly=" readonly ";
		$this->id=($this->id=="")?$this->name:$this->id;

        //$style='style="border:solid 1px blue;color:black;background:#EDEDED;text-align:right"';
        $style=' class="inum input_text_ro"';
        $this->value=str_replace('"','',$this->value);
        $r='<INPUT '.$style.' TYPE="TEXT" id="'.
           $this->id.'"'.
           'NAME="'.$this->name.'" VALUE="'.$this->value.'"  '.
           'SIZE="'.$this->size.'" '.$this->javascript." $readonly $this->extra >";

        /* add tag for column if inside a table */
        if ( $this->table == 1 )		  $r='<td>'.$r.'</td>';

        return $r;

    }
	 /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null)
    {
		if ( isset ($this->prec)) {
			$this->javascript= 'onchange="format_number(this,'.$this->prec.');"';
		}
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
		$this->id=($this->id=="")?$this->name:$this->id;

        if ( $this->readOnly==true) return $this->display();

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

}


