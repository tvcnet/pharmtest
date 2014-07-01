<?php
/*
Template Name: Full Width
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

            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
                                                                        
                <article <?php post_class(); ?>>

				    <h1 class="title"><?php the_title(); ?></h1>
                    
                    <div class="entry">
	                	<?php the_content(); ?>
	               	</div><!-- /.entry -->

					<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>

                </article><!-- /.post -->
                                                    
			<?php endwhile; else: ?>
				<article <?php post_class(); ?>>
                	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ) ?></p>
                </article><!-- /.post -->
            <?php endif; ?>  
        
		</div><!-- /#main -->
		
    </div><!-- /#content -->
		
<?php get_footer(); ?>