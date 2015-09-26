<?php 
//@description:FIHISTO
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUFIN/FIHISTO',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'history_operation.inc.php';
