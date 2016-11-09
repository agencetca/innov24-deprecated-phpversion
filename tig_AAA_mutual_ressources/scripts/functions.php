<?php



require_once( '../../inc/header.inc.php' ); // BDD


    //------------FONCTION TIME--------------------------------------------------------------
    function DataTime($dataTime) 
    { 
    $date = date("j/m/Y", $dataTime); 
    $hour = date("H:i", $dataTime); 
    $yesterday = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))); 
    $today = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d"), date("Y"))); 
    $tomorrow = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))); 
    $aftertomorrow = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 2, date("Y")));
    $inaweek = date("j/m/Y", mktime(0, 0, 0, date("m"), date("d") + 7, date("Y")));  
    if ($date == $yesterday) $dateDisplay = _t('_datatimefunction_yesterday'); 
    elseif ($date == $today) $dateDisplay = _t('_datatimefunction_today');
    elseif ($date == $tomorrow) $dateDisplay = _t('_datatimefunction_tomorrow'); 
    elseif ($date == $aftertomorrow) $dateDisplay = _t('_datatimefunction_after'); 
    elseif ($date == $inaweek) $dateDisplay = _t('_datatimefunction_week'); 
    else $dateDisplay = $date;
    return("$dateDisplay $hour");
    }
    //------------FONCTION TIME--------------------------------------------------------------


    //------------ USER SETTINGS--------------------------------------------------------------

    function innov24_config() { // Request aims to fetch Configure_me settings in a string
    $configureme = "SELECT afk_cfgme_interest.cat_id FROM afk_cfgme_interest WHERE afk_cfgme_interest.user_id=".$memberiID."";
    $configreq = mysql_query($configureme) or die("A MySQL error has occurred.<br />Your Query: " . $configureme . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());
    $config5 = mysql_fetch_assoc($configreq);
    $config4 = implode(",",$config5);
    $config3 = str_replace(",", "','", $config4);
    $config2 = str_replace("{", "','", $config3);
    $config1 = str_replace("}", "','", $config2);
   	$innov24_config = substr($config1, 0, -3);
   	return ($innov24_config);
   	}
	
	function privacy_OthersToMe() { 
    // Request aims to fetch the list of privacy_groups which I belong in a string
    $privacy_OthersToMe_array = array();
    $privacy_OthersToMe_req = "SELECT sys_privacy_members.group_id FROM sys_privacy_members WHERE sys_privacy_members.member_id=".$memberiID."";
    $privacy_OthersToMe_fetch = mysql_query($privacy_OthersToMe_req) or die("A MySQL error has occurred.<br />Your Query: " . $privacy_OthersToMe_req . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());
    while ($privacy_OthersToMe_result = mysql_fetch_assoc($privacy_OthersToMe_fetch)) {
      array_push($privacy_OthersToMe_array, $privacy_OthersToMe_result['group_id']);}
    $privacy_OthersToMe_not_formated = implode(",",$privacy_OthersToMe_array);
    $privacy_OthersToMe = str_replace(",", "','", $privacy_OthersToMe_not_formated);
	return ($privacy_OthersToMe);
   	}

	function privacy_MeToOthers() { 
    // Request aims to fetch the list of privacy_groups which I own in a string
    $privacy_MeToOthers_array = array();
    $privacy_MeToOthers_req = "SELECT sys_privacy_groups.id FROM sys_privacy_groups WHERE sys_privacy_groups.owner_id=".$memberiID."";
    $privacy_MeToOthers_fetch = mysql_query($privacy_MeToOthers_req) or die("A MySQL error has occurred.<br />Your Query: " . $privacy_MeToOthers_req . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());
    while ($privacy_MeToOthers_result = mysql_fetch_assoc($privacy_MeToOthers_fetch)) {
      array_push($privacy_MeToOthers_array, $privacy_MeToOthers_result['id']);}
    $privacy_MeToOthers_not_formated = implode(",",$privacy_MeToOthers_array);
    $privacy_MeToOthers = str_replace(",", "','", $privacy_MeToOthers_not_formated);
    return ($privacy_MeToOthers);
   	}

	function bigfriendlist() { 
    // Request aims to fetch the first part of my friend list in a string
    $friendlist1_array = array();
    $friendlist1_req = "SELECT sys_friend_list.Profile FROM sys_friend_list 
    WHERE sys_friend_list.ID=".$memberiID." AND sys_friend_list.Check=1";
    $friendlist1_fetch = mysql_query($friendlist1_req) or die("A MySQL error has occurred.<br />Your Query: " . $friendlist1_req . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());
    while ($friendlist1_result = mysql_fetch_assoc($friendlist1_fetch)) {
      array_push($friendlist1_array, $friendlist1_result['Profile']);}

    // Request aims to fetch the second part of my friend list in a string
    $friendlist2_array = array();
    $friendlist2_req = "SELECT sys_friend_list.ID FROM sys_friend_list 
    WHERE sys_friend_list.Profile=".$memberiID." AND sys_friend_list.Check=1";
    $friendlist2_fetch = mysql_query($friendlist2_req) or die("A MySQL error has occurred.<br />Your Query: " . $friendlist2_req . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());
    while ($friendlist2_result = mysql_fetch_assoc($friendlist2_fetch)) {
      array_push($friendlist2_array, $friendlist2_result['ID']);}

    //Merge both friend lists
    $bigfriendlist_array = array_merge($friendlist1_array, $friendlist2_array);
    $bigfriendlist_not_formated = implode(",",$bigfriendlist_array);
    $bigfriendlist_formated = str_replace(",", "','", $bigfriendlist_not_formated);
    $bigfriendlist = substr_replace($bigfriendlist_formated, "".$memberiID."','", 0, 0);
    return ($bigfriendlist);
   	}
