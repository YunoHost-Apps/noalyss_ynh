<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/*! \file
 * \brief default page where user access
 */
/*! \mainpage NOALYSS
 * Documentation
 * - \subpage Francais
 * - \subpage English
 *
 *\page Francais
 * \section intro_sec Introduction
 *
 * Cette partie contient de la documentation pour les développeurs.
 *
 * \section convention_code Convention de codage
 * <p>
 * Quelques conventions de codage pour avoir un code plus ou moins
 * homogène
 * <ol>
 * <li>Tant que possible réutiliser ce qui existe déjà, </li>
 * <li>Améliorer ce qui existe déjà et vérifier que cela fonctionne toujours</li>
 * <li>Documenter avec les tags doxygen votre nouveau code,</li>
 * <li>Dans le répertoire include: Les noms de fichiers sont *.inc.php pour les fichiers à éxécuter</li>
 * <li>Dans le répertoire include: Les noms de fichiers sont *.php pour les fichiers contenant des fonctions uniquement</li>
 * <li>Dans le répertoire include: Les noms de fichier sont
 * class_*.php pour les fichiers contenant des classes.</li>
 * <li>Dans le répertoire include: Les noms de fichier ajax* correspondent aux fichiers appelé par une fonction javascript en ajax, 
 * normalement le nom de fichier est basé sur le nom de la fonction javascript
 * exemple pour la fonction javascript anc_key_choice le fichier correspondant est
 * ajax_anc_key_choice.php
 * <li>Dans le répertoire include/template: les fichiers de
 * présentation HTML </li>
 * <li>Utiliser sql/upgrade.sql comme fichier temporaire pour modifier la base de données, en général
 *  ce fichier deviendra l'un des patch </li>
 * <li>Faire de la doc </li>
 * </ol>
 *
 * </p>
 * \section conseil Conseils
 * <p>
 * Utiliser cette documentation, elle est générée automatiquement avec Doxygen,
 * <ul>
 * <li>Related contient tous les \\todo</li>
 * <li>Global -> function pour lire toute la doc sur les fonctions</li>
 * <li>Regarder dans dossier1.html et account_repository.html  pour la doc des base de données
 *</ul>
 *  et il ne faut connaître que ces tags
 * <ul>
 * <li> \\file en début de fichier</li>
 * <li> \\todo ajouter un todo </li>
 * <li> \\enum pour commenter une variable</li>
 * <li> \\param pour commenter le paramètre d'une fonction</li>
 * <li> \\brief Commentaire du fichier, de la fonction ou de la classe</li>
 * <li> \\note des notes, des exemples</li>
 * <li> \\throw or exception is a function can throw an exception
 * <li> \\par to create a new paragraph
 * <li> \\return ce que la fonction retourne</li>
 * <li> \\code et \\endcode si on veut donner un morceau de code comme documentation</li>
 * <li> \\verbatim et \\endverbatim si on veut donner une description d'un tableau,  comme documentation</li>
 *<li>  \\see xxxx Ajoute un lien vers un fichier, une fonction ou une classe </li>
 * </ul>
 *----------------------------------------------------------------------
 *\page English
 * \section intro_sec Introduction
 *
 * This parts contains documentation for developpers
 *
 * \section convention_code Coding convention
 * <p>
 * Some coding conventions to have a homogeneous code
 * <ol>
 * <li>Reuse the existing code , </li>
 * <li>Improve and test that the function is still working</li>
 * <li>Make documentation thanks doxygen tag</li>
 * <li>In the folder include: filenames ending by  *.inc.php will be executer after being included</li>
 * <li>In the folder include: filenames end by  *.php if they contains only function</li>
 * <li>In the folder include: filenames starting with
 * class_*.php if it is related to a class.</li>
 * <li>In the folder include, files starting with ajax are executed by ajax call, usually, the file name is
 * based on the javascript function, example for the javascript function anc_key_choice the corresponding file is
 * ajax_anc_key_choice.php
 * 
 * <li>In the folder include/template: files for the HTML presentation
 * </li>
 * <li>Use sql/upgrade.sql as temporary file to modify the database,this file will be the base for a SQL patch
 *  </li>
 * <li>Write documentation </li>
 * </ol>
 *
 * </p>
 * \section advice Advices
 * <p>
 * Use this document, it is generated automatically by doxygen, check the documentation your made, read it first this
 * documentation before making changes
 * <ul>
 * <li>Related contains all the \\todo</li>
 * <li>Global -> all the functions</li>
 * <li>check into mod1.html and account_repository.html for the database design
 *</ul>
 *  You need to know only these tags
 * <ul>
 * <li> \\file in the beginning of a file</li>
 * <li> \\todo add a todo </li>
 * <li> \\enum comment a variable</li>
 * <li> \\param about the parameter of a function</li>
 * <li> \\brief Documentation of the file, function or class</li>
 * <li> \\note note exemple</li>
 * <li> \\throw or exception is a function can throw an exception
 * <li> \\par to create a new paragraph
 * <li> \\return what the function returns</li>
 * <li> \\code and \\endcode code snippet given as example</li>
 * <li> \\verbatim and \\endverbatim if we want to keep the formatting without transformation</li>
 *<li>  \\see xxxx create a link to the file, function or object xxxx </li>
 * </ul>
 */

