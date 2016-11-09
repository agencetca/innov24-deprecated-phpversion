<?php
/************************************************
	The Search PHP File
************************************************/


/************************************************
	MySQL Connect
************************************************/

require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
//REQUETE
mysql_query("SET NAMES 'utf8'");

/*************************************************
Securite Boonex
/*************************************************/
$memberID = getID($_GET['ID']);
check_logged($memberID);
/************************************************
	DateTime Functionality
************************************************/
function DateTime($dataTime) { 
$date = date("j/m/Y", $dataTime); $hour = date("H:i", $dataTime); 
$yesterday = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))); 
$today = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d"), date("Y"))); 
$tomorrow = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))); 
$aftertomorrow = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 2, date("Y"))); 
$inaweek = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 7, date("Y"))); if ($date == $yesterday) 
$dateDisplay = _t('_datatimefunction_yesterday'); elseif ($date == $today) 
$dateDisplay = _t('_datatimefunction_today'); elseif ($date == $tomorrow) 
$dateDisplay = _t('_datatimefunction_tomorrow'); elseif ($date == $aftertomorrow) 
$dateDisplay = _t('_datatimefunction_after'); elseif ($date == $inaweek) 
$dateDisplay = _t('_datatimefunction_week'); else $dateDisplay = $date; 
return("$dateDisplay $hour"); }
/************************************************
	Search Functionality
************************************************/

// Define Output HTML Formating
//Profiles
$html = '';
$html .= '<li class="result">';
$html .= '<a target="_blank" href="urlString">';
$html .= '<div style="width:50%; display: inline-block">
<h3>emailString</h3>
<h3><font color=#FF9900>'._t(_search_pseudo).': </font>nickString</h3>
<h3>bignameString</h3>
<h3>corpString</h3>
<h3>protypeString</h3>
<h3>flagString paysString, cityString</h3>
</div><div style="width:50%; display: inline-block;" align="right">
<div style="width:50%; display: inline-block">
<img src="http://localhost/innov24/Search/images/profil.png"></img></div>
<div style="width:50%; float: inline-block"><font color="3CFF00">'._t(_search_profil).'</font>
<div style="margin-bottom=30px";>points : pointsString</div></div>';
$html .= '</a>';
$html .= '</li>';

//events
$html2 = '';
$html2 .= '<li class="result">';
$html2 .= '<a target="_blank" href="urlStringevt">';
$html2 .= '<div style="width:100%; display: inline-block">
<div style="width:40%;height:100%; display: inline-block">
<h3><font color=#FF9900>'._t(_search_category_evt).': </font>evtcatString</h3>
<h3><font color=#FF9900>'._t(_search_titre_evt).': </font>evttitString</h3>
<h3>evtautString  (evtnickString)</h3>
<h3>evtflagString evtpaysString, evtcityString</h3>
<h3>evtstartString</h3>
<h3>evtendString</h3>

</div>
<div style="width:59%; display:inline-block;" align="right">
<div style="width:50%; display: block">
<img src="http://localhost/innov24/Search/images/calendrier.png"></img></div>
<div style="width:50%; display: block">
<font color="3CFF00">'._t(_search_events).'</font>
<div style="margin-bottom:20px;">Fans : evtfansString</div>
</div>
</div></div>';

$html2 .= '</a>';
$html2 .= '</li>';
//Groups
$html3 = '';
$html3 .= '<li class="result">';
$html3 .= '<a target="_blank" href="urlStringgrp">';
$html3 .= '<div style="width:100%; display: inline-block">
<div style="width:40%;height:100%; display: inline-block">
<h3><font color=#FF9900>'._t(_search_category_grp).': </font>grpcatString</h3>
<h3><font color=#FF9900>'._t(_search_titre_grp).': </font>grptitString</h3>
<h3>grpautString  (grpnickString)</h3>
<h3>grpflagString grppaysString, grpcityString</h3>
<h3>grpstartString</h3>
<h3>grpendString</h3>

</div>
<div style="width:59%; display:inline-block;" align="right">
<div style="width:50%; display: block">
<img src="http://localhost/innov24/Search/images/groups.png"></img></div>
<div style="width:50%; display: block">
<font color="3CFF00">'._t(_search_groups).'</font>
<div style="margin-bottom:20px;">Fans : grpfansString</div>
</div>
</div></div>';

$html3 .= '</a>';
$html3 .= '</li>';
//NEWS(Blogs)
$html4 = '';
$html4 .= '<li class="result">';
$html4 .= '<a target="_blank" href="urlStringnews">';
$html4 .= '<div style="width:100%; display: inline-block">
<div style="width:40%;height:100%; display: inline-block">
<h3><font color=#FF9900>'._t(_search_category_news).': </font>newscatString</h3>
<h3><font color=#FF9900>'._t(_search_titre_news).': </font>newstitString</h3>
<h3>newsautString  (newsnickString)</h3>
<h3>newsflagString newspaysString, newscityString</h3>
<h3>newsstartString</h3>

</div>
<div style="width:59%; display:inline-block;" align="right">
<div style="width:50%; display: block">
<img src="http://localhost/innov24/Search/images/news.png"></img></div>
<div style="width:50%; display: block">
<font color="3CFF00">'._t(_search_news).'</font>
<div style="margin-bottom:20px;">Views : newsfansString</div>
</div>
</div></div>';

$html4 .= '</a>';
$html4 .= '</li>';

//PRODUCTS(Store)
$html5 = '';
$html5 .= '<li class="result">';
$html5 .= '<a target="_blank" href="urlStringsto">';
$html5 .= '<div style="width:100%; display: inline-block">
<div style="width:40%;height:100%; display: inline-block">
<h3><font color=#FF9900>'._t(_search_category_sto).': </font>stocatString</h3>
<h3><font color=#FF9900>'._t(_search_titre_sto).': </font>stotitString</h3>
<h3>stoautString  (stonickString)</h3>
<h3>stoflagString stopaysString, stocityString</h3>
<h3>stostartString</h3>

</div>
<div style="width:59%; display:inline-block;" align="right">
<div style="width:50%; display: block">
<img src="http://localhost/innov24/Search/images/sto.png"></img></div>
<div style="width:50%; display: block">
<font color="3CFF00">'._t(_search_sto).'</font>
<div style="margin-bottom:20px;">Views : stofansString</div>
</div>
</div></div>';

$html5 .= '</a>';
$html5 .= '</li>';


// Get Search
$search_string = preg_replace("#[^\s\p{L}\$&\#:!@]#u", " ", $_POST['query']);
$search_string = mysql_real_escape_string($search_string);
//var_dump($search_string);

