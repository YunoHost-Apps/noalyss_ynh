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
 *
 * \brief Plan Analytique
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_anc_plan.php';
require_once NOALYSS_INCLUDE.'/class_anc_account.php';
$ret="";
$str_dossier=Dossier::get();
//---------------------------------------------------------------------------
// action
// Compute the redcontent div
//---------------------------------------------------------------------------
if ( isset($_REQUEST['sa']))
{
    $sa=$_REQUEST['sa'];

    // show the form for adding a pa
    if ( $sa == "add_pa")
    {
        $new=new Anc_Plan($cn);
        if ( $new->isAppend() == true)
        {
            $ret.= '<div style="position:absolute;top:25%" class="inner_box">';
            $ret.=HtmlInput::title_box(_('Nouveau plan'),'','none');
            $ret.= '<form method="post">';
            $ret.=dossier::hidden();
            $ret.= $new->form();
            $ret.= HtmlInput::hidden("sa","pa_write");
            $ret.=HtmlInput::submit("submit",_("Enregistre"));
            $ret.= '</form>';
            $ret.= '</div>';
        }
        else
        {
            $ret.= '<div class="content">'.
                   '<h2 class="notice">'.
                   _("Maximum de plan analytique est atteint").
                   "</h2></div>";
        }
        $sa="anc_menu";
    }
    // Add
    if ( $sa == "pa_write")
    {
        $new=new Anc_Plan($cn);


        if ( $new->isAppend() == false)
        {
            $ret.= '<h2 class="notice">'.
                   _("Maximum de plan analytique est atteint").
                   "</h2>";
        }
        else
        {
            $new=new Anc_Plan($cn);
            $new->name=$_POST['pa_name'];
            $new->description=$_POST['pa_description'];
            $new->add();
        }
        $sa="anc_menu";
    }
    
    // Update the PA
    if ( $sa == "pa_update" )
    {
        $new=new Anc_Plan($cn,$_GET['pa_id']);
        $new->name=$_POST['pa_name'];
        $new->description=$_POST['pa_description'];
        $new->update();
        $ret='<div class="content">';
        $ret.='<h2 class="info">'._('Mis à jour').'</h2>';
        $ret.="</div>";
        $sa="anc_menu";
    }
    // show the form for add a poste
    if ( $sa=='po_add')
    {
        $po=new Anc_Account($cn);
        $po->pa_id=$_REQUEST['pa_id'];
        $wSa=HtmlInput::hidden("sa","po_write");
        $ret.='<div class="content">';
        $ret.=h2(_("Ajout d'un poste analytique"));
        $ret.='<form method="post">';
        $ret.=dossier::hidden();
        $ret.=$po->form();
        $ret.=$wSa;
        $ret.=HtmlInput::submit("add",_("Ajout"));
        $ret.="</form>";
        $ret.="</div>";
        
    }
    // record the poste
    if ( $sa=="po_write")
    {
        //		var_dump($_POST);
        $po=new Anc_Account($cn);
        $po->get_from_array($_POST);
        $po->add();
        $sa="pa_detail";
        

    }
    /* delete pa */
    if ( $sa == "pa_delete")
    {
        $delete=new Anc_Plan($cn,$_GET['pa_id']);
        $delete->delete();
        $sa="anc_menu";
    }
    /* po detail
     *---> in ajax : montre detail d'un poste analytique
     * 
     */
    if ( $sa=="po_detail")
    {
        $ret.=h2(_('Modification'));
        $po=new Anc_Account($cn,$_GET['po_id']);
        $po->get_by_id();
        $ret.='<div class="content">';
        $ret.='<form method="post">';
        $ret.=dossier::hidden();

        $ret.=$po->form();
        $ret.=HtmlInput::hidden('sa','po_update');
        $ret.=HtmlInput::submit('Correction','Correction');
        $ret.=sprintf('<input type="button" class="smallbutton" value="Efface" onClick="return confirm_box(\'anchor_del\',\' Voulez-vous vraiment effacer cette activité\','
                . 'function () { window.location=\'do.php?ac='.$_REQUEST['ac'].'&sa=po_delete&po_id=%s&pa_id=%s&'.$str_dossier.'\'}) ;"',
                      $po->id,
                      $_REQUEST['pa_id']
                     );

        $ret.='</form>';
        $ret.='</div>';
        $sa="";
    }
    /**
     * mise à jour po 
     */
    if ( $sa=="po_update")
    {
        $po=new Anc_Account($cn);
        $po->get_from_array($_POST);
        $po->update();
        $sa="pa_detail";
    }
    /**
     * Efface po
     */
    if ( $sa=="po_delete")
    {
        $po=new Anc_Account($cn,$_REQUEST['po_id']);
        $po->delete();
        $sa="pa_detail";
    }
    // show the detail
    if ( $sa == "pa_detail" )
    {
        $new=new Anc_Plan($cn,$_GET['pa_id']);
        $wSa=HtmlInput::hidden("sa","pa_update");

        $new->get();

        $ret.= '<div class="content">';
        $ret.= '<h2 class="info">'._("Mise à jour").'</h2>';
        $ret.= '<form method="post">';
        $ret.=dossier::hidden();

        $ret.= $new->form();
        $ret.= $wSa;
        $ret.=HtmlInput::submit("submit",_("Enregistre"));
        $ret.=HtmlInput::button_anchor(_('Efface'),
                '',
                'remove_analytic_plan',
                'onclick="return confirm_box(\'remove_analytic_plan\',\'Effacer ?\',function () {window.location=\'do.php?ac='.$_REQUEST['ac'].'&pa_id='.$_GET['pa_id'].'&sa=pa_delete&'.$str_dossier.'\';})"',
                'smallbutton');
        $ret.= '</form>';
        /**
         * Detail now
         */
            $count=0;

        $new=new Anc_Plan($cn,$_REQUEST['pa_id']);
        $new->get();
        $array=$new->get_poste_analytique(" order by po_name");
        $ret.='<div class="content">';
        $ret.='<table class="table_large">';
        $ret.="<tr>";
        $ret.="<th>"._("Nom")." </td>";
        $ret.="<th>"._("Montant")." </td>";
        $ret.="<th>"._("Description")." </td>";
        $ret.="<th>"._("Groupe")."</th>";
        $ret.="<th>"._("Plan A")." </td>";
        $ret.="</tr>";
        $class="";
        foreach ($array as $obj)
        {
            $count++;
            if ( $count %2 == 0 )
                $class="even";
            else
                $class="odd";

            $ret.="<TR class=\"$class\">";
            $ret.="<TD class=\"vert_mtitle\">".
                  '<a style="text-decoration:underline;" href="?ac='.$_REQUEST['ac'].'&sa=po_detail&po_id='.$obj->id.'&pa_id='.$_REQUEST['pa_id'].'&'.
                  $str_dossier.'">'.
                  h($obj->name).
                  '</a>';
            "</td>"
            ;
            $ret.="<TD align=\"right\">".$obj->amount."</td>";
            $ret.="<TD>".h($obj->description)."</td>";
            $ret.="<td>".$obj->ga_id."</td>";
            $ret.="<TD>".h($new->name)."</td>";
            $ret.="</tr>";

        }
        $ret.="</table>";
        // ---> montre form pour ajouter po
        $ret.=HtmlInput::button_anchor(_('Ajout'),"?ac=".$_REQUEST['ac']."&sa=po_add&pa_id=".$_GET['pa_id']."&".$str_dossier,'','','smallbutton');
        $href=http_build_query(array('ac'=>$_REQUEST['ac'],'gDossier'=>$_REQUEST['gDossier']));
        $ret.= '<a style="display:inline" class="smallbutton" href="do.php?'.$href.'">'._('Retour').'</a>';
        $ret.= '</div>';
        
    }
}
else {
    $sa="anc_menu";
}

