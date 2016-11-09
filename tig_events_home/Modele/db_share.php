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
// 					$sql_share_events = "INSERT INTO events_home_share (ID, PostID, IDSharer, IDReceipt) VALUES (\"\", \"".$PostID."\", \"".$IDsharer."\", \"".$IDreceipt."\")";
// 					$req_share_events = mysql_query($sql_share_events);

					$sql_update_events = "UPDATE bx_events_main SET Shared = Shared+1 WHERE ID = ".$PostID."";
					$req_update_events = mysql_query($sql_update_events);

		}

























