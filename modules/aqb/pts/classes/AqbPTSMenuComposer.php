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

class AqbPTSMenuComposer {
	var $_sMenuTable;
	var $_sParseURL;
	var $_oDb;

	function AqbPTSMenuComposer($sTable, $sParseURL, $oDb) {
		$this->_sMenuTable = $sTable;
		$this->_sParseURL = $sParseURL;
		$this->_oDb = $oDb;
	}

	function constructMenuLayout() {
		if ($this->_sMenuTable == 'sys_menu_top') return $this->constructTopMenuLayout();
		elseif ($this->_sMenuTable == 'sys_menu_member') return $this->constructMemberMenuLayout();
		else return 'Not Implemented';
	}
	function constructTopMenuLayout() {
		$sTopQuery = "SELECT `ID`, `Name` FROM `{$this->_sMenuTable}` WHERE `Active`=1 AND `Type`='top' ORDER BY `Order`";
		$rTopItems = $this->_oDb->res( $sTopQuery );

		$sSysQuery = "SELECT `ID`, `Name` FROM `{$this->_sMenuTable}` WHERE `Active`=1 AND `Type`='system' ORDER BY `Order`";
		$rSysItems = $this->_oDb->res( $sSysQuery );

		$aTopItems = array();
		$aCustomItems = array();
		$aSystemItems = array();
		while( $aTopItem = mysql_fetch_assoc( $rTopItems ) ) {
			$aTopItems[$aTopItem['ID']] = $aTopItem['Name'];
			$aCustomItems[$aTopItem['ID']] = array();
			$sQuery = "SELECT `ID`, `Name` FROM `{$this->_sMenuTable}` WHERE `Active`=1 AND `Type`='custom' AND `Parent`={$aTopItem['ID']} ORDER BY `Order`";

			$rCustomItems = $this->_oDb->res( $sQuery );
			while( $aCustomItem = mysql_fetch_assoc( $rCustomItems ) ) {
				$aCustomItems[$aTopItem['ID']][$aCustomItem['ID']] = $aCustomItem['Name'];
			}
		}

		while( $aSystemItem = mysql_fetch_assoc( $rSysItems ) ) {
			$aSystemItems[$aSystemItem['ID']] = $aSystemItem['Name'];
			$aCustomItems[$aSystemItem['ID']] = array();
			$sQuery = "SELECT `ID`, `Name` FROM `{$this->_sMenuTable}` WHERE `Active`=1 AND `Type`='custom' AND `Parent`={$aSystemItem['ID']} ORDER BY `Order`";

			$rCustomItems = $this->_oDb->res( $sQuery );
			while( $aCustomItem = mysql_fetch_assoc( $rCustomItems ) ) {
				$aCustomItems[$aSystemItem['ID']][$aCustomItem['ID']] = $aCustomItem['Name'];
			}
		}

		$sRet = '<table><tr>';

		foreach ($aTopItems as $iItemID => $sItemName) {
			$sRet .= '<td valign="top">';
			$sRet .= '<div class="aqb_top_menu_item"><a href="javascript:showMenuEditForm('.$iItemID.')">'.$sItemName.'</a></div>';
			foreach ($aCustomItems[$iItemID] as $iCustomItemID => $sCustomItemName) {
				$sRet .= '<div class="aqb_custom_menu_item"><a href="javascript:showMenuEditForm('.$iCustomItemID.')">'.$sCustomItemName.'</a></div>';
			}
			$sRet .= '</td>';
		}

		foreach ($aSystemItems as $iItemID => $sItemName) {
			$sRet .= '<td valign="top">';
			$sRet .= '<div class="aqb_system_menu_item">'.$sItemName.'</div>';
			foreach ($aCustomItems[$iItemID] as $iCustomItemID => $sCustomItemName) {
				$sRet .= '<div class="aqb_custom_menu_item"><a href="javascript:showMenuEditForm('.$iCustomItemID.')">'.$sCustomItemName.'</a></div>';
			}
			$sRet .= '</td>';
		}

		$sRet .= '</tr></table>';
		$sRet .= '<script language="javascript">var sParserURL = "'.$this->_sParseURL.'"</script>';


		return $sRet;
	}
	function constructMemberMenuLayout() {
		$sTopQuery = "SELECT `ID`, `Name` FROM `{$this->_sMenuTable}` WHERE `Active`='1' AND `Type` <> 'linked_item' ORDER BY `Position`, `Order`";
		$rTopItems = $this->_oDb->res( $sTopQuery );

		$aTopItems = array();
		while( $aTopItem = mysql_fetch_assoc( $rTopItems ) ) {
			$aTopItems[$aTopItem['ID']] = $aTopItem['Name'];
		}


		$sRet = '<table><tr>';

		foreach ($aTopItems as $iItemID => $sItemName) {
			$sRet .= '<td valign="top">';
			$sRet .= '<div class="aqb_top_menu_item"><a href="javascript:showMenuEditForm('.$iItemID.')">'.$sItemName.'</a></div>';
			$sRet .= '</td>';
		}

		$sRet .= '</tr></table>';
		$sRet .= '<script language="javascript">var sParserURL = "'.$this->_sParseURL.'"</script>';

		return $sRet;
	}

	function updateCache($sType, $sCacheFolder) {
		 $rCacheFile = @fopen($sCacheFolder.$sType.'_menu.inc', 'w');

		 $sCacheString = '';
		 $aMenuItems = $this->_oDb->getAll("SELECT `MenuItemID`, `ProfileTypesVisibility` FROM `aqb_pts_{$sType}_menu_visibility`");
		 foreach ($aMenuItems as $aMenuItem) {
		 	$sCacheString .= "\t{$aMenuItem['MenuItemID']} => {$aMenuItem['ProfileTypesVisibility']},\n";
		 }

		 @fputs($rCacheFile, "return array(\n{$sCacheString});");
		 @fclose($rCacheFile);
	}
	function loadCache($sType, $sCacheFolder, $bStopRecursion = false) {
		$sCacheFile = $sCacheFolder.$sType.'_menu.inc';

		$aRet = array();
		if (!file_exists( $sCacheFile ) or !$aRet = @eval( file_get_contents($sCacheFile) ) or !is_array($aRet)) {
           AqbPTSMenuComposer::updateCache($sType, $sCacheFolder);
           if (!$bStopRecursion) AqbPTSMenuComposer::loadCache($sType, $sCacheFolder, true);
		}


        return $aRet;
	}
}
?>