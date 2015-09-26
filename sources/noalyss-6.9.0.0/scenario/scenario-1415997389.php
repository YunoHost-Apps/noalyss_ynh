<?php 
//@description:ODS
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUODS/ODS',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_ods.inc.php';
