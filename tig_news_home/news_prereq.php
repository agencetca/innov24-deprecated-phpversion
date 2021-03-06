<?php

session_start();

require_once(BX_DIRECTORY_PATH_ROOT.'tig_news_home/Controleur/functions.php');

//====================================================================================================================//
							//CONFIGURATION DES POSTS DU WALL
//====================================================================================================================//

				//On récupère le lien qui mène au profil de l'auteur du post
				$LienProfil = getProfileLink($contenu['OwnerID']);

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
				$LienPost = "blogs/entry/".$contenu['PostUri']."";

				//On prépare l'affichage des sur-catégories (les 'Types' de fawn)
				$SurCategorie = _t('_bx_blog_Blogs');
				$CategorieType = _t($contenu['LKey']);

				//On prépare l'affichage des catégories Boonex qui ont été choisies
				$Secteurs = preg_replace('#[;]#', ' - ', $contenu['Categories']);


				//On va chercher le titre du post
				$Titre_du_Post = $contenu['PostCaption'];

				//On défini l'apperçu (snippet) du texte
				$snip = (strlen($contenu['PostText']) > 500) ? substr($contenu['PostText'], 0, 500) . '...' : $contenu['PostText'];
				$snip = strip_tags($snip);

				//S'il y a une date associé à ce type de post, on construit son affichage sur une ligne ici (sinon commenter la ligne)
				$Date = '<p class="datestyle">'._t('_wipM_creation_date').' : '.DataTime($contenu['PostDate'],$dateFormatC).'</p>';

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
				if ($contenu['PostPhoto']!=NULL){
					$PostPhoto = '<div class="blog_photos"><img src="media/images/blog/big_'.$contenu['PostPhoto'].'" width="auto" height="64"/></div>';
				}else{$PostPhoto = '';}

				//On défini la privacy du post et l'image qui doit s'afficher en conséquence
				if ($contenu['allowView'] == 3){
				$PrivacyDiv = '<div class="privacypic4"></div>';
				}
				else if ($contenu['allowView'] == 4){
				$PrivacyDiv = '<div class="privacypic1"></div>';
				}
				else if ($contenu['allowView'] == 5){
				$PrivacyDiv = '<div class="privacypic2"></div>';
				}
				else{
				$PrivacyDiv = '<div class="privacypic3"></div>';
				}

				//On regarde le payment statut du post (s'il n'y en a pas besoin, commenter la ligne)
				$PaiementStatut = _t('_free_news');

				//On préparel'affichage du nombre commentaires
				$Nbr_de_commentaires = $contenu['CommentsCount'];

				//On prépare l'affichage du nombre de vues
				$nombre_de_vues = $contenu['Views'];

				//On prépare l'affichage du nombre de partage
				$Nbr_de_partages = $contenu['Shared'];

				//On prépare les variables qui serviront à partager le post
				$postid[$i]= $contenu['PostID'];
				$recipient_p_link[$i] = preg_replace('#(\x5c)*(\')*(")*#', "", $LienProfil);
				$recipient_p_nick[$i] = preg_replace('#(\x5c)*(\')*(")*#', "", $contenu['FirstName']).' '.preg_replace('#(\')*(")*#', "", $contenu['LastName']);
				$post_url[$i] = 'blogs/entry/'.preg_replace('#(\x5c)*(\')*(")*#', "", $LienProfil);
				$post_caption[$i] = preg_replace('#(\x5c)*(\')*(")*#', "", $Titre_du_Post);
				$profile_who_share[$i] = getNickName($memberiID);
				$link_to_profile_who_share[$i] = getProfileLink($memberiID);
				
				//On configure le bouton de partage		
				$sender[$i] = $memberiID;
				$receiver[$i] = $memberiID;
				$bouton_de_partage = '<button href="#?w=300" rel="popup_share_'.$nom_module.'" class="share_button" onclick="ShareConfirm(\''.$i.'\',\''.$memberiID.'\',\''.$postid[$i].'\',\''.$recipient_p_link[$i].'\',\''.$recipient_p_nick[$i].'\',\''.$link_to_profile_who_share[$i].'\',\''.$profile_who_share[$i].'\',\''.$post_url[$i].'\',\''.$post_caption[$i].'\',\''.$sender[$i].'\',\''.$receiver[$i].'\')">'._t('_wipM_sharebutton').'</button>';


				//DONE! ;)



				
