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

bx_import('BxDolModule');
bx_import('BxDolProfileFields');

require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );
require_once(BX_DIRECTORY_PATH_INC . "prof.inc.php");

//a fix for stupid IE to prevent ajax requests being cached
if (strpos($_SERVER['REQUEST_URI'], 'aqb_pts') !== false and isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) {
	send_headers_page_changed();
}
//a fix for stupid IE to prevent ajax requests being cached

class AqbPTSModule extends BxDolModule {
	var $aLayouts = array();
	var $oPV;
	var $sHPBlockTemplate = "if (BxDolRequest::serviceExists('aqb_pts', 'get_members_block')) return BxDolService::call('aqb_pts', 'get_members_block', array(__pt__));";
	/**
	 * Constructor
	 */
	function AqbPTSModule($aModule) {
	    parent::BxDolModule($aModule);
	    $this->oPV = new BxDolProfileFields( 100 );
	}

	//--- Pages ---//
	function actionGetPageProfileTypes() {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$sRet = DesignBoxAdmin(_t('_aqb_pts_profile_types'), $this->actionGetProfileTypes(true));
		$sRet .= DesignBoxAdmin(_t('_aqb_pts_new_profile_type'), $this->_oTemplate->displayAddTypeForm());
		$sRet .= DesignBoxAdmin(_t('_aqb_pts_migration_tool'), $this->getMigrationTool());
		$sRet .= DesignBoxAdmin(_t('_aqb_pts_notes'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => _t('_aqb_pts_notes_text'))));

