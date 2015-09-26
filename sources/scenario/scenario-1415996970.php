<?php 
//@description:ODS Enregistrement
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUODS/ODS',
);
$_POST=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUODS/ODS',
  'p_jrn' => '4',
  'e_date' => '14.11.2014',
  'e_pj' => 'ODS1',
  'e_pj_suggest' => 'ODS1',
  'desc' => 'TVA',
  'nb_item' => '5',
  'jrn_type' => 'ODS',
  'qc_0' => '',
  'poste0' => '4519',
  'ld0' => 'Compte TVA',
  'amount0' => '250',
  'qc_1' => '',
  'poste1' => '6700',
  'ld1' => 'Paiement TVA',
  'amount1' => '250',
  'ck1' => '',
  'qc_2' => '',
  'poste2' => '',
  'ld2' => '',
  'amount2' => '',
  'qc_3' => '',
  'poste3' => '',
  'ld3' => '',
  'amount3' => '',
  'qc_4' => '',
  'poste4' => '',
  'ld4' => '',
  'amount4' => '',
  'jrn_concerned' => '',
  'summary' => 'Sauvez',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_ods.inc.php';
