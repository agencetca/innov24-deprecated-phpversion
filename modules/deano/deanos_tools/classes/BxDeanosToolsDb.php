<?php
/***************************************************************************
* Date				: Sun August 1, 2010
* Copywrite			: (c) 2009, 2010 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Tools
* Product Version	: 1.8.4
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );


/*
* Quotes module Data
*/
class BxDeanosToolsDb extends BxDolModuleDb {	
	var $_oConfig;
	/*
	* Constructor.
	*/
	function BxDeanosToolsDb(&$oConfig) {
		parent::BxDolModuleDb();

		$this->_oConfig = $oConfig;
	}

	function getModuleVersion() {
		return $this->getOne("SELECT `version` FROM `sys_modules` WHERE `title`='Deanos Tools'");
	}

	function getMemberCount() {
		return $this->getOne("SELECT COUNT(`ID`) FROM `Profiles`");
	}

	function getMemberCount2($orderBy='NickName',$sSearch) {
		if ($sSearch == '') {
			$query = "SELECT COUNT(`ID`) FROM `Profiles` ORDER BY `Role` DESC,`$orderBy`";
		} else {
			$query = "SELECT COUNT(`ID`) FROM `Profiles` WHERE `NickName` LIKE '%$sSearch%' ORDER BY `Role` DESC,`$orderBy`";
		}
		return $this->getOne($query);
	}

	function getAdminCount() {
		return $this->getOne("SELECT COUNT(`ID`) FROM `Profiles` WHERE `Role` = 3");
	}

	function getNickName($id) {
		return $this->getOne("SELECT `NickName` FROM `Profiles` WHERE `ID`='$id'");
	}

	function getMembers($start,$count,$orderBy='NickName',$sSearch) {
		if ($sSearch == '') {
			$query = "SELECT `ID`,`NickName`,`Role` FROM `Profiles` ORDER BY `Role` DESC,`$orderBy` LIMIT $start,$count";
		} else {
			$query = "SELECT `ID`,`NickName`,`Role` FROM `Profiles` WHERE `NickName` LIKE '%$sSearch%' ORDER BY `Role` DESC,`$orderBy` LIMIT $start,$count";
		}
		return $this->getAll($query);
	}


	function setAdmin($id) {
		$query = "UPDATE `Profiles` SET `Role`=3 WHERE `ID`='$id'";
		$this->query($query);
	}

	function setMember($id) {
		$query = "UPDATE `Profiles` SET `Role`=1 WHERE `ID`='$id'";
		$this->query($query);
	}

