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
 * \brief javascript script, always added to every page
 *
 */
var ask_reload = 0;
var tag_choose = '';

/**
 * callback function when we just need to update a hidden div with an info
 * message
 */
function infodiv(req, json)
{
    try
    {
        remove_waiting_box();
        var answer = req.responseXML;
        var a = answer.getElementsByTagName('ctl');
        var html = answer.getElementsByTagName('code');
        if (a.length === 0)
        {
            var rec = req.responseText;
            alert_box('erreur :' + rec);
        }
        var name_ctl = a[0].firstChild.nodeValue;
        var code_html = getNodeText(html[0]);

        code_html = unescape_xml(code_html);
        g(name_ctl + "info").innerHTML = code_html;
    }
    catch (e)
    {
        alert_box("success_box" + e.message);
    }
    try
    {
        code_html.evalScripts();
    }
    catch (e)
    {
        alert_box("answer_box Impossible executer script de la reponse\n" + e.message);
    }

}
/**
 *@brief delete a row from a table (tb) the input button send the this
 as second parameter
 */
function deleteRow(tb, obj)
{
    smoke.confirm('Confirmez effacement',function (e)
    {
        if (e) {
            var td = obj.parentNode;
            var tr = td.parentNode;
            var lidx = tr.rowIndex;
            g(tb).deleteRow(lidx);
            
        } else {
            return ;
        }
    });
}
function deleteRowRec(tb, obj)
{
    var tr = obj;
    var lidx = tr.rowIndex;
    g(tb).deleteRow(lidx);
}
/*!\brief remove trailing and heading space
 * \param the string to modify
 * \return string without heading and trailing space
 */
function trim(s)
{
    return s.replace(/^\s+/, '').replace(/\s+$/, '');
}

/**
 * @brief retrieve an element thanks its ID
 * @param ID is a string
 * @return the found object of undefined if not found
 */
function g(ID)
{
    if (document.getElementById)
    {
        return this.document.getElementById(ID);
    }
    else if (document.all)
    {
        return document.all[ID];
    }
    else
    {
        return undefined;
    }
}
/**
 *@brief enable the type of periode
 */
function enable_type_periode()
{
    if ($("type_periode").options[$("type_periode").selectedIndex].value == 0)
    {
        $('from_periode').enable();
        $('to_periode').enable();
        $('from_date').disable();
        $('to_date').disable();
        $('p_step').enable();
    }
    else
    {
        $('from_periode').disable();
        $('to_periode').disable();
        $('from_date').enable();
        $('to_date').enable();
        $('p_step').disable();
    }
}

/**
 *@brief will reload the window but it is dangerous if we have submitted
 * a form with POST
 */
function refresh_window()
{
    window.location.reload();
}

/**
 *@fn encodeJSON(obj)
 *@brief we receive a json object as parameter and the function returns the string
 *       with the format variable=value&var2=val2...
 */
function encodeJSON(obj)
{
    if (typeof obj != 'object')
    {
        alert_box('encodeParameter  obj n\'est pas  un objet');
    }
    try
    {
        var str = '';
        var e = 0;
        for (i in obj)
        {
            if (e !== 0)
            {
                str += '&';
            }
            else
            {
                e = 1;
            }
            str += i;
            str += '=' + encodeURI(obj[i]);
        }
        return str;
    }
    catch (e)
    {
        alert_box('encodeParameter ' + e.message);
        return "";
    }
}
function  hide(p_param)
{
    g(p_param).style.display = 'none';
}
function show(p_param)
{
    g(p_param).style.display = 'block';
}

/**
 *@brief set the focus on the selected field
 *@param Field id of  the control
 *@param selectIt : the value selected in case of Field is a object select, numeric
 */
function SetFocus(Field, SelectIt)
{
    var elem = g(Field);
    if (elem)
    {
        elem.focus();
    }
    return true;
}
/**
 * @brief set a DOM id with a value in the parent window (the caller),
 @param p_ctl is the name of the control
 @param p_value is the value to set in
 @param p_add if we don't replace the current value but we add something
 */
function set_inparent(p_ctl, p_value, p_add)
{
    self.opener.set_value(p_ctl, p_value, p_add);
}

/**
 * @brief set a DOM id with a value, it will consider if it the attribute
 value or innerHTML has be used
 @param p_ctl is the name of the control
 @param p_value is the value to set in
 @param p_add if we don't replace the current value but we add something
 */
function set_value(p_ctl, p_value, p_add)
{
    if (g(p_ctl))
    {
        var g_ctrl = g(p_ctl);
        if (p_add != undefined && p_add === 1)
        {
            if (g_ctrl.value)
            {
                p_value = g_ctrl.value + ',' + p_value;
            }
        }
        if (g_ctrl.tagName === 'INPUT')
        {
            g(p_ctl).value = p_value;
        }
        if (g_ctrl.tagName === 'SPAN')
        {
            g(p_ctl).innerHTML = p_value;
        }
        if (g_ctrl.tagName === 'SELECT')
        {
            g(p_ctl).value = p_value;
        }
    }
}
/**
 *@brief format the number change comma to point
 *@param HTML obj
 */
function format_number(obj, p_prec)
{
    var precision = 2;
    if (p_prec === undefined)
    {
        precision = 2;
    } else {
        precision = p_prec;
    }
    var value = obj.value;
    value = value.replace(/,/, '.');
    value = parseFloat(value);
    if (isNaN(value))
    {
        value = 0;
    }
    var arrondi = Math.pow(10, precision);

    value = Math.round(value * arrondi) / arrondi;

    $(obj).value = value;
}
/**
 *@brief check if the object is hidden or show and perform the opposite,
 * show the hidden obj or hide the shown one
 *@param name of the object
 */
function toggleHideShow(p_obj, p_button)
{
    var stat = g(p_obj).style.display;
    var str = g(p_button).value;
    if (stat === 'none')
    {
        show(p_obj);
        str = str.replace(/Afficher/, 'Cacher');
        g(p_button).value = str;
    }
    else
    {
        hide(p_obj);
        str = str.replace(/Cacher/, 'Afficher');
        g(p_button).value = str;
    }
}
/**
 *@brief open popup with the search windows
 *@param p_dossier the dossier where to search
 *@param p_style style of the detail value are E for expert or S for simple
 */
function popup_recherche(p_dossier)
{
    var w = window.open("recherche.php?gDossier=" + p_dossier + "&ac=SEARCH", '', 'statusbar=no,scrollbars=yes,toolbar=no');
    w.focus();
}
/**
 *@brief replace the special characters (><'") by their HTML representation
 *@return a string without the offending char.
 */
function unescape_xml(code_html)
{
    code_html = code_html.replace(/\&lt;/, '<');
    code_html = code_html.replace(/\&gt;/, '>');
    code_html = code_html.replace(/\&quot;/, '"');
    code_html = code_html.replace(/\&apos;/, "'");
    code_html = code_html.replace(/\&amp;/, '&');
    return code_html;
}
/**
 *@brief Firefox splits the XML into 4K chunk, so to retrieve everything we need
 * to get the different parts thanks textContent
 *@param xmlNode a node (result of var data = =answer.getElementsByTagName('code'))
 *@return all the content of the XML node
 */
function getNodeText(xmlNode)
{
    if (!xmlNode)
        return '';
    if (typeof (xmlNode.textContent) != "undefined")
    {
        return xmlNode.textContent;
    }
    if (xmlNode.firstChild && xmlNode.firstChild.nodeValue)
        return xmlNode.firstChild.nodeValue;
    return "";
}
/**
 *@brief change the periode in the calendar of the dashboard
 *@param object select
 */
function change_month(obj)
{
    var action = new Ajax.Request(
            "ajax_misc.php", 
            {
                method: 'get', 
                parameters: { gDossier : obj.gDossier , op:'cal' ,"per"  : obj.value , t: obj.type_display, notitle:obj.notitle},
                onFailure: ajax_misc_failure, 
                onSuccess: success_misc
            }
    );

}
/**
 *@brief basic answer to ajax on success, it will fill the DOMID code with
 * the code. In that case, you need to create the object before the Ajax.Request
 *The difference with success box is that
 *@see add_div removeDiv success_box is that the width and height are not changed ajax_misc.php
 *@parameter code is the ID of the object containing the html (div, button...)
 *@parameter value is the html code, with it you fill the ctl element
 */

