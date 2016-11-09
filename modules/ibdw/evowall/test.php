<html>
<head>
<title>the title</title>
<script type="text/javascript" 
   src="/inc/js/jquery-1.3.2.min.js"></script>
   <script type="text/javascript" language="javascript">
   $(document).ready(function() {
      $("#driver").click(function(event){
          $.getJSON('/inc/js/result.json', function(jd) {
             $('#stage').html('<p> Name: ' + jd.name + '</p>');
             $('#stage').append('<p>Age : ' + jd.age+ '</p>');
             $('#stage').append('<p> Sex: ' + jd.sex+ '</p>');
          });
      });
   });
   </script>
</head>
<body>
   <p>Click on the button to load result.json file:</p>
   <div id="stage" style="background-color:blue;">
          STAGE
   </div>
   <input type="button" id="driver" value="Load Data" />
</body>
</html>
