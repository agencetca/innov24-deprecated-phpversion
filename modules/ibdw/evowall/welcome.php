<?php
  $sql = "SELECT COUNT(*) AS verifica FROM bx_spy_data WHERE sender_id = ".(int)$_COOKIE['memberID'];
  $exesql = mysql_query($sql);
  $fetchsql = mysql_fetch_assoc($exesql);
  $numerazione_query = $fetchsql['verifica'];
  if(($numerazione_query < $welcome_n_query+1) and (!isset($_COOKIE['welcomeoff']))) 
  { 
?> 
<div id="welcomebox">
<div id="avatarsimple">
<img src="<?php echo BX_DOL_URL_ROOT;?>modules/ibdw/evowall/templates/uni/css/immagini/infowelcome.png" class="mioavats">
</div>
<div class="warning" id="messaggio">
<div id="primariga" style="color: #666666;font-size: 12px;line-height: 15px;">
<b><?php echo _t("_ibdw_evowall_welcome_title");?></b><?php echo _t("_ibdw_evowall_welcome_message");?>
</div>
<div id="cont_bottoni" style="margin-top:10px;">
<div id="matitaintro"><a href="javascript:nonmostrare();"><img class="matita" src="<?php echo BX_DOL_URL_ROOT;?>modules/ibdw/evowall/templates/uni/css/immagini/post.png"><span class="comfx"><?php echo _t("_ibdw_evowall_welcome_hidemessage");?></span></a></div>
</div>
</div>
</div>
<script>
  function setCookie(sNome, sValore, iGiorni) {
    var dtOggi = new Date()
    var dtExpires = new Date()
  dtExpires.setTime
    (dtOggi.getTime() + 24 * iGiorni * 3600000)
  document.cookie = sNome + "=" + escape(sValore) +
    "; expires=" + dtExpires.toGMTString();
  }
  function nonmostrare(){
  $("#welcomebox").fadeOut();
  setCookie('welcomeoff','1',<?php echo $welcome_cookie_day;?>);
  }
</script>
<?php } ?>