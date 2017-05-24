<?php

/*! \file compilation_faust.php
  \brief This file call the compilation.php file and save the results
  \author Romain Michon - Damien Cramet

  It indicates to the compiler to carry out a Faust compilation
  and save the results into the SESSION variables. Then it creates
  a package .tgz with the .cpp resulting file and a Makefile. This
  package can be downloaded by the user to compile the code C++ on
  his computer.
  It finally opens the result page.

  Copyright (C) 2003-2011 GRAME, Centre National de Creation Musicale
  ---------------------------------------------------------------------
  This file is free software; you can redistribute it
  and/or modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 3 of
  the License, or (at your option) any later version.
  See <http://www.gnu.org/licenses/>.

*/

if (!defined("__compilation_faust__"))
{
  define("__compilation_faust__", 1);

  if (session_id()=="") session_start();

  // require "inc/mess.inc";
  require("php/env.php");
  require "php/functions.php";
  require "php/make_element.php";

  //get the html code
  if ($_SESSION['htmlCode'] == "" ) $_SESSION['htmlCode'] = read_file ("compiler.html");
  $html = $_SESSION['htmlCode'];

  //the top of the page is displayed
  //display_header($html);
  //display_navigation($html,0);
  // display_catalog($html,"goto_codeC.php");

  //getting the code to be compiled
  $codeAcompiler = $_SESSION['code_faust'];

  //if the faust code area is empty, an error message is returned
  if ( $codeAcompiler == "" | $codeAcompiler == "$mess_code" ){
    $_SESSION['resultat_faust'] = -1;
  } else {
      //otherwise the compilation is carried out
    $sessiondirname = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id']."/";
    $workdirname = $sessiondirname."workdir/";
    $fileName = $workdirname.$_SESSION['appli_name'].".dsp";
    $cppFileName = $workdirname.$_SESSION['appli_name'].".cpp";
    $javaFileName = $workdirname.$_SESSION['appli_name'].".java";
    $wastFileName = $workdirname.$_SESSION['appli_name'].".wast";
    $cFileName = $workdirname.$_SESSION['appli_name'].".c";
    $llvmFileName = $workdirname.$_SESSION['appli_name'].".llvm";
    $logfile = $workdirname."errors.log";
    $opt = $_SESSION['compil_options'];
    $oscControl = $_SESSION['OSCselect'];
    $dspdepts = "DSPDEPTS=\"".$_SESSION['dspDept']."\"";

    //generates the colored c++ code
    exec("make ".$oscControl." ".$dspdepts." -C $workdirname highlighted 2>> $logfile", $none, $ret1);
    //generates the source package
    exec("make ".$oscControl." ".$dspdepts." -C $workdirname source 2>> $logfile", $none, $ret2);

    if($_SESSION['langMenu'] == "java"){
      exec("faust -lang ".$_SESSION['langMenu']." $fileName >> ".$javaFileName, $none, $retJava);
      $_SESSION['codeJava'] = read_file($javaFileName);
    }

    if($_SESSION['langMenu'] == "wast"){
      exec("faust -lang ".$_SESSION['langMenu']." $fileName >> ".$wastFileName);
      $_SESSION['codeJs'] = read_file($wastFileName);
    }

    if($_SESSION['langMenu'] == "c"){
      exec("faust -lang ".$_SESSION['langMenu']." $fileName >> ".$cFileName);
      $_SESSION['codeC'] = read_file($cFileName);
    }

    if($_SESSION['langMenu'] == "llvm"){
      exec("faust -lang ".$_SESSION['langMenu']." $fileName >> ".$llvmFileName);
      $_SESSION['codeLLVM'] = read_file($llvmFileName);
    }

    if (($ret1 | $ret2) == 0) {
      $_SESSION['comp_faust_done'] = 1;
      $_SESSION['resultat_faust'] = 1;
      //$_SESSION['code_C_h'] = extract_code(read_file($workdirname."highlighted"));
      $_SESSION['code_C_h'] = read_file($cppFileName);
    }
    else {
      $_SESSION['resultat_faust'] = 0;
      $_SESSION['erreur_faust'] = read_file($logfile);
    }
  }

//display_footer($html);

//resultat_compilation is opened to diplay the result
//echo "<script type=\"text/javascript\">";
//echo "document.location.replace(\"result_compilation_faust.php\")";
//echo "</script> ";
}
require ("result_compilation_faust.php");
?>
