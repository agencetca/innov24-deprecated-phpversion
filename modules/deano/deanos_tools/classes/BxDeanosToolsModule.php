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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolModule.php');
require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPaginate.php');

class BxDeanosToolsModule
    extends BxDolModule
    {

    // constructor
    function BxDeanosToolsModule(&$aModule) { parent::BxDolModule($aModule); }

    function actionAdministration()
        {
        global $_page, $_page_cont;
        require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');
        $logged['admin']=member_auth(1, true, true);
        $iNameIndex=9;
        $_page=array
            (
            'name_index' => $iNameIndex,
            'css_name' => array('forms_adv.css'),
            'js_name' => array(),
            'header' => _t('_dbcsDeanosToolsHeader'),
            'header_text' => _t('_dbcsDeanosToolsHeaderText')
            );

        $_page_cont[$iNameIndex]['page_main_code'].=$this->DeanoMainCode();
        PageCodeAdmin();
        }

    function DeanoMainCode()
        {
		global $site;
		$settingsv = ' collapsed';
		$settingss = ' style="display: none;"';
		$saCv = ' collapsed';
		$saCs = ' style="display: none;"';
		$scCv = ' collapsed';
		$scCs = ' style="display: none;"';
		$smCv = ' collapsed';
		$smCs = ' style="display: none;"';
		$stCv = ' collapsed';
		$stCs = ' style="display: none;"';
		$sqCv = ' collapsed';
		$sqCs = ' style="display: none;"';
		$pmCv = ' collapsed';
		$pmCs = ' style="display: none;"';
		$pwCv = ' collapsed';
		$pwCs = ' style="display: none;"';
		$pbCv = ' collapsed';
		$pbCs = ' style="display: none;"';
		$peCv = ' collapsed';
		$peCs = ' style="display: none;"';
		//$otCv = ' collapsed';
		//$otCs = ' style="display: none;"';
		$cacheC = ' collapsed';
		$cacheS = ' style="display: none;"';
		$ipC = ' collapsed';
		$ipS = ' style="display: none;"';

        $sExistedC = _t('_dbcsDeanosToolsBoxHeader', $this->_oDb->getModuleVersion());
        $sCss = $this->_oTemplate->addCss('unit.css', true);
		$sJs = $this->_oTemplate->addJs('deanostools.js', true);
        $sAction = BX_DOL_URL_ROOT . 'modules/?r=deanos_tools/administration/';
		$GetLangID = $_GET['LangID'];
		if ($GetLangID == '') $GetLangID = 1;
        $dbAction = $_REQUEST['saction'];
		$section = $_REQUEST['se'];
		if(isset($_POST['save']) && isset($_POST['cat'])) {
			$section = 'settings';
			$this->dbcsClearCache('sys_options');
		}
		$sCode = '';

		switch ($section) {
			case "settings":
				$settingsv = '';
				$settingss = '';
		        break;
		    case "sa":
				$saCv = '';
				$saCs = '';
		        break;
			case "sc":
				$scCv = '';
				$scCs = '';
			break;
			case "sm":
				$smCv = '';
				$smCs = '';
			break;
			case "st":
				$stCv = '';
				$stCs = '';
			break;
			case "sq":
				$sqCv = '';
				$sqCs = '';
			break;
			case "pm":
				$pmCv = '';
				$pmCs = '';
			break;
			case "pw":
				$pwCv = '';
				$pwCs = '';
			break;
			case "pb":
				$pbCv = '';
				$pbCs = '';
			break;
			case "pe":
				$peCv = '';
				$peCs = '';
			break;
			case "ot":
				$otCv = '';
				$otCs = '';
			break;
			case "dc":
				$cacheC = '';
				$cacheS = '';
			break;
			case "ip":
				$ipC = '';
				$ipS = '';
			break;

		}
	
		switch ($dbAction) {
		    case "sm":
				// set role to member
				$dbID = $_POST['id'];
				if($dbID > 0) {
					$this->_oDb->setMember($dbID);
				    $sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_Member') . ' ' . $this->_oDb->getNickName($dbID) . ' ' . _t('_dbcs_DT_Member Role'),4) . '</div>';
				   	$sCode .= '	<div class="clear_both"></div>';
					$this->dbcsClearCache('users');
				}
		    break;
			case "sa":
				// set role to admin
				$dbID = $_POST['id'];
				if($dbID > 0) {
					$this->_oDb->setAdmin($dbID);
				    $sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_Member') . ' ' . $this->_oDb->getNickName($dbID) . ' ' . _t('_dbcs_DT_Admin Role'),4) . '</div>';
				   	$sCode .= '	<div class="clear_both"></div>';
					$this->dbcsClearCache('users');
				}
			break;
			case "sc":
				// save copyright.
				$dbID = $this->_oDb->getCopyrightID();
				$dbLang=$_GET['LangID'];
				$dbText=$_POST['copyright'];
				$this->_oDb->saveCopyrightText($dbID,$dbLang,$dbText);
				compileLanguage($dblang);
				$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_Copyright Text Saved'),4) . '</div>';
				$sCode .= '	<div class="clear_both"></div>';
			break;

			case "ds":
				// delete shoutbox messages.
				$dbcnt=0;
				foreach($_POST as $name => $value) {
					$r=strstr($name, "C_");
					if($r) {
						$s=explode("_",$r);
						$n=$s[1];
						$this->_oDb->deleteShoutboxMessage($n);
						$dbcnt++;
					}
				}
				if ($dbcnt > 1) {
					$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_Deleted') . $dbcnt . _t('_dbcs_DT_ShoutboxM1'),4) . '</div>';
					$sCode .= '	<div class="clear_both"></div>';
				}
				if ($dbcnt == 1) {
					$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_Deleted') . $dbcnt . _t('_dbcs_DT_ShoutboxM2'),4) . '</div>';
					$sCode .= '	<div class="clear_both"></div>';
				}
				if ($dbcnt == 0) {
					$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_ShoutboxM3'),4) . '</div>';
					$sCode .= '	<div class="clear_both"></div>';
				}

			break;
			case "dt":
				// delete site tags.
				$dbcnt=0;
				foreach($_POST as $name => $value) {
					$r=strstr($name, "C_");
					if($r) {
						$s=explode("_",$r);
						$n=$s[1];
						$b=explode(",",$n);
						$a1=base64_decode($b[0]);
						$a2=base64_decode($b[1]);
						$this->_oDb->deleteTag($a1,$a2);
						$dbcnt++;
					}
				}
				if ($dbcnt > 1) {
					$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_Deleted') . $dbcnt . _t('_dbcs_DT_SiteTagM1'),4) . '</div>';
					$sCode .= '	<div class="clear_both"></div>';
				}
				if ($dbcnt == 1) {
					$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_Deleted') . $dbcnt . _t('_dbcs_DT_SiteTagM2'),4) . '</div>';
					$sCode .= '	<div class="clear_both"></div>';
				}
				if ($dbcnt == 0) {
					$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_SiteTagM3'),4) . '</div>';
					$sCode .= '	<div class="clear_both"></div>';
				}
			break;
			case "sq":
				// sql query.
				$dbQuery = $_POST['dbcsDTsqlquery'];
				if (get_magic_quotes_gpc()) {
					$dbQuery = stripslashes($dbQuery);
				}
				file_put_contents(BX_DIRECTORY_PATH_ROOT . "tmp/dbsql.sql",$dbQuery);
				execSqlFile(BX_DIRECTORY_PATH_ROOT . "tmp/dbsql.sql");
				unlink(BX_DIRECTORY_PATH_ROOT . "tmp/dbsql.sql");
				$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_MySql Query Executed'),4) . '</div>';
				$sCode .= '	<div class="clear_both"></div>';
			break;
			case "uf":
				$target_path = BX_DIRECTORY_PATH_ROOT . "tmp/";
				$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
				if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
					execSqlFile($target_path);
					unlink($target_path);
					$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_MySql File Uploaded and Executed'),4) . '</div>';
					$sCode .= '	<div class="clear_both"></div>';
				} else{
					$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_Error_Uploading_File'),4) . '</div>';
					$sCode .= '	<div class="clear_both"></div>';
				}
			break;

			case "dm":
				// delete messages.
				$senderID=$_POST['senderid'];
				$this->_oDb->deleteMessages($senderID);
				$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_DeleteM1') . $senderID . _t('_dbcs_DT_DeleteM2'),4) . '</div>';
				$sCode .= '	<div class="clear_both"></div>';
			break;
			case "sw":
				// Set Page Width.
				$iPageWidth=$_POST['pagewidth'];
				$this->_oDb->setPageWidth($iPageWidth);
				$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_PageWidthSet'),4) . '</div>';
				$sCode .= '	<div class="clear_both"></div>';
				$this->dbcsClearCache('sys_page_compose');
				$this->dbcsClearCache('sys_options');
			break;
			case "pb":
				// Insert PHP Block.
				$s1 = $_POST['phppage'];
				$s2 = $_POST['phplkey'];
				$s3 = $_POST['phpltext'];
				$s4 = process_db_input($_POST['dbcsDTphpcode']);
				if (!$this->_oDb->keyExists($s2)) {
					// key does not exist. Add it to all installed languages under my catagory.
					addStringToLanguage($s2, $s3, -1, $this->_oDb->getLangCat());
				}
				// now insert the php block on specified page.
				$this->_oDb->insertPHPBlock($s1,$s2,$s3,$s4);
				
				$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_PHPBlockSaved'),4) . '</div>';
				$sCode .= '	<div class="clear_both"></div>';
				$this->dbcsClearCache('sys_page_compose');
				$this->dbcsClearCache('sys_options');
			break;
			case "pe":
				// Edit/Delete PHP Block.
				$PHPid = intval($_GET['phpbid']);
				if (isset($_POST['B1']) && $PHPid > 0) {
					// save button clicked.
					$sLkey = $_POST['phplkey'];
					$sPHPCode = process_db_input($_POST['dbcsDTphpcode']);

					$this->_oDb->updatePHPBlock($PHPid,$sLkey,$sPHPCode);

					$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_PHPBlockSavedE'),4) . '</div>';
					$sCode .= '	<div class="clear_both"></div>';
				}

				if (isset($_POST['B2']) && $PHPid > 0) {
					// delete button clicked.

					$this->_oDb->deletePHPBlock($PHPid);

					$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_PHPBlockDeletedE'),4) . '</div>';
					$sCode .= '	<div class="clear_both"></div>';
				}
				$this->dbcsClearCache('sys_page_compose');
				$this->dbcsClearCache('sys_options');
				$_GET['phpbid'] = 0;
			break;
			case "dc":
				// process cache file deletion.
				$bSelected = false;
				if (isset($_POST['B1'])) {
					$sPath = BX_DIRECTORY_PATH_DBCACHE;
					$s = $_POST['CacheS'];
					if ($s){
						$bSelected = true;
						foreach ($s as $t){
							if (is_file($sPath . $t)) {
								unlink($sPath . $t);
							}
							if (is_dir($sPath . $t)) {
								$this->delete_directory($sPath . $t);
							}
							//$sCode .= $t . '<br />';
						}
					}



					if ($bSelected) {
						$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_SelectedCacheDeleted'),4) . '</div>';
						$sCode .= '	<div class="clear_both"></div>';
					} else {
						$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_NoCacheSelected'),4) . '</div>';
						$sCode .= '	<div class="clear_both"></div>';
					}
				}
				if (isset($_POST['B2'])) {
					$sPath = BX_DIRECTORY_PATH_CACHE_PUBLIC;
					$s = $_POST['CachePublicS'];
					if ($s){
						$bSelected = true;
						foreach ($s as $t){
							if (is_file($sPath . $t)) {
								unlink($sPath . $t);
							}
							if (is_dir($sPath . $t)) {
								$this->delete_directory($sPath . $t);
							}
							//$sCode .= $t . '<br />';
						}
					}




					if ($bSelected) {
						$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_SelectedCachePublicDeleted'),4) . '</div>';
						$sCode .= '	<div class="clear_both"></div>';
					} else {
						$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_NoCachePublicSelected'),4) . '</div>';
						$sCode .= '	<div class="clear_both"></div>';
					}

				}
				if (isset($_POST['B3'])) {
					$sPath = BX_DIRECTORY_PATH_ROOT . 'tmp/';
					$s = $_POST['TmpS'];
					if ($s){
						$bSelected = true;
						foreach ($s as $t){
							if (is_file($sPath . $t)) {
								unlink($sPath . $t);
							}
							if (is_dir($sPath . $t)) {
								$this->delete_directory($sPath . $t);
							}
							//$sCode .= $t . '<br />';
						}
					}





					if ($bSelected) {
						$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_SelectedTmpDeleted'),4) . '</div>';
						$sCode .= '	<div class="clear_both"></div>';
					} else {
						$sCode .= '	<div id="quotes_box">' . MsgBox(_t('_dbcs_DT_NoTmpSelected'),4) . '</div>';
						$sCode .= '	<div class="clear_both"></div>';
					}
				}

			break;
			case "ip":
				// IP Addresses.

			break;

		}
		

$sCode .= '
<div class="form_advanced_wrapper adm-settings-form_wrapper">
<table cellspacing="0" cellpadding="0" class="form_advanced_table">


  
<!-- ************************************** Section Begin ************************************** -->

  <thead class="collapsable' . $settingsv . '">
    <tr class="headers">
      <th class="block_header">' . _t('_dbcs_DT_Settings_HE') . '</th>
    </tr>
  </thead>
  <tbody' . $settingss . '>
<tr><td>
';

            // get sys_option's category id;
            $iCatId = $this-> _oDb -> getSettingsCategoryId('dbcs_DT_sapp');
            if(!$iCatId) {
                $sOptions = MsgBox( _t('_Empty') );
            }
            else {
                bx_import('BxDolAdminSettings');

                $oSettings = new BxDolAdminSettings($iCatId);
                
                $mixedResult = '';
                if(isset($_POST['save']) && isset($_POST['cat'])) {
                    $mixedResult = $oSettings -> saveChanges($_POST);
                }

                // get option's form;
                $sOptions = $oSettings -> getForm();
                if($mixedResult !== true && !empty($mixedResult)) {
                    $sOptions = $mixedResult . $sOptions;
                }
            }

			$dbcsMessage = '<div style="padding-bottom:8px; white-space:normal;">' . _t('_dbcs_DT_Settings_Message') . '</div>';
            $sCssStyles = $this -> _oTemplate -> addCss('forms_adv.css', true);


        $sCode .= DesignBoxAdmin( _t('_Settings')
			, $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => $dbcsMessage . $sCssStyles . $sOptions) ));


		$guestLog = getParam('dbcs_DT_logguests'); 
		$iGuestCheck = $this->_oDb->checkGuestAlert();
		if ($guestLog == 1) {
			if ($iGuestCheck < 1) {
				$this->_oDb->addGuestAlert();
				$this->dbcsClearCache('sys_alerts');
				$this->dbcsClearCache('sys_options');
			}
		}
		if ($guestLog == 0) {
			if ($iGuestCheck > 0) {
				$this->_oDb->removeGuestAlert();
				$this->dbcsClearCache('sys_alerts');
				$this->dbcsClearCache('sys_options');
			}
		}


