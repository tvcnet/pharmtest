<?php
/*
Template Name: Child Menu
*/
?>
<?php get_header(); ?>
	
<div class="main <?php echo alx_layout_class(); ?>">
	<div class="main-inner group">

		<section class="content">
			
			<?php get_template_part('inc/page-title'); ?>
			
			<div class="pad group">
				
				<?php while ( have_posts() ): the_post(); ?>
				
					<article <?php post_class('group'); ?>>
						<?php get_template_part('inc/page-image'); ?>
						<div class="entry">
							<?php the_content(); ?>
							<div class="clear"></div>
						</div><!--/.entry-->
					</article>
					
					<?php if ( ot_get_option('page-comments') != '' ) { comments_template('/comments.php',true); } ?>
					
				<?php endwhile; ?>
				
			</div><!--/.pad-->
		</section><!--/.content-->
		
		<?php get_sidebar(); ?>
	
	</div><!--/.main-inner-->
</div><!--/.main-->

<?php get_footer(); ?>