<?php 
//@description:ODS utilisation code AD 
$_GET=array (
  'gDossier' => '42',
  'ac' => 'ODS',
  'go' => 'aller',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
