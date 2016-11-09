<?php
/***************************************************************************
* Date				: Sun August 1, 2010
* Copywrite			: (c) 2009, 2010 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Tools
* Product Version	: 1.8.4
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/

require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");

class BxDeanosToolsInstaller extends BxDolInstaller {
	function BxDeanosToolsInstaller($aConfig) {
		parent::BxDolInstaller($aConfig);
	}

	function install($aParams) {
        $aResult = parent::install($aParams);
        $this->addExceptionsFields(array('POST.dbcsDTphpcode', 'REQUEST.dbcsDTphpcode', 'POST.dbcsDTsqlquery', 'REQUEST.dbcsDTsqlquery'));
        return $aResult;
    }

    function uninstall($aParams) {
        $this->removeExceptionsFields(array('POST.dbcsDTphpcode', 'REQUEST.dbcsDTphpcode', 'POST.dbcsDTsqlquery', 'REQUEST.dbcsDTsqlquery'));
        return parent::uninstall($aParams);
    }

}
?>