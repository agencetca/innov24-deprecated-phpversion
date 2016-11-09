<?php
require_once( '../inc/header.inc.php' ); // connect BDD
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

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

//---TABLEAUX--------
$tableau = array(); // On crée un tableau vide
$tabmodif = array(); // 2eme tableau vide

//On sélectionne tous les évènements concernant l'user connecté triés par date d'expiration
$sql_principale = "SELECT * FROM sys_pre_values,bx_events_main,bx_events_participants WHERE sys_pre_values.Value = bx_events_main.Type AND bx_events_main.ID = bx_events_participants.id_entry AND bx_events_participants.id_profile=".$memberiID." ORDER BY bx_events_main.EventEnd ASC"; 
//On éxécute la requête
$req_principale = mysql_query($sql_principale);
// On liste les infos liés aux événements auxquels l'user connecté participe
while($result_principal = mysql_fetch_assoc($req_principale)){
array_push($tableau,$result_principal['Open']); // On met toutes les valeurs de la colonne Open dans le tableau
array_push($tabmodif,$result_principal['Modif']); // On met toutes les valeurs de la colonne Modif dans le tableau


//-----Condition 1 : Events activés et à venir-------
if(($result_principal['Open']==1) and ($result_principal['EventEnd'] > time())){
//On met Modif à 1
$sql_modif1 = "UPDATE bx_events_main SET Modif=1 WHERE Open=1 AND EventEnd > ".time()."";
$req_modif1 = mysql_query($sql_modif1);
// On affiche les events correspondants
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
}//------------FIN Condition 1----------------

//-------Condition 2 : Events non activés et à venir-----------
if(($result_principal['Open']==0) and ($result_principal['EventEnd'] > time())){
$sql_modif2 = "UPDATE bx_events_main SET Modif=1 WHERE Open=0 AND EventEnd > ".time()."";
$req_modif2 = mysql_query($sql_modif2);
$ModifTitle = $result_principal['Title'];
$ModifTitleShort = substr($ModifTitle,0,25);
// On affiche les events correspondants selon user id = createur event
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
} //------------FIN Condition 2----------------

//--------Condition 3 : Events activés et passés--------------
if(($result_principal['Open']==1) and ($result_principal['EventEnd'] < time())){
//On sélectionne les events correspondants
$sql_past = "UPDATE bx_events_main SET Open=0 WHERE Open=1 AND EventEnd < ".time()."";
//$sql_past = "UPDATE bx_events_main SET Featured=0 WHERE Featured=1 AND EventEnd < ".time()."";
//On éxecute la requête
$req_past = mysql_query($sql_past);
}//-----------FIN Condition 3--------------

//-------------Condition 4 : Event non activé et passé---------
if(($result_principal['Open']==0) and ($result_principal['EventEnd'] < time())){
$sql_reset = "UPDATE bx_events_main SET Modif=0 WHERE Modif=1 AND EventEnd < ".time()."";
//$sql_reset = "UPDATE bx_events_main SET Featured=0 WHERE Featured=1 AND EventEnd < ".time()."";
$req_reset = mysql_query($sql_reset);
}
//----------FIN Condition 4---------------
}
//---------Variables pour compter lignes BDD------------
  $varX = 0;
  $counttab = count($tabmodif);
  //echo $counttab; //Test du nb de lignes de la BDD
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
 //---------------------FIN--------------
}
?>