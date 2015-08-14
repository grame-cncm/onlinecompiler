<?php

require "php/functions.php";
require "php/update.php";

//session is started
if (session_id()=="") session_start();

if(!empty($_FILES['file']['name'][0])){
	foreach ($_FILES['file']['name'] as $position => $name) {
    	if(move_uploaded_file($_FILES['file']['tmp_name'][$position], '/home/faust/www/onlinecompiler/tmp/'.$_SESSION['id'].'/uploads/'.$name)){
			$_SESSION['fileDroped'] = 1;
			$_SESSION['dropedFileName'] = $name;
			update();
		}
	}
}

?>
