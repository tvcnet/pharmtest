<?php

class ITSEC_Ban_Users_Admin {

	private
		$settings,
		$core,
		$module_path;

	function __construct( $core ) {

		if ( is_admin() ) {

			$this->initialize( $core );

		}

	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function add_admin_meta_boxes() {

		$id    = 'ban_users_options';
		$title = __( 'Banned Users', 'ithemes-security' );

		add_meta_box(
			$id,
			$title,
			array( $this, 'metabox_advanced_settings' ),
			'security_page_toplevel_page_itsec_settings',
			'advanced',
			'core'
		);

		$this->core->add_toc_item(
		           array(
			           'id'    => $id,
			           'title' => $title,
		           )
		);

	}

	/**
	 * Add Away mode Javascript
	 *
	 * @return void
	 */
	public function admin_script() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) !== false ) {

			wp_enqueue_script( 'itsec_ban_users_js', $this->module_path . 'js/admin-ban_users.js', 'jquery', $itsec_globals['plugin_build'] );

		}

	}

	/**
	 * echos Banned Agents field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function ban_users_agent_list() {

		$agent_list = '';

		//Convert and show the agent list
		if ( isset( $this->settings['agent_list'] ) && is_array( $this->settings['agent_list'] ) && sizeof( $this->settings['agent_list'] ) >= 1 ) {

			$agent_list = implode( PHP_EOL, $this->settings['agent_list'] );

		} elseif ( isset( $this->settings['agent_list'] ) && ! is_array( $this->settings['agent_list'] ) && strlen( $this->settings['agent_list'] ) > 1 ) {

			$agent_list = $this->settings['agent_list'];

		}

		$content = '<textarea id="itsec_ban_users_agent_list" name="itsec_ban_users[agent_list]" rows="10" cols="50">' . $agent_list . '</textarea>';
		$content .= '<p>' . __( 'Use the guidelines below to enter user agents that will not be allowed access to your site.', 'ithemes-security' ) . '</p>';
		$content .= '<ul>';
		$content .= '<li>' . __( 'Enter only 1 user agent per line.', 'ithemes-security' ) . '</li>';
		$content .= '</ul>';

		echo $content;

	}

	/**
	 * echos hackrepair list Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function ban_users_default() {

		if ( isset( $this->settings['default'] ) && $this->settings['default'] === true ) {
			$default = 1;
		} else {
			$default = 0;
		}

		$content = '<input type="checkbox" id="itsec_ban_users_default" name="itsec_ban_users[default]" value="1" ' . checked( 1, $default, false ) . '/>';
		$content .= '<label for="itsec_ban_users_default"> ' . __( 'Enable HackRepair.com\'s blacklist feature', 'ithemes-security' ) . '</label>';
		$content .= '<p class="description">' . __( 'As a getting-started point you can include the excellent blacklist developed by Jim Walker of <a href="http://hackrepair.com/blog/how-to-block-bots-from-seeing-your-website-bad-bots-and-drive-by-hacks-explained" target="_blank">HackRepair.com</a>.', 'ithemes-security' ) . '</p>';

		echo $content;

	}

	/**
	 * echos Enabled Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function ban_users_enabled() {

		if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {
			$enabled = 1;
		} else {
			$enabled = 0;
		}

		$content = '<input type="checkbox" id="itsec_ban_users_enabled" name="itsec_ban_users[enabled]" value="1" ' . checked( 1, $enabled, false ) . '/>';
		$content .= '<label for="itsec_ban_users_enabled"> ' . __( 'Enable ban users', 'ithemes-security' ) . '</label>';

		echo $content;

	}

	/**
	 * echos Banned Hosts field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function ban_users_host_list() {

		$host_list = '';

		//Convert and show the host list
		if ( isset( $this->settings['host_list'] ) && is_array( $this->settings['host_list'] ) && sizeof( $this->settings['host_list'] ) >= 1 ) {

			$host_list = implode( PHP_EOL, $this->settings['host_list'] );

		} elseif ( isset( $this->settings['host_list'] ) && ! is_array( $this->settings['host_list'] ) && strlen( $this->settings['host_list'] ) > 1 ) {

			$host_list = $this->settings['host_list'];

		}

		$content = '<textarea id="itsec_ban_users_host_list" name="itsec_ban_users[host_list]" rows="10" cols="50">' . $host_list . '</textarea>';
		$content .= '<p>' . __( 'Use the guidelines below to enter hosts that will not be allowed access to your site. Note you cannot ban yourself.', 'ithemes-security' ) . '</p>';
		$content .= '<ul>';
		$content .= '<li>' . __( 'You may ban users by individual IP address or IP address range.', 'ithemes-security' ) . '</li>';
		$content .= '<li>' . __( 'Individual IP addesses must be in IPV4 standard format (i.e. ###.###.###.### or ###.###.###.###/##). Wildcards (*) or a netmask is allowed to specify a range of ip addresses.', 'ithemes-security' ) . '</li>';
		$content .= '<li>' . __( 'If using a wildcard (*) you must start with the right-most number in the ip field. For example ###.###.###.* and ###.###.*.* are permitted but ###.###.*.### is not.', 'ithemes-security' ) . '</li>';
		$content .= '<li><a href="http://ip-lookup.net/domain-lookup.php" target="_blank">' . __( 'Lookup IP Address.', 'ithemes-security' ) . '</a></li>';
		$content .= '<li>' . __( 'Enter only 1 IP address or 1 IP address range per line.', 'ithemes-security' ) . '</li>';
		$content .= '</ul>';

		echo $content;

	}

	/**
	 * echos Banned white list field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function ban_users_white_list() {

		$white_list = '';

		//Convert and show the agent list
		if ( isset( $this->settings['white_list'] ) && is_array( $this->settings['white_list'] ) && sizeof( $this->settings['white_list'] ) >= 1 ) {

			$white_list = implode( PHP_EOL, $this->settings['white_list'] );

		} elseif ( isset( $this->settings['white_list'] ) && ! is_array( $this->settings['white_list'] ) && strlen( $this->settings['white_list'] ) > 1 ) {

			$white_list = $this->settings['white_list'];

		}

		$content = '<textarea id="itsec_ban_users_white_list" name="itsec_ban_users[white_list]" rows="10" cols="50">' . $white_list . '</textarea>';
		$content .= '<p>' . __( 'Use the guidelines below to enter hosts that will not be banned from your site. This will keep you from locking yourself out of any features if you should trigger a lockout. Please note this does not override away mode.', 'ithemes-security' ) . '</p>';
		$content .= '<ul>';
		$content .= '<li>' . __( 'You may white list users by individual IP address or IP address range.', 'ithemes-security' ) . '</li>';
		$content .= '<li>' . __( 'Individual IP addesses must be in IPV4 standard format (i.e. ###.###.###.### or ###.###.###.###/##). Wildcards (*) or a netmask is allowed to specify a range of ip addresses.', 'ithemes-security' ) . '</li>';
		$content .= '<li>' . __( 'If using a wildcard (*) you must start with the right-most number in the ip field. For example ###.###.###.* and ###.###.*.* are permitted but ###.###.*.### is not.', 'ithemes-security' ) . '</li>';
		$content .= '<li><a href="http://ip-lookup.net/domain-lookup.php" target="_blank">' . __( 'Lookup IP Address.', 'ithemes-security' ) . '</a></li>';
		$content .= '<li>' . __( 'Enter only 1 IP address or 1 IP address range per line.', 'ithemes-security' ) . '</li>';
		$content .= '</ul>';

		echo $content;

	}

	/**
	 * Build the rewrite rules and sends them to the file writer
	 *
	 * @param array   $rules_  array array of rules to modify
	 * @param array   $input   array of options, ips, etc
	 * @param boolean $current whether the current IP can be included in the ban list
	 *
	 * @return array array of rules to send to file writer
	 */
	public static function build_rewrite_rules( $rules_array, $input = null, $current = false ) {

		//setup data structures to write. These are simply lists of all IPs and hosts as well as options to check
		if ( $input === null ) { //blocking ip on the fly

			$input = get_site_option( 'itsec_ban_users' );

		}

		$default        = $input['default'];
		$enabled        = $input['enabled'];
		$raw_host_list  = $input['host_list'];
		$raw_agent_list = $input['agent_list'];
		$raw_white_list = $input['white_list'];

		$server_type = ITSEC_Lib::get_server(); //Get the server type to build the right rules

		//initialize lists so we can check later if we've used them
		$host_list    = '';
		$agent_list   = '';
		$default_list = '';

		//load the default blacklist if needed
		if ( $default === true && $server_type === 'nginx' ) {
			$default_list = file_get_contents( plugin_dir_path( __FILE__ ) . 'lists/hackrepair-nginx.inc' );
		} elseif ( $default === true ) {
			$default_list = file_get_contents( plugin_dir_path( __FILE__ ) . 'lists/hackrepair-apache.inc' );
		}

		//Only process other lists if the feature has been enabled
		if ( $enabled === true ) {

			//process hosts list
			if ( is_array( $raw_host_list ) ) {

				foreach ( $raw_host_list as $host ) {

					$converted_host = ITSEC_Lib::ip_wild_to_mask( $host );

					if ( ! ITSEC_Ban_Users_Admin::is_ip_whitelisted( $converted_host, $raw_white_list, $current ) ) {

						if ( $server_type === 'nginx' ) { //NGINX rules
							$host_rule = "\tdeny " . trim( $converted_host ) . ';';
						} else { //rules for all other servers
							$host_rule = 'Deny from ' . trim( $converted_host );
						}

						$host_list .= $host_rule . PHP_EOL; //build large string of all hosts

					} else {

						/**
						 * @todo warn the user the ip to be banned is whitelisted
						 */

					}

				}

			}

			//Process the agents list
			if ( is_array( $raw_agent_list ) ) {

				$count = 1; //to help us find the last one

				foreach ( $raw_agent_list as $agent ) {

					if ( strlen( trim( $agent ) ) > 1 ) {

						//if it isn't the last rule make sure we add an or
						if ( $count < sizeof( $agent ) ) {
							$end = ' [NC,OR]' . PHP_EOL;
						} else {
							$end = '[NC]' . PHP_EOL;
						}

						if ( $server_type === 'nginx' ) { //NGINX rule
							$converted_agent = 'if ($http_user_agent ~* "^' . trim( $agent ) . '"){ return 403; }' . PHP_EOL;
						} else { //Rule for all other servers
							$converted_agent = 'RewriteCond %{HTTP_USER_AGENT} ^' . trim( $agent ) . $end;
						}

						$agent_list .= $converted_agent; //build large string of all agents

					}

					$count ++;

				}

			}

		}

		$rules = ''; //initialize rules

		//Start with default rules if we have them
		if ( strlen( $default_list ) > 1 ) {

			$rules .= $default_list;

		}

		//Add banned host lists
		if ( strlen( $host_list ) > 1 || strlen( $default_list ) ) {

			if ( strlen( $default_list ) > 1 ) {
				$rules .= PHP_EOL;
			}

			if ( $server_type === 'nginx' && strlen( $host_list ) > 1 ) { //NGINX rules

				$rules .= $host_list;

			} elseif ( strlen( $host_list ) > 1 ) {

				$rules .= 'Order allow,deny' . PHP_EOL;
				$rules .= $host_list;
				$rules .= 'Allow from all';

			}

		}

		//Add banned user agents
		if ( strlen( $agent_list ) > 1 ) {

			if ( strlen( $default_list ) > 1 || strlen( $host_list ) > 1 ) {
				$rules .= PHP_EOL;
			}

			if ( $server_type === 'nginx' ) { //NGINX rules

				$rules .= $agent_list;

			} else {

				$rules .= '<IfModule mod_rewrite.c>' . PHP_EOL;
				$rules .= 'RewriteEngine On' . PHP_EOL;
				$rules .= $agent_list;
				$rules .= 'RewriteRule ^(.*)$ - [F,L]' . PHP_EOL;
				$rules .= '</IfModule>' . PHP_EOL;

			}

		}

		if ( strlen( $rules ) > 0 ) {
			$rules = explode( PHP_EOL, $rules );
		} else {
			$rules = false;
		}

		//create a proper array for writing
		$rules_array[] = array( 'type' => 'htaccess', 'priority' => 1, 'name' => 'Ban Users', 'rules' => $rules, );

		return $rules_array;

	}

	/**
	 * Sets the status in the plugin dashboard
	 *
	 * @since 4.0
	 *
	 * @return array statuses
	 */
	public function dashboard_status( $statuses ) {

		if ( $this->settings['enabled'] === true ) {

			$status_array = 'safe-low';
			$status       = array( 'text' => __( 'You are blocking known bad hosts and agents with the ban users tool.', 'ithemes-security' ), 'link' => '#itsec_ban_users_enabled', );

		} else {

			$status_array = 'low';
			$status       = array( 'text' => __( 'You are not blocking any users that are known to be a problem. Consider turning on the Ban Users feature.', 'ithemes-security' ), 'link' => '#itsec_ban_users_enabled', );

		}

		array_push( $statuses[$status_array], $status );

		return $statuses;

	}

	/**
	 * Empty callback function
	 */
	public function empty_callback_function() {
	}

	/**
	 * Execute admin initializations
	 *
	 * @return void
	 */
	public function initialize_admin() {

		//default blacklist section
		add_settings_section(
			'ban_users_default',
			__( 'Default Blacklist', 'ithemes-security' ),
			array( $this, 'empty_callback_function' ),
			'security_page_toplevel_page_itsec_settings'
		);

		//Enabled section
		add_settings_section(
			'ban_users_enabled',
			__( 'Configure Ban Users', 'ithemes-security' ),
			array( $this, 'empty_callback_function' ),
			'security_page_toplevel_page_itsec_settings'
		);

		//primary settings section
		add_settings_section(
			'ban_users_settings',
			__( 'Configure Ban Users', 'ithemes-security' ),
			array( $this, 'empty_callback_function' ),
			'security_page_toplevel_page_itsec_settings'
		);

		//default list field
		add_settings_field(
			'itsec_ban_users[default]',
			__( 'Default Blacklist', 'ithemes-security' ),
			array( $this, 'ban_users_default' ),
			'security_page_toplevel_page_itsec_settings',
			'ban_users_default'
		);

		//enabled field
		add_settings_field(
			'itsec_ban_users[enabled]',
			__( 'Ban Users', 'ithemes-security' ),
			array( $this, 'ban_users_enabled' ),
			'security_page_toplevel_page_itsec_settings',
			'ban_users_enabled'
		);

		//host list field
		add_settings_field(
			'itsec_ban_users[host_list]',
			__( 'Ban Hosts', 'ithemes-security' ),
			array( $this, 'ban_users_host_list' ),
			'security_page_toplevel_page_itsec_settings',
			'ban_users_settings'
		);

		//agent _list field
		add_settings_field(
			'itsec_ban_users[agent_list]',
			__( 'Ban User Agents', 'ithemes-security' ),
			array( $this, 'ban_users_agent_list' ),
			'security_page_toplevel_page_itsec_settings',
			'ban_users_settings'
		);

		//agent _list field
		add_settings_field(
			'itsec_ban_users[white_list]',
			__( 'Whitelist Users', 'ithemes-security' ),
			array( $this, 'ban_users_white_list' ),
			'security_page_toplevel_page_itsec_settings',
			'ban_users_settings'
		);

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_ban_users',
			array( $this, 'sanitize_module_input' )
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
	private function initialize( $core ) {

		$this->core        = $core;
		$this->settings    = get_site_option( 'itsec_ban_users' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_filter( 'itsec_file_rules', array( $this, 'build_rewrite_rules' ) );

		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_action( 'itsec_admin_init', array( $this, 'initialize_admin' ) ); //initialize admin area
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) ); //enqueue scripts for admin page
		add_filter( 'itsec_add_dashboard_status', array( $this, 'dashboard_status' ) ); //add information for plugin status
		add_filter( 'itsec_tracking_vars', array( $this, 'tracking_vars' ) );

		//manually save options on multisite
		if ( is_multisite() ) {
			add_action( 'itsec_admin_init', array( $this, 'save_network_options' ) ); //save multisite options
		}

	}

	/**
	 * Inserts an IP address into the htaccess ban list.
	 *
	 * @since 4.0
	 *
	 * @param      $ip
	 * @param null $ban_list
	 * @param null $white_list
	 *
	 * @return void
	 */
	public static function insert_ip( $ip, $ban_list = null, $white_list = null ) {

		$settings = get_site_option( 'itsec_ban_users' );

		if ( $ban_list !== null ) {

			$ban_list = $settings['ban_list'];

		}

		if ( $white_list !== null ) {

			$white_list = $settings['white_list'];

		}

	}

	/**
	 * Determines whether a given IP address is whitelisted
	 *
	 * @param  string  $ip_to_check ip to check
	 * @param  array   $white_ips   ip list to compare to if not yet saved to options
	 * @param  boolean $current     whether to whitelist the current ip or not (due to saving, etc)
	 *
	 * @return boolean               true if whitelisted or false
	 */
	public static function is_ip_whitelisted( $ip_to_check, $white_ips = null, $current = false ) {

		if ( $white_ips === null ) {

			$settings = get_site_option( 'itsec_ban_users' );

			$white_ips = $settings['white_list'];

		}

		if ( $current === true ) {
			$white_ips[] = ITSEC_Lib::get_ip(); //add current user ip to whitelist to check automatically
		}

		foreach ( $white_ips as $white_ip ) {

			$converted_white_ip = ITSEC_Lib::ip_wild_to_mask( $white_ip );

			$check_range = ITSEC_Lib::cidr_to_range( $converted_white_ip );
			$ip_range    = ITSEC_Lib::cidr_to_range( $ip_to_check );

			if ( sizeof( $check_range ) === 2 ) { //range to check

				$check_min = ip2long( $check_range[0] );
				$check_max = ip2long( $check_range[1] );

				if ( sizeof( $ip_range ) === 2 ) {

					$ip_min = ip2long( $ip_range[0] );
					$ip_max = ip2long( $ip_range[1] );

					if ( ( $check_min < $ip_min && $ip_min < $check_max ) || ( $check_min < $ip_max && $ip_max < $check_max ) ) {
						return true;
					}

				} else {

					$ip = ip2long( $ip_range[0] );

					if ( $check_min < $ip && $ip < $check_max ) {
						return true;
					}

				}

			} else { //single ip to check

				$check = ip2long( $check_range[0] );

				if ( sizeof( $ip_range ) === 2 ) {

					$ip_min = ip2long( $ip_range[0] );
					$ip_max = ip2long( $ip_range[1] );

					if ( $ip_min < $check && $check < $ip_max ) {
						return true;
					}

				} else {

					$ip = ip2long( $ip_range[0] );

					if ( $check == $ip ) {
						return true;
					}

				}

			}

		}

		return false;

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function metabox_advanced_settings() {

		echo '<p>' . __( 'This feature allows you to completely ban hosts and user agents from your site without having to manage any configuration of your server. Any IP addresses or user agents found in the lists below will not be allowed any access to your site.', 'ithemes-security' ) . '</p>';

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'ban_users_default', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'ban_users_enabled', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'ban_users_settings', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save Changes', 'ithemes-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

		echo '</form>';

	}

	/**
	 * Sanitize and validate input
	 *
	 * @param  Array $input array of input fields
	 *
	 * @return Array         Sanitized array
	 */
	public function sanitize_module_input( $input ) {

		$no_errors = false; //start out assuming they entered a bad IP somewhere

		//Sanitize checkbox features
		$input['enabled'] = ( isset( $input['enabled'] ) && intval( $input['enabled'] == 1 ) ? true : false );
		$input['default'] = ( isset( $input['default'] ) && intval( $input['default'] == 1 ) ? true : false );

		//process agent list
		if ( isset( $input['agent_list'] ) && ! is_array( $input['agent_list'] ) ) {
			$agents = explode( PHP_EOL, $input['agent_list'] );
		} else {
			$agents = array();
		}

		$good_agents = array();

		foreach ( $agents as $agent ) {
			$good_agents[] = quotemeta( sanitize_text_field( $agent ) );
		}

		$input['agent_list'] = $good_agents;

		//Process white list
		if ( isset( $input['white_list'] ) && ! is_array( $input['white_list'] ) ) {
			$white_listed_addresses = explode( PHP_EOL, $input['white_list'] );
		} else {
			$white_listed_addresses = array();
		}

		$bad_white_listed_ips = array();
		$raw_white_listed_ips = array();

		foreach ( $white_listed_addresses as $index => $address ) {

			if ( strlen( trim( $address ) ) > 0 ) {

				if ( ITSEC_Lib::validates_ip_address( $address ) === false ) {

					$bad_white_listed_ips[] = filter_var( $address, FILTER_SANITIZE_STRING );

				}

				$raw_white_listed_ips[] = filter_var( $address, FILTER_SANITIZE_STRING );

			} else {
				unset( $white_listed_addresses[$index] );
			}

		}

		$raw_white_listed_ips = array_unique( $raw_white_listed_ips );

		if ( sizeof( $bad_white_listed_ips ) > 0 ) {

			$input['enabled'] = false; //disable ban users list

			$type    = 'error';
			$message = '';

			$message .= sprintf( '%s<br /><br />', __( 'Note that the ban users feature has been disabled until the following errors are corrected:', 'ithemes-security' ) );

			foreach ( $bad_white_listed_ips as $bad_ip ) {
				$message .= sprintf( '%s %s<br />', $bad_ip, __( 'is not a valid address in the whitelist users box.', 'ithemes-security' ) );
			}

			add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

		} else {

			$no_errors = true;

		}

		$input['white_list'] = $raw_white_listed_ips;

		//Process hosts list
		if ( isset( $input['host_list'] ) && ! is_array( $input['host_list'] ) ) {
			$addresses = explode( PHP_EOL, $input['host_list'] );
		} else {
			$addresses = array();
		}

		$bad_ips = array();
		$raw_ips = array();

		foreach ( $addresses as $index => $address ) {

			if ( strlen( trim( $address ) ) > 0 ) {

				if ( ITSEC_Lib::validates_ip_address( $address ) === false ) {

					$bad_ips[] = filter_var( $address, FILTER_SANITIZE_STRING );

				}

				$raw_ips[] = filter_var( $address, FILTER_SANITIZE_STRING );

			} else {
				unset( $addresses[$index] );
			}

		}

		$raw_ips = array_unique( $raw_ips );

		if ( sizeof( $bad_ips ) > 0 ) {

			$input['enabled'] = false; //disable ban users list

			$type = 'error';

			if ( $no_errors === true ) {
				$message .= sprintf( '%s<br /><br />', __( 'Note that the ban users feature has been disabled until the following errors are corrected:', 'ithemes-security' ) );
			}

			foreach ( $bad_ips as $bad_ip ) {
				$message .= sprintf( '%s %s<br />', $bad_ip, __( 'is not a valid address in the ban users box.', 'ithemes-security' ) );
			}

			add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

		} else {

			$no_errors = true;

		}

		$input['host_list'] = $raw_ips;

		if ( $no_errors === true ) {

			if (
				! isset( $type ) &&
				(
					$input['host_list'] !== $this->settings['host_list'] ||
					$input['white_list'] !== $this->settings['white_list'] ||
					$input['enabled'] !== $this->settings['enabled'] ||
					$input['default'] !== $this->settings['default'] ||
					$input['agent_list'] !== $this->settings['agent_list']
				)

			) {

				add_site_option( 'itsec_rewrites_changed', true );

			}

		}

		if ( is_multisite() ) {

			if ( isset( $type ) ) {

				$error_handler = new WP_Error();

				$error_handler->add( $type, $message );

				$this->core->show_network_admin_notice( $error_handler );

			} else {

				$this->core->show_network_admin_notice( false );

			}

			$this->settings = $input;

		}

		return $input;

	}

	/**
	 * Prepare and save options in network settings
	 *
	 * @return void
	 */
	public function save_network_options() {

		if ( isset( $_POST['itsec_ban_users'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'ithemes-security' ) );
			}

			update_site_option( 'itsec_ban_users', $_POST['itsec_ban_users'] ); //we must manually save network options

		}

	}

	/**
	 * Adds fields that will be tracked for Google Analytics
	 *
	 * @since 4.0
	 *
	 * @param array $vars tracking vars
	 *
	 * @return array tracking vars
	 */
	public function tracking_vars( $vars ) {

		$vars['itsec_ban_users'] = array(
			'enabled' => '0:b',
			'default' => '0:b',
		);

		return $vars;

	}

}