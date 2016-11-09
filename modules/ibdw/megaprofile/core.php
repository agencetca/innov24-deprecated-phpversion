<script>
(function(a){a.fn.autoResize=function(j){var b=a.extend({onResize:function(){},animate:true,animateDuration:150,animateCallback:function(){},
extraSpace:20,limit:1000},j);this.filter('textarea').each(function(){var c=a(this)
.css({resize:'none','overflow-y':'hidden'}),k=c.height(),
f=(function(){var l=['height','width','lineHeight','textDecoration','letterSpacing'],h={};a.each(l,function(d,e){h[e]=c.css(e)});
return c.clone().removeAttr('id')
.removeAttr('name').css({position:'absolute',top:0,left:-9999}).css(h).attr('tabIndex','-1').insertBefore(c)})(),i=null,g=function()
{f.height(0).val(a(this).val()).scrollTop(10000);var d=Math.max(f.scrollTop(),k)+b.extraSpace,e=a(this).add(f);if(i===d){return}i=d;if(d>=b.limit)
{a(this).css('overflow-y','');return}b.onResize.call(this);b.animate&&c.css('display')==='block'?e.stop().animate({height:d},b.animateDuration,b.animateCallback):
e.height(d)};c.unbind('.dynSiz').bind('click.dynSiz',g).bind('keydown.dynSiz',g).bind('change.dynSiz',g)});return this}})(jQuery);

$(document).ready(function($){
	    $('#deskup').autoResize();
});
</script>

<?php
if(isset($_POST['thisajax'])) 
{ 
 require_once( '../../../inc/header.inc.php' );
 require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
 require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
 require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
}
include BX_DIRECTORY_PATH_MODULES.'ibdw/megaprofile/config.php';
if (!defined('BX_AVA_EXT') and ($thumbtype==0)) {
    define ('BX_AVA_DIR_USER_AVATARS', BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/data/images/'); // directory where avatar images are stored
    define ('BX_AVA_URL_USER_AVATARS', BX_DOL_URL_MODULES . 'boonex/avatar/data/images/'); // base url for all avatar images
    define ('BX_AVA_EXT', '.jpg'); // default avatar extension
    define ('BX_AVA_W', 64); // avatar image width
    define ('BX_AVA_H', 64); // avatar image height
    define ('BX_AVA_ICON_W', 32); // avatar icon width
    define ('BX_AVA_ICON_H', 32); // avatar icon height
 }
mysql_query("SET NAMES 'utf8'");

if(isset($_POST['idus'])) {$ottieniID = $_POST['idus'];}
else {$ottieniID=getID($_REQUEST['ID']);}

$versione="SELECT Name,sys_options.VALUE FROM sys_options WHERE Name='sys_tmp_version'";
$esegui=mysql_query($versione);
$riga=mysql_fetch_assoc($esegui);
$explode=explode('.',$riga['VALUE']);
$versionedolmain=$explode[1];
$versionedol=$explode[2];

$infoprof=getProfileInfo($ottieniID);
$usernameis = $infoprof['NickName'];

$IDmio=$_COOKIE['memberID'];
$valoriutente=getProfileInfo($proprietario);

//foto del profilo
//for D7.0 and D7.1
if ($versionedolmain=='0')
{
 $queryprofilos="SELECT ibdw_mega_profile.Hash FROM ibdw_mega_profile INNER JOIN bx_photos_main ON ibdw_mega_profile.Hash=bx_photos_main.Hash WHERE ibdw_mega_profile.Owner=".$ottieniID." Limit 0,1";
 $risultfoto=mysql_query($queryprofilos);
 $contarisfoto=mysql_num_rows($risultfoto);
 $mainfoto=mysql_fetch_assoc($risultfoto);
 $nomeutente = getNickName($ottieniID);
}
else
{
 $nomeutente = getUsername($ottieniID);
 $ottienialbum = "SELECT VALUE FROM sys_options WHERE Name='bx_photos_profile_album_name'"; 
 $eseguialbum = mysql_query($ottienialbum);
 $fetchalbum= mysql_fetch_assoc($eseguialbum);
 $nalbumm = $fetchalbum['VALUE'];
 $nomealbum = uriFilter(str_replace("{nickname}",$nomeutente,$nalbumm));
 $nomealbumtrue = str_replace("{nickname}",$nomeutente,$nalbumm); 
 $nomealbumtrue=addslashes($nomealbumtrue);
 
 $queryprofilos="SELECT bx_photos_main.Hash FROM bx_photos_main INNER JOIN sys_albums ON bx_photos_main.ID=sys_albums.LastObjId WHERE bx_photos_main.Owner=".$ottieniID." AND sys_albums.Uri='$nomealbum' Limit 0,1";
 $risultfoto=mysql_query($queryprofilos);
 $contarisfoto=mysql_num_rows($risultfoto);
 $mainfoto=mysql_fetch_assoc($risultfoto);
}
function TagliaStringa($stringa, $max_char)
{
 if(strlen($stringa)>$max_char)
 {
  $stringa_tagliata=substr($stringa, 0,$max_char);
  $last_space=strrpos($stringa_tagliata," ");
  $stringa_ok=substr($stringa_tagliata, 0,$last_space);
  return $stringa_ok."...";
 }
 else{return $stringa;}
}

function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}

function cleardescriptiondisplayed ($inputstring)
{
 $inputstring=str_replace('ecommerciale','&',$inputstring);
 $inputstring = str_replace( "<br>", "<br>", $inputstring );
 $inputstring = str_replace( "<br/>", "<br>", $inputstring );
 $inputstring = str_replace( "<br />", "<br>", $inputstring );
 $inputstring = str_replace( "<BR>", "<br>", $inputstring );
 $inputstring = str_replace( "<BR/>", "<br>", $inputstring );
 $inputstring = str_replace( "<BR />", "<br>", $inputstring );
 $inputstring=strip_tags(html_entity_decode($inputstring),'<br>');
 $clearedtext=strip_html_tags($inputstring);
 return $clearedtext;
}

function stringtosendtojava ($textstring)
{
 $textstring=str_replace('"','apicedouble',$textstring);
 $textstring=addslashes($textstring);
 $textsent=htmlspecialchars($textstring);
 return $textsent;
}

function criptcodes($numero) 
{
 $id0 = rand(1,9);
 $id1 = rand(10000,99999);    
 $mx = $numero*$id0;
 $generacriptcodes = $id0.$id1.$mx;
 return ($generacriptcodes); 
}
    
function decriptcodes($code) 
{
 $estrazione0 = substr($code,0,1); 
 $estrazione = substr($code,6);   
 $mxestrazione = $estrazione/$estrazione0; 
 return ($mxestrazione); 
}

$photodeluxe = 0; 
//verifica installazione di photodeluxe
$verificaphotodeluxe = "SELECT uri FROM sys_modules WHERE uri = 'photo_deluxe'";
$eseguiverificaphotodeluxe = mysql_query($verificaphotodeluxe);
$numerophotodeluxe = mysql_num_rows($eseguiverificaphotodeluxe);
if($numerophotodeluxe != 0) { $photodeluxe = 1; }
 
if($photodeluxe == 1) 
{ 
 //verifichiamo se è abilitata l'integrazione tra i moduli 
 $integrazionepdx = "SELECT integrazionemegaprofile FROM photodeluxe_config WHERE ind = 1";
 $eseguiintregazionepdx = mysql_query($integrazionepdx);
 $rowintegrazionepdx = mysql_fetch_assoc($eseguiintregazionepdx);
 $attivaintegrazione = $rowintegrazionepdx['integrazionemegaprofile']; 
}
 
