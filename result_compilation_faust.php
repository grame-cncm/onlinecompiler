<?php

/*! \file resulta_compilation_faust.php
  \brief This file build page that displays the result of the Faust compilation
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
// require "inc/mess.inc";
require "php/make_element.php";

//create a new session folder if it doesn't exist
if($_SESSION['id'] == "") $_SESSION['id'] = session_id();
$_SESSION['path'] = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id'];
system("scripts/new_session ".$_SESSION['path'], $ret);
if ($ret != 0) {erreur("result_compilation_faust.php: Unable to start a new session. Please, try later."); return 1;}

//download the source package id it was created
if (isset($_POST['submit'])) {
  if ($_POST['submit'] == "Get the source package") {
    getpackage($fichier, $_SESSION['enrobagemenu']);
    exit;
  }
}

//global variable to set the position of the frame in the naviagtion menu
$_SESSION['goto'] = "faust";
$_SESSION['orig_editor'] = 2;

//get the html code
if ($_SESSION['htmlCode'] == "" ) $_SESSION['htmlCode'] = read_file ("compiler.html");
$html = $_SESSION['htmlCode'];
$resultat = get_section($html, "resultat");

//the html page is filled with the result of the Faust compilation
if ($_SESSION['resultat_faust'] == 1) {
  if($_SESSION['langMenu'] == "java"){
    $assoc['__resultat__'] = $_SESSION['codeJava'];
    $assoc['__mode__'] = "\"text/x-java\"";
    $dis = "disableOthers";
  }
  elseif($_SESSION['langMenu'] == "wast"){
    $assoc['__resultat__'] = $_SESSION['codeJs'];
    $assoc['__mode__'] = "\"application/wasm\"";
    $dis = "disableOthers";
  }
  elseif($_SESSION['langMenu'] == "c"){
    $assoc['__resultat__'] = $_SESSION['codeC'];
    $assoc['__mode__'] = "\"text/x-csrc\"";
    $dis = "disableOthers";
  }
  elseif($_SESSION['langMenu'] == "llvm"){
    $assoc['__resultat__'] = $_SESSION['codeLLVM'];
    $assoc['__mode__'] = "\"text/x-llvm\"";
    $dis = "disableOthers";
  }
  else{
    $assoc['__mode__'] = "\"text/x-c++src\"";
    $assoc['__resultat__'] = $_SESSION['code_C_h'];
    $dis = "";
  }
  /*
  if ($_SESSION['enrobagemenu'] != "none") {
    $exec = get_section ($html, "package");
  }
  */
}
else {
  if($_SESSION['code_faust'] == "Enter Faust code here") $assoc['__resultat__'] = "Please, enter some Faust code in the \"Faust Code\" tab or drag a Faust file in the area above.";
  else $assoc['__resultat__'] = $_SESSION['erreur_faust'];
  $assoc['__mode__'] = "\"text/x-faust\"";
}

$typeresultat = fill_template($typeresultat, $assoc);
$resultat = fill_template($resultat, $assoc);

//the html page is displayed
display_header($html);
// display_catalog($html,"goto_codeC.php");
display_dropFile($html,"goto_codeC2()");
display_navigation($html,1);
display_options($html,"resultat_compilation_faust.php","goto_codeC2()",$dis);
//print $exec;
print $resultat;
//print $_SESSION['codeJava'];
display_footer($html);

?>
