<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><TABLE class="result">
    <tr>
        <th><?php echo _("Pièce")?></th>
        <th><?php echo _("Date")?></th>
        <th><?php echo _("Paiement")?></th>
        <th><?php echo _("Ref")?></th>
        <th><?php echo _("Client")."/"._("Fournisseur")?></th>
        <th><?php echo _("Description")?></th>
        <th style="text-align:right">HTVA</th>
        <th style="text-align:right">Privé</th>
        <th style="text-align:right">DNA</th>
        
        
<?php
$col_tva="";

 if ( $own->MY_TVA_USE=='Y')
        {
            echo '<th style="text-align:right">TVA ND</th>';
            $a_Tva=$cn->get_array("select tva_id,tva_label from tva_rate where tva_rate != 0.0000 order by tva_rate");
            foreach($a_Tva as $line_tva)
            {
                $col_tva.='<th style="text-align:right">Tva '.$line_tva['tva_label'].'</th>';
            }
        }
echo $col_tva;      
?>
        <th style="text-align:right">TVAC</th>
        <th><?php echo _("Opérations rapprochées")?></th>
    </tr>
<?php
$i = 0;
$cn->prepare('reconcile_date','select * from jrn where jr_id in (select jra_concerned from jrn_rapt where jr_id = $1 union all select jr_id from jrn_rapt where jra_concerned=$1)');
$tot['htva']=0;
$tot['dep_priv']=0;
$tot['dna']=0;
$tot['tva_nd']=0;
$tot['tvac']=0;
$tot['tva']=array();
bcscale(2);
foreach ($Row as $line) {
    $i++;
    /*
     * Get date of reconcile operation
     */
    $ret_reconcile=$cn->execute('reconcile_date',array($line['jr_id']));
   
    $class = ($i % 2 == 0) ? ' class="even" ' : ' class="odd" ';
    echo "<tr $class>";
    echo "<TD>" . h($line['jr_pj_number']) . "</TD>";
    echo "<TD>" . smaller_date($line['date']) . "</TD>";
    echo "<TD>" . smaller_date($line['date_paid']) . "</TD>";
    echo "<TD>" . HtmlInput::detail_op($line['jr_id'], $line['jr_internal']) . "</TD>";
    $tiers = $Jrn->get_tiers($line['jrn_def_type'], $line['jr_id']);
    echo td($tiers);
    echo "<TD>" . h($line['comment']) . "</TD>";
    $dep_priv=($line['dep_priv']==0)?"":nbm($line['dep_priv']);
    $tot['dep_priv']=bcadd($tot['dep_priv'],  floatval($line['dep_priv']));
    $dna=($line['dna']==0)?"":nbm($line['dna']);
    $tot['dna']=bcadd($tot['dna'],floatval($line['dna']));
    echo "<TD class=\"num\">" . nbm($line['HTVA']) . "</TD>";
    $tot['htva']=bcadd($tot['htva'],  floatval($line['HTVA']));
    
    echo "<TD class=\"num\">" .$dep_priv . "</TD>";
    echo "<TD class=\"num\">" . $dna . "</TD>";
    if ($own->MY_TVA_USE == 'Y' )
    {
        $tva_dna=($line['tva_dna']==0)?"":nbm($line['tva_dna']);
        $tot['tva_nd']=bcadd($tot['tva_nd'],  floatval($line['tva_dna']));
        echo "<TD class=\"num\">" . $tva_dna. "</TD>";
        $a_tva_amount=array();
        foreach ($line['TVA'] as $lineTVA)
            {
                foreach ($a_Tva as $idx=>$line_tva)
                {

                    if ($line_tva['tva_id'] == $lineTVA[1][0])
                    {
                        $a=$line_tva['tva_id'];
                        $a_tva_amount[$a]=$lineTVA[1][2];
                    }
                }
            }
        foreach ($a_Tva as $line_tva)
        {
            $a=$line_tva['tva_id'];
            if ( isset($a_tva_amount[$a])) {
                echo '<td class="num">'.nb($a_tva_amount[$a]).'</td>';
                $tot['tva'][$a]=(isset($tot['tva'][$a]))?bcadd($tot['tva'][$a],floatval($a_tva_amount[$a])):floatval($a_tva_amount[$a]);
            }
            else
                printf("<td class=\"num\"></td>");
        }
    }
    echo '<td class="num">'.$line['TVAC'].'</td>';
    $tot['tvac']=bcadd($tot['tvac'], floatval($line['TVAC']));
    /*
     * If reconcile print them
     */
    echo '<td>';
    $max=Database::num_row($ret_reconcile);
    if ($max > 0) {
        $sep="";
        for ($e=0;$e<$max;$e++) {
            $row=Database::fetch_array($ret_reconcile, $e);
            echo $sep.HtmlInput::detail_op($row['jr_id'],$row['jr_date'].' '. $row['jr_internal']);
            $sep=' ,';
        }
    }
    echo '</td>';
    echo "</tr>";
}
/** 
 * summary
 */
?>
    <tr class="highlight">
        <td>
            <?php echo _('Totaux')?>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="num"><?php echo nbm($tot['htva']); ?></td>
        <td class="num"><?php echo nbm($tot['dep_priv']) ?></td>
        <td class="num"><?php echo nbm($tot['dna'])?></td>
        <?php if ($own->MY_TVA_USE == 'Y' ): ?>
            <td><?php echo nbm($tot['tva_nd']) ?></td>
            <?php  foreach ($a_Tva as $line_tva) :
                        $a=$line_tva['tva_id'];
                        if ( isset($tot['tva'][$a])) :
                    ?>
                        <td class="num"><?php echo nbm($tot['tva'][$a])?></td>
                    <?php else : ?>
                        <td>

                        </td>
                    <?php endif; ?>
                <?php endforeach;?>
        <?php endif; ?>
        <td class="num"><?php echo nbm($tot['tvac'])?></td>
        <td></td>
    </tr>
        
        
</table>