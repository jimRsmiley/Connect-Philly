function canBrowserGeolocate() {	
    return navigator.geolocation;
}

function getMapsUrl( address ) {
    address += ' Philadelphia, PA';
    
    var retVal = 'http://maps.google.com/maps?q='
                + encodeURIComponent( address )
                ;
    return retVal;
}

function getPhoneNumberUrl( number ) {
    myregexp = new RegExp('(\\d\\d\\d)(\\d\\d\\d)(\\d\\d\\d\\d)' );
    
    if ( myregexp.test(number) ) {
        matches = myregexp.exec(number);
        number = '('+matches[1]+') '+matches[2]+'-'+matches[3];
    }
    
    return '<a href="tel://' + number + '">' + number + '</a>';
}


function getCenterHtml( center ) {

    console.log( center );
    
    wifiString = '';
    if( center.hasWifiAccess.toLowerCase() == 'yes' ) {
        wifiString = '<div style="float: left; margin-right: 5px">Wifi</div>';
    }
    
    openString = '';
    if( center.openStatus == 1 ) {
        openString = 'Open Now';
    }
    else if( center.openStatus == 2 ) {
        openString = 'Closed Now';
    }
    
    hoursString = '<div class="hoursDescription">'
					+ '<div data-role="collapsible">'
						+ '<H3>Hours:</H3>'
						+ '<p>'
						+ '<span class="dayAbbrev">Su:</span>' + center.sundayHoursDescription + ' '
						+ '<span class="dayAbbrev">M</span>' + center.mondayHoursDescription + ' '
						+ '<span class="dayAbbrev">Tu</span>' + center.tuesdayHoursDescription + ' '
						+ '<span class="dayAbbrev">W</span>' + center.wednesdayHoursDescription + ' '    
						+ '<span class="dayAbbrev">Th</span>' + center.thursdayHoursDescription + ' '
						+ '<span class="dayAbbrev">F</span>' + center.fridayHoursDescription + ' '
						+ '<span class="dayAbbrev">Sa</span>' + center.saturdayHoursDescription + ' '
						+ '</p>'
					+ '</div>'
	            + '</div>'
                ;
    
    var html = '<div>'
            + '<div>' + center.locationTitle + '</div>'
            + '<div><a href="' +
                getMapsUrl( center.address1 ) 
                + '">' 
                + center.address1 
                + '</a></div>'
            + '<span>'
                +  wifiString 
                + '<div style="float: left">Tel.: ' + getPhoneNumberUrl(center.centerPhoneNumber) + '</div>'
            + '</span>'
            + '<div style="clear: both">'
            + '<div>' + openString + '</div>'
            + '<div>' + hoursString + '</div>'
            + '<div>' + center.distance.toFixed(1) + ' miles away</div>'
            + '</div';
    return html;
}