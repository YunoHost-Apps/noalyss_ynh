<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><?php 
require_once NOALYSS_INCLUDE.'/template/ledger_detail_top.php';
require_once NOALYSS_INCLUDE.'/class_anc_operation.php';
require_once NOALYSS_INCLUDE.'/class_anc_plan.php';
 $str_anc="";
?>
<?php 
require_once NOALYSS_INCLUDE.'/class_own.php';
require_once  NOALYSS_INCLUDE.'/class_anc_plan.php';
?>
<div class="content" style="padding:0">

    <?php if ( $access=='W') : ?>
<form class="print" onsubmit="return op_save(this);">
   <?php endif; ?>

    <?php echo HtmlInput::hidden('whatdiv',$div).HtmlInput::hidden('jr_id',$jr_id).dossier::hidden();?>
  <table style="width:100%">
      <tr>
          <td>
            <table>
                <tr>
                    <td>
                        <?php
                        $date=new IDate('p_date');
                        $date->value=format_date($obj->det->jr_date);
                         echo td(_('Date')).td($date->input());

                         ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <?php 
                          $itext=new IText('lib');
                          $itext->value=strip_tags($obj->det->jr_comment);
                          $itext->size=40;
                          echo td(_('Libellé')).td($itext->input());


                        ?>
                    </td>
                </tr>
                 <tr>
                     <td>
                        <?php echo td(_('Montant')).td(nbm($obj->det->jr_montant),' class="inum"');?>
                     </td>
                 </tr>
                  <tr>
                      <td>
                        <?php 
                        $itext=new IText('npj');
                        $itext->value=strip_tags($obj->det->jr_pj_number);
                        echo td(_('Pièce')).td($itext->input());
                        ?>
                    </td>
                  </tr>
            </table>
        </td>
                <td style="width:50%;height:100%;vertical-align:top;text-align: center">
                    <table style="width:99%;height:8rem;vertical-align:top;">
                        <tr style="height: 5%">
                            <td style="text-align:center;vertical-align: top">
                                Note
                            </td></tr>
                        <tr>
                            <td style="text-align:center;vertical-align: top">
                                <?php
                                $inote = new ITextarea('jrn_note');
                                $inote->style=' class="itextarea" style="width:90%;height:100%;"';
                                $inote->value = strip_tags($obj->det->note);
                                echo $inote->input();
                                ?>

                            </td>
                        </tr>
                    </table>
                </td>

</tr>
</table>

<div class="myfieldset">
<?php 
  require_once NOALYSS_INCLUDE.'/class_own.php';
  $owner=new Own($cn);
?>
<table class="result">
<tr>
<?php 
    echo th(_('Poste Comptable'));
    echo th(_('Quick Code'));
    echo th(_('Libellé'));
echo th(_('Débit'), 'style="text-align:right"');
echo th(_('Crédit'), 'style="text-align:right"');
    if ($owner->MY_ANALYTIC != 'nu' /* && $div == 'popup' */ ){
      $anc=new Anc_Plan($cn);
      $a_anc=$anc->get_list(' order by pa_id ');
      $x=count($a_anc);
      /* set the width of the col */
       $str_anc.='<tr><th>Code</th><th>Poste</th><th>Montant</th><th colspan="' . $x . '">' . _('Compt. Analytique') . '</th>';

      /* add hidden variables pa[] to hold the value of pa_id */
      $str_anc.= Anc_Plan::hidden($a_anc);
    }
echo '</tr>';
$amount_idx=0;
  for ($e=0;$e<count($obj->det->array);$e++) {
    $row=''; $q=$obj->det->array;
    $view_history= sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:view_history_account(\'%s\',\'%s\')" >%s</A>',
			   $q[$e]['j_poste'], $gDossier, $q[$e]['j_poste']);

    $row.=td($view_history);

    if ( $q[$e]['j_qcode'] !='') {
      $fiche=new Fiche($cn);
      $fiche->get_by_qcode($q[$e]['j_qcode']);
      $view_history= sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:view_history_card(\'%s\',\'%s\')" >%s</A>',
			   $fiche->id, $gDossier, $q[$e]['j_qcode']);
    }
    else
      $view_history='';
    $row.=td($view_history);
	$l_lib = $q[$e]['j_text'] ;

    if ( $l_lib!='')
	{
	 $l_lib=$q[$e]['j_text'];
	}
      else  if ( $q[$e]['j_qcode'] !='') {
      // nom de la fiche
      $ff=new Fiche($cn);
      $ff->get_by_qcode( $q[$e]['j_qcode']);
      $l_lib=$ff->strAttribut(ATTR_DEF_NAME);
    } else {
      // libellé du compte
      $name=$cn->get_value('select pcm_lib from tmp_pcmn where pcm_val=$1',array($q[$e]['j_poste']));
      $l_lib=$name;
    }
    $l_lib=strip_tags($l_lib);
    if ($owner->MY_UPDLAB == 'Y')
    {
        $hidden = HtmlInput::hidden("j_id[]", $q[$e]['j_id']);
        $input = new IText("e_march" . $q[$e]['j_id'] . "_label", $l_lib);
        $input->css_size="100%";
    }
    else
    {
        $input = new ISpan("e_march" . $q[$e]['j_id'] . "_label");
		$input->value=$l_lib;
        $hidden = HtmlInput::hidden("j_id[]", $q[$e]['j_id']);
    }
     $row.=td($input->input().$hidden);
    $montant=td(nbm($q[$e]['j_montant']),'class="num"');
    $row.=($q[$e]['j_debit']=='t')?$montant:td('');
    $row.=($q[$e]['j_debit']=='f')?$montant:td('');
    /* Analytic accountancy */
    if ( $owner->MY_ANALYTIC != "nu" /*&& $div=='popup'*/){
      if ( preg_match('/^(6|7)/',$q[$e]['j_poste'])) {

	echo HtmlInput::hidden("amount_t".$amount_idx,$q[$e]['j_montant']);
	$anc_op=new Anc_Operation($cn);
	$anc_op->j_id=$q[$e]['j_id'];
        $anc_op->in_div=$div;
        $str_anc.='<tr>';
	$str_anc.=HtmlInput::hidden('op[]',$anc_op->j_id);
        $str_anc.=td($q[$e]['j_qcode']);
        $str_anc.=td($q[$e]['j_poste']);
        $str_anc.=td($q[$e]['j_montant']);
	$str_anc.=$anc_op->display_table(1,$q[$e]['j_montant'],$div);
        $str_anc.='</tr>';
	$amount_idx++;
      }  else {
	$row.=td('');
      }
    }
    $class=($e%2==0)?' class="even"':'class="odd"';

    echo tr($row,$class);

  }
?>
</table>
</div>
<?php 
require_once NOALYSS_INCLUDE.'/template/ledger_detail_bottom.php';
?>
</div>
