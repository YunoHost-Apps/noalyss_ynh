<?php
//This file is part of NOALYSS and is under GPL 
//see licence.txt
?><!-- left div -->
<div id="calendar_box_div" class="box">
<?php echo HtmlInput::title_box(_('Calendrier'),'cal_div','zoom',"onclick=\"calendar_zoom($obj)\"");?>
<?php echo $cal->display('short',0); ?>
</div>

<div id="todo_listg_div" class="box"> <?php echo HtmlInput::title_box(_('Pense-Bête'),"todo_listg_div",'zoom'," onclick=\"zoom_todo()\"")?>

<?php
/*
 * Todo list
 */
echo dossier::hidden();
$todo=new Todo_List($cn);
$array=$todo->load_all();
$a_todo=Todo_List::to_object($cn,$array);

echo HtmlInput::button('add',_('Ajout'),'onClick="add_todo()"','smallbutton');
  echo '<table id="table_todo" class="sortable" width="100%">';
  echo '<tr><th class=" sorttable_sorted_reverse" id="todo_list_date">Date <span id="sorttable_sortrevind">&nbsp;&blacktriangle;</span></th><th>Titre</th><th></th>';
if ( ! empty ($array) )  {
  $nb=0;
  $today=date('d.m.Y');

  foreach ($a_todo as $row) {
    if ( $nb % 2 == 0 ) $odd='odd '; else $odd='even ';
    $nb++;
    echo $row->display_row($odd);
  }
}
  echo '</table>';
?>
</div>

<div id="situation_div" class="box"> 
    <?php echo HtmlInput::title_box(_("Situation"),"situation_div",'none')?>
    <table class='result'>
		<tr>
			<th>

			</th>
			<th>
                            <?php echo date('d.m.y'); ?>
			</th>
                        <th>
                            <?php echo _('En retard') ?>
                        </th>
		</tr>
		<tr>
			<td>
				<?php echo _("Action"); ?>
			</td>
			<td>
				<?php if (count($last_operation)>0): ?>
				<A class="mtitle" style="color:red;text-decoration:underline;font-weight: bolder;"onclick="display_detail('action_now_div')">
					<span class="notice">
					<?php echo count($last_operation) ?>
					&nbsp;<?php echo _("détail"); ?>
					</span>
				</A>
			<?php else: ?>
				 0
			<?php endif; ?>
			</td>

			<td >
			<?php if (count($late_operation)>0): ?>
				<A class="mtitle"  style="color:red;text-decoration:underline;;font-weight: bolder" onclick="display_detail('action_late_div')">
				<span class="notice"><?php echo count($late_operation) ?>
					&nbsp;<?php echo _("détail"); ?>
                                </span>
				</A>
			<?php else: ?>
				 0
			<?php endif; ?>
			</td>

		</tr>
		<tr>
			<td>
				<?php echo _("Paiement fournisseur"); ?>
			</td>
			<td >
			<?php if (count($supplier_now)>0): ?>
				<A class="mtitle"  style="color:red;text-decoration:underline;font-weight: bolder" onclick="display_detail('supplier_now_div')">
				<span class="notice"><?php echo count($supplier_now) ?>&nbsp;<?php echo _("détail"); ?></span>
					
				</A>
			<?php else: ?>
				 0
			<?php endif; ?>
			</td>
			<td >
			<?php if (count($supplier_late)>0): ?>
				<A class="mtitle"  style="color:red;text-decoration:underline;font-weight: bolder" onclick="display_detail('supplier_late_div')">
				<span class="notice"><?php echo count($supplier_late) ?>&nbsp;<?php echo _("détail"); ?></span>
					
				</A>
			<?php else: ?>
				 0
			<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo _("Paiement client"); ?>
			</td>
			<td>
				<?php if (count($customer_now)>0): ?>
				<A class="mtitle"  style="color:red;text-decoration:underline;font-weight: bolder" onclick="display_detail('customer_now_div')">
				<span class="notice"><?php echo count($customer_now) ?>&nbsp;<?php echo _("détail"); ?></span>
					
				</A>
			<?php else: ?>
				 0
			<?php endif; ?>
			</td>
			<td>
				<?php if (count($customer_late)>0): ?>
				<A class="mtitle"  style="color:red;text-decoration:underline;font-weight: bolder" onclick="display_detail('customer_late_div')">
				<span class="notice"><?php echo count($customer_late) ?>&nbsp;<?php echo _("détail"); ?></span>
					
				</A>
			<?php else: ?>
				 0
			<?php endif; ?>
			</td>
		</tr>
	</table>
