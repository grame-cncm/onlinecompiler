<?php

/*! \file goto_SVG.php
  \brief This file is used to link any page to the SVG diagram page
  \author Romain Michon - Damien Cramet

  This page updates the SESSION values and,
  according to the current state, it opens the affichage_svg.php file
  or launchs the Faust compilation if the diagram is not generated yet.
  If the Faust compilation has failed, it opens the result page to
  display the error.

  Copyright (C) 2003-2011 GRAME, Centre National de Creation Musicale
  ---------------------------------------------------------------------
  This file is free software; you can redistribute it
  and/or modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 3 of
  the License, or (at your option) any later version.


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

  //global variable to set the position of the frame on the navigation bar
  $_SESSION['goto'] = "svg";

  //genrating the block diagram from the faust code
  $codeToCompile = $_SESSION['code_faust'];
  if ( $codeToCompile == "" | $codeToCompile == "$mess_code" ) {
    $_SESSION['resultat_faust'] = -1;
  } else {
    if($_SESSION['svgtype'] == "") $_SESSION['svgtype'] = "Block-Diagram";
    if($_POST['svgtype'] != "") $_SESSION['svgtype'] = $_POST['svgtype'];
    if($_SESSION['imptype'] == "") $_SESSION['imptype'] = "Impulse-Response";
    if($_POST['imptype'] != "") $_SESSION['imptype'] = $_POST['imptype'];
    if($_SESSION['impLength'] == "") $_SESSION['impLength'] = 1000;
    if($_POST['impLength'] != ""){
      if($_POST['impLength'] > 88200 | $_POST['impLength'] < 1) echo "<script>alert('The length of the impulse response must be greater than 0 and smaller than 88200.')</script>";
      else $_SESSION['impLength'] = $_POST['impLength'];
    }
    if($_SESSION['spectDuration'] == "") $_SESSION['spectDuration'] = 1000;
    if($_POST['spectDuration'] != ""){
      if($_POST['spectDuration'] > 8000 | $_POST['spectDuration'] < 1) echo "<script>alert('The duration of your spectrogram must be greater than 0ms and smaller than 8000ms.')</script>";
      else $_SESSION['spectDuration'] = $_POST['spectDuration'];
    }
    if($_SESSION['winSize'] == "") $_SESSION['winSize'] = 256;
    if($_POST['winSize'] != ""){
      if($_POST['winSize'] >= 8192 | $_POST['winSize'] <= 64) echo "<script>alert('The window size must be greater or equal to 64 and smaller or equal to 8192.')</script>";
      else $_SESSION['winSize'] = $_POST['winSize'];
    }

    $sessiondirname = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id'];
    ##$sessiondirname = "http://faust.grame.fr/onlinecompiler/tmp/".$_SESSION['id']."/";
    $workdirname = "$sessiondirname/workdir";
    $appliName = $workdirname."/".$_SESSION['appli_name'].".dsp";
    $dspdepts = "DSPDEPTS=".$_SESSION['dspDept'];
    if($_SESSION['diagramDone'] == 1) exec("rm "."$workdirname/diagram.zip");
    if($_SESSION['svgtype'] == "Block-Diagram"){
      exec("LD_RUN_PATH=/usr/X11R6/lib; export LD_RUN_PATH; make ".$dspdepts." -C $workdirname svg", $none, $ret);
      $zipedDiagram = $_SESSION['appli_name']."-svg/process.svg";
    }
    if($_SESSION['svgtype'] == "Signal-Graph"){
      exec("faust2sig -svg $appliName", $none, $ret);
      $zipedDiagram = $_SESSION['appli_name'].".dsp.sig.svg";
    }
    if($_SESSION['svgtype'] == "Task-Graph"){
      exec("faust2graph -svg $appliName", $none, $ret);
      $zipedDiagram = $_SESSION['appli_name'].".dsp.graph.svg";
    }
    if($_SESSION['svgtype'] == "Impulse-Response (Effect)"){
      exec("scripts/faust2impulse ".$appliName." ".$_SESSION['impLength']." ".$_SESSION['imptype'], $none, $ret);
      if($_SESSION['imptype'] == "Impulse-Response") $zipedDiagram = $_SESSION['appli_name'].".imp.svg";
      else $zipedDiagram = $_SESSION['appli_name'].".impspec.svg";
    }
    if($_SESSION['svgtype'] == "Spectrogram (Instrument)"){
      exec("scripts/faust2impulse ".$appliName." ".$_SESSION['spectDuration']." Spectrogram ".$_SESSION['winSize'], $none, $ret);
    }

    ### check for faust error
    if ($ret != 0) {
        $_SESSION['diagramDone'] = 0;
        $_SESSION['resultat_faust'] = 0;
        require "index.php";
    } else {
        exec("cd ".$workdirname." && zip diagram.zip ".$zipedDiagram,$none, $ret);
        $_SESSION['diagramDone'] = 1;
        $_SESSION['resultat_faust'] = 1;
        require "display_svg.php";
    }
 }

} else {
  require "index.php";
}

?>
