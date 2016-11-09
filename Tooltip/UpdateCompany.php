<?php
//IMPORTS
require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

//VARS
$userid = (int)$_COOKIE['memberID'];
$link=GetProfileLink($userid);
$company = addslashes(htmlentities($_POST['Company'],ENT_QUOTES,"UTF-8"));
$time=time();

$sql_company = "SELECT ml_pages_main.id as id,ml_pages_main.title as title FROM ml_pages_main";
$req_company = mysql_query($sql_company) or die(mysql_error());
while($result_company = mysql_fetch_assoc($req_company)){
if (strtolower($company)==strtolower($result_company['title'])){
$update_company_fans="INSERT INTO ml_pages_fans (`id_entry`,`id_profile`,`when`,`confirmed`) VALUES ('".$result_company['id']."','".$userid."','".$time."',1)";
$result_update_company = mysql_query($update_company_fans) or die(mysql_error());
$insertion1 = "UPDATE Profiles SET Company='".$company."'WHERE ID=".$userid."";
$resultUpdate1 = mysql_query($insertion1) or die(mysql_error());
}
else{
$insertion = "UPDATE Profiles SET Company='".$company."'WHERE ID=".$userid."";
$resultUpdate = mysql_query($insertion) or die(mysql_error());
}
}
header('Location:'.$link.'');

?>