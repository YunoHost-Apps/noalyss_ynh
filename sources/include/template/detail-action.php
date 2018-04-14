<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><div>
<h2 class="gest_name"><?php echo $sp->input();   ?></h2>
<div style="width:47%;float:left;">


    <table>
			<tr class="highlight">
            <TD>
	    <?php echo _('N° document')?>
            </TD>
            <TD >
              <?php echo $this->ag_id;?>
            </TD>
          </TR>
			 <tr>
            <TD>
	    <?php echo _('Reference')?>
            </TD>
            <TD>
              <?php echo $str_ag_ref;
              ?>
            </TD>
          </TR>
   <tr>
            <TD>
	    <?php echo _('Type')?>
            </TD>
            <TD>
              <?php echo $str_doc_type;
              ?>
            </TD>
          </tr>
        <tr>

	<tr>
          <TD>
	    <?php echo _('Destinataire')?>
          </TD>
          <TD>
  <?php echo $w->search().$w->input();
            ?>
          </td>
          </Tr>
	<tr>
          <TD>
	  <?php echo _('Contact')?>
          </TD>
          <TD>
  <?php 
  if  ($g_user->can_write_action($this->ag_id) == true ):
    echo $ag_contact->search().$ag_contact->input();
  endif;
            ?>
          </td>
          </Tr>
	<tr>
          <TD colspan="2">
             <?php echo $spcontact->input(); ?>
          </td>
          </Tr>
          <?php if ($this->ag_id > 0 ): ?>
          <tr>
              <td>
                  <?php echo _('Autres concernés')?>
              </td>
              <td id="concerned_card_td">
              <?php 
                    echo $this->display_linked();
                     if  ($g_user->can_write_action($this->ag_id) == true ):
                        echo HtmlInput::button_action_add_concerned_card( $this->ag_id);
                     endif;
               ?>
              </td>
              <td>

              </td>
          </tr>
          <?php endif; ?>
        </table>
 <?php if ($p_view != 'READ') echo $str_add_button;?>

</div>
<div style="width:47%;float:left">
        <table>

         
            <TD>
   <?php echo _('Date')?>
            </TD>
            <TD>
              <?php echo $date->input();
              ?>
            </TD>
          </TR>
          <TR>
            <TD>
	    <?php echo _('Heure')?>
            </TD>
            <TD>
              <?php echo $str_ag_hour;
              ?>
            </TD>
          </TR>
          <tr>
		<TR>
            <TD>
	    <?php echo _('Date limite')?>
            </TD>
            <TD>
              <?php echo $remind_date->input();
              ?>
            </TD>
          </TR>
          <tr>
            <TD>
	    <?php echo _('Etat')?>
            </TD>
            <td>
              <?php echo $str_state;
              ?>
            <TD>
            </TD>
          </TR>
          <tr>
            <TD>
	    <?php echo _('Priorité')?>
            </TD>
            <td>
              <?php echo $str_ag_priority;
              ?>
            <TD>
            </TD>
          </TR>
          <tr>
            <TD>
	    <?php echo _('Groupe Gestion')?>
            </TD>
            <td>
              <?php echo $str_ag_dest;?>
          </tr>
<?php if ($this->ag_id > 0 ): ?>
          <tr>
            <TD>
                Dossier / tags
            </TD>
            
            <td id="action_tag_td">
                <?php
                   $this->tag_cell();
                ?>
            </td>
          </TR>
<?php endif; ?>          
        </table>

