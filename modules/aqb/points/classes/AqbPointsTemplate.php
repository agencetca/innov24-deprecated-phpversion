<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

bx_import('BxTemplFormView');
bx_import('BxTemplSearchResult');
bx_import('BxTemplSearchProfile');
bx_import('BxDolTwigTemplate');
bx_import('BxDolParams');
require_once('AqbPointsFormCheckerHelper.php');

define('BX_DOL_ADM_MP_JS_NAME', 'Profile');
define('BX_DOL_ADM_MP_PER_PAGE', 32);
define('BX_DOL_ADM_MP_PER_PAGE_STEP', 16);

class AqbPointsTemplate extends BxDolTwigTemplate {
   var $_ActiveMenuLink = '', $oSearchProfileTmpl;
	
	/**
	 * Constructor
	 */
	
	function AqbPointsTemplate(&$oConfig, &$oDb) {
	    parent::BxDolTwigTemplate($oConfig, $oDb);
	    $this -> oSearchProfileTmpl = new BxTemplSearchProfile();
	}
	
	function addAdminCss ($sName) 
    {
     	parent::addAdminCss($sName);
    }
	
    function parseHtmlByName ($sName, $aVars) {
     	return parent::parseHtmlByName ($sName, $aVars);
    }
    
    function genWrapperInput($aInput, $sContent) {
       $oForm = new BxTemplFormView(array());
       
       $sAttr = isset($aInput['attrs_wrapper']) && is_array($aInput['attrs_wrapper']) ? $oForm -> convertArray2Attrs($aInput['attrs_wrapper']) : '';
       switch ($aInput['type']) {
            case 'textarea':
                $sCode = <<<BLAH
                        <div class="input_wrapper input_wrapper_{$aInput['type']}" $sAttr>
                            <div class="input_border">
                                $sContent
                            </div>
                            <div class="input_close_{$aInput['type']} left top"></div>
                            <div class="input_close_{$aInput['type']} left bottom"></div>
                            <div class="input_close_{$aInput['type']} right top"></div>
                            <div class="input_close_{$aInput['type']} right bottom"></div>
                        </div>
BLAH;
            break;
            
            default:
                $sCode = <<<BLAH
                        <div class="input_wrapper input_wrapper_{$aInput['type']}" $sAttr>
                            $sContent
                            <div class="input_close input_close_{$aInput['type']}"></div>
                        </div>
BLAH;
        }
        
        return $sCode;
    }
    
    function getActionsPanel(){
    	$this -> _oDb -> checkIfNewModulesInstalled();
		
		if (isset($_REQUEST['points-actions-save'])) 
   		{	
   			$this -> setActions();
   			$sMessage = MsgBox(_t('_aqb_points_successfully_saved'), 2);
   		}	
   		$aGroups = $this -> _oDb -> getInstalledModulesWithActions();
    	$aActionsGroups['inputs'] = array();

		$sAddAction = _t('_aqb_points_admin_action_add_new');
		$sDisableModule = _t('_aqb_points_admin_disable_module');
		
    	foreach($aGroups as $k => $v){
		    $iCount = $this -> _oDb -> getActionsCount($v['module_uri']) ;
			$sContent = '';
			
			if ($iCount)
				{
					$sStat = ':&nbsp;' . _t('_aqb_points_admin_actions_module_stat', $iCount, $this -> _oDb -> getActionsCount($v['module_uri'], true)) . '&nbsp;' . (!(int)$v['installed'] ? _t('_aqb_points_module_not_installed') : '');
					$sContent = $this -> getActions($v['module_uri']);
				}	
			else
				{
					$sStat = ':&nbsp;('._t('_aqb_points_module_no_actions').')';
				}	
	
				$sDisableLink = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'disable_module/' . $v['module_uri'];	
				$sAddActionLink = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'add_action_form/' . $v['module_uri'];	
				
				$sDisableConfrm = addslashes(_t('_aqb_points_admin_disable_module_confirm'));
				$sRedirect  = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/actions';
				
				$sLinks =<<<EOF
				<div class="points-links-item-left">
							<span class="points-admin-module-link"><a href="javascript:void(0);" onclick="javascript:AqbPointsManage.showPopup('{$sAddActionLink}');">{$sAddAction}</a></span>
							<span class="points-admin-module-link"><a href="javascript:void(0);" onclick="javascript:Profile.disableModule('{$sDisableLink}', '{$sDisableConfrm}', '{$sRedirect}');">{$sDisableModule}</a></span>
				</div>
EOF;
			$aActionsGroups['inputs'] = array_merge($aActionsGroups['inputs'], array( 
    	    	"{$v['module_uri']}_section_begin" => array(
                    'type' => 'block_header',
                    'caption' => '<b>'._t("_aqb_module_title_{$v['module_uri']}"). '</b>' . $sStat,
                    'collapsable' => true,
                    'collapsed' => true
        	 	),
				"{$v['module_uri']}_area" => array(
                    'type' => 'custom',
                    'content' => $sContent . $sLinks,
        	 	    'colspan' => true,
        	 	    'attrs_wrapper' => array('style' => 'float:none;')                                  
				),
				"{$v['module_uri']}_section_end" => array(
                    'type' => 'block_end'
        	 	)
    		   ));
    	
    	}
    	
    	$aForm = array(
     	'form_attrs' => array(
            'id' => 'points-actions-from',
			'name' => 'points-actions-form',
            'method' => 'post',
		    'enctype' => '',
			'action' => '',
	        ),
	        'params' => array (
                'db' => array(
                    'submit_name' => 'points-actions-save',
                )
        ));
		
        $aForm['inputs'] = $aActionsGroups['inputs'];
        $aForm['inputs']['save'] = array(
                    'type' => 'submit',
                    'name' => 'points-actions-save',
                    'value' => _t('_aqb_points_save_button'),
        			'colspan' => false,
                    'attrs' => array('style' => 'width:150px;') 
    	);

    	$oForm = new BxTemplFormView($aForm);
   		return $sMessage . $oForm -> getCode(); 	    	
    } 
    function getActions($sUri = ''){
    	$aActions = $this -> _oDb -> getActions($sUri);
    	
    	$aItems = array();
    	$oForm = new BxTemplFormView(array());

    	foreach($aActions as $k => $v){
    		$aLabel  = array('type' => 'value', 'value' => $v['title']);
    		$aPoints = array('type' => 'text',  'name' => 'action_value_'.$v['id'], 'value' => $v['points'], 'attrs' => array('style' => 'width:100px'), 'attrs_wrapper' => array('style' => 'width:100px;float:none;'));
    		$aLimit  = array('type' => 'text',  'name' => 'action_limit_'.$v['id'], 'value' => $v['day_limit'], 'attrs' => array('style' => 'width:100px'), 'attrs_wrapper' => array('style' => 'width:100px;float:none;'));
    		
    		$aItems[] = array(
    			'title' => _t($v['title']),
    			'points_num' => $this -> genWrapperInput($aPoints, $oForm -> genInput($aPoints)),
    		    'points_limit' => $this -> genWrapperInput($aLimit, $oForm -> genInput($aLimit)),
    		    'checked' => $v['active'] == 'true' ? 'checked="checked"' : '',  
				'row_id' => 'row_'.$v['id'],
				'delete'=> '<a href = "javascript:void(0);" onclick="javascript:Profile.removeAction(\''.'row_'.$v['id'].'\',\''. BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri().'delete_action/'.$v['id'].'\',\''.addslashes(_t('_aqb_points_admin_action_delete_confirm')).'\');">'._t('_aqb_points_block_delete').'</a>',	
    		    'id' => $v['id']);
    	}
    		
    	return $this -> parseHtmlByName('admin_actions', 
		array(
				 'bx_repeat:points_actions' => $aItems				 				 
			 ));
    }
	
    function setActions(){
    	$aEnabled = isset($_POST['points_actions']) && is_array($_POST['points_actions']) ? $_POST['points_actions'] : array(); 
    	$iCount = $this -> _oDb -> getActionsMaxId(); if (!$iCount) return false;
    	
		$this -> _oDb -> unactiveActions();		
		
		foreach($aEnabled as $key => $value){
				$this -> _oDb -> saveActions((int)$_POST['action_value_'.$value], (int)$_POST['action_limit_'.$value], 'true', $value);
			}
		return true;	
    } 
    
    function getInfoBlock($sProfileId = ''){
    	
    	$aGroupsWithActions = $this -> getActionsInfoArray();
    	$sActions = _t('_aqb_action_description');
    	$sPoints = _t('_aqb_action_points');
    	
    	$sResult = '';
     if ($this -> _oConfig -> isPointsInfoEnabled())
     {
    	foreach($aGroupsWithActions as $key => $value){
    		$sModuleTitle = _t('_aqb_module_title_'.$key);
    		
    		$sResult .=<<<EOF
    		<tr><td colspan = "2" class = "block_header">{$sModuleTitle}</td></tr>
    		<tr><th>{$sActions}</th><th>{$sPoints}</th></tr>
EOF;
    		foreach($value as $k => $v){
    		 $sActionName = _t($v['title']); 
    		 $sResult .= '<tr><td class="value">'.$sActionName.'</td><td class="value">'.$v['points'].'</td></tr>';	
    		}	
    	}
    }else  $sResult = '<tr><td colspan="2">'._t('_aqb_points_nothing_found').'</td></tr>';
    
    	return DesignBoxContent('', $this -> parseHtmlByName('actions_info', 
		array(
				 'activities_info' => _t('_aqb_activities_info'),
				 'activities_addon_info' =>  $this -> _oConfig -> isIncrementEnabled() ? '<a href = "javascript:void(0);" onclick = "javascript:AqbPointsManage.showPopup(\''.BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'show_increment_info' . '\');">' . _t('_aqb_points_activities_addon_info') . '</a>' : '',
				 'content' => $sResult,	
		)),1, $this -> getTopMenuHtml($this -> getMainMenu($sProfileId), 'actions'));	
    }
    