if ( ! file_exists('..'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'config.inc.php'))
{
    header("Location: admin/setup.php",true, 307);
    exit(0);
}

echo '<!doctype html><HTML>
<head>
<TITLE> NOALYSS </TITLE>
<link rel="shortcut icon" type="image/ico" href="favicon.ico" />
<style>
BODY {
background-color:white;
font-size:12px;
font-family:sans-serif,arial;
color:blue;
}
.cell , #recover_box p{
font-size : 18px;
}
.remark {
border: solid black 1px;
font-family:sans-serif;
font-size: 9px;
color:blue;
width:200px;
padding:3px;
}
.gras {
font-size:12px;
font-family:sans-serif,arial;
color:red;

}
.input_text {
border:1px solid blue;
margin:1px;
padding: 10px;
border-radius: 4px;
font-size:18px;
}
.button {
color:white;
font-weight: bold;
border:0px;
text-decoration:none;
font-family: helvetica,arial,sans-serif;
background-image: url("image/bg-submit2.gif");
background-repeat: repeat-x repeat-y;
background-position: left;
text-decoration:none;
font-family: helvetica,arial,sans-serif;
border-width:0px;
padding:2px 4px 2px 4px;
cursor:pointer;
margin:31px 2px 1px 2px;
-moz-border-radius:2px 2px;
border-radius:2px 2px;
font-size : 20px;
margin-bottom: 10px;
}
.button:hover {
cursor:pointer;
background-color:white;
border-style:  solid;
border-width:  0px;
background-image: url("image/bg-submit3.gif");
background-repeat: repeat-x repeat-y;
}
</style>
<script src="js/scripts.js" type="text/javascript"></script>
</head>
<BODY>';
$my_domain="";
require_once '../include/constant.php';
require_once '../include/config.inc.php';
require_once NOALYSS_INCLUDE.'/ac_common.php';

if ( strlen(domaine) > 0 )
{
    $my_domain="Domaine : ".domaine;
}

if (defined("RECOVER") && isset ($_REQUEST['recover']) )
{
    require_once '../include/recover.php';
}
// reconnect , create a variable to reconnect properly in login.php
$goto="";
if (isset ($_REQUEST['reconnect']) && isset ($_REQUEST['backurl'])) {
    $goto='<input type="hidden" value="'.$_REQUEST['backurl'].'" name="backurl">';
}
echo '
<span style="background-color:#879ed4;color:white;padding-left:4px;padding-right:4px;">
version  6.9 - '.$my_domain.'
</span>
<BR>
<BR>
<BR>

<BR>
<center>
<IMG SRC="image/logo6820.png" style="width:420px;height:200px" alt="NOALYSS">
<BR>
<BR>
<BR>

