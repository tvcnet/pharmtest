<?php
/**
 * Custom Icons for iThemes Products
 *
 * @package icon-fonts
 * @author Justin Kopepasah
 * @version 1.1.0
*/

if ( ! function_exists( 'it_icon_font_admin_enueue_scripts' ) ) {
	function it_icon_font_admin_enueue_scripts() {
		if ( version_compare( $GLOBALS['wp_version'], '3.7.10', '>=' ) ) {
			$dir = str_replace( '\\', '/', dirname( __FILE__ ) );
			
			$content_dir = rtrim( str_replace( '\\', '/', WP_CONTENT_DIR ), '/' );
			$abspath = rtrim( str_replace( '\\', '/', ABSPATH ), '/' );
			
			if ( 0 === strpos( $dir, $content_dir ) ) {
				$url = WP_CONTENT_URL . str_replace( '\\', '/', preg_replace( '/^' . preg_quote( $content_dir, '/' ) . '/', '', $dir ) );
			} else if ( 0 === strpos( $dir, $abspath ) ) {
				$url = get_option( 'siteurl' ) . str_replace( '\\', '/', preg_replace( '/^' . preg_quote( $abspath, '/' ) . '/', '', $dir ) );
			}
			
			if ( empty( $url ) ) {
				$dir = realpath( $dir );
				
				if ( 0 === strpos( $dir, $content_dir ) ) {
					$url = WP_CONTENT_URL . str_replace( '\\', '/', preg_replace( '/^' . preg_quote( $content_dir, '/' ) . '/', '', $dir ) );
				} else if ( 0 === strpos( $dir, $abspath ) ) {
					$url = get_option( 'siteurl' ) . str_replace( '\\', '/', preg_replace( '/^' . preg_quote( $abspath, '/' ) . '/', '', $dir ) );
				}
			}
			
			if ( is_ssl() ) {
				$url = preg_replace( '|^http://|', 'https://', $url );
			}
			
			
			wp_enqueue_style( 'ithemes-icon-font', "$url/icon-fonts.css" );
		}
	}
	add_action( 'admin_enqueue_scripts', 'it_icon_font_admin_enueue_scripts' );
}
