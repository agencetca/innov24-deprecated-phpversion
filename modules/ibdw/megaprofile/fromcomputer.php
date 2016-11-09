<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/megaprofile/config.php';
$mioid=(int)$_COOKIE['memberID'];

$versione="SELECT Name,sys_options.VALUE FROM sys_options WHERE Name='sys_tmp_version'";
$esegui=mysql_query($versione);
$riga=mysql_fetch_assoc($esegui);
$explode=explode('.',$riga['VALUE']);
$versionedolmain=$explode[1];
$versionedol=$explode[2];
if($versionedolmain=='0') 
{
 $nomeutente = getNickName($mioid);
}
else
{
 $nomeutente = getUsername($mioid);
}
	$ottienialbum = "SELECT VALUE FROM sys_options WHERE Name='bx_photos_profile_album_name'";
	$eseguialbum = mysql_query($ottienialbum);
	$fetchalbum= mysql_fetch_assoc($eseguialbum);
	$nalbumm = $fetchalbum['VALUE'];
	$nomealbum = uriFilter(str_replace("{nickname}",$nomeutente,$nalbumm));
	$nomealbumtrue = str_replace("{nickname}",$nomeutente,$nalbumm); 
	$nomealbumtrue=addslashes($nomealbumtrue);
	
  $estrazione = "SELECT ID,ObjCount,Uri FROM sys_albums WHERE Owner = '$mioid' AND Uri='$nomealbum'";
  $esegui = mysql_query($estrazione);
  $verificanumero = mysql_num_rows($esegui);
  
  
  if($verificanumero == 0) { 
  
  $inserimento = "INSERT INTO sys_albums (Caption,Uri,Location,Type,Owner,Status,ObjCount,LastObjId,AllowAlbumView,Date) 
                  VALUES ('".$nomealbumtrue."','".$nomealbum."','Undefined','bx_photos','".$mioid."','Active','0','0','1',UNIX_TIMESTAMP())";
  $esegui_exe = mysql_query($inserimento);
  
  }
 // if($_COOKIE['lang'] == 'en')
  $file_button = 'modules/ibdw/megaprofile/engine_upload/uploadify.allglyphs.swf';
 // else
 // $file_button = 'modules/ibdw/megaprofile/engine_upload/uploadify.swf';
?>
   
   
      <script type="text/javascript" src="<?php echo $site['url'];?>modules/ibdw/megaprofile/engine_upload/swfobject.js"></script>
      <script type="text/javascript" src="<?php echo $site['url'];?>modules/ibdw/megaprofile/engine_upload/jquery.uploadify.v2.1.4.min.js"></script>
	  
      <script type="text/javascript">
        $(document).ready(function() {
            $('#file_upload').uploadify({
                'uploader'  : '<?php echo $site['url'].$file_button;?>',
                'script'    : '<?php echo $site['url'];?>modules/ibdw/megaprofile/engine_upload/uploadify.php',
                'cancelImg' : '<?php echo $site['url'];?>modules/ibdw/megaprofile/engine_upload/cancel.png',
                'folder'    : 'modules/boonex/photos/data/files##<?php echo $mioid;?>',
                'fileExt'     : '*.jpg;*.jpeg;*.gif;*.png',
                'fileDesc'    : 'Image Files (JPG/GIF/PNG)',
                'sizeLimit'   : <?php echo $maxsize;?>,
				'buttonText' : '<? echo _t("_ibdw_mp_selectfiles");?>',
				'buttonImg': '<?php echo $site['url'];?>modules/ibdw/megaprofile/templates/base/images/buttonupload.png',
				'wmode': 'transparent',
				'hideButton': true,
                'auto'      : true,
				'onSWFReady':  function () {$('#textbuttoncustom').show();},
                'onAllComplete' : function(event,data) {
                    aggiornamentoajax_profile_cropexe();
              }
			
              });
              });
      </script>
 <div id="closeblur" onclick="chiudiospite();"> </div>         
<div id="mainalbumstyle">
<h2><?php echo _t("_ibdw_mp_frompc");?></h2>
<div class="linestyle"> </div>
<span class="textintroup"><?php echo _t("_ibdw_mp_introfrompc");?></span> 
<div class="infopointupload"> 
1.<?php echo _t("_ibdw_mp_sg1");?> <br/>
2.<?php echo _t("_ibdw_mp_sg2");?><br/>
3.<?php echo _t("_ibdw_mp_sg3");?><br/>
4.<?php echo _t("_ibdw_mp_sg4");?><br/>
</div>
  <div class="uploadbotton">  
 <input id="file_upload" name="file_upload" type="file" />
 <span id="textbuttoncustom" style="display:none;
    border-radius: 4px;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
    background: none repeat scroll 0 0 #585858;
	box-shadow: 0 0 1px 1px #AAAAAA;
	-webkit-box-shadow: 0 0 1px 1px #AAAAAA;
	-moz-box-shadow:0 0 1px 1px #AAAAAA;
	text-shadow: 1px 1px 1px #111111;
    color: #FFFFFF;
    font-family: tahoma;
    font-size: 14px;
    font-weight: normal;
    height: 26px;
    left: 0;
    margin: 0;
    padding: 4px 0 0;
    position: absolute;
    text-align: center;
    top: 0;
    width: 120px;
    z-index: -1;
	"><? echo _t("_ibdw_mp_selectfiles");?></span>
</div>
