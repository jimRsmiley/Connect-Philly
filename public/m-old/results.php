<?php
require_once( 'includes.php' );
include( 'inc.header.php' );
?>

<title>Center Results - Connect Philly</title>

<script type="text/javascript">

// everytime the index page is shown run onLoad
$('#results').live('pageshow', function () {
	console.log( 'pageshow #results' );
    showLoadingCentersMsg();
});

// everytime the index page is shown run onLoad
$('#centerdetail').live('pageshow', function () {
	console.log( 'pageshow #centerdetail' );
    $.mobile.hidePageLoadingMsg();
});

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

function requestCenters( lat, lng, optionsArray ) {
		json = '"lat":"' + lat + '"'
		+ ','
		+ '"lng":"' + lng + '"'
		+ ','
		+ getSearchOptionsJson(optionsArray)
        + ','
        + '"numCenters":"10"';
	json = '{' + json + '}';
	console.log( 'requestCenters: ' + json );
	
	$.ajax(
		{
			url: "/center-request/by-location?format=json", 
			type: 'post',
			data: json,
			success: 
				function(result) {
                    console.log('result json:' + result );
                    var object = jQuery.parseJSON(result);
                    console.log('result json:' + result['ComputerCenters'] );
                    var object = jQuery.parseJSON(result['ComputerCenters'][0]);
                    centers = object;
                    object = result;

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
                    $('div#centerlist').css( 'visibility', 'visible' );
                    //$('ul').autoRender(object);
                    console.log( 'hiding loading message' );
                    $.mobile.hidePageLoadingMsg(); 
                },
            fail:
                function() {
                    console.log('json request failed');
                }
            
		}
	); // end ajax
}

<?php
$lat = ( array_key_exists( 'lat', $_GET ) ) ? $_GET['lat'] : 'null';
$lng = (array_key_exists('lng',$_GET)) ? $_GET['lng'] : 'null';
$searchOptions = ( array_key_exists( 'searchOptions', $_GET ) ) ? $_GET['searchOptions'] : 'null';
?>
var lat = <?=$lat?>;
var lng = <?=$lng ?>;
var searchOptions = <?=$searchOptions?>;

// if we're processing a request for 
if( lat && lng ) {
	requestCenters( lat,lng,searchOptions );
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
  <div id="centerlist" style="visibility:hidden">
		<div class="ComputerCenters">
        	<a href="/m/centerdetail.php?rowid=" rel="external" data-role="button" data-icon="arrow-r" data-iconpos="right" style="text-align:left">
                <span>
                    <span class="distance"></span> miles away -
                    <span class="locationTitle"></span>
                </span>
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