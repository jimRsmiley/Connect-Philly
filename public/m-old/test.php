<!DOCTYPE html> 
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<!--
    JQuery and JQuery Mobile
-->
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js"></script>


<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>

<script type="text/javascript" src="/js/connect.js"></script>
<script type="text/javascript" src="/js/pure/pure.js"></script>
<link href="/css/mobile.css" rel="stylesheet" type="text/css" />

<title>Add a center - Connect Philly</title>
</head>

<body>

<div data-role="page" id="hours-page" data-url="#">

    <div data-role="header"> 
		<div><a href="/m" data-role="button" data-icon="home" rel="external">Home</a></div><div><h1><img src="/images/m/connectphilly_house.jpg" width="450" alt="Connect Philly"></h1></div> 
	</div><!-- /header -->
    
    <div data-role="content">
<fieldset data-role="controlgroup" data-type="horizontal" style="text-align: center">
  <div style="width: 200px; margin: 0 auto;">
   <input type="radio" name="radio-choice-1" id="radio-choice-1" value="choice-1" checked="checked" />
   <label for="radio-choice-1">A</label>
   <input data-theme="e" type="radio" name="radio-choice-1" id="radio-choice-2" value="choice-2"  />
   <label for="radio-choice-2">B</label>
 </div>
</fieldset>

    </div>
    
    <div data-role="footer">
      <h4>Footer</h4>
    </div> 
</div>

    
</body>

</html>