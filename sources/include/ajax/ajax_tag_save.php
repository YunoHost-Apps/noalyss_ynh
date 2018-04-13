<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
/**
 *@file
 *@brief save a new tag or disable / enable the tag
 *@see Tag
 */
if ( !defined ('ALLOWED') )  die('Appel direct ne sont pas permis');
$http=new HttpInput();
$op=$http->request("op");
global $g_user;
$nDossier=Dossier::id();
///check security
if ( $g_user->check_module('CFGTAG')==0)
{
    die(_("non permis"));
}


require_once NOALYSS_INCLUDE.'/class/tag.class.php';

//Save a tag
if ($op=='tag_save')
{
    $tag=new Tag($cn);
    $tag->save($_GET);
    return;
} 
//---------------------------------------------------------------------
// Enable or disable a tag
//---------------------------------------------------------------------
if ( $op == "tag_activate")
{
    $tag_id=$http->get("t_id");
    $tag=new Tag($cn, $tag_id);
    $return=array();
    $id=sprintf("tag_onoff%d",$tag_id);
    if ( $tag->data->getp('t_actif') == 'Y')
    {
        $tag->data->t_actif='N';
        $tag->data->save();
        $return['code']='&#xf204;';
        $return['style']='color:red';
    } else {
        $tag->data->t_actif='Y';
        $tag->data->save();
        $return['code']='&#xf205;';
        $return['style']='color:green';
    }
    header("Content-type: text/json; charset: utf8",true);
    echo json_encode($return);
}
?>
