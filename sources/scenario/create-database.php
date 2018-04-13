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

$_POST['gDossier']=$gDossierLogInput;
$_GET['gDossier']=$gDossierLogInput;
$_REQUEST=array_merge($_GET,$_POST);

/**
 * @file
 * @brief 
 * @param type $name Descriptionara
 */
$cn=new Database();

$cn->exec_sql("create database ".domaine."dossier1 encoding='utf8'");

$cn=new Database(1, 'dos');
$cn->execute_script(NOALYSS_INCLUDE.'/sql/mod1/schema.sql');
$cn->execute_script(NOALYSS_INCLUDE.'/sql/mod1/data.sql');
$cn->execute_script(NOALYSS_INCLUDE.'/sql/mod1/constraint.sql');
