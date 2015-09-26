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
 * \brief Verify the saldo of ledger: independant file
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');

require_once  NOALYSS_INCLUDE.'/class_user.php';
require_once NOALYSS_INCLUDE.'/class_acc_bilan.php';

global $g_captcha,$g_failed,$g_succeed;

$cn=new Database(dossier::id());
$exercice=$g_user->get_exercice();
echo '<div class="content">';

$sql_year=" and j_tech_per in (select p_id from parm_periode where p_exercice='".$g_user->get_exercice()."')";
echo '<div class="myfieldset"><h1 class="legend">'._('Vérification des journaux').'</h1>';

$sql="select jrn_def_id,jrn_def_name from jrn_def";
$res=$cn->exec_sql($sql);
$jrn=Database::fetch_all($res);
echo '<table class="result">';
echo tr(th(_('Journal')).th(_('Débit'),' style="display:right"').th(_("Crédit"),' style="display:right"').th(_("Différence"),' style="display:right"').th(''));
$ix=0;
foreach ($jrn as $l)
{
    $id=$l['jrn_def_id'];
    $name=$l['jrn_def_name'];
    $deb=$cn->get_value("select sum (j_montant) from jrnx where j_debit='t' and j_jrn_def=$id $sql_year ");
    $cred=$cn->get_value("select sum (j_montant) from jrnx where j_debit='f' and j_jrn_def=$id  $sql_year ");

    if ( $cred == $deb )
    {
    $result =$g_succeed;
}
else
{
    $result = $g_failed;
}
    $class=($ix%2==0)?'odd':"even";
    print tr(td($name).td(nbm($deb),'class="num"').td(nbm($cred),'class="num"').td(nbm($result),'class="num"').td($result),"class=\"$class\"");
    $ix++;

}

$deb=$cn->get_value("select sum (j_montant) from jrnx where j_debit='t' $sql_year ");
$cred=$cn->get_value("select sum (j_montant) from jrnx where j_debit='f' $sql_year ");

if ( $cred == $deb )
{
    $result =$g_succeed;
}
else
{
    $result = $g_failed;
}
$class=($ix%2==0)?'odd':"even";
print tr(td(_('Grand livre')).td(nbm($deb),' class="num"').td(nbm($cred),' class="num"').td(nbm($result),' class="num"')
        .td($result),"class=\"$class\"");

echo '</table>';
echo '</div>';
echo '<hr>';
echo '<div class="myfieldset"><h1 class="legend">'._('Vérification des comptes').'</h1>';
$bilan=new Acc_Bilan($cn);
$periode=new Periode($cn);
list ($start_periode,$end_periode)=$periode->get_limit($exercice);
$bilan->from=$start_periode->p_id;
$bilan->to=$end_periode->p_id;
$bilan->verify();
echo '</div>';
?>
<hr>
<div class="myfieldset">
    <h1 class="legend">
        <?php echo _("Vérification des fiches").'</legend>';?>
    </h1>
    <h2>
        <?php echo _('Fiches ayant changé de poste comptable');?>
    </h2>
    <?php
    $sql_year_target=" target.j_tech_per in (select p_id from parm_periode where p_exercice='".$g_user->get_exercice()."')";
    $sql_year_source=" source.j_tech_per in (select p_id from parm_periode where p_exercice='".$g_user->get_exercice()."')";

    $sql_qcode="select distinct source.f_id,source.j_qcode 
            from jrnx as source ,jrnx as target 
            where
            source.j_id < target.j_id 
            and source.j_poste<>target.j_poste 
            and source.j_qcode = target.j_qcode
            and $sql_year_source and $sql_year_target
           ";
    $sql_poste="select distinct j_poste,pcm_lib from jrnx join tmp_pcmn on (pcm_val=j_poste) where j_qcode =$1 $sql_year";
    $a_qcode=$cn->get_array($sql_qcode);
    $res=$cn->prepare('get_poste',$sql_poste);
    echo _("Résultat");
    if (count($a_qcode) == 0) { echo " OK $g_succeed";}  else { echo " "._('Attention ').$g_failed;}
    ?>
    <ol>
    <?php
    for ($i=0;$i<count($a_qcode);$i++):
        $poste=$cn->execute('get_poste',array($a_qcode[$i]['j_qcode']));
    ?>
        <li><?php 
                echo HtmlInput::card_detail($a_qcode[$i]["j_qcode"],$a_qcode[$i]["j_qcode"],' style="display:inline"') ;
                echo " ";
                echo HtmlInput::history_card($a_qcode[$i]["f_id"],_("Hist."),' display:inline'); 
                        
                ?>
        
        </li>
        <ul>
        <?php $all_dep=Database::fetch_all($poste); 
        for ($e=0;$e<count($all_dep);$e++):
        ?>
            <li>
                <?php echo HtmlInput::history_account($all_dep[$e]['j_poste'],$all_dep[$e]['j_poste'],' display:inline ')?>
                <?php echo h($all_dep[$e]['pcm_lib'])?>
            </li>
        <?php
        endfor;
        ?>
        </ul>
    <?php
    endfor;
    ?>
    </ol>
  
    <h2><?php echo _('Poste comptable utilisé sans la fiche correspondante') ?></h2>