$sCode .= '
</td></tr>
</tbody>
<!-- ************************************** Section End ************************************** -->






  
  <thead class="collapsable' . $saCv . '">
    <tr class="headers">
      <th class="block_header">' . _t('_dbcs_DT_Site Administrators') . '</th>
    </tr>
  </thead>
  <tbody' . $saCs . '>

<tr>
<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section1_Msg') . '</td>
</tr>



<tr>
	<td>';

	$start = intval($_GET['st']);
	$perpage = intval($_GET['pp']);
	if ($perpage == 0) $perpage = getParam('dbcs_DT_sapp'); 
	if ($_REQUEST['search'] == '') {
		$sCode .= '<form method="POST" action="?r=deanos_tools/administration/&se=sa">';
	} else {
		$sCode .= '<form method="POST" action="?r=deanos_tools/administration/&se=sa&search=' . $_REQUEST['search'] . '">';
	}
	$sCode .= '
	<div style="float:left;margin-right:6px;margin-top:5px">' . _t('_dbcs_DT_Search_Label') . '</div><div style="float:left;margin-right:6px;;margin-top:1px" class="input_wrapper input_wrapper_text">
      <input type="text" name="search" class="form_input_text" value="' . $_REQUEST['search'] . '">
      <div class="input_close input_close_text"></div>
    </div><div style="float:left;"><input type="submit" value="' . _t('_dbcs_DT_Search_Button') . '" name="B1"></div>
	</form>
	</td>
