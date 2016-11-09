<!-- ***********************************************************************************************************************







ATTENTION PLEASE : The code must be past in a PHP bloc in Boonex administration/pageBuilder.php WITHOUT the PHP beacons







*********************************************************************************************************************** -->

<?php

echo '<html>
<head>
<script>
function getChoice(int)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("refreshed").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","wip-member/3colchoice.php?choice="+int,true);
xmlhttp.send();
}
</script>';

//DEBUT AJAX

//-----EVENT-----
echo '<script> jQuery(document).ready(function(){
setInterval(function(){
jQuery.ajax({
          type: "GET", // Le type de ma requete
          url: "../wip-member/Dataevent.php",
          success: function(dataevt, textStatus, jqXHR) {
		  $("#RefreshEvt").empty(); // on vide le div
          $("#RefreshEvt").append(dataevt); // on met dans le div le r?sultat de la requete ajax
          //alert(data); //Verif données envoyées
		  }
		  });
		  },2000);
});</script>';

//-----PROJECTS-----
echo '<script> jQuery(document).ready(function(){
setInterval(function(){
jQuery.ajax({
          type: "GET", // Le type de ma requete
          url: "../wip-member/Datagroup.php",
          success: function(datagrp, textStatus, jqXHR) {
		  $("#RefreshGrp").empty(); // on vide le div
          $("#RefreshGrp").append(datagrp); // on met dans le div le r?sultat de la requete ajax
          //alert(data); //Verif données envoyées
		  }
		  });
		  },2000);
});</script>';

//-----NEWS------
echo '<script> jQuery(document).ready(function(){
setInterval(function(){
jQuery.ajax({
          type: "GET", // Le type de ma requete
          url: "../wip-member/Datanews.php",
          success: function(datanews, textStatus, jqXHR) {
		  $("#RefreshNews").empty(); // on vide le div
          $("#RefreshNews").append(datanews); // on met dans le div le r?sultat de la requete ajax
          //alert(data); //Verif données envoyées
		  }
		  });
		  },2000);
});</script>';

//-----PRODUCT-----




//-----SERVICES------




//FIN AJAX

echo '<link rel="stylesheet" type="text/css" href="wip-member/css/wipmod.css">';

echo '</head>
<body>';

$memberiID=(int)$_COOKIE['memberID'];

function DataTime($dataTime) 
{ 
$date = date("j/m/Y", $dataTime); 
$hour = date("H:i", $dataTime); 
$yesterday = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))); 
$today = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d"), date("Y"))); 
$tomorrow = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))); 
$aftertomorrow = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 2, date("Y")));
$inaweek = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 7, date("Y")));  
if ($date == $yesterday) $dateDisplay = _t('_datatimefunction_yesterday'); 
elseif ($date == $today) $dateDisplay = _t('_datatimefunction_today');
elseif ($date == $tomorrow) $dateDisplay = _t('_datatimefunction_tomorrow'); 
elseif ($date == $aftertomorrow) $dateDisplay = _t('_datatimefunction_after'); 
elseif ($date == $inaweek) $dateDisplay = _t('_datatimefunction_week'); 
else $dateDisplay = $date; return("$dateDisplay $hour");
}

echo '<div id="chooseform">
<center>
<form>
<input type="radio" class="styled" name="choice" value="0" onclick="getChoice(this.value)" checked>
<input type="radio" class="styled" name="choice" value="2" onclick="getChoice(this.value)">
<input type="radio" class="styled" name="choice" value="4" onclick="getChoice(this.value)">
<input type="radio" class="styled" name="choice" value="6" onclick="getChoice(this.value)">
<input type="radio" class="styled" name="choice" value="8" onclick="getChoice(this.value)">
</form>
</center>
</div>';

echo '<div id="refreshed">';
echo '<div class="titledivbox">
        <div class="eventicon titledivstyle">
          <a href="m/events/browse/my"><div class="titleitembox">'._t('_wipM_my_events').'</div></a>
        </div>
      </div>';
echo '<div id="RefreshEvt" class="scroll3col">';

//Look Dataevent.php for more explanations on the code below

$tableau = array();
$tabmodif = array();

$sql_principale = "SELECT * FROM sys_pre_values,bx_events_main,bx_events_participants WHERE sys_pre_values.Value = bx_events_main.Type AND bx_events_main.ID = bx_events_participants.id_entry AND bx_events_participants.id_profile=".$memberiID." ORDER BY bx_events_main.EventEnd ASC";
$req_principale = mysql_query($sql_principale);