	function getPackages(){
		$aPackages = $this -> _oDb -> getPackages();
		if (empty($aPackages)) return array();
		
		$sPathBase = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();		
		$sPathModules = BX_DOL_URL_MODULES;
		
		$sImage = $this -> getIconUrl("shoppingcart.gif");
		$sButtonTitle = _t('_aqb_points_buy_now');
		
		$sButtons = '<table cellspacing="0" cellpadding="0" class="aqb-points-packages">';
		
		foreach($aPackages as $k => $v){
		$sPrice = _t('_aqb_points_package_title_buy_form', $v['points'], $this -> _oConfig -> getCurrencySign() . $v['price']);
		$sButtons .=<<<EOF
		<tr>
			<td>{$sPrice}</td>
			<td>
			<div class="button_input_wrapper">
		        <button onclick="javascript:AqbPointsManage.onBuyPackage('{$sPathBase}buy_package/{$v['points']}','{$sPathModules}?r=payment/cart/');" type="button">
						<img src="{$sImage}"><span style="margin:3px;">{$sButtonTitle}</span></button>
			</div>
			</td>	
		</tr>
EOF;
		
		}
		
		$sButtons .= '</table>';
		
		return $sButtons;
	}	
	
    function buyPointsBlock(){
   	
    $fPrice = $this -> _oConfig -> getPointsPrice();
   	if ($fPrice == 0) return;
   	
   	$sCurCode = $this -> _oConfig -> getCurrencySign();
    
   	$oFormInput = new BxTemplFormView(array()); 
    $aInput = array(
                'type' => 'text',
                'name' => 'points_num',
                'value' => 0,
				'info' =>  _t('_aqb_points_num_input_info'),
       		    'colspan' => true,
       		    'attrs' => array('onkeyup' => "javascript:AqbPointsManage.sumUpdate(this,{$fPrice});", 'id' => 'aqb_points_num')
    		);
   	  
      $sInputTitle = _t('_aqb_points_buy_caption');
      $sInputSum = _t('_aqb_points_buy_sum');
      
	  $sPackages = $this -> getPackages();
	  
      $sInput = $this -> genWrapperInput($aInput, $oFormInput -> genInput($aInput));

      $sInputArea=<<<EOF
      <div class="buy-form-item">{$sInputTitle}:</div>
      <div style = "float:left;">{$sInput}</div>
      <div class="buy-form-item">
      	&nbsp;<span >{$sInputSum}</span>&nbsp;
      	<span id="aqb_price_counter">0</span>{$sCurCode}
      </div>
EOF;
    	
    $aForm = array(
     	'form_attrs' => array(
            'id' => 'buy-points-form',
			'name' => 'points-ips-form',
            'method' => 'post',
		    'enctype' => 'multipart/form-data',
			'action' => '',
    		'onsubmit' => "javascript: AqbPointsManage.onSubmitPrice('".BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . "buy_points/', '" . BX_DOL_URL_MODULES . "?r=payment/cart/' , '" . addslashes(_t('_aqb_points_transaction_exists')) . "'); return false;"
        ),
		
        'table_attrs' => array(
			'cellpadding' => 0,
        	'cellspacing' => 0,
        	'class' => 'buy-points-table'
        ), 
		);
	 
	 $aForm['inputs'] = array();
	 if (!empty($sPackages))
        $aForm['inputs'] = array_merge($aForm['inputs'], array(
        'packages' => array(
                    'type' => 'custom',
                    'content' => $sPackages,
        			'colspan' => true,
       		)));
		
		$aForm['inputs'] = array_merge($aForm['inputs'], array(	
		'text' => array(
                    'type' => 'custom',
                    'content' => _t('_aqb_points_buy_description', $fPrice . $sCurCode),
        			'colspan' => true,
       		),
       	        
       	'text_input' => array(
                'type' => 'custom',
                'content' => $sInputArea,
       		    'colspan' => true,
        ),
       	
        'buy' => array(
                    'type' => 'submit',
                    'name' => 'points_buy_button',
                    'value' => _t('_aqb_points_buy_button'),
        			'attrs' => array('id' => 'aqb_buy_points_button')
    	)));
    
	$oForm = new BxTemplFormView($aForm);
	
	$aVars = array (
            'form' => $oForm -> getCode()
	 );
	
	 return PopupBox('aqb_popup', _t('_aqb_buy_points_title'), $this -> parseHtmlByName('num_points_block', $aVars));
   }

function getIncrementInfoBlock(){
	if (!$this -> _oConfig -> isIncrementEnabled()) return MsgBox(_t('_aqb_points_not_enabled'));
    
    $aInfo = $this -> _oDb -> getIncrementInfo();
	
	$aItems = array();
	foreach($aInfo as $key => $value){
		$aItems[] = array('mlevel_name' => $value['Name'], 'mlevel_increment' => $value['increment']); 
	}
    
	return  PopupBox('aqb_popup', _t('_aqb_points_activities_addon_info'), $this -> parseHtmlByName('increment_info', 
																													array(
																															'bx_repeat:membership_increment' => $aItems, 
																															'increment_info' => _t('_aqb_points_increment_info'))));
}

function getPresentPointsBlock($iProfileID){
	global $oFunctions;
   	$aProfileInfo = getProfileInfo($iProfileID); 
	
   	if ($iProfileID === false) return MsgBox('_aqb_points_member_not_found');
	
	$iPointsNum = $this -> _oDb -> getProfileTotalPointsNum(getLoggedId());
   	
   	$oFormInput = new BxTemplFormView(array()); 
    $aInput = array(
                'type' => 'text',
                'name' => 'points_num',
                'value' => 0,
       		    'colspan' => true,
       		    'attrs' => array('id' => 'aqb_points_num'),
    			'attrs_wrapper' => array('style' => 'width:100px;')
    		);
   	  
      
      $sInput = $this -> genWrapperInput($aInput, $oFormInput -> genInput($aInput));

      $sScriptUrl = $this -> _oConfig -> getHomeUrl() . 'js/main.js';
	  $sWrongPointsMessage = addslashes(_t('_aqb_points_wrong_present_points'));
	  $sConfirm = addslashes(_t('_aqb_points_confirm_present', '{0}'));
	  
	  $sUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'present_points/';
	  
$sInputArea=<<<EOF
      <script language="javascript" type="text/javascript" src="{$sScriptUrl}"></script>
EOF;
      
      $aForm = array(
     	'form_attrs' => array(
            'id' => 'buy-points-form',
			'name' => 'points-ips-form',
            'method' => 'post',
		    'enctype' => 'multipart/form-data',
			'action' => '',
    		'onsubmit' => "javascript: AqbPointsManage.onSubmitPresent('{$sWrongPointsMessage}', '{$sConfirm}', '{$sUrl}'); return false;"
        ),
		
        'table_attrs' => array(
			'cellpadding' => 0,
        	'cellspacing' => 0,
        	'class' => 'buy-points-table'
        ), 

        'inputs' => array(
        'text' => array(
                    'type' => 'custom',
                    'content' => $sInputArea . _t('_aqb_points_i_have', $iPointsNum),
        			'colspan' => true,
       		),
       	        
       	'text_input' => array(
                'type' => 'custom',
       		    'content' => $sInput,
       			'caption' => _t('_aqb_points_buy_caption'),
				'info' =>  _t('_aqb_points_present_input_info'),
       		    'colspan' => false,
    		),
	   
    	'profile_id' => array(
                'type' => 'hidden',
                'value' => (int)$iProfileID,
       		    'name' => 'aqb_profile_id',
				'attrs' => array('id' => 'aqb_profile_id')
        ),
        
        'curr_balance' => array(
                'type' => 'hidden',
                'value' => (int)$iPointsNum,
       		    'name' => 'aqb_current_balance',
				'attrs' => array('id' => 'aqb_current_balance')
        ),
       	
        'present' => array(
                    'type' => 'submit',
                    'name' => 'aqb_present_points_button',
                    'value' => _t('_aqb_points_present_button'),
        			'attrs' => array('id' => 'aqb_present_points_button')
    	)));
    
	$oForm = new BxTemplFormView($aForm);
	$aVars = array (
            'form' => $oForm -> getCode()
	 );
	
	 $aVarsPopup = array (
            'title' =>  _t('_aqb_points_present_link'),
            'content' => $this -> parseHtmlByName('num_points_block', $aVars),
      );  
	 
     return $oFunctions -> transBox($this -> parseHtmlByName('popup', $aVarsPopup), true);
    }
   
	function getProfileHistoryBlock($iProfileId , $isAdmin = false, &$aPageSettings){
       	$oForm = new BxTemplFormView(array()); 
   	 	$aSubmit = array(
		                   'type' => 'submit',
		                   'name' => 'points-show-history',
		                   'value' => _t('_aqb_points_show')
   	 					);
   	 	
   	 	$sBuyLink = '<a href = "javascript:void(0);" onclick = "javascript:AqbPointsManage.showPopup(\''.(BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri().'buy_form/').'\');">'._t('_aqb_points_buy').'</a>';
		if ($isAdmin) $aProfileInfo = getProfileInfo($iProfileId);
		
		$sRequest = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . "history/{$iProfileId}/"; 
		$sRequest = $sRequest . '{page}/{activities}/{days}';//{per_page}/

		$sRequest = str_replace('{activities}', $aPageSettings['activities'], $sRequest);
		$sRequest = str_replace('{days}', (!(int)$aPageSettings['days'] ? '' : (int)$aPageSettings['days']), $sRequest);
		
		$aHistory = array('days' => (int)$aPageSettings['days'], 'activities' => $aPageSettings['activities']);
		
		$iTotalNum = $this -> _oDb -> getHistoryCount((int)$iProfileId, $aHistory);
		
		$iPerPage = (int)$aPageSettings['per_page'];
		$iCurPage = (int)$aPageSettings['page'];

		if( $iCurPage < 1 )
			$iCurPage = 1;

		$iLimitFrom = ( $iCurPage - 1 ) * $iPerPage;

		// gen pagination block ;
		$oPaginate = new BxDolPaginate
		(
			array
			(
				'page_url'   => $sRequest,
				'count'      => $iTotalNum,
				'per_page'   => $iPerPage,
				'page'               => $iCurPage,
				'per_page_changer'   => false,
				'page_reloader'      => true,
				'on_change_page'     => null,
				'on_change_per_page' => null
			)
		);
		
		$sPagination = $oPaginate -> getPaginate();
 		
		$aHistory = array_merge($aHistory, array('isAdmin' => $isAdmin, 'from' => (int)$iLimitFrom, 'to' => (int)$iPerPage));
		
   	 	return DesignBoxContent('', $this -> parseHtmlByName('profile_stat', 
		array(
				 'from_begin' => $isAdmin ? '<form class="form_advanced" enctype="multipart/form-data" method="post" action="'.BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri().'history/'.$iProfileId.'" id="points-history-form" onsubmit="return confirm(\''.addslashes(_t('_aqb_points_confirm_delete')).'\');">' : '',
				 'from_end' => $isAdmin ? '</from>' : '',
				 'bx_repeat:info' => $this -> getProfileHistoryArray($iProfileId, $aHistory),
				 'select_actions' => $this -> getSelectOfActionsByGroups($aPageSettings['activities']),
				 'cur_balance' => _t('_aqb_current_balance'),
				 'activities' => !$isAdmin ? _t('_aqb_activities') : _t('_aqb_points_admin_activities' , $aProfileInfo['NickName']."'s"),
				 'total_points_num' => 	$this -> _oDb -> getProfileTotalPointsNum($iProfileId),
				 'admin_col_head' => $isAdmin ? '<th></th>' : '',
				 'admin_button' => $isAdmin ? '<tr><td style="text-align:left;width:70px;"><input type="checkbox" onclick="$(\'#points-history-form input[name=\\\'history[]\\\']:enabled\').attr(\'checked\', this.checked)" class="admin-actions-select-all">'._t('_aqb_points_select_all').'</td><td colspan="4" style="text-align:left;">
												  <input type = "submit" name="history_delete" value="'._t('_aqb_points_block_delete').'"></td></tr>' : '',
				 'cur_date' => $this -> _oDb -> getCurrentDate(),
				 'show' => _t('_aqb_actions_display_title'),
				 'during_period' => _t('_aqb_during_period'),
				 'exchange_points_to_cache' => $this -> getExchangeButton(),
				 'select_during_with' => $this -> getSelectDateInrevals((int)$aPageSettings['days']),
				 'submit' => $oForm -> genInput($aSubmit),
				 'paginate' => 	$sPagination,
				 'URL' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . "history/{$iProfileId}/",	
				 'buy_link' => $isAdmin ? $sBuyLink . '&nbsp;&nbsp;&nbsp;<a href = "'. BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/members">'._t('_aqb_points_admin_go_back_to_members').'</a>' : $sBuyLink 
		)),1, $this -> getTopMenuHtml($this -> getMainMenu($iProfileId)));				
    }
        
