<?php

/*!
\file make_element.php
\brief This file contains the functions make_navigation and make_catalog
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

if (!defined("__make_element__"))
{
  define("__make_element__", 1);

  function display_header($html){
    require "php/functions.php";
    print get_header($html, "header");
  }

  function display_footer($html){
    require "php/functions.php";
    print get_footer($html, "footer");
  }

  function display_welcome($html){
    require "php/functions.php";
    if($_SESSION['firstOpening'] == ""){
      print get_section($html, "welcome");
      $_SESSION['firstOpening'] = 1;
    }
  }

  function display_helpCompiler($html){
    require "php/functions.php";
    print get_section($html, "helpCompiler");
  }

  function display_helpSaver($html){
    require "php/functions.php";
    print get_section($html, "helpSaver");
  }

  function display_helpCatalog($html){
    require "php/functions.php";
    print get_section($html, "helpCatalog");
  }

  function display_dropFile($html,$goto){
    require "php/functions.php";
    if ($_SESSION['jsDropFile'] == "" ) $_SESSION['jsDropFile'] = read_file ("js/uploadDrag.js");
    //the AJX script handling file droping is completed and added to the html page
    $js = $_SESSION['jsDropFile'];
    $assocJS['__goto__'] = $goto;
    $js = fill_template($js, $assocJS);
    $dropArea = get_section($html, "dropFile");
    $assoc['__scriptDropFile__'] = $js;
    if ($goto == "goto_codeFaust()") $assoc['__textDrop__'] = "Drop your .dsp file here or write your Faust code below";
    else $assoc['__textDrop__'] = "Drop your .dsp file here";
    print fill_template($dropArea, $assoc);
  }

  function display_exampleSaver($html){
    require "php/functions.php";
    require "php/form.php";
    display_helpSaver($html);
    $exampleSaver = get_section($html, "exampleSaver");

    if($_SESSION['displayExampleSaver'] == 1) $assoc['__displayExampleSaver__'] = "$('#showExampleSaver').hide();";
    else $assoc['__displayExampleSaver__'] = "$('#exampleSaver').hide();$('#hideExampleSaver').hide();";

    //fill the different fields with deffault or current values
    if(($_SESSION['userName'] != "YourName") && ($_SESSION['userName'] != "")) $assoc['__userName__'] = $_SESSION['userName'];
    else $assoc['__userName__'] = "YourName";
    if(($_SESSION['userEmail'] != "YourE-mail") && ($_SESSION['userEmail'] != "")) $assoc['__userEmail__'] = $_SESSION['userEmail'];
    else $assoc['__userEmail__'] = "YourE-mail";
    if(($_SESSION['exampleName'] != "ObjectName") && ($_SESSION['exampleName'] != "")) $assoc['__exampleName__'] = $_SESSION['exampleName'];
    else $assoc['__exampleName__'] = "ObjectName";
    if(($_SESSION['descriptionArea'] != "Describe your Faust Object") && ($_SESSION['descriptionArea'] != "")) $assoc['__descriptionArea__'] = $_SESSION['descriptionArea'];
    else $assoc['__descriptionArea__'] = "Describe your Faust Object";

    //category list is built in function of the folder in catalog/
    $catList = array();
    exec("ls catalog | grep user",$catList,$ret);
    if ($ret == 1) print("Can't get the category list!");
    $assoc = array_merge($assoc, make_menu("__catMenu__","catMenu",$_SESSION['catMenu'],"",$catList,"cat"));

    print fill_template ($exampleSaver, $assoc);
  }

  /*
    process_sent_example: This function process an example saved by the user with the example saver.
    It created all the files relative to an example in the catalog on the server and send emails to the users when
    their examples are modified.
   */

  function process_sent_example(){
    if($_POST['submitExample'] == "saveExample"){
      if($_POST['faustcodeSaved'] != "" && $_POST['faustcodeSaved'] != "Enter Faust code here"){
	$exampleAuthor = $_POST['userName'];
	$_SESSION['userName'] = $_POST['userName'];
	$exampleAuthorMail = $_POST['userEmail'];
	$_SESSION['userEmail'] = $_POST['userEmail'];
	$exampleFileName = str_replace(" ", "", $_POST['exampleName']);
	$_SESSION['exampleName'] = $exampleFileName;
	$exampleCat = $_POST['catMenu'];
	$_SESSION['catMenu'] = $_POST['catMenu'];
	$exampleDirName = "catalog/".$exampleCat."/";
	$exampleDescriptionDirName = $exampleDirName."description/".$exampleFileName.".txt";
	$exampleCaptureDirName = $exampleDirName."capture/";
	$exampleCaptureFileName = $exampleCaptureDirName.$exampleFileName."_compr.png";
	if($_POST['descriptionArea'] == "Describe your Faust Object") $fileDescription = "";
	else $fileDescription = $_POST['descriptionArea'];
	$_SESSION['descriptionArea'] = $_POST['descriptionArea'];

	//test if the given Faust code is correct
	$testDirectory = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/processExample/".$_SESSION['id'];
	$testFile = $testDirectory."/test.dsp";
	$testCppFile = $testDirectory."/test.cpp";
	exec("mkdir ".$testDirectory,$none,$ret);
	$fdin = fopen($testFile,"w");
	fputs($fdin, $_POST['faustcodeSaved']);
	fclose($fdin);
	exec("faust ".$testFile." -o ".$testCppFile,$none,$retTstFaust);
	exec("rm -r ".$testDirectory,$none,$ret);

	$_POST['faustcode'] = $_POST['faustcodeSaved'];

	if($retTstFaust == 0){
	  //extract the number of votes from the description file
	  if(file_exists($exampleDescriptionDirName)) $voteNumber = exec("cat $exampleDescriptionDirName | grep Voted | cut -c 12-",$none,$ret);
	  else $voteNumber = 0;

	  //build the description content
	  $descriptionContent = "by <a href=\"mailto:".$exampleAuthorMail."\">".$exampleAuthor."</a>: \n".$fileDescription."\n<br>Voted: ".$voteNumber;

	  //read the user info file
	  $userInfoDirName = $exampleDirName."uinfo/".$exampleFileName.".txt";
	  if(file_exists($userInfoDirName)){
	    $fdin = fopen($userInfoDirName,"r");
	    $userInfoContentOld = fread($fdin, filesize($userInfoDirName))."\n";
	    fclose($fdin);
	  }
	  //user info file content
	  $userInfoContent = $userInfoContentOld.date(DATE_RFC822)."\n".$exampleAuthor."\n".$exampleAuthorMail."\nforwarded for: ".$_SERVER['HTTP_X_FORWARDED_FOR']."\nip: ".$_SERVER['REMOTE_ADDR']."\n";

	  //faust example code
	  $exampleFileCode = $_POST['faustcodeSaved'];
	  $exampleCodeDirName = $exampleDirName."src/".$exampleFileName.".dsp";

	  //process the datas sent by the user
	  if($_POST['userName'] != "YourName" && $_POST['userEmail'] != "YourE-mail" && $_POST['exampleName'] != "ObjectName"){
	    if($_FILES['descriptionPicture']['name'] != ""){
	      if((($_FILES['descriptionPicture']['type'] == "image/gif") ||
		  ($_FILES['descriptionPicture']['type'] == "image/png") ||
		  ($_FILES['descriptionPicture']['type'] == "image/jpeg") ||
		  ($_FILES['descriptionPicture']['type'] == "image/pjpeg")) &&
		 ($_FILES['descriptionPicture']['size'] < 100000)){
		if ($_FILES['descriptionPicture']['error'] > 0) echo "Return Code: ".$_FILES['descriptionPicture']['error']."<br />";
		else{
		  //the screenshot is saved and scaled
		  move_uploaded_file($_FILES['descriptionPicture']['tmp_name'],$exampleDirName."capture/".$_FILES['descriptionPicture']['name']);
		  exec("convert ".$exampleCaptureDirName.$_FILES['descriptionPicture']['name']." -resize x100 ".$exampleCaptureFileName." && rm ".$exampleCaptureDirName.$_FILES['descriptionPicture']['name'],$none,$ret);
		}
	      }
	    }
	    else{
	      if(($_FILES['descriptionPicture']['type'] != "image/gif") &&
		 ($_FILES['descriptionPicture']['type'] != "image/png") &&
		 ($_FILES['descriptionPicture']['type'] != "image/jpeg") &&
		 ($_FILES['descriptionPicture']['type'] != "image/pjpeg") &&
		 ($_FILES['descriptionPicture']['type'] != ""))
		echo "<script>alert('The capture image has a wrong file format: please, only use gif, png or jpg. The default image will be used!')</script>";
	      if($_FILES['descriptionPicture']['size'] >= 100000)
		echo "<script>alert('The capture image you provided is too big: capture image size has to be less than 100kb. The default image will be used!')</script>";
	      exec("cp images/captureDef.png ".$exampleDirName."capture/".$exampleFileName."_compr.png",$none,$ret);
	    }

	    //the different files are saved in the catalog

	    $fdin = fopen($exampleDescriptionDirName,"w");
	    fputs($fdin, $descriptionContent);
	    fclose($fdin);

	    $fdin = fopen($exampleCodeDirName,"w");
	    fputs($fdin, $exampleFileCode);
	    fclose($fdin);

	    $fdin = fopen($userInfoDirName,"w");
	    fputs($fdin, $userInfoContent);
	    fclose($fdin);

	    //a mail is sent to the owners of an example
	    exec("cat $userInfoDirName | grep @ | sort -u",$userMailList,$ret);
	    $i = 0;
	    while($userMailList[$i] != ""){
	      if($i == 0) $to = $userMailList[$i];
	      else $to = $to.", ".$userMailList[$i];
	      $i = $i+1;
	    }
	    $to = $to.", grame.faust@gmail.com";
	    $subject = "[Faust Online Compiler] Code modification notification";
	    $message = "Hi,\n\nYour Faust object \"$exampleFileName\" from the Faust online compiler catalog has been modified by $exampleAuthor($exampleAuthorMail):\n\n$exampleFileCode\n\nPlease, report any bug at: rmnmichon@gmail.com.\n\nBests,\n\nThe Faust Team.";
	    $from = "grame.faust@gmail.com";
	    $headers = "From:" . $from;
	    mail($to,$subject,$message,$headers);

	    echo "<script>alert('Your example: \"$exampleFileName\" has been saved in the \"$exampleCat\" category of the catalog.')</script>";
	  }
	  else echo "<script>alert('Please, complete all the required fields to save your example in the catalog.')</script>";
	}
	else echo "<script>alert('Wrong Faust Code! Please enter a correct Faust Code.')</script>";
      }
      else echo "<script>alert('Please enter some Faust code.')</script>";
    }
  }

  /*display_catalog: This function build the software catalog. The first input argument must be an
    array containing the code of compiler.html. The second argument is the name of
    the php file where the browser should be routed when clicking on the Choose it! button.*/

  function display_catalog($html,$gotoAction){
    require "php/functions.php";

    if (session_id()=="") session_start();

    //activate the help div for the catalog
    display_helpCatalog($html);

    if($gotoAction == "goto_codeFaust.php") $assoc['__displayModeSaver__'] =
	 "<div id = \"showExampleSaver\"><img src=\"images/showSaver.png\"></div><div id=\"hideExampleSaver\"><img src=\"images/hideSaver.png\"></div>";
    else $assoc['__displayModeSaver__'] = "";

    //getting the apropriate section of compiler.html
    $catalog = get_section($html, "catalog");

    //location variables
    if($_SESSION['catalogSectionLocation'] == "") $catalogSectionLocation = 10000;
    else $catalogSectionLocation = $_SESSION['catalogSectionLocation'];
    if($_SESSION['catalogItemLocation'] == "") $catalogItemLocation = 10000;
    else $catalogItemLocation = $_SESSION['catalogItemLocation'];

    if($_SESSION['catalogDisplayRet'] == 2){
      $assoc['__catalogDisplayScript__'] = "$('#hideCatalog').hide();$('#catalog').hide();";}
    else $assoc['__catalogDisplayScript__'] = "$('#catalog').show();$('#showCatalog').hide();";

    //listing the different category contained in catalog
    exec("ls catalog",$listSec,$retSec);
    if ($retSec != 0) {erreur("Catalog.php : The script liste_exemples is not working!"); return 1;}

    //loop used to acces the content of each category
    $i = 0;
    while ($listSec[$i] != ""){

      //the list of categories is created
      $assoc['__catalogSectionTitles__'] = $assoc['__catalogSectionTitles__'].
	"<div id = \"catalogSectionTitle$i\"><div class = \"catalogSectionTitle\">$listSec[$i]</div></div>";

      //the categories browsing javascript is created
      $assoc['__catalogScript__'] = $assoc['__catalogScript__'].
	"$('#catalogSectionItems$i').hide();
         if($i == $catalogSectionLocation){ $('#catalogSectionTitle$i').css(\"background-color\",\"#F0F0F0\");$('#catalogSectionItems$i').show().siblings('div:visible').hide();}
         $('#catalogSectionTitle$i').css(\"width\",\"146px\").css(\"margin\",\"2px\").css(\"position\",\"relative\").click(function(){ $('#catalogSectionItems$i').show().siblings('div:visible').hide();$(this).css(\"background-color\",\"#F0F0F0\").siblings().css(\"background-color\",\"#FFFFFF\");});";

      //getting the content the content of each category
      $listItems = array();
      exec("scripts/liste_exemples ".$listSec[$i],$listItems,$retItems);
      if ($retItems != 0) {erreur("Catalog.php : The script liste_exemples is not working!"); return 1;}

      //creating the category's item column
      $assoc['__catalogSectionItems__'] = $assoc['__catalogSectionItems__'].
	"<div id = \"catalogSectionItems$i\"><div class = \"catalogSectionItems\">";

      //loop used to peruse the content of each category
      $j = 0;
      while ($listItems[$j] != "#"){
	$description = read_file("catalog/".$listSec[$i]."/description/".$listItems[$j].".txt");

	//creating the list of category's items
	$assoc['__catalogSectionItems__'] = $assoc['__catalogSectionItems__'].
	  "<div id = \"catalogItem$i-$j\"><div class = \"catalogItem\"><a ondblclick='sendExample".$i."n".$j."()'>$listItems[$j]</a></div></div>";

	//creating the description sections of each items
	if($i>=4) {
	  $deleteButton = "<input type = \"button\" onclick = \"removeExample".$i."n".$j."()\" value = \"Delete it!\">";
	  //$voteButton = "<div id=\"voteButton\"><img src=\"images/voteButton.png\"></div>";
	  $voteButton = "<input type = \"submit\" name = \"vote\" value = \"Vote!\">";
	} else {
	   $deleteButton = "";
	   $voteButton = "";
	}
	$assoc['__catalogItemDescription__'] = $assoc['__catalogItemDescription__'].
	  "<div id = \"catalogItemDescription$i-$j\">
<div class = \"catalogItemDescription\">
<table cellspacing=0>
<tr>
<td valign=\"top\">
<div class = \"catalogStaticTitle\">Description</div>
</td>
<td>
<form name =\"formCatalog$i-$j\" action=\"$gotoAction\" method=\"post\">
<input type=hidden name = \"title_example\" value=\"$listItems[$j]\">
<input type=hidden name = \"title_group\" value=\"$listSec[$i]\">
<input type=\"hidden\" name = \"catalogDisplay2\" value = \"1\">
<input type=\"hidden\" name = \"catalogSectionLocation\" value = \"$i\">
<input type=\"hidden\" name = \"catalogItemLocation\" value = \"$j\">
<input type=\"hidden\" name = \"removeExample\" id = \"removeExample$i-$j\">
<input type = \"submit\" name = \"get_code\" value = \"Choose it!\">
$deleteButton
$voteButton
</form>
</td>
</tr>
</table>
<script>
function sendExample".$i."n".$j."() {
document.forms[\"formCatalog$i-$j\"].submit();
}
function removeExample".$i."n".$j."(){
var r=confirm(\"Are your sure you want to delete '$listItems[$j]'?\");
if(r==true){
document.getElementById(\"removeExample$i-$j\").value = \"remove\";
sendExample".$i."n".$j."();
}
}
</script>
<div class = \"catalogItemDescriptionContent\">
<table>
<tr>
<td> <div class=\"catalogImg\"><img align=\"left\" alt=\"gui\" src=\"catalog/$listSec[$i]/capture/$listItems[$j]_compr.png\"></div>$description</td>
</tr>
</table>
</div></div></div>";

	//items browsing javascript
	$assoc['__catalogScript__'] = $assoc['__catalogScript__']."
$('#catalogItemDescription$i-$j').hide();
if(($i == $catalogSectionLocation) && ($j == $catalogItemLocation)){ $('#catalogItem$i-$j').css(\"background-color\",\"#F0F0F0\");$('#catalogItemDescription$i-$j').show().siblings('div:visible').hide();}
$('#catalogItem$i-$j').click(function(){ $('#catalogItemDescription$i-$j').show().siblings('div:visible').hide();$(this).css(\"background-color\",\"#F0F0F0\").siblings().css(\"background-color\",\"#FFFFFF\");});";
	$description = "";
	$j = $j+1;
      }
      $assoc['__catalogSectionItems__'] = $assoc['__catalogSectionItems__']."</div></div>";
      $i = $i+1;
    }
    if($gotoAction == "goto_codeFaust.php") $assoc['__sendFaustCodeScript__'] = "document.getElementById(\"faustcode\").value = editor.getValue();";
    else $assoc['__sendFaustCodeScript__'] = "";
    print fill_template ($catalog, $assoc);
  }

  /*
    update_catalog: update the catalog when an example is added or deleted and when a user voted
   */

  function update_catalog(){
    if($_POST['title_example'] != ""){
      $_POST['enrobagemenu'] = $_SESSION['enrobagemenu'];
      $_POST['OSCselect'] = $_SESSION['OSCselect'];
      $_POST['options'] = $_SESSION['compil_options'];
      $_SESSION['title_example'] = $_POST['title_example'];
      $titleExample = $_POST['title_example'];
      $_SESSION['title_group'] = $_POST['title_group'];
      $titleGroup = $_POST['title_group'];
      $_SESSION['example'] = 1;
      $descriptionFilePath = "catalog/".$titleGroup."/description/".$titleExample.".txt";

      if($titleGroup != "Effects" && $titleGroup != "Faust-STK" && $titleGroup != "Synthesizers" && $titleGroup != "Tools"){
	$_SESSION['exampleName'] = $titleExample;
	$_SESSION['catMenu'] = $titleGroup;
	$_SESSION['descriptionArea'] = exec("cat $descriptionFilePath | head -2 | tail -1",$none,$ret);
      }

      //update the description file when voting
      if($_POST['vote'] == "Vote!"){
	exec("cat $descriptionFilePath",$exampleDescriptionFile,$ret);
	$voteNumber = exec("cat $descriptionFilePath | grep Voted | cut -c 12-",$none,$ret);
	$voteNumber = $voteNumber+1;
	$exampleDescriptionFile[2] = "<br>Voted: ".$voteNumber;
	$descriptionContent = $exampleDescriptionFile[0]."\n".$exampleDescriptionFile[1]."\n".$exampleDescriptionFile[2];
	$fdin = fopen($descriptionFilePath,"w");
	fputs($fdin, $descriptionContent);
	fclose($fdin);
      }

      //remove an example from the catalog
      if($_POST['removeExample'] == "remove"){
	$exampleToDeleteGroup = "catalog/".$titleGroup."/capture";
	exec("ls $exampleToDeleteGroup",$elementsList);
	if($elementsList[1] == "") echo "<script>alert('User categories can not be empty, you can\'t delete this file.')</script>";
	else{
	  $_SESSION['example'] = "";
	  $exampleToDeletePath = "catalog/".$titleGroup."/uinfo/".$titleExample.".txt";
	  exec("cat $exampleToDeletePath | grep @ | sort -u",$userMailList,$ret);
	  //an e-mail is sent to the owners of the example being deleted
	  $i = 0;
	  while($userMailList[$i] != ""){
	    if($i == 0) $to = $userMailList[$i];
	    else $to = $to.", ".$userMailList[$i];
	    $i = $i+1;
	  }
	  $to = $to.", grame.faust@gmail.com";
	  $subject = "[Faust Online Compiler] Code deletion notification";
	  $message = "Hi,\n\nYour Faust object \"$titleExample\" from the Faust online compiler catalog has been deleted.\n\nPlease, report any bug at: rmnmichon@gmail.com.\n\nBests,\n\nThe Faust Team.";
	  $from = "grame.faust@gmail.com";
	  $headers = "From:" . $from;
	  mail($to,$subject,$message,$headers);

	  $fdin = fopen($exampleToDeletePath,"r");
	  $userInfoContentOld = fread($fdin, filesize($exampleToDeletePath))."\n";
	  fclose($fdin);
	  $userInfoContent = $userInfoContentOld.date(DATE_RFC822)."\nEXAMPLE DELETED\nforwarded for: ".$_SERVER['HTTP_X_FORWARDED_FOR']."\nip: ".$_SERVER['REMOTE_ADDR']."\n";
	  $fdin = fopen($exampleToDeletePath,"w");
	  fputs($fdin, $userInfoContent);
	  fclose($fdin);
	  exec("./scripts/remove_example $titleGroup $titleExample",$none,$ret);
	  echo "<script>alert('\"$titleExample\" has been successfully removed from the catalog and its different owners have been informed of this modification.')</script>";
	}
      }
    }
  }

  /*make_navigation: This function is used to move the frame on the navigation div to indicate
    to the user its location in the online compiler*/

    function make_navigation($html)
    {
        require "php/functions.php";

        $navigation = get_section($html, "navigation");
        $large = "";
        if (session_id()=="") session_start();
        if($_SESSION['fullScreenMode'] == 1) $large = "-large";
        if ($_SESSION['goto'] == "faust") {
            $assoc['__active2__'] = "class='active'";
            $assoc['__cadre__'] = "<a><img src=\"images/frame".$large.".png\" id=\"cadre2\" alt=\"cadre\"/></a>";
        } else if ($_SESSION['goto'] == "faustCode") {
            $assoc['__active1__'] = "class='active'";
            $assoc['__cadre__'] = "<a><img src=\"images/frame".$large.".png\" id=\"cadre1\" alt=\"cadre\"/></a>";
        } else if ($_SESSION['goto'] == "exec") {
            $assoc['__active5__'] = "class='active'";
            $assoc['__cadre__'] = "<a><img src=\"images/frame".$large.".png\" id=\"cadre3\" alt=\"cadre\"/></a>";
        } else if ($_SESSION['goto'] == "svg") {
            $assoc['__active3__'] = "class='active'";
            $assoc['__cadre__'] = "<a><img src=\"images/frame".$large.".png\" id=\"cadre4\" alt=\"cadre\"/></a>";
        } else if ($_SESSION['goto'] == "mdoc") {
            $assoc['__active4__'] = "class='active'";
            $assoc['__cadre__'] = "<a><img src=\"images/frame".$large.".png\" id=\"cadre5\" alt=\"cadre\"/></a>";
        } else {
            $assoc['__active1__'] = "class='active'";
            $assoc['__cadre__'] = "<a><img src=\"images/frame".$large.".png\" id=\"cadre1\" alt=\"cadre\"/></a>";
        }
        return $assoc;

    }

  /*display_navigation: this function build the navigation menu depending on the user's location.
   The first argument should be the array containing compiler.html. The second argument gives the
  version of naviagtion bar: 2 for the faust code page, 1 for the pages that include the compilation
  options menu and anything else for all the other pages.*/

  function display_navigation($html,$version)
  {
    require "php/functions.php";
    $navigation =  get_section($html, "navigation");
    $assoc = make_navigation($html);
    $large = "";
    if($_SESSION['fullScreenMode'] == 1) $large = "-large";
    if ($version == 1){
        $assoc['__link1__']   = "javascript:goto_codeFaust()";
        $assoc['__link2__']   = "javascript:goto_codeC2()";
        $assoc['__link3__']   = "javascript:goto_SVG2()";
        $assoc['__link4__']   = "javascript:goto_mdoc2()";
        $assoc['__link5__']   = "javascript:goto_exec2()";

      $assoc['__seeCPP__'] = "<div id=\"see-cpp\"><a href=\"javascript:goto_codeC2()\"><img src=\"images/CppCode".$large.".png\"></a></div>";
      $assoc['__edFaust__'] = "<div id=\"ed-faust\"><a href=\"javascript:goto_codeFaust()\"><img src=\"images/FaustCode".$large.".png\"></a></div>";
      $assoc['__seeSVG__'] = "<div id=\"see-svg\"><a href=\"javascript:goto_SVG2()\"><img src=\"images/SvgDiag".$large.".png\"></a></div>";
      $assoc['__autoDoc__'] = "<div id=\"auto-doc\"><a href=\"javascript:goto_mdoc2()\"><img src=\"images/AutoDoc".$large.".png\"></a></div>";
      $assoc['__getExec__'] = "<div id=\"exec-file\"><a href=\"javascript:goto_exec2()\"><img src=\"images/ExecFile".$large.".png\"></a></div>";
      $assoc['__navigForm__'] = "";
    } else if ($version == 2) {
        $assoc['__link1__']   = "javascript:goto_codeFaust()";
        $assoc['__link2__']   = "javascript:goto_codeC()";
        $assoc['__link3__']   = "javascript:goto_SVG()";
        $assoc['__link4__']   = "javascript:goto_mdoc()";
        $assoc['__link5__']   = "javascript:goto_exec()";

      $assoc['__seeCPP__'] = "<div id=\"see-cpp\"><a href=\"javascript:goto_codeC()\"><img src=\"images/CppCode".$large.".png\"></a></div>";
      $assoc['__edFaust__'] = "<div id=\"ed-faust\"><a href=\"javascript:goto_codeFaust()\"><img src=\"images/FaustCode".$large.".png\"></a></div>";
      $assoc['__seeSVG__'] = "<div id=\"see-svg\"><a href=\"javascript:goto_SVG()\"><img src=\"images/SvgDiag".$large.".png\"></a></div>";
      $assoc['__autoDoc__'] = "<div id=\"auto-doc\"><a href=\"javascript:goto_mdoc()\"><img src=\"images/AutoDoc".$large.".png\"></a></div>";
      $assoc['__getExec__'] = "<div id=\"exec-file\"><a href=\"javascript:goto_exec()\"><img src=\"images/ExecFile".$large.".png\"></a></div>";
      $assoc['__navigForm__'] = "";
    } else {
        $assoc['__link1__']   = "javascript:goto_codeFaust3()";
        $assoc['__link2__']   = "javascript:goto_codeC3()";
        $assoc['__link3__']   = "javascript:goto_SVG3()";
        $assoc['__link4__']   = "javascript:goto_mdoc3()";
        $assoc['__link5__']   = "javascript:goto_exec3()";

      $assoc['__seeCPP__'] = "<div id=\"see-cpp\"><a href=\"javascript:goto_codeC3()\"><img src=\"images/CppCode".$large.".png\"></a></div>";
      $assoc['__edFaust__'] = "<div id=\"ed-faust\"><a href=\"javascript:goto_codeFaust3()\"><img src=\"images/FaustCode".$large.".png\"></a></div>";
      $assoc['__seeSVG__'] = "<div id=\"see-svg\"><a href=\"javascript:goto_SVG3()\"><img src=\"images/SvgDiag".$large.".png\"></a></div>";
      $assoc['__autoDoc__'] = "<div id=\"auto-doc\"><a href=\"javascript:goto_mdoc3()\"><img src=\"images/AutoDoc".$large.".png\"></a></div>";
      $assoc['__getExec__'] = "<div id=\"exec-file\"><a href=\"javascript:goto_exec3()\"><img src=\"images/ExecFile".$large.".png\"></a></div>";
      $assoc['__navigForm__'] = "<form name=\"formNavigation\" method = \"post\"><input type=\"hidden\" name=\"catalogDisplay1\" id=\"catalogDisplay1\"/></form>";
    }
    /*  */


    $navigation = fill_template($navigation, $assoc);
    print $navigation;
  }

  /*get_options: This function build the compilation options menu. Its first argument must
   be an array containing the code of compiler.html. The second argument should be the name
  of the file from where the function is called and the third is the action that web-browser should
  execute when the architecture is changed in the enrobage menu.*/

  function display_options($html,$action,$goto,$dis)
  {
    if (session_id()=="") session_start();
    // require "./inc/mess.inc";
    require "php/form.php";
    require "php/functions.php";

    display_helpCompiler($html);

    //get the required section in compiler.html
    $compOptions = get_section($html, "compOptions");

    if($dis == "disableLang") $disLang = "disabled";
    if($dis == "disableOthers") $disOthers = "disabled";

    //is the code has been changed, we update the value of each option
    if ($_SESSION['code_changed']==1){
      $assoc = $_SESSION['infos_options'];
      $assoc['__titre__'] = $_SESSION['appli_name'];
      $assoc['__options_value__'] = $_SESSION['compil_options'];
      $osMenuOnChange = "ONCHANGE=\"$goto\"";


      //check if the OSC checkbock has been checked
      if ($_SESSION['OSCselect'] == "OSC=1") $OSCselected = "checked";
      else $OSCselected = "";

      //the new values are saved
      $_SESSION['infos_options'] = $assoc ;
    }
    else {
      $_SESSION['infos_options'] = "";
    }

    //if the option informations already exists, then they are used
    if ($_SESSION['infos_options'] != "") $assoc = $_SESSION['infos_options'];
    else {
      $assoc['__titre__'] = "myFaustProgram";
      $assoc['__options_value__'] = "";
    }

    //the option help list is built
    $options= array();
    exec("faust -h",$options,$ret);
    if ($ret != 0) {erreur("Compiler.php : The Faust Compiler is not installed!"); return 1;}
    $options = extract_options($options);

    //enrobage menu is built
    $assoc = array_merge ($assoc, make_menu("__menuallenrobage__","enrobagemenu",$_SESSION['enrobagemenu'],"ONCHANGE=\"$goto\" ONFOCUS=\"check_enrobage()\"",$_SESSION['list_enrob_compil'],"opt",$disOthers));

    if($_SESSION['oscState'] != 1) $oscState = "disabled";

    //option values are set and updated
    $assoc['__action__'] = $action;
    $assoc['__OSCvalue__'] = $OSCselected." onchange=\"$goto\" ".$oscState;
    $assoc['__options__'] = "<a>&nbsp;&nbsp;Opts<span>".$options."</span></a>" ;
    $assoc['__name__'] = "<a>Name<span>Enter a name for your program</span></a>" ;
    $assoc['__enrobage__'] = "<a>Architecture<span>Choose an architecture to generate a compilable C++ file or an executable</span></a>" ;
    $assoc['__OSC__'] = "<a>OSC</a>";
    $assoc['__refreshGoto__'] = $goto;

    //processor architecture menu and os menu are built
    $osList[0] = "Linux";
    $osList[1] = "Windows";
    $osList[2] = "OSX";
    $osList[3] = "Raspberry";
    $osList[4] = "Android";
    $osList[5] = "Ros";
    $osList[6] = "Web";
    $osList[7] = "Bela";
    $osList[8] = "Juce";
    $osList[9] = "Unity";

    $assoc = array_merge ($assoc, make_menu("__osMenu__","osMenu",$_SESSION['osMenu'],$osMenuOnChange,$osList,"cat", $disOthers));

    //displayed language
    $langList[0] = "C++";
    $langList[1] = "c";
    $langList[2] = "java";
    $langList[3] = "wast";
    $langList[4] = "llvm";
    $assoc = array_merge ($assoc, make_menu("__langMenu__","langMenu",$_SESSION['langMenu'],"ONCHANGE=\"$goto\"",$langList,"cat", $disLang));

    //the compOptions section is filled and returned
    $ret =  fill_template ($compOptions, $assoc);
    $_SESSION['infos_options'] = $assoc ;
    print $ret ;
  }

  function display_atelier($html)
  {
    if (session_id()=="") session_start();
    // require "./inc/mess.inc";
    require "php/functions.php";

    $atelier = get_section($html, "atelier");

    //if the code is changed then infos_ateliers is updated
    if ($_SESSION['code_changed']==1){
      $assoc = $_SESSION['infos_atelier'];
      $assoc['__code__'] = traiter_chaine($_SESSION['code_faust']);
      $_SESSION['infos_atelier'] = $assoc ;
    }

    //otherwise infos_atelier is filled with nothing
    else $_SESSION['infos_atelier']="";

    //if the code comes from the catalog then...
    $file = $_SESSION['title_example'];
    if (($_SESSION['example']==1) && ($file!="")){
      $_SESSION['fichier'] = $file;
      $name = $_SESSION['fichier'];
      $group = $_SESSION['title_group'];
      $assoc['__code__'] = Remove_CR(read_file("catalog/".$group."/src/".$name.".dsp"));
      $_SESSION['comp_faust_done'] = 0 ;
      $_SESSION['comp_C_done'] = 0 ;
      $_POST['get_code'] = 0;
    }

    //if the code already exists, we use it
    else if ($_SESSION['infos_atelier'] !="") $assoc = $_SESSION['infos_atelier'];

    //otherwise the code area is filled we a default message
    else $assoc['__code__'] = "// Enter Faust code here\n\nprocess = +;";

    //the atelier section is filled
    $ret =  fill_template ($atelier, $assoc);
    $_SESSION['infos_atelier'] = $assoc ;
    print $ret;
  }

}

?>
