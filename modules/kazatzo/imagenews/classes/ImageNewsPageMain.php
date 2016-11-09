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

bx_import('BxDolTextPageMain');

class ImageNewsPageMain extends BxDolTextPageMain { 
    function ImageNewsPageMain(&$oObject) {
        parent::BxDolTextPageMain('imagenews_home', $oObject);
    }
}
?>