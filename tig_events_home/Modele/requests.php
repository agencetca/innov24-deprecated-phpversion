<?php

session_start();


	function count_it($default_behaviour,$param,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers) {

    //----------------------- REQUETE QUI COMPTE -------------------------------

		$count = "SELECT count(distinct bx_events_main.ID) FROM bx_events_main
		INNER JOIN Profiles ON bx_events_main.ResponsibleID=Profiles.ID AND Profiles.Status='Active' 
		INNER JOIN sys_pre_values ON bx_events_main.Type=sys_pre_values.Value 
		WHERE (bx_events_main.Type='".$param."')
		AND (".$default_behaviour.")
		AND bx_events_main.Categories IN ('".$innov24_config."') 
		AND (bx_events_main.allow_view_event_to=3 
		OR bx_events_main.allow_view_event_to=4 
		OR (bx_events_main.allow_view_event_to=5 
		AND bx_events_main.ResponsibleID IN ('".$bigfriendlist."')) 
		OR bx_events_main.allow_view_event_to IN ('".$privacy_OthersToMe."') 
		OR bx_events_main.allow_view_event_to IN ('".$privacy_MeToOthers."'))";
		$resultat = mysql_query($count);
		$total = mysql_fetch_array($resultat);
		$nb_total = $total[0];
		mysql_free_result ($resultat);
		
		return $nb_total;

	}

	function bring_it($default_behaviour,$param,$Limit,$Where,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers) {

    //----------------------- REQUETE QUI RAMENE LES POSTS -------------------------------

		$bring = "SELECT DISTINCT sys_pre_values.LKey,bx_events_main.ID AS PostID, bx_events_main.Shared, bx_events_main.ResponsibleID, bx_events_main.EntryUri,bx_events_main.Title,bx_events_main.Description,bx_events_main.Categories,bx_events_main.Date,bx_events_main.Views,bx_events_main.CommentsCount,bx_events_main.allow_view_event_to,bx_events_main.PrimPhoto,Profiles.Avatar,Profiles.ID,Profiles.FirstName,Profiles.LastName,Profiles.ProfileType
		FROM bx_events_main 
		INNER JOIN Profiles ON bx_events_main.ResponsibleID=Profiles.ID AND Profiles.Status='Active' 
		INNER JOIN sys_pre_values ON bx_events_main.Type=sys_pre_values.Value 
		WHERE ".$Where." (bx_events_main.Type='".$param."')
		AND (".$default_behaviour.")
		AND bx_events_main.Categories IN ('".$innov24_config."') 
		AND (bx_events_main.allow_view_event_to=3 
		OR bx_events_main.allow_view_event_to=4 
		OR (bx_events_main.allow_view_event_to=5 
		AND bx_events_main.ResponsibleID IN ('".$bigfriendlist."')) 
		OR bx_events_main.allow_view_event_to IN ('".$privacy_OthersToMe."') 
		OR bx_events_main.allow_view_event_to IN ('".$privacy_MeToOthers."'))
		ORDER BY bx_events_main.Date DESC ".$Limit."";
		$resultat = mysql_query($bring) or die("<br />REQUETE2<br />Your Query: " . $bring . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());
		
		return $resultat;


	}