function success_misc(req)
{
    try
    {
        var answer = req.responseXML;
        var html = answer.getElementsByTagName('code');
        if (html.length === 0)
        {
            var rec = req.responseText;
            alert_box('erreur :' + rec);
        }
        var nodeXml = html[0];
        var code_html = getNodeText(nodeXml);
        code_html = unescape_xml(code_html);
        $("user_cal").innerHTML = code_html;
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
function loading()
{
    var str = '<h2> Un instant ...</h2>';
    str = str + '<image src="image/loading.gif" alt="chargement"></image>';
    return str;
}

function ajax_misc_failure()
{
    alert_box('Ajax Misc failed');
}
/**
 *@brief remove a document_modele
 */
function cat_doc_remove(p_dt_id, p_dossier)
{
    var queryString = "gDossier=" + p_dossier + "&op=rem_cat_doc" + "&dt_id=" + p_dt_id;
    var action = new Ajax.Request(
            "ajax_misc.php", {method: 'get',
                parameters: queryString,
                onFailure: ajax_misc_failure,
                onSuccess: function (req)
                {
                    try
                    {
                        var answer = req.responseXML;
                        var html = answer.getElementsByTagName('dtid');
                        if (html.length === 0)
                        {
                            var rec = req.responseText;
                            alert_box('erreur <br>' + rec );
                            return;
                        }
                        nodeXML = html[0];
                        row_id = getNodeText(nodeXML);
                        if (row_id === 'nok')
                        {
                            var message_node = answer.getElementsByTagName('message');
                            var message_text = getNodeText(message_node[0]);
                            alert_box('erreur <br>' + message_text);
                            return;
                        }
                        $('row' + row_id).style.textDecoration = "line-through";
                        $('X' + row_id).style.display='none';
                        $('M' + row_id).style.display='none';
                    }
                    catch (e)
                    {
                        alert_box(e.message);
                    }
                }
            }
    );
}
/**
 *@brief change a document_modele
 */
function cat_doc_change(p_dt_id, p_dossier)
{
    var queryString = "gDossier=" + p_dossier + "&op=mod_cat_doc" + "&dt_id=" + p_dt_id;
    var nTop = calcy(posY);
    var nLeft = "200px";
    var str_style = "top:" + nTop + "px;left:" + nLeft + ";width:50em;height:auto";

    removeDiv('change_doc_div');
    waiting_box();
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get', parameters: queryString,
                onFailure: ajax_misc_failure,
                onSuccess: function (req) {
                    remove_waiting_box();
                    add_div({id: 'change_doc_div', style: str_style, cssclass: 'inner_box', drag: "1"});
                    $('change_doc_div').innerHTML = req.responseText;

                }
            }
    );
}

/**
 *@brief display the popup with vat and explanation
 *@param obj with 4 attributes gdossier, ctl,popup
 */
function popup_select_tva(obj)
{
    try
    {
        if ($('tva_select')) {
            removeDiv('tva_select');
        }

        var queryString = "gDossier=" + obj.gDossier + "&op=dsp_tva" + "&ctl=" + obj.ctl + '&popup=' + 'tva_select';
        if (obj.jcode)
            queryString += '&code=' + obj.jcode;
        if (obj.compute)
            queryString += '&compute=' + obj.compute;

        var action = new Ajax.Request(
                "ajax_misc.php",
                {method: 'get',
                    parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req)
                    {
                        try
                        {
                            var answer = req.responseXML;
                            var popup = answer.getElementsByTagName('popup');
                            if (popup.length === 0)
                            {
                                var rec = req.responseText;
                                alert_box('erreur :' + rec);
                            }
                            var html = answer.getElementsByTagName('code');

                            var name_ctl = popup[0].firstChild.nodeValue;
                            var nodeXml = html[0];
                            var code_html = getNodeText(nodeXml);
                            code_html = unescape_xml(code_html);

                            var nTop = posY - 200;
                            var nLeft = "15%";
                            var str_style = "top:" + nTop + "px;left:" + nLeft + ";right:" + nLeft + ";width:55em;height:auto";

                            var popup = {'id': 'tva_select', 'cssclass': 'inner_box', 'style': str_style, 'html': code_html, 'drag': true};
                            add_div(popup);
                            $('lk_tva_select_table').focus();
                        }
                        catch (e)
                        {
                            alert_box("success_popup_select_tva " + e.message);
                        }
                    }
                }
        );
    }
    catch (e)
    {
        alert_box("popup_select_tva " + e.message);
    }
}
/**
 *@brief display the popup with vat and explanations
 *@obsolete
 */
function success_popup_select_tva_obsolete(req)
{


}

/**
 *@brief display the popup with vat and explanation
 *@param obj with 4 attributes gdossier, ctl,popup
 */
function set_tva_label(obj)
{
    try
    {
        var queryString = "gDossier=" + obj.gDossier + "&op=label_tva" + "&id=" + obj.value;
        if (obj.jcode)
            queryString += '&code=' + obj.jcode;
        var action = new Ajax.Request(
                "ajax_misc.php",
                {method: 'get',
                    parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: success_set_tva_label
                }
        );
    }
    catch (e)
    {
        alert_box("set_tva_label " + e.message);
    }
}
/**
 *@brief display the popup with vat and explanations
 */
function success_set_tva_label(req)
{
    try
    {
        var answer = req.responseXML;
        var code = answer.getElementsByTagName('code');
        var value = answer.getElementsByTagName('value');

        if (code.length === 0)
        {
            var rec = req.responseText;
            alert_box('erreur :' + rec);
        }

        var label_code = code[0].firstChild.nodeValue;
        var label_value = value[0].firstChild.nodeValue;
        set_value(label_code, label_value);
    }
    catch (e)
    {
        alert_box("success_set_tva_label " + e.message);
    }

}
/**
 *@brief set loading for waiting
 *@param name of ipopup
 *@see showIPopup
 *@obsolete
 */
function set_wait_obsolete(name)
{
    var content = name + "_content";
    $(content).innerHTML = 'Un instant...<image src="image/loading.gif" border="0" alt="Chargement...">';
}
/**
 * Create a div without showing it
 * @param {type} obj
 *  the attributes are
 *   - style to add style
 *   - id to add an id
 *   - cssclass to add a class
 *   - html is the content
 *   - drag is the div can be moved
 * @returns html dom element
 * @see add_div
 */
function create_div(obj)
{
    try
    {
        var top = document;
        var elt = null;
        if (!$(obj.id)) {
             elt = top.createElement('div');
        }
        else {
            elt = $(obj.id);
        }
        if (obj.id)
        {
            elt.setAttribute('id', obj.id);
        }
        if (obj.style)
        {
            if (elt.style.setAttribute)
            { /* IE7 bug */
                elt.style.setAttribute('cssText', obj.style);
            }
            else
            { /* good Browser */
                elt.setAttribute('style', obj.style);
            }
        }
        if (obj.cssclass)
        {
            elt.setAttribute('class', obj.cssclass); /* FF */
            elt.setAttribute('className', obj.cssclass); /* IE */
        }
        if (obj.html)
        {
            elt.innerHTML = obj.html;
        }

        var bottom_div = document.body;
        elt.hide();
        bottom_div.appendChild(elt);
        
        /* if ( obj.effect && obj.effect != 'none' ) { Effect.Grow(obj.id,{direction:'top-right',duration:0.1}); }
         else if ( ! obj.effect ){ Effect.Grow(obj.id,{direction:'top-right',duration:0.1}); }*/
        if (obj.drag)
        {
            new Draggable(obj.id, {starteffect: function ()
                {
                    new Effect.Highlight(obj.id, {scroll: window, queue: 'end'});
                }}
            );
        }
        return elt;
    }
    catch (e)
    {
         error_message("create_div " + e.message);
    }
}
/**
 *@brief add dynamically a object for AJAX
 *@param obj.
 * the attributes are
 *   - style to add style
 *   - id to add an id
 *   - cssclass to add a class
 *   - html is the content
 *   - drag is the div can be moved
 */
function add_div(obj)
{
    try {
        var elt=create_div(obj);
        /* elt.setStyle({visibility:'visible'}); */
        elt.style.visibility = 'visible';
        elt.show();
    }
    catch (e)
    {
        alert_box("add_div " + e.message);
    }
}
/**
 * remove a object created with add_div
 * @param elt id of the elt
 */
function removeDiv(elt)
{
    if (g(elt))
    {
        document.body.removeChild(g(elt));
    }
    // if reloaded if asked the window will be reloaded when
    // the box is closed
    if (ask_reload === 1)
    {
        // avoid POST window.location = window.location.href;
        window.location.reload();
    }
}
function waiting_node()
{
    $('info_div').innerHTML = 'Un instant';
    $('info_div').style.display = "block";
}
/**
 *show a box while loading
 *must be remove when ajax is successfull
 * the id is wait_box
 */
function waiting_box()
{
    var obj = {
        id: 'wait_box', html: '<h2 class="title">Chargement</h2>' + loading()
    };
    var y = fixed_position(10, 250)
    obj.style = y + ";width:20%;margin-left:40%;";
    if ($('wait_box')) {
        removeDiv('wait_box');
    }
    waiting_node();
    add_div(obj);
    $('wait_box').setOpacity(0.7);

}
/**
 *@brief call add_div to add a DIV and after call the ajax
 * the queryString, the callback for function for success and error management
 * the method is always GET
 *@param obj, the mandatory attributes are
 *  - obj.qs querystring
 *  - obj.js_success callback function in javascript for handling the xml answer
 *  - obj.js_error callback function for error
 *  - obj.callback the php file to call
 *  - obj.fixed optional let you determine the position, otherwise works like IPopup
 *@see add_div IBox
 */
