<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><?php
echo HtmlInput::title_box('Tag', $p_prefix.'tag_div');
$max=$this->cn->count($ret);
if ( $max == 0 ) {
    echo h2(_("Aucune étiquette disponible"),' class="notice"');
    return;
}
?>
<?php echo _("Cherche")." ".HtmlInput::filter_table($p_prefix.'tag_tb_id', '0,1', 1); ?>
<?php echo HtmlInput::button_action(_('Uniquement actif'), 'show_only_row(\''.$p_prefix.'tag_tb_id'.'\',\'tag_status\',\'Y\')');?>
<?php echo HtmlInput::button_action(_('Tous'), 'show_all_row(\''.$p_prefix.'tag_tb_id'.'\')');?>
<table class="result" id="<?php echo $p_prefix;?>tag_tb_id">
    <tr>
        <th>
            <?php echo _("Tag")?>
        </th>
        <th>
            <?php echo _("Description")?>
        </th>
    </tr>
<?php
$gDossier=Dossier::id();
    for ($i=0;$i<$max;$i++):
        $row=Database::fetch_array($ret, $i);
    $attr=sprintf('tag_status="%s"',$row['t_actif']);
?>
    <tr <?=$attr?> class="<?php echo (($i%2==0)?'even':'odd');?>">
        <td>
            <?php
            $js=sprintf("search_add_tag('%s','%s','%s')",$gDossier,$row['t_id'],$p_prefix);
            echo HtmlInput::anchor($row['t_tag'], "", "onclick=\"$js\"");
            ?>
        </td>
        <td>
            <?php
            echo $row['t_description'];
            ?>
        </td>
         <td>
            <?php
            if ( $row['t_actif'] == 'N') { 
                echo _('non actif');
            }
            ?>
        </td>
    </tr>
<?php
 endfor;
 ?>
</table>
<script>
    show_only_row('<?=$p_prefix?>tag_tb_id','tag_status','Y');
</script>    
<?=HtmlInput::button_close($p_prefix.'tag_div')?>