<hr>
<?php
/**
//This file is part of NOALYSS and is under GPL 
//see licence.txt
*/
/**
 * @brief show the common parts of operation details 
 * 
 * Variables : $div = popup or box (det[0-9]
 * 
 */
$cn=Dossier::connect();
// Contains all the linked actions
$a_followup = Follow_Up::get_all_operation($jr_id);
//
// Contains all the linked operations
$oRap=new Acc_Reconciliation($cn);
$oRap->jr_id=$jr_id;
$aRap=$oRap->get();

// Detail of operation
 $detail = new Acc_Misc($cn, $obj->jr_id);
 $detail->get();
 
 // find out exercice
 $periode_id=new Periode($cn,$detail->det->jr_tech_per);
 $exercice=$periode_id->get_exercice();
 
 
 $nb_document=($detail->det->jr_pj_name != "")?1:0;


// Array of tab
// 
$a_tab['writing_div']=array('id'=>'writing_div'.$div,'label'=>_('Ecriture Comptable'),'display'=>'none');
$a_tab['info_operation_div']=array('id'=>'info_operation_div'.$div,'label'=>_('Information'),'display'=>'none');
$a_tab['linked_operation_div']=array('id'=>'linked_operation_div'.$div,'label'=>_('Opérations liées').'('.count($aRap).')','display'=>'none');
$a_tab['document_operation_div']=array('id'=>'document_operation_div'.$div,'label'=>_('Document').'('.$nb_document.')','display'=>'block');
$a_tab['linked_action_div']=array('id'=>'linked_action_div'.$div,'label'=>_('Actions Gestion').'('.count($a_followup).')','display'=>'none');
$a_tab['analytic_div']=array('id'=>'analytic_div'.$div,'label'=>_('Comptabilité Analytique'),'display'=>'none');

 
// show tabs
if ( $div != "popup") :
 $a_tab['document_operation_div']['display']='block';
?>
<ul  class="tabs">
    <?php foreach ($a_tab as $idx=>$a_value): ?>
    <?php 
        $class=($a_value['display']=='block') ?"tabs_selected":"tabs"
    ?>
    <li class="<?php echo $class?>">
        <?php $div_tab_id=$a_value['id'];?>
        <a href="javascript:void(0)" onclick="unselect_other_tab(this.parentNode.parentNode);var tab=Array('writing_div<?php echo $div?>','info_operation_div<?php echo $div?>','linked_operation_div<?php echo $div?>','document_operation_div<?php echo $div?>','linked_action_div<?php echo $div?>','analytic_div<?php echo $div?>');this.parentNode.className='tabs_selected' ;show_tabs(tab,'<?php echo $div_tab_id; ?>');"><?php echo _($a_value['label'])?></a>
    </li>
    <?php    endforeach; ?>
</ul>
<div style="clear:both"></div>
<?php
else :
    foreach ($a_tab as $idx=>$a_value):
    $a_tab[$idx]['display']='block';
    endforeach; 
endif;
?>


<?php
    $cmd=new IText('BON_COMMANDE',$obj->info->command);
    $other=new IText('OTHER',$obj->info->other);
?>
        <div id="writing_div<?php echo $div;?>" class="myfieldset" style="display:<?php echo $a_tab['writing_div']['display']?>">
          <?php 
          // display title only in popup
          if ($div == 'popup') :
          ?> 
                <h1 class="legend"><?php echo $a_tab['writing_div']['label']?></h1>
          <?php endif; ?>

<div class="content">
            <?php
           
            ?>
            <table class="result">
                <tr>
                    <?php
                    echo th(_('Poste Comptable'));
                    echo th(_('Quick Code'));
                    echo th(_('Libellé'));
                    echo th(_('Débit'), ' style="text-align:right"');
                    echo th(_('Crédit'), ' style="text-align:right"');
                    echo '</tr>';
                    for ($e = 0; $e < count($detail->det->array); $e++)
                    {
                        $row = '';
                        $q = $detail->det->array;
                        $view_history = HtmlInput::history_account($q[$e]['j_poste'], $q[$e]['j_poste'], "", $exercice);
                                
                        $row.=td($view_history);
                        if ($q[$e]['j_qcode'] != '')
                        {
                            $fiche = new Fiche($cn);
                            $fiche->get_by_qcode($q[$e]['j_qcode']);
                            $view_history = HtmlInput::history_card($fiche->id,  $q[$e]['j_qcode'],"",$exercice);
                        } else
                            $view_history = '';
                        $row.=td($view_history);
                        
                        if ($q[$e]['j_text']=="")
                        {
                            if ($q[$e]['j_qcode'] != '')
                            {
                            // nom de la fiche
                                $ff = new Fiche($cn);
                                $ff->get_by_qcode($q[$e]['j_qcode']);
                                $row.=td($ff->strAttribut(h(ATTR_DEF_NAME)));
                            } else
                            {
                                // libellé du compte
                                $name = $cn->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1', array($q[$e]['j_poste']));
                                $row.=td(h($name));
                            }
                        }
                        else 
                            $row.=td(h($q[$e]['j_text']));
                        
                        $montant = td(nbm($q[$e]['j_montant']), 'class="num"');
                        $row.=($q[$e]['j_debit'] == 't') ? $montant : td('');
                        $row.=($q[$e]['j_debit'] == 'f') ? $montant : td('');
                        $class=($e%2==0)?' class="even"':'class="odd"';

                        echo tr($row,$class);
                    }
                    ?>
            </table>
        </div>
