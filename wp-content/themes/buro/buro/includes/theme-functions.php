<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Exclude categories from displaying on the "Blog" page template.
- Exclude categories from displaying on the homepage.
- CPT Slides
- Register WP Menus
- Page navigation
- Post Meta
- Subscribe & Connect
- Comment Form Fields
- Comment Form Settings

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Exclude categories from displaying on the "Blog" page template.
/*-----------------------------------------------------------------------------------*/

// Exclude categories on the "Blog" page template.
add_filter( 'woo_blog_template_query_args', 'woo_exclude_categories_blogtemplate' );

function woo_exclude_categories_blogtemplate ( $args ) {

	if ( ! function_exists( 'woo_prepare_category_ids_from_option' ) ) { return $args; }

	$excluded_cats = array();

	// Process the category data and convert all categories to IDs.
	$excluded_cats = woo_prepare_category_ids_from_option( 'woo_exclude_cats_blog' );

	// Homepage logic.
	if ( count( $excluded_cats ) > 0 ) {

		// Setup the categories as a string, because "category__not_in" doesn't seem to work
		// when using query_posts().

		foreach ( $excluded_cats as $k => $v ) { $excluded_cats[$k] = '-' . $v; }
		$cats = join( ',', $excluded_cats );

		$args['cat'] = $cats;
	}

	return $args;

} // End woo_exclude_categories_blogtemplate()

/*-----------------------------------------------------------------------------------*/
/* Exclude categories from displaying on the homepage.
/*-----------------------------------------------------------------------------------*/

// Exclude categories on the homepage.
add_filter( 'pre_get_posts', 'woo_exclude_categories_homepage' );

function woo_exclude_categories_homepage ( $query ) {

	if ( ! function_exists( 'woo_prepare_category_ids_from_option' ) ) { return $query; }

	$excluded_cats = array();

	// Process the category data and convert all categories to IDs.
	$excluded_cats = woo_prepare_category_ids_from_option( 'woo_exclude_cats_home' );

	// Homepage logic.
	if ( is_home() && ( count( $excluded_cats ) > 0 ) ) {
		$query->set( 'category__not_in', $excluded_cats );
	}

	$query->parse_query();

	return $query;

} // End woo_exclude_categories_homepage()

/*-----------------------------------------------------------------------------------*/
/* Custom Post Type - Slides */
/*-----------------------------------------------------------------------------------*/

add_action('init', 'woo_add_slides');
function woo_add_slides() 
{
  $labels = array(
    'name' => _x('Slides', 'post type general name', 'woothemes', 'woothemes'),
    'singular_name' => _x('Slide', 'post type singular name', 'woothemes'),
    'add_new' => _x('Add New', 'slide', 'woothemes'),
    'add_new_item' => __('Add New Slide', 'woothemes'),
    'edit_item' => __('Edit Slide', 'woothemes'),
    'new_item' => __('New Slide', 'woothemes'),
    'view_item' => __('View Slide', 'woothemes'),
    'search_items' => __('Search Slides', 'woothemes'),
    'not_found' =>  __('No slides found', 'woothemes'),
    'not_found_in_trash' => __('No slides found in Trash', 'woothemes'), 
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_icon' => get_template_directory_uri() .'/includes/images/slides.png',
    'menu_position' => null,
    'supports' => array('title','editor','thumbnail', /*'author','thumbnail','excerpt','comments'*/)
  ); 
  register_post_type('slide',$args);
}

/*-----------------------------------------------------------------------------------*/
/* Register WP Menus */
/*-----------------------------------------------------------------------------------*/
if ( function_exists( 'wp_nav_menu') ) {
	add_theme_support( 'nav-menus' );
	register_nav_menus( array( 'primary-menu' => __( 'Primary Menu', 'woothemes' ) ) );
	register_nav_menus( array( 'top-menu' => __( 'Top Menu', 'woothemes' ) ) );
}


/*-----------------------------------------------------------------------------------*/
/* Page navigation */
/*-----------------------------------------------------------------------------------*/
if (!function_exists( 'woo_pagenav')) {
	function woo_pagenav() {

		global $woo_options;

		// If the user has set the option to use simple paging links, display those. By default, display the pagination.
		if ( array_key_exists( 'woo_pagination_type', $woo_options ) && $woo_options[ 'woo_pagination_type' ] == 'simple' ) {
			if ( get_next_posts_link() || get_previous_posts_link() ) {
		?>
            <div class="nav-entries">
            	<span class="nav-prev fl">
                	<?php next_posts_link( ''. __( '<span class="meta-nav">&larr;</span> Older posts', 'woothemes' ) . '' ); ?>
                </span>
                <span class="nav-next fr">
                <?php previous_posts_link( ''. __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'woothemes' ) . '' ); ?>
                </span>
                <div class="fix"></div>
            </div>
		<?php
			}
		} else {
			woo_pagination();

		} // End IF Statement

	} // End woo_pagenav()
} // End IF Statement