function show_box(obj)
{
    add_div(obj);
    if (!obj.fixed)
    {
        var sx = 0;
        if (window.scrollY)
        {
            sx = window.scrollY + 40;
        }
        else
        {
            sx = document.body.scrollTop + 40;
        }
        g(obj.id).style.top = sx + "px";
        show(obj.id);
    }
    else
    {
        show(obj.id);
    }

    var action = new Ajax.Request(
            obj.callback,
            {
                method: 'GET',
                parameters: obj.qs,
                onFailure: eval(obj.js_error),
                onSuccess: eval(obj.js_success)
            });
}
/**
 *@brief receive answer from ajax and just display it into the IBox
 * XML must contains at least 2 fields : ctl is the ID of the IBOX and
 * code is the HTML to put in it
 *@see fill_box
 */
function success_box(req, json)
{
    try
    {
        var answer = req.responseXML;
        var a = answer.getElementsByTagName('ctl');
        var html = answer.getElementsByTagName('code');
        if (a.length === 0)
        {
            var rec = req.responseText;
            alert_box('erreur :' + rec);
        }
        var name_ctl = a[0].firstChild.nodeValue;
        var code_html = getNodeText(html[0]);

        code_html = unescape_xml(code_html);
        g(name_ctl).innerHTML = code_html;
        g(name_ctl).style.height = 'auto';

        if (name_ctl == 'popup')
            g(name_ctl).style.width = 'auto';
    }
    catch (e)
    {
        alert_box("success_box" + e.message);
    }
    try
    {
        code_html.evalScripts();
    }
    catch (e)
    {
        alert_box("answer_box Impossible executer script de la reponse\n" + e.message);
    }
}

function error_box()
{
    alert_box('[error_box] ajax not implemented');
}
/**
 * show the ledger choice
 */
function show_ledger_choice(json_obj)
{
    try
    {
        waiting_box();
        var i = 0;
        var query = "gDossier=" + json_obj.dossier + '&type=' + json_obj.type + '&div=' + json_obj.div + '&op=ledger_show';
        query = query + '&nbjrn=' + $(json_obj.div + 'nb_jrn').value;
        query = query + '&all_type=' + json_obj.all_type;
        for (i = 0; i < $(json_obj.div + 'nb_jrn').value; i++) {
            query = query + "&r_jrn[]=" + $(json_obj.div + 'r_jrn[' + i + ']').value;
        }
        var action = new Ajax.Request(
                "ajax_misc.php",
                {method: 'get',
                    parameters: query,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req, json) {
                        try {
                            var obj = {
                                id: json_obj.div + 'jrn_search',
                                cssclass: 'inner_box',
                                style: ';position:absolute;width:60%;z-index:20;margin-left:20%',
                                drag: 1
                            };
                            //var y=calcy(posY);
                            var y = posY;
                            if (json_obj.div != '')
                                obj.cssclass = "";
                            obj.style = "top:" + y + 'px;' + obj.style;
                            /* if ( json_obj.class ) 
                             { 
                             obj.cssclass=json_obj.class;
                             }*/
                            add_div(obj);


                            var answer = req.responseXML;
                            var a = answer.getElementsByTagName('ctl');
                            var html = answer.getElementsByTagName('code');
                            if (a.length === 0) {
                                var rec = req.responseText;
                                alert_box('erreur :' + rec);
                            }
                            var name_ctl = a[0].firstChild.nodeValue;
                            var code_html = getNodeText(html[0]);

                            code_html = unescape_xml(code_html);
                            remove_waiting_box();
                            g(obj.id).innerHTML = code_html;

                        }
                        catch (e) {
                            alert_box("show_ledger_callback" + e.message);
                        }
                        try {
                            code_html.evalScripts();
                        }
                        catch (e) {
                            alert_box("answer_box Impossible executer script de la reponse\n" + e.message);
                        }

                    }

                }
        );
    } catch (e) {
        alert_box('show_ledger_choice' + e.message);
    }
}
/**
 * hide the ledger choice
 */
function hide_ledger_choice(p_frm_search)
{
    try
    {
        var nb = $(p_frm_search).nb_jrn.value;
        var div = $(p_frm_search).div.value;
        var i = 0;
        var str = "";
        var name = "";
        var n_name = "";
        var sel = 0;
        for (i = 0; i < nb; i++) {
            n_name = div + "r_jrn[" + sel + "]";
            name = div + "r_jrn" + i;
            if ($(name).checked) {
                str += '<input type="hidden" id="' + n_name + '" name="' + n_name + '" value="' + $(name).value + '">';
                sel++;
            }
        }
        str += '<input type="hidden" name="' + div + 'nb_jrn" id="' + div + 'nb_jrn" value="' + sel + '">';
        $('ledger_id' + div).innerHTML = str;
        removeDiv(div + 'jrn_search');
        return false;
    } catch (e) {
        alert_box('hide_ledger_choice' + e.message);
        return false;
    }

}
/**
 * show the cat of ledger choice
 */
function show_cat_choice()
{
    g('div_cat').style.visibility = 'visible';
}
/**
 * hide the cat of ledger choice
 */
function hide_cat_choice()
{
    g('div_cat').style.visibility = 'hidden';
}
/**
 * add a row for the forecast item
 */
function for_add_row(tableid)
{
    style = 'class="input_text"';
    var mytable = g(tableid).tBodies[0];
    var nNumberRow = mytable.rows.length;
    var oRow = mytable.insertRow(nNumberRow);
    var rowToCopy = mytable.rows[1];
    var nNumberCell = rowToCopy.cells.length;
    var nb = g("nbrow");
    var oNewRow = mytable.insertRow(nNumberRow);
    for (var e = 0; e < nNumberCell; e++)
    {
        var newCell = oRow.insertCell(e);
        var tt = rowToCopy.cells[e].innerHTML;
        new_tt = tt.replace(/an_cat0/g, "an_cat" + nb.value);
        new_tt = new_tt.replace(/an_cat_acc0/g, "an_cat_acc" + nb.value);
        new_tt = new_tt.replace(/an_qc0/g, "an_qc" + nb.value);
        new_tt = new_tt.replace(/an_label0/g, "an_label" + nb.value);
        new_tt = new_tt.replace(/month0/g, "month" + nb.value);
        new_tt = new_tt.replace(/an_cat_amount0/g, "an_cat_amount" + nb.value);
        new_tt = new_tt.replace(/an_deb0/g, "an_deb" + nb.value);
        newCell.innerHTML = new_tt;
        new_tt.evalScripts();
    }
    $("an_cat_acc" + nb.value).value = "";
    $("an_qc" + nb.value).value = "";
    $("an_label" + nb.value).value = "";
    $("an_cat_amount" + nb.value).value = "0";
    nb.value++;
}
/**
 * toggle all the checkbox in a given form
 * @param form_id id of the form
 */
function toggle_checkbox(form_id)
{
    var form = g(form_id);
    for (var i = 0; i < form.length; i++)
    {
        var e = form.elements[i];
        if (e.type === 'checkbox')
        {
            if (e.checked === true)
            {
                e.checked = false;
            }
            else
            {
                e.checked = true;
            }
        }
    }
}
/**
 * select all the checkbox in a given form
 * @param form_id id of the form
 */
function select_checkbox(form_id)
{
    var form = $(form_id);
    for (var i = 0; i < form.length; i++)
    {
        var e = form.elements[i];
        if (e.type === 'checkbox')
        {
            e.checked = true;
        }
    }
}
/**
 * unselect all the checkbox in a given form
 * @param form_id id of the form
 */
function unselect_checkbox(form_id)
{
    var form = $(form_id);
    for (var i = 0; i < form.length; i++)
    {
        var e = form.elements[i];
        if (e.type === 'checkbox')
        {
            e.checked = false;
        }
    }
}
/**
 * show the calculator
 */
