<?php

if ( ! class_exists( 'ITSEC_Hide_Backend' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-hide-backend.php' );
}

if ( ! class_exists( 'ITSEC_Hide_Backend_Admin' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-hide-backend-admin.php' );
}

new ITSEC_Hide_Backend();
new ITSEC_Hide_Backend_Admin( $this );