/*-----------------------------------------------------------------------------------*/
/* WooTabs - Popular Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists( 'woo_tabs_popular')) {
	function woo_tabs_popular( $posts = 5, $size = 45 ) {
		global $post;
		$popular = get_posts( 'ignore_sticky_posts=1&orderby=comment_count&showposts='.$posts);
		foreach($popular as $post) :
			setup_postdata($post);
	?>
	<li>
		<?php if ($size <> 0) woo_image( 'height='.$size.'&width='.$size.'&class=thumbnail&single=true' ); ?>
		<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php endforeach;
	}
}


/*-----------------------------------------------------------------------------------*/
/* Post Meta */
/*-----------------------------------------------------------------------------------*/

if (!function_exists( 'woo_post_meta')) {
	function woo_post_meta( ) {
?>
<p class="post-meta">
    <span class="post-date"><span class="small"><?php _e( 'Posted on', 'woothemes' ) ?></span> <?php the_time( get_option( 'date_format' ) ); ?></span>
    <span class="post-author"><span class="small"><?php _e( 'by', 'woothemes' ) ?></span> <?php the_author_posts_link(); ?></span>
    <span class="post-category"><span class="small"><?php _e( 'in', 'woothemes' ) ?></span> <?php the_category( ', ') ?></span>
    <?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>
</p>
<?php
	}
}


/*-----------------------------------------------------------------------------------*/
/* Subscribe / Connect */
/*-----------------------------------------------------------------------------------*/

