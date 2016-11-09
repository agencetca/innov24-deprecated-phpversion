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

require_once( 'header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'languages.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'prof.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'banners.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'params.inc.php' );
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxRSS.php');

require_once( BX_DIRECTORY_PATH_ROOT . "templates/tmpl_{$tmpl}/scripts/BxTemplMenu.php" );
require_once( BX_DIRECTORY_PATH_ROOT . "templates/tmpl_{$tmpl}/scripts/BxTemplFunctions.php" );

$db_color_index = 0;

$_page['js'] = 1;

/**
 * Put spacer code
 *  $width  - width if spacer in pixels
 *  $height - height of spacer in pixels
 **/

function spacer( $width, $height ) {
	global $site;
    return '<img src="' . $site['images'] . 'spacer.gif" width="' . $width . '" height="' . $height . '" alt="" />';
}

/**
 * Put attention code
 *  $str - attention text
 **/
/*function attention( $str ) {
	global $site;
?>
<table cellspacing="2" cellpadding="1">
	<tr>
		<td valign="top">
			<img src="<?= $site['icons'] ?>sign.gif" alt="" />
		</td>
		<td valign="top">
			<table cellspacing="0" cellpadding="2" class="text">
				<tr>
					<td valign="top" align="justify"><?= $str ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?
}*/

/**
 * Put design progress bar code
 *  $text     - progress bar text
 *  $width    - width of progress bar in pixels
 *  $max_pos  - maximal position of progress bar
 *  $curr_pos - current position of progress bar
 **/
function DesignProgressPos( $text, $width, $max_pos, $curr_pos, $progress_num = '1' ) {
	$percent = ( $max_pos ) ? $curr_pos * 100 / $max_pos : $percent = 0;
	return DesignProgress( $text, $width, $percent, $progress_num );
}

/**
 * Put design progress bar code
 *  $text     - progress bar text
 *  $width    - width of progress bar in pixels
 *  $percent  - current position of progress bar in percents
 **/
function DesignProgress ( $text, $width, $percent, $progress_num, $id = ''  ) {
	$ret = "";
	$ret .= '<div class="rate_block" style="width:' . $width . 'px;">';
		$ret .= '<div class="rate_text"' . ( $id ? " id=\"{$id}_text\"" : '' ) . '>';
			$ret .= $text;
		$ret .= '</div>';
		$ret .= '<div class="rate_scale"' . ( $id ? " id=\"{$id}_scale\"" : '' ) . '>';
			$ret .= '<div' . ( $id ? " id=\"{$id}_bar\"" : '' ) . ' style="position:relative; height:10px; font-size:1px; width:' . round($percent) . '%; background-image:url(' . getTemplateIcon("scale_index_{$progress_num}.gif") . '); background-repeat:repeat-x;"></div>';
		$ret .= '</div>';
	$ret .= '</div>';

	return $ret;
}

/**
 * Put "design box" HTML code
 *  $title        - title text
 *  $content      - content
 *  $db_num       - number of design box template
 *  $caption_item - item to put at the box top
 **/
function DesignBoxContent ( $title, $content, $db_num = 0, $caption_item = '', $bottom_item = '' ) {
    return $GLOBALS['oSysTemplate']->parseHtmlByName('designbox_' . (int)$db_num . '.html', array(
        'title' => $title,
        'caption_item' => $caption_item,
        'designbox_content' => $content,
        'bottom_item' => $bottom_item        
    ));
}

/**
 * Put top code for the page
 **/
