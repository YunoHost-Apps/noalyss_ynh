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
if ( !defined ('ALLOWED')) die('Forbidden');
/*!\file
* \brief Management of the folder
 *
 */
require_once NOALYSS_INCLUDE.'/class_itext.php';
require_once NOALYSS_INCLUDE.'/class_icheckbox.php';
require_once NOALYSS_INCLUDE.'/class_itextarea.php';
require_once NOALYSS_INCLUDE.'/class_html_input.php';

$sa=(isset($_REQUEST['sa']))?$_REQUEST['sa']:'list';
//---------------------------------------------------------------------------
// Update
$dossier_id=HtmlInput::default_value_request('d', -1);

if ( isset ($_POST['upd']) && isNumber($dossier_id) == 1 && $dossier_id != -1)
{
    $dos=new dossier($dossier_id);
    $name=HtmlInput::default_value_post('name', "--vide--");
    $desc=HtmlInput::default_value_post('desc', "--vide--");
    $dos->set_parameter('name',$name);
    $dos->set_parameter('desc',$desc);
    $dos->save();
}
echo '<div class="content" style="width:80%;margin-left:10%">';
/*
 *  check and add an new folder
 */
if ( isset ($_POST["DATABASE"]) )
{
    $repo=new Database();
    $dos=HtmlInput::default_value_post('DATABASE', "");
    $dos=sql_string($dos);
    if (strlen($dos)==0)
    {
        echo _("Le nom du dossier est vide");
        exit -1;
    }
    /* 
     * check template encoding
     */
    
    // Get the modeledef.mod_id
    $template=HtmlInput::default_value_post("FMOD_ID",-1);
    if ( $template == -1 || isNumber($template ) == 0) 
        die (_('Parametre invalide'));
    /*
     * If template is not empty
     */
    if ( $template != 0 )
    {
        // compute template name
        $template_name=domaine.'mod'.$template;


        $encoding=$repo->get_value("select encoding from pg_database  where ".
                                 " datname=$1",array($template_name));
        if ( $encoding != 6 )
        {
            alert(_('Désolé vous devez migrer ce modèle en unicode'));
            echo '<span class="error">';
            echo _('le modele ').domaine.'mod'.$_POST["FMOD_ID"]._(" doit être migré en unicode.");
            echo _('Pour le passer en unicode, faites-en un backup puis restaurez le fichier reçu').'</span>';
            echo HtmlInput::button_anchor('Retour','admin_repo.php?action=dossier_mgt');
            return;
        }
    }
    /*
     * Insert new dossier with description
     */
    $desc=HtmlInput::default_value_post("DESCRIPTION","");
    try
    {
        $repo->start();

        $Res=$repo->exec_sql("insert into ac_dossier(dos_name,dos_description)
                           values ($1,$2)",array($dos,$desc));
        $l_id=$repo->get_current_seq('dossier_id');
        $repo->commit();
    }
    catch (Exception $e)
    {
        $msg=_("Desole la creation de ce dossier a echoue,\n la cause la plus probable est".
               ' deux fois le même nom de dossier');
        alert($msg);
        $l_id=0;
        $repo->rollback();

    }
    // If the id is not null, name successfully inserted
    // Database created

    if ( $l_id != 0)
    {
        /*
         * We don't create  and empty database
         */
        if ($template != 0 )
        {
            //--
            // setting the year
            //--
            $year=sql_string($_POST['YEAR']);
            if ( strlen($year) != 4 || isNumber($year) == 0 || $year > 2100 || $year < 2000 || $year != round($year,0))
            {
                echo "$year"._(" est une année invalide");
                $Res=$repo->exec_sql("delete from ac_dossier where dos_id=$l_id");
            }
            else
            {
                $Sql=sprintf("CREATE DATABASE %sDOSSIER%d encoding='UTF8' TEMPLATE %sMOD%d",
                             domaine,
                             $l_id,
                             domaine,
                             sql_string($_POST["FMOD_ID"]));
                ob_start();
                if ( $repo->exec_sql($Sql)==false)
                {
                    echo   "[".$Sql."]";

                    //ob_end_clean();
                    $repo->exec_sql("delete from ac_dossier where dos_id=$l_id");
                    echo "<h2 class=\"error\">"._(" Base de donnée ").domaine."mod".$_POST['FMOD_ID']."  ".
                    _("est accèdée, déconnectez-vous d'abord")."</h2>";
                    exit;
                }
                ob_flush();
                // Connect to the new database
                $cn=new Database($l_id);
                //--year --
                $Res=$cn->exec_sql("delete from parm_periode");
                if ( ($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0 )
                    $fev=29;
                else
                    $fev=28;
                $Res=$cn->exec_sql("delete from user_local_pref where parameter_type='PERIODE'");
                $nb_day=array(31,$fev,31,30,31,30,31,31,30,31,30,31);
                $m=1;
                foreach ($nb_day as $day)
                {
                    $p_start=sprintf("01-%d-%s",$m,$year);
                    $p_end=sprintf("%d-%d-%s",$day,$m,$year);
                    $sql=sprintf("insert into parm_periode (p_start,p_end,p_exercice)
                                 values (to_date('%s','DD-MM-YYYY'),to_date('%s','DD-MM-YYYY'),'%s')",
                                 $p_start,$p_end,$year);
                    $Res=$cn->exec_sql($sql);
                    $m++;
                }
                $sql="	insert into jrn_periode(p_id,jrn_def_id,status) ".
                     "select p_id,jrn_def_id, 'OP'".
                     " from parm_periode cross join jrn_def";
                $Res=$cn->exec_sql($sql);

                Dossier::synchro_admin($l_id);


            }
        }
        else {
            /*
             * An empty database is asked, it is created 
             */
             $Sql=sprintf("CREATE DATABASE %sDOSSIER%d encoding='UTF8' ",
                             domaine,
                             $l_id,
                             domaine
                         );
                ob_start();
                if ( $repo->exec_sql($Sql)==false)
                {
                    echo   "[".$Sql."]";

                    //ob_end_clean();
                    $repo->exec_sql("delete from ac_dossier where dos_id=$l_id");
                    echo _("Echec création ");
                    exit;
                }
                ob_flush();
            
        }
    } // if $l_id != 0
} // $_POST[DATABASE]
?>
<h2> <?php echo _('Dossier Management')?></h2>

<?php
//---------------------------------------------------------------------------
// List of folder
if ( $sa == 'list' )
{
	require_once NOALYSS_INCLUDE.'/class_sort_table.php';
        echo '<p>';
        echo HtmlInput::button(_('Ajouter'),_('Ajouter un dossier')," onclick=\$('folder_add_id').show()");
        echo '</p>';
	$header=new Sort_Table();
	$url=$_SERVER['PHP_SELF']."?sa=list&action=".$_REQUEST['action'];
	$header->add(_("id"),$url," order by dos_id asc"," order by dos_id desc","da","dd");
	$header->add(_("Nom"),$url," order by dos_name asc"," order by dos_name desc","na","nd");
	$header->add(_("Description"),$url," order by dos_description asc"," order by dos_description  desc","da","dd");
        $repo=new Dossier(0);
	$repocn=new Database();
	$ord=(isset($_REQUEST['ord']))?$_REQUEST['ord']:'na';
	$sql_order=$header->get_sql_order($ord);
	$Res=$repocn->get_array("select *  from ac_dossier $sql_order");

	$compteur=1;
        $template="";
	echo '<div class="content">';
	echo '<span style="display:block">';
	echo _('Filtre').HtmlInput::infobulle(23);
	echo HtmlInput::filter_table("t_dossier", "0,1,2","1");
	echo '</span>';
    echo '<TABLE id="t_dossier" class="table_large" >';
	$r="";
	$r.='<th>'.$header->get_header(0).'</td>';
	$r.='<th>'.$header->get_header(1).'</td>';
	$r.='<th>'.$header->get_header(2).'</td>';
    $r.=th(_('Taille')).th(_('Nom base de données'));

    $r=tr($r);
    echo $r;
    // show all dossiers
    if ( $Res != null )
    {
        foreach ( $Res as $Dossier)
        {

            if ( $compteur%2 == 0 )
                $cl='class="odd"';
            else
                $cl='class="even"';

            echo "<TR id=\"folder{$Dossier['dos_id']}\" $cl><TD style=\"vertical-align:top\"> ".
	      $Dossier['dos_id']."</td><td> <B>".h($Dossier['dos_name'])."</B> </TD>";
	    $str_name=domaine.'dossier'.$Dossier['dos_id'];

	    echo "<TD><I>  ".h($Dossier['dos_description'])."</I></td>";

            $database_exist=$repocn->exist_database($str_name);

            if ($database_exist > 0 )
            {
                $size=$repocn->get_value("select pg_database_size($1)/(1024*1024)::float",
                                     array($str_name));
                echo td(nbm($size)."MB",' style="text-align:right"');
            } else {
                echo td(_("Dossier inexistant"),'style="color:red"');
            }
	    echo td($str_name);
            if ( $database_exist > 0)
            {
                echo td(HtmlInput::anchor(_('Effacer'),'?action=dossier_mgt&sa=del&d='.$Dossier['dos_id']," onclick=\"folder_drop('".$Dossier['dos_id']."')\""));

                echo td(HtmlInput::anchor(_('Modifier'),'?action=dossier_mgt&sa=mod&d='
                                                 .$Dossier['dos_id']," onclick=\"folder_modify('".$Dossier['dos_id']."')\""));

                echo td(HtmlInput::anchor(_('Backup'),'backup.php?action=backup&sa=b&t=d&d='
                                              .$Dossier['dos_id']));
            } else
            {
                echo td(HtmlInput::anchor(_('Effacer'),'?action=dossier_mgt&sa=del&d='.$Dossier['dos_id']," onclick=\"folder_drop('".$Dossier['dos_id']."')\""));
            }
            $compteur++;

        }
        echo "</TR>";

    }
    echo '</table>';


}
?>
<div id="folder_add_id" class="inner_box" style="display:none;top:50px">
    <?php
//---------------------------------------------------------------------------
// Add a new folder
    echo HtmlInput::title_box(_("Ajout d'un dossier"), 'folder_add_id', "hide");
    $repo=new Database();
    // Load the available Templates
    $Res=$repo->exec_sql("select mod_id,mod_name,mod_desc from
                       modeledef order by mod_name");
    $count=Database::num_row($Res);

    if ( $count == 0 )
    {
        echo _("pas de modèle disponible");
    }
    else
    {
        $template='<SELECT NAME=FMOD_ID>';
       
        for ($i=0;$i<$count;$i++)
        {
            $mod=Database::fetch_array($Res,$i);
            $template.='<OPTION VALUE="'.$mod['mod_id'].'"> '.h($mod['mod_name']." - ".mb_substr($mod['mod_desc'],0,30));
        }// for
         $template.='<option value="0" >'._('Aucun modèle (dossier vide, devant être restauré manuellement)');
        $template.="</SELECT>";
    }// if count = 0
    $m_date=date('Y');

    ?>

    </TABLE>

    <FORM ACTION="admin_repo.php?action=dossier_mgt" METHOD="POST">
                 <TABLE>
                 <TR>
                 <TD><?php echo _('Nom du dossier');
    ?></td><td>  <INPUT TYPE="TEXT" class="input_text" NAME="DATABASE"> </TD>
                                         </TR><TR>
                                         <TD><?php echo _('Description');
    ?></td><td>  <TEXTAREA  class="input_text"  COLS="60" ROWS="2" NAME="DESCRIPTION" ></TEXTAREA> </TD>
                                          </TR>
                                          <TR> <TD><?php echo _('Modèle');
    ?></td><td>  <?php   echo $template;
    ?> </TD></TR>
    <TR><TD><?php echo _('Année')?> </TD><TD><input  class="input_text"  type="text" size=4 name="YEAR" value=<?php  echo '"'.$m_date.'"'; ?>></TD></TR>
    <TR>
    <TD> <INPUT TYPE=SUBMIT class="button" VALUE="<?php echo _('Creation Dossier'); ?>"> </TD>
                                              <td>
    </td>
    </TR>
    </TABLE>
    </FORM>
    <?php

?>
</div>
<?php
//---------------------------------------------------------------------------
// action = del
//---------------------------------------------------------------------------
if ( $sa == 'remove' && isNumber($dossier_id) == 1 && $dossier_id != -1 )
{
    
    if ( ! isset ($_REQUEST['p_confirm']))
    {
        echo _('Désolé, vous n\'avez pas coché la case');
        echo HtmlInput::button_anchor(_('Retour'),'?action=dossier_mgt');
        return;
    }

    $cn=new Database();
    $msg="dossier";
    $name=$cn->get_value("select dos_name from ac_dossier where dos_id=$1",array($dossier_id));
    if ( strlen(trim($name)) == 0 )
    {
        echo "<h2 class=\"error\"> $msg "._('inexistant')."</h2>";
        return;
    }
    /**
     * Check if db exists
     */
    $str_name=domaine.'dossier'.$dossier_id;
    
    $database_exist=$cn->exist_database($str_name);
    
    // if db exists for postgres then drop it
    if ( $database_exist  == 1)
    {
        $sql="drop database ".domaine."dossier".sql_string($_REQUEST['d']);
        ob_start();
        if ( $cn->exec_sql($sql)==false)
        {
            ob_end_clean();

            echo "<h2 class=\"error\"> ";
            echo _('Base de donnée ').domaine."dossier".$_REQUEST['d'].
            _("est accèdée, déconnectez-vous d'abord")."</h2>";
            exit;
        }
        ob_flush();
    }
    
    // clean tables about this dossier
    $sql="delete from  jnt_use_dos where dos_id=$1";
    $cn->exec_sql($sql,array($dossier_id));
    $sql="delete from ac_dossier where dos_id=$1";
    $cn->exec_sql($sql,array($dossier_id));
    print '<h2 class="error">';
    printf (_("Le dossier %s est effacé").'</h2>',h($name));
    echo HtmlInput::button_anchor(_('Retour'),'?action=dossier_mgt');
}
?>
</div>
