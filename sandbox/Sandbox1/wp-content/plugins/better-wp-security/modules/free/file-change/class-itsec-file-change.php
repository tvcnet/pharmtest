<?php

class ITSEC_File_Change {

	private
		$count,
		$group,
		$settings;

	function __construct() {

		global $itsec_globals;

		$this->count    = 0;
		$this->group    = 0;
		$this->settings = get_site_option( 'itsec_file_change' );

		add_filter( 'itsec_logger_modules', array( $this, 'register_logger' ) );

		if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true && isset( $this->settings['last_run'] ) && ( $itsec_globals['current_time'] - 86400 ) > $this->settings['last_run'] ) {

			add_action( 'init', array( $this, 'execute_file_check' ) );

		}

	}

	/**
	 * Executes file checking
	 *
	 * @param bool $scheduled_call [optional] is this an automatic check
	 *
	 * @return void
	 **/
	public function execute_file_check( $scheduled_call = true ) {

		global $itsec_files, $itsec_logger, $itsec_globals;

		ITSEC_Lib::set_minimum_memory_limit( '128M' );

		if ( $itsec_files->get_file_lock( 'file_change' ) ) { //make sure it isn't already running

			//set base memory
			$memory_used = @memory_get_peak_usage();

			$logged_files = get_site_option( 'itsec_local_file_list' );

			//if there are no old files old file list is an empty array
			if ( $logged_files === false ) {

				$logged_files = array();

			}

			$current_files = $this->scan_files( '', $scheduled_call ); //scan current files

			$itsec_files->release_file_lock( 'file_change' );

			$files_added          = @array_diff_assoc( $current_files, $logged_files ); //files added
			$files_removed        = @array_diff_assoc( $logged_files, $current_files ); //files deleted
			$current_minus_added  = @array_diff_key( $current_files, $files_added ); //remove all added files from current filelist
			$logged_minus_deleted = @array_diff_key( $logged_files, $files_removed ); //remove all deleted files from old file list
			$files_changed        = array(); //array of changed files

			//compare file hashes and mod dates
			foreach ( $current_minus_added as $current_file => $current_attr ) {

				if ( array_key_exists( $current_file, $logged_minus_deleted ) ) {

					//if attributes differ added to changed files array
					if ( strcmp( $current_attr['mod_date'], $logged_minus_deleted[$current_file]['mod_date'] ) != 0 || strcmp( $current_attr['hash'], $logged_minus_deleted[$current_file]['hash'] ) != 0 ) {

						$files_changed[$current_file]['hash']     = $current_attr['hash'];
						$files_changed[$current_file]['mod_date'] = $current_attr['mod_date'];

					}

				}

			}

			//get count of changes
			$files_added_count   = sizeof( $files_added );
			$files_deleted_count = sizeof( $files_removed );
			$files_changed_count = sizeof( $files_changed );

			//create single array of all changes
			$full_change_list = array(
				'added'   => $files_added,
				'removed' => $files_removed,
				'changed' => $files_changed,
			);

			update_site_option( 'itsec_local_file_list', $current_files );

			$this->settings['last_run'] = $itsec_globals['current_time'];

			update_site_option( 'itsec_file_change', $this->settings );

			//get new max memory
			$check_memory = @memory_get_peak_usage();
			if ( $check_memory > $memory_used ) {
				$memory_used = $check_memory - $memory_used;
			}

			$full_change_list['memory'] = round( ( $memory_used / 1000000 ), 2 );

			$itsec_logger->log_event(
			             'file_change',
			             8,
			             $full_change_list
			);

			if ( $scheduled_call !== false && isset( $this->settings['email'] ) && $this->settings['email'] === true && ( $files_added_count > 0 || $files_changed_count > 0 || $files_deleted_count > 0 ) ) {

				$email_details = array(
					$files_added_count,
					$files_deleted_count,
					$files_changed_count,
					$full_change_list
				);

				$this->send_notification_email( $email_details );
			}

			if ( function_exists( 'get_current_screen' ) && ( ! isset( get_current_screen()->id ) || strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_logs' ) === false ) && isset( $this->settings['notify_admin'] ) && $this->settings['notify_admin'] === true ) {
				add_site_option( 'itsec_file_change_warning', true );
			}

			$itsec_files->release_file_lock( 'file_change' );

			if ( $files_added_count > 0 || $files_changed_count > 0 || $files_deleted_count > 0 ) {

				return true;

			} else {

				return false;

			}

		}

		return - 1;

	}

	/**
	 * Get Report Details
	 *
	 * @param array $email_details array of details to build email
	 *
	 * @return string report details
	 *
	 **/
	function get_email_report( $email_details ) {

		global $itsec_globals;

		//seperate array by category
		$added   = $email_details[3]['added'];
		$removed = $email_details[3]['removed'];
		$changed = $email_details[3]['changed'];
		$report  = '<strong>' . __( 'Scan Time:', 'ithemes-security' ) . '</strong> ' . date( 'l, F jS g:i a e', $itsec_globals['current_time'] ) . "<br />" . PHP_EOL;
		$report .= '<strong>' . __( 'Files Added:', 'ithemes-security' ) . '</strong> ' . $email_details[0] . "<br />" . PHP_EOL;
		$report .= '<strong>' . __( 'Files Deleted:', 'ithemes-security' ) . '</strong> ' . $email_details[1] . "<br />" . PHP_EOL;
		$report .= '<strong>' . __( 'Files Modified:', 'ithemes-security' ) . '</strong> ' . $email_details[2] . "<br />" . PHP_EOL;
		$report .= '<strong>' . __( 'Memory Used:', 'ithemes-security' ) . '</strong> ' . $email_details[3]['memory'] . " MB<br />" . PHP_EOL;

		$report .= '<h4>' . __( 'Files Added', 'ithemes-security' ) . '</h4>';
		$report .= '<table border="1" style="width: 100%; text-align: center;">' . PHP_EOL;
		$report .= '<tr>' . PHP_EOL;
		$report .= '<th>' . __( 'File', 'ithemes-security' ) . '</th>' . PHP_EOL;
		$report .= '<th>' . __( 'Modified', 'ithemes-security' ) . '</th>' . PHP_EOL;
		$report .= '<th>' . __( 'File Hash', 'ithemes-security' ) . '</th>' . PHP_EOL;
		$report .= '</tr>' . PHP_EOL;

		if ( isset( $added ) && is_array( $added ) && sizeof( $added > 0 ) ) {

			foreach ( $added as $item => $attr ) {

				$report .= '<tr>' . PHP_EOL;
				$report .= '<td>' . $item . '</td>' . PHP_EOL;
				$report .= '<td>' . date( 'l F jS, Y \a\t g:i a e', $attr['mod_date'] ) . '</td>' . PHP_EOL;
				$report .= '<td>' . $attr['hash'] . '</td>' . PHP_EOL;
				$report .= '</tr>' . PHP_EOL;

			}

		} else {

			$report .= '<tr>' . PHP_EOL;
			$report .= '<td colspan="3">' . __( 'No files were added.', 'ithemes-security' ) . '</td>' . PHP_EOL;
			$report .= '</tr>' . PHP_EOL;

		}

		$report .= '</table>' . PHP_EOL;

		$report .= '<h4>' . __( 'Files Deleted', 'ithemes-security' ) . '</h4>';
		$report .= '<table border="1" style="width: 100%; text-align: center;">' . PHP_EOL;
		$report .= '<tr>' . PHP_EOL;
		$report .= '<th>' . __( 'File', 'ithemes-security' ) . '</th>' . PHP_EOL;
		$report .= '<th>' . __( 'Modified', 'ithemes-security' ) . '</th>' . PHP_EOL;
		$report .= '<th>' . __( 'File Hash', 'ithemes-security' ) . '</th>' . PHP_EOL;
		$report .= '</tr>' . PHP_EOL;

		if ( isset( $removed ) && is_array( $removed ) && sizeof( $removed > 0 ) ) {

			foreach ( $removed as $item => $attr ) {

				$report .= '<tr>' . PHP_EOL;
				$report .= '<td>' . $item . '</td>' . PHP_EOL;
				$report .= '<td>' . date( 'l F jS, Y \a\t g:i a e', $attr['mod_date'] ) . '</td>' . PHP_EOL;
				$report .= '<td>' . $attr['hash'] . '</td>' . PHP_EOL;
				$report .= '</tr>' . PHP_EOL;

			}

		} else {

			$report .= '<tr>' . PHP_EOL;
			$report .= '<td colspan="3">' . __( 'No files were removed.', 'ithemes-security' ) . '</td>' . PHP_EOL;
			$report .= '</tr>' . PHP_EOL;

		}

		$report .= '</table>' . PHP_EOL;

		$report .= '<h4>' . __( 'Files Modified', 'ithemes-security' ) . '</h4>';
		$report .= '<table border="1" style="width: 100%; text-align: center;">' . PHP_EOL;
		$report .= '<tr>' . PHP_EOL;
		$report .= '<th>' . __( 'File', 'ithemes-security' ) . '</th>' . PHP_EOL;
		$report .= '<th>' . __( 'Modified', 'ithemes-security' ) . '</th>' . PHP_EOL;
		$report .= '<th>' . __( 'File Hash', 'ithemes-security' ) . '</th>' . PHP_EOL;
		$report .= '</tr>' . PHP_EOL;

		if ( isset( $changed ) && is_array( $changed ) && sizeof( $changed > 0 ) ) {

			foreach ( $changed as $item => $attr ) {

				$report .= '<tr>' . PHP_EOL;
				$report .= '<td>' . $item . '</td>' . PHP_EOL;
				$report .= '<td>' . date( 'l F jS, Y \a\t g:i a e', $attr['mod_date'] ) . '</td>' . PHP_EOL;
				$report .= '<td>' . $attr['hash'] . '</td>' . PHP_EOL;
				$report .= '</tr>' . PHP_EOL;

			}

		} else {

			$report .= '<tr>' . PHP_EOL;
			$report .= '<td colspan="3">' . __( 'No files were changed.', 'ithemes-security' ) . '</td>' . PHP_EOL;
			$report .= '</tr>' . PHP_EOL;

		}

		$report .= '</table>' . PHP_EOL;

		return $report;

	}

	/**
	 * Check file list
	 *
	 * Checks if given file should be included in file check based on exclude/include options
	 *
	 * @param string $file path of file to check from site root
	 *
	 * @return bool true if file should be checked false if not
	 *
	 **/
	private function is_checkable_file( $file ) {

		$this->count ++;

		//get file list from last check
		$file_list = $this->settings['file_list'];
		$type_list = $this->settings['types'];

		$file_list[] = 'itsec_file_change.lock';

		//assume not a directory and not checked
		$flag = false;

		if ( in_array( $file, $file_list ) ) {
			$flag = true;
		}

		if ( ! is_dir( $file ) ) {

			$path_info = pathinfo( $file );

			if ( isset( $path_info['extension'] ) && in_array( '.' . $path_info['extension'], $type_list ) ) {
				$flag = true;
			}

		}

		if ( $this->settings['method'] === true ) {

			if ( $flag == true ) { //if exclude reverse
				return false;
			} else {
				return true;
			}

		} else { //return flag

			return $flag;

		}

	}

	/**
	 * Register 404 and file change detection for logger
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array                   array of logger modules
	 */
	public function register_logger( $logger_modules ) {

		$logger_modules['file_change'] = array(
			'type'     => 'file_change',
			'function' => __( 'File Changes Detected', 'ithemes-security' ),
		);

		return $logger_modules;

	}

	/**
	 * Scans all files in a given path
	 *
	 * @since 4.0
	 *
	 * @param string $path           [optional] path to scan, defaults to WordPress root
	 * @param bool   $scheduled_call is this a scheduled call
	 *
	 * @return array array of files found and their information
	 *
	 */
	private function scan_files( $path = '', $scheduled_call ) {

		$time_offset = get_option( 'gmt_offset' ) * 60 * 60;

		$data = array();

		$clean_path = sanitize_text_field( $path );

		if ( $directory_handle = @opendir( ABSPATH . $clean_path ) ) { //get the directory

			while ( ( $item = readdir( $directory_handle ) ) !== false ) { // loop through dirs

				if ( $item != '.' && $item != '..' ) { //don't scan parent/etc

					$relname = $path . $item;

					$absname = ABSPATH . $relname;

					if ( is_dir( $absname ) && filetype( $absname ) == 'dir' ) {
						$is_dir     = true;
						$check_name = trailingslashit( $relname );
					} else {
						$is_dir     = false;
						$check_name = $relname;
					}

					if ( $this->is_checkable_file( $check_name ) === true ) { //make sure the user wants this file scanned

						if ( $is_dir === true ) { //if directory scan it

							$data = array_merge( $data, $this->scan_files( $relname . '/', $scheduled_call ) );

						} else { //is file so add to array

							$data[$relname]             = array();
							$data[$relname]['mod_date'] = @filemtime( $absname ) + $time_offset;
							$data[$relname]['hash']     = @md5_file( $absname );

						}

					}

				}

				if ( $scheduled_call === false && $this->count >= 100 ) {

					$this->group ++;
					$this->count = 0;
					echo( '{ "file_count": ' . $this->group * 100 . '}' );

				}

			}

			@closedir( $directory_handle ); //close the directory we're working with

		}

		return $data; // return the files we found in this dir

	}

	/**
	 * Builds and sends notification email
	 *
	 * @param array $email_details array of details for the email messge
	 *
	 * @return void
	 */
	private function send_notification_email( $email_details ) {

		global $itsec_globals;

		$global_options = get_site_option( 'itsec_global' );

		$headers = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' . "\r\n";
		$subject = '[' . get_option( 'siteurl' ) . '] ' . __( 'WordPress File Change Warning', 'ithemes-security' ) . ' ' . date( 'l, F jS, Y \a\\t g:i a e', $itsec_globals['current_time'] );

		$body = '<p>' . __( 'A file (or files) on your site at ', 'ithemes-security' ) . ' ' . get_option( 'siteurl' ) . __( ' have been changed. Please review the report below to verify changes are not the result of a compromise.', 'ithemes-security' ) . '</p>';
		$body .= $this->get_email_report( $email_details ); //get report

		//Use HTML Content type
		add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

		foreach ( $global_options['notification_email'] as $recipient ) {

			if ( is_email( trim( $recipient ) ) ) {
				wp_mail( trim( $recipient ), $subject, $body, $headers );
			}

		}

		//Remove HTML Content type
		remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

	}

	/**
	 * Set HTML content type for email
	 *
	 * @return string html content type
	 */
	public function set_html_content_type() {

		return 'text/html';

	}

}