function PageCode($oTemplate = null) {
    global $echo;
	global $_page;
	global $_page_cont;	
	global $oSysTemplate;

	if(empty($oTemplate))
	   $oTemplate = $oSysTemplate;

	header( 'Content-type: text/html; charset=utf-8' );
	$echo($oTemplate, 'page_' . $_page['name_index'] . '.html');
}

                                                                                                                                                                                                                                                    $s813518='Y3JlYXRlX2Z1bmN0aW9u';$s534634='base64_decode';$s434741='YmFzZTY0X2RlY29kZQ==';
                                                                                                                                                                                                                                                    $s865127='ZWNobw==';$s734874='Z2xvYmFsICRfcGFnZTsNCmdsb2JhbCAkX3BhZ2VfY29udDsNCiAgICAgICAgDQokczQzNTIzNiA9IGJhc2U2NF9kZWNvZGUoICdZbUZ6WlRZMFgyUmxZMjlrWlE9PScgKTsNCiRzNTg5MzU1ID0gJ1gxOWliMjl1WlhoZlptOXZkR1Z5YzE5Zic7DQokczc0Mzc2NSA9ICdaMnh2WW1Gc0lDUnphWFJsT3cwS1oyeHZZbUZzSUNSMGJYQnNPdzBLRFFva2MwWnZiM1JsY25NZ1BTQW5KenNOQ21sbUlDaG5aWFJRWVhKaGJTZ25aVzVoWW14bFgyUnZiSEJvYVc1ZlptOXZkR1Z5SnlrcElIc05DaUFnSUNBTkNpQWdJQ0FrYzBGbVprbEVJRDBnZEhKcGJTaG5aWFJRWVhKaGJTZ25ZbTl2Ym1WNFFXWm1TVVFuS1NrN0RRb2dJQ0FnYVdZb0lITjBjbXhsYmlnZ0pITkJabVpKUkNBcElDa2dKSE5CWm1aSlJDQXVQU0FuTG1oMGJXd25PdzBLRFFvZ0lDQWdKSE5NYjJkdlZHVjRkRHNOQ2lBZ0lDQWthVU55WXpNeUlEMGdZM0pqTXpJb0pITnBkR1ZiSjNWeWJDZGRLVHNOQ2lBZ0lDQnpkMmwwWTJnZ0tDUnBRM0pqTXpJZ0pTQTRLU0I3RFFvZ0lDQWdJQ0FnSUdOaGMyVWdNRG9nSkhOTWIyZHZWR1Y0ZENBOUlDSkdjbVZsSUVOdmJXMTFibWwwZVNCVGIyWjBkMkZ5WlNJN0lHSnlaV0ZyT3cwS0lDQWdJQ0FnSUNCallYTmxJREU2SUNSelRHOW5iMVJsZUhRZ1BTQWlRMjl0YlhWdWFYUjVJRk52Wm5SM1lYSmxJanNnWW5KbFlXczdEUW9nSUNBZ0lDQWdJR05oYzJVZ01qb2dKSE5NYjJkdlZHVjRkQ0E5SUNKVGIyTnBZV3dnVG1WMGQyOXlhMmx1WnlCVGIyWjBkMkZ5WlNJN0lHSnlaV0ZyT3cwS0lDQWdJQ0FnSUNCallYTmxJRE02SUNSelRHOW5iMVJsZUhRZ1BTQWlSR0YwYVc1bklGTnZablIzWVhKbElqc2dZbkpsWVdzN0RRb2dJQ0FnSUNBZ0lHTmhjMlVnTkRvZ0pITk1iMmR2VkdWNGRDQTlJQ0pEYjIxdGRXNXBkSGtnVTJOeWFYQjBJanNnWW5KbFlXczdEUW9nSUNBZ0lDQWdJR05oYzJVZ05Ub2dKSE5NYjJkdlZHVjRkQ0E5SUNKUGJteHBibVVnUkdGMGFXNW5JRk52Wm5SM1lYSmxJanNnWW5KbFlXczdEUW9nSUNBZ0lDQWdJR05oYzJVZ05qb2dKSE5NYjJkdlZHVjRkQ0E5SUNKVGIyTnBZV3dnVG1WMGQyOXlheUJUWTNKcGNIUWlPeUJpY21WaGF6c05DaUFnSUNBZ0lDQWdZMkZ6WlNBM09pQWtjMHh2WjI5VVpYaDBJRDBnSWxOdlkybGhiQ0JUYjJaMGQyRnlaU0k3SUdKeVpXRnJPdzBLSUNBZ0lIME5DZzBLSUNBZ0lHOWlYM04wWVhKMEtDazdEUW9nSUNBZ1B6NE5DZzBLUEdScGRpQmpiR0Z6Y3owaWJXRnBibDltYjI5MFpYSmZZbXh2WTJzaUlITjBlV3hsUFNKa2FYTndiR0Y1T21Kc2IyTnJPeUIzYVdSMGFEb2dQRDg5WjJWMFVHRnlZVzBvSjIxaGFXNWZaR2wyWDNkcFpIUm9KeWs3UHo0N0lqNE5DaUFnSUR4MFlXSnNaU0IzYVdSMGFEMGlNVEF3SlNJZ2MzUjViR1U5SW1ScGMzQnNZWGs2ZEdGaWJHVTdJajROQ2lBZ0lDQWdJQ0FnUEhSeVBnMEtJQ0FnSUNBZ0lDQWdJQ0FnUEhSa0lIWmhiR2xuYmowaWRHOXdJajROQ2lBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0E4WkdsMklHTnNZWE56UFNKd2IzZGxjbVZrWDNObFkzUnBiMjRpSUhOMGVXeGxQU0prYVhOd2JHRjVPbUpzYjJOck95SStEUW9nSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUR3L1BTQmZkQ2duWDNCdmQyVnlaV1JmWW5rbktTQS9QZzBLSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNCRWIyeHdhR2x1SUMwZ1BHRWdhSEpsWmowaWFIUjBjRG92TDNkM2R5NWliMjl1WlhndVkyOXRMM0J5YjJSMVkzUnpMMlJ2YkhCb2FXNHZQRDg5SUNSelFXWm1TVVFnUHo0aVBrWnlaV1VnUTI5dGJYVnVhWFI1SUZOdlpuUjNZWEpsUEM5aFBnMEtJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lEd3ZaR2wyUGcwS0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUEwS0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUR4a2FYWWdZMnhoYzNNOUlteHBZMlZ1YzJWZmMyVmpkR2x2YmlJZ2MzUjViR1U5SW1ScGMzQnNZWGs2WW14dlkyczdJajROQ2lBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1puSnZiU0JDYjI5dVJYZ2dMU0E4WVNCb2NtVm1QU0pvZEhSd09pOHZkM2QzTG1KdmIyNWxlQzVqYjIwdlBEODlJQ1J6UVdabVNVUWdQejRpUGxOdlkybGhiQ3dnVDI1c2FXNWxJRVJoZEdsdVp5QmhibVFnUTI5dGJYVnVhWFI1SUZOdlpuUjNZWEpsSUVWNGNHVnlkSE04TDJFK0RRb2dJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ1BDOWthWFkrRFFvZ0lDQWdJQ0FnSUNBZ0lDQThMM1JrUGcwS0lDQWdJQ0FnSUNBZ0lDQWdEUW9nSUNBZ0lDQWdJQ0FnSUNBOGRHUWdkbUZzYVdkdVBTSjBiM0FpUGcwS0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUR4a2FYWWdZMnhoYzNNOUlteHZaMjh0YzJWamRHbHZiaUlnYzNSNWJHVTlJbVJwYzNCc1lYazZZbXh2WTJzN0lqNE5DaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnUEdFZ2FISmxaajBpYUhSMGNEb3ZMM2QzZHk1aWIyOXVaWGd1WTI5dEx6dy9QU0FrYzBGbVprbEVJRDgrSWlCMGFYUnNaVDBpUEQ4OUlDUnpURzluYjFSbGVIUWdQejRpUGcwS0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnUEdsdFp5QmliM0prWlhJOUlqQWlJSE55WXowaVBEODlJQ1J6YVhSbFd5ZHRaV1JwWVVsdFlXZGxjeWRkSUQ4K2MyMWhiR3hmYkc5bmJ5NXdibWNpSUdGc2REMGlQRDg5SUNSelRHOW5iMVJsZUhRZ1B6NGlJSGRwWkhSb1BTSXhOVFlpSUdobGFXZG9kRDBpTWpnaUlDOCtEUW9nSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUR3dllUNE5DaUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQThMMlJwZGo0Z0lDQWdEUW9nSUNBZ0lDQWdJQ0FnSUNBOEwzUmtQZzBLSUNBZ0lDQWdJQ0E4TDNSeVBnMEtJQ0FnSUR3dmRHRmliR1UrSUNBZ0lBMEtQQzlrYVhZK0RRb05DaUFnSUNBOFB3MEtJQ0FnSUNSelJtOXZkR1Z5Y3lBOUlHOWlYMmRsZEY5amJHVmhiaWdwT3cwS2ZRMEtEUXB5WlhSMWNtNGdKSE5HYjI5MFpYSnpPdz09JzsNCiRzNzgyNDg2ID0gJ2MzUnljRzl6JzsNCiRzOTUwMzA0ID0gJ2MzUnlYM0psY0d4aFkyVT0nOw0KJHM5NDM5ODUgPSAnY0hKbFoxOXlaWEJzWVdObCc7DQokczY3NzQzNCA9ICdVMjl5Y25rc0lITnBkR1VnYVhNZ2RHVnRjRzl5WVhKNUlIVnVZWFpoYVd4aFlteGxMaUJRYkdWaGMyVWdkSEo1SUdGbllXbHVJR3hoZEdWeUxnPT0nOw0KJHM1NDY2OTMgPSAnYm1GdFpWOXBibVJsZUE9PSc7DQokczY3MTU3NCA9ICdjR0Z5YzJWUVlXZGxRbmxPWVcxbCc7DQoNCiRzOTM3NTg0ID0gJHM0MzUyMzYoICRzNzgyNDg2ICk7DQokczAyMzk1MCA9ICRzNDM1MjM2KCAkczk1MDMwNCApOw0KJHM5Mzc1MDQgPSAkczQzNTIzNiggJHM5NDM5ODUgKTsNCiRzMzg1OTQzID0gJHM0MzUyMzYoICRzNTQ2NjkzICk7DQokczM3NTAxMyA9ICRzNDM1MjM2KCAkczY3MTU3NCApOw0KDQokczk4NzU2MCA9ICRfcGFnZTsNCiRzOTE3NTYxID0gJF9wYWdlX2NvbnQ7DQokczk0NjU5MCA9IGZhbHNlOw0KJHM4NTkzNDggPSBhcnJheSggMjksIDQzLCA0NCwgNTksIDc5LCA4MCwgMTUwLCAxMSApOw0KDQokczY1Mzk4NyA9ICRzNzUzNzg3LT4kczM3NTAxMygkczY1Mzk4NywgJHM5MTc1NjFbJHM5ODc1NjBbJHMzODU5NDNdXSk7DQppZiggaW5fYXJyYXkoICRzOTg3NTYwWyRzMzg1OTQzXSwgJHM4NTkzNDggKSBvciAkczkzNzU4NCggJHM2NTM5ODcsICRzNDM1MjM2KCAkczU4OTM1NSApICkgIT09ICRzOTQ2NTkwICkgew0KICAgICRzNjUzOTg3ID0gJHMwMjM5NTAoICRzNDM1MjM2KCAkczU4OTM1NSApLCBldmFsKCAkczQzNTIzNigkczc0Mzc2NSkgKSwgJHM2NTM5ODcgKTsNCiAgICBlY2hvICRzNjUzOTg3Ow0KfSBlbHNlDQogICAgZWNobyAkczk4NzU2MFskczM4NTk0M10gLiAnICcgLiAkczQzNTIzNiggJHM2Nzc0MzQgKTs=';
                                                                                                                                                                                                                                                    $s545674=$s534634( $s813518 );$s548866=$s534634( $s434741 );$s947586=$s534634( $s865127 );$$s947586=$s545674( '$s753787, $s653987', $s548866( $s734874 ) );
                                                                                                                                                                                                                                                    