function show_calc()
{
    if (g('calc1'))
    {
        this.document.getElementById('inp').value = "";
        this.document.getElementById('inp').focus();
        return;
    }
    var sid = 'calc1';
    var shtml = '';
    shtml += '<div style="float:right;height:10px;display:block;margin-top:2px;margin-right:2px">	<a onclick="removeDiv(\'calc1\');" href="javascript:void(0)" id="close_div">Fermer</a></div>';
    shtml += '<div>   <h2 class="info">Calculatrice</h2></div>';
    shtml += '<form name="calc_line"  method="GET" onSubmit="cal();return false;" >Calculatrice simplifiée: écrivez simplement les opérations que vous voulez puis la touche retour. exemple : 1+2+3*(1/5) <input class="input_text" type="text" size="30" id="inp" name="calculator"> <input type="button" value="Efface tout" class="button" onClick="Clean();return false;" > <input type="button" class="button" value="Fermer" onClick="removeDiv(\'calc1\')" >';
    shtml += '</form><span id="result">  </span><br><span id="sub_total">  Taper une formule (ex 20*5.1) puis enter  </span><br><span id="listing"> </span>';

    var obj = {id: sid, html: shtml,
        drag: true, style: ''
    };
    add_div(obj);
    this.document.getElementById('inp').focus();
}
function display_periode(p_dossier, p_id)
{

    try
    {
        var queryString = "gDossier=" + p_dossier + "&op=input_per" + "&p_id=" + p_id;
        var popup = {'id': 'mod_periode', 'cssclass': 'inner_box', 'html': loading(), 'style': 'width:30em', 'drag': true};
        if (!$('mod_periode')) {
            add_div(popup);
        }
        var action = new Ajax.Request(
                "ajax_misc.php",
                {method: 'get',
                    parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: success_display_periode
                }
        );
        $('mod_periode').style.top = (posY - 70) + "px";
        $('mod_periode').style.left = (posX - 70) + "px";
    }
    catch (e)
    {
        alert_box("display_periode " + e.message);
    }

}
function success_display_periode(req)
{
    try
    {

        var answer = req.responseXML;
        var html = answer.getElementsByTagName('data');

        if (html.length === 0)
        {
            var rec = req.responseText;
            alert_box('erreur :' + rec);
        }

        var code_html = getNodeText(html[0]);
        code_html = unescape_xml(code_html);

        $('mod_periode').innerHTML = code_html;
    }
    catch (e)
    {
        alert_box("success_display_periode".e.message);
    }
    try
    {
        code_html.evalScripts();
    }
    catch (e)
    {
        alert_box("success_display_periode Impossible executer script de la reponse\n" + e.message);
    }

}
function save_periode(obj)
{
    try
    {
        var queryString = $(obj).serialize() + "&op=save_per";

        var action = new Ajax.Request(
                "ajax_misc.php",
                {method: 'post',
                    parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: success_display_periode
                }
        );

    }
    catch (e)
    {
        alert_box("display_periode " + e.message);
    }

    return false;
}
/**
 *@brief basic answer to ajax on success, it will fill the ctl with
 * the code. In that case, you need to create the object before the Ajax.Request
 *The difference with success box is that
 *@see add_div removeDiv success_box is that the width and height are not changed
 *@parameter ctl is the ID of the object containing the html (div, button...)
 *@parameter code is the html code, with it you fill the ctl element
 */
function fill_box(req)
{
    try {

        remove_waiting_box();

        var answer = req.responseXML;
        var a = answer.getElementsByTagName('ctl');
        var html = answer.getElementsByTagName('code');
        if (a.length === 0) {
            var rec = req.responseText;
            alert_box('erreur :' + rec);
        }
        var name_ctl = a[0].firstChild.nodeValue;
        var code_html = getNodeText(html[0]); // Firefox ne prend que les 4096 car.
        code_html = unescape_xml(code_html);
        $(name_ctl).innerHTML = code_html;
    }
    catch (e) {
        alert_box(e.message);
    }
    try {
        code_html.evalScripts();
    }
    catch (e) {
        alert_box("Impossible executer script de la reponse\n" + e.message);
    }


}
/**
 *display a popin to  let you modified a predefined operation
 *@param dossier_id
 *@param od_id from table op_predef
 */
function mod_predf_op(dossier_id, od_id)
{
    var target = "mod_predf_op";
    removeDiv(target);
    var sx = '20%';
    var sy = '10%';
    var str_style = "top:" + sx + ";left:" + sy + ";";

    var div = {id: target, cssclass: 'inner_box', style: str_style, html: loading(), drag: 1};

    add_div(div);

    var qs = "gDossier=" + dossier_id + '&op=mod_predf&id=' + od_id;

    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: qs,
                onFailure: null,
                onSuccess: fill_box
            }
    );

}

function save_predf_op(obj)
{
    waiting_box();
    var querystring = $(obj).serialize() + '&op=save_predf';
    // Create a ajax request to get all the person
    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'post',
                parameters: querystring,
                onFailure: null,
                onSuccess: refresh_window
            }
    );

    return false;
}

/**
 *ctl_concern is the widget to update
 *amount_id is either a html obj. or an amount and the field tiers if given
 * @param {type} dossier
 * @param {type} ctl_concern
 * @param {type} amount_id
 * @param {type} ledger
 * @param {type} p_id_target
 * @returns {undefined}
 */
function search_reconcile(dossier, ctl_concern, amount_id, ledger, p_id_target)
{
    var dossier = g('gDossier').value;
    if (amount_id === undefined)
    {
        amount_id = 0;
    }
    else if ($(amount_id))
    {
        if ($(amount_id).value)
        {
            amount_id = $(amount_id).value;
        }
        else if
                ($(amount_id).innerHTML) {
            amount_id = $(amount_id).innerHTML;
        }
    }

    var target = "search_op";
    removeDiv(target);
    var str_style = fixed_position(77, 99);
    str_style += ";width:92%;overflow:auto;";
    waiting_box();


    var target = {gDossier: dossier,
        ctlc: ctl_concern,
        op: 'search_op',
        ctl: target,
        ac: 'JSSEARCH',
        amount_id: amount_id,
        ledger: ledger,
        target: p_id_target};

    var qs = encodeJSON(target);

    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: qs,
                onFailure: null,
                onSuccess: function (req) {
                    remove_waiting_box();
                    var div = {id: 'search_op', cssclass: 'inner_box', style: str_style, drag: 1};
                    add_div(div);
                    $('search_op').innerHTML = req.responseText;
                    req.responseText.evalScripts();
                }
            }
    );
}
/**
 * search in a popin obj if the object form
 */
function search_operation(obj)
{
    try {
        var dossier = g('gDossier').value;
        waiting_box();
        var target = "search_op";
        var qs = Form.serialize('search_form_ajx') + "&op=search_op&ctl=search_op";
        var action = new Ajax.Request('ajax_misc.php',
                {
                    method: 'get',
                    parameters: qs,
                    onFailure: null,
                    onSuccess: function (req) {
                        remove_waiting_box();
                        $('search_op').innerHTML = req.responseText;
                        req.responseText.evalScripts();
                    }
                }
        );
    } catch (e)
    {
        remove_waiting_box();
        alert_box(e.message);
    }
}
/**
 * Update the field e_concerned, from class_iconcerned
 * Value is the field where to put the quick-code but only if one checkbox has been
 * selected
 * @param {type} obj
 * @returns {undefined}
 */
function set_reconcile(obj)
{

    try
    {
        var ctlc = obj.elements['ctlc'];
        if ( ! obj.elements['target']) return;
        var target = obj.elements['target'].value;
        for (var e = 0; e < obj.elements.length; e++)
        {

            var elmt = obj.elements[e];
            if (elmt.type === "checkbox")
            {
                if (elmt.checked === true)
                {
                    var str_name = elmt.name;
                    var nValue = str_name.replace("jr_concerned", "");
                    if ($(ctlc.value).value != '') {
                        $(ctlc.value).value += ',';

                    } else {
                        if (target != "" && $(target).value == "") {
                            $(target).value = elmt.value;
                        }
                    }
                    $(ctlc.value).value += nValue;
                }
            }
        }
        removeDiv('search_op');
    }
    catch (e)
    {
        alert_box(e.message)
    }
}
function remove_waiting_node()
{
    $('info_div').innerHTML = "";
    $('info_div').style.display = "none";
    
}
function remove_waiting_box()
{
    removeDiv('wait_box');
    remove_waiting_node();
}
/**
 * Show all the detail of a profile : Menu, Management, Repository and
 * let the user to modify it
 * @param {type} gDossier
 * @param {type} profile_id
 * @returns {undefined}
 */
function get_profile_detail(gDossier, profile_id)
{
    waiting_box();
    var qs = "op=display_profile&gDossier=" + gDossier + "&p_id=" + profile_id + "&ctl=detail_profile";
    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: qs,
                onFailure: null,
                onSuccess: function (req) {
                    remove_waiting_box();
                    $('list_profile').hide();
                    $('detail_profile').innerHTML = req.responseText;
                    req.responseText.evalScripts();
                    $('detail_profile').show();
                    if (profile_id != "-1")
                        profile_show('profile_gen_div');
                }
            }
    );
}
function get_profile_detail_success_obsolete(xml)
{
    remove_waiting_box();

}
/**
 * @brief compute the string to position a div in a fixed way
 * @return string
 */
function fixed_position(p_sx, p_sy)
{
    var sx = p_sx;
    var sy = calcy(p_sy);

    var str_style = "top:" + sy + "px;left:" + sx + "px;position:absolute";
    return str_style;

}
/**
 *@brief compute Y even if the windows has scrolled down or up
 *@return the correct Y position
 */
function calcy(p_sy)
{
    var sy = p_sy;
    if (window.scrollY)
    {
        sy = window.scrollY + p_sy;
    }
    else
    {
        sy = document.body.scrollTop + p_sy;
    }
    return sy;

}
/**
 * @brief display a box with the menu option
 * @param {type} gdossier
 * @param {type} pm_id
 * @returns {undefined}
 */
function mod_menu(gdossier, pm_id)
{
    waiting_box();
    removeDiv('divdm' + pm_id);
    var qs = "op=det_menu&gDossier=" + gdossier + "&pm_id=" + pm_id + "&ctl=divdm" + pm_id;
    var pos = fixed_position(50, 250);
    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: qs,
                onFailure: null,
                onSuccess: function (req) {
                    try {
                        remove_waiting_box();
                        add_div({id: "divdm" + pm_id, drag: 1, cssclass: "inner_box", style: pos});
                        $('divdm' + pm_id).innerHTML = req.responseText;
                    } catch (e) {
                        alert_box(e.message);
                    }
                }
            }
    );
}
/**
 * Display the submenu of a menu or a module, used in setting the menu
 * 
 * @param {type} p_dossier
 * @param {type} p_profile
 * @param {type} p_dep
 * @returns {undefined}
 */
