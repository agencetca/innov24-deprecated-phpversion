<?
/**********************************************************************************
*                            IBDW 1Col Dolphin Smart Community Builder
*                              -------------------
*     begin                : May 1 2010
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW SpyWall is not free and you cannot redistribute and/or modify it.
* 
* IBDW SpyWall is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

$querydiconfigurazione = "SELECT * FROM `1col_config` LIMIT 0 , 1";
$risultato = mysql_query($querydiconfigurazione);
$riga = mysql_fetch_assoc($risultato);

//AVATAR TYPE: Standard (default style of Dolphin avatar) or Simple (similar to fb)
//$avatartype="standard";
$avatartype=$riga['avatartype'];

//EMAIL: Show or hide the email address
$shemaila=$riga['emailad'];

//STATUS: Show or hide the member status
$status = $riga['status'];

//CITY: Show or hide the member city
$scity = $riga['city'];

//SISTEMA SLIDEDOWN PER ALTRE INFORMAZIONI --- IMPOSTA ON PER ATTIVARLO --- OFF PER DISATTIVARLO

$slideotherinfo = $riga['slide'];
//imposta in millisecondi la velocità di scorrimento
$velocityslide = $riga['slidevelocity'];

//max number of online friends
$maxnumberonlinef=$riga['numbermaxfriend'];;

//Set the time refresh in milliseconds
$timereload=$riga['timereload'];


/*this option allows you to choose the "username" format in the menu*/
// values:
//0 = nickname
//1 = realname
//2 = firstname

$usernamem=$riga['usernameformat'];


//To show/hide sections (value ON/OFF)
$mainmenuvar=$riga['mainmenuvar'];
$mediavar=$riga['mediavar'];
$accounteditvar=$riga['acceditvar'];
$sonlinefriends=$riga['onlinefriendvar'];
//Ajouts perso systeme Market Place
$marketvar=$riga['marketvar'];
//Section "Alerts"
$alertevar=$riga['alertevar'];

//To show/hide link to modules in main menu (value ON/OFF).
//IMPORTANT: if some boonex module is not installed, you must turn off the corrisponding variable. 
//Also even if you have installed a module but you dont want show the link into the menu, 
//you can turn off the variable that refers at this mod

$sitesvar=$riga['siti'];
$adsvar=$riga['annunci'];
$evntvar=$riga['eventi'];
$groupsvar=$riga['gruppi'];
$pollsvar=$riga['sondaggi'];
$photosvar=$riga['foto'];
$videosvar=$riga['video'];
$filesvar=$riga['file'];
$soundsvar=$riga['suoni'];
$gigsvar=$riga['gigsvar']; //GIGS
$storevar=$riga['storevar']; //MARKET	
$topicvar=$riga['topicvar']; //ARTICLES
$tchatvar=$riga['tchatvar']; //TCHAT
$wallvar=$riga['wallvar'];//WALL
$blogvar=$riga['blogvar'];//BLOG
$alertvar=$riga['alertvar'];//ALERT




//Turn off to not show the Delete Account button
$deleteaccount = $riga['deletebutton'];


// Management dynamic URLs
/*
You can change the link addresses of the menu. This can be usefully when you have installed the custom module.
Example
$photourl = 'm/photos/albums/my/main/';   >>  $photourl = 'm/photospersonal/my/';
*/

$mailurl = $riga['mailurl'];     //MAIL URL
$groupurl = $riga['groupurl'];            // GROUPS URL
$addgroupurl = $riga['addgroupurl']; // ADD GROUPS URL
$eventurl = $riga['eventurl'];             // EVENT URL
$addeventurl = $riga['addeventurl'];   // AD EVENT URL
$pollurl = $riga['pollurl'];   // POLL URL
$addpollurl = $riga['addpollurl'];     // ADD POLL URL
$adsurl = $riga['adsurl'];      // AD URL
$addadsurl = $riga['addadsurl'];       // ADD AD URL
$siteurl = $riga['siteurl'];   // SITE URL
$fileurl = $riga['fileurl'];   // FILE URL
$addfileurl = $riga['addfileurl'];  // ADD FILE URL
$addsiteurl = $riga['addsiteurl'];     // ADD SITE URL
$photourl = $riga['photourl'];       // PHOTO URL
$addphotourl = $riga['addphotourl']; //ADD PHOTO URL
$videourl = $riga['videourl'];    // VIDEO URL
$addvideourl = $riga['addvideourl']; //ADD VIDEO URL
$soundurl = $riga['soundurl'];     // SOUND URL
$addsoundurl = $riga['addsoundurl']; //ADD SOUND URL
$avatarurl = $riga['avatarurl'];               // AVATAR URL
$gigsurl = $riga['gigsurl']; //GIGS URL
$gigsserv = $riga['gigsserv']; //ADDGIGS SERVICE
$storeurl = $riga['storeurl']; //STORE URL
$storeserv = $riga['storeserv']; //ADDSTORE URL
$topicurl = $riga['topicurl']; //ARTICLES URL
$topicserv = $riga['topicserv']; //ADDARTICLES URL
$tchaturl = $riga['tchaturl']; //TCHAT URL
$wallurl=$riga['wallurl'];//WALL URL
$blogurl=$riga['blogurl'];//BLOG URL
$addblogurl=$riga['addblogurl']; //ADD BLOG
$addalert=$riga['addalert'];//ADD ALERT
$alerturl=$riga['alerturl'];//ALERT URL

