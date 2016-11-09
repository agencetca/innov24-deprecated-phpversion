<?php


//Nom du module (important!) --no spaces
$nom_module = "Events";

//Nom et lien du bouton de publication
$LangageKey_du_bouton_de_publication = "_Publish";
$Lien_du_boutton_de_publication = BX_DOL_URL_ROOT."modules/?r=events/browse/my&bx_events_filter=add_event";

//Nom et lien du bouton "my items"
$LangageKey_du_bouton_my_items = "My_Events";
$Lien_du_boutton_my_items = BX_DOL_URL_ROOT."m/events/browse/my";

//Lkey des catégories
$all_categories = "_All_publications";
$categorie1 = "_Articles";
$categorie2 = "_Columns";
$categorie3 = "_Releases";
$categorie4 = "_Pouet";
$categorie5 = "_Plop";
$categorie6 = "_Prout";
$categorie7 = "_Lol";
$categorie8 = "_Innov24";

//Lkey du bouton "More View"
$more_view = "_more_view";


//===================================================================//
							//Requetes
							
$default_behaviour = "bx_events_main.Open=1 
AND bx_events_main.Status='approved'";							

//===================================================================//