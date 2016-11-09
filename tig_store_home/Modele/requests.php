<?php

session_start();


	function count_it($default_behaviour,$param,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers) {

    //----------------------- REQUETE QUI COMPTE -------------------------------

		$count = "SELECT count(distinct bx_store_products.id) 
		FROM bx_store_products
		INNER JOIN Profiles ON (bx_store_products.author_id=Profiles.ID AND Profiles.Status='Active') 
		INNER JOIN sys_pre_values ON bx_store_products.StoreType=sys_pre_values.Value 
		WHERE (bx_store_products.StoreType='".$param."')
		AND (".$default_behaviour.")
		AND bx_store_products.categories IN ('".$innov24_config."') 
		AND (bx_store_products.allow_view_product_to=3 
		OR bx_store_products.allow_view_product_to=4 
		OR (bx_store_products.allow_view_product_to=5 
		AND bx_store_products.author_id IN ('".$bigfriendlist."')) 
		OR bx_store_products.allow_view_product_to IN ('".$privacy_OthersToMe."') 
		OR bx_store_products.allow_view_product_to IN ('".$privacy_MeToOthers."'))";
		$resultat = mysql_query($count);
		$total = mysql_fetch_array($resultat);
		$nb_total = $total[0];
		mysql_free_result ($resultat);
		
		return $nb_total;

	}

	function bring_it($default_behaviour,$param,$Limit,$Where,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers) {

    //----------------------- REQUETE QUI RAMENE LES POSTS -------------------------------

		$bring = "SELECT DISTINCT sys_pre_values.LKey,bx_store_products.id, bx_store_products.Shared, bx_store_products.author_id, bx_store_products.uri,bx_store_products.title,bx_store_products.desc,bx_store_products.categories,bx_store_products.created,bx_store_products.views,bx_store_products.comments_count,bx_store_products.allow_view_product_to,bx_store_products.thumb,Profiles.Avatar,Profiles.ID,Profiles.FirstName,Profiles.LastName,Profiles.ProfileType
		FROM bx_store_products
		INNER JOIN Profiles ON (bx_store_products.author_id=Profiles.ID AND Profiles.Status='Active')
		INNER JOIN sys_pre_values ON bx_store_products.StoreType=sys_pre_values.Value 
		WHERE ".$Where." (bx_store_products.StoreType='".$param."')
		AND (".$default_behaviour.")
		AND bx_store_products.categories IN ('".$innov24_config."') 
		AND (bx_store_products.allow_view_product_to=3 
		OR bx_store_products.allow_view_product_to=4 
		OR (bx_store_products.allow_view_product_to=5 
		AND bx_store_products.author_id IN ('".$bigfriendlist."')) 
		OR bx_store_products.allow_view_product_to IN ('".$privacy_OthersToMe."') 
		OR bx_store_products.allow_view_product_to IN ('".$privacy_MeToOthers."'))
		ORDER BY bx_store_products.created DESC ".$Limit."";
		$resultat = mysql_query($bring) or die("<br />REQUETE2<br />Your Query: " . $bring . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());
		
		return $resultat;


	}






