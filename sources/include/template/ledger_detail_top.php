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
             'op'=>'ledger',
            'ajax'=>$callback));
     $msg_close=_('Fermer');
     $msg_pop=_('Ouvrir dans une fenêtre séparée');
     
     $js="a=window.open('popup.php?{$str}','','titlebar=no,location=no,statusbar=no,menubar=no,toolbar=no,fullscreen=yes,scrollbars=yes,resizable=yes,status=no'); a.focus();removeDiv('{$div}')";
     echo Icon_Action::zoom($div, $js);
     echo Icon_Action::draggable($div);
     echo Icon_Action::close($div);
   }
?>
</div>
<div>
   <?php echo h2($oLedger->get_name(),'class="title"'); ?>
</div>
<?php echo _("Opération ID")."=".hb($obj->det->jr_internal); ?>
<div id="<?php echo $div.'info'?>" class="divinfo"></div>
<?php require_once NOALYSS_INCLUDE.'/lib/itextarea.class.php';
?>