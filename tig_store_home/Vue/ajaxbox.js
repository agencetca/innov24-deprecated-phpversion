    	setInterval(function(){
    
        if (window.XMLHttpRequest){
          xmlhttp=new XMLHttpRequest();
          }
        else {
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          }
        xmlhttp.onreadystatechange=function() {
          if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById("new_msg").innerHTML=xmlhttp.responseText;
            }
          }
        xmlhttp.open("GET","tig_store_home/Modele/alerts.php?,true);
        xmlhttp.send();
    	},2000);