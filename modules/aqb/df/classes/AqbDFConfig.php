<?php
define('AQB_DF_MULTISELECT_ALLOWED', true); //this setting allows multiselectable fields on search page
//define('AQB_DF_MULTISELECT_ALLOWED', false); //this setting converts multiselectable fields on search page to single select fields

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

bx_import('BxDolConfig');

class AqbDFConfig extends BxDolConfig {
	/**
	 * Constructor
	 */
	function AqbDFConfig($aModule) {
	    parent::BxDolConfig($aModule);
	}
	function isMultiselectAllowed() {
		return AQB_DF_MULTISELECT_ALLOWED;
	}
}
?>