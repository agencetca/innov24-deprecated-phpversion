<?php
	if( isset($_REQUEST['gConf']) ) die; // globals hack prevention
	require_once ($gConf['dir']['layouts'] . 'base_en/params.php');
    $gConf['dir']['xsl'] = $gConf['dir']['layouts'] . 'oounisoft_en/xsl/';	// xsl dir
    $gConf['url']['css'] = $gConf['url']['layouts'] . 'oounisoft_en/css/';	// css url
    $gConf['url']['xsl'] = $gConf['url']['layouts'] . 'oounisoft_en/xsl/';	// xsl url

?>
