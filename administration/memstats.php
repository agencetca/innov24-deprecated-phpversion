<?php
/***************************************************************************
* Date				: Jun 06, 2011
* Copywrite			: (c) 2011 by kazatzo
*
* Product Name		: Member Statistics
* Product Version	: 1.0
*
* IMPORTANT: This is a commercial product made by kazatzo
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from kazatzo
*
***************************************************************************/

require_once( '../inc/header.inc.php' );

$GLOBALS['iAdminPage'] = 1;

require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

define('BX_DOL_ADM_MP_CTL', 'qlinks');
define('BX_DOL_ADM_MP_JS_NAME', 'oMP');
define('BX_DOL_ADM_MP_PER_PAGE', 32);
define('BX_DOL_ADM_MP_PER_PAGE_STEP', 16);

$logged['admin'] = member_auth( 1, true, true );

$sCtlType = isset($_POST['adm-mp-members-ctl-type']) && in_array($_POST['adm-mp-members-ctl-type'], array('qlinks', 'browse', 'calendar', 'tags', 'search')) ? $_POST['adm-mp-members-ctl-type'] : BX_DOL_ADM_MP_CTL;
if (isset($_POST['action']) && $_POST['action'] == 'get_stats') 
{
    $aParams = array();
    if (is_array($_POST['ctl_value']))
        foreach ($_POST['ctl_value'] as $sValue) 
		{
            $aValue = explode('=', $sValue);
            $aParams[$aValue[0]] = $aValue[1];
        }
    $oJson = new Services_JSON();
    echo $oJson->encode(array('code' => 0, 'content' => getStats(array(
        'view_type' => $_POST['view_type'], 
        'view_start' => (int)$_POST['view_start'],
        'view_per_page' => (int)$_POST['view_per_page'],
        'view_order' => $_POST['view_order'],
        'ctl_type' => $_POST['ctl_type'],
        'ctl_params' => $aParams, 
    ))));
    exit;
}

$iNameIndex = 50;
$_page = array(
    'name_index' => $iNameIndex,
    'css_name' => array('forms_adv.css', 'profiles.css', 'memstats.css'),
    'js_name' => array('memstats.js'),
    'header' => _t('_member_statistics_manage_members')
);
$_page_cont[$iNameIndex] = array(
	'page_code_stats' => statsPageCode($sCtlType, $aParams),
	'page_code_settings_stats' => statsPageCodeSettings(),
    'obj_name' => BX_DOL_ADM_MP_JS_NAME,
    'actions_url' => $GLOBALS['site']['url_admin'] . 'memstats.php',
    'sel_control' => $sCtlType,
    'per_page' => BX_DOL_ADM_MP_PER_PAGE,
    'order_by' => ''
);
PageCodeAdmin();