//Les CHECKBOXES !!!
$Journalist1=$riga['Journalist1'];
$Communicator1=$riga['Communicator1'];
$Leader1=$riga['Leader1'];
$Journalist2=$riga['Journalist2'];
$Communicator2=$riga['Communicator2'];
$Leader2=$riga['Leader2'];
$Journalist3=$riga['Journalist3'];
$Communicator3=$riga['Communicator3'];
$Leader3=$riga['Leader3'];
$Journalist4=$riga['Journalist4'];
$Communicator4=$riga['Communicator4'];
$Leader4=$riga['Leader4'];
$Journalist5=$riga['Journalist5'];
$Communicator5=$riga['Communicator5'];
$Leader5=$riga['Leader5'];
$Journalist6=$riga['Journalist6'];
$Communicator6=$riga['Communicator6'];
$Leader6=$riga['Leader6'];
$Journalist7=$riga['Journalist7'];
$Communicator7=$riga['Communicator7'];
$Leader7=$riga['Leader7'];
$Journalist8=$riga['Journalist8'];
$Communicator8=$riga['Communicator8'];
$Leader8=$riga['Leader8'];
$Journalist9=$riga['Journalist9'];
$Communicator9=$riga['Communicator9'];
$Leader9=$riga['Leader9'];
$Journalist10=$riga['Journalist10'];
$Communicator10=$riga['Communicator10'];
$Leader10=$riga['Leader10'];
$Journalist11=$riga['Journalist11'];
$Communicator11=$riga['Communicator11'];
$Leader11=$riga['Leader11'];
$Journalist12=$riga['Journalist12'];
$Communicator12=$riga['Communicator12'];
$Leader12=$riga['Leader12'];
$Journalist13=$riga['Journalist13'];
$Communicator13=$riga['Communicator13'];
$Leader13=$riga['Leader13'];
$Journalist14=$riga['Journalist14'];
$Communicator14=$riga['Communicator14'];
$Leader14=$riga['Leader14'];
$Journalist15=$riga['Journalist15'];
$Communicator15=$riga['Communicator15'];
$Leader15=$riga['Leader15'];
//Fin CHECKBOXES !

$avaset = $riga['avaset'];
$privasett = $riga['privasett'];
$sottoscrizione  = $riga['sottoscrizione'];
$mailset = $riga['mailset'];
$amiciset = $riga['amiciset'];

if ($riga['customlink1']!='') { $cs1 = $riga['customlink1']; } else { $cs1 = '0'; }
if ($riga['customlink2']!='') { $cs2 = $riga['customlink2']; } else { $cs2 = '0'; }
if ($riga['customlink3']!='') { $cs3 = $riga['customlink3']; } else { $cs3 = '0'; }
if ($riga['customlink4']!='') { $cs4 = $riga['customlink4']; } else { $cs4 = '0'; }
if ($riga['customlink5']!='') { $cs5 = $riga['customlink5']; } else { $cs5 = '0'; }

if ($riga['customsect1']!='') { $customnamesect1 = $riga['customsect1']; } else { $customnamesect1 = '0'; }
if ($riga['customsect2']!='') { $customnamesect2 = $riga['customsect2']; } else { $customnamesect2 = '0'; }
if ($riga['customsect3']!='') { $customnamesect3 = $riga['customsect3']; } else { $customnamesect3 = '0'; }
if ($riga['customsect4']!='') { $customnamesect4 = $riga['customsect4']; } else { $customnamesect4 = '0'; }
if ($riga['customsect5']!='') { $customnamesect5 = $riga['customsect5']; } else { $customnamesect5 = '0'; }
if ($riga['customsect6']!='') { $customnamesect6 = $riga['customsect6']; } else { $customnamesect6 = '0'; }
if ($riga['customsect7']!='') { $customnamesect7 = $riga['customsect7']; } else { $customnamesect7 = '0'; }
if ($riga['customsect8']!='') { $customnamesect8 = $riga['customsect8']; } else { $customnamesect8 = '0'; }
if ($riga['customsect9']!='') { $customnamesect9 = $riga['customsect9']; } else { $customnamesect9 = '0'; }
if ($riga['customsect10']!='') { $customnamesect10 = $riga['customsect10']; } else { $customnamesect10 = '0'; }
if ($riga['customsect11']!='') { $customnamesect11 = $riga['customsect11']; } else { $customnamesect11 = '0'; }
if ($riga['customsect12']!='') { $customnamesect12 = $riga['customsect12']; } else { $customnamesect12 = '0'; }
if ($riga['customsect13']!='') { $customnamesect13 = $riga['customsect13']; } else { $customnamesect13 = '0'; }
if ($riga['customsect14']!='') { $customnamesect14 = $riga['customsect14']; } else { $customnamesect14 = '0'; }
if ($riga['customsect15']!='') { $customnamesect15 = $riga['customsect15']; } else { $customnamesect15 = '0'; }
?>