// Check Length More Than One Character
a:
if (strlen($search_string) >= 1 && $search_string !== ' ') {	// Build Query

///////////////////////////////GOURMANDISES//////////////////////////////////////////
/////////////////////////#1 SOUS L'OCEAN/////////////////////////////////////////////
if ((preg_match("#^(ocean:)#",$search_string))==true){
$search_string = preg_replace("#^(ocean\:)#","",$search_string);
$search_string = mysql_real_escape_string($search_string);
echo '<object type="audio/mpeg" data="Search/sons/sousocean.mp3" height="0" width="0">
<param name="filename" value="Search/sons/sousocean.mp3" />
<param name="autostart" value="true" />
<param name="loop" value="true" />
</object>';
goto a;
}
//////////////////////////////#2 ANTI SCRIPT//////////////////////////////////
if ((preg_match("#^(<script)#",$search_string))==true){
$search_string = preg_replace("#^(<script)#","",$search_string);
$search_string = mysql_real_escape_string($search_string);
echo'<script>
function nomlama(){
var nom = prompt("Bonjour Mr Le H4cK3rZ veuillez vous identifier :","Prenom du Lam3rZ");
if ((nom==null)|(nom=="Prenom du Lam3rZ")) {nomlama();}
else
{
compte=0;
for (var i=1500;i>compte;i--)
{
alert("Bienvenue ! "+ nom +" Plus que "+ i +" clicks a effectuer ! Mouhahahaha!");
}
}}
nomlama();
</script>';
return null;
}
/////////////////////////////DEBUT PROFIL/////////////////////////////////////////////////
if(($search_string[0]=='@')or((preg_match("#^(profile:)|^(@)#",$search_string))==true)){
$search_string = preg_replace("#^(profile\:)|^(@)#","%",$search_string);
$search_string = mysql_real_escape_string($search_string);
//var_dump($search_string);
	//Query regex pour Profiles
	$query_nick= 'SELECT Profiles.Email,Profiles.ProfileType,Profiles.City,Profiles.NickName,Profiles.Company,Profiles.LastName,Profiles.FirstName,
	aqb_pts_profile_types.Name,aqb_pts_profile_types.ID,Profiles.AqbPoints,aqb_pts_profile_types.Obsolete,Profiles.Country as count,sys_countries.Country,sys_countries.ISO2
	FROM Profiles,aqb_pts_profile_types,sys_countries 
	WHERE aqb_pts_profile_types.ID=Profiles.ProfileType
	AND aqb_pts_profile_types.Obsolete = 0 AND
	sys_countries.ISO2 = Profiles.Country AND
	(aqb_pts_profile_types.Name LIKE "%'.$search_string.'%"
	OR Profiles.NickName LIKE "%'.$search_string.'%"
	OR Profiles.City LIKE "%'.$search_string.'%"
	OR Profiles.Email LIKE "%'.$search_string.'%" 
	OR Profiles.Company LIKE "%'.$search_string.'%"
	OR sys_countries.Country LIKE "%'.$search_string.'%"
	OR CONCAT (Profiles.LastName," ",Profiles.FirstName) 
	LIKE "%'.$search_string.'%" 
	OR CONCAT (Profiles.FirstName," ",Profiles.LastName) LIKE "%'.$search_string.'%") 
	ORDER BY Profiles.AqbPoints DESC
	LIMIT 7';	 
	
	// Do Search Nicks
	$result = mysql_query($query_nick)or die(mysql_error());
	while($results = mysql_fetch_array($result)) {
		$result_array[] = $results;
	}
	// Check If We Have nick Results
	if (isset($result_array)) {
		foreach ($result_array as $result) {
				
				// Bypass le SQL + Insérer NomPrenom dans Tableau
				
				$PrenomNom = $result['FirstName'].' '.$result['LastName'];
				$NomPrenom = $result['LastName'].' '.$result['FirstName'];
				
				$tab= array();
				array_push($tab,$PrenomNom);
				$nbtot=(count($tab)-1)/2;
				
				$tab2=array();
				array_push($tab2,$NomPrenom);
				$nbtot2=(count($tab2)-1)/2;
				
				if(strtolower($search_string)==strtolower($tab[$nbtot])){
				$search_string=$NomPrenom;
				$tab[$nbtot]=$tab2[$nbtot2];
				$display_nom_prenom = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab[$nbtot]);
				}
				else{
				$display_nom_prenom = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab2[$nbtot2]);
				}
			//////////////////AFFICHAGE PROFILE/////////////////////////////
			$display_nick = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['NickName']);
			$display_profile_type = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['Name']);
			$display_pays = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['Country']);
			$display_city = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['City']);
			$display_flag = genFlag($result['count']);
			$display_points = $result['AqbPoints'];
			$display_company = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['Company']);
			$display_email = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['Email']);
			$display_url = 'http://localhost/innov24/'.urlencode($result['NickName']);
			
			//Hack pour l'encodage
			if($search_string[0]=='%'){
			$search_string = preg_replace("#^([%])#","",$search_string);
			}
			
			// Insert pour nickname
			$output = str_replace('emailString', $display_email, $html);
			$output = str_replace('nickString', $display_nick, $output);
			$output = str_replace('pointsString',$display_points, $output);
			$output = str_replace('paysString',$display_pays, $output);
			$output = str_replace('cityString',$display_city, $output);
			$output = str_replace('flagString',$display_flag, $output);
			$output = str_replace('corpString', $display_company, $output);
			$output = str_replace('protypeString', $display_profile_type, $output);
			$output = str_replace('bignameString', $display_nom_prenom, $output); 
			$output = str_replace('urlString', $display_url, $output);
			// Output
			echo($output);
		}
	}
	return null;
	}
	else{
	//Query nick normale
	$query_nick= 'SELECT Profiles.Email,Profiles.ProfileType,Profiles.City,Profiles.NickName,Profiles.Company,Profiles.LastName,Profiles.FirstName,
	aqb_pts_profile_types.Name,aqb_pts_profile_types.ID,Profiles.AqbPoints,aqb_pts_profile_types.Obsolete,Profiles.Country as count,sys_countries.Country,sys_countries.ISO2
	FROM Profiles,aqb_pts_profile_types,sys_countries 
	WHERE aqb_pts_profile_types.ID=Profiles.ProfileType
	AND aqb_pts_profile_types.Obsolete = 0 AND
	sys_countries.ISO2 = Profiles.Country AND
	(aqb_pts_profile_types.Name LIKE "%'.$search_string.'%"
	OR Profiles.NickName LIKE "%'.$search_string.'%"
	OR Profiles.City LIKE "%'.$search_string.'%"
	OR Profiles.Email LIKE "%'.$search_string.'%" 
	OR Profiles.Company LIKE "%'.$search_string.'%"
	OR sys_countries.Country LIKE "%'.$search_string.'%"
	OR CONCAT (Profiles.LastName," ",Profiles.FirstName) 
	LIKE "%'.$search_string.'%" 
	OR CONCAT (Profiles.FirstName," ",Profiles.LastName) LIKE "%'.$search_string.'%") 
	ORDER BY Profiles.AqbPoints DESC
	LIMIT 5';	 
	
	// Do Search Nicks
	$result = mysql_query($query_nick)or die(mysql_error());
	while($results = mysql_fetch_array($result)) {
		$result_array[] = $results;
	}
	// Check If We Have nick Results
	if (isset($result_array)) {
		foreach ($result_array as $result) {
				
				// Bypass le SQL + Insérer NomPrenom dans Tableau
				
				$PrenomNom = $result['FirstName'].' '.$result['LastName'];
				$NomPrenom = $result['LastName'].' '.$result['FirstName'];
				
				$tab= array();
				array_push($tab,$PrenomNom);
				$nbtot=(count($tab)-1)/2;
				
				$tab2=array();
				array_push($tab2,$NomPrenom);
				$nbtot2=(count($tab2)-1)/2;
				
				if(strtolower($search_string)==strtolower($tab[$nbtot])){
				$search_string=$NomPrenom;
				$tab[$nbtot]=$tab2[$nbtot2];
				$display_nom_prenom = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab[$nbtot]);
				}
				else{
				$display_nom_prenom = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab2[$nbtot2]);
				}
			//////////////////AFFICHAGE PROFILE/////////////////////////////
			$display_nick = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['NickName']);
			$display_profile_type = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['Name']);
			$display_pays = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['Country']);
			$display_city = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['City']);
			$display_flag = genFlag($result['count']);
			$display_points = $result['AqbPoints'];
			$display_company = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['Company']);
			$display_email = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result['Email']);
			$display_url = 'http://localhost/innov24/'.urlencode($result['NickName']);
			// Insert pour nickname
			$output = str_replace('emailString', $display_email, $html);
			$output = str_replace('nickString', $display_nick, $output);
			$output = str_replace('pointsString',$display_points, $output);
			$output = str_replace('paysString',$display_pays, $output);
			$output = str_replace('cityString',$display_city, $output);
			$output = str_replace('flagString',$display_flag, $output);
			$output = str_replace('corpString', $display_company, $output);
			$output = str_replace('protypeString', $display_profile_type, $output);
			$output = str_replace('bignameString', $display_nom_prenom, $output); 
			$output = str_replace('urlString', $display_url, $output);
			// Output
			echo($output);
		}
	}
	
	}
