<?php 
//@description:CFGPCMN
$_GET=array (
  'ac' => 'PARAM/CFGPCMN',
  'p_start' => '4',
  'gDossier' => '42',
);
$_POST=array (
  'p_valu' => '4519',
  'p_libu' => 'Compte TVA',
  'p_parentu' => '451',
  'p_typeu' => 'PAS',
  'p_oldu' => '4519',
  'gDossier' => '42',
  'update' => 'Sauve',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'param_pcmn.inc.php';