	function isShoutBoxInstalled() {
		if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'bx_shoutbox_messages'")) > 0) {
			return true;
		} else {
			return false;
		}
	}
	function getShoutboxMessages() {
		$query = "SELECT * FROM `bx_shoutbox_messages`";
		return $this->getAll($query);
	}

	function deleteShoutboxMessage($dbid) {
		$query = "DELETE FROM `bx_shoutbox_messages` where `ID`=$dbid";
		$this->query($query);
	}

	function getTags($tagsStart, $tagsPerPage) {
		$query = "SELECT * FROM `sys_tags` ORDER BY `Tag` LIMIT $tagsStart, $tagsPerPage";
		return $this->getAll($query);
	}

	function getTagCount() {
		$query = "SELECT COUNT(*) FROM `sys_tags`";
		return $this->getOne($query);
	}

	function deleteTag($dbTag,$dbType) {
		$dbTagE = $this -> escape($dbTag);
		$query = "DELETE FROM `sys_tags` where `Tag`='$dbTagE' AND `Type`='$dbType'";
		$this->query($query);
	}

	function deleteMessages($senderID) {
		$query = "DELETE FROM `sys_messages` where `Sender`=$senderID";
		$this->query($query);
	}

	function getCopyrightID() {
		return $this->getOne("SELECT `ID` FROM `sys_localization_keys` where `Key`='_copyright'");
	}

	function getCopyrightText($dbid,$lKey) {
		$r = $this->getOne("SELECT `String` FROM `sys_localization_strings` where `IDKey`='$dbid' and `IDLanguage`='$lKey'");
		$r = htmlspecialchars($r);
		return $r;
	}

	function getLangKeys() {
		return $this->getAll("SELECT * FROM `sys_localization_languages`");
	}

	function getPages() {
		return $this->getAll("SELECT * FROM `sys_page_compose_pages`");
	}
	function getPHPBlocks() {
		return $this->getAll("SELECT * FROM `sys_page_compose` WHERE `Func`='PHP' ORDER BY `Page`");
	}

	function getPHPBlockData($iBlockID) {
		return $this->getRow("SELECT * FROM `sys_page_compose` WHERE `ID`='$iBlockID'");
	}

	function keyExists($sLKey) {
		return $this->getOne("SELECT `ID` FROM `sys_localization_keys` where `Key`='$sLKey'");
	}

	function getLangCat() {
		return $this->getOne("SELECT `ID` FROM `sys_localization_categories` where `Name`='Deano'");
	}

	function saveCopyrightText($dbid,$lKey,$crText) {
		$p = $crText;
		$this->query("UPDATE `sys_localization_strings` SET `String`='$p' where `IDKey`='$dbid' and `IDLanguage`='$lKey'");
	}

	function setPageWidth($iPageWidth) {
		if (is_numeric($iPageWidth)) {
			$sC = $iPageWidth . "px";
		} else {
			$sC = $iPageWidth;
		}
		$query = "UPDATE `sys_page_compose` SET `PageWidth`='$sC'";
		$this->query($query);
		$query = "UPDATE `sys_options` SET `VALUE` = '$sC' WHERE `Name`='main_div_width'";
		$this->query($query);
	}
	function insertPHPBlock($sPage,$sKey,$sText,$sCode) {
		$query = "INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES ('$sPage', '998px', '$sText', '$sKey', 0, 0, 'PHP', '$sCode', 1, 66, 'non,memb', 0)";
		$this->query($query);
	}

	function updatePHPBlock($iBlockID,$sLkey,$sPHPCode) {
		$query = "UPDATE `sys_page_compose` SET `Caption`='$sLkey', `Content`='$sPHPCode' WHERE `ID`='$iBlockID'";
		$this->query($query);
	}
	function deletePHPBlock($iBlockID) {
		$query = "DELETE FROM `sys_page_compose` WHERE `ID`='$iBlockID'";
		$this->query($query);
	}

	function createTimeStamp() {
		// this function creates a unix timestamp from the current date and time
		// ignoring the seconds.
		$iHour = date("H");
		$iMinute = date("i");
		$iTimeStamp = mktime($iHour,$iMinute,0);
		return $iTimeStamp;
	}

	function saveIP($sIP, $memID) {
		$curTime = $this->createTimeStamp();
		if ($memID > 0) {
			$sNickName = getNickName($memID);
		} else {
			$sNickName = "Guest";
		}
		$sNickName = $this -> escape($sNickName);
		$ipLong = ip2long($sIP);
		$query = "INSERT IGNORE INTO `dbcs_ip_address` (`member_id`, `nick_name`, `ip_address`, `ip_long`, `time_stamp`) VALUES ('$memID', '$sNickName', '$sIP', '$ipLong', '$curTime')";
		$this->query($query);
	}
	function getIPList($ipStart, $ipPerPage, $ipShowGuests, $ipShowMembers, $sNickSearch) {
		//echo $ipShowGuests;
		//exit;
		if ($sNickSearch == '') {
			$sSearch1 = "";
			$sSearch2 = "";
		} else {
			$sSearch1 = " WHERE `nick_name` LIKE '%$sNickSearch%'";
			$sSearch2 = " `nick_name` LIKE '%$sNickSearch%' AND";
		}
		if($ipShowGuests == 1 && $ipShowMembers == 1) {
			$sQuery = "SELECT * FROM `dbcs_ip_address`$sSearch1 ORDER BY `time_stamp` DESC LIMIT $ipStart, $ipPerPage";
		} elseif ($ipShowGuests == 1) {
			$sQuery = "SELECT * FROM `dbcs_ip_address` WHERE$sSearch2 `member_id`=0 ORDER BY `time_stamp` DESC LIMIT $ipStart, $ipPerPage";
		} elseif ($ipShowMembers == 1) {
			$sQuery = "SELECT * FROM `dbcs_ip_address` WHERE$sSearch2 `member_id`>0 ORDER BY `time_stamp` DESC LIMIT $ipStart, $ipPerPage";
		} else {
			return;
		}
		return $this->getAll($sQuery);

	}
	function getIPCount($ipShowGuests, $ipShowMembers, $sNickSearch) {
		if ($sNickSearch == '') {
			$sSearch1 = "";
			$sSearch2 = "";
		} else {
			$sSearch1 = " WHERE `nick_name` LIKE '%$sNickSearch%'";
			$sSearch2 = " `nick_name` LIKE '%$sNickSearch%' AND";
		}
		if($ipShowGuests == 1 && $ipShowMembers == 1) {
			$sQuery = "SELECT COUNT(*) FROM `dbcs_ip_address`$sSearch1";
		} elseif ($ipShowGuests == 1) {
			$sQuery = "SELECT COUNT(*) FROM `dbcs_ip_address` WHERE$sSearch2 `member_id`=0";
		} elseif ($ipShowMembers == 1) {
			$sQuery = "SELECT COUNT(*) FROM `dbcs_ip_address` WHERE$sSearch2 `member_id`>0";
		} else {
			return;
		}
		return $this->getOne($sQuery);
	}


	function getSettingsCategoryId($sCatName) {
		$sCatName = $this -> escape($sCatName);
		return $this -> getOne('SELECT `kateg` FROM `sys_options` WHERE `Name` = "' . $sCatName . '"');
	}

	function removeGuestAlert() {
		$hanID = $this -> getOne("SELECT `id` FROM `sys_alerts_handlers` WHERE `name`='dbcs_deanos_tools'");
		$sQuery = "DELETE FROM `sys_alerts` WHERE `handler_id` = '$hanID' AND `unit`='system'";
		$this->query($sQuery);
	}

	function addGuestAlert() {
		$hanID = $this -> getOne("SELECT `id` FROM `sys_alerts_handlers` WHERE `name`='dbcs_deanos_tools'");
		$sQuery = "INSERT INTO `sys_alerts` SET `unit`='system', `action`='begin',`handler_id`='$hanID'";
		$this->query($sQuery);
	}
	function checkGuestAlert() {
		$hanID = $this -> getOne("SELECT `id` FROM `sys_alerts_handlers` WHERE `name`='dbcs_deanos_tools'");
		$sQuery = "SELECT `id` FROM `sys_alerts` WHERE `unit`='system' AND `handler_id`='$hanID'";
		return $this->getOne($sQuery);
	}


}

?>