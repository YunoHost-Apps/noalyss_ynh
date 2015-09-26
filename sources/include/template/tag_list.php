<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><?php
$max=$this->cn->count($ret);
echo HtmlInput::filter_table("tag_tb", '0,1', '1');
?>
<table id="tag_tb">
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
$ac=$_REQUEST['ac'];
    for ($i=0;$i<$max;$i++):
        $row=Database::fetch_array($ret, $i);
?>
    <tr class="<?php echo (($i%2==0)?'even':'odd');?>">
        <td>
            <?php
            $js=sprintf("show_tag('%s','%s','%s','p')",$gDossier,$ac,$row['t_id']);
            echo HtmlInput::anchor($row['t_tag'], "", "onclick=\"$js\"");
            ?>
        </td>
        <td>
            <?php
            echo $row['t_description'];
            ?>
        </td>
    </tr>
<?php
 endfor;
 ?>
</table>