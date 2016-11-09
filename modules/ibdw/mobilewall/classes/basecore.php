<?php 
 if($newsupdate!=1) { include('personal_wall.php'); }
  
 while($row = mysql_fetch_array($result))
 {
    $nonstampa_foto=0;
    $nonstampa_video=0;      
    $nonstampa_fotodlx = 0;
    //$notizia_eliminata=0;
    $unserialize = unserialize($row['params']);				     
    $inviatore=$row['sender_id'];
    $ricevitore=$row['recipient_id'];
    $avatar = avatar($inviatore);
    $codiceazione=$row['id']; 
    $miadata=TempoPost($row['date'],$seldate,$offset);
    $date = $miadata;
    $senderid = $row['sender_id'];
	
	
	//verifichiamo se il sender è nella lista dei favoriti. La query restituisce 1 se l'utente ha inserito il mio profilo nella lista dei suoi favoriti
	$queryfave="SELECT count(*) FROM sys_fave_list WHERE id=".$row['sender_id']. " AND Profile=".$mioid;
	$resultqfave = mysql_query($queryfave);
	$num_faves = mysql_fetch_row($resultqfave);
	$num_fave=$num_faves[0];
	//resetto la variabile che decide se il contenuto va mostrato o no
	$okvista='no';    
   
    // SI E' ISCRITTO
    if ($row['lang_key']=='_bx_spy_profile_has_joined') 
    {       
     $textright = estrainick($row['sender_id'],$usernameformat);    
     $index_2 =  _t("_ibdw_mobilewall_member_subscribed"); 
     $sharefunction = '';
     echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
     $c_action++;
    }

    
    // HA VOTATO, MODIFICATO, VISTO, COMMENTATO IL PROFILO
    elseif ($row['lang_key']=='_bx_spy_profile_has_rated' OR $row['lang_key']=='_bx_spy_profile_has_edited' OR $row['lang_key']=='_bx_spy_profile_has_viewed' OR $row['lang_key']=='_bx_spy_profile_has_commented') 
            { 
                    if ($row['lang_key']=='_bx_spy_profile_has_rated') {$stampa=_t("_ibdw_mobilewall_profile_rate"); }
                    elseif ($row['lang_key']=='_bx_spy_profile_has_viewed') {$stampa = _t("_ibdw_mobilewall_spyprofile"); }
                    elseif ($row['lang_key']=='_bx_spy_profile_has_commented') {$stampa = _t("_ibdw_mobilewall_comment_add");}
                    elseif ($row['lang_key']=='_bx_spy_profile_has_edited' ){$stampa=_t("_ibdw_mobilewall_profile_edit"); }
                                      
                    $textright = estrainick($row['sender_id'],$usernameformat); 
                    
                    $recipient_nick = estrainick($row['recipient_id'],$usernameformat);
                    $recipient_link = estrainick($row['recipient_id'],0);
                    $recipient_text = '<a href="bxprofile:'.$recipient_link.'">'.$recipient_nick.'</a>';
                    $stampa = str_replace('{recipient_p_nick}', $recipient_text, $stampa);   
                    $index_2 =  $stampa; 
                    $sharefunction = '';
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
            }
            
    // HA ACCETTATO L AMICIZIA
    elseif ($row['lang_key']=='_bx_spy_profile_friend_accept') 
            {       
                    $textright = estrainick($row['sender_id'],$usernameformat);    
                    $index_2 =  _t("_ibdw_mobilewall_isfriend"); 
                      
                    //correzione spywall
                    $recipient = $row['recipient_id'];
                    $recipient_nick = estrainick($recipient,$usernameformat); 
                    $recipient_link = estrainick($recipient,0); 
                    $recipient_text = '<a href="bxprofile:'.$recipient_link.'">'.$recipient_nick.'</a>';
                    $index_2 = str_replace('{recipient_p_nick}', $recipient_text, $index_2);   
                    //fine correzione spywall
                    $sharefunction = ''; 
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
            }
    
    // SITO AGGIUNTO, VOTATO, COMMENTATO, MODIFICATO, CONDIVISO
    elseif ($row['lang_key']=='_bx_sites_poll_add' OR $row['lang_key']=='_bx_sites_poll_rate' OR $row['lang_key']=='_bx_sites_poll_commentPost' OR $row['lang_key']=='_bx_sites_poll_change') 
            { 
                     if ($row['lang_key']=='_bx_sites_poll_add') {$stampa = _t("_ibdw_mobilewall_site_add");}
                     elseif ($row['lang_key']=='_bx_sites_poll_rate') {$stampa=_t("_ibdw_mobilewall_site_rate");}
                     elseif ($row['lang_key']=='_bx_sites_poll_commentPost') {$stampa=_t("_ibdw_mobilewall_site_comment");}
                     elseif ($row['lang_key']=='_bx_sites_poll_change') {$stampa=_t("_ibdw_mobilewall_site_edit");}
                     
                     $urlwebsite = "SELECT url FROM bx_sites_main WHERE	title = '".addSlashes($unserialize['site_caption'])."'";
                     $exeurlsite = mysql_query($urlwebsite);
                     $urlfetch = mysql_fetch_assoc($exeurlsite);
                     $urldef = $urlfetch['url'];
                     
                    //correzione spywall
                    $site_caption = $unserialize['site_caption']; 
                    $site_link = $urldef;
                    if(!preg_match('/http:/i',$site_link)){ $site_link = 'http://' .$site_link;}  
                    $site = '<a href="'.$site_link.'">'.$site_caption.'</a>';
                    //fine correzione spywall                  
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $stampa = str_replace('{site_caption}',$site,$stampa);  
                    $sharefunction = '';
                    echo stampa_azione($codiceazione,$avatar,$textright,$stampa,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
 
            }
            
    // VIDEO AGGIUNTO, VOTATO, COMMENTATO
    elseif ($row['lang_key']=='_bx_videos_spy_added' OR $row['lang_key']=='_bx_videos_spy_rated' OR $row['lang_key']=='_bx_videos_spy_comment_posted') 
    { 
     if ($row['lang_key']=='_bx_videos_spy_added') {$stampa = _t("_ibdw_mobilewall_video_add"); }
     elseif ($row['lang_key']=='_bx_videos_spy_rated') {$stampa = _t("_ibdw_mobilewall_rated_videos"); }
     elseif ($row['lang_key']=='_bx_videos_spy_comment_posted') {$stampa = _t("_ibdw_mobilewall_comment_name"); }
                    
     $trovaslash = substr_count($unserialize[entry_url], "/");
     $verificauri = explode ("/",$unserialize[entry_url]);
     $verificauri = $verificauri[$trovaslash];
                    
     $queryvideo="SELECT ID,Title,Description,Owner,Source,Video,Uri,Status FROM RayVideoFiles WHERE Owner=".$row['sender_id']." AND Uri='$verificauri'"; 
     $resultvideo = mysql_query($queryvideo);
     $rowvideo = mysql_fetch_assoc($resultvideo);
     $verificapresenza = $rowvideo['Title'];  
	 
	 if($verificapresenza!='') 
	 {     
      $tubeorno = $rowvideo['Source'];
      $id_video = $rowvideo['ID'];
	  
	  $querypriva="SELECT AllowAlbumView FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID = sys_albums_objects.id_album WHERE sys_albums_objects.id_object=".$id_video." AND sys_albums.Type = 'bx_videos'";
      $resultpriva = mysql_query($querypriva) or die(mysql_error());
      $rowpriva = mysql_fetch_row($resultpriva);
   
      $okvista=privstate($rowpriva[0],'videos', $row['sender_id'], $mioid, $num_fave,'');
      if ($okvista==1)
	  {
       $idusername = estrainick($rowvideo['Owner'],0);   
    
	   if($tubeorno == 'youtube') 
	   {
		$titolofoto = $unserialize[entry_caption];
		$recipientnick = estrainick($row['recipient_id'],$usernameformat);
		$video = $foto = estrai_foto($row['sender_id'],$verificauri,'video');			 
        $recuperoalbumid = "SELECT album.ID FROM sys_albums AS album INNER JOIN sys_albums_objects AS ogg ON album.ID = ogg.ID_album WHERE album.Owner = ".$rowvideo['Owner']." AND ogg.ID_object = ".$id_video." AND album.Type = 'bx_videos' LIMIT 1";
        $exerecuperoalbumid = mysql_query($recuperoalbumid);
        $fetchrecuperoalbumid = mysql_fetch_assoc($exerecuperoalbumid);
        $idalbum = $fetchrecuperoalbumid['ID']; 
        $videoanteprima = "<div class='imgbox'><img src='http://i.ytimg.com/vi/".$video."/default.jpg'/ onclick=\"location.href='bxvideo://".$idusername."@".$idalbum."/".$id_video."'\"></div>";
	    $stampa = str_replace('{video_caption}',$titolofoto,$stampa);
		$stampa = str_replace('{recipient_p_nick}',$recipientnick,$stampa);
		$textright = estrainick($row['sender_id'],$usernameformat);
		$index_2 = $stampa.$videoanteprima;
		$descrizionetag  = strip_tags(str_replace('"'," ",$titolofoto));
		$descrizionetag  = str_replace("'"," ",$titolofoto);
		$sharefunction = '<div onclick="condivisionegenerale(\''.$codiceazione.'\',\''. $mioid.'\',\'0\',\'_bx_videos_spy_added\',\''.$video.'\',\'0\',\'0\',\''.$descrizionetag.'\',\'0\',\'0\',\''.$descrizionetag.'\',\''. $unserialize[entry_url].'\',\'0\',\'1\',\'0\',\'0\')" class="addcommento"><img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/share.png"><p>'._t("_ibdw_mobilewall_sharefun").'</p></div>';
		echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
	   }
       else
	   {
		$titolofoto = $unserialize[entry_caption];
		$recipientnick = estrainick($row['recipient_id'],$usernameformat);
		$video = $foto = estrai_foto($row['sender_id'],$verificauri,'video');
		if($verificapresenza!='') {$videoanteprima = "<div class='imgbox'><img src='".$urlsite."flash/modules/video/files/".$id_video."_small.jpg' onclick=\"location.href='bxvideo://".$idusername."@".$idalbum."/".$id_video."'\"/></div>";}
		else {$notizia_eliminata++; $nonstampa_video=1;}
		$stampa = str_replace('{video_caption}',$titolofoto,$stampa);
		$stampa = str_replace('{recipient_p_nick}',$recipientnick,$stampa);
		$textright = estrainick($row['sender_id'],$usernameformat);
		$index_2 = $stampa.$videoanteprima;
		$sharefunction = '';
		echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
		$c_action++;
	   }
	  }
	  else {$notizia_eliminata++;}
	 }
	 else {$notizia_eliminata++;}
    }
			
    // ANNUNCIO AGGIUNTO, VOTATO
    elseif ($row['lang_key']=='_bx_ads_added_spy' OR $row['lang_key']=='_bx_ads_rated_spy') 
            { 
                    if ($row['lang_key']=='_bx_ads_added_spy') {$stampa=_t("_ibdw_mobilewall_ads_add"); }
                    elseif ($row['lang_key']=='_bx_ads_rated_spy') {$stampa=_t("_ibdw_mobilewall_ads_rate"); }           
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $titoloads = $unserialize[ads_caption];
                    $stampa = str_replace('{ads_caption}',$titoloads,$stampa);  
                    $index_2 = $stampa;  
                    $sharefunction = '';
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
            }         

    // FOTO AGGIUNTA, COMMENTATA, VOTATA
    elseif ($row['lang_key']=='_bx_photos_spy_added' OR $row['lang_key']=='_bx_photos_spy_comment_posted' OR $row['lang_key']=='_bx_photos_spy_rated') 
    { 
     if ($row['lang_key']=='_bx_photos_spy_added') {$stampa=_t("_ibdw_mobilewall_photo_add"); }
     elseif ($row['lang_key']=='_bx_photos_spy_comment_posted') {$stampa=_t("_ibdw_mobilewall_comment_nphoto"); }
     elseif ($row['lang_key']=='_bx_photos_spy_rated') {$stampa=_t("_ibdw_mobilewall_rate_photo"); }               
     $textright = estrainick($row['sender_id'],$usernameformat);                    
     $trovaslash = substr_count($unserialize[entry_url], "/");
     $verificauri = explode ("/",$unserialize[entry_url]);
     $verificauri = $verificauri[$trovaslash];
     $pdxrecuperofoto = "SELECT ID,Hash,Ext,Tags FROM bx_photos_main WHERE Uri = '$verificauri'";
	 
     $pdxeseguirecuperofoto = mysql_query($pdxrecuperofoto);
     $pdxrowrecuperfoto = mysql_fetch_assoc($pdxeseguirecuperofoto);
     $foto = $pdxrowrecuperfoto['Hash']; 
	 
     $estensione = $pdxrowrecuperfoto['Ext'];   
     $idfoto = $pdxrowrecuperfoto['ID'];
     $idusername = estrainick($row['sender_id'],0);
     
	 
	 if($pdxrowrecuperfoto['Hash']!='') 
     { 
	  //Check album's privacy
	  $querypriva="SELECT AllowAlbumView FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID = sys_albums_objects.id_album WHERE id_object = '$idfoto' AND TYPE = 'bx_photos'";
	  $resultpriva = mysql_query($querypriva);
      $rowpriva = mysql_fetch_row($resultpriva);
	   
	  $okvista=privstate($rowpriva[0],'photos', $row['sender_id'], $mioid, $num_fave,'');
	  if ($okvista==1)
	  {
       $recuperoalbumid = "SELECT album.ID FROM sys_albums AS album INNER JOIN sys_albums_objects AS ogg ON album.ID = ogg.ID_album WHERE album.Owner = ".$row['sender_id']." AND ogg.ID_object = ".$idfoto."  AND album.Type = 'bx_photos' LIMIT 1";
       $exerecuperoalbumid = mysql_query($recuperoalbumid);
       $fetchrecuperoalbumid = mysql_fetch_assoc($exerecuperoalbumid);
       $idalbum = $fetchrecuperoalbumid['ID']; 
       $titolofoto = $unserialize[entry_caption];									
       $recipientnick = estrainick($row['recipient_id'],$usernameformat);
       //$foto = estrai_foto($row['sender_id'],$verificauri,'foto');
       $tagsfoto = $pdxrowrecuperfoto['Tags'];

       $fotourl = "<div class='imgbox'><a href='bxphoto://".$idusername."@".$idalbum."/".$idfoto."'><img src='".$urlsite."m/photos/get_image/browse/".$foto.".jpg'></a></div>";
	   if(($tagsfoto=='events') or ($tagsfoto=='groups') or ($tagsfoto=='sites')) {$notizia_eliminata++; $nonstampa_foto=1; }
       $stampa = str_replace('{photo_caption}',$titolofoto,$stampa);  
       $stampa = str_replace('{recipient_p_nick}',$recipientnick,$stampa);
       $index_2 = $stampa.$fotourl;                    
       $sharefunction = '<div onclick="condivisionegenerale(\''.$codiceazione.'\','. $mioid.',0,\'_bx_photos_spy_added\',\''. $foto.'\',0,0,\''.$titolofoto.'\',0,\''. $estensione.'\',\''.$titolofoto.'\',\''. $unserialize[entry_url].'\','.$idfoto.',0,0,0)" class="addcommento"><img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/share.png"><p>'._t("_ibdw_mobilewall_sharefun").'</p></div>';
       if($nonstampa_foto!=1) { echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);}
	   else {$notizia_eliminata++;}
	  }
	  else {$notizia_eliminata++;}
	 } 
     else {$notizia_eliminata++; $nonstampa_foto=1;}
	 $c_action++;
    }
            
    // AGGIUNTO GRUPPO, CONDIVISO, MODIFICATO, ISCRIZIONE, VOTATO, COMMENTATO
    elseif ($row['lang_key']=='_bx_groups_spy_post' OR $row['lang_key']=='_bx_groups_spy_post_change' OR $row['lang_key']=='_bx_groups_spy_join' OR $row['lang_key']=='_bx_groups_spy_rate' OR $row['lang_key']=='_bx_groups_spy_comment') 
            { 
                     if ($row['lang_key']=='_bx_groups_spy_post') {$stampa = _t("_ibdw_mobilewall_group_add");}
                     elseif ($row['lang_key']=='_bx_groups_spy_post_change') {$stampa = _t("_ibdw_mobilewall_group_editaw");}
                     elseif ($row['lang_key']=='_bx_groups_spy_join') {$stampa = _t("_ibdw_mobilewall_group_join");}
                     elseif ($row['lang_key']=='_bx_groups_spy_rate') {$stampa = _t("_ibdw_mobilewall_group_rate");}
                     elseif ($row['lang_key']=='_bx_groups_spy_comment') {$stampa = _t("_ibdw_mobilewall_group_comment");}        
                     $titologruppo = $unserialize[entry_title];                    
                     $stampa = str_replace('{group_caption}',$titologruppo,$stampa);
                     $textright = estrainick($row['sender_id'],$usernameformat);
                     $index_2 = $stampa;  
                     $sharefunction = '';
                     echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                     $c_action++;
            }              

    // HA AGGIUNTO UN EVENTO, ISCRITTO, VOTATO, COMMENTATO, MODIFICATO
    elseif ($row['lang_key']=='_bx_events_spy_post' OR $row['lang_key']=='_bx_events_spy_join' OR $row['lang_key']=='_bx_events_spy_rate' OR $row['lang_key']=='_bx_events_spy_comment' OR $row['lang_key']=='_bx_events_spy_post_change') 
            { 
                    if ($row['lang_key']=='_bx_events_spy_post') {$stampa = _t("_ibdw_mobilewall_event_add");}
                    elseif ($row['lang_key']=='_bx_events_spy_join') {$stampa = _t("_ibdw_mobilewall_event_join");}
                    elseif ($row['lang_key']=='_bx_events_spy_rate') {$stampa = _t("_ibdw_mobilewall_event_rate");}
                    elseif ($row['lang_key']=='_bx_events_spy_comment') {$stampa = _t("_ibdw_mobilewall_event_comment");}
                    elseif ($row['lang_key']=='_bx_events_spy_post_change') {$stampa = _t("_ibdw_mobilewall_event_edit");}
                    $titologruppo = $unserialize[entry_title];                    
                    $stampa = str_replace('{event_caption}',$titologruppo,$stampa);
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $index_2 = $stampa;  
                    $sharefunction = '';
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
            }              

    // SONDAGGIO AGGIUNTO, RISPOSTO, VOTATO, COMMENTATO
    elseif ($row['lang_key']=='_bx_poll_added' OR $row['lang_key']=='_bx_poll_answered' OR $row['lang_key']=='_bx_poll_rated' OR $row['lang_key']=='_bx_poll_commented') 
            { 
                    if ($row['lang_key']=='_bx_poll_added') {$stampa = _t("_ibdw_mobilewall_poll_add");}
                    elseif ($row['lang_key']=='_bx_poll_answered') {$stampa=_t("_ibdw_mobilewall_reply_polls");}
                    elseif ($row['lang_key']=='_bx_poll_rated') {$stampa=_t("_ibdw_mobilewall_rated_polls");}
                    elseif ($row['lang_key']=='_bx_poll_commented') {$stampa = _t("_ibdw_mobilewall_comment_polls");}           
                    $titologruppo = $unserialize[poll_caption];   
                    $recipientnick = $unserialize[recipient_p_nick];                 
                    $stampa = str_replace('{poll_caption}',$titologruppo,$stampa);
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $stampa = str_replace('{recipient_p_nick}',$recipientnick,$stampa);
                    $index_2 = $stampa;  
                    $sharefunction = ''; 
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
            }  


   // HA CONDIVISO UN ANNUNCIO
    elseif ($row['lang_key']=='_bx_ads_add_condivisione') 
            { 
                    $stampa = _t("_ibdw_mobilewall_share_asdaction");                    
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $parametriannuncio=$row['params'];
                    $parametriannuncio = explode("##", $parametriannuncio);        
                    $stampa = str_replace('{ads_caption}', $parametriannuncio[1], $stampa);                    
                    $index_2 = $stampa;
                    $sharefunction = '';
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;            
            }

    // HA CONDIVISO UNA FOTO
    elseif ($row['lang_key']=='_bx_photo_add_condivisione') 
    { 
     $stampa = _t("_ibdw_mobilewall_share_photoaction");
     $textright = estrainick($row['sender_id'],$usernameformat);
     $parametriannuncio=$row['params'];
     $parametriannuncio = explode("##", $parametriannuncio);        
                    $stampa = str_replace('{photo_caption}', $parametriannuncio[1], $stampa);
                    $idusername = estrainick($row['sender_id'],0);
                    $recuperoalbumid = "SELECT album.ID FROM sys_albums AS album INNER JOIN sys_albums_objects AS ogg ON album.ID = ogg.ID_album WHERE album.Owner = ".$row['sender_id']." AND ogg.ID_object = ".$parametriannuncio[5]."  AND album.Type = 'bx_photos' LIMIT 1";
                    $exerecuperoalbumid = mysql_query($recuperoalbumid);
                    $fetchrecuperoalbumid = mysql_fetch_assoc($exerecuperoalbumid);
                    $idalbum = $fetchrecuperoalbumid['ID'];                     
                    $fotourl = "<div class='imgbox'><img src=\"".$urlsite."m/photos/get_image/browse/".$parametriannuncio[0].".".$parametriannuncio[3]."\" onclick=\"location.href='bxphoto://".$idusername."@".$idalbum."/".$parametriannuncio[5]."'\"/></div>";
                    $index_2 = $stampa.$fotourl;
                    $sharefunction = '<div onclick="condivisionegenerale(\''.$codiceazione.'\','. $mioid.',0,\'_bx_photos_spy_added\',\''. $parametriannuncio[0].'\',0,0,\''.$parametriannuncio[4].'\',0,\''. $parametriannuncio[3].'\',\''.$parametriannuncio[1].'\',\''. $parametriannuncio[2].'\','.$parametriannuncio[5].',0,0,0)" class="addcommento"><img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/share.png"><p>'._t("_ibdw_mobilewall_sharefun").'</p></div>';
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;         
            }
            
        // HA CONDIVISO UN VIDEO YOUTUBE
    elseif ($row['lang_key']=='_bx_videotube_add_condivisione') 
            { 
                    $stampa = _t("_ibdw_mobilewall_share_videoaction");
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $parametriannuncio=$row['params'];
                    $parametriannuncio = explode("##", $parametriannuncio);        
                    $indirizzoyou = 'http://www.youtube.com/v/'.$parametriannuncio[0];
                    $stampa = str_replace('{video_caption}', $parametriannuncio[1], $stampa);
                    $stampa = str_replace('{video_url}', $indirizzoyou, $stampa);                          
                    $fotourl = "<div class='imgbox'><img src=\"http://i.ytimg.com/vi/".$parametriannuncio[0]."/default.jpg\" onclick=\"location.href='http://www.youtube.com/v/".$parametriannuncio[0]."'\"/></div>";
                    $index_2 = $stampa.$fotourl;
                    $sharefunction = '<div onclick="condivisionegenerale(\''.$codiceazione.'\',\''. $mioid.'\',\'0\',\'_bx_videos_spy_added\',\''. $parametriannuncio[0].'\',\'0\',\'0\',\''.str_replace('"', "''", $parametriannuncio[3]).'\',\'0\',\'0\',\''.str_replace('"', "''", $parametriannuncio[1]).'\',\''. $parametriannuncio[2].'\',\'0\',\'1\',\'0\',\'0\')" class="addcommento"><img src="'.$urlsite.'modules/ibdw/mobilewall/templates/uni/images/share.png"><p>'._t("_ibdw_mobilewall_sharefun").'</p></div>';
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
            }

    // HA INSERITO UN MESSAGGIO PERSONALE
    elseif (($row['lang_key']=='_bx_spywall_message') OR ($row['lang_key']=='_bx_spywall_messageseitu')) 
            {  
                    $messaggio = urlreplace($unserialize['messaggioo']); 
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $index_2 = $messaggio;
                    $sharefunction = '';
					
					if($row['sender_id']==$row['recipient_id'])
					{
					 echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
					}
					else
					{
					 $recipiente = estrainick($row['recipient_id'],$usernameformat);
					 echo stampa_azione2($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction,$recipiente);
					}   
                    $c_action++;                   
            }
            
      // ALBUM SHARE IBDW PHOTO DELUXE
    elseif ($row['lang_key']=='_bx_photoalbumshare') 
            { 
                    $stampa = _t("_ibdw_mobilewall_share_album");               
                    $idalbum = $unserialize['idalbum'];                    
                    $estrazione = "SELECT ID,Caption,Owner FROM sys_albums WHERE ID=".$idalbum;
                    $esecuzione = mysql_query($estrazione);
                    $fetchassoc = mysql_fetch_assoc($esecuzione);                    
                    $stampa = str_replace('{album_caption}',$fetchassoc['Caption'], $stampa);
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $idusername = estrainick($row['sender_id'],0);                    
                    $estrazione = "SELECT sys_albums_objects.id_object,sys_albums.Caption, bx_photos_main.ID,bx_photos_main.Title , bx_photos_main.Hash,bx_photos_main.Ext  FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID = sys_albums_objects.id_album) INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID='$idalbum' ORDER BY ID DESC LIMIT 0,2";
                    $esegui = mysql_query($estrazione);
                    $fotourl = '<div class="clear"></div>';
                    while($foto = mysql_fetch_array($esegui)) {$fotourl = $fotourl."<div class='imgboxs'style='background:url(".$urlsite."m/photos/get_image/browse/".$foto['Hash'].".".$foto['Ext'].")' onclick=\"location.href='bxphoto://".$idusername."@".$idalbum."/".$foto['ID']."'\"></div>";} 
                    $fotourl = $fotourl.'<div class="clear"></div>';
                    $index_2 = $stampa.$fotourl;                    
                    $sharefunction = '';
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
            }
            
          // ALBUM TAG IBDW PHOTO DELUXE
    elseif ($row['lang_key']=='bx_photo_deluxe_tag') 
            { 
                    $stampa = _t("bx_mobilewall_photo_deluxe_tag");                     
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $idusername = estrainick($row['sender_id'],0);
                    $recipientnick = estrainick($row['recipient_id'],$usernameformat);  
                    $recipient_link = estrainick($row['recipient_id'],0);
                    $recipient_nick = estrainick($row['recipient_id'],$usernameformat);
                    $recipient_text = '<a href="bxprofile:'.$recipient_link.'">'.$recipient_nick.'</a>';
                    $stampa = str_replace('{recipient_p_nick}', $recipient_text, $stampa); 
                    $idalbum = $unserialize['idalbum'];
                    $idfoto = $unserialize['idfoto']; 
                    $estrazione = "SELECT Title,Uri,Owner,Hash,Ext,bx_photos_main.Desc FROM bx_photos_main WHERE ID='$idfoto'";
                    $esecuzione = mysql_query($estrazione);
                    $fetchassoc = mysql_fetch_assoc($esecuzione);
                    $estrazionea = "SELECT id_album,id_object FROM sys_albums_objects WHERE id_object='$idfoto'";
                    $esecuzionea = mysql_query($estrazionea);
                    $fetchassoca = mysql_fetch_assoc($esecuzionea);
                    $stampa = str_replace('{album_caption}',$fetchassoc['Title'], $stampa);
                    $hash = $fetchassoc['Hash'];
                    $exte = $fetchassoc['Ext']; 
                    $fotourl = '<div class="clear"></div>';
                    $fotourl = $fotourl."<div class='imgboxs'style='background:url(".$urlsite."m/photos/get_image/browse/".$hash.".".$exte.")' onclick=\"location.href='bxphoto://".$idusername."@".$fetchassoca['id_album']."/".$idfoto."'\"></div>"; 
                    $fotourl = $fotourl.'<div class="clear"></div>';
                    $index_2 = $stampa.$fotourl;                    
                    $sharefunction = '';
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
            }
            
              // ibdw photo deluxe commento
    elseif ($row['lang_key']=='bx_photo_deluxe_commentofoto') 
            { 
                    $stampa = _t("bx_mobilewall_photo_deluxe_commentofotomsg"); 
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $idusername = estrainick($row['recipient_id'],0);                    
                    $recipient_link = estrainick($row['recipient_id'],0);
                    $recipient_nick = estrainick($row['recipient_id'],$usernameformat);
                    $recipient_text = '<a href="bxprofile:'.$recipient_link.'">'.$recipient_nick.'</a>';
                    $stampa = str_replace('{recipient_p_nick}', $recipient_text, $stampa);                                                                           
                    $idfoto = $unserialize['idalbum'];                    
                    $estrazione = "SELECT Title,Uri,Owner,Hash,Ext,bx_photos_main.Desc FROM bx_photos_main WHERE ID='$idfoto'";
                    $esecuzione = mysql_query($estrazione);
                    $fetchassoc = mysql_fetch_assoc($esecuzione);                    
                    $recuperoalbumid = "SELECT album.ID FROM sys_albums AS album INNER JOIN sys_albums_objects AS ogg ON album.ID = ogg.ID_album WHERE album.Owner = ".$row['recipient_id']." AND ogg.ID_object = ".$idfoto."  AND album.Type = 'bx_photos' LIMIT 1";
                    $exerecuperoalbumid = mysql_query($recuperoalbumid);
                    $fetchrecuperoalbumid = mysql_fetch_assoc($exerecuperoalbumid);
                    $idalbum = $fetchrecuperoalbumid['ID'];                    
                    $stampa = str_replace('{album_caption}',$fetchassoc['Title'], $stampa);
                    $hash = $fetchassoc['Hash'];
                    $exte = $fetchassoc['Ext']; 
                    $fotourl = '<div class="clear"></div>';
                    $fotourl = $fotourl."<div class='imgboxs'style='background:url(".$urlsite."m/photos/get_image/browse/".$hash.".".$exte.")' onclick=\"location.href='bxphoto://".$idusername."@".$idalbum."/".$idfoto."'\"></div>"; 
                    $fotourl = $fotourl.'<div class="clear"></div>';
                    $numero_caratteri = 32;
                    $stringa_in_input = $unserialize['commento'];
                    if(strlen(trim($stringa_in_input))>$numero_caratteri) {$stringa_in_out = substr($stringa_in_input,0,strpos($stringa_in_input,' ',$numero_caratteri)).'...';}
                    else {$stringa_in_out = $stringa_in_input;}
                    $fotourl = $fotourl.'<div id="listalike"><img src="'.$urlsite.'/modules/ibdw/mobilewall/templates/uni/images/noty.png"><b>'.$textright.'</b>: '.$stringa_in_out.'</div>';
                    $index_2 = $stampa.$fotourl;                    
                    $sharefunction = '';
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
            }
            
    // ibdw photo deluxe commento album
    elseif ($row['lang_key']=='bx_photo_deluxe_commentoalbum') 
            { 
                    $stampa = _t("bx_mobilewall_photo_deluxe_commentoalbummsg"); 
                    $textright = estrainick($row['sender_id'],$usernameformat);
                    $recipient_link = estrainick($row['recipient_id'],0);
                    $recipient_nick = estrainick($row['recipient_id'],$usernameformat);
                    $recipient_text = '<a href="bxprofile:'.$recipient_link.'">'.$recipient_nick.'</a>';
                    $stampa = str_replace('{recipient_p_nick}', $recipient_text, $stampa); 
                    $idusername = estrainick($row['recipient_id'],0);                                                      
                    $idalbums = $unserialize['idalbum'];
                    $titolo = $unserialize['Caption'];
                    $commento = $unserialize['commento'];                                        
                    $stampa = str_replace('{album_caption}',$titolo, $stampa);                    
                    $fotourl = '<div class="clear"></div>';                    
                    $estrazione = "SELECT sys_albums_objects.id_object, sys_albums.Caption , bx_photos_main.ID,bx_photos_main.Title , bx_photos_main.Hash,bx_photos_main.Ext  FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID = sys_albums_objects.id_album) INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID='$idalbums' ORDER BY ID DESC LIMIT 0,2";
                    $esegui = mysql_query($estrazione);
                    $fotourl = '<div class="clear"></div>';
                    while($foto = mysql_fetch_array($esegui)) {$fotourl = $fotourl."<div class='imgboxs'style='background:url(".$urlsite."m/photos/get_image/browse/".$foto['Hash'].".".$foto['Ext'].")' onclick=\"location.href='bxphoto://".$idusername."@".$idalbums."/".$foto['ID']."'\"></div>";} 
                    $fotourl = $fotourl.'<div class="clear"></div>';                    
                    $numero_caratteri = 32;
                    $stringa_in_input = $commento;
                    if(strlen(trim($stringa_in_input))>$numero_caratteri) {$stringa_in_out = substr($stringa_in_input,0,strpos($stringa_in_input,' ',$numero_caratteri)).'...';}
                    else {$stringa_in_out = $stringa_in_input;}
                    $fotourl = $fotourl.'<div id="listalike"><img src="'.$urlsite.'/modules/ibdw/mobilewall/templates/uni/images/noty.png"><b>'.$textright.'</b>: '.$stringa_in_out.'</div>';
                    $index_2 = $stampa.$fotourl;                    
                    $sharefunction = '';
                    echo stampa_azione($codiceazione,$avatar,$textright,$index_2,$date,$mioid,$senderid,$sharefunction);
                    $c_action++;
            }
}

//$conteggioazioni = $conteggioazioni + $c_action;
if($notizia_eliminata!=0) 
{
 $inizioquery=$inizioquery+$c_action;
 $limite=$notizia_eliminata;
 $c_action=0;
 include('masterquery.php');
 $result = mysql_query($query);
 $numero = mysql_num_rows($result);
 $inizioquerys=$inizioquery+$limite;
 $notizia_eliminata=0;
 $newsupdate = 1;
 include('basecore.php');
}
//risetto limite al valore settato in amministrazione, tanto la query è già stata lanciata. quindi il nuovo basecore utilizzerà i valori della query lanciata prima
//di questa nuova inizializzazione
$limite=$riga['limite'];