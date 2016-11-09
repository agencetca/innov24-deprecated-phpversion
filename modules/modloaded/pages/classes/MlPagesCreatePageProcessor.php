<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/
ini_set("memory_limit","120M");
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );


bx_import('BxTemplFormView');

require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesPageFields.php');
require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesController.php');
class MlPagesCreatePageProcessor {
    
    var $oPF; //profile fields
    var $iPage; //currently shown page
    var $aPages; //available pages
    var $aValues; //inputted values
    var $aErrors; //errors generated on page
    var $bAjaxMode; // defines if the script were requested by ajax
    
    var $bCoupleEnabled;
    var $bCouple;
    
    function MlPagesCreatePageProcessor() {
        $this -> aErrors = array( 0 => array(), 1 => array() ); 
        
        $this -> oPF = new MlPagesPageFields(1);
		
		$this -> aValues = array();
		$this -> aValues[0] = $this -> aValues[1] = $this -> oPF -> getDefaultValues();// double arrays (for couples)
        $this -> bAjaxMode = ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' );

				$this->_iProfileId = $GLOBALS['logged']['member'] || $GLOBALS['logged']['admin'] ? $_COOKIE['memberID'] : 0;
    }
    
    function process() {
        if( !$this -> oPF -> aArea )
            return 'Profile Fields cache not loaded. Cannot continue.';
        
        $this -> aPages = array_keys( $this -> oPF -> aArea );
        
        $this -> iPage = ( isset( $_POST['join_page'] ) ) ? $_POST['join_page'] : 0; // get current working page from POST
        //$this -> iPage == 'done';
        if( $this -> iPage !== 'done' )
            $this -> iPage = (int)$this -> iPage;
        
        $this -> getCoupleOptions();
       
        $this -> processPostValues();

        //if( $this -> bAjaxMode) {
        //if( $_GET['ajax']) {echo $this -> iPage;
        if($this -> bAjaxMode) {
            $this -> showErrorsJson();
            exit;
        } else {
            ob_start();
            if( $this -> iPage === 'done' ) { //if all pages are finished and no errors found
                list( $iEntryId, $sEntryUri, $sTitle, $sStatus) = $this -> registerPage();
                
                if( !$iEntryId )
                    $this -> showFailPage();
                else
                    $this -> showFinishPage( $iEntryId, $sEntryUri, $sTitle, $sStatus );
            } else 
              $this -> showJoinForm();
            
            return ob_get_clean();
        }
    }
    
    function getCoupleOptions() {
        //find Couple item (check if it is active)
        $aCoupleItem = false;
        foreach ($this -> aPages as $iPageInd => $iPage) { //cycle pages
            $aBlocks = $this -> oPF -> aArea[ $iPage ];
            foreach ($aBlocks as $iBlockID => $aBlock) {   //cycle blocks
                $aItems = $aBlock['Items'];
                foreach ($aItems as $iItemID => $aItem) {  //cycle items
                    if( $aItem['Name'] == 'Couple' ) { // we found it!
                        $aCoupleItem = $aItem;
                        break;
                    }
                }
                
                if( $aCoupleItem ) // we already found it
                    break;
            }
            
            if( $aCoupleItem ) // we already found it
                break;
        }
        
        if( $aCoupleItem ) {
            $this -> bCoupleEnabled      = true;
            $this -> bCouple             = ( isset( $_REQUEST['Couple'] ) and $_REQUEST['Couple'] == 'yes' ) ? true : false;
        } else {
            $this -> bCoupleEnabled      = false;
            $this -> bCouple             = false;
        }
    }
    
    function processPostValues() {
        foreach ($this -> aPages as $iPage) { //cycle pages

           if( $this -> iPage !== 'done' and $iPage >= $this -> iPage ) {
                $this -> iPage = $iPage; // we are on the current page. dont process these values, dont go further, just show form.
                break;
            }
            // process post values by Profile Fields class
            $this -> oPF -> processPostValues( $this -> bCouple, $this -> aValues, $this -> aErrors, $iPage );
            if( !empty( $this -> aErrors[0] ) or ( $this -> bCouple and !empty( $this -> aErrors[1] ) ) ) { //we found errors on previous page
                // do not process further values, just go to erroneous page.
                $this -> iPage = $iPage;
                break;
            }
        }
    }
    
    function showErrorsJson() {
        header('Content-Type:text/javascript');
        echo $this -> oPF -> genJsonErrors( $this -> aErrors, $this -> bCouple );
    }
    
    function showJoinForm() {
        $aJoinFormParams = array(
            'couple_enabled' => $this->bCoupleEnabled,
            'couple'         => $this->bCouple,
            'page'           => $this->iPage,
            'hiddens'        => $this->genHiddenFieldsArray(),
            'errors'         => $this->aErrors,
            'values'         => $this->aValues,
        );
        //echoDbg($this -> oPF);
        echo $this->oPF->getFormCode($aJoinFormParams);
    }
    