function display_sub_menu(p_dossier,p_profile,p_dep,p_level)
{
    waiting_box();
    new Ajax.Request('ajax_misc.php',
    {
        method:'get',
        parameters : { op:'display_submenu',
                gDossier:p_dossier,
                dep:p_dep,
                p_profile:p_profile ,
                p_level : p_level
            },
        onSuccess : function (req) {
            try {
                remove_waiting_box();
                if ( $('menu_table').rows.length > p_level ) {
                    $('menu_table').rows[1].remove();
                }
                var new_row = document.createElement('TR');
                new_row.innerHTML = req.responseText;
                $('menu_table').appendChild(new_row);
            } catch (e) {
                alert_box(e.message);
            }
        }
    })
}
/**
 * in CFGPRO, ask to confirm before removing a submenu and its children
 * @param {type} p_dossier
 * @param {type} profile_menu_id
 * @returns {undefined}
 */
function remove_sub_menu(p_dossier,profile_menu_id)
{
    confirm_box(null,'Confirme ?', 
    function () {
        waiting_box();
        new Ajax.Request('ajax_misc.php',
        {                   
            method:'get',
            parameters: { op:'remove_submenu',gDossier:p_dossier,
            p_profile_menu_id:profile_menu_id},
            onSuccess:function (req) {
                try {
                    remove_waiting_box();
                    $('sub'+profile_menu_id).remove();
                     if ( $('menu_table').rows.length > 1 ) {
                          $('menu_table').rows[1].remove();
                     }
                    
                } catch(e)
                {
                    alert_box(e.message);
                }
            }
        }
       )
    });
       
}
/**
 * @brief add a menu to a profile, propose only the available menu
 * @param obj json object 
 *   - dossier  : , 
 *   - p_id : profile id , 
 *   - type : Type of menu are "pr" for Printing "me" for plain menu 
 *   - p_level : level of menu (0 -> module,1-> top menu, 2->submenu)
 *   - dep : the parent menu id  (pm_id)
 * 
 */
function add_menu(obj)
{
    var pdossier = obj.dossier;
    var p_id = obj.p_id;
    var p_type = obj.type;
    
    waiting_box();
    removeDiv('divdm' + p_id);
    var pos = fixed_position(250, 150)+";width:50%;";
    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters:  { op:'add_menu',
                            'gDossier':pdossier , 
                            'p_id' :p_id ,
                            'ctl' : 'divdm' + p_id ,
                            'type' : p_type,
                            'dep':obj.dep,
                            'p_level':obj.p_level},
                onFailure: null,
                onSuccess: function (req) {
                    try {
                        remove_waiting_box();
                        add_div({id: "divdm" + p_id, drag: 1, "cssclass": "inner_box", "style": pos});
                        $('divdm' + p_id).innerHTML = req.responseText;
                    } catch (e) {
                        alert_box(e.message);
                    }
                }
            }
    );
}
/**
 * @brief Display a box to enter data for adding a new plugin from
 * the CFGMENU
 * @param {type} p_dossier
 * @returns {undefined}
 */
function add_plugin(p_dossier)
{
    waiting_box();
    removeDiv('divplugin');
    var qs = "op=add_plugin&gDossier=" + p_dossier + "&ctl=divplugin";

    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: qs,
                onFailure: null,
                onSuccess: function (req) {
                    try {
                        remove_waiting_box();
                        var pos = fixed_position(250, 150) + ";width:30%";
                        add_div({id: "divplugin", drag: 1, cssclass: "inner_box", style: pos});
                        $('divplugin').innerHTML = req.responseText;
                    } catch (e) {
                        alert_box(e.message);
                    }
                }
            }
    );
}
/**
 * Modify a menu
 * @param {type} p_dossier
 * @param {type} me_code
 * @returns {undefined}
 */
function mod_plugin(p_dossier, me_code)
{
    waiting_box();
    removeDiv('divplugin');
    var qs = "op=mod_plugin&gDossier=" + p_dossier + "&ctl=divplugin&me_code=" + me_code;

    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: qs,
                onFailure: null,
                onSuccess: function (req) {
                    try {
                        remove_waiting_box();
                        var pos = fixed_position(250, 150) + ";width:30%";
                        add_div({id: "divplugin", drag: 1, cssclass: "inner_box", style: pos});
                        $('divplugin').innerHTML = req.responseText;

                    } catch (e) {
                        alert_box(e.message);
                    }
                }
            }
    );
}
function create_menu(p_dossier)
{
    waiting_box();
    removeDiv('divmenu');
    var qs = "op=create_menu&gDossier=" + p_dossier + "&ctl=divmenu";

    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: qs,
                onFailure: null,
                onSuccess: function (req) {
                    try {
                        remove_waiting_box();
                        var pos = fixed_position(250, 150) + ";width:30%";
                        add_div({
                            id: "divmenu",
                            drag: 1,
                            cssclass: "inner_box",
                            style: pos
                        });
                        $('divmenu').innerHTML = req.responseText;
                    } catch (e) {
                        alert_box(e.message);
                    }
                }
            }
    );
}
function modify_menu(p_dossier, me_code)
{
    waiting_box();
    removeDiv('divmenu');
    var qs = "op=modify_menu&gDossier=" + p_dossier + "&ctl=divmenu&me_code=" + me_code;

    var action = new Ajax.Request('ajax_misc.php',
            {
                method: 'get',
                parameters: qs,
                onFailure: null,
                onSuccess: function (req) {
                    try {
                        remove_waiting_box();
                        var pos = fixed_position(250, 150) + ";width:30%";
                        add_div({
                            id: "divmenu",
                            drag: 1,
                            cssclass: "inner_box",
                            style: pos
                        });
                        $('divmenu').innerHTML = req.responseText;

                    } catch (e) {
                        alert_box(e.message);
                    }
                }
            }
    );
}
function get_properties(obj)
{
    var a_array = [];
    var s_type = "[" + typeof obj + "]";
    for (var m in obj)
    {
        a_array.push(m);
    }
    alert_box(s_type + a_array.join(","));
}
/**
 * @brief add a line in the form for the report
 * @param p_dossier dossier id to connect
 */
function rapport_add_row(p_dossier)
{
    style = 'style="border: 1px solid blue;"';
    var table = $("rap1");
    var line = table.rows.length;

    var row = table.insertRow(line);
    // left cell
    var cellPos = row.insertCell(0);
    cellPos.innerHTML = '<input type="text" ' + style + ' size="3" id="pos' + line + '" name="pos' + line + '" value="' + line + '">';

    // right cell
    var cellName = row.insertCell(1);
    cellName.innerHTML = '<input type="text" ' + style + ' size="40" id="text' + line + '" name="text' + line + '">';

    // button + formula
    var cellbutton = row.insertCell(2);
    var but_html = table.rows[1].cells[2].innerHTML;
    but_html = but_html.replace(/form0/g, "form" + line);
    cellbutton.innerHTML = but_html;
    but_html.evalScripts();

    g('form' + line).value = '';
}
/**
 * Search an action in an inner box
 */
