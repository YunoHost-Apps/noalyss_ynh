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
 *
 *
 * \brief Misc Operation for analytic accountancy
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/lib/ihidden.class.php';
require_once NOALYSS_INCLUDE.'/lib/iselect.class.php';
require_once  NOALYSS_INCLUDE.'/class/anc_operation.class.php';
require_once  NOALYSS_INCLUDE.'/class/anc_plan.class.php';
require_once  NOALYSS_INCLUDE.'/class/anc_group_operation.class.php';

global $g_user;
$http=new HttpInput();

$str_dossier=Dossier::get();
$pa=new Anc_Plan($cn);
$m=$pa->get_list();
if ( ! $m )
{

    echo '<div ><h2 class="error">'._('Aucun plan analytique défini').'</h2></div>';
    return;
}



//----------------------------------------------------------------------
// show the left menu
//----------------------------------------------------------------------
echo '
<div class="menu2">
<table clsas="mtitle">
<tr>
<td  class="mtitle" >
<A class="mtitle" HREF="?ac='.$_REQUEST['ac'].'&new&'.$str_dossier.'"> '._('Nouveau').' </A>
</td>
<td  class="mtitle" >
<A class="mtitle" HREF="?ac='.$_REQUEST['ac'].'&see&'.$str_dossier.'">'._('Liste opérations').' </A
</td>
</tr>
</table>
</div>
';


//----------------------------------------------------------------------
// the pa_id is set
//
//----------------------------------------------------------------------
if ( isset($_GET['see']))
{

    // Show the list for the period
    // and exit
    //-----------------------------
    $a=new Anc_Operation($cn);

    echo '
    <div class="content"  >
    <form method= "get">
    ';

    echo dossier::hidden();
    $hid=new IHidden();

    $hid->name="ac";
    $hid->value=$_REQUEST['ac'];
    echo $hid->input();

    $hid->name="see";
    $hid->value="";
    echo $hid->input();

    $w=new ISelect();
    $w->name="p_periode";
// filter on the current year
    $filter_year=" where p_exercice='".$g_user->get_exercice()."'";

    $periode_start=$cn->make_array("select p_id,to_char(p_start,'DD-MM-YYYY') from parm_periode $filter_year order by  p_start,p_end",1);
    $g_user=new User($cn);
    $current=$http->get("p_periode","number",$g_user->get_periode());
    $w->value=$periode_start;
    $w->selected=$current;
    echo _('Filtrer par période').":".$w->input().HtmlInput::submit('gl_submit','Valider').'</form>';
    echo '<hr>';

    echo '<div class="content" >';
    echo $a->html_table($current);
    echo '</div>';
    return;
}
if ( isset($_POST['save']))
{
    // record the operation and exit
    // and exit
    //-----------------------------
    echo '<div class="redcontent" >'.
    _('Opération sauvée');
    $a=new Anc_Group_Operation($cn);

    $a->get_from_array($_POST);

    $a->save();
    echo $a->show();
    echo '</div>';
    return;
}

if ( isset($_GET['new']))
{
    //show the form for entering a new Anc_Operation
    //------------------------------------------
    $a=new Anc_Group_Operation($cn);

    $wSubmit=new IHidden("p_action","ca_od");
    $wSubmit->table=0;
    echo '<div class="redcontent"  >';
    echo '<form id="anc_od_frm" method="post" onsubmit="return validate_anc(\'anc_od_frm\');return false;">';
    echo dossier::hidden();
    echo $wSubmit->input();
    echo $a->form();
    echo HtmlInput::submit("save",_("Sauver"));
    echo '</form>';
    echo '<div class="info">';
    echo _('Débit').' = <span id="totalDeb"></span>';
    echo _('Crédit').' = <span id="totalCred"></span>';
    echo _('Difference').' = <span id="totalDiff"></span>
    </div>
    ';

    echo '</div>';
    $msg_comment=_("Commentaire vide");
    $msg_date=_("Date invalide");
echo <<<EOF
<script> 
    function validate_anc(p_frm_id) {
    try {
        if ($('pdesc').value.length==0) {
            smoke.alert('$msg_comment');
            return false;
        }
        if ( ! check_date($(p_frm_id)['pdate'].value) ) {
            smoke.alert('$msg_date');
            return false;
        }
        } catch (e) {
            smoke.alert(e.message);
        }
        return ;
    }
    
</script>;    

EOF;
    
   return;
}

?>
<div class="redcontent">
