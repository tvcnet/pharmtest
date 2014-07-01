<?php

class ITSEC_Widgets_Admin {

	function __construct() {

		if ( is_admin() ) {

			$this->initialize();

		}

	}

	/**
	 * Add meta boxes to primary options pages.
	 *
	 * @since 4.0
	 *
	 * @param array $available_pages array of available page_hooks
	 */
	public function add_admin_meta_boxes( $available_pages ) {

		foreach ( $available_pages as $page ) {

			add_meta_box(
				'itsec_security_updates',
				__( 'Download Our WordPress Security Pocket Guide', 'ithemes-security' ),
				array( $this, 'metabox_security_updates' ),
				$page,
				'priority_side',
				'core'
			);

			add_meta_box(
				'itsec_need_help',
				__( 'Need Help Securing Your Site?', 'ithemes-security' ),
				array( $this, 'metabox_need_help' ),
				$page,
				'side',
				'core'
			);

			if ( ! class_exists( 'backupbuddy_api0' ) ) {
				add_meta_box(
					'itsec_get_backup',
					__( 'Complete your security strategy with BackupBuddy', 'ithemes-security' ),
					array( $this, 'metabox_get_backupbuddy' ),
					$page,
					'side',
					'core'
				);
			}

		}

		add_meta_box(
			'itsec_get_started',
			__( 'Getting Started', 'ithemes-security' ),
			array( $this, 'metabox_get_started' ),
			'toplevel_page_itsec',
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
	 * Display the Get BackupBuddy metabox
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_get_backupbuddy() {

		echo '<p>' . __( 'BackupBuddy is the complete backup, restore and migration solution for your WordPress site. Schedule automated backups, store your backups safely off-site and restore your site quickly & easily.', 'ithemes-security' ) . '</p>';
		echo '<a href="http://www.ithemes.com/purchase/backupbuddy" class="button-secondary" target="_blank">' . __( 'Get BackupBuddy', 'ithemes-security' ) . '</a>';

	}

	/**
	 * Display the metabox for getting started
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_get_started() {

		echo '<div class="itsec_getting_started">';
		echo '<div class="column">';
		echo '<h2>' . __( 'Welcome to iThemes Security', 'ithemes-security' ) . '</h2>';
		echo '<p>' . __( 'First things first, before you get started securing your site we highly recommend making a backup of your site.', 'ithemes-security' ) . '</p>';

		if ( class_exists( 'backupbuddy_api0' ) ) {
			echo '<p>';
			echo '<a class="button-primary" href="admin.php?page=pb_backupbuddy_backup">' . __( 'Backup with BackupBuddy', 'ithemes-security' ) . '</a>';
			echo '</p>';

		} else {
			echo '<p>';
			echo '<a class="button-primary" href="admin.php?page=toplevel_page_itsec_backups">' . __( 'Backup your database', 'ithemes-security' ) . '</a>';
			echo '<span class="itsec-or">' . __( 'or', 'ithemes-security' ) . '</span>';
			echo '<a class="button-primary" href="http://ithemes.com/purchase/backupbuddy/" target="_blank">' . __( 'Get BackupBuddy', 'ithemes-security' ) . '</a>';
			echo '</p>';
		}

		echo '</div>';

		echo '<div class="column two">';
		echo '<h2>' . __( 'Need a professional to secure your site?', 'ithemes-security' ) . '</h2>';
		echo '<p>' . __( 'Every site has different server configurations and specific needs. Have a security expert tailor the iThemes Security settings to your sites needs.', 'ithemes-security' ) . '</p>';
		echo '<p><a class="button-primary" href="#">' . __( 'Get Professional Setup and Configuration', 'ithemes-security' ) . '</a></p>';
		echo '</div>';
		echo '</div>';

	}

	/**
	 * Display the Need Help metabox
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_need_help() {

		echo '<p>' . __( 'Be sure your site has been properly secured by having one of our security experts tailor iThemes Security settings to the specific needs of this site.', 'ithemes-security' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://www.ithemes.com/security" target="_blank">' . __( 'Have an expert secure my site', 'ithemes-security' ) . '</a></p>';
		echo '<p>' . __( 'Get added peace of mind with professional support from our expert team and pro features with iThemes Security Pro.', 'ithemes-security' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://www.ithemes.com/security" target="_blank">' . __( 'Get iThemes Security Pro', 'ithemes-security' ) . '</a></p>';

	}

	/**
	 * Display the Security Updates signup metabox.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_security_updates() {

		ob_start();
		?>

		<div id="mc_embed_signup">
			<form
				action="http://ithemes.us2.list-manage.com/subscribe/post?u=7acf83c7a47b32c740ad94a4e&amp;id=5176bfed9e"
				method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate"
				target="_blank" novalidate>
				<div style="text-align: center;">
					<img src="<?php echo plugins_url( 'img/security-ebook.png', __FILE__ ) ?>" width="145"
					     height="187" alt="WordPress Security - A Pocket Guide">
				</div>
				<p><?php _e( 'Get tips for securing your site + the latest WordPress security updates, news and releases from iThemes.', 'better-wp-security' ); ?></p>

				<div id="mce-responses" class="clear">
					<div class="response" id="mce-error-response" style="display:none"></div>
					<div class="response" id="mce-success-response" style="display:none"></div>
				</div>
				<label for="mce-EMAIL"
				       style="display: block;margin-bottom: 3px;"><?php _e( 'Email Address', 'better-wp-security' ); ?></label>
				<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL"
				       placeholder="email@domain.com">
				<br/><br/>
				<input type="submit" value="<?php _e( 'Subscribe', 'better-wp-security' ); ?>" name="subscribe"
				       id="mc-embedded-subscribe" class="button button-secondary">
			</form>
		</div>

		<?php
		ob_end_flush();

	}

}