<?
/***************************************************************************
* Date				: Jun 17, 2011
* Copywrite			: (c) 2011 by kazatzo
*
* Product Name		: Image News
* Product Version	: 1.0
*
* IMPORTANT: This is a commercial product made by kazatzo
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from kazatzo
*
***************************************************************************/

bx_import('BxDolTextModule');

require_once('ImageNewsCalendar.php');
require_once('ImageNewsCmts.php');
require_once('ImageNewsVoting.php');
require_once('ImageNewsSearchResult.php');
require_once('ImageNewsData.php');

class ImageNewsModule extends BxDolTextModule {
	/**
	 * Constructor
	 */
	function ImageNewsModule($aModule) {
	    parent::BxDolTextModule($aModule);

		//--- Define Membership Actions ---//
        defineMembershipActions(array('imagenews delete'), 'ACTION_ID_');
	}

	/**
	 * Service methods
	 */
	function serviceImageNewsRss($iLength = 0) {
	    return $this->actionRss($iLength);
	}

	/**
	 * Action methods
	 */
	function actionGetImageNews($sSampleType = 'all', $iStart = 0, $iPerPage = 0) {
		return $this->actionGetEntries($sSampleType, $iStart, $iPerPage);
	}

	/**
	 * Private methods. 
	 */
	function _createObjectCmts($iId) {
		return new ImageNewsCmts($this->_oConfig->getCommentsSystemName(), $iId);
	}
	function _createObjectVoting($iId) {
		return new ImageNewsVoting($this->_oConfig->getVotesSystemName(), $iId);
	}
	function _isDeleteAllowed($bPerform = false) {
		if(!isLogged())
			return false;

		if(isAdmin())
			return true;
			
		$aCheckResult = checkAction(getLoggedId(), ACTION_ID_IMAGENEWS_DELETE, $bPerform);			
		return $aCheckResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
	}
	
	function _actDelete($aIds) {	
		global $dir;
	    if(!$this->_isDeleteAllowed(true)) 
            return false;

		if(is_int($aIds) || is_string($aIds))
			$aIds = array((int)$aIds);
	    
		$aEntries = array();
		foreach($aIds as $iId) {
			$aEntries[$iId] = $this->_oDb->getEntries(array('sample_type' => 'id', 'id' => $iId));
	    }
		
	    $bResult = $this->_oDb->deleteEntries($aIds);
	    if($bResult) {
            $oTags = new BxDolTags();
            $oCategories = new BxDolCategories();
            $oSubscription = new BxDolSubscription();

	        foreach($aIds as $iId) {
				// delete image if exists
				if ($aEntries[$iId]['image'] != '' && !empty($aEntries[$iId]['image']))
				{
					@unlink("{$dir['root']}modules/kazatzo/imagenews/data/files/" . $aEntries[$iId]['image']);
				}
			
    	        //--- Entry -> Delete for Alerts Engine ---//
                $oAlert = new BxDolAlerts('imagenews', 'delete', $iId, BxDolTextData::getAuthorId());
                $oAlert->alert();
                //--- Entry -> Delete for Alerts Engine ---//
			
                //--- Reparse Global Tags ---//
                $oTags->reparseObjTags('imagenews', $iId);
                //--- Reparse Global Tags ---//
                
                //--- Reparse Global Categories ---//
                $oCategories->reparseObjTags('imagenews', $iId);
                //--- Reparse Global Categories ---//
                
                //--- Remove all subscriptions ---//
                $oSubscription->unsubscribe(array('type' => 'object_id', 'unit' => 'imagenews', 'object_id' => $iId));
                //--- Remove all subscriptions ---//
	        }
	    }
	    return $bResult;
	}
	function actionTags() {
		$sUri = $this->_oConfig->getUri();
		$oTags = new BxTemplTagsModule(array('type' =>  $sUri, 'orderby' => 'popular'), _t('_' . $sUri . '_bcaption_all_tags'), BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'tags');

		$aParams = array(
			'index' => 1,
			'css' => array('view.css'),
			'title' => array(
				'page' => _t('_' . $sUri . '_pcaption_tags')
			),
			'content' => array(
				'page_main_code' => $oTags->getCode()
			)
		);
		$this->_oTemplate->getPageCode($aParams);
	}
	