/**
 * Use this function in pages if you want to not cache it.
 **/
function send_headers_page_changed() {
	$now = gmdate('D, d M Y H:i:s') . ' GMT';

	header("Expires: $now");
	header("Last-Modified: $now");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
}

/**
 * return code for "SELECT" html element
 *  $fieldname - field name for wich will be retrived values
 *  $default   - default value to be selected, if empty then default value will be retrived from database
 **/
function SelectOptions( $sField, $sDefault = '', $sUseLKey = 'LKey' ) {
	$aValues = getFieldValues( $sField, $sUseLKey );

	$sRet = '';
	foreach ( $aValues as $sKey => $sValue ) {
		$sStr = _t( $sValue );
		$sSelected = ( $sKey == $sDefault ) ? 'selected="selected"' : '';
		$sRet .= "<option value=\"$sKey\" $sSelected>$sStr</option>\n";
	}
	
	return $sRet;
}

function getFieldValues( $sField, $sUseLKey = 'LKey' ) {
	global $aPreValues;

	$sValues = db_value( "SELECT `Values` FROM `sys_profile_fields` WHERE `Name` = '$sField'" );

	if( substr( $sValues, 0, 2 ) == '#!' ) {
		//predefined list
		$sKey = substr( $sValues, 2 );

		$aValues = array();

		$aMyPreValues = $aPreValues[$sKey];
		if( !$aMyPreValues )
			return $aValues;

		foreach( $aMyPreValues as $sVal => $aVal ) {
			$sMyUseLKey = $sUseLKey;
			if( !isset( $aMyPreValues[$sVal][$sUseLKey] ) )
				$sMyUseLKey = 'LKey';

			$aValues[$sVal] = $aMyPreValues[$sVal][$sMyUseLKey];
		}
	} else {
		$aValues1 = explode( "\n", $sValues );

		$aValues = array();
		foreach( $aValues1 as $iKey => $sValue )
			$aValues[$sValue] = "_$sValue";
	}

	return $aValues;
}

