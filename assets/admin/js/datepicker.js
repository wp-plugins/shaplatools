(function( $ ) {
	
	//Initializing jQuery UI Datepicker
	$( '#start_date' ).datepicker({
		dateFormat: 'MM dd, yy',
		changeMonth: true,
		changeYear: true,
		onClose: function( selectedDate ){
			$( '#start_date' ).datepicker( 'option', 'minDate', selectedDate );
		}
	});
	$( '#end_date' ).datepicker({
		dateFormat: 'MM dd, yy',
		changeMonth: true,
		changeYear: true,
		onClose: function( selectedDate ){
			$( '#end_date' ).datepicker( 'option', 'maxDate', selectedDate );
		}
	});
	$( '#_shapla_portfolio_date' ).datepicker({
		dateFormat: 'MM dd, yy',
		changeMonth: true,
		changeYear: true,
		onClose: function( selectedDate ){
			$( '#_shapla_portfolio_date' ).datepicker( 'option', 'maxDate', selectedDate );
		}
	});

})( jQuery );