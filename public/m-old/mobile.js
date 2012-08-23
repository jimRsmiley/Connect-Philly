function canBrowserGeolocate() {	
    return navigator.geolocation;
}

/*
 * show the computer cetner location message.  useful for when loading
 * computer centers via ajax
 */
function showLoadingCentersMsg() {
    $.mobile.loadingMessageTextVisible = true;
    $.mobile.showPageLoadingMsg("a", "Loading computer centers....");
}

function getMapsUrl( address ) {
    address += ' Philadelphia, PA';
    
    var retVal = 'http://maps.google.com/maps?q='
                + encodeURIComponent( address )
                ;
    return retVal;
}

/*
 * given a phone number, return the number inside a telephone protocol anchor
 * reference
 */
function getPhoneNumberUrl( number ) {
    myregexp = new RegExp('(\\d\\d\\d)(\\d\\d\\d)(\\d\\d\\d\\d)' );
    
    if ( myregexp.test(number) ) {
        matches = myregexp.exec(number);
        number = '('+matches[1]+') '+matches[2]+'-'+matches[3];
    }
    
    return '<a href="tel://' + number + '">' + number + '</a>';
}