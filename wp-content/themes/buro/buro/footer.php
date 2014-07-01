</div><!-- /#content-wrapper -->
<?php global $woo_options; ?>

	<?php
		if ( isset($woo_options[ 'woo_footer_sidebars' ]) ) { $total = $woo_options[ 'woo_footer_sidebars' ]; }
		if (!isset($total)) $total = 3;
		if ( ( woo_active_sidebar( 'footer-1') ||
			   woo_active_sidebar( 'footer-2') ||
			   woo_active_sidebar( 'footer-3') ||
			   ( isset($woo_options['woo_about_footer']) && $woo_options['woo_about_footer'] == 'true' )
			   ) && $total > 0 ) :

  	?>
	<div id="footer-widgets" class="col-<?php echo $total; ?>">
	
		<div class="col-full fix">
			<?php if ( isset($woo_options['woo_about_footer']) && $woo_options['woo_about_footer'] == 'true' ) { ?>
			<div id="about" class="fix">
    			
    			<?php woo_image('src='.$woo_options['woo_about_footer_image'].'&width=150&height=159'); ?>	
    		    
    		    <h3><?php echo stripslashes($woo_options['woo_about_footer_title']); ?></h3>
    		    
    		    <p><?php echo stripslashes(nl2br($woo_options['woo_about_footer_text'])); ?></p>
    		
    		</div><!-- /#about -->
    		<?php } ?>
    		<div id="widgets" class="<?php if ( $woo_options['woo_about_footer'] == 'false' ) { ?>no-about <?php } ?>fix">
			
				<?php $i = 0; while ( $i < $total ) : $i++; ?>
					<?php if ( woo_active_sidebar( 'footer-'.$i) ) { ?>
				
				<div class="block footer-widget-<?php echo $i; ?>">
        			<?php woo_sidebar( 'footer-'.$i); ?>
				</div>
				
	    		    <?php } ?>
				<?php endwhile; ?>
			
			</div><!-- /#widgets -->
		
		</div><!-- /.col-full -->
		
	</div><!-- /#footer-widgets  -->
    <?php endif; ?>
    
    <footer class="col-full">
    
    	<div>
			
			<?php if( isset($woo_options[ 'woo_footer' ]) && $woo_options[ 'woo_footer' ] == 'true' ) {
			
	    		echo stripslashes( $woo_options['woo_footer_text'] );
	    	
	    	} else { ?>
	    		
				<p><span>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo(); ?> <?php _e( 'All Rights Reserved.', 'woothemes' ); ?> ~  ~ <?php _e( 'Powered by', 'woothemes' ); ?> <a href="http://www.wordpress.org"><?php _e('WordPress','woothemes'); ?></a>. <?php _e( 'Designed by', 'woothemes' ); ?> <a href="<?php echo ( !empty( $woo_options['woo_footer_aff_link'] ) ? esc_url( $woo_options['woo_footer_aff_link'] ) : 'http://www.woothemes.com' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/woothemes.png" width="74" height="19" alt="Woo Themes" /></a></p>
			
			<?php } ?>
	    				
		</div>
		
    </footer>

</div><!-- /#wrapper -->
<?php wp_footer(); ?>
<?php woo_foot(); ?>
</body>
</html>