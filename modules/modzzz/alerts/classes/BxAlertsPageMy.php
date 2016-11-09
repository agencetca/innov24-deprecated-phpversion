<?php
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

bx_import('BxDolPageView');

class BxAlertsPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function BxAlertsPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::BxDolPageView('modzzz_alerts_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch ($_REQUEST['modzzz_alerts_filter']) {
        case 'add_alert':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_alerts':
            $sContent = $this->getBlockCode_My ();
            break;   
        default:
            $sContent = $this->getBlockCode_Main ();
        }

        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aMenu = array(
            _t('_modzzz_alerts_block_submenu_main') => array('href' => $sBaseUrl, 'active' => empty($_REQUEST['modzzz_alerts_filter']) || !$_REQUEST['modzzz_alerts_filter']),
            _t('_modzzz_alerts_block_submenu_add_alert') => array('href' => $sBaseUrl . '&modzzz_alerts_filter=add_alert', 'active' => 'add_alert' == $_REQUEST['modzzz_alerts_filter']),
            _t('_modzzz_alerts_block_submenu_manage_alerts') => array('href' => $sBaseUrl . '&modzzz_alerts_filter=manage_alerts', 'active' => 'manage_alerts' == $_REQUEST['modzzz_alerts_filter']) 
        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_alerts_import ('SearchResult');
        $o = new BxAlertsSearchResult('user', $this->_aProfile['NickName']);
        $o->aCurrent['rss'] = 0;
 
        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_alerts_page_title_my_alerts');

        if ($o->isError) {
            return DesignBoxContent(_t('_modzzz_alerts_block_users_alerts'), MsgBox(_t('_Empty')), 1);
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss ('unit.css');
            $this->_oTemplate->addCss ('main.css');            
            return $s;
        } else {
            return DesignBoxContent(_t('_modzzz_alerts_block_users_alerts'), MsgBox(_t('_Empty')), 1);
        }
    }

    function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_alerts_msg_you_have_no_alerts'), $sBaseUrl . '&modzzz_alerts_filter=add_alert');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_alerts_msg_you_have_some_alerts'), $sBaseUrl . '&modzzz_alerts_filter=manage_alerts', $iActive, $sBaseUrl . '&modzzz_alerts_filter=add_alert');
        return $this->_oTemplate->parseHtmlByName('my_alerts_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_alerts_create_alert', $aVars);
    }
 
	function getBlockCode_My() {
 
        $sForm = $this->_oMain->_manageEntries ('user', $this->_aProfile['NickName'], false, 'modzzz_alerts_user_form', array(
            'action_delete' => '_modzzz_alerts_admin_delete',
        ), 'modzzz_alerts_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_alerts_my_active');
        return $this->_oTemplate->parseHtmlByName('my_alerts_manage', $aVars);
    }    
}

?>
