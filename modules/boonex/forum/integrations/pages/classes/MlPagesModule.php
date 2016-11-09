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
require_once('MlPagesPSFM.php');
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );
function ml_pages_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'pages') {
        $oMain = BxDolModule::getInstance('MlPagesModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a);
}
ini_set("memory_limit","120M");
bx_import('BxDolTwigModule');
bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxTemplSearchResult');
define ('ML_PAGES_PHOTOS_CAT', 'Pages');
define ('ML_PAGES_PHOTOS_TAG', 'pages');

define ('ML_PAGES_VIDEOS_CAT', 'Pages');
define ('ML_PAGES_VIDEOS_TAG', 'pages');

define ('ML_PAGES_SOUNDS_CAT', 'Pages');
define ('ML_PAGES_SOUNDS_TAG', 'pages');

define ('ML_PAGES_FILES_CAT', 'Pages');
define ('ML_PAGES_FILES_TAG', 'pages');

define ('ML_PAGES_MAX_FANS', 1000);

define ("PARENT_CAT_PATH", BX_DIRECTORY_PATH_MODULES."modloaded/pages/templates/base/images/icons/categories/");
define ("PARENT_CAT_URL", BX_DOL_URL_MODULES."modloaded/pages/templates/base/images/icons/categories/");
/**
 * Pages module
 *
 * This module allow users to post upcoming pages, 
 * users can rate, comment, discuss it.
 * Page can have photo, video, sound and files.
 *
 * 
 *
 * Profile's Wall:
 * 'add page' page are displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new page was created
 * change - pages was chaned
 * join - somebody joined page
 * rate - somebody rated page
 * commentPost - somebody posted comment in page
 *
 *
 *
 * Memberships/ACL:
 * pages view - ML_PAGES_VIEW
 * pages browse - ML_PAGES_BROWSE
 * pages search - ML_PAGES_SEARCH
 * pages add - ML_PAGES_ADD
 * pages comments delete and edit - ML_PAGES_COMMENTS_DELETE_AND_EDIT
 * pages edit any page - ML_PAGES_EDIT_ANY_EVENT
 * pages delete any page - ML_PAGES_DELETE_ANY_EVENT
 * pages mark as featured - ML_PAGES_MARK_AS_FEATURED
 * pages approve - ML_PAGES_APPROVE
 * pages broadcast message - ML_PAGES_BROADCAST_MESSAGE
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different pages
 * @see MlPagesModule::serviceHomepageBlock
 * BxDolService::call('pages', 'homepage_block', array());
 *
 * Profile block with user's pages
 * @see MlPagesModule::serviceProfileBlock
 * BxDolService::call('pages', 'profile_block', array($iProfileId));
 *
 * Page's forum permissions (for internal usage only)
 * @see MlPagesModule::serviceGetForumPermission
 * BxDolService::call('pages', 'get_forum_permission', array($iMemberId, $iForumId));
 *
 * Member menu item for pages (for internal usage only)
 * @see MlPagesModule::serviceGetMemberMenuItem
 * BxDolService::call('pages', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'ml_pages'
 * The following alerts are rised
 *
 *  join - user joined an page
 *      $iObjectId - page id
 *      $iSenderId - joined user
 *
 *  add - new page was added
 *      $iObjectId - page id
 *      $iSenderId - creator of an page
 *      $aExtras['status'] - status of added page
 *
 *  change - page's info was changed
 *      $iObjectId - page id
 *      $iSenderId - editor user id
 *      $aExtras['status'] - status of changed page
 *
 *  delete - page was deleted
 *      $iObjectId - page id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - page was marked/unmarked as featured
 *      $iObjectId - page id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if page was marked as featured and 0 - if page was removed from featured 
 *
 */
class MlPagesModule extends BxDolTwigModule {

    var $_iProfileId;
    var $_oPrivacy;
		var	$aFields = array(
			'Value'  => 'The value stored in the database',
			'LKey'   => 'Primary language key used for displaying',
			);
    function MlPagesModule(&$aModule) {

        parent::BxDolTwigModule($aModule);
        $this->_sFilterName = 'ml_pages_filter';
        $this->_sPrefix = 'ml_pages';

        $GLOBALS['oMlPagesModule'] = &$this;
        bx_import ('Privacy', $aModule);
        $this->_oPrivacy = new MlPagesPrivacy($this);
    }
		function _blockLocation ($sLocation, $iAuthorId, $sPrefix = false) {
			$aVars = array (   
				'display_form' => ($iAuthorId == $this->_iProfileId || isAdmin() ? 'visible' : 'none'),
				'ml_page_form_submit' => _t('_ml_pages_form_save'),
				'draggable' => ($iAuthorId == $this->_iProfileId || isAdmin() ? 'true' : 'false'),
				'ml_page_form_find' => _t('_ml_pages_form_find'),
				'Closest_matching_address' => _t('_ml_pages_closest_matching_address'),
				'Current_position' => _t('_ml_pages_current_position'),
				'default_address' => $sLocation ? $sLocation : getParam('ml_pages_default_location'),
			);
			return $this->_oTemplate->parseHtmlByName('location', $aVars);
		}
    function actionLocation($sUri) {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $sUri);
    		$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
    		$GLOBALS['oTopMenu']->setCustomSubHeader($aPageInfo['title']);
    		if ($_POST['save_location'])
    		{
    			if ($_POST['location'])
    				$this->_oDb->insertPageLocation($aPageInfo['id'], $_POST['location']);

    		}
    		$sLocation = $this->_oDb->getPageLocation($aPageInfo['id']);
    		$this->_oTemplate->pageStart();
		    $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        if (!$sLocation && $aPageInfo['author_id'] != $this->_iProfileId && !isAdmin())
        	echo MsgBox(_t('_ml_pages_action_page_no_location'));
        else
    			echo $this->_blockLocation ($sLocation, $aPageInfo['author_id'], false);
    		$this->_oTemplate->pageCode(_t('_ml_pages_action_page_location', $aPageInfo['title']));
    }
    function actionFollowBox ($sUri) {
    		global $site;
    		global $tmpl;
    		if (!$sUri) return;
				$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
        $a = array ('ID' => $aPageInfo['author_id'], 'Avatar' => $aPageInfo['thumb']);

        $aImageThumb = BxDolService::call('photos', 'get_image', array($a, 'thumb'), 'Search');
            
        $aVars = array (
        		'tmpl_name' => 'tmpl_' . $tmpl,
        		'url' => BX_DOL_URL_ROOT,
        		'page_url' => BX_DOL_URL_ROOT . 'm/pages/view/' . $sUri,
        		'find_us_on' => _t('_ml_pages_find_us_on'),
        		'site_title' => $site['title'],
        		'width' => $_GET['width'],
        		'height' => $_GET['height'],
        		'bg_image' => $aImageThumb['file'] ? $aImageThumb['file'] : getTemplateIcon('no-photo.png'),
        		'border_color' => $_GET['border_color'] ? $_GET['border_color'] : 'CCC',
        		'background_color' => $_GET['background_color'],
        		'colorscheme' => $_GET['colorscheme'],
        		'page_title' => '<b>' . $aPageInfo['title'] . '</b>',
        		'total_fans' => number_format($this->_oDb->getTotalFans($aPageInfo['id'])) . ' ' . _t('_ml_pages_total_fans'),
            'prefix' => 'id'.time().'_'.rand(1, 999999), 
        );

        echo $this->_oTemplate->parseHtmlByName('entry_view_block_follow', $aVars);
    }
    function actionFansBox ($sUri) {
    	global $tmpl;
    		global $site;
    		if (!$sUri) return;
				$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
				$aFans = $this->_oDb->getPageFans($aPageInfo['id']);
        $aVars = array (
        		'tmpl_name' => 'tmpl_' . $tmpl,
        		'url' => BX_DOL_URL_ROOT,
        		'page_url' => BX_DOL_URL_ROOT . 'm/pages/view/' . $sUri,
        		'find_us_on' => _t('_ml_pages_find_us_on'),
        		'site_title' => $site['title'],
        		'width' => $_GET['width'],
        		'height' => $_GET['height'],
        		'border_color' => $_GET['border_color'] ? $_GET['border_color'] : 'CCC',
        		'background_color' => $_GET['background_color'],
        		'colorscheme' => $_GET['colorscheme'],
        		'page_title' => '<b>' . $aPageInfo['title'] . '</b>',
        		'total_fans' => number_format($this->_oDb->getTotalFans($aPageInfo['id'])),
        		'people_is_fan_of' => _t('_ml_pages_people_is_fan_of'),
            'prefix' => 'id'.time().'_'.rand(1, 999999), 
            'bx_repeat:images_icons' => array (),
        );

        foreach ($aFans as $aData) {

						$sAvatar = $GLOBALS['oFunctions']->getMemberAvatar($aData['id']);
            $aVars['bx_repeat:images_icons'][] = array (
                'icon_url' => $sAvatar,
                'url' => BX_DOL_URL_ROOT,
                'title' => $aData['NickName'],
                'nick' => strmaxtextlen($aData['NickName'], 10),
            );
        }



        echo $this->_oTemplate->parseHtmlByName('entry_view_block_fans', $aVars);
    }
    function _blockVideo ($aReadyMedia, $iAuthorId, $sPrefix = false) {

        if (!$aReadyMedia)
          return MsgBox(_t('_ml_page_no_media_found', _t('_ml_pages_caption_videos')));

        $aVars = array (
            'title' => false,
            'prefix' => $sPrefix ? $sPrefix : 'id'.time().'_'.rand(1, 999999), 
            'bx_repeat:videos' => array (),
            'bx_repeat:icons' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {

            $a = BxDolService::call('videos', 'get_video_array', array($iMediaId), 'Search');
			$a['id'] = $iMediaId;

            $aVars['bx_repeat:videos'][] = array (
                'style' => false === $aVars['title'] ? '' : 'display:none;',
                'id' => $iMediaId,
                'video' => BxDolService::call('videos', 'get_video_concept', array($a), 'Search'),
            );            
            $aVars['bx_repeat:icons'][] = array (
                'id' => $iMediaId,
                'icon_url' => $a['file'],
                'title' => $a['title'],
            );
            if (false === $aVars['title'])
                $aVars['title'] = $a['title'];
        }

        if (!$aVars['bx_repeat:icons'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_videos', $aVars);
    }  
    function _blockPhoto (&$aReadyMedia, $iAuthorId, $sPrefix = false, $iPageId) {

        if (!$aReadyMedia)
          return MsgBox(_t('_ml_page_no_media_found', _t('_ml_pages_caption_photos')));

				if ($_POST['set_thumb'])
					db_res("UPDATE `ml_pages_main` SET `thumb` = {$_POST['set_thumb']} WHERE `id` = {$iPageId} LIMIT 1");
					
				$iThumb = db_value("SELECT `thumb` FROM `ml_pages_main` WHERE `id` = {$iPageId} LIMIT 1");
				
        $aVars = array (
            'image_url' => false,
            'title' => false,
            'prefix' => $sPrefix ? $sPrefix : 'id'.time().'_'.rand(1, 999999), 
            'bx_repeat:images_icons' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {

            $a = array ('id' => $iAuthorId, 'Avatar' => $iMediaId);

            $aImageFile = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');            
            if ($aImageFile['no_image']) 
                continue;

            $aImageIcon = BxDolService::call('photos', 'get_image', array($a, 'icon'), 'Search');
            if ($aImageIcon['no_image']) 
                continue;

            if (!$aVars['image_url']) {
                $aVars['image_url'] = $aImageFile['file'];
                $aVars['title'] = $aImageFile['title'];
                $aVars['set_as_thumb'] = ($iAuthorId == $this->_iProfileId || isAdmin() ? '<div class="button_wrapper"><input class="form_input_submit" type="submit" value="'._t('_ml_pages_set_as_thumb').'"/><div class="button_wrapper_close"></div></div>' : '');

            }
						$sChecked = $iThumb == $iMediaId ? 'checked' : '';
            $aVars['bx_repeat:images_icons'][] = array (
                'icon_url' => $aImageIcon['file'],
                'image_url' => $aImageFile['file'],
                'title' => $aImageIcon['title'],
                'default' => ($iAuthorId == $this->_iProfileId || isAdmin() ? '<input ' . $sChecked . ' value="'.$iMediaId.'" name="set_thumb" type="radio"/>' : ''),
            );
        }

        if (!$aVars['bx_repeat:images_icons'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_images', $aVars);
    }
    function _blockFiles ($aReadyMedia, $iAuthorId = 0, $iPageId = 0) {        

        if (!$aReadyMedia)
          return MsgBox(_t('_ml_page_no_media_found', _t('_ml_pages_caption_files')));

        $aVars = array (
            'bx_repeat:files' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {        

            $a = BxDolService::call('files', 'get_file_array', array($iMediaId), 'Search');
            if (!$a['date'])
                continue;

            bx_import('BxTemplFormView');
            $oForm = new BxTemplFormView(array());

            $aInputBtnDownload = array (
                'type' => 'submit',
                'name' => 'download', 
                'value' => _t ('_download'), 
                'attrs' => array(
                    'onclick' => "window.open ('" . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "download/".$iPageId."/{$iMediaId}','_self');",
                ),
            );

            $aVars['bx_repeat:files'][] = array (
                'id' => $iMediaId,
                'title' => $a['title'],
                'icon' => $a['file'],                
                'date' => defineTimeInterval($a['date']),
                'btn_download' => $oForm->genInputButton ($aInputBtnDownload),
            );            
        }

        if (!$aVars['bx_repeat:files'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_files', $aVars);
    }
    function _blockSound ($aReadyMedia, $iAuthorId, $sPrefix = false) {

        if (!$aReadyMedia)
          return MsgBox(_t('_ml_page_no_media_found', _t('_ml_pages_caption_sounds')));


        $aVars = array (
            'title' => false,
            'prefix' => $sPrefix ? $sPrefix : 'id'.time().'_'.rand(1, 999999), 
            'bx_repeat:sounds' => array (),
            'bx_repeat:icons' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {

            $a = BxDolService::call('sounds', 'get_music_array', array($iMediaId, 'browse'), 'Search');

            $aVars['bx_repeat:sounds'][] = array (
                'style' => false === $aVars['title'] ? '' : 'display:none;',
                'id' => $iMediaId,
                'sound' => getApplicationContent('mp3', 'player', array('id' => $iMediaId, 'user' => $_COOKIE['memberID'], 'password' => $_COOKIE['memberPassword']), true),
            );            
            $aVars['bx_repeat:icons'][] = array (
                'id' => $iMediaId,
                'icon_url' => $a['file'],
                'title' => $a['title'],
            );
            if (false === $aVars['title'])
                $aVars['title'] = $a['title'];
        }

        if (!$aVars['bx_repeat:icons'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_sounds', $aVars);
    }
    
    function actionHome () {
        parent::_actionHome(_t('_ml_pages_main'));
    }
		function _blockWebsiteForm($aData)
		{
        $aForm = array(
            'form_attrs' => array(
                'id'     => 'form_site',
                'name'     => 'form_site',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'ml_pages_websites',
                    'key' => 'entry_id',
                ),
            ),
            'inputs' => array(
                'website' => array(
                    'type' => 'text',
                    'name' => 'website',
                    'value' => $_POST['website'] ? $_POST['website'] : $aData['website'],
                    'caption' => _t('_ml_pages_website'),
                    'required' => true,
                    'info' => _t('_ml_pages_website_info'),
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t('_ml_pages_website_err'),
                    ),
                    'db' => array(
                        'pass' => 'Xss'
                    ),
                    'display' => true,
                ),                
                'width' => array(
                    'type' => 'text',
                    'name' => 'width',
                    'required' => false,
                    'info' => _t('_ml_pages_width_info'),
                    'value' => $_POST['width'] ? $_POST['width'] : $aData['width'],
                    'caption' => _t('_ml_pages_website_width'),
                    'db' => array(
                        'pass' => 'XssHtml'
                    )
                ),
                'height' => array(
                    'type' => 'text',
                    'name' => 'height',
                    'required' => false,
                    'info' => _t('_ml_pages_height_info'),
                    'value' => $_POST['height'] ? $_POST['height'] : $aData['height'],
                    'caption' => _t('_ml_pages_website_height'),
                    'db' => array(
                        'pass' => 'XssHtml'
                    )
                ),
                'page_width' => array(
                    'type' => 'text',
                    'name' => 'page_width',
                    'required' => false,
                    'info' => _t('_ml_pages_page_width_info'),
                    'value' => $_POST['page_width'] ? $_POST['page_width'] : $aData['page_width'],
                    'caption' => _t('_ml_pages_website_page_width'),
                    'db' => array(
                        'pass' => 'XssHtml'
                    )
                ),
                'submit' => array(
                    'type' => 'submit',
                    'name' => 'save_website',
                    'required' => false,
                    'value' => _t('_ml_pages_website_save'),
                    'db' => array(
                        'pass' => 'XssHtml'
                    )
                ),
            ),            
        );
        return $aForm;
		}
		function _blockWebsite ($aData, $sUri) {
			$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
			$aVars = array (   
				'url' => BX_DOL_URL_ROOT,
				'uri' => $sUri,
				'website' => $aData['website'],
				'width' => $aData['width'],
				'height' => $aData['height'],
				'embed_code' => _t('_ml_pages_embed_code'),
				'show_embed_code' => _t('_ml_pages_show_embed_code'),
				'be_a_fan' => _t('_ml_pages_be_a_fan'),
				'user_id' => $this->_iProfileId,
				'page_id' => $aPageInfo['id'],
				'display' => $this->isAllowedJoin($aPageInfo) ? 'block' : 'none',
			);
			return $this->_oTemplate->parseHtmlByName('website', $aVars);
		}
		function actionWebsiteSettings($sUri)
		{
			$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $sUri);
    		$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
    		$GLOBALS['oTopMenu']->setCustomSubHeader($aPageInfo['title']);
    		if ($_POST['save_website'])
    		{
    			if (($_POST['website'] && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $_POST['website'])) || !$_POST['website'])
						$sErr =  MsgBox(_t('_ml_page_website_invalid'));
    			if ($_POST['width'] && !is_numeric($_POST['width']))
    				$sErr .=  MsgBox(_t('_ml_page_width_invalid'));
    			if ($_POST['height'] && !is_numeric($_POST['height']))
    				$sErr .=  MsgBox(_t('_ml_page_height_invalid'));    				
    			if ($_POST['page_width'] && !is_numeric($_POST['page_width']))
    				$sErr .=  MsgBox(_t('_ml_page_page_width_invalid'));    			
    			if (!$sErr)
    			{
    				$this->_oDb->insertPageWebsite($aPageInfo['id'], $_POST['website'], $_POST['width'], $_POST['height'], $_POST['page_width']);
    				$sCode =  MsgBox(_t('_ml_page_website_save'));
    			}
    		}
    		$this->_oTemplate->pageStart();
		    $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
				
		    	
        if ($aPageInfo['author_id'] == $this->_iProfileId || isAdmin())
        {
	        $aData = $this->_oDb->getPageWebsite($aPageInfo['id']);
	    		$aForm = $this->_blockWebsiteForm($aData);
	    		$oForm = new BxTemplFormView($aForm);
	    		$sWebsiteView = $sErr . $sCode . $oForm->getCode();
	    		echo $sWebsiteView;
	    	}
	    	else
	    		echo MsgBox(_t('_ml_pages_msg_access_denied'));
	    	
    		$this->_oTemplate->pageCode(_t('_ml_page_website', $aPageInfo['title']));
		}
		function actionWebsite($sUri)
		{
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $sUri);
				
    		$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
    		if ($_POST['save_website'])
    		{
    			if (($_POST['website'] && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $_POST['website'])) || !$_POST['website'])
						$sErr =  MsgBox(_t('_ml_page_website_invalid'));
    			if ($_POST['width'] && !is_numeric($_POST['width']))
    				$sErr .=  MsgBox(_t('_ml_page_width_invalid'));
    			if ($_POST['height'] && !is_numeric($_POST['height']))
    				$sErr .=  MsgBox(_t('_ml_page_height_invalid'));    				
    			if (!$sErr)
    			{
    				$this->_oDb->insertPageWebsite($aPageInfo['id'], $_POST['website'], $_POST['width'], $_POST['height']);
    				$sCode =  MsgBox(_t('_ml_page_website_save'));
    			}
    		}
    		$this->_oTemplate->pageStart();
		    $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $aData = $this->_oDb->getPageWebsite($aPageInfo['id']);
       /* if ($aPageInfo['author_id'] == $this->_iProfileId || isAdmin())
        {
	    		$aForm = $this->_blockWebsiteForm($aData);
	    		$oForm = new BxTemplFormView($aForm);
	    		$sWebsiteView = $sErr . $sCode . $oForm->getCode();
	    	}*/
	    	
	    	if (!empty($aData))
	    	{
	    		if ($aData['page_width'] > 0)
	    			$GLOBALS['oSysTemplate']->setPageWidth($aData['page_width'] . 'px');
	    		echo $this->_blockWebsite($aData, $sUri);
	    	}
	    	else
	    		echo MsgBox(_t('_ml_pages_no_website_found'));
					
	    	//echo $sWebsiteView;
    		$this->_oTemplate->pageCode(_t('_ml_page_website', $aPageInfo['title']));
		}
    function actionVideos ($sUri) {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $sUri);
    		$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
    		$this->_oTemplate->pageStart();
		    $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
    		echo $this->_blockVideo ($this->_oDb->getMediaIds($aPageInfo['id'], 'videos'), $aPageInfo['author_id']);
    		$this->_oTemplate->pageCode(_t('_ml_page_videos', $aPageInfo['title']));
        //parent::_actionVideos ($sUri, _t('_ml_pages_caption_videos'));
    }
    function _blockEvents ($aData, $iAuthorId, $sPrefix = false, $sPaginate = '', $sUri) {
			global $oFunctions;

        if (empty($aData))
          return MsgBox(_t('_ml_page_no_event_added',BX_DOL_URL_ROOT, $sUri));
				$sTemplate = $iAuthorId == $this->_iProfileId ? 'unit_admin' : 'unit_event';
        foreach ($aData as $aEvent) {
        	$sImage = '';
	        if ($aEvent['thumb']) {
	            $a = array ('ID' => $aEvent['author_id'], 'Avatar' => $aEvent['thumb']);
	            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
	            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
	        } 
	        $aVars = array (            
	            'id' => $aEvent['id'],
	            'thumb_url' => $sImage ? $sImage : getTemplateIcon('no-photo.png'),
	            'page_url' => BX_DOL_URL_ROOT . 'm/events/view/' . $aEvent['uri'],
	            'page_title' => $aEvent['title'],
	            'spacer' => getTemplateIcon('spacer.gif'),
	            'page_start' => '',
	            'author' => $aEvent['author_id'] ? getNickName($aEvent['author_id']) : _t('_ml_pages_admin'),
	            'author_url' => $aEvent['author_id'] ? getProfileLink($aEvent['author_id']) : 'javascript:void(0);',
	            'author_title' => _t('_From'),
	        );  
        	$sCode .= $this->_oTemplate->parseHtmlByName($sTemplate, $aVars);
        }
        $sFormName = 'add_event_form';
				$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
								'action_del' => _t('_ml_pages_categ_del_event')
							), 'pathes', false);
        if ($sCode) {
        	$aVars = array (  
        		'filter_panel' => '',
        		'pagination' => $sPaginate,
        		'actions_panel' => $iAuthorId == $this->_iProfileId ? $sControls : '',
        		'form_name' => $sFormName,
        		'content' => $oFunctions->centerContent ($sCode, '.ml_pages_unit'),
        	);
        	$GLOBALS['oSysTemplate']->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
        	$GLOBALS['oSysTemplate']->addCss('unit.css');
        	return $this->_oTemplate->parseHtmlByName('manage', $aVars);
        }
    }
    function _blockAddEvents ($aData, $iAuthorId, $sPrefix = false, $sPaginate = '') {
			global $oFunctions;

        if (empty($aData))
          return MsgBox(_t('_ml_page_no_event_found',BX_DOL_URL_ROOT));
				
        foreach ($aData as $aEvent) {
        	$sImage = '';
	        if ($aEvent['thumb']) {
	            $a = array ('ID' => $aEvent['author_id'], 'Avatar' => $aEvent['thumb']);
	            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
	            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
	        } 
	        $aVars = array (            
	            'id' => $aEvent['id'],
	            'thumb_url' => $sImage ? $sImage : getTemplateIcon('no-photo.png'),
	            'page_url' => BX_DOL_URL_ROOT . 'm/events/view/' . $aEvent['uri'],
	            'page_title' => $aEvent['title'],
	            'spacer' => getTemplateIcon('spacer.gif'),
	            'page_start' => '',
	            'author' => $aEvent['author_id'] ? getNickName($aEvent['author_id']) : _t('_ml_pages_admin'),
	            'author_url' => $aEvent['author_id'] ? getProfileLink($aEvent['author_id']) : 'javascript:void(0);',
	            'author_title' => _t('_From'),
	        );  
        	$sCode .= $this->_oTemplate->parseHtmlByName('unit_admin', $aVars);
        }
        $sFormName = 'add_event_form';
				$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
								'action_save' => _t('_ml_pages_categ_add_event')
							), 'pathes', false);
        if ($sCode) {
        	$aVars = array (  
        		'filter_panel' => '',
        		'pagination' => $sPaginate,
        		'actions_panel' => $sControls,
        		'form_name' => $sFormName,
        		'content' => $oFunctions->centerContent ($sCode, '.ml_pages_unit'),
        	);
        	$GLOBALS['oSysTemplate']->addDynamicLocation($this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
        	$GLOBALS['oSysTemplate']->addCss('unit.css');
        	return $this->_oTemplate->parseHtmlByName('manage', $aVars);
        }
    }
    function actionEvents ($sUri) {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $sUri);
    		$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
    		$GLOBALS['oTopMenu']->setCustomSubHeader($aPageInfo['title']);
        if ($_POST['action_del'])
        {
        	if (!empty($_POST['entry']))
        	{
        		$aPageEvents = explode(';', $aPageInfo['Events']);
        		foreach ($_POST['entry'] as $iEventId)
        		{
        			$aKey = array_keys($aPageEvents, $iEventId);
        			unset($aPageEvents[$aKey[0]]);
        		}
        		$sPageEvents = implode(';', $aPageEvents);
        		$this->_oDb->updatePageEvents($aPageInfo['id'], $sPageEvents);
        		header("location:".BX_DOL_URL_ROOT."m/pages/events/{$sUri}");
        		exit;
        	}
        }
        $sEvents = str_replace(';', ',', $aPageInfo['Events']);
    		$sEvents = str_replace(';', ',', $aPageInfo['Events']);
    		$sEvents = $sEvents ? $sEvents : 0;
        bx_import('BxDolPaginate');
        $iPage = $_GET['page'] ? $_GET['page'] : 0;
        $iPerPage = $_GET['per_page'] ? $_GET['per_page'] : 12;
        $sLink  = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'events/' . $sUri . '&page={page}&per_page={per_page}';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sLink,
            'count' => $this->_oDb->getCountUserEventsByPage($this->_iProfileId, $sEvents),
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => '',
        ));
        $sPaginate = $oPaginate->getPaginate();
    		$aPageEvents = $this->_oDb->getUserEventsByPage($aPageInfo['author_id'], $sEvents, ($iPage > 1 ? $iPerPage : 0), $iPerPage);
    		//$aPageEvents = explode(getParam('ml_pages_multi_divider'), $aPageInfo['Events']);
    		$this->_oTemplate->pageStart();
		    $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
    		echo $this->_blockEvents ($aPageEvents, $aPageInfo['author_id'], false, $sPaginate, $sUri);
    		$this->_oTemplate->pageCode(_t('_ml_pages_action_page_events', $aPageInfo['title']));
        //parent::_actionPhotos ($sUri, _t('_ml_pages_caption_photos'));
    } 
    function actionAddEvents ($sUri) {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $sUri);
    		$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
    		$GLOBALS['oTopMenu']->setCustomSubHeader($aPageInfo['title']);
        if ($_POST['action_save'])
        {
        	if (!empty($_POST['entry']))
        	{
        		$sPageEvents = $aPageInfo['Events'] . ($aPageInfo['Events'] ? ';' : '') . implode(';', $_POST['entry']);
        		$aPageEvents = array_unique(explode(';', $sPageEvents));
        		$sPageEvents = implode(';', $aPageEvents);
        		$this->_oDb->updatePageEvents($aPageInfo['id'], $sPageEvents);
        		header("location:".BX_DOL_URL_ROOT."m/pages/events/{$sUri}");
        		exit;
        	}
        }
    		$sEvents = str_replace(';', ',', $aPageInfo['Events']);
    		$sEvents = $sEvents ? $sEvents : 0;
        bx_import('BxDolPaginate');
        $iPage = $_GET['page'] ? $_GET['page'] : 0;
        $iPerPage = $_GET['per_page'] ? $_GET['per_page'] : 12;
        $sLink  = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'add_events/' . $sUri . '&page={page}&per_page={per_page}';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sLink,
            'count' => $this->_oDb->getCountUserEvents($this->_iProfileId, $sEvents),
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => '',
        ));
        $sPaginate = $oPaginate->getPaginate();
    		$aUserEvents = $this->_oDb->getUserEvents($this->_iProfileId, $sEvents, ($iPage > 1 ? $iPerPage : 0), $iPerPage);
    		$this->_oTemplate->pageStart();
		    $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
    		echo $this->_blockAddEvents ($aUserEvents, $this->_iProfileId, false, $sPaginate);
    		$this->_oTemplate->pageCode(_t('_ml_pages_action_add_event', $aPageInfo['title']));
        //parent::_actionPhotos ($sUri, _t('_ml_pages_caption_photos'));
    }    
    function actionPhotos ($sUri) {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $sUri);
    		$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
    		$this->_oTemplate->pageStart();
		    $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
    		echo $this->_blockPhoto ($this->_oDb->getMediaIds($aPageInfo['id'], 'images'), $aPageInfo['author_id'], false, $aPageInfo['id']);
    		$this->_oTemplate->pageCode(_t('_ml_page_photos', $aPageInfo['title']));
        //parent::_actionPhotos ($sUri, _t('_ml_pages_caption_photos'));
    }

    function actionSounds ($sUri) {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $sUri);
    		$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
    		$this->_oTemplate->pageStart();
		    $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
    		echo $this->_blockSound ($this->_oDb->getMediaIds($aPageInfo['id'], 'sounds'), $aPageInfo['author_id']);
    		$this->_oTemplate->pageCode(_t('_ml_page_sounds', $aPageInfo['title']));
        //parent::_actionSounds ($sUri, _t('_ml_pages_caption_sounds'));
    }

    function actionFiles ($sUri) {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $sUri);
    		$aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
    		$this->_oTemplate->pageStart();
		    $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
    		echo $this->_blockFiles ($this->_oDb->getMediaIds($aPageInfo['id'], 'files'), $aPageInfo['author_id'], $aPageInfo['id']);
    		$this->_oTemplate->pageCode(_t('_ml_page_files', $aPageInfo['title']));
        //parent::_actionFiles ($sUri, _t('_ml_pages_caption_files'));
    }

    function actionComments ($sUri) {
        parent::_actionComments ($sUri, _t('_ml_pages_caption_comments'));
    }
    

    function actionBrowseFans ($sUri) {
        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getFansBrowse', $this->_oDb->getParam('ml_pages_perpage_browse_fans'), 'browse_fans/', _t('_ml_pages_caption_fans'));
    }

    function actionView ($sUri) {
        parent::_actionView ($sUri, _t('_ml_pages_msg_pending_approval'));        
    }
    function actionUpload ($sUri) {        
        $aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
        $this->_oTemplate->pageStart();
        echo BxDolService::call('photos', 'get_uploader_form', array(array('mode' => 'single', 'category' => 'page wall', 'album'=>_t('_ml_page_photo_album', $aPageInfo['title']), 'from_page_wall' => 0, 'owner_id' => $aPageInfo['author_id'], 'page_id' => $aPageInfo['id'], 'page_uri' => $sUri, 'media_type' => 'photos')), 'Uploader');
        $this->_oTemplate->pageCode($aPageInfo['title']);
    }
    function actionUploadPhotos ($sUri) {
        //parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_ml_pages_caption_upload_photos'));

        parent::_preProductTabs($sUri);
        $aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
        $this->_oTemplate->pageStart();
        if (!$this->isAllowedUploadPhotos($aPageInfo))
        {
        	echo MsgBox(_t('_ml_pages_upload_not_allowed', _t('_ml_pages_caption_photos')));
        	$this->_oTemplate->pageCode($aPageInfo['title']);
        	return;
        }
        echo BxDolService::call('photos', 'get_uploader_form', array(array('mode' => 'single', 'category' => 'page wall', 'album'=>_t('_ml_page_photo_album', $aPageInfo['title']), 'from_page_wall' => 0, 'owner_id' => $aPageInfo['author_id'], 'page_id' => $aPageInfo['id'], 'page_uri' => $sUri, 'media_type' => 'photos')), 'Uploader');
        $this->_oTemplate->pageCode($aPageInfo['title']);
    }

    function actionUploadVideos ($sUri) {
        //parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_ml_pages_caption_upload_videos'));

    		parent::_preProductTabs($sUri);
        $aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
        $this->_oTemplate->pageStart();
        if (!$this->isAllowedUploadVideos($aPageInfo))
        {
        	echo MsgBox(_t('_ml_pages_upload_not_allowed', _t('_ml_pages_caption_videos')));
        	$this->_oTemplate->pageCode($aPageInfo['title']);
        	return;
        }
        echo BxDolService::call('videos', 'get_uploader_form', array(array('restriction' => 3, 'mode' => 'single', 'category' => 'page wall', 'album'=>_t('_ml_page_video_album', $aPageInfo['title']), 'from_page_wall' => 0, 'owner_id' => $aPageInfo['author_id'], 'page_id' => $aPageInfo['id'], 'page_uri' => $sUri, 'media_type' => 'videos')), 'Uploader');
        $this->_oTemplate->pageCode($aPageInfo['title']);
    }
    function actionEmbedVideos ($sUri) {
    		parent::_preProductTabs($sUri);
        $aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
        $this->_oTemplate->pageStart();
        if (!$this->isAllowedUploadVideos($aPageInfo))
        {
        	echo MsgBox(_t('_ml_pages_upload_not_allowed', _t('_ml_pages_caption_videos')));
        	$this->_oTemplate->pageCode($aPageInfo['title']);
        	return;
        }
        echo BxDolService::call('videos', 'get_uploader_form', array(array('mode' => 'embed', 'category' => 'page wall', 'album'=>_t('_ml_page_video_album', $aPageInfo['title']), 'from_page_wall' => 0, 'owner_id' => $aPageInfo['author_id'], 'page_id' => $aPageInfo['id'], 'page_uri' => $sUri, 'media_type' => 'videos')), 'Uploader');
        $this->_oTemplate->pageCode($aPageInfo['title']);
    }
    
    function actionUploadSounds ($sUri) {
        //parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_ml_pages_caption_upload_sounds')); 

    		parent::_preProductTabs($sUri);
    		$this->_oTemplate->pageStart();
        $aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
        if (!$this->isAllowedUploadFiles($aPageInfo))
        {
        	echo MsgBox(_t('_ml_pages_upload_not_allowed', _t('_ml_pages_caption_sounds')));
        	$this->_oTemplate->pageCode($aPageInfo['title']);
        	return;
        }
      
        echo BxDolService::call('sounds', 'get_uploader_form', array(array('mode' => 'single', 'category' => 'page wall', 'album'=>_t('_ml_page_sound_album', $aPageInfo['title']), 'from_page_wall' => 0, 'owner_id' => $aPageInfo['author_id'], 'page_id' => $aPageInfo['id'], 'page_uri' => $sUri, 'media_type' => 'sounds')), 'Uploader');
        $this->_oTemplate->pageCode($aPageInfo['title']);
    }

    function actionUploadFiles ($sUri) {
        //parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_ml_pages_caption_upload_files')); 
				parent::_preProductTabs($sUri);
        $aPageInfo = $this->_oDb->getPageInfoByUri($sUri);
        $this->_oTemplate->pageStart();
        echo BxDolService::call('files', 'get_uploader_form', array(array('mode' => 'single', 'category' => 'page wall', 'album'=>_t('_ml_page_file_album', $aPageInfo['title']), 'from_page_wall' => 0, 'owner_id' => $aPageInfo['author_id'], 'page_id' => $aPageInfo['id'], 'page_uri' => $sUri, 'media_type' => 'files')), 'Uploader');
        $this->_oTemplate->pageCode($aPageInfo['title']);
    }

    function actionBroadcast ($iEntryId) {
        parent::_actionBroadcast ($iEntryId, _t('_ml_pages_caption_broadcast'), _t('_ml_pages_msg_broadcast_no_fans'), _t('_ml_pages_msg_broadcast_message_sent'));
    }    

    function actionInvite ($iEntryId) {
        parent::_actionInvite ($iEntryId, 'ml_pages_invitation', $this->_oDb->getParam('ml_pages_max_email_invitations'), _t('_ml_pages_invitation_sent'), _t('_ml_pages_no_users_msg'), _t('_ml_pages_caption_invite'));
    }

    function _getInviteParams ($aDataEntry, $aInviter) {
        return array (
                'PageName' => $aDataEntry['title'],
                'PageUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['id']) : 'javascript:void(0);',
                'InviterNickName' => $aInviter ? $aInviter['NickName'] : _t('_ml_pages_user_unknown'),
                'InvitationText' => stripslashes(strip_tags($_POST['inviter_text'])),
            );        
    }

    function actionCalendar ($iYear = '', $iMonth = '') {
        parent::_actionCalendar ($iYear, $iMonth, _t('_ml_pages_calendar'));
    }

    function actionSearch ($sKeyword = '', $sCountry = '') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_GET['Keyword'] = $sKeyword;
        if ($sCountry)
            $_GET['Country'] = explode(',', $sCountry);

        if (is_array($_GET['Country']) && 1 == count($_GET['Country']) && !$_GET['Country'][0]) {
            unset($_GET['Country']);
            unset($sCountry);
        }

        if ($sCountry || $sKeyword) {
            $_GET['submit_form'] = 1;
        }
        
        ml_pages_import ('FormSearch');
        $oForm = new MlPagesFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {

            ml_pages_import ('SearchResult');
            $o = new MlPagesSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Country'));

            if ($o->isError) {
                $this->_oTemplate->displayPageNotFound ();
                return;
            }

            if ($s = $o->processing()) {
                echo $s;
            } else {
                $this->_oTemplate->displayNoData ();
                return;
            }

            $this->isAllowedSearch(true); // perform search action 

            $this->_oTemplate->addCss ('unit.css');
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode($o->aCurrent['title'], false, false);
            return;

        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_ml_pages_caption_search'));
    }
    function actionDeletePhotoField ($iDumbId, $iEntryId, $sUri, $iAuthorId, $sFieldName, $iPhotoId = 0) {
    		if ($iAuthorId == $this->_iProfileId || isAdmin())
    		{
    			$aFieldInfo = db_arr("SELECT `FieldPhotos`, `{$sFieldName}` FROM `ml_pages_main` WHERE `id` = {$iEntryId} LIMIT 1");
    			foreach ($aFieldInfo as $sKey => $sValue)
    				$aFieldData[$sKey] = explode(getParam('ml_pages_multi_divider'), $sValue);
    		}
    		unset($aFieldData['FieldPhotos'][$iDumbId]);
    		unset($aFieldData[$sFieldName][$iDumbId]);
    		$sFieldPhotos = implode(getParam('ml_pages_multi_divider'), $aFieldData['FieldPhotos']);
    		$$sFieldName = $aFieldData[$sFieldName] ? implode(getParam('ml_pages_multi_divider'), $aFieldData[$sFieldName]) : '';
    		$sFieldNameData = addslashes($$sFieldName);
    		db_res("UPDATE `ml_pages_main` SET `{$sFieldName}` = '{$$sFieldName}', `FieldPhotos` = '{$sFieldPhotos}' WHERE `id` = {$iEntryId} LIMIT 1");
    		header("location:".BX_DOL_URL_ROOT."m/pages/view/{$sUri}");
    		exit;
    } 
    
    function actionAdd () {
        parent::_actionAdd (_t('_ml_pages_caption_add'));
    }

    function actionEdit ($iEntryId) {
				 parent::_actionEdit ($iEntryId, _t('_ml_pages_caption_edit'));
    }

    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_ml_pages_page_was_deleted'));
    }

    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_ml_pages_msg_added_to_featured'), _t('_ml_pages_msg_removed_from_featured'));       
    }

    function actionJoin ($iEntryId, $iProfileId) {
        parent::_actionJoin ($iEntryId, $iProfileId, _t('_ml_pages_page_joined_already'), _t('_ml_pages_page_joined_already_pending'), _t('_ml_pages_page_join_success'), _t('_ml_pages_page_join_success_pending'), _t('_ml_pages_page_leave_success'));
    }    

    function actionFans ($iPageId) {
        $iPageId = (int)$iPageId;
        if (!($aPage = $this->_oDb->getEntryByIdAndOwner ($iPageId, 0, true))) {
            echo MsgBox(_t('_Empty'));
            return;
        }

        ml_pages_import ('PageView');
        $oPage = new MlPagesPageView ($this, $aPage);
        $a = $oPage->getBlockCode_Fans();
        echo $a[0];
        exit;
    }

    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_ml_pages_caption_share_page'));
    }

    function actionManageFansPopup ($iEntryId) {
        parent::_actionManageFansPopup ($iEntryId, _t('_ml_pages_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', ML_PAGES_MAX_FANS);
    }

    function actionTags() {
        parent::_actionTags (_t('_ml_pages_tags'));
    }

    function actionCategories() {
        parent::_actionCategories (_t('_ml_pages_categories'));
    }    

    function actionDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getEntryByIdAndOwner((int)$iEntryId, 0, true))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!$this->isAllowedView ($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

    // ================================== external actions

    /**
     * Homepage block with different pages
     * @return html to display on homepage in a block
     */ 
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';

        bx_import ('PageMain', $this->_aModule);
        $o = new MlPagesPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . '?';

        //$sBrowseMode = $_GET['ml_pages_filter'] ? $_GET['ml_pages_filter'] : 'all';
        $sBrowseMode = $_GET['ml_pages_filter'];
				$aMainCategories = $this->_oDb->getMainCategories();
				foreach ($aMainCategories as $oCategories)
				{
					$sId = $oCategories['id'];
					$sName = $oCategories['Name'];
			 
					$aCategories[_t($sName)] = array(
						'href' => BX_DOL_URL_ROOT . "?ml_pages_filter={$sId}",
						'active' => $sId == $sBrowseMode,
						'dynamic' => true,
					);
				}
        return $o->ajaxBrowse(
            $sBrowseMode,
            $this->_oDb->getParam('ml_pages_perpage_homepage'), 
						$aCategories
        );
    }

    /**
     * Profile block with user's pages
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new MlPagesPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('ml_pages_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false
        );
    }

    /**
     * Profile block with pages user joined
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlockJoined ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new MlPagesPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
        return $o->ajaxBrowse(
            'joined', 
            $this->_oDb->getParam('ml_pages_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false
        );
    }

    /**
     * Page's forum permissions
     * @param $iMemberId profile id
     * @param $iForumId forum id
     * @return array with permissions
     */ 
    function serviceGetForumPermission($iMemberId, $iForumId) {

        $iMemberId = (int)$iMemberId;
        $iForumId = (int)$iForumId;

        $aFalse = array ( // default permissions, for visitors for example
            'admin' => 0,
            'read' => 1,
            'post' => 0,
        );

        if (!($aForum = $this->_oDb->getForumById ($iForumId))) {            
            return $aFalse;
        }

        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner ($aForum['entry_id'], 0, true))) {
            return $aFalse;
        }

        $aTrue = array (
            'admin' => $aDataEntry['author_id'] == $iMemberId || $this->isAdmin() ? 1 : 0, // author is admin
            'read' => 1,
            'post' => $this->isAllowedPostInForum ($aDataEntry, $iMemberId) ? 1 : 0,
        );
        return $aTrue;
    }

    /**
     * Member menu item for pages
     * @return html to show in member menu
     */ 
    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_ml_pages'), _t('_ml_pages'), 'pages.png');
    }

    function serviceGetWallPost ($aPage) {
        return parent::_serviceGetWallPost ($aPage, _t('_ml_pages_wall_object'), _t('_ml_pages_wall_added_new'));
    }

    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_ml_pages_spy_post',
            'change' => '_ml_pages_spy_post_change',
            'join' => '_ml_pages_spy_join',
            'rate' => '_ml_pages_spy_rate',
            'commentPost' => '_ml_pages_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_ml_pages_sbs_change'),
            'commentPost' => _t('_ml_pages_sbs_comment'),
            'rate' => _t('_ml_pages_sbs_rate'),
            'join' => _t('_ml_pages_sbs_join'),
        );

        return parent::_serviceGetSubscriptionParams ($sAction, $iEntryId, $a);
    }

    // ================================== admin actions

    function actionGatherLangKeys () {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
        
        $a = array ();
        $sDir = BX_DIRECTORY_PATH_MODULES . $GLOBALS['aModule']['path'] . 'classes/';        
        if ($h = opendir($sDir)) {
            while (false !== ($f = readdir($h))) {
                if ($f == "." || $f == ".." || substr($f, -4) != '.php') 
                    continue;
                $s = file_get_contents ($sDir . $f);
                if (preg_match_all("/_t[\s]*\([\s]*['\"]{1}(.*?)['\"]{1}[\s]*\)/", $s, $m))
                    foreach ($m[1] as $sKey) 
                        $a[] = $sKey;
            }
            closedir($h);
        }

        echo '<pre>';
        echo "\$aLangContent = array(\n";
        asort ($a);
        foreach ($a as $sKey)
            if (preg_match('/^_ml_pages/', $sKey))
                echo "\t'$sKey' => '" . (_t($sKey) == $sKey ? '' : _t($sKey)) . "',\n";
        echo ');';
        echo '</pre>';
        exit;
    }



		function genListOptions( $aLists, $sActive ) {
			$sRet = '';
			foreach( $aLists as $sKey => $sValue ) {
				$sRet .= '
					<option value="' .
					htmlspecialchars( $sKey ) .
					'"' . ( ( $sKey == $sActive ) ? ' selected="selected"' : '' ) .
					'>' . htmlspecialchars( $sValue ) . '</option>';
			}
			
			return $sRet;
		}

		
		function genListRows($sList) {

			$sDeleteIcon = $GLOBALS['oAdmTemplate']->getImageUrl('minus1.gif');
			$sUpIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_up.gif');
			$sDownIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_down.gif');
			
			$aRows = $this->_oDb ->getPreValues($sList);
			?>
				<tr class="headers">
			<?
			foreach( $this->aFields as $sField => $sHelp ) {
				?>
					<th>
						<span class="tableLabel"
						  onmouseover="showFloatDesc( '<?= addslashes( htmlspecialchars( $sHelp ) ) ?>' );"
						  onmousemove="moveFloatDesc( event );"
						  onmouseout="hideFloatDesc();">
							<?= $sField ?>
						</span>
					</th>
				<?
			}
			?>
					<th>&nbsp;</th>
				</tr>
			<?
			
			$iCounter = 0;
			
			foreach ($aRows as $aRow) {
				?>
				<tr>
				<?
				foreach( $this->aFields as $sField => $sHelp ) {
					if ($sField == 'Enabled'){
						$sChecked = $aRow[$sField] == 'on' ? 'checked' : '';
					?>
					<td><input type="checkbox" class="value_input" name="PreList[<?= $iCounter ?>][<?= $sField ?>]" <?echo $sChecked ?> /></td>
				<?}else{?>
					<td><input type="text" class="value_input" name="PreList[<?= $iCounter ?>][<?= $sField ?>]" value="<?= htmlspecialchars( $aRow[$sField] ) ?>" /></td>
				<?}
				}
				?>
					<th><img src="<?=$sDeleteIcon?>"     class="row_control" title="Delete"    alt="Delete" onclick="delRow( this );" /></th>
				</tr>
				<?
				
				$iCounter ++;
			}
			?>
				<tr class="headers">
					<td colspan="<?= count( $this->aFields ) ?>">&nbsp;</td>
					<th>
		                <img src="<?= $GLOBALS['oAdmTemplate']->getImageUrl('plus1.gif') ?>" class="row_control" title="Add" alt="Add" onclick="addRow( this );" />
					</th>
				</tr>
			<?
			
			return $iCounter;
		}
		function saveList( $sList, $aData ) {

			$sList_db = trim( process_db_input( $sList ) );
			if( $sList_db == '' )
				return false;
			
			$sQuery = "DELETE FROM `ml_pages_pre_values` WHERE `Key` = '$sList_db'";
			
			db_res( $sQuery );
			//$sList_db = uriGenerate($sList_db, 'ml_pages_pre_values', '`Key`', 10);
			
			$sValuesAlter = '';
			
			foreach( $aData as $iInd => $aRow ) {
				$aRow['Value'] = str_replace( ',', '', trim( $aRow['Value'] ) );
				
				if( $aRow['Value'] == '' )
					continue;
				
				$sValuesAlter .= "'" . process_db_input( $aRow['Value'] ) . "', ";
				
				$sInsFields = '';
				$sInsValues = '';
				foreach( $this->aFields as $sField => $sTemp ) {
					$sValue = trim( process_db_input( $aRow[$sField] ) );
					$sValue = $sField == 'Value' ? uriGenerate($sValue, 'ml_pages_pre_values', '`Value`', 10) : $sValue;
					$sInsFields .= "`$sField`, ";
					$sInsValues .= "'$sValue', ";
				}
				$sInsFields = substr( $sInsFields, 0, -2 ); //remove ', '
				$sInsValues = substr( $sInsValues, 0, -2 );
				$sQuery = "INSERT INTO `ml_pages_pre_values` ( `Key`, $sInsFields, `Order` ) VALUES ( '$sList_db', $sInsValues, $iInd )";
				
				db_res( $sQuery );
			}
			
		
		    return true;
		}
    function _actionAdminUploadCatIcon () {
		if ( 0 < $_FILES['iconfile']['size'] && 0 < strlen( $_FILES['iconfile']['name'] ) ) {
			$sFileName = time();
			$sExt = moveUploadedImage( $_FILES, 'iconfile', PARENT_CAT_PATH . $sFileName, '', false );
			if( strlen( $sExt ) && !(int)$sExt ) {
			 
				$sBgOrigC = PARENT_CAT_PATH . 'ml_pages_' . $sFileName.$sExt;
				if (file_exists($sBgOrigC) && !is_dir($sBgOrigC)) {
					@unlink( $sBgOrigC );
				}

				$sFileNameExt = $sFileName.$sExt;
				imageResize( PARENT_CAT_PATH . $sFileName.$sExt, PARENT_CAT_PATH . 'ml_pages_' . $sFileName.$sExt, getParam("ml_pages_cat_icowidth"), getParam("ml_pages_cat_icoheigth"));
				chmod( PARENT_CAT_PATH . 'ml_pages_' . $sFileName.$sExt, 0644 );
				@unlink( PARENT_CAT_PATH . $sFileName . $sExt );
				if ($sExt != '')
					$sIcon = 'ml_pages_' . $sFileName.$sExt;
			}
			return $sIcon; 
		} 
	}

	function _actionAdminRemoveCatIcon($iParent) {
		$oDb = new BxDolDb();
	
		$sIcon = $oDb->getOne("SELECT `Icon` FROM `ml_pages_categories` WHERE 
			`ID` = $iParent");
 		
		$sBgOrigC = PARENT_CAT_PATH . $sIcon; 
		if (file_exists($sBgOrigC)  && !is_dir($sBgOrigC)) {
			unlink( $sBgOrigC );
		} 
	}		
    function actionAdministrationSubCategories () {
		$oDb = new BxDolDb();
		
		$iParent = isset($_REQUEST['parent']) ? $_REQUEST['parent'] : '0';
		$iChild = isset($_REQUEST['child']) ? $_REQUEST['child'] : '0';

		// check actions
		if(is_array($_POST))
		{
			if(isset($_POST['action_save']) && !empty($_POST['action_save']))
			{  
				$sName = $_POST['parentname'];
				$sCName = $_POST['captionname'];
				
				$iParentId = $_POST['parent'];
				if ($sName && $sCName)
				{
					$oDb->query("INSERT INTO `ml_pages_categories` SET `Name`='$sName', `Caption`='$sCName', `Parent`='$iParentId'");
					$iNewCatId = mysql_insert_id();
					if ($iNewCatId)
					{
						$oDb->query( "UPDATE ml_pages_fields SET `Categories` = CONCAT_WS(',',`Categories`,'{$iNewCatId}') WHERE `ID` IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14)");
						$oDb->cleanCache('ml_pages_fields');
					}
				}
			} 
			if(isset($_POST['action_edit']) && !empty($_POST['action_edit']))
			{  
 				$sName = $_POST['parentname']; 
 				$sCName = $_POST['captionname'];
		 		$iChildId = $_POST['child']; 
		 		if ($sName && $sCName)
				$oDb->query("UPDATE `ml_pages_categories` SET `Name`='$sName', `Caption`='$sCName' WHERE 
					`ID` = $iChildId");
			} 
			if(isset($_POST['action_delete']) && !empty($_POST['action_delete']))
			{  
 					$iChildId = $_POST['child']; 
 					if (!$oDb->getOne("SELECT 1 FROM `ml_pages_main` WHERE `category` = {$iChildId} LIMIT 1"))
 						$oDb->query("DELETE FROM `ml_pages_categories` WHERE 
							`ID` = $iChild");
					else
						$sMessage .= MsgBox(_t('_ml_pages_cant_delete_category'));
						
			} 

		}
 
		$aParentCategories = $this->_oDb->getCategories();
		$sContent .= "<div class=\"top_settings_block\">
	    <div class=\"ordered_block\">
	        "._t('_ml_pages_administration_main_categories')."&nbsp;<select name=\"select_type\" onchange=\"location.href='".BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sub_categories/?action=all&parent=' . "' + this.value;\">";
	           $sContent .= "<option value=\"0\" {$sSelected}>"._t('_ml_pages_select')."</option>";
	           foreach ($aParentCategories as $sKey => $aValue)
	           {
	           		$sKey = $aValue['ID'];
								$sValue = $aValue['Name'];
	           		$sSelected = $sKey == $iParent ? 'selected="selected"' : '';
	            	$sContent .= "<option value=\"{$sKey}\" {$sSelected}>".$sValue."</option>";
	        	 }
	        $sContent .= "</select>
	    </div>
	</div>";
		if ($iParent)
		{
			$aChildCategories = $this->_oDb->getCategories($iParent);
			$sContent .= "<div class=\"top_settings_block\">
		    <div class=\"ordered_block\">
		        "._t('_ml_pages_administration_sub_categories')."&nbsp;<select name=\"select_type\" onchange=\"location.href='".BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sub_categories/?action=all&parent=' . $iParent . '&child=' . "' + this.value;\">";
		           $sContent .= "<option value=\"0\" {$sSelected}>"._t('_ml_pages_select_add')."</option>";
		           foreach ($aChildCategories as $sKey => $aValue)
		           {
		           		$sKey = $aValue['ID'];
									$sValue = $aValue['Name'];
		           		$sSelected = $sKey == $iChild ? 'selected="selected"' : '';
		            	$sContent .= "<option value=\"{$sKey}\" {$sSelected}>".$sValue."</option>";
		        	 }
		        $sContent .= "</select>
		    </div>
		</div>";			
			
			$aCategory = $oDb->getRow("SELECT * FROM `ml_pages_categories` WHERE  `ID` = '$iChild'");
		}		
		
		 
		$sFormName = 'categories_form';
  
	    if($iChild){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_ml_pages_categ_btn_update'),
				'action_delete' => _t('_ml_pages_categ_btn_delete'), 
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_ml_pages_categ_btn_save')
			), 'pathes', false);	 
	    }

		$aVars = array(
			'name' => $aCategory['Name'],
			'caption_name' => $aCategory['Caption'],
			'parent_id'=> $iParent, 
			'child_id'=> $aCategory['ID'],   
			'form_name' => $sFormName, 
			'controls' => $iParent ? $sControls  : ''
		);

		$sContent = $sContent . $sMessage;
		$sContent .= $this->_oTemplate->parseHtmlByName('child_categories',$aVars);
	  
		return $sContent;
	}
 
    function actionAdministrationMainCategories () {
		$oDb = new BxDolDb();
		
		$iParent = isset($_REQUEST['parent']) ? $_REQUEST['parent'] : '0';
 
		// check actions
		if(is_array($_POST))
		{
			if(isset($_POST['action_save']) && !empty($_POST['action_save']))
			{  
				$sName = $_POST['parentname'];
				$sCName = $_POST['captionname'];
				if ($sName && $sCName)
				{
					$sIcon = $this->_actionAdminUploadCatIcon();
					$oDb->query("INSERT INTO `ml_pages_categories` SET `Name`='$sName', `Caption`='$sCName', `Icon`='$sIcon'");
				}
			} 
			if(isset($_POST['action_edit']) && !empty($_POST['action_edit']))
			{  
 				$sName = $_POST['parentname']; 
				$sCName = $_POST['captionname'];
				if ($sName && $sCName)
				{
					$sIcon = $this->_actionAdminUploadCatIcon();
					$sExtra = ($sIcon) ? ",`Icon`='$sIcon'" : "";
					 
					if($sIcon)
						$this->_actionAdminRemoveCatIcon($iParent);
			 		
					$oDb->query("UPDATE `ml_pages_categories` SET `Name`='$sName', `Caption`='$sCName' {$sExtra} WHERE 
						`ID` = $iParent");
					}
			} 
			if(isset($_POST['action_delete']) && !empty($_POST['action_delete']))
			{  
 					$this->_actionAdminRemoveCatIcon($iParent);
					$sQuery = db_res("SELECT `ID` FROM `ml_pages_categories` WHERE `Parent` = {$iParent}");
					while($aRow = mysql_fetch_array($sQuery))
					{
	 					if (!$oDb->getOne("SELECT 1 FROM `ml_pages_main` WHERE `category` = {$aRow['ID']} LIMIT 1"))
	 						$oDb->query("DELETE FROM `ml_pages_categories` WHERE 
							`ID` = $iParent OR `Parent` = $iParent");
						else
							$sMessage .= MsgBox(_t('_ml_pages_cant_delete_category')); 
					}
			} 
			if(isset($_POST['action_add']) && !empty($_POST['action_add']))
			{  
				$iParent = 0;  
			}  
		}
 
		$aParentCategories = $this->_oDb->getCategories();
		$sContent .= "<div class=\"top_settings_block\">
	    <div class=\"ordered_block\">
	        "._t('_ml_pages_administration_main_categories')."&nbsp;<select name=\"select_type\" onchange=\"location.href='".BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/main_categories/?action=all&parent=' . "' + this.value;\">";
	           $sContent .= "<option value=\"0\" {$sSelected}>"._t('_ml_pages_select_add')."</option>";
	           foreach ($aParentCategories as $sKey => $aValue)
	           {
	           		$sKey = $aValue['ID'];
								$sValue = $aValue['Name'];
	           		$sSelected = $sKey == $iParent ? 'selected="selected"' : '';
	            	$sContent .= "<option value=\"{$sKey}\" {$sSelected}>".$sValue."</option>";
	        	 }
	        $sContent .= "</select>
	    </div>
	</div>";
		
		$aCategory = $oDb->getRow("SELECT * FROM `ml_pages_categories` WHERE  `ID` = '$iParent'");
		 
		$sFormName = 'categories_form';
  
	    if($iParent){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_ml_pages_categ_btn_update'),
				'action_delete' => _t('_ml_pages_categ_btn_delete'), 
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_ml_pages_categ_btn_save')
			), 'pathes', false);	 
	    }

		$aVars = array(
			'display' => 'block',
			'name' => $aCategory['Name'],
			'caption_name' => $aCategory['Caption'],
			'id'=> $aCategory['ID'],  
			'icon_url'=> $aCategory['Icon'] ? PARENT_CAT_URL.$aCategory['Icon'] : getTemplateIcon('no-photo.png'), 
			'form_name' => $sFormName, 
			'controls' => $sControls
		);

		$sContent = $sContent . $sMessage;
		$sContent .= $this->_oTemplate->parseHtmlByName('parent_categories',$aVars);
	  
		return $sContent;
	}
 
 
		function actionAdministrationCategories() {
		
			$sDeleteIcon = $GLOBALS['oAdmTemplate']->getImageUrl('minus1.gif');
			$sUpIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_up.gif');
			$sDownIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_down.gif');
		
		    $sPopupAdd = $iAmInPopup ? '&popup=1' : '';
		    $sResultMsg = '';


		
			//get lists
			$aLists = array( '' => '- Select -' );
			$aKeys = $this->_oDb->getPreKeys();
			foreach ($aKeys as $aList)
				$aLists[ $aList['Key'] ] = $aList['Key'];
			
			$sListIn = bx_get('list');
			if ($sListIn !== false) {
				$sList_db = process_db_input($sListIn);
				$sList    = process_pass_data($sListIn);
				
				$iCount = $this->_oDb->getPreValuesCount($sListIn);
				if (!$iCount) //if no rows returned...
					$aLists[ $sList ] = $sList; //create new list
			} else {
				$sList = '';
			}

		    ob_start();
		
		    if ($sResultMsg)
		        echo MsgBox($sResultMsg);
			?>	
			<script type="text/javascript">
				function createNewList() {
					var sNewList = prompt( 'Please enter name of new list' );
					
					if( sNewList == null )
						return false;
					
					sNewList = $.trim( sNewList );
					
					if( !sNewList.length ) {
						alert( 'You should enter correct name' );
						return false;
					}
					window.location = '<?=BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories'; ?>&list=' + encodeURIComponent( sNewList ) + '<?= $sPopupAdd ?>';
				}
				
				function addRow( eImg ) {
		
					$( eImg ).parent().parent().before(
						'<tr>' +
						<?
						foreach( $this->aFields as $sField => $sHelp ) {
							?>
							'<td><input type="text" class="value_input" name="PreList[' + iNextInd + '][<?= $sField ?>]" value="" /></td>' +
							<?
						}
						?>
							'<th>' +
								'<img src="<?= $sDeleteIcon ?>"     class="row_control" title="Delete"    alt="Delete" onclick="delRow( this );" />' +
								'<img src="<?= $sUpIcon ?>"   class="row_control" title="Move up"   alt="Move up" onclick="moveUpRow( this );" />' +
								'<img src="<?= $sDownIcon ?>" class="row_control" title="Move down" alt="Move down" onclick="moveDownRow( this );" />' +
							'</th>' +
						'</tr>'
					);
					
					iNextInd ++;
					
					sortZebra();
				}
				
				function delRow( eImg ) {
					$( eImg ).parent().parent().remove();
					sortZebra();
				}
				
				function moveUpRow( eImg ) {
					var oCur = $( eImg ).parent().parent();
					var oPrev = oCur.prev( ':not(.headers)' );
					if( !oPrev.length )
						return;
					
					// swap elements values
					var oCurElems  = $('input', oCur.get(0));
					var oPrevElems = $('input', oPrev.get(0));
					
					oCurElems.each( function(iInd) {
						var oCurElem  = $( this );
						var oPrevElem = oPrevElems.filter( ':eq(' + iInd + ')' );
						
						// swap them
						var sCurValue = oCurElem.val();
						oCurElem.val( oPrevElem.val() );
						oPrevElem.val( sCurValue );
					} );
				}
				
				function moveDownRow( eImg ) {
					var oCur = $( eImg ).parent().parent();
					var oPrev = oCur.next( ':not(.headers)' );
					if( !oPrev.length )
						return;
					
					// swap elements values
					var oCurElems  = $('input', oCur.get(0));
					var oPrevElems = $('input', oPrev.get(0));
					
					oCurElems.each( function(iInd) {
						var oCurElem  = $( this );
						var oPrevElem = oPrevElems.filter( ':eq(' + iInd + ')' );
						
						// swap them
						var sCurValue = oCurElem.val();
						oCurElem.val( oPrevElem.val() );
						oPrevElem.val( sCurValue );
					} );
				}
				
				function sortZebra() {
					$( '#listEdit tr:even' ).removeClass( 'even odd' ).addClass( 'even' );
					$( '#listEdit tr:odd'  ).removeClass( 'even odd' ).addClass( 'odd'  );
				}
				
				//just a design
				$( document ).ready( sortZebra );
			</script>
			
			<form action="<?=BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories'; ?>" method="post">
				<table id="listEdit" cellpadding="0" cellspacing="0">
					<tr>
						<th colspan="<?= count( $this->aFields ) + 1 ?>">
							Select a list:
							<select name="list"
							  onchange="if( this.value != '' ) window.location = '<?=BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories'; ?>' + '&list=' + encodeURIComponent( this.value ) + '<?= $sPopupAdd ?>';">
								<?= $this->genListOptions( $aLists, $sList ) ?>
							</select>
							<input type="button" value="Create New" onclick="createNewList();" />
						</th>
					</tr>
			<?
			if( $sList !== '' ) {
				$iNextInd = $this->genListRows( $sList );
				?>
					<tr>
						<th colspan="8">
							<input type="hidden" name="popup" value="<?= $iAmInPopup ?>" />
							<input type="submit" name="action" value="Save" />
						</th>
					</tr>
				<?
			} else
				$iNextInd = 0;
			?>
				</table>
				
				<script type="text/javascript">
					iNextInd = <?= $iNextInd ?>;
				</script>
			</form>
			<?
			return ob_get_clean();
		}
    function actionAdministration ($sUrl = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();

        $aMenu = array(
            'home' => array(
                'title' => _t('_ml_pages_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/home', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_ml_pages_administration_admin_pages'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),            
            'create' => array(
                'title' => _t('_ml_pages_administration_create_page'), 
                'href' => BX_DOL_URL_ROOT . 'modules/modloaded/pages/create.php', 
                'target' => '_blank',
                //'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_ml_pages_administration_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
            'main_categories' => array(
                'title' => _t('_ml_pages_administration_main_categories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/main_categories',
                '_func' => array ('name' => 'actionAdministrationMainCategories', 'params' => array()),
            ),
            'sub_categories' => array(
                'title' => _t('_ml_pages_administration_sub_categories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sub_categories',
                '_func' => array ('name' => 'actionAdministrationSubCategories', 'params' => array()),
            ),
            'fields' => array(
                'title' => _t('_ml_pages_administration_fields'), 
                'href' => BX_DOL_URL_ROOT . 'modules/modloaded/pages/pages.php',
                '_func' => array ('name' => 'actionAdministrationFields', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'home';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);
				
				/*switch(bx_get('action')) {
					case 'getArea':
						$this->genAreaJSON((int)bx_get('id'));
						break;
					case 'createNewBlock':
						$this->createNewBlock();
						break;
					case 'createNewItem':
						$this->createNewItem();
						break;
					case 'savePositions':
						$this->savePositions((int)bx_get('id'));
						break;
					case 'loadEditForm':
						$this->showEditForm((int)bx_get('id'), (int)bx_get('area'));
						break;
					case 'dummy':
						echo 'Dummy!';
						break;
					case 'Save'://save item
						$this->saveItem((int)bx_get('area'), $_POST);
						break;
					case 'Delete'://delete item
						$this->deleteItem((int)bx_get('id'), (int)bx_get('area'));
						break;
				}*/


        
        echo $this->_oTemplate->adminBlock ($sContent, '', $aMenu);
				$GLOBALS['oAdmTemplate']->addJsTranslation(array(
					'_adm_mbuilder_active_items',
					'_adm_txt_pb_inactive_blocks',
					'_adm_mbuilder_inactive_items'
				));
        $this->_oTemplate->addJsAdmin ('ui.core.js');
        $this->_oTemplate->addJsAdmin ('ui.tabs.js');
        $this->_oTemplate->addJsAdmin ('ui.sortable.js');
        $this->_oTemplate->addJsAdmin ('fields.js');
        $this->_oTemplate->addCssAdmin ('fields.css');
        $this->_oTemplate->addCssAdmin ('predefined_values.css');
        $this->_oTemplate->addCssAdmin ('admin.css');
        $this->_oTemplate->addCssAdmin ('unit.css');
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_extra.css'); 
        $this->_oTemplate->addCssAdmin ('forms_adv.css');        
        $this->_oTemplate->pageCodeAdmin (_t('_ml_pages_administration'));
    }
		function _getCreatePageCategoriesForm($iPageId = 0, $sMainCategory = '', $sSubCategory = '')
		{
			$sMainCategory = $_POST['main_category'] ? $_POST['main_category'] : $sMainCategory;
	    $aMainCategory = $this->_oDb->getMainCategory();
	    foreach ($aMainCategory as $sKey => $aRow)
	    {
	    	$aMainCategoryKeys[$sKey]['main_option_value'] = $aRow['ID'];
	    	$aMainCategoryKeys[$sKey]['main_option_name'] = _t($aRow['Caption']);
	    	$aMainCategoryKeys[$sKey]['main_option_icon'] = $aRow['Icon'] ? PARENT_CAT_URL . $aRow['Icon'] : getTemplateIcon('no-photo.png');
	    	$aSubCategory = $this->_oDb->getSubCategory($aRow['ID']);
		    $sSubCategories = '';
		    $sSubCategories .= '<div class="input_wrapper input_wrapper_select" >';
		    $sSubCategories .= '<select onchange="$(\'#sub_category\').val($(\'#sub_category' . $aRow['ID'] . '\').val());return false;" class="form_input_select" name="sub_category' . $aRow['ID'] . '" id="sub_category' . $aRow['ID'] . '">';
		    $sSubCategories .= '<option value="">' . _t('_ml_pages_select') . '</option>';
		    foreach ($aSubCategory as $sSKey => $aSRow)
					$sSubCategories .= '<option value="' . $aSRow['ID'] . '">' . _t($aSRow['Caption']) . '</option>';
				
				$sSubCategories .= '</select><div class="input_close input_close_select"></div></div>';
		    $aMainCategoryKeys[$sKey]['categ_sub_name_options'] = $sSubCategories;
				$aMainCategoryKeys[$sKey]['button_select'] = _t('_ml_pages_button_select');
	    }
	    

	    $aTemplateKeys = array(
	    	'display_categ' => ($_POST['sub_category'] && $_POST['main_category'] ? 'none' : 'block'),
	    	'page_id' => $iPageId,
	    	'main_select' =>  _t('_ml_pages_select'),
	    	'categories' =>  _t('_ml_pages_categories'),
	    	'select_categories' =>  _t('_ml_pages_categories'),
	    	'sub_category' => $_POST['sub_category'],
	    	'main_category' => $_POST['main_category'],
	    	'bx_repeat:main_category' => $aMainCategoryKeys,
	    );
	    return $aTemplateKeys;
		}  
		
		function _getCategoriesForm($iPageId = 0, $sMainCategory = '', $sSubCategory = '')
		{
			$sMainCategory = $_POST['main_category'] ? $_POST['main_category'] : $sMainCategory;
	    $aMainCategory = $this->_oDb->getMainCategory();
	    foreach ($aMainCategory as $sKey => $aRow)
	    {
	    	$aMainCategoryKeys[$sKey]['main_option_value'] = $aRow['ID'];
	    	$aMainCategoryKeys[$sKey]['main_option_name'] = _t($aRow['Caption']);
	    	$aMainCategoryKeys[$sKey]['selected'] = $sMainCategory == $aRow['ID'] ? 'selected' : '';
	    }
	    if ($sMainCategory)
	    {
		    $aSubCategory = $this->_oDb->getSubCategory($sMainCategory);
		    $sSubCategory = $_POST['sub_category'] ? $_POST['sub_category'] : $sSubCategory;
		    foreach ($aSubCategory as $sKey => $aRow)
		    {
		    	$aSubCategoryKeys[$sKey]['sub_option_value'] = $aRow['ID'];
		    	$aSubCategoryKeys[$sKey]['sub_option_name'] = _t($aRow['Caption']);
		    	$aSubCategoryKeys[$sKey]['selected'] = $sSubCategory == $aRow['ID'] ? 'selected' : '';
		    }
	    }
	    $aTemplateKeys = array(
	    	'page_id' => $iPageId,
	    	'main_select' =>  _t('_ml_pages_select'),
	    	'sub_select' =>  _t('_ml_pages_select'),
	    	'categories' =>  _t('_ml_pages_categories'),
	    	'select_categories' =>  _t('_ml_pages_categories'),
	    	'sub_category' => $_POST['sub_category'],
	    	'main_category' => $_POST['main_category'],
	    	'bx_repeat:main_category' => $aMainCategoryKeys,
	    	'bx_repeat:sub_category' => $aSubCategoryKeys,
	    );
	    return $aTemplateKeys;
		}    
		
    function actionAdministrationFields () {


			    $aTemplateKeys = $this->_getCategoriesForm();
					return DesignBoxAdmin(
			    _t('_ml_page_creator_title'),
			    $this->_oTemplate->parseHtmlByName('pages', $aTemplateKeys),
			    array(
			        'adm-fb-ctl-m1' => array(
			            'title' => _t('_ml_pagess_page'),            
			            'href' => 'javascript:void(0)',
			            'onclick' => 'javascript:changeType(this)',
			            'active' => 1
			        ),
			        'adm-fb-ctl-edit-tab' => array(
			            'title' => _t('_ml_pages_edit_page'),
			            'href' => 'javascript:void(0)',
			            'onclick' => 'javascript:changeType(this)',
			            'active' => 0
			        ),
			        'adm-fb-ctl-view-tab' => array(
			            'title' => _t('_ml_pages_view_page'),
			            'href' => 'javascript:void(0)',
			            'onclick' => 'javascript:changeType(this)',
			            'active' => 0
			        ),

			    )
			);

			
    }
    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Pages');
    }

    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_ml_pages_admin_delete', '_ml_pages_admin_activate');
    }

    // ================================== pages

    function onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'ml_pages_join_request', ML_PAGES_MAX_FANS);
    }

    function onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'ml_pages_join_reject');
    }

    function onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'ml_pages_fan_remove');
    }

    function onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'ml_pages_fan_become_admin');
    }

    function onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry, 'ml_pages_admin_become_fan');
    }

    function onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'ml_pages_join_confirm');
    }

    // ================================== permissions
    
    function isAllowedView ($aPage, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aPage['author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, ML_PAGES_VIEW, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
        // check user group 
	    return $this->_oPrivacy->check('view_page', $aPage['id'], $this->_iProfileId); 
    }

    function isAllowedBrowse ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, ML_PAGES_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, ML_PAGES_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, ML_PAGES_ADD, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aPage, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aPage['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, ML_PAGES_EDIT_ANY_PAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedMarkAsFeatured ($aPage, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, ML_PAGES_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedBroadcast ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, ML_PAGES_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedDelete (&$aPage, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aPage['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, ML_PAGES_DELETE_ANY_PAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     

    function isAllowedJoin (&$aDataEntry) {        
        if (!$this->_iProfileId) 
            return false;

        $isAllowed = $this->_oPrivacy->check('join', $aDataEntry['id'], $this->_iProfileId);     
        return $isAllowed && $this->_isAllowedJoinByMembership ($aDataEntry);
    }

    function _isAllowedJoinByMembership (&$aPage) {        
        if (!$aPage['PageMembershipFilter']) return true;
        require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
        $aMemebrshipInfo = getMemberMembershipInfo($this->_iProfileId);
        return $aPage['PageMembershipFilter'] == $aMemebrshipInfo['ID'] ? true : false;
    }

    function isAllowedSendInvitation (&$aPage) {
        return ($aPage['author_id'] == $this->_iProfileId && ($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && isProfileActive($this->_iProfileId));
    }

    function isAllowedSharePage (&$aPage) {
        return true;
    }

    function isAllowedViewFans (&$aPage) {

        if (($aPage['author_id'] == $this->_iProfileId && $GLOBALS['logged']['member'] && isProfileActive($this->_iProfileId)) || $this->isAdmin ())
            return true;

        return $this->_oPrivacy->check('view_fans', $aPage['id'], $this->_iProfileId);     
    }

    function isAllowedComments (&$aPage) {

        if (($aPage['author_id'] == $this->_iProfileId && $GLOBALS['logged']['member'] && isProfileActive($this->_iProfileId)) || $this->isAdmin ())
            return true;

        return $this->_oPrivacy->check('comment', $aPage['id'], $this->_iProfileId);
    }

    function isAllowedUploadPhotos(&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        return $this->_oPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideos(&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false;                
        return $this->_oPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSounds(&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForSounds())
            return false;                        
        return $this->_oPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFiles(&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForFiles())
            return false;                        
        return $this->_oPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedCreatorCommentsDeleteAndEdit (&$aPage, $isPerformAction = false) {
        if ($this->isAdmin()) return true;        
        if (!$GLOBALS['logged']['member'] || $aPage['author_id'] != $this->_iProfileId)
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, ML_PAGES_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedRate (&$aPage) {

        if (($aPage['author_id'] == $this->_iProfileId && $GLOBALS['logged']['member'] && isProfileActive($this->_iProfileId)) || $this->isAdmin ())
            return true;

        return $this->_oPrivacy->check('rate', $aPage['id'], $this->_iProfileId);
    }

    function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1) {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId)) || $this->_oPrivacy->check('post_in_forum', $aDataEntry['id'], $iProfileId);
    }

    function isAllowedManageAdmins($aDataEntry) {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }

    function isAllowedManageFans($aDataEntry) {
        return $this->isEntryAdmin($aDataEntry);
    }

    function isFan($aDataEntry, $iProfileId = 0, $isConfirmed = true) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isFan ($aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isEntryAdmin($aDataEntry, $iProfileId = 0) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isGroupAdmin ($aDataEntry['id'], $iProfileId) && isProfileActive($iProfileId);
    }

    function _defineActions () {
        defineMembershipActions(array('pages view', 'pages browse', 'pages search', 'pages add', 'pages comments delete and edit', 'pages edit any page', 'pages delete any page', 'pages mark as featured', 'pages approve', 'pages broadcast message'), 'ML_');
    }

    // ================================== other function 

    function _browseMy (&$aProfile) {
        parent::_browseMy ($aProfile, _t('_ml_pages_block_my_pages'));
    }    


}

?>