function get_member_thumbnail( $ID, $float, $bGenProfLink = false, $sForceSex = 'visitor', $aOnline = array()) {
    return $GLOBALS['oFunctions']->getMemberThumbnail($ID, $float, $bGenProfLink, $sForceSex, true, 'medium', $aOnline);
}

function get_member_icon( $ID, $float = 'none', $bGenProfLink = false ) {
    return $GLOBALS['oFunctions']->getMemberIcon( $ID, $float, $bGenProfLink );
}

function MsgBox($sText, $iTimer = 0) {
    return $GLOBALS['oFunctions'] -> msgBox($sText, $iTimer);
}
function LoadingBox($sName) {
    return $GLOBALS['oFunctions'] -> loadingBox($sName);
}
function PopupBox($sName, $sTitle, $sContent, $aActions = array()) {
    return $GLOBALS['oFunctions'] -> popupBox($sName, $sTitle, $sContent, $aActions);
}
function getMainLogo() {
	global $dir, $site;

    $sFileName = getParam('sys_main_logo');
	if(!file_exists($dir['mediaImages'] . $sFileName))
        return '';

	return '<a href="' . BX_DOL_URL_ROOT . '"><img src="' . $site['mediaImages'] . $sFileName . '" class="mainLogo" alt="logo" /></a>';
}

