<?php

session_start();

require_once('../../inc/header.inc.php'); //BDD
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php'); //LKEYS
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');

require_once(BX_DIRECTORY_PATH_ROOT.'tig_groups_home/config.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_groups_home/Controleur/functions.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_groups_home/Modele/requests.php');

$memberiID = getID($_GET['ID']);
$premiere_occurence = $_SESSION['premiere_occurence_'.$nom_module.''];
$nb_total = $_SESSION['nb_total_' . $nom_module .''];

$last_post_read = $_SESSION['last_'.$nom_module.'_read'];
$limit_posts = $_SESSION['limit_posts'];

// On calcul le prochain paquet de posts à ramener
$next_stack = $_SESSION['last_' . $nom_module . '_read'] + $limit_posts;

//On gere le debordement
If ($next_stack > $_SESSION['nbr_total' . $nom_module .'']) {$next_stack = $_SESSION['nbr_total' . $nom_module .''];}

//On va chercher les settings
$param = conditions_de_recherche($_SESSION[''.$nom_module.'_current_position']);
$innov24_config = innov24_config($memberiID);
$bigfriendlist = bigfriendlist($memberiID);
$privacy_OthersToMe = privacy_OthersToMe($memberiID);
$privacy_MeToOthers = privacy_MeToOthers($memberiID);

//On ne recherche que dans le passé
$Limit = "LIMIT ".$_SESSION['last_' . $nom_module . '_read'].",".$next_stack."";
$Where = "bx_groups_main.id <= ".$premiere_occurence." AND ";

//On execute la requete
$resultat = bring_it($default_behaviour,$param,$Limit,$Where,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers);


for ($i=$_SESSION['last_' . $nom_module . '_read'];$i<$next_stack;$i++) {

$contenu = mysql_fetch_assoc($resultat);

				require(BX_DIRECTORY_PATH_ROOT.'tig_groups_home/groups_prereq.php');
				//On affiche l'item
				echo '<li style="list-style-type: none;">';
				require(BX_DIRECTORY_PATH_ROOT.'tig_AAA_mutual_ressources/scripts/content.php');
				echo '<hr class="separation">';
				echo '</li>';
				
				//On met en mémoire le dernier post lu
	 			$_SESSION['last_' . $nom_module . '_read'] = $i+1;

}//fin de for