</tr>




    <tr>
      <td>
<table width="100%" border="0" cellspacing="0" cellpadding="0">




<tr class="db1">';
$s1 = 'right';
$s2 = 'right';
if ($_GET['ob'] == 'ID') $s1 = 'down';
if ($_GET['ob'] == '' || $_GET['ob'] == 'NickName') $s2 = 'down';
$sCode .= '
<td><div class="sortcont"><a href="?r=deanos_tools/administration/&se=sa&ob=ID">' . _t('_dbcs_DT_Member ID') . '<div class="sortarrow"><img class="block_collapse_btn" src="' . BX_DOL_URL_ROOT . 'templates/base/images/icons/toggle_' . $s1 . '.png"></div></a></div></td>
<td><div class="sortcont2"><a href="?r=deanos_tools/administration/&se=sa&ob=NickName">' . _t('_dbcs_DT_Member Nickname') . '<div class="sortarrow2"><img class="block_collapse_btn" src="' . BX_DOL_URL_ROOT . 'templates/base/images/icons/toggle_' . $s2 . '.png"></div></a></div></td>
<td>' . _t('_dbcs_DT_Logon as Member') . '</td>
<td>' . _t('_dbcs_DT_Current Role') . '</td>
<td>' . _t('_dbcs_DT_Set Role') . '</td>
</tr>
';

$memCount = $this->_oDb->getMemberCount();
$adminCount = $this->_oDb->getAdminCount();
$orderBy = 'NickName';
$sSearch = $_REQUEST['search'];
if ($_GET['ob'] != '') $orderBy = $_GET['ob'];
$MemKeys = $this->_oDb->getMembers($start,$perpage,$orderBy,$sSearch);
foreach ($MemKeys as $iID => $MemData) {
	$MemID = (int)$MemData['ID'];
	$MemNick = $MemData['NickName'];
	$MemRole = $MemData['Role'];
	$sCode .= '<tr class="db2">';
	$MemPass = getPassword($MemID);
	$aID = $_COOKIE['memberID'];
	$aPass = getPassword($aID);
    $aUrl = parse_url($GLOBALS['site']['url']);
    $sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';

	$sCode .= '<td width="1"><a href="' . BX_DOL_URL_ROOT . 'pedit.php?ID=' . $MemID . '">' . $MemID . '</a></td>';
	$sCode .= '<td><a href="' . BX_DOL_URL_ROOT . $MemNick . '">' . $MemNick . '</a></td>';
	if ($_COOKIE['memberID'] == $MemID) {
		$sCode .= '<td width="60" style="color:#800000">Logged On</td>';
	} else {
		$sCode .= '<td width="60"><a href="' . BX_DOL_URL_MODULES . 'deano/deanos_tools/logon_frame.php?m=' . $MemID . '&p=' . $MemPass . '&am=' . $aID . '&ap=' . $aPass . '&t=' . $sPath . '">Logon</a></td>';
	}
	if ($MemRole == 3) {
		$sCode .= '<td width="60" style="color:#800000">' . _t('_dbcs_DT_Admin') . '</td>';
	} else {
		$sCode .= '<td width="60" style="color:#000080">' . _t('_dbcs_DT_Member') . '</td>';
	}
	if ($MemRole == 3) {
		if ($adminCount > 1) {
			//$sCode .= '<td width="80"><a href="?r=deanos_tools/administration/&se=sa&saction=sm&id=' . $MemID . '">' . _t('_dbcs_DT_Set as Member') . '</a></td>';
			$sCode .= '<td width="80"><a href="javascript:void(0);" onclick="setMember(\'' . $MemID . '\')">' . _t('_dbcs_DT_Set as Member') . '</a></td>';

		} else {
			$sCode .= '<td width="80">' . _t('_dbcs_DT_Not Available') . '</td>';
		}
	} else {
		//$sCode .= '<td width="80"><a href="?r=deanos_tools/administration/&se=sa&saction=sa&id=' . $MemID . '">' . _t('_dbcs_DT_Set as Admin') . '</a></td>';
		$sCode .= '<td width="80"><a href="javascript:void(0);" onclick="setAdmin(\'' . $MemID . '\')">' . _t('_dbcs_DT_Set as Admin') . '</a></td>';

	}

	$sCode .= '</tr>';

}

	if ($_REQUEST['search'] == '') {
		$sPageURL = '?r=deanos_tools/administration/&se=sa&st={start}&pp={per_page}&ob=' . $orderBy;
	} else {
		$sPageURL = '?r=deanos_tools/administration/&se=sa&st={start}&pp={per_page}&ob=' . $orderBy . '&search=' . $_REQUEST['search'];
	}

	if ($_REQUEST['search'] != '') $memCount = $this->_oDb->getMemberCount2($orderBy,$sSearch);

	$oPaginate = new BxDolPaginate(array(
        'start' => $start,
        'count' => $memCount,
        'per_page' => $perpage,
        'page_url' => $sPageURL,
        'on_change_page' => 'aa'
    ));
    $sPaginate = $oPaginate->getPaginate();    


