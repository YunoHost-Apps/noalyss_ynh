<?php 
//@description:de Detail VEN
$_GET=array (
  'gDossier' => '27',
  'act' => 'de',
  'jr_id' => '3532',
  'div' => 'det2',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'ajax_ledger.php';
