<?php
$paginadicontrollo=$_SERVER['PHP_SELF'];
$ajaxactive = strpos($paginadicontrollo, 'member.php');
$ajaxactivehome = strpos($paginadicontrollo, 'index.php');
$ajaxactiveprofile = strpos($paginadicontrollo, 'profile.php');
if ($ajaxactive==0 AND $ajaxactivehome==0 AND $ajaxactiveprofile==0) {
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
}
if (!defined('BX_AVA_EXT')) {
    define ('BX_AVA_DIR_USER_AVATARS', BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/data/images/'); // directory where avatar images are stored
    define ('BX_AVA_URL_USER_AVATARS', BX_DOL_URL_MODULES . 'boonex/avatar/data/images/'); // base url for all avatar images
    define ('BX_AVA_EXT', '.jpg'); // default avatar extension
    define ('BX_AVA_W', 64); // avatar image width
    define ('BX_AVA_H', 64); // avatar image height
    define ('BX_AVA_ICON_W', 32); // avatar icon width
    define ('BX_AVA_ICON_H', 32); // avatar icon height
 }

require_once( BX_DIRECTORY_PATH_INC . 'match.inc.php' ); 

//CONVERTE LA DATA DAL FORMATO TIME A QUELLO "UMANO" IN ITALIANO
function convertiDataTime($dataTime,$linguaggio) 
{ 
 if ($linguaggio=="uni")
 {
  $data = date("m/j/Y", $dataTime); 
  $ora = date("H:i", $dataTime); 
  $ieri = date("m/j/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))); 
  $oggi = date("m/j/Y", mktime(0, 0, 0, date("m"), date("d"), date("Y"))); 
  $domani = date("m/j/Y", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))); 
  $dopodomani = date("m/j/Y", mktime(0, 0, 0, date("m"), date("d") + 2, date("Y")));
  $unasettimana = date("m/j/Y", mktime(0, 0, 0, date("m"), date("d") + 7, date("Y")));  
 }
 else if ($linguaggio=="eur")
 {
  $data = date("j/m/Y", $dataTime); 
  $ora = date("H:i", $dataTime); 
  $ieri = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))); 
  $oggi = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d"), date("Y"))); 
  $domani = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))); 
  $dopodomani = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 2, date("Y")));
  $unasettimana = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 7, date("Y")));  
 }
 else if ($linguaggio=="jpn")
 {
  $data = date("Y/m/j", $dataTime); 
  $ora = date("H:i", $dataTime); 
  $ieri = date("Y/m/j", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))); 
  $oggi = date("Y/m/j", mktime(0, 0, 0, date("m"), date("d"), date("Y"))); 
  $domani = date("Y/m/j", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))); 
  $dopodomani = date("Y/m/j", mktime(0, 0, 0, date("m"), date("d") + 2, date("Y")));
  $unasettimana = date("Y/m/j", mktime(0, 0, 0, date("m"), date("d") + 7, date("Y")));  
 } 
  if ($data == $ieri) $dataOk = _t('_ibdw_thirdcolumn_yesterday'); 
  elseif ($data == $oggi) $dataOk = _t('_ibdw_thirdcolumn_today');
  elseif ($data == $domani) $dataOk = _t('_ibdw_thirdcolumn_tomorrow'); 
  elseif ($data == $dopodomani) $dataOk = _t('_ibdw_thirdcolumn_after'); 
  elseif ($data == $unasettimana) $dataOk = _t('_ibdw_thirdcolumn_week'); 
  else $dataOk = $data; return("$dataOk $ora");
}