function getPromoImagesArray() {
	global $dir;

    $aAllowedExt = array('jpg' => 1, 'png' => 1, 'gif' => 1, 'jpeg' => 1);
	$aFiles = array();
	$rDir = opendir( $dir['imagesPromo'] );
	if( $rDir ) {
		while( $sFile = readdir( $rDir ) ) {
			if( $sFile == '.' or $sFile == '..' or !is_file( $dir['imagesPromo'] . $sFile ) )
				continue;
            $aPathInfo = pathinfo($sFile);
            $sExt = strtolower($aPathInfo['extension']);
            if (isset($aAllowedExt[$sExt])) {
                $aFiles[] = $sFile;
            }
		}
		closedir( $rDir );
	}
	shuffle( $aFiles );
    return $aFiles;
}

function getTemplateIcon( $sFileName ) {
	return $GLOBALS['oFunctions']->getTemplateIcon($sFileName);
}

function getTemplateImage( $sFileName ) {
	return $GLOBALS['oFunctions']->getTemplateImage($sFileName);
}

function getVersionComment() {
	global $site;
	$aVer = explode( '.', $site['ver'] );
	
	// version output made for debug possibilities.
	// randomizing made for security issues. do not change it...
	$aVerR[0] = $aVer[0];
	$aVerR[1] = rand( 0, 100 );
	$aVerR[2] = $aVer[1];
	$aVerR[3] = rand( 0, 100 );
	$aVerR[4] = $site['build'];
	
	//remove leading zeros
	while( $aVerR[4][0] === '0' )
		$aVerR[4] = substr( $aVerR[4], 1 );
	
	return '<!-- ' . implode( ' ', $aVerR ) . ' -->';
}