if(isset($_POST['crop'])) { 
echo '
<script>
$(document).ready(function($){
	    ibdw_crop();
});
</script>'; } 

?>
<div id="crop">
  <div class="topcrop">

		<?
		 if ($contarisfoto>0)
		 {
		?>
		<img src="<?php echo BX_DOL_URL_ROOT.'m/photos/get_image/file/'.$mainfoto['Hash'].'.jpg';?>" id="cropbox"/>
   <?php
        }
    $hashs = $mainfoto['Hash']; 
    $trovaid="SELECT ID,Hash FROM bx_photos_main WHERE Hash = '$hashs'"; 
    $eseguixe = mysql_query($trovaid); 
    $rowid = mysql_fetch_assoc($eseguixe);
    
    ?>
		
		<form>
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
			<input type="hidden" id="setavatar" name="setavatar" value="<?php if ($setavatard == 'ON') {echo '1';} else { echo'0';}?>" />
			<input type="hidden" id="ift" name="ift" value="<?php echo $rowid['ID'];?>"/>
			<?php if($thumbtype==0) {?>
      <input id="chekingava" type="checkbox" name="check" onclick="checking();" <?php if ($setavatard == 'ON') {echo 'checked';}?>/>
      <div id="chekingava" style="right: 324px;"><?php echo _t("_ibdw_mp_cropandavatar");?></div>
      <?php } ?>
       
		</form>
	</div>	
	<div class="downcrop">
	<h2><?php echo _t("_ibdw_mp_cropp");?></h2>
	<p><?php echo _t("_ibdw_mp_cropintro");?></p>
  <div id="bottonicrop">
  <div id="btncrop"><a href="javascript:cropping(<?php echo $rowid['ID'];?>);"><?php echo _t("_ibdw_mp_savecrop");?></a></div>
  <div id="btncrops"><a href="javascript:chiudiocrop(<?php echo $rowid['ID'];?>);"><?php echo _t("_ibdw_mp_cancellcrop");?></a></div>
  <div id="btncropx" onclick="closecroparea();"><?php echo _t("_ibdw_mp_annulla");?></div>
  </div>
  </div>
</div>
<div class="ibdwdbContent" <?php if($ottieniID == $IDmio) {echo ' onmouseout="if ($(\'#disabledl\').val()==0) outpic();" ';}?> ><div class="ibdwviewSwitchFile" <?php if($ottieniID == $IDmio) {echo ' onmouseout="if ($(\'#disabledl\').val()==0) outpic();" ';}?>>
<?php 
if($ottieniID == $IDmio) 
{ 
 echo '<div id="onpic" onclick="gopic();" onmouseout="outpic();" onmouseover="disableblink();">'. _t("_ibdw_mp_changepic").'</div><div id="onpic_sottomenu" onmouseout=""><ul><li class="introprofile">'._t("_ibdw_mp_changepic").'</li>';
 include('phdx_extension.php');
 echo '<li class="introalbum" onclick="ibdw_selectalbum();">'._t("_ibdw_mp_calbum").'</li><li class="introuploadfile" onclick="ibdw_frompc();">'._t("_ibdw_mp_cpc").'</li>';
 if($contarisfoto!=0) echo '<li class="introcropfile" id="pelagen"><a href="javascript:ibdw_crop();">'._t("_ibdw_mp_ritagliamenu").'</a></li>';
 if($contarisfoto!=0 and $thumbtype==0) echo '<li class="introsetava" onclick="ibdw_setavatar('.$rowid['ID'].');" id="croppelagen">'._t("_ibdw_mp_ritsetava").'</li>';
 if($webcam=='ON') echo '<li class="introtakecam" onclick="ibdw_take();">'._t("_ibdw_mp_takeweb").'</li>';
 if ($contarisfoto>0 and $thumbtype==0) echo '<li class="introremove" onclick="removeimage();">'._t("_ibdw_mp_removeimg").'</li>';
 echo '</ul></div><div id="blurfocus" onmouseover="outout();"></div><input type="hidden" id="disabledl" name="disabledl" value=0>';
 echo '<div class="picSwitcher" onmouseover="if ($(\'#disabledl\').val()==0) fadepic(); else outpic2();" onmouseout="xiudi();" >';
}
if ($contarisfoto>0) echo '<img class="ibdwmainPic" src="'.BX_DOL_URL_ROOT.'m/photos/get_image/file/'.$mainfoto['Hash'].'.jpg">';
else 
{
 if($defaultimage==1) 
 { 
  if($infoprof['Couple']<>"0") echo '<img class="ibdwmainPic" src="'.BX_DOL_URL_ROOT.'modules/ibdw/megaprofile/templates/base/images/couple.gif">';
  elseif($infoprof['Sex']=="female") echo '<img class="ibdwmainPic" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/woman_big.gif">';
	else echo '<img class="ibdwmainPic" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/man_big.gif"></a>';
 }
 else 
 {
  $nomepredefinitoalbum="SELECT VALUE FROM sys_options WHERE Name='bx_photos_profile_album_name'";
	$eseguinome=mysql_query($nomepredefinitoalbum);
	$nomee = mysql_fetch_array($eseguinome);
	$nalbumm=$nomee['VALUE'];
  $nomealbum=uriFilter(str_replace("{nickname}",$usernameis,$nalbumm));
  $estrazione="SELECT ID FROM sys_albums WHERE Owner=".$ottieniID." AND Uri='".$nomealbum."'";
  $esegui=mysql_query($estrazione);
  
  $ispresent=mysql_num_rows($esegui);
  if ($ispresent>0)
  {
  
	$info=mysql_fetch_assoc($esegui);
  $idalbum=$info['ID'];
  $estraziones="SELECT sys_albums_objects.id_object, sys_albums.Caption , bx_photos_main.ID , bx_photos_main.Hash,bx_photos_main.Ext FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID = sys_albums_objects.id_album)
                INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID=".$idalbum." ORDER BY ID ASC LIMIT 1";
  $eseguis=mysql_query($estraziones);
	$contafotoalbum=mysql_num_rows($eseguis);
  }
  else $contafotoalbum=0;
  
  if ($contafotoalbum>0) 
  {
   $fetchfotopredefinita=mysql_fetch_assoc($eseguis);
   echo '<img class="ibdwmainPic" src="'.BX_DOL_URL_ROOT.'m/photos/get_image/file/'.$fetchfotopredefinita['Hash'].'.jpg">';
  }
	else
	{
	 if ($infoprof['Couple']<>"0") echo '<img class="ibdwmainPic" src="'.BX_DOL_URL_ROOT.'modules/ibdw/megaprofile/templates/base/images/couple.gif">';
	 elseif ($infoprof['Sex']=="female") echo '<img class="ibdwmainPic" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/woman_big.gif">';
	 else echo '<img class="ibdwmainPic" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/man_big.gif"></a>';
	}
 }
}
if($ottieniID == $IDmio) 
{echo "</div>";}
?>
</div></div>
<div id="loader"></div>
<div id="ospite"></div>
<?php
//BLOCCO MENU
echo '<div class="profile_menu">';
if ($IDmio==$ottieniID)
{
 if($photoview=='ON') 
 {
  if($attivaintegrazione == 0) echo '<a href="'.BX_DOL_URL_ROOT.'m/photos/albums/my/main/" class="profile_menu_link">'._t("_ibdw_mp_photofme").'</a>';
  else echo '<a href="'.BX_DOL_URL_ROOT.'page/photodeluxe?profileID='.$ottieniID.'" class="profile_menu_link">'._t("_ibdw_mp_photofme").'</a>';
 }
 if($videoview=='ON') echo '<a href="'.BX_DOL_URL_ROOT.'m/videos/albums/my/main" class="profile_menu_link">'._t("_ibdw_mp_videofme").'</a>';
 if($soundview=='ON') echo '<a href="'.BX_DOL_URL_ROOT.'m/sounds/albums/my/main" class="profile_menu_link">'._t("_ibdw_mp_soundofme").'</a>';
 echo '<a href="'.BX_DOL_URL_ROOT.'pedit.php?ID='.$ottieniID.'" class="profile_menu_link">'._t("_ibdw_mp_editme").'</a>';
 if($custompro=='ON') echo '<script language="javascript" type="text/javascript" src="'.BX_DOL_URL_PLUGINS.'jquery/jquery.form.js"></script><a class="profile_menu_link" onclick="$(\'#profile_customize_page\').fadeIn(\'slow\', function() {dbTopMenuLoad(\'profile_customizer\');});">'._t("_ibdw_mp_customize").'</a>';
}
elseif (isLogged())
{
 echo '<div style="display: none;" id="ajaxy_popup_result_div_'.$ottieniID.'">&nbsp;</div>';
 if($usernamem==0) $NomeProfilo=$usernameis;
 elseif($usernamem==1) $NomeProfilo=ucfirst($infoprof['FirstName'])." ".ucfirst($infoprof['LastName']);
 elseif($usernamem==2) $NomeProfilo=ucfirst($infoprof['FirstName']);
 if($photoview=='ON') 
 { 
  if($attivaintegrazione == 0) echo '<a href="'.BX_DOL_URL_ROOT.'m/photos/albums/browse/owner/'.$usernameis.'" class="profile_menu_link">'.str_replace("{NickName}",$NomeProfilo,_t("_ibdw_mp_photof")).'</a>';
  else echo '<a href="'.BX_DOL_URL_ROOT.'page/photodeluxe?ui='.$usernameis.'&profileID='.$ottieniID.'" class="profile_menu_link">'.str_replace("{NickName}",$NomeProfilo,_t("_ibdw_mp_photof")).'</a>';
 }
 if($videoview=='ON') echo '<a href="'.BX_DOL_URL_ROOT.'m/videos/albums/browse/owner/'.$usernameis.'" class="profile_menu_link">'.str_replace("{NickName}",$NomeProfilo,_t("_ibdw_mp_videof")).'</a>';
 if($soundview=='ON') echo '<a href="'.BX_DOL_URL_ROOT.'m/sounds/albums/browse/owner/'.$usernameis.'" class="profile_menu_link">'.str_replace("{NickName}",$NomeProfilo,_t("_ibdw_mp_soundof")).'</a>';
 if($sendmessage=='ON') echo '<a href="'.BX_DOL_URL_ROOT.'mail.php?mode=compose&recipient_id='.$ottieniID.'" class="profile_menu_link">'._t("_ibdw_mp_sendmsg").'</a>';
 if($favepro=='ON') 
 {
 ?>
 <a href="" onclick="$.post('list_pop.php?action=hot', { ID: '<?php echo $ottieniID;?>' }, function(sData){ $('#ajaxy_popup_result_div_<?php echo $ottieniID;?>').html(sData) } );return false;" class="profile_menu_link"><?php echo _t("_ibdw_mp_fave");?></a>
 <?php 
 } 
 
 if($greetingview=='ON' AND $versionedolmain=='0' AND $versionedol<'5') 
 { 
 ?><a href="" onclick="getHtmlData('ajaxy_popup_result_div_<?php echo $ottieniID;?>', 'greet.php?sendto=<?php echo $ottieniID;?>&mode=ajax');return false;" class="profile_menu_link"><?php echo _t("_ibdw_mp_greetings");?></a>
 <?php
 }
 elseif ($greetingview=='ON') 
 { ?> 
 <a href="" onclick="$.post('greet.php', { sendto: '<?php echo $ottieniID;?>' }, function(sData){ $('#ajaxy_popup_result_div_<?php echo $ottieniID;?>').html(sData) } );return false;" class="profile_menu_link"><?php echo _t("_ibdw_mp_greetings");?></a> 
 <?php 
 }
 //Standard Report Tool
 if($reportspam=='ON' AND $versionedolmain=='0' AND $versionedol<'5' AND $reportspamtool=='0') 
 { 
 ?>
 <a href="" onclick="getHtmlData('ajaxy_popup_result_div_<?php echo $ottieniID;?>', 'list_pop.php?action=spam&ID=<?php echo $ottieniID;?>&mode=ajax');return false;" class="profile_menu_link"><?php echo _t("_ibdw_mp_spamrep");?></a>
 <?php
 }
 elseif ($reportspam=='ON' AND $reportspamtool=='0') { 
 ?>
<a href="" onclick="$.post('list_pop.php?action=spam', { ID: '<?php echo $ottieniID;?>' }, function(sData){ $('#ajaxy_popup_result_div_<?php echo $ottieniID;?>').html(sData) } );return false;" class="profile_menu_link"><?php echo _t("_ibdw_mp_spamrep");?></a> 
 <?php
 }
 elseif($reportspam=='ON' AND $reportspamtool=='1') 
 {//Modzzz Report Tool 
 ?>
 <a href="javascript:showPopupAnyHtml (site_url+'m/report/mark_report/Profile/<?php echo $ottieniID;?>');" class="profile_menu_link"><?php echo _t("_ibdw_mp_spamrep");?></a>
 <?php
 }
 if($befriend == 'ON') 
 { 
  if (!is_friends($ottieniID,$IDmio))
  {
   //verifico se l'altro utente mi ha fatto una richiesta d'amicizia
   $esegui="SELECT ID,Profile FROM sys_friend_list WHERE ID=".$IDmio." AND Profile=".$ottieniID." AND sys_friend_list.Check=0";
   $parti=mysql_query($esegui);
   $numero=mysql_num_rows($parti);
   if($numero!=0) echo '<div id="messaggioautomatico"><div class="loaderajax"></div>'._t("_ibdw_mp_errorfriend").'</div>';
   else echo '<div id="messaggioautomatico"><div class="loaderajax"></div>'._t("_ibdw_mp_friendrequ").'</div>';
 ?>
 <a href="javascript:aggiungiamico();"  class="profile_menu_link"><?php echo _t("_ibdw_mp_befriend");?></a>
 <script>
 function aggiungiamico() 
 { 
  $("#messaggioautomatico").fadeIn();
  window.setTimeout("aggiungiexe()", 2000);
 }
 function aggiungiexe() 
 { 
  $.ajax({type: "POST", url: "modules/ibdw/megaprofile/azioni.php", data: "id=<?php echo $IDmio;?>&profile=<?php echo $ottieniID;?>&tipo=aggiungi",
  success: function(msg) {aggiornamentoajax_profileid(<?php echo $ottieniID;?>);} });
 }
 </script>
 <?
  }
  if (is_friends($ottieniID,$IDmio))
  {
  ?>
   <a href="javascript:eliminaamico();" class="profile_menu_link"><?php echo _t("_ibdw_mp_removefriend");?></a>
   <script>
   function eliminaamico() 
   { 
    $.ajax({type: "POST", url: "modules/ibdw/megaprofile/azioni.php", data: "id=<?php echo $IDmio;?>&profile=<?php echo $ottieniID;?>&tipo=delete",
    success: function(msg) {aggiornamentoajax_profileid(<?php echo $ottieniID;?>);} });
   }
   </script>
  <?
  }
 }
 function bloccato($io, $lui)
 {
  $sQueryblocco = "SELECT COUNT(*) FROM sys_block_list WHERE ID=".$io." AND Profile=".$lui;  
  return db_value($sQueryblocco) ? true : false; 
 }
 function favorito($io, $lui)
 {
  $sQueryfave = "SELECT COUNT(*) FROM sys_fave_list WHERE ID=".$io." AND Profile=".$lui;  
  return db_value($sQueryfave) ? true : false; 
 }
 if($blockview=='ON') 
 { 
  if (!bloccato($IDmio,$ottieniID))
  {
  ?>
  <a href="javascript:bloccaamico();" class="profile_menu_link"><?php echo _t("_ibdw_mp_block");?></a>
  <script>
  function bloccaamico() 
  { 
   $.ajax({ type: "POST", url: "modules/ibdw/megaprofile/azioni.php", data: "id=<?php echo $IDmio;?>&profile=<?php echo $ottieniID;?>&tipo=blocca",
   success: function(msg) {aggiornamentoajax_profileid(<?php echo $ottieniID;?>);} });
  }
  </script>
  <?
  }
  elseif (bloccato($IDmio,$ottieniID))
  {
  ?>
  <a href="javascript:sbloccaamico();" class="profile_menu_link"><?php echo _t("_ibdw_mp_unblock");?></a>
  <script>
  function sbloccaamico() 
  { 
   $.ajax({ type: "POST", url: "modules/ibdw/megaprofile/azioni.php", data: "id=<?php echo $IDmio;?>&profile=<?php echo $ottieniID;?>&tipo=sblocca",
   success: function(msg) {aggiornamentoajax_profileid(<?php echo $ottieniID;?>);} });
  }
  </script>
  <?
  }
 }
 if($subscribeview=='ON') 
 {
  function sottoscritto($io, $lui)
  {
   $sQuerysubsc="SELECT COUNT(*) FROM sys_sbs_entries WHERE subscriber_id=".$io." AND object_id=".$lui. " AND (subscription_id=3 OR subscription_id=4 OR subscription_id=5)";  
   return db_value($sQuerysubsc) ? true : false; 
  }
  if (!sottoscritto($IDmio,$ottieniID))
  {
  ?>
   <a onclick="oBxDolSubscription.subscribe(<?php echo $IDmio;?>, 'profile', '',<?php echo $ottieniID;?>);aggiornamentoajax_profileid(<?php echo $ottieniID;?>);" class="profile_menu_link"><?php echo _t("_ibdw_mp_subs");?></a>
  <?
  }
  elseif (sottoscritto($IDmio,$ottieniID))
  {
  ?>
   <a onclick="oBxDolSubscription.unsubscribe(<?php echo $IDmio;?>, 'profile', '',<?php echo $ottieniID;?>);aggiornamentoajax_profileid(<?php echo $ottieniID;?>);" class="profile_menu_link"><?php echo _t("_ibdw_mp_unsubs");?></a>
  <?
  }
 }
//parentesi che chiude l'else del menu per altro profilo 
}
//SUPPORTO LINK AGGIUNTIVI TERZEPARTI PER ALTRI PROFILI
include BX_DIRECTORY_PATH_MODULES.'ibdw/megaprofile/extralink.php';
echo '</div>';

//AREA SIMPLE MESSENGER
if (get_user_online_status($ottieniID)==1 and $ottieniID<>$IDmio and get_user_online_status($IDmio))
{
 $okvista=0;
 //abilito la vista se è un amico e l'abilitazione è per gli amici
 if ($typeofallowed==1)
 {
  $querydiverifica="SELECT ID FROM Profiles WHERE ID=".$ottieniID." AND (id IN (select id from sys_friend_list where Profile=$IDmio AND sys_friend_list.Check=1) OR id IN (select Profile from sys_friend_list where id=$IDmio AND sys_friend_list.Check=1))";
  $controllase = mysql_query($querydiverifica);
  $yesornot=mysql_num_rows($controllase);
  if ($yesornot>0) $okvista=1;
 }
 //abilito la vista se è un membro confermato e l'abilitazione è per i membri confermati
 elseif ($typeofallowed==2)
 {
  $querydiverifica="SELECT ID FROM Profiles WHERE ID=".$ottieniID." AND Profiles.Status='active'";
  $controllase = mysql_query($querydiverifica);
  $yesornot=mysql_num_rows($controllase);
  if ($yesornot>0) $okvista=1;
 }
 //echo $querydiverifica." <br><br>numero: ".$yesornot;
 if ($okvista==1)
 {
  ?>   
  <div style="margin-top:10px;"></div>
  <div class="spacediv">
  <div class="form_advanced_table simple_messenger_chat_block_tbl"><div class="input_wrapper input_wrapper_text simple_messenger_chat_wrapper">
   <?php
   if ($versionedolmain=="0")
   {
   ?>
   <div class="field_design"><img width="14" height="12" src="<?php echo BX_DOL_URL_ROOT?>modules/boonex/simple_messenger/templates/base/images/icons/message.png"></div>
   <?
   }
   else
   {
   ?>
   <div class="field_design"><i class="sys-icon comments-alt"></i></div>
   <?
   }
   ?>
   <input type="text" onkeyup="if ( typeof oSimpleMessenger != 'undefined' ){oSimpleMessenger.sendMessage(event, this, <?php echo $ottieniID;?>)}" onclick="this.value=''" value="<?php echo str_replace('{ProfileName}',$NomeProfilo,_t('_ibdw_mp_chatwith'));?>..." name="status_message" class="form_input_text">
   <div class="input_close input_close_text"></div>
  </div>
  </div>
  <div class="clear_both"></div>
  </div>
  <?
 }
}

//FINE BLOCCO MENU
$campi= "DescriptionMe";
if ($religionview == "ON") $campi=$campi.", Religion";
if ($occupationview == "ON") $campi=$campi.", Occupation";
if ($emailview == "ON") $campi=$campi.", Email";
if ($lookingforview == "ON") $campi=$campi.", LookingFor";
if ($sexview == "ON") $campi=$campi.", Sex";
if ($headlineview == "ON") $campi=$campi.",Headline";
if ($infocityview == "ON") $campi=$campi.",City,Country";
if ($datebirthview == "ON") $campi=$campi.",DateOfBirth";
if ($relstatusview == "ON") $campi=$campi.",RelationshipStatus";
$campi=$campi.",Couple";

$queryinfoprofilos="SELECT ".$campi." FROM Profiles WHERE ID=".$ottieniID;
$risultinfo=mysql_query($queryinfoprofilos);
$infoprof=mysql_fetch_assoc($risultinfo);
if ($infoprof['Country']<>"")
{
 $paese="SELECT LKey FROM sys_pre_values WHERE sys_pre_values.Key='Country' AND sys_pre_values.Value='".$infoprof['Country']."'";
 $rispaese=mysql_query($paese);
 $infopaese=mysql_fetch_assoc($rispaese);
}
if ($formatodata=="0")
{
 $datanascita=date('j F, Y', strtotime($infoprof['DateOfBirth']));
 $compleanno=date('j F', strtotime($infoprof['DateOfBirth']));
}
elseif ($formatodata=="1")
{
 $datanascita=date('F j, Y', strtotime($infoprof['DateOfBirth']));
 $compleanno=date('F j', strtotime($infoprof['DateOfBirth']));
}
$datanascita=str_replace("January",_t("_January"),$datanascita);
$datanascita=str_replace("February",_t("_February"),$datanascita);
$datanascita=str_replace("March",_t("_March"),$datanascita);
$datanascita=str_replace("April",_t("_April"),$datanascita);
$datanascita=str_replace("May",_t("_May"),$datanascita);
$datanascita=str_replace("June",_t("_June"),$datanascita);
$datanascita=str_replace("July",_t("_July"),$datanascita);
$datanascita=str_replace("August",_t("_August"),$datanascita);
$datanascita=str_replace("September",_t("_September"),$datanascita);
$datanascita=str_replace("October",_t("_October"),$datanascita);
$datanascita=str_replace("November",_t("_November"),$datanascita);
$datanascita=str_replace("December",_t("_December"),$datanascita);
$compleanno=str_replace("January",_t("_January"),$compleanno);
$compleanno=str_replace("February",_t("_February"),$compleanno);
$compleanno=str_replace("March",_t("_March"),$compleanno);
$compleanno=str_replace("April",_t("_April"),$compleanno);
$compleanno=str_replace("May",_t("_May"),$compleanno);
$compleanno=str_replace("June",_t("_June"),$compleanno);
$compleanno=str_replace("July",_t("_July"),$compleanno);
$compleanno=str_replace("August",_t("_August"),$compleanno);
$compleanno=str_replace("September",_t("_September"),$compleanno);
$compleanno=str_replace("October",_t("_October"),$compleanno);
$compleanno=str_replace("November",_t("_November"),$compleanno);
$compleanno=str_replace("December",_t("_December"),$compleanno);

//BLOCCO DESCRIZIONE
if($descriptionview == 'ON' and $infoprof['Couple']=="0") 
{
 if ($infoprof['DescriptionMe']<>"")
 {
  //text to display as preview
  $descrizionemod0=cleardescriptiondisplayed($infoprof['DescriptionMe']);
  //text for the readmore value
  $descrizionemod0readmore=stringtosendtojava($descrizionemod0);
  echo '<div id="ibdw_text_container0" class="ibdw_text_container">';
  if($ottieniID==$IDmio) echo '<div id="ibdwareap0" class="ibdwareap"><div id="ibdw_text0" onclick="editmon(0);" style="display: block;" class="ibdw_text">'.TagliaStringa($descrizionemod0, $maxlunghdesc).'</div>'; 
  else echo '<div id="ibdwareap0" class="ibdwareap1"><div id="ibdw_text0" style="display: block;" class="ibdw_textother">'.TagliaStringa($descrizionemod0, $maxlunghdesc).'</div>';
  echo '</div></div>';
  if($ottieniID==$IDmio) 
  {
   echo '<div id="deskedits0" class="deskedits">
   <textarea id="deskup0" class="areadeskedit">'.str_replace("ecommerciale","&",str_replace("<br />","\n",str_replace("<br>","\n",$descrizionemod0))).'</textarea>
   <div id="editinfocont"><div id="saveb0" class="saveb" onclick="update('.$IDmio.',0);">'. _t("_ibdw_mp_salva").'</div>
   <div id="saven0" class="saven" onclick="noedit(0);">'. _t("_ibdw_mp_annulla").'</div></div>
   </div>';
  }
  else
  {
   if (strlen($descrizionemod0)>$maxlunghdesc) echo '<div id="ibdw_readmore0" class="ibdw_readmore" onclick="iampra(0,\''.urlencode($descrizionemod0readmore).'\');">'. _t("_ibdw_mp_readmore").'</div>';
   echo '<div id="ibdw_cllpse0" class="ibdw_cllpse" onclick="disampra(0,\''.urlencode(str_replace("&#39;","\'",TagliaStringa($descrizionemod0, $maxlunghdesc))).'\');" style="display:none;">'. _t("_ibdw_mp_collapse").'</div>';
  }
 }
}
//FINE BLOCCO DESCRIZIONE

//BLOCCO INFORMAZIONI
if($informationview=='ON')
{
 if ($infoprof['RelationshipStatus']<>"" or $infoprof['DateOfBirth']<>"" or $infoprof['City']<>"" or $infoprof['Headline']<>"" or $infoprof['Email']<>"" or $infoprof['Sex']<>"" or $infoprof['LookingFor']<>"" or $infoprof['Occupation']<>"" or $infoprof['Religion']<>"")
 {
  if ($infoprof['Couple']<>"0")
  {
   //SE IL PROFILO APPARTIENE AD UNA COPPIA ALLORA RICAVO LE INFORMAZIONI SULL ALTRO PROFILO
   $queryinfoprofilos2="SELECT ".$campi." FROM Profiles WHERE ID=".$infoprof['Couple'];
   $risultinfo2=mysql_query($queryinfoprofilos2);
   $infoprof2=mysql_fetch_assoc($risultinfo2);
   if ($infoprof2['Country']<>"")
   {
    $paese2="SELECT LKey FROM sys_pre_values WHERE sys_pre_values.Key='Country' AND sys_pre_values.Value='".$infoprof2['Country']."'";
    $rispaese2=mysql_query($paese2);
    $infopaese2=mysql_fetch_assoc($rispaese2);
   }
   if ($formatodata=="0")
   {
    $datanascita2=date('j F, Y', strtotime($infoprof2['DateOfBirth']));
	  $compleanno2=date('j F', strtotime($infoprof['DateOfBirth']));
   }
   elseif ($formatodata=="1")
   {
    $datanascita2=date('F j, Y', strtotime($infoprof2['DateOfBirth']));
	  $compleanno2=date('F j', strtotime($infoprof['DateOfBirth']));
   }
   $datanascita2=str_replace("January",_t("_January"),$datanascita2);
   $datanascita2=str_replace("February",_t("_February"),$datanascita2);
   $datanascita2=str_replace("March",_t("_March"),$datanascita2);
   $datanascita2=str_replace("April",_t("_April"),$datanascita2);
   $datanascita2=str_replace("May",_t("_May"),$datanascita2);
   $datanascita2=str_replace("June",_t("_June"),$datanascita2);
   $datanascita2=str_replace("July",_t("_July"),$datanascita2);
   $datanascita2=str_replace("August",_t("_August"),$datanascita2);
   $datanascita2=str_replace("September",_t("_September"),$datanascita2);
   $datanascita2=str_replace("October",_t("_October"),$datanascita2);
   $datanascita2=str_replace("November",_t("_November"),$datanascita2);
   $datanascita2=str_replace("December",_t("_December"),$datanascita2);
   $compleanno2=str_replace("January",_t("_January"),$compleanno2);
   $compleanno2=str_replace("February",_t("_February"),$compleanno2);
   $compleanno2=str_replace("March",_t("_March"),$compleanno2);
   $compleanno2=str_replace("April",_t("_April"),$compleanno2);
   $compleanno2=str_replace("May",_t("_May"),$compleanno2);
   $compleanno2=str_replace("June",_t("_June"),$compleanno2);
   $compleanno2=str_replace("July",_t("_July"),$compleanno2);
   $compleanno2=str_replace("August",_t("_August"),$compleanno2);
   $compleanno2=str_replace("September",_t("_September"),$compleanno2);
   $compleanno2=str_replace("October",_t("_October"),$compleanno2);
   $compleanno2=str_replace("November",_t("_November"),$compleanno2);
   $compleanno2=str_replace("December",_t("_December"),$compleanno2);
   echo '<div class="summaryinfo"><h3 class="io">'._t("_ibdw_mp_firstinfos").'</h3><div class="dentro">';
  }
  else echo '<div class="summaryinfo"><h3 class="io">'._t("_ibdw_mp_infos").'</h3><div class="dentro">';
  if ($infoprof['RelationshipStatus']<>"" and $relstatusview == "ON") 
  {
   if ($infoprof['RelationshipStatus']=="Single") $miostatorel=_t("_FieldValues_Single");
   elseif ($infoprof['RelationshipStatus']=="In a Relationship") $miostatorel=_t("_FieldValues_In a Relationship");
   elseif ($infoprof['RelationshipStatus']=="Engaged") $miostatorel=_t("_FieldValues_Engaged");
   elseif ($infoprof['RelationshipStatus']=="Married") $miostatorel=_t("_FieldValues_Married");
   elseif ($infoprof['RelationshipStatus']=="In an Open Relationship") $miostatorel=_t("_FieldValues_In an Open Relationship");
   elseif ($infoprof['RelationshipStatus']=="It's Complicated") $miostatorel=_t("_FieldValues_It's Complicated");
   else $miostatorel=_t("_undefined");
   echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_relst").'</span><span class="ztit2">'.$miostatorel.'</span></div>';
  }
  if ($infoprof['DateOfBirth']<>"" and $datebirthview=="ON" and $agestyle=='2' and $infoprof['DateOfBirth']!="0000-00-00" ) echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_brt").'</span><span class="ztit2">'.$datanascita.'</span></div>';
  if ($infoprof['DateOfBirth']<>"" and $datebirthview=="ON" and $agestyle=='3' and $infoprof['DateOfBirth']!="0000-00-00" ) echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_bd").'</span><span class="ztit2">'.$compleanno.'</span></div>';
  if ($infoprof['DateOfBirth']<>"" and $datebirthview=="ON" and $agestyle=='1' and $infoprof['DateOfBirth']!="0000-00-00" ) 
  {
   $databirth=$infoprof['DateOfBirth'];
   $esplosione=explode('-',$databirth);
   // Mia data di nascita 
   $giorno=$esplosione[2];
   $mese=$esplosione[1];
   $anno=$esplosione[0];
   // Differenza in secondi tra la data corrente e la mia data di nascita
   $differenza=time()-mktime(0,0,0,$mese,$giorno,$anno);
   // Calcolo età
   $eta=floor($differenza/31536000);
   echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_age").'</span><span class="ztit2">'.$eta.' '._t("_ibdw_mp_age2").'</span></div>';
  }
  if ($infoprof['City']<>"" and $infocityview=="ON") echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_city").'</span><span class="ztit2">'.$infoprof['City'].', '._t($infopaese['LKey']).'</span></div>';
  if ($infoprof['Headline']<>"" and $headlineview == "ON") echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_headline").'</span><span class="ztit2">'.$infoprof['Headline'].'</span></div>';
  if($infoprof['Email']<>"" and $emailview == 'ON') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_email").'</span><span class="ztit2">'.$infoprof['Email'].'</span></div>'; 
  if($infoprof['Sex']<>"" and $sexview == 'ON') 
  {
   if ($infoprof['Sex']=='male') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_sex").'</span><span class="ztit2">'._t("_ibdw_mp_sexmale").'</span></div>';
   elseif ($infoprof['Sex']=='female') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_sex").'</span><span class="ztit2">'._t("_ibdw_mp_sexfemale").'</span></div>';
  } 
  if($infoprof['LookingFor']<>"" and $lookingforview=='ON') 
  {
   if ($infoprof['LookingFor']=='male,female') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_lookingfor").'</span><span class="ztit2">'._t("_ibdw_mp_lookingformalefemale").'</span></div>';
   elseif ($infoprof['LookingFor']=='male') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_lookingfor").'</span><span class="ztit2">'._t("_ibdw_mp_lookingformale").'</span></div>';
   elseif ($infoprof['LookingFor']=='female') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_lookingfor").'</span><span class="ztit2">'._t("_ibdw_mp_lookingforfemale").'</span></div>';
  } 
  if ($infoprof['Occupation']<>"" and $occupationview=="ON") echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_occupation").'</span><span class="ztit2">'.$infoprof['Occupation'].'</span></div>';
  if ($infoprof['Religion']<>"" and $religionview=="ON") echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_religion").'</span><span class="ztit2">'.$infoprof['Religion'].'</span></div>';
  echo '</div></div>';
  
  //INFORMAZIONI PER IL SECONDO PROFILO QUANDO COPPIA
  if ($infoprof['Couple']<>"0")
  { 
   //BLOCCO DESCRIZIONE PROFILO 1
   if($descriptionview=='ON') 
   {
    if ($infoprof['DescriptionMe']<>"")
    {
	  //text to display as preview
    $descrizionemod1=cleardescriptiondisplayed($infoprof['DescriptionMe']);
    //text for the readmore value
    $descrizionemod1readmore=stringtosendtojava($descrizionemod1);
	  echo '<div class="giu"></div><div id="ibdw_text_container1" class="ibdw_text_container">';
    if($ottieniID==$IDmio) echo '<div id="ibdwareap1" class="ibdwareap"><div id="ibdw_text1" onclick="editmon(1);" style="display: block;" class="ibdw_text">'.TagliaStringa($descrizionemod1, $maxlunghdesc).'</div>';
    else echo '<div id="ibdwareap1" class="ibdwareap1"><div id="ibdw_text1" style="display: block;" class="ibdw_text">'.TagliaStringa($descrizionemod1, $maxlunghdesc).'</div>';
    echo '</div></div>';
    if($ottieniID==$IDmio) 
    {    
     echo '<div id="deskedits1" class="deskedits"><textarea id="deskup1" class="areadeskedit">'.str_replace("ecommerciale","&",str_replace("<br />","\n",str_replace("<br>","\n",$descrizionemod1))).'</textarea>
           <div id="editinfocont">
            <div id="saveb1" class="saveb" onclick="update('.$IDmio.',1);">'. _t("_ibdw_mp_salva").'</div>
            <div id="saven1" class="saven" onclick="noedit(1);">'. _t("_ibdw_mp_annulla").'</div></div></div>';
    }
    else
    {
     if (strlen($descrizionemod1)>$maxlunghdesc) echo '<div id="ibdw_readmore1" class="ibdw_readmore" onclick="iampra(1,\''.$descrizionemod1readmore.'\');">'. _t("_ibdw_mp_readmore").'</div>';
     echo '<div id="ibdw_cllpse1" class="ibdw_cllpse" onclick="disampra(1,\''.str_replace("&#39;","\'",TagliaStringa($descrizionemod1, $maxlunghdesc)).'\');" style="display:none;">'. _t("_ibdw_mp_collapse").'</div>';
    }
   }
  }
  //FINE BLOCCO DESCRIZIONE PROFILO 1
	echo '<div class="summaryinfo"><h3 class="io">'._t("_ibdw_mp_secondinfos").'</h3><div class="dentro">';
  if ($infoprof2['RelationshipStatus']<>"" and $relstatusview == "ON") 
  {
   if ($infoprof2['RelationshipStatus']=="Single") $miostatore2=_t("_FieldValues_Single");
   elseif ($infoprof2['RelationshipStatus']=="In a Relationship") $miostatore2=_t("_FieldValues_In a Relationship");
   elseif ($infoprof2['RelationshipStatus']=="Engaged") $miostatore2=_t("_FieldValues_Engaged");
   elseif ($infoprof2['RelationshipStatus']=="Married") $miostatore2=_t("_FieldValues_Married");
   elseif ($infoprof2['RelationshipStatus']=="In an Open Relationship") $miostatore2=_t("_FieldValues_In an Open Relationship");
   else $miostatore2=_t("_FieldValues_It's Complicated");
   echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_relst").'</span><span class="ztit2">'.$miostatore2.'</span></div>';
  }
  if ($infoprof2['DateOfBirth']<>"" and $datebirthview == "ON" and $agestyle=='2' and $infoprof2['DateOfBirth']!="0000-00-00") echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_brt").'</span><span class="ztit2">'.$datanascita2.'</span></div>';
  if ($infoprof2['DateOfBirth']<>"" and $datebirthview == "ON" and $agestyle=='3' and $infoprof2['DateOfBirth']!="0000-00-00") echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_bd").'</span><span class="ztit2">'.$datanascita2.'</span></div>';
  if ($infoprof2['DateOfBirth']<>"" and $datebirthview == "ON" and $agestyle=='1' and $infoprof2['DateOfBirth']!="0000-00-00") 
	{
   $databirth=$infoprof2['DateOfBirth'];
   $esplosione=explode('-',$databirth);
   // Mia data di nascita 
   $giorno=$esplosione[2];
   $mese=$esplosione[1];
   $anno=$esplosione[0];
   //Differenza in secondi tra la data corrente e la mia data di nascita
   $differenza=time()-mktime(0,0,0,$mese,$giorno,$anno);
   //Calcolo età
   $eta=floor($differenza/31536000);
   echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_age").'</span><span class="ztit2">'.$eta.' '._t("_ibdw_mp_age2").'</span></div>';
  }
  if ($infoprof2['Headline']<>"" and $headlineview=="ON") echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_headline").'</span><span class="ztit2">'.$infoprof2['Headline'].'</span></div>';
  if($infoprof2['Email']<>"" and $emailview=='ON') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_email").'</span><span class="ztit2">'.$infoprof2['Email'].'</span></div>'; 
  if($infoprof2['Sex']<>"" and $sexview=='ON') 
  {
   if ($infoprof2['Sex']=='male') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_sex").'</span><span class="ztit2">'._t("_ibdw_mp_sexmale").'</span></div>';
   elseif ($infoprof2['Sex']=='female') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_sex").'</span><span class="ztit2">'._t("_ibdw_mp_sexfemale").'</span></div>';
  } 
  if($infoprof2['LookingFor']<>"" and $lookingforview=='ON') 
  {
   if ($infoprof2['LookingFor']=='male,female') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_lookingfor").'</span><span class="ztit2">'._t("_ibdw_mp_lookingformalefemale").'</span></div>';
   elseif($infoprof2['LookingFor']=='male') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_lookingfor").'</span><span class="ztit2">'._t("_ibdw_mp_lookingformale").'</span></div>';
   elseif($infoprof2['LookingFor']=='female') echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_lookingfor").'</span><span class="ztit2">'._t("_ibdw_mp_lookingforfemale").'</span></div>';
  } 
  if ($infoprof2['Occupation']<>"" and $occupationview=="ON") echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_occupation").'</span><span class="ztit2">'.$infoprof2['Occupation'].'</span></div>';
  if ($infoprof2['Religion']<>"" and $religionview=="ON") echo '<div class="rowinfo"><span class="ztit">'._t("_ibdw_mp_religion").'</span><span class="ztit2">'.$infoprof2['Religion'].'</span></div>';
  echo '</div></div>';
	
	//BLOCCO DESCRIZIONE PROFILO 2
  if($descriptionview=='ON') 
  {
   if ($infoprof2['DescriptionMe']<>"")
   {
    //text to display as preview
    $descrizionemod2=cleardescriptiondisplayed($infoprof['DescriptionMe']);
 	  //text for the readmore value
 	  $descrizionemod2readmore=stringtosendtojava($descrizionemod2);
    echo '<div class="giu"></div><div id="ibdw_text_container2" class="ibdw_text_container">';
    if($ottieniID==$IDmio) echo '<div id="ibdwareap2" class="ibdwareap"><div id="ibdw_text2" onclick="editmon(2);" style="display: block;" class="ibdw_text">'.TagliaStringa($descrizionemod2, $maxlunghdesc).'</div>';
    else echo '<div id="ibdwareap2" class="ibdwareap1"><div id="ibdw_text2" style="display: block;" class="ibdw_text">'.TagliaStringa($descrizionemod2, $maxlunghdesc).'</div>';
    echo '</div></div>';
    if($ottieniID==$IDmio) 
    {    
     echo '<div id="deskedits2" class="deskedits">
     <textarea id="deskup2" class="areadeskedit">'.str_replace("ecommerciale","&",str_replace("<br />","\n",str_replace("<br>","\n",$descrizionemod2))).'</textarea>
      <div id="editinfocont">
       <div id="saveb2"  class="saveb" onclick="update('.$infoprof['Couple'].',2);">'. _t("_ibdw_mp_salva").'</div>
       <div id="saven2" class="saven" onclick="noedit(2);">'. _t("_ibdw_mp_annulla").'</div>
      </div>
     </div>';
    }
    else
    {
     if (strlen($descrizionemod2)>$maxlunghdesc) echo '<div id="ibdw_readmore2" class="ibdw_readmore" onclick="iampra(2,\''.$descrizionemod2readmore.'\');">'. _t("_ibdw_mp_readmore").'</div>';
     echo '<div id="ibdw_cllpse2" class="ibdw_cllpse" onclick="disampra(2,\''.str_replace("&#39;","\'",TagliaStringa($descrizionemod2, $maxlunghdesc)).'\');" style="display:none;">'. _t("_ibdw_mp_collapse").'</div>';
    }
   }
  }
  //FINE BLOCCO DESCRIZIONE PROFILO 2
 }
} 
}
//FINE BLOCCO INFORMAZIONI

if($ordinamentomf=="0") $filtroordinemf="p.Avatar DESC";
else $filtroordinemf="RAND()";
//gestione ordinamento amici
if($ordinamentof=="0") $filtroordinef="amico DESC";
else $filtroordinef="RAND()";

//BLOCCO AMICI IN COMUNE
if($mutualfriendview == 'ON' and isLogged()) 
{
 if ($IDmio<>$ottieniID)
 {
  //CONTA GLI AMICI IN COMUNE CON UN ALTRO UTENTE
  function CountMutualFriendsnew ($ioio,$luilui) {
		   $sQuery = "SELECT COUNT(*) 
 	       FROM `Profiles` AS p 
 	       LEFT JOIN `sys_friend_list` AS f1 ON ( f1.`ID` = p.`ID` AND f1.`Profile` = '$luilui' AND `f1`.`Check` =1 ) 
 	       LEFT JOIN `sys_friend_list` AS f2 ON ( f2.`Profile` = p.`ID` AND f2.`ID` = '$luilui' AND `f2`.`Check` =1 ) 
 	       LEFT JOIN `sys_friend_list` AS f11 ON ( f11.`ID` = p.`ID` AND f11.`Profile` = '$ioio' AND `f11`.`Check` =1 ) 
 	       LEFT JOIN `sys_friend_list` AS f22 ON ( f22.`Profile` = p.`ID` AND f22.`ID` = '$ioio' AND `f22`.`Check` =1 ) 
 	       WHERE 1 AND (f1.`ID` IS NOT NULL OR f2.`ID` IS NOT NULL) AND (f11.`ID` IS NOT NULL OR f22.`ID` IS NOT NULL)";
		return (int)db_value($sQuery);
	}
  
  $numeroamiciincomune=CountMutualFriendsnew($IDmio,$ottieniID);
  if ($numeroamiciincomune>0)
  {  
   echo '<div class="summaryinfo"><div class="io"><div class="menuname">'._t("_ibdw_mp_mutual").'<div class="contamici">'.$numeroamiciincomune.' '._t("_ibdw_mp_mutual2").'</div></div></div>';
   $sQuerytrovacomuni = "SELECT p.ID AS friendID , p.NickName FROM Profiles AS p INNER JOIN (SELECT IF( '".$ottieniID."' = f.ID , f.Profile , f.ID ) AS ID FROM sys_friend_list AS f 
   WHERE 1 AND (f.Profile = '".$ottieniID."' OR f.ID = '".$ottieniID."') AND f.Check = 1) AS f1 ON (f1.ID = p.ID) INNER JOIN (SELECT IF( '".$IDmio."' = f.ID , f.Profile , f.ID ) AS ID 
   FROM sys_friend_list AS f WHERE 1 AND (f.Profile = '".$IDmio."' OR f.ID = '".$IDmio."') AND f.Check = 1) AS f2 ON (f2.ID = p.ID) WHERE p.ID IN (SELECT ID From Profiles) ORDER BY ".$filtroordinemf;

   $resmutualfriend = mysql_query($sQuerytrovacomuni);
   echo '<div id="avacontainer">';
   for($contamutual=0;$contamutual<min($maxnumbermutualfriends,$numeroamiciincomune);$contamutual++)
   {
	  echo '<div class="singoloava">';
	  $listaamicicomuni=mysql_fetch_array($resmutualfriend);
	  $amicocomune= $listaamicicomuni[0];
	  $infoamicocom=getProfileInfo($amicocomune);
    if($usernamem==0) $NomeAmicoCom=$infoamicocom['NickName'];
   	if($usernamem==1) $NomeAmicoCom=ucfirst($infoamicocom['FirstName'])." ".ucfirst($infoamicocom['LastName']);
   	if($usernamem==2) $NomeAmicoCom=ucfirst($infoamicocom['FirstName']); 

    if ($thumbtype==0) 
    {
     if ($infoamicocom['Avatar']<>"0") echo '<a href="'.getProfileLink($infoamicocom['ID']).'" title="'.$NomeAmicoCom.'"><img class="favatar" src="'.BX_AVA_URL_USER_AVATARS.$infoamicocom['Avatar'].BX_AVA_EXT.'"></a>';
     else
	   {
	    if ($infoamicocom['Sex']=="female") echo '<a href="'.getProfileLink($infoamicocom['ID']) . '" title="'.$NomeAmicoCom.'"><img class="favatar" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/woman_medium.gif"></a>';
      else echo '<a href="'.getProfileLink($infoamicocom['ID']).'" title="'.$NomeAmicoCom.'"><img class="favatar" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/man_medium.gif"></a>';
	   }
    }
    else 
    {
     $Miniaturaamicocom = get_member_thumbnail($infoamicocom['ID'], 'none', false);
     echo $Miniaturaamicocom;
    }
  
	  if ($showname=="ON") echo '<a class="nlung" href="'.getProfileLink($infoamicocom['ID']) . '" title="'.$NomeAmicoCom.'">'.$NomeAmicoCom.'</a>';
    echo '</div>';
   }				
   echo '</div></div>';
  }
 }
}
//FINE BLOCCO AMICI IN COMUNE