if (!function_exists( 'woo_subscribe_connect')) {
	function woo_subscribe_connect($widget = 'false', $title = '', $form = '', $social = '') {

		global $woo_options;

		// Setup title
		if ( isset($woo_options[ 'woo_connect_title' ]) && $widget != 'true' )
			$title = $woo_options[ 'woo_connect_title' ];

		// Setup related post (not in widget)
		$related_posts = '';
		if ( isset($woo_options[ 'woo_connect_related' ]) && $woo_options[ 'woo_connect_related' ] == "true" && $widget != "true" )
			$related_posts = do_shortcode( '[related_posts limit="5"]' );

?>
	<?php if ( isset($woo_options[ 'woo_connect' ]) && $woo_options[ 'woo_connect' ] == "true" || $widget == 'true' ) : ?>
	<div id="connect" class="fix">
		<h3><?php if ( $title ) echo apply_filters( 'widget_title', $title ); else _e('Subscribe','woothemes'); ?></h3>

		<div <?php if ( $related_posts != '' ) echo 'class="col-left"'; ?>>
			<p><?php if ($woo_options[ 'woo_connect_content' ] != '') echo stripslashes($woo_options[ 'woo_connect_content' ]); else _e( 'Subscribe to our e-mail newsletter to receive updates.', 'woothemes' ); ?></p>

			<?php if ( $woo_options[ 'woo_connect_newsletter_id' ] != "" AND $form != 'on' ) : ?>
			<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open( 'http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $woo_options[ 'woo_connect_newsletter_id' ]; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520' );return true">
				<input class="email" type="text" name="email" value="<?php esc_attr_e( 'E-mail', 'woothemes' ); ?>" onfocus="if (this.value == '<?php _e( 'E-mail', 'woothemes' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'E-mail', 'woothemes' ); ?>';}" />
				<input type="hidden" value="<?php echo $woo_options[ 'woo_connect_newsletter_id' ]; ?>" name="uri"/>
				<input type="hidden" value="<?php bloginfo( 'name' ); ?>" name="title"/>
				<input type="hidden" name="loc" value="en_US"/>
				<input class="submit" type="submit" name="submit" value="<?php _e( 'Submit', 'woothemes' ); ?>" />
			</form>
			<?php endif; ?>

			<?php if ( $woo_options['woo_connect_mailchimp_list_url'] != "" AND $form != 'on' AND $woo_options['woo_connect_newsletter_id'] == "" ) : ?>
			<!-- Begin MailChimp Signup Form -->
			<div id="mc_embed_signup">
				<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="<?php echo $woo_options['woo_connect_mailchimp_list_url']; ?>" method="post" target="popupwindow" onsubmit="window.open('<?php echo $woo_options['woo_connect_mailchimp_list_url']; ?>', 'popupwindow', 'scrollbars=yes,width=650,height=520');return true">
					<input type="text" name="EMAIL" class="required email" value="<?php _e('E-mail','woothemes'); ?>"  id="mce-EMAIL" onfocus="if (this.value == '<?php _e('E-mail','woothemes'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail','woothemes'); ?>';}">
					<input type="submit" value="<?php _e('Submit', 'woothemes'); ?>" name="subscribe" id="mc-embedded-subscribe" class="btn submit button">
				</form>
			</div>
			<!--End mc_embed_signup-->
			<?php endif; ?>

			<?php if ( $social != 'on' ) : ?>
			<div class="social<?php if ( $related_posts == '' AND $woo_options[ 'woo_connect_newsletter_id' ] != "" ) echo ' fr'; ?>">
		   		<?php if ( $woo_options[ 'woo_connect_rss' ] == "true" ) { ?>
		   		<a href="<?php if ( $woo_options['woo_feed_url'] ) { echo esc_url( $woo_options['woo_feed_url'] ); } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-rss.png" title="<?php _e('Subscribe to our RSS feed', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_twitter' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $woo_options['woo_connect_twitter'] ); ?>" class="twitter"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-twitter.png" title="<?php _e('Follow us on Twitter', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_facebook' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $woo_options['woo_connect_facebook'] ); ?>" class="facebook"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-facebook.png" title="<?php _e('Connect on Facebook', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_youtube' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $woo_options['woo_connect_youtube'] ); ?>" class="youtube"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-youtube.png" title="<?php _e('Watch on YouTube', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_flickr' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $woo_options['woo_connect_flickr'] ); ?>" class="flickr"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-flickr.png" title="<?php _e('See photos on Flickr', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_linkedin' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $woo_options['woo_connect_linkedin'] ); ?>" class="linkedin"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-linkedin.png" title="<?php _e('Connect on LinkedIn', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_delicious' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $woo_options['woo_connect_delicious'] ); ?>" class="delicious"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-delicious.png" title="<?php _e('Discover on Delicious', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_googleplus' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $woo_options['woo_connect_googleplus'] ); ?>" class="googleplus"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-googleplus.png" title="<?php _e('View Google+ profile', 'woothemes'); ?>" alt=""/></a>

				<?php } ?>
			</div>
			<?php endif; ?>

		</div><!-- col-left -->

		<?php if ( $woo_options[ 'woo_connect_related' ] == "true" AND $related_posts != '' ) : ?>
		<div class="related-posts col-right">
			<h4><?php _e( 'Related Posts:', 'woothemes' ); ?></h4>
			<?php echo $related_posts; ?>
		</div><!-- col-right -->
		<?php wp_reset_query(); endif; ?>

	</div>
	<?php endif; ?>
<?php
	}
}

/*-----------------------------------------------------------------------------------*/
/* Comment Form Fields */
/*-----------------------------------------------------------------------------------*/

	add_filter( 'comment_form_default_fields', 'woo_comment_form_fields' );

	if ( ! function_exists( 'woo_comment_form_fields' ) ) {
		function woo_comment_form_fields ( $fields ) {

			$commenter = wp_get_current_commenter();

			$required_text = ' <span class="required">(' . __( 'Required', 'woothemes' ) . ')</span>';

			$req = get_option( 'require_name_email' );
			$aria_req = ( $req ? " aria-required='true'" : '' );
			$fields =  array(
				'author' => '<p class="comment-form-author">' .
							'<input id="author" class="txt" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />' .
							'<label for="author">' . __( 'Name' ) . ( $req ? $required_text : '' ) . '</label> ' .
							'</p>',
				'email'  => '<p class="comment-form-email">' .
				            '<input id="email" class="txt" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />' .
				            '<label for="email">' . __( 'Email' ) . ( $req ? $required_text : '' ) . '</label> ' .
				            '</p>',
				'url'    => '<p class="comment-form-url">' .
				            '<input id="url" class="txt" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' .
				            '<label for="url">' . __( 'Website' ) . '</label>' .
				            '</p>',
			);

			return $fields;

		} // End woo_comment_form_fields()
	}

