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
/* $Revision$ */

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/*!\file
 * \brief javascript script for the ledger in accountancy,
 * compute the sum, add a row at the table..
 *
 */
var layer = 1;
/**
 * @brief update the list of available predefined operation when we change the ledger.
 */
function update_predef(p_type, p_direct, p_ac)
{
    var jrn = g("p_jrn").value;
    var dossier = g("gDossier").value;
    var querystring = 'gDossier=' + dossier + '&l=' + jrn + '&t=' + p_type + '&d=' + p_direct + "&op=up_predef&ac=" + p_ac;
    g("p_jrn_predef").value = jrn;
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: querystring,
                onFailure: error_get_predef,
                onSuccess: function (req) {
                    try {
                        $('info_div').innerHTML = "ok";
                        var answer = req.responseXML;
                        var a = answer.getElementsByTagName('code');
                        var html = answer.getElementsByTagName('value');
                        if (a.length == 0)
                        {
                            var rec = req.responseText;
                            alert_box('erreur :' + rec);
                        }
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        // document.getElementsByName(name_ctl)[0].value = code_html;
                        $('modele_op_div').innerHTML = code_html;
                    } catch (e) {
                        $('info_div').innerHTML = e.message;
                    }
                }
            }
    );
}

/**
 * @brief update the list of payment method when we change the ledger.
 */
function update_pay_method()
{
    waiting_box();
    var jrn = g("p_jrn").value;
    var dossier = g("gDossier").value;
    var querystring = 'gDossier=' + dossier + '&l=' + jrn + "&op=up_pay_method";
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: querystring,
                onFailure: error_get_predef,
                onSuccess: function (req) {
                    remove_waiting_box();
                    var answer = req.responseText;
                    $('payment').innerHTML = answer;
                }
            }
    );
}

/**
 *@brief update ctl id =jrn_name with the value of p_jrn
 */
function update_name()
{
    var jrn_id = $('p_jrn').value;
    var dossier = g("gDossier").value;
    var querystring = 'gDossier=' + dossier + '&l=' + jrn_id + "&op=ledger_description";
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: querystring,
                onFailure: error_get_pj,
                onSuccess: function (req) {
                    $('jrn_name_div').innerHTML = req.responseText;
                }
            }
    );

}
/**
 * @brief update the field predef
 */
function error_get_predef(request, json)
{
    alert_box("Erreur mise à jour champs non possible");

}
/**
 * @brief update the list of available predefined operation when we change the ledger.
 */
function update_pj()
{
    var jrn = g("p_jrn").value;
    var dossier = g("gDossier").value;
    var querystring = 'gDossier=' + dossier + '&l=' + jrn + "&op=upd_receipt";
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: querystring,
                onFailure: error_get_pj,
                onSuccess: success_get_pj
            }
    );
}
/**
 *@brief ask the name, quick_code of the bank for the ledger
 */
function update_bank()
{
    var jrn = g('p_jrn').value;
    var dossier = g('gDossier').value;
    var qs = 'gDossier=' + dossier + '&op=bkname&p_jrn=' + jrn;
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: qs,
                onFailure: error_get_pj,
                onSuccess: success_update_bank
            }
    );

}
/**
 * @brief Update the number of rows when changing of ledger
 */
function update_row(ctl)
{
    try
    {
        var jrn = g('p_jrn').value;
        var dossier = g('gDossier').value;
        var qs = encodeURI('gDossier=' + dossier + '&op=minrow&j=' + jrn + '&ctl=' + ctl);
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get',
                    parameters: qs,
                    onFailure: null,
                    onSuccess: function (request, json)
                    {
                        try {
                            var answer = request.responseText.evalJSON(true);
                            var row = parseFloat(answer.row);
                            var current_row = parseFloat($('nb_item').value);
                            if (current_row > row) {
                                // Too many row
                                var delta = $('nb_item').value - row;
                                var idx = $('nb_item').value;
                                for (var i = 0; i < delta; i++) {
                                    $(ctl).deleteRow(-1);
                                    idx--;
                                }
                                $('nb_item').value = row;
                            }
                            if (current_row < row) {
                                // We need to add rows
                                var delta = row - current_row;
                                for (var i = 0; i < delta; i++) {
                                    if (ctl == 'fin_item') {
                                        ledger_fin_add_row();
                                    }
                                    if (ctl == 'sold_item') {
                                        ledger_add_row();
                                    }
                                    if (ctl == 'quick_item') {
                                        quick_writing_add_row();
                                    }
                                }
                            }
                        } catch (e) {
                            alert_box(e.message);
                        }
                    }
                }
        );
    } catch (e) {
        alert_box(e.message);
    }
}
/**
 * @brief Put into the span, the name of the bank, the bank account
 * and the quick_code
 */
function success_update_bank(req)
{
    try
    {
        var answer = req.responseXML;
        var a = answer.getElementsByTagName('code');
        var html = answer.getElementsByTagName('value');
        if (a.length == 0)
        {
            var rec = req.responseText;
            alert_box('erreur :' + rec);
        }
        var name_ctl = a[0].firstChild.nodeValue;
        var code_html = getNodeText(html[0]);
        code_html = unescape_xml(code_html);
        $(name_ctl).innerHTML = code_html;
    }
    catch (e)
    {
        alert_box("success_update_bank" + e.message);
    }
}
/**
 * @brief call ajax, ask what is the last date for the current ledger
 */
