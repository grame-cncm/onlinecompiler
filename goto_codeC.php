<?php

/*! \file goto_codeC.php
  \brief This file links any page to the compilation pages
  \author Romain Micon - Damien Cramet

  This page is called by the Javascript function goto_codeC().
  It updates the SESSION values and, according to the current
  state, it launchs the Faust compilation or display the result
  if the code is already compiled.

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
require "php/update.php";
require "php/make_element.php";

//the html page is displayed
if ($_SESSION['htmlCode'] != "" ){
  $html = $_SESSION['htmlCode'];
  update();

  //global variable to set the position of the frame in the navigation bar
  $_SESSION['goto'] = "faust";

  //if the code is alreay compiled, then we directly go the result
  if ($_SESSION['comp_faust_done']==1){
    require("result_compilation_faust.php");
  } else {
    require("compilation_faust.php");
  }
} else {
  require("index.php");
}

?>
