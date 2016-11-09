<?
/*************************Product owner info********************************
*
*     author               : Boonexpert 
*     contact info         : boonexpert@gmail.com
*
/*************************Product info**************************************
*
*                          Background Switcher
*                          ------------------------
*     version              : 1.0
*     date		   : 3 December 2010
*     compability          : Dolphin 7.0.0 - 7.0.4
*     License type         : Custom
*
* IMPORTANT: This is a commercial product made by Boonexpert and cannot be modified for other than personal use. 
* This product cannot be redistributed for free or a fee without written permission from Boonexpert. 
*
*     Upgrade possibilities : All future upgrades will be added to this product package
*
****************************************************************************/

require_once(BX_DIRECTORY_PATH_INC . 'profiles.inc.php');

check_logged();

bx_import('BxDolRequest');
BxDolRequest::processAsAction($GLOBALS['aModule'], $GLOBALS['aRequest']);

?>