function get_last_date()
{
    var jrn = g('p_jrn').value;
    var dossier = g('gDossier').value;
    var qs = 'gDossier=' + dossier + '&op=lastdate&p_jrn=' + jrn;
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: qs,
                onFailure: error_get_pj,
                onSuccess: success_get_last_date
            }
    );
}
/**
 * @brief callback ajax, set the ctl with the last date from the ledger
 */
function success_get_last_date(req)
{
    try
    {
        var answer = req.responseXML;
        var a = answer.getElementsByTagName('code');
        var html = answer.getElementsByTagName('value');
        if (a.length == 0)
        {
            var rec = req.responseText;
            alert_box('erreur :' + rec);
        }
        var name_ctl = a[0].firstChild.nodeValue;
        var code_html = getNodeText(html[0]);
        code_html = unescape_xml(code_html);
        document.getElementsByName(name_ctl)[0].value = code_html;
    }
    catch (e)
    {
        alert_box(e.message);
    }
}
/**
 * @brief update the field predef
 */
function success_get_pj(request, json)
{

    var answer = request.responseText.evalJSON(true);
    var obj = g("e_pj");
    obj.value = '';
    if (answer.length == 0)
        return;
    obj.value = answer.pj;
    g("e_pj_suggest").value = answer.pj;
}
/**
 * @brief update the field predef
 */
function error_get_pj(request, json)
{
    alert_box("Ajax a echoue");
}

/**
 * @brief add a line in the form for the ledger fin
 */
function ledger_fin_add_row()
{
    var style = 'class="input_text"';
    var mytable = g("fin_item").tBodies[0];
    var line = mytable.rows.length;
    var row = mytable.insertRow(line);
    var nb = g("nb_item");
    var rowToCopy = mytable.rows[1];
    var nNumberCell = rowToCopy.cells.length;
    for (var e = 0; e < nNumberCell; e++)
    {
        var newCell = row.insertCell(e);
        if (e == 0) {
            newCell.id = 'tdchdate' + nb.value;
        }
        var tt = rowToCopy.cells[e].innerHTML;
        var new_tt = tt.replace(/e_other0/g, "e_other" + nb.value);
        new_tt = new_tt.replace(/e_other0_comment/g, "e_other" + nb.value + '_comment');
        new_tt = new_tt.replace(/e_other_name0/g, "e_other_name" + nb.value);
        new_tt = new_tt.replace(/e_other0_amount/g, "e_other" + nb.value + '_amount');
        new_tt = new_tt.replace(/e_concerned0/g, "e_concerned" + nb.value);
        new_tt = new_tt.replace(/e_other0_label/g, "e_other" + nb.value + '_label');
        new_tt = new_tt.replace(/dateop0/g, "dateop" + nb.value);
        newCell.innerHTML = new_tt;
        newCell.className=rowToCopy.cells[e].className;
        new_tt.evalScripts();
    }
    g("e_other" + nb.value).value = "";
    g("e_other_name" + nb.value).value = "";
    g("e_other" + nb.value + '_amount').value = "0";
    g("e_other" + nb.value + '_comment').value = "";
    g("e_concerned" + nb.value).value = "";

    var ch = $('chdate').options[$('chdate').selectedIndex].value;
    if (ch == 1) {
        $('tdchdate' + nb.value).hide();
    }
    nb.value++;
}

/**
 * @brief add a line in the form for the purchase ledger
 * @param p_dossier folder id
 * @param p_table_name
 */
function ledger_add_row()
{
    try {
        style = 'class="input_text"';
        var mytable = g("sold_item").tBodies[0];
        var ofirstRow = mytable.rows[1];
        var line = mytable.rows.length;
        var nCell = mytable.rows[1].cells.length;
        var row = mytable.insertRow(line);
        var nb = g("nb_item");
        for (var e = 0; e < nCell; e++)
        {
            var newCell = row.insertCell(e);
            var tt = ofirstRow.cells[e].innerHTML;
            var new_tt = tt.replace(/march0/g, "march" + nb.value);
            new_tt = new_tt.replace(/quant0/g, "quant" + nb.value);
            new_tt = new_tt.replace(/sold\(0\)/g, "sold(" + nb.value + ")");
            new_tt = new_tt.replace(/compute_ledger\(0\)/g, "compute_ledger(" + nb.value + ")");
            new_tt = new_tt.replace(/clean_tva\(0\)/g, "clean_tva(" + nb.value + ")");
            newCell.innerHTML = new_tt;
            newCell.className=ofirstRow.cells[e].className;
            new_tt.evalScripts();
        }

        $("e_march" + nb.value + "_label").innerHTML = '';
        $("e_march" + nb.value + "_label").value = '';
        $("e_march" + nb.value + "_price").value = '0';
        $("e_march" + nb.value).value = "";
        $("e_quant" + nb.value).value = "1";
        if ($("e_march" + nb.value + "_tva_amount"))
            $("e_march" + nb.value + "_tva_amount").value = 0;

        nb.value++;

        new_tt.evalScripts();
    } catch (e) {
        alert_box(e.message);
    }
}
/**
 * @brief compute the sum of a purchase, update the span tvac, htva and tva
 * all the needed data are taken from the document (hidden field :  gdossier)
 * @param the number of the changed ctrl
 */
