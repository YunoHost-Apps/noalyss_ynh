//This file is part of NOALYSS and is under GPL 
//see licence.txt
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

content[200]="Indiquez ici le récuterpertoire où les documents temporaires peuvent être sauvés exemple c:/temp, /tmp"
content[201]="Désactiver le changement de langue (requis pour MacOSX)";
content[202]="Le chemin vers le repertoire contenant psql, pg_dump...";
content[203]="Utilisateur de la base de donnée postgresql";
content[204]="Mot de passe de l'utilisateur ";
content[205]="Port de postgresql";
content[206]="En version mono dossier, le nom de la base de données doit être mentionné";
content[207]="Vous devez choisir si NOALYSS est installé sur l'un de vos servers ou sur un server mutualisé qui ne donne qu'une seule base de données";


function show_dbname(obj) {
	try {
		if (obj.checked === true)
		{
			this.document.getElementById('div_db').style.visibility= 'visible';
		}
		else {
                        this.document.getElementById('div_db').style.visibility= 'hidden';
		}
	} catch (e) {
		alert_box(e.getMessage);
	}
}