<p class="notice">
        <?php echo _('Cela pourrait causer des différences entre les balances par fiches et celle par postes comptables, utilisez le plugin 
         "OUTIL COMPTABLE" pour corriger');
        ?>
</p>

<?php
$sql_account_used="
    select distinct vw.f_id,j_poste ,vw_name,quick_code
    from vw_poste_qcode as vw 
    join jrnx using (j_poste) 
    join vw_fiche_attr as v_attr on (vw.f_id=v_attr.f_id)
    join jrn on (jrnx.j_grpt=jrn.jr_grpt_id) 
    where 
        jrnx.f_id is null  $sql_year order by 1
";
       
$sql_concerned_operation="select vw.f_id,jrnx.j_date,jrnx.j_id,jrn.jr_id,jrnx.j_poste,jr_internal ,jr_comment
    from vw_poste_qcode as vw 
    join jrnx using (j_poste) 
    join jrn on (jrnx.j_grpt=jrn.jr_grpt_id) 
    where 
    jrnx.f_id is null and vw.f_id= $1 $sql_year";
$a_account_used=$cn->get_array($sql_account_used);
$nb_account_used=count ($a_account_used);
    if ( $nb_account_used == 0 ) 
    {
        echo _('Résultat')." ".$g_succeed;
    }
    $ret=$cn->prepare('get_operation',$sql_concerned_operation);
    
?>
    
        <?php for ($i=0;$i<$nb_account_used;$i++): ?>
        <h3>
            <?php 
                echo _('Poste comptable ').$a_account_used[$i]['j_poste']._(' pour la fiche ').$a_account_used[$i]['quick_code']." ".$a_account_used[$i]['vw_name'];
            ?>
        </h3>
        <?php
            $ret_operation=$cn->execute('get_operation',array($a_account_used[$i]['f_id']));
                 $a_operation=Database::fetch_all($ret_operation); 
                 $nb_operation=count($a_operation);
        ?>
        <table class="result">
            <?php for ($x=0;$x<$nb_operation;$x++):?>
        <tr>
            <td>
                <?php echo $a_operation[$x]['j_date']; ?>
            </td>
            <td>
                <?php echo h($a_operation[$x]['jr_comment']); ?>
            </td>
            <td>
                <?php echo HtmlInput::detail_op($a_operation[$x]['jr_id'],$a_operation[$x]['jr_internal']); ?>
            </td>
            <td>
              
            </td>
        </tr>
        <?php endfor;?>
        </table>
    <?php endfor;?>
</div>