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

/**
 *	Profile Fields Manager
 */
class MlPagesPSFM {
	function MlPagesPSFM( $iArea, $iCategory = 0 ) {
		if (!$iCategory)
			return false;
		$this->iCategory = $iCategory;
		$this -> aColNames = array (
			1  => array( 'Page' => 'Join',   'Order' => 'JoinOrder',         'Block' => 'JoinBlock',         'ShowSysItems' => 'Captcha,TermsOfUse,thumb', 'EditAdd' => array( 'JoinPage' ) ),
			
			2  => array( 'Page' => 'Edit',   'Order' => 'EditOrder',      'Block' => 'EditBlock' ),
			
			5  => array( 'Page' => 'View',   'Order' => 'ViewOrder',      'Block' => 'ViewBlock' ),
			//9  => array( 'Page' => 'Browse',   'Order' => 'BrowseOrder',      'Block' => 'BrowseBlock' ),
		);
		//db_res("insert into sys_sbs_queue set body='{$iCategory}'");
		$this -> sLinkPref = '#!'; //prefix for values links (@see predefined links)

		
		$this -> aTypes = array(
			'text'       => 'Text',
			'area'       => 'TextArea',
			'html_area'  => 'HtmlTextArea',
			'pass'       => 'Password',
			'date'       => 'Date',
			'select_one' => 'Selector',
			'select_set' => 'Multiple Selector',
			'num'        => 'Number',
			'range'      => 'Range',
			'bool'       => 'Boolean (checkbox)'
		);
		
		//altering table properties
		$this -> aTypesAlter = array(
			'text'       => "varchar(255) NOT NULL {default}",
			'area'       => "text NOT NULL",
			'html_area'  => "text NOT NULL",
			'pass'       => "varchar(40) NOT NULL",
			'date'       => "varchar(255) NOT NULL {default}",
			'select_one' => "enum({values})",
			'select_one_linked' => "varchar(255) NOT NULL default ''",
			'select_set' => "set({values}) NOT NULL default ''",
			'select_set_linked' => "set({values}) NOT NULL default ''",
			'num'        => "int(10) unsigned NOT NULL {default}",
			'range'      => "varchar(255) NOT NULL {default}",
			'bool'       => "tinyint(1) NOT NULL {default}"
		);
		
		$this -> iAreaID = (int)$iArea;

		if( !( $this -> iAreaID > 0 and isset( $this -> aColNames[$this -> iAreaID] ) ) )
			return false;
		
		// retrieve default language
		$sLangDfl = addslashes( getParam('lang_default') );
		$sQuery = "SELECT `ID` FROM `sys_localization_languages` WHERE `Name` = '$sLangDfl'";
		$this -> sLangID = (int)db_value( $sQuery );
		
		if( !$this -> sLangID )
			die('Cannot continue. Default language not found. Check the Basic Settings.');
		
		$this -> areaPageName    = $this -> aColNames[$this -> iAreaID]['Page'];
		$this -> areaOrderCol    = $this -> aColNames[$this -> iAreaID]['Order'];
		$this -> areaBlockCol    = $this -> aColNames[$this -> iAreaID]['Block'];
		$this -> areaSysItems    = $this -> aColNames[$this -> iAreaID]['ShowSysItems'];
		
		$this -> areaEditAddCols = $this -> aColNames[$this -> iAreaID]['EditAdd'];
		
		$this -> aBlocks = array();
		$this -> aItems  = array();
		
		$this -> aBlocksInac = array();
		$this -> aItemsInac  = array();
		
	}
	
	function genJSON() {
		//if (!$this -> sSubCategory || !$this -> sMainCategory) return false;
		$this -> fillMyArrays();
		$this -> oJSONObject = new BxDolPSFMAreaJSONObj( $this );
		$oJSONConv = new Services_JSON();
		return $oJSONConv -> encode( $this -> oJSONObject );
	}
	
