<?php
if(($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) and ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowlike')))
{
  $querylike= "SELECT id_utente FROM ibdw_likethis WHERE id_notizia=".$assegnazione;
  $querylikeresult= mysql_query($querylike);
  $rowquerylikeconta= mysql_num_rows($querylikeresult);
  echo '<div id="element_likes'.$assegnazione.'">';
  if ($rowquerylikeconta>2)
  {
   echo '<div class="notyd"></div><div id="element_like'.$assegnazione.'" class="elementlike"><img class="ilicss" src="'.$imagepath.'likesym.png" /><a href="javascript:slide_persone'.$assegnazione.'();">'.$rowquerylikeconta.' '._t("_ibdw_evowall_elementlike1").'</a> '._t("_ibdw_evowall_elementlike2").'<div id="slide_persone'.$assegnazione.'" class="slide_persone"><input type="hidden" id="slide_up'.$assegnazione.'" value="0">';
   while($rowquerylike = mysql_fetch_array($querylikeresult))
   {
    $aInfomember = getProfileInfo($rowquerylike['id_utente']);
    if($usernameformat=='Nickname') {$realname =  ucfirst($aInfomember['NickName']);}
    elseif($usernameformat=='Full name') {$realname =  ucfirst($aInfomember['FirstName']) . " " . ucfirst($aInfomember['LastName']);}
    elseif($usernameformat=='FirstName') {$realname =  ucfirst($aInfomember['FirstName']);}
    echo '<div id="contbox_user_like"><div id="avat_like_box">';
    if ($aInfomember['Avatar']<>"0") {echo '<img class="mioavatsmallx" src="'.BX_AVA_URL_USER_AVATARS.$aInfomember['Avatar'].'i'.BX_AVA_EXT.'">';}
    else 
    {
     if ($aInfomember['Sex']=="female") { echo '<img class="mioavatsmallx" src="/templates/base/images/icons/woman_small.gif">'; }
     else { echo '<img class="mioavatsmallx" src="/templates/base/images/icons/man_small.gif">';}
    }
    echo '</div><div id="user_like_box"><a href="'.ucfirst($aInfomember['NickName']).'" class="friend_like_name">'.$realname.'</a>';
    if (is_friends($accountid,$rowquerylike['id_utente'])==FALSE AND $rowquerylike['id_utente']!=$accountid) 
    {
     if ($funclass->CountMutualFriends3($accountid,$rowquerylike['id_utente'])>0) echo '<div class="box_likemutual"><b>'.$funclass->CountMutualFriends3($accountid,$rowquerylike['id_utente']).'</b>'._t("_ibdw_evowall_mutualfriend").'</div>';
     else echo '<div class="box_likemutual"></div>'; 
    }
    $idutenteidx = $rowquerylike['id_utente'];
    if(is_friends($accountid,$rowquerylike['id_utente'])==FALSE AND $rowquerylike['id_utente']!=$accountid) { 
    //controlla che la richiesta non sia inoltrata    
    $query="SELECT ID,Profile,sys_friend_list.Check FROM sys_friend_list WHERE ID = '$accountid' AND Profile = '$idutenteidx' AND sys_friend_list.Check=0";
    $esegui = mysql_query($query);
    $verifica = mysql_num_rows($esegui);
    if($verifica != 0) { echo '<div class="box_aggiungilike" id="box_aggiungilike'.$idutenteidx.'">'._t("_ibdw_evowall_friendlike2").'</div>'; }
    else { echo '<div class="box_aggiungilike" id="box_aggiungilike'.$idutenteidx.'"><a href="javascript:aggiungi_friend'.$rowquerylike['id_utente'].'('.$idutenteidx.')";>'._t("_ibdw_evowall_friendlike").'</a></div>'; }
   }
   echo '</div></div>
   <script>
    function slide_persone'.$assegnazione.'() 
	{
	 var slide_up = $("#slide_up'.$assegnazione.'").val();
	 if(slide_up==0)
	 {
      $("#slide_persone'.$assegnazione.'").fadeIn(1);
      $("#slide_up'.$assegnazione.'").val(1);
     }
     else 
	 {
      $("#slide_persone'.$assegnazione.'").css(\'display\',\'none\');
      $("#slide_up'.$assegnazione.'").val(0);
     }
    }
   </script>
   <script>
	function aggiungi_friend'.$rowquerylike['id_utente'].'(idevento) 
	{
	 var id_user='.$rowquerylike['id_utente'].';
	 $.ajax({ type: "POST", url: "modules/ibdw/evowall/aggiungi_sugg.php", data: "id_user=" + id_user, success: function(html){ notifica_generale("'._t("_ibdw_evowall_notificalikefriend").'");
	 $("#box_aggiungilike"+idevento).fadeOut();}});
	};
	</script>';
   }
   echo '</div></div>';
  } 
  elseif($rowquerylikeconta>0)
  {
   $contapiace=0;
   echo '<div class="notyd"></div><div id="element_like'.$assegnazione.'" class="elementlike"><img class="ilicss" src="'.$imagepath.'likesym.png" />';
   while($rowquerylike = mysql_fetch_array($querylikeresult))
   {
    if (($rowquerylikeconta == 1) AND ($rowquerylike['id_utente'] == $accountid )) {echo _t("_ibdw_evowall_ilikeit");}
    else 
    {
     $contapiace=$contapiace+1;
   	 $aInfomember = getProfileInfo($rowquerylike['id_utente']);
     if($contapiace==1) {echo '<b> '._t("_ibdw_evowall_thiselementlike").' </b>';}
     if($usernameformat=='Nickname') {$realname=ucfirst($aInfomember['NickName']);}
     elseif($usernameformat=='Full name') {$realname=ucfirst($aInfomember['FirstName']) . " " . ucfirst($aInfomember['LastName']);}
     elseif($usernameformat=='FirstName') {$realname=ucfirst($aInfomember['FirstName']);}
     if ($contapiace>1) {echo ' '._t("_ibdw_evowall_likeand").' ';}
     if($rowquerylike['id_utente']==$accountid) { echo '<a href="'.$aInfomember['NickName'].'"> '._t("_ibdw_evowall_likeyou").' </a>';}
     else {echo '<a href="'.$aInfomember['NickName'].'">'.$realname.' </a>';}
    }
   }
   echo '</div>';
  }
  echo '</div>';
  echo '<script type="text/javascript">
	     function slide'.$assegnazione.'() 
		 {
		  $("#slide'.$assegnazione.'").slideToggle("fast");
		  trimx(document.mioform'.$assegnazione.'.go'.$assegnazione.','.$assegnazione.');
		 };
		 function raffa() {};
  	    </script>';
}
  echo '<div id="cont_bottoni">';

  if ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))
  {      
   echo '<div id="matitaintro"><a href="javascript:slide'.$assegnazione.'();"><span class="comfx">'._t("_ibdw_evowall_ins_comm").'</span></a></div>';
  }
   $querylikecontrollo="SELECT id_notizia,id_utente FROM ibdw_likethis WHERE id_notizia='$assegnazione' AND id_utente='$accountid'";
   $querylikecontrolloresult= mysql_query($querylikecontrollo);
   $rowquerylikecontrollo= mysql_num_rows($querylikecontrolloresult);
   echo '<div id="cont_box_like_ajax'.$assegnazione.'">';
 
   if(($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) and ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowlike')))
   {
    if($rowquerylikecontrollo>=1) 
    {
     echo '<div id="nolike'.$assegnazione.'" class="nolike">
       <form name="likethis'.$assegnazione.'" id="nlikethis'.$assegnazione.'" action="javascript:raffa();">
		   <input id="nid_like'.$assegnazione.'" type="hidden" name="id" value="'. $assegnazione.'">
		   <input id="nuser_like'.$assegnazione.'" type="hidden" name="user" value="'. $accountid.'">
		   <input type="submit" value="'._t("_ibdw_evowall_unlikethis").'">
		   </form>
	       </div>
	       <script>
	        $("#nlikethis'.$assegnazione.'").submit(function() 
	        {
	         var nid_like=$("#nid_like'.$assegnazione.'").attr("value");
		     var nuser_like=$("#nuser_like'.$assegnazione.'").attr("value");
		     $.ajax({type: "POST", url: "modules/ibdw/evowall/like_action.php", data: "id=" + nid_like +"&user=" + nuser_like +"&set=1",success: function(html){ajax_like_riepilogo'.$assegnazione.'();}});
            });
	       </script>';
    }
    else 
    {
     echo '<div id="like'.$assegnazione.'" class="boxlike">
 	  	   <form name="likethis'.$assegnazione.'" id="likethis'.$assegnazione.'" action="javascript:raffa();">
		   <input id="id_like'.$assegnazione.'" type="hidden" name="id" value="'. $assegnazione.'">
		   <input id="user_like'.$assegnazione.'" type="hidden" name="user" value="'.$accountid.'">
		   <input id="like'.$assegnazione.'" type="hidden" name="like" value="1">
		   <input type="submit" value="'._t("_ibdw_evowall_likethis").'">
		   </form>
	       </div>
           <script>
            $("#likethis'.$assegnazione.'").submit(function() 
	        {
	         var id_like=$("#id_like'.$assegnazione.'").attr("value");
	         var user_like=$("#user_like'.$assegnazione.'").attr("value");
	         var like=$("#like'.$assegnazione.'").attr("value");
	         $.ajax({type: "POST", url: "modules/ibdw/evowall/like_action.php", data: "id=" + id_like +"&user=" +user_like+"&set=0",
	         success: function(html){ajax_like_riepilogo'.$assegnazione.'();}});
	        });
	       </script>';
    }
   }
  echo '</div>';
   
      
echo '
    <div id="data">'.$miadata.'</div>
    <div id="clear" style="clear:both"></div>
    
    <div id="commcontainer">
      <div id="slide'.$assegnazione.'" class="slidecss">
		    <form name="mioform'.$assegnazione.'" id="inseriscicommento'.$assegnazione.'" action="javascript:raffa();">
		      <textarea class="mycomm" name="commento" id="go'.$assegnazione.'" onkeypress="trimx(this,'.$assegnazione.');return imposeMaxLength(this, '.$commlength.');" onkeyup="trimx(this,'.$assegnazione.');"></textarea>
		      <input id="id'.$assegnazione.'" type="hidden" name="id" value="'.$assegnazione.'">
		      <input id="user'.$assegnazione.'" type="hidden" name="user" value="'.$accountid.'">
		      <input id="pagina'.$assegnazione.'" type="hidden" name="pagina" value="'. $pagina.'">
		      <input id="assegnazione'.$assegnazione.'" type="hidden" name="ass" value="'.$assegnazione.'">
		      <span id="submitfix"><input type="submit" value="'._t('_ibdw_evowall_send').'" id="sub'.$assegnazione.'" name="sendr'.$assegnazione.'" disabled></span>
		    </form>
	   </div>
   </div>
	 
   <script>
	 $("#inseriscicommento'.$assegnazione.'").submit(function() 
	 {
	  var commi=$("#go'.$assegnazione.'").attr("value");
	  var commento=encodeURIComponentNew(commi);
	  var id=$("#id'.$assegnazione.'").attr("value");
	  var user=$("#user'.$assegnazione.'").attr("value");
	  var pagina=$("#pagina'.$assegnazione.'").attr("value");
	  var assegnazione=$("#assegnazione'.$assegnazione.'").attr("value");
	  $("#sub'.$assegnazione.'").css("background-color","#999");
	  $("#sub'.$assegnazione.'").val("Wait");
	  $.ajax({type: "POST", url: "modules/ibdw/evowall/commento.php", data: "commento="+ commento +"&id=" +id +"&user=" +user +"&pagina=" +pagina +"&assegnazione=" +assegnazione ,
	  success: function(html)
	  {
	   $("#commenti'.$assegnazione.'").remove();
	   $("#newentry'.$assegnazione.'").prepend(html);
	   $("#slide'.$assegnazione.'").fadeOut();
	   $("#sub'.$assegnazione.'").css("background-color","#888");
	   $("#sub'.$assegnazione.'").val("'._t("_ibdw_evowall_send").'");
	   $("#go'.$assegnazione.'").val("");
	   $("#go'.$assegnazione.'").text("");
	  }});
	 });
	 </script>';
 
?>