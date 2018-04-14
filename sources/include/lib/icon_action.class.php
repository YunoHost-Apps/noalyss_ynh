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
// Copyright (2018) Author Dany De Bontridder <dany@alchimerys.be>

/**
 * @file
 * @brief Utility , library of icon with javascript
 */

/**
 * @brief Utility , library of icon with javascript
 */
class Icon_Action
{

    /**
     * Display a icon with a magnify glass
     * @param string $id id of element
     * @param string $p_javascript
     * @param string $p_style optionnal HTML code
     * @return type
     */
    static function icon_magnifier($id, $p_javascript, $p_style="")
    {
        $r="";
        $r.=sprintf('<span  id="%s" class=" smallbutton icon" style="%s" onclick="%s">&#xf50d;</span>',
                $id, $p_style, $p_javascript);
        return $r;
    }

    /**
     * 
     * @param type $id
     * @param type $p_javascript
     * @param type $p_style
     * @return type
     */
    static function icon_add($id, $p_javascript, $p_style="")
    {
        $r=sprintf('<input  class="smallbutton icon" onclick="%s" id="%s" type="button" %s value="&#xe828;">',
                $p_javascript, $id, $p_style);
        return $r;
    }

    /**
     * 
     * @param string $id
     * @param string $p_javascript
     * @param string opt $p_style
     * @return html string
     */
    static function clean_zone($id, $p_javascript, $p_style="")
    {
        $r=sprintf('<input class="smallbutton" onclick="%s" id="%s" value="X" %s type="button" style="">',
                $p_javascript, $id, $p_style
        );
        return $r;
    }

    /**
     * Display a info in a bubble, text is in message_javascript
     * @param integer $p_comment
     * @see message_javascript.php
     * @return html string
     */
    static function infobulle($p_comment)
    {
        $r='<span tabindex="-1" class="icon" style="cursor:pointer;display:inline;text-decoration:none;" onmouseover="showBulle(\''.$p_comment.'\')"  onclick="showBulle(\''.$p_comment.'\')" onmouseout="hideBulle(0)">';
        $r.="&#xf086;";
        $r.='</span>';

        return $r;
    }

    /**
     * Display a icon ON
     * @param string $p_div id of  element
     * @param string $p_javascript
     * @param string $p_style optionnal HTML code
     * @return html string
     */
    static function iconon($p_id, $p_javascript, $p_style="")
    {
        $r=sprintf('<span  style="color:green;cursor:pointer" id="%s" class="icon" style="%s" onclick="%s">&#xf205;</span>',
                $p_id, $p_style, $p_javascript);
        return $r;
    }

    /**
     * Display a icon OFF
     * @param string $p_div id of  element
     * @param string $p_javascript
     * @param string $p_style optionnal HTML code
     * @return html string
     */
    static function iconoff($p_id, $p_javascript, $p_style="")
    {
        $r=sprintf('<span  style="color:red;cursor:pointer" id="%s" class="icon" style="%s" onclick="%s">&#xf204;</span>',
                $p_id, $p_style, $p_javascript);
        return $r;
    }

    /**
     * Return a html string with an anchor which close the inside popup. (top-right corner)
     * @param name of the DIV to close
     */
    static function close($p_div)
    {
        $r='';
        $r.=sprintf('<A class="icon" onclick="removeDiv(\'%s\')">&#10761;</A>',
                $p_div);
        return $r;
    }

    /**
     * Display a icon for fix or move a div 
     * @param string $p_div id of  the div to fix/move
     * @param string $p_javascript
     * @return html string
     */
    static function draggable($p_div)
    {
        $drag=sprintf('<span id="pin_%s" style="" class="icon " onclick="pin(\'%s\')" >'.UNPINDG.'</span>',
                $p_div, $p_div);
        return $drag;
    }

    /**
     * Display a icon for zooming
     * @param string $p_div id of  the div to zoom
     * @param string $p_javascript
     * @return html string
     */
    static function zoom($p_div, $p_javascript)
    {
        $r=sprintf('<span  id="span_%s" class="icon" onclick="%s">
                &#xf08e;</span>', $p_div, $p_javascript);
        return $r;
    }

    /**
     * Display a warning in a bubble, text is in message_javascript
     * @param integer $p_comment
     * @see message_javascript.php
     * @return html string
     */
    static function warnbulle($p_comment)
    {
        $r=sprintf('<span tabindex="-1" onmouseover="showBulle(\'%s\')"  onclick="showBulle(\'%s\')" onmouseout="hideBulle(0)" style="color:red" class="icon">&#xe818;</span>',
                $p_comment, $p_comment);

        return $r;
    }

    /**
     * Return a html string with an anchor to hide a div, put it in the right corner
     * @param $action action action to perform (message)
     * @param $javascript javascript
     * @note not protected against html
     * @see Acc_Ledger::display_search_form
     */
    static function hide($action, $javascript)
    {
        $r='';
        $r.='<span id="hide" class="icon"   onclick="'.$javascript.'">'.$action.'</span>';
        return $r;
    }
    /**
     * Display the icon of a trashbin
     * @param string $p_id DOMid 
     * @param string $p_javascript
     * @return htmlString
     */
    static function trash($p_id,$p_javascript) 
    {
        $r='<span id="'.$p_id.'" onclick="'.$p_javascript.'" class="icon">&#xe80f;</span>';
        return $r;
    }
}
