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

require_once( BX_DIRECTORY_PATH_ROOT . 'plugins/Services_JSON.php' );

class BxDolPageViewAdmin {
	var $aPages = array();
	var $oPage;
	var $sPage_db; //name of current page, used form database manipulations
	var $sDBTable; //used database table
	var $bAjaxMode = false;
	var $aTitles; // array containing aliases of pages

	function BxDolPageViewAdmin( $sDBTable, $sCacheFile ) {

        $GLOBALS['oAdmTemplate']->addJsTranslation(array(
        	'_adm_pbuilder_Reset_page_warning',
        	'_adm_pbuilder_Column_non_enough_width_warn',
        	'_adm_pbuilder_Column_delete_confirmation',
        	'_adm_pbuilder_Add_column',
        	'_adm_pbuilder_Want_to_delete'
        ));
        $GLOBALS['oAdmTemplate']->addJsImage(array(
        	'pb_delete_column' => 'pb_cross.gif'
        ));

        $this -> sDBTable = $sDBTable;
		$this -> sCacheFile = $sCacheFile;
        
        // special actions (without creating page)
        if (isset($_REQUEST['action_sys'])) {
            switch ($_REQUEST['action_sys']) {
                case 'loadNewPageForm':
                    echo $this -> getCssCode();
                    echo $this -> showNewPageForm();
                break;
                
                case 'createNewPage':
                    echo $this->createUserPage();
                break;
            }
            exit;
        }
        
		$sPage = process_pass_data( isset( $_REQUEST['Page'] ) ? trim( urldecode ($_REQUEST['Page']) ) : '' );

		$this -> getPages();
		
		if (strlen($sPage) && in_array($sPage, $this->aPages)) {
            $this->oPage = new BxDolPVAPage( $sPage, $this );
		}
        
		$this -> checkAjaxMode();
		if( !empty($_REQUEST['action']) and $this -> oPage ) {
			$this -> sPage_db = addslashes( $this -> oPage -> sName );
			
			switch( $_REQUEST['action'] ) {
				case 'load':
					header( 'Content-type:text/javascript' );
					send_headers_page_changed();
					echo $this -> oPage -> getJSON();
					break;
				
				case 'saveColsWidths':
					if( is_array( $_POST['widths'] ) ) {
						$this -> saveColsWidths( $_POST['widths'] );
						$this -> createCache();
					}
					break;
				
				case 'saveBlocks':
					if( is_array( $_POST['columns'] ) ) {
						$this -> saveBlocks( $_POST['columns'] );
						$this -> createCache();
					}
					break;
				
				case 'loadEditForm':
					if( $iBlockID = (int)$_POST['id'] ) {
                        header( 'Content-type:text/html;charset=utf-8' );
						echo $this -> getCssCode();
						echo $this -> showPropForm( $iBlockID );
					}
					break;
				
				case 'saveItem':
					if( (int)$_POST['id'] ) {
						$this -> saveItem( $_POST );
						$this -> createCache((int)$_POST['id']);
					}
					break;

				case 'deleteCustomPage' :
				    header( 'Content-type:text/html;charset=utf-8' );
				    $sPage = isset($_POST['Page']) ? $_POST['Page'] : '';

				    if(!$sPage) {
				        echo _t('_Error Occured');
				    }
				    else {
				        //remove page from page builder
				        $this -> deleteCustomPage($sPage);
	                }
				    break;

				case 'deleteBlock':
					if( $iBlockID = (int)$_REQUEST['id'] ) {
						$this -> deleteBlock( $iBlockID );
						$this -> createCache();
					}
					break;
				
				case 'checkNewBlock':
					if( $iBlockID = (int)$_REQUEST['id'] )
						$this -> checkNewBlock( $iBlockID );
					break;
				
				case 'savePageWidth':
					if( $sPageWidth = process_pass_data( $_POST['width'] ) ) {
						$this -> savePageWidth( $sPageWidth );
						$this -> createCache();
						
						if( $this -> oPage -> sName == 'index' ) {
							if( $sPageWidth == '100%' )
								setParam( 'promoWidth', '998' );
							else
								setParam( 'promoWidth', (int)$sPageWidth );
							
							ResizeAllPromos();
						}
					}
					break;
				
				case 'saveOtherPagesWidth':
					if( $sWidth = $_REQUEST['width'] ) {
						setParam( 'main_div_width', $sWidth );
						echo 'OK';
					}
					break;
				
				case 'resetPage':
					$this -> resetPage();
					$this -> createCache();
					break;
			}
		}
		if($this -> bAjaxMode)
			exit;

		$sMainPageContent = $this -> showBuildZone();

		global $_page, $_page_cont;			
		$iNameIndex = 0;
		$_page = array(
			'name_index' => $iNameIndex,
			'css_name' => array('pageBuilder.css', 'forms_adv.css'),
			'js_name' => array('ui.core.js', 'ui.sortable.js', 'ui.slider.js', 'BxDolPageBuilder.js'),
			'header' => _t('_adm_pbuilder_title'),
			'header_text' => _t('_adm_pbuilder_box_title'),
		);
		$_page_cont[$iNameIndex]['page_main_code'] = $GLOBALS['oAdmTemplate']->addJs(array('tiny_mce_gzip.js', 'page_builder_tiny.js'), 1) . $sMainPageContent;

		PageCodeAdmin();
	}
	
