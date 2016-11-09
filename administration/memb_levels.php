<?php

/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

require_once( '../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );
bx_import('BxDolAdminSettings');
bx_import('BxTemplSearchResult');

$logged['admin'] = member_auth( 1, true, true );

$oSettings = new BxDolAdminSettings(5); 

//--- Process submit ---//
$mixedResultSettings = '';
$mixedResultAvailable = '';
$mixedResultActions = '';
$mixedResultAction = '';
$mixedResultPrices = '';
if(isset($_POST['save']) && isset($_POST['cat'])) {
    $mixedResultSettings = $oSettings->saveChanges($_POST);
} else if((isset($_POST['adm-mlevels-enable']) || isset($_POST['adm-mlevels-disable'])) && !empty($_POST['levels'])) {
    if(isset($_POST['adm-mlevels-enable']))
        $sValue = 'yes';
    else if(isset($_POST['adm-mlevels-disable']))
        $sValue = 'no';
    
    $GLOBALS['MySQL']->query("UPDATE `sys_acl_levels` SET `Active`='" . $sValue . "' WHERE `ID` IN ('" . implode("','", $_POST['levels']) . "')");
} else if(isset($_POST['adm-mlevels-delete']) && !empty($_POST['levels'])) {
    foreach($_POST['levels'] as $iId)
        if(($mixedResultAvailable = deleteMembership($iId)) !== true)
            break;
} else if(isset($_POST['adm-mlevels-actions-enable']) || isset($_POST['adm-mlevels-actions-disable'])) {
    $iLevelId = (int)$_POST['level'];

    foreach($_POST['actions'] as $iId) {
        if(isset($_POST['adm-mlevels-actions-enable']))
            $sQuery = "REPLACE INTO `sys_acl_matrix` SET `IDLevel`='" . $iLevelId . "', `IDAction`='" . $iId . "'";
        else 
            $sQuery = "DELETE FROM `sys_acl_matrix` WHERE `IDLevel`='" . $iLevelId . "' AND `IDAction`='" . $iId . "'";

        $GLOBALS['MySQL']->query($sQuery);
    }
} else if(isset($_POST['adm-mlevels-prices-add'])) {
    $iLevelId = (int)$_POST['level'];
    $iDays = (int)$_POST['days'];
    $iPrice = (float)trim($_POST['price'], " $");
    
    $GLOBALS['MySQL']->query("INSERT INTO `sys_acl_level_prices`(`IDLevel`, `Days`, `Price`) VALUES('" . $iLevelId . "', '" . $iDays . "', '" . $iPrice . "')");
} else if(isset($_POST['adm-mlevels-prices-delete'])) {
    $GLOBALS['MySQL']->query("DELETE FROM `sys_acl_level_prices` WHERE `id` IN ('" . implode("','", $_POST['prices']) . "')");
} else if(isset($_POST['adm-mlevels-action-save'])) {
    $sQuery = "REPLACE INTO `sys_acl_matrix` SET `IDLevel`='" . (int)$_POST['levelId'] . "', `IDAction`='" . (int)$_POST['actionId'] . "'";
    $sQuery .= !empty($_POST['allowedCnt']) ? ", `AllowedCount`='" . (int)$_POST['allowedCnt'] . "'" : "";
    $sQuery .= !empty($_POST['period']) ? ", `AllowedPeriodLen`='" . (int)$_POST['period'] . "'" : "";        
    $sQuery .= !empty($_POST['dateStart']) && strtotime($_POST['dateStart']) > 0 ? ", `AllowedPeriodStart`=FROM_UNIXTIME(" . strtotime($_POST['dateStart']) . ")" : "";
    $sQuery .= !empty($_POST['dateEnd']) && strtotime($_POST['dateEnd']) > 0 ? ", `AllowedPeriodEnd`=FROM_UNIXTIME(" . strtotime($_POST['dateEnd']) . ")" : "";
    $aResult = $GLOBALS['MySQL']->query($sQuery) > 0 ? array('code' => 0, 'message' => MsgBox(_t('_adm_txt_mlevels_action_saved'))) : array('code' => 1, 'message' => MsgBox(_t('_adm_txt_mlevels_action_cannot_save')));

    $oJson = new Services_JSON();   
    echo "<script>parent.onResult(" . $oJson->encode($aResult) . ");</script>";
    exit;
} else if(isset($_POST['action']) && $_POST['action'] == 'get_edit_form_action') {
    $oJson = new Services_JSON();   
    echo $oJson->encode(array('code' => PageCodeAction((int)$_POST['level_id'], (int)$_POST['action_id'], $mixedResultAction)));
    exit;
}
    
$iLevelId = bx_get('level') !== false ? (int)bx_get('level') : 0;

$iNameIndex = 6;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('forms_adv.css', 'settings.css', 'memb_levels.css', 'plugins/jquery/themes/|ui.datepicker.css'),
    'js_name' => array('ui.datepicker.js', 'memb_levels.js'),
    'header' => _t('_adm_page_cpt_memb_levels'),    
);
$_page_cont[$iNameIndex] = array(
    'page_code_settings' => PageCodeSettings($mixedResultSettings),
    'page_code_create' => PageCodeCreate(bx_get('action') !== false && bx_get('action') == 'edit' ? $iLevelId : 0),
    'page_code_available' => PageCodeAvailable($mixedResultAvailable),
    'page_code_actions' => bx_get('action') !== false && bx_get('action') == 'actions' && $iLevelId > 0 ? PageCodeActions($iLevelId, $mixedResultActions) : "",
    'page_code_prices' => bx_get('action') !== false && bx_get('action') == 'prices' && $iLevelId > 0 ? PageCodePrices($iLevelId, $mixedResultPrices) : "",
);

