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
 *  *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// Copyright (2014) Author Dany De Bontridder <dany@alchimerys.be>

if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
require_once NOALYSS_INCLUDE.'/class_extension.php';

/**
 * @file
 * @brief Automatic installation of plugins and activation
 */
global $cn;

/******************************************************************************
 * Scan the plugin folder and file in each subfolder a property file and
 * store them into an array a_plugin
 ******************************************************************************
 */
$dirscan=scandir(NOALYSS_PLUGIN);
$nb_dirscan=count($dirscan);
$a_plugin=array();
for ($e=0;$e<$nb_dirscan;$e++) {
    if ($dirscan[$e] != '.' && $dirscan[$e]!='..' && is_dir(NOALYSS_PLUGIN.'/'.$dirscan[$e])) {
        $dir_plugin=$dirscan[$e];
        if (file_exists(NOALYSS_PLUGIN.'/'.$dir_plugin.'/plugin.xml')) {

            $extension=Extension::read_definition(NOALYSS_PLUGIN.'/'.$dir_plugin.'/plugin.xml');
            for ($i=0;$i<count($extension);$i++)
            {
                $a_plugin[]=clone $extension[$i];
            }
            
        }
    }
}
$nb_plugin=count($a_plugin);

/**
 * available profiles
 */
$a_profile=$cn->get_array('select p_id,p_name from profile where p_id > 0 order by p_name');
$nb_profile=count($a_profile);
/******************************************************************************
 * save 
 ******************************************************************************/
if ( isset ($_POST['save_plugin'])){
    // retrieve array of plugin
    $plugin=HtmlInput::default_value_post('plugin', array());
    // for each extension
    for ($i=0;$i<$nb_plugin;$i++) {
        
        $code=$a_plugin[$i]->me_code;
        // for each profile
        for ($e=0;$e<$nb_profile;$e++)
        {
            $profile=$a_profile[$e]['p_id'];
            if ( isset ($plugin[$code][$profile])) {
                // insert or update into db
                $count = $cn->get_value("select count(*) from menu_ref where me_code=$1", array($code));
                if ( $count == 0 ) {
                    $a_plugin[$i]->insert();
                }
                try
                {
                    $a_plugin[$i]->insert_profile_menu($profile,'EXT');
                }
                catch (Exception $exc)
                {
                    $profile_name=$cn->get_value('select profile.p_name from profile where p_id=$1'
                            ,array($profile));
                    echo '<p class="notice">';
                    echo "code $code"," profile $profile_name ",$exc->getMessage();
                    echo '</p>';
                }

            } else {
                // delete
                $a_plugin[$i]->remove_from_profile_menu ($profile);
            }
    }
    }
}
/******************************************************************************
 * Display the Plugin and for each profile were it is installed or not
 ******************************************************************************/


?>
<div class="content">
    <?php echo _('Nombre de plugins trouvés')." ".$nb_plugin; ?>
    <form method="post">
    <table class="result">
        <tr>
            <th><?php echo _('Extension')?></th>
            <th><?php echo _('Menu')?></th>
            <th><?php echo _('Description')?></th>
            <th><?php echo _('Chemin')?></th>
            <th><?php echo _('Disponible')?></th>
        </tr>
        <?php for ($e=0;$e<$nb_plugin;$e++) : 
            //-----
            $a_profile=$cn->get_array("select distinct
                    p_id,p_name,
                    (select count(*)  from profile_menu as a where a.p_id=b.p_id and me_code=$1 )+
                    (select count(*)  from menu_ref as c join profile_menu as d on (d.me_code=c.me_code) where d.p_id=b.p_id and me_file=$2 )  as cnt 
                    from profile as b  
                    where p_id > 0 
                    order by p_name",array($a_plugin[$e]->me_code,$a_plugin[$e]->me_file));

            $class=($e%2==0)?'odd':'even';
            ?>
        <tr class="<?php echo $class?>">
            <td>
                <?php echo h($a_plugin[$e]->me_code); ?>
            </td>
            <td>
                <?php echo h($a_plugin[$e]->me_menu); ?>
            </td>
            <td>
                <?php echo h($a_plugin[$e]->me_description); ?>
            </td>
            <td>
                <?php echo h($a_plugin[$e]->me_file); ?>
            </td>
            <td>
                <?php 
                
                    for ($w=0;$w<$nb_profile;$w++) :
                        ?>
                    <span style="display:block">
                    
                    <?php
                        $a=new ICheckBox('plugin['.$a_plugin[$e]->me_code.']['.$a_profile[$w]['p_id'].']');
                        if ($a_profile[$w]['cnt']>0) $a->selected=true;
                        echo $a->input();
                        echo $a_profile[$w]['p_name'];
                 ?>
                    </span>
                    <?php
                    endfor;
                ?>
            </td>
        </tr>
        
        <?php endfor; ?>
    </table>
        <?php echo HtmlInput::submit('save_plugin', _('Valider')); ?>
   </form>
</div>