    function createUserPage() {
        
        $sUri = process_db_input($_REQUEST['uri']);
        
        if (db_value("select `Name` from `{$this -> sDBTable}_pages` where `Name` = '$sUri'"))
            return 'Uri is not unique.';
        
        $sTitle = process_db_input($_REQUEST['title']);
        $bIsSystem = 0;
		
		$sQuery = "
			INSERT INTO `{$this -> sDBTable}_pages` (`Name`, `Title`, `Order`, `System`)
			SELECT '{$sUri}', '$sTitle', MAX(`Order`) + 1, '$bIsSystem'
			FROM `{$this -> sDBTable}_pages` LIMIT 1
		";
		
        db_res($sQuery);

		if (!db_affected_rows()) return 'Failed database insert';

		return 'OK';
    }

	function savePageWidth( $sPageWidth ) {
		$sPageWidth = process_db_input( $sPageWidth, BX_TAGS_STRIP );
		$sQuery = "UPDATE `{$this -> sDBTable}` SET `PageWidth` = '{$sPageWidth}' WHERE `Page` = '{$this -> sPage_db}'";
		db_res( $sQuery );

		echo 'OK';
	}

	function createCache($iBlockId = 0) {
		$oCacher = new BxDolPageViewCacher( $this -> sDBTable, $this -> sCacheFile );
		$oCacher -> createCache();

        if ($iBlockId > 0) {
            $oCacheBlocks = $oCacher->getBlocksCacheObject ();
            $a = array (
                $iBlockId.true.'tab'.false, 
                $iBlockId.false.'tab'.false, 
                $iBlockId.true.'popup'.false,
                $iBlockId.false.'popup'.false,
                $iBlockId.true.'tab'.true,
                $iBlockId.false.'tab'.true,
                $iBlockId.true.'popup'.true,
                $iBlockId.false.'popup'.true
            );
            foreach ($a as $sKey)
                $oCacheBlocks->delData($oCacher->genBlocksCacheKey ($sKey));
        }

	}

	function checkNewBlock( $iBlockID ) {
	    $iBlockID = (int) $iBlockID;

		$sQuery = "SELECT `Desc`, `Caption`, `Func`, `Content`, `Visible` FROM `{$this -> sDBTable}` WHERE `ID` = '{$iBlockID}'";
		$aBlock = db_assoc_arr( $sQuery );
		
		if( $aBlock['Func'] == 'Sample' ) {
			$sQuery = "
				INSERT INTO `{$this -> sDBTable}` SET
					`Desc`    = '" . addslashes( $aBlock['Desc']    ) . "',
					`Caption` = '" . addslashes( $aBlock['Caption'] ) . "',
					`Func`    = '{$aBlock['Content']}',
					`Visible` = '{$aBlock['Visible']}',
					`Page`    = '{$this -> sPage_db}'
			";
			db_res( $sQuery );

			echo db_last_id();

			$this -> createCache();
		}
	}
	
	function deleteCustomPage($sPageName)
	{
	    $sPageName = process_db_input($sPageName, BX_TAGS_STRIP, BX_SLASHES_AUTO);
	    $sQuery = "DELETE `{$this -> sDBTable}_pages`, `{$this -> sDBTable}` FROM  `{$this -> sDBTable}_pages`
	    	LEFT JOIN `{$this -> sDBTable}` ON `{$this -> sDBTable}`.`Page` = `{$this -> sDBTable}_pages`.`Name`
	    	WHERE `{$this -> sDBTable}_pages`.`Name` = '{$sPageName}'";

	    db_res($sQuery);
	}

