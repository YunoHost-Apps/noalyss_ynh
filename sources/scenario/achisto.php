<?php 
//@description:ACHISTO
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUACH/ACHISTO',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'history_operation.inc.php';
