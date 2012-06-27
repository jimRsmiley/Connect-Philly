<?php
require_once( 'includes.php' );
?>

<!DOCTYPE html> 
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Center Results - Connect Philly</title>
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js"></script>

<script type="text/javascript" src="/m/mobile.js"></script>
<script type="text/javascript" src="/m/js/pure/pure.js"></script>
<link href="/m/style.css" rel="stylesheet" type="text/css">

<script type="text/javascript">


var centers;
/*
	pull the search options from the checkboxes and return their values in an array
*/
function getSearchOptionsJson() {
    
	var inputs = document.getElementsByName("searchOptions");
	
	var searchOptions = new Array();
	for( i = 0; i < inputs.length; i++ ) {
		console.log( 'getSearchOptionsJson: ' + inputs[i].value + ': ' + inputs[i].checked );
		
		if( inputs[i].checked != false && inputs[i].value != null ) {
			searchOptions[ searchOptions.length ] = inputs[i].value;
		}
	}
	
	var json = '"searchOptions":[';
	for( i = 0; i < searchOptions.length; i++ ) {
		json += '"' + searchOptions[i] + '"';
		
		if( i+1 < searchOptions.length ) {
			json += ',';
		}
	}
	json += ']';
	
	return json;
}

function requestCenters( latitude, longitude, optionsArray ) {
		json = '"latitude":"' + latitude + '"'
		+ ','
		+ '"longitude":"' + longitude + '"'
		+ ','
		+ getSearchOptionsJson(optionsArray);
	json = '{' + json + '}';
	console.log( 'requestCenters: ' + json );
	
	$.ajax(
		{
			url: "center_request.php", 
			type: 'post',
			data: json,
            beforeSend: 
				function() {
					$.mobile.loadingMessage = 'Loading Computer Centers';
					$.mobile.showPageLoadingMsg(); 
				}, //Show spinner
            complete: 
				function() { 
					$.mobile.hidePageLoadingMsg(); 
				}, //Hide spinner
			success: 
				function(result) {
				//console.log(result);
				var object = jQuery.parseJSON(result);
				centers = object;
				
				$('div.ComputerCenter').css('background-color',"#06F");
				
				var directive = {
					'div.ComputerCenters' : {
						'center <- ComputerCenters' : {
							'span.distance' : 'center.distance',
							'span.locationTitle' : 'center.locationTitle',
							'a@href+' : 'center.rowid'
						}
					}
				}
				
				console.log( object );
				$('div#centerlist').render(object, directive);
				//$('ul').autoRender(object);
			}
		}
	); // end ajax
}

<?php
$latitude = ( array_key_exists( 'latitude', $_GET ) ) ? $_GET['latitude'] : 'null';
$longitude = (array_key_exists('longitude',$_GET)) ? $_GET['longitude'] : 'null';
$searchOptions = ( array_key_exists( 'searchOptions', $_GET ) ) ? $_GET['searchOptions'] : 'null';
?>
var latitude = <?=$latitude?>;
var longitude = <?=$longitude ?>;
var searchOptions = <?=$searchOptions?>;

if( latitude && longitude ) {
	requestCenters( latitude,longitude,searchOptions );
}
</script>
</head>

<body>
<div data-role="page" id="results">
  <div data-role="header">
   <?php echo CONNECT_M_HEADER; ?>
  </div>
  <div data-role="content" id="resultscontent">
  
  <!-- HTML template -->
  <div id="centerlist">
		<div class="ComputerCenters">
        	<a href="/m/results.php#centerdetail?rowid=" rel="external" data-role="button" data-icon="arrow-r" data-iconpos="right" style="text-align:left">
                <span class="distance"></span> miles away -<br/>
                <span class="locationTitle"></span>
            </a>
        </div>
  </ul>
  <!-- end HTML template -->
  
  </div><!-- content -->
  <div data-role="footer">
    <h4>Footer</h4>
  </div>
</div>
<div data-role="page" id="centerdetail">
  <div data-role="header">
    <h1>Header</h1>
  </div>
  <div data-role="content">This is the center detail</div>
  <div data-role="footer">
    <h4>Footer</h4>
  </div>
</div>
</body>
</html>