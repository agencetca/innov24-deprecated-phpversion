<?php
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

bx_import('BxDolTextCron');

class ImageNewsCron extends BxDolTextCron {
	function ImageNewsCron() {
		parent::BxDolTextCron();

		$this->_oModule = BxDolModule::getInstance('ImageNewsModule');
	}
	
	function processing() {
		$sModuleName = $this->_oModule->_oConfig->getUri();

		$aIds = array();
		if($this->_oModule->_oDb->publish($aIds))
			foreach($aIds as $iId) {
				//--- Entry -> Publish for Alerts Engine ---//
				$oAlert = new BxDolAlerts('imagenews', 'publish', $iId);
				$oAlert->alert();
				//--- Entry -> Publish for Alerts Engine ---//

				//--- Reparse Global Tags ---//
				$oTags = new BxDolTags();
				$oTags->reparseObjTags('imagenews', $iId);
				//--- Reparse Global Tags ---//

				//--- Reparse Global Categories ---//            
				$oCategories = new BxDolCategories();
				$oCategories->reparseObjTags('imagenews', $iId);
				//--- Reparse Global Categories ---//
			}
    }
}
?>