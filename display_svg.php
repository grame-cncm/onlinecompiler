<?php

/*! \file display_svg.php
  \brief This file build the diagram display page.
  \author Romain Michon - Damien Cramet

  It reads the page compiler.html and fills the templates
  (__template__).
  The section "diagram" of compilation.html is filled with an
  "<embed>" mark out which displays the SVG diagram.
  See the line concerning the "<embed>" mark out creation
  to change the SVG diagram display.

  Copyright (C) 2003-2011 GRAME, Centre National de Creation Musicale
  ---------------------------------------------------------------------
  This file is free software; you can redistribute it
  and/or modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 3 of
  the License, or (at your option) any later version.
*/

if (session_id() == "") session_start();

require("php/env.php");
require "php/functions.php";
require "php/make_element.php";
require "php/form.php";

//create a new session folder if it doesn't exist
if($_SESSION['id'] == "") $_SESSION['id'] = session_id();
$_SESSION['path'] = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id'];
system("scripts/new_session ".$_SESSION['path'], $ret);
if ($ret != 0) {erreur("display_svg.php: Unable to start a new session. Please, try later."); return 1;}

//update the global variable to set the frame position on the navigation bar
$_SESSION['goto'] = "svg";

//get the html code
if ($_SESSION['htmlCode'] == "" ) $_SESSION['htmlCode'] = read_file ("compiler.html");
$html = $_SESSION['htmlCode'];
$diag = get_section($html, "diagram");

//compute pathname to process.svg file
$applname 	= $_SESSION['appli_name'];
//$sessiondirname	= "$_SERVER['DOCUMENT_ROOT']/onlinecompiler/tmp/".$_SESSION['id']."/";
$sessiondirname	= "/onlinecompiler/tmp/".$_SESSION['id']."/";
$workdirname	= $sessiondirname."workdir/";
$blockDiagram 	= $workdirname.$applname."-svg/process.svg";
$signalGraph  = $workdirname.$applname.".dsp.sig.svg";
$taskGraph  = $workdirname.$applname.".dsp.graph.svg";
$impulseResponse = $workdirname.$applname.".imp.svg";
$impulseResponseSpec = $workdirname.$applname.".impspec.svg";
$spectrogram = $workdirname.$applname.".spec.jpeg";
$zipedDiagram = $workdirname."diagram.zip";

if($_SESSION['fullScreenMode'] == 1) $boxWidth = 836;
else $boxWidth = 700;

// //fill the html page with the embeded diagram
// if($_SESSION['code_faust'] == "Enter Faust code here") $assoc['__diagram__'] = "Please, enter some Faust code in the \"Faust Code\" tab or drag a Faust file in the area above.";
// else if($_SESSION['svgtype'] == "Signal-Graph") $assoc['__diagram__'] = "<embed src=".$signalGraph." align=\"center\" width = \"".$boxWidth."\" height =\"600\" type=\"image/svg+xml\" />";
// else if($_SESSION['svgtype'] == "Task-Graph") $assoc['__diagram__'] = "<embed src=".$taskGraph." align=\"center\" width = \"".$boxWidth."\" height =\"600\" type=\"image/svg+xml\" />";
// else if($_SESSION['svgtype'] == "Impulse-Response (Effect)"){
//     if($_SESSION['imptype'] == "FFT") $impulseResponseFile = $impulseResponseSpec;
//     else $impulseResponseFile = $impulseResponse;
//     $assoc['__diagram__'] = "<embed src=".$impulseResponseFile." align=\"center\" width = \"".$boxWidth."\" height =\"600\" type=\"image/svg+xml\" />";
// }
// else if($_SESSION['svgtype'] == "Spectrogram (Instrument)") $assoc['__diagram__'] = "<img src=".$spectrogram." align=\"center\" width = \"".$boxWidth."\" />";
// else{
//     $assoc['__diagram__'] = "<embed src=".$blockDiagram." align=\"center\" width = \"".$boxWidth."\" height =\"600\" type=\"image/svg+xml\" />";
//     $downloadFile = $blockDiagram;
// }