//CONTA GLI AMICI IN COMUNE CON UN ALTRO UTENTE
function CountMutualFriends ($ioio,$luilui) 
{
		   $sQuery = "SELECT COUNT(*) 
 	       FROM `Profiles` AS p 
 	       LEFT JOIN `sys_friend_list` AS f1 ON ( f1.`ID` = p.`ID` AND f1.`Profile` = '$luilui' AND `f1`.`Check` =1 ) 
 	       LEFT JOIN `sys_friend_list` AS f2 ON ( f2.`Profile` = p.`ID` AND f2.`ID` = '$luilui' AND `f2`.`Check` =1 ) 
 	       LEFT JOIN `sys_friend_list` AS f11 ON ( f11.`ID` = p.`ID` AND f11.`Profile` = '$ioio' AND `f11`.`Check` =1 ) 
 	       LEFT JOIN `sys_friend_list` AS f22 ON ( f22.`Profile` = p.`ID` AND f22.`ID` = '$ioio' AND `f22`.`Check` =1 ) 
 	       WHERE 1 AND (f1.`ID` IS NOT NULL OR f2.`ID` IS NOT NULL) AND (f11.`ID` IS NOT NULL OR f22.`ID` IS NOT NULL)";
		return (int)db_value($sQuery);
}

//fa replace di di un testo url aggiugendo tag html

function urlreplace($text) {
  $text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
    '<a target=\'_blank\'  href="\\1">\\1</a>', $text);
  $text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
    '\\1<a target=\'_blank\'  href="http://\\2">\\2</a>', $text);
  $text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})',
    '<a target=\'_blank\'  href="mailto:\\1">\\1</a>', $text);
  $text = ereg_replace("www+[^<>[:space:]]+[[:alnum:]/].fan-club.it","<a target=\'_blank\'  href=\"\\0\">\\0</a>", $text);
  $text = ereg_replace("(^| )(www([.]?[a-zA-Z0-9_/-])*)", "\\1<a target=\'_blank\'  href=\"http://\\2\">\\2</a>", $text);
  return $text;
}

$userid=getID($_REQUEST['ID']);
$valoriutente = getProfileInfo($userid);
mysql_query("SET NAMES 'utf8'");
include BX_DIRECTORY_PATH_MODULES.'ibdw/3col/config.php';
$deltatime=$deltahours*3600;
//nuovi saluti
$query = "SELECT SUM(Number) FROM `sys_greetings` WHERE (`Profile`=" . $userid . " AND `New`='1')";
$resultsal = mysql_query($query);
$contasal = mysql_fetch_array($resultsal);

//richieste di amicizia: visualizza solo le richieste fatte a me
$query = "SELECT ID FROM `sys_friend_list` WHERE `Profile`=" . $userid . " AND `Check`=0";
$resultreqfriends = mysql_query($query);
$contareqamicizie = mysql_num_rows($resultreqfriends);