function statsPageCode($sDefaultCtl = BX_DOL_ADM_MP_CTL, $aParams) 
{ 
	global $iInterval, $sStartStats, $sEndStats;
	setStatValues($aParams);
    $oPaginate = new BxDolPaginate(array(
        'per_page' => BX_DOL_ADM_MP_PER_PAGE,
        'per_page_step' => BX_DOL_ADM_MP_PER_PAGE_STEP,
        'on_change_per_page' => BX_DOL_ADM_MP_JS_NAME . '.changePerPage(this, '.$iInterval.', \''.$sStartStats.'\', \''.$sEndStats.'\');', 
    ));    
	
    $aResult = array(
    	'action_url' => $GLOBALS['site']['url_admin'] . 'memstats.php',
        'view_type' => 'geeky',
		'change_order' => BX_DOL_ADM_MP_JS_NAME . '.changeOrder(this, '.$iInterval.',  \''.$sStartStats.'\', \''.$sEndStats.'\');', 
        'per_page' => $oPaginate->getPages(),
        'loading' => LoadingBox('adm-mp-stats-loading'), 
    );
    $aResult = array_merge($aResult, array('style_geeky' => '', 'content_geeky' => getStats(array())));
	$aResult = array_merge($aResult, array('image_graph' => getImageGraph()));

    return DesignBoxAdmin(_t('_member_statistics_stats'), $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_stats.html', $aResult));
}

function getStats ($aParams) 
{
	global $sStartStats, $sEndStats, $iInterval;
    if (!isset($aParams['view_start']) || empty($aParams['view_start']))
        $aParams['view_start'] = 0;
    if (!isset($aParams['view_per_page']) || empty($aParams['view_per_page']))
        $aParams['view_per_page'] = BX_DOL_ADM_MP_PER_PAGE;

	$aParams['view_order_way'] = 'DESC';
    if (!isset($aParams['view_order']) || empty($aParams['view_order']))
        $aParams['view_order'] = 'dateRange';
	else 
	{
		$aOrder = explode(' ', $aParams['view_order']);
		if (count($aOrder) > 1) {
			$aParams['view_order'] = $aOrder[0];
			$aParams['view_order_way'] = $aOrder[1]; 
		}
	}
    $sQuery = "SELECT MIN(`tp`.`DateReg`) AS `start_date`, MAX(`tp`.`DateReg`) AS `end_date` FROM `Profiles` AS `tp`";
    $aDate = db_arr($sQuery);
	$sStartDate = str_replace(' ', '-', $aDate['start_date']);
	list($iYearStart, $iMonthStart, $iDayStart, $sHourStart) = explode('-', $sStartDate);
	// set start date
	if (isset($_POST['start_stats']))
	{
		$sUserStartDate = $_POST['start_stats'];
		$sUserStartDate = "{$sUserStartDate} 00:00:00";
		// if start date is greater than the registration date of the first member 
		if (strtotime($sUserStartDate) > strtotime("{$iYearStart}-{$iMonthStart}-{$iDayStart} 00:00:00"))
		{
			$aToday = getDate();
			$iYear = $aToday['year'];
			$iMonth = $aToday['mon'];
			$iDay = $aToday['mday'];
			// make sure that start date will not be greater than today
			if (strtotime($_POST['start_stats']) <  strtotime("{$iYear}-{$iMonth}-{$iDay}"))
			{
				$sUserStartDate = str_replace(' ', '-', $sUserStartDate);
				list($iYearStart, $iMonthStart, $iDayStart, $sHourStart) = explode('-', $sUserStartDate);	
			}
		}
	}
	else if (isset($aParams['ctl_params']['start_stats']))
	{
		$sUserStartDate = $aParams['ctl_params']['start_stats'];
		$sUserStartDate = "{$sUserStartDate} 00:00:00";
		$sUserStartDate = str_replace(' ', '-', $sUserStartDate);
		list($iYearStart, $iMonthStart, $iDayStart, $sHourStart) = explode('-', $sUserStartDate);	
	}
	else
	{
		list($iYearStart, $iMonthStart, $iDayStart, $sHourStart) = explode('-', '2010-01-01');	
	}
	$sStartStats = "{$iYearStart}-{$iMonthStart}-{$iDayStart}";
	//set end date
	if (isset($_POST['end_stats']))
	{
		$aToday = getDate();
		$iYear = $aToday['year'];
		$iMonth = $aToday['mon'];
		$iDay = $aToday['mday'];
		// end day will never be a day after today
		if (strtotime($_POST['end_stats']) > strtotime("{$iYear}-{$iMonth}-{$iDay}"))
		{
			$aDate['end_date'] = reformDate($iYear, $iMonth, $iDay);
		}
		else if (strtotime($_POST['end_stats']) < strtotime($sStartStats))
		{
			$aDate['end_date'] = reformDate($iYear, $iMonth, $iDay);
		}
		else
			$aDate['end_date'] = $_POST['end_stats'];
	}
	else if (isset($aParams['ctl_params']['end_stats']))
	{
		$aDate['end_date'] = $aParams['ctl_params']['end_stats'];
	}
	else
	{
		$aToday = getDate();
		$iYear = $aToday['year'];
		$iMonth = $aToday['mon'];
		$iDay = $aToday['mday'];
		$aDate['end_date'] = reformDate($iYear,$iMonth,$iDay);
	}
	$sEndDate = str_replace(' ', '-', $aDate['end_date']);
	list($iYearEnd, $iMonthEnd, $iDayEnd, $sHourEnd) = explode('-', $sEndDate);	
	$sFinishDate = "{$iYearEnd}-{$iMonthEnd}-{$iDayEnd}";
	$sEndStats = $sFinishDate;
	$sFinishDate = addDate($sFinishDate, 1, 0, 0);
	$sFinishDate = "{$sFinishDate} 00:00:00";
	$sEachDate = "{$iYearStart}-{$iMonthStart}-{$iDayStart} 00:00:00";
	$iCount=0;
	if (isset($_POST['interval']))
		$iInterval = (int)$_POST['interval'];
	else if (isset($aParams['ctl_params']['interval']))
		$iInterval = (int)$aParams['ctl_params']['interval'];
	else
		$iInterval = 96;	
	while (strtotime($sEachDate)<= strtotime($sFinishDate))
	{
		$iCount++;
		if ($iCount == 1)
		{
			$sSqlQuery = "SELECT members, registered, dateRange from ((SELECT COUNT(*) AS members, 
			0 AS registered, '{$sEachDate}' AS dateRange 
			FROM Profiles WHERE DateReg < '{$sEachDate}' AND Status = 'Active') ";
		}
		else if ($iCount>1)
		{
			$sSqlQuery = $sSqlQuery . "UNION (SELECT COUNT(*) AS members, 
			(SELECT COUNT(*) FROM Profiles WHERE DateReg < '{$sEachDate}' AND DateReg >= '{$sPreviousDate}' AND Status = 'Active') AS registered, 
			'{$sEachDate}' AS dateRange FROM Profiles WHERE DateReg < '{$sEachDate}' AND Status = 'Active')";
		}
		$sPreviousDate = $sEachDate;
		list($sDate, $sHour) = explode(' ', $sEachDate);	
		if ($iInterval == 1)
		{
			$sDate = addDate($sDate, 0, 1, 0);
			$sEachDate = "{$sDate} 00:00:00";
		}
		else if ($iInterval == 12)
		{
			list($iHours, $iMinutes, $iSeconds) = explode( ':', $sHour);
			$iHours =  (int)$iHours + $iInterval;
			if ($iHours%24 == 0)
			{
				$iHours = 0;
				$sDate = addDate($sDate, 1, 0, 0);
			}
			if ($iHours==0)
				$sEachDate = "{$sDate} 00:00:00";
			else
				$sEachDate = "{$sDate} {$iHours}:00:00";
		}
		else if ($iInterval>=24)
		{
			$iDays =  $iInterval/24;
			$sDate = addDate($sDate, $iDays, 0, 0);
			$sEachDate = "{$sDate} 00:00:00";
		}
	}
	$sQuery = $sSqlQuery . ") sq ORDER BY sq.`dateRange` ASC";
	$sSqlQuery = $sSqlQuery . ") sq
		ORDER BY sq.`" . $aParams['view_order'] . "` " . $aParams['view_order_way'] . "
    	LIMIT " . $aParams['view_start'] . ", " . $aParams['view_per_page'];
    //--- Get Paginate ---//
    $oPaginate = new BxDolPaginate(array(
        'start' => $aParams['view_start'],
		'count' => $iCount,
        'per_page' => $aParams['view_per_page'],
        'page_url' => $GLOBALS['site']['url_admin'] . 'memstats.php?start={start}',
        'on_change_page' => BX_DOL_ADM_MP_JS_NAME . '.changePage({start}, '.$iInterval.',  \''.$sStartStats.'\', \''.$sEndStats.'\');', 
    ));
    $sPaginate = $oPaginate->getPaginate();    

    $aProfiles = $GLOBALS['MySQL']->getAll($sSqlQuery);
    $sFunction = 'getStatsGeeky';
    return $sFunction($aProfiles, $sPaginate, $sQuery);
}