		return $sRet;
	}
	function actionGetPageProfileFields() {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();
		return $this->_oTemplate->displayProfileFieldsCompose();
	}
	function actionGetPageTopMenu() {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();
		return $this->_oTemplate->displayTopMenuCompose();
	}
	function actionGetPageMemberMenu() {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();
		return $this->_oTemplate->displayMemberMenuCompose();
	}
	function actionGetPagePageBlocks($sPage = '') {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();
		return $this->_oTemplate->displayPageCompose($sPage);
	}
	function actionGetPageSearchResult($iProfileType = 0, $bIsAjax = false) {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$iProfileType = $iProfileType > 0 ? intval($iProfileType) : $this->_oDb->getOne("SELECT MIN(`ID`) FROM `aqb_pts_profile_types`");
		$iProfileType = intval($iProfileType);

		$aProfileTypes = $this->_oDb->getAllprofileTypes();

		$iMaxRow = $this->_oDb->getOne("SELECT MAX(`row`) FROM `aqb_pts_search_result_layout` WHERE `ProfileType` = {$iProfileType}");

		$aFields = $this->_oDb->getFieldsLayout($iProfileType);
		$aRows = $this->_oDb->getFieldsByRows($iProfileType);

		return $this->_oTemplate->displaySearchLayoutTool($iProfileType, $aProfileTypes, $iMaxRow, $aFields, $aRows, $bIsAjax);
	}


	//--- Page parts ---//
	function actionChangeSearchFieldLayout() {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();
		$iProfileType = intval($_REQUEST['ptid']);

		if (isset($_POST['clear'])) {
			$this->clearSearchLayout($iProfileType);
		}
		if (isset($_POST['add'])) {
			$this->addFieldToSearchLayout(intval($_POST['fid']), $_POST['col'], $_POST['row'], intval($_POST['ptid']));
		}
		if (isset($_POST['remove'])) {
			$this->removeFieldFromSearchLayout($_POST['fid'], intval($_POST['ptid']));
		}

		return $this->actionGetPageSearchResult($iProfileType, true);
	}
	function actionGetProfileTypes($bAddWrapper = false) {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$aTypes = $this->_oDb->getAll("SELECT * FROM `aqb_pts_profile_types` ORDER BY `ID`");
		foreach ($aTypes as $i => $aType) {
			$aTypes[$i]['Members'] = $this->_oDb->getOne("SELECT COUNT(*) FROM `Profiles` WHERE `ProfileType` = {$aType['ID']}");
			$sCode = addslashes(str_replace('__pt__', $aType['ID'], $this->sHPBlockTemplate));
			$aTypes[$i]['hp_exists'] = $this->_oDb->getOne("SELECT COUNT(*) FROM `sys_page_compose` WHERE `Content` = '{$sCode}'");
		}
		$sRet = $this->_oTemplate->displayProfileTypes($aTypes);
		if ($bAddWrapper) $sRet= '<div id="types_list">'.$sRet.'</div>';
		return $sRet;
	}
	function getMigrationTool() {
		$aMemLevels = getMemberships();
		unset($aMemLevels[1]);
		$aPTypes = $this->_oDb->getPairs("SELECT ID, Name FROM `aqb_pts_profile_types`", 'ID', 'Name');
		$aMemLevels[0] = _t('_aqb_pts_any');
		ksort($aMemLevels);
		$aPTypes[0] = _t('_aqb_pts_any');
		ksort($aPTypes);
		return $this->_oTemplate->displayMigrationTool($aMemLevels, $aPTypes);
	}

	//--- Actions ---//
	function actionToggleStatus($iType) {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$iType = intval($iType);
		$bObsolete = $this->_oDb->getOne("SELECT `Obsolete` FROM `aqb_pts_profile_types` WHERE `ID` = {$iType} LIMIT 1");
		if ($bObsolete) $this->_oDb->query("UPDATE `aqb_pts_profile_types` SET `Obsolete` = 0 WHERE `ID` = {$iType} LIMIT 1");
		else $this->_oDb->query("UPDATE `aqb_pts_profile_types` SET `Obsolete` = 1 WHERE `ID` = {$iType} LIMIT 1");
	}
	function actionToggleHpStatus($iType) {
		global $aPreValues;
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$iType = intval($iType);
		$sCode = addslashes(str_replace('__pt__', $iType, $this->sHPBlockTemplate));
		$bExists = $this->_oDb->getOne("SELECT COUNT(*) FROM `sys_page_compose` WHERE `Content` = '{$sCode}'");
		if ($bExists) {
			$sQuery = "DELETE FROM `sys_page_compose` WHERE `Content` = '{$sCode}'";
			@unlink(BX_DIRECTORY_PATH_CACHE . 'sys_page_compose.inc');
		} else {
			$sPTName = addslashes(_t($aPreValues['ProfileType'][$iType]['LKey']));
			$sQuery = "	INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `Visible`)
						VALUES ('index', '998px', 'List of members of {$sPTName} profile type', '{$sPTName}', 0, 0, 'PHP', '{$sCode}', 1, 'non,memb')";
		}
		$this->_oDb->query($sQuery);
	}
	function actionDeleteType($iType) {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$iType = intval($iType);
		$this->_oDb->query("DELETE FROM `aqb_pts_profile_types` WHERE `ID` = {$iType} LIMIT 1");
		$this->_oDb->query("DELETE FROM `sys_pre_values` WHERE `Key` = 'ProfileType' AND `Value` = {$iType} LIMIT 1");
    	$this->_compilePreValues();
	}
	function actionCreateProfileType() {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$sTypeName = trim(process_db_input($_POST['type_name'], BX_TAGS_STRIP));
		if (strlen($sTypeName) == 0) return "Type name must be at least 1 character long.";
	    $res = $this->_oDb->getFirstRow("SELECT `ID` FROM `aqb_pts_profile_types` WHERE `Name`='{$sTypeName}'");
	    if ($res) return "You have to select unique type name.";
	    $aIds = $this->_oDb->getAll("SELECT `ID` FROM `aqb_pts_profile_types` ORDER BY `ID` ASC");
	    $ids = array();
	    foreach ($aIds as $aId) {
	        $ids[$aId['ID']] = 1;
	    }
	    for ($i = 1; $i <= 1073741824 && $i > 0; $i <<= 1) {
	        if (!$ids[$i]) {
	            $this->_oDb->res("INSERT INTO `aqb_pts_profile_types` SET `ID`={$i}, `Name`='{$sTypeName}'");
	            addStringToLanguage( '__'.$sTypeName, $sTypeName );
	            compileLanguage();
	            $order = $this->_oDb->getOne("SELECT MAX(`Order`) FROM `sys_pre_values` WHERE `Key` = 'ProfileType'") + 1;
	            $this->_oDb->res("INSERT INTO `sys_pre_values` SET `Key` = 'ProfileType', `Value` = {$i}, `Order` = {$order}, `LKey` = '__{$sTypeName}'");
	            $this->_compilePreValues();
	            return;
	        }
	    }
	    //should only happen if there are more than 30 types already.
	    return "In this revision of this mod there is a limit of profile types at 31.";
	}
	function actionMigrate($iMemLevel, $iPType, $iSetType, $iStart, $iProfiles) {
		header('Content-Type:text/javascript');
		$oJson = new Services_JSON();
		if(!isAdmin())
			return $oJson->encode(array('error' => 'Hack attempt'));

		$iTotal = 0;
		if ($iStart == 0) $iTotal = $this->_oDb->getOne("SELECT COUNT(*) FROM `Profiles`");

		$iMigrated = 0;

		$aProfile = $this->_oDb->getFirstRow("SELECT `ID`, `ProfileType` FROM `Profiles` ORDER BY ID LIMIT {$iStart}, {$iProfiles}");
		$iProcessed = 0;
		while ($aProfile) {
			$iProcessed++;
			if (!$iPType || $iPType == $aProfile['ProfileType']) {
				$aMembership = getMemberMembershipInfo($aProfile['ID']);
				if (!$iMemLevel || $aMembership['ID'] == $iMemLevel) {
					$this->_oDb->res("UPDATE `Profiles` SET `ProfileType` = {$iSetType} WHERE `ID` = {$aProfile['ID']} LIMIT 1");
					createUserDataFile( $aProfile['ID'] );
					$iMigrated++;
				}
			}
			$aProfile = $this->_oDb->getNextRow();
		}


		return $oJson->encode(array(
			'bEnd' => $iProcessed != $iProfiles,
			'iMigrated' => $iMigrated,
			'iTotal' => $iTotal
		));
	}


	function actionTopMenu($sAction, $iMenuItemID) {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		if ($sAction == 'edit') {
			list ($sMenuItemName, $iMenuItemVisibility) = $this->_oDb->getMenuItemVisibility('top', $iMenuItemID);
			return PopupBox('aqb_popup_edit_form', $sMenuItemName, $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $this->_oTemplate->getMenuItemEditForm('top', $iMenuItemID, $iMenuItemVisibility).LoadingBox('formItemEditLoading'))));
		}elseif ($sAction == 'save') {
			$this->saveMenuItem('top', $iMenuItemID);
			$aResult = array('message' => MsgBox(_t('_Saved')), 'timer' => 1);
			$oJson = new Services_JSON();
            return $oJson->encode($aResult);
		}
	}
	function actionMemberMenu($sAction, $iMenuItemID) {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		if ($sAction == 'edit') {
			list ($sMenuItemName, $iMenuItemVisibility) = $this->_oDb->getMenuItemVisibility('member', $iMenuItemID);
			return PopupBox('aqb_popup_edit_form', $sMenuItemName, $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $this->_oTemplate->getMenuItemEditForm('member', $iMenuItemID, $iMenuItemVisibility).LoadingBox('formItemEditLoading'))));
		}elseif ($sAction == 'save') {
			$this->saveMenuItem('member', $iMenuItemID);
			$aResult = array('message' => MsgBox(_t('_Saved')), 'timer' => 1);
			$oJson = new Services_JSON();
            return $oJson->encode($aResult);
		}
	}
	function saveMenuItem($sType, $iMenuItemID) {
		$pts_visible_to = 0;
		if (is_array($_POST['pts_visible_to'])) {
			$aData = array_flip($_POST['pts_visible_to']);
			if ( isset($aData[1073741823]) ) {
				$pts_visible_to = 1073741823;
			} else {
				foreach ($aData as $ptype => $dummy) {
					$pts_visible_to += intval($ptype);
				}
		    }
		}
		$this->_oDb->setMenuItemVisibility($sType, $iMenuItemID, $pts_visible_to);

		require_once( $this->_oConfig->getClassPath() . 'AqbPTSMenuComposer.php');
		AqbPTSMenuComposer::updateCache($sType, $this->_oConfig->getHomePath().'cache/');
	}
	function actionPageBlock($sAction, $iID) {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$sPageName = $this->_oDb->getPageNameOfBlock($iID);
		if ($sAction == 'edit') {
			list( $sName, $iVisibility, $iRelevancy) = $this->_oDb->getPageBlockVisibility($iID);
			return PopupBox('aqb_popup_edit_form', $sName, $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $this->_oTemplate->getPageBlockEditForm($iID, $sPageName, $iVisibility, $iRelevancy).LoadingBox('formItemEditLoading'))));
		}elseif ($sAction == 'save') {
			if ($sPageName != 'member') {
				$pts_visible_to = 0;
				if (is_array($_POST['pts_visible_to'])) {
					$aData = array_flip($_POST['pts_visible_to']);
					if ( isset($aData[1073741823]) ) {
						$pts_visible_to = 1073741823;
					} else {
						foreach ($aData as $ptype => $dummy) {
							$pts_visible_to += intval($ptype);
						}
				    }
				}
			} else $pts_visible_to = 1073741823;
			if ($sPageName == 'member' || $sPageName == 'profile' || $sPageName == 'profile_info') {
				$pts_relevant_to = 0;
				if (is_array($_POST['pts_relevant_to'])) {
					$aData = array_flip($_POST['pts_relevant_to']);
					if ( isset($aData[1073741823]) ) {
						$pts_relevant_to = 1073741823;
					} else {
						foreach ($aData as $ptype => $dummy) {
							$pts_relevant_to += intval($ptype);
						}
				    }
				}
			} else $pts_relevant_to = 1073741823;
			$this->_oDb->setPageBlockVisibility($iID, $pts_visible_to, $pts_relevant_to);

			require_once( $this->_oConfig->getClassPath() . 'AqbPTSPageComposer.php');
			AqbPTSPageComposer::updateCache($this->_oConfig->getHomePath().'cache/');

			$aResult = array('message' => MsgBox(_t('_Saved')), 'timer' => 1);
			$oJson = new Services_JSON();
            return $oJson->encode($aResult);
		}
	}

	function actionFieldsParser() {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		require_once( $this->_oConfig->getClassPath() . 'AqbPTSFieldsParser.php');
		$oFieldsParser = new AqbPTSFieldsParser($this->_oDb);

		send_headers_page_changed();
		switch( $_REQUEST['action'] ) {
			case 'getArea':
				return $oFieldsParser->genAreaJSON( (int)$_REQUEST['id'] );
				break;
			case 'loadEditForm':
				return $oFieldsParser->showEditForm( (int)$_REQUEST['id'] );
				break;
			case 'Save':
				$sRet = $oFieldsParser->saveItem( (int)$_POST['field_id'], $_POST );
				$oFieldsParser-> updateCache($this->_oConfig->getHomePath().'cache/');
				return $sRet;
				break;
		}
		return 'Not implemented';
	}

	//--- Services ---//
	function serviceGetMemberProfileType($iProfileID) {
		global $aPreValues;
		$aProfile = getProfileInfo($iProfileID);
		$sProfileType = $aPreValues['ProfileType'][$aProfile['ProfileType']]['LKey'];
		return !empty($sProfileType) ? _t($sProfileType) : 'Undefined';
	}
	function serviceJoinFormFilter($aBlocks, $aArea) {
		global $aPreValues;

		$aInactiveProfileTypes = $this->_oDb->getAll("SELECT `ID` FROM `aqb_pts_profile_types` WHERE `Obsolete` = 1");
		foreach ($aInactiveProfileTypes as $aProfileType) {
			unset($aPreValues['ProfileType'][$aProfileType['ID']]);
		}

		$iCurrentProfileType = 0;
		if ($_GET['pid']) $iCurrentProfileType = intval($_GET['pid']);
		elseif ($_POST['ProfileType'][0]) $iCurrentProfileType = intval($_POST['ProfileType'][0]);
		else {
			$keys = array_keys($aPreValues['ProfileType']);
			$iCurrentProfileType = $keys[0];
		}

		require_once( $this->_oConfig->getClassPath() . 'AqbPTSFieldsParser.php');
		$oFieldsParser = new AqbPTSFieldsParser($this->_oDb);
		$aPFRelevancy = $oFieldsParser->loadCache($this->_oConfig->getHomePath().'cache/');

		foreach ($aArea as $page => $area ) { //through pages
			foreach ($area as $block_id => $block_props) { //through blocks
				if (isset($aPFRelevancy[$block_id]) && !(intval($aPFRelevancy[$block_id]) & $iCurrentProfileType) ) unset($aArea[$page][$block_id]);
				else {
					foreach ($block_props['Items'] as $item_id => $item_props) { //through items
						if (isset($aPFRelevancy[$item_id]) && !(intval($aPFRelevancy[$item_id]) & $iCurrentProfileType) ) unset($aArea[$page][$block_id]['Items'][$item_id]);
					}
				}
			}
		}
		$aBlocks = $aArea;
	}
	function servicePeditFormFilter($iProfileID, &$aBlocks, &$aArea) {
		global $aPreValues;

		$aProfile = getProfileInfo($iProfileID);
		$iCurrentProfileType = intval($aProfile['ProfileType']);

		if ($_GET['pid']) $iCurrentProfileType = intval($_GET['pid']);
		elseif ($_POST['ProfileType'][0]) $iCurrentProfileType = intval($_POST['ProfileType'][0]);

		$aInactiveProfileTypes = $this->_oDb->getAll("SELECT `ID` FROM `aqb_pts_profile_types` WHERE `Obsolete` = 1");
		foreach ($aInactiveProfileTypes as $aProfileType) {
			unset($aPreValues['ProfileType'][$aProfileType['ID']]);
		}

		require_once( $this->_oConfig->getClassPath() . 'AqbPTSFieldsParser.php');
		$oFieldsParser = new AqbPTSFieldsParser($this->_oDb);
		$aPFRelevancy = $oFieldsParser->loadCache($this->_oConfig->getHomePath().'cache/');

		foreach ($aArea as $block_id => $block_props) { //through blocks
			if (isset($aPFRelevancy[$block_id]) && !(intval($aPFRelevancy[$block_id]) & $iCurrentProfileType) ) unset($aArea[$block_id]);
			else {
				foreach ($block_props['Items'] as $item_id => $item_props) { //through items
					if (isset($aPFRelevancy[$item_id]) && !(intval($aPFRelevancy[$item_id]) & $iCurrentProfileType) ) unset($aArea[$block_id]['Items'][$item_id]);
				}
			}
		}
		$aBlocks = $aArea;
	}
	function serviceProfileViewFilter($iProfileID, $aBlocks, $aArea) {
		$this->servicePeditFormFilter($iProfileID, $aBlocks, $aArea);
	}
	function serviceAddFieldChangeHandler($iAreaID, $aForm) {
		if ($iAreaID < 1 || $iAreaID > 4) return;

		foreach ($aForm['inputs'] as $id => $aInput) {
        	if ($aInput['name'] == 'ProfileType[0]') {
        		$sRequestString = $iAreaID > 1 && isset($_GET['ID']) ? 'ID='.intval($_GET['ID']).'&' : ''; //pedit
        		$aForm['inputs'][$id]['attrs']['onchange'] = 'window.location.href = "'.$_SERVER['PHP_SELF'].'?'.$sRequestString.'pid=" + this.value;';
        		if ($_GET['pid']) $aForm['inputs'][$id]['value'] = intval($_GET['pid']);
        		break;
			}
		}
	}

	function serviceMenuItemsFilter($sType, $aItems) {
		if ($_REQUEST['modules-uninstall'] && in_array('aqb/pts/', $_REQUEST['pathes'])) return ;

		$iViewer = intval($_COOKIE['memberID']);
		if (!$iViewer) return; //I know it is a buggy way, but for some reason a call to isMember or check_login always fails inside this service call on a so early stage of script execution - so it is a bug of Dolphin framework

		$aViewer = getProfileInfo($iViewer);
		$iProfileType = intval($aViewer['ProfileType']);

		require_once( $this->_oConfig->getClassPath() . 'AqbPTSMenuComposer.php');
		$aMenuCache = AqbPTSMenuComposer::loadCache($sType, $this->_oConfig->getHomePath().'cache/');

		foreach ($aItems as $iItem => $aItem) {
			$iRealID = intval($GLOBALS['site']['build']) >= 3 && $sType == 'member' ? $aItems[$iItem]['menu_id'] : $iItem;
			if (isset($aMenuCache[$iRealID]) && !($iProfileType & $aMenuCache[$iRealID])) unset($aItems[$iItem]);
		}

		if (intval($GLOBALS['site']['build']) >= 3 && $sType == 'member') $aItems = array_values($aItems);
	}

	function servicePageBlocksFilter($oBxDolPageView) {
		$iViewer = intval($_COOKIE['memberID']);
		if ($iViewer) {
			$aProfile = getProfileInfo($iViewer);
			$iProfileType = $aProfile['ProfileType'];
			$iViewedProfileType = 0;
		}

		$bCheckRelevancy = false;
		if ($oBxDolPageView->sPageName == 'profile' || $oBxDolPageView->sPageName == 'profile_info') {
			$iViewedProfileType = $oBxDolPageView->oProfileGen->_aProfile['ProfileType'];
			$bCheckRelevancy = true;
		} elseif ($oBxDolPageView->sPageName == 'member') {
			$iViewedProfileType = $oBxDolPageView->aMemberInfo['ProfileType'];
			$bCheckRelevancy = true;
		}

		require_once( $this->_oConfig->getClassPath() . 'AqbPTSPageComposer.php');
		$aPageBlocksCache = AqbPTSPageComposer::loadCache($this->_oConfig->getHomePath().'cache/');

		foreach ($oBxDolPageView->aPage['Columns'] as $iColumn => $aColumn) {
			foreach ($aColumn['Blocks'] as $iBlockID => $aBlock) {
				if ($iViewer && isset($aPageBlocksCache[$iBlockID]['ProfileTypesVisibility']) && !($iProfileType & $aPageBlocksCache[$iBlockID]['ProfileTypesVisibility'])) unset($oBxDolPageView->aPage['Columns'][$iColumn]['Blocks'][$iBlockID]);
				if ($bCheckRelevancy && isset($aPageBlocksCache[$iBlockID]['ProfileTypes']) && !($iViewedProfileType & $aPageBlocksCache[$iBlockID]['ProfileTypes'])) unset($oBxDolPageView->aPage['Columns'][$iColumn]['Blocks'][$iBlockID]);
			}
		}
	}

	function serviceFilterFields(&$aBlocks, $iCurrentProfileType) {
		require_once( $this->_oConfig->getClassPath() . 'AqbPTSFieldsParser.php');
		$oFieldsParser = new AqbPTSFieldsParser($this->_oDb);
		$aPFRelevancy = $oFieldsParser->loadCache($this->_oConfig->getHomePath().'cache/');

		foreach ($aBlocks as $block_id => $block_props) { //through blocks
			if (isset($aPFRelevancy[$block_id]) && !(intval($aPFRelevancy[$block_id]) & $iCurrentProfileType) ) unset($aBlocks[$block_id]);
			else {
				foreach ($block_props['Items'] as $item_id => $item_props) { //through items
					if (isset($aPFRelevancy[$item_id]) && !(intval($aPFRelevancy[$item_id]) & $iCurrentProfileType) ) unset($aBlocks[$block_id]['Items'][$item_id]);
				}
			}
		}
	}
	function serviceGetSearchLayout($aProfile, $aTemplKeys) {
		if (!$aProfile['ProfileType']) return;

		if (!isset($this->aLayouts[$aProfile['ProfileType']])) {
			$rows = array();
			$res = $this->_oDb->getResultedFieldsLayout($aProfile['ProfileType']);
			$max_row = 0;
			foreach ($res as $row) {
		        if (($row['Type'] == 'select_one' || $row['Type'] == 'select_set') && substr($row['Values'], 0, 2) != '#!') {
					$row['Values'] = explode("\n", $row['Values']);
				}
				$rows[$row['row']][$row['col']] = $row;
				$max_row = $row['row'];
			}
			$this->aLayouts[$aProfile['ProfileType']]['rows'] = $rows;
			$this->aLayouts[$aProfile['ProfileType']]['max_row'] = $max_row;
		} else {
			$rows = $this->aLayouts[$aProfile['ProfileType']]['rows'];
			$max_row = $this->aLayouts[$aProfile['ProfileType']]['max_row'];
		}

		$aTemplKeys['profile_fields'] = $this->_formatSearchResult($rows, $max_row, $aProfile);
		$aTemplKeys['ext_css_class'] = '" style="height: auto;';
	}
	function serviceGetCustomTemplate() {
		$this->_oTemplate->addCss('search.css');
		return $this->_oTemplate;
	}
	function serviceGetSearchForms($aParams, $oBxDolProfileFields) {
		$aProfileTypes = $GLOBALS['aPreValues']['ProfileType'];
		$aBlocksDefault = $oBxDolProfileFields->aBlocks;

		$this->serviceFilterFields($oBxDolProfileFields->aBlocks, 1073741823);
		$aParams['default_params']['ProfileType'] = 1073741823;
		$iActiveForm = 0;
        $sRet = '
        	<link href="modules/aqb/pts/templates/base/css/jquery-ui.css" rel="stylesheet" type="text/css" />
        	<script language="javascript" type="text/javascript" src="plugins/jquery/jquery-ui.js"></script>
        	<div id="search_forms_loading"><center><img alt="loading ..." src="templates/base/images/loading.gif"/></center></div>
        	<div id="search_forms" style="display:none">
            	<div class="ui-accordion-group">
					<h3 class="ui-accordion-header">
						<a href="javascript:void(0);">'._t('_aqb_pts_visible_for_all').'</a>
					</h3>
					<div class="ui-accordion-content">'.$oBxDolProfileFields->getFormsSearch($aParams).'</div>
				</div>';

        $oBxDolProfileFields->aBlocks = $aBlocksDefault;
        $iFormsCounter = 1;
        foreach ($aProfileTypes as $iPType => $aPTName) {
        	if ($_REQUEST['ProfileType'] == $iPType) $iActiveForm = $iFormsCounter;
        	$aParams['default_params']['ProfileType'] = $iPType;
        	$this->serviceFilterFields($oBxDolProfileFields->aBlocks, $iPType);
        	$sRet .= '
        		<div class="ui-accordion-group">
					<h3 class="ui-accordion-header">
						<a href="javascript:void(0);">'._t($aPTName['LKey']).'</a>
					</h3>
					<div class="ui-accordion-content">'.$oBxDolProfileFields->getFormsSearch($aParams).'</div>
				</div>';
        	$oBxDolProfileFields->aBlocks = $aBlocksDefault;
        	$iFormsCounter++;
		}

        $sRet .='
        	</div>
			<script language="javascript" type="text/javascript">
				$(\'#search_forms\').accordion({
					header: \'h3\',
					autoHeight: false,
					active: '.$iActiveForm.'
				});
				$(document).ready(function() {
					$(\'#search_forms\').css(\'display\', \'block\');
					$(\'#search_forms_loading\').css(\'display\', \'none\');
				});
			</script>';
        return $sRet;
	}
	function serviceGetMembersBlock($iProfileType) {
		bx_import('BxTemplIndexPageView');
		$oIndexPage = new BxTemplIndexPageView();

		$iMaxNum = (int) $this->_oDb->getParam( "top_members_max_num" ); // number of profiles
		return $oIndexPage->getMembers('Members', array('ProfileType' => $iProfileType), $iMaxNum);
	}
	function _formatSearchResult($rows, $max_row, $p_arr) {
		$result = '<table width="100%" cellspacing="0" cellpadding="0">';
		for ($i = 1; $i <= $max_row; $i++) {
			if (isset($rows[$i][0])) {
				$aItem = $rows[$i][0];
				$value = $this->oPV->getViewableValue( $aItem, $p_arr[$aItem['Name']] );
				if (strlen($value)) {
					$result .= '<tr><td colspan="2">';
					$result .= '<table width="100%" class="aqb_pts_profile_info_block" cellspacing="0" cellpadding="1"><tr>'.
									'<td class="profile_info_label">' . htmlspecialchars( _t( '_FieldCaption_'.$aItem['Name'].'_View' ) ) . ':</td>'.
									'<td class="profile_info_value" colspan="2">'.$value.'</td>'.
								'</tr></table>';
					$result .= '</td><tr>';
				}
			} else {
				if (isset($rows[$i][1])) {
					$aItem = $rows[$i][1];
					$value = $this->oPV->getViewableValue( $aItem, $p_arr[$aItem['Name']] );
					if (strlen($value)) {
						$col1 = '<table width="100%" class="aqb_pts_profile_info_block" cellspacing="0" cellpadding="1"><tr>'.
									'<td class="profile_info_label">' . htmlspecialchars( _t( '_FieldCaption_'.$aItem['Name'].'_View' ) ) . ':</td>'.
									'<td class="profile_info_value" colspan="2">'.$value.'</td>'.
								'</tr></table>';
					} else $col1 =  '&nbsp;';
				} else $col1 =  '&nbsp;';
				if (isset($rows[$i][2])) {
					$aItem = $rows[$i][2];
					$value = $this->oPV->getViewableValue( $aItem, $p_arr[$aItem['Name']] );
					if (strlen($value)) {
						$col2 = '<table width="100%" class="aqb_pts_profile_info_block" cellspacing="0" cellpadding="1"><tr>'.
									'<td class="profile_info_label">' . htmlspecialchars( _t( '_FieldCaption_'.$aItem['Name'].'_View' ) ) . ':</td>'.
									'<td class="profile_info_value" colspan="2">'.$value.'</td>'.
								'</tr></table>';
					} else $col2 =  '&nbsp;';
				} else $col2 =  '&nbsp;';
				$result .= '<tr><td width="50%">'.$col1.'</td><td>'.$col2.'</td></tr>';
			}
			if ($i == 1) $result = str_replace('width=\'25%\'', '', $result);
		}
		$result .= '</table>';
		return $result;
	}

	function clearSearchLayout($iProfileType) {
		$this->_oDb->query("DELETE FROM `aqb_pts_search_result_layout` WHERE `ProfileType` = {$iProfileType}");
	}
	function addFieldToSearchLayout($id, $col, $row, $tid) {
		$cond = $col != 0 ? "(`col` = {$col} OR `col` = 0) AND " : '';
		$row_arr = $this->_oDb->getRow("SELECT `col`, `row` FROM `aqb_pts_search_result_layout` WHERE {$cond} `row` = {$row} AND `ProfileType` = {$tid}");
		if ($row_arr) {
			if ($col == 0) {
				$this->_oDb->query("UPDATE `aqb_pts_search_result_layout` SET `row` = `row` + 1 WHERE `row` >= {$row} AND `ProfileType` = {$tid}");
			} else {
				$this->MoveCellDown($row_arr['col'], $row_arr['row'], $tid);
			}
		}
		$this->_oDb->query("INSERT INTO `aqb_pts_search_result_layout` SET `FieldID` = {$id}, `col` = {$col}, `row` = {$row}, `ProfileType` = {$tid}");
		return _t('_aqb_pts_operation_success');
	}
	function MoveCellDown($col, $row, $tid) {
		if ($col == 0) {
			$this->_oDb->query("UPDATE `aqb_pts_search_result_layout` SET `row` = `row` + 1 WHERE `row` >= {$row} AND `ProfileType` = {$tid}");
			return;
		} else {
			$row_arr = $this->_oDb->getRow("SELECT `col`, `row` FROM `aqb_pts_search_result_layout` WHERE (`col` = {$col} OR `col` = 0) AND `row` = ".($row+1)." AND `ProfileType` = {$tid}");
			if ($row_arr) $this->MoveCellDown($row_arr['col'], $row_arr['row'], $tid);
			$this->_oDb->query("UPDATE `aqb_pts_search_result_layout` SET `row` = `row` + 1 WHERE `row` = {$row} AND `col` = {$col} AND `ProfileType` = {$tid}");
		}
	}
	function removeFieldFromSearchLayout($id, $tid) {
		$res = $this->_oDb->getRow("SELECT `col`, `row`, `ID` FROM `aqb_pts_search_result_layout` WHERE `FieldID` = {$id} AND `ProfileType` = {$tid}");
		$col = $res['col'];
		$row = $res['row'];
		$rec_id = $res['ID'];
		$this->_oDb->query("DELETE FROM `aqb_pts_search_result_layout` WHERE `ID` = {$rec_id}");
		if ($col == 0) $this->_oDb->query("UPDATE `aqb_pts_search_result_layout` SET `row` = `row` - 1 WHERE `row` > {$row} AND `ProfileType` = {$tid}");
		else {
			$col2 = $col == 1 ? 2 : 1;
			$aSibling = $this->_oDb->getRow("SELECT `col`, `row`, `ID` FROM `aqb_pts_search_result_layout` WHERE `row` = {$row} AND `col` = {$col2}");
			if (!$aSibling) $this->_oDb->query("UPDATE `aqb_pts_search_result_layout` SET `row` = `row` - 1 WHERE `row` > {$row} AND `ProfileType` = {$tid}");
		}
		return _t('_aqb_pts_operation_success');
	}
	function _compilePreValues() {
		if (function_exists('compilePreValues')) return compilePreValues();

		$sQuery = "SELECT DISTINCT `Key` FROM `sys_pre_values`";
		$rKeys = db_res( $sQuery );

		$rProf = @fopen( BX_DIRECTORY_PATH_INC . 'prof.inc.php', 'w' );
		if( !$rProf ) {
			echo '<b>Warning!</b> Couldn\'t compile prof.inc.php. Please check permissions.';
			return false;
		}

		fwrite( $rProf, "<?\n\$aPreValues = array(\n" );

		while( $aKey = mysql_fetch_assoc( $rKeys ) ) {
			$sKey    = $aKey['Key'];
			$sKey_db = addslashes( $sKey );

			fwrite( $rProf, "  '$sKey_db' => array(\n" );

			$sQuery = "SELECT * FROM `sys_pre_values` WHERE `Key` = '$sKey_db' ORDER BY `Extra2`, `Order`";
			$rRows  = $this->_oDb->res( $sQuery );

			while( $aRow = mysql_fetch_assoc( $rRows ) ) {
				$sValue_db = addslashes( $aRow['Value'] );
				fwrite( $rProf, "    '{$sValue_db}' => array( " );

				foreach( $aRow as $sValKey => $sValue ) {
					if( $sValKey == 'Key' or $sValKey == 'Value' or $sValKey == 'Order' )
						continue; //skip key, value and order. they already used

					if( !strlen( $sValue ) )
						continue; //skip empty values

					fwrite( $rProf, "'$sValKey' => '" . addslashes( $sValue ) . "', " );
				}

				fwrite( $rProf, "),\n" );
			}

			fwrite( $rProf, "  ),\n" );
		}

		fwrite( $rProf, ");\n" );
		fwrite( $rProf, '
$aPreValues[\'Country\'] = sortArrByLang( $aPreValues[\'Country\'] );

function sortArrByLang( $aArr ) {
	if( !function_exists( \'_t\' ) )
		return $aArr;

	$aSortArr = array();
	foreach( $aArr as $sKey => $aValue )
		$aSortArr[$sKey] = _t( $aValue[\'LKey\'] );

	asort( $aSortArr );

	$aNewArr = array();
	foreach( $aSortArr as $sKey => $sVal )
		$aNewArr[$sKey] = $aArr[$sKey];

	return $aNewArr;
}
		' );

		fclose( $rProf );

		return true;
	}
}
?>