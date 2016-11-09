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

bx_import('BxDolTwigPageView');

class MlPagesPageView extends BxDolTwigPageView {	

    function MlPagesPageView(&$oPagesMain, &$aPage) {
        parent::BxDolTwigPageView('ml_pages_view', $oPagesMain, $aPage);
				$this->_iPageID = $aPage['id'];
				require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesPageFields.php');
				$this->_getPageData($this->_iPageID);
		    if( $this->_aPage ) 
		    {
		    	if( isMember() ) 
		      {
						$iMemberId = getLoggedId();
						if( $iMemberId == $this->_aPage['author_id'] ) 
						{
							$this->owner = true;
			        $iPFArea = isAdmin() ? 5 : 6;            
						}
					} 
				if (!$iPFArea) $iPFArea = 6;
				
		    $this->oPF = new MlPagesPageFields( $iPFArea );
				if( !$this->oPF->aBlocks)
					return false;
		
				$this->aPFBlocks = $this->oPF->aBlocks;
			}
	}


	function _getPageData($iID) {
		global $aUser;

		$bUseCacheSystem = ( getParam('enable_cache_system') == 'on' ) ? true : false;

		$sPageCache = BX_DIRECTORY_PATH_CACHE . 'page' . $iID . '.php';
		if( $bUseCacheSystem && file_exists( $sPageCache ) && is_file( $sPageCache ) ) {
			require_once($sPageCache);
			$this -> _aPage = $aUser[$iID];
		} else
			$this -> _aPage = $this -> _oDb->getPageDataById( $iID );
	}

	
	function getBlockCode_PFBlock($iBlockID, $sContent) {
		require_once($this->_oMain->_oConfig->_sClassPath . 'MlPagesPageFields.php');
		$this->oPF = new MlPagesPageFields(5);
		if( !$this->oPF->aBlocks)
			return false;
		$this->aPFBlocks = $this->oPF->aBlocks;
		return $this->oPF->showBlockPFBlock($iBlockID, '', $sContent, true, $this->aDataEntry['id'], $this->aPFBlocks);
  }
	