function getStatsGeeky($aProfiles, $sPaginate, $sQuery) 
{
	global $iStatsVal;
    $iEmailLength = 20; 
    $aItems = array();
	$aYValues = array();
	$aXValues = array();
	$i=0;
    foreach ($aProfiles as $aProfile)
	{
		$aItems[$aProfile['dateRange']] = array(
			'members' => $aProfile['members'],
			'date' => $aProfile['dateRange'],
			'registered' => $aProfile['registered'],
		);
		$i++;
    }
	$i=0;
	$aStats = $GLOBALS['MySQL']->getAll($sQuery);
	if (isset($_POST['stats_type']))
		$iStatsVal = (int)$_POST['stats_type'];
	else
		$iStatsVal = 1;	
    foreach ($aStats as $aStat)
	{
		if ($iStatsVal == 1)
			$aYValues[$i]=$aStat['members'];
		else
		{
			$aYValues[$i]=$aStat['registered'];
		}
		$aXValues[$i] = $aStat['dateRange'];
		$i++;
    }
	createGraph($aXValues,$aYValues, $iStatsVal);

    return $GLOBALS['oAdmTemplate']->parseHtmlByName('mp_stats_geeky.html', array(
        'bx_repeat:items' => array_values($aItems),
        'paginate' => $sPaginate
    ));
}

