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
 * \brief create a popup in html above the current layer
 * the html inside the popup cannot contain any floating elt as div..
 *
 */
require_once NOALYSS_INCLUDE.'/function_javascript.php';
require_once NOALYSS_INCLUDE.'/class_html_input.php';

class IPopup extends HtmlInput
{
    var $name;			/*!< name name and id of the div */
    function __construct($p_name)
    {
        $this->name=$p_name;
        $this->parameter='';
        $this->attribute=array();
        $this->drag=false;
        $this->blocking=true;
    }
    function set_width($p_val)
    {
        $js=sprintf('$("%s'.'_border").style.width="%s";',
                    $this->name,$p_val);
        $this->parameter.=$js;

    }
    function set_height($p_val)
    {
        $js=sprintf('$("%s'.'_border").style.height="%s";',
                    $this->name,$p_val);
        $this->parameter.=$js;

    }
    /**
     *@brief set or not a blocking fond
     *@param $p_block if true if you want to avoid access to background,
     *accept true or false
     */
    function set_block($p_block)
    {
        $this->blocking=$p_block;
    }

    function set_zindex($p_val)
    {
        $js=sprintf('$("%s'.'_border").style.zIndex=%d;',
                    $this->name,$p_val);
        $js=sprintf('$("%s'.'_content").style.zIndex=%d;',
                    $this->name,$p_val);
        $this->parameter.=$js;
    }
    function set_dragguable($p_value)
    {
        $this->drag=$p_value;
    }
    /*!\brief set the attribute thanks javascript as the width, the position ...
     *\param $p_name attribute name
     *\param $p_val val of the attribute
     *\note add to  the this->attribut, it will be used in input()
     */
    function set_attribute($p_name,$p_val)
    {
        $this->attribute[]=array($p_name,$p_val);
    }
    /*!\brief set the title of a ipopup thanks javascript and php mode
     *\param title of the IPopup
     *\return html string with js script
     */
    function set_title($p_title)
    {
        $this->title=$p_title;
        $s=sprintf('$("%s_"+"title")="%s"',
                   $this->name,$this->title);
        return create_script($s);
    }
    function input()
    {
        $r="";
        if ($this->blocking)
        {
            $r.=sprintf('<div id="%s_fond" class="popup_back">',$this->name);
            $r.="</div>";
        }
        $javascript=sprintf("javascript:hideIPopup('%s')",
                            $this->name);


        if ( isset($this->title) && trim($this->title) != "" )
        {
            $r.=sprintf('<div id="%s_border" id="%s_border" class="popup_border_title">',
                        $this->name,
                        $this->name);
            $r.=sprintf('<span id="%s_">%s</span>',$this->name,$this->title);
        }
        else
        {
            $r.=sprintf('<div id ="%s_border" id="%s_border" class="popup_border_notitle">',
                        $this->name,
                        $this->name);
        }
        $r.='<div style="position:absolute;top:0px;right:10px;font-weight:normal;font-size:9px;color:black;text-align:right">';
        $r.=sprintf('<a style="background-color:blue;color:white;text-decoration:none" href="%s">&#10761;</a></div>',
                    $javascript);

        $r.=sprintf('<div id ="%s_content" id="%s_content" class="inner_box"> %s </div></div>',
                    $this->name,
                    $this->name,
                    $this->value);


        /* Add properties at the widget */
        $attr=$this->parameter;
        for ($i=0;$i< count($this->attribute);$i++)
        {
            list($name,$value)=$this->attribute[$i];
            $tmp1=sprintf("$('%s').%s='%s';",
                          $this->name,
                          $name,
                          $value);
            $attr.=$tmp1;
        }
        $draggable='';
        if ($this->drag==true)
        {
            /* add draggable possibility */
            $draggable=sprintf("  new Draggable('%s_border',{starteffect:function(){
                               new Effect.Highlight('%s_border',{scroll:window,queue:'end'});  } });"
                               ,$this->name
                               ,$this->name);

        }
        $attr=create_script($attr.$draggable);
        $r.=$attr;
        return $r;
    }

    static function test_me()
    {
        require_once NOALYSS_INCLUDE.'/class_iselect.php';
        $select=new ISelect('a');
        $select->value=array(array ('value'=>0,'label'=>'Première valeur'),
                             array ('value'=>0,'label'=>'Première valeur'),
                             array ('value'=>0,'label'=>'Première valeur'));
        for ($e=0;$e<5;$e++)
        {
            echo $select->input();
            if ($e%10 == 0 ) echo '<hr>';
        }
        $a=new IPopup('pop1');
        $a->value="";
        for ($e=0;$e<500;$e++)
        {
            $a->value.="<p>Il etait une fois dans  un pays vraiment lointain où même plus loin que ça</p>";
        }
        echo $a->input();
        echo '
        <input type="button" onclick="hide(\'pop1\');hide(\'pop1_border\')" value="cacher">
                                     <input type="button" onclick="showIPopup(\'pop1\')" value="montrer">
              ';
        $a=new IPopup('pop2');
        $a->value='';
        $a->title="Retrouvez une saucisse";
        echo $a->input();
        echo '
        <input type="button" onclick="hide(\'pop2\');hide(\'pop2_border\')" value="cacher">
                        <input type="button" onclick="showIPopup(\'pop2\')" value="montrer">
              ';

    }
}