/////////////////////////////////////////FIN PROFIL///////////////////////////////////////////////
	
///////////////////////////////////////////DEBUT EVENTS//////////////////////////////////////////
if(($search_string[0]=='!')or((preg_match("#^(event:)|^(!)#",$search_string))==true)){
$search_string = preg_replace("#^(event\:)|^(!)#","%",$search_string);
$search_string = mysql_real_escape_string($search_string);
	//Query regex pour events
	$query_event = 'SELECT bx_events_main.Title,bx_events_main.EventStart,bx_events_main.Open,bx_events_main.EventEnd,bx_events_main.EntryUri,
	bx_events_main.City,bx_events_main.ResponsibleID,bx_events_main.Categories,bx_events_main.FansCount,
	Profiles.ID,Profiles.LastName,Profiles.FirstName,Profiles.NickName,bx_events_main.Country as count,sys_countries.Country,sys_countries.ISO2
	FROM bx_events_main,Profiles,sys_countries
	WHERE bx_events_main.ResponsibleID=Profiles.ID
	AND sys_countries.ISO2 = bx_events_main.Country AND
	(CONCAT (Profiles.LastName," ",Profiles.FirstName) LIKE "%'.$search_string.'%" 
	OR CONCAT (Profiles.FirstName," ",Profiles.LastName) LIKE "%'.$search_string.'%"
	OR bx_events_main.Title LIKE "%'.$search_string.'%" 
	OR Profiles.NickName LIKE "%'.$search_string.'%" 
	OR bx_events_main.City LIKE "%'.$search_string.'%"
	OR bx_events_main.Categories LIKE "%'.$search_string.'%"
	OR sys_countries.Country LIKE "%'.$search_string.'%")
	ORDER BY bx_events_main.EventEnd ASC
	LIMIT 7';
	
	// Do Search Events
	$result_evt = mysql_query($query_event)or die(mysql_error());
	while($results_evt = mysql_fetch_array($result_evt)) {
		$result_array_evt[] = $results_evt;
	}
	// Check If We Have evt Results
	
	if (isset($result_array_evt)) {
		foreach ($result_array_evt as $result_evt) {
			
			if(($result_evt['EventEnd']>time())and ($result_evt['Open']==1)){
		
		// Bypass le SQL + Insérer NomPrenom dans Tableau
				
				$PrenomNom = $result_evt['FirstName'].' '.$result_evt['LastName'];
				$NomPrenom = $result_evt['LastName'].' '.$result_evt['FirstName'];
				
				$tab3= array();
				array_push($tab3,$PrenomNom);
				$nbtot3=(count($tab3)-1)/2;
				
				$tab4=array();
				array_push($tab4,$NomPrenom);
				$nbtot4=(count($tab4)-1)/2;
				
				if(strtolower($search_string)==strtolower($tab3[$nbtot3])){
				$search_string=$NomPrenom;
				$tab3[$nbtot3]=$tab4[$nbtot4];
				$display_auteur_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab3[$nbtot3]);
				}
				else{
				$display_auteur_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab4[$nbtot4]);
				}

		//EVT
			//Cut texte du titre
			if(strlen($result_evt['Title'])<30){
			//Si la longueur de la chaine est inférieure à cette valeur, on l'affiche entièrement
			$display_title_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_evt['Title']);
			}
			else{
			//On nettoie la chaine pour qu'elle s'affiche sur une seule ligne, sans balises html, selon une longueur prédéfini
			$long=30;
			$hardcut=substr($result_evt['Title'],0,$long);
			$nospam=preg_replace("#(:?\s)?(:?\n)?(:?\r)?#",'',$hardcut);
			$lowered=ucwords(strtolower($nospam));
			$hardsoft=preg_replace("#(?:<.>)*<*(?:/.*>)*<*#",'',$lowered);
			//Fin du nettoyage
			$display_title_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $hardsoft.'...');
			}	
			//////////////////AFFICHAGE EVENTS/////////////////////////////
			$display_nick_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_evt['NickName']);
			$display_city_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_evt['City']);
			$display_pays_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_evt['Country']);
			$display_flag_evt = genFlag($result_evt['count']);
			$display_fans_evt = $result_evt['FansCount'];
			$display_evt_start = DateTime($result_evt['EventStart'],$dateFormatC);
			$display_evt_end = DateTime($result_evt['EventEnd'],$dateFormatC);
			$display_cat_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_evt['Categories']);
			$display_url_evt = 'http://localhost/innov24/m/events/view/'.urlencode($result_evt['EntryUri']);
			
			//Hack pour l'encodage
			if($search_string[0]=='%'){
			$search_string = preg_replace("#^([%])#","",$search_string);
			}
			
			// Insert pour events
			$output = str_replace('evtcatString',$display_cat_evt, $html2);
			$output = str_replace('evtpaysString',$display_pays_evt, $output);
			$output = str_replace('evtflagString',$display_flag_evt, $output);
			$output = str_replace('evtfansString',$display_fans_evt, $output);
			$output = str_replace('evtstartString',$display_evt_start, $output);
			$output = str_replace('evtendString',$display_evt_end, $output);
			$output = str_replace('evtcityString',$display_city_evt, $output);
			$output = str_replace('evtautString',$display_auteur_evt, $output);
			$output = str_replace('evtnickString',$display_nick_evt, $output);
			$output = str_replace('evttitString', $display_title_evt, $output);
			$output = str_replace('urlStringevt', $display_url_evt, $output);
			// Output
			echo($output);
		}

}
}
return null;
}
else{
//Query normale pour events
	$query_event = 'SELECT bx_events_main.Title,bx_events_main.EventStart,bx_events_main.Open,bx_events_main.EventEnd,bx_events_main.EntryUri,
	bx_events_main.City,bx_events_main.ResponsibleID,bx_events_main.Categories,bx_events_main.FansCount,
	Profiles.ID,Profiles.LastName,Profiles.FirstName,Profiles.NickName,bx_events_main.Country as count,sys_countries.Country,sys_countries.ISO2
	FROM bx_events_main,Profiles,sys_countries
	WHERE bx_events_main.ResponsibleID=Profiles.ID
	AND sys_countries.ISO2 = bx_events_main.Country AND
	(CONCAT (Profiles.LastName," ",Profiles.FirstName) LIKE "%'.$search_string.'%" 
	OR CONCAT (Profiles.FirstName," ",Profiles.LastName) LIKE "%'.$search_string.'%"
	OR bx_events_main.Title LIKE "%'.$search_string.'%" 
	OR Profiles.NickName LIKE "%'.$search_string.'%" 
	OR bx_events_main.City LIKE "%'.$search_string.'%"
	OR bx_events_main.Categories LIKE "%'.$search_string.'%"
	OR sys_countries.Country LIKE "%'.$search_string.'%")
	ORDER BY bx_events_main.EventEnd ASC 
	LIMIT 5';
	
	// Do Search Events
	$result_evt = mysql_query($query_event)or die(mysql_error());
	while($results_evt = mysql_fetch_array($result_evt)) {
		$result_array_evt[] = $results_evt;
	}
	// Check If We Have evt Results
	
	if (isset($result_array_evt)) {
		foreach ($result_array_evt as $result_evt) {
			
			if(($result_evt['EventEnd']>time())and ($result_evt['Open']==1)){
		
		// Bypass le SQL + Insérer NomPrenom dans Tableau
				
				$PrenomNom = $result_evt['FirstName'].' '.$result_evt['LastName'];
				$NomPrenom = $result_evt['LastName'].' '.$result_evt['FirstName'];
				
				$tab3= array();
				array_push($tab3,$PrenomNom);
				$nbtot3=(count($tab3)-1)/2;
				
				$tab4=array();
				array_push($tab4,$NomPrenom);
				$nbtot4=(count($tab4)-1)/2;
				
				if(strtolower($search_string)==strtolower($tab3[$nbtot3])){
				$search_string=$NomPrenom;
				$tab3[$nbtot3]=$tab4[$nbtot4];
				$display_auteur_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab3[$nbtot3]);
				}
				else{
				$display_auteur_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab4[$nbtot4]);
				}
		//EVT
			//Cut texte du titre
			if(strlen($result_evt['Title'])<30){
			//Si la longueur de la chaine est inférieure à cette valeur, on l'affiche entièrement
			$display_title_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_evt['Title']);
			}
			else{
			//On nettoie la chaine pour qu'elle s'affiche sur une seule ligne, sans balises html, selon une longueur prédéfini
			$long=30;
			$hardcut=substr($result_evt['Title'],0,$long);
			$nospam=preg_replace("#(:?\s)?(:?\n)?(:?\r)?#",'',$hardcut);
			$lowered=ucwords(strtolower($nospam));
			$hardsoft=preg_replace("#(?:<.>)*<*(?:/.*>)*<*#",'',$lowered);
			//Fin du nettoyage
			$display_title_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $hardsoft.'...');
			}	
			//////////////////AFFICHAGE EVENTS/////////////////////////////
			$display_nick_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_evt['NickName']);
			$display_city_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_evt['City']);
			$display_pays_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_evt['Country']);
			$display_flag_evt = genFlag($result_evt['count']);
			$display_fans_evt = $result_evt['FansCount'];
			$display_evt_start = DateTime($result_evt['EventStart'],$dateFormatC);
			$display_evt_end = DateTime($result_evt['EventEnd'],$dateFormatC);
			$display_cat_evt = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_evt['Categories']);
			$display_url_evt = 'http://localhost/innov24/m/events/view/'.urlencode($result_evt['EntryUri']);

			// Insert pour events
			$output = str_replace('evtcatString',$display_cat_evt, $html2);
			$output = str_replace('evtpaysString',$display_pays_evt, $output);
			$output = str_replace('evtflagString',$display_flag_evt, $output);
			$output = str_replace('evtfansString',$display_fans_evt, $output);
			$output = str_replace('evtstartString',$display_evt_start, $output);
			$output = str_replace('evtendString',$display_evt_end, $output);
			$output = str_replace('evtcityString',$display_city_evt, $output);
			$output = str_replace('evtautString',$display_auteur_evt, $output);
			$output = str_replace('evtnickString',$display_nick_evt, $output);
			$output = str_replace('evttitString', $display_title_evt, $output);
			$output = str_replace('urlStringevt', $display_url_evt, $output);
			// Output
			echo($output);
		}

}
}
}
///////////////////////////////////////////////////////FIN EVENTS////////////////////////////////////////////
	
