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
 * \brief Html Input show just a ref to an action
 * create a button with a link, if you want to use a javascript
 * value must be empty
 */
require_once NOALYSS_INCLUDE.'/class_html_input.php';
class IAction_deprecated extends HtmlInput
{
    /*!\brief show the html  input of the widget*/
    public function input($p_name="",$p_value="")
    {
        $this->name=($p_name=="")?$this->name:$p_name;
        $this->value=($p_value=="")?$this->value:$p_value;
        $this->id=($this->id=="")?$this->name:$this->id;
        if ( $this->readOnly==true) return $this->display();
        $this->javascript= (!isset ($this->javascript))?"":$this->javascript;
        if ( $this->value !="")
            $r=sprintf('<span id="%s" class="action"> <A class="action" HREF="%s" %s>%s</A></span>',
                       $this->id,
                       $this->value,
                       $this->javascript,
                       $this->label);
        else
            $r=sprintf('<span id="%s" class="action"> <A class="action" href="javascript: %s">%s</A></span>',
                       $this->id,
                       $this->javascript,
                       $this->label);

        return $r;

    }
    /*!\brief print in html the readonly value of the widget*/
    public function display()
    {
        return;
    }
    static public function test_me()
    {
    }
}
