<?php
require_once ('class_icard.php');
require_once('class_ifile.php');
require_once('class_database.php');
require_once('class_iselect.php');
require_once('class_dossier.php');
require_once('class_fiche.php');

/* 
Ma première extension, intégrer un fichier de client dans une catégorie 
de fiche, ce fichier est en CSV. Le code est simple et compréhensible, normalement on 
devrait avoir une meilleure gestion des erreurs, vérifier les attaques SQL Inject,...
Ce code n'est là QUE pour expliquer le concept
*/


// se connecter au dossier courant
$cn=new Database(dossier::id());

// dans extension.php on vérifie la sécurité, en ajoutez une ici n'est en général 
// pas nécessaire


// Ce form permet de choisir dans quel cat de fiche je veux intégrer les 
// enregistrements.

echo '<form METHOD="get" action="extension.php">';
echo dossier::hidden();
// Ceci vous permet de revenir ici (voir extension.php)
echo HtmlInput::extension();

echo "Choix de la catégorie de fiche";
$select_cat=new ISelect('fd_id');
$select_cat->value=$cn->make_array('select fd_id,fd_label from fiche_def where frd_id='.
	FICHE_TYPE_CLIENT);
echo $select_cat->input();
echo HtmlInput::submit('display_prop','Afficher les propriétés');

echo '</FORM>';

// on choisit d'afficher les propriétés avant de confirmer l'import
// get parce qu'on interroge
if ( isset($_GET['display_prop'])){
	$a=new Fiche($cn);
	$prop=$a->to_array($_GET['fd_id']);
	foreach ($prop as $key=>$value) 	echo "Index : $key valeur $value <br/>";
	
	echo '<form method="POST" action="extension.php"  enctype="multipart/form-data">';
	echo dossier::hidden();
	echo HtmlInput::extension();
	echo HtmlInput::hidden('fd_id',$_GET['fd_id']);
	$file=new IFile('fichier_csv');
	echo $file->input();
	echo HtmlInput::submit('start_import','Démarrez importation');
	echo '</form>';
	exit;
}
// Il est demandé de démarrer l'importation 
// Post parce qu'on sauve
// On image que le fichier CSV n'a que 4 champs
// "nom client","prenom client", "numero client","adresse client"
//
if ( isset($_POST['start_import'])){
	$fd_id=$_POST['fd_id'];
	$tmp_file=$_FILES['fichier_csv']['tmp_name'];
	if ( ! is_uploaded_file($tmp_file)) 
	  die ('Je ne peux charger ce fichier');
	// on ouvre le fichier 
	$f=fopen($tmp_file,'r');
	// On récupère les propriétés de cette catégorie de fiche
	$client=new Fiche($cn);
	// $array contient toutes les valeurs nécessaires à Fiche::insert,
	$array=$client->to_array($_POST['fd_id']);
		
	while ( ($data=fgetcsv($f))==true) {
		// remarque : on a éliminé les traitements d'erreur
		
		// On  remet tous les attributs (propriétés) à vide
		foreach(array_keys($array) as $key) $array[$key]="";
		
		// Nom et prénom
		$array['av_text1']=$data[0].' '.$data[1];
		// Numéro de client
		$array['av_text30']=$data[2];
		// Adresse
		$array['av_text14']=$data[3];
		// Quickcode
		$array['av_text23']="CLI".$data[2];
		$client->insert($fd_id,$array);
	}
	exit;
}

?>

Voici le fichier plugin_client.txt (dans le répertoire dev)
"Nom client1","Prénom","C1","Rue de la boite,55"
"Nom client2","Prénom","C2","Rue du couvercle,55"
"Nom client3","Prénom","C3","Rue de la chaussure,55"
"Nom client4","Prénom","C4","Rue de la couleur,55"

Si vous vérifiez dans VW_CLIENT, vous verrez que toutes vos fiches ont été ajoutées. Dans l'exemple, il faudra rajouter un traitement d'erreur plus élaborée, le fait que si une fiche echoue , l'opération est annulée (Database::rollback) ou alors création d'un fichier avec les enregistrements "ratés"...


