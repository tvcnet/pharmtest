jQuery(document).ready(function($){

/*-----------------------------------------------------------------------------------*/
/* Feedback slide/fade setup. */
/*-----------------------------------------------------------------------------------*/
	if ( jQuery( '.feedback' ).length ) {
		jQuery( '.feedback' ).each( function () {
			var effect = 'none';
			var autoPlayInterval = 5000;
			
			if ( jQuery( this ).hasClass( 'fade' ) ) { effect = 'fade'; }
			if ( jQuery( this ).find( 'input[name="speed"]' ).length ) {
				autoPlayInterval = parseInt( jQuery( this ).find( 'input[name="speed"]' ).attr( 'value' ) );
				jQuery( this ).find( 'input[name="speed"]' ).remove();
			}
			
			if ( effect != 'none' ) {
				jQuery( this ).slides({
					container: 'feedback-list',
					generateNextPrev: false,
					effect: effect, 
					play: autoPlayInterval, 
					fadeSpeed: 350, 
					autoHeight: true, 
					generatePagination: false,
					paginationClass: 'pagination', 
					hoverPause: true, 
					animationComplete: function () { jQuery( this ).stop(); }, 
					slidesLoaded: function () { jQuery( '.feedback-list .slides_control' ).css( 'height', jQuery( '.feedback-list .quote:first' ).height() ); }
				});
			}
		});
	}				

/*-----------------------------------------------------------------------------------*/
/* Make sure feedback widgets have the correct width on each feedback item. */
/*-----------------------------------------------------------------------------------*/

	if ( jQuery( '.widget_woo_feedback .feedback-list' ).length ) {
		jQuery('.widget_woo_feedback .feedback-list' ).each( function () {
			var width = jQuery( this ).parent().width();
			if ( width ) {
				jQuery( this ).find( '.quote' ).css( 'width', width + 'px' );
			}	
		});
	}
							
}); // End jQuery()