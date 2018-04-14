<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><?php
$max=$this->cn->count($ret);
echo HtmlInput::filter_table("tag_tb", '0,1', '1');
$nDossier=Dossier::id();
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
        <td>
            <?php
            $id=sprintf("tag_onoff%d",$row['t_id']);
            // Activate button
            if ( $row['t_actif'] == "Y") {
               $js=sprintf("activate_tag('%s','%s')",$nDossier,$row['t_id']);
               echo Icon_Action::iconon($id, $js);
            } else {
               $js=sprintf("activate_tag('%s','%s')",$nDossier,$row['t_id']);
               echo Icon_Action::iconoff($id, $js);
                
            }
            ?>
        </td>
    </tr>
<?php
 endfor;
 ?>
</table>