$sCode .= '
</table> ' . $sPaginate;
$sCode .= '
<div style="display: none">
<form method="POST" id="setadmin" name="setadmin" action="?r=deanos_tools/administration/&se=sa">
	<input type="hidden" name="saction" value="zz">
	<input type="hidden" name="id" value="0">
</form>
</div>
<br>
</td>
    </tr>
  </tbody>
<thead class="collapsable' . $scCv . '">
  <tr class="headers">
    <th class="block_header">' . _t('_dbcs_DT_Set Copyright Text') . '</th>
  </tr>
</thead>
<tbody' . $scCs . '>
<tr>
<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section4_Msg') . '</td>
</tr>
    <tr>
      <td>
<form id="langform" method="POST" action="?r=deanos_tools/administration/&saction=sc&se=sc&LangID=' . $GetLangID . '">
  <table cellspacing="0" cellpadding="0" class="form_advanced_table">
    <tr>
      <td class="caption">' . _t('_dbcs_DT_LangR') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_select_box">
          <select name="sbLangKeys" class="form_input_select" onChange="javascript: LoadLang(this.value)">
          ';
$lKeys = $this->_oDb->getLangKeys();
foreach ($lKeys as $iID => $dbData) {
	$iLID = (int)$dbData['ID'];
	$sLName = $dbData['Name'];
	$sLTitle = $dbData['Title'];
	$sLFlag = $dbData['Flag'];
	if ($iLID == $GetLangID) {
		$sCode .= '
<option value="' . $iLID . '" selected>' . $sLTitle . '</option>
';
	} else {
		$sCode .= '
<option value="' . $iLID . '">' . $sLTitle . '</option>
';
	}
}
$dbID = $this->_oDb->getCopyrightID();
$dbText = $this->_oDb->getCopyrightText($dbID,$GetLangID);
$sCode .= '
</select>
</div>
<div class="clear_both">
</td>
</tr>
<tr>
  <td class="caption">' . _t('_dbcs_DT_Copyright Text') . '</td>
  <td class="value"><div class="clear_both"></div>
    <div class="input_wrapper input_wrapper_text">
      <input type="text" value="' . $dbText . '" name="copyright" class="form_input_text">
      <div class="input_close input_close_text"></div>
    </div>
    <div class="clear_both"></div></td>
</tr>
<tr>
  <td class="caption"></td>
  <td class="value"><input type="submit" value="' . _t('_dbcs_DT_Save') . '" name="B1"></td>
</tr>
</table>
</form>
</td>
</tr>
</tbody>
'; 
if ($this->_oDb->isShoutBoxInstalled()) {
  $sCode .= '
  <thead class="collapsable' . $smCv . '">
    <tr class="headers">
      <th class="block_header">' . _t('_dbcs_DT_Shoutbox Messages') . '</th>
    </tr>
  </thead>
  <tbody' . $smCs . '>
<tr>
<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section5_Msg') . '</td>
</tr>

	<tr>
      <td>
<form id="sbform" method="POST" action="?r=deanos_tools/administration/&saction=ds&se=sm">	  
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="db1">
      <td nowrap width="10"><input id="checkall" type="checkbox" name="checkall" value="ON" onclick="checkedAll(\'sbform\');"></td>
      <td nowrap width="10"><b>' . _t('_dbcs_DT_From') . '</b></td>
      <td><b>' . _t('_dbcs_DT_Message') . '</b></td>
      <td nowrap width="150"><b>' . _t('_dbcs_DT_Date') . '</b></td>
    </tr>
';
$sbMessages = $this->_oDb->getShoutboxMessages();
$dbcnt = 0;
foreach ($sbMessages as $iID => $sbData) {
	$sbID = (int)$sbData['ID'];
	$sbFrom = (int)$sbData['OwnerID'];
	$sbMsg = $sbData['Message'];
	$sbDate = $sbData['Date'];
	$sCode .= '<tr class="db2">';
	$sCode .= '<td><input type="checkbox" id="C_' . $sbID . '" name="C_' . $sbID . '" value="ON"></td>';
	$sCode .= '<td>' . $this->_oDb->getNickName($sbFrom) . '</td>';
	$sCode .= '<td style="white-space: normal;">' . $sbMsg . '</td>';
	$sCode .= '<td>' . $sbDate . '</td>';
	$sCode .= '</tr>';
	$dbcnt++;
}	  
if ($dbcnt > 0) {
	$sCode .= '<td colspan="4"><input type="submit" value="' . _t('_dbcs_DT_Delete') . '" name="B2"></td>';
} else {
	$sCode .= '<td colspan="4">' . MsgBox(_t('_Empty')) . '</td>';
}

$sCode .= '

</table>
</form>
	  </td>
    </tr>
    <tr>
';
$sCode .= '
</tr>


  </tbody>

';
}


$sCode .= '
  <thead class="collapsable' . $stCv . '">
    <tr class="headers">
      <th class="block_header">' . _t('_dbcs_DT_Site Tags') . '</th>
    </tr>
  </thead>
  <tbody' . $stCs . '">
<tr>
<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section6_Msg') . '</td>
</tr>
	<tr>
      <td>
	  
<form id="tagform" method="POST" action="?r=deanos_tools/administration/&saction=dt&se=st">	  
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="db1">
      <td nowrap width="10"><input id="checkall" type="checkbox" name="checkall" value="ON" onclick="checkedAll(\'tagform\');"></td>
      <td><b>' . _t('_dbcs_DT_Tag') . '</b></td>
      <td><b>' . _t('_dbcs_DT_Type') . '</b></td>
      <td><b>' . _t('_dbcs_DT_Date') . '</b></td>
    </tr>
';

$tagsStart = intval($_GET['st']);
$tagsPerPage = intval($_GET['pp']);
if($tagsPerPage == 0) $tagsPerPage = getParam('dbcs_DT_stpp');
$tagsCount = $this->_oDb->getTagCount();
if ($tagsCount > 0) {
	$sTags = $this->_oDb->getTags($tagsStart, $tagsPerPage);
	foreach ($sTags as $iID => $stData) {
		$stTag = $stData['Tag'];
		$stTag64 = base64_encode($stTag);
		$stType = $stData['Type'];
		$stType64 = base64_encode($stType);
		$stDate = $stData['Date'];
		$sCode .= '<tr class="db2">';
		$sCode .= '<td><input type="checkbox" id="C_' . $stTag64 . ',' . $stType64 . '" name="C_' . $stTag64 . ',' . $stType64 . '" value="ON"></td>';
		$sCode .= '<td>' . $stTag . '</td>';
		$sCode .= '<td>' . $stType . '</td>';
		$sCode .= '<td>' . $stDate . '</td>';
		$sCode .= '</tr>';
	}	  

	$sTagsPageURL = '?r=deanos_tools/administration/&se=st&st={start}&pp={per_page}';
	$oPaginate = new BxDolPaginate(array(
        'start' => $tagsStart,
        'count' => $tagsCount,
        'per_page' => $tagsPerPage,
        'page_url' => $sTagsPageURL,
        'on_change_page' => 'aa'
    ));
    $sPaginate = $oPaginate->getPaginate();    
	
	$sCode .= '<tr><td style="padding:0px;" colspan="4">' . $sPaginate . '</td></tr>';
	
    $sCode .= '<td height="30" colspan="4"><input type="submit" value="' . _t('_dbcs_DT_Delete') . '" name="B2"></td>';
} else {
	$sCode .= '<td>' . MsgBox(_t('_Empty')) . '</td>';
}


