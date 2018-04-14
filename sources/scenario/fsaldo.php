<?php 
//@description:FSALDO
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUFIN/FSALDO',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_fin_saldo.inc.php';
