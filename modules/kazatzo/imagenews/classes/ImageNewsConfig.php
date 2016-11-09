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

bx_import('BxDolTextConfig');

class ImageNewsConfig extends BxDolTextConfig {
	function ImageNewsConfig($aModule) {
	    parent::BxDolTextConfig($aModule);
	}
	function init(&$oDb) {
	    parent::init($oDb);

	    $this->_bAutoapprove = $this->_oDb->getParam('imagenews_autoapprove') == 'on';
	    $this->_bComments = $this->_oDb->getParam('imagenews_comments') == 'on';
	    $this->_sCommentsSystemName = "imagenews";
	    $this->_bVotes = $this->_oDb->getParam('imagenews_votes') == 'on';
	    $this->_sVotesSystemName = "imagenews";
	    $this->_sDateFormat = getLocaleFormat(BX_DOL_LOCALE_DATE_SHORT, BX_DOL_LOCALE_DB);
	    $this->_sAnimationEffect = 'fade';
	    $this->_iAnimationSpeed = 'slow';
	    $this->_iIndexNumber = (int)$this->_oDb->getParam('imagenews_index_number');
	    $this->_iMemberNumber = (int)$this->_oDb->getParam('imagenews_member_number');
		$this->_iSnippetLength = 150;	
	    $this->_iPerPage = (int)$this->_oDb->getParam('imagenews_per_page');
	    $this->_sSystemPrefix = 'imagenews';
	    $this->_aJsClasses = array('main' => 'ImageNewsMain');
	    $this->_aJsObjects = array('main' => 'oImageNewsMain');
	    $this->_iRssLength = (int)$this->_oDb->getParam('imagenews_rss_length');
		$this->_iImageType = $this->_oDb->getParam('imagenews_type');
	}
	
	function getImageType() {
	    return $this->_iImageType;
	}
}
?>