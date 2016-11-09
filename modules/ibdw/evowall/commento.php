<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
include_once (BX_DIRECTORY_PATH_MODULES.'boonex/avatar/include.php');
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
mysql_query("SET NAMES 'utf8'");

$verifica = "SELECT uri FROM sys_modules WHERE uri='ibdwemail'";
$exegui = mysql_query($verifica);
$num_ver = mysql_num_rows($exegui);

if($num_ver!=0) {include('../ibdwemail/email_notify.php');}
//dichiarazione funzioni


require_once('functions.php');
$funclass=new swfunc();
$accountid = (int)$_COOKIE['memberID'];

$id=$_POST['user'];
$pagina=$_POST['pagina'];
$assegnazione=$_POST['assegnazione'];
$limitecommento=$commnum;
if ($id==$accountid) 
{
 $azioneamico=$_POST['id'];
 $commentopost=str_replace("'", "`", $_POST['commento']);
 $queryricavaamico="SELECT sender_id FROM bx_spy_data WHERE id=$azioneamico";
 $resultricavaamico = mysql_query($queryricavaamico);
 $rowricavaamico = mysql_fetch_row($resultricavaamico);	
 $querydiverifica="(SELECT * FROM sys_friend_list WHERE id=$rowricavaamico[0] and Profile=$accountid and sys_friend_list.Check=1) UNION (SELECT * FROM sys_friend_list WHERE id=$accountid and Profile=$rowricavaamico[0] and sys_friend_list.Check=1)";
 $resultdiverifica = mysql_query($querydiverifica);
 $rowdiverifica = mysql_num_rows($resultdiverifica);
 $query  = "INSERT INTO commenti_spy_data (data,user,commento) VALUES ('".$_POST['id']."', '".$_POST['user']."', '".$commentopost."')";
 $result = mysql_query($query);
 $pippo=mysql_insert_id(); 
 
 $queryc  = "UPDATE bx_spy_data SET PostCommentsN=PostCommentsN+1 WHERE id=".$_POST['id'];
 $resultc = mysql_query($queryc);
  
 $controllo_prop = "SELECT sender_id,params,lang_key FROM bx_spy_data WHERE id = ".$_POST['id']." LIMIT 1";
 $execontrollo_prop = mysql_query($controllo_prop);
 $fetch_assoc = mysql_fetch_assoc($execontrollo_prop);
 
 $sender_id_prop = $fetch_assoc['sender_id']; 
 
  //sender nickname
  $aInfomember1=getProfileInfo($_POST['user']);
  $miosender=$aInfomember1['NickName'];

  //recipient nickname
  $aInfomember2=getProfileInfo($sender_id_prop);
  $miorecipient=$aInfomember2['NickName'];
  
  if (
  $fetch_assoc['lang_key']=='_bx_photos_spy_added' OR $fetch_assoc['lang_key']=='_bx_videos_spy_added' OR $fetch_assoc['lang_key']=='_bx_poll_added'
  OR $fetch_assoc['lang_key']=='_bx_groups_spy_post' OR $fetch_assoc['lang_key']=='_bx_events_spy_post' OR $fetch_assoc['lang_key']=='_bx_sites_poll_add'
  OR $fetch_assoc['lang_key']=='_bx_ads_added_spy' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione' 
  OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_video_add_condivisione'
  OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione' 
  OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_event_add_condivisione'
  OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_site_add_condivisione' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione'  
  OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione' OR $fetch_assoc['lang_key']=='_bx_blog_added_spy'
  OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione' OR $fetch_assoc['lang_key']=='_bx_sounds_spy_added'
  OR $fetch_assoc['lang_key']=='_ue30_event_spy_post' OR $fetch_assoc['lang_key']=='_ue30_event_add_condivisione'
  OR $fetch_assoc['lang_key']=='_ue30_location_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione'
  OR $fetch_assoc['lang_key']=='_modzzz_property_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_modzzz_property_share'
  OR $fetch_assoc['lang_key']=='_modzzz_club_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_clubs_add_condivisione'
  OR $fetch_assoc['lang_key']=='_modzzz_petitions_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_petitions_add_condivisione'
  OR $fetch_assoc['lang_key']=='_modzzz_bands_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_bands_add_condivisione'
  OR $fetch_assoc['lang_key']=='_modzzz_pets_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_pets_add_condivisione'
  OR $fetch_assoc['lang_key']=='_modzzz_schools_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_schools_add_condivisione'
  OR $fetch_assoc['lang_key']=='_modzzz_notices_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_notices_add_condivisione'
  OR $fetch_assoc['lang_key']=='_modzzz_classified_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione'
  OR $fetch_assoc['lang_key']=='_modzzz_news_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_news_add_condivisione'
  OR $fetch_assoc['lang_key']=='_modzzz_jobs_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_jobs_add_condivisione'
  OR $fetch_assoc['lang_key']=='_modzzz_listing_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione'
  )
  {
  $unserialize = unserialize($fetch_assoc['params']);
  $url_specifico = 1;	
  if($fetch_assoc['lang_key']=='_bx_photos_spy_added' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_photo'; $url_element = $unserialize['entry_url']; }
  elseif($fetch_assoc['lang_key']=='_bx_videos_spy_added' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_video_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_video'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_bx_poll_added' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_poll'; $url_element = $unserialize['poll_url'];}
  elseif($fetch_assoc['lang_key']=='_bx_groups_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_groups'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_bx_events_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_event_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_events'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_bx_sites_poll_add' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_site_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_sites'; $url_element = $unserialize['site_url'];}                   
  elseif($fetch_assoc['lang_key']=='_bx_ads_added_spy' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_ads'; $url_element = $unserialize['ads_url'];}
  elseif($fetch_assoc['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione' OR $fetch_assoc['lang_key']=='_bx_blog_added_spy') {$lang_string='_ibdw_evowall_notify_comment_blogs'; $url_element = $unserialize['post_url'];}
  elseif($fetch_assoc['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione' OR $fetch_assoc['lang_key']=='_bx_sounds_spy_added') {$lang_string='_ibdw_evowall_notify_comment_sounds'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_ue30_event_spy_post' OR $fetch_assoc['lang_key']=='_ue30_event_add_condivisione') {$lang_string = '_ibdw_evowall_notify_comment_events'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_ue30_location_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_ue30location'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_property_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_modzzz_property_share') {$lang_string='_ibdw_evowall_notify_comment_modzzz_property'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_club_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_clubs_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_clubs'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_petitions_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_petitions_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_petitions'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_bands_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_bands_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_bands'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_pets_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_pets_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_pets'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_schools_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_schools_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_schools'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_notices_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_notices_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_notices'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_classified_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_classified'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_news_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_news_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_news'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_jobs_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_jobs_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_jos'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_listing_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_listing'; $url_element = $unserialize['entry_url'];}
 } 
  else {$lang_string='_ibdw_evowall_notify_comment';$url_specifico=0;}
  
  $array["sender_p_link"] = BX_DOL_URL_ROOT.$miosender;
  $array["sender_p_nick"] = $miosender;
  $array["recipient_p_link"] = BX_DOL_URL_ROOT.$miorecipient;
  $array["recipient_p_nick"] = $miorecipient;
  if($url_specifico == 1) { $array["url"] = $url_element; }
  $str = serialize($array);
  
  if($_POST['user']!=$sender_id_prop)
  {
   $query_wall="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES ('".$_POST['user']."','".$sender_id_prop."','".$lang_string."','".$str."','profiles_activity')";
   $result_wall=mysql_query($query_wall);
  }
  $query2="INSERT INTO datacommenti (IDCommento) VALUES ('".$pippo."')";
  $result2 = mysql_query($query2);
  $proprietario = "SELECT id,sender_id FROM bx_spy_data WHERE id='$assegnazione'";
  $esegui = mysql_query($proprietario);
  $fetch = mysql_fetch_assoc($esegui);
  $newpro = $fetch['sender_id'];
  $parametriass = $assegnazione.'###'.$pippo;
  if($newpro != $accountid AND $istruzione_4 == '1' AND $num_ver != 0) { email_notify('commento',$newpro,$accountid,$parametriass); }
}
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
include 'templatesw.php';
echo '<div id="newentry'.$assegnazione.'"></div>';
echo '<div id="commenti'.$assegnazione.'" class="commenti">';
echo '<script>
		function altricommenti'.$assegnazione.'(assegnazione,limitecommento,pagina) 
		{
		 $("#swich_comment'.$assegnazione.' .othnews").css({"background-image" : "url('.$imagepath.'load.gif)" , "background-repeat" : "no-repeat" , "background-position" : "left","padding-left" : "20px"});
		 $.ajax({type: "POST",url: "modules/ibdw/evowall/altricommenti.php",data: "assegnazione=" + assegnazione + "&limitecommento=" + limitecommento + "&pagina=" + pagina,
		 success: function(html)
		 			{
					 $("#commenti'.$assegnazione.'").remove();
					 $("#newentry'.$assegnazione.'").append(html);
					}
		});
		};
		</script>';
		//ottengo la data di pubblicazione del post per confrontarla con le date dei commenti che devono essere successivi al post ovviamente
        $querydatapost="SELECT bx_spy_data.date FROM bx_spy_data WHERE ID=$assegnazione";
        $resultdatapost = mysql_query($querydatapost);
        $valdata = mysql_fetch_assoc($resultdatapost);

		$querycontacommenti="SELECT commenti_spy_data.data FROM commenti_spy_data LEFT JOIN datacommenti ON commenti_spy_data.id=datacommenti.IDCommento WHERE commenti_spy_data.data=$assegnazione and datacommenti.date>'".$valdata['date']."'";
		
		
		$resultcontacommenti = mysql_query($querycontacommenti);
		$rowcontacommenti = mysql_num_rows($resultcontacommenti);
		if ($rowcontacommenti==0) {echo '';}
		else 
		{
		 if ($rowcontacommenti==1) {$titlecommentis=_t('_ibdw_evowall_comment_title1');$endcomment=_t('_ibdw_evowall_comment_titlef1');}
		 else {$titlecommentis=_t('_ibdw_evowall_comment_title2');$endcomment=_t('_ibdw_evowall_comment_titlef2');}
		 echo '<div class="commentreport"><div class="comm">'.$titlecommentis.' <span class="numerocommenti'.$assegnazione.'">'.$rowcontacommenti.'</span> '.$endcomment.'</b></div>';
		 if($rowcontacommenti>$limitecommento) {echo '<div class="vedialtro"><a href="javascript:altricommenti'.$assegnazione.'('.$assegnazione.','.$limitecommento.',\''.$pagina.'\')" class="othnews">'._t("_ibdw_evowall_altricommenti").'</a></div>';}
		 echo '</div>';
		}
		if($ordinec=='Last') {$tipoordine="DESC";}
		else {$tipoordine="ASC";}
		
		$querydelcommento="SELECT * FROM (SELECT t1.*,t2.date, Profiles.ID as MYID, Profiles.NickName, Profiles.Avatar FROM (commenti_spy_data AS t1 LEFT JOIN datacommenti as t2 ON t1.id=t2.IDCommento) INNER JOIN Profiles ON t1.user = Profiles.ID WHERE data=$assegnazione ORDER BY t1.id DESC LIMIT 0,".$limitecommento.") AS t3 WHERE date>'".$valdata['date']."' ORDER BY t3.id ASC";
		
		
		$resultdelcommento = mysql_query($querydelcommento);
		echo '<div id="nuovocommento'.$assegnazione.'"> </div>';
		echo '<div id="swich_comment'. $assegnazione.'" class="swichwidth">';
		while($rowdelcommento = mysql_fetch_array($resultdelcommento))
		{
		 echo '<div id="commentario" class="super_commento'.$rowdelcommento[id].'">';
		 if ($rowdelcommento[user]==$accountid ) 
		 {
		  echo '
        <script>
        $(document).ready(function(){
          var configs = {    
          over: fade_cmnt_BT'.$rowdelcommento[id].',  
          out: out_cmnt_BT'.$rowdelcommento[id].',
          interval:1
        };
        $(".super_commento'.$rowdelcommento[id].'").hoverIntent(configs);		
      });
      function fade_cmnt_BT'.$rowdelcommento[id].'()
        {
          $("#elimina'.$rowdelcommento[id].'").fadeIn(1);
        }
      function out_cmnt_BT'.$rowdelcommento[id].'()
        {
          $("#elimina'.$rowdelcommento[id].'").css(\'display\',\'none\');
        }
      </script>
      <div id="elimina'.$rowdelcommento[id].'" class="eliminab" style="display:none;">
		  	<form id="elimina'.$rowdelcommento[id].'" action="javascript:elimina();">
				 <input id="id'.$rowdelcommento[id].'" type="hidden" name="id" value="'.$rowdelcommento[id].'">
				 <input id="assegnazione'.$assegnazione.'" type="hidden" name="id" value="'.$assegnazione.'">
				 <input id="pagina'.$assegnazione.'" type="hidden" name="id" value="'.$pagina.'">
				 <input id="limite'.$assegnazione.'" type="hidden" name="id" value="'.$limitecommento.'">
				 <input type="image" src="modules/ibdw/evowall/templates/uni/css/immagini/depr.png" title="'._t('_ibdw_evowall_delete').'" class="elimina'.$rowdelcommento[id].'">
				</form>
				</div>
				<script>
		$("#elimina'.$rowdelcommento[id].'").submit(function() 
		{
		 var id=$("#id'.$rowdelcommento[id].'").attr("value");
		 $(".elimina'.$rowdelcommento[id].'").val("Wait");
		 var numero_commento = $(".numerocommenti'.$assegnazione.'").html(); 
		 var def_numero_commento = parseInt(numero_commento)-1;
		 $.ajax({type: "POST", url: "modules/ibdw/evowall/elimina.php", data: "id=" + id + "&idpost='.$assegnazione.'",
		 success: function(html)
		 {
		  $(".super_commento'.$rowdelcommento[id].'").fadeOut(1);
		  $(".numerocommenti'.$assegnazione.'").html(def_numero_commento); 
		 }});
		 });
		 </script>';
		 }
		 $cmn=str_replace("-->","--&gt;",str_replace("<--","&lt;--",$rowdelcommento[commento]));
		 $cmn = strip_tags($cmn);
		 $cmn=$funclass->urlreplace($cmn);
		 $cmn=str_replace("`", "'", $cmn);
	 
		 $miadatac=$funclass->TempoPost($rowdelcommento['date'],$seldate,$offset);
	 
		 echo '<div id="single_comment'.$rowdelcommento[id].'" class="single_comment"><div id="contentcomm"><div id="avacomm">';
		 if ($rowdelcommento['Avatar']<>"0") {echo '<img class="mioavatsmallx" src="'.BX_AVA_URL_USER_AVATARS.$rowdelcommento['Avatar'].'i'.BX_AVA_EXT.'">';}
		 else 
		 {
		  if ($rowdelcommento['Sex']=="female") { echo '<img class="mioavatsmallx" src="/templates/base/images/icons/woman_small.gif">'; }
		  else { echo '<img class="mioavatsmallx" src="/templates/base/images/icons/man_small.gif">';}
		 }
		echo '</div><div id="commcomm">';
		 
		 if($usernameformat=='Nickname') {echo '<a href="'.$rowdelcommento[NickName].'"><b>'.$rowdelcommento[NickName].'</b></a>: '.$cmn.'<div class="stydata">'.$miadatac.'</div></div></div></div></div>';}
		 if($usernameformat=='Full name')
		 {
             $aInfomember = getProfileInfo($rowdelcommento['user']);
             $realname =  ucfirst($aInfomember['FirstName']) . " " . ucfirst($aInfomember['LastName']);   
             echo '<a href="'.$rowdelcommento[NickName].'"><b>'.$realname.'</b></a>: '.$cmn.'<div class="stydata">'.$miadatac.'</div></div></div></div></div>';
		 }
		 if($usernameformat=='FirstName')
		 {
		  $aInfomember = getProfileInfo($rowdelcommento['user']);
		  $realname =  ucfirst($aInfomember['FirstName']);
		  echo '<a href="'.$rowdelcommento[NickName].'"><b>'.$realname.'</b></a>: '.$cmn.'<div class="stydata">'.$miadatac.'</div></div></div></div></div>';
		 }
		}
		echo '<div id="altricommenti'.$assegnazione.'"></div></div></div>';
?>