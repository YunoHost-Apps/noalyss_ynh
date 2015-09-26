<?php 
//@description:VERIFBIL
$_GET=array (
  'gDossier' => '27',
  'ac' => 'COMPTA/ADV/VERIFBIL',
  'go' => 'aller',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'verif_bilan.inc.php';
