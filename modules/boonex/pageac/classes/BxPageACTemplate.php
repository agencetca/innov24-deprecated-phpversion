<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolModuleTemplate');
bx_import('BxTemplFormView');

class BxPageACTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function BxPageACTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);
	}

	function getTabs() {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'action_get_page_';

		$this->addAdminCss(array('tabs.css', 'admin.css', 'forms_adv.css'));
		$this->addAdminJs(array('ui.tabs.js', 'main.js'));

		$aTabs = array(
			'bx_repeat:page_tabs' => array(
				array(
					'page_url' => $sBaseUrl.'rules',
					'page_name' => _t('_bx_pageac_rules_page')
				),
				array(
					'page_url' => $sBaseUrl.'top_menu',
					'page_name' => _t('_bx_pageac_topmenu_page')
				),
				array(
					'page_url' => $sBaseUrl.'member_menu',
					'page_name' => _t('_bx_pageac_membermenu_page')
				),
				array(
					'page_url' => $sBaseUrl.'page_blocks',
					'page_name' => _t('_bx_pageac_page_blocks_page')
				)
			),
			'base_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri()
		);
		return $this->parseHtmlByName('tabs.html', $aTabs);
	}

	function displayRulesList($aRules) {
		$aResult['bx_if:rules_not_exist'] = array(
        	'condition' => count($aRules) == 0,
        	'content' => array(
        		'no_rules' => MsgBox(_t('_bx_pageac_no_rules_admin'))
        	)
		);

		$aResult['bx_if:rules_exist'] = array(
        	'condition' => count($aRules) > 0,
        	'content' => array()
		);

		$aRulesList = array();
		if (count($aRules) > 0)
		foreach ($aRules as $aRule) {
			$aForbiddenGroups = array();
			foreach ($this->_oConfig->_aMemberships as $iMemLevelID => $sMemLevelName) {
				$aForbiddenGroups[] = array(
					'checked' => $aRule['MemLevels'][$iMemLevelID] ? 'checked="checked"' : '',
					'rule_id' => $aRule['ID'],
					'memlevel_id' => $iMemLevelID,
					'memlevel_name' => $sMemLevelName
				);
			}

			$aRulesList[] = array(
				'rule_id' => $aRule['ID'],
				'rule_text' => htmlentities($aRule['Rule']),
				'bx_repeat:forbidden_groups' => $aForbiddenGroups
			);
		}

		$aResult['bx_if:rules_exist']['content']['bx_repeat:rules'] = $aRulesList;

		return  $this->parseHtmlByName('rules_list.html', $aResult);
	}

	function displayNewRuleForm() {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$this->_aNewRuleForm = array(
	    	'form_attrs' => array(
                'id' => 'new_rule_form',
                'name' => 'new_rule_form',
                'action' => $sBaseUrl . 'action_new_rule/',
                'method' => 'post',
                'onsubmit' => 'oBxPageACMain.addNewRule(this); return false;'
            ),
            'inputs' => array (
                'rule_text' => array(
                	'type' => 'text',
                	'name' => 'rule',
                	'caption' => _t('_bx_pageac_page_url'),
                	'info' => _t('_bx_pageac_page_url_descr'),
                ),
                'rule_advanced' => array(
                	'type' => 'checkbox',
                	'name' => 'advanced',
                	'caption' => _t('_bx_pageac_advanced'),
                	'info' => _t('_bx_pageac_advanced_descr'),
                ),
                'rule_access' => array(
                	'type' => 'checkbox_set',
                	'name' => 'memlevels',
                	'caption' => _t('_bx_pageac_forbidden_groups'),
                	'value' => array_keys($this->_oConfig->_aMemberships),
                	'values' => $this->_oConfig->_aMemberships
                ),
                'rule_submit' => array(
					'type' => 'submit',
					'name' => 'add_rule',
					'value' => _t('_bx_pageac_add_rule')
                )
			)
		);

		$oForm = new BxTemplFormView($this->_aNewRuleForm);
		return $oForm->getCode();
	}

	function displayTopMenuCompose($aTopMenuArray) {
		$aTopItems = array();
		foreach ($aTopMenuArray['TopItems'] as $iItemID => $sItemName) {
			$aCustomItems = array();
			foreach ($aTopMenuArray['CustomItems'][$iItemID] as $iCustomItemID => $sCustomItemName) {
				$aCustomItems[] = array(
					'custom_item_id' => $iCustomItemID,
					'custom_item_caption' => $sCustomItemName
				);
			}
			$aTopItems[] = array(
				'item_id' => $iItemID,
				'item_caption' => $sItemName,
				'bx_repeat:custom_items' => $aCustomItems
			);
		}

		$aSystemItems = array();
		foreach ($aTopMenuArray['SystemItems'] as $iItemID => $sItemName) {
			$aCustomItems = array();
			foreach ($aTopMenuArray['CustomItems'][$iItemID] as $iCustomItemID => $sCustomItemName) {
				$aCustomItems[] = array(
					'custom_item_id' => $iCustomItemID,
					'custom_item_caption' => $sCustomItemName
				);
			}
			$aSystemItems[] = array(
				'item_id' => $iItemID,
				'item_caption' => $sItemName,
				'bx_repeat:custom_items' => $aCustomItems
			);
		}

		$aResult = array(
			'parser_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri().'action_top_menu/',
			'bx_repeat:top_items' => $aTopItems,
			'bx_repeat:system_items' => $aSystemItems,
		);

		return  $this->parseHtmlByName('top_menu_table.html', $aResult);
	}
	function displayMemberMenuCompose($aItemsArray) {
		$aItems = array();
		foreach ($aItemsArray as $iItemID => $sItemName) {
			$aItems[] = array(
				'item_id' => $iItemID,
				'item_caption' => $sItemName
			);
		}

		$aResult = array(
			'parser_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri().'action_member_menu/',
			'bx_repeat:top_items' => $aItems,
		);

		return  $this->parseHtmlByName('member_menu_table.html', $aResult);
	}
	function getMenuItemEditForm($sMenuType, $iMenuItem, $aMenuItemVisibility) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$aMemLevelValues = array();
		$aMemLevelCheckedValues = array();
		$aMemLevelValues[-1] = _t('_bx_pageac_visible_for_all');
		if (empty($aMenuItemVisibility)) $aMemLevelCheckedValues[] = -1;
		foreach ($this->_oConfig->_aMemberships as $iID => $sName) {
			if ($iID == 1) continue;
			$aMemLevelValues[$iID] = $sName;
			if ($aMenuItemVisibility[$iID] || empty($aMenuItemVisibility)) $aMemLevelCheckedValues[] = $iID;
		}

		$aMenuItemEditForm = array(
	    	'form_attrs' => array(
                'id' => 'item_edit',
                'name' => 'item_edit',
                'action' => $sBaseUrl . 'action_'.$sMenuType.'_menu/save/'.$iMenuItem,
                'method' => 'post',
                'onsubmit' => 'oBxPageACMain.saveItem(this); return false;'
            ),
            'inputs' => array (
            	'mlv_visible_to' => array(
            		'type' => 'checkbox_set',
					'caption' => _t('_bx_pageac_visible_for'),
					'name' => 'mlv_visible_to',
					'value' => $aMemLevelCheckedValues,
					'values' => $aMemLevelValues
            	),
                'submit' => array(
					'type' => 'submit',
					'name' => 'add_rule',
					'value' => _t('_Save Changes')
                )
			)
		);

		$oForm = new BxTemplFormView($aMenuItemEditForm);
		return $oForm->getCode();
	}
	function getPageBlockEditForm($iMenuItem, $aMenuItemVisibility) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$aMemLevelValues = array();
		$aMemLevelCheckedValues = array();
		$aMemLevelValues[-1] = _t('_bx_pageac_visible_for_all');
		if (empty($aMenuItemVisibility)) $aMemLevelCheckedValues[] = -1;
		foreach ($this->_oConfig->_aMemberships as $iID => $sName) {
			if ($iID == 1) continue;
			$aMemLevelValues[$iID] = $sName;
			if ($aMenuItemVisibility[$iID] || empty($aMenuItemVisibility)) $aMemLevelCheckedValues[] = $iID;
		}

		$aMenuItemEditForm = array(
	    	'form_attrs' => array(
                'id' => 'item_edit',
                'name' => 'item_edit',
                'action' => $sBaseUrl . 'action_page_block/save/'.$iMenuItem,
                'method' => 'post',
                'onsubmit' => 'oBxPageACMain.saveItem(this); return false;'
            ),
            'inputs' => array (
            	'mlv_visible_to' => array(
            		'type' => 'checkbox_set',
					'caption' => _t('_bx_pageac_visible_for'),
					'name' => 'mlv_visible_to',
					'value' => $aMemLevelCheckedValues,
					'values' => $aMemLevelValues
            	),
                'submit' => array(
					'type' => 'submit',
					'name' => 'add_rule',
					'value' => _t('_Save Changes')
                )
			)
		);

		$oForm = new BxTemplFormView($aMenuItemEditForm);
		return $oForm->getCode();
	}

	function _getAvailablePages($aPages) {
		$aPagesTempl = array();
		foreach ($aPages as $aPage) {
			$sTitle = htmlspecialchars( $aPage['Title'] ? $aPage['Title'] : $aPage['Name'] );
			$aPagesTempl[] = array(
				'page_name' => htmlspecialchars_adv($aPage['Name']),
				'page_caption' => $sTitle,
			);
		}

		$aResult = array(
			'update_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri().'action_get_page_page_blocks/',
			'bx_repeat:pages' => $aPagesTempl
		);
		return  $this->parseHtmlByName('page_builder_main.html', $aResult);
	}
	function _getPageBlocks($aColumns) {
		$sParseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri().'action_page_block/edit/';

		$aResult = array(
			'bx_repeat:pages' => $aPagesTempl
		);

		foreach ($aColumns as $iColumn => $aBlocks) {
			$aBlocksTmpl = array();
			foreach ($aBlocks as $aBlock) {
				$aBlocksTmpl[] = array(
					'block_caption' => _t( $aBlock['Caption'] ),
					'edit_block_url' => $sParseUrl.$aBlock['ID']
				);
			}
			$aResult['bx_repeat:columns'][] = array(
				'column' => $iColumn,
				'bx_repeat:blocks' => $aBlocksTmpl
			);
		}


		return  $this->parseHtmlByName('page_builder_blocks.html', $aResult);
	}
}
?>