//---------------------------------------------------------------------------
// Show lmenu
//
//---------------------------------------------------------------------------
if ($sa=='anc_menu')
{

    $obj=new Anc_Plan($cn);
    $list=$obj->get_list();




    if (empty($list))
    {
        echo '<div class="content">';
        echo '<TABLE>';
        echo '<TR><TD class="vert_mtitle">';
        echo '<a href="?ac='.$_REQUEST['ac'].'&sa=add_pa&'.$str_dossier.'">'._("Ajout d'un plan comptable").'</a>';
        echo '</TD></TR>';
        echo '</TABLE>';

        echo '</div>';
        if (!isset($_REQUEST['sa']))
            echo '<div class="notice">'.
            _("Aucun plan analytique n'est défini").
            '</div>';
    }
    else
    {
        echo '<div class="content">';

        echo '<table class="vert_mtitle">';
        if ($obj->isAppend()==true)
        {
            echo '<TR><TD class="first">';
            echo '<a href="?ac='.$_REQUEST['ac'].'&sa=add_pa&'.$str_dossier.'">'._("Ajout d'un plan comptable").'</a>';
            echo '</TD></TR>';
        }
        foreach ($list as $line)
        {
            echo '<TR>';
            echo '<TD>'.
            '<a href="?ac='.$_REQUEST['ac'].'&sa=pa_detail&pa_id='.$line['id'].'&'.$str_dossier.'">'.
            h($line['name']);

            echo $line['description']."</a>";
            echo "</td>";
            echo "</TR>\n";
        }
        echo '</TABLE>';


        echo '</div>';
    }
}
//---------------------------------------------------------------------------
// show the content part
//
//
//---------------------------------------------------------------------------

echo $ret;
