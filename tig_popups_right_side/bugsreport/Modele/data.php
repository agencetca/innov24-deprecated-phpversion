<?php

	function innov24_dev() {

    //----------------------- REQUETE QUI RAMENE LES POSTS -------------------------------

		$i24dev = "SELECT *
		FROM innov24_dev 
		INNER JOIN Profiles ON innov24_dev.idprofile=Profiles.ID
		ORDER BY innov24_dev.position DESC";
		$innov24_dev = mysql_query($i24dev);

		return $innov24_dev;

	}