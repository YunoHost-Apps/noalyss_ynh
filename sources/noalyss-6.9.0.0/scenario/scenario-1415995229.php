<?php 
//@description:ACH Encodage d'une vente
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUACH/ACH',
);
$_POST=array (
  'gDossier' => '42',
  'nb_item' => '1',
  'p_jrn' => '3',
  'e_date' => '01.03.2014',
  'e_ech' => '',
  'e_client' => 'IMMOB',
  'e_pj' => 'ACH1',
  'e_pj_suggest' => 'ACH1',
  'e_comm' => 'Loyer',
  'e_march0' => 'LOYER',
  'e_march0_price' => '2560',
  'e_quant0' => '1',
  'htva_march0' => '2560',
  'e_march0_tva_id' => '4',
  'e_march0_tva_amount' => '0',
  'tva_march0' => '0',
  'tvac_march0' => '2560',
  'jrn_type' => 'ACH',
  'p_action' => 'ach',
  'sa' => 'p',
  'e_mp' => '0',
  'view_invoice' => 'Enregistrer',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_ach.inc.php';
