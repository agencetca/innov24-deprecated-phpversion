<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.modloaded.com
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
* see license.txt file; if not, write to marketing@modloaded.com
***************************************************************************/

bx_import('BxDolTwigModuleDb');

/*
 * Pages module Data
 */
class MlPagesDb extends BxDolTwigModuleDb {	

	/*
	 * Constructor.
	 */
	function MlPagesDb(&$oConfig) {
		parent::BxDolTwigModuleDb($oConfig);

        $this->_sTableMain = 'main';
        $this->_sTableMediaPrefix = '';
        $this->_sFieldId = 'id';
        $this->_sFieldAuthorId = 'author_id';
        $this->_sFieldUri = 'uri';
        $this->_sFieldTitle = 'title';
        //$this->_sFieldDescription = 'Description';
        $this->_sFieldTags = 'tags';
        $this->_sFieldThumb = 'thumb';
        $this->_sFieldStatus = 'status';
        $this->_sFieldFeatured = 'featured';
        $this->_sFieldCreated = 'created';
        //$this->_sFieldJoinConfirmation = 'JoinConfirmation';
        $this->_sFieldFansCount = 'fans_count';
        $this->_sTableFans = 'fans';
        $this->_sTableAdmins = 'admins';        
        $this->_sFieldAllowViewTo = 'allow_view_page_to';
	}
		function getCountUserEventsByPage($iUserId, $sEvents = '')
 		{
 			return $this->getOne("SELECT COUNT(`id`) FROM `bx_events_main` WHERE `author_id` = {$iUserId} AND `id` IN ({$sEvents}) AND `status` = 'approved'");
 		}
 		function getCountUserEvents($iUserId, $sEvents = '')
 		{
 			return $this->getOne("SELECT COUNT(`id`) FROM `bx_events_main` WHERE `author_id` = {$iUserId} AND `id` NOT IN ({$sEvents}) AND `status` = 'approved'");
 		}
		function getEventInfo($iEventId)
		{
 			return $this->getRow("SELECT `id`, `title`, `uri`, `thumb`, `Date` FROM `bx_events_main` WHERE `id` = {$iEventId} AND `status` = 'approved'");
		}
		function getUserEventsByPage($iUserId, $sEvents = '', $iPage, $iPerPage)
 		{
 			return $this->getAll("SELECT `id`, `title`, `uri`, `thumb`, `Date` FROM `bx_events_main` WHERE `author_id` = {$iUserId} AND `id` IN ({$sEvents}) AND `status` = 'approved' LIMIT {$iPage}, {$iPerPage}");
 		}
 		function getUserEvents($iUserId, $sEvents = '', $iPage, $iPerPage)
 		{
 			return $this->getAll("SELECT `id`, `title`, `uri`, `thumb`, `Date` FROM `bx_events_main` WHERE `author_id` = {$iUserId} AND `id` NOT IN ({$sEvents}) AND `status` = 'approved' LIMIT {$iPage}, {$iPerPage}");
 		}
		function updatePageEvents($iID, $sPageEvents)
		{
			$this->query("UPDATE `ml_pages_main` SET `Events` = '{$sPageEvents}' WHERE `id` = {$iID} LIMIT 1");
		}
		function getPageWebsite($iPageId)
		{
			return $this->getRow("SELECT * FROM `ml_pages_websites` WHERE `entry_id` = '{$iPageId}' LIMIT 1");
		}
		function insertPageWebsite($iPageId, $sWebsite, $sWidth, $sHeight, $iPageWidth)
		{
			$this->query("INSERT INTO `ml_pages_websites` (`entry_id`, `website`, `height`, `width`, `page_width`) VALUES ('{$iPageId}', '{$sWebsite}', '{$sHeight}', '{$sWidth}', '{$iPageWidth}') ON DUPLICATE KEY UPDATE `website` = '{$sWebsite}', `height` = '{$sHeight}', `width` = '{$sWidth}', `page_width` = '{$iPageWidth}'");
		}
		function getMainCategories()
		{
			return $this->getAll("SELECT `ID`, `Name` FROM `ml_pages_categories` WHERE `Parent` = 0"); 
		}
		function getPageLocation($iPageId)
		{
			return $this->getOne("SELECT `location` FROM `ml_pages_locations` WHERE `entry_id` = '{$iPageId}' LIMIT 1");
		}
		function insertPageLocation($iPageId, $sLocation)
		{
			$this->query("INSERT INTO `ml_pages_locations` (`entry_id`, `location`) VALUES ('{$iPageId}', '{$sLocation}') ON DUPLICATE KEY UPDATE `location` = '{$sLocation}'");
		} 
		function getCategories($iParent = 0)
		{ 
	 		$aAllEntries = $this->getAll("SELECT `ID`, `Name` FROM `ml_pages_categories` WHERE `Parent` = {$iParent}"); 
			return $aAllEntries; 
		}
		function getTotalFans($iPageId)
		{
			if (!$iPageId) return;
 			return $this->getOne("SELECT COUNT(`Profiles`.`id`) AS Total FROM `ml_pages_fans` LEFT JOIN `Profiles` ON `Profiles`.`id` = `ml_pages_fans`.`id_profile` WHERE `confirmed` = 1 AND `id_entry` = {$iPageId} AND `Profiles`.`status` = 'Active' ORDER BY RAND() LIMIT 10");
		}
		function getPageFans($iPageId)
		{
			if (!$iPageId) return;
 			return $this->getAll("SELECT `Profiles`.`id`, `Profiles`.`NickName`, `Profiles`.`Avatar` FROM `ml_pages_fans` LEFT JOIN `Profiles` ON `Profiles`.`id` = `ml_pages_fans`.`id_profile` WHERE `confirmed` = 1 AND `id_entry` = {$iPageId} AND `Profiles`.`status` = 'Active' ORDER BY RAND() LIMIT 10");
		}
		function getPageDataById( $iID )
		{
			$iID = (int)$iID;
			return $this->getRow( "SELECT * FROM `ml_pages_main` WHERE `id` = '$iID'" );
		}
		function getPageInfoByUri( $sUri ) {
			return $this->getRow( "SELECT * FROM `ml_pages_main` WHERE `uri` = '{$sUri}'");
		}	
		function getPageInfo( $iPageID ) {
			return db_assoc_arr( "SELECT * FROM `ml_pages_main` WHERE `id` = " . (int)$iPageID );
		}	
		function getPreValuesCount ($sKey) {
			return $this->getOne("SELECT COUNT(*) FROM `ml_pages_pre_values`  WHERE `Key` = '$sKey'");
		}
		function getSubCategory($sKey)
		{
			//$sqlQuery = "SELECT `Value`, `LKey` FROM `ml_pages_pre_values` WHERE `Key` = '{$sKey}'";
			$sqlQuery = "SELECT `ID`, `Caption` FROM `ml_pages_categories` WHERE `Parent` = {$sKey}";
			return $this->getAll($sqlQuery);
		}
		function getMainCategory()
		{
			//$sqlQuery = "SELECT DISTINCT `Key` FROM `ml_pages_pre_values`";
			$sqlQuery = "SELECT `ID`, `Caption`, `Icon` FROM `ml_pages_categories` WHERE `Parent` = 0";
			return $this->getAll($sqlQuery);
		}
		function getPreKeys()
		{
			$sqlQuery = "SELECT DISTINCT `Key` FROM `ml_pages_pre_values`";
			return $this->getAllWithKey($sqlQuery, 'Key');
		}
		function getPreValues ($sList) {
			$sqlQuery = "SELECT * FROM `ml_pages_pre_values` WHERE `Key` = '$sList'";
			return $this->getAllWithKey($sqlQuery, 'Value');
		}
    function getUpcomingPage ($isFeatured) {
        $sWhere = '';
        if ($isFeatured) 
            $sWhere = " AND `{$this->_sFieldFeatured}` = '1' ";                
        return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . "main` WHERE `status` = 'approved' AND `{$this->_sFieldAllowViewTo}` = '" . BX_DOL_PG_ALL . "' $sWhere ORDER BY `Date` ASC LIMIT 1");
    }

    function getEntriesByMonth ($iYear, $iMonth, $iNextYear, $iNextMonth) {
        return $this->getAll ("SELECT *, DAYOFMONTH(FROM_UNIXTIME(`PageStart`)) AS `Day`
            FROM `" . $this->_sPrefix . "main`
            WHERE `status` = 'approved'");
    }

    function deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        if ($iRet = parent::deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin)) {
            $this->query ("DELETE FROM `" . $this->_sPrefix . "fans` WHERE `id_entry` = $iId");
            $this->deleteEntryMediaAll ($iId, 'images');
            $this->deleteEntryMediaAll ($iId, 'videos');
            $this->deleteEntryMediaAll ($iId, 'sounds');
            $this->deleteEntryMediaAll ($iId, 'files');
        }
        return $iRet;
    }

	function getAllCatsInfo() {
		$sSQL = "
			SELECT *
			FROM `ml_pages_categories`
			WHERE `Parent` = '0'
			ORDER BY `Name` ASC
		";
		$vSqlRes = db_res($sSQL);
		return $vSqlRes;
	}

	function getAllSubCatsInfo($iID) {
		$sSQL = "
			SELECT * FROM `ml_pages_categories`
			WHERE `Parent` = {$iID}
			ORDER BY `Name` ASC
		";
		return db_res($sSQL);
	}

	function getCountOfAdsInSubCat($iID) 
	{
		$sAdsCntSQL = "
			SELECT COUNT(`ml_pages_main`.`id`) AS 'Count'
			FROM `ml_pages_main`
            INNER JOIN `ml_pages_categories` ON `ml_pages_main`.`category` = `ml_pages_categories`.`id`
			WHERE `ml_pages_categories`.`id`='{$iID}'
			{$sTimeRestriction}";

		return (int)$this->getOne($sAdsCntSQL);
	}


}

?>
