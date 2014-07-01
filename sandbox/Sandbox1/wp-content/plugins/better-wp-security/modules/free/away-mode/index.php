<?php

if ( ! class_exists( 'ITSEC_Away_Mode' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-away-mode.php' );
}

if ( ! class_exists( 'ITSEC_Away_Mode_Admin' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-away-mode-admin.php' );
}

$away_mode = new ITSEC_Away_Mode();
new ITSEC_Away_Mode_Admin( $this, $away_mode );
