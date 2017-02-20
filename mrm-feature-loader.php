<?php
/*
Plugin Name: Feature loader
Plugin URI: https://maramadi.de
Description: Mini plugin system on theme basis
Version: dev
Author: Markus Diehl
Author URI: https://maramadi.de
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: mrm-feature-loader
*/
// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;
// Load the plugin
require "autoload.php";

// Bind class to Mrmfl
// 
use MrmFeatureLoader as MRMFL;

// Load the activation hook
register_activation_hook( __FILE__, function(){

	MRMFL::activation();

});

// Load the deactivation hook
register_deactivation_hook( __FILE__, function(){

	MRMFL::deactivation();

});
// Initialize
MRMFL::init();