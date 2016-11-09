<?php

session_start();


	function count_it($default_behaviour,$param,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers) {

    //----------------------- REQUETE QUI COMPTE -------------------------------

		$count = "SELECT count(distinct bx_groups_main.id) 
		FROM bx_groups_main
		INNER JOIN Profiles ON (bx_groups_main.author_id=Profiles.ID AND Profiles.Status='Active') 
		INNER JOIN sys_pre_values ON bx_groups_main.type=sys_pre_values.Value 
		WHERE (bx_groups_main.type='".$param."')
		AND (".$default_behaviour.")
		AND bx_groups_main.categories IN ('".$innov24_config."') 
		AND (bx_groups_main.allow_view_group_to=3 
		OR bx_groups_main.allow_view_group_to=4 
		OR (bx_groups_main.allow_view_group_to=5 
		AND bx_groups_main.author_id IN ('".$bigfriendlist."')) 
		OR bx_groups_main.allow_view_group_to IN ('".$privacy_OthersToMe."') 
		OR bx_groups_main.allow_view_group_to IN ('".$privacy_MeToOthers."'))";
		$resultat = mysql_query($count);
		$total = mysql_fetch_array($resultat);
		$nb_total = $total[0];
		mysql_free_result ($resultat);
		
		return $nb_total;

	}

	function bring_it($default_behaviour,$param,$Limit,$Where,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers) {

    //----------------------- REQUETE QUI RAMENE LES POSTS -------------------------------

		$bring = "SELECT DISTINCT sys_pre_values.LKey,bx_groups_main.id, bx_groups_main.Shared, bx_groups_main.author_id, bx_groups_main.uri,bx_groups_main.title,bx_groups_main.desc,bx_groups_main.categories,bx_groups_main.created,bx_groups_main.views,bx_groups_main.comments_count,bx_groups_main.allow_view_group_to,bx_groups_main.thumb,Profiles.Avatar,Profiles.ID,Profiles.FirstName,Profiles.LastName,Profiles.ProfileType
		FROM bx_groups_main
		INNER JOIN Profiles ON (bx_groups_main.author_id=Profiles.ID AND Profiles.Status='Active')
		INNER JOIN sys_pre_values ON bx_groups_main.type=sys_pre_values.Value 
		WHERE ".$Where." (bx_groups_main.type='".$param."')
		AND (".$default_behaviour.")
		AND bx_groups_main.categories IN ('".$innov24_config."') 
		AND (bx_groups_main.allow_view_group_to=3 
		OR bx_groups_main.allow_view_group_to=4 
		OR (bx_groups_main.allow_view_group_to=5 
		AND bx_groups_main.author_id IN ('".$bigfriendlist."')) 
		OR bx_groups_main.allow_view_group_to IN ('".$privacy_OthersToMe."') 
		OR bx_groups_main.allow_view_group_to IN ('".$privacy_MeToOthers."'))
		ORDER BY bx_groups_main.created DESC ".$Limit."";
		$resultat = mysql_query($bring) or die("<br />REQUETE2<br />Your Query: " . $bring . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());
		
		return $resultat;


	}