function compute_ledger(p_ctl_nb)
{
    var dossier = g("gDossier").value;
    var a = -1;
    if (document.getElementById("e_march" + p_ctl_nb + '_tva_amount'))
    {
        a = trim(g("e_march" + p_ctl_nb + '_tva_amount').value);
        g("e_march" + p_ctl_nb + '_tva_amount').value = a;
    }
    if (!document.getElementById("e_march" + p_ctl_nb)) {
        return;
    }
    g("e_march" + p_ctl_nb).value = trim(g("e_march" + p_ctl_nb).value);
    var qcode = g("e_march" + p_ctl_nb).value;

    if (qcode.length == 0)
    {
        clean_ledger(p_ctl_nb);
        refresh_ledger();
        return;
    }
    /*
     * if tva_id is empty send a value of -1
     */
    var tva_id = -1;
    if (g('e_march' + p_ctl_nb + '_tva_id'))
    {
        tva_id = g('e_march' + p_ctl_nb + '_tva_id').value;
        if (trim(tva_id) == '')
        {
            tva_id = -1;
        }
    }

    g('e_march' + p_ctl_nb + '_price').value = trim(g('e_march' + p_ctl_nb + '_price').value);
    var price = g('e_march' + p_ctl_nb + '_price').value;

    g('e_quant' + p_ctl_nb).value = trim(g('e_quant' + p_ctl_nb).value);
    var quantity = g('e_quant' + p_ctl_nb).value;
    var querystring = 'gDossier=' + dossier + '&c=' + qcode + '&t=' + tva_id + '&p=' + price + '&q=' + quantity + '&n=' + p_ctl_nb;
    var action = new Ajax.Request(
            "compute.php",
            {
                method: 'get',
                parameters: querystring,
                onFailure: error_compute_ledger,
                onSuccess: success_compute_ledger
            }
    );
}
/**
 *@brief refresh the purchase screen, recompute vat, total...
 */
function refresh_ledger()
{
    var tva = 0;
    var htva = 0;
    var tvac = 0;

    for (var i = 0; i < g("nb_item").value; i++)
    {
        if (g('tva_march' + i))
            tva += g('tva_march' + i).value * 1;
        if (g('htva_march' + i))
            htva += g('htva_march' + i).value * 1;
        if (g('tvac_march' + i))
            tvac += g('tvac_march' + i).value * 1;
    }

    if (g('tva'))
        g('tva').innerHTML = Math.round(tva * 100) / 100;
    if (g('htva'))
        g('htva').innerHTML = Math.round(htva * 100) / 100;
    if (g('tvac'))
        g('tvac').innerHTML = Math.round(tvac * 100) / 100;
}
/**
 *@brief update the field htva, tva_id and tvac, callback function for  compute_sold
 * it the field TVA in the answer contains NA it means that VAT is appliable and then do not
 * update the VAT field except htva_martc
 */
function success_compute_ledger(request, json)
{
    var answer = request.responseText.evalJSON(true);
    var ctl = answer.ctl;
    var rtva = answer.tva;
    var rhtva = answer.htva;
    var rtvac = answer.tvac;

    if (rtva == 'NA')
    {
        var rhtva = answer.htva * 1;
        g('htva_march' + ctl).value = rhtva;
        g('tvac_march' + ctl).value = rtvac;
        g('sum').show();
        refresh_ledger();

        return;
    }
    rtva = answer.tva * 1;



    g('sum').show();
    if (g('e_march' + ctl + '_tva_amount').value == "" || g('e_march' + ctl + '_tva_amount').value == 0)
    {
        g('tva_march' + ctl).value = rtva;
        g('e_march' + ctl + '_tva_amount').value = rtva;
    }
    else
    {
        g('tva_march' + ctl).value = g('e_march' + ctl + '_tva_amount').value;
    }
    g('htva_march' + ctl).value = Math.round(parseFloat(rhtva) * 100) / 100;
    var tmp1 = Math.round(parseFloat(g('htva_march' + ctl).value) * 100) / 100;
    var tmp2 = Math.round(parseFloat(g('tva_march' + ctl).value) * 100) / 100;
    g('tvac_march' + ctl).value = Math.round((tmp1 + tmp2) * 100) / 100;

    refresh_ledger();
}

/**
 * @brief callback error function for  compute_sold
 */
function error_compute_ledger(request, json)
{
    alert_box('Ajax does not work');
}
function compute_all_ledger()
{
    var loop = 0;
    for (loop = 0; loop < g("nb_item").value; loop++)
    {
        compute_ledger(loop);
    }
    var tva = 0;
    var htva = 0;
    var tvac = 0;

    for (var i = 0; i < g("nb_item").value; i++)
    {
        if (g('tva_march'))
            tva += g('tva_march' + i).value * 1;
        if (g('htva_march' + i))
            htva += g('htva_march' + i).value * 1;
        if (g('tvac_march' + i))
            tvac += g('tvac_march' + i).value * 1;
    }

    if (g('tva'))
        g('tva').innerHTML = Math.round(tva * 100) / 100;
    if (g('htva'))
        g('htva').innerHTML = Math.round(htva * 100) / 100;
    if (g('tvac'))
        g('tvac').innerHTML = Math.round(tvac * 100) / 100;


}

