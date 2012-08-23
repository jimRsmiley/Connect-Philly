<?php
require_once( 'includes.php' );
include( 'inc.header.php' );
?>

<title>Connect Philly</title>

<script type="text/javascript">
console.clear();

// when the index page is shown, check that the browser can geolocate, if it
// can, show 'Use Current Location' in addresss search text input
$('#indexpage').live('pageshow', function () {
	console.log( 'pageshow function' );
	// if the browser can navigate
	if (canBrowserGeolocate()) {
		// set the value of the the search box to use current location
		$('#searchaddress').val('Use Current Location');
		//$('#searchaddress').val('5th and market');
		// and make the text italics
		$('#searchaddress').css('font-style',"italic");
	}
});

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
function submitLocation() {
	$.mobile.showPageLoadingMsg();
	address = $('#searchaddress').val();
	console.log( 'geolocateAddress(): ' + address );
	
	// if using the current browser locaton
	if( canBrowserGeolocate() && address == 'Use Current Location' )
	{
		console.log( 'search address ' + address );
		
		successCallback = function (position) {
				$('#lat').val( position.coords.latitude );
				$('#lng').val( position.coords.longitude );
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
			$('#lat').val( results[0].geometry.location.lat() );
			$('#lng').val( results[0].geometry.location.lng() );
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

      <input type="hidden" name="lat" id="lat" value="asdf" />
      <input type="hidden" name="lng" id="lng" value="asfd" />
      
        <div align="center"> Connect Philly will help you find the computer access locations of the nearest to you. </div>
        <div>
          <div data-role="fieldcontain">
            <div align="center">
              <p>
                <input type="text" name="searchaddress" id="searchaddress" value=""  onfocus="deleteInputText('#searchaddress'); return false;">
              </p>
              <p>&nbsp;
                <button type="button" onClick="submitLocation();">Get Centers</button>
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