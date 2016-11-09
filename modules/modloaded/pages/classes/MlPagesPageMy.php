<?php
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

bx_import('BxDolPageView');

class MlPagesPageMy extends BxDolPageView {

    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;
    var $_aProfile;

	function MlPagesPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oConfig = $oMain->_oConfig;
        $this->_oDb = $oMain->_oDb;
        $this->_aProfile = &$aProfile;
		parent::BxDolPageView('ml_pages_my');
	}

	function getBlockCode_Owner() {
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch (bx_get('ml_pages_filter')) {
        case 'edit_privacy':            
            $sContent = $this->getBlockCode_Privacy (bx_get('page_id'));
            break;
        case 'edit_page':            
            $sContent = $this->getBlockCode_Edit (bx_get('page_id'));
            break;
        case 'add_page':            
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_pages':
            $sContent = $this->getBlockCode_Manage ();
            break;            
        case 'pending_pages':
            $sContent = $this->getBlockCode_Pending ();
            break;            
        default:
            $sContent = $this->getBlockCode_Main ();
        }

        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aMenu = array(
            _t('_ml_pages_block_submenu_main') => array('href' => $sBaseUrl, 'active' => !bx_get('ml_pages_filter')),
            _t('_ml_pages_block_submenu_add') => array('href' => $sBaseUrl . '&ml_pages_filter=add_page', 'active' => 'add_page' == bx_get('ml_pages_filter')),
            _t('_ml_pages_block_submenu_manage') => array('href' => $sBaseUrl . '&ml_pages_filter=manage_pages', 'active' => 'manage_pages' == bx_get('ml_pages_filter')),
            _t('_ml_pages_block_submenu_pending') => array('href' => $sBaseUrl . '&ml_pages_filter=pending_pages', 'active' => 'pending_pages' == bx_get('ml_pages_filter')),
        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        ml_pages_import ('SearchResult');
        $o = new MlPagesSearchResult('user', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION));
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_ml_pages_block_my_pages');

        if ($o->isError) {
            return MsgBox(_t('_Empty'));
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss ('unit.css');
            $this->_oTemplate->addCss ('main.css');
            return $s;
        } else {
            return DesignBoxContent(_t('_ml_pages_block_user_pages'), MsgBox(_t('_Empty')), 1);
        }
    }

	function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_ml_pages_msg_you_have_pending_approval_pages'), $sBaseUrl . '&ml_pages_filter=pending_pages', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_ml_pages_msg_you_have_no_pages'), $sBaseUrl . '&ml_pages_filter=add_page');
        else
            $aVars['msg'] = sprintf(_t('_ml_pages_msg_you_have_some_pages'), $sBaseUrl . '&ml_pages_filter=manage_pages', $iActive, $sBaseUrl . '&ml_pages_filter=add_page');
        return $this->_oTemplate->parseHtmlByName('my_pages_main', $aVars);
    }    

    function getBlockCode_Privacy($iPageId) {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }        
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my');
        $aVars = array ('form' => ob_get_clean());
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_pages_edit_page', $aVars);
    }  
    function getBlockCode_Edit($iPageId) {
        if (!$this->_oMain->isAllowedAdd() || !$iPageId) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
			  require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesPageEditProcessor.php');
			  $oEditPageProc = new MlPagesPageEditProcessor($iPageId);
        //$this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my');
				require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesModule.php');
				$aModule = array(
					'class_prefix' => 'MlPages',
					'path' => 'modloaded/pages/'
					);    
				$oMlPages = new MlPagesModule($aModule);
		    $aVars = $oMlPages->_getCategoriesForm($iPageId, $oEditPageProc -> sMainCategory, $oEditPageProc -> sSubCategory);
        $aVars = array_merge($aVars, array ('form' => $oEditPageProc->process()));
        $this->_oTemplate->addCss ('forms_extra.css');
				$GLOBALS['oSysTemplate']->addJsTranslation('_Errors in join form');
				$GLOBALS['oSysTemplate']->addJs(array('pedit.js', 'jquery.form.js'));
				$GLOBALS['oSysTemplate']->addCss(array('join.css'));
        return $this->_oTemplate->parseHtmlByName('my_pages_edit_page', $aVars);
    }  

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }

        ob_start();
			  require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesCreatePageProcessor.php');
			  $oCreatePageProc = new MlPagesCreatePageProcessor();
        //$this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my');
				require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesModule.php');
				$aModule = array(
					'class_prefix' => 'MlPages',
					'path' => 'modloaded/pages/'
					);   
				$oMlPages = new MlPagesModule($aModule);
				$aVars = $oMlPages->_getCategoriesForm();
        $aVars = array_merge($aVars, array ('form' => $oCreatePageProc->process()));
        $this->_oTemplate->addCss ('forms_extra.css');
				$GLOBALS['oSysTemplate']->addJsTranslation('_Errors in join form');
				$GLOBALS['oSysTemplate']->addJs(array('join.js', 'jquery.form.js'));
				$GLOBALS['oSysTemplate']->addCss(array('join.css'));
        return $this->_oTemplate->parseHtmlByName('my_pages_create_page', $aVars);
    }        

	function getBlockCode_Manage() {
        $sForm = $this->_oMain->_manageEntries ('user', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), false, 'ml_pages_my_active', array(
                'action_delete' => '_ml_pages_admin_delete',
        ), 'ml_pages_my_active', 7); 
        $aVars = array ('form' => $sForm, 'id' => 'ml_pages_my_active');
        return $this->_oTemplate->parseHtmlByName('my_pages_manage', $aVars);
    }

	function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'ml_pages_my_pending', array(
                'action_delete' => '_ml_pages_admin_delete',
        ), 'ml_pages_my_pending', 7); 
        $aVars = array ('form' => $sForm, 'id' => 'ml_pages_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_pages_manage', $aVars);
    }
}

?>
