<?php
//IMPORTS
require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
//include('MaConfig.php');
//VARS
$userid = (int)$_COOKIE['memberID'];
$link=GetProfileLink($userid);
//mysql_query("SET NAMES 'utf8'");

$Headline = addslashes(htmlentities($_POST['Headline'],ENT_QUOTES,"UTF-8"));

$insertion = "UPDATE Profiles SET Headline='".$Headline."'WHERE ID=".$userid."";
$resultUpdate = mysql_query($insertion) or die(mysql_error());

header('Location:'.$link.'');

?>