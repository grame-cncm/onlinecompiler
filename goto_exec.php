<?php

/*! \file goto_exec.php
  \brief This file carry out the g++ compilation and links to the page
  displaying the result of this operation
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
require "php/update.php";
require "php/make_element.php";

//get the html code
if ($_SESSION['htmlCode'] != "" ){
  $html = $_SESSION['htmlCode'];
  // display_header($html);
  // display_catalog($html,"goto_exec.php");
  // display_navigation($html,0);

  //update and process
  //update_catalog();
  update();

  //global variable to set the position of the frame on the navigation bar
  $_SESSION['goto'] = "exec";

  //the g++ compilation is processed
  if ($_SESSION['comp_C_done'] != 1){
    $workdirname = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id']."/workdir";
    $dspdepts = "DSPDEPTS=\"".$_SESSION['dspDept']."\"";
    $logfile = "$workdirname/errors.log";
    $pconfpath = "/usr/local/lib/pkgconfig:/usr/local/share/pkgconfig:/usr/lib/pkgconfig:/usr/share/pkgconfig:/opt/kde3/lib/pkgconfig:/opt/gnome/lib/pkgconfig:/opt/gnome/lib/pkgconfig:/opt/gnome/share/pkgconfig";
    $oscControl = $_SESSION['OSCselect'];
    //$OS = "OS=".$_SESSION['osMenu'];
    //$arch = "PROCARCH=".$_SESSION['archMenu'];
    $sessionIdPath = "SESSIONID=".$_SESSION['id'];
    exec("LD_RUN_PATH=/usr/X11R6/lib; export LD_RUN_PATH; export PKG_CONFIG_PATH=$pconfpath; make ".$dspdepts." ".$oscControl." ".$sessionIdPath." -C $workdirname binary 2>> $logfile", $none, $ret);
    $_SESSION['comp_C_done'] = 1;

    //successful compilation
    if ($ret == 0) {
      $_SESSION['resultat_C'] = 1;
      $_SESSION['exec_file'] = substr(read_firstline("$workdirname/binaryfilename.txt"),0,-1);
      $qrcodeurl = "tmp/".$_SESSION['id']."/workdir/qr.png";
      $fileURL = "http://".$_SERVER['SERVER_NAME']."/onlinecompiler/tmp/".$_SESSION['id']."/workdir/".$_SESSION['exec_file'];
      exec("qrencode -o " . $qrcodeurl . " \"" . $fileURL . "\"");
      $_SESSION['reponse_g++'] =
      "The file <a href='" . $fileURL . "' target='_blank'>"
      . $_SESSION['exec_file'] . "</a> as been successfully generated and can now be downloaded."
      . "<div id='qrcode'> <a href='" . $fileURL . "' target='_blank'> <img src='" . $qrcodeurl . "'alt='qrcode face'> </a> </div>";
    //   echo "<script type=\"text/javascript\">";
    //   echo "document.location.replace(\"result_compilation_C.php\")";
    //   echo "</script> ";
    } else {
      $_SESSION['resultat_C'] = 0;
      $_SESSION['reponse_g++'] = read_file($logfile);
    //   echo "document.location.replace(\"result_compilation_C.php\")";
    //   echo "</script> ";
    //   echo "<script type=\"text/javascript\">";
    }
  }

  require("result_compilation_C.php");
  // display_footer($html);
} else {
  // echo "<script type=\"text/javascript\">";
  // echo "document.location.replace(\"index.php\")";
  // echo "</script> ";
  require("index.php");
}

?>
