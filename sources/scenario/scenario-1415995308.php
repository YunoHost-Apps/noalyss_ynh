<?php 
//@description:CFGLED
$_GET=array (
  'gDossier' => '42',
  'ac' => 'PARAM/CFGLED',
  'sa' => 'detail',
  'p_jrn' => '1',
);
$_POST=array (
  'p_jrn' => '1',
  'sa' => 'detail',
  'gDossier' => '42',
  'p_jrn_deb_max_line' => '10',
  'p_ech_lib' => 'echeance',
  'p_jrn_type' => 'FIN',
  'p_jrn_name' => 'Financier',
  'bank' => 'BQ',
  'min_row' => '5',
  'jrn_def_pj_pref' => 'FIN',
  'jrn_def_pj_seq' => '0',
  'p_description' => 'Concerne tous les mouvements financiers (comptes en banque, caisses, visa...)',
  'FICHEDEB' => 
  array (
    0 => '3',
    1 => '2',
    2 => '4',
  ),
  'update' => 'Sauve',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'cfgledger.inc.php';