	function getExchangeButton(){
		$iPoints = $this -> _oDb -> getProfileTotalPointsNum(getLoggedId());

		if (!$this -> _oConfig -> isExchangePointsToCacheEnabled() || !(int)$iPoints) return '';
		
		$sButtonTitle = _t('_aqb_exchange_points_to_money_title');
		
	   	$iPointsLimit = $this -> _oConfig -> getExchangePointsLimit();
		$fPrice = $this -> _oConfig -> priceForExchangeToPoints();
		
		$sPathBase = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();		
		$sMessage = bx_js_string(_t('_aqb_points_send_exchange_request', $iPoints, $this -> _oConfig -> getCurrencySign(), round($fPrice*$iPoints,2)));
		
		$sImage = $this -> getIconUrl("exchange_to_money.png");
		$sButton =<<<EOF
		<div class="button_input_wrapper">
	        <button onclick="javascript:AqbPointsManage.onExchangePointsToMoney('{$sPathBase}check_exchange_request','{$sMessage}', '{$sPathBase}exchange_to_money');" type="button">
					<img src="{$sImage}"><span>{$sButtonTitle}</span></button>
		</div>
EOF;
		return $sButton;
	}	
    
    function getTopMenuHtml($aTopMenu, $sAction = '') 
    {
        $aItems = array();
        $sAction = $sAction ? $sAction : 'history';
        foreach ($aTopMenu as $sName => $aItem)
        {
           	$aItems[$aItem['title']] = array(                
                'dynamic' => $aItem['dynamic'] ? $aItem['dynamic'] : false,
                'active' => ($sName == $sAction ? 1 : 0),
                'href' => $aItem['href']
            );
        }        
        return BxDolPageView::getBlockCaptionItemCode(0, $aItems);
    }

    function getMainMenu($sProfile = ''){
		return $aTopMenu = array(            
	        'history' => array('href' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'history/'. $sProfile,  'title' => _t('_aqb_points_history'), 'active' => '1'),
		    'actions' => $this -> _oConfig -> isPointsInfoEnabled() ? array('href' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'history/actions/' . $sProfile,  'title' => _t('_aqb_points_actions_info'), 'active' => '0') : array()
	     );	    
	}	
	
	function getActionsInfoArray(){
    	$aGroups = $this -> _oDb -> getInstalledModulesWithActions();	
       	if (count($aGroups) == 0) return '';
		
       	$aResult = array();	
       	foreach($aGroups as $k => $v){
    	   $aActions = $this -> _oDb -> getActions($v['module_uri']);
    	   if (count($aActions) < 1) continue;
		   
    	   $aResultActions = array();		   
		   
		   foreach($aActions as $key => $value){
    	 	  if (!(int)$value['points'] || !(int)$value['day_limit'] || $value['active'] != 'true') continue;
			  $aResultActions[] = array('points' => $value['points'], 'title' => _t($value['title']));
    	   }
    	   
    	   $aResult[$v['module_uri']] = $aResultActions;  
    	}
    	
    	return $aResult;
    }
    
    function getProfileHistoryArray($iProfileId, &$aHistory){
    $aProfileInfo = $this -> _oDb -> getProfileHistory($iProfileId, $aHistory);
	$iSum = $this -> _oDb -> getProfilePointsNum($iProfileId, $aHistory);
    $aProfileStat = array();
   
	foreach($aProfileInfo as $k => $v){
		$aProfileStat[] = array(
				    			'date' => $v['formated_date'],
				    			'description' => _t($v['reason']),
				    		    'points' => $v['points'],
				    		    'balance' => $iSum,
		            			'admin_col_check' => $aHistory['isAdmin'] ? '<td class="value" style="text-align:center;"><input type="checkbox" style = "mergin-left:10px;" value="'.$v['id'].'" name = "history[]"/></td>' : '',	
				   		        );
		$iSum -=(int)$v['points'];
	  }
    
	  return $aProfileStat;
    }
    
 	
    function getSelectDateInrevals($iVal = 0){
	 	$aSelectOptions[] = array('key' => '0', 'value' => _t('_aqb_all_days'));
	 	$aSelectOptions[] = array('key' => '10', 'value' => _t('_aqb_last_days', 10));
	 	$aSelectOptions[] = array('key' => '30', 'value' => _t('_aqb_last_days', 30));
	 	$aSelectOptions[] = array('key' => '60', 'value' => _t('_aqb_last_days', 60));
	 	$aSelectOptions[] = array('key' => '90', 'value' => _t('_aqb_last_days', 90));
    	$aSelect = array('type' => 'select',  'name' => 'action_time_submit', 'value' => (int)$iVal,'values' => $aSelectOptions, 'attrs' => array('class' => 'points-actions-time-select'));
   	
    	$oForm = new BxTemplFormView(array()); 
    	return $oForm -> genInput($aSelect);
	}
    
	function getSelectOfActionsByGroups($sVal = 'all'){
    	if ($sVal != 'all' && !(int)$sVal) $sVal = 'all';
    	
		$aGroups = $this -> _oDb -> getInstalledModulesWithActions();	
       	if (count($aGroups) == 0) return '';

       	$aSelectOptions[] = array('key' => 'all', 'value' => _t('_aqb_all_activities'));
       	$i = 0;

       	foreach($aGroups as $k => $v){
    	   $aActions = $this -> _oDb -> getActions($v['module_uri']);
    	   if (empty($aActions)) continue;

    	   if ($i) $aSelectOptions[] = array('key' => '', 'value' => '');
	      	   
       	   $i++; 
    	   
		   $aSelectOptionsValues = array();
       	   foreach($aActions as $key => $value){
    	 	  if ($value['active'] == 'true')
					$aSelectOptionsValues[] = array('key' => $value['id'], 'value' => _t($value['title']));
    	   }  
		
		   if (empty($aSelectOptionsValues)) continue;			
		   
		   $aSelectOptions = array_merge($aSelectOptions, array(array('key' => $v['module_uri'], 'value' => ' ------ ' . _t('_aqb_module_title_' . $v['module_uri']) . ' ------ ')), $aSelectOptionsValues);
    	}
    	
    	$aSelect = array('type' => 'select',  'name' => 'action_submit', 'values' => $aSelectOptions, 'value' => $sVal,'attrs' => array('class' => 'points-actions-select'));
    	$oForm = new BxTemplFormView(array()); 	
    	return $oForm -> genInput($aSelect);
    }

