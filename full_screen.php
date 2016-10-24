<?php
//if no session is found, a new one is started
if (session_id()=="") session_start();

require("php/env.php");
require "php/update.php";

$_SESSION['fullScreenMode'] = $_POST['fullScreenMode'];
$_SESSION['fullScreenModeTest'] = 1;

$tt = update();

echo "Full Screen mode has been opened in a New Window.";

//jump to index.php
echo "<script type=\"text/javascript\">";
if($_SESSION['fullScreenMode'] == 1){
  echo "window.open(\"http://faust.grame.fr/onlinecompiler/index.php\")";
}
if($_SESSION['fullScreenMode'] == 0) echo "window.open(\"http://faust.grame.fr/index.php/online-examples\",\"_self\")";
echo "</script> ";

?>
