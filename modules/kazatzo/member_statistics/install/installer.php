<?php
/***************************************************************************
* Date				: Jun 06, 2011
* Copywrite			: (c) 2011 by kazatzo
*
* Product Name		: Member Statistics
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

class MemberStatisticsInstaller extends BxDolInstaller {
	function BxFBFriendInviterInstaller($aConfig) {
		parent::BxDolInstaller($aConfig);
	}
}
?>