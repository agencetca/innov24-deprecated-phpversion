<?php
 if (!defined('BX_AVA_EXT')) {
    define ('BX_AVA_DIR_USER_AVATARS', BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/data/images/'); // directory where avatar images are stored
    define ('BX_AVA_URL_USER_AVATARS', BX_DOL_URL_MODULES . 'boonex/avatar/data/images/'); // base url for all avatar images
    define ('BX_AVA_EXT', '.jpg'); // default avatar extension
    define ('BX_AVA_W', 64); // avatar image width
    define ('BX_AVA_H', 64); // avatar image height
    define ('BX_AVA_ICON_W', 32); // avatar icon width
    define ('BX_AVA_ICON_H', 32); // avatar icon height
 }
 $tmpx=0;
 $aInfomembers=getProfileInfo($accountid);
 $nomedominio=dirname($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
 $proprietario=getID($_REQUEST['ID']);
 if(!isset($hidden_intro)) echo '<div id="listanotizie">';
 require_once (BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/functions.php');
 $funclass=new swfunc();
 
 //get if HTML5 module is active so to use it for media playing
 $getinfhtml5="SELECT COUNT(*) FROM sys_modules WHERE uri='h5av'";
 $gethtml5status=mysql_query($getinfhtml5);
 $html5status=mysql_fetch_array($gethtml5status);

 if ($html5status[0]==0) $playerused=0;
 elseif ($html5status[0]==1) $playerused=1;
 
 //Get the membership level
 $infoMember=getMemberMembershipInfo($accountid);
 $profilemembership=$infoMember['ID'];
 
 $limitazionequery=0;
 
 //PhotoDeluxe installation check
 $photodeluxe=0;
 $verificaphotodeluxe="SELECT uri FROM sys_modules WHERE uri='photo_deluxe'";
 $eseguiverificaphotodeluxe=mysql_query($verificaphotodeluxe);
 $numerophotodeluxe=mysql_num_rows($eseguiverificaphotodeluxe);
 if($numerophotodeluxe!=0) {$photodeluxe=1;}
 
 //integration modules check 
 if($photodeluxe==1) 
 {
  $integrazionepdx="SELECT integrazionespywallevo,spywallpreview FROM photodeluxe_config WHERE ind=1";
  $eseguiintregazionepdx=mysql_query($integrazionepdx);
  $rowintegrazionepdx=mysql_fetch_assoc($eseguiintregazionepdx);
  $attivaintegrazione=$rowintegrazionepdx['integrazionespywallevo']; 
  $evowallpreview=$rowintegrazionepdx['spywallpreview']; 
 }
 $verifica_partent=0;  
 while($row=mysql_fetch_array($result) and ($limitazionequery<=$limite-1))
 {    
  if(!isset($off_parent)) $off_parent=0;
  if($off_parent==0 AND $grouping=='on') $verifica_partent=$row['recordsfound']-1;   
  $limitazionequery++;
  if($limitazionequery==1 AND !isset($provavariabile)) {$ultimoid=$row['id']; $GLOBAL['ultimoid']=$row['id'];}
  $contanews++;
  $unserialize=unserialize($row['params']);
  $miadata0=$funclass->TempoPost($row['date'],$seldate,$offset);

  //DATE WITH ICON
  $miadata=$funclass->formaticondate($miadata0,$row['lang_key'],$imagepath);  
  		     
  //COMMONS
  $inviatore=$row['sender_id'];
  $ricevitore=$row['recipient_id'];
  $infoamico=getProfileInfo($row['sender_id']);
  $Miniaturaamico=get_member_icon($infoamico['ID'],'none',false);
  $codiceazione=$row['id'];
  $assegnazione=$row[id];         
  $parteintroduttiva='  
  <script>
   $(document).ready(function()
   {
    var config={over: fade_BT'.$assegnazione.',out: out_BT'.$assegnazione.',interval:1};
    $("#azione'.$assegnazione.'").hoverIntent(config);';
	$parteintroduttiva=$parteintroduttiva.'	
   });
   function fade_BT'.$assegnazione.'() {$("#fade_bt_list'.$assegnazione.'").fadeIn(1);}
   function out_BT'.$assegnazione.'() {$("#fade_bt_list'.$assegnazione.'").fadeOut(1);$(".ibdw_bt_superlist").fadeOut(1); $(".mm_setmenu").val(0);$("#fade_bt_list'.$assegnazione.'").removeClass("fix_in_border");$("#menutop_ajax").removeClass("fix_in_border");} 
  </script>              
  <div id="azione'.$assegnazione.'" class="azioni" ';
  if($off_parent==1 AND $contanews==1) $parteintroduttiva=$parteintroduttiva.'style="margin-top:-8px;"'; 
  $parteintroduttiva=$parteintroduttiva.'>'; 

//AVATAR: simple or default
if ($avatartype=="Default")
{
 $myface='<div id="avatar">'.$Miniaturaamico;
 $triangle='</div><div id="leftarr0"></div>';
}
else
{
 $LinkUtente=getProfileLink($infoamico['ID']);
 $triangle='</div><div id="leftarr"></div>';
 if ($infoamico['Avatar']<>"0") {$myface='<div id="avatarsimple"><a href="'.$LinkUtente.'"><img class="mioavats" src="'.BX_AVA_URL_USER_AVATARS.$infoamico['Avatar'].BX_AVA_EXT.'"></a>';}
 else 
 {
  if ($infoamico['Sex']=="female") {$myface='<div id="avatarsimple"><a href="'.$LinkUtente.'"><img class="mioavats" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/woman_medium.gif"></a>';}
  else {$myface='<div id="avatarsimple"><a href="'.$LinkUtente.'"><img class="mioavats" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/man_medium.gif"></a>';}
 }
}
$parteintroduttiva=$parteintroduttiva.$myface.$triangle;
//END

$idn++; 
//check if sender is in the favorite list. Return 1 if the members have me on his favorite list
$queryfave="SELECT count(*) FROM sys_fave_list WHERE id=".$row['sender_id']." AND Profile=".$proprietario;
$resultqfave=mysql_query($queryfave);
$num_faves=mysql_fetch_row($resultqfave);
$num_fave=$num_faves[0];
$okvista='no';
include 'customfeed.php';

//JOINED - ISCRITTO
if ($row['lang_key']=='_bx_spy_profile_has_joined') 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t("_ibdw_evowall_member_subscribed");
  echo $parteintroduttiva;
  echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
  if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
  {
   if($verifica_partent==0) 
   {
    echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
    include 'inc_eliminab.php';
    echo '</div>';
    include 'bt_external_eliminab.php'; 
   }
  }
  $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
  if($usernameformat=='Nickname') $stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);
  else $stampa=str_replace('{profile_nick}',$realname,$stampa);
  $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
  echo $stampa;
  echo '</span></div>';
  if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
  else $commentsarea='justdate.php';
  include ($commentsarea);
  echo '</div></div>';
 }
 else $tmpx++;
}
//END

//ALBUM SHARE PHOTODELUXE
elseif($row['lang_key']=='_bx_photoalbumshare' and ($funclass->ActionVerify($profilemembership,"EVO WALL - Photos"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t("_ibdw_evowall_share_album");
  $idalbum=$unserialize['idalbum'];
  $estrazione="SELECT ID,Caption,Owner,ObjCount,Description FROM sys_albums WHERE ID=".$idalbum;
  $esecuzione=mysql_query($estrazione);
  $fetchassoc=mysql_fetch_assoc($esecuzione);
  if ($fetchassoc['ObjCount']>0)
  {
   $generaurl=BX_DOL_URL_ROOT.'page/photodeluxe#ia='.criptcodex($idalbum).'&ui='.criptcodex($fetchassoc['Owner']);
   echo $parteintroduttiva;
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
    {
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
    
 	 if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['Caption']=$fetchassoc['Caption'];
      $parametri_photo['sender_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['sender_p_nick']=$aInfomembers['NickName'];
      $parametri_photo['entry_url']=$generaurl;
      $parametri_photo['idalbum']=$idalbum;
      $params_condivisione=serialize($parametri_photo);   
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; 
      $bt_condivisione_params['3']='_bx_photoalbumshare'; //Lang_Key_share
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
 	 }
	 echo '</div>'; //div che chiude il bt_list di evo
	 if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
	 if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
    }   
   }
   $selezioneutenza="SELECT NickName FROM Profiles WHERE ID=".$row['sender_id'];
   $eseguiselezioneutenza=mysql_query($selezioneutenza);
   $assocselezioneutenza=mysql_fetch_assoc($eseguiselezioneutenza);
   $nickassociazione=$assocselezioneutenza['NickName'];
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$nickassociazione,$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$nickassociazione,$stampa);
   $stampa=str_replace('{album_url}',$generaurl,$stampa);
   $stampa=str_replace('{album_caption}',$fetchassoc['Caption'],$stampa);
   echo $stampa;
   echo '</span><div id="content_album_share">';  
   $estrazione="SELECT sys_albums_objects.id_object,sys_albums.Caption,bx_photos_main.ID,bx_photos_main.Title,bx_photos_main.Hash,bx_photos_main.Ext  FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album) INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID=".$idalbum." ORDER BY ID DESC LIMIT 0,".$evowallpreview;
   $esegui=mysql_query($estrazione);
   $numerazioneal=mysql_num_rows($esegui);
   if($numerazioneal!=0) {        
   while($foto=mysql_fetch_array($esegui)) 
   {
    $id_generale_foto_dlx=$assegnazione.$foto['id_object'];
    if($photoautozoom=='on')
    {
     $script_js='
     <script>
      $(document).ready(function()
 	  {
       var config={over: dlx_pop'.$id_generale_foto_dlx.',out: out_dlx_pop'.$id_generale_foto_dlx.',interval:150};
       $("#dlx_photozoom'.$id_generale_foto_dlx.'").hoverIntent(config);	
       $("#dlx_photozoom'.$id_generale_foto_dlx.'").mouseover(function() {$(this).css("opacity","0.5");});
       $("#dlx_photozoom'.$id_generale_foto_dlx.'").mouseout(function() { $(this).css("opacity","1");  });	
     });
      function dlx_pop'.$id_generale_foto_dlx.'()
	  {
       var set_value_photozoom=$("#set_value_sharedlx'.$assegnazione.'").val();
       if(set_value_photozoom!='.$foto['id_object'].') 
	   {
        $("#dlx_photo'.$assegnazione.'").html("<img src=\"m/photos/get_image/file/'.$foto['Hash'].'.'.$foto['Ext'].'\">");
        var larg=$("#bloccoav").width(); 
        var new_larg=larg-20;
        var new_largs=new_larg+"px";
        $("#dlx_photo'.$assegnazione.'").css("max-width",new_largs);    
        $("#dlx_photozoom'.$id_generale_foto_dlx.' img").css("opacity","0.5");
        $("#dlx_photo'.$assegnazione.'").fadeIn(1);
        $("#dlx_photo'.$assegnazione.' img").fadeIn(1000);
        $("#set_value_sharedlx'.$assegnazione.'").val('.$foto['id_object'].'); 
      }
      }
      function out_dlx_pop'.$id_generale_foto_dlx.'() {$("#dlx_photozoom'.$id_generale_foto_dlx.' img").css("opacity","1");} 
     </script>';
    }
    else {$script_js='';}
    echo $script_js;
    echo '<div onclick="location.href=\''.$generaurl.'\'" class="albumshareevowall" id="dlx_photozoom'.$id_generale_foto_dlx.'" style="background-image:url(&quot;'.BX_DOL_URL_ROOT.'m/photos/get_image/browse/'.$foto['Hash'].'.'.$foto['Ext'].'&quot;);"></div>';}
   }
   else {echo '<div onclick="location.href=\''.$generaurl.'\'" id="albumshareevowall" style="background-image: url(&quot;'.BX_DOL_URL_ROOT.'modules/ibdw/photo_deluxe/templates/uni/css/immagini/photos.png&quot;);"></div>';}
   echo '</div></div>';
   echo '<div id="dlx_photo'.$assegnazione.'" class="pop_foto"></div><input type="hidden" id="set_value_sharedlx'.$assegnazione.'" value="0">';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea);
   if($verifica_partent!=0)  echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

//ALBUM TAG PHOTODELUXE
elseif($row['lang_key']=='bx_photo_deluxe_tag' and ($funclass->ActionVerify($profilemembership,"EVO WALL - Photos"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t("bx_photo_deluxe_tag_title");
  $idalbum=$unserialize['idalbum'];
  $idfoto=$unserialize['idfoto']; 
  $estrazione="SELECT Title,Uri,Owner,Hash,Ext,bx_photos_main.Desc,Size FROM bx_photos_main WHERE ID=".$idfoto;
  $esecuzione=mysql_query($estrazione);
  $fetchassoc=mysql_fetch_assoc($esecuzione);
  if($fetchassoc['Title']==FALSE) $tmpx++;
  else 
  {
   $querypriva="SELECT AllowAlbumView FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object=$idfoto AND TYPE='bx_photos'";
   $resultpriva=mysql_query($querypriva);
   $rowpriva=mysql_fetch_row($resultpriva);
   $okvista=$funclass->privstate($rowpriva[0],'photos',$row['recipient_id'],$accountid,$num_fave,'');
  
   if ($okvista==1)
   {
    echo $parteintroduttiva;
    echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
    {
     if($verifica_partent==0) 
	   {
      echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" />
            <a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div>
            <div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
            include 'inc_eliminab.php';
	    echo '</div>';
	    include 'bt_external_eliminab.php'; 
   	 }
    } 
    $selezioneutenza="SELECT NickName FROM Profiles WHERE ID=".$row['sender_id'];
    $eseguiselezioneutenza=mysql_query($selezioneutenza);
    $assocselezioneutenza=mysql_fetch_assoc($eseguiselezioneutenza);
    $nickassociazione=$assocselezioneutenza['NickName'];
    $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') $stampa=str_replace('{profile_nick}',$nickassociazione,$stampa);
    else $stampa=str_replace('{profile_nick}',$realname,$stampa);
    $stampa=str_replace('{profile_link}',$nickassociazione,$stampa);
    $selezioneutenza="SELECT NickName FROM Profiles WHERE ID=".$row['recipient_id'];
    $eseguiselezioneutenza=mysql_query($selezioneutenza);
    $assocselezioneutenza=mysql_fetch_assoc($eseguiselezioneutenza);
    $nickassociazione=$assocselezioneutenza['NickName'];
    $realname=$funclass->TipoUtente($row['recipient_id'],$usernameformat);
    if($usernameformat=='Nickname') $stampa=str_replace('{recipient_nick}',$nickassociazione,$stampa);
    else $stampa=str_replace('{recipient_nick}',$realname,$stampa);
    $stampa=str_replace('{recipient_link}',$nickassociazione,$stampa);
    $estrazionea="SELECT id_album,id_object FROM sys_albums_objects WHERE id_object=".$idfoto;
    $esecuzionea=mysql_query($estrazionea);
    $fetchassoca=mysql_fetch_assoc($esecuzionea);
    $generaurl=BX_DOL_URL_ROOT.'page/photoview?iff='.criptcodex($idfoto).'&ia='.criptcodex($fetchassoca['id_album']).'&ui='.criptcodex($fetchassoc['Owner']);
    $stampa=str_replace('{album_url}',$generaurl,$stampa);
    $stampa=str_replace('{album_caption}',$fetchassoc['Title'],$stampa);
    echo $stampa;
    echo '</div>';
    echo '<div id="bloccoav"><div id="anteprima" class="fadeMini'.$assegnazione.'">';
    $hash=$fetchassoc['Hash'];
    $exte=$fetchassoc['Ext']; 
    $titolofoto=$fetchassoc['Title'];
    $descrizion=$fetchassoc['Desc'];
    $phsize=$fetchassoc['Size'];  
    $descrizionecondividiw=$funclass->tagliaz($funclass->cleartesto($descrizion),300);
    if ($rowpriva[0]==2) echo '<a href="'.$generaurl.'"><img src="'.$imagepath.'unklock.png" class="unklockstyle2" alt="'._t("_ibdw_evowall_bx_access_denied").'"></a>';
    elseif ($idfoto==FALSE) echo '<a href="'.$generaurl.'"><img src="'.$imagepath.'unk.png" class="unklockstyle2"></a>';
    else
    {//use the original size if lower
     $widthpic=substr($phsize,0,stripos($phsize,"x"));
     if ($widthpic<$widthphoto) $wused=$widthpic;
     else {$wused=$widthphoto;}
     if($photolarge=='')
     {
      if ($widthpic>200) echo '<a href="'.$generaurl.'"><img class="unklockstyle" onload="$(this).fadeIn(200);" src="m/photos/get_image/browse/'.$hash.'.'.$exte.'"></a><div class="lents" onclick="open_medium('.$assegnazione.','.$wused.');"><img src="'.$imagepath.'zoom.png" title="'._t("_ibdw_evowall_maximize").'"></div></div><div id="anteprima_medium" class="fadeMedium'.$assegnazione.'"><div id="ray_foto_zoom'.$assegnazione.'"></div><input type="hidden" id="value_foto_zoom'.$assegnazione.'" value="m/photos/get_image/file/'.$hash.'.'.$exte.'" /></div><div id="descrizione"><a href="'.$generaurl.'"><h3>'.$titolofoto.'</h3></a><p>'.$descrizion.'</p></div></div>';
	    else echo '<a href="'.$generaurl.'"><img src="m/photos/get_image/browse/'.$hash.'.'.$exte.'" class="unklockstyle" onload="$(this).fadeIn(200);" width="'.$wused.'"></a></div><div id="descrizione"><a href="'.$generaurl.'"><h3>'.$titolofoto.'</h3></a> <p>'.$descrizion.'</p></div><div class="clear"></div></div>';
     }
     else echo '<a href="'.$generaurl.'"><img onload="$(this).fadeIn(200);" src="m/photos/get_image/file/'.$hash.'.'.$exte.'" width="'.$wused.'"></a></div><div id="descrizione"><a href="'.$generaurl.'"><h3>'.$titolofoto.'</h3></a><p>'.$descrizion.'</p></div></div>';
    }   
    if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
    else $commentsarea='justdate.php';
    include ($commentsarea);
    if($verifica_partent!=0)  echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div></div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
   }
   else $tmpx++;
  }
 }
 else $tmpx++;
}
//END
                                  
//PHOTODELUXE COMMENT 
elseif($row['lang_key']=='bx_photo_deluxe_commentofoto' and ($funclass->ActionVerify($profilemembership,"EVO WALL - Photos"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $idfoto=$unserialize['idalbum'];
  $estrazione="SELECT Title,Uri,Owner,Hash,Ext,bx_photos_main.Desc,Size FROM bx_photos_main WHERE ID=".$idfoto;
  $esecuzione=mysql_query($estrazione);
  $fetchassoc=mysql_fetch_assoc($esecuzione); 
  if($fetchassoc['Title']==FALSE) $tmpx++;
  else 
  {
   $querypriva="SELECT AllowAlbumView FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object=".$idfoto." AND TYPE='bx_photos'";
   $resultpriva=mysql_query($querypriva);
   $rowpriva=mysql_fetch_row($resultpriva);
   $okvista=$funclass->privstate($rowpriva[0],'photos',$row['recipient_id'],$accountid,$num_fave,'');
  
   if ($okvista==1)
   {
    if($row['sender_id']==$row['recipient_id']) $stampa=_t("_ibdw_evowall_notify_comment_photo_yown");
    else $stampa=_t("bx_photo_deluxe_commentofotomsg");
    echo $parteintroduttiva;  
    echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
    {
     if($verifica_partent==0) 
	   {
      echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
	    include 'inc_eliminab.php';
      echo '</div>';
      include 'bt_external_eliminab.php'; 
     }
    }
    $selezioneutenza="SELECT NickName FROM Profiles WHERE ID=".$row['sender_id'];
    $eseguiselezioneutenza=mysql_query($selezioneutenza);
    $assocselezioneutenza=mysql_fetch_assoc($eseguiselezioneutenza);
    $nickassociazione=$assocselezioneutenza['NickName'];
    $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') $stampa=str_replace('{sender_p_nick}',$nickassociazione,str_replace('{sender_p_link}',$nickassociazione,str_replace('{profile_nick}',$nickassociazione,$stampa)));
    else $stampa=str_replace('{sender_p_nick}',$realname,str_replace('{sender_p_link}',$nickassociazione,str_replace('{profile_nick}',$realname,$stampa)));
    $stampa=str_replace('{profile_link}',$nickassociazione,$stampa);
    $selezioneutenza="SELECT NickName FROM Profiles WHERE ID=".$row['recipient_id'];
    $eseguiselezioneutenza=mysql_query($selezioneutenza);
    $assocselezioneutenza=mysql_fetch_assoc($eseguiselezioneutenza);
    $nickassociazione=$assocselezioneutenza['NickName'];
    $realname=$funclass->TipoUtente($row['recipient_id'],$usernameformat);
    if($usernameformat=='Nickname') $stampa=str_replace('{recipient_nick}',$nickassociazione,$stampa);
    else $stampa=str_replace('{recipient_nick}',$realname,$stampa);
    $stampa=str_replace('{recipient_link}',$nickassociazione,$stampa);
    $estrazionea="SELECT id_album,id_object FROM sys_albums_objects WHERE id_object=".$idfoto;
    $esecuzionea=mysql_query($estrazionea);
    $fetchassoca=mysql_fetch_assoc($esecuzionea);
    $generaurl=BX_DOL_URL_ROOT.'page/photoview?iff='.criptcodex($idfoto).'&ia='.criptcodex($fetchassoca['id_album']).'&ui='.criptcodex($fetchassoc['Owner']);
    $stampa=str_replace('{album_url}',$generaurl,$stampa);
    $stampa=str_replace('{album_caption}',$fetchassoc['Title'],$stampa);
    echo $stampa;
    $numero_caratteri=32;
    $stringa_in_input=$unserialize['commento'];
    echo '</div>';
    if(strlen(trim($stringa_in_input))>$numero_caratteri) $testo=substr($stringa_in_input,0,strpos($stringa_in_input,' ',$numero_caratteri)).'...';
    else $testo=$stringa_in_input;
    echo '<div id="commentos"><p>'.$testo.'</p></div>';
    echo '<div id="bloccoav"><div id="anteprima" class="fadeMini'.$assegnazione.'">';
    $hash=$fetchassoc['Hash'];
    $exte=$fetchassoc['Ext']; 
    $titolofoto=$fetchassoc['Title'];
    $descrizion=$fetchassoc['Desc']; 
    $phsize=$fetchassoc['Size'];
    $descrizionecondividiw=$funclass->tagliaz($funclass->cleartesto($descrizion),300);
    if ($rowpriva[0]==2) echo '<a href="'.$generaurl.'"><img src="'.$imagepath.'unklock.png" class="unklockstyle2" alt="'._t("_ibdw_evowall_bx_access_denied").'"></a>';
    elseif ($idfoto==FALSE) echo '<a href="'.$generaurl.'"><img src="'.$imagepath.'unk.png" class="unklockstyle2"></a>';
    else 
    {
     //use the original size if lower
     $widthpic=substr($phsize,0,stripos($phsize,"x"));
     if ($widthpic<$widthphoto) $wused=$widthpic;
     else $wused=$widthphoto;
     if($photolarge=='')
	   {
	    if ($widthpic>200) echo '<a href="'.$generaurl.'"><img class="unklockstyle" onload="$(this).fadeIn(200);" src="m/photos/get_image/browse/'.$hash.'.'.$exte.'"></a><div class="lents" onclick="open_medium('.$assegnazione.','.$wused.');"><img src="'.$imagepath.'zoom.png" title="'._t("_ibdw_evowall_maximize").'"></div></div><div id="anteprima_medium" class="fadeMedium'.$assegnazione.'"><div id="ray_foto_zoom'.$assegnazione.'"></div><input type="hidden" id="value_foto_zoom'.$assegnazione.'" value="m/photos/get_image/file/'.$hash.'.'.$exte.'" /></div><div id="descrizione"><a href="'.$generaurl.'"><h3>'.$titolofoto.'</h3></a><p>'.$descrizion.'</p></div></div>';
 	    else echo '<a href="'.$generaurl.'"><img src="m/photos/get_image/browse/'.$hash.'.'.$exte.'" class="unklockstyle" onload="$(this).fadeIn(200);"></a></div><div id="descrizione"><a href="'.$generaurl.'"><h3>'.$titolofoto.'</h3></a> <p>'.$descrizion.'</p></div><div class="clear"></div></div>';
     }
     else echo '<a href="'.$generaurl.'"><img onload="$(this).fadeIn(200);" src="m/photos/get_image/file/'.$hash.'.'.$exte.'" width="'.$wused.'"></a></div><div id="descrizione"><a href="'.$generaurl.'"><h3>'.$titolofoto.'</h3></a><p>'.$descrizion.'</p></div></div>';
    } 
    if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
    else $commentsarea='justdate.php';
    include ($commentsarea);
    if($verifica_partent!=0)  echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div></div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
   } 
   else $tmpx++;
  }
 }
 else $tmpx++; 
}
//END


//PHOTODELUXE LIKE 
elseif($row['lang_key']=='_ibdw_photodeluxe_likeadd' and ($funclass->ActionVerify($profilemembership,"EVO WALL - Photos"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $idfoto=$unserialize['idfoto'];
  $estrazione="SELECT Title,Uri,Owner,Hash,Ext,bx_photos_main.Desc,Size FROM bx_photos_main WHERE ID=".$idfoto;
  $esecuzione=mysql_query($estrazione);
  $fetchassoc=mysql_fetch_assoc($esecuzione); 
  if($fetchassoc['Title']==FALSE) $tmpx++;
  else 
  {
   $querypriva="SELECT AllowAlbumView FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object=".$idfoto." AND TYPE='bx_photos'";
   $resultpriva=mysql_query($querypriva);
   $rowpriva=mysql_fetch_row($resultpriva);
   $okvista=$funclass->privstate($rowpriva[0],'photos',$row['sender_id'],$accountid,$num_fave,'');
	
   if ($okvista==1)
   {
    if($row['recipient_id']==0) $stampa=_t("_ibdw_evowall_notify_comment_like_yown");
    else $stampa=_t("bx_photo_deluxe_likesfotomsg");
    echo $parteintroduttiva;  
    echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
	  {
     if($verifica_partent==0)
	   {
      echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'">
            <input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" />
            <a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a>
            </div>
            <div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
      if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     
	    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
      {
       /**SHARING BLOCK**/
       $indirizzourl_true=BX_DOL_URL_ROOT.'m/photos/view/'.$fetchassoc['Uri'];
       include('bt_condivisione_1.php');
       $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName'];
       $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
       $parametri_photo['entry_url']=$indirizzourl_true;
       $parametri_photo['entry_caption']=$unserialize['title'];
       $params_condivisione=serialize($parametri_photo);   
       $bt_condivisione_params['1']=$accountid; //Sender
       $bt_condivisione_params['2']=$row['sender_id']; 
       $bt_condivisione_params['3']='_ibdw_evowall_bx_photo_add_condivisione'; //Lang_Key_share
       $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
       include('bt_condivisione_2.php');
       /**END SHARING BLOCK**/
	    }
	    echo '</div>'; //div che chiude il bt_list di evo
	    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
	    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     }   
    }
    $selezioneutenza="SELECT NickName FROM Profiles WHERE ID=".$row['sender_id'];
    $eseguiselezioneutenza=mysql_query($selezioneutenza);
    $assocselezioneutenza=mysql_fetch_assoc($eseguiselezioneutenza);
    $nickassociazione=$assocselezioneutenza['NickName'];
    $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') $stampa=str_replace('{sender_p_nick}',$nickassociazione,str_replace('{sender_p_link}',$nickassociazione,str_replace('{profile_nick}',$nickassociazione,$stampa)));
    else $stampa=str_replace('{sender_p_nick}',$realname,str_replace('{sender_p_link}',$nickassociazione,str_replace('{profile_nick}',$realname,$stampa)));
    $stampa=str_replace('{profile_link}',$nickassociazione,$stampa);
    $selezioneutenza="SELECT NickName FROM Profiles WHERE ID=".$row['recipient_id'];
    $eseguiselezioneutenza=mysql_query($selezioneutenza);
    $assocselezioneutenza=mysql_fetch_assoc($eseguiselezioneutenza);
    $nickassociazione=$assocselezioneutenza['NickName'];
    $realname=$funclass->TipoUtente($row['recipient_id'],$usernameformat);
    if($usernameformat=='Nickname') $stampa=str_replace('{recipient_nick}',$nickassociazione,$stampa);
    else $stampa=str_replace('{recipient_nick}',$realname,$stampa);
    $stampa=str_replace('{recipient_link}',$nickassociazione,$stampa);
    $estrazionea="SELECT id_album,id_object FROM sys_albums_objects WHERE id_object=".$idfoto;
    $esecuzionea=mysql_query($estrazionea);
    $fetchassoca=mysql_fetch_assoc($esecuzionea);
    $generaurl=BX_DOL_URL_ROOT.'page/photoview?iff='.criptcodex($idfoto).'&ia='.criptcodex($fetchassoca['id_album']).'&ui='.criptcodex($fetchassoc['Owner']);
    $stampa=str_replace('{album_url}',$generaurl,$stampa);
    $stampa=str_replace('{album_caption}',$fetchassoc['Title'],$stampa);
    echo $stampa;
    echo '</div>';
    echo '<div id="bloccoav"><div id="anteprima" class="fadeMini'.$assegnazione.'">';
    $hash=$fetchassoc['Hash'];
    $exte=$fetchassoc['Ext']; 
    $titolofoto=$fetchassoc['Title'];
    $descrizion=$fetchassoc['Desc']; 
    $phsize=$fetchassoc['Size'];
    $descrizionecondividiw=$funclass->tagliaz($funclass->cleartesto($descrizion),300);
    if ($rowpriva[0]==2) echo '<a href="'.$generaurl.'"><img src="'.$imagepath.'unklock.png" class="unklockstyle2" alt="'._t("_ibdw_evowall_bx_access_denied").'"></a>';
    elseif ($idfoto==FALSE) echo '<a href="'.$generaurl.'"><img src="'.$imagepath.'unk.png" class="unklockstyle2"></a>';
    else 
    {
     //use the original size if lower
     $widthpic=substr($phsize,0,stripos($phsize,"x"));
     if ($widthpic<$widthphoto) $wused=$widthpic;
     else $wused=$widthphoto;
     if($photolarge=='')
	   {
	    if ($widthpic>200) echo '<a href="'.$generaurl.'"><img class="unklockstyle" onload="$(this).fadeIn(200);" src="m/photos/get_image/browse/'.$hash.'.'.$exte.'"></a><div class="lents" onclick="open_medium('.$assegnazione.','.$wused.');"><img src="'.$imagepath.'zoom.png" title="'._t("_ibdw_evowall_maximize").'"></div></div><div id="anteprima_medium" class="fadeMedium'.$assegnazione.'"><div id="ray_foto_zoom'.$assegnazione.'"></div><input type="hidden" id="value_foto_zoom'.$assegnazione.'" value="m/photos/get_image/file/'.$hash.'.'.$exte.'" /></div><div id="descrizione"><a href="'.$generaurl.'"><h3>'.$titolofoto.'</h3></a><p>'.$descrizion.'</p></div></div>';
 	    else echo '<a href="'.$generaurl.'"><img src="m/photos/get_image/browse/'.$hash.'.'.$exte.'" class="unklockstyle" onload="$(this).fadeIn(200);"></a></div><div id="descrizione"><a href="'.$generaurl.'"><h3>'.$titolofoto.'</h3></a> <p>'.$descrizion.'</p></div><div class="clear"></div></div>';
     }
     else echo '<a href="'.$generaurl.'"><img onload="$(this).fadeIn(200);" src="m/photos/get_image/file/'.$hash.'.'.$exte.'" width="'.$wused.'"></a></div><div id="descrizione"><a href="'.$generaurl.'"><h3>'.$titolofoto.'</h3></a><p>'.$descrizion.'</p></div></div>';
    } 
    if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
    else $commentsarea='justdate.php';
    include ($commentsarea);
    if($verifica_partent!=0)  echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div></div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
   }
   else $tmpx++;
  }
 }
 else $tmpx++; 
}
//END

//PHOTODELUXE ALBUM COMMENT
elseif($row['lang_key']=='bx_photo_deluxe_commentoalbum' and ($funclass->ActionVerify($profilemembership,"EVO WALL - Photos")))  
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if($row['sender_id']==$row['recipient_id']) $stampa=_t("_ibdw_evowall_comment_album");
  else $stampa=_t("bx_photo_deluxe_commentoalbummsg");
  echo $parteintroduttiva;
  echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
  if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
  {
   if($verifica_partent==0) 
   {
    echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
    include 'inc_eliminab.php';
    echo '</div>';  
    include 'bt_external_eliminab.php'; 
   }
  }
  $selezioneutenza="SELECT NickName FROM Profiles WHERE ID=".$row['sender_id'];
  $eseguiselezioneutenza=mysql_query($selezioneutenza);
  $assocselezioneutenza=mysql_fetch_assoc($eseguiselezioneutenza);
  $nickassociazione=$assocselezioneutenza['NickName'];
  $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat); 
  if($usernameformat=='Nickname') $stampa=str_replace('{profile_nick}',$nickassociazione,$stampa);
  else $stampa=str_replace('{profile_nick}',$realname,$stampa);
  $stampa=str_replace('{profile_link}',$nickassociazione,$stampa);
  $selezioneutenza="SELECT NickName FROM Profiles WHERE ID=".$row['recipient_id'];
  $eseguiselezioneutenza=mysql_query($selezioneutenza);
  $assocselezioneutenza=mysql_fetch_assoc($eseguiselezioneutenza);
  $nickassociazione=$assocselezioneutenza['NickName'];
  $realname=$funclass->TipoUtente($row['recipient_id'],$usernameformat);
  if($usernameformat=='Nickname') $stampa=str_replace('{recipient_nick}',$nickassociazione,$stampa);
  else $stampa=str_replace('{recipient_nick}',$realname,$stampa);
  $stampa=str_replace('{recipient_link}',$nickassociazione,$stampa);
  $idalbums=$unserialize['idalbum'];
  $titolo=$unserialize['Caption'];
  $commento=$unserialize['commento'];
  $estrazione="SELECT Owner FROM sys_albums WHERE ID=".$idalbums;
  $esecuzione=mysql_query($estrazione);
  $fetchassoc=mysql_fetch_assoc($esecuzione);
  $generaurl=BX_DOL_URL_ROOT.'page/photodeluxe?#ia='.criptcodex($idalbums).'&ui='.criptcodex($fetchassoc['Owner']);
  $stampa=str_replace('{album_url}',$generaurl,$stampa);
  $stampa=str_replace('{album_caption}',$titolo,$stampa);
  echo $stampa;
  $numero_caratteri=32;
  $stringa_in_input=$commento;
  if(strlen(trim($stringa_in_input))>$numero_caratteri) $testo=substr($stringa_in_input,0,strpos($stringa_in_input,' ',$numero_caratteri)).'...';
  else $testo=$stringa_in_input;
  echo '</div>';
  //chiude primariga
  echo '<div id="commentos"><p>'.$testo.'</p></div><div id="content_album_share">';
  $estrazione="SELECT sys_albums_objects.id_object,sys_albums.Caption,bx_photos_main.ID,bx_photos_main.Title,bx_photos_main.Hash,bx_photos_main.Ext FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album) INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID='$idalbums' ORDER BY ID DESC LIMIT 0,$evowallpreview";
  $esegui=mysql_query($estrazione);
  $numerazioneal=mysql_num_rows($esegui);
  if($numerazioneal!=0) {while($foto=mysql_fetch_array($esegui)) echo '<div onclick="location.href=\''.$generaurl.'\'" class="albumshareevowall" id="albumshareevowall" style="background-image: url(&quot;'.BX_DOL_URL_ROOT.'m/photos/get_image/browse/'.$foto['Hash'].'.'.$foto['Ext'].'&quot;);"></div>';}
  echo '</div>';
  if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
  else $commentsarea='justdate.php';
  include ($commentsarea);
  if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div></div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
 }
 else $tmpx++;  
}
//END

//PROFILE - PROFILO
elseif($row['lang_key']=='_bx_spy_profile_has_rated' OR $row['lang_key']=='_bx_spy_profile_has_edited' OR $row['lang_key']=='_bx_spy_profile_has_viewed' OR $row['lang_key']=='_bx_spy_profile_has_commented') 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_spy_profile_has_rated') $stampa=_t("_ibdw_evowall_profile_rate");
  elseif ($row['lang_key']=='_bx_spy_profile_has_viewed') $stampa=_t("_ibdw_evowall_spyprofile");
  elseif ($row['lang_key']=='_bx_spy_profile_has_commented') $stampa=_t("_ibdw_evowall_comment_add");
  elseif ($row['lang_key']=='_bx_spy_profile_has_edited') $stampa=_t("_ibdw_evowall_profile_edit");
  if($hideupdate=='' AND ($row['lang_key']=='_bx_spy_profile_has_edited' AND $unserialize['profile_nick']!=getNickName($row['sender_id']))) $tmpx++;
  else
  {
   echo $parteintroduttiva; 
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
	 include 'inc_eliminab.php';
     echo '</div>';
     include 'bt_external_eliminab.php'; 
    }
   }
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') $stampa=str_replace('{sender_p_nick}',$unserialize['sender_p_nick'],$stampa);
   else $stampa=str_replace('{sender_p_nick}',$realname,$stampa);
   $realname=$funclass->TipoUtente($row['recipient_id'],$usernameformat);
   if($usernameformat=='Nickname') $stampa=str_replace('{recipient_p_nick}',$unserialize['recipient_p_nick'],$stampa); 
   else $stampa=str_replace('{recipient_p_nick}',$realname,$stampa);
   $stampa=str_replace('{sender_p_link}',$unserialize['sender_p_link'],$stampa);
   $stampa=str_replace('{recipient_p_link}',$unserialize['recipient_p_link'],$stampa);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') $stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   echo $stampa;
   echo'</span></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea);
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
  }
 }
 else $tmpx++;
}
//END

