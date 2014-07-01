<?php 
/**
 * Homepage Banner Panel
 */
global $woo_options, $panel_error_message; ?>
<?php if ( isset($woo_options['woo_homepage_banner_title']) && isset($woo_options['woo_homepage_banner_text']) && ( $woo_options['woo_homepage_banner_title'] != '' || $woo_options['woo_homepage_banner_text'] != '' )) { ?>
<div class="banner">
	<h3><?php echo stripslashes($woo_options['woo_homepage_banner_title']); ?></h3>
    <p><?php echo nl2br($woo_options['woo_homepage_banner_text']); ?></p>
    	    
    <span class="ribbon"></span>
    <span class="edge"></span>
</div>
<?php } else {
	$panel_error_message = __('Please setup the banner title and text in your Theme Options to display the banner correctly.','woothemes');
    get_template_part( 'includes/panel-error' );
} ?>