	function actionTag($sTag = '', $iPage = 1, $iPerPage = 0) {
		$sUri = $this->_oConfig->getUri();
		$sBaseUri = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$sContent = MsgBox(_t('_' . $sUri . '_msg_no_results'));
		if(!empty($sTag))
			list($sTagDisplay, $sContent) = $this->getTagContent($sTag, $iPage, $iPerPage);

		$aParams = array(
			'css' => array('view.css'),
			'title' => array(
				'page' => _t('_' . $sUri . '_pcaption_tag', $sTagDisplay),
				'block' => _t('_' . $sUri . '_bcaption_tag', $sTagDisplay)
			),
			'breadcrumb' => array(
				_t('_' . $sUri . '_top_menu_item') => $sBaseUri . 'home/',
				_t('_' . $sUri . '_tags_top_menu_sitem') => $sBaseUri . 'tags/',
				$sTagDisplay => ''
			),
			'content' => array(
				'page_main_code' => $sContent
			)
		);
		$this->_oTemplate->getPageCode($aParams);
	}
	
	function actionCategories() {
		$sUri = $this->_oConfig->getUri();
		$oCategories = new BxTemplCategoriesModule(array('type' => $sUri), _t('_' . $sUri . '_bcaption_all_categories'), BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'category');

		$aParams = array(
			'index' => 1,
			'title' => array(
				'page' => _t('_' . $sUri . '_pcaption_categories')
			),
			'content' => array(
				'page_main_code' => $oCategories->getCode()
			)
		);
		$this->_oTemplate->getPageCode($aParams);
	}
	
	function actionCategory($sCategory = '', $iPage = 1, $iPerPage = 0) {
		$sUri = $this->_oConfig->getUri();
		$sBaseUri = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$sContent = MsgBox(_t('_' . $sUri . '_msg_no_results'));
		if(!empty($sCategory))
			list($sCategoryDisplay, $sContent) = $this->getCategoryContent($sCategory, $iPage, $iPerPage);

		$aParams = array(
			'title' => array(
				'page' => _t('_' . $sUri . '_pcaption_category', $sCategoryDisplay),
				'block' => _t('_' . $sUri . '_bcaption_category', $sCategoryDisplay)
			),
			'breadcrumb' => array(
				_t('_' . $sUri . '_top_menu_item') => $sBaseUri . 'home/',
				_t('_' . $sUri . '_categories_top_menu_sitem') => $sBaseUri . 'categories/',
				$sCategoryDisplay => ''
			),
			'content' => array(
				'page_main_code' => $sContent
			)
		);
		$this->_oTemplate->getPageCode($aParams);
	}
	