function clean_tva(p_ctl)
{
    if (g('e_march' + p_ctl + '_tva_amount'))
        g('e_march' + p_ctl + '_tva_amount').value = 0;
}

function clean_ledger(p_ctl_nb)
{
    if (g("e_march" + p_ctl_nb))
    {
        g("e_march" + p_ctl_nb).value = trim(g("e_march" + p_ctl_nb).value);
    }
    if (g('e_march' + p_ctl_nb + '_price'))
    {
        g('e_march' + p_ctl_nb + '_price').value = '';
    }
    if (g('e_quant' + p_ctl_nb))
    {
        g('e_quant' + p_ctl_nb).value = '1';
    }
    if (g('tva_march' + p_ctl_nb + '_show'))
    {
        g('tva_march' + p_ctl_nb + '_show').value = '0';
    }
    if (g('tva_march' + p_ctl_nb))
    {
        g('tva_march' + p_ctl_nb).value = 0;
    }
    if (g('htva_march' + p_ctl_nb))
    {
        g('htva_march' + p_ctl_nb).value = 0;
    }
    if (g('tvac_march' + p_ctl_nb))
    {
        g('tvac_march' + p_ctl_nb).value = 0;
    }

}
/**
 * @brief add a line in the form for the quick_writing
 */
function quick_writing_add_row()
{
    style = 'class="input_text"';
    var mytable = g("quick_item").tBodies[0];
    var nNumberRow = mytable.rows.length;
    var oRow = mytable.insertRow(nNumberRow);
    var rowToCopy = mytable.rows[1];
    var nNumberCell = rowToCopy.cells.length;
    var nb = g("nb_item");

    var oNewRow = mytable.insertRow(nNumberRow);
    for (var e = 0; e < nNumberCell; e++)
    {
        var newCell = oRow.insertCell(e);
        var tt = rowToCopy.cells[e].innerHTML;
        new_tt = tt.replace(/qc_0/g, "qc_" + nb.value);
        new_tt = new_tt.replace(/amount0/g, "amount" + nb.value);
        new_tt = new_tt.replace(/poste0/g, "poste" + nb.value);
        new_tt = new_tt.replace(/ck0/g, "ck" + nb.value);
        new_tt = new_tt.replace(/ld0/g, "ld" + nb.value);
        newCell.innerHTML = new_tt;
        newCell.className=rowToCopy.cells[e].className;
        new_tt.evalScripts();
    }
    $("qc_" + nb.value).value = "";
    $("amount" + nb.value).value = "";
    $("poste" + nb.value).value = "";
    $("ld" + nb.value).value = "";



    nb.value++;

}
function RefreshMe()
{
    window.location.reload();
}


function go_next_concerned()
{
    var form = document.forms[1];

    for (var e = 0; e < form.elements.length; e++)
    {
        var elmt = form.elements[e];
        if (elmt.type == "checkbox")
        {
            if (elmt.checked == true)
            {
                return confirm("Si vous changez de page vous perdez les reconciliations, continuez ?");
            }
        }
    }
    return true;
}
/**
 * @brief View the history of an account
 * @param {type} p_value
 * @param {type} dossier
 * @returns {undefined}
 */
function view_history_account(p_value, dossier,p_exercice)
{
    layer++;
    var idbox = 'det' + layer;
    var popup = {'id': idbox, 'cssclass': 'inner_box', 'html': loading(), 'drag': false};

    var querystring={'gDossier':dossier,'act':'de','pcm_val':p_value,'div':idbox,'l':layer,'op':'history','exercice':p_exercice};
    waiting_box();

    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: querystring,
                onFailure: error_box,
                onSuccess: function (req, xml)
                {
                    remove_waiting_box();
                    add_div(popup);
                    success_box(req, xml);
                    $(idbox).style.top = calcy(140 + (layer * 3)) + "px";
                }
            }
    );

}
/**
 * @brief View the history of an account
 * @param {type} p_value
 * @param {type} dossier
 * @returns {undefined}
 */
function view_history_anc_account(p_value, dossier,p_exercice)
{
    layer++;
    var idbox = 'det' + layer;
    var popup = {'id': idbox, 'cssclass': 'inner_box', 'html': loading(), 'drag': false};

    var querystring={'gDossier':dossier,'op':'history_anc_account','po_id':p_value,'div':idbox,'l':layer,'act':'history','exercice':p_exercice};
    waiting_box();

    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: querystring,
                onFailure: error_box,
                onSuccess: function (req, xml)
                {
                    remove_waiting_box();
                    add_div(popup);
                    $(idbox).innerHTML=req.responseText;
                    $(idbox).style.top = calcy(140 + (layer * 3)) + "px";
                }
            }
    );

}
/**
 * @brief Change the view of account history
 * @param {type} obj
 * @returns {Boolean}
 */
function update_history_account(obj)
{
    try {
        var querystring = {
            "l" :obj.div ,
            "div" :obj.div ,
            "gDossier" : obj.gDossier,
            "pcm_val" : obj.pcm_val ,
            "ex" : obj.select.options[obj.select.selectedIndex].text,
            "op":"history",
            "exercice":obj.exercice
        };
        waiting_box();
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_box,
                    onSuccess: function (req, xml)
                    {
                        remove_waiting_box();
                        success_box(req, xml);
                        g(obj.div).style.top = calcy(140 + (layer * 3)) + "px";
                    }
                });
    } catch (e)
    {
        alert_box("update_history_account error " + e.message);
    }

    return false;
}
/*!\brief Change the view of card history
 * \param p_value f_id of the card
 */
