<?php
require_once( '../inc/header.inc.php' );
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
	echo '	</p></div>
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
?>


















