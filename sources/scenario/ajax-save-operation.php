<?php 
//@description:save
$_GET=array (
);
$_POST=array (
  'whatdiv' => 'det2',
  'jr_id' => '2856',
  'gDossier' => '27',
  'p_date' => '05.12.2013',
  'p_ech' => '',
  'p_date_paid' => '',
  'npj' => 'ACH607',
  'lib' => 'Consommable, ptit matériel',
  'jrn_note' => '',
  'j_id' => 
  array (
    0 => '7629',
    1 => '7630',
  ),
  'BON_COMMANDE' => '',
  'OTHER' => '',
  'raptdet2' => '',
  'related' => '327,231,330',
  'Fermer' => 'Fermer',
  'save' => 'Sauver',
  'Effacer' => 'Effacer',
  'bextdet2' => 'Extourner',
  'rapt' => '',
  'div' => 'det2',
  'act' => 'save',
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'ajax_ledger.php';