///////////////////////////////////////////////////////DEBUT GROUPS/////////////////////////////////////////
if(($search_string[0]=='&')or((preg_match("#^(group:)|^(&)#",$search_string))==true)){
$search_string = preg_replace("#^(group\:)|^(&)#","%",$search_string);
$search_string = mysql_real_escape_string($search_string);
	//Query regex pour groups
	$query_grp = 'SELECT bx_groups_main.title,bx_groups_main.created,bx_groups_main.uri,bx_groups_main.OpenGrp,bx_groups_main.Deadline,
	bx_groups_main.city,bx_groups_main.author_id,bx_groups_main.categories,bx_groups_main.fans_count,
	Profiles.ID,Profiles.LastName,Profiles.FirstName,Profiles.NickName,bx_groups_main.country as countgrp,sys_countries.Country,sys_countries.ISO2
	FROM bx_groups_main,Profiles,sys_countries
	WHERE bx_groups_main.author_id=Profiles.ID
	AND sys_countries.ISO2 = bx_groups_main.country AND
	(CONCAT (Profiles.LastName," ",Profiles.FirstName) LIKE "%'.$search_string.'%" 
	OR CONCAT (Profiles.FirstName," ",Profiles.LastName) LIKE "%'.$search_string.'%"
	OR bx_groups_main.title LIKE "%'.$search_string.'%"  
	OR Profiles.NickName LIKE "%'.$search_string.'%" 
	OR bx_groups_main.city LIKE "%'.$search_string.'%"
	OR bx_groups_main.categories LIKE "%'.$search_string.'%"
	OR sys_countries.country LIKE "%'.$search_string.'%")
	ORDER BY bx_groups_main.Deadline ASC
	LIMIT 7';
	
		//Do Search Groups
	 $result_grp = mysql_query($query_grp)or die(mysql_error());
	while($results_grp = mysql_fetch_array($result_grp)) {
		$result_array_grp[] = $results_grp; 
	}
	
	// Check If We Have groups Results
	 if (isset($result_array_grp)) {
		foreach ($result_array_grp as $result_grp) {
			if(($result_grp['Deadline']>time())and ($result_grp['OpenGrp']==1)){
		
		// Bypass le SQL + Insérer NomPrenom dans Tableau
				
				$PrenomNom = $result_grp['FirstName'].' '.$result_grp['LastName'];
				$NomPrenom = $result_grp['LastName'].' '.$result_grp['FirstName'];
				
				$tab5= array();
				array_push($tab5,$PrenomNom);
				$nbtot5=(count($tab5)-1)/2;
				
				$tab6=array();
				array_push($tab6,$NomPrenom);
				$nbtot6=(count($tab6)-1)/2;
				
				if(strtolower($search_string)==strtolower($tab5[$nbtot5])){
				$search_string=$NomPrenom;
				$tab5[$nbtot5]=$tab6[$nbtot6];
				$display_auteur_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab5[$nbtot5]);
				}
				else{
				$display_auteur_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab6[$nbtot6]);
				}
				//var_dump($tab5[$nbtot5]);
		//GRP
			//Cut texte du titre
			if(strlen($result_grp['title'])<30){
			//Si la longueur de la chaine est inférieure à cette valeur, on l'affiche entièrement
			$display_title_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_grp['title']);
			}
			else{
			//On nettoie la chaine pour qu'elle s'affiche sur une seule ligne, sans balises html, selon une longueur prédéfini
			$long=30;
			$hardcut=substr($result_grp['title'],0,$long);
			$nospam=preg_replace("#(:?\s)?(:?\n)?(:?\r)?#",'',$hardcut);
			$lowered=ucwords(strtolower($nospam));
			$hardsoft=preg_replace("#(?:<.>)*<*(?:/.*>)*<*#",'',$lowered);
			//Fin du nettoyage
			$display_title_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $hardsoft.'...');
			}	
			//////////////////AFFICHAGE GROUPS/////////////////////////////
			$display_nick_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_grp['NickName']);
			$display_city_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_grp['city']);
			$display_pays_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_grp['Country']);
			$display_flag_grp = genFlag($result_grp['countgrp']);
			$display_fans_grp = $result_grp['fans_count'];
			$display_grp_start = DateTime($result_grp['created'],$dateFormatC);
			$display_grp_end = DateTime($result_grp['Deadline'],$dateFormatC);
			$display_cat_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_grp['categories']);
			$display_url_grp = 'http://localhost/innov24/m/groups/view/'.urlencode($result_grp['uri']);
			
			//Hack pour l'encodage
			if($search_string[0]=='%'){
			$search_string = preg_replace("#^([%])#","",$search_string);
			}
			
			// Insert pour groups
			$output = str_replace('grpcatString',$display_cat_grp, $html3);
			$output = str_replace('grppaysString',$display_pays_grp, $output);
			$output = str_replace('grpflagString',$display_flag_grp, $output);
			$output = str_replace('grpfansString',$display_fans_grp, $output);
			$output = str_replace('grpstartString',$display_grp_start, $output);
			$output = str_replace('grpendString',$display_grp_end, $output);
			$output = str_replace('grpcityString',$display_city_grp, $output);
			$output = str_replace('grpautString',$display_auteur_grp, $output);
			$output = str_replace('grpnickString',$display_nick_grp, $output);
			$output = str_replace('grptitString', $display_title_grp, $output);
			$output = str_replace('urlStringgrp', $display_url_grp, $output);
			
			// Output
			echo($output);
		}
		}
}
return null;
}
else
{
//Query normale pour groups
	$query_grp = 'SELECT bx_groups_main.title,bx_groups_main.created,bx_groups_main.uri,bx_groups_main.OpenGrp,bx_groups_main.Deadline,
	bx_groups_main.city,bx_groups_main.author_id,bx_groups_main.categories,bx_groups_main.fans_count,
	Profiles.ID,Profiles.LastName,Profiles.FirstName,Profiles.NickName,bx_groups_main.country as countgrp,sys_countries.Country,sys_countries.ISO2
	FROM bx_groups_main,Profiles,sys_countries
	WHERE bx_groups_main.author_id=Profiles.ID
	AND sys_countries.ISO2 = bx_groups_main.country AND
	(CONCAT (Profiles.LastName," ",Profiles.FirstName) LIKE "%'.$search_string.'%" 
	OR CONCAT (Profiles.FirstName," ",Profiles.LastName) LIKE "%'.$search_string.'%"
	OR bx_groups_main.title LIKE "%'.$search_string.'%"  
	OR Profiles.NickName LIKE "%'.$search_string.'%" 
	OR bx_groups_main.city LIKE "%'.$search_string.'%"
	OR bx_groups_main.categories LIKE "%'.$search_string.'%"
	OR sys_countries.country LIKE "%'.$search_string.'%")
	ORDER BY bx_groups_main.Deadline ASC
	LIMIT 5';
	
		//Do Search Groups
	 $result_grp = mysql_query($query_grp)or die(mysql_error());
	while($results_grp = mysql_fetch_array($result_grp)) {
		$result_array_grp[] = $results_grp; 
	}
	
	// Check If We Have groups Results
	 if (isset($result_array_grp)) {
		foreach ($result_array_grp as $result_grp) {
			if(($result_grp['Deadline']>time())and ($result_grp['OpenGrp']==1)){
		
		// Bypass le SQL + Insérer NomPrenom dans Tableau
				
				$PrenomNom = $result_grp['FirstName'].' '.$result_grp['LastName'];
				$NomPrenom = $result_grp['LastName'].' '.$result_grp['FirstName'];
				
				$tab5= array();
				array_push($tab5,$PrenomNom);
				$nbtot5=(count($tab5)-1)/2;
				
				$tab6=array();
				array_push($tab6,$NomPrenom);
				$nbtot6=(count($tab6)-1)/2;
				
				if(strtolower($search_string)==strtolower($tab5[$nbtot5])){
				$search_string=$NomPrenom;
				$tab5[$nbtot5]=$tab6[$nbtot6];
				$display_auteur_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab5[$nbtot5]);
				}
				else{
				$display_auteur_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab6[$nbtot6]);
				}
		//GRP
			//Cut texte du titre
			if(strlen($result_grp['title'])<30){
			//Si la longueur de la chaine est inférieure à cette valeur, on l'affiche entièrement
			$display_title_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_grp['title']);
			}
			else{
			//On nettoie la chaine pour qu'elle s'affiche sur une seule ligne, sans balises html, selon une longueur prédéfini
			$long=30;
			$hardcut=substr($result_grp['title'],0,$long);
			$nospam=preg_replace("#(:?\s)?(:?\n)?(:?\r)?#",'',$hardcut);
			$lowered=ucwords(strtolower($nospam));
			$hardsoft=preg_replace("#(?:<.>)*<*(?:/.*>)*<*#",'',$lowered);
			//Fin du nettoyage
			$display_title_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $hardsoft.'...');
			}	
			//////////////////AFFICHAGE GROUPS/////////////////////////////
			$display_nick_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_grp['NickName']);
			$display_city_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_grp['city']);
			$display_pays_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_grp['Country']);
			$display_flag_grp = genFlag($result_grp['countgrp']);
			$display_fans_grp = $result_grp['fans_count'];
			$display_grp_start = DateTime($result_grp['created'],$dateFormatC);
			$display_grp_end = DateTime($result_grp['Deadline'],$dateFormatC);
			$display_cat_grp = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_grp['categories']);
			$display_url_grp = 'http://localhost/innov24/m/groups/view/'.urlencode($result_grp['uri']);

			// Insert pour groups
			$output = str_replace('grpcatString',$display_cat_grp, $html3);
			$output = str_replace('grppaysString',$display_pays_grp, $output);
			$output = str_replace('grpflagString',$display_flag_grp, $output);
			$output = str_replace('grpfansString',$display_fans_grp, $output);
			$output = str_replace('grpstartString',$display_grp_start, $output);
			$output = str_replace('grpendString',$display_grp_end, $output);
			$output = str_replace('grpcityString',$display_city_grp, $output);
			$output = str_replace('grpautString',$display_auteur_grp, $output);
			$output = str_replace('grpnickString',$display_nick_grp, $output);
			$output = str_replace('grptitString', $display_title_grp, $output);
			$output = str_replace('urlStringgrp', $display_url_grp, $output);
			
			// Output
			echo($output);
		}
		}
}
}
////////////////////////////////////////////////////////////////FIN GROUPS///////////////////////////////////////////////////
	
