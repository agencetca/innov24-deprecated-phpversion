<?php

require_once('../../inc/header.inc.php'); //BDD
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php'); //LKEYS
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');

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



		$select = "SELECT sys_categories.Liked
		FROM sys_categories
		WHERE Type = '".$Type."'
		AND Category ='".$CatID."'";
		$nombre = mysql_query($select);
		$tot = mysql_fetch_array($nombre);
		$tot_likes = $tot[0];
		
		echo $tot_likes;