<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolTwigModuleDb');
 
define ('TF_FORUM',			 '`'.$gConf['db']['prefix'].'forum`');
define ('TF_FORUM_CAT',		 '`'.$gConf['db']['prefix'].'forum_cat`');
define ('TF_FORUM_POST',	 '`'.$gConf['db']['prefix'].'forum_post`');
define ('TF_FORUM_TOPIC',	 '`'.$gConf['db']['prefix'].'forum_topic`');
define ('TF_FORUM_VOTE',	 '`'.$gConf['db']['prefix'].'forum_vote`');
define ('TF_FORUM_REPORT',	 '`'.$gConf['db']['prefix'].'forum_report`');
define ('TF_FORUM_FLAG',	 '`'.$gConf['db']['prefix'].'forum_flag`');
 


/*
 * Alerts module Data
 */
class BxAlertsDb extends BxDolTwigModuleDb {	

	/*
	 * Constructor.
	 */
	function BxAlertsDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
 
	}
 
    function deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `{$this->_sFieldAuthorId}` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldId}` = $iId $sWhere LIMIT 1")))
            return false;
 
        return true;
    } 

 	function getVoteUnit ($sTable) {  
		$sUnit = $this->getOne("SELECT `ObjectName` FROM `sys_objects_vote` WHERE `TriggerTable`='$sTable' LIMIT 1");
		
		return $sUnit;
	}

 	function getID ($sNick) {  
		$iId = $this->getOne("SELECT `ID` FROM `Profiles` WHERE `NickName`='$sNick' LIMIT 1");
		
		return $iId;
	}
 
 	function getUnits() {  
         //return $this->getPairs ("SELECT `unit`, `group` FROM `" . $this->_sPrefix . "actions` WHERE `active`=1", 'unit', 'group');
         $aDb = $this->getAll ("SELECT `unit`, `group` FROM `" . $this->_sPrefix . "actions` WHERE `active`=1");
	
		 $aUnits = array();
		 foreach($aDb as $aEachUnit){
			$aUnits[$aEachUnit['unit']] = _t($aEachUnit['group']); 
		 }

		 return $aUnits;
  	}
  
 	function getAllNotify($sType) {
		return $this->getAll("SELECT `id`,`author_id`,`uri`,`phrase`,`unit`,`notify`,`search`,`status`,`created` FROM `" . $this->_sPrefix . "main` WHERE `status`='approved' AND `notify`='$sType'");  
	}

 	function getAlertsActions($bActiveOnly=true) {
		
		if($bActiveOnly)
			$sWhere = "WHERE `active`=1 ";
  
		$arrActions = $this->getAll("SELECT * FROM `" . $this->_sPrefix . "actions` {$sWhere} ORDER BY `group`, `unit`");
	 
		return $arrActions;
	}
    
	function getAllFaves($iRecipientId) {
	  
		$vProfiles = db_res("SELECT `Profile` FROM `sys_fave_list` WHERE `ID`='{$iRecipientId}'");
  
		$aFaves = array();
 
		while ($aProfiles = mysql_fetch_assoc($vProfiles)) {
			$aFaves[] = $aProfiles['Profile'];
		}

		return $aFaves; 
	}
	 
	function getAllFriends($iID) {
  
		$sqlQuery = "
			SELECT p.`ID`
			FROM `Profiles` AS p
			LEFT JOIN `sys_friend_list` AS f1 ON (f1.`ID` = p.`ID` AND f1.`Profile` ='{$iID}' AND `f1`.`Check` = 1)
			LEFT JOIN `sys_friend_list` AS f2 ON (f2.`Profile` = p.`ID` AND f2.`ID` ='{$iID}' AND `f2`.`Check` = 1)
			WHERE 
			(f1.`ID` IS NOT NULL OR f2.`ID` IS NOT NULL)
		";

		$aFriends = array();

		$vProfiles = db_res($sqlQuery);
		while ($aProfiles = mysql_fetch_assoc($vProfiles)) {
			$aFriends[] = $aProfiles['ID'];
		}

		return $aFriends;
	}

	function isOwnerFave($iRecipientId, $iActionMemberId) {
	
		if($iRecipientId == $iActionMemberId) 
			return true;
	  
		$cnt = db_arr("SELECT SUM(`Check`) AS 'cnt' FROM `sys_fave_list` WHERE `ID`='{$iRecipientId}' AND `Profile`='{$iActionMemberId}'");
 
		return ($cnt['cnt'] > 0 ? true : false);
	}
	 
	function isOwnerFriend($iRecipientId, $iActionMemberId)
	{
		if($iRecipientId == $iActionMemberId) 
			return true;
	  
		$cnt = db_arr("SELECT SUM(`Check`) AS 'cnt' FROM `sys_friend_list` WHERE `ID`='".$iRecipientId."' AND `Profile`='".$iActionMemberId."' OR `ID`='".$iActionMemberId."' AND `Profile`='".$iRecipientId."'");
 
		return ($cnt['cnt'] > 0 ? true : false);
	}
  
	function getObjectOwner($sUnit, $iItemId) {
	 
		$aRow = $this->getRow("SELECT `table`, `id_field`, `owner_field` FROM `" . $this->_sPrefix . "field_mapping` WHERE `unit`='$sUnit' LIMIT 1");
		$sOwnerFld = $aRow['owner_field'];
		$sIDFld = $aRow['id_field'];
		$sTableFld = $aRow['table'];
		 
		if($sIDFld) {
			$iOwner = $this->getOne("SELECT `".$sOwnerFld."` FROM `".$sTableFld."` WHERE `".$sIDFld."`='$iItemId' LIMIT 1");

			return $iOwner;
		} 

		return 0; 
 	}
  
	function removeProfileEntries($iProfileId) { 
		 $this->query("DELETE FROM `" . $this->_sPrefix . "main` WHERE `author_id` = '$iProfileId'"); 
 	}


/*******************************/
 
	function getTopicOLD ($iTopicId){
	
		return $this->getRow ( "SELECT `topic_id`, `topic_uri`, `topic_title`, `forum_title`, `forum_desc`, `forum_type`, `forum_uri`, f1.`forum_id`, `cat_id`, `first_post_user` FROM " . TF_FORUM_TOPIC . " AS f1 INNER JOIN " . TF_FORUM . " USING (`forum_id`) WHERE f1.`topic_id` = '$iTopicId' LIMIT 1");
    }
 
 	function getPost ($iTopicId){ 
		global $gConf;

		return $this->getRow ( "SELECT `post_text`  FROM `" . $gConf['db']['prefix'] . "forum_post` WHERE `topic_id` = '$iTopicId' ORDER BY `when` DESC LIMIT 1");
    }

 	function getTopic ($iTopicId, $sAction){ 
		global $gConf;
 
		if($sAction=='new_topic')
			$arrTopic = $this->getRow("SELECT `topic_id`, `forum_id`, `topic_title`, `topic_uri`, `last_post_user` FROM `" . $gConf['db']['prefix'] . "forum_topic` WHERE `topic_uri` = '" . $iTopicId . "' LIMIT 1"); 
		else
			$arrTopic = $this->getRow("SELECT `topic_id`, `forum_id`, `topic_title`, `topic_uri`, `last_post_user` FROM `" . $gConf['db']['prefix'] . "forum_topic` WHERE `topic_id` = '" . $iTopicId . "' LIMIT 1"); 
	
		return $arrTopic;
	}
 
	function AllowAlerts($sUnit, $sPeriod='now'){
	
		$iOwnerId = getLoggedId();
		if(!$iOwnerId)
			return false;
		
		$iActive = (int)$this->getOne("SELECT `id` FROM `" . $this->_sPrefix . "actions` WHERE  `unit`='$sUnit' AND `active`=1 LIMIT 1"); 

		if(!$iActive)
			return false;
 
		return (int)$this->getOne("SELECT `id` FROM `" . $this->_sPrefix . "main` WHERE  `author_id`=$iOwnerId AND `notify`='$sPeriod' AND (`unit` LIKE '%{$sUnit};%' OR `unit` LIKE '%;{$sUnit}%') LIMIT 1"); 
	}
   
	function getKeyWords($iRecipientId, $sUnit='', $sPeriod='now'){
  
		if($sUnit)
			return $this->getAll("SELECT `id`,`author_id`,`uri`,`deliver`,`phrase`,`search`,`unit` FROM `" . $this->_sPrefix . "main` WHERE  `author_id`=$iRecipientId AND `notify`='$sPeriod'  AND `status`='approved' AND (`unit` = '{$sUnit}' OR `unit` LIKE '%{$sUnit};%' OR `unit` LIKE '%;{$sUnit}%')");  
		else 
			return $this->getAll("SELECT `id`,`author_id`,`uri`,`deliver`,`phrase`,`search`,`unit` FROM `" . $this->_sPrefix . "main` WHERE  `author_id`=$iRecipientId AND `notify`='$sPeriod'  AND `status`='approved'");  
	}
  
	function getObjectRecord($sUnit, $iItemId) {
  		
		$aRecord =  array();  

		$aRow = $this->getRow("SELECT `table`, `id_field`, `owner_field`, `title_field`, `desc_field`, `uri_field`,`view_uri`,`class` FROM `" . $this->_sPrefix . "field_mapping` WHERE `unit`='$sUnit' LIMIT 1");

		$sOwnerFld = $aRow['owner_field'];
		$sTitleFld = $aRow['title_field'];
		$sDescFld = $aRow['desc_field']; 
		$sUriFld = $aRow['uri_field'];
		$sIDFld = $aRow['id_field'];
		$sTableFld = $aRow['table'];
		
		if($sIDFld) {
			$aRecord = $this->getRow("SELECT `".$sTitleFld."` as `title`, `".$sDescFld."` as `desc`, `".$sUriFld."` as `uri`, `".$sOwnerFld."` as `author_id` FROM `".$sTableFld."` WHERE `".$sIDFld."`='$iItemId' LIMIT 1");
		} 

		$aRecord['class'] = $aRow['class']; 
		$aRecord['view_uri'] = $aRow['view_uri']; 

		return $aRecord;  
 	} 
 
	function getObjectRecords($sUnit) {
  
		$iLastCronRun = (int)$this->getOne("SELECT `last_run` FROM `" . $this->_sPrefix . "cron` LIMIT 1");

		$aRow = $this->getRow("SELECT `table`, `id_field`, `owner_field`, `title_field`, `desc_field`, `uri_field`, `create_field`, `view_uri`, `class` FROM `" . $this->_sPrefix . "field_mapping` WHERE `unit`='$sUnit' LIMIT 1");

		$sOwnerFld = $aRow['owner_field'];
		$sTitleFld = $aRow['title_field'];
		$sDescFld = $aRow['desc_field']; 
		$sUriFld = $aRow['uri_field'];
		$sIDFld = $aRow['id_field'];
		$sCreateFld = $aRow['create_field'];
	 	$sTableFld = $aRow['table'];
 		$sClass = $aRow['class']; 
		$sViewUri = $aRow['view_uri']; 

		if(empty($aRow))
			return array();

		$aRecord = $this->getAll("SELECT '$sClass' as `class`, '$sViewUri' as `view_uri`, `".$sIDFld."` as `id`,  `".$sTitleFld."` as `title`, `".$sDescFld."` as `desc`, `".$sUriFld."` as `uri`, `".$sOwnerFld."` as `author_id` FROM `".$sTableFld."` WHERE `".$sCreateFld."` > $iLastCronRun ORDER BY `".$sCreateFld."` DESC");
	   
		return $aRecord;  
 	} 
  
	//override forum delete since there is no forum
	function deleteForum ($iEntryId){
		//
	}

 	function getNotifyActions($bActiveOnly=true) {
		
		if($bActiveOnly)
			$sWhere = "WHERE `active`=1 ";
   
		$arrActions = $this->getAll("SELECT * FROM `" . $this->_sPrefix . "actions` {$sWhere} ORDER BY `group`, `unit`");
	 
		return $arrActions;
	}

}

?>