<?php

if ( ! class_exists( 'ITSEC_Strong_Passwords' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-strong-passwords.php' );
}

if ( ! class_exists( 'ITSEC_Strong_Passwords_Admin' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-strong-passwords-admin.php' );
}

new ITSEC_Strong_Passwords();
new ITSEC_Strong_Passwords_Admin( $this );