    function getPointsLeadersPagesBlock(&$aDisplaySettings) {
		// lang keys ;
		$sPhotoCaption  = _t( '_With photos only' );
		$sOnlineCaption = _t( '_online only' );
		$sSimpleCaption = _t( '_Simple' );
		$sExtendCaption = _t( '_Extended' );
		
		$sTemplateName = ($aDisplaySettings['mode'] == 'extended') ? 'points_profiles_ext' : 'points_profiles_sim';
		$iIndex = 0;

		$_GET['mode'] = $aDisplaySettings['mode'];
		// need for the block divider ;

		$aResult = $this -> _oDb -> getLeadersPage($aDisplaySettings);
		if (!$aResult['rData']) $sOutputHtml = MsgBox(_t( '_Empty' ));
		
		$iTotalNum = $aResult['count'];
		 	
		$aRow = array();
		
		if ($aResult['rData'])
		while( $aRow = mysql_fetch_assoc($aResult['rData']) ) {
			// generate the `couple` thumbnail ;
			$aRow['points'] = $aRow['points'] ? _t('_aqb_points_number', $aRow['points']) : _t('_aqb_points_number', 0);
				
			if ( $aRow['Couple']) {        
				$aCoupleInfo = getProfileInfo( $aRow['Couple'] );
				if ( !($iIndex % 2)  ) {
					$sOutputHtml .= $this -> oSearchProfileTmpl -> PrintSearhResult($aRow, $aCoupleInfo, array('points' => $aRow['points'], 'ext_css_class' => ''), $sTemplateName, $this);
				} else {
					// generate the filled block ;
					$sOutputHtml .= $this -> oSearchProfileTmpl -> PrintSearhResult($aRow, $aCoupleInfo, array('points' => $aRow['points'], 'ext_css_class' => 'search_filled_block'), $sTemplateName, $this);
				}
			} else { // generate the `single` thumbnail ;
				if ( !($iIndex % 2)  ) {
					$sOutputHtml .= $this -> oSearchProfileTmpl -> PrintSearhResult($aRow, '', array('points' => $aRow['points'], 'ext_css_class' => ''), $sTemplateName, $this);
				} else {
					// generate the filled block ;
					$sOutputHtml .= $this -> oSearchProfileTmpl -> PrintSearhResult($aRow, '', array('points' => $aRow['points'], 'ext_css_class' => 'search_filled_block'), $sTemplateName, $this);
				}
			}
			$iIndex++;
		}

		$sRequest = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'leaders/' . $aDisplaySettings['mode'] . '/'; 
		$sRequest = getClearedParam( 'sort',  $sRequest);

		$sRequest = $sRequest . '{sorting}/{page}/{per_page}/{with_photos}/{online}';
			
		$sRequest = str_replace('{with_photos}', $aDisplaySettings['with_photos'], $sRequest);
		$sRequest = str_replace('{online}', $aDisplaySettings['online'], $sRequest);
			
		// gen pagination block ;
		$oPaginate = new BxDolPaginate
		(
			array
			(
				'page_url'   => $sRequest,
				'count'      => $iTotalNum,
				'per_page'   => (int)$aDisplaySettings['per_page'],
				'sorting'    => $aDisplaySettings['sort'],
				'page'               => (int)$aDisplaySettings['page'],
				'per_page_changer'   => false,
				'page_reloader'      => true,
				'on_change_page'     => null,
				'on_change_per_page' => null,
		
				'per_page_step'      => ( $aDisplaySettings['mode'] == 'extended' ) ? 5 : 16,
			)
		);

		$sRequest = str_replace('{page}', (int)$aDisplaySettings['page'], $sRequest);
		$sRequest = str_replace('{per_page}', (int)$aDisplaySettings['per_page'], $sRequest);
		$sRequest = str_replace('{sorting}', $aDisplaySettings['sort'], $sRequest);
			
		$sPagination = $oPaginate -> getPaginate();
 		$sPerPageBlock = $oPaginate -> getPages( (int)$aDisplaySettings['per_page'] ); 

		// prepare to output ;
		$sOutputHtml .='<div class="clear_both"></div>';

		// fill array with sorting params ;
		$aSortingParam = array
		(
			'points_up'     => _t( '_aqb_up_points' ),
			'points_down'     => _t( '_aqb_down_points' )
		);

		// gen sorting block ( type of : drop down ) ;

		$sSortBlock = $oPaginate -> getSorting($aSortingParam, $aDisplaySettings['sort']);

		$sPhotosChecked = ((int)$aDisplaySettings['with_photos']) ? 'checked="checked"' : null;
		$sOnlineChecked = ((int)$aDisplaySettings['online']) ? 'checked="checked"' : null;
		
			$sRequest = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'leaders/' . $aDisplaySettings['mode']; 
			$sRequestOnline = $sRequest . '/'.$aDisplaySettings['sort'].'/'.$aDisplaySettings['page'].'/'.$aDisplaySettings['per_page']. '/' . $aDisplaySettings['with_photos'] .'/'. ((int)$aDisplaySettings['online'] ? 0 : 1);
			$sRequest = $sRequest . '/'.$aDisplaySettings['sort'].'/'.$aDisplaySettings['page'].'/'.$aDisplaySettings['per_page']. '/' .((int)$aDisplaySettings['with_photos'] ? 0 : 1) .'/'. $aDisplaySettings['online'];

		$sPhotosChecked .= " onclick=\"javascript:window.location = '{$sRequest}'\"";
		$sOnlineChecked .= " onclick=\"javascript:window.location = '{$sRequestOnline}'\"";

		$sModeLocation = getClearedParam( 'mode',  $sRequest);
		$sModeLocation = getClearedParam( 'per_page',  $sModeLocation);

		// ** gen header part - with some display options ;

		// fill array with template's keys ;
		$aTemplateKeys = array
		(
			'sort_block'      => $sSortBlock,
			'photo_checked'   => $sPhotosChecked,
			'photo_caption'   => $sPhotoCaption,
			'online_checked'  => $sOnlineChecked,
			'online_caption'  => $sOnlineCaption,
			'per_page_block'  => $sPerPageBlock,
			'searched_data'   => $sOutputHtml,
			'pagination'      => $sPagination,
		);    

		// build template ;
		$sOutputHtml = $this -> parseHtmlByName('browse_searched_block', $aTemplateKeys );
		
		// generate toggle ellements ;
		$aToggleItems = array
		(
			'simple'             =>  _t( '_Simple' ),
			'extended'     =>    _t( '_Extended' ),
		);
	
		
		foreach( $aToggleItems as $sKey => $sValue ) {
			$aToggleEllements[$sKey] = array
			(
				'href' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'leaders/' . $sKey .'/',
			    'title' => $sValue
			);
		}
		return DesignBoxContent(_t('_aqb_browse_points_leaders'), $sOutputHtml, 1, $this -> getTopMenuHtml($aToggleEllements, $aDisplaySettings['mode']));
	}
	
 function getPointsLeadersBlock ($sBlockName) {
        global $oFunctions;
        $aData = array();
        $sPaginate = '';
  
		$aDBTopMenu = array();
			
 	    $iLimit = $this -> _oConfig -> getPageNumLeadersBlock();	
 	    
		$aModes = array('lower', 'upper', 'online');
        $sMode = (in_array($_GET[$sBlockName . 'Mode'], $aModes)) ? $_GET[$sBlockName . 'Mode'] : $sMode = 'upper';
        foreach( $aModes as $sMyMode ) {
            $aDBTopMenu[_t('_aqb_'.$sMyMode)] = array('href' => "{$_SERVER['PHP_SELF']}?{$sBlockName}Mode=$sMyMode", 'dynamic' => true, 'active' => ( $sMyMode == $sMode ));
        }
        
        $aProfiles = $this -> _oDb -> getMembersForPointsLeadersBlock((int)$_REQUEST['page'], $sMode, $iLimit);
        $iCount = $aProfiles !== false ? (int)$aProfiles['icount']: 0;
         
        if ($iCount){   
			$iNewWidth = BX_AVA_W + 6;
        	$aOnline = array();
            foreach($aProfiles['data'] as $k => $v) {
            	$v['points'] = _t('_aqb_points_number', $v['points']);
                $sCode .= '<div class="featured_block_1" style="width:'.$iNewWidth.'px;">';
                $aOnline['is_online'] = $v['is_online'];
                $sCode .= '<div class="thumb_username">'.$v['points'].'</div>';
                $sCode .= get_member_thumbnail($v['ID'], 'none', true, 'visitor', $aOnline);
                $sCode .= '</div>';
            }			
			$sCode = $oFunctions -> centerContent($sCode, '.featured_block_1');
            
			$iPages = ceil($iCount/ (int)$iLimit);
         
            if ($iPages > 1) {
                $oPaginate = new BxDolPaginate(array(
                    'page_url' => 'index.php',
                    'count' => $iCount,
                    'per_page' => $iLimit,
                    'page' =>  $aProfiles['ipage'],
                    'per_page_changer' => true,
                    'page_reloader' => true,
                    'on_change_page' => 'return !loadDynamicBlock({id}, \''.$_SERVER['PHP_SELF'].'?'.$sBlockName.'Mode='.$sMode.'&page={page}\')',
                    'on_change_per_page' => ''
                ));
                $sPaginate = $oPaginate -> getSimplePaginate(BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'leaders/');
            }    
        } else {
            $sCode = MsgBox(_t("_Empty"));
        }
        return array($sCode, $aDBTopMenu, $sPaginate);
    }
    /**********************************************************************/
    
	function getAddActionBlock($sUri){
  	if (!$sUri) return false;;
    
	$sWrongCreateMessage = addslashes(_t('_aqb_points_action_empty_title_or_name'));
	
	$sUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'create_action/'.$sUri;
    $sRedirect = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/actions';
  
    $aForm = array(
     	'form_attrs' => array(
            'id' => 'buy-points-form',
			'name' => 'points-ips-form',
            'method' => 'post',
		    'enctype' => 'multipart/form-data',
			'action' => '',
    		'onsubmit' => "javascript: Profile.onSubmitAction('{$sWrongCreateMessage}', '{$sUrl}', '{$sRedirect}'); return false;"
        ),
		
        'table_attrs' => array(
			'cellpadding' => 0,
        	'cellspacing' => 0,
        	'class' => 'buy-points-table'
        ), 

        'inputs' => array(
       	
		'text_input_action_alert' => array(
                'type' => 'text',
                'name' => 'action_alert',
                'value' => '',
				'required' => true,
       			'caption' => _t('_aqb_points_action_alert'),
				'info' => _t('_aqb_points_action_alert_info'),
       		    'attrs' => array('id' => 'aqb_points_action_alert'),
    	),
		
		'text_input_action_alert_name' => array(
                'type' => 'text',
                'name' => 'action_alert_name',
                'value' => '',
				'required' => true,
       			'caption' => _t('_aqb_points_action_alert_name'),
				'info' => _t('_aqb_points_action_alert_name_info'),
       		    'attrs' => array('id' => 'aqb_points_action_alert_name'),
    	),
		
		'text_input_action_title' => array(
                'type' => 'text',
                'name' => 'action_title',
                'value' => '',
				'required' => true,
       			'caption' => _t('_aqb_points_action_title'),
				'info' => _t('_aqb_points_action_title_info'),
       		    'attrs' => array('id' => 'aqb_points_action_title'),
    	),
		
		'text_input_points' => array(
                'type' => 'text',
                'name' => 'points_num',
                'value' => 0,
       		    'colspan' => false,
    			'caption' => _t('_aqb_points_num'),
	   		    'attrs' => array('id' => 'aqb_points_num'),
    	),
    	
		'text_input_action_limit' => array(
                'type' => 'text',
                'name' => 'points_limit',
                'value' => 0,
       		    'colspan' => false,
    			'caption' => _t('_aqb_points_limit'),
	   		    'attrs' => array('id' => 'aqb_points_limit'),
    	),
		
    	'action_checked' => array(
                'type' => 'checkbox',
                'name' => 'enabled_check',
				'checked' => true,
        		'caption' => _t('_aqb_points_active'),
	   		    'attrs' => array('id' => 'aqb_points_check_id'),
    	),
	           	
        'create' => array(
                    'type' => 'submit',
                    'name' => 'aqb_create_action',
                    'value' => _t('_aqb_points_admin_create_button'),
        			'attrs' => array('id' => 'aqb_create_action')
    	)));
    
	 $oForm = new BxTemplFormView($aForm);
	 return PopupBox('aqb_popup', _t('_aqb_points_admin_create_action'), $oForm -> getCode());
	}
	
