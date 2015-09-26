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

// Copyright 2015 Author Dany De Bontridder danydb@aevalys.eu

// require_once '.php';
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

global $g_user, $cn, $g_parameter;

// Security check if user can connect and update
if ($g_user->check_module('CFGPCMN') == 0 )
{
    $html=h2(_('Action interdite'),' class="notice"');
    $html = escape_xml($response);

    header('Content-type: text/xml; charset=UTF-8');
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>pcmn_update</ctl>
<code>$html</code>
<status>NOTALLOWED</status>
</data>
EOF;
    return;
}

ob_start();
$pcmn_val=HtmlInput::default_value_get('value', "-1");

// if empty 
if ( $pcmn_val != "-1" )
{
    // not set

}
$action='new';
$val=new IText('p_valu');
$parent=new IText('p_parentu');
$lib=new IText('p_libu');
$lib->css_size="100%";
$type=new ISelect('p_typeu');
$type->value=Acc_Account::$type;

if ( $pcmn_val != "")
{
    $action='update';
    /*
     * Not empty, show the default value
     */
    $account = new Acc_Account($cn);
    $account->set_parameter('value',$pcmn_val);
    $account->load();
    
    $val->value=$account->get_parameter('value');
    $parent->value=$account->get_parameter('parent');
    $lib->value=$account->get_parameter('libelle');
    $type->selected=$account->get_parameter('type');
            
}

require 'template/pcmn_update.php';
$response = ob_get_clean();
$html = escape_xml($response);
if ( headers_sent() ) {
 echo $response;   
 echo $html;   
}     else {     
    header('Content-type: text/xml; charset=UTF-8');
    echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl></ctl>
<code>$html</code>
<status>ok</status>
</data>
EOF;
     }