<?php
/**
* Customizer actions and filters
*
* 
* @package      FPC
* @subpackage   classes
* @since        1.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
*/

class TC_back_fpc {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

		add_action ( 'customize_register'				, array( $this , 'tc_add_controls_class' ) ,10,1);
		add_action ( 'customize_controls_enqueue_scripts'	, array( $this , 'tc_customize_controls_js_css' ));
		add_action ( 'customize_register'				, array( $this , 'tc_customize_register' ) , 20, 1 );
		add_action ( 'customize_preview_init'			, array( $this , 'tc_customize_preview_js' ));
		add_filter( 'plugin_action_links' 				, array( $this , 'tc_plugin_action_links' ), 10 , 2 );
    }


    function tc_plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( dirname( dirname( dirname(__FILE__) ) ).'/featured-pages-customizer.php' ) ) {
			$links[] = '<a href="' . admin_url( 'customize.php' ) . '">'.__( 'Settings' ).'</a>';
		}
		return $links;
	}


	function tc_add_controls_class( $type) {
		require_once ( dirname( __FILE__ ) . '/class_controls_fpc.php');
	}


	function tc_customize_register( $wp_customize) {
		return $this -> tc_customize_factory ( $wp_customize , $args = $this -> tc_customize_arguments(), $setup = TC_utils_fpc::$instance -> tc_customizer_map() );
	}


	function tc_customize_arguments() {
		$args = array(
				'sections' => array(
							'title' ,
							'priority' ,
							'description'
				),
				'settings' => array(
							'default'			=>	null,
							'capability'		=>	'manage_options' ,
							'setting_type'		=>	'option' ,
							'sanitize_callback'	=>	null,
							'transport'			=>	null
				),
				'controls' => array(
							'title' ,
							'label' ,
							'section' ,
							'settings' ,
							'type' ,
							'choices' ,
							'priority' ,
							'sanitize_callback' ,
							'notice' ,
							'buttontext' ,//button specific
							'link' ,//button specific
							'step' ,//number specific
							'min' ,//number specific
							'range-input' ,
							'max',
							'cssid',
							'slider_default',
				)
		);
		return apply_filters( 'fpc_customizer_arguments', $args );
	}





	/**
	 * Generates customizer
	 */
	function tc_customize_factory ( $wp_customize , $args, $setup ) {

		//remove sections
		if ( isset( $setup['remove_section'])) {
			foreach ( $setup['remove_section'] as $section) {
				$wp_customize	-> remove_section( $section);
			}
		}

		//add sections
		if ( isset( $setup['add_section'])) {
			foreach ( $setup['add_section'] as  $key => $options) {
				//generate section array
				$option_section = array();

				foreach( $args['sections'] as $sec) {
					$option_section[$sec] = isset( $options[$sec]) ?  $options[$sec] : null;
				}

				//add section
				$wp_customize	-> add_section( $key,$option_section);
			}//end foreach
		}//end if


		//get_settings
		if ( isset( $setup['get_setting'])) {
			foreach ( $setup['get_setting'] as $setting) {
				$wp_customize	-> get_setting( $setting )->transport = 'postMessage';
			}
		}

		//add settings and controls
		if ( isset( $setup['add_setting_control'])) {

			foreach ( $setup['add_setting_control'] as $key => $options) {
				//isolates the option name for the setting's filter
				$f_option_name = 'setting';
				$f_option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
	            if ( isset( $match[1][0] ) ) {$f_option_name = $match[1][0];}

				//declares settings array
				$option_settings = array();
				foreach( $args['settings'] as $set => $set_value) {
					if ( $set == 'setting_type' ) {
						$option_settings['type'] = isset( $options['setting_type']) ?  $options['setting_type'] : $args['settings'][$set];
						$option_settings['type'] = apply_filters( $f_option_name .'_customizer_set', $option_settings['type'] , $set );
					}
					else {
						$option_settings[$set] = isset( $options[$set]) ?  $options[$set] : $args['settings'][$set];
						$option_settings[$set] = apply_filters( $f_option_name .'_customizer_set' , $option_settings[$set] , $set );
					}
				}

				//add setting
				$wp_customize	-> add_setting( $key, $option_settings );
			
				//generate controls array
				$option_controls = array();
				foreach( $args['controls'] as $con) {
					$option_controls[$con] = isset( $options[$con]) ?  $options[$con] : null;
				}

				//add control with a dynamic class instanciation if not default
				if(!isset( $options['control'])) {
						$wp_customize	-> add_control( $key,$option_controls );
				}
				else {
						$wp_customize	-> add_control( new $options['control']( $wp_customize, $key, $option_controls ));
				}

			}//end for each
		}//end if isset

	}//end of customize generator function





	/**
	 *  Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	function tc_customize_preview_js() {
		wp_enqueue_script( 
			'tc-fpc-preview' ,
			plugins_url( 'featured-pages-customizer/back/assets/js/fpc-customizer-preview.js' ),
			array( 'customize-preview' ),
			'20120827' ,
			true );

		//localizes
		wp_localize_script( 
	        'tc-fpc-preview', 
	        'TCFPCPreviewParams',
	        apply_filters('tc_fpc_js_preview_params' ,
		        array(
		        	'OptionPrefix' 	=> TC_fpc::$instance -> plug_option_prefix
		        )
		    )
        );
	}


	/**
	 * Add script to controls
	 * Dependency : customize-controls located in wp-includes/script-loader.php
	 * Hooked on customize_controls_enqueue_scripts located in wp-admin/customize.php
	 */
	function tc_customize_controls_js_css() {
		$plug_option_prefix 	= TC_fpc::$instance -> plug_option_prefix;

		wp_register_style( 
			'tc-fpc-controls-style' ,
			plugins_url( 'featured-pages-customizer/back/assets/css/fpc-customizer-control.css' ),
			array( 'customize-controls' ),
			null,
			$media = 'all'
		);
		wp_enqueue_style('tc-fpc-controls-style');

		wp_enqueue_script( 
			'tc-fpc-controls' ,
			plugins_url( 'featured-pages-customizer/back/assets/js/fpc-customizer-control.js' ),
			array( 'customize-controls' ),
			null ,
			true
			);

		//gets the featured pages id from init
		$fp_ids				= apply_filters( 'fpc_featured_pages_ids' , TC_fpc::$instance -> fpc_ids);

		//declares the common fp control fields and the dynamic arrays
		$fp_controls 			= array(
			"{$plug_option_prefix}[tc_fp_position]",
			"{$plug_option_prefix}[tc_show_featured_pages_img]",
			"{$plug_option_prefix}[tc_featured_page_button_text]",
			"{$plug_option_prefix}[tc_featured_page_button_color]",
			"{$plug_option_prefix}[tc_featured_page_background]",
			"{$plug_option_prefix}[tc_featured_page_text_color]"
		);
		$page_dropdowns 		= array();
		$text_fields			= array();

		//adds filtered page dropdown fields
		foreach ( $fp_ids as $id ) {
			$page_dropdowns[] 	= "{$plug_option_prefix}[tc_featured_page_{$id}]";
			$text_fields[]		= "{$plug_option_prefix}[tc_featured_text_{$id}]";
		}

		//localizes
		wp_localize_script( 
	        'tc-fpc-controls', 
	        'TCFPCControlParams',
	        apply_filters('tc_fpc_js_control_params' ,
		        array(
		        	'OptionPrefix' 	=> $plug_option_prefix,
		        	'FPControls' 	=> array_merge( $fp_controls , $page_dropdowns , $text_fields )
		        )
		    )
        );

		//adds some nice google fonts to the customizer
        wp_enqueue_style(
          'fpc-google-fonts', 
          $this-> tc_customizer_gfonts_url(), 
          array(), 
          null 
        );
	}



	/**
	* Builds Google Fonts url
	*/
	function tc_customizer_gfonts_url() {
      //declares the google font vars
      $fonts_url          = '';
      $font_families      = apply_filters( 'tc_fpc_customizer_google_fonts' , array('Raleway') );

      $query_args         = array(
          'family' => implode( '|', $font_families ),
          //'subset' => urlencode( 'latin,latin-ext' ),
      );

      $fonts_url          = add_query_arg( $query_args, "//fonts.googleapis.com/css" );

      return $fonts_url;
    }


	



}//end of class