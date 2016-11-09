<?
/**********************************************************************************
*                            IBDW MobileWall for Dolphin Smart Community Builder
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
* and css style file.
* For more details write to info@ilbellodelweb.it
**********************************************************************************/

require_once(BX_DIRECTORY_PATH_INC . 'profiles.inc.php');
check_logged();
bx_import('BxDolRequest');
BxDolRequest::processAsAction($GLOBALS['aModule'], $GLOBALS['aRequest']);
?>

