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

bx_import('BxDolTextData');
bx_import ('BxDolImageResize');

class ImageNewsData extends BxDolTextData {    
	function ImageNewsData(&$oModule) {
	    parent::BxDolTextData('imagenews', $oModule->_oConfig->getUri());

	    $this->_oModule = &$oModule;
		$oCategories = new BxDolCategories();
		$oCategories->getTagObjectConfig();
		$this->_aForm = array(
            'form_attrs' => array(
                'id' => 'text_data',
                'name' => 'text_data',
                'action' => BX_DOL_URL_ROOT . $this->_oModule->_oConfig->getBaseUri() . 'admin/',
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ),
            'params' => array (
                'db' => array(
                    'table' => $this->_oModule->_oDb->getPrefix() . 'entries',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'caption',
                    'submit_name' => 'post'
                ),
            ),
            'inputs' => array (
                'author_id' => array(
                    'type' => 'hidden',
                    'name' => 'author_id',                
                    'value' => 0,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),
                'caption' => array(
                    'type' => 'text',
                    'name' => 'caption',
                    'caption' => _t("_td_caption"),
                    'value' => '',
                	'required' => 1,
					'info' => _t('_imagenews_info_caption'),
                    'checker' => array (  
                        'func' => 'length',
                        'params' => array(3,64),
                        'error' => _t('_imagenews_err_title_length'),
                    ),                    
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
				'old_file' => array(
					'type' => 'custom',
					'content' => '',
					'colspan' => true, 				
				),
				'image_browse' => array(
					'type' => 'file',
					'name' => 'image_browse',
					'caption' => _t('_imagenews_browse_file'),
					'value' => '', 				
				),
                'snippet' => array(
                    'type' => 'textarea',
                    'html' => 0,
                    'name' => 'snippet',
                    'caption' => _t("_td_snippet"),
                    'value' => '',
                	'required' => 1,
					'info' => _t('_imagenews_info_snippet'),
                    'checker' => array (  
                        'func' => 'length',
                        'params' => array(3, $this->_oModule->_oConfig->getSnippetLength()),
                        'error' => _t('_imagenews_err_snippet_length'),
                    ),                    
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'content' => array(
                    'type' => 'textarea',
                    'html' => 2,
                    'name' => 'content',
                    'caption' => _t("_td_content"),
                    'value' => '',
                	'required' => 1,
                    'checker' => array (  
                        'func' => 'length',
                        'params' => array(3,65536),
                        'error' => _t('_imagenews_err_caption_length'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml',
                    ),
                ),
                'when' => array(
                    'type' => 'datetime',
                    'name' => 'when',
                    'caption' => _t("_td_date"),
                    'value' => date('Y-m-d H:i:00'),
                	'required' => 1,
                    'checker' => array (  
                        'func' => 'DateTime',
                        'error' => _t('_td_err_empty_value'),
                    ),                    
                    'db' => array (
                        'pass' => 'DateTime', 
                    ),
                ),
                'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t("_td_tags"),
                    'value' => '',
                	'required' => 1,
                    'checker' => array (  
                        'func' => 'length',
                        'params' => array(3,64),
                        'error' => _t('_imagenews_err_tag_length'),
                    ),
                    'info' => _t('_sys_tags_note'),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
                'categories' => $oCategories->getGroupChooser ('imagenews', $this->_iOwnerId, true),
                'allow_comment_to' => array(
					'type' => 'hidden',
					'name' => 'comment',
					'value' => 0,
					'db' => array (
						'pass' => 'Int',
					),
				),
                'allow_vote_to' => array(
					'type' => 'hidden',
					'name' => 'vote',
					'value' => 0,
					'db' => array (
						'pass' => 'Int',
					),
				),
                'post' => array(
                    'type' => 'submit',
                    'name' => 'post',
                    'value' => _t("_td_post"),
                ),
            )
        );
	}
	
	function getPostForm($aAddFields = array()) {
        $oForm = new BxTemplFormView($this->_aForm);
        $oForm->initChecker();

        if($oForm->isSubmittedAndValid()) {
			global $dir;
			if(isset($_POST['post']) && $_FILES['image_browse']['name']){
				$aFileInfo = getimagesize($_FILES['image_browse']['tmp_name']);
				if(empty($aFileInfo))
					return '_adm_txt_settings_file_not_image';

				$sExt = '';
				switch( $aFileInfo['mime'] ) {
					case 'image/jpeg': $sExt = 'jpg'; break;
					case 'image/gif':  $sExt = 'gif'; break;
					case 'image/png':  $sExt = 'png'; break;
				}
				if(empty($sExt))
					return '_adm_txt_settings_file_wrong_format';

				$sFileName = mktime() . '.' . $sExt;
				$sFilePath = "{$dir['root']}modules/kazatzo/imagenews/data/files/" . $sFileName;
				if(!move_uploaded_file( $_FILES['image_browse']['tmp_name'], $sFilePath))
					return '_adm_txt_settings_file_cannot_move';
				list($iWidth, $iHeight) = getimagesize($sFilePath);
				$o =& BxDolImageResize::instance($iWidth, $iHeight);
				if (($iWidth/$iHeight) > 16/9)
				{
					$fDiffWidth = $iWidth - (16/9)*$iHeight;
					$iWidth = (16/9)*$iHeight;
					$o->setCropOptions($fDiffWidth/2,  0, $iWidth, $iHeight);
				}
				else 
				{
					$fDiffHeight = $iHeight - (9*$iWidth)/16;
					$iHeight = (9*$iWidth)/16;
					$o->setCropOptions(0,  $fDiffHeight/2, $iWidth, $iHeight);
				}
				$o->setJpegOutput (false);
				$o->setSize (650, 361);
				$o->setSquareResize (false);
				$o->resize($sFilePath, $sFilePath);
			}		  
        	$iDateNow = mktime();
        	$iDatePublish = $oForm->getCleanValue('when');
        	if($iDatePublish > $iDateNow)
        		$iStatus = BX_TD_STATUS_PENDING;
        	else if($iDatePublish <= $iDateNow && $this->_oModule->_oConfig->isAutoapprove())
        		$iStatus = BX_TD_STATUS_ACTIVE;
        	else
        		$iStatus = BX_TD_STATUS_INACTIVE;

            $aDefFields = array(
                'uri' => $oForm->generateUri(),
                'date' => $iDateNow,
            	'status' => $iStatus,
				'image' => $sFileName
            );
            $iId = $oForm->insert(array_merge($aDefFields, $aAddFields));
            
            //--- 'System' -> Post for Alerts Engine ---//
            bx_import('BxDolAlerts');
            $oAlert = new BxDolAlerts('imagenews', 'post', $iId, $this->_iOwnerId);
            $oAlert->alert();
            //--- 'System' -> Post for Alerts Engine ---//
            
            //--- Reparse Global Tags ---//
            $oTags = new BxDolTags();
            $oTags->reparseObjTags('imagenews', $iId);
            //--- Reparse Global Tags ---//
            
            //--- Reparse Global Categories ---//
            $oCategories = new BxDolCategories();
            $oCategories->reparseObjTags('imagenews', $iId);
            //--- Reparse Global Categories ---//

            header('Location: ' . $oForm->aFormAttrs['action']);
        }
        else 
            return $oForm->getCode();
    }
	
	function getEditForm($aValues, $aAddFields = array()) {
		global $site;
        $oCategories = new BxDolCategories();
		if (isset($this->_aForm['inputs']['categories'])) {
			//--- convert post form to edit one ---//
			$this->_aForm['inputs']['categories'] = $oCategories->getGroupChooser('imagenews', $this->_iOwnerId, true, $aValues['categories']);
        }
        if (!empty($aValues) && is_array($aValues)) {
            foreach($aValues as $sKey => $sValue)
                if (array_key_exists($sKey, $this->_aForm['inputs'])) {
                    if ($this->_aForm['inputs'][$sKey]['type'] == 'checkbox')
                        $this->_aForm['inputs'][$sKey]['checked'] = (int)$sValue == 1 ? true : false;
                    else if($this->_aForm['inputs'][$sKey]['type'] == 'select_box' && $this->_aForm['inputs'][$sKey]['name'] == 'Categories') {
                        $aCategories = preg_split( '/['.$oCategories->sTagsDivider.']/', $sValue, 0, PREG_SPLIT_NO_EMPTY );
                        $this->_aForm['inputs'][$sKey]['value'] = $aCategories;
                    }   
                    else if($this->_aForm['inputs'][$sKey]['name'] != 'when') 
                        $this->_aForm['inputs'][$sKey]['value'] = $sValue;
                }
            unset( $this->_aForm['inputs']['author_id']);
            $this->_aForm['inputs']['id'] = array(
                'type' => 'hidden',
                'name' => 'id',                
                'value' => $aValues['id'],
                'db' => array (
                    'pass' => 'Int',
                )
            );
			if ($aValues['image'] != '')
			{
				$path = "{$site['url']}modules/kazatzo/imagenews/data/files/" . $aValues['image'];
				$htmlImage = "<img src='" . $path . "' />";
				$this->_aForm['inputs']['old_file']['content'] = $htmlImage;
			}
            $this->_aForm['inputs']['post']['value'] = _t("_td_edit");
        }
        $oForm = new BxTemplFormView($this->_aForm);
        $oForm->initChecker();
        
        if ($oForm->isSubmittedAndValid()) {
			global $dir;
			if ($_FILES['image_browse']['name']){
				$aFileInfo = getimagesize( $_FILES['image_browse']['tmp_name']);
				if (empty($aFileInfo))
					return '_adm_txt_settings_file_not_image';

				$sExt = '';
				switch ( $aFileInfo['mime'] ) {
					case 'image/jpeg': $sExt = 'jpg'; break;
					case 'image/gif':  $sExt = 'gif'; break;
					case 'image/png':  $sExt = 'png'; break;
				}
				if (empty($sExt))
					return '_adm_txt_settings_file_wrong_format';

				$sFileName = mktime() . '.' . $sExt;
				$sFilePath = "{$dir['root']}modules/kazatzo/imagenews/data/files/" . $sFileName;
				if(!move_uploaded_file( $_FILES['image_browse']['tmp_name'], $sFilePath))
					return '_adm_txt_settings_file_cannot_move';
				list($iWidth, $iHeight) = getimagesize($sFilePath);
				$o =& BxDolImageResize::instance($iWidth, $iHeight);
				if (($iWidth/$iHeight) > 16/9)
				{
					$fDiffWidth = $iWidth - (16/9)*$iHeight;
					$iWidth = (16/9)*$iHeight;
					$o->setCropOptions($fDiffWidth/2,  0, $iWidth, $iHeight);
				}
				else 
				{
					$fDiffHeight = $iHeight - (9*$iWidth)/16;
					$iHeight = (9*$iWidth)/16;
					$o->setCropOptions(0,  $fDiffHeight/2, $iWidth, $iHeight);
				}
				$o->setJpegOutput (false);
				$o->setSize (650, 361);
				$o->setSquareResize (false);
				$o->resize($sFilePath, $sFilePath);
			}		  
        	$iDateNow = mktime();
        	$iDatePublish = $oForm->getCleanValue('when');
        	if ($iDatePublish > $iDateNow)
        		$iStatus = BX_TD_STATUS_PENDING;
        	else if ($iDatePublish <= $iDateNow && $this->_oModule->_oConfig->isAutoapprove())
        		$iStatus = BX_TD_STATUS_ACTIVE;
        	else
        		$iStatus = BX_TD_STATUS_INACTIVE;
			if ($sFileName !=''){
				// check if an image already exists and remove it
				if($aValues['image'] != '' && !empty($aValues['image']))
					@unlink("{$dir['root']}modules/kazatzo/imagenews/data/files/" . $aValues['image']);
				$aDefFields = array(                
					'date' => $iDateNow,
					'status' => $iStatus,
					'image' => $sFileName
				);
			}
			else {
				$aDefFields = array(                
					'date' => $iDateNow,
					'status' => $iStatus
				);
			}
            $oForm->update($aValues['id'], array_merge($aDefFields, $aAddFields));

            //--- 'System' -> Edit for Alerts Engine ---//
            bx_import('BxDolAlerts');
            $oAlert = new BxDolAlerts('imagenews', 'edit', $aValues['id'], $this->_iOwnerId);
            $oAlert->alert();
            //--- 'System' -> Edit for Alerts Engine ---//

            //--- Reparse Global Tags ---//
            $oTags = new BxDolTags();
            $oTags->reparseObjTags('imagenews', $aValues['id']);
            //--- Reparse Global Tags ---//

            //--- Reparse Global Categories ---//            
            $oCategories->reparseObjTags('imagenews', $aValues['id']);
            //--- Reparse Global Categories ---//

            header('Location: ' . $oForm->aFormAttrs['action']);
        }
        else 
            return $oForm->getCode();
    }
	
	function getImageNews()
	{
	
	
	
	}
}
?>