function addDate($sGivenDate,$iDay=0,$iMth=0,$iYr=0) 
{
    $iCd = strtotime($sGivenDate);
    $sNewDate = date('Y-m-d', mktime(0,0,0,date('m',$iCd)+$iMth, date('d',$iCd)+$iDay, date('Y',$iCd)+$iYr));
    return $sNewDate;
}

function getImageGraph() 
{
	global $dir, $site;

    $sFileName =  mktime() . '.png';
	$sQuery = "SELECT `image_name` FROM `mem_stats_image` WHERE `image_id` = 1";
	$aName = db_arr($sQuery);
	$sFileName =  $aName['image_name'].'.png';

	return '<img src="'. $site['mediaImages'] . $sFileName .'"/>';
}

function createGraph($aValuesX, $aValues, $iStatsVal)
{
	header("Content-type: image/png");
	// Define variables
	$iImgWidth=720;
	$iImgHeight=400;
	$iGrid=100;
	$fGraphSpacing=0.0;
	$iMax=0;
	$iOffset = 20;
	$sFontFile = BX_DIRECTORY_PATH_ROOT . "simg/verdana.ttf";

	// Create image and define colors
	$rImage=imagecreate($iImgWidth + $iOffset, $iImgHeight + $iOffset);
	$colorWhite=imagecolorallocate($rImage, 255, 255, 255);
	$iColorGrey=imagecolorallocate($rImage, 192, 192, 192);
	if ($iStatsVal == 1)
	{
		$iColorGraph=imagecolorallocate($rImage, 0, 119, 204);
		$iColorLightGraph = imagecolorallocate($rImage,230, 242, 250);
	}
	else
	{
		$iColorGraph=imagecolorallocate($rImage, 218, 117, 9);
		$iColorLightGraph = imagecolorallocate($rImage,253, 200, 146);
	}
	$iColorFontGrey = imagecolorallocate($rImage,102, 102, 102);
	
	// check if values are empty (do not draw graph for one value or less)
	if (count($aValues)>1)
	{
		//Creation of new array with height adjusted values
		while (list($iKey, $iVal) = each($aValues))
		{
			if ($iVal>$iMax)
			{
				$iMax=$iVal;
			}
		}

		for ($i=0; $i<count($aValues); $i++)
		{
			if ($iMax == 0) // to avoid division by zero errors if all values are zero
				$aGraphValues[$i] = 0;
			else
				$aGraphValues[$i] = $aValues[$i] * ((($iImgHeight-$iGrid)*(1-$fGraphSpacing))/$iMax);
		}
		$iGridY = round(($iMax/(1-$fGraphSpacing))/3);
		
		// Create line graph
		if ($iImgWidth/$iGrid>count($aGraphValues))
		{
			$iSpace=$iGrid;
		}
		else
		{
			$iSpace = $iImgWidth/(count($aGraphValues)-1);
		}
		
		$iIndex=0;
		while(($iIndex*6)<=count($aGraphValues))
		{
			$iIndex++;
		}
		$iIndex--;
		// to avoid division errors
		if ($iIndex<=0) $iIndex=1;
		
		// Create grid
		for ($i=1; $i<($iImgHeight/$iGrid); $i++)
			imageline($rImage, 0 + $iOffset, $i*$iGrid, $iImgWidth + $iOffset, $i*$iGrid, $iColorGrey);
		for ($i=1; $i<count($aGraphValues) -1; $i++)
		{
			if (($i % $iIndex)==0)
			{
				imageline($rImage, $i*$iSpace + $iOffset, 0, $i*$iSpace + $iOffset, $iImgHeight, $iColorGrey);	
			}
		}
		$iPointSet = 0;
		for ($i=0; $i<count($aGraphValues)-1; $i++)
		{
			imageLineThick($rImage, $i*$iSpace + $iOffset, ($iImgHeight-$aGraphValues[$i]), ($i+1)*$iSpace + $iOffset, ($iImgHeight-$aGraphValues[$i+1]), $iColorGraph, 3.5);
			if ($iImgHeight-$aGraphValues[$i] < $iImgHeight - 50 && $iPointSet == 0 && $i>0)
			{
				$iPointSet = 1;
				$iPointX = $i*$iSpace + $iOffset;
				$iPointY = $iImgHeight-8;
			}
			// check if graph will be complete (no color fill will be applied)
			if ((count($aGraphValues)-1)*$iSpace != $iImgWidth)
			{
				$iPointSet = 0;
			}
		}
		// create border to define fill
		imageline($rImage, 0 + $iOffset, 0, 0 + $iOffset, $iImgHeight, $iColorGraph);
		imageline($rImage, $iImgWidth-1 + $iOffset, 0, $iImgWidth-1 + $iOffset, $iImgHeight-1, $iColorGraph);
		imageline($rImage, 0 + $iOffset, $iImgHeight-1, $iImgWidth-1 + $iOffset, $iImgHeight-1, $iColorGraph);
		if ($iPointSet==1 && $iStatsVal == 1)
			imagefilltoborder($rImage, $iPointX, $iPointY, $iColorGraph, $iColorLightGraph);
		
		for ($i=1; $i<count($aGraphValues) - 1; $i++)
		{
			if (($i % $iIndex)==0 && ($iImgWidth - ($i*$iSpace + $iOffset - 20))>=50)
			{
				if (!function_exists('imagettftext'))
				{
					imagestring($rImage, 2,  $i*$iSpace + $iOffset - 10, $iImgHeight - 15, $aValuesX[$i], $iColorGraph);
				}
				else
				{
					list($sDate, $sHour) = explode( ' ', $aValuesX[$i]);
					imagettftext($rImage, 6, 0, $i*$iSpace + $iOffset - 20, $iImgHeight - 5, $iColorGraph, $sFontFile, $sDate);
					list($sHours, $sMinutes, $sSeconds) = explode(':', $sHour);
					imagettftext($rImage, 6, 0, $i*$iSpace + $iOffset - 10, $iImgHeight + 8, $iColorGraph, $sFontFile, $sHours.':'.$sMinutes);
				}
			}
			else if ($i == count($aGraphValues) - 2)
			{
				if (!function_exists('imagettftext'))
				{
					imagestring($rImage, 2,  ($i+1)*$iSpace + $iOffset - 20, $iImgHeight - 15, $aValuesX[$i+1], $iColorGraph);
				}
				else
				{
					list($sDate, $sHour) = explode( ' ',  $aValuesX[$i+1]);
					imagettftext($rImage, 6, 0, ($i+1)*$iSpace + $iOffset - 50, $iImgHeight - 5, $iColorGraph, $sFontFile, $sDate);
					list($sHours, $sMinutes, $sSeconds) = explode(':', $sHour);
					imagettftext($rImage, 6, 0, ($i+1)*$iSpace + $iOffset - 25, $iImgHeight + 8, $iColorGraph, $sFontFile, $sHours.':'.$sMinutes);
				}
			}
			if ($i==1)
			{
				if (!function_exists('imagettftext'))
				{
					imagestring($rImage, 2, $iOffset - 10, $iImgHeight - 15, $aValuesX[$i-1], $iColorGraph);
				}
				else
				{
					list($sDate, $sHour) = explode( ' ',  $aValuesX[$i-1]);
					imagettftext($rImage, 6, 0, $iOffset - 20, $iImgHeight - 5, $iColorGraph, $sFontFile, $sDate);
					list($sHours, $sMinutes, $sSeconds) = explode(':', $sHour);
					imagettftext($rImage, 6, 0, $iOffset - 10, $iImgHeight + 8, $iColorGraph, $sFontFile, $sHours.':'.$sMinutes);
				}
			}
		}	
		
		// Create border around image
		imageline($rImage, 0 + $iOffset, 0, 0 + $iOffset, $iImgHeight, $iColorGrey);
		imageline($rImage, 0 + $iOffset, 0, $iImgWidth + $iOffset, 0, $iColorGrey);
		imageline($rImage, $iImgWidth-1 + $iOffset, 0, $iImgWidth-1 + $iOffset, $iImgHeight-1, $iColorGrey);
		imageline($rImage, 0 + $iOffset, $iImgHeight-1, $iImgWidth-1 + $iOffset, $iImgHeight-1, $iColorGrey);

		// write text on image
		if (!function_exists('imagettftext'))
		{
			imagestring($rImage, 3,  45, 0, _t("_member_statistics_members"), $iColorFontGrey);
			imagestring($rImage, 3, $iImgWidth - 15 , $iImgHeight + 5,  _t("_member_statistics_time"), $iColorFontGrey);
			if ($iMax !=0)
			{
				imagestring($rImage, 2, 4, $iGrid, $iMax, $iColorFontGrey );
			}
			if ($iGridY !=0)
			{
				imagestring($rImage, 2, 4, 0, $iGridY *4, $iColorFontGrey );
				imagestring($rImage, 2, 4, 2*$iGrid, $iGridY *2, $iColorFontGrey );
				imagestring($rImage, 2, 4, 3*$iGrid, $iGridY *1, $iColorFontGrey );	
			}
			imagestring($rImage, 2, 4, 4*$iGrid, 0, $iColorFontGrey );	
		}
		else
		{
			imagettftext($rImage, 11, 0, 45, 15 , $iColorFontGrey, $sFontFile, _t("_member_statistics_members") );
			imagettftext($rImage, 11, 0, $iImgWidth - 25 , $iImgHeight + 15, $iColorFontGrey, $sFontFile, _t("_member_statistics_time"));
			if ($iMax !=0)
			{
				imagettftext($rImage, 10, 0, 4, 15 + $iGrid, $iColorFontGrey, $sFontFile, $iMax );
			}
			if ($iGridY !=0)
			{
				imagettftext($rImage, 10, 0, 4, 15 + 2*$iGrid, $iColorFontGrey, $sFontFile, $iGridY *2 );
				imagettftext($rImage, 10, 0, 4, 15 + 3*$iGrid, $iColorFontGrey, $sFontFile, $iGridY *1 );
				imagettftext($rImage, 10, 0, 4, 15, $iColorFontGrey, $sFontFile, $iGridY *4 );
			}
			imagettftext($rImage, 10, 0, 4, 4*$iGrid - 15, $iColorFontGrey, $sFontFile, 0);
		}
	}
	else
	{ // no data will be shown
		// write text on image
		if (!function_exists('imagettftext'))
		{
			imagestring($rImage, 3,  45, 0, _t("_member_statistics_members"), $iColorFontGrey);
			imagestring($rImage, 3, $iImgWidth - 15 , $iImgHeight + 5, _t("_member_statistics_time"), $iColorFontGrey);
			imagestring($rImage, 5, 320, 170, _t("_member_statistics_no_data"), $iColorFontGrey);
		}
		else
		{
			imagettftext($rImage, 11, 0, 45, 15 , $iColorFontGrey, $sFontFile, _t("_member_statistics_members") );
			imagettftext($rImage, 11, 0, $iImgWidth - 25 , $iImgHeight + 15, $iColorFontGrey, $sFontFile, _t("_member_statistics_time"));
			imagettftext($rImage, 44, 0, 260, 210, $iColorFontGrey, $sFontFile, _t("_member_statistics_no_data"));
		}
		// Create border around image
		imageline($rImage, 0 + $iOffset, 0, 0 + $iOffset, $iImgHeight, $iColorGrey);
		imageline($rImage, 0 + $iOffset, 0, $iImgWidth + $iOffset, 0, $iColorGrey);
		imageline($rImage, $iImgWidth-1 + $iOffset, 0, $iImgWidth-1 + $iOffset, $iImgHeight-1, $iColorGrey);
		imageline($rImage, 0 + $iOffset, $iImgHeight-1, $iImgWidth-1 + $iOffset, $iImgHeight-1, $iColorGrey);
		// Create grid
		for ($i=1; $i<($iImgHeight/$iGrid); $i++)
			imageline($rImage, 0 + $iOffset, $i*$iGrid, $iImgWidth + $iOffset, $i*$iGrid, $iColorGrey);
	}
	// Output graph and clear image from memory
	$sFileName = mktime();
	$sQuery = "SELECT `image_name` FROM `mem_stats_image` WHERE `image_id` = 1";
	$sEmpty = "DELETE FROM `mem_stats_image`";
	$aName = db_arr($sQuery);
	// delete old image graph
	@unlink(BX_DIRECTORY_PATH_ROOT. 'media/images/'  . $aName['image_name'] . '.png');
	$rDelete = mysql_query($sEmpty);
	$sQuery = "INSERT INTO `mem_stats_image` SET `image_id` = 1, `image_name` = '{$sFileName}'";
	$rInsert =  mysql_query($sQuery);
	imagepng($rImage, BX_DIRECTORY_PATH_ROOT. 'media/images/' . $sFileName . '.png');
	imagedestroy($rImage);
}