//BLOCCO AMICI
//numero degli amici
if($friendview == 'ON') 
{
 $querynum = "SELECT amico FROM ((select ID as amico from sys_friend_list WHERE Profile=".$ottieniID." AND ID IN (SELECT ID From Profiles) AND sys_friend_list.Check=1)
 UNION (select Profile as amico from sys_friend_list WHERE ID=".$ottieniID." AND Profile IN (SELECT ID From Profiles) AND sys_friend_list.Check=1)) as miatabella ORDER BY ".$filtroordinef;
 $resultsfriend = mysql_query($querynum);
 $contagliamici = mysql_num_rows($resultsfriend);
 if($contagliamici>0)
 {
  echo '<div class="summaryinfo"><div class="io"><div class="menuname">'._t("_ibdw_mp_friends").'<div class="amdx"><a href="viewFriends.php?iUser='.$ottieniID.'" class="linkallf">'._t("_ibdw_mp_all").'</a></div><div class="contamici">'.$contagliamici;
  if ($contagliamici==1) echo ' '._t("_ibdw_mp_friends2");
  else echo ' '._t("_ibdw_mp_friends3");
  echo '</div></div></div><div id="avacontainer">';
  for($contatore=0;$contatore<min($maxnumberfriends,$contagliamici);$contatore++)
  {
   echo '<div class="singoloava">';
   $listaamici=mysql_fetch_array($resultsfriend);
   if ($listaamici[0]<>$ottieniID) {$amico= $listaamici[0];}
   else {$amico= $listaamici[1];}
   $infoamico=getProfileInfo($amico);
   if($usernamem==0) $NomeAmico=$infoamico['NickName'];
   if($usernamem==1) $NomeAmico=ucfirst($infoamico['FirstName'])." ".ucfirst($infoamico['LastName']);
   if($usernamem==2) $NomeAmico=ucfirst($infoamico['FirstName']);

   if ($thumbtype==0) 
   {
    if ($infoamico['Avatar']<>"0") echo '<a href="'.getProfileLink($infoamico['ID']) . '" title="'.$NomeAmico.'"><img class="favatar" src="'.BX_AVA_URL_USER_AVATARS.$infoamico['Avatar'].BX_AVA_EXT.'"></a>';
    else 
    {
	   if ($infoamico['Sex']=="female") echo '<a href="'.getProfileLink($infoamico['ID']) . '" title="'.$NomeAmico.'"><img class="favatar" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/woman_medium.gif"></a>';
	   else echo '<a href="'.getProfileLink($infoamico['ID']).'" title="'.$NomeAmico. '"><img class="favatar" src="'.BX_DOL_URL_ROOT.'templates/base/images/icons/man_medium.gif"></a>';
    }
   }
   else 
   {
    $Miniaturaamico = get_member_thumbnail($infoamico['ID'], 'none', false);
    echo $Miniaturaamico;
   }

   if ($showname=="ON")	echo '<a class="nlung" href="'.getProfileLink($infoamico['ID']) . '" title="'.$NomeAmico.'">'.$NomeAmico.'</a>';
   echo '</div>';
  }
  echo '</div></div>';
 }
}
//FINE BLOCCO AMICI