</div>
<div id="info_operation_div<?php echo $div;?>" class="myfieldset" style="display:<?php echo $a_tab['info_operation_div']['display']?>">
    <?php 
          // display title only in popup
          if ($div == 'popup') :
          ?> 
                <h1 class="legend"><?php echo $a_tab['info_operation_div']['label']?></h1>
          <?php endif; ?>
    <table>
        <tr>
            <td><?php echo _(" Bon de commande")?>   :</td><td> <?php echo Icon_Action::infobulle(31)." ".$cmd->input();  ?></td>
        </tr>
        <tr>
            <td> <?php echo _("Autre information")?> : </td><td><?php echo Icon_Action::infobulle(30)." ".$other->input();?></td>
        </tr>
        <tr>
            <td>
                <?=_("Type opération")?>
            </td>
            <td>
                <?php
                    // Opération type
                    
                    echo Acc_Operation::select_operation_type($detail->det->jr_optype)->input();
                ?>
            </td>
        </tr>
    </table>
</div>
<div id="linked_operation_div<?php echo $div;?>" style="display:<?php echo $a_tab['linked_operation_div']['display']?>" class="myfieldset">
 <?php 
          // display title only in popup
          if ($div == 'popup') :
          ?> 
                <h1 class="legend"><?php echo $a_tab['linked_operation_div']['label']?></h1>
          <?php endif; ?>
<?php 

if ($aRap  != null ) {
    $amount_tva_include=(isset($tvac))?$tvac:$detail->det->jr_montant;
  $tableid="tb".$div;
  $total_rec=0;
  echo '<table id="'.$tableid.'">';
  for ($e=0;$e<count($aRap);$e++)  {
    $opRap=new Acc_Operation($cn);
    $opRap->jr_id=$aRap[$e];
    $internal=$opRap->get_internal();
    $array_jr=$cn->get_array('select jr_date,jr_pj_number,jr_montant,jr_comment from jrn where jr_id=$1',array($aRap[$e]));
    $amount=$array_jr[0]['jr_montant'];
    $total_rec=bcadd($total_rec,$amount);
    $str="modifyOperation(".$aRap[$e].",".$gDossier.")";
    
    // If write access , allow to drop Reconciles operations
    if ( $access=='W') {
            $rmReconciliation=new IButton('rmr');
            $rmReconciliation->label=SMALLX;
            $rmReconciliation->class="tinybutton";
            $rmReconciliation->javascript="return confirm_box(null,'"._("vous confirmez?")."',";
            $rmReconciliation->javascript.=sprintf('function () { dropLink(\'%s\',\'%s\',\'%s\',\'%s\');deleteRowRec(\'%s\',$(\'row%d\'));})',
					  $gDossier,
					  $div,
					  $jr_id,
					   $aRap[$e],
					   $tableid,
                                          $e
					  );

      $remove=$rmReconciliation->input();
    }
    else
      $remove='';
    
    $comment=strip_tags($array_jr[0]['jr_comment']);
    $pj_nb=h($array_jr[0]['jr_pj_number']);
    echo tr (td(format_date($array_jr[0]['jr_date'])).
            td('<a class="line" href="javascript:void(0)" onclick="'.$str.'" >'.$internal.'</A>').
            td($pj_nb).
            td($comment).
            td(_('montant').'='.nbm($amount)).
            td(_('delta').'='.nbm(bcsub($amount_tva_include,$total_rec))).
            td($remove),' id = "row'.$e.'"');
  }
  echo '</table>';
}
?>
<?php 
if ( $access=='W') {
     $wConcerned=new IConcerned("rapt".$div);
     $wConcerned->amount_id=$obj->det->jr_montant;
     $wConcerned->div="search_reconcile";
    echo $wConcerned->input();

}
?>
</div>
<div id="linked_action_div<?php echo $div;?>" style="display:<?php echo $a_tab['linked_action_div']['display']?>" class="myfieldset">
         <?php 
  // display title only in popup
  if ($div == 'popup') :
  ?> 
        <h1 class="legend"><?php echo $a_tab['linked_action_div']['label']?></h1>
  <?php endif; ?>
<?php 
/**
 * show possible linked actions
 */
