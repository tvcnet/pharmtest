<?php get_header(); ?>
<?php global $woo_options, $panel_error_message; ?>

    <div id="content" class="col-full">
    
    	<?php
    		$panel_count = 0;
			if ( isset( $woo_options['woo_slider'] ) && $woo_options['woo_slider'] == 'true' ) {		
				// Load the slider.
				get_template_part( 'includes/slider' );
				$panel_count++;
			}
		?>
    	
    	<?php 
    		if ( isset($woo_options['woo_homepage_recent_posts']) && $woo_options['woo_homepage_recent_posts'] == 'true' ) { 
    			// Load the recent posts panel.
				get_template_part( 'includes/panel-recent-posts' );
				$panel_count++;
    		} 
    	?>
    	
    	<?php 
    		if ( isset($woo_options['woo_homepage_banner']) && $woo_options['woo_homepage_banner'] == 'true' ) { 
    			// Load the homepage banner panel.
				get_template_part( 'includes/panel-banner' );
				$panel_count++;
    		} 
    	?>
    	
    	<?php 
    		if ( woo_active_sidebar( 'homepage-left' ) || woo_active_sidebar( 'homepage-right' ) ) { 
    			// Load the homepage widgets panel.
				get_template_part( 'includes/panel-homepage-widgets' );
				$panel_count++;
			} 
    	?>
    	
    	<?php 
    		// Display info message to tell user to setup theme options before continuing
    		if ( $panel_count == 0 ) {
    			$panel_error_message = __('Please setup your Theme Options before continuing.','woothemes');
    			get_template_part( 'includes/panel-error' );
    		}
    	?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>