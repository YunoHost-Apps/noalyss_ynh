<?php 
//@description:ACH Appel menu Achat
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUACH/ACH',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_ach.inc.php';
