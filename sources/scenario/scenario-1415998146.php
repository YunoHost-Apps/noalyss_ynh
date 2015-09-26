<?php 
//@description:ODHISTO
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUODS/ODHISTO',
  'go' => 'aller',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'history_operation.inc.php';