/*-----------------------------------------------------------------------------------*/
/* Comment Form Settings */
/*-----------------------------------------------------------------------------------*/

	add_filter( 'comment_form_defaults', 'woo_comment_form_settings' );

	if ( ! function_exists( 'woo_comment_form_settings' ) ) {
		function woo_comment_form_settings ( $settings ) {

			$settings['comment_notes_before'] = '';
			$settings['comment_notes_after'] = '';
			$settings['label_submit'] = __( 'Submit Comment', 'woothemes' );
			$settings['cancel_reply_link'] = __( 'Click here to cancel reply.', 'woothemes' );

			return $settings;

		} // End woo_comment_form_settings()
	}

	/*-----------------------------------------------------------------------------------*/
	/* Misc back compat */
	/*-----------------------------------------------------------------------------------*/

	// array_fill_keys doesn't exist in PHP < 5.2
	// Can remove this after PHP <  5.2 support is dropped
	if ( !function_exists( 'array_fill_keys' ) ) {
		function array_fill_keys( $keys, $value ) {
			return array_combine( $keys, array_fill( 0, count( $keys ), $value ) );
		}
	}

/*-----------------------------------------------------------------------------------*/
/* Custom Post Type - Feedback (Feedback Component) */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_add_feedback' ) ) {
	function woo_add_feedback() {
		global $woo_options;
		
		if ( ( isset( $woo_options['woo_feedback_disable'] ) && $woo_options['woo_feedback_disable'] == 'true' ) ) { return; }
		
		$labels = array(
			'name' => _x( 'Feedback', 'post type general name', 'woothemes' ),
			'singular_name' => _x( 'Feedback Item', 'post type singular name', 'woothemes' ),
			'add_new' => _x( 'Add New', 'slide', 'woothemes' ),
			'add_new_item' => __( 'Add New Feedback Item', 'woothemes' ),
			'edit_item' => __( 'Edit Feedback Item', 'woothemes' ),
			'new_item' => __( 'New Feedback Item', 'woothemes' ),
			'view_item' => __( 'View Feedback Item', 'woothemes' ),
			'search_items' => __( 'Search Feedback Items', 'woothemes' ),
			'not_found' =>  __( 'No Feedback Items found', 'woothemes' ),
			'not_found_in_trash' => __( 'No Feedback Items found in Trash', 'woothemes' ), 
			'parent_item_colon' => ''
		);
		
		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true, 
			'_builtin' => false,
			'show_ui' => true, 
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_icon' => get_template_directory_uri() .'/includes/images/feedback.png',
			'menu_position' => null,
			'supports' => array( 'title', 'editor'/*, 'author', 'thumbnail', 'excerpt', 'comments'*/ ),
		);
		
		register_post_type( 'feedback', $args );

	} // End woo_add_feedback()
}

add_action( 'init', 'woo_add_feedback', 10 );

/*-----------------------------------------------------------------------------------*/
/* Woo Feedback, woo_get_feedback_entries() */
/*
/* Get feedback entries.
/*
/* @param array/string $args
/* @since 4.5.0
/* @return array/boolean
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_get_feedback_entries' ) ) {
	function woo_get_feedback_entries ( $args = '' ) {
		$defaults = array(
			'limit' => 5, 
			'orderby' => 'post_date', 
			'order' => 'DESC', 
			'id' => 0
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'woo_get_feedback_args', $args );
		
		// The Query Arguments.
		$query_args = array();
		$query_args['post_type'] = 'feedback';
		$query_args['numberposts'] = $args['limit'];
		$query_args['orderby'] = $args['orderby'];
		$query_args['order'] = $args['order'];
		
		if ( is_numeric( $args['id'] ) && ( intval( $args['id'] ) > 0 ) ) {
			$query_args['p'] = intval( $args['id'] );
		}
		
		// Whitelist checks.
		if ( ! in_array( $query_args['orderby'], array( 'none', 'ID', 'author', 'title', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order', 'meta_value', 'meta_value_num' ) ) ) {
			$query_args['orderby'] = 'date';
		}
		
		if ( ! in_array( $query_args['order'], array( 'ASC', 'DESC' ) ) ) {
			$query_args['order'] = 'DESC';
		}
		
		if ( ! in_array( $query_args['post_type'], get_post_types() ) ) {
			$query_args['post_type'] = 'feedback';
		}
		
		// The Query.
		$query = get_posts( $query_args );
		
		// The Display.
		if ( ! is_wp_error( $query ) && is_array( $query ) && count( $query ) > 0 ) {} else {
			$query = false;
		}
		
		return $query;
		
	} // End woo_get_feedback_entries()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Feedback, woo_display_feedback_entries() */