	function serviceViewBlock($sUri) {
	    $aParams = is_numeric($sUri) ? array('sample_type' => 'id', 'id' => $sUri) : array('sample_type' => 'uri', 'uri' => $sUri);
	    $aEntry = $this->_oDb->getEntries($aParams);

	    $sModuleUri = $this->_oConfig->getUri();
	    $oView = new BxDolViews($sModuleUri, $aEntry['id']);
        $oView->makeView();

        $this->_oTemplate->setPageTitle($aEntry['caption']);
        $GLOBALS['oTopMenu']->setCustomSubHeader($aEntry['caption']);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_' . $sModuleUri . '_top_menu_item') => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'index/',
            $aEntry['caption'] => '')
        );
	    return $this->_oTemplate->displayList(array(
	       'sample_type' => 'view', 
	       'viewer_type' => $this->_oTextData->getViewerType(), 
	       'uri' => $aEntry['uri']
        ));
	}
	
	function serviceActionBlock($sUri) {
	    $aParams = is_numeric($sUri) ? array('sample_type' => 'id', 'id' => $sUri) : array('sample_type' => 'uri', 'uri' => $sUri);
	    $aEntry = $this->_oDb->getEntries($aParams);

	    $sModuleUri = $this->_oConfig->getUri();
	    if($aEntry['status'] != BX_TD_STATUS_ACTIVE)
            return MsgBox(_t('_' . $sModuleUri . '_msg_no_results'));

		$sModuleUri = $this->_oConfig->getUri();
        $oSubscription = new BxDolSubscription();
        $aButton = $oSubscription->getButton($this->getUserId(), $sModuleUri, '', $aEntry['id']);        

		$aReplacement['sbs_' . $sModuleUri . '_title'] = $aButton['title']; 
        $aReplacement['sbs_' . $sModuleUri . '_script'] = $aButton['script'];
                
        if($this->_isDeleteAllowed()) {
        	$this->_oTemplate->addJsTranslation(array('_' . $sModuleUri . '_msg_success_delete', '_' . $sModuleUri . '_msg_failed_delete'));

        	$aReplacement['del_' . $sModuleUri . '_title'] = _t('_' . $sModuleUri . '_actions_delete');
        	$aReplacement['del_' . $sModuleUri . '_script'] = $this->_oConfig->getJsObject() . '.deleteEntry(' . $aEntry['id'] . ')';
        }
        else
        	$aReplacement['del_' . $sModuleUri . '_title'] = '';

		return $oSubscription->getData() . $GLOBALS['oFunctions']->genObjectsActions($aReplacement, $this->_oConfig->getUri());
	}
	
	function serviceGetSubscriptionParams($sUnit, $sAction, $iObjectId) {
		$sUnit = str_replace('imagenews', '_imagenews', $sUnit);
		if(empty($sAction))
			$sAction = 'main';

		$aItem = $this->_oDb->getEntries(array('sample_type' => 'id', 'id' => $iObjectId));

		return array(
			'template' => array(
				'Subscription' => _t($sUnit . '_sbs_' . $sAction, $aItem['caption']), 
				'ViewLink' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri()  . 'view/' . $aItem['uri']
			)
		);
	}
		
	function _actFeatured($aIds, $bPositive = true) {	    
	    if(!isAdmin()) 
            return false;

		if(is_int($aIds) || is_string($aIds))
			$aIds = array((int)$aIds);

	    $bResult = $this->_oDb->updateEntry($aIds, array('featured' => ($bPositive ? 1 : 0)));
	    if($bResult)
	        foreach($aIds as $iId) {
    	        //--- Entry -> Featured for Alerts Engine ---//
                bx_import('BxDolAlerts');
                $oAlert = new BxDolAlerts($this->_oConfig->getUri(), 'featured', $iId, BxDolTextData::getAuthorId());
                $oAlert->alert();
                //--- Entry -> Featured for Alerts Engine ---//
	        }

	    return $bResult;
	}
	
	function _actPublish($aIds, $bPositive = true) {
	    if(!isAdmin()) 
            return false;

		if(is_int($aIds) || is_string($aIds))
			$aIds = array((int)$aIds);

	    $bResult = $this->_oDb->updateEntry($aIds, array('status' => ($bPositive ? BX_TD_STATUS_ACTIVE : BX_TD_STATUS_INACTIVE)));
	    if($bResult)
	        foreach($aIds as $iId) {
    	        //--- Entry -> Publish/Unpublish for Alerts Engine ---//
                $oAlert = new BxDolAlerts($this->_oConfig->getUri(), ($bPositive ? 'publish' : 'unpublish'), $iId, BxDolTextData::getAuthorId());
                $oAlert->alert();
                //--- Entry -> Publish/Unpublish for Alerts Engine ---//
                
                //--- Reparse Global Tags ---//
                $oTags = new BxDolTags();
                $oTags->reparseObjTags('imagenews', $iId);
                //--- Reparse Global Tags ---//
                
                //--- Reparse Global Categories ---//            
                $oCategories = new BxDolCategories();
                $oCategories->reparseObjTags('imagenews', $iId);
                //--- Reparse Global Categories ---//
	        }
        
	    return $bResult;
	}
	
	function actionRss($iLength = 0) {
		$iLength = $iLength != 0 ? $iLength : (int)$this->_oConfig->getRssLength();

	    $aEntries = $this->_oDb->getEntries(array(
            'sample_type' => 'archive', 
            'viewer_type' => $this->_oTextData->getViewerType(), 
            'start' => 0, 
            'count' => $iLength
        ));

        $aRssData = array();
        $sRssViewUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/';
        foreach($aEntries as $aEntry) {
            if(empty($aEntry['caption'])) continue;

        	$aRssData[$aEntry['id']] = array(
        	   'UnitID' => $aEntry['id'],
        	   'OwnerID' => $aEntry['id'],
        	   'UnitTitle' => $aEntry['caption'],
        	   'UnitLink' => $sRssViewUrl . $aEntry['uri'],
        	   'UnitDesc' => $aEntry['content'],
        	   'UnitDateTimeUTS' => $aEntry['when_uts'],
        	   'UnitIcon' => ''
            );
        }

	    $oRss = new BxDolRssFactory();
	    return $oRss->GenRssByData($aRssData, _t('_imagenews_rss_caption'), $this->_oConfig->getBaseUri() . 'act_rss/');
	}
}
?>
