<?php

session_start();

echo 'plop';

require_once('../../inc/header.inc.php'); //BDD
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php'); //LKEYS
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_AAA_mutual_ressources/main_config.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_store_home/Controleur/functions.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_store_home/Modele/requests.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_store_home/config.php');

$memberiID = getID($_GET['ID']);
$ancien_nb_total = $_SESSION['nb_total_' . $nom_module .''];
$storechoice = $_SESSION['' . $nom_module . '_current_position'];

// if(isset($ancien_nb_total) AND isset($storechoice)){

//On va chercher les paramètres de la recherche
$param = conditions_de_recherche($storechoice);

//On recupere les settings utilisateurs
$innov24_config = innov24_config($memberiID);
$bigfriendlist = bigfriendlist($memberiID);
$privacy_OthersToMe = privacy_OthersToMe($memberiID);
$privacy_MeToOthers = privacy_MeToOthers($memberiID);

//On execute la requete visant à connaitre le nombre total de nouveaux posts
$nouveau_nb_total = count_it($default_behaviour,$param,$innov24_config,$bigfriendlist,$privacy_OthersToMe,$privacy_MeToOthers);

$new_messages = $nouveau_nb_total - $ancien_nb_total;

	if ($new_messages==1){
	echo '<div class="new_msg_box">
			<span class="msgtextestyle">';
				echo _t('msg_box_part_one');
				echo'</span>
			<span class="msgintstyle"> ';
				echo $new_messages;
				echo '</span>
			<span class="msgtextestyle">';
				echo _t('msg_box_part_two_single');
				echo'</span>&nbsp;<a style="cursor:pointer;" OnClick="javascript:window.location.reload()">Refresh</a>
		</div>';

	}
	elseif ($new_messages>1){
	echo '<div class="new_msg_box">
			<span class="msgtextestyle">';
				echo _t('msg_box_part_one');
				echo'</span>
			<span class="msgintstyle">';
				echo $new_messages;
				echo '</span>
			<span style="cursor:pointer;">';
				echo _t('msg_box_part_two_many');
				echo'</span>&nbsp;<a style="cursor:pointer;" OnClick="javascript:window.location.reload()">Refresh</a>
		</div>';
	}

// }//fin des if(isset)
