	function fillMyArrays() {
        if (!$this -> areaOrderCol)
            return false;
        //if ($this -> areaPageName != 'Browse') return;
		//collect active fields

		//blocks
		$sBlocksQuery = "
			SELECT
				`ID`,
				`Name`
			FROM `ml_pages_fields`
			WHERE
				`{$this -> areaOrderCol}` IS NOT NULL AND
				`Type` = 'block' 
				AND FIND_IN_SET ({$this->iCategory}, `Categories`)
			ORDER BY
				`{$this -> areaOrderCol}`
		";
		//db_res("insert into sys_sbs_queue set body='".addslashes($sBlocksQuery)."'");
		$rBlocks = db_res( $sBlocksQuery );
		
		while( $aBlock = mysql_fetch_assoc( $rBlocks ) ) {
			$iBlockID = $aBlock['ID'];
			
			$this -> aBlocks[ $iBlockID ] = $aBlock['Name'];
			
			//get items of this block
			$sItemsQuery = "
				SELECT
					`ID`,
					`Name`
				FROM `ml_pages_fields`
				WHERE
					`Type` != 'block' AND
					`{$this -> areaOrderCol}` IS NOT NULL AND
					`{$this -> areaBlockCol}` = $iBlockID AND
					(
						`Type` != 'system' OR
						(
							`Type` = 'system' AND
							FIND_IN_SET( `Name`, '{$this -> areaSysItems}' )
						)
					)
					AND FIND_IN_SET ({$this->iCategory}, `Categories`)
				ORDER BY
					`{$this -> areaOrderCol}`
			";
			//db_res("insert into sys_sbs_queue set body='".addslashes($sItemsQuery)."'");
			$rItems = db_res( $sItemsQuery );
			
			while( $aItem = mysql_fetch_assoc( $rItems ) )
				$this -> aItems[ $aItem['ID'] ] = array( $aItem['Name'], $iBlockID );
		}
		
		//collect inactive fields
		//				`{$this -> areaOrderCol}` IS NULL AND

		//blocks
		$sBlocksInacQuery = "
			SELECT
				`ID`,
				`Name`
			FROM `ml_pages_fields`
			WHERE
				`{$this -> areaOrderCol}` IS NULL AND
				`Type` = 'block'
				AND FIND_IN_SET ({$this->iCategory}, `Categories`)
		";
		//db_res("insert into sys_sbs_queue set body='".addslashes($sBlocksInacQuery)."'");
		$rBlocksInac = db_res( $sBlocksInacQuery );
		
		while( $aBlock = mysql_fetch_assoc( $rBlocksInac ) )
			$this -> aBlocksInac[ $aBlock['ID'] ] = $aBlock['Name'];
		
		//items
		$sActiveBlocksList = implode( ',', array_keys( $this -> aBlocks ) );
		if( $sActiveBlocksList == '' )
			$sActiveBlocksList = "NULL";
					

		$sItemsInacQuery = "
			SELECT
				`ID`,
				`Name`
			FROM `ml_pages_fields`
			WHERE
				`Type` != 'block' AND (
					`{$this -> areaBlockCol}` = 0 OR
					`{$this -> areaBlockCol}` NOT IN ($sActiveBlocksList)
				) AND (
					`Type` != 'system' OR (
						`Type` = 'system' AND
						FIND_IN_SET( `Name`, '{$this -> areaSysItems}' )
					)
				)
				AND FIND_IN_SET ({$this->iCategory}, `Categories`)
		";
		//db_res("insert into sys_sbs_queue set body='".addslashes($sItemsInacQuery)."'");
		$rItemsInac = db_res( $sItemsInacQuery );
		
		while( $aItem = mysql_fetch_assoc( $rItemsInac ) )
			$this -> aItemsInac[ $aItem['ID'] ] = $aItem['Name'];
		
		//echoDbg( $this );exit;
	}
	function savePositions( $aInArrays ) {

		db_res( "UPDATE `ml_pages_fields` SET `{$this -> areaOrderCol}` = NULL WHERE FIND_IN_SET ({$this->iCategory}, `Categories`)" );
		db_res( "UPDATE `ml_pages_fields` SET `{$this -> areaBlockCol}` = 0 WHERE FIND_IN_SET ({$this->iCategory}, `Categories`)" );
		
		if( is_array( $aInArrays['blocks'] ) ) {
			foreach( $aInArrays['blocks'] as $iBlockID ) {
				$iBlockID = (int)$iBlockID;
				
				$iBlockOrd = (int)db_value( "
					SELECT MAX( `{$this -> areaOrderCol}` )
					FROM `ml_pages_fields`
					WHERE `Type` = 'block' AND FIND_IN_SET ({$this->iCategory}, `Categories`)
				" ) + 1;
				db_res( "
					UPDATE `ml_pages_fields`
					SET `{$this -> areaOrderCol}` = $iBlockOrd
					WHERE `ID` = $iBlockID AND FIND_IN_SET ({$this->iCategory}, `Categories`)
				" );
			}
			
			if( is_array( $aInArrays['items'] ) and is_array( $aInArrays['items_blocks'] ) ) {
				foreach( $aInArrays['items'] as $iItemID ) {
					$iItemID = (int)$iItemID;
					$iItemBlockID = (int)$aInArrays['items_blocks'][$iItemID];
					
					if( in_array( $iItemBlockID, $aInArrays['blocks'] ) ) {
						$iItemOrd = db_value( "
							SELECT MAX( `{$this -> areaOrderCol}` )
							FROM `ml_pages_fields`
							WHERE `Type` != 'block' AND `{$this -> areaBlockCol}` = $iItemBlockID
							AND FIND_IN_SET ({$this->iCategory}, `Categories`)
						" ) + 1;
						
						db_res( "
							UPDATE `ml_pages_fields`
							SET
								`{$this -> areaOrderCol}` = $iItemOrd,
								`{$this -> areaBlockCol}` = $iItemBlockID
							WHERE `ID` = $iItemID
							AND FIND_IN_SET ({$this->iCategory}, `Categories`)
						" );
					}
				}
			}
		}
		
		echo 'OK';
	}
	
	function genFieldEditForm( $iItemID ) {
		$sQuery = "
			SELECT
				`Name`,
				`Type`,
				`Control`,
				`Extra`,
				`Min`,
				`Max`,
				`Values`,
				`UseLKey`,
				`Check`,
				`Unique`,
				`Default`,
				`Mandatory`,
				`IdField`,
				`CaptionField`,
				`DepField`,
				`DepParentField`,
				`MediaType`,
				`WithPhoto`,
				`WithTime`,
				`Multiplyable`,
				`AddOn`,
				`Attribute`,
				`MultipleStyle`,
				`MultipleCss`,
				`Deletable`,
				`MatchField`,
				`Link`,
				`CaptionDisable`,
				`MatchPercent`" .
				
				( $this -> areaEditAddCols ?
					( ', `' . implode( '`, `', $this -> areaEditAddCols ) . '`' ) :
					''
				) . "
				
			FROM `ml_pages_fields` WHERE `ID` = $iItemID AND FIND_IN_SET ({$this->iCategory}, `Categories`)";
		
		$aField = db_assoc_arr( $sQuery );
		
		if( !$aField ) {
			echo _t('_adm_fields_error_field_not_found');
			return;
		}

        $sGeneralC = _t('_adm_fields_general');
        $sAdvancedC = _t('_adm_fields_advanced');
        $sMessagesC = _t('_adm_fields_messages');
        $sMatchingC = _t('_adm_fields_matching');
        $sNameC = _t('_adm_mbuilder_System_Name');
        $sCaptionC = _t('_Caption');
        $sDescriptionC = _t('_Description');
        $sNameDescC = _t('_adm_fields_name_desc');
        $sTypeC = _t('_Type');
        $sJoinPageC = _t('_adm_fields_join_page');
        $sSearchInFieldsC = _t('_adm_fields_search_in_fields');
        $sSearchInFieldsDescC = _t('_adm_fields_search_in_fields_desc');
        $sMutualCpFieldsC = _t('_adm_fields_mutual_couple_fields');
        $sMutualCpFieldsDescC = _t('_adm_fields_mutual_couple_fields_desc');
        $sSureDeleteItemC = addslashes(_t('_adm_fields_delete_item_desc'));

		// field title and description
		$this -> fieldCaption = "_ml_pages_FieldCaption_{$aField['Name']}_{$this -> areaPageName}"; // _ml_pages_FieldCaption_Sex_Join
		$this -> fieldDesc    = "_ml_pages_FieldDesc_{$aField['Name']}_{$this -> areaPageName}";    // _ml_pages_FieldDesc_Sex_Join
		
		$this -> showFormTabs = ( $aField['Type'] != 'block' and $aField['Type'] != 'system' );

        $sCaptionDescC = _t('_adm_fields_caption_desc', $this -> areaPageName, $this -> fieldCaption);
        $sDescriptionDescC = _t('_adm_fields_description_desc', $this -> areaPageName, $this -> fieldDesc);		
		
		?>
		<input type="hidden" name="action" value="saveItem" />
		<input type="hidden" name="id" value="<?= $iItemID ?>" />
		<input type="hidden" name="sub_category" value="<?= $this -> iCategory ?>" />
		<input type="hidden" name="area" value="<?= $this -> iAreaID ?>" />
		
		<?
		if( $this -> showFormTabs ) {
			?>
			<ul id="form_tabs_switcher">
				<li><a href="#f1"><?= $sGeneralC ?></a></li>
				<li><a href="#f2"><?= $sAdvancedC ?></a></li>
				<li><a href="#f3"><?= $sMessagesC ?></a></li>
				<!--<li><a href="#f4"><?= $sMatchingC ?></a></li>-->
			</ul>
			<?
		}
		?>
		
        
		<table class="field_edit_tab" id="f1"> <!-- General -->
			<tr>
				<td class="label"><?= $sNameC ?>:</td>
				<td class="value">
					<input type="text" maxlength="255" class="input_text" name="Name"
					  value="<?= htmlspecialchars( $aField['Name'] ); ?>"
					  <? if( $aField['Type'] == 'system' or !$aField['Deletable'] ) echo 'readonly="readonly"'; ?> />
				</td>
				<td class="info">
		<?
		if( $aField['Type'] != 'block' and $aField['Type'] != 'system' )
			echo $this -> getInfoIcon(addslashes($sNameDescC));
		else
			echo '&nbsp;';
		?>
				</td>
			</tr>
			<tr>
				<td class="label"><?= $sCaptionC ?>:</td>
				<td class="value">
					<input type="text" maxlength="255" class="input_text" name="Caption"
					  value="<?= htmlspecialchars( $this -> getLangString( $this -> fieldCaption ) ); ?>" />
				</td>
				<td class="info">
					<?= $this -> getInfoIcon(addslashes($sCaptionDescC)) ?>
				</td>
			</tr>
			<tr>
				<td class="label"><?= $sDescriptionC ?>:</td>
				<td class="value">
					<textarea class="input_text" name="Desc"><?= htmlspecialchars( $this -> getLangString( $this -> fieldDesc ) ); ?></textarea>
				</td>
				<td class="info">
					<?= $this -> getInfoIcon(addslashes($sDescriptionDescC)) ?>
				</td>
			</tr>
		<?
		
		if( $aField['Type'] == 'block' ) {
			if( $this -> iAreaID == 5 || $this -> iAreaID == 6 || $this -> iAreaID == 7 || $this -> iAreaID == 8) { //Join
				?>

				<?
			}
		} else {
			?>

			<tr>
				<td class="label"><?= $sTypeC ?>:</td>
				<td class="value">
			<?
			if( $aField['Type'] == 'system' )
				echo 'System';
			else {
				?>
					<select name="Type" class="select_type" onchange="changeFieldType( this.value );">
						<?= $this -> getTypeOptions( $aField['Type'] ) ?>
					</select>
				<?
			}
			?>
				</td>
				<td class="info">&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?= _t('_ml_pages_disable_caption') ?>:</td>
				<td class="value">
						<input type="checkbox" name="CaptionDisable" value="" class="input_checkbox"
						<?= $aField['CaptionDisable'] ? 'checked="checked"' : '' ?> />
				</td>
				<td class="info">
					<?= $this -> getInfoIcon( _t('_ml_pages_disable_caption_desc')); ?>
				</td>
			</tr>
			<?
		}
		
		//system fields properties
		if( $aField['Name'] == 'Keyword' ) {
			?>
			<tr>
				<td class="label"><?= $sSearchInFieldsC ?>:</td>
				<td class="value">
					<select name="KeywordFields[]" class="select_multiple" multiple="multiple">
						<?= $this -> getFieldsOptionsList( $aField['Extra'], 'Keyword' ) ?>
					</select>
				</td>
				<td class="info">
					<?= $this -> getInfoIcon(addslashes($sSearchInFieldsDescC)) ?>
				</td>
			</tr>
			<?
		} elseif( $aField['Name'] == 'Couple' ) {
			?>
			<tr>
				<td class="label"><?= $sMutualCpFieldsC ?>:</td>
				<td class="value">
					<select name="CoupleFields[]" class="select_multiple" multiple="multiple">
						<?= $this -> getFieldsOptionsList( $aField['Extra'], 'Couple' ) ?>
					</select>
				</td>
				<td class="info">
					<?= $this -> getInfoIcon(addslashes($sMutualCpFieldsDescC)) ?>
				</td>
			</tr>
			<?
		}
		
		?>
		</table>
        
		<?
		
		if( $this -> showFormTabs ) {
			$this -> genFormAdvTab( $aField );
			$this -> genFormMiscTab( $aField );
			//$this -> genFormMatchTab( $aField );
		}
		
		?>
		<table class="field_edit_tab"> <!--Controls-->
			<tr>
				<td class="buttons" colspan="3">
					<input type="submit" name="action" value="Save" />
		<?
		
		if( $aField['Type'] != 'system' and $aField['Deletable'] ) {
			?>
					<input type="submit" name="action" value="Delete" onclick="return confirm('<?= $sSureDeleteItemC ?>');" />
			<?
		}
		?>					
				</td>
			</tr>
		</table>
		
		<script type="text/javascript">
			$(document).ready( function(){
                $('.edit_item_table_cont:first').tabs({selected:0});
				changeFieldType( '<?= $aField['Type'] ?>' );
			} );
		</script>
		<?
	}
	
	function getFieldsOptionsList( $sSelected, $sType ) {
		$aSelected = explode( "\n", $sSelected );
		foreach( $aSelected as $iKey => $sValue )
			$aSelected[$iKey] = trim( $sValue );
		
		switch( $sType ) {
			case 'Keyword': $sWhere = "`Type` = 'text' OR `Type` = 'area' OR `Type` = 'html_area'"; break;
			case 'Couple' : $sWhere = "`Type` != 'block' AND `Type` != 'system' AND `Deletable` = 1"; break;
			default       : $sWhere = "0";
		}
		
		
		
		$sQuery = "SELECT `Name` FROM `ml_pages_fields` WHERE $sWhere";
		$rFields = db_res( $sQuery );
		
		$sRet = '';
		while( $aField = mysql_fetch_assoc( $rFields ) ) {
			$sRet .= '<option value="' . $aField['Name'] . '"' .
			( in_array( $aField['Name'], $aSelected ) ? 'selected="selected"' : '' ) . '>' .
			$aField['Name'] . '</option>';
		}
		
		return $sRet;
	}
	
	function getJoinPagesSelector( $iCurrent ) {
		$sQuery = "SELECT MAX( `JoinPage` ) FROM `ml_pages_fields`";
		$iMaxPage = (int)db_value( $sQuery );
		
		$sRet = '<select name="JoinPage" class="select_page">';
		for( $iPage = 0; $iPage <= ( $iMaxPage + 1 ); $iPage ++ ) {
			$sRet .= 
				'<option value="' . $iPage . '"' .
				( ( $iPage == $iCurrent ) ? ' selected="selected"' : '' ) . '>' .
				$iPage . '</option>';
		}
		$sRet .= '</select>';
		
		return $sRet;
	}
	
	function genFormMatchTab( $aField ) {
		$aForm = array(
			'MatchField' => array(
				'label' => _t('_adm_fields_match_with_field'),
				'type'  => 'select',
				'info'  => addslashes(_t('_adm_fields_match_with_field_desc')),
				'value' => $aField['MatchField'],
				'values' => $this -> getMatchFields( $aField )
			),
			'MatchPercent' => array(
				'label' => _t('_adm_fields_match_percent'),
				'type'  => 'text',
				'info'  => addslashes(_t('_adm_fields_match_percent_desc')),
				'value' => $aField['MatchPercent']
			)
		);
		
		$this -> genTableEdit( $aForm, 'f4' );
	}
	
	function getMatchFields( $aField ) {
		$aSelectFields = array( $aField['Type'] );
		
		switch( $aField['Type'] ) {
			case 'select_set':
				$aSelectFields[] = 'select_one';
			break;
			
			case 'select_one':
				$aSelectFields[] = 'select_set';
			break;
			
			case 'range':
				$aSelectFields[] = 'num';
				$aSelectFields[] = 'date';
			break;
		}
		
		$sQuery = "SELECT `ID`, `Name` FROM `ml_pages_fields` WHERE FIND_IN_SET( `Type`, '" . implode( ',', $aSelectFields ) . "' )";
		$rMyFields = db_res( $sQuery );
		
		$aMyFields = array( '0' => '-Not set-' );
		while( $aMyField = mysql_fetch_assoc( $rMyFields ) ) {
			$aMyFields[ $aMyField['ID'] ] = $aMyField['Name'];
		}
		
		return $aMyFields;
	}
	
	function genFormAdvTab( $aField ) {
		$sFields = db_res("SELECT `Name` FROM `ml_pages_fields` WHERE `Type` != 'block' AND `Type` != 'system' AND `Name` != '{$aField[Name]}'");
		$aFields = array();
		while ($aRow = mysql_fetch_array($sFields))
			$aFields[$aRow['Name']] = $aRow['Name'];

			
		$aForm = array(
			'Control_one' => array(
				'label'  => _t('_adm_fields_selector_control'),
				'type'   => 'select',
				'info'   => addslashes(_t('_adm_fields_selector_control_desc')),
				'value'  => $aField['Control'],
				'row_id' => 'field_control_select_one',
				'values' => array( 
					'select' => 'Select (Dropdown box)',
					'radio'  => 'Radio-buttons'
				)
			),
			'Control_set' => array(
				'label'  => _t('_adm_fields_multiple_selector_control'),
				'type'   => 'select',
				'info'   => addslashes(_t('_adm_fields_multiple_selector_control_desc')),
				'value'  => $aField['Control'],
				'row_id' => 'field_control_select_set',
				'values' => array( 
					'select' => 'Select (Box)',
					'checkbox' => 'Checkboxes'
				)
			),
			'Attribute' => array(
				'label'  => _t('_ml_pages_adm_fields_attribute'),
				'type'   => 'select',
				'info'   => addslashes(_t('_ml_pages_adm_fields_attribute_desc')),
				'value'  => $aField['Attribute'],
				'row_id' => 'field_control_attribute',
				'values' => array( 
					'none' => 'None',
					'multiplyable' => 'Multiplyable',
					'addon' => 'Multiplyable + AddOn',
				)
			),
			'Mandatory' => array(
				'label'  => _t('_adm_fields_mandatory'),
				'type'   => 'checkbox',
				'info'   => addslashes(_t('_adm_fields_mandatory_desc')),
				'value'  => $aField['Mandatory'],
				'row_id' => 'field_mandatory'
			),
			'IdField' => array(
				'label'  => _t('_ml_pages_adm_fields_id_field'),
				'type'   => 'text',
				'info'   => addslashes(_t('_ml_pages_adm_fields_id_field_desc')),
				'value'  => $aField['IdField'],
				'row_id' => 'field_id_field'
			),
			'CaptionField' => array(
				'label'  => _t('_ml_pages_adm_fields_caption_field'),
				'type'   => 'text',
				'info'   => addslashes(_t('_ml_pages_adm_fields_caption_field_desc')),
				'value'  => $aField['CaptionField'],
				'row_id' => 'field_caption_field'
			),
			'DepField' => array(
				'label'  => _t('_ml_pages_adm_fields_dep_field'),
				'type'   => 'text',
				'info'   => addslashes(_t('_ml_pages_adm_fields_dep_field_desc')),
				'value'  => $aField['DepField'],
				'row_id' => 'field_dep_field'
			),
			'DepParentField' => array(
				'label'  => _t('_ml_pages_adm_fields_dep_parent_field'),
				'type'   => 'text',
				'info'   => addslashes(_t('_ml_pages_adm_fields_dep_parent_field_desc')),
				'value'  => $aField['DepParentField'],
				'row_id' => 'field_dep_parent_field'
			),
			'MediaType' => array(
				'label'  => _t('_ml_pages_adm_fields_media_type'),
				'type'   => 'select',
				'info'   => addslashes(_t('_ml_pages_adm_fields_media_type_desc')),
				'value'  => $aField['MediaType'],
				'row_id' => 'field_control_media_type',
				'values' => array( 
					'none' => 'Standard',
					'photo' => 'Photo',
					'video' => 'Video',
					'sound' => 'Sound',
					'file' => 'File',
					'rss' => 'Rss',
					'youtube' => 'YouTube',
					'map' => 'Google map',
					'editorlog' => 'Log editor',
					'datelog' => 'Log edit date',
				)
			),			
			/*'WithPhoto' => array(
				'label'  => _t('_ml_pages_adm_fields_with_photo'),
				'type'   => 'checkbox',
				'info'   => addslashes(_t('_ml_pages_adm_fields_with_photo_desc')),
				'value'  => $aField['WithPhoto'],
				'row_id' => 'field_with_photo'
			),*/
			'WithTime' => array(
				'label'  => _t('_ml_pages_adm_fields_with_time'),
				'type'   => 'checkbox',
				'info'   => addslashes(_t('_ml_pages_adm_fields_with_time_desc')),
				'value'  => $aField['WithTime'],
				'row_id' => 'field_with_time'
			),

			'Multiplyable' => array(
				'label'  => _t('_ml_page_adm_fields_multiplyable'),
				'type'   => 'checkbox',
				'info'   => addslashes(_t('_ml_page_adm_fields_multiplyabl_desc')),
				'value'  => $aField['Multiplyable'],
				'row_id' => 'field_multiplyable'
			),
			'AddOn' => array(
				'label'  => _t('_ml_page_adm_fields_add_on'),
				'type'   => 'checkbox',
				'info'   => addslashes(_t('_ml_page_adm_fields_add_on_desc')),
				'value'  => $aField['AddOn'],
				'row_id' => 'field_add_on'
			),
			/*'Link' => array(
				'label'  => _t('_ml_pages_adm_fields_link'),
				'type'   => 'checkbox',
				'info'   => addslashes(_t('_ml_pages_adm_fields_link_desc')),
				'value'  => $aField['Link'],
				'row_id' => 'field_link'
			),
			'WithPhoto' => array(
				'label'  => _t('_ml_pages_adm_fields_with_photo'),
				'type'   => 'checkbox',
				'info'   => addslashes(_t('_ml_pages_adm_fields_with_photo_desc')),
				'value'  => $aField['WithPhoto'],
				'row_id' => 'field_with_photo'
			),
			'Multiplyable' => array(
				'label'  => _t('_ml_page_adm_fields_multiplyable'),
				'type'   => 'checkbox',
				'info'   => addslashes(_t('_ml_page_adm_fields_multiplyabl_desc')),
				'value'  => $aField['Multiplyable'],
				'row_id' => 'field_multiplyable'
			),
			'MultipleStyle' => array(
				'label'  => _t('_ml_page_adm_fields_multiplestyle'),
				'type'   => 'select',
				'info'   => addslashes(_t('_ml_page_adm_fields_multiplestyle_desc')),
				'value'  => $aField['MultipleStyle'],
				'row_id' => 'field_multiplestyle',
				'values' => array( 
					'Unordered'  => _t('_ml_page_unordered'),
					'Ordered' => _t('_ml_page_ordered'),
				)
			),
			'MultipleCss' => array(
				'label'  => _t('_ml_page_adm_fields_multiplecss'),
				'type'   => 'textarea',
				'info'   => addslashes(_t('_ml_page_adm_fields_multiplecss_desc')),
				'value'  => $aField['MultipleCss'],
				'row_id' => 'field_multiplecss',
			),*/
			/*'PhotoFor' => array(
				'label'  => _t('_ml_page_adm_fields_photofor'),
				'type'   => 'select',
				'info'   => addslashes(_t('_adm_fields_photofor_desc')),
				'value'  => $aField['PhotoFor'],
				'row_id' => 'fieldphotofor',
				'values' => $aFields
			),*/
			'Min' => array(
				'label'  => _t('_adm_fields_minimum_value'),
				'type'   => 'text',
				'info'   => addslashes(_t('_adm_fields_minimum_value_desc')),
				'value'  => $aField['Min'],
				'row_id' => 'field_minimum'
			),
			'Max' => array(
				'label'  => _t('_adm_fields_maximum_value'),
				'type'   => 'text',
				'info'   => addslashes(_t('_adm_fields_maximum_value_desc')),
				'value'  => $aField['Max'],
				'row_id' => 'field_maximum'
			),
			'Unique' => array(
				'label'  => _t('_adm_fields_unique'),
				'type'   => 'checkbox',
				'info'   => addslashes(_t('_adm_fields_unique_desc')),
				'value'  => $aField['Unique'],
				'row_id' => 'field_unique'
			),
			'Check' => array(
				'label'  => _t('_adm_ambuilder_Check'),
				'type'   => 'textarea',
				'info'   => addslashes(_t('_adm_ambuilder_Check_desc')),
				'value'  => $aField['Check'],
				'row_id' => 'field_check'
			),
			'Values' => array(
				'label'  => _t('_ml_pages_adm_fields_values'),
				'type'   => 'values',
				'info'  => addslashes(_t('_ml_pages_adm_fields_values_desc')),
				'value'  => $aField['Values'],
				'row_id' => 'field_values'
			),
			'UseLKey' => array(
				'label'  => _t('_adm_fields_used_lang_key'),
				'type'   => 'select',
				'info'  => addslashes(_t('_adm_fields_used_lang_key_desc')),
				'value'  => $aField['UseLKey'],
				'row_id' => 'field_lkey',
				'values' => array( 
					'LKey'  => 'LKey',
					'LKey2' => 'LKey2',
					'LKey3' => 'LKey3',
				)
			),
			'Default' => array(
				'label'  => _t('_ml_pages_adm_fields_default'),
				'type'   => 'text',
				'info'  => addslashes(_t('_ml_pages_adm_fields_default_desc')),
				'value'  => $aField['Default'],
				'row_id' => 'field_default'
			)
		);
		
		$this -> genTableEdit( $aForm, 'f2' );
	}
	
	function genTableEdit( $aForm, $sID = '' ) {
		?>
		<table class="field_edit_tab" <?= $sID ? ( 'id="' . $sID . '"' ) : '' ?>>
		
		<?
		foreach( $aForm as $sInputName => $aInput ) {
			?>
			<tr <?= $aInput['row_id'] ? ( 'id="' . $aInput['row_id'] . '"' ) : '' ?>>
				<td class="label"><?= $aInput['label'] ?>:</td>
				<td class="value">
			<?
			switch( $aInput['type'] ) {
				case 'textarea':
					?>
					<textarea name="<?= $sInputName ?>" class="input_text"><?= htmlspecialchars( $aInput['value'] ) ?></textarea>
					<?
				break;
				case 'checkbox':
					?>
					<input type="checkbox" name="<?= $sInputName ?>" value="yes" class="input_checkbox"
					  <?= $aInput['value'] ? 'checked="checked"' : '' ?> />
					<?
				break;
				case 'text':
					?>
					<input type="text" name="<?= $sInputName ?>" value="<?= htmlspecialchars( $aInput['value'] ) ?>" class="input_text" />
					<?
				break;
				case 'select':
					?>
					<select name="<?= $sInputName ?>" class="select_type">
					<?
					foreach( $aInput['values'] as $sKey => $sValue ) {
						?>
						<option value="<?= $sKey ?>" <?= ( $sKey == $aInput['value'] ) ? 'selected="selected"' : '' ?>><?= $sValue ?></option>
						<?
					}
					?>
					</select>
					<?
				break;
				case 'values':
					if( substr( $aInput['value'], 0, 2 ) == $this -> sLinkPref ) { //it is link
						$sLink = substr( $aInput['value'], 2 );
						?>
					<input type="hidden" name="<?= $sInputName ?>" value="<?= htmlspecialchars( $aInput['value'] ) ?>" />
					<a href="preValues.php?list=<?= urlencode( $sLink ) ?>" target="_blank"
					  onclick="return !window.open( this.href + '&popup=1', 'preValuesEdit', 'width=635,height=700,resizable=yes,scrollbars=yes,toolbar=no,status=no,menubar=no' );"
					  title="Edit list"><?= $sLink ?></a>
					
					<a href="javascript:void(0);" onclick="activateValuesEdit( this );" title="Change link" style="margin-left:20px;">
						<img src="templates/base/images/icons/edit.gif" alt="Change" />
					</a>
						<?
					}
					else { //it is simple list
						?>
					<textarea class="input_text" name="<?= $sInputName ?>"><?= htmlspecialchars( $aInput['value'] ) ?></textarea>
						<?
					}
				break;
			}
			?>
				</td>
				<td class="info">
					<?= $this -> getInfoIcon( $aInput['info'] ) ?>
				</td>
			</tr>
			<?
		}
		?>
		
		</table>
		<?
	}
	
	//used for parsing extra parameters
	function parseParams( $sParams ) {
		if( $sParams == '' )
			return array();
		
		$aParams = array();
		
		$aParamLines = explode( "\n", $sParams );
		
		foreach( $aParamLines as $sLine ) {
			list( $sKey, $sValue) = explode( ':', $sLine, 2 );
			$aParams[$sKey] = $sValue;
		}
		
		return $aParams;
	}
	
	function genFormMiscTab( $aField ) {
		$aForm = array(
			'Mandatory_msg' => array(
				'label'  => _t('_adm_fields_mandatory_error_message'),
				'type'   => 'textarea',
				'info'   => addslashes(_t('_adm_fields_mandatory_error_message_desc', $aField['Name'])),
				'value'  => $this -> getLangString( "_ml_pages_FieldError_{$aField['Name']}_Mandatory" ),
				/*'row_id' => 'field_mandatory_msg'*/
			),
			'Min_msg' => array(
				'label'  => _t('_adm_fields_minimum_exceed_error_message'),
				'type'   => 'textarea',
				'info'   => addslashes(_t('_adm_fields_minimum_exceed_error_message_desc', $aField['Name'])),
				'value'  => $this -> getLangString( "_ml_pages_FieldError_{$aField['Name']}_Min" ),
				'row_id' => 'field_minimum_msg'
			),
			'Max_msg' => array(
				'label'  => _t('_adm_fields_maximum_exceed_error_message'),
				'type'   => 'textarea',
                'info'   => addslashes(_t('_adm_fields_maximum_exceed_error_message_desc', $aField['Name'])),
				'value'  => $this -> getLangString( "_ml_pages_FieldError_{$aField['Name']}_Max" ),
				'row_id' => 'field_maximum_msg'
			),
			'Unique_msg' => array(
				'label'  => _t('_adm_fields_non_unique_error_message'),
				'type'   => 'textarea',
				'info'   => addslashes(_t('_adm_fields_non_unique_error_message_desc', $aField['Name'])),
				'value'  => $this -> getLangString( "_ml_pages_FieldError_{$aField['Name']}_Unique" ),
				'row_id' => 'field_unique_msg'
			),
			'Check_msg' => array(
				'label'  => _t('_adm_fields_check_error_message'),
				'type'   => 'textarea',
				'info'   => addslashes(_t('_adm_fields_check_error_message_desc', $aField['Name'])),
				'value'  => $this -> getLangString( "_ml_pages_FieldError_{$aField['Name']}_Check" ),
				'row_id' => 'field_check_msg'
			)
		);
			
		$this -> genTableEdit( $aForm, 'f3' );
	}
	
	function getInfoIcon( $sText ) {
		return '
		<img src="'.getTemplateIcon('info.gif').'" class="info_icon"
		  onmouseover="showFloatDesc(\'' . htmlspecialchars( $sText ) . '\');"
		  onmousemove="moveFloatDesc( event )"
		  onmouseout="hideFloatDesc();" />
		';
	}
	
	function getTypeOptions( $sActive ) {
		$sRet = '';
		
		foreach( $this -> aTypes as $sKey => $sValue ) {
			$sRet .= '<option value="' . $sKey . '" ' . ( $sActive == $sKey ? 'selected="selected"' : '') . '>' . $sValue . '</option>';
		}
		
		return $sRet;
	}
	
	function getLangString( $sKey ) {
		if( $sKey == '' )
			return '';
		
		$sKey_db    = addslashes( $sKey );
		$sString_db = addslashes( $sString );
		
		$sQuery = "SELECT `ID` FROM `sys_localization_keys` WHERE `Key` = '$sKey_db'";
		$iKeyID = (int)db_value( $sQuery );
		
		if( !$iKeyID )
			return '';
		
		$sQuery = "
			SELECT `String` FROM `sys_localization_strings`
			WHERE `IDKey` = $iKeyID AND `IDLanguage` = {$this -> sLangID}";
		
		return (string)db_value( $sQuery );
	}
	
	function saveItem( $aData ) {
		$this -> genSaveItemHeader();
		$this -> isHaveErrors = false;
		//echoDbg( $aData );
		$iItemID = (int)$aData['id'];

		$aItem = db_assoc_arr( "SELECT * FROM `ml_pages_fields` WHERE `ID` = $iItemID" );
		
		if( !$aItem ) {
			$this -> genSaveItemError(_t('_adm_fields_warning_field_not_found'));
			$this -> genSaveItemFooter();
			return false;
		}
		
		// just a flag
		$bHaveErrors = false;
		
		// this array will be put into db
		$aUpdate = array();
		
		// check name
		if( $aItem['Type'] != 'system' and $aItem['Deletable'] ) { //we can change the name
			
			$sName = trim( strip_tags( process_pass_data( $aData['Name'] ) ) );
			
			if( $sName === '' ) {
				$this -> genSaveItemError( _t('_adm_fields_error_you_must_enter_name'), 'Name' );
				$bHaveErrors = true;
			} elseif( $aItem['Type'] != 'block' and !preg_match( '/^[a-z][a-z0-9_]*$/i', $sName ) ) {
				//$this -> genSaveItemError( _t('_adm_fields_error_name_latin'), 'Name' );
				$bHaveErrors = true;
			} elseif($GLOBALS['MySQL']->getOne("SELECT COUNT(*) FROM `ml_pages_fields` WHERE `Name`='" . $sName . "' AND `ID`<>'" . $iItemID . "'") || ($sName != $aItem['Name'] && $GLOBALS['MySQL']->isFieldExists('ml_pages_main', $sName))) {
				$this -> genSaveItemError( _t('_adm_fields_error_name_already_exists'), 'Name' );
				$bHaveErrors = true;
			} elseif( $sName == $aItem['Name'] ) {
				// all ok. don't change
			} else
				$aUpdate['Name'] = $sName; //change
		}
		
		$sNewName = isset( $aUpdate['Name'] ) ? $aUpdate['Name'] : $aItem['Name'];
		
		$this -> fieldCaption = "_ml_pages_FieldCaption_{$sNewName}_{$this -> areaPageName}"; // _ml_pages_FieldCaption_Sex_Join
		$this -> fieldDesc    = "_ml_pages_FieldDesc_{$sNewName}_{$this -> areaPageName}";    // _ml_pages_FieldDesc_Sex_Join
		
		// check Caption
		$sCaption = trim( process_pass_data( $aData['Caption'] ) );
		
		if( $sCaption === '' ) {
			$this -> genSaveItemError( _t('_adm_fields_error_you_must_enter_caption'), 'Caption' );
			$bHaveErrors = true;
		} elseif( $this -> getLangString( $this -> fieldCaption ) == $sCaption ) {
			// all ok dont change
		} else
			$this -> updateLangString( $this -> fieldCaption, $sCaption );
		
		// check Description
		$sDesc = trim( process_pass_data( $aData['Desc'] ) );
		if( $this -> getLangString( $this -> fieldDesc ) != $sDesc )
			$this -> updateLangString( $this -> fieldDesc, $sDesc );
			
		
		
		// check type
		if( $aItem['Type'] != 'system' and $aItem['Type'] != 'block' ) {
			//we can change the type
			$sType = trim( strip_tags( process_pass_data( $aData['Type'] ) ) );

			if( !isset( $this -> aTypes[$sType] ) ) {
				$this -> genSaveItemError( _t('_adm_fields_error_i_dont_know_this_type'), 'Type' );
				$bHaveErrors = true;
			} elseif( $sType == $aItem['Type'] ) {
				// all ok. don't change
			} else {
				$aUpdate['Type'] = $sType; //change
			}
			// check the additional properties
			if( !$bHaveErrors ) { // do not continue if have errors
				
				// check selectors controls
				if( $sType == 'select_one' ) {
					if( $aData['Control_one'] == $aItem['Control'] ) {
						//all ok
					} elseif( $aData['Control_one'] == 'select' or $aData['Control_one'] == 'radio' ) {
						$aUpdate['Control'] = $aData['Control_one'];
					} elseif ($aData['AddOn'] && $aData['IdField']){
						$this -> genSaveItemError(_t('_adm_fields_error_you_entered_incorrect_value'), 'Default' );
						$bHaveErrors = true;
					} else {
						$this -> genSaveItemError( _t('_adm_fields_error_i_dont_know_this_control_type'), 'Control_one' );
						$bHaveErrors = true;
					}
				} elseif( $sType == 'select_set' ) {
					if( $aData['Control_set'] == $aItem['Control'] ) {
						//all ok
					} elseif( $aData['Control_set'] == 'select' or $aData['Control_set'] == 'checkbox' ) {
						$aUpdate['Control'] = $aData['Control_set'];
					} else {
						$this -> genSaveItemError( _t('_adm_fields_error_i_dont_know_this_control_type'), 'Control_set' );
						$bHaveErrors = true;
					}
				} else
					$aUpdate['Control'] = null;
				
				//check Min
				$iMin = trim($aData['Min']);
				if($iMin === '' || $sType == 'bool' || $sType == 'select_one' || $sType == 'select_set')
					$iMin = null;
				else {
					$iMin = (int)$iMin;
					if(($sType == 'area' || $sType == 'html_area') && $iMin > 65534)
						$iMin = 65534;
					else if($sType != 'area' && $sType != 'html_area' && $iMin > 254)
						$iMin = 254;
					else if($iMin < 0)
						$iMin = 0;
				}
				$aUpdate['Min'] = $iMin;
				
				//check Max
				$iMax = trim($aData['Max']);
				if($iMax === '' || $sType == 'bool' || $sType == 'select_one' || $sType == 'select_set' )
					$iMax = null;
				else {
					$iMax = (int)$iMax;
					if(($sType == 'area' || $sType == 'html_area') && $iMax > 65534)
						$iMax = 65534;
					else if($sType != 'area' && $sType != 'html_area' && $iMax > 254)
						$iMax = 254;
					else if($iMax < 0)
						$iMax = 0;
				}
				$aUpdate['Max'] = $iMax;
				
				// set min and max search age
				if( $sNewName == 'DateOfBirth' )
					$this -> setMinMaxAge( ( $iMin ? $iMin : 18 ), ( $iMax ? $iMax : 75 ) );
				
				//check Check :)
				if( $sType == 'select_one' or $sType == 'select_set' or $sType == 'bool' )
					$aUpdate['Check'] = '';
				else {
					$sCheck = trim( process_pass_data( $aData['Check'] ) );
					
					if( $aItem['Check'] != $sCheck )
						$aUpdate['Check'] = $sCheck;
				}
				
				//Unique
				$aUpdate['Unique'] = (
					isset( $aData['Unique'] ) and
					$aData['Unique'] == 'yes' and
					( $sType == 'text' or $sType == 'area' or $sType == 'html_area' or $sType == 'num' )
				) ? 1 : 0;
				
				//Mandatory
				$aUpdate['Mandatory'] = ( isset( $aData['Mandatory'] ) and $aData['Mandatory'] == 'yes' ) ? 1 : 0;
				
				$aUpdate['WithPhoto'] = ( isset( $aData['WithPhoto'] ) and $aData['WithPhoto'] == 'yes' ) ? 1 : 0;
				$aUpdate['WithTime'] = ( isset( $aData['WithTime'] ) and $aData['WithTime'] == 'yes' ) ? 1 : 0;
				$aUpdate['IdField'] = trim( process_pass_data( $aData['IdField'] ) );
				$aUpdate['CaptionField'] = trim( process_pass_data( $aData['CaptionField'] ) );
				$aUpdate['DepField'] = trim( process_pass_data( $aData['DepField'] ) );
				$aUpdate['DepParentField'] = trim( process_pass_data( $aData['DepParentField'] ) );				
				$aUpdate['MediaType'] = trim( process_pass_data( $aData['MediaType'] ) );		
				$aUpdate['Attribute'] = trim( process_pass_data( $aData['Attribute'] ) );							
				//$aUpdate['Multiplyable'] = ( isset( $aData['Multiplyable'] ) and $aData['Multiplyable'] == 'yes' ) ? 1 : 0;
				if (isset( $aData['Multiplyable'] ) and $aData['Multiplyable'] == 'yes' )
				{
					$aUpdate['Multiplyable'] = 1;
					$aUpdate['Attribute'] = 'none';
				}
				if ($aUpdate['Attribute'] == 'multiplyable')
					$aUpdate['Multiplyable'] = 1;
				elseif ($aUpdate['Attribute'] == 'addon')
					$aUpdate['Multiplyable'] = 0;
				else
					$aUpdate['Multiplyable'] = 0;
					
				$aUpdate['AddOn'] = ( isset( $aData['AddOn'] ) and $aData['AddOn'] == 'yes' ) ? 1 : 0;
				
				$aUpdate['MultipleCss'] = trim( process_pass_data( $aData['MultipleCss'] ) );
				
				$aUpdate['CaptionDisable'] = (isset( $aData['CaptionDisable'] ) ) ? 1 : 0;
				
				$aUpdate['Link'] = ( isset( $aData['Link'] ) ) ? 1 : 0;
				
				$sMultipleStyle = trim( process_pass_data( $aData['MultipleStyle'] ) );
				if( !$sMultipleStyle )
					$sMultipleStyle = 'Unordered';
										
				$aUpdate['MultipleStyle'] = $sMultipleStyle;
									
				//check Values
				if( $sType == 'select_one' or $sType == 'select_set' ) {
					$sValues = trim( strip_tags( process_pass_data( $aData['Values'] ) ) );
					
					$sValues = str_replace( "\r", "\n", $sValues );   // for mac
					$sValues = str_replace( "\n\n", "\n", $sValues ); // for win
					                                                ; // for *nix ;)
					
					if( $sValues === '' ) {
						$this -> genSaveItemError( _t('_adm_fields_error_you_must_enter_values'), 'Values' );
						$bHaveErrors = true;
					} elseif( $sValues != $aItem['Values'] ) {
						if( substr( $sValues, 0, 2 ) == $this -> sLinkPref and !$this -> checkValuesLink( substr( $sValues, 2 ) ) ) {
							$this -> genSaveItemError( _t('_adm_fields_error_you_entered_incorrect_link'), 'Values' );
							$bHaveErrors = true;
						} else
							$aUpdate['Values'] = $sValues;
					}
					
					// get LKey
					//$sUseLKey = trim( process_pass_data( $aData['UseLKey'] ) );
					//if( !$sUseLKey )
						//$sUseLKey = 'LKey';

					//$sUseLKey = trim( process_pass_data( $aData['UseLKey'] ) );
					//if( !$sUseLKey )
					//	$sUseLKey = 'LKey';
											
					//$aUpdate['UseLKey'] = $sUseLKey;

					$sUseLKey = trim( process_pass_data( $aData['UseLKey'] ) );
					if( !$sUseLKey )
						$sUseLKey = 'LKey';
											
					$aUpdate['UseLKey'] = $sUseLKey;


															
					if( substr( $sValues, 0, 2 ) != $this -> sLinkPref ) { // if not a link
						//Add Lang key for each value: _FieldValues_Example
						
						$aValues2LF = explode("\n", $sValues);
						foreach ( $aValues2LF as $sValues2LF ) {
							$sLFKey = '_FieldValues_' . $sValues2LF;
							$sLFValue = $sValues2LF;
							//print "{$sLFKey}<br />";
							if( $this -> getLangString( $sLFKey ) != $sLFValue )
								$this -> updateLangString( $sLFKey, $sLFValue );
						}
					}
					
				} elseif( $aItem['Values'] != '' )
					$aUpdate['Values'] = '';
				
				if( !$bHaveErrors ) {
					//Default
					switch( $sType ) {
						/*case 'photo':
							$aUpdate['PhotoFor'] = $aData['PhotoFor'];
						break;*/
						case 'text':
							$aUpdate['Default'] = trim( process_pass_data( $aData['Default'] ) );
						break;
						case 'area':
							$aUpdate['Default'] = trim( process_pass_data( _t( $aData['Default'] ) ) );
						break;
						case 'pass':
						case 'html_area':
						case 'select_set':
							$aUpdate['Default'] = '';
						break;
						case 'num':
							$aUpdate['Default'] = (int)$aData['Default'];
						break;
						case 'bool':
							$aUpdate['Default'] = (int)(bool)$aData['Default'];
						break;
						case 'range':
							if( trim( $aData['Default'] ) == '' )
								$aUpdate['Default'] = '';
							else {
								list( $sFirst, $sSecond ) = explode( '-', trim( $aData['Default'], 2 ) ); 
								$sFirst  = (int)trim( $sFirst );
								$sSecond = (int)trim( $sSecond );
								$aUpdate['Default'] = "$sFirst-$sSecond";
							}
						break;
						case 'date':
							if( $aData['Default'] === '' )
								$aUpdate['Default'] = '';
							else
								$aUpdate['Default'] = date( 'Y-m-d', strtotime( trim( process_pass_data( $aData['Default'] ) ) ) );
						break;
						
						case 'select_one':
							$sDefault = trim( process_pass_data( $aData['Default'] ) );
							if( $sDefault === '' )
								$aUpdate['Default'] = '';
							else
								$aUpdate['Default'] = $sDefault;

							/*else {
								if( $this -> checkSelectDefault( $sValues, $sDefault ) )
									$aUpdate['Default'] = $sDefault;
								else {
									$this -> genSaveItemError(_t('_adm_fields_error_you_entered_incorrect_value'), 'Default' );
									$bHaveErrors = true;
								}
							}*/
						break;
					}
					
					//matching. not implemented yet
				}
			}
		}
		
		if( $aItem['Type'] == 'block' and $this -> iAreaID == 1 ) { //Join
			//get JoinPage
			$iJoinPage = (int)$aData['JoinPage'];
			if( $aItem['JoinPage'] != $iJoinPage )
				$aUpdate['JoinPage'] = $iJoinPage;
		}

			
		//system fields properties
		if( $aItem['Name'] == 'Keyword' ) {
			if( is_array( $aData['KeywordFields'] ) ) {
				$sKeywordFields = implode( "\n", $aData['KeywordFields'] );
				
				if( process_pass_data( $sKeywordFields ) != $aItem['Extra'] )
					$aUpdate['Extra'] = $sKeywordFields;
			}
		}
		
		if( $aItem['Name'] == 'Couple' ) {
			if( is_array( $aData['CoupleFields'] ) ) {
				$sKeywordFields = implode( "\n", $aData['CoupleFields'] );
				
				if( process_pass_data( $sKeywordFields ) != $aItem['Extra'] )
					$aUpdate['Extra'] = $sKeywordFields;
			}
		}
		
		// update error messages
		foreach( array( 'Mandatory', 'Min', 'Max', 'Unique', 'Check' ) as $sErrName ) {
			$sErrMsg = trim( process_pass_data( $aData[$sErrName . '_msg'] ) );
			if( empty($sErrMsg) )
				continue;
			
			$sErrKey = "_ml_pages_FieldError_{$sNewName}_{$sErrName}";
			
			$this -> updateLangString( $sErrKey, $sErrMsg );
		}
		
		// add matching
		if( isset( $aData['MatchField'] ) and (int)$aData['MatchField'] != $aItem['MatchField'] )
			$aUpdate['MatchField'] = (int)$aData['MatchField'];
		
		if( isset( $aData['MatchPercent'] ) and (int)$aData['MatchPercent'] != $aItem['MatchPercent'] )
			$aUpdate['MatchPercent'] = (int)$aData['MatchPercent'];
		
		if( !empty( $aUpdate ) and !$bHaveErrors ) {
			$this -> doUpdateItem( $aItem, $aUpdate );
			//if( isset( $aUpdate['Name'] ) )
				//$this -> genSaveItemFormUpdate( 'updateItem', $iItemID, $aUpdate['Name'] );
			
			if( $aItem['Type'] == 'block' and $aUpdate['Name'] ) {
				$sQuery = "
					UPDATE `sys_page_compose` SET
						`Caption` = '_ml_pages_FieldCaption_" . addslashes( $sNewName ) . "_View'
					WHERE
						`Func` = 'PFBlock' AND
						`Content` = '$iItemID'
						AND `Page` = 'ml_pages_view'
					";
				
				db_res( $sQuery );
			}
		}
		
		if( !$bHaveErrors )
			$this -> genSaveItemFormClose();
		
		$this -> genSaveItemFooter();
	}
	
	function setMinMaxAge( $iMin, $iMax ) {
		setParam( 'search_start_age', $iMin );
		setParam( 'search_end_age',   $iMax );
	}
	
	function checkValuesLink( $sKey ) {
		global $aPreValues;
		
		return isset( $aPreValues[$sKey] );
	}
	
	function checkSelectDefault( $sValues, $sDefault ) {
		global $aPreValues;
		
		if( substr( $sValues, 0, 2 ) == $this -> sLinkPref ) { //it is link
			$sKey = substr( $sValues, 2 );
			return isset( $aPreValues[$sKey][$sDefault] );
		} else {
			$aValues = explode( "\n", $sValues );
			return in_array( $sDefault, $aValues );
		}
	}
	
	function doUpdateItem( $aItem, $aUpdate ) {
		global $aPreValues;
		
		$aUpdateStrs = '';
		foreach( $aUpdate as $sKey => $sValue ) {
			if( is_null( $sValue ) )
				$aUpdateStrs[] = "`$sKey` = NULL";
			else
				$aUpdateStrs[] = "`$sKey` = '" . addslashes( $sValue ) . "'";
		}
		
		$sQuery = "
			UPDATE `ml_pages_fields` SET 
				" . implode( ", 
				", $aUpdateStrs ) . "
			WHERE `ID` = {$aItem['ID']}";
		db_res( $sQuery );
		
		if( //we need alter Profiles table
			$aItem['Type'] != 'block' and (
				isset( $aUpdate['Type'] ) or
				isset( $aUpdate['Name'] ) or
				isset( $aUpdate['Values'] ) or
				isset( $aUpdate['Default'] )
			)
		) {
			$aAlter = array(
				'Type'    => isset( $aUpdate['Type']    ) ? $aUpdate['Type']    : $aItem['Type'],
				'Name'    => isset( $aUpdate['Name']    ) ? $aUpdate['Name']    : $aItem['Name'],
				'Values'  => isset( $aUpdate['Values']  ) ? $aUpdate['Values']  : $aItem['Values'],
				'Default' => isset( $aUpdate['Default'] ) ? $aUpdate['Default'] : $aItem['Default'],
			);
			
			if( substr( $aAlter['Values'], 0, 2 ) == $this -> sLinkPref || (db_value("SHOW TABLES LIKE '{$aAlter['Values']}'") && $aAlter['Type'] == 'select_one'))
				$aAlter['Type'] .= '_linked';
			
			$sQuery = "ALTER TABLE ml_pages_main CHANGE `{$aItem['Name']}` `{$aAlter['Name']}` {$this -> aTypesAlter[$aAlter['Type']]}";
			$sReplacedVal = ($aAlter['Default'] != '') ? "default '" . addslashes( $aAlter['Default'] ) . "'" : "";
			$sQuery = str_replace( '{default}', $sReplacedVal, $sQuery );
			
			if( $aAlter['Type'] == 'select_one' or $aAlter['Type'] == 'select_set' ) { //insert values
				$aValuesAlter = explode( "\n", $aAlter['Values'] ); //explode values to array
				
				foreach( $aValuesAlter as $iKey => $sValue ){ //add slashes to every value
					$sValue = str_replace( '\\', '\\\\', $sValue );
					$sValue = str_replace( '\'', '\\\'', $sValue );
					$aValuesAlter[$iKey] = $sValue;
				}
				
				$sValuesAlter = " '" . implode( "', '", $aValuesAlter ) . "' "; // implode values to string like 'a','b','c\'d'
				$sQuery = str_replace( '{values}', $sValuesAlter, $sQuery ); //replace it in place
			} elseif( $aAlter['Type'] == 'select_set_linked' ) {
				$sLink = substr( $aAlter['Values'], 2 );
				$aValuesAlter = array_keys( $aPreValues[$sLink] );
				
				$sValuesAlter = implode( ', ', $aValuesAlter );
				$sValuesAlter = str_replace( '\\', '\\\\', $sValuesAlter );
				$sValuesAlter = str_replace( '\'', '\\\'', $sValuesAlter );
				
				$sValuesAlter = "'" .str_replace( ', ', "', '", $sValuesAlter ) ."'";
				
				$sQuery = str_replace( '{values}', $sValuesAlter, $sQuery ); //replace it in place
			}
			
			db_res( $sQuery );
		}
	}
	
	function createNewField() {
		$iNewID = 0;
		
		//try to insert new item
		if( db_res( "INSERT INTO `ml_pages_fields` (`Name`, `Type`, `Categories` ) VALUES ('NEW_ITEM', 'text', {$this->iCategory})", 0 ) and $iNewID = db_last_id() ) {
			//if success - try to alter table
			if( !db_res( "ALTER TABLE ml_pages_main ADD `NEW_ITEM` varchar(255) NOT NULL default ''", 0 ) ) {
				//if couldn't alter - delete inserted field
				db_res( "DELETE FROM `ml_pages_fields` WHERE `ID` = $iNewID AND FIND_IN_SET ({$this->iCategory}, `Categories`)" );
				$iNewID = 0;
			}
		}
		
		return $iNewID;
	}
	
	function createNewBlock() {
		db_res( "INSERT INTO `ml_pages_fields` (`Name`, `Type`, `Categories` ) VALUES ('NEW BLOCK', 'block', {$this->iCategory})", 0 );
		$iNewID = db_last_id();
		
		db_res( "
			INSERT INTO `sys_page_compose`
				( `Desc`, `Caption`, `Visible`, `Func`, `Content`, `Page` )
			VALUES
				( 'Page Fields Block', '_ml_pages_FieldCaption_NEW BLOCK_View', 'non,memb', 'PFBlock', '$iNewID', 'ml_pages_view' )
		" );
		
		
		return $iNewID;
	}
	
	function updateLangString( $sKey, $sString ) {
		
		if( $sKey == '' )
			return false;
		
		$sKey_db    = addslashes( $sKey );
		$sString_db = addslashes( $sString );
		
		$sQuery = "SELECT `ID` FROM `sys_localization_keys` WHERE `Key` = '$sKey_db'";
		$iKeyID = (int)db_value( $sQuery );
		
		if( !$iKeyID ) { //create key
			$sQuery = "INSERT INTO `sys_localization_keys` (`IDCategory`,`Key`) VALUES (32,'$sKey_db')";
			db_res( $sQuery );
			$iKeyID = db_last_id();
		}
		
		$sQuery = "
			SELECT COUNT( * ) FROM `sys_localization_strings`
			WHERE `IDKey` = $iKeyID AND `IDLanguage` = {$this -> sLangID}";
		
		$iCount = (int)db_value( $sQuery );
		
		if( $iCount ) {
			$sQuery = "
				UPDATE `sys_localization_strings`
				SET `String` = '$sString_db'
				WHERE `IDKey` = $iKeyID AND `IDLanguage` = {$this -> sLangID}";
			
			db_res( $sQuery );
		} else {
			$sQuery = "INSERT INTO `sys_localization_strings` VALUES ( $iKeyID, {$this -> sLangID}, '$sString_db' )";
			db_res( $sQuery );
		}
		
		compileLanguage( $this -> sLangID );
	}
	
	function genSaveItemHeader() {
		?>
<html><script type="text/javascript">
		<?
	}
	
	function genSaveItemError( $sText, $sField = '' ) {
		$this -> isHaveErrors = true;
		
		if( !$sField )
			echo "alert( '" . addslashes( $sText ) . "' );";
		else {
			?>
			parent.genEditFormError( '<?= $sField ?>', '<?= addslashes( $sText ) ?>' );
			<?
		}
	}
	
	function genSaveItemFooter() {
		?>

</script></html>
		<?
	}
	
	function deleteItem( $iItemID ) {
		$this -> genSaveItemHeader();
	
		$aItem = db_assoc_arr( "SELECT * FROM `ml_pages_fields` WHERE `ID` = $iItemID" );
		
		if( !$aItem )
			$this -> genSaveItemError(_t('_adm_fields_warning_item_not_found'));
		elseif( $aItem['Type'] == 'system' or !(int)$aItem['Deletable'] )
			$this -> genSaveItemError(_t('_adm_fields_error_field_cannot_be_deleted'));
		else{
			$sQuery = "DELETE FROM `ml_pages_fields` WHERE `ID` = $iItemID ";
			db_res( $sQuery );
			
			if( $aItem['Type'] == 'block' )
			{
				db_res( "DELETE FROM `sys_page_compose` WHERE `Func` = 'PFBlock' AND `Content` = '$iItemID'" );
			}
			if( $aItem['Type'] != 'block' )
				db_res( "ALTER TABLE ml_pages_main DROP `{$aItem['Name']}`" );
			
			$this -> genSaveItemFormUpdate( 'deleteItem', $iItemID );
			//$this -> genSaveItemFormClose();
		}
		$this -> genSaveItemFooter();
	}
	
	function genSaveItemFormUpdate( $sText, $iItemID, $sNewName = '' ) {
		?>
		parent.updateBuilder( '<?= $sText ?>', <?= $iItemID ?>, '<?= addslashes( $sNewName ) ?>' );
		<?
	}
	
	function genSaveItemFormClose() {
		?>
		parent.hideEditForm();
		<?
	}
}

class BxDolPSFMAreaJSONObj {
	
	function BxDolPSFMAreaJSONObj( $oArea ) {
		$this -> id = $oArea -> iAreaID;
		
		$this -> active_blocks   = array();
		$this -> inactive_blocks = array();
		$this -> active_items    = array();
		$this -> inactive_items  = array();
		
		foreach( $oArea -> aBlocks as $iID => $sName )
			$this -> active_blocks[] = new BxDolPSFMItem( $iID, $sName );
		
		foreach( $oArea -> aItems as $iID => $aItem )
			$this -> active_items[] = new BxDolPSFMItem( $iID, $aItem[0], $aItem[1] );
		
		foreach( $oArea -> aBlocksInac as $iID => $sName )
			$this -> inactive_blocks[] = new BxDolPSFMItem( $iID, $sName );
		
		foreach( $oArea -> aItemsInac as $iID => $sName )
			$this -> inactive_items[] = new BxDolPSFMItem( $iID, $sName );
	}
}

/* Used for JSON generation */
class BxDolPSFMItem {
	
	function BxDolPSFMItem( $iID, $sName, $iBlock = 0 ) {
		$this -> id = $iID;
		$this -> name = $sName;
		
		$this -> block = $iBlock;
	}
}



/**
 *	Cacher created only for creating a cache :)
 */
class BxDolPSFMCacher {
	
	var $aAreasProps;
	
	function BxDolPSFMCacher($iCategory) {
		if (!$iCategory) return false;

		$this->iCategory = $iCategory;
		$this -> sCacheFile = BX_DIRECTORY_PATH_DBCACHE . 'db_' . 'ml_pages_fields'. '_' . md5($site['ver'] . $site['build'] . $site['url']) . '.php';		
		//additional properties for caching blocks
		$aAddBlockProps = array( 
			'Join' => array(
				'Caption'   => '_ml_pages_FieldCaption_{Name}_Join',
				'Categories'   => '{Categories}',
				//'Desc'      => '_ml_pages_FieldDesc_{Name}_Join'
			),
			'Edit' => array(
				'Caption'   => '_ml_pages_FieldCaption_{Name}_Edit',
				'Categories'   => '{Categories}',
				//'Desc'      => '_ml_pages_FieldDesc_{Name}_Edit'
			),
			'View' => array(
				'Caption'   => '_ml_pages_FieldCaption_{Name}_View',
				'Categories'   => '{Categories}',
				//'Desc'      => '_ml_pages_FieldDesc_{Name}_View'
			),
			'Browse' => array(
				'Caption'   => '_ml_pages_FieldCaption_{Name}_Browse',
				'Categories'   => '{Categories}',
				//'Desc'      => '_ml_pages_FieldDesc_{Name}_View'
			),
		);

		//additional properties for caching items
		$aAddProps = array( 
			'Join' => array(
				'Caption'   => '_ml_pages_FieldCaption_{Name}_Join',
				'Desc'      => '_ml_pages_FieldDesc_{Name}_Join',
				'MandatoryMsg' => '_ml_pages_FieldError_{Name}_Mandatory',
				'MinMsg'    => '_ml_pages_FieldError_{Name}_Min',
				'MaxMsg'    => '_ml_pages_FieldError_{Name}_Max',
				'UniqueMsg' => '_ml_pages_FieldError_{Name}_Unique',
				'CheckMsg'  => '_ml_pages_FieldError_{Name}_Check'
			),
			'Edit' => array(
				'Caption'   => '_ml_pages_FieldCaption_{Name}_Edit',
				'Desc'      => '_ml_pages_FieldDesc_{Name}_Edit',
				'MandatoryMsg'  => '_ml_pages_FieldError_{Name}_Mandatory',
				'MinMsg'    => '_ml_pages_FieldError_{Name}_Min',
				'MaxMsg'    => '_ml_pages_FieldError_{Name}_Max',
				'UniqueMsg' => '_ml_pages_FieldError_{Name}_Unique',
				'CheckMsg'  => '_ml_pages_FieldError_{Name}_Check'
			),
			'View' => array(
				'Caption'   => '_ml_pages_FieldCaption_{Name}_View',
				'Desc'      => '_ml_pages_FieldDesc_{Name}_View'
			),
			'Browse' => array(
				'Caption'   => '_ml_pages_FieldCaption_{Name}_Browse',
				'Desc'      => '_ml_pages_FieldDesc_{Name}_Browse'
			),
			'Search' => array(
				'Caption'   => '_ml_pages_FieldCaption_{Name}_Search',
				'Desc'      => '_ml_pages_FieldDesc_{Name}_Search'
			)
		);
		
		$this -> aAreasProps = array (
			1  => array( 'Title' => 'Join',            'Order' => 'JoinOrder',         'Block' => 'JoinBlock',         'AddSelect' => 'Control,Extra,Min,Max,Values,Check,Unique,Mandatory,UseLKey,Default,Multiplyable,Attribute,AddOn,WithPhoto,WithTime,IdField,CaptionField,DepField,DepParentField,MediaType', 'AddBlockProps' => $aAddBlockProps['Join'],   'AddProps' => $aAddProps['Join']   ),
			2  => array( 'Title' => 'Edit',    'Order' => 'EditOrder',      'Block' => 'EditBlock',      'AddSelect' => 'Control,Extra,Min,Max,Values,Check,Unique,Mandatory,UseLKey,Multiplyable,Attribute,AddOn,WithPhoto,WithTime,IdField,CaptionField,DepField,DepParentField,MediaType', 'AddBlockProps' => $aAddBlockProps['Edit'],   'AddProps' => $aAddProps['Edit']   ),
			5  => array( 'Title' => 'View ',    'Order' => 'ViewOrder',      'Block' => 'ViewBlock',      'AddSelect' => 'Values,UseLKey,Multiplyable,Attribute,AddOn,WithPhoto,WithTime,CaptionDisable,MultipleStyle,MultipleCss,Link,IdField,CaptionField,DepField,DepParentField,MediaType', 'AddBlockProps' => $aAddBlockProps['View'],                                                'AddProps' => $aAddProps['View']   ),
			9  => array( 'Title' => 'Browse ',    'Order' => 'BrowseOrder',      'Block' => 'BrowseBlock',      'AddSelect' => 'Values,UseLKey,Multiplyable,Attribute,AddOn,WithPhoto,WithTime,CaptionDisable,MultipleStyle,MultipleCss,Link,IdField,CaptionField,DepField,DepParentField,MediaType', 'AddBlockProps' => $aAddBlockProps['Browse'],                                                'AddProps' => $aAddProps['Browse']   ),

			//special areas
			100 => array( 'Title' => 'All Fields. PC cache', 'AddSelect' => 'Default,Unique,Extra' ),
			101 => array( 'Title' => 'Matching Fields',       'AddSelect' => 'MatchField,MatchPercent' ),
		);
		
	}
	function createOldCache() {
		$rCacheFile = @fopen( $this -> sCacheFile, 'w' );
		if( !$rCacheFile ) {
			echo '<br /><b>Warning!</b> Cannot open Profile Fields cache file (' . $this -> sCacheFile . ') for write.';
			return false;
		}
		
		$sCacheString = "// cache of Profile Fields\n\nreturn array(\n  //areas\n";
		
		// get areas
		foreach ($this -> aAreasProps as $iAreaID => $aArea) {
			$oArea = new BxDolPageFieldsArea( $iAreaID, $this, $this->iCategory );
			
			$sCacheString .= $oArea -> getCacheString();
		}
		
		$sCacheString .= ");\n";
		
		
		$iRes = fwrite( $rCacheFile, $sCacheString );
		
		fclose( $rCacheFile );
		
		if( $iRes === false ) {
			echo '<br /><b>Warning!</b> Cannot write to Profile Fields cache file (' . $this -> sCacheFile . ').';
			return false;
		}
		
		return true;
	}

	function createCache() {
				global $site;
				if ((int)$site['build'] < 3)
				{
					$this->createOldCache();
					return true;
				}
        // clear page blocks cache for join and search form blocks	
        bx_import('BxDolPageViewAdmin'); // for clear caching abilities 
        $oPageViewCacher = new BxDolPageViewCacher ('', '');
        $oCacheBlocks = $oPageViewCacher->getBlocksCacheObject ();
        $aBlockIds = array (4, 146, 147, 156);
        $aKeys = array (
            true.'tab'.false, 
            false.'tab'.false, 
            true.'popup'.false,
            false.'popup'.false,
            true.'tab'.true,
            false.'tab'.true,
            true.'popup'.true,
            false.'popup'.true
        );
        foreach ($aBlockIds as $iBlockId)
            foreach ($aKeys as $sKey)
                $oCacheBlocks->delData($oPageViewCacher->genBlocksCacheKey ($iBlockId.$sKey));



	
		$sCacheString = "// cache of Profile Fields\n\nreturn array(\n  //areas\n";
		// get areas
		foreach ($this -> aAreasProps as $iAreaID => $aArea) {
			$oArea = new BxDolPageFieldsArea( $iAreaID, $this, $this->iCategory );
			
			$sCacheString .= $oArea -> getCacheString();
		}
		
		$sCacheString .= ");\n";
		
		
        $aCacheArray = eval($sCacheString);

        $oCache = $GLOBALS['MySQL']->getDbCacheObject();
        return $oCache->setData ($GLOBALS['MySQL']->genDbCacheKey('ml_pages_fields'), $aCacheArray); 
	}
	
}

/**
 * ProfileFieldsArea
 * Used primarily to create cache strings
 */ 
class BxDolPageFieldsArea {
	
	var $id;
	var $oParent;
	var $sTitle;
	var $sBlockCol;
	var $sOrderCol;
	var $aBlocks;
	var $aPages;
	
	function BxDolPageFieldsArea( $iAreaID, &$oParent, $iCategory) {
		if (!$iCategory)
			return false;
		$this->iCategory = $iCategory;
		$this -> id = $iAreaID;
		$this -> oParent = &$oParent;

		$this -> sTitle         = $this -> oParent -> aAreasProps[ $this -> id ]['Title'];
		$this -> sBlockCol      = $this -> oParent -> aAreasProps[ $this -> id ]['Block'];
		$this -> sOrderCol      = $this -> oParent -> aAreasProps[ $this -> id ]['Order'];
		$this -> sAddSelect     = $this -> oParent -> aAreasProps[ $this -> id ]['AddSelect'];
		$this -> aAddBlockProps = $this -> oParent -> aAreasProps[ $this -> id ]['AddBlockProps'];
		$this -> aAddProps      = $this -> oParent -> aAreasProps[ $this -> id ]['AddProps'];


	}
	//`JoinPage` = $iPage
	function getBlocks( $sAddSort = '1' ) {
		
		$aBlocks = array();
		$sQuery = "
			SELECT
				`ID`,
				`Name`,
				`Categories`
			FROM `ml_pages_fields`
			WHERE
				`Type` = 'block' AND
				`{$this -> sOrderCol}` IS NOT NULL AND
				$sAddSort
				AND FIND_IN_SET ({$this->iCategory}, `Categories`)
			ORDER BY
				`{$this -> sOrderCol}`
		";
		$rBlocks = db_res( $sQuery );
		while( $aBlock = mysql_fetch_assoc( $rBlocks ) ) {
			$aBlocks[ $aBlock['ID'] ] = $aBlock['Name'];
		}
		return $aBlocks;
	}	
	function getCacheString() {
		
		$sCacheString = "\n  //{$this -> sTitle}\n  {$this -> id} => array(\n"; //!pasd
		if( $this -> id == 1 ) {
			$this -> aPages = $this -> getJoinPages();
			$sCacheString .= "    //pages\n";
			foreach( $this -> aPages as $iPage ){
				$this -> aBlocks = $this -> getBlocks( "`JoinPage` = $iPage" );
				$sCacheString .= "    $iPage => array(\n"; //!pasd
					$sCacheString .= $this -> getBlocksCacheString( '  ' );
				$sCacheString .= "    ),\n";
			}
		} else {
			if( $this -> id == 100 or $this -> id == 101 )
				$this -> aBlocks = array( 0 => '' );
			else
				$this -> aBlocks = $this -> getBlocks();
			
			$sCacheString .= $this -> getBlocksCacheString();
		}
		
		$sCacheString .= "  ),\n";
		return $sCacheString;
	}


	function getBlocksCacheString( $sPrefix = '' ) {
		$sCacheString = "$sPrefix    //blocks\n";
		foreach ($this -> aBlocks as $iBlockID => $sBlockName) {
			$sBlockName = $this -> addSlashes( $sBlockName );
			$sCacheString .= "$sPrefix    $iBlockID => array(\n"; //!pasd
			$sCacheString .= "$sPrefix      //block properties\n";
			
			// add additional properties
			if( is_array($this -> aAddBlockProps) )
				foreach ($this -> aAddBlockProps as $sProp => $sValue) {
					if ($sProp == 'Categories')
						$sPropValue = str_replace( '{Categories}', db_value("SELECT `Categories` FROM `ml_pages_fields` WHERE `Name` = '{$sBlockName}' LIMIT 1"), $sValue );
					else
						$sPropValue = str_replace( '{Name}', $sBlockName, $sValue );

					$sCacheString .= "$sPrefix      '$sProp' => '$sPropValue',\n";
				}
			
			//process items
			$aItems = $this -> getItemsOfBlock($iBlockID);
			
			$sCacheString .= "$sPrefix      'Items' => array(\n";
			foreach ($aItems as $iBlockID => $aItem) {
				$sCacheString .= "$sPrefix        $iBlockID => array(\n"; //!pasd
				$sCacheString .= "$sPrefix          //item properties\n";
				
				// add additional properties
				if( is_array($this -> aAddProps) )
					foreach ($this -> aAddProps as $sProp => $sValue) {
						$aItem[ $sProp ] = str_replace( '{Name}', $aItem['Name'], $sValue );
					}
				foreach ($aItem as $sProp => $sValue) {
					if( $sProp == 'ID' )
						continue; //do not process ID it is already in key
					
					$sCacheString .= "$sPrefix          '$sProp' => " . $this -> processValue4CacheString( $sProp, $sValue, "$sPrefix          " ) . ",\n";
				}
				
				$sCacheString .= "$sPrefix        ),\n";
			}
			
			$sCacheString .= "$sPrefix      ),\n"; //close items
			$sCacheString .= "$sPrefix    ),\n"; //close block
		}
		
		return $sCacheString;
	}	
	function processValue4CacheString( $sProp, $sValue, $sPrefix = '' ) {
		if( is_null( $sValue ) )
			return 'null'; //just a null
		
		switch( $sProp ) {
			case 'Name':
			case 'Type':
			case 'Caption':
			case 'Desc':
			case 'MandatoryMsg':
			case 'MinMsg':
			case 'MaxMsg':
			case 'UniqueMsg':
			case 'CheckMsg':
			case 'IdField':
			case 'CaptionField':
			case 'DepField':
			case 'DepParentField':
			case 'MediaType':
			case 'Attribute':
			case 'Categories':
				return "'$sValue'"; // string in single quotes, simple text (without quotes)
			case 'Min':
			case 'Max':
			case 'MatchPercent':
			case 'MatchField':
				return "$sValue"; //integer
			case 'Mandatory':
			case 'WithTime':
			case 'WithPhoto':
			case 'Multiplyable':
			case 'AddOn':
			case 'CaptionDisable':
			case 'Unique':
				return ( $sValue == '1' ? 'true' : 'false' ); //boolean
			case 'Values':
				if( $sValue == '' )
					return "''";
				elseif( substr( $sValue, 0, 2) == '#!' )
					return '"' . $this -> addSlashesDblQuot( $sValue ) . '"'; //string in double quotes
				else {
					// WOOW! Lets make it array! >:-E
					$aValues = explode( "\n", $sValue );
					
					$sRet = "array(\n";
					
					foreach( $aValues as $iKey => $sValue1 ) {
						$sValue1 = $this -> addSlashes( $sValue1 );
						$sRet .= "$sPrefix  '$sValue1',\n";
					}
					
					$sRet .= "$sPrefix)";
					
					return $sRet;
				}
			default:
				return '"' . $this -> addSlashesDblQuot( $sValue ) . '"'; //string in double quotes
		}
	}
	
	function addSlashes( $sText ) {
		$sText = str_replace( "\\", "\\\\", $sText );
		$sText = str_replace( "'", "\\'", $sText );
		
		return $sText;
	}
	
	function addSlashesDblQuot( $sText ){
		$sText = str_replace( '\\',  '\\\\', $sText );
		$sText = str_replace( '"',   '\"',   $sText );
		$sText = str_replace( '$',   '\$',   $sText );
		$sText = str_replace( "\r",  '\r',   $sText );
		$sText = str_replace( "\n",  '\n',   $sText );
		$sText = str_replace( "\t",  '\t',   $sText );
		$sText = str_replace( "\x0", '\x0',  $sText );
		
		return $sText;
	}

	
	function getJoinPages() {
		$aPages = array();
		
		$sQuery = "
			SELECT
				DISTINCT `JoinPage`
			FROM `ml_pages_fields`
			WHERE
				`Type` = 'block' AND
				`JoinOrder` IS NOT NULL
				AND FIND_IN_SET ({$this->iCategory}, `Categories`)
		";
		
		$rPages = db_res( $sQuery );
		
		while( $aPage = mysql_fetch_assoc( $rPages ) ) {
			$aPages[] = (int)$aPage['JoinPage'];
		}
		
		return $aPages;
	}
	

	
	function getItemsOfBlock( $iBlockID ) {
		$aItems = array();
		
		$sAddSelect = '`' . str_replace( ',', '`, `', $this -> sAddSelect ) . '`';
		
		if( $this -> id == 100 )
			$sWhere = '1';
		elseif( $this -> id == 101 )
			$sWhere = "`MatchField` != ''";
		else
			$sWhere = "`{$this -> sBlockCol}` = $iBlockID AND `{$this -> sOrderCol}` IS NOT NULL";
		
		$sOrderCol = isset( $this -> sOrderCol ) ? $this -> sOrderCol : 'ID';
		//$sWhere
		$sQuery = "
			SELECT
				`ID`,
				`Name`,
				`Type`,
				`Categories`,
				$sAddSelect
			FROM
				`ml_pages_fields`
			WHERE
				`Type` != 'block' AND $sWhere
				AND FIND_IN_SET ({$this->iCategory}, `Categories`)
			ORDER BY
				`$sOrderCol`
		";
		$rItems = db_res( $sQuery );
		while( $aItem = mysql_fetch_assoc($rItems) ) {
			$aItems[ $aItem['ID'] ] = $aItem;
		}
		return $aItems;
	}
}

?>
