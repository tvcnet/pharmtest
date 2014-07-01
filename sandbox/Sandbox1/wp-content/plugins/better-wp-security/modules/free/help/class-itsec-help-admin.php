<?php

class ITSEC_Help_Admin {

	function __construct() {

		if ( is_admin() ) {

			$this->initialize();

		}

	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 */
	public function add_admin_meta_boxes() {

		add_meta_box(
			'itsec_help_info',
			__( 'Help', 'ithemes-security' ),
			array( $this, 'add_help_intro' ),
			'security_page_toplevel_page_itsec_help',
			'normal',
			'core'
		);

	}

	/**
	 * Initializes all admin functionality.
	 *
	 * @since 4.0
	 *
	 * @param ITSEC_Core $core The $itsec_core instance
	 *
	 * @return void
	 */
	private function initialize() {

		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) ); //add meta boxes to admin page

	}

	/**
	 * Build and echo the away mode description
	 *
	 * @return void
	 */
	public function add_help_intro() {

		$content = 'Support Information';

		echo $content;

	}

}