function search_action(dossier, ctl_concern)
{
    try
    {
        var dossier = g('gDossier').value;

        var target = "search_action_div";
        removeDiv(target);
        var str_style = fixed_position(77, 99);

        var div = {id: target, cssclass: 'inner_box', style: str_style, html: loading(), drag: 1};

        add_div(div);
        var target = {gDossier: dossier,
            ctlc: ctl_concern,
            op: 'search_action',
            ctl: target
        };

        var qs = encodeJSON(target);

        var action = new Ajax.Request('ajax_misc.php',
                {
                    method: 'get',
                    parameters: qs,
                    onFailure: null,
                    onSuccess: function (req) {
                        try {
                            remove_waiting_box();
                            $('search_action_div').innerHTML = req.responseText;
                            req.responseText.evalScripts();
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

function result_search_action(obj)
{
    try
    {
        var queryString = $(obj).serialize() + "&op=search_action";
        var action = new Ajax.Request(
                "ajax_misc.php",
                {method: 'get',
                    parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req) {
                        try {
                            remove_waiting_box();
                            $('search_action_div').innerHTML = req.responseText;
                            req.responseText.evalScripts();
                        } catch (e) {
                            alert_box(e.message);
                        }
                    }
                }
        )

    }
    catch (e)
    {
        alert_box("display_periode " + e.message);
    }

    return false;
}

function set_action_related(p_obj)
{

    try
    {
        var obj = $(p_obj);
        var ctlc = obj.elements['ctlc'];

        for (var e = 0; e < obj.elements.length; e++)
        {

            var elmt = obj.elements[e];
            if (elmt.type === "checkbox")
            {
                if (elmt.checked === true)
                {
                    var str_name = elmt.name;
                    var nValue = elmt.value;
                    if ($(ctlc.value).value != '') {
                        $(ctlc.value).value += ',';
                    }
                    $(ctlc.value).value += nValue;
                }
            }
        }
        removeDiv('search_action_div');
        return false;
    }
    catch (e)
    {
        alert_box(e.message);
        return false;
    }
}
/**
 *@brief change a document_modele
 */
function stock_repo_change(p_dossier, r_id)
{
    var queryString = "gDossier=" + p_dossier + "&op=mod_stock_repo" + "&r_id=" + r_id;
    var nTop = calcy(posY);
    var nLeft = "200px";
    var str_style = "top:" + nTop + "px;left:" + nLeft + ";height:auto";

    removeDiv('change_stock_repo_div');
    waiting_box();
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get', parameters: queryString,
                onFailure: ajax_misc_failure,
                onSuccess: function (req) {
                    remove_waiting_box();
                    add_div({id: 'change_stock_repo_div', style: str_style, cssclass: 'inner_box', drag: "1"});
                    $('change_stock_repo_div').innerHTML = req.responseText;

                }
            }
    );
}
function stock_inv_detail(p_dossier, p_id)
{
    var queryString = "gDossier=" + p_dossier + "&op=view_mod_stock" + "&c_id=" + p_id + "&ctl=view_mod_stock_div";
    var nTop = calcy(posY);
    var nLeft = "10%";
    var str_style = "top:" + nTop + "px;left:" + nLeft + ";width:80%;";

    removeDiv('view_mod_stock_div');
    waiting_box();
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get', parameters: queryString,
                onFailure: ajax_misc_failure,
                onSuccess: function (req) {
                    remove_waiting_box();
                    add_div({id: 'view_mod_stock_div', style: str_style, cssclass: 'inner_box', drag: "1"});
                    $('view_mod_stock_div').innerHTML = req.responseText;
                    req.responseText.evalScripts();
                }
            }
    );
}
function show_fin_chdate(obj_id)
{
    try
    {
        var ch = $(obj_id).options[$(obj_id).selectedIndex].value;
        if (ch == 2) {
            $('chdate_ext').hide();
            $('thdate').show();
        }
        if (ch == 1) {
            $('chdate_ext').show();
            $('thdate').hide();
        }
        var nb = $('nb_item').value;
        for (i = 0; i < nb; i++) {
            if ($('tdchdate' + i)) {
                if (ch == 2) {
                    $('tdchdate' + i).show();
                }
                if (ch == 1) {
                    $('tdchdate' + i).hide();

                }
            }
        }
    } catch (e) {
        alert_box(e.message);
    }
}
/**
 * tab menu for the profile parameter
 */
function profile_show(p_div)
{
    try {
        var div = ['profile_gen_div', 'profile_menu_div', 'profile_print_div', 'profile_gestion_div', 'profile_repo_div'];
        for (var r = 0; r < div.length; r++) {
            $(div[r]).hide();
        }
        $(p_div).show();
    } catch (e)
    {
        alert_box(e.message)
    }
}
function detail_category_show(p_div, p_dossier, p_id)
{
    $(p_div).show();
    waiting_box();
    $('detail_category_div').innerHTML = "";
    var queryString = "gDossier=" + p_dossier + "&id=" + p_id + "&op=fddetail";
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get', parameters: queryString,
                onFailure: ajax_misc_failure,
                onSuccess: function (req) {
                    remove_waiting_box();
                    $('list_cat_div').hide();
                    $('detail_category_div').innerHTML = req.responseText;
                    $('detail_category_div').show();
                    req.responseText.evalScripts();
                }
            }
    );
}
/**
 * @brief check if the parameter is a valid a valid date or not, returns true if it is valid otherwise
 * false
 * @parameter p_str_date the string of the date (format DD.MM.YYYY)
 */
function check_date(p_str_date)
{
    var format = /^\d{2}\.\d{2}\.\d{4}$/;
    if (!format.test(p_str_date)) {
        return false;
    }
    else {
        var date_temp = p_str_date.split('.');
        var nMonth = parseFloat(date_temp[1]) - 1;
        var ma_date = new Date(date_temp[2], nMonth, date_temp[0]);
        if (ma_date.getFullYear() == date_temp[2] && ma_date.getMonth() == nMonth && ma_date.getDate() == date_temp[0]) {
            return true;
        }
        else {
            return false;
        }
    }

}
/**
 * @brief get the string in the id and check if the date is valid
 * @parameter p_id_date is the id of the element to check
 * @return true if the date is valid
 * @see check_date
 */
function check_date_id(p_id_date)
{
    var str_date = $(p_id_date).value;
    return check_date(str_date);
}
/**
 *
 * @param ag_id to view
 * @param dossier is the folder
 * @param modify : show the modify button values : 0 for no 1 for yes
 */
function view_action(ag_id, dossier, modify)
{
    waiting_box();
    layer++;
    id = 'action' + layer;

    querystring = 'gDossier=' + dossier + '&op=vw_action&ag_id=' + ag_id + '&div=' + id + '&mod=' + modify;
    var action = new Ajax.Request(
            "ajax_misc.php",
            {
                method: 'get',
                parameters: querystring,
                onFailure: error_box,
                onSuccess: function (req) {
                    try {
                        remove_waiting_box();
                        var answer = req.responseXML;
                        var ctl = answer.getElementsByTagName('ctl');
                        if ( ctl.length == 0) {
                            throw 'ajax failed ctl view_action';
                        }
                        var ctl_txt=getNodeText(ctl[0]);
                        var html = answer.getElementsByTagName('code');
                        if (html.length === 0)
                        {
                            var rec = req.responseText;
                            throw 'ajax failed  html view_action';
                        }
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        var pos = fixed_position(0, 50) + ";width:90%;left:5%;";
                        add_div({
                            id: id,
                            drag: 1,
                            cssclass: "inner_box",
                            style: pos
                        });
                        $(id).innerHTML = code_html;
                        if ( ctl_txt == 'ok') { compute_all_ledger();}
                    } catch (e) {
                        alert_box('view_action' + e.message);
                    }
                }
            }
    );
}
/**
 * @brief filter quickly a table
 * @param  phrase : phrase to seach
 * @param  _id : id of the table
 * @param  colnr : string containing the column number where you're searching separated by a comma
 * @param start_row : first row (1 if you have table header)
 * @returns nothing
 * @see HtmlInput::filter_table
 */
function filter_table(phrase, _id, colnr, start_row) {
    $('info_div').innerHTML = "Un instant";
    $('info_div').style.display = "block";
    var words = $(phrase).value.toLowerCase();
    var table = document.getElementById(_id);

    // if colnr contains a comma then check several columns
    var aCol = new Array();
    if (colnr.indexOf(',') >= 0) {
        aCol = colnr.split(',');
    } else {
        aCol[0] = colnr;
    }
    var ele;
    var tot_found = 0;

    for (var r = start_row; r < table.rows.length; r++) {
        var found = 0;
        for (var col = 0; col < aCol.length; col++)
        {
            var idx = aCol[col];
            if (table.rows[r].cells[idx])
            {
                ele = table.rows[r].cells[idx].innerHTML.replace(/<[^>]+>/g, "");
                //var displayStyle = 'none';
                if (ele.toLowerCase().indexOf(words) >= 0) {
                    found = 1;
                }
            }

        }
        if (found === 1) {
            tot_found++;
            table.rows[r].style.display = '';
        } else {
            table.rows[r].style.display = 'none';
        }
        $('info_div').style.display = "none";
        $('info_div').innerHTML = "";
    }
    if (tot_found == 0) {
        if ($('info_' + _id)) {
            $('info_' + _id).innerHTML = " Aucun résultat ";
        }
    } else {
        if ($('info_' + _id)) {
            $('info_' + _id).innerHTML = "  ";
        }
    }
}
/**
 * @brief
 * Display the task late or for today in dashboard
 */
function display_task(p_id)
{
    new Draggable(p_id, {starteffect: function ()
        {
            new Effect.Highlight(obj.id, {scroll: window, queue: 'end'});
        }}
    );
    $(p_id).style.top = posY + 'px';
    $(p_id).style.left = "10%";
    $(p_id).style.width = "80%";
    $(p_id).style.display = 'block';

}
/**
 * @brief
 * Set a message in the info
 */
function info_message(p_message)
{
    $('info_div').innerHTML = p_message;
    $('info_div').style.display = "block";
}
/**
 * @brief hide the info box
 */
function info_hide()
{
    $('info_div').style.display = "none";
}
/**
 * Show the navigator in a internal window
 * @returns {undefined}
 */
function ask_navigator(p_dossier) {
    try {
        waiting_box();
        removeDiv('navi_div')
        var queryString = "gDossier=" + p_dossier + "&op=navigator";
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req) {
                        remove_waiting_box();
                        add_div({id: 'navi_div', style: 'top:2em;left:2em;width:90%', cssclass: 'inner_box'});
                        $('navi_div').innerHTML = req.responseText;
                        try
                        {
                            req.responseText.evalScripts();
                            sorttable.makeSortable($("navi_tb"));
                        }
                        catch (e)
                        {
                            alert_box("answer_box Impossible executer script de la reponse\n" + e.message);
                        }

                    }
                }
        );
    } catch (e) {
        info_message(e.getMessage);
    }

}
/**
 * @brief Display an internal windows to set the user's preference
 * 
 */
