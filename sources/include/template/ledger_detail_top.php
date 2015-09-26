<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><div class="bxbutton">
<?php 
   if ($div != "popup") {
     $callback=$_SERVER['PHP_SELF'];
     // create the url for the ajax (for zoom feature)
     $str=http_build_query(array(
            'gDossier'=>Dossier::id(),
             'jr_id'=>$obj->det->jr_id,
             'act'=>'de',
             'div'=>$div,
            'ajax'=>$callback));
     $msg_close=_('Fermer');
     $msg_pop=_('Ouvrir dans une fenêtre séparée');
     
     echo '<A id="close_div" title="'.$msg_pop.'" onclick="var a=window.open(\'popup.php?'.$str.'\',\'\',\'titlebar=no,location=no,statusbar=no,menubar=no,toolbar=no,fullscreen=yes,scrollbars=yes,resizable=yes,status=no\'); a.focus();removeDiv(\''.$div.'\')">&#11036;
</A>';
     echo '<A id="close_div" title="'.$msg_close.'"  onclick="removeDiv(\''.$div.'\');">&#10761;</A>';
   }
?>
</div>
<div>
   <?php echo h2($oLedger->get_name(),'class="title"'); ?>
</div>
<?php echo _("Opération ID")."=".hb($obj->det->jr_internal); ?>
<div id="<?php echo $div.'info'?>" class="divinfo"></div>
<?php require_once NOALYSS_INCLUDE.'/class_itextarea.php';
?>