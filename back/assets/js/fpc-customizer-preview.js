
( function( $ ) {
	var OptionPrefix 	= TCFPCPreviewParams.OptionPrefix;
	var CurrentBtnColor = $( '.fp-button' ).attr('data-color');

	//show image
	wp.customize( OptionPrefix + '[tc_show_featured_pages_img]' , function( value ) {
		value.bind( function( to ) {
			if ( false == to ) {
				$( '.fpc-widget-front .thumb-wrapper' ).fadeOut('fast');
			} else {
				$( '.fpc-widget-front .thumb-wrapper' ).fadeIn('fast');
			}
		} );
	} );

	//button color
	wp.customize( OptionPrefix + '[tc_featured_page_button_color]' , function( value ) {
		value.bind( function( to ) {
			$( '.fp-button' ).removeClass(CurrentBtnColor);
			$( '.fp-button' ).addClass(to);
			CurrentBtnColor = to;
		} );
	} );

	//featured page background
	wp.customize( OptionPrefix + '[tc_featured_page_background]' , function( value ) {
		value.bind( function( to ) {
			$( '.round-div' ).css( 'border-color' , to );
			$( '.fpc-container' ).css( 'background-color' , to );
		} );
	} );

	//featured page text color
	wp.customize( OptionPrefix + '[tc_featured_page_text_color]' , function( value ) {
		value.bind( function( to ) {
			$( '.fpc-marketing .fpc-widget-front h2, .fpc-widget-front > p ' ).css( 'color' , to );
		} );
	} );

	//featured page one text
	wp.customize( OptionPrefix + '[tc_featured_text_one]' , function( value ) {
		value.bind( function( to ) {
			$( '.fpc-widget-front p.fp-text-one' ).html( to );
		} );
	} );

	//featured page two text
	wp.customize( OptionPrefix + '[tc_featured_text_two]' , function( value ) {
		value.bind( function( to ) {
			$( '.fpc-widget-front p.fp-text-two' ).html( to );
		} );
	} );

	//featured page three text
	wp.customize( OptionPrefix + '[tc_featured_text_three]' , function( value ) {
		value.bind( function( to ) {
			$( '.fpc-widget-front p.fp-text-three' ).html( to );
		} );
	} );

	//featured page button text
	wp.customize( OptionPrefix + '[tc_featured_page_button_text]' , function( value ) {
		value.bind( function( to ) {
			$( '.fp-button' ).html( to );
		} );
	} );

} )( jQuery );

