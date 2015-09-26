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

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/*!\file
 * \brief Manage the attributs
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_fiche_attr.php';



$fa=new Fiche_Attr($cn);

/////////////////////////////////////////////////////////////////////////////
// If data are post we save them first
/////////////////////////////////////////////////////////////////////////////
if ( isset($_POST['save']))
{
    $ad_id=$_POST['ad_id'];
    $ad_text=$_POST['desc'];
    $ad_type=$_POST['type'];
    $ad_size=$_POST['size'];
    $ad_extra=$_POST['extra'];
    try
    {
        $cn->start();
        for ($e=0;$e<count($ad_id);$e++)
        {
            $fa->set_parameter('id',$ad_id[$e]);
            $fa->set_parameter('desc',$ad_text[$e]);
            $fa->set_parameter('type',$ad_type[$e]);
            $fa->set_parameter('size',$ad_size[$e]);
            $fa->set_parameter('extra',$ad_extra[$e]);
            if ( trim($ad_text[$e])!='' && trim($ad_type[$e])!='')
                $fa->save();
        }
        $cn->commit();
    }
    catch (Exception $e)
    {
      alert($e->getMessage());
        $cn->rollback();
    }

}
/* show list of existing */
$gDossier=dossier::id();
$array=$fa->seek();

$select_type=new ISelect('type[]');
$select_type->table=0;
$desc=new IText('desc[]');
$desc->size=50;
$size=new INum('size[]');
$size->size=5;
$extra=new IText('extra[]');

$select_type->value=array(
                        array('value'=>'text','label'=>_('Texte')),
                        array('value'=>'numeric','label'=>_('Nombre')),
                        array('value'=>'date','label'=>_('Date')),
                        array('value'=>'zone','label'=>_('Zone de texte')),
                        array('value'=>'poste','label'=>_('Poste Comptable')),
                        array('value'=>'card','label'=>_('Fiche')),
                        array('value'=>'select','label'=>_('Selection'))
                    );

echo '<div class="content">';
echo '<form method="post">';

echo HtmlInput::hidden('sa','fat');
echo HtmlInput::hidden('p_action','divers');
echo '<table id="tb_rmfa">';
echo '<tr>';
echo th(_("id"));
echo th(_("Description"));
echo th(_("Type"));
echo th(_("Taille"));
echo th(_("Paramètre"));
echo '</tr>';
for ($e=0;$e<count($array);$e++)
{
    $row=$array[$e];
    $r='';
    $r.=td(HtmlInput::hidden('ad_id[]',$row->get_parameter('id')).$row->get_parameter('id'));
    $select_type->selected=$row->get_parameter('type');
    $desc->value=$row->get_parameter('desc');
    $size->value=$row->get_parameter('size');
    $extra->value=$row->get_parameter('extra');
    $remove=new IButton('rmfa'.$e);
    $remove->label=_('Effacer');
    if ( $row->get_parameter('id')>= 9000)
    {
        $select_type->readOnly=false;
        $desc->readOnly=false;
        $size->readOnly=false;
        $extra->readOnly=false;

        $desc->style=' class="input_text" ';
        $r.=td($desc->input());
        $r.=td($select_type->input());
        $r.=td($size->input());
        $r.=td($extra->input());

        $remove->javascript=sprintf('confirm_box(\'tb_rmfa\',\'Vous  confirmez ?\',function() { removeCardAttribut(%d,%d,\'tb_rmfa\',$(\'rmfa%d\') );})',
                                    $row->get_parameter('id'),$gDossier,$e);
        $msg='<span class="notice">'._("Attention : effacera les données qui y sont liées").' </span>';
        $r.=td($remove->input().$msg);
    }
    else
    {
        $select_type->readOnly=true;
        $desc->readOnly=true;
        $size->readOnly=true;
        $extra->readOnly=true;

        $r.=td($desc->input().HtmlInput::hidden('type[]',''));
        $r.=td($select_type->input());
        $r.=td($size->input());
        $r.=td($extra->input());
        $r.=td("");
    }




    echo tr($r);

}
$desc->readOnly=false;
$select_type->readOnly=false;
$size->readOnly=false;
$extra->readOnly=false;
$desc->value='';
$select_type->selected=-1;
$r=td(HtmlInput::hidden('ad_id[]','0'));
$r.=td($desc->input());
$r.=td($select_type->input());
$r.=td($size->input());
$r.=td($extra->input());
echo tr($r);

echo '</table>';
echo HtmlInput::submit('save',_('Sauver'));
echo '</form>';
echo '</div>';
