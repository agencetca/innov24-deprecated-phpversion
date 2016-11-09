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


class AqbDFTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function AqbDFTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);
	}
	function getDependencies($aDependentLists) {
		if (empty($aDependentLists)) return MsgBox(_t('_aqb_df_no_deps'));
		$sBaseUri = BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri();

		$sRet = '<table><tr>';
		foreach ($aDependentLists as $aList) {
			$sRet .= '<td valign="top">';
			$sRet .= '<div class="aqb_top_level_list"><a href="javascript:AqbDFEditTopList(\''.BX_DOL_URL_ADMIN.'\', \''.addslashes($aList[0]).'\')">'.$aList[0].'</a></div>';
			for ($i = 1; $i < count($aList); $i++) {
				$sRet .= '<div class="aqb_df_list"><a href="javascript:AqbDFEditDependentList(\''.$sBaseUri.'\', \''.addslashes($aList[$i]).'\', \''.addslashes($aList[$i-1]).'\')">'.$aList[$i].'</a></div>';
			}
			$sRet .= '</td>';
		}
		$sRet .= '</tr></table>';
		return $sRet;
	}
	function getNewListForm($aAvailableLists) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
		$aTaskForm = array(
	    	'form_attrs' => array(
                'id' => 'aqb_new_list',
                'name' => 'aqb_new_list',
                'action' => $sBaseUrl.'action_new',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
                'onsubmit' => 'javascript: AqbDFNewDependentListStart(this, \''.$sBaseUrl.'action_dependencies/\'); return false;'
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'save',
                ),
            ),
            'inputs' => array (
                'name' => array(
                	'type' => 'text',
                	'name' => 'Name',
                	'caption' => _t('_aqb_df_name'),
                	'required' => true,
                	'checker' => array (
                        'func' => 'length',
                        'params' => array(4,64),
                        'error' => _t ('_aqb_df_name_error'),
                    ),
                ),
                'parent' => array(
	                'type' => 'select',
	                'name' => 'Parent',
	                'caption' => _t('_aqb_df_parent_list'),
	                'values' => $aAvailableLists,
	                'required' => true,
	                'checker' => array (
	                    'func' => 'avail',
	                    'error' => 'Doesn\'t exists',
	                ),
				),
                'submit' => array(
					'type' => 'submit',
					'name' => 'save',
					'value' => _t('_aqb_df_proceed')
                )
			)
		);

		$oForm = new BxTemplFormView($aTaskForm);

		$oForm->initChecker($_REQUEST);

		return $oForm;
	}
	function getListEditForm($sListName, $sParent, $sParentVal, $sMessage = '') {
		$sDeleteIcon = $GLOBALS['oAdmTemplate']->getImageUrl('minus1.gif');
		$sUpIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_up.gif');
		$sDownIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_down.gif');

		$aResult = array(
			'page_header' => _t('_aqb_df_dependent_list'),
			'admin_url' => BX_DOL_URL_ADMIN,
			'page_main_code' => $this->getValuesEditForm($sListName, $sParent, $sParentVal),
			'delete_icon' => $sDeleteIcon,
			'up_icon' => $sUpIcon,
			'down_icon' => $sDownIcon,
			'message' => !empty($sMessage) ? MsgBox($sMessage) : '',
			'home_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri().'action_dependencies/'
		);
		return $this->parseHtmlByName('table_edit.html', $aResult);
	}



	function getValuesEditForm($sListName, $sParent, $sParentVal) {
		$aParentValues = $this->_oDb->getAllWithKey("SELECT `Value`, `LKey` FROM `sys_pre_values` WHERE `Key` = '".addslashes($sParent)."' ORDER BY `Order`", 'Value');
		if (empty($sParentVal)) {
			$aKeys = array_keys($aParentValues);
			$sParentVal = $aKeys[0];
		}

		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri().'action_edit/'.urlencode($sListName).'/'.urlencode($sParent).'/';

	    ob_start();
	    if ($sResultMsg)
	        echo MsgBox($sResultMsg);
		?>
		<form action="<?php echo $sBaseUrl.urlencode($sParentVal).'/' ?>" method="post">
			<table id="listEdit" cellpadding="0" cellspacing="0">
				<tr>
					<th colspan="3">
						The list <font style="color: red;"><?php echo $sListName?></font> depends on the list <font style="color: red;"><?php echo $sParent?></font><br />
						The following values of <font style="color: red;"><?php echo $sListName?></font> list will appear when
						<select name="ParentVal" onchange="window.location.href = '<?php echo $sBaseUrl?>'+this.value+'/';">
							<?php
							foreach ($aParentValues as $sValue => $sLKey) {
								$sSelected = $sValue == $sParentVal ? 'selected="selected"' : '';
								?>
									<option value="<?php echo htmlspecialchars( $sValue ) ?>" <?php echo $sSelected?>><?php echo _t($sLKey['LKey']) ?></option>
								<?php
							}
							?>
						</select> is selected
					</th>
				</tr>
		<?php
		$iNextInd = $this->genListRows( $sListName, $sParent, $sParentVal );
		?>
				<tr>
					<th colspan="3">
						<input type="submit" name="action" value="Save" />
					</th>
				</tr>
			</table>
			<script type="text/javascript">
				iNextInd = <?php echo $iNextInd ?>;
			</script>
		</form>
		<?php
		return $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => ob_get_clean()));
	}

	function genListRows( $sList, $sParent, $sParentVal ) {
		$sDeleteIcon = $GLOBALS['oAdmTemplate']->getImageUrl('minus1.gif');
		$sUpIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_up.gif');
		$sDownIcon = $GLOBALS['oAdmTemplate']->getImageUrl('arrow_down.gif');

		$sQuery = "SELECT * FROM `sys_pre_values` WHERE `Key` = '{$sList}' AND `Extra` = '{$sParent}' AND `Extra2` = '{$sParentVal}' ORDER BY `Order`";
		$rList = $this->_oDb->res( $sQuery );
		?>
			<tr class="headers">
				<th>
					<span class="tableLabel" onmouseover="showFloatDesc( 'The value stored in the database<br />Must be unique across the whole list.' );" onmousemove="moveFloatDesc( event );" onmouseout="hideFloatDesc();">
						Value
					</span>
				</th>
				<th>
					<span class="tableLabel" onmouseover="showFloatDesc( 'Primary language key used for displaying' );" onmousemove="moveFloatDesc( event );" onmouseout="hideFloatDesc();">
						LKey
					</span>
				</th>
				<th>&nbsp;</th>
			</tr>
		<?php
		$iCounter = 0;
		while( $aRow = mysql_fetch_assoc( $rList ) ) {
			?>
			<tr>
				<td><input type="text" class="value_input" name="PreList[<?php echo $iCounter ?>][Value]" value="<?php echo htmlspecialchars( $aRow['Value'] ) ?>" /></td>
				<td><input type="text" class="value_input" name="PreList[<?php echo $iCounter ?>][LKey]" value="<?php echo htmlspecialchars( $aRow['LKey'] ) ?>" /></td>
				<td>
					<img src="<?php echo $sDeleteIcon?>" class="row_control" title="Delete" 	alt="Delete" onclick="delRow( this );" />
					<img src="<?php echo $sUpIcon ?>"   class="row_control" title="Move up"   alt="Move up" onclick="moveUpRow( this );" />
					<img src="<?php echo $sDownIcon ?>" class="row_control" title="Move down" alt="Move down" onclick="moveDownRow( this );" />
				</td>
			</tr>
			<?php
			$iCounter ++;
		}
		?>
			<tr class="headers">
				<td colspan="2">&nbsp;</td>
				<th>
	                <img src="<?php echo $GLOBALS['oAdmTemplate']->getImageUrl('plus1.gif') ?>" class="row_control" title="Add" alt="Add" onclick="addRow( this );" />
				</th>
			</tr>
		<?php

		return $iCounter;
	}
}
?>