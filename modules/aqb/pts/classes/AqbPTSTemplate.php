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

bx_import('BxDolModuleTemplate');

class AqbPTSTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function AqbPTSTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);
	}

	function getTabs() {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'action_get_page_';

		$this->addAdminCss(array('tabs.css', 'admin.css', 'forms_adv.css', 'fields.css'));
		$this->addAdminJs(array('ui.tabs.js', 'main.js', 'fields.js'));
		$this->addJsTranslation(array('_aqb_pts_initializing', '_aqb_pts_processed', '_aqb_pts_profiles', '_aqb_pts_migrated', '_adm_mbuilder_active_items', '_adm_txt_pb_inactive_blocks', '_adm_mbuilder_inactive_items'));

		$aTabs = array(
			'bx_repeat:page_tabs' => array(
				array(
					'page_url' => $sBaseUrl.'profile_types',
					'page_name' => _t('_aqb_pts_profile_types')
				),
				array(
					'page_url' => $sBaseUrl.'profile_fields',
					'page_name' => _t('_aqb_pts_profile_fields_page')
				),
				array(
					'page_url' => $sBaseUrl.'top_menu',
					'page_name' => _t('_aqb_pts_topmenu_page')
				),
				array(
					'page_url' => $sBaseUrl.'member_menu',
					'page_name' => _t('_aqb_pts_membermenu_page')
				),
				array(
					'page_url' => $sBaseUrl.'page_blocks',
					'page_name' => _t('_aqb_pts_page_blocks_page')
				),
				array(
					'page_url' => $sBaseUrl.'search_result',
					'page_name' => _t('_aqb_pts_search_result_layout')
				)
			),
			'base_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(),
			'migrate_per_operation' => $this->_oConfig->_iMigratePerRequest
		);
		return $this->parseHtmlByName('tabs.html', $aTabs);
	}

	function displayProfileTypes($aTypes) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'admin/';

		$aResult['bx_if:profile_types_not_exist'] = array(
        	'condition' => count($aTypes) == 0,
        	'content' => array()
		);

		$aResult['bx_if:profile_types_exist'] = array(
        	'condition' => count($aTypes) > 0,
        	'content' => array(
				'form_action' => $sBaseUrl
			)
		);

		$aTypesList = array();
		foreach ($aTypes as $aType) {
			$aTypesList[] = array(
				'obsolete_row' => $aType['Obsolete'] ? 'style="background-color: #FF6666"' : '',
				'ptype_id' => $aType['ID'],
				'ptype_name' => $aType['Name'],
				'ptype_members' => $aType['Members'],
				'action_name' => $aType['Obsolete'] ? 'active' : 'inactive',
				'action_caption' => $aType['Obsolete'] ? _t('_aqb_pts_activate') : _t('_aqb_pts_deactivate'),
				'action_hp_caption' => $aType['hp_exists'] ? _t('_aqb_pts_fields_remove') : _t('_aqb_pts_pt_add'),
				'disabled_delete' => $aType['Members'] ? 'disabled="disabled"' : ''
			);
		}

		$aResult['bx_if:profile_types_exist']['content']['bx_repeat:profile_types'] = $aTypesList;

		return  $this->parseHtmlByName('profile_types_list.html', $aResult);
	}

	function displayAddTypeForm() {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$aNewTypeForm = array(
	    	'form_attrs' => array(
                'id' => 'aqb_new_type_form',
                'name' => 'aqb_new_type_form',
                'action' => $sBaseUrl . 'action_create_profile_type/',
                'method' => 'post',
                'onsubmit' => 'oAqbPTSMain.addNewType(this); return false;'
            ),
            'inputs' => array (
                'rule_text' => array(
                	'type' => 'text',
                	'name' => 'type_name',
                	'caption' => _t('_aqb_pts_pt_name'),
                	'value' => ''
                ),
                'rule_submit' => array(
					'type' => 'submit',
					'name' => 'addtype',
					'value' => _t('_aqb_pts_pt_add')
                )
			)
		);

		$oForm = new BxTemplFormView($aNewTypeForm);
		return $oForm->getCode();
	}

	function displayMigrationTool($aMemLevels, $aPTypes) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'admin/';

		$aPTypesNoAny = $aPTypes;
		unset($aPTypesNoAny[0]);

		$aMigrationToolForm = array(
	    	'form_attrs' => array(
                'id' => 'aqb_migration_tool_form',
                'name' => 'aqb_migration_tool_form',
                'action' => $sBaseUrl . 'action_migrate/',
                'method' => 'post',
                'onsubmit' => 'oAqbPTSMain.migrate(this); return false;'
            ),
            'inputs' => array (
                'membership_type' => array(
                	'type' => 'select',
                	'name' => 'membership_type',
                	'caption' => _t('_aqb_pts_for_all_profiles'),
                	'values' => $aMemLevels,
                	'value' => ''
                ),
                'profile_type' => array(
                	'type' => 'select',
                	'name' => 'profile_type',
                	'caption' => _t('_aqb_pts_and_profiles'),
                	'values' => $aPTypes,
                	'value' => ''
                ),
                'set_profile_type' => array(
                	'type' => 'select',
                	'name' => 'set_profile_type',
                	'caption' => _t('_aqb_pts_set_pt'),
                	'values' => $aPTypesNoAny,
                	'value' => ''
                ),
                'migrate' => array(
					'type' => 'submit',
					'name' => 'migrate',
					'value' => _t('_aqb_pts_apply')
                )
			)
		);

		$oForm = new BxTemplFormView($aMigrationToolForm);
		return '<div id="migrate_progress" style="display: none;"></div>'.$oForm->getCode();
	}
	function displayTopMenuCompose() {
		require_once( $this->_oConfig->getClassPath() . 'AqbPTSMenuComposer.php');

		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$oMenuComposer = new AqbPTSMenuComposer('sys_menu_top', $sBaseUrl.'action_top_menu/', $this->_oDb);
		return $oMenuComposer->constructMenuLayout();
	}

	function displayMemberMenuCompose() {
		require_once( $this->_oConfig->getClassPath() . 'AqbPTSMenuComposer.php');

		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$oMenuComposer = new AqbPTSMenuComposer('sys_menu_member', $sBaseUrl.'action_member_menu/', $this->_oDb);
		return $oMenuComposer->constructMenuLayout();
	}

	function getMenuItemEditForm($sMenuType, $iMenuItem, $iMenuItemVisibility) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$aProfileTypeValues = array();
		$aProfileTypeCheckedValues = array();
		$aProfileTypeValues[1073741823] = _t('_aqb_pts_visible_for_all');
		if ($iMenuItemVisibility == 1073741823) $aProfileTypeCheckedValues[] = 1073741823;
		$aPTypes = $this->_oDb->getPairs("SELECT ID, Name FROM `aqb_pts_profile_types`", 'ID', 'Name');
		foreach ($aPTypes as $iType => $sName) {
			$aProfileTypeValues[$iType] = $sName;
			if ($iMenuItemVisibility & $iType) $aProfileTypeCheckedValues[] = $iType;
		}

		$aMenuItemEditForm = array(
	    	'form_attrs' => array(
                'id' => 'aqb_menu_item_edit',
                'name' => 'aqb_menu_item_edit',
                'action' => $sBaseUrl . 'action_'.$sMenuType.'_menu/save/'.$iMenuItem,
                'method' => 'post',
                'onsubmit' => 'saveMenuItem(this); return false;'
            ),
            'inputs' => array (
            	'pts_visible_to' => array(
            		'type' => 'checkbox_set',
					'caption' => _t('_aqb_pts_visible_for'),
					'name' => 'pts_visible_to',
					'value' => $aProfileTypeCheckedValues,
					'values' => $aProfileTypeValues
            	),
                'submit' => array(
					'type' => 'submit',
					'name' => 'save',
					'value' => _t('_Save Changes')
                )
			)
		);

		$oForm = new BxTemplFormView($aMenuItemEditForm);
		return $oForm->getCode();
	}
	function getPageBlockEditForm($iMenuItem, $sPageName, $iPageBlockVisibility, $iPageBlockRelevancy) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$aPageBlockEditForm['form_attrs'] = array(
                'id' => 'aqb_menu_item_edit',
                'name' => 'aqb_menu_item_edit',
                'action' => $sBaseUrl . 'action_page_block/save/'.$iMenuItem,
                'method' => 'post',
                'onsubmit' => 'saveMenuItem(this); return false;'
		);

		if ($sPageName == 'member' || $sPageName == 'profile' || $sPageName == 'profile_info') {
			$aProfileTypeValues = array();
			$aProfileTypeCheckedValues = array();
			$aProfileTypeValues[1073741823] = _t('_aqb_pts_visible_for_all');
			if ($iPageBlockRelevancy == 1073741823) $aProfileTypeCheckedValues[] = 1073741823;
			$aPTypes = $this->_oDb->getPairs("SELECT ID, Name FROM `aqb_pts_profile_types`", 'ID', 'Name');
			foreach ($aPTypes as $iType => $sName) {
				$aProfileTypeValues[$iType] = $sName;
				if ($iPageBlockRelevancy & $iType) $aProfileTypeCheckedValues[] = $iType;
			}

			$aPageBlockEditForm['inputs']['pts_relevant_to'] = array(
            		'type' => 'checkbox_set',
					'caption' => _t('_aqb_pts_relevant_to'),
					'name' => 'pts_relevant_to',
					'value' => $aProfileTypeCheckedValues,
					'values' => $aProfileTypeValues
	        );
		}

		if ($sPageName != 'member') {
			$aProfileTypeValues = array();
			$aProfileTypeCheckedValues = array();
			$aProfileTypeValues[1073741823] = _t('_aqb_pts_visible_for_all');
			if ($iPageBlockVisibility == 1073741823) $aProfileTypeCheckedValues[] = 1073741823;
			$aPTypes = $this->_oDb->getPairs("SELECT ID, Name FROM `aqb_pts_profile_types`", 'ID', 'Name');
			foreach ($aPTypes as $iType => $sName) {
				$aProfileTypeValues[$iType] = $sName;
				if ($iPageBlockVisibility & $iType) $aProfileTypeCheckedValues[] = $iType;
			}

			$aPageBlockEditForm['inputs']['pts_visible_to'] = array(
            		'type' => 'checkbox_set',
					'caption' => _t('_aqb_pts_visible_for'),
					'name' => 'pts_visible_to',
					'value' => $aProfileTypeCheckedValues,
					'values' => $aProfileTypeValues
	        );
		}

		$aPageBlockEditForm['inputs']['submit'] = array(
				'type' => 'submit',
				'name' => 'save',
				'value' => _t('_Save Changes')
        );

		$oForm = new BxTemplFormView($aPageBlockEditForm);
		return $oForm->getCode();
	}

	function displayPageCompose($sPage) {
		require_once( $this->_oConfig->getClassPath() . 'AqbPTSPageComposer.php');

		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$oMenuComposer = new AqbPTSPageComposer($sPage, $sBaseUrl, $this->_oDb);
		return $oMenuComposer->constructPageLayout();
	}

	function displayProfileFieldsCompose() {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		return DesignBoxAdmin(
	    	'',
		    $this->parseHtmlByName('fields.html', array('parser_url' => $sBaseUrl.'action_fields_parser/')),
		    array(
		        'adm-fb-ctl-m1' => array(
		            'title' => _t('_adm_fields_join_form'),
		            'href' => 'javascript:void(0)',
		            'onclick' => 'javascript:changeType(this)',
		            'active' => 1
		        ),
		        'adm-fb-ctl-edit-tab' => array(
		            'title' => _t('_adm_fields_edit_profile'),
		            'href' => 'javascript:void(0)',
		            'onclick' => 'javascript:changeType(this)',
		            'active' => 0
		        ),
		        'adm-fb-ctl-view-tab' => array(
		            'title' => _t('_adm_fields_view_profile'),
		            'href' => 'javascript:void(0)',
		            'onclick' => 'javascript:changeType(this)',
		            'active' => 0
		        ),
		        'adm-fb-ctl-search-tab' => array(
		            'title' => _t('_adm_fields_search_profiles'),
		            'href' => 'javascript:void(0)',
		            'onclick' => 'javascript:changeType(this)',
		            'active' => 0
		        )
		    )
		);
	}

	function displaySearchLayoutTool($iProfileType, $aProfileTypes, $iMaxRow, $aFields, $aRows, $bIsAjax = false) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();


		$aProfileTypesOptions = array();
		foreach ($aProfileTypes as $aProfileType) {
			$aProfileTypesOptions[] = array(
				'pt_id' => $aProfileType['ID'],
				'selected' => $aProfileType['ID'] == $iProfileType ? 'selected="selected"' : '',
				'pt_name' => _t('__'.$aProfileType['Name'])
			);
		}

		$sPossibleRows = '';
		for ($i = 1; $i <= $iMaxRow + 1; $i++) $sPossibleRows .= "<option value=\"{$i}\">{$i}</option>";

		$aFieldsForm = array();
		foreach ($aFields as $aField) {
			$aFieldsForm[] = array(
				'field_name' => $aField['Name'],
				'action_url' => $sBaseUrl.'action_change_search_field_layout/',
				'ptid' => $iProfileType,
				'fid' => $aField['ID'],
				'bx_if:field_inactive' => array(
					'condition' => empty($aField['FieldID']),
					'content' => array(
						'possible_rows' => $sPossibleRows
					)
				),
				'bx_if:field_active' => array(
					'condition' => !empty($aField['FieldID']),
					'content' => array()
				),
			);
		}

		$aFieldsLayout = array();
		for ($i = 1; $i <= $iMaxRow; $i++) {
			$res = '';
			if (isset($aRows[$i][0])) {
				$res = '<td colspan="2">'.$aRows[$i][0][1].'</td>';
			} else {
				$col1 = isset($aRows[$i][1]) ? $aRows[$i][1][1] : '&nbsp;';
				$col2 = isset($aRows[$i][2]) ? $aRows[$i][2][1] : '&nbsp;';
				$res = '<td>'.$col1.'</td><td>'.$col2.'</td>';
			}
			$aFieldsLayout[] = array(
				'layout_row' => $res
			);
		}

		$aResultLayout = array(
			'action_url' => $sBaseUrl.'action_change_search_field_layout/',
			'bx_repeat:fields'	 => $aFieldsForm,
			'ptid' => $iProfileType,
			'bx_if:has_fields_layout' => array(
				'condition' => $iMaxRow > 0,
				'content' => array(
					'bx_repeat:fields_layout' => $aFieldsLayout
				)
			),
			'bx_if:not_has_fields_layout' => array(
				'condition' => $iMaxRow == 0,
				'content' => array()
			)
		);

		$aResult = array(
			'bx_if:profile_types' => array(
				'condition' => count($aProfileTypes),
				'content' => array(
					'page_action_url' => $sBaseUrl,
					'bx_repeat:profile_types'	 => $aProfileTypesOptions,
					'layout' => $this->parseHtmlByName('seacrh_result_layout.html', $aResultLayout),
				)
			),
			'bx_if:no_profile_types' => array(
				'condition' => !count($aProfileTypes),
				'content' => array()
			)
		);

		if ($bIsAjax) {
			return $aResult['bx_if:profile_types']['content']['layout'];
		}

		return  $this->parseHtmlByName('seacrh_result_page.html', $aResult);
	}
}
?>