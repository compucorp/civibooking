
cj(function($) {
	cj('#printer-friendly').html('<div class="ui-icon ui-icon-print"></div>');
	cj('#printer-friendly').click(function(e) {
		var dateAsObject = cj('#dayview_select_date_display').datepicker('getDate');
		//set date value & print layout to url
		var url = CRM.url('civicrm/booking/day-view/print?snippet=2', {"date": dateAsObject.getTime()});
		window.open(url);
		e.preventDefault();
	});
});
