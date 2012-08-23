<?php
require_once( 'includes.php' );
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Add A Center - Connect Philly</title>
<link href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" rel="stylesheet" type="text/css" />
<script src="http://code.jquery.com/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js" type="text/javascript"></script>
<script src="/js/connect.js" type="text/javascript"></script>

<script type="text/javascript">

*/
				//requestCenters(json);



/*$('div[data-role=page]').live('pagechange',function(){
	setFormValues();
});

$('div[data-role=page]').live('pagebeforechange',function(){
	storeFormValues();
});*/



function setHours() {
	
	var hour = document.forms[1].elements['hour'].value;
	var minutes = document.forms[1].elements['minutes'].value;
	var amPm = document.forms[1].elements['amPm'].value;
	var hoursString = hour + ':' + minutes + ' ' + amPm;
	console.log( hoursString );
	
	console.log( pendingCenter );
}


</script>

</head>

<body>
<div data-role="page" id="index" data-url="addcenter.php&index">

  <div data-role="header"> 
		<?php echo CONNECT_M_HEADER; ?> 
  </div><!-- /header -->

  <div data-role="content">
    <form id="form1" name="form1" method="post">
    
      <div data-role="fieldcontain">
        <p>
          <label for="textinput">Location Title:</label>
          <input type="text" name="locationTitle" id="textinput" value=""  />
        </p>
      </div>
      
      <div data-role="fieldcontain">
        <p>
            <label for="textinput2">Address 1:</label>
            <input type="text" name="address1" id="textinput2" value=""  />
          </p>
      </div>
          
      <div data-role="collapsible">
            	<h3>Set Hours</h3>
            	<div>
                	<a href="#hours" data-role="button">Set Sunday Hours</a> 
                </div>
      </div><!-- end collapsible -->
      
      <div>
      		<p>
            <button name="SubmitLocation">Submit Locaton</button>
         </p>
      </div>
    </form>
  </div><!-- end content -->
  
  <div data-role="footer">
    <h4>Footer</h4>
  </div>
  
</div>

<div data-role="page" id="hours" data-url="addcenter.php&hours">

    <div data-role="header"> 
		<?php echo CONNECT_M_HEADER; ?> 
	</div><!-- /header -->
    
    <div data-role="content">
    <form name="form1" method="post">
            <fieldset data-role="controlgroup" data-type="horizontal">
             
              <select name="hour" id="hour" data-mini="true">
                <option>Hour</option>
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
                <option>6</option>
                <option>7</option>
                <option>8</option>
                <option>9</option>
                <option>10</option>
                <option>11</option>
                <option>12</option>
            </select>
            
            <select name="minutes" id="minutes" data-mini="true">
                <option>Minute</option>
                <option>00</option>
                <option>30</option>
            </select>
           
           <select name="amPm" id="amPm" data-mini="true">
                <option>AM</option>
                <option>PM</option>
            </select>
           </fieldset>
            
        <div data-role="fieldcontain">
            <div>
                	<a href="#index" data-role="button">Set Sunday Hours</a> 
                </div>
        </div>
    
    </form>
    </div>
    
    <div data-role="footer">
      <h4>Footer</h4>
    </div> 
    
</div>

</body>

</html>