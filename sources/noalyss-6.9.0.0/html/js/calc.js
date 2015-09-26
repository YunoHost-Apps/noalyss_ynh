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

/*! \file
 * \brief This file show a little online calculator, in the caller
 *        the span id result, listing, the id form calc_line and the
 *       
 *
 */
var p_history="";
var p_variable="";
// add input
function cal()
{
    p_variable=this.document.getElementById('inp').value;
    if (p_variable.search(/^\s*$/) !=-1)
    {
        return;
    }
    try
    {
        Compute();
	p_variable=p_variable.replace(/ /g,"");
	p_variable=p_variable.replace(/\+/g,"+ ");
	p_variable=p_variable.replace(/-/g,"- ");
	p_variable=p_variable.replace(/\//g,"/ ");

        sub=eval(p_variable);
        var result=parseFloat(sub);
        result=Math.round(result*100)/100;
    }
    catch(exception)
    {
        alert_box("Mauvaise formule\n"+p_variable);
        return false;
    }
    p_history=p_history+'<hr>'+p_variable;
    p_history+="="+result.toString();
    var str_sub="<hr><p> Total :"+p_variable+" = "+result.toString()+"</p>";
    this.document.getElementById("sub_total").innerHTML=str_sub;
    this.document.getElementById("listing").innerHTML=p_history;
    this.document.getElementById('inp').value="";
}
// Clean
//
function Clean()
{
    this.document.getElementById('listing').innerHTML="";
    this.document.getElementById('result').innerHTML="";
    this.document.getElementById('sub_total').innerHTML="";
    this.document.getElementById('inp').value="";
    this.document.getElementById('inp').focus();

}

function Compute()
{
    var tot=0;
    var ret="";

    this.document.getElementById('inp').value="";
    this.document.getElementById('inp').focus();
}
