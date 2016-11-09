<?php
$querydiconfigurazione = "SELECT * FROM `megaprofile_config` LIMIT 0 , 1";
$risultato = mysql_query($querydiconfigurazione);
$riga = mysql_fetch_assoc($risultato);

//formato del nome utente
$usernamem=$riga['usernameformat'];
//visualizza il nome sotto la miniatura
$showname=$riga['namestm'];
//numero massimo di amici da visualizzare nel box
$maxnumberfriends=$riga['nfriend'];
//numero massimo di amici in comune da visualizzare nel box
$maxnumbermutualfriends=$riga['nfriendm'];
//numero massimo di album foto da visualizzare nel box
$maxnumberalbumsfoto=$riga['nalbum'];
//numero massimo di album video da visualizzare nel box
$maxnumberalbumsvideo=$riga['nalbum'];
//Lunghezza della descrizione profilo
$maxlunghdesc=$riga['crtdesk'];
//Lunghezza della descrizione albums
$maxlunghdescalbum=$riga['albumdescr'];
//formato data (EU per europa, UN internazionale)
$formatodata=$riga['frmdata'];
//abilita webcam
$webcam=$riga['webcam'];
$custompro=$riga['custompro'];
$favepro=$riga['favepro'];
$photoview=$riga['linkphoto'];
$videoview=$riga['linkvideo'];
$soundview=$riga['linksound'];
$sendmessage=$riga['sndmessage'];
$greetingview=$riga['greet'];
$reportspam=$riga['rpspam'];
$befriend=$riga['friendblock'];
$blockview=$riga['blockblock'];
$subscribeview=$riga['subscribe'];
$informationview=$riga['infoblock'];
$descriptionview=$riga['descblock'];
$friendview=$riga['friend'];
$mutualfriendview=$riga['mutualfr'];
$photoviewalbum=$riga['photoalbum'];
$videoviewalbum=$riga['videoalbum'];
$friendsord=$riga['friendsord'];
$mutualfriendord=$riga['mutualfriendord'];

//informazioni da visualizzare
$relstatusview =$riga['relstatusview'];
$datebirthview = $riga['datebirthview'];
$infocityview = $riga['infocityview'];
$headlineview = $riga['headlineview'];
$emailview = $riga['emailview'];
$sexview = $riga['sexview'];
$lookingforview = $riga['lookingforview'];
$occupationview = $riga['occupationview'];
$religionview = $riga['religionview'];
$setavatard = $riga['setavatard'];
$agestyle = $riga['agestyle'];

$link1 = $riga['link1'];
$link2 = $riga['link2'];
$link3 = $riga['link3'];
$link4 = $riga['link4'];
$link5 = $riga['link5'];

$maxsize = $riga['maxsize'];

$defaultimage = $riga['defimage'];

$typeofallowed = $riga['typeofallowed'];

//Use the default Boonex report system (0) or the the custom tool by Modzzz 
$reportspamtool = $riga['reportspamtool'];
//thumbnail type: avatar or member thumbnail
$thumbtype=$riga['thumbtype'];
?>