	function deleteBlock( $iBlockID ) {
	    $iBlockID = (int) $iBlockID;

		$sQuery = "DELETE FROM `{$this -> sDBTable}` WHERE `Page` = '{$this -> sPage_db}' AND `ID` = '{$iBlockID}'";
		db_res( $sQuery );
	}
	
	function resetPage() {
		if( $this -> oPage -> bResetable ) {
			$sQuery = "DELETE FROM `{$this -> sDBTable}` WHERE `Page` = '{$this -> sPage_db}'";
			db_res($sQuery);
			execSqlFile( $this -> oPage -> sDefaultSqlFile );
			
			if( $this -> oPage -> sName == 'index' ) {
				setParam( 'promoWidth', '960' );
				ResizeAllPromos();
			}
		}
		
		echo (int)$this -> oPage -> bResetable;
	}
	
	function saveItem( $aData ) {
		$iID = (int)$aData['id'];
		
		$sQuery = "SELECT `Func` FROM `{$this -> sDBTable}` WHERE `ID` = $iID";
		$sFunc  = db_value( $sQuery );
		if( !$sFunc )
			return;
		
		$sCaption = process_db_input($aData['Caption'], BX_TAGS_STRIP);
		$sVisible = is_array( $aData['Visible'] ) ? implode( ',', $aData['Visible'] ) : '';
		$iCache = (int)$aData['Cache'] > 0 ? (int)$aData['Cache'] : 0;

		if( $sFunc == 'RSS' )
			$sContentUpd = "`Content` = '" . process_db_input($aData['Url'], BX_TAGS_STRIP) . '#' . (int)$aData['Num'] . "',";
		elseif( $sFunc == 'Echo' )
			$sContentUpd = "`Content` = '" . process_db_input($aData['Content'], BX_TAGS_NO_ACTION) . "',";
			
// Deano - PHP Block Mod - Code Start
elseif( $sFunc == 'PHP' )
$sContentUpd = "`Content` = '" . process_db_input($aData['Content'], BX_TAGS_NO_ACTION) . "',";
// Deano - PHP Block Mod - Code End
			
		elseif( $sFunc == 'XML' ) {
			$iApplicationID = (int)$aData['application_id'];
			$sContentUpd = "`Content` = '" . $iApplicationID . "',";
		} else
			$sContentUpd = '';
		
		$sQuery = "
			UPDATE `{$this -> sDBTable}` SET
				`Caption` = '{$sCaption}',
				{$sContentUpd}
				`Visible` = '{$sVisible}',
                `Cache` = '{$iCache}'
			WHERE `ID` = '{$iID}'
		";
		db_res( $sQuery );
		$sCaption = process_pass_data($aData['Caption']);
                if (mb_strlen($sCaption) == 0)
                    $sCaption = '_Empty';
		echo _t($sCaption);
	}
	
	function saveColsWidths( $aWidths ) {
		$iCounter = 0;
		foreach( $aWidths as $iWidth ) {
			$iCounter ++;
			$iWidth = (float)$iWidth;
			
			$sQuery = "UPDATE `{$this -> sDBTable}` SET `ColWidth` = $iWidth WHERE `Page` = '{$this -> sPage_db}' AND `Column` = $iCounter";
			db_res( $sQuery );
		}
		
		echo 'OK';
	}
	
	function saveBlocks( $aColumns ) {
		//reset blocks on this page
		$sQuery = "UPDATE `{$this -> sDBTable}` SET `Column` = 0, `Order` = 0 WHERE `Page` = '{$this -> sPage_db}'";
		db_res( $sQuery );
		
		$iColCounter = 0;
		foreach( $aColumns as $sBlocks ) {
			$iColCounter ++;
			
			$aBlocks = explode( ',', $sBlocks );
			foreach( $aBlocks as $iOrder => $iBlockID ) {
				$iBlockID = (int)$iBlockID;
				$sQuery = "UPDATE `{$this -> sDBTable}` SET `Column` = $iColCounter, `Order` = $iOrder WHERE `ID` = $iBlockID AND `Page` = '{$this -> sPage_db}'";
				db_res( $sQuery );
			}
		}
		
		echo 'OK';
	}

	function getCssCode() {
		return $GLOBALS['oAdmTemplate']->addCss(array('general.css', 'forms_adv.css'), true);
	}
    
