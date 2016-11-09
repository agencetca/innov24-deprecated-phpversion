<?php
require_once( '../../../inc/header.inc.php' );
$mioid=(int)$_COOKIE['memberID'];
mysql_query("SET NAMES 'utf8'");
$query = "DELETE FROM ibdw_mega_profile WHERE Owner = '$mioid'";
$esegui = mysql_query($query);
?>