<?php 
//@description:CFGLED Configuration journaux
$_GET=array (
  'gDossier' => '42',
  'ac' => 'PARAM/CFGLED',
  'sa' => 'detail',
  'p_jrn' => '1',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'cfgledger.inc.php';
