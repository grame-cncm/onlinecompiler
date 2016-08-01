<?php

/*! \file compiler.php
  \brief This file build the "Faust Code" tab.
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

error_reporting(E_ALL);

//if no session is found, a new one is started
if (session_id()=="") session_start();

require("php/env.php");
require "php/functions.php";
require "php/make_element.php";

//session variables are updated
$_SESSION['orig_editor'] = 1;
$_SESSION['goto'] = "faustCode";

//creating the session folder on the server id it doesn't already exists
$_SESSION['id'] = session_id();
$_SESSION['path'] = "$_SERVER['DOCUMENT_ROOT']/onlinecompiler/tmp/$_SESSION['id']";
system("scripts/new_session ".$_SESSION['path'], $ret);
if ($ret != 0) {erreur("index.php: Unable to start a new session. Please, try later."); return 1;}

//get the code of compiler.html
if ($_SESSION['htmlCode'] == "" || $_SESSION['fullScreenModeTest'] == 1){
  if($_SESSION['fullScreenMode'] == 1) $_SESSION['htmlCode'] = read_file("compiler-large.html");
  else $_SESSION['htmlCode'] = read_file("compiler.html");
  $_SESSION['fullScreenModeTest'] = 0;
}
$html = $_SESSION['htmlCode'];

//the html page is built
display_header($html);
// display_catalog($html,"goto_codeFaust.php");
display_exampleSaver($html);
display_navigation($html,2);
display_dropFile($html,"goto_codeFaust()");
display_atelier($html);
display_welcome($html);
display_footer($html);
?>