</div>

<!-- Mini rapport -->
<?php
/*
 * Mini Report
 */
$report=$g_user->get_mini_report();

$rapport=new Acc_Report($cn);
$rapport->id=$report;
if ( $rapport->exist() == false ) {
  $g_user->set_mini_report(0);
  $report=0;
}

if ( $report != 0 ) : ?>
<div id="report_div" class="box"><?php echo HtmlInput::title_box($rapport->get_name(),'report_div','none');?>
<?php    
  $exercice=$g_user->get_exercice();
  if ( $exercice == 0 ) {
    alert(_('Aucune periode par defaut'));
  } else {
    $periode=new Periode($cn);
    $limit=$periode->limit_year($exercice);

    $result=$rapport->get_row($limit['start'],$limit['end'],'periode');
    $ix=0;
    if ( count ($result) >  0)
    {
        echo '<table border="0" width="100%">';
        foreach ($result as $row) {
          $ix++;
              $class=($ix%2==0)?' class="even" ':' class="odd" ';
          echo '<tr '.$class.'>';

          echo '<td> '.$row['desc'].'</td>'.
            '<td style="text-align:right">'.nbm($row['montant'])." &euro;</td>";
          echo '</tr>';
        }
        echo '</table>';
    } else {
        echo _('Aucun résultat');
    }
  }
  ?>
  </div>
<?php
  else :
?>
  <div id="report_div" class="box"> <?php echo HtmlInput::title_box(_('Aucun rapport défini'),'report_div','none')?>
<p>
  <a href="javascript:void(0)" class="cell" onclick="set_preference('<?php echo dossier::id()?>')"><?php echo _('Cliquez ici pour mettre à jour vos préférences')?></a>
<p>
</div>
<?php
endif;
?>

    
<div id="action_late_div"  class="inner_box" style="position:fixed;display:none;margin-left:12%;top:25%;width:75%;min-height:50%;overflow: auto;">
	<?php
		echo HtmlInput::title_box(_("Action en retard"), "action_late_div","hide")
	?>
	<ol>
	<?php if (count($late_operation)> 0) :

	for($i=0;$i<count($late_operation);$i++):
	?>
	<li>
		<?php echo HtmlInput::detail_action($late_operation[$i]['ag_id'],h($late_operation[$i]['ag_ref']))?>
	<span>
	<?php echo smaller_date($late_operation[$i]['ag_timestamp_fmt'])?>
	</span>
		<span  style="font-weight: bolder ">
			<?php echo h($late_operation[$i]['vw_name'])?>
		</span>
	<span>
	<?php echo h(mb_substr($late_operation[$i]['ag_title'],0,50,'UTF-8'))?>
	</span>
	<span style="font-style: italic">
	<?php echo $late_operation[$i]['dt_value']?>
	</span>
	</li>
	<?php endfor;?>
	</ol>
	<?php else : ?>
	<h2 class='notice'><?php echo _("Aucune action en retard")?></h2>
	<?php endif; ?>
	</div>

	<div id="action_now_div" class="inner_box" style="display:none;margin-left:25%;width: 50%;top:25%;min-height:50%;overflow: auto;">
	<?php
		echo HtmlInput::title_box(_("Action pour aujourd'hui"), "action_now_div","hide")
	?>
	<ol>
	<?php
	if (count($last_operation)> 0) :
	for($i=0;$i<count($last_operation);$i++):
	?>
	<li>
		<?php echo HtmlInput::detail_action($last_operation[$i]['ag_id'],h($last_operation[$i]['ag_ref']))?>
	<span>
	<?php echo smaller_date($last_operation[$i]['ag_timestamp_fmt'])?>
	</span>
		<span  style="font-weight: bolder ">
			<?php echo h($last_operation[$i]['vw_name'])?>
		</span>
	<span>
	<?php echo h(mb_substr($last_operation[$i]['ag_title'],0,50,'UTF-8'))?>
	</span>
	<span style="font-style: italic">
	<?php echo $last_operation[$i]['dt_value']?>
	</span>
	</li>
	<?php endfor;?>
	</ol>
