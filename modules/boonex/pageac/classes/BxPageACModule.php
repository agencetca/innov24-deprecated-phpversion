<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
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

bx_import('BxDolModule');
bx_import('BxDolCacheFile');
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

class BxPageACModule extends BxDolModule {
	/**
	 * Constructor
	 */
	function BxPageACModule($aModule) {
	    parent::BxDolModule($aModule);
	}
	//--- Pages (tabs) ---//
	function actionGetPageRules() {
		send_headers_page_changed();
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$sRet = DesignBoxAdmin(_t('_bx_pageac_current_rules'), $this->actionGetRulesList(true));
		$sRet .= DesignBoxAdmin(_t('_bx_pageac_new_rule'), $this->_oTemplate->displayNewRuleForm());
		$sRet .= DesignBoxAdmin(_t('_bx_pageac_note'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => _t('_bx_pageac_note_text'))));
		return $sRet;
	}
	function actionGetPageTopMenu() {
		send_headers_page_changed();
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$aTopMenuArray = $this->_oDb->getTopMenuArray();
		return $this->_oTemplate->displayTopMenuCompose($aTopMenuArray);
	}
	function actionGetPageMemberMenu() {
		send_headers_page_changed();
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$aMemberMenuArray = $this->_oDb->getMemberMenuArray();
		return $this->_oTemplate->displayMemberMenuCompose($aMemberMenuArray);
	}
	function actionGetPagePageBlocks($sPage = '') {
		send_headers_page_changed();
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		if (empty($sPage)) {
			return $this->_oTemplate->_getAvailablePages($this->_oDb->getAvailablePages());
		} else {
			$aColumns = $this->_oDb->getPageBlocks($sPage);
			return $this->_oTemplate->_getPageBlocks($aColumns);
		}
	}
	//--- Actions ---//
	function actionGetRulesList($bAddWrapper = false) {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$aRules = $this->_oDb->getAllRules();
		$sRet = $this->_oTemplate->displayRulesList($aRules);
		if ($bAddWrapper) $sRet= '<div id="rules_list">'.$sRet.'</div>';
		return $sRet;
	}

	function actionNewRule() {
		if(!isAdmin()) return 'Hack attempt';

		$sRule = $this->_validateRule($_POST['rule'], $_POST['advanced']);
		if (!empty($sRule)) {
			$aMemLevels = array();
			foreach ($_POST['memlevels'] as $iID) {
				$aMemLevels[$iID] = 1;
			}

			$this->_oDb->addRule($sRule, $aMemLevels);
			return '';
		} else {
			return _t('_bx_pageac_page_url_empty');
		}

	}
	function actionSaveRule() {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$iRuleID = intval($_POST['rule_id']);
		$sRule = $this->_validateRule($_POST['rule_text'], true);
		if (!strlen($sRule)) {
			return $this->actionDeleteRule();
		}
		else {
			$aMemLevels = array();
			$aData = explode(',', $_POST['rule_mlvs']);
			foreach ($aData as $iID) {
				if ($iID) $aMemLevels[$iID] = 1;
			}
			$this->_oDb->updateRule($iRuleID, $sRule, $aMemLevels);
			return MsgBox(_t('_bx_pageac_saved'), 1).$this->actionGetRulesList();
		}
	}

	function actionDeleteRule() {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		$iRuleID = intval($_POST['rule_id']);

		$this->_oDb->deleteRule($iRuleID);
		return MsgBox(_t('_bx_pageac_deleted'), 1).$this->actionGetRulesList();
	}


	function actionTopMenu($sAction, $iMenuItemID) {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		if ($sAction == 'edit') {
			$aMenuItemVisibility = $this->_oDb->getMenuItemVisibility('top', $iMenuItemID);
			return PopupBox('pageac_popup_edit_form', _t('_bx_pageac_visible_for'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $this->_oTemplate->getMenuItemEditForm('top', $iMenuItemID, $aMenuItemVisibility).LoadingBox('formItemEditLoading'))));
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
			$aMenuItemVisibility = $this->_oDb->getMenuItemVisibility('member', $iMenuItemID);
			return PopupBox('pageac_popup_edit_form', _t('_bx_pageac_visible_for'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $this->_oTemplate->getMenuItemEditForm('member', $iMenuItemID, $aMenuItemVisibility).LoadingBox('formItemEditLoading'))));
		}elseif ($sAction == 'save') {
			$this->saveMenuItem('member', $iMenuItemID);
			$aResult = array('message' => MsgBox(_t('_Saved')), 'timer' => 1);
			$oJson = new Services_JSON();
            return $oJson->encode($aResult);
		}
	}
	function saveMenuItem($sType, $iMenuItemID) {
		$aVisibleTo = array();
		if (is_array($_POST['mlv_visible_to'])) {
			$aData = array_flip($_POST['mlv_visible_to']);
			if ( !isset($aData[-1]) ) {
				foreach ($aData as $iLevel => $dummy) {
					$aVisibleTo[$iLevel] = 1;
				}
		    }
		}
		if (empty($aVisibleTo) && !isset($aData[-1])) $aVisibleTo[] = 0;
		$this->_oDb->setMenuItemVisibility($sType, $iMenuItemID, $aVisibleTo);
	}
	function actionPageBlock($sAction, $iID) {
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();

		if ($sAction == 'edit') {
			$aVisibility = $this->_oDb->getPageBlockVisibility($iID);
			return PopupBox('pageac_popup_edit_form', _t('_bx_pageac_visible_for'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $this->_oTemplate->getPageBlockEditForm($iID, $aVisibility).LoadingBox('formItemEditLoading'))));
		}elseif ($sAction == 'save') {
			$aVisibleTo = array();
			if (is_array($_POST['mlv_visible_to'])) {
				$aData = array_flip($_POST['mlv_visible_to']);
				if ( !isset($aData[-1]) ) {
					foreach ($aData as $iMemLevel => $dummy) {
						$aVisibleTo[$iMemLevel] = 1;
					}
			    }
			}
			if (empty($aVisibleTo) && !isset($aData[-1])) $aVisibleTo[] = 0;
			$this->_oDb->setPageBlockVisibility($iID, $aVisibleTo);
			$aResult = array('message' => MsgBox(_t('_Saved')), 'timer' => 1);
			$oJson = new Services_JSON();
            return $oJson->encode($aResult);
		}
	}
	function _validateRule($sRule, $bIsAdvanced) {
		$sRule = trim($sRule);
		if (empty($sRule)) return '';

		if(get_magic_quotes_gpc()) $sRule = stripslashes($sRule); //remove slashes to work with an URL pattern as is

		$sBaseURL = basename(BX_DOL_URL_ROOT);

		if (strpos($sRule, BX_DOL_URL_ROOT) === 0) $sRule = substr($sRule, strlen(BX_DOL_URL_ROOT)); //if URL starts from http://www.site.com
		elseif (strpos($sRule, $sBaseURL) === 0) $sRule = substr($sRule, strlen($sBaseURL)+1); //if URL starts from www.site.com
		else {
			if (strpos($sBaseURL, 'www.') === 0) {
				$sBaseURL = substr($sBaseURL, 4);
				if (strpos($sRule, $sBaseURL) === 0) $sRule = substr($sRule, strlen($sBaseURL)+1); //if URL starts from site.com
			}
		}

		if (!$bIsAdvanced) {
			$sRule = addcslashes($sRule, '|\\{}[]()#:^$.?+*'); //   |\{}[]()#:^$.?+* - special regex characters
			if (!empty($sRule)) $sRule .= '.*';
		}
		if(get_magic_quotes_gpc()) $sRule = addslashes($sRule); //add slashes back if necessary because later process_db_input will be called.

		return $sRule;
	}

	//--- Services ---//
	function serviceMenuItemsFilter($sType, &$aItems) {
		if ($_REQUEST['modules-uninstall'] && in_array('boonex/pageac/', $_REQUEST['pathes'])) return ;//to avoid menu filtering during module uninstallation.

		define('BX_DOL_ROLE_MEMBER',    1);	//this code is required here because at the time of BxDolMenu::load function call profiles.inc.php isn't fully included yet,
		define('BX_DOL_ROLE_ADMIN',     2);	//thus all defines and function calls located in profiles.inc.php aren't executed at this moment
		check_logged();						//so a call to isLogged or check_logged always would fail here because BX_DOL_ROLE_MEMBER/BX_DOL_ROLE_ADMIN aren't defined yet.
		if (!isLogged()) return; //non-members visibility controlled by default in builders
		elseif (isRole(BX_DOL_ROLE_ADMIN, getLoggedId())) return; //admin isn't affected by this module

		$aMembership = getMemberMembershipInfo(getLoggedId());
		$iMemLevel = intval($aMembership['ID']);

		$aMenuCache = $this->_oDb->getAllMenuItems($sType);

		if ($sType == 'member') {
			foreach ($aItems as $sSection => $aSubItems) {
				if (!is_array($aSubItems)) continue;
				foreach ($aSubItems as $iItem => $aItem) {
					$iRealID = $aSubItems[$iItem]['menu_id'];
					if (!empty($aMenuCache[$iRealID]['MemLevels']) && !$aMenuCache[$iRealID]['MemLevels'][$iMemLevel]) unset($aItems[$sSection][$iItem]);
				}
				$aItems[$sSection] = array_values($aItems[$sSection]);
			}
		} else {
			foreach ($aItems as $iItem => $aItem) {
				if (!empty($aMenuCache[$iItem]['MemLevels']) && !$aMenuCache[$iItem]['MemLevels'][$iMemLevel]) unset($aItems[$iItem]);
			}
		}
	}

	function servicePageBlocksFilter(&$oBxDolPageView) {
		if (!isLogged()) return; //non-members visibility controlled by default in builders
		elseif (isRole(BX_DOL_ROLE_ADMIN, getLoggedId())) return; //admin isn't affected by this module

		$aMembership = getMemberMembershipInfo(getLoggedId());
		$iMemLevel = $aMembership['ID'];

		$aPageBlocksCache = $this->_oDb->getAllPageBlocks();

		foreach ($oBxDolPageView->aPage['Columns'] as $iColumn => $aColumn) {
			foreach ($aColumn['Blocks'] as $iBlockID => $aBlock) {
				if (!empty($aPageBlocksCache[$iBlockID]) && !$aPageBlocksCache[$iBlockID]['MemLevels'][$iMemLevel]) unset($oBxDolPageView->aPage['Columns'][$iColumn]['Blocks'][$iBlockID]);
			}
		}
	}
	function serviceResponceProtectURL($sURL) {
		if (isLogged() && isRole(BX_DOL_ROLE_ADMIN, getLoggedId()) || strpos($sURL, '/'.$GLOBALS['admin_dir']) === 0) return; //admin isn't affected by this module also access to admin panel shouldn't ever be protected

		$aMemLevel = getMemberMembershipInfo(getLoggedId());
		$iMemLevel = $aMemLevel['ID'];

		if ($iMemLevel) {
			$aRules = $this->_oDb->getAllRules();
			foreach ($aRules as $aRule) {
				if ($aRule['MemLevels'][$iMemLevel] && @preg_match('#'.$aRule['Rule'].'#i', $sURL)) {
					global $_page;
					global $_page_cont;
					$_page['name_index'] = -1;
					$_page['header'] = _t("_bx_pageac_access_denied");
					$_page_cont[$_page['name_index']]['page_main_code'] = MsgBox(_t("_bx_pageac_deny_text"));
					PageCode();
					exit;
				}
			}
		}
	}
}
?>