//FRIEND ACCEPT
elseif ($row['lang_key']=='_bx_spy_profile_friend_accept')
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t("_ibdw_evowall_isfriend");
  echo $parteintroduttiva;
  echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
  if (($inviatore==$accountid OR $ricevitore==$profileid OR isAdmin() OR isModerator()) AND $accountid!=0) 
  {
   if($verifica_partent==0)
   {
    echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
    include 'inc_eliminab.php';   
    echo '</div>';
    include 'bt_external_eliminab.php'; 
   }
  }
  
  echo '<img class="addit" src="'.$imagepath.'user_add.png">';
  $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
  
  if ($usernameformat=='Nickname') $stampa=str_replace('{sender_p_nick}',$unserialize['sender_p_nick'],$stampa);
  else $stampa=str_replace('{recipient_p_nick}',$realname,$stampa);
  $stampa=str_replace('{sender_p_link}',$unserialize['sender_p_link'],$stampa);
  $realname=$funclass->TipoUtente($row['recipient_id'],$usernameformat);
  if($usernameformat=='Nickname') $stampa=str_replace('{recipient_p_nick}',$unserialize['recipient_p_nick'],$stampa);
  else $stampa=str_replace('{sender_p_nick}',$realname,$stampa);
  $stampa=str_replace('{recipient_p_link}',$unserialize['recipient_p_link'],$stampa);
  echo $stampa;  
  echo'</span></div>';
  if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
  else $commentsarea='justdate.php';
  include ($commentsarea);
  if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div></div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
 }
 else $tmpx++;
}			
//END

