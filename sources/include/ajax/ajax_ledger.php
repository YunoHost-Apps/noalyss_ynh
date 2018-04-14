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
 * \brief answer to the ajax request for the ledger
 * it means :
    - detail of an operation (expert, user and analytic view)
    - removal of an operation
    - load a receipt document
    - for reconcialiation
    - update of analytic content
*/
if ( ! defined('ALLOWED')) die(_('Non authorisé'));

require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/class/user.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_operation.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_ledger.class.php';
require_once NOALYSS_INCLUDE.'/class/fiche.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_reconciliation.class.php';
require_once NOALYSS_INCLUDE.'/class/anc_operation.class.php';
require_once NOALYSS_INCLUDE.'/lib/idate.class.php';
require_once NOALYSS_INCLUDE.'/class/noalyss_parameter_folder.class.php';
require_once NOALYSS_INCLUDE.'/lib/iconcerned.class.php';
require_once NOALYSS_INCLUDE.'/lib/http_input.class.php';
$http=new HttpInput();

/**
 * Check if we receive the needed data (jr_id...)
 */
global $g_user,$cn,$g_parameter;
mb_internal_encoding("UTF-8");

try
{
    $action=$http->request('act');
    $jr_id=$http->request('jr_id');
    $div=$http->request('div');		/* the div source and target for javascript */
    $gDossier=dossier::id();
    
}
catch (Exception $exc)
{
    error_log($exc->getTraceAsString());
    return;
}

/**
 *if $_SESSION['g_user'] is not set : echo a warning
 */

$cn=Dossier::connect();
$g_parameter=new Noalyss_Parameter_Folder($cn);

$g_user->check();
if ( $g_user->check_dossier(dossier::id(),true)=='X' )
{
    ob_start();
    require_once  NOALYSS_TEMPLATE.'/ledger_detail_forbidden.php';
	echo HtmlInput::button_close($div);
    $html=ob_get_contents();
    ob_end_clean();
    $html=escape_xml($html);
    header('Content-type: text/xml; charset=UTF-8');
    echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$div</ctl>
<code>$html</code>
</data>
EOF;
    exit();
}


