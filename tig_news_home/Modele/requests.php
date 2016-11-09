<?php

session_start();


	function count_it($default_behaviour,$param,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers) {
	


    //----------------------- REQUETE QUI COMPTE -------------------------------

		$count = "SELECT count(distinct bx_blogs_posts.PostID) FROM bx_blogs_posts
		INNER JOIN Profiles ON bx_blogs_posts.OwnerID=Profiles.ID AND Profiles.Status='Active' 
		INNER JOIN sys_pre_values ON bx_blogs_posts.NewsType=sys_pre_values.Value 
		WHERE (bx_blogs_posts.NewsType='".$param."')
		AND (".$default_behaviour.")
		AND bx_blogs_posts.Categories IN ('".$innov24_config."') 
		AND (bx_blogs_posts.allowView=3 
		OR bx_blogs_posts.allowView=4 
		OR (bx_blogs_posts.allowView=5 
		AND bx_blogs_posts.OwnerID IN ('".$bigfriendlist."')) 
		OR bx_blogs_posts.allowView IN ('".$privacy_OthersToMe."') 
		OR bx_blogs_posts.allowView IN ('".$privacy_MeToOthers."'))";
		$resultat = mysql_query($count);
		$total = mysql_fetch_array($resultat);
		$nb_total = $total[0];
		mysql_free_result ($resultat);
		
		return $nb_total;

	}

	function bring_it($default_behaviour,$param,$Limit,$Where,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers) {

    //----------------------- REQUETE QUI RAMENE LES POSTS -------------------------------

		$bring = "SELECT DISTINCT sys_pre_values.LKey,bx_blogs_posts.PostID, bx_blogs_posts.Shared, bx_blogs_posts.OwnerID, bx_blogs_posts.PostUri,bx_blogs_posts.PostCaption,bx_blogs_posts.PostText,bx_blogs_posts.Categories,bx_blogs_posts.PostDate,bx_blogs_posts.Views,bx_blogs_posts.CommentsCount,bx_blogs_posts.allowView,bx_blogs_posts.PostPhoto,Profiles.Avatar,Profiles.ID,Profiles.FirstName,Profiles.LastName,Profiles.ProfileType
		FROM bx_blogs_posts 
		INNER JOIN Profiles ON bx_blogs_posts.OwnerID=Profiles.ID AND Profiles.Status='Active' 
		INNER JOIN sys_pre_values ON bx_blogs_posts.NewsType=sys_pre_values.Value 
		WHERE ".$Where." (bx_blogs_posts.NewsType='".$param."')
		AND (".$default_behaviour.")
		AND bx_blogs_posts.Categories IN ('".$innov24_config."') 
		AND (bx_blogs_posts.allowView=3 
		OR bx_blogs_posts.allowView=4 
		OR (bx_blogs_posts.allowView=5 
		AND bx_blogs_posts.OwnerID IN ('".$bigfriendlist."')) 
		OR bx_blogs_posts.allowView IN ('".$privacy_OthersToMe."') 
		OR bx_blogs_posts.allowView IN ('".$privacy_MeToOthers."')) 
		ORDER BY bx_blogs_posts.PostDate DESC ".$Limit."";
		$resultat = mysql_query($bring) or die("<br />REQUETE2<br />Your Query: " . $bring . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());
		
		return $resultat;


	}






