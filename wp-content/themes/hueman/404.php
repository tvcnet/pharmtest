<?php get_header(); ?>

<div class="main <?php echo alx_layout_class(); ?>">
	<div class="main-inner group">

		<section class="content">
		
			<?php get_template_part('inc/page-title'); ?>
			
			<div class="pad group">		
				
				<div class="notebox">
					<?php get_search_form(); ?>
				</div>
				
				<div class="entry">
					<p><?php _e( 'The page you trying to reach does not exist, or has been moved. Please use the menus or the search box to find what you are looking for.', 'hueman' ); ?></p>
				</div>
				
			</div><!--/.pad-->
			
		</section><!--/.content-->
		
		<?php get_sidebar(); ?>
	
	</div><!--/.main-inner-->
</div><!--/.main-->

<?php get_footer(); ?>