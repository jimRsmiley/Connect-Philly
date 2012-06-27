function ComputerCenter() {
	this.locationTitle;
	this.address1;
	
	this.sundayHoursOpen;
	this.sundayHoursClose;	
};

function CenterRequest() {
	this.lat;
	this.lng;
};

function CenterRequestJSONBuilder() {
	
	this.getJson = function( centerRequest ) {
		var retVal = '[';
			+ '{"lat":"'+centerRequest.lat+'"}'
			+ '{"lng":"'+centerRequest.lng+'"}'
			+ ']';
			
		return retVal;
	};
};