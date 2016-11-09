<?php
/***************************************************************************
*
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

bx_import('BxDolModuleDb');

class AqbPTSDb extends BxDolModuleDb {
	/*
	 * Constructor.
	 */
	function AqbPTSDb(&$oConfig) {
		parent::BxDolModuleDb($oConfig);

		$this->_oConfig = &$oConfig;
	}

	function getMenuItemVisibility($sType, $iMenuItemID) {
		$sTableName = "aqb_pts_{$sType}_menu_visibility";
		$iRes = $this->getOne("SELECT `ProfileTypesVisibility` FROM {$sTableName} WHERE `MenuItemID` = {$iMenuItemID} LIMIT 1");
		$iVis = $iRes !== false ? $iRes : 1073741823;
		$sName = $this->getOne("SELECT `Name` FROM `sys_menu_{$sType}` WHERE `ID` = {$iMenuItemID} LIMIT 1");
		return array($sName, $iVis);
	}
	function setMenuItemVisibility($sType, $iMenuItemID, $iMemLevels) {
		$sTableName = "aqb_pts_{$sType}_menu_visibility";
		$this->query("REPLACE {$sTableName} SET `ProfileTypesVisibility` = {$iMemLevels}, `MenuItemID` = {$iMenuItemID}");
	}
	function getPageBlockVisibility($iID) {
		$aData = $this->getRow("SELECT `ProfileTypesVisibility`, `ProfileTypes` FROM `aqb_pts_page_blocks_visibility` WHERE `PageBlockID` = {$iID} LIMIT 1");
		$iVis = count($aData) ? $aData['ProfileTypesVisibility'] : 1073741823;
		$iRel = count($aData) ? $aData['ProfileTypes'] : 1073741823;
		$sName = $this->getOne("SELECT `Caption` FROM `sys_page_compose` WHERE `ID` = {$iID} LIMIT 1");
		return array(_t($sName), $iVis, $iRel);
	}
	function setPageBlockVisibility($iID, $iVis, $iRel) {
		$this->query("REPLACE `aqb_pts_page_blocks_visibility` SET `ProfileTypesVisibility` = {$iVis}, `ProfileTypes` = {$iRel}, `PageBlockID` = {$iID}");
	}
	function getPageNameOfBlock($iID) {
		return $this->getOne("SELECT `Page` FROM `sys_page_compose` WHERE `ID` = {$iID} LIMIT 1");
	}
	function getPFItemRelevancy($iItemID) {
		$sFieldName = $this->getOne("SELECT `Name` FROM `sys_profile_fields` WHERE `ID` = {$iItemID} LIMIT 1");
		$iRel = $this->getOne("SELECT `ProfileTypes` FROM `aqb_pts_profile_fields` WHERE `FieldID` = {$iItemID} LIMIT 1");
		$iRel = $iRel !== false ? $iRel : 1073741823;
		return array($sFieldName, $iRel);
	}
	function setPFItemRelevancy($iItemID, $iRel) {
		$this->query("REPLACE `aqb_pts_profile_fields` SET `ProfileTypes` = {$iRel}, `FieldID` = {$iItemID}");
	}
	function getPFItemsRelevancy() {
		return $this->getAll("SELECT `FieldID`, `ProfileTypes` FROM `aqb_pts_profile_fields`");
	}
	function getAllprofileTypes() {
		return $this->getAll("SELECT `ID`, `Name` FROM `aqb_pts_profile_types` ORDER BY `ID`");
	}
	function getFieldsLayout($iProfileType) {
		return $this->getAll("	SELECT `sys_profile_fields`.`ID`, `Name`,  `aqb_pts_search_result_layout`.`FieldID`, IF(`ProfileTypes` is null, 1073741823, `ProfileTypes`) as `ProfileTypes`
								FROM `sys_profile_fields`
								LEFT OUTER JOIN `aqb_pts_profile_fields` ON `sys_profile_fields`.`ID` = `aqb_pts_profile_fields`.`FieldID`
								LEFT OUTER JOIN (
									SELECT *
									FROM `aqb_pts_search_result_layout`
									WHERE `ProfileType` = {$iProfileType}
									) AS `aqb_pts_search_result_layout`
									ON `aqb_pts_search_result_layout`.`FieldID` = `sys_profile_fields`.`ID`
								WHERE `Type` <> 'block' AND `Type` <> 'system' AND (`ProfileTypes` & {$iProfileType} OR `ProfileTypes` IS NULL)
								ORDER BY `ID`");
	}
	function getFieldsByRows($iProfileType) {
		$aRows = array();
		$rRows = $this->res("	SELECT `FieldID`, `name`, `row`, `col`
								FROM `aqb_pts_search_result_layout` JOIN `sys_profile_fields` ON `aqb_pts_search_result_layout`.`FieldID` = `sys_profile_fields`.`ID`
								WHERE `ProfileType` = {$iProfileType}
								ORDER BY `row`");

		while (($row = mysql_fetch_row($rRows))) {
			$aRows[$row[2]][$row[3]] = array($row[0], $row[1]);
		}
		return $aRows;
	}
	function getResultedFieldsLayout($iProfileType) {
		return $this->getAll("	SELECT `sys_profile_fields`.*, `row`, `col`
								FROM (SELECT * FROM `aqb_pts_search_result_layout` WHERE `ProfileType` = {$iProfileType}) `aqb_pts_search_result_layout`
								JOIN `sys_profile_fields` ON `sys_profile_fields`.`ID` = `aqb_pts_search_result_layout`.`FieldID`
								ORDER BY `row`");
	}
}
?>