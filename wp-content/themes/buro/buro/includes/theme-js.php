<?php
if ( ! is_admin() ) { add_action( 'wp_print_scripts', 'woothemes_add_javascript' ); }

if ( ! function_exists( 'woothemes_add_javascript' ) ) {
	function woothemes_add_javascript() {
	global $woo_options;
/*
		// Use Google jQuery minified script instead
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery',  'http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js');
*/
		wp_enqueue_script( 'jquery' );    
		wp_enqueue_script( 'general', get_template_directory_uri() . '/includes/js/general.js', array( 'jquery' ) );
		wp_enqueue_script( 'html5', get_template_directory_uri() . '/includes/js/html5.js', array( 'jquery' ) );
		wp_enqueue_script( 'fitvids', get_template_directory_uri() . '/includes/js/jquery.fitvids.js', array( 'jquery' ) );
		wp_register_script( 'slides', get_template_directory_uri() . '/includes/js/slides.min.jquery.js', array( 'jquery' ) );
		wp_register_script( 'flexslider', get_template_directory_uri() . '/includes/js/jquery.flexslider-min.js', array( 'jquery' ) );
		wp_register_script( 'woo-feedback', get_template_directory_uri() . '/includes/js/feedback.js', array( 'jquery', 'slides' ) );
		// Load the JavaScript for the slides and testimonals on the homepage.
		
		// Conditionally load the Feedback JavaScript, where needed.
		$load_feedback_js = false;
		
		if ( is_page_template( 'template-feedback.php' ) ) {
			$load_feedback_js = true;
		}
			 
		// Allow child themes/plugins to load the Feedback JavaScript when they need it.
		$load_feedback_js = apply_filters( 'woo_load_feedback_js', $load_feedback_js );

		if ( $load_feedback_js ) { wp_enqueue_script( 'woo-feedback' ); }
		
		do_action( 'woothemes_add_javascript' );
		
		if ( is_home() ){
		
			if ( isset( $woo_options['woo_slider'] ) && ( $woo_options['woo_slider'] == 'true' ) ) {
				wp_enqueue_script( 'flexslider' );
			}
			
			wp_enqueue_script( 'woo-feedback' );
			// Load the custom slider settings.
			
			$autoStart = false;
			$autoSpeed = 6;
			$slideSpeed = 0.5;
			$effect = 'slide';
			$nextprev = 'true';
			$pagination = 'true';
			$hoverpause = 'true';
			$autoheight = 'false';
			
			// Get our values from the database and, if they're there, override the above defaults.
			$fields = array(
							'autoStart' => 'auto', 
							'autoSpeed' => 'interval', 
							'slideSpeed' => 'speed', 
							'effect' => 'effect', 
							'nextprev' => 'nextprev', 
							'pagination' => 'pagination', 
							'hoverpause' => 'hover', 
							'autoHeight' => 'autoheight'
							);
			
			foreach ( $fields as $k => $v ) {
				if ( is_array( $woo_options ) && isset( $woo_options['woo_slider_' . $v] ) && $woo_options['woo_slider_' . $v] != '' ) {
					${$k} = $woo_options['woo_slider_' . $v];
				}
			}
			
			// Set auto speed to 0 if we want to disable automatic sliding.
			if ( $autoStart == 'false' ) {
				$autoSpeed = 0;
			}
			
			$data = array(
						'speed' => $slideSpeed, 
						'auto' => $autoSpeed, 
						'effect' => $effect, 
						'nextprev' => $nextprev, 
						'pagination' => $pagination, 
						'hoverpause' => $hoverpause, 
						'autoheight' => true
						);
						
			wp_localize_script( 'general', 'woo_slider_settings', $data );
		}
	}
}
?>