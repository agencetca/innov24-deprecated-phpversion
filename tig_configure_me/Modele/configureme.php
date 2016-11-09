<?php

require_once('../../inc/header.inc.php'); //BDD
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php'); //LKEYS
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');

$memberiID = getID($_GET['ID']);
$single = $_GET['string'];
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

//On compare la catégorie séletionnée avec le tableau qui lui correspond
$pattern1 = '[{}]';
$pattern2 = '^'.$identifiant.'';
$pattern3 = ',';

$Array = preg_replace("#".$pattern1."#",'',$Cat);
$Array = preg_replace("#".$pattern2."#",'',$Array);
$Array = preg_split("#".$pattern3."#",$Array);


	//On regarde si la catégorie sélectionnée est dans le tableau
	if (array_keys($Array,$single)) {
	//Si oui, cherche la clé associée
	$key = array_search($single,$Array);
	//Et on enlève la ligne
	unset($Array[$key]);
	$Array = array_merge($Array); //on reindexe le tableau grâce à cette technique
	} else {
	//Sinon on la pousse à l'intérieur
	array_push($Array,$single);;
	}


//On retrie le tableau dans l'ordre alphabetique
usort($Array, "strcasecmp");

 
//On met à jour la chaine de caractère liée à la catégorie sélectionnée
$String = implode(",", $Array);

$Pattern = "^,";
$String = preg_replace("#".$Pattern."#","",$String);

if ($String==NULL) {
$Cat = "".$identifiant."}";
} else {
$Cat = "".$identifiant."{".$String."}";
}

//On recréé la string entière en suivant le modèle du configure me d'AFK
$BigString = $FirstPart.$Cat.$LastPart;

$update = 'UPDATE afk_cfgme_interest
SET afk_cfgme_interest.cat_id = "'.$BigString.'"
WHERE afk_cfgme_interest.user_id='.$memberiID.'';
$update_final = mysql_query($update);
















