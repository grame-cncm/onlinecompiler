<?php

/*! \file result_compilation_C.php
\brief This file build the page displaying the results of the gcc compilation
\author Romain Michon - Damien Cramet

Copyright (C) 2003-2011 GRAME, Centre National de Creation Musicale
---------------------------------------------------------------------
This file is free software; you can redistribute it
and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3 of
the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; If not, see <http://www.gnu.org/licenses/>.

EXCEPTION : As a special exception, you may create a larger work
that contains this FAUST architecture section and distribute
that work under terms of your choice, so long as this FAUST
architecture section is not modified.
*/

//session is started
if (session_id()=="") session_start();

require("php/env.php");
require "php/functions.php";
require "php/form.php";
require "php/getfile.php";
require "inc/mess.inc";
require "php/make_element.php";

//create a new session folder if it doesn't exist
if($_SESSION['id'] == "") $_SESSION['id'] = session_id();
$_SESSION['path'] = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id'];
system("scripts/new_session ".$_SESSION['path'], $ret);
if ($ret != 0) {erreur("result_compilation_C.php: Unable to start a new session. Please, try later."); return 1;}

//global variable to set the position of the frame on the navigation bar
$_SESSION['goto'] = "exec";
$_SESSION['orig_editor'] = 2;

//download button
if ($_POST['submit'] == "Download the executable file") {
    getInWorkdir($_SESSION['exec_file']);
    exit;
}

//get the code of compiler.html
if ($_SESSION['htmlCode'] == "" ) $_SESSION['htmlCode'] = read_file ("compiler.html");
$html = $_SESSION['htmlCode'];
$resultat = get_section($html, "resultatC");

//the html page is filled
if($_SESSION['enrobagemenu'] == "none" || $_SESSION['enrobagemenu'] == ""){
    $assoc['__resultat__'] = "Please, select an architecture in the architeture menu.";
    if($_SESSION['code_faust'] == "Enter Faust code here") $assoc['__resultat__'] = $assoc['__resultat__']."<br>Please, enter some Faust code in the \"Faust Code\" tab or drag a Faust file in the area above.";
}else{
    $assoc['__resultat__'] =  $_SESSION['reponse_g++'];
}

if ($_SESSION['resultat_C'] == 1){
    $exec = get_section ($html, "exec");
}
$resultat = fill_template($resultat, $assoc);

//the html page is displayed
display_header($html);
// display_catalog($html,"goto_exec.php");
display_dropFile($html,"goto_exec2()");
display_navigation($html,1);
display_options($html,"result_compilation_C.php","goto_exec2()","disableLang");
print $exec;
print $resultat ;
//echo "voila : ".$_SESSION['OSCselect'];
display_footer($html);

?>
