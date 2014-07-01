<?php
/*
Template Name: Image Gallery
*/
?>

<?php get_header(); ?>
       
    <div id="content" class="page col-full fix">
		<div id="main" class="col-left">
                                                                            
		<?php if ( isset($woo_options[ 'woo_breadcrumbs_show' ]) && $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) { ?>
			<div id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</div><!--/#breadcrumbs -->
		<?php } ?>  

            <article <?php post_class('fix'); ?>>

			    <h1 class="title"><?php the_title(); ?></h1>
                
				<div class="entry">

		            <?php if (have_posts()) : the_post(); ?>
	            	<?php the_content(); ?>
		            <?php endif; ?>  

                <?php query_posts( 'showposts=60' ); ?>
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>				
                    <?php $wp_query->is_home = false; ?>

                    <?php woo_image( 'single=true&class=thumbnail alignleft' ); ?>
                
                <?php endwhile; endif; ?>	
                </div>

            </article><!-- /.post -->              
                                                            
		</div><!-- /#main -->
		
        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>