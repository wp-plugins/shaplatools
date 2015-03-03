(function( $ ) {
	
	//Initializing jQuery UI Datepicker
	$( '#shaplatools_event_start_date' ).datepicker({
		dateFormat: 'MM dd, yy',
		changeMonth: true,
		changeYear: true,
		onClose: function( selectedDate ){
			$( '#shaplatools_event_start_date' ).datepicker( 'option', 'minDate', selectedDate );
		}
	});
	$( '#shaplatools_event_end_date' ).datepicker({
		dateFormat: 'MM dd, yy',
		changeMonth: true,
		changeYear: true,
		onClose: function( selectedDate ){
			$( '#shaplatools_event_end_date' ).datepicker( 'option', 'maxDate', selectedDate );
		}
	});
	$( '#shaplatools_portfolio_date' ).datepicker({
		dateFormat: 'MM dd, yy',
		changeMonth: true,
		changeYear: true,
		onClose: function( selectedDate ){
			$( '#shaplatools_portfolio_date' ).datepicker( 'option', 'maxDate', selectedDate );
		}
	});

})( jQuery );