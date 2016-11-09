<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
include_once (BX_DIRECTORY_PATH_MODULES.'boonex/avatar/include.php');
mysql_query("SET NAMES 'utf8'");

include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
include 'templatesw.php';
require_once('functions.php');
$funclass=new swfunc();
$verifica="SELECT uri FROM sys_modules WHERE uri = 'ibdwemail'";
$exegui=mysql_query($verifica);
$num_ver=mysql_num_rows($exegui);
if($num_ver != 0) include('../ibdwemail/email_notify.php');
$accountid=(int)$_COOKIE['memberID'];
$user=$_POST['user'];
$idnotizia=$_POST['id'];
$set=$_POST['set'];
if($set==0)
{
 $controllo="SELECT id_notizia FROM ibdw_likethis WHERE id_notizia='".$idnotizia."' AND id_utente=".$user;
 echo $controllo;
 $esegui=mysql_query($controllo);
 $numeri=mysql_num_rows($esegui);
 if ($user==$accountid AND $numeri==0) 
 {
  $query="INSERT INTO ibdw_likethis (id_notizia,id_utente,ibdw_likethis.like) VALUES ('".$_POST['id']."', '".$_POST['user']."', '1')";
  $result=mysql_query($query); 
  $queryc  = "UPDATE bx_spy_data SET PostLikeN=PostLikeN+1 WHERE id=".$_POST['id'];
  $resultc = mysql_query($queryc);
  $proprietario = "SELECT id,sender_id,params,lang_key FROM bx_spy_data WHERE id='$idnotizia'";
  $esegui = mysql_query($proprietario);
  $fetch = mysql_fetch_assoc($esegui);
  $newpro = $fetch['sender_id'];
  
  if($newpro != $accountid AND $istruzione_1 == '1' AND $num_ver != 0 ) { email_notify('like_action',$newpro,$accountid,$idnotizia); }
  
  $aInfomember1=getProfileInfo($accountid);
  $aInfomember2=getProfileInfo($newpro);
  if ($usernameformat=='Nickname')
  {
   //il nickname del sender
   $miosender=$aInfomember1['NickName'];
   //il nickname del recipient
   $miorecipient=$aInfomember2['NickName'];
  }
  elseif ($usernameformat=='FirstName')
  {
   //il nickname del sender
   $miosender=ucfirst($aInfomember1['FirstName']);
   //il nickname del recipient
   $miorecipient=ucfirst($aInfomember2['FirstName']);
  }
  elseif($usernameformat=='Full name')
  {
   //il nickname del sender
   $miosender=ucfirst($aInfomember1['FirstName'])." ".ucfirst($aInfomember1['LastName']);
   //il nickname del recipient
   $miorecipient=ucfirst($aInfomember2['FirstName'])." ".ucfirst($aInfomember2['LastName']);
  }
  $miosendernickname=$aInfomember1['NickName'];
  $miorecipientnickname=$aInfomember2['NickName'];
  if (
  $fetch['lang_key']=='_bx_photos_spy_added' OR $fetch['lang_key']=='_bx_videos_spy_added' OR $fetch['lang_key']=='_bx_poll_added'
  OR $fetch['lang_key']=='_bx_groups_spy_post' OR $fetch['lang_key']=='_bx_events_spy_post' OR $fetch['lang_key']=='_bx_sites_poll_add'
  OR $fetch['lang_key']=='_bx_ads_added_spy' OR $fetch['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione' OR $fetch['lang_key']=='_ibdw_evowall_bx_video_add_condivisione'
  OR $fetch['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione' OR $fetch['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione'
  OR $fetch['lang_key']=='_ibdw_evowall_bx_event_add_condivisione' OR $fetch['lang_key']=='_ibdw_evowall_bx_site_add_condivisione' OR $fetch['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione'
  OR $fetch['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione' OR $fetch['lang_key']=='_bx_blog_added_spy' OR $fetch['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione' OR $fetch['lang_key']=='_bx_sounds_spy_added'
  OR $fetch['lang_key']=='_ue30_event_spy_post' OR $fetch['lang_key']=='_ue30_event_add_condivisione'
  OR $fetch['lang_key']=='_ue30_location_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione'
  OR $fetch['lang_key']=='_modzzz_property_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_modzzz_property_share'
  OR $fetch['lang_key']=='_modzzz_club_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_clubs_add_condivisione'
  OR $fetch['lang_key']=='_modzzz_petitions_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_petitions_add_condivisione'
  OR $fetch['lang_key']=='_modzzz_bands_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_bands_add_condivisione'
  OR $fetch['lang_key']=='_modzzz_pets_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_pets_add_condivisione'
  OR $fetch['lang_key']=='_modzzz_schools_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_schools_add_condivisione'
  OR $fetch['lang_key']=='_modzzz_notices_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_notices_add_condivisione'
  OR $fetch['lang_key']=='_modzzz_classified_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione'
  OR $fetch['lang_key']=='_modzzz_news_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_news_add_condivisione'
  OR $fetch['lang_key']=='_modzzz_jobs_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_jobs_add_condivisione'
  OR $fetch['lang_key']=='_modzzz_listing_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione'
  )
  {
    $unserialize=unserialize($fetch['params']);
    $url_specifico=1;	
    if($fetch['lang_key']=='_bx_photos_spy_added' OR $fetch['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_photo'; $url_element = $unserialize['entry_url']; }
    elseif($fetch['lang_key']=='_bx_videos_spy_added'  OR $fetch['lang_key']=='_ibdw_evowall_bx_video_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_video'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_bx_poll_added' OR $fetch['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_poll'; $url_element = $unserialize['poll_url'];}
    elseif($fetch['lang_key']=='_bx_groups_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_groups'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_bx_events_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_event_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_events'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_bx_sites_poll_add' OR $fetch['lang_key']=='_ibdw_evowall_bx_site_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_sites'; $url_element = $unserialize['site_url'];}                   
    elseif($fetch['lang_key']=='_bx_ads_added_spy' OR $fetch['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_ads'; $url_element = $unserialize['ads_url'];}
	elseif($fetch['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione' OR $fetch['lang_key']=='_bx_blog_added_spy') {$lang_string='_ibdw_evowall_notify_like_blogs'; $url_element = $unserialize['post_url'];}
    elseif($fetch['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione' OR $fetch['lang_key']=='_bx_sounds_spy_added') {$lang_string='_ibdw_evowall_notify_like_sounds'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_ue30_event_spy_post' OR $fetch['lang_key']=='_ue30_event_add_condivisione') {$lang_string = '_ibdw_evowall_notify_like_events'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_ue30_location_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_ue30location'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_modzzz_property_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_modzzz_property_share') {$lang_string='_ibdw_evowall_notify_like_modzzz_property'; $url_element = $unserialize['entry_url'];}
	elseif($fetch['lang_key']=='_modzzz_club_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_clubs_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_clubs'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_modzzz_petitions_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_petitions_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_petitions'; $url_element = $unserialize['entry_url'];}
	elseif($fetch['lang_key']=='_modzzz_bands_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_bands_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_bands'; $url_element = $unserialize['entry_url'];}
	elseif($fetch['lang_key']=='_modzzz_pets_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_pets_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_pets'; $url_element = $unserialize['entry_url'];}
	elseif($fetch['lang_key']=='_modzzz_schools_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_schools_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_schools'; $url_element = $unserialize['entry_url'];}
	elseif($fetch['lang_key']=='_modzzz_notices_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_notices_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_notices'; $url_element = $unserialize['entry_url'];}
	elseif($fetch['lang_key']=='_modzzz_classified_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_classified'; $url_element = $unserialize['entry_url'];}
	elseif($fetch['lang_key']=='_modzzz_news_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_news_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_news'; $url_element = $unserialize['entry_url'];}
  	elseif($fetch['lang_key']=='_modzzz_jobs_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_jobs_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_jobs'; $url_element = $unserialize['entry_url'];}
	elseif($fetch['lang_key']=='_modzzz_listing_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_listing'; $url_element = $unserialize['entry_url'];}
  } 
  else {$lang_string='_ibdw_evowall_notify_like';$url_specifico=0;}
  $array["sender_p_link"]= BX_DOL_URL_ROOT.$miosendernickname;
  $array["sender_p_nick"]= $miosender;
  $array["recipient_p_link"]=BX_DOL_URL_ROOT.$miorecipientnickname;
  $array["recipient_p_nick"]=$miorecipient;
  if($url_specifico == 1) $array["url"]=$url_element;
  $str = serialize($array);	
  if($accountid!=$newpro) 
  {
   $query_wall="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES ('".$accountid."','".$newpro."','".$lang_string."','".$str."','profiles_activity')";
   $result_wall=mysql_query($query_wall);
  }
 }
}
elseif($set==1) {
 $query="DELETE FROM ibdw_likethis WHERE id_notizia=".$idnotizia." AND id_utente= ".$user;
 $result=mysql_query($query);
 
 $queryc="UPDATE bx_spy_data SET PostLikeN=PostLikeN-1 WHERE id=".$idnotizia;
 $resultc=mysql_query($queryc);
}
elseif($set==2)
{
 $assegnazione=$idnotizia;
 $querylike="SELECT id_utente FROM ibdw_likethis WHERE id_notizia=".$assegnazione;
 $querylikeresult=mysql_query($querylike);
 $rowquerylikeconta=mysql_num_rows($querylikeresult);
 if ($rowquerylikeconta>2)
 {
    echo '
    <div class="notyd"></div><div id="element_like'.$assegnazione.'" class="elementlike">
      <img src="'.$imagepath.'likesym.png" class="ilicss" />
        <a href="javascript:slide_persone'.$assegnazione.'();">'.$rowquerylikeconta.' '._t("_ibdw_evowall_elementlike1").'</a> '._t("_ibdw_evowall_elementlike2").'
          <div id="slide_persone'.$assegnazione.'" class="slide_persone">
          <input type="hidden" id="slide_up'.$assegnazione.'" value="0">';
            while($rowquerylike = mysql_fetch_array($querylikeresult))
              {
                $aInfomember = getProfileInfo($rowquerylike['id_utente']);
                if($usernameformat=='Nickname') {$realname =  ucfirst($aInfomember['NickName']);}
                elseif($usernameformat=='Full name') {$realname =  ucfirst($aInfomember['FirstName']) . " " . ucfirst($aInfomember['LastName']);}
                elseif($usernameformat=='FirstName') {$realname =  ucfirst($aInfomember['FirstName']);}
   
                echo '<div id="contbox_user_like"><div id="avat_like_box">';
		              if ($aInfomember['Avatar']<>"0") {echo '<img class="mioavatsmallx" src="'.BX_AVA_URL_USER_AVATARS.$aInfomember['Avatar'].'i'.BX_AVA_EXT.'">';}
		              else 
		                {
		                  if ($aInfomember['Sex']=="female") { echo '<img class="mioavatsmallx" src="/templates/base/images/icons/woman_small.gif">'; }
		                  else { echo '<img class="mioavatsmallx" src="/templates/base/images/icons/man_small.gif">';}
		                }
		        echo '</div><div id="user_like_box"><a href="'.ucfirst($aInfomember['NickName']).'" class="friend_like_name">'.$realname.'</a>';
          if (is_friends($accountid,$rowquerylike['id_utente'])==FALSE AND $rowquerylike['id_utente']!=$accountid) 
          {
            if ($funclass->CountMutualFriends3($accountid,$rowquerylike['id_utente'])>0) echo '<div class="box_likemutual"><b>'.$funclass->CountMutualFriends3($accountid,$rowquerylike['id_utente']).'</b> '._t("_ibdw_evowall_mutualfriend").'</div>';
	          else echo '<div class="box_likemutual"></div>'; 
          }
        $idutenteidx = $rowquerylike['id_utente'];
        if(is_friends($accountid,$rowquerylike['id_utente'])==FALSE AND $rowquerylike['id_utente']!=$accountid) { 
          //controlla che la richiesta non sia inoltrata    
          $query="SELECT ID,Profile,sys_friend_list.Check FROM sys_friend_list WHERE ID = '$accountid' AND Profile = '$idutenteidx' AND sys_friend_list.Check=0";
          $esegui = mysql_query($query);
          $verifica = mysql_num_rows($esegui);
          if($verifica != 0) { echo '<div class="box_aggiungilike" id="box_aggiungilike'.$idutenteidx.'">'._t("_ibdw_evowall_friendlike2").'</div>'; }
          else { echo '<div class="box_aggiungilike" id="box_aggiungilike'.$idutenteidx.'"><a href="javascript:aggiungi_friend'.$rowquerylike['id_utente'].'('.$idutenteidx.')";>'._t("_ibdw_evowall_friendlike").'</a></div>'; }
        }
    echo '</div></div>
  	<script>
		 function slide_persone'.$assegnazione.'() {
		 var slide_up = $("#slide_up'.$assegnazione.'").val();
		 if(slide_up==0){
     $("#slide_persone'.$assegnazione.'").fadeIn(1);
     $("#slide_up'.$assegnazione.'").val(1);
     }
     else {
     $("#slide_persone'.$assegnazione.'").css(\'display\',\'none\');
     $("#slide_up'.$assegnazione.'").val(0);
     }
     }
		</script>
		<script>
		 function aggiungi_friend'.$rowquerylike['id_utente'].'(idevento) 
		 {
		  var id_user='.$rowquerylike['id_utente'].';
		  $.ajax({ type: "POST", url: "modules/ibdw/evowall/aggiungi_sugg.php", data: "id_user=" + id_user, success: function(html){ notifica_generale("'._t("_ibdw_evowall_notificalikefriend").'"); $("#box_aggiungilike"+idevento).fadeOut();}});
		 };
		</script>';
          }
echo '</div></div>';
} 
elseif($rowquerylikeconta>0)
{
 $contapiace=0;
 echo '<div class="notyd"></div><div id="element_like'.$assegnazione.'" class="elementlike"><img class="ilicss" src="'.$imagepath.'likesym.png" /> ';
 while($rowquerylike = mysql_fetch_array($querylikeresult))
 {
  if (($rowquerylikeconta == 1) AND ($rowquerylike['id_utente'] == $accountid )) {echo _t("_ibdw_evowall_ilikeit");}
  else 
  {
   $contapiace=$contapiace+1;
   $aInfomember = getProfileInfo($rowquerylike['id_utente']);
   if($contapiace==1) {echo '<b> '._t("_ibdw_evowall_thiselementlike").' </b>';}
   if($usernameformat=='Nickname') {$realname=ucfirst($aInfomember['NickName']);}
   elseif($usernameformat=='Full name') {$realname=ucfirst($aInfomember['FirstName']) . " " . ucfirst($aInfomember['LastName']);}
   elseif($usernameformat=='FirstName') {$realname=ucfirst($aInfomember['FirstName']);}
   if ($contapiace>1) {echo ' '._t("_ibdw_evowall_likeand").' ';}
   if($rowquerylike['id_utente']==$accountid) { echo '<a href="'.$aInfomember['NickName'].'"> '._t("_ibdw_evowall_likeyou").' </a>';}
   else {echo '<a href="'.$aInfomember['NickName'].'">'.$realname.' </a>';}
  }
 }
 echo '</div>';
}
}

elseif($set==3){ 
$assegnazione = $idnotizia;
$querylikecontrollo="SELECT id_notizia,id_utente FROM ibdw_likethis WHERE id_notizia='$assegnazione' AND id_utente='$accountid'";
$querylikecontrolloresult= mysql_query($querylikecontrollo);
$rowquerylikecontrollo= mysql_num_rows($querylikecontrolloresult);

        if($rowquerylikecontrollo>=1) 
          {
            echo '
                <div id="nolike'.$assegnazione.'" class="nolike">
 		               <form name="likethis'.$assegnazione.'" id="nlikethis'.$assegnazione.'" action="javascript:raffa();">
		                <input id="nid_like'.$assegnazione.'" type="hidden" name="id" value="'. $assegnazione.'">
		                <input id="nuser_like'.$assegnazione.'" type="hidden" name="user" value="'. $accountid.'">
		                <input type="submit" value="'._t("_ibdw_evowall_unlikethis").'">
		               </form>
	               </div>
	   
                <script>
	                 $("#nlikethis'.$assegnazione.'").submit(function() 
	                   {
	                       var nid_like=$("#nid_like'.$assegnazione.'").attr("value");
		                     var nuser_like=$("#nuser_like'.$assegnazione.'").attr("value");
		                     $.ajax({type: "POST", url: "modules/ibdw/evowall/like_action.php", data: "id=" + nid_like +"&user=" + nuser_like +"&set=1",
		                     success: function(html){ajax_like_riepilogo'.$assegnazione.'();}});
		                 });
	             </script>';
        }

      else 
        {
              echo '
              <div id="like'.$assegnazione.'" class="boxlike">
 		               <form name="likethis'.$assegnazione.'" id="likethis'.$assegnazione.'" action="javascript:raffa();">
		                  <input id="id_like'.$assegnazione.'" type="hidden" name="id" value="'. $assegnazione.'">
		                  <input id="user_like'.$assegnazione.'" type="hidden" name="user" value="'. $accountid.'">
		                  <input id="like'.$assegnazione.'" type="hidden" name="like" value="1">
		                  <input type="submit" value="'._t("_ibdw_evowall_likethis").'">
		               </form>
	            </div>
	  
              <script>
	                 $("#likethis'.$assegnazione.'").submit(function() 
	                   {
	                     var id_like=$("#id_like'.$assegnazione.'").attr("value");
		                   var user_like=$("#user_like'.$assegnazione.'").attr("value");
		                   var like=$("#like'.$assegnazione.'").attr("value");
		                   $.ajax({type: "POST", url: "modules/ibdw/evowall/like_action.php", data: "id=" + id_like +"&user=" +user_like+"&set=0",
		                   success: function(html){ajax_like_riepilogo'.$assegnazione.'();}});
		                });
	            </script>';
      }
}
?>