function imageLineThick($rImage, $iX1, $iY1, $iX2, $iY2, $iColor, $iThick = 1)
{
    if ($iThick == 1) 
	{
        return imageline($rImage, $iX1, $iY1, $iX2, $iY2, $iColor);
    }
    $fT = $iThick / 2 - 0.5;
    if ($iX1 == $iX2 || $iY1 == $iY2) 
	{
        return imagefilledrectangle($rImage, round(min($iX1, $iX2) - $fT), round(min($iY1, $iY2) - $fT), round(max($iX1, $iX2) + $fT), round(max($iY1, $iY2) + $fT), $iColor);
    }
    $rMedium = ($iY2 - $iY1) / ($iX2 - $iX1); //y = kx + q
    $rA = $fT / sqrt(1 + pow($rMedium, 2));
    $aPoints = array(
        round($iX1 - (1+$rMedium)*$rA), round($iY1 + (1-$rMedium)*$rA),
        round($iX1 - (1-$rMedium)*$rA), round($iY1 - (1+$rMedium)*$rA),
        round($iX2 + (1+$rMedium)*$rA), round($iY2 - (1-$rMedium)*$rA),
        round($iX2 + (1-$rMedium)*$rA), round($iY2 + (1+$rMedium)*$rA),
    );
    imagefilledpolygon($rImage, $aPoints, 4, $iColor);
    return imagepolygon($rImage, $aPoints, 4, $iColor);
}

