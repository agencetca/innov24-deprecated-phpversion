<?php

session_start();

require_once('../../inc/header.inc.php'); //BDD
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php'); //LKEYS
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_AAA_mutual_ressources/main_config.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_groups_home/Controleur/functions.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_groups_home/Modele/requests.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_groups_home/config.php');

//On récupère la catégorie qui a été sélectionnée et on l'enregistre
$_SESSION['' . $nom_module . '_current_position'] = $_GET['int'];
$groupschoice = $_SESSION['' . $nom_module . '_current_position'];

//On va chercher les paramètres de la recherche
$memberiID = getID($_GET['ID']);
$_SESSION['limit_posts'] = $limit_posts;
$tableau = array();
$limit_posts = $_SESSION['limit_posts'];
$nb_total = $_SESSION['nb_total_' . $nom_module .''];
$param = conditions_de_recherche($groupschoice);

//On recupere les settings utilisateurs
$innov24_config = innov24_config($memberiID);
$bigfriendlist = bigfriendlist($memberiID);
$privacy_OthersToMe = privacy_OthersToMe($memberiID);
$privacy_MeToOthers = privacy_MeToOthers($memberiID);

//On reset le dernier message lu dans cette catégorie (pour revenir au premier)
$_SESSION['last_' . $nom_module . '_read'] = 1;

//On execute une prerequete afin de connaitre le nombre total de posts qui peuvent être téléchargés
$nb_total = count_it($default_behaviour,$param,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers);
$_SESSION['nbr_total' . $nom_module .''] = $nb_total;

//On gere le debordement
If ($limit_posts >= $nb_total) {$limit_posts = $nb_total;}

 //Si le nombre de posts n'est pas egal à zero on execute la requete
 if ($nb_total<=0){
 			include BX_DIRECTORY_PATH_ROOT.'tig_AAA_mutual_ressources/scripts/noitem.php';
 			return NULL;
} else {
			//On cree le container qui va recevoir les posts
	echo '<ul id="content" style="padding: 0; margin: 0; outline: 0;">';
			
		echo '<div id="wall">';
			
			$Limit = "LIMIT 0,".$limit_posts."";
			$resultat = bring_it($default_behaviour,$param,$Limit,$SpecialParamWhere,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers);

				//On commence l'affichage, item par item
				for ($i=1;$i<=$limit_posts;$i++) {
				
 	 			//On met en mémoire le dernier post lu
	 			$_SESSION['last_' . $nom_module . '_read'] = $i;
 
				//On lit le résultat de la requete SQL
				$contenu = mysql_fetch_assoc($resultat);
				
 	 			//On pousse dans le tableau l'id dU post
	 			array_push($tableau, $contenu['id']);
 
 				//On réalise le travail préparatoire
				require(BX_DIRECTORY_PATH_ROOT.'tig_groups_home/groups_prereq.php');
 
				//On affiche l'item
				echo '<li style="list-style-type: none;">';
				require(BX_DIRECTORY_PATH_ROOT.'tig_AAA_mutual_ressources/scripts/content.php');
				echo '<hr class="separation">';
				echo '</li>';

				
			} // fin de la boucle for



	//On enregistre l'ID du post le plus ancien
	$_SESSION['premiere_occurence_' . $nom_module .''] = reset($tableau);
	
	//On nettoie la mémoire SQL
	mysql_free_result ($resultat); 

}//fin du else

?>