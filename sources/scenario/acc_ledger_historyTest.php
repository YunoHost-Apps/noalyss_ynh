<?php

/*
 *   This file is part of NOALYSS.
 *
 *   PhpCompta is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   PhpCompta is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with PhpCompta; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
// Copyright (2018) Author Dany De Bontridder <dany@alchimerys.be>



/**
 * @file
 * @brief Test of ledger_history 
 */
require_once NOALYSS_INCLUDE."/class/acc_ledger_history.class.php";

$ledger=[1,2,3,4];
echo Dossier::hidden();
global $cn, $g_user, $g_succeed, $g_failed;
$cn=Dossier::connect();

$ledger_history=Acc_Ledger_History::factory($cn, $ledger, 216, 217, "E");
echo h1("Detailled Accounting");
echo h2(_("export detail html all ledgers result = Detailled Accounting from Acc_Ledger_History_Generic"));
$ledger_history->export_detail_html();

echo h2(_("Set mode to D etailled all_ledgers result = Detailled Accounting from Acc_Ledger_History_Generic" ));
$ledger_history->set_m_mode("D");
$ledger_history->export_html();

echo h1(_("Only VEN from Acc_Ledger_History_Sale"));
$ledger_history=Acc_Ledger_History::factory($cn, [2], 216, 217, "L");
$ledger_history->export_detail_html();

echo h2(_("Only VEN one line"));
$ledger_history->set_a_ledger([2]);
$ledger_history->set_m_mode("L");
$ledger_history->export_html();

echo h2(_("Only VEN Detailled"));
$ledger_history->set_a_ledger([2]);
$ledger_history->set_m_mode("D");
$ledger_history->export_html();

echo h2(_("Only VEN Extended"));
$ledger_history->set_a_ledger([2]);
$ledger_history->set_m_mode("E");
$ledger_history->export_html();

echo h2("VEN + ACH");
$ledger_history=Acc_Ledger_History::factory($cn, [3,2], 216, 217, "L");

$ledger_history->export_oneline_html();

echo h1("ACH from Acc_Ledger_History_Purchase");
echo h2("Detailled accouting");
$ledger_history=new Acc_Ledger_History_Purchase($cn,[3],216,217,"A");
$ledger_history->export_html();
echo h2("Ach one line");
$ledger_history->set_m_mode("L");
$ledger_history->export_html();
echo h2("Ach Detail");
$ledger_history->set_m_mode("D");
$ledger_history->export_html();
echo h2("Ach Extended");
$ledger_history->set_m_mode("E");
$ledger_history->export_html();

echo h1("FIN from Acc_Ledger_History_Financial");
echo h2("Detailled accouting");
$ledger_history=new Acc_Ledger_History_Financial($cn,[11,16],216,217,"A");
$ledger_history->export_html();
echo h2("FIN one line");
$ledger_history->set_m_mode("L");
$ledger_history->export_html();
echo h2(">FIN Detail");
$ledger_history->set_m_mode("D");
$ledger_history->export_html();
echo h2("FIN Extended");
$ledger_history->set_m_mode("E");
$ledger_history->export_html();