//////////////////////////////////////////////////DEBUT NEWS///////////////////////////////////////////////////////////////
if(($search_string[0]=='#')or((preg_match("#^(news:)|^(\#)#",$search_string))==true)){
$search_string = preg_replace("#^(news\:)|^(\#)#","%",$search_string);
$search_string = mysql_real_escape_string($search_string);
	//Query regex pour news
	$query_news = 'SELECT bx_blogs_posts.PostCaption,bx_blogs_posts.PostDate,bx_blogs_posts.OpenNews,bx_blogs_posts.PostUri,
	bx_blogs_posts.City,bx_blogs_posts.OwnerID,bx_blogs_posts.Categories,bx_blogs_posts.Views,
	Profiles.ID,Profiles.LastName,Profiles.FirstName,Profiles.NickName,bx_blogs_posts.Country as countnews,sys_countries.Country,sys_countries.ISO2
	FROM bx_blogs_posts,Profiles,sys_countries
	WHERE bx_blogs_posts.OwnerID=Profiles.ID
	AND sys_countries.ISO2 = bx_blogs_posts.Country AND
	(CONCAT (Profiles.LastName," ",Profiles.FirstName) LIKE "%'.$search_string.'%" 
	OR CONCAT (Profiles.FirstName," ",Profiles.LastName) LIKE "%'.$search_string.'%"
	OR bx_blogs_posts.PostCaption LIKE "%'.$search_string.'%" 
	OR Profiles.NickName LIKE "%'.$search_string.'%" 
	OR bx_blogs_posts.City LIKE "%'.$search_string.'%"
	OR bx_blogs_posts.Categories LIKE "%'.$search_string.'%"
	OR sys_countries.Country LIKE "%'.$search_string.'%")
	ORDER BY bx_blogs_posts.PostDate ASC
	LIMIT 7';
	
	//Do Search News
	 $result_news = mysql_query($query_news)or die(mysql_error());
	while($results_news = mysql_fetch_array($result_news)) {
		$result_array_news[] = $results_news; 
	}
	
	// Check If We Have news Results
	 if (isset($result_array_news)) {
		foreach ($result_array_news as $result_news) {
		if($result_news['OpenNews']==1){
		// Bypass le SQL + Insérer NomPrenom dans Tableau
				
				$PrenomNom = $result_news['FirstName'].' '.$result_news['LastName'];
				$NomPrenom = $result_news['LastName'].' '.$result_news['FirstName'];
				
				$tab7= array();
				array_push($tab7,$PrenomNom);
				$nbtot7=(count($tab7)-1)/2;
				
				$tab8=array();
				array_push($tab8,$NomPrenom);
				$nbtot8=(count($tab8)-1)/2;
				
				if(strtolower($search_string)==strtolower($tab7[$nbtot7])){
				$search_string=$NomPrenom;
				$tab7[$nbtot7]=$tab8[$nbtot8];
				$display_auteur_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab7[$nbtot7]);
				}
				else{
				$display_auteur_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab8[$nbtot8]);
				}

		//NEWS
			//Cut texte du titre
			if(strlen($result_news['PostCaption'])<30){
			//Si la longueur de la chaine est inférieure à cette valeur, on l'affiche entièrement
			$display_title_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_news['PostCaption']);
			}
			else{
			//On nettoie la chaine pour qu'elle s'affiche sur une seule ligne, sans balises html, selon une longueur prédéfini
			$long=30;
			$hardcut=substr($result_news['PostCaption'],0,$long);
			$nospam=preg_replace("#(:?\s)?(:?\n)?(:?\r)?#",'',$hardcut);
			$lowered=ucwords(strtolower($nospam));
			$hardsoft=preg_replace("#(?:<.>)*<*(?:/.*>)*<*#",'',$lowered);
			//Fin du nettoyage
			$display_title_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $hardsoft.'...');
			}	
			//////////////////AFFICHAGE NEWS/////////////////////////////
			$display_nick_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_news['NickName']);
			$display_city_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_news['City']);
			$display_pays_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_news['Country']);
			$display_flag_news = genFlag($result_news['countnews']);
			$display_fans_news = $result_news['Views'];
			$display_news_start = DateTime($result_news['PostDate'],$dateFormatC);
			$display_cat_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_news['Categories']);
			$display_url_news = 'http://localhost/innov24/blogs/entry/'.urlencode($result_news['PostUri']);
			
			//Hack pour l'encodage
			if($search_string[0]=='%'){
			$search_string = preg_replace("#^([%])#","",$search_string);
			}
			
			// Insert pour news
			$output = str_replace('newscatString',$display_cat_news, $html4);
			$output = str_replace('newspaysString',$display_pays_news, $output);
			$output = str_replace('newsflagString',$display_flag_news, $output);
			$output = str_replace('newsfansString',$display_fans_news, $output);
			$output = str_replace('newsstartString',$display_news_start, $output);
			$output = str_replace('newsendString',$display_news_end, $output);
			$output = str_replace('newscityString',$display_city_news, $output);
			$output = str_replace('newsautString',$display_auteur_news, $output);
			$output = str_replace('newsnickString',$display_nick_news, $output);
			$output = str_replace('newstitString', $display_title_news, $output);
			$output = str_replace('urlStringnews', $display_url_news, $output);
			
			// Output
			echo($output);
		}
}
}
return null;
}
else
{
//Query normale pour news
	$query_news = 'SELECT bx_blogs_posts.PostCaption,bx_blogs_posts.PostDate,bx_blogs_posts.OpenNews,bx_blogs_posts.PostUri,
	bx_blogs_posts.City,bx_blogs_posts.OwnerID,bx_blogs_posts.Categories,bx_blogs_posts.Views,
	Profiles.ID,Profiles.LastName,Profiles.FirstName,Profiles.NickName,bx_blogs_posts.Country as countnews,sys_countries.Country,sys_countries.ISO2
	FROM bx_blogs_posts,Profiles,sys_countries
	WHERE bx_blogs_posts.OwnerID=Profiles.ID
	AND sys_countries.ISO2 = bx_blogs_posts.Country AND
	(CONCAT (Profiles.LastName," ",Profiles.FirstName) LIKE "%'.$search_string.'%" 
	OR CONCAT (Profiles.FirstName," ",Profiles.LastName) LIKE "%'.$search_string.'%"
	OR bx_blogs_posts.PostCaption LIKE "%'.$search_string.'%" 
	OR Profiles.NickName LIKE "%'.$search_string.'%" 
	OR bx_blogs_posts.City LIKE "%'.$search_string.'%"
	OR bx_blogs_posts.Categories LIKE "%'.$search_string.'%"
	OR sys_countries.Country LIKE "%'.$search_string.'%")
	ORDER BY bx_blogs_posts.PostDate ASC
	LIMIT 5';
	
	//Do Search News
	 $result_news = mysql_query($query_news)or die(mysql_error());
	while($results_news = mysql_fetch_array($result_news)) {
		$result_array_news[] = $results_news; 
	}
	
	// Check If We Have news Results
	 if (isset($result_array_news)) {
		foreach ($result_array_news as $result_news) {
		if($result_news['OpenNews']==1){
		// Bypass le SQL + Insérer NomPrenom dans Tableau
				
				$PrenomNom = $result_news['FirstName'].' '.$result_news['LastName'];
				$NomPrenom = $result_news['LastName'].' '.$result_news['FirstName'];
				
				$tab7= array();
				array_push($tab7,$PrenomNom);
				$nbtot7=(count($tab7)-1)/2;
				
				$tab8=array();
				array_push($tab8,$NomPrenom);
				$nbtot8=(count($tab8)-1)/2;
				
				if(strtolower($search_string)==strtolower($tab7[$nbtot7])){
				$search_string=$NomPrenom;
				$tab7[$nbtot7]=$tab8[$nbtot8];
				$display_auteur_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab7[$nbtot7]);
				}
				else{
				$display_auteur_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab8[$nbtot8]);
				}
		//NEWS
			//Cut texte du titre
			if(strlen($result_news['PostCaption'])<30){
			//Si la longueur de la chaine est inférieure à cette valeur, on l'affiche entièrement
			$display_title_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_news['PostCaption']);
			}
			else{
			//On nettoie la chaine pour qu'elle s'affiche sur une seule ligne, sans balises html, selon une longueur prédéfini
			$long=30;
			$hardcut=substr($result_news['PostCaption'],0,$long);
			$nospam=preg_replace("#(:?\s)?(:?\n)?(:?\r)?#",'',$hardcut);
			$lowered=ucwords(strtolower($nospam));
			$hardsoft=preg_replace("#(?:<.>)*<*(?:/.*>)*<*#",'',$lowered);
			//Fin du nettoyage
			$display_title_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $hardsoft.'...');
			}	
			//////////////////AFFICHAGE NEWS/////////////////////////////
			$display_nick_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_news['NickName']);
			$display_city_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_news['City']);
			$display_pays_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_news['Country']);
			$display_flag_news = genFlag($result_news['countnews']);
			$display_fans_news = $result_news['Views'];
			$display_news_start = DateTime($result_news['PostDate'],$dateFormatC);
			$display_cat_news = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_news['Categories']);
			$display_url_news = 'http://localhost/innov24/blogs/entry/'.urlencode($result_news['PostUri']);
	
			// Insert pour news
			$output = str_replace('newscatString',$display_cat_news, $html4);
			$output = str_replace('newspaysString',$display_pays_news, $output);
			$output = str_replace('newsflagString',$display_flag_news, $output);
			$output = str_replace('newsfansString',$display_fans_news, $output);
			$output = str_replace('newsstartString',$display_news_start, $output);
			$output = str_replace('newsendString',$display_news_end, $output);
			$output = str_replace('newscityString',$display_city_news, $output);
			$output = str_replace('newsautString',$display_auteur_news, $output);
			$output = str_replace('newsnickString',$display_nick_news, $output);
			$output = str_replace('newstitString', $display_title_news, $output);
			$output = str_replace('urlStringnews', $display_url_news, $output);
			
			// Output
			echo($output);
		}
}
}
}
//////////////////////////////////////////////////FIN NEWS////////////////////////////////////////////////

