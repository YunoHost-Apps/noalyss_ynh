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
/*!\file
 *\brief common function for "Ecriture directe"
 */
function checkTotalDirect()
{
    var ie4=false;
    if ( document.all )
    {
        ie4=true;
    }// Ajouter getElementById par document.all[str]
    var total_deb=0.0;
    var total_cred=0.0;


    var nb_item_id=document.getElementById('nb_item');
    if ( ! nb_item_id )
    {
        return;
    }

    var nb_item=nb_item_id.value;

    for (var i=0;i <nb_item ;i++)
    {
        var doc_amount=document.getElementById("amount"+i);
        if ( ! doc_amount )
        {
            return;
        }
        var side=document.getElementsByName("ck"+i);
        if ( ! side )
        {
            return;
        }
        var amount=parseFloat(doc_amount.value);

        if ( isNaN(amount) == true)
        {
            amount=0.0;
        }
        for (var e=0;e<side.length;e++)
        {
            if ( side[e].checked == true)
            {
                //	alert_box('checked !!! '+side[e].value);
                total_deb+=amount;
            }
            else
            {
                total_cred+=amount;
            }
        }

        //    alert_box("amount ="+i+"="+amount+" cred/deb = "+deb+"total d/b"+total_deb+"/"+total_cred);
    }



    r_total_cred=Math.round(total_cred*100)/100;
    r_total_deb=Math.round(total_deb*100)/100;
    document.getElementById('totalDeb').innerHTML=r_total_deb;
    document.getElementById('totalCred').innerHTML=r_total_cred;
    var diff=0;
    if ( r_total_deb != r_total_cred )
    {
        document.getElementById("totalDiff").style.color="red";
        document.getElementById("totalDiff").style.fontWeight="bold";
        document.getElementById("totalDiff").innerHTML="Différence";
        diff=total_deb-total_cred;
        diff=Math.round(diff*100)/100;
        document.getElementById("totalDiff").innerHTML=diff;
        return false;

    }
    else
    {
        document.getElementById("totalDiff").innerHTML="0.0";
        return true;
    }
}
function controleBalance()
{
    if ( checkTotalDirect() == true ) { return true;}
    else {
        smoke.alert('Balance incorrecte D/C');
        return false;
    }
}