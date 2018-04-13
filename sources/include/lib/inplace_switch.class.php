<?php

/*
 *   This file is part of NOALYSS.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2016) Author Dany De Bontridder <dany@alchimerys.be>

/**
 * @file
 * @brief  A switch let you switch between 2 values : 0 and 1, it is used to 
 * replace the check
 */
class Inplace_Switch
{

    /// The icon on
    private $iconon;
    /// The icon off
    private $iconoff;
    /// name of the widget, javascript id must be unique
    private $name;
    /// value
    private $value;
    /// Json object
    private $json;
    /// callback
    private $callback;
    /// Supplemental javascript command, execute after the ajax script
    private $jscript;
    
    function __construct($p_name, $p_value)
    {
        $this->name=$p_name;
        $this->value=$p_value;
        $this->iconon=Icon_Action::iconon(uniqid(), "");
        $this->iconoff=Icon_Action::iconoff(uniqid(), "");
        $this->json=json_encode(['name'=>$p_name,"value"=>$p_value], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
        $this->callback="ajax.php";
        $this->jscript="";
    }

    function input()
    {
        if ($this->value==1)
        {
            $icon=$this->iconon;
            $color="green";
        }
        elseif ($this->value==0)
        {
            $icon=$this->iconoff;
            $color="red";
        }
        else
        {
            throw new Exception(_("Invalide value"));
        }
        
        printf('<span style="text-decoration: none;color:%s" class="inplace_edit icon" id="%s">', $color,$this->name);
        echo $icon;
        echo '</span>';
        echo <<<EOF
        <script>
{$this->name}.onclick=function() {new Ajax.Updater({$this->name},'{$this->callback}',{method:"get",parameters:{$this->json},evalScripts:true} );
   {$this->jscript} 
   }
</script>
EOF;
    }
    public function get_jscript()
    {
        return $this->jscript;
    }

    public function set_jscript($jscript)
    {
        $this->jscript=$jscript;
    }

        public function get_json()
    {
        return $this->json;
    }

    public function get_callback()
    {
        return $this->callback;
    }

    public function set_json($json)
    {
        $this->json=$json;
    }

    public function set_callback($callback)
    {
        $this->callback=$callback;
    }

    public function get_iconon()
    {
        return $this->iconon;
    }

    public function get_iconoff()
    {
        return $this->iconoff;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_value()
    {
        return $this->value;
    }

    public function set_iconon($iconon)
    {
        $this->iconon=$iconon;
    }

    public function set_iconoff($iconoff)
    {
        $this->iconoff=$iconoff;
    }

    public function set_name($name)
    {
        $this->name=$name;
    }

    public function set_value($value)
    {
        $this->value=$value;
    }
  /**
     * Add json parameter to the current one, if there attribute already exists
     * it will be overwritten
     */
    function add_json_param($p_attribute,$p_value) {
        $x=json_decode($this->json,TRUE);
        $x[$p_attribute]=$p_value;
        $this->json=json_encode($x, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
    }
}
