<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx 
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

function modzzz_alerts_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'alerts') {
        $oMain = BxDolModule::getInstance('BxAlertsModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' ); 
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' ); 
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxDolAdminSettings'); 
bx_import('BxTemplSearchResult');  

/*
 * Alerts module
 *
 * This module allow users to create user's alerts, 
 * users can rate, comment and discuss alerts.
 * Alerts can have photos, videos, sounds and files, uploaded
 * by alerts's admins.
 *
 *  
 * 
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different alerts
 * @see BxAlertsModule::serviceHomepageBlock
 * BxDolService::call('alerts', 'homepage_block', array());
 *
 * Profile block with user's alerts
 * @see BxAlertsModule::serviceProfileBlock
 * BxDolService::call('alerts', 'profile_block', array($iProfileId));
 *

 *
 * Member menu item for alerts (for internal usage only)
 * @see BxAlertsModule::serviceGetMemberMenuItem
 * BxDolService::call('alerts', 'get_member_menu_item', array());
 *
 *
 

 *
 */
class BxAlertsModule extends BxDolTwigModule {

    var $_oPrivacy;
    var $_aQuickCache = array ();

    function BxAlertsModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'modzzz_alerts_filter';
        $this->_sPrefix = 'modzzz_alerts';

        bx_import ('Privacy', $aModule);
        $this->_oPrivacy = new BxAlertsPrivacy($this);

        $GLOBALS['oBxAlertsModule'] = &$this;
    }

    function actionHome () {
		$aProfile = getProfileInfo ($this->_iProfileId);
        $this->_browseMy ($aProfile); 
    }
	 
    function _browseMy (&$aProfile) {        
        parent::_browseMy ($aProfile, _t('_modzzz_alerts_page_title_my_alerts'));
    } 

    function actionView ($sUri) {
        parent::_actionView ($sUri, _t('_modzzz_alerts_msg_pending_approval'));
    }
 
    function actionSearch ($sKeyword = '', $sCategory = '') {
        parent::_actionSearch ($sKeyword, $sCategory, _t('_modzzz_alerts_page_title_search'));
    }
    
    function _defineActions () {
        defineMembershipActions(array('alerts add alert', 'alerts edit any alert', 'alerts delete any alert'));
    }

    // ================================== external actions
 
  
    /**
     * Member accountpage block with different alerts
     * @return html to display on accountpage in a block
     */     

    function serviceAccountBlock () {
 
		if(getParam("modzzz_alerts_activated") != 'on')
			return;

		if(!$this->isAllowedAdd())
			return;

        $this->_oTemplate->addCss(array('main.css'));

		$sCreateUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my&modzzz_alerts_filter=add_alert'; 
		$sManageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my&modzzz_alerts_filter=manage_alerts'; 

		$aVars = array( 
			'create_url' => $sCreateUrl, 
			'manage_link' => _t('_modzzz_alerts_keyphrase_manage_link', $sManageUrl),  
  		);
 
		$sCode = $this->_oTemplate->parseHtmlByName('create_keyphrase', $aVars);  

		return $sCode; 
    }
 
 
    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_modzzz_alerts'), _t('_modzzz_alerts'), 'alerts.png');
    }
 
    // ================================== admin actions

    function actionAdministration ($sUrl = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();

        $aMenu = array(
 			'manage' => array(
                'title' => _t('_modzzz_alerts_menu_admin_manage_actions'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/manage', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array()),
            ),  
 			'templates' => array(
                'title' => _t('_modzzz_alerts_menu_admin_manage_templates'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/templates', 
                '_func' => array ('name' => 'actionAdministrationTemplates', 'params' => array()),
            ),  
            'settings' => array(
                'title' => _t('_modzzz_alerts_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'settings';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_alerts_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin ('admin.css');
        $this->_oTemplate->addCssAdmin ('unit.css');
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_extra.css'); 
        $this->_oTemplate->addCssAdmin ('forms_adv.css');    
        $this->_oTemplate->addCssAdmin ('settings.css');    
        //$this->_oTemplate->addJsAdmin ('alerts_templates.js');    
  
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_alerts_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Alerts');
    }
  
	function actionAdministrationTemplates($mixedResult='') {
	 
		$oSettings = new BxDolAdminSettings(4); 
	  
		$sPageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/templates';

		//--- Process submit ---//
		$mixedResultSettings = '';
		$mixedResultTemplates = '';
		if(isset($_POST['save']) && isset($_POST['cat'])) {
			$mixedResultSettings = $oSettings->saveChanges($_POST);
		} elseif(isset($_POST['action']) && $_POST['action'] == 'get_translations') {
			$aTranslation = $GLOBALS['MySQL']->getRow("SELECT `Subject` AS `subject`, `Body` AS `body` FROM `sys_email_templates` WHERE `Name`='" . process_db_input($_POST['templ_name']) . "' AND `LangID`='" . (int)$_POST['lang_id'] . "' LIMIT 1");
			if(empty($aTranslation))
				$aTranslation = $GLOBALS['MySQL']->getRow("SELECT `Subject` AS `subject`, `Body` AS `body` FROM `sys_email_templates` WHERE `Name`='" . process_db_input($_POST['templ_name']) . "' AND `LangID`='0' LIMIT 1");
				
			$oJson = new Services_JSON();   
			echo $oJson->encode(array('subject' => $aTranslation['subject'], 'body' => $aTranslation['body']));
			exit;
		}



		$aForm = array(
			'form_attrs' => array(
				'id' => 'adm-email-templates',
				'action' => '',
				'method' => 'post',
				'enctype' => 'multipart/form-data',
			),
			'params' => array (
					'db' => array(
						'table' => 'sys_email_templates',
						'key' => 'ID',
						'uri' => '',
						'uri_title' => '',
						'submit_name' => 'adm-emial-templates-save'
					),
				),
			'inputs' => array ()
		);

		$aLanguages = $GLOBALS['MySQL']->getAll("SELECT `ID` AS `id`, `Title` AS `title` FROM `sys_localization_languages`");
		
		$aLanguageChooser = array(array('key' => 0, 'value' => 'default'));
		foreach($aLanguages as $aLanguage)
			$aLanguageChooser[] = array('key' => $aLanguage['id'], 'value' => $aLanguage['title']);
		
		$sLanguageCpt = _t('_adm_txt_email_language');
		$sSubjectCpt = _t('_adm_txt_email_subject');
		$sBodyCpt = _t('_adm_txt_email_body');

		$aEmails = $GLOBALS['MySQL']->getAll("SELECT DISTINCT tmpl.`ID` AS `id`, tmpl.`Name` AS `name`, tmpl.`Subject` AS `subject`, tmpl.`Body` AS `body`, tmpl.`Desc` AS `description` FROM `sys_email_templates` tmpl WHERE tmpl.`Name` IN ('modzzz_alerts_now_notify','modzzz_alerts_daily_notify','modzzz_alerts_weekly_notify') AND `LangID`='0' ORDER BY `ID`");
		foreach($aEmails as $aEmail) {
			$aForm['inputs'] = array_merge($aForm['inputs'], array(
				$aEmail['name'] . '_Beg' => array(
					'type' => 'block_header',
					'caption' => $aEmail['description'],
					'collapsable' => true,
					'collapsed' => true
				),
				$aEmail['name'] . '_Language' => array(
					'type' => 'select',
					'name' => $aEmail['name'] . '_Language',
					'caption' => $sLanguageCpt,
					'value' =>  0,
					'values' => $aLanguageChooser,
					'db' => array (
						'pass' => 'Int',
					),
					'attrs' => array(
						'onchange' => "javascript:getAlertsTranslations(this, '{$sPageUrl}')"
					)
				),
				$aEmail['name'] . '_Subject' => array(
					'type' => 'text',
					'name' => $aEmail['name'] . '_Subject',
					'caption' => $sSubjectCpt,
					'value' => $aEmail['subject'],
					'db' => array (
						'pass' => 'Xss',
					),
				),
				$aEmail['name'] . '_Body' => array(
					'type' => 'textarea',
					'name' => $aEmail['name'] . '_Body',
					'caption' => $sBodyCpt,
					'value' => $aEmail['body'],
					'db' => array (
						'pass' => 'XssHtml',
					),
				),
				$aEmail['name'] . '_End' => array(
					'type' => 'block_end'
				)
			));
		}
		
		$aForm['inputs']['adm-emial-templates-save'] = array(
			'type' => 'submit',
			'name' => 'adm-emial-templates-save',
			'value' => _t('_adm_btn_email_save'),
		);

		$oForm = new BxTemplFormView($aForm);
		$oForm->initChecker();

		$sResult = "";
		if($oForm->isSubmittedAndValid()) {
			$iResult = 0;
			foreach($aEmails as $aEmail) {
				$iEmailId = (int)$GLOBALS['MySQL']->getOne("SELECT `ID` FROM `sys_email_templates` WHERE `Name`='" . process_db_input($aEmail['name']) . "' AND `LangID`='" . (int)$_POST[$aEmail['name'] . '_Language'] . "' LIMIT 1");
				if($iEmailId != 0)
					$iResult += (int)$GLOBALS['MySQL']->query("UPDATE `sys_email_templates` SET `Subject`='" . process_db_input($_POST[$aEmail['name'] . '_Subject']) . "', `Body`='" . process_db_input($_POST[$aEmail['name'] . '_Body']) . "' WHERE `ID`='" . $iEmailId . "'");
				else
					$iResult += (int)$GLOBALS['MySQL']->query("INSERT INTO `sys_email_templates` SET `Name`='" . process_db_input($aEmail['name']) . "', `Subject`='" . process_db_input($_POST[$aEmail['name'] . '_Subject']) . "', `Body`='" . process_db_input($_POST[$aEmail['name'] . '_Body']) . "', `LangID`='" . (int)$_POST[$aEmail['name'] . '_Language'] . "'");
			}
			
			$sResult .= MsgBox(_t($iResult > 0 ? "_adm_txt_email_success_save" : "_adm_txt_email_nothing_changed"), 3);
		}
		$sResult .= $oForm->getCode();

		return DesignBoxAdmin(_t('_adm_box_cpt_email_templates'), $GLOBALS['oAdmTemplate']->parseHtmlByName('email_templates.html', array(
			'content' => stripslashes($sResult),
			'loading' => LoadingBox('adm-email-loading')
		)));
	}
 
    function actionAdministrationManage () {
	   
		if(isset($_POST['submit_form']) && !empty($_POST['submit_form'])){ 
			foreach($_POST['item'] as $iKey) {  
				$bStatus = (int)$_POST['active'][$iKey];
 
		 		$this->_oDb->query("UPDATE `" . $this->_oDb->_sPrefix . "actions` SET  `active`='$bStatus' WHERE `id`=$iKey");
			}  
		}
		
		$arrActions =  $this->_oDb->getNotifyActions(false);
 
        $aForm = array(
            'form_attrs' => array(
                'action' => '',
                'method' => 'post',
            ), 
		    'params' => array (
			   'db' => array( 
				   'submit_name' => 'submit_form',     
			   ),
		   ), 
		);
  
		$iter=1;
 		foreach($arrActions as $aEachAction){  
		
 			$iId = $aEachAction['id'];
			$sActionC = _t($aEachAction['group']); 
			$sStatus = ($aEachAction['active']) ? "checked='checked'" : "";
  
			$aForm['inputs']["Item{$iter}"] = array(
				'type' => 'custom',
				'name' => "Item{$iter}",
				'content' =>  "<div style='width:100%'>
								<div style='float:left;width:90%'>{$sActionC}</div>  
								<div style='float:left;width:5%'><input type=checkbox name='active[$iId]' value='1' {$sStatus}></div>
								<input type=hidden name='item[$iId]' value='{$iId}'> 
							  </div>
							  <div class='clear_both'></div>",  
				'colspan' => true
			);
  
 			$iter++; 
		}//END

		if(count($arrActions)) { 
			$aForm['inputs']['Submit'] = array (
				'type' => 'submit',
				'name' => 'submit_form',
				'value' => _t('_Submit'),
				'colspan' => false,
			);  
		}else{
			 $aForm['inputs']["NoItem"] = array(
				'type' => 'custom',
				'name' => "NoItem",
				'content' =>  MsgBox(_t("_modzzz_notify_no_actions")), 
				'colspan' => true
			);  
		}

		$oForm = new BxTemplFormView($aForm);
		$sCode .= '<div class="dbContent">' . $oForm->getCode() . '</div>'; 
 
		return $sCode;
	}
   
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_alerts') == 'on'));
		 
        return $bEnabled;
    }
 
    function serviceDeleteProfileData ($iProfileId) {

        $iProfileId = (int)$iProfileId;

        if (!$iProfileId)
            return false;
 
       $this->_oDb->removeProfileEntries($iProfileId); 
    }

    function serviceResponseProfileDelete ($oAlert) {

        if (!($iProfileId = (int)$oAlert->iObject))
            return false;

        $this->serviceDeleteProfileData ($iProfileId);
        
        return true;
    }
 
    function isAllowedView ($aDataEntry, $isPerformAction = false) {

        // admin and owner only have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;
 
		return false; 
     }

    function isAllowedBrowse ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_ALERTS_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
 
    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_ALERTS_ADD_ALERT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

   function _addForm ($sRedirectUrl) {

        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {

            $sStatus = 'approved' ;
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
            );                        
            $aValsAdd[$this->_oDb->_sFieldAuthorId] = $this->_iProfileId;

            $iEntryId = $oForm->insert ($aValsAdd);

            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);
                if (!$sRedirectUrl)
                    $sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else {
            
            echo $oForm->getCode ();

        }
    }


    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_ALERTS_EDIT_ANY_ALERT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_ALERTS_DELETE_ANY_ALERT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }   

    function onEventCreate ($iEntryId, $sStatus, $aDataEntry = array()) {

		$oAlert = new BxDolAlerts($this->_sPrefix, 'add', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		$oAlert->alert();
    }


	//alerts subscribers 
	function alertAtOnce($sUnit, $sAction, $iObject) {

		$iRecipientId = $this->_iProfileId;

		if(!$iRecipientId)
			return;
  
		$aObject = $this->_oDb->getObjectRecord($sUnit, $iObject);
		$sItemTitle =$aObject['title'];
		$sItemDesc = $aObject['desc'];
		$sItemUri = $aObject['uri'];
		$sClass = $aObject['class'];

		if($sClass){
			$oModule = BxDolModule::getInstance($sClass);   
			$sModuleUri = $GLOBALS['site']['url'] . $oModule->_oConfig->getBaseUri();
		} 

		$sItemUrl = $aObject['view_uri'];
		$sItemUrl = str_replace("{id}", $iObject, $sItemUrl);
		$sItemUrl = str_replace("{uri}", $sItemUri, $sItemUrl);
		$sItemUrl = str_replace("{module_url}", $sModuleUri, $sItemUrl);
		$sItemUrl = str_replace("{site_url}", $GLOBALS['site']['url'], $sItemUrl);
 
		$aRecipient = getProfileInfo($iRecipientId);
		$sRecipientName = $aRecipient['NickName'];
		$sRecipientEmail = $aRecipient['Email'];
	   
		$oEmailTemplate = new BxDolEmailTemplates(); 
 		$aTemplate = $oEmailTemplate->getTemplate('modzzz_alerts_now_notify', $iRecipientId);
		
		$sMessage = str_replace("<RecipientName>", $sRecipientName, $aTemplate['Body']);  
 
		$aUnits = $this->_oDb->getUnits();
 
		$aKeywords = $this->_oDb->getKeyWords($iRecipientId, $sUnit); 
 

		foreach($aKeywords as $aEachKeyword){
			$sPhrase = $aEachKeyword['phrase']; 
			$sGroup = ucwords($aUnits[$sUnit]);
 			$sSearch = $aEachKeyword['search'];  
			$sEmail = $aEachKeyword['deliver']; 
			$sEmail = ($sEmail) ? $sEmail : $sRecipientEmail;
 /*
			$aAllEntries = $this->_oDb->getObjectRecord(0, true);
 
			if (!preg_match($expr, $sItemTitle.$sItemDesc)) {
			   continue;
			} 
 */

		    $expr = '/\b'.$sPhrase.'\b/i';
			$replace = '<b>'.$sPhrase.'</b>';

			switch($sSearch){
				case 'title':
					$sSearchText = $sItemTitle;
					$sItemTitle = preg_replace($expr,$replace,$sItemTitle);
 				break;
				case 'desc':
					$sSearchText = $sItemDesc;
					$sItemDesc = preg_replace($expr,$replace,$sItemDesc);
				break;
				case 'both':
					$sSearchText = $sItemTitle.$sItemDesc;
					$sItemTitle = preg_replace($expr,$replace,$sItemTitle);
					$sItemDesc = preg_replace($expr,$replace,$sItemDesc);
				break;
			}
 
			if(!stristr($sSearchText, $sPhrase)) {
 				continue;	 
			}
			
			$sImage = '';
			switch($sUnit){
				case 'bx_videos':
					$a = BxDolService::call('videos', 'get_video_array', array($iObject), 'browse'); 
					$sImage = $a['file']; 
				break;	
				case 'bx_photos':
					$a = BxDolService::call('photos', 'get_photo_array', array($iObject,'browse'), 'Search'); 
					$sImage = $a['file'];  
				break;	
				case 'bx_sounds':
					$a = BxDolService::call('sounds', 'get_sound_array', array($iObject), 'browse'); 
					$sImage = $a['file']; 
				break;	
				case 'bx_files':
					$a = BxDolService::call('files', 'get_file_array', array($iObject), 'browse'); 
					$sImage = $a['file']; 
				break;	 
			}
 
			$aVars['bx_repeat:entries'][] = array(
				'item_url' =>  $sItemUrl,  
				'item_title' =>  $sItemTitle,  
				'bx_if:description' => array( 
					'condition' =>  strlen(trim($sItemDesc)),
					'content' => array(
						'item_desc' => $sItemDesc,  
					) 
				),  
				'bx_if:photo' => array( 
					'condition' =>  strlen(trim($sImage)),
					'content' => array(
						'item_image' => $sImage,  
					) 
				) 
 			);	        
	   
			$sData = $this->_oTemplate->parseHtmlByName("member_alerts", $aVars);
			$sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $aTemplate['Subject']);
			$sSubject = str_replace("<Phrase>", $sPhrase, $sSubject); 
			
			$sMessage = str_replace("<Data>", $sData, $sMessage); 
			$sMessage = str_replace("<Phrase>", $sPhrase, $sMessage); 
			$sMessage = str_replace("<Group>", $sGroup, $sMessage); 
			$sMessage = str_replace("<SiteName>", $GLOBALS['site']['title'], $sMessage); 
			$sMessage = str_replace("<SiteUrl>", $GLOBALS['site']['url'], $sMessage); 
 
			sendMail( $sEmail, $sSubject, $sMessage, $iRecipientId, $aPlus , 'html'); 
		}
 
  	}

	function alertAtOnceForum($sUnit, $sAction, $iObject, $aExtras=array()) {
 	    global $gConf;
 
		$iRecipientId = $this->_iProfileId;
 
		if(!$iRecipientId)
			return;
 
		if(empty($aExtras)){ 
			$arrTopic = $this->_oDb->getTopic ($iObject, $sAction); 
			$iTopicId =  $arrTopic['topic_id']; 
			$iForumId = $arrTopic['forum_id'];  
			$sItemTitle = $arrTopic['topic_title'];
			$sItemUri = $arrTopic['topic_uri'];
			$sItemUrl = $gConf['url']['base'] . sprintf($gConf['rewrite']['topic'], $sItemUri);
	  
			$aPost = $this->_oDb->getPost($iTopicId); 
			$sItemDesc = $aPost['post_text']; 
		}else{ 
			$iTopicId =  $iObject; 
			$iForumId = $aExtras[0];
			$sItemTitle = $aExtras[1];
			$sItemDesc = $aExtras[2];
			$sItemUri = $aExtras[5];
			$sItemUrl = $gConf['url']['base'] . sprintf($gConf['rewrite']['topic'], $sItemUri);  
		}

		$aRecipient = getProfileInfo($iRecipientId);
		$sRecipientName = $aRecipient['NickName'];
		$sRecipientEmail = $aRecipient['Email'];
	   
		$oEmailTemplate = new BxDolEmailTemplates(); 
 		$aTemplate = $oEmailTemplate->getTemplate('modzzz_alerts_now_notify', $iRecipientId);
		
		$aKeywords = $this->_oDb->getKeyWords($iRecipientId, $sUnit); 

		$sMessage = str_replace("<RecipientName>", $sRecipientName, $aTemplate['Body']);  

		$aUnits = $this->_oDb->getUnits();

		foreach($aKeywords as $aEachKeyword){
			$sPhrase = $aEachKeyword['phrase'];
 			$sGroup = ucwords($aUnits[$sUnit]);
 			$sSearch = $aEachKeyword['search']; 

 /*
			$aAllEntries = $this->_oDb->getObjectRecord(0, true);
		    $expr = '/\b'.$sPhrase.'\b/i';
		    $content = preg_replace($expr,$replace,$content);

			if (!preg_match($expr, $sItemTitle.$sItemDesc)) {
			   continue;
			} 
 */

		    $expr = '/\b'.$sPhrase.'\b/i';
			$replace = '<b>'.$sPhrase.'</b>';

			switch($sSearch){
				case 'title':
					$sSearchText = $sItemTitle;
					$sItemTitle = preg_replace($expr,$replace,$sItemTitle);
 				break;
				case 'desc':
					$sSearchText = $sItemDesc;
					$sItemDesc = preg_replace($expr,$replace,$sItemDesc);
				break;
				case 'both':
					$sSearchText = $sItemTitle.$sItemDesc;
					$sItemTitle = preg_replace($expr,$replace,$sItemTitle);
					$sItemDesc = preg_replace($expr,$replace,$sItemDesc);
				break;
			}
   
			if(!stristr($sSearchText, $sPhrase)) {
 				continue;	 
			}
  
			$aVars['bx_repeat:entries'][] = array(
				'item_url' =>  $sItemUrl,  
				'item_title' =>  $sItemTitle,  
				'bx_if:description' => array( 
					'condition' =>  strlen(trim($sItemDesc)),
					'content' => array(
						'item_desc' => $sItemDesc,  
					) 
				), 
 			);	 
 
			$sData = $this->_oTemplate->parseHtmlByName("member_alerts", $aVars);
			$sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $aTemplate['Subject']);
			$sSubject = str_replace("<Phrase>", $sPhrase, $sSubject); 
			
			$sMessage = str_replace("<Data>", $sData, $sMessage); 
			$sMessage = str_replace("<Phrase>", $sPhrase, $sMessage); 
			$sMessage = str_replace("<Group>", $sGroup, $sMessage); 
			$sMessage = str_replace("<SiteName>", $GLOBALS['site']['title'], $sMessage); 
			$sMessage = str_replace("<SiteUrl>", $GLOBALS['site']['url'], $sMessage); 
	  
			sendMail( $sRecipientEmail, $sSubject, $sMessage, $iRecipientId, $aPlus , 'html'); 
		}

  	}
  
	function actionProcess($sPeriod) {
		$this->processPeriodically($sPeriod);
	}

	//alerts subscribers daily/weekly
	function processPeriodically($sPeriod) {
  
		$aUnits = $this->_oDb->getUnits();

		$aKeywords = $this->_oDb->getAllNotify($sPeriod);

		if(empty($aKeywords))
			return;

		foreach($aKeywords as $aEachKeyword){

			$sPhrase = $aEachKeyword['phrase']; 
			$sSearch = $aEachKeyword['search']; 

			$iRecipientId = $aEachKeyword['author_id']; 
			$aRecipient = getProfileInfo($iRecipientId);
			$sRecipientName = $aRecipient['NickName'];
			$sRecipientEmail = $aRecipient['Email'];
		   
			$oEmailTemplate = new BxDolEmailTemplates(); 
			$aTemplate = $oEmailTemplate->getTemplate('modzzz_alerts_'.$sPeriod.'_notify', $iRecipientId);
			
			$sMessage = str_replace("<RecipientName>", $sRecipientName, $aTemplate['Body']);  
  
			$expr = '/\b'.$sPhrase.'\b/i';
			$replace = '<b>'.$sPhrase.'</b>';
 
			$aNotifyUnits = explode(';', $aEachKeyword['unit']);

			if(empty($aNotifyUnits))
				continue;

			foreach($aNotifyUnits as $sUnit){
 			  
				$sGroup = ucwords($aUnits[$sUnit]); 
				$sMessage = str_replace("<Group>", $sGroup, $sMessage); 

				$aObjects = $this->_oDb->getObjectRecords($sUnit);
				
				if(empty($aObjects)) continue;

				$sData = '';
				$aVars = array();
				foreach($aObjects as $aObject){

					$iObject = $aObject['id'];
					$sItemTitle = $aObject['title'];
					$sItemDesc = $aObject['desc'];
					$sItemUri = $aObject['uri'];
					$sClass = $aObject['class'];

					switch($sSearch){
						case 'title':
							$sSearchText = $sItemTitle;
							$sItemTitle = preg_replace($expr,$replace,$sItemTitle);
						break;
						case 'desc':
							$sSearchText = $sItemDesc;
							$sItemDesc = preg_replace($expr,$replace,$sItemDesc);
						break;
						case 'both':
							$sSearchText = $sItemTitle.$sItemDesc;
							$sItemTitle = preg_replace($expr,$replace,$sItemTitle);
							$sItemDesc = preg_replace($expr,$replace,$sItemDesc);
						break;
					}
		 
					if(!stristr($sSearchText, $sPhrase)) {
						continue;	 
					}
 
					if($sClass){
						$oModule = BxDolModule::getInstance($sClass);   
						$sModuleUri = $GLOBALS['site']['url'] . $oModule->_oConfig->getBaseUri();
					} 

					$sItemUrl = $aObject['view_uri'];
					$sItemUrl = str_replace("{id}", $iObject, $sItemUrl);
					$sItemUrl = str_replace("{uri}", $sItemUri, $sItemUrl);
					$sItemUrl = str_replace("{module_url}", $sModuleUri, $sItemUrl);
					$sItemUrl = str_replace("{site_url}", $GLOBALS['site']['url'], $sItemUrl);
			   
					$sImage = '';
					switch($sUnit){
						case 'bx_videos':
							$a = BxDolService::call('videos', 'get_video_array', array($iObject), 'browse'); 
							$sImage = $a['file']; 
						break;	
						case 'bx_photos':
							$a = BxDolService::call('photos', 'get_photo_array', array($iObject,'browse'), 'Search'); 
							$sImage = $a['file'];  
						break;	
						case 'bx_sounds':
							$a = BxDolService::call('sounds', 'get_sound_array', array($iObject), 'browse'); 
							$sImage = $a['file']; 
						break;	
						case 'bx_files':
							$a = BxDolService::call('files', 'get_file_array', array($iObject), 'browse'); 
							$sImage = $a['file']; 
						break;	 
					}
		 
					$aVars['bx_repeat:entries'][] = array(
						'item_url' =>  $sItemUrl,  
						'item_title' =>  $sItemTitle,  
						'bx_if:description' => array( 
							'condition' =>  strlen(trim($sItemDesc)),
							'content' => array(
								'item_desc' => $sItemDesc,  
							) 
						),  
						'bx_if:photo' => array( 
							'condition' =>  strlen(trim($sImage)),
							'content' => array(
								'item_image' => $sImage,  
							) 
						) 
					);	        
				
				}//end foreach object

				$aVars['group'] = $sGroup;

				$sData = $this->_oTemplate->parseHtmlByName("period_alerts", $aVars); 
 
				$sAllUnitsData = $sAllUnitsData . $sData; 
  
			}//end foreach unit
  
			if(!$sAllUnitsData)
				  return;

			$sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $aTemplate['Subject']);
			$sSubject = str_replace("<Phrase>", $sPhrase, $sSubject); 
			
			$sMessage = str_replace("<Data>", $sAllUnitsData, $sMessage); 
			$sMessage = str_replace("<Phrase>", $sPhrase, $sMessage); 
			$sMessage = str_replace("<SiteName>", $GLOBALS['site']['title'], $sMessage); 
			$sMessage = str_replace("<SiteUrl>", $GLOBALS['site']['url'], $sMessage); 

			sendMail( $sRecipientEmail, $sSubject, $sMessage, $iRecipientId, $aPlus , 'html'); 		
 
 			$sAllUnitsData = '';

		}//end foreach keyword
  
	}//end func
 

}

?>