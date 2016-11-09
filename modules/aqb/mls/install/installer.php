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
require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");

class AqbMLSInstaller extends BxDolInstaller {
    function AqbMLSInstaller($aConfig) {
        parent::BxDolInstaller($aConfig);
    }
    function actionCheckPassByRef() {
    	$bEnabled = ini_get('allow_call_time_pass_reference');
		return $bEnabled ? BX_DOL_INSTALLER_SUCCESS : array('code' => BX_DOL_INSTALLER_FAILED, 'content' => '');
    }
    function actionCheckPassByRefFailed() {
    	return 'Add please the line <br /><br />php_flag allow_call_time_pass_reference On<br /><br />to the top of your .htaccess file in the Dolphin\'s root folder';
    }
}
?>