<form action="login.php" method="post" name="loginform">'.
       $goto .
'<TABLE><TR><TD>
<TABLE  BORDER=0 CELLSPACING=0>
<TR>
<TD class="cell">Utilisateur</TD>
<TD><input type="text" class="input_text" value="" id="p_user" name="p_user" tabindex="1"></TD>
</TR>
<TR>
<TD class="cell"> Mot de passe </TD>
<TD><INPUT TYPE="PASSWORD"  class="input_text" value=""  NAME="p_pass" tabindex="2"></TD>
</TR>';



if ( $g_captcha == true )
  {
    echo '<tr ><td colspan="2" style="width:auto">';
    echo "<table style=\"border:1px solid black\">";
    echo '<tr>';
    echo '<td colspan="2" style="with:auto;font-size:12px;text-align:center">';
    echo "Indiquer le code que vous lisez dans l'image";
    echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo td('<img id="captcha" src="securimage/securimage_show.php" alt="CAPTCHA Image" border=1/>','colspan="2" style="width:auto;text-align:center"');
    echo '</tr>';
    echo '<tr>';

    echo td('<input type="text" class="input_text" name="captcha_code" size="10" maxlength="6" autocomplete="off"/>'.
	    '<a href="#" onclick="document.getElementById(\'captcha\').src = \'securimage/securimage_show.php?\' + Math.random(); return false">Reload Image</a>','colspan="2" style="width:auto;text-align:center"');
    echo '</tr>';
    echo '</table>';
    echo '</td>';
    echo '<tr>';
  }
echo '
<TR style="height:50px;vertical-align:bottom">
<TD style="width:auto;text-align:center" colspan="2">
<INPUT TYPE="SUBMIT"  style="width:250px;height:48px;-moz-border-radius:10px;border-radius:10px" class="button" NAME="login" value="Se connecter">
</TD>
</TR>
</table>
</TD></TR>';

?>
</table>

</form>
<?php if (defined("RECOVER")) : ?>
    <a id="recover_link" href="#">Mot de passe oublié ? </a>
    
<div id="recover_box" style="display:none;position:absolute;top:40%;z-index:1;border:solid blue 2px;width:30%;margin-left: 25%;background-color: whitesmoke">
    <span style="display:block;font-size:120%;padding:10px">Indiquez votre login ou votre email <span style="cursor: pointer;background-color: white;color:block;top:-5px;float: right;position:relative;right:-5px" id="close"><a ref="#" id="close_link"><?php echo SMALLX?></a></span></span>
            <form method="POST">
                <input type="hidden" value="send_email" name="id">
                <input type="hidden" value="recover" name="recover" >
                <p>
                Login <input type="text"     class="input_text" name="login" nohistory>
                </p>
                <p>OU</p> 
                <p>
                Email <input type="text"  class="input_text" name="email" nohistory>
                </p>
                <input type="submit" class="button" name="send_email" value="Envoi email">
                
            </form>
</div>
    <script>
        document.getElementById('recover_link').onclick=function() {
            document.getElementById('recover_box').style.display="block";
        }
        document.getElementById('close_link').onclick=function() {
            document.getElementById('recover_box').style.display="none";
        }
    </script>
<?php endif; ?>
        
<div style="position:absolute;bottom: 0px;width:80%;right:10%">
    <p>Nous conseillons d'utiliser Firefox ou chrome.</p>
    <p>We recommend to use Firefox or Chrome.</p>
<ul style="list-style:none;display:block">
    <li style="display:inline"> <a href="https://www.mozilla.org/fr/firefox/new/"> <img border="0" width="128px" src="image/header-firefox.png"></a></li>
<li style="display:inline"><a href="https://www.google.fr/chrome/browser/desktop/"> <img border="0" width="128px" src="image/chrome_logo_2x.png"></a></li>
</ul>
</div>
 <script> SetFocus('p_user'); </script>

</body>
</html>

