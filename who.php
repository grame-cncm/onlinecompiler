<?php 
	require("php/env.php");
	echo shell_exec('faust -v');
	echo shell_exec('whoami');
	echo shell_exec('groups');
?>