    function genHiddenFieldsArray() {
        $aHiddenFields = array();
        
        //retrieve next page
        $iPageInd = (int)array_search( $this -> iPage, $this -> aPages );
        $iNextInd = $iPageInd + 1;
        
        if( array_key_exists( $iNextInd, $this -> aPages ) )
            $sNextPage = $this -> aPages[ $iNextInd ];
        else
            $sNextPage = 'done';
        
        // insert next page
        $aHiddenFields['join_page'] = $sNextPage;
        
        //echoDbg( $this -> aValues );
        
        // insert entered values
        $iHumans = $this -> bCouple ? 2 : 1;
        for( $iHuman = 0; $iHuman < $iHumans; $iHuman ++ ) {
            foreach( $this -> aPages as $iPage ) {
                if( $iPage == $this -> iPage )
                    break; // we are on this page
                
                $aBlocks = $this -> oPF -> aArea[ $iPage ];
                foreach( $aBlocks as $aBlock ) {
                    foreach( $aBlock['Items'] as $aItem ) {
                        $sItemName = $aItem['Name'];
                        
                        if( isset( $this -> aValues[$iHuman][ $sItemName ] ) ) {
                            $mValue = $this -> aValues[$iHuman][ $sItemName ];
                            
                            switch( $aItem['Type'] ) {
                                case 'pass':
                                    $aHiddenFields[ $sItemName . '_confirm[' . $iHuman . ']' ] = $mValue;
                                case 'text':
                                case 'area':
                                case 'html_area':
                                case 'date':
                                case 'datetime':
                                case 'select_one':
                                case 'num':
                                    $aHiddenFields[ $sItemName . '[' . $iHuman . ']' ] = $mValue;
                                break;
                                
                                case 'select_set':
                                    foreach( $mValue as $iInd => $sValue )
                                        $aHiddenFields[ $sItemName . '[' . $iHuman . '][' . $iInd . ']' ] = $sValue;
                                break;
                                
                                case 'range':
                                    $aHiddenFields[ $sItemName . '[' . $iHuman . '][0]' ] = $mValue[0];
                                    $aHiddenFields[ $sItemName . '[' . $iHuman . '][1]' ] = $mValue[1];
                                break;
                                
                                case 'bool':
                                    $aHiddenFields[ $sItemName . '[' . $iHuman . ']' ] = $mValue ? 'yes' : '';
                                break;
                                
                                case 'system':
                                    switch( $aItem['Name'] ) {
                                        case 'Couple':
                                        case 'TermsOfUse':
                                            $aHiddenFields[ $sItemName ] = $mValue ? 'yes' : '';
                                        break;
                                        
                                        case 'Captcha':
                                            $aHiddenFields[ $sItemName ] = $mValue;
                                        break;
                                        
                                        case 'thumb':
                                            $aHiddenFields['thumb_tmp'] = $mValue;
                                        break;
                                    }
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $aHiddenFields;
    }
		function looper()
		{
		    $path = $GLOBALS['dir']['tmp'];
		    $dir_handle = @opendir($path) or die("Unable to open $path");
		    while ($file = readdir($dir_handle)) {
		
		    if($file == "." || $file == ".." || $file == "index.php" )
		        continue;
		        $sret = "<a href=\"$file\">". $GLOBALS['dir']['tmp'] . $file."</a>-".filesize($GLOBALS['dir']['tmp'] . $file)."<br />";
		    }
		    closedir($dir_handle);
		  return;
		}      
    function registerPage() {
        $oPC = new MlPagesController();
        $sDelimeter = getParam('ml_pages_multi_divider');
        $sDelimeter = $sDelimeter ? $sDelimeter : ';';
        $aMedia = array();    	    
        list($aPage1, $aMedia) = $this->oPF->getPageFromValues($this->aValues[0]);

        list($iId1, $sEntryUri, $sStatus, $aPage1['title']) = $oPC->createPage($aPage1);

        //--- check whether profile was created successfully or not
        if(!$iId1) {
            if(isset($aPage1['thumb']) && !empty($aPage1['thumb']))
                @unlink($GLOBALS['dir']['tmp'] . $aPage1['thumb']);

            return array(false, 'Fail');
        }
        else
        {
	        if(isset($aPage1['thumb']) && !empty($aPage1['thumb'])) 
	        {
	          if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader')) {
	              $aFileInfo = array (
	              		'medTitle' => $aPage1['title'],
	              		'Categories' => array($aPage1['title'] . ' ' . _t('_bx_photos')),
	                  'album' => _t('_ml_pages_photo_album', $aPage1['title']),
	              );
	              $this->looper();
	              $iPhotoId = BxDolService::call('photos', 'perform_photo_upload', array($GLOBALS['dir']['tmp'] . $aPage1['thumb'], $aFileInfo, false), 'Uploader');
	          		if ($iPhotoId)
	          		{
	          			if (db_value("SHOW TABLES LIKE 'ml_pages_images'"))
	          				db_res("INSERT INTO `ml_pages_imagesimages` SET `entry_id` = {$iId1}, `media_id` = {$iPhotoId}");
	          			db_res("UPDATE `ml_pages_main` SET `thumb` = {$iPhotoId} WHERE `id` = {$iId1} LIMIT 1");
	          		}	
	          }
	        }
	        if (!empty($aMedia))
	        {
		        foreach ($aMedia as $sKey => $sValue)
		        {
		        	$aMediaData = explode($sDelimeter, $aPage1[$sValue]);
		      		switch($sKey)
		      		{
		      			case 'photo':
		      				$sTable = 'ml_pages_images';
		      				$iIdField = 'entry_id';
		      				$iMedIdField = 'media_id';
		      				if (BxDolRequest::serviceExists("photos", "perform_photo_upload", 'Uploader'))
		      				{
		      					$i = 0;
		      					foreach($aMediaData as $sData)
		      					{
				              $aInfo = array (
				              		'medTitle' => $_POST[$sValue . '_title_photos'][$i] ? $_POST[$sValue . '_title_photos'][$i] : $_FILES[$sValue]['name'][$i],
				              		'Categories' => array($aPage1['title'] . ' ' . _t("_bx_{$sKey}s")),
				              		'album' => _t('_ml_pages_photo_album', $aPage1['title']),
				              );
				              $this->looper();
				              $iMediaId = BxDolService::call("photos", "perform_photo_upload", array($GLOBALS['dir']['tmp'] . $sData, $aInfo, false), 'Uploader');
				            	if ($iMediaId)
				            	{
				            		@unlink($GLOBALS['dir']['tmp'] . $sData);
				          			if (db_value("SHOW TABLES LIKE '{$sTable}'"))
				          				db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$iId1}, `{$iMedIdField}` = {$iMediaId}");
				          			if (!db_value("SELECT `thumb` FROM `ml_pages_main` WHERE `id` = {$iId1} LIMIT 1") && $i == 0)
				          				db_res("UPDATE `ml_pages_main` SET `thumb` = {$iMediaId} WHERE `id` = {$iId1} LIMIT 1");
				            	}
				            	$i++;
				            }
		      				}
		      			break;
		      			case 'video':
		      				$sTable = 'ml_pages_videos';
		      				$iIdField = 'entry_id';
		      				$iMedIdField = 'media_id';
		      				if (BxDolRequest::serviceExists("videos", "perform_video_upload", 'Uploader'))
		      				{
		      					$i = 0;
		      					foreach($aMediaData as $sData)
		      					{
				              $aInfo = array (
				              		'medTitle' => $_POST[$sValue . '_title_videos'][$i] ? $_POST[$sValue . '_title_videos'][$i] : $_FILES[$sValue]['name'][$i],
				              		'Categories' => array($aPage1['title'] . ' ' . _t("_bx_{$sKey}s")),
				              		'album' => _t('_ml_pages_photo_album', $aPage1['title']),
				              );
				              $this->looper();
				              $iMediaId = BxDolService::call("videos", "perform_video_upload", array($GLOBALS['dir']['tmp'] . $sData, $aInfo, false), 'Uploader');
				            	if ($iMediaId)
				            	{
				            		@unlink($GLOBALS['dir']['tmp'] . $sData);
				          			if (db_value("SHOW TABLES LIKE '{$sTable}'"))
				          				db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$iId1}, `{$iMedIdField}` = {$iMediaId}");
				            	}
				            	$i++;
				            }
		      				}
		      			break;
		      			case 'sound':
		      				$sTable = 'ml_pages_sounds';
		      				$iIdField = 'entry_id';
		      				$iMedIdField = 'media_id';
		      				if (BxDolRequest::serviceExists("sounds", "perform_music_upload", 'Uploader'))
		      				{
		      					$i = 0;
		      					foreach($aMediaData as $sData)
		      					{
				              $aInfo = array (
				              		'medTitle' => $_POST[$sValue . '_title_sounds'][$i] ? $_POST[$sValue . '_title_sounds'][$i] : $_FILES[$sValue]['name'][$i],
				              		'Categories' => array($aPage1['title'] . ' ' . _t("_bx_{$sKey}s")),
				              		'album' => _t('_ml_pages_photo_album', $aPage1['title']),
				              );
				              $this->looper();
				              $iMediaId = BxDolService::call("sounds", "perform_music_upload", array($GLOBALS['dir']['tmp'] . $sData, $aInfo, false), 'Uploader');
				            	if ($iMediaId)
				            	{
				            		@unlink($GLOBALS['dir']['tmp'] . $sData);
				          			if (db_value("SHOW TABLES LIKE '{$sTable}'"))
				          				db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$iId1}, `{$iMedIdField}` = {$iMediaId}");
				            	}
				            	$i++;
				            }
		      				}
		      			break;
		      			case 'file':
		      				$sTable = 'ml_pages_files';
		      				$iIdField = 'entry_id';
		      				$iMedIdField = 'media_id';
		      				if (BxDolRequest::serviceExists("files", "perform_file_upload", 'Uploader'))
		      				{
		      					$i = 0;
		      					foreach($aMediaData as $sData)
		      					{
				              $aInfo = array (
				              		'medTitle' => $_POST[$sValue . '_title_files'][$i] ? $_POST[$sValue . '_title_files'][$i] : $_FILES[$sValue]['name'][$i],
				              		'Categories' => array($aPage1['title'] . ' ' . _t("_bx_{$sKey}s")),
				              		'album' => _t('_ml_pages_photo_album', $aPage1['title']),
				              );
				              $this->looper();
				              $iMediaId = BxDolService::call("files", "perform_file_upload", array($GLOBALS['dir']['tmp'] . $sData, $aInfo, false), 'Uploader');
				            	if ($iMediaId)
				            	{
				            		@unlink($GLOBALS['dir']['tmp'] . $sData);
				          			if (db_value("SHOW TABLES LIKE '{$sTable}'"))
				          				db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$iId1}, `{$iMedIdField}` = {$iMediaId}");
				            	}
				            	$i++;
				            }
		      				}
		      			break;
		      			case 'youtube':
		      				$i = 0;
							    $sTable = 'ml_pages_youtube';
							    $iIdField = 'id_entry';
							    $sUrlField = 'url';
							    $sTitleField = 'title';
		    					foreach($aMediaData as $sData)
		    					{
			      				$sTitle = $_POST[$sValue . '_title_youtube'][$i];
				          	if (db_value("SHOW TABLES LIKE '{$sTable}'") && $sData)
				          		db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$iId1}, `{$sUrlField}` = '{$sData}', `{$sTitleField}` = '{$sTitle}'");
		    						$i++;
		    					}
		      			break;
		      			case 'rss':
		      				$i = 0;
							    $sTable = 'ml_pages_rss';
							    $iIdField = 'id_entry';
							    $sUrlField = 'url';
							    $sTitleField = 'name';
		    					foreach($aMediaData as $sData)
		    					{
			      				$sTitle = $_POST[$sValue . '_title_rss'][$i];
				          	if (db_value("SHOW TABLES LIKE '{$sTable}'") && $sData)
				          		db_res("INSERT INTO `{$sTable}` SET `{$iIdField}` = {$iId1}, `{$sUrlField}` = '{$sData}', `{$sTitleField}` = '{$sTitle}'");
		    						$i++;
		    					}
		      			break;
		      		}
		        }
	      	}	        
         }
       return array($iId1, $sEntryUri, $aPage1['title'], $sStatus);
    }
    
