<?
require_once('../../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );

require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );


        if (!$GLOBALS['logged']['admin']) {
            Header("location:http://".$_SERVER['SERVER_NAME']);
        }

include "MdbBConfig.php";

if(isset($_GET['del']) && !empty($_GET['del'])) 
	unlink("../backup/".$_GET['del']);


function mfilesize($file) {

$size=filesize(PATH."$file");

      $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
      if ($size == 0) { return('n/a'); } else {
      return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); }
}


?>
<style>
  .tdtext, .tdtxt, .tr_title{
    border-top:solid 1px #bababa;
    border-bottom:solid 1px #bababa;
    border-collapse: collapse; 
    font-family:Arial;
    font-size:12px;
  }

  .tdtxt{
    border:solid collapse;
    text-align:center;
  }

  .uc:hover, .ch:hover{
    background-color:#e7e8ff;
  }

  .ch{
    background-color:#f1f1f1;
  }

  .tr_title{
    background-color:#e3e3e3;
    border-bottom:solid 3px #bababa;
  }
</style>

<table width=100% cellspacing=0 cellpadding=10 border=0>
 <tr class='tr_title'>
      <td class='tr_title'>
	<b>Backup file name</b>
      </td>
      <td class='tr_title' width=100px>
	<b>File Size</b>
      </td>
      <td class='tr_title' width=30px>
	<b>Delete</b>
      </td>
    </tr>


<?php

$ih=0;
if ($handle = opendir(PATH)) {
    while (false !== ($file = readdir($handle))) {

        if ($file != "." && $file != ".." && $file != "index.html" && $file != "dumper.cfg.php") {
++$ih;

if($ih % 2) $class=' ch'; else $class='';
            print "<tr class='tdtext$class uc'>
      <td class='tdtext'>
	<a href='".PATH."$file'> $file </a>
      </td>
      <td class='tdtext'>
	".mfilesize(PATH.$file)." </a>
      </td>
      <td class='tdtxt'>
	<a href='?del=$file'>x</a>
      </td>
    </tr>
	";

        }

    }
    closedir($handle);
}
?>

     
</table>