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

// Copyright 2014 Author Dany De Bontridder danydb@aevalys.eu
// require_once '.php';
/**
 *@file
 *@brief insert concerned operation , call from follow up
 */

if (!defined('ALLOWED'))
    die('Appel direct ne sont pas permis');

require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

ob_start();
try
{
    $ag_id=$http->get("ag_id");
}
catch (Exception $exc)
{
    echo $exc->getMessage();
    error_log($exc->getTraceAsString());
    return;
}

require_once NOALYSS_INCLUDE.'/class/acc_ledger.class.php';

$r=HtmlInput::title_box(_("Détail fiche"), 'search_card');

$r.='<form id="search_card1_frm" method="GET" onsubmit="action_add_concerned_card(this);return false;">';
$q=new IText('query');
$q->value=(isset($query))?$query:'';
$r.='<span style="margin-left:50px">';
$r.=_('Fiche contenant').Icon_Action::infobulle(19);
$r.=$q->input();
$r.=HtmlInput::submit('fs', _('Recherche'), "", "smallbutton");
$r.='</span>';
$r.=dossier::hidden().HtmlInput::hidden('op', 'add_concerned_card');
$r.=HtmlInput::request_to_hidden(array('ag_id'));
$r.='</form>';
$query=$http->get("query", "string","");
$sql_array['query']=$query;
$sql_array['typecard']='all';

$fiche=new Fiche($cn);
/* Build the SQL and show result */
$sql=$fiche->build_sql($sql_array);


/* We limit the search to MAX_SEARCH_CARD records */
$sql=$sql.' order by vw_name limit '.MAX_SEARCH_CARD;
$a=$cn->get_array($sql);
for ($i=0; $i<count($a); $i++)
{
    $array[$i]['quick_code']=$a[$i]['quick_code'];
    $array[$i]['name']=h($a[$i]['vw_name']);
    $array[$i]['accounting']=$a[$i]['accounting'];
    $array[$i]['first_name']=h($a[$i]['vw_first_name']);
    $array[$i]['description']=h($a[$i]['vw_description']);
    $array[$i]['javascript']=sprintf("action_save_concerned(%d,'%s','%s')",$gDossier,$a[$i]['f_id'],$ag_id);
}//foreach


echo $r;
// No accountancy history
$accvis=0; 
require_once(NOALYSS_TEMPLATE.'/card_result.php');
$response=ob_get_contents();
ob_end_clean();


$html=escape_xml($response);
if ( !headers_sent() ) { header('Content-type: text/xml; charset=UTF-8');} else {echo $response;echo $html;}
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>unused</ctl>
<code>$html</code>
</data>
EOF;
?>