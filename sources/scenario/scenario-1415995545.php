<?php 
//@description:COMPANY 
$_GET=array (
  'gDossier' => '42',
  'ac' => 'PARAM/COMPANY',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'company.inc.php';