PageCodeAdmin();

function PageCodeSettings($mixedResult) {
    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $GLOBALS['oSettings']->getForm()));    
    if($mixedResult !== true && !empty($mixedResult))
        $sResult = $mixedResult . $sResult;
    
    return DesignBoxAdmin(_t('_adm_box_cpt_mlevel_settings'), $sResult);
}
function PageCodeCreate($iLevelId = 0) {
    $sSubmitUrl = $GLOBALS['site']['url_admin'] . 'memb_levels.php';

    $aLevel = array();
    if(($bEdit = $iLevelId != 0) === true)
        $aLevel = $GLOBALS['MySQL']->getRow("SELECT `Name` AS `Name`, `Description` AS `Description`, `Order` AS `Order` FROM `sys_acl_levels` WHERE `ID`='" . $iLevelId . "' LIMIT 1");

    $aForm = array(
        'form_attrs' => array(
            'id' => 'adm-mlevels-create',
            'action' => $sSubmitUrl,
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ),
        'params' => array (
            'db' => array(
                'table' => 'sys_acl_levels',
                'key' => 'ID',
                'uri' => '',
                'uri_title' => '',
                'submit_name' => 'Submit'
            ),
        ),
        'inputs' => array (            
            'Active' => array(
                'type' => 'hidden',
                'name' => 'Active',
                'value' => 'no',
                'db' => array (
                    'pass' => 'Xss',
                ),
            ),
            'Purchasable' => array(
                'type' => 'hidden',
                'name' => 'Purchasable',
                'value' => 'yes',
                'db' => array (
                    'pass' => 'Xss',
                ),
            ),
            'Removable' => array(
                'type' => 'hidden',
                'name' => 'Removable',
                'value' => 'yes',
                'db' => array (
                    'pass' => 'Xss',
                ),
            ),
            'Name' => array(
                'type' => 'text',
                'name' => 'Name',
                'caption' => _t('_adm_txt_mlevels_name'),
                'value' => isset($aLevel['Name']) ? $aLevel['Name'] : '',
                'db' => array (
                    'pass' => 'Xss',
                ),
                'checker' => array (
					'func' => 'length',
					'params' => array(3,100),
					'error' => _t('_adm_txt_mlevels_name_err'),
				),
            ),
            'Icon' => array(
                'type' => 'file',
                'name' => 'Icon',
                'caption' => _t('_adm_txt_mlevels_icon'),
            	'value' => '',
                'checker' => array (
					'func' => '',
					'params' => '',
					'error' => _t('_adm_txt_mlevels_icon_err'),
				),
            ),
            'Description' => array(
                'type' => 'textarea',
                'name' => 'Description',
                'caption' => _t('_adm_txt_mlevels_description'),
                'value' => isset($aLevel['Description']) ? $aLevel['Description'] : '',
                'db' => array (
                    'pass' => 'XssHtml',
                ),
            ),
            'Order' => array(
                'type' => 'text',
                'name' => 'Order',
                'caption' => _t('_adm_txt_mlevels_order'),
                'value' => isset($aLevel['Order']) ? $aLevel['Order'] : 0,
                'db' => array (
                    'pass' => 'Int',
                ),
                'checker' => array (
                    'func' => 'preg',
                    'params' => array('/^[1-9][0-9]*$/'),
                    'error' => _t('_adm_txt_mlevels_order_err'),
                ),
            ),
            'Submit' => array(
                'type' => 'submit',
                'name' => 'Submit',
                'value' => _t('_adm_btn_mlevels_add'),
            ),                
        )
    );

    //--- Convert Add to Edit
    if($bEdit) {
        unset($aForm['inputs']['Active']);
        unset($aForm['inputs']['Purchasable']);
        unset($aForm['inputs']['Removable']);
        unset($aForm['inputs']['Icon']);

        $aForm['form_attrs']['action'] .= '?action=edit&level=' . $iLevelId;
        $aForm['inputs']['Submit']['value'] = _t('_adm_btn_mlevels_save');
        $aForm['inputs']['ID'] = array(
            'type' => 'hidden',
            'name' => 'ID',
            'value' => $iLevelId,
            'db' => array (
                'pass' => 'Int',
            )
        );
    }

    $oForm = new BxTemplFormView($aForm);
    $oForm->initChecker();

    if($oForm->isSubmittedAndValid()) {
        //--- Add new level
        if(!$bEdit) {
            $sFilePath = BX_DIRECTORY_PATH_ROOT . 'media/images/membership/';
            $sFileName = time();
            $sFileExt = '';

            if(isImage($_FILES['Icon']['type'], $sFileExt) && !empty($_FILES['Icon']['tmp_name']) && move_uploaded_file($_FILES['Icon']['tmp_name'],  $sFilePath . $sFileName . '.' . $sFileExt)) {
                $sPath = $sFilePath . $sFileName . '.' . $sFileExt;
                imageResize($sPath, $sPath, 110, 110);

                $iId = (int)$oForm->insert(array('Icon' => $sFileName . '.' . $sFileExt));
                if($iId != 0) {
                    $sName = $oForm->getCleanValue('Name');
                    addStringToLanguage('_adm_txt_mp_' . strtolower($sName), $sName);
                }

                header('Location: ' . $sSubmitUrl);
                exit;
            }
            else
                $oForm->aInputs['Icon']['error'] = $oForm->aInputs['Icon']['checker']['error'];
        }
        //--- Edit existing level
        else {
            $bResult = $oForm->update($iLevelId);
            if($bResult !== false) {
                deleteStringFromLanguage('_adm_txt_mp_' . strtolower($aLevel['Name']));

                $sName = $oForm->getCleanValue('Name');
                addStringToLanguage('_adm_txt_mp_' . strtolower($sName), $sName);
            }

            header('Location: ' . $sSubmitUrl);
            exit;
        }
    }

    return DesignBoxAdmin(_t(!$bEdit ? '_adm_box_cpt_mlevel_create' : '_adm_box_cpt_mlevel_edit'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode())));
}
function PageCodeAvailable($mixedResult) {
    //--- Get Items ---//
    $aItemsSystem = $aItemsCustom = array();

    $aLevels = $GLOBALS['MySQL']->getAll("SELECT `ID` AS `id`, `Name` AS `title`, `Active` AS `active`, `Purchasable` AS `purchasable`, `Removable` AS `removable` FROM `sys_acl_levels` WHERE `Removable`='no' ORDER BY `ID` ASC");
    foreach($aLevels as $aLevel)
        $aItemsSystem[] = array(
            'id' => $aLevel['id'],
            'title' => $aLevel['title'],
            'actions_link' => $GLOBALS['site']['url_admin'] . 'memb_levels.php?action=actions&level=' . $aLevel['id'] . '#actions' . $aLevel['id'],
        );

    $aLevels = $GLOBALS['MySQL']->getAll("SELECT `ID` AS `id`, `Name` AS `title`, `Active` AS `active`, `Purchasable` AS `purchasable`, `Removable` AS `removable` FROM `sys_acl_levels` WHERE `Removable`='yes' ORDER BY `Order` ASC");
    foreach($aLevels as $aLevel)
        $aItemsCustom[] = array(
            'id' => $aLevel['id'],
            'title' => $aLevel['title'],
            'class' => $aLevel['active'] == 'yes' ? 'adm-mlevels-enabled' : 'adm-mlevels-disabled',
            'actions_link' => $GLOBALS['site']['url_admin'] . 'memb_levels.php?action=actions&level=' . $aLevel['id'] . '#actions' . $aLevel['id'],
            'bx_if:editable' => array(
                'condition' => $aLevel['removable'] == 'yes',
                'content' => array(
                    'edit_link' => $GLOBALS['site']['url_admin'] . 'memb_levels.php?action=edit&level=' . $aLevel['id'],
                )
            ),
            'bx_if:purchasable' => array(
                'condition' => $aLevel['purchasable'] == 'yes',
                'content' => array(
                    'price_link' => $GLOBALS['site']['url_admin'] . 'memb_levels.php?action=prices&level=' . $aLevel['id'] . '#prices' . $aLevel['id'],
                )
            )
        );

    //--- Get Controls ---//
    $aButtons = array(
        'adm-mlevels-enable' => _t('_adm_btn_mlevels_enable'),
        'adm-mlevels-disable' => _t('_adm_btn_mlevels_disable'),
        'adm-mlevels-delete' => _t('_adm_btn_mlevels_delete')
    );    
    $sControls = BxTemplSearchResult::showAdminActionsPanel('adm-mlevels-list-form', $aButtons, 'levels');

    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName(
        'mlevels_list.html', 
        array(
            'bx_repeat:items_system' => $aItemsSystem, 
            'bx_repeat:items_custom' => $aItemsCustom, 
            'controls' => $sControls
        )
    );

    if($mixedResult !== true && !empty($mixedResult))
        $sResult = MsgBox(_t($mixedResult), 3) . $sResult;

    return DesignBoxAdmin(_t('_adm_box_cpt_mlevel_levels'), $sResult);
}
function PageCodeActions($iId, $mixedResult) {
    $sTitle = $GLOBALS['MySQL']->getOne("SELECT `Name` FROM `sys_acl_levels` WHERE `ID`='" . $iId . "' LIMIT 1");

    //--- Get Items ---//
    $aItems = array();

    $aActions = $GLOBALS['MySQL']->getAll("SELECT `ta`.`ID` AS `id`, `ta`.`Name` AS `title` FROM `sys_acl_actions` AS `ta` ORDER BY `ta`.`Name`");
    $aActionsActive = $GLOBALS['MySQL']->getAllWithKey("SELECT `ta`.`ID` AS `id`, `ta`.`Name` AS `title` FROM `sys_acl_actions` AS `ta` LEFT JOIN `sys_acl_matrix` AS `tm` ON `ta`.`ID`=`tm`.`IDAction` LEFT JOIN `sys_acl_levels` AS `tl` ON `tm`.`IDLevel`=`tl`.`ID` WHERE `tl`.`ID`='" . $iId . "' ORDER BY `ta`.`Name`", "id");
    foreach($aActions as $aAction) {
        $bEnabled = array_key_exists($aAction['id'], $aActionsActive);
        $aItems[] = array(
            'action_id' => $aAction['id'],
            'title' => $aAction['title'],
            'class' => $bEnabled ? 'adm-mlevels-enabled' : 'adm-mlevels-disabled',
            'bx_if:enabled' => array(
                'condition' => $bEnabled,
                'content' => array(
                    'level_id' => $iId,
                    'action_id' => $aAction['id'],
                    'title' => $aAction['title']
                )
            ),
            'bx_if:disabled' => array(
                'condition' => !$bEnabled,
                'content' => array(
                    'action_id' => $aAction['id'],
                    'title' => $aAction['title']
                )
            ),
        );
    }

    //--- Get Controls ---//
    $aButtons = array(
        'adm-mlevels-actions-enable' => _t('_adm_btn_mlevels_enable'),
        'adm-mlevels-actions-disable' => _t('_adm_btn_mlevels_disable')        
    );    
    $sControls = BxTemplSearchResult::showAdminActionsPanel('adm-mlevels-actions-form', $aButtons, 'actions');
	    
    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('mlevels_actions.html', array(
        'id' => $iId,
        'bx_repeat:items' => $aItems,
        'controls' => $sControls,
        'url_admin' => $GLOBALS['site']['url_admin']
    ));
    
    if($mixedResult !== true && !empty($mixedResult))
        $sResult = MsgBox(_t($mixedResult), 3) . $sResult;

    return DesignBoxAdmin(_t('_adm_box_cpt_mlevel_actions', $sTitle), $sResult);
}
function PageCodeAction($iLevelId, $iActionId, $mixedResult) {
    $aAction = $GLOBALS['MySQL']->getRow("SELECT * FROM `sys_acl_matrix` WHERE `IDLevel`='" . $iLevelId . "' AND `IDAction`='" . $iActionId . "'");
    
    $aForm = array(
        'form_attrs' => array(
            'id' => 'adm-mlevels-action-form',
            'target' => 'adm-mlevels-action-iframe',
            'action' => $GLOBALS['site']['url_admin'] . 'memb_levels.php',
            'method' => 'post',
            'enctype' => 'multipart/form-data'            
        ),
        'params' => array (),
        'inputs' => array (            
            'levelId' => array(
                'type' => 'hidden',
                'name' => 'levelId',
                'value' => $iLevelId
            ),
            'actionId' => array(
                'type' => 'hidden',
                'name' => 'actionId',
                'value' => $iActionId
            ),
            'allowedCnt' => array(
                'type' => 'text',
                'name' => 'allowedCnt',
                'caption' => _t('_adm_txt_mlevels_actions_number'),
                'info' => _t('_adm_txt_mlevels_actions_number_desc'),
                'value' => isset($aAction['AllowedCount']) ? (int)$aAction['AllowedCount'] : ""
            ),
            'period' => array(
                'type' => 'text',
                'name' => 'period',
                'caption' => _t('_adm_txt_mlevels_actions_reset'),
                'info' => _t('_adm_txt_mlevels_actions_reset_desc'),
                'value' => isset($aAction['AllowedPeriodLen']) ? (int)$aAction['AllowedPeriodLen'] : ""
            ),
            'dateStart' => array(
                'type' => 'datetime',
                'name' => 'dateStart',
                'caption' => _t('_adm_txt_mlevels_actions_avail_start'),
                'info' => _t('_adm_txt_mlevels_actions_avail_desc'),
                'value' => isset($aAction['AllowedPeriodStart']) ? $aAction['AllowedPeriodStart'] : "",
                'display' => 'filterDate',
                'attrs' => array(
                    'allow_input' => 'true',
                ),
            ),
            'dateEnd' => array(
                'type' => 'datetime',
                'name' => 'dateEnd',
                'caption' => _t('_adm_txt_mlevels_actions_avail_end'),
                'info' => _t('_adm_txt_mlevels_actions_avail_desc'),
                'value' => isset($aAction['AllowedPeriodEnd']) ? $aAction['AllowedPeriodEnd'] : "",
                'display' => 'filterDate',
            	'attrs' => array(
                    'allow_input' => 'true',
                ),
            ), 
            'adm-mlevels-action-save' => array(
                'type' => 'submit',
                'name' => 'adm-mlevels-action-save',
                'value' => _t('_adm_btn_mlevels_save'),
            ),                
        )
    );
    $oForm = new BxTemplFormView($aForm);

    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('mlevels_action.html', array(
        'content' => $oForm->getCode()        
    ));
    
    if($mixedResult !== true && !empty($mixedResult))
        $sResult = MsgBox(_t($mixedResult), 3) . $sResult;

    return $sResult;
}
function PageCodePrices($iId, $mixedResult) {
    $sTitle = $GLOBALS['MySQL']->getOne("SELECT `Name` FROM `sys_acl_levels` WHERE `ID`='" . $iId . "' LIMIT 1");

    //--- Get Items ---//
    $aItems = array();

    $oModuleDb = new BxDolModuleDb();
    if($oModuleDb->isModule('payment')) {
    	$aInfo = BxDolService::call('payment', 'get_currency_info');
    	$sCurrencySign = $aInfo['sign'];
    } 
    else 
    	$sCurrencySign = getParam('currency_sign');

    $aPrices = $GLOBALS['MySQL']->getAll("SELECT `id` AS `id`, `Days` AS `days`, `Price` AS `price` FROM `sys_acl_level_prices` WHERE `IDLevel`='" . $iId . "' ORDER BY `id`");
    foreach($aPrices as $aPrice)
        $aItems[] = array(
            'id' => $aPrice['id'],
            'title' => (int)$aPrice['days'] == 0 ? _t('_adm_txt_mlevels_price_info_lifetime', $sCurrencySign, $aPrice['price']) : _t('_adm_txt_mlevels_price_info', $aPrice['days'], $sCurrencySign, $aPrice['price']),
        );

    //--- Get Controls ---//
    $aButtons = array(
        'adm-mlevels-prices-delete' => _t('_adm_btn_mlevels_delete')
    );    
    $sControls = BxTemplSearchResult::showAdminActionsPanel('adm-mlevels-prices-form', $aButtons, 'prices');
	    
    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('mlevels_prices.html', array(
        'id' => $iId,
        'bx_repeat:items' => $aItems,
        'controls' => $sControls
    ));

    if($mixedResult !== true && !empty($mixedResult))
        $sResult = MsgBox(_t($mixedResult), 3) . $sResult;

    return DesignBoxAdmin(_t('_adm_box_cpt_mlevel_prices', $sTitle), $sResult);
}
function isImage($sMimeType, &$sFileExtension) {
	$bResult = true;
	switch($sMimeType) {
		case 'image/jpeg':
		case 'image/pjpeg':
			$sFileExtension = 'jpg';
			break;
		case 'image/png':
		case 'image/x-png':
			$sFileExtension = 'png';
			break;
		case 'image/gif':
			$sFileExtension = 'gif';
			break;
		default:
			$bResult = false;
	}
	return $bResult;
}
function deleteMembership($iId) {
	$iId = (int)$iId;

	$aLevel = $GLOBALS['MySQL']->getRow("SELECT `Icon` AS `icon`, `Removable` AS `removable` FROM `sys_acl_levels` WHERE `ID`='" . $iId . "'");
	if(empty($aLevel))
		return "_adm_txt_mlevels_not_found";

	//Check if membership can be removed
	if($aLevel['removable'] != 'yes')
		return '_adm_txt_mlevels_cannot_remove';

	//Check if there are still members using this membership
	$iDateExpires = $GLOBALS['MySQL']->getOne("SELECT UNIX_TIMESTAMP(MAX(`DateExpires`)) as `MaxDateExpires` FROM `sys_acl_levels_members` WHERE `IDLevel`='" . $iId . "'");
	if($iDateExpires > time())
		return "_adm_txt_mlevels_is_used";

	@unlink(BX_DIRECTORY_PATH_ROOT . 'media/images/membership/' . $aLevel['icon']);
	db_res("DELETE FROM `sys_acl_level_prices` WHERE `IDLevel`='" . $iId . "'");
	db_res("DELETE FROM `sys_acl_matrix` WHERE `IDLevel`='" . $iId . "'");
	db_res("DELETE FROM `sys_acl_levels` WHERE `ID`='" . $iId . "'");
	
	return true;
}
?>
