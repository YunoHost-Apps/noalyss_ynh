<?php 
//@description:HIST Historique operation du 1.1.2014 au 31.12.2014
$_GET=array (
  'gDossier' => '37',
  'ledger_type' => 'ALL',
  'ac' => 'HIST',
  'nb_jrn' => '0',
  'date_start' => '01.01.2014',
  'date_end' => '31.12.2014',
  'date_paid_start' => '',
  'date_paid_end' => '',
  'desc' => '',
  'amount_min' => '0',
  'amount_max' => '0',
  'qcode' => '',
  'accounting' => '',
  'search' => 'Rechercher',
);
$_POST=array (
);
$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
 $_REQUEST=array_merge($_GET,$_POST);
include 'history_operation.inc.php';
