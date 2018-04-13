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

require_once  NOALYSS_INCLUDE.'/class/user.class.php';
require_once NOALYSS_INCLUDE.'/class/acc_bilan.class.php';

global $g_captcha,$g_failed,$g_succeed;
$cn=Dossier::connect();
$exercice=$g_user->get_exercice();
echo '<div class="content">';
$sql_year="  j_tech_per in (select p_id from parm_periode where p_exercice='".$g_user->get_exercice()."')";
echo '<div class="myfieldset"><h1 class="legend">'._('Vérification des journaux').'</h1>';

$sql="
    with jdebit as (
	select sum (j_montant) as amount,j_debit , jr_def_id 
	from jrnx join jrn on (j_grpt=jr_grpt_id)
	where 
	$sql_year
	and
	j_debit='t'
	group by jr_def_id,j_debit
	),
jcredit as (
	select sum (j_montant) as amount,j_debit , jr_def_id 
	from jrnx join jrn on (j_grpt=jr_grpt_id)
	where 
	$sql_year
	and 
	j_debit='f'
	group by jr_def_id,j_debit
	)
select jrn_def_id,
	jrn_def_name,
	jdebit.amount as deb,
	jcredit.amount as cred
	from jrn_def 
join  jdebit on (Jdebit.jr_def_id=jrn_def.jrn_def_id)
join  jcredit on (jcredit.jr_def_id=jrn_def.jrn_def_id)
where jcredit.amount<>jdebit.amount
order by jrn_def_name
    ";
$res=$cn->exec_sql($sql);
$jrn=Database::fetch_all($res);

$nb_jrn= count($jrn);
if ( $jrn ===false  ) {
    echo $g_succeed." "._("Aucune anomalie dans les montants des journaux");
}
echo '<table class="result">';
echo tr(th(_('Journal')).th(_('Débit'),' style="display:right"').th(_("Crédit"),' style="display:right"').th(_("Différence"),' style="display:right"').th(''));

$nb_jrn=count($jrn);
if ( $jrn === false) $nb_jrn=0;
$ix=0;
for ($i=0;$i<$nb_jrn;$i++)
{
    $l=$jrn[$i];
    $id=$l['jrn_def_id'];
    $name=$l['jrn_def_name'];
    $deb=$l['deb'];
    $cred=$l['cred'];
    $result = $g_failed;
    $class=($ix%2==0)?'odd':"even";
    print tr(td($name).td(nbm($deb),'class="num"').td(nbm($cred),'class="num"').td(nbm($result),'class="num"').td($result),"class=\"$class\"");
    $ix++;

}

$deb=$cn->get_value("select sum (j_montant) from jrnx where j_debit='t' and $sql_year ");
$cred=$cn->get_value("select sum (j_montant) from jrnx where j_debit='f' and $sql_year ");

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
    $sql_year_target=" j_tech_per in (select p_id from parm_periode where p_exercice='".$g_user->get_exercice()."')";

    $sql_fiche_id="
        select count(*),f_id from (
        select distinct 
                f_id,j_poste 
        from jrnx 
        where
        $sql_year_target
) as m
group by f_id
having count(*) > 1
           ";
    
    $a_fiche_id=$cn->get_array($sql_fiche_id);
    
    $sql_poste="select distinct j_poste,pcm_lib from jrnx join tmp_pcmn on (pcm_val=j_poste) where f_id =$1 and $sql_year";
    $sql_qcode="select ad_value as qcode from fiche_detail where f_id=$1 and ad_id=".ATTR_DEF_QUICKCODE;
    $res=$cn->prepare('get_poste',$sql_poste);
    $resQcode=$cn->prepare('get_qcode',$sql_qcode);
    if ( $res == false || $resQcode == false ) {
        echo "ERREUR ".__FILE__.":".__LINE__."prepare failed";
    }
    echo _("Résultat");
    if (count($a_fiche_id) == 0) { echo " OK $g_succeed";}  else { echo " "._('Attention ').$g_failed;}
    ?>
    <ol>
    <?php
    for ($i=0;$i<count($a_fiche_id);$i++):
        $poste=$cn->execute('get_poste',array($a_fiche_id[$i]['f_id']));
        $tmp_qcode=$cn->execute('get_qcode',array($a_fiche_id[$i]['f_id']));
        $qcode=Database::fetch_all($tmp_qcode);
        if ( $qcode[0]['qcode']=="") {
            continue;
        }
    ?>
        <li><?php 
                echo HtmlInput::card_detail($qcode[0]['qcode'],$qcode[0]['qcode'],' style="display:inline"') ;
                echo " ";
                echo HtmlInput::history_card($a_fiche_id[$i]["f_id"],_("Hist."),' display:inline'); 
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
        jrnx.f_id is null and $sql_year order by 1
";
       
$sql_concerned_operation="select vw.f_id,jrnx.j_date,jrnx.j_id,jrn.jr_id,jrnx.j_poste,jr_internal ,jr_comment
    from vw_poste_qcode as vw 
    join jrnx using (j_poste) 
    join jrn on (jrnx.j_grpt=jrn.jr_grpt_id) 
    where 
    jrnx.f_id is null and vw.f_id= $1 and $sql_year";
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