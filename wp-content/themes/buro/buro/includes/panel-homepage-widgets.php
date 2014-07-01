<?php 
/**
 * Homepage Widgets Panel
 */
global $woo_options;
?>
<div id="home-widgets" class="fix">
	<?php
    if ( woo_active_sidebar( 'homepage-left' ) ) { ?>
    	<div id="home-widgets-left">
    		<?php
    		woo_sidebar( 'homepage-left' ); 
    		?>
    	</div><!-- /#home-widgets-left -->
    	<?php
    }
    if ( woo_active_sidebar( 'homepage-right' ) ) { ?>
    	<div id="home-widgets-right" class="fix">
    		<?php
    		woo_sidebar( 'homepage-right' ); 
    		?>
    	</div><!-- /#home-widgets-righ -->
    	<?php
    }
    ?>
</div><!-- /#home-widgets -->