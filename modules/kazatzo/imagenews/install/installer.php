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

require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");

class ImageNewsInstaller extends BxDolInstaller {
    function ImageNewsInstaller($aConfig) {
        parent::BxDolInstaller($aConfig);
    }

	function install($aParams) {
        $aResult = parent::install($aParams);

        $this->addHtmlFields(array('POST.content', 'REQUEST.content'));
		$this->updateEmailTemplatesExceptions ();

        return $aResult;
    }

    function uninstall($aParams) {
        $this->removeHtmlFields();
		$this->updateEmailTemplatesExceptions ();
        return parent::uninstall($aParams);
    }
}
?>