if (isLogged())
{
 //verifichiamo se il sender è nella lista dei favoriti. La query restituisce 1 se l'utente ha inserito il mio profilo nella lista dei suoi favoriti
 $queryfave="SELECT count(*) FROM sys_fave_list WHERE id=".$ottieniID. " AND Profile=".$IDmio;
 $resultqfave = mysql_query($queryfave) or die(mysql_error());
 $num_faves = mysql_fetch_row($resultqfave) or die(mysql_error());
 $num_fave=$num_faves[0];
 //resetto la variabile che decide se il contenuto va mostrato o no
}
//SETTO A ZERO LE VARIABILI UTILI PER CAPTARE LA PRIVACY PREDEFINITA PER VIDEO E FOTO DEL PROFILO
$okf='no';
$okv='no';
//DEFAULT PRIVACY PER LE FOTO DI QUESTO UTENTE
$privdefault="select group_id from sys_privacy_defaults inner join sys_privacy_actions on sys_privacy_defaults.action_id=sys_privacy_actions.id where sys_privacy_actions.module_uri='photos' and sys_privacy_defaults.owner_id=".$ottieniID;
$resultdefault = mysql_query($privdefault);
$rowdefprivf = mysql_fetch_row($resultdefault);
//se la privacy predefinita è 3,4,5 o 6 (ovvero public,members o amici ed in quest ultimo caso verifico se siamo amici oppure io sono nella lista dei favoriti di questo profilo)
if (($rowdefprivf[0]==5 and is_friends($ottieniID,$IDmio)) OR ($rowdefprivf[0]==3) OR ($rowdefprivf[0]==4) OR ($rowdefprivf[0]==6 and $num_fave==1) OR ($ottieniID==$IDmio)) 
{
 $okf='si';
}
elseif($rowdefpriv[0]=="") 
{
 //get the default site privacy for a specific type of content
 $getdefaultvalue="SELECT default_group FROM sys_privacy_actions WHERE module_uri='photos'";
 $resultdefault = mysql_query($getdefaultvalue);
 $rowdefpriv = mysql_fetch_row($resultdefault);
 if ((((($rowdefpriv[0]==5 and is_friends($ottieniID,$IDmio)) OR ($rowdefpriv[0]==6 and $num_fave==1)) OR ($rowdefpriv[0]==3)) OR ($rowdefpriv[0]==4)) OR ($ottieniID==$IDmio)) 
 {
  $okf='si';
 }
 else $okf='no';
}
else
{
 //get the id of the custom privacy level for the sender
 $querycustom="SELECT id FROM sys_privacy_groups WHERE owner_id=".$ottieniID;
 $getcustomid=mysql_query($querycustom);
 $getnump= mysql_num_rows($getcustomid);
 if ($getnump>0) 
 {
  $getidcustomprivacy=mysql_fetch_assoc($getcustomid);
  $verifiam="SELECT ID FROM Profiles WHERE ID=".$IDmio." AND ID IN (SELECT member_id FROM sys_privacy_members WHERE group_id=".$getidcustomprivacy['id'].")";
  $getifiam=mysql_query($verifiam);
  $numberisoneifiam=mysql_num_rows($getifiam);
  if ($numberisoneifiam==1) $okf='si';
  else $okf='no';
 }
 else $okf='no';
}

