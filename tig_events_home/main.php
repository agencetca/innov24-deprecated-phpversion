<?php
session_start();

require_once(BX_DIRECTORY_PATH_ROOT.'tig_AAA_mutual_ressources/main_config.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_events_home/Controleur/functions.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_events_home/Modele/requests.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_events_home/config.php');

$memberiID = getID($_GET['ID']);
$_SESSION['limit_posts'] = $limit_posts;
$tableau = array();

//On détermine dans quelle catégorie se trouve l'utilisateur
if(!isset($_SESSION['' . $nom_module . '_current_position'])){
$eventschoice = 0;
}else {$eventschoice = $_SESSION['' . $nom_module . '_current_position'];}

//On va chercher les paramètres de la recherche
$param = conditions_de_recherche($eventschoice);

//On affiche le menu
include(BX_DIRECTORY_PATH_ROOT.'tig_events_home/Vue/menu.php');

//On cree le container qui va recevoir les posts
echo '<ul id="content" style="padding: 0; margin: 0; outline: 0;">';
	echo '<div id="wall">';
	
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
  			echo '</div>';
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
				
 	 			//On pousse dans le tableau l'id du post
	 			array_push($tableau, $contenu['PostID']);

 				//On réalise le travail préparatoire
				require(BX_DIRECTORY_PATH_ROOT.'tig_events_home/events_prereq.php');
 
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

echo'		</div>
<!-- On referme la liste -->
		</ul>';
		
include(BX_DIRECTORY_PATH_ROOT.'tig_events_home/Vue/popups.php');		

echo '</div>';

echo '</body>
</html>';


?>
<script src="tig_events_home/Controleur/wallajax.js"></script>

<script src="tig_events_home/Vue/ajaxbox.js"></script>


<!-- // SHARING -->

<script>
function ShareConfirm(i,b,c,d,e,f,g,h,j) {

	reference = i;
	memberID = b;
	postid = c;

	a1 = d;
	a2 = e;
	a3 = f;
	a4 = g;
		
	a4 = a4.replace(/[àáâãäå]/gi,"a");
	a4 = a4.replace(/[òóôõöø]/gi,"o");
	a4 = a4.replace(/[èéêë]/gi,"e");
	a4 = a4.replace(/ç/gi,"c");
	a4 = a4.replace(/[ìíîï]/gi,"i");
	a4 = a4.replace(/[ùúûü]/gi,"u");
	a4 = a4.replace(/[ÿ]/gi,"y");
	a4 = a4.replace(/[ñ]/gi,"n");
	a4 = a4.replace(/[^a-zA-Z0-9 :;?!$-_%\/@]/g,' ');

	sender = h;
	receiver = j;

	n1 = a1.length;
	n2 = a2.length;
	n3 = a3.length;
	n4 = a4.length;

	nom_module = '<?php echo $nom_module; ?>' ;

	var popID = $('button.share_button[href^=#]').attr('rel'); //Trouver la pop-up correspondante
	var popURL = $('button.share_button[href^=#]').attr('href'); //Retrouver la largeur dans le href

	//Récupérer les variables depuis le lien
	var query= popURL.split('?');
	var dim= query[1].split('&amp;');
	var popWidth = dim[0].split('=')[1]; //La première valeur du lien

	//Faire apparaitre la pop-up et ajouter le bouton de fermeture
	$('#' + popID).fadeIn().css({
	'width': Number(popWidth)
	})

//Récupération du margin, qui permettra de centrer la fenêtre - on ajuste de 80px en conformité avec le CSS
	var popMargTop = ($('#' + popID).height() + 80) / 2;
	var popMargLeft = ($('#' + popID).width() + 80) / 2;

//On affecte le margin
	$('#' + popID).css({
	'margin-top' : -popMargTop,
	'margin-left' : -popMargLeft
	});

//Effet fade-in du fond opaque
	$('body').append('<div id="fade"></div>'); //Ajout du fond opaque noir
	//Apparition du fond - .css({'filter' : 'alpha(opacity=80)'}) pour corriger les bogues de IE
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
	return false;

}//fin de la fonction

	//Fermeture de la pop-up et du fond
	$('button.close, #fade').live('click', function() { //Au clic sur le bouton ou sur le calque...
	$('#fade , #popup_share_'+nom_module+'').fadeOut(function() {
	$('#fade, button.close').remove();  //...ils disparaissent ensemble
});
	return false;
});

// 	$('#popup_share_events').mouseover(function() {
// 	$('body').css( "overflow", "hidden" );
// });
// 
// 	$('#popup_share_events').mouseout(function() {
// 	$('body').css( "overflow", "scroll" );
// });


function share_events() 
{

var lkey = '_ibdw_evowall_bx_event_add_condivisione';

	$.ajax({
	type: "POST",url: "modules/ibdw/evowall/condivisione.php",data:"1="+sender+"&2="+receiver+"&3="+lkey+"&4=a:4:{s:12:doppiequotprofile_linkdoppiequot;s:"+n1+":doppiequot"+a1+"doppiequot;s:12:doppiequotprofile_nickdoppiequot;s:"+n2+":doppiequot"+a2+"doppiequot;s:9:doppiequotentry_urldoppiequot;s:"+n3+":doppiequot"+a3+"doppiequot;s:11:doppiequotentry_titledoppiequot;s:"+n4+":doppiequot"+a4+"doppiequot;}",  
	success: function(data) 
	{}
	}),
	$.get('tig_events_home/Modele/db_share.php', {sender:sender,receiver:receiver,postid:postid,memberID:memberID}, function(data) {
		$.get('tig_events_home/Modele/share_ajax.php', {postid:postid}, function(responseText) {
		$('#nbr_share_text'+reference).html(responseText);
		});
	$('button.close, #fade, #popup_share_'+nom_module+'').fadeOut(1000);
	action_one();
	action_two();
	});
}

function action_one() {

    $('#sharenum'+reference).fadeIn();
    $('#sharenum'+reference).html('<?php echo ''._t('_wipM_sharedbutton').'';?>'); 
    $('#sharenum'+reference).fadeOut('slow'); 
}


function action_two() {
 	$('#share_button'+reference).html('<?php echo ''._t('_wipM_resharebutton').'';?>');
}


function cancel_sharing() {
	//Fermeture du pop-up
	$('button.close, #fade, #popup_share_'+nom_module+'').fadeOut();
}

</script>