<?php

require "php/functions.php";

//session is started
if (session_id()=="") session_start();

$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
if ($fn){
  $tmpUploads = "/home/faust/www/onlinecompiler/tmp/".$_SESSION['id']."/uploads/";
  file_put_contents($tmpUploads.$fn,file_get_contents('php://input'));
  $_SESSION['fileDroped'] = 1;
  $_SESSION['dropedFileName'] = $_SERVER['HTTP_X_FILENAME'];
}

?>
