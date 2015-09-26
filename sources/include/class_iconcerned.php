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
 *  - name is the name and id of the input
 *  - extra amount of the operation to reconcile
 *  - extra2 ledger paid
 */
require_once NOALYSS_INCLUDE.'/class_html_input.php';
class IConcerned extends HtmlInput
{

	public function __construct($p_name='',$p_value='',$p_id="")
	{
		$this->name=$p_name;
		$this->value=$p_value;
		$this->amount_id=null;
		$this->paid='';
		$this->id=$p_id;
                $this->tiers=""; // id of the field for the tiers to be updated
	}
    /*!\brief show the html  input of the widget*/
    public function input($p_name=null,$p_value=null)
    {
        $this->name=($p_name==null)?$this->name:$p_name;
        $this->value=($p_value==null)?$this->value:$p_value;
        if ( $this->readOnly==true) return $this->display();

	    $this->id=($this->id=="")?$this->name:$this->id;


        $r=sprintf("
                    <image onclick=\"search_reconcile(".dossier::id().",'%s','%s','%s','%s')\" class=\"image_search\" src=\"image/magnifier13.png\" />
                   
                   <INPUT TYPE=\"text\"  style=\"color:black;background:lightyellow;border:solid 1px grey;\"  NAME=\"%s\" ID=\"%s\" VALUE=\"%s\" SIZE=\"8\" readonly>
				   <INPUT class=\"smallbutton\"  TYPE=\"button\" onClick=\"$('%s').value=''\" value=\"X\">

                   ",
                   $this->name,
                   $this->amount_id,
                   $this->paid,
                   $this->tiers,
                   $this->name,
                   $this->id,
                   $this->value,
                   $this->id
                  );
        return $r;
    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        $r=sprintf("<span><b>%s</b></span>",$this->value);
        $r.=sprintf('<input type="hidden" name="%s" value="%s">', $this->name,$this->value);
        return $r;

    }
    static public function test_me()
    {
    }
}
