<?php

if ( ! class_exists( 'ITSEC_Ban_Users_Admin' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-ban-users-admin.php' );
}

new ITSEC_Ban_Users_Admin( $this );
