<?
/**********************************************************************************
*                            IBDW 1Col Dolphin Smart Community Builder
*                              -------------------
*     begin                : May 1 2010
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW 1Col is not free and you cannot redistribute and/or modify it.
* 
* IBDW 1Col is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

bx_import ('BxDolTwigTemplate');
class FirstColTemplate extends BxDolTwigTemplate {
	function FirstColTemplate(&$oConfig, &$oDb) {
	    parent::BxDolTwigTemplate($oConfig, $oDb);
    }
}// 
// 
// bx_import('BxDolModuleTemplate');
// class FirstColTemplate extends BxDolModuleTemplate {
// /**
//      * Constructor
//      */
//     function FirstColTemplate(&$oConfig, &$oDb) {
//         parent::BxDolModuleTemplate($oConfig, $oDb);
//     }
//     // Output
//     function pageCode($aPage = array(), $aPageCont = array(), $aCss = array(), $aJs = array(), $bAdminMode = false, $isSubActions = true) {
//         if (!empty($aPage)) {
//             foreach ($aPage as $sKey => $sValue)
//                 $GLOBALS['_page'][$sKey] = $sValue;
//         }
//         if (!empty($aPageCont)) {
//             foreach ($aPageCont as $sKey => $sValue)
//                 $GLOBALS['_page_cont'][$aPage['name_index']][$sKey] = $sValue;
//         }
//         if (!empty($aCss))
//             $this->addCss($aCss);
//         if (!empty($aJs))
//             $this->addJs($aJs);
// 
//         if (!$bAdminMode)
//             PageCode($this);
//         else
//             PageCodeAdmin();
//     }
// }

?>