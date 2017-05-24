<?php

/*! \file update.php
\brief This file contains the function "update"
\author Romain Michon - Damien Cramet

Copyright (C) 2003-2015 GRAME, Centre National de Creation Musicale
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


function update()
{
    require "functions.php";
    //session is started
    if (session_id()=="") {
        session_start();
    }

    //global variable to display the catalog or not and to memorise the position of the user in the catalog
    $_SESSION['catalogDisplay'] = $_POST['catalogDisplay2'].$_POST['catalogDisplay1'];
    if ($_SESSION['catalogDisplay'] == 1) $_SESSION['catalogDisplayRet'] = 1;
    if ($_SESSION['catalogDisplay'] == 2) $_SESSION['catalogDisplayRet'] = 2;
    if ($_POST['catalogSectionLocation'] != "") $_SESSION['catalogSectionLocation'] = $_POST['catalogSectionLocation'];
    if ($_POST['catalogItemLocation'] != "") $_SESSION['catalogItemLocation'] = $_POST['catalogItemLocation'];

    //client os is detected
    if($_POST['firstCodeSubmit'] == 1 && $_SESSION['firstSubmitDone'] != 1){
        $browser_info = get_browser(null, true);
        if ($browser_info['platform'] == "Linux") $_POST['osMenu'] = "Linux";
        if ($browser_info['platform'] == "Windows") $_POST['osMenu'] = "Windows";
        if ($browser_info['platform'] == "MacOSX") $_POST['osMenu'] = "OSX";
        $_POST['enrobagemenu'] = "none";
        if($_SESSION['title_example'] == "") $_POST['nomApplication'] = "myFaustProgram";
        $_SESSION['firstSubmitDone'] = 1;
    }

    //the history file is created
    exec("touch ".$_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id']."/history.txt", $none, $ret);
    if ($ret != 0) {erreur("Update.php : Probleme avec le dossier de session"); return 1;}

    //in the case where an object from the catalog has been selected...
    $file = $_SESSION['title_example'];
    if (($_SESSION['example']==1) && ($file!="")){
        $_SESSION['fichier'] = $file;
        $name = $_SESSION['fichier'];
        $group = $_SESSION['title_group'];

        //the file containing the code is read from the server
        $_POST['faustcode'] = Remove_CR(read_file("catalog/".$group."/src/".$name.".dsp"));
        $_SESSION['code_faust'] =  $_POST['faustcode'];
        $_POST['nomApplication'] = $name;

        //the .dsp dependencies are detected and their path is saved
        $_SESSION['dspDept'] = exec("faust -flist catalog/".$group."/src/".$name.".dsp | head -n 1 | sed 's/**//' | tr \" \" \"\n\" | grep .dsp | grep -v /".$name.".dsp | tr \"\n\" \" \" ", $none, $ret);
        //$_SESSION['dspDept'] = system("faust -flist catalog/".$group."/src/".$name.".dsp | head -n 1 | sed 's/**//' | tr \" \" \"\n\" | grep .dsp | grep -v /".$name.".dsp", $ret);

        //global variables are updated
        $_SESSION['comp_faust_done'] = 0 ;
        $_SESSION['comp_C_done'] = 0 ;
        $_SESSION['example']=0;
    }

    if ($_SESSION['fileDroped'] == 1){
        $dropedFilePath = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id']."/uploads/".$_SESSION['dropedFileName'];
        $_SESSION['code_faust'] = Remove_CR(read_file($dropedFilePath));
        //the faust program name is created in function of the name of the .dsp file
        $_POST['nomApplication'] = exec("echo ".$_SESSION['dropedFileName']." | cut -d \. -f 1", $none, $ret);

        //post variables are updated
        if ($_POST['enrobagemenu'] == "") $_POST['enrobagemenu'] = $_SESSION['enrobagemenu'];
        if ($_POST['langMenu'] == "") $_POST['langMenu'] = $_SESSION['langMenu'];
        if ($_POST['osMenu'] == "") $_POST['osMenu'] = $_SESSION['osMenu'];
        if ($_POST['OSCselect'] == "") $_POST['OSCselect'] = $_SESSION['OSCselect'];
        if ($_POST['options'] == "") $_POST['options'] = $_SESSION['options'];
    }

    //if any change occured in the code or in the compilation options...
    if (($_SESSION['code_faust'] != $_POST['faustcode'] && $_POST['faustcode']!= "")
    || ($_SESSION['enrobagemenu'] != $_POST['enrobagemenu'] && $_POST['enrobagemenu']!= "" )
    || ($_SESSION['langMenu'] != $_POST['langMenu'] && $_POST['langMenu'] != "")
    || ($_SESSION['osMenu'] != $_POST['osMenu'] && $_POST['osMenu'] != "")
    || ($_SESSION['appli_name'] != $_POST['nomApplication'] && $_POST['nomApplication']!= "")
    || ($_SESSION['OSCselect'] != $_POST['OSCselect'])
    || ($_SESSION['compil_options'] != $_POST['options'])
    || ($_SESSION['fileDroped'] == 1)){

        // Update the state values of the current session
        if (($_SESSION['orig_editor'] == 1) || ($_SESSION['fileDroped'] == 1)){
            $_SESSION['code_changed'] = 1;
            if(($_SESSION['orig_editor'] != 2) && ($_SESSION['fileDroped'] != 1)) $_SESSION['code_faust'] = $_POST['faustcode'];
            $_SESSION['orig_editor'] = 0;
            $_SESSION['fileDroped'] = 0;
        }
        else{
            $_SESSION['code_changed'] = 1 ;
        }

        if ($_POST['langMenu'] != ""){
            $_SESSION['langMenu'] = $_POST['langMenu'];
        }

        if($_POST['osMenu'] != ""){
            $_SESSION['previousOSMenu'] = $_SESSION['osMenu'];
            $_SESSION['osMenu'] = $_POST['osMenu'];
        }
        //enrobage menu is built
        $list = array();
        if($_SESSION['osMenu'] == "Windows") exec("scripts/liste_enrobages Windows",$list,$ret);
        else if($_SESSION['osMenu'] == "OSX") exec("scripts/liste_enrobages OSX",$list,$ret);
        else if($_SESSION['osMenu'] == "Raspberry") exec("scripts/liste_enrobages Raspberry",$list,$ret);
        else if($_SESSION['osMenu'] == "Android") exec("scripts/liste_enrobages Android",$list,$ret);
        else if($_SESSION['osMenu'] == "Ros") exec("scripts/liste_enrobages Ros",$list,$ret);
        else if($_SESSION['osMenu'] == "Web") exec("scripts/liste_enrobages Web",$list,$ret);
        else if($_SESSION['osMenu'] == "Bela") exec("scripts/liste_enrobages Bela",$list,$ret);
        else if($_SESSION['osMenu'] == "Juce") exec("scripts/liste_enrobages Juce",$list,$ret);
        else if($_SESSION['osMenu'] == "Unity") exec("scripts/liste_enrobages Unity",$list,$ret);
        else exec("scripts/liste_enrobages Linux",$list,$ret);
        $list[0] = "none";
        $_SESSION['list_enrob_compil'] = $list;

        if($_POST['enrobagemenu'] != ""){
            $cutedEnrobagemenu = cutEnrobagemenu($_POST['enrobagemenu']);
            $_SESSION['cutPos'] = $cutedEnrobagemenu;
            $i = 0;

            while($list[$i] != "#" && cutEnrobagemenu($list[$i]) != $cutedEnrobagemenu) $i = $i + 1;
            if($list[$i] != "#"){
                if($_SESSION['osMenu'] == "Linux" && ($_SESSION['previousOSMenu'] == "Windows" || $_SESSION['previousOSMenu'] == "OSX")) $_SESSION['enrobagemenu'] = $_POST['enrobagemenu']."-64bits";
                else if(($_SESSION['osMenu'] == "Windows" || $_SESSION['osMenu'] == "OSX") && $_SESSION['previousOSMenu'] == "Linux") $_SESSION['enrobagemenu'] = $cutedEnrobagemenu;
                else $_SESSION['enrobagemenu'] = $_POST['enrobagemenu'];
            }
            else{
                #echo "<script>alert('The Faust architecture ".$_POST['enrobagemenu']." is not compatible with ".$_SESSION['osMenu'].". The default Faust architecture will be selected.')</script>";
                $_SESSION['enrobagemenu'] = "none";
            }
        }

        if($_POST['nomApplication'] != "") $_SESSION['appli_name'] = $_POST['nomApplication'];
        if($_SESSION['appli_name']=="") $_SESSION['appli_name'] = "noname";
        $_SESSION['fichier'] = $_SESSION['appli_name'];
        if($_POST['nomApplication']){
            $_SESSION['OSCselect'] = $_POST['OSCselect'];
            $_SESSION['compil_options'] = $_POST['options'];
        }
        //$_SESSION['compil_options'] = $_POST['options'];
        $_SESSION['comp_faust_done']=0;
        $_SESSION['comp_C_done']=0;

        // prepare the work directory
        $applname = $_SESSION['appli_name'];
        $architecture = $_SESSION['enrobagemenu'];
        $options = $_SESSION['compil_options'];
        $faustcode = traiter_chaine(Remove_CR($_SESSION['code_faust']));

        $sessiondirname = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id']."/";
        $workdirname = $sessiondirname."workdir/";
        $faustfilename = $workdirname.$applname.".dsp";
        if($architecture == "none") $srcmakefile = "Makefiles/Makefile.none";
        else $srcmakefile = "Makefiles/".$_SESSION['osMenu']."/Makefile.".$architecture;
        $dstmakefile = $workdirname."Makefile";

        if(exec("head -n 1 $srcmakefile",$none,$ret) == "#OSC_ALLOWED") $_SESSION['oscState'] = 1;
        else{
            $_SESSION['oscState'] = 0;
            if($_SESSION['OSCselect'] == "OSC=1"){
                echo "<script>alert('OSC is supported by the Faust architecture \"".$architecture."\"!')</script>";
                $_SESSION['OSCselect'] = "";
            }
        }

        // create a clean work directory
        exec("rm -rf $workdirname; mkdir $workdirname");

        // create the faust source file
        $fdin = fopen($faustfilename, "w");
        fputs( $fdin, $faustcode ) ;
        fclose( $fdin );

        $fdma = fopen($dstmakefile, "w");
        fputs($fdma, "faustfile := ".$applname.".dsp\n");
        if ($options != "") fputs( $fdma, "OPT := ".$options."\n") ;
        fclose( $fdma );

        exec("cat $srcmakefile >> $dstmakefile");
    }
}

?>
