<?php 
//@description:FIN Confirmation paiement
$_GET=array (
  'gDossier' => '42',
  'ac' => 'COMPTA/MENUFIN/FIN',
);
$_POST=array (
  'ac' => 'COMPTA/MENUFIN/FIN',
  'gDossier' => '42',
  'p_jrn' => '1',
  'nb_item' => '5',
  'last_sold' => '',
  'first_sold' => '0',
  'e_pj' => 'FIN1',
  'e_pj_suggest' => 'FIN1',
  'e_date' => '02.01.2014',
  'mt' => '1415995351.0398',
  'sa' => 'n',
  'e_other0' => 'IMMOB ',
  'e_other0_comment' => '',
  'e_other0_amount' => '2560',
  'e_concerned0' => '2',
  'dateop0' => '',
  'chdate' => '1',
  'e_other1' => '',
  'e_other1_comment' => '',
  'e_other1_amount' => '0',
  'e_concerned1' => '',
  'dateop1' => '',
  'e_other2' => '',
  'e_other2_comment' => '',
  'e_other2_amount' => '0',
  'e_concerned2' => '',
  'dateop2' => '',
  'e_other3' => '',
  'e_other3_comment' => '',
  'e_other3_amount' => '0',
  'e_concerned3' => '',
  'dateop3' => '',
  'e_other4' => '',
  'e_other4_comment' => '',
  'e_other4_amount' => '0',
  'e_concerned4' => '',
  'dateop4' => '',
  'confirm' => 'Confirmer',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'compta_fin.inc.php';