while($result_principal = mysql_fetch_assoc($req_principale)){
array_push($tableau,$result_principal['Open']);
array_push($tabmodif,$result_principal['Modif']);

if(($result_principal['Open']==1) and ($result_principal['EventEnd'] > time())){
  $sql_modif1 = "UPDATE bx_events_main SET Modif=1 WHERE Open=1 AND EventEnd > ".time()."";
  $req_modif1 = mysql_query($sql_modif1);
    echo '
      <div class="boxcontent">
        <div class="topElement">
          <div class="typebox">
            <p class="typestyle">'._t($result_principal['LKey']).'</p>
          </div>
          <div class="picbox">
            <div class="picON"></div>
          </div>
        </div>
        <div class="middleElement">
          <div class="titlebox">
            <p class="titlestyle"><a href="m/events/view/'.$result_principal['EntryUri'].'">'.$result_principal['Title'].'</a></p>
          </div>
          <div class="bxcategorybox">
            <p class="bxcategorystyle">'.$result_principal['Categories'].'</p>
          </div>
          <div class="datebox">
            <p class="datestyle">'._t('_wipM_deadline').' : '.DataTime($result_principal['EventStart'],$dateFormatC).'</p>
          </div>
        </div>
        <div class="bottomElement">
          <div class="fansbox">
            <p class="fanscount">'.$result_principal['FansCount'].'</p>
            <div class="fanspic"></div>
          </div>
        </div>
      </div>';
}

if(($result_principal['Open']==0) and ($result_principal['EventEnd'] > time())){
  $sql_modif2 = "UPDATE bx_events_main SET Modif=1 WHERE Open=0 AND EventEnd > ".time()."";
  $req_modif2 = mysql_query($sql_modif2);
  $ModifTitle = $result_principal['Title'];
  $ModifTitleShort = substr($ModifTitle,0,25);
  if ($result_principal['ResponsibleID'] != $memberiID){
    echo '
      <div class="Modboxcontent">
        <div class="topElement">
          <div class="typebox">
            <p class="Modtypestyle">'._t($result_principal['LKey']).'</p>
          </div>
          <div class="picbox">
          <div class="ModpicOFF"></div>
          </div>
        </div>
        <div class="middleElement">
          <div class="titlebox">
            <p class="Modtitlestyle"><a href="m/events/view/'.$result_principal['EntryUri'].'">'.$ModifTitleShort.'</a>';
      if(strlen($ModifTitle) > 25){
      echo '<span style="color:#9BB9D2;">...</span>';
      }
      echo '  </p></div>
          <div class="modifiedmsgbox">
            <p class="modifiedmsgstyle">'._t('_wipM_modified').'</p>
          </div>
          <div class="requestbox">
            <p class="requeststyle"><a href="mail.php?mode=compose&recipient_id='.$result_principal['ResponsibleID'].'">'._t('_wipM_modified_sendmsg').'</a>
          </div>
        </div>
        <div class="bottomElement">
          <div class="fansbox">
            <p class="fanscount">'.$result_principal['FansCount'].'</p>
            <div class="Modfanspic"></div>
          </div>
        </div>
      </div>';
    }
    else{
      echo '
      <div class="Modboxcontent">
        <div class="topElement">
          <div class="typebox">
            <p class="Modtypestyle">'._t($result_principal['LKey']).'</p>
          </div>
          <div class="picbox">
          <div class="ModpicOFF"></div>
          </div>
        </div>
        <div class="middleElement">
          <div class="titlebox">
            <p class="Modtitlestyle"><a href="m/events/view/'.$result_principal['EntryUri'].'">'.$ModifTitleShort.'</a>';
      if(strlen($ModifTitle) > 25){
      echo '<span style="color:#9BB9D2;">...</span>';
      }
      echo '  </p></div>
          <div class="modifiedmsgbox">
            <p class="modifiedmsgstyle">'._t('_wipM_modified_responsible').'</p>
          </div>
          <div class="editbox">
            <p class="editstyle"><a href="m/events/view/'.$result_principal['EntryUri'].'">'._t('_wipM_edititem').'</a></p>
          </div>
        </div>
        <div class="bottomElement">
          <div class="fansbox">
            <p class="fanscount">'.$result_principal['FansCount'].'</p>
            <div class="Modfanspic"></div>
          </div>
        </div>
      </div>';
    }
}

if(($result_principal['Open']==1) and ($result_principal['EventEnd'] < time())){
$sql_past = "UPDATE bx_events_main SET Open=0 WHERE Open=1 AND EventEnd < ".time()."";
$req_past = mysql_query($sql_past);
}

if(($result_principal['Open']==0) and ($result_principal['EventEnd'] < time())){
$sql_reset = "UPDATE bx_events_main SET Modif=0 WHERE Modif=1 AND EventEnd < ".time()."";
$req_reset = mysql_query($sql_reset);
}

}
  $varX = 0;
  $counttab = count($tabmodif);
  $tab2D = array($tableau,$tabmodif);
  for($varX=0;$varX<$counttab;$varX++){
  $calc = $tab2D[0][$varX]+$tab2D[1][$varX];
  }
  if ($calc == 0){

    echo '
    <div class="noitems">
      <br />
      <p>'._t('_wipM_no_events_scheduled').'</p>
      <a href="m/events/browse/my&bx_events_filter=add_event">'._t('_wipM_create_an_event').'</a>
      <br />
      <br />
    </div>';
}
echo '
</div>
</div>
</body>
</html>';

?>