echo '<ul style="list-style-type:square;">';
for ($i = 0; $i < count($a_followup); $i++)
{
    $remove='';
    if ( $access=='W') $remove=HtmlInput::button_action_remove_operation($a_followup[$i]['ago_id']);
        if ( $div == 'popup')
        {
                echo '<li id="op'.$a_followup[$i]['ago_id'].'">'.HtmlInput::detail_action($a_followup[$i]['ag_id'], h($a_followup[$i]['ag_ref']." ".$a_followup[$i]['ag_title']),0).$remove.'</li>';
        }
        else
        {
                echo '<li id="op'.$a_followup[$i]['ago_id'].'">'.HtmlInput::detail_action($a_followup[$i]['ag_id'], h($a_followup[$i]['ag_ref']." ".$a_followup[$i]['ag_title']),1).$remove.'</li>';
        }
}
echo '</ul>';
$related=new IRelated_Action('related');
$related->id='related'.$div;
 if ( $access=='W') echo $related->input();
echo '</div>';
?>

<?php 

require_once NOALYSS_TEMPLATE.'/ledger_detail_file.php';
?>


<div id="analytic_div<?php echo $div;?>" style="overflow:auto;display:<?php echo $a_tab['analytic_div']['display']?>">
   <?php
    if ($div == 'popup') :
    ?> 
        <h1 class="legend"><?php echo $a_tab['analytic_div']['label']?></h1>
  <?php endif; ?>
    <?php if ( $owner->MY_ANALYTIC != "nu") : 
        if ( strpos($str_anc,'<td>') == true ):
        ?>
     
                <table class="result">
                            <?php echo $str_anc;?>
                </table>
        <?php else: ?>
        <span class="notice">
        <?php echo _('Aucune donnée'); ?>
            </span>
        <?php endif;?>
<?php else:?>
    <span class="notice">
    <?php echo _('Non utilisée'); ?>
    </span>
<?php endif;?>
</div>

<hr>
<?php 
      echo '<p style="text-align:center">';

if ( $div != 'popup' ) {
  $a=new IButton('Fermer',_('Fermer'));
  $a->label=_("Fermer");
  $a->javascript="removeDiv('".$div."')";
  echo $a->input();
} else {
    echo HtmlInput::hidden('p_jrn',$oLedger->id);
}

?>

<?php 

/**
 * if you can write
 */
  if ( $access=='W') {
  echo HtmlInput::submit('save',_('Sauver'),'onClick="return verify_ca(\'popup\');"');
  $owner=new Noalyss_Parameter_Folder($cn);
  if ($owner->MY_ANALYTIC != 'nu' /*&& $div=='popup' */){
    echo '<input type="button" class="smallbutton" value="'._('verifie CA').'" onClick="verify_ca(\''.$div.'\');">';
  }

  $per=new Periode($cn,$obj->det->jr_tech_per);
  if ( $per->is_closed() == 0 && $owner->MY_STRICT=='N' && $g_user->check_action(RMOPER)==1)
  {
    $remove=new IButton('Effacer');
    $remove->label=_('Effacer');
    $remove->javascript="return confirm_box(null,'Vous confirmez effacement ?',function () {removeOperation('".$obj->det->jr_id."',".dossier::id().",'".$div."')})";
    echo $remove->input();
  }
 //----------------------------------------------------
 // Extourne
 //----------------------------------------------------
  $reverse=new IButton('bext'.$div);
  $reverse->label=_('Extourner');
  $reverse->javascript="g('ext".$div."').style.display='block'";
  echo $reverse->input();
    echo '</p>';
echo '</form>';

  echo '<div id="ext'.$div.'" class="inner_box" style="position:absolute;top:40px;display:none">';
  $date=new IDate('ext_date');
  $extourne_label=new IText("ext_label");
  $extourne_label->size=40;
  $r="<form id=\"form_".$div."\" onsubmit=\"return false;\">";
  $r.=HtmlInput::hidden('jr_id',$_REQUEST['jr_id'])
      . HtmlInput::hidden('div',$div).dossier::hidden().HtmlInput::hidden('act','reverseop');
  $r.=HtmlInput::title_box(_('Extourner'), 'ext'.$div, 'hide');
  $r.="<p>";
  $r.= _("Extourner une opération vous permet de l'annuler par son écriture inverse");
  $r.="</p>";

  $r.="<p>";
  $r.=_("entrez une date")." :".$date->input();
  $r.="</p>";

  $r.="<p>";
  $r.=_("Libellé")." :".$extourne_label->input();
  $r.="</p>";

  
  $r.='<p  style="text-align:center">';
  $r.=HtmlInput::submit('x','accepter',
          'onclick="confirm_box($(\'form_'.$div.'\'),\'Vous confirmez  ? \',function () {$(\'form_'.$div.'\').divname=\''.$div.'\';reverseOperation($(\'form_'.$div.'\'))}); return false"');
    $r.="</p>";
  $r.='</form>';
  echo $r;
  echo '</div>';



}else {
    echo '</p>';
}
?>
