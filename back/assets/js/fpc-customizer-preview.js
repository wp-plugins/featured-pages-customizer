/*! Featured Pages Customizer preview by Nicolas Guillaume, GPL2+ licensed */
( function( $ ) {
	var OptionPrefix 		= TCFPCPreviewParams.OptionPrefix,
		CurrentBtnColor 	= $( '.fp-button' ).attr('data-color');


	//show image
	wp.customize( OptionPrefix + '[tc_show_fp_img]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$( '.fpc-widget-front .thumb-wrapper' ).addClass('fpc-hide');
			} else {
				$( '.fpc-widget-front .thumb-wrapper' ).removeClass('fpc-hide');
			}
		} );
	} );
	//featured page background
	wp.customize( OptionPrefix + '[tc_fp_background]' , function( value ) {
		value.bind( function( to ) {
			$( '.fpc-marketing .fpc-widget-front .round-div' ).attr('style' , 'border-color:' + to + '!important');
			$( '.fpc-container' ).attr('style' , 'background-color:' + to + '!important');
		} );
	} );

	//featured page text color
	wp.customize( OptionPrefix + '[tc_fp_text_color]' , function( value ) {
		value.bind( function( to ) {
			$( '.fpc-marketing .fpc-widget-front h2, .fpc-widget-front > p' ).attr('style' , 'color:' + to + '!important');
		} );
	} );

	//fp titles
	wp.customize( OptionPrefix + '[tc_show_fp_title]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$( '.fpc-widget-front .fp-title' ).addClass('fpc-hide');
			} else {
				$( '.fpc-widget-front .fp-title' ).removeClass('fpc-hide');
			}
		} );
	} );

	//fp excerpts
	wp.customize( OptionPrefix + '[tc_show_fp_text]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$( '.fpc-widget-front .fp-excerpt' ).addClass('fpc-hide');
			} else {
				$( '.fpc-widget-front .fp-excerpt' ).removeClass('fpc-hide');
			}
		} );
	} );
	//fp button
	wp.customize( OptionPrefix + '[tc_show_fp_button]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$( '.fpc-widget-front .fp-button' ).addClass('fpc-hide');
			} else {
				$( '.fpc-widget-front .fp-button' ).removeClass('fpc-hide');
			}
		} );
	} );
	//button color
	wp.customize( OptionPrefix + '[tc_fp_button_color]' , function( value ) {
		value.bind( function( to ) {
		    $( '.fp-button' ).removeClass(CurrentBtnColor).addClass(to);
			CurrentBtnColor = to;
		} );
	} );

	//featured page button text
	wp.customize( OptionPrefix + '[tc_fp_button_text]' , function( value ) {
		value.bind( function( to ) {
            if ( to )
                $( '.fpc-widget-front .fp-button' ).html( to ).removeClass( 'fpc-hide' );
            else
                $( '.fpc-widget-front .fp-button' ).addClass( 'fpc-hide' );
		} );
	} );

	//featured page button color
	wp.customize( OptionPrefix + '[tc_fp_button_text_color]' , function( value ) {
		value.bind( function( to ) {
			$( '.fpc-marketing .fpc-widget-front .fp-button' ).attr('style' , 'color:' + to + '!important');
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
} )(jQuery);
