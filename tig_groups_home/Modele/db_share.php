<?php

require_once('../../inc/header.inc.php'); //BDD
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php'); //LKEYS
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');

check_logged($_GET['memberID']);

$IDsharer = $_GET['sender'];
$PostID = $_GET['postid'];
$IDreceipt = $_GET['receiver'];

if ($_COOKIE['memberID']=$IDsharer)
		{
					//On update la bdd
// 					$sql_share_groups = "INSERT INTO groups_home_share (ID, PostID, IDSharer, IDReceipt) VALUES (\"\", \"".$PostID."\", \"".$IDsharer."\", \"".$IDreceipt."\")";
// 					$req_share_groups = mysql_query($sql_share_groups);

					$sql_update_groups = "UPDATE bx_groups_main SET Shared = Shared+1 WHERE id = ".$PostID."";
					$req_update_groups = mysql_query($sql_update_groups);

		}

