//DEFAULT PRIVACY PER I VIDEO DI QUESTO UTENTE
$privdefault="select group_id from sys_privacy_defaults inner join sys_privacy_actions on sys_privacy_defaults.action_id=sys_privacy_actions.id where sys_privacy_actions.module_uri='videos' and sys_privacy_defaults.owner_id=".$ottieniID;
$resultdefault = mysql_query($privdefault);
$rowdefprivv = mysql_fetch_row($resultdefault);
//se la privacy predefinita è 3,4 o 5 (ovvero public,members o amici ed in quest ultimo caso verifico se siamo amici)
if (($rowdefprivv[0]==5 and is_friends($ottieniID,$IDmio)) OR ($rowdefprivv[0]==3) OR ($rowdefprivv[0]==4) OR ($rowdefprivv[0]==6 and $num_fave==1) OR ($ottieniID==$IDmio)) $okv='si';
elseif($rowdefprivv[0]=="") 
  {
   //get the default site privacy for a specific type of content
   $getdefaultvalue="SELECT default_group FROM sys_privacy_actions WHERE module_uri='videos'";
   $resultdefault = mysql_query($getdefaultvalue);
   $rowdefprivv = mysql_fetch_row($resultdefault);
   if ((((($rowdefprivv[0]==5 and is_friends($ottieniID,$IDmio)) OR ($rowdefprivv[0]==6 and $num_fave==1)) OR ($rowdefprivv[0]==3)) OR ($rowdefprivv[0]==4)) OR ($ottieniID==$IDmio)) $okv='si';
   else $okv='no';
  }