	function getGivePointsBlock($iProfileID, $sAction = 'give'){
   	$aProfileInfo = getProfileInfo($iProfileID); 
   	if ($iProfileID === false) return MsgBox('_aqb_points_member_not_found');
  	
   	$oFormInput = new BxTemplFormView(array()); 
    
	$sWrongPointsMessage = addslashes(_t('_aqb_points_wrong_present_points'));
	$sConfirm = addslashes(_t('_aqb_points_admin_confirm_empty_reason'));
	$sUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'give_points/'.$sAction;
  
    $aForm = array(
     	'form_attrs' => array(
            'id' => 'buy-points-form',
			'name' => 'points-ips-form',
            'method' => 'post',
		    'enctype' => 'multipart/form-data',
			'action' => '',
    		'onsubmit' => "javascript: Profile.onSubmitAdminPoints('{$sWrongPointsMessage}', '{$sConfirm}', '{$sUrl}'); return false;"
        ),
		
        'table_attrs' => array(
			'cellpadding' => 0,
        	'cellspacing' => 0,
        	'class' => 'buy-points-table'
        ), 

        'inputs' => array(
       	'text_input' => array(
                'type' => 'text',
                'name' => 'points_num',
                'value' => 0,
       		    'colspan' => false,
    			'caption' => _t('_aqb_points_buy_caption'),
				'info' =>  _t('_aqb_points_present_input_info'),
       		    'attrs' => array('id' => 'aqb_points_num'),
    	),
    	
    	'text_reason_input' => array(
                'type' => 'text',
                'name' => 'points_reason',
                'value' => '',
       			'caption' => _t('_aqb_points_admin_reason'),
				'info' => _t('_aqb_points_admin_reason_info'),
       		    'attrs' => array('id' => 'aqb_points_reason','SIZE' => '50', 'MAXLENGTH' => 255),
    	),
	   
    	'profile_id' => array(
                'type' => 'hidden',
                'value' => (int)$iProfileID,
       		    'name' => 'aqb_profile_id',
				'attrs' => array('id' => 'aqb_profile_id')
        ),
        
        'action' => array(
                'type' => 'hidden',
                'value' => $sAction,
       		    'name' => 'aqb_action',
				'attrs' => array('id' => 'aqb_action')
        ),
       	
        'send' => array(
                    'type' => 'submit',
                    'name' => 'aqb_send_points',
                    'value' => _t('_aqb_points_admin_send_points'),
        			'attrs' => array('id' => 'aqb_send_points')
    	)));
    
	$oForm = new BxTemplFormView($aForm);
	$aVars = array (
            'form' => $oForm -> getCode()
	 );
	
	 $aVarsPopup = array (
            'title' =>  _t('_aqb_points_present_link'),
            'content' => $this -> parseHtmlByName('num_points_block', $aVars),
      );  
	 
     if ($sAction == 'penalty')
      	$sTitle = _t('_aqb_points_admin_penalty_for', $aProfileInfo['NickName']);
     else
     	$sTitle = _t('_aqb_points_admin_bonus_for', $aProfileInfo['NickName']); 	
       
     return PopupBox('aqb_popup', $sTitle, $this -> parseHtmlByName('num_points_block', $aVars));
    }
    	