////////////////////////////////////////////////DEBUT STORE/////////////////////////////////////////////
if(($search_string[0]=='$')or((preg_match("#^(store:)|^([$])#",$search_string))==true)){
$search_string = preg_replace("#^(store\:)|^([$])#","%",$search_string);	
$search_string = mysql_real_escape_string($search_string);

	//Query regex pour Products(store)
	$query_sto = 'SELECT bx_store_products.title,bx_store_products.created,bx_store_products.uri,bx_store_products.OpenSto,
	bx_store_products.City,bx_store_products.author_id,bx_store_products.categories,bx_store_products.views,
	Profiles.ID,Profiles.LastName,Profiles.FirstName,Profiles.NickName,bx_store_products.Country as countsto,sys_countries.Country,sys_countries.ISO2
	FROM bx_store_products,Profiles,sys_countries
	WHERE bx_store_products.author_id=Profiles.ID
	AND sys_countries.ISO2 = bx_store_products.Country AND
	(CONCAT (Profiles.LastName," ",Profiles.FirstName) LIKE "%'.$search_string.'%" 
	OR CONCAT (Profiles.FirstName," ",Profiles.LastName) LIKE "%'.$search_string.'%"
	OR bx_store_products.title LIKE "%'.$search_string.'%" 
	OR Profiles.NickName LIKE "%'.$search_string.'%" 
	OR bx_store_products.City LIKE "%'.$search_string.'%"
	OR bx_store_products.categories LIKE "%'.$search_string.'%"
	OR sys_countries.Country LIKE "%'.$search_string.'%")
	ORDER BY bx_store_products.views DESC
	LIMIT 7';

	//Do Search Products(Store)
	 $result_sto = mysql_query($query_sto)or die(mysql_error());
	while($results_sto = mysql_fetch_array($result_sto)) {
		$result_array_sto[] = $results_sto;
	}
	// Check If We Have products Results
	 if (isset($result_array_sto)) {
		foreach ($result_array_sto as $result_sto) {
		if($result_sto['OpenSto']==1){
		
		// Bypass le SQL + Insérer NomPrenom dans Tableau
				
				$PrenomNom = $result_sto['FirstName'].' '.$result_sto['LastName'];
				$NomPrenom = $result_sto['LastName'].' '.$result_sto['FirstName'];
				
				$tab9= array();
				array_push($tab9,$PrenomNom);
				$nbtot9=(count($tab9)-1)/2;
				
				$tab10=array();
				array_push($tab10,$NomPrenom);
				$nbtot10=(count($tab10)-1)/2;
				
				if(strtolower($search_string)==strtolower($tab9[$nbtot9])){
				$search_string=$NomPrenom;
				$tab9[$nbtot9]=$tab10[$nbtot10];
				$display_auteur_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab9[$nbtot9]);
				}
				else{
				$display_auteur_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab10[$nbtot10]);
				}
		//STORE
			//Cut texte du titre
			if(strlen($result_sto['title'])<30){
			//Si la longueur de la chaine est inférieure à cette valeur, on l'affiche entièrement
			$display_title_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_sto['title']);
			}
			else{
			//On nettoie la chaine pour qu'elle s'affiche sur une seule ligne, sans balises html, selon une longueur prédéfini
			$long=30;
			$hardcut=substr($result_sto['title'],0,$long);
			$nospam=preg_replace("#(:?\s)?(:?\n)?(:?\r)?#",'',$hardcut);
			$lowered=ucwords(strtolower($nospam));
			$hardsoft=preg_replace("#(?:<.>)*<*(?:/.*>)*<*#",'',$lowered);
			//Fin du nettoyage
			$display_title_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $hardsoft.'...');
			}	
			//////////////////AFFICHAGE STORE/////////////////////////////
			$display_nick_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_sto['NickName']);
			$display_city_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_sto['City']);
			$display_pays_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_sto['Country']);
			$display_flag_sto = genFlag($result_sto['countsto']);
			$display_fans_sto = $result_sto['views'];
			$display_sto_start = DateTime($result_sto['created'],$dateFormatC);
			$display_cat_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_sto['categories']);
			$display_url_sto = 'http://localhost/innov24/m/store/view/'.urlencode($result_sto['uri']);
			
			//Hack pour l'encodage
			if($search_string[0]=='%'){
			$search_string = preg_replace("#^([%])#","",$search_string);
			}
			
			// Insert pour store
			$output = str_replace('stocatString',$display_cat_sto, $html5);
			$output = str_replace('stopaysString',$display_pays_sto, $output);
			$output = str_replace('stoflagString',$display_flag_sto, $output);
			$output = str_replace('stofansString',$display_fans_sto, $output);
			$output = str_replace('stostartString',$display_sto_start, $output);
			$output = str_replace('stoendString',$display_sto_end, $output);
			$output = str_replace('stocityString',$display_city_sto, $output);
			$output = str_replace('stoautString',$display_auteur_sto, $output);
			$output = str_replace('stonickString',$display_nick_sto, $output);
			$output = str_replace('stotitString', $display_title_sto, $output);
			$output = str_replace('urlStringsto', $display_url_sto, $output);
			
			// Output
			echo($output);
		
}
}
}
return null;
}
else
{
//Query normale pour Products(store)
	$query_sto = 'SELECT bx_store_products.title,bx_store_products.created,bx_store_products.uri,bx_store_products.OpenSto,
	bx_store_products.City,bx_store_products.author_id,bx_store_products.categories,bx_store_products.views,
	Profiles.ID,Profiles.LastName,Profiles.FirstName,Profiles.NickName,bx_store_products.Country as countsto,sys_countries.Country,sys_countries.ISO2
	FROM bx_store_products,Profiles,sys_countries
	WHERE bx_store_products.author_id=Profiles.ID
	AND sys_countries.ISO2 = bx_store_products.Country AND
	(CONCAT (Profiles.LastName," ",Profiles.FirstName) LIKE "%'.$search_string.'%" 
	OR CONCAT (Profiles.FirstName," ",Profiles.LastName) LIKE "%'.$search_string.'%"
	OR bx_store_products.title LIKE "%'.$search_string.'%" 
	OR Profiles.NickName LIKE "%'.$search_string.'%" 
	OR bx_store_products.City LIKE "%'.$search_string.'%"
	OR bx_store_products.categories LIKE "%'.$search_string.'%"
	OR sys_countries.Country LIKE "%'.$search_string.'%")
	ORDER BY bx_store_products.views DESC
	LIMIT 5';

	//Do Search Products(Store)
	 $result_sto = mysql_query($query_sto)or die(mysql_error());
	while($results_sto = mysql_fetch_array($result_sto)) {
		$result_array_sto[] = $results_sto;
	}
	// Check If We Have products Results
	 if (isset($result_array_sto)) {
		foreach ($result_array_sto as $result_sto) {
		if($result_sto['OpenSto']==1){
		
		// Bypass le SQL + Insérer NomPrenom dans Tableau
				
				$PrenomNom = $result_sto['FirstName'].' '.$result_sto['LastName'];
				$NomPrenom = $result_sto['LastName'].' '.$result_sto['FirstName'];
				
				$tab9= array();
				array_push($tab9,$PrenomNom);
				$nbtot9=(count($tab9)-1)/2;
				
				$tab10=array();
				array_push($tab10,$NomPrenom);
				$nbtot10=(count($tab10)-1)/2;
				
				if(strtolower($search_string)==strtolower($tab9[$nbtot9])){
				$search_string=$NomPrenom;
				$tab9[$nbtot9]=$tab10[$nbtot10];
				$display_auteur_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab9[$nbtot9]);
				}
				else{
				$display_auteur_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $tab10[$nbtot10]);
				}
		//STORE
			//Cut texte du titre
			if(strlen($result_sto['title'])<30){
			//Si la longueur de la chaine est inférieure à cette valeur, on l'affiche entièrement
			$display_title_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_sto['title']);
			}
			else{
			//On nettoie la chaine pour qu'elle s'affiche sur une seule ligne, sans balises html, selon une longueur prédéfini
			$long=30;
			$hardcut=substr($result_sto['title'],0,$long);
			$nospam=preg_replace("#(:?\s)?(:?\n)?(:?\r)?#",'',$hardcut);
			$lowered=ucwords(strtolower($nospam));
			$hardsoft=preg_replace("#(?:<.>)*<*(?:/.*>)*<*#",'',$lowered);
			//Fin du nettoyage
			$display_title_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $hardsoft.'...');
			}	
			//////////////////AFFICHAGE STORE/////////////////////////////
			$display_nick_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_sto['NickName']);
			$display_city_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_sto['City']);
			$display_pays_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_sto['Country']);
			$display_flag_sto = genFlag($result_sto['countsto']);
			$display_fans_sto = $result_sto['views'];
			$display_sto_start = DateTime($result_sto['created'],$dateFormatC);
			$display_cat_sto = preg_replace("/".$search_string."/i", "<b class='highlight'>".$search_string."</b>", $result_sto['categories']);
			$display_url_sto = 'http://localhost/innov24/m/store/view/'.urlencode($result_sto['uri']);

			// Insert pour store
			$output = str_replace('stocatString',$display_cat_sto, $html5);
			$output = str_replace('stopaysString',$display_pays_sto, $output);
			$output = str_replace('stoflagString',$display_flag_sto, $output);
			$output = str_replace('stofansString',$display_fans_sto, $output);
			$output = str_replace('stostartString',$display_sto_start, $output);
			$output = str_replace('stoendString',$display_sto_end, $output);
			$output = str_replace('stocityString',$display_city_sto, $output);
			$output = str_replace('stoautString',$display_auteur_sto, $output);
			$output = str_replace('stonickString',$display_nick_sto, $output);
			$output = str_replace('stotitString', $display_title_sto, $output);
			$output = str_replace('urlStringsto', $display_url_sto, $output);
			
			// Output
			echo($output);
		}
}
}
}
/////////////////////////////////////////////////////FIN STORE////////////////////////////////////////////////////////
}
?>