function view_history_card(p_value, dossier,p_exercice)
{
    layer++;
    var idbox = 'det' + layer;
    var popup = {'id':idbox,
                'cssclass': 'inner_box', 
                'html':loading(), 
                'drag':false};
    var querystring = { 'gDossier' : dossier ,
         'act':'de',
         'f_id' : p_value , 
         'div' : idbox ,
          "l" : layer,
          "op":"history",
      "exercice":p_exercice};
    waiting_box();
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: querystring,
                onFailure: error_box,
                onSuccess: function (req, xml)
                {
                    remove_waiting_box();
                    add_div(popup);
                    success_box(req, xml);
                    g(idbox).style.top = calcy(140 + (layer * 3)) + "px";
                }
            }
    );
}
/**
 * @brief update history view after changing the exercice
 * @param {type} obj
 * @returns {Boolean}
 */
function update_history_card(obj)
{
    try {
        var querystring = {
            "l" : obj.div , 
            "div" : obj.div ,
            "gDossier" : obj.gDossier,
            "f_id" : obj.f_id ,
            "ex" : obj.select.options[obj.select.selectedIndex].text,
            "op":"history",
            "exercice":obj.exercice
        };
        waiting_box();
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get',
                    parameters: querystring,
                    onFailure: error_box,
                    onSuccess: function (req, xml)
                    {
                        remove_waiting_box();
                        success_box(req, xml);
                        g(obj.div).style.top = calcy(140 + (layer * 3)) + "px";
                    }
                });
    } catch (e)
    {
        alert_box("update_history_account error " + e.message);
    }

    return false;
}
/**
 * remove an Operation
 *@param p_jr_id is the jrn.jr_id
 *@param dossier
 *@param the div
 */
function removeOperation(p_jr_id, dossier, div)
{
    waiting_box();
    var qs = { "gDossier" :  dossier ,
        "op":"ledger",
        "act":"rmop",
        "div" : div ,
        "jr_id" : p_jr_id};
    new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: qs,
                onFailure: error_box,
                onSuccess: infodiv
            }
    );

}

/**
 * reverse an Operation
 *@param pointer to the FORM
 */
function reverseOperation(obj)
{
    var qs = $(obj).serialize()+ "&op=ledger";
    g('ext' + obj.divname).style.display = 'none';
    g('bext' + obj.divname).style.display = 'none';
    waiting_box();
    new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: qs,
                onFailure: error_box,
                onSuccess: function (req) {
                    try {
                        var action = new Ajax.Request(
                                "ajax_misc.php",
                                {
                                    method: 'get',
                                    parameters:{"gDossier" : obj["gDossier"].value,"op":"ledger","act":"de","div":obj['div'].value,"jr_id":obj['jr_id'].value},
                                    onFailure: error_box,
                                    onSuccess: function (xml, txt) {
                                        
                                        success_box(xml, txt);
                                        infodiv(req);
                                    }
                                });
                    } catch (ex) {
                        smoke.alert(ex.message);
                    }
                }
            }
    );

    return false;
}

/*!
 * \brief Show the details of an operation
 * \param p_value jrn.jr_id
 * \param dossier dossier id
 */
function modifyOperation(p_value, dossier)
{
    layer++;
    var id_div = 'det' + layer;
    waiting_box();
    var querystring = { "gDossier" :  dossier ,
        "op":"ledger",
        "act":"de",
        "div" : id_div ,
        "jr_id" : p_value};
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: querystring,
                onFailure: error_box,
                onSuccess: function (xml, txt) {
                    var popup = {'id': id_div, 'cssclass': 'inner_box'
                        , 'html': "", 'drag': false};
                    remove_waiting_box();
                    add_div(popup);
                    success_box(xml, txt);
                    $(id_div).style.position = "absolute";
                    $(id_div).style.top = calcy(100 + (layer * 3)) + "px";
                }
            }
    );
}

/*!\brief
 * \param p_value jrn.jr_id
 */

function viewOperation(p_value, p_dossier)
{
    modifyOperation(p_value, p_dossier)
}
function dropLink(p_dossier, p_div, p_jr_id, p_jr_id2)
{
    var querystring = { "gDossier" :  p_dossier ,
        "op":"ledger",
        "act":"rmr",
        "div" : p_div ,
        "jr_id" : p_jr_id,
        "jr_id2" : p_jr_id2};
    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: querystring,
                onFailure: null,
                onSuccess: null
            }
    );
}
/**
 *@brief this function is called before the querystring is send to the
 * fid2.php, add a filter based on the ledger 'p_jrn'
 *@param obj is the input field
 *@param queryString is the queryString to modify
 *@see ICard::input
 */
function filter_card(obj, queryString)
{
    jrn = $('p_jrn').value;
    if (jrn == -1)
    {
        type = $('ledger_type').value;
        queryString = queryString + '&type=' + type;
    }
    else
    {
        queryString = queryString + '&j=' + jrn;
    }
    return queryString;
}
/**
 *@brief to display the lettering for the operation, call
 * ajax function
 *@param obj object attribut :  gDossier,j_id,obj_type
 */