if ($showfriendsreq=="ON")
{
 if ($contareqamicizie>0) 
 {
  echo '<div class="rhegionmenuelement3">';
  echo '<div class="rigamenumodificadplops"><div class="titleamicireq"><a href="communicator.php?person_switcher=to&communicator_mode=friends_requests">'._t('_ibdw_thirdcolumn_friendship').'</a><span class="bubble3colforceddx">' . $contareqamicizie . '</span></div></div>';
  for ($numami=0;$numami<$contareqamicizie;$numami++)
  {
   $richiestaamiciaz=mysql_fetch_array($resultreqfriends);
   if (!is_friends($richiestaamiciaz[0],$userid))
   {
   $amichetto=getProfileInfo($richiestaamiciaz[0]);
   $LinkUtente = getProfileLink($amichetto['ID']);
   $Miniaturaa = get_member_icon($amichetto['ID'], 'none', false);
   $aInfomember = getProfileInfo($amichetto['ID']);
   
   if($usernamef==0) {$NameAmico=$amichetto['NickName'];}
   if($usernamef==1) {$realname=ucfirst($aInfomember['FirstName'])." ".ucfirst($aInfomember['LastName']);$NameAmico=$realname;}
   if($usernamef==2) {$realname=ucfirst($aInfomember['FirstName']);$NameAmico=$realname;}           

   if ($avatartype=="standard") {echo '<div id="richiesta'.$richiestaamiciaz[0].'" class="rigamenu4"><div class="mioavatarsmall3">'. $Miniaturaa .'</div>';}
   else
   {
    if ($numami==0) {$rigamostra="rigamenu6";}
    else {$rigamostra="rigamenu5";}
    if ($aInfomember['Avatar']<>"0") {echo '<div id="richiesta'.$richiestaamiciaz[0].'" class="'.$rigamostra.'"><div class="mioavatarfb"><a href="'.$LinkUtente.'"><img class="mioavatfb" src="'.BX_AVA_URL_USER_AVATARS.$aInfomember['Avatar'].BX_AVA_EXT.'"></a></div>';}
    else 
    {
     if ($aInfomember['Sex']=="female") { echo '<div id="richiesta'.$richiestaamiciaz[0].'" class="'.$rigamostra.'"><a href="'.$LinkUtente.'"><img class="mioavatfb" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/woman_medium.gif"></a>'; }
     else {echo '<div id="richiesta'.$richiestaamiciaz[0].'" class="'.$rigamostra.'"><a href="'.$LinkUtente.'"><img class="mioavatfb" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/man_medium.gif"></a>';}
    }
   }

   echo '<div class="mioutentesmall3">
   <div class="nomeamico"><a href="' . getProfileLink($amichetto['ID']) . '">'  .$NameAmico. '</a></div>
   <div class="contenitorepulsanti">';
   if ($avatartype=="standard") {echo '<div class="pulsante1"></div>';}
   else
   {
	  if (CountMutualFriends($userid,$amichetto['ID'])>0) {echo '<div id="amicomuni">'._t('_ibdw_thirdcolumn_mutualfriends').CountMutualFriends($userid,$amichetto['ID']).'</div>';}
    else {echo '<div id="distanziatore"></div>';}
   }
   if(getID($_REQUEST['ID'])==(int)$_COOKIE['memberID'])
   {                 
    echo '<div class="pulsante">
          <form id="pulsantedscelta'.$richiestaamiciaz[0].'" action="javascript:elimina();">
           <input id="richiestaamiciaz'.$richiestaamiciaz[0].'"  type="hidden" name="id" value="'.$richiestaamiciaz[0].'">
           <input id="3colid'.$richiestaamiciaz[0].'"  type="hidden" name="id" value="'.$userid.'">
           <input id="scelta'.$richiestaamiciaz[0].'"  type="hidden" name="id" value="ok">
           <input type="submit" value="'._t('_ibdw_thirdcolumn_accept').'" class="click">
          </form>
         </div>
         <div class="pulsante">
          <form id="pulsantedsceltano'.$richiestaamiciaz[0].'" action="javascript:elimina();">
           <input id="nrichiestaamiciaz'.$richiestaamiciaz[0].'"  type="hidden" name="id" value="'.$richiestaamiciaz[0].'">
           <input id="nid'.$richiestaamiciaz[0].'"  type="hidden" name="id" value="'.$userid.'">
           <input id="nscelta'.$richiestaamiciaz[0].'"  type="hidden" name="id" value="no">
           <input type="submit" value="'._t('_ibdw_thirdcolumn_reject').'" class="click">
          </form>
         </div>';
    }
    echo '</div></div></div>
          
         <script>
         $("#pulsantedscelta'.$richiestaamiciaz[0].'").submit (function() 
         { 
           var ottieniid=$("#3colid'.$richiestaamiciaz[0].'").val();
           var richiesta=$("#richiestaamiciaz'.$richiestaamiciaz[0].'").val();
           var scelta=$("#scelta'.$richiestaamiciaz[0].'").attr("value");
           var numero=$(".provax").text();
           var numero=numero-1;
           $.ajax({
 			     type: "POST", url: "modules/ibdw/3col/sistemanotifica.php", data: "ottieniid=" + ottieniid + "&richiestaamiciaz=" + richiesta +"&scelta=" + scelta + "&nameformat=" + '.$usernamef.', success: function(html)
           {
            $("#richiesta'.$richiestaamiciaz[0].'").fadeOut();
            $(".provax").html(numero);
            $("#testingx").append(html);
           }
	 	      });
         });
         </script>  
         <script>
         $("#pulsantedsceltano'.$richiestaamiciaz[0].'").submit (function() 
         { 
          var ottieniid=$("#nid'.$richiestaamiciaz[0].'").attr("value");
          var richiesta=$("#nrichiestaamiciaz'.$richiestaamiciaz[0].'").attr("value");
          var scelta=$("#nscelta'.$richiestaamiciaz[0].'").attr("value");
          var numero=$(".provax").text();
          var numero=numero-1;
          $.ajax({
 			     type: "POST", url: "modules/ibdw/3col/sistemanotifica.php", data: "ottieniid=" + ottieniid + "&richiestaamiciaz=" + richiesta +"&scelta=" + scelta, success: function(html)
           {
            $("#richiesta'.$richiestaamiciaz[0].'").fadeOut();
            aggiornajax();
           }
 		      });
         });
         </script>';
    }
  else
  {
   $query = "DELETE FROM `sys_friend_list` WHERE `Profile`=" . $userid . " AND ID=".$richiestaamiciaz[0]." AND `Check`=0";
   $resultreqfriends = mysql_query($query);
  }
  }
  echo '</div>';
 }
}