$sCode .= '
</table>
</form>
    </td>
	</tr>
	<tr>
';
$sCode .= '
    </tr>
  </tbody>

<thead class="collapsable' . $sqCv . '">
  <tr class="headers">
    <th class="block_header">' . _t('_dbcs_DT_SQL Query Tool') . '</th>
  </tr>
</thead>
<tbody' . $sqCs . '">
<tr>
<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section7_Msg') . '</td>
</tr>
	<tr>
      <td>
<form id="sqlform" method="POST" action="?r=deanos_tools/administration/&saction=sq&se=sq">
  <table border="0" width="100%">
	<tr>
		<td style="width: 10px;white-space:nowrap;vertical-align:top;">' . _t('_dbcs_DT_MySQL Query') . '</td>
		<td><div class="input_wrapper input_wrapper_textarea" style="width: 100%">
          <div class="input_border">
            <textarea name="dbcsDTsqlquery" class="form_input_textarea" rows="1" cols="20"></textarea>
          </div>
          <div class="input_close_textarea left top"></div>
          <div class="input_close_textarea left bottom"></div>
          <div class="input_close_textarea right top"></div>
          <div class="input_close_textarea right bottom"></div>
        </div></td>
	</tr>
	<tr>
		<td style="width: 10px;white-space:nowrap;vertical-align:middle;">&nbsp;</td>
		<td><input type="submit" value="' . _t('_dbcs_DT_Send') . '" name="B1"></td>
	</tr>
	</table>
</form>
<form enctype="multipart/form-data" action="?r=deanos_tools/administration/&saction=uf&se=sq" method="POST">
	<table border="0 width=" width="100%">
		<tr>
			<td style="width: 10px;white-space:nowrap;vertical-align:middle;">' . _t('_dbcs_DT_FileToUpload') . '</td>
			<td style="width: 10px;white-space:nowrap;vertical-align:middle;"><input name="uploadedfile" type="file" /></td>
			<td><input type="submit" value="Upload File" /></td>
		</tr>
	</table>
</form>
</td>
    </tr>
  </tbody>



<!-- ************************************** Section Begin ************************************** -->
<thead class="collapsable' . $pmCv . '">
  <tr class="headers">
    <th class="block_header">' . _t('_dbcs_DT_Remove Spam Messages') . '</th>
  </tr>
</thead>
<tbody' . $pmCs . '">
	<tr>
		<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section8_Msg') . '</td>
	</tr>

    <tr>
      <td>
<form id="dmform" method="POST" action="?r=deanos_tools/administration/&saction=dm&se=pm">	  
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="caption">' . _t('_dbcs_DT_Senders Member ID') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text">
          <input type="text" name="senderid" class="form_input_text">
          <div class="input_close input_close_text"></div>
        </div>
        <div class="clear_both"></div></td>
	</tr>
	<tr>
		<td class="caption"></td>
		<td class="value"><input type="submit" value="' . _t('_dbcs_DT_Delete') . '" name="B2"></td>
	</tr>

</table>
</form>
	  </td>
    </tr>
  </tbody>
<!-- ************************************** Section End ************************************** -->

<!-- ************************************** Section Begin ************************************** -->
<thead class="collapsable' . $pwCv . '">
  <tr class="headers">
    <th class="block_header">' . _t('_dbcs_DT_Set Page Widths') . '</th>
  </tr>
</thead>
<tbody' . $pwCs . '">
	<tr>
		<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section9_Msg') . '</td>
	</tr>

    <tr>
      <td>
<form id="dmform" method="POST" action="?r=deanos_tools/administration/&saction=sw&se=pw">	  
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="caption">' . _t('_dbcs_DT_Page Width') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text">';
		if ($_POST['pagewidth'] == '') {
			$iPageWidth = 998;
		} else {
			$iPageWidth = intval($_POST['pagewidth']);
		}
		$sCode .= '
          <input type="text" name="pagewidth" value="' . $iPageWidth . '" class="form_input_text">
          <div class="input_close input_close_text"></div>
        </div>
        <div class="clear_both"></div></td>
	</tr>
	<tr>
		<td class="caption"></td>
		<td class="value"><input type="submit" value="' . _t('_dbcs_DT_Save') . '" name="B2"></td>
	</tr>

</table>
</form>
	  </td>
    </tr>
  </tbody>
<!-- ************************************** Section End ************************************** -->


<!-- ************************************** Section Begin ************************************** -->

  <thead class="collapsable' . $pbCv . '">
    <tr class="headers">
      <th class="block_header">' . _t('_dbcs_DT_PHPBlock_H') . '</th>
    </tr>
  </thead>
  <tbody' . $pbCs . '>
<tr>
<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section10_Msg') . '</td>
</tr>

    <tr>
      <td class="dbrow">
<form method="POST" action="?r=deanos_tools/administration/&saction=pb&se=pb">
  <table cellspacing="0" cellpadding="0" class="form_advanced_table">
    <tr>
      <td class="caption">' . _t('_dbcs_DT_PHPBlock_P') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_select_box">
          <select name="phppage" class="form_input_select">
          ';
$lKeys = $this->_oDb->getPages();
foreach ($lKeys as $iID => $dbData) {
	$sLName = $dbData['Name'];
	$sLTitle = $dbData['Title'];
	$sCode .= '<option value="' . $sLName . '">' . $sLTitle . '</option>';
}
$sCode .= '
</select>
</div>
<div class="clear_both"></td>
	</tr>

    <tr>
      <td class="caption">' . _t('_dbcs_DT_PHPBlock_LKey') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text">
          <input type="text" name="phplkey" value="_New PHP Block" class="form_input_text">
          <div class="input_close input_close_text"></div>
        </div>
        <div class="clear_both"></div></td>
	</tr>
    <tr>
      <td class="caption">' . _t('_dbcs_DT_PHPBlock_LText') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text">
          <input type="text" name="phpltext" value="New PHP Block" class="form_input_text">
          <div class="input_close input_close_text"></div>
        </div>
        <div class="clear_both"></div></td>
	</tr>
    <tr>
      <td class="caption">' . _t('_dbcs_DT_PHPBlock_Code') . '</td>
      <td class="value"><div class="input_wrapper input_wrapper_textarea" style="width: 100%">
          <div class="input_border">
            <textarea name="dbcsDTphpcode" class="form_input_textarea" rows="1" cols="20">echo \'Hello World!\';</textarea>
          </div>
          <div class="input_close_textarea left top"></div>
          <div class="input_close_textarea left bottom"></div>
          <div class="input_close_textarea right top"></div>
          <div class="input_close_textarea right bottom"></div>
        </div></td>
	</tr>

    <tr>
      <td class="caption"></td>
      <td class="value"><input type="submit" value="' . _t('_dbcs_DT_PHPBlock_Insert') . '" name="B1"></td>
    </tr>
    
  </table>
</form>
	  </td>
    </tr>
  </tbody>
<!-- ************************************** Section End ************************************** -->

