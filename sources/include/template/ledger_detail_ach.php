<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
$str_anc="";
?><?php require_once NOALYSS_INCLUDE.'/template/ledger_detail_top.php'; ?>
<div class="content" style="padding:0;">
    <?php
    require_once NOALYSS_INCLUDE.'/class_own.php';
    $owner = new Own($cn);
    ?>

    <?php if ($access == 'W') : ?>
        <form  class="print" onsubmit="return op_save(this);">
        <?php endif; ?>

        <?php echo HtmlInput::hidden('whatdiv', $div) . HtmlInput::hidden('jr_id', $jr_id) . dossier::hidden(); ?>
        <table style="width:100%">
            <tr><td>
                    <table>
                            <td></td>
                        <?php
                        $date = new IDate('p_date');
                        $date->value = format_date($obj->det->jr_date);
                        echo td(_('Date')) . td($date->input());
                        ?>
                        <tr>
                            <td></td>
                            <?php
                            $date_ech = new IDate('p_ech');
                            $date_ech->value = format_date($obj->det->jr_ech);
                            echo td(_('Echeance')) . td($date_ech->input());
                            ?>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <?php echo _("Date paiement")?>
                            </td>
                            <td>
                                <?php
                                $date_paid = new IDate('p_date_paid');
                                $date_paid->value = format_date($obj->det->jr_date_paid);
                                echo $date_paid->input();
                                ?>
                            </td>
                        </tr>

                        <tr><td>
                                <?php
                                $bk = new Fiche($cn, $obj->det->array[0]['qp_supplier']);
                                echo td(_('Fournisseur'));

                                $view_card_detail = HtmlInput::card_detail($bk->get_quick_code(), h($bk->getName()), ' class="line" ');
                                echo td($view_card_detail);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                $itext = new IText('npj');
                                $itext->value = strip_tags($obj->det->jr_pj_number);
                                echo td(_('Pièce')) . td($itext->input());
                                ?>
                            </td>
                        <tr>
                            <td>
                                <?php
                                $itext = new IText('lib');
                                $itext->value = strip_tags($obj->det->jr_comment);
                                $itext->size = 40;
                                echo td(_('Libellé')) . td($itext->input(), ' colspan="2" ');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Payé</td>
                            <td>
                                <?php
                                $ipaid = new ICheckBox("ipaid", 'paid');
                                $ipaid->selected = ($obj->det->jr_rapt == 'paid');
                                echo $ipaid->input();
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
            <table class="result">
                <?php
                bcscale(2);
                $total_htva = 0;
                $total_tvac = 0;
                echo th(_('Quick Code'));
                echo th(_('Description'));
                if ($owner->MY_TVA_USE == 'Y')
                {
                    echo th(_('Taux TVA'), 'style="text-align:right"');
                } else
                {
                    echo th('');
                }
                echo th(_('Prix/Un.'), 'style="text-align:right"');
                echo th(_('Quantité'), 'style="text-align:right"');
                echo th(_('Non ded'), 'style="text-align:right"');

                if ($owner->MY_TVA_USE == 'Y')
                {
                    echo th(_('HTVA'), 'style="text-align:right"');
                    echo th(_('TVA ND'), 'style="text-align:right"');
                    echo th(_('TVA'), 'style="text-align:right"');
                    echo th(_('TVAC'), 'style="text-align:right"');
                } else
                    echo th(_('Total'), 'style="text-align:right"');

                if ($owner->MY_ANALYTIC != 'nu' )
                {
                    $anc = new Anc_Plan($cn);
                    $a_anc = $anc->get_list(' order by pa_id ');
                    $x = count($a_anc);
                    /* set the width of the col */
                    /* add hidden variables pa[] to hold the value of pa_id */
                    $str_anc.='<tr><th>Code</th><th>Montant</th><th colspan="' . $x . '">' . _('Compt. Analytique') . Anc_Plan::hidden($a_anc).'</th></tr>';

                }
                echo '</tr>';
                for ($e = 0; $e < count($obj->det->array); $e++)
                {
                    $row = '';
                    $q = $obj->det->array[$e];
                    $fiche = new Fiche($cn, $q['qp_fiche']);
                    $qcode=$fiche->strAttribut(ATTR_DEF_QUICKCODE);
                    $view_card_detail = HtmlInput::card_detail($qcode, "", ' class="line" ');
                    $row = td($view_card_detail);
                    $sym_tva = '';

                    if ($owner->MY_TVA_USE == 'Y' && $q['qp_vat_code'] != '')
                    {
                        /* retrieve TVA symbol */
                        $tva = new Acc_Tva($cn, $q['qp_vat_code']);
                        $tva->load();
                        $sym_tva = h($tva->get_parameter('label'));
                    }
                    if ($owner->MY_UPDLAB == 'Y')
                    {
                        $l_lib = ($q['j_text'] == '') ? $fiche->strAttribut(ATTR_DEF_NAME) : $q['j_text'];
                        $hidden = HtmlInput::hidden("j_id[]", $q['j_id']);
                        $input = new IText("e_march" . $q['j_id'] . "_label", $l_lib);
                        $input->css_size = "100%";
                    } else
                    {
                        $input = new ISpan("e_march" . $q['j_id'] . "_label");
                        $hidden = HtmlInput::hidden("j_id[]", $q['j_id']);
                        $input->value = $fiche->strAttribut(ATTR_DEF_NAME);
                    }
                    $row.=td($input->input() . $hidden);
                    $row.=td($sym_tva, 'style="text-align:center"');
                    $pu = 0;
                    if ($q['qp_quantite'] != 0)
                        $pu = bcdiv($q['qp_price'], $q['qp_quantite']);
                    $row.=td(nbm($pu), 'class="num"');
                    $row.=td(nbm($q['qp_quantite']), 'class="num"');

                    $no_ded = bcadd($q['qp_dep_priv'], $q['qp_nd_amount']);
                    $row.=td(nbm($no_ded), ' style="text-align:right"');
                    $htva = $q['qp_price'];


                    $row.=td(nbm($htva), 'class="num"');
                    $tvac = bcadd($htva, $q['qp_vat']);
                    $tvac = bcadd($tvac, $q['qp_nd_tva']);
                    $tvac = bcadd($tvac, $q['qp_nd_tva_recup']);
                    $tvac = bcsub ($tvac,$q['qp_vat_sided']);

                    if ($owner->MY_TVA_USE == 'Y')
                    {
                        $tva_amount_nd = bcadd($q['qp_nd_tva_recup'], $q['qp_nd_tva']);
                        $class = "";
                        if ($q['qp_vat_sided'] <> 0)
                        {
                            $class = ' style="text-decoration:line-through"';
                        }
                        $row.=td(nbm($tva_amount_nd), 'class="num" ' . $class);
                        $row.=td(nbm($q['qp_vat']), 'class="num" ' . $class);
                        $row.=td(nbm($tvac), 'class="num"');
                    }
                    $total_tvac=bcadd($total_tvac,$tvac);
                    $total_htva=bcadd($htva,$total_htva);
                    /* Analytic accountancy */
                    if ($owner->MY_ANALYTIC != "nu" /*&& $div == 'popup'*/ )
                    {
                        $poste = $fiche->strAttribut(ATTR_DEF_ACCOUNT);
                        if (preg_match('/^(6|7)/', $poste))
                        {
                            $anc_op = new Anc_Operation($cn);
                            $anc_op->j_id = $q['j_id'];
                            $anc_op->in_div=$div;
                            echo HtmlInput::hidden('op[]', $anc_op->j_id);
                            /* compute total price */
                            bcscale(2);
                            $str_anc.='<tr>';
                            $str_anc.=td($qcode);
                            $str_anc.=td(nbm($htva));
                            $str_anc.=$anc_op->display_table(1, $htva, $div);
                        } else
                        {
                            $row.=td('');
                        }
                    }
                     $class=($e%2==0)?' class="even"':'class="odd"';
                     echo tr($row,$class);
                }
                if ($owner->MY_TVA_USE == 'Y')
                    $row = td(_('Total'), ' style="font-style:italic;text-align:right;font-weight: bolder;width:auto" colspan="6"');
                else
                    $row = td(_('Total'), ' style="font-style:italic;text-align:right;font-weight: bolder;width:auto" colspan="6"');
                $row.=td(nbm($total_htva), 'class="num" style="font-style:italic;font-weight: bolder;"');
                if ($owner->MY_TVA_USE == 'Y')
                    $row.=td("") . td("").td(nbm($total_tvac), 'class="num" style="font-style:italic;font-weight: bolder;"');
                echo tr($row);
                ?>
            </table>


        </div>


<?php
require_once NOALYSS_INCLUDE.'/template/ledger_detail_bottom.php';
?>
</div>