function dsp_letter(obj)
{
    try
    {
        var queryString = 'gDossier=' + obj.gDossier + '&j_id=' + obj.j_id + '&op=dl' + '&ot=' + obj.obj_type;
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get',
                    parameters: queryString,
                    onFailure: error_dsp_letter,
                    onSuccess: success_dsp_letter
                }
        );
        g('search').style.display = 'none';
        g('list').style.display = 'none';
        $('detail').innerHTML = loading();
        g('detail').style.display = 'block';
    }
    catch (e)
    {
        alert_box('dsp_letter failed  ' + e.message);
    }
}

function success_dsp_letter(req)
{
    try
    {
        var answer = req.responseXML;
        var a = answer.getElementsByTagName('code');
        var html = answer.getElementsByTagName('value');
        if (a.length == 0)
        {
            var rec = req.responseText;
            alert_box('erreur :' + rec);
        }
        var name_ctl = a[0].firstChild.nodeValue;
        var code_html = getNodeText(html[0]);
        code_html = unescape_xml(code_html);
        $('detail').innerHTML = code_html;
    }
    catch (e)
    {
        alert_box(e.message);
    }
    try
    {
        code_html.evalScripts();
    }
    catch (e)
    {
        alert_box("Impossible executer script de la reponse\n" + e.message);
    }

}
function error_dsp_letter(req)
{
    alert_box('Erreur AJAX DSP_LETTER');
}

function search_letter(obj)
{
    try
    {
        var str_query = '';
        if (obj.elements['gDossier'])
            str_query = 'gDossier=' + obj.elements['gDossier'].value;
        if (obj.elements['j_id'])
            str_query += '&j_id=' + obj.elements['j_id'].value;
        if (obj.elements['ot'])
            str_query += '&ot=' + obj.elements['ot'].value;
        if (obj.elements['op'])
            str_query += '&op=' + obj.elements['op'].value;
        if (obj.elements['min_amount'])
            str_query += '&min_amount=' + obj.elements['min_amount'].value;
        if (obj.elements['max_amount'])
            str_query += '&max_amount=' + obj.elements['max_amount'].value;
        if (obj.elements['search_start'])
            str_query += '&search_start=' + obj.elements['search_start'].value;
        if (obj.elements['search_end'])
            str_query += '&search_end=' + obj.elements['search_end'].value;
        if (obj.elements['side'])
            str_query += '&side=' + obj.elements['side'].value;


        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get',
                    parameters: str_query,
                    onFailure: error_dsp_letter,
                    onSuccess: success_dsp_letter
                }
        );
        $('list').hide();
        $('search').hide();
        $('detail').innerHTML = loading();
        $('detail').show();
    }
    catch (e)
    {
        alert_box('search_letter  ' + e.message);
    }
}
/**
 *@brief save an operation in ajax, it concerns only the
 * comment, the pj and the rapt
 * the form elements are access by their name
 *@param obj form
 */
function op_save(obj)
{
    try {
        var queryString = $(obj).serialize();
        queryString += "&gDossier=" + obj.gDossier.value;
        var rapt2 = "rapt" + obj.whatdiv.value;
        queryString += "&rapt=" + g(rapt2).value;
        queryString += '&jr_id=' + obj.jr_id.value;
        var jr_id=obj.jr_id.value;
        queryString += '&div=' + obj.whatdiv.value;
        var divid=obj.whatdiv.value;
        queryString += '&act=save';
        queryString += '&op=ledger';
        
        waiting_box();
        /*
         * Operation detail is in a new window
         */
        if (g('inpopup'))
        {
            var action = new Ajax.Request('ajax_misc.php',
                    {
                        method: 'post',
                        parameters: encodeURI(queryString),
                        onFailure: null,
                        onSuccess: infodiv
                    }
            );
            // window.close();
        }
        else
        {
            /*
             *Operation is in a modal box 
             */
            var action = new Ajax.Request('ajax_misc.php',
                    {
                        method: 'post',
                        parameters: queryString,
                        onFailure: null,
                        onSuccess: function(req,json) {
                            new Ajax.Request('ajax_misc.php', {
                                parameters:{'gDossier':obj.gDossier.value,
                                         'act':'de',
                                         'op':'ledger',
                                         'jr_id' :  jr_id,
                                         'div' :  divid},
                                onSuccess:function(xml) {
                                    try {
                                        var answer=xml.responseXML;
                                        var html = answer.getElementsByTagName('code');
                                        $(divid).innerHTML=unescape(getNodeText(html[0]));
                                         $(divid).innerHTML.evalScripts();
                                        remove_waiting_box();
                                        }  catch (e) {
                                            alert_box("1038"+e.message)
                                        } 
                                    }
                             });
                            
                        }
                    });
        }
        return false;
    } catch (e)
    {
        alert_box(e.message);
    }
}
function  get_history_account(ctl, dossier) {
    if ($(ctl).value != '')
    {
        view_history_account($(ctl).value, dossier);
    }
}
var previous = [];
var let_previous="";
function show_reconcile(p_div, p_let)
{
    try
    {
        if (previous.length != 0 || p_let == let_previous )
        {
            var count_elt = previous.length;
            var i = 0;
            for (i = 0; i < count_elt; i++) {
                previous[i].style.backgroundColor = '';
                previous[i].style.color = '';
                previous[i].style.fontWeight = "";
            }
        }
        var name = 'tr_' + p_let + '_' + p_div;
        var elt = document.getElementsByName(name);
        if ( p_let != let_previous )  {
            
            previous = elt;
            var count_elt = elt.length;
            var i = 0;
            for (i = 0; i < count_elt; i++) {
                elt[i].style.backgroundColor = '#000066';
                elt[i].style.color = 'white';
                elt[i].style.fontWeight = 'bolder';

            }
            let_previous=p_let;
        }
        else {
                let_previous="";
        }

    } catch (e)
    {
        alert_box(e.message);
    }


}
/**
 * @brief add a line in the form for the purchase ledger
 */