</div>
<div style="clear: both"></div>
	<div style="float:left;width: 47%">
		<h4 style="display:inline;">Opérations concernées</h4>
		<ol>

		<?php
		for ($o=0;$o<count($operation);$o++)
		{
			if ( $p_view != 'READ')
				{
                                        $js  = HtmlInput::button_action_remove_operation($operation[$o]['ago_id']);
					echo '<li id="op'.$operation[$o]['ago_id'].'">'.$operation[$o]['str_date']." ".HtmlInput::detail_op($operation[$o]['jr_id'],$operation[$o]['jr_internal'])." ".h($operation[$o]['jr_comment'])." "
						.$js.'</li>';
				}
				else
				{
					echo '<li >'.$operation[$o]['str_date']." ".HtmlInput::detail_op($operation[$o]['jr_id'],$operation[$o]['jr_internal'])." ".h($operation[$o]['jr_comment'])." "
						.'</li>';
				}
		}

		?>
		</ol>
		<?php if ($p_view != 'READ')   echo '<span class="noprint">'.$iconcerned->input().'</span>';?>
	</div>

        <div style="float:left;width: 47%">
		<h4 style="display:inline"><?php echo _("Actions concernées")?></h4>
		<ol>

		<?php
		$base=HtmlInput::request_to_string(array("gDossier","ac","sa","sb","sc","f_id"));
		for ($o=0;$o<count($action);$o++)
		{
			if ( $p_view != 'READ' && $p_base != 'ajax')
			{
                            $rmAction=sprintf("return confirm_box(null,'"._('Voulez-vous effacer cette action ')."', function () {remove_action('%s','%s','%s');});",
					dossier::id(),
					$action[$o]['ag_id'],$_REQUEST['ag_id']);
                            $showAction='<a class="line" href="'.$base."&ag_id=".$action[$o]['ag_id'].'">';
                            $js= '<a class="tinybutton" id="acact'.$action[$o]['ag_id'].'" href="javascript:void(0)" onclick="'.$rmAction.'">'.SMALLX.'</a>';
                            echo '<li id="act'.$action[$o]['ag_id'].'">'.$showAction.$action[$o]['str_date']." ".$action[$o]['ag_ref']." ".
					h($action[$o]['sub_title']).'('.h($action[$o]['dt_value']).')</a>'." "
				.$js.'</li>';
			} else 
                        /*
                         * Display detail requested from Ajax Div
                         */
                         if ( $p_base == 'ajax' )
                         {
                            $xaction = sprintf('view_action(%d,%d,%d)',$action[$o]['ag_id'],Dossier::id(),1);
                            $showAction='<a class="line" href="javascript:'.$xaction.'">';
                            echo '<li>'.$showAction.$action[$o]['str_date']." ".$action[$o]['ag_ref']." ".
					h($action[$o]['sub_title']).'('.h($action[$o]['dt_value']).')</a>'." "
				.'</li>';
                         }
                         /*
                          * READ ONLY
                          */
                         else
                         {
				$showAction='<a class="line" href="'.$base."&ag_id=".$action[$o]['ag_id'].'">';
				echo '<li>'.$showAction.$action[$o]['str_date']." ".$action[$o]['ag_ref']." ".
					h($action[$o]['sub_title']).'('.h($action[$o]['dt_value']).')</a>'." "
				.'</li>';
			}
		}

		?>
		</ol>
		<?php if ( $p_view != 'READ') echo '<span class="noprint">'.$iaction->input().'</span>';?>
	</div>
</div>
<div style="clear: both"></div>
<div id="div_action_description">
  <h1 class="legend">
	    <?php echo _('Description')?>
  </h1>
  <p>
<script language="javascript">
   function enlarge(p_id_textarea){
   $(p_id_textarea).style.height=$(p_id_textarea).style.height+250+'px';
   $('bt_enlarge').style.display="none";
   $('bt_small').style.display="inline";
 }
function small(p_id_textarea){
   $('bt_enlarge').style.display="inline";
   $('bt_small').style.display="none";

   }
</script>
<?php if  ($p_view != 'NEW') : ?>
Document créé le <?php echo $this->ag_timestamp ?> par <?php echo $this->ag_owner?>
<?php endif; ?>
  <h4 class="info" style="margin-left:110px"><?php echo _('Titre')?></h4>
    <p style="margin-left:100px">
    <?php echo $title->input();
    ?>
</p>
    <div style="margin-left:100px">
   <?php
   $style_enl='style="display:inline"';$style_small='style="display:none"';

for( $c=0;$c<count($acomment);$c++){
        if ($c == 0) { $m_desc=_('Description');}
        else
        if ($c == 1) { $m_desc=_('Commentaire');}
        else
         { $m_desc="";}?>
        <h4 class="info" >   <?php echo $m_desc;?></h4>

	<?php
        if ( $p_view != 'READ')
	{
		$rmComment=sprintf("return confirm_box(null,'"._('Voulez-vous effacer ce commentaire')." ?',function() {remove_comment('%s','%s');});",
						dossier::id(),
						$acomment[$c]['agc_id']);
				$js= '<a class="tinybutton" id="accom'.$acomment[$c]['agc_id'].'" href="javascript:void(0)" onclick="'.$rmComment.'">'.SMALLX.'</a>';
		echo hb('n°'.$acomment[$c]['agc_id'].'('.$acomment[$c]['tech_user']." ".$acomment[$c]['str_agc_date'].')').$js.
				'<pre style="white-space: -moz-pre-wrap;white-space: pre-wrap;border:1px solid blue;width:80%;" id="com'.$acomment[$c]['agc_id'].'"> '.
				" ".h($acomment[$c]['agc_comment']).'</pre>'
				;
	}
	else
	{
		echo hb('n°'.$acomment[$c]['agc_id'].'('.$acomment[$c]['tech_user']." ".$acomment[$c]['str_agc_date'].')').
				'<pre style="white-space: -moz-pre-wrap;white-space: pre-wrap;border:1px solid blue;width:80%;" id="com'.$acomment[$c]['agc_id'].'"> '.
				" ".h($acomment[$c]['agc_comment']).'</pre>'
				;

	}
}
echo '<span class="noprint">';
echo $desc->input();
echo '</span>';
?>
<?php if ($p_view != "READ" ): ?>
<p class="noprint">
<input type="button" id="bt_enlarge" <?php echo $style_enl?> value="+" onclick="enlarge('ag_comment');return false;">
<input type="button" id="bt_small"  <?php echo $style_small?> value="-" style="display:none" onclick="small('ag_comment');return false;">
</p>
<?php endif; ?>
  </div>
