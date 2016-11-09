<?php
/***************************************************************************
*
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

bx_import('BxDolModule');

require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

//a fix for stupid IE to prevent ajax requests being cached
if (isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) {
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}
//a fix for stupid IE to prevent ajax requests being cached

class AqbMLSModule extends BxDolModule {
	/**
	 * Constructor
	 */
	function AqbMLSModule($aModule) {
	    parent::BxDolModule($aModule);
	}
	function displayAdminPage($bAddWrapper = true) {
		$aAvailableMemberships = getMemberships(true);
		$aFilters = $this->_oDb->getAllWithKey("SELECT * FROM `aqb_mls_acl_levels`", 'AclID');
		$aMemberships = array();
		foreach ($aAvailableMemberships as $iAclID => $sName) {
			$aMemberships[] = array(
				'ID' => $iAclID,
				'Name' =>$sName,
				'ProfileTypes' => isset($aFilters[$iAclID]) ? $aFilters[$iAclID]['ProfileTypes'] : 1073741823
			);
		}
		$aProfileTypes = $this->_oDb->getAllWithKey("SELECT * FROM `aqb_pts_profile_types`", 'ID');
		$sRet = $this->_oTemplate->getACLList($aMemberships, $aProfileTypes);
		if ($bAddWrapper) $sRet = '<div id="aqb_mls_acls_list">'.$sRet.'</div>';
		return $sRet;
	}
	function actionSave() {
		$aPTypes = $_POST['ptypes'];
		foreach ($aPTypes as $iAcl => $aTypes) {
			if (isset($aTypes[1073741823])) $this->_oDb->query("DELETE FROM `aqb_mls_acl_levels` WHERE `AclID` = {$iAcl} LIMIT 1");
			else {
				$iProfileTypes = 0;
				foreach ($aTypes as $iType => $dummy) {
					$iProfileTypes |= $iType;
				}
				$this->_oDb->query("REPLACE INTO `aqb_mls_acl_levels` SET `AclID` = {$iAcl}, `ProfileTypes` = {$iProfileTypes}");
			}
		}
		return $this->displayAdminPage(false);
	}

	function serviceProfileTypesFilter(&$sJoinClause, &$sWhereClause) {
		$iProfile = getLoggedId();
        $aProfile = getProfileInfo($iProfile);
        $iProfileType = $aProfile['ProfileType'];
        if ($iProfileType) {
        	$sJoinClause .= "LEFT JOIN `aqb_mls_acl_levels` ON `tl`.`ID`=`aqb_mls_acl_levels`.`AclID`";
        	$sWhereClause .= " AND (`ProfileTypes` & {$iProfileType} OR `ProfileTypes` IS NULL)";
        }
	}
}
?>