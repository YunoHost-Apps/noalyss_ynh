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
 * \brief Html Input , create a tag <SELECT> ... </SELECT> 
 * if readonly == true then display the label corresponding to the selected value
 * You can use also $this->rowsize  to specify the number of lines to display
 * 
 * @see Database::make_array
 */
require_once NOALYSS_INCLUDE.'/lib/html_input.class.php';
class ISelect extends HtmlInput
{
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null)
    {
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
        if ( $this->readOnly==true) return $this->display();
        $style=(isset($this->style))?$this->style:"";
		$this->id=($this->id=="")?$this->name:$this->id;

        $disabled=($this->disabled==true)?"disabled":"";
        $rowsize = (isset ($this->rowsize)) ? ' size = "'.$this->rowsize.'"':"";
        
        $r="";

        $a="<SELECT   id=\"$this->id\" NAME=\"$this->name\" $style $this->javascript $disabled $rowsize>";
        if (empty($this->value)) return '';
        for ( $i=0;$i<sizeof($this->value);$i++)
        {
            $checked=($this->selected==$this->value[$i]['value'])?"SELECTED":"";
            $a.='<OPTION VALUE="'.$this->value[$i]['value'].'" '.$checked.'>';
            $a.=$this->value[$i]['label'];
        }
        $a.="</SELECT>";
        if ( $this->table == 1 )		  $a='<td>'.$a.'</td>';

        return $r.$a;
    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        $r="";
        for ( $i=0;$i<sizeof($this->value);$i++)
        {
            if ($this->selected==$this->value[$i]['value'] )
            {
                $r=h($this->value[$i]['label']);

            }
        }
      // $r='<span class="input_text_ro">'.$r.'</span>';
	if ( $this->table == 1 )		  $a='<td>'.$r.'</td>';
        return $r;
    }
    /*!\brief print in html the readonly value of the widget*/
    public function get_value()
    {
        $r="";
        for ( $i=0;$i<sizeof($this->value);$i++)
        {
            if ($this->selected==$this->value[$i]['value'] )
            {
                $r=h($this->value[$i]['label']);

            }
        }
        return $r;
    }
   /**
    * @brief set the value of an ISelect with the array , this array
    * is bidimensional , the first dimension is the code to store and the second
    * is the label to display.
    * Example
    * @code
    * array(array('M'=>'Mister'),array('Ms'=>'Miss'));
    * // will be turned into 
    * array( array("value"=>'M,"label"=>"Mister")...)
    * @endcode
    * @param type $p_array
    * @return type
    */
   public function transform($p_array) {
        if (count($p_array)==0) return array();
        $a_ret=array();
        foreach ($p_array as $key=>$value) {
            $a_ret['value']=$key;
            $a_ret['label']=$value;
            $this->value[]=$a_ret;
        }
    }
    function set_value($p_string) {
        $this->selected=$p_string;
    }
    static public function test_me()
    {
    }
}
