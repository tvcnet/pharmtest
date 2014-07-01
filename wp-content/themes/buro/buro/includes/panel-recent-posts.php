<?php 
/**
 * Homepage Recent Posts Panel
 */
global $woo_options, $panel_error_message;
$entries = $woo_options[ 'woo_homepage_recent_posts_entries' ]; 
if (!isset($entries)) $entries = 3; 
// Tags
$feat_tags_array = explode(',',$woo_options['woo_homepage_recent_posts_tags']); // Tags to be shown
$tag_array = array();
foreach ($feat_tags_array as $key => $value){ 
    $tag_test = get_term_by( 'name', trim($value), 'post_tag', 'ARRAY_A' );
    if (isset($tag_test['term_id']) && $tag_test['term_id'] > 0 ) {
    	$tag_array[] = $tag_test['term_id'];
    }
}
// Query Args
$recent_posts_args = array(
    'tag__in' => $tag_array,
    'posts_per_page' => $entries
);
// Query
$recent_posts_query = new WP_Query( $recent_posts_args ); 
?>
<?php if ($recent_posts_query->have_posts()) { $count = 0; ?>
    <section id="latest-articles" class="fix">
    <?php while ($recent_posts_query->have_posts()) : $recent_posts_query->the_post(); $recent_posts_query++; $count++; ?>
    
    	<article <?php if ( $count % 3 == 0 ) { ?> class="last"<?php } ?>>
    		
    		<?php $img = woo_image('link=img&width=350&return=true&alt=Recent&noheight=true'); ?>
    	 	
    	 	<?php if ( $img != '' ) { ?><a class="image" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo $img; ?></a><?php } ?>
    	 	
    	 	<span class="post-meta"><?php _e( 'Posted on', 'woothemes' ) ?> <?php the_time( get_option( 'date_format' ) ); ?></span>
    	 	
    	 	<h2 class="title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
    	 	
    	 	<div class="entry">
    	 	
    	 		<?php the_excerpt(); ?>
    	 	
    	 	</div>
    	 	
    	 	<div class="post-more">
    	 		<span class="wrap">
    	 			<span class="comments"><?php comments_popup_link(__( 'Leave a comment', 'woothemes' ), __( '1 Comment', 'woothemes' ), __( '% Comments', 'woothemes' )); ?></span>
        			<span class="read-more"><a href="<?php the_permalink() ?>" title="<?php esc_attr_e( 'Read More', 'woothemes' ); ?>"><?php _e( 'Read More', 'woothemes' ); ?></a></span>	   			 			
    	 		</span>
    	 	</div><!-- /post-more -->
    	 
    	 </article>
    	 
    	 <?php if ( $count % 3 == 0 ) { ?>
    	 	<div class="fix"></div>
    	 <?php } ?>
    
    <?php endwhile; ?>
    </section>
<?php } else {
	$panel_error_message = __('Please add some posts in order to display your recent posts correctly.','woothemes');
    get_template_part( 'includes/panel-error' );
} ?>