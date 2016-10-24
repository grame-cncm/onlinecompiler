<?php

/*! \file goto_mdoc.php
  \brief This file build the automatic documentation and links to documenator
  viewer.
  \author Romain Michon

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
require "php/update.php";
require "php/make_element.php";

//the html page is displayed
if ($_SESSION['htmlCode'] != "" ){
  $html = $_SESSION['htmlCode'];
  display_header($html);
  //display_catalog($html,"goto_mdoc.php");
  display_navigation($html,0);

  //update and process
  //update_catalog();
  update();

  //global variable to set the position of the frame on the navigation bar
  $_SESSION['goto'] = "mdoc";

  //the faust code is processed in the local package genrerated by update()
  $sessiondirname = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id'];
  $workdirname	= "$sessiondirname/workdir";
  $appname = $_SESSION['appli_name'];
  $dspdepts = "DSPDEPTS=".$_SESSION['dspDept'];

  //a second version of the file with a random name is created to chisel the google viewer cache
  $randMdoc = rand(0,40000);
  $_SESSION['randMdoc'] = $randMdoc;
  exec("LD_RUN_PATH=/usr/X11R6/lib; export LD_RUN_PATH; make $dspdepts -C $workdirname mdoc; cp $workdirname/$appname-mdoc/pdf/$appname.pdf $workdirname/$appname-mdoc/pdf/$appname$randMdoc.pdf", $none, $ret);
  if ($ret == 0) {
    $_SESSION['resultat_mdoc'] = 1;
  }
  else {
    $_SESSION['resultat_mdoc'] = 0;
  }
  echo "<script type=\"text/javascript\">";
  echo "document.location.replace(\"result_compilation_mdoc.php\")";
  echo "</script> ";

  display_footer($html);
}

//if session was lost
else{
  echo "<script type=\"text/javascript\">";
  echo "document.location.replace(\"index.php\")";
  echo "</script> ";
}

?>