	function showBuildZone() {
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('pbuilder_content.html', array(
            'selector' => $this->getPageSelector(),
            'bx_if:page' => array(
                'condition' => (bool)$this -> oPage,
                'content' => array(
                    'bx_if:delete_link' => array(
                        'condition' => !$this->oPage->isSystem,
                        'content' => array(
                        )
                    ),
                    'bx_if:view_link' => array(
                        'condition' => !$this->oPage->isSystem,
                        'content' => array(
                            'site_url' => $GLOBALS['site']['url'],
                            'page_name' => htmlspecialchars($this->oPage->sName)
                        )
                    ),
                    'parser_url' => bx_html_attribute($_SERVER['PHP_SELF']),
                    'page_name' => addslashes($this->oPage->sName),
                    'page_width_min' => getParam('sys_template_page_width_min'),
                    'page_width_max' => getParam('sys_template_page_width_max'),
                    'page_width' => $this->oPage->iPageWidth,
                    'main_width' => getParam('main_div_width')
                )
            ),
        ));
	}
    
	function getPageSelector() {
        $aPages = array(
            array(
                'value' => 'none', 
                'title' => _t('_adm_txt_pb_select_page'),
                'selected' => empty($this->oPage->sName) ? 'selected="selected"' : ''
            )
        );
        foreach($this->aPages as $iKey => $sPage)
            $aPages[] = array(
                'value' => htmlspecialchars_adv( urlencode($sPage)),
                'title' => htmlspecialchars( isset($this -> aTitles[$sPage]) ? $this -> aTitles[$sPage] : $sPage ),
                'selected' => $this->oPage->sName == $sPage ? 'selected="selected"' : ''
            );
        
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('pbuilder_cpanel.html', array(
            'bx_repeat:pages' => $aPages,
            'url' => bx_html_attribute($_SERVER['PHP_SELF'])
        ));
	}

	function getPages() {
		$sPagesQuery = "SELECT `Name`, `Title` FROM `{$this -> sDBTable}_pages` ORDER BY `Order`";

		$rPages = db_res( $sPagesQuery );
		while( $aPage = mysql_fetch_assoc($rPages) ) {
			$this -> aPages[] = $aPage['Name'];
			$this -> aTitles[$aPage['Name']] = $aPage['Title'];
		}
	}

	function checkAjaxMode() {
		if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' )
			$this -> bAjaxMode = true;
	}