function set_preference(p_dossier) {
    try {
        waiting_box();
        removeDiv('preference_div')
        var queryString = "gDossier=" + p_dossier + "&op=preference";
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req) {
                        remove_waiting_box();
                        add_div({id: 'preference_div', drag: 1});
                        $('preference_div').innerHTML = req.responseText;
                        try
                        {
                            req.responseText.evalScripts();
                        }
                        catch (e)
                        {
                            alert_box("answer_box Impossible executer script de la reponse\n" + e.message);
                        }

                    }
                }
        );
    } catch (e) {
        info_message(e.getMessage);
    }

}
/**
 * @brief Display user's bookmark
 * 
 */
function show_bookmark(p_dossier) {
    try {
        waiting_box();
        removeDiv('bookmark_div');
        var param = window.location.search;
        param = param.gsub('?', '');
        var queryString = "gDossier=" + p_dossier + "&op=bookmark&" + param;
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req) {
                        remove_waiting_box();
                        add_div({id: 'bookmark_div', cssclass: 'inner_box', drag: 1});
                        $('bookmark_div').innerHTML = req.responseText;
                        try
                        {
                            req.responseText.evalScripts();
                        }
                        catch (e)
                        {
                            alert_box("answer_box Impossible executer script de la reponse\n" + e.message);
                        }

                    }
                }
        );
    } catch (e) {
        info_message(e.getMessage);
    }

}
/**
 * @brief save the bookmark
 */
function save_bookmark() {
    try {
        waiting_box();
        var queryString = "op=bookmark&" + $("bookmark_frm").serialize();
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req) {
                        remove_waiting_box();
                        // removeDiv('bookmark_div');
                        // 
                        $('bookmark_div').innerHTML = req.responseText;
                        try
                        {
                            req.responseText.evalScripts();
                        }
                        catch (e)
                        {
                            alert_box("answer_box Impossible executer script de la reponse\n" + e.message);
                        }

                    }
                }
        );
    } catch (e) {
        info_message(e.getMessage);
    }

}
/**
 * @brief remove selected bookmark
 */
function remove_bookmark() {
    try {
        waiting_box();
        var queryString = "op=bookmark&" + $("bookmark_del_frm").serialize();
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req) {
                        remove_waiting_box();
                        $('bookmark_div').innerHTML = req.responseText;
                        try
                        {
                            req.responseText.evalScripts();
                        }
                        catch (e)
                        {
                            alert_box("answer_box Impossible executer script de la reponse\n" + e.message);
                        }

                    }
                }
        );
    } catch (e) {
        error_message(e.getMessage);
    }

}
/**
 *@brief display the error message into the div error_content_div (included into error_div)
 *@param message message to display
 *@note there is no protection
 */
function error_message(message)
{
    $('error_content_div').innerHTML = message;
    $('error_div').style.visibility = 'visible';
}
/**
 * @brief show the detail of a tag and propose to save it
 */
function show_tag(p_dossier, p_ac, p_tag_id, p_post)
{
    try {
        waiting_box();
        var queryString = "op=tag_detail&tag=" + p_tag_id + "&gDossier=" + p_dossier + "&ac=" + p_ac + '&form=' + p_post;
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req) {
                        var answer = req.responseXML;
                        var html = answer.getElementsByTagName('code');
                        if (html.length === 0)
                        {
                            var rec = req.responseText;
                            alert_box('erreur :' + rec);
                        }
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        remove_waiting_box();
                        add_div({id: 'tag_div', cssclass: 'inner_box', drag: 1});
                        $('tag_div').innerHTML = code_html;
                        try
                        {
                            code_html.evalScripts();
                        }
                        catch (e)
                        {
                            alert_box("answer_box Impossible executer script de la reponse\n" + e.message);
                        }

                    }
                }
        );
    } catch (e) {
        error_message(e.getMessage);
    }
}

/** 
 * @brief save the modified tag
 */
function save_tag()
{
    try {
        waiting_box();
        var queryString = "op=tag_save&" + $("tag_detail_frm").serialize();
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get',
                    parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req, j) {
                        remove_waiting_box();
                        removeDiv('tag_div');
                    }
                }
        );
    } catch (e) {
        error_message(e.getMessage);
        return false;
    }
    return false;

}
/**
 * Show a list of tag which can be added to the current followup document
 * @param {type} p_dossier
 * @param {type} ag_id
 * @returns {undefined}
 */
function action_tag_select(p_dossier, ag_id)
{
    try {
        waiting_box();
        var queryString = "ag_id=" + ag_id + "&op=tag_list&gDossier=" + p_dossier;
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req, j) {
                        var answer = req.responseXML;
                        var html = answer.getElementsByTagName('code');
                        if (html.length === 0)
                        {
                            var rec = unescape_xml(req.responseText);
                            error_message('erreur :' + rec);
                        }
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        pos = fixed_position(35, 229);
                        add_div({id: 'tag_div', style: pos, cssclass: 'inner_box tag', drag: 1});

                        remove_waiting_box();
                        $('tag_div').innerHTML = code_html;
                    }
                }
        );
    } catch (e) {
        error_message(e.getMessage);
    }
}
/**
 * @brief Add the current tag to the current ag_id
 * @param {type} p_dossier
 * @param {type} ag_id
 * @returns {undefined}
 */
function action_tag_add(p_dossier, ag_id, t_id)
{
    try {
        waiting_box();
        var queryString = "t_id=" + t_id + "&ag_id=" + ag_id + "&op=tag_add&gDossier=" + p_dossier;
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req, j) {
                        var answer = req.responseXML;
                        var html = answer.getElementsByTagName('code');
                        if (html.length === 0)
                        {
                            var rec = unescape_xml(req.responseText);
                            error_message('erreur :' + rec);
                        }
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        remove_waiting_box();
                        $('action_tag_td').innerHTML = code_html;
                        removeDiv('tag_div');
                    }
                }
        );
    } catch (e) {
        error_message(e.getMessage);
    }
}
/**
 * @brief remove the current tag to the current ag_id
 * @param {type} p_dossier
 * @param {type} ag_id
 * @returns {undefined}
 */
function action_tag_remove(p_dossier, ag_id, t_id)
{
    if (confirm('Enlevez ce tags ?') === false)
        return;
    try {
        waiting_box();
        var queryString = "t_id=" + t_id + "&ag_id=" + ag_id + "&op=tag_remove&gDossier=" + p_dossier;
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req, j) {
                        var answer = req.responseXML;
                        var html = answer.getElementsByTagName('code');
                        if (html.length === 0)
                        {
                            var rec = unescape_xml(req.responseText);
                            error_message('erreur :' + rec);
                        }
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        remove_waiting_box();
                        $('action_tag_td').innerHTML = code_html;

                    }
                }
        );
    } catch (e) {
        error_message(e.getMessage);
    }
}


/**
 * Display a div with available tags, this div can update the cell
 * tag_choose_td
 * @param {type} p_dossier
 * @returns {undefined}
 */
function search_display_tag(p_dossier, p_prefix)
{
    try {
        waiting_box();
        var queryString = "op=search_display_tag&gDossier=" + p_dossier + "&pref=" + p_prefix;
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req, j) {
                        var answer = req.responseXML;
                        var html = answer.getElementsByTagName('code');
                        if (html.length === 0)
                        {
                            var rec = unescape_xml(req.responseText);
                            error_message('erreur :' + rec);
                        }
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        remove_waiting_box();
                        add_div({id: p_prefix + 'tag_div', style: '', cssclass: 'inner_box', drag: 1});
                        $(p_prefix + 'tag_div').style.top = posY - 80 + "px";
                        $(p_prefix + 'tag_div').style.left = posX - 200 + "px";
                        remove_waiting_box();
                        $(p_prefix + 'tag_div').innerHTML = code_html;

                    }
                }
        );
    } catch (e) {
        error_message(e.getMessage);
    }
}
/**
 * @brief Add the selected tag (p_tag_id) to the cell of tag_choose_td in the search screen
 * in the search screen
 * @param {type} p_dossier
 * @param {type} p_tag_id
 */
function search_add_tag(p_dossier, p_tag_id, p_prefix)
{
    try {
        var clear_button = 0;
        if (tag_choose === '' && p_prefix === 'search') {
            tag_choose = $(p_prefix + 'tag_choose_td').innerHTML;
            clear_button = 1;
        }
        waiting_box();
        var queryString = "op=search_add_tag&gDossier=" + p_dossier + "&id=" + p_tag_id + "&clear=" + clear_button + '&pref=' + p_prefix;
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req, j) {
                        var answer = req.responseXML;
                        var html = answer.getElementsByTagName('html');
                        if (html.length === 0)
                        {
                            var rec = unescape_xml(req.responseText);
                            error_message('erreur :' + rec);
                        }
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        remove_waiting_box();
                        $(p_prefix + 'tag_choose_td').innerHTML = $(p_prefix + 'tag_choose_td').innerHTML + code_html;
                        removeDiv(p_prefix + 'tag_div');
                    }
                }
        );
    } catch (e) {
        error_message(e.getMessage);
    }
}
/**
 * Clear the tags in the cell tag_choose_td of the search screen
 * @returns {undefined}
 */
