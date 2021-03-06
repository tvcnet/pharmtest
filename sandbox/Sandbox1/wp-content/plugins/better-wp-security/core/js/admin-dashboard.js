jQuery( document ).ready( function () {

	jQuery( '.itsec_toc_item_link' ).click( function ( event ) {

		event.preventDefault();

		var goto = jQuery( this ).attr( 'href' );

		console.log( goto );

		jQuery( 'html, body' ).animate(
			{
				scrollTop: jQuery( goto ).offset().top
			},
			1000
		);

	} );

	jQuery( '.itsec_return_to_top' ).click( function ( event ) {

		event.preventDefault();

		jQuery( 'html, body' ).animate(
			{
				scrollTop: jQuery( 'html, body' ).offset().top
			},
			500
		);

	} );

	jQuery( function () {
		jQuery( "#itsec_tabbed_dashboard_content" ).tabs();
	} );
	
	
	var toc_fixed = false;
	jQuery(window).scroll(function() {
		if( jQuery(this).scrollTop() >= 175 ) {
			if( ! toc_fixed ) {
				toc_fixed = true;
				jQuery('#global_table_of_contents').addClass('fixed');
			}
		} else {
			if( toc_fixed ) {
				toc_fixed = false;
				jQuery('#global_table_of_contents').removeClass('fixed');
			}
		}
	});

} );

function itsec_toc_select( value ) {

	if ( value ) {
		
		if ( jQuery( value ).hasClass( 'closed' ) ) {
			jQuery( value ).removeClass( 'closed' );
		}
		
		jQuery( 'html, body' ).animate(
			{
				scrollTop: jQuery( value ).offset().top - 50
			},
			500
		);
	}

}

if ( window.location.hash ) {

	var id = window.location.hash.substring( 1 );

	jQuery( window ).load( function () {

		var target_offset = jQuery( "#" + id ).offset();
		var target_top = target_offset.top;
		var scroll_target = jQuery( '#' + id ).parent().parent();
		var toggle_target = scroll_target.parents( '.postbox' );

		// open metabox if needed
		if ( toggle_target.hasClass( 'closed' ) ) {
			toggle_target.removeClass( 'closed' );
		}
		//scroll to setting and highlight it
		scroll_to_setting( scroll_target );
	} );

	function scroll_to_setting( scroll_target ) {

		var id = window.location.hash.substring( 1 );
		var target_offset = jQuery( "#" + id ).offset();
		var target_top = target_offset.top;

		jQuery( 'html, body' ).animate( { scrollTop: target_top - 100 }, 500 );
		jQuery( scroll_target ).animate( {
			                                 backgroundColor: '#ffffcb'
		                                 }, 1000 );

		setTimeout( function () {
			jQuery( scroll_target ).animate( {
				                                 backgroundColor: '#fff'
			                                 }, 1000 )
		}, 6000 );
	}

}