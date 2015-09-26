<?php 
//@description:ODS utilisation opération prédéfinie pour OD
$_GET=array (
  'action' => 'use_opd',
  'p_jrn_predef' => '4',
  'ac' => 'COMPTA/MENUODS/ODS',
  'gDossier' => '42',
  'pre_def' => '2',
);
$_POST=array (
  'ac' => 'COMPTA/MENUODS/ODS',
  'e_date' => '14.11.2014',
  'desc' => 'Paiement  TVA',
  'period' => '102',
  'e_pj' => 'ODS4',
  'e_pj_suggest' => 'ODS4',
  'mt' => '1415997404.8993',
  'e_comm' => 'Paiement  TVA',
  'jrn_type' => 'ODS',
  'p_jrn' => '4',
  'nb_item' => '10',
  'jrn_concerned' => '',
  'gDossier' => '42',
  'poste0' => '4519',
  'ld0' => 'Compte TVA',
  'amount0' => '250.0000',
  'poste1' => '6700',
  'ld1' => 'Impôts et précomptes dus ou versés',
  'amount1' => '250.0000',
  'ck1' => '',
  'opd_name' => '',
  'od_description' => '',
  'save' => 'Confirmer',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_ods.inc.php';