</div>
<?php if ( $p_view !='READ'  ) :?>
<input type='button' class="button" class="noprint" value="<?php echo _('Montrer articles');?>" id="toggleButton" onclick='toggleShowDetail()'>
<input type='button' class="button" class="noprint" value="<?php echo _('Générer')?>" id="toggleButtonGenerate" onclick="$('div_generate_document').show()">
<?php endif; ?>
<?php
/**
 * check if there card to show,
 */
$show_row=0;
for ($i=0;$i<count($aArticle);$i++) :
	if ( ($aCard[$i] != 0 && $p_view == 'READ') || $p_view != 'READ'){ $show_row=1;break;}
endfor;
?>
<?php
/*
 * display detail if there card or if we are in UPDATE or NEW mode
 */
if ($show_row !=0 ) :

	?>
<div id="fldDetail" class="myfieldset" style='padding-bottom:  100px;display:block;top:2px'>
   <LEGEND> <?php echo _('Détail')?>
</LEGEND>
<?php // hidden fields
$show_row=0;
for ($i=0;$i<count($aArticle);$i++) :
	echo $aArticle[$i]['ad_id'];
	echo $aArticle[$i]['hidden_tva'];
	echo $aArticle[$i]['hidden_htva'];
	if ( ($aCard[$i] != 0 && $p_view == 'READ') || $p_view != 'READ'){ $show_row=1;}
endfor;
?>
    <div style="position:relative;top:5px">
<table style="width:100%" id="art" >
<tr>
  <th><?php echo _('Fiche')?></th>
  <th><?php echo _('Description')?></th>
  <th><?php echo _('prix unitaire')?></th>
<th><?php echo _('quantité')?></th>
<th><?php echo _('Code TVA')?></th>
<th><?php echo _('Montant TVA')?></th>
<th><?php echo _('Montant TVAC')?></th>

</tr>
<?php for ($i=0;$i<count($aArticle);$i++): ?>
<?php
if ( ($aCard[$i] != 0 && $p_view == 'READ') || $p_view != 'READ'):
	$show_row++;
	?>
<TR>
<TD><?php echo $aArticle[$i]['fid'] ?></TD>
<TD><?php echo $aArticle[$i]['desc'] ?></TD>
<TD class="num"><?php echo $aArticle[$i]['pu'] ?></TD>
<TD class="num"><?php echo $aArticle[$i]['quant'] ?></TD>
<TD class="num"><?php echo $aArticle[$i]['tvaid'] ?></TD>
<TD class="num"><?php echo $aArticle[$i]['tva'] ?></TD>
<TD class="num"><?php echo $aArticle[$i]['tvac'] ?></TD>
</TR>
<?php endif; ?>
<?php endfor; ?>
</table>
    </div>
<script language="JavaScript">
if ( $('e_march0') && $('e_march0').value =='') { toggleShowDetail();}
$('div_generate_document').hide();
function toggleShowDetail() {
	try {var detail=g('fldDetail');
	var but=g('toggleButton');
	if (detail.style.display=='block' ) { but.value="<?php echo _("Montrer les détails")?>";detail.style.display='none';}
	else { but.value="<?php echo _("Cacher les détails")?>";detail.style.display='block';} }
	catch (error)  {alert(error);}
	}
</script>    

<?php if ( $show_row != 0 ): ?>
<div>
  
    <div style=" float:right;margin-right: 2px" id="sum">
    <br><span style="text-align: right;" class="highlight" id="htva"><?php echo bcsub($tot_item,$tot_vat) ?></span>
     <br><span style="text-align: right" class="highlight" id="tva"><?php echo $tot_vat?></span>
    <br><span style="text-align: right" class="highlight" id="tvac"><?php echo $tot_item?></span>
 </div>

    <div  style="float:right;margin-right: 230px" >
    <br>Total HTVA
    <br>Total TVA
    <br>Total TVAC
 </div>

 <?php if ( ! $readonly ) :  ?>
    <div style="float:right" >
    <input name="act" id="act_bt" class="smallbutton" value="<?php echo _('Actualiser')?>" onclick="compute_all_ledger();" type="button">
     <input type="button" class="smallbutton" onclick="gestion_add_row()" value="<?php echo _("Ajouter une ligne")?>">
     </div>
     
