<!-- ***********************************************************************************************************************







ATTENTION PLEASE : The code must be past in a PHP bloc in Boonex administration/pageBuilder.php WITHOUT the PHP beacons







*********************************************************************************************************************** -->

<?php

$memberiID = getID( $_GET['ID'] );

echo '
<!doctype html>
  <html>
      <head>';
    echo '
    <script>
      function getChoice(int) {
        if (window.XMLHttpRequest){
          xmlhttp=new XMLHttpRequest();
          }
        else {
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          }
        xmlhttp.onreadystatechange=function() {
          if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById("panel").innerHTML=xmlhttp.responseText;
            }
          }
        xmlhttp.open("GET","news_panel/newspanel.php?config="+int,true);
        xmlhttp.send();
      }
    </script>';

    echo '
    <link rel="stylesheet" type="text/css" href="news_panel/css/newspanel.css">
    </head>';


//-------------------------PANEL CONFIGURE ME----------------------------------------

    echo'
    <body>';
    echo '
      <div class="titlepanelbox">
            <p class="titlepanelstyle">';
            echo _t('_configure_me');
        echo '</p>
        </div>';

      echo'
      <div id="panel">


      <div id="outdiv">
        <iframe src="http://localhost/innov24/m/configure/edit/1" scrolling="yes" width="230px" height="447px" id="iniframe"></iframe>
      </div> 


      </div>';

    echo '
    </body>';

//-------------------------PANEL CONFIGURE ME----------------------------------------

  echo '
  </html>';

?>
