<?php

require_once('../../inc/header.inc.php'); //BDD
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php'); //LKEYS
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');

$memberiID = getID($_GET['ID']);
$identifiant = $_GET['identifiant'];

$request = "SELECT afk_cfgme_interest.cat_id
FROM afk_cfgme_interest
WHERE afk_cfgme_interest.user_id=".$memberiID."";
$resultat = mysql_query($request);

$categories = mysql_fetch_assoc($resultat);
$category = $categories['cat_id'];

mysql_free_result ($resultat);

//On verifie que les categories existent, sinon on crée la structure de base
if ($category == NULL) {
$category = "Events}News}Jobs}Groups}Store}";
}

//On enregistre la categorie qui nous interesse
$Pattern = '(.*?)('.$identifiant.'\{?.*?\})(.*\{?.*?\})';
$Cat = preg_replace("#".$Pattern."#","$2",$category);

//On enregistre ce qui est avant

$Pattern = '(.*?)('.$identifiant.'\{?.*?\})(.*\{?.*?\})';
$FirstPart = preg_replace("#".$Pattern."#","$1",$category);

//On enregistre ce qui est après

$Pattern = '(.*?)('.$identifiant.'\{?.*?\})(.*\{?.*?\})';
$LastPart = preg_replace("#".$Pattern."#","$3",$category);

//On vide les catégories du module sélectionnée et on reconstruit la chaine

$Cat = ''.$identifiant.'}';
$BigString = $FirstPart.$Cat.$LastPart;

$update = 'UPDATE afk_cfgme_interest
SET afk_cfgme_interest.cat_id = "'.$BigString.'"
WHERE afk_cfgme_interest.user_id='.$memberiID.'';
$update_final = mysql_query($update);