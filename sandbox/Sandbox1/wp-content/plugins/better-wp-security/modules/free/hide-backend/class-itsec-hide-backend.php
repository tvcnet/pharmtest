<?php

class ITSEC_Hide_Backend {

	private
		$settings;

	function __construct() {

		$this->settings = get_site_option( 'itsec_hide_backend' );

		//Execute module functions on frontend init
		if ( $this->settings['enabled'] === true ) {

			add_action( 'init', array( $this, 'execute_hide_backend' ) );
			add_action( 'login_init', array( $this, 'execute_hide_backend_login' ) );

			add_filter( 'body_class', array( $this, 'remove_admin_bar' ) );
			add_filter( 'wp_redirect', array( $this, 'filter_login_url' ), 10, 2 );
			add_filter( 'site_url', array( $this, 'filter_login_url' ), 10, 2 );

			remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );

		}

	}

	/**
	 * Execute hide backend functionality
	 *
	 * @return void
	 */
	public function execute_hide_backend() {

		//redirect wp-admin and wp-register.php to 404 when not logged in
		if (
			(
				get_site_option( 'users_can_register' ) == false &&
				(
					isset( $_SERVER['REQUEST_URI'] ) && $_SERVER['REQUEST_URI'] == '/wp-register.php' ||
					isset( $_SERVER['REQUEST_URI'] ) && $_SERVER['REQUEST_URI'] == '/wp-signup.php'
				)
			) ||
			(
				isset( $_SERVER['REQUEST_URI'] ) && $_SERVER['REQUEST_URI'] == '/wp-login.php' && is_user_logged_in() !== true
			) ||
			( is_admin() && is_user_logged_in() !== true ) ||
			(
				$this->settings['register'] != 'wp-register.php' &&
				strpos( $_SERVER['REQUEST_URI'], 'wp-register.php' ) !== false ||
				strpos( $_SERVER['REQUEST_URI'], 'wp-signup.php' ) !== false
			)
		) {

			ITSEC_Lib::set_404();
		}

		$url_info   = parse_url( $_SERVER['REQUEST_URI'] );
		$login_path = site_url( $this->settings['slug'], 'relative' );

		if ( $url_info['path'] === $login_path ) {

			if ( ! is_user_logged_in() ) {

				status_header( 200 );

				require_once( ABSPATH . 'wp-login.php' );

				if ( ITSEC_Lib::get_server() == 'nginx' ) {
					die();
				}

			} elseif ( ! isset( $_GET['action'] ) || sanitize_text_field( $_GET['action'] ) != 'logout' ) {
				wp_redirect( get_admin_url() );
				exit();
			}

		}

	}

	/**
	 * Filter the old login page out
	 *
	 * @return void
	 */
	public function execute_hide_backend_login() {

		if ( strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) ) { //are we on the login page

			ITSEC_Lib::set_404();

		}

	}

	/**
	 * Filters redirects for currect login URL
	 *
	 * @param  string $url  URL redirecting to
	 * @param  string $path Path or status code (depending on which call used)
	 *
	 * @return string       Correct redirect URL
	 */
	public function filter_login_url( $url, $path ) {

		if ( strpos( $url, 'wp-login.php' ) !== false ) { //only run on wp-login.php

			$pos = strpos( $path, '?' );
			$loc = $path;

			if ( $pos === false ) {
				$pos = strpos( $url, '?' );
				$loc = $url;
			}

			if ( $pos === false ) {
				$query = '';
			} else {
				$query = substr( $loc, $pos );
			}

			$login_url = site_url( $this->settings['slug'] ) . $query;

		} else { //not wp-login.php

			$login_url = $url;

		}

		return $login_url;

	}

	/**
	 * Removes the admin bar class from the body tag
	 *
	 * @param  array $classes body tag classes
	 *
	 * @return array          body tag classes
	 */
	function remove_admin_bar( $classes ) {

		if ( is_admin() && is_user_logged_in() !== true ) {

			foreach ( $classes as $key => $value ) {

				if ( $value == 'admin-bar' ) {
					unset( $classes[$key] );
				}

			}

		}

		return $classes;

	}

}