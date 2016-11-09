<?php
$mitrovo=$_SERVER['PHP_SELF'];
$bottonealtre=strpos($mitrovo, 'bottonealtrenews.php');
if($bottonealtre!= 0) 
{
 require_once( '../../../inc/header.inc.php' );
 require_once( BX_DIRECTORY_PATH_INC.'design.inc.php' );
 require_once( BX_DIRECTORY_PATH_INC.'profiles.inc.php' );
 require_once( BX_DIRECTORY_PATH_INC.'utils.inc.php' );
}
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
if(isset($_POST['contanews'])){$contanews=$_POST['contanews'];}
if(isset($_POST['limite'])) {$limite=$_POST['limite'];}
if(isset($_POST['pagina'])) {$pagina=$_POST['pagina'];}
if(isset($_POST['idrichiesto'])) { $accountid = $_POST['idrichiesto']; } else { $accountid = getID($_REQUEST['ID']);}
if(isset($_POST['ultimoid'])) {$ultimoid=$_POST['ultimoid'];}  else { $ultimoid = $GLOBAL['ultimoid']; }
if($hidemorenews == '') {echo '<a href="javascript:altrenews('.$contanews.','.$limite.',\' '.$pagina.' \','.$accountid.','.$ultimoid.');" class="primoaltre"><b>'._t('_ibdw_evowall_altrenews').'</b></a>';}
echo '<input type="hidden" id="ajx_contanews" value="'.$contanews.'" />';
echo '<input type="hidden" id="ajx_limite" value="'.$limite.'" />';
echo '<input type="hidden" id="ajx_pagina" value="'.$pagina.'" />';
echo '<input type="hidden" id="ajx_mioid" value="'.$accountid.'" />';
echo '<input type="hidden" id="ajx_ultimoid" value="'.$ultimoid.'" />';
?>