	function showPropForm($iBlockID) {
	
// Deano - PHP Block Mod - Code Start
$sPHPBlockC = _t('_adm_pbuilder_PHP_Block');
$sPHPContentC = _t('_adm_pbuilder_PHP_content');
// Deano - PHP Block Mod - Code End
	
		$sNoPropertiesC = _t('_adm_pbuilder_This_block_has_no_properties');
		$sProfileFieldsC = _t('_adm_pbuilder_Profile_Fields');
		$sHtmlBlockC = _t('_adm_pbuilder_HTML_Block');
		$sXmlBlockC = _t('_adm_pbuilder_XML_Block');
		$sRssBlockC = _t('_adm_pbuilder_RSS_Feed');
		$sSpecialBlockC = _t('_adm_pbuilder_Special_Block');
		$sHtmlContentC = _t('_adm_pbuilder_HTML_content');
		$sXmlPathC = _t('_adm_pbuilder_XML_path');
		$sUrlRssFeedC = _t('_adm_pbuilder_Url_of_RSS_feed');
		$sNumbRssItemsC = _t('_adm_pbuilder_Number_RSS_items');
		$sTypeC = _t('_Type');
		$sDescriptionC = _t('_Description');
		$sCaptionLangKeyC = _t('_adm_pbuilder_Caption_Lang_Key');
		$sVisibleForC = _t('_adm_mbuilder_Visible_for');
		$sGuestC = _t('_Guest');
		$sMemberC = _t('_Member');
        $sCaptionCacheC = _t('_adm_pbuilder_Caption_Cache');

		$sQuery = "SELECT * FROM `{$this -> sDBTable}` WHERE `Page` = '{$this -> sPage_db}' AND `ID` = $iBlockID";
		$aItem = db_assoc_arr($sQuery);
		if(!$aItem)
			return MsgBox($sNoPropertiesC);

		$sPageName = htmlspecialchars($this->oPage->sName);

		$sBlockType = '';
		switch( $aItem['Func'] ) {

case 'PHP': $sBlockType = $sPHPBlockC; break;	// Deano - PHP Block Code Add
		
			case 'PFBlock': $sBlockType = $sProfileFieldsC; break;
			case 'Echo':    $sBlockType =$sHtmlBlockC; break;
			case 'XML':     $sBlockType =$sXmlBlockC; break;
			case 'RSS':     $sBlockType =$sRssBlockC; break;
			default:        $sBlockType =$sSpecialBlockC; break;
		}

		$aVisibleValues = array();
		if(strpos($aItem['Visible'], 'non') !== false) 
            $aVisibleValues[] = 'non';
		if(strpos( $aItem['Visible'], 'memb' ) !== false)
            $aVisibleValues[] = 'memb';

// 		$sDeleteButton = ($aItem['Func'] == 'RSS' or $aItem['Func'] == 'Echo' or $aItem['Func'] == 'XML') ? '<input type="reset" value="Delete" name="Delete" />' : '';
// Deano - PHP Block Mod - Code Start
$sDeleteButton = ($aItem['Func'] == 'RSS' or $aItem['Func'] == 'Echo' or $aItem['Func'] == 'XML' or $aItem['Func'] == 'PHP') ? '<input type="reset" value="Delete" name="Delete" />' : '';		
// Deano - PHP Block Mod - Code End

		$aForm = array(
			'form_attrs' => array(
				'name' => 'formItemEdit',
				'action' => bx_html_attribute($_SERVER['PHP_SELF']),
				'method' => 'post',
			),
			'inputs' => array(
				'Page' => array(
					'type' => 'hidden',
					'name' => 'Page',
					'value' => $sPageName,
				),
				'id' => array(
					'type' => 'hidden',
					'name' => 'id',
					'value' => $iBlockID,
				),
				'action' => array(
					'type' => 'hidden',
					'name' => 'action',
					'value' => 'saveItem',
				),
				'header1' => array(
					'type' => 'block_header',
					'caption' => $sTypeC . ': ' . $sBlockType,
				),
				'header2' => array(
					'type' => 'block_header',
					'caption' => $sDescriptionC . ': ' . $aItem['Desc'],
				),
				'Caption' => array(
					'type' => 'text',
					'name' => 'Caption',
					'caption' => $sCaptionLangKeyC,
					'value' => $aItem['Caption'],
					'required' => true,
				),
				'Visible' => array(
					'type' => 'checkbox_set',
					'caption' => $sVisibleForC,
					'name' => 'Visible',
					'value' => $aVisibleValues,
					'values' => array(
                        'non' => $sGuestC,
                        'memb' => $sMemberC
					)
				),
				'Cache' => array(
					'type' => 'text',
					'name' => 'Cache',
					'caption' => $sCaptionCacheC,
					'value' => (int)$aItem['Cache'],
					'required' => false,
				),				
			),
		);

		$sBlockContent = $aItem['Content'];
		//$sBlockContent = htmlspecialchars_adv( $aItem['Content'] );

		if( $aItem['Func'] == 'Echo' ) {
			$aForm['inputs']['Content'] = array(
                'type' => 'textarea',
                'html' => false,
                'attrs' => array ('id' => 'form_input_html'.$iBlockID),
				'name' => 'Content',
				'value' => $sBlockContent,
				'caption' => $sHtmlContentC,
                'required' => true,
                'colspan' => true,
			);
			
// Deano - PHP Block Mod - Code Start
} elseif( $aItem['Func'] == 'PHP' ) {
$aForm['inputs']['Content'] = array(
'type' => 'textarea',
'html' => false,
'name' => 'Content',
'value' => $sBlockContent,
'caption' => $sPHPContentC,
'required' => true,
);
// Deano - PHP Block Mod - Code End
			
		} elseif( $aItem['Func'] == 'XML' ) {
			$aExistedApplications = BxDolService::call('open_social', 'get_admin_applications', array());

			$aForm['inputs']['Applications'] = array(
				'type' => 'select',
				'name' => 'application_id',
				'caption' => _t('_osi_Existed_applications'),
				'values' => $aExistedApplications
			);
		} elseif( $aItem['Func'] == 'RSS' ) {
			list( $sUrl, $iNum ) = explode( '#', $aItem['Content'] );
			$iNum = (int)$iNum;

			$aForm['inputs']['Url'] = array(
				'type' => 'text',
				'name' => 'Url',
				'caption' => $sUrlRssFeedC,
				'value' => $sUrl,
				'required' => true,
			);
			$aForm['inputs']['Num'] = array(
				'type' => 'text',
				'name' => 'Num',
				'caption' => $sNumbRssItemsC,
				'value' => $iNum,
				'required' => true,
			);
		}

		$aForm['inputs']['Save'] = array(
			'type' => 'submit',
			'name' => 'Save',
			'caption' => _t('_Save'),
			'value' => _t('_Save'),
		);

// 		if ($aItem['Func'] == 'RSS' or $aItem['Func'] == 'Echo' or $aItem['Func'] == 'XML') {
// 			$aForm['inputs']['Delete'] = array(
// 				'type' => 'reset',
// 				'name' => 'Delete',
// 				'caption' => 'Delete',
// 				'value' => 'Delete',
// 			);
// 		}

// Deano - PHP Block Mod - Code Start
if ($aItem['Func'] == 'RSS' or $aItem['Func'] == 'Echo' or $aItem['Func'] == 'XML' or $aItem['Func'] == 'PHP') {
$aForm['inputs']['Delete'] = array(
'type' => 'reset',
'name' => 'Delete',
'caption' => 'Delete',
'value' => 'Delete',
);
}
// Deano - PHP Block Mod - Code End

		$sResult = '';
		$oForm = new BxTemplFormView($aForm);

		$aVariables = array (
			'caption' => _t('_adm_pbuilder_Block'),
			'main_content' => $oForm->getCode(),
			'loading' => LoadingBox('adm-pbuilder-loading')
		);
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('popup_form_wrapper.html', $aVariables);
	}
    
