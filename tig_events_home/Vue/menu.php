<?php
session_start();
?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="tig_AAA_mutual_ressources/home.css">
		
			<script>
			function getChoice(int) {
			if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
			}
			else {
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("wall").innerHTML=xmlhttp.responseText;
			}
			}
			xmlhttp.open("GET","tig_events_home/Modele/chooser.php?int="+int,false);
			xmlhttp.send();
			}
			</script>
		
	</head>
	<body>


<div class="spacer20px"></div>
<div class=top_container>

<?php 

//Boutton de publication
echo'
	<a href="'.$Lien_du_boutton_de_publication.'">
		<button type="button" class="publish_button">'._t($LangageKey_du_bouton_de_publication).'</button>
	</a>
';

//Boutton "My Items"
echo'
	<a href="'.$Lien_du_boutton_my_items.'">
		<button type="button" class="my_items_button">'._t($LangageKey_du_bouton_my_items).'</button>
	</a>
';

//Menu de navigation

	switch ($_SESSION['' . $nom_module . '_current_position']) {
	  case '0':
		$selected0=' selected="selected"';
		$selected1='';
		$selected2='';
		$selected3='';
		$selected4='';
		$selected5='';
		$selected6='';
		$selected7='';
		$selected8='';
		break;
	  case '1':
		$selected0='';
		$selected1=' selected="selected"';
		$selected2='';
		$selected3='';
		$selected4='';
		$selected5='';
		$selected6='';
		$selected7='';
		$selected8='';
		break;
	  case '2':
		$selected0='';
		$selected1='';
		$selected2=' selected="selected"';
		$selected3='';
		$selected4='';
		$selected5='';
		$selected6='';
		$selected7='';
		$selected8='';
		break;
	  case '3':
		$selected0='';
		$selected1='';
		$selected2='';
		$selected3=' selected="selected"';
		$selected4='';
		$selected5='';
		$selected6='';
		$selected7='';
		$selected8='';
		break;
	  case '4':
		$selected0='';
		$selected1='';
		$selected2='';
		$selected3='';
		$selected4=' selected="selected"';
		$selected5='';
		$selected6='';
		$selected7='';
		$selected8='';
		break;
	  case '5':
		$selected0='';
		$selected1='';
		$selected2='';
		$selected3='';
		$selected4='';
		$selected5=' selected="selected"';
		$selected6='';
		$selected7='';
		$selected8='';
		break;
	  case '6':
		$selected0='';
		$selected1='';
		$selected2='';
		$selected3='';
		$selected4='';
		$selected5='';
		$selected6=' selected="selected"';
		$selected7='';
		$selected8='';
		break;
	  case '7':
		$selected0='';
		$selected1='';
		$selected2='';
		$selected3='';
		$selected4='';
		$selected5='';
		$selected6='';
		$selected7=' selected="selected"';
		$selected8='';
		break;

	  case '8':
		$selected0='';
		$selected1='';
		$selected2='';
		$selected3='';
		$selected4='';
		$selected5='';
		$selected6='';
		$selected7='';
		$selected8=' selected="selected"';
		break;

	  default:
		$selected0=' selected="selected"';
		$selected1='';
		$selected2='';
		$selected3='';
		$selected4='';
		$selected5='';
		$selected6='';
		$selected7='';
		break;
	}
	

//Pour mettre à jour les variables Lkeys de catégories, voir config.php

echo '<div class="dropdown">';
echo '<select id="chooseform" name="selection" onchange="getChoice(this.value)">
        <option value="0"'.$selected0.'>'._t($all_categories).'</option>
        <option value="1"'.$selected1.'>'._t($categorie1).'</option>
        <option value="2"'.$selected2.'>'._t($categorie2).'</option>
        <option value="3"'.$selected3.'>'._t($categorie3).'</option>
        <option value="4"'.$selected4.'>'._t($categorie4).'</option>
        <option value="5"'.$selected5.'>'._t($categorie5).'</option>
        <option value="6"'.$selected6.'>'._t($categorie6).'</option>
        <option value="7"'.$selected7.'>'._t($categorie7).'</option>
        <option value="8"'.$selected8.'>'._t($categorie8).'</option>
      </select>';
echo '</div>';



?>
</div>

<!-- Alert Box -->
<div class="new_msg_container">
	<?php
	include BX_DIRECTORY_PATH_ROOT.'tig_AAA_mutual_ressources/scripts/alertbox.php';
	?>
</div>
