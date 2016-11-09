<?
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
require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");
require_once(BX_DIRECTORY_PATH_INC . "prof.inc.php");

class AqbPTSInstaller extends BxDolInstaller {
    function AqbPTSInstaller($aConfig) {
        parent::BxDolInstaller($aConfig);
    }
    function install($aParams) {
    	$aResult = parent::install($aParams);

    	if ($aResult['result']) {
		    if($rHandler = opendir(BX_DIRECTORY_PATH_CACHE)) {
		        while(($sFile = readdir($rHandler)) !== false)
		            if(substr($sFile, 0, strlen('user')) == 'user')
		                @unlink(BX_DIRECTORY_PATH_CACHE . $sFile);
		        closedir($rHandler);
		    }
		    $this->_compilePreValues();
    	}
    	return $aResult;
    }
    function uninstall($aParams) {
    	$aResult = parent::uninstall($aParams);
    	if ($aResult['result']) {
    		$oModuleDb = new BxDolModuleDb();
    		$sCode = addslashes("if (BxDolRequest::serviceExists('aqb_pts', 'get_members_block')) return BxDolService::call('aqb_pts', 'get_members_block'%");
			$sQuery = "DELETE FROM `sys_page_compose` WHERE `Content` LIKE '{$sCode}'";
			$oModuleDb->query($sQuery);
			@unlink(BX_DIRECTORY_PATH_CACHE . 'sys_page_compose.inc');
    	}
    	return $aResult;
    }
    function _compilePreValues() {
		if (function_exists('compilePreValues')) return compilePreValues();

		$sQuery = "SELECT DISTINCT `Key` FROM `sys_pre_values`";
		$rKeys = db_res( $sQuery );

		$rProf = @fopen( BX_DIRECTORY_PATH_INC . 'prof.inc.php', 'w' );
		if( !$rProf ) {
			echo '<b>Warning!</b> Couldn\'t compile prof.inc.php. Please check permissions.';
			return false;
		}

		fwrite( $rProf, "<?\n\$aPreValues = array(\n" );

		while( $aKey = mysql_fetch_assoc( $rKeys ) ) {
			$sKey    = $aKey['Key'];
			$sKey_db = addslashes( $sKey );

			fwrite( $rProf, "  '$sKey_db' => array(\n" );

			$sQuery = "SELECT * FROM `sys_pre_values` WHERE `Key` = '$sKey_db' ORDER BY `Order`";
			$rRows  = db_res( $sQuery );

			while( $aRow = mysql_fetch_assoc( $rRows ) ) {
				$sValue_db = addslashes( $aRow['Value'] );
				fwrite( $rProf, "    '{$sValue_db}' => array( " );

				foreach( $aRow as $sValKey => $sValue ) {
					if( $sValKey == 'Key' or $sValKey == 'Value' or $sValKey == 'Order' )
						continue; //skip key, value and order. they already used

					if( !strlen( $sValue ) )
						continue; //skip empty values

					fwrite( $rProf, "'$sValKey' => '" . addslashes( $sValue ) . "', " );
				}

				fwrite( $rProf, "),\n" );
			}

			fwrite( $rProf, "  ),\n" );
		}

		fwrite( $rProf, ");\n" );
		fwrite( $rProf, '
$aPreValues[\'Country\'] = sortArrByLang( $aPreValues[\'Country\'] );

function sortArrByLang( $aArr ) {
	if( !function_exists( \'_t\' ) )
		return $aArr;

	$aSortArr = array();
	foreach( $aArr as $sKey => $aValue )
		$aSortArr[$sKey] = _t( $aValue[\'LKey\'] );

	asort( $aSortArr );

	$aNewArr = array();
	foreach( $aSortArr as $sKey => $sVal )
		$aNewArr[$sKey] = $aArr[$sKey];

	return $aNewArr;
}
		' );

		fclose( $rProf );

		return true;
	}
}
?>