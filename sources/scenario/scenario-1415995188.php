<?php 
//@description:VEN Enregistrement de la vente
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/VENMENU/VEN',
);
$_POST=array (
  'gDossier' => '42',
  'bon_comm' => '',
  'other_info' => '',
  'e_client' => 'CLIENT1 ',
  'nb_item' => '2',
  'p_jrn' => '2',
  'mt' => '1415995185.7619',
  'e_comm' => 'Première vente',
  'e_date' => '14.11.2014',
  'e_ech' => '',
  'e_pj' => 'VEN1',
  'e_pj_suggest' => 'VEN1',
  'e_mp' => '0',
  'jrn_type' => 'VEN',
  'e_march0' => 'DEPLAC',
  'e_march0_price' => '120',
  'e_march0_tva_id' => '1',
  'e_march0_tva_amount' => '25.2',
  'e_quant0' => '1',
  'e_march1' => '',
  'e_march1_price' => '',
  'e_march1_tva_id' => '',
  'e_march1_tva_amount' => '',
  'e_quant1' => '1',
  'ac' => 'COMPTA/VENMENU/VEN',
  'opd_name' => '',
  'od_description' => '',
  'record' => 'Enregistrement',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_ven.inc.php';