    function showNewPageForm() {
		$oForm = new BxTemplFormView(array(
			'form_attrs' => array(
				'name' => 'formItemEdit',
				'action' => bx_html_attribute($_SERVER['PHP_SELF']),
				'method' => 'post',
			),
            'inputs' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'action_sys',
                    'value' => 'createNewPage',
                ),
                array(
                    'type' => 'text',
                    'name' => 'uri',
                    'value' => 'newpage',
                    'caption' => _t('_Page URI'),
                    'info' => _t('_adm_pbuilder_uri_info', BX_DOL_URL_ROOT . 'page/newpage'),
                ),
                array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_Page title'),
                    'value' => 'New Page',
                    'info' => _t('_adm_pbuilder_title_info'),
                ),
                array(
                    'type' => 'submit',
                    'name' => 'do_submit',
                    'value' => _t('_adm_btn_Create_page'),
                ),
            ),
        ));
        
		$aVariables = array(
			'caption' => _t('_adm_pbuilder_Create_new_page'),
			'main_content' => $oForm->getCode(),
			'loading' => LoadingBox('adm-pbuilder-loading')
		);
        return $GLOBALS['oAdmTemplate']->parseHtmlByName('popup_form_wrapper.html', $aVariables);
    }
}

class BxDolPVAPage {
	var $sName;
	var $sName_db;
	var $oParent;
	var $aColsWidths     = array();
	var $aBlocks         = array();
    var $aBlocksOrder    = array();
	var $aBlocksInactive = array();
	var $aBlocksSamples  = array();
	var $aMinWidths      = array();
	var $iPageWidth;
	var $bResetable; //defines if the page can be reset
	var $sDefaultSqlFile; //file containing default setting for reset
    var $isSystem; // defines if the page is system or created by admin
	
	function BxDolPVAPage( $sPage, &$oParent ) {
		global $admin_dir;
		
		$this -> sName   = $sPage;
		$this -> sName_db = addslashes( $this -> sName );
		
		/* @var $this->oParent BxDolPageViewAdmin */
		$this -> oParent = &$oParent;
		
		$this -> sDefaultSqlFile = BX_DIRECTORY_PATH_ROOT . "{$admin_dir}/default_builders/{$this -> oParent -> sDBTable}_{$this -> sName}.sql";
		$this -> bResetable = file_exists( $this -> sDefaultSqlFile );
				
		$this -> loadContent();
	}
	
