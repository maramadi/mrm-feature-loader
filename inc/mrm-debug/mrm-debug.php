<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
if(class_exists('MrmDebug')) return;

Class MrmDebug {
	/************************************
	*
	*  Helper functions
	*
	* --------------------------------*/
	static function warning($msg) {
		if(WP_DEBUG){
			$msg = htmlspecialchars($msg);
			trigger_error($msg, E_USER_WARNING);
		}
	}

	static function log($log) {
		if ( true === WP_DEBUG ) {
		    if ( is_array( $log ) || is_object( $log ) ) {
		        error_log( print_r( $log, true ) );
		    } else {
		        error_log( $log );
		    }
		}
	}
}