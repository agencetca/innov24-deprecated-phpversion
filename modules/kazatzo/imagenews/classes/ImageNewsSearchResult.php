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

bx_import('BxDolTextSearchResult');

class ImageNewsSearchResult extends BxDolTextSearchResult {
	function ImageNewsSearchResult($oModule = null) {
		$oModule = !empty($oModule) ? $oModule : BxDolModule::getInstance('ImageNewsModule');

        parent::BxDolTextSearchResult($oModule);
		$this->_oModule = $oModule;

		$sModuleUri = $this->_oModule->_oConfig->getUri();
		$this->aCurrent['name'] = $sModuleUri;	
	}
}
?>