function statsPageCodeSettings() 
{
	global $sStartStats, $sEndStats, $iInterval, $iStatsVal;
	$aIntervalValues = array (
		12 => _t('_member_statistics_12_hours'), 
		24 => _t('_member_statistics_1_day'), 
		48 => _t('_member_statistics_2_days'), 
		96 => _t('_member_statistics_4_days'), 
		168 => _t('_member_statistics_1_week'), 
		336 => _t('_member_statistics_2_weeks'), 
		1 => _t('_member_statistics_1_month'),
	);
	$aStatsType = array (
		1 => _t('_member_statistics_total_members'), 
		2 => _t('_member_statistics_members_registered'), 
	);
    $aForm = array(
        'form_attrs' => array(
            'id' => 'adm-settings-form-logo',
            'name' => 'adm-settings-form-logo',
            'action' => '',
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ),
        'params' => array(),
        'inputs' => array(
            'start_stats' => array(
                'type' => 'date',
                'name' => 'start_stats',
                'caption' => _t('_member_statistics_start_stats'),
				'info' => _t('_start_stats_info'),
				'value' => $sStartStats,
                'checker' => array (  
					'func' => 'Date',
				), 
            ),
            'end_stats' => array(
                'type' => 'date',
                'name' => 'end_stats',
                'caption' => _t('_member_statistics_end_stats'),
				'info' => _t('_end_stats_info'),
				'value' => $sEndStats,
				'checker' => array (  
					'func' => 'Date',
				), 
            ),
            'interval' => array(
                'type' => 'select',
                'name' => 'interval',
                'caption' => _t('_member_statistics_interval'),
				'info' => _t('_interval_info'),
				'values' => $aIntervalValues,
				'value' => $iInterval,
            ),
			'stats_type' => array(
                'type' => 'select',
                'name' => 'stats_type',
                'caption' => _t('_member_statistics_stats_type'),
				'values' => $aStatsType,
				'value' => $iStatsVal,
            ),
            'upload' => array(
                'type' => 'submit',
                'name' => 'upload',
                'value' => _t("_member_statistics_stats_save"),
            )
        )
    );
    
    $oForm = new BxTemplFormView($aForm);
    $sResult = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode()));

    return DesignBoxAdmin(_t('_member_statistics_stats_settings'), $sResult);
}

