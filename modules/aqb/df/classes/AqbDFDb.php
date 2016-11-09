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

bx_import('BxDolModuleDb');

class AqbDFDb extends BxDolModuleDb {
	/*
	 * Constructor.
	 */
	function AqbDFDb(&$oConfig) {
		parent::BxDolModuleDb($oConfig);

		$this->_oConfig = &$oConfig;
	}
	function getDependentLists() {
		$aResult = array();
		$aRes = $this->getAll("SELECT DISTINCT `Key` FROM `sys_pre_values` WHERE `Extra` <> ''");
		foreach ($aRes as $aList) {
			$aForward = array();
			$aLink = $this->getParentList($aList['Key']);
			while ($aLink['parent']) {
				$aForward[] = $aLink['name'];
				$aLink = $aLink['parent'];
			}
			$aForward[] = $aLink['name'];

			$aResult[] = array_reverse($aForward);
		}

		//experimental
		//on field delete
		$aDepsFullStrings = array();
		foreach ($aResult as $i => $aDep) {
			$sLink = implode($aDep);
			$aDepsFullStrings[$sLink] = $i;
		}
		foreach ($aDepsFullStrings as $sFullPath => $iDep) {
			foreach ($aDepsFullStrings as $sFullPath2 => $iDep2) {
				if ($sFullPath != $sFullPath2 && strpos($sFullPath2, $sFullPath) === 0) {
					unset($aResult[$iDep]);
				}
			}
		}
		//experimental

		return $aResult;
	}
	function getParentList($sList) {
		$sList = addslashes($sList);
		$sParent = $this->getOne("SELECT `Extra` FROM `sys_pre_values` WHERE `Key` = '{$sList}' LIMIT 1");
		if (empty($sParent)) return array('name' => $sList, 'parent' => false);
		else return array('name' => $sList, 'parent' => $this->getParentList($sParent));
	}
	function getAvailableLists() {
		$aRes = $this->getAll('SELECT DISTINCT `Key` FROM `sys_pre_values` ORDER BY `Key`');
		$aResult = array();
		foreach ($aRes as $aItem) $aResult[$aItem['Key']] = $aItem['Key'];
		return $aResult;
	}

	function getPossibleFieldsForDependentField($sCurrentFieldName) {
		$aRes = $this->getAll("SELECT `ID`, `Name`, `Values` FROM `sys_profile_fields` WHERE `Type` = 'select_one' AND `Control` = 'select' AND `Values` LIKE '#!%' AND `Name`<>'".process_db_input($sCurrentFieldName)."' ORDER BY `Name`");
		$aRet = array();
		foreach ($aRes as $aField) {
			$sList = substr($aField['Values'], 2);
			$bHasChildList = $this->getOne("SELECT `Key` FROM `sys_pre_values` WHERE `Extra` = '{$sList}' LIMIT 1");
			if ($bHasChildList) $aRet[$aField['ID']] = htmlspecialchars($aField['Name']);
		}
		return $aRet;
	}
	function getPossibleListsForDependentField() {
		$aRes = $this->getAll("SELECT DISTINCT `Key`, `Extra` FROM `sys_pre_values` WHERE `Extra` <> ''");
		$aRet = array();
		foreach ($aRes as $aList) {
			$aFields = $this->getAll("SELECT `ID` FROM `sys_profile_fields` WHERE `Type` = 'select_one' AND `Control` = 'select' AND `Values` = '#!".addslashes($aList['Extra'])."'");
			foreach ($aFields as $aField) {
				$sKey = addslashes($aList['Key']);
				while (isset($aRet[$sKey])) $sKey .= ' ';
				$aRet[$sKey] = array('LKey' => htmlspecialchars($aList['Key']), 'Extra2' => $aField['ID']);
			}
		}
		return $aRet;
	}
	function checkDependency($sListName, $sParentFieldID) {
		$sParentFieldID = intval($sParentFieldID);
		$sValues = $this->getOne("SELECT `Values` FROM `sys_profile_fields` WHERE `Type` = 'select_one' AND `Control` = 'select' AND `Values` LIKE '#!%' AND `ID` = {$sParentFieldID} LIMIT 1");
		if (!$sValues) return;
		$sValues = addslashes(substr($sValues, 2));
		$sListName = process_db_input($sListName);
		return $this->getOne("SELECT COUNT(*) FROM `sys_pre_values` WHERE `Key` = '{$sListName}' AND `Extra` = '{$sValues}' LIMIT 1");
	}
	function cleanDependency($iField) {
		$this->query("DELETE FROM `aqb_df_dependencies` WHERE `Field` = {$iField} LIMIT 1");
	}
	function updateDependency($iField, $iDependsOn, $bUseAjax) {
		$bUseAjax = $bUseAjax ? 1 : 0;
		$this->query("REPLACE `aqb_df_dependencies` SET `Field` = {$iField}, `DependsOn` = {$iDependsOn}, `UseAjax` = {$bUseAjax}");
	}
	function getFieldPropertiesByName($sField, $bCustom = false) {
		if (strpos($sField, '[') !== false) $sField = preg_replace("/\[.*\]/", "", $sField);
		$sField = process_db_input($sField);

		if (!$bCustom) {
			$aField = $this->getRow("SELECT `ID`, `Values` FROM `sys_profile_fields` WHERE `Name` = '{$sField}' LIMIT 1");
			if (!$aField['ID']) return false;
			$sValues = substr($aField['Values'], 2);

			$aDependency = $this->getRow("SELECT * FROM `aqb_df_dependencies` WHERE `Field` = {$aField['ID']} LIMIT 1");

			return array('values' => $sValues, 'ajax' => $aDependency['UseAjax']);
		} else {
			$aDependency = $this->getRow("SELECT * FROM `aqb_df_custom_dependencies` WHERE `Field` = '{$sField}' LIMIT 1");
			return array('values' => $aDependency['ValuesList'], 'ajax' => $aDependency['UseAjax']);
		}
	}
	function getFieldsForSettingDependency() {
		return $this->getAll("
			SELECT `deps`.`Name`, `deps`.`Values`, `sys_profile_fields`.`Name` as `DependsOn`
			FROM (
				SELECT `Name`, `Values`, `DependsOn`
				FROM `sys_profile_fields` JOIN `aqb_df_dependencies`
				ON `sys_profile_fields`.`ID` = `aqb_df_dependencies`.`Field`
			) as `deps` JOIN `sys_profile_fields`
			ON `sys_profile_fields`.`ID` = `deps`.`DependsOn`
			ORDER BY `ID`");
	}
}
?>