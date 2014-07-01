jQuery( document ).ready( function () {

	//setup the tooltip
	jQuery( '.nav-tab-wrapper' ).pointer(
		{
			content: '<h3>' + itsec_tooltip_text.header + '</h3>' + '<p>' + itsec_tooltip_text.text + '</p>',
			position: 'top',
			pointerWidth: 400,
			close: function () {

				var data = {
					action: 'itsec_tooltip_ajax',
					module: 'close',
					nonce: itsec_tooltip_text.nonce
				};

				//call the ajax
				jQuery.post( ajaxurl, data );

			}
		}
	).pointer( 'open' );

	//process tooltip actions
	jQuery( '.itsec_tooltip_ajax' ).click( function ( event ) {

		event.preventDefault();

		var module = jQuery( this ).attr( 'href' );
		var caller = this;

		var data = {
			action: 'itsec_tooltip_ajax',
			module: module,
			nonce: itsec_tooltip_text.nonce
		};

		//let user know we're working
		jQuery( caller ).removeClass( 'itsec_tooltip_ajax button-primary' ).addClass( 'button-secondary' ).html( 'Working...' );

		//call the ajax
		jQuery.post( ajaxurl, data, function ( response ) {

			if ( response == 'true' ) {

				jQuery( caller ).replaceWith( '<span class="itsec_tooltip_success">' + itsec_tooltip_text.messages[module].success + '</span>' );

			} else {

				jQuery( caller ).replaceWith( '<span class="itsec_tooltip_failure">' + itsec_tooltip_text.messages[module].failure + '</span>' );
			}

		} );

	} );

} );