function gestion_add_row()
{
    try {
        style = 'class="input_text"';
        var mytable = g("art").tBodies[0];
        var ofirstRow = mytable.rows[1];
        var line = mytable.rows.length;
        var nCell = mytable.rows[1].cells.length;
        var row = mytable.insertRow(line);
        var nb = g("nb_item");
        for (var e = 0; e < nCell; e++)
        {
            var newCell = row.insertCell(e);
            var tt = ofirstRow.cells[e].innerHTML;
            var new_tt = tt.replace(/march0/g, "march" + nb.value);
            new_tt = new_tt.replace(/quant0/g, "quant" + nb.value);
            new_tt = new_tt.replace(/sold\(0\)/g, "sold(" + nb.value + ")");
            new_tt = new_tt.replace(/compute_ledger\(0\)/g, "compute_ledger(" + nb.value + ")");
            new_tt = new_tt.replace(/clean_tva\(0\)/g, "clean_tva(" + nb.value + ")");
            new_tt = new_tt + '<input type="hidden" id="tva_march' + nb.value + '">';
            new_tt = new_tt + '<input type="hidden" id="htva_march' + nb.value + '">';
            newCell.innerHTML = new_tt;
            if (mytable.rows[1].cells[e].hasClassName("num")) {
                newCell.addClassName("num");
            }
            new_tt.evalScripts();
        }

        g("e_march" + nb.value + "_label").innerHTML = '&nbsp;';
        g("e_march" + nb.value + "_label").value = '';
        g("e_march" + nb.value + "_price").value = '0';
        g("e_march" + nb.value).value = "";
        g("e_quant" + nb.value).value = "1";
        g('tvac_march' + nb.value).value = "0";
        if ($("e_march" + nb.value + "_tva_amount"))
            g("e_march" + nb.value + "_tva_amount").value = 0;

        nb.value++;

        new_tt.evalScripts();
    } catch (e) {
        alert_box(e.message);
    }

}
function document_remove(p_dossier,p_div,p_jrid)
{
    smoke.confirm('Effacer ?', function (e) 
    {
        if (e) {
            new Ajax.Request('ajax_misc.php',
            {
                parameters:{"op":"ledger","gDossier":p_dossier,"div":p_div,"p_jrid":p_jrid,'act':'rmf'},
                onSuccess : function(x) {
                    $('receipt'+p_div).innerHTML=x.responseText;
                }
            })
        }
    });
}
/***
 * @brief receive an object and display a list of filter + form to save one
 * fill up the span (id : {div}search_filter_span) with the name of the selected filter
 * Object = '{'div':'','type':'ALL','all_type':1,'dossier':'10104'}' 
 * @see Acc_Ledger_Search
 */
function manage_search_filter(p_obj) {
    waiting_box();
    new Ajax.Request("ajax_misc.php", {
       method:'get',
       parameters:{"op":"display_search_filter","gDossier":p_obj.dossier,"div":p_obj.div,"ledger_type":p_obj.ledger_type,"all_type":p_obj.all_type},
       onSuccess:function(req) {
            remove_waiting_box();
            var x=posX;
            var y=posY-20;
            create_div({'id':'boxfilter'+p_obj.div,'cssclass':'inner_box','html':req.responseText,'style':'top:'+y+'px;left:'+x+'px;position:absolute;width:400px'});
            $('boxfilter'+p_obj.div).show();
       }
    });
}

/**
 * Send data from the form and record a new filter , the ajax answer is a json object
 * with the attribute filter_name,filter_id,status,message
 * 
 * @param p_div prefix id of all concerned DOM Element
 * @param p_dossier 
 * @see Acc_Ledger_Search
 */