    function showFailPage() {
        echo '<div class="dbContentHtml">';
            echo _t( '_Join failed' );
        echo '</div>';
    }

    function _getEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        if (!$isAdmin) 
            $sWhere = " AND `{$this->_sFieldAuthorId}` = '$iOwner' ";
        return $this->getRow ("SELECT * FROM `ml_pages_main` WHERE `id` = $iId LIMIT 1");
    }
        
    function showFinishPage( $iEntryId, $sUri, $sTitle, $sStatus ) {
				global $site;

            if ($iEntryId) {
            	if ($sStatus == 'approved')
            	{
	        			bx_import('BxDolTags');
				        $o = new BxDolTags ();
				        $o->reparseObjTags('ml_pages', $iEntryId);
				        bx_import('BxDolCategories');
				        $o = new BxDolCategories ();
				        $o->reparseObjTags('ml_pages', $iEntryId);
				      }
			      	$sTitle = addslashes($sTitle);
			      	echo "INSERT INTO `ml_pages_forum` SET `forum_uri` = '{$sUri}', `cat_id` = 1, `forum_title` = '{$sTitle}', `forum_desc` = '".getNickName($this->_iProfileId)."', `forum_last` = UNIX_TIMESTAMP(), `forum_type` = 'public', `entry_id` = '{$iEntryId}'";
							db_res ("INSERT INTO `ml_pages_forum` SET `forum_uri` = '{$sUri}', `cat_id` = 1, `forum_title` = '{$sTitle}', `forum_desc` = '".getNickName($this->_iProfileId)."', `forum_last` = UNIX_TIMESTAMP(), `forum_type` = 'public', `entry_id` = '{$iEntryId}'");
							header("location:{$site['url']}m/pages/view/{$sUri}");
							exit;
            }
            


    }
}
?>
