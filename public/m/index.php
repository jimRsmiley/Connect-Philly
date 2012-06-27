<?php
require_once( 'includes.php' );
?>

<!DOCTYPE html> 
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Connect Philly</title>
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js"></script>
<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="/m/mobile.js"></script>
<script type="text/javascript" src="/m/js/pure/pure.js"></script>
<link href="/m/style.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
console.clear();

// everytime the index page is shown run onLoad
$('#indexpage').live('pageshow', function () {
	console.log( 'pageshow function' );
    onLoad();
});

function onLoad() {
	console.log( 'onload()' );
	
	// if the browser can navigate
	if (canBrowserGeolocate()) {
		// set the value of the the search box to use current location
		$('#searchaddress').val('Use Current Location');
		//$('#searchaddress').val('5th and market');
		// and make the text italics
		$('#searchaddress').css('font-style',"italic");
	}
}

function submitForm() {
	console.log( 'submitForm()' );
	geolocateAddress();
}

// delete the input text
function deleteInputText(name) {
	console.log( 'deleteInputText()' );
    $(name).val('');
    $(name).css('font-style',"normal");
}

/*
* submit the addresss location and search options, then populate the
* the response page
*/
function geolocateAddress() {
	
	address = $('#searchaddress').val();
	console.log( 'geolocateAddress(): ' + address );
	
	// if using the current browser locaton
	if( canBrowserGeolocate() && address == 'Use Current Location' )
	{
		console.log( 'search address ' + address );
		
		successCallback = function (position) {
				$('#latitude').val( position.coords.latitude );
				$('#longitude').val( position.coords.longitude );
				$('#searchaddress').remove();
				$('#form1').submit();
			}; // end function(position)
			
		navigator.geolocation.getCurrentPosition( successCallback );
		

	}
	
	else 
	{
		console.log('geolocating address ' + address );
		geocoder = new google.maps.Geocoder();
		geocoder.geocode( { 'address': address}, function(results, status) {
		  if (status == google.maps.GeocoderStatus.OK) {
			$('#latitude').val( results[0].geometry.location.lat() );
			$('#longitude').val( results[0].geometry.location.lng() );
			$('#searchaddress').remove();
			$('#form1').submit();
		  } else {
			alert("Geocode was not successful for the following reason: " + status);
			return false;
		  }
		});
	}

}
</script>
</head>

<body>

<div data-role="page" id="indexpage">
	<div data-role="header"> 
		<?php echo CONNECT_M_HEADER; ?> 
	</div><!-- /header -->

    <div data-role="content" id="content"> 
      <form id="form1" name="form1" method="get" action="results.php" data-ajax="false">

      <input type="hidden" name="latitude" id="latitude" value="asdf" />
      <input type="hidden" name="longitude" id="longitude" value="asfd" />
      
        <div align="center"> Connect Philly will help you find the computer access locations of the nearest to you. </div>
        <div>
          <div data-role="fieldcontain">
            <div align="center">
              <p>
                <input type="text" name="searchaddress" id="searchaddress" value=""  onfocus="deleteInputText('#searchaddress'); return false;">
              </p>
              <p>&nbsp;
                <button type="button" onClick="submitForm();">Get Centers</button>
              </p>
            </div>
          </div>
          <div align="center"></div>
        </div>

        <div data-role="fieldcontain" style="text-align:center">
          <fieldset data-role="controlgroup" data-type="horizontal" style="text-align:center">
            <legend>Center Search Options</legend>
            <p>
              <input type="checkbox" name="searchOptions" id="checkbox1_0" class="custom" value="open">
              <label for="checkbox1_0">Open Now</label>
              <input type="checkbox" name="searchOptions" id="checkbox1_1" class="custom" value="wifi">
              <label for="checkbox1_1">Wifi</label>
            </p>
            <p>&nbsp;</p>
          </fieldset>
        </div>
      </form>
      <div>Connect Philly needs all the locations it can handle, please consider any locatons you know of to our database<a href="/m/addcenter.php" data-role="button" rel="external">Add a center</a> </div>
    </div><!-- content -->
      
    <div data-role="footer">
        
    </div><!-- end footer -->
  
</div><!-- end landing page -->

</body>
</html>