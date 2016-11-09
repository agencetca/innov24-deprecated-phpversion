<?php

require_once('../../../inc/header.inc.php'); //BDD
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php'); //LKEYS
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_popups_right_side/bugsreport/config.php');
require_once(BX_DIRECTORY_PATH_ROOT.'tig_popups_right_side/bugsreport/Modele/data.php');

?>


<html>
	<head>
		<link href="http://localhost/innov24/tig_popups_right_side/bugsreport/Vue/style.css" rel="stylesheet" type="text/css">
	</head>
	<body>

		<div class="content">
			
			<div class="title0"><h1><?php echo _t($BigTitle);?></h1></div>

			<div class="top1">
			<h2><?php echo _t($Titre_colonne_1);?></h2>
				<div class="text_top"><?php echo _t($Texte_colonne_1);?></div>
				<form method="post" action = "modules/andrew/fchat/bugsreport/bug_upload.php" enctype="multipart/form-data">
					<span>Optional Comment : </span><br /><textarea name="comment" value="" cols="32" rows="10" style="margin-bottom: 5px; margin-top: 2px;"></textarea><br />
					<input type="file" name="bug_" accept="image/*">
					<input type="submit">
				</form>
				<div class="text_top"><?php echo _t($Texte_colonne_1_tips);?></div>
			</div>

			<div class="top2">
			<h2><?php echo _t($Titre_colonne_2);?></h2>
				<div class="text_top"><?php echo _t($Texte_colonne_2);?></div>
			</div>
	
			<div class="top3">
			<h2><?php echo _t($Titre_colonne_3);?></h2>
				<div class="text_top"><?php echo _t($Texte_colonne_3);?></div>

			<?php
			
				$innov24_dev = innov24_dev();
				while ($resultat = mysql_fetch_assoc($innov24_dev)) {
				
				//On défini l'image de profil qui va s'afficher
				$pic_path = BX_DOL_URL_MODULES.'boonex/avatar/data/images/'.$resultat['Avatar'].'.jpg';
				$pic_default = 'templates/tmpl_oounisoft/images/icons/man_medium.gif';
				$pic_validation = @getimagesize($pic_path);
				if(!is_array($pic_validation)){
				$UserPic = $pic_default;
				}else{
				$UserPic = $pic_path;
				}
				$Nom = $resultat['FirstName'].' '.$resultat['LastName'];
				$Cie = $resultat['cie'];
				$Skills = $resultat['skills'];
				$LienProfil = getprofilelink($resultat['ID']); 
				//A rajouter : $LienOffers
				echo '
				
				<div style="display:inline-blocks">
				
				<ul>
					<li style= "list-style-type: none; margin-left:-40px;">
					
					<div class="container">';
					
echo'					<a href="'.$LienProfil.'" style="text-decoration: none;"><img class="photo" src="'.$UserPic.'"/>
						<div class="identite">
						'.$Nom.'<br />'.$Cie.'
						</div>
						<div class="skills">
						'.$Skills.'
						</div></a>
						<a href="'.$LienProfil.'" style="text-decoration: none;"><div class="lienprofil">
						<div class="lien_texte">'._t($Texte_du_bouton_voir_profil).'</div>
						</div></a>
					</div>
					
					</li>
				</ul>
				
				</div>
				
				
				
				';
				}
			
			
			
			?>
			
			</div>
			
		</div>

	<button type="button" class="button_cancel" onclick="cancel()">Close this window</button>

	</body>
</html>