<!-- ************************************** Section Begin ************************************** -->

  <thead class="collapsable' . $peCv . '">
    <tr class="headers">
      <th class="block_header">' . _t('_dbcs_DT_PHPBlock_HE') . '</th>
    </tr>
  </thead>
  <tbody' . $peCs . '>
<tr>
<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section11_Msg') . '</td>
</tr>

    <tr>
      <td class="dbrow">
';
$PHPid = intval($_GET['phpbid']);
$sCode .= '
<form method="POST" action="?r=deanos_tools/administration/&saction=pe&se=pe&phpbid=' . $PHPid . '">
  <table cellspacing="0" cellpadding="0" class="form_advanced_table">
    <tr>
      <td class="caption">' . _t('_dbcs_DT_PHPBlock_PE') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_select_box">
          <select name="phppage" class="form_input_select" onChange="javascript: LoadPHP(this.value)">
          ';
if ($PHPid == 0) {
	$sCode .= '<option selected value="0">Select PHP Block to Edit or Delete</option>';
} else {
	$sCode .= '<option value="0">Select PHP Block to Edit or Delete</option>';
}
$lKeys = $this->_oDb->getPHPBlocks();
foreach ($lKeys as $iID => $dbData) {
	$iID = (int)$dbData['ID'];
	$sDesc = $dbData['Desc'];
	$sPage = $dbData['Page'];
	$sSelected = '';
	if ($PHPid == $iID) {
		$sSelected = 'selected ';
	}
	$sCode .= '<option ' . $sSelected . 'value="' . $iID . '">Page: ' . $sPage . ' - Desc: ' . $sDesc . '</option>';
}
if ($PHPid > 0) {
	$aPHPData = $this->_oDb->getPHPBlockData($PHPid);
}
$sCode .= '
</select>
</div>
<div class="clear_both"></td>
	</tr>

    <tr>
      <td class="caption">' . _t('_dbcs_DT_PHPBlock_LKeyE') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text">
          <input type="text" name="phplkey" value="' . $aPHPData['Caption'] . '" class="form_input_text">
          <div class="input_close input_close_text"></div>
        </div>
        <div class="clear_both"></div></td>
	</tr>
    <tr>
      <td class="caption">' . _t('_dbcs_DT_PHPBlock_CodeE') . '</td>
      <td class="value"><div class="input_wrapper input_wrapper_textarea" style="width: 100%">
          <div class="input_border">
            <textarea name="dbcsDTphpcode" class="form_input_textarea" rows="1" cols="20">' . $aPHPData['Content'] . '</textarea>
          </div>
          <div class="input_close_textarea left top"></div>
          <div class="input_close_textarea left bottom"></div>
          <div class="input_close_textarea right top"></div>
          <div class="input_close_textarea right bottom"></div>
        </div></td>
	</tr>

    <tr>
      <td class="caption"></td>
      <td class="value"><input type="submit" value="' . _t('_dbcs_DT_PHPBlock_SaveE') . '" name="B1">&nbsp;<input type="submit" value="' . _t('_dbcs_DT_PHPBlock_DeleteE') . '" name="B2"></td>
    </tr>
    
  </table>
</form>
	  </td>
    </tr>
  </tbody>
<!-- ************************************** Section End ************************************** -->

<!-- ************************************** Section Begin ************************************** -->

  <thead class="collapsable' . $cacheC . '">
    <tr class="headers">
      <th class="block_header">' . _t('_dbcs_DT_Cache_HE') . '</th>
    </tr>
  </thead>
  <tbody' . $cacheS . '>
<tr>
<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section13_Msg') . '</td>
</tr>

    <tr>
      <td class="dbrow">
';
$sCode .= '
<form method="POST" action="?r=deanos_tools/administration/&saction=dc&se=dc">
  <table cellspacing="0" cellpadding="0" class="form_advanced_table">
    <tr>
      <td class="caption">' . _t('_dbcs_DT_Cache') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_select_box">
	<select multiple="multiple" name="CacheS[]" size="10">
';

$dir = BX_DIRECTORY_PATH_DBCACHE;
$dh  = opendir($dir);
$files = array();
while (false !== ($filename = readdir($dh))) {
   $files[] = $filename;
}
sort($files);
$dirCnt = 0;
foreach ($files as $file)
{
	if ($file != '.' && $file != '..' && $file != '.htaccess') {
		if (is_dir(BX_DIRECTORY_PATH_DBCACHE . $file)) $sCode .= '<option class="folderbg " value="' . $file . '">' . $file . '</option>';
		if (is_file(BX_DIRECTORY_PATH_DBCACHE . $file)) $sCode .= '<option class="filebg " value="' . $file . '">' . $file . '</option>';
		$dirCnt ++;
	}
}
if ($dirCnt == 0) {
		$sCode .= '<option>No files found.</option>';
}
$sCode .= '
	</select>
</div>
<div class="clear_both"></td>
	</tr>
    <tr>
      <td class="caption"></td>
      <td class="value"><input type="submit" value="' . _t('_dbcs_DT_DeleteSelected') . '" name="B1"></td>
    </tr>

    <tr>
      <td class="caption">' . _t('_dbcs_DT_CachePublic') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_select_box">
	<select multiple="multiple" name="CachePublicS[]" size="10">
';
$dir = BX_DIRECTORY_PATH_CACHE_PUBLIC;
$dh  = opendir($dir);
$files = array();
while (false !== ($filename = readdir($dh))) {
   $files[] = $filename;
}
sort($files);
$dirCnt = 0;
foreach ($files as $file)
{
	if ($file != '.' && $file != '..' && $file != '.htaccess') {
		if (is_dir(BX_DIRECTORY_PATH_CACHE_PUBLIC . $file)) $sCode .= '<option class="folderbg " value="' . $file . '">' . $file . '</option>';
		if (is_file(BX_DIRECTORY_PATH_CACHE_PUBLIC . $file)) $sCode .= '<option class="filebg " value="' . $file . '">' . $file . '</option>';
		$dirCnt ++;
	}
}
if ($dirCnt == 0) {
		$sCode .= '<option>No files found.</option>';
}
$sCode .= '
	</select>
</div>
<div class="clear_both"></td>
	</tr>
    <tr>
      <td class="caption"></td>
      <td class="value"><input type="submit" value="' . _t('_dbcs_DT_DeleteSelected') . '" name="B2"></td>
    </tr>

    <tr>
      <td class="caption">' . _t('_dbcs_DT_Tmp') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_select_box">
	<select multiple="multiple" name="TmpS[]" size="10">
';
$dir = BX_DIRECTORY_PATH_ROOT . 'tmp';
$dh  = opendir($dir);
$files = array();
while (false !== ($filename = readdir($dh))) {
   $files[] = $filename;
}
sort($files);
$dirCnt = 0;
foreach ($files as $file)
{
	if ($file != '.' && $file != '..' && $file != '.htaccess') {
		if (is_dir(BX_DIRECTORY_PATH_ROOT . 'tmp/' . $file)) $sCode .= '<option class="folderbg " value="' . $file . '">' . $file . '</option>';
		if (is_file(BX_DIRECTORY_PATH_ROOT . 'tmp/' . $file)) $sCode .= '<option class="filebg " value="' . $file . '">' . $file . '</option>';
		$dirCnt ++;
	}
}
if ($dirCnt == 0) {
		$sCode .= '<option>No files found.</option>';
}
$sCode .= '
	</select>