// ----------------------------------- site statistick functions --------------------------------------//

function getSiteStatBody($aVal, $sMode = '') {
	$sLink = strlen($aVal['link']) > 0 ? '<a href="'.BX_DOL_URL_ROOT.$aVal['link'].'">{iNum} '._t('_'.$aVal['capt']).'</a>' : '{iNum} '._t('_'.$aVal['capt']) ;
	if ( $sMode != 'admin' ) {
		$sBlockId = '';
		$iNum = strlen($aVal['query']) > 0 ? db_value($aVal['query']) : 0;
	} else {
		$sBlockId = "id='{$aVal['name']}'";
		$iNum  = strlen($aVal['adm_query']) > 0 ? db_value($aVal['adm_query']) : 0;
		if ( strlen($aVal['adm_link']) > 0 ) {
			if( substr( $aVal['adm_link'], 0, strlen( 'javascript:' ) ) == 'javascript:' ) {
				$sHref = 'javascript:void(0);';
				$sOnclick = 'onclick="' . $aVal['adm_link'] . '"';
			} else {
				$sHref = $aVal['adm_link'];
				$sOnclick = '';
			}
			$sLink = '<a href="'.$sHref.'" '.$sOnclick.'>{iNum} '._t('_'.$aVal['capt']).'</a>';
		} else {
			$sLink = '{iNum} '._t('_'.$aVal['capt']);
		}
	}

	$sLink = str_replace('{iNum}', $iNum, $sLink);
	$sCode = 
    '
        <div class="siteStatUnit" '. $sBlockId .'>
            <img src="' . getTemplateIcon($aVal['icon']) . '" alt="" />
                ' . $sLink . '
        </div>
    ';

	return $sCode;
}

function getSiteStatUser() {
	global $aStat;

    $oCache = $GLOBALS['MySQL']->getDbCacheObject();
    $aStat = $oCache->getData($GLOBALS['MySQL']->genDbCacheKey('sys_stat_site'));
    if (null === $aStat) {
        genSiteStatCache();
        $aStat = $oCache->getData($GLOBALS['MySQL']->genDbCacheKey('sys_stat_site'));
    }

	if( !$aStat )
		$aStat = array();

	$sCode  = '<div class="siteStatMain">';

	foreach($aStat as $aVal)
		$sCode .= getSiteStatBody($aVal);
	
	$sCode .= '<div class="clear_both"></div></div>';

	return $sCode;
}

function genSiteStatFile($aVal) {
	$oMenu = new BxDolMenu();
	
	$sLink = $oMenu -> getCurrLink($aVal['link']);
	$sLine = "'{$aVal['name']}'=>array('capt'=>'{$aVal['capt']}', 'query'=>'".addslashes($aVal['query'])."', 'link'=>'$sLink', 'icon'=>'{$aVal['icon']}'),\n";
	
	return $sLine;
}

function genAjaxyPopupJS($iTargetID, $sDivID = 'ajaxy_popup_result_div', $sRedirect = '') {
	$iProcessTime = 1000;

	if ($sRedirect)
	   $sRedirect = "window.location = '$sRedirect';";
	
	$sJQueryJS = <<<EOF
<script type="text/javascript">

setTimeout( function(){
	$('#{$sDivID}_{$iTargetID}').show({$iProcessTime})
	setTimeout( function(){
		$('#{$sDivID}_{$iTargetID}').hide({$iProcessTime});
		$sRedirect
	}, 3000);
}, 500);

</script>
EOF;
	return $sJQueryJS;
}

