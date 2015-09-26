<?php
/*
 *   This file is part of PhpCompta.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if (!defined('RECOVER'))
    die('Appel direct ne sont pas permis');
define('SIZE_REQUEST', 70);


require_once NOALYSS_INCLUDE.'/class_html_input.php';
/**
 * @brief generate a random string of char
 * @param $car int length of the string
 */
function generate_random($car)
{
    $string="";
    $chaine="abcdefghijklmnpqrstuvwxyABCDEFGHIJKLMNPQRSTUVWXY0123456789";
    srand((double) microtime()*1020030);
    for ($i=0; $i<$car; $i++)
    {
        $string .= $chaine[rand()%strlen($chaine)];
    }
    return $string;
}

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
$action=HtmlInput::default_value_request("id", "");
if ($action=="") :
    /*
     * Display dialog box
     */
    ?>
    Donnez votre login ou votre email
    <form method="POST">
        <input type="hidden" value="send_email" name="id">
        <input type="hidden" value="recover" name="recover">
        login <input type="text"   name="login">
        or 
        email <input type="text" name="email">
        <input type="submit" name="send_email" value="Envoi email">
    </form>
    <?php
elseif ($action=="send_email") :
    require_once NOALYSS_INCLUDE.'/class_sendmail.php';
    require_once NOALYSS_INCLUDE.'/class_database.php';
    /*
     * Check if user exists, if yes save a recover request
     */
    $login_input=HtmlInput::default_value_request("login", "");
    $email_input=HtmlInput::default_value_request("email", "");
    $cn=new Database(0);
    $valid=false;
    if (trim($login_input)!=""):
        $array=$cn->get_array("select use_id,use_email,use_login from ac_users where lower(use_login)=lower($1) "
               , array($login_input));
    elseif (trim($email_input)!=""):
        $array=$cn->get_array("select use_id,use_email,use_login from ac_users where  "
                ."  lower(use_email)=lower($1) ", array( $email_input));

    else:
       return;
    endif;


    if ($cn->size()!=0):
        list($user_id, $user_email, $user_login)=array_values($array[0]);
        if (trim($user_email)!=" ") :
            $valid=true;
        endif;
    endif;


    if ($valid==true):
        $request_id=generate_random(SIZE_REQUEST);
        $user_password=generate_random(10);
        /*
         * save the request into 
         */
        $cn->exec_sql("insert into recover_pass(use_id,request,password,created_on,created_host) "
                ." values ($1,$2,$3,now(),$4)", array($user_id, $request_id, $user_password, $_SERVER['REMOTE_ADDR']));

        /*
         * send an email
         */
        $mail=new Sendmail();
        $mail->set_from(ADMIN_WEB);
        $mail->mailto($user_email);
        $mail->set_subject("NOALYSS : Réinitialisation de mot de passe");
        $message=<<<EOF
     Bonjour,
      
Une demande de réinitialisation de votre mot de passe a été demandée par {$_SERVER['REMOTE_ADDR']}
   
Votre nom d'utilisateur est {$user_login}
Votre mot de passe est {$user_password}

Suivez ce lien pour activer le changement ou ignorer ce message si vous n'êtes pas l'auteur de cette demande.
Ce lien ne sera actif que 12 heures.
   
   
   https://{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}?recover&id=req&req={$request_id}
   
   Merci d'utiliser NOALYSS
   
Cordialement,

Noalyss team
      
EOF;
        $mail->set_message($message);
        $mail->compose();
        $mail->send();
        echo '<p style="position:absolute;z-index:2;top:25px;left: 50px; background-color:whitesmoke;">
L\'email a été envoyé avec un lien et le nouveau mot de passe, vérifiez vos spams</p>';
    endif;
elseif ($action=="req") :
    $request_id=HtmlInput::default_value_request("req", "");
    if (strlen(trim($request_id))==SIZE_REQUEST) :
        require_once NOALYSS_INCLUDE.'/class_database.php';
        $cn=new Database(0);

        $value=$cn->get_value("select password from recover_pass where request=$1 and created_on > now() - interval '12 hours' and recover_on is null", array($request_id));
        if ($cn->get_affected()>0) :
            $cn->exec_sql("update ac_users set use_pass=md5(rp.password) from recover_pass as rp where rp.use_id=ac_users.use_id and request=$1", array($request_id));
            $cn->exec_sql("update recover_pass set recover_by=$1 , recover_on=now() where request=$2", array($_SERVER['REMOTE_ADDR'],$request_id));
            ?>
    <p style="position:absolute;z-index:2;top:25px;left: 50px; background-color:whitesmoke;">
            Opération réussie , vous pouvez vous connecter avec votre nouveau mot de passe
             
    </p>
            <?php
        endif;
    else:
        die("Requête inconnue");
    endif;
endif;