	function loadContent() {
		
        $sQuery = "select `System` from `{$this -> oParent -> sDBTable}_pages` where `Name` = '{$this -> sName_db}'";
        $this->isSystem = (bool)(int)db_value($sQuery);
        
        //get page width
        $sQuery = "SELECT `PageWidth` FROM `{$this -> oParent -> sDBTable}` WHERE `Page` = '{$this -> sName_db}' LIMIT 1";
        $this -> iPageWidth = db_value( $sQuery );
        
        if (!$this -> iPageWidth)
            $this -> iPageWidth = '960px';
        
        //get columns widths
        $sQuery = "
            SELECT
                `Column`,
                `ColWidth`
            FROM `{$this -> oParent -> sDBTable}`
            WHERE
                `Page` = '{$this -> sName_db}' AND
                `Column` != 0
            GROUP BY `Column`
            ORDER BY `Column`
        ";
        $rColumns = db_res( $sQuery );
        while( $aColumn = mysql_fetch_assoc( $rColumns ) ) {
            $iColumn                       = (int)$aColumn['Column'];
            $this -> aColsWidths[$iColumn] = (float)$aColumn['ColWidth'];
            $this -> aBlocks[$iColumn]     = array();
            $this -> aBlocksOrder[$iColumn]= array();
            
            //get active blocks
            $sQueryActive = "
                SELECT
                    `ID`,
                    `Caption`
                FROM `{$this -> oParent -> sDBTable}`
                WHERE
                    `Page` = '{$this -> sName_db}' AND
                    `Column` = $iColumn
                ORDER BY `Order`
                ";
            
            $rBlocks = db_res( $sQueryActive );
            
            while( $aBlock  = mysql_fetch_assoc( $rBlocks ) ) {
                $this -> aBlocks[$iColumn][ $aBlock['ID'] ] = _t( $aBlock['Caption'] );
                $this -> aBlocksOrder[$iColumn][] = $aBlock['ID'];
            }
        }
        
        // load minimal widths
        $sQuery = "SELECT `ID`, `MinWidth` FROM `{$this -> oParent -> sDBTable}` WHERE `MinWidth` > 0 AND `Page`= '{$this -> sName_db}'";
        $rBlocks = db_res( $sQuery );
        while( $aBlock = mysql_fetch_assoc( $rBlocks ) )
            $this -> aMinWidths[ (int)$aBlock['ID'] ] = (int)$aBlock['MinWidth'];
        
        $this -> loadInactiveBlocks();
	}
	
	function loadInactiveBlocks() {
		//get inactive blocks and samples
		$sQueryInactive = "
			SELECT
				`ID`,
				`Caption`
			FROM `{$this -> oParent -> sDBTable}`
			WHERE
				`Page` = '{$this -> sName_db}' AND
				`Column` = 0
		";
		
		$sQuerySamples = "
			SELECT
				`ID`,
				`Caption`
			FROM `{$this -> oParent -> sDBTable}`
			WHERE
				`Func` = 'Sample'
		";
		
		$rInactive = db_res( $sQueryInactive );
		$rSamples  = db_res( $sQuerySamples );
		
		while( $aBlock = mysql_fetch_assoc( $rInactive ) )
			$this -> aBlocksInactive[ (int)$aBlock['ID'] ] = _t( $aBlock['Caption'] );
		
		while( $aBlock = mysql_fetch_assoc( $rSamples ) )
			$this -> aBlocksSamples[ (int)$aBlock['ID'] ] = _t( $aBlock['Caption'] );
	}
	
	function getJSON() {
		$oPVAPageJSON = new BxDolPVAPageJSON( $this );
		$oJson = new Services_JSON();
		return $oJson -> encode($oPVAPageJSON);
	}
	
}

/* temporary JSON object */
class BxDolPVAPageJSON {
	var $active;
    var $active_order;
	var $inactive;
	var $samples;
	var $widths;
	var $min_widths;
	
	function BxDolPVAPageJSON( $oParent ) {
		$this -> widths     = $oParent -> aColsWidths;
		$this -> min_widths = $oParent -> aMinWidths;
		$this -> active     = $oParent -> aBlocks;
        $this -> active_order = $oParent -> aBlocksOrder;
		$this -> inactive   = $oParent -> aBlocksInactive;
		$this -> samples    = $oParent -> aBlocksSamples;
	}
}


class BxDolPageViewCacher {
	var $sCacheFile;
    var $oBlocksCacheObject;
	
	function BxDolPageViewCacher( $sDBTable, $sCacheFile ) {
		$this -> sDBTable = $sDBTable;
		$this -> sCacheFile = $sCacheFile;
	}
	