</div>
<div class="clear_both"></td>
	</tr>

    <tr>
      <td class="caption"></td>
      <td class="value"><input type="submit" value="' . _t('_dbcs_DT_DeleteSelected') . '" name="B3"></td>
    </tr>
    
  </table>
</form>
	  </td>
    </tr>
  </tbody>
<!-- ************************************** Section End ************************************** -->

<!-- ************************************** Section Begin ************************************** -->

  <thead class="collapsable' . $ipC . '">
    <tr class="headers">
      <th class="block_header">' . _t('_dbcs_DT_IP_HE') . '</th>
    </tr>
  </thead>
  <tbody' . $ipS . '>
<tr>
<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section14_Msg') . '</td>
</tr>
<tr>';

$ipStart = intval($_GET['st']);
$ipPerPage = intval($_GET['pp']);

if ($_REQUEST['sg'] == 1) $ipShowGuests = 1;
if ($_REQUEST['sm'] == 1) $ipShowMembers = 1;
$sNickSearch = $_REQUEST['search'];

if($_GET['se'] == '') {
	$ipShowGuests = 1;
	$ipShowMembers = 1;
}

if($ipShowGuests == 1) $ipShowGuestsC = ' checked';
if($ipShowMembers == 1) $ipShowMembersC = ' checked';


$sCode .= '

	<td style="white-space: normal; padding: 6px;">
		<form method="POST" action="?r=deanos_tools/administration/&se=ip">
		<div style="float:left; margin-right:4px; padding-top: 6px;">' . _t('_dbcs_DT_ShowMembers') . '</div>
		<div style="float:left; padding-top:4px;"><input' . $ipShowMembersC . ' name="sm" type="checkbox" value="1"></div>
		<div style="float:left; margin-right:8px; padding-top:4px;">&nbsp;</div>
		<div style="float:left; margin-right:4px; padding-top: 6px;">' . _t('_dbcs_DT_ShowGuests') . '</div>
		<div style="float:left; padding-top:4px;"><input' . $ipShowGuestsC . ' name="sg" type="checkbox" value="1"></div>
		<div style="float:left; margin-right:8px; padding-top:4px;">&nbsp;</div>
<div style="float:left; margin-right: 4px; padding-top: 6px;">' . _t('_dbcs_DT_SearchNick') . '</div>
<div class="input_wrapper input_wrapper_text" style="margin-right: 6px; margin-top: 1px; width:150px">
	<input class="form_input_text" name="search" type="text" value="' . $sNickSearch . '">
	<div class="input_close input_close_text">
	</div>
</div>
<div style="float:left; margin-right:8px">&nbsp;</div>
<div style="float:left;"><input name="ipsubmit" type="submit" value="' . _t('_dbcs_DT_Submit') . '" /></div>

		</form>
	</td>
</tr>

<tr>
';



if($ipPerPage == 0) $ipPerPage = getParam('dbcs_DT_alpp');
$ipCount = $this->_oDb->getIPCount($ipShowGuests, $ipShowMembers, $sNickSearch);

if ($ipCount > 0) {
	$sCode .= '
	<td>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr class="db1">
			<td nowrap><b>' . _t('_dbcs_DT_Member ID') . '</b></td>
			<td nowrap><b>' . _t('_dbcs_DT_Nickname') . '</b></td>
			<td nowrap><b>' . _t('_dbcs_DT_IP Address') . '</b></td>
			<td nowrap><b>' . _t('_dbcs_DT_Logon Time') . '</b></td>
		</tr>
	';
	$aIP = $this->_oDb->getIPList($ipStart, $ipPerPage, $ipShowGuests, $ipShowMembers, $sNickSearch);
	foreach ($aIP as $iID => $sData) {
		$sCode .= '<tr>';
		$sCode .= '	<td>' . $sData['member_id'] . '</td>';
		if ($sData['member_id'] > 0) {
			$sCode .= ' <td><a href="' . BX_DOL_URL_ROOT . $sData['nick_name'] . '">' . $sData['nick_name'] . '</a></td>';
		} else {
			$sCode .= ' <td>' . $sData['nick_name'] . '</td>';
		}
		$sCode .= '	<td><a href="http://www.dnsstuff.com/tools/whois/?tool_id=66&token=&toolhandler_redirect=0&ip=' . $sData['ip_address'] . '">' . $sData['ip_address'] . '</a></td>';
		$sCode .= '	<td>' . date("F j, Y, g:i a", $sData['time_stamp']) . '</td>';
		$sCode .= '</tr>';
	}

	$sIPPageURL = '?r=deanos_tools/administration/&search=' . $sNickSearch . '&se=ip&sg=' . $ipShowGuests . '&sm=' . $ipShowMembers . '&st={start}&pp={per_page}';
	$oPaginate = new BxDolPaginate(array(
        'start' => $ipStart,
        'count' => $ipCount,
        'per_page' => $ipPerPage,
        'page_url' => $sIPPageURL,
        'on_change_page' => 'aa'
    ));
    $sPaginate = $oPaginate->getPaginate();    

	$sCode .= '<tr><td colspan="4">' . $sPaginate . '</td></tr>';

	$sCode .= '
	</table>
	</td>
	';
} else {
	$sCode .= '<td>' . MsgBox(_t('_Empty')) . '</td>';
}
$sCode .= '
</tr> 
</tbody>
<!-- ************************************** Section End ************************************** -->

<!-- ************************************** Section Begin ************************************** -->

  <thead class="collapsable' . $otCv . '">
    <tr class="headers">
      <th class="block_header">' . _t('_dbcs_DT_OtherBlock_HE') . '</th>
    </tr>
  </thead>
  <tbody' . $otCs . '>
<tr>
<td style="white-space:normal;padding: 6px;">' . _t('_dbcs_DT_Section12_Msg') . '</td>
</tr>

    <tr>
      <td class="dbrow">
';
$sCode .= '

<style>
.mytable td {
	white-space:normal;
	border-bottom: none;
	vertical-align: middle;
	padding: 0px;
}
.mytable tr {
	white-space: normal;
	border-bottom: none;
}
</style>
<div style="font-size: 18px; font-weight: bold; padding-bottom: 10px;"><a href="http://www.boonex.com/market/posts/deano92964">More of my Modules</a></div>
<!-- <div style="height:236px; overflow-y: scroll;"> -->
<table class="mytable" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width: 100px; padding-right: 6px;">
	<a href="http://www.boonex.com/m/dolphin-anti-spam">
	<img src="http://www.boonex.com/modules/Market/data/images/d/de/deano92964/rl/408980.jpg" width="100" height="100"></a></td>
    <td><table border="0">
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Name:</b></td>
          <td><a href="http://www.boonex.com/m/dolphin-anti-spam">Dolphin Anti 
			Spam</a></td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Version:</b></td>
          <td>1.0.2</td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; vertical-align: top; padding-right: 4px;"><b>Description:</b></td>
          <td>Are you tired of all the spam signups on your site? Want to remove 
			the captcha from the join form but are afraid it will increase spam 
			accounts? Well then this is the solution. Dolphin Anti Spam. This 
			product is a join filtering system. Blocks signups using the 
			following criteria.</td>
        </tr>
      </table></td>
  </tr>
