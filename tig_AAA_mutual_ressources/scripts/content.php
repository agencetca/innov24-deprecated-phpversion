<?php

echo '
<div class="boxcontent">

	<div class="topElement">
		<div class="typeBIGbox">
			<a href="'.$LienProfil.'">
				<div class="avatarbox">
					<div class="ownerNamebox">
						<div class="firstandlastnamebox"><p class="firstandlastnamestyle">'.$Prenom.'&nbsp;'.$Nom.'</p></div>
						<div class="profiletypebox"><p class="profiletypestyle">'.$ProfileType.'</p></div>
					</div>
					<div class="picbox">
						<img src="'.$UserPic.'" alt="" height="64" width="64">
					</div>
				</div>
			</a>
		</div>
		<div class=typebox>
			<p class="typestyle">'.$SurCategorie.'/'.$CategorieType.'</p>
			<div class="payment_status">'.$PaiementStatut.'</div>
		</div>
	</div>

	<hr class="line">

	<a href="'.$LienPost.'">
		<div class="middleElement">
			<div class="bxcategorybox">
				<p class="bxcategorystyle">'.$Secteurs.'</p>
			</div>
			<div class="titlebox">
				<p class="titlestyle">'.$Titre_du_Post.'</p>
			</div>
			<div class="descrbox">
				'.$PostPhoto.'
				<p class="descrstyle">'.$snip.'</p>
			</div>
			<div class="datebox">
				'.$Date.'
			</div>
		</div>
	</a>

	<hr class="line">

	<div class="bottomElement">';
echo	$bouton_de_partage;  
echo'	<div class="nbr_share">
			<div id="nbr_share_text'.$i.'" class="nbr_share_text">'.$Nbr_de_partages.'</div>
		</div>
		<div id="sharenum'.$i.'" style="position:relative; float:left; font-family:arial; font-size:12px; font-weight:bold; font-style:normal; text-decoration:none; text-align:center; text-shadow:1px 1px 0px #ffffff; margin-left: 5%; margin-top: 3px;"></div>
		<div class="privacybox">'.$PrivacyDiv.'</div>
		<div class="blablabox">
			<div class="blablapic"></div>
			<p class="blablacount">'.$Nbr_de_commentaires.'</p>
		</div>
		<div class="fansbox">
			<div class="viewspic"></div>
		</div>
		<p class="fanscount">'.$nombre_de_vues.'</p>
	</div>

</div>';