<?php endif; ?>         
</div>
<?php if ( $this->ag_id != 0 && ! $readonly) : ?>
     <div >
         <p>
         <?php
            $query=  http_build_query(array('gDossier'=>Dossier::id(),'ag_id'=>$this->ag_id,'create_invoice'=>1,'ac'=>$menu->get('code_invoice')));
            echo HtmlInput::button_anchor(_("Transformer en facture"),"do.php?".$query,"create_invoice", '  target="_blank" ',"button");
         ?>
         </p>
      </div>
     <?php endif; ?>
<?php endif; ?>
</div>
<?php endif; ?>

<div style="clear:both"></div>    
<?php if ($p_view != 'READ' && $str_select_doc != '') : ?>
<div id="div_generate_document" class="noprint" style="display:none" >
  <legend>
     <?php echo _('Document à générer')?>
  </legend>
  <?php echo $str_select_doc;
 echo $str_submit_generate;
  ?>
</div>
<?php endif; ?>


<div class="myfieldset" id="div_action_attached_doc">
  <legend>
     <?php echo _('Pièces attachées')?>
  </legend>
  <div class="print">
      <table>
  <?php
for ($i=0;$i<sizeof($aAttachedFile);$i++) :
  ?>

      <tr>
          <td>
              <A class="print" style="display:inline" id="<?php echo "doc".$aAttachedFile[$i]['d_id'];?>" href="<?php echo $aAttachedFile[$i]['link']?>">
          <?php echo $aAttachedFile[$i]['d_filename'];?>         </a>
          </td>
          <td>
        <label> : </label>
        <span id="print_desc<?php echo $aAttachedFile[$i]['d_id'];?>"> <?php echo h($aAttachedFile[$i]['d_description'])?>
       <?php if ($p_view != 'READ') : ?> 
        <?php 
            $js=sprintf("javascript:show_description('%s')",$aAttachedFile[$i]['d_id']);
        ?>
        <a class="line"  id="<?php echo 'desc'.$aAttachedFile[$i]['d_id'];?>" onclick="<?php echo $js?>"><?php echo _("Modifier")?></a>    
        
        </span>
        </td>
        <td>
        <span class="noprint" id="input_desc<?php echo $aAttachedFile[$i]['d_id'];?>" style="display:none" >
              <input type="input" class="input_text" id="input_desc_txt<?php echo $aAttachedFile[$i]['d_id'];?>" value="<?php echo h($aAttachedFile[$i]['d_description'])?>">
              <?php 
              $js=sprintf("update_document('%s','%s')",dossier::id(),$aAttachedFile[$i]['d_id']);
              echo HtmlInput::button('save_desc'.$aAttachedFile[$i]['d_id'], _('Sauve'), 'onclick="'.$js.'"','smallbutton');
              ?>
        </span>
        <?php else: ?>
        </span>
        <?php endif;?>
<?php $rmDoc=sprintf("return confirm_box(null,'"._('Voulez-vous effacer le document')." %s' , function(){remove_document('%s','%s');});",
	$aAttachedFile[$i]['d_filename'],
	dossier::id(),
	$aAttachedFile[$i]['d_id']);
    ?>
        </td>
        <td>
  <?php if ($p_view != 'READ') : ?>  <a class="line"  id="<?php echo "ac".$aAttachedFile[$i]['d_id'];?>" href="javascript:void(0)" onclick="<?php echo $rmDoc;?>"><?php echo _("Effacer")?></a><?php endif;?>
        </td>
  </tr>
  <?php
endfor;
  ?>
  </table>
  </div>
  <script language="javascript">
function addFiles() {
try {
	docAdded=document.getElementById('add_file');
	new_element=document.createElement('li');
	new_element.innerHTML='<input class="inp" type="file" value="" name="file_upload[]"/><label>Description</label> <input type="input" class="input_text" name="input_desc[]" >';
	docAdded.appendChild(new_element);
}
catch(exception) { alert('<?php echo j(_('Je ne peux pas ajouter de fichier'))?>'); alert(exception.message);}
}
</script>
<?php if ($p_view != 'READ') : ?>
  <div class="noprint">
     <h3 >Fichiers à ajouter: </h3>
    <ol id='add_file'  >
      <li>
        <?php echo $upload->input();
        ?>
        <label><?php echo _('Description')?></label>
        <input type="input" class="input_text" name="input_desc[]" >
      </li>
    </ol>
  <span   >
 <input type="button" class="smallbutton" onclick="addFiles();" value="<?php echo _("Ajouter un fichier")?>">
  </span>
  </div>
 <?php endif;?>
</div>
</div>
<script>compute_all_ledger()</script>
