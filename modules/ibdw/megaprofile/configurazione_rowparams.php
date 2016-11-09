<?
  require_once( '../../../inc/header.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
  $userid = (int)$_COOKIE['memberID'];
  if(!isAdmin()) { exit;}
  mysql_query("SET NAMES 'utf8'");
  if(isset($_POST['action'])){
  if($_POST['action'] == 'new'){          
  $name = $_POST['name'];
  $lang = $_POST['lang'];
  $status = $_POST['status'];
  $order = $_POST['order'];
  $url = $_POST['url'];
  $destination = $_POST['destination'];
  
  $inserimento = "INSERT INTO `ibdw_mega_extralink` (`Name`, `LangKey`, `Status`, `UrlDyn`, `Destination`, `Order`) 
                  VALUES 
                  ('".$name."', '".$lang."', '".$status."', '".$url."', '".$destination."', '".$order."')";
  $exe = mysql_query($inserimento);
  header('Location: configurazione.php'); exit();
  }
  else { 
  $inserimento = "DELETE FROM `ibdw_mega_extralink` WHERE ID = ".$_POST['idblox'];
  $exe = mysql_query($inserimento);
  }
  }
  else { 
  $idbox = $_POST['idbox'];
  $namebox = $_POST['namebox'];
  $langbox = $_POST['langbox'];
  $statusbox = $_POST['statusbox'];
  $urlbox = $_POST['urlbox'];  
  $destination = $_POST['destination'];
  $ordinebox = $_POST['ordinebox'];

  $update = "UPDATE `ibdw_mega_extralink` SET 
                `Name` = '".$namebox."',
                `LangKey` = '".$langbox."',
                `Status` = '".$statusbox."',
                `UrlDyn` = '".$urlbox."',
                `Destination` = '".$destination."',
                `Order` = '".$ordinebox."'
             WHERE ID = ".$idbox."  LIMIT 1";
  $exeup = mysql_query($update); 
  } 
?>