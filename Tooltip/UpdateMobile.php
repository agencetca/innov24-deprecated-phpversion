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

$mobile_number = addslashes(htmlentities($_POST['mobile_number'],ENT_QUOTES,"UTF-8"));

$insertion = "UPDATE Profiles SET mobile_number='".$mobile_number."'WHERE ID=".$userid."";
$resultUpdate = mysql_query($insertion) or die(mysql_error());

header('Location:'.$link.'');

?>