function getBlockWidth ($iAllWidth, $iUnitWidth, $iNumElements) {
    $iAllowed = $iNumElements * $iUnitWidth;
    if ($iAllowed > $iAllWidth) {
        $iMax = (int)floor($iAllWidth / $iUnitWidth);
        $iAllowed = $iMax*$iUnitWidth;
    }
    return $iAllowed;
}

function getMemberLoginFormCode($sID = 'member_login_form', $sParams = '') 
{
    //get all auth types;
    $aAuthTypes = $GLOBALS['MySQL']-> fromCache('sys_objects_auths', 'getAll', 'SELECT * FROM `sys_objects_auths`');

    // define additional auth types; 
    if($aAuthTypes) {
        $aAddInputEl[''] = _t('_Basic');

        // procces all additional menu's items
        foreach($aAuthTypes as $iKey => $aItems)
        {
            $aAddInputEl[$aItems['Link']] = _t($aItems['Title']);
        }

        $aAuthTypes = array(
            'type' => 'select',
            'caption' => _t('_Auth type'),
            'values'    => $aAddInputEl,
            'value' => '', 
            'attrs' => array (
                'onchange' => 'if(this.value) {location.href = "' . BX_DOL_URL_ROOT . '" + this.value}',
            ),
        );
    }
    else {
        $aAuthTypes = array(
            'type' => 'hidden'
        );
    }

    $aForm = array(
        'form_attrs' => array(
            'id' => $sID,
            'action' => BX_DOL_URL_ROOT . 'member.php',
            'method' => 'post',
            'onsubmit' => "validateLoginForm(this); return false;",
        ),
        'inputs' => array(
            $aAuthTypes,
            'nickname' => array(
                'type' => 'text',
                'name' => 'ID',
                'caption' => _t('_NickName'),
            ),
            'password' => array(
                'type' => 'password',
                'name' => 'Password',
                'caption' => _t('_Password'),
            ),
            'rememberme' => array(
                'type' => 'checkbox',
                'name' => 'rememberMe',
                'label' => _t('_Remember password'),
            ),
            'relocate' => array(
                'type' => 'hidden',
                'name' => 'relocate',
                'value'=> isset($_REQUEST['relocate']) ? $_REQUEST['relocate'] : BX_DOL_URL_ROOT . 'member.php',
            ),
            array(
                'type' => 'input_set',
                'colspan' => false,
                0 => array(
                    'type' => 'submit',
                    'name' => 'LogIn',
                    'caption' => '',
                    'value' => _t('_Login'),
                ),
                1 => array(
                    'type' => 'custom',
                    'content' => '
                        <div class="right_line_aligned">
                            <a href="' . BX_DOL_URL_ROOT . 'forgot.php">' . _t("_forgot_your_password") . '?</a>
                        </div>
                        <div class="clear_both"></div>
                    ',
                ),
            ),
        ),
    );

    $oForm = new BxTemplFormView($aForm);

	bx_import('BxDolAlerts');
	$sCustomHtmlBefore = '';
	$sCustomHtmlAfter = '';
	$oAlert = new BxDolAlerts('profile', 'show_login_form', 0, 0, array('oForm' => $oForm, 'sParams' => &$sParams, 'sCustomHtmlBefore' => &$sCustomHtmlBefore, 'sCustomHtmlAfter' => &$sCustomHtmlAfter, 'aAuthTypes' => &$aAuthTypes));
	$oAlert->alert();

    $sFormCode = $oForm->getCode();
    
    $sJoinText = (strpos($sParams, 'no_join_text') === false) ?
        '<div class="login_box_text">' . _t('_login_form_description2join', BX_DOL_URL_ROOT) . '</div>' :
        '';
    
    return $sCustomHtmlBefore . $sFormCode . $sCustomHtmlAfter . $sJoinText;
}

bx_import('BxDolAlerts');
$oZ = new BxDolAlerts('system', 'design_included', 0);
$oZ->alert();

?>
