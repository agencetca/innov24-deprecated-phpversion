<?
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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');

class MlPageWallResponse extends BxDolAlertsResponse {        
    var $_oModule;

	/**
	 * Constructor
	 * @param  MlPageWallModule $oModule - an instance of current module
	 */
	function MlPageWallResponse($oModule) {
	    parent::BxDolAlertsResponse();

	    $this->_oModule = $oModule;
	}	
	/**
	 * Overwtire the method of parent class.
	 *
	 * @param BxDolAlerts $oAlert an instance of alert.
	 */
  function _notifyFans($iSenderId = 0, $iPageId, $aExtraParams = array()) {
      if (!$iPageId || !db_value("SHOW TABLES LIKE 'bx_spy_data'")) return;
			require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesModule.php');
			$aModule = array(
				'class_prefix' => 'MlPages',
				'path' => 'modloaded/pages/',
				'db_prefix' => 'ml_pages_'
				);        
      $oMlPagesModule = new MlPagesModule($aModule);
      $aFans = $oMlPagesModule->_oDb->getPageFans($iPageId);
      $aRet = array(
              'sender_p_link'  => getProfileLink($iSenderId), 
              'sender_p_nick'  => getNickName($iSenderId),
              'page_url'     => $aExtraParams['page_url'],
              'page_title' => $aExtraParams['page_title'],
              'media_link' => $aExtraParams['media_link'],
              'media_title' => $aExtraParams['media_title'],
      );
      $aRet['lang_key']  = '_page_wall_notify';
      foreach($aFans as $aFan)
      {
      	if ($aFan['ID'] == $iSenderId) 
      		continue;
      	$aRet['recipient_id'] = $aFan['ID'];
      	$sData = serialize($aRet);
      	
      	db_res("INSERT INTO `bx_spy_data` SET `sender_id` = {$iSenderId}, `recipient_id` = {$aRet[recipient_id]}, `lang_key` = '{$aRet[lang_key]}', `date` = TIMESTAMP(NOW()), `type` = 'content_activity', `viewed` = 0, `params` = '{$sData}'");
      }
  }
	function response($oAlert) {
			global $site;
			if ((int)$oAlert->aExtras['from_wall']) return;
	    $bFromWall = !empty($oAlert->aExtras) && (int)$oAlert->aExtras['from_page_wall'] == 1;

	    if ($oAlert->sUnit != 'bx_forum' && $oAlert->sUnit != 'bx_files' && $oAlert->aExtras['page_id'])
	    {
	      $this->_oModule->_iOwnerId = (int)$oAlert->aExtras['owner_id'];
	      $sMedia = strtolower(str_replace('bx_', '', $oAlert->sUnit));
	      $aMediaInfo = $this->_oModule->_getCommonMedia($sMedia, $oAlert->iObject, $oAlert->aExtras['page_id']);
	      $iPageId = $oAlert->aExtras['page_id'];
	      $iOwnerId = $this->_oModule->_getAuthorId();
	      $iObjectId = $this->_oModule->_getAuthorId();
	      $sType = $this->_oModule->_oConfig->getCommonPostPrefix() . $sMedia;
	      $sAction = '';
	      $sContent = $aMediaInfo['content'];
	      $sTitle = $aMediaInfo['title'];
	      $sDescription = $aMediaInfo['description'];
	      $sMediaUri = db_value("SELECT `uri` FROM `ml_pages_main` WHERE `id` = {$iPageId} LIMIT 1"); 
	      $sMediaLink = BX_DOL_URL_ROOT . "m/pages/{$sMedia}/"; 
	    }
	    elseif ($oAlert->sUnit == 'bx_files' && $oAlert->aExtras['page_id'])
	    {
	    	$sMedia = strtolower(str_replace('bx_', '', $oAlert->sUnit));
    	  $iPageId = $oAlert->aExtras['page_id'];
	    	$iOwnerId = $this->_oModule->_getAuthorId();
	    	$iObjectId = $this->_oModule->_getAuthorId();
	    	$sTitle = _t('_page_wall_added_file');
	    	$sMediaUri = db_value("SELECT `uri` FROM `ml_pages_main` WHERE `id` = {$iPageId} LIMIT 1");   	
	    	$sContent = _t('_page_wall_new_file_content',  BX_DOL_URL_ROOT . "m/pages/files/" . $sMediaUri, db_value("SELECT `Title` FROM `bx_files_main` WHERE `ID` = {$oAlert->iObject} LIMIT 1"));
	    	$sType = $this->_oModule->_oConfig->getCommonPostPrefix() . $sMedia;	
	    	$sMediaLink = BX_DOL_URL_ROOT . "m/pages/files/"; 
	    	
	  	}
	    elseif (strpos($_SERVER['REQUEST_URI'], '/forum/pages/') && $oAlert->sUnit == 'bx_forum' && $aForumPage['entry_id'])
	    { 
	    	$aForumPage = db_arr("SELECT `entry_id`, `topic_uri`, `ml_pages_forum_topic`.`topic_title` FROM `ml_pages_forum` LEFT JOIN `ml_pages_forum_topic` ON `ml_pages_forum_topic`.`forum_id` = `ml_pages_forum`.`forum_id` WHERE `ml_pages_forum_topic`.`topic_uri` = '{$oAlert->iObject}' OR `ml_pages_forum_topic`.`topic_id` = '{$oAlert->iObject}' LIMIT 1");
	    	$iPageId = $aForumPage['entry_id'];
	    	$sMediaType = $oAlert->sAction;
	    	$iOwnerId = $this->_oModule->_getAuthorId();
	    	$iObjectId = $this->_oModule->_getAuthorId();
	    	$sTitle = $oAlert->sAction == 'new_topic' ? _t('_page_wall_new_forum_topic') : _t('_page_wall_reply_forum_topic');
	    	$sContent = _t('_page_wall_new_forum_content', BX_DOL_URL_ROOT . "forum/pages/topic/$aForumPage[topic_uri].htm", $aForumPage['topic_title']);
	    	$sType = $this->_oModule->_oConfig->getCommonPostPrefix() . $oAlert->sAction;
	    	$sMediaLink = BX_DOL_URL_ROOT . "forum/pages/topic/";
	    	$sMediaUri = "{$aForumPage[topic_uri]}.htm";
	    }
	    if (!$iPageId)
	    	return;
      $iId = $this->_oModule->_oDb->insertEvent(array(
          'owner_id' => $iOwnerId,
          'page_id' => $iPageId,
          'object_id' => $iObjectId,
          'type' => $sType,
          'action' => $sAction,
          'content' => addslashes($sContent),
          'title' => $sTitle,
          'description' => $sDescription
      ));
     
			if ($oAlert->iObject && $sMedia)
				$this->_oModule->_oDb->insertPageMedia($sMedia, $oAlert->iObject, $iPageId);
     
      if($bFromWall)
          echo "<script>parent." . $this->_oModule->_sJsPostObject . "._getPost(null, " . $iId . ")</script>";
      
      if ($iPageId)    
      {
      	$aPageInfo = db_arr("SELECT `uri`, `title` FROM `ml_pages_main` WHERE `id` = {$iPageId} LIMIT 1");
      	$this->_notifyFans($iOwnerId, $iPageId, array('page_url' => BX_DOL_URL_ROOT . 'm/pages/view/' . $aPageInfo['uri'], 'page_title' => $aPageInfo['title'], 'media_link' => $sMediaLink . $sMediaUri, 'media_title' => _t("_page_wall_add_". ($sMediaType ? $sMediaType : $sMedia) . "")));
			}

	}	
}
?>