else
{ 
 //get the id of the custom privacy level for the sender
 $querycustom="SELECT id FROM sys_privacy_groups WHERE owner_id=".$ottieniID;
 $getcustomid=mysql_query($querycustom);
 $getnump= mysql_num_rows($getcustomid);
 if ($getnump>0) 
 {
  $getidcustomprivacy=mysql_fetch_assoc($getcustomid);
  $verifiam="SELECT ID FROM Profiles WHERE ID=".$IDmio." AND ID IN (SELECT member_id FROM sys_privacy_members WHERE group_id=".$getidcustomprivacy['id'].")";
  $getifiam=mysql_query($verifiam);
  $numberisoneifiam=mysql_num_rows($getifiam);
  if ($numberisoneifiam==1) $okv='si';
  else $okv='no';
 }
 else $okv='no';
}

//Ottengo il nome predefinito dell'album Hidden
$hiddenalbumname='SELECT VALUE FROM sys_options WHERE Name="sys_album_default_name"';
$risulthidd=mysql_query($hiddenalbumname);
$namealbumhidd=mysql_fetch_assoc($risulthidd);

//BLOCCO ALBUM FOTO
if (($photoviewalbum!='ON') or (!is_friends($ottieniID,$IDmio) and $okf=='no' and $num_fave!=1) or (is_friends($ottieniID,$IDmio) and $okf=='no' and $num_fave!=1)) {}
else
{ 
  if ($ottieniID==$IDmio) {$estrazione = "SELECT ID,Caption,Uri,ObjCount,Description,Owner FROM sys_albums WHERE Owner = '$ottieniID' AND Type='bx_photos' AND Caption<>'".$namealbumhidd['VALUE']."' AND ObjCount>0";}
  elseif (!isLogged())
  {//Mostro ai Guest solo gli album con privacy uguale a public o privacy uguale a default se il proprietario dell'album ha come privacy di default public
   if ($okf=='si') $estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_photos' AND Caption<>'".$namealbumhidd['VALUE']."' AND ObjCount>0) AND ((AllowAlbumView=1) OR (AllowAlbumView=3))";
   else $estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_photos' AND Caption<>'".$namealbumhidd['VALUE']."' AND ObjCount>0) AND (AllowAlbumView=3)";
  }
  else
  {
   //Im Logged
   //Im not a friend and Im not a favorite
   if (!is_friends($ottieniID,$IDmio) and $okf=='si' and $num_fave==1) 
   {
    $estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_photos' AND ObjCount>0 AND Caption<>'".$namealbumhidd['VALUE']."') AND (AllowAlbumView=1 OR AllowAlbumView=3 OR AllowAlbumView=4 OR AllowAlbumView=6)";
   }
   elseif (!is_friends($ottieniID,$IDmio) and $okf=='si' and $num_fave!=1) 
   {//Im not a friend but Im a favorite
    $estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_photos' AND ObjCount>0 AND Caption<>'".$namealbumhidd['VALUE']."') AND (AllowAlbumView=1 OR AllowAlbumView=3 OR AllowAlbumView=4)";
   }
   elseif (is_friends($ottieniID,$IDmio) and ($okf=='no') and $num_fave==1) 
   {//Im friend and favorite
    $estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_photos' AND Caption<>'".$namealbumhidd['VALUE']."' AND ObjCount>0) AND (AllowAlbumView=3 OR AllowAlbumView=4 OR AllowAlbumView=5 OR AllowAlbumView=6)";
   }
   elseif (is_friends($ottieniID,$IDmio) and ($okf=='si') and $num_fave==1) {$estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_photos' AND Caption<>'".$namealbumhidd['VALUE']."' AND ObjCount>0) AND (AllowAlbumView=3 OR AllowAlbumView=4 OR AllowAlbumView=5 OR AllowAlbumView=1 OR AllowAlbumView=6)";}
   elseif (is_friends($ottieniID,$IDmio) and ($okf=='si') and $num_fave==0) 
   {//Im friend but not a favorite
    $estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_photos' AND Caption<>'".$namealbumhidd['VALUE']."' AND ObjCount>0) AND (AllowAlbumView=3 OR AllowAlbumView=4 OR AllowAlbumView=5 OR AllowAlbumView=1)";
   }
  }
  $esegui = mysql_query($estrazione);
  $contaalbum = mysql_num_rows($esegui);
  
  if($contaalbum>0)
  {
   $numerofoto=min($maxnumberalbumsfoto,$contaalbum);
   echo '<div class="summaryinfo"><div class="io"><div class="menuname">'._t("_ibdw_mp_aphotos").'<div class="amdx">'; 
   if($attivaintegrazione == 0) 
   {
    if ($IDmio==$ottieniID) {echo '<a href="'.BX_DOL_URL_ROOT.'m/photos/albums/my/main/" class="linkallf">';}
    else {echo '<a href="'.BX_DOL_URL_ROOT.'m/photos/albums/browse/owner/'.$usernameis.'" class="linkallf">';} 
   }
   else 
   { 
    if ($IDmio==$ottieniID) {echo '<a href="'.BX_DOL_URL_ROOT.'page/photodeluxe" class="linkallf">';}
    else {echo '<a href="'.BX_DOL_URL_ROOT.'page/photodeluxe?ui='.$usernameis.'" class="linkallf">';} 
   }
   echo _t("_ibdw_mp_all").'</a></div><div class="contamici">'.str_replace("{numeroallalbum}",$contaalbum,str_replace("{numberofalbum}",$numerofoto,_t("_ibdw_mp_ofalbums"))).'</div></div></div>';
   for($contatoref=0;$contatoref<min($maxnumberalbumsfoto,$contaalbum);$contatoref++)
   {
    $foto=mysql_fetch_array($esegui);
    $anteprima = "SELECT sys_albums_objects.id_object, bx_photos_main.ID, bx_photos_main.Hash, bx_photos_main.Owner, bx_photos_main.Ext FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album) INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID='".$foto['ID']."'";
    $exe_anteprima = mysql_query($anteprima);
    $anteprimariga = mysql_fetch_assoc($exe_anteprima);
    $numerodellefoto= mysql_num_rows($exe_anteprima);
    echo '<div class="ibdw_photo_mainphoto" onclick="ibdw_photo_albumupdate('.$foto['ID'].')">';
    echo '<div class="ibdw_photo_sysbordatura" onclick="ibdw_photo_albumupdate('.$foto['ID'].')">';
    if($attivaintegrazione == 0) 
    { 
     echo '<div class="ibdw_photo_mainphoto_album"><a href="'.BX_DOL_URL_ROOT.'m/photos/browse/album/'.$foto['Uri'].'/owner/'.$usernameis.'"><img class="dimfotop" src="'.BX_DOL_URL_ROOT.'m/photos/get_image/browse/'.$anteprimariga['Hash'].'.'.$anteprimariga['Ext'].'"></a></div>';
     echo '</div>';
     echo '<div class="infoalbum"><div class="titoloalbum"><a href="'.BX_DOL_URL_ROOT.'m/photos/browse/album/'.$foto['Uri'].'/owner/'.$usernameis.'">'.$foto['Caption'].'</a></div><div class="descrizionealbum">'.TagliaStringa($foto['Description'],$maxlunghdescalbum).'</div><div class="objconta">'.str_replace("{numphotos}",$numerodellefoto,_t("_ibdw_mp_rphotos")).'</div></div>';
    }
    else 
    { 
     echo '<div class="ibdw_photo_mainphoto_album"><a href="'.BX_DOL_URL_ROOT.'page/photodeluxe?ia='.criptcodes($foto['ID']).'&ui='.criptcodes($foto['Owner']).'"><img class="dimfotop" src="'.BX_DOL_URL_ROOT.'m/photos/get_image/browse/'.$anteprimariga['Hash'].'.'.$anteprimariga['Ext'].'"></a></div>';
     echo '</div>';
     echo '<div class="infoalbum"><div class="titoloalbum"><a href="'.BX_DOL_URL_ROOT.'page/photodeluxe?ia='.criptcodes($foto['ID']).'&ui='.criptcodes($foto['Owner']).'">'.$foto['Caption'].'</a></div><div class="descrizionealbum">'.TagliaStringa($foto['Description'],$maxlunghdescalbum).'</div><div class="objconta">'.str_replace("{numphotos}",$numerodellefoto,_t("_ibdw_mp_rphotos")).'</div></div>';
    }
    echo '</div>';
   }
   echo '</div>';
  }
} 

//BLOCCO ALBUM VIDEO
if (($videoviewalbum!='ON') or (!is_friends($ottieniID,$IDmio) and $okv=='no' and $num_fave!=1) or (is_friends($ottieniID,$IDmio) and $okv=='no' and $num_fave!=1)) {}
else
{
 if ($ottieniID==$IDmio) {$estrazione = "SELECT ID,Caption,Uri,ObjCount,Description,Owner FROM sys_albums WHERE Owner = '$ottieniID' AND Type='bx_videos' AND Caption<>'".$namealbumhidd['VALUE']."' AND ObjCount>0";}
 //Im not logged, so I can see only where privacy is public or default and default privacy is public
 elseif (!isLogged())
 {
  if ($okv=='si') $estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_videos' AND ObjCount>0) AND ((AllowAlbumView=1) OR (AllowAlbumView=3))";
  else $estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_videos' AND ObjCount>0) AND (AllowAlbumView=3)";
 }
 else
 {
  //Im logged
  //Im not a friend and Im not a favorite
  if (!is_friends($ottieniID,$IDmio) and $okv=='si' and $num_fave==1) {$estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_videos' AND ObjCount>0 AND Caption<>'".$namealbumhidd['VALUE']."') AND (AllowAlbumView=1 OR AllowAlbumView=3 OR AllowAlbumView=4 OR AllowAlbumView=6)";}
  //Im not a friend but Im a favorite
  elseif (!is_friends($ottieniID,$IDmio) and $okv=='si' and $num_fave!=1) {$estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_videos' AND ObjCount>0 AND Caption<>'".$namealbumhidd['VALUE']."') AND (AllowAlbumView=1 OR AllowAlbumView=3 OR AllowAlbumView=4)";}
  
  //Im friend and Im a favorite
  elseif (is_friends($ottieniID,$IDmio) and ($okv=='no') and $num_fave==1) {$estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_videos' AND Caption<>'".$namealbumhidd['VALUE']."' AND ObjCount>0) AND (AllowAlbumView=3 OR AllowAlbumView=4 OR AllowAlbumView=5 OR AllowAlbumView=6)";}
  elseif (is_friends($ottieniID,$IDmio) and ($okv=='si') and $num_fave==1) {$estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_videos' AND Caption<>'".$namealbumhidd['VALUE']."' AND ObjCount>0) AND (AllowAlbumView=3 OR AllowAlbumView=4 OR AllowAlbumView=5 OR AllowAlbumView=1 OR AllowAlbumView=6)";}
  //Im friend but not a favorite
  elseif (is_friends($ottieniID,$IDmio) and ($okv=='si') and $num_fave==0) {$estrazione="SELECT ID,Caption,Uri,ObjCount,Description,AllowAlbumView,Owner FROM sys_albums WHERE (Owner = '$ottieniID' AND Type='bx_videos' AND Caption<>'".$namealbumhidd['VALUE']."' AND ObjCount>0) AND (AllowAlbumView=3 OR AllowAlbumView=4 OR AllowAlbumView=5 OR AllowAlbumView=1)";}
 }
 $esegui = mysql_query($estrazione);
 $contaalbum = mysql_num_rows($esegui);
 if($contaalbum>0)
 {
  $numerovideo=min($maxnumberalbumsfoto,$contaalbum);
  echo '<div class="summaryinfo"><div class="io"><div class="menuname">'._t("_ibdw_mp_avideos").'<div class="amdx">';
  
  if ($IDmio==$ottieniID) {echo '<a href="'.BX_DOL_URL_ROOT.'m/videos/albums/my/main/" class="linkallf">';}
  else {echo '<a href="'.BX_DOL_URL_ROOT.'m/videos/albums/browse/owner/'.$nomeutente.'" class="linkallf">';}
  echo _t("_ibdw_mp_all").'</a></div><div class="contamici">'.str_replace("{numeroallalbum}",$contaalbum,str_replace("{numberofalbum}",$numerovideo,_t("_ibdw_mp_ofalbums"))).'</div></div></div>';
  for($contatoref=0;$contatoref<min($maxnumberalbumsvideo,$contaalbum);$contatoref++)
  {
   $video=mysql_fetch_array($esegui);
   $anteprima = "SELECT sys_albums_objects.id_object, RayVideoFiles.ID , RayVideoFiles.Source, RayVideoFiles.Video FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID = sys_albums_objects.id_album) INNER join RayVideoFiles ON RayVideoFiles.ID=sys_albums_objects.id_object WHERE sys_albums.ID='".$video['ID']."'";
   $exe_anteprima = mysql_query($anteprima);
   $numerodeivideo= mysql_num_rows($exe_anteprima);
   $thummm="";
   for($contatorev=0;$contatorev<$video['ObjCount'];$contatorev++)
   {
    $anteprimariga = mysql_fetch_assoc($exe_anteprima);
    if ($anteprimariga['Source']=="youtube") { $thummm='<img class="dimfotop" src="http://i.ytimg.com/vi/'.$anteprimariga['Video'].'/default.jpg">';}
    elseif($thummm=="") {$thummm='<img class="dimfotop" src="'.BX_DOL_URL_ROOT.'flash/modules/video/files/'.$anteprimariga['ID'].'_small.jpg">';}
   }
   echo '<div class="ibdw_photo_mainphoto" onclick="ibdw_photo_albumupdate('.$video['ID'].')"><div class="ibdw_photo_sysbordatura" onclick="ibdw_photo_albumupdate('.$video['ID'].')"><div class="ibdw_photo_mainphoto_album"><a href="'.BX_DOL_URL_ROOT.'m/videos/browse/album/'.$video['Uri'].'/owner/'.$usernameis.'">'.$thummm.'</a></div></div>';
   echo '<div class="infoalbum"><div class="titoloalbum"><a href="'.BX_DOL_URL_ROOT.'m/videos/browse/album/'.$video['Uri'].'/owner/'.$usernameis.'">'.$video['Caption'].'</a></div><div class="descrizionealbum">'.TagliaStringa($video['Description'],$maxlunghdescalbum).'</div><div class="objconta">'.str_replace("{numvideos}",$numerodeivideo,_t("_ibdw_mp_rvideos")).'</div></div></div>';
  }
  echo '</div>';
 }
}
?>
<script>
 function elimina() {} 
 
 function editmon(valore) 
 { 
  $("#ibdw_text_container"+valore).fadeOut(1);
  $("#deskedits"+valore).fadeIn(1);
 }
 
 function noedit(valore) 
 { 
  $("#deskedits"+valore).fadeOut(1);
  $("#ibdw_text_container"+valore).fadeIn(1);
 }
     
 function update(idprofilo,valore)
 {
  var testo1 = $("#deskup"+valore).val();
  var testo1 = testo1.replace("&","ecommerciale");
  $.ajax({type: "POST", url: "modules/ibdw/megaprofile/updatedesc.php", 
  data: "testo=" +testo1 + "&idu="+idprofilo,
  success: function(html)
  {
   aggiornamentoajax_profile();
  }});
 }

 function PaddslashesA(stringa) 
 {
  return (stringa + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');  
 }
 
 function iampra(valore,descrizione) 
 {
  var descrizioneda=PaddslashesA(descrizione); 
  
  var descrizioneda=descrizioneda.replace('apicedouble','"');
  while (descrizioneda != (descrizioneda = descrizioneda.replace('apicedouble', '"')));
  $("#ibdw_text"+valore).replaceWith("<div id=ibdw_text"+valore+">"+decodeURIComponent((descrizioneda + '').replace(/\+/g, '%20'))+"</div>");
  $("#ibdw_readmore"+valore).fadeOut(1);
  $("#ibdw_cllpse"+valore).css("display","block");
 }
 function disampra(valore,descrizione) 
 {
  $("#ibdw_text"+valore).replaceWith("<div class=ibdw_text id=ibdw_text"+valore+">"+decodeURIComponent((descrizione + '').replace(/\+/g, '%20'))+"...</div>");
  $("#ibdw_readmore"+valore).fadeIn(1);
  $("#ibdw_cllpse"+valore).css("display","none");
 }
</script>