	function getMembershipPanel($aParam){
		$aResult = array();
	    $aItems = $this -> _oDb -> getMembershipLevels();
		
		foreach($aItems as $aItem)
		{
			if ((int)$aParam['ID'] == (int)$aItem['ID']) $sTitle = '<b>' . strtolower($aItem['Name']) .'</b>';
			else $sTitle = strtolower($aItem['Name']);
			$aResult[] = array('link' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/membership/' . $aItem['ID'], 'on_click' => '', 'title' => $sTitle, 'class' => ($aItem['Active'] == 'yes' ? 'active' : 'not_active'));
		}	
	
		$sMemBlock = $this->parseHtmlByName('memberships', array('bx_repeat:membership_links' =>  $aResult));
	
		return $sMemBlock ;	
	}
	
	function getMemLevelsBlocks(&$aParam)
	{
		$aMembershipInfo = getMembershipInfo($aParam['ID']);
		
		if (isset($aParam['points-submit-settings']) && $this -> _oDb -> saveMemlevelsSettings($aParam)) $sMessageSettings = MsgBox(_t('_aqb_points_successfully_saved')); 
		if (isset($aParam['points-mlevels-prices-add'])) $this -> _oDb -> saveMemlevelsPricing($aParam);
		if (isset($aParam['points-mlevels-prices-delete'])) foreach($_POST['prices'] as $iId) $this -> _oDb -> deleteMemlevelsPricing((int)$iId);
		
		$aMemSettings = $this -> _oDb -> getMemLevelsSettings($aParam['ID']);
		$aMemPricing = $this -> _oDb -> getMemLevelsPricing($aParam['ID']);
		
		$aForm = array(
        'form_attrs' => array(
            'id' => 'points-mlevel-settings-form',
            'action' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri().'administration/membership/'.$aParam['ID'],
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ),
		
		'inputs' => array (
    	
		'limit_points' => array (
			'type' => 'text',
	                'name' => 'points_increment',
	                'caption' => _t('_aqb_points_admin_increment'),
	                'value' => (int)$aMemSettings['increment'],
					'info' =>  _t('_aqb_points_admin_increment_info')
	        ),
	            
		'maximum_points_per_day' => array (
			'type' => 'text',
	                'name' => 'maximum_per_day_points',
	                'caption' => _t('_aqb_points_admin_maximum_per_day_for_level'),
	                'value' => (int)$aMemSettings['maximum_per_day'],
					'info' =>  _t('_aqb_points_admin_maximum_per_day_for_level_info')
	        ),	
			
		'maximum_input' => array (
			'type' => 'text',
	                'name' => 'maximum_points',
	                'caption' => _t('_aqb_points_admin_maximum_for_level'),
	                'value' => (int)$aMemSettings['maximum'],
					'info' =>  _t('_aqb_points_admin_maximum_for_level_info')
	        ),
        
		'submit' => array (
					'type' => 'submit',
	                'name' => 'points-submit-settings',
	                'value' => _t('_aqb_points_save_button'),

	        )
	    )
        
	);
		
		$oForm = new BxTemplFormView($aForm);
			    
		if ((int)$aParam['ID'] > 1)
	    {	
		    $aButtons = array(
		        'points-mlevels-prices-delete' => _t('_aqb_points_block_delete')
		    );    
	
			foreach($aMemPricing as $aPrice)
				$aItems[] = array(
				'id' => $aPrice['id'],
				'title' => _t('_aqb_points_mlevels_price_item', (!(int)$aPrice['days'] ? _t('_aqb_points_admin_unlimited_level') : $aPrice['days'] ) , $aPrice['points']),
			);			
			
		    $sControls = BxTemplSearchResult::showAdminActionsPanel('points-mlevels-prices-form', $aButtons, 'prices');
	    
			$sMemPrices = $this->parseHtmlByName('memlevels_prices', 
												array(
												        'controls' => $sControls,
														'bx_repeat:items' => $aItems 
	    										 ));
			$sPricingBlock = DesignBoxContent(_t('_aqb_points_admin_melevels_pricing', $aMembershipInfo['Name']), $sMemPrices, 1);
	    }									 
			
	  return DesignBoxContent(_t('_aqb_points_admin_melevel_seetings', $aMembershipInfo['Name']), $sMessageSettings . $oForm -> getCode(), 1) . $sPricingBlock; 
	}
	
	function getPackagesPanel(){
		if (isset($_POST['package-add'])) $this -> _oDb -> addPackage();
		if (isset($_POST['package-delete']) && is_array($_POST['packages'])) $this -> _oDb -> deletePackage();
		
		$aPackages = $this -> _oDb -> getPackages();
		$aButtons = array(
		        'package-delete' => _t('_aqb_points_price_delete')
		    );  
		
		if (!empty($aPackages)){	
			$sControls = BxTemplSearchResult::showAdminActionsPanel('packages-form', $aButtons, 'packages');
		    
			$aItems = array();
			if (!empty($aPackages))
			foreach($aPackages as $aPrice)
					$aItems[] = array(
								'points' => $aPrice['points'],
								'title' => _t('_aqb_points_package_title', $aPrice['points'], $this -> _oConfig -> getCurrencySign() . $aPrice['price'])
				);	
		}	
		
		return $sMemPrices = $this->parseHtmlByName('packages', 
												array(
												        'controls' => $sControls,
														'bx_repeat:items' => $aItems 
	    										 ));
	}
	
	function getEditSettingsArea(){
	
		$sSaved = $this -> _oDb -> saveMessages($_POST) ? MsgBox(_t('_aqb_points_txt_successfully_saved')) : ''; 
		return DesignBoxAdmin(_t('_aqb_points_settings'), $this -> parseHtmlByName('messages.html', 
		array(
				'msgbox'	=> $sSaved,
				'messages' => $this -> getBanMessagesForm(),
			)), $this -> getMainMenu());
	
	}
		
	function getBlockSearch() {
    
	$aForm = array(
        'form_attrs' => array(
            'id' => 'points-search-form',
            'action' => $_SERVER['PHP_SELF'],
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ),
        'inputs' => array (
            'points-filter-input' => array(
                'type' => 'text',
                'name' => 'points-filter-input',
                'caption' => _t('_aqb_points_txt_filter'),
                'value' => '',
				'info' =>  _t('_aqb_points_txt_filter_info')
 			),
            'search' => array(
                'type' => 'button',
                'name' => 'search',
                'value' => _t('_aqb_points_txt_search'),
                'attrs' => array(
                    'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeFilterSearch()'
                )
            ), 
       )
    );

    $oForm = new BxTemplFormView($aForm);
    return $oForm->getCode();
}

	function getCodeMembers(){
	//--- Get Controls ---//
    $aButtons = array(
    	'aqb-points-delete' => _t('_aqb_points_block_delete')
    );    
    
	$sControls = BxTemplSearchResult::showAdminActionsPanel('points-members-form', $aButtons, 'members');
       
    $oPaginate = new BxDolPaginate(array(
        'per_page' => BX_DOL_ADM_MP_PER_PAGE,
        'per_page_step' => BX_DOL_ADM_MP_PER_PAGE_STEP,
        'on_change_per_page' => BX_DOL_ADM_MP_JS_NAME . '.changePerPage(this);'
    ));    

    $aResult = array(
        'per_page' => $oPaginate->getPages(),
        'control' => $sControls,
		'links' => '<b><a href="javascript:void(0);" onclick="javascript:Profile.showAll();">'._t('_aqb_points_txt_show_all_members').'</a></b>&nbsp;&nbsp;|&nbsp;&nbsp;<b><a href="javascript:void(0);" onclick="javascript:Profile.cleanProfileHistory(\''.BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'clean_all_history/'.'\',\''.addslashes(_t('_aqb_points_confirm_clean')).'\');">'._t('_aqb_points_txt_clean_all_members').'</a></b>'
    );

    $aResult = array_merge($aResult, array('style_common' => '', 'content_common' => $this -> getMembersPanel()));
   	 
	return $this -> parseHtmlByName('admin_main', 
	array(
				'search' => $this -> getBlockSearch(), 
				'members' =>$this  -> parseHtmlByName('members', $aResult),
			    'obj_name' => BX_DOL_ADM_MP_JS_NAME,
				'actions_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(),
			    'sel_control' => '',
			    'sel_view' => '',
			    'per_page' => '32',
			    'order_by' => '',
				'loading' => LoadingBox('points-members-loading')			
		));
			
}
		
	function getMembersPanel($aParpoints = array()) {

	if (empty($aParpoints['view_type'])) $aParpoints['view_type'] = 'ASC';
	if(!isset($aParpoints['view_start']) || empty($aParpoints['view_start'])) $aParpoints['view_start'] = 0;
    if(!isset($aParpoints['view_per_page']) || empty($aParpoints['view_per_page'])) $aParpoints['view_per_page'] = BX_DOL_ADM_MP_PER_PAGE;
	
    $aParpoints['view_order_way'] = $aParpoints['view_type'];
    
    if(!isset($aParpoints['view_order']) || empty($aParpoints['view_order'])) $aParpoints['view_order'] = 'ID';
	
    $aProfiles = $this -> _oDb -> getMembers($aParpoints);
   	
    $sBaseUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
    
    $aItems = array();
    foreach($aProfiles as $aProfile){
        $aItems[$aProfile['id']] = array(
            'id' => $aProfile['id'],
            'username' => $aProfile['username'],
            'edit_link' => BX_DOL_URL_ROOT . 'pedit.php?ID=' . $aProfile['id'],
            'registration' => $aProfile['registration'],
            'last_login' => $aProfile['last_login'],            
    		'points_num' => (empty($aProfile['points_num']) ? 0 : $aProfile['points_num']),
			'penalty' => '<a href="javascript:void(0);" onclick = "javascript:AqbPointsManage.showPopup(\''.$sBaseUrl . 'give_points_from/' . $aProfile['id'] . '/penalty' . '\');">'._t('_aqb_points_txt_penalty').'</a>',
        	'present' => '<a href="javascript:void(0);" onclick = "javascript:AqbPointsManage.showPopup(\''.$sBaseUrl . 'give_points_from/' . $aProfile['id'] . '/give' . '\');">'._t('_aqb_points_txt_present').'</a>',
        	'history' => '<a href="'.$sBaseUrl . 'history/' . $aProfile['id'] .'">'._t('_aqb_points_txt_history').'</a>',
			'history_clean' => '<a href="javascript:void(0);" onclick="javascript:Profile.cleanProfileHistory(\''.$sBaseUrl . 'history_clean/' . $aProfile['id'] .'\',\''.addslashes(_t('_aqb_points_confirm_clean')).'\');">'._t('_aqb_points_txt_history_clean').'</a>',
        );
    }
  
	//--- Get Paginate ---//
    $oPaginate = new BxDolPaginate(array(
        'start' => $aParpoints['view_start'],
        'count' => $this -> _oDb -> _iMembersCount,
        'per_page' => $aParpoints['view_per_page'],
        'page_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . '?start={start}',
        'on_change_page' => BX_DOL_ADM_MP_JS_NAME . '.changePage({start})'
    ));
    
	$sPaginate = $oPaginate -> getPaginate(); 
	    
    return $this -> parseHtmlByName('members_table', array(
        'bx_repeat:items' => array_values($aItems),
		'username_function' => $this -> getOrderParpoints('NickName', $aParpoints),
		'username_arrow' => $this -> getArrowImage('NickName', $aParpoints),
		'registration_function' => $this -> getOrderParpoints('DateReg', $aParpoints),
		'registration_arrow' => $this -> getArrowImage('DateReg', $aParpoints),
		'last_login_function' => $this -> getOrderParpoints('DateLastNav', $aParpoints),  
		'last_login_arrow' => $this -> getArrowImage('DateLastNav', $aParpoints),
		'points_num_function' => $this -> getOrderParpoints('points_num', $aParpoints), 
		'points_num_arrow' => $this -> getArrowImage('points_num', $aParpoints),
		'paginate' => $sPaginate
    ));                                                                                                             
	
 }
	
	function getOrderParpoints($sFName, &$aParpoints){
		return 'onclick="'.BX_DOL_ADM_MP_JS_NAME.'.orderByField(\'' . (($aParpoints['view_order'] == $sFName && 'asc' == strtolower($aParpoints['view_type'])) ? 'desc' : 
			   ($aParpoints['view_order'] == $sFName && 'desc' == strtolower($aParpoints['view_type']) ? 'asc' : '')) . '\',\''.$sFName.'\')"';
    }
	
	function getArrowImage($sFName, &$aParpoints){
	    if ($aParpoints['view_order'] == $sFName && 'asc' == strtolower($aParpoints['view_type'])) return '<img class="points-sort-arrow" src="' . $this->getIconUrl('arrow_up.png') . '" />'; 
		if ($aParpoints['view_order'] == $sFName) return '<img class="points-sort-arrow" src="' .  $this->getIconUrl('arrow_down.png'). '" />';
		
		return '';			
	}	
	
	function getMyMembershipLevel($iUserId) {
	    if (!(int)$iUserId) return '';
		$aProfileLevel = getMemberMembershipInfo($iUserId);       
		
		$sExp = isset($aProfileLevel['DateExpires']) ?  _t('_aqb_points_membership_exp_in', floor(((int)$aProfileLevel['DateExpires'] - time())/86400)) : _t('_aqb_points_membership_exp_never');
		$aLevelInfo = $this -> _oDb -> getMembershipLevels((int)$aProfileLevel['ID']);	

		return $this->parseHtmlByName('my_level', array(
            'id' => $aLevelInfo['0']['ID'],
            'title' => $aLevelInfo['0']['Name'],
            'icon' =>  $this -> _oConfig -> getIconsUrl() . $aLevelInfo['0']['Icon'],
            'description' => str_replace("\$", "&#36;", $aLevelInfo['0']['Description']),
            'expires' => $sExp,
			'cur_balance' => _t('_aqb_points_i_have', $this -> _oDb -> getProfileTotalPointsNum($iUserId))
            )
        );
	}
	
	function getAvailableMembershipLevels(&$aValues) {
		$aMemLevels = array();
		
		$sRedirect = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'membership/';
		
		foreach($aValues as $aValue) { 
            $aMemLevels[] = array(
                'url_root' => BX_DOL_URL_ROOT,
                'id' => $aValue['id_level'],
                'title' => $aValue['Name'],
                'icon' =>  $this -> _oConfig -> getIconsUrl() . $aValue['Icon'],
                'description' => str_replace("\$", "&#36;", $aValue['Description']),
                'days' => (int)$aValue['days'] > 0 ?  $aValue['days'] . ' ' . _t('_aqb_points_membership_days') : _t('_aqb_points_membership_exp_never') ,
                'points' => _t('_aqb_points_number', $aValue['points']),
				'func' => "javascript:AqbPointsManage.onExchangePoints('" . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'exchange_points/' . $aValue['id'] . "','".addslashes(_t('_aqb_points_confirm_exchange'))."','{$sRedirect}');"	
	        );
	    }

	    return $this -> parseHtmlByName('memlevels', array('bx_repeat:levels' => $aMemLevels));
	}
	
	function getSettingsPanel() {
        $iId = $this->_oDb->getSettingsCategory();
        if(empty($iId))
           return MsgBox(_t('_aqb_points_nothing_found'));
        bx_import('BxDolAdminSettings');

        $mixedResult = '';

        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
			$oSettings -> _onSavePermalinks();
        }
        
        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();
                   
			
        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;
        return $sResult;
    }
	
	function getExchangePanel(){
		if ((int)$_GET['delete']) $this -> _oDb -> deleteTransactionsItem((int)$_GET['delete']);
		
		if ((int)$_POST['transaction_id'] && $_POST['submit_transaction']){ 
			$aResult = $this -> _oDb -> processExchangeManyally((int)$_POST['transaction_id'], $_POST['transaction']);
			
			if ($aResult['type'] == 'exchange'){
				$aProfileInfo = getProfileInfo($aResult['member_id']);
				$aPlus = array(
							'PointsNum' => (int)$aResult['points'],
							'Sum' => $aResult['price'],					
							'PointsHistoryLink' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(). 'history/' 	
						   );
					
				$rEmailTemplate = new BxDolEmailTemplates();
				$aTemplate = $rEmailTemplate -> getTemplate( 't_AqbPointsExchanged') ;
				sendMail( $aProfileInfo['Email'],  $aTemplate['Subject'], $aTemplate['Body'], $aResult['member_id'], $aPlus);
			}
		}

	$oPaginate = new BxDolPaginate(array(
        'per_page' => BX_DOL_ADM_MP_PER_PAGE,
        'per_page_step' => BX_DOL_ADM_MP_PER_PAGE_STEP,
        'on_change_per_page' => BX_DOL_ADM_MP_JS_NAME . '.changePerPage(this);'
    ));    
    
		
	$aParams = array();
	if ($sNickName) $aParams = array('filter' => $sNickName);
    $aResult = array('style_common' => '', 'content_common' => $this -> getRequestsPanel($aParams));

	return $this -> parseHtmlByName('ex_requests', 
	array(
				'search' => $this -> getBlockSearch(), 
				'requests' => $this  -> parseHtmlByName('requests', $aResult),
			    'obj_name' => BX_DOL_ADM_MP_JS_NAME,
				'actions_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(),
			    'sel_view' => '',
			    'per_page' => BX_DOL_ADM_MP_PER_PAGE,
			    'order_by' => '',
				'section' => 'requests',
				'loading' => LoadingBox('div-loading')			
		));
			
}
		
