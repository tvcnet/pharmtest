<?php
/**
 * Homepage Slider
 */
	global $woo_options, $wp_query, $post, $panel_error_message;
	$exclude = array();
	$count = 0;
?>

<?php $slides = get_posts('suppress_filters=0&post_type=slide&showposts='.$woo_options[ 'woo_slider_entries' ]); ?>
<?php if (!empty($slides)) { ?>
    
	<div id="slides">
	    <ul class="slides fix">
	        
	            <?php foreach($slides as $post) : setup_postdata($post); $count++; ?>    
	            
	            <li id="slide-<?php echo $count; ?>" class="slide slide-id-<?php the_ID(); ?>">
	        		
	        		<div class="slide-img">
	    	    		<?php
	    	    		$slide_url = get_post_meta($post->ID, 'url', true );
	    	    		if ( isset($slide_url) && $slide_url != '' ) { ?>
	    	    		<a href="<?php echo $slide_url; ?>" title="<?php the_title_attribute(); ?>">
	    	    		<?php } // End If Statement
	    	    		$has_embed = woo_embed( 'width=1180&key=embed&class=slide-video&id='.$post->ID );
	        			if ( $has_embed ) {
	        				echo $has_embed; // Minus 6px off the width to cater for the 3px border.
	        			} else {
	        				woo_image( 'key=image&width=1180&class=slide-image&link=img&noheight=true' );
	        			}
	        			if ( isset($slide_url) && $slide_url != '' ) { ?>
	    	    		</a>
	    	    		<?php } // End If Statement
	        		?>
	    	    	</div>
	    	    	
	    	    	<div class="slide-content">
	    	    	
	    	    		<h2 class="title">
	    	    		<?php if ( isset($slide_url) && $slide_url != '' ) { ?>
	    	    		<a href="<?php echo $slide_url; ?>" rel="bookmark"  title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a><?php } else { the_title(); } ?></h2>
	       		     		
	       		     	<div class="entry">
	           		     	<?php the_excerpt(); ?>
						</div>
	    	    	
	    	    	</div>
	            	
	            </li><!--/.slide-->
	            
			<?php endforeach; ?> 
			
	    </ul><!-- /.slides -->
	    
	</div><!-- /#slides -->

<?php } else {
	$panel_error_message = __('Please add some slides in order to display the slider correctly.','woothemes');
    get_template_part( 'includes/panel-error' );
} ?>

<?php if ( get_option( 'woo_exclude' ) != $exclude ) { update_option( 'woo_exclude', $exclude ); } ?>

<?php 
// Slider Settings
if ( isset($woo_options['woo_slider_hover']) ) { $pauseOnHover = $woo_options['woo_slider_hover']; } else { $pauseOnHover = 'false'; }
if ( isset($woo_options['woo_slider_speed']) ) { $slideshowSpeed = $woo_options['woo_slider_speed']; } else { $slideshowSpeed = '7'; } // milliseconds
if ( isset($woo_options['woo_fade_speed']) ) { $animationDuration = $woo_options['woo_fade_speed']; } else { $animationDuration = '0.6'; } // milliseconds
?>	  
<script type="text/javascript">
   jQuery(window).load(function() {
   	jQuery('#slides').flexslider({
   		animation: "fade",
   		directionNav: false,
   		pauseOnHover: <?php echo $pauseOnHover; ?>,
   		<?php if ( $slideshowSpeed == "Off" ) { ?>
   		slideshow: false,
   		<? } else { ?>
   		slideshowSpeed: <?php echo $slideshowSpeed; ?> * 1000,
   		<?php } ?>
   		animationDuration: <?php echo $animationDuration; ?> * 1000
   	});
   	jQuery('#slides').addClass('loaded');
   });
</script>