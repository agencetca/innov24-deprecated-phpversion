<?php
$retriveidevowall="SELECT ID FROM sys_options_cats WHERE name='EVO Wall'";
$risultatoid = mysql_query($retriveidevowall);
$idmodulo=mysql_fetch_assoc($risultatoid);
$querydiconfigurazione="SELECT Name, VALUE FROM sys_options WHERE kateg=".$idmodulo['ID'];
$risultato = mysql_query($querydiconfigurazione);
while ($feccia=mysql_fetch_array($risultato))
{
 $name = $feccia['Name'];
 $riga[$name]=$feccia['VALUE'];
}
$color=$riga['TemplateColor'];
$licensekey=$riga['LicenseKey'];
$DefaultProfilePrivacy=$riga['DefaultProfilePrivacy'];
$DefaultAccountPrivacy=$riga['DefaultAccountPrivacy'];
$DefaultHomePrivacy=$riga['DefaultHomePrivacy'];
$photo=$riga['AllowPhotos'];
$video=$riga['AllowVideos'];
$group=$riga['AllowGroups'];
$event=$riga['AllowEvents'];
$eventmodule=$riga['EventMod'];
$site=$riga['AllowSites'];
$poll=$riga['AllowPolls'];
$ads=$riga['AllowAds'];
$blogs=$riga['AllowBlogs'];
$sounds=$riga['AllowSounds'];             
$messlength=$riga['MessageLenght'];
$commlength=$riga['CommentLenght'];
$commprevnum=$riga['CommentPrevNum'];
$commnum=$riga['CommentNum'];
$newline=$riga['NewLine'];
$avatartype=$riga['AvatarType'];
$usernameformat=$riga['NameFormat'];
$hideupdate=$riga['DisplayProfileUpdate'];
$ordinec=$riga['CommentOrder'];
$namephotoalbum=str_replace(" ","-",preg_replace('/\s{2,}/',' ',$riga['WallPhotoName']));
$namevideoalbum=str_replace(" ","-",preg_replace('/\s{2,}/',' ',$riga['WallVideoName'])); 
$privacyalbum=$riga['WallDefaultPrivacy'];
if($privacyalbum == 'Default') { $privacyalbum=1; }
elseif($privacyalbum == 'Me Only') { $privacyalbum=2; }
elseif($privacyalbum == 'Public') { $privacyalbum=3; }
elseif($privacyalbum == 'Members') { $privacyalbum=4; }
elseif($privacyalbum == 'Friends') { $privacyalbum=5; }
elseif($privacyalbum == 'Faves') { $privacyalbum=6; }            
$grouping=$riga['Grouping'];
$widthphoto=$riga['PhotoSizeWidth'];
$photolarge=$riga['PhotoLarge'];
$photoautozoom=$riga['PhotoAutoZoom'];
$widthvideo=$riga['VideoSizeWidth'];
$heightvideo=$riga['VideoSizeHeight'];
$limite=$riga['DisplayNewsNumber'];
$spyprofileview=$riga['ProfileViewedBy'];
$bkunconfirmed=$riga['DenyAccessToUnconfirmed'];
$refreshtype=$riga['RefreshType'];
$refreshtime=$riga['Refreshrate']*60000;
$autoscroll=$riga['AutoScroll'];
$delaymillisecond = $riga['AutoScrollTime'];
$hidemorenews=$riga['HideMoreNewsButton'];
$seldate=$riga['DateFormat'];

if ($seldate=="dd/mm/yyyy")
{
 $seldate="d/m/Y H:i:s";
}
elseif($seldate=="mm/dd/yyyy")
{
 $seldate="m/d/Y H:i:s";
}
elseif($seldate=="yyyy/mm/dd")
{
 $seldate="Y/m/d H:i:s";
}
$offset=$riga['Offset']; 
$welcome=$riga['WelcomeMessage'];
$welcome_n_query = $riga['WelcomeNPost'];
$welcome_cookie_day = $riga['WelcomeCLife'];
$VideoYoutubeM =  $riga['VideoYoutubeM'];
$VideoFalshM =  $riga['VideoFalshM'];
$VideoOtherM =  $riga['VideoOtherM'];
$PhotoRegularM =  $riga['PhotoRegularM'];
$PhotoFlashM =  $riga['PhotoFlashM'];
$PhotoOtherM =  $riga['PhotoOtherM'];
$DefaultPhotoM =  $riga['DefaultPhotoM'];
$DefaultVideoM =  $riga['DefaultVideoM'];
$UrlPlugin =  $riga['UrlPlugin'];
$daysmostpopular =$riga['DaysMostPopular'];
$modzzzproperty =$riga['ModzzzProperty'];
$ue30locations =$riga['UE30Locations'];
$timing_similar = $riga['TimingSimilar'];
$similar_news_min = $riga['SimilarNewsMin'];
$photo_max_preview=$riga['PhotoMaxPreview'];
$fbenabled=$riga['AllowFacebook'];
$ggenabled=$riga['AllowGoogle'];
$twenabled=$riga['AllowTwitter'];
$lienabled=$riga['AllowLinkedIn'];
$psenabled=$riga['AllowPinterest'];
$bienabled=$riga['AllowBaidu'];
$wbenabled=$riga['AllowWeibo'];
$qzenabled=$riga['AllowQzone'];

//other 3d party modules
$modzzzclubs="on";
$modzzzpetitions="on";
$modzzzpets="on";
$modzzzbands="on";
$modzzzschools="on";
$modzzznotices="on";
$modzzzclassified="on";
$modzzznews="on";
$modzzzjobs="on";
$modzzzlist="on";
?>