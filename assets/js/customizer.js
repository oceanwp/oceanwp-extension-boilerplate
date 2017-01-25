/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Declare vars
	var api = wp.customize;

	api( 'oeb_color_control', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'background-color', to );
		} );
	} );

	api( 'oeb_range_control', function( value ) {
		value.bind( function( to ) {
			var $child = $( '.customizer-oeb_range_control' );
			if ( to ) {
				var style = '<style class="customizer-oeb_range_control">#main #content-wrap{padding: ' + to + 'px 0;}</style>';
				if ( $child.length ) {
					$child.replaceWith( style );
				} else {
					$( 'head' ).append( style );
				}
			} else {
				$child.remove();
			}
		} );
	} );
	
} )( jQuery );
