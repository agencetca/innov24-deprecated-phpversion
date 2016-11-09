<?php
if ($funclass->ActionVerify($profilemembership,"EVO WALL - Post sharing"))
{
 echo '<a id="bottone_sub_elimina" class="bottone_sub_elimina'.$codiceazione.'" href="javascript:substratocondivisione'.$assegnazione.'()">'._t("_ibdw_evowall_condividi").'</a>
       <script>
        function substratocondivisione'.$assegnazione.'() {$(".elimxx").fadeOut(); $(".condxx").fadeOut(); $(".condivisionemessaggio'.$assegnazione.'").fadeIn(1);open_bt_list('.$codiceazione.');oscura();}
        function annulla_condivisione'.$assegnazione.'() {$(".condivisionemessaggio'.$assegnazione.'").fadeOut(1);schiarisci();}
       </script>';
       $sharefb=0;
       $sharegoogle=0;
       $sharetwitter=0;
	   $sharelinkedin=0;
	   $sharepinterest=0;
	   $sharebaidu=0;
	   $shareweibo=0;
	   $shareqzone=0;
	   
	   if ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowfbshare') and $fbenabled) $sharefb=1;
	   if ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowgoogleshare') and $ggenabled) $sharegoogle=1;
	   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowtwshare') and $twenabled) $sharetwitter=1;
	   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlinkedinshare') and $lienabled) $sharelinkedin=1;
	   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowpinterestshare') and $psenabled) $sharepinterest=1;
	   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowbaidushare') and $bienabled) $sharebaidu=1;
	   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowweiboshare') and $wbenabled) $shareweibo=1;
	   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowqzoneshare') and $qzenabled) $shareqzone=1;

       if($row['lang_key']=='_bx_photos_spy_added' OR $row['lang_key']=='_bx_photos_spy_comment_posted' OR $row['lang_key']=='_bx_photos_spy_rated' OR $row['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione' OR $row['lang_key']=='_bx_photo_add_condivisione') 
	   {
        $titolo=$unserialize['entry_caption'];
        if($attivaintegrazione==0) $url=$unserialize['entry_url'];
		else $url=BX_DOL_URL_ROOT.'page/photoview?iff='.$pdxidfoto;
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfoto[16].'.'.$rowfoto[3];
        $descrizione=$rowfoto[7];
       }
       
       if($row['lang_key']=='_bx_photoalbumshare') 
	   {  $sharegoogle=0;
        $sharetwitter=0;
	      $sharelinkedin=0;
	      $sharepinterest=0;
	      $sharebaidu=0;
	      $shareweibo=0;
	      $shareqzone=0;
        $titolo=readyshare($fetchassoc['Caption']);
        $url=$generaurl;
        $descrizione=readyshare($fetchassoc['Description']);
        $getidfirstphoto="SELECT sys_albums_objects.id_object,sys_albums.Caption,bx_photos_main.ID,bx_photos_main.Title,bx_photos_main.Hash,bx_photos_main.Ext  FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album) INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID=".$idalbum." ORDER BY ID DESC LIMIT 0,1";
        $getidfphoto=mysql_query($getidfirstphoto);
        $runidphotoget=mysql_fetch_assoc($getidfphoto);
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$runidphotoget['Hash'].'.'.$runidphotoget['Ext'];
       }

       if($row['lang_key']=='_bx_videos_spy_added' OR $row['lang_key']=='_bx_videos_spy_rated' OR $row['lang_key']=='_bx_videos_spy_comment_posted' OR $row['lang_key']=='_ibdw_evowall_bx_video_add_condivisione') 
	   {
        $titolo=readyshare($unserialize['entry_caption']);
        $url=$unserialize['entry_url'];
        if($rowvideo[4]=='youtube') 
		{ 
         $immagine='http://i.ytimg.com/vi/'.$rowvideo[5].'/maxresdefault.jpg';
         $descrizione=$rowvideo[2];
        }
        else 
	    {
         $immagine=BX_DOL_URL_ROOT.'flash/modules/video/files/'.$rowvideo[0].'.jpg';
         $descrizione=$rowvideo[2];
        }
       }
       
       if($row['lang_key']=='_bx_ads_added_spy' OR $row['lang_key']=='_bx_ads_rated_spy' OR $row['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione') 
	   { 
        $titolo=$unserialize['ads_caption'];
        $url=$unserialize['ads_url'];
        $immagine=BX_DOL_URL_ROOT.'media/images/classifieds/thumb_'.$rowqueryannunciofoto[1];
        $descrizione=$rowqueryannuncio[6];
       }
       
       if($row['lang_key']=='_bx_sites_poll_add' OR $row['lang_key']=='_bx_sites_poll_rate' OR $row['lang_key']=='_bx_sites_poll_commentPost' OR $row['lang_key']=='_bx_sites_poll_change' OR $row['lang_key']=='_ibdw_evowall_bx_site_add_condivisione')
	   {
		$titolo=$unserialize['site_caption'];
        $url=$unserialize['site_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowsito[4].'.'. $rowsito[5];
        $descrizione=$rowrichiestasito[3];
       }
       
       if($row['lang_key']=='_bx_events_spy_post' OR $row['lang_key']=='_bx_events_spy_join' OR $row['lang_key']=='_bx_events_spy_rate' OR $row['lang_key']=='_bx_events_spy_comment' OR $row['lang_key']=='_bx_events_spy_post_change' OR $row['lang_key']=='_ibdw_evowall_bx_event_add_condivisione') 
	   {
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotoevento[3].'.'. $rowfotoevento[1];
        $descrizione=$rowevento[2];
       }
       
       if($row['lang_key']=='_bx_groups_spy_post' OR $row['lang_key']=='_bx_groups_spy_post_change' OR $row['lang_key']=='_bx_groups_spy_join' OR $row['lang_key']=='_bx_groups_spy_rate' OR $row['lang_key']=='_bx_groups_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione') 
	   {
		$titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$rowgruppo[2];
       }
       
       if($row['lang_key']=='_bx_poll_added' OR $row['lang_key']=='_bx_poll_answered' OR $row['lang_key']=='_bx_poll_rated' OR $row['lang_key']=='_bx_poll_commented' OR $row['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') 
	   { 
        $titolo=$unserialize['poll_caption'];
        $url=$unserialize['poll_url'];
        $immagine='0';
        $descrizione='0';
       }
	   
	   if($row['lang_key']=='_modzzz_property_spy_post' OR $row['lang_key']=='_modzzz_property_spy_post_change' OR $row['lang_key']=='_modzzz_property_spy_join' OR $row['lang_key']=='_modzzz_property_spy_rate' OR $row['lang_key']=='_modzzz_property_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_property_share') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$fotoarray[0];
        $descrizione=$descrizioneproperty." $".number_format($rowqueryproperty['price']);
       }
	   
	   if($row['lang_key']=='_bx_blog_added_spy' OR $row['lang_key']=='_bx_blog_rated_spy' OR $row['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione') 
	   { 
        $titolo=$unserialize['post_caption'];
        $url=$unserialize['post_url'];
        $immagine=BX_DOL_URL_ROOT.'media/images/blog/big_'.$rowqueryblog[6];
        $descrizione=$descrizioneblog;
       }
	   
	   if($row['lang_key']=='_bx_sounds_spy_added' OR $row['lang_key']=='_bx_sounds_spy_comment_posted' OR $row['lang_key']=='_bx_sounds_spy_rated' OR $row['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_caption'];
        $url=$unserialize['entry_url'];
        $immagine='0';
        $descrizione=$descrizionesound;
       }
	   
	   if($row['lang_key']=='_ibdw_evowall_bx_url_add' or $row['lang_key']=='_ibdw_evowall_bx_url_share') 
	   { 
        $titolo=$unserialize['titolosito'];
        $url=$unserialize['indirizzo'];
        $immagine=$unserialize['immagine'];
        $descrizione=$unserialize['descrizione'];
       }
	   
	   if($row['lang_key']=='_ue30_event_spy_post' OR $row['lang_key']=='_ue30_event_spy_join' OR $row['lang_key']=='_ue30_event_spy_rate' OR $row['lang_key']=='_ue30_event_spy_comment' OR $row['lang_key']=='_ue30_event_spy_post_change' OR $row['lang_key']=='_ibdw_evowall_ue30_event_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url= $unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotoevento[3].'.'. $rowfotoevento[1];
        $descrizione=$rowevento[2];
       }
	   
	   if($row['lang_key']=='_ue30_location_spy_post' OR $row['lang_key']=='_ue30_location_spy_post_change' OR $row['lang_key']=='_ue30_location_spy_join' OR $row['lang_key']=='_ue30_location_spy_rate' OR $row['lang_key']=='_ue30_location_spy_comment' OR $row['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotoevento[3].'.'. $rowfotoevento[1];
        $descrizione=$rowevento[2];
       }
	   
	   if($row['lang_key']=='_modzzz_club_spy_post' OR $row['lang_key']=='_modzzz_club_spy_post_change' OR $row['lang_key']=='_modzzz_club_spy_join' OR $row['lang_key']=='_modzzz_club_spy_rate' OR $row['lang_key']=='_modzzz_club_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_clubs_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$rowgruppo[2];
       }
	   
	   if($row['lang_key']=='_modzzz_pets_spy_post' OR $row['lang_key']=='_modzzz_pets_spy_post_change' OR $row['lang_key']=='_modzzz_pets_spy_rate' OR $row['lang_key']=='_modzzz_pets_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_pets_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$rowgruppo[2];
       }
	   
	   if($row['lang_key']=='_modzzz_petitions_spy_post' OR $row['lang_key']=='_modzzz_petitions_spy_post_change' OR $row['lang_key']=='_modzzz_petitions_spy_join' OR $row['lang_key']=='_modzzz_petitions_spy_rate' OR $row['lang_key']=='_modzzz_petitions_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_petitions_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$rowgruppo[2];
       }
	   
	   if($row['lang_key']=='_modzzz_bands_spy_post' OR $row['lang_key']=='_modzzz_bands_spy_post_change' OR $row['lang_key']=='_modzzz_bands_spy_join' OR $row['lang_key']=='_modzzz_bands_spy_rate' OR $row['lang_key']=='_modzzz_bands_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_bands_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$rowgruppo[2];
       }
	   
	   if($row['lang_key']=='_modzzz_schools_spy_post' OR $row['lang_key']=='_modzzz_schools_spy_post_change' OR $row['lang_key']=='_modzzz_schools_spy_join' OR $row['lang_key']=='_modzzz_schools_spy_rate' OR $row['lang_key']=='_modzzz_schools_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_schools_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$rowgruppo[2];
       }
	   
	   if($row['lang_key']=='_modzzz_notices_spy_post' OR $row['lang_key']=='_modzzz_notices_spy_post_change' OR $row['lang_key']=='_modzzz_notices_spy_rate' OR $row['lang_key']=='_modzzz_notices_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_notices_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine='0';
        $descrizione=$rowgruppo[2];
       }
	   
	   if($row['lang_key']=='_modzzz_classified_spy_post' OR $row['lang_key']=='_modzzz_classified_spy_post_change' OR $row['lang_key']=='_modzzz_classified_spy_rate' OR $row['lang_key']=='_modzzz_classified_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$rowgruppo[2];
       }
	   
	   if($row['lang_key']=='_modzzz_news_spy_post' OR $row['lang_key']=='_modzzz_news_spy_post_change' OR $row['lang_key']=='_modzzz_news_spy_rate' OR $row['lang_key']=='_modzzz_news_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_news_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$rowgruppo[2];
       }
	   
	   if($row['lang_key']=='_modzzz_jobs_spy_post' OR $row['lang_key']=='_modzzz_jobs_spy_post_change' OR $row['lang_key']=='_modzzz_jobs_spy_join' OR $row['lang_key']=='_modzzz_jobs_spy_rate' OR $row['lang_key']=='_modzzz_jobs_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_jobs_share') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$fotoarray[0];
        $descrizione=$descrizioneproperty." $".number_format($rowqueryproperty['price']);
       }
	   
	   if($row['lang_key']=='_modzzz_listing_spy_post' OR $row['lang_key']=='_modzzz_listing_spy_post_change' OR $row['lang_key']=='_modzzz_listing_spy_join' OR $row['lang_key']=='_modzzz_listing_spy_rate' OR $row['lang_key']=='_modzzz_listing_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_listing_share') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$fotoarray[0];
        $descrizione=$descrizioneproperty." $".number_format($rowqueryproperty['price']);
       }
	   
    if($sharefb==1) echo '<a id="bottone_sub_elimina" href="javascript:open_fb(\''.addslashes(strip_tags ($titolo)).'\',\''.$immagine.'\',\''.addslashes(htmlspecialchars(strip_tags ($descrizione))).'\',\''.$url.'\')">'._t("_ibdw_evowall_facebook_share").'</a>';
    if($sharegoogle==1) echo '<a id="bottone_sub_elimina" href="javascript:open_google(\''.addslashes(strip_tags ($titolo)).'\',\''.$url.'\',\''.$immagine.'\',\''.addslashes(htmlspecialchars(strip_tags ($descrizione))).'\')">'._t("_ibdw_evowall_google_share").'</a>';
    if($sharetwitter==1) echo '<a id="bottone_sub_elimina" href="javascript:open_twitter(\''.addslashes(strip_tags ($titolo)).'\',\''.$url.'\',\''.$immagine.'\',\''.addslashes(htmlspecialchars(strip_tags ($descrizione))).'\')">'._t("_ibdw_evowall_twitter_share").'</a>';
    if($sharelinkedin==1) echo '<a id="bottone_sub_elimina" href="javascript:open_linkedin(\''.addslashes(strip_tags ($titolo)).'\',\''.$url.'\',\''.$immagine.'\',\''.addslashes(htmlspecialchars(strip_tags ($descrizione))).'\')">'._t("_ibdw_evowall_linkedin_share").'</a>';
	if($sharepinterest==1) echo '<a id="bottone_sub_elimina" href="javascript:open_pinterest(\''.addslashes(strip_tags ($titolo)).'\',\''.$url.'\',\''.$immagine.'\',\''.addslashes(htmlspecialchars(strip_tags ($descrizione))).'\')">'._t("_ibdw_evowall_pinterest_share").'</a>';	
	if($sharebaidu==1) echo '<a id="bottone_sub_elimina" href="javascript:open_baidu(\''.addslashes(strip_tags ($titolo)).'\',\''.$url.'\',\''.$immagine.'\',\''.addslashes(htmlspecialchars(strip_tags ($descrizione))).'\')">'._t("_ibdw_evowall_baidu_share").'</a>';
	if($shareweibo==1) echo '<a id="bottone_sub_elimina" href="javascript:open_weibo(\''.addslashes(strip_tags ($titolo)).'\',\''.$url.'\',\''.$immagine.'\',\''.addslashes(htmlspecialchars(strip_tags ($descrizione))).'\')">'._t("_ibdw_evowall_weibo_share").'</a>';
	if($shareqzone==1) echo '<a id="bottone_sub_elimina" href="javascript:open_qzone(\''.addslashes(strip_tags ($titolo)).'\',\''.$url.'\',\''.$immagine.'\',\''.addslashes(htmlspecialchars(strip_tags ($descrizione))).'\')">'._t("_ibdw_evowall_qzone_share").'</a>';
}
?>