//fill the html page with the embeded diagram
if($_SESSION['code_faust'] == "Enter Faust code here") $assoc['__diagram__'] = "Please, enter some Faust code in the \"Faust Code\" tab or drag a Faust file in the area above.";
else if($_SESSION['svgtype'] == "Signal-Graph") $assoc['__diagram__'] = "<embed src=".$signalGraph." type=\"image/svg+xml\" />";
else if($_SESSION['svgtype'] == "Task-Graph") $assoc['__diagram__'] = "<embed src=".$taskGraph." type=\"image/svg+xml\" />";
else if($_SESSION['svgtype'] == "Impulse-Response (Effect)"){
    if($_SESSION['imptype'] == "FFT") $impulseResponseFile = $impulseResponseSpec;
    else $impulseResponseFile = $impulseResponse;
    $assoc['__diagram__'] = "<embed src=".$impulseResponseFile." type=\"image/svg+xml\" />";
}
else if($_SESSION['svgtype'] == "Spectrogram (Instrument)") $assoc['__diagram__'] = "<img src=".$spectrogram." />";
else{
    $assoc['__diagram__'] = "<embed src=".$blockDiagram." type=\"image/svg+xml\" />";
    $downloadFile = $blockDiagram;
}

$listSVG[0] = "Block-Diagram";
$listSVG[1] = "Signal-Graph";
$listSVG[2] = "Task-Graph";
$listSVG[3] = "Impulse-Response (Effect)";
$listSVG[4] = "Spectrogram (Instrument)";
$imptype[0] = "Impulse-Response";
$imptype[1] = "FFT";

$assoc = array_merge ($assoc, make_menu("__menuOptionSVG__","svgtype",$_SESSION['svgtype'],"ONCHANGE=\"submitOptionSVG()\"",$listSVG,"cat","enabled"));
$assoc['__downloadFig__'] = "<input type=\"button\" id=\"downloadFig\" name=\"downloadFig\" onclick=\"window.location.href='$zipedDiagram'\" value=\"Download Figure\">";
if($_SESSION['svgtype'] == "Impulse-Response (Effect)"){
  $assoc = array_merge ($assoc, make_menu("__impType__","imptype",$_SESSION['imptype'],"ONCHANGE=\"submitOptionSVG()\"",$imptype,"cat","enabled"));
  $assoc['__impulseParam__'] = "<a>Signal Length</a><input type=\"text\" onchange=\"submitOptionSVG()\" id=\"impLength\" name=\"impLength\" value=\"".$_SESSION['impLength']."\">";
  $assoc['__spectDuration__'] = "";
  $assoc['__winSize__'] = "";
}
else if($_SESSION['svgtype'] == "Spectrogram (Instrument)"){
  $assoc['__downloadFig__'] = "";
  $assoc['__impType__'] = "";
  $assoc['__impulseParam__'] = "";
  $assoc['__spectDuration__'] = "<a>Performance Duration(ms)</a><input type=\"text\" onchange=\"submitOptionSVG()\" id=\"spectDuration\" name=\"spectDuration\" value=\"".$_SESSION['spectDuration']."\">";
  $assoc['__winSize__'] = "<a>FFT window size</a><input type=\"text\" onchange=\"submitOptionSVG()\" id=\"winSize\" name=\"winSize\" value=\"".$_SESSION['winSize']."\">";
}
else{
  $assoc['__impType__'] = "";
  $assoc['__impulseParam__'] = "";
  $assoc['__spectDuration__'] = "";
  $assoc['__winSize__'] = "";
}
$diag = fill_template($diag, $assoc);

//display the html page
display_header($html);
// display_catalog($html,"goto_SVG.php");
display_dropFile($html,"document.location.replace(\"goto_SVG.php\")");
display_navigation($html,0);
print $diag;
display_footer($html);

?>