	function getRequestsPanel($aParpoints = array()) {
		if (empty($aParpoints['view_type'])) $aParpoints['view_type'] = 'ASC';
		if(!isset($aParpoints['view_start']) || empty($aParpoints['view_start'])) $aParpoints['view_start'] = 0;
	    if(!isset($aParpoints['view_per_page']) || empty($aParpoints['view_per_page'])) $aParpoints['view_per_page'] = BX_DOL_ADM_MP_PER_PAGE;
	
		    $aParpoints['view_order_way'] = $aParpoints['view_type'];
		    
		    if(!isset($aParpoints['view_order']) || empty($aParpoints['view_order'])) $aParpoints['view_order'] = 'ID';
			
		    $aProfiles = $this -> _oDb -> getRequests($aParpoints);
 	
			$sBaseUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
		
			if (empty($aProfiles)) return MsgBox(_t('_aqb_points_nothing_found'));
			
		    $aItems = array();
		    foreach($aProfiles as $aProfile){
		        $aItems[] = array(
		            'username' => $aProfile['username'],
					'status' => $aProfile['status'],
		            'date_start' => $aProfile['date'],
					'price' => $aProfile['price'],
					'points' => $aProfile['points'],	
		    		'color' => $aProfile['status'] == 'unpaid' ? "background-color:red;" : '',
					'remove' => '<a href="javascript:void(0);" onclick="javascript: if (confirm(\''.addslashes(_t('_aqb_points_admin_confirm_del_transactions')).'\')) window.location = \'' . $sBaseUrl . 'administration/exchange/&delete=' . $aProfile['id']  . '\'; " title="'._t('_aqb_points_txt_exchange_remove').'"><img src="' . $this -> getIconUrl('clean.png') . '" /></a>',
					'pay' => $this -> _oDb -> isPaymentInstalled($aProfile['member_id']) && (int)$aProfile['points'] && $aProfile['status'] == 'unpaid'? '<a onclick="javascript:$.post(\''. BX_DOL_URL_MODULES . '?r=payment/act_add_to_cart/' . $aProfile['member_id'] . "/" . $this -> _oConfig -> getId() . "/{$aProfile['id']}/{$aProfile['points']}" . '\', \'\',function(oData){try{alert(oData.message);window.location = \''. BX_DOL_URL_MODULES . '?r=payment/cart/\';}catch(e){}},\'json\');" href="javascript:void(0);" title="'._t('_aqb_points_txt_exchange_pay_cart').'"><img src="' . $this -> getIconUrl('shoppingcart.gif') . '" /></a>' : '<img title="'._t('_aqb_points_txt_exchange_pay_cart_not_installed').'" src="' . $this -> getIconUrl('shoppingcart_dis.gif') . '" />',
					'manually' =>  '<a href="javascript:void(0);" onclick="javascript:AqbPointsManage.showPopup(\'' . $sBaseUrl . 'pay_form/' . $aProfile['id'] .'\');" title="'._t('_aqb_points_txt_exchange_manually').'"><img src="' . $this -> getIconUrl('pay.png') . '" /></a>',

		        );
		    }
		  
			//--- Get Paginate ---//
		    $oPaginate = new BxDolPaginate(array(
		        'start' => $aParpoints['view_start'],
		        'count' => count($aProfiles),
		        'per_page' => $aParpoints['view_per_page'],
		        'page_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . '?start={start}',
		        'on_change_page' => BX_DOL_ADM_MP_JS_NAME . '.changePage({start})'
		    ));
		    
			$sPaginate = $oPaginate -> getPaginate(); 
			
		    return $this -> parseHtmlByName('requests_table', array(
		        'currency' => $this -> _oConfig -> getCurrencySign(),
				'bx_repeat:items' => $aItems,

				'username_function' => $this -> getOrderParpoints('NickName', $aParpoints),
				'username_arrow' => $this -> getArrowImage('NickName', $aParpoints),

				'status_function' => $this -> getOrderParpoints('status', $aParpoints),
				'status_arrow' => $this -> getArrowImage('status', $aParpoints),
				
				'date_start_function' => $this -> getOrderParpoints('date', $aParpoints),
				'date_start_arrow' => $this -> getArrowImage('date', $aParpoints),

				'price_function' => $this -> getOrderParpoints('price', $aParpoints),
				'price_arrow' => $this -> getArrowImage('price', $aParpoints),

				'points_function' => $this -> getOrderParpoints('points', $aParpoints),
				'points_arrow' => $this -> getArrowImage('points', $aParpoints),
				
				'paginate' => $sPaginate
		    ));                                                                                                             
	
   }
   
   function getPayManuallyForm($iTransactionID){
		$aInfo = $this -> _oDb -> getTransactionInfo($iTransactionID);
	
		$aForm = array(
			    'form_attrs' => array(
			            'id' => 'add_transaction',
			            'name' => 'add_transaction',
						'method' => 'post',
						'action' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/exchange',
			            'enctype' => 'multipart/form-data'						
			        ),
			
				'params' => array (
                'db' => array(
                    'submit_name' => 'submit_transaction',
					)
				),
				
				'inputs' => array(
	            
				'hidden' => array(
	                'type' => 'hidden',
	                'name' => 'transaction_id',
	                'value' => $iTransactionID,
	     		),
				
				'transaction_id' => array(
	                'type' => 'text',
	                'name' => 'transaction',
	                'value' => $aInfo['tnx'],
					'caption' =>  _t('_aqb_points_txt_exchange_form_transaction_id'),
					'info' =>  _t('_aqb_points_txt_exchange_form_transaction_id_info'),
	     		)));

	$aPayments = $this -> _oDb -> getAvailablePayament((int)$aInfo['buyer_id']);
	
	if (!empty($aPayments)){
	$aForm['inputs'] = array_merge($aForm['inputs'], array(		
     			'payments' => array(
	                'type' => 'radio_set',
				    'name'   => 'payments',
					'value'  => $this -> _oDb -> getTransactionsPaymentProvider($aInfo['tnx']),
                    'values' => $aPayments,
                    'caption'   => _t('_aqb_points_txt_payment_providers'),

	     		)));
	}			
	
	$aForm['inputs'] = array_merge($aForm['inputs'], array(
				'status' => array(
	                'type' => 'radio_set',
				    'name'  => 'status',
					'value' => $aInfo['status'] == 'pending'? 'unpaid' : 'paid' ,  
                    'values' => array('paid' => 'paid','unpaid' => 'unpaid'),
                    'caption'   => _t('_aqb_points_txt_transaction_status'),

	     		),
				'buttons' => array(
					'type' => 'submit',
					'name' => 'submit_transaction',
					'value' =>  _t('_aqb_points_txt_exchange_form_transaction_id_button')					
				)
			)	
        );
		
		$oForm = new BxTemplFormView($aForm);
		return $oForm->getCode();
	}
	
	function getLevelsSection($iItem = 0){
	   $oButtons = new BxTemplFormView(array());

	   if ($iItem) $aItem = $this -> _oDb -> getLevel($iItem); else $aItem = &$_POST;
		   
	   $aMin = array('type' => 'text',  'name' => 'min' , 'value' => $aItem['min'], 'attrs' => array('style' => 'width:85px;'), 'attrs_wrapper' => array('style' => 'width:85px;margin-right:2%;float:left;'));
	   $sMin = $this -> genWrapperInput($aMin, $oButtons -> genInput($aMin));

   	   $aMax = array('type' => 'text',  'name' => 'max' , 'value' => $aItem['max'], 'attrs' => array('style' => 'width:85px;'), 'attrs_wrapper' => array('style' => 'width:85px;margin-left:2%;float:left;'));
	   $sMax = $this -> genWrapperInput($aMax, $oButtons -> genInput($aMax));
		
	   $aItems = array();	   
				
	   $aForm = array(
			    'form_attrs' => array(
			            'id' => 'levels',
			            'name' => 'levels',
						'method' => 'post',
			            'enctype' => 'multipart/form-data'
			        ),
				
				'params' => array (
								'db' => array(
												'submit_name' => 'level_upload',
											 ),
								'checker_helper' => 'AqbPointsFormCheckerHelper'
				),
		
				'inputs' => array(
	            
				'name' => array(
	                'type' => 'text',
	                'name' => 'title',
					'required' => true,
					'value' => $aItem['title'],
					'caption' =>  _t('_aqb_points_level_name'),
					'checker' => array (
						                 'func' => 'length',
						                 'params' => array(1, 255),
										 'error' => _t ('_aqb_points_title_err_title'),
							            ),    
	     		),
				
				'interval' => array(
	                'type' => 'custom',
					'content' => '<div>' . $sMin . '<div style="float:left;">-</div>' . $sMax . '</div>',
					'caption' =>  _t('_aqb_points_level_interval'),
					'required' => true,
                    'checker' => array (
                        'func' => 'Interval',
                        'params' => array(bx_get('min'), bx_get('max')),
                        'error' => _t ('_aqb_points_interval_err_title'),
                    ),
	     		),
				
				'img' => array(
	                'type' => 'file',
	                'name' => 'img',
	                'caption' => _t('_aqb_points_level_img'),
	                'required' => true										
					)));
	

		if ($iItem){	
				$aForm['inputs'] = array_merge($aForm['inputs'], array('preview' =>  array(
														'type' => 'custom',
														'content' => '<img style="border:solid gray 1px;"  src = "' . $this -> _oConfig -> getLevelFolderUrl() . $aItem['img'] . '" />',
												        'caption' => _t('_aqb_points_preview'),
												    ),
										'hidden' =>  array(
														'type' => 'hidden',
														'name' => 'id',
												        'value' => $iItem
												    )
										));													
										
		}
		
		if (!isset($_POST['id']))
		$aForm['inputs']['img']['checker'] = array (
	                        'func' => 'length',
	                        'params' => array(1,255),
	                        'error' => _t('_aqb_points_img_err_title'),
	                    );		
							
		$aForm['inputs']['upload'] = array(
							            'type' => 'submit',
							            'name' => 'level_upload',
							            'value' => _t("_aqb_points_upload"),
							          );
		
		$oForm = new BxTemplFormView($aForm);
		$oForm -> initChecker();
		
		if ($oForm -> isSubmittedAndValid()){ 
			$aFileInfo = &$_POST;
			$aUploadResult = $this -> uploadImage();
			if (is_array($aUploadResult) || (int)$aFileInfo['id']){
				$aFileInfo = array_merge($aFileInfo, $aUploadResult);
				if ($this -> _oDb -> createLevel($aFileInfo)) $sMessage = MsgBox(_t('_aqb_points_level_successfully_created'), 2);
			}
			else $sMessage = $aUploadResult;
			
		}		
		
		return $iItem ?  PopupBox('aqb_popup', _t('_aqb_points_levels_edit'), $sMessage . $oForm->getCode()) : $sMessage . $oForm->getCode();
	}
	
