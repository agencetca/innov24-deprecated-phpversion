<?php
/***************************************************************************
* Date				: Sun August 1, 2010
* Copywrite			: (c) 2009, 2010 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Tools
* Product Version	: 1.8.4
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

$bCookieSet = false;
if (isset($_COOKIE['logonframe'])) $bCookieSet = true;
if ($bCookieSet || isAdmin()) {
	//setcookie('logonframe', '', time() - 96 * 3600, $t);
	//unset($_COOKIE['logonframe']);
	$m=$_GET['m'];
	$p=$_GET['p'];
	$t=$_GET['t'];
	// logout current member
	//setcookie('memberID', '', time() - 96 * 3600, $t);
	//setcookie('memberPassword', '', time() - 96 * 3600, $t);
	//unset($_COOKIE['memberID']);
	//unset($_COOKIE['memberPassword']);
	// the above no longer needed. Deleting all cookies now.

	// switch back to origional admin
	// Due to possible problems with other modules, i need to loop through
	// all cookies and remove them.
	foreach($_COOKIE as $key => $value ) {
		//echo $key . "<br>";
		setcookie($key,'',time()-3000,'/');
		unset($key);
	}
	//exit;
	$sHost = '';
	$iCookieTime = $bRememberMe ? time() + 24*60*60*30 : 0;
	setcookie("memberID", $m, $iCookieTime, $t, $sHost);
	$_COOKIE['memberID'] = $m;
	setcookie("memberPassword", $p, $iCookieTime, $t, $sHost, false, true /* http only */);
	$_COOKIE['memberPassword'] = $p;
	// redirect back to deanos_tools.
	header( "Location:../../?r=deanos_tools/administration/&se=sa" );
} else {
	$_page['name_index'] = 0;
	$_page['header'] = "{$site['title']}";
	$_page['header_text'] = "{$site['title']}";
	$_page_cont[0]['page_main_code'] = MsgBox(_t('_INVALID_ROLE'));
	PageCode();
	exit;
}
?>