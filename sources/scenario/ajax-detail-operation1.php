<?php 
//@description:de Detail d'un achat
$_GET=array (
  'op' => 'ledger',
  'act' => 'de',
  'jr_id' => '2856',
  'div' => 'det2',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'ajax_misc.php';
