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

bx_import('BxDolModuleTemplate');

/*
 * Quotes module View
 */
class BxDeanosToolsTemplate extends BxDolModuleTemplate {
	/**
	* Constructor
	*/
	function BxDeanosToolsTemplate(&$oConfig, &$oDb) {
		parent::BxDolModuleTemplate($oConfig, $oDb);

		//$this->_aTemplates = array('unit', 'adm_unit');
	}

	function loadTemplates() {
	    parent::loadTemplates();
	}

	function parseHtmlByName ($sName, &$aVars) {        
		return parent::parseHtmlByName ($sName.'.html', $aVars);
	}
}

?>