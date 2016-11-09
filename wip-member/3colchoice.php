<?php

require_once('../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');

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

$choix = $_GET['choice'];

if ($choix == 0)
  {
echo '<div class="titledivbox">
        <div class="eventicon titledivstyle">
          <a href="m/events/browse/my"><div class="titleitembox">'._t('_wipM_my_events').'</div></a>
        </div>
      </div>';
echo '
<div id="RefreshEvt" class="scroll3col">';

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
//On sélectionne les events correspondants
$sql_past = "UPDATE bx_events_main SET Open=0 WHERE Open=1 AND EventEnd < ".time()."";
//$sql_past = "UPDATE bx_events_main SET Featured=0 WHERE Featured=1 AND EventEnd < ".time()."";
//On éxecute la requête
$req_past = mysql_query($sql_past);
}

if(($result_principal['Open']==0) and ($result_principal['EventEnd'] < time())){
$sql_reset = "UPDATE bx_events_main SET Modif=0 WHERE Modif=1 AND EventEnd < ".time()."";
//$sql_reset = "UPDATE bx_events_main SET Featured=0 WHERE Featured=1 AND EventEnd < ".time()."";
$req_reset = mysql_query($sql_reset);
}

}
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
  }
echo '</div>';
}
if ($choix == 2)
  {
echo '<div class="titledivbox">
        <div class="newsicon titledivstyle">
          <a href="blogs/my_page/"><div class="titleitembox">'._t('_wipM_my_news').'</div></a>
        </div>
      </div>';
echo '<div id="RefreshNews" class="scroll3col">';

//Look Dataevent.php for more explanations on the code below

$tableau_news = array();
$tabmodif_news = array();

$sql_principale_news = "SELECT * FROM sys_pre_values,bx_blogs_posts WHERE sys_pre_values.Value = bx_blogs_posts.NewsType AND bx_blogs_posts.OwnerID=".$memberiID." ORDER BY PostDate DESC"; 
$req_principale_news = mysql_query($sql_principale_news);

while($result_principal_news = mysql_fetch_assoc($req_principale_news)){
array_push($tableau_news,$result_principal_news['OpenNews']);
array_push($tabmodif_news,$result_principal_news['ModifNews']);

if($result_principal_news['OpenNews']==1){
$sql_modif1_news = "UPDATE bx_blogs_posts SET ModifNews=0 WHERE OpenNews=1";
$req_modif1_news = mysql_query($sql_modif1_news);
  echo '
  <div class="boxcontent">
    <div class="topElement">
      <div class="typebox">
        <p class="typestyle">'._t($result_principal_news['LKey']).'</p>
      </div>
      <div class="picbox">
        <div class="picON"></div>
      </div>
    </div>
    <div class="middleElement">
      <div class="titlebox">
        <p class="titlestyle"><a href="blogs/entry/'.$result_principal_news['PostUri'].'">'.$result_principal_news['PostCaption'].'</a></p>
      </div>
      <div class="bxcategorybox">
        <p class="bxcategorystyle">'.$result_principal_news['Categories'].'</p>
      </div>
      <div class="datebox">
        <p class="datestyle">'._t('_wipM_creation_date').' : '.DataTime($result_principal_news['PostDate'],$dateFormatC).'</p>
      </div>
    </div>
    <div class="bottomElement">
      <div class="fansbox">
        <p class="fanscount">'.$result_principal_news['Views'].'</p>
        <div class="viewspic"></div>
      </div>
    </div>
  </div>';
}

if($result_principal_news['OpenNews']==0){
$sql_modif2_news = "UPDATE bx_blogs_posts SET ModifNews=1 WHERE OpenNews=0";
$req_modif2_news = mysql_query($sql_modif2_news);
$ModifTitle = $result_principal_news['PostCaption'];
$ModifTitleShort = substr($ModifTitle,0,25);
  if ($result_principal_news['OwnerID'] != $memberiID){
  echo '
  <div class="Modboxcontent">
    <div class="topElement">
      <div class="typebox">
        <p class="Modtypestyle">'._t($result_principal_news['LKey']).'</p>
      </div>
      <div class="picbox">
      <div class="ModpicOFF"></div>
      </div>
    </div>
    <div class="middleElement">
      <div class="titlebox">
        <p class="Modtitlestyle"><a href="blogs/entry/'.$result_principal_news['PostUri'].'">'.$ModifTitleShort.'</a>';
  if(strlen($ModifTitle) > 25){
  echo '<span style="color:#9BB9D2;">...</span>';
  }
  echo '  </p></div>
      <div class="modifiedmsgbox">
        <p class="modifiedmsgstyle">'._t('_wipM_modified').'</p>
      </div>
      <div class="requestbox">
        <p class="requeststyle"><a href="mail.php?mode=compose&recipient_id='.$result_principal_news['OwnerID'].'">'._t('_wipM_modified_sendmsg').'</a>
      </div>
    </div>
    <div class="bottomElement">
      <div class="fansbox">
        <p class="fanscount">'.$result_principal_news['Views'].'</p>
        <div class="Modviewspic"></div>
      </div>
    </div>
  </div>';
  }
  else{
    echo '
  <div class="Modboxcontent">
    <div class="topElement">
      <div class="typebox">
        <p class="Modtypestyle">'._t($result_principal_news['LKey']).'</p>
      </div>
      <div class="picbox">
      <div class="ModpicOFF"></div>
      </div>
    </div>
    <div class="middleElement">
      <div class="titlebox">
        <p class="Modtitlestyle"><a href="blogs/entry/'.$result_principal_news['PostUri'].'">'.$ModifTitleShort.'</a>';
  if(strlen($ModifTitle) > 25){
  echo '<span style="color:#9BB9D2;">...</span>';
  }
  echo '  </p></div>
      <div class="modifiedmsgbox">
        <p class="modifiedmsgstyle">'._t('_wipM_modified_responsible').'</p>
      </div>
      <div class="editbox">
        <p class="editstyle"><a href="blogs/entry/'.$result_principal_news['PostUri'].'">'._t('_wipM_edititem').'</a></p>
      </div>
    </div>
    <div class="bottomElement">
      <div class="fansbox">
        <p class="fanscount">'.$result_principal_news['Views'].'</p>
        <div class="Modviewspic"></div>
      </div>
    </div>
  </div>';
}
}
}
  $varY = 0;
  $counttab_news = count($tabmodif_news);
  $tab2D_news = array($tableau_news,$tabmodif_news);
  for($varY=0;$varY<$counttab_news;$varY++){
  $calc_news = $tab2D_news[0][$varY]+$tab2D_news[1][$varY];
  }
  if ($calc_news == 0){

  echo '
  <div class="noitems">
    <br />
    <p>'._t('_wipM_no_published_news').'</p>
    <a href="blogs/my_page/add/">'._t('_wipM_create_a_news').'</a>
    <br />
    <br />
  </div>';
  }
echo '</div>';
}
if ($choix == 4)
  {
echo '<div class="titledivbox">
        <div class="projecticon titledivstyle">
          <a href="m/groups/browse/my"><div class="titleitembox">'._t('_wipM_my_projects').'</div></a>
        </div>
      </div>';
echo '<div id="RefreshGrp" class="scroll3col">';

//Look Dataevent.php for more explanations on the code below

$tableau_grp = array();
$tabmodif_grp = array();

$sql_principale_grp = "SELECT * FROM sys_pre_values,bx_groups_main,bx_groups_fans WHERE sys_pre_values.Value = bx_groups_main.type AND bx_groups_main.id = bx_groups_fans.id_entry AND bx_groups_fans.id_profile=".$memberiID." ORDER BY bx_groups_main.Deadline ASC"; 
$req_principale_grp = mysql_query($sql_principale_grp);

while($result_principal_grp = mysql_fetch_assoc($req_principale_grp)){
array_push($tableau_grp,$result_principal_grp['OpenGrp']);
array_push($tabmodif_grp,$result_principal_grp['ModifGrp']);

  if(($result_principal_grp['OpenGrp']==1) and ($result_principal_grp['Deadline'] > time())){
  $sql_modif1_grp = "UPDATE bx_groups_main SET ModifGrp=1 WHERE OpenGrp=1 AND Deadline > ".time()."";
  $req_modif1_grp = mysql_query($sql_modif1_grp);
  echo '
  <div class="boxcontent">
    <div class="topElement">
      <div class="typebox">
        <p class="typestyle">'._t($result_principal_grp['LKey']).'</p>
      </div>
      <div class="picbox">
        <div class="picON"></div>
      </div>
    </div>
    <div class="middleElement">
      <div class="titlebox">
        <p class="titlestyle"><a href="m/groups/view/'.$result_principal_grp['uri'].'">'.$result_principal_grp['title'].'</a></p>
      </div>
      <div class="bxcategorybox">
        <p class="bxcategorystyle">'.$result_principal_grp['categories'].'</p>
      </div>
      <div class="datebox">
        <p class="datestyle">'._t('_wipM_deadline').' : '.DataTime($result_principal_grp['Deadline'],$dateFormatC).'</p>
      </div>
    </div>
    <div class="bottomElement">
      <div class="fansbox">
        <p class="fanscount">'.$result_principal_grp['fans_count'].'</p>
        <div class="fanspic"></div>
      </div>
    </div>
  </div>';
  }

  if(($result_principal_grp['OpenGrp']==0) and ($result_principal_grp['Deadline'] > time())){
  $sql_modif2_grp = "UPDATE bx_groups_main SET ModifGrp=1 WHERE OpenGrp=0 AND Deadline > ".time()."";
  $req_modif2_grp = mysql_query($sql_modif2);
  $ModifTitle = $result_principal_grp['title'];
  $ModifTitleShort = substr($ModifTitle,0,25);
    if ($result_principal_grp['author_id'] != $memberiID){
  echo '
  <div class="Modboxcontent">
    <div class="topElement">
      <div class="typebox">
        <p class="Modtypestyle">'._t($result_principal_grp['LKey']).'</p>
      </div>
      <div class="picbox">
      <div class="ModpicOFF"></div>
      </div>
    </div>
    <div class="middleElement">
      <div class="titlebox">
        <p class="Modtitlestyle"><a href="m/groups/view/'.$result_principal_grp['uri'].'">'.$ModifTitleShort.'</a>';
  if(strlen($ModifTitle) > 25){
  echo '<span style="color:#9BB9D2;">...</span>';
  }
  echo '  </p></div>
      <div class="modifiedmsgbox">
        <p class="modifiedmsgstyle">'._t('_wipM_modified').'</p>
      </div>
      <div class="requestbox">
        <p class="requeststyle"><a href="mail.php?mode=compose&recipient_id='.$result_principal_grp['author_id'].'">'._t('_wipM_modified_sendmsg').'</a>
      </div>
    </div>
    <div class="bottomElement">
      <div class="fansbox">
        <p class="fanscount">'.$result_principal_grp['fans_count'].'</p>
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
        <p class="Modtypestyle">'._t($result_principal_grp['LKey']).'</p>
      </div>
      <div class="picbox">
      <div class="ModpicOFF"></div>
      </div>
    </div>
    <div class="middleElement">
      <div class="titlebox">
        <p class="Modtitlestyle"><a href="m/groups/view/'.$result_principal_grp['uri'].'">'.$ModifTitleShort.'</a>';
  if(strlen($ModifTitle) > 25){
  echo '<span style="color:#9BB9D2;">...</span>';
  }
  echo '  </p></div>
      <div class="modifiedmsgbox">
        <p class="modifiedmsgstyle">'._t('_wipM_modified_responsible').'</p>
      </div>
      <div class="editbox">
        <p class="editstyle"><a href="m/groups/view/'.$result_principal_grp['uri'].'">'._t('_wipM_edititem').'</a></p>
      </div>
    </div>
    <div class="bottomElement">
      <div class="fansbox">
        <p class="fanscount">'.$result_principal_grp['fans_count'].'</p>
        <div class="Modfanspic"></div>
      </div>
    </div>
  </div>';
    }
  }

  if(($result_principal_grp['OpenGrp']==1) and ($result_principal_grp['Deadline'] < time())){
  $sql_past_grp = "UPDATE bx_groups_main SET OpenGrp=0 WHERE OpenGrp=1 AND Deadline < ".time()."";
  $req_past_grp = mysql_query($sql_past_grp);
  }

  if(($result_principal_grp['OpenGrp']==0) and ($result_principal_grp['Deadline'] < time())){
  $sql_reset_grp = "UPDATE bx_groups_main SET ModifGrp=0 WHERE ModifGrp=1 AND Deadline < ".time()."";
  $req_reset_grp = mysql_query($sql_reset_grp);
  }
}
  $varY = 0;
  $counttab_grp = count($tabmodif_grp);
  $tab2D_grp = array($tableau_grp,$tabmodif_grp);
  for($varY=0;$varY<$counttab_grp;$varY++){
  $calc_grp = $tab2D_grp[0][$varY]+$tab2D_grp[1][$varY];
  }
  if ($calc_grp == 0){
  echo '
  <div class="noitems">
    <br />
    <p>'._t('_wipM_no_projects_scheduled').'</p>
    <a href="m/groups/browse/my&bx_groups_filter=add_group">'._t('_wipM_create_a_group').'</a>
    <br />
    <br />
  </div>';
  }
echo '</div>';
}
if ($choix == 6)
  {
  echo 'Products';
  }
if ($choix == 8)
  {
  echo 'Services';
  }
// if ($choix == 4)
//   {
//   echo 'Organizations';
//   }

?>