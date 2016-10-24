<?php

require("php/env.php");
require "php/functions.php";
require "php/update.php";

//session is started
if (session_id()=="") session_start();

if(!empty($_FILES['file']['name'][0])){
	foreach ($_FILES['file']['name'] as $position => $name) {
		$filepath = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id']."/uploads/$name";
    	if(move_uploaded_file($_FILES['file']['tmp_name'][$position], $filepath)){
			// Only small files are potential URLs
			if (filesize($filepath) < 512) {
				// check if file content is a valid URL
				$content = file($filepath);
				//file_put_contents('php://stderr', print_r($content, TRUE));
				if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $content[0])) {
				//if (!filter_var($content[0], FILTER_VALIDATE_URL) === false) {
					// Valid URL: we read the content of the URL and save it
					//file_put_contents('php://stderr', $content[0]);
					$urlcontent = file($content[0]);
					file_put_contents($filepath, $urlcontent);
				} else {
    				//file_put_contents('php://stderr', "Not an URL\n");
				}
			}
			$_SESSION['fileDroped'] = 1;
			$_SESSION['dropedFileName'] = $name;
			//update();
		}
	}
}

?>
