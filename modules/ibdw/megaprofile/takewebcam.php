<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
$mioid=(int)$_COOKIE['memberID'];
?>
<div id="closeblur" onclick="chiudiospite();"> </div>   
<div id="tkwebcam">
<object height="300" width="400" type="application/x-shockwave-flash" id="ray_flash_photo_shooter_object" name="ray_flash_photo_shooter_embed" style="display: block;" data="<?php echo $site['url'];?>flash/modules/global/app/holder_as3.swf">
<param name="allowScriptAccess" value="always">
<param name="allowFullScreen" value="true">
<param name="base" value="<?php echo $site['url'];?>flash/modules/photo/">
<param name="bgcolor" value="#FFFFFF"><param name="wmode" value="opaque">
<param name="flashvars" value="url=<?php echo $site['url'];?>flash/XML.php&amp;module=photo&amp;app=shooter&amp;id=<?php echo $mioid;?>&amp;extra=">
</object>
</div>
<div id="takeinst">    
<div class="infotake">
1.<?php echo _t("_ibdw_mp_wksg1");?><br/>
2.<?php echo _t("_ibdw_mp_wksg2");?><br/>
3.<?php echo _t("_ibdw_mp_wksg3");?><br/>
4.<?php echo _t("_ibdw_mp_wksg4");?><br/>
</div>
<div id="continuatakeweb" onclick="updatewebca();">
<?php echo _t("_ibdw_mp_wkcnt");?>
</div>
</div>
<script>
function updatewebca() {
  $("#continuatakeweb").fadeOut(1); 
  $.ajax({
   type: 'POST',
   data: "idutente=<?php echo $mioid;?>",
   url: 'modules/ibdw/megaprofile/selectwebcam.php',
    success: function(html) {
      aggiornamentoajax_profile_cropexe();
    }
});
}
</script>    