//ID amici PER DETERMINARE GLI EVENTI DA PUBBLICARE
$query = "(SELECT ID FROM sys_friend_list WHERE Profile=".$userid." AND sys_friend_list.Check=1) UNION (SELECT Profile FROM sys_friend_list WHERE ID=".$userid." AND sys_friend_list.Check=1)";
$resultfriends = mysql_query($query);
$contaamici = mysql_num_rows($resultfriends);


//Array degli ID degli amici
if ($contaamici>0)
{
 for($conta=0;$conta<$contaamici;$conta++)
 {
  $listaamici=mysql_fetch_array($resultfriends);
  $amico[$conta]= $listaamici[0];
  if ($conta==0) $arrayidamici=$amico[$conta];
  else $arrayidamici=$arrayidamici.",".$amico[$conta]; 
 } 
}

if($showsuggprof=="ON" AND getID($_REQUEST['ID'])==(int)$_COOKIE['memberID'])
{
//ottengo gli id degli utenti che ho bloccato
 $getidblocked="SELECT sys_block_list.Profile FROM sys_block_list WHERE sys_block_list.ID=".$userid;
 $getblocked=mysql_query($getidblocked);
 $numblocked=mysql_num_rows($getblocked);
 if ($numblocked>0)
 {
  for ($d=0;$d<$numblocked;$d++)
  {
   $getidbl=mysql_fetch_array($getblocked);
   $idblk=$getidbl[0];
   if ($d==0) $arrayidblk=$idblk;
   else $arrayidblk=$arrayidblk.",".$idblk;
  }
 }
 
 //ottengo la lista dei profili già inseriti nei miei suggerimenti
 $getidsuggested="SELECT FriendID FROM suggerimenti WHERE mioID=".$userid;
 $runsuggested=mysql_query($getidsuggested);
 $numalreadysugg= mysql_num_rows($runsuggested);
 if ($numalreadysugg>0)
 {  
  for ($c=0;$c<$numalreadysugg;$c++)
  {
   $getidsg=mysql_fetch_array($runsuggested);
   $idsg=$getidsg[0];
   if ($c==0) $arrayidsg=$idsg;
   else $arrayidsg=$arrayidsg.",".$idsg;
  }
 }

 //ID DEGLI UTENTI BLOCCATI
 //$arrayidblk;

 //ID DEGLI AMICI
 //$arrayidamici;

 //Query suggerimento profili
 $querysugge ="SELECT ID FROM Profiles WHERE Status='Active' AND ID<>".$userid;
 
 //controllo che non sia già stato suggerito
 if ($numalreadysugg>0) $querysugge=$querysugge." AND ID NOT IN (".$arrayidsg.")";
 
 //se ho amici controllo che il profilo non sia nella mia lista di amici
 if ($contaamici>0) $querysugge=$querysugge." AND ID NOT IN (".$arrayidamici.")";
 
 //se ho degli utenti bloccati controllo che il profilo non sia tra questi
 if ($numblocked>0) $querysugge=$querysugge." AND ID NOT IN (".$arrayidblk.")";
 
 //se ho scartato degli ID non li devo considerare al successivo refresh
 if ($arraydegliesclusi<>"") $querysugge=$querysugge." AND ID NOT IN (".$arraydegliesclusi.")";
 

 $resultsugge = mysql_query($querysugge);
 $contasugge = mysql_num_rows($resultsugge); 
  
 if ($contasugge>0)
 {
  //verifico che l'utente non abbia superato il max numero di richieste giornaliere
  $queryverificache = "SELECT Contaric,logrichieste.When, SUBDATE(CURRENT_TIMESTAMP(),1) FROM logrichieste WHERE IdUtente=".$userid;
  $resultche = mysql_query($queryverificache);
  $allora=mysql_fetch_array($resultche);
 
  //Se è passato più di un giorno da quando si è fatta l'ultima richiesta allora resetta il numero di richieste che possone essere fatte nella giornata
  if ($allora[2]>$allora[1])
  {
   $queryrest="UPDATE logrichieste SET Contaric=0,logrichieste.When=CURRENT_TIMESTAMP WHERE IdUtente=".$userid;
   $lanciarest = mysql_query($queryrest);	
  }
  
  if ($allora[0]<$maxnumberoffriendrequests)
  {
   
   for ($contatore=0;$contatore<$maxnumberoffriendrequests;$contatore++)
   {
    $listasugge=mysql_fetch_array($resultsugge);
    $metchpercentage=getProfilesMatch( $userid, $listasugge[0]);
    $mutualfriends=CountMutualFriends ($userid,$listasugge[0]);   
    
    if ((($conditionto=="OR") AND (($metchpercentage>$trsugg) or ($mutualfriends>$trfriends))) OR (($conditionto=="AND") AND (($metchpercentage>$trsugg) and ($mutualfriends>$trfriends))))
    {
 	   $queryinserta="INSERT INTO suggerimenti (mioID,FriendID,Rifiutato,Pertinenza,MutualF) VALUES(".$userid.",".$listasugge[0].",0,".$metchpercentage.",".$mutualfriends.")";
 	   $resultqueryinserta = mysql_query($queryinserta);
    }
    else $arraydegliesclusi=$arraydegliesclusi.",".$listasugge[0];    
   }
   $selectsuggestion="SELECT mioID,FriendID,MutualF FROM suggerimenti WHERE (mioID=".$userid." and Rifiutato=0) ORDER BY Pertinenza DESC,MutualF DESC LIMIT ".$limitsuggest;
   $resultsceltaa = mysql_query($selectsuggestion);
   $contasscelta = mysql_num_rows($resultsceltaa);
   $miolimite=min($limitsuggest,$contasscelta);
   if ($miolimite>0)
   {
    echo '<div class="rhegionmenuelement3"><div class="rigamenumodificadx"><div class="titlesugge">'._t('_ibdw_thirdcolumn_suggest').'</div></div>';
   }
   for ($zzetab=0;$zzetab<$miolimite;$zzetab++)  
   {
    $listadeiscelti=mysql_fetch_array($resultsceltaa);  
    $infosugge=getProfileInfo($listadeiscelti[1]);
    
    if(is_friends($infosugge['ID'],$userid) or isBlocked($userid,$infosugge['ID'])) 
    { 
     //cancello i suggerimenti di profili che ho già accettato come amici o che ho bloccato
     $removeprofile="DELETE FROM suggerimenti WHERE (FriendID=".$infosugge['ID']." AND mioID=".$userid.") OR (mioID=".$infosugge['ID']." AND FriendID=".$userid.")";
	   $execdeleteprofile=mysql_query($removeprofile);
    }
    if ($infosugge['NickName']=="") 
	  {//rimozione profili non esistenti
	   $removeprofilecanceled="DELETE FROM suggerimenti WHERE FriendID=".$listadeiscelti[1]." OR mioID=".$listadeiscelti[1];
	   
	   $execdeleteprofile=mysql_query($removeprofilecanceled);
	  }
    else
    {
     $iconasugge=get_member_icon($infosugge['ID'], 'none', false);	
	   $LinkUtente = getProfileLink($infosugge['ID']);
     $aInfomember = getProfileInfo($infosugge['ID']);
	   if($usernamef==0) { $NomeSugge=$infosugge['NickName']; }
     if($usernamef==1) { $realname =  ucfirst($aInfomember['FirstName']) . " " . ucfirst($aInfomember['LastName']); $NomeSugge=$realname; }
     if($usernamef==2) { $realname =  ucfirst($aInfomember['FirstName']); $NomeSugge=$realname; }
     if ($zzetab==0) {$rigamostra="rigamenusuggetop";}
     else {$rigamostra="rigamenusugge";}
     echo '<div id="rigasuggerimento'.$infosugge['ID'].'" class="'.$rigamostra.'"><div class="itemcont">
     <div id="deletesugg'.$infosugge['ID'].'" class="deletesuggest"><input type="hidden" id="dlt'.$infosugge['ID'].'" value="'.$infosugge['ID'].'">
     <input type="hidden" id="name'.$infosugge['ID'].'" value="'.$NomeSugge.'"></div><div class="riepilogo">';
     if ($avatartype=="standard") echo '<div class="mioavatarsmall2">'. $iconasugge .'</div><div class="mioutentesmall4">';
     else
	   {
	    if ($aInfomember['Avatar']<>"0") echo '<div class="mioavatarfb2"><a href="'.$LinkUtente.'"><img class="mioavatfb2" src="'.BX_AVA_URL_USER_AVATARS.$aInfomember['Avatar'].BX_AVA_EXT.'"></a></div><div class="mioutentesmall5">';
      else 
      {
       if ($aInfomember['Sex']=="female") echo '<div class="mioavatarfb2"><a href="'.$LinkUtente.'"><img class="mioavatfb2" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/woman_medium.gif"></a></div><div class="mioutentesmall5">';
       else echo '<div class="mioavatarfb2"><a href="'.$LinkUtente.'"><img class="mioavatfb2" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/man_medium.gif"></a></div><div class="mioutentesmall5">';
      }
     }
     echo '<div class="nomeamico"><a href="'.getProfileLink($infosugge['ID']).'">'.$NomeSugge.'</a></div><div class="affinita">'._t('_ibdw_thirdcolumn_affinita1').' '.getProfilesMatch($userid,$listadeiscelti[1]).'%</div>';
	   if (CountMutualFriends($userid,$listadeiscelti[1])>0) echo "<div class='mtf'>" ._t('_ibdw_thirdcolumn_mutualfriends').' ' . CountMutualFriends($userid,$listadeiscelti[1]) . "</div>";
     echo '<div id="suggerito'.$infosugge['ID'].'" class="suggerito">'._t('_ibdw_thirdcolumn_affinita2').'</div></div></div></div></div>';
     echo'
     <script>
     $("#deletesugg'.$infosugge['ID'].'").click(function() {
           var id_user     = $("#dlt'.$infosugge['ID'].'").attr("value");
           $.ajax({
 			     type: "POST",
 			     url: "modules/ibdw/3col/elimina_sugg.php",
 			     data: "id_user=" + id_user,
 			     success: function(html){
                       $("#rigasuggerimento'.$infosugge['ID'].'").fadeOut(1000);
                       aggiornaajax();
                       }
	 	      });
           });
          
     </script>
     <script>
     $("#suggerito'.$infosugge['ID'].'").click(function() {
           var id_user     = $("#dlt'.$infosugge['ID'].'").attr("value");
           var nome        = $("#name'.$infosugge['ID'].'").attr("value");
           $.ajax({
 			     type: "POST",
 			     url: "modules/ibdw/3col/aggiungi_sugg.php",
 			     data: "id_user=" + id_user,
 			     success: function(html){
                       $("#rigasuggerimento'.$infosugge['ID'].'").fadeOut(1000);
                       aggiornaajax();
                       createOne();
                       }
 		      });
           });        
     </script>';
	  }
   }
   if ($miolimite>0) echo '</div>';
  }
  }
}