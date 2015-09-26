* 
 * Copyright (C) 2014 Dany De Bontridder <dany@alchimerys.be>
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

Afin d'avoir les scénarios pour les rejouer avec test.php.

Dans include/constant.php, changer 
define ('LOGINPUT',false); par
define ('LOGINPUT',true); 

Ensuite, aller dans votre dossier de test et faites une action (une vente, un achat...), vous devez ouvrir le fichier test.php
ce que vous venez de faire a été sauvé dans le répertoire défini par $_ENV['TMP'], (sous linux il s'agit de /tmp )avec un nom ressemblant à scenario-<nombre>.php
    
Vous devez d'abord copier ce fichier dans ce répertoire-ci

Si vous pointez votre browser sur noalyss/html/test.php (après avoir créé le fichier authorized_debug) en cliquant sur le 
lien avec le nom de fichier vous pourrez rejouer l'action. Vous pouvez améliorer la description en changeant l'annotation //@description: <CODE> 

Vous pouvez aussi utiliser un nom de fichier plus parlant.    
    
L'objectif étant de pouvoir tester et de rejouer facilement les actions que vous avez faites.


