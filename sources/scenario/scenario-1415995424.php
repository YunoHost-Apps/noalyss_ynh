<?php 
//@description:CFGPCMN Plan comptable
$_GET=array (
  'gDossier' => '42',
  'ac' => 'PARAM/CFGPCMN',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'param_pcmn.inc.php';
