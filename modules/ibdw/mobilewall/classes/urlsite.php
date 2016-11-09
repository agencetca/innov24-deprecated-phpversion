<?php 
$_REAL_SCRIPT_DIR = realpath(dirname($_SERVER['urlsite.php']));
$path = dirname($_SERVER['PHP_SELF']);
$percor = str_replace("modules","",str_replace("modules/ibdw/mobilewall/classes","",$path));
$urlsite = 'http://'.$_SERVER["SERVER_NAME"].$percor;
?>