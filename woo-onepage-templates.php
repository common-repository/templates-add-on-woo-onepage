<?php
/*
 * Plugin Name: Templates Add-on for Woo OnePage - Lite
 * Version: 0.9
 * Plugin URI: http://www.amadercode.com/woo-one-page-templates
 * Description: Awesome Templates for Woo OnePage checkout Shop.
 * Author: AmaderCode Lab
 * Author URI: http://www.amadercode.com/
 * Requires at least: 4.0
 * Tested up to: 5.0
 * WC tested up to: 3.7
 * WC requires at least: 3.0
 * Text Domain: woo-one-page-templates
 * Domain Path: /lang/
 * @package WordPress
 */

// Define ACL_WOOT_PLUGIN_FILE && ACL_WOOT_URL
if ( ! defined( 'ACL_WOOT_PLUGIN_FILE' ) ) {
    define( 'ACL_WOOT_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'ACL_WOOT_URL' ) ) {
    define( 'ACL_WOOT_URL', plugin_dir_url(__FILE__ ));
}
if ( ! defined( 'ACL_WOOT_PATH' ) ) {
    define( 'ACL_WOOT_PATH', plugin_dir_path(__FILE__ ));
}

if ( ! defined( 'ACL_WOOT_VERSION' ) ) {
    define( 'ACL_WOOT_VERSION', '1.0.0');
}

if ( ! defined( 'WOO_ONEPAGE_PRO' ) ) {
    define( 'WOO_ONEPAGE_PRO', true);
}

// Include the main Template plugin class.
if ( ! class_exists( 'ACL_WOOT_Plugin' ) ) {
    include_once('includes/class-woot-plugin.php');
}

/**
 * Main instance of ACL_WOOT_Plugin.
 * @since  1.0.0
 * @return ACL_WOOT_Plugin
 */
function acl_woot_plugin() {
    return ACL_WOOT_Plugin::instance();
}
add_action('init', 'acl_woot_plugin');
//install hook
//if ( class_exists( 'ACL_WOOT_Plugin' ) ) {
    //include_once( 'includes/class-woot-install.php');
//}