<?php
/*
Template Name: Tags
*/
?>

<?php get_header(); ?>
       
    <div id="content" class="page col-full fix">
		<div id="main" class="fullwidth">
            
		<?php if ( isset($woo_options[ 'woo_breadcrumbs_show' ]) && $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) { ?>
			<div id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</div><!--/#breadcrumbs -->
		<?php } ?>  
                                                                        
            <article <?php post_class(); ?>>

			    <h1 class="title"><?php the_title(); ?></h1>
                
	            <?php if (have_posts()) : the_post(); ?>
            	<div class="entry">
            		<?php the_content(); ?>
            	</div>	            	
	            <?php endif; ?>  
	            
                <div class="tag_cloud">
        			<?php wp_tag_cloud( 'number=0' ); ?>
    			</div>

            </article><!-- /.post -->
        
		</div><!-- /#main -->
		
    </div><!-- /#content -->
		
<?php get_footer(); ?>