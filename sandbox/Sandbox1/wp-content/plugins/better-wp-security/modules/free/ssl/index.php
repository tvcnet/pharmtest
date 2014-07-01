<?php

if ( ! class_exists( 'ITSEC_SSL' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-ssl.php' );
}

if ( ! class_exists( 'ITSEC_SSL_Admin' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-ssl-admin.php' );
}

$ssl = new ITSEC_SSL();
new ITSEC_SSL_Admin( $this, $ssl );
