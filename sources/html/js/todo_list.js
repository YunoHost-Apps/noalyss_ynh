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
 * \brief this file contains all the javascript needed by the todo_list. 
 *      it requires prototype.js. The calling page must have 
 *      the gDossier
 * 
 */
function todo_list_show(p_id)
{
    waiting_node();
    /*
     * create a div id based on p_id
     */
    

    try
    {
         var gDossier = $('gDossier').value;
        var action = new Ajax.Request(
                'ajax_todo_list.php',
                {
                    method: 'get',
                    parameters:
                            {'show':
                                        1, 'id':
                                        p_id, 'gDossier':
                                        gDossier
                            },
                    onFailure: todo_list_show_error,
                    onSuccess: function (req)
                    {
                        try
                        {
                            var todo_div=create_div({id:'todo_list_div'+p_id,cssclass:'add_todo_list',drag:1});
                           


                            todo_div.style.top = (posY + offsetY) + 'px';
                            todo_div.style.left = (posX + offsetX - 200) + 'px';

                            var answer = req.responseXML;
                            var tl_id = answer.getElementsByTagName('tl_id');
                            var tl_content = answer.getElementsByTagName('tl_content');

                            if (tl_id.length == 0)
                            {
                                var rec = req.responseText;
                                alert_box('erreur :' + rec);
                            }
                            var content = unescape_xml(getNodeText(tl_content[0]));
                            todo_div.innerHTML=content;
                            
                            remove_waiting_node();
                            content.evalScripts();
                            Effect.SlideDown(todo_div, {duration: 0.1, direction: 'top-left'})
                        }
                        catch (e)
                        {
                            alert_box(e.message);
                        }
                    }
                }
        );
    }
    catch (e)
    {
        alert_box(" Envoi ajax non possible" + e.message);
    }
    return false;
}
function todo_list_show_error(request_json)
{
    alert_box('failure');
}
function add_todo()
{
    todo_list_show(0);
}
function todo_list_remove(p_ctl)
{
    smoke.confirm('Effacer ?',
    function (e) {
        if ( !e ) {return;}
        $("tr" + p_ctl).hide();
        var gDossier = $('gDossier').value;

        var action = new Ajax.Request(
                'ajax_todo_list.php',
                {
                    method: 'get',
                    parameters:{'del':1, 'id':p_ctl, 'gDossier':gDossier}
                }
        );
        return false;
    });
}
function todo_list_save(p_form)
{
    try {
    var form=$('todo_form_'+p_form);
    var json=form.serialize(true);
    new Ajax.Request('ajax_todo_list.php',
                    {
                        method:'get',
                       parameters:json,
                       onSuccess:function (req) {
                           // On success : reload the correct row and close 
                           // the box
                           var answer = req.responseXML;
                            var tl_id = answer.getElementsByTagName('tl_id');
                            var content = answer.getElementsByTagName('row');
                            var style  =  answer.getElementsByTagName('style');

                            if (tl_id.length == 0)
                            {
                                var rec = req.responseText;
                                alert_box('erreur :' + rec);
                            }
                            var tr = $('tr'+p_form);
                            if ( p_form == 0) 
                            {
                                tr=document.createElement('tr');
                                tr.id='tr'+getNodeText(tl_id[0]);
                                $('table_todo').appendChild(tr);
                            }
                            var html=getNodeText(content[0]);
                            tr.innerHTML=unescape_xml(html);
                            $w(tr.className).each ( function(p_class) { tr.removeClassName(p_class); } );
                            tr.addClassName(getNodeText(style[0]));
                            // remove the user list if exists
                            if ( $('shared_'+p_form)) {$('shared_'+p_form).remove();}
                           Effect.Fold('todo_list_div'+p_form,{duration:0.1});
                       }
                    }
                    );
        }
        catch (e) {
            console.log(e.message);
            return false;
        }
        return false;
}

/**
 * @brief toggle the zoom of the note
 */
var todo_maximize=false;
/**
 * @brief maximize or minimize the todo  list from the
 * dashboard.
 * @returns {undefined}
 */
function zoom_todo ()
{
    waiting_box();
    if ( ! todo_maximize)
    {
        var clonetodo=$('todo_listg_div').clone();
        clonetodo.setAttribute('id','clone_todo_list')
        clonetodo.setStyle({'z-index':1,'position':'absolute','width':'95%','height':'95%','top':'2%','right':'2%','left':'2%'})
        clonetodo.innerHTML=$('todo_listg_div').innerHTML;
        $('todo_listg_div').innerHTML="";
        clonetodo.addClassName('inner_box');
        clonetodo.removeClassName('box');
        document.body.appendChild(clonetodo);
        todo_maximize=true;
    } else
    {
        todo_maximize=false;
         $('todo_listg_div').innerHTML=$('clone_todo_list').innerHTML;
        $('clone_todo_list').remove();
    }
    
  sorttable.makeSortable(document.getElementById('table_todo'));
  remove_waiting_box();
}
function todo_list_share(p_note, p_dossier)
{
    waiting_node();
    new Ajax.Request(
            'ajax_todo_list.php',
            {
                method: "get",
                parameters: {"act": 'shared_note',
                    "todo_id": p_note,
                    "gDossier": p_dossier
                },
                onSuccess: function (p_xml) {
                    try {
                        /**
                         * Show the little div to add other user
                         * or a error message if it is forbidden
                         */
                        remove_waiting_node();
                        var answer = p_xml.responseXML;
                        var content = answer.getElementsByTagName('content');
                        if (content.length == 0) {
                            return;
                        }
                        var html_content=unescape_xml(getNodeText(content[0]));
                        var shared_note = "shared_" + p_note;
                        create_div({"id": shared_note, "cssclass": "inner_box",drag:1});
                        $("shared_" + p_note).setStyle( { top : posY + offsetY+"px",left:posX+offsetY+"px","width":"25%"});
                        $("shared_" + p_note).hide();
                        $(shared_note).innerHTML = html_content;
                        $(shared_note).show();

                    } catch (e) {
                        alert_box(e.message);
                    }
                }
            }
    );

}
function todo_list_set_share(note_id,p_login,p_dossier)
{
    waiting_node();
    new Ajax.Request('ajax_todo_list.php',
            {
                method:"get",
                parameters: { todo_id:note_id,act:"set_share","gDossier":p_dossier,"login":p_login},
                onSuccess:function() {
                    remove_waiting_node();
                }
            }
    )
}
function todo_list_remove_share(note_id,p_login,p_dossier)
{
    waiting_node();
    new Ajax.Request('ajax_todo_list.php',{
        parameters : {
            'gDossier':p_dossier,
            'todo_id':note_id,
            'login':p_login,
            'act':"remove_share"
        },
        method:"get",
        onSuccess:function (p_xml) {
            remove_waiting_node();
            var answer=p_xml.responseXML;
            var status=answer.getElementsByTagName('status');
            if ( status.length == 0) {
                alert_box ('erreur reponse ');
            }
            var status_code=getNodeText(status[0]);
            if ( status_code == 'ok') {
                $("tr" + note_id).hide();
                
            }
        }
    });
}