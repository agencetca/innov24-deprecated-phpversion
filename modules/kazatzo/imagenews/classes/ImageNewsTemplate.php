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

bx_import('BxDolTextTemplate');

class ImageNewsTemplate extends BxDolTextTemplate {
	function ImageNewsTemplate(&$oConfig, &$oDb) {
	    parent::BxDolTextTemplate($oConfig, $oDb);

	    $this->sCssPrefix = 'imagenews';
	}
	
	function displayList($aParams) {
		global $site;
	    $sSampleType = $aParams['sample_type'];
	    $iViewerType = $aParams['viewer_type'];
	    $iStart = isset($aParams['start']) ? (int)$aParams['start'] : -1;
	    $iPerPage = isset($aParams['count']) ? (int)$aParams['count'] : -1;
	    $bShowEmpty = isset($aParams['show_empty']) ? $aParams['show_empty'] : true;
        $bAdminPanel = $iViewerType == BX_TD_VIEWER_TYPE_ADMIN && ((isset($aParams['admin_panel']) && $aParams['admin_panel']) || $sSampleType == 'admin');

        $sModuleUri = $this->_oConfig->getUri();
	    $aEntries = $this->_oDb->getEntries($aParams);
	    if(empty($aEntries)) 
	    	return $bShowEmpty ? MsgBox(_t('_' . $sModuleUri . '_msg_no_results')) : "";

	    $oTags = new BxDolTags();
	    $oCategories = new BxDolCategories();
	    
	    //--- Language translations ---//
        $sLKLinkPublish = _t('_' . $sModuleUri . '_lcaption_publish');
        $sLKLinkEdit = _t('_' . $sModuleUri . '_lcaption_edit');
        $sLKLinkDelete = _t('_' . $sModuleUri . '_lcaption_delete');
        
        $sBaseUri = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
        $sJsMainObject = $this->_oConfig->getJsObject();

        $aResult['sample'] = $sSampleType;
        $aResult['bx_repeat:entries'] = array();        
	    foreach($aEntries as $aEntry) {
	        $sVotes = "";            

	        if($this->_oConfig->isVotesEnabled()) {
                $oVotes = $this->_oModule->_createObjectVoting($aEntry['id']);
                $sVotes = $oVotes->getJustVotingElement(0, $aEntry['id']);
	        }

	        $aTags = $oTags->explodeTags($aEntry['tags']);
	        $aCategories = $oCategories->explodeTags($aEntry['categories']);

	        $aTagItems = array();	        
	        foreach($aTags as $sTag) {
	        	$sTag = trim($sTag);
	        	$aTagItems[] = array('href' => $sBaseUri . 'tag/' . title2uri($sTag), 'title' => $sTag);
	        }

	        $aCategoryItems = array();	        
	        foreach($aCategories as $sCategory) {
	        	$sCategory = trim($sCategory);
	        	$aCategoryItems[] = array('href' => $sBaseUri . 'category/' . title2uri($sCategory), 'title' => $sCategory);
	        }
			if (!empty($aEntry['image']))
			{
				$path = "{$site['url']}modules/kazatzo/imagenews/data/files/" . $aEntry['image'];
				$htmlImage = "<img src='" . $path . "' /><br \><br \>";
				$display = "block";
				$container = "content-wrapper";
			}
			else
			{
				$display = "none";
				$container = "content-wrapper-no-img";
			}
			$aResult['bx_repeat:entries'][] = array(
                'id' => $this->_oConfig->getSystemPrefix() . $aEntry['id'],
                'caption' => str_replace("$", "&#36;", $aEntry['caption']),
                'class' => !in_array($sSampleType, array('view')) ? ' ' . $this->sCssPrefix . '-text-snippet' : '',
                'date' => getLocaleDate($aEntry['when_uts'], BX_DOL_LOCALE_DATE),
	        	'comments' => (int)$aEntry['cmts_count'],
	        	'bx_repeat:categories' => $aCategoryItems,
	        	'bx_repeat:tags' => $aTagItems,	    
				'snippet' => str_replace("$", "&#36;", $aEntry['snippet']),
				'image' =>  $htmlImage ,
				'content' => str_replace("$", "&#36;", $aEntry['content']),
				'path' => $path,
				'display' => $display,
				'container' => $container,
                'link' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aEntry['uri'],
                'voting' => $sVotes,
                'bx_if:checkbox' => array(
                    'condition' => $bAdminPanel,
                    'content' => array(
                        'id' => $aEntry['id']
                    ),
                ),
                'bx_if:status' => array(
                    'condition' => $iViewerType == BX_TD_VIEWER_TYPE_ADMIN,
                    'content' => array(
                        'status' => _t('_' . $sModuleUri . '_status_' . $aEntry['status'])
                    ),
                ),
                'bx_if:featured' => array(
                    'condition' => $iViewerType == BX_TD_VIEWER_TYPE_ADMIN && (int)$aEntry['featured'] == 1,
                    'content' => array(),
                ),
                'bx_if:edit_link' => array (
                    'condition' => $iViewerType == BX_TD_VIEWER_TYPE_ADMIN,
                    'content' => array(
                        'edit_link_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'admin/' . $aEntry['uri'],
                        'edit_link_caption' => $sLKLinkEdit,
                    )
                )
            );	        
	    };

	    $aResult['paginate'] = '';
	    if(!in_array($sSampleType, array('id', 'uri', 'view', 'search_unit'))) {
	    	if(!empty($sSampleType))
    	    	$this->_updatePaginate($aParams);
			
			$aResult['paginate'] = $this->oPaginate->getPaginate($iStart, $iPerPage);
	    }

	    $aResult['loading'] = LoadingBox($sModuleUri . '-' . $sSampleType . '-loading');

		if ($bAdminPanel)
			$sRes = $this->parseHtmlByName('list-admin.html', $aResult);
		else
		{
			//if (count($aEntries)>1) 
			if($sSampleType != 'view')
			{
				if ($this->_oConfig->getImageType() == "Small image next to text")
					$sRes = $this->parseHtmlByName('list.html', $aResult);
				else
					$sRes = $this->parseHtmlByName('list3.html', $aResult);
			}
			else
				$sRes = $this->parseHtmlByName('list2.html', $aResult);
		}
	    return $sRes;
	}
}
?>