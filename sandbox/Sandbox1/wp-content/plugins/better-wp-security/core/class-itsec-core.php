<?php
/**
 * iThemes Security Core.
 *
 * Core class for iThemes Security sets up globals and other items and dispatches modules.
 *
 * @package iThemes_Security
 *
 * @since   4.0
 *
 * @global array  $itsec_globals Global variables for use throughout iThemes Security.
 * @global object $itsec_files   iThemes Security file writer.
 * @global object $itsec_logger  iThemes Security logging class.
 * @global object $itsec_lockout Class for handling lockouts.
 *
 */
if ( ! class_exists( 'ITSEC_Core' ) ) {

	final class ITSEC_Core {

		private
			$tooltip_modules,
			$pages,
			$tracking_vars,
			$toc_items;

		public
			$available_pages;

		/**
		 * Loads core functionality across both admin and frontend.
		 *
		 * Creates all plugin globals, registers activation and related hooks,
		 * loads the text domain and loads all plugin modules
		 *
		 * @since  4.0
		 *
		 * @access private
		 *
		 * @param string $plugin_dir the main plugin file
		 *
		 * @return void
		 */
		function __construct( $plugin_dir ) {

			global $itsec_globals, $itsec_files, $itsec_logger, $itsec_lockout;

			$this->tooltip_modules = array(); //initialize tooltip modules.

			$upload_dir = wp_upload_dir(); //get the full upload directory array so we can grab the base directory.

			//Set plugin defaults
			$itsec_globals = array(
				'plugin_build'       => 4002, //plugin build number - used to trigger updates
				'plugin_access_lvl'  => 'manage_options', //Access level required to access plugin options
				'plugin_name'        => __( 'iThemes Security', 'ithemes-security' ), //the name of the plugin
				'plugin_base'        => str_replace( WP_PLUGIN_DIR . '/', '', $plugin_dir ),
				'plugin_file'        => $plugin_dir, //the main plugin file
				'plugin_dir'         => plugin_dir_path( $plugin_dir ), //the path of the plugin directory
				'plugin_url'         => plugin_dir_url( $plugin_dir ), //the URL of the plugin directory
				'is_iwp_call'        => false,
				'ithemes_dir'        => $upload_dir['basedir'] . '/ithemes-security', //folder for saving iThemes Security files
				'ithemes_log_dir'    => $upload_dir['basedir'] . '/ithemes-security/logs', //folder for saving iThemes Security logs
				'ithemes_backup_dir' => $upload_dir['basedir'] . '/ithemes-security/backups', //folder for saving iThemes Backup files
				'current_time'       => current_time( 'timestamp' ), //the current local time in unix timestamp format
				'current_time_gmt'   => current_time( 'timestamp', 1 ), //the current gmt time in unix timestamp format
				'settings'           => get_site_option( 'itsec_global' ),
				'free_modules'       => array(
					'four-oh-four',
					'admin-user',
					'away-mode',
					'ban-users',
					'brute-force',
					'backup',
					'file-change',
					'hide-backend',
					'ssl',
					'strong-passwords',
					'tweaks',
					'content-directory',
					'database-prefix',
					'help',
					'widgets',
				),
				'pro_modules'        => array(
					'help',
					'widgets',
				),
			);

			$this->pages = array(
				array(
					'title' => __( 'Settings', 'ithemes-security' ),
					'slug'  => 'settings',
				),
				array(
					'title' => __( 'Advanced', 'ithemes-security' ),
					'slug'  => 'advanced',
				),
				array(
					'title' => __( 'Backups', 'ithemes-security' ),
					'slug'  => 'backups',
				),
				array(
					'title' => __( 'Logs', 'ithemes-security' ),
					'slug'  => 'logs',
				),
				array(
					'title' => __( 'Help', 'ithemes-security' ),
					'slug'  => 'help',
				),
			);

			//Determine if we need to run upgrade scripts
			$plugin_data = get_site_option( 'itsec_data' );

			if ( $plugin_data === false ) { //if plugin data does exist
				$plugin_data = $this->save_plugin_data();
			}

			$itsec_globals['data'] = $plugin_data; //adds plugin data to $itsec_globals

			//Add Javascripts script
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) ); //enqueue scripts for admin page

			//load utility functions
			if ( ! class_exists( 'ITSEC_Lib' ) ) {
				require( $itsec_globals['plugin_dir'] . 'core/class-itsec-lib.php' );
			}

			//load logging functions
			if ( ! class_exists( 'ITSEC_Logger' ) ) {

				require( $itsec_globals['plugin_dir'] . 'core/class-itsec-logger.php' );
				$itsec_logger = new ITSEC_Logger( $this );

			}

			//load lockout functions
			if ( ! class_exists( 'ITSEC_Lockout' ) ) {

				require( $itsec_globals['plugin_dir'] . 'core/class-itsec-lockout.php' );
				$itsec_lockout = new ITSEC_Lockout();

			}

			//load file utility functions
			if ( ! class_exists( 'ITSEC_Files' ) ) {

				require( $itsec_globals['plugin_dir'] . 'core/class-itsec-files.php' );
				$itsec_files = new ITSEC_Files();

			}

			//load the text domain
			load_plugin_textdomain( 'ithemes-security', false, $itsec_globals['plugin_dir'] . 'core/languages' );

			//builds admin menus after modules are loaded
			if ( is_admin() ) {

				//load logging functions
				if ( ! class_exists( 'ITSEC_Dashboard_Admin' ) ) {

					require( $itsec_globals['plugin_dir'] . 'core/class-itsec-dashboard-admin.php' );
					new ITSEC_Dashboard_Admin( $this );

				}

				//load logging functions
				if ( ! class_exists( 'ITSEC_Global_Settings' ) ) {

					require( $itsec_globals['plugin_dir'] . 'core/class-itsec-global-settings.php' );
					new ITSEC_Global_Settings( $this );

				}

				//Process support plugin nag
				add_action( 'itsec_admin_init', array( $this, 'support_nag' ) );

				//Process support plugin nag
				add_action( 'itsec_admin_init', array( $this, 'setup_nag' ) );

				//add action link
				add_filter( 'plugin_action_links', array( $this, 'add_action_link' ), 10, 2 );

				//add plugin meta links
				add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 4 );

				//Register all plugin modules
				add_action( 'plugins_loaded', array( $this, 'register_modules' ) );

				//Run ajax for tooltips
				add_action( 'wp_ajax_itsec_tooltip_ajax', array( $this, 'admin_tooltip_ajax' ) );
				add_action( 'wp_ajax_itsec_tracking_ajax', array( $this, 'admin_tracking_ajax' ) );

				$this->build_admin();

			}

			//require plugin setup information
			if ( ! class_exists( 'ITSEC_Setup' ) ) {
				require( $itsec_globals['plugin_dir'] . 'core/class-itsec-setup.php' );
			}

			register_activation_hook( $itsec_globals['plugin_file'], array( 'ITSEC_Setup', 'on_activate' ) );
			register_deactivation_hook( $itsec_globals['plugin_file'], array( 'ITSEC_Setup', 'on_deactivate' ) );
			register_uninstall_hook( $itsec_globals['plugin_file'], array( 'ITSEC_Setup', 'on_uninstall' ) );

			if ( isset( $itsec_globals['settings']['infinitewp_compatibility'] ) && $itsec_globals['settings']['infinitewp_compatibility'] === true ) {

				$HTTP_RAW_POST_DATA = @file_get_contents( 'php://input' );

				if ( $HTTP_RAW_POST_DATA !== false && strlen( $HTTP_RAW_POST_DATA ) > 0 ) {

					$data = base64_decode( $HTTP_RAW_POST_DATA );

					if ( strpos( $data, 's:10:"iwp_action";' ) !== false ) {
						$itsec_globals['is_iwp_call'] = true;
					}

				}

			}

			//load all present modules
			$this->load_modules();

			//see if the saved build version is older than the current build version
			if ( isset( $plugin_data['build'] ) && $plugin_data['build'] !== $itsec_globals['plugin_build'] ) {
				new ITSEC_Setup( 'activate', $plugin_data['build'] ); //run upgrade scripts
			}

			//See if they're upgrade from Better WP Security
			if ( is_multisite() ) {

				switch_to_blog( 1 );

				$bwps_options = get_option( 'bit51_bwps' );

				restore_current_blog();

			} else {

				$bwps_options = get_option( 'bit51_bwps' );

			}

			if ( $bwps_options !== false ) {
				add_action( 'plugins_loaded', array( $this, 'do_upgrade' ) );
			}

			add_action( 'itsec_wpconfig_metabox', array( $itsec_files, 'config_metabox_contents' ) );
			add_action( 'itsec_rewrite_metabox', array( $itsec_files, 'rewrite_metabox_contents' ) );

		}

		/**
		 * Add action link to plugin page.
		 *
		 * Adds plugin settings link to plugin page in WordPress admin area.
		 *
		 * @since 4.0
		 *
		 * @param object $links Array of WordPress links
		 * @param string $file  String name of current file
		 *
		 * @return object Array of WordPress links
		 *
		 */
		function add_action_link( $links, $file ) {

			static $this_plugin;

			global $itsec_globals;

			if ( empty( $this_plugin ) ) {
				$this_plugin = $itsec_globals['plugin_base'];
			}

			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=itsec">' . __( 'Dashboard', 'ithemes-security' ) . '</a>';
				array_unshift( $links, $settings_link );
			}

			return $links;
		}

		/**
		 * Adds links to the plugin row meta
		 *
		 * @since 4.0
		 *
		 * @param array  $meta        Existing meta
		 * @param string $plugin_file the wp plugin slug (path)
		 * @param array  $plugin_data the data WP harvested from the plugin header
		 * @param string $status      the plugin activation status
		 *
		 * @return array
		 */
		function add_plugin_meta_links( $meta, $plugin_file ) {

			global $itsec_globals;

			if ( $itsec_globals['plugin_base'] == $plugin_file ) {
				$meta[] = '<a href="http://www.ithemes.com/security" target="_blank">' . __( 'Get Pro Setup', 'ithemes-security' ) . '</a>';
				$meta[] = '<a href="http://www.ithemes.com/forum" target="_blank">' . __( 'Support', 'ithemes-security' ) . '</a>';
				$meta[] = '<a href="http://www.ithemes.com/security" target="_blank">' . __( 'FAQs', 'ithemes-security' ) . '</a>';
			}

			return $meta;
		}

		/**
		 * Add items to the table of contents
		 *
		 * @since 4.0
		 *
		 * @param array $item the item to add to the table of content
		 *
		 * @return void
		 */
		public function add_toc_item( $item ) {

			$this->toc_items[] = $item;

		}

		/**
		 * Process the ajax call for the tooltip.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function admin_tooltip_ajax() {

			global $itsec_globals;

			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_tooltip_nonce' ) ) {
				die ();
			}

			if ( sanitize_text_field( $_POST['module'] ) == 'close' ) {

				$data                       = $itsec_globals['data'];
				$data['tooltips_dismissed'] = true;
				update_site_option( 'itsec_data', $data );

			} else {

				call_user_func_array( $this->tooltip_modules[sanitize_text_field( $_POST['module'] )]['callback'], array() );

			}

			die(); // this is required to return a proper result

		}

		/**
		 * Process the ajax call for the tracking script.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function admin_tracking_ajax() {

			global $itsec_globals;

			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'itsec_tracking_nonce' ) ) {
				die ();
			}

			if ( sanitize_text_field( $_POST['module'] ) == 'close' ) {

				$data                       = $itsec_globals['data'];
				$data['tooltips_dismissed'] = true;
				update_site_option( 'itsec_data', $data );

			} else {

				call_user_func_array( $this->tooltip_modules[sanitize_text_field( $_POST['module'] )]['callback'], array() );

			}

			die(); // this is required to return a proper result

		}

		/**
		 * Displays plugin admin notices.
		 *
		 * @since 4.0
		 *
		 * @return  void
		 */
		public function admin_notices() {

			if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) !== false || strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_advanced' ) !== false ) {

				$errors = get_settings_errors( 'itsec' );

				$updated = '';

				if ( get_site_option( 'itsec_manual_update' ) == true ) {

					delete_site_option( 'itsec_manual_update' );

					if ( ITSEC_Lib::get_server() == 'nginx' ) {

						$server = __( 'NGINX conf file and/or restart your NGINX server', 'ithemes-security' );

					} else {

						$server = __( '.htaccess file', 'ithemes-security' );

					}

					$updated = sprintf(
						'<br />%s %s %s <a href="%s">%s</a> %s',
						__( 'As you have not allowed this plugin to update system files you must update your', 'ithemes-security' ),
						$server,
						__( 'as well as your wp-config.php file manually. Rules to insert in both files can be found on the Dashboard page.', 'ithemes-security' ),
						'?page=toplevel_page_itsec_settings#itsec_global_write_files',
						__( 'Click here', 'ithemes-security' ),
						__( 'to allow this plugin to write to these files.', 'ithemes-security' )
					);

				}

				if ( sizeof( $errors ) === 0 && isset ( $_GET['settings-updated'] ) && sanitize_text_field( $_GET['settings-updated'] ) == 'true' ) {

					add_settings_error( 'itsec', esc_attr( 'settings_updated' ), __( 'Settings Updated', 'ithemes-security' ) . $updated, 'updated' );

				}

			}

			settings_errors( 'itsec' );

		}

		/**
		 * Add Tracking Javascript.
		 *
		 * Adds javascript for tracking settings to all itsec admin pages
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function admin_script() {

			global $itsec_globals;

			$tips     = '';
			$messages = array();

			if ( sizeof( $this->tooltip_modules ) > 0 ) {

				uasort( $this->tooltip_modules, array( $this, 'sort_tooltips' ) );

				$tips .= '<ol>';

				foreach ( $this->tooltip_modules as $module => $tip ) {

					$tips .= '<li class="' . $tip['class'] . ' tooltip_' . $module . '"><h4>' . $tip['heading'] . '</h4><p>' . $tip['text'] . '</p><a href="' . $module . '" class="itsec_tooltip_ajax button-primary">' . $tip['link_text'] . '</a></li>';
					$messages[$module] = array(
						'success' => $tip['success'],
						'failure' => $tip['failure'],
					);

				}

				$tips .= '</ol>';

			}

			wp_register_style( 'itsec_notice_css', $itsec_globals['plugin_url'] . 'core/css/itsec_notice.css' ); //add multi-select css
			wp_enqueue_style( 'itsec_notice_css' );
			wp_enqueue_script( 'itsec_footer', $itsec_globals['plugin_url'] . 'core/js/admin-dashboard-footer.js', 'jquery', $itsec_globals['plugin_build'], true );

			if ( ! isset( $itsec_globals['data']['tooltips_dismissed'] ) || $itsec_globals['data']['tooltips_dismissed'] === false ) {

				wp_enqueue_style( 'wp-pointer' );
				wp_enqueue_script( 'wp-pointer' );
				wp_enqueue_script( 'itsec_tooltip', $itsec_globals['plugin_url'] . 'core/js/admin-tooltip.js', 'jquery', $itsec_globals['plugin_build'], true );
				wp_localize_script( 'itsec_tooltip', 'itsec_tooltip_text', array(
					'header'   => __( 'Important First Steps', 'ithemes-security' ),
					'text'     => $tips,
					'nonce'    => wp_create_nonce( 'itsec_tooltip_nonce' ),
					'messages' => $messages,
				) );

			}

			if ( ( isset( $itsec_globals['settings']['allow_tracking'] ) && $itsec_globals['settings']['allow_tracking'] === true && strpos( get_current_screen()->id, 'itsec' ) !== false ) || get_option( 'bit51_bwps' ) !== false ) {

				wp_enqueue_script( 'itsec_tracking', $itsec_globals['plugin_url'] . 'core/js/tracking.js', 'jquery', $itsec_globals['plugin_build'] );
				wp_localize_script( 'itsec_tracking', 'itsec_tracking_vars', array( 'vars' => $this->tracking_vars, 'nonce' => wp_create_nonce( 'itsec_tracking_nonce' ) ) );

			}

		}

		/**
		 * Creates admin tabs.
		 *
		 * Used to display module tabs across all iThemes Security admin pages.
		 *
		 * @since 4.0
		 *
		 * @param  string $current current tab id
		 *
		 * @return void
		 */
		public function admin_tabs( $current = null ) {

			if ( $current == null ) {
				$current = 'itsec';
			}

			echo '<div id="icon-themes" class="icon32"><br></div>';
			echo '<h2 class="nav-tab-wrapper">';

			$class = ( $current == 'itsec' ) ? ' nav-tab-active' : '';
			echo '<a class="nav-tab' . $class . '" href="?page=itsec">' . __( 'Dashboard', 'ithemes-security' ) . '</a>';

			foreach ( $this->pages as $page ) {

				$class = ( $current == 'toplevel_page_itsec_' . $page['slug'] ) ? ' nav-tab-active' : '';
				echo '<a class="nav-tab' . $class . '" href="?page=toplevel_page_itsec_' . $page['slug'] . '">' . $page['title'] . '</a>';

			}

			echo '</h2>';

		}

		/**
		 * Enqueue actions to build the admin pages.
		 *
		 * Calls all the needed actions to build any given admin page.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function build_admin() {

			add_action( 'admin_notices', array( $this, 'admin_notices' ) );

			add_action( 'admin_init', array( $this, 'execute_admin_init' ) );

			if ( is_multisite() ) { //must be network admin in multisite
				add_action( 'network_admin_menu', array( $this, 'setup_primary_admin' ) );
			} else {
				add_action( 'admin_menu', array( $this, 'setup_primary_admin' ) );
			}

		}

		/**
		 * Prints out all settings sections added to a particular settings page.
		 *
		 * adapted from core function for better styling within meta_box.
		 *
		 * @since 4.0
		 *
		 * @param string  $page       The slug name of the page whos settings sections you want to output
		 * @param string  $section    the section to show
		 * @param boolean $show_title Whether or not the title of the section should display: default true.
		 *
		 * @return void
		 */
		public function do_settings_section( $page, $section, $show_title = true ) {

			global $wp_settings_sections, $wp_settings_fields;

			if ( ! isset( $wp_settings_sections ) || ! isset( $wp_settings_sections[$page] ) || ! isset( $wp_settings_sections[$page][$section] ) ) {
				return;
			}

			$section = $wp_settings_sections[$page][$section];

			if ( $section['title'] && $show_title === true )
				echo "<h4>{$section['title']}</h4>\n";

			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[$page] ) || ! isset( $wp_settings_fields[$page][$section['id']] ) )
				return;

			echo '<table class="form-table" id="' . $section['id'] . '">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';

		}

		/**
		 * Calls upgrade script for older versions (pre 4.x).
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function do_upgrade() {

			new ITSEC_Setup( 'activate', 3064 ); //run upgrade scripts
		}

		/**
		 * Enqueue the styles for the admin area so WordPress can load them.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function enqueue_admin_styles() {

			global $itsec_globals;

			wp_enqueue_style( 'itsec_admin_styles' );
			do_action( $itsec_globals['plugin_url'] . 'enqueue_admin_styles' );

		}

		/**
		 * Registers admin styles and handles other items required at admin_init
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function execute_admin_init() {

			global $itsec_globals;

			wp_register_style( 'itsec_admin_styles', $itsec_globals['plugin_url'] . 'core/css/ithemes.css' );
			do_action( 'itsec_admin_init' ); //execute modules init scripts

		}

		/**
		 * Getter for Table of Contents items.
		 *
		 * @since 4.0
		 *
		 * @return mixed array of toc items
		 */
		public function get_toc_items() {

			return $this->toc_items;

		}

		/**
		 * Loads required plugin modules.
		 *
		 *
		 * Recursively loads all modules in the modules/ folder by calling their index.php.
		 * Note: Do not modify this area other than to specify modules to load.
		 * Build all functionality into the appropriate module.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function load_modules() {

			global $itsec_globals;

			$free_modules_folder = $itsec_globals['plugin_dir'] . 'modules/free';
			$pro_modules_folder  = $itsec_globals['plugin_dir'] . 'modules/pro';

			$has_pro = is_dir( $pro_modules_folder );

			if ( $has_pro ) {

				foreach ( $itsec_globals['pro_modules'] as $module ) {

					require( $pro_modules_folder . '/' . $module . '/index.php' );

				}

			}

			foreach ( $itsec_globals['free_modules'] as $module ) {

				if ( $has_pro === false || ! in_array( $module, $itsec_globals['pro_modules'] ) ) {
					require( $free_modules_folder . '/' . $module . '/index.php' );
				}

			}

		}

		/**
		 * Enqueue JavaScripts for admin page rendering amd execute calls to add further meta_boxes.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function page_actions() {

			do_action( 'itsec_add_admin_meta_boxes', $this->available_pages );

			//Set two columns for all plugins using this framework
			add_screen_option( 'layout_columns', array( 'max' => 2, 'default' => 2 ) );

			//Enqueue common scripts and try to keep it simple
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );

		}

		/**
		 * Prints network admin notices.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function print_network_admin_notice() {

			global $itsec_saved_network_notices;

			echo $itsec_saved_network_notices;

			unset( $itsec_saved_network_notices ); //delete any saved messages

		}

		/**
		 * Register modules that will use the lockout service
		 *
		 * @return void
		 */
		public function register_modules() {

			$this->tooltip_modules = apply_filters( 'itsec_tooltip_modules', $this->tooltip_modules );
			$this->tracking_vars   = apply_filters( 'itsec_tracking_vars', $this->tracking_vars );

		}

		/**
		 * Render basic structure of the settings page.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function render_page() {

			global $itsec_globals;

			if ( is_multisite() ) {
				$screen = substr( get_current_screen()->id, 0, strpos( get_current_screen()->id, '-network' ) );
			} else {
				$screen = get_current_screen()->id; //the current screen id
			}

			?>

			<div class="wrap">

				<h2><?php echo $itsec_globals['plugin_name'] . ' - ' . get_admin_page_title(); ?></h2>
				<?php
				if ( isset ( $_GET['page'] ) ) {
					$this->admin_tabs( $_GET['page'] );
				} else {
					$this->admin_tabs();
				}
				?>

				<?php
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
				?>

				<div id="poststuff">

					<?php
					//set appropriate action for multisite or standard site
					if ( is_multisite() ) {
						$action = '';
					} else {
						$action = 'options.php';
					}
					?>
					<?php if ($screen == 'security_page_toplevel_page_itsec_settings') { ?>
					<form name="security_page_toplevel_page_itsec_settings" method="post"
					      action="<?php echo $action; ?>" class="itsec-settings-form">
						<?php } ?>

						<div id="post-body"
						     class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

							<div id="postbox-container-2" class="postbox-container">
								<?php do_action( 'itsec_page_top', $screen ); ?>
								<?php do_meta_boxes( $screen, 'top', null ); ?>
								<?php do_meta_boxes( $screen, 'normal', null ); ?>
								<?php do_action( 'itsec_page_middle', $screen ); ?>
								<?php do_meta_boxes( $screen, 'advanced', null ); ?>
								<?php do_meta_boxes( $screen, 'bottom', null ); ?>
								<?php do_action( 'itsec_page_bottom', $screen ); ?>
							</div>

							<div id="postbox-container-1" class="postbox-container">
								<?php do_meta_boxes( $screen, 'priority_side', null ); ?>
								<?php do_meta_boxes( $screen, 'side', null ); ?>
								<?php if ( $screen == 'security_page_toplevel_page_itsec_settings' ) { ?>
									<a href="#"
									   class="itsec_return_to_top"><?php _e( 'Return to top', 'ithemes-security' ); ?></a>
								<?php } ?>
							</div>


						</div>

						<?php if ($screen == 'security_page_toplevel_page_itsec_settings') { ?>
					</form>
				<?php } ?>
					<!-- #post-body -->

				</div>
				<!-- #poststuff -->

			</div><!-- .wrap -->

		<?php
		}

		/**
		 * Saves general plugin data to determine global items.
		 *
		 * Sets up general plugin data such as build, and others.
		 *
		 * @since 4.0
		 *
		 * @return array plugin data
		 */
		public function save_plugin_data() {

			global $itsec_globals;

			$save_data = false; //flag to avoid saving data if we don't have to

			$plugin_data = get_site_option( 'itsec_data' );

			//Update the build number if we need to
			if ( ! isset( $plugin_data['build'] ) || ( isset( $plugin_data['build'] ) && $plugin_data['build'] !== $itsec_globals['plugin_build'] ) ) {
				$plugin_data['build'] = $itsec_globals['plugin_build'];
				$save_data            = true;
			}

			//update the activated time if we need to in order to tell when the plugin was installed
			if ( ! isset( $plugin_data['activation_timestamp'] ) ) {
				$plugin_data['activation_timestamp'] = $itsec_globals['current_time_gmt'];
				$save_data                           = true;
			}

			//update the activated time if we need to in order to tell when the plugin was installed
			if ( ! isset( $plugin_data['already_supported'] ) ) {
				$plugin_data['already_supported'] = false;
				$save_data                        = true;
			}

			//update the activated time if we need to in order to tell when the plugin was installed
			if ( ! isset( $plugin_data['setup_completed'] ) ) {
				$plugin_data['setup_completed'] = false;
				$save_data                      = true;
			}

			//update the tooltips dismissed
			if ( ! isset( $plugin_data['tooltips_dismissed'] ) ) {
				$plugin_data['tooltips_dismissed'] = false;
				$save_data                         = true;
			}

			//update the options table if we have to
			if ( $save_data === true ) {
				update_site_option( 'itsec_data', $plugin_data );
			}

			return $plugin_data;

		}

		/**
		 * Handles the building of admin menus and calls required functions to render admin pages.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function setup_primary_admin() {

			global $itsec_globals;

			$this->available_pages[] = add_menu_page(
				__( 'Dashboard', 'ithemes-security' ),
				__( 'Security', 'ithemes-security' ),
				$itsec_globals['plugin_access_lvl'],
				'itsec',
				array( $this, 'render_page' )
			);

			foreach ( $this->pages as $page ) {

				$this->available_pages[] = add_submenu_page(
					'itsec',
					$page['title'],
					$page['title'],
					$itsec_globals['plugin_access_lvl'],
					$this->available_pages[0] . '_' . $page['slug'],
					array( $this, 'render_page' )
				);

			}

			//Make the dashboard is named correctly
			global $submenu;

			if ( isset( $submenu['itsec'] ) ) {
				$submenu['itsec'][0][0] = __( 'Dashboard', 'ithemes-security' );
			}

			foreach ( $this->available_pages as $page ) {

				add_action( 'load-' . $page, array( $this, 'page_actions' ) ); //Load page structure
				add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_admin_styles' ) ); //Load admin styles

			}

		}

		/**
		 * Setup and call admin messages.
		 *
		 * Sets up messages and registers actions for WordPress admin messages.
		 *
		 * @since 4.0
		 *
		 * @param object $messages WordPress error object or string of message to display
		 *
		 * @return void
		 */
		public function show_network_admin_notice( $errors ) {

			global $itsec_saved_network_notices; //use global to transfer to add_action callback

			$itsec_saved_network_notices = ''; //initialize so we can get multiple error messages (if needed)

			if ( function_exists( 'apc_store' ) ) {
				apc_clear_cache(); //Let's clear APC (if it exists) when big stuff is saved.
			}

			if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) !== false || strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_advanced' ) !== false ) {

				if ( $errors === false && isset ( $_GET['settings-updated'] ) && sanitize_text_field( $_GET['settings-updated'] ) == 'true' ) {

					$updated = '';

					if ( get_site_option( 'itsec_manual_update' ) == true ) {

						delete_site_option( 'itsec_manual_update' );

						if ( ITSEC_Lib::get_server() == 'nginx' ) {

							$server = __( 'NGINX conf file and/or restart your NGINX server', 'ithemes-security' );

						} else {

							$server = __( '.htaccess file', 'ithemes-security' );

						}

						$updated = sprintf(
							'<br />%s %s %s',
							__( 'As you have not allowed this plugin to update system files you must update your', 'ithemes-security' ),
							$server,
							__( 'as well as your wp-config.php file manually. Rules to insert in both files can be found on the Dashboard page.', 'ithemes-security' )
						);

					}

					$itsec_saved_network_notices = '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>' . __( 'Settings Updated', 'ithemes-security' ) . $updated . '</strong></p></div>';

				} elseif ( is_wp_error( $errors ) ) { //see if object is even an error

					$error_messages = $errors->get_error_messages(); //get all errors if it is

					$type = key( $errors->errors );

					foreach ( $error_messages as $error ) {

						$itsec_saved_network_notices = '<div id="setting-error-settings_updated" class="' . sanitize_text_field( $type ) . ' settings-error"><p><strong>' . sanitize_text_field( $error ) . '</strong></p></div>';
					}

				}

				//register appropriate message actions
				add_action( 'admin_notices', array( $this, 'print_network_admin_notice' ) );
				add_action( 'network_admin_notices', array( $this, 'print_network_admin_notice' ) );

			}

		}

		/**
		 * Display (and hide) setup nag.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function setup_nag() {

			global $blog_id, $itsec_globals;

			if ( is_multisite() && ( $blog_id != 1 || ! current_user_can( 'manage_network_options' ) ) ) { //only display to network admin if in multisite
				return;
			}

			$options = $itsec_globals['data'];

			//display the notifcation if they haven't turned it off
			if ( ( ! isset( $options['setup_completed'] ) || $options['setup_completed'] === false ) ) {

				if ( ! function_exists( 'ithemes_plugin_setup_notice' ) ) {

					function ithemes_plugin_setup_notice() {

						global $itsec_globals;

						echo '<div class="updated" id="itsec_setup_notice"><span class="it-icon-itsec"></span>'
						     . $itsec_globals['plugin_name'] . ' ' . __( 'is almost ready.', 'ithemes-security' ) . '<input type="button" class="itsec-notice-button" value="' . __( 'Secure Your Site Now', 'ithemes-security' ) . '" onclick="document.location.href=\'?itsec_setup=yes&_wpnonce=' . wp_create_nonce( 'itsec-nag' ) . '\';"><a target="_blank" href="http://www.ithemes.com/security" class="itsec-notice-button">' . __( 'Have a Pro Secure Your Site', 'ithemes-security' ) . '</a><input type="button" class="itsec-notice-hide" value="&times;" onclick="document.location.href=\'?itsec_setup=no&_wpnonce=' . wp_create_nonce( 'itsec-nag' ) . '\';">
						</div>';

					}

				}

				if ( is_multisite() ) {
					add_action( 'network_admin_notices', 'ithemes_plugin_setup_notice' ); //register notification
				} else {
					add_action( 'admin_notices', 'ithemes_plugin_setup_notice' ); //register notification
				}

			}

			//if they've clicked a button hide the notice
			if ( isset( $_GET['itsec_setup'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'itsec-nag' ) ) {

				$options = $itsec_globals['data'];

				$options['setup_completed'] = true;

				update_site_option( 'itsec_data', $options );

				if ( is_multisite() ) {
					remove_action( 'network_admin_notices', 'ithemes_plugin_setup_notice' );
				} else {
					remove_action( 'admin_notices', 'ithemes_plugin_setup_notice' );
				}

				if ( sanitize_text_field( $_GET['itsec_setup'] ) == 'no' && isset( $_SERVER['HTTP_REFERER'] ) ) {

					wp_redirect( $_SERVER['HTTP_REFERER'], '302' );

				} else {

					wp_redirect( 'admin.php?page=itsec', '302' );

				}

			}

		}

		/**
		 * Sorts tooltips from highest priority to lowest.
		 *
		 * @since 4.0
		 *
		 * @param array $a tooltip
		 * @param array $b tooltip
		 *
		 * @return int 1 if a is a lower priority, -1 if b is a lower priority, 0 if equal
		 */
		public function sort_tooltips( $a, $b ) {

			if ( $a['priority'] == $b['priority'] ) {
				return 0;
			}

			return ( $a['priority'] < $b['priority'] ? 1 : - 1 );

		}

		/**
		 * Display (and hide) support the plugin reminder.
		 *
		 * This will display a notice to the admin of the site only asking them to support
		 * the plugin after they have used it for 30 days.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function support_nag() {

			global $blog_id, $itsec_globals;

			if ( is_multisite() && ( $blog_id != 1 || ! current_user_can( 'manage_network_options' ) ) ) { //only display to network admin if in multisite
				return;
			}

			$options = $itsec_globals['data'];

			//display the notifcation if they haven't turned it off and they've been using the plugin at least 30 days
			if ( ( ! isset( $options['already_supported'] ) || $options['already_supported'] === false ) && $options['activation_timestamp'] < ( $itsec_globals['current_time_gmt'] - 2592000 ) ) {

				if ( ! function_exists( 'ithemes_plugin_support_notice' ) ) {

					function ithemes_plugin_support_notice() {

						global $itsec_globals;

						echo '<div class="updated" id="itsec_support_notice">
						<span>' . __( 'It looks like you\'ve been enjoying', 'ithemes-security' ) . ' ' . $itsec_globals['plugin_name'] . ' ' . __( 'for at least 30 days. Would you consider a small donation to help support continued development of the plugin?', 'ithemes-security' ) . '</span><input type="button" class="itsec-notice-button" value="' . __( 'Support This Plugin', 'ithemes-security' ) . '" onclick="document.location.href=\'?itsec_donate=yes&_wpnonce=' . wp_create_nonce( 'itsec-nag' ) . '\';">  <input type="button" class="itsec-notice-button" value="' . __( 'Rate it 5★\'s', 'ithemes-security' ) . '" onclick="document.location.href=\'?itsec_rate=yes&_wpnonce=' . wp_create_nonce( 'itsec-nag' ) . '\';">  <input type="button" class="itsec-notice-button" value="' . __( 'Tell Your Followers', 'ithemes-security' ) . '" onclick="document.location.href=\'?itsec_tweet=yes&_wpnonce=' . wp_create_nonce( 'itsec-nag' ) . '\';">  <input type="button" class="itsec-notice-hide" value="&times;" onclick="document.location.href=\'?itsec_no_nag=off&_wpnonce=' . wp_create_nonce( 'itsec-nag' ) . '\';">
						</div>';

					}

				}

				if ( is_multisite() ) {
					add_action( 'network_admin_notices', 'ithemes_plugin_support_notice' ); //register notification
				} else {
					add_action( 'admin_notices', 'ithemes_plugin_support_notice' ); //register notification
				}

			}

			//if they've clicked a button hide the notice
			if ( ( isset( $_GET['itsec_no_nag'] ) || isset( $_GET['itsec_rate'] ) || isset( $_GET['itsec_tweet'] ) || isset( $_GET['itsec_donate'] ) ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'itsec-nag' ) ) {

				$options = $itsec_globals['data'];

				$options['already_supported'] = true;

				update_site_option( 'itsec_data', $options );

				if ( is_multisite() ) {
					remove_action( 'network_admin_notices', 'ithemes_plugin_support_notice' );
				} else {
					remove_action( 'admin_notices', 'ithemes_plugin_support_notice' );
				}

				//take the user to paypal if they've clicked donate
				if ( isset( $_GET['itsec_donate'] ) ) {
					wp_redirect( 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=V647NGJSBC882', '302' );
				}

				//Go to the WordPress page to let them rate it.
				if ( isset( $_GET['itsec_rate'] ) ) {
					wp_redirect( 'http://wordpress.org/plugins/better-wp-security/', '302' );
				}

				//Compose a Tweet
				if ( isset( $_GET['itsec_tweet'] ) ) {
					wp_redirect( 'http://twitter.com/home?status=' . urlencode( 'I use ' . $itsec_globals['plugin_name'] . ' for WordPress by @iThemes and you should too - http://bit51.com/software/better-wp-security/' ), '302' );
				}

				if ( sanitize_text_field( $_GET['itsec_no_nag'] ) == 'off' && isset( $_SERVER['HTTP_REFERER'] ) ) {

					wp_redirect( $_SERVER['HTTP_REFERER'], '302' );

				} else {

					wp_redirect( 'admin.php?page=itsec', '302' );

				}

			}

		}

	}

}
