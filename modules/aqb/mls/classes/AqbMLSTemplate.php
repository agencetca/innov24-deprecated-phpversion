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

bx_import('BxDolModuleTemplate');

class AqbMLSTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function AqbMLSTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);
	}

	function getACLList($aMemberships, $aProfileTypes) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$aResult['bx_if:acl_not_exist'] = array(
        	'condition' => count($aMemberships) == 0,
        	'content' => array()
		);

		$aResult['bx_if:acl_exist'] = array(
        	'condition' => count($aMemberships) > 0,
        	'content' => array(
        		'form_action' => $sBaseUrl.'action_save/'
        	)
		);

		$aAclsList = array();
		foreach ($aMemberships as $aMembership) {
			$aAclsList[] = array(
				'acl_name' => $aMembership['Name'],
				'profile_types' => $this->getPTcheckboxes($aMembership['ID'], $aProfileTypes, $aMembership['ProfileTypes'])
			);
		}

		$aResult['bx_if:acl_exist']['content']['bx_repeat:acl_levels'] = $aAclsList;

		return  $this->parseHtmlByName('acl_levels_list.html', $aResult);
	}
	function getPTcheckboxes($iAcl, $aProfileTypes, $iProfileTypes) {
		$sRet = '';
		$checked = $iProfileTypes == 1073741823 ? 'checked="checked"' : '';
		$sRet .= "<label><input type=\"checkbox\" name=\"ptypes[{$iAcl}][1073741823]\" {$checked}>"._t('_aqb_mls_all')."</label><br />";
		foreach ($aProfileTypes as $iType => $sName) {
			$checked = $iType & $iProfileTypes ? 'checked="checked"' : '';
			$sRet .= "<label><input type=\"checkbox\" name=\"ptypes[{$iAcl}][{$iType}]\" {$checked}>"._t($GLOBALS['aPreValues']['ProfileType'][$iType]['LKey'])."</label><br />";
		}

		return $sRet;
	}
}
?>