	function createCache() {
		
        $oCacheBlocks = $this->getBlocksCacheObject ();
        $oCacheBlocks->removeAllByPrefix ('pb_');

		$sCacheString = "// cache of Page View composer\n\nreturn array(\n  //pages\n";
		
		//get pages
       
		$sQuery = "SELECT `Page` AS `Name` FROM `{$this -> sDBTable}` WHERE `Page` != '' GROUP BY `Page`"; 
        
		$rPages = db_res( $sQuery );
		
		while ($aPageN = mysql_fetch_assoc($rPages)) {			
			$sPageName  = addslashes($aPageN['Name']);
			$aPageN['Title'] = db_value("SELECT `Title` FROM `{$this -> sDBTable}_pages` WHERE `Name` = '$sPageName'");
			$sPageTitle = addslashes($aPageN['Title']);
            $sPageWidth = db_value("SELECT `PageWidth` FROM `{$this -> sDBTable}` WHERE `Page` = '$sPageName' LIMIT 1");
            $sPageWidth = empty($sPageWidth) ? '998px' : $sPageWidth;
            
			$sCacheString .= "  '$sPageName' => array(\n";
			$sCacheString .= "    'Title' => '$sPageTitle',\n";
			$sCacheString .= "    'Width' => '$sPageWidth',\n";
			$sCacheString .= "    'Columns' => array(\n";
			
			//get columns
			$sQuery = "
				SELECT
					`Column`,
					`ColWidth`
				FROM `{$this -> sDBTable}`
				WHERE
					`Page` = '$sPageName' AND
					`Column` > 0
				GROUP BY `Column`
				ORDER BY `Column`
			";
			$rColumns = db_res( $sQuery );
			
			while( $aColumn = mysql_fetch_assoc( $rColumns ) ) {
				$iColumn = $aColumn['Column'];
				$iColWidth  = $aColumn['ColWidth'];
				
				$sCacheString .= "      $iColumn => array(\n";
				$sCacheString .= "        'Width'  => $iColWidth,\n";
				$sCacheString .= "        'Blocks' => array(\n";
				
				//get blocks of column
				$sQuery = "
					SELECT
						`ID`,
						`Caption`,
						`Func`,
						`Content`,
						`DesignBox`,
						`Visible`,
                        `Cache`
					FROM `{$this -> sDBTable}`
					WHERE
						`Page` = '$sPageName' AND
						`Column` = $iColumn
					ORDER BY `Order` ASC
				";
				$rBlocks = db_res( $sQuery );
				
				while( $aBlock = mysql_fetch_assoc( $rBlocks ) ) {
					$sCacheString .= "          {$aBlock['ID']} => array(\n";
					
					$sCacheString .= "            'Func'      => '{$aBlock['Func']}',\n";
					$sCacheString .= "            'Content'   => '" . $this -> addSlashes( $aBlock['Content'] ) . "',\n";
					$sCacheString .= "            'Caption'   => '" . $this -> addSlashes( $aBlock['Caption'] ) . "',\n";
					$sCacheString .= "            'Visible'   => '{$aBlock['Visible']}',\n";
					$sCacheString .= "            'DesignBox' => {$aBlock['DesignBox']},\n";
                    $sCacheString .= "            'Cache'     => {$aBlock['Cache']}\n";
					
					$sCacheString .= "          ),\n"; //close block
				}
				$sCacheString .= "        )\n"; //close blocks
				$sCacheString .= "      ),\n"; //close column
			}
			
			$sCacheString .= "    )\n"; //close columns
			$sCacheString .= "  ),\n"; //close page
		}
		
		$sCacheString .= ");\n"; //close main array
		
		
        $aResult = eval($sCacheString);

        $oCache = $GLOBALS['MySQL']->getDbCacheObject();
        $oCache->setData ($GLOBALS['MySQL']->genDbCacheKey($this -> sCacheFile), $aResult);

		return true;
	}
	
	function addSlashes( $sText ) {
		$sText = str_replace( '\\', '\\\\', $sText );
		$sText = str_replace( '\'', '\\\'', $sText );
		
		return $sText;
	}

    function getBlocksCacheObject () {
        if ($this->oBlocksCacheObject != null) {
            return $this->oBlocksCacheObject;
        } else {
            $sEngine = getParam('sys_pb_cache_engine');  
            $this->oBlocksCacheObject = bx_instance ('BxDolCache'.$sEngine);
            if (!$this->oBlocksCacheObject->isAvailable())
                $this->oBlocksCacheObject = bx_instance ('BxDolCacheFile');
            return $this->oBlocksCacheObject;
        }
    }

    function genBlocksCacheKey ($sId) {
        global $site;
        return 'pb_' . $sId . '_' . md5($site['ver'] . $site['build'] . $site['url'] . getCurrentLangName(false) . $GLOBALS['oSysTemplate']->getCode()) . '.php';
    }
}

?>
