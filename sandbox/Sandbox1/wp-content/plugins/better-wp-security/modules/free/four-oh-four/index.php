<?php

if ( ! class_exists( 'ITSEC_Four_Oh_Four' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-four-oh-four.php' );
}

if ( ! class_exists( 'ITSEC_Four_Oh_Four_Admin' ) ) {
	require( dirname( __FILE__ ) . '/class-itsec-four-oh-four-admin.php' );
}

$four_oh_four = new ITSEC_Four_Oh_Four();
new ITSEC_Four_Oh_Four_Admin( $this, $four_oh_four );
