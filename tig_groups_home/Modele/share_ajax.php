<?php

require_once('../../inc/header.inc.php'); //BDD

$PostID = $_GET['postid'];

$sql_count = "SELECT bx_groups_main.Shared
FROM bx_groups_main
WHERE id = ".$PostID."";
$req_count = mysql_query($sql_count);

$answer = mysql_fetch_assoc($req_count);

echo $answer['Shared'];