	function getBlockCode_Author() {

        return $this->_oTemplate->blockInfo ($this->aDataEntry);
    }
  function getBlockCode_Forum() {
  	global $tmpl;
  	$sLang = getCurrentLangName();
  	$iForumLimit = getParam('ml_pages_forum_limit') ? getParam('ml_pages_forum_limit') : 10;
  	$this->_oTemplate->addCss (BX_DOL_URL_ROOT . 'modules/boonex/forum/layout/'. $tmpl . '_' . $sLang. '/css/main.css');
  	$sContent = '<div class="boxContent"><table class="forum_table_list">';
		$sQuery = db_res("SELECT `ml_pages_forum_topic`.`topic_id`, `ml_pages_forum_topic`.`topic_uri`, `ml_pages_forum_topic`.`topic_title`, `ml_pages_forum_topic`.`when`,
  		`ml_pages_forum_topic`.`topic_posts`, `ml_pages_forum_topic`.`first_post_user`, `ml_pages_forum_topic`.`last_post_when`, `ml_pages_forum_topic`.`last_post_user` FROM `ml_pages_forum_topic` LEFT JOIN `ml_pages_forum` ON `ml_pages_forum`.`forum_id` = `ml_pages_forum_topic`.`forum_id`
  		WHERE  `ml_pages_forum`.`forum_uri` = '{$this->aDataEntry['uri']}' LIMIT {$iForumLimit}");
		//$aXml = simplexml_load_file(BX_DOL_URL_ROOT . "forum/pages/rss/forum/{$this->aDataEntry['uri']}.htm");
		//echo count($aXml->channel->item);
  	//foreach($aXml->channel->item as $sKey => $sValue){
  	while($aRow = mysql_fetch_array($sQuery)){
  	$sLastPost = db_value("SELECT `post_text` FROM ml_pages_forum_post WHERE `topic_id` = {$aRow['topic_id']} ORDER BY post_id DESC LIMIT 1");
  	$sContent .= '<tr><td class="forum_table_column_first forum_table_fixed_height">
  		<div class="forum_icon_title_desc">
  			<img src="' . BX_DOL_URL_ROOT . 'forum/layout/base_' . $sLang . '/img/topic.png"></img>
  				<a class="forum_topic_title" href="' . BX_DOL_URL_ROOT . 'forum/pages/topic/' . $aRow['topic_uri'] . '.htm">' . $aRow['topic_title'] . '</a>
  					<br></br><span><span class="forum_stat">created by ' . $aRow['first_post_user'] . ' ' . date('m.d.Y H:i', $aRow['when']) . ' - last reply by ' . $aRow['last_post_user'] . ' ' . date('m.d.Y H:i', $aRow['last_post_when'])  . '</span></span>
  						<span>' . $sLastPost . '</span></div></td><td class="forum_table_column_stat">' . $aRow['topic_posts'] . ' posts</td></tr>';
  	/*$sContent .= '<tr><td class="forum_table_column_first forum_table_fixed_height">
  		<div class="forum_icon_title_desc">
  			<img src="' . BX_DOL_URL_ROOT . 'forum/layout/base_' . $sLang . '/img/topic.png"></img>
  				<a class="forum_topic_title" href="' . $sValue->link . '">' . strip_tags($sValue->description) . '</a>
  					<br></br><span><span class="forum_stat">created by admin 26.09.2012 05:58 - last reply by admin 26.09.2012 06:00</span></span>
  						<span>testtesttesttesttesttest</span></div></td><td class="forum_table_column_stat">2 posts</td></tr>';*/
  	
  	}

  	$sContent .= '</table></div>';
  	return $sContent;
  }      


	function getBlockCode_Desc() {
        global $oSysTemplate;

        $aPageInfo = $this->_oDb->getPageInfo($this->_iPageID);
        if(!$aPageInfo['DescriptionMe']) {
            return;
        }

        $aTemplateKeys = array(
            'content' => $aPageInfo['DescriptionMe'],
        );

        return $oSysTemplate -> parseHtmlByName('default_padding.html', $aTemplateKeys);
       // return $this->_oTemplate->blockDesc ($this->aDataEntry);
    }
		function _blockEmbed () {
			$aVars = array (   
				'url' => BX_DOL_URL_ROOT,
				'uri' => $this->aDataEntry['uri'],
				'show_embed_code' => _t('_ml_pages_show_embed_code'),
				'be_a_fan' => _t('_ml_pages_be_a_fan'),
				'user_id' => $this->_oMain->_iProfileId,
				'page_id' => $this->aDataEntry['id'],
				'display' => 'none',
				//'display' => $this->isAllowedJoin($aPageInfo) ? 'block' : 'none',
			);
			return $this->_oTemplate->parseHtmlByName('embed', $aVars);
		}
    function getBlockCode_Embed() {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $this->aDataEntry['uri']);
    		return $this->_blockEmbed();
    		//return $this->_oMain->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id'], false, $this->aDataEntry['id']);
    } 
    function getBlockCode_Photos() {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $this->aDataEntry['uri']);
    		$aPhotos = $this->_oDb->getMediaIds($this->aDataEntry['id'], 'images');
    		if (!empty($aPhotos))
    			return $this->_oMain->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id'], false, $this->aDataEntry['id']);
    }    

    function getBlockCode_Videos() {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $this->aDataEntry['uri']);
    		$aVideos = $this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos');
    		if (!empty($aVideos))
        	return $this->_oMain->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Sounds() {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $this->aDataEntry['uri']);
    		$aSounds = $this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds');
    		if (!empty($aSounds))
        	return $this->_oMain->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Files() {
    		$GLOBALS['oTopMenu']->setCustomVar('ml_pages_view_uri', $this->aDataEntry['uri']);
    		$aFiles = $this->_oDb->getMediaIds($this->aDataEntry['id'], 'files');
    		if (!empty($aFiles))
        	return $this->_oMain->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id'], $this->aDataEntry['id']);
    }    

    function getBlockCode_Rate() {
        ml_pages_import('Voting');
        $o = new MlPagesVoting ('ml_pages', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        return $o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry));
    }        

    function getBlockCode_Comments() {    
        ml_pages_import('Cmts');
        $o = new MlPagesCmts ('ml_pages', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            
		function getBlockCode_PrimPhoto() {
				return $this->_oTemplate->blockPrimPhoto ($this->aDataEntry);
		}
		
    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'ml_pages', '', (int)$this->aDataEntry['id']);

			$isFan = $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 1);

            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'titleSubscribe' => $aSubscribeButton['title'],                
                'titleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_ml_pages_action_title_edit') : '',
                'titlePrivacy' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_ml_pages_action_title_privacy') : '',
                'titleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_ml_pages_action_title_delete') : '',
                'titleJoin' => $this->_oMain->isAllowedJoin($this->aDataEntry) ? ($isFan ? _t('_ml_pages_action_title_leave') : _t('_ml_pages_action_title_join')) : '',
                'titleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_ml_pages_action_title_invite') : '',
                'titleShare' => $this->_oMain->isAllowedSharePage($this->aDataEntry) ? _t('_ml_pages_action_title_share') : '',
                'titleBroadcast' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_ml_pages_action_title_broadcast') : '',
                'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['Featured'] ? _t('_ml_pages_action_remove_from_featured') : _t('_ml_pages_action_add_to_featured')) : '',
                'titleManageFans' => $this->_oMain->isAllowedManageFans($this->aDataEntry) ? _t('_ml_pages_action_manage_fans') : '',
                'titleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_ml_pages_action_upload_photos') : '',
                'titleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_ml_pages_action_upload_videos') : '',
                'titleEmbedVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_ml_pages_action_embed_videos') : '',
                'titleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_ml_pages_action_upload_sounds') : '',
                'titleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_ml_pages_action_upload_files') : '',
                'titleAddEvent' => $this->aDataEntry['author_id'] == $this->_oMain->_iProfileId ? _t('_ml_pages_action_add_event') : '',
                'titleAddWebsite' => $this->aDataEntry['author_id'] == $this->_oMain->_iProfileId ? _t('_ml_pages_action_add_website') : '',

            );

            if (!$this->aInfo['titleEdit'] && !$this->aInfo['titleDelete'] && !$this->aInfo['titleJoin'] && !$this->aInfo['titleInvite'] && !$this->aInfo['titleShare'] && !$this->aInfo['AddToFeatured'] && !$this->aInfo['titleBroadcast'] && !$this->aInfo['titleSubscribe'] && !$this->aInfo['titleManageFans'] && !$this->aInfo['titleUploadPhotos'] && !$this->aInfo['titleUploadVideos'] && !$this->aInfo['titleUploadSounds'] && !$this->aInfo['titleUploadFiles'])
                return '';

            return $oSubscription->getData() . $oFunctions->genObjectsActions($this->aInfo, 'ml_pages');
        } 

        return '';
    }    

    function getBlockCode_Fans() {
        return parent::_blockFans ($this->_oDb->getParam('ml_pages_perpage_fans'), 'isAllowedViewFans', 'getFans');
    }        

    function getBlockCode_FansUnconfirmed() {
        return parent::_blockFansUnconfirmed (ML_PAGES_MAX_FANS);
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
			return $this->_oTemplate->parseHtmlByName('location_block', $aVars);
		}
    /*function getBlockCode_Location() {
    		$sLocation = $this->_oDb->getPageLocation($this->_iPageID);
    		return $this->_blockLocation($sLocation, $this->_aPage['author_id']);
        return parent::_blockFansUnconfirmed (ML_PAGES_MAX_FANS);
    }*/
    
    function getCode() {

        $this->_oMain->_processFansActions ($this->aDataEntry, ML_PAGES_MAX_FANS);

        return parent::getCode();
    }    
}

?>
