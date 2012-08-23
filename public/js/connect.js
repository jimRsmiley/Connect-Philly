function CenterRequestJSONBuilder() {
	
	this.getJson = function( centerRequest ) {
		var retVal = '[';
			+ '{"lat":"'+centerRequest.lat+'"}'
			+ '{"lng":"'+centerRequest.lng+'"}'
			+ ']';
			
		return retVal;
	};
};

/*
 * given a string, insert spaces in front and capitalize first letter
 */
function capAndAddSpacesToCamelCase(string) {
    string = string.replace( /([A-Z])/, " $1" );
    string = string.charAt(0).toUpperCase() + string.slice(1);
    return string;
}

/**
 * given the css selector name of the input, checks whether the browser can
 * geolocate and then sets the value to 'Use Current Location' and turns it
 * italic
 */
function setCurrentLocationInput( name ) {
	// if the browser can geolocate
	if (canBrowserGeolocate()) {
        console.log( arguments.callee.name + ': browser can geolocate');
		// set the value of the the search box to use current location
		$(name).val('Use Current Location');
		//$('#searchaddress').val('5th and market');
		// and make the text italics
		$(name).css('font-style',"italic");
        $(name).css('text-align','center');
	}
    else {
        console.log( arguments.callee.name + ': browser cannot geolocate');
    }
}

/*
 *  delete the input text, set font style to normal, and text-align left
 */
function deleteInputText(name) {
	console.log( 'deleteInputText()' );
    $(name).val('');
    $(name).css('font-style',"normal");
    $(name).css('text-align','left');
}

/**
 * return true if the browser can geolocate the user's locaton
*/
function canBrowserGeolocate() {
    alert( 'browser can navigate: ' + navigator.geolocation );
    return navigator.geolocation;
}

/*
 * show the computer cetner location message.  useful for when loading
 * computer centers via ajax
 */
function showLoadingMsg( msg ) {
    $.mobile.loadingMessageTextVisible = true;
    $.mobile.showPageLoadingMsg("a", msg );
}

function hideLoadingMsg() {
    $.mobile.hidePageLoadingMsg();
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

function geocodeAddress(address) {
	console.log( 'geolocateAddress(): ' + address );
	
    geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 
        'address': address
        }, 
        function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                $('#lat').val( results[0].geometry.location.lat() );
                $('#lng').val( results[0].geometry.location.lng() );
            } else {
                alert("Could not geocode address, please modify and try again" );
                console.log( 'could not goeocode address, reason: ' + result );
                return false;
        }
    });
}



function showDayButtons( dayArray ) {
    
    html = '<fieldset data-role="controlgroup" data-type="horizontal" style="text-align:center">'+"\n";
    html += '<legend>Select the days these hours apply to</legend>'+"\n";
    html += '<p>';
    
    for( var i in dayArray) {
        day = dayArray[i];
        html += '<label for="'+day+'Hours">'+day+'</label>'+"\n";
        html += '<input type="checkbox" name="searchOptions" id="'+day+'Hours" value="'+day+'"/>'+"\n";
    }
    
    html += '</p>'+"\n";
    html += '</fieldset>'+"\n";
    
    console.log( html );
    
    $('#checkbox-days').html( html );
}

if( typeof( ConnectUtils ) == "undefined" ) ConnectUtils = {};

ConnectUtils = function() {}

ConnectUtils.capitaliseFirstLetter = function(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}


if( typeof( CenterHours ) == "undefined" ) CenterDay = {};

CenterDay = function(day,openTime,closeTime) {
    this.day = day;
    this.openTime = openTime;
    this.closeTime = closeTime;
    this.hoursDescription = openTime + " to " + closeTime;
}

CenterDay.prototype.toString = function() {
    return "CenterDay ["+this.day+"]";
}
    

if( typeof( AddCenter ) == "undefined" ) AddCenter = {};


AddCenter = function() {
    this.pendingCenter = {};
    this.pendingDays = [];
}

AddCenter.prototype.test = function() {
    console.log( "inside test()" );
    console.log( this.pendingDays );
}

/*
 *returns the days that have hours set
 */
AddCenter.prototype.getPendingDays = function() {
    //this.pendingDays.sort();
    
    /*function( a,b) {
        if( a.day.charAt(0) < b.day.charAt(0) ) {
            return 1;
        }
        else if( a.day.charAt(0) < b.day.charAt(0) ) {
            return -1;
        }
        else {
            return 0;
        }
    });*/
    return this.pendingDays;
}

/*
 *return the pending day given the daystring.  daystring should be full name
 *of day
 */
AddCenter.prototype.getPendingDay = function( dayString ) {
   dayString = dayString.toLowerCase();
   
   for( i in this.pendingDays ) {
       
       if( dayString == this.pendingDays[i].day ) {
           return this.pendingDays[i];
       }
   }
   
   return null;
}

AddCenter.prototype.submitCenter = function() {

        var locationTitle = $('input[id=locationTitle]').val();
        var address1 = $('input[id=address1]').val();

        var computerCenter = {};
        computerCenter.locationTitle = locationTitle;
        computerCenter.address1 = address1;

        console.log( 'my json object:' + computerCenter );
        jsonStr = JSON.stringify(computerCenter);
        console.log( 'my json object:' + jsonStr );

        showLoadingMsg('Submitting computer center');
        $.ajax({
            url: '/m/add-center/add',
            data: jsonStr,
            type: 'post',
            success: function() {
                console.log( 'javascript sucess function' );
            },
            error: function() {
                console.log( 'adding center did not work' );
            }
        }).done( function() { hideLoadingMsg(); });    

        $.mobile.changePage( '#success-page' );
}
    
/**
 * return the names of the days that don't have hours assigned
 */
AddCenter.prototype.getUndefinedHoursDays = function() {
        
        days = [];
        if( this.pendingDays ) 
        {
            for( i in this.getDaysOfTheWeek() ) {
                day = this.getDaysOfTheWeek()[i]
                
                pendingDay = this.getPendingDay(day);
                
                if( pendingDay == null ) {
                    days.push(day);
                }
            }
        }
        else 
        {
            days = this.getDaysOfTheWeek();
        }
        
        return days;
}

/**
* return the days of the week in an array starting with monday
*/
AddCenter.prototype.getDaysOfTheWeek = function() {
    return [ "monday","tuesday","wednesday", "thursday", "friday",
                    "saturday","sunday" ];
}

/**
 * store the hours from the hours form in a javascript global variable
 */
AddCenter.prototype.storeHours = function(openTime, closeTime, days ) {

    if( this.pendingDays == null ) {
        this.pendingDays = [];
    }
    
    for( var key in days ) 
    {
        var day = days[key];
        centerDay = new CenterDay(day,openTime,closeTime);
        this.pendingDays.push( centerDay );
    }
}

AddCenter.prototype.getDayAbbreviations = function () {
    var dayAbrev = {};
    
    dayAbrev.monday = "Mon";
    dayAbrev.tuesday = "Tue";
    dayAbrev.wednesday = "Wed";
    dayAbrev.thursday = "Thur";
    dayAbrev.friday = "Fri";
    dayAbrev.saturday = "Sat";
    dayAbrev.sunday = "Sun";
    
    return dayAbrev;
}