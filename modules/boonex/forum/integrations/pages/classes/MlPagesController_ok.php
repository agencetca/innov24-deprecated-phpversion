<?php

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolEmailTemplates.php' );
require_once(BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesPageFields.php');

class MlPagesController {

	var $oPF;
	var $aItems;
	var $oEmailTemplate;

	function MlPagesController() {
		$this -> oEmailTemplate = new BxDolEmailTemplates();
	}
	
	function createPage( $aData, $bSendMails = true, $iMainMemberID = 0 ) {
		if( !$aData or !is_array($aData) or empty($aData) )
			return false;
		
		unset( $aData['Couple'] );
		unset( $aData['Captcha'] );
		unset( $aData['TermsOfUse'] );
		unset( $aData['PrimPhoto'] );

		/* @var $this->oPF BxDolProfileFields */ $this -> oPF = new 
		MlPagesPageFields(100);
		
		if( !$this -> oPF -> aArea ) {
			echo 'Profile Fields cache not loaded. Cannot continue.';
			return false;
		}
		
		$this -> aItems = $this -> oPF -> aArea[0]['Items'];

		if( $iMainMemberID )
			$aMainMember = $this -> getPageInfo( $iMainMemberID );
		else
			$aMainMember = false;
		
		// begin profile info collecting
		$aNewPage = array();
		$aPhotoFields = array();
		foreach( $this -> aItems as $aItem ) {
			$sItemName = $aItem['Name'];


			if( array_key_exists( $sItemName, $aData )) {
				$aNewPage[$sItemName] = $aData[$sItemName];
				if ($aData[$sItemName . '_photos'] && $aData[$sItemName])
					$aPhotoFields[$sItemName] = $aData[$sItemName . '_photos'];
			} elseif( $aMainMember and array_key_exists( $sItemName, $aMainMember ) and $aItem['Type'] != 'system' ) {
				if( $aItem['Unique'] )
					$aNewPage[$sItemName] = $this -> genUniqueValue( $sItemName, $aMainMember[$sItemName] );
				else
					$aNewPage[$sItemName] = $aMainMember[$sItemName];
			} else {
				switch( $aItem['Type'] ) {
					case 'pass':
						$aNewPage[$sItemName] = $this -> genRandomPassword();
					break;
					
					case 'num':
						$aNewPage[$sItemName] = (int)$aItem['Default'];
					break;
					
					case 'bool':
						$aNewPage[$sItemName] = (bool)$aItem['Default'];
					break;
					
					case 'system':
						switch( $sItemName ) {
							case 'ID': //set automatically
							case 'Captcha': //not been inserted
							case 'Location': //not been inserted
							case 'Keyword': //not been inserted
							case 'TermsOfUse': //not been inserted
								//pass
							break;
							
							case 'Date':
								$aNewPage[$sItemName] = time(); // set current date
							break;
							
							case 'DateLastEdit':
							case 'DateLastLogin':
								$aNewPage[$sItemName] = '0000-00-00';
							break;
							
							case 'Couple':
								$aNewPage[$sItemName] = $aMainMember ? $iMainMemberID : 0; //if main member exists, set him as a couple link
							break;
							
							case 'Featured':
								$aNewPage[$sItemName] = false;
							break;
							
							case 'Status':
								$aNewPage[$sItemName] = 'approved';
							break;
						}
					break;
					
					default:
						$aNewPage[$sItemName] = $aItem['Default'];
				}
			}
		} //we completed collecting
		$sEntryUri = uriGenerate($aNewPage['Title'], 'ml_pages_main', 'EntryUri');
		$sSet = $this -> collectSetString( $aNewPage );
		$sQuery = "INSERT INTO `ml_pages_main` SET \n$sSet";

		$rRes = db_res( $sQuery );
        
        if( $rRes ) {
			$iNewID = db_last_id();
			$this -> createPageCache( $iNewID );
			$sStatus = getParam('ml_pages_autoapproval') == 'on' || isAdmin() ? 'approved' : 'pending';
			$this -> updatePage($iNewID, array(
                'ResponsibleID' => getLoggedId(),
                'SubCategory' => bx_get('sub_category'),
                'MainCategory' => bx_get('main_category'),
                'Categories' => db_value("SELECT `Name` FROM `ml_pages_categories` WHERE `ID`=".bx_get('sub_category')." LIMIT 1"),
                'EntryUri' => $sEntryUri,
                'Status' => $sStatus
            ));
            			
			return array( $iNewID, $sEntryUri, $sStatus, $aPhotoFields );
		} else
			return array( false, 'Failed', 0 );
	}
	function getPageInfoDirect ($iPageID) {
	    return $GLOBALS['MySQL']->getRow("SELECT * FROM `ml_pages_main` WHERE `ID`='" . $iPageID . "' LIMIT 1");
	}
	function createPageDataFile( $iPageID ) {
	    global $aUser;
	
	    $bUseCacheSystem = ( getParam('enable_cache_system') == 'on' ) ? true : false;
		if (!$bUseCacheSystem) return false;
	
		$iPageID = (int)$iPageID;
		$fileName = BX_DIRECTORY_PATH_CACHE . 'page' . $iPageID . '.php';
		if( $userID > 0 ) {
	
			$aPreUser = $this->getPageInfoDirect ($iPageID);
	
			if( isset( $aPreUser ) and is_array( $aPreUser ) and $aPreUser) {
				$sUser = '<?';
				$sUser .= "\n\n";
				$sUser .= '$aUser[' . $userID . '] = array();';
				$sUser .= "\n";
				$sUser .= '$aUser[' . $userID . '][\'datafile\'] = true;';
				$sUser .= "\n";
	
				$replaceWhat = array( '\\',   '\''   );
				$replaceTo   = array( '\\\\', '\\\'' );
	
				foreach( $aPreUser as $key =>  $value )
					$sUser .= '$aUser[' . $userID . '][\'' . $key . '\']' . ' = ' . '\'' . str_replace( $replaceWhat, $replaceTo, $value )  . '\'' . ";\n";
	
				$sUser .= "\n" . '?>';
	
				if( $file = fopen( $fileName, "w" ) ) {
					fwrite( $file, $sUser );
					fclose( $file );
					@chmod ($fileName, 0666);
	
					@include( $fileName );
					return true;
				} else
					return false;
			}
		} else
			return false;
	}	
	function createPageCache( $iMemID ) {
		$this->createPageDataFile( $iMemID );
	}
	
	function sendConfMail( $iMemID ) {
		global $site;
		
		$iMemID = (int)$iMemID;
		$aMember = $this -> getPageInfo( $iMemID );
		if( !$aMember )
			return false;
		
		$sEmail    = $aMember['Email'];
		
		$sConfCode = base64_encode( base64_encode( crypt( $sEmail, CRYPT_EXT_DES ? 'secret_ph' : 'se' ) ) );
		$sConfLink = "{$site['url']}profile_activate.php?ConfID={$iMemID}&ConfCode=" . urlencode( $sConfCode );
		
		$aPlus = array( 'ConfCode' => $sConfCode, 'ConfirmationLink' => $sConfLink );
		
		$aTemplate = $this -> oEmailTemplate -> getTemplate( 't_Confirmation' ) ;
		return sendMail( $sEmail, $aTemplate['Subject'], $aTemplate['Body'], $iMemID, $aPlus );
	}

	// sent when user status changed to active
	function sendActivationMail( $iMemID ) {

		$iMemID = (int)$iMemID;
		$aMember  = $this -> getPageInfo( $iMemID );
		if( !$aMember )
			return false;

		$sEmail    = $aMember['Email'];
		$aTemplate = $this -> oEmailTemplate -> getTemplate( 't_Activation' ) ;

       	return sendMail( $sEmail, $aTemplate['Subject'], $aTemplate['Body'], $iMemID );
	}

	//sent if member in approval status
	function sendApprovalMail( $iMemId ) {
		
	}
	
	// sent to admin
	function sendNewUserNotify( $iMemID ) {
		global $site;
		
		$iMemID = (int)$iMemID;
		$aMember = $this -> getProfileInfo( $iMemID );
		if( !$aMember )
			return false;

		$oEmailTemplates = new BxDolEmailTemplates();
		$aTemplate = $oEmailTemplates->getTemplate('t_UserJoined');

		return sendMail($site['email_notify'], $aTemplate['Subject'], $aTemplate['Body'], $iMemID);
	}
	
	function updatePage( $iMemberID, $aData ) {
		if( !$aData or !is_array($aData) or empty($aData) )
			return false;
		
        
		$sSet = $this -> collectSetString( $aData );
		$sQuery = "UPDATE `ml_pages_main` SET {$sSet} WHERE `ID` = " . (int)$iMemberID;
		//echo $sQuery ;
		db_res($sQuery);
		$this -> createPageCache( $iMemberID );
		return (bool)db_affected_rows();
	}

    
	function collectSetString( $aData ) {
		$sRequestSet = '';
		
		foreach( $aData as $sField => $mValue ) {
			if( is_string($mValue) )
				$sValue = "'" . addslashes( $mValue ) . "'";
			elseif( is_bool($mValue) )
				$sValue = (int)$mValue;
			elseif( is_array($mValue) ) {
				$sValue = '';
				foreach( $mValue as $sStr )
					$sValue .= addslashes( str_replace( ',', '', $sStr ) ) . ',';
					
				$sValue = "'" . substr($sValue,0,-1) . "'";
			} elseif( is_int($mValue) ) {
				$sValue = $mValue;
			} else
				continue;
			
			$sRequestSet .= "`$sField` = $sValue,\n";
		}
		
		$sRequestSet = substr( $sRequestSet,0, -2 );// remove ,\n
		
		return $sRequestSet;
	}
	
	function deleteProfile( $iMemberID ) {
		
	}
	
	function genRandomPassword() {
		return 'aaaaaa';
	}
	
	function getPageInfo( $iID ) {
		return db_assoc_arr( "SELECT * FROM `ml_pages_main` WHERE `ID` = " . (int)$iID );
	}
	
	function genUniqueValue( $sFieldName, $sValue, $bRandMore = false ) {
		if( $bRandMore )
			$sRand = '(' . rand(1000, 9999) . ')';
		else
			$sRand = '(2)';
			
		$sNewValue = $sValue . $sRand;
		
		$iCount = (int)db_value( "SELECT COUNT(*) FROM `ml_pages_main` WHERE `$sFieldName` = '" . addslashes($sNewValue) . "'" );
		if( $iCount )
			return genUniqueValue( $sFieldName, $sValue, true );
		else
			return $sNewValue;
	}
}