<?php 
//@description:ODS Confirmation OD
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUODS/ODS',
);
$_POST=array (
  'ac' => 'COMPTA/MENUODS/ODS',
  'e_date' => '14.11.2014',
  'desc' => 'TVA',
  'period' => '102',
  'e_pj' => 'ODS1',
  'e_pj_suggest' => 'ODS1',
  'mt' => '1415996970.8822',
  'e_comm' => 'TVA',
  'jrn_type' => 'ODS',
  'p_jrn' => '4',
  'nb_item' => '5',
  'jrn_concerned' => '',
  'gDossier' => '42',
  'poste0' => '4519',
  'ld0' => 'Compte TVA',
  'amount0' => '250',
  'poste1' => '6700',
  'ld1' => 'Paiement TVA',
  'amount1' => '250',
  'ck1' => '',
  'opd_name' => 'Paiement  TVA',
  'od_description' => '',
  'save' => 'Confirmer',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_ods.inc.php';
