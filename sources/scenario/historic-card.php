<?php 
//@description:history
$_GET=array (
  'gDossier' => '10104',
  'act' => 'de',
  'f_id' => '11',
  'div' => 'det2',
  'l' => '2',
  'op' => 'history',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'ajax_misc.php';
