<?php
require_once( '../inc/header.inc.php' ); // connect BDD
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

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

$memberiID=(int)$_COOKIE['memberID'];

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
				<p class="titlestyle"><a href="m/groups/view/'.$result_principal_news['PostUri'].'">'.$result_principal_news['PostCaption'].'</a></p>
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
				<p class="Modtitlestyle"><a href="m/groups/view/'.$result_principal_news['PostUri'].'">'.$ModifTitleShort.'</a>';
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
				<p class="Modtitlestyle"><a href="m/groups/view/'.$result_principal_news['PostUri'].'">'.$ModifTitleShort.'</a>';
	if(strlen($ModifTitle) > 25){
	echo '<span style="color:#9BB9D2;">...</span>';
	}
	echo '	</p></div>
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
?>