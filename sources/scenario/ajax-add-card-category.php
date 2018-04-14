<?php 
//@description:st montre choix des catégories de fiches 
$_GET=array (
  'gDossier' => '42',
  'ctl' => 'select_card_div',
  'op' => 'card',
  'op2'=>'st',
  'fil' => '-1',
  'ledger' => '2',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include NOALYSS_HOME.'/ajax_misc.php';
