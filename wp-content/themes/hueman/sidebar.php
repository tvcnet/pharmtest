<?php if ( 	
	( is_home() && ( ot_get_option('layout-home') != 'col-1c' ) ) ||
	( is_single() && ( ot_get_option('layout-single') != 'col-1c' ) ) ||
	( is_archive() && ( ot_get_option('layout-archive') != 'col-1c' ) ) ||
	( is_category() && ( ot_get_option('layout-archive-category') != 'col-1c' ) ) ||
	( is_search() && ( ot_get_option('layout-search') != 'col-1c' ) ) ||
	( is_404() && ( ot_get_option('layout-404') != 'col-1c' ) ) ||
	( is_page() && ( ot_get_option('layout-page') != 'col-1c' ) )
): ?>
<?php $sidebar = alx_sidebar_primary(); ?>

<div class="sidebar s1">
	
	<a class="sidebar-toggle" title="<?php _e('Expand Sidebar','hueman'); ?>"><i class="fa icon-sidebar-toggle"></i></a>
	
	<div class="sidebar-content">
		
		<div class="sidebar-top group">
			<p><?php _e('Follow:','hueman'); ?></p>
			<?php alx_social_links() ; ?>
		</div>
		
		<?php if ( ot_get_option( 'post-nav' ) == 's1') { get_template_part('inc/post-nav'); } ?>
		
		<?php if( is_page_template('page-templates/child-menu.php') ): ?>
		<ul class="child-menu group">
			<?php wp_list_pages('title_li=&sort_column=menu_order&depth=3'); ?>
		</ul>
		<?php endif; ?>
		
		<?php dynamic_sidebar($sidebar); ?>
		
	</div><!--/.sidebar-content-->
	
</div><!--/.sidebar-->

<?php alx_sidebar_dual(); ?>
	
	
<?php endif; ?>