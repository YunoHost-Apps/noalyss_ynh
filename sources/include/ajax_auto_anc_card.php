<?php 
//This file is part of NOALYSS and is under GPL 
//see licence.txt

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_database.php';
require_once NOALYSS_INCLUDE.'/class_user.php';
$cn=new Database($_GET['gDossier']);


if ( isset($_REQUEST['pa_id']) )
{   
    $res=$cn->exec_sql("select po_name,po_description from  poste_analytique where pa_id=$1 ~* and (po_description ~* $2 or po_name ~* $3 order by po_id limit 12",
        array($_REQUEST['pa_id'],$_POST['anccard'],$_POST['anccard']));
}
else
{
       $res=$cn->exec_sql("select po_name,po_description from  poste_analytique where po_description ~* $1 or po_name ~* $2 order by po_id limit 12 ",
        array($_POST['anccard'],$_POST['anccard']));
}
$nb=Database::num_row($res);
	echo "<ul>";
for ($i = 0;$i< $nb;$i++)
{
	$row=Database::fetch_array($res,$i);
	echo "<li>";
	echo $row['po_name'];
	echo '<span class="informal"> '.$row['po_description'].'</span></li>';
}
	echo "</ul>";
?>