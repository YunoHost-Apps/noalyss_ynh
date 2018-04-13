<?php 
//@description:ACH Utilisation opération prédéfinie
$_GET=array (
  'p_jrn_predef' => '3',
  'ac' => 'COMPTA/MENUACH/ACH',
  'gDossier' => '42',
  'pre_def' => '1',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_ach.inc.php';
