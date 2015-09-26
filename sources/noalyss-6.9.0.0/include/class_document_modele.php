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
/*! \file
 * \brief Class for the document template
 */
/*!
 * \brief Class for the document template
 */
require_once NOALYSS_INCLUDE.'/class_icheckbox.php';
require_once NOALYSS_INCLUDE.'/class_ihidden.php';
require_once NOALYSS_INCLUDE.'/class_ifile.php';
class Document_modele
{
    var $cn;         	/*!< $cn  database connection */
    var $md_id;	        /*!< $md_id pk */
    var $md_name;         /*!< $md_name template's name */
    var $md_type;         /*!< $md_type template's type (letter, invoice, order...) */
    var $md_lob;          /*!< $md_lob Document file */
    var $md_sequence;     /*!<  $md_sequence sequence name (autogenerate) */
    var $sequence;        /*!< $sequence sequence number used by the create sequence start with */
    var $md_affect;	/*!< $md_affect if you can use it in VEN for sale, ACH for purchase or GES for follow-up */
    //Constructor parameter = database connexion
    function Document_modele($p_cn,$p_id=-1)
    {
        $this->cn=$p_cn;
        $this->md_id=$p_id;
    }

    /*!
     **************************************************
     * \brief : show all the stored document_modele.
     *        return a string containing all the data
     *        separate by TD & TR tag
     * \return table in HTML Code
     */
    function myList()
    {
        $s=dossier::get();
        $sql="select md_id,md_name,md_affect,dt_value from document_modele join document_type on(dt_id=md_type) order by md_name";
        $Res=$this->cn->exec_sql($sql);
        $all=Database::fetch_all($Res);
	$r='';
        if ( Database::num_row($Res) != 0 ) {

	  $r.='<p><form method="post">';
	  $r.=dossier::hidden();
	  $r.="<table>";
	  $r.="<tr> ";
	  $r.=th(_('Nom'));
	  $r.=th(_('Catégorie'));
	  $r.=th(_('Affect.'));
	  $r.=th(_('Fichier'));
	  $r.=th(_('Effacer'));
	  $r.="</tr>";
	  foreach ( $all as $row)
	    {
	      $r.="<tr>";
	      $r.="<td>";
	      $r.=h($row['md_name']);
	      $r.="</td>";
	      $r.="<td>";
	      $r.=$row['dt_value'];
	      $r.="</td>";
	      $r.=td(h($row['md_affect']));
	      $r.="<td>";
	      $r.='<A HREF="show_document_modele.php?md_id='.$row['md_id'].'&'.$s.'">Document</a>';
	      $r.="</td>";
	      $r.="<TD>";
	      $c=new ICheckBox();
	      $c->name="dm_remove_".$row['md_id'];
	      $r.=$c->input();
	      $r.="</td>";
	      $r.=td(HtmlInput::detail_modele_document($row['md_id'],'Modifier'));

	      $r.="</tr>";
	    }
	  $r.="</table>";

	  // need hidden parameter for subaction
	  $a=new IHidden();
	  $a->name="sa";
	  $a->value="rm_template";
	  $r.=$a->input();
	  $r.=HtmlInput::submit("rm_template","Effacer la sélection");
	}
	$b=new IButton('show');
	$b->label="Ajout d'un document";
	$b->javascript="$('add_modele').style.display='block';new Draggable('add_modele',{starteffect:function(){
                      new Effect.Highlight(obj.id,{scroll:window,queue:'end'});}});";
		$r.=$b->input();
        $r.="</form></p>";
        return $r;
    }
    /*!
     * \brief :  Save a document_modele in the database,
     *       if the document_modele doesn't exist yet it will be
     *       first created (-> insert into document_modele)
     *       in that case the name and the type must be set
     *       set before calling Save, the name will be modified
     *       with sql_string
     *
     */
    function Save()
    {
        // if name is empty return immediately
        if ( trim(strlen($this->md_name))==0)
            return;
        try
        {
            // Start transaction
            $this->cn->start();
            // Save data into the table document_modele
            // if $this->md_id == -1 it means it is a new document model
            // so first we have to insert it
            // the name and the type must be set before calling save
            if ( $this->md_id == -1)
            {

                // insert into the table document_modele
                $this->md_id=$this->cn->get_next_seq('document_modele_md_id_seq');
                $sql="insert into document_modele(md_id,md_name,md_type,md_affect)
                     values ($1,$2,$3,$4)";

                $Ret=$this->cn->exec_sql($sql,array($this->md_id,$this->md_name,$this->md_type,$this->md_affect));
                // create the sequence for this modele of document
                $this->md_sequence="document_".$this->cn->get_next_seq("document_seq");
                // if start is not equal to 0 and he's a number than the user
                // request a number change

                if ( $this->start != 0 && isNumber($this->start) == 1 )
                {
                    $sql="alter sequence seq_doc_type_".$this->md_type." restart ".$this->start;
                    $this->cn->exec_sql($sql);
                }

            }
            // Save the file
            $new_name=tempnam($_ENV['TMP'],'document_');
            if ( strlen ($_FILES['doc']['tmp_name']) != 0 )
            {
                if (move_uploaded_file($_FILES['doc']['tmp_name'],
                                       $new_name))
                {
                    // echo "Image saved";
                    $oid= $this->cn->lo_import($new_name);
                    if ( $oid == false )
                    {
                        echo_error('class_document_modele.php',__LINE__,"cannot upload document");
                        $this->cn->rollback();
                        return;
                    }
                    // Remove old document
                    $ret=$this->cn->exec_sql("select md_lob from document_modele where md_id=".$this->md_id);
                    if (Database::num_row($ret) != 0)
                    {
                        $r=Database::fetch_array($ret,0);
                        $old_oid=$r['md_lob'];
                        if (strlen($old_oid) != 0)
                            $this->cn->lo_unlink($old_oid);
                    }
                    // Load new document
                    $this->cn->exec_sql("update document_modele set md_lob=".$oid.", md_mimetype='".$_FILES['doc']['type']."' ,md_filename='".$_FILES['doc']['name']."' where md_id=".$this->md_id);
                    $this->cn->commit();
                }
                else
                {
                    echo "<H1>Error</H1>";
                    $this->cn->rollback();
                   throw new Exception("Erreur".__FILE__.__LINE__);
                }
            }
        }
        catch (Exception $e)
        {
            rollback($this->cn);
            return ;
        }
    }
    /*!
     * \brief Remove a template
     * \return nothing
     */
    function Delete()
    {
        $this->cn->start();
        // first we unlink the document
        $sql="select md_lob from document_modele where md_id=".$this->md_id;
        $res=$this->cn->exec_sql($sql);
        $r=Database::fetch_array($res,0);
        // if a lob is found
        if ( strlen ($r['md_lob']) &&
                $this->cn->exist_blob($r['md_lob']) )
        {
            // we remove it first
            $this->cn->lo_unlink($r['md_lob']);
        }
        // now we can delete the row
        $sql="delete from document_modele where md_id =".$this->md_id;
        $sql=$this->cn->exec_sql($sql);
        $this->cn->commit();
    }

    /**
     * @brief show the form for loading a template
     * @param p_action for the field action = destination url
     *
     *
     * @return string containing the forms
     */
    function form()
    {
        $r='<p class="notice">';
        $r.='Veuillez introduire les mod&egrave;les servant à g&eacute;n&eacute;rer vos documents';
        $r.='</p>';
        $r.='<form enctype="multipart/form-data"  method="post">';
        $r.=dossier::hidden();
        // we need to add the sub action as hidden
        $h=new IHidden();
        $h->name="sa";
        $h->value="add_document";

        $r.=$h->input();

        $r.='<table>';
        $t=new IText();
        $t->name="md_name";
        $r.="<tr><td> Nom </td><td>".$t->input()."</td>";

        $r.="</tr>";
        $r.="<tr><td>Catégorie de document </td>";
        $w=new ISelect();
        $w->name="md_type";

        $w->value=$this->cn->make_array('select dt_id,dt_value from document_type order by dt_value');
        $r.="<td>".$w->input()."</td></tr>";

        $r.='<tr>';
        $r.=td(_('Affectation'));
        $waffect=new ISelect();
        $waffect->name='md_affect';
        $waffect->value=array(
                            array('value'=>'ACH','label'=>_('Uniquement journaux achat')),
                            array('value'=>'VEN','label'=>_('Uniquement journaux vente')),
                            array('value'=>'GES','label'=>_('Partie gestion'))
                        );

        $r.=td($waffect->input());
        $r.='</tr>';

        $f=new IFile();
        $f->name="doc";
        $r.="<tr><td>fichier</td><td> ".$f->input()."</td></tr>";

        $start=new IText();
        $start->name="start_seq";
        $start->size=9;
        $start->value="0";

        $r.="<tr><td> Numerotation commence a</td><td> ".$start->input()."</td>";
        $r.='<td class="notice">Si vous laissez &agrave; 0, la num&eacute;rotation ne changera pas, la prochaine facture sera n+1, n étant le n° que vous avez donn&eacute;</td>';
        $r.="</tr>";
        $r.='</table>';
        $r.=HtmlInput::submit('add_document','Ajout');
        $r.="</form></p>";
        return $r;
    }
    /*!\brief load the value of a document_modele,the ag_id variable must be set
     */
    function load()
    {
        $array=$this->cn->get_array("SELECT md_id, md_name, md_lob, md_type, md_filename, md_mimetype,md_affect".
                                    " FROM document_modele where md_id=$1",array($this->md_id));
        if ( count($array) == 0 ) return null;
        foreach ( array('md_name', 'md_lob','md_type', 'md_filename', 'md_mimetype','md_affect') as $idx)
        {
            $this->$idx=$array[0][$idx];
        }
    }
    /*!
     * \brief :  update a document_modele in the database,
     */
    function update($p_array)
    {
      $this->load();
        // if name is empty return immediately
        if ( trim(strlen($p_array['md_name']))==0)
            return;
        try
        {
            // Start transaction
            $this->cn->start();
	    $sql="update document_modele set md_name=$1,md_type=$2,md_affect=$3 where md_id=$4";
	    $this->cn->exec_sql($sql,array(
					   $p_array['md_name'],
					   $p_array['md_type'],
					   $p_array['md_affect'],
					   $this->md_id
					   ));
	    if ( $p_array['seq'] != 0 )
	      $this->cn->alter_seq('seq_doc_type_'.$p_array['md_type'],$p_array['seq']);

            // Save the file
            $new_name=tempnam($_ENV['TMP'],'document_');
            if ( strlen ($_FILES['doc']['tmp_name']) != 0 )
            {
                if (move_uploaded_file($_FILES['doc']['tmp_name'],
                                       $new_name))
                {
                    // echo "Image saved";
                    $oid= $this->cn->lo_import($new_name);
                    if ( $oid == false )
                    {
                        echo_error('class_document_modele.php',__LINE__,"cannot upload document");
                        $this->cn->rollback();
                        return;
                    }
                    // Remove old document
                    $ret=$this->cn->exec_sql("select md_lob from document_modele where md_id=".$this->md_id);
                    if (Database::num_row($ret) != 0)
                    {
                        $r=Database::fetch_array($ret,0);
                        $old_oid=$r['md_lob'];
                        if (strlen($old_oid) != 0)
                            $this->cn->lo_unlink($old_oid);
                    }
                    // Load new document
                    $this->cn->exec_sql("update document_modele set md_lob=".$oid.", md_mimetype='".$_FILES['doc']['type']."' ,md_filename='".$_FILES['doc']['name']."' where md_id=".$this->md_id);
                    $this->cn->commit();
                }
                else
                {
                    echo "<H1>Error</H1>";
                    $this->cn->rollback();
                    throw new Exception("Erreur".__FILE__.__LINE__);
                }
            }
        }
        catch (Exception $e)
        {
            rollback($this->cn);
            return ;
        }
	$this->cn->commit();
    }

}
?>
