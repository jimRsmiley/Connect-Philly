<?php
require_once( 'includes.php' );
include( 'inc.header.php' );
?>

<title>Center Results - Connect Philly</title>

<script type="text/javascript">
    
    url = "/center-request/byrowid?format=json&rowid=<?=$_GET['rowid']?>";
$.ajax(
    {
        url: url, 
        success: 
            function(result) {
                center = result['ComputerCenters'][0];
                
                for (var prop in center) {
                    console.log(prop + " = " + center[prop]);
                    $('#'+prop).html(center[prop]);
                }

            }
    }
); // end ajax

</script>
</head>

<body>

<div data-role="page" id="centerdetail">
  <div data-role="header">
    <h1>Header</h1>
  </div>
  <div data-role="content">
      <h3 id="locationTitle"></h3>
      <div>Address1:<span id="address1"></span></div>
      <div data-role="collapsible">
        <h4>Hours</h4>
        <div>Sunday: <span id="sundayHoursDescription"></span></div>
        <div>Monday: <span id="mondayHoursDescription"></span></div>
        <div>Tuesday: <span id="tuesdayHoursDescription"></span></div>
        <div>Wednesday: <span id="wednesdayHoursDescription"></span></div>
        <div>Thursday: <span id="thursdayHoursDescription"></span></div>
        <div>Friday: <span id="fridayHoursDescription"></span></div>
        <div>Saturday: <span id="saturdayHoursDescription"></span></div>
        
      </div>
  </div>
  <div data-role="footer">
    <h4>Footer</h4>
  </div>
</div>
    
</body>
</html>