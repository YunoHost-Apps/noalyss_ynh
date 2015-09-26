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
 * \brief this file answer to the Ajax request from js/accounting_item.js
 * - op
    - sf  show form of search
         - param j : ledger -> php jrn
         - param c : control for storing the pcm_val -> javascript account
         - param l : control for storing the pcm_lib -> javascript label
	 - param ctl : the node to update (ipopup)
	 - param q : the acc_query -> javascript query
 * - ctl (to return)
 *
 *
 */
if ( ! defined('ALLOWED')) define ('ALLOWED',1);

require_once '../include/constant.php';
require_once  NOALYSS_INCLUDE.'/ac_common.php';
require_once NOALYSS_INCLUDE.'/class_acc_ledger.php';
require_once  NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/function_javascript.php';
require_once NOALYSS_INCLUDE.'/class_acc_account_ledger.php';
mb_internal_encoding("UTF-8");

extract($_REQUEST);
$var=array('gDossier','op','ctl');
$cont=0;
/*  check if mandatory parameters are given */
foreach ($var as $v)
{
    if ( ! isset ($_REQUEST [$v] ) )
    {
        echo "$v is not set ";
        $cont=1;
    }
}
ajax_disconnected($ctl);

set_language();

if ( $cont != 0 ) exit();
$cn=new Database(dossier::id());
require_once  NOALYSS_INCLUDE.'/class_user.php';
global $g_user;
$g_user=new User($cn);
$g_user->Check();
if  ($g_user->check_dossier(dossier::id()) == 'X') exit();
$xml="";
if ( LOGINPUT)
    {
        $file_loginput=fopen($_ENV['TMP'].'/scenario-'.$_SERVER['REQUEST_TIME'].'.php','a+');
        fwrite ($file_loginput,"<?php \n");
        fwrite ($file_loginput,'//@description:'.$op."\n");
        fwrite($file_loginput, '$_GET='.var_export($_GET,true));
        fwrite($file_loginput,";\n");
        fwrite($file_loginput, '$_POST='.var_export($_POST,true));
        fwrite($file_loginput,";\n");
        fwrite($file_loginput, '$_POST[\'gDossier\']=$gDossierLogInput;');
        fwrite($file_loginput,"\n");
        fwrite($file_loginput, '$_GET[\'gDossier\']=$gDossierLogInput;');
        fwrite($file_loginput,"\n");
        fwrite($file_loginput,' $_REQUEST=array_merge($_GET,$_POST);');
        fwrite($file_loginput,"\n");
         fwrite($file_loginput,"include '".basename(__FILE__)."';\n");
        fclose($file_loginput);
    }
switch ($op)
{
    /*----------------------------------------------------------------------
     * Show the form and the result
     *
     ----------------------------------------------------------------------*/
case "sf":
        $ipopup=$ctl;
    $attr=sprintf('this.ctl=\'%s\';',$ipopup);
    $ctl.='_content';
    $it=new IText('acc_query');
    $it->size=30;
    $it->value=(isset($q))?$q:'';
    $str_poste=$it->input();
    $str_submit=HtmlInput::submit('sf',_('Recherche'),"","smallbutton");
    $r='';
	$r=HtmlInput::anchor_close('search_account');
    $r.='<div> '.h2(_('Poste Comptable'),' class="title"').'</div>';

    $r.='<form id="sp" method="get" onsubmit="'.$attr.'search_get_poste(this);return false;">';
    ob_start();
    require_once NOALYSS_INCLUDE.'/template/account_search.php';
    $r.=ob_get_contents();
    ob_end_clean();
    $r.=dossier::hidden();
    $r.=(isset ($c))?HtmlInput::hidden('account',$c):"";
    $r.=(isset ($l))?HtmlInput::hidden('label',$l):"";
    $r.=(isset ($j))?HtmlInput::hidden('jrn',$j):"";
    $r.=(isset ($nover))?HtmlInput::hidden('nover','1'):"";
    $r.=(isset ($nosearch))?HtmlInput::hidden('nosearch','1'):"";
    $r.=(isset ($bracket))?HtmlInput::hidden('bracket','1'):"";


    $r.='</form>';
    $sql="
		select pcm_val,pcm_lib,array_to_string(array_agg(j_qcode) , ',') as acode
		from tmp_pcmn left join vw_poste_qcode on (j_poste=pcm_val) ";
    $sep=" where ";
    /* build the sql stmt */
    if ( isset($j) && $j > 0 && isNumber($j))
    {
        /* create a filter on the ledger */
        $ledger=new Acc_Account_Ledger($cn,0);
        $fd_id=$ledger->build_sql_account($j);
        if ( $fd_id != '' )
        {
            $sql.=" $sep (".$fd_id.')';
            $sep=" and ";
        }
    }
    /* show result */
    if ( isset($q) && strlen(trim($q)) > 0)
    {
        $q= sql_string($q);
        $sql.=sprintf(" $sep ( pcm_val::text like '%s%%' or pcm_lib::text ilike '%%%s%%') ",
                      $q,$q);
    }
    $sql.=' group by pcm_val,pcm_lib,pcm_val_parent, pcm_type  order by pcm_val::text limit 50';
    if ( isset($q) && strlen(trim($q))> 0 )
    {
        $array=$cn->get_array($sql);
    }
    if ( ! isset($q) ) $array=array();
    if ( isset($q) && strlen(trim($q))==0) $array=array();

    /*  set the javascript */
    for ($i=0;$i<count($array);$i++)
    {
        $pcm_val=$array[$i]['pcm_val'];
        if ( isset($bracket))
        {
            $pcm_val='['.$pcm_val.']';
        }
        if (isset($nover))
        {
            /* no overwrite  */
            $str=sprintf("$('%s').value=$('%s').value+' '+'%s';",
                         $c,$c,$pcm_val);

        }
        else
        {
            $str=sprintf("$('%s').value='%s';",
                         $c,$pcm_val);
        }

        if ( isset($l) )
        {
            $str.=sprintf("set_value('%s',g('%s').innerHTML);",
                          $l,"lib$i");

        }
        $str.="removeDiv('search_account');";
        $array[$i]['javascript']=$str;
    }
    ob_start();

    require_once NOALYSS_INCLUDE.'/template/account_result.php';
    $r.=ob_get_contents();
    ob_end_clean();

    $html=$r;
    break;
}
$xml=escape_xml($html);
if (headers_sent() && DEBUG ) {
    echo $html;
}
else 
{
    header('Content-type: text/xml; charset=UTF-8');
}
    
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$ctl</ctl>
<code>$xml</code>
</data>
EOF;