<?php endif; ?>
	</div>
	<?php display_dashboard_operation($supplier_now,_("Fournisseurs à payer aujourd'hui"),'supplier_now_div'); ?>
	<?php display_dashboard_operation($supplier_late,_("Fournisseurs en retad"),'supplier_late_div'); ?>
	<?php display_dashboard_operation($customer_now,_("Encaissement clients aujourd'hui"),'customer_now_div'); ?>
	<?php display_dashboard_operation($customer_late,_("Clients en retard"),'customer_late_div'); ?>
</div>



<div id="last_operation_box_div" class="box">
<?php echo HtmlInput::title_box(_('Dernières opérations'),"last_operation_box_div",'zoom','onclick="popup_recherche('.dossier::id().')"')?>

<table style="width: 100%">
<?php
for($i=0;$i<count($last_ledger);$i++):
	$class=($i%2==0)?' class="even" ':' class="odd" ';
?>
<tr <?php echo $class ?>>
	<td class="box">
            <?php echo   smaller_date($last_ledger[$i]['jr_date_fmt'])?>
	</td>
	<td class="box">
		<?php echo $last_ledger[$i]['jr_pj_number']?>
            
        </td>
<td class="box">
   <?php echo h(mb_substr($last_ledger[$i]['jr_comment'],0,40,'UTF-8'))?>
</td>
<td class="box">
<?php echo HtmlInput::detail_op($last_ledger[$i]['jr_id'], $last_ledger[$i]['jr_internal'])?>
</td>
<td class="num box">
<?php echo nbm($last_ledger[$i]['jr_montant'])?>
</td>

</tr>
<?php endfor;?>
</table>
    
</div>
<div id="last_operation_management_div" class="box">
    <?php 
     echo HtmlInput::title_box(_('Suivi'),"last_operation_management_div",'zoom','onclick="action_show('.dossier::id().')"');
    ?>
    <?php
    require_once NOALYSS_INCLUDE.'/class_follow_up.php';
    $gestion=new Follow_Up($cn);
    $array=$gestion->get_last(MAX_ACTION_SHOW);
    $len_array=count($array);
    ?>
    <table style="width: 100%">
    <?php
    for ($i=0;$i < $len_array;$i++) :
    ?>
        <tr class=" <?php echo ($i%2==0)?'even':'odd'?>">
            <td class="box">
                <?php echo smaller_date($array[$i]['ag_timestamp_fmt']) ;?>
            </td>
            <td class="box">
                <?php echo HtmlInput::detail_action($array[$i]['ag_id'], $array[$i]['ag_ref'], 1)  ?>
            </td>
            <td class="box">
                <?php echo mb_substr(h($array[$i]['quick_code']),0,15)?>
            </td>
            <td class="box cut">
                <?php echo h($array[$i]['ag_title'])?>
            </td>
        </tr>
    <?php
    endfor;
    ?>
    </table>
</div>

<div id="add_todo_list" class="box" style="display:none">
	<script charset="utf-8" type="text/javascript" language="javascript">
		new Draggable($('add_todo_list'),{});
	</script>
<form method="post">
<?php
$wDate=new IDate('p_date_todo');
$wDate->id='p_date_todo';
$wTitle=new IText('p_title');
$wDesc=new ITextArea('p_desc');
$wDesc->heigh=5;
$wDesc->width=40;
echo HtmlInput::title_box("Note","add_todo_list","hide");
echo _("Date")." ".$wDate->input().'<br>';
echo _("Titre")." ".$wTitle->input().'<br>';
echo _("Description")."<br>".$wDesc->input().'<br>';
echo dossier::hidden();
echo HtmlInput::hidden('tl_id',0);
echo HtmlInput::submit('save_todo_list',_('Sauve'),'onClick="Effect.Fold(\'add_todo_list\');return true;"');
echo HtmlInput::button('hide',_('Annuler'),'onClick="Effect.Fold(\'add_todo_list\');return true;"');
?>
</form>
</div>

<script type="text/javascript" language="javascript" charset="utf-8">
function display_detail(div) {
	$(div).style.display="block";
       // $(div).style.top=calcy('150')+'px';
	//Effect.Grow(div,{});
}
try {
var array=Array('customer_now_div','customer_late_div','supplier_now_div','supplier_late_div','action_now_div','action_late_div');
var i=0;
for  (i=0;i < array.length;i++) {
	new Draggable(array[i],{});
	}
} catch (e) { alert(e.getMessage);}
</script>
