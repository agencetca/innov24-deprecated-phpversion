<?php

session_start();

require_once(BX_DIRECTORY_PATH_ROOT.'tig_store_home/Controleur/functions.php');


//====================================================================================================================//
							//CONFIGURATION DES POSTS DU WALL
//====================================================================================================================//

				//On récupère le lien qui mène au profil de l'auteur du post
				$LienProfil = getProfileLink($contenu['author_id']);

				//On récupère les infos de l'auteur du post
				$Prenom = $contenu['FirstName'];
				$Nom = $contenu['LastName'];

				// On défini le Profile Type de l'auteur du post
				switch ($contenu['ProfileType']) {
				case 2:
				$ProfileType = _t('__Journalist');
				break;

				case 4:
				$ProfileType = _t('__Communicator');
				break;

				case 8:
				$ProfileType = _t('__Leader');
				break;
				}

				//On fabrique le lien qui mène au post
				$LienPost = "m/store/view/".$contenu['uri']."";

				//On prépare l'affichage des sur-catégories (les 'Types' de fawn)
				$SurCategorie = _t('_bx_store_Store');
				$CategorieType = _t($contenu['LKey']);

				//On prépare l'affichage des catégories Boonex qui ont été choisies
				$Secteurs = preg_replace('#[;]#', ' - ', $contenu['categories']);


				//On va chercher le titre du post
				$Titre_du_Post = $contenu['title'];

				//On défini l'apperçu (snippet) du texte
				$snip = (strlen($contenu['desc']) > 500) ? substr($contenu['desc'], 0, 500) . '...' : $contenu['desc'];
				$snip = strip_tags($snip);

				//S'il y a une date associé à ce type de post, on construit son affichage sur une ligne ici (sinon commenter la ligne)
				$Date = '<p class="datestyle">'._t('_wipM_creation_date').' : '.DataTime($contenu['created'],$dateFormatC).'</p>';

				//On défini l'image de profil qui va s'afficher
				$pic_path = BX_DOL_URL_MODULES.'boonex/avatar/data/images/'.$contenu['Avatar'].'.jpg';
				$pic_default = 'templates/tmpl_oounisoft/images/icons/man_medium.gif';
				$pic_validation = @getimagesize($pic_path);
				if(!is_array($pic_validation)){
				$UserPic = $pic_default;
				}else{
				$UserPic = $pic_path;
				}

				//On récupère l'image du post, s'il y en a une!
				if ($contenu['thumb']!=NULL){
					$PostPhoto = '<div class="blog_photos"><img src="media/images/blog/big_'.$contenu['thumb'].'" width="auto" height="64"/></div>';
				}else{$PostPhoto = '';}

				//On défini la privacy du post et l'image qui doit s'afficher en conséquence
				if ($contenu['allow_view_product_to'] == 3){
				$PrivacyDiv = '<div class="privacypic4"></div>';
				}
				else if ($contenu['allow_view_product_to'] == 4){
				$PrivacyDiv = '<div class="privacypic1"></div>';
				}
				else if ($contenu['allow_view_product_to'] == 5){
				$PrivacyDiv = '<div class="privacypic2"></div>';
				}
				else{
				$PrivacyDiv = '<div class="privacypic3"></div>';
				}

// 				//On regarde le payment statut du post (s'il n'y en a pas besoin, commenter la ligne)
// 				$PaiementStatut = _t('_free_news');

				//On préparel'affichage du nombre de commentaires
				$Nbr_de_commentaires = $contenu['comments_count'];

				//On prépare l'affichage du nombre de vues
				$nombre_de_vues = $contenu['views'];

				//On prépare l'affichage du nombre de partage
				$Nbr_de_partages = $contenu['Shared'];

				//On prépare les variables qui serviront à partager le post
				$postid[$i]= $contenu['id'];
				$post_url[$i] = 'm/store/view/'.preg_replace('#(\x5c)*(\')*(")*#', "", $contenu['uri']);
				$post_caption[$i] = preg_replace('#(\x5c)*(\')*(")*#', "", $contenu['title']);
				$profile_who_share[$i] = getNickName($memberiID);
				$link_to_profile_who_share[$i] = getProfileLink($memberiID);

				//On configure le bouton de partage	
				$sender[$i] = $memberiID;
				$receiver[$i] = $memberiID;
				$bouton_de_partage = '<button href="#?w=300" rel="popup_share_'.$nom_module.'" class="share_button" onclick="ShareConfirm(\''.$i.'\',\''.$memberiID.'\',\''.$postid[$i].'\',\''.$link_to_profile_who_share[$i].'\',\''.$profile_who_share[$i].'\',\''.$post_url[$i].'\',\''.$post_caption[$i].'\',\''.$sender[$i].'\',\''.$receiver[$i].'\')">'._t('_wipM_sharebutton').'</button>';


				//DONE! ;)



				
