/**
 * SaaSSkul Admin & Customizer Real-time Preview logic
 */
( function( $ ) {

	// Update Header Background in real-time
	wp.customize( 'header_color', function( value ) {
		value.bind( function( newval ) {
			$( 'header' ).css( 'background', newval );
		} );
	} );

	// Update Primary Brand Color in real-time
	wp.customize( 'brand_color', function( value ) {
		value.bind( function( newval ) {
			$( '.button-primary, .ak-btn' ).css( 'background-color', newval );
		} );
	} );

} )( jQuery );
