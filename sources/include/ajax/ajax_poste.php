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
if ( ! defined('ALLOWED')) die (_('Non authorisé'));

require_once  NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/class/acc_ledger.class.php';
require_once  NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/lib/function_javascript.php';
require_once NOALYSS_INCLUDE.'/class/acc_account_ledger.class.php';
mb_internal_encoding("UTF-8");

extract($_REQUEST, EXTR_SKIP);

if  ($g_user->check_dossier(dossier::id()) == 'X') exit();

switch ($op2)
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
    $r.=HtmlInput::title_box(_('Poste Comptable'),'search_account',"close","","y");
    

    $r.='<form id="sp" method="get" onsubmit="'.$attr.'search_get_poste(this);return false;">';
    ob_start();
    require_once NOALYSS_TEMPLATE.'/account_search.php';
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

    require_once NOALYSS_TEMPLATE.'/account_result.php';
    $r.=ob_get_contents();
    ob_end_clean();
    
    $html=$r;
    $html.=HtmlInput::button_close("search_account");
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
