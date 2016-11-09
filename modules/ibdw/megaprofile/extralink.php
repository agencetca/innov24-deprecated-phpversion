<?
//INIZIO PERSONALIZZAZIONE 

//PER MIOPROFILO

if ($IDmio==$ottieniID)
{
 $extralink = "SELECT * FROM ibdw_mega_extralink WHERE (Status=1 AND Destination=0) ORDER BY ibdw_mega_extralink.order ASC";
 $lanciaextralink = mysql_query($extralink);
 
 while($row = mysql_fetch_array($lanciaextralink))
 {
   $stringa=$row["LangKey"];
   $LinkProfileAction=str_replace("{profileNick}",getNickName($ottieniID),str_replace("#DOLROOT#",BX_DOL_URL_ROOT,str_replace("#UserID#",$ottieniID,$row["UrlDyn"])));
   echo '<a '.$LinkProfileAction.' class="profile_menu_link">'._t($stringa).'</a>';
 }
}

else
//PER GLI ALTRI PROFILI
{
 $extralink = "SELECT * FROM ibdw_mega_extralink WHERE (Status=1 AND Destination=1 AND Name<>'Simple Messenger') ORDER BY ibdw_mega_extralink.order ASC";
 $lanciaextralink = mysql_query($extralink) or die(mysql_error());
 $contaoccorrenze = mysql_num_rows($lanciaextralink);

 while($row = mysql_fetch_array($lanciaextralink))
 { 
   $stringa=$row["LangKey"];
   $LinkProfileAction=str_replace("{profileNick}",getNickName($ottieniID),str_replace("#DOLROOT#",BX_DOL_URL_ROOT,str_replace("#UserID#",$ottieniID,$row["UrlDyn"])));
   echo '<a '.$LinkProfileAction.' class="profile_menu_link">'._t($stringa).'</a>';
 }
}

//FINE PERSONALIZZAZIONE
?>