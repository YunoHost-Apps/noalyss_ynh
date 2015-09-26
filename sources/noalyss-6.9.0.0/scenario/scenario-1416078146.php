<?php 
//@description:bc
$_GET=array (
  'gDossier' => '42',
  'ctl' => 'div_new_card',
  'fd_id' => '2',
  'op' => 'bc',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'ajax_card.php';
