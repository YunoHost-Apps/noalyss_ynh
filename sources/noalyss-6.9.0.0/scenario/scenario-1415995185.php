<?php 
//@description:VEN Encodage d'une vente
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/VENMENU/VEN',
);
$_POST=array (
  'ledger_type' => 'VEN',
  'ac' => 'COMPTA/VENMENU/VEN',
  'sa' => 'p',
  'gDossier' => '42',
  'nb_item' => '2',
  'p_jrn' => '2',
  'e_date' => '14.11.2014',
  'e_ech' => '',
  'e_client' => 'CLIENT1 ',
  'e_pj' => 'VEN1',
  'e_pj_suggest' => 'VEN1',
  'e_comm' => 'Première vente',
  'e_march0' => 'DEPLAC',
  'e_march0_price' => '120',
  'e_quant0' => '1',
  'htva_march0' => '120',
  'e_march0_tva_id' => '1',
  'e_march0_tva_amount' => '25.2',
  'tva_march0' => '25.2',
  'tvac_march0' => '145.2',
  'e_march1' => '',
  'e_march1_price' => '',
  'e_quant1' => '1',
  'htva_march1' => '0',
  'e_march1_tva_id' => '',
  'e_march1_tva_amount' => '',
  'tva_march1' => '0',
  'tvac_march1' => '0',
  'jrn_type' => 'VEN',
  'e_mp' => '0',
  'view_invoice' => 'Enregistrer',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_ven.inc.php';
