<?php
// Imports
require_once( 'inc/header.inc.php' );
//Tableaux
$tabnoms = array(); // On cr�e un tableau vide
$tabprenoms = array(); // On cr�e un tableau vide
$tabcoms = array(); // 3eme tableau vide
//Requete
$sql_coms = "SELECT * FROM commenti_spy_data,Profiles WHERE Profiles.ID=commenti_spy_data.user ORDER BY commenti_spy_data.id DESC LIMIT 6";
$req_coms = mysql_query($sql_coms) or die ("Impossible de cr�er l'enregistrement :" . mysql_error());

while($result_coms = mysql_fetch_assoc($req_coms)){
array_push($tabnoms,$result_coms['LastName']);
array_push($tabprenoms,$result_coms['FirstName']); 
array_push($tabcoms,$result_coms['commento']); 
//echo $result_coms['NickName'].' : "'.$result_coms['commento'].'"<br>';
}
$counttab = count($tabnoms); 
$varX=0;
$max_length = 75;
for($varX=0;$varX<$counttab;$varX++){
$com[$varX]=$tabprenoms[$varX].' '.$tabnoms[$varX].' : "'.$tabcoms[$varX].'"';
if (strlen($com[$varX]) > $max_length){
	$offset = ($max_length - 3) - strlen($com[$varX]);
	$com[$varX] = substr($com[$varX], 0, strrpos($com[$varX], ' ', $offset)) . '...';
	}
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<meta charset="utf-8" />
</head>
<body>
<div id="example">
  <ul>
    <li id="com1"><?php echo $com[0];?></li>
    <li id="com2"><?php echo $com[1];?></li>
	<li id="com3"><?php echo $com[2];?></li>
	<li id="com4"><?php echo $com[3];?></li>
	<li id="com5"><?php echo $com[4];?></li>
	<li id="com6"><?php echo $com[5];?></li>
  </ul>
</div>
</body>
</html>