	function getLevelsList(){
	    $bResult = false;
		$sMessage = '';
		
		$aLevel = $this ->_oDb -> getLevelsList();
		if (isset($_POST['points-levels-save'])){
		foreach($aLevel as $iKey => $aValue) $bResult |= $this -> _oDb -> updateLevel($aValue['id']);			
			
			if ($bResult){
				$sMessage = MsgBox(_t('_aqb_points_levels_update_successfully'), 2);
				$aLevel = $this ->_oDb -> getLevelsList();
			}	
			else 	
				$sMessage = MsgBox(_t('_aqb_points_levels_update_fault'), 2);
		}						
		
		if (empty($aLevel)) return MsgBox(_t('_aqb_points_nothing_found'));
		
	   $aValues = getMemberships(); 
	   
	   $aMemValues = array('0' =>  strtolower(_t('_aqb_points_levels_none')));
	   foreach($aValues as $iKey => $sValue){
			if ($iKey < 2) continue;
			$aMemValues[] = array('key' => $iKey, 'value' => strtolower($sValue));
			
			$aM = getMembershipPrices($iKey);
			if (!empty($aM)){ 
				foreach($aM as $sDays => $sPrices)
				$aMemValues[] = array('key' => $iKey . ':' . $sDays , 'value' => strtolower($sValue . _t('_aqb_points_action_membership_days', $sDays)));
			}
		}
		
		$aLevelsList['inputs'] = array();
		
		$sUrlDelete =  BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'delete_level/';
		$sUrlBase =  BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/levels';
		
		foreach($aLevel as $iKey => $aValue){
		  $aLevelInfo = $this -> _oDb -> getLevel($aValue['id']);
		  
		  $aLevelsList['inputs'] = array_merge($aLevelsList['inputs'], array( 
    	    	"{$aValue['id']}_section_begin" => array(
                    'type' => 'block_header',
                    'caption' => _t('_aqb_points_levels_title', $aValue['title'], $aValue['min'], $aValue['max']) . '&nbsp; <a href="javascript:void(0);" onclick="AqbPointsManage.showPopup(\'' .  BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . "edit_level/{$aValue['id']}" .'\');">' . _t('_aqb_points_levels_edit') . '</a>' . '&nbsp;<b>/</b>&nbsp;' . '<a href="javascript:void(0);" onclick="Profile.onDeleteLevel(\'' . $sUrlDelete . $aValue['id'] .'\',\'' . bx_js_string(_t('_aqb_points_confirm_delete')) . '\', \'' . $sUrlBase . '\');">' . _t('_aqb_points_levels_delete') . '</a>' ,
                    'collapsable' => true,
                    'collapsed' => true
        	 	),
				"{$aValue['id']}_bonus" => array(
                    'type' => 'text',
                    'value' => (int)$aLevelInfo['bonus'],
					'name' => 'bonus_'.$aValue['id'],
        	 	    'attrs_wrapper' => array('style' => 'width:100px;'),
					'attrs' => array('style' => 'width:100px;'),
					'caption' => _t('_aqb_points_levels_bonus_caption', $aLevelInfo['title'])	
				),
				"{$aValue['id']}_mem" => array(
                    'type' => 'select',
					'name' => 'membership_'.$aValue['id'],
					'value' => $aLevelInfo['memlevel'],
                    'values' => $aMemValues,
        			'caption' => _t('_aqb_points_levels_upgrade_caption', $aLevelInfo['title'])	
				),
				"{$aValue['id']}_section_end" => array(
                    'type' => 'block_end'
        	 	),
    		   ));
    	}   	
    	
		$aForm = array(
     	'form_attrs' => array(
            'id' => 'points-levels-from',
			'name' => 'points-levels-form',
            'method' => 'post',
		    'enctype' => '',
			'action' => '',
	        ),
	        'params' => array (
                'db' => array(
                    'submit_name' => 'points-levels-save',
                )
        ));
		
        $aForm['inputs'] = $aLevelsList['inputs'];
        $aForm['inputs']['save'] = array(
                    'type' => 'submit',
                    'name' => 'points-levels-save',
                    'value' => _t('_aqb_points_save_button'),
        			'colspan' => false,
                    'attrs' => array('style' => 'width:150px;') 
    	);

		$oForm = new BxTemplFormView($aForm);
   		return $sMessage . $oForm -> getCode();		
	}
	
	function uploadImage(){
			$aFileInfo = array();
			if (!$_FILES['img']['tmp_name']) return $aFileInfo;
			
			$aFileInfo = getimagesize($_FILES['img']['tmp_name']);
			if(empty($aFileInfo)) return MsgBox(_t('_aqb_points_upload_error'), 2);

			$sExt = '';
			
			switch( $aFileInfo['mime'] ) {
				case 'image/jpeg': $sExt = 'jpg'; break;
				case 'image/gif':  $sExt = 'gif'; break;
				case 'image/png':  $sExt = 'png'; break;
			}
			
			if(empty($sExt)) return MsgBox(_t('_aqb_points_upload_error'));

		    $sFileName = mktime() . '.' . $sExt;
			$sFileBasePath = $this -> _oConfig -> getLevelFolderPath();
			$sFilePath = $sFileBasePath . $sFileName;
			
			if(!file_exists($sFileBasePath)) {
				mkdir($sFileBasePath, 0777, true); 
				@chmod($sFileBasePath, 0777);
			};

			if (!(int)imageResize($_FILES['img']['tmp_name'], $sFilePath, 64, 64, true)) $aFileInfo['img'] = $sFileName;		
	
		return $aFileInfo; 
	}
		
	function getAccountLevelsBlock($iProfileId){
       	if (!(int)$iProfileId) return '';
		
		$aAllLevels = $this -> _oDb -> getLevelsList();
		if (empty($aAllLevels)) return '';
				
		$aInfo = $this -> _oDb -> getMyCurrentLevel($iProfileId);
		$aNextLevel = !empty($aInfo) ? $this -> _oDb -> getLevelByValue((int)$aInfo['max'] + 1) : $this -> _oDb -> getMinLevel();

		$sViewLevelsLink = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'get_levels_info/';
		$iBalance = $this -> _oDb -> getProfileTotalPointsNum($iProfileId);
		$sBuyLink = '<a href = "javascript:void(0);" onclick = "javascript:AqbPointsManage.showPopup(\''.(BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri().'buy_form/').'\');">'._t('_aqb_points_buy').'</a>';
		
    	return $this -> parseHtmlByName('levels_info', 
		array(
				 'pic' => $aInfo['img'] ? '<div class="aqb-points-levels-pic"><img style="border:solid gray 1px;"  src = "' . $this -> _oConfig -> getLevelFolderUrl() . $aInfo['img'] . '" /></div>' : '',
				 'title' =>  $aInfo['title'] ? $aInfo['title'] : _t('_aqb_points_txt_level_title_none'),
				 'balance' => "<div class=\"aqb-points-levels-item\"><b>" . _t('_aqb_points_txt_points_balance') . ":&nbsp;</b>" . $iBalance . "</div>",	
				 'view_all' => '(<a href="javascript:void(0);" onclick="javascript:AqbPointsManage.showPopup(\''. $sViewLevelsLink . '\');">' . _t('_aqb_points_txt_view_all_levels') . "</a>)&nbsp; <b>{$sBuyLink}</b>",
				 'next_level_info' => !empty($aNextLevel) ? _t('_aqb_points_txt_next_level', (int)$aNextLevel['min'] - $iBalance , $aNextLevel['title']) : ''
		));	
    }
	
	function getProfileLevelsBlock($iProfileId){
       	if (!(int)$iProfileId) return '';
		
		$aAllLevels = $this -> _oDb -> getLevelsList();
		if (empty($aAllLevels)) return '';
		
		$aInfo = $this -> _oDb -> getMyCurrentLevel($iProfileId);
		$aNextLevel = !empty($aInfo) ? $this -> _oDb -> getLevelByValue((int)$aInfo['max'] + 1) : $this -> _oDb -> getMinLevel();
	
		$iBalance = $this -> _oDb -> getProfileTotalPointsNum($iProfileId);
	
    	return $this -> parseHtmlByName('levels_info', 
		array(
				 'pic' => $aInfo['img'] ? '<div class="aqb-points-levels-pic"><img style="border:solid gray 1px;"  src = "' . $this -> _oConfig -> getLevelFolderUrl() . $aInfo['img'] . '" /></div>' : '',
				 'title' =>  $aInfo['title'] ? $aInfo['title'] : _t('_aqb_points_txt_level_title_none_profile'),
				 'balance' => '',	
				 'view_all' => '',
				 'next_level_info' => !empty($aNextLevel) ? _t('_aqb_points_txt_next_level_profile', (int)$aNextLevel['min'] - $iBalance , $aNextLevel['title']) : ''
		));	
    }
	
	
	function getLevelsInfoTable(){
	if (!$this -> _oConfig -> isLevelsEnabled()) return '';
    
    $aInfo = $this -> _oDb -> getLevelsList();
	if (empty($aInfo)) return PopupBox('aqb_popup', _t('_aqb_points_txt_all_levels_info_title'), MsgBox(_t('_aqb_points_nothing_found')));
	
	$aItems = array();
	foreach($aInfo as $iKey => $aValue){
		$aTmp = split(':', $aValue['memlevel']);
		$aMembershipInfo = getMembershipInfo($aTmp[0]);
		$aItems[] = array(	
							'icon' => $aValue['img'] ? '<img style="border:solid gray 1px;"  src = "' . $this -> _oConfig -> getLevelFolderUrl() . $aValue['img'] . '" />' : '---',
							'title' => $aValue['title'], 
							'range' => "{$aValue['min']} - {$aValue['max']}",
							'bonus' => (int)$aValue['bonus'] ? $aValue['bonus'] : '---',
							'membership' => $aValue['memlevel'] ? $aMembershipInfo['Name'] . ((int)$aTmp[1] ? '&nbsp;(' . _t('_aqb_points_action_membership_days', $aTmp[1]) . ')' : '') : '---'
						 ); 
	}
    
	return  PopupBox('aqb_popup', _t('_aqb_points_txt_all_levels_info_title'), $this -> parseHtmlByName('levels_info_table', 
																													array(
																															'bx_repeat:table' => $aItems, 
																														  )));
	}
}
?>