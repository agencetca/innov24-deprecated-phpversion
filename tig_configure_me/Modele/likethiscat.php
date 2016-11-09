<?php

require_once('../../inc/header.inc.php'); //BDD
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php'); //LKEYS
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');

$memberiID = getID($_GET['ID']);
$identifiant = $_GET['identifiant'];
$CatID = $_GET['original'];


		switch ($identifiant) {
			case 'News':
			$Table = "tig_table_likes_news";
			$Type = "bx_blogs";
			break;

			case 'Events':
			$Table = "tig_table_likes_events";
			$Type = "bx_events";
			break;

			case 'Groups':
			$Table = "tig_table_likes_groups";
			$Type = "bx_groups";
			break;

			case 'Store':
			$Table = "tig_table_likes_store";
			$Type = "bx_store";
			break;

			default:
			break;
		}


$verif = "SELECT COUNT(*)
FROM ".$Table."
WHERE Category ='".$CatID."'
AND MemberID = ".$memberiID."";
$resultat = mysql_query($verif);
$total = mysql_fetch_array($resultat);
$nb_total = $total[0];

if ($nb_total == 0) {

				$like = "INSERT INTO ".$Table." (ID, MemberID,Category,Timestamp)
				VALUES ('','".$memberiID."','".$CatID."','') ";
				$added = mysql_query($like) or die("<br />REQUETE2<br />Your Query: " . $bring . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());

				$maj2 = "UPDATE sys_categories
				SET Liked = Liked+1
				WHERE Type = '".$Type."'
				AND Category ='".$CatID."'";
				$ok2 = mysql_query($maj2);
				
		}