//SITE - SITO
elseif(($row['lang_key']=='_bx_sites_poll_add' OR $row['lang_key']=='_bx_sites_poll_rate' OR $row['lang_key']=='_bx_sites_poll_commentPost' OR $row['lang_key']=='_bx_sites_poll_change' OR $row['lang_key']=='_ibdw_evowall_bx_site_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Sites"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_sites_poll_add') $stampa=_t("_ibdw_evowall_site_add");
  elseif ($row['lang_key']=='_bx_sites_poll_rate') $stampa=_t("_ibdw_evowall_site_rate");
  elseif ($row['lang_key']=='_bx_sites_poll_commentPost') $stampa=_t("_ibdw_evowall_site_comment");
  elseif ($row['lang_key']=='_bx_sites_poll_change') $stampa=_t("_ibdw_evowall_site_edit");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_site_add_condivisione') $stampa=_t("_ibdw_evowall_bx_site_add_condivisione");
  $idswiaz=$row[id];
  $verificauri= explode ("/",$unserialize[site_url]);
  $namefoto=$unserialize['site_caption'];
  $queryrichiestasito="SELECT title,photo,ownerid,description,entryUri,id FROM bx_sites_main WHERE entryUri='$verificauri[3]'";
  $resultrichiestasito=mysql_query($queryrichiestasito);
  $rowrichiestasito=mysql_fetch_row($resultrichiestasito);
  $fotossito=$rowrichiestasito['1'];
  if ($fotossito<>"")
  {
   $querysito="SELECT ID,Title,Tags,Owner,Hash,Ext,bx_photos_main.Desc,status FROM bx_photos_main WHERE ID=".$fotossito;
   $resultsito=mysql_query($querysito);
   $rowsito=mysql_fetch_row($resultsito);
   $querypriva="SELECT AllowView FROM bx_sites_main WHERE id=".$rowrichiestasito['5'];
   $resultpriva=mysql_query($querypriva);
   $rowpriva=mysql_fetch_row($resultpriva);
   $okvista=$funclass->privstate($rowpriva[0],'bx_sites',$row['sender_id'],$accountid,$num_fave,'view');
   if($rowrichiestasito[0]==FALSE or $rowsito[7]=='pending') $tmpx++;
   elseif ($okvista==1)
   {
    $nomesito=$unserialize['site_caption'];
    $nomesito=$funclass->tagliaz($nomesito,80); 
    echo $parteintroduttiva;
    $descrizioner=$funclass->tagliaz($funclass->cleartesto($rowrichiestasito[3]),300);
    echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
    {
     if($verifica_partent==0)
 	 {
      echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
      if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';	 
 	  if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
   	  {
       /**Share System**/   
       include('bt_condivisione_1.php');
       $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
       $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
       $parametri_photo['site_url']=$unserialize['site_url'];
       $parametri_photo['site_caption']=$unserialize['site_caption'];
       $params_condivisione=serialize($parametri_photo);   
       $bt_condivisione_params['1']=$accountid; //Sender
       $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
       $bt_condivisione_params['3']='_ibdw_evowall_bx_site_add_condivisione'; //Lang_Key_share 
       $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
       include('bt_condivisione_2.php'); 
       /**End Share System**/	 
      }
      echo '</div>'; //div che chiude il bt_list di evo
      if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
      if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
     }
    }
    $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') $stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);
    else $stampa=str_replace('{profile_nick}',$realname,$stampa);
    $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
    $stampa=str_replace('{site_url}',$unserialize['site_url'],$stampa);
    $stampa=str_replace('{site_caption}',$nomesito,$stampa);          
    echo $stampa;
    echo '</div><div id="bloccoav">';
    if ($rowsito[4]!=FALSE) echo'<div id="anteprima"><a href="'.$unserialize['site_url'].'"><img src="m/photos/get_image/browse/'.$rowsito[4].'.'.$rowsito[5].'"></a></div>';
    echo '<div id="descrizione"><a href="'.$unserialize['site_url'].'"><h3>'.$unserialize['site_caption'].'</h3></a><p>'.$descrizioner.'</p></div></div>';
    if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
    else $commentsarea='justdate.php';
    include ($commentsarea);
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div></div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
 }
 else $tmpx++;
}
//END

//VIDEO
elseif (($row['lang_key']=='_bx_videos_spy_added' OR $row['lang_key']=='_bx_videos_spy_rated' OR $row['lang_key']=='_bx_videos_spy_comment_posted' OR $row['lang_key']=='_ibdw_evowall_bx_video_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Videos")))
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_videos_spy_added') $stampa=_t("_ibdw_evowall_video_add");
  elseif ($row['lang_key']=='_bx_videos_spy_rated') $stampa=_t("_ibdw_evowall_rated_videos");
  elseif ($row['lang_key']=='_bx_videos_spy_comment_posted') $stampa=_t("_ibdw_evowall_comment_name");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_video_add_condivisione') $stampa=_t("_ibdw_evowall_bx_video_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $queryvideo="SELECT ID,Title,Description,Owner,Source,Video,Uri,Status FROM RayVideoFiles WHERE Uri='$verificauri'";
  $resultvideo=mysql_query($queryvideo);
  $rowvideo=mysql_fetch_row($resultvideo);
  $nomevideo=$unserialize['entry_caption']; 
  if($rowvideo[1]==FALSE) {$tmpx++;$dlt="DELETE FROM `bx_spy_data` WHERE `id`=".$row['id']; $dlt_exe=mysql_query($dlt);}
  else 
  {
   $querypriva="SELECT AllowAlbumView FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE sys_albums_objects.id_object=".$rowvideo[0]." AND sys_albums.Type='bx_videos'";
   $resultpriva=mysql_query($querypriva);
   $rowpriva=mysql_fetch_row($resultpriva);
   $okvista=$funclass->privstate($rowpriva[0],'videos',$row['sender_id'],$accountid,$num_fave,'');
   if ($okvista==1)
   {
    if($rowvideo[7]!='approved') 
    {
     echo $parteintroduttiva.'<div id="messaggio" class="'.$avatartype.'"><div id="primariga">';
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)
	 {
	  if($verifica_partent==0)
	  {
	   echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
       include 'inc_eliminab.php';
       echo '</div>';
      }
     }
     $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
     echo '<a class="usernamestyle" href="'.$unserialize['profile_link'].'">';
     if($usernameformat=='Nickname') echo $unserialize['profile_nick'];
     if($usernameformat=='Full name') echo $realname;
     if($usernameformat=='FirstName') echo $realname;
     echo '</a> <span class="actionx">'._t("_ibdw_evowall_bx_video_under").' <b>'.$nomevideo.'</b> '._t("_ibdw_evowall_bx_video_undernotify").'</span></div>';
	 if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
     echo '</div></div>';
     if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
    }
    else 
    {
     $idswial=$row[id]; 
     echo $parteintroduttiva;
     echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
     if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
	 {
	  if($verifica_partent==0)
	  {
       echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
       if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';  
 	   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
       {
        /**Share System**/
        include('bt_condivisione_1.php');
        $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
        $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
        $parametri_photo['entry_url']=$unserialize['entry_url'];
        $parametri_photo['entry_caption']=$unserialize['entry_caption'];
        $params_condivisione=serialize($parametri_photo);   
        $bt_condivisione_params['1']=$accountid; //Sender
        $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
        $bt_condivisione_params['3']='_ibdw_evowall_bx_video_add_condivisione'; //Lang_Key_share 
        $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
        include('bt_condivisione_2.php'); 
        /**End Share System**/
       }
       echo '</div>'; //div che chiude il bt_list di evo
	   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
	   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
	  }
	 }
     $nomevideo=$unserialize['entry_caption'];
     $nomevideo=$funclass->tagliaz($nomevideo,80);
     $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
     if($usernameformat=='Nickname') $stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);
     else $stampa=str_replace('{profile_nick}',$realname,$stampa);
     $realname=$funclass->TipoUtente($row['recipient_id'],$usernameformat);
     if($usernameformat=='Nickname') $stampa=str_replace('{recipient_p_nick}',$unserialize['recipient_p_nick'],$stampa);
     else $stampa=str_replace('{recipient_p_nick}',$realname,$stampa);
     $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
     $stampa=str_replace('{video_url}',$unserialize['entry_url'],$stampa);
     $stampa=str_replace('{entry_caption}',$nomevideo,$stampa);
     $stampa=str_replace('{recipient_p_link}',$unserialize['recipient_p_link'],$stampa);
     $stampa=str_replace('{entry_url}',$unserialize[entry_url],$stampa);
     echo $stampa;
     echo '</div>
     <div id="bloccoav"><div id="anteprima">
 	 <script>
	  function fadeTub'.$idswial.'() {$("#tubeswich'.$idswial.'").fadeIn(); }
	  function fadeOutTub'.$idswial.'() 
	  {
	   $("#descrizione'.$idswial.' h3").css("float","left");
	   $("#descrizione'.$idswial.' h3").css("width","100%");
	   $("#descrizione'.$idswial.' p").css("float","left");
	  }
	  function scrollatube'.$idswial.'()
	  {
	   $("#imgtube'.$idswial.'").fadeOut(500);
	   $("#playbottone'.$idswial.'").fadeOut(500);
	   setTimeout("fadeTub'.$idswial.'()",1000);
       setTimeout("fadeOutTub'.$idswial.'()",2000);
	   ';
	   if ($rowvideo[4]=='') {echo '$("#bx-media'.$idswial.'").attr("autoplay", "autoplay");';}
	   echo '
      }
	 </script>
     <style> #tubeswich'.$idswial.'{display:none;}</style>';
     if ($rowvideo[4]=='youtube') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(http://i.ytimg.com/vi/'.$rowvideo[5].'/default.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div id="tubeswich'.$idswial.'" class="tubeswich"><object width="'.$widthvideo.'" height="'.$heightvideo.'" style="display: block;"><param name="movie" value="http://www.youtube.com/v/'.$rowvideo[5].'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$rowvideo[5].'&autoplay=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$widthvideo.'" height="'.$heightvideo.'" wmode="opaque"></embed></object></div>';
	 elseif ($rowvideo[4]=='') 
	 {
	  echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'">';
	  if ($playerused==0)
	  {
	   echo '<object height="'.$heightvideo.'" width="'.$widthvideo.'" style="display: block;"><param value="flash/modules/global/app/holder_as3.swf" name="movie"><param value="always" name="allowScriptAccess"><param value="true" name="allowFullScreen"><param value="flash/modules/video/" name="base"><param value="#FFFFFF" name="bgcolor"><param value="opaque" name="wmode"><param value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=video&amp;app=player&amp;id='.$rowvideo[0].'&amp;user=&amp;password=" name="flashVars"><embed height="'.$heightvideo.'" width="'.$widthvideo.'" flashvars="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=video&amp;app=player&amp;id='.$rowvideo[0].'&amp;user=&amp;password=" wmode="opaque" bgcolor="#FFFFFF" base="flash/modules/video/" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="flash/modules/global/app/holder_as3.swf"></object>';
      }
	  elseif ($playerused==1)
	  {
	   $percorsofile=BX_DIRECTORY_PATH_ROOT."/flash/modules/video/files/";
	   if (file_exists($percorsofile . $rowvideo[0] . '.webm'))
	   {
	    //USE HTML5 PLAYER
		$gettokenvideo="SELECT Token FROM RayVideoTokens WHERE ID=".$rowvideo[0]." LIMIT 0,1";
	    $gettokenfromid=mysql_query($gettokenvideo);
	    $resulttok=mysql_fetch_assoc($gettokenfromid);
	    if ($resulttok['Token']==NULL) 
	    { 
	     $iCurrentTime = time();
         $sToken = md5($iCurrentTime);
	     $creaquery="INSERT INTO RayVideoTokens (ID,Token,Date) VALUES('".$rowvideo[0]."','".$sToken."','".$iCurrentTime."')";
	 	 $creatoken=mysql_query($creaquery);
		 $gettokenvideo="SELECT Token FROM RayVideoTokens WHERE ID=".$rowvideo[0]." LIMIT 0,1";
	     $gettokenfromid=mysql_query($gettokenvideo);
	     $resulttok=mysql_fetch_assoc($gettokenfromid);
	    }
	    echo '<video id="bx-media'.$assegnazione.'" style="width:'.$widthvideo.'px;" autobuffer="" preload="auto" controls="" tabindex="0">
              <source src="'.BX_DOL_URL_ROOT.'flash/modules/video/get_file.php?id='.$rowvideo[0].'&amp;ext=webm&amp;token='.$resulttok['Token'].'" type="video/webm; codecs=&quot;vp8, vorbis&quot;"></source>
              <source src="'.BX_DOL_URL_ROOT.'flash/modules/video/get_file.php?id='.$rowvideo[0].'&amp;ext=m4v&amp;token='.$resulttok['Token'].'"></source>
              <script language="javascript" type="text/javascript">
        	  function reload() {location.href=\'/modules/index.php?r=videos/view/'.addslashes($rowvideo[1]).'&module=video&app=player\';}
    	 	  </script>
			  <div id="video_player">
			   <object width="'.$widthvideo.'" type="application/x-shockwave-flash" id="ray_flash_video_player_object" name="ray_flash_video_player_embed" style="display:block;" data="'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf"><param name="allowScriptAccess" value="always"><param name="allowFullScreen" value="true"><param name="base" value="'.BX_DOL_URL_ROOT.'flash/modules/video/"><param name="bgcolor" value="#FFFFFF"><param name="wmode" value="opaque"><param name="flashvars" value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=video&amp;app=player&amp;id=16&amp;user=&amp;password=">
			   </object>
			  </div>
			  <script language="javascript" type="text/javascript">
			   var flashvars={url:"'.BX_DOL_URL_ROOT.'flash/XML.php",module:"video",app:"player",id:"16",user:"",password:""};
			   var params={allowScriptAccess:"always",allowFullScreen:"true",base:"'.BX_DOL_URL_ROOT.'flash/modules/video/",bgcolor:"#FFFFFF",wmode:"opaque"};
			   var attributes = {id: "ray_flash_video_player_object",name: "ray_flash_video_player_embed",style: "display:block;"};
			   swfobject.embedSWF("'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf", "video_player_1359543421", "'.$widthvideo.'", "", "9.0.0", "'.BX_DOL_URL_ROOT.'flash/modules/global/app/expressInstall.swf", flashvars, params, attributes);
			  </script>
             </video>';
	   }
	   else
	   {
	    //USE BOONEX FLASH PLAYER
		echo '<object height="'.$heightvideo.'" width="'.$widthvideo.'" style="display: block;"><param value="flash/modules/global/app/holder_as3.swf" name="movie"><param value="always" name="allowScriptAccess"><param value="true" name="allowFullScreen"><param value="flash/modules/video/" name="base"><param value="#FFFFFF" name="bgcolor"><param value="opaque" name="wmode"><param value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=video&amp;app=player&amp;id='.$rowvideo[0].'&amp;user=&amp;password=" name="flashVars"><embed height="'.$heightvideo.'" width="'.$widthvideo.'" flashvars="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=video&amp;app=player&amp;id='.$rowvideo[0].'&amp;user=&amp;password=" wmode="opaque" bgcolor="#FFFFFF" base="flash/modules/video/" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="flash/modules/global/app/holder_as3.swf"></object>';
	   }
	  }
	  echo '</div>';
	 }
	 elseif ($rowvideo[4]=='bliptv') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" wmode="opaque" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="http://blip.tv/play/'.$rowvideo[5].'"></div>';
     elseif ($rowvideo[4]=='myspace') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'"><param value="true" name="allowFullScreen"><param value="opaque" name="wmode"><param value="http://mediaservices.myspace.com/services/media/embed.aspx/m='.$rowvideo[5].',t=1,mt=video,ap=true" name="movie"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" wmode="opaque" type="application/x-shockwave-flash" allowfullscreen="true" src="http://mediaservices.myspace.com/services/media/embed.aspx/m='.$rowvideo[5].',t=1,mt=video,ap=true"></object></div>';
     elseif ($rowvideo[4]=='slutload') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" data="http://emb.slutload.com/'.$rowvideo[5].'" type="application/x-shockwave-flash"><param value="always" name="AllowScriptAccess"><param value="http://emb.slutload.com/'.$rowvideo[5].'" name="movie"><param value="opaque" name="wmode"><param value="true" name="allowfullscreen"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" wmode="opaque" allowfullscreen="true" allowscriptaccess="always" src="http://emb.slutload.com/'.$rowvideo[5].'"></object></div>';
	 elseif ($rowvideo[4]=='gaywatch') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" data="http://www.gaywatch.com/mediaplayer.swf" type="application/x-shockwave-flash"><param value="http://www.gaywatch.com/mediaplayer.swf" name="movie"><param value="true" name="allowFullScreen"><param value="opaque" name="wmode"><param value="config=http://www.gaywatch.com/video_config.php?id='.$rowvideo[5].'&amp;autostart=true" name="flashvars"></object></div>';
     elseif ($rowvideo[4]=='movieclips') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" data="http://static.movieclips.com/embedplayer.swf?shortid='.$rowvideo[5].'" type="application/x-shockwave-flash" style="display:object;"><param value="http://static.movieclips.com/embedplayer.swf?shortid='.$rowvideo[5].'" name="movie"><param value="opaque" name="wmode"><param value="always" name="allowscriptaccess"><param value="true" name="allowfullscreen"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" allowfullscreen="true" allowscriptaccess="always" wmode="opaque" type="application/x-shockwave-flash" src="http://static.movieclips.com/embedplayer.swf?shortid='.$rowvideo[5].'"></object></div>';
	 elseif ($rowvideo[4]=='vimeo') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" data="http://vimeo.com/moogaloop.swf" type="application/x-shockwave-flash" style="display:object;"><param value="always" name="allowscriptaccess"><param value="true" name="allowfullscreen"><param value="http://vimeo.com/moogaloop.swf" name="movie"><param value="opaque" name="wmode"><param value="clip_id='.$rowvideo[5].'&amp;server=vimeo.com&amp;fullscreen=1&amp;show_title=0&amp;show_byline=1&amp;show_portrait=1&amp;color=00ADEF&amp;autoplay=0" name="flashvars"></object></div>';
	 elseif ($rowvideo[4]=='dailymotion') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" style="display:object;"><param value="http://www.dailymotion.com/swf/'.$rowvideo[5].'?autoPlay=0&amp;related=0" name="movie"><param value="opaque" name="wmode"><param value="true" name="allowFullScreen"><param value="always" name="allowScriptAccess"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" wmode="opaque" allowscriptaccess="always" allowfullscreen="true" type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/'.$rowvideo[5].'?autoPlay=0&amp;related=0"></object></div>'; 
	 elseif ($rowvideo[4]=='godtube') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" data="http://www.godtube.com/resource/mediaplayer/5.3/player.swf" type="application/x-shockwave-flash"><param value="http://www.godtube.com/resource/mediaplayer/5.3/player.swf" name="movie"><param value="true" name="allowfullscreen"><param value="always" name="allowscriptaccess"><param value="opaque" name="wmode"><param value="file=http://www.godtube.com/resource/mediaplayer/'.$rowvideo[5].'.file&amp;image=http://www.godtube.com/resource/mediaplayer/'.$rowvideo[5].'.jpg&amp;screencolor=000000&amp;type=video&amp;autostart=false&amp;playonce=true&amp;skin=http://www.godtube.com//resource/mediaplayer/skin/carbon/carbon.zip&amp;logo.file=http://media.salemwebnetwork.com/godtube/theme/default/media/embed-logo.png&amp;logo.link=http://www.godtube.com/watch/%3Fv%3D'.$rowvideo[5].'&amp;logo.position=top-left&amp;logo.hide=false&amp;controlbar.position=over" name="flashvars"></object></div>';
     elseif ($rowvideo[4]=='metacafe') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="opaque" src="http://www.metacafe.com/fplayer/'.$rowvideo[5].'/test.swf?playerVars=autoPlay=no"></div>';
     elseif ($rowvideo[4]=='redtube') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" style="display:object;"><param value="http://embed.redtube.com/player/" name="movie"><param value="id='.$rowvideo[5].'&amp;style=redtube&amp;autostart=false" name="FlashVars"><param value="true" name="allowFullScreen"><param value="opaque" name="wmode"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" wmode="opaque" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" flashvars="autostart=false" src="http://embed.redtube.com/player/?id='.$rowvideo[5].'&amp;style=redtube"></object></div>';
     elseif ($rowvideo[4]=='sextube') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" flashvars="autostart=false&amp;provider=http&amp;logo.hide=true&amp;config=http://www.sextube.com/flv_player/data/playerConfig/'.$rowvideo[5].'.xml" wmode="opaque" allowscriptaccess="always" allowfullscreen="true" bgcolor="000000" src="http://www.sextube.com/flv_player/skins/new/player.swf"></div>';
     elseif ($rowvideo[4]=='wattv') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" id="wat"><param value="http://www.wat.tv/swf2/'.$rowvideo[5].'" name="movie"><param value="true" name="allowFullScreen"><param value="always" name="allowScriptAccess"><param value="opaque" name="wmode"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" wmode="opaque" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="http://www.wat.tv/swf2/'.$rowvideo[5].'"></object></div>';
     elseif ($rowvideo[4]=='xtube') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" align="middle" id="slideshow" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param value="always" name="allowScriptAccess"><param value="opaque" name="wmode"><param value="true" name="allowFullScreen"><param value="swfURL=http://cdn1.publicvideo.xtube.com&amp;autoplay=0&amp;video_id='.$rowvideo[5].'&amp;en_flash_lib_path=http://cdn1.static.xtube.com/embed/library.swf" name="flashVars"><param value="http://cdn1.static.xtube.com/embed/scenes_player.swf?xv=1" name="movie"><param value="high" name="quality"><param value="#000000" name="bgcolor"><param value="true" name="allowFullScreen"><param value="http://www.xtube.com/play_re.php?v='.$rowvideo[5].'" name="targetUrl"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="opaque" allowscriptaccess="always" name="slideshow" allowfullscreen="true" bgcolor="#000000" quality="high" src="http://cdn1.static.xtube.com/embed/scenes_player.swf?xv=1" flashvars="swfURL=http://cdn1.publicvideo.xtube.com&amp;autoplay=0&amp;video_id='.$rowvideo[5].'&amp;en_flash_lib_path=http://cdn1.static.xtube.com/embed/library.swf"></object></div>';
     elseif ($rowvideo[4]=='xvideos') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param value="high" name="quality"><param value="#000000" name="bgcolor"><param value="always" name="allowScriptAccess"><param value="opaque" name="wmode"><param value="http://static.xvideos.com/swf/flv_player_site_v4.swf" name="movie"><param value="true" name="allowFullScreen"><param value="id_video='.$rowvideo[5].'" name="flashvars"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="opaque" flashvars="id_video='.$rowvideo[5].'" allowfullscreen="true" bgcolor="#000000" quality="high" menu="false" allowscriptaccess="always" src="http://static.xvideos.com/swf/flv_player_site_v4.swf"></object></div>';
     elseif ($rowvideo[4]=='xxxbunker') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="'.$widthvideo.'" height="'.$heightvideo.'" style="display:object;"><param value="http://xxxbunker.com/flash/player.swf" name="movie"><param value="opaque" name="wmode"><param value="true" name="allowfullscreen"><param value="always" name="allowscriptaccess"><param value="config=http://xxxbunker.com/playerConfig.php?videoid='.$rowvideo[5].'&amp;autoplay=false" name="flashvars"><embed width="'.$widthvideo.'" height="'.$heightvideo.'" flashvars="config=http://xxxbunker.com/playerConfig.php?videoid='.$rowvideo[5].'&amp;autoplay=false" wmode="opaque" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="http://xxxbunker.com/flash/player.swf"></object></div>';
	 elseif ($rowvideo[4]=='youku') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><embed src="http://player.youku.com/player.php/sid/'.$rowvideo[5].'/v.swf" allowFullScreen="true" quality="high" width="'.$widthvideo.'" height="'.$heightvideo.'" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed></div>';
	 elseif ($rowvideo[4]=='custom') 
	 {
	  echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo[0].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'">';
	  echo html_entity_decode(str_replace('width=&quot;640&quot; height=&quot;385&quot;','width="'.$widthvideo.'" height="'.$heightvideo.'"',$rowvideo[5]));
	  echo '</div>';
	 } 
     else $tmpx++;
     $descrizionee=$funclass->tagliaz($funclass->cleartesto($rowvideo[2]),300);
     echo '</div><div id="descrizione'.$idswial.'" class="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$rowvideo[1].'</h3></a><p>'.$descrizionee.'</p></div></div>';
     if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
     else $commentsarea='justdate.php';
     include ($commentsarea);
	 if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
     echo '</div></div>';
     if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
    }
   }
   else $tmpx++;
  }
 }
 else $tmpx++;
}
//END

// ADS
elseif (($row['lang_key']=='_bx_ads_added_spy' OR $row['lang_key']=='_bx_ads_rated_spy' OR $row['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Ads"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_ads_added_spy') $stampa=_t("_ibdw_evowall_ads_add");
  elseif ($row['lang_key']=='_bx_ads_rated_spy') $stampa=_t("_ibdw_evowall_ads_rate");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione') $stampa=_t("_ibdw_evowall_bx_ads_add_condivisione");
  $idswiah=$row[id];
  $titoloannuncioa=$unserialize['ads_caption'];
  $titoloannuncioa=$funclass->tagliaz($titoloannuncioa,80);
  $trovaslash=substr_count($unserialize['ads_url'],"/");
  $verificauri=explode ("/",$unserialize['ads_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryannuncio="SELECT * FROM bx_ads_main WHERE EntryUri='$verificauri'";
  $resultqueryannuncio=mysql_query($queryannuncio);
  $rowqueryannuncio=mysql_fetch_row($resultqueryannuncio);
  $numeroseriale=$rowqueryannuncio[11];
  $queryannunciofoto="SELECT MediaID,MediaFile FROM bx_ads_main_media WHERE MediaID='$numeroseriale'";
  $resultqueryannunciofoto=mysql_query($queryannunciofoto);
  $rowqueryannunciofoto=mysql_fetch_row($resultqueryannunciofoto);
  $descrizioneannuncio=$funclass->tagliaz($funclass->cleartesto($rowqueryannuncio[6]),150);
  $querypriva="SELECT AllowView FROM bx_ads_main WHERE id=".$rowqueryannuncio['0'];
  $resultpriva=mysql_query($querypriva);
  $rowpriva=mysql_fetch_row($resultpriva);
  $okvista=$funclass->privstate($rowpriva[0],'ads',$row['sender_id'],$accountid,$num_fave,'view');
  if($rowqueryannuncio[4]==FALSE or $rowqueryannuncio[7]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0))
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['recipient_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['recipient_p_nick']=$aInfomembers['NickName'];
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['ads_url']=$unserialize['ads_url'];
      $parametri_photo['ads_caption']=$unserialize['ads_caption'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_ads_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }       
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') $stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);   
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{ads_url}',$unserialize['ads_url'],$stampa);
   $titoloannuncios=$unserialize['ads_caption'];
   $titoloannuncios=$funclass->tagliaz($titoloannuncios,80);
   $stampa=str_replace('{ads_caption}',$titoloannuncios,$stampa);
   echo $stampa;
   echo '</div><div id="bloccoav">';
   if ($rowqueryannunciofoto[1]!=FALSE) echo '<div id="anteprima"><a href="'.$unserialize['ads_url'].'"><img src="media/images/classifieds/thumb_'.$rowqueryannunciofoto[1].'" class"dimfoto"></a></div>';
   echo '<div id="descrizione"><a href="'.$unserialize['ads_url'].'"><h3>'.$titoloannuncioa.'</h3></a> <p>'.$descrizioneannuncio.'</p>';
   if ($rowqueryannuncio[8]<>0) echo '<div id="adsdatacont"><strong>'._t("_adm_txt_mlevels_price").'</strong>: '._t("_ibdw_evowall_currency_price").number_format($rowqueryannuncio[8],2,",",".").'</div>';
   echo '</div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea);
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

// BLOGS
elseif (($row['lang_key']=='_bx_blog_added_spy' OR $row['lang_key']=='_bx_blog_rated_spy' OR $row['lang_key']=='_bx_blog_commented_spy' OR $row['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Blogs"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_blog_added_spy') {$stampa=_t("_ibdw_evowall_blogs_add");}
  elseif ($row['lang_key']=='_bx_blog_rated_spy') {$stampa=_t("_ibdw_evowall_blogs_rate");}
  elseif ($row['lang_key']=='_bx_blog_commented_spy') {$stampa=_t("_ibdw_evowall_blogs_comment");}
  elseif ($row['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione') {$stampa=_t("_ibdw_evowall_bx_blogs_add_condivisione");}
  $idswiah=$row[id];
  $blogtitle=$unserialize['post_caption'];
  $blogtitle=$funclass->tagliaz($blogtitle,80);
  $trovaslash=substr_count($unserialize['post_url'],"/");
  $verificauri=explode ("/",$unserialize['post_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryblog="SELECT * FROM bx_blogs_posts WHERE PostUri='$verificauri'";
  $resultqueryblog=mysql_query($queryblog);
  $rowqueryblog=mysql_fetch_row($resultqueryblog);
  $fotoblog=$rowqueryblog[6];
  $descrizioneblog=$rowqueryblog[3];
  $descrizioneblog=$funclass->tagliaz($funclass->cleartesto($descrizioneblog),150);
  $querypriva="SELECT AllowView FROM bx_blogs_posts WHERE PostID='".$rowqueryblog['0']."'";
  $resultpriva=mysql_query($querypriva);
  $rowpriva=mysql_fetch_row($resultpriva);
  $okvista=$funclass->privstate($rowpriva[0],'ads',$row['sender_id'],$accountid,$num_fave,'view');
  if ($okvista==1) 
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) {
   if($verifica_partent==0){
   echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'">
   <input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" />
            <a class="bt_open'.$assegnazione.'" id="bt_open">
              <img src="'.$imagepath.'fx_down.png">
            </a>
           </div>
           <div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
   
   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
   {
    /**Share System**/
    include('bt_condivisione_1.php');
    $parametri_photo['recipient_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
    $parametri_photo['recipient_p_nick']=$aInfomembers['NickName'];
    $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
    $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    $parametri_photo['post_url']=$unserialize['post_url'];
    $parametri_photo['post_caption']=$unserialize['post_caption'];
    $params_condivisione=serialize($parametri_photo);
    $bt_condivisione_params['1']=$accountid; //Sender
    $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
    $bt_condivisione_params['3']='_ibdw_evowall_bx_blogs_add_condivisione'; //Lang_Key_share 
    $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
    include('bt_condivisione_2.php');
    /**End Share System**/
   }       
    echo '</div>'; //div che chiude il bt_list di evo
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
    }
    $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}   
    else $stampa=str_replace('{profile_nick}',$realname,$stampa);
    $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
    $stampa=str_replace('{post_url}',$unserialize['post_url'],$stampa);
    $blogtitle=$unserialize['post_caption'];
    $blogtitle=$funclass->tagliaz($blogtitle,80);
    $stampa=str_replace('{post_caption}',$blogtitle,$stampa);
    echo $stampa;
    echo '</div><div id="bloccoav">';
    if ($fotoblog!=FALSE) {echo '<div id="anteprima"><a href="'.$unserialize['post_url'].'"><img src="media/images/blog/big_'.$fotoblog.'" class"dimfoto"></a></div>';}
    echo '<div id="descrizione"><a href="'.$unserialize['post_url'].'"><h3>'.$blogtitle.'</h3></a> <p>'.$descrizioneblog.'</p></div></div>';
    
	if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
    else $commentsarea='justdate.php';
    include ($commentsarea);
      
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div></div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
  else $tmpx++;
}
//END

// SOUND
elseif (($row['lang_key']=='_bx_sounds_spy_added' OR $row['lang_key']=='_bx_sounds_spy_comment_posted' OR $row['lang_key']=='_bx_sounds_spy_rated' OR $row['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Sounds")))
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_sounds_spy_added') {$stampa=_t("_ibdw_evowall_sounds_add");}
  elseif ($row['lang_key']=='_bx_sounds_spy_comment_posted') {$stampa=_t("_ibdw_evowall_sounds_comment");}
  elseif ($row['lang_key']=='_bx_sounds_spy_rated') {$stampa=_t("_ibdw_evowall_sounds_rate");}
  elseif ($row['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione') {$stampa=_t("_ibdw_evowall_bx_sounds_add_condivisione");}
  $idswiah=$row[id];
  $soundtitle=$unserialize['entry_caption'];
  $soundtitle=$funclass->tagliaz($soundtitle,80);
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $querysound="SELECT * FROM RayMp3Files WHERE Uri='$verificauri'";  
  $resultquerysound=mysql_query($querysound);
  $rowquerynum= mysql_num_rows($resultquerysound);
  if ($rowquerynum==0) $tmpx++;
  else
  {
  $rowquerysound=mysql_fetch_row($resultquerysound);
  $descrizionesound=$rowquerysound[5];
  $descrizionesound=$funclass->tagliaz($funclass->cleartesto($descrizionesound),150);
  $querypriva="SELECT AllowAlbumView FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE sys_albums_objects.id_object=".$rowquerysound['0']." AND sys_albums.Type='bx_sounds'";
  $resultpriva=mysql_query($querypriva);
  $rowpriva=mysql_fetch_row($resultpriva);
  $okvista=$funclass->privstate($rowpriva[0],'ads',$row['sender_id'],$accountid,$num_fave,'view');
  if ($okvista==1) 
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) {
   if($verifica_partent==0){
   echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'">
   <input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" />
            <a class="bt_open'.$assegnazione.'" id="bt_open">
              <img src="'.$imagepath.'fx_down.png">
            </a>
           </div>
           <div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
   
   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
   {
    /**Share System**/
    include('bt_condivisione_1.php');
    $parametri_photo['recipient_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
    $parametri_photo['recipient_p_nick']=$aInfomembers['NickName'];
    $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
    $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    $parametri_photo['entry_url']=$unserialize['entry_url'];
    $parametri_photo['entry_caption']=$unserialize['entry_caption'];
    $params_condivisione=serialize($parametri_photo);
    $bt_condivisione_params['1']=$accountid; //Sender
    $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
    $bt_condivisione_params['3']='_ibdw_evowall_bx_sounds_add_condivisione'; //Lang_Key_share 
    $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
    include('bt_condivisione_2.php');
    /**End Share System**/
   }       
    echo '</div>'; //div che chiude il bt_list di evo
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
    }
    $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}   
    else $stampa=str_replace('{profile_nick}',$realname,$stampa);
    $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
	$stampa=str_replace('{recipient_p_nick}',$unserialize['recipient_p_nick'],$stampa);
	$stampa=str_replace('{recipient_p_link}',$unserialize['recipient_p_link'],$stampa);
    $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
    $soundtitle=$unserialize['entry_caption'];
    $soundtitle=$funclass->tagliaz($soundtitle,80);
    $stampa=str_replace('{entry_caption}',$soundtitle,$stampa);
    echo $stampa;
    echo '</div><div id="bloccoav">
    <div id="anteprima">
    <div class="audio_play'.$assegnazione.'" id="player_audio" onclick="fadeAudioElement('.$assegnazione.');"><img src="'.$imagepath.'sound_preview.png"></div>
    <div class="object_player'.$assegnazione.'" id="object_audio">';
	
	if ($playerused==0)
	{
     echo '<object width="260" height="230" type="application/x-shockwave-flash" id="ray_flash_mp3_player_object" name="ray_flash_mp3_player_embed" style="display:block;" 
            data="'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf">
            <param name="allowScriptAccess" value="always">
            <param name="allowFullScreen" value="true">
            <param name="base" value="flash/modules/mp3/">
            <param name="bgcolor" value="#FFFFFF">
            <param name="wmode" value="opaque">
            <param name="flashvars" value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=mp3&amp;app=player&amp;id='.$rowquerysound[0].'&amp;user=&amp;password=">
     </object>';
	}
	elseif ($playerused==1)
	{
	 $percorsofile=BX_DIRECTORY_PATH_ROOT."/flash/modules/mp3/files/";
	 if (file_exists($percorsofile . $rowquerysound[0] . '.ogg'))
	 {
	  //USE HTML5 PLAYER
	  $gettokensound="SELECT Token FROM RayMp3Tokens WHERE ID=".$rowquerysound[0]." LIMIT 0,1";
	  $gettokenfromid=mysql_query($gettokensound);
	  $resulttok=mysql_fetch_assoc($gettokenfromid);
	  if ($resulttok['Token']==NULL) 
	  {
	   $iCurrentTime = time();
       $sToken = md5($iCurrentTime);
	   $creaquery="INSERT INTO RayMp3Tokens (ID,Token,Date) VALUES('".$rowquerysound[0]."','".$sToken."','".$iCurrentTime."')";
	   $creatoken=mysql_query($creaquery);
	   $gettokensound="SELECT Token FROM RayMp3Tokens WHERE ID=".$rowquerysound[0]." LIMIT 0,1";
	   $gettokenfromid=mysql_query($gettokensound);
	   $resulttok=mysql_fetch_assoc($gettokenfromid);
	  }
	  echo '<audio id="bx-media'.$assegnazione.'" style="width:260px;height:50px;" autobuffer="" preload="auto" controls="" tabindex="0">
            <source src="'.BX_DOL_URL_ROOT.'flash/modules/mp3/get_file.php?id='.$rowquerysound[0].'&amp;token='.$resulttok['Token'].'" type="audio/mpeg; codecs=&quot;mp3&quot;"></source>
            <source src="'.BX_DOL_URL_ROOT.'flash/modules/mp3/get_file.php?id='.$rowquerysound[0].'&amp;token='.$resulttok['Token'].'&amp;ext=ogg" type="audio/ogg; codecs=&quot;vorbis&quot;"></source>
            <script language="javascript" type="text/javascript">
	         function reload() {location.href=\'/modules/index.php?r=sounds/view/'.addslashes($soundtitle).'&module=mp3&app=player\';}
			</script>
			<div id="mp3_player">
			 <object height="350" width="100%" type="application/x-shockwave-flash" id="ray_flash_mp3_player_object" name="ray_flash_mp3_player_embed" style="display:block;" data="'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf">
			  <param name="allowScriptAccess" value="always">
			  <param name="allowFullScreen" value="true">
			  <param name="base" value="'.BX_DOL_URL_ROOT.'flash/modules/mp3/">
			  <param name="bgcolor" value="#FFFFFF">
			  <param name="wmode" value="opaque">
			  <param name="flashvars" value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=mp3&amp;app=player&amp;id='.$rowquerysound[0].'&amp;user=&amp;password=">
			 </object>
			</div>
			<script language="javascript" type="text/javascript">
			 var flashvars={url:"'.BX_DOL_URL_ROOT.'flash/XML.php",module:"mp3",app:"player",id:"3",user:"",password:""};
			 var params={allowScriptAccess:"always",allowFullScreen:"true",base:"'.BX_DOL_URL_ROOT.'flash/modules/mp3/",bgcolor:"#FFFFFF",wmode:"opaque"};
			 var attributes = {id: "ray_flash_mp3_player_object",name: "ray_flash_mp3_player_embed",style: "display:block;"};
			 swfobject.embedSWF("'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf", "mp3_player_1359487231", "100%", "350", "9.0.0", "'.BX_DOL_URL_ROOT.'flash/modules/global/app/expressInstall.swf", flashvars, params, attributes);
			</script>
           </audio>';
	 }
	 else
	 {
	  //USE BOONEX FLASH PLAYER
	  echo '<object width="260" height="230" type="application/x-shockwave-flash" id="ray_flash_mp3_player_object" name="ray_flash_mp3_player_embed" style="display:block;" 
            data="'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf">
            <param name="allowScriptAccess" value="always">
            <param name="allowFullScreen" value="true">
            <param name="base" value="flash/modules/mp3/">
            <param name="bgcolor" value="#FFFFFF">
            <param name="wmode" value="opaque">
            <param name="flashvars" value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=mp3&amp;app=player&amp;id='.$rowquerysound[0].'&amp;user=&amp;password=">
           </object>';
	 }
	}
	echo '</div></div>';
    echo '<div id="descrizione"><a href="'.$unserialize['entry_url'].'"><h3>'.$soundtitle.'</h3></a> <p>'.$descrizionesound.'</p></div></div>';
    
	if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
    else $commentsarea='justdate.php';
    include ($commentsarea);
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div></div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
 } 
 else $tmpx++;
}
//END


// MODZZZ PROPERTY REAL ESTATE
elseif ($row['lang_key']=='_modzzz_property_spy_post' OR $row['lang_key']=='_modzzz_property_spy_post_change' OR $row['lang_key']=='_modzzz_property_spy_join' OR $row['lang_key']=='_modzzz_property_spy_rate' OR $row['lang_key']=='_modzzz_property_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_property_share') 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t($row['lang_key']);
  $idswiah=$row[id];
  $propertytitle=$unserialize['entry_title'];
  $propertytitle=$funclass->tagliaz($propertytitle,80);
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryproperty="SELECT title,uri,modzzz_property_main.desc,price,allow_view_property_to,thumb,id,stutus FROM modzzz_property_main WHERE uri='$verificauri'";
  $resultqueryproperty= mysql_query($queryproperty);
  
  $nmproperties=mysql_num_rows($resultqueryproperty);
  if ($nmproperties>0)
  {
   $rowqueryproperty=mysql_fetch_assoc($resultqueryproperty);
   $descrizioneproperty=$rowqueryproperty['desc'];
   $getstatus=$rowqueryproperty['status'];
   if ($getstatus='approved')
   {
   $descrizioneproperty=$funclass->tagliaz($funclass->cleartesto($descrizioneproperty),150);
   //get pics of the property
   $getallpic="SELECT media_id FROM modzzz_property_images WHERE entry_id=".$rowqueryproperty['id'];
   $resultallpic=mysql_query($getallpic);
   $numberpics=mysql_num_rows($resultallpic);
   $iesima=0;
   while($extract=mysql_fetch_array($resultallpic))
   {
    $queryfotoproperty="SELECT Hash,Ext FROM bx_photos_main WHERE ID=".$extract['media_id'];
    $resultfotoproperty=mysql_query($queryfotoproperty);
    $rowfotoproperty=mysql_fetch_row($resultfotoproperty);
    //get name array of pics name (hash and extension)
    $fotoarray[$iesima]=$rowfotoproperty[0].".".$rowfotoproperty[1];
    $iesima++;
   }
   //get privacy
   $rowpriva=$rowqueryproperty['allow_view_property_to'];
   $okvista=$funclass->privstate($rowpriva,'property',$row['sender_id'],$accountid,$num_fave,'view');
   if ($okvista==1) 
   {
    echo $parteintroduttiva;
    echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
    {
     if($verifica_partent==0)
 	 {
      echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
      if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
      if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
      {
       /**Share System**/
       include('bt_condivisione_1.php');
       $parametri_photo['recipient_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
       $parametri_photo['recipient_p_nick']=$aInfomembers['NickName'];
       $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
       $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
       $parametri_photo['entry_url']=$unserialize['entry_url'];
       $parametri_photo['entry_title']=$unserialize['entry_title'];
       $params_condivisione=serialize($parametri_photo);
       $bt_condivisione_params['1']=$accountid; //Sender
       $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
       $bt_condivisione_params['3']='_ibdw_evowall_modzzz_property_share'; //Lang_Key_share 
       $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
       include('bt_condivisione_2.php');
       /**End Share System**/
      }       
      echo '</div>';
      if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
      if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
     }
    }
    $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') $stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);   
    else $stampa=str_replace('{profile_nick}',$realname,$stampa);
    $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
    $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
    $propertytitle=$unserialize['entry_title'];
    $propertytitle=$funclass->tagliaz($propertytitle,80);
    $stampa=str_replace('{entry_title}',$propertytitle,$stampa);
    echo $stampa;	
    echo '</div><div id="bloccoav">';
    if ($numberpics>0) 
    {
 	 if ($numberpics==1) echo '<div id="anteprima"><a href="'.$unserialize['entry_url'].'"><img src="m/photos/get_image/browse/'.$fotoarray[0].'"></a></div>';
     else
	 {
	  //set width based on pics number
	  $widhtsingle=(200/$numberpics)-2;
	  echo'<div id="anteprima"><div id="contpics">';
	  for ($nump=0;$nump<$numberpics;$nump++) echo '<a href="'.$unserialize['entry_url'].'"><img class="miniproperty" width="'.$widhtsingle.'%" src="m/photos/get_image/browse/'.$fotoarray[$nump].'"></a> ';
 	  echo '</div></div>';
	 }
    }
    echo '<div id="descrizione"><a href="'.$unserialize['entry_url'].'"><h3>'.$propertytitle.'</h3></a> <p>'.$descrizioneproperty.'</p><p class="currency">'._t("_modzzz_property_price").": $ ".number_format($rowqueryproperty['price']).'</p></div></div>';
    if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
    else $commentsarea='justdate.php';
    include ($commentsarea);
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div></div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
   }
   else $tmpx++;
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

// MODZZZ CLUB
elseif (($row['lang_key']=='_modzzz_club_spy_post' OR $row['lang_key']=='_modzzz_club_spy_post_change' OR $row['lang_key']=='_modzzz_club_spy_join' OR $row['lang_key']=='_modzzz_club_spy_rate' OR $row['lang_key']=='_modzzz_club_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_clubs_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//modzzz club inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_modzzz_club_spy_post') $stampa=_t("_ibdw_evowall_clubs_add");
  elseif ($row['lang_key']=='_modzzz_club_spy_post_change') $stampa=_t("_ibdw_evowall_clubs_editaw");
  elseif ($row['lang_key']=='_modzzz_club_spy_join') $stampa=_t("_ibdw_evowall_clubs_join");
  elseif ($row['lang_key']=='_modzzz_club_spy_rate') $stampa=_t("_ibdw_evowall_clubs_rate");
  elseif ($row['lang_key']=='_modzzz_club_spy_comment') $stampa=_t("_ibdw_evowall_clubs_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_clubs_add_condivisione') $stampa=_t("_ibdw_evowall_bx_clubs_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT title,thumb,modzzz_club_main.desc,uri,id,status,allow_view_club_to FROM modzzz_club_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo['6'],'clubs',$row['sender_id'],$accountid,$num_fave,'view_clubs'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo[1];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_clubs_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) {echo '<div id="anteprima"><a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'"></a></div>';}
   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END

// MODZZZ PETS
elseif (($row['lang_key']=='_modzzz_pets_spy_post' OR $row['lang_key']=='_modzzz_pets_spy_post_change' OR $row['lang_key']=='_modzzz_pets_spy_rate' OR $row['lang_key']=='_modzzz_pets_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_pets_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//modzzz pets inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_modzzz_pets_spy_post') $stampa=_t("_ibdw_evowall_pets_add");
  elseif ($row['lang_key']=='_modzzz_pets_spy_post_change') $stampa=_t("_ibdw_evowall_pets_editaw");
  elseif ($row['lang_key']=='_modzzz_pets_spy_rate') $stampa=_t("_ibdw_evowall_pets_rate");
  elseif ($row['lang_key']=='_modzzz_pets_spy_comment') $stampa=_t("_ibdw_evowall_pets_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_pets_add_condivisione') $stampa=_t("_ibdw_evowall_bx_pets_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT petname,thumb,modzzz_pets_main.desc,uri,id,status,allow_view_pet_to FROM modzzz_pets_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo['6'],'pets',$row['sender_id'],$accountid,$num_fave,'view_pets'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo[1];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_pets_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) {echo '<div id="anteprima"><a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'"></a></div>';}
   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END

// MODZZZ PETITIONS
elseif (($row['lang_key']=='_modzzz_petitions_spy_post' OR $row['lang_key']=='_modzzz_petitions_spy_post_change' OR $row['lang_key']=='_modzzz_petitions_spy_join' OR $row['lang_key']=='_modzzz_petitions_spy_rate' OR $row['lang_key']=='_modzzz_petitions_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_petitions_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//modzzz petitions inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_modzzz_petitions_spy_post') $stampa=_t("_ibdw_evowall_petitions_add");
  elseif ($row['lang_key']=='_modzzz_petitions_spy_post_change') $stampa=_t("_ibdw_evowall_petitions_editaw");
  elseif ($row['lang_key']=='_modzzz_petitions_spy_join') $stampa=_t("_ibdw_evowall_petitions_join");
  elseif ($row['lang_key']=='_modzzz_petitions_spy_rate') $stampa=_t("_ibdw_evowall_petitions_rate");
  elseif ($row['lang_key']=='_modzzz_petitions_spy_comment') $stampa=_t("_ibdw_evowall_petitions_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_petitions_add_condivisione') $stampa=_t("_ibdw_evowall_bx_petitions_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT title,thumb,modzzz_petitions_main.desc,uri,id,status,allow_view_petition_to FROM modzzz_petitions_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo['6'],'petitions',$row['sender_id'],$accountid,$num_fave,'view_petitions'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo[1];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_petitions_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) {echo '<div id="anteprima"><a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'"></a></div>';}
   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END

// MODZZZ BANDS
elseif (($row['lang_key']=='_modzzz_bands_spy_post' OR $row['lang_key']=='_modzzz_bands_spy_post_change' OR $row['lang_key']=='_modzzz_bands_spy_join' OR $row['lang_key']=='_modzzz_bands_spy_rate' OR $row['lang_key']=='_modzzz_bands_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_bands_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//modzzz bands inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_modzzz_bands_spy_post') $stampa=_t("_ibdw_evowall_bands_add");
  elseif ($row['lang_key']=='_modzzz_bands_spy_post_change') $stampa=_t("_ibdw_evowall_bands_editaw");
  elseif ($row['lang_key']=='_modzzz_bands_spy_join') $stampa=_t("_ibdw_evowall_bands_join");
  elseif ($row['lang_key']=='_modzzz_bands_spy_rate') $stampa=_t("_ibdw_evowall_bands_rate");
  elseif ($row['lang_key']=='_modzzz_bands_spy_comment') $stampa=_t("_ibdw_evowall_bands_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_bands_add_condivisione') $stampa=_t("_ibdw_evowall_bx_bands_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT title,thumb,modzzz_bands_main.desc,uri,id,status,allow_view_band_to FROM modzzz_bands_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo['6'],'bands',$row['sender_id'],$accountid,$num_fave,'view_bands'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo[1];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_bands_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) {echo '<div id="anteprima"><a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'"></a></div>';}
   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END


// MODZZZ SCHOOLS
elseif (($row['lang_key']=='_modzzz_schools_spy_post' OR $row['lang_key']=='_modzzz_schools_spy_post_change' OR $row['lang_key']=='_modzzz_schools_spy_join' OR $row['lang_key']=='_modzzz_schools_spy_rate' OR $row['lang_key']=='_modzzz_schools_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_schools_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//modzzz schools inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_modzzz_schools_spy_post') $stampa=_t("_ibdw_evowall_schools_add");
  elseif ($row['lang_key']=='_modzzz_schools_spy_post_change') $stampa=_t("_ibdw_evowall_schools_editaw");
  elseif ($row['lang_key']=='_modzzz_schools_spy_join') $stampa=_t("_ibdw_evowall_schools_join");
  elseif ($row['lang_key']=='_modzzz_schools_spy_rate') $stampa=_t("_ibdw_evowall_schools_rate");
  elseif ($row['lang_key']=='_modzzz_schools_spy_comment') $stampa=_t("_ibdw_evowall_schools_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_schools_add_condivisione') $stampa=_t("_ibdw_evowall_bx_schools_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT title,thumb,modzzz_schools_main.desc,uri,id,status,allow_view_school_to FROM modzzz_schools_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo['6'],'schools',$row['sender_id'],$accountid,$num_fave,'view_schools'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo[1];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_schools_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) {echo '<div id="anteprima"><a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'"></a></div>';}
   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END

// MODZZZ NOTICES
elseif (($row['lang_key']=='_modzzz_notices_spy_post' OR $row['lang_key']=='_modzzz_notices_spy_post_change' OR $row['lang_key']=='_modzzz_notices_spy_rate' OR $row['lang_key']=='_modzzz_notices_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_notices_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//modzzz notices inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_modzzz_notices_spy_post') $stampa=_t("_ibdw_evowall_notices_add");
  elseif ($row['lang_key']=='_modzzz_notices_spy_post_change') $stampa=_t("_ibdw_evowall_notices_editaw");
  elseif ($row['lang_key']=='_modzzz_notices_spy_rate') $stampa=_t("_ibdw_evowall_notices_rate");
  elseif ($row['lang_key']=='_modzzz_notices_spy_comment') $stampa=_t("_ibdw_evowall_notices_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_notices_add_condivisione') $stampa=_t("_ibdw_evowall_bx_notices_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT title,thumb,modzzz_notices_main.desc,uri,id,status,allow_view_notice_to FROM modzzz_notices_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo['6'],'notices',$row['sender_id'],$accountid,$num_fave,'view_notices'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_notices_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';

   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END


// MODZZZ CLASSIFIEDS
elseif (($row['lang_key']=='_modzzz_classified_spy_post' OR $row['lang_key']=='_modzzz_classified_spy_post_change' OR $row['lang_key']=='_modzzz_classified_spy_rate' OR $row['lang_key']=='_modzzz_classified_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//modzzz classified inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_modzzz_classified_spy_post') $stampa=_t("_ibdw_evowall_classified_add");
  elseif ($row['lang_key']=='_modzzz_classified_spy_post_change') $stampa=_t("_ibdw_evowall_classified_editaw");
  elseif ($row['lang_key']=='_modzzz_classified_spy_rate') $stampa=_t("_ibdw_evowall_classified_rate");
  elseif ($row['lang_key']=='_modzzz_classified_spy_comment') $stampa=_t("_ibdw_evowall_classified_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione') $stampa=_t("_ibdw_evowall_bx_classified_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT title,thumb,modzzz_classified_main.desc,uri,id,status,allow_view_classified_to FROM modzzz_classified_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo['6'],'classified',$row['sender_id'],$accountid,$num_fave,'view_classified'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo[1];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_classified_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) {echo '<div id="anteprima"><a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'"></a></div>';}
   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END


// MODZZZ NEWS
elseif (($row['lang_key']=='_modzzz_news_spy_post' OR $row['lang_key']=='_modzzz_news_spy_post_change' OR $row['lang_key']=='_modzzz_news_spy_rate' OR $row['lang_key']=='_modzzz_news_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_news_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//modzzz news inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_modzzz_news_spy_post') $stampa=_t("_ibdw_evowall_news_add");
  elseif ($row['lang_key']=='_modzzz_news_spy_post_change') $stampa=_t("_ibdw_evowall_news_editaw");
  elseif ($row['lang_key']=='_modzzz_news_spy_rate') $stampa=_t("_ibdw_evowall_news_rate");
  elseif ($row['lang_key']=='_modzzz_news_spy_comment') $stampa=_t("_ibdw_evowall_news_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_news_add_condivisione') $stampa=_t("_ibdw_evowall_bx_news_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT title,thumb,modzzz_news_main.desc,uri,id,status,allow_view_news_to FROM modzzz_news_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo['6'],'news',$row['sender_id'],$accountid,$num_fave,'view_news'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo[1];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_news_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) {echo '<div id="anteprima"><a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'"></a></div>';}
   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END


// MODZZZ JOBS
elseif (($row['lang_key']=='_modzzz_jobs_spy_join' OR $row['lang_key']=='_modzzz_jobs_spy_post' OR $row['lang_key']=='_modzzz_jobs_spy_post_change' OR $row['lang_key']=='_modzzz_jobs_spy_rate' OR $row['lang_key']=='_modzzz_jobs_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_jobs_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//modzzz news inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_modzzz_jobs_spy_join') $stampa=_t("_ibdw_evowall_jobs_join");
  elseif ($row['lang_key']=='_modzzz_jobs_spy_post') $stampa=_t("_ibdw_evowall_jobs_add");
  elseif ($row['lang_key']=='_modzzz_jobs_spy_post_change') $stampa=_t("_ibdw_evowall_jobs_editaw");
  elseif ($row['lang_key']=='_modzzz_jobs_spy_rate') $stampa=_t("_ibdw_evowall_jobs_rate");
  elseif ($row['lang_key']=='_modzzz_jobs_spy_comment') $stampa=_t("_ibdw_evowall_jobs_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_jobs_add_condivisione') $stampa=_t("_ibdw_evowall_bx_jobs_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT title,thumb,modzzz_jobs_main.desc,uri,id,status,allow_view_job_to FROM modzzz_jobs_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo['6'],'jobs',$row['sender_id'],$accountid,$num_fave,'view_jobs'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo[1];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_jobs_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) {echo '<div id="anteprima"><a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'"></a></div>';}
   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END

// MODZZZ LISTINGS
elseif (($row['lang_key']==_modzzz_listing_spy_join OR $row['lang_key']=='_modzzz_listing_spy_post' OR $row['lang_key']=='_modzzz_listing_spy_post_change' OR $row['lang_key']=='_modzzz_listing_spy_rate' OR $row['lang_key']=='_modzzz_listing_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//modzzz news inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']==_modzzz_listing_spy_join) $stampa=_t("_ibdw_evowall_listing_join");
  elseif ($row['lang_key']=='_modzzz_listing_spy_post') $stampa=_t("_ibdw_evowall_listing_add");
  elseif ($row['lang_key']=='_modzzz_listing_spy_post_change') $stampa=_t("_ibdw_evowall_listing_editaw");
  elseif ($row['lang_key']=='_modzzz_listing_spy_rate') $stampa=_t("_ibdw_evowall_listing_rate");
  elseif ($row['lang_key']=='_modzzz_listing_spy_comment') $stampa=_t("_ibdw_evowall_listing_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione') $stampa=_t("_ibdw_evowall_bx_listing_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT title,thumb,modzzz_listing_main.desc,uri,id,status,allow_view_listing_to FROM modzzz_listing_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo['6'],'listing',$row['sender_id'],$accountid,$num_fave,'view_listing'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo[1];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_listing_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) {echo '<div id="anteprima"><a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'"></a></div>';}
   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END


// PHOTO - FOTO
elseif (($row['lang_key']=='_bx_photos_spy_added' OR $row['lang_key']=='_bx_photos_spy_comment_posted' OR $row['lang_key']=='_bx_photos_spy_rated' OR $row['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione' OR $row['lang_key']=='_bx_photo_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Photos"))) 
{               
 if (($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview')) OR $off_parent==1)
 {
  if ($row['lang_key']=='_bx_photos_spy_added') {if($verifica_partent==0){$stampa=_t("_ibdw_evowall_photo_add"); } else {$stampa=_t("_ibdw_evowall_parent_photo"); }}
  elseif ($row['lang_key']=='_bx_photos_spy_comment_posted') {if($verifica_partent==0){$stampa=_t("_ibdw_evowall_comment_nphoto");} else {$stampa=_t("_ibdw_evowall_comment_nphoto_multi");}}
  elseif ($row['lang_key']=='_bx_photos_spy_rated') {if($verifica_partent==0){$stampa=_t("_ibdw_evowall_rate_photo");} else {$stampa=_t("_ibdw_evowall_rate_photo_multi"); }}
  elseif ($row['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione' OR $row['lang_key']=='_bx_photo_add_condivisione') {$stampa=_t("_ibdw_evowall_bx_photo_add_condivisione");}
  $nomeimg=$unserialize['entry_caption'];
  $nomeimg=$funclass->tagliaz($nomeimg,80);
  $urlimg=$unserialize[entry_url]; 
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $queryfoto="SELECT * FROM bx_photos_main WHERE Uri='".$verificauri."'";                                                                      
  $resultfoto=mysql_query($queryfoto);
  $rowfoto=mysql_fetch_row($resultfoto); 
  $indirizzourl_true=BX_DOL_URL_ROOT.'m/photos/view/'.$rowfoto[6];
   
  //ottengo l'uri dell'album
  $queryhidden="SELECT Uri FROM sys_albums WHERE ID IN (SELECT id_album FROM sys_albums_objects WHERE id_object=".$rowfoto[0].")";
  $resultqueryh=mysql_query($queryhidden);
  $checkvaluealbum=mysql_fetch_row($resultqueryh); 
  
  //ottengo il nome predefinito per l'album hidden
  $getdefault="SELECT `VALUE` FROM `sys_options` WHERE `Name`='sys_album_default_name'";
  $resultqueryd=mysql_query($getdefault);
  $checkdefault=mysql_fetch_row($resultqueryd);
  
  //controllo quindi se l'uri è hidden,in tal caso scarto la notizia della foto
  if ($checkvaluealbum[0]==$checkdefault[0] or ($rowfoto[15]=='pending')) $tmpx++;
  else 
  {
   if($rowfoto[4]==FALSE) {$dlt="DELETE FROM `bx_spy_data` WHERE `id`=".$row['id']; $dlt_exe=mysql_query($dlt); $tmpx++;}
   else 
   {
    $idswiak=$row[id];
    $querypriva="SELECT AllowAlbumView FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object='$rowfoto[0]' AND TYPE='bx_photos'";
    $resultpriva=mysql_query($querypriva);
    $rowpriva=mysql_fetch_row($resultpriva);
	
    if($attivaintegrazione==1) 
  	{
     $pdxrecuperofoto="SELECT ID,Owner FROM bx_photos_main WHERE Uri='$verificauri'";
     $pdxeseguirecuperofoto=mysql_query($pdxrecuperofoto);
     $pdxrowrecuperfoto=mysql_fetch_assoc($pdxeseguirecuperofoto);
     $pdxidfoto=$pdxrowrecuperfoto['ID'];
     $pdxuserid=$pdxrowrecuperfoto['Owner'];
     $pdxrecuperoalbum="SELECT id_album FROM sys_albums_objects WHERE id_object='$pdxidfoto'";
     $pdxeseguirecuperoalbum=mysql_query($pdxrecuperoalbum);
     $pdxrowrecuperoalbum=mysql_fetch_assoc($pdxeseguirecuperoalbum); 
     $pdxidalbums=$pdxrowrecuperoalbum['id_album'];  
      
     //controllo campi vuoti
     $selezionedlxvuoti="SELECT id_notizia FROM ibdw_likethis WHERE	id_notizia='$assegnazione' AND phdlxid='0'";
     $eseguiselezionedlxvuoti=mysql_query($selezionedlxvuoti);
     $numdlxvuoti=mysql_num_rows($eseguiselezionedlxvuoti);
   
     if($numdlxvuoti!=0) 
	 {
      $updatelike="UPDATE ibdw_likethis SET phdlxid='".$pdxrowrecuperfoto['ID']."',typelement='photo' WHERE id_notizia='$assegnazione' AND typelement !='phunsign'";
      $eseguiupdatelike=mysql_query($updatelike);
     }  
    }
    $okvista=$funclass->privstate($rowpriva[0],'photos',$row['sender_id'],$accountid,$num_fave,'');
	
    if ($okvista==1)
    {
     echo $parteintroduttiva;
     echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
     
	 
	 if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
	 {
      if($verifica_partent==0)
	  {
       echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'">
             <input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" />
             <a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a>
             </div>
             <div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
      if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     
	  if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
      {
       /**SHARING BLOCK**/
       include('bt_condivisione_1.php');
       $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
       $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
       $parametri_photo['entry_url']=$indirizzourl_true;
       $parametri_photo['entry_caption']=$nomeimg;
       $params_condivisione=serialize($parametri_photo);   
       $bt_condivisione_params['1']=$accountid; //Sender
       if($row['lang_key']=='_bx_photo_add_condivisione') 
	   {
	    $bt_condivisione_params['2']=0; //Recipient
	   }
       else {$bt_condivisione_params['2']=$row['sender_id'];}  
       $bt_condivisione_params['3']='_ibdw_evowall_bx_photo_add_condivisione'; //Lang_Key_share
       $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
       include('bt_condivisione_2.php');
       /**END SHARING BLOCK**/
	  }
	  echo '</div>'; //div che chiude il bt_list di evo
	  if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
	  if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     }   
    }
	$realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
    else $stampa=str_replace('{profile_nick}',$realname,$stampa);  
    $realname=$funclass->TipoUtente($row['recipient_id'],$usernameformat);
    if($usernameformat=='Nickname') {$stampa=str_replace('{recipient_p_nick}',$unserialize['recipient_p_nick'],$stampa);}  
    else {$stampa=str_replace('{recipient_p_nick}',$realname,$stampa);}       
    $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
    $stampa=str_replace('{recipient_p_link}',$unserialize['recipient_p_link'],$stampa);
    
	if($attivaintegrazione==0) {$stampa=str_replace('{entry_url}',$urlimg,$stampa);} 
	else {$indirizzopdx=BX_DOL_URL_ROOT.'page/photoview?iff='.criptcodex($pdxidfoto).'&ia='.criptcodex($pdxidalbums).'&ui='.criptcodex($pdxuserid); $stampa=str_replace('{entry_url}',$indirizzopdx,$stampa);}
    $stampa=str_replace('{entry_url}',$urlimg,$stampa);
    $stampa=str_replace('{entry_caption}',$nomeimg,$stampa);
    $stampa=str_replace('{number}',$verifica_partent+1,$stampa);
    echo $stampa;
	if($photoautozoom=='on')
	{
     $script_js='<script>
     $(document).ready(function()
	 {
      var config={over: fade_evp_pop'.$assegnazione.',out: out_evp_pop'.$assegnazione.',interval:150};
      $("#pop_evophoto'.$assegnazione.'").hoverIntent(config);
      $("#pop_evophoto'.$assegnazione.' img").mouseover(function() {$(this).css("opacity","0.5");});
      $("#pop_evophoto'.$assegnazione.' img").mouseout(function() {$(this).css("opacity","1");});
     });
     function fade_evp_pop'.$assegnazione.'()
	 {
      $(".pop_foto").css("background-image","url(modules/ibdw/evowall/templates/uni/css/immagini/big-loader.gif)"); 
      var set_value_photozoom=$("#set_value_photozoom'.$assegnazione.'").val();
      if(set_value_photozoom!='.$rowfoto[0].') 
	  {
	   $("#popup'.$assegnazione.'").html("<img src=\"m/photos/get_image/file/'.$rowfoto[16].'.'.$rowfoto[3].'\">");
       var larg=$("#bloccoav").width(); 
       var new_larg=larg-20;
       var new_largs=new_larg+"px";
       $("#popup'.$assegnazione.'").css("max-width",new_largs);
       $("#popup'.$assegnazione.'").fadeIn(1); 
       $("#popup'.$assegnazione.' img").fadeIn(1000); 
       $("#set_value_photozoom'.$assegnazione.'").val('.$rowfoto[0].');
       $(".pop_foto").css("background-image","none");
      }
     }
     function out_evp_pop'.$assegnazione.'(){$("#pop_evophoto'.$assegnazione.' img").css("opacity","1");} 
  </script>';}
  else {$script_js='';}         
  
  
  
  if($verifica_partent>1){$num_foto=$verifica_partent+1;} 
  echo '</div><div id="bloccoav">';
  if($verifica_partent>1)
  {
   if($num_foto>$photo_max_preview) {$div_foto=$photo_max_preview;} 
   else {$div_foto=$num_foto;}
   echo '<script>
          $(document).ready(function() 
		  {
           var larg=$("#bloccoav").width();
           var width_foto=larg/'.$div_foto.';
		   var width_foto=width_foto-6;
           $(".fadeMini'.$assegnazione.' img").css("width",width_foto);
           var height=width_foto/1.5;
           $(".fadeMini'.$assegnazione.'").css("height",height);
           $("#messaggio").css("height","100%");
           $(".pop_foto img").css("width","100%");
      	  });
         </script> ';
  }
  
  echo '<div id="anteprima" class="fadeMini'.$assegnazione.'">';
  $descrizioneq=$funclass->tagliaz($funclass->cleartesto($rowfoto[7]),300);

  if($attivaintegrazione==0) 
  {
   //get image size checking the limit from the settings
   $widthpic=substr($rowfoto[4],0,stripos($rowfoto[4],"x"));
   if ($widthpic<$widthphoto) {$wused=$widthpic;}
   else {$wused=$widthphoto;}
   if($photolarge=='')
   {
	if($verifica_partent==0) {echo '<a href="'.$urlimg.'"><img src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'"></a>';}
    else 
	{
	 echo $script_js.'<a id="pop_evophoto'.$assegnazione.'" class="marginfix" href="'.$urlimg.'"><img src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'"></a>';
     echo estrai_foto_parent($row['id'],$row['lang_key'],$row['sender_id'],$row['recordsfound'],$photo_max_preview,$attivaintegrazione,$photoautozoom,$row['date']);
	} 
    if($verifica_partent==0){echo '<div class="lents" onclick="open_medium('.$assegnazione.','.$wused.');"><img src="'.$imagepath.'zoom.png" title="'._t("_ibdw_evowall_maximize").'"></div>';}
    echo '</div>';
    if($verifica_partent==0)
	{
	 echo '<div id="anteprima_medium" class="fadeMedium'.$assegnazione.'"><div id="ray_foto_zoom'.$assegnazione.'"></div><input type="hidden" id="value_foto_zoom'.$assegnazione.'" value="m/photos/get_image/file/'.$rowfoto[16].'.'.$rowfoto[3].'" /></div>
           <div id="descrizione"><a href="'.$urlimg.'"><h3>'.$rowfoto[5].'</h3></a><p>'.$descrizioneq.'</p></div>';} echo '<div class="clear"></div></div>';
	}
	else
	{
	 if ($widthpic>200) {if($verifica_partent==0){echo '<a href="'.$urlimg.'"><img src="m/photos/get_image/file/'.$rowfoto[16].'.'.$rowfoto[3].'" onload="$(this).fadeIn(200);" width="'.$wused.'"></a>';}
     else 
	 {
      echo $script_js.'<a id="pop_evophoto'.$assegnazione.'" class="marginfix" href="'.$urlimg.'"><img src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'" class="unklockstyle" onload="$(this).fadeIn(200);"></a>'; 
      echo estrai_foto_parent($row['id'],$row['lang_key'],$row['sender_id'],$row['recordsfound'],$photo_max_preview,$attivaintegrazione,$photoautozoom,$row['date']);
     }
     echo '</div>';
     if($verifica_partent==0) {echo '<div id="descrizione"><a href="'.$urlimg.'"><h3>'.$rowfoto[5].'</h3></a> <p>'.$descrizioneq.'</p></div>';} 
     echo '<div class="clear"></div></div>';
    }
	    else
	    {
	     if($verifica_partent==0) {echo '<a href="'.$urlimg.'"><img src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'" class="unklockstyle" onload="$(this).fadeIn(200);" width="'.$wused.'"></a>';}
         else 
		 {
       	  echo $script_js.'<a id="pop_evophoto'.$assegnazione.'" class="marginfix" href="'.$urlimg.'"><img src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'" class="unklockstyle" onload="$(this).fadeIn(200);"></a>';
		  echo estrai_foto_parent($row['id'],$row['lang_key'],$row['sender_id'],$row['recordsfound'],$photo_max_preview,$attivaintegrazione,$photoautozoom,$row['date']);
		 }
       
       echo'</div>';
       if($verifica_partent==0){echo '<div id="descrizione"><a href="'.$urlimg.'"><h3>'.$rowfoto[5].'</h3></a> <p>'.$descrizioneq.'</p></div>';} echo '<div class="clear"></div></div>';
	    }
	   }
	  }
	  
      else 
	  {
	   //ottengo la larghezza della foto ingrandita,se questa è minore della larhezza che abbiamo impostato in amministrazione allora usa la larghezza naturale per evitare di sgranarla
	   $widthpic=substr($rowfoto[4],0,stripos($rowfoto[4],"x"));
	   if ($widthpic<$widthphoto) {$wused=$widthpic;}
	   else {$wused=$widthphoto;}

	   if($photolarge=='')
	   {
	    if ($widthpic>200)
		{
       if($verifica_partent==0){
       echo '<a href="'.$indirizzopdx.'">
              <img class="unklockstyle" onload="$(this).fadeIn(200);" src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'">
             </a>';
       }
       else {
        echo $script_js.'<a href="'.$indirizzopdx.'" id="pop_evophoto'.$assegnazione.'" class="marginfix">
              <img class="unklockstyle" onload="$(this).fadeIn(200);" src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'">
             </a>';
       echo estrai_foto_parent($row['id'],$row['lang_key'],$row['sender_id'],$row['recordsfound'],$photo_max_preview,$attivaintegrazione,$photoautozoom,$row['date']);
       
       }
             if($verifica_partent==0){
             echo '
             <div class="lents" onclick="open_medium('.$assegnazione.','.$wused.');">
                <img src="'.$imagepath.'zoom.png" title="'._t("_ibdw_evowall_maximize").'">
             </div>';}
       echo '</div>';
       if($verifica_partent==0){
       echo '
               <div id="anteprima_medium" class="fadeMedium'.$assegnazione.'">';
	               echo '<div id="ray_foto_zoom'.$assegnazione.'"></div>
               <input type="hidden" id="value_foto_zoom'.$assegnazione.'" value="m/photos/get_image/file/'.$rowfoto[16].'.'.$rowfoto[3].'" />
               </div><div id="descrizione"><a href="'.$indirizzopdx.'"><h3>'.$rowfoto[5].'</h3></a><p>'.$descrizioneq.'</p></div>';} echo '</div>';
		}
		else
		{
		 if($verifica_partent==0){
		 echo '<a href="'.$indirizzopdx.'">
     <img src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'" class="unklockstyle" onload="$(this).fadeIn(200);"></a>
     '; }
     else {
     echo $script_js.'<a href="'.$indirizzopdx.'" id="pop_evophoto'.$assegnazione.'" class="marginfix">
     <img src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'" class="unklockstyle" onload="$(this).fadeIn(200);"></a>';
     echo estrai_foto_parent($row['id'],$row['lang_key'],$row['sender_id'],$row['recordsfound'],$photo_max_preview,$attivaintegrazione,$photoautozoom,$row['date']);}
     
     echo'</div>';if($verifica_partent==0){echo '<div id="descrizione"><a href="'.$indirizzopdx.'"><h3>'.$rowfoto[5].'</h3></a> <p>'.$descrizioneq.'</p></div>';} echo '<div class="clear"></div></div>';
		}
       }
	   else
	   {
	    if($verifica_partent==0){
	    echo '<a href="'.$indirizzopdx.'">
            <img onload="$(this).fadeIn(200);" src="m/photos/get_image/file/'.$rowfoto[16].'.'.$rowfoto[3].'" width="'.$wused.'">
           </a>';
      }
      else {
       echo $script_js.'<a href="'.$indirizzopdx.'" id="pop_evophoto'.$assegnazione.'" class="marginfix">
            <img class="unklockstyle" onload="$(this).fadeIn(200);" src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'">
           </a>';
      echo estrai_foto_parent($row['id'],$row['lang_key'],$row['sender_id'],$row['recordsfound'],$photo_max_preview,$attivaintegrazione,$photoautozoom,$row['date']);}
      
      echo'</div>';if($verifica_partent==0){echo '<div id="descrizione"><a href="'.$indirizzopdx.'"><h3>'.$rowfoto[5].'</h3></a><p>'.$descrizioneq.'</p></div>';} echo '<div class="clear"></div></div>';
		
	   }
      }
	  echo '<input type="hidden" id="set_value_photozoom'.$assegnazione.'" value="0"/><div id="popup'.$assegnazione.'" class="pop_foto"></div>';	  
	  if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';


	  
	  
      else $commentsarea='justdate.php';
      include ($commentsarea);
	  
	  
      $pdxidfoto=0; 
      if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
      echo '</div></div>';
      if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
     }
     elseif ($okvista=='no') $tmpx++;  
   }
  }
 }
 else $tmpx++;
}     
//END

// GROUPS - GRUPPO
elseif (($row['lang_key']=='_bx_groups_spy_post' OR $row['lang_key']=='_bx_groups_spy_post_change' OR $row['lang_key']=='_bx_groups_spy_join' OR $row['lang_key']=='_bx_groups_spy_rate' OR $row['lang_key']=='_bx_groups_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_groups_spy_post') $stampa=_t("_ibdw_evowall_group_add");
  elseif ($row['lang_key']=='_bx_groups_spy_post_change') $stampa=_t("_ibdw_evowall_group_editaw");
  elseif ($row['lang_key']=='_bx_groups_spy_join') $stampa=_t("_ibdw_evowall_group_join");
  elseif ($row['lang_key']=='_bx_groups_spy_rate') $stampa=_t("_ibdw_evowall_group_rate");
  elseif ($row['lang_key']=='_bx_groups_spy_comment') $stampa=_t("_ibdw_evowall_group_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione') $stampa=_t("_ibdw_evowall_bx_gruppo_add_condivisione");
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT title,thumb,bx_groups_main.desc,uri,id,status,allow_view_group_to FROM bx_groups_main WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_row($resultgruppo);
  $okvista=$funclass->privstate($rowgruppo[6],'groups',$row['sender_id'],$accountid,$num_fave,'view_group'); 
  if($rowgruppo[0]==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowgruppo[5]=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo[1];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row[id];
   echo $parteintroduttiva;
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo[2]),300);
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_title']=$unserialize['entry_title'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_gruppo_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
     echo '</div>'; //div che chiude il bt_list di evo
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
   }
   $nomegruppo=$unserialize['entry_title'];
   $nomegruppo=$funclass->tagliaz($nomegruppo,80);
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   if($usernameformat=='Nickname') $stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);
   else $stampa=str_replace('{profile_nick}',$realname,$stampa);
   $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) echo '<div id="anteprima"><a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'"></a></div>';
   echo '<div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$unserialize['entry_title'].'</h3></a><p>'.$descrizionet.'</p></div></div>';
   if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
   else $commentsarea='justdate.php';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div></div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}		 
//END

// EVENT - EVENTO
elseif (($row['lang_key']=='_bx_events_spy_post' OR $row['lang_key']=='_bx_events_spy_join' OR $row['lang_key']=='_bx_events_spy_rate' OR $row['lang_key']=='_bx_events_spy_comment' OR $row['lang_key']=='_bx_events_spy_post_change' OR $row['lang_key']=='_ibdw_evowall_bx_event_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Events"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_events_spy_post') {$stampa=_t("_ibdw_evowall_event_add");}
  elseif ($row['lang_key']=='_bx_events_spy_join') {$stampa=_t("_ibdw_evowall_event_join");}
  elseif ($row['lang_key']=='_bx_events_spy_rate') {$stampa=_t("_ibdw_evowall_event_rate");}
  elseif ($row['lang_key']=='_bx_events_spy_comment') {$stampa=_t("_ibdw_evowall_event_comment");}
  elseif ($row['lang_key']=='_bx_events_spy_post_change') {$stampa=_t("_ibdw_evowall_event_edit");}
  elseif ($row['lang_key']=='_ibdw_evowall_bx_event_add_condivisione') {$stampa=_t("_ibdw_evowall_bx_event_add_condivisione");}
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $queryevento="SELECT Title,PrimPhoto,Description,EntryUri,ID,EventStart,EventEnd,Status FROM bx_events_main WHERE EntryUri='$verificauri'";
  $resultevento=mysql_query($queryevento);
  $rowevento=mysql_fetch_row($resultevento); 
  $roweventonum=mysql_num_rows($resultevento); 
  
  if ($roweventonum==0) $tmpx++;
  else 
  {
      
  $queryfotoevento="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowevento[1];
  $resultfotoevento=mysql_query($queryfotoevento);
  $rowfotoevento=mysql_fetch_row($resultfotoevento);
  $idswiaee=$row[id];
  $idswiab=$row[id];
  $querypriva="SELECT allow_view_event_to FROM bx_events_main WHERE id=".$rowevento['4'];
  $resultpriva=mysql_query($querypriva);
  $rowpriva=mysql_fetch_row($resultpriva);
  $okvista=$funclass->privstate($rowpriva[0],'events',$row['sender_id'],$accountid,$num_fave,'view_event'); 
  if($rowevento[0]==FALSE or $rowevento[7]=='pending') $tmpx++;
  elseif ($okvista==1)
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) {
   if($verifica_partent==0){
   echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'">
   <input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" />
            <a class="bt_open'.$assegnazione.'" id="bt_open">
              <img src="'.$imagepath.'fx_down.png">
            </a>
           </div>
           <div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
   
   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
   {
    /**Share System**/
    include('bt_condivisione_1.php');
    $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
    $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    $parametri_photo['entry_url']=$unserialize['entry_url'];
    $parametri_photo['entry_title']=$unserialize['entry_title'];
    $params_condivisione=serialize($parametri_photo);
    $bt_condivisione_params['1']=$accountid; //Sender
    $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
    $bt_condivisione_params['3']='_ibdw_evowall_bx_event_add_condivisione'; //Lang_Key_share 
    $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
    include('bt_condivisione_2.php');
    /**End Share System**/
   }
    echo '</div>'; //div che chiude il bt_list di evo
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
    }
    }
    $nomeevento=$unserialize['entry_title'];
    $nomeevento=$funclass->tagliaz($nomeevento,80);
    $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
    else $stampa=str_replace('{profile_nick}',$realname,$stampa);
    $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
    $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);             
    $stampa=str_replace('{entry_title}',$nomeevento,$stampa);
    echo $stampa;
    echo '</div><div id="bloccoav"><div id="anteprima">';
    if ($rowfotoevento[3]==FALSE) {echo '<a href="'.$unserialize[entry_url].'"><img src="'.$imagepath.'unk.png" class="unklockstyle2"></a>';}
    else {echo '<a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotoevento[3].'.'.$rowfotoevento[1].'"class="unklockstyle" onload="$(this).fadeIn(200);"></a>';}
    $descrizionei=$rowevento[2];
    $descrizionei=$funclass->tagliaz($descrizionei,300);
	if ($seldate=="d/m/Y H:i:s")
	{
	 $dateeventstart=date("d/m/Y H:i:s",($rowevento[5]+$offset));
	 $dateeventend=date("d/m/Y H:i",($rowevento[6]+$offset));
	}
	else
	{
	 $dateeventstart=date("m/d/Y H:i",($rowevento[5]+$offset));
	 $dateeventend=date("m/d/Y H:i",($rowevento[6]+$offset));
	}
    echo '</div><div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$rowevento[0].'</h3></a> <p>'.$funclass->cleartesto($descrizionei).'</p><div id="eventmulticont"><div id="eventdatacont"><p class="eventsdate">'._t("_ibdw_evowall_event_start").'</p><div class="eventvalue">'.$dateeventstart.'</div></div><div id="eventdatacont"><p class="eventsdate">'._t("_ibdw_evowall_event_end").'</p><div class="eventvalue">'.$dateeventend.'</div></div></div></div></div>';
  
    if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
    else $commentsarea='justdate.php';
    include ($commentsarea);
    
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div></div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; ;
  }
  else $tmpx++;
 }
 }
 else $tmpx++;
}
//END

// UE30 - EVENT
elseif (($row['lang_key']=='_ue30_event_spy_post' OR $row['lang_key']=='_ue30_event_spy_join' OR $row['lang_key']=='_ue30_event_spy_rate' OR $row['lang_key']=='_ue30_event_spy_comment' OR $row['lang_key']=='_ue30_event_spy_post_change' OR $row['lang_key']=='_ibdw_evowall_ue30_event_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Events")))  
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_ue30_event_spy_post') {$stampa=_t("_ibdw_evowall_event_add");}
  elseif ($row['lang_key']=='_ue30_event_spy_join') {$stampa=_t("_ibdw_evowall_event_join");}
  elseif ($row['lang_key']=='_ue30_event_spy_rate') {$stampa=_t("_ibdw_evowall_event_rate");}
  elseif ($row['lang_key']=='_ue30_event_spy_comment') {$stampa=_t("_ibdw_evowall_event_comment");}
  elseif ($row['lang_key']=='_ue30_event_spy_post_change') {$stampa=_t("_ibdw_evowall_event_edit");}
  elseif ($row['lang_key']=='_ibdw_evowall_ue30_event_add_condivisione') {$stampa=_t("_ibdw_evowall_ue30_event_add_condivisione");}
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $queryevento="SELECT Title,PrimPhoto,Description,EntryUri,ID,EventStart,EventEnd,Status FROM ue30_event_main WHERE EntryUri='$verificauri'";
  $resultevento=mysql_query($queryevento);
  $rowevento=mysql_fetch_row($resultevento);     
  $queryfotoevento="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowevento[1];
  $resultfotoevento=mysql_query($queryfotoevento);
  $rowfotoevento=mysql_fetch_row($resultfotoevento);
  $idswiaee=$row[id];
  $idswiab=$row[id];
  $querypriva="SELECT allow_view_event_to FROM ue30_event_main WHERE id=".$rowevento['4'];
  $resultpriva=mysql_query($querypriva);
  $rowpriva=mysql_fetch_row($resultpriva);
  $okvista=$funclass->privstate($rowpriva[0],'events',$row['sender_id'],$accountid,$num_fave,'view_event'); 
  if($rowevento[0]==FALSE or $rowevento[7]=='pending') $tmpx++;
  elseif ($okvista==1)
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) {
   if($verifica_partent==0){
   echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'">
   <input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" />
            <a class="bt_open'.$assegnazione.'" id="bt_open">
              <img src="'.$imagepath.'fx_down.png">
            </a>
           </div>
           <div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
   {
    /**Share System**/
    include('bt_condivisione_1.php');
    $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
    $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    $parametri_photo['entry_url']=$unserialize['entry_url'];
    $parametri_photo['entry_title']=$unserialize['entry_title'];
    $params_condivisione=serialize($parametri_photo);
    $bt_condivisione_params['1']=$accountid; //Sender
    $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
    $bt_condivisione_params['3']='_ibdw_evowall_bx_event_add_condivisione'; //Lang_Key_share 
    $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
    include('bt_condivisione_2.php');
    /**End Share System**/
   }
    echo '</div>'; //div che chiude il bt_list di evo
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
    }
    $nomeevento=$unserialize['entry_title'];
    $nomeevento=$funclass->tagliaz($nomeevento,80);
    $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
    else $stampa=str_replace('{profile_nick}',$realname,$stampa);
    $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
    $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);             
    $stampa=str_replace('{entry_title}',$nomeevento,$stampa);
    echo $stampa;
    echo '</div><div id="bloccoav"><div id="anteprima">';
    if ($rowfotoevento[3]==FALSE) {echo '<a href="'.$unserialize[entry_url].'"><img src="'.$imagepath.'unk.png" class="unklockstyle2"></a>';}
    else {echo '<a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotoevento[3].'.'.$rowfotoevento[1].'"class="unklockstyle" onload="$(this).fadeIn(200);"></a>';}
    $descrizionei=$rowevento[2];
    $descrizionei=$funclass->tagliaz($descrizionei,300);
	if ($seldate=="d/m/Y H:i:s")
	{
	 $dateeventstart=date("d/m/Y H:i:s",($rowevento[5]+$offset));
	 $dateeventend=date("d/m/Y H:i",($rowevento[6]+$offset));
	}
	else
	{
	 $dateeventstart=date("m/d/Y H:i",($rowevento[5]+$offset));
	 $dateeventend=date("m/d/Y H:i",($rowevento[6]+$offset));
	}
    echo '</div><div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$rowevento[0].'</h3></a> <p>'.$funclass->cleartesto($descrizionei).'</p><div id="eventdatacont"><p class="eventsdate">'._t("_ibdw_evowall_event_start").'</p><div class="eventvalue">'.$dateeventstart.'</div></div><div id="eventmulticont"><div id="eventdatacont"><p class="eventsdate">'._t("_ibdw_evowall_event_end").'</p><div class="eventvalue">'.$dateeventend.'</div></div></div></div></div>';	
  
    if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
    else $commentsarea='justdate.php';
    include ($commentsarea);
   
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div></div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

// LOCATION UE30
elseif ($row['lang_key']=='_ue30_location_spy_post' OR $row['lang_key']=='_ue30_location_spy_post_change' OR $row['lang_key']=='_ue30_location_spy_join' OR $row['lang_key']=='_ue30_location_spy_rate' OR $row['lang_key']=='_ue30_location_spy_comment' OR $row['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione')  
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t($row['lang_key']);
  $trovaslash=substr_count($unserialize[entry_url],"/");
  $verificauri=explode ("/",$unserialize[entry_url]);
  $verificauri=$verificauri[$trovaslash];
  $querylocation="SELECT title,thumb,ue30_location_main.desc,uri,id,country,city,allow_view_location_to FROM ue30_location_main WHERE uri='$verificauri'";
  $resultlocation=mysql_query($querylocation);
  $rowlocation=mysql_fetch_row($resultlocation);     
  $queryfotolocation="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowlocation[1];
  $resultfotolocation=mysql_query($queryfotolocation);
  $rowfotolocation=mysql_fetch_row($resultfotolocation);
  $idswiaee=$row[id];
  $idswiab=$row[id];
  $okvista=$funclass->privstate($rowlocation[7],'locations',$row['sender_id'],$accountid,$num_fave,'view_event'); 
  if($rowlocation[0]==FALSE) $tmpx++;
  elseif ($okvista==1)
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) {
   if($verifica_partent==0){
   echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'">
   <input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" />
            <a class="bt_open'.$assegnazione.'" id="bt_open">
              <img src="'.$imagepath.'fx_down.png">
            </a>
           </div>
           <div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
   {
    /**Share System**/
    include('bt_condivisione_1.php');
    $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
    $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    $parametri_photo['entry_url']=$unserialize['entry_url'];
    $parametri_photo['entry_title']=$unserialize['entry_title'];
    $params_condivisione=serialize($parametri_photo);
    $bt_condivisione_params['1']=$accountid; //Sender
    $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
    $bt_condivisione_params['3']='_ibdw_evowall_ue30_locations_add_condivisione'; //Lang_Key_share 
    $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
    include('bt_condivisione_2.php');
    /**End Share System**/
   }
    echo '</div>'; //div che chiude il bt_list di evo
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
    }
    $nomelocation=$unserialize['entry_title'];
    $nomelocation=$funclass->tagliaz($nomelocation,80);
    $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
    if($usernameformat=='Nickname') $stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);
    else $stampa=str_replace('{profile_nick}',$realname,$stampa);
    $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
    $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);             
    $stampa=str_replace('{entry_title}',$nomelocation,$stampa);
    echo $stampa;
    echo '</div><div id="bloccoav"><div id="anteprima">';
    if ($rowfotolocation[3]==FALSE) {echo '<a href="'.$unserialize[entry_url].'"><img src="'.$imagepath.'unk.png" class="unklockstyle2"></a>';}
    else {echo '<a href="'.$unserialize[entry_url].'"><img src="m/photos/get_image/browse/'.$rowfotolocation[3].'.'.$rowfotolocation[1].'"class="unklockstyle" onload="$(this).fadeIn(200);"></a>';}
    $descrizionei=$rowlocation[2];
    $descrizionei=$funclass->tagliaz($descrizionei,300);
    echo '</div><div id="descrizione"><a href="'.$unserialize[entry_url].'"><h3>'.$rowlocation[0].'</h3></a> <p>'.$funclass->cleartesto($descrizionei).'</p></div></div>';
	if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
    else $commentsarea='justdate.php';
    include ($commentsarea);
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div></div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

// POLL - SONDAGGIO
elseif (($row['lang_key']=='_bx_poll_added' OR $row['lang_key']=='_bx_poll_answered' OR $row['lang_key']=='_bx_poll_rated' OR $row['lang_key']=='_bx_poll_commented' OR $row['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Polls")))
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_poll_added') $stampa=_t("_ibdw_evowall_poll_add");
  elseif ($row['lang_key']=='_bx_poll_answered') $stampa=_t("_ibdw_evowall_reply_polls");
  elseif ($row['lang_key']=='_bx_poll_rated') $stampa=_t("_ibdw_evowall_rated_polls");
  elseif ($row['lang_key']=='_bx_poll_commented') $stampa=_t("_ibdw_evowall_comment_polls");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') $stampa=_t("_ibdw_evowall_bx_poll_add_condivisione");
  $idswiaaa=$row[id];
  echo $parteintroduttiva;
  echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
  if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare') AND $row['lang_key']=='_bx_poll_added' OR $row['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
  {
   if($verifica_partent==0)
   {
    echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
	if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
    {
     if ($row['lang_key']=='_bx_poll_added' OR $row['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') 
     {
      /**Share System**/
      include('bt_condivisione_1.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=$funclass->TipoUtente($row['sender_id'],$usernameformat);
      $parametri_photo['poll_url']=str_replace('&','xeamp',$unserialize['poll_url']);
      $parametri_photo['poll_caption']=$unserialize['poll_caption'];
      $params_condivisione=serialize($parametri_photo);
      $bt_condivisione_params['1']=$accountid; //Sender
      $bt_condivisione_params['2']=$row['sender_id']; //Recipient 
      $bt_condivisione_params['3']='_ibdw_evowall_bx_poll_add_condivisione'; //Lang_Key_share 
      $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
      include('bt_condivisione_2.php');
      /**End Share System**/
     }
    }
    echo '</div>'; //div che chiude il bt_list di evo
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
   }
  }
  $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
  if($usernameformat=='Nickname') {$stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);}
  else $stampa=str_replace('{profile_nick}',$realname,$stampa);
  $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
  $stampa=str_replace('{recipient_p_link}',$unserialize['recipient_p_link'],$stampa);
  $stampa=str_replace('{recipient_p_nick}',$unserialize['recipient_p_nick'],$stampa);
  $stampa=str_replace('{poll_url}',$unserialize['poll_url'],$stampa);             
  $stampa=str_replace('{poll_caption}',$unserialize['poll_caption'],$stampa);
  $stampa=str_replace('{entry_url}',$unserialize['poll_link'],$stampa);
  echo $stampa;
  echo '</div>';  
  if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
  else $commentsarea='justdate.php';
  include ($commentsarea); 
  if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div></div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
 }
 else $tmpx++;
}
//END

//URL AUTODETECTION - Plugin
elseif (($row['lang_key']=='_ibdw_evowall_bx_url_add' or $row['lang_key']=='_ibdw_evowall_bx_url_share') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Sites")))
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $parametri_photo['immagine']=$unserialize['immagine'];
  $ricavarenomi="SELECT NickName FROM Profiles WHERE ID=".$row[sender_id];
  $resultricavanomi=mysql_query($ricavarenomi);
  $rowricavanomi=mysql_fetch_row($resultricavanomi);
  echo $parteintroduttiva;
  echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
  if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
  {
   if($verifica_partent==0)
   {
    echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open">
          <img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'inc_eliminab.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
    {
     /**Share System**/
     include('bt_condivisione_1.php');
     $parametri_photo['sender_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
     $parametri_photo['sender_p_nick']=$aInfomembers['NickName'];
     $parametri_photo['indirizzo']=$unserialize['indirizzo'];
     $parametri_photo['titolosito']=str_replace("persaccento","'",str_replace(array("\n","\r"),"",urldecode($unserialize['titolosito']))); 
	 $parametri_photo['descrizione']=str_replace("persaccento","'",str_replace(array("\n","\r"),"",trim(urldecode($unserialize['descrizione']))));
     $parametri_photo['immagine']=preg_replace("/\s*/m","",$unserialize['immagine']);
     $parametri_photo['anteprimano']=$unserialize['anteprimano'];  
     $params_condivisione=serialize($parametri_photo);   
     $bt_condivisione_params['1']=$accountid; //Sender
	 if ($profileid<>$row['sender_id']) $bt_condivisione_params['2']=$row['sender_id']; //Recipient
	 else $bt_condivisione_params['2']=0; //Recipient  
     $bt_condivisione_params['3']='_ibdw_evowall_bx_url_share'; //Lang_Key_share
     $bt_condivisione_params['4']=readyshare($params_condivisione); //Params
     include('bt_condivisione_2.php');
     /**End Share System**/
	}
	echo '</div>'; //div che chiude il bt_list di evo
	if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
	if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_external_eliminab.php';
   }
  }
  $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
  echo '<a class="usernamestyle" href="'.$rowricavanomi[0].'">';
  if($usernameformat=='Nickname') echo $rowricavanomi[0];
  else echo $realname; 
  $indirizzourl=$unserialize['indirizzo'];
  $messaggiourl=$unserialize['messaggio'];
  echo '</a>';
  if ($row['sender_id']<>$row['recipient_id'] and $row['recipient_id']<>0)
  { 
   $ricavarenomi1="SELECT NickName FROM Profiles WHERE ID=".$row[recipient_id];
   $resultricavanomi1=mysql_query($ricavarenomi1);
   $rowricavanomi1=mysql_fetch_row($resultricavanomi1);
   echo '<img src="'.$imagepath.'versus.png" class="versusimg">';
   $realname=$funclass->TipoUtente($row['recipient_id'],$usernameformat);
   echo '<a class="usernamestyle" href="'.$rowricavanomi1[0].'">';
   if($usernameformat=='Nickname') echo $rowricavanomi1[0];
   else echo $realname;
   echo '</a>';
  }
  if ($messaggiourl<>"" and $messaggiourl<>$indirizzourl) 
  {
   $messaggiourl=str_replace("codapos1","'",$messaggiourl);
   $messaggiourl=str_replace("%26","&",$messaggiourl);
   echo '<span class="actionx">'._t("_ibdw_evowall_messstyle1").$messaggiourl._t("_ibdw_evowall_messstyle2").'</span><div id="spacer"></div>';
  }
  else 
  {    
   if ($row['lang_key']=='_ibdw_evowall_bx_url_add') echo '<span class="actionx">'._t("_ibdw_evowall_urlmess").'<a target="_blank" href="'.$indirizzourl.'">'.$indirizzourl.'</a></span>';
   elseif($row['lang_key']=='_ibdw_evowall_bx_url_share') echo '<span class="actionx">'._t("_ibdw_evowall_surlmess").'<a target="_blank" href="'.$indirizzourl.'">'.$indirizzourl.'</a></span>';
  }
  echo '</div><div id="bloccoav">';
  if($unserialize['anteprimano']==1 OR $unserialize['immagine']=='undefined') echo '<style>#webint'.$assegnazione.'{width:96%;}</style>'; 
  else 
  {
   //Check image. If not exists (error 404) is not displayed
   $ch=curl_init($unserialize['immagine']);
   curl_setopt($ch,CURLOPT_NOBODY,true);
   curl_exec($ch);
   $retcode=curl_getinfo($ch,CURLINFO_HTTP_CODE);
   curl_close($ch);
   if ($retcode<>404) echo '<div id="anteprima"><a target="_blank" href="'.$indirizzourl.'"><img src="'.$unserialize['immagine'].'" class="webimage"></a></div>';
  }
  $titolodefinitivo=str_replace("codapos1","'",urldecode(str_replace("persaccento","'",str_replace(array("\n","\r"), "",$unserialize['titolosito']))));
  $titolodefinitivo=str_replace("codapos2","&quot;",$titolodefinitivo);
  $titolodefinitivo=html_entity_decode($titolodefinitivo);
  $descrizionedefinitiva=$funclass->resetslash(str_replace("&apos;","'",urldecode($unserialize['descrizione'])));
  
  $descrizionedefinitiva=str_replace("ibdwbackslashibdwbackslash","&#92;",$descrizionedefinitiva);
  $descrizionedefinitiva=str_replace("codapos2","&quot;",$descrizionedefinitiva);
  echo '<div class="webint'.$assegnazione.'" id="descrizione"><a target="_blank" href="'.$indirizzourl.'"><h3>';
  echo $titolodefinitivo;
  echo '</h3></a><p class="stylewebsite"><a href="'.$indirizzourl.'" target="_blank" >'.$indirizzourl.'</a></p><p>'.$descrizionedefinitiva.'</p></div><div class="clear"></div></div>'; 
  if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
  else $commentsarea='justdate.php';
  include ($commentsarea);
  if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div></div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
 }
 else $tmpx++;
}
//END

// MESSAGE - MESSAGGIO PERSONALE
elseif (($row['lang_key']=='_ibdw_evowall_bx_evowall_message') OR ($row['lang_key']=='_ibdw_evowall_bx_evowall_messageseitu')) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $ricavarenomi="SELECT NickName FROM Profiles WHERE ID=".$row[sender_id];
  $resultricavanomi=mysql_query($ricavarenomi);
  $rowricavanomi=mysql_fetch_row($resultricavanomi);
  if($row['lang_key']=='_ibdw_evowall_bx_evowall_messageseitu')
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if (($inviatore==$accountid OR $ricevitore==$profileid OR isAdmin() OR isModerator()) AND $accountid!=0) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     include 'inc_eliminab.php';
     echo '</div>';
     include 'bt_external_eliminab.php'; 
    }
   }
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   $parmessaggio=$unserialize['messaggioo'];
   $parmessaggio=$funclass->urlreplace($parmessaggio);
   $parmessaggio=str_replace("`","'",$parmessaggio);
   $parmessaggio=strip_tags($parmessaggio,'<br/><a>');
   if ($newline=="") $parmessaggio=str_replace("<br/>"," ",$parmessaggio);
   echo '<a class="usernamestyle" href="'.$rowricavanomi[0].'">';
   if($usernameformat=='Nickname') echo $rowricavanomi[0];
   else echo $realname;
   echo '</a><span class="actionx">'._t("_ibdw_evowall_messstyle1").$parmessaggio._t("_ibdw_evowall_messstyle2").'</span></div>'; 
  }
  else 
  {
   $ricavarenomi1="SELECT NickName FROM Profiles WHERE ID=".$row[recipient_id];
   $resultricavanomi1=mysql_query($ricavarenomi1);
   $rowricavanomi1=mysql_fetch_row($resultricavanomi1);
   echo $parteintroduttiva;
   echo '<div id="messaggio" class="'.$avatartype.'"><div id="primariga">';
   if (($inviatore==$accountid OR $ricevitore==$profileid OR isAdmin() OR isModerator()) AND $accountid!=0) 
   {
    if($verifica_partent==0)
	{
     echo '<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" />
     <a class="bt_open'.$assegnazione.'" id="bt_open"><img src="'.$imagepath.'fx_down.png"></a></div>
     <div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'"> '; //start bt list di evo
     include 'inc_eliminab.php';
     echo '</div>';
     include 'bt_external_eliminab.php'; 
    }
   }
   $realname=$funclass->TipoUtente($row['sender_id'],$usernameformat);
   $parmessaggio=$unserialize['messaggioo'];
   $parmessaggio=strip_tags($parmessaggio,'<br/><a>');
   $parmessaggio=$funclass->urlreplace($parmessaggio);
   $parmessaggio=str_replace("`","'",$parmessaggio);
   if ($newline=="") $parmessaggio=str_replace("<br/>"," ",$parmessaggio);
   echo '<a class="usernamestyle" href="'.$rowricavanomi[0].'">';
   if($usernameformat=='Nickname') echo $rowricavanomi[0];
   else echo $realname;
   echo '</a><img src="'.$imagepath.'versus.png" class="versusimg">';
   $realname=$funclass->TipoUtente($row['recipient_id'],$usernameformat);
   echo '<a class="usernamestyle" href="'.$rowricavanomi1[0].'">';
   if($usernameformat=='Nickname') echo $rowricavanomi1[0];
   else echo $realname;
   echo'</a><span class="actionx">'._t("_ibdw_evowall_messstyle1").$parmessaggio._t("_ibdw_evowall_messstyle2").'</span></div>';
  }
  if((($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) or ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))) and (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment')) or ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike'))) and ($verifica_partent==0)) $commentsarea='scomment.php';
  else $commentsarea='justdate.php';
  include ($commentsarea);
  if($verifica_partent!=0)  echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div></div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
 }
 else $tmpx++;
}
//END

}//while
//div listanotizie

if(!isset($hidden_intro)) echo '</div>';  
if(isset($typeoforder)) $typeoforder=$typeoforder;
else 
{
 if(isset($_COOKIE["typeoforder"]))
 {
  if($_COOKIE["typeoforder"]=='1') $typeoforder="Popular";
  else $typeoforder="";
 }
 else $typeoforder="";
}
?>
<script>
function delaycustom() {ajax_load_close();}
function agg_ajax() 
{
 ajax_load_active();
 <?php
  if(!isset($ultimoid)) $ultimoid=$GLOBAL['ultimoid'];
  echo 'verificaupdate('.$ultimoid.',\''.$pagina.'\',\''.$contanews.'\');';
 ?> 
 setTimeout('delaycustom()',2000);
}
<?php
if ($typeoforder!="Popular" AND $miapag!='profile') {
?>
window.onfocus=function () 
{
 tempodelta= new Date().getTime();
 if (((tempodelta-tempoinit)><?php echo $refreshtime;?>) && (<?php echo $refreshtype;?>='Auto'))
 {
  <?php
  if(!isset($ultimoid)) $ultimoid=$GLOBAL['ultimoid'];
  echo 'verificaupdate('.$ultimoid.',\''.$pagina.'\',\''.$contanews.'\');';?>
  tempoinit= new Date().getTime();
 }
}
<?php } ?>
</script>
<script type="text/javascript">
function verificaupdate(ultimoid,pagina,contanews) {
    $.ajax({
      type: 'POST',
      url: 'modules/ibdw/evowall/verificaupdate.php',
      data: "ultimoid=" + ultimoid + "&pagina=" + pagina + "&contanews=" + contanews + "&idrichiesto=<?php echo $accountid;?>",
      success: function(html){
      $('#updateajax').prepend(html);
      $('#updateajax').fadeIn(1000);
      }
    });
}
function aggiornabottonealtrenews(contanews,limite,pagina,idrichiesto,ultimoid) {
  $.ajax({
      url: 'modules/ibdw/evowall/bottonealtrenews.php',
      type: 'POST',
      data: "contanews=" + contanews + "&limite=" + limite + "&pagina=" + pagina + "&idrichiesto=" + idrichiesto+ "&ultimoid=" + ultimoid,
      success: function(data) {
            $('#altro').html(data);
      }
    });
}
</script>