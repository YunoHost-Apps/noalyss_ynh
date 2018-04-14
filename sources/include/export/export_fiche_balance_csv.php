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
 * \brief Called by impress->category, export in CVS the history of a category
 * of card
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');


// Security we check if user does exist and his privilege
require_once NOALYSS_INCLUDE.'/class/user.class.php';
require_once NOALYSS_INCLUDE.'/lib/database.class.php';
require_once NOALYSS_INCLUDE.'/class/dossier.class.php';
require_once NOALYSS_INCLUDE.'/lib/ac_common.php';
require_once NOALYSS_INCLUDE.'/lib/noalyss_csv.class.php';

$allcard=(isset($_GET['allcard']))?1:0;
$export=new Noalyss_Csv(_('fiche-balance'));

/* balance */
if ( $_GET['histo'] == 4 || $_GET['histo'] == 5)
{
    $fd=new Fiche_Def($cn,$_REQUEST['cat']);
    if ( $allcard==1 && $fd->hasAttribute(ATTR_DEF_ACCOUNT) == false )
    {
      exit;
    }
	// all card
	if ($allcard==1)
	{
		$afiche=$cn->get_array("select fd_id from vw_fiche_def where ad_id=".ATTR_DEF_ACCOUNT." order by fd_label asc");
	}
	else
	{
		$afiche[0]=array('fd_id'=>$_REQUEST['cat']);
	}
        
	$title=array("Quick code",_("Nom"),_("poste comptable"),_("debit"),_("credit"),_("solde"),"D/C");
        $export->send_header();
        $export->write_header($title);
        
        for ($e = 0; $e < count($afiche); $e++)
	{
		$aCard = $cn->get_array("select f_id,ad_value from fiche join fiche_Detail using (f_id)  where ad_id=1 and fd_id=$1 order by 2 ", array($afiche[$e]['fd_id']));

		if (empty($aCard))
		{
			continue;
		}

		for ($i = 0; $i < count($aCard); $i++)
		{
			if (isDate($_REQUEST['start']) == null || isDate($_REQUEST['end']) == null)
				exit;
			$filter = " (j_date >= to_date('" . $_REQUEST['start'] . "','DD.MM.YYYY') " .
					" and  j_date <= to_date('" . $_REQUEST['end'] . "','DD.MM.YYYY')) ";
			$oCard = new Fiche($cn, $aCard[$i]['f_id']);
			$solde = $oCard->get_solde_detail($filter);
			if ($solde['debit'] == 0 && $solde['credit'] == 0)
				continue;
			/* only not purged card */
			if ($_GET['histo'] == 5 && $solde['solde'] == 0)
				continue;
			$side = '';
			if (bcsub($solde['credit'], $solde['debit']) < 0)
				$side = 'Deb.';
			if (bcsub($solde['credit'], $solde['debit']) > 0)
				$side = 'Cred.';

                        $export->add($oCard->strAttribut(ATTR_DEF_QUICKCODE));
                        $export->add($oCard->strAttribut(ATTR_DEF_NAME));
                        $export->add($oCard->strAttribut(ATTR_DEF_ACCOUNT));
                        $export->add($solde['debit'],"number"); 
                        $export->add($solde['credit'],"number"); 
                        $export->add(abs($solde['solde']),"number");
                        $export->add($side);
                        $export->write();
		}
	}
}
else
{
	// all card
	if ($allcard == 1)
	{
		$afiche = $cn->get_array("select fd_id from vw_fiche_def where ad_id=" . ATTR_DEF_ACCOUNT . " order by fd_label asc");
	}
	else
	{
		$afiche[0] = array('fd_id' => $_REQUEST['cat']);
	}
        $export->send_header();
	$fic=new Fiche($cn);
	for ($e = 0; $e < count($afiche); $e++)
	{
		$array = Fiche::get_fiche_def($cn, $afiche[$e]['fd_id'], 'name_asc');

		foreach ($array as $card)
		{
			$row = new Fiche($cn, $card['f_id']);
			$letter = new Lettering_Card($cn);
			$letter->set_parameter('quick_code', $row->strAttribut(ATTR_DEF_QUICKCODE));
			$letter->set_parameter('start', $_GET['start']);
			$letter->set_parameter('end', $_GET['end']);
			// all
			if ($_GET['histo'] == 0)
			{
				$letter->get_all();
			}

			// lettered
			if ($_GET['histo'] == 1)
			{
				$letter->get_letter();
			}
			// unlettered
			if ($_GET['histo'] == 2)
			{
				$letter->get_unletter();
			}
			if ($_GET['histo'] == 6)
			{
				$letter->get_letter_diff();
			}
			/* skip if nothing to display */
			if (count($letter->content) == 0)
				continue;
			$export->add($row->strAttribut(ATTR_DEF_QUICKCODE));
                        $export->add( $row->strAttribut(ATTR_DEF_NAME));
                        $export->add($row->strAttribut(ATTR_DEF_ACCOUNT));
                        $export->write();

			
			$export->add(_('Date'));
			$export->add(_('ref'));
			$export->add(_('Interne'));
			$export->add(_('Comm'));
			$export->add(_('Débit'));
			$export->add(_('Crébit'));
			$export->add(_('Prog.'));
			$export->add(_('D/C'));
			$export->add(_('Let.'));
			$export->add(_("Diff Let."));
			$export->write();
                        
			$amount_deb = 0;
			$amount_cred = 0;
			$prog = 0;
			bcscale(2);
			for ($i = 0; $i < count($letter->content); $i++)
			{
				$row = $letter->content[$i];
				$export->add($row['j_date_fmt']);
				$export->add($row['jr_pj_number']);
				$export->add($row['jr_internal']);
				$export->add($row['jr_comment']);
				if ($row['j_debit'] == 't')
				{
					$export->add($row['j_montant'],"number");
					$amount_deb=bcadd($amount_deb,$row['j_montant']);
					$prog = bcadd($prog, $row['j_montant']);
				}
				else
				{
					$export->add($row['j_montant'],"number");
					$amount_cred=bcadd($amount_cred,$row['j_montant']);
					$prog = bcsub($prog, $row['j_montant']);
				}
				$export->add(abs($prog),"number");
                                $export->add($fic->get_amount_side($prog),"number");
				if ($row['letter'] != -1)
				{
					$export->add(strtoupper(base_convert($row['letter'],10,36)));
					$export->add($row['letter_diff'],"number");
				}
				else
					$export->add("");
				$export->write();
			}
			if ($prog < 0 )
				$msg="Solde Debit";
			elseif ($prog>0)
				$msg="Solde Credit";
			else
				$msg="soldé";
                        $export->add("");
                        $export->add("");
                        $export->add("");
                        
                        $export->add($msg);
                        $export->add($amount_deb,"number");
                        $export->add($amount_cred,"number");
                        $export->add(abs($prog),"number");
                        $export->add($fic->get_amount_side($prog),"number");
			$export->write();
		}
	}
}
exit;