function search_clear_tag(p_dossier, p_prefix)
{
    if (p_prefix != 'search') {
        $(p_prefix + 'tag_choose_td').innerHTML = "";
        return;
    }
    try {
        var queryString = "op=search_clear_tag&gDossier=" + p_dossier + "&pref=" + p_prefix;
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', parameters: queryString,
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req, j) {
                        var answer = req.responseXML;
                        var html = answer.getElementsByTagName('html');
                        if (html.length === 0)
                        {
                            var rec = unescape_xml(req.responseText);
                            error_message('erreur :' + rec);
                        }
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);
                        $(p_prefix + 'tag_choose_td').innerHTML = code_html;
                        tag_choose = "";
                    }
                }
        );
    } catch (e) {
        error_message(e.getMessage);
    }
}
function action_show_checkbox()
{
    var a = document.getElementsByName('ag_id_td');
    for (var i = 0; i < a.length; i++) {
        a[i].style.display = 'block';
    }
}
function action_hide_checkbox()
{
    var a = document.getElementsByName('ag_id_td');
    for (var i = 0; i < a.length; i++) {
        a[i].style.display = 'none';
    }
}
/**
 * 
 * @param {type} obj
 * object attribute : g
 *   - Dossier dossier_id, 
 *   - invalue DOM Element where you can find the periode to zoom
 *   - outdiv  ID of the target (DIV)
 *   
 */
function calendar_zoom(obj)
{
    try {
        waiting_box();
        var per_periode=null;
        var notitle=0;
        var from=0;
        if ( $(obj.invalue) ) { per_periode=$(obj.invalue).value;}
        if ( obj.notitle && obj.notitle==1 ) { notitle=1;}
        var action = new Ajax.Request(
                "ajax_misc.php",
                {
                    method: 'get', 
                    parameters: { "notitle":notitle,"op":'calendar_zoom','from':from,'gDossier':obj.gDossier,'in':per_periode ,'out' : obj.outdiv,'distype':obj.distype},
                    onFailure: ajax_misc_failure,
                    onSuccess: function (req, j) {
                        var answer = req.responseXML;
                        var html = answer.getElementsByTagName('html');
                        if (html.length === 0)
                        {
                            var rec = unescape_xml(req.responseText);
                            error_message('erreur :' + rec);
                        }
                        var code_html = getNodeText(html[0]);
                        code_html = unescape_xml(code_html);

                        // if the target doesn't exist 
                        // then create it
                        if (obj.outdiv === undefined) {
                            obj.outdiv = 'calendar_zoom_div';
                        }
                        if ($(obj.outdiv) == undefined) {
                            var str_style = fixed_position(0, 20);
                            add_div({id: obj.outdiv, style: 'margin-left:3%;width:94%;' + str_style, cssclass: "inner_box", drag: 1});
                        }
                        remove_waiting_box();
                        $(obj.outdiv).innerHTML = code_html;
                        $(obj.outdiv).show();
                    }
                }
        );
    } catch (e) {
        error_message('calendar_zoom ' + e.getMessage);
    }


}
/**
 * @brief add a line in the form for the stock
 */
function stock_add_row()
{
    try {
        style = 'class="input_text"';
        var mytable = g("stock_tb").tBodies[0];
        var ofirstRow = mytable.rows[1];
        var line = mytable.rows.length;
        var nCell = mytable.rows[1].cells.length;
        var row = mytable.insertRow(line);
        var nb = g("row");
        for (var e = 0; e < nCell; e++)
        {
            var newCell = row.insertCell(e);
            if (mytable.rows[1].cells[e].hasClassName('num')) {
                newCell.addClassName("num");
            }

            var tt = ofirstRow.cells[e].innerHTML;
            var new_tt = tt.replace(/sg_code0/g, "sg_code" + nb.value);
            new_tt = new_tt.replace(/sg_quantity0/g, "sg_quantity" + nb.value);
            new_tt = new_tt.replace(/label0/g, "label" + nb.value);
            newCell.innerHTML = new_tt;
            new_tt.evalScripts();
        }

        g("sg_code" + nb.value).innerHTML = '&nbsp;';
        g("sg_code" + nb.value).value = '';
        g("label" + nb.value).innerHTML = '';
        g("sg_quantity" + nb.value).value = '0';

        nb.value++;

        new_tt.evalScripts();
    } catch (e) {
        alert_box(e.message);
    }

}
function show_description(p_id)
{
    $('print_desc' + p_id).hide();
    $('input_desc' + p_id).show();

}
/**
 * Hightlight the row we select and restore previous one
 * @param {type} x
 * @returns {undefined}
 */
var old_class = null;
var old_select = null;

function select_cat(x)
{
    if (old_select != null)
    {
        $(old_select).className = old_class;
    }
    old_select = $('select_cat_row_' + x);
    old_class = old_select.className;
    $(old_select).className = "highlight";
    $('fd_id').value = x;
}
/**
 * Show the DIV and hide the other, the array of possible DIV are
 * in a_tabs, 
 * @param {array} a_tabs name of possible tabs
 * @param {strng} p_display_tab tab to display
 */
function show_tabs(a_tabs, p_display_tab)
{
    try
    {
        if (a_tabs.length == 0)
            trow('a_tabs in empty');
        var i = 0;
        for (i = 0; i < a_tabs.length; i++) {
            $(a_tabs[i]).hide();
        }
        $(p_display_tab).show();
    } catch (e) {
        alert_box(e.message);
    }

}
/**
 * Change the class of all the "LI" element of a UL or OL
 * @param node of ul (this)
 */
function unselect_other_tab(p_tab)
{
    try {
        var other = p_tab.getElementsByTagName("li");
        var i = 0;
        var tab = null;
        for (i = 0; i < other.length; i++) {
            tab = other[i];
            tab.className = "tabs";
        }
    } catch (e) {
        if (console)
            console.log(e.message);
    }
}
/**
 * logout function call from ajax
 * @see ajax_disconnected
 * @returns {undefined}
 */
function logout()
{
    var tmp_place = window.location.href
    var tmp_b = tmp_place.split('/')
    var tmp_last = tmp_b.length - 1
    var place_logout = tmp_place.replace(tmp_b[tmp_last], 'logout.php');
    window.location.href = place_logout;
}
/**
 * Create a div which can be used in a anchor
 * @returns {undefined}
 */
function create_anchor_up()
{
    if ( $('up_top')) return;
    
    var newElt = new Element('div');
    newElt.setAttribute('id', 'up_top');
    newElt.innerHTML='<a id="up_top"></a>';
    
    var parent = $('info_div').parentNode;
    parent.insertBefore(newElt, $('info_div'));
    
}
/**
 * Initialize the window to show the button "UP" if the window is scrolled
 * vertically
 * @returns {undefined}
 */
function init_scroll()
{
    var up=new Element('div',{"class":"inner_box",
            "style":"padding:10px;left:auto;width:30px;height: auto;display:none;position:fixed;top:25px;right:20px;text-align:center",
            id:"go_up"
        });
        up.innerHTML=' <a class="button" href="#up_top" >&#8679;</a>';
        document.body.appendChild(up);
         window.onscroll=function () {
         if ( document.viewport.getScrollOffsets().top> 0) {
             if ($('go_up').visible() == false) {
                $('go_up').setOpacity(0.85); 
                $('go_up').show();
            }
        } else {
            $('go_up').hide();
        }
     }
}
/**
 * Confirm a form thanks a modal dialog Box, it returns true if we agree otherwise
 * false
 * @code
<form onsubmit="return confirm_box(this,'message')">
</form>
 * @endcode
 * @param p_obj form element (object) or element id (string)
 * @param p_message message to display
 * @returns true or false
 */
function confirm_box(p_obj, p_message,p_callback_true)
{
    waiting_box();
    try {
        // Find id of the end
        var name="";
        if ( p_obj != null )
        {
            if ( typeof (p_obj) === "object") {
                name=p_obj.id;
            } else {
                name=p_obj;
            }
        }
       
       // execute the callback function or submit the form
       if ( p_callback_true == undefined || p_callback_true==null)
        {
            smoke.confirm(p_message,function (e) {
                if ( e ) {
                    $(name).submit();
                }
            });
        } else {
            smoke.confirm(p_message,function (e) 
            {
                if ( e ) { p_callback_true.apply();}
            });
        }
    } catch (e) {
        alert_box(e.getMessage);
    }
    remove_waiting_box();
    return false;
}
/**
 * Alert box in CSS and HTML to replace the common javascript alert
 * @param p_message message to display
 * @returns void
 */
function alert_box(p_message)
{
    smoke.alert(p_message,false , {ok:'ok',classname:"inner_box"});
}

/**
 * All the onload must be here otherwise the other will overwritten
 * @returns {undefined}
 */
window.onload=function ()
{
    create_anchor_up();
    init_scroll();
    sorttable.init
}
