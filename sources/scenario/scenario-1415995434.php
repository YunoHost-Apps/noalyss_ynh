<?php 
//@description:CFGPCMN Ajout d'un poste comptable
$_GET=array (
  'gDossier' => '42',
  'ac' => 'PARAM/CFGPCMN',
);
$_POST=array (
  'p_action' => 'pcmn',
  'gDossier' => '42',
  'p_val' => '4519',
  'p_lib' => 'Compte TVA',
  'p_parent' => '451',
  'p_type' => 'ACT',
  'Ajout' => 'Ajout',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'param_pcmn.inc.php';
