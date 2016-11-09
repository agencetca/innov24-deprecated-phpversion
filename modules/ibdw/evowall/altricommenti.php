<?php 
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC.'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC.'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC.'utils.inc.php' );
include_once (BX_DIRECTORY_PATH_MODULES.'boonex/avatar/include.php');
require_once('functions.php');
$funclass=new swfunc();
$accountid=(int)$_COOKIE['memberID'];
mysql_query("SET NAMES 'utf8'");
$pagina=$_POST['pagina'];
$assegnazione=$_POST['assegnazione'];
$limitecommento=$_POST['limitecommento'];
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
$limitecommento=$limitecommento+$commprevnum;
include 'templatesw.php';
echo '<div id="newentry'.$assegnazione.'"></div><div id="commenti'.$assegnazione.'" class="commenti">
       <script>
        function altricommenti'.$assegnazione.'(assegnazione,limitecommento,pagina) 
		{
         $("#swich_comment'.$assegnazione.' .othnews").css({"background-image":"url('.$imagepath.'load.gif)","background-repeat":"no-repeat","background-position":"left","padding-left":"20px"});  
         $.ajax({
          type: "POST",url: "modules/ibdw/evowall/altricommenti.php",data:"assegnazione="+assegnazione+"&limitecommento="+limitecommento+"&pagina="+pagina,
                 success: function(html)
				 {
                  $("#commenti'.$assegnazione.'").remove();
                  $("#newentry'.$assegnazione.'").append(html);
                 }
              });
              };
       </script> ';

//ottengo la data di pubblicazione del post per confrontarla con le date dei commenti che devono essere successivi al post ovviamente
$querydatapost="SELECT bx_spy_data.date FROM bx_spy_data WHERE ID=$assegnazione";
$resultdatapost = mysql_query($querydatapost);
$valdata = mysql_fetch_assoc($resultdatapost);	   
	   
$querycontacommenti="SELECT commenti_spy_data.data FROM commenti_spy_data LEFT JOIN datacommenti ON commenti_spy_data.id=datacommenti.IDCommento WHERE data=$assegnazione and datacommenti.date>'".$valdata['date']."'";
$resultcontacommenti=mysql_query($querycontacommenti);
$rowcontacommenti=mysql_num_rows($resultcontacommenti);

if ($rowcontacommenti==0) {echo '';}
else 
{
 if ($rowcontacommenti==1) {$titlecommentis=_t('_ibdw_evowall_comment_title1');$endcomment=_t('_ibdw_evowall_comment_titlef1');}
 else {$titlecommentis=_t('_ibdw_evowall_comment_title2');$endcomment=_t('_ibdw_evowall_comment_titlef2');}
 echo '<div class="commentreport"><div class="comm">'.$titlecommentis.' <span class="numerocommenti'.$assegnazione.'">'.$rowcontacommenti.'</span> '.$endcomment.'</b></div>';
 if($rowcontacommenti>$limitecommento) {echo '<div class="vedialtro"><a href="javascript:altricommenti'.$assegnazione.'('.$assegnazione.','.$limitecommento.',\''.$pagina.'\')" class="othnews">'._t("_ibdw_evowall_altricommenti").'</a></div>';}
 echo '</div>';}
if($ordinec=='Last') {$tipoordine="DESC";}
else {$tipoordine="ASC";}

$querydelcommento="SELECT commenti_spy_data.*,datacommenti.date, Profiles.ID, Profiles.NickName, Profiles.Sex, Profiles.Avatar FROM (commenti_spy_data LEFT JOIN datacommenti ON commenti_spy_data.id=datacommenti.IDCommento) INNER JOIN Profiles ON commenti_spy_data.user = Profiles.ID WHERE date>'".$valdata['date']."' AND data=$assegnazione ORDER BY commenti_spy_data.id ".$tipoordine." LIMIT 0,$limitecommento";





$resultdelcommento=mysql_query($querydelcommento); 
echo '<div id="nuovocommento'.$assegnazione.'"></div><div id="swich_comment'. $assegnazione.'" class="swichwidth">';
while($rowdelcommento=mysql_fetch_array($resultdelcommento))
{
 echo '<div id="commentario" class="super_commento'.$rowdelcommento[id].'">';
 if ($rowdelcommento[user]==$accountid) 
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
  <div id="elimina'.$rowdelcommento[id].'" class="eliminab" style="display:none;"><form id="elimina'.$rowdelcommento[id].'" action="javascript:elimina();"><input id="id'.$rowdelcommento[id].'" type="hidden" name="id" value="'.$rowdelcommento[id].'">
		<input id="assegnazione'.$assegnazione.'" type="hidden" name="id" value="'.$assegnazione.'"><input id="pagina'.$assegnazione.'" type="hidden" name="id" value="'.$pagina.'">
		<input id="limite'.$assegnazione.'" type="hidden" name="id" value="'.$limitecommento.'"><input type="image" src="modules/ibdw/evowall/templates/uni/css/immagini/depr.png" title="'._t('_ibdw_evowall_delete').'" class="elimina'.$rowdelcommento[id].'"></form></div> 
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
 $cmn=strip_tags($cmn);
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
 if($usernameformat=="Nickname") {echo '<a href="'.$rowdelcommento[NickName].'"><b>' . $rowdelcommento[NickName] . '</b></a>: ' . $cmn . '<div class="stydata">'.$miadatac.'</div></div></div></div></div>';}
 if($usernameformat=="Full name") 
 {
  $aInfomember=getProfileInfo($rowdelcommento['user']);
  $realname=ucfirst($aInfomember['FirstName'])." ".ucfirst($aInfomember['LastName']);    
  echo '<a href="'.$rowdelcommento[NickName].'"><b>'.$realname.'</b></a>: '.$cmn.'<div class="stydata">'.$miadatac.'</div></div></div></div></div>';
 }
 if($usernameformat=="FirstName") 
 {
  $aInfomember=getProfileInfo($rowdelcommento['user']);
  $realname=ucfirst($aInfomember['FirstName']);
  echo '<a href="'.$rowdelcommento[NickName].'"><b>'.$realname.'</b></a>: '.$cmn.'<div class="stydata">'.$miadatac.'</div></div></div></div></div>';}
 }
 echo '<div id="altricommenti'.$assegnazione.'"></div></div></div>';
?>