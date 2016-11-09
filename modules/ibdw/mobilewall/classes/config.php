<?php
$querydiconfigurazione = "SELECT * FROM `mobilewall_config` LIMIT 0 , 1";
$risultato = mysql_query($querydiconfigurazione);
$riga = mysql_fetch_assoc($risultato);

/******************************************************
C O N T E N T  B O X  B O T T O N S 
*******************************************************
* turn ON/OFF the bottons that you want enable/disable 
in to the content box*/

$photo=$riga['foto'];
$video=$riga['video'];
$group=$riga['gruppi'];
$event=$riga['eventi'];
$site=$riga['siti'];
$poll=$riga['sondaggi'];
$ads=$riga['annunci'];                                                           


/******************************************************
D A T E  F O R M A T  S E T T I N G S
*******************************************************
* changes $seldate value in $intdate or $eudate to choose date format */

$eudate='d/m/Y H:i:s'; //dd/mm/yyyy 06/12/2010
$intdate='m/d/Y H:i:s'; //mm/dd/yyyy 12/06/2010

$seldate=$riga['formatodata'];

//change this only for delta time tuning pourpose
//(for Delta time of 1 hour set 3600: this is the second in 1 hours)

$offset=$riga['offset']; 


/*************************************************************************
S P Y     W A L L    P R O F I L E    V I E W   C O N F I G U R A T I O N  
**************************************************************************
*to disable/enable the profile update or view */
// OFF= hide profiles view

$spyprofileview=$riga['spywallprofileview'];
$profileupdate=$riga['profileupdate'];


/*************************************************************************
N E W S    T O     D I S P L A Y    C O N F I G U R A T I O N  
**************************************************************************
*sets the amount of news to display in each query. Defaut value is 10 */

$limite=$riga['limite']; 


/*************************************************************************
N I C K    N A M E    F O R M A T   
**************************************************************************/
/*this option allows you to change the format "username" in the view of news*/
//0 = nickname
//1 = realname
//2 = firstname
   
$usernameformat=$riga['usernameformat']; 

$sharefunny = $riga['shareact'];
$commentfunny = $riga['commentact'];
$likefunny = $riga['likeact'];
?>