// check if the user can access the ledger where the operation is (view) and
// if he can modify it
$op=new Acc_Operation($cn);
$op->jr_id=$jr_id;
$ledger=$op->get_ledger();
if ($ledger=="")
{

    ob_start();
	echo HtmlInput::title_box(_("Information"), $div);
    require_once NOALYSS_TEMPLATE.'/ledger_detail_forbidden.php';
	echo HtmlInput::button_close($div);
    $html=ob_get_contents();
    ob_end_clean();

    $html=escape_xml($html);
    if ( ! headers_sent()) {     header('Content-type: text/xml; charset=UTF-8');} else { echo "HTML".unescape_xml($html);}
    echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$div</ctl>
<code>$html</code>
</data>
EOF;
    exit();

}
$access=$g_user->get_ledger_access($ledger);
if ( $access == 'X' )
{
    ob_start();
	echo HtmlInput::title_box(_("Information"), $div);
    require_once NOALYSS_TEMPLATE.'/ledger_detail_forbidden.php';
	echo HtmlInput::button_close($div);
    $html=ob_get_contents();
    ob_end_clean();
    $html=escape_xml($html);
    header('Content-type: text/xml; charset=UTF-8');
    echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$div</ctl>
<code>$html</code>
</data>
EOF;
    exit();
}
$html=var_export($_REQUEST,true);
switch ($action)
{
    ///////////////////////////////////////////////////////////////////////////
    //  remove op
    ///////////////////////////////////////////////////////////////////////////
case 'rmop':
        if ( $access=='W' && $g_user->check_action(RMOPER) == 1)
        {
            ob_start();
            /* get the ledger */
            try
            {
                $cn->start();
                $oLedger=new Acc_Ledger($cn,$ledger);
                $oLedger->jr_id=$jr_id=$http->request('jr_id',"number");
                $oLedger->delete();
                $cn->commit();
                echo _("Opération Effacée");
            }
            catch (Exception $e)
            {
                record_log($e->getTraceAsString());
                $e->getMessage();
                $cn->rollback();
            }
            $html=ob_get_contents();
            ob_end_clean();
        }
        else 
        {
            $html= _("Effacement refusé");
        }
    break;
    //////////////////////////////////////////////////////////////////////
    // DE Detail
    //////////////////////////////////////////////////////////////////////
case 'de':
    ob_start();

    try
    {
        /* get detail op (D/C) */
        $op->get();			
        /* return an obj. ACH / FIN or VEN or null if nothing is found*/
        $obj=$op->get_quant();	
        
        $oLedger=new Acc_Ledger($cn,$ledger);
        if ( $obj==null || $obj->signature == 'ODS'  )
        {
            /* only the details */
            require_once NOALYSS_TEMPLATE.'/ledger_detail_misc.php';
        }
        elseif ( $obj->signature=='ACH')
        {
            require_once NOALYSS_TEMPLATE.'/ledger_detail_ach.php';
        }
        elseif ($obj->signature=='FIN')
        {
            require_once NOALYSS_TEMPLATE.'/ledger_detail_fin.php';
        }
        elseif ( $obj->signature=='VEN')
        {
            require_once NOALYSS_TEMPLATE.'/ledger_detail_ven.php';
        }
    }
    catch (Exception $e)
    {
        record_log($e->getTraceAsString());
        echo Icon_Action::close($div);
        echo '<h2 class="error">'._("Désolé il y a une erreur").'</h2>';
    }
    $html=ob_get_contents();
    ob_end_clean();

    break;
    /////////////////////////////////////////////////////////////////////////////
    // form for the file
    /////////////////////////////////////////////////////////////////////////////
case 'file':
    $op->get();
    $obj=$op->get_quant();	/* return an obj. ACH / FIN or VEN or null if nothing is found*/
    
    $repo = new Database();
    $theme = $repo->get_value("select the_filestyle from theme where the_name=$1", array($_SESSION['g_theme']));
    html_min_page_start($theme);

    // if there is a receipt document
    if ( $obj->det->jr_pj_name=='')
    {
        if ( ! isset($_REQUEST['ajax']) ) {
                echo '<div class="op_detail_frame">';
        }else {
                echo "<div>";

        }
        if ( $access=='W')
        {
            echo '<FORM METHOD="POST" ENCTYPE="multipart/form-data" id="form_file">';

            $sp=new ISpan('file'.$div);
            $sp->style="display:none;background-color:red;color:white;font-size:12px";
            $sp->value=_("Chargement");
            echo $sp->input();
            echo HtmlInput::hidden('act','loadfile');
            echo dossier::hidden();
            echo HtmlInput::hidden('jr_id',$jr_id);
            echo HtmlInput::hidden('div',$div);

            echo '<INPUT TYPE="FILE" name="pj" onchange="getElementById(\'file'.$div.'\').style.display=\'inline\';submit(this);">';
            echo '</FORM>';
        }
        else
        {
            if (!isset($_REQUEST['ajax']))
            {
                    echo '<div class="op_detail_frame">';
            }
            else
            {
                    echo "<div>";
            }
            

            echo _('Aucun fichier');
    }
    echo '</div>';
    exit();
    }
    else
    {
        // There is no document attached to this writing
        //
        if ( ! isset($_REQUEST['ajax']) ) {
                echo '<div class="op_detail_frame">';
        }else {
                echo "<div>";

        }
        echo '<div class="op_detail_frame">';
        $x='';
        if ($access=='W' && $g_user->check_action (RMRECEIPT) == 1)
        {
            // Not possible to remove the file thanks a modal dialog box,
            // because of the frameset
            $x=sprintf('<a class="smallbutton" style="margin-left:12;margin-right:12" href="ajax_misc.php?op=ledger&gDossier=%d&div=%s&jr_id=%s&act=rmf" onclick="return confirm(\'Effacer le document ?\')">'.SMALLX.'</a>',
                       $gDossier,$div,$jr_id);
            
        }  
        $filename= $obj->det->jr_pj_name;
        if ( strlen($obj->det->jr_pj_name) > 20 )
        {
            $filename=mb_substr($obj->det->jr_pj_name,0,23);
        }
        echo HtmlInput::show_receipt_document($jr_id,h($filename));
        echo $x;
        echo '</div>';
        echo '</body></html>';
        exit();
    }
/////////////////////////////////////////////////////////////////////////////
// load a file
/////////////////////////////////////////////////////////////////////////////
case 'loadfile':
    if ( $access == 'W' && isset ($_FILES))
    {
        $cn->start();
        // remove the file
        $grpt=$cn->get_value('select jr_grpt_id from jrn where jr_id=$1',array($jr_id));
        $cn->save_receipt($grpt);
        $cn->commit();
        // Show a link to the new file
        $op->get();
        $obj=$op->get_quant();	/* return an obj. ACH / FIN or VEN or null if nothing is found*/

        echo "<html><head>";
        $repo=new Database();
        $theme=$repo->get_value("select the_filestyle from theme where the_name=$1",array($_SESSION['g_theme']));
        echo    "   <LINK REL=\"stylesheet\" type=\"text/css\" href=\"$theme\" media=\"screen\">";
        echo "</head>";
		if ( ! isset($_REQUEST['ajax']) ) echo "<body class=\"op_detail_frame\">"; else echo "<body>";
		echo "<h2>"._("Document")."</h2>";
        echo '<div class="op_detail_frame">';
        
        // check if the user can remove a document
        if ($g_user->check_action (RMRECEIPT) == 1) {
            // Not possible to remove the file thanks a modal dialog box,
            // because of the frameset
            $x=sprintf('<a class="mtitle" class="notice" style="margin-left:12;margin-right:12px" href="ajax_misc.php?op=ledger&gDossier=%d&div=%s&jr_id=%s&act=rmf" onclick="return confirm(\'Effacer le document ?\')">'.SMALLX.'</a>',
                   $gDossier,$div,$jr_id);
            echo $x;
        }
        $filename= $obj->det->jr_pj_name;
        echo HtmlInput::show_receipt_document($jr_id,h($filename));

        echo '</div>';

    }
    exit();
/////////////////////////////////////////////////////////////////////////////
// remove a file
/////////////////////////////////////////////////////////////////////////////
case 'rmf':
    if (   $access == 'W' && $g_user->check_action (RMRECEIPT) == 1)
    {
        echo "<html><head>";
        $repo=new Database();
        $theme=$repo->get_value("select the_filestyle from theme where the_name=$1",array($_SESSION['g_theme']));
        echo    "   <LINK REL=\"stylesheet\" type=\"text/css\" href=\"$theme\" media=\"screen\">";
        echo "</head><body class=\"op_detail_frame\">";
		echo "<h2>"._("Document")."</h2>";
        echo '<div class="op_detail_frame">';
        echo '<FORM METHOD="POST" ENCTYPE="multipart/form-data" id="form_file">';
        $sp=new ISpan('file'.$div);
        $sp->style="display:none;width:155;height:15;background-color:red;color:white;font-size:10";
        $sp->value=_("Chargement");
        echo $sp->input();

        echo HtmlInput::hidden('act','loadfile');
        echo dossier::hidden();
        echo HtmlInput::hidden('jr_id',$jr_id);
        echo HtmlInput::hidden('div',$div);

        echo '<INPUT TYPE="FILE" name="pj" onchange="getElementById(\'file'.$div.'\').style.display=\'inline\';submit(this);">';
        echo '</FORM>';
        $ret=$cn->exec_sql("select jr_pj from jrn where jr_id=$1",array($jr_id));
        if (Database::num_row($ret) != 0)
        {
            $r=Database::fetch_array($ret,0);
            $old_oid=$r['jr_pj'];
            if (strlen($old_oid) != 0)
            {
                // check if this pj is used somewhere else
                $c=$cn->count_sql("select * from jrn where jr_pj=".$old_oid);
                if ( $c == 1 )
                    $cn->lo_unlink($old_oid);
            }
            $cn->exec_sql("update jrn set jr_pj=null, jr_pj_name=null, ".
                          "jr_pj_type=null  where jr_id=$1",array($jr_id));
        }
    }
    echo '</div>';
    exit();
/////////////////////////////////////////////////////////////////////////////
// Save operation detail
/////////////////////////////////////////////////////////////////////////////
case 'save':
    ob_start();
    try
    {
        $cn->start();
        if ( $access=="W")
        {
	  if (isset($_POST['p_ech']) )
	    {
	      $ech=$_POST['p_ech'];
	      if ( trim($ech) != '' && isDate($ech) != null)
		{
		  $cn->exec_sql("update jrn set jr_ech=to_date($1,'DD.MM.YYYY') where jr_id=$2",
				array($ech,$jr_id));

		}
	      else
		{
		  $cn->exec_sql("update jrn set jr_ech=null where jr_id=$1",
				array($jr_id));

		}
	    }
            
	  if (isset($_POST['p_date_paid']) )
	    {
	      $ech=$_POST['p_date_paid'];
	      if ( trim($ech) != '' && isDate($ech) != null)
		{
		  $cn->exec_sql("update jrn set jr_date_paid=to_date($1,'DD.MM.YYYY') where jr_id=$2",
				array($ech,$jr_id));

		}
	      else
		{
		  $cn->exec_sql("update jrn set jr_date_paid=null where jr_id=$1",
				array($jr_id));

		}
	    }
            
            $cn->exec_sql("update jrn set jr_comment=$1,jr_pj_number=$2,jr_date=to_date($4,'DD.MM.YYYY'),jr_optype=$5 where jr_id=$3",
                          array($_POST['lib'],$_POST['npj'],$jr_id,$_POST['p_date'],$_POST['jr_optype']));
	    $cn->exec_sql("update jrnx set j_date=to_date($1,'DD.MM.YYYY') where j_grpt in (select jr_grpt_id from jrn where jr_id=$2)",
			  array($_POST['p_date'],$jr_id));
	    $cn->exec_sql('update operation_analytique set oa_date=j_date from jrnx
				where
				operation_analytique.j_id=jrnx.j_id  and
				operation_analytique.j_id in (select j_id
						from jrnx join jrn on (j_grpt=jr_grpt_id)
						where jr_id=$1)
						',array($jr_id));
	    $cn->exec_sql("select comptaproc.jrn_add_note($1,$2)",
			  array($jr_id,$_POST['jrn_note']));
            $rapt=$_POST['rapt'];

            if ( $g_parameter->MY_UPDLAB=='Y' && isset ($_POST['j_id']))
            {
                $a_rowid=$_POST["j_id"];
                for ($e=0;$e<count($a_rowid);$e++)
                {
                    $id="e_march".$a_rowid[$e]."_label";
                    $cn->exec_sql('update jrnx set j_text=$1 where j_id=$2',  array(strip_tags($_POST[$id]),$a_rowid[$e]));
                }
            }
            if (trim($rapt) != '')
            {
                $rec=new Acc_Reconciliation ($cn);
                $rec->set_jr_id($jr_id);

                if (strpos($rapt,",") != 0 )
                {
                    $aRapt=explode(',',$rapt);
                    /* reconcialition */
                    foreach ($aRapt as $rRapt)
                    {
                        if ( isNumber($rRapt) == 1 )
                        {
                            // Add a "concerned operation to bound these op.together
                            $rec->insert($rRapt);
                        }
                    }
                }
                else
                    if ( isNumber($rapt) == 1 )
                    {
                        $rec->insert($rapt);
                    }
            }
              if ( isset($_POST['ipaid']))
              {
                  $cn->exec_sql("update jrn set jr_rapt='paid' where jr_id=$1",array($jr_id));
              }
              else
              {
                  $cn->exec_sql("update jrn set jr_rapt=null where jr_id=$1",array($jr_id));
              }
            ////////////////////////////////////////////////////
            // CA
            //////////////////////////////////////////////////
            $owner = new Noalyss_Parameter_Folder($cn);
            if ( $owner->MY_ANALYTIC != "nu" && isset ($_POST['op']) )
            {
                // for each item, insert into operation_analytique */
                $opanc=new Anc_Operation($cn);
                $opanc->save_update_form($_POST);
            }
            //////////////////////////////////////////////////////////////////
            //Save other info
            //////////////////////////////////////////////////////////////////
            $op->save_info($_POST['OTHER'],'OTHER');
            $op->save_info($_POST['BON_COMMANDE'],'BON_COMMANDE');
            
            ///////////////////////////////////////////////////////////////////
            // Save related
            //////////////////////////////////////////////////////////////////
            $related=$http->post("related","string");
            if ($related == "0" )                
                throw new Exception('Parameter not send -> related'.__FILE__.__LINE__,10);
            $op->insert_related_action($related);

        }
        echo _('Opération sauvée');
        $cn->commit();
    }
    catch (Exception $e)
    {
        record_log($e->getTraceAsString());
      if ( DEBUG )   echo $e->getMessage();
      alert(_( "Changement impossible: on ne peut pas changer la date dans une période fermée"));
    }
    $html=ob_get_contents();
    ob_end_clean();

    break;
    ////////////////////////////////////////////////////////////////////////////
    // remove a reconciliation
    ////////////////////////////////////////////////////////////////////////////
case 'rmr':
    if ( $access=='W')
    {
        $rec=new Acc_Reconciliation($cn);
        $rec->set_jr_id($jr_id);
        $rec->remove($_GET['jr_id2']);
    }
    break;
    ////////////////////////////////////////////////////////////////////////////
    // ask for a date for reversing the operation
    ////////////////////////////////////////////////////////////////////////////
case 'ask_extdate':
    $date=new IDate('p_date');
    $html.="<form id=\"form_".$div."\" onsubmit=\"return reverseOperation(this);\">";
    $html.=HtmlInput::hidden('jr_id',$_REQUEST['jr_id']).
            HtmlInput::hidden('div',$div).
            dossier::hidden().
            HtmlInput::hidden('act','reverseop');
    
    $html.='<h2 class="info">'._('entrez une date').' </H2>'.$date->input();
    $html.=HtmlInput::submit('x','accepter');
    $html.=HtmlInput::button_close($div);
    $html.='</form>';
    break;
    ////////////////////////////////////////////////////////////////////////////
    // Reverse an operation
    ////////////////////////////////////////////////////////////////////////////
case 'reverseop':
    if ( $access=='W')
    {
        ob_start();
        try
        {
            $ext_date=$http->request("ext_date","date");
            $ext_label=$http->request("ext_label");
            $cn->start();
            $oLedger=new Acc_Ledger($cn,$ledger);
            $oLedger->jr_id=$jr_id;
            if ( trim($ext_label) == "" ) {
                $ext_label=_("Extourne").$cn->get_value("select jr_comment from jrn where jr_id=$1",[$jr_id]);
            }
            $oLedger->reverse($ext_date,$ext_label);
            $cn->commit();
            echo _("Opération extournée");
        }
        catch (Exception $e)
        {
            record_log($e->getTraceAsString());
            echo $e->getMessage();
            $cn->rollback();
        }
    }
    $html=ob_get_contents();
    ob_end_clean();
    break;
}
$html=escape_xml($html);
 if ( ! headers_sent()) {     header('Content-type: text/xml; charset=UTF-8');} else { echo "HTML".unescape_xml($html);}
 
 echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<data>
<ctl>$div</ctl>
<code>$html</code>
</data>
EOF;
