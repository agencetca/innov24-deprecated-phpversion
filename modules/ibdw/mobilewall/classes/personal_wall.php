<script>
$(document).ready(function() {
  $("#messaggio").val("<?php echo _t("_ibdw_mobilewall_personalwall");?>");
});
</script>
<div id="personal">
  <div id="multi_action" onclick="fadeElement('#fade_action');">
  </div>
  <div id="fade_action"> 
       <div class="action_row" onclick="location.href='bxphotoupload:mobile-wall'"><?php echo _t("_ibdw_mobilewall_photogo");?></div>
       <div class="action_row" onclick="window.location.reload();"><?php echo _t("_ibdw_mobilewall_reload");?></div>
       <div class="action_row" onclick="fadeElementOut('#fade_action');"><?php echo _t("_ibdw_mobilewall_close");?></div>
  </div>
  <div id="textarea_wall"> <input onclick="reset();" type="text" id="messaggio" value="<?php echo _t("_ibdw_mobilewall_personalwall");?>"></div>
  <div id="sub_wall" onclick="inviamessaggio();"> </div>
</div>                  
<?php
$nickinvia = estrainick($mioid,$usernameformat);
$nickriceve = estrainick($userid,$usernameformat);
?>

<script>
function inviamessaggio(){ 
  var messaggio = $("#messaggio").val();
  if(messaggio != "<?php echo _t("_ibdw_mobilewall_personalwall");?>" && messaggio != "" ) {
  var messaggio = messaggio.replace(/[']/g,"xyapostrofo");
  var messaggio = messaggio.replace(/&/g,"ecommerciale");
  $.ajax({
   type: "POST",
   url:  "<?php echo $urlsite;?>modules/ibdw/mobilewall/classes/invia_wall.php",
   data: "messaggio="+messaggio+"&nickinvia=<?php echo $nickinvia;?>&nickriceve=<?php echo $nickriceve;?>&invia=<?php echo $mioid;?>&riceve=<?php echo $userid;?>",
   success: function(data){
   window.location.reload();
   }
 });
}
}

function reset() { 
  $("#messaggio").val("");
}      
</script>