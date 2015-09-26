/* 
 * Copyright (C) 2015 Dany De Bontridder <dany@alchimerys.be>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * Display the forbidden folders if the request comes from a form
 * with an input text (id:database_filter_input) then this text is 
 * used as a filter
 * @param {type} p_user : the user id
 * @returns nothing
 */
function folder_display(p_user)
{
    /**
     * If form exist and there is something
     * 
     */
    var p_filter = "";
    if ($('database_filter_input')) {
        console.log($('database_filter_input').value);
        p_filter = $('database_filter_input').value;
    }
    /*
     * Ajax request to display the folder
     */
    new Ajax.Request('ajax_misc.php', {
        method: "get",
        parameters: {"p_user": p_user, "op": "folder_display", "p_filter": p_filter, 'gDossier': 0},
        onSuccess: function (p_xml) {
            // table id = database_list
            var folder = {};
            var create = false;
            if (!$('folder_list_div')) {
                folder = create_div({'id': 'folder_list_div', 'cssclass': "inner_box", 'style': 'width:90%,right:5%;top:100px'});
                create = true;
            }
            folder = $('folder_list_div');
            // Analyze XML answer 
            var answer = p_xml.responseXML;
            var a = answer.getElementsByTagName('status');
            var html = answer.getElementsByTagName('content');
            if (a.length == 0) {
                var rec = req.responseText;
                alert_box('erreur :' + rec);
            }

            var content = getNodeText(html[0]);
            // fill up the div
            folder.innerHTML = unescape_xml(content);

            // show it
            folder.show();
            $('database_filter_input').focus();
        }
    });
}
/**
 * Remove the grant for an user to the given database id
 * @param {integer} p_user use_id  id of the user
 * @param {integer} p_dossier id of the database
 * @returns nothing
 */
function folder_remove(p_user,p_dossier )
{
    smoke.confirm ('Confirmer',
    function (e) {
        if (e ) {
            waiting_box();
            new Ajax.Request('ajax_misc.php', {
                method: "get",
                parameters: {"p_user": p_user, 'p_dossier': p_dossier, "op": "folder_remove", 'gDossier': 0},
                onSuccess: function (p_xml) {
                    // table id = database_list
                    new Effect.Opacity('row'+p_dossier, { from: 1.0, to: 0.0, duration: 0.2 });
                    remove_waiting_box();
                }
            
        });
    } else {
        return ;
    }
    });
}

/**
 * Grant the access to a folder for a given user and add a row in the table 
 * (id : database_list)
 * @param {integer} p_user use_id  id of the user
 * @param {integer} p_dossier id of the database
 * @returns {undefined}
 */
function folder_add(p_user, p_dossier)
{
    waiting_box();
    new Ajax.Request('ajax_misc.php', {
        method: "get",
        parameters: {"p_user": p_user, 'p_dossier': p_dossier, "op": "folder_add", 'gDossier': 0},
        onSuccess: function (p_xml) {
            // table id = database_list
            // Analyze XML answer 
            var answer = p_xml.responseXML;
            var a = answer.getElementsByTagName('status');
            var html = answer.getElementsByTagName('content');
            if (a.length == 0) {
                var rec = req.responseText;
                alert_box('erreur :' + rec);
            }

            var content = getNodeText(html[0]);
            var nb = $('database_list').rows.length + 1;
            var row = new Element('tr', {'id': 'row' + p_dossier});
            if (nb % 2 == 0) {
                row.addClassName('odd');
            } else {
                row.addClassName('even');
            }
            row.innerHTML = unescape_xml(content);
            $('database_list').appendChild(row);
            $('row_db_'+p_dossier).hide();
            remove_waiting_box();
        }
    });

}
function display_admin_answer(p_dossier,p_action)
{
     waiting_box();
    new Ajax.Request ("ajax_misc.php",{
        method:"get",
        parameters:{"p_dossier":p_dossier,"op":p_action,'gDossier':0},
        onSuccess : function (p_xml) {
            try {
            var div_display="folder_admin_div";
            var answer = p_xml.responseXML;
            var a = answer.getElementsByTagName('status');
            var html = answer.getElementsByTagName('content');
            if (a.length == 0) {
                var rec = req.responseText;
                alert_box('erreur :' + rec);
            }
            
            var folder;
            var create = false;
            if (!$(div_display)) {
                folder = create_div({'id': div_display, 'cssclass': "inner_box", style: 'width:90%;right:5%;top:100px'});
                create = true;
            }
            folder=$(div_display);

            var content = getNodeText(html[0]);
            folder.innerHTML=unescape_xml(content);
            var pos=calcy(250);
            $(div_display).setStyle({top:pos+'px'});
            
            folder.show();
            remove_waiting_box();
        } catch (e) {
            console.log(e.message);
        }
        }
    });
}
function folder_drop(p_dossier)
{
   display_admin_answer(p_dossier,'folder_drop');
}

function folder_modify(p_dossier)
{
   display_admin_answer(p_dossier,'folder_modify'); 
}
function modele_modify(p_dossier)
{
   display_admin_answer(p_dossier,'modele_modify'); 
}
function modele_drop(p_dossier)
{
   display_admin_answer(p_dossier,'modele_drop'); 
}