/*
/* Display posts of the "feedback" post type.
/*
/* @param array/string $args
/* @since 4.5.0
/* @return string $html (if "echo" not set to true)
/* @uses woo_get_feedback_entries()
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_display_feedback_entries' ) ) {
	function woo_display_feedback_entries ( $args = '' ) {
		$defaults = array(
			'limit' => 5, 
			'speed' => 7000, 
			'orderby' => 'rand', 
			'order' => 'DESC', 
			'id' => 0, 
			'display_author' => true, 
			'display_page_template_url' => 0,
			'display_url' => true, 
			'page_url' => 0, 
			'effect' => 'fade', // Options: 'fade', 'none'
			'pagination' => false, 
			'echo' => true
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'woo_display_feedback_args', $args );
		
		$html = '';
		
		woo_do_atomic( 'woo_feedback_before', $args );
		
		// The Query.
		$query = woo_get_feedback_entries( $args );
		
		// The Display.
		if ( ! is_wp_error( $query ) && is_array( $query ) && count( $query ) > 0 ) {
			
			if ( $args['effect'] != 'none' ) {
				$effect = ' ' . $args['effect'];
			}
			
			$html .= '<div class="feedback' . $effect . '">' . "\n";
			$html .= '<input type="hidden" name="speed" value="' . intval( $args['speed'] ) . '" />';
			$html .= '<div class="feedback-list testimonial">' . "\n";
		
			foreach ( $query as $post ) {
				setup_postdata( $post );
				
				$author = '';
				$author_text = '';
				
				// If we need to display either the author, URL or both, get the data.
				if ( $args['display_author'] == true || $args['display_url'] == true ) {
					$meta = get_post_custom( $post->ID );
					
					if ( isset( $meta['feedback_author'] ) && ( $meta['feedback_author'][0] != '' ) && $args['display_author'] == true ) {
						$author .= '<cite class="feedback-author">';
					}
					
					if ( isset( $meta['feedback_url'] ) && ( $meta['feedback_url'][0] != '' ) && $args['display_url'] == true ) {
						$author .= '<a href="' . $meta['feedback_url'][0] . '" title="' . esc_attr( $author_text ) . '" class="feedback-url">' . $meta['feedback_author'][0] . '</a>';
					} else {
						$author .= $meta['feedback_author'][0];
					}
					
					if ( isset( $meta['feedback_author'] ) && ( $meta['feedback_author'][0] != '' ) && $args['display_author'] == true ) {
						$author .= '</cite><!--/.feedback-author-->' . "\n";
					}
				}
				
				$html .= '<div id="quote-' . $post->ID . '" class="quote">' . "\n";
					$html .= '<blockquote class="feedback-text">' . get_the_content() . '</blockquote>' . "\n";
					$html .= $author;
				$html .= '</div>' . "\n";
			}
			
			$html .= '</div><!--/.feedback-list-->' . "\n";
			
			if ( $args['pagination'] == true && count( $query ) > 1 && $args['effect'] != 'none' ) {
			
				$html .= '<ul class="pagination">' . "\n";
				$count = 1;
				foreach ( $query as $post ) {
					$html .= '<li><a href="#0">' . $count . '</a></li>';
					$count++;
				}
		        $html .= '</ul>' . "\n";
		        
			}
			
			$html .= '</div><!--/.feedback-->' . "\n";
			
			$html .= '<div class="more">'."\n";
			
			if ( isset($args['page_url']) && $args['page_url'] > 0 ) { 
				$html .= '<a href="'.get_permalink($args['page_url']).'" title="'.__('View More Testimonials','woothemes').'">'.__('View More Testimonials','woothemes').'</a>'."\n"; 
		   	}
			
			$html .= '</div><!-- /.more -->'."\n";
    	
		}
		
		// Allow child themes/plugins to filter here.
		$html = apply_filters( 'woo_feedback_html', $html, $query );
		
		if ( $args['echo'] != true ) { return $html; }
		
		// Should only run is "echo" is set to true.
		echo $html;
		
		woo_do_atomic( 'woo_feedback_after', $args ); // Only if "echo" is set to true.
		
		wp_reset_query();
		
	} // End woo_display_feedback_entries()
}

/*-----------------------------------------------------------------------------------*/
/* END */
/*-----------------------------------------------------------------------------------*/
?>