function save_filter(p_div,p_dossier) {
    var elt=['ledger_type','nb_jrn','date_start','date_end','date_paid_start','date_paid_end','desc','amount_min','amount_max','qcode','accounting'];
    var eltValue={};
    eltValue['gDossier']=p_dossier;
    eltValue['op']="save_filter";
    eltValue['div']=p_div;
    eltValue['filter_name']=$(p_div+"filter_new").value;
    // Get all elt from the form
    for ( var i = 0 ; i < elt.length;i++) {
        var idx=elt[i];
        eltValue[idx]=$(p_div+elt[i]).value;
   
    }
    if (eltValue['amount_min']=="") eltValue["amount_min"]=0;
    if (eltValue['amount_max']=="") eltValue["amount_max"]=0;
    
    //ledger's list r_jrn
    if (eltValue['nb_jrn'] > 0) {
        eltValue['r_jrn']=[];
        for (i=0;i<eltValue['nb_jrn'];i++) {
            var idx=p_div+'r_jrn['+i+']';
            eltValue['r_jrn'+i]=$(idx).value
   
        }
    }
    //unpaid
    eltValue['unpaid']=$(p_div+"unpaid").checked;
    new Ajax.Request('ajax_misc.php', {
        method:"POST",
        parameters:eltValue,
        onSuccess:function (req) {
            try {
                var answer=req.responseJSON;
                if ( answer.status == 'OK') {
                    /*Add the new list to the selection */
                    var new_item=document.createElement('li');
                    new_item.innerHTML=answer.filter_name;
                    new_item.setAttribute("id","li"+p_div+"_"+answer.filter_id);
                    $('manage'+p_div).appendChild(new_item);
                    $(p_div+"filter_new").value="";
                } else {
                    throw answer.message;
                }
            } catch (e) {
                smoke.alert(e);
            }
        }
    });
}
/**
 * Load a search filter  and fill up the form search
 * @param p_div prefix id of all concerned DOM Element
 * @param p_dossier 
 * @param p_filter_id filter id (SQL user_filter.id)
 * @see Acc_Ledger_Search
 */
function load_filter(p_div,p_dossier,p_filter_id) {
    new Ajax.Request('ajax_misc.php',{
       method:"get",
       parameters:{"gDossier":p_dossier,"div":p_div,"op":"load_filter","filter_id":p_filter_id},
       onSuccess:function (req) {
           try {
                var answer=req.responseJSON;    
                console.log(answer);
                var elt=['ledger_type','date_start','date_end','date_paid_start','date_paid_end','desc','amount_min','amount_max','qcode','accounting'];
                for (var i=0;i<elt.length;i++) {
                    var idx=elt[i];
                    $(p_div+idx).value=answer[elt[i]]
                }
               // fillup the r_jrn array
               var eltLedgerId=$("ledger_id"+p_div);
               eltLedgerId.innerHTML="";
               var eltHidden=document.createElement("input");
               eltHidden.setAttribute("name",p_div+"nb_jrn");
               eltHidden.setAttribute("type","hidden");
               eltHidden.setAttribute("id",p_div+"nb_jrn");
               eltHidden.setAttribute("value",answer.nb_jrn);
               eltLedgerId.appendChild(eltHidden);  
               
               for ( var i=0;i < answer.nb_jrn;i++) {
                   // create hidden element and add them into eltLedgerId
                   var eltHidden=document.createElement("input");
                   
                   eltHidden.setAttribute("name",p_div+"r_jrn["+i+"]");
                   eltHidden.setAttribute("type","hidden");
                   eltHidden.setAttribute("id",p_div+"r_jrn["+i+"]");
                   eltHidden.setAttribute("value",answer.r_jrn[i]);
                   eltLedgerId.appendChild(eltHidden);
               }
               if ( answer.unpaid == 'false') {
                   $(p_div+"unpaid").checked=false;
               }
               if ( answer.unpaid == 'true') {
                   $(p_div+"unpaid").checked=true;
               }

               
           } catch (e) {
              smoke.alert(e.message);
           }
           
       }
    });
}
/**
 * @brief delete a saved search filter  from the db, it is limited to the current
 * user
 * @parameter p_div
identification des elements LI manageli{div}_{filter_id}
identification element UL manage{div}
@parameter p_filter_id SQL user_filter.id
*/

function delete_filter (p_div,p_dossier,p_filter_id) {
    new Ajax.Request("ajax_misc",{
        parameters:{"gDossier":p_dossier,"div":p_div,"filter_id":p_filter_id,'op':"delete_search_operation"},
        method:"POST",
        onSuccess:function (req) {
            try {
            var answer=req.evalJSON;
           
            var child=$("manageli"+p_div+"_"+p_filter_id);
                if ( child )  {$("manage"+p_div).removeChild(child); }
            }catch (e) {
                console.log(e.message)
            }
            
        }
    })
    
}     
/**
 * Reset the search_form and reinitialize all the input but ledger_type
 * @param p_div prefix for DOM Element
 */
function reset_filter(p_div) {
  // clean all the input fields but ledger_type remains
  var elt=['date_start','date_end','date_paid_start','date_paid_end','desc','amount_min','amount_max','qcode','accounting'];
                for (var i=0;i<elt.length;i++) {
                    var idx=elt[i];
                    $(p_div+idx).value="";
                }
  if ( $(p_div+"date_start_hidden")) {
      $(p_div+"date_start").value=$(p_div+"date_start_hidden").value;
  }
  if ( $(p_div+"date_end_hidden")) {
      $(p_div+"date_end").value=$(p_div+"date_end_hidden").value;
  }
  // clean all the selected ledger
   var eltLedgerId=$("ledger_id"+p_div);
               eltLedgerId.innerHTML="";
               var eltHidden=document.createElement("input");
               eltHidden.setAttribute("name",p_div+"nb_jrn");
               eltHidden.setAttribute("type","hidden");
               eltHidden.setAttribute("id",p_div+"nb_jrn");
               eltHidden.setAttribute("value",0);
               eltLedgerId.appendChild(eltHidden);
  
  // By default , unpaid is uncked
   $(p_div+"unpaid").checked=false;
}