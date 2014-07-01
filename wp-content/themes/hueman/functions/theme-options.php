<?php

/*  Initialize the options before anything else. 
/* ------------------------------------ */
add_action( 'admin_init', 'custom_theme_options', 1 );


/*  Build the custom settings & update OptionTree.
/* ------------------------------------ */
function custom_theme_options() {
	
	// Get a copy of the saved settings array.
	$saved_settings = get_option( 'option_tree_settings', array() );

	// Custom settings array that will eventually be passed to the OptionTree Settings API Class.
	$custom_settings = array(

/*  Help pages
/* ------------------------------------ */	
	'contextual_help' => array(
      'content'       => array( 
        array(
          'id'        => 'general_help',
          'title'     => 'Documentation',
          'content'   => '
			<p>First, a friendly warning: Please remember that the "Reset Options" button resets <strong>ALL</strong> options. That means, if you reset your styling options, all your custom sidebars and other settings will be reset as well.</p>
			<p><i>Frequently Asked Questions:</i></p>
			<p><strong>Q: Styling options do not work, why?</strong> &mdash; A: Make sure that the dynamic.css file has server permissions set to chmod 0777, so that it is writable. You may also need to empty cache.</p>
		'
        )
      )
    ),
	
/*  Admin panel sections
/* ------------------------------------ */	
	'sections'        => array(
		array(
			'id'		=> 'general',
			'title'		=> 'General'
		),
		array(
			'id'		=> 'blog',
			'title'		=> 'Blog'
		),
		array(
			'id'		=> 'header',
			'title'		=> 'Header'
		),
		array(
			'id'		=> 'footer',
			'title'		=> 'Footer'
		),
		array(
			'id'		=> 'layout',
			'title'		=> 'Layout'
		),
		array(
			'id'		=> 'sidebars',
			'title'		=> 'Sidebars'
		),
		array(
			'id'		=> 'social-links',
			'title'		=> 'Social Links'
		),
		array(
			'id'		=> 'styling',
			'title'		=> 'Styling'
		),
	),
	
/*  Theme options
/* ------------------------------------ */
	'settings'        => array(
		
		// General: Responsive Layout
		array(
			'id'		=> 'responsive',
			'label'		=> 'Responsive Layout',
			'desc'		=> 'Disable mobile and tablet optimizations [ <strong>responsive.css</strong> ]',
			'type'		=> 'checkbox',
			'section'	=> 'general',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Disable'
				)
			)
		),
		// General: Mobile Sidebar
		array(
			'id'		=> 'mobile-sidebar-hide',
			'label'		=> 'Mobile Sidebar Content',
			'desc'		=> 'Hide sidebar content on low-resolution mobile devices (320px)',
			'type'		=> 'radio',
			'std'		=> '1',
			'section'	=> 'general',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Show sidebars'
				),
				array( 
					'value' => 's1',
					'label' => 'Hide primary sidebar'
				),
				array( 
					'value' => 's2',
					'label' => 'Hide secondary sidebar'
				),
				array( 
					'value' => 's1-s2',
					'label' => 'Hide both sidebars'
				)
			)
		),
		// General: Favicon
		array(
			'id'		=> 'favicon',
			'label'		=> 'Favicon',
			'desc'		=> 'Upload a 16x16px Png/Gif image that will be your favicon',
			'type'		=> 'upload',
			'section'	=> 'general'
		),
		// General: RSS Feed
		array(
			'id'		=> 'rss-feed',
			'label'		=> 'FeedBurner URL',
			'desc'		=> 'Enter your full FeedBurner URL (or any other preferred feed URL) if you wish to use FeedBurner over the standard WordPress feed e.g. http://feeds.feedburner.com/yoururlhere ',
			'type'		=> 'text',
			'section'	=> 'general'
		),
		// General: Comments
		array(
			'id'		=> 'page-comments',
			'label'		=> 'Comments',
			'desc'		=> '',
			'type'		=> 'checkbox',
			'section'	=> 'general',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Enable comments on pages',
					'std'	=> '1'
				)
			)
		),
		// Blog: Heading
		array(
			'id'		=> 'blog-heading',
			'label'		=> 'Heading',
			'desc'		=> 'Your blog heading',
			'type'		=> 'text',
			'section'	=> 'blog'
		),
		// Blog: Subheading
		array(
			'id'		=> 'blog-subheading',
			'label'		=> 'Subheading',
			'desc'		=> 'Your blog subheading',
			'type'		=> 'text',
			'section'	=> 'blog'
		),
		// Blog: Excerpt Length
		array(
			'id'			=> 'excerpt-length',
			'label'			=> 'Excerpt Length',
			'desc'			=> 'Max number of words',
			'std'			=> '34',
			'type'			=> 'numeric-slider',
			'section'		=> 'blog',
			'min_max_step'	=> '0,100,1'
		),
		// Blog: Featured Posts
		array(
			'id'		=> 'featured-posts-include',
			'label'		=> 'Featured Posts',
			'desc'		=> 'To show featured posts in the slider AND the content below<br /><i>Usually not recommended</i>',
			'type'		=> 'checkbox',
			'section'	=> 'blog',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Include featured posts in content area'
				)
			)
		),
		// Blog: Featured Category
		array(
			'id'		=> 'featured-category',
			'label'		=> 'Featured Category',
			'desc'		=> 'By not selecting a category, it will show your latest post(s) from all categories',
			'type'		=> 'category-select',
			'section'	=> 'blog'
		),
		// Blog: Featured Category Count
		array(
			'id'			=> 'featured-posts-count',
			'label'			=> 'Featured Post Count',
			'desc'			=> 'Max number of featured posts to display. <br /><i>Set to 1 and it will show it without any slider script</i><br /><i>Set it to 0 to disable</i>',
			'std'			=> '1',
			'type'			=> 'numeric-slider',
			'section'		=> 'blog',
			'min_max_step'	=> '0,10,1'
		),
		// Blog: Comment Count
		array(
			'id'		=> 'comment-count',
			'label'		=> 'Thumbnail Comment Count',
			'desc'		=> 'Comment count on thumbnails',
			'type'		=> 'checkbox',
			'section'	=> 'blog',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Disable'
				)
			)
		),
		// Blog: Single - Authorbox
		array(
			'id'		=> 'author-bio',
			'label'		=> 'Single &mdash; Author Bio',
			'desc'		=> 'Shows post author description, if it exists',
			'type'		=> 'checkbox',
			'section'	=> 'blog',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Disable'
				)
			)
		),
		// Blog: Single - Related Posts
		array(
			'id'		=> 'related-posts',
			'label'		=> 'Single &mdash; Related Posts',
			'desc'		=> 'Shows randomized related articles below the post',
			'std'		=> 'categories',
			'type'		=> 'radio',
			'section'	=> 'blog',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Disable'
				),
				array( 
					'value' => 'categories',
					'label' => 'Related by categories'
				),
				array( 
					'value' => 'tags',
					'label' => 'Related by tags'
				)
			)
		),
		// Blog: Single - Post Navigation Location
		array(
			'id'		=> 'post-nav',
			'label'		=> 'Single &mdash; Post Navigation',
			'desc'		=> 'Shows links to the next and previous article',
			'std'		=> 's1',
			'type'		=> 'radio',
			'section'	=> 'blog',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Disable'
				),
				array( 
					'value' => 's1',
					'label' => 'Sidebar Primary'
				),
				array( 
					'value' => 's2',
					'label' => 'Sidebar Secondary'
				),
				array( 
					'value' => 'content',
					'label' => 'Below content'
				)
			)
		),
		// Header: Custom Logo
		array(
			'id'		=> 'custom-logo',
			'label'		=> 'Custom Logo',
			'desc'		=> 'Upload your custom logo image. Set logo max-height in styling options.',
			'type'		=> 'upload',
			'section'	=> 'header'
		),
		// Header: Site Description
		array(
			'id'		=> 'site-description',
			'label'		=> 'Site Description',
			'desc'		=> 'The description that appears next to your logo',
			'type'		=> 'checkbox',
			'section'	=> 'header',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Disable'
				)
			)
		),
		// Footer: Widget Columns
		array(
			'id'		=> 'footer-widgets',
			'label'		=> 'Footer Widget Columns',
			'desc'		=> 'Select columns to enable footer widgets<br /><i>Recommended number: 3</i>',
			'std'		=> '0',
			'type'		=> 'radio-image',
			'section'	=> 'footer',
			'class'		=> '',
			'choices'	=> array(
				array(
					'value'		=> '0',
					'label'		=> 'Disable',
					'src'		=> get_template_directory_uri() . '/functions/images/layout-off.png'
				),
				array(
					'value'		=> '1',
					'label'		=> '1 Column',
					'src'		=> get_template_directory_uri() . '/functions/images/footer-widgets-1.png'
				),
				array(
					'value'		=> '2',
					'label'		=> '2 Columns',
					'src'		=> get_template_directory_uri() . '/functions/images/footer-widgets-2.png'
				),
				array(
					'value'		=> '3',
					'label'		=> '3 Columns',
					'src'		=> get_template_directory_uri() . '/functions/images/footer-widgets-3.png'
				),
				array(
					'value'		=> '4',
					'label'		=> '4 Columns',
					'src'		=> get_template_directory_uri() . '/functions/images/footer-widgets-4.png'
				)
			)
		),
		// Footer: Custom Logo
		array(
			'id'		=> 'footer-logo',
			'label'		=> 'Footer Logo',
			'desc'		=> 'Upload your custom logo image',
			'type'		=> 'upload',
			'section'	=> 'footer'
		),
		// Footer: Copyright
		array(
			'id'		=> 'copyright',
			'label'		=> 'Footer Copyright',
			'desc'		=> 'Replace the footer copyright text',
			'type'		=> 'text',
			'section'	=> 'footer'
		),
		// Footer: Credit
		array(
			'id'		=> 'credit',
			'label'		=> 'Footer Credit',
			'desc'		=> 'Disable footer credit text',
			'std'		=> '',
			'type'		=> 'checkbox',
			'section'	=> 'footer',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Disable'
				)
			)
		),
		// Layout : Global
		array(
			'id'		=> 'layout-global',
			'label'		=> 'Global Layout',
			'desc'		=> 'Other layouts will override this option if they are set',
			'std'		=> 'col-3cm',
			'type'		=> 'radio-image',
			'section'	=> 'layout',
			'choices'	=> array(
				array(
					'value'		=> 'col-1c',
					'label'		=> '1 Column',
					'src'		=> get_template_directory_uri() . '/functions/images/col-1c.png'
				),
				array(
					'value'		=> 'col-2cl',
					'label'		=> '2 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cl.png'
				),
				array(
					'value'		=> 'col-2cr',
					'label'		=> '2 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cr.png'
				),
				array(
					'value'		=> 'col-3cm',
					'label'		=> '3 Column Middle',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cm.png'
				),
				array(
					'value'		=> 'col-3cl',
					'label'		=> '3 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cl.png'
				),
				array(
					'value'		=> 'col-3cr',
					'label'		=> '3 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cr.png'
				)
			)
		),
		// Layout : Home
		array(
			'id'		=> 'layout-home',
			'label'		=> 'Posts Home',
			'desc'		=> '[ <strong>is_home</strong> ] Posts homepage layout',
			'std'		=> 'inherit',
			'type'		=> 'radio-image',
			'section'	=> 'layout',
			'choices'	=> array(
				array(
					'value'		=> 'inherit',
					'label'		=> 'Inherit Global Layout',
					'src'		=> get_template_directory_uri() . '/functions/images/layout-off.png'
				),
				array(
					'value'		=> 'col-1c',
					'label'		=> '1 Column',
					'src'		=> get_template_directory_uri() . '/functions/images/col-1c.png'
				),
				array(
					'value'		=> 'col-2cl',
					'label'		=> '2 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cl.png'
				),
				array(
					'value'		=> 'col-2cr',
					'label'		=> '2 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cr.png'
				),
				array(
					'value'		=> 'col-3cm',
					'label'		=> '3 Column Middle',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cm.png'
				),
				array(
					'value'		=> 'col-3cl',
					'label'		=> '3 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cl.png'
				),
				array(
					'value'		=> 'col-3cr',
					'label'		=> '3 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cr.png'
				)
			)
		),
		// Layout : Single
		array(
			'id'		=> 'layout-single',
			'label'		=> 'Single',
			'desc'		=> '[ <strong>is_single</strong> ] Single post layout - If a post has a set layout, it will override this.',
			'std'		=> 'inherit',
			'type'		=> 'radio-image',
			'section'	=> 'layout',
			'choices'	=> array(
				array(
					'value'		=> 'inherit',
					'label'		=> 'Inherit Global Layout',
					'src'		=> get_template_directory_uri() . '/functions/images/layout-off.png'
				),
				array(
					'value'		=> 'col-1c',
					'label'		=> '1 Column',
					'src'		=> get_template_directory_uri() . '/functions/images/col-1c.png'
				),
				array(
					'value'		=> 'col-2cl',
					'label'		=> '2 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cl.png'
				),
				array(
					'value'		=> 'col-2cr',
					'label'		=> '2 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cr.png'
				),
				array(
					'value'		=> 'col-3cm',
					'label'		=> '3 Column Middle',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cm.png'
				),
				array(
					'value'		=> 'col-3cl',
					'label'		=> '3 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cl.png'
				),
				array(
					'value'		=> 'col-3cr',
					'label'		=> '3 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cr.png'
				)
			)
		),
		// Layout : Archive
		array(
			'id'		=> 'layout-archive',
			'label'		=> 'Archive',
			'desc'		=> '[ <strong>is_archive</strong> ] Category, date, tag and author archive layout',
			'std'		=> 'inherit',
			'type'		=> 'radio-image',
			'section'	=> 'layout',
			'choices'	=> array(
				array(
					'value'		=> 'inherit',
					'label'		=> 'Inherit Global Layout',
					'src'		=> get_template_directory_uri() . '/functions/images/layout-off.png'
				),
				array(
					'value'		=> 'col-1c',
					'label'		=> '1 Column',
					'src'		=> get_template_directory_uri() . '/functions/images/col-1c.png'
				),
				array(
					'value'		=> 'col-2cl',
					'label'		=> '2 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cl.png'
				),
				array(
					'value'		=> 'col-2cr',
					'label'		=> '2 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cr.png'
				),
				array(
					'value'		=> 'col-3cm',
					'label'		=> '3 Column Middle',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cm.png'
				),
				array(
					'value'		=> 'col-3cl',
					'label'		=> '3 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cl.png'
				),
				array(
					'value'		=> 'col-3cr',
					'label'		=> '3 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cr.png'
				)
			)
		),
		// Layout : Archive - Category
		array(
			'id'		=> 'layout-archive-category',
			'label'		=> 'Archive &mdash; Category',
			'desc'		=> '[ <strong>is_category</strong> ] Category archive layout',
			'std'		=> 'inherit',
			'type'		=> 'radio-image',
			'section'	=> 'layout',
			'choices'	=> array(
				array(
					'value'		=> 'inherit',
					'label'		=> 'Inherit Global Layout',
					'src'		=> get_template_directory_uri() . '/functions/images/layout-off.png'
				),
				array(
					'value'		=> 'col-1c',
					'label'		=> '1 Column',
					'src'		=> get_template_directory_uri() . '/functions/images/col-1c.png'
				),
				array(
					'value'		=> 'col-2cl',
					'label'		=> '2 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cl.png'
				),
				array(
					'value'		=> 'col-2cr',
					'label'		=> '2 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cr.png'
				),
				array(
					'value'		=> 'col-3cm',
					'label'		=> '3 Column Middle',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cm.png'
				),
				array(
					'value'		=> 'col-3cl',
					'label'		=> '3 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cl.png'
				),
				array(
					'value'		=> 'col-3cr',
					'label'		=> '3 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cr.png'
				)
			)
		),
		// Layout : Search
		array(
			'id'		=> 'layout-search',
			'label'		=> 'Search',
			'desc'		=> '[ <strong>is_search</strong> ] Search page layout',
			'std'		=> 'inherit',
			'type'		=> 'radio-image',
			'section'	=> 'layout',
			'choices'	=> array(
				array(
					'value'		=> 'inherit',
					'label'		=> 'Inherit Global Layout',
					'src'		=> get_template_directory_uri() . '/functions/images/layout-off.png'
				),
				array(
					'value'		=> 'col-1c',
					'label'		=> '1 Column',
					'src'		=> get_template_directory_uri() . '/functions/images/col-1c.png'
				),
				array(
					'value'		=> 'col-2cl',
					'label'		=> '2 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cl.png'
				),
				array(
					'value'		=> 'col-2cr',
					'label'		=> '2 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cr.png'
				),
				array(
					'value'		=> 'col-3cm',
					'label'		=> '3 Column Middle',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cm.png'
				),
				array(
					'value'		=> 'col-3cl',
					'label'		=> '3 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cl.png'
				),
				array(
					'value'		=> 'col-3cr',
					'label'		=> '3 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cr.png'
				)
			)
		),
		// Layout : Error 404
		array(
			'id'		=> 'layout-404',
			'label'		=> 'Error 404',
			'desc'		=> '[ <strong>is_404</strong> ] Error 404 page layout',
			'std'		=> 'inherit',
			'type'		=> 'radio-image',
			'section'	=> 'layout',
			'choices'	=> array(
				array(
					'value'		=> 'inherit',
					'label'		=> 'Inherit Global Layout',
					'src'		=> get_template_directory_uri() . '/functions/images/layout-off.png'
				),
				array(
					'value'		=> 'col-1c',
					'label'		=> '1 Column',
					'src'		=> get_template_directory_uri() . '/functions/images/col-1c.png'
				),
				array(
					'value'		=> 'col-2cl',
					'label'		=> '2 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cl.png'
				),
				array(
					'value'		=> 'col-2cr',
					'label'		=> '2 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cr.png'
				),
				array(
					'value'		=> 'col-3cm',
					'label'		=> '3 Column Middle',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cm.png'
				),
				array(
					'value'		=> 'col-3cl',
					'label'		=> '3 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cl.png'
				),
				array(
					'value'		=> 'col-3cr',
					'label'		=> '3 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cr.png'
				)
			)
		),
		// Layout : Default Page
		array(
			'id'		=> 'layout-page',
			'label'		=> 'Default Page',
			'desc'		=> '[ <strong>is_page</strong> ] Default page layout - If a page has a set layout, it will override this.',
			'std'		=> 'inherit',
			'type'		=> 'radio-image',
			'section'	=> 'layout',
			'choices'	=> array(
				array(
					'value'		=> 'inherit',
					'label'		=> 'Inherit Global Layout',
					'src'		=> get_template_directory_uri() . '/functions/images/layout-off.png'
				),
				array(
					'value'		=> 'col-1c',
					'label'		=> '1 Column',
					'src'		=> get_template_directory_uri() . '/functions/images/col-1c.png'
				),
				array(
					'value'		=> 'col-2cl',
					'label'		=> '2 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cl.png'
				),
				array(
					'value'		=> 'col-2cr',
					'label'		=> '2 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-2cr.png'
				),
				array(
					'value'		=> 'col-3cm',
					'label'		=> '3 Column Middle',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cm.png'
				),
				array(
					'value'		=> 'col-3cl',
					'label'		=> '3 Column Left',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cl.png'
				),
				array(
					'value'		=> 'col-3cr',
					'label'		=> '3 Column Right',
					'src'		=> get_template_directory_uri() . '/functions/images/col-3cr.png'
				)
			)
		),
		// Sidebars: Create Areas
		array(
			'id'		=> 'sidebar-areas',
			'label'		=> 'Create Sidebars',
			'desc'		=> 'You must save changes for the new areas to appear below. <br /><i>Warning: Make sure each area has a unique ID.</i>',
			'type'		=> 'list-item',
			'section'	=> 'sidebars',
			'choices'	=> array(),
			'settings'	=> array(
				array(
					'id'		=> 'id',
					'label'		=> 'Sidebar ID',
					'desc'		=> 'This ID must be unique, for example "sidebar-about"',
					'std'		=> 'sidebar-',
					'type'		=> 'text',
					'choices'	=> array()
				)
			)
		),
		// Sidebar 1 & 2
		array(
			'id'		=> 's1-home',
			'label'		=> 'Home',
			'desc'		=> '[ <strong>is_home</strong> ] Primary',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's2-home',
			'label'		=> 'Home',
			'desc'		=> '[ <strong>is_home</strong> ] Secondary',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's1-single',
			'label'		=> 'Single',
			'desc'		=> '[ <strong>is_single</strong> ] Primary - If a single post has a unique sidebar, it will override this.',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's2-single',
			'label'		=> 'Single',
			'desc'		=> '[ <strong>is_single</strong> ] Secondary - If a single post has a unique sidebar, it will override this.',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's1-archive',
			'label'		=> 'Archive',
			'desc'		=> '[ <strong>is_archive</strong> ] Primary',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's2-archive',
			'label'		=> 'Archive',
			'desc'		=> '[ <strong>is_archive</strong> ] Secondary',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's1-archive-category',
			'label'		=> 'Archive &mdash; Category',
			'desc'		=> '[ <strong>is_category</strong> ] Primary',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's2-archive-category',
			'label'		=> 'Archive &mdash; Category',
			'desc'		=> '[ <strong>is_category</strong> ] Secondary',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's1-search',
			'label'		=> 'Search',
			'desc'		=> '[ <strong>is_search</strong> ] Primary',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's2-search',
			'label'		=> 'Search',
			'desc'		=> '[ <strong>is_search</strong> ] Secondary',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's1-404',
			'label'		=> 'Error 404',
			'desc'		=> '[ <strong>is_404</strong> ] Primary',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's2-404',
			'label'		=> 'Error 404',
			'desc'		=> '[ <strong>is_404</strong> ] Secondary',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's1-page',
			'label'		=> 'Default Page',
			'desc'		=> '[ <strong>is_page</strong> ] Primary - If a page has a unique sidebar, it will override this.',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		array(
			'id'		=> 's2-page',
			'label'		=> 'Default Page',
			'desc'		=> '[ <strong>is_page</strong> ] Secondary - If a page has a unique sidebar, it will override this.',
			'type'		=> 'sidebar-select',
			'section'	=> 'sidebars'
		),
		// Social Links : List
		array(
			'id'		=> 'social-links',
			'label'		=> 'Social Links',
			'desc'		=> 'Create and organize your social links',
			'type'		=> 'list-item',
			'section'	=> 'social-links',
			'choices'	=> array(),
			'settings'	=> array(
				array(
					'id'		=> 'social-icon',
					'label'		=> 'Icon Name',
					'desc'		=> 'Font Awesome icon names [<a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank"><strong>View all</strong>]</a>  ',
					'std'		=> 'fa-',
					'type'		=> 'text',
					'choices'	=> array()
				),
				array(
					'id'		=> 'social-link',
					'label'		=> 'Link',
					'desc'		=> 'Enter the full url for your icon button',
					'std'		=> 'http://',
					'type'		=> 'text',
					'choices'	=> array()
				),
				array(
					'id'		=> 'social-color',
					'label'		=> 'Icon Color',
					'desc'		=> 'Set a unique color for your icon (optional)',
					'std'		=> '',
					'type'		=> 'colorpicker',
					'section'	=> 'styling'
				),
				array(
					'id'		=> 'social-target',
					'label'		=> 'Link Options',
					'desc'		=> '',
					'std'		=> '',
					'type'		=> 'checkbox',
					'choices'	=> array(
						array( 
							'value' => '_blank',
							'label' => 'Open in new window'
						)
					)
				)
			)
		),
		// Styling: Container Width
		array(
			'id'			=> 'container-width',
			'label'			=> 'Website Max-width',
			'desc'			=> 'Max-width of the container. If you use 2 sidebars, your container should be at least 1200px.<br /><i>Note: For 720px content (default) use <strong>1380px</strong> for 2 sidebars and <strong>1120px</strong> for 1 sidebar. If you use a combination of both, try something inbetween.</i>',
			'std'			=> '1380',
			'type'			=> 'numeric-slider',
			'section'		=> 'styling',
			'min_max_step'	=> '1024,1600,1'
		),
		// Styling: Sidebar Padding
		array(
			'id'		=> 'sidebar-padding',
			'label'		=> 'Sidebar Width',
			'desc'		=> 'Change sidebar content padding and width.',
			'type'		=> 'radio',
			'std'		=> '30',
			'section'	=> 'styling',
			'choices'	=> array(
				array( 
					'value' => '30',
					'label' => '280px primary, 200px secondary (30px padding)'
				),
				array( 
					'value' => '20',
					'label' => '300px primary, 220px secondary (20px padding)'
				)
			)
		),
		// Styling: Primary Color
		array(
			'id'		=> 'color-1',
			'label'		=> 'Primary Color',
			'desc'		=> 'Set the primary accent color of your theme<br /><i>Default: #3b8dbd</i>',
			'std'		=> '#3b8dbd',
			'type'		=> 'colorpicker',
			'section'	=> 'styling',
			'class'		=> ''
		),
		// Styling: Secondary Color
		array(
			'id'		=> 'color-2',
			'label'		=> 'Secondary Color',
			'desc'		=> 'Set the secondary accent color of your theme<br /><i>Default: #82b965</i>',
			'std'		=> '#82b965',
			'type'		=> 'colorpicker',
			'section'	=> 'styling',
			'class'		=> ''
		),
		// Styling: Topbar Color
		array(
			'id'		=> 'color-topbar',
			'label'		=> 'Topbar Color',
			'desc'		=> 'Topbar menu and search<br /><i>Default: #26272b</i>',
			'std'		=> '#26272b',
			'type'		=> 'colorpicker',
			'section'	=> 'styling',
			'class'		=> ''
		),
		// Styling: Header Color
		array(
			'id'		=> 'color-header',
			'label'		=> 'Header Color',
			'desc'		=> 'Header background<br /><i>Default: #33363b</i>',
			'std'		=> '#33363b',
			'type'		=> 'colorpicker',
			'section'	=> 'styling',
			'class'		=> ''
		),
		// Styling: Footer Color
		array(
			'id'		=> 'color-footer',
			'label'		=> 'Footer Color',
			'desc'		=> 'Footer background<br /><i>Default: #33363b</i>',
			'std'		=> '#33363b',
			'type'		=> 'colorpicker',
			'section'	=> 'styling',
			'class'		=> ''
		),
		// Styling: Header Logo Max-height
		array(
			'id'			=> 'logo-max-height',
			'label'			=> 'Header Logo Image Max-height',
			'desc'			=> 'When you upload your logo image it should have the double height of this to be high resolution.',
			'std'			=> '60',
			'type'			=> 'numeric-slider',
			'section'		=> 'styling',
			'min_max_step'	=> '40,120,1'
		),
		// Styling: Image Border Radius
		array(
			'id'			=> 'image-border-radius',
			'label'			=> 'Image Border Radius',
			'desc'			=> 'Give your thumbnails and layout images pretty rounded corners',
			'std'			=> '0',
			'type'			=> 'numeric-slider',
			'section'		=> 'styling',
			'min_max_step'	=> '0,15,1'
		),
		// Styling: Page Background
		array(
			'id'		=> 'background-page',
			'label'		=> 'Page Background',
			'desc'		=> 'Set background color and/or upload your own background image',
			'type'		=> 'background',
			'section'	=> 'styling'
		),
		// Styling: CSS Output
		array(
			'id'		=> 'dynamic-output',
			'label'		=> 'Dynamic CSS',
			'desc'		=> 'This auto-generated CSS code is added to the [ <strong>dynamic.css</strong> ] file<br /><i>Warning: Do not remove, change or edit this code in any way. Just let it be :-)</i>',
			'type'		=> 'css',
			'section'	=> 'styling',
			'rows'		=> '2',
			'std'		=> '
.container-inner { max-width: {{container-width}}px; }

.sidebar .widget { padding-left: {{sidebar-padding}}px; padding-right: {{sidebar-padding}}px; padding-top: {{sidebar-padding}}px; }

img { -webkit-border-radius: {{image-border-radius}}px; border-radius: {{image-border-radius}}px; }

.site-title a img { max-height: {{logo-max-height}}px; }

#page { {{background-page}} }

::selection { background-color: {{color-1}}; }
::-moz-selection { background-color: {{color-1}}; }

a,
.themeform label .required,
#flexslider-featured .flex-direction-nav .flex-next:hover,
#flexslider-featured .flex-direction-nav .flex-prev:hover,
.post-hover:hover .post-title a,
.post-title a:hover,
.s1 .post-nav li a:hover i,
.content .post-nav li a:hover i,
.post-related a:hover,
.s1 .widget_rss ul li a,
#footer .widget_rss ul li a,
.s1 .widget_calendar a,
#footer .widget_calendar a,
.s1 .alx-tab .tab-item-category a,
.s1 .alx-posts .post-item-category a,
.s1 .alx-tab li:hover .tab-item-title a,
.s1 .alx-tab li:hover .tab-item-comment a,
.s1 .alx-posts li:hover .post-item-title a,
#footer .alx-tab .tab-item-category a,
#footer .alx-posts .post-item-category a,
#footer .alx-tab li:hover .tab-item-title a,
#footer .alx-tab li:hover .tab-item-comment a,
#footer .alx-posts li:hover .post-item-title a,
.comment-tabs li.active a,
.comment-awaiting-moderation,
.child-menu a:hover,
.child-menu .current_page_item > a,
.wp-pagenavi a { color: {{color-1}}; }

.themeform input[type="submit"],
.themeform button[type="submit"],
.s1 .sidebar-top,
.s1 .sidebar-toggle,
#flexslider-featured .flex-control-nav li a.flex-active,
.post-tags a:hover,
.s1 .widget_calendar caption,
#footer .widget_calendar caption,
.author-bio .bio-avatar:after,
.commentlist li.bypostauthor > .comment-body:after { background-color: {{color-1}}; }

.post-format .format-container { border-color: {{color-1}}; }

.s1 .alx-tabs-nav li.active a,
#footer .alx-tabs-nav li.active a,
.comment-tabs li.active a,
.wp-pagenavi a:hover,
.wp-pagenavi a:active,
.wp-pagenavi span.current { border-bottom-color: {{color-1}}!important; }

.s2 .post-nav li a:hover i,
.s2 .widget_rss ul li a,
.s2 .widget_calendar a,
.s2 .alx-tab .tab-item-category a,
.s2 .alx-posts .post-item-category a,
.s2 .alx-tab li:hover .tab-item-title a,
.s2 .alx-tab li:hover .tab-item-comment a,
.s2 .alx-posts li:hover .post-item-title a { color: {{color-2}}; }

.s2 .sidebar-top,
.s2 .sidebar-toggle,
.post-comments,
.jp-play-bar,
.jp-volume-bar-value,
.s2 .widget_calendar caption { background-color: {{color-2}}; }

.s2 .alx-tabs-nav li.active a { border-bottom-color: {{color-2}}; }
.post-comments span:before { border-right-color: {{color-2}}; }

.search-expand,
#nav-topbar.nav-container { background-color: {{color-topbar}}; }
@media only screen and (min-width: 720px) {
	#nav-topbar .nav ul { background-color: {{color-topbar}}; }
}

#header { background-color: {{color-header}}; }
@media only screen and (min-width: 720px) {
	#nav-header .nav ul { background-color: {{color-header}}; }
}

#footer-bottom { background-color: {{color-footer}}; }
'
		),
		// Styling: Custom CSS
		array(
			'id'		=> 'custom-output',
			'label'		=> 'Custom CSS',
			'desc'		=> 'Add your custom CSS styles here. It will be added to the [ <strong>dynamic.css</strong> ] file',
			'type'		=> 'css',
			'section'	=> 'styling',
			'std'		=> '
/* Add your custom CSS below */
'
		)
	)
);

/*  Settings are not the same? Update the DB
/* ------------------------------------ */
	if ( $saved_settings !== $custom_settings ) {
		update_option( 'option_tree_settings', $custom_settings ); 
	} 
}
