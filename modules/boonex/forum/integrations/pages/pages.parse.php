<?php

define ('BX_SECURITY_EXCEPTIONS', true);
$aBxSecurityExceptions = array ();
$aBxSecurityExceptions[] = 'POST.Check';
$aBxSecurityExceptions[] = 'REQUEST.Check';
$aBxSecurityExceptions[] = 'POST.Values';
$aBxSecurityExceptions[] = 'REQUEST.Values';


require_once('../../../inc/header.inc.php');
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_MODULES . 'modloaded/pages/classes/MlPagesPSFM.php' );
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

send_headers_page_changed();

$logged['admin'] = member_auth( 1, true, true );
switch(bx_get('action')) {
	case 'getArea':
		genAreaJSON((int)bx_get('id'), bx_get('sub_category'));
		break;
	case 'createNewBlock':
		createNewBlock(bx_get('sub_category'));
		break;
	case 'createNewItem':
		createNewItem(bx_get('sub_category'));
		break;
	case 'savePositions':
		savePositions((int)bx_get('id'), bx_get('sub_category'));
		break;
	case 'loadEditForm':
		showEditForm((int)bx_get('id'), (int)bx_get('area'), bx_get('sub_category'));
		break;
	case 'dummy':
		echo 'Dummy!';
		break;
	case 'Save'://save item
		saveItem((int)bx_get('area'), $_POST, bx_get('sub_category'));
		break;
	case 'Delete'://delete item
		deleteItem((int)bx_get('id'), (int)bx_get('area'), bx_get('sub_category'));
		break;
}

function createNewBlock($iCategory) {
	$oFields = new MlPagesPSFM( 1, $iCategory );
	$iNewID = $oFields -> createNewBlock();
	header('Content-Type:text/javascript');
	echo '{id:' . $iNewID . '}';
}

function createNewItem($iCategory) {
	$oFields = new MlPagesPSFM( 1, $iCategory );
	$iNewID = $oFields -> createNewField();

	bx_import('BxDolInstallerUtils');
	$oInstallerUtils = new BxDolInstallerUtils();
	$oInstallerUtils->updateProfileFieldsHtml();
	
	header('Content-Type:text/javascript');
	echo '{id:' . $iNewID . '}';
}

function genAreaJSON( $iAreaID, $iCategory) {
	$oFields = new MlPagesPSFM( $iAreaID, $iCategory );
	
	header( 'Content-Type:text/javascript' );
	echo $oFields -> genJSON();
}

function savePositions( $iAreaID, $iCategory ) {
	$oFields = new MlPagesPSFM( $iAreaID, $iCategory );
	
	header( 'Content-Type:text/javascript' );
	$oFields -> savePositions( $_POST );

	$oCacher = new BxDolPSFMCacher($iCategory);
	$oCacher -> createCache();
}

function saveItem( $iAreaID, $aData, $iCategory ) {
	$oFields = new MlPagesPSFM( $iAreaID, $iCategory );
	$oFields -> saveItem( $_POST );

	$oCacher = new BxDolPSFMCacher($iCategory);
	$oCacher -> createCache();
}

function deleteItem( $iItemID, $iAreaID, $iCategory ) {
	$oFields = new MlPagesPSFM( $iAreaID, $iCategory );
	$oFields -> deleteItem( $iItemID );

	$oCacher = new BxDolPSFMCacher($iCategory);
	$oCacher -> createCache();
}

function showEditForm( $iItemID, $iAreaID, $iCategory ) {
	$oFields = new MlPagesPSFM( $iAreaID, $iCategory );
	
	ob_start();
	?>
	<form name="fieldEditForm" method="post" action="<?=$GLOBALS['site']['url'] . 'modules/modloaded/pages/pages.parse.php'; ?>" target="fieldFormSubmit" onsubmit="clearFormErrors( this )">
        <div class="edit_item_table_cont">
            <?=$oFields -> genFieldEditForm( $iItemID ); ?>
        </div>
	</form>

	<iframe name="fieldFormSubmit" style="display:none;"></iframe>
	<?
	$sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => ob_get_clean()));

	echo PopupBox('pf_edit_popup', _t('_adm_fields_box_cpt_field'), $sResult);
}

?>