function setStatValues($aParams)
{
	global $iInterval, $sStartStats, $sEndStats;
	if (isset($_POST['interval']))
		$iInterval = (int)$_POST['interval'];
	else if (isset($aParams['ctl_params']['interval']))
		$iInterval = (int)$aParams['ctl_params']['interval'];
	else
		$iInterval = 96;	
	$sQuery = "SELECT MIN(`tp`.`DateReg`) AS `start_date`, MAX(`tp`.`DateReg`) AS `end_date` FROM `Profiles` AS `tp`";
    $aDate = db_arr($sQuery);
	$sStartDate = str_replace(' ', '-', $aDate['start_date']);
	list($iYearStart, $iMonthStart, $iDayStart, $sHourStart) = explode('-', $sStartDate);
	// set start date
	if (isset($_POST['start_stats']))
	{
		$sUserStartDate = $_POST['start_stats'];
		$sUserStartDate = "{$sUserStartDate} 00:00:00";
		// if start date is greater than the registration date of the first member 
		if (strtotime($sUserStartDate) > strtotime("{$iYearStart}-{$iMonthStart}-{$iDayStart} 00:00:00"))
		{
			$aToday = getDate();
			$iYear = $aToday['year'];
			$iMonth = $aToday['mon'];
			$iDay = $aToday['mday'];
			// make sure that start date will not be greater than today
			if (strtotime($_POST['start_stats']) <  strtotime("{$iYear}-{$iMonth}-{$iDay}"))
			{
				$sUserStartDate = str_replace(' ', '-', $sUserStartDate);
				list($iYearStart, $iMonthStart, $iDayStart, $sHourStart) = explode('-', $sUserStartDate);	
			}
		}
	}
	else if (isset($aParams['ctl_params']['start_stats']))
	{
		$sUserStartDate = $aParams['ctl_params']['start_stats'];
		$sUserStartDate = "{$sUserStartDate} 00:00:00";
		$sUserStartDate = str_replace(' ', '-', $sUserStartDate);
		list($iYearStart, $iMonthStart, $iDayStart, $sHourStart) = explode('-', $sUserStartDate);	
	}
	$sStartStats = "{$iYearStart}-{$iMonthStart}-{$iDayStart}";
	if (isset($_POST['end_stats']))
	{
		$aToday = getDate();
		$iYear = $aToday['year'];
		$iMonth = $aToday['mon'];
		$iDay = $aToday['mday'];
		// end day will never be a day after today
		if (strtotime($_POST['end_stats']) > strtotime("{$iYear}-{$iMonth}-{$iDay}"))
		{
			$aDate['end_date'] = reformDate($iYear, $iMonth, $iDay);
		}
		else if (strtotime($_POST['end_stats']) < strtotime($sStartStats))
		{
			$aDate['end_date'] = reformDate($iYear, $iMonth, $iDay);
		}
		else
			$aDate['end_date'] = $_POST['end_stats'];
	}
	else if (isset($aParams['ctl_params']['end_stats']))
	{
		$aDate['end_date'] = $aParams['ctl_params']['end_stats'];
	}
	else
	{
		$aToday = getDate();
		$iYear = $aToday['year'];
		$iMonth = $aToday['mon'];
		$iDay = $aToday['mday'];
		$aDate['end_date'] = reformDate($iYear,$iMonth,$iDay);
	}
	$sEndDate = str_replace(' ', '-', $aDate['end_date']);
	list($iYearEnd, $iMonthEnd, $iDayEnd, $sHourEnd) = explode('-', $sEndDate);	
	$sFinishDate = "{$iYearEnd}-{$iMonthEnd}-{$iDayEnd}";
	$sEndStats = $sFinishDate;
}

function reformDate($iYear, $iMonth, $iDay)
{
	$sDate = "";
	if ($iMonth>9 && $iDay>9)
		$sDate  = "{$iYear}-{$iMonth}-{$iDay}";
	else if ($iMonth>9 && $iDay<=9)
		$sDate  = "{$iYear}-{$iMonth}-0{$iDay}";
	else if ($iMonth<=9 && $iDay>9)
		$sDate  = "{$iYear}-0{$iMonth}-{$iDay}";
	else if ($iMonth<=9 && $iDay<=9)
		$sDate  = "{$iYear}-0{$iMonth}-0{$iDay}";
	return $sDate;
}
?>
