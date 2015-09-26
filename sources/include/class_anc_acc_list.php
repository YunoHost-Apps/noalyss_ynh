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
 * \brief
 */

require_once NOALYSS_INCLUDE.'/class_anc_acc_link.php';

class Anc_Acc_List extends Anc_Acc_Link
{
  /**
   *@brief display form to get the parameter
   *  - card_poste 1 by card, 2 by account
   *  - from_poste
   *  - to_poste
   *  - from from date
   *  - to until date
   *  - pa_id Analytic plan to use
   */
  function display_form($p_hidden='')
  {
    $r=parent::display_form($p_hidden);
    $icard=new ISelect('card_poste');
    $icard->value=array(
			array('value'=>1,'label'=>'Par fiche /Activité'),
			array('value'=>2,'label'=>'Par poste comptable/Activité'),
			array('value'=>3,'label'=>'Par activité/Fiche'),
			array('value'=>4,'label'=>'Par activité/Poste Comptable')

			);

    $icard->selected=$this->card_poste;
    $r.=$icard->input();
    $r.=HtmlInput::request_to_hidden(array('ac'));
    return $r;
  }
 /**
   * load the data
   * does not return anything but give a value to this->aheader and this->arow
   */
  function load_anc_account()
  {
    $date=$this->set_sql_filter();
    $date=($date != '')?"  $date":'';
    $sql_from_poste=($this->from_poste!='')?" and  po.po_name >= upper('".Database::escape_string($this->from_poste)."')":'';
    $sql_to_poste=($this->to_poste!='')?" and  po.po_name <= upper('".Database::escape_string($this->to_poste)."')":'';
    $this->arow=$this->db->get_array("
 SELECT po.po_id, po.pa_id, po.po_name, po.po_description, sum(
        CASE
            WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
            ELSE operation_analytique.oa_amount
        END) AS sum_amount, jrnx.j_poste, tmp_pcmn.pcm_lib AS name
   FROM operation_analytique
   JOIN poste_analytique po USING (po_id)
   JOIN jrnx USING (j_id)
   JOIN tmp_pcmn ON jrnx.j_poste::text = tmp_pcmn.pcm_val::text ".
"					where
		pa_id=$1 ".$date.$sql_from_poste.$sql_to_poste."

  GROUP BY po.po_id, po.po_name, po.pa_id, jrnx.j_poste, tmp_pcmn.pcm_lib, po.po_description
 HAVING sum(
CASE
    WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
    ELSE operation_analytique.oa_amount
END) <> 0::numeric  order by po_id,j_poste",array($this->pa_id));

  }
  /**
   * load the data
   * does not return anything but give a value to this->aheader and this->arow
   */
  function load_anc_card()
  {
    $date=$this->set_sql_filter();
    $date=($date != '')?"  $date":'';
    $sql_from_poste=($this->from_poste!='')?" and  po.po_name >= upper('".Database::escape_string($this->from_poste)."')":'';
    $sql_to_poste=($this->to_poste!='')?" and  po.po_name <= upper('".Database::escape_string($this->to_poste)."')":'';
    $this->arow=$this->db->get_array(" SELECT po.po_id, po.pa_id, po.po_name, po.po_description, sum(
        CASE
            WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
            ELSE operation_analytique.oa_amount
        END) AS sum_amount, jrnx.f_id, jrnx.j_qcode, ( SELECT fiche_detail.ad_value
           FROM fiche_detail
          WHERE fiche_detail.ad_id = 1 AND fiche_detail.f_id = jrnx.f_id) AS name
   FROM operation_analytique
   JOIN poste_analytique po USING (po_id)
   JOIN jrnx USING (j_id) ".
				     " where pa_id=$1 ".$date.$sql_from_poste.$sql_to_poste
				     ."
  GROUP BY po.po_id, po.po_name, po.pa_id, jrnx.f_id, jrnx.j_qcode, ( SELECT fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 1 AND fiche_detail.f_id = jrnx.f_id), po.po_description
 HAVING sum(
CASE
    WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
    ELSE operation_analytique.oa_amount
END) <> 0::numeric order by po_name,name",array($this->pa_id));

  }

  /**
   * load the data
   * does not return anything but give a value to this->aheader and this->arow
   */
  function load_poste()
  {
    $date=$this->set_sql_filter();
    $date=($date != '')?"  $date":'';
    $sql_from_poste=($this->from_poste!='')?" and  po.po_name >= upper('".Database::escape_string($this->from_poste)."')":'';
    $sql_to_poste=($this->to_poste!='')?" and  po.po_name <= upper('".Database::escape_string($this->to_poste)."')":'';
  $this->arow=$this->db->get_array("SELECT po.po_id, po.pa_id, po.po_name, po.po_description, sum(
        CASE
            WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
            ELSE operation_analytique.oa_amount
        END) AS sum_amount, jrnx.j_poste, tmp_pcmn.pcm_lib AS name
   FROM operation_analytique
   JOIN poste_analytique po USING (po_id)
   JOIN jrnx USING (j_id)
   JOIN tmp_pcmn ON jrnx.j_poste::text = tmp_pcmn.pcm_val::text ".
"					where
		pa_id=$1 ".$date.$sql_from_poste.$sql_to_poste."

  GROUP BY po.po_id, po.po_name, po.pa_id, jrnx.j_poste, tmp_pcmn.pcm_lib, po.po_description
 HAVING sum(
CASE
    WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
    ELSE operation_analytique.oa_amount
END) <> 0::numeric  order by j_poste,po_name",array($this->pa_id));

  }

  /**
   * load the data
   * does not return anything but give a value to this->aheader and this->arow
   */
  function load_card()
  {
    $date=$this->set_sql_filter();
    $date=($date != '')?"  $date":'';
    $sql_from_poste=($this->from_poste!='')?" and  po.po_name >= upper('".Database::escape_string($this->from_poste)."')":'';
    $sql_to_poste=($this->to_poste!='')?" and  po.po_name <= upper('".Database::escape_string($this->to_poste)."')":'';

   $this->arow=$this->db->get_array(" SELECT po.po_id, po.pa_id, po.po_name, po.po_description, sum(
        CASE
            WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
            ELSE operation_analytique.oa_amount
        END) AS sum_amount, jrnx.f_id, jrnx.j_qcode, ( SELECT fiche_detail.ad_value
           FROM fiche_detail
          WHERE fiche_detail.ad_id = 1 AND fiche_detail.f_id = jrnx.f_id) AS name
   FROM operation_analytique
   JOIN poste_analytique po USING (po_id)
   JOIN jrnx USING (j_id) ".
				     " where pa_id=$1 ".$date.$sql_from_poste.$sql_to_poste
				     ."
  GROUP BY po.po_id, po.po_name, po.pa_id, jrnx.f_id, jrnx.j_qcode, ( SELECT fiche_detail.ad_value
   FROM fiche_detail
  WHERE fiche_detail.ad_id = 1 AND fiche_detail.f_id = jrnx.f_id), po.po_description
 HAVING sum(
CASE
    WHEN operation_analytique.oa_debit = true THEN operation_analytique.oa_amount * (-1)::numeric
    ELSE operation_analytique.oa_amount
END) <> 0::numeric order by name,po_name",array($this->pa_id));
  }
  /**
   *@brief display the button export CSV
   *@param $p_hidden is a string containing hidden items
   *@return html string
   */
  function show_button($p_hidden="")
  {
    $r="";
    $r.= '<form method="GET" action="export.php"  style="display:inline">';
    $r.= HtmlInput::hidden("act","CSV:AncAccList");
    $r.= HtmlInput::hidden("to",$this->to);
    $r.= HtmlInput::hidden("from",$this->from);
    $r.= HtmlInput::hidden("pa_id",$this->pa_id);
    $r.= HtmlInput::hidden("from_poste",$this->from_poste);
    $r.= HtmlInput::hidden("to_poste",$this->to_poste);
    $r.= HtmlInput::hidden("card_poste",$this->card_poste);

    $r.= $p_hidden;
    $r.= dossier::hidden();
    $r.=HtmlInput::submit('bt_csv',"Export en CSV");
    $r.= '</form>';
    return $r;
  }
  function display_html()
  {
    bcscale(2);
    if ( $this->check()  != 0)
      {
	alert('Désolé mais une des dates données n\'est pas valide');
	return;
      }
    //---------------------------------------------------------------------------
    // Card  - Acc
    //---------------------------------------------------------------------------

    if ( $this->card_poste=='1')
      {
	$this->load_card();

	/*
	 * Show all the result
	 */
	$tot_card=0;$prev='';
	echo '<table class="result" style="margin-left:5px;margin-top:5px">';
	$tot_glob=0;
	for ($i=0;$i<count($this->arow);$i++)
	  {
	    if ( $i == 0 )
	      {
		$prev=$this->arow[$i]['f_id'];
		echo '<tr><td>'.HtmlInput::history_card ($this->arow[$i]['f_id'],$this->arow[$i]['j_qcode'].' '.$this->arow[$i]['name'],' display:inline').'</td></tr>';
	      }
	    $style= ( $i % 2 == 0)?' class="odd" ':' class="even" ';
	    if ( $i != 0 && $prev != $this->arow[$i]['f_id'])
	      {
		echo  td('Total');
		echo td(nbm($tot_card),' class="num"');
		echo '</tr>';
		echo '<tr  style="padding-top:5px"><td>'.HtmlInput::history_card($this->arow[$i]['f_id'],$this->arow[$i]['j_qcode'].' '.$this->arow[$i]['name'],' display:inline ').'</td></tr>';
		$tot_card=0;
		$prev = $this->arow[$i]['f_id'];
	      }

	    echo '<tr '.$style.'>';
	    $amount=$this->arow[$i]['sum_amount'];
	    if ($amount==null)$amount=0;

	    $tot_card=bcadd($tot_card,$amount);
	    $tot_glob=bcadd($tot_glob,$amount);
	    echo td($this->arow[$i]['po_name']."   ".
		    $this->arow[$i]['po_description'],'style="padding-left:10"');
	    echo td(nbm($amount),' class="num" ');
	    echo '</tr>';

	  }
	echo '<tr>';
	echo  td('Total');
	echo td(nbm($tot_card),' class="num"');
	echo '</tr>';

	echo '</table>';
	echo '<h2> Résultat global '.nbm($tot_glob).'</h2>';
      }
    //---------------------------------------------------------------------------
    // Accountancy - Analytic
    //---------------------------------------------------------------------------

    if ( $this->card_poste=='2')
      {
	$this->load_poste();
	/*
	 * Show all the result
	 */
	$tot_card=0;$prev='';
	echo '<table class="result" style="margin-left:20px;margin-top:5px">';
	$tot_glob=0;
	for ($i=0;$i<count($this->arow);$i++)
	  {
	    if ( $i == 0 )
	      {
		$prev=$this->arow[$i]['j_poste'];
		echo '<tr><td>'.HtmlInput::history_account ($this->arow[$i]['j_poste'],$this->arow[$i]['j_poste'].' '.$this->arow[$i]['name'],' display:inline').'</td></tr>';
	      }
	    $style= ( $i % 2 == 0)?' class="odd" ':' class="even" ';
	    if ( $i != 0 && $prev != $this->arow[$i]['j_poste'])
	      {
		echo  td('Total');
		echo td(nbm($tot_card),' class="num"');
		echo '</tr>';
		echo '<tr  style="padding-top:5px"><td>'.HtmlInput::history_account($this->arow[$i]['j_poste'],$this->arow[$i]['j_poste'].' '.$this->arow[$i]['name'],' display:inline ').'</td></tr>';
		$tot_card=0;
		$prev = $this->arow[$i]['j_poste'];
	      }

	    echo '<tr '.$style.'>';
	    $amount=$this->arow[$i]['sum_amount'];
	    if ($amount==null)$amount=0;

	    $tot_card=bcadd($tot_card,$amount);
	    $tot_glob=bcadd($tot_glob,$amount);


	    echo td($this->arow[$i]['po_name']."   ".
		    $this->arow[$i]['po_description'],'style="padding-left:10"');
	    echo td(nbm($amount),' class="num" ');
	    echo '</tr>';

	  }
	echo '<tr>';
	echo  td('Total');
	echo td(nbm($tot_card),' class="num"');
	echo '</tr>';

	echo '</table>';
	echo td(nbm($tot_card),' class="num"');
      }
    //---------------------------------------------------------------------------
    // Acc after card
    //---------------------------------------------------------------------------
    if ( $this->card_poste=='3')
      {
	$this->load_anc_card();
	/*
	 * Show all the result
	 */
	$tot_card=0;$prev='';
	echo '<table class="result" style="margin-left:20px;margin-top:5px">';
	$tot_glob=0;
	for ($i=0;$i<count($this->arow);$i++)
	  {
	    if ( $i == 0 )
	      {
		$prev=$this->arow[$i]['po_id'];
		echo '<tr><td>'.$this->arow[$i]['po_name']."  ".$this->arow[$i]['po_description'].'</td></tr>';

	      }
	    $style= ( $i % 2 == 0)?' class="odd" ':' class="even" ';
	    if ( $i != 0 && $prev != $this->arow[$i]['po_id'])
	      {
		echo  td('Total');
		echo td(nbm($tot_card),' class="num"');
		echo '</tr>';
		echo '<tr><td>'.$this->arow[$i]['po_name']."  ".$this->arow[$i]['po_description'].'</td></tr>';

		$tot_card=0;
		$prev = $this->arow[$i]['po_id'];
	      }

	    echo '<tr '.$style.'>';
	    $amount=$this->arow[$i]['sum_amount'];
	    if ($amount==null)$amount=0;

	    $tot_card=bcadd($tot_card,$amount);
	    $tot_glob=bcadd($tot_glob,$amount);
	    echo '<td style="padding-left:10">'.HtmlInput::history_card ($this->arow[$i]['f_id'],$this->arow[$i]['j_qcode'].' '.$this->arow[$i]['name'],' display:inline').'</td>';

	    echo td(nbm($amount),' class="num" ');
	    echo '</tr>';

	  }
	echo '<tr>';
	echo  td('Total');
	echo td(nbm($tot_card),' class="num"');
	echo '</tr>';

	echo '</table>';
	echo td(nbm($tot_card),' class="num"');
      }
    //---------------------------------------------------------------------------
    // Analytic - Accountancy
    //---------------------------------------------------------------------------


    if ( $this->card_poste=='4')
      {
	$this->load_anc_account();

	/*
	 * Show all the result
	 */
	$tot_card=0;$prev='';
	echo '<table class="result" style="margin-left:20px;margin-top:5px">';
	$tot_glob=0;
	for ($i=0;$i<count($this->arow);$i++)
	  {
	    if ( $i == 0 )
	      {
		$prev=$this->arow[$i]['po_id'];
		echo '<tr><td>'.$this->arow[$i]['po_name']."  ".$this->arow[$i]['po_description'].'</td></tr>';
	      }
	    $style= ( $i % 2 == 0)?' class="odd" ':' class="even" ';
	    if ( $i != 0 && $prev != $this->arow[$i]['po_id'])
	      {
		echo  td('Total');
		echo td(nbm($tot_card),' class="num"');
		echo '</tr>';

		$tot_card=0;
		$prev = $this->arow[$i]['po_id'];
		echo '<tr><td>'.$this->arow[$i]['po_name']."  ".$this->arow[$i]['po_description'].'</td></tr>';

	      }

	    echo '<tr '.$style.'>';
	    $amount=$this->arow[$i]['sum_amount'];
	    if ($amount==null)$amount=0;

	    $tot_card=bcadd($tot_card,$amount);
	    $tot_glob=bcadd($tot_glob,$amount);
	    echo '<td style="padding-left:10">'.HtmlInput::history_account ($this->arow[$i]['j_poste'],$this->arow[$i]['j_poste'].' '.$this->arow[$i]['name'],' display:inline').'</td>';
	    echo td(nbm($amount),' class="num" ');
	    echo '</tr>';

	  }
	echo '<tr>';
	echo  td('Total');
	echo td(nbm($tot_card),' class="num"');
	echo '</tr>';

	echo '</table>';
	echo '<h2> Résultat global '.nbm($tot_glob).'</h2>';
      }

  }
  function export_csv()
  {
   bcscale(2);
   if ( $this->check () != 0 ) {throw new Exception (_("date invalide"));}
      //---------------------------------------------------------------------------
    // Card  - Acc
    //---------------------------------------------------------------------------

    if ( $this->card_poste=='1')
      {
	$this->load_card();

	/*
	 * Show all the result
	 */
	$prev='';


	for ($i=0;$i<count($this->arow);$i++)
	  {
	    printf('"%s";" %s"', $this->arow[$i]['j_qcode'],$this->arow[$i]['name']);

	    $amount=$this->arow[$i]['sum_amount'];
	    if ($amount==null)$amount=0;

	    printf(';"%s";" %s";',
		   $this->arow[$i]['po_name'],
		   $this->arow[$i]['po_description']);
	    printf("%s",nb($amount));
	    printf("\r\n");
	  }
      }
    //---------------------------------------------------------------------------
    // Accountancy - Analytic
    //---------------------------------------------------------------------------

    if ( $this->card_poste=='2')
      {
	$this->load_poste();
	/*
	 * Show all the result
	 */
	for ($i=0;$i<count($this->arow);$i++)
	  {
	    printf('"%s";" %s"', $this->arow[$i]['j_poste'],$this->arow[$i]['name']);

	    $amount=$this->arow[$i]['sum_amount'];
	    if ($amount==null)$amount=0;

	    printf(';"%s";" %s";',
		   $this->arow[$i]['po_name'],
		   $this->arow[$i]['po_description']);
	    printf("%s",nb($amount));
	    printf("\r\n");


	  }

      }
    //---------------------------------------------------------------------------
    // Acc after card
    //---------------------------------------------------------------------------
    if ( $this->card_poste=='3')
      {
	$this->load_anc_card();
	/*
	 * Show all the result
	 */
	for ($i=0;$i<count($this->arow);$i++)
	  {
	    printf('"%s";" %s";', $this->arow[$i]['po_name'],$this->arow[$i]['po_description']);

	    $amount=$this->arow[$i]['sum_amount'];
	    if ($amount==null)$amount=0;

	    printf('"%s";"%s";',
		   $this->arow[$i]['j_qcode'],
		   $this->arow[$i]['name']);
	    printf("%s",nb($amount));
	    printf("\r\n");


	  }
      }
    //---------------------------------------------------------------------------
    // Analytic - Accountancy
    //---------------------------------------------------------------------------


    if ( $this->card_poste=='4')
      {
	$this->load_anc_account();

	/*
	 * Show all the result
	 */
	for ($i=0;$i<count($this->arow);$i++)
	  {
	    printf('"%s";"%s";', $this->arow[$i]['po_name'],$this->arow[$i]['po_description']);

	    $amount=$this->arow[$i]['sum_amount'];
	    if ($amount==null)$amount=0;

	    printf('"%s";"%s";',
		   $this->arow[$i]['j_poste'],
		   $this->arow[$i]['name']);
	    printf("%s",nb($amount));
	    printf("\r\n");


	  }
      }




  }

}