</table>
<div style="height:18px;"></div>
<table class="mytable" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width: 100px; padding-right: 6px;"><a href="http://www.boonex.com/m/Deanos_Facebook_Connect_Version_2_0"><img src="http://www.boonex.com/modules/Market/data/images/d/de/deano92964/lg/921979.jpg" width="100" height="100"></a></td>
    <td><table border="0">
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Name:</b></td>
          <td><a href="http://www.boonex.com/m/Deanos_Facebook_Connect_Version_2_0">Deanos Facebook Connect</a></td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Version:</b></td>
          <td>4.1.2</td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; vertical-align: top; padding-right: 4px;"><b>Description:</b></td>
          <td>This module was developed to replace the Boonex version of 
            Facebook connect. I maintain this version throughout all versions of 
            dolphin and any changes Facebook makes so a working version is 
            always available. Far more reliable than the boonex version, and has 
            more features. </td>
        </tr>
      </table></td>
  </tr>
</table>
<div style="height:18px;"></div>
<table class="mytable" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width: 100px; padding-right: 6px;"><a href="http://www.boonex.com/m/deanos-google-connect">
	<img src="http://www.boonex.com/modules/Market/data/images/d/de/deano92964/rl/307337.jpg" width="100" height="100"></a></td>
    <td><table border="0">
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Name:</b></td>
          <td><a href="http://www.boonex.com/m/deanos-google-connect">Deanos Google Connect</a></td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Version:</b></td>
          <td>1.0.2</td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; vertical-align: top; padding-right: 4px;"><b>Description:</b></td>
          <td>This module allows your members to quickly signup to connect to your site using their Google account information. Similar to Facebook connect, but members can connect using their google information instead.</td>
        </tr>
      </table></td>
  </tr>
</table>
<div style="height:18px;"></div>
<table class="mytable" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width: 100px; padding-right: 6px;"><a href="http://www.boonex.com/m/Online_Members_for_Member_Menu"><img src="http://www.boonex.com/modules/Market/data/images/d/de/deano92964/rl/868943.jpg" width="100" height="100"></a></td>
    <td><table border="0">
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Name:</b></td>
          <td><a href="http://www.boonex.com/m/Online_Members_for_Member_Menu">Online Members For Member Menu</a></td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Version:</b></td>
          <td>2.0.4</td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; vertical-align: top; padding-right: 4px;"><b>Description:</b></td>
          <td>This mod is an add-on to the dolphin member menu. Shows a pop up list of members currently online. Members shown can be filtered by Gender and age range. Couple option can be disabled in the settings. </td>
        </tr>
      </table></td>
  </tr>
</table>
<div style="height:18px;"></div>
<table class="mytable" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width: 100px; padding-right: 6px;"><a href="http://www.boonex.com/m/Admin_Announcements"><img src="http://www.boonex.com/modules/Market/data/images/d/de/deano92964/lg/288151.jpg" width="100" height="100"></a></td>
    <td><table border="0">
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Name:</b></td>
          <td><a href="http://www.boonex.com/m/Admin_Announcements">Admin Announcements</a></td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Version:</b></td>
          <td>1.0.4</td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; vertical-align: top; padding-right: 4px;"><b>Description:</b></td>
          <td>Admin Announcements is a new module to provide a single announcement   display targeted to specific ages or membership levels. One announcement   at a time is displayed, and when skipped or marked as read, the next is   displayed. When no more announcements are available, the box simply   disapears from the page. Admin Announcements is intended as a alternative to other methods used in dolphin to display notices to your members.</td>
        </tr>
      </table></td>
  </tr>
</table>
<div style="height:18px;"></div>
<table class="mytable" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width: 100px; padding-right: 6px;"><a href="http://www.boonex.com/m/Head_Injections"><img src="http://www.boonex.com/modules/Market/data/images/d/de/deano92964/lg/267972.jpg" width="100" height="100"></a></td>
    <td><table border="0">
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Name:</b></td>
          <td><a href="http://www.boonex.com/m/Head_Injections">Head Injections</a></td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; padding-right: 4px;"><b>Product Version:</b></td>
          <td>2.0.1</td>
        </tr>
        <tr>
          <td style="white-space: nowrap; text-align: right; vertical-align: top; padding-right: 4px;"><b>Description:</b></td>
          <td>The Per Page Head Injections mod allows you to inject additional code   such as meta tags, script and style tags into the head section of any   dolphin page. Pages are matched by their current title. The title is currently the only tag within 
            the head that can be overridden.</td>
        </tr>
      </table></td>
  </tr>
</table>
</div>
<div style="padding-top: 10px; font-weight: bold;">Help support the continued development of my free modules by purchasing some of my others.</div>



	  </td>
    </tr>
  </tbody>
<!-- ************************************** Section End ************************************** -->



</table>
</div>
';

            bx_import('BxDolPageView');
	        $sActions = BxDolPageView::getBlockCaptionMenu(mktime(), array(
	            'add_unit' => array('href' => $sAction, 'title' => _t('_dbcs_DT_Refresh Page'), 'onclick' => '', 'active' => 0),
	        ));
            return DesignBoxContent($sExistedC, $sCss . $sJs . $sCode, 1, $sActions);
            
        }

function delete_directory($dirname) {
   if (is_dir($dirname))
      $dir_handle = opendir($dirname);
   if (!$dir_handle)
      return false;
   while($file = readdir($dir_handle)) {
      if ($file != "." && $file != "..") {
         if (!is_dir($dirname."/".$file))
            unlink($dirname."/".$file);
         else
            $this->delete_directory($dirname.'/'.$file);    
      }
    }
   closedir($dir_handle);
   rmdir($dirname);
   return true;
}
 

function dbcsClearCache($sSection) {
	switch($sSection) {
		case 'sys_options':
			// Clear for dolphin 7.0.3
			$files = glob(BX_DIRECTORY_PATH_CACHE . 'sys_options_*.php');
			array_map('unlink', $files);
			// Clear for dolphin versions below 7.0.3
			$sFileName = BX_DIRECTORY_PATH_CACHE . 'sys_options.php';
			if (file_exists($sFileName)) unlink($sFileName);
			break;
		case 'sys_page_compose':
			// Clear for dolphin 7.0.3
			$files = glob(BX_DIRECTORY_PATH_CACHE . 'db_sys_page_compose*.php');
			array_map('unlink', $files);
			// Clear for dolphin versions below 7.0.3
			$sFileName = BX_DIRECTORY_PATH_CACHE . 'sys_page_compose.inc';
			if (file_exists($sFileName)) unlink($sFileName);
			break;
		case 'sys_alerts':
			// Clear for dolphin 7.0.3
			$files = glob(BX_DIRECTORY_PATH_CACHE . 'db_sys_alerts*.php');
			array_map('unlink', $files);
			// Clear for dolphin versions below 7.0.3
			$sFileName = BX_DIRECTORY_PATH_CACHE . 'sys_alerts.inc';
			if (file_exists($sFileName)) unlink($sFileName);
			break;
		case 'users':
			$files = glob(BX_DIRECTORY_PATH_CACHE . 'user*.php');
			array_map('unlink', $files);
			break;

	}
}


    }
?>