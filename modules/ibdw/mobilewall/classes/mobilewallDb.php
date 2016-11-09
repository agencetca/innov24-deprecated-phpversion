<?
/**********************************************************************************
*                            IBDW MobileWall Dolphin Smart Community Builder
*                              -------------------
*     begin                : Oct 18 2011
*     copyright            : (C) 2011 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW MobileWall is not free and you cannot redistribute and/or modify it.
* 
* IBDW MobileWall is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details write to info@ilbellodelweb.it
**********************************************************************************/

bx_import('BxDolModuleDb');

class mobilewallDb extends BxDolModuleDb {

	function mobilewallDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }

    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'mobilewall' LIMIT 1");
    }    
}

?>
